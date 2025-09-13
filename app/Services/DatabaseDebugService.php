<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Str;

class DatabaseDebugService
{
    const CAPTURE_KEY = 'db_debug_capturing';
    const DATA_KEY    = 'db_debug_queries';
    const START_KEY   = 'db_debug_start_time';
    const LIMIT       = 1000;
    const SESSION_TIMEOUT = 600; // 10 minutes
    
    private $queries = [];
    private $startTime;
    private $isCapturing = false;
    protected bool $listening = false;
    
    /**
     * Start capturing database queries
     */
    public function startCapture(): array
    {
        if (!config('monitoring.debug.enabled', false)) {
            abort(403, 'Database debug is disabled');
        }

        $this->validatePermissions();

        Cache::put(self::CAPTURE_KEY, true, now()->addSeconds(self::SESSION_TIMEOUT));
        Cache::put(self::START_KEY, now()->toIso8601String(), self::SESSION_TIMEOUT);
        Cache::put(self::DATA_KEY, [], self::SESSION_TIMEOUT);

        if ($this->listening) {
            return ['status' => 'already_running', 'started_at' => Cache::get(self::START_KEY)];
        }

        DB::listen(function (QueryExecuted $query) {
            if (!Cache::get(self::CAPTURE_KEY)) return;
            $this->captureQueryWithContext($query);
        });

        $this->listening = true;
        $this->isCapturing = true;
        $this->startTime = microtime(true);
        $this->queries = [];

        Log::info('Database debug capture started', [
            'user_id' => auth()->id(),
            'session_timeout' => self::SESSION_TIMEOUT
        ]);

        return [
            'status' => 'started',
            'started_at' => Cache::get(self::START_KEY),
            'timeout_seconds' => self::SESSION_TIMEOUT,
            'limit' => self::LIMIT
        ];
    }
    
    /**
     * Stop capturing database queries
     */
    public function stopCapture(): array
    {
        $this->validatePermissions();

        $wasRunning = Cache::get(self::CAPTURE_KEY, false);
        $startedAt = Cache::get(self::START_KEY);
        $queryCount = count(Cache::get(self::DATA_KEY, []));

        Cache::forget(self::CAPTURE_KEY);
        // Keep START_KEY and DATA_KEY for post-session export

        $this->isCapturing = false;

        Log::info('Database debug capture stopped', [
            'user_id' => auth()->id(),
            'was_running' => $wasRunning,
            'query_count' => $queryCount,
            'started_at' => $startedAt
        ]);

        return [
            'status' => 'stopped',
            'was_running' => $wasRunning,
            'query_count' => $queryCount,
            'started_at' => $startedAt
        ];
    }
    
    /**
     * Get debug status
     */
    public function getStatus(): array
    {
        $this->validatePermissions();

        $isRunning = Cache::get(self::CAPTURE_KEY, false);
        $startedAt = Cache::get(self::START_KEY);
        $queryCount = count(Cache::get(self::DATA_KEY, []));

        return [
            'running' => $isRunning,
            'started_at' => $startedAt,
            'query_count' => $queryCount,
            'limit' => self::LIMIT,
            'timeout_seconds' => self::SESSION_TIMEOUT
        ];
    }
    
    /**
     * Clear captured data
     */
    public function clearData(): array
    {
        $this->validatePermissions();

        $queryCount = count(Cache::get(self::DATA_KEY, []));
        
        Cache::put(self::DATA_KEY, [], self::SESSION_TIMEOUT);
        Cache::forget(self::START_KEY);

        Log::info('Database debug data cleared', [
            'user_id' => auth()->id(),
            'cleared_count' => $queryCount
        ]);

        return [
            'status' => 'cleared',
            'cleared_count' => $queryCount
        ];
    }

    /**
     * Export queries (admin only)
     */
    public function exportQueries(): array
    {
        $this->validatePermissions();

        if (!auth()->user()->can('monitoring.debug.export')) {
            abort(403, 'Export permission required');
        }

        $queries = $this->getCapturedQueries();
        $metadata = $this->getStatus();

        return [
            'metadata' => $metadata,
            'queries' => $queries,
            'exported_at' => now()->toIso8601String(),
            'exported_by' => auth()->user()->email ?? 'unknown'
        ];
    }
    
