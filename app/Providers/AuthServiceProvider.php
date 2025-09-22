<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Documento\DocumentoModelo;
use App\Models\Documento\DocumentoInstancia;
use App\Models\SessaoPlenaria;
use App\Models\ParecerJuridico;
use App\Models\Workflow;
use App\Policies\DocumentoModeloPolicy;
use App\Policies\DocumentoInstanciaPolicy;
use App\Policies\ExpedientePolicy;
use App\Policies\ParecerJuridicoPolicy;
use App\Policies\WorkflowPolicy;

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
        Workflow::class => WorkflowPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // 🏛️ GATES DE WORKFLOW - Ações de transição entre etapas
        Gate::define('workflow.aprovar', [WorkflowPolicy::class, 'aprovar']);
        Gate::define('workflow.devolver', [WorkflowPolicy::class, 'devolver']);
        Gate::define('workflow.solicitar_alteracoes', [WorkflowPolicy::class, 'solicitar_alteracoes']);
        Gate::define('workflow.assinar', [WorkflowPolicy::class, 'assinar']);
        Gate::define('workflow.protocolar', [WorkflowPolicy::class, 'protocolar']);
        Gate::define('workflow.finalizar', [WorkflowPolicy::class, 'finalizar']);
        Gate::define('workflow.arquivar', [WorkflowPolicy::class, 'arquivar']);
        Gate::define('workflow.enviar_legislativo', [WorkflowPolicy::class, 'enviar_legislativo']);
        Gate::define('workflow.enviar_protocolo', [WorkflowPolicy::class, 'enviar_protocolo']);
        Gate::define('workflow.devolver_edicao', [WorkflowPolicy::class, 'devolver_edicao']);
        Gate::define('workflow.salvar_rascunho', [WorkflowPolicy::class, 'salvar_rascunho']);

        // 🔧 GATES DE CONTROLE DE WORKFLOW
        Gate::define('workflow.pause', [WorkflowPolicy::class, 'pause']);
        Gate::define('workflow.resume', [WorkflowPolicy::class, 'resume']);
        Gate::define('workflow.view_history', [WorkflowPolicy::class, 'viewHistory']);
        Gate::define('workflow.view_status', [WorkflowPolicy::class, 'viewStatus']);

        // 📊 GATES DE ADMINISTRAÇÃO DE WORKFLOWS (Admin only)
        Gate::define('workflow.manage', function ($user) {
            return $user->hasRole('Admin');
        });

        Gate::define('workflow.create_new', function ($user) {
            return $user->hasRole('Admin');
        });

        Gate::define('workflow.edit_structure', function ($user) {
            return $user->hasRole('Admin');
        });

        Gate::define('workflow.set_default', function ($user) {
            return $user->hasRole('Admin');
        });

        Gate::define('workflow.toggle_active', function ($user) {
            return $user->hasRole('Admin');
        });

        // 👀 GATES DE VISUALIZAÇÃO
        Gate::define('workflow.view_all', function ($user) {
            return $user->hasAnyRole(['Admin', 'Legislativo']);
        });

        Gate::define('workflow.view_designer', function ($user) {
            return $user->hasRole('Admin');
        });

        // 📈 GATES DE RELATÓRIOS
        Gate::define('workflow.reports', function ($user) {
            return $user->hasAnyRole(['Admin', 'Legislativo', 'Expediente']);
        });

        Gate::define('workflow.analytics', function ($user) {
            return $user->hasRole('Admin');
        });

        // 📄 GATE PARA EXPORTAÇÃO PDF ONLYOFFICE
        Gate::define('edit-onlyoffice', function ($user, \App\Models\Proposicao $proposicao) {
            // Permitir usuários com roles LEGISLATIVO, PARLAMENTAR ou ADMIN para exportar PDF
            return $user->hasAnyRole(['LEGISLATIVO', 'PARLAMENTAR', 'ADMIN']);
        });
    }
}