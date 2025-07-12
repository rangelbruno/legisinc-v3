<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoProjeto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'template_conteudo',
        'ativo',
        'metadados',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'metadados' => 'array',
    ];

    // Relacionamentos
    public function projetos(): HasMany
    {
        return $this->hasMany(Projeto::class, 'tipo_projeto_id');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    // Métodos de negócio
    public function isAtivo(): bool
    {
        return $this->ativo;
    }

    public function hasTemplate(): bool
    {
        return !empty($this->template_conteudo);
    }

    public function getTotalProjetos(): int
    {
        return $this->projetos()->count();
    }

    public function getProjetosAtivos(): int
    {
        return $this->projetos()->where('ativo', true)->count();
    }
}
