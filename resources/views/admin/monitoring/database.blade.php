@extends('components.layouts.app')

@section('title', 'Monitoramento de Banco de Dados')

@section('content')

<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    🗃️ Monitoramento de Banco de Dados
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Administração</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.monitoring.index') }}" class="text-muted text-hover-primary">Observabilidade</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Banco de Dados</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div class="text-end d-none d-sm-block">
                    <div class="text-muted fw-semibold fs-7" id="last-update">{{ date('d/m/Y H:i:s') }}</div>
                </div>
                <a href="{{ route('admin.monitoring.index') }}" class="btn btn-sm btn-light">
                    <i class="ki-duotone ki-left fs-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Voltar
                </a>
                <button type="button" class="btn btn-sm btn-success" id="start-monitoring" onclick="startMonitoring()">
                    <i class="ki-duotone ki-play fs-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Iniciar Monitoramento
                </button>
                <button type="button" class="btn btn-sm btn-danger d-none" id="stop-monitoring" onclick="stopMonitoring()">
                    <i class="ki-duotone ki-stop fs-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Parar Monitoramento
                </button>
                <button type="button" class="btn btn-sm btn-info" id="export-data" onclick="exportData()" disabled>
                    <i class="ki-duotone ki-file-down fs-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Exportar
                </button>
                <button type="button" class="btn btn-sm btn-warning" id="clear-data" onclick="clearData()" disabled>
                    <i class="ki-duotone ki-trash fs-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Limpar
                </button>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!-- Status do Monitoramento -->
            <div class="row g-5 g-xl-8 mb-5" id="monitoring-status" style="display: none;">
                <div class="col-12">
                    <div class="alert alert-success d-flex align-items-center">
                        <i class="ki-duotone ki-check-circle fs-2x me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1">🔍 Monitoramento de Banco Ativo</h4>
                            <span id="monitoring-info">Capturando queries SQL em tempo real...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Widgets de Estatísticas -->
            <div class="row g-5 g-xl-8">
                
                <!-- Total de Queries -->
                <div class="col-xl-3">
                    <div class="card card-xl-stretch mb-xl-8">
                        <div class="card-header border-0 bg-primary py-5">
                            <h3 class="card-title fw-bold text-white">📊 Total Queries</h3>
                        </div>
                        <div class="card-body py-3">
                            <div class="text-gray-900 fw-bold fs-2x mb-1" id="total-queries">0</div>
                            <div class="fw-semibold text-muted fs-7">Queries executadas</div>
                        </div>
                    </div>
                </div>

                <!-- Tempo Médio -->
                <div class="col-xl-3">
                    <div class="card card-xl-stretch mb-xl-8">
                        <div class="card-header border-0 bg-warning py-5">
                            <h3 class="card-title fw-bold text-white">⚡ Tempo Médio</h3>
                        </div>
                        <div class="card-body py-3">
                            <div class="text-gray-900 fw-bold fs-2x mb-1" id="avg-time">0ms</div>
                            <div class="fw-semibold text-muted fs-7">Tempo de execução</div>
                        </div>
                    </div>
                </div>

                <!-- Queries Lentas -->
                <div class="col-xl-3">
                    <div class="card card-xl-stretch mb-xl-8">
                        <div class="card-header border-0 bg-danger py-5">
                            <h3 class="card-title fw-bold text-white">🐌 Queries Lentas</h3>
                        </div>
                        <div class="card-body py-3">
                            <div class="text-gray-900 fw-bold fs-2x mb-1" id="slow-queries">0</div>
                            <div class="fw-semibold text-muted fs-7">Mais de 100ms</div>
                        </div>
                    </div>
                </div>

                <!-- Tabelas Mais Usadas -->
                <div class="col-xl-3">
                    <div class="card card-xl-stretch mb-xl-8">
                        <div class="card-header border-0 bg-info py-5">
                            <h3 class="card-title fw-bold text-white">🎯 Tabela Principal</h3>
                        </div>
                        <div class="card-body py-3">
                            <div class="text-gray-900 fw-bold fs-2x mb-1" id="main-table">-</div>
                            <div class="fw-semibold text-muted fs-7" id="main-table-count">Tabela mais utilizada</div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Row-->

            <!-- Seções com Tabs -->
            <div class="row g-5 g-xl-8 mt-5">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#queries-tab">
                                        🔍 Queries em Tempo Real
                                        <span class="badge badge-sm badge-circle badge-light-primary ms-2" id="queries-count">0</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#methods-tab">
                                        🌐 Métodos HTTP → SQL
                                        <span class="badge badge-sm badge-circle badge-light-warning ms-2" id="methods-count">0</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#stats-tab">📈 Estatísticas</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="monitoringTabContent">
                                
                                <!-- Tab: Queries em Tempo Real -->
                                <div class="tab-pane fade show active" id="queries-tab">
                                    <div id="queries-container">
                                        <div class="text-center text-muted py-10">
                                            <i class="ki-duotone ki-database fs-4x mb-5">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <h3>Inicie o monitoramento para ver as queries</h3>
                                            <p>Clique em "Iniciar Monitoramento" para capturar queries SQL em tempo real</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab: Métodos HTTP → SQL -->
                                <div class="tab-pane fade" id="methods-tab">
                                    <div id="methods-container">
                                        <div class="text-center text-muted py-10">
                                            <i class="ki-duotone ki-router fs-4x mb-5">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <h3>Mapeamento HTTP → SQL</h3>
                                            <p>Quando o monitoramento estiver ativo, você verá quais métodos HTTP geram quais operações SQL</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab: Estatísticas -->
                                <div class="tab-pane fade" id="stats-tab">
                                    <div id="stats-container">
                                        <div class="text-center text-muted py-10">
                                            <i class="ki-duotone ki-chart-pie-simple fs-4x mb-5">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <h3>Estatísticas detalhadas</h3>
                                            <p>Estatísticas por tipo de query, tabela e performance</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!--end::Container-->
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let monitoringActive = false;
let pollingInterval = null;
let httpMethodsMap = new Map();
let sessionId = null;
let hasData = false;

