@extends('components.layouts.app')

@section('title', 'Protocolo de Proposições')

@section('content')
<style>
/* Dashboard Card Styles */
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

/* Card hover effect */
.dashboard-card-primary:hover, 
.dashboard-card-info:hover, 
.dashboard-card-success:hover, 
.dashboard-card-warning:hover {
    transform: translateY(-5px);
    transition: all .3s ease;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .dashboard-card-primary .fs-2hx,
    .dashboard-card-info .fs-2hx,
    .dashboard-card-success .fs-2hx,
    .dashboard-card-warning .fs-2hx {
        font-size: 2rem !important;
    }
    
    .h-70px.w-70px {
        height: 50px !important;
        width: 50px !important;
    }
    
    .fs-2x {
        font-size: 1.5rem !important;
    }
}

@media (max-width: 576px) {
    .dashboard-card-primary .card-header,
    .dashboard-card-info .card-header,
    .dashboard-card-success .card-header,
    .dashboard-card-warning .card-header {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }
    
    .dashboard-card-primary .card-body,
    .dashboard-card-info .card-body,
    .dashboard-card-success .card-body,
    .dashboard-card-warning .card-body {
        padding-top: 0.5rem !important;
    }
    
    /* Search input responsivo */
    .w-250px {
        width: 100% !important;
        max-width: 250px;
    }
    
    /* Paginação responsiva */
    .pagination {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    /* Título da página mais compacto */
    .page-heading {
        font-size: 2rem !important;
    }
    
    /* Card headers mais compactos */
    .card-header {
        padding: 1rem !important;
    }
    
    /* Badge e texto menor em mobile */
    .badge {
        font-size: 0.7rem !important;
    }
    
    /* Botões de ação touch-friendly */
    .btn-sm {
        min-height: 38px !important;
        min-width: 38px !important;
    }
    
    /* Tabela como cards em mobile */
    #kt_proposicoes_table tbody tr {
        display: block !important;
        border: 1px solid #e1e5e9 !important;
        border-radius: 0.625rem !important;
        margin-bottom: 1rem !important;
        background: white !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;
        padding: 1rem !important;
    }
    
    #kt_proposicoes_table tbody td {
        display: block !important;
        border: none !important;
        padding: 0.25rem 0 !important;
        text-align: left !important;
    }
    
    #kt_proposicoes_table tbody td:last-child {
        text-align: center !important;
        margin-top: 1rem !important;
        padding-top: 1rem !important;
        border-top: 1px solid #e1e5e9 !important;
    }
    
    /* Ocultar cabeçalho da tabela em mobile */
    #kt_proposicoes_table thead {
        display: none !important;
    }
    
    /* Estilos específicos para mobile cards */
    .mobile-card-content {
        width: 100%;
    }
    
    .mobile-info-grid {
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px solid #e1e5e9;
    }
    
    .mobile-info-grid .col-6,
    .mobile-info-grid .col-12 {
        margin-bottom: 0.5rem;
    }
    
    .mobile-ementa {
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Botões de ação mobile */
    .mobile-actions .btn {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        padding: 0.5rem !important;
        line-height: 1.2 !important;
    }
    
    .mobile-actions .btn i {
        margin-bottom: 0.25rem !important;
        margin-right: 0 !important;
    }
    
    /* Ajustes de tipografia mobile */
    .mobile-card-content .badge {
        font-size: 0.7rem !important;
        padding: 0.25rem 0.5rem !important;
    }
    
    .mobile-card-content .fs-6 {
        font-size: 0.9rem !important;
        line-height: 1.3 !important;
    }
    
    .mobile-card-content .fs-7 {
        font-size: 0.8rem !important;
    }
    
    .mobile-card-content .fs-8 {
        font-size: 0.75rem !important;
    }
    
    /* Espaçamento entre cards */
    #kt_proposicoes_table tbody tr:last-child {
        margin-bottom: 0 !important;
    }
}

