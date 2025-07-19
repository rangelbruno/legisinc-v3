<?php

namespace App\Models\Parametro;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParametroSubmodulo extends Model
{
    use HasFactory;

    protected $table = 'parametros_submodulos';

    protected $fillable = [
        'modulo_id',
        'nome',
        'descricao',
        'tipo',
        'config',
        'ordem',
        'ativo',
    ];

    protected $casts = [
        'config' => 'array',
        'ativo' => 'boolean',
        'ordem' => 'integer',
        'modulo_id' => 'integer',
    ];

    // Relacionamentos
    public function modulo(): BelongsTo
    {
        return $this->belongsTo(ParametroModulo::class, 'modulo_id');
    }

    public function campos(): HasMany
    {
        return $this->hasMany(ParametroCampo::class, 'submodulo_id');
    }

    public function camposAtivos(): HasMany
    {
        return $this->campos()->where('ativo', true)->orderBy('ordem');
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

    public function scopePorModulo($query, int $moduloId)
    {
        return $query->where('modulo_id', $moduloId);
    }

    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Accessors
    public function getCaminhoCompletoAttribute(): string
    {
        return "{$this->modulo->nome} > {$this->nome}";
    }

    public function getConfigFormatadaAttribute(): array
    {
        return $this->config ?? [];
    }

    // Métodos
    public function getProximaOrdem(): int
    {
        $ultimaOrdem = static::where('modulo_id', $this->modulo_id)
            ->max('ordem') ?? 0;
        return $ultimaOrdem + 1;
    }

    public function hasCampos(): bool
    {
        return $this->campos()->count() > 0;
    }

    public function getCamposCount(): int
    {
        return $this->campos()->count();
    }

    public function getCamposAtivosCount(): int
    {
        return $this->camposAtivos()->count();
    }

    public function isFormulario(): bool
    {
        return $this->tipo === 'form';
    }

    public function isCheckbox(): bool
    {
        return $this->tipo === 'checkbox';
    }

    public function isSelect(): bool
    {
        return $this->tipo === 'select';
    }

    public function isToggle(): bool
    {
        return $this->tipo === 'toggle';
    }

    public function isCustom(): bool
    {
        return $this->tipo === 'custom';
    }

    public function toJsonExtract(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'tipo' => $this->tipo,
            'config' => $this->config_formatada,
            'ordem' => $this->ordem,
            'ativo' => $this->ativo,
            'campos' => $this->camposAtivos->map(fn($campo) => $campo->toJsonExtract()),
            'estatisticas' => [
                'total_campos' => $this->getCamposCount(),
                'campos_ativos' => $this->getCamposAtivosCount(),
                'campos_utilizados' => $this->campos->filter(fn($campo) => $campo->hasValor())->count(),
                'campos_obrigatorios' => $this->campos->where('obrigatorio', true)->count(),
            ]
        ];
    }
}