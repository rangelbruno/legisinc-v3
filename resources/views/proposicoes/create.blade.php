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
                            <select name="tipo" id="tipo" class="form-select" data-control="select2" data-placeholder="Selecione o tipo de proposição" required>
                                <option value="">Selecione o tipo de proposição</option>
                                @foreach($tipos as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                Escolha o tipo adequado conforme a natureza da proposição.
                            </div>
                        </div>

                        <!-- Modelo -->
                        <div class="mb-4" id="modelo-container" style="display: none;">
                            <label for="modelo" class="form-label required">Selecionar Modelo</label>
                            <select name="modelo" id="modelo" class="form-select" data-control="select2" data-placeholder="Selecione um modelo">
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

    // Inicializar Select2
    $('#tipo').select2({
        width: '100%',
        placeholder: 'Selecione o tipo de proposição',
        allowClear: false,
        minimumResultsForSearch: 3
    });

    $('#modelo').select2({
        width: '100%',
        placeholder: 'Selecione um modelo',
        allowClear: false,
        minimumResultsForSearch: 3
    });

    // Carregar modelos quando tipo for selecionado
    $('#tipo').on('change', function() {
        const tipo = $(this).val();
        
        if (tipo) {
            carregarModelos(tipo);
            $('#modelo-container').show();
        } else {
            $('#modelo-container').hide();
            $('#modelo').val(''); // Limpar seleção de modelo
            $('#btn-continuar').prop('disabled', true);
        }
        
        // Validar formulário quando tipo for alterado
        validarFormulario();
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
        
        const modeloId = $('#modelo').val();
        if (!modeloId) {
            alert('Selecione um modelo para continuar.');
            return;
        }
        
        if (proposicaoId) {
            // Já tem proposição salva, continuar direto
            window.location.href = `/proposicoes/${proposicaoId}/preencher-modelo/${modeloId}`;
        } else {
            // Salvar rascunho primeiro, depois continuar
            $('#btn-continuar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Salvando...');
            
            const dados = {
                tipo: $('#tipo').val(),
                ementa: 'Proposição em elaboração', // Ementa padrão temporária
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            $.post('/proposicoes/salvar-rascunho', dados)
                .done(function(response) {
                    if (response.success) {
                        proposicaoId = response.proposicao_id;
                        window.location.href = `/proposicoes/${proposicaoId}/preencher-modelo/${modeloId}`;
                    }
                })
                .fail(function(xhr) {
                    alert('Erro ao salvar rascunho. Tente novamente.');
                    console.error(xhr.responseText);
                })
                .always(function() {
                    $('#btn-continuar').prop('disabled', false).html('<i class="fas fa-arrow-right me-2"></i>Continuar');
                });
        }
    });

    function carregarModelos(tipo) {
        $('#modelo').html('<option value="">Carregando...</option>');
        
        $.get(`/proposicoes/modelos/${tipo}`)
            .done(function(modelos) {
                console.log('Dados recebidos:', modelos);
                console.log('Tipo dos dados:', typeof modelos);
                console.log('É array?', Array.isArray(modelos));
                
                let options = '<option value="">Selecione um modelo</option>';
                
                // Verificar se é um array
                if (Array.isArray(modelos)) {
                    modelos.forEach(function(modelo) {
                        options += `<option value="${modelo.id}">${modelo.nome}</option>`;
                    });
                } else {
                    console.error('Dados recebidos não são um array:', modelos);
                    options = '<option value="">Erro: formato de dados inválido</option>';
                }
                
                $('#modelo').html(options);
            })
            .fail(function(xhr, status, error) {
                console.error('Erro ao carregar modelos:', xhr, status, error);
                $('#modelo').html('<option value="">Erro ao carregar modelos</option>');
                
                let errorMessage = 'Erro ao carregar modelos';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage += ': ' + xhr.responseJSON.message;
                } else if (xhr.status) {
                    errorMessage += ' (Status: ' + xhr.status + ')';
                }
                
                toastr.error(errorMessage);
            });
    }

    function salvarRascunho() {
        const dados = {
            tipo: $('#tipo').val(),
            ementa: 'Proposição em elaboração', // Ementa padrão temporária
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        if (!dados.tipo) {
            toastr.warning('Selecione o tipo de proposição');
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
        const modelo = $('#modelo').val();

        // Permitir continuar se tipo e modelo estão selecionados
        const valido = tipo && modelo;
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

/* Estilos Select2 Moderno - Tema Padrão */
.select2-container {
    width: 100% !important;
    font-family: inherit !important;
}

/* Container principal do select */
.select2-selection--single {
    height: 58px !important;
    border: 2px solid #e1e5e9 !important;
    border-radius: 12px !important;
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%) !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    position: relative !important;
    overflow: hidden !important;
}

.select2-selection--single:hover {
    border-color: #007bff !important;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2) !important;
    transform: translateY(-1px) !important;
}

.select2-container--focus .select2-selection--single,
.select2-container--open .select2-selection--single {
    border-color: #007bff !important;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1), 0 4px 15px rgba(0, 123, 255, 0.2) !important;
    transform: translateY(-1px) !important;
}

/* Texto renderizado */
.select2-selection__rendered {
    padding: 0 20px !important;
    line-height: 54px !important;
    color: #2c3e50 !important;
    font-weight: 500 !important;
    font-size: 1rem !important;
}

.select2-selection__placeholder {
    color: #8b9cb5 !important;
    font-weight: 400 !important;
    font-style: italic !important;
}

/* Seta do dropdown */
.select2-selection__arrow {
    height: 58px !important;
    width: 20px !important;
    top: 0 !important;
    right: 15px !important;
}

.select2-selection__arrow b {
    border-color: #007bff transparent transparent transparent !important;
    border-style: solid !important;
    border-width: 7px 7px 0 7px !important;
    height: 0 !important;
    left: 50% !important;
    margin-left: -7px !important;
    margin-top: -3px !important;
    position: absolute !important;
    top: 50% !important;
    width: 0 !important;
    transition: transform 0.3s ease !important;
}

.select2-container--open .select2-selection__arrow b {
    transform: rotate(180deg) !important;
}

/* Dropdown */
.select2-dropdown {
    border: 2px solid #007bff !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 30px rgba(0, 123, 255, 0.2) !important;
    background: white !important;
    margin-top: 5px !important;
    overflow: hidden !important;
    animation: fadeInDown 0.3s ease !important;
}

/* Campo de busca */
.select2-search--dropdown {
    padding: 16px !important;
    background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%) !important;
    border-bottom: 1px solid #e1e5e9 !important;
}

.select2-search__field {
    border: 2px solid #e1e5e9 !important;
    border-radius: 8px !important;
    padding: 12px 16px !important;
    font-size: 1rem !important;
    width: 100% !important;
    transition: all 0.3s ease !important;
    background: white !important;
}

.select2-search__field:focus {
    border-color: #007bff !important;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1) !important;
    outline: none !important;
}

/* Opções do dropdown */
.select2-results__options {
    max-height: 280px !important;
    padding: 8px 0 !important;
}

.select2-results__option {
    padding: 14px 20px !important;
    font-size: 1rem !important;
    color: #2c3e50 !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    border-left: 4px solid transparent !important;
    position: relative !important;
}

.select2-results__option--highlighted {
    background: linear-gradient(90deg, rgba(0, 123, 255, 0.12) 0%, rgba(0, 123, 255, 0.06) 100%) !important;
    color: #007bff !important;
    border-left-color: #007bff !important;
}

.select2-results__option[aria-selected="true"] {
    background: linear-gradient(90deg, rgba(40, 167, 69, 0.12) 0%, rgba(40, 167, 69, 0.06) 100%) !important;
    color: #28a745 !important;
    border-left-color: #28a745 !important;
    font-weight: 600 !important;
}

.select2-results__option[aria-selected="true"]::after {
    content: '✓' !important;
    position: absolute !important;
    right: 20px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    color: #28a745 !important;
    font-weight: bold !important;
    font-size: 1.1em !important;
}

/* Mensagens do dropdown */
.select2-results__message {
    padding: 16px 20px !important;
    color: #8b9cb5 !important;
    text-align: center !important;
    font-style: italic !important;
    font-size: 0.95rem !important;
}

/* Animações */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsividade */
@media (max-width: 768px) {
    .select2-selection--single {
        height: 52px !important;
    }
    
    .select2-selection__rendered {
        line-height: 48px !important;
        padding: 0 16px !important;
    }
    
    .select2-selection__arrow {
        height: 52px !important;
        right: 12px !important;
    }
}
</style>
@endpush