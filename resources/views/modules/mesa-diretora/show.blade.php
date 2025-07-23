@extends('components.layouts.app')

@section('title', $title ?? 'Membro - Mesa Diretora')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ $membro['parlamentar_nome'] }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('mesa-diretora.index') }}" class="text-muted text-hover-primary">Mesa Diretora</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">{{ $membro['parlamentar_nome'] }}</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('mesa-diretora.edit', $membro['id']) }}" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-pencil fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Editar
                </a>
                <a href="{{ route('mesa-diretora.index') }}" class="btn btn-sm fw-bold btn-secondary">
                    <i class="ki-duotone ki-black-left fs-2"></i>
                    Voltar
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
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

            <!--begin::Layout-->
            <div class="d-flex flex-column flex-lg-row">
                <!--begin::Sidebar-->
                <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
                    <!--begin::Card-->
                    <div class="card mb-5 mb-xl-8">
                        <!--begin::Card body-->
                        <div class="card-body">
                            <!--begin::Summary-->
                            <!--begin::User Info-->
                            <div class="d-flex flex-center flex-column py-5">
                                <!--begin::Avatar-->
                                <div class="symbol symbol-100px symbol-circle mb-7">
                                    <div class="symbol-label fs-2 fw-semibold text-success">
                                        {{ substr($membro['parlamentar_nome'], 0, 1) }}
                                    </div>
                                </div>
                                <!--end::Avatar-->
                                
                                <!--begin::Name-->
                                <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-3">
                                    {{ $membro['parlamentar_nome'] }}
                                </a>
                                <!--end::Name-->
                                
                                <!--begin::Position-->
                                <div class="mb-9">
                                    <div class="badge badge-lg badge-light-primary d-inline">
                                        {{ $membro['cargo_formatado'] }}
                                    </div>
                                </div>
                                <!--end::Position-->
                            </div>
                            <!--end::User Info-->
                            
                            <!--begin::Info-->
                            <div class="d-flex flex-wrap flex-center">
                                <!--begin::Stats-->
                                <div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3">
                                    <div class="fs-4 fw-bold text-gray-700">
                                        <span class="w-75px">Status</span>
                                        @if($membro['status'] === 'ativo')
                                            <div class="badge badge-light-success">{{ $membro['status_formatado'] }}</div>
                                        @else
                                            <div class="badge badge-light-secondary">{{ $membro['status_formatado'] }}</div>
                                        @endif
                                    </div>
                                </div>
                                <!--end::Stats-->
                            </div>
                            <!--end::Info-->
                            <!--end::Summary-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Sidebar-->
                
                <!--begin::Content-->
                <div class="flex-lg-row-fluid ms-lg-15">
                    <!--begin::Tab Content-->
                    <div class="tab-content" id="myTabContent">
                        <!--begin::Tab Pane-->
                        <div class="tab-pane fade show active" id="kt_member_view_overview_tab" role="tabpanel">
                            <!--begin::Card-->
                            <div class="card pt-4 mb-6 mb-xl-9">
                                <!--begin::Card header-->
                                <div class="card-header border-0">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <h2>Informações do Mandato</h2>
                                    </div>
                                    <!--end::Card title-->
                                </div>
                                <!--end::Card header-->
                                
                                <!--begin::Card body-->
                                <div class="card-body pt-0 pb-5">
                                    <!--begin::Table wrapper-->
                                    <div class="table-responsive">
                                        <!--begin::Table-->
                                        <table class="table align-middle table-row-dashed gy-5">
                                            <tbody class="fs-6 fw-semibold text-gray-600">
                                                <tr>
                                                    <td class="text-muted">
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-profile-user fs-2 me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                                <span class="path4"></span>
                                                            </i>
                                                            Parlamentar
                                                        </div>
                                                    </td>
                                                    <td class="fw-bold text-end">
                                                        <div class="d-flex align-items-center justify-content-end">
                                                            <div class="d-flex flex-column">
                                                                <span class="text-gray-800 mb-1">{{ $membro['parlamentar_nome'] }}</span>
                                                                <span class="text-muted">{{ $membro['parlamentar_partido'] }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-award fs-2 me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                            Cargo na Mesa
                                                        </div>
                                                    </td>
                                                    <td class="fw-bold text-end">
                                                        <div class="badge badge-light-primary">{{ $membro['cargo_formatado'] }}</div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-calendar fs-2 me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            Período do Mandato
                                                        </div>
                                                    </td>
                                                    <td class="fw-bold text-end">{{ $membro['mandato_formatado'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-status fs-2 me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            Status do Mandato
                                                        </div>
                                                    </td>
                                                    <td class="fw-bold text-end">
                                                        @if($membro['is_mandato_ativo'])
                                                            <div class="badge badge-light-success">Mandato Ativo</div>
                                                        @else
                                                            <div class="badge badge-light-secondary">Mandato Inativo</div>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-check-circle fs-2 me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            Status
                                                        </div>
                                                    </td>
                                                    <td class="fw-bold text-end">
                                                        @if($membro['status'] === 'ativo')
                                                            <div class="badge badge-light-success">{{ $membro['status_formatado'] }}</div>
                                                        @else
                                                            <div class="badge badge-light-secondary">{{ $membro['status_formatado'] }}</div>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-calendar-add fs-2 me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                                <span class="path4"></span>
                                                                <span class="path5"></span>
                                                                <span class="path6"></span>
                                                            </i>
                                                            Registrado em
                                                        </div>
                                                    </td>
                                                    <td class="fw-bold text-end">{{ $membro['created_at'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-calendar-edit fs-2 me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                                <span class="path4"></span>
                                                                <span class="path5"></span>
                                                                <span class="path6"></span>
                                                            </i>
                                                            Última Atualização
                                                        </div>
                                                    </td>
                                                    <td class="fw-bold text-end">{{ $membro['updated_at'] }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <!--end::Table-->
                                    </div>
                                    <!--end::Table wrapper-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->

                            @if($membro['observacoes'])
                            <!--begin::Card-->
                            <div class="card pt-4 mb-6 mb-xl-9">
                                <!--begin::Card header-->
                                <div class="card-header border-0">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <h2>Observações</h2>
                                    </div>
                                    <!--end::Card title-->
                                </div>
                                <!--end::Card header-->
                                
                                <!--begin::Card body-->
                                <div class="card-body pt-0 pb-5">
                                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6">
                                        <i class="ki-duotone ki-information fs-2tx text-info me-4">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <div class="d-flex flex-stack flex-grow-1">
                                            <div class="fw-semibold">
                                                <div class="fs-6 text-gray-700">{{ $membro['observacoes'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                            @endif

                            <!--begin::Card-->
                            <div class="card pt-4 mb-6 mb-xl-9">
                                <!--begin::Card header-->
                                <div class="card-header border-0">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <h2>Ações</h2>
                                    </div>
                                    <!--end::Card title-->
                                </div>
                                <!--end::Card header-->
                                
                                <!--begin::Card body-->
                                <div class="card-body pt-0 pb-5">
                                    <div class="d-flex flex-wrap gap-5">
                                        <!--begin::Action-->
                                        <a href="{{ route('mesa-diretora.edit', $membro['id']) }}" class="btn btn-primary">
                                            <i class="ki-duotone ki-pencil fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Editar Membro
                                        </a>
                                        <!--end::Action-->
                                        
                                        @if($membro['status'] === 'ativo' && $membro['is_mandato_ativo'])
                                        <!--begin::Action-->
                                        <button class="btn btn-warning" data-kt-member-id="{{ $membro['id'] }}" data-kt-action="finalizar_mandato">
                                            <i class="ki-duotone ki-time fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Finalizar Mandato
                                        </button>
                                        <!--end::Action-->
                                        @endif
                                        
                                        <!--begin::Action-->
                                        <button class="btn btn-danger" data-kt-member-id="{{ $membro['id'] }}" data-kt-action="delete_row">
                                            <i class="ki-duotone ki-trash fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                                <span class="path5"></span>
                                            </i>
                                            Excluir Membro
                                        </button>
                                        <!--end::Action-->
                                    </div>
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Tab Pane-->
                    </div>
                    <!--end::Tab Content-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Layout-->
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
                <form id="kt_modal_delete_member_form" class="form" action="{{ route('mesa-diretora.destroy', $membro['id']) }}" method="POST">
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
                <form id="kt_modal_finalizar_mandato_form" class="form" action="{{ route('mesa-diretora.finalizar', $membro['id']) }}" method="POST">
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

var KTMemberShow = function () {
    
    var handleDeleteMember = function () {
        const deleteButton = document.querySelector('[data-kt-action="delete_row"]');
        
        if (deleteButton) {
            deleteButton.addEventListener('click', function (e) {
                e.preventDefault();
                const modal = document.querySelector('#kt_modal_delete_member');
                $(modal).modal('show');
            });
        }
    }

    var handleFinalizarMandato = function () {
        const finalizarButton = document.querySelector('[data-kt-action="finalizar_mandato"]');
        
        if (finalizarButton) {
            finalizarButton.addEventListener('click', function (e) {
                e.preventDefault();
                const modal = document.querySelector('#kt_modal_finalizar_mandato');
                $(modal).modal('show');
            });
        }
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
            handleDeleteMember();
            handleFinalizarMandato();
            handleModalCancel();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTMemberShow.init();
});
</script>
@endpush