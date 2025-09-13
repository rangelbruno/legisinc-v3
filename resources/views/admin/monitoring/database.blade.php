@extends('components.layouts.app')

@section('title', 'Monitoramento de Banco de Dados')

@section('content')

<style>
.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
}

.cursor-pointer {
    cursor: pointer;
}

.card-hover:hover .text-gray-900 {
    color: var(--kt-primary) !important;
}

.card-hover:hover i {
    color: var(--kt-primary) !important;
}

.stats-card {
    border: none;
    border-radius: 0.75rem;
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.monitoring-alert {
    border-radius: 0.75rem;
    border: none;
    backdrop-filter: blur(10px);
}

.nav-tabs {
    border-bottom: none;
    gap: 0.5rem;
}

.nav-tabs .nav-item {
    margin-bottom: 0;
}

.nav-tabs .nav-link {
    border: 2px solid transparent;
    border-radius: 0.75rem;
    padding: 1rem 1.5rem;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: #f8f9fa;
    color: #6c757d;
    position: relative;
    overflow: hidden;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 70px;
    margin-right: 0.5rem;
    margin-bottom: 0;
}

.nav-tabs .nav-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(241, 65, 108, 0.1) 0%, rgba(114, 57, 234, 0.1) 100%);
    transition: left 0.3s ease;
    z-index: 1;
}

.nav-tabs .nav-link:hover::before {
    left: 0;
}

