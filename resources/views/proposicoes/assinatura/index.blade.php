@extends('components.layouts.app')

@section('title', 'Minhas Proposições - Assinatura')

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
</style>

<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Assinatura de Proposições
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Assinatura</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('proposicoes.minhas-proposicoes') }}" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-primary btn-active-light-primary">
                    <i class="ki-duotone ki-element-11 fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>Todas as Proposições
                </a>
                <a href="{{ route('proposicoes.historico-assinaturas') }}" class="btn btn-sm btn-primary">
                    <i class="ki-duotone ki-time fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>Histórico
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!-- Estatísticas Rápidas -->
            <!--begin::Row-->
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                <!--begin::Col-->
                <div class="col-xl-4">
                    <!--begin::Statistics Widget 5-->
                    <div class="card bg-success hoverable card-xl-stretch mb-xl-8">
                        <!--begin::Body-->
                        <div class="card-body">
                            <i class="ki-duotone ki-check-circle text-white fs-2x ms-n1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="text-white fw-bold fs-2 mb-2 mt-5">{{ $proposicoes->where('status', 'aprovado_assinatura')->count() }}</div>
                            <div class="fw-semibold text-white opacity-75">Aguardando Assinatura</div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Statistics Widget 5-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-xl-4">
                    <!--begin::Statistics Widget 5-->
                    <div class="card bg-warning hoverable card-xl-stretch mb-xl-8">
                        <!--begin::Body-->
                        <div class="card-body">
                            <i class="ki-duotone ki-information text-white fs-2x ms-n1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="text-white fw-bold fs-2 mb-2 mt-5">{{ $proposicoes->where('status', 'devolvido_correcao')->count() }}</div>
                            <div class="fw-semibold text-white opacity-75">Para Correção</div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Statistics Widget 5-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-xl-4">
                    <!--begin::Statistics Widget 5-->
                    <div class="card bg-info hoverable card-xl-stretch mb-xl-8">
                        <!--begin::Body-->
                        <div class="card-body">
                            <i class="ki-duotone ki-element-11 text-white fs-2x ms-n1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                            <div class="text-white fw-bold fs-2 mb-2 mt-5">{{ $proposicoes->total() }}</div>
                            <div class="fw-semibold text-white opacity-75">Total Pendente</div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Statistics Widget 5-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

            <!-- Abas -->
            <!--begin::Card-->
            <div class="card">
                <!--begin::Header-->
                <div class="card-header border-0 pt-6">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <!--begin::Search-->
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" data-kt-customer-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Buscar proposições..." />
                        </div>
                        <!--end::Search-->
                    </div>
                    <!--begin::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar">
                        <!--begin::Toolbar-->
                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                            <!--begin::Menu-->
                            <div class="w-150px">
                                <!--begin::Select2-->
                                <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Status" data-kt-customer-table-filter="status">
                                    <option></option>
                                    <option value="all">Todos</option>
                                    <option value="aprovado_assinatura">Aprovadas</option>
                                    <option value="devolvido_correcao">Devolvidas</option>
                                </select>
                                <!--end::Select2-->
                            </div>
                            <!--end::Menu-->
                        </div>
                        <!--end::Toolbar-->
                    </div>
                    <!--end::Card toolbar-->
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body pt-0">
                    <!--begin::Nav-->
                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold" id="tab-assinatura" role="tablist">
                        <!--begin::Nav item-->
                        <li class="nav-item mt-2" role="presentation">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5 active" id="aprovadas-tab" data-bs-toggle="tab" 
                                    data-bs-target="#aprovadas" type="button" role="tab">
                                <i class="ki-duotone ki-check-circle fs-2 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Aprovadas para Assinatura
                                <span class="badge badge-light-success ms-2">{{ $proposicoes->where('status', 'aprovado_assinatura')->count() }}</span>
                            </a>
                        </li>
                        <!--end::Nav item-->
                        <!--begin::Nav item-->
                        <li class="nav-item mt-2" role="presentation">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5" id="devolvidas-tab" data-bs-toggle="tab" 
                                    data-bs-target="#devolvidas" type="button" role="tab">
                                <i class="ki-duotone ki-information fs-2 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                Devolvidas para Correção
                                <span class="badge badge-light-warning ms-2">{{ $proposicoes->where('status', 'devolvido_correcao')->count() }}</span>
                            </a>
                        </li>
                        <!--end::Nav item-->
                    </ul>
                    <!--end::Nav-->

                    <!--begin::Tab Content-->
                    <div class="tab-content" id="tab-assinatura-content">
                        <!-- Aba: Aprovadas para Assinatura -->
                        <!--begin::Tab pane-->
                        <div class="tab-pane fade show active" id="aprovadas" role="tabpanel">
                            @php $aprovadas = $proposicoes->where('status', 'aprovado_assinatura') @endphp
                            
                            @if($aprovadas->count() > 0)
                                <!--begin::Table-->
                                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_customers_table">
                                    <!--begin::Table head-->
                                    <thead>
                                        <!--begin::Table row-->
                                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                            <th class="min-w-125px">Proposição</th>
                                            <th class="min-w-250px">Ementa</th>
                                            <th class="min-w-125px">Revisão</th>
                                            <th class="min-w-125px">Data Aprovação</th>
                                            <th class="min-w-100px">Urgência</th>
                                            <th class="text-end min-w-70px">Ações</th>
                                        </tr>
                                        <!--end::Table row-->
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody class="fw-semibold text-gray-600">
                                        @foreach($aprovadas as $proposicao)
                                        <!--begin::Table row-->
                                        <tr>
                                            <!--begin::Proposição-->
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge badge-light-secondary fw-bold fs-8 px-2 py-1 me-2">{{ $proposicao->tipo }}</span>
                                                    </div>
                                                    <a href="{{ route('proposicoes.show', $proposicao) }}" class="text-gray-800 text-hover-primary fs-6 fw-bold mb-1 mt-1">{{ $proposicao->titulo ?? 'Sem título' }}</a>
                                                    @if($proposicao->numero_temporario)
                                                        <span class="text-muted fs-7">Nº {{ $proposicao->numero_temporario }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <!--end::Proposição-->
                                            <!--begin::Ementa-->
                                            <td>
                                                <div class="text-gray-600 fs-7" title="{{ $proposicao->ementa }}">
                                                    {{ Str::limit($proposicao->ementa, 100) }}
                                                </div>
                                            </td>
                                            <!--end::Ementa-->
                                            <!--begin::Revisão-->
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-gray-800 fs-6 fw-bold mb-1">{{ $proposicao->revisor->name ?? 'N/A' }}</span>
                                                    <span class="badge badge-light-success fs-7">Aprovado</span>
                                                </div>
                                            </td>
                                            <!--end::Revisão-->
                                            <!--begin::Data-->
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-gray-800 fs-6 fw-bold mb-1">
                                                        {{ $proposicao->data_revisao ? $proposicao->data_revisao->format('d/m/Y') : 'N/A' }}
                                                    </span>
                                                    @if($proposicao->data_revisao)
                                                        <span class="text-muted fs-7">{{ $proposicao->data_revisao->format('H:i') }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <!--end::Data-->
                                            <!--begin::Urgência-->
                                            <td>
                                                @if($proposicao->urgencia === 'urgentissima')
                                                    <span class="badge badge-light-danger">Urgentíssima</span>
                                                @elseif($proposicao->urgencia === 'urgente')
                                                    <span class="badge badge-light-warning">Urgente</span>
                                                @else
                                                    <span class="badge badge-light-secondary">Normal</span>
                                                @endif
                                            </td>
                                            <!--end::Urgência-->
                                            <!--begin::Action-->
                                            <td class="text-end">
                                                <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    Ações
                                                    <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                                </a>
                                                <!--begin::Menu-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="{{ route('proposicoes.show', $proposicao) }}" class="menu-link px-3">
                                                            <i class="ki-duotone ki-eye fs-6 me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>Visualizar
                                                        </a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="{{ route('proposicoes.assinar', $proposicao) }}" class="menu-link px-3">
                                                            <i class="ki-duotone ki-pencil fs-6 me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>Assinar
                                                        </a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu-->
                                            </td>
                                            <!--end::Action-->
                                        </tr>
                                        <!--end::Table row-->
                                        @endforeach
                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            @else
                                <!--begin::Empty state-->
                                <div class="d-flex flex-column flex-center">
                                    <img src="/assets/media/illustrations/sketchy-1/2.png" class="mw-300px" alt="No data" />
                                    <div class="fs-1 fw-bolder text-dark mb-4">Nenhuma proposição aprovada</div>
                                    <div class="fs-6">Suas proposições aparecerão aqui após aprovação pela revisão legislativa.</div>
                                </div>
                                <!--end::Empty state-->
                            @endif
                        </div>
                        <!--end::Tab pane-->

                        <!-- Aba: Devolvidas para Correção -->
                        <!--begin::Tab pane-->
                        <div class="tab-pane fade" id="devolvidas" role="tabpanel">
                            @php $devolvidas = $proposicoes->where('status', 'devolvido_correcao') @endphp
                            
                            @if($devolvidas->count() > 0)
                                <!--begin::Table-->
                                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_customers_table2">
                                    <!--begin::Table head-->
                                    <thead>
                                        <!--begin::Table row-->
                                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                            <th class="min-w-125px">Proposição</th>
                                            <th class="min-w-200px">Ementa</th>
                                            <th class="min-w-200px">Motivo da Devolução</th>
                                            <th class="min-w-125px">Data Devolução</th>
                                            <th class="min-w-100px">Urgência</th>
                                            <th class="text-end min-w-70px">Ações</th>
                                        </tr>
                                        <!--end::Table row-->
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody class="fw-semibold text-gray-600">
                                        @foreach($devolvidas as $proposicao)
                                        <!--begin::Table row-->
                                        <tr>
                                            <!--begin::Proposição-->
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge badge-light-secondary fw-bold fs-8 px-2 py-1 me-2">{{ $proposicao->tipo }}</span>
                                                    </div>
                                                    <a href="{{ route('proposicoes.show', $proposicao) }}" class="text-gray-800 text-hover-primary fs-6 fw-bold mb-1 mt-1">{{ $proposicao->titulo ?? 'Sem título' }}</a>
                                                    @if($proposicao->numero_temporario)
                                                        <span class="text-muted fs-7">Nº {{ $proposicao->numero_temporario }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <!--end::Proposição-->
                                            <!--begin::Ementa-->
                                            <td>
                                                <div class="text-gray-600 fs-7" title="{{ $proposicao->ementa }}">
                                                    {{ Str::limit($proposicao->ementa, 80) }}
                                                </div>
                                            </td>
                                            <!--end::Ementa-->
                                            <!--begin::Motivo-->
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-gray-600 fs-7" title="{{ $proposicao->parecer_tecnico }}">
                                                        {{ Str::limit($proposicao->parecer_tecnico, 80) }}
                                                    </span>
                                                    @if($proposicao->revisor)
                                                        <span class="text-muted fs-8 mt-1">Por: {{ $proposicao->revisor->name }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <!--end::Motivo-->
                                            <!--begin::Data-->
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-gray-800 fs-6 fw-bold mb-1">
                                                        {{ $proposicao->data_revisao ? $proposicao->data_revisao->format('d/m/Y') : 'N/A' }}
                                                    </span>
                                                    @if($proposicao->data_revisao)
                                                        <span class="text-muted fs-7">{{ $proposicao->data_revisao->format('H:i') }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <!--end::Data-->
                                            <!--begin::Urgência-->
                                            <td>
                                                @if($proposicao->urgencia === 'urgentissima')
                                                    <span class="badge badge-light-danger">Urgentíssima</span>
                                                @elseif($proposicao->urgencia === 'urgente')
                                                    <span class="badge badge-light-warning">Urgente</span>
                                                @else
                                                    <span class="badge badge-light-secondary">Normal</span>
                                                @endif
                                            </td>
                                            <!--end::Urgência-->
                                            <!--begin::Action-->
                                            <td class="text-end">
                                                <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    Ações
                                                    <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                                </a>
                                                <!--begin::Menu-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="{{ route('proposicoes.show', $proposicao) }}" class="menu-link px-3">
                                                            <i class="ki-duotone ki-eye fs-6 me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>Visualizar
                                                        </a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="{{ route('proposicoes.corrigir', $proposicao) }}" class="menu-link px-3">
                                                            <i class="ki-duotone ki-pencil fs-6 me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>Corrigir
                                                        </a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu-->
                                            </td>
                                            <!--end::Action-->
                                        </tr>
                                        <!--end::Table row-->
                                        @endforeach
                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            @else
                                <!--begin::Empty state-->
                                <div class="d-flex flex-column flex-center">
                                    <img src="/assets/media/illustrations/sketchy-1/5.png" class="mw-300px" alt="No data" />
                                    <div class="fs-1 fw-bolder text-dark mb-4">Nenhuma proposição devolvida</div>
                                    <div class="fs-6">Parabéns! Não há proposições que precisem de correção no momento.</div>
                                </div>
                                <!--end::Empty state-->
                            @endif
                        </div>
                        <!--end::Tab pane-->
                    </div>
                    <!--end::Tab Content-->
                </div>
                <!--end::Body-->
                
                <!-- Paginação -->
                @if($proposicoes->hasPages())
                    <!--begin::Card footer-->
                    <div class="card-footer">
                        {{ $proposicoes->links() }}
                    </div>
                    <!--end::Card footer-->
                @endif
            </div>
            <!--end::Card-->

        </div>
        <!--end::Container-->
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection

@push('scripts')
<script>
"use strict";

// Class definition
var KTAssinaturasTable = function () {
    // Shared variables
    var table;
    var table2;
    var dt;
    var dt2;
    var filterStatus;

    // Private functions
    var initDatatable = function () {
        dt = $("#kt_customers_table").DataTable({
            info: false,
            order: [],
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: 5 }, // Disable ordering on column 5 (actions)
            ],
        });
        
        dt2 = $("#kt_customers_table2").DataTable({
            info: false,
            order: [],
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: 5 }, // Disable ordering on column 5 (actions)
            ],
        });

        table = dt.$;
        table2 = dt2.$;
    }

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = function () {
        const filterSearch = document.querySelector('[data-kt-customer-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            dt.search(e.target.value).draw();
            dt2.search(e.target.value).draw();
        });
    }

    // Filter Datatable
    var handleFilterDatatable = () => {
        // Select filter options
        filterStatus = document.querySelector('[data-kt-customer-table-filter="status"]');

        // Filter datatable on submit
        filterStatus.addEventListener('change', function (e) {
            let value = e.target.value;
            if (value === 'all') {
                value = '';
            }
            dt.column(4).search(value).draw();
            dt2.column(4).search(value).draw();
        });
    }

    // Public methods
    return {
        init: function () {
            if (!table) {
                return;
            }

            initDatatable();
            handleSearchDatatable();
            handleFilterDatatable();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTAssinaturasTable.init();
});

// Tab persistence
$(document).ready(function() {
    // Manter a aba ativa baseada na URL ou localStorage
    const activeTab = localStorage.getItem('assinatura-active-tab') || 'aprovadas-tab';
    
    if (activeTab) {
        const tabTrigger = new bootstrap.Tab(document.getElementById(activeTab));
        tabTrigger.show();
    }

    // Salvar aba ativa
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        localStorage.setItem('assinatura-active-tab', e.target.id);
    });

    // Auto-refresh das estatísticas a cada 60 segundos
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            window.location.reload();
        }
    }, 60000);

    // Tooltips para textos truncados
    $('[title]').tooltip();
});
</script>
@endpush