<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoParametro extends Model
{
    use HasFactory;

    protected $table = 'tipos_parametros';

    protected $fillable = [
        'nome',
        'codigo',
        'classe_validacao',
        'configuracao_padrao',
        'ativo'
    ];

    protected $casts = [
        'configuracao_padrao' => 'array',
        'ativo' => 'boolean'
    ];

    public const TIPOS_SISTEMA = [
        'string' => 'Texto',
        'text' => 'Texto Longo',
        'integer' => 'Número Inteiro',
        'decimal' => 'Número Decimal',
        'boolean' => 'Sim/Não',
        'date' => 'Data',
        'datetime' => 'Data e Hora',
        'time' => 'Hora',
        'email' => 'Email',
        'url' => 'URL',
        'json' => 'JSON',
        'array' => 'Array',
        'file' => 'Arquivo',
        'image' => 'Imagem',
        'color' => 'Cor',
        'enum' => 'Lista de Opções',
        'password' => 'Senha'
    ];

    public const CONFIGURACOES_PADRAO = [
        'string' => [
            'max_length' => 255,
            'min_length' => 0,
            'regex' => null
        ],
        'text' => [
            'max_length' => 65535,
            'min_length' => 0
        ],
        'integer' => [
            'min' => null,
            'max' => null,
            'step' => 1
        ],
        'decimal' => [
            'min' => null,
            'max' => null,
            'step' => 0.01,
            'precision' => 2
        ],
        'boolean' => [
            'default_value' => false,
            'true_label' => 'Sim',
            'false_label' => 'Não'
        ],
        'date' => [
            'format' => 'Y-m-d',
            'min_date' => null,
            'max_date' => null
        ],
        'datetime' => [
            'format' => 'Y-m-d H:i:s',
            'min_date' => null,
            'max_date' => null
        ],
        'time' => [
            'format' => 'H:i:s',
            'min_time' => null,
            'max_time' => null
        ],
        'email' => [
            'multiple' => false,
            'domains' => []
        ],
        'url' => [
            'allowed_protocols' => ['http', 'https'],
            'allow_ftp' => false
        ],
        'json' => [
            'validate_structure' => false,
            'required_keys' => []
        ],
        'array' => [
            'separator' => ',',
            'trim_values' => true,
            'remove_empty' => true
        ],
        'file' => [
            'max_size' => 2048, // KB
            'allowed_extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
            'multiple' => false
        ],
        'image' => [
            'max_size' => 2048, // KB
            'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'svg'],
            'max_width' => 2000,
            'max_height' => 2000
        ],
        'color' => [
            'format' => 'hex',
            'allow_alpha' => false
        ],
        'enum' => [
            'options' => [],
            'multiple' => false
        ],
        'password' => [
            'min_length' => 8,
            'require_uppercase' => true,
            'require_lowercase' => true,
            'require_numbers' => true,
            'require_symbols' => false
        ]
    ];

    // Relacionamentos
    public function parametros(): HasMany
    {
        return $this->hasMany(Parametro::class, 'tipo_parametro_id');
    }

    public function parametrosAtivos(): HasMany
    {
        return $this->hasMany(Parametro::class, 'tipo_parametro_id')->where('ativo', true);
    }

    // Accessors
    public function getTipoFormatadoAttribute(): string
    {
        return self::TIPOS_SISTEMA[$this->codigo] ?? $this->codigo;
    }

    public function getConfiguracaoPadraoFormatadaAttribute(): array
    {
        $configuracaoPadrao = self::CONFIGURACOES_PADRAO[$this->codigo] ?? [];
        
        // Mesclar com configuração específica
        if ($this->configuracao_padrao) {
            $configuracaoPadrao = array_merge($configuracaoPadrao, $this->configuracao_padrao);
        }
        
        return $configuracaoPadrao;
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('nome');
    }

    public function scopePorCodigo($query, string $codigo)
    {
        return $query->where('codigo', $codigo);
    }

    // Métodos
    public function getParametrosCount(): int
    {
        return $this->parametros()->count();
    }

    public function getParametrosAtivosCount(): int
    {
        return $this->parametrosAtivos()->count();
    }

    public function isString(): bool
    {
        return in_array($this->codigo, ['string', 'text']);
    }

    public function isNumeric(): bool
    {
        return in_array($this->codigo, ['integer', 'decimal']);
    }

    public function isDate(): bool
    {
        return in_array($this->codigo, ['date', 'datetime', 'time']);
    }

    public function isBoolean(): bool
    {
        return $this->codigo === 'boolean';
    }

    public function isFile(): bool
    {
        return in_array($this->codigo, ['file', 'image']);
    }

    public function isArray(): bool
    {
        return in_array($this->codigo, ['array', 'json']);
    }

    public function getValidationRules(): array
    {
        $rules = [];
        $config = $this->configuracao_padrao_formatada;

        switch ($this->codigo) {
            case 'string':
                $rules[] = 'string';
                if (isset($config['max_length'])) {
                    $rules[] = 'max:' . $config['max_length'];
                }
                if (isset($config['min_length']) && $config['min_length'] > 0) {
                    $rules[] = 'min:' . $config['min_length'];
                }
                break;
                
            case 'integer':
                $rules[] = 'integer';
                if (isset($config['min'])) {
                    $rules[] = 'min:' . $config['min'];
                }
                if (isset($config['max'])) {
                    $rules[] = 'max:' . $config['max'];
                }
                break;
                
            case 'decimal':
                $rules[] = 'numeric';
                if (isset($config['min'])) {
                    $rules[] = 'min:' . $config['min'];
                }
                if (isset($config['max'])) {
                    $rules[] = 'max:' . $config['max'];
                }
                break;
                
            case 'boolean':
                $rules[] = 'boolean';
                break;
                
            case 'date':
                $rules[] = 'date';
                break;
                
            case 'datetime':
                $rules[] = 'date';
                break;
                
            case 'email':
                $rules[] = 'email';
                break;
                
            case 'url':
                $rules[] = 'url';
                break;
                
            case 'json':
                $rules[] = 'json';
                break;
        }

        return $rules;
    }
}