@extends('components.layouts.app')

@section('title', 'Testes de Segurança - Sistema Parlamentar')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Testes de Segurança
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('tests.index') }}" class="text-muted text-hover-primary">Testes</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Segurança</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('tests.index') }}" class="btn btn-sm btn-light">
                    <i class="ki-duotone ki-arrows-left fs-4 me-1"></i>
                    Voltar aos Testes
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Coming Soon Card-->
            <div class="row g-5 g-xl-8">
                <div class="col-xl-12">
                    <div class="card card-xl-stretch">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center py-20">
                            <div class="symbol symbol-100px mb-10">
                                <span class="symbol-label bg-light-dark text-dark">
                                    <i class="ki-duotone ki-shield-tick fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                            </div>
                            <h1 class="fw-bold text-dark fs-3 mb-5">Testes de Segurança</h1>
                            <div class="text-center">
                                <p class="text-gray-600 fs-6 mb-8">
                                    Esta seção está em desenvolvimento e incluirá testes para:
                                </p>
                                <div class="row g-5 mb-8">
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="ki-duotone ki-check-circle text-success fs-3 me-3"></i>
                                            <span class="fw-semibold">Validação de Entrada</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="ki-duotone ki-check-circle text-success fs-3 me-3"></i>
                                            <span class="fw-semibold">Autenticação</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="ki-duotone ki-check-circle text-success fs-3 me-3"></i>
                                            <span class="fw-semibold">Scan de Vulnerabilidades</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-info d-flex align-items-center p-5 mb-10">
                                    <i class="ki-duotone ki-information-5 fs-2hx text-info me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <h4 class="mb-1 text-info fw-bold">Em Desenvolvimento</h4>
                                        <span class="fs-7">Esta funcionalidade estará disponível em breve</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Coming Soon Card-->

        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection