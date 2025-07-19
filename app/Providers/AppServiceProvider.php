<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
