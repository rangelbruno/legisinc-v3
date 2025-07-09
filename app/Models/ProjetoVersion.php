<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjetoVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'projeto_id',
        'version_number',
        'conteudo',
        'changelog',
        'comentarios',
        'author_id',
        'tipo_alteracao',
        'is_current',
        'is_published',
        'diff_data',
        'tamanho_bytes',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'is_published' => 'boolean',
        'diff_data' => 'array',
        'tamanho_bytes' => 'integer',
    ];

    // Constantes
    public const TIPOS_ALTERACAO = [
        'criacao' => 'Criação',
        'revisao' => 'Revisão',
        'emenda' => 'Emenda',
        'correcao' => 'Correção',
        'formatacao' => 'Formatação',
    ];

    // Relacionamentos
    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Accessors
    public function getTipoAlteracaoFormatadoAttribute(): string
    {
        return self::TIPOS_ALTERACAO[$this->tipo_alteracao] ?? $this->tipo_alteracao;
    }

    public function getTamanhoFormatadoAttribute(): string
    {
        if (!$this->tamanho_bytes) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = floor(log($this->tamanho_bytes, 1024));
        
        return round($this->tamanho_bytes / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    public function getStatusAttribute(): string
    {
        if ($this->is_current) {
            return 'Atual';
        }
        
        if ($this->is_published) {
            return 'Publicada';
        }
        
        return 'Histórico';
    }

    // Scopes
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopePorProjeto($query, $projetoId)
    {
        return $query->where('projeto_id', $projetoId);
    }

    // Métodos de negócio
    public function tornarAtual(): void
    {
        // Desativar outras versões atuais
        self::where('projeto_id', $this->projeto_id)
            ->where('id', '!=', $this->id)
            ->update(['is_current' => false]);

        // Ativar esta versão
        $this->update(['is_current' => true]);

        // Atualizar o projeto
        $this->projeto()->update([
            'version_atual' => $this->version_number,
            'conteudo' => $this->conteudo,
        ]);
    }

    public function publicar(): void
    {
        $this->update(['is_published' => true]);
    }

    public function calcularDiferenca(?ProjetoVersion $versaoAnterior = null): array
    {
        if (!$versaoAnterior) {
            $versaoAnterior = self::where('projeto_id', $this->projeto_id)
                ->where('version_number', '<', $this->version_number)
                ->orderBy('version_number', 'desc')
                ->first();
        }

        if (!$versaoAnterior) {
            return [
                'tipo' => 'criacao',
                'linhas_adicionadas' => substr_count($this->conteudo, "\n") + 1,
                'linhas_removidas' => 0,
                'caracteres_adicionados' => strlen($this->conteudo),
                'caracteres_removidos' => 0,
            ];
        }

        $linhasAtuais = explode("\n", $this->conteudo);
        $linhasAnteriores = explode("\n", $versaoAnterior->conteudo);

        return [
            'tipo' => 'alteracao',
            'linhas_adicionadas' => max(0, count($linhasAtuais) - count($linhasAnteriores)),
            'linhas_removidas' => max(0, count($linhasAnteriores) - count($linhasAtuais)),
            'caracteres_adicionados' => max(0, strlen($this->conteudo) - strlen($versaoAnterior->conteudo)),
            'caracteres_removidos' => max(0, strlen($versaoAnterior->conteudo) - strlen($this->conteudo)),
        ];
    }
}