<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;

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
        
        // Registrar View Composer para notificações
        View::composer([
            'components.layouts.header',
            'components.layouts.app',
            'layouts.app'
        ], \App\Http\View\Composers\NotificationComposer::class);

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
