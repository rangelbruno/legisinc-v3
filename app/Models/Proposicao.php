<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proposicao extends Model
{
    protected $table = 'proposicoes';

    protected $fillable = [
        'tipo',
        'ementa',
        'conteudo',
        'arquivo_path',
        'autor_id',
        'status',
        'ano',
        'modelo_id',
        'template_id',
        'ultima_modificacao'
    ];

    protected $casts = [
        'ultima_modificacao' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    /**
     * Scope para filtrar por autor
     */
    public function scopeDoAutor($query, $autorId)
    {
        return $query->where('autor_id', $autorId);
    }

    /**
     * Scope para filtrar por status
     */
    public function scopeComStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get formatted tipo
     */
    public function getTipoFormatadoAttribute()
    {
        $tipos = [
            'projeto_lei_ordinaria' => 'Projeto de Lei Ordinária',
            'projeto_lei_complementar' => 'Projeto de Lei Complementar',
            'indicacao' => 'Indicação',
            'projeto_decreto_legislativo' => 'Projeto de Decreto Legislativo',
            'projeto_resolucao' => 'Projeto de Resolução'
        ];

        return $tipos[$this->tipo] ?? $this->tipo;
    }
}
