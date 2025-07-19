<?php

namespace App\Models\Parametro;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParametroModulo extends Model
{
    use HasFactory;

    protected $table = 'parametros_modulos';

    protected $fillable = [
        'nome',
        'descricao',
        'icon',
        'ordem',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'ordem' => 'integer',
    ];

    // Relacionamentos
    public function submodulos(): HasMany
    {
        return $this->hasMany(ParametroSubmodulo::class, 'modulo_id');
    }

    public function submodulosAtivos(): HasMany
    {
        return $this->submodulos()->where('ativo', true)->orderBy('ordem');
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

    // Accessors
    public function getIconClassAttribute(): string
    {
        return $this->icon ? "ki-duotone {$this->icon}" : 'ki-duotone ki-setting-2';
    }

    // Métodos
    public function getProximaOrdem(): int
    {
        $ultimaOrdem = static::max('ordem') ?? 0;
        return $ultimaOrdem + 1;
    }

    public function hasSubmodulos(): bool
    {
        return $this->submodulos()->count() > 0;
    }

    public function getSubmodulosCount(): int
    {
        return $this->submodulos()->count();
    }

    public function getSubmodulosAtivosCount(): int
    {
        return $this->submodulosAtivos()->count();
    }

    public function toJsonExtract(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'icon' => $this->icon,
            'ordem' => $this->ordem,
            'ativo' => $this->ativo,
            'submodulos' => $this->submodulosAtivos->map(function ($submodulo) {
                return $submodulo->toJsonExtract();
            }),
            'estatisticas' => [
                'total_submodulos' => $this->getSubmodulosCount(),
                'submodulos_ativos' => $this->getSubmodulosAtivosCount(),
                'total_campos' => $this->submodulos->reduce(function ($carry, $submodulo) {
                    return $carry + $submodulo->getCamposCount();
                }, 0),
                'campos_ativos' => $this->submodulos->reduce(function ($carry, $submodulo) {
                    return $carry + $submodulo->getCamposAtivosCount();
                }, 0),
                'campos_utilizados' => $this->submodulos->reduce(function ($carry, $submodulo) {
                    return $carry + $submodulo->campos->filter(fn($campo) => $campo->hasValor())->count();
                }, 0),
            ]
        ];
    }

    public function toJsonExtractSimple(): array
    {
        $campos = [];
        
        // Carrega os relacionamentos necessários se não estiverem carregados
        if (!$this->relationLoaded('submodulos')) {
            $this->load(['submodulos.campos.valores']);
        }
        
        foreach ($this->submodulosAtivos as $submodulo) {
            foreach ($submodulo->camposAtivos as $campo) {
                $valorAtual = null;
                $utilizado = false;
                
                try {
                    $utilizado = $campo->hasValor();
                    if ($utilizado) {
                        $valorAtual = $campo->valor_atual;
                    }
                } catch (\Exception $e) {
                    // Se houver erro ao verificar valor, considera como não utilizado
                    $utilizado = false;
                    $valorAtual = null;
                }
                
                $campos[] = [
                    'modulo' => $this->nome,
                    'submodulo' => $submodulo->nome,
                    'campo' => $campo->nome,
                    'label' => $campo->label ?: $campo->nome,
                    'tipo' => $campo->tipo_campo,
                    'obrigatorio' => (bool) $campo->obrigatorio,
                    'utilizado' => $utilizado,
                    'valor_atual' => $valorAtual,
                ];
            }
        }

        return [
            'modulo' => $this->nome,
            'total_campos' => count($campos),
            'campos_utilizados' => collect($campos)->where('utilizado', true)->count(),
            'campos' => $campos
        ];
    }
}