@extends('components.layouts.app')

@section('title', 'Parecer Jurídico - Proposições')

@section('content')
<style>
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

/* Card hover effects */
.dashboard-card-primary:hover,
.dashboard-card-success:hover,
.dashboard-card-warning:hover,
.dashboard-card-info:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.dashboard-card-primary,
.dashboard-card-success,
.dashboard-card-warning,
.dashboard-card-info {
    transition: all 0.3s ease;
}

/* Card click animation */
.dashboard-card-primary:active,
.dashboard-card-success:active,
.dashboard-card-warning:active,
.dashboard-card-info:active {
    transform: translateY(-2px);
    transition: all 0.1s ease;
}

/* Enhanced spacing for better visual hierarchy */
.row.gy-5.gx-xl-8.mb-8 {
    margin-bottom: 3rem !important;
}

/* Card container improvements */
.card.card-flush {
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #e4e6ef;
}

/* Improved table card styling */
.card-header.align-items-center {
    border-bottom: 1px solid #e4e6ef;
    background-color: #f9f9f9;
}

/* Better visual separation between sections */
#kt_app_content .app-container .row + .row {
    margin-top: 2rem;
}

/* Specific spacing for stats row */
.row.gy-5.gx-xl-8.mb-8::after {
    content: '';
    display: block;
    width: 100%;
    height: 1px;
    background: linear-gradient(90deg, transparent 0%, #e4e6ef 50%, transparent 100%);
    margin-top: 1.5rem;
}

/* Enhanced responsive spacing */
@media (max-width: 768px) {
    .row.gy-5.gx-xl-8.mb-8 {
        margin-bottom: 2rem !important;
    }
    
    #kt_app_content .app-container .row + .row {
        margin-top: 1.5rem;
    }
}
</style>

