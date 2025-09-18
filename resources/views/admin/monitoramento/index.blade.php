@extends('components.layouts.app')

@section('title', 'Monitoramento - Grafana & Prometheus')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    📊 Monitoramento - Grafana & Prometheus
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Admin</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Monitoramento</li>
                </ul>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <!-- Tabs -->
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5 active" data-bs-toggle="tab" href="#overview">
                                📈 Overview
                            </a>
                        </li>
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#servicos">
                                🔧 Serviços
                            </a>
                        </li>
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#metricas">
                                📊 Métricas
                            </a>
                        </li>
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#documentacao">
                                📚 Documentação
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="monitoramentoTabs">
                        <!-- Overview Tab -->
                        <div class="tab-pane fade show active" id="overview">
                            <div class="row g-6 g-xl-9 mb-6">
                                <!-- Quick Access Cards -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="card bg-light-primary h-100">
                                        <div class="card-body">
                                            <div class="fw-bold text-primary mb-2">📈 Grafana Dashboard</div>
                                            <div class="text-gray-900 fw-bold fs-6 mb-2">Interface de Visualização</div>
                                            <div class="text-gray-500 fs-7 mb-4">admin / admin</div>
                                            <a href="http://localhost:3000" target="_blank" class="btn btn-sm btn-primary">
                                                Acessar Grafana
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <div class="card bg-light-info h-100">
                                        <div class="card-body">
                                            <div class="fw-bold text-info mb-2">🔍 Prometheus</div>
                                            <div class="text-gray-900 fw-bold fs-6 mb-2">Coleta de Métricas</div>
                                            <div class="text-gray-500 fs-7 mb-4">Query & Targets</div>
                                            <a href="http://localhost:9090" target="_blank" class="btn btn-sm btn-info">
                                                Acessar Prometheus
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <div class="card bg-light-success h-100">
                                        <div class="card-body">
                                            <div class="fw-bold text-success mb-2">🐘 PostgreSQL</div>
                                            <div class="text-gray-900 fw-bold fs-6 mb-2">Database Metrics</div>
                                            <div class="text-gray-500 fs-7 mb-4">Port 9187</div>
                                            <a href="http://localhost:9187/metrics" target="_blank" class="btn btn-sm btn-success">
                                                Ver Métricas
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Summary -->
                            <div class="card bg-light">
                                <div class="card-header border-0">
                                    <h3 class="card-title">✅ Status Atual do Sistema</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="d-flex align-items-center mb-2">
                                                    <span class="bullet bullet-vertical bg-success me-3"></span>
                                                    <strong>Grafana:</strong> Funcionando (localhost:3000)
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <span class="bullet bullet-vertical bg-success me-3"></span>
                                                    <strong>Prometheus:</strong> Funcionando (localhost:9090)
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <span class="bullet bullet-vertical bg-success me-3"></span>
                                                    <strong>Traefik Metrics:</strong> Coletando dados
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="d-flex align-items-center mb-2">
                                                    <span class="bullet bullet-vertical bg-success me-3"></span>
                                                    <strong>PostgreSQL Exporter:</strong> Configurado
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <span class="bullet bullet-vertical bg-success me-3"></span>
                                                    <strong>Métricas LegisInc:</strong> Funcionando
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <span class="bullet bullet-vertical bg-warning me-3"></span>
                                                    <strong>Dashboards:</strong> Configuração manual
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Serviços Tab -->
                        <div class="tab-pane fade" id="servicos">
                            <div class="card">
                                <div class="card-header border-0">
                                    <h3 class="card-title">🔧 Status dos Serviços</h3>
                                    <div class="card-toolbar">
                                        <button class="btn btn-sm btn-light-primary" onclick="atualizarStatusServicos()">
                                            <i class="ki-outline ki-arrows-circle fs-2"></i> Atualizar
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="status-servicos-loading" class="text-center py-10">
                                        <div class="spinner-border spinner-border-sm" role="status"></div>
                                        <span class="ms-2">Carregando status dos serviços...</span>
                                    </div>
                                    <div id="status-servicos-content" style="display: none;">
                                        <!-- Conteúdo será preenchido via JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Métricas Tab -->
                        <div class="tab-pane fade" id="metricas">
                            <div class="row g-6">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header border-0">
                                            <h3 class="card-title">📊 Métricas Disponíveis</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h5 class="text-primary">🚦 Traefik Gateway</h5>
                                                    <ul class="text-gray-600">
                                                        <li>traefik_service_requests_total</li>
                                                        <li>traefik_service_request_duration_seconds</li>
                                                        <li>traefik_backend_requests_total</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-4">
                                                    <h5 class="text-info">🐘 PostgreSQL</h5>
                                                    <ul class="text-gray-600">
                                                        <li>pg_stat_user_tables_*</li>
                                                        <li>pg_stat_database_*</li>
                                                        <li>pg_up</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-4">
                                                    <h5 class="text-success">🏛️ LegisInc</h5>
                                                    <ul class="text-gray-600">
                                                        <li>legisinc_proposicoes_*</li>
                                                        <li>legisinc_usuarios_*</li>
                                                        <li>legisinc_documentos_*</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Documentação Tab -->
                        <div class="tab-pane fade" id="documentacao">
                            <div class="card">
                                <div class="card-header border-0">
                                    <h3 class="card-title">📚 Guia Completo</h3>
                                </div>
                                <div class="card-body">
                                    <div id="markdown-content" class="markdown-content">
                                        @if($conteudoMarkdown)
                                            <pre class="markdown-raw" style="display: none;">{{ $conteudoMarkdown }}</pre>
                                            <div id="markdown-rendered"></div>
                                        @else
                                            <div class="alert alert-warning">
                                                Documentação não encontrada. Verifique se o arquivo
                                                <code>docs/grafana-prometheus-guia-completo.md</code> existe.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.markdown-content {
    line-height: 1.6;
}

