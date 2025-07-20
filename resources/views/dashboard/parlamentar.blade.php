@extends('components.layouts.app')

@section('title', 'Dashboard - Parlamentar')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-5">
        <div>
            <h1 class="h3 text-gray-800 mb-0">Dashboard Parlamentar</h1>
            <p class="text-muted">Bem-vindo, {{ auth()->user()->name }}</p>
        </div>
        <div>
            <a href="{{ route('proposicoes.criar') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nova Proposição
            </a>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-xl-2-5 col-lg-4 col-md-6">
            <div class="card bg-primary text-white">
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
                <div class="card-footer bg-primary border-primary">
                    <a href="{{ route('proposicoes.minhas-proposicoes') }}?status=em_elaboracao" 
                       class="text-white text-decoration-none">
                        <small>Ver detalhes <i class="fas fa-arrow-right ms-1"></i></small>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-2-5 col-lg-4 col-md-6">
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
                <div class="card-footer bg-success border-success">
                    <a href="{{ route('proposicoes.assinatura') }}" 
                       class="text-white text-decoration-none">
                        <small>Assinar agora <i class="fas fa-arrow-right ms-1"></i></small>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-2-5 col-lg-4 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $estatisticas['devolvidas_correcao'] }}</h4>
                            <p class="mb-0 opacity-75">Para Correção</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-warning border-warning">
                    <a href="{{ route('proposicoes.assinatura') }}?tab=devolvidas" 
                       class="text-white text-decoration-none">
                        <small>Corrigir agora <i class="fas fa-arrow-right ms-1"></i></small>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-2-5 col-lg-4 col-md-6">
            <div class="card bg-info text-white">
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
                <div class="card-footer bg-info border-info">
                    <a href="{{ route('proposicoes.minhas-proposicoes') }}?status=em_tramitacao" 
                       class="text-white text-decoration-none">
                        <small>Acompanhar <i class="fas fa-arrow-right ms-1"></i></small>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-2-5 col-lg-4 col-md-6">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $estatisticas['total_proposicoes'] }}</h4>
                            <p class="mb-0 opacity-75">Total de Proposições</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-list fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-secondary border-secondary">
                    <a href="{{ route('proposicoes.minhas-proposicoes') }}" 
                       class="text-white text-decoration-none">
                        <small>Ver todas <i class="fas fa-arrow-right ms-1"></i></small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Ações Urgentes -->
        <div class="col-lg-8">
            @if($proposicoes_urgentes->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-circle text-warning me-2"></i>
                        Ações Urgentes
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($proposicoes_urgentes as $proposicao)
                        <div class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-light text-dark me-2">{{ $proposicao->tipo }}</span>
                                        <strong>{{ $proposicao->titulo ?? 'Sem título' }}</strong>
                                        @if($proposicao->status === 'aprovado_assinatura')
                                            <span class="badge bg-success ms-2">Aguardando Assinatura</span>
                                        @elseif($proposicao->status === 'devolvido_correcao')
                                            <span class="badge bg-warning ms-2">Para Correção</span>
                                        @endif
                                    </div>
                                    <p class="mb-1 text-muted">{{ Str::limit($proposicao->ementa, 100) }}</p>
                                    <small class="text-muted">
                                        Atualizado {{ $proposicao->updated_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    @if($proposicao->status === 'aprovado_assinatura')
                                        <a href="{{ route('proposicoes.assinar', $proposicao) }}" 
                                           class="btn btn-success btn-sm">
                                            <i class="fas fa-pen me-1"></i>Assinar
                                        </a>
                                    @elseif($proposicao->status === 'devolvido_correcao')
                                        <a href="{{ route('proposicoes.corrigir', $proposicao) }}" 
                                           class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit me-1"></i>Corrigir
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @if($proposicoes_urgentes->count() >= 3)
                <div class="card-footer text-center">
                    <a href="{{ route('proposicoes.assinatura') }}" class="btn btn-outline-primary btn-sm">
                        Ver todas as ações pendentes
                    </a>
                </div>
                @endif
            </div>
            @endif

            <!-- Proposições Recentes -->
            <div class="card">
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
                                        <th>Ementa</th>
                                        <th>Status</th>
                                        <th>Atualização</th>
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
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 300px;">
                                                {{ $proposicao->ementa }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $proposicao->status_cor }}">
                                                {{ $proposicao->status_formatado }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $proposicao->updated_at->diffForHumans() }}</small>
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
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Nenhuma proposição criada ainda</h6>
                            <a href="{{ route('proposicoes.criar') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Criar primeira proposição
                            </a>
                        </div>
                    @endif
                </div>
                @if($proposicoes_recentes->count() >= 5)
                <div class="card-footer text-center">
                    <a href="{{ route('proposicoes.minhas-proposicoes') }}" class="btn btn-outline-primary btn-sm">
                        Ver todas as proposições
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Links Rápidos -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Ações Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('proposicoes.criar') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Nova Proposição
                        </a>
                        <a href="{{ route('proposicoes.assinatura') }}" class="btn btn-success">
                            <i class="fas fa-pen me-2"></i>Assinar Proposições
                        </a>
                        <a href="{{ route('proposicoes.minhas-proposicoes') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>Minhas Proposições
                        </a>
                        <a href="{{ route('proposicoes.historico-assinaturas') }}" class="btn btn-outline-info">
                            <i class="fas fa-history me-2"></i>Histórico
                        </a>
                    </div>
                </div>
            </div>

            <!-- Dicas -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        Dicas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <small>
                            <strong>Lembre-se:</strong> Proposições devolvidas para correção têm prioridade. 
                            Corrija-as o quanto antes para não atrasar a tramitação.
                        </small>
                    </div>
                    
                    <div class="alert alert-success">
                        <small>
                            <strong>Dica:</strong> Use modelos para agilizar a criação de novas proposições. 
                            Eles garantem que todos os elementos obrigatórios estejam presentes.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.col-xl-2-5 {
    flex: 0 0 auto;
    width: 20%;
}

@media (max-width: 1199.98px) {
    .col-xl-2-5 {
        width: 25%;
    }
}

@media (max-width: 991.98px) {
    .col-xl-2-5 {
        width: 50%;
    }
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.list-group-item-action:hover {
    background-color: var(--bs-light);
}

.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
@endpush