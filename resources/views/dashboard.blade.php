@extends('components.layouts.app')

@section('title', 'Dashboard - Sistema Parlamentar')



@section('content')
<style>
.dashboard-card-primary {
    background: linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.dashboard-card-info {
    background: linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.dashboard-card-success {
    background: linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.dashboard-card-warning {
    background: linear-gradient(135deg, #FFC700 0%, #e6b300 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #FFC700 0%, #e6b300 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
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
                    Dashboard
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('home') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Dashboard</li>
                </ul>
            </div>
            <!--end::Page title-->
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

            <!--begin::Row-->
            <div class="row gy-5 gx-xl-8">
                <!--begin::Col-->
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <!--begin::Card widget 20-->
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-primary">
                        <!--begin::Header-->
                        <div class="card-header pt-5 pb-3">
                            <!--begin::Icon-->
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-people text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </div>
                            <!--end::Icon-->
                        </div>
                        <!--end::Header-->
                        <!--begin::Card body-->
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <!--begin::Stats-->
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">5</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">/ 21</span>
                            </div>
                            <!--end::Stats-->
                            <!--begin::Title-->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Parlamentares</span>
                                <span class="badge badge-light-primary fs-8">24%</span>
                            </div>
                            <!--end::Title-->
                            <!--begin::Progress-->
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: 24%"></div>
                            </div>
                            <!--end::Progress-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card widget 20-->
                </div>
                <!--end::Col-->
                
                <!--begin::Col-->
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <!--begin::Card widget 20-->
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-info">
                        <!--begin::Header-->
                        <div class="card-header pt-5 pb-3">
                            <!--begin::Icon-->
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-document text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <!--end::Icon-->
                        </div>
                        <!--end::Header-->
                        <!--begin::Card body-->
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <!--begin::Stats-->
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">0</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">projetos</span>
                            </div>
                            <!--end::Stats-->
                            <!--begin::Title-->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Projetos</span>
                                <span class="badge badge-light-info fs-8">0%</span>
                            </div>
                            <!--end::Title-->
                            <!--begin::Progress-->
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: 0%"></div>
                            </div>
                            <!--end::Progress-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card widget 20-->
                </div>
                <!--end::Col-->
                
                <!--begin::Col-->
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <!--begin::Card widget 20-->
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-success">
                        <!--begin::Header-->
                        <div class="card-header pt-5 pb-3">
                            <!--begin::Icon-->
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-calendar text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <!--end::Icon-->
                        </div>
                        <!--end::Header-->
                        <!--begin::Card body-->
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <!--begin::Stats-->
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">0</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">sessões</span>
                            </div>
                            <!--end::Stats-->
                            <!--begin::Title-->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Sessões</span>
                                <span class="badge badge-light-success fs-8">0%</span>
                            </div>
                            <!--end::Title-->
                            <!--begin::Progress-->
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: 0%"></div>
                            </div>
                            <!--end::Progress-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card widget 20-->
                </div>
                <!--end::Col-->
                
                <!--begin::Col-->
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <!--begin::Card widget 20-->
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-warning">
                        <!--begin::Header-->
                        <div class="card-header pt-5 pb-3">
                            <!--begin::Icon-->
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-questionnaire-tablet text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <!--end::Icon-->
                        </div>
                        <!--end::Header-->
                        <!--begin::Card body-->
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <!--begin::Stats-->
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">0</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">votações</span>
                            </div>
                            <!--end::Stats-->
                            <!--begin::Title-->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Votações</span>
                                <span class="badge badge-light-warning fs-8">0%</span>
                            </div>
                            <!--end::Title-->
                            <!--begin::Progress-->
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: 0%"></div>
                            </div>
                            <!--end::Progress-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card widget 20-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
            
            <!--begin::Row-->
            <div class="row g-5 g-xl-8 mt-5 mt-xl-8">
                <!--begin::Col-->
                <div class="col-xl-6 col-lg-12">
                    <!--begin::Modules Progress Card-->
                    <div class="card card-flush h-xl-100 mb-5 mb-xl-10">
                        <!--begin::Header-->
                        <div class="card-header pt-5 pb-3 border-0 bg-light-primary">
                            <!--begin::Title-->
                            <div class="card-title align-items-start flex-column w-100">
                                <!--begin::Info-->
                                <div class="d-flex align-items-center mb-3">
                                    <!--begin::Icon-->
                                    <div class="symbol symbol-50px me-3">
                                        <div class="symbol-label bg-primary text-white">
                                            <i class="ki-duotone ki-element-7 fs-2 text-white">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <!--end::Icon-->
                                    <!--begin::Title Text-->
                                    <div class="flex-grow-1">
                                        <h3 class="card-label fw-bold text-gray-900 mb-1">Módulos do Sistema</h3>
                                        <div class="text-muted fw-semibold fs-7">Sistema de Tramitação Parlamentar</div>
                                    </div>
                                    <!--end::Title Text-->
                                </div>
                                <!--end::Info-->
                                
                                <!--begin::Stats-->
                                <div class="d-flex align-items-center justify-content-between w-100">
                                    <div class="d-flex align-items-baseline">
                                        <span class="fs-2qx fw-bold text-gray-900 me-3 lh-1 ls-n1" data-kt-countup="true" data-kt-countup-value="20">0</span>
                                        <div class="d-flex flex-column">
                                            <span class="badge badge-light-success fs-8 fw-bold mb-1">
                                                <i class="ki-duotone ki-arrow-up fs-8 text-success me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                1 Ativo
                                            </span>
                                            <span class="text-muted fs-8">Total de módulos</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-gray-900 fw-bold fs-6">
                                            <span data-kt-countup="true" data-kt-countup-value="360">0</span>+
                                        </div>
                                        <div class="text-muted fs-8">funcionalidades</div>
                                    </div>
                                </div>
                                <!--end::Stats-->
                            </div>
                            <!--end::Title-->
                        </div>
                        <!--end::Header-->
                        
                        <!--begin::Card body-->
                        <div class="card-body pt-3 pb-5">
                            <!--begin::Progress Items-->
                            <div class="d-flex flex-column gap-4">
                                <!--begin::Item-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Symbol-->
                                    <div class="symbol symbol-40px me-4">
                                        <div class="symbol-label bg-light-success">
                                            <i class="ki-duotone ki-people text-success fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                                <span class="path5"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <!--end::Symbol-->
                                    <!--begin::Content-->
                                    <div class="flex-grow-1">
                                        <a href="{{ route('parlamentares.index') }}" class="text-gray-900 fw-bold text-hover-primary fs-6 mb-1">Parlamentares</a>
                                        <div class="text-gray-600 fs-7 mb-2">Sistema de gestão completo</div>
                                        <div class="progress h-5px bg-light-success">
                                            <div class="progress-bar bg-success" style="width: 90%"></div>
                                        </div>
                                    </div>
                                    <!--end::Content-->
                                    <!--begin::Status-->
                                    <div class="text-end ms-3">
                                        <span class="badge badge-success fs-8 fw-bold mb-1">✓ Ativo</span>
                                        <div class="text-gray-500 fs-8">18/20</div>
                                    </div>
                                    <!--end::Status-->
                                </div>
                                <!--end::Item-->
                                
                                <!--begin::Item-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Symbol-->
                                    <div class="symbol symbol-40px me-4">
                                        <div class="symbol-label bg-light-warning">
                                            <i class="ki-duotone ki-document text-warning fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <!--end::Symbol-->
                                    <!--begin::Content-->
                                    <div class="flex-grow-1">
                                        <div class="text-gray-900 fw-bold fs-6 mb-1">Projetos & Tramitação</div>
                                        <div class="text-gray-600 fs-7 mb-2">Gestão de projetos de lei</div>
                                        <div class="progress h-5px bg-light-warning">
                                            <div class="progress-bar bg-warning" style="width: 17%"></div>
                                        </div>
                                    </div>
                                    <!--end::Content-->
                                    <!--begin::Status-->
                                    <div class="text-end ms-3">
                                        <span class="badge badge-warning fs-8 fw-bold mb-1">⏳ Dev</span>
                                        <div class="text-gray-500 fs-8">5/30</div>
                                    </div>
                                    <!--end::Status-->
                                </div>
                                <!--end::Item-->
                                
                                <!--begin::Item-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Symbol-->
                                    <div class="symbol symbol-40px me-4">
                                        <div class="symbol-label bg-light-primary">
                                            <i class="ki-duotone ki-calendar text-primary fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <!--end::Symbol-->
                                    <!--begin::Content-->
                                    <div class="flex-grow-1">
                                        <div class="text-gray-900 fw-bold fs-6 mb-1">Sessões Plenárias</div>
                                        <div class="text-gray-600 fs-7 mb-2">Controle de sessões</div>
                                        <div class="progress h-5px bg-light-primary">
                                            <div class="progress-bar bg-primary" style="width: 0%"></div>
                                        </div>
                                    </div>
                                    <!--end::Content-->
                                    <!--begin::Status-->
                                    <div class="text-end ms-3">
                                        <span class="badge badge-primary fs-8 fw-bold mb-1">📋 Plan</span>
                                        <div class="text-gray-500 fs-8">0/25</div>
                                    </div>
                                    <!--end::Status-->
                                </div>
                                <!--end::Item-->
                            </div>
                            <!--end::Progress Items-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Modules Progress Card-->
                </div>
                <!--end::Col-->
                
                <!--begin::Col-->
                <div class="col-xl-6 col-lg-12">
                    <!--begin::Active Users Card-->
                    <div class="card card-flush h-xl-100 mb-5 mb-xl-10">
                        <!--begin::Header-->
                        <div class="card-header pt-5 pb-3 border-0 bg-light-success">
                            <!--begin::Title-->
                            <div class="card-title align-items-start flex-column w-100">
                                <!--begin::Info-->
                                <div class="d-flex align-items-center mb-3">
                                    <!--begin::Icon-->
                                    <div class="symbol symbol-50px me-3">
                                        <div class="symbol-label bg-success text-white">
                                            <i class="ki-duotone ki-user-tick fs-2 text-white">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <!--end::Icon-->
                                    <!--begin::Title Text-->
                                    <div class="flex-grow-1">
                                        <h3 class="card-label fw-bold text-gray-900 mb-1">Usuários Ativos</h3>
                                        <div class="text-muted fw-semibold fs-7">Online nos últimos 30 dias</div>
                                    </div>
                                    <!--end::Title Text-->
                                </div>
                                <!--end::Info-->
                                
                                <!--begin::Stats-->
                                <div class="d-flex align-items-center justify-content-between w-100">
                                    <div class="d-flex align-items-baseline">
                                        <span class="fs-2qx fw-bold text-gray-900 me-3 lh-1 ls-n1" data-kt-countup="true" data-kt-countup-value="12">0</span>
                                        <div class="d-flex flex-column">
                                            <span class="badge badge-light-success fs-8 fw-bold mb-1">
                                                <i class="ki-duotone ki-arrow-up fs-8 text-success me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                +25%
                                            </span>
                                            <span class="text-muted fs-8">vs mês anterior</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-gray-900 fw-bold fs-6">48%</div>
                                        <div class="text-muted fs-8">taxa de atividade</div>
                                    </div>
                                </div>
                                <!--end::Stats-->
                            </div>
                            <!--end::Title-->
                        </div>
                        <!--end::Header-->
                        
                        <!--begin::Card body-->
                        <div class="card-body pt-3 pb-5">
                            <!--begin::Activity Stats-->
                            <div class="d-flex flex-column gap-4">
                                <!--begin::Activity Item-->
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px me-4">
                                        <div class="symbol-label bg-light-success">
                                            <i class="ki-duotone ki-people text-success fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                                <span class="path5"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="text-gray-900 fw-bold fs-6 mb-1">Parlamentares</div>
                                        <div class="text-gray-600 fs-7 mb-2">5 membros cadastrados</div>
                                        <div class="progress h-5px bg-light-success">
                                            <div class="progress-bar bg-success" style="width: 100%"></div>
                                        </div>
                                    </div>
                                    <div class="text-end ms-3">
                                        <span class="badge badge-success fs-8 fw-bold mb-1">100%</span>
                                        <div class="text-gray-500 fs-8">5/5</div>
                                    </div>
                                </div>
                                <!--end::Activity Item-->
                                
                                <!--begin::Activity Item-->
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px me-4">
                                        <div class="symbol-label bg-light-primary">
                                            <i class="ki-duotone ki-user text-primary fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="text-gray-900 fw-bold fs-6 mb-1">Administradores</div>
                                        <div class="text-gray-600 fs-7 mb-2">3 admins ativos</div>
                                        <div class="progress h-5px bg-light-primary">
                                            <div class="progress-bar bg-primary" style="width: 75%"></div>
                                        </div>
                                    </div>
                                    <div class="text-end ms-3">
                                        <span class="badge badge-primary fs-8 fw-bold mb-1">75%</span>
                                        <div class="text-gray-500 fs-8">3/4</div>
                                    </div>
                                </div>
                                <!--end::Activity Item-->
                                
                                <!--begin::Activity Item-->
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px me-4">
                                        <div class="symbol-label bg-light-warning">
                                            <i class="ki-duotone ki-profile-user text-warning fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="text-gray-900 fw-bold fs-6 mb-1">Outros Usuários</div>
                                        <div class="text-gray-600 fs-7 mb-2">4 usuários diversos</div>
                                        <div class="progress h-5px bg-light-warning">
                                            <div class="progress-bar bg-warning" style="width: 50%"></div>
                                        </div>
                                    </div>
                                    <div class="text-end ms-3">
                                        <span class="badge badge-warning fs-8 fw-bold mb-1">50%</span>
                                        <div class="text-gray-500 fs-8">4/8</div>
                                    </div>
                                </div>
                                <!--end::Activity Item-->
                            </div>
                            <!--end::Activity Stats-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Active Users Card-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
            
            <!--begin::Row-->
            <div class="row gy-5 g-xl-8 mt-5 mt-xl-8">
                <!--begin::Col-->
                <div class="col-xl-8 col-lg-12">
                    <!--begin::Enhanced Modules Table-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Módulos Disponíveis</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Sistema de Tramitação Parlamentar 2.0</span>
                            </h3>
                            <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" title="Ver todos">
                                <a href="{{ route('parlamentares.index') }}" class="btn btn-sm btn-light btn-active-primary">
                                    <i class="ki-duotone ki-plus fs-2"></i>
                                    Ver Parlamentares
                                </a>
                            </div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            <!--begin::Table container-->
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                    <!--begin::Table head-->
                                    <thead>
                                        <tr class="fw-bold text-muted">
                                            <th class="min-w-150px">Módulo</th>
                                            <th class="min-w-140px">Status</th>
                                            <th class="min-w-120px">Funcionalidades</th>
                                            <th class="min-w-100px text-end">Ações</th>
                                        </tr>
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-45px me-5">
                                                        <span class="symbol-label bg-light-primary text-primary fw-bold">P</span>
                                                    </div>
                                                    <div class="d-flex justify-content-start flex-column">
                                                        <a href="{{ route('parlamentares.index') }}" class="text-dark fw-bold text-hover-primary fs-6">Parlamentares</a>
                                                        <span class="text-muted fw-semibold text-muted d-block fs-7">Gestão de parlamentares</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-success fs-7 fw-bold">Implementado</span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex flex-column w-100 me-2">
                                                    <div class="d-flex flex-stack mb-2">
                                                        <span class="text-muted me-2 fs-7 fw-semibold">18 de 20</span>
                                                    </div>
                                                    <div class="progress h-6px w-100">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: 90%" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-end flex-shrink-0">
                                                    <a href="{{ route('parlamentares.index') }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                        <i class="ki-duotone ki-switch fs-5">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-45px me-5">
                                                        <span class="symbol-label bg-light-warning text-warning fw-bold">D</span>
                                                    </div>
                                                    <div class="d-flex justify-content-start flex-column">
                                                        <a href="#" class="text-dark fw-bold text-hover-primary fs-6">Documentos</a>
                                                        <span class="text-muted fw-semibold text-muted d-block fs-7">Projetos e tramitação</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-info fs-7 fw-bold">Em breve</span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex flex-column w-100 me-2">
                                                    <div class="d-flex flex-stack mb-2">
                                                        <span class="text-muted me-2 fs-7 fw-semibold">0 de 30</span>
                                                    </div>
                                                    <div class="progress h-6px w-100">
                                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-end flex-shrink-0">
                                                    <span class="badge badge-light-info">Próximo</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-45px me-5">
                                                        <span class="symbol-label bg-light-success text-success fw-bold">S</span>
                                                    </div>
                                                    <div class="d-flex justify-content-start flex-column">
                                                        <a href="#" class="text-dark fw-bold text-hover-primary fs-6">Sessões</a>
                                                        <span class="text-muted fw-semibold text-muted d-block fs-7">Controle de sessões</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-info fs-7 fw-bold">Em breve</span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex flex-column w-100 me-2">
                                                    <div class="d-flex flex-stack mb-2">
                                                        <span class="text-muted me-2 fs-7 fw-semibold">0 de 25</span>
                                                    </div>
                                                    <div class="progress h-6px w-100">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-end flex-shrink-0">
                                                    <span class="badge badge-light-info">Planejado</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Table container-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Tables Widget 3-->
                </div>
                <!--end::Col-->
                
                <!--begin::Col-->
                <div class="col-xl-4">
                    <!--begin::List Widget 4-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header align-items-center border-0 mt-4">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="fw-bold mb-2 text-dark">Acesso Rápido</span>
                                <span class="text-muted fw-semibold fs-7">Principais funcionalidades</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-5">
                            <!--begin::Item-->
                            <div class="d-flex align-items-sm-center mb-7">
                                <!--begin::Symbol-->
                                <div class="symbol symbol-50px me-5">
                                    <span class="symbol-label">
                                        <i class="ki-duotone ki-people fs-2x text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                        </i>
                                    </span>
                                </div>
                                <!--end::Symbol-->
                                <!--begin::Section-->
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    <div class="flex-grow-1 me-2">
                                        <a href="{{ route('parlamentares.index') }}" class="text-gray-800 text-hover-primary fs-6 fw-bold">Ver Parlamentares</a>
                                        <span class="text-muted fw-semibold d-block fs-7">Lista completa e estatísticas</span>
                                    </div>
                                    <a href="{{ route('parlamentares.index') }}" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
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
                                    <span class="symbol-label">
                                        <i class="ki-duotone ki-abstract-39 fs-2x text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <!--end::Symbol-->
                                <!--begin::Section-->
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    <div class="flex-grow-1 me-2">
                                        <a href="{{ route('parlamentares.mesa-diretora') }}" class="text-gray-800 text-hover-primary fs-6 fw-bold">Mesa Diretora</a>
                                        <span class="text-muted fw-semibold d-block fs-7">Composição atual da mesa</span>
                                    </div>
                                    <a href="{{ route('parlamentares.mesa-diretora') }}" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
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
                                    <span class="symbol-label">
                                        <i class="ki-duotone ki-plus fs-2x text-success">
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
                                        <a href="{{ route('parlamentares.create') }}" class="text-gray-800 text-hover-primary fs-6 fw-bold">Novo Parlamentar</a>
                                        <span class="text-muted fw-semibold d-block fs-7">Cadastrar novo parlamentar</span>
                                    </div>
                                    <a href="{{ route('parlamentares.create') }}" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
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
                                    <span class="symbol-label">
                                        <i class="ki-duotone ki-user fs-2x text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <!--end::Symbol-->
                                <!--begin::Section-->
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    <div class="flex-grow-1 me-2">
                                        <a href="{{ route('user-api.index') }}" class="text-gray-800 text-hover-primary fs-6 fw-bold">Gestão de Usuários</a>
                                        <span class="text-muted fw-semibold d-block fs-7">Administração do sistema</span>
                                    </div>
                                    <a href="{{ route('user-api.index') }}" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
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
                    <!--end::List Widget 4-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection