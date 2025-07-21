<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Documento\DocumentoInstancia;

class Projeto extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'ementa',
        'tipo_proposicao_id',
        'status',
        'created_by'
    ];

    public function tipoProposicao()
    {
        return $this->belongsTo(TipoProposicao::class);
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoInstancia::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}