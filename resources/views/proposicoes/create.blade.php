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

                        <!-- Ementa -->
                        <div class="mb-4" id="ementa-container" style="display: none;">
                            <label for="ementa" class="form-label required">Ementa da Proposição</label>
                            <textarea name="ementa" id="ementa" class="form-control" rows="3" placeholder="Descreva resumidamente o objetivo da proposição..." required></textarea>
                            <div class="form-text">
                                Descreva de forma clara e objetiva o que a proposição pretende regulamentar ou modificar.
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

                        <!-- Geração via IA -->
                        <div class="mb-4" id="ia-container" style="display: none;">
                            <div class="card border-primary">
                                <div class="card-header bg-light-primary">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-robot text-primary me-2"></i>
                                        Geração Automática via IA
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-3">
                                        Use inteligência artificial para gerar automaticamente o texto da proposição baseado na ementa fornecida.
                                    </p>
                                    <div class="d-flex gap-2 align-items-center">
                                        <button type="button" class="btn btn-primary" id="btn-gerar-ia">
                                            <i class="fas fa-magic me-2"></i>Gerar Texto via IA
                                        </button>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="usar_ia" name="usar_ia" value="1">
                                            <label class="form-check-label" for="usar_ia">
                                                Usar IA para gerar texto
                                            </label>
                                        </div>
                                    </div>
                                    <div id="ia-status" class="mt-3" style="display: none;">
                                        <div class="alert alert-info">
                                            <i class="fas fa-spinner fa-spin me-2"></i>
                                            Gerando texto via IA, aguarde...
                                        </div>
                                    </div>
                                </div>
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
    
    // Chaves para localStorage
    const STORAGE_KEY = 'proposicao_form_data';
    const AI_TEXT_KEY = 'proposicao_ai_text';
    
    // Função para salvar dados no localStorage
    function salvarDadosFormulario() {
        const dados = {
            tipo: $('#tipo').val(),
            ementa: $('#ementa').val(),
            modelo: $('#modelo').val(),
            usar_ia: $('#usar_ia').is(':checked'),
            timestamp: Date.now()
        };
        
        localStorage.setItem(STORAGE_KEY, JSON.stringify(dados));
        
        // Salvar texto da IA separadamente se existir
        if (window.textoGeradoIA) {
            localStorage.setItem(AI_TEXT_KEY, window.textoGeradoIA);
        }
    }
    
    // Função para carregar dados do localStorage
    function carregarDadosFormulario() {
        try {
            const dadosString = localStorage.getItem(STORAGE_KEY);
            if (!dadosString) return false;
            
            const dados = JSON.parse(dadosString);
            console.log('Carregando dados salvos:', dados);
            
            // Verificar se os dados não são muito antigos (1 hora)
            const agora = Date.now();
            const umHora = 60 * 60 * 1000;
            
            if (agora - dados.timestamp > umHora) {
                console.log('Dados expirados, limpando...');
                limparDadosFormulario();
                return false;
            }
            
            // Restaurar campos
            if (dados.tipo) {
                console.log('Restaurando tipo:', dados.tipo);
                $('#tipo').val(dados.tipo).trigger('change');
                
                // Mostrar containers que dependem do tipo
                $('#ementa-container').show();
                $('#modelo-container').show();
                $('#ia-container').show();
                
                // Carregar modelos para o tipo selecionado
                carregarModelos(dados.tipo);
                
                // Aguardar um pouco para os modelos serem carregados
                setTimeout(() => {
                    if (dados.modelo) {
                        console.log('Restaurando modelo:', dados.modelo);
                        $('#modelo').val(dados.modelo).trigger('change');
                    }
                }, 1000); // Aumentei o tempo para garantir que os modelos sejam carregados
            }
            
            if (dados.ementa) {
                console.log('Restaurando ementa:', dados.ementa.substring(0, 50) + '...');
                $('#ementa').val(dados.ementa);
            }
            
            if (dados.usar_ia) {
                console.log('Restaurando usar_ia:', dados.usar_ia);
                $('#usar_ia').prop('checked', true);
            }
            
            // Restaurar texto da IA se existir
            const textoIA = localStorage.getItem(AI_TEXT_KEY);
            if (textoIA) {
                window.textoGeradoIA = textoIA;
                mostrarPreviewTextoIA(textoIA);
            }
            
            // Validar formulário após restaurar dados
            setTimeout(() => {
                validarFormulario();
            }, 600);
            
            return true;
        } catch (e) {
            console.warn('Erro ao carregar dados do formulário:', e);
            limparDadosFormulario();
            return false;
        }
    }
    
    // Função para limpar dados do localStorage
    function limparDadosFormulario() {
        localStorage.removeItem(STORAGE_KEY);
        localStorage.removeItem(AI_TEXT_KEY);
        window.textoGeradoIA = null;
    }
    
    // Carregar dados salvos na inicialização
    carregarDadosFormulario();

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
            $('#ementa-container').show();
            $('#modelo-container').show();
            $('#ia-container').show();
        } else {
            $('#ementa-container').hide();
            $('#modelo-container').hide();
            $('#ia-container').hide();
            $('#ementa').val('');
            $('#modelo').val('');
            $('#usar_ia').prop('checked', false);
            $('#btn-continuar').prop('disabled', true);
        }
        
        // Validar formulário quando tipo for alterado
        validarFormulario();
    });

    // Validar se pode continuar
    $('#modelo, #ementa').on('change keyup', function() {
        validarFormulario();
    });

    // Funcionalidade de geração via IA
    $('#btn-gerar-ia').on('click', function() {
        gerarTextoViaIA();
    });

    // Alternar entre usar IA e não usar
    $('#usar_ia').on('change', function() {
        console.log('DEBUG: Checkbox usar_ia mudou para:', $(this).is(':checked'));
        validarFormulario();
        salvarDadosFormulario(); // Auto-salvar quando mudar
    });

    // Auto-salvar quando campos importantes mudarem
    $('#tipo').on('change', function() {
        salvarDadosFormulario();
    });
    
    $('#ementa').on('input keyup blur', function() {
        // Debounce para evitar muitas chamadas
        clearTimeout(window.ementaTimeout);
        window.ementaTimeout = setTimeout(() => {
            salvarDadosFormulario();
        }, 500);
    });
    
    $('#modelo').on('change', function() {
        salvarDadosFormulario();
    });

    // Salvar rascunho
    $('#btn-salvar-rascunho').on('click', function() {
        salvarRascunho();
    });

    // Continuar para próxima etapa
    $('#form-criar-proposicao').on('submit', function(e) {
        e.preventDefault();
        
        const usarIA = $('#usar_ia').is(':checked');
        const modeloId = $('#modelo').val();
        
        console.log('DEBUG: Form submit initiated', {
            usarIA: usarIA,
            modeloId: modeloId,
            temTextoIA: !!window.textoGeradoIA,
            textoIA: window.textoGeradoIA ? window.textoGeradoIA.substring(0, 50) + '...' : 'null',
            proposicaoId: proposicaoId
        });
        
        // Só exigir modelo se NÃO estiver usando IA
        if (!usarIA && !modeloId) {
            alert('Selecione um modelo para continuar.');
            return;
        }
        
        // Se usar IA, verificar se há texto gerado
        if (usarIA && !window.textoGeradoIA) {
            alert('Gere o texto via IA antes de continuar.');
            return;
        }
        
        if (proposicaoId) {
            // Já tem proposição salva, continuar direto
            console.log('Debug: Proposição já existe', {
                proposicaoId: proposicaoId,
                usarIA: usarIA,
                temTextoIA: !!window.textoGeradoIA,
                modeloId: modeloId
            });
            
            if (usarIA && window.textoGeradoIA) {
                // Se usou IA, ir direto ao editor OnlyOffice para parlamentares
                console.log('Debug: Redirecionando para OnlyOffice com IA');
                // Limpar dados salvos pois vamos continuar
                limparDadosFormulario();
                window.location.href = `/proposicoes/${proposicaoId}/onlyoffice/editor-parlamentar?ai_content=true`;
            } else {
                // Fluxo normal - preencher modelo
                console.log('Debug: Redirecionando para preencher modelo, modeloId:', modeloId);
                if (!modeloId) {
                    alert('Erro: Modelo não selecionado para fluxo tradicional');
                    return;
                }
                // Limpar dados salvos pois vamos continuar
                limparDadosFormulario();
                window.location.href = `/proposicoes/${proposicaoId}/preencher-modelo/${modeloId}`;
            }
        } else {
            // Salvar rascunho primeiro, depois continuar
            $('#btn-continuar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Salvando...');
            
            const dados = {
                tipo: $('#tipo').val(),
                ementa: $('#ementa').val() || 'Proposição em elaboração',
                usar_ia: usarIA ? 1 : 0,
                texto_ia: usarIA ? window.textoGeradoIA : null,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            $.post('/proposicoes/salvar-rascunho', dados)
                .done(function(response) {
                    if (response.success) {
                        proposicaoId = response.proposicao_id;
                        console.log('Debug: Proposição salva com sucesso', {
                            proposicaoId: proposicaoId,
                            usarIA: usarIA,
                            temTextoIA: !!window.textoGeradoIA,
                            modeloId: modeloId
                        });
                        
                        if (usarIA && window.textoGeradoIA) {
                            // Se usou IA, ir direto ao editor OnlyOffice para parlamentares
                            console.log('Debug: Redirecionando para OnlyOffice com IA após salvar');
                            // Limpar dados salvos pois vamos continuar
                            limparDadosFormulario();
                            window.location.href = `/proposicoes/${proposicaoId}/onlyoffice/editor-parlamentar?ai_content=true`;
                        } else {
                            // Fluxo normal - preencher modelo
                            console.log('Debug: Redirecionando para preencher modelo após salvar, modeloId:', modeloId);
                            if (!modeloId) {
                                alert('Erro: Modelo não selecionado para fluxo tradicional após salvar');
                                return;
                            }
                            // Limpar dados salvos pois vamos continuar
                            limparDadosFormulario();
                            window.location.href = `/proposicoes/${proposicaoId}/preencher-modelo/${modeloId}`;
                        }
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
            ementa: $('#ementa').val() || 'Proposição em elaboração',
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
        const ementa = $('#ementa').val();
        const modelo = $('#modelo').val();
        const usarIA = $('#usar_ia').is(':checked');

        // Permitir continuar se tipo, ementa e modelo estão preenchidos
        // OU se vai usar IA (tipo e ementa suficientes)
        const valido = tipo && ementa && (modelo || usarIA);
        $('#btn-continuar').prop('disabled', !valido);
        
        // Mostrar/esconder seleção de modelo baseado no uso de IA
        if (usarIA) {
            $('#modelo-container').slideUp();
        } else {
            $('#modelo-container').slideDown();
        }
    }

    // Função para gerar texto via IA
    function gerarTextoViaIA() {
        const tipo = $('#tipo').val();
        const ementa = $('#ementa').val();

        if (!tipo || !ementa) {
            toastr.warning('Selecione o tipo de proposição e preencha a ementa');
            return;
        }

        // Mostrar status de carregamento
        $('#ia-status').show();
        $('#btn-gerar-ia').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Gerando...');

        // Fazer requisição para gerar texto
        $.post('/proposicoes/gerar-texto-ia', {
            tipo: tipo,
            ementa: ementa,
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            $('#ia-status').hide();
            
            if (response.success) {
                toastr.success('Texto gerado via IA com sucesso!');
                $('#usar_ia').prop('checked', true);
                
                // Salvar o texto gerado para usar depois
                window.textoGeradoIA = response.texto;
                
                // Salvar dados do formulário incluindo texto da IA
                salvarDadosFormulario();
                
                // Validar formulário novamente
                validarFormulario();
                
                // Mostrar preview se possível
                if (response.texto) {
                    mostrarPreviewTextoIA(response.texto);
                }
            } else {
                toastr.error('Erro ao gerar texto: ' + (response.message || 'Erro desconhecido'));
            }
        })
        .fail(function(xhr) {
            $('#ia-status').hide();
            console.error('Erro na requisição:', xhr);
            toastr.error('Erro ao conectar com o serviço de IA');
        })
        .always(function() {
            $('#btn-gerar-ia').prop('disabled', false).html('<i class="fas fa-magic me-2"></i>Gerar Texto via IA');
        });
    }

    // Função para mostrar preview do texto gerado
    function mostrarPreviewTextoIA(texto) {
        const maxLength = 200;
        const preview = texto.length > maxLength ? texto.substring(0, maxLength) + '...' : texto;
        
        $('#ia-status').html(`
            <div class="alert alert-success">
                <h6><i class="fas fa-check me-2"></i>Texto gerado com sucesso!</h6>
                <p class="mb-2"><strong>Preview:</strong></p>
                <div class="bg-light p-2 rounded small">${preview}</div>
                <small class="text-muted mt-2 d-block">
                    Texto completo será usado na próxima etapa (${texto.length} caracteres)
                </small>
            </div>
        `).show();
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