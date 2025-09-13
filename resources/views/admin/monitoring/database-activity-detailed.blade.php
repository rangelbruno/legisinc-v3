@extends('components.layouts.app')

@section('title', 'Análise Detalhada de Tabela')

@section('content')

<style>
/* Design minimalista e clean */
.minimal-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.minimal-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    font-weight: 600;
    font-size: 1rem;
    color: #374151;
}

.minimal-content {
    padding: 1.5rem;
}

.table-selector {
    background: #f9fafb;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.metric-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.metric-item {
    background: #f8fafc;
    padding: 1rem;
    border-radius: 6px;
    text-align: center;
    border: 1px solid #e2e8f0;
}

.metric-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.metric-label {
    font-size: 0.875rem;
    color: #6b7280;
}

.flow-stage {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    margin-bottom: 1rem;
    overflow: hidden;
}

.flow-stage-header {
    background: #f3f4f6;
    padding: 0.75rem 1rem;
    display: flex;
    justify-content: between;
    align-items: center;
    font-weight: 600;
    color: #374151;
}

.flow-stage-content {
    padding: 1rem;
    background: #ffffff;
}

.change-item {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.change-item:last-child {
    border-bottom: none;
}

.column-name {
    font-weight: 600;
    color: #1f2937;
    min-width: 120px;
}

.change-values {
    flex: 1;
    margin-left: 1rem;
    font-size: 0.875rem;
    color: #4b5563;
}

.old-value, .new-value {
    padding: 2px 6px;
    border-radius: 4px;
    font-family: monospace;
}

.old-value {
    background: #fef2f2;
    color: #dc2626;
}

.new-value {
    background: #f0fdf4;
    color: #16a34a;
}

.record-item {
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.record-item:hover {
    border-color: #3b82f6;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
}

.record-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.record-id {
    font-weight: 700;
    color: #1f2937;
    font-size: 1.1rem;
}

.record-stats {
    font-size: 0.875rem;
    color: #6b7280;
}

.user-flow {
    background: #f3f4f6;
    padding: 0.5rem;
    border-radius: 4px;
    font-size: 0.875rem;
    color: #374151;
    margin-top: 0.5rem;
    font-family: monospace;
}

.loading-state {
    text-align: center;
    padding: 2rem;
    color: #6b7280;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #9ca3af;
}

.btn-minimal {
    background: #ffffff;
    border: 1px solid #d1d5db;
    color: #374151;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-minimal:hover {
    border-color: #3b82f6;
    color: #3b82f6;
}

.btn-minimal.active {
    background: #3b82f6;
    border-color: #3b82f6;
    color: #ffffff;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 1rem;
}
</style>

<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    🔍 Análise Detalhada de Tabela
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Administração</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.monitoring.index') }}" class="text-muted text-hover-primary">Monitoring</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.monitoring.database-activity') }}" class="text-muted text-hover-primary">Atividade DB</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Análise Detalhada</li>
                </ul>
            </div>
            <!--end::Page title-->
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!-- Seletor de Tabela -->
            <div class="minimal-card">
                <div class="minimal-header">
                    📋 Selecionar Tabela para Análise
                </div>
                <div class="minimal-content">
                    <div class="table-selector">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Tabela</label>
                                <select class="form-select" id="table-selector">
                                    <option value="">Selecione uma tabela</option>
                                    <option value="proposicoes">📄 Proposições</option>
                                    <option value="users">👤 Usuários</option>
                                    <option value="templates">📝 Templates</option>
                                    <option value="sessoes">🏛️ Sessões</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Período</label>
                                <select class="form-select" id="period-selector">
                                    <option value="24h">Últimas 24 horas</option>
                                    <option value="7d">Últimos 7 dias</option>
                                    <option value="30d">Últimos 30 dias</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary w-100" onclick="loadTableAnalysis()">
                                    Analisar Tabela
                                </button>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn-minimal w-100" onclick="refreshAnalysis()">
                                    🔄 Atualizar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Métricas Gerais -->
            <div class="minimal-card" id="metrics-section" style="display: none;">
                <div class="minimal-header">
                    📊 Métricas da Tabela
                </div>
                <div class="minimal-content">
                    <div class="metric-grid" id="table-metrics">
                        <!-- Métricas serão carregadas aqui -->
                    </div>
                </div>
            </div>

            <!-- Lista de Registros -->
            <div class="minimal-card" id="records-section" style="display: none;">
                <div class="minimal-header">
                    📁 Registros com Atividade Recente
                    <span class="float-end" id="records-count"></span>
                </div>
                <div class="minimal-content">
                    <div id="records-list">
                        <div class="loading-state">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                            <p class="mt-2">Carregando registros...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Análise de Colunas -->
            <div class="minimal-card" id="columns-section" style="display: none;">
                <div class="minimal-header">
                    🔧 Análise de Colunas Mais Alteradas
                </div>
                <div class="minimal-content">
                    <div id="columns-analysis">
                        <!-- Análise será carregada aqui -->
                    </div>
                </div>
            </div>

            <!-- Modal para Fluxo Detalhado -->
            <div class="modal fade" id="recordFlowModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">🔄 Fluxo de Alterações</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="record-flow-content">
                            <!-- Conteúdo do fluxo será carregado aqui -->
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
let currentTable = null;

