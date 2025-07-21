@extends('components.layouts.app')

@section('title', 'Documentos em Tramitação')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Documentos em Tramitação
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
                    <li class="breadcrumb-item text-muted">Tramitação</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('documentos.modelos.index') }}" class="btn btn-sm fw-bold btn-light-primary">
                    <i class="ki-duotone ki-file fs-2"></i>
                    Ver Modelos
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


            <!--begin::Stats cards-->
            <div class="row g-5 g-xl-10 mb-10">
                <div class="col-xxl-3 col-md-6">
                    <div class="card bg-light-primary">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-3">
                                    <span class="symbol-label bg-primary">
                                        <i class="ki-duotone ki-file-added text-white fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div>
                                    <span class="text-muted fw-semibold d-block fs-7">Rascunhos</span>
                                    <span class="fw-bold text-primary fs-2">{{ $instancias->where('status', 'rascunho')->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-md-6">
                    <div class="card bg-light-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-3">
                                    <span class="symbol-label bg-warning">
                                        <i class="ki-duotone ki-user-edit text-white fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </span>
                                </div>
                                <div>
                                    <span class="text-muted fw-semibold d-block fs-7">Com Parlamentar</span>
                                    <span class="fw-bold text-warning fs-2">{{ $instancias->where('status', 'parlamentar')->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-md-6">
                    <div class="card bg-light-info">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-3">
                                    <span class="symbol-label bg-info">
                                        <i class="ki-duotone ki-verify text-white fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div>
                                    <span class="text-muted fw-semibold d-block fs-7">Em Revisão</span>
                                    <span class="fw-bold text-info fs-2">{{ $instancias->where('status', 'legislativo')->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-md-6">
                    <div class="card bg-light-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-3">
                                    <span class="symbol-label bg-success">
                                        <i class="ki-duotone ki-check-circle text-white fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div>
                                    <span class="text-muted fw-semibold d-block fs-7">Finalizados</span>
                                    <span class="fw-bold text-success fs-2">{{ $instancias->where('status', 'finalizado')->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Stats cards-->

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
                                   placeholder="Buscar documentos..." />
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
                                <div class="px-7 py-5">
                                    <form method="GET">
                                        <div class="mb-10">
                                            <label class="form-label fs-6 fw-semibold">Status:</label>
                                            <select class="form-select form-select-solid fw-bold" name="status">
                                                <option value="">Todos</option>
                                                <option value="rascunho" {{ request('status') == 'rascunho' ? 'selected' : '' }}>Rascunho</option>
                                                <option value="parlamentar" {{ request('status') == 'parlamentar' ? 'selected' : '' }}>Com Parlamentar</option>
                                                <option value="legislativo" {{ request('status') == 'legislativo' ? 'selected' : '' }}>Em Revisão</option>
                                                <option value="finalizado" {{ request('status') == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                                            </select>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <a href="{{ route('documentos.instancias.index') }}" class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6">Limpar</a>
                                            <button type="submit" class="btn btn-primary fw-semibold px-6">Aplicar</button>
                                        </div>
                                    </form>
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
                                    <th class="min-w-200px">Projeto</th>
                                    <th class="min-w-150px">Modelo</th>
                                    <th class="min-w-100px">Status</th>
                                    <th class="min-w-100px">Versão</th>
                                    <th class="min-w-100px">Atualizado em</th>
                                    <th class="text-end min-w-100px">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                @forelse($instancias as $instancia)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px me-5">
                                                    <span class="symbol-label bg-light-info text-info fw-bold">
                                                        <i class="ki-duotone ki-file-text fs-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="{{ route('documentos.instancias.show', $instancia) }}" 
                                                       class="text-gray-900 fw-bold text-hover-primary fs-6">
                                                        {{ $instancia->projeto->titulo ?? 'Projeto #' . $instancia->projeto_id }}
                                                    </a>
                                                    @if($instancia->projeto->ementa)
                                                        <span class="text-muted fw-semibold text-muted d-block fs-7">
                                                            {{ Str::limit($instancia->projeto->ementa, 50) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-primary">{{ $instancia->modelo->nome }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $instancia->status_formatado['classe'] }}">
                                                {{ $instancia->status_formatado['texto'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-gray-900 fw-bold">v{{ $instancia->versao }}</span>
                                        </td>
                                        <td>{{ $instancia->updated_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" 
                                               data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                Ações
                                                <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                            </a>
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" 
                                                 data-kt-menu="true">
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('documentos.instancias.show', $instancia) }}" class="menu-link px-3">Visualizar</a>
                                                </div>
                                                @if($instancia->arquivo_path)
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('documentos.instancias.download', $instancia) }}" class="menu-link px-3">Download</a>
                                                </div>
                                                @endif
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('documentos.instancias.versoes', $instancia) }}" class="menu-link px-3">Versões</a>
                                                </div>
                                                @if($instancia->status !== 'finalizado')
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3 text-danger delete-instancia" 
                                                       data-instancia-id="{{ $instancia->id }}">Excluir</a>
                                                </div>
                                                @endif
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
                                                <div class="text-muted fw-bold fs-6">Nenhum documento em tramitação</div>
                                                <div class="text-muted fs-7">Os documentos aparecerão aqui conforme forem criados</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!--end::Table-->

                    @if($instancias->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-10">
                            <div class="text-muted">
                                Mostrando {{ $instancias->firstItem() }} até {{ $instancias->lastItem() }} de {{ $instancias->total() }} resultados
                            </div>
                            {{ $instancias->links() }}
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
            <div class="modal-header">
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
                    Tem certeza que deseja excluir este documento em tramitação?
                </div>
                <div class="text-danger fs-7">
                    <strong>Atenção:</strong> Esta ação excluirá todas as versões e não pode ser desfeita.
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
    // Configurar exclusão de instâncias
    document.querySelectorAll('.delete-instancia').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const instanciaId = this.dataset.instanciaId;
            
            document.getElementById('form_exclusao').action = `/admin/documentos/instancias/${instanciaId}`;
            
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