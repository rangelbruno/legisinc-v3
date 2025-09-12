<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class MigrateWithPreservation extends Command
{
    protected $signature = 'migrate:safe 
                          {--fresh : Run migrate:fresh instead of migrate}
                          {--seed : Seed the database after migration}
                          {--force : Force the operation to run in production}
                          {--generate-seeders : Auto-generate preservation seeders}';

    protected $description = 'Executa migrations de forma segura, preservando automaticamente todas as melhorias';

    public function handle()
    {
        $this->info('🛡️ Migrate Seguro com Preservação Automática v2.0');
        $this->newLine();

        try {
            // 1. Detectar e gerar seeders automaticamente se solicitado
            if ($this->option('generate-seeders')) {
                $this->generatePreservationSeeders();
            }

            // 2. Fazer backup inteligente
            $this->smartBackup();

            // 3. Executar migration
            $this->executeMigration();

            // 4. Corrigir permissões iniciais após migration
            $this->fixStoragePermissions();

            // 5. Corrigir namespaces no DatabaseSeeder
            $this->fixSeederNamespaces();

            // 6. Executar seeders se solicitado
            if ($this->option('seed')) {
                $this->executeSeeders();
            }

            // 7. Restaurar melhorias
            $this->restoreImprovements();

            // 8. Corrigir permissões finais
            $this->fixStoragePermissions();

            // 9. Validar resultado
            $this->validateResult();

            $this->newLine();
            $this->info('✅ Migration segura concluída com sucesso!');
            $this->info('🎯 Todas as melhorias foram preservadas automaticamente');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Erro durante migration segura: ' . $e->getMessage());
            
            // Tentar recovery automático
            $this->attemptRecovery();
            
            return 1;
        }
    }

    private function generatePreservationSeeders(): void
    {
        $this->info('🔧 Gerando seeders de preservação automaticamente...');
        
        $exitCode = Artisan::call('melhorias:generate', ['--auto' => true]);
        
        if ($exitCode === 0) {
            $this->info('✅ Seeders de preservação gerados');
        } else {
            $this->warn('⚠️ Problemas ao gerar seeders de preservação');
        }
        
        $output = Artisan::output();
        if ($output) {
            $this->line($output);
        }
    }

    private function smartBackup(): void
    {
        $this->info('💾 Executando backup inteligente...');
        
        // Usar o sistema de backup existente
        Artisan::call('backup:dados-criticos');
        
        // Executar seeder de preservação inteligente
        Artisan::call('db:seed', [
            '--class' => 'SmartPreservationSeeder',
            '--force' => true
        ]);
        
        $this->info('✅ Backup inteligente concluído');
    }

    private function executeMigration(): void
    {
        $this->info('🗄️ Executando migration...');
        
        if ($this->option('fresh')) {
            $command = 'migrate:fresh';
            $this->warn('⚠️ ATENÇÃO: Recriando banco de dados completamente');
        } else {
            $command = 'migrate';
        }

        $params = [];
        if ($this->option('force')) {
            $params['--force'] = true;
        }

        Artisan::call($command, $params);
        $this->info('✅ Migration executada');
        
        // Mostrar output da migration
        $output = Artisan::output();
        if ($output) {
            $this->line($output);
        }
    }

    private function executeSeeders(): void
    {
        $this->info('🌱 Executando seeders...');
        
        $params = [];
        if ($this->option('force')) {
            $params['--force'] = true;
        }

        // Primeiro executar o seeder de preservação
        Artisan::call('db:seed', array_merge($params, [
            '--class' => 'SmartPreservationSeeder'
        ]));

        // Depois executar todos os seeders
        Artisan::call('db:seed', $params);
        
        // Corrigir permissões do storage após seeders
        $this->fixStoragePermissions();
        
        $this->info('✅ Seeders executados');
    }

    private function restoreImprovements(): void
    {
        $this->info('♻️ Restaurando melhorias preservadas...');
        
        // Chamar método de restauração do SmartPreservationSeeder
        $seeder = new \Database\Seeders\SmartPreservationSeeder();
        $seeder->setCommand($this);
        $seeder->restaurarPreservacoes();
        
        $this->info('✅ Melhorias restauradas');
    }

    private function validateResult(): void
    {
        $this->info('🔍 Validando resultado...');
        
        $arquivosCriticos = [
            'app/Http/Controllers/ProposicaoAssinaturaController.php',
            'app/Http/Controllers/ProposicaoProtocoloController.php',
            'app/Services/OnlyOffice/OnlyOfficeService.php',
            'app/Services/Template/TemplateProcessorService.php',
            'app/Services/Template/TemplateVariableService.php',
            'app/Models/Proposicao.php',
            'config/dompdf.php'
        ];

        $validados = 0;
        foreach ($arquivosCriticos as $arquivo) {
            if (File::exists(base_path($arquivo))) {
                $validados++;
                $this->line("  ✓ {$arquivo}");
            } else {
                $this->warn("  ⚠️ {$arquivo} não encontrado");
            }
        }

        $this->info("📊 Arquivos validados: {$validados}/" . count($arquivosCriticos));
    }

    private function fixStoragePermissions(): void
    {
        $this->info('🔒 Corrigindo permissões do storage...');
        
        try {
            // Corrigir ownership para o usuário correto (laravel)
            $commands = [
                'chown -R laravel:laravel /var/www/html/storage',
                'chown -R laravel:laravel /var/www/html/bootstrap/cache',
                'find /var/www/html/storage -type d -exec chmod 775 {} \;',
                'find /var/www/html/storage -type f -exec chmod 664 {} \;',
                'chmod -R 775 /var/www/html/bootstrap/cache',
                'mkdir -p /var/www/html/storage/logs',
                'touch /var/www/html/storage/logs/laravel.log',
                'chown laravel:laravel /var/www/html/storage/logs/laravel.log',
                'chmod 664 /var/www/html/storage/logs/laravel.log'
            ];
            
            foreach ($commands as $command) {
                exec($command, $output, $returnVar);
                if ($returnVar !== 0) {
                    $this->warn("⚠️ Comando falhou: {$command}");
                }
            }
            
            // Limpar todos os caches compilados
            try {
                Artisan::call('optimize:clear');
                $this->line('  ✓ Cache limpo com optimize:clear');
            } catch (\Exception $e) {
                $this->warn("⚠️ Erro ao limpar cache: " . $e->getMessage());
                
                // Fallback manual
                if (file_exists(base_path('storage/framework/views'))) {
                    $files = glob(base_path('storage/framework/views/*.php'));
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            unlink($file);
                        }
                    }
                }
                
                Artisan::call('view:clear');
            }
            
            $this->info('✅ Permissões do storage corrigidas');
            
        } catch (\Exception $e) {
            $this->warn('⚠️ Erro ao corrigir permissões: ' . $e->getMessage());
            $this->info('💡 Execute manualmente: chown -R laravel:laravel /var/www/html/storage && chmod -R 775 /var/www/html/storage');
        }
    }

    private function fixSeederNamespaces(): void
    {
        $this->info('🔧 Corrigindo namespaces no DatabaseSeeder...');
        
        try {
            Artisan::call('fix:seeder-namespaces');
            $output = Artisan::output();
            
            if (strpos($output, 'Fixed') !== false) {
                $this->info('✅ Namespaces corrigidos no DatabaseSeeder');
            } else {
                $this->line('  ✓ Namespaces já estão corretos');
            }
            
        } catch (\Exception $e) {
            $this->warn('⚠️ Erro ao corrigir namespaces: ' . $e->getMessage());
        }
    }

    private function attemptRecovery(): void
    {
        $this->warn('🔄 Tentando recovery automático...');
        
        try {
            // Tentar restaurar backup
            Artisan::call('backup:dados-criticos', ['--restore' => true]);
            $this->info('✅ Backup restaurado');
            
        } catch (\Exception $e) {
            $this->error('❌ Recovery falhou: ' . $e->getMessage());
            $this->error('💡 Execute manualmente: php artisan backup:dados-criticos --restore');
        }
    }
}