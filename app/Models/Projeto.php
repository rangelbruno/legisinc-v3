<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Projeto extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descricao',
        'tipo',
        'autor_id',
        'conteudo',
        'status',
        'numero_protocolo',
        'data_assinatura',
        'observacoes',
        'numero',
        'ano',
        'relator_id',
        'comissao_id',
        'urgencia',
        'resumo',
        'ementa',
        'version_atual',
        'palavras_chave',
        'data_protocolo',
        'data_limite_tramitacao',
        'ativo',
        'metadados',
    ];

    protected $casts = [
        'data_protocolo' => 'datetime',
        'data_assinatura' => 'datetime',
        'data_limite_tramitacao' => 'date',
        'metadados' => 'array',
        'ativo' => 'boolean',
        'version_atual' => 'integer',
    ];

    // Constantes para os tipos
    public const TIPOS = [
        'projeto_lei_ordinaria' => 'Projeto de Lei Ordinária',
        'projeto_lei_complementar' => 'Projeto de Lei Complementar',
        'emenda_constitucional' => 'Emenda Constitucional',
        'decreto_legislativo' => 'Decreto Legislativo',
        'resolucao' => 'Resolução',
        'indicacao' => 'Indicação',
        'requerimento' => 'Requerimento',
    ];

    // Constantes para status conforme fluxo de tramitação
    public const STATUS = [
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

    // Constantes para urgência
    public const URGENCIA = [
        'normal' => 'Normal',
        'urgente' => 'Urgente',
        'urgentissima' => 'Urgentíssima',
    ];

    // Relacionamentos
    // Note: tipo is now an enum field, not a foreign key
    // public function tipoProjeto(): BelongsTo
    // {
    //     return $this->belongsTo(TipoProjeto::class, 'tipo');
    // }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    public function relator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'relator_id');
    }

    // public function comissao(): BelongsTo
    // {
    //     return $this->belongsTo(Comissao::class);
    // }

    public function versions(): HasMany
    {
        return $this->hasMany(ProjetoVersion::class)->orderBy('version_number', 'desc');
    }

    public function versionAtual(): HasOne
    {
        return $this->hasOne(ProjetoVersion::class)->where('is_current', true);
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(ProjetoAnexo::class)->where('ativo', true)->orderBy('ordem');
    }

    public function tramitacao(): HasMany
    {
        return $this->hasMany(ProjetoTramitacao::class)->orderBy('created_at');
    }

    public function tramitacaoAtual(): HasOne
    {
        return $this->hasOne(ProjetoTramitacao::class)
            ->latest();
    }

    // Accessors
    public function getTipoFormatadoAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function getStatusFormatadoAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getUrgenciaFormatadaAttribute(): string
    {
        return self::URGENCIA[$this->urgencia] ?? $this->urgencia;
    }

    public function getNumeroCompletoAttribute(): string
    {
        return "{$this->numero}/{$this->ano}";
    }

    public function getStatusCorAttribute(): string
    {
        $cores = [
            'rascunho' => 'secondary',
            'enviado' => 'info',
            'em_analise' => 'primary',
            'aprovado' => 'success',
            'rejeitado' => 'danger',
            'assinado' => 'warning',
            'protocolado' => 'info',
            'em_sessao' => 'warning',
            'votado' => 'success',
        ];

        return $cores[$this->status] ?? 'secondary';
    }

    public function getUrgenciaCorAttribute(): string
    {
        $cores = [
            'normal' => 'success',
            'urgente' => 'warning',
            'urgentissima' => 'danger',
        ];

        return $cores[$this->urgencia] ?? 'success';
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePorAutor($query, $autorId)
    {
        return $query->where('autor_id', $autorId);
    }

    public function scopePorComissao($query, $comissaoId)
    {
        return $query->where('comissao_id', $comissaoId);
    }

    public function scopeUrgentes($query)
    {
        return $query->whereIn('urgencia', ['urgente', 'urgentissima']);
    }

    public function scopeVisiveisPorUsuario($query, $user)
    {
        if (!$user) {
            return $query->whereIn('status', ['aprovado', 'rejeitado', 'arquivado']);
        }

        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isParlamentar()) {
            return $query->where(function ($q) use ($user) {
                $q->where('autor_id', $user->id)
                  ->orWhereIn('status', ['aprovado', 'rejeitado', 'arquivado']);
            });
        }

        return $query->whereIn('status', ['aprovado', 'rejeitado', 'arquivado']);
    }

    // Métodos de negócio conforme novo fluxo
    public function isRascunho(): bool
    {
        return $this->status === 'rascunho';
    }

    public function isEnviado(): bool
    {
        return $this->status === 'enviado';
    }

    public function isEmAnalise(): bool
    {
        return $this->status === 'em_analise';
    }

    public function isAprovado(): bool
    {
        return $this->status === 'aprovado';
    }

    public function isRejeitado(): bool
    {
        return $this->status === 'rejeitado';
    }

    public function isAssinado(): bool
    {
        return $this->status === 'assinado';
    }

    public function isProtocolado(): bool
    {
        return $this->status === 'protocolado';
    }

    public function isEmSessao(): bool
    {
        return $this->status === 'em_sessao';
    }

    public function isVotado(): bool
    {
        return $this->status === 'votado';
    }

    public function hasContent(): bool
    {
        return !empty($this->conteudo);
    }

    public function podeEditarConteudo(): bool
    {
        return in_array($this->status, ['rascunho']);
    }

    public function podeAnexarArquivos(): bool
    {
        return in_array($this->status, ['rascunho', 'enviado', 'em_analise']);
    }

    public function podeSerEnviado(): bool
    {
        return $this->status === 'rascunho' && $this->hasContent();
    }

    public function podeSerAssinado(): bool
    {
        return $this->status === 'aprovado';
    }

    public function podeSerProtocolado(): bool
    {
        return $this->status === 'assinado';
    }

    public function proximaEtapaTramitacao(): ?string
    {
        $fluxo = [
            'rascunho' => 'envio_para_analise',
            'enviado' => 'analise_legislativo',
            'em_analise' => 'aprovacao_ou_rejeicao',
            'aprovado' => 'assinatura',
            'assinado' => 'protocolo',
            'protocolado' => 'inclusao_sessao',
            'em_sessao' => 'votacao',
        ];

        return $fluxo[$this->status] ?? null;
    }

    public function criarNovaVersao(string $conteudo, string $changelog = null, string $tipoAlteracao = 'revisao'): ProjetoVersion
    {
        // Desativar versão atual
        $this->versions()->update(['is_current' => false]);

        // Criar nova versão
        $novaVersao = $this->versions()->create([
            'version_number' => $this->version_atual + 1,
            'conteudo' => $conteudo,
            'changelog' => $changelog,
            'tipo_alteracao' => $tipoAlteracao,
            'author_id' => auth()->id(),
            'is_current' => true,
            'tamanho_bytes' => strlen($conteudo),
        ]);

        // Atualizar projeto
        $this->update([
            'version_atual' => $novaVersao->version_number,
            'conteudo' => $conteudo,
        ]);

        return $novaVersao;
    }

    public function adicionarTramitacao(string $acao, string $statusAnterior = null, string $statusAtual = null, string $observacoes = null): ProjetoTramitacao
    {
        return $this->tramitacao()->create([
            'usuario_id' => auth()->id(),
            'status_anterior' => $statusAnterior ?? $this->status,
            'status_atual' => $statusAtual ?? $this->status,
            'acao' => $acao,
            'observacoes' => $observacoes,
        ]);
    }
}