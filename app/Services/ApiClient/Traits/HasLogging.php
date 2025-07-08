<?php

namespace App\Services\ApiClient\Traits;

use Illuminate\Support\Facades\Log;
use App\Services\ApiClient\DTOs\ApiResponse;

trait HasLogging
{
    /**
     * Log channel
     */
    protected string $logChannel = 'api_client';

    /**
     * Log de requisição iniciada
     */
    protected function logRequest(string $method, string $endpoint, array $data = []): void
    {
        $context = [
            'provider' => static::class,
            'method' => strtoupper($method),
            'endpoint' => $endpoint,
            'data' => $this->sanitizeLogData($data),
            'timestamp' => now()->toISOString(),
        ];

        Log::channel($this->logChannel)->info('API Request Started', $context);
    }

    /**
     * Log de resposta bem-sucedida
     */
    protected function logResponse(ApiResponse $response, float $responseTime): void
    {
        $context = [
            'provider' => static::class,
            'success' => $response->success,
            'status_code' => $response->statusCode,
            'response_time' => round($responseTime, 3),
            'data_size' => strlen(json_encode($response->data)),
            'timestamp' => now()->toISOString(),
        ];

        Log::channel($this->logChannel)->info('API Response Received', $context);
    }

    /**
     * Log de erro
     */
    protected function logError(\Exception $exception, array $context = []): void
    {
        $logContext = array_merge([
            'provider' => static::class,
            'error' => $exception->getMessage(),
            'error_code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'timestamp' => now()->toISOString(),
        ], $context);

        Log::channel($this->logChannel)->error('API Request Failed', $logContext);
    }

    /**
     * Log de health check
     */
    protected function logHealthCheck(bool $isHealthy, float $responseTime): void
    {
        $context = [
            'provider' => static::class,
            'healthy' => $isHealthy,
            'response_time' => round($responseTime, 3),
            'timestamp' => now()->toISOString(),
        ];

        $level = $isHealthy ? 'info' : 'warning';
        Log::channel($this->logChannel)->$level('API Health Check', $context);
    }

    /**
     * Sanitizar dados sensíveis para log
     */
    protected function sanitizeLogData(array $data): array
    {
        $sensitiveKeys = ['password', 'token', 'key', 'secret', 'auth', 'authorization'];
        
        return array_map(function ($value) use ($sensitiveKeys) {
            if (is_array($value)) {
                return $this->sanitizeLogData($value);
            }
            
            return $value;
        }, array_combine(
            array_keys($data),
            array_map(function ($key, $value) use ($sensitiveKeys) {
                if (in_array(strtolower($key), $sensitiveKeys)) {
                    return '***HIDDEN***';
                }
                return $value;
            }, array_keys($data), array_values($data))
        ));
    }
} 