<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up()
    {
        // Drop old tables if they exist
        DB::statement('DROP TABLE IF EXISTS monitoring_metrics CASCADE');
        DB::statement('DROP TABLE IF EXISTS monitoring_logs CASCADE');
        DB::statement('DROP TABLE IF EXISTS monitoring_traces CASCADE');
        DB::statement('DROP TABLE IF EXISTS monitoring_alerts CASCADE');

        // Create partitioned metrics table
        DB::statement("
            CREATE TABLE monitoring_metrics (
                id           BIGSERIAL,
                metric_type  VARCHAR(50)  NOT NULL,
                metric_name  VARCHAR(100) NOT NULL,
                value        NUMERIC(15,4),
                tags         JSONB,
                created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id, created_at)
            ) PARTITION BY RANGE (created_at)
        ");

        // Create partitioned logs table
        DB::statement("
            CREATE TABLE monitoring_logs (
                id         BIGSERIAL,
                level      VARCHAR(20) NOT NULL,
                message    TEXT,
                context    JSONB,
                exception  JSONB,
                request_id UUID,
                user_id    INTEGER,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id, created_at)
            ) PARTITION BY RANGE (created_at)
        ");

        // Create partitioned traces table
        DB::statement("
            CREATE TABLE monitoring_traces (
                id             BIGSERIAL,
                request_id     UUID NOT NULL,
                span_id        VARCHAR(50),
                parent_span_id VARCHAR(50),
                operation      VARCHAR(100),
                start_time     TIMESTAMP,
                end_time       TIMESTAMP,
                duration_ms    INTEGER,
                tags           JSONB,
                created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id, created_at)
            ) PARTITION BY RANGE (created_at)
        ");

        // Create partitioned alerts table
        DB::statement("
            CREATE TABLE monitoring_alerts (
                id          BIGSERIAL,
                alert_type  VARCHAR(50),
                severity    VARCHAR(20),
                message     TEXT,
                details     JSONB,
                resolved    BOOLEAN DEFAULT FALSE,
                resolved_at TIMESTAMP,
                created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id, created_at)
            ) PARTITION BY RANGE (created_at)
        ");

        // Create indexes on parent tables (will be inherited by partitions)
        $this->createOptimizedIndexes();

        // Create initial partitions for current and next month
        $this->createInitialPartitions();
    }

    public function down()
    {
        DB::statement('DROP TABLE IF EXISTS monitoring_alerts CASCADE');
        DB::statement('DROP TABLE IF EXISTS monitoring_traces CASCADE');
        DB::statement('DROP TABLE IF EXISTS monitoring_logs CASCADE');
        DB::statement('DROP TABLE IF EXISTS monitoring_metrics CASCADE');
    }

    private function createOptimizedIndexes()
    {
        // Metrics indexes
        DB::statement('CREATE INDEX IF NOT EXISTS idx_metrics_type_time ON monitoring_metrics (metric_type, created_at DESC)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_metrics_name_time ON monitoring_metrics (metric_name, created_at DESC)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_metrics_tags ON monitoring_metrics USING GIN (tags)');
        
        // Logs indexes
        DB::statement('CREATE INDEX IF NOT EXISTS idx_logs_level_time ON monitoring_logs (level, created_at DESC)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_logs_request ON monitoring_logs (request_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_logs_user ON monitoring_logs (user_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_logs_context ON monitoring_logs USING GIN (context)');
        
        // Traces indexes
        DB::statement('CREATE INDEX IF NOT EXISTS idx_traces_request ON monitoring_traces (request_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_traces_duration ON monitoring_traces (duration_ms DESC)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_traces_tags ON monitoring_traces USING GIN (tags)');
        
        // Alerts indexes
        DB::statement('CREATE INDEX IF NOT EXISTS idx_alerts_severity_time ON monitoring_alerts (severity, created_at DESC)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_alerts_resolved ON monitoring_alerts (resolved)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_alerts_type ON monitoring_alerts (alert_type)');
    }

    private function createInitialPartitions()
    {
        $tables = ['monitoring_metrics', 'monitoring_logs', 'monitoring_traces', 'monitoring_alerts'];
        
        // Create partitions for current month and next 2 months
        for ($i = 0; $i <= 2; $i++) {
            $month = Carbon::now()->startOfMonth()->addMonths($i);
            $partitionName = $month->format('Y_m');
            $startDate = $month->copy()->startOfMonth()->format('Y-m-01');
            $endDate = $month->copy()->endOfMonth()->addDay()->format('Y-m-d');
            
            foreach ($tables as $table) {
                DB::statement("
                    CREATE TABLE IF NOT EXISTS {$table}_{$partitionName}
                    PARTITION OF {$table}
                    FOR VALUES FROM ('{$startDate}') TO ('{$endDate}')
                ");
            }
        }
    }
};