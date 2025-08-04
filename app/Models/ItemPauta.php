<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemPauta extends Model
{
    protected $fillable = [
        'sessao_id',
        'proposicao_id',
        'ordem',
        'momento',
        'status',
        'resultado_votacao',
        'observacoes'
    ];

    /**
     * Relacionamento com a sessão plenária
     */
    public function sessao(): BelongsTo
    {
        return $this->belongsTo(SessaoPlenaria::class, 'sessao_id');
    }

    /**
     * Relacionamento com a proposição
     */
    public function proposicao(): BelongsTo
    {
        return $this->belongsTo(Proposicao::class);
    }

    /**
     * Scope para itens do expediente
     */
    public function scopeExpediente($query)
    {
        return $query->where('momento', 'EXPEDIENTE');
    }

    /**
     * Scope para itens da ordem do dia
     */
    public function scopeOrdemDia($query)
    {
        return $query->where('momento', 'ORDEM_DO_DIA');
    }

    /**
     * Scope para itens aguardando
     */
    public function scopeAguardando($query)
    {
        return $query->where('status', 'AGUARDANDO');
    }

    /**
     * Scope para itens votados
     */
    public function scopeVotados($query)
    {
        return $query->where('status', 'VOTADO');
    }

    /**
     * Scope para itens aprovados
     */
    public function scopeAprovados($query)
    {
        return $query->where('resultado_votacao', 'APROVADO');
    }

    /**
     * Scope para itens rejeitados
     */
    public function scopeRejeitados($query)
    {
        return $query->where('resultado_votacao', 'REJEITADO');
    }

    /**
     * Scope ordenado por momento e ordem
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('momento')->orderBy('ordem');
    }

    /**
     * Verifica se é item do expediente
     */
    public function isExpediente(): bool
    {
        return $this->momento === 'EXPEDIENTE';
    }

    /**
     * Verifica se é item da ordem do dia
     */
    public function isOrdemDia(): bool
    {
        return $this->momento === 'ORDEM_DO_DIA';
    }

    /**
     * Verifica se está aguardando votação
     */
    public function isAguardando(): bool
    {
        return $this->status === 'AGUARDANDO';
    }

    /**
     * Verifica se foi votado
     */
    public function isVotado(): bool
    {
        return $this->status === 'VOTADO';
    }

    /**
     * Verifica se foi aprovado
     */
    public function isAprovado(): bool
    {
        return $this->resultado_votacao === 'APROVADO';
    }

    /**
     * Verifica se foi rejeitado
     */
    public function isRejeitado(): bool
    {
        return $this->resultado_votacao === 'REJEITADO';
    }

    /**
     * Verifica se teve emendas
     */
    public function isEmendado(): bool
    {
        return $this->resultado_votacao === 'EMENDADO';
    }

    /**
     * Obter cor do badge do momento
     */
    public function getCorMomento(): string
    {
        return match($this->momento) {
            'EXPEDIENTE' => 'info',
            'ORDEM_DO_DIA' => 'primary',
            default => 'secondary'
        };
    }

    /**
     * Obter cor do badge do status
     */
    public function getCorStatus(): string
    {
        return match($this->status) {
            'AGUARDANDO' => 'warning',
            'EM_DISCUSSAO' => 'primary',
            'VOTADO' => 'success',
            'ADIADO' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Obter cor do badge do resultado da votação
     */
    public function getCorResultado(): string
    {
        return match($this->resultado_votacao) {
            'APROVADO' => 'success',
            'REJEITADO' => 'danger',
            'EMENDADO' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Obter texto formatado do momento
     */
    public function getMomentoFormatado(): string
    {
        return match($this->momento) {
            'EXPEDIENTE' => 'Expediente',
            'ORDEM_DO_DIA' => 'Ordem do Dia',
            default => 'Não definido'
        };
    }

    /**
     * Obter texto formatado do status
     */
    public function getStatusFormatado(): string
    {
        return match($this->status) {
            'AGUARDANDO' => 'Aguardando',
            'EM_DISCUSSAO' => 'Em Discussão',
            'VOTADO' => 'Votado',
            'ADIADO' => 'Adiado',
            default => 'Não definido'
        };
    }

    /**
     * Obter texto formatado do resultado da votação
     */
    public function getResultadoFormatado(): string
    {
        return match($this->resultado_votacao) {
            'APROVADO' => 'Aprovado',
            'REJEITADO' => 'Rejeitado',
            'EMENDADO' => 'Aprovado com Emendas',
            default => 'Não votado'
        };
    }
}