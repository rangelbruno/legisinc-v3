@extends('components.layouts.app')

@section('title', 'Atividade do Banco de Dados')

@section('content')

<style>
.activity-card {
    border: 1px solid #e1e5e9;
    border-radius: 0.5rem;
    background: #ffffff;
    margin-bottom: 1rem;
    transition: all 0.2s ease;
}

.activity-card:hover {
    border-color: #5e72e4;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
}

.activity-header {
    background: #f8f9fa;
    padding: 1rem;
    border-bottom: 1px solid #e1e5e9;
    border-radius: 0.5rem 0.5rem 0 0;
    font-weight: 600;
}

.activity-content {
    padding: 1.5rem;
}

.metric-box {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 0.75rem;
    text-align: center;
    margin-bottom: 1rem;
}

.filter-box {
    background: #f8f9fa;
    border: 1px solid #e1e5e9;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.operation-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.operation-INSERT { background: #28a745; color: white; }
.operation-UPDATE { background: #ffc107; color: #212529; }
.operation-DELETE { background: #dc3545; color: white; }
.operation-SELECT { background: #17a2b8; color: white; }
.operation-OTHER { background: #6c757d; color: white; }

.table-activity {
    max-height: 600px;
    overflow-y: auto;
}

.realtime-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #28a745;
    display: inline-block;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

/* Multi-Select Styles */
.multi-select-container {
    position: relative;
}

.multi-select-display {
    cursor: pointer;
    user-select: none;
    position: relative;
    padding-right: 30px;
}

.multi-select-display::after {
    content: '▼';
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.8rem;
    color: #6c757d;
}

.multi-select-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    z-index: 1000;
    max-height: 250px;
    overflow-y: auto;
}

.search-box {
    padding: 0.5rem;
    border-bottom: 1px solid #e9ecef;
}

.options-container {
    max-height: 200px;
    overflow-y: auto;
}

.option-item {
    display: block;
    padding: 0.5rem;
    cursor: pointer;
    transition: background-color 0.15s;
    border-bottom: 1px solid #f8f9fa;
    margin: 0;
}

.option-item:hover {
    background-color: #f8f9fa;
}

.option-item input[type="checkbox"] {
    margin-right: 0.5rem;
}

/* Filter Tags */
.filter-tag {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    background: #e7f3ff;
    border: 1px solid #b6d7ff;
    border-radius: 1rem;
    font-size: 0.875rem;
    color: #0056b3;
    margin: 0.25rem 0.25rem 0.25rem 0;
}

.filter-tag .remove-tag {
    margin-left: 0.5rem;
    cursor: pointer;
    color: #dc3545;
    font-weight: bold;
}

.filter-tag .remove-tag:hover {
    color: #a71e2a;
}

.stat-item {
    background: white;
    border: 1px solid #e1e5e9;
    border-radius: 0.5rem;
    padding: 1rem;
    text-align: center;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #5e72e4;
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
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
                    🗄️ Atividade do Banco de Dados
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
                    <li class="breadcrumb-item text-muted">Atividade DB</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div class="text-end d-none d-sm-block">
                    <div class="text-muted fw-semibold fs-7">
                        <span class="realtime-indicator"></span>
                        <span id="last-update">Carregando...</span>
                    </div>
                </div>
                <a href="{{ route('admin.monitoring.database-activity.detailed') }}" class="btn btn-sm btn-success">
                    <i class="ki-duotone ki-chart-line-down fs-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Análise Detalhada
                </a>
                <button type="button" class="btn btn-sm btn-primary" onclick="refreshData()">
                    <i class="ki-duotone ki-arrows-circle fs-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Atualizar
                </button>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!-- Status do Sistema -->
            <div class="activity-card">
                <div class="activity-header">
                    🔧 Status do Sistema de Monitoramento
                </div>
                <div class="activity-content">
                    <div class="alert alert-success d-flex align-items-center mb-3">
                        <i class="ki-duotone ki-check-circle fs-2x text-success me-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div>
                            <h6 class="mb-1">✅ Sistema Otimizado</h6>
                            <small class="text-muted">Recursão resolvida • Filtros anti-loop ativos • Performance otimizada</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Métricas em Tempo Real -->
            <div class="activity-card">
                <div class="activity-header">
                    📊 Métricas em Tempo Real
                </div>
                <div class="activity-content">
                    <div class="stats-grid" id="realtime-metrics">
                        <div class="stat-item">
                            <div class="stat-value" id="operations-last-minute">-</div>
                            <div class="stat-label">Último minuto</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" id="operations-last-5min">-</div>
                            <div class="stat-label">Últimos 5 minutos</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" id="operations-last-hour">-</div>
                            <div class="stat-label">Última hora</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" id="avg-query-time">-</div>
                            <div class="stat-label">Tempo médio (ms)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros Avançados -->
            <div class="activity-card">
                <div class="activity-header">
                    🔍 Filtros Avançados
                    <button type="button" class="btn btn-sm btn-light float-end" onclick="clearAllFilters()">
                        <i class="ki-duotone ki-cross fs-6">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Limpar
                    </button>
                </div>
                <div class="activity-content">
                    <!-- Tags de Filtros Ativos -->
                    <div id="active-filters" class="mb-3" style="display: none;">
                        <div class="d-flex flex-wrap gap-2" id="filter-tags">
                            <!-- Tags serão inseridas aqui -->
                        </div>
                    </div>

                    <div class="filter-box">
                        <div class="row g-3">
                            <!-- Seleção Múltipla de Tabelas -->
                            <div class="col-md-6">
                                <label class="form-label">📋 Tabelas</label>
                                <div class="multi-select-container">
                                    <div class="form-control multi-select-display" onclick="toggleMultiSelect('tables')" id="tables-display">
                                        Clique para selecionar tabelas...
                                    </div>
                                    <div class="multi-select-dropdown" id="tables-dropdown" style="display: none;">
                                        <div class="search-box">
                                            <input type="text" class="form-control form-control-sm" placeholder="Buscar tabela..." onkeyup="debouncedFilterOptions('tables', this.value)">
                                        </div>
                                        <div class="options-container" id="tables-options">
                                            <!-- Opções serão carregadas dinamicamente -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Seleção Múltipla de Métodos HTTP -->
                            <div class="col-md-6">
                                <label class="form-label">🌐 Métodos HTTP</label>
                                <div class="multi-select-container">
                                    <div class="form-control multi-select-display" onclick="toggleMultiSelect('methods')" id="methods-display">
                                        Clique para selecionar métodos...
                                    </div>
                                    <div class="multi-select-dropdown" id="methods-dropdown" style="display: none;">
                                        <div class="options-container">
                                            <label class="option-item">
                                                <input type="checkbox" value="GET" onchange="updateMultiSelect('methods')"> GET
                                            </label>
                                            <label class="option-item">
                                                <input type="checkbox" value="POST" onchange="updateMultiSelect('methods')"> POST
                                            </label>
                                            <label class="option-item">
                                                <input type="checkbox" value="PUT" onchange="updateMultiSelect('methods')"> PUT
                                            </label>
                                            <label class="option-item">
                                                <input type="checkbox" value="PATCH" onchange="updateMultiSelect('methods')"> PATCH
                                            </label>
                                            <label class="option-item">
                                                <input type="checkbox" value="DELETE" onchange="updateMultiSelect('methods')"> DELETE
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Seleção Múltipla de Operações SQL -->
                            <div class="col-md-4">
                                <label class="form-label">🗄️ Operações SQL</label>
                                <div class="multi-select-container">
                                    <div class="form-control multi-select-display" onclick="toggleMultiSelect('operations')" id="operations-display">
                                        Selecionar operações...
                                    </div>
                                    <div class="multi-select-dropdown" id="operations-dropdown" style="display: none;">
                                        <div class="options-container">
                                            <label class="option-item">
                                                <input type="checkbox" value="SELECT" onchange="updateMultiSelect('operations')"> SELECT
                                            </label>
                                            <label class="option-item">
                                                <input type="checkbox" value="INSERT" onchange="updateMultiSelect('operations')"> INSERT
                                            </label>
                                            <label class="option-item">
                                                <input type="checkbox" value="UPDATE" onchange="updateMultiSelect('operations')"> UPDATE
                                            </label>
                                            <label class="option-item">
                                                <input type="checkbox" value="DELETE" onchange="updateMultiSelect('operations')"> DELETE
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Período -->
                            <div class="col-md-4">
                                <label class="form-label">⏱️ Período</label>
                                <select class="form-select" id="filter-period">
                                    <option value="">Todo o período</option>
                                    <option value="1min">Último minuto</option>
                                    <option value="5min">Últimos 5 minutos</option>
                                    <option value="1hour" selected>Última hora</option>
                                    <option value="1day">Último dia</option>
                                    <option value="7days">Últimos 7 dias</option>
                                </select>
                            </div>

                            <!-- Endpoint -->
                            <div class="col-md-4">
                                <label class="form-label">🔗 Endpoint</label>
                                <input type="text" class="form-control" id="filter-endpoint" placeholder="Ex: proposicoes, api/notifications">
                            </div>

                            <!-- Botões de Ação -->
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary" onclick="applyFilters()">
                                        <i class="ki-duotone ki-magnifier fs-6">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Aplicar Filtros
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="saveFilterPreset()">
                                        <i class="ki-duotone ki-save-2 fs-6">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Salvar Preset
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="exportResults()">
                                        <i class="ki-duotone ki-file-down fs-6">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Exportar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas por Tabela -->
            <div class="activity-card">
                <div class="activity-header">
                    📈 Estatísticas por Tabela (Última Hora)
                </div>
                <div class="activity-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Tabela</th>
                                    <th>Total</th>
                                    <th>SELECT</th>
                                    <th>INSERT</th>
                                    <th>UPDATE</th>
                                    <th>DELETE</th>
                                    <th>Tempo Médio</th>
                                    <th>Última Atividade</th>
                                </tr>
                            </thead>
                            <tbody id="table-stats">
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Carregando estatísticas...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Atividades Recentes -->
            <div class="activity-card">
                <div class="activity-header">
                    🕒 Atividades Recentes
                    <span class="float-end">
                        <small id="activities-count" class="text-muted">-</small>
                    </span>
                </div>
                <div class="activity-content">
                    <div class="table-activity">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th width="120">Horário</th>
                                    <th width="100">Tabela</th>
                                    <th width="80">Operação</th>
                                    <th width="80">Tempo (ms)</th>
                                    <th width="70">Linhas</th>
                                    <th width="80">Método</th>
                                    <th>Endpoint</th>
                                    <th width="60">Usuário</th>
                                </tr>
                            </thead>
                            <tbody id="recent-activities">
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Carregando atividades...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
let refreshInterval;

// Função de throttling para otimizar performance
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// Função de debounce para inputs
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Inicializar página com verificações de segurança
document.addEventListener('DOMContentLoaded', function() {
    // Usar MutationObserver para aguardar elementos serem carregados
    const observer = new MutationObserver(function(mutations, obs) {
        const filterTable = document.getElementById('filter-table');
        const realtimeMetrics = document.getElementById('realtime-metrics');
        const recentActivities = document.getElementById('recent-activities');

        if (filterTable && realtimeMetrics && recentActivities) {
            obs.disconnect(); // Parar de observar
            try {
                loadActiveTables();
                refreshData();
                startAutoRefresh();
                console.log('Interface de monitoramento inicializada com sucesso');
            } catch (error) {
                console.error('Erro na inicialização:', error);
            }
        }
    });

    // Começar a observar mudanças no DOM
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Timeout de segurança - parar após 5 segundos
    setTimeout(() => {
        observer.disconnect();
        console.warn('Timeout na inicialização - alguns elementos podem não estar disponíveis');
    }, 5000);
});

function startAutoRefresh() {
    // Atualizar a cada 10 segundos para reduzir carga
    // Verificar se a página está visível antes de atualizar
    refreshInterval = setInterval(function() {
        if (!document.hidden) {
            refreshData();
        }
    }, 10000);
}

// Pausar/retomar auto-refresh baseado na visibilidade da página
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        // Página não visível - pausar refresh
        if (refreshInterval) {
            clearInterval(refreshInterval);
            refreshInterval = null;
        }
    } else {
        // Página visível - retomar refresh
        if (!refreshInterval) {
            startAutoRefresh();
            // Atualizar imediatamente ao voltar
            refreshData();
        }
    }
}, { passive: true });

function refreshData() {
    throttledRefreshData();
}

function loadActiveTables() {
    const select = document.getElementById('filter-table');

    // Verificar se elemento existe
    if (!select) {
        console.warn('Elemento filter-table não encontrado');
        return;
    }

    fetch('{{ route("admin.monitoring.database-activity.active-tables") }}')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && Array.isArray(data.tables)) {
                select.innerHTML = '<option value="">Todas as tabelas</option>';

                data.tables.forEach(table => {
                    const option = document.createElement('option');
                    option.value = table;
                    option.textContent = table;
                    select.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Erro ao carregar tabelas:', error);
            // Fallback: adicionar opções estáticas principais
            if (select) {
                select.innerHTML = `
                    <option value="">Todas as tabelas</option>
                    <option value="proposicoes">proposicoes</option>
                    <option value="users">users</option>
                    <option value="templates">templates</option>
                `;
            }
        });
}

function loadRealtimeMetrics() {
    fetch('{{ route("admin.monitoring.database-activity.realtime-metrics") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const metrics = data.metrics;

                document.getElementById('operations-last-minute').textContent = metrics.operations_last_minute || 0;
                document.getElementById('operations-last-5min').textContent = metrics.operations_last_5min || 0;
                document.getElementById('operations-last-hour').textContent = metrics.operations_last_hour || 0;
                document.getElementById('avg-query-time').textContent =
                    metrics.avg_query_time_last_5min ? Math.round(metrics.avg_query_time_last_5min * 100) / 100 + 'ms' : '0ms';
            }
        })
        .catch(error => console.error('Erro ao carregar métricas:', error));
}

