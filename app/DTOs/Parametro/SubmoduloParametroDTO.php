<?php

namespace App\DTOs\Parametro;

class SubmoduloParametroDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $modulo_id = null,
        public ?string $nome = null,
        public ?string $descricao = null,
        public ?string $tipo = null,
        public ?array $config = null,
        public ?int $ordem = null,
        public ?bool $ativo = null,
        public ?array $campos = null,
        public ?ModuloParametroDTO $modulo = null,
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            modulo_id: $data['modulo_id'] ?? null,
            nome: $data['nome'] ?? null,
            descricao: $data['descricao'] ?? null,
            tipo: $data['tipo'] ?? null,
            config: $data['config'] ?? null,
            ordem: $data['ordem'] ?? null,
            ativo: $data['ativo'] ?? null,
            campos: $data['campos'] ?? null,
            modulo: isset($data['modulo']) ? ModuloParametroDTO::fromArray($data['modulo']) : null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'modulo_id' => $this->modulo_id,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'tipo' => $this->tipo,
            'config' => $this->config,
            'ordem' => $this->ordem,
            'ativo' => $this->ativo,
            'campos' => $this->campos,
            'modulo' => $this->modulo?->toArray(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ], fn($value) => $value !== null);
    }

    public function toCreateArray(): array
    {
        return array_filter([
            'modulo_id' => $this->modulo_id,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'tipo' => $this->tipo,
            'config' => $this->config,
            'ordem' => $this->ordem,
            'ativo' => $this->ativo ?? true,
        ], fn($value) => $value !== null);
    }

    public function toUpdateArray(): array
    {
        return array_filter([
            'modulo_id' => $this->modulo_id,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'tipo' => $this->tipo,
            'config' => $this->config,
            'ordem' => $this->ordem,
            'ativo' => $this->ativo,
        ], fn($value) => $value !== null);
    }

    public function getCaminhoCompleto(): string
    {
        $nomeModulo = $this->modulo?->nome ?? 'Módulo';
        return "{$nomeModulo} > {$this->nome}";
    }

    public function isFormulario(): bool
    {
        return $this->tipo === 'form';
    }

    public function isCheckbox(): bool
    {
        return $this->tipo === 'checkbox';
    }

    public function isSelect(): bool
    {
        return $this->tipo === 'select';
    }

    public function isToggle(): bool
    {
        return $this->tipo === 'toggle';
    }

    public function isCustom(): bool
    {
        return $this->tipo === 'custom';
    }
}