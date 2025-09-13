@extends('components.layouts.app')

@section('title', 'Dashboard de Observabilidade')

@section('content')

<style>
.dashboard-layer {
    border: 2px solid #e1e5e9;
    border-radius: 0.75rem;
    background: #ffffff;
    margin-bottom: 1.5rem;
    /* Transição removida para design minimalista */
}

.dashboard-layer:hover {
    /* Hover removido para design minimalista */
}

.layer-header {
    background: #f8f9fa;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e1e5e9;
    border-radius: 0.75rem 0.75rem 0 0;
    font-weight: 600;
    font-size: 1.1rem;
    text-align: center;
    color: #3f4254;
}

.layer-content {
    padding: 1.5rem;
}

.component-box {
    border: 1px solid #e1e5e9;
    border-radius: 0.5rem;
    padding: 1rem;
    text-align: center;
    background: #ffffff;
    /* Transição removida para design minimalista */
    cursor: pointer;
    height: 100%;
}

.component-box:hover {
    /* Hover removido para design minimalista */
    border-color: #e1e5e9;
    background: #ffffff;
}

.component-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    display: block;
}

.component-title {
    font-weight: 600;
    color: #3f4254;
    margin-bottom: 0.25rem;
}

.component-subtitle {
    font-size: 0.875rem;
    color: #a1a5b7;
}

