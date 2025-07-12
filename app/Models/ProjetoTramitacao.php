<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class ProjetoTramitacao extends Model
{
    use HasFactory;

    protected $table = 'projeto_tramitacao';

    protected $fillable = [
        'projeto_id',
        'usuario_id',
        'status_anterior',
        'status_atual',
        'acao',
        'observacoes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Constantes conforme documento
    public const STATUS_TRAMITACAO = [
        'rascunho' => 'Rascunho',
        'enviado' => 'Enviado para Análise',
        'em_analise' => 'Em Análise',
        'aprovado' => 'Aprovado',
        'rejeitado' => 'Rejeitado',
        'assinado' => 'Assinado',
        'protocolado' => 'Protocolado',
        'em_sessao' => 'Em Sessão',
        'votado' => 'Votado',
    ];

    public const ACOES = [
        'criou' => 'Criou',
        'enviou' => 'Enviou',
        'analisou' => 'Analisou',
        'aprovou' => 'Aprovou',
        'rejeitou' => 'Rejeitou',
        'assinou' => 'Assinou',
        'protocolou' => 'Protocolou',
        'incluiu_sessao' => 'Incluiu em Sessão',
        'votou' => 'Votou',
    ];

    // Relacionamentos
    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Accessors
    public function getAcaoFormatadaAttribute(): string
    {
        return self::ACOES[$this->acao] ?? $this->acao;
    }

    public function getStatusAnteriorFormatadoAttribute(): string
    {
        return self::STATUS_TRAMITACAO[$this->status_anterior] ?? $this->status_anterior;
    }

    public function getStatusAtualFormatadoAttribute(): string
    {
        return self::STATUS_TRAMITACAO[$this->status_atual] ?? $this->status_atual;
    }

    public function getDataFormatadaAttribute(): string
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    public function getObservacoesFormatadas(): string
    {
        if (!$this->observacoes) {
            return '';
        }

        return nl2br(e($this->observacoes));
    }

    // Scopes
    public function scopePorProjeto($query, $projetoId)
    {
        return $query->where('projeto_id', $projetoId);
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopePorAcao($query, $acao)
    {
        return $query->where('acao', $acao);
    }

    public function scopePorStatusAtual($query, $status)
    {
        return $query->where('status_atual', $status);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Métodos de negócio
    public function isAcaoPositiva(): bool
    {
        return in_array($this->acao, ['aprovou', 'assinou', 'protocolou', 'incluiu_sessao']);
    }

    public function isAcaoNegativa(): bool
    {
        return $this->acao === 'rejeitou';
    }

    public function getDescricaoCompleta(): string
    {
        $usuario = $this->usuario->name ?? 'Usuario';
        $acao = $this->acao_formatada;
        
        if ($this->status_anterior && $this->status_anterior !== $this->status_atual) {
            return "{$usuario} {$acao} o projeto (de {$this->status_anterior_formatado} para {$this->status_atual_formatado})";
        }
        
        return "{$usuario} {$acao} o projeto";
    }
}