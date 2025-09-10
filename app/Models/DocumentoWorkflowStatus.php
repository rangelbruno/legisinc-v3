<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoWorkflowStatus extends Model
{
    protected $table = 'documento_workflow_status';
    
    protected $fillable = [
        'documento_id', 'documento_type', 'workflow_id', 'etapa_atual_id',
        'status', 'prazo_atual', 'iniciado_em', 'finalizado_em', 
        'dados_workflow', 'version'
    ];
    
    protected $casts = [
        'dados_workflow' => 'array',
        'iniciado_em' => 'datetime',
        'finalizado_em' => 'datetime',
        'prazo_atual' => 'datetime'
    ];

    /**
     * Documento associado (morfismo)
     */
    public function documento()
    {
        return $this->morphTo();
    }
    
    /**
     * Workflow atual
     */
    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }
    
    /**
     * Etapa atual do workflow
     */
    public function etapaAtual()
    {
        return $this->belongsTo(WorkflowEtapa::class, 'etapa_atual_id');
    }

    /**
     * Histórico deste documento no workflow
     */
    public function historico()
    {
        return $this->hasMany(DocumentoWorkflowHistorico::class, 'documento_id', 'documento_id')
            ->where('documento_type', $this->documento_type)
            ->where('workflow_id', $this->workflow_id)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Scope para documentos em andamento
     */
    public function scopeEmAndamento($query)
    {
        return $query->where('status', 'em_andamento');
    }

    /**
     * Scope para documentos finalizados
     */
    public function scopeFinalizado($query)
    {
        return $query->where('status', 'finalizado');
    }

    /**
     * Scope para documentos atrasados
     */
    public function scopeAtrasado($query)
    {
        return $query->where('status', 'em_andamento')
            ->whereNotNull('prazo_atual')
            ->where('prazo_atual', '<', now());
    }

    /**
     * Verifica se o documento está atrasado
     */
    public function isAtrasado(): bool
    {
        return $this->status === 'em_andamento' 
            && $this->prazo_atual 
            && $this->prazo_atual < now();
    }

    /**
     * Verifica se foi marcado como atrasado
     */
    public function isMarcadoAtrasado(): bool
    {
        return data_get($this->dados_workflow, 'atrasado', false);
    }

    /**
     * Marca como atrasado
     */
    public function marcarAtrasado(): void
    {
        $dados = $this->dados_workflow ?? [];
        $dados['atrasado'] = true;
        $dados['atrasado_desde'] = now()->toISOString();
        
        $this->update(['dados_workflow' => $dados]);
    }
}