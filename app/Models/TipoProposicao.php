<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TipoProposicao extends Model
{
    use HasFactory;

    protected $table = 'tipo_proposicoes';

    protected $fillable = [
        'codigo',
        'nome',
        'descricao',
        'icone',
        'cor',
        'ativo',
        'ordem',
        'configuracoes',
        'template_padrao',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'ordem' => 'integer',
        'configuracoes' => 'array',
    ];

    /**
     * Cores disponíveis para tipos de proposição
     */
    const CORES_DISPONIVEIS = [
        'primary' => 'Azul',
        'success' => 'Verde',
        'info' => 'Ciano',
        'warning' => 'Amarelo',
        'danger' => 'Vermelho',
        'secondary' => 'Cinza',
        'dark' => 'Preto',
        'light' => 'Branco',
    ];

    /**
     * Ícones ki-duotone disponíveis
     */
    const ICONES_DISPONIVEIS = [
        'ki-document' => 'Documento',
        'ki-file-added' => 'Arquivo Adicionado',
        'ki-security-user' => 'Segurança/Constitucional',
        'ki-notepad' => 'Bloco de Notas',
        'ki-verify' => 'Verificado/Resolução',
        'ki-arrow-up-right' => 'Seta/Indicação',
        'ki-questionnaire-tablet' => 'Questionário/Requerimento',
        'ki-message-text' => 'Mensagem/Moção',
        'ki-book' => 'Livro/Lei',
        'ki-profile-circle' => 'Perfil/Pessoa',
        'ki-calendar' => 'Calendário/Prazo',
        'ki-gear' => 'Configuração',
    ];

    /**
     * Scope para tipos ativos
     */
    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para ordenação
     */
    public function scopeOrdenados(Builder $query): Builder
    {
        return $query->orderBy('ordem')->orderBy('nome');
    }

    /**
     * Buscar tipos ativos para dropdown
     */
    public static function getParaDropdown(): array
    {
        try {
            return static::ativos()
                ->ordenados()
                ->pluck('nome', 'codigo')
                ->toArray();
        } catch (\Exception $e) {
            // Fallback caso a tabela não exista ainda
            return static::getTiposPadrao();
        }
    }

    /**
     * Tipos padrão para fallback quando a tabela não existe
     */
    public static function getTiposPadrao(): array
    {
        return [
            'projeto_lei_ordinaria' => 'Projeto de Lei Ordinária',
            'projeto_lei_complementar' => 'Projeto de Lei Complementar',
            'proposta_emenda_constitucional' => 'Proposta de Emenda Constitucional',
            'decreto_legislativo' => 'Projeto de Decreto Legislativo',
            'resolucao' => 'Projeto de Resolução',
            'indicacao' => 'Indicação',
            'requerimento' => 'Requerimento',
            'mocao' => 'Moção',
        ];
    }

    /**
     * Buscar tipo por código
     */
    public static function buscarPorCodigo(string $codigo): ?self
    {
        try {
            return static::where('codigo', $codigo)->first();
        } catch (\Exception $e) {
            // Fallback caso a tabela não exista ainda
            $tiposPadrao = static::getTiposPadrao();
            if (isset($tiposPadrao[$codigo])) {
                // Criar objeto simulado
                $tipo = new static();
                $tipo->codigo = $codigo;
                $tipo->nome = $tiposPadrao[$codigo];
                $tipo->ativo = true;
                $tipo->icone = 'ki-document';
                $tipo->cor = 'primary';
                $tipo->template_padrao = null;
                return $tipo;
            }
            return null;
        }
    }

    /**
     * Verificar se código já existe (para validação)
     */
    public static function codigoExiste(string $codigo, ?int $excluirId = null): bool
    {
        try {
            $query = static::where('codigo', $codigo);
            
            if ($excluirId) {
                $query->where('id', '!=', $excluirId);
            }
            
            return $query->exists();
        } catch (\Exception $e) {
            // Fallback caso a tabela não exista ainda
            return false;
        }
    }

    /**
     * Obter próxima ordem disponível
     */
    public static function proximaOrdem(): int
    {
        try {
            return (static::max('ordem') ?? 0) + 1;
        } catch (\Exception $e) {
            // Fallback caso a tabela não exista ainda
            return 1;
        }
    }

    /**
     * Accessor para configurações como objeto
     */
    public function getConfiguracoesParsedAttribute(): object
    {
        return (object) ($this->configuracoes ?? []);
    }

    /**
     * Accessor para cor do badge
     */
    public function getCorBadgeAttribute(): string
    {
        return match($this->cor) {
            'primary' => 'badge-primary',
            'success' => 'badge-success',
            'info' => 'badge-info',
            'warning' => 'badge-warning',
            'danger' => 'badge-danger',
            'secondary' => 'badge-secondary',
            'dark' => 'badge-dark',
            'light' => 'badge-light',
            default => 'badge-primary',
        };
    }

    /**
     * Accessor para classe do ícone
     */
    public function getIconeClasseAttribute(): string
    {
        return "ki-duotone {$this->icone} fs-2";
    }

    /**
     * Accessor para status formatado
     */
    public function getStatusFormatadoAttribute(): array
    {
        return [
            'texto' => $this->ativo ? 'Ativo' : 'Inativo',
            'classe' => $this->ativo ? 'badge-success' : 'badge-secondary',
            'icone' => $this->ativo ? 'ki-check' : 'ki-cross',
        ];
    }

    /**
     * Mutator para código (sempre lowercase e com underscores)
     */
    public function setCodigoAttribute($value): void
    {
        $this->attributes['codigo'] = strtolower(str_replace([' ', '-'], '_', $value));
    }

    /**
     * Mutator para ordem (garantir que seja inteiro positivo)
     */
    public function setOrdemAttribute($value): void
    {
        $this->attributes['ordem'] = max(0, (int) $value);
    }

    /**
     * Relacionamento com template
     */
    public function template()
    {
        return $this->hasOne(TipoProposicaoTemplate::class);
    }

    /**
     * Relacionamento com templates (plural)
     */
    public function templates()
    {
        return $this->hasMany(TipoProposicaoTemplate::class);
    }

    /**
     * Verificar se tipo possui template ativo
     */
    public function hasTemplate(): bool
    {
        return $this->template && $this->template->ativo;
    }
}