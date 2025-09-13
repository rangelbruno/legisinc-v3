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
        Schema::create('database_column_changes', function (Blueprint $table) {
            $table->id();
            $table->string('table_name', 100)->index();
            $table->string('column_name', 100)->index();
            $table->bigInteger('record_id')->index(); // ID do registro que foi alterado
            $table->enum('operation_type', ['INSERT', 'UPDATE', 'DELETE'])->index();

            // Valores antigos e novos (JSON para flexibilidade)
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();

            // Informações do usuário e contexto
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_role', 50)->nullable(); // Parlamentar, Legislativo, Protocolo, etc.
            $table->string('user_name', 255)->nullable(); // Nome do usuário para facilitar visualização

            // Contexto da requisição
            $table->string('request_method', 10)->nullable();
            $table->string('endpoint', 255)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();

            // Metadados adicionais
            $table->decimal('query_time_ms', 10, 2)->default(0);
            $table->string('sql_hash', 64)->index();
            $table->json('additional_context')->nullable(); // Para informações extras do workflow

            $table->timestamps();

            // Índices compostos para consultas otimizadas
            $table->index(['table_name', 'record_id']);
            $table->index(['table_name', 'column_name']);
            $table->index(['table_name', 'record_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['user_role', 'created_at']);
            $table->index(['operation_type', 'created_at']);

            // Chave estrangeira para usuários
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Criar view para análise de fluxo de alterações (PostgreSQL otimizado)
        DB::statement("
            CREATE VIEW database_record_flow AS
            SELECT
                table_name,
                record_id,
                COUNT(*) as total_changes,
                MIN(created_at) as first_change,
                MAX(created_at) as last_change,
                STRING_AGG(user_role, ' → ' ORDER BY created_at) as user_flow,
                STRING_AGG(user_name, ' → ' ORDER BY created_at) as user_name_flow,
                ARRAY_AGG(DISTINCT column_name) as changed_columns,
                COUNT(DISTINCT user_id) as unique_users
            FROM database_column_changes
            GROUP BY table_name, record_id
            ORDER BY MAX(created_at) DESC;
        ");

        // View para análise de colunas mais alteradas
        DB::statement("
            CREATE VIEW database_column_activity AS
            SELECT
                table_name,
                column_name,
                COUNT(*) as total_changes,
                COUNT(DISTINCT record_id) as unique_records,
                COUNT(DISTINCT user_id) as unique_users,
                STRING_AGG(DISTINCT user_role, ', ') as roles_involved,
                MAX(created_at) as last_change,
                AVG(query_time_ms) as avg_query_time
            FROM database_column_changes
            WHERE created_at >= NOW() - INTERVAL '24 hours'
            GROUP BY table_name, column_name
            ORDER BY total_changes DESC;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover views
        DB::statement("DROP VIEW IF EXISTS database_column_activity;");
        DB::statement("DROP VIEW IF EXISTS database_record_flow;");

        Schema::dropIfExists('database_column_changes');
    }
};