/* Layout específico para tablet */
@media (max-width: 991px) and (min-width: 577px) {
    /* Ajustes para tablets */
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .badge {
        font-size: 0.75rem !important;
    }
}
</style>
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Protocolo de Proposições
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Proposições</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Protocolo</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('proposicoes.protocolos-hoje') }}" class="btn btn-sm btn-flex btn-outline btn-active-color-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver protocolos de hoje">
                    <i class="ki-duotone ki-calendar-8 fs-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                        <span class="path5"></span>
                        <span class="path6"></span>
                    </i>
                    Protocolos Hoje
                </a>
                <a href="{{ route('proposicoes.estatisticas-protocolo') }}" class="btn btn-sm btn-primary">
                    <i class="ki-duotone ki-chart-simple fs-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                    Estatísticas
                </a>
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Stats-->
            <div class="row g-3 g-md-5 g-xl-10 mb-5 mb-xl-10">
                <!--begin::Col-->
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
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
                                <span class="fs-2hx fw-bold text-white me-2">{{ $proposicoes->count() }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">proposições</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Aguardando Protocolo</span>
                                @php
                                    $totalProposicoes = \App\Models\Proposicao::whereIn('status', ['enviado_protocolo', 'assinado', 'protocolado'])->count();
                                    $percentual = $totalProposicoes > 0 ? round(($proposicoes->count() / $totalProposicoes) * 100) : 0;
                                @endphp
                                <span class="badge badge-light-warning fs-8">{{ $percentual }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $percentual }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-primary cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-warning-2 text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $proposicoes->where('urgencia', '!=', 'normal')->count() }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">urgentes</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Com Urgência</span>
                                @php
                                    $percentualUrgente = $proposicoes->count() > 0 ? round(($proposicoes->where('urgencia', '!=', 'normal')->count() / $proposicoes->count()) * 100) : 0;
                                @endphp
                                <span class="badge badge-light-primary fs-8">{{ $percentualUrgente }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $percentualUrgente }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-success cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-calendar-8 text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                    <span class="path6"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $proposicoes->where('created_at', '>=', today())->count() }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">hoje</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Recebidas Hoje</span>
                                @php
                                    $percentualHoje = $proposicoes->count() > 0 ? round(($proposicoes->where('created_at', '>=', today())->count() / $proposicoes->count()) * 100) : 0;
                                @endphp
                                <span class="badge badge-light-success fs-8">{{ $percentualHoje }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $percentualHoje }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-info cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-hourglass text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $proposicoes->where('data_assinatura', '<=', now()->subDays(2))->count() }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">atrasadas</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Aguardam +2 Dias</span>
                                @php
                                    $percentualAtrasadas = $proposicoes->count() > 0 ? round(($proposicoes->where('data_assinatura', '<=', now()->subDays(2))->count() / $proposicoes->count()) * 100) : 0;
                                @endphp
                                <span class="badge badge-light-info fs-8">{{ $percentualAtrasadas }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $percentualAtrasadas }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Stats-->

            <!--begin::Filtros-->
            <div class="card mb-5 mb-xl-10">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Filtros</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Filtrar proposições por critérios</span>
                    </h3>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body py-3">
                    <div class="row g-3 g-md-5 g-xl-8">
                        <div class="col-xl-3 col-lg-6 col-md-6 col-12">
                            <label class="required fw-semibold fs-6 mb-2">Tipo de Proposição</label>
                            <select id="filtro-tipo" class="form-select form-select-solid" data-control="select2" data-placeholder="Selecionar tipo">
                                <option value="">Todos os tipos</option>
                                <option value="PL">Projeto de Lei</option>
                                <option value="PLP">Projeto de Lei Complementar</option>
                                <option value="PEC">Proposta de Emenda Constitucional</option>
                                <option value="PDC">Projeto de Decreto Legislativo</option>
                                <option value="PRC">Projeto de Resolução</option>
                                <option value="mocao">Moção</option>
                                <option value="indicacao">Indicação</option>
                                <option value="requerimento">Requerimento</option>
                            </select>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-12">
                            <label class="required fw-semibold fs-6 mb-2">Urgência</label>
                            <select id="filtro-urgencia" class="form-select form-select-solid" data-control="select2" data-placeholder="Selecionar urgência">
                                <option value="">Todas as urgências</option>
                                <option value="urgentissima">Urgentíssima</option>
                                <option value="urgente">Urgente</option>
                                <option value="normal">Normal</option>
                            </select>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-12">
                            <label class="required fw-semibold fs-6 mb-2">Autor</label>
                            <input type="text" id="filtro-autor" class="form-control form-control-solid" placeholder="Nome do autor" />
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-12">
                            <label class="fw-semibold fs-6 mb-2 d-none d-xl-block">&nbsp;</label>
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <button type="button" class="btn btn-primary flex-fill" id="btn-filtrar">
                                    <i class="ki-duotone ki-magnifier fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <span class="d-none d-sm-inline">Filtrar</span>
                                </button>
                                <button type="button" class="btn btn-light flex-fill" id="btn-limpar">
                                    <i class="ki-duotone ki-cross fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <span class="d-none d-sm-inline">Limpar</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Filtros-->

            <!--begin::Proposições-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Proposições para Protocolação</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">{{ $proposicoes->total() }} proposições encontradas</span>
                    </h3>
                    <div class="card-toolbar">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-1 position-absolute ms-6">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" data-kt-filter="search" class="form-control form-control-solid w-250px w-md-300px ps-15" placeholder="Buscar proposição..." />
                        </div>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body py-4">
                    @if($proposicoes->count() > 0)
                        <!--begin::Table-->
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_proposicoes_table">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th class="w-10px pe-2 d-none d-lg-table-cell">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" id="select-all" />
                                            </div>
                                        </th>
                                        <th class="min-w-50px d-none d-md-table-cell">Urgência</th>
                                        <th class="min-w-125px">Proposição</th>
                                        <th class="min-w-125px d-none d-lg-table-cell">Autor</th>
                                        <th class="min-w-250px d-none d-xl-table-cell">Ementa</th>
                                        <th class="min-w-125px d-none d-sm-table-cell">Data Assinatura</th>
                                        <th class="min-w-100px d-none d-md-table-cell">Tempo Espera</th>
                                        <th class="text-end min-w-100px">Ações</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="text-gray-600 fw-semibold">
                                    @foreach($proposicoes as $proposicao)
                                    <tr class="{{ $proposicao->urgencia !== 'normal' ? 'bg-light-warning' : '' }}">
                                        <td class="d-none d-lg-table-cell">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input row-checkbox" type="checkbox" value="{{ $proposicao->id }}" />
                                            </div>
                                        </td>
                                        <td class="text-center d-none d-md-table-cell">
                                            @if($proposicao->urgencia === 'urgentissima')
                                                <span class="badge badge-light-danger fw-bold fs-8 px-2 py-1" title="Urgentíssima">
                                                    <i class="ki-duotone ki-warning-2 fs-5">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            @elseif($proposicao->urgencia === 'urgente')
                                                <span class="badge badge-light-warning fw-bold fs-8 px-2 py-1" title="Urgente">
                                                    <i class="ki-duotone ki-warning-1 fs-5">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </span>
                                            @else
                                                <span class="badge badge-light-secondary fw-bold fs-8 px-2 py-1" title="Normal">
                                                    <i class="ki-duotone ki-information fs-5">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="mobile-card-content">
                                                <!-- Cabeçalho com tipo e urgência -->
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge badge-light-primary fw-bold fs-8 px-2 py-1 me-2">{{ $proposicao->tipo }}</span>
                                                        @if($proposicao->numero_temporario)
                                                            <small class="text-muted">Nº {{ $proposicao->numero_temporario }}</small>
                                                        @endif
                                                    </div>
                                                    <!-- Urgência sempre visível em mobile -->
                                                    @if($proposicao->urgencia === 'urgentissima')
                                                        <span class="badge badge-light-danger fw-bold fs-8 px-2 py-1" title="Urgentíssima">
                                                            <i class="ki-duotone ki-warning-2 fs-5">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                            <span class="d-none d-sm-inline ms-1">Urgentíssima</span>
                                                        </span>
                                                    @elseif($proposicao->urgencia === 'urgente')
                                                        <span class="badge badge-light-warning fw-bold fs-8 px-2 py-1" title="Urgente">
                                                            <i class="ki-duotone ki-warning-1 fs-5">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            <span class="d-none d-sm-inline ms-1">Urgente</span>
                                                        </span>
                                                    @else
                                                        <span class="badge badge-light-secondary fw-bold fs-8 px-2 py-1 d-md-none" title="Normal">
                                                            <i class="ki-duotone ki-information fs-5">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                            <span class="d-none d-sm-inline ms-1">Normal</span>
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                <!-- Título da proposição -->
                                                @if($proposicao->titulo)
                                                    <div class="text-gray-800 fw-bold fs-6 mb-2">{{ $proposicao->titulo }}</div>
                                                @endif
                                                
                                                <!-- Grid de informações em mobile -->
                                                <div class="row g-2 d-md-none mobile-info-grid">
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-profile-circle text-muted fs-5 me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                            <div>
                                                                <div class="text-gray-800 fw-semibold fs-7">{{ $proposicao->autor->name }}</div>
                                                                <div class="text-muted fs-8">{{ $proposicao->autor->cargo_atual ?? 'Parlamentar' }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    @if($proposicao->data_assinatura)
                                                    <div class="col-6">
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-calendar-8 text-muted fs-5 me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                                <span class="path4"></span>
                                                                <span class="path5"></span>
                                                                <span class="path6"></span>
                                                            </i>
                                                            <div>
                                                                <div class="text-gray-800 fw-semibold fs-7">{{ $proposicao->data_assinatura->format('d/m/Y') }}</div>
                                                                <div class="text-muted fs-8">{{ $proposicao->data_assinatura->format('H:i') }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-6">
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-timer text-muted fs-5 me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                            <div>
                                                                @php
                                                                    $diasEspera = (int) $proposicao->data_assinatura->diffInDays(now());
                                                                    $horasEspera = $proposicao->data_assinatura->diffInHours(now());
                                                                @endphp
                                                                
                                                                @if($diasEspera > 0)
                                                                    <span class="badge badge-light-{{ $diasEspera > 2 ? 'danger' : ($diasEspera > 1 ? 'warning' : 'success') }} fw-bold fs-8">
                                                                        {{ $diasEspera }} {{ $diasEspera == 1 ? 'dia' : 'dias' }}
                                                                    </span>
                                                                @elseif($horasEspera > 0)
                                                                    <span class="badge badge-light-success fw-bold fs-8">{{ (int) $horasEspera }}h</span>
                                                                @else
                                                                    <span class="badge badge-light-success fw-bold fs-8">Agora</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    
                                                    @if($proposicao->ementa)
                                                    <div class="col-12 mt-2">
                                                        <div class="text-muted fs-7 mobile-ementa">
                                                            <i class="ki-duotone ki-document text-muted fs-5 me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            {{ Str::limit($proposicao->ementa, 150) }}
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="d-none d-lg-table-cell">
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-800 fw-bold mb-1">{{ $proposicao->autor->name }}</span>
                                                <span class="text-muted fs-7">{{ $proposicao->autor->cargo_atual ?? 'Parlamentar' }}</span>
                                            </div>
                                        </td>
                                        <td class="d-none d-xl-table-cell">
                                            <div class="text-gray-600" style="max-width: 300px;" title="{{ $proposicao->ementa }}">
                                                {{ Str::limit($proposicao->ementa, 100) }}
                                            </div>
                                        </td>
                                        <td class="d-none d-sm-table-cell">
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-800 fw-bold">{{ $proposicao->data_assinatura ? $proposicao->data_assinatura->format('d/m/Y') : 'N/A' }}</span>
                                                @if($proposicao->data_assinatura)
                                                    <span class="text-muted fs-7">{{ $proposicao->data_assinatura->format('H:i') }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="d-none d-md-table-cell">
                                            @if($proposicao->data_assinatura)
                                                @php
                                                    $diasEspera = (int) $proposicao->data_assinatura->diffInDays(now());
                                                    $horasEspera = $proposicao->data_assinatura->diffInHours(now());
                                                    $minutosEspera = $proposicao->data_assinatura->diffInMinutes(now());
                                                @endphp
                                                
                                                @if($diasEspera > 0)
                                                    <span class="badge badge-light-{{ $diasEspera > 2 ? 'danger' : ($diasEspera > 1 ? 'warning' : 'success') }} fw-bold fs-8 px-2 py-1">
                                                        {{ $diasEspera }} {{ $diasEspera == 1 ? 'dia' : 'dias' }}
                                                    </span>
                                                @elseif($horasEspera > 0)
                                                    <span class="badge badge-light-success fw-bold fs-8 px-2 py-1">
                                                        {{ (int) $horasEspera }}h
                                                    </span>
                                                @else
                                                    <span class="badge badge-light-success fw-bold fs-8 px-2 py-1">
                                                        {{ (int) $minutosEspera }}min
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="mobile-actions">
                                                <!-- Desktop buttons -->
                                                <div class="d-none d-md-flex justify-content-end">
                                                    <a href="{{ route('proposicoes.show', $proposicao) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Visualizar">
                                                        <i class="ki-duotone ki-eye fs-3">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                    </a>
                                                    <a href="{{ route('proposicoes.protocolar.show', $proposicao) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Protocolar">
                                                        <i class="ki-duotone ki-files-tablet fs-3">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </a>
                                                </div>
                                                
                                                <!-- Mobile buttons - Layout horizontal para melhor UX -->
                                                <div class="d-flex d-md-none gap-2 justify-content-center">
                                                    <a href="{{ route('proposicoes.show', $proposicao) }}" class="btn btn-light btn-sm flex-fill" style="min-height: 44px; max-width: 120px;">
                                                        <i class="ki-duotone ki-eye fs-4 mb-1">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                        <div class="fs-8 fw-semibold">Visualizar</div>
                                                    </a>
                                                    <a href="{{ route('proposicoes.protocolar.show', $proposicao) }}" class="btn btn-primary btn-sm flex-fill" style="min-height: 44px; max-width: 120px;">
                                                        <i class="ki-duotone ki-files-tablet fs-4 mb-1">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        <div class="fs-8 fw-semibold">Protocolar</div>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                <!--end::Table body-->
                                </tbody>
                            </table>
                        </div>
                        <!--end::Table-->
                        
                        <!--begin::Pagination-->
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pt-10 gap-3">
                            <div class="fs-6 fw-semibold text-gray-700 text-center text-md-start">
                                <span class="d-none d-sm-inline">Mostrando {{ $proposicoes->firstItem() }} a {{ $proposicoes->lastItem() }} de </span>
                                <span class="d-sm-none">Total: </span>{{ $proposicoes->total() }} proposições
                            </div>
                            <div class="d-flex justify-content-center">
                                {{ $proposicoes->links() }}
                            </div>
                        </div>
                        <!--end::Pagination-->
                    @else
                        <!--begin::Empty state-->
                        <div class="text-center py-20">
                            <div class="mb-7">
                                <i class="ki-duotone ki-files-tablet fs-5x text-success">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <h3 class="text-gray-800 fw-bold fs-2 mb-4">Nenhuma proposição aguardando protocolo</h3>
                            <p class="text-gray-600 fw-semibold fs-6 mb-7">
                                Todas as proposições assinadas já foram protocoladas.
                            </p>
                            <a href="{{ route('proposicoes.protocolos-hoje') }}" class="btn btn-primary">
                                <i class="ki-duotone ki-calendar-8 fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                    <span class="path6"></span>
                                </i>
                                Ver Protocolos de Hoje
                            </a>
                        </div>
                        <!--end::Empty state-->
                    @endif
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Proposições-->

            <!--begin::Ações em Lote-->
            @if($proposicoes->count() > 0)
            <div class="card mt-5">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Ações em Lote</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Selecionar múltiplas proposições para ações em massa</span>
                    </h3>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body py-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" id="select-all-bulk">
                                <label class="form-check-label fw-semibold text-gray-700" for="select-all-bulk">
                                    Selecionar todas as proposições visíveis
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" class="btn btn-primary" id="btn-protocolar-lote" disabled>
                                <i class="ki-duotone ki-files-tablet fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Protocolar Selecionadas
                            </button>
                        </div>
                    </div>
                    
                    <div id="lote-info" class="mt-5" style="display: none;">
                        <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                            <i class="ki-duotone ki-information-5 fs-2tx text-primary me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <div class="fs-6 text-gray-700">
                                        <span id="lote-count" class="fw-bold">0</span> proposições selecionadas para protocolação em lote.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Ações em Lote--> 
            @endif
            
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection

@push('scripts')
<script>
"use strict";

// Class definition
var KTProposicoesList = function() {
    // Define shared variables
    var table = document.getElementById('kt_proposicoes_table');
    var datatable;

    // Private functions
    var initToggleToolbar = function() {
        // Toggle selected action toolbar
        const container = document.querySelector('#kt_proposicoes_table');
        const checkboxes = container.querySelectorAll('[type="checkbox"]');

        // Select all checkboxes
        const allCheckboxes = container.querySelector('#select-all');
        if (allCheckboxes) {
            allCheckboxes.addEventListener('change', function(e) {
                checkboxes.forEach(c => {
                    if (c !== allCheckboxes) {
                        c.checked = e.target.checked;
                    }
                });
                atualizarLote();
            });
        }

        // Checkbox on change event
        checkboxes.forEach(c => {
            c.addEventListener('change', function() {
                setTimeout(function() {
                    atualizarLote();
                }, 50);
            });
        });
    }

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = function() {
        const filterSearch = document.querySelector('[data-kt-filter="search"]');
        filterSearch.addEventListener('keyup', function(e) {
            const searchText = e.target.value;
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchText.toLowerCase())) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Public methods
    return {
        init: function() {
            if (!table) {
                return;
            }

            initToggleToolbar();
            handleSearchDatatable();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTProposicoesList.init();
});

// Funções específicas da página
$(document).ready(function() {
    // Filtros
    $('#btn-filtrar').on('click', function() {
        aplicarFiltros();
    });

    $('#btn-limpar').on('click', function() {
        $('#filtro-tipo').val('').trigger('change');
        $('#filtro-urgencia').val('').trigger('change');
        $('#filtro-autor').val('');
        aplicarFiltros();
    });

    // Enter para filtrar
    $('#filtro-autor').on('keypress', function(e) {
        if (e.which === 13) {
            aplicarFiltros();
        }
    });

    // Bulk actions
    $('#select-all-bulk').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.row-checkbox').prop('checked', isChecked);
        atualizarLote();
    });

    $('#btn-protocolar-lote').on('click', function() {
        const selecionadas = $('.row-checkbox:checked').length;
        
        if (selecionadas === 0) {
            Swal.fire({
                text: "Selecione pelo menos uma proposição",
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: "Ok",
                customClass: {
                    confirmButton: "btn fw-bold btn-primary",
                }
            });
            return;
        }

        Swal.fire({
            text: `Confirma a protocolação em lote de ${selecionadas} proposições?`,
            icon: "question",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Sim, protocolar!",
            cancelButtonText: "Cancelar",
            customClass: {
                confirmButton: "btn fw-bold btn-primary",
                cancelButton: "btn fw-bold btn-active-light-primary"
            }
        }).then(function(result) {
            if (result.value) {
                // Implementar protocolação em lote
                Swal.fire({
                    text: "Funcionalidade em desenvolvimento",
                    icon: "info",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });
            }
        });
    });

    function aplicarFiltros() {
        const params = new URLSearchParams(window.location.search);
        
        const tipo = $('#filtro-tipo').val();
        const urgencia = $('#filtro-urgencia').val();
        const autor = $('#filtro-autor').val();

        if (tipo) params.set('tipo', tipo);
        else params.delete('tipo');

        if (urgencia) params.set('urgencia', urgencia);
        else params.delete('urgencia');

        if (autor) params.set('autor', autor);
        else params.delete('autor');

        params.delete('page'); // Reset pagination

        window.location.search = params.toString();
    }

    // Aplicar filtros da URL
    const urlParams = new URLSearchParams(window.location.search);
    $('#filtro-tipo').val(urlParams.get('tipo') || '');
    $('#filtro-urgencia').val(urlParams.get('urgencia') || '');
    $('#filtro-autor').val(urlParams.get('autor') || '');

    // Auto-refresh a cada 60 segundos
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            window.location.reload();
        }
    }, 60000);
});

