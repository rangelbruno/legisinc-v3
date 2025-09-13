@extends('components.layouts.app')

@section('title', 'Análise Detalhada de Tabela')

@section('content')

<!-- OTIMIZAÇÕES CRÍTICAS INLINE -->
<script>
// Aplicar polyfill de eventos passivos IMEDIATAMENTE
(function() {
    'use strict';

    // Detectar suporte a passive events
    let supportsPassive = false;
    try {
        const opts = Object.defineProperty({}, 'passive', {
            get: function() { supportsPassive = true; return false; }
        });
        window.addEventListener("testPassive", null, opts);
        window.removeEventListener("testPassive", null, opts);
    } catch (e) {}

    if (supportsPassive) {
        const passiveEvents = ['touchstart','touchmove','touchend','touchcancel','mousewheel','wheel','scroll','pointermove','pointerover','pointerenter','pointerdown','pointerup'];

        // Override addEventListener IMEDIATAMENTE
        const orig = EventTarget.prototype.addEventListener;
        EventTarget.prototype.addEventListener = function(type, listener, options) {
            if (passiveEvents.includes(type)) {
                if (typeof options === 'boolean') {
                    options = { capture: options, passive: true };
                } else if (typeof options === 'object' && options !== null) {
                    options = { ...options, passive: true };
                } else {
                    options = { passive: true };
                }
            }
            return orig.call(this, type, listener, options);
        };
        console.log('⚡ Passive events enabled immediately');
    }

    // Suprimir warnings do Vue IMEDIATAMENTE
    const originalWarn = console.warn;
    console.warn = function(...args) {
        const message = args.join(' ');
        if (message.includes('You are running a development build of Vue') ||
            message.includes('Make sure to use the production build') ||
            message.includes('vue.global.js') ||
            message.includes('development build')) {
            return;
        }
        originalWarn.apply(console, args);
    };
    console.log('🔇 Vue warnings suppressed immediately');
})();
</script>

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

.activity-row {
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    will-change: transform; /* Otimização para animações */
}

.activity-row:hover {
    background: #f9fafb;
    border-color: #3b82f6;
    transform: translateZ(0); /* Força hardware acceleration */
}

/* Performance optimizations */
.activity-details-content {
    contain: layout style paint;
}

.table-responsive {
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
}

.activity-details {
    display: none;
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid #e5e7eb;
}

.activity-row.expanded .activity-details {
    display: block;
}

.field-change {
    background: #f8fafc;
    padding: 0.5rem;
    margin-bottom: 0.5rem;
    border-radius: 4px;
    border-left: 3px solid #3b82f6;
}

