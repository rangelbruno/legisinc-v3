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
        // API removida - usando apenas SQLite diretamente
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
