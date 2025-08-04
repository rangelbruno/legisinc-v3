@extends('layouts.app')

@section('title', 'Detalhes da Proposição')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!-- Toolbar -->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    📄 {{ $proposicao->tipo_formatado }}
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
                    <li class="breadcrumb-item text-muted">Detalhes</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                @if($validacaoVotacao['pode_enviar'])
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modal_enviar_votacao">
                    <i class="ki-duotone ki-check-square fs-4"></i>
                    Enviar para Votação
                </button>
                @endif
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
            
            <div class="row g-5 g-xl-10">
                <!-- Informações principais -->
                <div class="col-xl-8">
                    <!-- Card da proposição -->
                    <div class="card card-flush mb-5">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold m-0">Informações da Proposição</h3>
                            </div>
                            <div class="card-toolbar">
                                <span class="badge badge-{{ $proposicao->getCorMomentoSessao() }} fs-7 fw-bold">
                                    {{ $proposicao->getMomentoSessaoFormatado() }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body pt-5">
                            <div class="row mb-7">
                                <label class="col-lg-4 fw-semibold text-muted">Tipo</label>
                                <div class="col-lg-8">
                                    <span class="fw-bold fs-6 text-gray-800">{{ $proposicao->tipo_formatado }}</span>
                                </div>
                            </div>
                            <div class="row mb-7">
                                <label class="col-lg-4 fw-semibold text-muted">Ementa</label>
                                <div class="col-lg-8">
                                    <span class="fw-semibold fs-6 text-gray-800">{{ $proposicao->ementa }}</span>
                                </div>
                            </div>
                            <div class="row mb-7">
                                <label class="col-lg-4 fw-semibold text-muted">Autor</label>
                                <div class="col-lg-8">
                                    <span class="fw-bold fs-6 text-gray-800">{{ $proposicao->autor->name }}</span>
                                    <span class="text-muted fs-7 d-block">{{ $proposicao->autor->roles->first()->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="row mb-7">
                                <label class="col-lg-4 fw-semibold text-muted">Status</label>
                                <div class="col-lg-8">
                                    <span class="badge badge-{{ $proposicao->getStatusColor() }} fs-7 fw-bold">
                                        {{ $proposicao->getStatusFormatado() }}
                                    </span>
                                </div>
                            </div>
                            <div class="row mb-7">
                                <label class="col-lg-4 fw-semibold text-muted">Protocolo</label>
                                <div class="col-lg-8">
                                    <span class="fw-bold fs-6 text-gray-800">{{ $proposicao->numero_protocolo }}</span>
                                    <span class="text-muted fs-7 d-block">{{ $proposicao->data_protocolo->format('d/m/Y H:i') }}</span>
                                    @if($proposicao->funcionarioProtocolo)
                                        <span class="text-muted fs-7 d-block">Por: {{ $proposicao->funcionarioProtocolo->name }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($proposicao->tem_parecer && $proposicao->parecer)
                            <div class="row mb-7">
                                <label class="col-lg-4 fw-semibold text-muted">Parecer Jurídico</label>
                                <div class="col-lg-8">
                                    <span class="badge badge-success fs-7 fw-bold">Disponível</span>
                                    <a href="#" class="btn btn-sm btn-light-primary ms-2">Ver Parecer</a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Conteúdo da proposição -->
                    @if($proposicao->conteudo)
                    <div class="card card-flush mb-5">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold m-0">Conteúdo</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="fs-6">
                                {!! nl2br(e($proposicao->conteudo)) !!}
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Histórico de tramitação -->
                    @if($tramitacao->count() > 0)
                    <div class="card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold m-0">Histórico de Tramitação</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @foreach($tramitacao as $log)
                                <div class="timeline-item">
                                    <div class="timeline-line w-40px"></div>
                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                        <div class="symbol-label bg-light">
                                            <i class="ki-duotone ki-abstract-26 fs-2 text-gray-500"></i>
                                        </div>
                                    </div>
                                    <div class="timeline-content mb-10 mt-n1">
                                        <div class="pe-3 mb-5">
                                            <div class="fs-5 fw-semibold mb-2">{{ $log->acao }}</div>
                                            @if($log->observacoes)
                                                <div class="d-flex align-items-center mt-1 fs-6">
                                                    <div class="text-muted me-2 fs-7">{{ $log->observacoes }}</div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="overflow-auto pb-5">
                                            <div class="d-flex align-items-center border border-dashed border-gray-300 rounded px-7 py-3 bg-light">
                                                <span class="text-gray-400 fw-semibold fs-8 me-2">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                                @if($log->user)
                                                    <span class="text-gray-400 fw-semibold fs-8">por {{ $log->user->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar com informações adicionais -->
                <div class="col-xl-4">
                    <!-- Status de votação -->
                    <div class="card card-flush mb-5">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold m-0">Status para Votação</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($validacaoVotacao['pode_enviar'])
                                <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-6">
                                    <i class="ki-duotone ki-check-square fs-2tx text-success me-4"></i>
                                    <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                        <div class="mb-3 mb-md-0 fw-semibold">
                                            <h4 class="text-gray-900 fw-bold">Pronta para Votação</h4>
                                            <div class="fs-6 text-gray-700 pe-7">
                                                Esta proposição pode ser enviada para votação.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                                    <i class="ki-duotone ki-information fs-2tx text-warning me-4"></i>
                                    <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                        <div class="mb-3 mb-md-0 fw-semibold">
                                            <h4 class="text-gray-900 fw-bold">Pendências</h4>
                                            <div class="fs-6 text-gray-700 pe-7">
                                                <ul class="mb-0">
                                                    @foreach($validacaoVotacao['erros'] as $erro)
                                                    <li>{{ $erro }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="separator my-5"></div>

                            <div class="d-flex flex-column">
                                <div class="fs-6 fw-bold text-gray-800 mb-2">Momento da Sessão:</div>
                                <div class="fs-7 text-muted mb-4">{{ $validacaoVotacao['descricao_momento'] }}</div>
                                
                                @if(empty($proposicao->momento_sessao) || $proposicao->momento_sessao === 'NAO_CLASSIFICADO')
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-info flex-fill" 
                                            onclick="classificarProposicao({{ $proposicao->id }}, 'EXPEDIENTE')">
                                        Expediente
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary flex-fill" 
                                            onclick="classificarProposicao({{ $proposicao->id }}, 'ORDEM_DO_DIA')">
                                        Ordem do Dia
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Documentos anexos -->
                    @if($proposicao->arquivo_path || $proposicao->arquivo_pdf_path)
                    <div class="card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold m-0">Documentos</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($proposicao->arquivo_path)
                            <div class="d-flex align-items-center mb-3">
                                <i class="ki-duotone ki-document fs-2 text-primary me-4"></i>
                                <div class="flex-grow-1">
                                    <a href="#" class="text-gray-800 text-hover-primary fs-6 fw-bold">Documento Original</a>
                                    <div class="text-muted fs-7">Arquivo da proposição</div>
                                </div>
                            </div>
                            @endif
                            @if($proposicao->arquivo_pdf_path)
                            <div class="d-flex align-items-center">
                                <i class="ki-duotone ki-file fs-2 text-danger me-4"></i>
                                <div class="flex-grow-1">
                                    <a href="#" class="text-gray-800 text-hover-primary fs-6 fw-bold">PDF</a>
                                    <div class="text-muted fs-7">Versão em PDF</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de envio para votação -->
@if($validacaoVotacao['pode_enviar'])
<div class="modal fade" id="modal_enviar_votacao" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <form method="POST" action="{{ route('expediente.enviar-votacao', $proposicao) }}">
                @csrf
                <div class="modal-header">
                    <h2 class="fw-bold">🗳️ Enviar para Votação</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Confirmação</span>
                        </label>
                        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6">
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <div class="fs-6 text-gray-700">
                                        Confirma o envio desta proposição para votação na <strong>{{ $proposicao->getMomentoSessaoFormatado() }}</strong>?
                                    </div>
                                    <div class="fs-7 text-muted mt-2">
                                        <strong>Proposição:</strong> {{ $proposicao->tipo_formatado }}<br>
                                        <strong>Ementa:</strong> {{ Str::limit($proposicao->ementa, 100) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="fs-6 fw-semibold mb-2">Observações (opcional)</label>
                        <textarea class="form-control form-control-solid" name="observacoes" rows="3" 
                                  placeholder="Observações sobre o envio para votação..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ki-duotone ki-check-square fs-4"></i>
                        Confirmar Envio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
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
</script>
@endpush
@endsection