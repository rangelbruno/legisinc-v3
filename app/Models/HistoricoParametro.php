<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoricoParametro extends Model
{
    use HasFactory;

    protected $table = 'historico_parametros';

    protected $fillable = [
        'parametro_id',
        'user_id',
        'acao',
        'valor_anterior',
        'valor_novo',
        'dados_contexto',
        'ip_address',
        'user_agent',
        'data_acao'
    ];

    protected $casts = [
        'dados_contexto' => 'array',
        'data_acao' => 'datetime',
        'parametro_id' => 'integer',
        'user_id' => 'integer'
    ];

    public const ACOES = [
        'create' => 'Criação',
        'update' => 'Atualização',
        'delete' => 'Exclusão'
    ];

    // Relacionamentos
    public function parametro(): BelongsTo
    {
        return $this->belongsTo(Parametro::class, 'parametro_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Accessors
    public function getAcaoFormatadaAttribute(): string
    {
        return self::ACOES[$this->acao] ?? $this->acao;
    }

    public function getDataFormatadaAttribute(): string
    {
        return $this->data_acao->format('d/m/Y H:i:s');
    }

    public function getValorAnteriorDisplayAttribute(): string
    {
        return $this->valor_anterior ?? '-';
    }

    public function getValorNovoDisplayAttribute(): string
    {
        return $this->valor_novo ?? '-';
    }

    public function getUsuarioNomeAttribute(): string
    {
        return $this->user->name ?? 'Usuário não encontrado';
    }

    public function getContextoFormatadoAttribute(): array
    {
        return [
            'IP' => $this->ip_address ?? '-',
            'Navegador' => $this->getUserAgentFormatado(),
            'URL' => $this->dados_contexto['url'] ?? '-',
            'Método' => $this->dados_contexto['method'] ?? '-',
        ];
    }

    // Scopes
    public function scopePorParametro($query, int $parametroId)
    {
        return $query->where('parametro_id', $parametroId);
    }

    public function scopePorUsuario($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePorAcao($query, string $acao)
    {
        return $query->where('acao', $acao);
    }

    public function scopePorPeriodo($query, \DateTime $inicio, \DateTime $fim)
    {
        return $query->whereBetween('data_acao', [$inicio, $fim]);
    }

    public function scopeRecentes($query, int $dias = 30)
    {
        return $query->where('data_acao', '>=', now()->subDays($dias));
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('data_acao', 'desc');
    }

    // Métodos
    public function getUserAgentFormatado(): string
    {
        if (!$this->user_agent) {
            return '-';
        }

        // Detectar browser
        if (str_contains($this->user_agent, 'Chrome')) {
            return 'Chrome';
        } elseif (str_contains($this->user_agent, 'Firefox')) {
            return 'Firefox';
        } elseif (str_contains($this->user_agent, 'Safari')) {
            return 'Safari';
        } elseif (str_contains($this->user_agent, 'Edge')) {
            return 'Edge';
        } else {
            return 'Outro';
        }
    }

    public function isCriacao(): bool
    {
        return $this->acao === 'create';
    }

    public function isAtualizacao(): bool
    {
        return $this->acao === 'update';
    }

    public function isExclusao(): bool
    {
        return $this->acao === 'delete';
    }

    public function getResumoAlteracao(): string
    {
        switch ($this->acao) {
            case 'create':
                return "Parâmetro criado com valor: {$this->valor_novo_display}";
                
            case 'update':
                return "Valor alterado de '{$this->valor_anterior_display}' para '{$this->valor_novo_display}'";
                
            case 'delete':
                return "Parâmetro excluído (valor era: {$this->valor_anterior_display})";
                
            default:
                return "Ação: {$this->acao_formatada}";
        }
    }

    public function getIconeAcao(): string
    {
        return match ($this->acao) {
            'create' => 'ki-plus',
            'update' => 'ki-pencil',
            'delete' => 'ki-trash',
            default => 'ki-information'
        };
    }

    public function getCorAcao(): string
    {
        return match ($this->acao) {
            'create' => 'success',
            'update' => 'warning',
            'delete' => 'danger',
            default => 'info'
        };
    }

    public function getTempoRelativo(): string
    {
        $diff = $this->data_acao->diffForHumans();
        return $diff;
    }

    public function getDetalhesCompletos(): array
    {
        return [
            'id' => $this->id,
            'parametro' => $this->parametro->nome ?? 'Parâmetro não encontrado',
            'usuario' => $this->usuario_nome,
            'acao' => $this->acao_formatada,
            'valor_anterior' => $this->valor_anterior_display,
            'valor_novo' => $this->valor_novo_display,
            'data' => $this->data_formatada,
            'tempo_relativo' => $this->tempo_relativo,
            'ip' => $this->ip_address,
            'navegador' => $this->getUserAgentFormatado(),
            'contexto' => $this->contexto_formatado,
            'resumo' => $this->resumo_alteracao,
            'icone' => $this->icone_acao,
            'cor' => $this->cor_acao
        ];
    }
}