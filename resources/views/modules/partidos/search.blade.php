@extends('components.layouts.app')

@section('title', $title ?? 'Buscar Partidos')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Buscar Partidos
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('partidos.index') }}" class="text-muted text-hover-primary">Partidos</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Buscar</li>
                </ul>
            </div>
            <!--end::Page title-->
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Search Summary-->
            <div class="card mb-5">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center flex-grow-1">
                            <i class="ki-duotone ki-magnifier fs-1 text-gray-400 me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div>
                                <h4 class="mb-1">Resultados para: "{{ $termo }}"</h4>
                                <span class="text-muted">{{ $total }} partido(s) encontrado(s)</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <a href="{{ route('partidos.index') }}" class="btn btn-light me-3">
                                <i class="ki-duotone ki-arrow-left fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Voltar à Lista
                            </a>
                            <a href="{{ route('partidos.create') }}" class="btn btn-primary">
                                <i class="ki-duotone ki-plus fs-2"></i>
                                Novo Partido
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Search Summary-->

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
                            <form method="GET" action="{{ route('partidos.search') }}" class="d-flex">
                                <input type="text" name="q" value="{{ $termo }}" 
                                       class="form-control form-control-solid w-250px ps-13" 
                                       placeholder="Buscar partidos..." />
                                <button type="submit" class="btn btn-primary ms-2">Buscar</button>
                            </form>
                        </div>
                        <!--end::Search-->
                    </div>
                    <!--begin::Card title-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body py-4">
                    @if($partidos->count() > 0)
                        <!--begin::Table-->
                        <div class="table-responsive">
                            <table class="table table-rounded table-striped border gy-7 gs-7">
                                <thead>
                                    <tr class="fw-semibold fs-6 text-gray-800 border-bottom-2 border-gray-200">
                                        <th class="min-w-125px">Sigla</th>
                                        <th class="min-w-175px">Nome</th>
                                        <th class="min-w-100px">Número</th>
                                        <th class="min-w-125px">Presidente</th>
                                        <th class="min-w-100px">Status</th>
                                        <th class="min-w-100px">Parlamentares</th>
                                        <th class="min-w-100px text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($partidos as $partido)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <div class="symbol-label fs-3 bg-light-primary text-primary">
                                                        {{ substr($partido->sigla, 0, 2) }}
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <span class="text-gray-800 fw-bold">{{ $partido->sigla }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-gray-600">{{ $partido->nome }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-info fw-bold">{{ $partido->numero }}</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-600">{{ $partido->presidente ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @if($partido->status === 'ativo')
                                                <span class="badge badge-light-success fw-bold">Ativo</span>
                                            @else
                                                <span class="badge badge-light-danger fw-bold">Inativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-light-primary fw-bold">{{ $partido->total_parlamentares }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                Ações
                                                <i class="ki-duotone ki-down fs-5 m-0"></i>
                                            </a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('partidos.show', $partido->id) }}" class="menu-link px-3">
                                                        Visualizar
                                                    </a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('partidos.edit', $partido->id) }}" class="menu-link px-3">
                                                        Editar
                                                    </a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" 
                                                       onclick="event.preventDefault(); if(confirm('Tem certeza que deseja deletar este partido?')) { document.getElementById('delete-form-{{ $partido->id }}').submit(); }">
                                                        Deletar
                                                    </a>
                                                    <form id="delete-form-{{ $partido->id }}" action="{{ route('partidos.destroy', $partido->id) }}" method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!--end::Table-->
                        
                        @if($partidos->hasPages())
                            <!--begin::Pagination-->
                            <div class="d-flex flex-stack flex-wrap pt-10">
                                <div class="fs-6 fw-semibold text-gray-700">
                                    Mostrando {{ $partidos->firstItem() }} a {{ $partidos->lastItem() }} 
                                    de {{ $partidos->total() }} resultados
                                </div>
                                {{ $partidos->appends(['q' => $termo])->links() }}
                            </div>
                            <!--end::Pagination-->
                        @endif
                    @else
                        <!--begin::Empty State-->
                        <div class="text-center py-20">
                            <div class="d-flex flex-column align-items-center">
                                <i class="ki-duotone ki-search-list fs-3x text-gray-400 mb-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <h3 class="text-gray-800 mb-3">Nenhum resultado encontrado</h3>
                                <span class="text-gray-600 mb-5">Não foi possível encontrar partidos com o termo "{{ $termo }}"</span>
                                <div class="d-flex gap-3">
                                    <a href="{{ route('partidos.index') }}" class="btn btn-light">
                                        Ver todos os partidos
                                    </a>
                                    <a href="{{ route('partidos.create') }}" class="btn btn-primary">
                                        Criar novo partido
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!--end::Empty State-->
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
@endsection