<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Tabela de métricas time-series
        Schema::create('monitoring_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('metric_type', 50)->index();
            $table->string('metric_name', 100);
            $table->decimal('value', 15, 4)->nullable();
            $table->jsonb('tags')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->index('created_at');
        });

        // Tabela de logs agregados
        Schema::create('monitoring_logs', function (Blueprint $table) {
            $table->id();
            $table->string('level', 20)->index();
            $table->text('message')->nullable();
            $table->jsonb('context')->nullable();
            $table->jsonb('exception')->nullable();
            $table->uuid('request_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->index('created_at');
        });

        // Tabela de traces de requisições
        Schema::create('monitoring_traces', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_id');
            $table->string('span_id', 50)->nullable();
            $table->string('parent_span_id', 50)->nullable();
            $table->string('operation', 100);
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->integer('duration_ms')->nullable();
            $table->jsonb('tags')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });

        // Tabela de alertas
        Schema::create('monitoring_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('alert_type', 50);
            $table->string('severity', 20);
            $table->text('message')->nullable();
            $table->jsonb('details')->nullable();
            $table->boolean('resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });

        // Criar índices otimizados após tabelas
        $this->createOptimizedIndexes();

        // Habilitar pg_stat_statements se não estiver ativo
        $this->enablePgStatStatements();
    }

    public function down()
    {
        Schema::dropIfExists('monitoring_alerts');
        Schema::dropIfExists('monitoring_traces');
        Schema::dropIfExists('monitoring_logs');
        Schema::dropIfExists('monitoring_metrics');
    }

    private function createOptimizedIndexes()
    {
        DB::unprepared('
            -- Índices para monitoring_metrics
            CREATE INDEX IF NOT EXISTS idx_metrics_type_time 
                ON monitoring_metrics (metric_type, created_at DESC);
            CREATE INDEX IF NOT EXISTS idx_metrics_name_time
                ON monitoring_metrics (metric_name, created_at DESC);
            CREATE INDEX IF NOT EXISTS idx_metrics_tags 
                ON monitoring_metrics USING GIN (tags);
            
            -- Índices para monitoring_logs
            CREATE INDEX IF NOT EXISTS idx_logs_level_time 
                ON monitoring_logs (level, created_at DESC);
            CREATE INDEX IF NOT EXISTS idx_logs_request 
                ON monitoring_logs (request_id);
            CREATE INDEX IF NOT EXISTS idx_logs_user 
                ON monitoring_logs (user_id);
            CREATE INDEX IF NOT EXISTS idx_logs_context 
                ON monitoring_logs USING GIN (context);
            
            -- Índices para monitoring_traces
            CREATE INDEX IF NOT EXISTS idx_traces_request 
                ON monitoring_traces (request_id);
            CREATE INDEX IF NOT EXISTS idx_traces_duration 
                ON monitoring_traces (duration_ms DESC);
            CREATE INDEX IF NOT EXISTS idx_traces_tags 
                ON monitoring_traces USING GIN (tags);
            
            -- Índices para monitoring_alerts
            CREATE INDEX IF NOT EXISTS idx_alerts_severity_time 
                ON monitoring_alerts (severity, created_at DESC);
            CREATE INDEX IF NOT EXISTS idx_alerts_resolved 
                ON monitoring_alerts (resolved);
        ');
    }

    private function enablePgStatStatements()
    {
        try {
            // Tentar criar extensão pg_stat_statements
            DB::unprepared('CREATE EXTENSION IF NOT EXISTS pg_stat_statements;');
            
            // Verificar se está habilitado
            $result = DB::select("
                SELECT name, setting 
                FROM pg_settings 
                WHERE name = 'shared_preload_libraries'
            ");
            
            if (empty($result) || strpos($result[0]->setting, 'pg_stat_statements') === false) {
                \Log::warning('pg_stat_statements não está em shared_preload_libraries. Adicione ao postgresql.conf e reinicie o PostgreSQL.');
            }
        } catch (\Exception $e) {
            \Log::warning('Não foi possível habilitar pg_stat_statements: ' . $e->getMessage());
        }
    }
};