<?php

namespace App\Http\Controllers;

use App\Services\Parametro\ParametroService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DadosGeraisCamaraController extends Controller
{
    protected ParametroService $parametroService;

    public function __construct(ParametroService $parametroService)
    {
        $this->parametroService = $parametroService;
    }

    /**
     * Mostrar tela de configuração dos dados gerais da câmara
     */
    public function index(): View
    {
        // Obter configurações atuais
        $configuracoes = $this->obterConfiguracoes();
        
        return view('modules.parametros.dados-gerais-camara', compact('configuracoes'));
    }

    /**
     * Salvar configurações dos dados gerais
     */
    public function store(Request $request): JsonResponse
    {
        $saveTab = $request->input('save_tab');
        
        // Define validation rules by tab
        $validationRules = $this->getValidationRulesByTab($saveTab);
        
        // Apply validation only for the current tab fields
        $request->validate($validationRules);

        try {
            // Save only the fields provided in the request based on the tab
            $this->saveTabFields($request, $saveTab);

            $tabNames = [
                'identificacao' => 'Identificação',
                'endereco' => 'Endereço',
                'contatos' => 'Contatos',
                'funcionamento' => 'Funcionamento',
                'gestao' => 'Gestão Atual'
            ];

            $tabDisplayName = $tabNames[$saveTab] ?? 'dados';

            return response()->json([
                'success' => true,
                'message' => "Dados da aba \"$tabDisplayName\" salvos com sucesso!",
                'saved_tab' => $saveTab
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter configurações atuais
     */
    private function obterConfiguracoes(): array
    {
        try {
            // Estratégia agressiva: limpar caches principais
            \Cache::flush(); // Limpa todo o cache Redis
            
            // Limpar OpCache se disponível
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }
            
            // Buscar dados DIRETAMENTE do banco, bypassing o ParametroService temporariamente
            $configuracoes = $this->obterConfiguracoesDiretas();
            
            return $configuracoes;
        } catch (\Exception $e) {
            // Se houver erro, usar valores padrão
            return [
                'nome_camara' => 'Câmara Municipal',
                'sigla_camara' => 'CM',
                'cnpj' => '',
                'endereco' => '',
                'numero' => '',
                'complemento' => '',
                'bairro' => '',
                'cidade' => '',
                'estado' => 'SP',
                'cep' => '',
                'telefone' => '',
                'telefone_secundario' => '',
                'email_institucional' => '',
                'email_contato' => '',
                'website' => '',
                'horario_funcionamento' => 'Segunda a Sexta, 8h às 17h',
                'horario_atendimento' => 'Segunda a Sexta, 8h às 16h',
                'presidente_nome' => '',
                'presidente_partido' => '',
                'legislatura_atual' => '2021-2024',
                'numero_vereadores' => 9
            ];
        }
    }

    /**
     * Obter configurações diretamente do banco de dados (bypass cache)
     */
    private function obterConfiguracoesDiretas(): array
    {
        $configuracoes = [];
        
        // Query direta para buscar todos os valores de uma vez
        $valores = DB::table('parametros_valores as pv')
            ->join('parametros_campos as pc', 'pv.campo_id', '=', 'pc.id')
            ->join('parametros_submodulos as ps', 'pc.submodulo_id', '=', 'ps.id')
            ->join('parametros_modulos as pm', 'ps.modulo_id', '=', 'pm.id')
            ->where('pm.nome', 'Dados Gerais')
            ->whereNull('pv.valido_ate')
            ->select('pc.nome as campo', 'ps.nome as submodulo', 'pv.valor')
            ->get()
            ->keyBy('campo');

        // Mapear os valores ou usar defaults
        $configuracoes = [
            // Identificação
            'nome_camara' => optional($valores->get('nome_camara'))->valor ?? 'Câmara Municipal',
            'sigla_camara' => optional($valores->get('sigla_camara'))->valor ?? 'CM',
            'cnpj' => optional($valores->get('cnpj'))->valor ?? '',
            
            // Endereço
            'endereco' => optional($valores->get('endereco'))->valor ?? '',
            'numero' => optional($valores->get('numero'))->valor ?? '',
            'complemento' => optional($valores->get('complemento'))->valor ?? '',
            'bairro' => optional($valores->get('bairro'))->valor ?? '',
            'cidade' => optional($valores->get('cidade'))->valor ?? '',
            'estado' => optional($valores->get('estado'))->valor ?? 'SP',
            'cep' => optional($valores->get('cep'))->valor ?? '',
            
            // Contatos
            'telefone' => optional($valores->get('telefone'))->valor ?? '',
            'telefone_secundario' => optional($valores->get('telefone_secundario'))->valor ?? '',
            'email_institucional' => optional($valores->get('email_institucional'))->valor ?? '',
            'email_contato' => optional($valores->get('email_contato'))->valor ?? '',
            'website' => optional($valores->get('website'))->valor ?? '',
            
            // Funcionamento
            'horario_funcionamento' => optional($valores->get('horario_funcionamento'))->valor ?? 'Segunda a Sexta, 8h às 17h',
            'horario_atendimento' => optional($valores->get('horario_atendimento'))->valor ?? 'Segunda a Sexta, 8h às 16h',
            
            // Gestão
            'presidente_nome' => optional($valores->get('presidente_nome'))->valor ?? '',
            'presidente_partido' => optional($valores->get('presidente_partido'))->valor ?? '',
            'legislatura_atual' => optional($valores->get('legislatura_atual'))->valor ?? '2021-2024',
            'numero_vereadores' => optional($valores->get('numero_vereadores'))->valor ?? 9
        ];

        return $configuracoes;
    }

    /**
     * Get validation rules for a specific tab
     */
    private function getValidationRulesByTab(string $tab): array
    {
        $allRules = [
            'identificacao' => [
                'nome_camara' => 'required|string|max:255',
                'sigla_camara' => 'required|string|max:20',
                'cnpj' => 'nullable|string|max:20',
            ],
            'endereco' => [
                'endereco' => 'required|string|max:255',
                'numero' => 'nullable|string|max:20',
                'complemento' => 'nullable|string|max:100',
                'bairro' => 'required|string|max:100',
                'cidade' => 'required|string|max:100',
                'estado' => 'required|string|max:3',
                'cep' => 'required|string|max:12',
            ],
            'contatos' => [
                'telefone' => 'required|string|max:20',
                'telefone_secundario' => 'nullable|string|max:20',
                'email_institucional' => 'required|email|max:255',
                'email_contato' => 'nullable|email|max:255',
                'website' => 'nullable|string|max:255',
            ],
            'funcionamento' => [
                'horario_funcionamento' => 'required|string|max:100',
                'horario_atendimento' => 'required|string|max:100',
            ],
            'gestao' => [
                'presidente_nome' => 'required|string|max:255',
                'presidente_partido' => 'required|string|max:50',
                'legislatura_atual' => 'required|string|max:50',
                'numero_vereadores' => 'required|integer|min:5|max:55',
            ]
        ];

        return $allRules[$tab] ?? [];
    }

    /**
     * Save fields for a specific tab
     */
    private function saveTabFields(Request $request, string $tab): void
    {
        $tabMapping = [
            'identificacao' => [
                'submodule' => 'Identificação',
                'fields' => ['nome_camara', 'sigla_camara', 'cnpj']
            ],
            'endereco' => [
                'submodule' => 'Endereço',
                'fields' => ['endereco', 'numero', 'complemento', 'bairro', 'cidade', 'estado', 'cep']
            ],
            'contatos' => [
                'submodule' => 'Contatos',
                'fields' => ['telefone', 'telefone_secundario', 'email_institucional', 'email_contato', 'website']
            ],
            'funcionamento' => [
                'submodule' => 'Funcionamento',
                'fields' => ['horario_funcionamento', 'horario_atendimento']
            ],
            'gestao' => [
                'submodule' => 'Gestão',
                'fields' => ['presidente_nome', 'presidente_partido', 'legislatura_atual', 'numero_vereadores']
            ]
        ];

        if (!isset($tabMapping[$tab])) {
            throw new \InvalidArgumentException("Invalid tab: $tab");
        }

        $config = $tabMapping[$tab];
        $submoduleName = $config['submodule'];
        $fields = $config['fields'];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $valor = $request->input($field);
                $this->parametroService->salvarValor('Dados Gerais', $submoduleName, $field, $valor);
            }
        }
    }
}