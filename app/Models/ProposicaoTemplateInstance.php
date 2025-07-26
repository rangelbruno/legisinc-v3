<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposicaoTemplateInstance extends Model
{
    protected $fillable = [
        'proposicao_id',
        'template_id',
        'arquivo_instance_path',
        'variaveis_preenchidas',
        'status',
        'document_key'
    ];

    protected $casts = [
        'variaveis_preenchidas' => 'array'
    ];

    public function proposicao(): BelongsTo
    {
        return $this->belongsTo(Proposicao::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(DocumentoTemplate::class, 'template_id');
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
