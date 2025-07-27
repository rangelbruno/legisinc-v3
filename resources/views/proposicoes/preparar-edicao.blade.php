@extends('layouts.app')

@section('title', 'Preparar Edição')

@section('content')
<div class="container">
    <!--begin::Toolbar-->
    <div class="toolbar mb-5 mb-lg-7" id="kt_toolbar">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                <i class="ki-duotone ki-pencil fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Preparar Edição da Proposição
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('proposicoes.minhas-proposicoes') }}" class="text-muted text-hover-primary">Minhas Proposições</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-gray-900">Preparar Edição</li>
            </ul>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Row-->
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-xl-12">
            <!--begin::Mixed Widget 1-->
            <div class="card card-xl-stretch mb-xl-8">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Informações da Proposição</span>
                        <span class="text-muted fw-semibold fs-7">Dados salvos e prontos para edição</span>
                    </h3>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body p-0">
                    <div class="px-9 pt-7 pb-9">
                        <!-- Informações da Proposição -->
                        <!--begin::Details-->
                        <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                            <div class="me-7 mb-4">
                                <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                                    <div class="symbol-label fs-2 fw-semibold text-success border border-dashed border-success">
                                        <i class="ki-duotone ki-document fs-2x text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">
                                                Proposição #{{ $proposicao->id }}
                                            </span>
                                            <span class="badge badge-light-warning fs-8 fw-semibold ms-2">Rascunho</span>
                                        </div>
                                        <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                            <span class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                                <i class="ki-duotone ki-profile-circle fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                                {{ ucfirst(str_replace('_', ' ', $proposicao->tipo)) }}
                                            </span>
                                            <span class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                                                <i class="ki-duotone ki-geolocation fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                @if($template)
                                                    {{ $template->tipoProposicao->nome ?? 'Template Personalizado' }}
                                                    <small class="text-success ms-2">(Template do Admin)</small>
                                                @else
                                                    Template em Branco
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap flex-stack">
                                    <div class="d-flex flex-column flex-grow-1 pe-8">
                                        <div class="d-flex flex-wrap">
                                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="fs-2 fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ strlen($proposicao->ementa) }}</div>
                                                    <div class="m-0">
                                                        <span class="fw-semibold fs-7 text-gray-400">caracteres</span>
                                                        <div class="fw-bold fs-6 text-gray-600">na ementa</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="fs-2 fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ strlen($proposicao->conteudo) }}</div>
                                                    <div class="m-0">
                                                        <span class="fw-semibold fs-7 text-gray-400">caracteres</span>
                                                        <div class="fw-bold fs-6 text-gray-600">no conteúdo</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center w-200px w-sm-300px flex-column mt-3">
                                        <button type="button" class="btn btn-primary btn-lg w-100 mb-3" id="btn-abrir-editor">
                                            <i class="ki-duotone ki-notepad-edit fs-2 me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            @if($template)
                                                Abrir Editor com Template Admin
                                            @else
                                                Abrir Editor OnlyOffice
                                            @endif
                                        </button>
                                        <div class="fs-7 text-muted mb-3">Editor será aberto em nova aba</div>
                                        
                                        <div class="separator separator-dashed my-3 w-100"></div>
                                        
                                        <button type="button" class="btn btn-success btn-lg w-100 mb-3" id="btn-enviar-legislativo">
                                            <i class="ki-duotone ki-paper-plane fs-2 me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Enviar para o Legislativo
                                        </button>
                                        <div class="fs-7 text-muted">Enviar proposição para análise</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Details-->

                        <!--begin::Ementa e Conteúdo-->
                        <div class="separator separator-dashed my-7"></div>
                        <div class="row mb-7">
                            <div class="col-lg-12">
                                <div class="mb-5">
                                    <label class="fw-semibold fs-6 text-gray-800">Ementa</label>
                                    <div class="text-gray-600 fw-normal fs-6 mt-2">{{ $proposicao->ementa }}</div>
                                </div>
                                <div class="mb-5">
                                    <label class="fw-semibold fs-6 text-gray-800">Conteúdo Principal</label>
                                    <div class="text-gray-600 fw-normal fs-6 mt-2">{{ Str::limit($proposicao->conteudo, 300) }}</div>
                                </div>
                            </div>
                        </div>
                        <!--end::Ementa e Conteúdo-->
                    </div>
                </div>
                <!--end::Body-->
            </div>
            <!--end::Mixed Widget 1-->
        </div>
    </div>
    <!--end::Row-->

    <!--begin::Row-->
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-xl-6">
            <!--begin::Card-->
            <div class="card card-xl-stretch mb-xl-8">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Próximos Passos</span>
                        <span class="text-muted fw-semibold fs-7">Como proceder com a edição</span>
                    </h3>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body py-3">
                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6">
                        <i class="ki-duotone ki-information-5 fs-2tx text-info me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-6 text-gray-700">
                                    <ol class="mb-0">
                                        <li>Clique em "Abrir Editor OnlyOffice" para editar o documento</li>
                                        <li>Faça as alterações necessárias no editor</li>
                                        <li>O documento será salvo automaticamente</li>
                                        <li>Após finalizar, clique em "Enviar para o Legislativo"</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
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
                        <span class="card-label fw-bold fs-3 mb-1">Fluxo de Tramitação</span>
                        <span class="text-muted fw-semibold fs-7">Etapas do processo</span>
                    </h3>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body py-3">
                    <!--begin::Timeline-->
                    <div class="timeline-label">
                        <div class="timeline-item">
                            <div class="timeline-label fw-bold text-gray-800 fs-6">Atual</div>
                            <div class="timeline-badge">
                                <i class="fa fa-genderless text-warning fs-1"></i>
                            </div>
                            <div class="timeline-content d-flex">
                                <span class="fw-bold text-gray-800 ps-3">Rascunho</span>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-label fw-bold text-gray-800 fs-6">Próximo</div>
                            <div class="timeline-badge">
                                <i class="fa fa-genderless text-muted fs-1"></i>
                            </div>
                            <div class="timeline-content d-flex">
                                <span class="fw-bold text-muted ps-3">Enviado ao Legislativo</span>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-label fw-bold text-gray-800 fs-6"></div>
                            <div class="timeline-badge">
                                <i class="fa fa-genderless text-muted fs-1"></i>
                            </div>
                            <div class="timeline-content d-flex">
                                <span class="fw-bold text-muted ps-3">Retornado</span>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-label fw-bold text-gray-800 fs-6"></div>
                            <div class="timeline-badge">
                                <i class="fa fa-genderless text-muted fs-1"></i>
                            </div>
                            <div class="timeline-content d-flex">
                                <span class="fw-bold text-muted ps-3">Assinado</span>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-label fw-bold text-gray-800 fs-6"></div>
                            <div class="timeline-badge">
                                <i class="fa fa-genderless text-muted fs-1"></i>
                            </div>
                            <div class="timeline-content d-flex">
                                <span class="fw-bold text-muted ps-3">Protocolado</span>
                            </div>
                        </div>
                    </div>
                    <!--end::Timeline-->
                </div>
                <!--end::Body-->
            </div>
            <!--end::Card-->
        </div>
    </div>
    <!--end::Row-->

    <!--begin::Actions-->
    <div class="text-center">
        <a href="{{ route('proposicoes.minhas-proposicoes') }}" class="btn btn-light me-3">
            <i class="ki-duotone ki-arrow-left fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            Ver Minhas Proposições
        </a>
    </div>
    <!--end::Actions-->
