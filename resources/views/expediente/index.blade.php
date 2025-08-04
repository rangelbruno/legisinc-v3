@extends('layouts.app')

@section('title', 'Painel do Expediente')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!-- Toolbar -->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    📋 Painel do Expediente
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Expediente</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modal_estatisticas">
                    <i class="ki-duotone ki-chart-simple fs-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                    Estatísticas
                </button>
                <button type="button" class="btn btn-sm btn-warning" onclick="reclassificarTodas()">
                    <i class="ki-duotone ki-arrows-circle fs-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Reclassificar Todas
                </button>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!-- Cards de Estatísticas -->
            <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50px" style="background-color: #F1416C; background-image:url('assets/media/patterns/vector-1.png')">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <span class="fs-2hx fw-bold text-white me-2 lh-1">{{ $estatisticas['total'] }}</span>
                                </div>
                                <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Total Protocoladas</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50px" style="background-color: #7239EA; background-image:url('assets/media/patterns/vector-1.png')">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <span class="fs-2hx fw-bold text-white me-2 lh-1">{{ $estatisticas['expediente'] }}</span>
                                </div>
                                <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Expediente</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50px" style="background-color: #17C653; background-image:url('assets/media/patterns/vector-1.png')">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <span class="fs-2hx fw-bold text-white me-2 lh-1">{{ $estatisticas['ordem_dia'] }}</span>
                                </div>
                                <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Ordem do Dia</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50px" style="background-color: #FFC700; background-image:url('assets/media/patterns/vector-1.png')">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <span class="fs-2hx fw-bold text-white me-2 lh-1">{{ $estatisticas['nao_classificado'] }}</span>
                                </div>
                                <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Não Classificadas</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alertas para não classificadas -->
            @if($proposicoesNaoClassificadas->count() > 0)
            <div class="alert alert-warning d-flex align-items-center p-5 mb-10">
                <i class="ki-duotone ki-shield-tick fs-2hx text-warning me-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-warning">Atenção!</h4>
                    <span>Existem {{ $proposicoesNaoClassificadas->count() }} proposições que precisam ser classificadas quanto ao momento da sessão.</span>
                </div>
            </div>
            @endif

            <div class="row g-5 g-xl-10">
                <!-- Proposições não classificadas -->
                @if($proposicoesNaoClassificadas->count() > 0)
                <div class="col-xl-12 mb-5 mb-xl-10">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-dark">
                                    ⚠️ Proposições Não Classificadas
                                </span>
                                <span class="text-muted mt-1 fw-semibold fs-7">
                                    Necessitam definição de momento da sessão
                                </span>
                            </h3>
                        </div>
                        <div class="card-body pt-6">
                            @include('expediente.partials.lista-nao-classificadas', ['proposicoes' => $proposicoesNaoClassificadas])
                        </div>
                    </div>
                </div>
                @endif

                <!-- Proposições do Expediente -->
                <div class="col-xl-6 mb-5 mb-xl-10">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-dark">
                                    📋 Expediente
                                </span>
                                <span class="text-muted mt-1 fw-semibold fs-7">
                                    {{ $regrasExpediente['observacoes'] }}
                                </span>
                            </h3>
                            <div class="card-toolbar">
                                <span class="badge badge-info fs-8 fw-bold">{{ $proposicoesExpediente->count() }} proposições</span>
                            </div>
                        </div>
                        <div class="card-body pt-6">
                            @include('expediente.partials.lista-expediente', ['proposicoes' => $proposicoesExpediente, 'regras' => $regrasExpediente])
                        </div>
                    </div>
                </div>

                <!-- Proposições da Ordem do Dia -->
                <div class="col-xl-6 mb-5 mb-xl-10">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-dark">
                                    ⚖️ Ordem do Dia
                                </span>
                                <span class="text-muted mt-1 fw-semibold fs-7">
                                    {{ $regrasOrdemDia['observacoes'] }}
                                </span>
                            </h3>
                            <div class="card-toolbar">
                                <span class="badge badge-primary fs-8 fw-bold">{{ $proposicoesOrdemDia->count() }} proposições</span>
                            </div>
                        </div>
                        <div class="card-body pt-6">
                            @include('expediente.partials.lista-ordem-dia', ['proposicoes' => $proposicoesOrdemDia, 'regras' => $regrasOrdemDia])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Estatísticas -->
<div class="modal fade" id="modal_estatisticas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">📊 Estatísticas do Expediente</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <div class="row g-5">
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-50px me-5">
                                <span class="symbol-label bg-light-info text-info fw-bold">E</span>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="fw-bold fs-6">Expediente</span>
                                <span class="fs-7 text-muted">{{ $estatisticas['expediente'] }} proposições ({{ $estatisticas['percentual_expediente'] }}%)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-50px me-5">
                                <span class="symbol-label bg-light-primary text-primary fw-bold">O</span>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="fw-bold fs-6">Ordem do Dia</span>
                                <span class="fs-7 text-muted">{{ $estatisticas['ordem_dia'] }} proposições ({{ $estatisticas['percentual_ordem_dia'] }}%)</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="separator separator-dashed my-7"></div>
                
                <div class="d-flex align-items-center mb-5">
                    <div class="symbol symbol-50px me-5">
                        <span class="symbol-label bg-light-success text-success fw-bold">T</span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="fw-bold fs-6">Total Protocoladas</span>
                        <span class="fs-7 text-muted">{{ $estatisticas['total'] }} proposições no sistema</span>
                    </div>
                </div>
                
                @if($estatisticas['nao_classificado'] > 0)
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-5">
                        <span class="symbol-label bg-light-warning text-warning fw-bold">?</span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="fw-bold fs-6">Não Classificadas</span>
                        <span class="fs-7 text-muted">{{ $estatisticas['nao_classificado'] }} proposições precisam de atenção</span>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function reclassificarTodas() {
    if (confirm('Deseja reclassificar automaticamente todas as proposições não classificadas?')) {
        window.location.href = '{{ route("expediente.reclassificar") }}';
    }
}

function classificarProposicao(id, momento) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/expediente/proposicoes/${id}/classificar`;
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    
    const momentoInput = document.createElement('input');
    momentoInput.type = 'hidden';
    momentoInput.name = 'momento_sessao';
    momentoInput.value = momento;
    
    form.appendChild(csrf);
    form.appendChild(momentoInput);
    document.body.appendChild(form);
    form.submit();
}

function enviarParaVotacao(id) {
    if (confirm('Deseja enviar esta proposição para votação?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/expediente/proposicoes/${id}/enviar-votacao`;
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection