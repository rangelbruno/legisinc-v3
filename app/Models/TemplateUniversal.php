<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateUniversal extends Model
{
    protected $table = 'template_universal';

    protected $fillable = [
        'nome',
        'descricao',
        'document_key',
        'arquivo_path',
        'conteudo',
        'formato',
        'variaveis',
        'ativo',
        'updated_by',
        'is_default',
    ];

    protected $casts = [
        'variaveis' => 'array',
        'ativo' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Relacionamentos sempre carregados por padrão
     */
    protected $with = ['updatedBy'];

    // Relacionamentos
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Métodos úteis
    public function getNomeTemplate(): string
    {
        return 'Template Universal: '.$this->nome;
    }

    public function getUrlEditor(): string
    {
        return route('admin.templates.universal.editor', $this);
    }

    public function getUrlDownload(): string
    {
        // Para OnlyOffice em container, usar nome do container da aplicação
        $baseUrl = config('app.url');

        // Se for localhost, trocar para nome do container que o OnlyOffice consegue acessar
        if (str_contains($baseUrl, 'localhost')) {
            $baseUrl = str_replace('localhost:8001', 'legisinc-app', $baseUrl);
        }

        return $baseUrl.'/api/templates/universal/'.$this->id.'/download';
    }

    /**
     * Verificar se este é o template padrão do sistema
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }

    /**
     * Definir como template padrão (remove is_default dos outros)
     */
    public function setAsDefault(): void
    {
        // Remove is_default de todos os outros templates
        static::where('id', '!=', $this->id)->update(['is_default' => false]);

        // Define este como padrão
        $this->update(['is_default' => true]);
    }

    /**
     * Obter o template padrão ativo
     */
    public static function getDefault(): ?self
    {
        return static::where('ativo', true)
            ->where('is_default', true)
            ->first();
    }

    /**
     * Obter ou criar template padrão
     */
    public static function getOrCreateDefault(): self
    {
        $default = static::getDefault();

        if (! $default) {
            $default = static::create([
                'nome' => 'Template Universal Padrão',
                'descricao' => 'Template universal que se adapta a qualquer tipo de proposição',
                'document_key' => 'template_universal_default_'.time(),
                'ativo' => true,
                'is_default' => true,
                'updated_by' => auth()->id() ?? 1, // Fallback para ID 1 se não logado
                'formato' => 'rtf',
            ]);
        }

        return $default;
    }

    /**
     * Aplicar template a um tipo de proposição específico
     */
    public function aplicarParaTipo(TipoProposicao $tipo, array $dadosPersonalizados = []): string
    {
        $conteudo = $this->conteudo;

        // Variáveis específicas do tipo de proposição
        $variaveisTipo = [
            '${tipo_proposicao}' => strtoupper($tipo->nome),
            '${codigo_tipo}' => $tipo->codigo,
            '${categoria_tipo}' => $tipo->categoria ?? 'GERAL',
        ];

        // Mesclar com dados personalizados
        $todasVariaveis = array_merge($variaveisTipo, $dadosPersonalizados);

        // Substituir variáveis no conteúdo
        foreach ($todasVariaveis as $variavel => $valor) {
            $conteudo = str_replace($variavel, $valor, $conteudo);
        }

        return $conteudo;
    }
}
