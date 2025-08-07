<?php

namespace App\Http\Controllers;

use App\Services\Parametro\ParametroService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class VariaveisDinamicasController extends Controller
{
    protected ParametroService $parametroService;

    public function __construct(ParametroService $parametroService)
    {
        $this->parametroService = $parametroService;
    }

    /**
     * Mostrar tela de configuração das variáveis dinâmicas
     */
    public function index(): View
    {
        \Log::info('📋 VariaveisDinamicasController::index chamado', [
            'user' => auth()->user()->email ?? 'não autenticado',
            'timestamp' => now()
        ]);
        
        // Obter configurações atuais
        $configuracoes = $this->obterConfiguracoes();
        
        return view('modules.parametros.variaveis-dinamicas', compact('configuracoes'));
    }

    /**
     * Salvar configurações das variáveis dinâmicas
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'variaveis' => 'required|array|min:1',
            'variaveis.*.nome' => 'required|string|max:100|regex:/^[a-zA-Z_][a-zA-Z0-9_]*$/',
            'variaveis.*.valor' => 'required|string|max:1000',
            'variaveis.*.descricao' => 'nullable|string|max:255',
            'variaveis.*.tipo' => 'required|in:texto,numero,data,boolean,url,email',
            'variaveis.*.escopo' => 'required|in:global,documentos,templates,sistema',
            'variaveis.*.formato' => 'nullable|string|max:100',
            'variaveis.*.validacao' => 'nullable|string|max:200'
        ], [
            'variaveis.required' => 'É necessário configurar pelo menos uma variável dinâmica.',
            'variaveis.*.nome.required' => 'O nome da variável é obrigatório.',
            'variaveis.*.nome.regex' => 'O nome da variável deve começar com letra ou underscore e conter apenas letras, números e underscores.',
            'variaveis.*.valor.required' => 'O valor da variável é obrigatório.',
            'variaveis.*.tipo.required' => 'O tipo da variável é obrigatório.',
            'variaveis.*.tipo.in' => 'O tipo deve ser: texto, numero, data, boolean, url ou email.',
            'variaveis.*.escopo.required' => 'O escopo da variável é obrigatório.',
            'variaveis.*.escopo.in' => 'O escopo deve ser: global, documentos, templates ou sistema.'
        ]);

        try {
            \Log::info('💾 Iniciando salvamento das variáveis dinâmicas', [
                'user' => auth()->user()->email,
                'total_variaveis' => count($request->input('variaveis'))
            ]);

            $variaveis = $request->input('variaveis');
            
            // Validar se não há nomes duplicados
            $nomes = collect($variaveis)->pluck('nome');
            if ($nomes->duplicates()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Existem nomes de variáveis duplicados. Cada variável deve ter um nome único.',
                    'errors' => ['variaveis' => ['Nomes de variáveis duplicados detectados.']]
                ], 422);
            }

            // Processar e validar cada variável
            $variaveisProcessadas = [];
            foreach ($variaveis as $index => $variavel) {
                $variavel['nome'] = strtoupper($variavel['nome']);
                
                // Validações específicas por tipo
                if ($variavel['tipo'] === 'numero' && !is_numeric($variavel['valor'])) {
                    return response()->json([
                        'success' => false,
                        'message' => "O valor da variável '{$variavel['nome']}' deve ser numérico.",
                        'errors' => ["variaveis.{$index}.valor" => ["Valor deve ser numérico."]]
                    ], 422);
                }
                
                if ($variavel['tipo'] === 'email' && !filter_var($variavel['valor'], FILTER_VALIDATE_EMAIL)) {
                    return response()->json([
                        'success' => false,
                        'message' => "O valor da variável '{$variavel['nome']}' deve ser um email válido.",
                        'errors' => ["variaveis.{$index}.valor" => ["Email inválido."]]
                    ], 422);
                }
                
                if ($variavel['tipo'] === 'url' && !filter_var($variavel['valor'], FILTER_VALIDATE_URL)) {
                    return response()->json([
                        'success' => false,
                        'message' => "O valor da variável '{$variavel['nome']}' deve ser uma URL válida.",
                        'errors' => ["variaveis.{$index}.valor" => ["URL inválida."]]
                    ], 422);
                }

                if ($variavel['tipo'] === 'boolean') {
                    $variavel['valor'] = in_array(strtolower($variavel['valor']), ['true', '1', 'sim', 'yes', 'verdadeiro']) ? 'true' : 'false';
                }

                if ($variavel['tipo'] === 'data') {
                    try {
                        $data = \Carbon\Carbon::parse($variavel['valor']);
                        $variavel['valor'] = $data->format('d/m/Y');
                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => "O valor da variável '{$variavel['nome']}' deve ser uma data válida.",
                            'errors' => ["variaveis.{$index}.valor" => ["Data inválida."]]
                        ], 422);
                    }
                }

                $variaveisProcessadas[] = $variavel;
            }

            // Salvar no sistema de parâmetros
            $this->salvarVariaveisParametros($variaveisProcessadas);

            \Log::info('✅ Variáveis dinâmicas salvas com sucesso', [
                'user' => auth()->user()->email,
                'total_variaveis' => count($variaveisProcessadas)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Variáveis dinâmicas configuradas com sucesso!',
                'total_variaveis' => count($variaveisProcessadas),
                'redirect' => route('parametros.dados-gerais-camara') // Temporary redirect
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ Erro ao salvar variáveis dinâmicas', [
                'user' => auth()->user()->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor. Tente novamente.',
                'error' => config('app.debug') ? $e->getMessage() : 'Erro interno'
            ], 500);
        }
    }

    /**
     * Obter configurações atuais das variáveis dinâmicas
     */
    private function obterConfiguracoes(): array
    {
        try {
            // Buscar variáveis do banco de dados
            $variaveis = $this->obterVariaveisParametros();
            
            return [
                'variaveis' => $variaveis,
                'tipos_disponiveis' => [
                    'texto' => 'Texto',
                    'numero' => 'Número',
                    'data' => 'Data',
                    'boolean' => 'Verdadeiro/Falso',
                    'url' => 'URL',
                    'email' => 'Email'
                ],
                'escopos_disponiveis' => [
                    'global' => 'Global (Todo o sistema)',
                    'documentos' => 'Documentos',
                    'templates' => 'Templates',
                    'sistema' => 'Sistema'
                ],
                'variaveis_padrao' => $this->obterVariaveisPadrao()
            ];
        } catch (\Exception $e) {
            \Log::error('❌ Erro ao obter configurações de variáveis dinâmicas', [
                'error' => $e->getMessage()
            ]);

            return [
                'variaveis' => $this->obterVariaveisPadrao(),
                'tipos_disponiveis' => [
                    'texto' => 'Texto',
                    'numero' => 'Número',
                    'data' => 'Data',
                    'boolean' => 'Verdadeiro/Falso',
                    'url' => 'URL',
                    'email' => 'Email'
                ],
                'escopos_disponiveis' => [
                    'global' => 'Global (Todo o sistema)',
                    'documentos' => 'Documentos', 
                    'templates' => 'Templates',
                    'sistema' => 'Sistema'
                ],
                'variaveis_padrao' => $this->obterVariaveisPadrao()
            ];
        }
    }

    /**
     * Obter variáveis padrão do sistema
     */
    private function obterVariaveisPadrao(): array
    {
        return [
            [
                'nome' => 'NOME_CAMARA',
                'valor' => 'Câmara Municipal',
                'descricao' => 'Nome completo da câmara municipal',
                'tipo' => 'texto',
                'escopo' => 'global',
                'formato' => null,
                'validacao' => 'required|string|max:255',
                'sistema' => true
            ],
            [
                'nome' => 'SIGLA_CAMARA',
                'valor' => 'CM',
                'descricao' => 'Sigla da câmara municipal',
                'tipo' => 'texto',
                'escopo' => 'global',
                'formato' => null,
                'validacao' => 'required|string|max:10',
                'sistema' => true
            ],
            [
                'nome' => 'DATA_ATUAL',
                'valor' => date('d/m/Y'),
                'descricao' => 'Data atual do sistema',
                'tipo' => 'data',
                'escopo' => 'global',
                'formato' => 'd/m/Y',
                'validacao' => null,
                'sistema' => true
            ],
            [
                'nome' => 'ANO_ATUAL',
                'valor' => date('Y'),
                'descricao' => 'Ano atual',
                'tipo' => 'numero',
                'escopo' => 'global',
                'formato' => null,
                'validacao' => null,
                'sistema' => true
            ],
            [
                'nome' => 'USUARIO_LOGADO',
                'valor' => auth()->user()->name ?? 'Sistema',
                'descricao' => 'Nome do usuário atualmente logado',
                'tipo' => 'texto',
                'escopo' => 'sistema',
                'formato' => null,
                'validacao' => null,
                'sistema' => true
            ]
        ];
    }

    /**
     * Obter variáveis dos parâmetros
     */
    private function obterVariaveisParametros(): array
    {
        try {
            // Por enquanto retorna as variáveis padrão
            // TODO: Implementar busca no banco quando o módulo for criado
            return $this->obterVariaveisPadrao();
        } catch (\Exception $e) {
            \Log::error('❌ Erro ao obter variáveis dos parâmetros', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Salvar variáveis nos parâmetros
     */
    private function salvarVariaveisParametros(array $variaveis): void
    {
        try {
            // TODO: Implementar salvamento no banco quando o módulo for criado
            \Log::info('📝 Salvando variáveis dinâmicas (simulado)', [
                'total' => count($variaveis)
            ]);
        } catch (\Exception $e) {
            \Log::error('❌ Erro ao salvar variáveis nos parâmetros', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}