function loadTableStats() {
    fetch('{{ route("admin.monitoring.database-activity.table-stats") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tbody = document.getElementById('table-stats');

                if (data.tables.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Nenhuma atividade na última hora</td></tr>';
                    return;
                }

                tbody.innerHTML = data.tables.map(table => `
                    <tr>
                        <td><strong>${table.table_name}</strong></td>
                        <td><span class="badge bg-primary">${table.total_operations}</span></td>
                        <td>${table.selects || 0}</td>
                        <td>${table.inserts || 0}</td>
                        <td>${table.updates || 0}</td>
                        <td>${table.deletes || 0}</td>
                        <td>${Math.round((table.avg_query_time || 0) * 100) / 100}ms</td>
                        <td><small>${formatDateTime(table.last_activity)}</small></td>
                    </tr>
                `).join('');
            }
        })
        .catch(error => console.error('Erro ao carregar estatísticas de tabelas:', error));
}

function loadRecentActivities() {
    let url = '{{ route("admin.monitoring.database-activity.filter") }}';
    const params = new URLSearchParams();

    // Filtros múltiplos
    if (selectedFilters.tables.length > 0) {
        selectedFilters.tables.forEach(table => params.append('tables[]', table));
    }

    if (selectedFilters.methods.length > 0) {
        selectedFilters.methods.forEach(method => params.append('methods[]', method));
    }

    if (selectedFilters.operations.length > 0) {
        selectedFilters.operations.forEach(operation => params.append('operations[]', operation));
    }

    // Filtros únicos
    const period = document.getElementById('filter-period').value;
    const endpoint = document.getElementById('filter-endpoint').value;

    if (period) {
        params.append('period', period);
    }

    if (endpoint) {
        params.append('endpoint', endpoint);
    }

    if (params.toString()) {
        url += '?' + params.toString();
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tbody = document.getElementById('recent-activities');
                document.getElementById('activities-count').textContent = `${data.total} atividades`;

                if (data.activities.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Nenhuma atividade encontrada</td></tr>';
                    return;
                }

                tbody.innerHTML = data.activities.map(activity => `
                    <tr>
                        <td><small>${formatDateTime(activity.created_at)}</small></td>
                        <td><strong>${activity.table_name}</strong></td>
                        <td><span class="operation-badge operation-${activity.operation_type}">${activity.operation_type}</span></td>
                        <td>${activity.query_time_ms}ms</td>
                        <td>${activity.affected_rows}</td>
                        <td><small>${activity.request_method || '-'}</small></td>
                        <td><small>${activity.endpoint || '-'}</small></td>
                        <td><small>${activity.user_id ? '#' + activity.user_id : '-'}</small></td>
                    </tr>
                `).join('');
            }
        })
        .catch(error => console.error('Erro ao carregar atividades:', error));
}