.markdown-content h1 {
    color: #1e1e2d;
    font-size: 2rem;
    font-weight: 600;
    margin: 2rem 0 1rem;
    border-bottom: 2px solid #f1f1f4;
    padding-bottom: 0.5rem;
}

.markdown-content h2 {
    color: #3f4254;
    font-size: 1.5rem;
    font-weight: 600;
    margin: 1.5rem 0 1rem;
}

.markdown-content h3 {
    color: #5e6278;
    font-size: 1.25rem;
    font-weight: 600;
    margin: 1rem 0 0.5rem;
}

.markdown-content pre {
    background: #f8f9fa;
    border: 1px solid #e1e3ea;
    border-radius: 0.375rem;
    padding: 1rem;
    overflow-x: auto;
    font-size: 0.875rem;
}

.markdown-content code {
    background: #f8f9fa;
    color: #e83e8c;
    padding: 0.125rem 0.25rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}

.markdown-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
}

.markdown-content th,
.markdown-content td {
    border: 1px solid #e1e3ea;
    padding: 0.75rem;
    text-align: left;
}

.markdown-content th {
    background: #f8f9fa;
    font-weight: 600;
}

.markdown-content ul {
    margin: 1rem 0;
    padding-left: 2rem;
}

.markdown-content li {
    margin: 0.25rem 0;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Renderizar markdown
    const markdownRaw = document.querySelector('.markdown-raw');
    const markdownRendered = document.getElementById('markdown-rendered');

    if (markdownRaw && markdownRendered) {
        const content = markdownRaw.textContent;
        markdownRendered.innerHTML = marked.parse(content);
    }

    // Carregar status inicial dos serviços
    atualizarStatusServicos();
});

function atualizarStatusServicos() {
    const loading = document.getElementById('status-servicos-loading');
    const content = document.getElementById('status-servicos-content');

    loading.style.display = 'block';
    content.style.display = 'none';

    fetch('{{ route("admin.monitoramento.status") }}')
        .then(response => response.json())
        .then(data => {
            let html = '<div class="row g-4">';

            Object.entries(data).forEach(([key, servico]) => {
                const statusColor = servico.status === 'running' ? 'success' :
                                  servico.status === 'stopped' ? 'danger' : 'warning';
                const statusText = servico.status === 'running' ? 'Rodando' :
                                 servico.status === 'stopped' ? 'Parado' : 'Desconhecido';

                html += `
                    <div class="col-md-4">
                        <div class="card border-${statusColor}">
                            <div class="card-body">
                                <h5 class="card-title text-${statusColor}">${servico.nome}</h5>
                                <p class="card-text">
                                    <strong>Status:</strong>
                                    <span class="badge badge-${statusColor}">${statusText}</span><br>
                                    <strong>Container:</strong> ${servico.container}<br>
                                    <strong>URL:</strong> <a href="${servico.url}" target="_blank">${servico.url}</a>
                                    ${servico.uptime ? `<br><strong>Uptime:</strong> ${servico.uptime}` : ''}
                                </p>
                            </div>
                        </div>
                    </div>
                `;
            });

            html += '</div>';
            content.innerHTML = html;

            loading.style.display = 'none';
            content.style.display = 'block';
        })
        .catch(error => {
            console.error('Erro ao carregar status:', error);
            content.innerHTML = '<div class="alert alert-danger">Erro ao carregar status dos serviços.</div>';
            loading.style.display = 'none';
            content.style.display = 'block';
        });
}
</script>
@endpush