// Check status on page load
document.addEventListener('DOMContentLoaded', function() {
    checkMonitoringStatus();
});

function checkMonitoringStatus() {
    fetch('{{ route("debug.status") }}')
        .then(response => response.json())
        .then(data => {
            if (data.active && data.db_capture_active) {
                monitoringActive = true;
                sessionId = data.session_id;
                updateUIForActiveMonitoring();
                startPolling();
            } else {
                // Check if there's cached data available for export
                checkDataAvailability();
            }
        })
        .catch(error => {
            console.error('Erro ao verificar status:', error);
        });
}

function checkDataAvailability() {
    fetch('{{ route("debug.database.queries") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.queries && data.queries.length > 0) {
                hasData = true;
                updateButtonStates();
            }
        })
        .catch(error => {
            // Silently ignore errors when checking data availability
        });
}

function startMonitoring() {
    const startButton = document.getElementById('start-monitoring');
    const stopButton = document.getElementById('stop-monitoring');
    
    startButton.disabled = true;
    startButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Iniciando...';
    
    fetch('{{ route("debug.start") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'started') {
            monitoringActive = true;
            sessionId = data.session_id;
            httpMethodsMap.clear();
            updateUIForActiveMonitoring();
            startPolling();
            clearQueryResults();
            showSuccess('Monitoramento iniciado com sucesso!');
        }
    })
    .catch(error => {
        console.error('Erro ao iniciar monitoramento:', error);
        showError('Erro ao iniciar monitoramento');
        startButton.disabled = false;
        startButton.innerHTML = '<i class="ki-duotone ki-play fs-5"><span class="path1"></span><span class="path2"></span></i> Iniciar Monitoramento';
    });
}

function stopMonitoring() {
    const startButton = document.getElementById('start-monitoring');
    const stopButton = document.getElementById('stop-monitoring');
    
    stopButton.disabled = true;
    stopButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Parando...';
    
    fetch('{{ route("debug.stop") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        monitoringActive = false;
        sessionId = null;
        updateUIForInactiveMonitoring();
        stopPolling();
        showSuccess('Monitoramento parado. Duração: ' + data.duration + ' segundos');
    })
    .catch(error => {
        console.error('Erro ao parar monitoramento:', error);
        showError('Erro ao parar monitoramento');
        stopButton.disabled = false;
        stopButton.innerHTML = '<i class="ki-duotone ki-stop fs-5"><span class="path1"></span><span class="path2"></span></i> Parar Monitoramento';
    });
}

