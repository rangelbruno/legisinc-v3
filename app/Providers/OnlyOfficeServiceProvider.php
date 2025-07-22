<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\OnlyOffice\OnlyOfficeService;

class OnlyOfficeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(OnlyOfficeService::class, function ($app) {
            return new OnlyOfficeService();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/onlyoffice.php' => config_path('onlyoffice.php'),
        ], 'onlyoffice-config');

        // Criar diretórios necessários
        $storagePath = storage_path('app/onlyoffice');
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }
    }
}