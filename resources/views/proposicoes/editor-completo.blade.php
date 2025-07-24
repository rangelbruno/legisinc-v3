@extends('layouts.app')

@section('title', 'Editor de Proposição')

@section('content')
<div class="container">
    <!--begin::Toolbar-->
    <div class="toolbar mb-5 mb-lg-7" id="kt_toolbar">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                <i class="ki-duotone ki-notepad-edit fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Editor de Proposição
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('proposicoes.minhas-proposicoes') }}" class="text-muted text-hover-primary">Minhas Proposições</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('proposicoes.preparar-edicao', [$proposicao->id, $template->id]) }}" class="text-muted text-hover-primary">Preparar Edição</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-gray-900">Editor OnlyOffice</li>
            </ul>
        </div>
        <div class="d-flex align-items-center">
            <button type="button" class="btn btn-success" id="btn-enviar-legislativo">
                <i class="ki-duotone ki-paper-plane fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Enviar para o Legislativo
            </button>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Row-->
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <!-- Editor OnlyOffice -->
        <div class="col-xl-9">
            <!--begin::Card-->
            <div class="card card-xl-stretch mb-xl-8">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Editor OnlyOffice</span>
                        <span class="text-muted fw-semibold fs-7">Edição em tempo real</span>
                    </h3>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body p-0">
                    <div id="onlyoffice-editor" style="height: 800px;">
                        <!-- OnlyOffice será renderizado aqui -->
                    </div>
                </div>
                <!--end::Body-->
            </div>
            <!--end::Card-->
        </div>

        <!-- Sidebar com Anexos -->
        <div class="col-xl-3">
            <!-- Informações da Proposição -->
            <!--begin::Card-->
            <div class="card card-xl-stretch mb-5 mb-xl-8">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">
                            <i class="ki-duotone ki-information-5 fs-2 text-info me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            Informações
                        </span>
                    </h3>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body py-3">
                    <div class="mb-5">
                        <label class="fw-semibold fs-6 text-gray-800">Proposição:</label>
                        <div class="fw-bold text-gray-600 fs-6 mt-1">#{{ $proposicao->id }}</div>
                    </div>
                    <div class="mb-5">
                        <label class="fw-semibold fs-6 text-gray-800">Tipo:</label>
                        <div class="fw-bold text-gray-600 fs-6 mt-1">{{ ucfirst(str_replace('_', ' ', $proposicao->tipo)) }}</div>
                    </div>
                    <div class="mb-0">
                        <label class="fw-semibold fs-6 text-gray-800">Template:</label>
                        <div class="fw-bold text-gray-600 fs-6 mt-1">{{ $template->nome }}</div>
                    </div>
                </div>
                <!--end::Body-->
            </div>
            <!--end::Card-->

            <!-- Anexos -->
            <!--begin::Card-->
            <div class="card card-xl-stretch mb-xl-8">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">
                            <i class="ki-duotone ki-paper-clip fs-2 text-primary me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Anexos
                        </span>
                    </h3>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body py-3">
                    <!-- Upload de Anexos -->
                    <div class="mb-5">
                        <label class="fw-semibold fs-6 text-gray-800 mb-3">Adicionar Anexo</label>
                        <input type="file" id="input-anexo" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <div class="text-muted fw-normal fs-7 mt-2">Máximo 10MB por arquivo</div>
                    </div>

                    <!-- Lista de Anexos -->
                    <div id="lista-anexos">
                        @if(count($proposicao->anexos) > 0)
                            @foreach($proposicao->anexos as $anexo)
                                <div class="anexo-item mb-3 p-3 border border-gray-300 border-dashed rounded" data-anexo-id="{{ $anexo['id'] }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="text-truncate fw-semibold text-gray-800" title="{{ $anexo['nome'] }}">
                                                <i class="ki-duotone ki-file fs-2 text-gray-600 me-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                {{ $anexo['nome'] }}
                                            </div>
                                            <div class="text-muted fw-normal fs-7 mt-1">{{ number_format($anexo['tamanho'] / 1024, 2) }} KB</div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-light-danger btn-remover-anexo" data-anexo-id="{{ $anexo['id'] }}">
                                            <i class="ki-duotone ki-cross fs-4">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-5" id="sem-anexos">
                                <i class="ki-duotone ki-paper-clip fs-3x text-gray-300 mb-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="fw-semibold text-gray-600 fs-6">Nenhum anexo adicionado</div>
                            </div>
                        @endif
                    </div>
                </div>
                <!--end::Body-->
            </div>
            <!--end::Card-->
        </div>
    </div>
    <!--end::Row-->
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
<!-- OnlyOffice API -->
<script type="text/javascript" src="{{ config('onlyoffice.document_server_url') }}/web-apps/apps/api/documents/api.js"></script>

