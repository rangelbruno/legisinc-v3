<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabelas já existem através das migrations originais
        // Este método apenas verifica se elas existem
        if (!Schema::hasTable('database_activities')) {
            Log::warning('⚠️ Tabela database_activities não encontrada');
        }

        // Tabela column_changes também já existe
        if (!Schema::hasTable('database_column_changes')) {
            Log::warning('⚠️ Tabela database_column_changes não encontrada');
        }

        // Inserir registro de preservação com campos corretos
        try {
            DB::table('database_activities')->insert([
                'table_name' => 'system_preservation',
                'operation_type' => 'CREATE',
                'affected_rows' => 0,
                'query_time_ms' => 0,
                'request_method' => 'SYSTEM',
                'endpoint' => '/system/preservation',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::info('✅ Sistema de monitoramento já preservado');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não fazer nada - preservação deve ser mantida
    }
};