.nav-tabs .nav-link:hover {
    border-color: var(--kt-primary);
    background: #ffffff;
    color: var(--kt-primary);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.nav-tabs .nav-link.active {
    background: linear-gradient(135deg, #F1416C 0%, #7239EA 100%);
    color: white;
    border-color: transparent;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(241, 65, 108, 0.3);
}

.nav-tabs .nav-link.active::before {
    display: none;
}

.nav-tabs .nav-link > * {
    position: relative;
    z-index: 2;
}

.nav-tabs .badge {
    font-size: 0.7rem;
    padding: 0.15rem 0.4rem;
    border-radius: 6px;
    font-weight: 600;
    min-width: 18px;
    text-align: center;
    margin-top: 0;
    border: none;
    background: #e9ecef;
    color: #6c757d;
}

.nav-tabs .nav-link.active .badge {
    background: var(--kt-primary) !important;
    color: white !important;
}

.nav-tabs .nav-link:hover .badge {
    background: #dee2e6 !important;
    color: #495057 !important;
}

/* Responsividade compacta */
@media (max-width: 992px) {
    .nav-tabs {
        gap: 1.5rem;
    }
    
    .nav-tabs .nav-link {
        padding: 1.25rem 1.5rem;
        min-height: 65px;
        min-width: 140px;
        max-width: 180px;
    }
}

@media (max-width: 768px) {
    .card-header.bg-light {
        padding: 1.5rem 1rem 1rem;
    }
    
    .card-header .d-flex {
        flex-direction: column;
        align-items: start !important;
        gap: 1rem;
    }
    
    .nav-tabs {
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .nav-tabs .nav-link {
        min-height: 60px;
        padding: 1rem 1.25rem;
        min-width: 120px;
        max-width: 160px;
    }
    
    .nav-tabs .nav-link .fw-bold {
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .nav-tabs {
        gap: 0.5rem;
        justify-content: stretch;
    }
    
    .nav-tabs .nav-item {
        flex: 1 1 auto;
    }
    
    .nav-tabs .nav-link {
        flex-direction: row;
        text-align: left;
        justify-content: space-between;
        align-items: center;
        min-height: 50px;
        min-width: auto;
        max-width: none;
        padding: 0.75rem 1rem;
    }
    
    .nav-tabs .nav-link .fw-bold {
        font-size: 0.85rem;
        margin-bottom: 0;
    }
    
    .nav-tabs .nav-link .fs-8 {
        display: none;
    }
    
    .nav-tabs .badge {
        margin-top: 0;
        margin-left: 0;
    }
}

/* Card header ultra minimalista */
.card-header.bg-light {
    background: #ffffff !important;
    border-bottom: 1px solid #e9ecef;
    padding: 2.5rem 2rem 1.5rem;
}

.card-header .card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #212529;
    letter-spacing: -0.01em;
}

.card-header .text-muted {
    font-size: 0.85rem;
    color: #8b949e !important;
    font-weight: 400;
}

.card-header .badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.5rem;
    font-weight: 600;
    border-radius: 4px;
}

/* Tabs compactas e proporcionais */
.nav-tabs {
    border-bottom: 1px solid #e9ecef;
    gap: 2rem;
    padding: 0;
    background: transparent;
    margin-bottom: 0;
    justify-content: center;
    display: flex;
}

.nav-tabs .nav-item {
    margin-bottom: 0;
    flex: 0 0 auto;
}

.nav-tabs .nav-link {
    border: none;
    border-bottom: 2px solid transparent;
    border-radius: 0;
    padding: 1.5rem 2rem;
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.15s ease;
    background: transparent;
    color: #8b949e;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 70px;
    min-width: 160px;
    max-width: 200px;
    margin: 0;
    position: relative;
    text-align: center;
    white-space: nowrap;
}

.nav-tabs .nav-link:hover {
    color: #495057;
    background: rgba(248, 249, 250, 0.5);
    border-bottom-color: #dee2e6;
}

.nav-tabs .nav-link.active {
    background: transparent;
    color: #212529;
    border-bottom-color: var(--kt-primary);
    font-weight: 600;
}

.nav-tabs .nav-link i {
    display: none;
}

.nav-tabs .nav-link .fw-bold {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    letter-spacing: 0.01em;
}

.nav-tabs .nav-link .fs-8 {
    font-size: 0.8rem;
    font-weight: 400;
    color: #8b949e;
    margin-bottom: 0.5rem;
}

.nav-tabs .nav-link.active .fs-8 {
    color: #6c757d;
}

.query-card {
    border: none;
    border-radius: 0.75rem;
    /* Transição removida para evitar animações */
    margin-bottom: 1rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.query-card:hover {
    /* Hover removido para reduzir animações */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.method-card {
    border: none;
    border-radius: 0.75rem;
    overflow: hidden;
    /* Transição removida para evitar animações */
}

.method-card:hover {
    /* Hover removido para reduzir animações */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.empty-state {
    padding: 3rem 2rem;
    text-align: center;
    border-radius: 0.75rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px dashed #dee2e6;
}

.empty-state i {
    opacity: 0.5;
}

/* Cards do Dashboard - Conforme guia */
.dashboard-card-primary {
    background: linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
    background-image: url("/assets/media/patterns/vector-1.png"), linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}

.dashboard-card-info {
    background: linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
    background-image: url("/assets/media/patterns/vector-1.png"), linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}

.dashboard-card-success {
    background: linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
    background-image: url("/assets/media/patterns/vector-1.png"), linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}

.dashboard-card-warning {
    background: linear-gradient(135deg, #FFC700 0%, #e6b300 100%) !important;
    background-image: url("/assets/media/patterns/vector-1.png"), linear-gradient(135deg, #FFC700 0%, #e6b300 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
</style>

<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    🗃️ Monitoramento de Banco de Dados
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Administração</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.monitoring.index') }}" class="text-muted text-hover-primary">Observabilidade</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Banco de Dados</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div class="text-end d-none d-sm-block">
                    <div class="text-muted fw-semibold fs-7" id="last-update">{{ date('d/m/Y H:i:s') }}</div>
                </div>
                <a href="{{ route('admin.monitoring.index') }}" class="btn btn-sm btn-light">
                    <i class="ki-duotone ki-left fs-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Voltar
                </a>
                <button type="button" class="btn btn-sm btn-success" id="start-monitoring" onclick="startMonitoring()">
                    <i class="ki-duotone ki-play fs-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Iniciar Monitoramento
                </button>
                <button type="button" class="btn btn-sm btn-danger d-none" id="stop-monitoring" onclick="stopMonitoring()">
                    <i class="ki-duotone ki-stop fs-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Parar Monitoramento
                </button>
                <button type="button" class="btn btn-sm btn-info" id="export-data" onclick="exportData()" disabled>
                    <i class="ki-duotone ki-file-down fs-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Exportar
                </button>
                <button type="button" class="btn btn-sm btn-warning" id="clear-data" onclick="clearData()" disabled>
                    <i class="ki-duotone ki-trash fs-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Limpar
                </button>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!-- Status do Monitoramento -->
            <div class="row g-5 g-xl-8 mb-5" id="monitoring-status" style="display: none;">
                <div class="col-12">
                    <div class="alert alert-success monitoring-alert d-flex align-items-center">
                        <i class="ki-duotone ki-check-circle fs-2x me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1">🔍 Monitoramento de Banco Ativo</h4>
                            <span id="monitoring-info">Capturando queries SQL em tempo real...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Widgets de Estatísticas - Dashboard Style -->
            <div class="row g-5 g-xl-8">
                
                <!-- Total de Queries -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-primary cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-chart-line-up text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2" id="total-queries">0</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">queries</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Total Executadas</span>
                                <span class="badge badge-light-primary fs-8" id="queries-percentage">100%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: 100%" id="queries-progress"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tempo Médio -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-warning cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-timer text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2" id="avg-time">0</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">ms</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Tempo Médio</span>
                                <span class="badge badge-light-warning fs-8" id="time-performance">Bom</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: 75%" id="time-progress"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Queries Lentas -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-primary cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-speed-down text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2" id="slow-queries">0</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">lentas</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Queries > 100ms</span>
                                <span class="badge badge-light-primary fs-8" id="slow-percentage">0%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: 0%" id="slow-progress"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela Principal -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-info cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-tablet-ok text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2" id="main-table-short">-</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">table</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white" id="main-table">Tabela Principal</span>
                                <span class="badge badge-light-info fs-8" id="table-usage">0%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: 60%" id="table-progress"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Row-->

            <!-- Seções com Tabs -->
            <div class="row g-5 g-xl-8 mt-5">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between mb-3">
                                <div>
                                    <h3 class="card-title fw-bold text-gray-800 mb-1">
                                        Análise Detalhada
                                    </h3>
                                    <p class="text-muted mb-0 fs-7">Visualize queries SQL, métodos HTTP e estatísticas em tempo real</p>
                                </div>
                                <div class="badge badge-light-success px-2 py-1 fs-8">
                                    Live
                                </div>
                            </div>
                            
                            <ul class="nav nav-tabs d-flex" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#queries-tab" aria-selected="true" role="tab">
                                        <i class="ki-duotone ki-search-list">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <span class="fw-bold">Queries</span>
                                        <span class="fs-8 text-center">Tempo Real</span>
                                        <span class="badge badge-sm badge-circle badge-light-primary" id="queries-count">0</span>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#methods-tab" aria-selected="false" tabindex="-1" role="tab">
                                        <i class="ki-duotone ki-route">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span class="fw-bold">HTTP → SQL</span>
                                        <span class="fs-8 text-center">Mapeamento</span>
                                        <span class="badge badge-sm badge-circle badge-light-warning" id="methods-count">0</span>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#stats-tab" aria-selected="false" tabindex="-1" role="tab">
                                        <i class="ki-duotone ki-chart-pie-simple">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span class="fw-bold">Estatísticas</span>
                                        <span class="fs-8 text-center">Detalhadas</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="monitoringTabContent">
                                
                                <!-- Tab: Queries em Tempo Real -->
                                <div class="tab-pane fade show active" id="queries-tab">
                                    <div id="queries-container">
                                        <div class="empty-state">
                                            <i class="ki-duotone ki-database fs-4x mb-4">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <h3 class="text-gray-800 mb-3">Inicie o monitoramento para ver as queries</h3>
                                            <p class="text-muted mb-4">Clique em "Iniciar Monitoramento" para capturar queries SQL em tempo real</p>
                                            <div class="d-flex justify-content-center">
                                                <div class="spinner-border text-primary" role="status" style="display: none;">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab: Métodos HTTP → SQL -->
                                <div class="tab-pane fade" id="methods-tab">
                                    <div id="methods-container">
                                        <div class="empty-state">
                                            <i class="ki-duotone ki-router fs-4x mb-4">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <h3 class="text-gray-800 mb-3">Mapeamento HTTP → SQL</h3>
                                            <p class="text-muted">Quando o monitoramento estiver ativo, você verá quais métodos HTTP geram quais operações SQL</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab: Estatísticas -->
                                <div class="tab-pane fade" id="stats-tab">
                                    <div id="stats-container">
                                        <div class="empty-state">
                                            <i class="ki-duotone ki-chart-pie-simple fs-4x mb-4">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <h3 class="text-gray-800 mb-3">Estatísticas detalhadas</h3>
                                            <p class="text-muted">Estatísticas por tipo de query, tabela e performance</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!--end::Container-->
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let monitoringActive = false;
let pollingInterval = null;
let httpMethodsMap = new Map();
let sessionId = null;
let hasData = false;
let isRequestInProgress = false;

// Performance optimization - debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Check status on page load with passive listeners
document.addEventListener('DOMContentLoaded', function() {
    checkMonitoringStatus();
}, { passive: true });

function checkMonitoringStatus() {
    fetch('{{ route("debug.status") }}')
        .then(response => response.json())
        .then(data => {
            if (data.active && data.db_capture_active) {
                monitoringActive = true;
                sessionId = data.session_id;
                updateUIForActiveMonitoring();
                startPolling();
            } else {
                // Check if there's cached data available for export
                checkDataAvailability();
            }
        })
        .catch(error => {
            console.error('Erro ao verificar status:', error);
        });
}

function checkDataAvailability() {
    fetch('{{ route("debug.database.queries") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.queries && data.queries.length > 0) {
                hasData = true;
                updateButtonStates();
            }
        })
        .catch(error => {
            // Silently ignore errors when checking data availability
        });
}

function startMonitoring() {
    const startButton = document.getElementById('start-monitoring');
    const stopButton = document.getElementById('stop-monitoring');
    
    startButton.disabled = true;
    startButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Iniciando...';
    
    fetch('{{ route("debug.start") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'started') {
            monitoringActive = true;
            sessionId = data.session_id;
            httpMethodsMap.clear();
            updateUIForActiveMonitoring();
            startPolling();
            clearQueryResults();
            showSuccess('Monitoramento iniciado com sucesso!');
        }
    })
    .catch(error => {
        console.error('Erro ao iniciar monitoramento:', error);
        showError('Erro ao iniciar monitoramento');
        startButton.disabled = false;
        startButton.innerHTML = '<i class="ki-duotone ki-play fs-5"><span class="path1"></span><span class="path2"></span></i> Iniciar Monitoramento';
    });
}

function stopMonitoring() {
    const startButton = document.getElementById('start-monitoring');
    const stopButton = document.getElementById('stop-monitoring');
    
    stopButton.disabled = true;
    stopButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Parando...';
    
    fetch('{{ route("debug.stop") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        monitoringActive = false;
        sessionId = null;
        updateUIForInactiveMonitoring();
        stopPolling();
        showSuccess('Monitoramento parado. Duração: ' + data.duration + ' segundos');
    })
    .catch(error => {
        console.error('Erro ao parar monitoramento:', error);
        showError('Erro ao parar monitoramento');
        stopButton.disabled = false;
        stopButton.innerHTML = '<i class="ki-duotone ki-stop fs-5"><span class="path1"></span><span class="path2"></span></i> Parar Monitoramento';
    });
}

function updateUIForActiveMonitoring() {
    document.getElementById('start-monitoring').classList.add('d-none');
    document.getElementById('stop-monitoring').classList.remove('d-none');
    document.getElementById('monitoring-status').style.display = 'block';
    document.getElementById('monitoring-info').textContent = `Sessão: ${sessionId} - Capturando dados...`;
    
    // Enable export and clear buttons
    updateButtonStates();
}

function updateUIForInactiveMonitoring() {
    document.getElementById('start-monitoring').classList.remove('d-none');
    document.getElementById('stop-monitoring').classList.add('d-none');
    document.getElementById('start-monitoring').disabled = false;
    document.getElementById('start-monitoring').innerHTML = '<i class="ki-duotone ki-play fs-5"><span class="path1"></span><span class="path2"></span></i> Iniciar Monitoramento';
    document.getElementById('stop-monitoring').disabled = false;
    document.getElementById('stop-monitoring').innerHTML = '<i class="ki-duotone ki-stop fs-5"><span class="path1"></span><span class="path2"></span></i> Parar Monitoramento';
    document.getElementById('monitoring-status').style.display = 'none';
    
    // Update button states based on data availability
    updateButtonStates();
}

// Debounced version of fetchQueries to prevent excessive requests
const debouncedFetchQueries = debounce(fetchQueries, 1000);

function startPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
    
    pollingInterval = setInterval(() => {
        if (monitoringActive) {
            debouncedFetchQueries();
        }
    }, 3000); // Poll every 3 seconds (reduced frequency)
}

function stopPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
}

function fetchQueries() {
    if (isRequestInProgress) {
        return Promise.resolve(); // Skip if already in progress
    }
    
    isRequestInProgress = true;
    
    return fetch('{{ route("debug.database.queries") }}')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                updateQueryDisplay(data.queries || []);
                updateStatistics(data.statistics || {}, data.queries || []);
                updateTimestamp();
                
                // Update data availability
                hasData = data.queries && data.queries.length > 0;
                updateButtonStates();
            } else {
                console.warn('Resposta de query sem sucesso:', data);
            }
        })
        .catch(error => {
            console.error('Erro ao buscar queries:', error);
            // Handle error gracefully without stopping the monitoring
            updateQueryDisplay([]);
            updateStatistics({}, []);
        })
        .finally(() => {
            isRequestInProgress = false;
        });
}

