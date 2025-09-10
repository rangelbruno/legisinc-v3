<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowTransicao extends Model
{
    protected $table = 'workflow_transicoes';
    
    protected $fillable = [
        'workflow_id', 'etapa_origem_id', 'etapa_destino_id',
        'acao', 'condicao', 'automatica'
    ];

    protected $casts = [
        'condicao' => 'array',
        'automatica' => 'boolean'
    ];

    /**
     * Workflow pai
     */
    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
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
     * Scope para transições automáticas
     */
    public function scopeAutomatica($query)
    {
        return $query->where('automatica', true);
    }

    /**
     * Scope para transições por ação
     */
    public function scopeAcao($query, string $acao)
    {
        return $query->where('acao', $acao);
    }

    /**
     * Verifica se a transição tem condições
     */
    public function temCondicoes(): bool
    {
        return !empty($this->condicao);
    }
}