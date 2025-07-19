<?php

namespace App\DTOs\Parametro;

class ValorParametroDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $campo_id = null,
        public ?string $valor = null,
        public ?string $tipo_valor = null,
        public ?int $user_id = null,
        public ?string $valido_ate = null,
        public ?CampoParametroDTO $campo = null,
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            campo_id: $data['campo_id'] ?? null,
            valor: $data['valor'] ?? null,
            tipo_valor: $data['tipo_valor'] ?? null,
            user_id: $data['user_id'] ?? null,
            valido_ate: $data['valido_ate'] ?? null,
            campo: isset($data['campo']) ? CampoParametroDTO::fromArray($data['campo']) : null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'campo_id' => $this->campo_id,
            'valor' => $this->valor,
            'tipo_valor' => $this->tipo_valor,
            'user_id' => $this->user_id,
            'valido_ate' => $this->valido_ate,
            'campo' => $this->campo?->toArray(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ], fn($value) => $value !== null);
    }

    public function toCreateArray(): array
    {
        return array_filter([
            'campo_id' => $this->campo_id,
            'valor' => $this->valor,
            'tipo_valor' => $this->tipo_valor ?? 'string',
            'user_id' => $this->user_id,
            'valido_ate' => $this->valido_ate,
        ], fn($value) => $value !== null);
    }

    public function toUpdateArray(): array
    {
        return array_filter([
            'valor' => $this->valor,
            'tipo_valor' => $this->tipo_valor,
            'user_id' => $this->user_id,
            'valido_ate' => $this->valido_ate,
        ], fn($value) => $value !== null);
    }

    public function getValorFormatado(): mixed
    {
        if (is_null($this->valor)) {
            return null;
        }

        return match ($this->tipo_valor) {
            'integer' => (int) $this->valor,
            'decimal' => (float) $this->valor,
            'boolean' => (bool) $this->valor,
            'date' => $this->valor instanceof \DateTime ? $this->valor : new \DateTime($this->valor),
            'datetime' => $this->valor instanceof \DateTime ? $this->valor : new \DateTime($this->valor),
            'array' => is_string($this->valor) ? explode(',', $this->valor) : (is_array($this->valor) ? $this->valor : [$this->valor]),
            'json' => is_string($this->valor) ? json_decode($this->valor, true) : $this->valor,
            default => (string) $this->valor
        };
    }

    public function getValorDisplay(): string
    {
        $valor = $this->getValorFormatado();
        
        if (is_null($valor)) {
            return '-';
        }

        return match ($this->tipo_valor) {
            'boolean' => $valor ? 'Sim' : 'Não',
            'date' => $valor instanceof \DateTime ? $valor->format('d/m/Y') : (string) $valor,
            'datetime' => $valor instanceof \DateTime ? $valor->format('d/m/Y H:i:s') : (string) $valor,
            'array' => is_array($valor) ? implode(', ', $valor) : (string) $valor,
            'json' => is_array($valor) ? json_encode($valor, JSON_PRETTY_PRINT) : (string) $valor,
            default => (string) $valor
        };
    }

    public function isValido(): bool
    {
        return is_null($this->valido_ate) || strtotime($this->valido_ate) > time();
    }

    public function isExpirado(): bool
    {
        return !$this->isValido();
    }

    public function defineValor(mixed $valor, string $tipo = 'string'): void
    {
        $this->valor = $valor;
        $this->tipo_valor = $tipo;
    }

    public function definePeriodoValidade(?\DateTime $validoAte = null): void
    {
        $this->valido_ate = $validoAte?->format('Y-m-d H:i:s');
    }
}