    /**
     * Get captured queries
     */
    public function getCapturedQueries(): array
    {
        $this->validatePermissions();
        
        $queries = Cache::get(self::DATA_KEY, []);
        
        // Process raw queries to ensure consistent format
        $processedQueries = [];
        foreach ($queries as $query) {
            // Check if this is already a processed query (has 'sql' field and proper structure)
            if (isset($query['sql']) && isset($query['type']) && isset($query['timestamp'])) {
                // Already processed, use as-is
                $processedQueries[] = $query;
            } else {
                // Raw query from DB::getQueryLog(), needs processing
                $processed = $this->processRawQuery($query);
                if ($processed) {
                    $processedQueries[] = $processed;
                }
            }
        }
        
        // Apply sampling if too many queries
        if (count($processedQueries) > 500) {
            $processedQueries = $this->applySampling($processedQueries, 500);
        }

        return $processedQueries;
    }
    
    /**
     * Validate user permissions for debug operations
     */
    protected function validatePermissions(): void
    {
        if (!auth()->check()) {
            abort(401, 'Authentication required');
        }

        if (!auth()->user()->can('monitoring.debug')) {
            abort(403, 'Debug monitoring permission required');
        }
    }
    
    /**
     * Apply sampling to reduce query count
     */
    protected function applySampling(array $queries, int $maxCount): array
    {
        $step = max(1, floor(count($queries) / $maxCount));
        $sampled = [];
        
        for ($i = 0; $i < count($queries); $i += $step) {
            $sampled[] = $queries[$i];
        }
        
        return array_slice($sampled, 0, $maxCount);
    }
    
    /**
     * Process raw query from DB::getQueryLog() into consistent format
     */
    private function processRawQuery($query)
    {
        // Handle different query formats (from DB::getQueryLog() vs DB::listen())
        $sql = $query['query'] ?? $query['sql'] ?? '';
        $bindings = $query['bindings'] ?? [];
        $time = $query['time'] ?? 0;
        
        // Skip if no SQL
        if (empty($sql)) {
            return null;
        }
        
        // Format SQL with bindings
        $formattedSql = $this->formatSqlWithBindings($sql, $bindings);
        
        return [
            'sql' => $formattedSql,
            'bindings' => $this->maskBindings($bindings),
            'time' => round($time, 2),
            'time_formatted' => number_format($time, 2) . ' ms',
            'type' => $this->getQueryType($sql),
            'performance' => $this->analyzePerformance($time),
            'tables' => $this->extractTables($sql),
            'timestamp' => $query['timestamp'] ?? now()->toIso8601String(),
            'http_method' => request()->method() ?? null,
            'http_url' => $this->sanitizeUrl(request()->fullUrl() ?? ''),
            'route_name' => optional(request()->route())->getName(),
        ];
    }
    
