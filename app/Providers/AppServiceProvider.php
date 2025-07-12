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
        // Register API Client interface binding using new API management system
        $this->app->bind(
            \App\Services\ApiClient\Interfaces\ApiClientInterface::class,
            function ($app) {
                // Use the full API configuration
                $config = config('api');

                return new \App\Services\ApiClient\Providers\NodeApiClient($config);
            }
        );

        // Register specific NodeApiClient binding for direct injection
        $this->app->bind(
            \App\Services\ApiClient\Providers\NodeApiClient::class,
            function ($app) {
                // Use the full API configuration
                $config = config('api');

                return new \App\Services\ApiClient\Providers\NodeApiClient($config);
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
