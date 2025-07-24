@extends('components.layouts.app')

@section('title', 'Editar Proposição')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-5">
        <div>
            <h1 class="h3 text-gray-800 mb-0">Editar Proposição</h1>
            <p class="text-muted">Etapa 3: Edição Final do Documento</p>
        </div>
        <div>
            <a href="{{ route('proposicoes.minhas-proposicoes') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Minhas Proposições
            </a>
        </div>
    </div>

    <!-- Stepper -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="stepper stepper-pills stepper-column d-flex flex-stack flex-wrap">
                        <div class="stepper-item completed" data-kt-stepper-element="nav">
                            <div class="stepper-wrapper d-flex align-items-center">
                                <div class="stepper-icon w-40px h-40px">
                                    <i class="stepper-check fas fa-check"></i>
                                    <span class="stepper-number">1</span>
                                </div>
                                <div class="stepper-label">
                                    <h3 class="stepper-title">Dados Básicos</h3>
                                    <div class="stepper-desc">Tipo, Ementa e Modelo</div>
                                </div>
                            </div>
                        </div>
                        <div class="stepper-item completed" data-kt-stepper-element="nav">
                            <div class="stepper-wrapper d-flex align-items-center">
                                <div class="stepper-icon w-40px h-40px">
                                    <i class="stepper-check fas fa-check"></i>
                                    <span class="stepper-number">2</span>
                                </div>
                                <div class="stepper-label">
                                    <h3 class="stepper-title">Preenchimento</h3>
                                    <div class="stepper-desc">Campos do Modelo</div>
                                </div>
                            </div>
                        </div>
                        <div class="stepper-item current" data-kt-stepper-element="nav">
                            <div class="stepper-wrapper d-flex align-items-center">
                                <div class="stepper-icon w-40px h-40px">
                                    <i class="stepper-check fas fa-check"></i>
                                    <span class="stepper-number">3</span>
                                </div>
                                <div class="stepper-label">
                                    <h3 class="stepper-title">Edição Final</h3>
                                    <div class="stepper-desc">Revisão e Envio</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Editor de Texto -->
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Editor de Texto da Proposição
                    </h5>
                </div>
                <div class="card-body">
                    <form id="form-editar-texto">
                        @csrf
                        <input type="hidden" name="proposicao_id" value="{{ $proposicao->id }}">
                        
                        <!-- Editor de Texto -->
                        <div class="mb-4">
                            <label for="conteudo" class="form-label">Conteúdo da Proposição</label>
                            <textarea name="conteudo" id="conteudo" class="form-control" rows="20" 
                                      placeholder="Digite o conteúdo da proposição..."
                                      required>{{ $proposicao->conteudo ?? '' }}</textarea>
                            <div class="form-text">
                                Use este editor para fazer ajustes finais no texto da proposição.
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button type="button" class="btn btn-outline-secondary" id="btn-salvar-rascunho">
                                    <i class="fas fa-save me-2"></i>Salvar Rascunho
                                </button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-warning me-2" id="btn-visualizar">
                                    <i class="fas fa-eye me-2"></i>Visualizar
                                </button>
                                <button type="submit" class="btn btn-success" id="btn-enviar-legislativo">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar para Legislativo
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar de Informações -->
        <div class="col-lg-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Informações da Proposição
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>ID:</strong> #{{ $proposicao->id }}
                    </div>
                    <div class="mb-3">
                        <strong>Status:</strong>
                        <span class="badge badge-warning">Rascunho</span>
                    </div>
                    <div class="mb-3">
                        <strong>Última Modificação:</strong><br>
                        <small class="text-muted">{{ date('d/m/Y H:i') }}</small>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        Dicas de Edição
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-1"></i>
                            Revise a estrutura do documento
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-1"></i>
                            Verifique a ortografia e gramática
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-1"></i>
                            Confirme todas as informações
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-1"></i>
                            Use linguagem formal e clara
                        </li>
                    </ul>

                    <div class="alert alert-info mt-3">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Lembrete:</strong> Após enviar para o legislativo, a proposição não poderá mais ser editada por você.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-save timer
    let autoSaveTimer;
    let hasChanges = false;

    // Detectar mudanças no conteúdo
    $('#conteudo').on('input', function() {
        hasChanges = true;
        clearTimeout(autoSaveTimer);
        
        // Auto-save após 3 segundos de inatividade
        autoSaveTimer = setTimeout(function() {
            if (hasChanges) {
                salvarRascunho(true); // true = auto-save silencioso
            }
        }, 3000);
    });

    // Salvar rascunho manualmente
    $('#btn-salvar-rascunho').on('click', function() {
        salvarRascunho(false);
    });

    // Visualizar proposição
    $('#btn-visualizar').on('click', function() {
        const proposicaoId = $('input[name="proposicao_id"]').val();
        window.open(`/proposicoes/${proposicaoId}`, '_blank');
    });

    // Enviar para legislativo
    $('#form-editar-texto').on('submit', function(e) {
        e.preventDefault();
        
        if (!$('#conteudo').val().trim()) {
            toastr.error('O conteúdo da proposição não pode estar vazio.');
            return;
        }

        // Confirmar envio
        if (confirm('Tem certeza que deseja enviar esta proposição para análise do legislativo? Após o envio, você não poderá mais editá-la.')) {
            enviarParaLegislativo();
        }
    });

    function salvarRascunho(autoSave = false) {
        const dados = {
            conteudo: $('#conteudo').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        const proposicaoId = $('input[name="proposicao_id"]').val();
        
        if (!autoSave) {
            $('#btn-salvar-rascunho').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Salvando...');
        }

        $.post(`/proposicoes/${proposicaoId}/salvar-texto`, dados)
            .done(function(response) {
                if (response.success) {
                    hasChanges = false;
                    if (!autoSave) {
                        toastr.success('Rascunho salvo com sucesso!');
                    }
                }
            })
            .fail(function(xhr) {
                if (!autoSave) {
                    toastr.error('Erro ao salvar rascunho');
                }
                console.error(xhr.responseText);
            })
            .always(function() {
                if (!autoSave) {
                    $('#btn-salvar-rascunho').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Salvar Rascunho');
                }
            });
    }

    function enviarParaLegislativo() {
        const proposicaoId = $('input[name="proposicao_id"]').val();
        
        $('#btn-enviar-legislativo').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Enviando...');

        // Primeiro salvar, depois enviar
        const dados = {
            conteudo: $('#conteudo').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $.post(`/proposicoes/${proposicaoId}/salvar-texto`, dados)
            .done(function() {
                // Agora enviar para legislativo
                $.ajax({
                    url: `/proposicoes/${proposicaoId}/enviar-legislativo`,
                    type: 'PUT',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .done(function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.href = '/proposicoes/minhas-proposicoes';
                        }, 2000);
                    }
                })
                .fail(function(xhr) {
                    toastr.error('Erro ao enviar proposição');
                    console.error(xhr.responseText);
                });
            })
            .fail(function(xhr) {
                toastr.error('Erro ao salvar antes do envio');
                console.error(xhr.responseText);
            })
            .always(function() {
                $('#btn-enviar-legislativo').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Enviar para Legislativo');
            });
    }

    // Avisar sobre mudanças não salvas
    window.addEventListener('beforeunload', function(e) {
        if (hasChanges) {
            const message = 'Você tem alterações não salvas. Deseja realmente sair?';
            e.returnValue = message;
            return message;
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.stepper .stepper-item.current .stepper-wrapper .stepper-icon {
    background-color: var(--bs-primary);
    color: white;
}

.stepper .stepper-item.completed .stepper-wrapper .stepper-icon {
    background-color: var(--bs-success);
    color: white;
}

#conteudo {
    font-family: 'Times New Roman', serif;
    font-size: 14px;
    line-height: 1.6;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.form-text {
    font-size: 0.875rem;
    color: #6c757d;
}
</style>
@endpush