<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Parecer Jurídico
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Parecer Jurídico</li>
                </ul>
            </div>
            <!--end::Page title-->
            
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!--begin::Export menu-->
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-light-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ki-duotone ki-file-down fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Relatórios
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="window.print()">
                            <i class="ki-duotone ki-printer fs-2 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                            Imprimir Lista
                        </a></li>
                        <li><a class="dropdown-item" href="#">
                            <i class="ki-duotone ki-chart-simple fs-2 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                            Estatísticas Detalhadas
                        </a></li>
                    </ul>
                </div>
                <!--end::Export menu-->
                
                <a href="{{ route('parecer-juridico.meus-pareceres') }}" class="btn btn-sm fw-bold btn-light-primary">
                    <i class="ki-duotone ki-eye fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    Meus Pareceres
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <x-alerts.flash />
            
            <!--begin::Stats Row-->
            <div class="row gy-5 gx-xl-8 mb-8">
                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="card card-flush h-100 dashboard-card-primary cursor-pointer" onclick="scrollToTable()" aria-label="Ver todas as proposições">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-questionnaire-tablet text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $proposicoes->total() }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">proposições</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Total de Proposições</span>
                                <span class="badge badge-light-primary fs-8">100%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col-->
                
                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="card card-flush h-100 dashboard-card-success cursor-pointer" onclick="filterByStatus('com_parecer')" aria-label="Filtrar proposições com parecer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-check-circle text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            @php
                                $comParecer = $proposicoes->where('tem_parecer_juridico', true)->count();
                                $percentParecer = $proposicoes->total() > 0 ? round(($comParecer / $proposicoes->total()) * 100) : 0;
                            @endphp
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $comParecer }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">pareceres</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Com Parecer Jurídico</span>
                                <span class="badge badge-light-success fs-8">{{ $percentParecer }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $percentParecer }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col-->
                
                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="card card-flush h-100 dashboard-card-warning cursor-pointer" onclick="filterByStatus('sem_parecer')" aria-label="Filtrar proposições pendentes de parecer">
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
                            @php
                                $pendentes = $proposicoes->where('tem_parecer_juridico', false)->count();
                                $percentPendentes = $proposicoes->total() > 0 ? round(($pendentes / $proposicoes->total()) * 100) : 0;
                            @endphp
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $pendentes }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">pendentes</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Pendentes de Parecer</span>
                                <span class="badge badge-light-warning fs-8">{{ $percentPendentes }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $percentPendentes }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col-->
                
                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="card card-flush h-100 dashboard-card-info cursor-pointer" onclick="filterByDate('today')" aria-label="Filtrar proposições protocoladas hoje">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-document text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            @php
                                $hoje = $proposicoes->filter(function($p) { 
                                    return $p->data_protocolo && $p->data_protocolo->isToday(); 
                                })->count();
                            @endphp
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $hoje }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">hoje</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Protocoladas Hoje</span>
                                <span class="badge badge-light-info fs-8">{{ $hoje > 0 ? '100' : '0' }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $hoje > 0 ? '85' : '0' }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Stats Row-->

            <!--begin::Table Row-->
            <div class="row">
                <!--begin::Col-->
                <div class="col-xl-12">
                    <!--begin::Proposições Card-->
                    <div class="card card-flush">
                        <!--begin::Card header-->
                        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                            <div class="card-title">
                                <!--begin::Search-->
                                <div class="d-flex align-items-center position-relative my-1">
                                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <input type="text" data-kt-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Buscar proposições..." />
                                </div>
                                <!--end::Search-->
                            </div>
                            
                            <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                                <!--begin::Filter menu-->
                                <div class="w-150px">
                                    <select class="form-select form-select-solid" data-control="select2" data-placeholder="Status Parecer" data-kt-filter="status-parecer">
                                        <option></option>
                                        <option value="all">Todos</option>
                                        <option value="com_parecer">Com Parecer</option>
                                        <option value="sem_parecer">Sem Parecer</option>
                                    </select>
                                </div>
                                <!--end::Filter menu-->
                                
                                <!--begin::Filter menu-->
                                <div class="w-150px">
                                    <select class="form-select form-select-solid" data-control="select2" data-placeholder="Tipo" data-kt-filter="tipo">
                                        <option></option>
                                        <option value="all">Todos os Tipos</option>
                                        @foreach($tipos as $tipo)
                                            <option value="{{ $tipo }}">{{ $tipo }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!--end::Filter menu-->
                                
                                <!--begin::Reset filter-->
                                <button type="button" class="btn btn-light-primary me-3" data-kt-filter="reset">
                                    <i class="ki-duotone ki-arrow-right fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Resetar
                                </button>
                                <!--end::Reset filter-->
                            </div>
                        </div>
                        <!--end::Card header-->
                        
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            @if($proposicoes->count() > 0)
                                <!--begin::Table-->
                                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_proposicoes_table">
                                    <thead>
                                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                            <th class="w-10px pe-2">
                                                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_proposicoes_table .form-check-input" value="1" />
                                                </div>
                                            </th>
                                            <th class="min-w-200px">Proposição</th>
                                            <th class="min-w-300px">Ementa</th>
                                            <th class="min-w-100px">Protocolo</th>
                                            <th class="min-w-100px">Status Parecer</th>
                                            <th class="text-end min-w-100px">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600">
                                        @foreach($proposicoes as $proposicao)
                                            <tr data-proposicao-id="{{ $proposicao->id }}" 
                                                data-tipo="{{ $proposicao->tipo }}"
                                                data-status-parecer="{{ $proposicao->tem_parecer_juridico ? 'com_parecer' : 'sem_parecer' }}">
                                                <td>
                                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                        <input class="form-check-input" type="checkbox" value="{{ $proposicao->id }}" />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="symbol symbol-45px me-5">
                                                            <div class="symbol-label bg-light-{{ $proposicao->tem_parecer_juridico ? 'success' : 'warning' }} text-{{ $proposicao->tem_parecer_juridico ? 'success' : 'warning' }} fw-bold">
                                                                {{ strtoupper(substr($proposicao->tipo, 0, 2)) }}
                                                            </div>
                                                        </div>
                                                        <div class="d-flex justify-content-start flex-column">
                                                            <a href="#" class="text-dark fw-bold text-hover-primary fs-6">{{ $proposicao->tipo }}</a>
                                                            <span class="text-muted fw-semibold text-muted d-block fs-7">
                                                                @if($proposicao->numero)
                                                                    Nº {{ $proposicao->numero }}
                                                                @else
                                                                    ID: {{ $proposicao->id }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-gray-800 mb-1">
                                                            {{ Str::limit($proposicao->ementa, 100) }}
                                                        </span>
                                                        @if($proposicao->autor)
                                                            <span class="text-muted fs-7">
                                                                <i class="ki-duotone ki-user fs-7 me-1">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                                {{ $proposicao->autor->name }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        @if($proposicao->numero_protocolo)
                                                            <span class="badge badge-light-info fs-7 fw-bold">
                                                                {{ $proposicao->numero_protocolo }}
                                                            </span>
                                                            @if($proposicao->data_protocolo)
                                                                <span class="text-muted fs-8 mt-1">
                                                                    {{ $proposicao->data_protocolo->format('d/m/Y H:i') }}
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="badge badge-light-secondary">Não protocolado</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($proposicao->tem_parecer_juridico)
                                                        @if($proposicao->parecerJuridico)
                                                            <div class="d-flex flex-column align-items-start">
                                                                <span class="badge badge-{{ $proposicao->parecerJuridico->getCorTipoParecer() }} fs-7 fw-bold mb-1">
                                                                    {{ $proposicao->parecerJuridico->getTipoParecerFormatado() }}
                                                                </span>
                                                                <span class="text-muted fs-8">
                                                                    {{ $proposicao->parecerJuridico->assessor->name ?? 'Assessor' }}
                                                                </span>
                                                            </div>
                                                        @else
                                                            <span class="badge badge-success">Com Parecer</span>
                                                        @endif
                                                    @else
                                                        <span class="badge badge-warning">
                                                            <i class="ki-duotone ki-time fs-7 me-1">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            Pendente
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-flex justify-content-end flex-shrink-0">
                                                        @if($proposicao->tem_parecer_juridico && $proposicao->parecerJuridico)
                                                            <a href="{{ route('parecer-juridico.show', $proposicao->parecerJuridico) }}" 
                                                               class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver Parecer">
                                                                <i class="ki-duotone ki-eye fs-2">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                    <span class="path3"></span>
                                                                </i>
                                                            </a>
                                                        @else
                                                            <a href="{{ route('parecer-juridico.create', $proposicao) }}" 
                                                               class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Emitir Parecer">
                                                                <i class="ki-duotone ki-plus fs-2">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                    <span class="path3"></span>
                                                                </i>
                                                            </a>
                                                        @endif
                                                        
                                                        <a href="{{ route('proposicoes.show', $proposicao) }}" 
                                                           class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver Proposição">
                                                            <i class="ki-duotone ki-document fs-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <!--end::Table-->
                                
                                <!--begin::Pagination-->
                                <div class="d-flex flex-stack flex-wrap pt-10">
                                    <div class="fs-6 fw-semibold text-gray-700">
                                        Mostrando {{ $proposicoes->firstItem() }} a {{ $proposicoes->lastItem() }} 
                                        de {{ $proposicoes->total() }} proposições
                                    </div>
                                    
                                    {{ $proposicoes->appends(request()->query())->links() }}
                                </div>
                                <!--end::Pagination-->
                            @else
                                <!--begin::Empty state-->
                                <div class="card-px text-center py-20">
                                    <div class="d-flex flex-center flex-column">
                                        <div class="d-flex flex-center rounded-circle h-150px w-150px bg-light-primary mb-7">
                                            <i class="ki-duotone ki-questionnaire-tablet fs-5x text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                        <h2 class="fs-2 fw-bold text-gray-800 mb-4">Nenhuma proposição protocolada</h2>
                                        <div class="fs-6 fw-semibold text-gray-600 mb-7">
                                            Não há proposições protocoladas disponíveis para análise jurídica no momento.
                                            <br>
                                            As proposições aparecerão aqui após serem protocoladas pelo sistema.
                                        </div>
                                        <div class="text-center">
                                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                                <i class="ki-duotone ki-arrow-left fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Voltar ao Dashboard
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Empty state-->
                            @endif
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Proposições Card-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Table Row-->
        </div>
    </div>
    <!--end::Content-->
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Table filtering functionality
    const table = document.getElementById('kt_proposicoes_table');
    const searchInput = document.querySelector('[data-kt-filter="search"]');
    const statusFilter = document.querySelector('[data-kt-filter="status-parecer"]');
    const tipoFilter = document.querySelector('[data-kt-filter="tipo"]');
    const resetBtn = document.querySelector('[data-kt-filter="reset"]');

    if (table && searchInput && statusFilter && tipoFilter && resetBtn) {
        // Search functionality
        searchInput.addEventListener('keyup', function() {
            filterTable();
        });

        // Status filter
        statusFilter.addEventListener('change', function() {
            filterTable();
        });

        // Tipo filter
        tipoFilter.addEventListener('change', function() {
            filterTable();
        });

        // Reset filters
        resetBtn.addEventListener('click', function() {
            searchInput.value = '';
            statusFilter.value = '';
            tipoFilter.value = '';
            filterTable();
        });

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const statusValue = statusFilter.value;
            const tipoValue = tipoFilter.value;
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(function(row) {
                let visible = true;

                // Search filter
                if (searchTerm) {
                    const rowText = row.textContent.toLowerCase();
                    if (!rowText.includes(searchTerm)) {
                        visible = false;
                    }
                }

                // Status filter
                if (statusValue && statusValue !== 'all') {
                    const rowStatus = row.getAttribute('data-status-parecer');
                    if (rowStatus !== statusValue) {
                        visible = false;
                    }
                }

                // Tipo filter
                if (tipoValue && tipoValue !== 'all') {
                    const rowTipo = row.getAttribute('data-tipo');
                    if (rowTipo !== tipoValue) {
                        visible = false;
                    }
                }

                row.style.display = visible ? '' : 'none';
            });

            // Update counter
            updateResultsCounter();
        }

        function updateResultsCounter() {
            const visibleRows = table.querySelectorAll('tbody tr:not([style*="display: none"])');
            const totalRows = table.querySelectorAll('tbody tr');
            const counterElement = document.querySelector('.fs-6.fw-semibold.text-gray-700');
            
            if (counterElement) {
                counterElement.textContent = `Mostrando ${visibleRows.length} de ${totalRows.length} proposições`;
            }
        }
    }

    // Bulk selection functionality
    const masterCheckbox = document.querySelector('[data-kt-check="true"]');
    const rowCheckboxes = document.querySelectorAll('#kt_proposicoes_table tbody .form-check-input');

    if (masterCheckbox) {
        masterCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(function(checkbox) {
                checkbox.checked = masterCheckbox.checked;
                toggleRowSelection(checkbox.closest('tr'), checkbox.checked);
            });
        });
    }

    rowCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            toggleRowSelection(this.closest('tr'), this.checked);
            updateMasterCheckbox();
        });
    });

    function toggleRowSelection(row, isSelected) {
        if (isSelected) {
            row.classList.add('table-active');
        } else {
            row.classList.remove('table-active');
        }
    }

    function updateMasterCheckbox() {
        if (masterCheckbox) {
            const checkedBoxes = document.querySelectorAll('#kt_proposicoes_table tbody .form-check-input:checked');
            const totalBoxes = document.querySelectorAll('#kt_proposicoes_table tbody .form-check-input');
            
            if (checkedBoxes.length === 0) {
                masterCheckbox.checked = false;
                masterCheckbox.indeterminate = false;
            } else if (checkedBoxes.length === totalBoxes.length) {
                masterCheckbox.checked = true;
                masterCheckbox.indeterminate = false;
            } else {
                masterCheckbox.checked = false;
                masterCheckbox.indeterminate = true;
            }
        }
    }

    // Enhanced row interactions
    const tableRows = document.querySelectorAll('#kt_proposicoes_table tbody tr');
    tableRows.forEach(function(row) {
        // Add hover effect
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'rgba(0, 0, 0, 0.02)';
        });

        row.addEventListener('mouseleave', function() {
            if (!this.classList.contains('table-active')) {
                this.style.backgroundColor = '';
            }
        });

        // Double click to open main action
        row.addEventListener('dblclick', function() {
            const proposicaoId = this.getAttribute('data-proposicao-id');
            const statusParecer = this.getAttribute('data-status-parecer');
            
            if (statusParecer === 'com_parecer') {
                // Find the view parecer link
                const viewLink = this.querySelector('a[href*="pareceres"]');
                if (viewLink) {
                    window.location.href = viewLink.href;
                }
            } else {
                // Find the create parecer link
                const createLink = this.querySelector('a[href*="parecer"]');
                if (createLink) {
                    window.location.href = createLink.href;
                }
            }
        });
    });

    // Add success animations for actions
    const actionButtons = document.querySelectorAll('.btn-icon');
    actionButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            // Add a subtle animation
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });

    // Initialize result counter
    updateResultsCounter();
    
    // Global functions for card interactions
    window.scrollToTable = function() {
        document.getElementById('kt_proposicoes_table').scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
        });
    };
    
    window.filterByStatus = function(status) {
        const statusFilter = document.querySelector('[data-kt-filter="status-parecer"]');
        if (statusFilter) {
            statusFilter.value = status;
            statusFilter.dispatchEvent(new Event('change'));
            scrollToTable();
        }
    };
    
    window.filterByDate = function(period) {
        // For now, just scroll to table. Could be enhanced to filter by date
        scrollToTable();
        // Future enhancement: implement date filtering
        if (period === 'today') {
            // Filter propositions from today
            const rows = document.querySelectorAll('#kt_proposicoes_table tbody tr');
            const today = new Date().toLocaleDateString('pt-BR');
            
            rows.forEach(function(row) {
                const dateCell = row.cells[3]; // Protocolo column contains date
                if (dateCell) {
                    const dateText = dateCell.textContent.trim();
                    const isToday = dateText.includes(today.split('/').slice(0, 2).join('/'));
                    row.style.display = isToday ? '' : 'none';
                }
            });
            
            updateResultsCounter();
        }
    };
});
</script>

