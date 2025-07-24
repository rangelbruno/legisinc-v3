@extends('layouts.app')

@section('title', 'Status da Proposição')

@section('content')
<div class="container">
    <!--begin::Toolbar-->
    <div class="toolbar mb-5 mb-lg-7" id="kt_toolbar">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                <i class="ki-duotone ki-chart-line-up fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Status da Proposição #{{ $proposicao->id }}
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('proposicoes.minhas-proposicoes') }}" class="text-muted text-hover-primary">Minhas Proposições</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-gray-900">Status Tramitação</li>
            </ul>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Row-->
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <!-- Progress Bar do Fluxo -->
            <!--begin::Card-->
            <div class="card card-xl-stretch mb-5 mb-xl-8">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">
                            <i class="ki-duotone ki-route fs-2 text-primary me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Fluxo de Tramitação
                        </span>
                        <span class="text-muted fw-semibold fs-7">Acompanhe o andamento da proposição</span>
                    </h3>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body py-3">
                    
                    <!--begin::Timeline-->
                    <div class="timeline-label">
                        <div class="timeline-item">
                            <div class="timeline-label fw-bold text-gray-800 fs-6">{{ $proposicao->status === 'rascunho' ? 'Atual' : 'Concluído' }}</div>
                            <div class="timeline-badge">
                                <i class="ki-duotone ki-pencil {{ $proposicao->status === 'rascunho' ? 'text-warning' : 'text-success' }} fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <div class="timeline-content d-flex">
                                <span class="fw-bold {{ $proposicao->status === 'rascunho' ? 'text-warning' : 'text-gray-800' }} ps-3">Rascunho</span>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-label fw-bold text-gray-800 fs-6">{{ $proposicao->status === 'enviado_legislativo' ? 'Atual' : ($proposicao->status_order > 1 ? 'Concluído' : '') }}</div>
                            <div class="timeline-badge">
                                <i class="ki-duotone ki-paper-plane {{ $proposicao->status === 'enviado_legislativo' ? 'text-info' : ($proposicao->status_order > 1 ? 'text-success' : 'text-muted') }} fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <div class="timeline-content d-flex">
                                <span class="fw-bold {{ $proposicao->status === 'enviado_legislativo' ? 'text-info' : ($proposicao->status_order > 1 ? 'text-gray-800' : 'text-muted') }} ps-3">Enviado ao Legislativo</span>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-label fw-bold text-gray-800 fs-6">{{ $proposicao->status === 'retornado_legislativo' ? 'Atual' : ($proposicao->status_order > 2 ? 'Concluído' : '') }}</div>
                            <div class="timeline-badge">
                                <i class="ki-duotone ki-arrow-left {{ $proposicao->status === 'retornado_legislativo' ? 'text-success' : ($proposicao->status_order > 2 ? 'text-success' : 'text-muted') }} fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <div class="timeline-content d-flex">
                                <span class="fw-bold {{ $proposicao->status === 'retornado_legislativo' ? 'text-success' : ($proposicao->status_order > 2 ? 'text-gray-800' : 'text-muted') }} ps-3">Retornado</span>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-label fw-bold text-gray-800 fs-6">{{ $proposicao->status === 'assinado' ? 'Atual' : ($proposicao->status_order > 3 ? 'Concluído' : '') }}</div>
                            <div class="timeline-badge">
                                <i class="ki-duotone ki-signature {{ $proposicao->status === 'assinado' ? 'text-primary' : ($proposicao->status_order > 3 ? 'text-success' : 'text-muted') }} fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <div class="timeline-content d-flex">
                                <span class="fw-bold {{ $proposicao->status === 'assinado' ? 'text-primary' : ($proposicao->status_order > 3 ? 'text-gray-800' : 'text-muted') }} ps-3">Assinado</span>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-label fw-bold text-gray-800 fs-6">{{ $proposicao->status === 'protocolado' ? 'Atual' : '' }}</div>
                            <div class="timeline-badge">
                                <i class="ki-duotone ki-check-circle {{ $proposicao->status === 'protocolado' ? 'text-dark' : 'text-muted' }} fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <div class="timeline-content d-flex">
                                <span class="fw-bold {{ $proposicao->status === 'protocolado' ? 'text-dark' : 'text-muted' }} ps-3">Protocolado</span>
                            </div>
                        </div>
                    </div>
                    <!--end::Timeline-->
                </div>
                <!--end::Body-->
            </div>
            <!--end::Card-->

            <!-- Status Atual -->
            <!--begin::Card-->
            <div class="card card-xl-stretch mb-5 mb-xl-8">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Status Atual</span>
                        <span class="text-muted fw-semibold fs-7">Situação atual da proposição</span>
                    </h3>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                @switch($proposicao->status)
                                    @case('rascunho')
                                        <span class="badge badge-light-warning fs-6 me-3 py-2 px-3">
                                            <i class="ki-duotone ki-pencil fs-6 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Rascunho
                                        </span>
                                        <span class="text-muted fw-semibold fs-6">Proposição ainda em elaboração</span>
                                        @break
                                    @case('enviado_legislativo')
                                        <span class="badge badge-light-info fs-6 me-3 py-2 px-3">
                                            <i class="ki-duotone ki-paper-plane fs-6 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Aguardando Retorno do Legislativo
                                        </span>
                                        <span class="text-muted fw-semibold fs-6">Proposição em análise legislativa</span>
                                        @break
                                    @case('retornado_legislativo')
                                        <span class="badge badge-light-success fs-6 me-3 py-2 px-3">
                                            <i class="ki-duotone ki-check fs-6 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Aprovado pelo Legislativo
                                        </span>
                                        <span class="text-muted fw-semibold fs-6">Aguardando assinatura</span>
                                        @break
                                    @case('assinado')
                                        <span class="badge badge-light-primary fs-6 me-3 py-2 px-3">
                                            <i class="ki-duotone ki-signature fs-6 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Documento Assinado
                                        </span>
                                        <span class="text-muted fw-semibold fs-6">Pronto para protocolo</span>
                                        @break
                                    @case('protocolado')
                                        <span class="badge badge-light-dark fs-6 me-3 py-2 px-3">
                                            <i class="ki-duotone ki-check-circle fs-6 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Protocolado
                                        </span>
                                        <span class="text-muted fw-semibold fs-6">Processo finalizado</span>
                                        @break
                                @endswitch
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            @if($proposicao->status === 'enviado_legislativo')
                                <!-- Botão para simular retorno do legislativo (apenas para teste) -->
                                <button type="button" class="btn btn-light-success btn-sm" onclick="simularRetornoLegislativo()">
                                    <i class="ki-duotone ki-fast-forward fs-4 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Simular Retorno
                                </button>
                            @endif
                            
                            @if($proposicao->status === 'retornado_legislativo')
                                <button type="button" class="btn btn-success" id="btn-assinar">
                                    <i class="ki-duotone ki-signature fs-2 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Assinar Documento
                                </button>
                            @endif
                            
                            @if($proposicao->status === 'assinado')
                                <button type="button" class="btn btn-dark" id="btn-protocolar">
                                    <i class="ki-duotone ki-check-circle fs-2 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Enviar para Protocolo
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                <!--end::Body-->
            </div>
            <!--end::Card-->

            <!-- Informações Detalhadas -->
            <!--begin::Row-->
            <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                <div class="col-xl-6">
                    <!--begin::Card-->
                    <div class="card card-xl-stretch mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 py-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">
                                    <i class="ki-duotone ki-information-5 fs-2 text-primary me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    Informações da Proposição
                                </span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            <div class="mb-5">
                                <label class="fw-semibold fs-6 text-gray-800">Tipo:</label>
                                <div class="fw-bold text-gray-600 fs-6 mt-1">{{ ucfirst(str_replace('_', ' ', $proposicao->tipo)) }}</div>
                            </div>
                            <div class="mb-5">
                                <label class="fw-semibold fs-6 text-gray-800">Ementa:</label>
                                <div class="text-gray-600 fs-6 mt-1">{{ $proposicao->ementa }}</div>
                            </div>
                            <div class="mb-0">
                                <label class="fw-semibold fs-6 text-gray-800">Autor:</label>
                                <div class="fw-bold text-gray-600 fs-6 mt-1">{{ Auth::user()->name }}</div>
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card-->
                </div>
                
                <div class="col-xl-6">
                    <!--begin::Card-->
                    <div class="card card-xl-stretch mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 py-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">
                                    <i class="ki-duotone ki-time fs-2 text-warning me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Histórico de Datas
                                </span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            @if($proposicao->enviado_em)
                                <div class="mb-5">
                                    <label class="fw-semibold fs-6 text-gray-800">Enviado ao Legislativo:</label>
                                    <div class="text-gray-600 fs-6 mt-1">{{ $proposicao->enviado_em->format('d/m/Y H:i') }}</div>
                                </div>
                            @endif
                            
                            @if($proposicao->assinatura)
                                <div class="mb-5">
                                    <label class="fw-semibold fs-6 text-gray-800">Data da Assinatura:</label>
                                    <div class="text-gray-600 fs-6 mt-1">{{ $proposicao->assinatura['assinado_em']->format('d/m/Y H:i') }}</div>
                                </div>
                                <div class="mb-5">
                                    <label class="fw-semibold fs-6 text-gray-800">Certificado:</label>
                                    <div class="text-gray-600 fs-7 mt-1">{{ $proposicao->assinatura['certificado'] }}</div>
                                </div>
                            @endif
                            
                            @if($proposicao->protocolo)
                                <div class="mb-5">
                                    <label class="fw-semibold fs-6 text-gray-800">Número do Protocolo:</label>
                                    <div class="fw-bold text-primary fs-6 mt-1">{{ $proposicao->protocolo['numero'] }}</div>
                                </div>
                                <div class="mb-0">
                                    <label class="fw-semibold fs-6 text-gray-800">Data do Protocolo:</label>
                                    <div class="text-gray-600 fs-6 mt-1">{{ $proposicao->protocolo['data']->format('d/m/Y H:i') }}</div>
                                </div>
                            @endif
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card-->
                </div>
            </div>
            <!--end::Row-->

            @if($proposicao->observacoes_legislativo)
                <!-- Observações do Legislativo -->
                <!--begin::Card-->
                <div class="card card-xl-stretch mb-5 mb-xl-8">
                    <!--begin::Header-->
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">
                                <i class="ki-duotone ki-message-text-2 fs-2 text-success me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                Observações do Legislativo
                            </span>
                        </h3>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body py-3">
                        <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-6">
                            <i class="ki-duotone ki-information-5 fs-2tx text-success me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <div class="fs-6 text-gray-700">{{ $proposicao->observacoes_legislativo }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Card-->
            @endif

            <!-- Ações -->
            <!--begin::Actions-->
            <div class="text-center">
                <a href="{{ route('proposicoes.minhas-proposicoes') }}" class="btn btn-light me-3">
                    <i class="ki-duotone ki-arrow-left fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Ver Minhas Proposições
                </a>
                
                @if(in_array($proposicao->status, ['rascunho', 'retornado_legislativo']))
                    <a href="{{ route('proposicoes.preparar-edicao', [$proposicao->id, $proposicao->template_id ?? 11]) }}" class="btn btn-primary">
                        <i class="ki-duotone ki-pencil fs-2 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Editar Proposição
                    </a>
                @endif
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Row-->
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const proposicaoId = {{ $proposicao->id }};
    
    // Simular retorno do legislativo (apenas para teste)
    window.simularRetornoLegislativo = function() {
        if (confirm('Simular retorno positivo do legislativo?')) {
            $.post(`/proposicoes/${proposicaoId}/retorno-legislativo`, {
                _token: $('meta[name="csrf-token"]').attr('content'),
                observacoes: 'Proposição aprovada pelo legislativo com ressalvas menores.'
            })
            .done(function(response) {
                if (response.success) {
                    toastr.success('Retorno do legislativo simulado!');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            })
            .fail(function() {
                toastr.error('Erro ao simular retorno');
            });
        }
    };
    
    // Assinar documento
    $('#btn-assinar').on('click', function() {
        const $btn = $(this);
        
        Swal.fire({
            title: 'Assinar Documento',
            text: 'Deseja assinar digitalmente esta proposição?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, Assinar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (result.isConfirmed) {
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Assinando...');
                
                $.post(`/proposicoes/${proposicaoId}/assinar-documento`, {
                    _token: $('meta[name="csrf-token"]').attr('content')
                })
                .done(function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Sucesso!',
                            text: response.message,
                            icon: 'success'
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                })
                .fail(function(xhr) {
                    const error = xhr.responseJSON?.message || 'Erro ao assinar documento';
                    Swal.fire('Erro', error, 'error');
                })
                .always(function() {
                    $btn.prop('disabled', false).html('<i class="ki-duotone ki-signature fs-2 me-2"><span class="path1"></span><span class="path2"></span></i>Assinar Documento');
                });
            }
        });
    });
    
    // Protocolar documento
    $('#btn-protocolar').on('click', function() {
        const $btn = $(this);
        
        Swal.fire({
            title: 'Enviar para Protocolo',
            text: 'Deseja enviar esta proposição para protocolo?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, Protocolar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Protocolando...');
                
                $.post(`/proposicoes/${proposicaoId}/enviar-protocolo`, {
                    _token: $('meta[name="csrf-token"]').attr('content')
                })
                .done(function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Sucesso!',
                            html: `${response.message}<br><strong>Protocolo: ${response.numero_protocolo}</strong>`,
                            icon: 'success'
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                })
                .fail(function(xhr) {
                    const error = xhr.responseJSON?.message || 'Erro ao protocolar documento';
                    Swal.fire('Erro', error, 'error');
                })
                .always(function() {
                    $btn.prop('disabled', false).html('<i class="ki-duotone ki-check-circle fs-2 me-2"><span class="path1"></span><span class="path2"></span></i>Enviar para Protocolo');
                });
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
/* Custom styles for status page */
.timeline-item .timeline-badge i {
    transition: all 0.3s ease;
}

.timeline-item:hover .timeline-badge i {
    transform: scale(1.1);
}

.card:hover {
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
}
</style>
@endpush