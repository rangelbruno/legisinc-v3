<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TipoProposicaoTemplate extends Model
{
    protected $fillable = [
        'tipo_proposicao_id',
        'document_key', 
        'arquivo_path',
        'variaveis',
        'ativo',
        'updated_by'
    ];

    protected $casts = [
        'variaveis' => 'array',
        'ativo' => 'boolean'
    ];

    // Relacionamentos
    public function tipoProposicao(): BelongsTo
    {
        return $this->belongsTo(TipoProposicao::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    // Métodos úteis
    public function getNomeTemplate(): string
    {
        return "Template: " . $this->tipoProposicao->nome;
    }

    public function getUrlEditor(): string
    {
        return route('templates.editor', $this->tipoProposicao);
    }

    public function getUrlDownload(): string
    {
        // Para OnlyOffice em container, usar nome do container da aplicação
        $baseUrl = config('app.url');
        
        // Se for localhost, trocar para nome do container que o OnlyOffice consegue acessar
        if (str_contains($baseUrl, 'localhost')) {
            $baseUrl = str_replace('localhost:8001', 'legisinc-app', $baseUrl);
        }
        
        return $baseUrl . '/api/templates/' . $this->id . '/download';
    }
}