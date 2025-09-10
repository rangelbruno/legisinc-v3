<?php

namespace App\Services\Workflow;

use App\Events\WorkflowAdvanced;
use App\Models\{Workflow, WorkflowEtapa, DocumentoWorkflowStatus, DocumentoWorkflowHistorico, WorkflowTransicao};
use App\Services\Workflow\ConditionEvaluator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\{DB, Gate, Log};
use Illuminate\Support\Carbon;

class WorkflowService
{
    /**
     * Inicia um fluxo de workflow para um documento
     */
    public function iniciarFluxo(Model $documento, int $workflowId): void
    {
        DB::transaction(function () use ($documento, $workflowId) {
            $workflow = Workflow::with(['etapas' => fn($q) => $q->orderBy('ordem')])
                              ->findOrFail($workflowId);
            
            $primeiraEtapa = $workflow->etapas->firstOrFail();

            // Criar ou atualizar status (idempotente)
            $status = DocumentoWorkflowStatus::updateOrCreate(
                [
                    'documento_id' => $documento->id,
                    'documento_type' => $documento::class,
                    'workflow_id' => $workflow->id
                ],
                [
                    'etapa_atual_id' => $primeiraEtapa->id,
                    'status' => 'em_andamento',
                    'iniciado_em' => now(),
                    'prazo_atual' => $this->calcularPrazo($primeiraEtapa),
                    'version' => DB::raw('version + 1')
                ]
            );

            // Histórico inicial
            DocumentoWorkflowHistorico::create([
                'documento_id' => $documento->id,
                'documento_type' => $documento::class,
                'workflow_id' => $workflow->id,
                'etapa_atual_id' => $primeiraEtapa->id,
                'usuario_id' => auth()->id() ?? 1, // Sistema
                'acao' => 'criado',
                'dados_contexto' => ['workflow_iniciado' => true]
            ]);

            // Atualizar campos de acesso rápido no documento
            $documento->update([
                'workflow_id' => $workflow->id,
                'etapa_workflow_atual_id' => $primeiraEtapa->id,
                'fluxo_personalizado' => true
            ]);

            Log::info('Workflow iniciado', [
                'documento' => $documento::class . ':' . $documento->id,
                'workflow' => $workflow->nome,
                'etapa_inicial' => $primeiraEtapa->nome
            ]);

            event(new WorkflowAdvanced($documento, null, $primeiraEtapa, 'criado'));
        });
    }

