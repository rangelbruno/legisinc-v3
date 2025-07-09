<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class ProjetoAnexo extends Model
{
    use HasFactory;

    protected $fillable = [
        'projeto_id',
        'nome_original',
        'nome_arquivo',
        'path',
        'mime_type',
        'tamanho',
        'tipo',
        'descricao',
        'ordem',
        'uploaded_by',
        'publico',
        'ativo',
        'metadados',
        'hash_arquivo',
    ];

    protected $casts = [
        'publico' => 'boolean',
        'ativo' => 'boolean',
        'metadados' => 'array',
        'tamanho' => 'integer',
        'ordem' => 'integer',
    ];

    // Constantes
    public const TIPOS = [
        'documento_base' => 'Documento Base',
        'emenda' => 'Emenda',
        'parecer' => 'Parecer',
        'justificativa' => 'Justificativa',
        'estudo_tecnico' => 'Estudo Técnico',
        'manifestacao' => 'Manifestação',
        'outro' => 'Outro',
    ];

    // Relacionamentos
    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Accessors
    public function getTipoFormatadoAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function getTamanhoFormatadoAttribute(): string
    {
        if (!$this->tamanho) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = floor(log($this->tamanho, 1024));
        
        return round($this->tamanho / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    public function getExtensaoAttribute(): string
    {
        return strtoupper(pathinfo($this->nome_original, PATHINFO_EXTENSION));
    }

    public function getIconeAttribute(): string
    {
        $extensao = strtolower(pathinfo($this->nome_original, PATHINFO_EXTENSION));
        
        $icones = [
            'pdf' => 'ki-file-pdf',
            'doc' => 'ki-file-word', 
            'docx' => 'ki-file-word',
            'xls' => 'ki-file-excel',
            'xlsx' => 'ki-file-excel',
            'ppt' => 'ki-file-powerpoint',
            'pptx' => 'ki-file-powerpoint',
            'jpg' => 'ki-file-image',
            'jpeg' => 'ki-file-image',
            'png' => 'ki-file-image',
            'gif' => 'ki-file-image',
            'txt' => 'ki-file-text',
            'zip' => 'ki-file-zip',
            'rar' => 'ki-file-zip',
        ];

        return $icones[$extensao] ?? 'ki-file';
    }

    public function getCorTipoAttribute(): string
    {
        $cores = [
            'documento_base' => 'primary',
            'emenda' => 'warning',
            'parecer' => 'info',
            'justificativa' => 'success',
            'estudo_tecnico' => 'secondary',
            'manifestacao' => 'light',
            'outro' => 'dark',
        ];

        return $cores[$this->tipo] ?? 'secondary';
    }

    public function getUrlDownloadAttribute(): string
    {
        return route('projetos.anexos.download', ['projeto' => $this->projeto_id, 'anexo' => $this->id]);
    }

    public function getUrlViewAttribute(): string
    {
        return route('projetos.anexos.view', ['projeto' => $this->projeto_id, 'anexo' => $this->id]);
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePublicos($query)
    {
        return $query->where('publico', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorProjeto($query, $projetoId)
    {
        return $query->where('projeto_id', $projetoId);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('ordem')->orderBy('created_at');
    }

    // Métodos de negócio
    public function podeSerBaixado(): bool
    {
        return $this->ativo && ($this->publico || auth()->check());
    }

    public function podeSerExcluido(): bool
    {
        return auth()->check() && (
            auth()->id() === $this->uploaded_by ||
            auth()->user()->isAdmin() ||
            auth()->id() === $this->projeto->autor_id
        );
    }

    public function existeArquivo(): bool
    {
        return Storage::exists($this->path);
    }

    public function getConteudoArquivo(): string
    {
        if (!$this->existeArquivo()) {
            throw new \Exception('Arquivo não encontrado no storage');
        }

        return Storage::get($this->path);
    }

    public function verificarIntegridade(): bool
    {
        if (!$this->hash_arquivo || !$this->existeArquivo()) {
            return false;
        }

        $hashAtual = hash_file('sha256', Storage::path($this->path));
        return $hashAtual === $this->hash_arquivo;
    }

    public function moverParaLixeira(): void
    {
        $this->update(['ativo' => false]);
    }

    public function restaurarDaLixeira(): void
    {
        $this->update(['ativo' => true]);
    }

    public function excluirDefinitivamente(): void
    {
        // Excluir arquivo físico
        if ($this->existeArquivo()) {
            Storage::delete($this->path);
        }

        // Excluir registro
        $this->delete();
    }

    // Events
    protected static function booted()
    {
        static::creating(function (ProjetoAnexo $anexo) {
            // Gerar hash do arquivo se não existir
            if (!$anexo->hash_arquivo && $anexo->path && Storage::exists($anexo->path)) {
                $anexo->hash_arquivo = hash_file('sha256', Storage::path($anexo->path));
            }

            // Definir ordem automática se não especificada
            if (!$anexo->ordem) {
                $ultimaOrdem = self::where('projeto_id', $anexo->projeto_id)->max('ordem') ?? 0;
                $anexo->ordem = $ultimaOrdem + 1;
            }
        });

        static::deleting(function (ProjetoAnexo $anexo) {
            // Excluir arquivo físico quando o registro for excluído
            if ($anexo->existeArquivo()) {
                Storage::delete($anexo->path);
            }
        });
    }
}