@extends('components.layouts.app')

@section('title', 'Dashboard Protocolo')

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
                    Dashboard Protocolo
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
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['aguardando_protocolo'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">aguardando</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Aguardando Protocolo</span>
                                <span class="badge badge-light-warning fs-8">{{ $estatisticas['total_proposicoes'] > 0 ? round(($estatisticas['aguardando_protocolo'] / $estatisticas['total_proposicoes']) * 100) : 0 }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $estatisticas['total_proposicoes'] > 0 ? round(($estatisticas['aguardando_protocolo'] / $estatisticas['total_proposicoes']) * 100) : 0 }}%"></div>
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
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['protocoladas_hoje'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">hoje</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Protocoladas Hoje</span>
                                <span class="badge badge-light-success fs-8">100%</span>
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
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-info cursor-pointer">
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
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['protocoladas_mes'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">no mês</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Protocoladas no Mês</span>
                                <span class="badge badge-light-info fs-8">{{ $estatisticas['protocoladas_mes'] > 0 ? 100 : 0 }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $estatisticas['protocoladas_mes'] > 0 ? 100 : 0 }}%"></div>
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
                                <i class="ki-duotone ki-user-square text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['por_funcionario_mes'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">minhas</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Minhas do Mês</span>
                                <span class="badge badge-light-primary fs-8">{{ $estatisticas['protocoladas_mes'] > 0 ? round(($estatisticas['por_funcionario_mes'] / $estatisticas['protocoladas_mes']) * 100) : 0 }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $estatisticas['protocoladas_mes'] > 0 ? round(($estatisticas['por_funcionario_mes'] / $estatisticas['protocoladas_mes']) * 100) : 0 }}%"></div>
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
                <div class="col-xl-8">
                    <!--begin::Proposições para Protocolo-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Proposições para Protocolo</span>
                                <span class="text-muted fw-semibold fs-7">Aguardando numeração</span>
                            </h3>
                            <div class="card-toolbar">
                                <a href="{{ route('proposicoes.protocolar') }}" class="btn btn-sm btn-light btn-active-primary">
                                    Ver Todas
                                </a>
                            </div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            @if($proposicoes_para_protocolo->count() > 0)
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
                                                <th class="min-w-100px">Data Assinatura</th>
                                                <th class="min-w-100px text-end">Ações</th>
                                            </tr>
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody>
                                            @foreach($proposicoes_para_protocolo as $proposicao)
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
                                                </td>
                                                <td>
                                                    <span class="badge badge-light fw-bold">{{ $proposicao->tipo }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted fw-semibold">{{ $proposicao->data_assinatura ? $proposicao->data_assinatura->format('d/m/Y') : 'N/A' }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('proposicoes.protocolar.show', $proposicao) }}" 
                                                       class="btn btn-sm btn-light-primary">
                                                        Protocolar
                                                    </a>
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
                                    <p class="text-muted">Não há proposições aguardando protocolo.</p>
                                </div>
                            @endif
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Proposições para Protocolo-->
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-4">
                    <!--begin::Protocolos Recentes-->
                    <div class="card card-xl-stretch mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Meus Protocolos Recentes</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Últimos protocolados</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            @if($protocolos_recentes->count() > 0)
                                <!--begin::Item list-->
                                <div class="d-flex flex-column gap-5">
                                    @foreach($protocolos_recentes as $protocolo)
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
                                            <span class="text-gray-800 fw-bold fs-6">
                                                {{ $protocolo->numero_protocolo ?? $protocolo->numero }}/{{ $protocolo->ano }}
                                            </span>
                                            <span class="text-muted fw-semibold">{{ $protocolo->data_protocolo ? $protocolo->data_protocolo->format('d/m/Y H:i') : 'N/A' }}</span>
                                        </div>
                                        <!--end::Description-->
                                    </div>
                                    <!--end::Item-->
                                    @endforeach
                                </div>
                                <!--end::Item list-->
                            @else
                                <div class="text-center py-10">
                                    <p class="text-muted">Nenhum protocolo realizado recentemente.</p>
                                </div>
                            @endif
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Protocolos Recentes-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

            <!--begin::Row - Performance e Alertas-->
            <div class="row g-5 g-xl-8 mt-5 mt-xl-10">
                <!--begin::Col-->
                <div class="col-xl-4">
                    <!--begin::Performance Card-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Performance Protocolo</span>
                                <span class="text-muted fw-semibold fs-7">Métricas operacionais</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            <div class="d-flex flex-column gap-4">
                                <!--begin::Item-->
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-timer text-primary fs-2 me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold fs-6">Tempo Médio</span>
                                            <span class="text-muted fs-7">Assinatura → Protocolo</span>
                                        </div>
                                    </div>
                                    <span class="badge badge-light-primary fw-bold">{{ $estatisticas['tempo_medio_protocolo'] }} min</span>
                                </div>
                                <!--end::Item-->
                                
                                <!--begin::Item-->
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-chart-line-up text-success fs-2 me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold fs-6">Eficiência</span>
                                            <span class="text-muted fs-7">Protocolos por hora</span>
                                        </div>
                                    </div>
                                    <span class="badge badge-light-success fw-bold">{{ $estatisticas['eficiencia_protocolo'] }}/h</span>
                                </div>
                                <!--end::Item-->
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Performance Card-->
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-8">
                    <!--begin::Alertas Operacionais-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Alertas Operacionais</span>
                                <span class="text-muted fw-semibold fs-7">Situações que requerem atenção</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            @if($alertas_protocolo->count() > 0)
                                <div class="d-flex flex-column gap-4">
                                    @foreach($alertas_protocolo as $alerta)
                                    <!--begin::Alert Item-->
                                    <div class="d-flex align-items-center p-4 rounded bg-light-{{ $alerta->tipo }}">
                                        <div class="symbol symbol-40px me-4">
                                            <span class="symbol-label bg-{{ $alerta->tipo }}">
                                                @if($alerta->tipo == 'warning')
                                                <i class="ki-duotone ki-warning-2 text-white fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                                @else
                                                <i class="ki-duotone ki-information text-white fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold fs-6 text-gray-800">{{ $alerta->titulo }}</span>
                                                <span class="badge badge-{{ $alerta->tipo }} fw-bold">{{ $alerta->count }}</span>
                                            </div>
                                            <span class="text-muted fs-7">{{ $alerta->descricao }}</span>
                                        </div>
                                    </div>
                                    <!--end::Alert Item-->
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-10">
                                    <i class="ki-duotone ki-check-circle text-success fs-3x mb-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <p class="text-muted">Nenhum alerta no momento. Tudo funcionando normalmente!</p>
                                </div>
                            @endif
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Alertas Operacionais-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

            <!--begin::Row - Controle de Numeração-->
            <div class="row g-5 g-xl-8 mt-5 mt-xl-10">
                <!--begin::Col-->
                <div class="col-xl-12">
                    <!--begin::Controle de Numeração-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Controle de Numeração</span>
                                <span class="text-muted fw-semibold fs-7">Próximos números por tipo de proposição</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            <div class="row g-4">
                                @foreach($numeracao_tipos as $numeracao)
                                <!--begin::Numeração Item-->
                                <div class="col-xl-3 col-md-6">
                                    @php
                                    $cardClass = match($numeracao->sigla) {
                                        'PL' => 'dashboard-card-primary',
                                        'MOC' => 'dashboard-card-warning', 
                                        'REQ' => 'dashboard-card-info',
                                        'IND' => 'dashboard-card-success',
                                        default => 'dashboard-card-primary'
                                    };
                                    @endphp
                                    <div class="card card-flush h-100 {{ $cardClass }} cursor-pointer">
                                        <div class="card-body d-flex flex-column justify-content-center text-center py-8">
                                            <div class="d-flex align-items-center justify-content-center mb-4">
                                                <div class="d-flex flex-center rounded-circle h-80px w-80px bg-white bg-opacity-20">
                                                    <span class="fw-bold text-white fs-2x">{{ $numeracao->sigla }}</span>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold fs-5 text-white mb-2">{{ $numeracao->tipo }}</span>
                                                <span class="text-white opacity-75 fs-7 mb-4">Próximo número</span>
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <span class="fs-3x fw-bold text-white me-2">{{ str_pad($numeracao->proximo, 3, '0', STR_PAD_LEFT) }}</span>
                                                    <span class="fs-3 fw-semibold text-white opacity-75">/{{ $numeracao->ano }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Numeração Item-->
                                @endforeach
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Controle de Numeração-->
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