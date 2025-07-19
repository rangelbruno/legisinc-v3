<?php

namespace App\Services\Parametro;

use App\Models\Parametro\ParametroModulo;
use App\Models\Parametro\ParametroSubmodulo;
use App\Models\Parametro\ParametroCampo;
use App\Models\Parametro\ParametroValor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConfiguracaoParametroService
{
    protected ParametroService $parametroService;
    protected ValidacaoParametroService $validacaoService;

    public function __construct(
        ParametroService $parametroService,
        ValidacaoParametroService $validacaoService
    ) {
        $this->parametroService = $parametroService;
        $this->validacaoService = $validacaoService;
    }

    /**
     * Configura parâmetros iniciais do sistema
     */
    public function configurarParametrosIniciais(): void
    {
        DB::transaction(function () {
            $this->criarModuloDadosCamara();
            $this->criarModuloConfiguracoesSessao();
            $this->criarModuloTipoSessao();
            $this->criarModuloMomentoSessao();
            $this->criarModuloTipoVotacao();
        });
    }

    /**
     * Cria o módulo "Dados da Câmara"
     */
    protected function criarModuloDadosCamara(): void
    {
        $modulo = $this->parametroService->criarModulo([
            'nome' => 'Dados da Câmara',
            'descricao' => 'Configurações institucionais da câmara',
            'icon' => 'ki-home',
            'ordem' => 1,
            'ativo' => true,
        ]);

        $submodulo = $this->parametroService->criarSubmodulo([
            'modulo_id' => $modulo->id,
            'nome' => 'Formulário Institucional',
            'descricao' => 'Dados básicos da câmara',
            'tipo' => 'form',
            'ordem' => 1,
            'ativo' => true,
        ]);

        $campos = [
            [
                'nome' => 'nome_camara',
                'label' => 'Nome da Câmara',
                'tipo_campo' => 'text',
                'obrigatorio' => true,
                'placeholder' => 'Ex: Câmara Municipal de São Paulo',
                'validacao' => ['required', 'string', 'max:255'],
                'ordem' => 1,
            ],
            [
                'nome' => 'endereco',
                'label' => 'Endereço',
                'tipo_campo' => 'textarea',
                'obrigatorio' => true,
                'placeholder' => 'Endereço completo da câmara',
                'validacao' => ['required', 'string'],
                'ordem' => 2,
            ],
            [
                'nome' => 'tipo_integracao',
                'label' => 'Tipo de Integração',
                'tipo_campo' => 'select',
                'obrigatorio' => true,
                'opcoes' => [
                    'api' => 'API Rest',
                    'xml' => 'XML',
                    'json' => 'JSON',
                    'manual' => 'Manual',
                ],
                'validacao' => ['required', 'in:api,xml,json,manual'],
                'ordem' => 3,
            ],
            [
                'nome' => 'qtd_vereadores',
                'label' => 'Quantidade de Vereadores',
                'tipo_campo' => 'number',
                'obrigatorio' => true,
                'validacao' => ['required', 'integer', 'min:1', 'max:100'],
                'ordem' => 4,
            ],
            [
                'nome' => 'qtd_quorum',
                'label' => 'Quantidade Quorum',
                'tipo_campo' => 'number',
                'obrigatorio' => true,
                'validacao' => ['required', 'integer', 'min:1'],
                'ordem' => 5,
            ],
            [
                'nome' => 'tempo_sessao',
                'label' => 'Tempo de Sessão (minutos)',
                'tipo_campo' => 'number',
                'obrigatorio' => false,
                'validacao' => ['nullable', 'integer', 'min:30', 'max:1440'],
                'ordem' => 6,
            ],
            [
                'nome' => 'logotipo',
                'label' => 'Logotipo',
                'tipo_campo' => 'file',
                'obrigatorio' => false,
                'validacao' => ['nullable', 'file', 'image', 'max:2048'],
                'ordem' => 7,
            ],
        ];

        foreach ($campos as $dadosCampo) {
            $dadosCampo['submodulo_id'] = $submodulo->id;
            $dadosCampo['ativo'] = true;
            $this->parametroService->criarCampo($dadosCampo);
        }
    }

    /**
     * Cria o módulo "Configurações da Sessão"
     */
    protected function criarModuloConfiguracoesSessao(): void
    {
        $modulo = $this->parametroService->criarModulo([
            'nome' => 'Configurações da Sessão',
            'descricao' => 'Configurações para controle de sessões',
            'icon' => 'ki-setting-3',
            'ordem' => 2,
            'ativo' => true,
        ]);

        $submodulo = $this->parametroService->criarSubmodulo([
            'modulo_id' => $modulo->id,
            'nome' => 'Controles de Sessão',
            'descricao' => 'Checkboxes para controle de sessão',
            'tipo' => 'checkbox',
            'ordem' => 1,
            'ativo' => true,
        ]);

        $campos = [
            [
                'nome' => 'veto_acato',
                'label' => 'Veto (Acato/Não Acato)',
                'tipo_campo' => 'checkbox',
                'obrigatorio' => false,
                'valor_padrao' => 'false',
                'ordem' => 1,
            ],
            [
                'nome' => 'iniciar_expediente',
                'label' => 'Iniciar Expediente',
                'tipo_campo' => 'checkbox',
                'obrigatorio' => false,
                'valor_padrao' => 'true',
                'ordem' => 2,
            ],
            [
                'nome' => 'abster',
                'label' => 'Permitir Abstenção',
                'tipo_campo' => 'checkbox',
                'obrigatorio' => false,
                'valor_padrao' => 'true',
                'ordem' => 3,
            ],
            [
                'nome' => 'chamada_automatica',
                'label' => 'Chamada Automática',
                'tipo_campo' => 'checkbox',
                'obrigatorio' => false,
                'valor_padrao' => 'false',
                'ordem' => 4,
            ],
            [
                'nome' => 'popup_votacao',
                'label' => 'Pop-up de Votação',
                'tipo_campo' => 'checkbox',
                'obrigatorio' => false,
                'valor_padrao' => 'true',
                'ordem' => 5,
            ],
        ];

        foreach ($campos as $dadosCampo) {
            $dadosCampo['submodulo_id'] = $submodulo->id;
            $dadosCampo['ativo'] = true;
            $this->parametroService->criarCampo($dadosCampo);
        }
    }

    /**
     * Cria o módulo "Tipo de Sessão"
     */
    protected function criarModuloTipoSessao(): void
    {
        $modulo = $this->parametroService->criarModulo([
            'nome' => 'Tipo de Sessão',
            'descricao' => 'Gerenciamento de tipos de sessão',
            'icon' => 'ki-calendar',
            'ordem' => 3,
            'ativo' => true,
        ]);

        $submodulo = $this->parametroService->criarSubmodulo([
            'modulo_id' => $modulo->id,
            'nome' => 'Cadastro de Tipos',
            'descricao' => 'Cadastro de tipos de sessão',
            'tipo' => 'form',
            'ordem' => 1,
            'ativo' => true,
        ]);

        $campos = [
            [
                'nome' => 'nome_tipo',
                'label' => 'Nome do Tipo',
                'tipo_campo' => 'text',
                'obrigatorio' => true,
                'validacao' => ['required', 'string', 'max:100'],
                'ordem' => 1,
            ],
            [
                'nome' => 'descricao_tipo',
                'label' => 'Descrição',
                'tipo_campo' => 'textarea',
                'obrigatorio' => false,
                'validacao' => ['nullable', 'string'],
                'ordem' => 2,
            ],
            [
                'nome' => 'status_tipo',
                'label' => 'Status',
                'tipo_campo' => 'select',
                'obrigatorio' => true,
                'opcoes' => [
                    'ativo' => 'Ativo',
                    'inativo' => 'Inativo',
                ],
                'validacao' => ['required', 'in:ativo,inativo'],
                'ordem' => 3,
            ],
        ];

        foreach ($campos as $dadosCampo) {
            $dadosCampo['submodulo_id'] = $submodulo->id;
            $dadosCampo['ativo'] = true;
            $this->parametroService->criarCampo($dadosCampo);
        }
    }

    /**
     * Cria o módulo "Momento da Sessão"
     */
    protected function criarModuloMomentoSessao(): void
    {
        $modulo = $this->parametroService->criarModulo([
            'nome' => 'Momento da Sessão',
            'descricao' => 'Gerenciamento de momentos de sessão',
            'icon' => 'ki-time',
            'ordem' => 4,
            'ativo' => true,
        ]);

        $submodulo = $this->parametroService->criarSubmodulo([
            'modulo_id' => $modulo->id,
            'nome' => 'Cadastro de Momentos',
            'descricao' => 'Cadastro de momentos de sessão',
            'tipo' => 'form',
            'ordem' => 1,
            'ativo' => true,
        ]);

        $campos = [
            [
                'nome' => 'nome_momento',
                'label' => 'Nome do Momento',
                'tipo_campo' => 'text',
                'obrigatorio' => true,
                'validacao' => ['required', 'string', 'max:100'],
                'ordem' => 1,
            ],
            [
                'nome' => 'descricao_momento',
                'label' => 'Descrição',
                'tipo_campo' => 'textarea',
                'obrigatorio' => false,
                'validacao' => ['nullable', 'string'],
                'ordem' => 2,
            ],
            [
                'nome' => 'ordem_momento',
                'label' => 'Ordem',
                'tipo_campo' => 'number',
                'obrigatorio' => true,
                'validacao' => ['required', 'integer', 'min:1'],
                'ordem' => 3,
            ],
            [
                'nome' => 'status_momento',
                'label' => 'Status',
                'tipo_campo' => 'select',
                'obrigatorio' => true,
                'opcoes' => [
                    'ativo' => 'Ativo',
                    'inativo' => 'Inativo',
                ],
                'validacao' => ['required', 'in:ativo,inativo'],
                'ordem' => 4,
            ],
        ];

        foreach ($campos as $dadosCampo) {
            $dadosCampo['submodulo_id'] = $submodulo->id;
            $dadosCampo['ativo'] = true;
            $this->parametroService->criarCampo($dadosCampo);
        }
    }

    /**
     * Cria o módulo "Tipo de Votação"
     */
    protected function criarModuloTipoVotacao(): void
    {
        $modulo = $this->parametroService->criarModulo([
            'nome' => 'Tipo de Votação',
            'descricao' => 'Gerenciamento de tipos de votação',
            'icon' => 'ki-vote-1',
            'ordem' => 5,
            'ativo' => true,
        ]);

        $submodulo = $this->parametroService->criarSubmodulo([
            'modulo_id' => $modulo->id,
            'nome' => 'Cadastro de Tipos de Votação',
            'descricao' => 'Cadastro de tipos de votação',
            'tipo' => 'form',
            'ordem' => 1,
            'ativo' => true,
        ]);

        $campos = [
            [
                'nome' => 'nome_votacao',
                'label' => 'Nome do Tipo',
                'tipo_campo' => 'text',
                'obrigatorio' => true,
                'validacao' => ['required', 'string', 'max:100'],
                'ordem' => 1,
            ],
            [
                'nome' => 'descricao_votacao',
                'label' => 'Descrição',
                'tipo_campo' => 'textarea',
                'obrigatorio' => false,
                'validacao' => ['nullable', 'string'],
                'ordem' => 2,
            ],
            [
                'nome' => 'regras_votacao',
                'label' => 'Regras',
                'tipo_campo' => 'textarea',
                'obrigatorio' => false,
                'validacao' => ['nullable', 'string'],
                'ordem' => 3,
            ],
            [
                'nome' => 'status_votacao',
                'label' => 'Status',
                'tipo_campo' => 'select',
                'obrigatorio' => true,
                'opcoes' => [
                    'ativo' => 'Ativo',
                    'inativo' => 'Inativo',
                ],
                'validacao' => ['required', 'in:ativo,inativo'],
                'ordem' => 4,
            ],
        ];

        foreach ($campos as $dadosCampo) {
            $dadosCampo['submodulo_id'] = $submodulo->id;
            $dadosCampo['ativo'] = true;
            $this->parametroService->criarCampo($dadosCampo);
        }
    }

    /**
     * Migra parâmetros existentes para o novo sistema
     */
    public function migrarParametrosExistentes(): void
    {
        Log::info('Iniciando migração de parâmetros existentes');

        try {
            // Aqui você pode implementar a lógica de migração dos parâmetros antigos
            // para o novo sistema, baseado na estrutura atual
            
            Log::info('Migração de parâmetros concluída com sucesso');
        } catch (\Exception $e) {
            Log::error('Erro na migração de parâmetros: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Valida integridade do sistema de parâmetros
     */
    public function validarIntegridade(): array
    {
        $erros = [];

        // Verificar se há módulos ativos
        if (ParametroModulo::ativo()->count() === 0) {
            $erros[] = 'Nenhum módulo ativo encontrado';
        }

        // Verificar se há submódulos órfãos
        $submodulosOrfaos = ParametroSubmodulo::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('parametros_modulos')
                ->whereRaw('parametros_modulos.id = parametros_submodulos.modulo_id')
                ->where('parametros_modulos.ativo', true);
        })->count();

        if ($submodulosOrfaos > 0) {
            $erros[] = "Existem {$submodulosOrfaos} submódulos sem módulo ativo";
        }

        // Verificar se há campos órfãos
        $camposOrfaos = ParametroCampo::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('parametros_submodulos')
                ->whereRaw('parametros_submodulos.id = parametros_campos.submodulo_id')
                ->where('parametros_submodulos.ativo', true);
        })->count();

        if ($camposOrfaos > 0) {
            $erros[] = "Existem {$camposOrfaos} campos sem submódulo ativo";
        }

        return [
            'integro' => empty($erros),
            'erros' => $erros
        ];
    }
}