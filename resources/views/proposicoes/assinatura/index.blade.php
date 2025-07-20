@extends('components.layouts.app')

@section('title', 'Minhas Proposições - Assinatura')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-5">
        <div>
            <h1 class="h3 text-gray-800 mb-0">Assinatura de Proposições</h1>
            <p class="text-muted">Proposições aguardando sua assinatura ou correção</p>
        </div>
        <div>
            <a href="{{ route('proposicoes.minhas-proposicoes') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-list me-2"></i>Todas as Proposições
            </a>
            <a href="{{ route('proposicoes.historico-assinaturas') }}" class="btn btn-outline-primary">
                <i class="fas fa-history me-2"></i>Histórico de Assinaturas
            </a>
        </div>
    </div>

    <!-- Estatísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $proposicoes->where('status', 'aprovado_assinatura')->count() }}</h4>
                            <p class="mb-0 opacity-75">Aguardando Assinatura</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-pen fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $proposicoes->where('status', 'devolvido_correcao')->count() }}</h4>
                            <p class="mb-0 opacity-75">Para Correção</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-edit fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $proposicoes->total() }}</h4>
                            <p class="mb-0 opacity-75">Total Pendente</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-clipboard-list fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Abas -->
    <div class="card">
        <div class="card-header border-0">
            <ul class="nav nav-tabs card-header-tabs" id="tab-assinatura" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="aprovadas-tab" data-bs-toggle="tab" 
                            data-bs-target="#aprovadas" type="button" role="tab">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Aprovadas para Assinatura 
                        <span class="badge bg-success ms-2">{{ $proposicoes->where('status', 'aprovado_assinatura')->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="devolvidas-tab" data-bs-toggle="tab" 
                            data-bs-target="#devolvidas" type="button" role="tab">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Devolvidas para Correção 
                        <span class="badge bg-warning ms-2">{{ $proposicoes->where('status', 'devolvido_correcao')->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content" id="tab-assinatura-content">
                <!-- Aba: Aprovadas para Assinatura -->
                <div class="tab-pane fade show active" id="aprovadas" role="tabpanel">
                    @php $aprovadas = $proposicoes->where('status', 'aprovado_assinatura') @endphp
                    
                    @if($aprovadas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Proposição</th>
                                        <th>Ementa</th>
                                        <th>Revisão</th>
                                        <th>Data Aprovação</th>
                                        <th>Urgência</th>
                                        <th width="150">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($aprovadas as $proposicao)
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
                                            <div class="text-truncate" style="max-width: 300px;" title="{{ $proposicao->ementa }}">
                                                {{ $proposicao->ementa }}
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $proposicao->revisor->name ?? 'N/A' }}</strong>
                                                <br><small class="text-success">Aprovado</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                {{ $proposicao->data_revisao ? $proposicao->data_revisao->format('d/m/Y') : 'N/A' }}
                                                @if($proposicao->data_revisao)
                                                    <br><small class="text-muted">{{ $proposicao->data_revisao->format('H:i') }}</small>
                                                @endif
                                            </div>
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
                                                <a href="{{ route('proposicoes.assinar', $proposicao) }}" 
                                                   class="btn btn-success" title="Assinar">
                                                    <i class="fas fa-pen"></i> Assinar
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-muted">Nenhuma proposição aprovada para assinatura</h5>
                            <p class="text-muted">Suas proposições aparecerão aqui após aprovação pela revisão legislativa.</p>
                        </div>
                    @endif
                </div>

                <!-- Aba: Devolvidas para Correção -->
                <div class="tab-pane fade" id="devolvidas" role="tabpanel">
                    @php $devolvidas = $proposicoes->where('status', 'devolvido_correcao') @endphp
                    
                    @if($devolvidas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Proposição</th>
                                        <th>Ementa</th>
                                        <th>Motivo da Devolução</th>
                                        <th>Data Devolução</th>
                                        <th>Urgência</th>
                                        <th width="150">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($devolvidas as $proposicao)
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
                                            <div class="text-truncate" style="max-width: 250px;" title="{{ $proposicao->ementa }}">
                                                {{ $proposicao->ementa }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $proposicao->parecer_tecnico }}">
                                                {{ $proposicao->parecer_tecnico }}
                                            </div>
                                            @if($proposicao->revisor)
                                                <br><small class="text-muted">Por: {{ $proposicao->revisor->name }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                {{ $proposicao->data_revisao ? $proposicao->data_revisao->format('d/m/Y') : 'N/A' }}
                                                @if($proposicao->data_revisao)
                                                    <br><small class="text-muted">{{ $proposicao->data_revisao->format('H:i') }}</small>
                                                @endif
                                            </div>
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
                                                <a href="{{ route('proposicoes.corrigir', $proposicao) }}" 
                                                   class="btn btn-warning" title="Corrigir">
                                                    <i class="fas fa-edit"></i> Corrigir
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-thumbs-up fa-3x text-success mb-3"></i>
                            <h5 class="text-muted">Nenhuma proposição devolvida para correção</h5>
                            <p class="text-muted">Parabéns! Não há proposições que precisem de correção no momento.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Paginação -->
        @if($proposicoes->hasPages())
            <div class="card-footer">
                {{ $proposicoes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Manter a aba ativa baseada na URL ou localStorage
    const activeTab = localStorage.getItem('assinatura-active-tab') || 'aprovadas-tab';
    
    if (activeTab) {
        const tabTrigger = new bootstrap.Tab(document.getElementById(activeTab));
        tabTrigger.show();
    }

    // Salvar aba ativa
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        localStorage.setItem('assinatura-active-tab', e.target.id);
    });

    // Auto-refresh das estatísticas a cada 60 segundos
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            window.location.reload();
        }
    }, 60000);

    // Tooltips para textos truncados
    $('[title]').tooltip();
});
</script>
@endpush

@push('styles')
<style>
.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.table td {
    vertical-align: middle;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.table-hover tbody tr:hover {
    background-color: var(--bs-light);
}

.nav-tabs .nav-link {
    border-bottom: 2px solid transparent;
}

.nav-tabs .nav-link.active {
    border-bottom-color: var(--bs-primary);
    font-weight: 600;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endpush