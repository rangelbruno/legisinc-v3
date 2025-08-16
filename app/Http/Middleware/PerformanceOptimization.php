<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PerformanceOptimization
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        $startQueries = $this->getQueryCount();

        // Configurar otimizações de conexão DB
        $this->optimizeDatabaseConnection();

        // Executar request
        $response = $next($request);

        // Coletar métricas de performance
        $this->collectPerformanceMetrics($request, $startTime, $startMemory, $startQueries);

        // Adicionar headers de cache se aplicável
        $this->addCacheHeaders($response, $request);

        return $response;
    }

    /**
     * Otimizar configurações de conexão do banco
     */
    private function optimizeDatabaseConnection(): void
    {
        // Configurar connection pooling e timeouts otimizados
        DB::statement('SET SESSION query_cache_type = ON');
        DB::statement('SET SESSION query_cache_size = 1048576'); // 1MB
    }

    /**
     * Coletar métricas de performance
     */
    private function collectPerformanceMetrics(Request $request, float $startTime, int $startMemory, int $startQueries): void
    {
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        $endQueries = $this->getQueryCount();

        $metrics = [
            'route' => $request->route()?->getName() ?? $request->path(),
            'method' => $request->method(),
            'execution_time' => round(($endTime - $startTime) * 1000, 2), // ms
            'memory_usage' => round(($endMemory - $startMemory) / 1024 / 1024, 2), // MB
            'peak_memory' => round(memory_get_peak_usage(true) / 1024 / 1024, 2), // MB
            'query_count' => $endQueries - $startQueries,
            'timestamp' => now()->toISOString()
        ];

        // Log requests lentos
        if ($metrics['execution_time'] > 1000) { // > 1 segundo
            Log::warning('Slow request detected', $metrics);
        }

        // Log muitas queries (possível N+1)
        if ($metrics['query_count'] > 20) {
            Log::warning('High query count detected', $metrics);
        }

        // Armazenar métricas para monitoramento (apenas em produção)
        if (app()->environment('production')) {
            $this->storeMetrics($metrics);
        }
    }

    /**
     * Adicionar headers de cache apropriados
     */
    private function addCacheHeaders(Response $response, Request $request): void
    {
        // Assets estáticos
        if ($this->isStaticAsset($request)) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000'); // 1 ano
            $response->headers->set('Expires', now()->addYear()->toRfc7231String());
            return;
        }

        // APIs e rotas de dados
        if ($this->isCacheableRoute($request)) {
            $response->headers->set('Cache-Control', 'public, max-age=300'); // 5 minutos
            $response->headers->set('Vary', 'Accept, Authorization');
        }

        // Rotas que não devem ser cachadas
        if ($this->isNonCacheableRoute($request)) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }
    }

    /**
     * Verificar se é um asset estático
     */
    private function isStaticAsset(Request $request): bool
    {
        $path = $request->path();
        $staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf'];
        
        return collect($staticExtensions)->some(function ($ext) use ($path) {
            return str_ends_with($path, ".{$ext}");
        });
    }

    /**
     * Verificar se a rota pode ser cacheada
     */
    private function isCacheableRoute(Request $request): bool
    {
        $cacheableRoutes = [
            'api.proposicoes.index',
            'api.tipos-proposicao.index',
            'dashboard.stats'
        ];

        $routeName = $request->route()?->getName();
        
        return in_array($routeName, $cacheableRoutes) && $request->method() === 'GET';
    }

    /**
     * Verificar se a rota NÃO deve ser cacheada
     */
    private function isNonCacheableRoute(Request $request): bool
    {
        $nonCacheableRoutes = [
            'proposicoes.store',
            'proposicoes.update',
            'login',
            'logout',
            'onlyoffice.*',
            '*.callback'
        ];

        $routeName = $request->route()?->getName() ?? '';
        
        return collect($nonCacheableRoutes)->some(function ($pattern) use ($routeName) {
            return str_is($pattern, $routeName);
        });
    }

    /**
     * Obter contagem de queries executadas
     */
    private function getQueryCount(): int
    {
        return count(DB::getQueryLog());
    }

    /**
     * Armazenar métricas para análise
     */
    private function storeMetrics(array $metrics): void
    {
        try {
            // Armazenar em cache Redis com TTL curto para análise
            $key = 'performance_metrics_' . date('Y_m_d_H');
            $existingMetrics = Cache::get($key, []);
            $existingMetrics[] = $metrics;
            
            // Manter apenas as últimas 1000 métricas por hora
            if (count($existingMetrics) > 1000) {
                $existingMetrics = array_slice($existingMetrics, -1000);
            }
            
            Cache::put($key, $existingMetrics, 3600); // 1 hora
        } catch (\Exception $e) {
            // Não falhar a request se não conseguir salvar métricas
            Log::error('Erro ao salvar métricas de performance: ' . $e->getMessage());
        }
    }
}