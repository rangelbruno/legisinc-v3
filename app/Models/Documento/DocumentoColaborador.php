<?php

namespace App\Models\Documento;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class DocumentoColaborador extends Model
{
    use HasFactory;

    protected $table = 'documento_colaboradores';

    protected $fillable = [
        'instancia_id',
        'user_id',
        'permissao',
        'ativo',
        'ultimo_acesso',
        'adicionado_por'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'ultimo_acesso' => 'datetime'
    ];

    const PERMISSAO_VIEW = 'view';
    const PERMISSAO_COMMENT = 'comment';
    const PERMISSAO_EDIT = 'edit';
    const PERMISSAO_ADMIN = 'admin';

    public function instancia()
    {
        return $this->belongsTo(DocumentoInstancia::class, 'instancia_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function adicionadoPor()
    {
        return $this->belongsTo(User::class, 'adicionado_por');
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeComPermissao($query, $permissao)
    {
        return $query->where('permissao', $permissao);
    }

    public function podeEditar(): bool
    {
        return in_array($this->permissao, [self::PERMISSAO_EDIT, self::PERMISSAO_ADMIN]) && $this->ativo;
    }

    public function podeAdministrar(): bool
    {
        return $this->permissao === self::PERMISSAO_ADMIN && $this->ativo;
    }

    public function getPermissaoFormatadaAttribute(): array
    {
        return match($this->permissao) {
            self::PERMISSAO_VIEW => ['texto' => 'Visualizar', 'classe' => 'badge-light'],
            self::PERMISSAO_COMMENT => ['texto' => 'Comentar', 'classe' => 'badge-info'],
            self::PERMISSAO_EDIT => ['texto' => 'Editar', 'classe' => 'badge-warning'],
            self::PERMISSAO_ADMIN => ['texto' => 'Administrar', 'classe' => 'badge-danger'],
            default => ['texto' => 'Indefinido', 'classe' => 'badge-secondary']
        };
    }
}