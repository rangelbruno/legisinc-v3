<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ProposicaoHistorico extends Model
{
    protected $table = 'proposicoes_historico';
    
    protected $fillable = [
        'proposicao_id',
        'usuario_id', 
        'acao',
        'tipo_alteracao',
        'status_anterior',
        'status_novo',
        'arquivo_path_anterior',
        'arquivo_path_novo',
        'conteudo_anterior',
        'conteudo_novo',
        'metadados',
        'origem',
        'observacoes',
        'diff_conteudo',
        'tamanho_anterior',
        'tamanho_novo',
        'data_alteracao',
        'ip_usuario',
        'user_agent'
    ];

    protected $casts = [
        'metadados' => 'array',
        'diff_conteudo' => 'array',
        'data_alteracao' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relacionamentos
    public function proposicao(): BelongsTo
    {
        return $this->belongsTo(Proposicao::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Métodos estáticos para criação de histórico
    public static function criarHistorico(
        int $proposicaoId,
        string $acao,
        array $dados = [],
        ?int $usuarioId = null,
        string $origem = 'onlyoffice'
    ): self {
        $dadosHistorico = [
            'proposicao_id' => $proposicaoId,
            'usuario_id' => $usuarioId ?? auth()->id(),
            'acao' => $acao,
            'origem' => $origem,
            'data_alteracao' => now(),
            'ip_usuario' => request()->ip(),
            'user_agent' => request()->userAgent()
        ];

        // Merge com dados específicos passados
        $dadosHistorico = array_merge($dadosHistorico, $dados);

        return static::create($dadosHistorico);
    }

    // Método para criar diff inteligente de conteúdo
    public static function calcularDiff(?string $anterior, ?string $novo): ?array
    {
        if (empty($anterior) && empty($novo)) {
            return null;
        }

        if (empty($anterior)) {
            return [
                'tipo' => 'criacao',
                'caracteres_adicionados' => strlen($novo ?? ''),
                'linhas_adicionadas' => substr_count($novo ?? '', "\n") + 1
            ];
        }

        if (empty($novo)) {
            return [
                'tipo' => 'remocao',
                'caracteres_removidos' => strlen($anterior),
                'linhas_removidas' => substr_count($anterior, "\n") + 1
            ];
        }

        // Diff simples baseado em caracteres
        $anteriorLength = strlen($anterior);
        $novoLength = strlen($novo);
        
        $diff = [
            'tipo' => 'edicao',
            'caracteres_anterior' => $anteriorLength,
            'caracteres_novo' => $novoLength,
            'diferenca_caracteres' => $novoLength - $anteriorLength,
            'similarity_percent' => round((1 - (levenshtein(
                substr($anterior, 0, 255), 
                substr($novo, 0, 255)
            ) / max(strlen(substr($anterior, 0, 255)), strlen(substr($novo, 0, 255))))) * 100, 2)
        ];

        // Detectar mudanças significativas
        if (abs($diff['diferenca_caracteres']) > 100) {
            $diff['mudanca_significativa'] = true;
        }

        return $diff;
    }

    // Método para registrar callback do OnlyOffice
    public static function registrarCallbackOnlyOffice(
        Proposicao $proposicao,
        ?string $arquivoAnterior,
        string $arquivoNovo,
        ?string $conteudoAnterior,
        ?string $conteudoNovo,
        array $callbackData = []
    ): self {
        $diff = self::calcularDiff($conteudoAnterior, $conteudoNovo);
        
        return self::criarHistorico(
            $proposicao->id,
            'callback_onlyoffice',
            [
                'tipo_alteracao' => 'arquivo',
                'arquivo_path_anterior' => $arquivoAnterior,
                'arquivo_path_novo' => $arquivoNovo,
                'conteudo_anterior' => strlen($conteudoAnterior ?? '') > 1000 ? 
                    substr($conteudoAnterior, 0, 1000) . '...' : $conteudoAnterior,
                'conteudo_novo' => strlen($conteudoNovo ?? '') > 1000 ? 
                    substr($conteudoNovo, 0, 1000) . '...' : $conteudoNovo,
                'diff_conteudo' => $diff,
                'tamanho_anterior' => strlen($conteudoAnterior ?? ''),
                'tamanho_novo' => strlen($conteudoNovo ?? ''),
                'metadados' => [
                    'callback_data' => $callbackData,
                    'file_type' => pathinfo($arquivoNovo, PATHINFO_EXTENSION),
                    'timestamp_callback' => now()->toISOString()
                ]
            ],
            $proposicao->modificado_por,
            'onlyoffice'
        );
    }

    // Scopes para consultas comuns
    public function scopePorProposicao($query, int $proposicaoId)
    {
        return $query->where('proposicao_id', $proposicaoId);
    }

    public function scopePorUsuario($query, int $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopePorPeriodo($query, Carbon $inicio, Carbon $fim)
    {
        return $query->whereBetween('data_alteracao', [$inicio, $fim]);
    }

    public function scopeApenasOnlyOffice($query)
    {
        return $query->where('origem', 'onlyoffice');
    }

    // Accessor para resumo da alteração
    public function getResumoAttribute(): string
    {
        $usuario = $this->usuario?->name ?? 'Sistema';
        $data = $this->data_alteracao->format('d/m/Y H:i');
        
        switch ($this->acao) {
            case 'callback_onlyoffice':
                $tamanho = $this->tamanho_novo - ($this->tamanho_anterior ?? 0);
                $sinal = $tamanho > 0 ? '+' : '';
                return "{$usuario} editou via OnlyOffice ({$sinal}{$tamanho} chars) - {$data}";
                
            case 'status_change':
                return "{$usuario} alterou status de '{$this->status_anterior}' para '{$this->status_novo}' - {$data}";
                
            default:
                return "{$usuario} {$this->acao} - {$data}";
        }
    }
}
