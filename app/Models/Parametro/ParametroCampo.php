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
        if (is_array($this->opcoes)) {
            return $this->opcoes;
        }
        
        if (is_string($this->opcoes)) {
            $decoded = json_decode($this->opcoes, true);
            return (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
        }
        
        return [];
    }

    public function getValidacaoFormatadaAttribute(): array
    {
        if (is_array($this->validacao)) {
            return $this->validacao;
        }
        
        if (is_string($this->validacao)) {
            $decoded = json_decode($this->validacao, true);
            return (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
        }
        
        return [];
    }

    public function getValorAtualAttribute(): mixed
    {
        try {
            // Se valores já estão carregados, usar o relacionamento loaded
            if ($this->relationLoaded('valores')) {
                $valor = $this->valores
                    ->where('valido_ate', null)
                    ->sortByDesc('created_at')
                    ->first();
                
                if (!$valor) {
                    $valor = $this->valores
                        ->where('valido_ate', '>', now())
                        ->sortByDesc('created_at')
                        ->first();
                }
            } else {
                // Fazer query se não estiver carregado
                $valor = $this->valores()
                    ->where(function($query) {
                        $query->whereNull('valido_ate')
                              ->orWhere('valido_ate', '>', now());
                    })
                    ->latest()
                    ->first();
            }
            
            return $valor ? $valor->valor_formatado : $this->valor_padrao;
            
        } catch (\Exception $e) {
            // Log::warning('Erro ao acessar valor atual do campo', [
                //     'campo_id' => $this->id,
                //     'campo_nome' => $this->nome,
                //     'error' => $e->getMessage()
            // ]);
            return $this->valor_padrao;
        }
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
        try {
            if ($this->relationLoaded('valores')) {
                return $this->valores->isNotEmpty();
            }
            return $this->valores()->exists();
        } catch (\Exception $e) {
            return false;
        }
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
            try {
                // Garantir que validacao é um array
                $validacaoArray = [];
                
                if (is_array($this->validacao)) {
                    $validacaoArray = $this->validacao;
                } elseif (is_string($this->validacao)) {
                    // Se for string, tentar decodificar JSON
                    $decoded = json_decode($this->validacao, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $validacaoArray = $decoded;
                    } else {
                        // Se não for JSON válido, tratar como regra única
                        $validacaoArray = [$this->validacao];
                    }
                }
                
                if (!empty($validacaoArray) && is_array($validacaoArray)) {
                    $rules = array_merge($rules, $validacaoArray);
                }
            } catch (\Exception $e) {
                // Em caso de erro, ignorar validações personalizadas
                // Log::warning("Erro ao processar validações personalizadas para campo {$this->nome}: " . $e->getMessage());
            }
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

    /**
     * Validar valor do campo
     */
    public function validarValor($valor): array
    {
        $errors = [];

        // Verificar se é obrigatório
        if ($this->obrigatorio && (is_null($valor) || $valor === '')) {
            $errors[] = "O campo {$this->label} é obrigatório";
            return $errors; // Se obrigatório e vazio, não precisa validar mais
        }

        // Se valor está vazio e não é obrigatório, não validar
        if (is_null($valor) || $valor === '') {
            return $errors;
        }

        // Validações por tipo de campo
        switch ($this->tipo_campo) {
            case 'email':
                if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "O campo {$this->label} deve ser um e-mail válido";
                }
                break;

            case 'number':
                if (!is_numeric($valor)) {
                    $errors[] = "O campo {$this->label} deve ser numérico";
                }
                break;

            case 'select':
                $opcoes = $this->opcoes_formatada;
                if (!empty($opcoes) && !array_key_exists($valor, $opcoes)) {
                    $errors[] = "O valor selecionado para {$this->label} não é válido";
                }
                break;

            case 'checkbox':
                // Checkbox só aceita 1, 0, true, false, "1", "0"
                if (!in_array($valor, [1, 0, true, false, '1', '0'], true)) {
                    $errors[] = "O campo {$this->label} deve ser verdadeiro ou falso";
                }
                break;

            case 'file':
                // Para arquivos, apenas verificar se é uma string (path)
                if (!is_string($valor)) {
                    $errors[] = "O campo {$this->label} deve ser um arquivo válido";
                }
                break;
        }

        // Validações customizadas do campo
        if ($this->validacao) {
            $validacoes = $this->validacao_formatada;
            
            foreach ($validacoes as $regra => $parametro) {
                switch ($regra) {
                    case 'min':
                        if (strlen($valor) < $parametro) {
                            $errors[] = "O campo {$this->label} deve ter no mínimo {$parametro} caracteres";
                        }
                        break;

                    case 'max':
                        if (strlen($valor) > $parametro) {
                            $errors[] = "O campo {$this->label} deve ter no máximo {$parametro} caracteres";
                        }
                        break;

                    case 'min_value':
                        if (is_numeric($valor) && $valor < $parametro) {
                            $errors[] = "O campo {$this->label} deve ser maior ou igual a {$parametro}";
                        }
                        break;

                    case 'max_value':
                        if (is_numeric($valor) && $valor > $parametro) {
                            $errors[] = "O campo {$this->label} deve ser menor ou igual a {$parametro}";
                        }
                        break;

                    case 'regex':
                        if (!preg_match($parametro, $valor)) {
                            $errors[] = "O formato do campo {$this->label} não é válido";
                        }
                        break;
                }
            }
        }

        return $errors;
    }

    /**
     * Definir valor do campo
     */
    public function setValor($valor, $userId = null)
    {
        // Determinar tipo do valor
        $tipoValor = 'string';
        $valorFormatado = $valor;

        if (is_array($valor) || is_object($valor)) {
            $valorFormatado = json_encode($valor);
            $tipoValor = 'json';
        } elseif (is_bool($valor)) {
            $valorFormatado = $valor ? '1' : '0';
            $tipoValor = 'boolean';
        } elseif ($this->tipo_campo === 'checkbox') {
            $valorFormatado = $valor ? '1' : '0';
            $tipoValor = 'boolean';
        } elseif (is_int($valor)) {
            $tipoValor = 'integer';
        } elseif (is_float($valor)) {
            $tipoValor = 'decimal';
        }

        // Criar ou atualizar valor
        return $this->valores()->updateOrCreate(
            ['campo_id' => $this->id],
            [
                'valor' => $valorFormatado,
                'tipo_valor' => $tipoValor,
                'user_id' => $userId ?? auth()->id()
            ]
        );
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