function updateUIForActiveMonitoring() {
    document.getElementById('start-monitoring').classList.add('d-none');
    document.getElementById('stop-monitoring').classList.remove('d-none');
    document.getElementById('monitoring-status').style.display = 'block';
    document.getElementById('monitoring-info').textContent = `Sessão: ${sessionId} - Capturando dados...`;
    
    // Enable export and clear buttons
    updateButtonStates();
}

function updateUIForInactiveMonitoring() {
    document.getElementById('start-monitoring').classList.remove('d-none');
    document.getElementById('stop-monitoring').classList.add('d-none');
    document.getElementById('start-monitoring').disabled = false;
    document.getElementById('start-monitoring').innerHTML = '<i class="ki-duotone ki-play fs-5"><span class="path1"></span><span class="path2"></span></i> Iniciar Monitoramento';
    document.getElementById('stop-monitoring').disabled = false;
    document.getElementById('stop-monitoring').innerHTML = '<i class="ki-duotone ki-stop fs-5"><span class="path1"></span><span class="path2"></span></i> Parar Monitoramento';
    document.getElementById('monitoring-status').style.display = 'none';
    
    // Update button states based on data availability
    updateButtonStates();
}

function startPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
    
    pollingInterval = setInterval(() => {
        if (monitoringActive) {
            fetchQueries();
        }
    }, 2000); // Poll every 2 seconds
}

function stopPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
}

function fetchQueries() {
    fetch('{{ route("debug.database.queries") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateQueryDisplay(data.queries);
                updateStatistics(data.statistics, data.queries);
                updateTimestamp();
                
                // Update data availability
                hasData = data.queries && data.queries.length > 0;
                updateButtonStates();
            }
        })
        .catch(error => {
            console.error('Erro ao buscar queries:', error);
        });
}

