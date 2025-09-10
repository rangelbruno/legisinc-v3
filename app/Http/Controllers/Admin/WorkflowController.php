<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Workflow, WorkflowEtapa, WorkflowTransicao};
use App\Services\Workflow\{WorkflowManagerService, WorkflowService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Gate, Log};
use Illuminate\Validation\Rule;

class WorkflowController extends Controller
{
    protected WorkflowManagerService $workflowManager;
    protected WorkflowService $workflowService;

    public function __construct(
        WorkflowManagerService $workflowManager,
        WorkflowService $workflowService
    ) {
        $this->workflowManager = $workflowManager;
        $this->workflowService = $workflowService;
    }

    /**
     * Listar todos os workflows
     */
    public function index(Request $request)
    {
        // Verificação de permissão
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores podem gerenciar workflows.');
        }

        $query = Workflow::with(['etapas', 'transicoes']);

        // Filtros
        if ($request->filled('tipo_documento')) {
            $query->where('tipo_documento', $request->tipo_documento);
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->boolean('ativo'));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'ILIKE', "%{$search}%")
                  ->orWhere('descricao', 'ILIKE', "%{$search}%");
            });
        }

        $workflows = $query->orderBy('is_default', 'desc')
                          ->orderBy('tipo_documento')
                          ->orderBy('ordem')
                          ->orderBy('nome')
                          ->paginate(20);

        // Estatísticas rápidas
        $stats = [
            'total' => Workflow::count(),
            'ativos' => Workflow::where('ativo', true)->count(),
            'padroes' => Workflow::where('is_default', true)->count(),
            'tipos_documento' => Workflow::select('tipo_documento')
                                       ->distinct()
                                       ->pluck('tipo_documento')
                                       ->toArray()
        ];

        return view('admin.workflows.index', compact('workflows', 'stats'));
    }

    /**
     * Exibir formulário de criação
     */
    public function create()
    {
        // Verificação de permissão
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores podem criar workflows.');
        }

        $tiposDocumento = [
            'proposicao' => 'Proposição',
            'requerimento' => 'Requerimento', 
            'oficio' => 'Ofício',
            'parecer' => 'Parecer',
            'ata' => 'Ata',
            'resolucao' => 'Resolução'
        ];

        $rolesDisponiveis = [
            'Parlamentar' => 'Parlamentar',
            'Legislativo' => 'Legislativo',
            'Protocolo' => 'Protocolo',
            'Expediente' => 'Expediente',
            'Admin' => 'Administrador'
        ];

        return view('admin.workflows.create', compact('tiposDocumento', 'rolesDisponiveis'));
    }

    /**
     * Salvar novo workflow
     */
    public function store(Request $request)
    {
        // Verificação de permissão
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores podem criar workflows.');
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'tipo_documento' => 'required|string|max:100',
            'ativo' => 'boolean',
            'is_default' => 'boolean',
            'ordem' => 'nullable|integer|min:0',
            'configuracao' => 'nullable|array',
            
            // Dados do Designer (JSON)
            'etapas' => 'required|array|min:1',
            'etapas.*.key' => 'required|string|max:50',
            'etapas.*.nome' => 'required|string|max:255',
            'etapas.*.descricao' => 'nullable|string',
            'etapas.*.role_responsavel' => 'nullable|string|max:50',
            'etapas.*.ordem' => 'required|integer|min:1',
            'etapas.*.tempo_limite_dias' => 'nullable|integer|min:1',
            'etapas.*.permite_edicao' => 'boolean',
            'etapas.*.permite_assinatura' => 'boolean', 
            'etapas.*.requer_aprovacao' => 'boolean',
            'etapas.*.acoes_possiveis' => 'nullable|array',
            'etapas.*.condicoes' => 'nullable|array',

            'transicoes' => 'nullable|array',
            'transicoes.*.from' => 'required_with:transicoes|string',
            'transicoes.*.to' => 'required_with:transicoes|string',
            'transicoes.*.acao' => 'required_with:transicoes|string|max:100',
            'transicoes.*.condicao' => 'nullable|array',
            'transicoes.*.automatica' => 'boolean'
        ]);

        try {
            $workflow = $this->workflowManager->criarWorkflow($validated);

            // Validar workflow criado
            $erros = $this->workflowManager->validarWorkflow($workflow);
            if (!empty($erros)) {
                return back()->withErrors(['workflow' => 'Problemas no workflow: ' . implode(', ', $erros)])
                            ->withInput();
            }

            Log::info('Workflow criado via interface admin', [
                'workflow' => $workflow->nome,
                'usuario' => auth()->user()->email,
                'etapas' => count($validated['etapas']),
                'transicoes' => count($validated['transicoes'] ?? [])
            ]);

            return redirect()
                ->route('admin.workflows.show', $workflow)
                ->with('success', "Workflow '{$workflow->nome}' criado com sucesso!");

        } catch (\Exception $e) {
            Log::error('Erro ao criar workflow', [
                'error' => $e->getMessage(),
                'usuario' => auth()->user()->email,
                'dados' => $validated
            ]);

            return back()->withErrors(['error' => 'Erro ao criar workflow: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Exibir workflow específico
     */
    public function show(Workflow $workflow)
    {
        // Verificação de permissão
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores podem visualizar workflows.');
        }

        $workflow->load([
            'etapas' => fn($q) => $q->orderBy('ordem'),
            'transicoes.etapaOrigem',
            'transicoes.etapaDestino'
        ]);

        // Estatísticas de uso
        $stats = [
            'documentos_em_uso' => $workflow->documentosStatus()->whereNotIn('status', ['finalizado', 'cancelado'])->count(),
            'documentos_finalizados' => $workflow->documentosStatus()->where('status', 'finalizado')->count(),
            'total_historico' => $workflow->historico()->count(),
            'tempo_medio_conclusao' => 0, // TODO: implementar cálculo
            'etapas_mais_demoradas' => [], // TODO: implementar análise
        ];

        // Validação básica do workflow
        $errosValidacao = [];
        if ($workflow->etapas->count() === 0) {
            $errosValidacao[] = 'Workflow não possui etapas configuradas';
        }
        if ($workflow->transicoes->count() === 0 && $workflow->etapas->count() > 1) {
            $errosValidacao[] = 'Workflow possui múltiplas etapas mas nenhuma transição configurada';
        }

        return view('admin.workflows.show', compact('workflow', 'stats', 'errosValidacao'));
    }

    /**
     * Exibir formulário de edição
     */
    public function edit(Workflow $workflow)
    {
        // Verificação de permissão
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores podem editar workflows.');
        }

        $workflow->load(['etapas' => fn($q) => $q->orderBy('ordem'), 'transicoes']);

        $tiposDocumento = [
            'proposicao' => 'Proposição',
            'requerimento' => 'Requerimento',
            'oficio' => 'Ofício', 
            'parecer' => 'Parecer',
            'ata' => 'Ata',
            'resolucao' => 'Resolução'
        ];

        $rolesDisponiveis = [
            'Parlamentar' => 'Parlamentar',
            'Legislativo' => 'Legislativo', 
            'Protocolo' => 'Protocolo',
            'Expediente' => 'Expediente',
            'Admin' => 'Administrador'
        ];

        return view('admin.workflows.edit', compact('workflow', 'tiposDocumento', 'rolesDisponiveis'));
    }

    /**
     * Atualizar workflow
     */
    public function update(Request $request, Workflow $workflow)
    {
        // Verificação de permissão
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores podem atualizar workflows.');
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'tipo_documento' => 'required|string|max:100',
            'configuracao' => 'nullable|array',
            
            // Se fornecidas etapas/transições, revalidar estrutura completa
            'etapas' => 'nullable|array',
            'etapas.*.key' => 'required_with:etapas|string|max:50',
            'etapas.*.nome' => 'required_with:etapas|string|max:255',
            'etapas.*.descricao' => 'nullable|string',
            'etapas.*.role_responsavel' => 'nullable|string|max:50',
            'etapas.*.ordem' => 'required_with:etapas|integer|min:1',
            'etapas.*.tempo_limite_dias' => 'nullable|integer|min:1',
            'etapas.*.permite_edicao' => 'boolean',
            'etapas.*.permite_assinatura' => 'boolean',
            'etapas.*.requer_aprovacao' => 'boolean',
            'etapas.*.acoes_possiveis' => 'nullable|array',
            'etapas.*.condicoes' => 'nullable|array',

            'transicoes' => 'nullable|array',
            'transicoes.*.from' => 'required_with:transicoes|string',
            'transicoes.*.to' => 'required_with:transicoes|string',
            'transicoes.*.acao' => 'required_with:transicoes|string|max:100',
            'transicoes.*.condicao' => 'nullable|array',
            'transicoes.*.automatica' => 'boolean'
        ]);

        try {
            $workflowAtualizado = $this->workflowManager->atualizarWorkflow($workflow->id, $validated);

            // Validar workflow atualizado
            $erros = $this->workflowManager->validarWorkflow($workflowAtualizado);
            if (!empty($erros)) {
                return back()->withErrors(['workflow' => 'Problemas no workflow: ' . implode(', ', $erros)])
                            ->withInput();
            }

            Log::info('Workflow atualizado via interface admin', [
                'workflow' => $workflowAtualizado->nome,
                'usuario' => auth()->user()->email
            ]);

            return redirect()
                ->route('admin.workflows.show', $workflowAtualizado)
                ->with('success', "Workflow '{$workflowAtualizado->nome}' atualizado com sucesso!");

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar workflow', [
                'workflow_id' => $workflow->id,
                'error' => $e->getMessage(),
                'usuario' => auth()->user()->email
            ]);

            return back()->withErrors(['error' => 'Erro ao atualizar workflow: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Remover workflow
     */
    public function destroy(Workflow $workflow)
    {
        // Verificação de permissão
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores podem excluir workflows.');
        }

        try {
            $nome = $workflow->nome;
            $this->workflowManager->removerWorkflow($workflow->id);

            Log::info('Workflow removido via interface admin', [
                'workflow' => $nome,
                'usuario' => auth()->user()->email
            ]);

            return redirect()
                ->route('admin.workflows.index')
                ->with('success', "Workflow '{$nome}' removido com sucesso!");

        } catch (\Exception $e) {
            Log::error('Erro ao remover workflow', [
                'workflow_id' => $workflow->id,
                'error' => $e->getMessage(),
                'usuario' => auth()->user()->email
            ]);

            return back()->withErrors(['error' => 'Erro ao remover workflow: ' . $e->getMessage()]);
        }
    }

    /**
     * Duplicar workflow
     */
    public function duplicate(Request $request, Workflow $workflow)
    {
        // Verificação de permissão
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores podem duplicar workflows.');
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:workflows,nome'
        ]);

        try {
            $workflowDuplicado = $this->workflowManager->duplicarWorkflow($workflow->id, $validated['nome']);

            Log::info('Workflow duplicado via interface admin', [
                'original' => $workflow->nome,
                'duplicado' => $workflowDuplicado->nome,
                'usuario' => auth()->user()->email
            ]);

            return redirect()
                ->route('admin.workflows.show', $workflowDuplicado)
                ->with('success', "Workflow '{$workflowDuplicado->nome}' duplicado com sucesso!");

        } catch (\Exception $e) {
            Log::error('Erro ao duplicar workflow', [
                'workflow_id' => $workflow->id,
                'error' => $e->getMessage(),
                'usuario' => auth()->user()->email
            ]);

            return back()->withErrors(['error' => 'Erro ao duplicar workflow: ' . $e->getMessage()]);
        }
    }

    /**
     * Ativar/Desativar workflow
     */
    public function toggle(Workflow $workflow)
    {
        // Verificação de permissão
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores podem ativar/desativar workflows.');
        }

        try {
            $novoStatus = !$workflow->ativo;
            $this->workflowManager->ativarDesativarWorkflow($workflow->id, $novoStatus);

            $acao = $novoStatus ? 'ativado' : 'desativado';

            Log::info("Workflow {$acao} via interface admin", [
                'workflow' => $workflow->nome,
                'usuario' => auth()->user()->email
            ]);

            return back()->with('success', "Workflow '{$workflow->nome}' {$acao} com sucesso!");

        } catch (\Exception $e) {
            Log::error('Erro ao alterar status do workflow', [
                'workflow_id' => $workflow->id,
                'error' => $e->getMessage(),
                'usuario' => auth()->user()->email
            ]);

            return back()->withErrors(['error' => 'Erro ao alterar status: ' . $e->getMessage()]);
        }
    }

    /**
     * Definir como padrão
     */
    public function setDefault(Workflow $workflow)
    {
        // Verificação de permissão
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores podem definir workflows como padrão.');
        }

        try {
            $this->workflowManager->definirWorkflowPadrao($workflow->id, $workflow->tipo_documento);

            Log::info('Workflow definido como padrão via interface admin', [
                'workflow' => $workflow->nome,
                'tipo_documento' => $workflow->tipo_documento,
                'usuario' => auth()->user()->email
            ]);

            return back()->with('success', "Workflow '{$workflow->nome}' definido como padrão para {$workflow->tipo_documento}!");

        } catch (\Exception $e) {
            Log::error('Erro ao definir workflow como padrão', [
                'workflow_id' => $workflow->id,
                'error' => $e->getMessage(),
                'usuario' => auth()->user()->email
            ]);

            return back()->withErrors(['error' => 'Erro ao definir como padrão: ' . $e->getMessage()]);
        }
    }

    /**
     * Designer visual de workflows
     */
    public function designer(Workflow $workflow = null)
    {
        // Verificação de permissão
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores podem acessar o designer de workflows.');
        }

        $tiposDocumento = [
            'proposicao' => 'Proposição',
            'requerimento' => 'Requerimento',
            'oficio' => 'Ofício',
            'parecer' => 'Parecer',
            'ata' => 'Ata',
            'resolucao' => 'Resolução'
        ];

        $rolesDisponiveis = [
            'Parlamentar' => 'Parlamentar',
            'Legislativo' => 'Legislativo',
            'Protocolo' => 'Protocolo',
            'Expediente' => 'Expediente',
            'Admin' => 'Administrador'
        ];

        $acoesDisponiveis = [
            'aprovar', 'devolver', 'solicitar_alteracoes', 'assinar', 
            'protocolar', 'finalizar', 'arquivar', 'enviar_legislativo',
            'enviar_protocolo', 'devolver_edicao', 'salvar_rascunho'
        ];

        return view('admin.workflows.designer', compact(
            'workflow', 'tiposDocumento', 'rolesDisponiveis', 'acoesDisponiveis'
        ));
    }

    /**
     * Obter dados do workflow para o Designer (JSON)
     */
    public function designerData(Workflow $workflow)
    {
        try {
            // Verificação de permissão
            if (!auth()->user()->isAdmin()) {
                return response()->json(['error' => 'Acesso negado'], 403);
            }

            $workflow->load(['etapas' => fn($q) => $q->orderBy('ordem'), 'transicoes.etapaOrigem', 'transicoes.etapaDestino']);

            // Calcular posições automáticas baseadas na ordem das etapas
            $etapasData = $workflow->etapas->map(function($etapa, $index) use ($workflow) {
                // Calcular posição em layout horizontal com mais espaço
                $x = 50 + ($index * 300); // Espaçamento horizontal de 300px
                $y = 150 + (($index % 2) * 200); // Alternando altura com mais espaço
                
                return [
                    'id' => $etapa->id,
                    'key' => $etapa->key,
                    'nome' => $etapa->nome,
                    'descricao' => $etapa->descricao,
                    'tipo' => self::mapearTipoEtapa($etapa->role_responsavel, $index, $workflow->etapas->count()),
                    'role_responsavel' => $etapa->role_responsavel,
                    'ordem' => $etapa->ordem,
                    'tempo_limite_dias' => $etapa->tempo_limite_dias,
                    'permite_edicao' => $etapa->permite_edicao,
                    'permite_assinatura' => $etapa->permite_assinatura,
                    'requer_aprovacao' => $etapa->requer_aprovacao,
                    'acoes_possiveis' => $etapa->acoes_possiveis,
                    'condicoes' => $etapa->condicoes,
                    'posicao' => ['x' => $x, 'y' => $y]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'workflow' => [
                        'id' => $workflow->id,
                        'nome' => $workflow->nome,
                        'descricao' => $workflow->descricao,
                        'tipo_documento' => $workflow->tipo_documento,
                        'ativo' => $workflow->ativo,
                        'is_default' => $workflow->is_default,
                        'configuracao' => $workflow->configuracao
                    ],
                    'etapas' => $etapasData,
                    'transicoes' => $workflow->transicoes->map(fn($transicao) => [
                        'id' => $transicao->id,
                        'from' => $transicao->etapaOrigem->key,
                        'to' => $transicao->etapaDestino->key,
                        'acao' => $transicao->acao,
                        'condicao' => $transicao->condicao,
                        'automatica' => $transicao->automatica,
                        'ordem' => $transicao->ordem ?? 0
                    ])
                ]
            ]);
        } catch (\Exception $e) {
            $workflowId = isset($workflow) && $workflow ? $workflow->id : 'unknown';
            \Log::error('Erro no designerData: ' . $e->getMessage(), ['workflow_id' => $workflowId]);
            return response()->json([
                'success' => false,
                'error' => 'Erro interno do servidor',
                'message' => config('app.debug') ? $e->getMessage() : 'Erro interno'
            ], 500);
        }
    }

    // ==========================================
    // MÉTODOS AUXILIARES PRIVADOS
    // ==========================================

    private static function mapearTipoEtapa(string $role, int $index, int $total): string
    {
        // Primeira etapa sempre é inicial
        if ($index === 0) {
            return 'inicial';
        }
        
        // Última etapa sempre é final
        if ($index === $total - 1) {
            return 'final';
        }
        
        // Etapas que podem ter decisões (aprovar/rejeitar/devolver)
        $rolesDecisao = ['Legislativo', 'Parlamentar'];
        if (in_array($role, $rolesDecisao)) {
            return 'decisao';
        }
        
        // Demais etapas são processos
        return 'processo';
    }

    private function calcularTempoMedioConclusao(Workflow $workflow): ?string
    {
        // TODO: Implementar quando houver dados de histórico suficientes
        return null;
        /*
        $historicos = $workflow->historico()
            ->where('acao', 'finalizar')
            ->get();

        if ($historicos->isEmpty()) {
            return null;
        }

        // Lógica será implementada quando os relacionamentos estiverem completos
        return null;
        */
    }

    private function obterEtapasMaisDemoradas(Workflow $workflow): array
    {
        // TODO: Implementar análise de performance das etapas
        return [];
        /*
        // Análise das etapas mais demoradas baseada no histórico
        $etapasStats = $workflow->historico()
            ->select('etapa_atual_id')
            ->selectRaw('AVG(EXTRACT(EPOCH FROM (created_at - lag(created_at) OVER (PARTITION BY documento_id, documento_type ORDER BY created_at)))) as tempo_medio_segundos')
            ->selectRaw('COUNT(*) as total_passagens')
            ->groupBy('etapa_atual_id')
            ->having('total_passagens', '>', 5) // Apenas etapas com dados suficientes
            ->orderByDesc('tempo_medio_segundos')
            ->limit(3)
            ->get();
        */

        return [];
        // TODO: Implementar quando o sistema tiver dados de histórico suficientes
        /*
        return $etapasStats->map(function ($stat) {
            $etapa = WorkflowEtapa::find($stat->etapa_atual_id);
            return [
                'etapa' => $etapa?->nome ?? 'Etapa Removida',
                'tempo_medio' => $stat->tempo_medio_segundos ? round($stat->tempo_medio_segundos / 3600, 1) . ' horas' : 'N/A',
                'total_passagens' => $stat->total_passagens
            ];
        })->toArray();
        */
    }
}