// Performance optimization: Track last query count to avoid unnecessary DOM updates
let lastQueryCount = 0;
let lastUpdateTime = 0;
const MIN_UPDATE_INTERVAL = 1000; // Minimum 1 second between DOM updates

function updateQueryDisplay(queries) {
    const container = document.getElementById('queries-container');
    
    // Performance optimization: Skip DOM updates if data hasn't changed much
    const now = Date.now();
    if (queries.length === lastQueryCount && now - lastUpdateTime < MIN_UPDATE_INTERVAL) {
        return;
    }
    
    lastQueryCount = queries.length;
    lastUpdateTime = now;
    
    if (queries.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-10">
                <i class="ki-duotone ki-database fs-4x mb-5">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <h3>Monitoramento ativo - aguardando queries</h3>
                <p>Execute algumas ações no sistema para ver as queries SQL</p>
            </div>
        `;
        return;
    }
    
    let html = '<div class="row g-5">';
    
    queries.slice(-10).reverse().forEach((query, index) => {
        if (!query) return;
        
        const performanceClass = getPerformanceClass(query.performance || 'fast');
        const typeClass = getTypeClass(query.type || 'select');
        
        html += `
            <div class="col-12">
                <div class="card query-card">
                    <div class="card-header py-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge badge-${typeClass} me-3 px-3 py-2">${query.type || 'UNKNOWN'}</span>
                                <span class="badge badge-${performanceClass} px-3 py-2">${query.time_formatted || '0ms'}</span>
                                ${(query.tables && query.tables.length > 0) ? `<span class="badge badge-light-info ms-2 px-3 py-2">${query.tables.join(', ')}</span>` : ''}
                            </div>
                            <small class="text-muted fw-semibold">${formatTimestamp(query.timestamp || new Date())}</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 p-3 bg-light rounded">
                            <code class="text-dark fs-7 fw-semibold">${escapeHtml(query.sql || query.formatted_sql || 'SQL não disponível')}</code>
                        </div>
                        ${query.backtrace && query.backtrace.length > 0 ? `
                            <div class="collapse" id="backtrace-${index}">
                                <div class="border-top pt-3 mt-3">
                                    <h6 class="text-gray-800 fw-bold mb-3">
                                        <i class="ki-duotone ki-code fs-5 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                        Backtrace
                                    </h6>
                                    <div class="bg-light p-3 rounded">
                                        ${query.backtrace.slice(0, 3).map(item => `
                                            <div class="text-muted fs-8 mb-1">
                                                <span class="text-primary">${item.file}:${item.line}</span> - 
                                                <span class="text-gray-700">${item.class}${item.function}</span>
                                            </div>
                                        `).join('')}
                                        ${query.backtrace.length > 3 ? `<div class="text-muted fs-8 fst-italic">... and ${query.backtrace.length - 3} more</div>` : ''}
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-light-primary" type="button" data-bs-toggle="collapse" data-bs-target="#backtrace-${index}" onclick="this.style.display='none'">
                                <i class="ki-duotone ki-eye fs-6 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                Ver Backtrace
                            </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
    
    // Update counters
    document.getElementById('queries-count').textContent = queries.length;
}

function updateStatistics(stats, queries) {
    const totalQueries = stats.total_queries || 0;
    const avgTime = Math.round(stats.average_time || 0);
    const slowQueries = (stats.slow_queries || 0) + (stats.very_slow_queries || 0);
    
    // Update total queries card
    document.getElementById('total-queries').textContent = totalQueries;
    
    // Update avg time card
    document.getElementById('avg-time').textContent = avgTime;
    const timePerformance = avgTime < 50 ? 'Excelente' : avgTime < 100 ? 'Bom' : avgTime < 200 ? 'Regular' : 'Lento';
    document.getElementById('time-performance').textContent = timePerformance;
    const timeProgress = Math.max(10, Math.min(100, 100 - (avgTime / 10)));
    document.getElementById('time-progress').style.width = timeProgress + '%';
    
    // Update slow queries card
    document.getElementById('slow-queries').textContent = slowQueries;
    const slowPercentage = totalQueries > 0 ? Math.round((slowQueries / totalQueries) * 100) : 0;
    document.getElementById('slow-percentage').textContent = slowPercentage + '%';
    document.getElementById('slow-progress').style.width = slowPercentage + '%';
    
    // Most used table
    const tables = stats.by_table || {};
    const tableEntries = Object.entries(tables);
    if (tableEntries.length > 0) {
        const [mostUsedTable, tableData] = tableEntries.reduce((a, b) => tables[a[0]].count > tables[b[0]].count ? a : b);
        const tableShortName = mostUsedTable.length > 8 ? mostUsedTable.substring(0, 8) + '...' : mostUsedTable;
        const tableUsage = Math.round((tableData.count / totalQueries) * 100) || 0;
        
        document.getElementById('main-table-short').textContent = tableShortName;
        document.getElementById('main-table').textContent = mostUsedTable;
        document.getElementById('table-usage').textContent = tableUsage + '%';
        document.getElementById('table-progress').style.width = Math.max(10, tableUsage) + '%';
    }
    
    // Update methods tab with queries data
    updateMethodsDisplay(stats, queries || []);
    
    // Update stats tab
    updateStatsDisplay(stats);
}

function updateMethodsDisplay(stats, queries) {
    const container = document.getElementById('methods-container');
    
    // Verify queries is an array
    if (!queries || !Array.isArray(queries)) {
        queries = [];
    }
    
    // Group queries by HTTP method and SQL operation
    const httpMethodMap = new Map();
    
    queries.forEach(query => {
        if (!query) return;
        
        const httpMethod = query.http_method || 'UNKNOWN';
        const sqlType = query.type || 'unknown';
        const route = query.route_name || 'N/A';
        
        const key = `${httpMethod}`;
        
        if (!httpMethodMap.has(key)) {
            httpMethodMap.set(key, {
                method: httpMethod,
                operations: new Map(),
                total_queries: 0,
                total_time: 0
            });
        }
        
        const methodData = httpMethodMap.get(key);
        methodData.total_queries++;
        methodData.total_time += (query.time || 0);
        
        if (!methodData.operations.has(sqlType)) {
            methodData.operations.set(sqlType, {
                count: 0,
                time: 0,
                tables: new Set(),
                routes: new Set()
            });
        }
        
        const operationData = methodData.operations.get(sqlType);
        operationData.count++;
        operationData.time += (query.time || 0);
        
        // Safely handle tables array
        const tables = query.tables || [];
        if (Array.isArray(tables)) {
            tables.forEach(table => operationData.tables.add(table));
        }
        
        if (route !== 'N/A') operationData.routes.add(route);
    });
    
    if (httpMethodMap.size === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-10">
                <i class="ki-duotone ki-router fs-4x mb-5">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <h3>Aguardando operações HTTP</h3>
                <p>Execute ações no sistema para ver o mapeamento HTTP → SQL</p>
            </div>
        `;
        return;
    }
    
    let html = '<div class="row g-5">';
    
    Array.from(httpMethodMap.values()).forEach(methodData => {
        const methodClass = getHttpMethodClass(methodData.method);
        
        html += `
            <div class="col-12">
                <div class="card method-card mb-4">
                    <div class="card-header bg-light-${methodClass} py-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge badge-${methodClass} badge-lg me-3 px-4 py-2 fs-6 fw-bold">${methodData.method}</span>
                                <div>
                                    <div class="text-gray-900 fw-bold fs-5">${methodData.total_queries} queries</div>
                                    <div class="text-muted fs-7 fw-semibold">${Math.round(methodData.total_time)}ms total</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <i class="ki-duotone ki-code fs-2x text-${methodClass}">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                                <div class="text-muted fs-8 mt-1">API Endpoint</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
        `;
        
        Array.from(methodData.operations.entries()).forEach(([sqlType, operationData]) => {
            const typeClass = getTypeClass(sqlType);
            const tables = Array.from(operationData.tables).slice(0, 3).join(', ');
            const routes = Array.from(operationData.routes).slice(0, 2);
            
            html += `
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light-${typeClass} rounded">
                        <span class="badge badge-${typeClass} me-3">${sqlType}</span>
                        <div class="flex-grow-1">
                            <div class="fw-bold">${operationData.count}x</div>
                            <div class="text-muted fs-8">${Math.round(operationData.time)}ms</div>
                            ${tables ? `<div class="text-muted fs-8 mt-1">📊 ${tables}</div>` : ''}
                            ${routes.length > 0 ? `<div class="text-primary fs-8 mt-1">🌐 ${routes.join(', ')}</div>` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += `
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
    
    // Update counter
    document.getElementById('methods-count').textContent = httpMethodMap.size;
}

function getHttpMethodClass(method) {
    switch(method) {
        case 'GET': return 'primary';
        case 'POST': return 'success';
        case 'PUT': 
        case 'PATCH': return 'warning';
        case 'DELETE': return 'danger';
        case 'OPTIONS': return 'info';
        case 'CLI': return 'dark';
        default: return 'secondary';
    }
}

function updateStatsDisplay(stats) {
    const container = document.getElementById('stats-container');
    const tables = stats.by_table || {};
    const types = stats.by_type || {};
    
    let html = `
        <div class="row g-5">
            <div class="col-md-6">
                <h5>📊 Por Tipo de Query</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Quantidade</th>
                                <th>Tempo Total</th>
                                <th>Tempo Médio</th>
                            </tr>
                        </thead>
                        <tbody>
    `;
    
    Object.entries(types || {}).forEach(([type, data]) => {
        const avgTime = data.count > 0 ? Math.round(data.time / data.count) : 0;
        html += `
            <tr>
                <td><span class="badge badge-${getTypeClass(type)}">${type}</span></td>
                <td>${data.count}</td>
                <td>${Math.round(data.time)}ms</td>
                <td>${avgTime}ms</td>
            </tr>
        `;
    });
    
    html += `
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <h5>🎯 Por Tabela</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tabela</th>
                                <th>Quantidade</th>
                                <th>Tempo Total</th>
                            </tr>
                        </thead>
                        <tbody>
    `;
    
    Object.entries(tables || {}).slice(0, 10).forEach(([table, data]) => {
        html += `
            <tr>
                <td><code>${table}</code></td>
                <td>${data.count}</td>
                <td>${Math.round(data.time)}ms</td>
            </tr>
        `;
    });
    
    html += `
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
}

function getPerformanceClass(performance) {
    switch(performance) {
        case 'excellent': return 'success';
        case 'good': return 'primary';
        case 'average': return 'warning';
        case 'slow': return 'danger';
        case 'very_slow': return 'dark';
        default: return 'secondary';
    }
}

function getTypeClass(type) {
    switch(type) {
        case 'SELECT': return 'primary';
        case 'INSERT': return 'success';
        case 'UPDATE': return 'warning';
        case 'DELETE': return 'danger';
        case 'CREATE': return 'info';
        case 'TRANSACTION': return 'secondary';
        default: return 'light';
    }
}

// Cache button state checks to prevent excessive API calls
let lastButtonStateCheck = 0;
const BUTTON_STATE_CHECK_INTERVAL = 5000; // 5 seconds

function updateButtonStates() {
    const exportButton = document.getElementById('export-data');
    const clearButton = document.getElementById('clear-data');
    
    // Se o monitoramento está ativo, sempre habilitar os botões
    if (monitoringActive) {
        exportButton.disabled = false;
        clearButton.disabled = false;
        return;
    }
    
    // Throttle API calls for button state checking
    const now = Date.now();
    if (now - lastButtonStateCheck < BUTTON_STATE_CHECK_INTERVAL) {
        // Use cached state
        exportButton.disabled = !hasData;
        clearButton.disabled = !hasData;
        return;
    }
    
    lastButtonStateCheck = now;
    
    // Se não está ativo, verificar se há dados no servidor para sincronizar estado
    fetch('{{ route("debug.database.queries") }}')
        .then(response => response.json())
        .then(data => {
            const hasServerData = data.success && data.queries && data.queries.length > 0;
            
            // Atualizar variável local com estado real do servidor
            hasData = hasServerData;
            
            // Export button: enabled if there's data available
            exportButton.disabled = !hasServerData;
            
            // Clear button: enabled if there's data available
            clearButton.disabled = !hasServerData;
        })
        .catch(error => {
            console.error('Error checking server data state:', error);
            // Em caso de erro, usar estado local como fallback
            exportButton.disabled = !hasData;
            clearButton.disabled = !hasData;
        });
}

function clearQueryResults() {
    // Reset dashboard cards
    document.getElementById('total-queries').textContent = '0';
    document.getElementById('avg-time').textContent = '0';
    document.getElementById('slow-queries').textContent = '0';
    document.getElementById('main-table-short').textContent = '-';
    document.getElementById('main-table').textContent = 'Tabela Principal';
    
    // Reset badges and progress bars
    document.getElementById('time-performance').textContent = 'Bom';
    document.getElementById('slow-percentage').textContent = '0%';
    document.getElementById('table-usage').textContent = '0%';
    
    document.getElementById('time-progress').style.width = '75%';
    document.getElementById('slow-progress').style.width = '0%';
    document.getElementById('table-progress').style.width = '60%';
    
    // Reset tab counters
    document.getElementById('queries-count').textContent = '0';
    document.getElementById('methods-count').textContent = '0';
    
    // Update data availability
    hasData = false;
    updateButtonStates();
}

function updateTimestamp() {
    const now = new Date();
    document.getElementById('last-update').textContent = now.toLocaleDateString('pt-BR') + ' ' + now.toLocaleTimeString('pt-BR');
}

function formatTimestamp(timestamp) {
    return new Date(timestamp).toLocaleTimeString('pt-BR');
}

function escapeHtml(text) {
    if (!text || typeof text !== 'string') {
        return '';
    }
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function showSuccess(message) {
    Swal.fire({
        icon: 'success',
        title: 'Sucesso!',
        text: message,
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}

function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Erro',
        text: message,
        timer: 5000,
        timerProgressBar: true,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        toast: true,
        position: 'top-end'
    });
}

function exportData() {
    if (!monitoringActive && !hasData) {
        showError('Não há dados disponíveis para exportar');
        return;
    }
    
    const exportButton = document.getElementById('export-data');
    const originalContent = exportButton.innerHTML;
    
    exportButton.disabled = true;
    exportButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Exportando...';
    
    // Get current data
    fetch('{{ route("debug.database.queries") }}')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const exportData = {
                    session_id: data.session_id || 'unknown',
                    session_active: data.session_active || false,
                    exported_at: new Date().toISOString(),
                    statistics: data.statistics || {},
                    queries: data.queries || [],
                    summary: {
                        total_queries: (data.statistics && data.statistics.total_queries) || 0,
                        total_time: Math.round((data.statistics && data.statistics.total_time) || 0),
                        average_time: Math.round((data.statistics && data.statistics.average_time) || 0),
                        slow_queries: ((data.statistics && data.statistics.slow_queries) || 0) + ((data.statistics && data.statistics.very_slow_queries) || 0)
                    },
                    message: data.message || 'Data exported'
                };
                
                // Check if there's actually data to export
                if (exportData.queries.length === 0) {
                    showError('Não há dados disponíveis para exportar');
                    return;
                }
                
                // Generate and download JSON file
                const blob = new Blob([JSON.stringify(exportData, null, 2)], { type: 'application/json' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `database_monitoring_${exportData.session_id}_${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.json`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                const statusMsg = exportData.session_active ? 'sessão ativa' : 'dados em cache';
                showSuccess(`Dados exportados com sucesso! ${exportData.queries.length} queries exportadas (${statusMsg}).`);
            } else {
                const errorMsg = data.message || data.error || 'Erro desconhecido ao obter dados';
                showError(`Erro ao obter dados para exportação: ${errorMsg}`);
            }
        })
        .catch(error => {
            console.error('Erro na exportação:', error);
            showError('Erro ao exportar dados');
        })
        .finally(() => {
            exportButton.disabled = false;
            exportButton.innerHTML = originalContent;
        });
}

function clearData() {
    // Primeiro vamos verificar se realmente há dados para limpar
    // consultando o backend ao invés de confiar apenas na variável local
    fetch('{{ route("debug.database.queries") }}')
        .then(response => response.json())
        .then(data => {
            const hasServerData = data.success && data.queries && data.queries.length > 0;
            
            if (!monitoringActive && !hasServerData) {
                Swal.fire({
                    icon: 'info',
                    title: 'Nenhum dado disponível',
                    text: 'Não há dados de monitoramento para limpar.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }
            
            // Confirmar limpeza com SweetAlert
            Swal.fire({
                icon: 'warning',
                title: 'Confirmar limpeza',
                text: 'Tem certeza que deseja limpar todos os dados de monitoramento? Esta ação não pode ser desfeita.',
                showCancelButton: true,
                confirmButtonText: 'Sim, limpar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    performClearData();
                }
            });
        })
        .catch(error => {
            console.error('Error checking data availability:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Erro ao verificar disponibilidade de dados.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33'
            });
        });
}

function performClearData() {
    const clearButton = document.getElementById('clear-data');
    const originalContent = clearButton.innerHTML;
    
    clearButton.disabled = true;
    clearButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Limpando...';
    
    // Clear cache data
    fetch('{{ route("debug.clear-cache") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            session_id: sessionId,
            clear_queries: true
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Clear UI
            clearQueryResults();
            
            // Reset containers
            document.getElementById('queries-container').innerHTML = `
                <div class="text-center text-muted py-10">
                    <i class="ki-duotone ki-database fs-4x mb-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <h3>Dados limpos - aguardando novas queries</h3>
                    <p>Execute algumas ações no sistema para ver as queries SQL</p>
                </div>
            `;
            
            document.getElementById('methods-container').innerHTML = `
                <div class="text-center text-muted py-10">
                    <i class="ki-duotone ki-router fs-4x mb-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <h3>Dados limpos</h3>
                    <p>Execute ações no sistema para ver o novo mapeamento HTTP → SQL</p>
                </div>
            `;
            
            document.getElementById('stats-container').innerHTML = `
                <div class="text-center text-muted py-10">
                    <i class="ki-duotone ki-chart-pie-simple fs-4x mb-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <h3>Dados limpos</h3>
                    <p>Estatísticas serão exibidas conforme novas queries forem capturadas</p>
                </div>
            `;
            
            const statusMsg = data.session_active ? 'sessão ativa' : 'cache limpo';
            showSuccess(`Dados de monitoramento limpos com sucesso! (${statusMsg})`);
        } else {
            const errorMsg = data.message || 'Erro desconhecido ao limpar dados';
            showError(`Erro ao limpar dados: ${errorMsg}`);
        }
    })
    .catch(error => {
        console.error('Erro ao limpar dados:', error);
        showError('Erro ao limpar dados de monitoramento');
    })
    .finally(() => {
        clearButton.disabled = false;
        clearButton.innerHTML = originalContent;
    });
}
</script>
@endpush
@endsection