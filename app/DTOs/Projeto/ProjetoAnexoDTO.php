<?php

namespace App\DTOs\Projeto;

class ProjetoAnexoDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $projetoId = null,
        public ?string $nomeOriginal = null,
        public ?string $nomeArquivo = null,
        public ?string $path = null,
        public ?string $mimeType = null,
        public ?int $tamanho = null,
        public ?string $tipo = null,
        public ?string $descricao = null,
        public ?int $ordem = null,
        public ?int $uploadedBy = null,
        public ?bool $publico = null,
        public ?bool $ativo = null,
        public ?array $metadados = null,
        public ?string $hashArquivo = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            projetoId: $data['projeto_id'] ?? null,
            nomeOriginal: $data['nome_original'] ?? null,
            nomeArquivo: $data['nome_arquivo'] ?? null,
            path: $data['path'] ?? null,
            mimeType: $data['mime_type'] ?? null,
            tamanho: $data['tamanho'] ?? null,
            tipo: $data['tipo'] ?? 'outro',
            descricao: $data['descricao'] ?? null,
            ordem: $data['ordem'] ?? null,
            uploadedBy: $data['uploaded_by'] ?? null,
            publico: $data['publico'] ?? true,
            ativo: $data['ativo'] ?? true,
            metadados: $data['metadados'] ?? null,
            hashArquivo: $data['hash_arquivo'] ?? null,
        );
    }

    public static function fromUploadedFile(\Illuminate\Http\UploadedFile $file, int $projetoId, string $tipo = 'outro'): self
    {
        return new self(
            projetoId: $projetoId,
            nomeOriginal: $file->getClientOriginalName(),
            nomeArquivo: $file->hashName(),
            mimeType: $file->getMimeType(),
            tamanho: $file->getSize(),
            tipo: $tipo,
            uploadedBy: auth()->id(),
            publico: true,
            ativo: true,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'projeto_id' => $this->projetoId,
            'nome_original' => $this->nomeOriginal,
            'nome_arquivo' => $this->nomeArquivo,
            'path' => $this->path,
            'mime_type' => $this->mimeType,
            'tamanho' => $this->tamanho,
            'tipo' => $this->tipo,
            'descricao' => $this->descricao,
            'ordem' => $this->ordem,
            'uploaded_by' => $this->uploadedBy,
            'publico' => $this->publico,
            'ativo' => $this->ativo,
            'metadados' => $this->metadados,
            'hash_arquivo' => $this->hashArquivo,
        ];
    }

    public function toCreateArray(): array
    {
        $array = $this->toArray();
        unset($array['id']);
        return array_filter($array, fn($value) => $value !== null);
    }

    public function isValid(): bool
    {
        return !empty($this->projetoId) && 
               !empty($this->nomeOriginal) && 
               !empty($this->uploadedBy);
    }

    public function getValidationErrors(): array
    {
        $errors = [];

        if (empty($this->projetoId)) {
            $errors['projeto_id'] = 'Projeto é obrigatório';
        }

        if (empty($this->nomeOriginal)) {
            $errors['nome_original'] = 'Nome do arquivo é obrigatório';
        }

        if (empty($this->uploadedBy)) {
            $errors['uploaded_by'] = 'Usuário que fez upload é obrigatório';
        }

        if ($this->tamanho && $this->tamanho > 50 * 1024 * 1024) { // 50MB
            $errors['tamanho'] = 'Arquivo muito grande (máximo 50MB)';
        }

        return $errors;
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mimeType ?? '', 'image/');
    }

    public function isPdf(): bool
    {
        return $this->mimeType === 'application/pdf';
    }

    public function isDocument(): bool
    {
        $documentMimes = [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
        ];

        return in_array($this->mimeType, $documentMimes);
    }

    public function getExtension(): string
    {
        return strtolower(pathinfo($this->nomeOriginal ?? '', PATHINFO_EXTENSION));
    }

    public function getTamanhoFormatado(): string
    {
        if (!$this->tamanho) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = floor(log($this->tamanho, 1024));
        
        return round($this->tamanho / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    public function withDefaults(): self
    {
        return new self(
            id: $this->id,
            projetoId: $this->projetoId,
            nomeOriginal: $this->nomeOriginal,
            nomeArquivo: $this->nomeArquivo,
            path: $this->path,
            mimeType: $this->mimeType,
            tamanho: $this->tamanho,
            tipo: $this->tipo ?? 'outro',
            descricao: $this->descricao,
            ordem: $this->ordem ?? 0,
            uploadedBy: $this->uploadedBy ?? auth()->id(),
            publico: $this->publico ?? true,
            ativo: $this->ativo ?? true,
            metadados: $this->metadados ?? [],
            hashArquivo: $this->hashArquivo,
        );
    }
}