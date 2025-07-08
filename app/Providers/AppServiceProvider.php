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
                // Use new API management system
                $apiMode = config('api.mode', 'mock');
                
                if ($apiMode === 'mock') {
                    // Use mock configuration
                    $config = [
                        'base_url' => config('api.mock.base_url'),
                        'token' => '', // Mock API doesn't need token
                        'timeout' => 10,
                        'retries' => 1,
                        'cache_ttl' => 300,
                        'provider_name' => 'Mock API',
                    ];
                } else {
                    // Use external API configuration
                    $config = [
                        'base_url' => config('api.external.base_url'),
                        'token' => '', // JWT will be managed automatically
                        'timeout' => config('api.external.timeout', 30),
                        'retries' => config('api.external.retries', 3),
                        'cache_ttl' => config('api.cache_ttl', 300),
                        'provider_name' => 'External API',
                    ];
                }

                return new \App\Services\ApiClient\Providers\NodeApiClient($config);
            }
        );

        // Register specific NodeApiClient binding for direct injection
        $this->app->bind(
            \App\Services\ApiClient\Providers\NodeApiClient::class,
            function ($app) {
                // Use new API management system
                $apiMode = config('api.mode', 'mock');
                
                if ($apiMode === 'mock') {
                    // Use mock configuration
                    $config = [
                        'base_url' => config('api.mock.base_url'),
                        'token' => '', // Mock API doesn't need token
                        'timeout' => 10,
                        'retries' => 1,
                        'cache_ttl' => 300,
                        'provider_name' => 'Mock API',
                    ];
                } else {
                    // Use external API configuration
                    $config = [
                        'base_url' => config('api.external.base_url'),
                        'token' => '', // JWT will be managed automatically
                        'timeout' => config('api.external.timeout', 30),
                        'retries' => config('api.external.retries', 3),
                        'cache_ttl' => config('api.cache_ttl', 300),
                        'provider_name' => 'External API',
                    ];
                }

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
