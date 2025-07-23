@extends('components.layouts.app')

@section('title', $title ?? 'Estatísticas dos Partidos')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Estatísticas dos Partidos
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
                    <li class="breadcrumb-item text-muted">Estatísticas</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('partidos.index') }}" class="btn btn-sm fw-bold btn-light">
                    <i class="ki-duotone ki-arrow-left fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Voltar
                </a>
                <a href="{{ route('partidos.export.csv') }}" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-exit-up fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Exportar CSV
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Row-->
            <div class="row gy-5 g-xl-8">
                <!--begin::Col-->
                <div class="col-xl-4">
                    <!--begin::Statistics Card-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Resumo Geral</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Dados consolidados dos partidos</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-5">
                            <!--begin::Item-->
                            <div class="d-flex align-items-center mb-7">
                                <span class="fw-semibold fs-5 text-gray-800 flex-grow-1">Total de Partidos:</span>
                                <span class="badge badge-light-primary fs-6">{{ $estatisticas['total_partidos'] }}</span>
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="d-flex align-items-center mb-7">
                                <span class="fw-semibold fs-5 text-gray-800 flex-grow-1">Partidos Ativos:</span>
                                <span class="badge badge-light-success fs-6">{{ $estatisticas['partidos_ativos'] }}</span>
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="d-flex align-items-center mb-7">
                                <span class="fw-semibold fs-5 text-gray-800 flex-grow-1">Partidos Inativos:</span>
                                <span class="badge badge-light-danger fs-6">{{ $estatisticas['partidos_inativos'] }}</span>
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="d-flex align-items-center mb-7">
                                <span class="fw-semibold fs-5 text-gray-800 flex-grow-1">Total de Parlamentares:</span>
                                <span class="badge badge-light-info fs-6">{{ $estatisticas['total_parlamentares'] }}</span>
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="d-flex align-items-center mb-7">
                                <span class="fw-semibold fs-5 text-gray-800 flex-grow-1">Partidos sem Parlamentares:</span>
                                <span class="badge badge-light-warning fs-6">{{ $estatisticas['partidos_sem_parlamentares'] }}</span>
                            </div>
                            <!--end::Item-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Statistics Card-->
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-8">
                    <!--begin::Maior Partido Card-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Partido com Mais Parlamentares</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Maior representação</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-5">
                            @if($estatisticas['partido_mais_parlamentares'])
                                <div class="d-flex align-items-center mb-5">
                                    <div class="symbol symbol-circle symbol-60px overflow-hidden me-4">
                                        <div class="symbol-label fs-2 bg-light-primary text-primary">
                                            {{ substr($estatisticas['partido_mais_parlamentares']->sigla, 0, 2) }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-gray-800 fw-bold fs-4">{{ $estatisticas['partido_mais_parlamentares']->sigla }}</span>
                                            <span class="badge badge-light-primary fs-5">{{ $estatisticas['partido_mais_parlamentares']->total_parlamentares }} parlamentares</span>
                                        </div>
                                        <span class="text-gray-600 fw-semibold fs-6">{{ $estatisticas['partido_mais_parlamentares']->nome }}</span>
                                        @if($estatisticas['partido_mais_parlamentares']->presidente)
                                            <div class="text-muted fs-7 mt-1">Presidente: {{ $estatisticas['partido_mais_parlamentares']->presidente }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="separator separator-dashed my-5"></div>
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('partidos.show', $estatisticas['partido_mais_parlamentares']->id) }}" class="btn btn-light-primary">
                                        Ver Detalhes
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-10">
                                    <span class="text-gray-400 fs-5">Nenhum partido com parlamentares encontrado</span>
                                </div>
                            @endif
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Maior Partido Card-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

            @if(!empty($estatisticas['por_decada_fundacao']))
            <!--begin::Row-->
            <div class="row gy-5 g-xl-8 mt-5">
                <!--begin::Col-->
                <div class="col-xl-12">
                    <!--begin::Fundação por Década Card-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Partidos por Década de Fundação</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Distribuição histórica</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-5">
                            <div class="row">
                                @foreach($estatisticas['por_decada_fundacao'] as $decada => $quantidade)
                                <div class="col-md-3 col-sm-6 mb-7">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-45px me-5">
                                            <span class="symbol-label bg-light-info text-info">
                                                <i class="ki-duotone ki-calendar fs-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800 fw-bold fs-5">{{ $decada }}s</span>
                                            <span class="text-gray-600 fw-semibold fs-6">{{ $quantidade }} partido(s)</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Fundação por Década Card-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
            @endif

            <!--begin::Row-->
            <div class="row gy-5 g-xl-8 mt-5">
                <!--begin::Col-->
                <div class="col-xl-12">
                    <!--begin::Distribution Card-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Distribuição de Status</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Visão geral do status dos partidos</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-5">
                            <div class="row">
                                <!--begin::Item-->
                                <div class="col-md-6 mb-7">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-50px me-5">
                                            <span class="symbol-label bg-light-success">
                                                <i class="ki-duotone ki-check-circle text-success fs-2x">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column flex-grow-1">
                                            <span class="text-gray-800 fw-bold fs-5 mb-1">Partidos Ativos</span>
                                            <div class="progress h-5px bg-light-success mb-2">
                                                @php
                                                    $percentualAtivos = $estatisticas['total_partidos'] > 0 ? 
                                                        ($estatisticas['partidos_ativos'] / $estatisticas['total_partidos']) * 100 : 0;
                                                @endphp
                                                <div class="progress-bar bg-success" style="width: {{ $percentualAtivos }}%"></div>
                                            </div>
                                            <span class="text-gray-600 fw-semibold fs-6">{{ $estatisticas['partidos_ativos'] }} de {{ $estatisticas['total_partidos'] }} ({{ round($percentualAtivos) }}%)</span>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Item-->
                                
                                <!--begin::Item-->
                                <div class="col-md-6 mb-7">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-50px me-5">
                                            <span class="symbol-label bg-light-danger">
                                                <i class="ki-duotone ki-cross-circle text-danger fs-2x">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column flex-grow-1">
                                            <span class="text-gray-800 fw-bold fs-5 mb-1">Partidos Inativos</span>
                                            <div class="progress h-5px bg-light-danger mb-2">
                                                @php
                                                    $percentualInativos = $estatisticas['total_partidos'] > 0 ? 
                                                        ($estatisticas['partidos_inativos'] / $estatisticas['total_partidos']) * 100 : 0;
                                                @endphp
                                                <div class="progress-bar bg-danger" style="width: {{ $percentualInativos }}%"></div>
                                            </div>
                                            <span class="text-gray-600 fw-semibold fs-6">{{ $estatisticas['partidos_inativos'] }} de {{ $estatisticas['total_partidos'] }} ({{ round($percentualInativos) }}%)</span>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Item-->
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Distribution Card-->
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