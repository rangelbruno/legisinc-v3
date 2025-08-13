@extends('components.layouts.app')

@section('title', 'Protocolos de Hoje')

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
                    Protocolos de Hoje
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
                    <li class="breadcrumb-item text-muted">Protocolos de Hoje</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('proposicoes.protocolar') }}" class="btn btn-sm btn-flex btn-outline btn-active-color-primary">
                    <i class="ki-duotone ki-arrow-left fs-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Voltar
                </a>
                <button type="button" class="btn btn-sm btn-primary" onclick="window.print()">
                    <i class="ki-duotone ki-printer fs-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                        <span class="path5"></span>
                    </i>
                    Imprimir
                </button>
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
                                <span class="fs-2hx fw-bold text-white me-2">{{ $proposicoes->total() }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">proposições</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Protocoladas Hoje</span>
                                @php
                                    $totalMes = \App\Models\Proposicao::where('status', 'protocolado')
                                        ->whereMonth('data_protocolo', now()->month)
                                        ->count();
                                    $percentualHoje = $totalMes > 0 ? round(($proposicoes->total() / $totalMes) * 100) : 100;
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
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-primary cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-document text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $proposicoes->where('tipo', 'PL')->count() }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">projetos</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Projetos de Lei</span>
                                @php
                                    $percentualPL = $proposicoes->total() > 0 ? round(($proposicoes->where('tipo', 'PL')->count() / $proposicoes->total()) * 100) : 0;
                                @endphp
                                <span class="badge badge-light-primary fs-8">{{ $percentualPL }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $percentualPL }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col-->
                
                <!--begin::Col-->
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-warning cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-paper-clip text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $proposicoes->whereIn('tipo', ['mocao', 'indicacao', 'requerimento'])->count() }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">documentos</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Outros Tipos</span>
                                @php
                                    $percentualOutros = $proposicoes->total() > 0 ? round(($proposicoes->whereIn('tipo', ['mocao', 'indicacao', 'requerimento'])->count() / $proposicoes->total()) * 100) : 0;
                                @endphp
                                <span class="badge badge-light-warning fs-8">{{ $percentualOutros }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $percentualOutros }}%"></div>
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
                                <i class="ki-duotone ki-profile-user text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $proposicoes->unique('autor_id')->count() }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">parlamentares</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Autores Diferentes</span>
                                @php
                                    $totalParlamentares = \App\Models\User::whereHas('roles', function($q) {
                                        $q->where('name', 'PARLAMENTAR');
                                    })->count();
                                    $percentualAutores = $totalParlamentares > 0 ? round(($proposicoes->unique('autor_id')->count() / $totalParlamentares) * 100) : 0;
                                @endphp
                                <span class="badge badge-light-info fs-8">{{ $percentualAutores }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $percentualAutores }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Stats-->

            <!--begin::Proposições-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Proposições Protocoladas em {{ now()->format('d/m/Y') }}</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Lista completa de protocolos realizados hoje</span>
                    </h3>
                    <div class="card-toolbar">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-1 position-absolute ms-6">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" data-kt-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Buscar protocolo..." />
                        </div>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body py-4">
                    @if($proposicoes->count() > 0)
                        <!--begin::Table-->
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_protocolos_table">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-100px">Nº Protocolo</th>
                                        <th class="min-w-125px">Tipo</th>
                                        <th class="min-w-125px">Autor</th>
                                        <th class="min-w-250px">Ementa</th>
                                        <th class="min-w-125px">Hora Protocolo</th>
                                        <th class="min-w-125px">Funcionário</th>
                                        <th class="text-end min-w-100px">Ações</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="text-gray-600 fw-semibold">
                                    @foreach($proposicoes as $proposicao)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-800 fw-bold">{{ $proposicao->numero_protocolo }}</span>
                                                <span class="text-muted fs-7">Sequencial: {{ $proposicao->numero_sequencial ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-primary fw-bold fs-8 px-2 py-1">{{ $proposicao->tipo }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-800 fw-bold mb-1">{{ $proposicao->autor->name }}</span>
                                                <span class="text-muted fs-7">{{ $proposicao->autor->cargo_atual ?? 'Parlamentar' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-gray-600" style="max-width: 300px;" title="{{ $proposicao->ementa }}">
                                                {{ Str::limit($proposicao->ementa, 100) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-800 fw-bold">{{ $proposicao->data_protocolo->format('H:i:s') }}</span>
                                                <span class="text-muted fs-7">{{ $proposicao->data_protocolo->diffForHumans() }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($proposicao->funcionarioProtocolo)
                                                <div class="d-flex flex-column">
                                                    <span class="text-gray-800 fw-bold mb-1">{{ $proposicao->funcionarioProtocolo->name }}</span>
                                                    <span class="text-muted fs-7">Protocolo</span>
                                                </div>
                                            @else
                                                <span class="text-muted">Sistema</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('proposicoes.show', $proposicao) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Visualizar">
                                                <i class="ki-duotone ki-eye fs-3">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                            </a>
                                            @if($proposicao->arquivo_pdf_path)
                                                <a href="{{ route('proposicoes.serve-pdf', $proposicao) }}" target="_blank" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver PDF">
                                                    <i class="ki-duotone ki-file-pdf fs-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                <!--end::Table body-->
                                </tbody>
                            </table>
                        </div>
                        <!--end::Table-->
                        
                        <!--begin::Pagination-->
                        <div class="d-flex justify-content-between align-items-center pt-10">
                            <div class="fs-6 fw-semibold text-gray-700">
                                Mostrando {{ $proposicoes->firstItem() }} a {{ $proposicoes->lastItem() }} de {{ $proposicoes->total() }} proposições
                            </div>
                            {{ $proposicoes->links() }}
                        </div>
                        <!--end::Pagination-->
                    @else
                        <!--begin::Empty state-->
                        <div class="text-center py-20">
                            <div class="mb-7">
                                <i class="ki-duotone ki-calendar-8 fs-5x text-muted">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                    <span class="path6"></span>
                                </i>
                            </div>
                            <h3 class="text-gray-800 fw-bold fs-2 mb-4">Nenhuma proposição protocolada hoje</h3>
                            <p class="text-gray-600 fw-semibold fs-6 mb-7">
                                Ainda não foram realizados protocolos na data de hoje.
                            </p>
                            <a href="{{ route('proposicoes.protocolar') }}" class="btn btn-primary">
                                <i class="ki-duotone ki-arrow-left fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Voltar para Protocolo
                            </a>
                        </div>
                        <!--end::Empty state-->
                    @endif
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Proposições-->
            
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
var KTProtocolosHoje = function() {
    // Define shared variables
    var table = document.getElementById('kt_protocolos_table');

    // Search Datatable
    var handleSearchDatatable = function() {
        const filterSearch = document.querySelector('[data-kt-filter="search"]');
        if (filterSearch) {
            filterSearch.addEventListener('keyup', function(e) {
                const searchText = e.target.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchText)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    }

    // Public methods
    return {
        init: function() {
            if (!table) {
                return;
            }

            handleSearchDatatable();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTProtocolosHoje.init();
});

// Auto-refresh a cada 60 segundos
setInterval(function() {
    if (document.visibilityState === 'visible') {
        window.location.reload();
    }
}, 60000);
</script>
@endpush

@push('styles')
<style>
@media print {
    .app-toolbar,
    .card-toolbar,
    .btn,
    .pagination {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .table {
        font-size: 12px !important;
    }
}
</style>
@endpush