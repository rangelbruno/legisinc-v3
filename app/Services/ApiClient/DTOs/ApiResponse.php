<?php

namespace App\Services\ApiClient\DTOs;

class ApiResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly array $data,
        public readonly ?string $message = null,
        public readonly int $statusCode = 200,
        public readonly ?array $headers = null,
        public readonly ?float $responseTime = null
    ) {}

    /**
     * Criar resposta de sucesso
     */
    public static function success(
        array $data,
        int $statusCode = 200,
        ?string $message = null,
        ?array $headers = null,
        ?float $responseTime = null
    ): self {
        return new self(
            success: true,
            data: $data,
            message: $message,
            statusCode: $statusCode,
            headers: $headers,
            responseTime: $responseTime
        );
    }

    /**
     * Criar resposta de erro
     */
    public static function error(
        string $message,
        int $statusCode = 400,
        array $data = [],
        ?array $headers = null,
        ?float $responseTime = null
    ): self {
        return new self(
            success: false,
            data: $data,
            message: $message,
            statusCode: $statusCode,
            headers: $headers,
            responseTime: $responseTime
        );
    }

    /**
     * Verificar se a resposta foi bem-sucedida
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Verificar se a resposta foi um erro
     */
    public function isError(): bool
    {
        return !$this->success;
    }

    /**
     * Obter apenas os dados
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Converter para array
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'data' => $this->data,
            'message' => $this->message,
            'status_code' => $this->statusCode,
            'headers' => $this->headers,
            'response_time' => $this->responseTime,
        ];
    }
} 