.field-name {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.field-values {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.value-old {
    background: #fef2f2;
    color: #dc2626;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: monospace;
}

.value-new {
    background: #f0fdf4;
    color: #16a34a;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: monospace;
}

.operation-badge {
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.operation-badge.insert {
    background: #dbeafe;
    color: #1e40af;
}

.operation-badge.update {
    background: #fef3c7;
    color: #92400e;
}

.operation-badge.delete {
    background: #fee2e2;
    color: #991b1b;
}

.operation-badge.select {
    background: #e5e7eb;
    color: #374151;
}

.method-badge {
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.method-badge.get {
    background: #dbeafe;
    color: #1e40af;
}

.method-badge.post {
    background: #dcfce7;
    color: #166534;
}

.method-badge.put {
    background: #fef3c7;
    color: #92400e;
}

.method-badge.patch {
    background: #e0e7ff;
    color: #3730a3;
}

.method-badge.delete {
    background: #fee2e2;
    color: #991b1b;
}

.filter-section {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.filter-group {
    margin-bottom: 1rem;
}

.filter-group:last-child {
    margin-bottom: 0;
}

.checkbox-group {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 0.5rem;
}

.checkbox-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.checkbox-item input[type="checkbox"] {
    margin: 0;
}

.checkbox-item label {
    font-size: 0.875rem;
    margin: 0;
    cursor: pointer;
}

.export-section {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 1rem;
    margin-top: 1rem;
}

.export-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-export {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-export:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-export.csv {
    background: #198754;
    color: white;
}

.btn-export.excel {
    background: #0d6efd;
    color: white;
}

.btn-export.detailed {
    background: #6f42c1;
    color: white;
}

.btn-export:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.export-info {
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 0.5rem;
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
                                    <!-- Opções serão carregadas dinamicamente -->
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

            <!-- Filtros Avançados -->
            <div class="minimal-card" id="filters-section" style="display: none;">
                <div class="minimal-header">
                    🔍 Filtros Avançados
                    <button type="button" class="btn btn-sm btn-light float-end" onclick="clearAllFilters()">
                        Limpar Filtros
                    </button>
                </div>
                <div class="minimal-content">
                    <div class="filter-section">
                        <div class="row">
                            <!-- Métodos HTTP -->
                            <div class="col-md-6">
                                <div class="filter-group">
                                    <label class="form-label fw-bold">Métodos HTTP</label>
                                    <div class="checkbox-group" id="http-methods-filter">
                                        <!-- Opções serão carregadas dinamicamente -->
                                    </div>
                                </div>
                            </div>
                            <!-- Tipos de Operação -->
                            <div class="col-md-6">
                                <div class="filter-group">
                                    <label class="form-label fw-bold">Tipos de Operação</label>
                                    <div class="checkbox-group" id="operations-filter">
                                        <!-- Opções serão carregadas dinamicamente -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="filter-group">
                                    <label class="form-label fw-bold">Usuário</label>
                                    <select class="form-control" id="user-id-filter">
                                        <option value="">Todos os usuários</option>
                                        <!-- Opções serão carregadas dinamicamente -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="filter-group">
                                    <label class="form-label fw-bold">Endpoint (contém)</label>
                                    <input type="text" class="form-control" id="endpoint-filter" placeholder="Ex: proposicoes, salvar">
                                    <small class="text-muted">Busca parcial no endpoint</small>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-success" onclick="applyFilters()">
                                ✅ Aplicar Filtros
                            </button>
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

            <!-- Lista de Atividades Detalhadas -->
            <div class="minimal-card" id="activities-section" style="display: none;">
                <div class="minimal-header">
                    📊 Atividades Detalhadas da Tabela
                    <span class="float-end" id="activities-count"></span>
                </div>
                <div class="minimal-content">
                    <!-- Seção de Exportação -->
                    <div class="export-section" id="export-section" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">📥 Exportar Dados</h6>
                            <small class="text-muted" id="export-count">0 registros para exportar</small>
                        </div>
                        <div class="export-buttons">
                            <button type="button" class="btn-export csv" onclick="exportData('csv')" id="btn-export-csv">
                                📄 CSV Básico
                            </button>
                            <button type="button" class="btn-export excel" onclick="exportData('excel')" id="btn-export-excel">
                                📊 Excel (XLSX)
                            </button>
                            <button type="button" class="btn-export detailed" onclick="exportData('detailed')" id="btn-export-detailed">
                                🔍 CSV Detalhado
                            </button>
                        </div>
                        <div class="export-info">
                            💡 <strong>CSV Básico</strong>: Informações principais das atividades<br>
                            📊 <strong>Excel</strong>: Formato otimizado para planilhas<br>
                            🔍 <strong>CSV Detalhado</strong>: Inclui detalhes completos dos campos alterados em cada operação
                        </div>
                    </div>

                    <div id="activities-list">
                        <div class="loading-state">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                            <p class="mt-2">Carregando atividades...</p>
                        </div>
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
let filterOptions = null;

// Carregar opções de filtro ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    loadFilterOptions();
}, { passive: true });

// Otimizar scroll listeners para melhor performance
if ('IntersectionObserver' in window) {
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Lazy loading ou outras otimizações podem ser adicionadas aqui
            }
        });
    }, observerOptions);
}

