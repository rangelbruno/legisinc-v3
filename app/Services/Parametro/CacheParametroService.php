<?php

namespace App\Services\Parametro;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class CacheParametroService
{
    private const CACHE_PREFIX = 'parametros:';
    private const DEFAULT_TTL = 3600; // 1 hora
    private const TAGS = [
        'modulos' => 'parametros_modulos',
        'submodulos' => 'parametros_submodulos',
        'campos' => 'parametros_campos',
        'valores' => 'parametros_valores',
        'configuracoes' => 'parametros_configuracoes'
    ];

    /**
     * Gera chave de cache padronizada
     */
    private function generateKey(string $type, ...$params): string
    {
        $key = self::CACHE_PREFIX . $type;
        
        foreach ($params as $param) {
            $key .= ':' . $param;
        }
        
        return $key;
    }

    /**
     * Verifica se o driver de cache suporta tagging
     */
    private function supportsTagging(): bool
    {
        try {
            $store = Cache::getStore();
            // Apenas Redis e alguns outros drivers suportam tagging
            return $store instanceof \Illuminate\Cache\RedisStore ||
                   $store instanceof \Illuminate\Cache\MemcachedStore ||
                   (method_exists($store, 'supportsTags') && $store->supportsTags());
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Cache para módulos
     */
    public function rememberModulos(callable $callback, int $ttl = null): mixed
    {
        $key = $this->generateKey('modulos');
        
        if ($this->supportsTagging()) {
            return Cache::tags([self::TAGS['modulos']])
                ->remember($key, $ttl ?? self::DEFAULT_TTL, $callback);
        }
        
        return Cache::remember($key, $ttl ?? self::DEFAULT_TTL, $callback);
    }

    /**
     * Cache para submódulos de um módulo específico
     */
    public function rememberSubmodulos(int $moduloId, callable $callback, int $ttl = null): mixed
    {
        $key = $this->generateKey('submodulos', $moduloId);
        
        if ($this->supportsTagging()) {
            return Cache::tags([self::TAGS['submodulos'], self::TAGS['modulos']])
                ->remember($key, $ttl ?? self::DEFAULT_TTL, $callback);
        }
        
        return Cache::remember($key, $ttl ?? self::DEFAULT_TTL, $callback);
    }

    /**
     * Cache para campos de um submódulo específico
     */
    public function rememberCampos(int $submoduloId, callable $callback, int $ttl = null): mixed
    {
        $key = $this->generateKey('campos', $submoduloId);
        
        if ($this->supportsTagging()) {
            return Cache::tags([self::TAGS['campos'], self::TAGS['submodulos']])
                ->remember($key, $ttl ?? self::DEFAULT_TTL, $callback);
        }
        
        return Cache::remember($key, $ttl ?? self::DEFAULT_TTL, $callback);
    }

    /**
     * Cache para configurações de módulo/submódulo
     */
    public function rememberConfiguracoes(string $modulo, string $submodulo, callable $callback, int $ttl = null): mixed
    {
        $key = $this->generateKey('config', $modulo, $submodulo);
        
        if ($this->supportsTagging()) {
            return Cache::tags([self::TAGS['configuracoes'], self::TAGS['valores']])
                ->remember($key, $ttl ?? self::DEFAULT_TTL, $callback);
        }
        
        return Cache::remember($key, $ttl ?? self::DEFAULT_TTL, $callback);
    }

    /**
     * Cache para valor específico
     */
    public function rememberValor(string $modulo, string $submodulo, string $campo, callable $callback, int $ttl = null): mixed
    {
        $key = $this->generateKey('valor', $modulo, $submodulo, $campo);
        
        if ($this->supportsTagging()) {
            return Cache::tags([self::TAGS['valores']])
                ->remember($key, $ttl ?? (self::DEFAULT_TTL * 2), $callback);
        }
        
        return Cache::remember($key, $ttl ?? (self::DEFAULT_TTL * 2), $callback);
    }

    /**
     * Cache para valores de um submódulo específico
     */
    public function rememberValores(int $submoduloId, callable $callback, int $ttl = null): mixed
    {
        $key = $this->generateKey('valores', $submoduloId);
        
        if ($this->supportsTagging()) {
            return Cache::tags([self::TAGS['valores'], self::TAGS['submodulos']])
                ->remember($key, $ttl ?? self::DEFAULT_TTL, $callback);
        }
        
        return Cache::remember($key, $ttl ?? self::DEFAULT_TTL, $callback);
    }

    /**
     * Invalida cache de módulos
     */
    public function invalidateModulos(): void
    {
        try {
            if ($this->supportsTagging()) {
                Cache::tags([self::TAGS['modulos']])->flush();
            } else {
                Cache::forget($this->generateKey('modulos'));
            }
            $this->logCacheOperation('invalidate', 'modulos');
        } catch (\Exception $e) {
            Log::error("Erro ao invalidar cache de módulos: {$e->getMessage()}");
        }
    }

    /**
     * Invalida cache de submódulos
     */
    public function invalidateSubmodulos(int $moduloId = null): void
    {
        try {
            if ($moduloId) {
                Cache::forget($this->generateKey('submodulos', $moduloId));
            } else {
                if ($this->supportsTagging()) {
                    Cache::tags([self::TAGS['submodulos']])->flush();
                } else {
                    // Para cache sem tagging, seria necessário manter lista de chaves
                    // Por simplicidade, não fazemos flush completo
                }
            }
            $this->logCacheOperation('invalidate', 'submodulos', $moduloId);
        } catch (\Exception $e) {
            Log::error("Erro ao invalidar cache de submódulos: {$e->getMessage()}");
        }
    }

    /**
     * Invalida cache de campos
     */
    public function invalidateCampos(int $submoduloId = null): void
    {
        try {
            if ($submoduloId) {
                Cache::forget($this->generateKey('campos', $submoduloId));
            } else {
                if ($this->supportsTagging()) {
                    Cache::tags([self::TAGS['campos']])->flush();
                }
            }
            $this->logCacheOperation('invalidate', 'campos', $submoduloId);
        } catch (\Exception $e) {
            Log::error("Erro ao invalidar cache de campos: {$e->getMessage()}");
        }
    }

    /**
     * Invalida cache de configurações
     */
    public function invalidateConfiguracoes(string $modulo = null, string $submodulo = null): void
    {
        try {
            if ($modulo && $submodulo) {
                Cache::forget($this->generateKey('config', $modulo, $submodulo));
            } else {
                if ($this->supportsTagging()) {
                    Cache::tags([self::TAGS['configuracoes']])->flush();
                }
            }
            $this->logCacheOperation('invalidate', 'configuracoes', $modulo, $submodulo);
        } catch (\Exception $e) {
            Log::error("Erro ao invalidar cache de configurações: {$e->getMessage()}");
        }
    }

    /**
     * Invalida cache de valores
     */
    public function invalidateValores(string $modulo = null, string $submodulo = null, string $campo = null): void
    {
        try {
            if ($modulo && $submodulo && $campo) {
                Cache::forget($this->generateKey('valor', $modulo, $submodulo, $campo));
            } else {
                if ($this->supportsTagging()) {
                    Cache::tags([self::TAGS['valores']])->flush();
                }
            }
            $this->logCacheOperation('invalidate', 'valores', $modulo, $submodulo, $campo);
        } catch (\Exception $e) {
            Log::error("Erro ao invalidar cache de valores: {$e->getMessage()}");
        }
    }

    /**
     * Invalida todo cache de parâmetros
     */
    public function invalidateAll(): void
    {
        try {
            if ($this->supportsTagging()) {
                foreach (self::TAGS as $tag) {
                    Cache::tags([$tag])->flush();
                }
            } else {
                // Para cache sem tagging, limpar apenas chaves conhecidas
                Cache::forget($this->generateKey('modulos'));
            }
            $this->logCacheOperation('invalidate_all');
        } catch (\Exception $e) {
            Log::error("Erro ao invalidar todo cache: {$e->getMessage()}");
        }
    }

    /**
     * Obtém estatísticas do cache
     */
    public function getCacheStats(): array
    {
        try {
            $stats = [
                'total_keys' => 0,
                'by_type' => [],
                'memory_usage' => 0,
                'hit_rate' => 0
            ];

            // Se usando Redis, obter estatísticas mais detalhadas
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Redis::connection();
                $keys = $redis->keys(self::CACHE_PREFIX . '*');
                $stats['total_keys'] = count($keys);

                foreach ($keys as $key) {
                    $type = $this->extractTypeFromKey($key);
                    $stats['by_type'][$type] = ($stats['by_type'][$type] ?? 0) + 1;
                }

                $info = $redis->info('memory');
                $stats['memory_usage'] = $info['used_memory'] ?? 0;
            }

            return $stats;
        } catch (\Exception $e) {
            Log::error("Erro ao obter estatísticas do cache: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Aquece o cache com dados mais acessados
     */
    public function warmUpCache(): void
    {
        try {
            // Simular requests para os dados mais comuns
            $this->logCacheOperation('warmup_started');
            
            // Aqui você implementaria a lógica específica baseada nos padrões de uso
            // Por exemplo, carregar módulos ativos, submódulos principais, etc.
            
            $this->logCacheOperation('warmup_completed');
        } catch (\Exception $e) {
            Log::error("Erro no warm-up do cache: {$e->getMessage()}");
        }
    }

    /**
     * Extrai tipo da chave do cache
     */
    private function extractTypeFromKey(string $key): string
    {
        $parts = explode(':', str_replace(self::CACHE_PREFIX, '', $key));
        return $parts[0] ?? 'unknown';
    }

    /**
     * Log de operações de cache
     */
    private function logCacheOperation(string $operation, ...$params): void
    {
        Log::info("Cache operation: {$operation}", [
            'params' => $params,
            'timestamp' => now(),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Verifica se o cache está funcionando
     */
    public function healthCheck(): array
    {
        try {
            $testKey = $this->generateKey('health_check', time());
            $testValue = 'cache_test_' . rand(1000, 9999);
            
            // Teste de write/read
            Cache::put($testKey, $testValue, 60);
            $retrieved = Cache::get($testKey);
            
            $isWorking = $retrieved === $testValue;
            
            // Limpar teste
            Cache::forget($testKey);
            
            return [
                'status' => $isWorking ? 'healthy' : 'unhealthy',
                'driver' => Cache::getDefaultDriver(),
                'test_passed' => $isWorking,
                'timestamp' => now()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'timestamp' => now()
            ];
        }
    }
}