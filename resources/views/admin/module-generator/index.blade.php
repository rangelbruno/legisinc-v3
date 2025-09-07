@extends('components.layouts.app')

@section('title', 'Gerador de Módulos - Sistema Parlamentar')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Gerador de Módulos
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Administração</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Gerador de Módulos</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('admin.module-generator.create') }}" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-plus fs-2"></i>Novo Módulo
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
                            <input type="text" data-kt-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Buscar módulo">
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-filter="filter">
                            <select class="form-select form-select-solid w-150px" data-kt-filter="status">
                                <option value="">Todos os Status</option>
                                <option value="draft">Rascunho</option>
                                <option value="generated">Gerado</option>
                                <option value="error">Com Erro</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!--end::Card header-->
                
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_modules">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_modules .form-check-input" value="1">
                                    </div>
                                </th>
                                <th class="min-w-125px">Módulo</th>
                                <th class="min-w-125px">Status</th>
                                <th class="min-w-125px">Tabela</th>
                                <th class="min-w-125px">Criado por</th>
                                <th class="min-w-125px">Data</th>
                                <th class="text-end min-w-100px">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @forelse($modules as $module)
                            <tr>
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" value="{{ $module->id }}">
                                    </div>
                                </td>
                                <td class="d-flex align-items-center">
                                    <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                        <div class="symbol-label">
                                            <i class="ki-duotone {{ $module->icon }} fs-2 text-{{ $module->color }}">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <a href="{{ route('admin.module-generator.show', $module) }}" class="text-gray-800 text-hover-primary mb-1">
                                            {{ $module->name }}
                                        </a>
                                        <span class="text-muted">{{ Str::limit($module->description, 50) }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($module->status === 'draft')
                                        <span class="badge badge-light-warning">Rascunho</span>
                                    @elseif($module->status === 'generated')
                                        <span class="badge badge-light-success">Gerado</span>
                                    @elseif($module->status === 'error')
                                        <span class="badge badge-light-danger">Erro</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-gray-600">{{ $module->table_name }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-circle symbol-25px overflow-hidden me-3">
                                            <div class="symbol-label fs-8 fw-semibold bg-primary text-inverse-primary">
                                                {{ substr($module->creator->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <span class="text-gray-600">{{ $module->creator->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    {{ $module->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        Ações
                                        <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                    </a>
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                        <div class="menu-item px-3">
                                            <a href="{{ route('admin.module-generator.show', $module) }}" class="menu-link px-3">
                                                Visualizar
                                            </a>
                                        </div>
                                        
                                        @if(!$module->isGenerated())
                                            <div class="menu-item px-3">
                                                <a href="{{ route('admin.module-generator.edit', $module) }}" class="menu-link px-3">
                                                    Editar
                                                </a>
                                            </div>
                                            <div class="menu-item px-3">
                                                <form action="{{ route('admin.module-generator.generate', $module) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="menu-link px-3 border-0 bg-transparent w-100 text-start" 
                                                            onclick="return confirm('Deseja gerar este módulo? Esta ação não pode ser desfeita.')">
                                                        Gerar Código
                                                    </button>
                                                </form>
                                            </div>
                                            <div class="separator my-2"></div>
                                            <div class="menu-item px-3">
                                                <form action="{{ route('admin.module-generator.destroy', $module) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="menu-link px-3 border-0 bg-transparent w-100 text-start text-danger" 
                                                            onclick="return confirm('Deseja excluir este módulo?')">
                                                        Excluir
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="menu-item px-3">
                                                <a href="{{ route('admin.module-generator.preview', $module) }}" class="menu-link px-3">
                                                    Ver Código
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-10">
                                    <div class="text-center">
                                        <i class="ki-duotone ki-folder fs-4x text-muted mb-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <p class="text-muted fs-5">Nenhum módulo encontrado</p>
                                        <a href="{{ route('admin.module-generator.create') }}" class="btn btn-primary">
                                            Criar Primeiro Módulo
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <!--end::Table-->
                    
                    @if($modules->hasPages())
                        <div class="d-flex flex-stack flex-wrap pt-10">
                            <div class="fs-6 fw-semibold text-gray-700">
                                Mostrando {{ $modules->firstItem() }} até {{ $modules->lastItem() }} de {{ $modules->total() }} módulos
                            </div>
                            <ul class="pagination">
                                {{ $modules->links() }}
                            </ul>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtro de busca
    const searchInput = document.querySelector('[data-kt-filter="search"]');
    const statusFilter = document.querySelector('[data-kt-filter="status"]');
    const table = document.getElementById('kt_table_modules');
    const tbody = table.querySelector('tbody');
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();
        const rows = tbody.querySelectorAll('tr');
        
        rows.forEach(row => {
            if (row.cells.length === 1) return; // Skip empty row
            
            const moduleName = row.cells[1].textContent.toLowerCase();
            const moduleStatus = row.querySelector('.badge')?.textContent.toLowerCase() || '';
            
            const matchesSearch = moduleName.includes(searchTerm);
            const matchesStatus = !statusValue || moduleStatus.includes(statusValue);
            
            row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        });
    }
    
    searchInput.addEventListener('keyup', filterTable);
    statusFilter.addEventListener('change', filterTable);
});
</script>
@endsection