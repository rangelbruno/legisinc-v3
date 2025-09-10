<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidarTransicaoRequest;
use App\Models\WorkflowTransicao;
use App\Services\WorkflowTransitionService;
use App\Validation\TransitionValidator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WorkflowTransitionController extends Controller
{
    protected WorkflowTransitionService $transitionService;
    protected TransitionValidator $validator;

    public function __construct(
        WorkflowTransitionService $transitionService,
        TransitionValidator $validator
    ) {
        $this->transitionService = $transitionService;
        $this->validator = $validator;
    }

    /**
     * Lista transições disponíveis para um documento
     */
    public function disponiveis(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'documento_type' => 'required|string',
                'documento_id' => 'required|integer'
            ]);

            $documento = $this->obterDocumento(
                $request->input('documento_type'),
                $request->input('documento_id')
            );

            if (!$documento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento não encontrado'
                ], 404);
            }

            // Verificar se usuário pode visualizar transições
            $this->authorize('visualizar', [WorkflowTransicao::class, $documento]);

            $transicoes = $this->transitionService->obterTransicoesDisponiveis($documento);

            return response()->json([
                'success' => true,
                'data' => $transicoes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter transições disponíveis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Executa uma transição
     */
    public function executar(ValidarTransicaoRequest $request): JsonResponse
    {
        try {
            $documento = $this->obterDocumento(
                $request->input('documento_type'),
                $request->input('documento_id')
            );

            if (!$documento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento não encontrado'
                ], 404);
            }

            $transicao = WorkflowTransicao::findOrFail($request->input('transicao_id'));

            // Verificar permissões
            $this->authorize('executar', [$transicao, $documento]);

            // Validar se a transição pode ser executada
            $erros = $this->validator->validarExecucao(
                $documento,
                $transicao,
                $request->input('dados_adicionais', [])
            );

            if (!empty($erros)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível executar a transição',
                    'errors' => $erros
                ], 422);
            }

            // Executar a transição
            $sucesso = $this->transitionService->executarTransicao(
                $documento,
                $transicao,
                array_merge(
                    $request->input('dados_adicionais', []),
                    ['observacoes' => $request->input('observacoes')]
                )
            );

            if ($sucesso) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transição executada com sucesso'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Falha ao executar transição'
            ], 500);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para executar esta transição'
            ], 403);

        } catch (\Exception $e) {
            Log::error('Erro ao executar transição via API', [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Valida uma transição sem executá-la
     */
    public function validar(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'documento_type' => 'required|string',
                'documento_id' => 'required|integer',
                'transicao_id' => 'required|integer|exists:workflow_transicoes,id',
                'dados_adicionais' => 'sometimes|array'
            ]);

            $documento = $this->obterDocumento(
                $request->input('documento_type'),
                $request->input('documento_id')
            );

            if (!$documento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento não encontrado'
                ], 404);
            }

            $transicao = WorkflowTransicao::findOrFail($request->input('transicao_id'));

            // Validar se a transição pode ser executada
            $erros = $this->validator->validarExecucao(
                $documento,
                $transicao,
                $request->input('dados_adicionais', [])
            );

            $podeExecutar = empty($erros);

            return response()->json([
                'success' => true,
                'data' => [
                    'pode_executar' => $podeExecutar,
                    'erros' => $erros,
                    'transicao' => [
                        'id' => $transicao->id,
                        'acao' => $transicao->acao,
                        'etapa_origem' => $transicao->etapaOrigem?->nome,
                        'etapa_destino' => $transicao->etapaDestino?->nome,
                        'automatica' => $transicao->automatica
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao validar transição',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Processa transições automáticas para um documento
     */
    public function processarAutomaticas(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'documento_type' => 'required|string',
                'documento_id' => 'required|integer'
            ]);

            $documento = $this->obterDocumento(
                $request->input('documento_type'),
                $request->input('documento_id')
            );

            if (!$documento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento não encontrado'
                ], 404);
            }

            // Verificar se usuário pode executar transições automáticas
            $this->authorize('visualizar', [WorkflowTransicao::class, $documento]);

            $processadas = $this->transitionService->processarTransicoesAutomaticas($documento);

            return response()->json([
                'success' => true,
                'message' => $processadas 
                    ? 'Transições automáticas processadas com sucesso'
                    : 'Nenhuma transição automática disponível',
                'data' => ['processadas' => $processadas]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar transições automáticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtém histórico de transições de um documento
     */
    public function historico(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'documento_type' => 'required|string',
                'documento_id' => 'required|integer',
                'limit' => 'sometimes|integer|min:1|max:100'
            ]);

            $documento = $this->obterDocumento(
                $request->input('documento_type'),
                $request->input('documento_id')
            );

            if (!$documento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento não encontrado'
                ], 404);
            }

            // Verificar se usuário pode visualizar histórico
            $this->authorize('visualizar', [WorkflowTransicao::class, $documento]);

            $historico = \App\Models\DocumentoWorkflowHistorico::where('documento_type', get_class($documento))
                ->where('documento_id', $documento->id)
                ->with(['workflow', 'transicao', 'etapaOrigem', 'etapaDestino', 'executadoPor'])
                ->orderBy('executado_em', 'desc')
                ->limit($request->input('limit', 20))
                ->get();

            return response()->json([
                'success' => true,
                'data' => $historico
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter histórico',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtém status atual do workflow de um documento
     */
    public function status(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'documento_type' => 'required|string',
                'documento_id' => 'required|integer'
            ]);

            $documento = $this->obterDocumento(
                $request->input('documento_type'),
                $request->input('documento_id')
            );

            if (!$documento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento não encontrado'
                ], 404);
            }

            $status = \App\Models\DocumentoWorkflowStatus::where('documento_type', get_class($documento))
                ->where('documento_id', $documento->id)
                ->where('ativo', true)
                ->with(['workflow', 'etapa', 'iniciadoPor'])
                ->first();

            if (!$status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento não possui workflow ativo'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter status do workflow',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtém uma instância do documento
     */
    protected function obterDocumento(string $tipo, int $id): ?Model
    {
        if (!class_exists($tipo)) {
            return null;
        }

        return $tipo::find($id);
    }
}