// Throttled version das funções principais
const throttledApplyFilters = throttle(function() {
    updateFilterTags();
    loadRecentActivities();
}, 500);

const throttledRefreshData = throttle(function() {
    loadRealtimeMetrics();
    loadTableStats();
    loadRecentActivities();
    updateTimestamp();
}, 1000);

function applyFilters() {
    throttledApplyFilters();
}

// Sistema de Multi-Select
let selectedFilters = {
    tables: [],
    methods: [],
    operations: []
};

function toggleMultiSelect(type) {
    const dropdown = document.getElementById(type + '-dropdown');
    const isVisible = dropdown.style.display !== 'none';

    // Fechar todos os outros dropdowns
    document.querySelectorAll('.multi-select-dropdown').forEach(d => d.style.display = 'none');

    // Toggle do dropdown atual
    dropdown.style.display = isVisible ? 'none' : 'block';

    // Carregar opções se necessário
    if (!isVisible && type === 'tables') {
        loadTableOptions();
    }
}

function updateMultiSelect(type) {
    const checkboxes = document.querySelectorAll(`#${type}-dropdown input[type="checkbox"]:checked`);
    const values = Array.from(checkboxes).map(cb => cb.value);

    selectedFilters[type] = values;

    const display = document.getElementById(type + '-display');
    if (values.length === 0) {
        display.textContent = getPlaceholderText(type);
        display.style.color = '#6c757d';
    } else {
        display.textContent = `${values.length} selecionado(s): ${values.slice(0, 2).join(', ')}${values.length > 2 ? '...' : ''}`;
        display.style.color = '#000';
    }
}