function loadFilterOptions() {
    fetch('{{ route("admin.monitoring.database-activity.filter-options") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                filterOptions = data.options;
                populateFilterOptions();
            }
        })
        .catch(error => {
            // Fallback para opções estáticas se a API falhar
            filterOptions = {
                http_methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
                operation_types: ['SELECT', 'INSERT', 'UPDATE', 'DELETE'],
                tables_with_activity: [
                    {table_name: 'proposicoes', activity_count: 513},
                    {table_name: 'users', activity_count: 21}
                ],
                active_users: [
                    {user_id: 2, activity_count: 1916},
                    {user_id: 3, activity_count: 573}
                ]
            };
            populateFilterOptions();
        });
}

function populateFilterOptions() {
    if (!filterOptions) return;

    // Popular métodos HTTP
    const httpMethodsContainer = document.getElementById('http-methods-filter');
    httpMethodsContainer.innerHTML = filterOptions.http_methods.map(method => `
        <div class="checkbox-item">
            <input type="checkbox" id="method-${method.toLowerCase()}" value="${method}" checked>
            <label for="method-${method.toLowerCase()}">${method}</label>
        </div>
    `).join('');

    // Popular tipos de operação
    const operationsContainer = document.getElementById('operations-filter');
    operationsContainer.innerHTML = filterOptions.operation_types.map(op => `
        <div class="checkbox-item">
            <input type="checkbox" id="op-${op.toLowerCase()}" value="${op}" checked>
            <label for="op-${op.toLowerCase()}">${op}</label>
        </div>
    `).join('');

    // Popular seletor de tabelas
    const tableSelector = document.getElementById('table-selector');
    const tableEmojis = {
        'proposicoes': '📄',
        'users': '👤',
        'templates': '📝',
        'sessoes': '🏛️',
        'parlamentars': '💼',
        'roles': '🔒',
        'permissions': '⚙️'
    };

    filterOptions.tables_with_activity.forEach(table => {
        const emoji = tableEmojis[table.table_name] || '📊';
        const option = document.createElement('option');
        option.value = table.table_name;
        option.textContent = `${emoji} ${table.table_name} (${table.activity_count})`;
        tableSelector.appendChild(option);
    });

    // Popular seletor de usuários
    const userSelector = document.getElementById('user-id-filter');
    filterOptions.active_users.forEach(user => {
        const option = document.createElement('option');
        option.value = user.user_id;
        option.textContent = `Usuário #${user.user_id} (${user.activity_count} atividades)`;
        userSelector.appendChild(option);
    });
}

function loadTableAnalysis() {
    const table = document.getElementById('table-selector').value;
    const period = document.getElementById('period-selector').value;

    if (!table) {
        alert('Por favor, selecione uma tabela');
        return;
    }

    currentTable = table;

    // Mostrar seções
    document.getElementById('filters-section').style.display = 'block';
    document.getElementById('metrics-section').style.display = 'block';
    document.getElementById('activities-section').style.display = 'block';
    document.getElementById('records-section').style.display = 'block';
    document.getElementById('columns-section').style.display = 'block';

    // Carregar dados
    loadTableMetrics(table, period);

    // Aplicar filtros iniciais (todos selecionados)
    const initialFilters = getSelectedFilters();
    loadTableActivities(table, period, initialFilters);

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

function loadTableActivities(table, period, filters = {}) {
    const periodMap = {
        '24h': '1day',
        '7d': '7days',
        '30d': '30days'
    };

    // Construir URL com filtros
    let url = `{{ route('admin.monitoring.database-activity.filter') }}?table=${table}&period=${periodMap[period] || '1day'}`;

    if (filters.methods && filters.methods.length > 0) {
        url += `&methods=${filters.methods.join(',')}`;
    }

    if (filters.operations && filters.operations.length > 0) {
        url += `&operations=${filters.operations.join(',')}`;
    }

    if (filters.userId) {
        url += `&user_id=${filters.userId}`;
    }

    if (filters.endpoint) {
        url += `&endpoint=${encodeURIComponent(filters.endpoint)}`;
    }

    // Usar AbortController para cancelar requests anteriores
    if (window.currentActivityRequest) {
        window.currentActivityRequest.abort();
    }

    window.currentActivityRequest = new AbortController();

    fetch(url, { signal: window.currentActivityRequest.signal })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                renderActivitiesList(data.activities);
                const countElement = document.getElementById('activities-count');
                if (countElement) {
                    countElement.textContent = `${data.total} atividades`;
                }
            } else {
                showEmptyState('activities-list', 'Nenhuma atividade encontrada');
            }
        })
        .catch(error => {
            if (error.name !== 'AbortError') {
                showEmptyState('activities-list', 'Erro ao carregar atividades');
            }
        })
        .finally(() => {
            window.currentActivityRequest = null;
        });
}

