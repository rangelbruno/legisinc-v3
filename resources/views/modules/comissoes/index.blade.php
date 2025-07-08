@extends('components.layouts.app')

@section('title', $title ?? 'Comissões')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Comissões
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Comissões</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                @can('comissoes.create')
                <a href="{{ route('comissoes.create') }}" class="btn btn-sm fw-bold btn-primary">
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

            @if(isset($error))
                <div class="alert alert-danger">
                    <i class="ki-duotone ki-cross-circle fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ $error }}
                </div>
            @endif
            
            <!--begin::Row-->
            <div class="row gy-5 g-xl-10">
                <!--begin::Col-->
                <div class="col-xl-4">
                    <!--begin::Mixed Widget 14-->
                    <div class="card card-xxl-stretch mb-5 mb-xl-8">
                        <!--begin::Beader-->
                        <div class="card-header border-0 py-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Estatísticas</span>
                                <span class="text-muted fw-semibold fs-7">Resumo das comissões</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body d-flex flex-column">
                            <div class="row">
                                <div class="col-6">
                                    <div class="d-flex align-items-center mb-5">
                                        <div class="symbol symbol-30px me-5">
                                            <span class="symbol-label">
                                                <i class="ki-duotone ki-category fs-1 text-primary">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                    <span class="path4"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold fs-6">{{ $estatisticas['total'] ?? 0 }}</span>
                                            <span class="fw-semibold fs-7 text-muted">Total</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center mb-5">
                                        <div class="symbol symbol-30px me-5">
                                            <span class="symbol-label">
                                                <i class="ki-duotone ki-check-circle fs-1 text-success">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold fs-6">{{ $estatisticas['ativas'] ?? 0 }}</span>
                                            <span class="fw-semibold fs-7 text-muted">Ativas</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center mb-5">
                                        <div class="symbol symbol-30px me-5">
                                            <span class="symbol-label">
                                                <i class="ki-duotone ki-time fs-1 text-warning">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold fs-6">{{ $estatisticas['permanentes'] ?? 0 }}</span>
                                            <span class="fw-semibold fs-7 text-muted">Permanentes</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center mb-5">
                                        <div class="symbol symbol-30px me-5">
                                            <span class="symbol-label">
                                                <i class="ki-duotone ki-search-list fs-1 text-info">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold fs-6">{{ $estatisticas['cpi'] ?? 0 }}</span>
                                            <span class="fw-semibold fs-7 text-muted">CPIs</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Mixed Widget 14-->
                </div>
                <!--end::Col-->
                
                <!--begin::Col-->
                <div class="col-xl-8">
                    <!--begin::Tables Widget 16-->
                    <div class="card card-xxl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Lista de Comissões</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">{{ $comissoes->count() }} comissões encontradas</span>
                            </h3>
                            <div class="card-toolbar">
                                <!--begin::Menu-->
                                <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    <i class="ki-duotone ki-category fs-6">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </button>
                                <!--begin::Menu 3-->
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3" data-kt-menu="true">
                                    <!--begin::Heading-->
                                    <div class="menu-item px-3">
                                        <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Filtros</div>
                                    </div>
                                    <!--end::Heading-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="{{ route('comissoes.index') }}" class="menu-link px-3">Todas</a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="{{ route('comissoes.index', ['tipo' => 'permanente']) }}" class="menu-link px-3">Permanentes</a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="{{ route('comissoes.index', ['tipo' => 'temporaria']) }}" class="menu-link px-3">Temporárias</a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="{{ route('comissoes.index', ['tipo' => 'cpi']) }}" class="menu-link px-3">CPIs</a>
                                    </div>
                                    <!--end::Menu item-->
                                </div>
                                <!--end::Menu 3-->
                                <!--end::Menu-->
                            </div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            @if($comissoes->isEmpty())
                                <div class="text-center py-10">
                                    <i class="ki-duotone ki-category fs-5x text-muted mb-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                    <div class="fs-1 fw-bold text-dark mb-4">Nenhuma comissão encontrada</div>
                                    <div class="fs-6">Não há comissões cadastradas no momento.</div>
                                    @can('comissoes.create')
                                    <div class="mt-5">
                                        <a href="{{ route('comissoes.create') }}" class="btn btn-primary">
                                            <i class="ki-duotone ki-plus fs-2"></i>
                                            Nova Comissão
                                        </a>
                                    </div>
                                    @endcan
                                </div>
                            @else
                                <!--begin::Table container-->
                                <div class="table-responsive">
                                    <!--begin::Table-->
                                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                        <!--begin::Table head-->
                                        <thead>
                                            <tr class="fw-bold text-muted">
                                                <th class="min-w-150px">Nome</th>
                                                <th class="min-w-100px">Tipo</th>
                                                <th class="min-w-100px">Status</th>
                                                <th class="min-w-150px">Presidente</th>
                                                <th class="min-w-100px">Membros</th>
                                                <th class="min-w-100px text-end">Ações</th>
                                            </tr>
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody>
                                            @foreach($comissoes as $comissao)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="symbol symbol-45px me-5">
                                                            <div class="symbol-label fs-3 bg-light-primary text-primary">
                                                                {{ strtoupper(substr($comissao['nome'], 0, 2)) }}
                                                            </div>
                                                        </div>
                                                        <div class="d-flex justify-content-start flex-column">
                                                            <a href="{{ route('comissoes.show', $comissao['id']) }}" class="text-dark fw-bold text-hover-primary fs-6">{{ $comissao['nome'] }}</a>
                                                            <span class="text-muted fw-semibold text-muted d-block fs-7">{{ Str::limit($comissao['descricao'], 60) }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $tipoBadge = match($comissao['tipo']) {
                                                            'permanente' => 'primary',
                                                            'temporaria' => 'warning',
                                                            'especial' => 'info',
                                                            'cpi' => 'danger',
                                                            'mista' => 'secondary',
                                                            default => 'light'
                                                        };
                                                    @endphp
                                                    <span class="badge badge-light-{{ $tipoBadge }} fs-7 fw-semibold">{{ $comissao['tipo_formatado'] }}</span>
                                                </td>
                                                <td>
                                                    @if($comissao['status'] === 'ativa')
                                                        <span class="badge badge-light-success fs-7 fw-semibold">Ativa</span>
                                                    @elseif($comissao['status'] === 'inativa')
                                                        <span class="badge badge-light-secondary fs-7 fw-semibold">Inativa</span>
                                                    @elseif($comissao['status'] === 'suspensa')
                                                        <span class="badge badge-light-warning fs-7 fw-semibold">Suspensa</span>
                                                    @else
                                                        <span class="badge badge-light-danger fs-7 fw-semibold">Encerrada</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if($comissao['presidente'])
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol-25px me-3">
                                                                <div class="symbol-label fs-8 fw-bold bg-light-success text-success">
                                                                    {{ strtoupper(substr($comissao['presidente']['nome'], 0, 2)) }}
                                                                </div>
                                                            </div>
                                                            <div class="d-flex flex-column">
                                                                <span class="fw-bold fs-7">{{ $comissao['presidente']['nome'] }}</span>
                                                                <span class="text-muted fs-8">{{ $comissao['presidente']['partido'] }}</span>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted fs-7">Não definido</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge badge-light fs-7 fw-semibold">{{ $comissao['total_membros'] }} membros</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-end flex-shrink-0">
                                                        <a href="{{ route('comissoes.show', $comissao['id']) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                            <i class="ki-duotone ki-switch fs-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                        </a>
                                                        @can('comissoes.edit')
                                                        <a href="{{ route('comissoes.edit', $comissao['id']) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                            <i class="ki-duotone ki-pencil fs-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                        </a>
                                                        @endcan
                                                        @can('comissoes.delete')
                                                        <a href="#" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" data-kt-comissoes-table-filter="delete_row">
                                                            <i class="ki-duotone ki-trash fs-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                                <span class="path4"></span>
                                                                <span class="path5"></span>
                                                            </i>
                                                        </a>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <!--end::Table body-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                                <!--end::Table container-->
                            @endif
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Tables Widget 16-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection