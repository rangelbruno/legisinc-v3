<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    protected $fillable = [
        'nome', 'descricao', 'tipo_documento', 'ativo', 
        'is_default', 'ordem', 'configuracao'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'is_default' => 'boolean',
        'configuracao' => 'array'
    ];

    /**
     * Etapas do workflow ordenadas por ordem
     */
    public function etapas()
    {
        return $this->hasMany(WorkflowEtapa::class)->orderBy('ordem');
    }

    /**
     * Todas as transições do workflow
     */
    public function transicoes()
    {
        return $this->hasMany(WorkflowTransicao::class);
    }

    /**
     * Status de documentos neste workflow
     */
    public function documentosStatus()
    {
        return $this->hasMany(DocumentoWorkflowStatus::class);
    }

    /**
     * Histórico de documentos neste workflow
     */
    public function historico()
    {
        return $this->hasMany(DocumentoWorkflowHistorico::class);
    }

    /**
     * Scope para workflows ativos
     */
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para workflows padrão
     */
    public function scopePadrao($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope por tipo de documento
     */
    public function scopeTipoDocumento($query, $tipo)
    {
        return $query->where('tipo_documento', $tipo);
    }

    /**
     * Verifica se o workflow tem documentos em uso
     */
    public function temDocumentosEmUso(): bool
    {
        return $this->documentosStatus()
            ->whereIn('status', ['em_andamento', 'pausado'])
            ->exists();
    }

    /**
     * Obtém a primeira etapa do workflow
     */
    public function primeiraEtapa(): ?WorkflowEtapa
    {
        return $this->etapas()->first();
    }
}