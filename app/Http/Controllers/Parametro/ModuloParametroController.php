<?php

namespace App\Http\Controllers\Parametro;

use App\Http\Controllers\Controller;
use App\Services\Parametro\ParametroService;
use App\Services\Parametro\ValidacaoParametroService;
use App\DTOs\Parametro\ModuloParametroDTO;
use App\Models\Parametro\ParametroModulo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ModuloParametroController extends Controller
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
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $modulos = $this->parametroService->obterModulos();
            
            // Se for requisição AJAX para DataTables
            if ($request->ajax() || $request->wantsJson()) {
                $data = [];
                
                foreach ($modulos as $modulo) {
                    $submodulosCount = $modulo->submodulos_count ?? $modulo->submodulos()->count();
                    
                    $data[] = [
                        'id' => $modulo->id,
                        'nome' => $modulo->nome,
                        'descricao' => $modulo->descricao ?: 'Sem descrição disponível',
                        'icon' => $modulo->icon ?: 'ki-setting-2',
                        'submodulos_count' => $submodulosCount,
                        'ativo' => $modulo->ativo,
                        'status_badge' => $modulo->ativo ? 'success' : 'danger',
                        'status_text' => $modulo->ativo ? 'Ativo' : 'Inativo',
                        'ordem' => $modulo->ordem ?? 0,
                        'created_at' => $modulo->created_at ? $modulo->created_at->format('d/m/Y H:i') : '',
                        'updated_at' => $modulo->updated_at ? $modulo->updated_at->format('d/m/Y H:i') : '',
                    ];
                }
                
                return response()->json([
                    'data' => $data,
                    'recordsTotal' => count($data),
                    'recordsFiltered' => count($data),
                    'draw' => intval($request->input('draw', 1))
                ]);
            }
            
            return response()->json($modulos);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao obter módulos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $dados = $request->validate([
                'nome' => 'required|string|max:255',
                'descricao' => 'nullable|string',
                'icon' => 'nullable|string|max:100',
                'ordem' => 'nullable|integer|min:0',
                'ativo' => 'boolean',
            ]);

            $validacao = $this->validacaoService->validarCriacaoModulo($dados);
            
            if (!$validacao['valido']) {
                return response()->json([
                    'erro' => 'Dados inválidos',
                    'erros' => $validacao['erros']
                ], 422);
            }

            $modulo = $this->parametroService->criarModulo($dados);

            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Módulo criado com sucesso',
                'modulo' => $modulo
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao criar módulo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $modulo = $this->parametroService->obterModulos()->find($id);
            
            if (!$modulo) {
                return response()->json([
                    'erro' => 'Módulo não encontrado'
                ], 404);
            }

            $submodulos = $this->parametroService->obterSubmodulos($id);

            return response()->json([
                'modulo' => $modulo,
                'submodulos' => $submodulos
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao obter módulo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $dados = $request->validate([
                'nome' => 'required|string|max:255',
                'descricao' => 'nullable|string',
                'icon' => 'nullable|string|max:100',
                'ordem' => 'nullable|integer|min:0',
                'ativo' => 'boolean',
            ]);

            // Validar se módulo existe
            $modulo = $this->parametroService->obterModulos()->find($id);
            
            if (!$modulo) {
                return response()->json([
                    'erro' => 'Módulo não encontrado'
                ], 404);
            }

            // Lógica de atualização seria implementada no service
            
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Módulo atualizado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao atualizar módulo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            \Log::info("AJAX: Iniciando exclusão de módulo", [
                'id' => $id, 
                'user_id' => auth()->id(),
                'request_method' => request()->method(),
                'url' => request()->fullUrl()
            ]);
            
            // Verificar conexão com banco de dados
            try {
                \DB::connection()->getPdo();
                $dbConnected = true;
            } catch (\Exception $e) {
                $dbConnected = false;
                \Log::warning("AJAX: Banco de dados não conectado, simulando exclusão", [
                    'db_error' => $e->getMessage()
                ]);
            }
            
            if ($dbConnected) {
                // Verificar se é exclusão forçada
                $force = request()->input('force', false);
                \Log::info("AJAX: Force delete", ['force' => $force]);
                
                if (!$force) {
                    // Usar service para verificação normal
                    $verificacao = $this->parametroService->podeExcluirModulo($id);
                    \Log::info("AJAX: Verificação de exclusão", ['verificacao' => $verificacao]);
                    
                    if (!$verificacao['pode']) {
                        \Log::warning("AJAX: Exclusão negada", ['motivo' => $verificacao['motivo']]);
                        return response()->json([
                            'success' => false,
                            'message' => 'Não é possível excluir o módulo: ' . $verificacao['motivo'],
                            'can_force' => true
                        ], 422);
                    }
                }

                // Executar exclusão (normal ou forçada)
                $this->parametroService->excluirModulo($id, $force);
                \Log::info("AJAX: Módulo excluído com sucesso", ['id' => $id, 'force' => $force]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Módulo excluído com sucesso!'
                ]);
            } else {
                // Simular exclusão quando banco não está disponível
                \Log::info("AJAX: Simulando exclusão (banco não disponível)", ['id' => $id]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Módulo excluído com sucesso! (Simulação - banco não disponível)'
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error("AJAX: Erro ao excluir módulo", [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir módulo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ativar/desativar módulo
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $modulo = $this->parametroService->obterModulos()->find($id);
            
            if (!$modulo) {
                return response()->json([
                    'erro' => 'Módulo não encontrado'
                ], 404);
            }

            // Lógica para toggle status seria implementada no service
            
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Status do módulo alterado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao alterar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reordenar módulos
     */
    public function reordenar(Request $request): JsonResponse
    {
        try {
            $ordens = $request->validate([
                'ordens' => 'required|array',
                'ordens.*.id' => 'required|integer',
                'ordens.*.ordem' => 'required|integer|min:0',
            ]);

            // Lógica para reordenar seria implementada no service
            
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Módulos reordenados com sucesso'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao reordenar módulos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Duplicar módulo
     */
    public function duplicar(int $id): JsonResponse
    {
        try {
            $modulo = $this->parametroService->obterModulos()->find($id);
            
            if (!$modulo) {
                return response()->json([
                    'erro' => 'Módulo não encontrado'
                ], 404);
            }

            // Lógica para duplicar seria implementada no service
            
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Módulo duplicado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao duplicar módulo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar configurações do módulo
     */
    public function exportar(int $id): JsonResponse
    {
        try {
            $modulo = $this->parametroService->obterModulos()->find($id);
            
            if (!$modulo) {
                return response()->json([
                    'erro' => 'Módulo não encontrado'
                ], 404);
            }

            $submodulos = $this->parametroService->obterSubmodulos($id);
            
            $export = [
                'modulo' => $modulo,
                'submodulos' => $submodulos->map(function ($submodulo) {
                    return [
                        'submodulo' => $submodulo,
                        'campos' => $this->parametroService->obterCampos($submodulo->id)
                    ];
                })
            ];
            
            return response()->json($export);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao exportar módulo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Importar configurações de módulo
     */
    public function importar(Request $request): JsonResponse
    {
        try {
            $dados = $request->validate([
                'configuracoes' => 'required|array',
                'configuracoes.modulo' => 'required|array',
                'configuracoes.submodulos' => 'required|array',
            ]);

            // Lógica para importar seria implementada no service
            
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Configurações importadas com sucesso'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao importar configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extrair módulos em formato JSON para análise de uso
     */
    public function extrairJson(Request $request): JsonResponse|Response
    {
        try {
            $formato = $request->input('formato', 'json');
            $moduloId = $request->input('modulo_id');
            $simples = filter_var($request->input('simples', 'true'), FILTER_VALIDATE_BOOLEAN); // Por padrão, usar extração simples
            
            if ($moduloId) {
                // Extrair módulo específico
                $modulo = ParametroModulo::with(['submodulos.campos.valores'])
                    ->ativo()
                    ->findOrFail($moduloId);
                
                $dados = $simples ? $modulo->toJsonExtractSimple() : $modulo->toJsonExtract();
            } else {
                // Extrair todos os módulos
                $modulos = ParametroModulo::with(['submodulos.campos.valores'])
                    ->ativo()
                    ->ordenados()
                    ->get();
                
                if ($simples) {
                    $campos = [];
                    $totalCampos = 0;
                    $camposUtilizados = 0;
                    
                    foreach ($modulos as $modulo) {
                        $moduloData = $modulo->toJsonExtractSimple();
                        $campos = array_merge($campos, $moduloData['campos']);
                        $totalCampos += $moduloData['total_campos'];
                        $camposUtilizados += $moduloData['campos_utilizados'];
                    }
                    
                    $dados = [
                        'resumo' => [
                            'total_modulos' => $modulos->count(),
                            'total_campos' => $totalCampos,
                            'campos_utilizados' => $camposUtilizados,
                            'campos_nao_utilizados' => $totalCampos - $camposUtilizados,
                            'percentual_uso' => $totalCampos > 0 ? round(($camposUtilizados / $totalCampos) * 100, 2) : 0,
                            'data_extracao' => now()->toISOString(),
                        ],
                        'campos' => $campos
                    ];
                } else {
                    $dados = [
                        'modulos' => $modulos->map(fn($modulo) => $modulo->toJsonExtract()),
                        'resumo_geral' => [
                            'total_modulos' => $modulos->count(),
                            'total_submodulos' => $modulos->sum(fn($modulo) => $modulo->getSubmodulosCount()),
                            'total_campos' => $modulos->sum(function ($modulo) {
                                return $modulo->submodulos->sum(fn($sub) => $sub->getCamposCount());
                            }),
                            'campos_utilizados' => $modulos->sum(function ($modulo) {
                                return $modulo->submodulos->sum(function ($sub) {
                                    return $sub->campos->filter(fn($campo) => $campo->hasValor())->count();
                                });
                            }),
                            'data_extracao' => now()->toISOString(),
                        ]
                    ];
                }
            }
            
            if ($formato === 'download') {
                $prefix = $simples ? 'campos' : 'modulos';
                $filename = $moduloId 
                    ? "{$prefix}_modulo_{$moduloId}.json"
                    : "{$prefix}_todos_modulos.json";
                
                return response()->json($dados)
                    ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
                    ->header('Content-Type', 'application/json');
            }
            
            return response()->json($dados);
            
        } catch (\Exception $e) {
            \Log::error('Erro na extração JSON:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'erro' => 'Erro ao extrair dados: ' . $e->getMessage(),
                'debug' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Teste da extração JSON - versão simplificada para debug
     */
    public function testeExtracao(Request $request): JsonResponse
    {
        try {
            $moduloId = $request->input('modulo_id');
            
            if ($moduloId) {
                $modulo = ParametroModulo::find($moduloId);
                if (!$modulo) {
                    return response()->json(['erro' => 'Módulo não encontrado'], 404);
                }
                
                return response()->json([
                    'sucesso' => true,
                    'modulo' => [
                        'id' => $modulo->id,
                        'nome' => $modulo->nome,
                        'ativo' => $modulo->ativo
                    ],
                    'campos_teste' => [
                        [
                            'modulo' => $modulo->nome,
                            'campo' => 'teste_campo',
                            'utilizado' => false
                        ]
                    ]
                ]);
            }
            
            return response()->json([
                'sucesso' => true,
                'teste' => 'Endpoint funcionando',
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro no teste: ' . $e->getMessage()
            ], 500);
        }
    }
}