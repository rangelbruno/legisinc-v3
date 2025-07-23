<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MesaDiretora extends Model
{
    use HasFactory;

    protected $table = 'mesa_diretora';

    protected $fillable = [
        'parlamentar_id',
        'cargo_mesa',
        'mandato_inicio',
        'mandato_fim',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'mandato_inicio' => 'date',
        'mandato_fim' => 'date',
    ];

    /**
     * Relacionamento com Parlamentar
     */
    public function parlamentar()
    {
        return $this->belongsTo(Parlamentar::class);
    }

    /**
     * Scope para membros ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo');
    }

    /**
     * Scope para mandato atual
     */
    public function scopeMandatoAtual($query)
    {
        return $query->where('mandato_inicio', '<=', now())
                    ->where('mandato_fim', '>=', now());
    }

    /**
     * Scope para buscar por termo
     */
    public function scopeBuscar($query, $termo)
    {
        return $query->whereHas('parlamentar', function ($q) use ($termo) {
            $q->where('nome', 'LIKE', "%{$termo}%");
        })->orWhere('cargo_mesa', 'LIKE', "%{$termo}%");
    }

    /**
     * Accessor para mandato formatado
     */
    public function getMandatoFormatadoAttribute()
    {
        return $this->mandato_inicio->format('d/m/Y') . ' - ' . $this->mandato_fim->format('d/m/Y');
    }

    /**
     * Accessor para status formatado
     */
    public function getStatusFormatadoAttribute()
    {
        return ucfirst($this->status);
    }

    /**
     * Accessor para cargo formatado
     */
    public function getCargoFormatadoAttribute()
    {
        return ucwords(strtolower($this->cargo_mesa));
    }

    /**
     * Verifica se o mandato está ativo
     */
    public function isMandatoAtivo()
    {
        return $this->status === 'ativo' && 
               now()->between($this->mandato_inicio, $this->mandato_fim);
    }
}
