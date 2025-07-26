<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentoTemplate extends Model
{
    protected $fillable = [
        'nome',
        'descricao',
        'tipo_proposicao_id',
        'arquivo_original_path',
        'arquivo_modelo_path',
        'variaveis_mapeamento',
        'configuracao_onlyoffice',
        'ativo',
        'created_by'
    ];

    protected $casts = [
        'variaveis_mapeamento' => 'array',
        'configuracao_onlyoffice' => 'array',
        'ativo' => 'boolean'
    ];

    public function tipoProposicao(): BelongsTo
    {
        return $this->belongsTo(TipoProposicao::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function variaveis(): HasMany
    {
        return $this->hasMany(TemplateVariavel::class, 'template_id');
    }

    public function instances(): HasMany
    {
        return $this->hasMany(ProposicaoTemplateInstance::class, 'template_id');
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }
}
