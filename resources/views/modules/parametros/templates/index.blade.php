@extends('components.layouts.app')

@section('title', 'Templates - Configurações')

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <i class="ki-duotone ki-document fs-2 me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Templates - Configurações
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('parametros.index') }}" class="text-muted text-hover-primary">Parâmetros</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Templates</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!--begin::Secondary button-->
                <a href="{{ route('parametros.index') }}" class="btn btn-sm btn-flex btn-secondary">
                    <i class="ki-duotone ki-arrow-left fs-3"></i>
                    Voltar
                </a>
                <!--end::Secondary button-->
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Alert-->
            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-check fs-2hx text-success me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h5 class="mb-1">Sucesso</h5>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            <!--end::Alert-->

            <!--begin::Header-->
            <div class="card mb-10">
                <div class="card-body p-9">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-60px me-5">
                            <span class="symbol-label bg-light-primary">
                                <i class="ki-duotone ki-document fs-2x text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="text-gray-900 fs-2 fw-bold mb-1">{{ $modulo->nome }}</h3>
                            <p class="text-gray-700 fs-6 mb-0">{{ $modulo->descricao }}</p>
                        </div>
                        <div class="text-end">
                            <span class="badge badge-light-success fs-7 fw-bold">{{ $cards->count() }} Submódulos</span>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Header-->

            <!--begin::Row-->
            <div class="row g-6 g-xl-9">
                @forelse($cards as $card)
                    <!--begin::Col-->
                    <div class="col-md-6 col-lg-4 col-xl-4">
                        <!--begin::Card-->
                        <div class="card card-flush h-xl-100">
                            <!--begin::Card header-->
                            <div class="card-header pt-5">
                                <!--begin::Card title-->
                                <div class="card-title d-flex flex-column">
                                    <!--begin::Icon-->
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="ki-duotone {{ $card->icon }} fs-2x text-primary me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <h3 class="fs-5 fw-bold text-gray-900 mb-0">{{ $card->nome }}</h3>
                                            <span class="text-gray-500 fs-7">Submódulo de {{ $card->modulo_pai }}</span>
                                        </div>
                                    </div>
                                    <!--end::Icon-->
                                </div>
                                <!--end::Card title-->
                                <!--begin::Card toolbar-->
                                <div class="card-toolbar">
                                    <!--begin::Menu-->
                                    <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        <i class="ki-duotone ki-dots-horizontal fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </button>
                                    <!--begin::Menu 3-->
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3" data-kt-menu="true">
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <a href="{{ route($card->rota) }}" class="menu-link px-3">
                                                <i class="ki-duotone ki-setting-3 fs-6 me-2"></i>
                                                Configurar
                                            </a>
                                        </div>
                                        <!--end::Menu item-->
                                    </div>
                                    <!--end::Menu 3-->
                                    <!--end::Menu-->
                                </div>
                                <!--end::Card toolbar-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body d-flex flex-column">
                                <!--begin::Description-->
                                <div class="flex-grow-1 mb-5">
                                    <p class="text-gray-700 fs-6 mb-0">
                                        {{ $card->descricao ?: 'Configurações específicas para este submódulo' }}
                                    </p>
                                </div>
                                <!--end::Description-->
                                <!--begin::Progress-->
                                <div class="d-flex flex-stack">
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-light-{{ $card->ativo ? 'success' : 'danger' }}">
                                            {{ $card->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route($card->rota) }}" class="btn btn-sm btn-primary">
                                            <i class="ki-duotone ki-setting-3 fs-6 me-1"></i>
                                            Configurar
                                        </a>
                                    </div>
                                </div>
                                <!--end::Progress-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Col-->
                @empty
                    <!--begin::Empty state-->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body d-flex flex-center flex-column py-20">
                                <div class="text-center">
                                    <i class="ki-duotone ki-document fs-4x text-primary mb-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <h3 class="text-gray-900 fs-4 fw-bold mb-2">Nenhum submódulo encontrado</h3>
                                    <p class="text-gray-500 fs-6 mb-0">Não há submódulos configurados para este módulo.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Empty state-->
                @endforelse
            </div>
            <!--end::Row-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
@endsection