<?php

namespace App\Services\ApiClient\Exceptions;

use Exception;

class ApiException extends Exception
{
    public function __construct(
        string $message,
        public readonly int $statusCode = 500,
        public readonly ?array $response = null,
        public readonly ?string $provider = null,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    /**
     * Criar exception para timeout
     */
    public static function timeout(string $provider, int $timeout): self
    {
        return new self(
            message: "API request timeout after {$timeout} seconds",
            statusCode: 408,
            provider: $provider
        );
    }

    /**
     * Criar exception para resposta inválida
     */
    public static function invalidResponse(string $provider, int $statusCode, ?array $response = null): self
    {
        return new self(
            message: "Invalid API response from {$provider}",
            statusCode: $statusCode,
            response: $response,
            provider: $provider
        );
    }

    /**
     * Criar exception para erro de conexão
     */
    public static function connectionError(string $provider, string $error): self
    {
        return new self(
            message: "Connection error with {$provider}: {$error}",
            statusCode: 503,
            provider: $provider
        );
    }

    /**
     * Criar exception para erro de autenticação
     */
    public static function authenticationError(string $provider): self
    {
        return new self(
            message: "Authentication failed with {$provider}",
            statusCode: 401,
            provider: $provider
        );
    }

    /**
     * Obter dados contextuais do erro
     */
    public function getContext(): array
    {
        return [
            'message' => $this->getMessage(),
            'status_code' => $this->statusCode,
            'provider' => $this->provider,
            'response' => $this->response,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
        ];
    }
} 