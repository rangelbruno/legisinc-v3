<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariavelDinamica extends Model
{
    use HasFactory;

    protected $table = 'variaveis_dinamicas';

    protected $fillable = [
        'nome',
        'valor',
        'descricao',
        'tipo',
        'escopo',
        'formato',
        'validacao',
        'sistema',
        'ativo',
        'ordem',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'sistema' => 'boolean',
        'ativo' => 'boolean',
        'ordem' => 'integer'
    ];

    /**
     * Usuário que criou a variável
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Usuário que atualizou a variável
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope para variáveis ativas
     */
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para variáveis por escopo
     */
    public function scopePorEscopo($query, $escopo)
    {
        return $query->where('escopo', $escopo);
    }

    /**
     * Scope para variáveis do sistema
     */
    public function scopeSistema($query, $sistema = true)
    {
        return $query->where('sistema', $sistema);
    }

    /**
     * Formatar valor baseado no tipo
     */
    public function getValorFormatadoAttribute()
    {
        switch ($this->tipo) {
            case 'data':
                try {
                    return \Carbon\Carbon::parse($this->valor)->format($this->formato ?: 'd/m/Y');
                } catch (\Exception $e) {
                    return $this->valor;
                }
            case 'boolean':
                return $this->valor === 'true' ? 'Sim' : 'Não';
            case 'numero':
                return is_numeric($this->valor) ? number_format((float)$this->valor, 0, ',', '.') : $this->valor;
            default:
                return $this->valor;
        }
    }

    /**
     * Obter variáveis padrão do sistema
     */
    public static function getVariaveisPadrao(): array
    {
        return [
            [
                'nome' => 'NOME_CAMARA',
                'valor' => 'Câmara Municipal',
                'descricao' => 'Nome completo da câmara municipal',
                'tipo' => 'texto',
                'escopo' => 'global',
                'formato' => null,
                'validacao' => 'required|string|max:255',
                'sistema' => true
            ],
            [
                'nome' => 'SIGLA_CAMARA',
                'valor' => 'CM',
                'descricao' => 'Sigla da câmara municipal',
                'tipo' => 'texto',
                'escopo' => 'global',
                'formato' => null,
                'validacao' => 'required|string|max:10',
                'sistema' => true
            ],
            [
                'nome' => 'DATA_ATUAL',
                'valor' => date('d/m/Y'),
                'descricao' => 'Data atual do sistema',
                'tipo' => 'data',
                'escopo' => 'global',
                'formato' => 'd/m/Y',
                'validacao' => null,
                'sistema' => true
            ],
            [
                'nome' => 'ANO_ATUAL',
                'valor' => date('Y'),
                'descricao' => 'Ano atual',
                'tipo' => 'numero',
                'escopo' => 'global',
                'formato' => null,
                'validacao' => null,
                'sistema' => true
            ],
            [
                'nome' => 'USUARIO_LOGADO',
                'valor' => auth()->user()->name ?? 'Sistema',
                'descricao' => 'Nome do usuário atualmente logado',
                'tipo' => 'texto',
                'escopo' => 'sistema',
                'formato' => null,
                'validacao' => null,
                'sistema' => true
            ]
        ];
    }
}