function updateQueryDisplay(queries) {
    const container = document.getElementById('queries-container');
    
    if (queries.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-10">
                <i class="ki-duotone ki-database fs-4x mb-5">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <h3>Monitoramento ativo - aguardando queries</h3>
                <p>Execute algumas ações no sistema para ver as queries SQL</p>
            </div>
        `;
        return;
    }
    
    let html = '<div class="row g-5">';
    
    queries.slice(-10).reverse().forEach((query, index) => {
        const performanceClass = getPerformanceClass(query.performance);
        const typeClass = getTypeClass(query.type);
        
        html += `
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge badge-${typeClass} me-3">${query.type}</span>
                                <span class="badge badge-${performanceClass}">${query.time_formatted}</span>
                                ${query.tables.length > 0 ? `<span class="badge badge-light-info ms-2">${query.tables.join(', ')}</span>` : ''}
                            </div>
                            <small class="text-muted">${formatTimestamp(query.timestamp)}</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <code class="text-dark fs-7">${escapeHtml(query.formatted_sql)}</code>
                        </div>
                        ${query.backtrace && query.backtrace.length > 0 ? `
                            <div class="collapse" id="backtrace-${index}">
                                <div class="border-top pt-3">
                                    <h6>Backtrace:</h6>
                                    ${query.backtrace.map(item => `
                                        <div class="text-muted fs-8">
                                            ${item.file}:${item.line} - ${item.class}${item.function}
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#backtrace-${index}">
                                Ver Backtrace
                            </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
    
    // Update counters
    document.getElementById('queries-count').textContent = queries.length;
}

function updateStatistics(stats, queries) {
    document.getElementById('total-queries').textContent = stats.total_queries || 0;
    document.getElementById('avg-time').textContent = Math.round(stats.average_time || 0) + 'ms';
    document.getElementById('slow-queries').textContent = (stats.slow_queries || 0) + (stats.very_slow_queries || 0);
    
    // Most used table
    const tables = stats.by_table || {};
    const mostUsedTable = Object.keys(tables).reduce((a, b) => tables[a].count > tables[b].count ? a : b, '');
    if (mostUsedTable) {
        document.getElementById('main-table').textContent = mostUsedTable;
        document.getElementById('main-table-count').textContent = `${tables[mostUsedTable].count} queries`;
    }
    
    // Update methods tab with queries data
    updateMethodsDisplay(stats, queries || []);
    
    // Update stats tab
    updateStatsDisplay(stats);
}

function updateMethodsDisplay(stats, queries) {
    const container = document.getElementById('methods-container');
    
    // Group queries by HTTP method and SQL operation
    const httpMethodMap = new Map();
    
    queries.forEach(query => {
        const httpMethod = query.http_method || 'UNKNOWN';
        const sqlType = query.type;
        const route = query.route_name || 'N/A';
        
        const key = `${httpMethod}`;
        
        if (!httpMethodMap.has(key)) {
            httpMethodMap.set(key, {
                method: httpMethod,
                operations: new Map(),
                total_queries: 0,
                total_time: 0
            });
        }
        
        const methodData = httpMethodMap.get(key);
        methodData.total_queries++;
        methodData.total_time += query.time;
        
        if (!methodData.operations.has(sqlType)) {
            methodData.operations.set(sqlType, {
                count: 0,
                time: 0,
                tables: new Set(),
                routes: new Set()
            });
        }
        
        const operationData = methodData.operations.get(sqlType);
        operationData.count++;
        operationData.time += query.time;
        query.tables.forEach(table => operationData.tables.add(table));
        if (route !== 'N/A') operationData.routes.add(route);
    });
    
    if (httpMethodMap.size === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-10">
                <i class="ki-duotone ki-router fs-4x mb-5">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <h3>Aguardando operações HTTP</h3>
                <p>Execute ações no sistema para ver o mapeamento HTTP → SQL</p>
            </div>
        `;
        return;
    }
    
    let html = '<div class="row g-5">';
    
    Array.from(httpMethodMap.values()).forEach(methodData => {
        const methodClass = getHttpMethodClass(methodData.method);
        
        html += `
            <div class="col-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light-${methodClass} py-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge badge-${methodClass} badge-lg me-3">${methodData.method}</span>
                                <div>
                                    <div class="text-gray-900 fw-bold">${methodData.total_queries} queries</div>
                                    <div class="text-muted fs-7">${Math.round(methodData.total_time)}ms total</div>
                                </div>
                            </div>
                            <i class="ki-duotone ki-code fs-2x text-${methodClass}">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
        `;
        
        Array.from(methodData.operations.entries()).forEach(([sqlType, operationData]) => {
            const typeClass = getTypeClass(sqlType);
            const tables = Array.from(operationData.tables).slice(0, 3).join(', ');
            const routes = Array.from(operationData.routes).slice(0, 2);
            
            html += `
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light-${typeClass} rounded">
                        <span class="badge badge-${typeClass} me-3">${sqlType}</span>
                        <div class="flex-grow-1">
                            <div class="fw-bold">${operationData.count}x</div>
                            <div class="text-muted fs-8">${Math.round(operationData.time)}ms</div>
                            ${tables ? `<div class="text-muted fs-8 mt-1">📊 ${tables}</div>` : ''}
                            ${routes.length > 0 ? `<div class="text-primary fs-8 mt-1">🌐 ${routes.join(', ')}</div>` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += `
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
    
    // Update counter
    document.getElementById('methods-count').textContent = httpMethodMap.size;
}

function getHttpMethodClass(method) {
    switch(method) {
        case 'GET': return 'primary';
        case 'POST': return 'success';
        case 'PUT': 
        case 'PATCH': return 'warning';
        case 'DELETE': return 'danger';
        case 'OPTIONS': return 'info';
        case 'CLI': return 'dark';
        default: return 'secondary';
    }
}

function updateStatsDisplay(stats) {
    const container = document.getElementById('stats-container');
    const tables = stats.by_table || {};
    const types = stats.by_type || {};
    
    let html = `
        <div class="row g-5">
            <div class="col-md-6">
                <h5>📊 Por Tipo de Query</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Quantidade</th>
                                <th>Tempo Total</th>
                                <th>Tempo Médio</th>
                            </tr>
                        </thead>
                        <tbody>
    `;
    
    Object.entries(types).forEach(([type, data]) => {
        const avgTime = data.count > 0 ? Math.round(data.time / data.count) : 0;
        html += `
            <tr>
                <td><span class="badge badge-${getTypeClass(type)}">${type}</span></td>
                <td>${data.count}</td>
                <td>${Math.round(data.time)}ms</td>
                <td>${avgTime}ms</td>
            </tr>
        `;
    });
    
    html += `
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <h5>🎯 Por Tabela</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tabela</th>
                                <th>Quantidade</th>
                                <th>Tempo Total</th>
                            </tr>
                        </thead>
                        <tbody>
    `;
    
    Object.entries(tables).slice(0, 10).forEach(([table, data]) => {
        html += `
            <tr>
                <td><code>${table}</code></td>
                <td>${data.count}</td>
                <td>${Math.round(data.time)}ms</td>
            </tr>
        `;
    });
    
    html += `
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
}

function getPerformanceClass(performance) {
    switch(performance) {
        case 'excellent': return 'success';
        case 'good': return 'primary';
        case 'average': return 'warning';
        case 'slow': return 'danger';
        case 'very_slow': return 'dark';
        default: return 'secondary';
    }
}

function getTypeClass(type) {
    switch(type) {
        case 'SELECT': return 'primary';
        case 'INSERT': return 'success';
        case 'UPDATE': return 'warning';
        case 'DELETE': return 'danger';
        case 'CREATE': return 'info';
        case 'TRANSACTION': return 'secondary';
        default: return 'light';
    }
}

function updateButtonStates() {
    const exportButton = document.getElementById('export-data');
    const clearButton = document.getElementById('clear-data');
    
    // Se o monitoramento está ativo, sempre habilitar os botões
    if (monitoringActive) {
        exportButton.disabled = false;
        clearButton.disabled = false;
        return;
    }
    
    // Se não está ativo, verificar se há dados no servidor para sincronizar estado
    fetch('{{ route("debug.database.queries") }}')
        .then(response => response.json())
        .then(data => {
            const hasServerData = data.success && data.queries && data.queries.length > 0;
            
            // Atualizar variável local com estado real do servidor
            hasData = hasServerData;
            
            // Export button: enabled if there's data available
            exportButton.disabled = !hasServerData;
            
            // Clear button: enabled if there's data available
            clearButton.disabled = !hasServerData;
        })
        .catch(error => {
            console.error('Error checking server data state:', error);
            // Em caso de erro, usar estado local como fallback
            exportButton.disabled = !hasData;
            clearButton.disabled = !hasData;
        });
}

function clearQueryResults() {
    document.getElementById('total-queries').textContent = '0';
    document.getElementById('avg-time').textContent = '0ms';
    document.getElementById('slow-queries').textContent = '0';
    document.getElementById('main-table').textContent = '-';
    document.getElementById('main-table-count').textContent = 'Tabela mais utilizada';
    document.getElementById('queries-count').textContent = '0';
    document.getElementById('methods-count').textContent = '0';
    
    // Update data availability
    hasData = false;
    updateButtonStates();
}

function updateTimestamp() {
    const now = new Date();
    document.getElementById('last-update').textContent = now.toLocaleDateString('pt-BR') + ' ' + now.toLocaleTimeString('pt-BR');
}

function formatTimestamp(timestamp) {
    return new Date(timestamp).toLocaleTimeString('pt-BR');
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

function showSuccess(message) {
    Swal.fire({
        icon: 'success',
        title: 'Sucesso!',
        text: message,
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}

function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Erro',
        text: message,
        timer: 5000,
        timerProgressBar: true,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        toast: true,
        position: 'top-end'
    });
}

function exportData() {
    if (!monitoringActive && !hasData) {
        showError('Não há dados disponíveis para exportar');
        return;
    }
    
    const exportButton = document.getElementById('export-data');
    const originalContent = exportButton.innerHTML;
    
    exportButton.disabled = true;
    exportButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Exportando...';
    
    // Get current data
    fetch('{{ route("debug.database.queries") }}')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const exportData = {
                    session_id: data.session_id || 'unknown',
                    session_active: data.session_active || false,
                    exported_at: new Date().toISOString(),
                    statistics: data.statistics || {},
                    queries: data.queries || [],
                    summary: {
                        total_queries: (data.statistics && data.statistics.total_queries) || 0,
                        total_time: Math.round((data.statistics && data.statistics.total_time) || 0),
                        average_time: Math.round((data.statistics && data.statistics.average_time) || 0),
                        slow_queries: ((data.statistics && data.statistics.slow_queries) || 0) + ((data.statistics && data.statistics.very_slow_queries) || 0)
                    },
                    message: data.message || 'Data exported'
                };
                
                // Check if there's actually data to export
                if (exportData.queries.length === 0) {
                    showError('Não há dados disponíveis para exportar');
                    return;
                }
                
                // Generate and download JSON file
                const blob = new Blob([JSON.stringify(exportData, null, 2)], { type: 'application/json' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `database_monitoring_${exportData.session_id}_${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.json`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                const statusMsg = exportData.session_active ? 'sessão ativa' : 'dados em cache';
                showSuccess(`Dados exportados com sucesso! ${exportData.queries.length} queries exportadas (${statusMsg}).`);
            } else {
                const errorMsg = data.message || data.error || 'Erro desconhecido ao obter dados';
                showError(`Erro ao obter dados para exportação: ${errorMsg}`);
            }
        })
        .catch(error => {
            console.error('Erro na exportação:', error);
            showError('Erro ao exportar dados');
        })
        .finally(() => {
            exportButton.disabled = false;
            exportButton.innerHTML = originalContent;
        });
}

function clearData() {
    // Primeiro vamos verificar se realmente há dados para limpar
    // consultando o backend ao invés de confiar apenas na variável local
    fetch('{{ route("debug.database.queries") }}')
        .then(response => response.json())
        .then(data => {
            const hasServerData = data.success && data.queries && data.queries.length > 0;
            
            if (!monitoringActive && !hasServerData) {
                Swal.fire({
                    icon: 'info',
                    title: 'Nenhum dado disponível',
                    text: 'Não há dados de monitoramento para limpar.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }
            
            // Confirmar limpeza com SweetAlert
            Swal.fire({
                icon: 'warning',
                title: 'Confirmar limpeza',
                text: 'Tem certeza que deseja limpar todos os dados de monitoramento? Esta ação não pode ser desfeita.',
                showCancelButton: true,
                confirmButtonText: 'Sim, limpar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    performClearData();
                }
            });
        })
        .catch(error => {
            console.error('Error checking data availability:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Erro ao verificar disponibilidade de dados.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33'
            });
        });
}

function performClearData() {
    const clearButton = document.getElementById('clear-data');
    const originalContent = clearButton.innerHTML;
    
    clearButton.disabled = true;
    clearButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Limpando...';
    
    // Clear cache data
    fetch('{{ route("debug.clear-cache") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            session_id: sessionId,
            clear_queries: true
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Clear UI
            clearQueryResults();
            
            // Reset containers
            document.getElementById('queries-container').innerHTML = `
                <div class="text-center text-muted py-10">
                    <i class="ki-duotone ki-database fs-4x mb-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <h3>Dados limpos - aguardando novas queries</h3>
                    <p>Execute algumas ações no sistema para ver as queries SQL</p>
                </div>
            `;
            
            document.getElementById('methods-container').innerHTML = `
                <div class="text-center text-muted py-10">
                    <i class="ki-duotone ki-router fs-4x mb-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <h3>Dados limpos</h3>
                    <p>Execute ações no sistema para ver o novo mapeamento HTTP → SQL</p>
                </div>
            `;
            
            document.getElementById('stats-container').innerHTML = `
                <div class="text-center text-muted py-10">
                    <i class="ki-duotone ki-chart-pie-simple fs-4x mb-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <h3>Dados limpos</h3>
                    <p>Estatísticas serão exibidas conforme novas queries forem capturadas</p>
                </div>
            `;
            
            const statusMsg = data.session_active ? 'sessão ativa' : 'cache limpo';
            showSuccess(`Dados de monitoramento limpos com sucesso! (${statusMsg})`);
        } else {
            const errorMsg = data.message || 'Erro desconhecido ao limpar dados';
            showError(`Erro ao limpar dados: ${errorMsg}`);
        }
    })
    .catch(error => {
        console.error('Erro ao limpar dados:', error);
        showError('Erro ao limpar dados de monitoramento');
    })
    .finally(() => {
        clearButton.disabled = false;
        clearButton.innerHTML = originalContent;
    });
}
</script>
@endpush
@endsection