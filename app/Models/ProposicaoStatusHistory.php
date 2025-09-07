<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposicaoStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'proposicao_status_history';

    protected $fillable = [
        'proposicao_id',
        'status_anterior',
        'status_novo',
        'user_id',
        'observacoes',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Proposição relacionada
     */
    public function proposicao(): BelongsTo
    {
        return $this->belongsTo(Proposicao::class);
    }

    /**
     * Usuário que fez a mudança
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para buscar histórico de uma proposição
     */
    public function scopeForProposicao($query, int $proposicaoId)
    {
        return $query->where('proposicao_id', $proposicaoId);
    }

    /**
     * Scope para transições específicas
     */
    public function scopeTransition($query, string $from, string $to)
    {
        return $query->where('status_anterior', $from)
                     ->where('status_novo', $to);
    }

    /**
     * Accessor para descrição da transição
     */
    public function getTransicaoDescricaoAttribute(): string
    {
        return "{$this->status_anterior} → {$this->status_novo}";
    }
}