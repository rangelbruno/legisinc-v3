<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GrupoParametro extends Model
{
    use HasFactory;

    protected $table = 'grupos_parametros';

    protected $fillable = [
        'nome',
        'codigo',
        'descricao',
        'icone',
        'cor',
        'ordem',
        'ativo',
        'grupo_pai_id'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'ordem' => 'integer',
        'grupo_pai_id' => 'integer'
    ];

    public const ICONES_DISPONIVEIS = [
        'ki-setting-2' => 'Configurações',
        'ki-abstract-26' => 'Sistema',
        'ki-flash' => 'Performance',
        'ki-courthouse' => 'Legislativo',
        'ki-notification-bing' => 'Notificações',
        'ki-design-1' => 'Interface',
        'ki-security-check' => 'Segurança',
        'ki-cloud' => 'Cloud',
        'ki-abstract-44' => 'Integração',
        'ki-briefcase' => 'Gestão'
    ];

    public const CORES_DISPONIVEIS = [
        '#009EF7' => 'Azul',
        '#7239EA' => 'Roxo',
        '#50CD89' => 'Verde',
        '#F1416C' => 'Vermelho',
        '#FFC700' => 'Amarelo',
        '#FF6800' => 'Laranja',
        '#E4E6EF' => 'Cinza',
        '#181C32' => 'Preto',
        '#3F4254' => 'Cinza Escuro',
        '#5E6278' => 'Cinza Médio'
    ];

    // Relacionamentos
    public function grupoPai(): BelongsTo
    {
        return $this->belongsTo(GrupoParametro::class, 'grupo_pai_id');
    }

    public function gruposFilhos(): HasMany
    {
        return $this->hasMany(GrupoParametro::class, 'grupo_pai_id')->where('ativo', true);
    }

    public function parametros(): HasMany
    {
        return $this->hasMany(Parametro::class, 'grupo_parametro_id');
    }

    public function parametrosAtivos(): HasMany
    {
        return $this->hasMany(Parametro::class, 'grupo_parametro_id')->where('ativo', true);
    }

    // Accessors
    public function getIconeFormatadoAttribute(): string
    {
        return self::ICONES_DISPONIVEIS[$this->icone] ?? $this->icone;
    }

    public function getCorFormatadaAttribute(): string
    {
        return self::CORES_DISPONIVEIS[$this->cor] ?? $this->cor;
    }

    public function getCaminhoCompletoAttribute(): string
    {
        $caminho = $this->nome;
        
        if ($this->grupoPai) {
            $caminho = $this->grupoPai->caminho_completo . ' > ' . $caminho;
        }
        
        return $caminho;
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

    public function scopeRaiz($query)
    {
        return $query->whereNull('grupo_pai_id');
    }

    public function scopeFilhos($query)
    {
        return $query->whereNotNull('grupo_pai_id');
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

    public function isRaiz(): bool
    {
        return is_null($this->grupo_pai_id);
    }

    public function hasFilhos(): bool
    {
        return $this->gruposFilhos()->count() > 0;
    }

    public function getProximaOrdem(): int
    {
        $ultimaOrdem = static::where('grupo_pai_id', $this->grupo_pai_id)
            ->max('ordem') ?? 0;
        
        return $ultimaOrdem + 1;
    }
}