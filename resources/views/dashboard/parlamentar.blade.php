@extends('components.layouts.app')

@section('title', 'Dashboard Parlamentar')

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
                    Dashboard Parlamentar
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
                    <a href="{{ route('proposicoes.minhas-proposicoes') }}" class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-primary cursor-pointer">
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
                                <span class="badge badge-light-primary fs-8">{{ $estatisticas['total_proposicoes'] > 0 ? round(($estatisticas['em_elaboracao'] / $estatisticas['total_proposicoes']) * 100) : 0 }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $estatisticas['total_proposicoes'] > 0 ? round(($estatisticas['em_elaboracao'] / $estatisticas['total_proposicoes']) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </a>
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-3">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-warning cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-verify text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['aguardando_assinatura'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">aguardando</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Aguardando Assinatura</span>
                                <span class="badge badge-light-warning fs-8">{{ $estatisticas['total_proposicoes'] > 0 ? round(($estatisticas['aguardando_assinatura'] / $estatisticas['total_proposicoes']) * 100) : 0 }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $estatisticas['total_proposicoes'] > 0 ? round(($estatisticas['aguardando_assinatura'] / $estatisticas['total_proposicoes']) * 100) : 0 }}%"></div>
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
                                <i class="ki-duotone ki-information text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['devolvidas_correcao'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">devolvidas</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Devolvidas p/ Correção</span>
                                <span class="badge badge-light-info fs-8">{{ $estatisticas['total_proposicoes'] > 0 ? round(($estatisticas['devolvidas_correcao'] / $estatisticas['total_proposicoes']) * 100) : 0 }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $estatisticas['total_proposicoes'] > 0 ? round(($estatisticas['devolvidas_correcao'] / $estatisticas['total_proposicoes']) * 100) : 0 }}%"></div>
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

            <!--begin::Row-->
            <div class="row g-5 g-xl-8 mt-5 mt-xl-10">
                <!--begin::Col-->
                <div class="col-xl-6">
                    <!--begin::Proposições Urgentes-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Proposições Urgentes</span>
                                <span class="text-muted fw-semibold fs-7">Requerem ação imediata</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            @if($proposicoes_urgentes->count() > 0)
                                <!--begin::Table container-->
                                <div class="table-responsive">
                                    <!--begin::Table-->
                                    <table class="table align-middle gs-0 gy-4">
                                        <!--begin::Table body-->
                                        <tbody>
                                            @foreach($proposicoes_urgentes as $proposicao)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="symbol symbol-45px me-5">
                                                            @if($proposicao->status == 'aprovado_assinatura')
                                                                <span class="symbol-label bg-light-warning">
                                                                    <i class="ki-duotone ki-verify fs-2 text-warning">
                                                                        <span class="path1"></span>
                                                                        <span class="path2"></span>
                                                                    </i>
                                                                </span>
                                                            @else
                                                                <span class="symbol-label bg-light-danger">
                                                                    <i class="ki-duotone ki-information fs-2 text-danger">
                                                                        <span class="path1"></span>
                                                                        <span class="path2"></span>
                                                                        <span class="path3"></span>
                                                                    </i>
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="d-flex justify-content-start flex-column">
                                                            <span class="text-dark fw-bold mb-1 fs-6">
                                                                {{ $proposicao->tipo }} {{ $proposicao->numero }}/{{ $proposicao->ano }}
                                                            </span>
                                                            <span class="text-muted fw-semibold text-muted d-block fs-7">
                                                                {{ Str::limit($proposicao->titulo, 50) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($proposicao->status == 'aprovado_assinatura')
                                                        <span class="badge badge-light-warning fw-bold">Aguardando Assinatura</span>
                                                    @else
                                                        <span class="badge badge-light-danger fw-bold">Correção Necessária</span>
                                                    @endif
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
                                    <p class="text-muted">Não há proposições urgentes no momento.</p>
                                </div>
                            @endif
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Proposições Urgentes-->
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-6">
                    <!--begin::Proposições Recentes-->
                    <div class="card card-xl-stretch mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Proposições Recentes</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Últimas atualizações</span>
                            </h3>
                            <div class="card-toolbar">
                                <a href="{{ route('proposicoes.minhas-proposicoes') }}" class="btn btn-sm btn-light btn-active-primary">
                                    <i class="ki-duotone ki-plus fs-2"></i>Minhas Proposições
                                </a>
                            </div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            @if($proposicoes_recentes->count() > 0)
                                <!--begin::Timeline-->
                                <div class="timeline-label">
                                    @foreach($proposicoes_recentes as $proposicao)
                                    <!--begin::Item-->
                                    <div class="timeline-item">
                                        <!--begin::Label-->
                                        <div class="timeline-label fw-bold text-gray-800 fs-6">
                                            {{ $proposicao->updated_at->format('d/m') }}
                                        </div>
                                        <!--end::Label-->
                                        <!--begin::Badge-->
                                        <div class="timeline-badge">
                                            <i class="fa fa-genderless text-{{ $proposicao->getStatusColor() }} fs-1"></i>
                                        </div>
                                        <!--end::Badge-->
                                        <!--begin::Content-->
                                        <div class="timeline-content d-flex">
                                            <span class="fw-bold text-gray-800 ps-3">
                                                <span class="text-dark">
                                                    {{ $proposicao->numero }}/{{ $proposicao->ano }}
                                                </span>
                                                - {{ Str::limit($proposicao->titulo, 40) }}
                                            </span>
                                        </div>
                                        <!--end::Content-->
                                    </div>
                                    <!--end::Item-->
                                    @endforeach
                                </div>
                                <!--end::Timeline-->
                            @else
                                <div class="text-center py-10">
                                    <p class="text-muted">Não há proposições recentes.</p>
                                </div>
                            @endif
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Proposições Recentes-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

            <!--begin::Row-->
            <div class="row g-5 g-xl-8 mt-5 mt-xl-10">
                <!--begin::Col-->
                <div class="col-xl-12">
                    <!--begin::Resumo-->
                    <div class="card mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Resumo das Proposições</span>
                                <span class="text-muted fw-semibold fs-7">Total de {{ $estatisticas['total_proposicoes'] }} proposições</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            <div class="row g-5 g-xl-8">
                                <div class="col text-center">
                                    <div class="fs-2qx fw-bold text-gray-800 mb-1">{{ $estatisticas['total_proposicoes'] }}</div>
                                    <div class="fs-6 fw-semibold text-gray-400">Total de Proposições</div>
                                </div>
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Resumo-->
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