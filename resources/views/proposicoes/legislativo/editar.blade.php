@extends('components.layouts.app')

@section('title', 'Editar Proposição - Legislativo')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-4">
        <div>
            <h1 class="h3 text-gray-800 mb-0">Editar Proposição</h1>
            <p class="text-muted">{{ $proposicao->tipo_formatado }} - Autor: {{ $proposicao->autor->name }}</p>
        </div>
        <div>
            <a href="{{ route('proposicoes.legislativo.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Voltar à Lista
            </a>
            <button type="button" class="btn btn-success" id="btn-enviar-parlamentar">
                <i class="fas fa-paper-plane me-2"></i>Enviar para Parlamentar
            </button>
        </div>
    </div>

    <!-- Informações da Proposição -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Edição da Proposição
                    </h5>
                </div>
                <div class="card-body">
                    <form id="form-edicao" action="{{ route('proposicoes.legislativo.salvar-edicao', $proposicao) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Ementa -->
                        <div class="mb-4">
                            <label for="ementa" class="form-label">
                                <strong>Ementa</strong>
                                <span class="text-danger">*</span>
                            </label>
                            <textarea 
                                id="ementa" 
                                name="ementa" 
                                class="form-control" 
                                rows="3" 
                                required
                                placeholder="Digite a ementa da proposição..."
                            >{{ old('ementa', $proposicao->ementa) }}</textarea>
                            <div class="form-text">Resumo do objetivo da proposição.</div>
                        </div>

                        <!-- Conteúdo -->
                        <div class="mb-4">
                            <label for="conteudo" class="form-label">
                                <strong>Conteúdo da Proposição</strong>
                                <span class="text-danger">*</span>
                            </label>
                            <div class="border rounded" style="min-height: 400px;">
                                <div id="editor-conteudo">{{ old('conteudo', $proposicao->conteudo) }}</div>
                            </div>
                            <input type="hidden" id="conteudo" name="conteudo" value="{{ old('conteudo', $proposicao->conteudo) }}">
                            <div class="form-text">Texto completo da proposição.</div>
                        </div>

                        <!-- Observações da Edição -->
                        <div class="mb-4">
                            <label for="observacoes_edicao" class="form-label">
                                <strong>Observações sobre as Edições</strong>
                            </label>
                            <textarea 
                                id="observacoes_edicao" 
                                name="observacoes_edicao" 
                                class="form-control" 
                                rows="4"
                                placeholder="Descreva as principais alterações realizadas..."
                            >{{ old('observacoes_edicao', $proposicao->observacoes_edicao) }}</textarea>
                            <div class="form-text">Explicação das modificações realizadas pelo Legislativo.</div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Salvar Alterações
                            </button>
                            <button type="button" class="btn btn-outline-info" id="btn-preview">
                                <i class="fas fa-eye me-2"></i>Visualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Status e Informações -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informações
                    </h6>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt>Status:</dt>
                        <dd>
                            <span class="badge bg-warning">Em Edição pelo Legislativo</span>
                        </dd>
                        
                        <dt>Tipo:</dt>
                        <dd>{{ $proposicao->tipo_formatado }}</dd>
                        
                        <dt>Autor:</dt>
                        <dd>{{ $proposicao->autor->name }}</dd>
                        
                        <dt>Data de Criação:</dt>
                        <dd>{{ $proposicao->created_at->format('d/m/Y H:i') }}</dd>
                        
                        @if($proposicao->ultima_modificacao)
                        <dt>Última Modificação:</dt>
                        <dd>{{ $proposicao->ultima_modificacao->format('d/m/Y H:i') }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Histórico de Versões -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Histórico
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Criada pelo Parlamentar</h6>
                                <small class="text-muted">{{ $proposicao->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Enviada para o Legislativo</h6>
                                <small class="text-muted">{{ $proposicao->updated_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                        <div class="timeline-item active">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Em edição pelo Legislativo</h6>
                                <small class="text-muted">Agora</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Enviar ao Parlamentar -->
<div class="modal fade" id="modalEnviarParlamentar" tabindex="-1" aria-labelledby="modalEnviarParlamentarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEnviarParlamentarLabel">Enviar para Parlamentar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-enviar-parlamentar" action="{{ route('proposicoes.legislativo.enviar-parlamentar', $proposicao) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="observacoes_retorno" class="form-label">Observações para o Parlamentar</label>
                        <textarea 
                            id="observacoes_retorno" 
                            name="observacoes_retorno" 
                            class="form-control" 
                            rows="4"
                            placeholder="Explique as alterações realizadas e orientações para o parlamentar..."
                        ></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="solicitar_aprovacao" name="solicitar_aprovacao" value="1">
                            <label class="form-check-label" for="solicitar_aprovacao">
                                <strong>Solicitar aprovação do autor antes de prosseguir</strong>
                            </label>
                            <div class="form-text">Se marcado, a proposição ficará aguardando aprovação do parlamentar. Se não marcado, será devolvida para edição livre.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane me-2"></i>Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Preview -->
<div class="modal fade" id="modalPreview" tabindex="-1" aria-labelledby="modalPreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPreviewLabel">Visualização da Proposição</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="preview-content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item:before {
    content: '';
    position: absolute;
    left: -30px;
    top: 8px;
    bottom: -12px;
    width: 2px;
    background: #dee2e6;
}

.timeline-item:last-child:before {
    display: none;
}

.timeline-marker {
    position: absolute;
    left: -36px;
    top: 4px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-item.active .timeline-marker {
    box-shadow: 0 0 0 2px #ffc107;
}

.ql-editor {
    min-height: 300px;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
$(document).ready(function() {
    // Inicializar editor Quill
    var quill = new Quill('#editor-conteudo', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link'],
                ['clean']
            ]
        }
    });

    // Sincronizar conteúdo do editor com input hidden
    quill.on('text-change', function() {
        $('#conteudo').val(quill.root.innerHTML);
    });

    // Salvar edição
    $('#form-edicao').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const url = $(this).attr('action');
        
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            beforeSend: function() {
                $('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Salvando...');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error('Erro ao salvar alterações');
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors || {};
                let errorMessage = 'Erro ao salvar alterações:\n';
                
                Object.values(errors).forEach(function(fieldErrors) {
                    fieldErrors.forEach(function(error) {
                        errorMessage += '- ' + error + '\n';
                    });
                });
                
                toastr.error(errorMessage);
            },
            complete: function() {
                $('button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Salvar Alterações');
            }
        });
    });

    // Preview
    $('#btn-preview').on('click', function() {
        const ementa = $('#ementa').val();
        const conteudo = quill.root.innerHTML;
        
        let previewHtml = '<div class="proposicao-preview">';
        previewHtml += '<h4>Ementa</h4>';
        previewHtml += '<p class="border-bottom pb-3 mb-4">' + (ementa || '<em>Ementa não informada</em>') + '</p>';
        previewHtml += '<h4>Conteúdo</h4>';
        previewHtml += '<div class="content">' + (conteudo || '<em>Conteúdo não informado</em>') + '</div>';
        previewHtml += '</div>';
        
        $('#preview-content').html(previewHtml);
        $('#modalPreview').modal('show');
    });

    // Enviar para parlamentar
    $('#btn-enviar-parlamentar').on('click', function() {
        $('#modalEnviarParlamentar').modal('show');
    });

    $('#form-enviar-parlamentar').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const url = $(this).attr('action');
        
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            beforeSend: function() {
                $('#modalEnviarParlamentar button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Enviando...');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#modalEnviarParlamentar').modal('hide');
                    
                    setTimeout(function() {
                        window.location.href = '{{ route("proposicoes.legislativo.index") }}';
                    }, 2000);
                } else {
                    toastr.error('Erro ao enviar proposição');
                }
            },
            error: function(xhr) {
                toastr.error('Erro ao enviar proposição');
            },
            complete: function() {
                $('#modalEnviarParlamentar button[type="submit"]').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Enviar');
            }
        });
    });

    // Auto-save a cada 2 minutos
    setInterval(function() {
        if ($('#form-edicao').length && $('#ementa').val().trim() !== '') {
            $('#form-edicao').trigger('submit');
        }
    }, 120000);
    
    // =========================================================================
    // ATUALIZAÇÃO AUTOMÁTICA AO RETORNAR DO EDITOR ONLYOFFICE
    // =========================================================================
    
    // Verificar se voltamos do editor OnlyOffice
    const editorFechado = localStorage.getItem('onlyoffice_editor_fechado');
    const destinoEsperado = localStorage.getItem('onlyoffice_destino');
    const urlAtual = window.location.href.split('?')[0]; // Remove query parameters
    
    if (editorFechado === 'true' && destinoEsperado) {
        // Verificar se estamos na página de destino correta
        const destinoLimpo = destinoEsperado.split('?')[0]; // Remove query parameters
        
        if (urlAtual === destinoLimpo) {
            console.log('🔄 Retornando do editor OnlyOffice - atualizando página Legislativo...');
            
            // Limpar flags do localStorage
            localStorage.removeItem('onlyoffice_editor_fechado');
            localStorage.removeItem('onlyoffice_destino');
            
            // Mostrar toast informativo
            if (typeof Swal !== 'undefined') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                
                Toast.fire({
                    icon: 'success',
                    title: 'Documento atualizado',
                    text: 'As alterações do editor foram salvas'
                });
            }
            
            // Forçar atualização da página após pequeno delay
            setTimeout(() => {
                if (!window.location.href.includes('_refresh=')) {
                    window.location.reload(true);
                }
            }, 1000);
        }
    }
    
    // Limpar parâmetros de refresh da URL
    if (window.location.href.includes('_refresh=')) {
        const urlLimpa = window.location.href.replace(/[?&]_refresh=\d+/, '');
        window.history.replaceState({}, document.title, urlLimpa);
    }
});
</script>
@endpush