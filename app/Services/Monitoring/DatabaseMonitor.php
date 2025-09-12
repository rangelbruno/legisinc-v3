<?php

namespace App\Services\Monitoring;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseMonitor
{
    /**
     * Obter conexões ativas no PostgreSQL
     */
    public function getActiveConnections(): array
    {
        try {
            $result = DB::select("
                SELECT COUNT(*) as total,
                       state,
                       wait_event_type,
                       wait_event
                FROM pg_stat_activity
                WHERE pid != pg_backend_pid()
                GROUP BY state, wait_event_type, wait_event
                ORDER BY total DESC
            ");

            $total = collect($result)->sum('total');
            $maxConnections = $this->getMaxConnections();

            return [
                'active_connections' => $total,
                'max_connections' => $maxConnections,
                'usage_percent' => $maxConnections > 0 ? round(($total / $maxConnections) * 100, 2) : 0,
                'by_state' => $result,
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to get active connections', ['error' => $e->getMessage()]);
            return ['error' => 'Unable to fetch connection data'];
        }
    }

    /**
     * Obter queries mais lentas (requer pg_stat_statements)
     */
    public function getSlowQueries(int $thresholdMs = 1000): array
    {
        try {
            $result = DB::select("
                SELECT query,
                       ROUND(mean_exec_time::numeric, 2) as mean_exec_time_ms,
                       calls,
                       ROUND(total_exec_time::numeric, 2) as total_exec_time_ms
                FROM pg_stat_statements
                WHERE mean_exec_time > ?
                ORDER BY mean_exec_time DESC
                LIMIT 10
            ", [$thresholdMs]);

            return [
                'slow_queries' => $result,
                'threshold_ms' => $thresholdMs,
                'count' => count($result),
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to get slow queries. pg_stat_statements may not be enabled.', [
                'error' => $e->getMessage()
            ]);
            return ['error' => 'pg_stat_statements not available'];
        }
    }

    /**
     * Obter tamanhos das tabelas
     */
    public function getTableSizes(): array
    {
        try {
            $result = DB::select("
                SELECT n.nspname AS schema,
                       c.relname AS table_name,
                       pg_size_pretty(pg_total_relation_size(c.oid)) AS total_size,
                       pg_total_relation_size(c.oid) AS size_bytes
                FROM pg_class c
                JOIN pg_namespace n ON n.oid = c.relnamespace
                WHERE c.relkind = 'r'
                  AND n.nspname NOT IN ('pg_catalog','information_schema')
                ORDER BY pg_total_relation_size(c.oid) DESC
                LIMIT 15
            ");

            return [
                'tables' => $result,
                'total_tables' => count($result),
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to get table sizes', ['error' => $e->getMessage()]);
            return ['error' => 'Unable to fetch table sizes'];
        }
    }

    /**
     * Obter cache hit ratio do PostgreSQL
     */
    public function getCacheHitRatio(): array
    {
        try {
            // Cache hit ratio global
            $globalResult = DB::select("
                SELECT CASE 
                    WHEN (sum(blks_hit) + sum(blks_read)) = 0 THEN 0
                    ELSE ROUND(
                        (sum(blks_hit)::float / NULLIF(sum(blks_hit) + sum(blks_read), 0)::float * 100)::numeric, 
                        2
                    )
                END AS cache_hit_ratio
                FROM pg_statio_user_tables
            ");

            // Cache hit ratio por tabela (top 10)
            $tableResults = DB::select("
                SELECT schemaname || '.' || relname AS table_name,
                       CASE 
                           WHEN (heap_blks_hit + heap_blks_read) = 0 THEN 0
                           ELSE ROUND(
                               (heap_blks_hit::float / NULLIF(heap_blks_hit + heap_blks_read, 0)::float * 100)::numeric,
                               2
                           )
                       END AS cache_hit_ratio,
                       heap_blks_hit,
                       heap_blks_read
                FROM pg_statio_user_tables
                WHERE heap_blks_hit + heap_blks_read > 100
                ORDER BY heap_blks_hit + heap_blks_read DESC
                LIMIT 10
            ");

            return [
                'global_cache_hit_ratio' => $globalResult[0]->cache_hit_ratio ?? 0,
                'by_table' => $tableResults,
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to get cache hit ratio', ['error' => $e->getMessage()]);
            return ['error' => 'Unable to fetch cache statistics'];
        }
    }

    /**
     * Verificar locks ativos
     */
    public function getLockStatus(): array
    {
        try {
            $result = DB::select("
                SELECT l.locktype,
                       l.mode,
                       l.granted,
                       a.pid,
                       a.usename,
                       SUBSTRING(a.query, 1, 100) as query_preview,
                       a.state,
                       a.query_start,
                       EXTRACT(EPOCH FROM (now() - a.query_start)) as seconds_running
                FROM pg_locks l
                JOIN pg_stat_activity a ON a.pid = l.pid
                WHERE NOT l.granted OR EXTRACT(EPOCH FROM (now() - a.query_start)) > 30
                ORDER BY a.query_start ASC
                LIMIT 20
            ");

            return [
                'locks' => $result,
                'blocked_queries' => collect($result)->where('granted', false)->count(),
                'long_running' => collect($result)->where('seconds_running', '>', 30)->count(),
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to get lock status', ['error' => $e->getMessage()]);
            return ['error' => 'Unable to fetch lock information'];
        }
    }

    /**
     * Obter máximo de conexões configurado
     */
    private function getMaxConnections(): int
    {
        try {
            $result = DB::select("SHOW max_connections");
            return (int) $result[0]->max_connections;
        } catch (\Exception $e) {
            return 100; // fallback padrão
        }
    }

    /**
     * Resumo geral do banco
     */
    public function getDatabaseSummary(): array
    {
        $connections = $this->getActiveConnections();
        $cache = $this->getCacheHitRatio();
        $locks = $this->getLockStatus();

        return [
            'connections' => [
                'active' => $connections['active_connections'] ?? 0,
                'max' => $connections['max_connections'] ?? 0,
                'usage_percent' => $connections['usage_percent'] ?? 0,
            ],
            'cache_hit_ratio' => $cache['global_cache_hit_ratio'] ?? 0,
            'blocked_queries' => $locks['blocked_queries'] ?? 0,
            'timestamp' => now()->toISOString(),
        ];
    }
}