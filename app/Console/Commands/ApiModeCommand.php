<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ApiModeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:mode {mode?} {--status : Show current API mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gerencia o modo da API (mock ou external)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('status')) {
            $this->showStatus();
            return;
        }

        $mode = $this->argument('mode');

        if (!$mode) {
            $this->showStatus();
            $mode = $this->choice('Escolha o modo da API:', ['mock', 'external'], 0);
        }

        if (!in_array($mode, ['mock', 'external'])) {
            $this->error('Modo inválido. Use "mock" ou "external".');
            return Command::FAILURE;
        }

        $this->setApiMode($mode);
        $this->info("✅ Modo da API alterado para: {$mode}");
        
        // Mostrar informações sobre o modo selecionado
        $this->showModeInfo($mode);
        
        return Command::SUCCESS;
    }

    /**
     * Define o modo da API no arquivo .env
     */
    private function setApiMode(string $mode): void
    {
        $envFile = base_path('.env');
        
        if (!File::exists($envFile)) {
            $this->error('Arquivo .env não encontrado.');
            return;
        }

        $envContent = File::get($envFile);
        
        // Verifica se API_MODE já existe
        if (preg_match('/^API_MODE=.*/m', $envContent)) {
            $envContent = preg_replace('/^API_MODE=.*/m', "API_MODE={$mode}", $envContent);
        } else {
            // Adiciona API_MODE no final do arquivo
            $envContent .= "\nAPI_MODE={$mode}\n";
        }
        
        File::put($envFile, $envContent);
        
        // Limpa o cache de configuração
        $this->call('config:clear');
    }

    /**
     * Mostra o status atual da API
     */
    private function showStatus(): void
    {
        $currentMode = config('api.mode', 'mock');
        
        $this->info("🔄 Status da API:");
        $this->line("   Modo atual: <fg=cyan>{$currentMode}</fg=cyan>");
        
        if ($currentMode === 'mock') {
            $this->line("   URL: <fg=yellow>" . config('api.mock.base_url') . "</fg=yellow>");
            $this->line("   Descrição: " . config('api.mock.description'));
        } else {
            $this->line("   URL: <fg=yellow>" . config('api.external.base_url') . "</fg=yellow>");
            $this->line("   Descrição: " . config('api.external.description'));
        }
        
        $this->newLine();
    }

    /**
     * Mostra informações sobre o modo selecionado
     */
    private function showModeInfo(string $mode): void
    {
        $this->newLine();
        
        if ($mode === 'mock') {
            $this->line("📋 <fg=green>Modo Mock Ativado</fg=green>");
            $this->line("   • Usa MockApiController interno");
            $this->line("   • Ideal para desenvolvimento");
            $this->line("   • Não requer API externa");
            $this->line("   • Dados armazenados em cache");
            $this->line("   • URL: " . config('api.mock.base_url'));
        } else {
            $this->line("🌐 <fg=blue>Modo External Ativado</fg=blue>");
            $this->line("   • Usa API externa");
            $this->line("   • Requer API Node.js rodando");
            $this->line("   • Ideal para produção");
            $this->line("   • Dados persistentes");
            $this->line("   • URL: " . config('api.external.base_url'));
            
            $this->newLine();
            $this->warn("⚠️  Certifique-se de que a API externa está rodando!");
        }
        
        $this->newLine();
        $this->line("💡 Para trocar novamente: <fg=cyan>php artisan api:mode</fg=cyan>");
    }
} 