<?php

namespace App\Providers;

use App\Services\OnlyOffice\OnlyOfficeService;
use App\Services\Template\TemplateParametrosService;
use App\Services\Template\TemplateProcessorService;
use App\Services\Template\TemplateUniversalService;
use Illuminate\Support\ServiceProvider;

class OnlyOfficeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(OnlyOfficeService::class, function ($app) {
            return new OnlyOfficeService(
                $app->make(TemplateParametrosService::class),
                $app->make(TemplateProcessorService::class),
                $app->make(TemplateUniversalService::class)
            );
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/onlyoffice.php' => config_path('onlyoffice.php'),
        ], 'onlyoffice-config');

        // Criar diretórios necessários
        $storagePath = storage_path('app/onlyoffice');
        if (! is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }
    }
}
