<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Parametro\ConfiguracaoParametroService;

class ParametrosMigrarExistentes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parametros:migrate-existing 
                            {--dry-run : Executar sem fazer alterações}
                            {--force : Forçar migração mesmo se já existirem dados}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrar parâmetros existentes para o novo sistema modular';

    protected ConfiguracaoParametroService $configuracaoService;

    /**
     * Create a new command instance.
     */
    public function __construct(ConfiguracaoParametroService $configuracaoService)
    {
        parent::__construct();
        $this->configuracaoService = $configuracaoService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('Iniciando migração de parâmetros existentes...');

        if ($dryRun) {
            $this->warn('Executando em modo dry-run. Nenhuma alteração será feita.');
        }

        try {
            // Verificar se já existem parâmetros modulares
            $modulosExistentes = \App\Models\Parametro\ParametroModulo::count();
            
            if ($modulosExistentes > 0 && !$force) {
                $this->warn("Já existem {$modulosExistentes} módulos no sistema.");
                
                if (!$this->confirm('Deseja continuar mesmo assim?')) {
                    $this->info('Migração cancelada.');
                    return 0;
                }
            }

            if (!$dryRun) {
                $this->configuracaoService->migrarParametrosExistentes();
            }

            $this->info('Migração concluída com sucesso!');

            // Exibir estatísticas
            if (!$dryRun) {
                $this->mostrarEstatisticas();
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("Erro durante a migração: {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * Exibe estatísticas do sistema de parâmetros
     */
    protected function mostrarEstatisticas(): void
    {
        $modulos = \App\Models\Parametro\ParametroModulo::count();
        $submodulos = \App\Models\Parametro\ParametroSubmodulo::count();
        $campos = \App\Models\Parametro\ParametroCampo::count();
        $valores = \App\Models\Parametro\ParametroValor::count();

        $this->table(
            ['Componente', 'Quantidade'],
            [
                ['Módulos', $modulos],
                ['Submódulos', $submodulos],
                ['Campos', $campos],
                ['Valores', $valores],
            ]
        );
    }
}