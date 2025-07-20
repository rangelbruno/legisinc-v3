@extends('components.layouts.app')

@section('title', 'Proposições para Revisão')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-5">
        <div>
            <h1 class="h3 text-gray-800 mb-0">Revisão Legislativa</h1>
            <p class="text-muted">Proposições aguardando análise técnica</p>
        </div>
        <div>
            <a href="{{ route('proposicoes.relatorio-legislativo') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-chart-bar me-2"></i>Relatório
            </a>
            <a href="{{ route('proposicoes.aguardando-protocolo') }}" class="btn btn-outline-success">
                <i class="fas fa-clipboard-list me-2"></i>Aguardando Protocolo
            </a>
        </div>
    </div>

    <!-- Estatísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $proposicoes->where('status', 'enviado_legislativo')->count() }}</h4>
                            <p class="mb-0 opacity-75">Aguardando Revisão</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $proposicoes->where('status', 'em_revisao')->count() }}</h4>
                            <p class="mb-0 opacity-75">Em Revisão</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-edit fa-2x opacity-75"></i>
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
                            <h4 class="mb-0">{{ $proposicoes->where('status', 'aprovado_assinatura')->count() }}</h4>
                            <p class="mb-0 opacity-75">Aprovadas Hoje</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $proposicoes->where('status', 'devolvido_correcao')->count() }}</h4>
                            <p class="mb-0 opacity-75">Devolvidas Hoje</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-times-circle fa-2x opacity-75"></i>
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
                    <label class="form-label">Status</label>
                    <select id="filtro-status" class="form-select">
                        <option value="">Todos os status</option>
                        <option value="enviado_legislativo">Aguardando Revisão</option>
                        <option value="em_revisao">Em Revisão</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select id="filtro-tipo" class="form-select">
                        <option value="">Todos os tipos</option>
                        <option value="PL">Projeto de Lei</option>
                        <option value="PLP">Projeto de Lei Complementar</option>
                        <option value="PEC">Proposta de Emenda Constitucional</option>
                        <option value="PDC">Projeto de Decreto Legislativo</option>
                        <option value="PRC">Projeto de Resolução</option>
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
                <i class="fas fa-list me-2"></i>
                Proposições para Revisão ({{ $proposicoes->total() }})
            </h5>
        </div>
        <div class="card-body p-0">
            @if($proposicoes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Proposição</th>
                                <th>Autor</th>
                                <th>Ementa</th>
                                <th>Data Envio</th>
                                <th>Status</th>
                                <th>Prioridade</th>
                                <th width="120">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposicoes as $proposicao)
                            <tr>
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
                                        {{ $proposicao->created_at->format('d/m/Y') }}
                                        <br><small class="text-muted">{{ $proposicao->created_at->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if($proposicao->status === 'enviado_legislativo')
                                        <span class="badge bg-primary">Aguardando Revisão</span>
                                    @elseif($proposicao->status === 'em_revisao')
                                        <span class="badge bg-warning">Em Revisão</span>
                                        @if($proposicao->revisor_id === auth()->id())
                                            <br><small class="text-success">Por você</small>
                                        @else
                                            <br><small class="text-muted">{{ $proposicao->revisor->name ?? 'Outro revisor' }}</small>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($proposicao->urgencia === 'urgentissima')
                                        <span class="badge bg-danger">Urgentíssima</span>
                                    @elseif($proposicao->urgencia === 'urgente')
                                        <span class="badge bg-warning">Urgente</span>
                                    @else
                                        <span class="badge bg-secondary">Normal</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('proposicoes.show', $proposicao) }}" 
                                           class="btn btn-outline-info" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($proposicao->status === 'enviado_legislativo' || 
                                            ($proposicao->status === 'em_revisao' && $proposicao->revisor_id === auth()->id()))
                                            <a href="{{ route('proposicoes.revisar.show', $proposicao) }}" 
                                               class="btn btn-primary" title="Revisar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="card-footer">
                    {{ $proposicoes->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma proposição para revisão</h5>
                    <p class="text-muted">Não há proposições aguardando análise técnica no momento.</p>
                </div>
            @endif
        </div>
    </div>
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
        $('#filtro-status').val('');
        $('#filtro-tipo').val('');
        $('#filtro-autor').val('');
        aplicarFiltros();
    });

    // Enter para filtrar
    $('#filtro-autor').on('keypress', function(e) {
        if (e.which === 13) {
            aplicarFiltros();
        }
    });

    function aplicarFiltros() {
        const params = new URLSearchParams(window.location.search);
        
        const status = $('#filtro-status').val();
        const tipo = $('#filtro-tipo').val();
        const autor = $('#filtro-autor').val();

        if (status) params.set('status', status);
        else params.delete('status');

        if (tipo) params.set('tipo', tipo);
        else params.delete('tipo');

        if (autor) params.set('autor', autor);
        else params.delete('autor');

        params.delete('page'); // Reset pagination

        window.location.search = params.toString();
    }

    // Aplicar filtros da URL
    const urlParams = new URLSearchParams(window.location.search);
    $('#filtro-status').val(urlParams.get('status') || '');
    $('#filtro-tipo').val(urlParams.get('tipo') || '');
    $('#filtro-autor').val(urlParams.get('autor') || '');

    // Auto-refresh a cada 30 segundos para status em tempo real
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            window.location.reload();
        }
    }, 30000);
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
</style>
@endpush