@extends('components.layouts.app')

@section('title', 'Modelos de Documentos')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Modelos de Documentos
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Documentos</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Modelos</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('documentos.modelos.create') }}" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-plus fs-2"></i>
                    Novo Modelo (Upload)
                </a>
                <a href="{{ route('documentos.modelos.create-onlyoffice') }}" class="btn btn-sm fw-bold btn-success">
                    <i class="ki-duotone ki-document-edit fs-2"></i>
                    Criar Online
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
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


            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-danger">Erro!</h4>
                        <span>{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" data-kt-docs-table-filter="search" 
                                   class="form-control form-control-solid w-250px ps-13" 
                                   placeholder="Buscar modelos..." />
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-docs-table-toolbar="base">
                            <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" 
                                    data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-filter fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Filtros
                            </button>
                            <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                                <div class="px-7 py-5">
                                    <div class="fs-5 text-gray-900 fw-bold">Opções de Filtro</div>
                                </div>
                                <div class="separator border-gray-200"></div>
                                <div class="px-7 py-5" data-kt-docs-table-filter="form">
                                    <div class="mb-10">
                                        <label class="form-label fs-6 fw-semibold">Status:</label>
                                        <select class="form-select form-select-solid fw-bold" data-kt-select2="true" 
                                                data-placeholder="Selecione" data-allow-clear="true" 
                                                data-kt-docs-table-filter="status">
                                            <option></option>
                                            <option value="ativo">Ativo</option>
                                            <option value="inativo">Inativo</option>
                                        </select>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="reset" class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6" 
                                                data-kt-menu-dismiss="true" data-kt-docs-table-filter="reset">Limpar</button>
                                        <button type="submit" class="btn btn-primary fw-semibold px-6" 
                                                data-kt-menu-dismiss="true" data-kt-docs-table-filter="filter">Aplicar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body py-4">
                    <!--begin::Table-->
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_docs_table">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-200px">Nome</th>
                                    <th class="min-w-150px">Tipo Proposição</th>
                                    <th class="min-w-100px">Versão</th>
                                    <th class="min-w-100px">Status</th>
                                    <th class="min-w-100px">Criado em</th>
                                    <th class="text-end min-w-100px">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                @forelse($modelos as $modelo)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px me-5">
                                                    <span class="symbol-label bg-light-primary text-primary fw-bold">
                                                        <i class="ki-duotone ki-document fs-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="{{ route('documentos.modelos.show', $modelo) }}" 
                                                       class="text-gray-900 fw-bold text-hover-primary fs-6">{{ $modelo->nome }}</a>
                                                    @if($modelo->descricao)
                                                        <span class="text-muted fw-semibold text-muted d-block fs-7">{{ Str::limit($modelo->descricao, 50) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($modelo->tipoProposicao)
                                                <span class="badge badge-primary">{{ $modelo->tipoProposicao->nome }}</span>
                                            @else
                                                <span class="text-muted">Geral</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-gray-900 fw-bold">{{ $modelo->versao }}</span>
                                        </td>
                                        <td>
                                            @if($modelo->ativo)
                                                <span class="badge badge-success">Ativo</span>
                                            @else
                                                <span class="badge badge-secondary">Inativo</span>
                                            @endif
                                        </td>
                                        <td>{{ $modelo->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" 
                                               data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                Ações
                                                <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                            </a>
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" 
                                                 data-kt-menu="true">
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('documentos.modelos.show', $modelo) }}" class="menu-link px-3">Visualizar</a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('documentos.modelos.download', $modelo) }}" class="menu-link px-3">Download</a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('documentos.modelos.edit', $modelo) }}" class="menu-link px-3">Editar</a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3 text-danger delete-modelo" 
                                                       data-modelo-id="{{ $modelo->id }}" 
                                                       data-modelo-nome="{{ $modelo->nome }}">Excluir</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-10">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="ki-duotone ki-file-deleted fs-3x text-muted mb-3">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <div class="text-muted fw-bold fs-6">Nenhum modelo encontrado</div>
                                                <div class="text-muted fs-7">Clique em "Novo Modelo" para começar</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!--end::Table-->

                    @if($modelos->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-10">
                            <div class="text-muted">
                                Mostrando {{ $modelos->firstItem() }} até {{ $modelos->lastItem() }} de {{ $modelos->total() }} resultados
                            </div>
                            {{ $modelos->links() }}
                        </div>
                    @endif
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="modal_confirmar_exclusao" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="modal_confirmar_exclusao_header">
                <h2 class="fw-bold">Confirmar Exclusão</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <div class="fw-semibold fs-6 text-gray-600 mb-5">
                    Tem certeza que deseja excluir o modelo "<span id="nome_modelo_exclusao"></span>"?
                </div>
                <div class="text-danger fs-7">
                    <strong>Atenção:</strong> Esta ação não pode ser desfeita.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <form id="form_exclusao" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar exclusão de modelos
    document.querySelectorAll('.delete-modelo').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const modeloId = this.dataset.modeloId;
            const modeloNome = this.dataset.modeloNome;
            
            document.getElementById('nome_modelo_exclusao').textContent = modeloNome;
            document.getElementById('form_exclusao').action = `/admin/documentos/modelos/${modeloId}`;
            
            const modal = new bootstrap.Modal(document.getElementById('modal_confirmar_exclusao'));
            modal.show();
        });
    });

    // Filtro de busca simples
    const searchInput = document.querySelector('[data-kt-docs-table-filter="search"]');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#kt_docs_table tbody tr');
            
            tableRows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush