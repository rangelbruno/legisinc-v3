@extends('components.layouts.app')

@section('title', 'Tipos de Proposição')

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <!--begin::Page title-->
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                Tipos de Proposição
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Admin</li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Tipos de Proposição</li>
            </ul>
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('admin.tipo-proposicoes.create') }}" class="btn btn-sm fw-bold btn-primary">
                <i class="ki-duotone ki-plus fs-2"></i>
                Novo Tipo
            </a>
        </div>
        <!--end::Actions-->
    </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-success">Sucesso!</h4>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-danger">Erro!</h4>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!--begin::Card-->
        <div class="card">
            <!--begin::Card header-->
            <div class="card-header border-0 pt-6">
                <!--begin::Card title-->
                <div class="card-title">
                    <!--begin::Search-->
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <input type="text" data-kt-tipo-table-filter="search" 
                               class="form-control form-control-solid w-250px ps-12" 
                               placeholder="Buscar tipos..." />
                    </div>
                    <!--end::Search-->
                </div>
                <!--begin::Card title-->
                <!--begin::Card toolbar-->
                <div class="card-toolbar">
                    <!--begin::Toolbar-->
                    <div class="d-flex justify-content-end" data-kt-tipo-table-toolbar="base">
                        <!--begin::Filter-->
                        <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            <i class="ki-duotone ki-filter fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Filtrar
                        </button>
                        <!--begin::Menu 1-->
                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                            <!--begin::Header-->
                            <div class="px-7 py-5">
                                <div class="fs-5 text-gray-900 fw-bold">Opções de Filtro</div>
                            </div>
                            <!--end::Header-->
                            <!--begin::Separator-->
                            <div class="separator border-gray-200"></div>
                            <!--end::Separator-->
                            <!--begin::Content-->
                            <div class="px-7 py-5" data-kt-tipo-table-filter="form">
                                <!--begin::Input group-->
                                <div class="mb-10">
                                    <label class="form-label fs-6 fw-semibold">Status:</label>
                                    <select class="form-select form-select-solid fw-bold" data-kt-select2="true" data-placeholder="Selecione o status" data-allow-clear="true" data-kt-tipo-table-filter="status">
                                        <option></option>
                                        <option value="ativo">Ativo</option>
                                        <option value="inativo">Inativo</option>
                                    </select>
                                </div>
                                <!--end::Input group-->
                                <!--begin::Actions-->
                                <div class="d-flex justify-content-end">
                                    <button type="reset" class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6" data-kt-menu-dismiss="true" data-kt-tipo-table-filter="reset">Limpar</button>
                                    <button type="submit" class="btn btn-primary fw-semibold px-6" data-kt-menu-dismiss="true" data-kt-tipo-table-filter="filter">Aplicar</button>
                                </div>
                                <!--end::Actions-->
                            </div>
                            <!--end::Content-->
                        </div>
                        <!--end::Menu 1-->
                        <!--end::Filter-->

                        <!--begin::Add tipo-->
                        <a href="{{ route('admin.tipo-proposicoes.create') }}" class="btn btn-primary">
                            <i class="ki-duotone ki-plus fs-2"></i>
                            Novo Tipo
                        </a>
                        <!--end::Add tipo-->
                    </div>
                    <!--end::Toolbar-->
                    <!--begin::Group actions-->
                    <div class="d-flex justify-content-end align-items-center d-none" data-kt-tipo-table-toolbar="selected">
                        <div class="fw-bold me-5">
                            <span class="me-2" data-kt-tipo-table-select="selected_count"></span>selecionado(s)
                        </div>
                        <button type="button" class="btn btn-danger" data-kt-tipo-table-select="delete_selected">
                            Excluir Selecionados
                        </button>
                    </div>
                    <!--end::Group actions-->
                </div>
                <!--end::Card toolbar-->
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body py-4">
                <!--begin::Table-->
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_tipo_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-10px pe-2">
                                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_tipo_table .form-check-input" value="1" />
                                </div>
                            </th>
                            <th class="min-w-125px">Tipo</th>
                            <th class="min-w-125px">Código</th>
                            <th class="min-w-125px">Status</th>
                            <th class="min-w-100px">Ordem</th>
                            <th class="min-w-125px">Criado em</th>
                            <th class="text-end min-w-100px">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @forelse($tipos as $tipo)
                        <tr>
                            <td>
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="{{ $tipo->id ?? 'temp' }}" />
                                </div>
                            </td>
                            <td class="d-flex align-items-center">
                                <!--begin::Avatar-->
                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                    <div class="symbol-label">
                                        <i class="{{ $tipo->icone_classe }} text-{{ $tipo->cor }}"></i>
                                    </div>
                                </div>
                                <!--end::Avatar-->
                                <!--begin::User details-->
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 text-hover-primary mb-1 fw-bold">{{ $tipo->nome }}</span>
                                    @if($tipo->descricao)
                                        <span class="text-muted fs-7">{{ Str::limit($tipo->descricao, 50) }}</span>
                                    @endif
                                </div>
                                <!--end::User details-->
                            </td>
                            <td>
                                <span class="badge badge-light-info">{{ $tipo->codigo }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $tipo->status_formatado['classe'] }}">
                                    {{ $tipo->status_formatado['texto'] }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted fw-semibold">{{ $tipo->ordem }}</span>
                            </td>
                            <td>{{ $tipo->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-end">
                                <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    Ações
                                    <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                </a>
                                <!--begin::Menu-->
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="{{ route('admin.tipo-proposicoes.show', $tipo) }}" class="menu-link px-3">
                                            Visualizar
                                        </a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="{{ route('admin.tipo-proposicoes.edit', $tipo) }}" class="menu-link px-3">
                                            Editar
                                        </a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3" 
                                           data-kt-tipo-toggle-status="{{ $tipo->id }}" 
                                           data-status="{{ $tipo->ativo ? 'ativo' : 'inativo' }}">
                                            {{ $tipo->ativo ? 'Desativar' : 'Ativar' }}
                                        </a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3 text-danger" data-kt-tipo-delete="{{ $tipo->id }}">
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
                            <td colspan="7" class="text-center py-10">
                                <div class="d-flex flex-column">
                                    <i class="ki-duotone ki-file-deleted fs-3x text-muted mb-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <span class="text-muted fs-5">Nenhum tipo de proposição encontrado</span>
                                    <a href="{{ route('admin.tipo-proposicoes.create') }}" class="btn btn-primary mt-3">
                                        <i class="ki-duotone ki-plus fs-2"></i>
                                        Criar Primeiro Tipo
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <!--end::Table-->

                <!--begin::Pagination-->
                @if($tipos->hasPages())
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted fs-7">
                        Mostrando {{ $tipos->firstItem() }} até {{ $tipos->lastItem() }} de {{ $tipos->total() }} registros
                    </div>
                    {{ $tipos->links() }}
                </div>
                @endif
                <!--end::Pagination-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
</div>
<!--end::Content-->

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle status functionality
    document.querySelectorAll('[data-kt-tipo-toggle-status]').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            
            const tipoId = this.getAttribute('data-kt-tipo-toggle-status');
            const currentStatus = this.getAttribute('data-status');
            const newStatus = currentStatus === 'ativo' ? 'inativo' : 'ativo';
            const actionText = newStatus === 'ativo' ? 'ativar' : 'desativar';
            
            Swal.fire({
                text: `Tem certeza que deseja ${actionText} este tipo?`,
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: `Sim, ${actionText}!`,
                cancelButtonText: "Cancelar",
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function(result) {
                if (result.value) {
                    fetch(`/admin/tipo-proposicoes/${tipoId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                text: data.message,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            }).then(function() {
                                location.reload();
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            text: "Erro ao alterar status",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    });
                }
            });
        });
    });

    // Delete functionality
    document.querySelectorAll('[data-kt-tipo-delete]').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            
            const tipoId = this.getAttribute('data-kt-tipo-delete');
            
            Swal.fire({
                text: "Tem certeza que deseja excluir este tipo?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Sim, excluir!",
                cancelButtonText: "Cancelar",
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function(result) {
                if (result.value) {
                    fetch(`/admin/tipo-proposicoes/${tipoId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                text: data.message,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                text: data.message || "Erro ao excluir tipo",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            text: "Erro ao excluir tipo",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    });
                }
            });
        });
    });
});
</script>
@endpush
@endsection