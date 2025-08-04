@extends('components.layouts.app')

@section('title', 'Central de Testes - Sistema Parlamentar')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Central de Testes do Sistema
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
                    <li class="breadcrumb-item text-muted">Testes</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div class="text-end d-none d-sm-block">
                    <div class="text-muted fw-semibold fs-7">{{ date('d/m/Y H:i') }}</div>
                </div>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Stats-->
            <div class="row g-5 g-xl-8 mb-8">
                <div class="col-xl-3">
                    <div class="card card-flush h-xl-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-45px me-5">
                                    <span class="symbol-label bg-light-success text-success">
                                        <i class="ki-duotone ki-check-circle fs-1"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="text-dark fw-bold fs-6">Testes Disponíveis</span>
                                    <span class="text-muted d-block fw-semibold">6 Categorias</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="card card-flush h-xl-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-45px me-5">
                                    <span class="symbol-label bg-light-primary text-primary">
                                        <i class="ki-duotone ki-code fs-1"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="text-dark fw-bold fs-6">Testes Unitários</span>
                                    <span class="text-muted d-block fw-semibold">Pest Framework</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="card card-flush h-xl-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-45px me-5">
                                    <span class="symbol-label bg-light-warning text-warning">
                                        <i class="ki-duotone ki-people fs-1"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="text-dark fw-bold fs-6">Testes Funcionais</span>
                                    <span class="text-muted d-block fw-semibold">Interface & API</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="card card-flush h-xl-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-45px me-5">
                                    <span class="symbol-label bg-light-info text-info">
                                        <i class="ki-duotone ki-rocket fs-1"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="text-dark fw-bold fs-6">Testes Performance</span>
                                    <span class="text-muted d-block fw-semibold">Carga & Stress</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Stats-->

            <!--begin::Test Categories Cards-->
            <div class="row g-6 g-xl-8">
                
                <!--begin::Testes de Usuários-->
                <div class="col-xl-4">
                    <div class="card card-flush h-100">
                        <div class="card-header">
                            <div class="card-title">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-5">
                                        <span class="symbol-label bg-light-primary text-primary">
                                            <i class="ki-duotone ki-people fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                                <span class="path5"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h3 class="fw-bold text-dark fs-4 mb-1">Testes de Usuários</h3>
                                        <span class="text-muted fs-7">Gestão e criação de usuários de teste</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-5">
                            <p class="text-gray-600 fs-6 mb-8">
                                Criar, listar e gerenciar usuários de teste para validação das funcionalidades do sistema.
                            </p>
                            <div class="d-flex flex-stack">
                                <div class="text-muted fs-7">
                                    <i class="ki-duotone ki-check-circle text-success fs-6 me-2"></i>
                                    Criação automática<br>
                                    <i class="ki-duotone ki-check-circle text-success fs-6 me-2"></i>
                                    Múltiplos perfis<br>
                                    <i class="ki-duotone ki-check-circle text-success fs-6 me-2"></i>
                                    Limpeza rápida
                                </div>
                                <a href="{{ route('tests.users') }}" class="btn btn-primary">
                                    Acessar Testes
                                    <i class="ki-duotone ki-arrows-right fs-4 ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Testes de Usuários-->

                <!--begin::Testes de Processos-->
                <div class="col-xl-4">
                    <div class="card card-flush h-100">
                        <div class="card-header">
                            <div class="card-title">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-5">
                                        <span class="symbol-label bg-light-success text-success">
                                            <i class="ki-duotone ki-setting-2 fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h3 class="fw-bold text-dark fs-4 mb-1">Testes de Processos</h3>
                                        <span class="text-muted fs-7">Workflows e processamento de templates</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-5">
                            <p class="text-gray-600 fs-6 mb-8">
                                Validar processamento de templates, workflows de proposições e integração entre módulos.
                            </p>
                            <div class="d-flex flex-stack">
                                <div class="text-muted fs-7">
                                    <i class="ki-duotone ki-check-circle text-success fs-6 me-2"></i>
                                    Template processing<br>
                                    <i class="ki-duotone ki-check-circle text-success fs-6 me-2"></i>
                                    Validação de dados<br>
                                    <i class="ki-duotone ki-check-circle text-success fs-6 me-2"></i>
                                    Testes Pest
                                </div>
                                <a href="{{ route('tests.processes') }}" class="btn btn-success">
                                    Acessar Testes
                                    <i class="ki-duotone ki-arrows-right fs-4 ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Testes de Processos-->

                <!--begin::Testes de API-->
                <div class="col-xl-4">
                    <div class="card card-flush h-100">
                        <div class="card-header">
                            <div class="card-title">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-5">
                                        <span class="symbol-label bg-light-warning text-warning">
                                            <i class="ki-duotone ki-technology-2 fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h3 class="fw-bold text-dark fs-4 mb-1">Testes de API</h3>
                                        <span class="text-muted fs-7">Endpoints e integração de APIs</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-5">
                            <p class="text-gray-600 fs-6 mb-8">
                                Testar endpoints da API, autenticação, validação de dados e responses dos serviços.
                            </p>
                            <div class="d-flex flex-stack">
                                <div class="text-muted fs-7">
                                    <i class="ki-duotone ki-check-circle text-success fs-6 me-2"></i>
                                    Endpoints REST<br>
                                    <i class="ki-duotone ki-check-circle text-success fs-6 me-2"></i>
                                    Autenticação<br>
                                    <i class="ki-duotone ki-check-circle text-success fs-6 me-2"></i>
                                    Response validation
                                </div>
                                <a href="{{ route('tests.api') }}" class="btn btn-warning">
                                    Acessar Testes
                                    <i class="ki-duotone ki-arrows-right fs-4 ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Testes de API-->

                <!--begin::Testes de Database-->
                <div class="col-xl-4">
                    <div class="card card-flush h-100">
                        <div class="card-header">
                            <div class="card-title">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-5">
                                        <span class="symbol-label bg-light-info text-info">
                                            <i class="ki-duotone ki-data fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h3 class="fw-bold text-dark fs-4 mb-1">Testes de Database</h3>
                                        <span class="text-muted fs-7">Integridade e performance do BD</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-5">
                            <p class="text-gray-600 fs-6 mb-8">
                                Validar integridade dos dados, migrations, seeds e performance das consultas do banco.
                            </p>
                            <div class="d-flex flex-stack">
                                <div class="text-muted fs-7">
                                    <i class="ki-duotone ki-check-circle text-success fs-6 me-2"></i>
                                    Integridade de dados<br>
                                    <i class="ki-duotone ki-check-circle text-success fs-6 me-2"></i>
                                    Migrations<br>
                                    <i class="ki-duotone ki-check-circle text-success fs-6 me-2"></i>
                                    Performance queries
                                </div>
                                <a href="{{ route('tests.database') }}" class="btn btn-info">
                                    Acessar Testes
                                    <i class="ki-duotone ki-arrows-right fs-4 ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Testes de Database-->

                <!--begin::Testes de Performance-->
                <div class="col-xl-4">
                    <div class="card card-flush h-100">
                        <div class="card-header">
                            <div class="card-title">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-5">
                                        <span class="symbol-label bg-light-danger text-danger">
                                            <i class="ki-duotone ki-rocket fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h3 class="fw-bold text-dark fs-4 mb-1">Testes de Performance</h3>
                                        <span class="text-muted fs-7">Carga, stress e otimização</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-5">
                            <p class="text-gray-600 fs-6 mb-8">
                                Avaliar performance do sistema sob carga, tempo de resposta e limites de capacidade.
                            </p>
                            <div class="d-flex flex-stack">
                                <div class="text-muted fs-7">
                                    <i class="ki-duotone ki-check-circle text-success fs-6 me-2"></i>
                                    Teste de carga<br>
                                    <i class="ki-duotone ki-check-circle text-success fs-6 me-2"></i>
                                    Stress testing<br>
                                    <i class="ki-duotone ki-check-circle text-success fs-6 me-2"></i>
                                    Métricas de tempo
                                </div>
                                <a href="{{ route('tests.performance') }}" class="btn btn-danger">
                                    Acessar Testes
                                    <i class="ki-duotone ki-arrows-right fs-4 ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Testes de Performance-->

                <!--begin::Testes de Segurança-->
                <div class="col-xl-4">
                    <div class="card card-flush h-100">
                        <div class="card-header">
                            <div class="card-title">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-5">
                                        <span class="symbol-label bg-light-dark text-dark">
                                            <i class="ki-duotone ki-shield-tick fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h3 class="fw-bold text-dark fs-4 mb-1">Testes de Segurança</h3>
                                        <span class="text-muted fs-7">Vulnerabilidades e proteções</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-5">
                            <p class="text-gray-600 fs-6 mb-8">
                                Verificar vulnerabilidades, validação de entrada, autenticação e autorização do sistema.
                            </p>
                            <div class="d-flex flex-stack">
                                <div class="text-muted fs-7">
                                    <i class="ki-duotone ki-check-circle text-success fs-6 me-2"></i>
                                    Validação entrada<br>
                                    <i class="ki-duotone ki-check-circle text-success fs-6 me-2"></i>
                                    Autenticação<br>
                                    <i class="ki-duotone ki-check-circle text-success fs-6 me-2"></i>
                                    Scan vulnerabilidades
                                </div>
                                <a href="{{ route('tests.security') }}" class="btn btn-dark">
                                    Acessar Testes
                                    <i class="ki-duotone ki-arrows-right fs-4 ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Testes de Segurança-->

            </div>
            <!--end::Test Categories Cards-->

        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any interactive functionality here if needed
    });
</script>
@endpush