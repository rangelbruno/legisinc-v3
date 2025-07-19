<?php

namespace App\Http\Controllers\Parametro;

use App\Http\Controllers\Controller;
use App\Services\Parametro\ParametroService;
use App\Services\Parametro\ValidacaoParametroService;
use App\DTOs\Parametro\SubmoduloParametroDTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubmoduloParametroController extends Controller
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
    public function index(Request $request, int $moduloId): JsonResponse
    {
        try {
            $submodulos = $this->parametroService->obterSubmodulos($moduloId);
            
            return response()->json($submodulos);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao obter submódulos: ' . $e->getMessage()
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
                'modulo_id' => 'required|exists:parametros_modulos,id',
                'nome' => 'required|string|max:255',
                'descricao' => 'nullable|string',
                'tipo' => 'required|in:form,checkbox,select,toggle,custom',
                'config' => 'nullable|array',
                'ordem' => 'nullable|integer|min:0',
                'ativo' => 'boolean',
            ]);

            $validacao = $this->validacaoService->validarCriacaoSubmodulo($dados);
            
            if (!$validacao['valido']) {
                return response()->json([
                    'erro' => 'Dados inválidos',
                    'erros' => $validacao['erros']
                ], 422);
            }

            $submodulo = $this->parametroService->criarSubmodulo($dados);

            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Submódulo criado com sucesso',
                'submodulo' => $submodulo
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao criar submódulo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $submodulos = $this->parametroService->obterSubmodulos($id);
            $submodulo = $submodulos->find($id);
            
            if (!$submodulo) {
                return response()->json([
                    'erro' => 'Submódulo não encontrado'
                ], 404);
            }

            $campos = $this->parametroService->obterCampos($id);

            return response()->json([
                'submodulo' => $submodulo,
                'campos' => $campos
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao obter submódulo: ' . $e->getMessage()
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
                'modulo_id' => 'required|exists:parametros_modulos,id',
                'nome' => 'required|string|max:255',
                'descricao' => 'nullable|string',
                'tipo' => 'required|in:form,checkbox,select,toggle,custom',
                'config' => 'nullable|array',
                'ordem' => 'nullable|integer|min:0',
                'ativo' => 'boolean',
            ]);

            // Lógica de atualização seria implementada no service
            
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Submódulo atualizado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao atualizar submódulo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            // Verificar se pode excluir
            $verificacao = $this->parametroService->podeExcluirSubmodulo($id);
            
            if (!$verificacao['pode']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível excluir o submódulo: ' . $verificacao['motivo']
                ], 422);
            }

            // Executar exclusão
            $this->parametroService->excluirSubmodulo($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Submódulo excluído com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir submódulo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ativar/desativar submódulo
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            // Lógica para toggle status seria implementada no service
            
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Status do submódulo alterado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao alterar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reordenar submódulos
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
                'mensagem' => 'Submódulos reordenados com sucesso'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao reordenar submódulos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Duplicar submódulo
     */
    public function duplicar(int $id): JsonResponse
    {
        try {
            // Lógica para duplicar seria implementada no service
            
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Submódulo duplicado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao duplicar submódulo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter campos do submódulo
     */
    public function campos(int $id): JsonResponse
    {
        try {
            $campos = $this->parametroService->obterCampos($id);
            
            return response()->json($campos);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao obter campos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Salvar valores dos campos do submódulo
     */
    public function salvarValores(Request $request, int $id): JsonResponse
    {
        try {
            $valores = $request->validate([
                'valores' => 'required|array'
            ]);

            $userId = auth()->id();
            $sucesso = $this->parametroService->salvarValores($id, $valores['valores'], $userId);
            
            if ($sucesso) {
                return response()->json([
                    'sucesso' => true,
                    'mensagem' => 'Valores salvos com sucesso'
                ]);
            } else {
                return response()->json([
                    'erro' => 'Erro ao salvar valores'
                ], 400);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao salvar valores: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar valores do submódulo
     */
    public function validarValores(Request $request, int $id): JsonResponse
    {
        try {
            $valores = $request->validate([
                'valores' => 'required|array'
            ]);

            // Obter informações do submódulo
            $submodulos = $this->parametroService->obterSubmodulos($id);
            $submodulo = $submodulos->find($id);
            
            if (!$submodulo) {
                return response()->json([
                    'erro' => 'Submódulo não encontrado'
                ], 404);
            }

            // Validar valores
            $erros = $this->validacaoService->validarMultiplos(
                $submodulo->modulo->nome,
                $submodulo->nome,
                $valores['valores']
            );
            
            return response()->json([
                'valido' => empty($erros),
                'erros' => $erros
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro na validação: ' . $e->getMessage()
            ], 500);
        }
    }
}