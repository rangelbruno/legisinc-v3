<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class PreservarDadosCriticosSeeder extends Seeder
{
    /**
     * Seeder que SEMPRE executa após migrate:fresh para preservar dados críticos
     */
    public function run(): void
    {
        $this->command->info('🛡️ Preservando dados críticos do sistema...');

        // 1. Executar comando de backup automático se houver dados existentes
        $this->fazerBackupSeNecessario();

        // 2. Executar configuração persistente
        $this->call(ConfiguracaoSistemaPersistenteSeeder::class);

        // 3. Verificar integridade dos dados
        $this->verificarIntegridade();

        $this->command->info('✅ Dados críticos preservados com sucesso!');
    }

    private function fazerBackupSeNecessario(): void
    {
        $this->command->info('💾 Verificando necessidade de backup...');

        // Verificar se já existe backup recente
        $latestBackup = storage_path('backups/dados-criticos-latest.json');

        if (file_exists($latestBackup)) {
            $backupAge = time() - filemtime($latestBackup);
            $maxAge = 24 * 60 * 60; // 24 horas

            if ($backupAge < $maxAge) {
                $this->command->line('  ✓ Backup recente encontrado ('.round($backupAge / 3600, 1).'h atrás)');

                return;
            }
        }

        // Fazer backup automático
        $this->command->line('  📦 Executando backup automático...');

        try {
            Artisan::call('backup:dados-criticos');
            $output = Artisan::output();

            // Mostrar apenas linhas importantes do output
            $lines = explode("\n", trim($output));
            foreach ($lines as $line) {
                if (strpos($line, '✓') !== false || strpos($line, '⚠️') !== false) {
                    $this->command->line('    '.trim($line));
                }
            }
        } catch (\Exception $e) {
            $this->command->warn('  ⚠️ Erro ao fazer backup: '.$e->getMessage());
        }
    }

    private function verificarIntegridade(): void
    {
        $this->command->info('🔍 Verificando integridade do sistema...');

        // Verificar arquivos críticos
        $arquivosCriticos = [
            'app/Http/Controllers/ProposicaoAssinaturaController.php',
            'app/Http/Controllers/ProposicaoProtocoloController.php',
            'app/Services/Template/TemplateVariableService.php',
            'app/Models/Proposicao.php',
        ];

        foreach ($arquivosCriticos as $arquivo) {
            if (file_exists(base_path($arquivo))) {
                $this->command->line("  ✓ {$arquivo}");
            } else {
                $this->command->warn("  ⚠️ {$arquivo} não encontrado");
            }
        }

        // Verificar diretórios críticos
        $directoriosCriticos = [
            'storage/app/private/templates',
            'storage/backups',
            'storage/fonts',
        ];

        foreach ($directoriosCriticos as $dir) {
            if (is_dir(base_path($dir))) {
                $this->command->line("  ✓ {$dir}/");
            } else {
                $this->command->warn("  ⚠️ {$dir}/ não encontrado");
            }
        }

        // Verificar configuração DomPDF
        if (file_exists(config_path('dompdf.php'))) {
            $this->command->line('  ✓ config/dompdf.php');
        } else {
            $this->command->warn('  ⚠️ config/dompdf.php não encontrado');
        }
    }
}
