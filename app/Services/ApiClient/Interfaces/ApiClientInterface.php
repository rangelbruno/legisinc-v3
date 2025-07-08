<?php

namespace App\Services\ApiClient\Interfaces;

use App\Services\ApiClient\DTOs\ApiResponse;

interface ApiClientInterface
{
    /**
     * Realizar uma requisição GET
     */
    public function get(string $endpoint, array $params = []): ApiResponse;

    /**
     * Realizar uma requisição POST
     */
    public function post(string $endpoint, array $payload = []): ApiResponse;

    /**
     * Realizar uma requisição PUT
     */
    public function put(string $endpoint, array $payload = []): ApiResponse;

    /**
     * Realizar uma requisição DELETE
     */
    public function delete(string $endpoint, array $params = []): ApiResponse;

    /**
     * Verificar saúde da API
     */
    public function healthCheck(): bool;

    /**
     * Obter configurações do provider
     */
    public function getConfig(): array;
} 