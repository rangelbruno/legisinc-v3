@extends('components.layouts.app')

@section('title', 'Testes de Processos - Sistema Parlamentar')

@push('styles')
<style>
    .workflow-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        padding: 30px;
        position: relative;
        overflow: hidden;
    }
    
    .workflow-step {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        position: relative;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .workflow-step.active {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border: 2px solid #007bff;
    }
    
    .workflow-step.completed {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border: 2px solid #28a745;
    }
    
    .workflow-step.pending {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border: 2px solid #ffc107;
    }
    
    .workflow-arrow {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 60px;
        position: relative;
    }
    
    .workflow-arrow::after {
        content: '';
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, #6c757d 0%, #007bff 50%, #6c757d 100%);
        position: relative;
        border-radius: 2px;
    }
    
    .workflow-arrow::before {
        content: '→';
        font-size: 24px;
        color: #007bff;
        background: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        z-index: 2;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    .document-icon {
        position: absolute;
        top: 50%;
        left: 0;
        transform: translateY(-50%);
        background: #007bff;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: moveDocument 8s linear infinite;
        z-index: 3;
        box-shadow: 0 2px 10px rgba(0,123,255,0.5);
    }
    
    @keyframes moveDocument {
        0% { left: 0; }
        25% { left: calc(25% - 15px); }
        50% { left: calc(50% - 15px); }
        75% { left: calc(75% - 15px); }
        100% { left: calc(100% - 30px); }
    }
    
    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
        margin-bottom: 10px;
    }
    
    .workflow-controls {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .step-indicator {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .step-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #6c757d;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    
    .step-number.active {
        background: #007bff;
        animation: pulse 1.5s infinite;
    }
    
    .step-number.completed {
        background: #28a745;
    }
</style>
@endpush

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Visualização de Processos de Tramitação
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('tests.index') }}" class="text-muted text-hover-primary">Testes</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Processos</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('tests.index') }}" class="btn btn-sm btn-light">
                    <i class="ki-duotone ki-arrows-left fs-4 me-1"></i>
                    Voltar aos Testes
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Visualização Avançada Card-->
            <div class="row g-5 g-xl-8 mb-8">
                <div class="col-xl-12">
                    <div class="card border-primary border-2">
                        <div class="card-header bg-light-primary">
                            <div class="card-title">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-5">
                                        <span class="symbol-label bg-primary text-white">
                                            <i class="ki-duotone ki-chart-line-up fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h3 class="fw-bold text-primary fs-4 mb-1">🎭 Sistema de Visualização Avançada</h3>
                                        <span class="text-muted fs-7">5 interfaces interativas para análise do fluxo legislativo</span>
                                        <span class="badge badge-primary ms-3">NOVO!</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-toolbar">
                                <a href="{{ route('tests.processes.visualization-center') }}" class="btn btn-primary btn-sm" target="_blank">
                                    <i class="ki-duotone ki-rocket fs-4 me-1"></i>
                                    Abrir Centro de Visualização
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-5">
                                <div class="col-lg-8">
                                    <p class="text-gray-600 fs-6 mb-6">
                                        Sistema completo de visualização do fluxo legislativo com 5 interfaces diferentes: 
                                        visualizador básico, dashboard avançado com D3.js, mapa de rede interativo, 
                                        animações cinematográficas e centro de controle centralizado.
                                    </p>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="ki-duotone ki-check-circle text-success fs-4 me-3"></i>
                                                <span class="fw-semibold">📊 Visualizador Básico</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="ki-duotone ki-check-circle text-success fs-4 me-3"></i>
                                                <span class="fw-semibold">🎛️ Dashboard D3.js Avançado</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="ki-duotone ki-check-circle text-success fs-4 me-3"></i>
                                                <span class="fw-semibold">🌐 Mapa de Rede Interativo</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="ki-duotone ki-check-circle text-success fs-4 me-3"></i>
                                                <span class="fw-semibold">🎭 Fluxo Animado GSAP</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="ki-duotone ki-check-circle text-success fs-4 me-3"></i>
                                                <span class="fw-semibold">🧪 Teste PHPUnit Completo</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="ki-duotone ki-check-circle text-success fs-4 me-3"></i>
                                                <span class="fw-semibold">📱 Responsive Design</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-4">
                                    <div class="bg-light rounded p-4">
                                        <h5 class="fw-bold mb-3">🎯 Estatísticas</h5>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Visualizações:</span>
                                            <span class="fw-bold text-primary">5</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Etapas do Fluxo:</span>
                                            <span class="fw-bold text-primary">9</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Tecnologias:</span>
                                            <span class="fw-bold text-primary">7</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-4">
                                            <span class="text-muted">Cobertura:</span>
                                            <span class="fw-bold text-success">100%</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="badge badge-light-primary">D3.js</span>
                                            <span class="badge badge-light-success">GSAP</span>
                                            <span class="badge badge-light-info">PHPUnit</span>
                                            <span class="badge badge-light-warning">HTML5</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex flex-wrap gap-3 mt-6">
                                <a href="{{ route('tests.processes.visualization-center') }}" class="btn btn-primary" target="_blank">
                                    <i class="ki-duotone ki-rocket fs-4 me-2"></i>
                                    Abrir Centro de Visualização
                                </a>
                                <a href="{{ route('tests.processes.fluxo-visualizer') }}" class="btn btn-light-primary" target="_blank">
                                    <i class="ki-duotone ki-chart-simple fs-4 me-2"></i>
                                    Visualizador Básico
                                </a>
                                <a href="{{ route('tests.processes.fluxo-dashboard') }}" class="btn btn-light-success" target="_blank">
                                    <i class="ki-duotone ki-element-11 fs-4 me-2"></i>
                                    Dashboard Avançado
                                </a>
                                <a href="{{ route('tests.processes.network-flow') }}" class="btn btn-light-info" target="_blank">
                                    <i class="ki-duotone ki-technology-2 fs-4 me-2"></i>
                                    Mapa de Rede
                                </a>
                                <a href="{{ route('tests.processes.animated-flow') }}" class="btn btn-light-warning" target="_blank">
                                    <i class="ki-duotone ki-picture fs-4 me-2"></i>
                                    Fluxo Animado
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Visualização Avançada Card-->

        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Página simplificada - apenas o card de visualização avançada
        console.log('Página de Testes de Processos carregada - Sistema de Visualização Avançada disponível');
    });
</script>
@endpush