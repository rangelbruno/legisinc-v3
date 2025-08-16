<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use App\Services\Performance\CacheService;
use App\Services\Performance\QueryOptimizationService;
use App\Services\Performance\PDFOptimizationService;
use App\Observers\ProposicaoObserver;
use App\Models\Proposicao;

class PerformanceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar services de performance
        $this->app->singleton(CacheService::class);
        $this->app->singleton(QueryOptimizationService::class);
        $this->app->singleton(PDFOptimizationService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Registrar observer para invalidação automática de cache
        Proposicao::observe(ProposicaoObserver::class);

        // Configurar otimizações de banco em produção
        if ($this->app->environment('production')) {
            $this->configureDatabaseOptimizations();
        }

        // Configurar query logging para desenvolvimento
        if ($this->app->environment('local', 'staging')) {
            $this->configureQueryLogging();
        }

        // Compartilhar dados de cache com views
        $this->sharePerformanceDataWithViews();

        // Configurar compressão de resposta
        $this->configureResponseCompression();
    }

    /**
     * Configurar otimizações de banco de dados
     */
    private function configureDatabaseOptimizations(): void
    {
        DB::listen(function ($query) {
            // Log queries lentas em produção
            if ($query->time > 1000) { // > 1 segundo
                \Log::warning('Slow query detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time
                ]);
            }
        });

        // Configurar connection pooling
        config([
            'database.connections.pgsql.options' => [
                \PDO::ATTR_PERSISTENT => true,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_STRINGIFY_FETCHES => false,
            ]
        ]);
    }

    /**
     * Configurar logging de queries para desenvolvimento
     */
    private function configureQueryLogging(): void
    {
        DB::enableQueryLog();

        // Log todas as queries em desenvolvimento
        DB::listen(function ($query) {
            if (request()->has('debug_queries')) {
                \Log::info('Query executed', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time
                ]);
            }
        });
    }

    /**
     * Compartilhar dados de performance com views
     */
    private function sharePerformanceDataWithViews(): void
    {
        View::composer('*', function ($view) {
            // Compartilhar estatísticas básicas de cache
            $view->with('cacheStats', [
                'enabled' => Cache::getStore() instanceof \Illuminate\Cache\RedisStore,
                'driver' => config('cache.default')
            ]);
        });
    }

    /**
     * Configurar compressão de resposta
     */
    private function configureResponseCompression(): void
    {
        if (!$this->app->environment('local')) {
            // Habilitar compressão Gzip
            if (function_exists('ob_gzhandler') && !ob_get_level()) {
                ob_start('ob_gzhandler');
            }
        }
    }
}