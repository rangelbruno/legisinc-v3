<?php

namespace App\Models\Documento;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class DocumentoVersao extends Model
{
    use HasFactory;

    protected $table = 'documento_versoes';

    protected $fillable = [
        'instancia_id',
        'arquivo_path',
        'arquivo_nome',
        'versao',
        'modificado_por',
        'comentarios',
        'hash_arquivo'
    ];

    protected $casts = [
        'versao' => 'integer'
    ];

    public $timestamps = true;

    public function instancia()
    {
        return $this->belongsTo(DocumentoInstancia::class);
    }

    public function modificador()
    {
        return $this->belongsTo(User::class, 'modificado_por');
    }

    public function scopePorInstancia($query, $instanciaId)
    {
        return $query->where('instancia_id', $instanciaId);
    }

    public function scopeOrdenadaPorVersao($query)
    {
        return $query->orderBy('versao', 'desc');
    }

    public function getDataModificacaoFormatadaAttribute()
    {
        return $this->created_at->format('d/m/Y H:i:s');
    }

    public function getTamanhoArquivoFormatadoAttribute()
    {
        if (!$this->arquivo_path || !file_exists(storage_path('app/' . $this->arquivo_path))) {
            return 'N/A';
        }

        $bytes = filesize(storage_path('app/' . $this->arquivo_path));
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
