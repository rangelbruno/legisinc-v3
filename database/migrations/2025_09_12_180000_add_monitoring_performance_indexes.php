<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add performance guard-rails indexes for production monitoring
     */
    public function up(): void
    {
        // 1) Expression indexes (JSONB) for most common queries
        
        // Route + time for percentile calculations (hot path)
        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_metrics_route_time
              ON monitoring_metrics ( (tags->>'route'), created_at DESC )
              WHERE metric_name = 'request_duration_ms'
        ");

        // Status + time for error rate calculations  
        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_metrics_status_time
              ON monitoring_metrics ( (tags->>'status'), created_at DESC )
              WHERE metric_name = 'request_duration_ms'
        ");

        // 2) BRIN for large partitions (time-based scans)
        
        // Efficient for time range scans on large datasets
        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_metrics_created_at_brin
              ON monitoring_metrics USING BRIN (created_at)
        ");

        // 3) Hot path partial indexes by metric type
        
        // Request duration metrics (most queried)
        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_metrics_request_duration_time
              ON monitoring_metrics (created_at DESC)
              WHERE metric_name = 'request_duration_ms'
        ");

        // Request count metrics for throughput calculations
        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_metrics_request_count_time
              ON monitoring_metrics (created_at DESC)
              WHERE metric_name = 'request_count'
        ");

        // 4) Composite indexes for alert queries
        
        // Value + time for threshold alerts (P95, slow requests)
        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_metrics_value_time_threshold
              ON monitoring_metrics (value DESC, created_at DESC)
              WHERE metric_name = 'request_duration_ms' AND value > 1000
        ");

        // Route + status for error rate by route
        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_metrics_route_status_errors
              ON monitoring_metrics ( (tags->>'route'), (tags->>'status') )
              WHERE metric_name = 'request_duration_ms' 
                AND (tags->>'status') ~ '^[45]'
        ");
    }

    /**
     * Remove performance indexes
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_metrics_route_time');
        DB::statement('DROP INDEX IF EXISTS idx_metrics_status_time');
        DB::statement('DROP INDEX IF EXISTS idx_metrics_created_at_brin');
        DB::statement('DROP INDEX IF EXISTS idx_metrics_request_duration_time');
        DB::statement('DROP INDEX IF EXISTS idx_metrics_request_count_time');
        DB::statement('DROP INDEX IF EXISTS idx_metrics_value_time_threshold');
        DB::statement('DROP INDEX IF EXISTS idx_metrics_route_status_errors');
    }
};