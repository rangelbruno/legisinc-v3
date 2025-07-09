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
        'etapa',
        'acao',
        'responsavel_id',
        'comissao_id',
        'orgao_destino',
        'observacoes',
        'despacho',
        'prazo',
        'data_inicio',
        'data_fim',
        'dias_tramitacao',
        'status',
        'urgente',
        'ordem',
        'dados_complementares',
    ];

    protected $casts = [
        'prazo' => 'date',
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
        'urgente' => 'boolean',
        'dados_complementares' => 'array',
        'dias_tramitacao' => 'integer',
        'ordem' => 'integer',
    ];

    // Constantes
    public const ETAPAS = [
        'protocolo' => 'Protocolo',
        'distribuicao' => 'Distribuição',
        'analise_comissao' => 'Análise da Comissão',
        'relatoria' => 'Relatoria',
        'parecer' => 'Parecer',
        'emenda' => 'Emenda',
        'votacao_comissao' => 'Votação na Comissão',
        'plenario' => 'Plenário',
        'votacao_plenario' => 'Votação no Plenário',
        'sancao' => 'Sanção',
        'promulgacao' => 'Promulgação',
        'publicacao' => 'Publicação',
        'arquivamento' => 'Arquivamento',
    ];

    public const ACOES = [
        'criado' => 'Criado',
        'enviado' => 'Enviado',
        'recebido' => 'Recebido',
        'analisado' => 'Analisado',
        'aprovado' => 'Aprovado',
        'rejeitado' => 'Rejeitado',
        'emendado' => 'Emendado',
        'devolvido' => 'Devolvido',
        'arquivado' => 'Arquivado',
        'desarquivado' => 'Desarquivado',
    ];

    public const STATUS = [
        'pendente' => 'Pendente',
        'em_andamento' => 'Em Andamento',
        'concluido' => 'Concluído',
        'cancelado' => 'Cancelado',
    ];

    // Relacionamentos
    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class);
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function comissao(): BelongsTo
    {
        return $this->belongsTo(Comissao::class);
    }

    // Accessors
    public function getEtapaFormatadaAttribute(): string
    {
        return self::ETAPAS[$this->etapa] ?? $this->etapa;
    }

    public function getAcaoFormatadaAttribute(): string
    {
        return self::ACOES[$this->acao] ?? $this->acao;
    }

    public function getStatusFormatadoAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getStatusCorAttribute(): string
    {
        $cores = [
            'pendente' => 'warning',
            'em_andamento' => 'primary',
            'concluido' => 'success',
            'cancelado' => 'danger',
        ];

        return $cores[$this->status] ?? 'secondary';
    }

    public function getTempoTramitacaoAttribute(): string
    {
        if ($this->data_fim) {
            $dias = $this->data_inicio->diffInDays($this->data_fim);
            return "{$dias} dia" . ($dias !== 1 ? 's' : '');
        }

        if ($this->status === 'em_andamento') {
            $dias = $this->data_inicio->diffInDays(now());
            return "{$dias} dia" . ($dias !== 1 ? 's' : '') . ' (em andamento)';
        }

        return 'Não iniciado';
    }

    public function getEstaDentroDosPrazosAttribute(): bool
    {
        if (!$this->prazo) {
            return true; // Sem prazo definido
        }

        $dataReferencia = $this->data_fim ?? now();
        return $dataReferencia <= $this->prazo;
    }

    public function getDiasRestantesAttribute(): ?int
    {
        if (!$this->prazo || $this->data_fim) {
            return null;
        }

        return now()->diffInDays($this->prazo, false);
    }

    public function getCorPrazoAttribute(): string
    {
        $diasRestantes = $this->dias_restantes;

        if ($diasRestantes === null || $diasRestantes > 5) {
            return 'success';
        }

        if ($diasRestantes > 2) {
            return 'warning';
        }

        return 'danger';
    }

    // Scopes
    public function scopePorProjeto($query, $projetoId)
    {
        return $query->where('projeto_id', $projetoId);
    }

    public function scopePorStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePorEtapa($query, $etapa)
    {
        return $query->where('etapa', $etapa);
    }

    public function scopePorResponsavel($query, $responsavelId)
    {
        return $query->where('responsavel_id', $responsavelId);
    }

    public function scopePorComissao($query, $comissaoId)
    {
        return $query->where('comissao_id', $comissaoId);
    }

    public function scopeUrgentes($query)
    {
        return $query->where('urgente', true);
    }

    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeEmAndamento($query)
    {
        return $query->where('status', 'em_andamento');
    }

    public function scopeConcluidos($query)
    {
        return $query->where('status', 'concluido');
    }

    public function scopeComPrazo($query)
    {
        return $query->whereNotNull('prazo');
    }

    public function scopeForaDoPrazo($query)
    {
        return $query->whereNotNull('prazo')
            ->where(function ($q) {
                $q->whereNull('data_fim')->where('prazo', '<', now())
                  ->orWhere('data_fim', '>', 'prazo');
            });
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('ordem')->orderBy('data_inicio');
    }

    // Métodos de negócio
    public function iniciar(): void
    {
        $this->update([
            'status' => 'em_andamento',
            'data_inicio' => now(),
        ]);
    }

    public function concluir(string $observacoes = null): void
    {
        $dataFim = now();
        $diasTramitacao = $this->data_inicio->diffInDays($dataFim);

        $this->update([
            'status' => 'concluido',
            'data_fim' => $dataFim,
            'dias_tramitacao' => $diasTramitacao,
            'observacoes' => $observacoes ?: $this->observacoes,
        ]);
    }

    public function cancelar(string $motivo = null): void
    {
        $this->update([
            'status' => 'cancelado',
            'observacoes' => $motivo ? "CANCELADO: {$motivo}" : $this->observacoes,
        ]);
    }

    public function prorrogarPrazo(Carbon $novoPrazo, string $justificativa = null): void
    {
        $this->update([
            'prazo' => $novoPrazo,
            'observacoes' => $justificativa 
                ? $this->observacoes . "\n\nPRAZO PRORROGADO: {$justificativa}"
                : $this->observacoes,
        ]);
    }

    public function marcarComoUrgente(string $justificativa = null): void
    {
        $this->update([
            'urgente' => true,
            'observacoes' => $justificativa 
                ? $this->observacoes . "\n\nMARCADO COMO URGENTE: {$justificativa}"
                : $this->observacoes,
        ]);
    }

    public function adicionarObservacao(string $observacao): void
    {
        $timestamp = now()->format('d/m/Y H:i');
        $usuario = auth()->user()->name ?? 'Sistema';
        
        $novaObservacao = "[{$timestamp} - {$usuario}] {$observacao}";
        
        $this->update([
            'observacoes' => $this->observacoes 
                ? $this->observacoes . "\n\n" . $novaObservacao
                : $novaObservacao,
        ]);
    }

    public function proximaEtapa(): ?string
    {
        $fluxoEtapas = [
            'protocolo' => 'distribuicao',
            'distribuicao' => 'analise_comissao',
            'analise_comissao' => 'relatoria',
            'relatoria' => 'parecer',
            'parecer' => 'votacao_comissao',
            'votacao_comissao' => 'plenario',
            'plenario' => 'votacao_plenario',
            'votacao_plenario' => 'sancao',
            'sancao' => 'promulgacao',
            'promulgacao' => 'publicacao',
        ];

        return $fluxoEtapas[$this->etapa] ?? null;
    }

    // Events
    protected static function booted()
    {
        static::creating(function (ProjetoTramitacao $tramitacao) {
            // Definir data de início se não especificada
            if (!$tramitacao->data_inicio) {
                $tramitacao->data_inicio = now();
            }

            // Definir ordem automática se não especificada
            if (!$tramitacao->ordem) {
                $ultimaOrdem = self::where('projeto_id', $tramitacao->projeto_id)->max('ordem') ?? 0;
                $tramitacao->ordem = $ultimaOrdem + 1;
            }
        });

        static::updated(function (ProjetoTramitacao $tramitacao) {
            // Calcular dias de tramitação quando concluído
            if ($tramitacao->status === 'concluido' && $tramitacao->data_fim && !$tramitacao->dias_tramitacao) {
                $tramitacao->update([
                    'dias_tramitacao' => $tramitacao->data_inicio->diffInDays($tramitacao->data_fim)
                ]);
            }
        });
    }
}