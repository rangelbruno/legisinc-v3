<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class BackupDadosCriticos extends Command
{
    protected $signature = 'backup:dados-criticos {--restore : Restaurar dados do backup}';

    protected $description = 'Faz backup ou restaura dados críticos do sistema';

    private $backupPath;

    public function __construct()
    {
        parent::__construct();
        $this->backupPath = storage_path('backups');
    }

    public function handle()
    {
        if ($this->option('restore')) {
            return $this->restaurarDados();
        } else {
            return $this->fazerBackup();
        }
    }

    private function fazerBackup()
    {
        $this->info('💾 Fazendo backup de dados críticos...');

        // Criar diretório de backup se não existir
        if (! File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }

        $dadosBackup = [];

        // Tabelas críticas do sistema
        $tabelasCriticas = [
            'ai_configurations',
            'parametros',
            'parametros_modulos',
            'parametros_submodulos',
            'parametros_campos',
            'template_padraos',
            'tipo_proposicao_templates',
            'tipo_proposicoes',
            'screen_permissions',
            'roles',
            'permissions',
            'model_has_roles',
            'model_has_permissions',
            'role_has_permissions',
        ];

        foreach ($tabelasCriticas as $tabela) {
            if (Schema::hasTable($tabela)) {
                try {
                    $dados = DB::table($tabela)->get()->toArray();
                    if (! empty($dados)) {
                        $dadosBackup[$tabela] = $dados;
                        $this->line("  ✓ Backup de {$tabela}: ".count($dados).' registros');
                    }
                } catch (\Exception $e) {
                    $this->warn("  ⚠️ Erro ao fazer backup de {$tabela}: ".$e->getMessage());
                }
            }
        }

        // Salvar backup com timestamp
        $timestamp = date('Y-m-d_H-i-s');
        $backupFile = $this->backupPath."/dados-criticos_{$timestamp}.json";
        File::put($backupFile, json_encode($dadosBackup, JSON_PRETTY_PRINT));

        // Manter link para o backup mais recente
        $latestBackup = $this->backupPath.'/dados-criticos-latest.json';
        if (File::exists($latestBackup)) {
            File::delete($latestBackup);
        }
        File::copy($backupFile, $latestBackup);

        $this->info("✅ Backup salvo em: {$backupFile}");
        $this->info("✅ Link atualizado: {$latestBackup}");

        // Limpar backups antigos (manter apenas os últimos 10)
        $this->limparBackupsAntigos();

        return 0;
    }

    private function restaurarDados()
    {
        $this->info('♻️ Restaurando dados do backup...');

        // Procurar backup mais recente
        $latestBackup = $this->backupPath.'/dados-criticos-latest.json';

        if (! File::exists($latestBackup)) {
            $this->error('❌ Nenhum backup encontrado!');
            $this->warn('Execute primeiro: php artisan backup:dados-criticos');

            return 1;
        }

        $conteudo = File::get($latestBackup);
        $dadosBackup = json_decode($conteudo, true);

        if (empty($dadosBackup)) {
            $this->error('❌ Backup vazio ou corrompido!');

            return 1;
        }

        $this->info('📊 Backup encontrado com dados de '.count($dadosBackup).' tabelas');

        // Confirmar com o usuário
        if (! $this->confirm('⚠️ ATENÇÃO: Isso irá sobrescrever os dados atuais. Deseja continuar?')) {
            $this->info('Operação cancelada pelo usuário.');

            return 0;
        }

        DB::beginTransaction();

        try {
            foreach ($dadosBackup as $tabela => $dados) {
                if (Schema::hasTable($tabela) && ! empty($dados)) {
                    // Limpar tabela
                    DB::table($tabela)->truncate();

                    // Inserir dados em lotes
                    $chunks = array_chunk($dados, 100);
                    foreach ($chunks as $chunk) {
                        DB::table($tabela)->insert($chunk);
                    }

                    $this->line("  ✓ Restaurado {$tabela}: ".count($dados).' registros');
                }
            }

            DB::commit();
            $this->info('✅ Dados restaurados com sucesso!');

            // Limpar caches
            $this->call('cache:clear');
            $this->call('config:clear');

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Erro ao restaurar dados: '.$e->getMessage());

            return 1;
        }
    }

    private function limparBackupsAntigos()
    {
        $files = File::glob($this->backupPath.'/dados-criticos_*.json');

        if (count($files) > 10) {
            // Ordenar por data (mais antigos primeiro)
            usort($files, function ($a, $b) {
                return filemtime($a) - filemtime($b);
            });

            // Remover arquivos mais antigos
            $toDelete = array_slice($files, 0, count($files) - 10);
            foreach ($toDelete as $file) {
                File::delete($file);
                $this->line('  🗑️ Removido backup antigo: '.basename($file));
            }
        }
    }
}