.main-title {
    text-align: center;
    font-size: 1.5rem;
    font-weight: 700;
    color: #3f4254;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 0.75rem;
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
                    🔍 Dashboard de Observabilidade
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Administração</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Observabilidade</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div class="text-end d-none d-sm-block">
                    <div class="text-muted fw-semibold fs-7" id="last-update">{{ date('d/m/Y H:i:s') }}</div>
                </div>
                <button type="button" class="btn btn-sm btn-primary" onclick="refreshDashboard()">
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

            <!-- Título Principal -->
            <div class="main-title">
                Dashboard Admin
            </div>

            <!-- Camada de Dashboard Admin -->
            <div class="dashboard-layer">
                <div class="layer-header">
                    Dashboard Admin
                </div>
                <div class="layer-content">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="component-box" onclick="location.href='{{ route('admin.monitoring.performance') }}'">
                                <div class="component-icon">
                                    <i class="ki-duotone ki-chart-simple fs-2x text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </div>
                                <div class="component-title">Métricas</div>
                                <div class="component-subtitle" id="performance-widget">Carregando...</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="component-box" onclick="location.href='{{ route('admin.monitoring.logs') }}'">
                                <div class="component-icon">
                                    <i class="ki-duotone ki-notebook fs-2x text-info">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </div>
                                <div class="component-title">Logs</div>
                                <div class="component-subtitle">Sistema de logs</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="component-box" onclick="location.href='{{ route('admin.monitoring.alerts') }}'">
                                <div class="component-icon">
                                    <i class="ki-duotone ki-notification-status fs-2x text-warning">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </div>
                                <div class="component-title">Alertas</div>
                                <div class="component-subtitle" id="alerts-widget">✅ Nenhum alerta ativo</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Camada de Coleta -->
            <div class="dashboard-layer">
                <div class="layer-header">
                    Camada de Coleta
                </div>
                <div class="layer-content">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="component-box" onclick="connectSSE()">
                                <div class="component-icon">
                                    <i class="ki-duotone ki-arrows-circle fs-2x text-success">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                                <div class="component-title">Collector</div>
                                <div class="component-subtitle">Tempo real</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="component-box">
                                <div class="component-icon">
                                    <i class="ki-duotone ki-package fs-2x text-secondary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </div>
                                <div class="component-title">Queues</div>
                                <div class="component-subtitle" id="system-uptime">Carregando...</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="component-box" onclick="location.href='{{ route('monitoring.health') }}'" target="_blank">
                                <div class="component-icon">
                                    <i class="ki-duotone ki-chart-line-up fs-2x text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                                <div class="component-title">Metrics</div>
                                <div class="component-subtitle">Health check</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Camada de Armazenamento -->
            <div class="dashboard-layer">
                <div class="layer-header">
                    Camada de Armazenamento
                </div>
                <div class="layer-content">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="component-box" onclick="location.href='{{ route('admin.monitoring.database') }}'">
                                <div class="component-icon">
                                    <i class="ki-duotone ki-database fs-2x text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                                <div class="component-title">PostgreSQL</div>
                                <div class="component-subtitle" id="database-widget">Carregando...</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="component-box">
                                <div class="component-icon">
                                    <i class="ki-duotone ki-flash fs-2x text-danger">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                                <div class="component-title">Redis</div>
                                <div class="component-subtitle">Cache em memória</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="component-box" onclick="location.href='{{ route('admin.monitoring.database-activity') }}'">
                                <div class="component-icon">
                                    <i class="ki-duotone ki-chart-line-down fs-2x text-success">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                                <div class="component-title">Atividade DB</div>
                                <div class="component-subtitle" id="db-activity-widget">Carregando...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status de Conexão Real-time -->
            <div class="row g-5 g-xl-8 mt-5" id="realtime-status" style="display: none;">
                <div class="col-12">
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="ki-duotone ki-information fs-2x me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1">📡 Monitoramento em Tempo Real Ativo</h4>
                            <span id="sse-status">Conectando ao stream de dados...</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-light ms-auto" onclick="disconnectSSE()">
                            Desconectar
                        </button>
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
<script>
let eventSource = null;

// Auto-refresh básico a cada 30 segundos
setInterval(refreshBasicData, 30000);

// Carregar dados iniciais
document.addEventListener('DOMContentLoaded', function() {
    refreshBasicData();
});

function refreshDashboard() {
    refreshBasicData();
    updateTimestamp();
}

function refreshBasicData() {
    fetch('{{ route("admin.monitoring.api.database-stats") }}')
        .then(response => response.json())
        .then(data => {
            updateDatabaseWidget(data);
        })
        .catch(error => {
            console.error('Erro ao carregar dados:', error);
        });
}

function updateDatabaseWidget(data) {
    const widget = document.getElementById('database-widget');
    const connections = data.connections || {};
    
    widget.innerHTML = `Conexões: ${connections.active || 0}/${connections.max || 100} (${connections.usage_percent || 0}%)`;
}

function updateTimestamp() {
    const now = new Date();
    document.getElementById('last-update').textContent = now.toLocaleDateString('pt-BR') + ' ' + now.toLocaleTimeString('pt-BR');
}

// Server-Sent Events para tempo real
function connectSSE() {
    if (eventSource) {
        return;
    }
    
    document.getElementById('realtime-status').style.display = 'block';
    document.getElementById('sse-status').textContent = 'Conectando...';
    
    eventSource = new EventSource('{{ route("admin.monitoring.stream") }}');
    
    eventSource.onopen = function(e) {
        document.getElementById('sse-status').textContent = '✅ Conectado - Recebendo dados em tempo real';
    };
    
    eventSource.addEventListener('heartbeat', function(e) {
        const data = JSON.parse(e.data);
        updateRealtimeData(data);
        document.getElementById('sse-status').textContent = `✅ Última atualização: ${new Date().toLocaleTimeString('pt-BR')} (Iteração ${data.iteration})`;
    });
    
    eventSource.onerror = function(e) {
        document.getElementById('sse-status').textContent = '❌ Erro na conexão - Tentando reconectar...';
    };
    
    eventSource.addEventListener('close', function(e) {
        document.getElementById('sse-status').textContent = '🔄 Conexão encerrada pelo servidor';
        disconnectSSE();
    });
}

function disconnectSSE() {
    if (eventSource) {
        eventSource.close();
        eventSource = null;
    }
    document.getElementById('realtime-status').style.display = 'none';
}

function updateRealtimeData(data) {
    // Atualizar widgets com dados em tempo real
    if (data.database) {
        updateDatabaseWidget(data.database);
    }

    if (data.performance) {
        const perfWidget = document.getElementById('performance-widget');
        perfWidget.innerHTML = `Req/s: ${data.performance.total_requests || 0} | ${data.performance.avg_response_time_ms || 0}ms`;
    }

    // Atualizar atividade do banco de dados
    if (data.database_activity) {
        const dbActivityWidget = document.getElementById('db-activity-widget');
        const totalOps = data.database_activity.operations_last_minute || 0;
        const avgTime = data.database_activity.avg_query_time_ms || 0;
        dbActivityWidget.innerHTML = `${totalOps} ops/min | ${avgTime}ms médio`;
    }

    // Atualizar sistema
    document.getElementById('system-uptime').textContent = `Buffer Redis: ${data.buffer_size || 0} items`;
}
</script>
@endpush
@endsection