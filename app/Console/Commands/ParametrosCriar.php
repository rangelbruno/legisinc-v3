<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Parametro\ParametroService;
use App\Services\Parametro\ValidacaoParametroService;

class ParametrosCriar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parametros:create 
                            {modulo : Nome do módulo}
                            {submodulo : Nome do submódulo}
                            {--tipo=form : Tipo do submódulo (form, checkbox, select, toggle, custom)}
                            {--descricao= : Descrição do submódulo}
                            {--icon= : Ícone do módulo}
                            {--ativo=true : Se deve estar ativo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Criar um novo módulo/submódulo de parâmetros';

    protected ParametroService $parametroService;
    protected ValidacaoParametroService $validacaoService;

    /**
     * Create a new command instance.
     */
    public function __construct(
        ParametroService $parametroService,
        ValidacaoParametroService $validacaoService
    ) {
        parent::__construct();
        $this->parametroService = $parametroService;
        $this->validacaoService = $validacaoService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $nomeModulo = $this->argument('modulo');
        $nomeSubmodulo = $this->argument('submodulo');
        $tipo = $this->option('tipo');
        $descricao = $this->option('descricao');
        $icon = $this->option('icon');
        $ativo = $this->option('ativo') === 'true';

        $this->info("Criando módulo/submódulo: {$nomeModulo} > {$nomeSubmodulo}");

        try {
            // Verificar se o módulo já existe
            $modulos = $this->parametroService->obterModulos();
            $modulo = $modulos->where('nome', $nomeModulo)->first();

            if (!$modulo) {
                $this->info("Módulo '{$nomeModulo}' não encontrado. Criando...");
                
                // Criar o módulo
                $dadosModulo = [
                    'nome' => $nomeModulo,
                    'descricao' => $descricao ?: "Módulo {$nomeModulo}",
                    'icon' => $icon ?: 'ki-setting-2',
                    'ativo' => $ativo,
                ];

                $validacao = $this->validacaoService->validarCriacaoModulo($dadosModulo);
                if (!$validacao['valido']) {
                    $this->error('Erro na validação do módulo:');
                    foreach ($validacao['erros'] as $erro) {
                        $this->error("- {$erro}");
                    }
                    return 1;
                }

                $modulo = $this->parametroService->criarModulo($dadosModulo);
                $this->info("Módulo '{$nomeModulo}' criado com sucesso!");
            } else {
                $this->info("Módulo '{$nomeModulo}' já existe. Usando existente.");
            }

            // Criar o submódulo
            $dadosSubmodulo = [
                'modulo_id' => $modulo->id,
                'nome' => $nomeSubmodulo,
                'descricao' => $descricao ?: "Submódulo {$nomeSubmodulo}",
                'tipo' => $tipo,
                'ativo' => $ativo,
            ];

            $validacao = $this->validacaoService->validarCriacaoSubmodulo($dadosSubmodulo);
            if (!$validacao['valido']) {
                $this->error('Erro na validação do submódulo:');
                foreach ($validacao['erros'] as $erro) {
                    $this->error("- {$erro}");
                }
                return 1;
            }

            $submodulo = $this->parametroService->criarSubmodulo($dadosSubmodulo);
            $this->info("Submódulo '{$nomeSubmodulo}' criado com sucesso!");

            // Exibir informações
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['Módulo ID', $modulo->id],
                    ['Módulo Nome', $modulo->nome],
                    ['Submódulo ID', $submodulo->id],
                    ['Submódulo Nome', $submodulo->nome],
                    ['Tipo', $submodulo->tipo],
                    ['Status', $submodulo->ativo ? 'Ativo' : 'Inativo'],
                ]
            );

            return 0;

        } catch (\Exception $e) {
            $this->error("Erro ao criar módulo/submódulo: {$e->getMessage()}");
            return 1;
        }
    }
}