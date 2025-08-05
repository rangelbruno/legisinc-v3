@extends('layouts.app')

@section('title', 'Relatório do Expediente')

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
<div class="d-flex flex-column flex-column-fluid">
    <!-- Toolbar -->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    📊 Relatório do Expediente
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('expediente.index') }}" class="text-muted text-hover-primary">Expediente</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Relatório</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-sm btn-success" onclick="exportarPDF()">
                    <i class="ki-duotone ki-file-down fs-4"></i>
                    Exportar PDF
                </button>
                <a href="{{ route('expediente.index') }}" class="btn btn-sm btn-light">
                    <i class="ki-duotone ki-arrow-left fs-4"></i>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!-- Filtros -->
            <div class="card card-flush mb-5">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="fw-bold m-0">Filtros do Relatório</h3>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('expediente.relatorio') }}" class="row g-5">
                        <div class="col-md-4">
                            <label class="form-label">Data Início</label>
                            <input type="date" name="data_inicio" class="form-control" value="{{ $dataInicio }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Data Fim</label>
                            <input type="date" name="data_fim" class="form-control" value="{{ $dataFim }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="ki-duotone ki-magnifier fs-4"></i>
                                Filtrar
                            </button>
                            <a href="{{ route('expediente.relatorio') }}" class="btn btn-light">
                                <i class="ki-duotone ki-arrows-circle fs-4"></i>
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-primary cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-chart-simple text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['total'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">proposições</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Total Protocoladas</span>
                                <span class="badge badge-light-primary fs-8">100%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-info cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-file-up text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['por_momento']['EXPEDIENTE'] ?? 0 }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">docs</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Expediente</span>
                                <span class="badge badge-light-info fs-8">{{ $estatisticas['total'] > 0 ? round((($estatisticas['por_momento']['EXPEDIENTE'] ?? 0) / $estatisticas['total']) * 100) : 0 }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $estatisticas['total'] > 0 ? round((($estatisticas['por_momento']['EXPEDIENTE'] ?? 0) / $estatisticas['total']) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-success cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-calendar text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['por_momento']['ORDEM_DO_DIA'] ?? 0 }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">itens</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Ordem do Dia</span>
                                <span class="badge badge-light-success fs-8">{{ $estatisticas['total'] > 0 ? round((($estatisticas['por_momento']['ORDEM_DO_DIA'] ?? 0) / $estatisticas['total']) * 100) : 0 }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $estatisticas['total'] > 0 ? round((($estatisticas['por_momento']['ORDEM_DO_DIA'] ?? 0) / $estatisticas['total']) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-warning cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-question text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['por_momento']['NAO_CLASSIFICADO'] ?? 0 }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">pendentes</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Não Classificadas</span>
                                <span class="badge badge-light-warning fs-8">{{ $estatisticas['total'] > 0 ? round((($estatisticas['por_momento']['NAO_CLASSIFICADO'] ?? 0) / $estatisticas['total']) * 100) : 0 }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $estatisticas['total'] > 0 ? round((($estatisticas['por_momento']['NAO_CLASSIFICADO'] ?? 0) / $estatisticas['total']) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-5 g-xl-10">
                <!-- Gráfico por Tipo -->
                <div class="col-xl-6">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-dark">Por Tipo de Proposição</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Distribuição por tipo</span>
                            </h3>
                        </div>
                        <div class="card-body pt-6">
                            @if($estatisticas['por_tipo']->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                        <thead>
                                            <tr class="fw-bold text-muted">
                                                <th>Tipo</th>
                                                <th class="text-end">Quantidade</th>
                                                <th class="text-end">%</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($estatisticas['por_tipo'] as $tipo => $quantidade)
                                            <tr>
                                                <td>
                                                    <span class="text-dark fw-bold fs-6">
                                                        {{ ucfirst(str_replace('_', ' ', $tipo)) }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="text-dark fw-bold fs-6">{{ $quantidade }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge badge-light-primary fs-8 fw-bold">
                                                        {{ $estatisticas['total'] > 0 ? round(($quantidade / $estatisticas['total']) * 100, 1) : 0 }}%
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-10">
                                    <i class="ki-duotone ki-chart-simple fs-4x text-muted mb-5"></i>
                                    <div class="fs-6 text-muted">Nenhum dado para exibir</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Gráfico por Autor -->
                <div class="col-xl-6">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-dark">Por Autor</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Proposições por parlamentar</span>
                            </h3>
                        </div>
                        <div class="card-body pt-6">
                            @if($estatisticas['por_autor']->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                        <thead>
                                            <tr class="fw-bold text-muted">
                                                <th>Autor</th>
                                                <th class="text-end">Quantidade</th>
                                                <th class="text-end">%</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($estatisticas['por_autor']->sortDesc()->take(10) as $autor => $quantidade)
                                            <tr>
                                                <td>
                                                    <span class="text-dark fw-bold fs-6">{{ $autor }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="text-dark fw-bold fs-6">{{ $quantidade }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge badge-light-success fs-8 fw-bold">
                                                        {{ $estatisticas['total'] > 0 ? round(($quantidade / $estatisticas['total']) * 100, 1) : 0 }}%
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-10">
                                    <i class="ki-duotone ki-profile-circle fs-4x text-muted mb-5"></i>
                                    <div class="fs-6 text-muted">Nenhum dado para exibir</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista detalhada -->
            @if($proposicoes->count() > 0)
            <div class="card card-flush mt-5">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Lista Detalhada</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">
                            {{ $proposicoes->count() }} {{ $proposicoes->count() === 1 ? 'proposição encontrada' : 'proposições encontradas' }}
                        </span>
                    </h3>
                </div>
                <div class="card-body pt-6">
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                            <thead>
                                <tr class="fw-bold text-muted">
                                    <th class="min-w-150px">Proposição</th>
                                    <th class="min-w-140px">Autor</th>
                                    <th class="min-w-100px">Momento</th>
                                    <th class="min-w-80px">Parecer</th>
                                    <th class="min-w-120px">Protocolo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proposicoes as $proposicao)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px me-5">
                                                <span class="symbol-label bg-light-{{ $proposicao->getCorMomentoSessao() }} text-{{ $proposicao->getCorMomentoSessao() }} fw-bold">
                                                    {{ strtoupper(substr($proposicao->tipo, 0, 2)) }}
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-start flex-column">
                                                <span class="text-dark fw-bold fs-6">{{ $proposicao->tipo_formatado }}</span>
                                                <span class="text-muted fw-semibold text-muted d-block fs-7">
                                                    {{ Str::limit($proposicao->ementa, 60) }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-bold d-block fs-6">{{ $proposicao->autor->name }}</span>
                                        <span class="text-muted fw-semibold fs-7">{{ $proposicao->autor->roles->first()->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $proposicao->getCorMomentoSessao() }} fs-8 fw-bold">
                                            {{ $proposicao->getMomentoSessaoFormatado() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($proposicao->tem_parecer)
                                            <span class="badge badge-success fs-8 fw-bold">Sim</span>
                                        @else
                                            <span class="badge badge-secondary fs-8 fw-bold">Não</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-dark fw-bold d-block fs-6">{{ $proposicao->numero_protocolo }}</span>
                                        <span class="text-muted fw-semibold fs-7">{{ $proposicao->data_protocolo->format('d/m/Y') }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>


@push('scripts')
<script>
function exportarPDF() {
    alert('Funcionalidade de exportação PDF será implementada em breve!');
}
</script>
@endpush
@endsection