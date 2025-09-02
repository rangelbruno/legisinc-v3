@extends('components.layouts.app')

@section('title', 'Dashboard Administrativo')

@section('content')
<style>
.dashboard-card-primary {
    background: linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
    background-image: url("/assets/media/patterns/vector-1.png"), linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.dashboard-card-info {
    background: linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
    background-image: url("/assets/media/patterns/vector-1.png"), linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.dashboard-card-success {
    background: linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
    background-image: url("/assets/media/patterns/vector-1.png"), linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.dashboard-card-warning {
    background: linear-gradient(135deg, #FFC700 0%, #e6b300 100%) !important;
    background-image: url("/assets/media/patterns/vector-1.png"), linear-gradient(135deg, #FFC700 0%, #e6b300 100%) !important;
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
                    Dashboard Administrativo
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
        <div id="kt_app_content_container" class="app-container container-xxl min-h-xl-450px">
            
            <x-alerts.flash />

            <!--begin::Row-->
            <div class="row g-5 g-xl-8">
                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-primary cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-abstract-26 text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['total_proposicoes'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">proposições</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Total de Proposições</span>
                                <span class="badge badge-light-primary fs-8">100%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-warning cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-document-text text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['em_elaboracao'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">em elaboração</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Em Elaboração</span>
                                <span class="badge badge-light-warning fs-8">{{ $estatisticas['total_proposicoes'] > 0 ? round(($estatisticas['em_elaboracao'] / $estatisticas['total_proposicoes']) * 100) : 0 }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $estatisticas['total_proposicoes'] > 0 ? round(($estatisticas['em_elaboracao'] / $estatisticas['total_proposicoes']) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-info cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-cheque text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                    <span class="path6"></span>
                                    <span class="path7"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['em_revisao'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">em revisão</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Em Revisão</span>
                                <span class="badge badge-light-info fs-8">{{ $estatisticas['total_proposicoes'] > 0 ? round(($estatisticas['em_revisao'] / $estatisticas['total_proposicoes']) * 100) : 0 }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $estatisticas['total_proposicoes'] > 0 ? round(($estatisticas['em_revisao'] / $estatisticas['total_proposicoes']) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-success cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-briefcase text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['em_tramitacao'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">tramitando</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Em Tramitação</span>
                                <span class="badge badge-light-success fs-8">{{ $estatisticas['total_proposicoes'] > 0 ? round(($estatisticas['em_tramitacao'] / $estatisticas['total_proposicoes']) * 100) : 0 }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $estatisticas['total_proposicoes'] > 0 ? round(($estatisticas['em_tramitacao'] / $estatisticas['total_proposicoes']) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

            <!--begin::Métricas Executivas Row-->
            <div class="row g-5 g-xl-8 mt-5 mt-xl-10">
                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-info cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-people text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $metricas_executivas['parlamentares_ativos'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">ativos</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Parlamentares Ativos</span>
                                <span class="badge badge-light-info fs-8">30 dias</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-success cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-calendar-8 text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                    <span class="path6"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $metricas_executivas['proposicoes_hoje'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">hoje</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Proposições Hoje</span>
                                <span class="badge badge-light-success fs-8">+{{ $metricas_executivas['proposicoes_hoje'] }}</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: 75%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-warning cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-chart-pie-simple text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $metricas_executivas['taxa_aprovacao'] }}%</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">aprovação</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Taxa de Aprovação</span>
                                <span class="badge badge-light-warning fs-8">média</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $metricas_executivas['taxa_aprovacao'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-primary cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-timer text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $metricas_executivas['tempo_medio_tramitacao'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">dias</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Tempo Médio</span>
                                <span class="badge badge-light-primary fs-8">tramitação</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: 60%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Métricas Executivas Row-->

            <!--begin::Alertas e Performance Row-->
            <div class="row g-5 g-xl-8 mt-5 mt-xl-10">
                <!--begin::Col-->
                <div class="col-xl-6">
                    <!--begin::Alertas Críticos-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">🚨 Alertas Críticos</span>
                                <span class="text-muted fw-semibold fs-7">Ações requeridas</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            @if($alertas_criticos->count() > 0)
                                <!--begin::Alertas-->
                                <div class="d-flex flex-column gap-4">
                                    @foreach($alertas_criticos as $alerta)
                                    <!--begin::Alerta-->
                                    <div class="d-flex align-items-center p-3 bg-light-{{ $alerta->tipo == 'warning' ? 'warning' : 'info' }} rounded">
                                        <div class="symbol symbol-40px me-4">
                                            <span class="symbol-label bg-{{ $alerta->tipo == 'warning' ? 'warning' : 'info' }}">
                                                <i class="ki-duotone ki-{{ $alerta->tipo == 'warning' ? 'warning' : 'information' }} text-white fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    @if($alerta->tipo == 'info')<span class="path3"></span>@endif
                                                </i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column flex-grow-1">
                                            <span class="text-gray-800 fw-bold fs-6">{{ $alerta->titulo }}</span>
                                            <span class="text-muted fw-semibold">{{ $alerta->descricao }}</span>
                                        </div>
                                        <span class="badge badge-{{ $alerta->tipo == 'warning' ? 'warning' : 'info' }} fw-bold">{{ $alerta->count }}</span>
                                    </div>
                                    <!--end::Alerta-->
                                    @endforeach
                                </div>
                                <!--end::Alertas-->
                            @else
                                <div class="text-center py-10">
                                    <div class="symbol symbol-100px mb-5">
                                        <span class="symbol-label bg-light-success">
                                            <i class="ki-duotone ki-check-circle text-success fs-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <h3 class="text-gray-800 fs-2 fw-bold mb-3">Sistema Operacional</h3>
                                    <p class="text-gray-600 fs-6 fw-semibold">Não há alertas críticos no momento</p>
                                </div>
                            @endif
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Alertas Críticos-->
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-6">
                    <!--begin::Performance Parlamentar-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">🏆 Top Parlamentares</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Por produtividade</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            @if($performance_parlamentar->count() > 0)
                                <!--begin::Lista-->
                                <div class="d-flex flex-column gap-4">
                                    @foreach($performance_parlamentar as $index => $parlamentar)
                                    <!--begin::Item-->
                                    <div class="d-flex align-items-center">
                                        <!--begin::Ranking-->
                                        <div class="symbol symbol-40px me-4">
                                            <span class="symbol-label bg-light-primary">
                                                <span class="text-primary fw-bold fs-4">{{ $index + 1 }}</span>
                                            </span>
                                        </div>
                                        <!--end::Ranking-->
                                        <!--begin::Info-->
                                        <div class="d-flex flex-column flex-grow-1">
                                            <span class="text-gray-800 fw-bold fs-6">{{ $parlamentar->name }}</span>
                                            <span class="text-muted fw-semibold">{{ $parlamentar->total_proposicoes }} proposições</span>
                                        </div>
                                        <!--end::Info-->
                                        <!--begin::Badge-->
                                        <span class="badge badge-light-primary fw-bold">{{ $parlamentar->total_proposicoes }}</span>
                                        <!--end::Badge-->
                                    </div>
                                    <!--end::Item-->
                                    @endforeach
                                </div>
                                <!--end::Lista-->
                            @else
                                <div class="text-center py-10">
                                    <p class="text-muted">Dados de performance não disponíveis</p>
                                </div>
                            @endif
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Performance Parlamentar-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Alertas e Performance Row-->

            <!--begin::Row-->
            <div class="row g-5 g-xl-8 mt-5 mt-xl-10">
                <!--begin::Col-->
                <div class="col-xl-8">
                    <!--begin::Proposições Recentes-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Proposições Recentes</span>
                                <span class="text-muted fw-semibold fs-7">Últimas atualizações no sistema</span>
                            </h3>
                            <div class="card-toolbar">
                                <a href="{{ route('proposicoes.legislativo.index') }}" class="btn btn-sm btn-light btn-active-primary">
                                    Ver Todas
                                </a>
                            </div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            @if($proposicoes_recentes->count() > 0)
                                <!--begin::Table container-->
                                <div class="table-responsive">
                                    <!--begin::Table-->
                                    <table class="table table-row-gray-300 align-middle gs-0 gy-4">
                                        <!--begin::Table head-->
                                        <thead>
                                            <tr class="fw-bold text-muted">
                                                <th class="min-w-150px">Proposição</th>
                                                <th class="min-w-140px">Autor</th>
                                                <th class="min-w-120px">Tipo</th>
                                                <th class="min-w-100px">Status</th>
                                                <th class="min-w-100px text-end">Ações</th>
                                            </tr>
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody>
                                            @foreach($proposicoes_recentes as $proposicao)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="d-flex justify-content-start flex-column">
                                                            <span class="text-dark fw-bold fs-6">
                                                                {{ $proposicao->numero ?? 'S/N' }}/{{ $proposicao->ano }}
                                                            </span>
                                                            <span class="text-muted fw-semibold text-muted d-block fs-7">
                                                                {{ Str::limit($proposicao->ementa, 40) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-dark fw-bold d-block fs-6">{{ $proposicao->autor->name ?? 'N/A' }}</span>
                                                    <span class="text-muted fw-semibold d-block fs-7">{{ $proposicao->created_at->format('d/m/Y') }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-light fw-bold">{{ $proposicao->tipo }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-light-primary fw-bold">{{ $proposicao->status }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge badge-light fw-bold">Detalhes</span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <!--end::Table body-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                                <!--end::Table container-->
                            @else
                                <div class="text-center py-10">
                                    <p class="text-muted">Não há proposições no sistema.</p>
                </div>
                            @endif
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Proposições Recentes-->
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-4 d-flex flex-column">
                    <!--begin::Estatísticas por Tipo-->
                    <div class="card flex-row-fluid mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Por Tipo</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Distribuição por tipo</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            @if($estatisticas_por_tipo->count() > 0)
                                <!--begin::Item list-->
                                <div class="d-flex flex-column gap-4">
                                    @foreach($estatisticas_por_tipo as $tipo)
                                    <!--begin::Item-->
                                    <div class="d-flex align-items-center">
                                        <!--begin::Symbol-->
                                        <div class="symbol symbol-40px me-4">
                                            <span class="symbol-label bg-light-primary">
                                                <i class="ki-duotone ki-document text-primary fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <!--end::Symbol-->
                                        <!--begin::Description-->
                                        <div class="d-flex flex-column flex-grow-1">
                                            <span class="text-gray-800 fw-bold fs-6">{{ $tipo->tipo }}</span>
                                            <span class="text-muted fw-semibold">{{ $tipo->total }} proposições</span>
                                        </div>
                                        <!--end::Description-->
                                        <!--begin::Progress-->
                                        <div class="text-end ms-3">
                                            <span class="badge badge-light-primary fw-bold">{{ $tipo->total }}</span>
                                        </div>
                                        <!--end::Progress-->
                                    </div>
                                    <!--end::Item-->
                                    @endforeach
                                </div>
                                <!--end::Item list-->
                            @else
                                <div class="text-center py-10">
                                    <p class="text-muted">Não há dados para exibir.</p>
                                </div>
                            @endif
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Estatísticas por Tipo-->

                    <!--begin::Estatísticas por Status-->
                    <div class="card flex-row-fluid mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Por Status</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Distribuição por status</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            @if($estatisticas_por_status->count() > 0)
                                <!--begin::Item list-->
                                <div class="d-flex flex-column gap-4">
                                    @foreach($estatisticas_por_status as $status)
                                    <!--begin::Item-->
                                    <div class="d-flex align-items-center">
                                        <!--begin::Symbol-->
                                        <div class="symbol symbol-40px me-4">
                                            <span class="symbol-label bg-light-success">
                                                <i class="ki-duotone ki-verify text-success fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <!--end::Symbol-->
                                        <!--begin::Description-->
                                        <div class="d-flex flex-column flex-grow-1">
                                            <span class="text-gray-800 fw-bold fs-6">{{ ucfirst(str_replace('_', ' ', $status->status)) }}</span>
                                            <span class="text-muted fw-semibold">{{ $status->total }} proposições</span>
                                        </div>
                                        <!--end::Description-->
                                        <!--begin::Progress-->
                                        <div class="text-end ms-3">
                                            <span class="badge badge-light-success fw-bold">{{ $status->total }}</span>
                                        </div>
                                        <!--end::Progress-->
                                    </div>
                                    <!--end::Item-->
                                    @endforeach
                                </div>
                                <!--end::Item list-->
                            @else
                                <div class="text-center py-10">
                                    <p class="text-muted">Não há dados para exibir.</p>
                                </div>
                            @endif
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Estatísticas por Status-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

            <!--begin::Ferramentas Administrativas Row-->
            <div class="row g-5 g-xl-8 mt-5 mt-xl-10">
                <!--begin::Col-->
                <div class="col-xl-4">
                    <!--begin::Database Administration Card-->
                    <div class="card card-flush h-100 mb-5 mb-xl-10">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px" style="background: linear-gradient(135deg, #1B84FF 0%, #0066CC 100%);">
                                <i class="fas fa-database text-white fs-2x"></i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-between pt-0">
                            <div class="mb-4">
                                <h3 class="text-gray-800 fw-bold fs-4 mb-2">Banco de Dados</h3>
                                <p class="text-muted fw-semibold fs-7 mb-3">
                                    Visualize e explore todas as tabelas do sistema
                                </p>
                                
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-table text-primary fs-6 me-2"></i>
                                    <span class="text-gray-700 fs-7">Listar todas as tabelas</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-eye text-success fs-6 me-2"></i>
                                    <span class="text-gray-700 fs-7">Visualizar dados das tabelas</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-search text-info fs-6 me-2"></i>
                                    <span class="text-gray-700 fs-7">Navegar pelos registros</span>
                                </div>
                            </div>
                            
                            <div>
                                <a href="{{ route('admin.database.index') }}" 
                                   class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-database me-2"></i>
                                    Administrar Banco
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--end::Database Administration Card-->
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-8">
                    <!--begin::System Tools-->
                    <div class="card mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">🛠️ Ferramentas do Sistema</span>
                                <span class="text-muted fw-semibold fs-7">Acesso rápido às funcionalidades administrativas</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center p-3 bg-light-primary rounded">
                                        <i class="fas fa-users text-primary fs-2x me-3"></i>
                                        <div>
                                            <h5 class="text-gray-800 mb-1">Usuários</h5>
                                            <p class="text-muted mb-0 fs-7">Gerenciar contas</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center p-3 bg-light-success rounded">
                                        <i class="fas fa-file-alt text-success fs-2x me-3"></i>
                                        <div>
                                            <h5 class="text-gray-800 mb-1">Templates</h5>
                                            <p class="text-muted mb-0 fs-7">Configurar modelos</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center p-3 bg-light-warning rounded">
                                        <i class="fas fa-cog text-warning fs-2x me-3"></i>
                                        <div>
                                            <h5 class="text-gray-800 mb-1">Configurações</h5>
                                            <p class="text-muted mb-0 fs-7">Parâmetros gerais</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::System Tools-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Ferramentas Administrativas Row-->

            <!--begin::Atividade do Sistema Row-->
            <div class="row g-5 g-xl-8 mt-5 mt-xl-10">
                <!--begin::Col-->
                <div class="col-xl-12">
                    <!--begin::Atividade do Sistema-->
                    <div class="card mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">📊 Atividade do Sistema</span>
                                <span class="text-muted fw-semibold fs-7">Últimos 7 dias</span>
                            </h3>
                            <div class="card-toolbar">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge badge-light-primary">Proposições</span>
                                    <span class="badge badge-light-info">Logins</span>
                                </div>
                            </div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            <div class="row g-5 g-xl-8">
                                @foreach($atividade_sistema as $dia)
                                <div class="col text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="fs-6 fw-bold text-gray-600 mb-2">{{ $dia->data }}</span>
                                        
                                        <div class="d-flex flex-column align-items-center mb-3">
                                            <div class="bg-primary rounded mb-1" style="width: 30px; height: {{ max(5, $dia->proposicoes * 10) }}px;"></div>
                                            <span class="fs-7 fw-semibold text-primary">{{ $dia->proposicoes }}</span>
                                        </div>
                                        
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="bg-info rounded mb-1" style="width: 30px; height: {{ max(5, $dia->logins * 2) }}px;"></div>
                                            <span class="fs-7 fw-semibold text-info">{{ $dia->logins }}</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Atividade do Sistema-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Atividade do Sistema Row-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection