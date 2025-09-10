<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Workflow, DocumentoWorkflowStatus, DocumentoWorkflowHistorico, Proposicao};
use App\Services\Workflow\{WorkflowService, WorkflowManagerService};
use App\Services\Workflow\ConditionEvaluator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{DB, Gate, Log};
use Illuminate\Validation\ValidationException;

class WorkflowApiController extends Controller
{
    protected WorkflowService $workflowService;
    protected WorkflowManagerService $workflowManager;

    public function __construct(
        WorkflowService $workflowService,
        WorkflowManagerService $workflowManager
    ) {
        $this->workflowService = $workflowService;
        $this->workflowManager = $workflowManager;
        $this->middleware('auth:sanctum');
    }

    // ==========================================
    // APIs DE LISTAGEM E CONSULTA
    // ==========================================

    /**
     * Listar workflows disponíveis por tipo de documento
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'tipo_documento' => 'nullable|string|max:100',
            'ativo' => 'nullable|boolean',
            'include_stats' => 'nullable|boolean'
        ]);

        $query = Workflow::with(['etapas' => fn($q) => $q->orderBy('ordem')]);

        if ($request->filled('tipo_documento')) {
            $query->where('tipo_documento', $request->tipo_documento);
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->boolean('ativo'));
        }

        $workflows = $query->orderBy('is_default', 'desc')
                          ->orderBy('ordem')
                          ->orderBy('nome')
                          ->get();

        $data = $workflows->map(function ($workflow) use ($request) {
            $result = [
                'id' => $workflow->id,
                'nome' => $workflow->nome,
                'descricao' => $workflow->descricao,
                'tipo_documento' => $workflow->tipo_documento,
                'ativo' => $workflow->ativo,
                'is_default' => $workflow->is_default,
                'ordem' => $workflow->ordem,
                'etapas_count' => $workflow->etapas->count(),
                'created_at' => $workflow->created_at,
                'updated_at' => $workflow->updated_at
            ];

            if ($request->boolean('include_stats')) {
                $result['stats'] = [
                    'documentos_em_uso' => $workflow->statusWorkflows()->where('status', 'em_andamento')->count(),
                    'documentos_finalizados' => $workflow->statusWorkflows()->where('status', 'finalizado')->count()
                ];
            }

            return $result;
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'total' => $workflows->count(),
                'tipos_documento' => Workflow::select('tipo_documento')->distinct()->pluck('tipo_documento')
            ]
        ]);
    }

    /**
     * Obter workflow padrão para um tipo de documento
     */
    public function getDefault(Request $request): JsonResponse
    {
        $request->validate([
            'tipo_documento' => 'required|string|max:100'
        ]);

        $workflow = $this->workflowManager->obterWorkflowPadrao($request->tipo_documento);

        if (!$workflow) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum workflow padrão encontrado para este tipo de documento',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $workflow->id,
                'nome' => $workflow->nome,
                'descricao' => $workflow->descricao,
                'tipo_documento' => $workflow->tipo_documento,
                'etapas' => $workflow->etapas->map(fn($etapa) => [
                    'id' => $etapa->id,
                    'key' => $etapa->key,
                    'nome' => $etapa->nome,
                    'ordem' => $etapa->ordem,
                    'role_responsavel' => $etapa->role_responsavel,
                    'acoes_possiveis' => $etapa->acoes_possiveis
                ])
            ]
        ]);
    }

    /**
     * Obter detalhes completos de um workflow
     */
    public function show(Workflow $workflow): JsonResponse
    {
        Gate::authorize('view', $workflow);

        $workflow->load([
            'etapas' => fn($q) => $q->orderBy('ordem'),
            'transicoes.etapaOrigem',
            'transicoes.etapaDestino'
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $workflow->id,
                'nome' => $workflow->nome,
                'descricao' => $workflow->descricao,
                'tipo_documento' => $workflow->tipo_documento,
                'ativo' => $workflow->ativo,
                'is_default' => $workflow->is_default,
                'ordem' => $workflow->ordem,
                'configuracao' => $workflow->configuracao,
                'etapas' => $workflow->etapas->map(fn($etapa) => [
                    'id' => $etapa->id,
                    'key' => $etapa->key,
                    'nome' => $etapa->nome,
                    'descricao' => $etapa->descricao,
                    'role_responsavel' => $etapa->role_responsavel,
                    'ordem' => $etapa->ordem,
                    'tempo_limite_dias' => $etapa->tempo_limite_dias,
                    'permite_edicao' => $etapa->permite_edicao,
                    'permite_assinatura' => $etapa->permite_assinatura,
                    'requer_aprovacao' => $etapa->requer_aprovacao,
                    'acoes_possiveis' => $etapa->acoes_possiveis,
                    'condicoes' => $etapa->condicoes
                ]),
                'transicoes' => $workflow->transicoes->map(fn($transicao) => [
                    'id' => $transicao->id,
                    'from_key' => $transicao->etapaOrigem->key,
                    'from_nome' => $transicao->etapaOrigem->nome,
                    'to_key' => $transicao->etapaDestino->key,
                    'to_nome' => $transicao->etapaDestino->nome,
                    'acao' => $transicao->acao,
                    'condicao' => $transicao->condicao,
                    'automatica' => $transicao->automatica
                ]),
                'created_at' => $workflow->created_at,
                'updated_at' => $workflow->updated_at
            ]
        ]);
    }

    // ==========================================
    // APIs DE EXECUÇÃO DE WORKFLOW
    // ==========================================

    /**
     * Iniciar workflow para um documento
     */
    public function startWorkflow(Request $request): JsonResponse
    {
        $request->validate([
            'documento_id' => 'required|integer',
            'documento_type' => 'required|string',
            'workflow_id' => 'required|integer|exists:workflows,id'
        ]);

        try {
            // Validar se o documento existe
            $documentoClass = $request->documento_type;
            $documento = $documentoClass::findOrFail($request->documento_id);

            // Verificar permissões
            if (!Gate::allows('workflow.view_status', $documento)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sem permissão para iniciar workflow neste documento'
                ], 403);
            }

            // Iniciar fluxo
            $this->workflowService->iniciarFluxo($documento, $request->workflow_id);

            // Obter status atual
            $status = $this->workflowService->obterStatus($documento);

            return response()->json([
                'success' => true,
                'message' => 'Workflow iniciado com sucesso',
                'data' => [
                    'status' => $status->status,
                    'etapa_atual' => [
                        'key' => $status->etapaAtual->key,
                        'nome' => $status->etapaAtual->nome,
                        'role_responsavel' => $status->etapaAtual->role_responsavel
                    ],
                    'prazo_atual' => $status->prazo_atual,
                    'iniciado_em' => $status->iniciado_em
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao iniciar workflow via API', [
                'request' => $request->all(),
                'error' => $e->getMessage(),
                'usuario' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao iniciar workflow: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Avançar etapa do workflow
     */
    public function advanceWorkflow(Request $request): JsonResponse
    {
        $request->validate([
            'documento_id' => 'required|integer',
            'documento_type' => 'required|string',
            'acao' => 'required|string|max:100',
            'comentario' => 'nullable|string|max:1000',
            'idempotency_key' => 'nullable|string|max:255'
        ]);

        try {
            // Validar se o documento existe
            $documentoClass = $request->documento_type;
            $documento = $documentoClass::findOrFail($request->documento_id);

            // Avançar etapa
            $this->workflowService->avancarEtapa(
                $documento,
                $request->acao,
                $request->comentario,
                $request->idempotency_key
            );

            // Obter novo status
            $status = $this->workflowService->obterStatus($documento);

            return response()->json([
                'success' => true,
                'message' => 'Workflow avançado com sucesso',
                'data' => [
                    'status' => $status->status,
                    'etapa_atual' => [
                        'key' => $status->etapaAtual->key,
                        'nome' => $status->etapaAtual->nome,
                        'role_responsavel' => $status->etapaAtual->role_responsavel,
                        'acoes_possiveis' => $status->etapaAtual->acoes_possiveis
                    ],
                    'prazo_atual' => $status->prazo_atual,
                    'version' => $status->version
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao avançar workflow via API', [
                'request' => $request->all(),
                'error' => $e->getMessage(),
                'usuario' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao avançar workflow: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter status atual de um documento
     */
    public function getDocumentStatus(Request $request): JsonResponse
    {
        $request->validate([
            'documento_id' => 'required|integer',
            'documento_type' => 'required|string'
        ]);

        try {
            $documentoClass = $request->documento_type;
            $documento = $documentoClass::findOrFail($request->documento_id);

            if (!Gate::allows('workflow.view_status', $documento)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sem permissão para visualizar status deste documento'
                ], 403);
            }

            $status = $this->workflowService->obterStatus($documento);

            if (!$status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento não possui workflow ativo',
                    'data' => null
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $status->status,
                    'workflow' => [
                        'id' => $status->workflow->id,
                        'nome' => $status->workflow->nome
                    ],
                    'etapa_atual' => [
                        'key' => $status->etapaAtual->key,
                        'nome' => $status->etapaAtual->nome,
                        'role_responsavel' => $status->etapaAtual->role_responsavel,
                        'permite_edicao' => $status->etapaAtual->permite_edicao,
                        'permite_assinatura' => $status->etapaAtual->permite_assinatura,
                        'acoes_possiveis' => $status->etapaAtual->acoes_possiveis
                    ],
                    'prazo_atual' => $status->prazo_atual,
                    'atrasado' => $status->prazo_atual && $status->prazo_atual->isPast(),
                    'iniciado_em' => $status->iniciado_em,
                    'finalizado_em' => $status->finalizado_em,
                    'version' => $status->version
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao obter status do documento via API', [
                'request' => $request->all(),
                'error' => $e->getMessage(),
                'usuario' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter histórico de um documento
     */
    public function getDocumentHistory(Request $request): JsonResponse
    {
        $request->validate([
            'documento_id' => 'required|integer',
            'documento_type' => 'required|string',
            'limit' => 'nullable|integer|min:1|max:100'
        ]);

        try {
            $documentoClass = $request->documento_type;
            $documento = $documentoClass::findOrFail($request->documento_id);

            if (!Gate::allows('workflow.view_history', $documento)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sem permissão para visualizar histórico deste documento'
                ], 403);
            }

            $historico = $this->workflowService->obterHistorico($documento);

            if ($request->filled('limit')) {
                $historico = $historico->take($request->limit);
            }

            return response()->json([
                'success' => true,
                'data' => $historico->map(fn($item) => [
                    'id' => $item->id,
                    'acao' => $item->acao,
                    'comentario' => $item->comentario,
                    'etapa_atual' => $item->etapaAtual ? [
                        'key' => $item->etapaAtual->key,
                        'nome' => $item->etapaAtual->nome
                    ] : null,
                    'etapa_anterior' => $item->etapaAnterior ? [
                        'key' => $item->etapaAnterior->key,
                        'nome' => $item->etapaAnterior->nome
                    ] : null,
                    'usuario' => $item->usuario ? [
                        'id' => $item->usuario->id,
                        'nome' => $item->usuario->name,
                        'email' => $item->usuario->email
                    ] : null,
                    'processado_em' => $item->processado_em,
                    'prazo_limite' => $item->prazo_limite,
                    'dados_contexto' => $item->dados_contexto,
                    'duracao_na_etapa' => $item->duracaoNaEtapa()
                ])
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao obter histórico do documento via API', [
                'request' => $request->all(),
                'error' => $e->getMessage(),
                'usuario' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter histórico: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar ações disponíveis para o usuário atual
     */
    public function getAvailableActions(Request $request): JsonResponse
    {
        $request->validate([
            'documento_id' => 'required|integer',
            'documento_type' => 'required|string'
        ]);

        try {
            $documentoClass = $request->documento_type;
            $documento = $documentoClass::findOrFail($request->documento_id);

            $status = $this->workflowService->obterStatus($documento);

            if (!$status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento não possui workflow ativo',
                    'data' => []
                ]);
            }

            $etapaAtual = $status->etapaAtual;
            $acoesDisponiveis = [];

            // Verificar cada ação possível na etapa atual
            foreach ($etapaAtual->acoes_possiveis ?? [] as $acao) {
                if ($this->workflowService->verificarPermissoes(auth()->user(), $documento, $acao)) {
                    // Verificar se existe transição válida para esta ação
                    $proximaEtapa = $this->workflowService->obterProximaEtapa($etapaAtual, $acao, $documento);
                    
                    if ($proximaEtapa) {
                        $acoesDisponiveis[] = [
                            'acao' => $acao,
                            'proxima_etapa' => [
                                'key' => $proximaEtapa->key,
                                'nome' => $proximaEtapa->nome
                            ]
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'etapa_atual' => [
                        'key' => $etapaAtual->key,
                        'nome' => $etapaAtual->nome,
                        'permite_edicao' => $etapaAtual->permite_edicao,
                        'permite_assinatura' => $etapaAtual->permite_assinatura
                    ],
                    'acoes_disponiveis' => $acoesDisponiveis,
                    'pode_pausar' => Gate::allows('workflow.pause', $documento),
                    'pode_retomar' => Gate::allows('workflow.resume', $documento) && $status->status === 'pausado'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao obter ações disponíveis via API', [
                'request' => $request->all(),
                'error' => $e->getMessage(),
                'usuario' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter ações disponíveis: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pausar workflow
     */
    public function pauseWorkflow(Request $request): JsonResponse
    {
        $request->validate([
            'documento_id' => 'required|integer',
            'documento_type' => 'required|string',
            'motivo' => 'nullable|string|max:500'
        ]);

        try {
            $documentoClass = $request->documento_type;
            $documento = $documentoClass::findOrFail($request->documento_id);

            if (!Gate::allows('workflow.pause', $documento)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sem permissão para pausar este workflow'
                ], 403);
            }

            $this->workflowService->pausarWorkflow($documento, $request->motivo);

            return response()->json([
                'success' => true,
                'message' => 'Workflow pausado com sucesso'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao pausar workflow via API', [
                'request' => $request->all(),
                'error' => $e->getMessage(),
                'usuario' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao pausar workflow: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retomar workflow pausado
     */
    public function resumeWorkflow(Request $request): JsonResponse
    {
        $request->validate([
            'documento_id' => 'required|integer',
            'documento_type' => 'required|string'
        ]);

        try {
            $documentoClass = $request->documento_type;
            $documento = $documentoClass::findOrFail($request->documento_id);

            if (!Gate::allows('workflow.resume', $documento)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sem permissão para retomar este workflow'
                ], 403);
            }

            $this->workflowService->retomarWorkflow($documento);

            return response()->json([
                'success' => true,
                'message' => 'Workflow retomado com sucesso'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao retomar workflow via API', [
                'request' => $request->all(),
                'error' => $e->getMessage(),
                'usuario' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao retomar workflow: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==========================================
    // APIs DE UTILIDADES
    // ==========================================

    /**
     * Validar condições JSON
     */
    public function validateConditions(Request $request): JsonResponse
    {
        $request->validate([
            'conditions' => 'required|array'
        ]);

        $errors = ConditionEvaluator::validate($request->conditions);

        return response()->json([
            'success' => empty($errors),
            'valid' => empty($errors),
            'errors' => $errors,
            'examples' => empty($errors) ? null : ConditionEvaluator::examples()
        ]);
    }

    /**
     * Obter exemplos de condições
     */
    public function getConditionExamples(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => ConditionEvaluator::examples()
        ]);
    }

    /**
     * Dashboard de workflows
     */
    public function dashboard(): JsonResponse
    {
        if (!Gate::allows('workflow.analytics')) {
            return response()->json([
                'success' => false,
                'message' => 'Sem permissão para acessar analytics'
            ], 403);
        }

        $stats = [
            'workflows' => [
                'total' => Workflow::count(),
                'ativos' => Workflow::where('ativo', true)->count(),
                'inativos' => Workflow::where('ativo', false)->count(),
                'padroes' => Workflow::where('is_default', true)->count()
            ],
            'documentos' => [
                'em_workflow' => DocumentoWorkflowStatus::where('status', 'em_andamento')->count(),
                'finalizados' => DocumentoWorkflowStatus::where('status', 'finalizado')->count(),
                'pausados' => DocumentoWorkflowStatus::where('status', 'pausado')->count()
            ],
            'tipos_documento' => Workflow::select('tipo_documento')
                ->selectRaw('COUNT(*) as total_workflows')
                ->selectRaw('SUM(CASE WHEN ativo THEN 1 ELSE 0 END) as workflows_ativos')
                ->groupBy('tipo_documento')
                ->get()
                ->keyBy('tipo_documento'),
            'atividade_recente' => DocumentoWorkflowHistorico::with(['etapaAtual', 'usuario'])
                ->latest('processado_em')
                ->limit(10)
                ->get()
                ->map(fn($item) => [
                    'acao' => $item->acao,
                    'etapa' => $item->etapaAtual?->nome,
                    'usuario' => $item->usuario?->name,
                    'processado_em' => $item->processado_em
                ])
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}