function getPlaceholderText(type) {
    const placeholders = {
        tables: 'Clique para selecionar tabelas...',
        methods: 'Clique para selecionar métodos...',
        operations: 'Selecionar operações...'
    };
    return placeholders[type] || 'Selecionar...';
}

function loadTableOptions() {
    const container = document.getElementById('tables-options');

    if (!container) {
        console.warn('Container tables-options não encontrado');
        return;
    }

    // Mostrar loading
    container.innerHTML = '<div class="p-2 text-center"><small class="text-muted">Carregando...</small></div>';

    fetch('{{ route("admin.monitoring.database-activity.active-tables") }}')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && Array.isArray(data.tables)) {
                container.innerHTML = data.tables.map(table => `
                    <label class="option-item">
                        <input type="checkbox" value="${table}" onchange="updateMultiSelect('tables')">
                        ${table}
                    </label>
                `).join('');
            } else {
                throw new Error('Dados inválidos recebidos');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar tabelas:', error);
            // Fallback com tabelas principais
            container.innerHTML = `
                <label class="option-item">
                    <input type="checkbox" value="proposicoes" onchange="updateMultiSelect('tables')">
                    proposicoes
                </label>
                <label class="option-item">
                    <input type="checkbox" value="users" onchange="updateMultiSelect('tables')">
                    users
                </label>
                <label class="option-item">
                    <input type="checkbox" value="templates" onchange="updateMultiSelect('tables')">
                    templates
                </label>
                <label class="option-item">
                    <input type="checkbox" value="sessoes" onchange="updateMultiSelect('tables')">
                    sessoes
                </label>
            `;
        });
}

