<?php

namespace App\DTOs\Projeto;

class ProjetoVersionDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $projetoId = null,
        public ?int $versionNumber = null,
        public ?string $conteudo = null,
        public ?string $changelog = null,
        public ?string $comentarios = null,
        public ?int $authorId = null,
        public ?string $tipoAlteracao = null,
        public ?bool $isCurrent = null,
        public ?bool $isPublished = null,
        public ?array $diffData = null,
        public ?int $tamanhoBytes = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            projetoId: $data['projeto_id'] ?? null,
            versionNumber: $data['version_number'] ?? null,
            conteudo: $data['conteudo'] ?? null,
            changelog: $data['changelog'] ?? null,
            comentarios: $data['comentarios'] ?? null,
            authorId: $data['author_id'] ?? null,
            tipoAlteracao: $data['tipo_alteracao'] ?? 'revisao',
            isCurrent: $data['is_current'] ?? false,
            isPublished: $data['is_published'] ?? false,
            diffData: $data['diff_data'] ?? null,
            tamanhoBytes: $data['tamanho_bytes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'projeto_id' => $this->projetoId,
            'version_number' => $this->versionNumber,
            'conteudo' => $this->conteudo,
            'changelog' => $this->changelog,
            'comentarios' => $this->comentarios,
            'author_id' => $this->authorId,
            'tipo_alteracao' => $this->tipoAlteracao,
            'is_current' => $this->isCurrent,
            'is_published' => $this->isPublished,
            'diff_data' => $this->diffData,
            'tamanho_bytes' => $this->tamanhoBytes,
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
               !empty($this->conteudo) && 
               !empty($this->authorId) &&
               !empty($this->versionNumber);
    }

    public function withDefaults(): self
    {
        return new self(
            id: $this->id,
            projetoId: $this->projetoId,
            versionNumber: $this->versionNumber,
            conteudo: $this->conteudo,
            changelog: $this->changelog,
            comentarios: $this->comentarios,
            authorId: $this->authorId ?? auth()->id(),
            tipoAlteracao: $this->tipoAlteracao ?? 'revisao',
            isCurrent: $this->isCurrent ?? false,
            isPublished: $this->isPublished ?? false,
            diffData: $this->diffData,
            tamanhoBytes: $this->tamanhoBytes ?? (strlen($this->conteudo ?? '') ?: null),
        );
    }
}