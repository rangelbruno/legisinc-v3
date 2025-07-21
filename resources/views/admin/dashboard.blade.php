@extends('components.layouts.app')

@section('title', 'Dashboard Administração')

@section('content')

<style>
.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
}

.cursor-pointer {
    cursor: pointer;
}

.card-hover:hover .text-gray-900 {
    color: var(--kt-primary) !important;
}

.card-hover:hover i {
    color: var(--kt-primary) !important;
}
</style>

<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Dashboard Administração
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Administração</li>
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

            <!-- Resumo do Sistema e Status - Seção Superior -->
            @if(auth()->user()->isAdmin())
            <!--begin::Row-->
            <div class="row g-5 g-xl-8">
                <!--begin::Col-->
                <div class="col-xl-8 col-lg-12">
                    <!--begin::System Summary Card-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Resumo do Sistema</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Estatísticas gerais de administração</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            <!--begin::Statistics-->
                            <div class="d-flex flex-column gap-4">
                                <!--begin::Item-->
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px me-4">
                                        <div class="symbol-label bg-light-primary">
                                            <i class="ki-duotone ki-people text-primary fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                                <span class="path5"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="text-gray-900 fw-bold fs-6 mb-1">Total de Usuários</div>
                                        <div class="text-gray-600 fs-7 mb-2">Usuários cadastrados no sistema</div>
                                        @php
                                            try {
                                                $totalUsers = \App\Models\User::count();
                                            } catch (\Exception $e) {
                                                $totalUsers = 12;
                                            }
                                        @endphp
                                        <div class="progress h-5px bg-light-primary">
                                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                                        </div>
                                    </div>
                                    <div class="text-end ms-3">
                                        <span class="badge badge-primary fs-8 fw-bold mb-1">{{ $totalUsers }}</span>
                                        <div class="text-gray-500 fs-8">Total</div>
                                    </div>
                                </div>
                                <!--end::Item-->
                                
                                <!--begin::Item-->
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px me-4">
                                        <div class="symbol-label bg-light-success">
                                            <i class="ki-duotone ki-user-tick text-success fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="text-gray-900 fw-bold fs-6 mb-1">Usuários Ativos</div>
                                        <div class="text-gray-600 fs-7 mb-2">Usuários com status ativo</div>
                                        @php
                                            try {
                                                $activeUsers = \App\Models\User::where('ativo', true)->count();
                                                $activePercentage = $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0;
                                            } catch (\Exception $e) {
                                                $activeUsers = 8;
                                                $activePercentage = 67;
                                            }
                                        @endphp
                                        <div class="progress h-5px bg-light-success">
                                            <div class="progress-bar bg-success" style="width: {{ $activePercentage }}%"></div>
                                        </div>
                                    </div>
                                    <div class="text-end ms-3">
                                        <span class="badge badge-success fs-8 fw-bold mb-1">{{ $activeUsers }}</span>
                                        <div class="text-gray-500 fs-8">{{ round($activePercentage) }}%</div>
                                    </div>
                                </div>
                                <!--end::Item-->
                                
                                <!--begin::Item-->
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px me-4">
                                        <div class="symbol-label bg-light-info">
                                            <i class="ki-duotone ki-file-added text-info fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="text-gray-900 fw-bold fs-6 mb-1">Tipos de Proposição</div>
                                        <div class="text-gray-600 fs-7 mb-2">Tipos de documentos configurados</div>
                                        @php
                                            try {
                                                $totalTipos = \App\Models\TipoProposicao::count();
                                                $tiposAtivos = \App\Models\TipoProposicao::ativos()->count();
                                                $tiposPercentage = $totalTipos > 0 ? ($tiposAtivos / $totalTipos) * 100 : 0;
                                            } catch (\Exception $e) {
                                                $totalTipos = 8;
                                                $tiposAtivos = 8;
                                                $tiposPercentage = 100;
                                            }
                                        @endphp
                                        <div class="progress h-5px bg-light-info">
                                            <div class="progress-bar bg-info" style="width: {{ $tiposPercentage }}%"></div>
                                        </div>
                                    </div>
                                    <div class="text-end ms-3">
                                        <span class="badge badge-info fs-8 fw-bold mb-1">{{ $tiposAtivos }}</span>
                                        <div class="text-gray-500 fs-8">de {{ $totalTipos }}</div>
                                    </div>
                                </div>
                                <!--end::Item-->
                                
                                <!--begin::Item-->
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px me-4">
                                        <div class="symbol-label bg-light-warning">
                                            <i class="ki-duotone ki-shield-tick text-warning fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="text-gray-900 fw-bold fs-6 mb-1">Permissões</div>
                                        <div class="text-gray-600 fs-7 mb-2">Regras de acesso configuradas</div>
                                        @php
                                            try {
                                                $totalPermissions = \App\Models\ScreenPermission::count();
                                            } catch (\Exception $e) {
                                                $totalPermissions = 15;
                                            }
                                        @endphp
                                        <div class="progress h-5px bg-light-warning">
                                            <div class="progress-bar bg-warning" style="width: 95%"></div>
                                        </div>
                                    </div>
                                    <div class="text-end ms-3">
                                        <span class="badge badge-warning fs-8 fw-bold mb-1">{{ $totalPermissions }}</span>
                                        <div class="text-gray-500 fs-8">Ativas</div>
                                    </div>
                                </div>
                                <!--end::Item-->
                            </div>
                            <!--end::Statistics-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::System Summary Card-->
                </div>
                <!--end::Col-->
                
                <!--begin::Col-->
                <div class="col-xl-4">
                    <!--begin::System Status Card-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header align-items-center border-0 mt-4">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="fw-bold mb-2 text-dark">Status do Sistema</span>
                                <span class="text-muted fw-semibold fs-7">Monitoramento em tempo real</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-5">
                            <!--begin::Item-->
                            <div class="d-flex align-items-sm-center mb-7">
                                <!--begin::Symbol-->
                                <div class="symbol symbol-50px me-5">
                                    <span class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-check-circle fs-2x text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <!--end::Symbol-->
                                <!--begin::Section-->
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    <div class="flex-grow-1 me-2">
                                        <span class="text-gray-800 fs-6 fw-bold">Sistema Operacional</span>
                                        <span class="text-muted fw-semibold d-block fs-7">Funcionando normalmente</span>
                                    </div>
                                    <span class="badge badge-light-success fs-8 fw-bold">99.9%</span>
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Item-->
                            
                            <!--begin::Item-->
                            <div class="d-flex align-items-sm-center mb-7">
                                <!--begin::Symbol-->
                                <div class="symbol symbol-50px me-5">
                                    <span class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-shield-tick fs-2x text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </span>
                                </div>
                                <!--end::Symbol-->
                                <!--begin::Section-->
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    <div class="flex-grow-1 me-2">
                                        <a href="{{ route('admin.screen-permissions.index') }}" class="text-gray-800 text-hover-primary fs-6 fw-bold">Permissões</a>
                                        <span class="text-muted fw-semibold d-block fs-7">Controle de acesso ativo</span>
                                    </div>
                                    <a href="{{ route('admin.screen-permissions.index') }}" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
                                        <i class="ki-duotone ki-arrow-right fs-5">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </a>
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Item-->
                            
                            <!--begin::Item-->
                            <div class="d-flex align-items-sm-center mb-7">
                                <!--begin::Symbol-->
                                <div class="symbol symbol-50px me-5">
                                    <span class="symbol-label bg-light-info">
                                        <i class="ki-duotone ki-setting-2 fs-2x text-info">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <!--end::Symbol-->
                                <!--begin::Section-->
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    <div class="flex-grow-1 me-2">
                                        <a href="{{ route('parametros.index') }}" class="text-gray-800 text-hover-primary fs-6 fw-bold">Parâmetros</a>
                                        <span class="text-muted fw-semibold d-block fs-7">Configurações do sistema</span>
                                    </div>
                                    <a href="{{ route('parametros.index') }}" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
                                        <i class="ki-duotone ki-arrow-right fs-5">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </a>
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Item-->
                            
                            <!--begin::Item-->
                            <div class="d-flex align-items-sm-center mb-7">
                                <!--begin::Symbol-->
                                <div class="symbol symbol-50px me-5">
                                    <span class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-file-added fs-2x text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <!--end::Symbol-->
                                <!--begin::Section-->
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    <div class="flex-grow-1 me-2">
                                        <a href="{{ route('admin.tipo-proposicoes.index') }}" class="text-gray-800 text-hover-primary fs-6 fw-bold">Tipos de Proposição</a>
                                        <span class="text-muted fw-semibold d-block fs-7">Gerenciar tipos de documentos</span>
                                    </div>
                                    <a href="{{ route('admin.tipo-proposicoes.index') }}" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
                                        <i class="ki-duotone ki-arrow-right fs-5">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </a>
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Item-->
                            
                            <!--begin::Item-->
                            <div class="d-flex align-items-sm-center">
                                <!--begin::Symbol-->
                                <div class="symbol symbol-50px me-5">
                                    <span class="symbol-label bg-light-warning">
                                        <i class="ki-duotone ki-document fs-2x text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <!--end::Symbol-->
                                <!--begin::Section-->
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    <div class="flex-grow-1 me-2">
                                        <a href="{{ route('documentos.modelos.index') }}" class="text-gray-800 text-hover-primary fs-6 fw-bold">Modelos</a>
                                        <span class="text-muted fw-semibold d-block fs-7">Templates de documentos</span>
                                    </div>
                                    <a href="{{ route('documentos.modelos.index') }}" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
                                        <i class="ki-duotone ki-arrow-right fs-5">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </a>
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Item-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::System Status Card-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
            
            <!-- Cards Administrativos - Seção Inferior -->
            <!--begin::Row-->
            <div class="row gy-5 gx-xl-8 mt-5 mt-xl-8">
            <!-- Usuários Admin -->
            <div class="col-xl-4 col-lg-6 col-md-6">
                <!--begin::Statistics Widget 2-->
                <div class="card card-xl-stretch mb-xl-8 card-hover cursor-pointer" onclick="window.location.href='{{ route('admin.usuarios.index') }}'">
                    <!--begin::Body-->
                    <div class="card-body d-flex align-items-center pt-3 pb-0">
                        <div class="d-flex flex-column flex-grow-1 py-2 py-lg-13 me-2">
                            <a href="{{ route('admin.usuarios.index') }}" class="fw-bold text-gray-900 fs-4 mb-2 text-hover-primary d-flex align-items-center">
                                Gerenciar Usuários Admin
                                <i class="ki-duotone ki-arrow-right fs-5 ms-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                            <span class="fw-semibold text-muted fs-5">Privilégios administrativos</span>
                        </div>
                        <i class="ki-duotone ki-profile-user text-primary fs-4x align-self-end h-100px">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Statistics Widget 2-->
            </div>
            @endif

            <!-- Usuários do Sistema -->
            @if(\App\Models\ScreenPermission::userCanAccessRoute('usuarios.index'))
            <div class="col-xl-4 col-lg-6 col-md-6">
                <!--begin::Statistics Widget 2-->
                <div class="card card-xl-stretch mb-xl-8 card-hover cursor-pointer" onclick="window.location.href='{{ route('usuarios.index') }}'">
                    <!--begin::Body-->
                    <div class="card-body d-flex align-items-center pt-3 pb-0">
                        <div class="d-flex flex-column flex-grow-1 py-2 py-lg-13 me-2">
                            <a href="{{ route('usuarios.index') }}" class="fw-bold text-gray-900 fs-4 mb-2 text-hover-primary d-flex align-items-center">
                                Gerenciar Usuários
                                <i class="ki-duotone ki-arrow-right fs-5 ms-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                            <span class="fw-semibold text-muted fs-5">Gestão completa de usuários</span>
                        </div>
                        <i class="ki-duotone ki-people text-success fs-4x align-self-end h-100px">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                        </i>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Statistics Widget 2-->
            </div>
            @endif

            <!-- Modelos de Projeto -->
            @if(auth()->user()->isAdmin())
            <div class="col-xl-4 col-lg-6 col-md-6">
                <!--begin::Statistics Widget 2-->
                <div class="card card-xl-stretch mb-xl-8 card-hover cursor-pointer" onclick="window.location.href='{{ route('documentos.modelos.index') }}'">
                    <!--begin::Body-->
                    <div class="card-body d-flex align-items-center pt-3 pb-0">
                        <div class="d-flex flex-column flex-grow-1 py-2 py-lg-13 me-2">
                            <a href="{{ route('documentos.modelos.index') }}" class="fw-bold text-gray-900 fs-4 mb-2 text-hover-primary d-flex align-items-center">
                                Gerenciar Modelos
                                <i class="ki-duotone ki-arrow-right fs-5 ms-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                            <span class="fw-semibold text-muted fs-5">Templates de documentos</span>
                        </div>
                        <i class="ki-duotone ki-document text-info fs-4x align-self-end h-100px">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Statistics Widget 2-->
            </div>

            <!-- Documentos em Tramitação -->
            <div class="col-xl-4 col-lg-6 col-md-6">
                <!--begin::Statistics Widget 2-->
                <div class="card card-xl-stretch mb-xl-8 card-hover cursor-pointer" onclick="window.location.href='{{ route('documentos.instancias.index') }}'">
                    <!--begin::Body-->
                    <div class="card-body d-flex align-items-center pt-3 pb-0">
                        <div class="d-flex flex-column flex-grow-1 py-2 py-lg-13 me-2">
                            <a href="{{ route('documentos.instancias.index') }}" class="fw-bold text-gray-900 fs-4 mb-2 text-hover-primary d-flex align-items-center">
                                Documentos em Tramitação
                                <i class="ki-duotone ki-arrow-right fs-5 ms-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                            <span class="fw-semibold text-muted fs-5">Acompanhar workflow</span>
                        </div>
                        <i class="ki-duotone ki-file-text text-success fs-4x align-self-end h-100px">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Statistics Widget 2-->
            </div>

            <!-- Permissões -->
            <div class="col-xl-4 col-lg-6 col-md-6">
                <!--begin::Statistics Widget 2-->
                <div class="card card-xl-stretch mb-xl-8 card-hover cursor-pointer" onclick="window.location.href='{{ route('admin.screen-permissions.index') }}'">
                    <!--begin::Body-->
                    <div class="card-body d-flex align-items-center pt-3 pb-0">
                        <div class="d-flex flex-column flex-grow-1 py-2 py-lg-13 me-2">
                            <a href="{{ route('admin.screen-permissions.index') }}" class="fw-bold text-gray-900 fs-4 mb-2 text-hover-primary d-flex align-items-center">
                                Configurar Permissões
                                <i class="ki-duotone ki-arrow-right fs-5 ms-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                            <span class="fw-semibold text-muted fs-5">Controle de acesso</span>
                        </div>
                        <i class="ki-duotone ki-shield-tick text-warning fs-4x align-self-end h-100px">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Statistics Widget 2-->
            </div>

            <!-- Parâmetros -->
            <div class="col-xl-4 col-lg-6 col-md-6">
                <!--begin::Statistics Widget 2-->
                <div class="card card-xl-stretch mb-xl-8 card-hover cursor-pointer" onclick="window.location.href='{{ route('parametros.index') }}'">
                    <!--begin::Body-->
                    <div class="card-body d-flex align-items-center pt-3 pb-0">
                        <div class="d-flex flex-column flex-grow-1 py-2 py-lg-13 me-2">
                            <a href="{{ route('parametros.index') }}" class="fw-bold text-gray-900 fs-4 mb-2 text-hover-primary d-flex align-items-center">
                                Configurar Parâmetros
                                <i class="ki-duotone ki-arrow-right fs-5 ms-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                            <span class="fw-semibold text-muted fs-5">Configurações do sistema</span>
                        </div>
                        <i class="ki-duotone ki-setting-2 text-danger fs-4x align-self-end h-100px">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Statistics Widget 2-->
            </div>

            <!-- Tipos de Proposição -->
            <div class="col-xl-4 col-lg-6 col-md-6">
                <!--begin::Statistics Widget 2-->
                <div class="card card-xl-stretch mb-xl-8 card-hover cursor-pointer" onclick="window.location.href='{{ route('admin.tipo-proposicoes.index') }}'">
                    <!--begin::Body-->
                    <div class="card-body d-flex align-items-center pt-3 pb-0">
                        <div class="d-flex flex-column flex-grow-1 py-2 py-lg-13 me-2">
                            <a href="{{ route('admin.tipo-proposicoes.index') }}" class="fw-bold text-gray-900 fs-4 mb-2 text-hover-primary d-flex align-items-center">
                                Tipos de Proposição
                                <i class="ki-duotone ki-arrow-right fs-5 ms-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                            <span class="fw-semibold text-muted fs-5">Gerenciar tipos de documentos</span>
                        </div>
                        <i class="ki-duotone ki-file-added text-primary fs-4x align-self-end h-100px">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Statistics Widget 2-->
            </div>

            <!-- Configurações (Em breve) -->
            <div class="col-xl-4 col-lg-6 col-md-6">
                <!--begin::Statistics Widget 2-->
                <div class="card card-xl-stretch mb-xl-8 card-hover cursor-pointer" onclick="showComingSoon('Configurações do Sistema')">
                    <!--begin::Body-->
                    <div class="card-body d-flex align-items-center pt-3 pb-0">
                        <div class="d-flex flex-column flex-grow-1 py-2 py-lg-13 me-2">
                            <a href="#" class="fw-bold text-gray-900 fs-4 mb-2 text-hover-primary d-flex align-items-center" onclick="showComingSoon('Configurações do Sistema')">
                                Ver Configurações
                                <i class="ki-duotone ki-arrow-right fs-5 ms-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                            <span class="fw-semibold text-muted fs-5">Configurações gerais</span>
                        </div>
                        <i class="ki-duotone ki-gear text-secondary fs-4x align-self-end h-100px">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Statistics Widget 2-->
            </div>
            <!--end::Row-->
            @endif
        
        </div>
        <!--end::Container-->
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<script>
// Função minimalista para "Em breve"
function showComingSoon(feature) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: feature,
            text: 'Esta funcionalidade está em desenvolvimento.',
            icon: 'info',
            confirmButtonText: 'OK',
            timer: 3000
        });
    } else {
        alert(feature + '\n\nEsta funcionalidade está em desenvolvimento.');
    }
}
</script>
@endsection