<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parametro extends Model
{
    use HasFactory;

    protected $table = 'parametros';

    protected $fillable = [
        'nome',
        'codigo',
        'descricao',
        'grupo_parametro_id',
        'tipo_parametro_id',
        'valor',
        'valor_padrao',
        'configuracao',
        'regras_validacao',
        'obrigatorio',
        'editavel',
        'visivel',
        'ativo',
        'ordem',
        'help_text'
    ];

    protected $casts = [
        'configuracao' => 'array',
        'regras_validacao' => 'array',
        'obrigatorio' => 'boolean',
        'editavel' => 'boolean',
        'visivel' => 'boolean',
        'ativo' => 'boolean',
        'ordem' => 'integer',
        'grupo_parametro_id' => 'integer',
        'tipo_parametro_id' => 'integer'
    ];

    // Relacionamentos
    public function grupoParametro(): BelongsTo
    {
        return $this->belongsTo(GrupoParametro::class, 'grupo_parametro_id');
    }

    public function tipoParametro(): BelongsTo
    {
        return $this->belongsTo(TipoParametro::class, 'tipo_parametro_id');
    }

    public function historico(): HasMany
    {
        return $this->hasMany(HistoricoParametro::class, 'parametro_id');
    }

    // Accessors
    public function getValorFormatadoAttribute(): mixed
    {
        if (is_null($this->valor)) {
            return $this->getValorPadraoFormatado();
        }

        return $this->formatarValor($this->valor);
    }

    public function getValorPadraoFormatadoAttribute(): mixed
    {
        if (is_null($this->valor_padrao)) {
            return null;
        }

        return $this->formatarValor($this->valor_padrao);
    }

    public function getValorDisplayAttribute(): string
    {
        $valor = $this->valor_formatado;
        
        if (is_null($valor)) {
            return '-';
        }

        switch ($this->tipoParametro->codigo) {
            case 'boolean':
                return $valor ? 'Sim' : 'Não';
                
            case 'date':
                return $valor instanceof \DateTime ? $valor->format('d/m/Y') : $valor;
                
            case 'datetime':
                return $valor instanceof \DateTime ? $valor->format('d/m/Y H:i:s') : $valor;
                
            case 'array':
                return is_array($valor) ? implode(', ', $valor) : $valor;
                
            case 'json':
                return is_array($valor) ? json_encode($valor, JSON_PRETTY_PRINT) : $valor;
                
            default:
                return (string) $valor;
        }
    }

    public function getCaminhoCompletoAttribute(): string
    {
        return $this->grupoParametro->caminho_completo . ' > ' . $this->nome;
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeVisiveis($query)
    {
        return $query->where('visivel', true);
    }

    public function scopeEditaveis($query)
    {
        return $query->where('editavel', true);
    }

    public function scopeObrigatorios($query)
    {
        return $query->where('obrigatorio', true);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('ordem')->orderBy('nome');
    }

    public function scopePorGrupo($query, int $grupoId)
    {
        return $query->where('grupo_parametro_id', $grupoId);
    }

    public function scopePorTipo($query, int $tipoId)
    {
        return $query->where('tipo_parametro_id', $tipoId);
    }

    public function scopePorCodigo($query, string $codigo)
    {
        return $query->where('codigo', $codigo);
    }

    public function scopeBusca($query, string $termo)
    {
        return $query->where(function ($q) use ($termo) {
            $q->where('nome', 'LIKE', "%{$termo}%")
              ->orWhere('codigo', 'LIKE', "%{$termo}%")
              ->orWhere('descricao', 'LIKE', "%{$termo}%");
        });
    }

    // Métodos
    public function formatarValor(mixed $valor): mixed
    {
        if (is_null($valor)) {
            return null;
        }

        switch ($this->tipoParametro->codigo) {
            case 'integer':
                return (int) $valor;
                
            case 'decimal':
                return (float) $valor;
                
            case 'boolean':
                return (bool) $valor;
                
            case 'date':
                return $valor instanceof \DateTime ? $valor : new \DateTime($valor);
                
            case 'datetime':
                return $valor instanceof \DateTime ? $valor : new \DateTime($valor);
                
            case 'array':
                if (is_string($valor)) {
                    $config = $this->tipoParametro->configuracao_padrao_formatada;
                    $separator = $config['separator'] ?? ',';
                    $valores = explode($separator, $valor);
                    
                    if ($config['trim_values'] ?? true) {
                        $valores = array_map('trim', $valores);
                    }
                    
                    if ($config['remove_empty'] ?? true) {
                        $valores = array_filter($valores);
                    }
                    
                    return array_values($valores);
                }
                return is_array($valor) ? $valor : [$valor];
                
            case 'json':
                return is_string($valor) ? json_decode($valor, true) : $valor;
                
            default:
                return (string) $valor;
        }
    }

    public function getValidationRules(): array
    {
        $rules = [];
        
        // Regras básicas do tipo
        $rules = array_merge($rules, $this->tipoParametro->getValidationRules());
        
        // Regras específicas do parâmetro
        if ($this->regras_validacao) {
            $rules = array_merge($rules, $this->regras_validacao);
        }
        
        // Obrigatório
        if ($this->obrigatorio) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }
        
        return $rules;
    }

    public function getProximaOrdem(): int
    {
        $ultimaOrdem = static::where('grupo_parametro_id', $this->grupo_parametro_id)
            ->max('ordem') ?? 0;
        
        return $ultimaOrdem + 1;
    }

    public function hasHistorico(): bool
    {
        return $this->historico()->count() > 0;
    }

    public function getUltimaAlteracao(): ?HistoricoParametro
    {
        return $this->historico()->latest('data_acao')->first();
    }

    public function isEditavel(): bool
    {
        return $this->editavel && $this->ativo;
    }

    public function isVisivel(): bool
    {
        return $this->visivel && $this->ativo;
    }

    public function hasValor(): bool
    {
        return !is_null($this->valor) && $this->valor !== '';
    }

    public function getValorOuPadrao(): mixed
    {
        return $this->hasValor() ? $this->valor_formatado : $this->valor_padrao_formatado;
    }

    public function resetarParaPadrao(): void
    {
        $this->valor = $this->valor_padrao;
        $this->save();
    }

    public function getCacheKey(): string
    {
        return 'parametro:' . $this->codigo;
    }

    public function getGrupoCacheKey(): string
    {
        return 'grupo_parametros:' . $this->grupoParametro->codigo;
    }
}