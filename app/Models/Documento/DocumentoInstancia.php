<?php

namespace App\Models\Documento;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Projeto;
use App\Models\User;

class DocumentoInstancia extends Model
{
    use HasFactory;

    protected $table = 'documento_instancias';

    protected $fillable = [
        'projeto_id',
        'modelo_id',
        'document_key',
        'titulo',
        'arquivo_path',
        'arquivo_nome',
        'arquivo_gerado_path',
        'arquivo_gerado_nome',
        'conteudo_personalizado',
        'variaveis_personalizadas',
        'status',
        'versao',
        'metadados',
        'colaboradores',
        'editado_em',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'metadados' => 'array',
        'variaveis_personalizadas' => 'array',
        'colaboradores' => 'array',
        'editado_em' => 'datetime',
        'versao' => 'integer'
    ];

    const STATUS_RASCUNHO = 'rascunho';
    const STATUS_PARLAMENTAR = 'parlamentar';
    const STATUS_LEGISLATIVO = 'legislativo';
    const STATUS_FINALIZADO = 'finalizado';

    public function projeto()
    {
        return $this->belongsTo(Projeto::class);
    }

    public function modelo()
    {
        return $this->belongsTo(DocumentoModelo::class);
    }

    public function versoes()
    {
        return $this->hasMany(DocumentoVersao::class, 'instancia_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePorStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function getStatusFormatadoAttribute()
    {
        return match($this->status) {
            self::STATUS_RASCUNHO => ['texto' => 'Rascunho', 'classe' => 'badge-secondary'],
            self::STATUS_PARLAMENTAR => ['texto' => 'Com Parlamentar', 'classe' => 'badge-warning'],
            self::STATUS_LEGISLATIVO => ['texto' => 'Em Revisão', 'classe' => 'badge-info'],
            self::STATUS_FINALIZADO => ['texto' => 'Finalizado', 'classe' => 'badge-success'],
            default => ['texto' => 'Indefinido', 'classe' => 'badge-light']
        };
    }

    public function proximaVersao()
    {
        return $this->versoes()->count() + 1;
    }
    
    public function colaboradores()
    {
        return $this->hasMany(\App\Models\Documento\DocumentoColaborador::class, 'instancia_id');
    }
}
