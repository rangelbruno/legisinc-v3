<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Aplicação migrada para PostgreSQL
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configurar paginação padrão
        Paginator::defaultView('pagination.bootstrap-4');
        Paginator::defaultSimpleView('pagination.bootstrap-4');

        // Prevenção N+1 queries em desenvolvimento
        Model::preventLazyLoading(! $this->app->isProduction());

        // Log personalizado para violações de lazy loading
        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) {
            $class = $model::class;
            \Log::warning("Lazy loading violation: [{$relation}] on model [{$class}].");
        });

        // Registrar View Composer para notificações
        View::composer([
            'components.layouts.header',
            'components.layouts.app',
            'layouts.app',
        ], \App\Http\View\Composers\NotificationComposer::class);

        // Configurar rate limiters
        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Registrar comandos Artisan do sistema de parâmetros
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\ParametrosCriar::class,
                \App\Console\Commands\ParametrosMigrarExistentes::class,
                \App\Console\Commands\ParametrosLimparCache::class,
                \App\Console\Commands\ParametrosValidarTodos::class,
                \App\Console\Commands\ParametrosSeed::class,
            ]);
        }
    }
}
