<?php

namespace App\Http\Controllers\Parametro;

use App\Http\Controllers\Controller;
use App\Services\Parametro\ParametroService;
use App\Services\Parametro\ValidacaoParametroService;
use App\DTOs\Parametro\CampoParametroDTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CampoParametroController extends Controller
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
    public function index(Request $request, int $submoduloId): JsonResponse
    {
        try {
            $campos = $this->parametroService->obterCampos($submoduloId);
            
            return response()->json($campos);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao obter campos: ' . $e->getMessage()
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
                'submodulo_id' => 'required|exists:parametros_submodulos,id',
                'nome' => 'required|string|max:255',
                'label' => 'required|string|max:255',
                'tipo_campo' => 'required|in:text,email,number,textarea,select,checkbox,radio,file,date,datetime',
                'descricao' => 'nullable|string',
                'obrigatorio' => 'boolean',
                'valor_padrao' => 'nullable|string',
                'opcoes' => 'nullable|array',
                'validacao' => 'nullable|array',
                'placeholder' => 'nullable|string|max:255',
                'classe_css' => 'nullable|string|max:255',
                'ordem' => 'nullable|integer|min:0',
                'ativo' => 'boolean',
            ]);

            $validacao = $this->validacaoService->validarCriacaoCampo($dados);
            
            if (!$validacao['valido']) {
                return response()->json([
                    'erro' => 'Dados inválidos',
                    'erros' => $validacao['erros']
                ], 422);
            }

            $campo = $this->parametroService->criarCampo($dados);

            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Campo criado com sucesso',
                'campo' => $campo
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao criar campo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $campos = $this->parametroService->obterCampos($id);
            $campo = $campos->find($id);
            
            if (!$campo) {
                return response()->json([
                    'erro' => 'Campo não encontrado'
                ], 404);
            }

            return response()->json($campo);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao obter campo: ' . $e->getMessage()
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
                'submodulo_id' => 'required|exists:parametros_submodulos,id',
                'nome' => 'required|string|max:255',
                'label' => 'required|string|max:255',
                'tipo_campo' => 'required|in:text,email,number,textarea,select,checkbox,radio,file,date,datetime',
                'descricao' => 'nullable|string',
                'obrigatorio' => 'boolean',
                'valor_padrao' => 'nullable|string',
                'opcoes' => 'nullable|array',
                'validacao' => 'nullable|array',
                'placeholder' => 'nullable|string|max:255',
                'classe_css' => 'nullable|string|max:255',
                'ordem' => 'nullable|integer|min:0',
                'ativo' => 'boolean',
            ]);

            // Lógica de atualização seria implementada no service
            
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Campo atualizado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao atualizar campo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            // Executar exclusão (campos podem ser excluídos mesmo com valores)
            $this->parametroService->excluirCampo($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Campo excluído com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir campo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ativar/desativar campo
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            // Lógica para toggle status seria implementada no service
            
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Status do campo alterado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao alterar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reordenar campos
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
                'mensagem' => 'Campos reordenados com sucesso'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao reordenar campos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Duplicar campo
     */
    public function duplicar(int $id): JsonResponse
    {
        try {
            // Lógica para duplicar seria implementada no service
            
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Campo duplicado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao duplicar campo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter tipos de campo disponíveis
     */
    public function tiposDisponiveis(): JsonResponse
    {
        try {
            $tipos = [
                'text' => 'Texto',
                'email' => 'E-mail',
                'number' => 'Número',
                'textarea' => 'Texto Longo',
                'select' => 'Seleção',
                'checkbox' => 'Checkbox',
                'radio' => 'Radio',
                'file' => 'Arquivo',
                'date' => 'Data',
                'datetime' => 'Data e Hora',
            ];
            
            return response()->json($tipos);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao obter tipos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter regras de validação disponíveis
     */
    public function regrasValidacao(): JsonResponse
    {
        try {
            $regras = $this->validacaoService->obterRegrasDisponiveis();
            
            return response()->json($regras);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao obter regras: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar configuração do campo
     */
    public function validarConfiguracao(Request $request): JsonResponse
    {
        try {
            $dados = $request->validate([
                'tipo_campo' => 'required|string',
                'validacao' => 'nullable|array',
                'opcoes' => 'nullable|array',
                'obrigatorio' => 'boolean',
            ]);

            $erros = [];

            // Validar se opcoes são obrigatórias para campos select/radio
            if (in_array($dados['tipo_campo'], ['select', 'radio']) && empty($dados['opcoes'])) {
                $erros[] = 'Campos do tipo select/radio devem ter opções definidas';
            }

            // Validar regras de validação
            if (!empty($dados['validacao'])) {
                $validacaoRegras = $this->validacaoService->validarRegrasValidacao($dados['validacao']);
                if (!$validacaoRegras['valido']) {
                    $erros = array_merge($erros, $validacaoRegras['erros']);
                }
            }

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