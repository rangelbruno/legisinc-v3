@extends('components.layouts.app')

@section('title', 'Configurações do Sistema - Debug Avançado')

@push('styles')
<style>
    .nav-tabs .nav-link {
        color: #7239ea;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        background-color: #f1effc;
        border-color: #7239ea #7239ea #f1effc;
        color: #7239ea;
    }
    .query-item {
        border-left: 4px solid #e1e3ea;
        transition: all 0.3s ease;
    }
    .query-item.excellent { border-left-color: #50cd89; }
    .query-item.good { border-left-color: #7239ea; }
    .query-item.average { border-left-color: #ffc700; }
    .query-item.slow { border-left-color: #f1416c; }
    .query-item.very_slow { border-left-color: #181c32; }
    .query-item:hover {
        box-shadow: 0 0 20px rgba(114, 57, 234, 0.1);
        transform: translateX(5px);
    }
    .sql-code {
        background: #1e1e2e;
        color: #c9f7f5;
        padding: 15px;
        border-radius: 8px;
        font-family: 'Monaco', 'Consolas', monospace;
        font-size: 12px;
        overflow-x: auto;
    }
    .sql-keyword { color: #f1416c; font-weight: bold; }
    .sql-table { color: #50cd89; }
    .sql-string { color: #ffc700; }
    .db-stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .capture-indicator {
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { opacity: 0.6; }
        50% { opacity: 1; }
        100% { opacity: 0.6; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between py-5">
        <div class="d-flex align-items-center">
            <i class="ki-duotone ki-setting-2 fs-1 text-primary me-3">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            <div>
                <h1 class="mb-0">Configurações do Sistema - Debug Avançado</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Configurações do Sistema
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-light-success" id="exportDebugData">
                <i class="ki-duotone ki-exit-down fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Exportar Debug
            </button>
            <button type="button" class="btn btn-light-warning" onclick="clearCache()">
                <i class="ki-duotone ki-broom fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                </i>
                Limpar Cache
            </button>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#general-tab">
                <i class="ki-duotone ki-gear fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Configurações Gerais
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#database-tab">
                <i class="ki-duotone ki-data fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Debug de Banco de Dados
                <span class="badge badge-danger ms-2 d-none" id="queryCountBadge">0</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#performance-tab">
                <i class="ki-duotone ki-chart-line-up fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Performance & Estatísticas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#realtime-tab">
                <i class="ki-duotone ki-time fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Monitoramento em Tempo Real
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="systemConfigTabContent">
        <!-- General Settings Tab -->
        <div class="tab-pane fade show active" id="general-tab">
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-gear fs-1 text-primary me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <h3 class="fw-bold m-0">Configurações Gerais</h3>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.system-configuration.update') }}" method="POST" id="systemConfigForm">
                    @csrf
                    <div class="card-body py-4">
                        <!-- Debug Logger Configuration -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-light-primary">
                                    <div class="card-body p-9">
                                        <div class="d-flex align-items-center mb-5">
                                            <i class="ki-duotone ki-code fs-2x text-primary me-4">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                            </i>
                                            <div class="flex-grow-1">
                                                <h4 class="text-gray-900 mb-1">🔧 Debug Logger</h4>
                                                <div class="text-gray-700 fw-semibold fs-6">
                                                    @if($debugLogger['exists'])
                                                        {{ $debugLogger['description'] }}
                                                    @else
                                                        Sistema de debug para capturar ações do usuário em tempo real
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                @if($debugLogger['exists'])
                                                    <span class="badge badge-{{ $debugLogger['active'] ? 'success' : 'secondary' }} fs-8 fw-bold me-3">
                                                        {{ $debugLogger['active'] ? 'ATIVO' : 'INATIVO' }}
                                                    </span>
                                                @endif
                                                
                                                <div class="form-check form-switch form-check-custom form-check-success form-check-solid">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="debug_logger_ativo" 
                                                           id="debug_logger_ativo" 
                                                           {{ $debugLogger['active'] ? 'checked' : '' }}
                                                           @if(!$debugLogger['exists']) disabled @endif>
                                                    <label class="form-check-label fw-semibold text-gray-400 ms-3" for="debug_logger_ativo">
                                                        @if($debugLogger['exists'])
                                                            {{ $debugLogger['active'] ? 'Desativar' : 'Ativar' }} Debug Logger
                                                        @else
                                                            Debug Logger não configurado
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        @if($debugLogger['exists'])
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="ki-duotone ki-check fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Salvar Configurações
                        </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Database Debug Tab -->
        <div class="tab-pane fade" id="database-tab">
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-data fs-1 text-primary me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <h3 class="fw-bold m-0">Debug de Banco de Dados</h3>
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success" id="startCaptureBtn" onclick="startDatabaseCapture()">
                                <i class="ki-duotone ki-play fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Iniciar Captura
                            </button>
                            <button type="button" class="btn btn-danger d-none" id="stopCaptureBtn" onclick="stopDatabaseCapture()">
                                <i class="ki-duotone ki-stop fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Parar Captura
                            </button>
                            <button type="button" class="btn btn-light-primary" onclick="refreshQueries()">
                                <i class="ki-duotone ki-refresh fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Atualizar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body py-4">
                    <!-- Capture Status -->
                    <div class="alert alert-info d-none" id="captureStatus">
                        <div class="d-flex align-items-center">
                            <div class="spinner-border spinner-border-sm text-info me-3 capture-indicator" role="status"></div>
                            <div>
                                <h4 class="alert-heading mb-1">Capturando Queries...</h4>
                                <p class="mb-0">As queries SQL estão sendo capturadas em tempo real. Execute ações no sistema para vê-las aparecer aqui.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Query Statistics -->
                    <div class="row mb-5" id="queryStats" style="display: none;">
                        <div class="col-md-3">
                            <div class="card bg-light-success">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <span class="svg-icon svg-icon-2x svg-icon-success me-3">
                                            <i class="ki-duotone ki-abstract-26 fs-2x text-success">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                        <div>
                                            <div class="text-gray-700">Total Queries</div>
                                            <div class="fs-2 fw-bold" id="totalQueries">0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light-primary">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <span class="svg-icon svg-icon-2x svg-icon-primary me-3">
                                            <i class="ki-duotone ki-timer fs-2x text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                        <div>
                                            <div class="text-gray-700">Tempo Total</div>
                                            <div class="fs-2 fw-bold" id="totalTime">0ms</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light-warning">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <span class="svg-icon svg-icon-2x svg-icon-warning me-3">
                                            <i class="ki-duotone ki-chart-line-down fs-2x text-warning">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                        <div>
                                            <div class="text-gray-700">Queries Lentas</div>
                                            <div class="fs-2 fw-bold" id="slowQueries">0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light-info">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <span class="svg-icon svg-icon-2x svg-icon-info me-3">
                                            <i class="ki-duotone ki-speed fs-2x text-info">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                        <div>
                                            <div class="text-gray-700">Tempo Médio</div>
                                            <div class="fs-2 fw-bold" id="avgTime">0ms</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Query Filter -->
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <div class="d-flex align-items-center">
                            <label class="form-label fw-bold me-3 mb-0">Filtrar por tipo:</label>
                            <select class="form-select form-select-sm w-150px" id="queryTypeFilter">
                                <option value="">Todos</option>
                                <option value="SELECT">SELECT</option>
                                <option value="INSERT">INSERT</option>
                                <option value="UPDATE">UPDATE</option>
                                <option value="DELETE">DELETE</option>
                                <option value="TRANSACTION">TRANSACTION</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center">
                            <label class="form-label fw-bold me-3 mb-0">Ordenar por:</label>
                            <select class="form-select form-select-sm w-150px" id="querySortBy">
                                <option value="time_desc">Tempo (Maior → Menor)</option>
                                <option value="time_asc">Tempo (Menor → Maior)</option>
                                <option value="newest">Mais Recente</option>
                                <option value="oldest">Mais Antigo</option>
                            </select>
                        </div>
                    </div>

                    <!-- Queries List -->
                    <div id="queriesList">
                        <div class="text-center py-10">
                            <i class="ki-duotone ki-data fs-5x text-muted">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <h4 class="text-muted mt-5">Nenhuma query capturada</h4>
                            <p class="text-muted">Clique em "Iniciar Captura" e execute ações no sistema para ver as queries SQL.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Tab -->
        <div class="tab-pane fade" id="performance-tab">
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-chart-line-up fs-1 text-primary me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <h3 class="fw-bold m-0">Performance & Estatísticas do Banco</h3>
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-light-primary" onclick="loadDatabaseStats()">
                            <i class="ki-duotone ki-refresh fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Atualizar Estatísticas
                        </button>
                    </div>
                </div>

                <div class="card-body py-4">
                    <!-- Database Overview -->
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <div class="db-stat-card">
                                <h4 class="text-white mb-3">📊 Informações do Banco</h4>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <div class="text-white-50 fs-7">Tamanho do Banco</div>
                                            <div class="fs-3 fw-bold" id="dbSize">-</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <div class="text-white-50 fs-7">Taxa de Cache Hit</div>
                                            <div class="fs-3 fw-bold" id="cacheHitRatio">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="db-stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <h4 class="text-white mb-3">🔗 Conexões Ativas</h4>
                                <div id="activeConnections">
                                    <div class="text-center py-5">
                                        <div class="spinner-border text-white" role="status"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tables Information -->
                    <h4 class="mb-4">📋 Informações das Tabelas</h4>
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablesInfo">
                            <thead>
                                <tr class="fw-bold fs-6 text-gray-800">
                                    <th>Tabela</th>
                                    <th>Tamanho</th>
                                    <th>Colunas</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <span class="text-muted">Clique em "Atualizar Estatísticas" para carregar</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-time Monitoring Tab -->
        <div class="tab-pane fade" id="realtime-tab">
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-time fs-1 text-primary me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <h3 class="fw-bold m-0">Monitoramento em Tempo Real</h3>
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" id="realtimeToggle">
                            <label class="form-check-label fw-semibold" for="realtimeToggle">
                                Auto-atualização
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card-body py-4">
                    <!-- Real-time Query Stream -->
                    <div class="alert alert-light-primary d-flex align-items-center p-5 mb-5">
                        <i class="ki-duotone ki-shield-tick fs-2hx text-primary me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-primary">Monitoramento em Tempo Real</h4>
                            <span>Visualize as queries sendo executadas no sistema em tempo real. Útil para debug e otimização de performance.</span>
                        </div>
                    </div>

                    <!-- Live Query Feed -->
                    <div id="liveQueryFeed" style="max-height: 600px; overflow-y: auto;">
                        <!-- Queries will be added here dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Query Detail Modal -->
<div class="modal fade" id="queryDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes da Query</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="queryDetailContent">
                    <!-- Query details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let captureActive = false;
let realtimeInterval = null;
let allQueries = [];

// Start database capture
async function startDatabaseCapture() {
    try {
        const response = await fetch('{{ route("admin.system-configuration.database.start-capture") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            captureActive = true;
            document.getElementById('startCaptureBtn').classList.add('d-none');
            document.getElementById('stopCaptureBtn').classList.remove('d-none');
            document.getElementById('captureStatus').classList.remove('d-none');
            showToast('Captura de queries iniciada', 'success');
            
            // Start auto-refresh
            setInterval(refreshQueries, 2000);
        }
    } catch (error) {
        showToast('Erro ao iniciar captura: ' + error.message, 'error');
    }
}

// Stop database capture
async function stopDatabaseCapture() {
    try {
        const response = await fetch('{{ route("admin.system-configuration.database.stop-capture") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            captureActive = false;
            document.getElementById('startCaptureBtn').classList.remove('d-none');
            document.getElementById('stopCaptureBtn').classList.add('d-none');
            document.getElementById('captureStatus').classList.add('d-none');
            showToast('Captura de queries parada', 'info');
        }
    } catch (error) {
        showToast('Erro ao parar captura: ' + error.message, 'error');
    }
}

// Refresh queries list
async function refreshQueries() {
    try {
        const response = await fetch('{{ route("admin.system-configuration.database.queries") }}');
        const data = await response.json();
        
        if (data.success) {
            allQueries = data.queries;
            updateQueryStats(data.statistics);
            renderQueries(data.queries);
            
            // Update badge
            const badge = document.getElementById('queryCountBadge');
            if (data.queries.length > 0) {
                badge.textContent = data.queries.length;
                badge.classList.remove('d-none');
            }
        }
    } catch (error) {
        console.error('Error refreshing queries:', error);
    }
}

// Update query statistics
function updateQueryStats(stats) {
    if (!stats) return;
    
    document.getElementById('queryStats').style.display = 'flex';
    document.getElementById('totalQueries').textContent = stats.total_queries || 0;
    document.getElementById('totalTime').textContent = (stats.total_time || 0).toFixed(2) + 'ms';
    document.getElementById('slowQueries').textContent = (stats.slow_queries || 0) + (stats.very_slow_queries || 0);
    document.getElementById('avgTime').textContent = (stats.average_time || 0).toFixed(2) + 'ms';
}

// Render queries list
function renderQueries(queries) {
    const container = document.getElementById('queriesList');
    
    if (!queries || queries.length === 0) {
        container.innerHTML = `
            <div class="text-center py-10">
                <i class="ki-duotone ki-data fs-5x text-muted">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <h4 class="text-muted mt-5">Nenhuma query capturada</h4>
                <p class="text-muted">Clique em "Iniciar Captura" e execute ações no sistema para ver as queries SQL.</p>
            </div>
        `;
        return;
    }
    
    // Apply filters and sorting
    let filteredQueries = filterAndSortQueries(queries);
    
    let html = '';
    filteredQueries.forEach((query, index) => {
        html += renderQueryItem(query, index);
    });
    
    container.innerHTML = html;
}

// Render single query item
function renderQueryItem(query, index) {
    const performanceClass = query.performance || 'excellent';
    const typeColor = getTypeColor(query.type);
    
    return `
        <div class="card query-item ${performanceClass} mb-3" onclick="showQueryDetail(${index})">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge badge-light-${typeColor} me-2">${query.type}</span>
                        ${query.tables.map(t => `<span class="badge badge-light-info me-1">${t}</span>`).join('')}
                    </div>
                    <div class="text-end">
                        <span class="badge badge-${getPerformanceBadge(performanceClass)}">${query.time_formatted}</span>
                    </div>
                </div>
                <div class="sql-preview text-muted fs-7" style="font-family: monospace;">
                    ${truncateSQL(query.sql, 150)}
                </div>
                <div class="mt-2 text-muted fs-8">
                    <i class="ki-duotone ki-time fs-7">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    ${new Date(query.timestamp).toLocaleTimeString()}
                </div>
            </div>
        </div>
    `;
}

// Filter and sort queries
function filterAndSortQueries(queries) {
    let filtered = [...queries];
    
    // Apply type filter
    const typeFilter = document.getElementById('queryTypeFilter').value;
    if (typeFilter) {
        filtered = filtered.filter(q => q.type === typeFilter);
    }
    
    // Apply sorting
    const sortBy = document.getElementById('querySortBy').value;
    switch(sortBy) {
        case 'time_desc':
            filtered.sort((a, b) => b.time - a.time);
            break;
        case 'time_asc':
            filtered.sort((a, b) => a.time - b.time);
            break;
        case 'newest':
            filtered.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
            break;
        case 'oldest':
            filtered.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));
            break;
    }
    
    return filtered;
}

// Show query detail modal
function showQueryDetail(index) {
    const query = allQueries[index];
    if (!query) return;
    
    const modal = new bootstrap.Modal(document.getElementById('queryDetailModal'));
    const content = document.getElementById('queryDetailContent');
    
    content.innerHTML = `
        <div class="mb-4">
            <h6 class="text-muted mb-2">SQL Query</h6>
            <div class="sql-code">${formatSQL(query.formatted_sql || query.sql)}</div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="text-muted mb-2">Performance</h6>
                <div class="d-flex align-items-center">
                    <span class="badge badge-${getPerformanceBadge(query.performance)} me-2">${query.performance}</span>
                    <span class="text-dark fw-bold">${query.time_formatted}</span>
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-2">Tipo</h6>
                <span class="badge badge-light-${getTypeColor(query.type)}">${query.type}</span>
            </div>
        </div>
        
        ${query.bindings && query.bindings.length > 0 ? `
            <div class="mb-4">
                <h6 class="text-muted mb-2">Bindings</h6>
                <code>${JSON.stringify(query.bindings, null, 2)}</code>
            </div>
        ` : ''}
        
        ${query.backtrace && query.backtrace.length > 0 ? `
            <div class="mb-4">
                <h6 class="text-muted mb-2">Call Stack</h6>
                <ul class="list-unstyled">
                    ${query.backtrace.map(trace => `
                        <li class="mb-2">
                            <code>${trace.file}:${trace.line}</code>
                            ${trace.class ? `<br><small class="text-muted">${trace.class}::${trace.function}()</small>` : ''}
                        </li>
                    `).join('')}
                </ul>
            </div>
        ` : ''}
    `;
    
    modal.show();
}

// Load database statistics
async function loadDatabaseStats() {
    try {
        const response = await fetch('{{ route("admin.system-configuration.database.stats") }}');
        const data = await response.json();
        
        if (data.success && data.stats) {
            // Update database info
            document.getElementById('dbSize').textContent = data.stats.database_size || '-';
            document.getElementById('cacheHitRatio').textContent = (data.stats.cache_hit_ratio || 0) + '%';
            
            // Update connections
            if (data.stats.connections) {
                const connectionsHtml = data.stats.connections.slice(0, 3).map(conn => `
                    <div class="mb-2">
                        <div class="text-white fw-bold">${conn.usename || 'Unknown'}</div>
                        <div class="text-white-50 fs-7">${conn.state || 'idle'} - ${conn.application_name || 'N/A'}</div>
                    </div>
                `).join('');
                
                document.getElementById('activeConnections').innerHTML = connectionsHtml || '<span class="text-white-50">Nenhuma conexão ativa</span>';
            }
            
            // Update tables info
            if (data.stats.tables) {
                const tbody = document.querySelector('#tablesInfo tbody');
                tbody.innerHTML = data.stats.tables.map(table => `
                    <tr>
                        <td class="fw-bold">${table.table_name}</td>
                        <td>${table.size}</td>
                        <td>${table.columns_count}</td>
                        <td>
                            <button class="btn btn-sm btn-light-primary" onclick="analyzeTable('${table.table_name}')">
                                <i class="ki-duotone ki-magnifier fs-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Analisar
                            </button>
                        </td>
                    </tr>
                `).join('');
            }
            
            showToast('Estatísticas atualizadas', 'success');
        }
    } catch (error) {
        showToast('Erro ao carregar estatísticas: ' + error.message, 'error');
    }
}

// Helper functions
function getTypeColor(type) {
    const colors = {
        'SELECT': 'primary',
        'INSERT': 'success',
        'UPDATE': 'warning',
        'DELETE': 'danger',
        'TRANSACTION': 'info',
        'CREATE': 'dark',
        'DROP': 'danger',
        'ALTER': 'warning'
    };
    return colors[type] || 'secondary';
}

function getPerformanceBadge(performance) {
    const badges = {
        'excellent': 'success',
        'good': 'primary',
        'average': 'warning',
        'slow': 'danger',
        'very_slow': 'dark'
    };
    return badges[performance] || 'secondary';
}

function truncateSQL(sql, maxLength) {
    if (sql.length <= maxLength) return sql;
    return sql.substring(0, maxLength) + '...';
}

function formatSQL(sql) {
    // Basic SQL syntax highlighting
    return sql
        .replace(/\b(SELECT|FROM|WHERE|JOIN|LEFT|RIGHT|INNER|ORDER BY|GROUP BY|HAVING|LIMIT|INSERT|INTO|VALUES|UPDATE|SET|DELETE|CREATE|ALTER|DROP|TABLE|INDEX|AND|OR|NOT|IN|EXISTS|BETWEEN|LIKE|AS)\b/gi, 
            '<span class="sql-keyword">$1</span>')
        .replace(/`(\w+)`/g, '<span class="sql-table">`$1`</span>')
        .replace(/'([^']*)'/g, '<span class="sql-string">\'$1\'</span>');
}

// Export debug data
document.getElementById('exportDebugData').addEventListener('click', function() {
    const debugData = {
        queries: allQueries,
        statistics: {
            total: document.getElementById('totalQueries').textContent,
            time: document.getElementById('totalTime').textContent,
            slow: document.getElementById('slowQueries').textContent,
            average: document.getElementById('avgTime').textContent
        },
        timestamp: new Date().toISOString()
    };
    
    const blob = new Blob([JSON.stringify(debugData, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `debug-queries-${Date.now()}.json`;
    a.click();
    URL.revokeObjectURL(url);
    
    showToast('Debug data exportado', 'success');
});

// Real-time monitoring toggle
document.getElementById('realtimeToggle').addEventListener('change', function() {
    if (this.checked) {
        startRealtimeMonitoring();
    } else {
        stopRealtimeMonitoring();
    }
});

function startRealtimeMonitoring() {
    if (!captureActive) {
        showToast('Inicie a captura de queries primeiro', 'warning');
        document.getElementById('realtimeToggle').checked = false;
        return;
    }
    
    realtimeInterval = setInterval(async () => {
        const response = await fetch('{{ route("admin.system-configuration.database.queries") }}');
        const data = await response.json();
        
        if (data.success && data.queries.length > 0) {
            const feed = document.getElementById('liveQueryFeed');
            const newQuery = data.queries[data.queries.length - 1];
            
            const queryHtml = `
                <div class="alert alert-light-primary mb-2 animate__animated animate__fadeInRight">
                    <div class="d-flex justify-content-between">
                        <span class="badge badge-${getTypeColor(newQuery.type)}">${newQuery.type}</span>
                        <span class="text-muted fs-8">${new Date(newQuery.timestamp).toLocaleTimeString()}</span>
                    </div>
                    <div class="mt-2 text-dark" style="font-family: monospace; font-size: 11px;">
                        ${truncateSQL(newQuery.sql, 100)}
                    </div>
                    <div class="mt-1">
                        <span class="badge badge-${getPerformanceBadge(newQuery.performance)}">${newQuery.time_formatted}</span>
                    </div>
                </div>
            `;
            
            feed.insertAdjacentHTML('afterbegin', queryHtml);
            
            // Keep only last 20 queries in feed
            while (feed.children.length > 20) {
                feed.removeChild(feed.lastChild);
            }
        }
    }, 1000);
}

function stopRealtimeMonitoring() {
    if (realtimeInterval) {
        clearInterval(realtimeInterval);
        realtimeInterval = null;
    }
}

// Event listeners for filters
document.getElementById('queryTypeFilter').addEventListener('change', () => renderQueries(allQueries));
document.getElementById('querySortBy').addEventListener('change', () => renderQueries(allQueries));

// Clear cache function
async function clearCache() {
    if (!confirm('Tem certeza que deseja limpar todo o cache do sistema?')) {
        return;
    }
    
    try {
        const response = await fetch('{{ route("admin.system-configuration.clear-cache") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        });
        
        const data = await response.json();
        showToast(data.message, data.success ? 'success' : 'error');
    } catch (error) {
        showToast('Erro ao limpar cache: ' + error.message, 'error');
    }
}

// Show toast function
function showToast(message, type = 'success') {
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toastr-top-right",
        "timeOut": "5000"
    };
    
    if (type === 'success') {
        toastr.success(message);
    } else if (type === 'error') {
        toastr.error(message);
    } else if (type === 'warning') {
        toastr.warning(message);
    } else {
        toastr.info(message);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load initial data if on performance tab
    if (window.location.hash === '#performance-tab') {
        loadDatabaseStats();
    }
});
</script>
@endpush

@endsection