    /**
     * Avança o documento para próxima etapa do workflow
     */
    public function avancarEtapa(
        Model $documento, 
        string $acao, 
        ?string $comentario = null,
        ?string $idempotencyKey = null
    ): void {
        DB::transaction(function () use ($documento, $acao, $comentario, $idempotencyKey) {
            // 🔒 Lock otimista para evitar condições de corrida
            $status = DocumentoWorkflowStatus::where([
                'documento_id' => $documento->id,
                'documento_type' => $documento::class,
            ])->lockForUpdate()->firstOrFail();

            // Verificar idempotência se fornecida
            if ($idempotencyKey && $this->jaProcessado($idempotencyKey)) {
                Log::info('Ação já processada (idempotência)', [
                    'documento' => $documento::class . ':' . $documento->id,
                    'acao' => $acao,
                    'key' => $idempotencyKey
                ]);
                return; // Já foi processado
            }

            $etapaAtual = WorkflowEtapa::findOrFail($status->etapa_atual_id);

            // Verificar permissões
            if (!$this->verificarPermissoes(auth()->user(), $documento, $acao)) {
                throw new \Exception('Sem permissão para executar esta ação');
            }

            // Determinar próxima etapa
            $proximaEtapa = $this->obterProximaEtapa($etapaAtual, $acao, $documento);
            if (!$proximaEtapa) {
                throw new \RuntimeException("Transição inválida: {$acao} na etapa {$etapaAtual->nome}");
            }

            // Registrar no histórico
            DocumentoWorkflowHistorico::create([
                'documento_id' => $documento->id,
                'documento_type' => $documento::class,
                'workflow_id' => $status->workflow_id,
                'etapa_atual_id' => $proximaEtapa->id,
                'etapa_anterior_id' => $etapaAtual->id,
                'usuario_id' => auth()->id(),
                'acao' => $acao,
                'comentario' => $comentario,
                'prazo_limite' => $this->calcularPrazo($proximaEtapa),
                'dados_contexto' => [
                    'idempotency_key' => $idempotencyKey,
                    'transicao_automatica' => false
                ]
            ]);

            // Atualizar status (com versioning)
            $novoStatus = $this->isEtapaFinal($proximaEtapa) ? 'finalizado' : 'em_andamento';
            $status->update([
                'etapa_atual_id' => $proximaEtapa->id,
                'status' => $novoStatus,
                'finalizado_em' => $novoStatus === 'finalizado' ? now() : null,
                'prazo_atual' => $this->calcularPrazo($proximaEtapa),
                'version' => $status->version + 1 // ⚡ Lock otimista
            ]);

            // Atualizar documento para acesso rápido
            $documento->update([
                'etapa_workflow_atual_id' => $proximaEtapa->id
            ]);

            // Marcar idempotência se fornecida
            if ($idempotencyKey) {
                $this->marcarProcessado($idempotencyKey);
            }

            Log::info('Workflow avançado', [
                'documento' => $documento::class . ':' . $documento->id,
                'acao' => $acao,
                'etapa_anterior' => $etapaAtual->nome,
                'etapa_atual' => $proximaEtapa->nome,
                'novo_status' => $novoStatus
            ]);

            event(new WorkflowAdvanced($documento, $etapaAtual, $proximaEtapa, $acao));
        });
    }

    /**
     * Verifica se o usuário tem permissão para executar uma ação
     */
    public function verificarPermissoes($usuario, Model $documento, string $acao): bool
    {
        if (!$usuario) {
            return false;
        }

        $status = DocumentoWorkflowStatus::where([
            'documento_id' => $documento->id,
            'documento_type' => $documento::class,
        ])->first();

        if (!$status) {
            return false;
        }

        $etapaAtual = WorkflowEtapa::find($status->etapa_atual_id);
        if (!$etapaAtual) {
            return false;
        }

        // 1. Verificar role da etapa
        if ($etapaAtual->role_responsavel && !$usuario->hasRole($etapaAtual->role_responsavel)) {
            return false;
        }

        // 2. Verificar ação permitida na etapa
        $acoesPermitidas = $etapaAtual->acoes_possiveis ?? [];
        if (!in_array($acao, $acoesPermitidas)) {
            return false;
        }

        // 3. Gate/Policy específica
        return Gate::allows('workflow.' . $acao, [$documento, $etapaAtual]);
    }

    /**
     * Obtém a próxima etapa baseada na ação atual
     */
    public function obterProximaEtapa(WorkflowEtapa $etapaAtual, string $acao, Model $documento): ?WorkflowEtapa
    {
        $transicao = WorkflowTransicao::where([
            'workflow_id' => $etapaAtual->workflow_id,
            'etapa_origem_id' => $etapaAtual->id,
            'acao' => $acao,
        ])->first();

        if (!$transicao) {
            return null;
        }

        // Avaliar condições JSON
        if ($transicao->condicao && !ConditionEvaluator::check($transicao->condicao, $documento)) {
            return null;
        }

        return WorkflowEtapa::find($transicao->etapa_destino_id);
    }

    /**
     * Obtém o status atual de um documento no workflow
     */
    public function obterStatus(Model $documento): ?DocumentoWorkflowStatus
    {
        return DocumentoWorkflowStatus::where([
            'documento_id' => $documento->id,
            'documento_type' => $documento::class,
        ])->with(['workflow', 'etapaAtual'])->first();
    }

    /**
     * Obtém o histórico completo de um documento no workflow
     */
    public function obterHistorico(Model $documento): \Illuminate\Database\Eloquent\Collection
    {
        return DocumentoWorkflowHistorico::where([
            'documento_id' => $documento->id,
            'documento_type' => $documento::class,
        ])->with(['etapaAtual', 'etapaAnterior', 'usuario'])
          ->orderBy('processado_em', 'desc')
          ->get();
    }

