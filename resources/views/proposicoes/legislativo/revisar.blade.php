@extends('components.layouts.app')

@section('title', 'Revisar Proposição')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-5">
        <div>
            <h1 class="h3 text-gray-800 mb-0">Revisão Técnica</h1>
            <p class="text-muted">{{ $proposicao->tipo }} - {{ $proposicao->ementa }}</p>
        </div>
        <div>
            <a href="{{ route('proposicoes.revisar') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Voltar à Lista
            </a>
            <a href="{{ route('proposicoes.show', $proposicao) }}" class="btn btn-outline-info">
                <i class="fas fa-eye me-2"></i>Visualizar
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Dados da Proposição -->
        <div class="col-lg-8">
            <!-- Informações Básicas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Dados da Proposição
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold" width="120">Tipo:</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $proposicao->tipo }}</span>
                                        {{ $proposicao->tipo_formatado }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Autor:</td>
                                    <td>{{ $proposicao->autor->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Data:</td>
                                    <td>{{ $proposicao->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                        <span class="badge bg-{{ $proposicao->status_cor }}">
                                            {{ $proposicao->status_formatado }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <strong>Ementa:</strong>
                                <p class="mt-2">{{ $proposicao->ementa }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Texto da Proposição -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-text text-primary me-2"></i>
                        Texto da Proposição
                    </h5>
                </div>
                <div class="card-body">
                    <div class="border rounded p-3" style="max-height: 500px; overflow-y: auto; background-color: #f8f9fa;">
                        {!! $proposicao->conteudo ?: '<p class="text-muted">Nenhum conteúdo disponível</p>' !!}
                    </div>
                </div>
            </div>

            <!-- Análise Técnica -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clipboard-check text-warning me-2"></i>
                        Análise Técnica
                    </h5>
                </div>
                <div class="card-body">
                    <form id="form-analise-tecnica">
                        @csrf
                        
                        <!-- Checkboxes de Análise -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="analise_constitucionalidade" 
                                           id="constitucionalidade" value="1"
                                           {{ $proposicao->analise_constitucionalidade ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="constitucionalidade">
                                        <i class="fas fa-balance-scale text-primary me-2"></i>
                                        Constitucionalidade
                                    </label>
                                    <div class="form-text">A proposição está em conformidade com a Constituição</div>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="analise_juridicidade" 
                                           id="juridicidade" value="1"
                                           {{ $proposicao->analise_juridicidade ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="juridicidade">
                                        <i class="fas fa-gavel text-success me-2"></i>
                                        Juridicidade
                                    </label>
                                    <div class="form-text">A proposição está em conformidade com o ordenamento jurídico</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="analise_regimentalidade" 
                                           id="regimentalidade" value="1"
                                           {{ $proposicao->analise_regimentalidade ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="regimentalidade">
                                        <i class="fas fa-book text-info me-2"></i>
                                        Regimentalidade
                                    </label>
                                    <div class="form-text">A proposição está em conformidade com o Regimento Interno</div>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="analise_tecnica_legislativa" 
                                           id="tecnica_legislativa" value="1"
                                           {{ $proposicao->analise_tecnica_legislativa ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="tecnica_legislativa">
                                        <i class="fas fa-tools text-warning me-2"></i>
                                        Técnica Legislativa
                                    </label>
                                    <div class="form-text">A proposição segue as normas de técnica legislativa</div>
                                </div>
                            </div>
                        </div>

                        <!-- Parecer Técnico -->
                        <div class="mb-4">
                            <label for="parecer_tecnico" class="form-label required">Parecer Técnico</label>
                            <textarea name="parecer_tecnico" id="parecer_tecnico" class="form-control" rows="6" 
                                      placeholder="Digite o parecer técnico detalhado..." required>{{ $proposicao->parecer_tecnico }}</textarea>
                            <div class="form-text">
                                Descreva as observações, correções necessárias ou aprovação da proposição.
                            </div>
                        </div>

                        <!-- Observações Internas -->
                        <div class="mb-4">
                            <label for="observacoes_internas" class="form-label">Observações Internas</label>
                            <textarea name="observacoes_internas" id="observacoes_internas" class="form-control" rows="3" 
                                      placeholder="Observações para arquivo interno...">{{ $proposicao->observacoes_internas }}</textarea>
                            <div class="form-text">
                                Notas internas que não serão visualizadas pelo parlamentar.
                            </div>
                        </div>

                        <!-- Ação Final -->
                        <div class="mb-4">
                            <label class="form-label required">Decisão</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipo_retorno" 
                                               id="aprovar" value="aprovado_assinatura">
                                        <label class="form-check-label" for="aprovar">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <strong>Aprovar para Assinatura</strong>
                                            <div class="form-text">Todas as análises devem estar aprovadas</div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipo_retorno" 
                                               id="devolver" value="devolver_correcao">
                                        <label class="form-check-label" for="devolver">
                                            <i class="fas fa-times-circle text-danger me-2"></i>
                                            <strong>Devolver para Correção</strong>
                                            <div class="form-text">Proposição precisa de ajustes</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" id="btn-salvar-analise">
                                <i class="fas fa-save me-2"></i>Salvar Análise
                            </button>
                            <button type="submit" class="btn btn-primary" id="btn-processar-decisao">
                                <i class="fas fa-paper-plane me-2"></i>Processar Decisão
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Atual -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Status da Revisão
                    </h6>
                </div>
                <div class="card-body">
                    @if($proposicao->revisor_id === auth()->id())
                        <div class="alert alert-warning">
                            <i class="fas fa-user-edit me-2"></i>
                            <strong>Em revisão por você</strong>
                            <br><small>Iniciado em {{ $proposicao->updated_at->format('d/m/Y H:i') }}</small>
                        </div>
                    @elseif($proposicao->revisor_id)
                        <div class="alert alert-info">
                            <i class="fas fa-user-check me-2"></i>
                            <strong>Em revisão por:</strong> {{ $proposicao->revisor->name }}
                            <br><small>Iniciado em {{ $proposicao->updated_at->format('d/m/Y H:i') }}</small>
                        </div>
                    @else
                        <div class="alert alert-primary">
                            <i class="fas fa-clock me-2"></i>
                            <strong>Aguardando revisão</strong>
                            <br><small>Enviado em {{ $proposicao->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Progresso da Análise -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-tasks text-success me-2"></i>
                        Progresso da Análise
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $analises = [
                            'constitucionalidade' => $proposicao->analise_constitucionalidade,
                            'juridicidade' => $proposicao->analise_juridicidade,
                            'regimentalidade' => $proposicao->analise_regimentalidade,
                            'tecnica_legislativa' => $proposicao->analise_tecnica_legislativa
                        ];
                        $aprovadas = array_filter($analises);
                        $total = count($analises);
                        $progresso = $total > 0 ? (count($aprovadas) / $total) * 100 : 0;
                    @endphp

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Análises Aprovadas</span>
                            <span class="text-muted">{{ count($aprovadas) }}/{{ $total }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: {{ $progresso }}%"></div>
                        </div>
                    </div>

                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between py-1">
                            <span>Constitucionalidade</span>
                            @if($proposicao->analise_constitucionalidade === true)
                                <i class="fas fa-check-circle text-success"></i>
                            @elseif($proposicao->analise_constitucionalidade === false)
                                <i class="fas fa-times-circle text-danger"></i>
                            @else
                                <i class="fas fa-clock text-muted"></i>
                            @endif
                        </li>
                        <li class="d-flex justify-content-between py-1">
                            <span>Juridicidade</span>
                            @if($proposicao->analise_juridicidade === true)
                                <i class="fas fa-check-circle text-success"></i>
                            @elseif($proposicao->analise_juridicidade === false)
                                <i class="fas fa-times-circle text-danger"></i>
                            @else
                                <i class="fas fa-clock text-muted"></i>
                            @endif
                        </li>
                        <li class="d-flex justify-content-between py-1">
                            <span>Regimentalidade</span>
                            @if($proposicao->analise_regimentalidade === true)
                                <i class="fas fa-check-circle text-success"></i>
                            @elseif($proposicao->analise_regimentalidade === false)
                                <i class="fas fa-times-circle text-danger"></i>
                            @else
                                <i class="fas fa-clock text-muted"></i>
                            @endif
                        </li>
                        <li class="d-flex justify-content-between py-1">
                            <span>Técnica Legislativa</span>
                            @if($proposicao->analise_tecnica_legislativa === true)
                                <i class="fas fa-check-circle text-success"></i>
                            @elseif($proposicao->analise_tecnica_legislativa === false)
                                <i class="fas fa-times-circle text-danger"></i>
                            @else
                                <i class="fas fa-clock text-muted"></i>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Guia de Análise -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        Guia de Análise
                    </h6>
                </div>
                <div class="card-body">
                    <div class="accordion accordion-flush" id="guia-analise">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#flush-collapseOne">
                                    Constitucionalidade
                                </button>
                            </h2>
                            <div id="flush-collapseOne" class="accordion-collapse collapse" 
                                 data-bs-parent="#guia-analise">
                                <div class="accordion-body">
                                    <small>
                                        Verificar se a proposição está em conformidade com os princípios e normas 
                                        constitucionais, não violando direitos fundamentais ou competências.
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#flush-collapseTwo">
                                    Juridicidade
                                </button>
                            </h2>
                            <div id="flush-collapseTwo" class="accordion-collapse collapse" 
                                 data-bs-parent="#guia-analise">
                                <div class="accordion-body">
                                    <small>
                                        Analisar se a proposição está em harmonia com o ordenamento jurídico vigente 
                                        e não conflita com outras normas.
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#flush-collapseThree">
                                    Regimentalidade
                                </button>
                            </h2>
                            <div id="flush-collapseThree" class="accordion-collapse collapse" 
                                 data-bs-parent="#guia-analise">
                                <div class="accordion-body">
                                    <small>
                                        Verificar se a proposição atende aos requisitos estabelecidos no 
                                        Regimento Interno da Casa Legislativa.
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#flush-collapseFour">
                                    Técnica Legislativa
                                </button>
                            </h2>
                            <div id="flush-collapseFour" class="accordion-collapse collapse" 
                                 data-bs-parent="#guia-analise">
                                <div class="accordion-body">
                                    <small>
                                        Analisar se a proposição segue as normas de redação, estrutura e 
                                        apresentação de textos legislativos.
                                    </small>
                                </div>
                            </div>
                        </div>
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
    // Salvar análise (sem finalizar)
    $('#btn-salvar-analise').on('click', function() {
        salvarAnalise();
    });

    // Processar decisão final
    $('#form-analise-tecnica').on('submit', function(e) {
        e.preventDefault();
        
        const tipoRetorno = $('input[name="tipo_retorno"]:checked').val();
        if (!tipoRetorno) {
            toastr.warning('Selecione uma decisão (Aprovar ou Devolver)');
            return;
        }

        if (tipoRetorno === 'aprovado_assinatura') {
            aprovarProposicao();
        } else {
            devolverProposicao();
        }
    });

    // Validação em tempo real
    $('input[type="checkbox"], input[type="radio"], textarea').on('change', function() {
        atualizarProgresso();
    });

    function salvarAnalise() {
        const dados = {
            analise_constitucionalidade: $('#constitucionalidade').is(':checked') ? 1 : 0,
            analise_juridicidade: $('#juridicidade').is(':checked') ? 1 : 0,
            analise_regimentalidade: $('#regimentalidade').is(':checked') ? 1 : 0,
            analise_tecnica_legislativa: $('#tecnica_legislativa').is(':checked') ? 1 : 0,
            parecer_tecnico: $('#parecer_tecnico').val(),
            observacoes_internas: $('#observacoes_internas').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $('#btn-salvar-analise').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Salvando...');

        $.post(`/proposicoes/{{ $proposicao->id }}/salvar-analise`, dados)
            .done(function(response) {
                if (response.success) {
                    toastr.success('Análise salva com sucesso!');
                }
            })
            .fail(function(xhr) {
                toastr.error('Erro ao salvar análise');
                console.error(xhr.responseText);
            })
            .always(function() {
                $('#btn-salvar-analise').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Salvar Análise');
            });
    }

    function aprovarProposicao() {
        // Verificar se todas as análises estão aprovadas
        const todasAprovadas = $('#constitucionalidade').is(':checked') && 
                              $('#juridicidade').is(':checked') && 
                              $('#regimentalidade').is(':checked') && 
                              $('#tecnica_legislativa').is(':checked');

        if (!todasAprovadas) {
            toastr.warning('Para aprovar, todas as análises técnicas devem estar marcadas como aprovadas');
            return;
        }

        const dados = {
            analise_constitucionalidade: 1,
            analise_juridicidade: 1,
            analise_regimentalidade: 1,
            analise_tecnica_legislativa: 1,
            parecer_tecnico: $('#parecer_tecnico').val(),
            observacoes_internas: $('#observacoes_internas').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        if (!dados.parecer_tecnico.trim()) {
            toastr.warning('O parecer técnico é obrigatório');
            return;
        }

        $('#btn-processar-decisao').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Aprovando...');

        $.ajax({
            url: `/proposicoes/{{ $proposicao->id }}/aprovar`,
            method: 'PUT',
            data: dados,
            success: function(response) {
                if (response.success) {
                    toastr.success('Proposição aprovada para assinatura!');
                    setTimeout(() => {
                        window.location.href = '/proposicoes/revisar';
                    }, 2000);
                }
            },
            error: function(xhr) {
                toastr.error('Erro ao aprovar proposição');
                console.error(xhr.responseText);
            },
            complete: function() {
                $('#btn-processar-decisao').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Processar Decisão');
            }
        });
    }

    function devolverProposicao() {
        const dados = {
            parecer_tecnico: $('#parecer_tecnico').val(),
            observacoes_internas: $('#observacoes_internas').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        if (!dados.parecer_tecnico.trim()) {
            toastr.warning('O parecer técnico é obrigatório para devolver a proposição');
            return;
        }

        $('#btn-processar-decisao').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Devolvendo...');

        $.ajax({
            url: `/proposicoes/{{ $proposicao->id }}/devolver`,
            method: 'PUT',
            data: dados,
            success: function(response) {
                if (response.success) {
                    toastr.success('Proposição devolvida para correção!');
                    setTimeout(() => {
                        window.location.href = '/proposicoes/revisar';
                    }, 2000);
                }
            },
            error: function(xhr) {
                toastr.error('Erro ao devolver proposição');
                console.error(xhr.responseText);
            },
            complete: function() {
                $('#btn-processar-decisao').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Processar Decisão');
            }
        });
    }

    function atualizarProgresso() {
        const analises = [
            $('#constitucionalidade').is(':checked'),
            $('#juridicidade').is(':checked'),
            $('#regimentalidade').is(':checked'),
            $('#tecnica_legislativa').is(':checked')
        ];
        
        const aprovadas = analises.filter(Boolean).length;
        const progresso = (aprovadas / analises.length) * 100;
        
        $('.progress-bar').css('width', progresso + '%');
        $('.progress').next().find('span:last').text(`${aprovadas}/${analises.length}`);
    }

    // Auto-save a cada 30 segundos
    setInterval(function() {
        if ($('#parecer_tecnico').val().trim() || 
            $('input[type="checkbox"]:checked').length > 0) {
            salvarAnalise();
        }
    }, 30000);
});
</script>
@endpush

@push('styles')
<style>
.required:after {
    content: " *";
    color: red;
}

.accordion-button {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
}

.accordion-body {
    padding: 0.75rem 1rem;
}

.form-check-input:checked + .form-check-label {
    color: var(--bs-success);
}

.progress {
    height: 8px;
}

.table-borderless td {
    border: none;
    padding: 0.25rem 0.5rem;
}
</style>
@endpush