<?php

namespace App\DTOs\Parametro;

class ModuloParametroDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $nome = null,
        public ?string $descricao = null,
        public ?string $icon = null,
        public ?int $ordem = null,
        public ?bool $ativo = null,
        public ?array $submodulos = null,
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            nome: $data['nome'] ?? null,
            descricao: $data['descricao'] ?? null,
            icon: $data['icon'] ?? null,
            ordem: $data['ordem'] ?? null,
            ativo: $data['ativo'] ?? null,
            submodulos: $data['submodulos'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'icon' => $this->icon,
            'ordem' => $this->ordem,
            'ativo' => $this->ativo,
            'submodulos' => $this->submodulos,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ], fn($value) => $value !== null);
    }

    public function toCreateArray(): array
    {
        return array_filter([
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'icon' => $this->icon,
            'ordem' => $this->ordem,
            'ativo' => $this->ativo ?? true,
        ], fn($value) => $value !== null);
    }

    public function toUpdateArray(): array
    {
        return array_filter([
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'icon' => $this->icon,
            'ordem' => $this->ordem,
            'ativo' => $this->ativo,
        ], fn($value) => $value !== null);
    }
}