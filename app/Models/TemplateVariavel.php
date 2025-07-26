<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateVariavel extends Model
{
    protected $table = 'template_variaveis';

    protected $fillable = [
        'template_id',
        'nome_variavel',
        'tipo',
        'descricao',
        'obrigatoria',
        'valor_padrao',
        'validacao_regex'
    ];

    protected $casts = [
        'obrigatoria' => 'boolean'
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(DocumentoTemplate::class, 'template_id');
    }

    public function scopeTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeObrigatoria($query)
    {
        return $query->where('obrigatoria', true);
    }
}
