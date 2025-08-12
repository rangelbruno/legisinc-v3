<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix storage and cache permissions for Laravel application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Corrigindo permissões do Laravel...');

        // Garantir que os diretórios existem
        $directories = [
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/views', 
            'storage/logs',
            'storage/app/public',
            'bootstrap/cache'
        ];

        foreach ($directories as $dir) {
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
                $this->line("📁 Criado diretório: {$dir}");
            }
        }

        // Verificar se estamos em um ambiente Docker
        $isDocker = file_exists('/.dockerenv');
        
        if ($isDocker) {
            $this->info('🐳 Ambiente Docker detectado, corrigindo permissões...');
            
            // Executar correções de permissão via shell
            $commands = [
                'chown -R laravel:laravel storage/',
                'chown -R laravel:laravel bootstrap/cache/',
                'chmod -R 755 storage/',
                'chmod -R 755 bootstrap/cache/',
                'chmod 775 storage/framework/cache/data/',
                'chmod -R 775 storage/logs/',
                'chmod -R 775 storage/framework/sessions/',
                'chmod -R 775 storage/framework/views/',
                'chmod -R 775 storage/app/'
            ];

            foreach ($commands as $command) {
                exec($command, $output, $returnCode);
                if ($returnCode === 0) {
                    $this->line("✅ Executado: {$command}");
                } else {
                    $this->error("❌ Falha ao executar: {$command}");
                }
            }
        } else {
            $this->info('💻 Ambiente local detectado, usando PHP para correções...');
            
            // Para ambientes não-Docker, usar PHP para mudanças básicas de permissão
            try {
                chmod(storage_path('framework/cache/data'), 0775);
                chmod(storage_path('logs'), 0775);
                chmod(storage_path('framework/sessions'), 0775);
                chmod(storage_path('framework/views'), 0775);
                chmod(storage_path('app'), 0775);
                
                $this->info('✅ Permissões básicas aplicadas');
            } catch (\Exception $e) {
                $this->error('❌ Erro ao aplicar permissões: ' . $e->getMessage());
            }
        }

        // Limpar caches
        $this->info('🧹 Limpando caches...');
        $this->call('cache:clear');
        $this->call('config:cache');
        
        $this->info('🎉 Processo de correção de permissões concluído!');
        
        return 0;
    }
}