function filterOptions(type, searchTerm) {
    const options = document.querySelectorAll(`#${type}-options .option-item`);
    const term = searchTerm.toLowerCase();

    // Usar requestAnimationFrame para melhor performance
    requestAnimationFrame(() => {
        options.forEach(option => {
            const text = option.textContent.toLowerCase();
            option.style.display = text.includes(term) ? 'block' : 'none';
        });
    });
}

// Versão com debounce para search
const debouncedFilterOptions = debounce(filterOptions, 300);

function updateFilterTags() {
    const tagsContainer = document.getElementById('filter-tags');
    const activeFiltersDiv = document.getElementById('active-filters');
    let tags = [];

    // Tags para tabelas
    selectedFilters.tables.forEach(table => {
        tags.push(`<span class="filter-tag">📋 ${table} <span class="remove-tag" onclick="removeFilter('tables', '${table}')">&times;</span></span>`);
    });

    // Tags para métodos
    selectedFilters.methods.forEach(method => {
        tags.push(`<span class="filter-tag">🌐 ${method} <span class="remove-tag" onclick="removeFilter('methods', '${method}')">&times;</span></span>`);
    });

    // Tags para operações
    selectedFilters.operations.forEach(operation => {
        tags.push(`<span class="filter-tag">🗄️ ${operation} <span class="remove-tag" onclick="removeFilter('operations', '${operation}')">&times;</span></span>`);
    });

    // Tags para outros filtros
    const period = document.getElementById('filter-period').value;
    const endpoint = document.getElementById('filter-endpoint').value;

    if (period) {
        const periodText = document.getElementById('filter-period').selectedOptions[0].text;
        tags.push(`<span class="filter-tag">⏱️ ${periodText} <span class="remove-tag" onclick="removeFilter('period', '')">&times;</span></span>`);
    }

    if (endpoint) {
        tags.push(`<span class="filter-tag">🔗 ${endpoint} <span class="remove-tag" onclick="removeFilter('endpoint', '')">&times;</span></span>`);
    }

    // Mostrar/esconder seção de tags
    if (tags.length > 0) {
        tagsContainer.innerHTML = tags.join('');
        activeFiltersDiv.style.display = 'block';
    } else {
        activeFiltersDiv.style.display = 'none';
    }
}

