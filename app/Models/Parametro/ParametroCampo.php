<?php

namespace App\Models\Parametro;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParametroCampo extends Model
{
    use HasFactory;

    protected $table = 'parametros_campos';

    protected $fillable = [
        'submodulo_id',
        'nome',
        'label',
        'tipo_campo',
        'descricao',
        'obrigatorio',
        'valor_padrao',
        'opcoes',
        'validacao',
        'placeholder',
        'classe_css',
        'ordem',
        'ativo',
    ];

    protected $casts = [
        'opcoes' => 'array',
        'validacao' => 'array',
        'obrigatorio' => 'boolean',
        'ativo' => 'boolean',
        'ordem' => 'integer',
        'submodulo_id' => 'integer',
    ];

    // Relacionamentos
    public function submodulo(): BelongsTo
    {
        return $this->belongsTo(ParametroSubmodulo::class, 'submodulo_id');
    }

    public function valores(): HasMany
    {
        return $this->hasMany(ParametroValor::class, 'campo_id');
    }

    public function valorAtual(): HasMany
    {
        return $this->valores()->whereNull('valido_ate')->orWhere('valido_ate', '>', now());
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('ordem')->orderBy('nome');
    }

    public function scopePorSubmodulo($query, int $submoduloId)
    {
        return $query->where('submodulo_id', $submoduloId);
    }

    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo_campo', $tipo);
    }

    public function scopeObrigatorios($query)
    {
        return $query->where('obrigatorio', true);
    }

    // Accessors
    public function getCaminhoCompletoAttribute(): string
    {
        return "{$this->submodulo->caminho_completo} > {$this->label}";
    }

    public function getOpcoesFormatadaAttribute(): array
    {
        return $this->opcoes ?? [];
    }

    public function getValidacaoFormatadaAttribute(): array
    {
        return $this->validacao ?? [];
    }

    public function getValorAtualAttribute(): mixed
    {
        $valor = $this->valorAtual()->latest()->first();
        return $valor ? $valor->valor_formatado : $this->valor_padrao;
    }

    // Métodos
    public function getProximaOrdem(): int
    {
        $ultimaOrdem = static::where('submodulo_id', $this->submodulo_id)
            ->max('ordem') ?? 0;
        return $ultimaOrdem + 1;
    }

    public function hasValor(): bool
    {
        return $this->valorAtual()->exists();
    }

    public function getValidationRules(): array
    {
        $rules = [];
        
        if ($this->obrigatorio) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        switch ($this->tipo_campo) {
            case 'email':
                $rules[] = 'email';
                break;
            case 'number':
                $rules[] = 'numeric';
                break;
            case 'date':
                $rules[] = 'date';
                break;
            case 'datetime':
                $rules[] = 'date';
                break;
            case 'file':
                $rules[] = 'file';
                break;
        }

        // Adicionar validações personalizadas
        if ($this->validacao) {
            $rules = array_merge($rules, $this->validacao);
        }

        return $rules;
    }

    public function isText(): bool
    {
        return $this->tipo_campo === 'text';
    }

    public function isEmail(): bool
    {
        return $this->tipo_campo === 'email';
    }

    public function isNumber(): bool
    {
        return $this->tipo_campo === 'number';
    }

    public function isTextarea(): bool
    {
        return $this->tipo_campo === 'textarea';
    }

    public function isSelect(): bool
    {
        return $this->tipo_campo === 'select';
    }

    public function isCheckbox(): bool
    {
        return $this->tipo_campo === 'checkbox';
    }

    public function isRadio(): bool
    {
        return $this->tipo_campo === 'radio';
    }

    public function isFile(): bool
    {
        return $this->tipo_campo === 'file';
    }

    public function isDate(): bool
    {
        return $this->tipo_campo === 'date';
    }

    public function isDatetime(): bool
    {
        return $this->tipo_campo === 'datetime';
    }

    public function hasOpcoes(): bool
    {
        return in_array($this->tipo_campo, ['select', 'radio', 'checkbox']) && !empty($this->opcoes);
    }

    public function toJsonExtract(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'label' => $this->label,
            'tipo_campo' => $this->tipo_campo,
            'descricao' => $this->descricao,
            'obrigatorio' => $this->obrigatorio,
            'valor_padrao' => $this->valor_padrao,
            'valor_atual' => $this->valor_atual,
            'opcoes' => $this->opcoes_formatada,
            'validacao' => $this->validacao_formatada,
            'placeholder' => $this->placeholder,
            'classe_css' => $this->classe_css,
            'ordem' => $this->ordem,
            'ativo' => $this->ativo,
            'utilizado' => $this->hasValor(),
            'validation_rules' => $this->getValidationRules(),
        ];
    }
}