<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('database_activities', function (Blueprint $table) {
            $table->id();
            $table->string('table_name', 100)->index();
            $table->enum('operation_type', ['SELECT', 'INSERT', 'UPDATE', 'DELETE', 'CREATE', 'ALTER', 'DROP', 'OTHER'])->index();
            $table->decimal('query_time_ms', 10, 2)->default(0);
            $table->integer('affected_rows')->default(0);
            $table->string('request_method', 10)->nullable();
            $table->string('endpoint', 255)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('sql_hash', 64)->index();
            $table->timestamps();

            // Índices compostos para otimização de consultas
            $table->index(['table_name', 'operation_type']);
            $table->index(['created_at', 'table_name']);
            $table->index(['user_id', 'created_at']);
            $table->index(['operation_type', 'created_at']);

            // Chave estrangeira para usuários
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Nota: Particionamento removido para simplificar implementação inicial
        // Pode ser adicionado posteriormente se necessário para otimização

        // Criar views úteis para relatórios
        DB::statement("
            CREATE VIEW database_activity_summary AS
            SELECT
                table_name,
                operation_type,
                COUNT(*) as total_operations,
                AVG(query_time_ms) as avg_query_time_ms,
                MAX(query_time_ms) as max_query_time_ms,
                SUM(affected_rows) as total_affected_rows,
                DATE_TRUNC('hour', created_at) as hour_bucket,
                MAX(created_at) as last_activity
            FROM database_activities
            WHERE created_at >= NOW() - INTERVAL '24 hours'
            GROUP BY table_name, operation_type, DATE_TRUNC('hour', created_at)
            ORDER BY hour_bucket DESC, total_operations DESC;
        ");

        DB::statement("
            CREATE VIEW database_activity_realtime AS
            SELECT
                table_name,
                operation_type,
                COUNT(*) as operations_count,
                AVG(query_time_ms) as avg_time_ms,
                request_method,
                endpoint,
                MAX(created_at) as last_seen
            FROM database_activities
            WHERE created_at >= NOW() - INTERVAL '5 minutes'
            GROUP BY table_name, operation_type, request_method, endpoint
            ORDER BY last_seen DESC;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover views
        DB::statement("DROP VIEW IF EXISTS database_activity_realtime;");
        DB::statement("DROP VIEW IF EXISTS database_activity_summary;");

        // Nota: Particionamento não foi implementado

        Schema::dropIfExists('database_activities');
    }
};