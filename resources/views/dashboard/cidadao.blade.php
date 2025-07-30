@extends('components.layouts.app')

@section('title', 'Dashboard Cidadão')

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
                    Dashboard Cidadão
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
                                <i class="ki-duotone ki-document-text text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['propostas_enviadas'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">propostas</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Propostas Enviadas</span>
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
                                <i class="ki-duotone ki-abstract-26 text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['em_analise'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">em análise</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Em Análise</span>
                                <span class="badge badge-light-warning fs-8">{{ $estatisticas['propostas_enviadas'] > 0 ? round(($estatisticas['em_analise'] / $estatisticas['propostas_enviadas']) * 100) : 0 }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $estatisticas['propostas_enviadas'] > 0 ? round(($estatisticas['em_analise'] / $estatisticas['propostas_enviadas']) * 100) : 0 }}%"></div>
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
                                <i class="ki-duotone ki-verify text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['aprovadas'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">aprovadas</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Aprovadas</span>
                                <span class="badge badge-light-success fs-8">{{ $estatisticas['propostas_enviadas'] > 0 ? round(($estatisticas['aprovadas'] / $estatisticas['propostas_enviadas']) * 100) : 0 }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $estatisticas['propostas_enviadas'] > 0 ? round(($estatisticas['aprovadas'] / $estatisticas['propostas_enviadas']) * 100) : 0 }}%"></div>
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
                                <i class="ki-duotone ki-chart-simple-3 text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['votos_realizados'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">votos</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Votos Realizados</span>
                                <span class="badge badge-light-info fs-8">{{ $estatisticas['votos_realizados'] > 0 ? 100 : 0 }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $estatisticas['votos_realizados'] > 0 ? 100 : 0 }}%"></div>
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
                    <!--begin::Minhas Propostas-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Minhas Propostas</span>
                                <span class="text-muted fw-semibold fs-7">Propostas que você enviou</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            @if($minhas_propostas->count() > 0)
                                <!--begin::Items-->
                                <div class="d-flex flex-column gap-4">
                                    @foreach($minhas_propostas as $proposta)
                                    <!--begin::Item-->
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40px me-4">
                                            <span class="symbol-label bg-light-primary">
                                                <i class="ki-duotone ki-document text-primary fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column flex-grow-1">
                                            <span class="text-gray-800 fw-bold fs-6">{{ Str::limit($proposta->ementa, 40) }}</span>
                                            <span class="text-muted fw-semibold">{{ $proposta->created_at->format('d/m/Y') }}</span>
                                        </div>
                                        <span class="badge badge-light-primary fw-bold">{{ $proposta->status }}</span>
                                    </div>
                                    <!--end::Item-->
                                    @endforeach
                                </div>
                                <!--end::Items-->
                            @else
                                <div class="text-center py-10">
                                    <p class="text-muted">Você ainda não enviou nenhuma proposta.</p>
                                </div>
                            @endif
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Minhas Propostas-->
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-6">
                    <!--begin::Proposições Públicas-->
                    <div class="card card-xl-stretch mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Proposições Públicas</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Em tramitação</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            @if($proposicoes_publicas->count() > 0)
                                <!--begin::Items-->
                                <div class="d-flex flex-column gap-4">
                                    @foreach($proposicoes_publicas as $proposicao)
                                    <!--begin::Item-->
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40px me-4">
                                            <span class="symbol-label bg-light-success">
                                                <i class="ki-duotone ki-briefcase text-success fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column flex-grow-1">
                                            <span class="text-gray-800 fw-bold fs-6">{{ Str::limit($proposicao->ementa, 40) }}</span>
                                            <span class="text-muted fw-semibold">{{ $proposicao->autor->name ?? 'N/A' }}</span>
                                        </div>
                                        <span class="badge badge-light-success fw-bold">{{ $proposicao->status }}</span>
                                    </div>
                                    <!--end::Item-->
                                    @endforeach
                                </div>
                                <!--end::Items-->
                            @else
                                <div class="text-center py-10">
                                    <p class="text-muted">Não há proposições públicas disponíveis.</p>
                                </div>
                            @endif
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Proposições Públicas-->
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