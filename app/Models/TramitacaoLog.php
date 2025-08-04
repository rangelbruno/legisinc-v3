<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TramitacaoLog extends Model
{
    protected $fillable = [
        'proposicao_id',
        'acao',
        'user_id',
        'status_anterior',
        'status_novo',
        'observacoes',
        'dados_adicionais'
    ];

    protected $casts = [
        'dados_adicionais' => 'array',
    ];

    /**
     * Relacionamento com a proposição
     */
    public function proposicao(): BelongsTo
    {
        return $this->belongsTo(Proposicao::class);
    }

    /**
     * Relacionamento com o usuário que executou a ação
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope para ordenar por data decrescente
     */
    public function scopeRecentes($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope para ações específicas
     */
    public function scopePorAcao($query, $acao)
    {
        return $query->where('acao', $acao);
    }

    /**
     * Scope para logs de uma proposição específica
     */
    public function scopePorProposicao($query, $proposicaoId)
    {
        return $query->where('proposicao_id', $proposicaoId);
    }

    /**
     * Scope para logs de um usuário específico
     */
    public function scopePorUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Obter cor do badge da ação
     */
    public function getCorAcao(): string
    {
        return match($this->acao) {
            'CRIADO' => 'primary',
            'ENVIADO_PARA_REVISAO' => 'info',
            'REVISADO' => 'warning',
            'ASSINADO' => 'success',
            'PROTOCOLADO' => 'dark',
            'PARECER_EMITIDO' => 'secondary',
            'INCLUIDO_PAUTA' => 'primary',
            'VOTADO' => 'info',
            'APROVADO' => 'success',
            'REJEITADO' => 'danger',
            default => 'light'
        };
    }

    /**
     * Obter texto formatado da ação
     */
    public function getAcaoFormatada(): string
    {
        return match($this->acao) {
            'CRIADO' => 'Proposição criada',
            'ENVIADO_PARA_REVISAO' => 'Enviado para revisão',
            'REVISADO' => 'Revisão concluída',
            'ASSINADO' => 'Documento assinado',
            'PROTOCOLADO' => 'Protocolado',
            'PARECER_EMITIDO' => 'Parecer jurídico emitido',
            'INCLUIDO_PAUTA' => 'Incluído na pauta',
            'VOTADO' => 'Votação realizada',
            'APROVADO' => 'Proposição aprovada',
            'REJEITADO' => 'Proposição rejeitada',
            default => 'Ação não reconhecida'
        };
    }

    /**
     * Obter descrição detalhada da mudança de status
     */
    public function getDescricaoMudancaStatus(): string
    {
        if ($this->status_anterior && $this->status_novo) {
            return "Status alterado de '{$this->status_anterior}' para '{$this->status_novo}'";
        }
        
        if ($this->status_novo) {
            return "Status definido como '{$this->status_novo}'";
        }
        
        return $this->getAcaoFormatada();
    }

    /**
     * Verificar se a ação representa uma mudança de status
     */
    public function isMudancaStatus(): bool
    {
        return !empty($this->status_anterior) || !empty($this->status_novo);
    }

    /**
     * Verificar se a ação é crítica (requer atenção especial)
     */
    public function isAcaoCritica(): bool
    {
        return in_array($this->acao, [
            'ASSINADO',
            'PROTOCOLADO',
            'APROVADO',
            'REJEITADO'
        ]);
    }

    /**
     * Obter ícone FontAwesome para a ação
     */
    public function getIconeAcao(): string
    {
        return match($this->acao) {
            'CRIADO' => 'fas fa-plus-circle',
            'ENVIADO_PARA_REVISAO' => 'fas fa-paper-plane',
            'REVISADO' => 'fas fa-edit',
            'ASSINADO' => 'fas fa-signature',
            'PROTOCOLADO' => 'fas fa-stamp',
            'PARECER_EMITIDO' => 'fas fa-gavel',
            'INCLUIDO_PAUTA' => 'fas fa-list',
            'VOTADO' => 'fas fa-vote-yea',
            'APROVADO' => 'fas fa-check-circle',
            'REJEITADO' => 'fas fa-times-circle',
            default => 'fas fa-info-circle'
        };
    }

    /**
     * Criar um novo log de tramitação
     */
    public static function criarLog(
        int $proposicaoId,
        string $acao,
        int $userId,
        ?string $statusAnterior = null,
        ?string $statusNovo = null,
        ?string $observacoes = null,
        ?array $dadosAdicionais = null
    ): self {
        return self::create([
            'proposicao_id' => $proposicaoId,
            'acao' => $acao,
            'user_id' => $userId,
            'status_anterior' => $statusAnterior,
            'status_novo' => $statusNovo,
            'observacoes' => $observacoes,
            'dados_adicionais' => $dadosAdicionais
        ]);
    }
}