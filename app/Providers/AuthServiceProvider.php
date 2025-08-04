<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Documento\DocumentoModelo;
use App\Models\Documento\DocumentoInstancia;
use App\Models\SessaoPlenaria;
use App\Models\ParecerJuridico;
use App\Policies\DocumentoModeloPolicy;
use App\Policies\DocumentoInstanciaPolicy;
use App\Policies\ExpedientePolicy;
use App\Policies\ParecerJuridicoPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        DocumentoModelo::class => DocumentoModeloPolicy::class,
        DocumentoInstancia::class => DocumentoInstanciaPolicy::class,
        SessaoPlenaria::class => ExpedientePolicy::class,
        ParecerJuridico::class => ParecerJuridicoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}