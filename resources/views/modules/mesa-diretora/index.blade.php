@extends('components.layouts.app')

@section('title', $title ?? 'Mesa Diretora')

@section('content')
<style>
.dashboard-card-primary {
    background: linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.dashboard-card-info {
    background: linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.dashboard-card-success {
    background: linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.dashboard-card-warning {
    background: linear-gradient(135deg, #FFC700 0%, #e6b300 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #FFC700 0%, #e6b300 100%) !important;
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
                    Mesa Diretora
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Mesa Diretora</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('mesa-diretora.atual') }}" class="btn btn-sm fw-bold btn-info">
                    <i class="ki-duotone ki-eye fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    Composição Atual
                </a>
                <a href="{{ route('mesa-diretora.historico') }}" class="btn btn-sm fw-bold btn-secondary">
                    <i class="ki-duotone ki-time fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Histórico
                </a>
                <a href="{{ route('mesa-diretora.create') }}" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-plus fs-2"></i>
                    Novo Membro
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if(isset($error))
                <div class="alert alert-danger">
                    <div class="alert-text">{{ $error }}</div>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <div class="alert-text">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <div class="alert-text">{{ session('error') }}</div>
                </div>
            @endif

            <!--begin::Statistics-->
            <div class="row g-5 g-xl-8">
                <div class="col-xl-3">
                    <div class="card dashboard-card-primary h-md-50 mb-5 mb-xl-8">
                        <div class="card-body">
                            <div class="text-inverse-primary fw-bold fs-2 mb-2 mt-5">
                                {{ $estatisticas['total_membros'] ?? 0 }}
                            </div>
                            <div class="fw-semibold text-inverse-primary">Total de Membros</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="card dashboard-card-success h-md-50 mb-5 mb-xl-8">
                        <div class="card-body">
                            <div class="text-inverse-success fw-bold fs-2 mb-2 mt-5">
                                {{ $estatisticas['membros_ativos'] ?? 0 }}
                            </div>
                            <div class="fw-semibold text-inverse-success">Membros Ativos</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="card dashboard-card-warning h-md-50 mb-5 mb-xl-8">
                        <div class="card-body">
                            <div class="text-inverse-warning fw-bold fs-2 mb-2 mt-5">
                                {{ $estatisticas['mandatos_finalizados'] ?? 0 }}
                            </div>
                            <div class="fw-semibold text-inverse-warning">Mandatos Finalizados</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="card dashboard-card-info h-md-50 mb-5 mb-xl-8">
                        <div class="card-body">
                            <div class="text-inverse-info fw-bold fs-2 mb-2 mt-5">
                                {{ count($cargos_disponiveis ?? []) }}
                            </div>
                            <div class="fw-semibold text-inverse-info">Cargos Disponíveis</div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Statistics-->

            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-6">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <!--begin::Search-->
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" data-kt-member-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Buscar membro..." />
                        </div>
                        <!--end::Search-->
                    </div>
                    <!--begin::Card title-->
                    
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar">
                        <!--begin::Toolbar-->
                        <div class="d-flex justify-content-end" data-kt-member-table-toolbar="base">
                            <!--begin::Filter-->
                            <div class="me-3">
                                <select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="Filtrar por status" data-kt-member-table-filter="status">
                                    <option></option>
                                    <option value="ativo">Ativo</option>
                                    <option value="finalizado">Finalizado</option>
                                </select>
                            </div>
                            <!--end::Filter-->
                            
                            <!--begin::Filter-->
                            <div class="me-3">
                                <select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="Filtrar por cargo" data-kt-member-table-filter="cargo">
                                    <option></option>
                                    @foreach($cargos_disponiveis as $cargo)
                                        <option value="{{ $cargo }}">{{ $cargo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Filter-->
                            
                            <!--begin::Add member-->
                            <a href="{{ route('mesa-diretora.create') }}" class="btn btn-primary">
                                <i class="ki-duotone ki-plus fs-2"></i>
                                Novo Membro
                            </a>
                            <!--end::Add member-->
                        </div>
                        <!--end::Toolbar-->
                    </div>
                    <!--end::Card toolbar-->
                </div>
                <!--end::Card header-->
                
                <!--begin::Card body-->
                <div class="card-body py-4">
                    <!--begin::Table-->
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_members_table">
                            <!--begin::Table head-->
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-125px">Parlamentar</th>
                                    <th class="min-w-125px">Cargo</th>
                                    <th class="min-w-125px">Mandato</th>
                                    <th class="min-w-100px">Status</th>
                                    <th class="text-end min-w-100px">Ações</th>
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            
                            <!--begin::Table body-->
                            <tbody class="text-gray-600 fw-semibold">
                                @forelse($membros as $membro)
                                <tr>
                                    <td class="d-flex align-items-center">
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column">
                                            <a href="{{ route('mesa-diretora.show', $membro['id']) }}" class="text-gray-800 text-hover-primary mb-1">{{ $membro['parlamentar_nome'] }}</a>
                                            <span class="text-muted">{{ $membro['parlamentar_partido'] }}</span>
                                        </div>
                                        <!--begin::User details-->
                                    </td>
                                    <td>
                                        <div class="badge badge-light-primary">{{ $membro['cargo_formatado'] }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800 mb-1">{{ $membro['mandato_formatado'] }}</span>
                                            @if($membro['is_mandato_ativo'])
                                                <span class="badge badge-light-success badge-sm">Mandato Ativo</span>
                                            @else
                                                <span class="badge badge-light-secondary badge-sm">Mandato Inativo</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($membro['status'] === 'ativo')
                                            <div class="badge badge-light-success">{{ $membro['status_formatado'] }}</div>
                                        @else
                                            <div class="badge badge-light-secondary">{{ $membro['status_formatado'] }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            Ações
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                        </a>
                                        <!--begin::Menu-->
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="{{ route('mesa-diretora.show', $membro['id']) }}" class="menu-link px-3">
                                                    Visualizar
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
                                            
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="{{ route('mesa-diretora.edit', $membro['id']) }}" class="menu-link px-3">
                                                    Editar
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
                                            
                                            @if($membro['status'] === 'ativo' && $membro['is_mandato_ativo'])
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3" data-kt-member-id="{{ $membro['id'] }}" data-kt-action="finalizar_mandato">
                                                    Finalizar Mandato
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
                                            @endif
                                            
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3 text-danger" data-kt-member-id="{{ $membro['id'] }}" data-kt-action="delete_row">
                                                    Excluir
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
                                        </div>
                                        <!--end::Menu-->
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ki-duotone ki-search-list fs-3x text-gray-500 mb-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            <span class="text-gray-700 fs-6">Nenhum membro encontrado</span>
                                            <a href="{{ route('mesa-diretora.create') }}" class="btn btn-primary btn-sm mt-3">
                                                <i class="ki-duotone ki-plus fs-2"></i>
                                                Adicionar Primeiro Membro
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <!--end::Table body-->
                        </table>
                    </div>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<!-- Modal para confirmar exclusão -->
<div class="modal fade" id="kt_modal_delete_member" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Confirmar Exclusão</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-members-modal-action="close">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_delete_member_form" class="form" action="#" method="POST">
                    @csrf
                    @method('DELETE')
                    
                    <div class="fv-row mb-7">
                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                            <i class="ki-duotone ki-warning fs-2tx text-warning me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <h4 class="text-gray-900 fw-bold">Atenção!</h4>
                                    <div class="fs-6 text-gray-700">Tem certeza que deseja excluir este membro da mesa diretora? Esta ação não pode ser desfeita.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-kt-members-modal-action="cancel">Cancelar</button>
                        <button type="submit" class="btn btn-danger" data-kt-indicator="off">
                            <span class="indicator-label">Excluir</span>
                            <span class="indicator-progress">Por favor aguarde...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para finalizar mandato -->
<div class="modal fade" id="kt_modal_finalizar_mandato" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Finalizar Mandato</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-members-modal-action="close">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_finalizar_mandato_form" class="form" action="#" method="POST">
                    @csrf
                    
                    <div class="fv-row mb-7">
                        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6">
                            <i class="ki-duotone ki-information fs-2tx text-info me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <h4 class="text-gray-900 fw-bold">Finalizar Mandato</h4>
                                    <div class="fs-6 text-gray-700">Tem certeza que deseja finalizar este mandato? O status será alterado para "finalizado" e a data de fim será ajustada para hoje.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-kt-members-modal-action="cancel">Cancelar</button>
                        <button type="submit" class="btn btn-warning" data-kt-indicator="off">
                            <span class="indicator-label">Finalizar Mandato</span>
                            <span class="indicator-progress">Por favor aguarde...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
"use strict";

var KTMembersList = function () {
    var table = document.getElementById('kt_members_table');
    var datatable;
    var toolbarBase;
    var toolbarSelected;
    var selectedCount;
    
    var initMemberTable = function () {
        if (!table) {
            return;
        }

        datatable = $(table).DataTable({
            'columnDefs': [
                { orderable: false, targets: 4 }
            ]
        });
    }

    var handleSearchDatatable = function () {
        const filterSearch = document.querySelector('[data-kt-member-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    }

    var handleFilterDatatable = function () {
        const filterStatus = document.querySelector('[data-kt-member-table-filter="status"]');
        const filterCargo = document.querySelector('[data-kt-member-table-filter="cargo"]');
        
        filterStatus.addEventListener('change', function (e) {
            datatable.column(3).search(e.target.value).draw();
        });
        
        filterCargo.addEventListener('change', function (e) {
            datatable.column(1).search(e.target.value).draw();
        });
    }

    var handleDeleteRows = function () {
        const deleteButtons = table.querySelectorAll('[data-kt-action="delete_row"]');

        deleteButtons.forEach(d => {
            d.addEventListener('click', function (e) {
                e.preventDefault();

                const parent = e.target.closest('tr');
                const memberName = parent.querySelectorAll('td')[0].innerText;
                const memberId = d.getAttribute('data-kt-member-id');

                const modal = document.querySelector('#kt_modal_delete_member');
                const form = modal.querySelector('#kt_modal_delete_member_form');
                
                form.setAttribute('action', '{{ route("mesa-diretora.destroy", ":id") }}'.replace(':id', memberId));
                
                $(modal).modal('show');
            })
        });
    }

    var handleFinalizarMandato = function () {
        const finalizarButtons = table.querySelectorAll('[data-kt-action="finalizar_mandato"]');

        finalizarButtons.forEach(d => {
            d.addEventListener('click', function (e) {
                e.preventDefault();

                const parent = e.target.closest('tr');
                const memberName = parent.querySelectorAll('td')[0].innerText;
                const memberId = d.getAttribute('data-kt-member-id');

                const modal = document.querySelector('#kt_modal_finalizar_mandato');
                const form = modal.querySelector('#kt_modal_finalizar_mandato_form');
                
                form.setAttribute('action', '{{ route("mesa-diretora.finalizar", ":id") }}'.replace(':id', memberId));
                
                $(modal).modal('show');
            })
        });
    }

    var handleModalCancel = function () {
        const cancelButtons = document.querySelectorAll('[data-kt-members-modal-action="cancel"]');
        const closeButtons = document.querySelectorAll('[data-kt-members-modal-action="close"]');

        cancelButtons.forEach(c => {
            c.addEventListener('click', function (e) {
                e.preventDefault();
                const modal = c.closest('.modal');
                $(modal).modal('hide');
            })
        });

        closeButtons.forEach(c => {
            c.addEventListener('click', function (e) {
                e.preventDefault();
                const modal = c.closest('.modal');
                $(modal).modal('hide');
            })
        });
    }

    return {
        init: function () {
            initMemberTable();
            
            if (datatable) {
                handleSearchDatatable();
                handleFilterDatatable();
                handleDeleteRows();
                handleFinalizarMandato();
                handleModalCancel();
            }
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTMembersList.init();
});
</script>
@endpush