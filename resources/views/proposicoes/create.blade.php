@extends('components.layouts.app')

@section('title', 'Criar Nova Proposição')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-5">
        <div>
            <h1 class="h3 text-gray-800 mb-0">Criar Nova Proposição</h1>
            <p class="text-muted">Etapa 1: Dados Básicos da Proposição</p>
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
                        <div class="stepper-item current" data-kt-stepper-element="nav">
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
                        <div class="stepper-item" data-kt-stepper-element="nav">
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

    <!-- Formulário -->
    <div class="row">
        <div class="col-lg-8 col-xl-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt text-primary me-2"></i>
                        Informações da Proposição
                    </h5>
                </div>
                <div class="card-body">
                    <form id="form-criar-proposicao">
                        @csrf
                        
                        <!-- Tipo de Proposição -->
                        <div class="mb-4">
                            <label for="tipo" class="form-label required">Tipo de Proposição</label>
                            <select name="tipo" id="tipo" class="form-select" required>
                                <option value="">Selecione o tipo de proposição</option>
                                @foreach($tipos as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                Escolha o tipo adequado conforme a natureza da proposição.
                            </div>
                        </div>

                        <!-- Ementa -->
                        <div class="mb-4">
                            <label for="ementa" class="form-label required">Ementa</label>
                            <textarea name="ementa" id="ementa" class="form-control" rows="4" 
                                      placeholder="Digite um resumo claro e objetivo do que trata a proposição..."
                                      maxlength="1000" required></textarea>
                            <div class="d-flex justify-content-between">
                                <div class="form-text">
                                    Resumo claro e objetivo da proposição (máximo 1000 caracteres).
                                </div>
                                <small class="text-muted">
                                    <span id="ementa-count">0</span>/1000
                                </small>
                            </div>
                        </div>

                        <!-- Modelo -->
                        <div class="mb-4" id="modelo-container" style="display: none;">
                            <label for="modelo" class="form-label required">Selecionar Modelo</label>
                            <select name="modelo" id="modelo" class="form-select">
                                <option value="">Carregando modelos...</option>
                            </select>
                            <div class="form-text">
                                Escolha um modelo base para sua proposição.
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" id="btn-salvar-rascunho">
                                <i class="fas fa-save me-2"></i>Salvar Rascunho
                            </button>
                            <button type="submit" class="btn btn-primary" id="btn-continuar" disabled>
                                <i class="fas fa-arrow-right me-2"></i>Continuar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar de Ajuda -->
        <div class="col-lg-4 col-xl-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Ajuda
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="fw-bold">Tipos de Proposição:</h6>
                        <ul class="list-unstyled small">
                            <li><strong>PL:</strong> Projeto de Lei</li>
                            <li><strong>PLP:</strong> Projeto de Lei Complementar</li>
                            <li><strong>PEC:</strong> Proposta de Emenda Constitucional</li>
                            <li><strong>PDC:</strong> Projeto de Decreto Legislativo</li>
                            <li><strong>PRC:</strong> Projeto de Resolução</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="fw-bold">Dicas para a Ementa:</h6>
                        <ul class="list-unstyled small">
                            <li>• Seja claro e objetivo</li>
                            <li>• Use linguagem formal</li>
                            <li>• Evite termos técnicos desnecessários</li>
                            <li>• Indique o objetivo principal</li>
                        </ul>
                    </div>

                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-lightbulb me-1"></i>
                            <strong>Dica:</strong> Você pode salvar como rascunho e continuar depois.
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
    let proposicaoId = null;

    // Contador de caracteres da ementa
    $('#ementa').on('input', function() {
        const count = $(this).val().length;
        $('#ementa-count').text(count);
        
        if (count > 950) {
            $('#ementa-count').addClass('text-warning');
        } else {
            $('#ementa-count').removeClass('text-warning');
        }
    });

    // Carregar modelos quando tipo for selecionado
    $('#tipo').on('change', function() {
        const tipo = $(this).val();
        
        if (tipo) {
            carregarModelos(tipo);
            $('#modelo-container').show();
        } else {
            $('#modelo-container').hide();
            $('#btn-continuar').prop('disabled', true);
        }
    });

    // Validar se pode continuar
    $('#modelo').on('change', function() {
        validarFormulario();
    });

    // Salvar rascunho
    $('#btn-salvar-rascunho').on('click', function() {
        salvarRascunho();
    });

    // Continuar para próxima etapa
    $('#form-criar-proposicao').on('submit', function(e) {
        e.preventDefault();
        
        if (proposicaoId) {
            const modeloId = $('#modelo').val();
            if (modeloId) {
                window.location.href = `/proposicoes/${proposicaoId}/preencher-modelo/${modeloId}`;
            }
        } else {
            alert('Erro: Proposição não foi salva. Tente salvar como rascunho primeiro.');
        }
    });

    function carregarModelos(tipo) {
        $('#modelo').html('<option value="">Carregando...</option>');
        
        $.get(`/proposicoes/modelos/${tipo}`)
            .done(function(modelos) {
                let options = '<option value="">Selecione um modelo</option>';
                modelos.forEach(function(modelo) {
                    options += `<option value="${modelo.id}">${modelo.nome}</option>`;
                });
                $('#modelo').html(options);
            })
            .fail(function() {
                $('#modelo').html('<option value="">Erro ao carregar modelos</option>');
                toastr.error('Erro ao carregar modelos');
            });
    }

    function salvarRascunho() {
        const dados = {
            tipo: $('#tipo').val(),
            ementa: $('#ementa').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        if (!dados.tipo || !dados.ementa) {
            toastr.warning('Preencha pelo menos o tipo e a ementa');
            return;
        }

        $('#btn-salvar-rascunho').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Salvando...');

        $.post('/proposicoes/salvar-rascunho', dados)
            .done(function(response) {
                if (response.success) {
                    proposicaoId = response.proposicao_id;
                    toastr.success('Rascunho salvo com sucesso!');
                    validarFormulario();
                }
            })
            .fail(function(xhr) {
                toastr.error('Erro ao salvar rascunho');
                console.error(xhr.responseText);
            })
            .always(function() {
                $('#btn-salvar-rascunho').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Salvar Rascunho');
            });
    }

    function validarFormulario() {
        const tipo = $('#tipo').val();
        const ementa = $('#ementa').val();
        const modelo = $('#modelo').val();

        const valido = tipo && ementa && modelo && proposicaoId;
        $('#btn-continuar').prop('disabled', !valido);
    }
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
</style>
@endpush