<script>
$(document).ready(function() {
    const proposicaoId = {{ $proposicao->id }};
    
    // Inicializar OnlyOffice
    const config = {
        document: {
            fileType: "rtf",
            key: "{{ $documentKey }}",
            title: "Proposição #{{ $proposicao->id }}",
            url: "{{ url('/onlyoffice/file/proposicao/' . $proposicao->id . '/' . $arquivoProposicao) }}"
        },
        documentType: "word",
        editorConfig: {
            callbackUrl: "{{ url('/onlyoffice/callback/proposicao/' . $proposicao->id) }}",
            lang: "pt-BR",
            mode: "edit",
            customization: {
                autosave: true,
                chat: false,
                comments: false,
                compactHeader: true,
                compactToolbar: false,
                forcesave: true,
                showReviewChanges: false,
                zoom: 100
            },
            user: {
                id: "{{ Auth::id() }}",
                name: "{{ Auth::user()->name }}"
            }
        },
        events: {
            onDocumentStateChange: function(event) {
                console.log("Document changed: ", event.data);
                // Auto-salvar quando houver mudanças
                if (event.data) {
                    setTimeout(() => {
                        showAutoSaveIndicator();
                    }, 1000);
                }
            },
            onDocumentReady: function() {
                console.log("Document is ready");
                toastr.success("Editor carregado com sucesso!");
            },
            onError: function(event) {
                console.error("OnlyOffice error: ", event.data);
                toastr.error("Erro no editor: " + event.data);
            }
        },
        height: "800px",
        width: "100%"
    };

    new DocsAPI.DocEditor("onlyoffice-editor", config);

    // Função para mostrar indicador de auto-salvamento
    function showAutoSaveIndicator() {
        // Remover indicador anterior se existir
        $('#auto-save-indicator').remove();
        
        // Adicionar indicador de salvamento
        const indicator = `
            <div id="auto-save-indicator" class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check me-2"></i>
                    <strong>Salvamento automático</strong> - Documento salvo
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        `;
        
        $('body').append(indicator);
        
        // Remover automaticamente após 3 segundos
        setTimeout(() => {
            $('#auto-save-indicator').fadeOut(500, function() {
                $(this).remove();
            });
        }, 3000);
    }

    // Upload de anexo
    $('#input-anexo').on('change', function() {
        const file = this.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('anexo', file);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Mostrar loading
        const $input = $(this);
        $input.prop('disabled', true);

        $.ajax({
            url: `/proposicoes/${proposicaoId}/upload-anexo`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Remover mensagem de "sem anexos"
                    $('#sem-anexos').remove();
                    
                    // Adicionar anexo à lista
                    const anexo = response.anexo;
                    const html = `
                        <div class="anexo-item mb-3 p-3 border border-gray-300 border-dashed rounded" data-anexo-id="${anexo.id}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-truncate fw-semibold text-gray-800" title="${anexo.nome}">
                                        <i class="ki-duotone ki-file fs-2 text-gray-600 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        ${anexo.nome}
                                    </div>
                                    <div class="text-muted fw-normal fs-7 mt-1">${(anexo.tamanho / 1024).toFixed(2)} KB</div>
                                </div>
                                <button type="button" class="btn btn-sm btn-light-danger btn-remover-anexo" data-anexo-id="${anexo.id}">
                                    <i class="ki-duotone ki-cross fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </button>
                            </div>
                        </div>
                    `;
                    $('#lista-anexos').append(html);
                    
                    toastr.success('Anexo adicionado com sucesso!');
                }
            },
            error: function() {
                toastr.error('Erro ao fazer upload do anexo');
            },
            complete: function() {
                $input.prop('disabled', false).val('');
            }
        });
    });

    // Remover anexo
    $(document).on('click', '.btn-remover-anexo', function() {
        const anexoId = $(this).data('anexo-id');
        const $anexoItem = $(this).closest('.anexo-item');

        if (confirm('Deseja remover este anexo?')) {
            $.ajax({
                url: `/proposicoes/${proposicaoId}/remover-anexo/${anexoId}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $anexoItem.remove();
                        
                        // Se não há mais anexos, mostrar mensagem
                        if ($('.anexo-item').length === 0) {
                            const html = `
                                <div class="text-center text-muted py-5" id="sem-anexos">
                                    <i class="ki-duotone ki-paper-clip fs-3x text-gray-300 mb-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="fw-semibold text-gray-600 fs-6">Nenhum anexo adicionado</div>
                                </div>
                            `;
                            $('#lista-anexos').html(html);
                        }
                        
                        toastr.success('Anexo removido com sucesso!');
                    }
                },
                error: function() {
                    toastr.error('Erro ao remover anexo');
                }
            });
        }
    });

    // Enviar para o Legislativo
    $('#btn-enviar-legislativo').on('click', function() {
        $('#modalEnviarLegislativo').modal('show');
    });

    $('#btn-confirmar-envio').on('click', function() {
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Enviando...');

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
.anexo-item {
    transition: background-color 0.2s;
}

.anexo-item:hover {
    background-color: #f8f9fa;
}

.btn-remover-anexo {
    opacity: 0.7;
    transition: opacity 0.2s;
}

.btn-remover-anexo:hover {
    opacity: 1;
}

#onlyoffice-editor {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}
</style>
@endpush