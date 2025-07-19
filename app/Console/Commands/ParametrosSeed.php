<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Parametro\ConfiguracaoParametroService;

class ParametrosSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parametros:seed 
                            {--modulo= : Seed apenas um módulo específico}
                            {--all : Seed todos os módulos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed parâmetros iniciais do sistema';

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
        $modulo = $this->option('modulo');
        $all = $this->option('all');

        if (!$modulo && !$all) {
            $this->error('Especifique --modulo=<nome> ou --all');
            return 1;
        }

        $this->info('Iniciando seed de parâmetros...');

        try {
            if ($all) {
                $this->configuracaoService->configurarParametrosIniciais();
                $this->info('Todos os parâmetros iniciais foram criados com sucesso!');
            } else {
                $this->seedModuloEspecifico($modulo);
            }

            // Exibir estatísticas
            $this->mostrarEstatisticas();

            return 0;

        } catch (\Exception $e) {
            $this->error("Erro durante o seed: {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * Seed um módulo específico
     */
    protected function seedModuloEspecifico(string $nomeModulo): void
    {
        $metodosDisponiveis = [
            'dados_camara' => 'criarModuloDadosCamara',
            'configuracoes_sessao' => 'criarModuloConfiguracoesSessao',
            'tipo_sessao' => 'criarModuloTipoSessao',
            'momento_sessao' => 'criarModuloMomentoSessao',
            'tipo_votacao' => 'criarModuloTipoVotacao',
        ];

        $nomeModuloNormalizado = strtolower(str_replace([' ', '-'], '_', $nomeModulo));

        if (!isset($metodosDisponiveis[$nomeModuloNormalizado])) {
            $this->error("Módulo '{$nomeModulo}' não encontrado.");
            $this->info('Módulos disponíveis:');
            foreach (array_keys($metodosDisponiveis) as $modulo) {
                $this->info("  - {$modulo}");
            }
            return;
        }

        $metodo = $metodosDisponiveis[$nomeModuloNormalizado];
        
        // Usar reflection para chamar o método privado
        $reflection = new \ReflectionClass($this->configuracaoService);
        $method = $reflection->getMethod($metodo);
        $method->setAccessible(true);
        $method->invoke($this->configuracaoService);

        $this->info("Módulo '{$nomeModulo}' criado com sucesso!");
    }

    /**
     * Exibe estatísticas do sistema de parâmetros
     */
    protected function mostrarEstatisticas(): void
    {
        $modulos = \App\Models\Parametro\ParametroModulo::count();
        $submodulos = \App\Models\Parametro\ParametroSubmodulo::count();
        $campos = \App\Models\Parametro\ParametroCampo::count();

        $this->table(
            ['Componente', 'Quantidade'],
            [
                ['Módulos', $modulos],
                ['Submódulos', $submodulos],
                ['Campos', $campos],
            ]
        );
    }
}