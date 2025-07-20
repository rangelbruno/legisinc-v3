@extends('components.layouts.app')

@section('title', 'Dashboard - Administrador')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-5">
        <div>
            <h1 class="h3 text-gray-800 mb-0">Dashboard Administrativo</h1>
            <p class="text-muted">Visão geral do sistema de proposições</p>
        </div>
        <div>
            <a href="{{ route('proposicoes.criar') }}" class="btn btn-primary me-2">
                <i class="fas fa-plus me-2"></i>Nova Proposição
            </a>
            <a href="/admin/dashboard" class="btn btn-outline-secondary">
                <i class="fas fa-cog me-2"></i>Admin Geral
            </a>
        </div>
    </div>

    <!-- Estatísticas Gerais -->
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $estatisticas['total_proposicoes'] }}</h4>
                            <p class="mb-0 opacity-75">Total Proposições</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-file-alt fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $estatisticas['em_elaboracao'] }}</h4>
                            <p class="mb-0 opacity-75">Em Elaboração</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-edit fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $estatisticas['em_revisao'] }}</h4>
                            <p class="mb-0 opacity-75">Em Revisão</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-search fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $estatisticas['aguardando_assinatura'] }}</h4>
                            <p class="mb-0 opacity-75">Aguardando Assinatura</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-pen fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $estatisticas['aguardando_protocolo'] }}</h4>
                            <p class="mb-0 opacity-75">Aguardando Protocolo</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-clipboard-list fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $estatisticas['em_tramitacao'] }}</h4>
                            <p class="mb-0 opacity-75">Em Tramitação</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-cogs fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Proposições Recentes -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock text-primary me-2"></i>
                        Proposições Recentes
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($proposicoes_recentes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Proposição</th>
                                        <th>Autor</th>
                                        <th>Status</th>
                                        <th>Data</th>
                                        <th width="100">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($proposicoes_recentes as $proposicao)
                                    <tr>
                                        <td>
                                            <div>
                                                <span class="badge bg-light text-dark me-2">{{ $proposicao->tipo }}</span>
                                                <strong>{{ $proposicao->titulo ?? 'Sem título' }}</strong>
                                            </div>
                                            <div class="text-muted small">
                                                {{ Str::limit($proposicao->ementa, 80) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $proposicao->autor->name ?? 'N/A' }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $proposicao->status_cor }}">
                                                {{ $proposicao->status_formatado }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $proposicao->created_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('proposicoes.show', $proposicao) }}" 
                                               class="btn btn-outline-info btn-sm" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Nenhuma proposição no sistema</h6>
                            <p class="text-muted">O sistema está pronto para receber as primeiras proposições.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Estatísticas e Links -->
        <div class="col-lg-4">
            <!-- Links Rápidos -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-link text-primary me-2"></i>
                        Links Rápidos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('proposicoes.revisar') }}" class="btn btn-outline-primary">
                            <i class="fas fa-search me-2"></i>Revisar Proposições
                        </a>
                        <a href="{{ route('proposicoes.protocolar') }}" class="btn btn-outline-success">
                            <i class="fas fa-clipboard-list me-2"></i>Protocolar
                        </a>
                        <a href="{{ route('proposicoes.minhas-proposicoes') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>Todas as Proposições
                        </a>
                        <a href="/admin/dashboard" class="btn btn-outline-info">
                            <i class="fas fa-cog me-2"></i>Admin Geral
                        </a>
                    </div>
                </div>
            </div>

            <!-- Estatísticas por Tipo -->
            @if($estatisticas_por_tipo->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-pie text-info me-2"></i>
                        Por Tipo
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($estatisticas_por_tipo as $stat)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-light text-dark">{{ $stat->tipo }}</span>
                        <span class="fw-bold">{{ $stat->total }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Estatísticas por Status -->
            @if($estatisticas_por_status->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-bar text-warning me-2"></i>
                        Por Status
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($estatisticas_por_status as $stat)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small">{{ ucfirst(str_replace('_', ' ', $stat->status)) }}</span>
                        <span class="fw-bold">{{ $stat->total }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.col-xl-2 {
    flex: 0 0 auto;
    width: 16.666667%;
}

@media (max-width: 1199.98px) {
    .col-xl-2 {
        width: 25%;
    }
}

@media (max-width: 991.98px) {
    .col-xl-2 {
        width: 50%;
    }
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.table-hover tbody tr:hover {
    background-color: var(--bs-light);
}
</style>
@endpush