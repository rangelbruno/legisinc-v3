<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Parlamentar extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'partido',
        'cargo',
        'status',
        'email',
        'telefone',
        'data_nascimento',
        'profissao',
        'escolaridade',
        'comissoes',
        'mandatos',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'comissoes' => 'array',
        'mandatos' => 'array',
    ];

    /**
     * Scope para parlamentares ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo');
    }

    /**
     * Scope para filtrar por partido
     */
    public function scopePartido($query, $partido)
    {
        return $query->where('partido', $partido);
    }

    /**
     * Scope para filtrar por status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para buscar por termo
     */
    public function scopeBuscar($query, $termo)
    {
        return $query->where(function ($q) use ($termo) {
            $q->where('nome', 'LIKE', "%{$termo}%")
              ->orWhere('partido', 'LIKE', "%{$termo}%")
              ->orWhere('cargo', 'LIKE', "%{$termo}%")
              ->orWhere('profissao', 'LIKE', "%{$termo}%");
        });
    }

    /**
     * Accessor para idade
     */
    public function getIdadeAttribute()
    {
        return $this->data_nascimento ? $this->data_nascimento->age : null;
    }

    /**
     * Accessor para total de comissões
     */
    public function getTotalComissoesAttribute()
    {
        return count($this->comissoes ?? []);
    }

    /**
     * Accessor para data nascimento formatada
     */
    public function getDataNascimentoFormatadaAttribute()
    {
        return $this->data_nascimento ? $this->data_nascimento->format('d/m/Y') : '';
    }

    /**
     * Accessor para status formatado
     */
    public function getStatusFormatadoAttribute()
    {
        return ucfirst($this->status);
    }

    /**
     * Accessor para partido em maiúsculas
     */
    public function getPartidoFormatadoAttribute()
    {
        return strtoupper($this->partido);
    }

    /**
     * Verificar se é aniversariante do mês
     */
    public function isAniversarianteMes($mes = null)
    {
        $mes = $mes ?? now()->month;
        return $this->data_nascimento && $this->data_nascimento->month === $mes;
    }

    /**
     * Obter mandatos ativos
     */
    public function getMandatosAtivos()
    {
        $mandatos = $this->mandatos ?? [];
        return array_filter($mandatos, function ($mandato) {
            return ($mandato['status'] ?? '') === 'atual';
        });
    }

    /**
     * Verificar se faz parte da mesa diretora
     */
    public function isMesaDiretora()
    {
        $cargosMesa = ['Presidente da Câmara', 'Vice-Presidente', '1º Secretário', '2º Secretário'];
        return in_array($this->cargo, $cargosMesa);
    }
}
