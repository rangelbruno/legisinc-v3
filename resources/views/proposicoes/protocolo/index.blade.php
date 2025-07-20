@extends('components.layouts.app')

@section('title', 'Protocolo de Proposições')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-5">
        <div>
            <h1 class="h3 text-gray-800 mb-0">Protocolo de Proposições</h1>
            <p class="text-muted">Proposições aguardando protocolação e distribuição</p>
        </div>
        <div>
            <a href="{{ route('proposicoes.protocolos-hoje') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-calendar-day me-2"></i>Protocolos Hoje
            </a>
            <a href="{{ route('proposicoes.estatisticas-protocolo') }}" class="btn btn-outline-info">
                <i class="fas fa-chart-bar me-2"></i>Estatísticas
            </a>
        </div>
    </div>

    <!-- Estatísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $proposicoes->count() }}</h4>
                            <p class="mb-0 opacity-75">Aguardando Protocolo</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $proposicoes->where('urgencia', '!=', 'normal')->count() }}</h4>
                            <p class="mb-0 opacity-75">Com Urgência</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $proposicoes->where('created_at', '>=', today())->count() }}</h4>
                            <p class="mb-0 opacity-75">Recebidas Hoje</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-check fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $proposicoes->where('data_assinatura', '<=', now()->subDays(2))->count() }}</h4>
                            <p class="mb-0 opacity-75">Aguardam +2 Dias</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-hourglass-half fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tipo de Proposição</label>
                    <select id="filtro-tipo" class="form-select">
                        <option value="">Todos os tipos</option>
                        <option value="PL">Projeto de Lei</option>
                        <option value="PLP">Projeto de Lei Complementar</option>
                        <option value="PEC">Proposta de Emenda Constitucional</option>
                        <option value="PDC">Projeto de Decreto Legislativo</option>
                        <option value="PRC">Projeto de Resolução</option>
                        <option value="mocao">Moção</option>
                        <option value="indicacao">Indicação</option>
                        <option value="requerimento">Requerimento</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Urgência</label>
                    <select id="filtro-urgencia" class="form-select">
                        <option value="">Todas as urgências</option>
                        <option value="urgentissima">Urgentíssima</option>
                        <option value="urgente">Urgente</option>
                        <option value="normal">Normal</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Autor</label>
                    <input type="text" id="filtro-autor" class="form-control" placeholder="Nome do autor">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="button" class="btn btn-primary" id="btn-filtrar">
                            <i class="fas fa-search me-2"></i>Filtrar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btn-limpar">
                            <i class="fas fa-times me-2"></i>Limpar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Proposições -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-clipboard-list me-2"></i>
                Proposições para Protocolação ({{ $proposicoes->total() }})
            </h5>
        </div>
        <div class="card-body p-0">
            @if($proposicoes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="60">Urgência</th>
                                <th>Proposição</th>
                                <th>Autor</th>
                                <th>Ementa</th>
                                <th>Data Assinatura</th>
                                <th>Tempo Espera</th>
                                <th width="120">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposicoes as $proposicao)
                            <tr class="{{ $proposicao->urgencia !== 'normal' ? 'table-warning' : '' }}">
                                <td class="text-center">
                                    @if($proposicao->urgencia === 'urgentissima')
                                        <span class="badge bg-danger" title="Urgentíssima">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </span>
                                    @elseif($proposicao->urgencia === 'urgente')
                                        <span class="badge bg-warning" title="Urgente">
                                            <i class="fas fa-exclamation"></i>
                                        </span>
                                    @else
                                        <span class="badge bg-secondary" title="Normal">
                                            <i class="fas fa-circle"></i>
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <span class="badge bg-light text-dark me-2">{{ $proposicao->tipo }}</span>
                                        <strong>{{ $proposicao->titulo ?? 'Sem título' }}</strong>
                                        @if($proposicao->numero_temporario)
                                            <br><small class="text-muted">Nº {{ $proposicao->numero_temporario }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $proposicao->autor->name }}</strong>
                                        <br><small class="text-muted">{{ $proposicao->autor->cargo_atual ?? 'Parlamentar' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 300px;" title="{{ $proposicao->ementa }}">
                                        {{ $proposicao->ementa }}
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        {{ $proposicao->data_assinatura ? $proposicao->data_assinatura->format('d/m/Y') : 'N/A' }}
                                        @if($proposicao->data_assinatura)
                                            <br><small class="text-muted">{{ $proposicao->data_assinatura->format('H:i') }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($proposicao->data_assinatura)
                                        @php
                                            $diasEspera = $proposicao->data_assinatura->diffInDays(now());
                                            $horasEspera = $proposicao->data_assinatura->diffInHours(now());
                                        @endphp
                                        
                                        @if($diasEspera > 0)
                                            <span class="badge bg-{{ $diasEspera > 2 ? 'danger' : ($diasEspera > 1 ? 'warning' : 'success') }}">
                                                {{ $diasEspera }} {{ $diasEspera == 1 ? 'dia' : 'dias' }}
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                {{ $horasEspera }}h
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('proposicoes.show', $proposicao) }}" 
                                           class="btn btn-outline-info" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('proposicoes.protocolar.show', $proposicao) }}" 
                                           class="btn btn-primary" title="Protocolar">
                                            <i class="fas fa-clipboard-list"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                Mostrando {{ $proposicoes->firstItem() }} a {{ $proposicoes->lastItem() }} 
                                de {{ $proposicoes->total() }} proposições
                            </small>
                        </div>
                        <div>
                            {{ $proposicoes->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-check fa-3x text-success mb-3"></i>
                    <h5 class="text-muted">Nenhuma proposição aguardando protocolo</h5>
                    <p class="text-muted">Todas as proposições assinadas já foram protocoladas.</p>
                    <a href="{{ route('proposicoes.protocolos-hoje') }}" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-day me-2"></i>Ver Protocolos de Hoje
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Ações em Lote -->
    @if($proposicoes->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-tasks me-2"></i>
                    Ações em Lote
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="select-all">
                            <label class="form-check-label" for="select-all">
                                <strong>Selecionar todas as proposições visíveis</strong>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-primary" id="btn-protocolar-lote" disabled>
                            <i class="fas fa-clipboard-list me-2"></i>Protocolar Selecionadas
                        </button>
                    </div>
                </div>
                
                <div id="lote-info" class="mt-3" style="display: none;">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="lote-count">0</span> proposições selecionadas para protocolação em lote.
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Filtros
    $('#btn-filtrar').on('click', function() {
        aplicarFiltros();
    });

    $('#btn-limpar').on('click', function() {
        $('#filtro-tipo').val('');
        $('#filtro-urgencia').val('');
        $('#filtro-autor').val('');
        aplicarFiltros();
    });

    // Enter para filtrar
    $('#filtro-autor').on('keypress', function(e) {
        if (e.which === 13) {
            aplicarFiltros();
        }
    });

    // Seleção em lote
    $('#select-all').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.row-checkbox').prop('checked', isChecked);
        atualizarLote();
    });

    $(document).on('change', '.row-checkbox', function() {
        atualizarLote();
    });

    $('#btn-protocolar-lote').on('click', function() {
        const selecionadas = $('.row-checkbox:checked').length;
        
        if (selecionadas === 0) {
            toastr.warning('Selecione pelo menos uma proposição');
            return;
        }

        if (confirm(`Confirma a protocolação em lote de ${selecionadas} proposições?`)) {
            // Implementar protocolação em lote
            toastr.info('Funcionalidade em desenvolvimento');
        }
    });

    function aplicarFiltros() {
        const params = new URLSearchParams(window.location.search);
        
        const tipo = $('#filtro-tipo').val();
        const urgencia = $('#filtro-urgencia').val();
        const autor = $('#filtro-autor').val();

        if (tipo) params.set('tipo', tipo);
        else params.delete('tipo');

        if (urgencia) params.set('urgencia', urgencia);
        else params.delete('urgencia');

        if (autor) params.set('autor', autor);
        else params.delete('autor');

        params.delete('page'); // Reset pagination

        window.location.search = params.toString();
    }

    function atualizarLote() {
        const selecionadas = $('.row-checkbox:checked').length;
        const total = $('.row-checkbox').length;
        
        $('#lote-count').text(selecionadas);
        
        if (selecionadas > 0) {
            $('#lote-info').show();
            $('#btn-protocolar-lote').prop('disabled', false);
        } else {
            $('#lote-info').hide();
            $('#btn-protocolar-lote').prop('disabled', true);
        }

        // Atualizar estado do "selecionar todos"
        if (selecionadas === total && total > 0) {
            $('#select-all').prop('indeterminate', false).prop('checked', true);
        } else if (selecionadas > 0) {
            $('#select-all').prop('indeterminate', true).prop('checked', false);
        } else {
            $('#select-all').prop('indeterminate', false).prop('checked', false);
        }
    }

    // Aplicar filtros da URL
    const urlParams = new URLSearchParams(window.location.search);
    $('#filtro-tipo').val(urlParams.get('tipo') || '');
    $('#filtro-urgencia').val(urlParams.get('urgencia') || '');
    $('#filtro-autor').val(urlParams.get('autor') || '');

    // Adicionar checkboxes às linhas da tabela
    $('tbody tr').each(function(index) {
        const $tr = $(this);
        const firstTd = $tr.find('td:first');
        firstTd.prepend(`
            <div class="form-check me-2" style="display: inline-block;">
                <input class="form-check-input row-checkbox" type="checkbox" value="${index}">
            </div>
        `);
    });

    // Auto-refresh a cada 60 segundos
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            window.location.reload();
        }
    }, 60000);

    // Tooltips
    $('[title]').tooltip();
});
</script>
@endpush

@push('styles')
<style>
.table td {
    vertical-align: middle;
}

.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.table-hover tbody tr:hover {
    background-color: var(--bs-light);
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1);
}

.badge {
    font-size: 0.75rem;
}

.form-check-input:indeterminate {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10h8'/%3e%3c/svg%3e");
}
</style>
@endpush