function atualizarLote() {
    const selecionadas = $('.row-checkbox:checked').length;
    const total = $('.row-checkbox').length;
    
    $('#lote-count').text(selecionadas);
    
    if (selecionadas > 0) {
        $('#lote-info').show();
        $('#btn-protocolar-lote').prop('disabled', false);
    } else {
        $('#lote-info').hide();
        $('#btn-protocolar-lote').prop('disabled', true);
    }

    // Atualizar estado do "selecionar todos"
    const selectAllBulk = $('#select-all-bulk')[0];
    if (selectAllBulk) {
        if (selecionadas === total && total > 0) {
            selectAllBulk.indeterminate = false;
            selectAllBulk.checked = true;
        } else if (selecionadas > 0) {
            selectAllBulk.indeterminate = true;
            selectAllBulk.checked = false;
        } else {
            selectAllBulk.indeterminate = false;
            selectAllBulk.checked = false;
        }
    }
}
</script>
@endpush

@push('styles')
<style>
/* Custom styles for protocol page with Keen UI compatibility */
.bg-light-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.form-check-input:indeterminate {
    background-color: var(--kt-primary);
    border-color: var(--kt-primary);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10h8'/%3e%3c/svg%3e");
}

/* Ensure tooltips work properly */
.tooltip {
    z-index: 9999;
}
</style>
@endpush