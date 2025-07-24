@extends('components.layouts.app')

@section('title', 'Preencher Modelo')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-5">
        <div>
            <h1 class="h3 text-gray-800 mb-0">Preencher Modelo</h1>
            <p class="text-muted">Etapa 2: Preenchimento dos Campos do Modelo</p>
        </div>
        <div>
            <a href="{{ route('proposicoes.criar') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
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
                        <div class="stepper-item current" data-kt-stepper-element="nav">
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
                        <div class="stepper-item" data-kt-stepper-element="nav">
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

    <!-- Formulário de Preenchimento -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-form text-primary me-2"></i>
                        Preencher Campos do Modelo
                    </h5>
                </div>
                <div class="card-body">
                    <form id="form-preencher-modelo">
                        @csrf
                        <input type="hidden" name="proposicao_id" value="{{ $proposicao->id }}">
                        <input type="hidden" name="modelo_id" value="{{ $modelo->id }}">

                        <!-- Campos simplificados -->
                        <div id="campos-modelo">
                            <!-- Ementa -->
                            <div class="mb-4">
                                <label for="ementa" class="form-label required">Ementa</label>
                                <textarea name="conteudo_modelo[ementa]" id="ementa" class="form-control" 
                                          rows="3" placeholder="Digite a ementa da proposição..." required>{{ session('proposicao_' . $proposicao->id . '_ementa', '') }}</textarea>
                                <div class="form-text">
                                    Resumo claro e objetivo da proposição.
                                </div>
                            </div>

                            <!-- Conteúdo Principal -->
                            <div class="mb-4">
                                <label for="conteudo" class="form-label required">Conteúdo Principal</label>
                                <textarea name="conteudo_modelo[conteudo]" id="conteudo" class="form-control" 
                                          rows="10" placeholder="Digite o conteúdo completo da proposição..." required></textarea>
                                <div class="form-text">
                                    Texto completo da proposição com artigos, parágrafos e incisos.
                                </div>
                            </div>

                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary" id="btn-continuar-edicao">
                                <i class="fas fa-arrow-right me-2"></i>Continuar para Edição
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Informações do Modelo
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Modelo:</strong> {{ $modelo->nome }}
                    </div>
                    <div class="mb-3">
                        <strong>Proposição:</strong> #{{ $proposicao->id }}
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        Dicas de Preenchimento
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-1"></i>
                            Preencha a ementa com clareza
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-1"></i>
                            Digite o conteúdo completo da proposição
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-1"></i>
                            O rascunho será salvo automaticamente
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-1"></i>
                            Use o OnlyOffice para formatação avançada
                        </li>
                    </ul>

                    <div class="alert alert-success">
                        <small>
                            <i class="fas fa-save me-1"></i>
                            <strong>Salvamento Automático:</strong> Seus dados serão salvos como rascunho automaticamente ao continuar para edição.
                        </small>
                    </div>

                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>OnlyOffice:</strong> Para templates, você pode abrir o editor completo em nova aba para edição avançada.
                        </small>
                    </div>
                    
                    @if(str_starts_with($modelo->id ?? '', 'template_'))
                    <div class="alert alert-success mt-3">
                        <small>
                            <i class="fas fa-file-word me-1"></i>
                            <strong>Template OnlyOffice:</strong> Este modelo suporta edição avançada com formatação completa.
                        </small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const proposicaoId = $('input[name="proposicao_id"]').val();
    const modeloId = $('input[name="modelo_id"]').val();
    
    // Verificar se é um template OnlyOffice
    const isTemplate = modeloId && modeloId.toString().startsWith('template_');
    const templateId = isTemplate ? modeloId.replace('template_', '') : null;

    // Função para salvar rascunho automaticamente
    function salvarRascunhoAutomatico() {
        const dados = {
            ementa: $('#ementa').val(),
            conteudo: $('#conteudo').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        if (!dados.ementa || !dados.conteudo) {
            return Promise.reject('Campos obrigatórios não preenchidos');
        }

        return $.post(`/proposicoes/${proposicaoId}/salvar-dados-temporarios`, dados);
    }

    // Continuar para edição
    $('#form-preencher-modelo').on('submit', function(e) {
        e.preventDefault();
        
        // Validar campos obrigatórios
        let valid = true;
        $('.form-control[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                valid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!valid) {
            toastr.error('Preencha todos os campos obrigatórios.');
            return;
        }

        $('#btn-continuar-edicao').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processando...');

        const dados = $(this).serialize();

        // Primeiro salvar como rascunho automaticamente, depois gerar documento
        salvarRascunhoAutomatico()
            .done(function() {
                // Após salvar rascunho, gerar documento
                $.post(`/proposicoes/${proposicaoId}/gerar-texto`, dados)
                    .done(function(response) {
                        if (response.success) {
                            toastr.success('Rascunho salvo automaticamente. Abrindo editor...');
                            
                            // Ir para tela intermediária de edição
                            if (isTemplate) {
                                window.location.href = `/proposicoes/${proposicaoId}/preparar-edicao/${templateId}`;
                            } else {
                                // Para modelos normais, ir para editor de texto simples
                                window.location.href = `/proposicoes/${proposicaoId}/editar-texto`;
                            }
                        }
                    })
                    .fail(function(xhr) {
                        toastr.error('Erro ao processar dados');
                        console.error(xhr.responseText);
                    })
                    .always(function() {
                        $('#btn-continuar-edicao').prop('disabled', false).html('<i class="fas fa-arrow-right me-2"></i>Continuar para Edição');
                    });
            })
            .fail(function() {
                toastr.error('Erro ao salvar rascunho automaticamente');
                $('#btn-continuar-edicao').prop('disabled', false).html('<i class="fas fa-arrow-right me-2"></i>Continuar para Edição');
            });
    });

    // Remover classe de erro quando usuário corrigir
    $('.form-control').on('input', function() {
        $(this).removeClass('is-invalid');
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

.required:after {
    content: " *";
    color: red;
}

.form-text {
    font-size: 0.875rem;
    color: #6c757d;
}

.is-invalid {
    border-color: #dc3545;
}
</style>
@endpush