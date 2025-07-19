<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Parametro\ConfiguracaoParametroService;
use App\Services\Parametro\ParametroService;

class ParametrosValidarTodos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parametros:validate-all 
                            {--fix : Tentar corrigir problemas encontrados}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validar integridade de todos os parâmetros do sistema';

    protected ConfiguracaoParametroService $configuracaoService;
    protected ParametroService $parametroService;

    /**
     * Create a new command instance.
     */
    public function __construct(
        ConfiguracaoParametroService $configuracaoService,
        ParametroService $parametroService
    ) {
        parent::__construct();
        $this->configuracaoService = $configuracaoService;
        $this->parametroService = $parametroService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fix = $this->option('fix');

        $this->info('Validando integridade do sistema de parâmetros...');

        try {
            // Validar integridade
            $resultado = $this->configuracaoService->validarIntegridade();

            if ($resultado['integro']) {
                $this->info('✓ Sistema de parâmetros íntegro!');
            } else {
                $this->error('✗ Problemas encontrados:');
                foreach ($resultado['erros'] as $erro) {
                    $this->error("  - {$erro}");
                }

                if ($fix) {
                    $this->info('Tentando corrigir problemas...');
                    // Implementar correções automáticas se necessário
                }
            }

            // Exibir estatísticas
            $this->mostrarEstatisticas();

            return $resultado['integro'] ? 0 : 1;

        } catch (\Exception $e) {
            $this->error("Erro durante a validação: {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * Exibe estatísticas do sistema de parâmetros
     */
    protected function mostrarEstatisticas(): void
    {
        $modulos = \App\Models\Parametro\ParametroModulo::count();
        $modulosAtivos = \App\Models\Parametro\ParametroModulo::where('ativo', true)->count();
        $submodulos = \App\Models\Parametro\ParametroSubmodulo::count();
        $submodulosAtivos = \App\Models\Parametro\ParametroSubmodulo::where('ativo', true)->count();
        $campos = \App\Models\Parametro\ParametroCampo::count();
        $camposAtivos = \App\Models\Parametro\ParametroCampo::where('ativo', true)->count();
        $valores = \App\Models\Parametro\ParametroValor::count();
        $valoresValidos = \App\Models\Parametro\ParametroValor::where(function ($query) {
            $query->whereNull('valido_ate')
                  ->orWhere('valido_ate', '>', now());
        })->count();

        $this->newLine();
        $this->info('Estatísticas do Sistema:');
        $this->table(
            ['Componente', 'Total', 'Ativos/Válidos'],
            [
                ['Módulos', $modulos, $modulosAtivos],
                ['Submódulos', $submodulos, $submodulosAtivos],
                ['Campos', $campos, $camposAtivos],
                ['Valores', $valores, $valoresValidos],
            ]
        );
    }
}