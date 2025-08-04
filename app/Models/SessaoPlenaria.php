<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessaoPlenaria extends Model
{
    protected $fillable = [
        'data',
        'hora_inicio',
        'hora_fim',
        'status',
        'observacoes',
        'criado_por'
    ];

    protected $casts = [
        'data' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fim' => 'datetime:H:i',
    ];

    /**
     * Relacionamento com o usuário que criou a sessão
     */
    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    /**
     * Relacionamento com os itens da pauta
     */
    public function itensPauta(): HasMany
    {
        return $this->hasMany(ItemPauta::class, 'sessao_id');
    }

    /**
     * Relacionamento com itens do expediente
     */
    public function itensExpediente(): HasMany
    {
        return $this->hasMany(ItemPauta::class, 'sessao_id')
            ->where('momento', 'EXPEDIENTE');
    }

    /**
     * Relacionamento com itens da ordem do dia
     */
    public function itensOrdemDia(): HasMany
    {
        return $this->hasMany(ItemPauta::class, 'sessao_id')
            ->where('momento', 'ORDEM_DO_DIA');
    }

    /**
     * Scope para sessões agendadas
     */
    public function scopeAgendadas($query)
    {
        return $query->where('status', 'AGENDADA');
    }

    /**
     * Scope para sessões em andamento
     */
    public function scopeEmAndamento($query)
    {
        return $query->where('status', 'EM_ANDAMENTO');
    }

    /**
     * Scope para sessões finalizadas
     */
    public function scopeFinalizadas($query)
    {
        return $query->where('status', 'FINALIZADA');
    }

    /**
     * Scope para sessões de hoje
     */
    public function scopeHoje($query)
    {
        return $query->whereDate('data', today());
    }

    /**
     * Scope para sessões futuras
     */
    public function scopeFuturas($query)
    {
        return $query->where('data', '>', today());
    }

    /**
     * Verifica se a sessão está agendada
     */
    public function isAgendada(): bool
    {
        return $this->status === 'AGENDADA';
    }

    /**
     * Verifica se a sessão está em andamento
     */
    public function isEmAndamento(): bool
    {
        return $this->status === 'EM_ANDAMENTO';
    }

    /**
     * Verifica se a sessão foi finalizada
     */
    public function isFinalizada(): bool
    {
        return $this->status === 'FINALIZADA';
    }

    /**
     * Verifica se a sessão foi cancelada
     */
    public function isCancelada(): bool
    {
        return $this->status === 'CANCELADA';
    }

    /**
     * Obter cor do badge do status
     */
    public function getCorStatus(): string
    {
        return match($this->status) {
            'AGENDADA' => 'primary',
            'EM_ANDAMENTO' => 'warning',
            'FINALIZADA' => 'success',
            'CANCELADA' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Obter texto formatado do status
     */
    public function getStatusFormatado(): string
    {
        return match($this->status) {
            'AGENDADA' => 'Agendada',
            'EM_ANDAMENTO' => 'Em Andamento',
            'FINALIZADA' => 'Finalizada',
            'CANCELADA' => 'Cancelada',
            default => 'Não definido'
        };
    }

    /**
     * Obter número total de itens na pauta
     */
    public function getTotalItens(): int
    {
        return $this->itensPauta()->count();
    }

    /**
     * Obter número de itens votados
     */
    public function getItensVotados(): int
    {
        return $this->itensPauta()->where('status', 'VOTADO')->count();
    }
}