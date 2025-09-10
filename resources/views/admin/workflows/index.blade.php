@extends('components.layouts.app')

@section('title', 'Sistema de Workflows')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Sistema de Workflows
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Administração</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Workflows</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.workflows.designer.new') }}" class="btn btn-sm fw-bold btn-light-primary">
                        <i class="ki-duotone ki-design-1 fs-2"></i>
                        Designer Visual
                    </a>
                    <a href="{{ route('admin.workflows.create') }}" class="btn btn-sm fw-bold btn-primary">
                        <i class="ki-duotone ki-plus fs-2"></i>
                        Novo Workflow
                    </a>
                @endif
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
        
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-check-circle fs-2hx text-success me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-gray-900">Sucesso!</h4>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-gray-900">Erro!</h4>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!--begin::Statistics cards-->
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                <div class="col-xl-3">
                    <div class="card card-xl-stretch">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-50px me-5">
                                    <span class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-route fs-2x text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="text-gray-900 fw-bold fs-6">Total de Workflows</div>
                                    <div class="text-muted fw-semibold fs-7">Workflows cadastrados</div>
                                    <div class="fw-bold fs-2 text-gray-800">{{ $stats['total'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3">
                    <div class="card card-xl-stretch">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-50px me-5">
                                    <span class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-check-circle fs-2x text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="text-gray-900 fw-bold fs-6">Workflows Ativos</div>
                                    <div class="text-muted fw-semibold fs-7">Em uso no sistema</div>
                                    <div class="fw-bold fs-2 text-gray-800">{{ $stats['ativos'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3">
                    <div class="card card-xl-stretch">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-50px me-5">
                                    <span class="symbol-label bg-light-warning">
                                        <i class="ki-duotone ki-star fs-2x text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="text-gray-900 fw-bold fs-6">Workflows Padrão</div>
                                    <div class="text-muted fw-semibold fs-7">Utilizados por padrão</div>
                                    <div class="fw-bold fs-2 text-gray-800">{{ $stats['padroes'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3">
                    <div class="card card-xl-stretch">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-50px me-5">
                                    <span class="symbol-label bg-light-info">
                                        <i class="ki-duotone ki-folder fs-2x text-info">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="text-gray-900 fw-bold fs-6">Tipos de Documento</div>
                                    <div class="text-muted fw-semibold fs-7">Suportados pelo sistema</div>
                                    <div class="fw-bold fs-2 text-gray-800">{{ count($stats['tipos_documento']) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Statistics cards-->

            <!--begin::Card toolbar and filters-->
            <div class="card card-flush">
                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-1 position-absolute ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" data-kt-workflow-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Buscar workflows..." value="{{ request('search') }}" />
                        </div>
                    </div>
                    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                        <form method="GET" class="d-flex gap-3">
                            <select name="tipo_documento" class="form-select form-select-solid w-150px" data-control="select2" data-placeholder="Tipo de Documento">
                                <option value="">Todos os tipos</option>
                                @foreach($stats['tipos_documento'] as $tipo)
                                    <option value="{{ $tipo }}" {{ request('tipo_documento') === $tipo ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $tipo)) }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <select name="ativo" class="form-select form-select-solid w-100px" data-control="select2" data-placeholder="Status">
                                <option value="">Todos</option>
                                <option value="1" {{ request('ativo') === '1' ? 'selected' : '' }}>Ativos</option>
                                <option value="0" {{ request('ativo') === '0' ? 'selected' : '' }}>Inativos</option>
                            </select>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="ki-duotone ki-magnifier fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Filtrar
                            </button>
                            
                            @if(request()->hasAny(['tipo_documento', 'ativo', 'search']))
                                <a href="{{ route('admin.workflows.index') }}" class="btn btn-light-primary">
                                    <i class="ki-duotone ki-cross fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Limpar
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                <!--begin::Card body-->
                <div class="card-body pt-0">
                    @if($workflows->count() > 0)
                        <!--begin::Table-->
                        <div class="table-responsive">
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="kt_workflow_table">
                                <thead>
                                    <tr class="fw-bold text-muted">
                                        <th class="min-w-200px">Nome</th>
                                        <th class="min-w-100px">Tipo</th>
                                        <th class="min-w-100px">Etapas</th>
                                        <th class="min-w-80px text-center">Status</th>
                                        <th class="min-w-80px text-center">Padrão</th>
                                        <th class="min-w-80px text-center">Ordem</th>
                                        <th class="min-w-120px">Atualizado</th>
                                        <th class="min-w-200px text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($workflows as $workflow)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-45px me-5">
                                                        <span class="symbol-label bg-light-primary">
                                                            <i class="ki-duotone ki-route fs-2x text-primary">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                        </span>
                                                    </div>
                                                    <div class="d-flex justify-content-start flex-column">
                                                        <a href="{{ route('admin.workflows.show', $workflow) }}" class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6">
                                                            {{ $workflow->nome }}
                                                        </a>
                                                        @if($workflow->descricao)
                                                            <span class="text-muted fw-semibold text-muted d-block fs-7">
                                                                {{ Str::limit($workflow->descricao, 60) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <span class="badge badge-light-info fw-bold">
                                                    {{ ucfirst(str_replace('_', ' ', $workflow->tipo_documento)) }}
                                                </span>
                                            </td>
                                            
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="badge badge-light-primary mb-1">{{ $workflow->etapas->count() }} etapas</span>
                                                    <span class="badge badge-light-secondary">{{ $workflow->transicoes->count() }} transições</span>
                                                </div>
                                            </td>
                                            
                                            <td class="text-center">
                                                @if($workflow->ativo)
                                                    <span class="badge badge-light-success">
                                                        <i class="ki-duotone ki-check fs-7"></i> Ativo
                                                    </span>
                                                @else
                                                    <span class="badge badge-light-danger">
                                                        <i class="ki-duotone ki-cross fs-7"></i> Inativo
                                                    </span>
                                                @endif
                                            </td>
                                            
                                            <td class="text-center">
                                                @if($workflow->is_default)
                                                    <i class="ki-duotone ki-star fs-1 text-warning">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            
                                            <td class="text-center">
                                                <span class="fw-bold fs-7">{{ $workflow->ordem ?? '-' }}</span>
                                            </td>
                                            
                                            <td>
                                                <span class="text-muted fw-semibold fs-7">
                                                    {{ $workflow->updated_at->format('d/m/Y H:i') }}
                                                </span>
                                            </td>
                                            
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end flex-shrink-0 gap-1">
                                                    <a href="{{ route('admin.workflows.show', $workflow) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="Visualizar">
                                                        <i class="ki-duotone ki-eye fs-3">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                    </a>
                                                    
                                                    @if(auth()->user()->isAdmin())
                                                        <a href="{{ route('admin.workflows.edit', $workflow) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="Editar">
                                                            <i class="ki-duotone ki-pencil fs-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                        </a>
                                                        
                                                        <a href="{{ route('admin.workflows.designer.edit', $workflow) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="Designer Visual">
                                                            <i class="ki-duotone ki-design-1 fs-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                        </a>
                                                    @endif
                                                    
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="ki-duotone ki-dots-vertical fs-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            @if(auth()->user()->isAdmin())
                                                                <li>
                                                                    <form method="POST" action="{{ route('admin.workflows.toggle', $workflow) }}"
                                                                          onsubmit="return confirm('{{ $workflow->ativo ? 'Desativar' : 'Ativar' }} este workflow?')">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="submit" class="dropdown-item">
                                                                            <i class="ki-duotone ki-switch {{ $workflow->ativo ? 'text-warning' : 'text-success' }} fs-2 me-2">
                                                                                <span class="path1"></span>
                                                                                <span class="path2"></span>
                                                                            </i>
                                                                            {{ $workflow->ativo ? 'Desativar' : 'Ativar' }}
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                
                                                                @unless($workflow->is_default)
                                                                    <li>
                                                                        <form method="POST" action="{{ route('admin.workflows.set-default', $workflow) }}"
                                                                              onsubmit="return confirm('Definir como workflow padrão para {{ $workflow->tipo_documento }}?')">
                                                                            @csrf
                                                                            @method('PATCH')
                                                                            <button type="submit" class="dropdown-item">
                                                                                <i class="ki-duotone ki-star text-warning fs-2 me-2">
                                                                                    <span class="path1"></span>
                                                                                    <span class="path2"></span>
                                                                                </i>
                                                                                Definir como Padrão
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                @endunless
                                                                
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <a class="dropdown-item" href="#" onclick="duplicateWorkflow({{ $workflow->id }}, '{{ $workflow->nome }}')">
                                                                        <i class="ki-duotone ki-copy text-info fs-2 me-2">
                                                                            <span class="path1"></span>
                                                                            <span class="path2"></span>
                                                                        </i>
                                                                        Duplicar
                                                                    </a>
                                                                </li>
                                                                
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <form method="POST" action="{{ route('admin.workflows.destroy', $workflow) }}"
                                                                          onsubmit="return confirm('Tem certeza que deseja remover este workflow? Esta ação não pode ser desfeita.')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="dropdown-item text-danger">
                                                                            <i class="ki-duotone ki-trash text-danger fs-2 me-2">
                                                                                <span class="path1"></span>
                                                                                <span class="path2"></span>
                                                                                <span class="path3"></span>
                                                                                <span class="path4"></span>
                                                                                <span class="path5"></span>
                                                                            </i>
                                                                            Remover
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!--end::Table-->
                        
                        <!--begin::Pagination-->
                        @if($workflows->hasPages())
                            <div class="d-flex flex-stack flex-wrap pt-10">
                                <div class="fs-6 fw-semibold text-gray-700">
                                    Exibindo {{ $workflows->firstItem() }} a {{ $workflows->lastItem() }} 
                                    de {{ $workflows->total() }} resultados
                                </div>
                                <ul class="pagination">
                                    {{ $workflows->appends(request()->query())->links() }}
                                </ul>
                            </div>
                        @endif
                        <!--end::Pagination-->
                    @else
                        <!--begin::Card empty state-->
                        <div class="card-px text-center py-20">
                            <div class="text-center mb-10 px-4">
                                <i class="ki-duotone ki-route fs-4x opacity-50 mb-5 text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <h2 class="fs-2 fw-bold mb-2">Nenhum Workflow Encontrado</h2>
                                <p class="text-gray-600 fs-6 fw-semibold mb-8">
                                    @if(request()->hasAny(['tipo_documento', 'ativo', 'search']))
                                        Nenhum workflow corresponde aos filtros aplicados.<br>
                                        Tente ajustar os critérios de busca.
                                    @else
                                        Comece criando seu primeiro workflow modular<br>
                                        para automatizar os processos de tramitação.
                                    @endif
                                </p>
                                
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.workflows.create') }}" class="btn btn-primary">
                                        <i class="ki-duotone ki-plus fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        Criar Primeiro Workflow
                                    </a>
                                @endif
                            </div>
                        </div>
                        <!--end::Card empty state-->
                    @endif
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card toolbar and filters-->
            
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<!-- Modal para duplicar workflow -->
<div class="modal fade" id="duplicateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="duplicateForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Duplicar Workflow</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome do Novo Workflow</label>
                        <input type="text" name="nome" class="form-control" required>
                        <small class="form-text text-muted">
                            O novo workflow será criado como inativo e não padrão.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Duplicar Workflow</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
"use strict";

// Duplicate workflow function
function duplicateWorkflow(workflowId, currentName) {
    const modal = new bootstrap.Modal(document.getElementById('duplicateModal'));
    const form = document.getElementById('duplicateForm');
    const nameInput = form.querySelector('input[name="nome"]');
    
    // Configure form action
    form.action = `/admin/workflows/${workflowId}/duplicate`;
    
    // Suggest name based on current
    nameInput.value = `${currentName} (Cópia)`;
    nameInput.select();
    
    modal.show();
}

// Initialize DataTable-like behavior for search
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('[data-kt-workflow-table-filter="search"]');
    if (searchInput) {
        let timeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = '{{ route('admin.workflows.index') }}';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'search';
                input.value = this.value;
                form.appendChild(input);
                
                // Preserve other filters
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.forEach((value, key) => {
                    if (key !== 'search' && value) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = key;
                        hiddenInput.value = value;
                        form.appendChild(hiddenInput);
                    }
                });
                
                document.body.appendChild(form);
                form.submit();
            }, 500);
        });
    }
});
</script>
@endpush