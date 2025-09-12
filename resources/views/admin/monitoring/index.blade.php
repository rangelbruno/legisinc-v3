@extends('components.layouts.app')

@section('title', 'Dashboard de Observabilidade')

@section('content')

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

            <!-- Widgets de Status Rápido -->
            <div class="row g-5 g-xl-8">
                
                <!-- Status Geral do Sistema -->
                <div class="col-xl-3">
                    <div class="card card-xl-stretch mb-xl-8">
                        <div class="card-header border-0 bg-success py-5">
                            <h3 class="card-title fw-bold text-white">🟢 Sistema</h3>
                        </div>
                        <div class="card-body py-3">
                            <div class="text-gray-900 fw-bold fs-6 mb-1">Status: Operacional</div>
                            <div class="fw-semibold text-muted fs-7" id="system-uptime">Carregando...</div>
                        </div>
                    </div>
                </div>

                <!-- Database Status -->
                <div class="col-xl-3">
                    <div class="card card-xl-stretch mb-xl-8">
                        <div class="card-header border-0 bg-info py-5">
                            <h3 class="card-title fw-bold text-white">🗃️ PostgreSQL</h3>
                        </div>
                        <div class="card-body py-3" id="database-widget">
                            <div class="fw-semibold text-muted fs-7">Carregando dados...</div>
                        </div>
                    </div>
                </div>

                <!-- Performance -->
                <div class="col-xl-3">
                    <div class="card card-xl-stretch mb-xl-8">
                        <div class="card-header border-0 bg-warning py-5">
                            <h3 class="card-title fw-bold text-white">⚡ Performance</h3>
                        </div>
                        <div class="card-body py-3" id="performance-widget">
                            <div class="fw-semibold text-muted fs-7">Carregando métricas...</div>
                        </div>
                    </div>
                </div>

                <!-- Alertas -->
                <div class="col-xl-3">
                    <div class="card card-xl-stretch mb-xl-8">
                        <div class="card-header border-0 bg-danger py-5">
                            <h3 class="card-title fw-bold text-white">🚨 Alertas</h3>
                        </div>
                        <div class="card-body py-3" id="alerts-widget">
                            <div class="text-success fw-bold fs-6">✅ Nenhum alerta ativo</div>
                            <div class="fw-semibold text-muted fs-7">Sistema funcionando normalmente</div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Row-->

            <!-- Seção de Navegação Rápida -->
            <div class="row g-5 g-xl-8 mt-5">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">🔗 Acesso Rápido às Funcionalidades</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 text-center mb-4">
                                    <a href="{{ route('admin.monitoring.database') }}" class="btn btn-light-info btn-block">
                                        <i class="ki-duotone ki-database fs-2x">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i><br>
                                        <small class="fw-bold mt-2">Banco de Dados</small>
                                    </a>
                                </div>
                                <div class="col-md-2 text-center mb-4">
                                    <a href="{{ route('admin.monitoring.performance') }}" class="btn btn-light-warning btn-block">
                                        <i class="ki-duotone ki-chart-line-up fs-2x">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i><br>
                                        <small class="fw-bold mt-2">Performance</small>
                                    </a>
                                </div>
                                <div class="col-md-2 text-center mb-4">
                                    <a href="{{ route('admin.monitoring.logs') }}" class="btn btn-light-dark btn-block">
                                        <i class="ki-duotone ki-file-search fs-2x">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i><br>
                                        <small class="fw-bold mt-2">Logs</small>
                                    </a>
                                </div>
                                <div class="col-md-2 text-center mb-4">
                                    <a href="{{ route('admin.monitoring.alerts') }}" class="btn btn-light-danger btn-block">
                                        <i class="ki-duotone ki-notification-bing fs-2x">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i><br>
                                        <small class="fw-bold mt-2">Alertas</small>
                                    </a>
                                </div>
                                <div class="col-md-2 text-center mb-4">
                                    <a href="{{ route('monitoring.health') }}" target="_blank" class="btn btn-light-success btn-block">
                                        <i class="ki-duotone ki-heart fs-2x">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i><br>
                                        <small class="fw-bold mt-2">Health Check</small>
                                    </a>
                                </div>
                                <div class="col-md-2 text-center mb-4">
                                    <button onclick="connectSSE()" class="btn btn-light-primary btn-block">
                                        <i class="ki-duotone ki-arrows-circle fs-2x">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i><br>
                                        <small class="fw-bold mt-2">Tempo Real</small>
                                    </button>
                                </div>
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
    
    widget.innerHTML = `
        <div class="text-gray-900 fw-bold fs-6 mb-1">Conexões: ${connections.active || 0}/${connections.max || 100}</div>
        <div class="fw-semibold text-muted fs-7">Uso: ${connections.usage_percent || 0}%</div>
    `;
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
        perfWidget.innerHTML = `
            <div class="text-gray-900 fw-bold fs-6 mb-1">Req/s: ${data.performance.total_requests || 0}</div>
            <div class="fw-semibold text-muted fs-7">Latência: ${data.performance.avg_response_time_ms || 0}ms</div>
        `;
    }
    
    // Atualizar sistema
    document.getElementById('system-uptime').textContent = `Buffer Redis: ${data.buffer_size || 0} items`;
}
</script>
@endpush
@endsection