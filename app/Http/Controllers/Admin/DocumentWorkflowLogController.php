<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentWorkflowLog;
use App\Models\TramitacaoLog;
use App\Models\DocumentoWorkflowHistorico;
use App\Models\Proposicao;
use App\Models\User;
use App\Models\ScreenPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DocumentWorkflowLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user || !$user->isAdmin()) {
                abort(403, 'Acesso negado. Apenas administradores podem acessar esta funcionalidade.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $filtros = $request->only([
            'proposicao_id', 'user_id', 'event_type', 'stage', 'status',
            'data_inicio', 'data_fim', 'per_page'
        ]);
        $perPage = $request->get('per_page', 25);

        // Query principal para DocumentWorkflowLog
        $documentWorkflowLogs = DocumentWorkflowLog::with(['proposicao', 'user'])
            ->when($filtros['proposicao_id'] ?? null, function ($query, $proposicaoId) {
                return $query->where('proposicao_id', $proposicaoId);
            })
            ->when($filtros['user_id'] ?? null, function ($query, $userId) {
                return $query->where('user_id', $userId);
            })
            ->when($filtros['event_type'] ?? null, function ($query, $eventType) {
                return $query->where('event_type', $eventType);
            })
            ->when($filtros['stage'] ?? null, function ($query, $stage) {
                return $query->where('stage', $stage);
            })
            ->when($filtros['status'] ?? null, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($filtros['data_inicio'] ?? null, function ($query, $dataInicio) {
                return $query->whereDate('created_at', '>=', $dataInicio);
            })
            ->when($filtros['data_fim'] ?? null, function ($query, $dataFim) {
                return $query->whereDate('created_at', '<=', $dataFim);
            })
            ->recentFirst();

        // Query base para TramitacaoLog (legacy - para comparação)
        $tramitacaoLogs = TramitacaoLog::with(['proposicao', 'usuario'])
            ->when($filtros['proposicao_id'] ?? null, function ($query, $proposicaoId) {
                return $query->where('proposicao_id', $proposicaoId);
            })
            ->when($filtros['user_id'] ?? null, function ($query, $userId) {
                return $query->where('user_id', $userId);
            })
            ->when($filtros['data_inicio'] ?? null, function ($query, $dataInicio) {
                return $query->whereDate('created_at', '>=', $dataInicio);
            })
            ->when($filtros['data_fim'] ?? null, function ($query, $dataFim) {
                return $query->whereDate('created_at', '<=', $dataFim);
            })
            ->orderBy('created_at', 'desc');

        // Combinar consultas
        if (!($filtros['proposicao_id'] ?? null)) {
            $workflowLogs = $documentWorkflowLogs->paginate($perPage, ['*'], 'workflow_page');
            $logs = $tramitacaoLogs->paginate($perPage, ['*'], 'tramitacao_page');
        } else {
            $workflowLogs = $documentWorkflowLogs->get();
            $logs = $tramitacaoLogs->get();
        }

        // Estatísticas gerais
        $estatisticas = $this->getEstatisticasDetalhadas();

        // Dados para filtros
        $proposicoes = Proposicao::select('id', 'numero', 'ano', 'tipo')
            ->orderBy('numero', 'desc')
            ->limit(100)
            ->get();

        $usuarios = User::select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        // Event types e stages únicos para filtros
        $eventTypes = DocumentWorkflowLog::select('event_type')
            ->distinct()
            ->orderBy('event_type')
            ->pluck('event_type');

        $stages = DocumentWorkflowLog::select('stage')
            ->distinct()
            ->orderBy('stage')
            ->pluck('stage');

        // Ações mais comuns
        $acoesComuns = DocumentWorkflowLog::select('event_type', DB::raw('count(*) as total'))
            ->groupBy('event_type')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        return view('admin.document-workflow-logs.index', compact(
            'workflowLogs',
            'logs',
            'estatisticas',
            'proposicoes',
            'usuarios',
            'eventTypes',
            'stages',
            'acoesComuns',
            'filtros'
        ));
    }

    private function getEstatisticasDetalhadas(): array
    {
        $hoje = Carbon::today();
        $ontem = Carbon::yesterday();
        $semanaPassada = Carbon::now()->subWeek();
        $mesPassado = Carbon::now()->subMonth();

        // Estatísticas dos novos logs de workflow
        $workflowStats = [
            'logs_hoje' => DocumentWorkflowLog::whereDate('created_at', $hoje)->count(),
            'logs_ontem' => DocumentWorkflowLog::whereDate('created_at', $ontem)->count(),
            'logs_semana' => DocumentWorkflowLog::where('created_at', '>=', $semanaPassada)->count(),
            'logs_mes' => DocumentWorkflowLog::where('created_at', '>=', $mesPassado)->count(),
            'total_logs' => DocumentWorkflowLog::count(),
        ];

        // Estatísticas por status
        $statusStats = DocumentWorkflowLog::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        // Estatísticas por stage
        $stageStats = DocumentWorkflowLog::select('stage', DB::raw('count(*) as total'))
            ->groupBy('stage')
            ->get()
            ->pluck('total', 'stage')
            ->toArray();

        // Estatísticas por event_type
        $eventStats = DocumentWorkflowLog::select('event_type', DB::raw('count(*) as total'))
            ->groupBy('event_type')
            ->get()
            ->pluck('total', 'event_type')
            ->toArray();

        // Logs de PDF específicos
        $pdfStats = [
            'pdf_exports_hoje' => DocumentWorkflowLog::whereDate('created_at', $hoje)
                ->where('event_type', 'pdf_exported')->count(),
            'pdf_exports_sucesso_hoje' => DocumentWorkflowLog::whereDate('created_at', $hoje)
                ->where('event_type', 'pdf_exported')
                ->where('status', 'success')->count(),
            'pdf_exports_erro_hoje' => DocumentWorkflowLog::whereDate('created_at', $hoje)
                ->where('event_type', 'pdf_exported')
                ->where('status', 'error')->count(),
            'pdf_exports_total' => DocumentWorkflowLog::where('event_type', 'pdf_exported')->count(),
        ];

        // Estatísticas de assinatura
        $signatureStats = [
            'assinaturas_hoje' => DocumentWorkflowLog::whereDate('created_at', $hoje)
                ->where('event_type', 'document_signed')->count(),
            'assinaturas_sucesso' => DocumentWorkflowLog::where('event_type', 'document_signed')
                ->where('status', 'success')->count(),
            'assinaturas_erro' => DocumentWorkflowLog::where('event_type', 'document_signed')
                ->where('status', 'error')->count(),
        ];

        // Estatísticas de protocolo
        $protocolStats = [
            'protocolos_hoje' => DocumentWorkflowLog::whereDate('created_at', $hoje)
                ->where('event_type', 'protocol_assigned')->count(),
            'protocolos_total' => DocumentWorkflowLog::where('event_type', 'protocol_assigned')->count(),
        ];

        // Estatísticas S3 baseadas nos logs do sistema
        $s3Stats = $this->getS3Statistics();

        // Estatísticas legacy (para comparação)
        $legacyStats = [
            'tramitacao_logs_hoje' => TramitacaoLog::whereDate('created_at', $hoje)->count(),
            'tramitacao_logs_total' => TramitacaoLog::count(),
        ];

        // Usuários mais ativos
        $usuariosAtivos = DocumentWorkflowLog::select('user_id', DB::raw('count(*) as total'))
            ->with('user:id,name')
            ->where('created_at', '>=', $mesPassado)
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Proposições com mais atividade
        $proposicoesAtivas = DocumentWorkflowLog::select('proposicao_id', DB::raw('count(*) as total'))
            ->with('proposicao:id,numero,ano,tipo')
            ->where('created_at', '>=', $mesPassado)
            ->groupBy('proposicao_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        return array_merge($workflowStats, [
            'status_breakdown' => $statusStats,
            'stage_breakdown' => $stageStats,
            'event_breakdown' => $eventStats,
            'pdf_stats' => $pdfStats,
            'signature_stats' => $signatureStats,
            'protocol_stats' => $protocolStats,
            's3_stats' => $s3Stats,
            'legacy_stats' => $legacyStats,
            'usuarios_ativos' => $usuariosAtivos,
            'proposicoes_ativas' => $proposicoesAtivas,
            'total_proposicoes' => Proposicao::count(),
        ]);
    }

    private function getEstatisticas(): array
    {
        $hoje = Carbon::today();
        $ontem = Carbon::yesterday();
        $semanaPassada = Carbon::now()->subWeek();
        $mesPassado = Carbon::now()->subMonth();

        return [
            'logs_hoje' => TramitacaoLog::whereDate('created_at', $hoje)->count(),
            'logs_ontem' => TramitacaoLog::whereDate('created_at', $ontem)->count(),
            'logs_semana' => TramitacaoLog::where('created_at', '>=', $semanaPassada)->count(),
            'logs_mes' => TramitacaoLog::where('created_at', '>=', $mesPassado)->count(),
            'workflow_hoje' => DocumentoWorkflowHistorico::whereDate('processado_em', $hoje)->count(),
            'workflow_ontem' => DocumentoWorkflowHistorico::whereDate('processado_em', $ontem)->count(),
            'workflow_semana' => DocumentoWorkflowHistorico::where('processado_em', '>=', $semanaPassada)->count(),
            'workflow_mes' => DocumentoWorkflowHistorico::where('processado_em', '>=', $mesPassado)->count(),
            'total_proposicoes' => Proposicao::count(),
            'usuarios_ativos' => User::whereHas('tramitacaoLogs', function ($query) use ($mesPassado) {
                $query->where('created_at', '>=', $mesPassado);
            })->count(),
            'acoes_criticas_hoje' => TramitacaoLog::whereDate('created_at', $hoje)
                ->whereIn('acao', ['ASSINADO', 'PROTOCOLADO', 'APROVADO', 'REJEITADO'])
                ->count(),
            'media_tempo_tramitacao' => $this->getMediaTempoTramitacao()
        ];
    }

    private function getMediaTempoTramitacao(): ?string
    {
        $proposicoesFinalizadas = Proposicao::whereHas('tramitacaoLogs', function ($query) {
            $query->whereIn('acao', ['PROTOCOLADO', 'APROVADO', 'REJEITADO']);
        })->with(['tramitacaoLogs' => function ($query) {
            $query->orderBy('created_at');
        }])->get();

        if ($proposicoesFinalizadas->isEmpty()) {
            return null;
        }

        $tempoTotal = 0;
        $contador = 0;

        foreach ($proposicoesFinalizadas as $proposicao) {
            $logs = $proposicao->tramitacaoLogs;
            if ($logs->count() >= 2) {
                $inicio = $logs->first()->created_at;
                $fim = $logs->last()->created_at;
                $tempoTotal += $inicio->diffInDays($fim);
                $contador++;
            }
        }

        if ($contador === 0) {
            return null;
        }

        $mediaDias = round($tempoTotal / $contador, 1);
        return $mediaDias . ' dias';
    }

    public function show(Request $request, $proposicaoId)
    {
        $proposicao = Proposicao::with([
            'tramitacaoLogs.usuario',
            'workflows.etapaAtual'
        ])->findOrFail($proposicaoId);

        $tramitacaoLogs = TramitacaoLog::where('proposicao_id', $proposicaoId)
            ->with('usuario')
            ->orderBy('created_at', 'desc')
            ->get();

        $workflowHistorico = DocumentoWorkflowHistorico::where('documento_id', $proposicaoId)
            ->where('documento_type', 'App\\Models\\Proposicao')
            ->with([
                'workflow',
                'transicao',
                'etapaOrigem',
                'etapaDestino',
                'executadoPor'
            ])
            ->orderBy('processado_em', 'desc')
            ->get();

        // Obter logs de DocumentWorkflowLog específicos
        $documentWorkflowLogs = DocumentWorkflowLog::where('proposicao_id', $proposicaoId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Obter logs de PDF específicos
        $pdfLogs = $this->getPdfWorkflowLogs(['proposicao_id' => $proposicaoId]);

        // Análise de permissões
        $permissoesUsadas = $this->analisarPermissoes($proposicao, $tramitacaoLogs, $workflowHistorico);

        // Timeline completo
        $timeline = $this->criarTimeline($tramitacaoLogs, $workflowHistorico, $pdfLogs, $documentWorkflowLogs);

        return view('admin.document-workflow-logs.show', compact(
            'proposicao',
            'tramitacaoLogs',
            'workflowHistorico',
            'permissoesUsadas',
            'timeline'
        ));
    }

    private function analisarPermissoes($proposicao, $tramitacaoLogs, $workflowHistorico): array
    {
        $permissoes = [];

        foreach ($tramitacaoLogs as $log) {
            if ($log->usuario) {
                $permissoes[] = [
                    'momento' => $log->created_at,
                    'acao' => $log->acao,
                    'usuario' => $log->usuario->name,
                    'permissao_verificada' => $this->verificarPermissaoUsuario($log->usuario, $log->acao),
                    'dados_adicionais' => $log->dados_adicionais,
                    'tipo' => 'tramitacao'
                ];
            }
        }

        foreach ($workflowHistorico as $historico) {
            if ($historico->executadoPor) {
                $permissoes[] = [
                    'momento' => $historico->processado_em,
                    'acao' => $historico->acao,
                    'usuario' => $historico->executadoPor->name,
                    'permissao_verificada' => $this->verificarPermissaoWorkflow($historico->executadoPor, $historico),
                    'etapa_origem' => $historico->etapaOrigem?->nome,
                    'etapa_destino' => $historico->etapaDestino?->nome,
                    'tipo' => 'workflow'
                ];
            }
        }

        return collect($permissoes)->sortByDesc('momento')->values()->all();
    }

    private function verificarPermissaoUsuario($usuario, $acao): array
    {
        $permissoes = [
            'is_admin' => $usuario->isAdmin(),
            'screen_permissions' => [],
            'acao_permitida' => false
        ];

        $modulosRelevantes = [
            'proposicoes' => ['CRIADO', 'ENVIADO_PARA_REVISAO'],
            'protocolo' => ['PROTOCOLADO'],
            'assinaturas' => ['ASSINADO'],
            'legislativo' => ['REVISADO', 'PARECER_EMITIDO']
        ];

        foreach ($modulosRelevantes as $modulo => $acoes) {
            if (in_array($acao, $acoes)) {
                $temPermissao = ScreenPermission::userCanAccessModule($usuario->id, $modulo);
                $permissoes['screen_permissions'][$modulo] = $temPermissao;
                if ($temPermissao) {
                    $permissoes['acao_permitida'] = true;
                }
            }
        }

        return $permissoes;
    }

    private function verificarPermissaoWorkflow($usuario, $historico): array
    {
        return [
            'is_admin' => $usuario->isAdmin(),
            'pode_executar_transicao' => true,
            'workflow_id' => $historico->workflow_id,
            'transicao_id' => $historico->workflow_transicao_id
        ];
    }

    private function criarTimeline($tramitacaoLogs, $workflowHistorico, $pdfLogs = null, $documentWorkflowLogs = null): array
    {
        $eventos = [];

        // Adicionar eventos de DocumentWorkflowLog (novo sistema)
        if ($documentWorkflowLogs && $documentWorkflowLogs->isNotEmpty()) {
            foreach ($documentWorkflowLogs as $log) {
                $usuario = $log->user;
                $userRoles = $usuario ? $usuario->getRoleNames()->toArray() : [];

                $eventos[] = [
                    'timestamp' => $log->created_at,
                    'tipo' => 'document_workflow',
                    'acao' => str_replace('_', ' ', ucfirst($log->event_type)),
                    'usuario' => $usuario?->name ?? 'Sistema',
                    'usuario_id' => $log->user_id,
                    'usuario_tipo' => !empty($userRoles) ? implode(', ', $userRoles) : 'Sistema',
                    'usuario_icone' => $this->getUserTypeIcon($userRoles),
                    'usuario_cor' => $this->getUserTypeColor($userRoles),
                    'descricao' => $log->description,
                    'observacoes' => $this->formatObservacoes($log),
                    'dados_adicionais' => $log->metadata,
                    'cor' => $this->getStatusColor($log->status),
                    'icone' => $this->getEventTypeIcon($log->event_type),
                    'critico' => $log->status === 'error',
                    'stage' => $log->stage,
                    'execution_time' => $log->formatted_execution_time,
                    'file_info' => $log->file_path ? [
                        'path' => $log->file_path,
                        'size' => $log->formatted_file_size,
                        'type' => $log->file_type,
                    ] : null,
                    'ip_address' => $log->ip_address,
                    'error_message' => $log->error_message,
                ];
            }
        }

        // Manter logs de tramitação existentes
        foreach ($tramitacaoLogs as $log) {
            $usuario = $log->usuario;
            $userRoles = $usuario ? $usuario->getRoleNames()->toArray() : [];

            $eventos[] = [
                'timestamp' => $log->created_at,
                'tipo' => 'tramitacao',
                'acao' => $log->getAcaoFormatada(),
                'usuario' => $usuario?->name ?? 'Sistema',
                'usuario_id' => $log->user_id,
                'usuario_tipo' => !empty($userRoles) ? implode(', ', $userRoles) : 'Sistema',
                'usuario_icone' => $this->getUserTypeIcon($userRoles),
                'usuario_cor' => $this->getUserTypeColor($userRoles),
                'descricao' => $log->getDescricaoMudancaStatus(),
                'observacoes' => $log->observacoes,
                'dados_adicionais' => $log->dados_adicionais,
                'cor' => $log->getCorAcao(),
                'icone' => $log->getIconeAcao(),
                'critico' => $log->isAcaoCritica(),
                'ip_address' => $log->ip_origem ?? null,
            ];
        }

        // Manter workflow histórico existente
        foreach ($workflowHistorico as $historico) {
            $usuario = $historico->executadoPor;
            $userRoles = $usuario ? $usuario->getRoleNames()->toArray() : [];

            $eventos[] = [
                'timestamp' => $historico->processado_em,
                'tipo' => 'workflow',
                'acao' => $historico->acao_formatada,
                'usuario' => $usuario?->name ?? 'Sistema',
                'usuario_id' => $historico->executado_por,
                'usuario_tipo' => !empty($userRoles) ? implode(', ', $userRoles) : 'Sistema',
                'usuario_icone' => $this->getUserTypeIcon($userRoles),
                'usuario_cor' => $this->getUserTypeColor($userRoles),
                'descricao' => "Transição de '{$historico->etapaOrigem?->nome}' para '{$historico->etapaDestino?->nome}'",
                'dados_contexto' => $historico->dados_contexto,
                'duracao' => $historico->duracaoNaEtapa(),
                'cor' => 'info',
                'icone' => 'fas fa-exchange-alt',
                'critico' => false
            ];
        }

        if ($pdfLogs && $pdfLogs->isNotEmpty()) {
            foreach ($pdfLogs as $pdfLog) {
                $isSuccess = isset($pdfLog['storage_verification']['storage_exists']) &&
                            $pdfLog['storage_verification']['storage_exists'];

                $pdfDescription = $this->criarDescricaoPdf($pdfLog);

                $eventos[] = [
                    'timestamp' => $pdfLog['timestamp'],
                    'tipo' => 'pdf_workflow',
                    'acao' => 'PDF Gerado Pós-Aprovação',
                    'usuario' => 'Sistema (Job Queue)',
                    'descricao' => $pdfDescription,
                    'observacoes' => $this->criarObservacoesPdf($pdfLog),
                    'dados_adicionais' => json_encode($pdfLog['raw_data'] ?? []),
                    'cor' => $isSuccess ? 'success' : 'danger',
                    'icone' => $isSuccess ? 'fas fa-file-pdf' : 'fas fa-exclamation-triangle',
                    'critico' => !$isSuccess,
                    'pdf_details' => $pdfLog['pdf_details'] ?? [],
                    'storage_verification' => $pdfLog['storage_verification'] ?? []
                ];
            }
        }

        return collect($eventos)->sortByDesc('timestamp')->values()->all();
    }

    /**
     * Obtém ícone baseado no tipo de usuário
     */
    private function getUserTypeIcon(array $userRoles): string
    {
        if (empty($userRoles)) {
            return 'fas fa-robot'; // Sistema
        }

        if (in_array('ADMIN', $userRoles)) {
            return 'fas fa-user-shield';
        }
        if (in_array('PARLAMENTAR', $userRoles)) {
            return 'fas fa-user-tie';
        }
        if (in_array('LEGISLATIVO', $userRoles)) {
            return 'fas fa-gavel';
        }
        if (in_array('PROTOCOLO', $userRoles)) {
            return 'fas fa-clipboard-list';
        }
        if (in_array('ASSESSOR_JURIDICO', $userRoles)) {
            return 'fas fa-balance-scale';
        }
        if (in_array('EXPEDIENTE', $userRoles)) {
            return 'fas fa-file-alt';
        }

        return 'fas fa-user';
    }

    /**
     * Obtém cor baseada no tipo de usuário
     */
    private function getUserTypeColor(array $userRoles): string
    {
        if (empty($userRoles)) {
            return 'secondary'; // Sistema
        }

        if (in_array('ADMIN', $userRoles)) {
            return 'danger';
        }
        if (in_array('PARLAMENTAR', $userRoles)) {
            return 'primary';
        }
        if (in_array('LEGISLATIVO', $userRoles)) {
            return 'success';
        }
        if (in_array('PROTOCOLO', $userRoles)) {
            return 'info';
        }
        if (in_array('ASSESSOR_JURIDICO', $userRoles)) {
            return 'warning';
        }
        if (in_array('EXPEDIENTE', $userRoles)) {
            return 'dark';
        }

        return 'light';
    }

    /**
     * Determina a próxima etapa esperada no workflow
     */
    public function getProximaEtapa(array $etapasCompletas): ?string
    {
        $fluxoWorkflow = [
            'creation',
            'editing',
            'review',
            'approval',
            'export',
            'signature',
            'protocol'
        ];

        foreach ($fluxoWorkflow as $etapa) {
            if (!in_array($etapa, $etapasCompletas)) {
                return $etapa;
            }
        }

        return null; // Workflow completo
    }

    /**
     * Obtém cor baseada no status
     */
    private function getStatusColor(string $status): string
    {
        return match($status) {
            'success' => 'success',
            'error' => 'danger',
            'warning' => 'warning',
            'pending' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Obtém ícone baseado no tipo de evento
     */
    private function getEventTypeIcon(string $eventType): string
    {
        return match($eventType) {
            'document_created' => 'fas fa-plus-circle',
            'pdf_exported' => 'fas fa-file-pdf',
            'document_signed' => 'fas fa-signature',
            'protocol_assigned' => 'fas fa-clipboard-check',
            'document_edited' => 'fas fa-edit',
            'document_approved' => 'fas fa-check-circle',
            'document_rejected' => 'fas fa-times-circle',
            default => 'fas fa-info-circle',
        };
    }

    /**
     * Formata observações para exibição
     */
    private function formatObservacoes($log): string
    {
        $observacoes = [];

        if ($log->file_path) {
            $observacoes[] = "📁 Arquivo: " . basename($log->file_path);
        }

        if ($log->file_size) {
            $observacoes[] = "📏 Tamanho: " . $log->formatted_file_size;
        }

        if ($log->execution_time_ms) {
            $observacoes[] = "⏱️ Tempo: " . $log->formatted_execution_time;
        }

        if ($log->protocol_number) {
            $observacoes[] = "🔢 Protocolo: " . $log->protocol_number;
        }

        if ($log->signature_type) {
            $observacoes[] = "✍️ Assinatura: " . ucfirst($log->signature_type);
        }

        return implode(' | ', $observacoes);
    }

    private function criarDescricaoPdf(array $pdfLog): string
    {
        $isSuccess = isset($pdfLog['storage_verification']['storage_exists']) &&
                    $pdfLog['storage_verification']['storage_exists'];

        if ($isSuccess) {
            $size = $pdfLog['pdf_details']['file_size_formatted'] ?? 'tamanho indefinido';
            $dbPath = $pdfLog['pdf_details']['database_path'] ?? 'caminho indefinido';

            return "PDF gerado e salvo com sucesso ({$size}) em: " . basename($dbPath);
        } else {
            return "Falha na geração ou salvamento do PDF";
        }
    }

    private function criarObservacoesPdf(array $pdfLog): string
    {
        $observacoes = [];

        if (isset($pdfLog['pdf_details']['database_path'])) {
            $observacoes[] = "📁 Caminho BD: " . $pdfLog['pdf_details']['database_path'];
        }

        if (isset($pdfLog['pdf_details']['absolute_path'])) {
            $observacoes[] = "📂 Caminho físico: " . $pdfLog['pdf_details']['absolute_path'];
        }

        if (isset($pdfLog['pdf_details']['file_size_formatted'])) {
            $observacoes[] = "📏 Tamanho: " . $pdfLog['pdf_details']['file_size_formatted'];
        }

        $verification = $pdfLog['storage_verification'] ?? [];
        if (isset($verification['storage_exists'])) {
            $observacoes[] = $verification['storage_exists']
                ? "✅ Arquivo existe no storage"
                : "❌ Arquivo NÃO encontrado no storage";
        }

        if (isset($verification['valid_pdf']) && $verification['valid_pdf']) {
            $observacoes[] = "📄 PDF válido";
        } elseif (isset($verification['valid_pdf'])) {
            $observacoes[] = "⚠️ PDF pode estar corrompido";
        }

        if (isset($pdfLog['conversion_metadata']['attempts_made'])) {
            $attempts = $pdfLog['conversion_metadata']['attempts_made'];
            $observacoes[] = "🔄 Tentativas: {$attempts}";
        }

        if (isset($pdfLog['conversion_metadata']['converter_used'])) {
            $converter = $pdfLog['conversion_metadata']['converter_used'];
            $observacoes[] = "⚙️ Conversor: {$converter}";
        }

        return implode(' | ', $observacoes);
    }

    public function export(Request $request)
    {
        $filtros = $request->only(['proposicao_id', 'user_id', 'acao', 'data_inicio', 'data_fim']);

        $logs = TramitacaoLog::with(['proposicao', 'usuario'])
            ->when($filtros['proposicao_id'] ?? null, function ($query, $proposicaoId) {
                return $query->where('proposicao_id', $proposicaoId);
            })
            ->when($filtros['user_id'] ?? null, function ($query, $userId) {
                return $query->where('user_id', $userId);
            })
            ->when($filtros['acao'] ?? null, function ($query, $acao) {
                return $query->where('acao', 'like', '%' . $acao . '%');
            })
            ->when($filtros['data_inicio'] ?? null, function ($query, $dataInicio) {
                return $query->whereDate('created_at', '>=', $dataInicio);
            })
            ->when($filtros['data_fim'] ?? null, function ($query, $dataFim) {
                return $query->whereDate('created_at', '<=', $dataFim);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $csvContent = "Data/Hora,Proposição,Ação,Usuário,Status Anterior,Status Novo,Observações\n";

        foreach ($logs as $log) {
            $csvContent .= sprintf(
                "%s,\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
                $log->created_at->format('d/m/Y H:i:s'),
                $log->proposicao ? "#{$log->proposicao->numero}/{$log->proposicao->ano}" : 'N/A',
                $log->getAcaoFormatada(),
                $log->usuario ? $log->usuario->name : 'Sistema',
                $log->status_anterior ?? '',
                $log->status_novo ?? '',
                str_replace('"', '""', $log->observacoes ?? '')
            );
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="logs_fluxo_documentos_' . date('Y-m-d_H-i-s') . '.csv"');
    }

    public function deleteLogs(Request $request)
    {
        $request->validate([
            'periodo' => 'required|in:hoje,semana,mes,3_meses,6_meses,1_ano,todos',
            'confirmar' => 'required|accepted'
        ]);

        $periodo = $request->get('periodo');

        $registrosExcluidos = 0;
        $workflowHistoricoExcluidos = 0;
        $documentWorkflowLogsExcluidos = 0;
        $pdfLogsLimpos = 0;

        // Determine date range based on period
        $dateRange = $this->getDateRangeForPeriod($periodo);

        // Handle legacy logs (TramitacaoLog and DocumentoWorkflowHistorico) - no filtering
        if ($periodo === 'todos') {
            $registrosExcluidos = TramitacaoLog::count();
            $workflowHistoricoExcluidos = DocumentoWorkflowHistorico::count();
            TramitacaoLog::truncate();
            DocumentoWorkflowHistorico::truncate();
        } else {
            $registrosExcluidos = TramitacaoLog::where('created_at', '>=', $dateRange['start'])
                ->when($dateRange['end'], fn($q) => $q->where('created_at', '<=', $dateRange['end']))
                ->delete();

            $workflowHistoricoExcluidos = DocumentoWorkflowHistorico::where('processado_em', '>=', $dateRange['start'])
                ->when($dateRange['end'], fn($q) => $q->where('processado_em', '<=', $dateRange['end']))
                ->delete();
        }

        // Handle DocumentWorkflowLog
        if ($periodo === 'todos') {
            $documentWorkflowLogsExcluidos = DocumentWorkflowLog::count();
            DocumentWorkflowLog::truncate();
        } else {
            $documentWorkflowLogsExcluidos = DocumentWorkflowLog::where('created_at', '>=', $dateRange['start'])
                ->when($dateRange['end'], fn($q) => $q->where('created_at', '<=', $dateRange['end']))
                ->delete();
        }

        // Clean PDF logs
        $pdfLogsLimpos = $this->cleanPdfLogs($periodo);

        $totalExcluidos = $registrosExcluidos + $workflowHistoricoExcluidos + $documentWorkflowLogsExcluidos + $pdfLogsLimpos;

        $mensagem = "Sucesso! Foram excluídos:";
        $mensagem .= "\n• {$registrosExcluidos} logs de tramitação";
        $mensagem .= "\n• {$workflowHistoricoExcluidos} logs de workflow histórico";
        $mensagem .= "\n• {$documentWorkflowLogsExcluidos} logs de workflow de documentos";
        $mensagem .= "\n• {$pdfLogsLimpos} logs de PDF";
        $mensagem .= "\nTotal: {$totalExcluidos} registros removidos do sistema.";

        return redirect()->route('admin.document-workflow-logs.index')
            ->with('success', $mensagem);
    }

    private function getDateRangeForPeriod(string $periodo): array
    {
        return match($periodo) {
            'hoje' => [
                'start' => Carbon::today(),
                'end' => Carbon::tomorrow()
            ],
            'semana' => [
                'start' => Carbon::now()->subWeek(),
                'end' => null
            ],
            'mes' => [
                'start' => Carbon::now()->subMonth(),
                'end' => null
            ],
            '3_meses' => [
                'start' => Carbon::now()->subMonths(3),
                'end' => null
            ],
            '6_meses' => [
                'start' => Carbon::now()->subMonths(6),
                'end' => null
            ],
            '1_ano' => [
                'start' => Carbon::now()->subYear(),
                'end' => null
            ],
            'todos' => [
                'start' => null,
                'end' => null
            ]
        };
    }

    public function exportJson(Request $request)
    {
        $filtros = $request->only(['proposicao_id', 'user_id', 'acao', 'data_inicio', 'data_fim']);

        $logs = TramitacaoLog::with([
            'proposicao' => function($query) {
                $query->select('id', 'numero', 'ano', 'tipo', 'ementa', 'created_at', 'updated_at');
            },
            'usuario' => function($query) {
                $query->select('id', 'name', 'email', 'created_at');
            }
        ])
        ->when($filtros['proposicao_id'] ?? null, function ($query, $proposicaoId) {
            return $query->where('proposicao_id', $proposicaoId);
        })
        ->when($filtros['user_id'] ?? null, function ($query, $userId) {
            return $query->where('user_id', $userId);
        })
        ->when($filtros['acao'] ?? null, function ($query, $acao) {
            return $query->where('acao', 'like', '%' . $acao . '%');
        })
        ->when($filtros['data_inicio'] ?? null, function ($query, $dataInicio) {
            return $query->whereDate('created_at', '>=', $dataInicio);
        })
        ->when($filtros['data_fim'] ?? null, function ($query, $dataFim) {
            return $query->whereDate('created_at', '<=', $dataFim);
        })
        ->orderBy('created_at', 'desc')
        ->get();

        $workflowHistorico = DocumentoWorkflowHistorico::with([
            'workflow' => function($query) {
                $query->select('id', 'nome', 'descricao', 'ativo');
            },
            'transicao' => function($query) {
                $query->select('id', 'nome', 'descricao');
            },
            'etapaOrigem' => function($query) {
                $query->select('id', 'nome', 'descricao');
            },
            'etapaDestino' => function($query) {
                $query->select('id', 'nome', 'descricao');
            },
            'executadoPor' => function($query) {
                $query->select('id', 'name', 'email');
            }
        ])
        ->when($filtros['proposicao_id'] ?? null, function ($query, $proposicaoId) {
            return $query->where('documento_id', $proposicaoId)
                       ->where('documento_type', 'App\\Models\\Proposicao');
        })
        ->when($filtros['user_id'] ?? null, function ($query, $userId) {
            return $query->where('executado_por', $userId);
        })
        ->when($filtros['data_inicio'] ?? null, function ($query, $dataInicio) {
            return $query->whereDate('processado_em', '>=', $dataInicio);
        })
        ->when($filtros['data_fim'] ?? null, function ($query, $dataFim) {
            return $query->whereDate('processado_em', '<=', $dataFim);
        })
        ->orderBy('processado_em', 'desc')
        ->get();

        $exportData = [
            'metadados' => [
                'exportado_em' => now()->toISOString(),
                'exportado_por' => auth()->user()->name,
                'filtros_aplicados' => $filtros,
                'total_logs_tramitacao' => $logs->count(),
                'total_workflow_historico' => $workflowHistorico->count(),
                'versao_sistema' => config('app.version', '1.0'),
                'periodo_exportacao' => [
                    'data_inicio' => $filtros['data_inicio'] ?? 'Sem limite',
                    'data_fim' => $filtros['data_fim'] ?? 'Sem limite'
                ]
            ],
            'logs_tramitacao' => [],
            'workflow_historico' => [],
            'estatisticas' => $this->getEstatisticas()
        ];

        foreach ($logs as $log) {
            $logData = [
                'id' => $log->id,
                'timestamp' => $log->created_at->toISOString(),
                'timestamp_formatado' => $log->created_at->format('d/m/Y H:i:s'),
                'acao' => [
                    'codigo' => $log->acao,
                    'nome_formatado' => $log->getAcaoFormatada(),
                    'cor_badge' => $log->getCorAcao(),
                    'icone' => $log->getIconeAcao(),
                    'critica' => $log->isAcaoCritica(),
                    'descricao_mudanca' => $log->getDescricaoMudancaStatus()
                ],
                'status' => [
                    'anterior' => $log->status_anterior,
                    'novo' => $log->status_novo,
                    'houve_mudanca' => $log->isMudancaStatus()
                ],
                'detalhes' => [
                    'observacoes' => $log->observacoes,
                    'dados_adicionais' => $log->dados_adicionais ? json_decode($log->dados_adicionais, true) : null,
                    'ip_origem' => $log->ip_origem ?? null,
                    'user_agent' => $log->user_agent ?? null
                ],
                'proposicao' => $log->proposicao ? [
                    'id' => $log->proposicao->id,
                    'identificacao' => "#{$log->proposicao->numero}/{$log->proposicao->ano}",
                    'tipo' => $log->proposicao->tipo,
                    'ementa' => $log->proposicao->ementa,
                    'criada_em' => $log->proposicao->created_at ? $log->proposicao->created_at->toISOString() : null,
                    'url_detalhes' => route('admin.document-workflow-logs.show', $log->proposicao->id)
                ] : null,
                'usuario' => $log->usuario ? [
                    'id' => $log->usuario->id,
                    'nome' => $log->usuario->name,
                    'email' => $log->usuario->email,
                    'cadastrado_em' => $log->usuario->created_at ? $log->usuario->created_at->toISOString() : null
                ] : [
                    'id' => null,
                    'nome' => 'Sistema',
                    'email' => null,
                    'cadastrado_em' => null
                ],
                'contexto_sistema' => [
                    'ambiente' => config('app.env'),
                    'versao_laravel' => app()->version(),
                    'timezone' => config('app.timezone')
                ]
            ];

            $exportData['logs_tramitacao'][] = $logData;
        }

        foreach ($workflowHistorico as $historico) {
            $workflowData = [
                'id' => $historico->id,
                'timestamp' => $historico->processado_em ? $historico->processado_em->toISOString() : null,
                'timestamp_formatado' => $historico->processado_em ? $historico->processado_em->format('d/m/Y H:i:s') : null,
                'acao' => [
                    'codigo' => $historico->acao,
                    'nome_formatado' => $historico->acao_formatada ?? $historico->acao,
                    'tipo' => 'workflow'
                ],
                'workflow' => $historico->workflow ? [
                    'id' => $historico->workflow->id,
                    'nome' => $historico->workflow->nome,
                    'descricao' => $historico->workflow->descricao,
                    'ativo' => $historico->workflow->ativo
                ] : null,
                'transicao' => $historico->transicao ? [
                    'id' => $historico->transicao->id,
                    'nome' => $historico->transicao->nome,
                    'descricao' => $historico->transicao->descricao
                ] : null,
                'etapas' => [
                    'origem' => $historico->etapaOrigem ? [
                        'id' => $historico->etapaOrigem->id,
                        'nome' => $historico->etapaOrigem->nome,
                        'descricao' => $historico->etapaOrigem->descricao
                    ] : null,
                    'destino' => $historico->etapaDestino ? [
                        'id' => $historico->etapaDestino->id,
                        'nome' => $historico->etapaDestino->nome,
                        'descricao' => $historico->etapaDestino->descricao
                    ] : null
                ],
                'executor' => $historico->executadoPor ? [
                    'id' => $historico->executadoPor->id,
                    'nome' => $historico->executadoPor->name,
                    'email' => $historico->executadoPor->email
                ] : null,
                'detalhes' => [
                    'dados_contexto' => $historico->dados_contexto ? json_decode($historico->dados_contexto, true) : null,
                    'duracao_na_etapa' => method_exists($historico, 'duracaoNaEtapa') ? $historico->duracaoNaEtapa() : null,
                    'observacoes' => $historico->observacoes ?? null
                ],
                'documento' => [
                    'id' => $historico->documento_id,
                    'tipo' => $historico->documento_type
                ]
            ];

            $exportData['workflow_historico'][] = $workflowData;
        }

        $exportData['sumario_acoes'] = [
            'tramitacao' => $logs->groupBy('acao')->map(function ($grupo, $acao) {
                return [
                    'acao' => $acao,
                    'total' => $grupo->count(),
                    'primeiro_registro' => $grupo->min('created_at'),
                    'ultimo_registro' => $grupo->max('created_at')
                ];
            })->values(),
            'workflow' => $workflowHistorico->groupBy('acao')->map(function ($grupo, $acao) {
                return [
                    'acao' => $acao,
                    'total' => $grupo->count(),
                    'primeiro_registro' => $grupo->min('processado_em'),
                    'ultimo_registro' => $grupo->max('processado_em')
                ];
            })->values()
        ];

        $fileName = 'logs_fluxo_documentos_detalhado_' . date('Y-m-d_H-i-s') . '.json';

        return response()->json($exportData, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function getPdfWorkflowLogs(array $filtros): \Illuminate\Support\Collection
    {
        $pdfLogs = collect();

        try {
            $logPath = storage_path('logs/document_workflow.log');
            $today = now()->format('Y-m-d');

            $possibleLogFiles = [
                storage_path("logs/document_workflow-{$today}.log"),
                $logPath,
            ];

            if (isset($filtros['data_inicio'])) {
                $startDate = \Carbon\Carbon::parse($filtros['data_inicio']);
                $endDate = isset($filtros['data_fim']) ? \Carbon\Carbon::parse($filtros['data_fim']) : now();

                while ($startDate <= $endDate) {
                    $possibleLogFiles[] = storage_path('logs/document_workflow-' . $startDate->format('Y-m-d') . '.log');
                    $startDate->addDay();
                }
            }

            foreach ($possibleLogFiles as $logFile) {
                if (file_exists($logFile)) {
                    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

                    foreach ($lines as $line) {
                        if (strpos($line, 'PDF_') !== false) {
                            $logEntry = $this->parsePdfLogEntry($line, $filtros);
                            if ($logEntry) {
                                $pdfLogs->push($logEntry);
                            }
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            \Log::warning('Erro ao ler logs de PDF workflow', [
                'error' => $e->getMessage()
            ]);
        }

        return $pdfLogs->sortByDesc('timestamp')->take(500);
    }

    private function parsePdfLogEntry(string $line, array $filtros): ?array
    {
        try {
            if (preg_match('/\[([^\]]+)\] \w+\.(\w+): ([^{]+)(.*)/', $line, $matches)) {
                $timestamp = $matches[1];
                $level = $matches[2];
                $message = trim($matches[3]);
                $jsonData = $matches[4] ?? '';

                $data = [];
                if (!empty($jsonData)) {
                    $decoded = json_decode($jsonData, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $data = $decoded;
                    }
                }

                if (isset($filtros['proposicao_id']) &&
                    isset($data['proposicao_id']) &&
                    $data['proposicao_id'] != $filtros['proposicao_id']) {
                    return null;
                }

                if (isset($filtros['acao']) &&
                    strpos($message, $filtros['acao']) === false) {
                    return null;
                }

                return [
                    'timestamp' => \Carbon\Carbon::parse($timestamp),
                    'level' => $level,
                    'message' => $message,
                    'action' => $data['action'] ?? $message,
                    'proposicao_id' => $data['proposicao_id'] ?? null,
                    'proposicao_numero' => $data['proposicao_numero'] ?? null,
                    'pdf_details' => $data['pdf_details'] ?? [],
                    'storage_verification' => $data['storage_verification'] ?? [],
                    'conversion_metadata' => $data['conversion_metadata'] ?? [],
                    'raw_data' => $data,
                    'type' => 'pdf_workflow'
                ];
            }
        } catch (\Exception $e) {
            // Ignorar linhas que não conseguimos parsear
        }

        return null;
    }

    private function cleanPdfLogs(string $periodo): int
    {
        $logsLimpos = 0;

        try {
            $dataLimite = null;

            switch ($periodo) {
                case 'hoje':
                    $dataLimite = Carbon::today();
                    break;
                case 'semana':
                    $dataLimite = Carbon::now()->subWeek();
                    break;
                case 'mes':
                    $dataLimite = Carbon::now()->subMonth();
                    break;
                case 'todos':
                    return $this->clearAllPdfLogFiles();
            }

            if ($dataLimite) {
                $logsLimpos = $this->filterPdfLogFiles($dataLimite);
            }

        } catch (\Exception $e) {
            \Log::error('Erro ao limpar logs de PDF', [
                'error' => $e->getMessage(),
                'periodo' => $periodo
            ]);
        }

        return $logsLimpos;
    }

    private function clearAllPdfLogFiles(): int
    {
        $arquivosLimpos = 0;

        $logFiles = [
            storage_path('logs/document_workflow.log'),
            storage_path('logs/laravel.log')
        ];

        $logDirectory = storage_path('logs');
        if (is_dir($logDirectory)) {
            $files = glob($logDirectory . '/document_workflow-*.log');
            $logFiles = array_merge($logFiles, $files);

            $laravelFiles = glob($logDirectory . '/laravel-*.log');
            $logFiles = array_merge($logFiles, $laravelFiles);
        }

        foreach ($logFiles as $logFile) {
            if (file_exists($logFile)) {
                $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $pdfLinesCount = 0;

                foreach ($lines as $line) {
                    if (strpos($line, 'PDF_') !== false) {
                        $pdfLinesCount++;
                    }
                }

                $nonPdfLines = [];
                foreach ($lines as $line) {
                    if (strpos($line, 'PDF_') === false) {
                        $nonPdfLines[] = $line;
                    }
                }

                file_put_contents($logFile, implode("\n", $nonPdfLines) . "\n");

                $arquivosLimpos += $pdfLinesCount;
            }
        }

        return $arquivosLimpos;
    }

    private function filterPdfLogFiles(Carbon $dataLimite): int
    {
        $logsRemovidos = 0;

        $logFiles = [
            storage_path('logs/document_workflow.log'),
            storage_path('logs/laravel.log')
        ];

        $logDirectory = storage_path('logs');
        if (is_dir($logDirectory)) {
            $files = glob($logDirectory . '/document_workflow-*.log');
            $logFiles = array_merge($logFiles, $files);

            $laravelFiles = glob($logDirectory . '/laravel-*.log');
            $logFiles = array_merge($logFiles, $laravelFiles);
        }

        foreach ($logFiles as $logFile) {
            if (file_exists($logFile)) {
                $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $linhasMantiadas = [];
                $pdfLinhasRemovidas = 0;

                foreach ($lines as $line) {
                    $manterLinha = true;

                    if (strpos($line, 'PDF_') !== false) {
                        if (preg_match('/\[([^\]]+)\]/', $line, $matches)) {
                            try {
                                $timestampLog = Carbon::parse($matches[1]);

                                if ($timestampLog < $dataLimite) {
                                    $manterLinha = false;
                                    $pdfLinhasRemovidas++;
                                }
                            } catch (\Exception $e) {
                                // Se não conseguir parsear a data, manter a linha
                            }
                        }
                    }

                    if ($manterLinha) {
                        $linhasMantiadas[] = $line;
                    }
                }

                if ($pdfLinhasRemovidas > 0) {
                    file_put_contents($logFile, implode("\n", $linhasMantiadas) . "\n");
                    $logsRemovidos += $pdfLinhasRemovidas;
                }
            }
        }

        return $logsRemovidos;
    }

    /**
     * Obtém estatísticas S3 baseadas nos logs do Laravel
     */
    private function getS3Statistics(): array
    {
        $hoje = Carbon::today();
        $logPath = storage_path('logs/laravel.log');

        $s3Stats = [
            'uploads_hoje' => 0,
            'uploads_sucesso' => 0,
            'uploads_erro' => 0,
            'tamanho_total_mb' => 0,
            'tempo_medio_ms' => 0,
            'ultimo_upload' => null,
            'status_configuracao' => 'inativo',
        ];

        try {
            // Verificar se S3 está configurado corretamente
            $s3Config = config('filesystems.disks.s3');
            $hasValidConfig = !empty($s3Config['key']) &&
                             !empty($s3Config['secret']) &&
                             !empty($s3Config['bucket']) &&
                             $s3Config['key'] !== 'your_aws_access_key_here';

            $s3Stats['status_configuracao'] = $hasValidConfig ? 'ativo' : 'inativo';

            if (!$hasValidConfig) {
                return $s3Stats;
            }

            // Ler logs do arquivo
            if (file_exists($logPath)) {
                $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $temposExecucao = [];
                $tamanhoTotal = 0;

                foreach ($lines as $line) {
                    // Procurar por logs S3 relacionados
                    if (strpos($line, 'OnlyOffice S3') !== false || strpos($line, 'S3 Auto') !== false) {

                        // Parse da linha de log
                        if (preg_match('/\[([^\]]+)\]/', $line, $dateMatches)) {
                            $logDate = Carbon::parse($dateMatches[1]);

                            if ($logDate->isToday()) {
                                $s3Stats['uploads_hoje']++;

                                if (!$s3Stats['ultimo_upload'] || $logDate > $s3Stats['ultimo_upload']) {
                                    $s3Stats['ultimo_upload'] = $logDate;
                                }
                            }

                            // Verificar sucesso ou erro
                            if (strpos($line, '✅') !== false || strpos($line, 'bem-sucedido') !== false || strpos($line, 'concluída') !== false) {
                                $s3Stats['uploads_sucesso']++;
                            } elseif (strpos($line, '❌') !== false || strpos($line, 'ERROR') !== false || strpos($line, 'falhou') !== false) {
                                $s3Stats['uploads_erro']++;
                            }

                            // Extrair tempo de execução
                            if (preg_match('/execution_time_ms["\']?[:\s]*(\d+(?:\.\d+)?)/', $line, $timeMatches)) {
                                $temposExecucao[] = (float)$timeMatches[1];
                            }

                            // Extrair tamanho do arquivo
                            if (preg_match('/file_size["\']?[:\s]*["\']?([^"\']*[KMGT]?B)["\']?/', $line, $sizeMatches)) {
                                $tamanhoTotal += $this->convertToBytes($sizeMatches[1]);
                            }
                        }
                    }
                }

                // Calcular estatísticas
                if (!empty($temposExecucao)) {
                    $s3Stats['tempo_medio_ms'] = round(array_sum($temposExecucao) / count($temposExecucao), 0);
                }

                $s3Stats['tamanho_total_mb'] = round($tamanhoTotal / (1024 * 1024), 2);
            }

        } catch (\Exception $e) {
            \Log::warning('Erro ao obter estatísticas S3', [
                'error' => $e->getMessage()
            ]);
        }

        return $s3Stats;
    }

    /**
     * Converte string de tamanho para bytes
     */
    private function convertToBytes(string $size): int
    {
        $size = trim($size);
        $units = ['B' => 1, 'KB' => 1024, 'MB' => 1024*1024, 'GB' => 1024*1024*1024];

        if (preg_match('/^([\d.]+)\s*([KMGT]?B)$/i', $size, $matches)) {
            $number = (float)$matches[1];
            $unit = strtoupper($matches[2]);
            return (int)($number * ($units[$unit] ?? 1));
        }

        return 0;
    }
}