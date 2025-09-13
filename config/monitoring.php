<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Debug System Configuration
    |--------------------------------------------------------------------------
    |
    | Controls the database debugging and query capture functionality.
    | IMPORTANT: Only enable in development and trusted staging environments.
    |
    */
    'debug' => [
        // Master feature flag - must be true to enable any debug features
        'enabled' => env('MONITORING_DEBUG_ENABLED', false),
        
        // Query capture settings
        'query_capture' => [
            'enabled' => env('MONITORING_DEBUG_QUERY_CAPTURE', true),
            'max_queries' => env('MONITORING_DEBUG_MAX_QUERIES', 1000),
            'session_timeout' => env('MONITORING_DEBUG_SESSION_TIMEOUT', 600), // 10 minutes
        ],
        
        // Security settings
        'security' => [
            'mask_pii' => env('MONITORING_DEBUG_MASK_PII', true),
            'truncate_sql' => env('MONITORING_DEBUG_TRUNCATE_SQL', 4000),
            'truncate_url' => env('MONITORING_DEBUG_TRUNCATE_URL', 512),
            'max_backtrace_frames' => env('MONITORING_DEBUG_MAX_BACKTRACE_FRAMES', 6),
        ],
        
        // Sampling to reduce overhead in high-traffic scenarios  
        'sampling' => [
            'enabled' => env('MONITORING_DEBUG_SAMPLING_ENABLED', false),
            'rate' => env('MONITORING_DEBUG_SAMPLING_RATE', 0.1), // 10% sampling
            'max_display_queries' => env('MONITORING_DEBUG_MAX_DISPLAY_QUERIES', 500),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Enterprise Monitoring Alerts Configuration
    |--------------------------------------------------------------------------
    |
    | Configure thresholds for automated alerts and notification channels.
    |
    */

    'alerts' => [
        // Master toggle for alert system
        'enabled' => env('MONITORING_ALERTS_ENABLED', false),
        
        // Alert thresholds (configurable per environment)
        'thresholds' => [
            'error_rate' => env('MONITORING_ALERT_ERROR_RATE', 5.0),        // 5% error rate
            'response_time' => env('MONITORING_ALERT_RESPONSE_TIME', 2000), // 2 seconds P95
            'memory_usage' => env('MONITORING_ALERT_MEMORY_MB', 1024),      // 1GB memory
            'disk_usage' => env('MONITORING_ALERT_DISK_PERCENT', 90),       // 90% disk
            'queue_backlog' => env('MONITORING_ALERT_QUEUE_BACKLOG', 1000), // 1000 jobs
            'db_connections' => env('MONITORING_ALERT_DB_CONNECTIONS', 80), // 80% connections
        ],
        
        // Alert suppression (prevent spam)
        'suppression_ttl' => 300, // 5 minutes
        
        // Maintenance windows (disable alerts during deployments)
        'maintenance_windows' => [
            // 'daily_backup' => ['start' => '02:00', 'end' => '03:00'],
            // 'weekly_maintenance' => ['day' => 'sunday', 'start' => '01:00', 'end' => '04:00'],
        ],
        
        // Legacy settings (maintained for compatibility)
        'error_rate_threshold' => env('MONITORING_ERROR_RATE_THRESHOLD', 1.0),
        'p95_threshold' => env('MONITORING_P95_THRESHOLD', 500),
        'db_connection_threshold' => env('MONITORING_DB_CONNECTION_THRESHOLD', 80),
        'cache_hit_threshold' => env('MONITORING_CACHE_HIT_THRESHOLD', 90),
        'blocked_queries_threshold' => env('MONITORING_BLOCKED_QUERIES_THRESHOLD', 5),
        'memory_threshold' => env('MONITORING_MEMORY_THRESHOLD', 85),
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
        // Enable metrics collection
        'enabled' => env('MONITORING_METRICS_ENABLED', true),
        
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
        
        // HTTP request metrics
        'http' => [
            'enabled' => env('MONITORING_METRICS_HTTP_ENABLED', true),
            'track_user_agents' => env('MONITORING_METRICS_TRACK_USER_AGENTS', false),
        ],
        
        // Redis queue settings for async metric processing (enterprise)
        'redis' => [
            'connection' => env('MONITORING_REDIS_CONNECTION', 'default'),
            'queue_key' => env('MONITORING_REDIS_QUEUE', 'monitoring:metrics'),
            'flush_enabled' => env('MONITORING_REDIS_FLUSH_ENABLED', true),
            'batch_size' => env('MONITORING_REDIS_BATCH_SIZE', 500),
            'timeout' => env('MONITORING_REDIS_TIMEOUT', 30), // seconds
            'max_queue_size' => env('MONITORING_METRICS_MAX_QUEUE_SIZE', 10000),
            'ttl' => env('MONITORING_METRICS_REDIS_TTL', 3600), // 1 hour
        ],
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