function loadTableAnalysis() {
    const table = document.getElementById('table-selector').value;
    const period = document.getElementById('period-selector').value;

    if (!table) {
        alert('Por favor, selecione uma tabela');
        return;
    }

    currentTable = table;

    // Mostrar seções
    document.getElementById('metrics-section').style.display = 'block';
    document.getElementById('records-section').style.display = 'block';
    document.getElementById('columns-section').style.display = 'block';

    // Carregar dados
    loadTableMetrics(table, period);
    loadTableRecords(table);
    loadColumnAnalysis(table, period);
}

function loadTableMetrics(table, period) {
    // Placeholder para métricas - será implementado com dados reais
    const metricsHtml = `
        <div class="metric-item">
            <div class="metric-value" id="total-records">-</div>
            <div class="metric-label">Registros Ativos</div>
        </div>
        <div class="metric-item">
            <div class="metric-value" id="total-changes">-</div>
            <div class="metric-label">Total de Mudanças</div>
        </div>
        <div class="metric-item">
            <div class="metric-value" id="unique-users">-</div>
            <div class="metric-label">Usuários Envolvidos</div>
        </div>
        <div class="metric-item">
            <div class="metric-value" id="avg-changes">-</div>
            <div class="metric-label">Mudanças/Registro</div>
        </div>
    `;

    document.getElementById('table-metrics').innerHTML = metricsHtml;

    // Simular carregamento de dados
    setTimeout(() => {
        document.getElementById('total-records').textContent = '15';
        document.getElementById('total-changes').textContent = '47';
        document.getElementById('unique-users').textContent = '8';
        document.getElementById('avg-changes').textContent = '3.1';
    }, 500);
}

function loadTableRecords(table) {
    const url = `{{ route('admin.monitoring.database-activity.table-records') }}?table=${table}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderRecordsList(data.records);
                document.getElementById('records-count').textContent = `${data.total} registros`;
            } else {
                showEmptyState('records-list', 'Nenhum registro encontrado');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar registros:', error);
            showEmptyState('records-list', 'Erro ao carregar registros');
        });
}

function renderRecordsList(records) {
    const container = document.getElementById('records-list');

    if (records.length === 0) {
        showEmptyState('records-list', 'Nenhum registro com atividade recente');
        return;
    }

    const html = records.map(record => `
        <div class="record-item" onclick="showRecordFlow('${currentTable}', ${record.record_id})">
            <div class="record-header">
                <div class="record-id">Registro #${record.record_id}</div>
                <div class="record-stats">
                    ${record.total_changes} alterações • ${record.columns_changed} colunas • ${record.roles_involved} roles
                </div>
            </div>
            <div class="user-flow">
                Fluxo: ${record.user_flow || 'N/A'}
            </div>
            <div class="record-stats">
                <small>
                    Primeira alteração: ${formatDateTime(record.first_change)} |
                    Última: ${formatDateTime(record.last_change)}
                </small>
            </div>
        </div>
    `).join('');

    container.innerHTML = html;
}

function loadColumnAnalysis(table, period) {
    const url = `{{ route('admin.monitoring.database-activity.column-analysis') }}?table=${table}&period=${period}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderColumnAnalysis(data.analysis);
            } else {
                showEmptyState('columns-analysis', 'Nenhuma análise disponível');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar análise:', error);
            showEmptyState('columns-analysis', 'Erro ao carregar análise');
        });
}

