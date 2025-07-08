@extends('components.layouts.app')

@section('title', $title ?? 'Comissões por Tipo')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Comissões {{ $tipoFormatado }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('comissoes.index') }}" class="text-muted text-hover-primary">Comissões</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">{{ $tipoFormatado }}</li>
                </ul>
            </div>
            <!--end::Page title-->
            
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('comissoes.index') }}" class="btn btn-sm btn-light-primary">
                    <i class="ki-duotone ki-arrow-left fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Voltar
                </a>
                @can('comissoes.create')
                <a href="{{ route('comissoes.create') }}" class="btn btn-sm btn-primary">
                    <i class="ki-duotone ki-plus fs-2"></i>
                    Nova Comissão
                </a>
                @endcan
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
                    <i class="ki-duotone ki-check-circle fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="ki-duotone ki-cross-circle fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ session('error') }}
                </div>
            @endif

            <!--begin::Statistics Card-->
            <div class="card mb-8">
                <div class="card-body">
                    <div class="row g-0">
                        <div class="col-sm-6 col-xl-3">
                            <div class="border-end px-10 py-6">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <div class="symbol-label bg-light-primary">
                                            <i class="ki-duotone ki-category text-primary fs-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fs-2 fw-bold text-gray-900">{{ $comissoes->count() }}</div>
                                        <div class="fs-7 text-muted">Total de {{ $tipoFormatado }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="border-end border-end-xl-0 px-10 py-6">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <div class="symbol-label bg-light-success">
                                            <i class="ki-duotone ki-check-circle text-success fs-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fs-2 fw-bold text-gray-900">{{ $comissoes->where('status', 'ativa')->count() }}</div>
                                        <div class="fs-7 text-muted">Ativas</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="border-end px-10 py-6">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <div class="symbol-label bg-light-info">
                                            <i class="ki-duotone ki-people text-info fs-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                                <span class="path5"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fs-2 fw-bold text-gray-900">{{ $comissoes->sum('total_membros') }}</div>
                                        <div class="fs-7 text-muted">Total de Membros</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="px-10 py-6">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <div class="symbol-label bg-light-warning">
                                            <i class="ki-duotone ki-crown text-warning fs-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fs-2 fw-bold text-gray-900">{{ $comissoes->whereNotNull('presidente')->count() }}</div>
                                        <div class="fs-7 text-muted">Com Presidentes</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Statistics Card-->

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
                            <input type="text" data-kt-comissoes-table-filter="search" 
                                   class="form-control form-control-solid w-250px ps-13" 
                                   placeholder="Buscar comissões">
                        </div>
                    </div>
                    
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-comissoes-table-toolbar="base">
                            <div class="me-3">
                                <select class="form-select form-select-solid" data-kt-select2="true" 
                                        data-placeholder="Status" id="filter-status">
                                    <option value="">Todos os Status</option>
                                    <option value="ativa">Ativa</option>
                                    <option value="inativa">Inativa</option>
                                    <option value="suspensa">Suspensa</option>
                                    <option value="encerrada">Encerrada</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body pt-0">
                    @if($comissoes->isNotEmpty())
                        <!--begin::Table-->
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_comissoes_table">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-125px">Comissão</th>
                                    <th class="min-w-100px">Tipo</th>
                                    <th class="min-w-80px">Status</th>
                                    <th class="min-w-120px">Presidente</th>
                                    <th class="min-w-80px">Membros</th>
                                    <th class="min-w-100px">Criada em</th>
                                    <th class="text-end min-w-100px">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                @foreach($comissoes as $comissao)
                                <tr data-status="{{ $comissao['status'] ?? 'ativa' }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-50px me-5">
                                                <div class="symbol-label fs-2 fw-bold text-inverse-primary bg-light-primary">
                                                    {{ strtoupper(substr($comissao['nome'], 0, 2)) }}
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <a href="{{ route('comissoes.show', $comissao['id']) }}" 
                                                   class="text-gray-800 text-hover-primary mb-1 fs-6 fw-bold">
                                                    {{ $comissao['nome'] }}
                                                </a>
                                                <span class="text-muted fw-semibold d-block fs-7">
                                                    {{ \Illuminate\Support\Str::limit($comissao['descricao'] ?? '', 60) }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $tipoColor = match($comissao['tipo'] ?? 'permanente') {
                                                'permanente' => 'primary',
                                                'temporaria' => 'info',
                                                'especial' => 'warning',
                                                'cpi' => 'danger',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge badge-{{ $tipoColor }}">
                                            {{ $comissao['tipo_formatado'] ?? 'Permanente' }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $statusBadge = match($comissao['status'] ?? 'ativa') {
                                                'ativa' => 'success',
                                                'inativa' => 'secondary',
                                                'suspensa' => 'warning',
                                                'encerrada' => 'danger',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge badge-light-{{ $statusBadge }}">
                                            {{ ucfirst($comissao['status'] ?? 'ativa') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(isset($comissao['presidente']['nome']))
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-30px me-3">
                                                    <div class="symbol-label fs-5 fw-bold text-success bg-light-success">
                                                        {{ strtoupper(substr($comissao['presidente']['nome'], 0, 2)) }}
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <span class="text-gray-800 fw-bold fs-7">{{ $comissao['presidente']['nome'] }}</span>
                                                    @if(isset($comissao['presidente']['partido']))
                                                        <span class="text-muted fs-8">{{ $comissao['presidente']['partido'] }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Não definido</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-light fs-7">
                                            {{ $comissao['total_membros'] ?? 0 }} membro{{ ($comissao['total_membros'] ?? 0) != 1 ? 's' : '' }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ isset($comissao['data_criacao']) ? \Carbon\Carbon::parse($comissao['data_criacao'])->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="text-end">
                                        <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" 
                                           data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            Ações
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                        </a>
                                        <!--begin::Menu-->
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" 
                                             data-kt-menu="true">
                                            <div class="menu-item px-3">
                                                <a href="{{ route('comissoes.show', $comissao['id']) }}" class="menu-link px-3">
                                                    Visualizar
                                                </a>
                                            </div>
                                            @can('comissoes.edit')
                                            <div class="menu-item px-3">
                                                <a href="{{ route('comissoes.edit', $comissao['id']) }}" class="menu-link px-3">
                                                    Editar
                                                </a>
                                            </div>
                                            @endcan
                                            @can('comissoes.delete')
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3" data-kt-comissoes-table-filter="delete_row">
                                                    Excluir
                                                </a>
                                            </div>
                                            @endcan
                                        </div>
                                        <!--end::Menu-->
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!--end::Table-->
                    @else
                        <!--begin::Empty state-->
                        <div class="text-center py-10">
                            <img src="{{ asset('assets/media/illustrations/sketchy-1/5.png') }}" class="mw-400px" alt="">
                            <div class="pt-10">
                                <h3 class="fs-1 fw-bold text-gray-900 mb-3">Nenhuma comissão {{ strtolower($tipoFormatado) }} encontrada</h3>
                                <p class="text-gray-400 fs-6 fw-semibold mb-8">
                                    Não existem comissões do tipo {{ strtolower($tipoFormatado) }} cadastradas no sistema.
                                </p>
                                @can('comissoes.create')
                                <a href="{{ route('comissoes.create') }}" class="btn btn-primary">
                                    <i class="ki-duotone ki-plus fs-2"></i>
                                    Criar Nova Comissão
                                </a>
                                @endcan
                            </div>
                        </div>
                        <!--end::Empty state-->
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

<!--begin::Javascript-->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.querySelector('[data-kt-comissoes-table-filter="search"]');
    const statusFilter = document.querySelector('#filter-status');
    const table = document.querySelector('#kt_comissoes_table');
    const rows = table ? table.querySelectorAll('tbody tr') : [];

    function filterTable() {
        const searchTerm = searchInput?.value.toLowerCase() || '';
        const statusValue = statusFilter?.value || '';

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const status = row.getAttribute('data-status') || '';
            
            const matchesSearch = text.includes(searchTerm);
            const matchesStatus = !statusValue || status === statusValue;
            
            row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        });
    }

    // Add event listeners
    searchInput?.addEventListener('keyup', filterTable);
    statusFilter?.addEventListener('change', filterTable);
});
</script>
<!--end::Javascript-->

@endsection