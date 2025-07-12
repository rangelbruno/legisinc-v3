@extends('components.layouts.app')

@section('title', 'Demo - Sistema de Alertas Melhorado')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Demo - Sistema de Alertas Melhorado
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Demo Alertas</li>
                </ul>
            </div>
            <!--end::Page title-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Bootstrap Alerts with Auto-Hide & Dismiss-->
            <div class="card mb-10">
                <div class="card-header">
                    <h3 class="card-title">Alertas Bootstrap Melhorados</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-5">Estes alertas agora possuem botão de fechar e some automaticamente após 5 segundos:</p>
                    
                    <div class="alert alert-success d-flex align-items-center p-5 mb-5">
                        <i class="ki-duotone ki-check-circle fs-2hx text-success me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-dark">Operação realizada com sucesso!</h4>
                            <span>Este alerta vai desaparecer automaticamente em 5 segundos, mas você pode fechá-lo clicando no X.</span>
                        </div>
                    </div>

                    <div class="alert alert-warning d-flex align-items-center p-5 mb-5">
                        <i class="ki-duotone ki-information fs-2hx text-warning me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-dark">Atenção!</h4>
                            <span>Este é um alerta de aviso que desaparece automaticamente.</span>
                        </div>
                    </div>

                    <div class="alert alert-info d-flex align-items-center p-5 mb-5">
                        <i class="ki-duotone ki-information fs-2hx text-info me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-dark">Informação</h4>
                            <span>Alerta informativo com dismiss automático e manual.</span>
                        </div>
                    </div>

                    <div class="alert alert-danger d-flex align-items-center p-5 mb-5">
                        <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-dark">Erro!</h4>
                            <span>Algo deu errado. Este alerta também desaparece automaticamente.</span>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Bootstrap Alerts-->

            <!--begin::Toast Notifications Demo-->
            <div class="card mb-10">
                <div class="card-header">
                    <h3 class="card-title">Notificações Toast Modernas</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-5">Clique nos botões abaixo para testar as notificações toast:</p>
                    
                    <div class="d-flex flex-wrap gap-3 mb-5">
                        <button type="button" class="btn btn-success" onclick="showAlert.success('Operação realizada com sucesso!')">
                            <i class="ki-duotone ki-check-circle fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Toast Sucesso
                        </button>
                        
                        <button type="button" class="btn btn-danger" onclick="showAlert.error('Ocorreu um erro inesperado!')">
                            <i class="ki-duotone ki-cross-circle fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Toast Erro
                        </button>
                        
                        <button type="button" class="btn btn-warning" onclick="showAlert.warning('Atenção! Verifique os dados antes de continuar.')">
                            <i class="ki-duotone ki-information fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            Toast Aviso
                        </button>
                        
                        <button type="button" class="btn btn-info" onclick="showAlert.info('Informação importante para o usuário.')">
                            <i class="ki-duotone ki-information fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            Toast Info
                        </button>
                        
                        <button type="button" class="btn btn-primary" onclick="showAlert.primary('Notificação do sistema.')">
                            <i class="ki-duotone ki-notification-bing fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            Toast Primary
                        </button>
                    </div>

                    <div class="separator mb-5"></div>

                    <h4 class="mb-3">Opções Avançadas:</h4>
                    <div class="d-flex flex-wrap gap-3">
                        <button type="button" class="btn btn-light-success" onclick="testCustomTimeout()">
                            Toast 10 segundos
                        </button>
                        
                        <button type="button" class="btn btn-light-warning" onclick="testNoTimeout()">
                            Toast sem timeout
                        </button>
                        
                        <button type="button" class="btn btn-light-info" onclick="testCustomTitle()">
                            Toast título customizado
                        </button>
                        
                        <button type="button" class="btn btn-light-danger" onclick="window.AlertManager.clearAllToasts()">
                            <i class="ki-duotone ki-trash fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                            Limpar Todos
                        </button>
                    </div>
                </div>
            </div>
            <!--end::Toast Notifications Demo-->

            <!--begin::Alert Persistence Demo-->
            <div class="card mb-10">
                <div class="card-header">
                    <h3 class="card-title">Alertas Persistentes</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-5">Alertas que não desaparecem automaticamente (útil para formulários):</p>
                    
                    <div class="alert alert-primary d-flex align-items-center p-5 mb-5" data-no-auto-hide="true">
                        <i class="ki-duotone ki-information fs-2hx text-primary me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-dark">Alerta Persistente</h4>
                            <span>Este alerta não desaparece automaticamente (attribute data-no-auto-hide="true"), mas ainda pode ser fechado manualmente.</span>
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary" onclick="addDynamicAlert()">
                        <i class="ki-duotone ki-plus fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Adicionar Alerta Dinâmico
                    </button>
                </div>
            </div>
            <!--end::Alert Persistence Demo-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

@push('scripts')
<script>
// Test functions for advanced toast options
function testCustomTimeout() {
    showAlert.warning('Este toast vai desaparecer em 10 segundos!', { timeout: 10000 });
}

function testNoTimeout() {
    showAlert.info('Este toast não vai desaparecer automaticamente.', { timeout: 0 });
}

function testCustomTitle() {
    showAlert.success('Usuário criado com sucesso!', { 
        title: 'Sistema de Usuários',
        timeout: 7000 
    });
}

function addDynamicAlert() {
    const alertHtml = `
        <div class="alert alert-success d-flex align-items-center p-5 mb-5">
            <i class="ki-duotone ki-check-circle fs-2hx text-success me-4">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            <div class="d-flex flex-column">
                <h4 class="mb-1 text-dark">Alerta Adicionado Dinamicamente!</h4>
                <span>Este alerta foi criado via JavaScript e automaticamente recebeu funcionalidade de dismiss e auto-hide.</span>
            </div>
        </div>
    `;
    
    const container = document.querySelector('.card-body');
    container.insertAdjacentHTML('beforeend', alertHtml);
}

// Demo of multiple toasts
document.addEventListener('DOMContentLoaded', function() {
    // Show a welcome message
    setTimeout(() => {
        showAlert.info('Bem-vindo à demonstração do sistema de alertas melhorado!', {
            title: 'Sistema LegisPro',
            timeout: 8000
        });
    }, 1000);
});
</script>
@endpush
@endsection