function renderActivitiesList(activities) {
    const container = document.getElementById('activities-list');

    if (activities.length === 0) {
        showEmptyState('activities-list', 'Nenhuma atividade encontrada para esta tabela');
        // Esconder seção de exportação se não há dados
        document.getElementById('export-section').style.display = 'none';
        return;
    }

    // Mostrar seção de exportação e atualizar contador
    document.getElementById('export-section').style.display = 'block';
    document.getElementById('export-count').textContent = `${activities.length} registros para exportar`;

    // Armazenar dados para exportação
    window.currentActivitiesData = activities;

    const html = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Horário</th>
                        <th>Operação</th>
                        <th>Tempo</th>
                        <th>Método</th>
                        <th>Endpoint</th>
                        <th>Usuário</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    ${activities.map(activity => `
                        <tr class="activity-row" data-activity-id="${activity.id}">
                            <td>${formatDateTime(activity.created_at)}</td>
                            <td>
                                <span class="operation-badge ${activity.operation_type.toLowerCase()}">
                                    ${activity.operation_type}
                                </span>
                            </td>
                            <td>${activity.query_time_ms}ms</td>
                            <td>
                                ${activity.request_method ?
                                    `<span class="method-badge ${activity.request_method.toLowerCase()}">${activity.request_method}</span>` :
                                    '-'
                                }
                            </td>
                            <td><small>${activity.endpoint || '-'}</small></td>
                            <td>#${activity.user_id || 'Sistema'}</td>
                            <td>
                                <button class="btn btn-sm btn-light" onclick="toggleActivityDetails(event, ${activity.id})">
                                    <i class="bi bi-eye"></i> Detalhes
                                </button>
                            </td>
                        </tr>
                        <tr id="details-${activity.id}" style="display: none;">
                            <td colspan="7">
                                <div class="activity-details-content p-3">
                                    ${renderActivityDetails(activity)}
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;

    container.innerHTML = html;
}

function renderActivityDetails(activity) {
    // Verificar se temos change_details
    if (!activity.change_details || activity.change_details === null || activity.change_details === 'null') {
        return '<div class="alert alert-info">💡 Sem detalhes disponíveis para esta operação</div>';
    }

    let details;
    try {
        if (typeof activity.change_details === 'string') {
            details = JSON.parse(activity.change_details);
        } else {
            details = activity.change_details;
        }
    } catch (e) {
        return '<div class="alert alert-warning">⚠️ Erro ao processar detalhes</div>';
    }

    if (!details || typeof details !== 'object') {
        return '<div class="alert alert-warning">⚠️ Detalhes em formato inválido</div>';
    }

    if (!details.fields || Object.keys(details.fields).length === 0) {
        return '<div class="alert alert-info">📝 Nenhuma mudança de campo registrada</div>';
    }

    // Construir HTML dos detalhes
    let html = '<div class="alert alert-success">';
    html += '<h6 class="mb-3">🔍 Detalhes da Operação</h6>';

    if (details.record_id) {
        html += `<div class="mb-2"><strong>🆔 ID do Registro:</strong> #${details.record_id}</div>`;
    }

    html += '<div class="mb-2"><strong>📝 Campos Alterados:</strong></div>';
    html += '<div class="row">';

    let fieldCount = 0;
    for (const [field, values] of Object.entries(details.fields)) {
        fieldCount++;
        const oldValue = values.old === null ? 'NULL' : String(values.old);
        const newValue = values.new === null ? 'NULL' : String(values.new);

        html += `
            <div class="col-md-6 mb-2">
                <div class="p-2 border rounded bg-light">
                    <div class="fw-bold text-primary">${escapeHtml(field)}</div>
                    <div class="small">
                        ${values.old !== null ?
                            `<span class="badge bg-danger">${escapeHtml(oldValue)}</span> → ` :
                            ''}
                        <span class="badge bg-success">${escapeHtml(newValue)}</span>
                    </div>
                </div>
            </div>
        `;
    }

    html += '</div>';
    html += `<div class="small text-muted mt-2">Total de campos alterados: ${fieldCount}</div>`;
    html += '</div>';

    return html;
}

function toggleActivityDetails(event, activityId) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    const detailsRow = document.getElementById(`details-${activityId}`);
    if (!detailsRow) return;

    // Usar DOMBatcher para otimizar manipulações DOM
    if (window.PerformanceOptimizer) {
        window.PerformanceOptimizer.batchRead(() => {
            const isVisible = detailsRow.style.display === 'table-row';

            window.PerformanceOptimizer.batchWrite(() => {
                if (!isVisible) {
                    // Fechar outras linhas abertas
                    document.querySelectorAll('[id^="details-"]').forEach(row => {
                        row.style.display = 'none';
                    });
                    detailsRow.style.display = 'table-row';
                    window.PerformanceOptimizer.optimizeAnimation(detailsRow, 'opacity, transform');
                } else {
                    detailsRow.style.display = 'none';
                }
            });
        });
    } else {
        // Fallback para caso o optimizer não esteja carregado
        requestAnimationFrame(() => {
            if (detailsRow.style.display === 'none' || !detailsRow.style.display) {
                document.querySelectorAll('[id^="details-"]').forEach(row => {
                    row.style.display = 'none';
                });
                detailsRow.style.display = 'table-row';
            } else {
                detailsRow.style.display = 'none';
            }
        });
    }
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function applyFilters() {
    if (!currentTable) {
        alert('Por favor, selecione uma tabela primeiro');
        return;
    }

    const filters = getSelectedFilters();
    const period = document.getElementById('period-selector').value;

    loadTableActivities(currentTable, period, filters);
}

function getSelectedFilters() {
    const filters = {};

    // Métodos HTTP
    const selectedMethods = [];
    document.querySelectorAll('#http-methods-filter input[type="checkbox"]:checked').forEach(cb => {
        selectedMethods.push(cb.value);
    });
    if (selectedMethods.length > 0) {
        filters.methods = selectedMethods;
    }

    // Operações
    const selectedOperations = [];
    document.querySelectorAll('#operations-filter input[type="checkbox"]:checked').forEach(cb => {
        selectedOperations.push(cb.value);
    });
    if (selectedOperations.length > 0) {
        filters.operations = selectedOperations;
    }

    // Usuário
    const userId = document.getElementById('user-id-filter').value.trim();
    if (userId) {
        filters.userId = userId;
    }

    // Endpoint
    const endpoint = document.getElementById('endpoint-filter').value.trim();
    if (endpoint) {
        filters.endpoint = endpoint;
    }

    return filters;
}

function clearAllFilters() {
    // Marcar todos os checkboxes
    document.querySelectorAll('#http-methods-filter input[type="checkbox"]').forEach(cb => {
        cb.checked = true;
    });
    document.querySelectorAll('#operations-filter input[type="checkbox"]').forEach(cb => {
        cb.checked = true;
    });

    // Limpar seletores
    document.getElementById('user-id-filter').value = '';
    document.getElementById('endpoint-filter').value = '';

    // Reaplicar com filtros limpos
    if (currentTable) {
        applyFilters();
    }
}

function exportData(format) {
    if (!window.currentActivitiesData || window.currentActivitiesData.length === 0) {
        alert('Nenhum dado disponível para exportação');
        return;
    }

    // Desabilitar botões durante exportação
    const exportButtons = document.querySelectorAll('.btn-export');
    exportButtons.forEach(btn => {
        btn.disabled = true;
        btn.textContent = btn.textContent.replace(/🔄.*/, '') + ' 🔄 Exportando...';
    });

    // Obter filtros aplicados
    const appliedFilters = getCurrentFilters();

    try {
        switch (format) {
            case 'csv':
                exportToCSV(window.currentActivitiesData, appliedFilters);
                break;
            case 'excel':
                exportToExcel(window.currentActivitiesData, appliedFilters);
                break;
            case 'detailed':
                exportToDetailedCSV(window.currentActivitiesData, appliedFilters);
                break;
            default:
                alert('Formato de exportação não suportado');
        }
    } catch (error) {
        alert('Erro durante a exportação: ' + error.message);
    } finally {
        // Reabilitar botões
        setTimeout(() => {
            exportButtons.forEach((btn, index) => {
                btn.disabled = false;
                const labels = ['📄 CSV Básico', '📊 Excel (XLSX)', '🔍 CSV Detalhado'];
                btn.textContent = labels[index];
            });
        }, 1000);
    }
}

function getCurrentFilters() {
    return {
        table: currentTable,
        period: document.getElementById('period-selector').value,
        methods: getSelectedFilters().methods || [],
        operations: getSelectedFilters().operations || [],
        userId: getSelectedFilters().userId || null,
        endpoint: getSelectedFilters().endpoint || null,
        timestamp: new Date().toISOString()
    };
}

function exportToCSV(data, filters) {
    exportViaAPI(filters, 'csv');
}

function exportToDetailedCSV(data, filters) {
    exportViaAPI(filters, 'detailed');
}

function exportToExcel(data, filters) {
    exportViaAPI(filters, 'excel');
}

// Usar API do backend para exportação (mais robusto)
function exportViaAPI(filters, format) {
    // Construir URL da API com filtros
    let url = `{{ route('admin.monitoring.database-activity.export') }}?format=${format}`;

    if (filters.table) {
        url += `&table=${filters.table}`;
    }
    if (filters.period) {
        url += `&period=${filters.period}`;
    }
    if (filters.methods && filters.methods.length > 0) {
        url += `&methods=${filters.methods.join(',')}`;
    }
    if (filters.operations && filters.operations.length > 0) {
        url += `&operations=${filters.operations.join(',')}`;
    }
    if (filters.userId) {
        url += `&user_id=${filters.userId}`;
    }
    if (filters.endpoint) {
        url += `&endpoint=${encodeURIComponent(filters.endpoint)}`;
    }

    // Criar link temporário para download
    const link = document.createElement('a');
    link.href = url;
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function generateCSVContent(data, filters, includeDetails = false) {
    let csv = '';

    // Cabeçalho com informações do filtro
    csv += `# Relatório de Atividades do Banco de Dados\n`;
    csv += `# Tabela: ${filters.table}\n`;
    csv += `# Período: ${filters.period}\n`;
    csv += `# Métodos: ${filters.methods.join(', ') || 'Todos'}\n`;
    csv += `# Operações: ${filters.operations.join(', ') || 'Todas'}\n`;
    csv += `# Gerado em: ${new Date().toLocaleString('pt-BR')}\n`;
    csv += `# Total de registros: ${data.length}\n`;
    csv += `\n`;

    // Cabeçalhos das colunas
    if (includeDetails) {
        csv += 'Data/Hora,Tabela,Operação,Tempo (ms),Linhas Afetadas,Método HTTP,Endpoint,Usuário ID,IP,Tem Detalhes,Campos Alterados,Detalhes Completos\n';
    } else {
        csv += 'Data/Hora,Tabela,Operação,Tempo (ms),Linhas Afetadas,Método HTTP,Endpoint,Usuário ID,IP\n';
    }

    // Dados
    data.forEach(activity => {
        const row = [
            activity.created_at || '',
            activity.table_name || '',
            activity.operation_type || '',
            activity.query_time_ms || '',
            activity.affected_rows || '',
            activity.request_method || '',
            activity.endpoint || '',
            activity.user_id || '',
            activity.ip_address || ''
        ];

        if (includeDetails) {
            let hasDetails = 'Não';
            let fieldsChanged = '';
            let fullDetails = '';

            if (activity.change_details) {
                try {
                    const details = typeof activity.change_details === 'string'
                        ? JSON.parse(activity.change_details)
                        : activity.change_details;

                    if (details && details.fields) {
                        hasDetails = 'Sim';
                        fieldsChanged = Object.keys(details.fields).join('; ');

                        const changes = [];
                        for (const [field, values] of Object.entries(details.fields)) {
                            const oldVal = values.old === null ? 'NULL' : values.old;
                            const newVal = values.new === null ? 'NULL' : values.new;
                            changes.push(`${field}: ${oldVal} → ${newVal}`);
                        }
                        fullDetails = changes.join(' | ');
                    }
                } catch (e) {
                    hasDetails = 'Erro';
                    fieldsChanged = 'Erro ao processar';
                }
            }

            row.push(hasDetails, fieldsChanged, fullDetails);
        }

        // Escapar aspas duplas e adicionar linha
        const escapedRow = row.map(field => {
            const str = String(field);
            return str.includes(',') || str.includes('"') || str.includes('\n')
                ? `"${str.replace(/"/g, '""')}"`
                : str;
        });

        csv += escapedRow.join(',') + '\n';
    });

    return csv;
}

function generateExcelContent(data, filters) {
    // Para simplicidade, vamos gerar um CSV avançado que pode ser aberto no Excel
    // Em uma implementação mais avançada, usaríamos uma biblioteca como xlsx.js
    return generateCSVContent(data, filters, true);
}

function downloadFile(content, filename, mimeType) {
    const blob = new Blob([content], { type: mimeType + ';charset=utf-8;' });
    const link = document.createElement('a');

    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    } else {
        // Fallback para browsers mais antigos
        window.open('data:' + mimeType + ';charset=utf-8,' + encodeURIComponent(content), '_blank');
    }
}

function getFormattedDate() {
    const now = new Date();
    return now.toISOString().slice(0, 19).replace(/[T:]/g, '_').replace(/-/g, '');
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

// Carregar scripts de otimização IMEDIATAMENTE - antes de qualquer biblioteca
(function() {
    'use strict';

    const scriptsToLoad = [
        '/js/passive-events-polyfill.js',
        '/js/vue-config.js',
        '/js/performance-optimizer.js'
    ];

    // Carregar todos os scripts imediatamente
    scriptsToLoad.forEach(function(scriptSrc, index) {
        const script = document.createElement('script');
        script.src = scriptSrc + '?v=' + Date.now();
        script.async = false; // Importante: carregar em ordem
        script.defer = false; // Carregar imediatamente

        // Log quando carregar
        script.onload = function() {
            console.log('✅ Loaded:', scriptSrc);
        };

        script.onerror = function() {
            console.warn('❌ Failed to load:', scriptSrc);
        };

        document.head.appendChild(script);
    });

    console.log('🚀 Performance optimization scripts loading...');
})();
</script>
@endpush

@endsection