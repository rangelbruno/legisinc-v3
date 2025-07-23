<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Partido extends Model
{
    use HasFactory;

    protected $fillable = [
        'sigla',
        'nome',
        'numero',
        'presidente',
        'fundacao',
        'site',
        'status',
    ];

    protected $casts = [
        'fundacao' => 'date',
    ];

    /**
     * Relacionamento com Parlamentares
     */
    public function parlamentares()
    {
        return $this->hasMany(Parlamentar::class, 'partido', 'sigla');
    }

    /**
     * Scope para partidos ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo');
    }

    /**
     * Scope para buscar por termo
     */
    public function scopeBuscar($query, $termo)
    {
        return $query->where(function ($q) use ($termo) {
            $q->where('nome', 'LIKE', "%{$termo}%")
              ->orWhere('sigla', 'LIKE', "%{$termo}%")
              ->orWhere('numero', 'LIKE', "%{$termo}%")
              ->orWhere('presidente', 'LIKE', "%{$termo}%");
        });
    }

    /**
     * Accessor para sigla em maiúsculas
     */
    public function getSiglaFormatadaAttribute()
    {
        return strtoupper($this->sigla);
    }

    /**
     * Accessor para status formatado
     */
    public function getStatusFormatadoAttribute()
    {
        return ucfirst($this->status);
    }

    /**
     * Accessor para data de fundação formatada
     */
    public function getFundacaoFormatadaAttribute()
    {
        return $this->fundacao ? $this->fundacao->format('d/m/Y') : '';
    }

    /**
     * Accessor para total de parlamentares
     */
    public function getTotalParlamentaresAttribute()
    {
        return $this->parlamentares()->count();
    }

    /**
     * Accessor para parlamentares ativos
     */
    public function getTotalParlamentaresAtivosAttribute()
    {
        return $this->parlamentares()->where('status', 'ativo')->count();
    }
}