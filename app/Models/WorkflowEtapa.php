<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowEtapa extends Model
{
    protected $fillable = [
        'workflow_id', 'key', 'nome', 'descricao', 'role_responsavel',
        'ordem', 'tempo_limite_dias', 'permite_edicao', 
        'permite_assinatura', 'requer_aprovacao', 
        'acoes_possiveis', 'condicoes'
    ];

    protected $casts = [
        'permite_edicao' => 'boolean',
        'permite_assinatura' => 'boolean',
        'requer_aprovacao' => 'boolean',
        'acoes_possiveis' => 'array',
        'condicoes' => 'array'
    ];

    /**
     * Workflow pai
     */
    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }
    
    /**
     * Transições que saem desta etapa
     */
    public function transicoesOrigem()
    {
        return $this->hasMany(WorkflowTransicao::class, 'etapa_origem_id');
    }
    
    /**
     * Transições que chegam nesta etapa
     */
    public function transicoesDestino()
    {
        return $this->hasMany(WorkflowTransicao::class, 'etapa_destino_id');
    }

    /**
     * Status de documentos nesta etapa
     */
    public function documentosStatus()
    {
        return $this->hasMany(DocumentoWorkflowStatus::class, 'etapa_atual_id');
    }

    /**
     * Histórico de documentos nesta etapa
     */
    public function historicoAtual()
    {
        return $this->hasMany(DocumentoWorkflowHistorico::class, 'etapa_atual_id');
    }

    /**
     * Histórico de documentos que passaram por esta etapa
     */
    public function historicoAnterior()
    {
        return $this->hasMany(DocumentoWorkflowHistorico::class, 'etapa_anterior_id');
    }

    /**
     * Verifica se uma ação é permitida nesta etapa
     */
    public function permiteAcao(string $acao): bool
    {
        return in_array($acao, $this->acoes_possiveis ?? []);
    }

    /**
     * Verifica se é etapa final (sem transições de saída)
     */
    public function isEtapaFinal(): bool
    {
        return $this->transicoesOrigem()->count() === 0;
    }

    /**
     * Obtém próximas etapas possíveis para uma ação
     */
    public function proximasEtapas(string $acao)
    {
        return $this->transicoesOrigem()
            ->where('acao', $acao)
            ->with('etapaDestino')
            ->get()
            ->pluck('etapaDestino');
    }
}