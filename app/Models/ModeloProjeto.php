<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModeloProjeto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'tipo_projeto',
        'conteudo_modelo',
        'campos_variaveis',
        'ativo',
        'criado_por',
    ];

    protected $casts = [
        'campos_variaveis' => 'array',
        'ativo' => 'boolean',
    ];

    // Constantes para os tipos (mesmas do Projeto)
    public const TIPOS_PROJETO = [
        'projeto_lei_ordinaria' => 'Projeto de Lei Ordinária',
        'projeto_lei_complementar' => 'Projeto de Lei Complementar',
        'emenda_constitucional' => 'Emenda Constitucional',
        'decreto_legislativo' => 'Decreto Legislativo',
        'resolucao' => 'Resolução',
        'indicacao' => 'Indicação',
        'requerimento' => 'Requerimento',
    ];

    // Relacionamentos
    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    // Accessors
    public function getTipoProjetoFormatadoAttribute(): string
    {
        return self::TIPOS_PROJETO[$this->tipo_projeto] ?? $this->tipo_projeto;
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_projeto', $tipo);
    }

    // Métodos de negócio
    public function processarConteudo(array $variaveis = []): string
    {
        $conteudo = $this->conteudo_modelo;
        
        // Substituir variáveis no formato {{VARIAVEL}}
        foreach ($variaveis as $chave => $valor) {
            $conteudo = str_replace('{{' . strtoupper($chave) . '}}', $valor, $conteudo);
        }
        
        // Variáveis padrão do sistema
        $variaveisDefault = [
            'DATA_HOJE' => now()->format('d/m/Y'),
            'ANO_ATUAL' => now()->year,
            'MES_ATUAL' => now()->locale('pt_BR')->monthName,
            'DIA_ATUAL' => now()->day,
        ];
        
        foreach ($variaveisDefault as $chave => $valor) {
            $conteudo = str_replace('{{' . $chave . '}}', $valor, $conteudo);
        }
        
        return $conteudo;
    }

    public function getVariaveisDisponiveis(): array
    {
        $variaveis = $this->campos_variaveis ?? [];
        
        // Adicionar variáveis padrão do sistema
        $variaveisDefault = [
            'DATA_HOJE' => 'Data atual (dd/mm/yyyy)',
            'ANO_ATUAL' => 'Ano atual',
            'MES_ATUAL' => 'Mês atual por extenso',
            'DIA_ATUAL' => 'Dia atual',
            'NOME_AUTOR' => 'Nome do autor do projeto',
            'NUMERO_PROJETO' => 'Número do projeto',
            'ANO_PROJETO' => 'Ano do projeto',
        ];
        
        return array_merge($variaveisDefault, $variaveis);
    }

    public function podeSerEditado(): bool
    {
        return auth()->user() && auth()->user()->isAdmin();
    }

    public function podeSerExcluido(): bool
    {
        return auth()->user() && auth()->user()->isAdmin();
    }
}