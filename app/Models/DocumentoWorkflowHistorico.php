<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoWorkflowHistorico extends Model
{
    protected $table = 'documento_workflow_historico';
    
    protected $fillable = [
        'documento_id', 'documento_type', 'workflow_id', 'workflow_transicao_id',
        'etapa_origem_id', 'etapa_destino_id', 'acao', 'dados_transicao',
        'executado_por', 'executado_em'
    ];
    
    protected $casts = [
        'dados_transicao' => 'array',
        'executado_em' => 'datetime'
    ];

    /**
     * Documento associado (morfismo)
     */
    public function documento()
    {
        return $this->morphTo();
    }
    
    /**
     * Workflow do histórico
     */
    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }
    
    /**
     * Transição que foi executada
     */
    public function transicao()
    {
        return $this->belongsTo(WorkflowTransicao::class, 'workflow_transicao_id');
    }
    
    /**
     * Etapa de origem da transição
     */
    public function etapaOrigem()
    {
        return $this->belongsTo(WorkflowEtapa::class, 'etapa_origem_id');
    }
    
    /**
     * Etapa de destino da transição
     */
    public function etapaDestino()
    {
        return $this->belongsTo(WorkflowEtapa::class, 'etapa_destino_id');
    }
    
    /**
     * Usuário que executou a transição
     */
    public function executadoPor()
    {
        return $this->belongsTo(User::class, 'executado_por');
    }

    /**
     * Scope para ordenar por data (mais recente primeiro)
     */
    public function scopeRecente($query)
    {
        return $query->orderBy('executado_em', 'desc');
    }

    /**
     * Scope por ação
     */
    public function scopeAcao($query, string $acao)
    {
        return $query->where('acao', $acao);
    }

    /**
     * Scope por período
     */
    public function scopePeriodo($query, $inicio, $fim)
    {
        return $query->whereBetween('executado_em', [$inicio, $fim]);
    }

    /**
     * Formatar duração entre etapas
     */
    public function duracaoNaEtapa(): ?string
    {
        if (!$this->etapaOrigem) {
            return null;
        }

        // Buscar entrada anterior na etapa de origem
        $anterior = static::where('documento_id', $this->documento_id)
            ->where('documento_type', $this->documento_type)
            ->where('etapa_destino_id', $this->etapa_origem_id)
            ->where('executado_em', '<', $this->executado_em)
            ->orderBy('executado_em', 'desc')
            ->first();

        if (!$anterior) {
            return null;
        }

        $duracao = $anterior->executado_em->diff($this->executado_em);
        
        if ($duracao->days > 0) {
            return $duracao->days . ' dias';
        } elseif ($duracao->h > 0) {
            return $duracao->h . ' horas';
        } else {
            return $duracao->i . ' minutos';
        }
    }

    /**
     * Scope por documento específico
     */
    public function scopePorDocumento($query, string $tipo, int $id)
    {
        return $query->where('documento_type', $tipo)->where('documento_id', $id);
    }

    /**
     * Scope por workflow
     */
    public function scopePorWorkflow($query, int $workflowId)
    {
        return $query->where('workflow_id', $workflowId);
    }

    /**
     * Scope por usuário executor
     */
    public function scopePorUsuario($query, int $userId)
    {
        return $query->where('executado_por', $userId);
    }

    /**
     * Accessor para nome da ação formatado
     */
    public function getAcaoFormatadaAttribute(): string
    {
        $acoes = [
            'criar' => 'Criado',
            'editar' => 'Editado',
            'protocolar' => 'Protocolado',
            'aprovar' => 'Aprovado',
            'rejeitar' => 'Rejeitado',
            'solicitar_alteracao' => 'Solicitação de Alteração',
            'encaminhar' => 'Encaminhado',
            'finalizar' => 'Finalizado',
            'arquivar' => 'Arquivado'
        ];

        return $acoes[$this->acao] ?? ucfirst($this->acao);
    }

    /**
     * Accessor para dados resumidos da transição
     */
    public function getResumoAttribute(): array
    {
        return [
            'acao' => $this->acao_formatada,
            'de' => $this->etapaOrigem?->nome,
            'para' => $this->etapaDestino?->nome,
            'usuario' => $this->executadoPor?->name,
            'data' => $this->executado_em?->format('d/m/Y H:i'),
            'duracao' => $this->duracaoNaEtapa()
        ];
    }
}