<style>
/* Enhanced table styling */
#kt_proposicoes_table tbody tr {
    transition: all 0.2s ease;
    cursor: pointer;
}

#kt_proposicoes_table tbody tr:hover {
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}

#kt_proposicoes_table tbody tr.table-active {
    background-color: rgba(33, 150, 243, 0.1) !important;
    border-left: 3px solid #2196F3;
}

/* Symbol animations */
.symbol {
    transition: transform 0.2s ease;
}

.symbol:hover {
    transform: scale(1.05);
}

/* Badge enhancements */
.badge {
    transition: all 0.2s ease;
}

.badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

/* Button icon animations */
.btn-icon {
    transition: all 0.2s ease;
}

.btn-icon:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Checkbox styling */
.form-check-input:indeterminate {
    background-color: #009ef7;
    border-color: #009ef7;
}

/* Filter controls */
.card-toolbar .form-select {
    transition: border-color 0.2s ease;
}

.card-toolbar .form-select:focus {
    border-color: #009ef7;
    box-shadow: 0 0 0 0.2rem rgba(0, 158, 247, 0.25);
}

/* Print styles */
@media print {
    .card-toolbar,
    .btn,
    .form-check,
    .pagination {
        display: none !important;
    }
    
    #kt_proposicoes_table {
        font-size: 12px;
    }
    
    .badge {
        border: 1px solid #000;
        color: #000 !important;
        background-color: transparent !important;
    }
}
</style>
@endsection