function renderColumnAnalysis(analysis) {
    const container = document.getElementById('columns-analysis');

    if (analysis.length === 0) {
        showEmptyState('columns-analysis', 'Nenhuma atividade de colunas encontrada');
        return;
    }

    const html = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Coluna</th>
                        <th>Alterações</th>
                        <th>Registros Únicos</th>
                        <th>Usuários</th>
                        <th>Roles Envolvidas</th>
                        <th>Última Alteração</th>
                    </tr>
                </thead>
                <tbody>
                    ${analysis.map(col => `
                        <tr>
                            <td><strong>${col.column_name}</strong></td>
                            <td><span class="badge bg-primary">${col.total_changes}</span></td>
                            <td>${col.unique_records}</td>
                            <td>${col.unique_users}</td>
                            <td><small>${col.roles_involved}</small></td>
                            <td><small>${formatDateTime(col.last_change)}</small></td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;

    container.innerHTML = html;
}

function showRecordFlow(table, recordId) {
    const url = `{{ route('admin.monitoring.database-activity.record-flow') }}?table=${table}&record_id=${recordId}`;

    // Mostrar loading no modal
    const modalContent = document.getElementById('record-flow-content');
    modalContent.innerHTML = `
        <div class="loading-state">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Carregando fluxo de alterações...</p>
        </div>
    `;

    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('recordFlowModal'));
    modal.show();

    // Carregar dados
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderRecordFlow(data);
            } else {
                modalContent.innerHTML = '<div class="empty-state">Nenhum fluxo encontrado</div>';
            }
        })
        .catch(error => {
            console.error('Erro ao carregar fluxo:', error);
            modalContent.innerHTML = '<div class="empty-state">Erro ao carregar fluxo</div>';
        });
}

function renderRecordFlow(data) {
    const container = document.getElementById('record-flow-content');

    if (data.flow.length === 0) {
        container.innerHTML = '<div class="empty-state">Nenhuma alteração encontrada</div>';
        return;
    }

    const html = `
        <div class="section-title">
            Registro #${data.record_id} da tabela "${data.table}"
        </div>
        <p class="text-muted">${data.total_stages} etapas • ${data.total_changes} alterações totais</p>

        ${data.flow.map((stage, index) => `
            <div class="flow-stage">
                <div class="flow-stage-header">
                    <span>Etapa ${stage.stage}: ${stage.user_role}</span>
                    <span class="text-muted">${stage.user_name} • ${formatDateTime(stage.timestamp)}</span>
                </div>
                <div class="flow-stage-content">
                    ${stage.changes.map(change => `
                        <div class="change-item">
                            <div class="column-name">${change.column}</div>
                            <div class="change-values">
                                <div>
                                    ${change.old_value ? `<span class="old-value">${JSON.stringify(change.old_value)}</span> → ` : ''}
                                    <span class="new-value">${JSON.stringify(change.new_value)}</span>
                                </div>
                                <small class="text-muted">${change.operation} em ${formatDateTime(change.timestamp)}</small>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `).join('')}
    `;

    container.innerHTML = html;
}

function refreshAnalysis() {
    if (currentTable) {
        loadTableAnalysis();
    }
}

function showEmptyState(containerId, message) {
    document.getElementById(containerId).innerHTML = `
        <div class="empty-state">
            <p>${message}</p>
        </div>
    `;
}

function formatDateTime(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}
</script>
@endpush

@endsection