<?php

namespace App\Services\ApiClient\Traits;

use Illuminate\Support\Facades\Cache;

trait HasCaching
{
    /**
     * Cache key prefix
     */
    protected string $cachePrefix = 'api_client';

    /**
     * Default cache TTL in seconds
     */
    protected int $defaultCacheTtl = 300;

    /**
     * Gerar chave de cache
     */
    protected function generateCacheKey(string $method, string $endpoint, array $params = []): string
    {
        $key = $this->cachePrefix . '_' . strtolower($method) . '_' . md5($endpoint . serialize($params));
        return $key;
    }

    /**
     * Obter dados do cache
     */
    protected function getFromCache(string $cacheKey): mixed
    {
        return Cache::get($cacheKey);
    }

    /**
     * Armazenar dados no cache
     */
    protected function putInCache(string $cacheKey, mixed $data, ?int $ttl = null): void
    {
        $ttl = $ttl ?? $this->defaultCacheTtl;
        Cache::put($cacheKey, $data, $ttl);
    }

    /**
     * Executar com cache
     */
    protected function remember(string $cacheKey, callable $callback, ?int $ttl = null): mixed
    {
        $ttl = $ttl ?? $this->defaultCacheTtl;
        return Cache::remember($cacheKey, $ttl, $callback);
    }

    /**
     * Limpar cache específico
     */
    protected function forgetCache(string $cacheKey): void
    {
        Cache::forget($cacheKey);
    }

    /**
     * Limpar todo o cache do cliente
     */
    protected function flushClientCache(): void
    {
        // Implementação específica dependendo do driver de cache
        // Para simplificar, vamos usar tags se disponível
        if (method_exists(Cache::getStore(), 'tags')) {
            Cache::tags([$this->cachePrefix])->flush();
        }
    }

    /**
     * Verificar se deve usar cache
     */
    protected function shouldUseCache(string $method): bool
    {
        // Por padrão, apenas GET requests são cacheadas
        return strtoupper($method) === 'GET';
    }
} 