    /**
     * Format SQL with bindings replaced
     */
    private function formatSqlWithBindings($sql, $bindings)
    {
        foreach ($bindings as $binding) {
            if (is_string($binding)) {
                $binding = "'" . $binding . "'";
            } elseif (is_null($binding)) {
                $binding = 'NULL';
            } elseif (is_bool($binding)) {
                $binding = $binding ? 'true' : 'false';
            } elseif (is_array($binding) || is_object($binding)) {
                $binding = "'" . json_encode($binding) . "'";
            }
            
            $sql = preg_replace('/\?/', $binding, $sql, 1);
        }
        
        return $sql;
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
    
    private function getQueryType(string $sql): string
    {
        $sql = strtolower(trim($sql));
        
        if (strpos($sql, 'select') === 0) return 'SELECT';
        if (strpos($sql, 'insert') === 0) return 'INSERT';
        if (strpos($sql, 'update') === 0) return 'UPDATE';
        if (strpos($sql, 'delete') === 0) return 'DELETE';
        if (strpos($sql, 'create') === 0) return 'CREATE';
        if (strpos($sql, 'alter') === 0) return 'ALTER';
        if (strpos($sql, 'drop') === 0) return 'DROP';
        if (strpos($sql, 'truncate') === 0) return 'TRUNCATE';
        
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
     * Capture query with HTTP context (updated with security)
     */
    protected function captureQueryWithContext(QueryExecuted $query): void
    {
        $data = Cache::get(self::DATA_KEY, []);
        
        // Implement sliding window - remove oldest when limit reached
        if (count($data) >= self::LIMIT) {
            array_shift($data);
        }

        $sql = $this->formatSqlForCapture($query->sql);
        
        $entry = [
            'sql'            => $this->truncate($sql, 4000),
            'bindings'       => $this->maskBindings($query->bindings),
            'time'           => round($query->time, 2),
            'time_formatted' => number_format($query->time, 2) . ' ms',
            'type'           => $this->getQueryType($sql),
            'performance'    => $this->analyzePerformance($query->time),
            'tables'         => $this->extractTables($sql),
            'timestamp'      => now()->toIso8601String(),
            'http_method'    => request()->method() ?? null,
            'http_url'       => $this->sanitizeUrl(request()->fullUrl() ?? ''),
            'route_name'     => optional(request()->route())->getName(),
            'request_id'     => app('request_id') ?? null,
            'user_id'        => auth()->id(),
            'backtrace'      => $this->filteredBacktrace(),
        ];

        $data[] = $entry;
        Cache::put(self::DATA_KEY, $data, self::SESSION_TIMEOUT);
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
            // Skip invalid queries
            if (!is_array($query)) {
                continue;
            }
            
            // Count by type
            $type = $query['type'] ?? 'unknown';
            if (!isset($stats['by_type'][$type])) {
                $stats['by_type'][$type] = ['count' => 0, 'time' => 0];
            }
            $stats['by_type'][$type]['count']++;
            $stats['by_type'][$type]['time'] += $query['time'] ?? 0;
            
            // Count by table
            $tables = $query['tables'] ?? [];
            if (is_array($tables)) {
                foreach ($tables as $table) {
                    if (!isset($stats['by_table'][$table])) {
                        $stats['by_table'][$table] = ['count' => 0, 'time' => 0];
                    }
                    $stats['by_table'][$table]['count']++;
                    $stats['by_table'][$table]['time'] += $query['time'] ?? 0;
                }
            }
            
            // Count slow queries
            $performance = $query['performance'] ?? 'fast';
            if ($performance === 'slow') {
                $stats['slow_queries']++;
            } elseif ($performance === 'very_slow') {
                $stats['very_slow_queries']++;
            }
        }
        
        return $stats;
    }
    
    // New security helper methods
    
    /**
     * Format SQL for better readability and security
     */
    protected function formatSqlForCapture(string $sql): string
    {
        return preg_replace('/\s+/', ' ', trim($sql));
    }

    /**
     * Truncate text to prevent memory issues
     */
    protected function truncate(string $text, int $maxLength = 4000): string
    {
        return mb_strlen($text) > $maxLength 
            ? mb_substr($text, 0, $maxLength) . '…' 
            : $text;
    }

    /**
     * Mask sensitive data in bindings
     */
    protected function maskBindings(array $bindings): array
    {
        return array_map(function ($value) {
            if (!is_scalar($value)) {
                return '[complex]';
            }

            $stringValue = (string) $value;

            // Mask potential PII patterns
            if (filter_var($stringValue, FILTER_VALIDATE_EMAIL)) {
                return $this->maskEmail($stringValue);
            }

            if (preg_match('/^\d{11}$|^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $stringValue)) {
                return '[CPF]';
            }

            if (preg_match('/^[a-zA-Z0-9]{20,}$/', $stringValue)) {
                return '[TOKEN]';
            }

            return $stringValue;
        }, $bindings);
    }

    /**
     * Mask email addresses
     */
    protected function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) return $email;
        
        $local = $parts[0];
        $domain = $parts[1];
        
        $maskedLocal = strlen($local) > 2 
            ? substr($local, 0, 2) . str_repeat('*', strlen($local) - 2)
            : str_repeat('*', strlen($local));
            
        return $maskedLocal . '@' . $domain;
    }

    /**
     * Sanitize URL to remove sensitive query parameters
     */
    protected function sanitizeUrl(string $url): string
    {
        // Remove query parameters that might contain sensitive data
        $parsed = parse_url($url);
        if (!$parsed) return '[invalid_url]';
        
        $clean = ($parsed['scheme'] ?? 'http') . '://' . ($parsed['host'] ?? 'localhost');
        if (isset($parsed['port'])) {
            $clean .= ':' . $parsed['port'];
        }
        if (isset($parsed['path'])) {
            $clean .= $parsed['path'];
        }
        
        return $this->truncate($clean, 512);
    }

    /**
     * Get filtered backtrace excluding vendor files
     */
    protected function filteredBacktrace(): array
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20);
        $filtered = [];
        
        foreach ($trace as $frame) {
            $file = $frame['file'] ?? '';
            
            // Skip vendor files and framework internals
            if ($file && !str_contains($file, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR)) {
                // Relative path from project root
                $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file);
                
                $filtered[] = [
                    'file' => $relativePath,
                    'line' => $frame['line'] ?? null,
                    'function' => $frame['function'] ?? null,
                    'class' => $frame['class'] ?? null,
                ];
            }
            
            // Limit to 6 frames to keep data manageable
            if (count($filtered) >= 6) break;
        }
        
        return $filtered;
    }
}