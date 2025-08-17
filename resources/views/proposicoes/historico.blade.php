@extends('layout.app')

@section('title', 'Histórico da Proposição')

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('proposicoes.index') }}">Proposições</a>
</li>
<li class="breadcrumb-item">
    <a href="{{ route('proposicoes.show', $proposicao) }}">{{ $proposicao->tipo }}</a>
</li>
<li class="breadcrumb-item active">Histórico</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-2"></i>
                        Histórico de Alterações
                    </h3>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="atualizarHistorico()">
                            <i class="fas fa-sync-alt"></i> Atualizar
                        </button>
                        @if(auth()->user()->isAdmin())
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportarHistorico()">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Informações da Proposição -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h5 class="text-primary">{{ $proposicao->tipo }}</h5>
                            <p class="text-muted mb-1">{{ $proposicao->ementa }}</p>
                            <small class="text-muted">
                                <strong>Autor:</strong> {{ $proposicao->autor->name ?? 'Desconhecido' }} |
                                <strong>Status:</strong> 
                                <span class="badge badge-{{ $proposicao->getStatusColor() }}">
                                    {{ $proposicao->getStatusLabel() }}
                                </span>
                            </small>
                        </div>
                        <div class="col-md-4 text-right">
                            <div id="estatisticas-container" class="d-none">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h6 class="mb-0" id="total-alteracoes">-</h6>
                                        <small class="text-muted">Alterações</small>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="mb-0" id="usuarios-envolvidos">-</h6>
                                        <small class="text-muted">Usuários</small>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="mb-0" id="origem-principal">-</h6>
                                        <small class="text-muted">Origem</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-control form-control-sm" id="filtro-origem">
                                <option value="">Todas as origens</option>
                                <option value="onlyoffice">OnlyOffice</option>
                                <option value="web">Interface Web</option>
                                <option value="system">Sistema</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control form-control-sm" id="filtro-acao">
                                <option value="">Todas as ações</option>
                                <option value="callback_onlyoffice">Edição OnlyOffice</option>
                                <option value="status_change">Mudança de Status</option>
                                <option value="create">Criação</option>
                            </select>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="input-group input-group-sm" style="max-width: 300px; margin-left: auto;">
                                <input type="text" class="form-control" id="busca-usuario" placeholder="Buscar por usuário...">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="limparFiltros()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loading -->
                    <div id="loading-historico" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Carregando...</span>
                        </div>
                        <p class="mt-2 text-muted">Carregando histórico...</p>
                    </div>

                    <!-- Timeline do Histórico -->
                    <div id="historico-timeline" class="d-none">
                        <div class="timeline">
                            <!-- Será preenchido via JavaScript -->
                        </div>
                    </div>

                    <!-- Mensagem quando não há histórico -->
                    <div id="sem-historico" class="text-center py-4 d-none">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum histórico encontrado</h5>
                        <p class="text-muted">Esta proposição ainda não possui alterações registradas.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalhes da alteração -->
<div class="modal fade" id="modalDetalhes" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes da Alteração</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Será preenchido via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 30px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
    padding-left: 70px;
}

