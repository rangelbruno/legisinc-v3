<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DatabaseDebugService
{
    private $queries = [];
    private $startTime;
    private $isCapturing = false;
    
    /**
     * Start capturing database queries
     */
    public function startCapture()
    {
        $this->isCapturing = true;
        $this->startTime = microtime(true);
        $this->queries = [];
        
        // Enable query log
        DB::enableQueryLog();
        
        // Store in cache for real-time access
        Cache::put('db_debug_capturing', true, 3600);
        Cache::put('db_debug_start_time', $this->startTime, 3600);
        Cache::forget('db_debug_queries'); // Clear previous queries
        
        // Listen to database queries and store them with HTTP context
        DB::listen(function ($query) {
            $this->captureQueryWithContext($query);
        });
    }
    
    /**
     * Stop capturing database queries
     */
    public function stopCapture()
    {
        $this->isCapturing = false;
        
        // Disable query log
        DB::disableQueryLog();
        
        // Clear cache
        Cache::forget('db_debug_capturing');
        Cache::forget('db_debug_start_time');
        Cache::forget('db_debug_queries');
    }
    
    /**
     * Get captured queries
     */
    public function getCapturedQueries()
    {
        try {
            // First get queries from current request
            $currentQueries = DB::getQueryLog() ?: [];
            
            // Then get cached queries from previous requests
            $cachedQueries = Cache::get('db_debug_queries', []);
            
            // Merge all queries
            $allQueries = array_merge($cachedQueries, $currentQueries);
            
            $processedQueries = [];
            
            foreach ($allQueries as $query) {
                if (is_array($query)) {
                    $processedQuery = $this->processQuery($query);
                    if ($processedQuery) {
                        $processedQueries[] = $processedQuery;
                    }
                }
            }
            
            return $processedQueries;
            
        } catch (\Exception $e) {
            Log::error('Error getting captured queries', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return [];
        }
    }
    
    /**
     * Process a single query for display
     */
    private function processQuery($query)
    {
        // Handle different query formats (from DB::getQueryLog() vs DB::listen())
        $sql = $query['query'] ?? $query['sql'] ?? '';
        $bindings = $query['bindings'] ?? [];
        $time = $query['time'] ?? 0;
        
        // Skip if no SQL
        if (empty($sql)) {
            return null;
        }
        
        // Replace bindings in SQL
        foreach ($bindings as $binding) {
            if (is_string($binding)) {
                $binding = "'" . $binding . "'";
            } elseif (is_null($binding)) {
                $binding = 'NULL';
            } elseif (is_bool($binding)) {
                $binding = $binding ? 'true' : 'false';
            }
            
            $sql = preg_replace('/\?/', $binding, $sql, 1);
        }
        
        // Analyze query type
        $type = $this->getQueryType($sql);
        
        // Check performance
        $performance = $this->analyzePerformance($time);
        
        // Format SQL for display
        $formattedSql = $this->formatSql($sql);
        
        // Get table information
        $tables = $this->extractTables($sql);
        
        return [
            'sql' => $sql,
            'formatted_sql' => $formattedSql,
            'bindings' => $bindings,
            'time' => $time,
            'time_formatted' => number_format($time, 2) . ' ms',
            'type' => $type,
            'performance' => $performance,
            'tables' => $tables,
            'timestamp' => now()->toISOString(),
            'backtrace' => $this->getSimplifiedBacktrace()
        ];
    }
    
    /**
     * Get query type (SELECT, INSERT, UPDATE, DELETE, etc.)
     */
    private function getQueryType($sql)
    {
        $sql = trim(strtoupper($sql));
        
        if (strpos($sql, 'SELECT') === 0) return 'SELECT';
        if (strpos($sql, 'INSERT') === 0) return 'INSERT';
        if (strpos($sql, 'UPDATE') === 0) return 'UPDATE';
        if (strpos($sql, 'DELETE') === 0) return 'DELETE';
        if (strpos($sql, 'CREATE') === 0) return 'CREATE';
        if (strpos($sql, 'DROP') === 0) return 'DROP';
        if (strpos($sql, 'ALTER') === 0) return 'ALTER';
        if (strpos($sql, 'TRUNCATE') === 0) return 'TRUNCATE';
        if (strpos($sql, 'BEGIN') === 0) return 'TRANSACTION';
        if (strpos($sql, 'COMMIT') === 0) return 'TRANSACTION';
        if (strpos($sql, 'ROLLBACK') === 0) return 'TRANSACTION';
        
        return 'OTHER';
    }
    
    /**
     * Analyze query performance
     */
    private function analyzePerformance($time)
    {
        if ($time < 1) return 'excellent';
        if ($time < 10) return 'good';
        if ($time < 50) return 'average';
        if ($time < 100) return 'slow';
        return 'very_slow';
    }
    
    /**
     * Format SQL for better readability
     */
    private function formatSql($sql)
    {
        // Basic SQL formatting
        $keywords = [
            'SELECT', 'FROM', 'WHERE', 'JOIN', 'LEFT JOIN', 'RIGHT JOIN', 
            'INNER JOIN', 'ORDER BY', 'GROUP BY', 'HAVING', 'LIMIT', 
            'INSERT INTO', 'VALUES', 'UPDATE', 'SET', 'DELETE FROM',
            'CREATE TABLE', 'ALTER TABLE', 'DROP TABLE', 'AND', 'OR'
        ];
        
        $formatted = $sql;
        foreach ($keywords as $keyword) {
            $formatted = preg_replace(
                '/\b' . $keyword . '\b/i',
                "\n" . $keyword,
                $formatted
            );
        }
        
        // Clean up extra newlines
        $formatted = preg_replace('/\n+/', "\n", $formatted);
        $formatted = trim($formatted);
        
        return $formatted;
    }
    
    /**
     * Extract table names from SQL
     */
    private function extractTables($sql)
    {
        $tables = [];
        
        // Extract from FROM clause
        if (preg_match_all('/FROM\s+`?(\w+)`?/i', $sql, $matches)) {
            $tables = array_merge($tables, $matches[1]);
        }
        
        // Extract from JOIN clauses
        if (preg_match_all('/JOIN\s+`?(\w+)`?/i', $sql, $matches)) {
            $tables = array_merge($tables, $matches[1]);
        }
        
        // Extract from INSERT INTO
        if (preg_match('/INSERT\s+INTO\s+`?(\w+)`?/i', $sql, $matches)) {
            $tables[] = $matches[1];
        }
        
        // Extract from UPDATE
        if (preg_match('/UPDATE\s+`?(\w+)`?/i', $sql, $matches)) {
            $tables[] = $matches[1];
        }
        
        // Extract from DELETE FROM
        if (preg_match('/DELETE\s+FROM\s+`?(\w+)`?/i', $sql, $matches)) {
            $tables[] = $matches[1];
        }
        
        return array_unique($tables);
    }
    
    /**
     * Capture query with HTTP context
     */
    private function captureQueryWithContext($query)
    {
        if (!Cache::get('db_debug_capturing', false)) {
            return;
        }
        
        $httpContext = $this->getHttpContext();
        $processedQuery = $this->processQuery([
            'sql' => $query->sql,
            'bindings' => $query->bindings,
            'time' => $query->time
        ]);
        
        // Add HTTP context to the query
        $processedQuery['http_method'] = $httpContext['method'] ?? 'CLI';
        $processedQuery['http_url'] = $httpContext['url'] ?? 'command-line';
        $processedQuery['route_name'] = $httpContext['route_name'] ?? null;
        $processedQuery['request_id'] = $httpContext['request_id'] ?? uniqid();
        
        // Store in cache for real-time access
        $cachedQueries = Cache::get('db_debug_queries', []);
        $cachedQueries[] = $processedQuery;
        
        // Keep only last 1000 queries to prevent memory issues
        if (count($cachedQueries) > 1000) {
            $cachedQueries = array_slice($cachedQueries, -1000);
        }
        
        Cache::put('db_debug_queries', $cachedQueries, 3600);
    }
    
    /**
     * Get HTTP context from current request
     */
    private function getHttpContext()
    {
        $context = [];
        
        if (app()->runningInConsole()) {
            return [
                'method' => 'CLI',
                'url' => 'command-line',
                'route_name' => null,
                'request_id' => 'cli_' . getmypid()
            ];
        }
        
        try {
            $request = request();
            $context = [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'route_name' => $request->route()?->getName(),
                'request_id' => $request->header('X-Request-ID', session()->getId() ?? uniqid())
            ];
        } catch (\Exception $e) {
            // Fallback if request is not available
            $context = [
                'method' => 'UNKNOWN',
                'url' => 'unknown',
                'route_name' => null,
                'request_id' => uniqid()
            ];
        }
        
        return $context;
    }

    /**
     * Get simplified backtrace for debugging
     */
    private function getSimplifiedBacktrace()
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
        $simplified = [];
        
        foreach ($trace as $item) {
            if (isset($item['file']) && !str_contains($item['file'], 'vendor/')) {
                $simplified[] = [
                    'file' => str_replace(base_path() . '/', '', $item['file']),
                    'line' => $item['line'] ?? 0,
                    'function' => $item['function'] ?? '',
                    'class' => $item['class'] ?? ''
                ];
            }
        }
        
        return array_slice($simplified, 0, 5);
    }
    
    /**
     * Get database statistics
     */
    public function getDatabaseStats()
    {
        try {
            $stats = [];
            
            // Get table sizes
            $tables = DB::select("
                SELECT 
                    table_name,
                    pg_size_pretty(pg_total_relation_size(quote_ident(table_name))) AS size,
                    (SELECT COUNT(*) FROM information_schema.columns WHERE table_name = t.table_name) as columns_count
                FROM information_schema.tables t
                WHERE table_schema = 'public'
                ORDER BY pg_total_relation_size(quote_ident(table_name)) DESC
            ");
            
            $stats['tables'] = $tables;
            
            // Get current connections
            $connections = DB::select("
                SELECT 
                    pid,
                    usename,
                    application_name,
                    client_addr,
                    state,
                    query_start,
                    state_change,
                    query
                FROM pg_stat_activity
                WHERE datname = current_database()
                AND pid != pg_backend_pid()
            ");
            
            $stats['connections'] = $connections;
            
            // Get database size
            $dbSize = DB::selectOne("
                SELECT pg_size_pretty(pg_database_size(current_database())) as size
            ");
            
            $stats['database_size'] = $dbSize->size;
            
            // Get cache hit ratio
            $cacheRatio = DB::selectOne("
                SELECT 
                    sum(heap_blks_hit) / (sum(heap_blks_hit) + sum(heap_blks_read)) as ratio
                FROM pg_statio_user_tables
            ");
            
            $stats['cache_hit_ratio'] = round(($cacheRatio->ratio ?? 0) * 100, 2);
            
            return $stats;
            
        } catch (\Exception $e) {
            Log::error('Error getting database stats: ' . $e->getMessage());
            return [
                'error' => 'Unable to fetch database statistics',
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get slow queries
     */
    public function getSlowQueries($threshold = 100)
    {
        $queries = $this->getCapturedQueries();
        
        return array_filter($queries, function($query) use ($threshold) {
            return $query['time'] > $threshold;
        });
    }
    
    /**
     * Get query statistics
     */
    public function getQueryStatistics()
    {
        $queries = $this->getCapturedQueries();
        
        $stats = [
            'total_queries' => count($queries),
            'total_time' => array_sum(array_column($queries, 'time')),
            'average_time' => count($queries) > 0 ? array_sum(array_column($queries, 'time')) / count($queries) : 0,
            'by_type' => [],
            'by_table' => [],
            'slow_queries' => 0,
            'very_slow_queries' => 0
        ];
        
        foreach ($queries as $query) {
            // Count by type
            $type = $query['type'];
            if (!isset($stats['by_type'][$type])) {
                $stats['by_type'][$type] = ['count' => 0, 'time' => 0];
            }
            $stats['by_type'][$type]['count']++;
            $stats['by_type'][$type]['time'] += $query['time'];
            
            // Count by table
            foreach ($query['tables'] as $table) {
                if (!isset($stats['by_table'][$table])) {
                    $stats['by_table'][$table] = ['count' => 0, 'time' => 0];
                }
                $stats['by_table'][$table]['count']++;
                $stats['by_table'][$table]['time'] += $query['time'];
            }
            
            // Count slow queries
            if ($query['performance'] === 'slow') {
                $stats['slow_queries']++;
            } elseif ($query['performance'] === 'very_slow') {
                $stats['very_slow_queries']++;
            }
        }
        
        return $stats;
    }
}