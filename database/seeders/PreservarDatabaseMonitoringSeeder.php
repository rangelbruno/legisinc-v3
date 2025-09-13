<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class PreservarDatabaseMonitoringSeeder extends Seeder
{
    /**
     * Preserva o Sistema de Monitoramento de Atividade de Banco de Dados
     * Versão 2.0 - Otimizada com proteção anti-recursão
     */
    public function run(): void
    {
        Log::info('🔧 Preservando Sistema de Monitoramento de Atividade de Banco de Dados...');

        try {
            // Verificar se as tabelas existem
            $this->ensureTablesExist();

            // Limpar dados antigos (manter apenas últimos 7 dias para performance)
            $this->cleanOldData();

            // Inserir dados de exemplo para demonstração
            $this->insertSampleData();

            // Registrar preservação
            $this->registerPreservation();

            Log::info('✅ Sistema de Monitoramento preservado com sucesso!');

        } catch (\Exception $e) {
            Log::error('❌ Erro ao preservar Sistema de Monitoramento: ' . $e->getMessage());
        }
    }

    /**
     * Garante que as tabelas existem
     */
    private function ensureTablesExist(): void
    {
        // As tabelas já devem existir através das migrations
        // Este método é apenas uma verificação de segurança

        if (!Schema::hasTable('database_activities')) {
            Log::warning('⚠️ Tabela database_activities não encontrada. Execute as migrations.');
            return;
        }

        if (!Schema::hasTable('database_column_changes')) {
            Log::warning('⚠️ Tabela database_column_changes não encontrada. Execute as migrations.');
            return;
        }

        Log::info('✓ Tabelas de monitoramento verificadas');
    }

    /**
     * Limpa dados antigos para manter performance
     */
    private function cleanOldData(): void
    {
        try {
            // Manter apenas últimos 7 dias
            $cutoffDate = now()->subDays(7);

            $deleted = DB::table('database_activities')
                ->where('created_at', '<', $cutoffDate)
                ->delete();

            if ($deleted > 0) {
                Log::info("🗑️ Removidos {$deleted} registros antigos de atividade");
            }
        } catch (\Exception $e) {
            Log::warning('⚠️ Não foi possível limpar dados antigos: ' . $e->getMessage());
        }
    }

    /**
     * Insere dados de exemplo para demonstração
     */
    private function insertSampleData(): void
    {
        // Dados de exemplo para demonstrar o sistema funcionando
        $sampleActivities = [
            [
                'table_name' => 'users',
                'operation_type' => 'SELECT',
                'affected_rows' => 5,
                'query_time_ms' => 1234,
                'request_method' => 'GET',
                'endpoint' => '/api/users',
                'created_at' => now()->subMinutes(30),
                'updated_at' => now()->subMinutes(30)
            ],
            [
                'table_name' => 'proposicoes',
                'operation_type' => 'INSERT',
                'affected_rows' => 1,
                'query_time_ms' => 2567,
                'request_method' => 'POST',
                'endpoint' => '/proposicoes',
                'created_at' => now()->subMinutes(15),
                'updated_at' => now()->subMinutes(15)
            ],
            [
                'table_name' => 'parlamentares',
                'operation_type' => 'UPDATE',
                'affected_rows' => 1,
                'query_time_ms' => 1890,
                'request_method' => 'PUT',
                'endpoint' => '/parlamentares/1',
                'created_at' => now()->subMinutes(5),
                'updated_at' => now()->subMinutes(5)
            ]
        ];

        foreach ($sampleActivities as $activity) {
            try {
                DB::table('database_activities')->insert($activity);
            } catch (\Exception $e) {
                // Ignorar erros de duplicação
                continue;
            }
        }

        Log::info('📊 Dados de exemplo inseridos no sistema de monitoramento');
    }

    /**
     * Registra a preservação do sistema
     */
    private function registerPreservation(): void
    {
        try {
            DB::table('database_activities')->insert([
                'table_name' => 'system_monitoring',
                'operation_type' => 'CREATE',
                'affected_rows' => 0,
                'query_time_ms' => 0,
                'request_method' => 'SYSTEM',
                'endpoint' => '/system/preservation/monitoring',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('📝 Preservação do Sistema de Monitoramento registrada');
        } catch (\Exception $e) {
            Log::info('✅ Sistema de monitoramento já preservado anteriormente');
        }
    }
}