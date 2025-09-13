<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create view for HTTP latency percentiles (last 1 hour)
        DB::statement("
            CREATE OR REPLACE VIEW vw_http_latency_last_1h AS
            SELECT
                COALESCE((tags->>'route')::text, 'unknown') AS route,
                COALESCE((tags->>'method')::text, 'GET') AS method,
                percentile_disc(0.50) WITHIN GROUP (ORDER BY value) AS p50,
                percentile_disc(0.95) WITHIN GROUP (ORDER BY value) AS p95,
                percentile_disc(0.99) WITHIN GROUP (ORDER BY value) AS p99,
                AVG(value)::numeric(8,2) AS avg_duration,
                MIN(value)::numeric(8,2) AS min_duration,
                MAX(value)::numeric(8,2) AS max_duration,
                COUNT(*) AS request_count,
                COUNT(CASE WHEN value > 1000 THEN 1 END) AS slow_requests
            FROM monitoring_metrics
            WHERE metric_name = 'request_duration_ms'
              AND created_at >= NOW() - INTERVAL '1 hour'
              AND value IS NOT NULL
            GROUP BY 1, 2
            ORDER BY p95 DESC
        ");

        // Create view for HTTP latency percentiles (last 24 hours)
        DB::statement("
            CREATE OR REPLACE VIEW vw_http_latency_last_24h AS
            SELECT
                COALESCE((tags->>'route')::text, 'unknown') AS route,
                COALESCE((tags->>'method')::text, 'GET') AS method,
                percentile_disc(0.50) WITHIN GROUP (ORDER BY value) AS p50,
                percentile_disc(0.95) WITHIN GROUP (ORDER BY value) AS p95,
                percentile_disc(0.99) WITHIN GROUP (ORDER BY value) AS p99,
                AVG(value)::numeric(8,2) AS avg_duration,
                COUNT(*) AS request_count,
                COUNT(CASE WHEN value > 1000 THEN 1 END) AS slow_requests,
                COUNT(CASE WHEN (tags->>'status')::text ~ '^5' THEN 1 END) AS error_5xx_count
            FROM monitoring_metrics
            WHERE metric_name = 'request_duration_ms'
              AND created_at >= NOW() - INTERVAL '24 hours'
              AND value IS NOT NULL
            GROUP BY 1, 2
            ORDER BY p95 DESC
        ");

        // Create view for error rates by route (last 1 hour)
        DB::statement("
            CREATE OR REPLACE VIEW vw_http_error_rates_last_1h AS
            SELECT
                COALESCE((tags->>'route')::text, 'unknown') AS route,
                COALESCE((tags->>'method')::text, 'GET') AS method,
                COUNT(*) AS total_requests,
                COUNT(CASE WHEN (tags->>'status')::text ~ '^4' THEN 1 END) AS error_4xx_count,
                COUNT(CASE WHEN (tags->>'status')::text ~ '^5' THEN 1 END) AS error_5xx_count,
                ROUND(
                    (COUNT(CASE WHEN (tags->>'status')::text ~ '^4' THEN 1 END)::float / NULLIF(COUNT(*), 0) * 100)::numeric, 2
                ) AS error_4xx_rate,
                ROUND(
                    (COUNT(CASE WHEN (tags->>'status')::text ~ '^5' THEN 1 END)::float / NULLIF(COUNT(*), 0) * 100)::numeric, 2
                ) AS error_5xx_rate,
                ROUND(
                    ((COUNT(CASE WHEN (tags->>'status')::text ~ '^[45]' THEN 1 END)::float / NULLIF(COUNT(*), 0)) * 100)::numeric, 2
                ) AS total_error_rate
            FROM monitoring_metrics
            WHERE metric_name IN ('request_duration_ms', 'request_count')
              AND created_at >= NOW() - INTERVAL '1 hour'
            GROUP BY 1, 2
            HAVING COUNT(*) >= 10  -- Only routes with significant traffic
            ORDER BY total_error_rate DESC, error_5xx_rate DESC
        ");

        // Create view for throughput by route (last 1 hour)
        DB::statement("
            CREATE OR REPLACE VIEW vw_http_throughput_last_1h AS
            SELECT
                COALESCE((tags->>'route')::text, 'unknown') AS route,
                COALESCE((tags->>'method')::text, 'GET') AS method,
                COUNT(*) AS total_requests,
                ROUND((COUNT(*) / 60.0)::numeric, 2) AS requests_per_minute,
                MIN(created_at) AS first_request,
                MAX(created_at) AS last_request
            FROM monitoring_metrics
            WHERE metric_name IN ('request_duration_ms', 'request_count')
              AND created_at >= NOW() - INTERVAL '1 hour'
            GROUP BY 1, 2
            ORDER BY total_requests DESC
        ");

        // Create view for monitoring system overview
        DB::statement("
            CREATE OR REPLACE VIEW vw_monitoring_overview AS
            SELECT
                'last_1h' AS timeframe,
                COUNT(*) AS total_metrics,
                COUNT(DISTINCT COALESCE((tags->>'route')::text, 'unknown')) AS unique_routes,
                AVG(CASE WHEN metric_name = 'request_duration_ms' THEN value END)::numeric(8,2) AS avg_response_time,
                percentile_disc(0.95) WITHIN GROUP (ORDER BY CASE WHEN metric_name = 'request_duration_ms' THEN value END) AS p95_response_time,
                COUNT(CASE WHEN metric_name = 'request_duration_ms' AND value > 1000 THEN 1 END) AS slow_requests,
                COUNT(CASE WHEN (tags->>'status')::text ~ '^5' THEN 1 END) AS server_errors,
                MIN(created_at) AS period_start,
                MAX(created_at) AS period_end
            FROM monitoring_metrics
            WHERE created_at >= NOW() - INTERVAL '1 hour'
            
            UNION ALL
            
            SELECT
                'last_24h' AS timeframe,
                COUNT(*) AS total_metrics,
                COUNT(DISTINCT COALESCE((tags->>'route')::text, 'unknown')) AS unique_routes,
                AVG(CASE WHEN metric_name = 'request_duration_ms' THEN value END)::numeric(8,2) AS avg_response_time,
                percentile_disc(0.95) WITHIN GROUP (ORDER BY CASE WHEN metric_name = 'request_duration_ms' THEN value END) AS p95_response_time,
                COUNT(CASE WHEN metric_name = 'request_duration_ms' AND value > 1000 THEN 1 END) AS slow_requests,
                COUNT(CASE WHEN (tags->>'status')::text ~ '^5' THEN 1 END) AS server_errors,
                MIN(created_at) AS period_start,
                MAX(created_at) AS period_end
            FROM monitoring_metrics
            WHERE created_at >= NOW() - INTERVAL '24 hours'
        ");

        // Create indexes to support the views
        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_monitoring_metrics_performance_lookup 
            ON monitoring_metrics (metric_name, created_at DESC) 
            WHERE metric_name IN ('request_duration_ms', 'request_count')
        ");

        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_monitoring_metrics_tags_route
            ON monitoring_metrics ((tags->>'route'))
            WHERE (tags->>'route') IS NOT NULL
        ");

        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_monitoring_metrics_tags_status
            ON monitoring_metrics ((tags->>'status'))
            WHERE (tags->>'status') IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop views
        DB::statement('DROP VIEW IF EXISTS vw_http_latency_last_1h');
        DB::statement('DROP VIEW IF EXISTS vw_http_latency_last_24h');
        DB::statement('DROP VIEW IF EXISTS vw_http_error_rates_last_1h');
        DB::statement('DROP VIEW IF EXISTS vw_http_throughput_last_1h');
        DB::statement('DROP VIEW IF EXISTS vw_monitoring_overview');
        
        // Drop specific indexes
        DB::statement('DROP INDEX IF EXISTS idx_monitoring_metrics_performance_lookup');
        DB::statement('DROP INDEX IF EXISTS idx_monitoring_metrics_tags_route');
        DB::statement('DROP INDEX IF EXISTS idx_monitoring_metrics_tags_status');
    }
};