    /**
     * Lista todos os documentos em determinada etapa
     */
    public function documentosNaEtapa(int $etapaId): \Illuminate\Database\Eloquent\Collection
    {
        return DocumentoWorkflowStatus::where('etapa_atual_id', $etapaId)
            ->where('status', 'em_andamento')
            ->with(['documento', 'workflow'])
            ->get();
    }

    /**
     * Lista documentos atrasados
     */
    public function documentosAtrasados(): \Illuminate\Database\Eloquent\Collection
    {
        return DocumentoWorkflowStatus::atrasado()
            ->with(['documento', 'workflow', 'etapaAtual'])
            ->get();
    }

    /**
     * Pausa um workflow
     */
    public function pausarWorkflow(Model $documento, string $motivo = null): void
    {
        DB::transaction(function () use ($documento, $motivo) {
            $status = DocumentoWorkflowStatus::where([
                'documento_id' => $documento->id,
                'documento_type' => $documento::class,
            ])->lockForUpdate()->firstOrFail();

            $dados = $status->dados_workflow ?? [];
            $dados['pausado_em'] = now()->toISOString();
            $dados['motivo_pausa'] = $motivo;

            $status->update([
                'status' => 'pausado',
                'dados_workflow' => $dados,
                'version' => $status->version + 1
            ]);

            // Registrar no histórico
            DocumentoWorkflowHistorico::create([
                'documento_id' => $documento->id,
                'documento_type' => $documento::class,
                'workflow_id' => $status->workflow_id,
                'etapa_atual_id' => $status->etapa_atual_id,
                'usuario_id' => auth()->id(),
                'acao' => 'pausado',
                'comentario' => $motivo,
                'dados_contexto' => ['motivo' => $motivo]
            ]);
        });
    }

    /**
     * Retoma um workflow pausado
     */
    public function retomarWorkflow(Model $documento): void
    {
        DB::transaction(function () use ($documento) {
            $status = DocumentoWorkflowStatus::where([
                'documento_id' => $documento->id,
                'documento_type' => $documento::class,
            ])->lockForUpdate()->firstOrFail();

            $dados = $status->dados_workflow ?? [];
            $dados['retomado_em'] = now()->toISOString();
            unset($dados['pausado_em'], $dados['motivo_pausa']);

            $status->update([
                'status' => 'em_andamento',
                'dados_workflow' => $dados,
                'version' => $status->version + 1
            ]);

            // Registrar no histórico
            DocumentoWorkflowHistorico::create([
                'documento_id' => $documento->id,
                'documento_type' => $documento::class,
                'workflow_id' => $status->workflow_id,
                'etapa_atual_id' => $status->etapa_atual_id,
                'usuario_id' => auth()->id(),
                'acao' => 'retomado'
            ]);
        });
    }

    // ==========================================
    // MÉTODOS AUXILIARES PRIVADOS
    // ==========================================

    /**
     * Verifica se a etapa é final (sem transições de saída)
     */
    private function isEtapaFinal(WorkflowEtapa $etapa): bool
    {
        return !WorkflowTransicao::where('etapa_origem_id', $etapa->id)->exists();
    }

    /**
     * Calcula o prazo limite baseado na etapa
     */
    private function calcularPrazo(WorkflowEtapa $etapa): ?Carbon
    {
        return $etapa->tempo_limite_dias 
            ? now()->addDays($etapa->tempo_limite_dias)
            : null;
    }

    /**
     * Verifica se uma ação já foi processada (idempotência)
     */
    private function jaProcessado(string $key): bool
    {
        return cache()->has("workflow_idempotency:{$key}");
    }

    /**
     * Marca uma ação como processada (idempotência)
     */
    private function marcarProcessado(string $key): void
    {
        cache()->put("workflow_idempotency:{$key}", true, now()->addHours(24));
    }
}