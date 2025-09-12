<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Monitoring Alerts Configuration
    |--------------------------------------------------------------------------
    |
    | Configure thresholds for automated alerts and notification channels.
    |
    */

    'alerts' => [
        // Error rate threshold (percentage)
        'error_rate_threshold' => env('MONITORING_ERROR_RATE_THRESHOLD', 1.0),
        
        // P95 latency threshold (milliseconds)
        'p95_threshold' => env('MONITORING_P95_THRESHOLD', 500),
        
        // Database connection usage threshold (percentage)
        'db_connection_threshold' => env('MONITORING_DB_CONNECTION_THRESHOLD', 80),
        
        // Cache hit ratio threshold (percentage)
        'cache_hit_threshold' => env('MONITORING_CACHE_HIT_THRESHOLD', 90),
        
        // Blocked queries threshold (count)
        'blocked_queries_threshold' => env('MONITORING_BLOCKED_QUERIES_THRESHOLD', 5),
        
        // Queue backlog threshold (count)
        'queue_backlog_threshold' => env('MONITORING_QUEUE_BACKLOG_THRESHOLD', 1000),
        
        // Memory usage threshold (percentage)
        'memory_threshold' => env('MONITORING_MEMORY_THRESHOLD', 85),
        
        // Disk usage threshold (percentage)
        'disk_threshold' => env('MONITORING_DISK_THRESHOLD', 90),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Channels
    |--------------------------------------------------------------------------
    */

    'notifications' => [
        'slack' => [
            'webhook_url' => env('MONITORING_SLACK_WEBHOOK'),
            'channel' => env('MONITORING_SLACK_CHANNEL', '#monitoring'),
            'username' => env('MONITORING_SLACK_USERNAME', 'Legisinc Monitor'),
            'enabled' => env('MONITORING_SLACK_ENABLED', false),
        ],
        
        'email' => [
            'recipients' => array_filter(explode(',', env('MONITORING_EMAIL_RECIPIENTS', ''))),
            'enabled' => env('MONITORING_EMAIL_ENABLED', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Metrics Configuration
    |--------------------------------------------------------------------------
    */

    'metrics' => [
        // Redis buffer size before forcing flush
        'buffer_max_size' => env('MONITORING_BUFFER_MAX_SIZE', 1000),
        
        // Flush interval (seconds)
        'flush_interval' => env('MONITORING_FLUSH_INTERVAL', 60),
        
        // Skip routes from metrics collection
        'skip_routes' => [
            'monitoring.health',
            'admin.monitoring.stream',
        ],
        
        // Sample rate for high-traffic endpoints (0.0 to 1.0)
        'sample_rate' => env('MONITORING_SAMPLE_RATE', 1.0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Retention
    |--------------------------------------------------------------------------
    */

    'retention' => [
        // Metrics retention in months
        'metrics_months' => env('MONITORING_METRICS_RETENTION', 3),
        
        // Logs retention in months  
        'logs_months' => env('MONITORING_LOGS_RETENTION', 1),
        
        // Traces retention in days
        'traces_days' => env('MONITORING_TRACES_RETENTION', 7),
        
        // Alerts retention in months
        'alerts_months' => env('MONITORING_ALERTS_RETENTION', 6),
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard Configuration
    |--------------------------------------------------------------------------
    */

    'dashboard' => [
        // Auto-refresh interval (seconds)
        'refresh_interval' => env('MONITORING_REFRESH_INTERVAL', 30),
        
        // SSE connection timeout (seconds)
        'sse_timeout' => env('MONITORING_SSE_TIMEOUT', 300),
        
        // Cache dashboard data (seconds)
        'cache_duration' => env('MONITORING_CACHE_DURATION', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring
    |--------------------------------------------------------------------------
    */

    'performance' => [
        // Slow query threshold (milliseconds)
        'slow_query_threshold' => env('MONITORING_SLOW_QUERY_THRESHOLD', 1000),
        
        // Slow request threshold (milliseconds)
        'slow_request_threshold' => env('MONITORING_SLOW_REQUEST_THRESHOLD', 2000),
        
        // Log slow requests
        'log_slow_requests' => env('MONITORING_LOG_SLOW_REQUESTS', true),
        
        // Log error requests (4xx/5xx)
        'log_error_requests' => env('MONITORING_LOG_ERROR_REQUESTS', true),
    ],
];