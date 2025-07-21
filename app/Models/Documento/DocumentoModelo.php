<?php

namespace App\Models\Documento;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\TipoProposicao;
use App\Models\User;

class DocumentoModelo extends Model
{
    use HasFactory;

    protected $table = 'documento_modelos';

    protected $fillable = [
        'nome',
        'descricao',
        'tipo_proposicao_id',
        'arquivo_path',
        'arquivo_nome',
        'arquivo_size',
        'variaveis',
        'versao',
        'ativo',
        'created_by'
    ];

    protected $casts = [
        'variaveis' => 'array',
        'ativo' => 'boolean',
        'arquivo_size' => 'integer'
    ];

    public function tipoProposicao()
    {
        return $this->belongsTo(TipoProposicao::class);
    }

    public function instancias()
    {
        return $this->hasMany(DocumentoInstancia::class, 'modelo_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorTipo($query, $tipoProposicaoId)
    {
        return $query->where('tipo_proposicao_id', $tipoProposicaoId);
    }
}
