<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParecerJuridico extends Model
{
    protected $fillable = [
        'proposicao_id',
        'assessor_id',
        'tipo_parecer',
        'fundamentacao',
        'conclusao',
        'emendas',
        'pdf_path',
        'data_emissao'
    ];

    protected $casts = [
        'data_emissao' => 'datetime',
    ];

    /**
     * Relacionamento com Proposição
     */
    public function proposicao(): BelongsTo
    {
        return $this->belongsTo(Proposicao::class);
    }

    /**
     * Relacionamento com Assessor (User)
     */
    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    /**
     * Scope para pareceres favoráveis
     */
    public function scopeFavoraveis($query)
    {
        return $query->where('tipo_parecer', 'FAVORAVEL');
    }

    /**
     * Scope para pareceres contrários
     */
    public function scopeContrarios($query)
    {
        return $query->where('tipo_parecer', 'CONTRARIO');
    }

    /**
     * Scope para pareceres com emendas
     */
    public function scopeComEmendas($query)
    {
        return $query->where('tipo_parecer', 'COM_EMENDAS');
    }

    /**
     * Verifica se o parecer é favorável
     */
    public function isFavoravel(): bool
    {
        return $this->tipo_parecer === 'FAVORAVEL';
    }

    /**
     * Verifica se o parecer é contrário
     */
    public function isContrario(): bool
    {
        return $this->tipo_parecer === 'CONTRARIO';
    }

    /**
     * Verifica se o parecer tem emendas
     */
    public function hasEmendas(): bool
    {
        return $this->tipo_parecer === 'COM_EMENDAS';
    }

    /**
     * Obter cor do badge do tipo de parecer
     */
    public function getCorTipoParecer(): string
    {
        return match($this->tipo_parecer) {
            'FAVORAVEL' => 'success',
            'CONTRARIO' => 'danger',
            'COM_EMENDAS' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Obter texto formatado do tipo de parecer
     */
    public function getTipoParecerFormatado(): string
    {
        return match($this->tipo_parecer) {
            'FAVORAVEL' => 'Favorável',
            'CONTRARIO' => 'Contrário',
            'COM_EMENDAS' => 'Favorável com Emendas',
            default => 'Não definido'
        };
    }
}