.timeline-marker {
    position: absolute;
    left: 22px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-marker.onlyoffice { background: #007bff; }
.timeline-marker.web { background: #28a745; }
.timeline-marker.system { background: #6c757d; }

.timeline-content {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.timeline-meta {
    font-size: 0.875rem;
    color: #6c757d;
}

.diff-info {
    font-size: 0.875rem;
    padding: 5px 10px;
    border-radius: 4px;
    margin-top: 8px;
}

.diff-info.success { background: #d4edda; color: #155724; }
.diff-info.info { background: #d1ecf1; color: #0c5460; }
.diff-info.warning { background: #fff3cd; color: #856404; }
.diff-info.danger { background: #f8d7da; color: #721c24; }

.badge-origem {
    font-size: 0.75rem;
    padding: 4px 8px;
}
</style>
@endpush

@push('scripts')
<script>
let historicoData = [];
let historicoFiltrado = [];

$(document).ready(function() {
    carregarHistorico();
    
    // Event listeners para filtros
    $('#filtro-origem, #filtro-acao').change(aplicarFiltros);
    $('#busca-usuario').on('input', aplicarFiltros);
});

function carregarHistorico() {
    $('#loading-historico').removeClass('d-none');
    $('#historico-timeline, #sem-historico').addClass('d-none');
    
    $.get(`/proposicoes/{{ $proposicao->id }}/historico`)
        .done(function(response) {
            historicoData = response.historico;
            historicoFiltrado = [...historicoData];
            
            if (response.estatisticas) {
                atualizarEstatisticas(response.estatisticas);
            }
            
            renderizarHistorico();
        })
        .fail(function(xhr) {
            console.error('Erro ao carregar histórico:', xhr.responseText);
            $('#sem-historico').removeClass('d-none');
            
            if (xhr.status === 403) {
                $('#sem-historico h5').text('Acesso Negado');
                $('#sem-historico p').text('Você não tem permissão para visualizar o histórico desta proposição.');
            }
        })
        .always(function() {
            $('#loading-historico').addClass('d-none');
        });
}

function atualizarEstatisticas(stats) {
    $('#total-alteracoes').text(stats.total_alteracoes);
    $('#usuarios-envolvidos').text(stats.usuarios_envolvidos);
    $('#origem-principal').text(stats.origem_mais_comum || 'N/A');
    $('#estatisticas-container').removeClass('d-none');
}

function renderizarHistorico() {
    if (historicoFiltrado.length === 0) {
        $('#sem-historico').removeClass('d-none');
        $('#historico-timeline').addClass('d-none');
        return;
    }
    
    const timeline = $('.timeline');
    timeline.empty();
    
    historicoFiltrado.forEach(item => {
        const timelineItem = `
            <div class="timeline-item" data-historico-id="${item.id}">
                <div class="timeline-marker ${item.origem}"></div>
                <div class="timeline-content">
                    <div class="timeline-header">
                        <div>
                            <strong>${item.usuario}</strong>
                            <span class="badge badge-origem badge-${getOrigemColor(item.origem)} ml-2">
                                ${getOrigemLabel(item.origem)}
                            </span>
                        </div>
                        <div class="timeline-meta">
                            ${item.data_alteracao}
                        </div>
                    </div>
                    <div class="timeline-body">
                        <p class="mb-2">${item.resumo}</p>
                        ${item.diff_info ? `
                            <div class="diff-info ${item.diff_info.cor}">
                                <strong>${item.diff_info.tipo}:</strong> ${item.diff_info.descricao}
                                ${item.diff_info.significativa ? ' <i class="fas fa-exclamation-triangle" title="Mudança significativa"></i>' : ''}
                            </div>
                        ` : ''}
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="verDetalhes(${item.id})">
                                <i class="fas fa-eye"></i> Ver Detalhes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        timeline.append(timelineItem);
    });
    
    $('#historico-timeline').removeClass('d-none');
    $('#sem-historico').addClass('d-none');
}

function aplicarFiltros() {
    const filtroOrigem = $('#filtro-origem').val();
    const filtroAcao = $('#filtro-acao').val();
    const buscaUsuario = $('#busca-usuario').val().toLowerCase();
    
    historicoFiltrado = historicoData.filter(item => {
        const passaOrigem = !filtroOrigem || item.origem === filtroOrigem;
        const passaAcao = !filtroAcao || item.acao === filtroAcao;
        const passaUsuario = !buscaUsuario || item.usuario.toLowerCase().includes(buscaUsuario);
        
        return passaOrigem && passaAcao && passaUsuario;
    });
    
    renderizarHistorico();
}

function limparFiltros() {
    $('#filtro-origem, #filtro-acao').val('');
    $('#busca-usuario').val('');
    aplicarFiltros();
}

function verDetalhes(historicoId) {
    $.get(`/proposicoes/{{ $proposicao->id }}/historico/${historicoId}`)
        .done(function(response) {
            const item = response.historico;
            
            const modalBody = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informações Básicas</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Usuário:</strong></td><td>${item.usuario}</td></tr>
                            <tr><td><strong>Ação:</strong></td><td>${item.acao}</td></tr>
                            <tr><td><strong>Origem:</strong></td><td>${getOrigemLabel(item.origem)}</td></tr>
                            <tr><td><strong>Data:</strong></td><td>${item.data_alteracao}</td></tr>
                            <tr><td><strong>IP:</strong></td><td>${item.ip_usuario || 'N/A'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Alterações</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Tipo:</strong></td><td>${item.tipo_alteracao}</td></tr>
                            <tr><td><strong>Tamanho Anterior:</strong></td><td>${item.tamanho_anterior || 'N/A'} bytes</td></tr>
                            <tr><td><strong>Tamanho Novo:</strong></td><td>${item.tamanho_novo || 'N/A'} bytes</td></tr>
                            <tr><td><strong>Diferença:</strong></td><td>${(item.tamanho_novo || 0) - (item.tamanho_anterior || 0)} bytes</td></tr>
                        </table>
                    </div>
                </div>
                
                ${item.diff_conteudo ? `
                    <h6>Análise da Alteração</h6>
                    <div class="alert alert-info">
                        <pre>${JSON.stringify(item.diff_conteudo, null, 2)}</pre>
                    </div>
                ` : ''}
                
                ${item.metadados ? `
                    <h6>Metadados Técnicos</h6>
                    <div class="alert alert-secondary">
                        <pre>${JSON.stringify(item.metadados, null, 2)}</pre>
                    </div>
                ` : ''}
            `;
            
            $('#modalDetalhes .modal-body').html(modalBody);
            $('#modalDetalhes').modal('show');
        })
        .fail(function(xhr) {
            console.error('Erro ao carregar detalhes:', xhr.responseText);
            alert('Erro ao carregar detalhes da alteração.');
        });
}

function atualizarHistorico() {
    carregarHistorico();
}

function exportarHistorico() {
    // TODO: Implementar exportação
    alert('Funcionalidade de exportação será implementada em breve.');
}

function getOrigemLabel(origem) {
    const labels = {
        'onlyoffice': 'OnlyOffice',
        'web': 'Interface Web', 
        'system': 'Sistema',
        'api': 'API'
    };
    return labels[origem] || origem;
}

function getOrigemColor(origem) {
    const colors = {
        'onlyoffice': 'primary',
        'web': 'success',
        'system': 'secondary',
        'api': 'warning'
    };
    return colors[origem] || 'secondary';
}
</script>
@endpush