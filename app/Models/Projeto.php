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
        'numero',
        'ano',
        'tipo',
        'autor_id',
        'relator_id',
        'comissao_id',
        'status',
        'urgencia',
        'resumo',
        'ementa',
        'conteudo',
        'version_atual',
        'palavras_chave',
        'observacoes',
        'data_protocolo',
        'data_limite_tramitacao',
        'ativo',
        'metadados',
    ];

    protected $casts = [
        'data_protocolo' => 'date',
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

    // Constantes para status
    public const STATUS = [
        'rascunho' => 'Rascunho',
        'protocolado' => 'Protocolado',
        'em_tramitacao' => 'Em Tramitação',
        'na_comissao' => 'Na Comissão',
        'em_votacao' => 'Em Votação',
        'aprovado' => 'Aprovado',
        'rejeitado' => 'Rejeitado',
        'retirado' => 'Retirado',
        'arquivado' => 'Arquivado',
    ];

    // Constantes para urgência
    public const URGENCIA = [
        'normal' => 'Normal',
        'urgente' => 'Urgente',
        'urgentissima' => 'Urgentíssima',
    ];

    // Relacionamentos
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
        return $this->hasMany(ProjetoTramitacao::class)->orderBy('ordem');
    }

    public function tramitacaoAtual(): HasOne
    {
        return $this->hasOne(ProjetoTramitacao::class)
            ->where('status', '!=', 'concluido')
            ->orderBy('ordem', 'desc');
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
            'protocolado' => 'info',
            'em_tramitacao' => 'primary',
            'na_comissao' => 'warning',
            'em_votacao' => 'warning',
            'aprovado' => 'success',
            'rejeitado' => 'danger',
            'retirado' => 'dark',
            'arquivado' => 'light',
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

    // Métodos de negócio
    public function isRascunho(): bool
    {
        return $this->status === 'rascunho';
    }

    public function isProtocolado(): bool
    {
        return $this->status === 'protocolado';
    }

    public function isAprovado(): bool
    {
        return $this->status === 'aprovado';
    }

    public function isRejeitado(): bool
    {
        return $this->status === 'rejeitado';
    }

    public function isArquivado(): bool
    {
        return $this->status === 'arquivado';
    }

    public function isVotado(): bool
    {
        return in_array($this->status, ['aprovado', 'rejeitado', 'arquivado']);
    }

    public function hasContent(): bool
    {
        return !empty($this->conteudo);
    }

    public function podeEditarConteudo(): bool
    {
        return in_array($this->status, ['rascunho', 'em_tramitacao']);
    }

    public function podeAnexarArquivos(): bool
    {
        return in_array($this->status, ['rascunho', 'em_tramitacao', 'na_comissao']);
    }

    public function estaNaComissao(): bool
    {
        return $this->status === 'na_comissao' && $this->comissao_id;
    }

    public function proximaEtapaTramitacao(): ?string
    {
        $fluxo = [
            'rascunho' => 'protocolo',
            'protocolado' => 'distribuicao',
            'em_tramitacao' => 'analise_comissao',
            'na_comissao' => 'relatoria',
            'em_votacao' => 'votacao_plenario',
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

    public function adicionarTramitacao(array $dados): ProjetoTramitacao
    {
        $ordem = $this->tramitacao()->max('ordem') + 1;

        return $this->tramitacao()->create(array_merge($dados, [
            'ordem' => $ordem,
            'data_inicio' => now(),
        ]));
    }
}