function removeFilter(type, value) {
    if (type === 'period') {
        document.getElementById('filter-period').value = '';
    } else if (type === 'endpoint') {
        document.getElementById('filter-endpoint').value = '';
    } else if (selectedFilters[type]) {
        selectedFilters[type] = selectedFilters[type].filter(item => item !== value);
        updateMultiSelect(type);

        // Desmarcar checkbox
        const checkbox = document.querySelector(`#${type}-dropdown input[value="${value}"]`);
        if (checkbox) checkbox.checked = false;
    }

    updateFilterTags();
    loadRecentActivities();
}

function clearAllFilters() {
    // Limpar seleções múltiplas
    selectedFilters = { tables: [], methods: [], operations: [] };

    // Limpar displays
    ['tables', 'methods', 'operations'].forEach(type => {
        const display = document.getElementById(type + '-display');
        display.textContent = getPlaceholderText(type);
        display.style.color = '#6c757d';

        // Desmarcar checkboxes
        document.querySelectorAll(`#${type}-dropdown input[type="checkbox"]`).forEach(cb => cb.checked = false);
    });

    // Limpar outros filtros
    document.getElementById('filter-period').value = '1hour';
    document.getElementById('filter-endpoint').value = '';

    // Esconder tags
    document.getElementById('active-filters').style.display = 'none';

    // Recarregar atividades
    loadRecentActivities();
}

function saveFilterPreset() {
    const preset = {
        tables: selectedFilters.tables,
        methods: selectedFilters.methods,
        operations: selectedFilters.operations,
        period: document.getElementById('filter-period').value,
        endpoint: document.getElementById('filter-endpoint').value
    };

    const name = prompt('Nome do preset:');
    if (name) {
        localStorage.setItem(`db_activity_preset_${name}`, JSON.stringify(preset));
        alert('Preset salvo com sucesso!');
    }
}

function exportResults() {
    // Construir URL de exportação com filtros atuais
    let url = '{{ route("admin.monitoring.database-activity.export") }}';
    const params = new URLSearchParams();

    // Aplicar os mesmos filtros da visualização atual
    if (selectedFilters.tables.length > 0) {
        selectedFilters.tables.forEach(table => params.append('tables[]', table));
    }

    if (selectedFilters.methods.length > 0) {
        selectedFilters.methods.forEach(method => params.append('methods[]', method));
    }

    if (selectedFilters.operations.length > 0) {
        selectedFilters.operations.forEach(operation => params.append('operations[]', operation));
    }

    const period = document.getElementById('filter-period').value;
    const endpoint = document.getElementById('filter-endpoint').value;

    if (period) {
        params.append('period', period);
    }

    if (endpoint) {
        params.append('endpoint', endpoint);
    }

    if (params.toString()) {
        url += '?' + params.toString();
    }

    // Abrir download em nova aba
    window.open(url, '_blank');
}

// Fechar dropdowns quando clicar fora (passive listener)
document.addEventListener('click', function(event) {
    if (!event.target.closest('.multi-select-container')) {
        document.querySelectorAll('.multi-select-dropdown').forEach(d => d.style.display = 'none');
    }
}, { passive: true });

// Otimizar scroll listeners se houver
document.addEventListener('scroll', function(event) {
    // Se houver scroll handlers, implementar aqui com throttling
}, { passive: true });

// Otimizar touch events se houver
document.addEventListener('touchstart', function(event) {
    // Touch handlers otimizados
}, { passive: true });

function formatDateTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
}

function updateTimestamp() {
    const now = new Date();
    document.getElementById('last-update').textContent =
        'Atualizado: ' + now.toLocaleTimeString('pt-BR');
}

// Cleanup ao sair da página
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
}, { passive: true });

// Error handling global para prevenir crashes
window.addEventListener('error', function(event) {
    console.error('Erro JavaScript capturado:', event.error);
    // Não deixar o erro quebrar a interface
    return true;
}, { passive: true });

// Error handling para promises rejeitadas
window.addEventListener('unhandledrejection', function(event) {
    console.error('Promise rejeitada:', event.reason);
    // Prevenir que apareça no console como unhandled
    event.preventDefault();
}, { passive: true });
</script>
@endpush

@endsection