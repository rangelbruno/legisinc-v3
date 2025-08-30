<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class RestaurarDadosCriticosSeeder extends Seeder
{
    /**
     * Seeder para restaurar dados críticos do backup
     * Use: php artisan db:seed --class=RestaurarDadosCriticosSeeder
     */
    public function run(): void
    {
        $this->command->info('♻️ Restaurando dados críticos do backup...');

        // Verificar se existe backup
        $latestBackup = storage_path('backups/dados-criticos-latest.json');

        if (! file_exists($latestBackup)) {
            $this->command->error('❌ Nenhum backup encontrado!');
            $this->command->warn('Execute primeiro: php artisan backup:dados-criticos');

            return;
        }

        // Confirmar com o usuário
        $this->command->warn('⚠️ ATENÇÃO: Isso irá sobrescrever os dados atuais do banco.');
        $this->command->warn('⚠️ Certifique-se de que você tem um backup atual antes de prosseguir.');

        if (! $this->command->confirm('Deseja continuar com a restauração?')) {
            $this->command->info('Operação cancelada pelo usuário.');

            return;
        }

        // Executar restauração
        try {
            Artisan::call('backup:dados-criticos', ['--restore' => true]);
            $output = Artisan::output();

            // Mostrar output da restauração
            $this->command->info('📋 Resultado da restauração:');
            $lines = explode("\n", trim($output));
            foreach ($lines as $line) {
                if (! empty(trim($line))) {
                    $this->command->line('  '.trim($line));
                }
            }

            // Aplicar configurações persistentes após restauração
            $this->command->info('🔧 Aplicando configurações persistentes...');
            $this->call(ConfiguracaoSistemaPersistenteSeeder::class);

            $this->command->info('✅ Restauração concluída com sucesso!');

        } catch (\Exception $e) {
            $this->command->error('❌ Erro durante a restauração: '.$e->getMessage());
        }
    }
}