</div>

<!--begin::Modal - Confirmar Envio-->
<div class="modal fade" id="modalEnviarLegislativo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Confirmar Envio</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body py-10 px-lg-17">
                <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                    <i class="ki-duotone ki-information-5 fs-2tx text-warning me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <div class="fs-6 text-gray-700 mb-2">Tem certeza que deseja enviar esta proposição para o Legislativo?</div>
                            <div class="fs-7 text-muted">Após o envio, não será possível fazer alterações no documento.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btn-confirmar-envio">
                    <i class="ki-duotone ki-paper-plane fs-2 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Confirmar Envio
                </button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Confirmar Envio-->
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const proposicaoId = {{ $proposicao->id }};
    const templateId = '{{ $template ? $template->id : "blank" }}';
    
    // Abrir editor OnlyOffice em nova aba
    $('#btn-abrir-editor').on('click', function() {
        // Desabilitar botão temporariamente
        $(this).prop('disabled', true);
        
        // Mostrar mensagem
        @if($template)
            toastr.info('Abrindo editor com template do administrador...');
        @else
            toastr.info('Abrindo editor em nova aba...');
        @endif
        
        // Abrir editor OnlyOffice diretamente em nova aba
        const url = `/proposicoes/${proposicaoId}/editar-onlyoffice/${templateId}`;
        window.open(url, '_blank');
        
        // Reabilitar botão após 2 segundos
        setTimeout(() => {
            $(this).prop('disabled', false);
        }, 2000);
        
        // Atualizar status na sessão
        $.post(`/proposicoes/${proposicaoId}/atualizar-status`, {
            _token: $('meta[name="csrf-token"]').attr('content'),
            status: 'em_edicao'
        });
    });

    // Enviar para o Legislativo
    $('#btn-enviar-legislativo').on('click', function() {
        $('#modalEnviarLegislativo').modal('show');
    });

    $('#btn-confirmar-envio').on('click', function() {
        $(this).prop('disabled', true).html('<i class="ki-duotone ki-loading fs-2 me-2"><span class="path1"></span><span class="path2"></span></i>Enviando...');

        $.ajax({
            url: `/proposicoes/${proposicaoId}/enviar-legislativo`,
            type: 'PUT',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Proposição enviada com sucesso!');
                    setTimeout(function() {
                        window.location.href = `/proposicoes/${proposicaoId}/status`;
                    }, 1500);
                }
            },
            error: function() {
                toastr.error('Erro ao enviar proposição');
                $('#btn-confirmar-envio').prop('disabled', false).html('<i class="ki-duotone ki-paper-plane fs-2 me-2"><span class="path1"></span><span class="path2"></span></i>Confirmar Envio');
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.badge {
    padding: 0.5rem 1rem;
}

.progress {
    background-color: #e9ecef;
}

.alert-info {
    background-color: #e7f3ff;
    border-color: #b6d4fe;
    color: #084298;
}
</style>
@endpush