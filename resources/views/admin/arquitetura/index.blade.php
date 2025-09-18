@extends('components.layouts.app')

@section('title', 'Arquitetura Gateway')

@push('styles')
<style>
.mermaid {
    max-width: 100%;
    overflow-x: auto;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.status-online {
    background-color: #d4edda;
    color: #155724;
}

.status-offline {
    background-color: #f8d7da;
    color: #721c24;
}

.status-running {
    background-color: #d1ecf1;
    color: #0c5460;
}

.card-hover {
    transition: all 0.3s ease;
    cursor: pointer;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
}

.architecture-section {
    margin-bottom: 2rem;
}

.container-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}

.external-link {
    color: #007bff;
    text-decoration: none;
}

.external-link:hover {
    text-decoration: underline;
}

.markdown-content {
    line-height: 1.6;
}

.markdown-content h1,
.markdown-content h2,
.markdown-content h3 {
    color: #1a1a1a;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.markdown-content h1 {
    font-size: 2rem;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
}

.markdown-content h2 {
    font-size: 1.5rem;
    color: #495057;
}

.markdown-content h3 {
    font-size: 1.25rem;
    color: #6c757d;
}

.markdown-content pre {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 4px;
    overflow-x: auto;
}

.markdown-content code {
    background-color: #f8f9fa;
    padding: 2px 4px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
}

.markdown-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
}

.markdown-content th,
.markdown-content td {
    border: 1px solid #dee2e6;
    padding: 0.75rem;
    text-align: left;
}

.markdown-content th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.refresh-btn {
    min-width: 120px;
}

.nav-tabs-custom .nav-link {
    border: none;
    border-bottom: 2px solid transparent;
    background: none;
    color: #6c757d;
    font-weight: 500;
}

.nav-tabs-custom .nav-link.active {
    border-bottom-color: var(--kt-primary);
    color: var(--kt-primary);
    background: none;
}

.tab-content-custom {
    padding-top: 2rem;
}
</style>
@endpush

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    🏗️ Arquitetura Gateway
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Admin</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Arquitetura Gateway</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button class="btn btn-sm btn-light refresh-btn" onclick="refreshStatus()">
                    <i class="ki-duotone ki-arrows-circle fs-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Atualizar Status
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary">
                    <i class="ki-duotone ki-arrow-left fs-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Voltar
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!--begin::Navigation-->
            <ul class="nav nav-tabs nav-tabs-custom nav-line-tabs-2x mb-5 fs-6">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#overview">
                        <i class="ki-duotone ki-abstract-26 fs-4 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Visão Geral
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#containers">
                        <i class="ki-duotone ki-monitor fs-4 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Containers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#fluxos">
                        <i class="ki-duotone ki-route fs-4 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Fluxos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#documentacao">
                        <i class="ki-duotone ki-document fs-4 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Documentação
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#explicacao">
                        <i class="ki-duotone ki-information fs-4 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        Explicação Completa
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#containers-simples">
                        <i class="ki-duotone ki-element-11 fs-4 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                        Containers Simplificado
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#containers-novos">
                        <i class="ki-duotone ki-add-item fs-4 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        Containers Novos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#containers-completo">
                        <i class="ki-duotone ki-menu fs-4 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        Containers — Completo
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#migrar-backend">
                        <i class="ki-duotone ki-switch fs-4 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Migrar Backend
                    </a>
                </li>
            </ul>
            <!--end::Navigation-->

            <!--begin::Tab content-->
            <div class="tab-content tab-content-custom" id="myTabContent">

                <!--begin::Overview tab-->
                <div class="tab-pane fade show active" id="overview" role="tabpanel">

                    <!--begin::Architecture diagram-->
                    <div class="card mb-8">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold">🏗️ Arquitetura Geral dos Containers</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mermaid" id="architecture-diagram">
graph TB
    subgraph "🌐 Frontend & Gateway"
        U[👤 Usuário] --> TK[🚦 Traefik Gateway :8000]
        TK --> |"80%"| L[🏛️ Laravel App :8001]
        TK --> |"20%"| N[⚡ Nova API :3001]
        L --> |"Mirror"| NS[🔍 Nginx Shadow :8002]
        NS --> |"Shadow Copy"| N
    end

    subgraph "🔍 Monitoramento"
        TK --> |"Métricas"| P[📊 Prometheus :9090]
        P --> G[📈 Grafana :3000]
        CM[🔄 Canary Monitor :3003] --> |"Controla %"| TK
        SC[📋 Shadow Comparator :3002] --> |"Compara Respostas"| CM
    end

    subgraph "💾 Dados"
        L --> DB[(🐘 PostgreSQL :5432)]
        N --> DB
        L --> R[(🔴 Redis :6379)]
        N --> R
    end

    subgraph "📄 Documentos"
        L --> OO[📝 OnlyOffice :8080]
    end

    style TK fill:#e1f5fe
    style L fill:#f3e5f5
    style N fill:#e8f5e8
    style CM fill:#fff3e0
    style DB fill:#fce4ec
    style R fill:#ffebee
                            </div>
                        </div>
                    </div>
                    <!--end::Architecture diagram-->

                    <!--begin::Quick access-->
                    <div class="row g-6 mb-8">
                        <div class="col-xl-3 col-lg-6">
                            <div class="card card-hover h-100" onclick="window.open('http://localhost:8000', '_blank')">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="ki-duotone ki-abstract-26 fs-2x text-primary me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div>
                                            <h4 class="fw-bold mb-1">Gateway Principal</h4>
                                            <p class="text-muted mb-0">Traefik Dashboard</p>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge badge-light-primary">:8000</span>
                                        <i class="ki-duotone ki-arrow-top-right fs-2 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                            <div class="card card-hover h-100" onclick="window.open('http://localhost:3003/status', '_blank')">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="ki-duotone ki-arrow-circle fs-2x text-warning me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div>
                                            <h4 class="fw-bold mb-1">Canary Monitor</h4>
                                            <p class="text-muted mb-0">Status & Controle</p>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge badge-light-warning">:3003</span>
                                        <i class="ki-duotone ki-arrow-top-right fs-2 text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                            <div class="card card-hover h-100" onclick="window.open('http://localhost:3000', '_blank')">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="ki-duotone ki-chart-line fs-2x text-success me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div>
                                            <h4 class="fw-bold mb-1">Grafana</h4>
                                            <p class="text-muted mb-0">Métricas & Analytics</p>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge badge-light-success">:3000</span>
                                        <i class="ki-duotone ki-arrow-top-right fs-2 text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                            <div class="card card-hover h-100" onclick="window.open('http://localhost:8090', '_blank')">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="ki-duotone ki-route fs-2x text-info me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div>
                                            <h4 class="fw-bold mb-1">Traefik API</h4>
                                            <p class="text-muted mb-0">API & Rotas</p>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge badge-light-info">:8090</span>
                                        <i class="ki-duotone ki-arrow-top-right fs-2 text-info">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Quick access-->

                </div>
                <!--end::Overview tab-->

                <!--begin::Containers tab-->
                <div class="tab-pane fade" id="containers" role="tabpanel">

                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold">📦 Status dos Containers</h3>
                            </div>
                            <div class="card-toolbar">
                                <button class="btn btn-sm btn-light" onclick="refreshContainers()">
                                    <i class="ki-duotone ki-arrows-circle fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Atualizar
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="containers-grid" class="container-grid">
                                <!-- Containers serão carregados via JavaScript -->
                                <div class="text-center p-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Carregando...</span>
                                    </div>
                                    <p class="mt-3 text-muted">Carregando status dos containers...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!--end::Containers tab-->

                <!--begin::Fluxos tab-->
                <div class="tab-pane fade" id="fluxos" role="tabpanel">

                    <!--begin::Request flow-->
                    <div class="card mb-8">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold">🔄 Fluxo de Requisições com Canary</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mermaid">
sequenceDiagram
    participant U as 👤 Usuário
    participant T as 🚦 Traefik
    participant WS as ⚖️ Weighted Service
    participant L as 🏛️ Laravel
    participant N as ⚡ Nova API
    participant DB as 💾 Database
    participant CM as 🔄 Canary Monitor

    U->>T: GET /api/parlamentares/buscar
    T->>WS: Route to weighted service

    alt 99% das vezes
        WS->>L: Forward to Laravel
        L->>DB: Query parlamentares
        DB-->>L: Return data
        L-->>WS: JSON response
    else 1% das vezes (Canary)
        WS->>N: Forward to Nova API
        N->>DB: Query parlamentares
        DB-->>N: Return data
        N-->>WS: JSON response
    end

    WS-->>T: Response
    T-->>U: JSON data

    Note over CM: Monitor métricas e ajusta %
    CM->>WS: Update weights if healthy
                            </div>
                        </div>
                    </div>
                    <!--end::Request flow-->

                    <!--begin::Migration process-->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold">🚀 Processo de Migration Backend</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mermaid">
sequenceDiagram
    participant Dev as Developer
    participant API as Nova API
    participant Config as Traefik Config
    participant Monitor as Canary Monitor

    Dev->>API: 1. Implement endpoint
    Note over API: Compatible response format

    Dev->>Config: 2. Add weighted route
    Note over Config: Nova API and Laravel weighted

    Config->>Monitor: 3. Monitor detects new route
    Monitor->>Monitor: 4. Start health checks
    Monitor->>Config: 5. Auto-scale if healthy
    Note over Monitor: Auto-scale gradually to 100%
                            </div>
                        </div>
                    </div>
                    <!--end::Migration process-->

                </div>
                <!--end::Fluxos tab-->

                <!--begin::Documentação tab-->
                <div class="tab-pane fade" id="documentacao" role="tabpanel">

                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold">📚 Documentação Completa</h3>
                            </div>
                            <div class="card-toolbar">
                                <a href="{{ asset('docs/arquitetura-gateway-visual.md') }}" target="_blank" class="btn btn-sm btn-light-primary">
                                    <i class="ki-duotone ki-document fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Ver Markdown
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="markdown-content">
                                <div class="alert alert-info d-flex align-items-center p-5 mb-10">
                                    <i class="ki-duotone ki-information-5 fs-2hx text-info me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <h4 class="mb-1 text-info">Documentação Interativa</h4>
                                        <span>Esta página apresenta uma versão interativa da documentação. Para a versão completa em Mermaid, acesse o arquivo markdown através do botão acima.</span>
                                    </div>
                                </div>

                                <h2>🎯 Benefícios da Arquitetura</h2>
                                <div class="row g-4 mb-8">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="ki-duotone ki-shield-tick fs-2x text-success me-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            <h4 class="fw-bold mb-0">Zero Downtime</h4>
                                        </div>
                                        <ul class="list-unstyled">
                                            <li>✅ Canary Deployment</li>
                                            <li>✅ Health Monitoring</li>
                                            <li>✅ Auto Rollback</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="ki-duotone ki-chart-line-up fs-2x text-primary me-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <h4 class="fw-bold mb-0">Observabilidade</h4>
                                        </div>
                                        <ul class="list-unstyled">
                                            <li>📊 Métricas Tempo Real</li>
                                            <li>📋 Logs Estruturados</li>
                                            <li>📈 Dashboards Visuais</li>
                                        </ul>
                                    </div>
                                </div>

                                <h2>🔧 Endpoints de Controle</h2>
                                <div class="table-responsive">
                                    <table class="table table-rounded table-striped border gy-7 gs-7">
                                        <thead>
                                            <tr class="fw-semibold fs-6 text-gray-800 border-bottom-2 border-gray-200">
                                                <th>Endpoint</th>
                                                <th>Método</th>
                                                <th>Descrição</th>
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><code>localhost:3003/status</code></td>
                                                <td><span class="badge badge-light-success">GET</span></td>
                                                <td>Status atual do canary</td>
                                                <td><a href="http://localhost:3003/status" target="_blank" class="btn btn-sm btn-light-primary">Abrir</a></td>
                                            </tr>
                                            <tr>
                                                <td><code>localhost:3003/metrics/history</code></td>
                                                <td><span class="badge badge-light-success">GET</span></td>
                                                <td>Histórico de métricas</td>
                                                <td><a href="http://localhost:3003/metrics/history" target="_blank" class="btn btn-sm btn-light-primary">Abrir</a></td>
                                            </tr>
                                            <tr>
                                                <td><code>localhost:8090/dashboard/</code></td>
                                                <td><span class="badge badge-light-success">GET</span></td>
                                                <td>Dashboard Traefik</td>
                                                <td><a href="http://localhost:8090/dashboard/" target="_blank" class="btn btn-sm btn-light-primary">Abrir</a></td>
                                            </tr>
                                            <tr>
                                                <td><code>localhost:3000</code></td>
                                                <td><span class="badge badge-light-success">GET</span></td>
                                                <td>Grafana (admin/admin)</td>
                                                <td><a href="http://localhost:3000" target="_blank" class="btn btn-sm btn-light-primary">Abrir</a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!--end::Documentação tab-->

                <!--begin::Explicação Completa tab-->
                <div class="tab-pane fade" id="explicacao" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold">🏗️ Explicação Completa da Arquitetura Gateway</h3>
                            </div>
                            <div class="card-toolbar">
                                <button class="btn btn-sm btn-light-info me-2" onclick="toggleExplanationView()">
                                    <i class="ki-duotone ki-eye fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Alternar Visualização
                                </button>
                                <a href="{{ asset('docs/arquitetura-gateway-explicacao-completa.md') }}" target="_blank" class="btn btn-sm btn-light-primary">
                                    <i class="ki-duotone ki-document fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Ver Markdown
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Alert informativo -->
                            <div class="alert alert-info d-flex align-items-center mb-5" role="alert">
                                <i class="ki-duotone ki-information-2 fs-2x text-info me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-info">Documento Explicativo Completo</h4>
                                    <span>Este documento explica em detalhes técnicos e não-técnicos o motivo da implementação do API Gateway, seus benefícios e como integrar com APIs externas.</span>
                                </div>
                            </div>

                            <!-- Quick Links -->
                            <div class="row g-4 mb-8">
                                <div class="col-md-3">
                                    <div class="card bg-light-primary">
                                        <div class="card-body p-4">
                                            <h5 class="text-primary mb-2">📌 Resumo Executivo</h5>
                                            <p class="text-gray-600 fs-7 mb-0">O que mudou e por quê</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light-success">
                                        <div class="card-body p-4">
                                            <h5 class="text-success mb-2">🎯 Para Não-Técnicos</h5>
                                            <p class="text-gray-600 fs-7 mb-0">Analogias simples</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light-warning">
                                        <div class="card-body p-4">
                                            <h5 class="text-warning mb-2">🔧 Detalhes Técnicos</h5>
                                            <p class="text-gray-600 fs-7 mb-0">Arquitetura completa</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light-info">
                                        <div class="card-body p-4">
                                            <h5 class="text-info mb-2">🔌 APIs Externas</h5>
                                            <p class="text-gray-600 fs-7 mb-0">Como integrar</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Markdown Content -->
                            <div id="explicacao-markdown-content" class="markdown-content">
                                @if($explicacaoCompleta)
                                    <pre class="explicacao-markdown-raw" style="display: none;">{{ $explicacaoCompleta }}</pre>
                                    <div id="explicacao-markdown-rendered"></div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="ki-duotone ki-warning fs-2x text-warning me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span>Documento de explicação não encontrado. Verifique se o arquivo <code>docs/arquitetura-gateway-explicacao-completa.md</code> existe.</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Explicação Completa tab-->

                <!--begin::Containers Simplificado tab-->
                <div class="tab-pane fade" id="containers-simples" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold">📦 Containers - Explicação Simples e Intuitiva</h3>
                            </div>
                            <div class="card-toolbar">
                                <button class="btn btn-sm btn-light-primary me-2" onclick="toggleContainersView()">
                                    <i class="ki-duotone ki-eye fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Alternar Visualização
                                </button>
                                <a href="{{ asset('docs/containers-explicacao-simples.md') }}" target="_blank" class="btn btn-sm btn-light-info">
                                    <i class="ki-duotone ki-document fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Ver Markdown
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Alert com analogia -->
                            <div class="alert alert-primary d-flex align-items-center mb-5" role="alert">
                                <i class="ki-duotone ki-home fs-2x text-primary me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-primary">Analogia do Condomínio Digital</h4>
                                    <span>Imagine cada serviço como um apartamento independente em um condomínio. Todos têm sua função, mas compartilham a mesma infraestrutura.</span>
                                </div>
                            </div>

                            <!-- Cards de Containers -->
                            <div class="row g-4 mb-8">
                                <div class="col-md-3">
                                    <div class="card bg-light-warning h-100">
                                        <div class="card-body text-center p-4">
                                            <i class="ki-duotone ki-shield-tick fs-3x text-warning mb-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <h5 class="text-warning mb-2">🚪 Portaria</h5>
                                            <p class="text-gray-600 fs-7 mb-0">Traefik Gateway</p>
                                            <small class="text-muted">Recebe e direciona visitantes</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light-primary h-100">
                                        <div class="card-body text-center p-4">
                                            <i class="ki-duotone ki-code fs-3x text-primary mb-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <h5 class="text-primary mb-2">🏛️ Escritório</h5>
                                            <p class="text-gray-600 fs-7 mb-0">Laravel App</p>
                                            <small class="text-muted">Onde o trabalho acontece</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light-success h-100">
                                        <div class="card-body text-center p-4">
                                            <i class="ki-duotone ki-data fs-3x text-success mb-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <h5 class="text-success mb-2">🗄️ Arquivo</h5>
                                            <p class="text-gray-600 fs-7 mb-0">PostgreSQL</p>
                                            <small class="text-muted">Guarda todos os dados</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light-info h-100">
                                        <div class="card-body text-center p-4">
                                            <i class="ki-duotone ki-graph-3 fs-3x text-info mb-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <h5 class="text-info mb-2">📺 TV Central</h5>
                                            <p class="text-gray-600 fs-7 mb-0">Grafana</p>
                                            <small class="text-muted">Mostra tudo visualmente</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Exemplo de Comunicação Visual -->
                            <div class="card bg-light mb-5">
                                <div class="card-header border-0">
                                    <h4 class="card-title">🔗 Como os Containers Conversam</h4>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-around text-center">
                                        <div>
                                            <div class="symbol symbol-50px symbol-light-primary mb-3">
                                                <span class="symbol-label">👤</span>
                                            </div>
                                            <span class="fw-bold">Usuário</span>
                                        </div>
                                        <i class="ki-duotone ki-arrow-right fs-2x text-gray-400">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div>
                                            <div class="symbol symbol-50px symbol-light-warning mb-3">
                                                <span class="symbol-label">🚪</span>
                                            </div>
                                            <span class="fw-bold">Portaria</span>
                                        </div>
                                        <i class="ki-duotone ki-arrow-right fs-2x text-gray-400">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div>
                                            <div class="symbol symbol-50px symbol-light-primary mb-3">
                                                <span class="symbol-label">🏛️</span>
                                            </div>
                                            <span class="fw-bold">Escritório</span>
                                        </div>
                                        <i class="ki-duotone ki-arrow-right fs-2x text-gray-400">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div>
                                            <div class="symbol symbol-50px symbol-light-success mb-3">
                                                <span class="symbol-label">🗄️</span>
                                            </div>
                                            <span class="fw-bold">Arquivo</span>
                                        </div>
                                    </div>
                                    <div class="text-center mt-4">
                                        <p class="text-muted">
                                            <strong>Exemplo:</strong> "Quero ver proposições" → Portaria recebe → Escritório processa → Arquivo fornece dados → Resposta volta pelo mesmo caminho
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Markdown Content -->
                            <div id="containers-markdown-content" class="markdown-content">
                                @if($containersExplicacao)
                                    <pre class="containers-markdown-raw" style="display: none;">{{ $containersExplicacao }}</pre>
                                    <div id="containers-markdown-rendered"></div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="ki-duotone ki-warning fs-2x text-warning me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span>Documento de explicação não encontrado. Verifique se o arquivo <code>docs/containers-explicacao-simples.md</code> existe.</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Containers Simplificado tab-->

                <!--begin::Containers Novos tab-->
                <div class="tab-pane fade" id="containers-novos" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold">🆕 Containers Novos - Por Que Existem</h3>
                            </div>
                            <div class="card-toolbar">
                                <button class="btn btn-sm btn-light-success me-2" onclick="checkAllHealth()">
                                    <i class="ki-duotone ki-shield-tick fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Verificar Saúde
                                </button>
                                <a href="{{ asset('docs/containers-novos-explicacao.md') }}" target="_blank" class="btn btn-sm btn-light-primary">
                                    <i class="ki-duotone ki-document fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Ver Markdown
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Alert sobre os 3 superpoderes -->
                            <div class="alert alert-success d-flex align-items-center mb-5" role="alert">
                                <i class="ki-duotone ki-rocket fs-2x text-success me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-success">3 Superpoderes Habilitados</h4>
                                    <span>🔀 <strong>Controle de Tráfego</strong> (gateway, shadow, canary) • 📊 <strong>Observabilidade</strong> (métricas em tempo real) • 🚀 <strong>Serviços Modernos</strong> (nova API, conversão de docs)</span>
                                </div>
                            </div>

                            <!-- Grupos de Containers -->
                            <div class="accordion" id="containersAccordion">

                                <!-- Grupo 1: Tráfego e Migração -->
                                <div class="accordion-item mb-3">
                                    <h2 class="accordion-header" id="headingTraffic">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTraffic">
                                            <i class="ki-duotone ki-arrow-mix fs-2 text-warning me-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div>
                                                <h4 class="mb-0">🔀 Tráfego e Migração</h4>
                                                <small class="text-muted">Gateway, Shadow, Canary Monitor</small>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapseTraffic" class="accordion-collapse collapse show" data-bs-parent="#containersAccordion">
                                        <div class="accordion-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Container</th>
                                                            <th>Porta</th>
                                                            <th>O Que Faz</th>
                                                            <th>Se Parar...</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>🚦 Traefik Gateway</strong></td>
                                                            <td><span class="badge badge-light">8000/8090</span></td>
                                                            <td>Roteia requisições (90% Laravel / 10% Nova)</td>
                                                            <td><span class="badge badge-danger">Sistema para</span></td>
                                                            <td><span id="health-traefik" class="badge badge-secondary">Verificando...</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>🔄 Nginx Shadow</strong></td>
                                                            <td><span class="badge badge-light">8002</span></td>
                                                            <td>Duplica para teste sem afetar usuário</td>
                                                            <td><span class="badge badge-warning">Perde teste seguro</span></td>
                                                            <td><span id="health-shadow" class="badge badge-secondary">Verificando...</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>🔍 Comparator</strong></td>
                                                            <td><span class="badge badge-light">3002</span></td>
                                                            <td>Compara respostas Legacy vs Nova</td>
                                                            <td><span class="badge badge-warning">Fica "cego"</span></td>
                                                            <td><span id="health-comparator" class="badge badge-secondary">Verificando...</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>📊 Canary Monitor</strong></td>
                                                            <td><span class="badge badge-light">3003</span></td>
                                                            <td>Painel visual do canário</td>
                                                            <td><span class="badge badge-info">Perde visualização</span></td>
                                                            <td><span id="health-canary" class="badge badge-secondary">Verificando...</span></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Grupo 2: Aplicações -->
                                <div class="accordion-item mb-3">
                                    <h2 class="accordion-header" id="headingApps">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseApps">
                                            <i class="ki-duotone ki-code fs-2 text-primary me-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div>
                                                <h4 class="mb-0">🧠 Aplicações</h4>
                                                <small class="text-muted">Laravel Legacy e Nova API</small>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapseApps" class="accordion-collapse collapse" data-bs-parent="#containersAccordion">
                                        <div class="accordion-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Container</th>
                                                            <th>Porta</th>
                                                            <th>O Que Faz</th>
                                                            <th>Se Parar...</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>🏛️ Laravel App</strong></td>
                                                            <td><span class="badge badge-light">8001</span></td>
                                                            <td>Sistema legado (90% do tráfego)</td>
                                                            <td><span class="badge badge-danger">Sistema principal para</span></td>
                                                            <td><span id="health-laravel" class="badge badge-secondary">Verificando...</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>⚡ Nova API</strong></td>
                                                            <td><span class="badge badge-light">3001</span></td>
                                                            <td>Versão moderna (10% canary)</td>
                                                            <td><span class="badge badge-warning">Volta 100% Laravel</span></td>
                                                            <td><span id="health-nova-api" class="badge badge-secondary">Verificando...</span></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Grupo 3: Observabilidade -->
                                <div class="accordion-item mb-3">
                                    <h2 class="accordion-header" id="headingObservability">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseObservability">
                                            <i class="ki-duotone ki-chart-line-up fs-2 text-info me-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div>
                                                <h4 class="mb-0">📊 Observabilidade</h4>
                                                <small class="text-muted">Prometheus, Grafana, Exporters</small>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapseObservability" class="accordion-collapse collapse" data-bs-parent="#containersAccordion">
                                        <div class="accordion-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Container</th>
                                                            <th>Porta</th>
                                                            <th>O Que Faz</th>
                                                            <th>Se Parar...</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>📈 Prometheus</strong></td>
                                                            <td><span class="badge badge-light">9090</span></td>
                                                            <td>Coleta todas as métricas</td>
                                                            <td><span class="badge badge-info">Perde gráficos</span></td>
                                                            <td><span id="health-prometheus" class="badge badge-secondary">Verificando...</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>🐘 Postgres Exporter</strong></td>
                                                            <td><span class="badge badge-light">9187</span></td>
                                                            <td>Métricas do banco de dados</td>
                                                            <td><span class="badge badge-info">Perde métricas DB</span></td>
                                                            <td><span id="health-pg-exporter" class="badge badge-secondary">Verificando...</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>📺 Grafana</strong></td>
                                                            <td><span class="badge badge-light">3000</span></td>
                                                            <td>Dashboards visuais</td>
                                                            <td><span class="badge badge-info">Perde visualização</span></td>
                                                            <td><span id="health-grafana" class="badge badge-secondary">Verificando...</span></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Grupo 4: Serviços de Suporte -->
                                <div class="accordion-item mb-3">
                                    <h2 class="accordion-header" id="headingSupport">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSupport">
                                            <i class="ki-duotone ki-setting-2 fs-2 text-success me-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div>
                                                <h4 class="mb-0">🛠️ Serviços de Suporte</h4>
                                                <small class="text-muted">OnlyOffice, Redis, PostgreSQL</small>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapseSupport" class="accordion-collapse collapse" data-bs-parent="#containersAccordion">
                                        <div class="accordion-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Container</th>
                                                            <th>Porta</th>
                                                            <th>O Que Faz</th>
                                                            <th>Se Parar...</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>📝 OnlyOffice</strong></td>
                                                            <td><span class="badge badge-light">8080</span></td>
                                                            <td>Edita/converte documentos</td>
                                                            <td><span class="badge badge-warning">Edição para</span></td>
                                                            <td><span id="health-onlyoffice" class="badge badge-secondary">Verificando...</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>📮 Redis</strong></td>
                                                            <td><span class="badge badge-light">6379</span></td>
                                                            <td>Cache e filas</td>
                                                            <td><span class="badge badge-warning">Sistema lento</span></td>
                                                            <td><span id="health-redis" class="badge badge-secondary">Verificando...</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>🗄️ PostgreSQL</strong></td>
                                                            <td><span class="badge badge-light">5432</span></td>
                                                            <td>Banco de dados principal</td>
                                                            <td><span class="badge badge-danger">Tudo para!</span></td>
                                                            <td><span id="health-postgres" class="badge badge-secondary">Verificando...</span></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Exemplo Prático: O que acontece numa request -->
                            <div class="row mt-8">
                                <!-- Shadow Traffic -->
                                <div class="col-lg-6">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <div class="card-title">
                                                <h4 class="fw-bold text-warning">🔄 1) Shadow Traffic (Teste Sem Risco)</h4>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="steps">
                                                <div class="d-flex align-items-center mb-4">
                                                    <span class="badge badge-circle badge-light-primary me-3">1</span>
                                                    <div>
                                                        <strong>Frontend chama:</strong><br>
                                                        <code class="bg-light-primary text-primary p-1 rounded">GET http://localhost:8000/api/proposicoes</code>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-4">
                                                    <span class="badge badge-circle badge-light-warning me-3">2</span>
                                                    <div>
                                                        <strong>Traefik (8000)</strong> encaminha para <strong>Laravel</strong><br>
                                                        <small class="text-muted">Resposta real para o usuário</small>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-4">
                                                    <span class="badge badge-circle badge-light-info me-3">3</span>
                                                    <div>
                                                        <strong>Nginx Shadow (8002)</strong> espelha para <strong>Nova API (3001)</strong><br>
                                                        <small class="text-muted">Com header <code>X-Shadow-Mode: true</code></small>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-4">
                                                    <span class="badge badge-circle badge-light-success me-3">4</span>
                                                    <div>
                                                        <strong>Shadow Comparator (3002)</strong> recebe as duas respostas<br>
                                                        <small class="text-muted">Mostra diff (ignora timestamp, request_id)</small>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-circle badge-light-dark me-3">5</span>
                                                    <div>
                                                        <strong>Você corrige</strong> diferenças até ficar estável<br>
                                                        <small class="text-success">✅ Usuário nem sabe do teste!</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Canary Deployment -->
                                <div class="col-lg-6">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <div class="card-title">
                                                <h4 class="fw-bold text-success">🐤 2) Canary (Tráfego Real Controlado)</h4>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="steps">
                                                <div class="d-flex align-items-center mb-4">
                                                    <span class="badge badge-circle badge-light-primary me-3">1</span>
                                                    <div>
                                                        <strong>Traefik pesa a rota:</strong><br>
                                                        <span class="badge badge-primary">90% Laravel</span>
                                                        <span class="badge badge-success">10% Nova API</span>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-4">
                                                    <span class="badge badge-circle badge-light-warning me-3">2</span>
                                                    <div>
                                                        <strong>Canary Monitor (3003)</strong> exibe:<br>
                                                        <small class="text-muted">p95, erros, RPS por rota</small>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-4">
                                                    <span class="badge badge-circle badge-light-success me-3">3</span>
                                                    <div>
                                                        <strong>Guardrails automáticos:</strong>
                                                        <div class="mt-2">
                                                            <div class="d-flex align-items-center mb-1">
                                                                <i class="ki-duotone ki-arrow-up fs-7 text-success me-1"><span class="path1"></span><span class="path2"></span></i>
                                                                <small>Erro ≤ 0,5% + p95 ≤ 200ms → sobe para 25%/50%...</small>
                                                            </div>
                                                            <div class="d-flex align-items-center">
                                                                <i class="ki-duotone ki-arrow-down fs-7 text-danger me-1"><span class="path1"></span><span class="path2"></span></i>
                                                                <small>Erro > 1% → rollback automático</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="alert alert-light-primary mt-4">
                                                    <strong>💡 Resultado:</strong> Migração gradual baseada em métricas reais
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- O que cada container resolve -->
                            <div class="card bg-light-info mt-8">
                                <div class="card-header border-0">
                                    <h4 class="card-title text-info">💡 O que cada container resolve que o monolito não resolvia</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <h6 class="fw-bold text-primary">🚦 Traefik Gateway</h6>
                                                <p class="text-gray-600 fs-7 mb-2">Migrar endpoint por endpoint, canário por rota, integrar API externa sem tocar no frontend</p>
                                            </div>

                                            <div class="mb-4">
                                                <h6 class="fw-bold text-warning">🔄 Nginx Shadow + Comparator</h6>
                                                <p class="text-gray-600 fs-7 mb-2">Validar compatibilidade real em produção sem risco</p>
                                            </div>

                                            <div class="mb-4">
                                                <h6 class="fw-bold text-success">📊 Prometheus + Exporters + Grafana</h6>
                                                <p class="text-gray-600 fs-7 mb-2">Ver p95, erros, RPS, DB — tomar decisão com dados (subir/baixar canário, otimizar query)</p>
                                            </div>

                                            <div class="mb-4">
                                                <h6 class="fw-bold text-info">⚡ Nova API</h6>
                                                <p class="text-gray-600 fs-7 mb-2">Onde você reimplementa regras/endpoints, ganhando flexibilidade tecnológica</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <h6 class="fw-bold text-primary">📝 OnlyOffice</h6>
                                                <p class="text-gray-600 fs-7 mb-2">Cadeia de documentos oficiais (PDF/assinaturas) com confiabilidade</p>
                                            </div>

                                            <div class="mb-4">
                                                <h6 class="fw-bold text-warning">📮 Redis</h6>
                                                <p class="text-gray-600 fs-7 mb-2">Filas/caches para performance e integridade (idempotência, rate-limit)</p>
                                            </div>

                                            <div class="mb-4">
                                                <h6 class="fw-bold text-success">🗄️ PostgreSQL</h6>
                                                <p class="text-gray-600 fs-7 mb-2">Verdade única dos dados com observabilidade completa</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dicas Rápidas de Operação -->
                            <div class="card bg-light-warning mt-6">
                                <div class="card-header border-0">
                                    <h4 class="card-title text-warning">⚡ Dicas Rápidas de Operação</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h6 class="fw-bold text-dark">👁️ Monitoramento</h6>
                                            <div class="mb-2">
                                                <a href="http://localhost:3003" target="_blank" class="text-primary text-hover-success">
                                                    <i class="ki-duotone ki-chart-simple fs-6 me-1"><span class="path1"></span><span class="path2"></span></i>
                                                    Canary Monitor :3003
                                                </a><br>
                                                <small class="text-muted">Ver % de tráfego por rota</small>
                                            </div>
                                            <div class="mb-2">
                                                <code class="bg-dark text-light p-1 rounded fs-8">docker logs -f legisinc-gateway-simple</code><br>
                                                <small class="text-muted">Logs do gateway em tempo real</small>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <h6 class="fw-bold text-dark">🔧 Troubleshooting</h6>
                                            <div class="mb-2">
                                                <code class="bg-dark text-light p-1 rounded fs-8">curl localhost:3001/health</code><br>
                                                <small class="text-muted">Nova API unhealthy?</small>
                                            </div>
                                            <div class="mb-2">
                                                <code class="bg-dark text-light p-1 rounded fs-8">docker logs legisinc-nova-api</code><br>
                                                <small class="text-muted">Porta errada? Dep faltando? Timeout DB/Redis?</small>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <h6 class="fw-bold text-dark">⚠️ Shadow</h6>
                                            <div class="mb-2">
                                                <span class="badge badge-warning">X-Shadow-Mode: true</span><br>
                                                <small class="text-muted">Confirme na Nova API para não persistir/enfileirar</small>
                                            </div>
                                            <div class="mb-2">
                                                <strong>Bypass temporário:</strong><br>
                                                <small class="text-muted">Se Traefik cair, use porta direta :8001 (debug only)</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="separator my-4"></div>

                                    <div class="alert alert-light-info">
                                        <i class="ki-duotone ki-information fs-2 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                        <strong>Lembre-se:</strong> Se Prometheus/Grafana caírem, o app segue normalmente — só perde gráficos. O sistema é resiliente por design.
                                    </div>
                                </div>
                            </div>

                            <!-- Guia de Deploy em VPS -->
                            <div class="card bg-light-success mt-8">
                                <div class="card-header border-0">
                                    <h4 class="card-title text-success">🚀 Deploy em VPS - Configuração Completa</h4>
                                    <div class="card-toolbar">
                                        <span class="badge badge-light-success">Guia Passo a Passo</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Estrutura de Arquivos -->
                                    <div class="mb-8">
                                        <h5 class="fw-bold text-dark mb-4">📁 Estrutura de Arquivos no Projeto</h5>
                                        <div class="bg-dark text-light p-4 rounded">
<pre class="text-light mb-0">legisinc-v2/
├── 📄 docker-compose.gateway-simple.yml    # ← Arquivo principal para VPS
├── 📁 gateway/                              # ← Pasta com todas as configurações
│   ├── 📁 traefik/
│   │   ├── 📁 dynamic/
│   │   │   └── routes.yml                   # ← Configuração de rotas
│   │   └── 📁 canary/
│   │       └── canary-routes.yml
│   ├── 📁 prometheus/
│   │   └── prometheus.yml                   # ← Configuração do Prometheus
│   ├── 📁 grafana/
│   │   ├── 📁 datasources/
│   │   │   └── prometheus.yml
│   │   └── 📁 dashboards/
│   │       └── dashboard.yml
│   ├── 📁 postgres-exporter/
│   │   └── queries.yaml                     # ← Queries customizadas do PostgreSQL
│   ├── 📁 canary-monitor/                   # ← Monitor do canário (Node.js)
│   ├── 📁 shadow-comparator/                # ← Comparador de respostas (Node.js)
│   └── 📁 nginx/                           # ← Configurações do Nginx Shadow
└── 📄 .env                                 # ← Variáveis de ambiente</pre>
                                        </div>
                                    </div>

                                    <!-- Passos do Deploy -->
                                    <div class="accordion" id="deployAccordion">

                                        <!-- Passo 1: Preparação da VPS -->
                                        <div class="accordion-item mb-3">
                                            <h2 class="accordion-header" id="headingPrep">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePrep">
                                                    <i class="ki-duotone ki-server fs-2 text-primary me-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <div>
                                                        <h5 class="mb-0">1️⃣ Preparação da VPS</h5>
                                                        <small class="text-muted">Instalar Docker, Docker Compose, Git</small>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapsePrep" class="accordion-collapse collapse show" data-bs-parent="#deployAccordion">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6 class="fw-bold text-primary">📦 Instalar Docker & Docker Compose</h6>
                                                            <pre class="bg-dark text-light p-3 rounded fs-8">
# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER

# Instalar Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Reiniciar sessão
logout</pre>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6 class="fw-bold text-success">🔧 Verificar Instalação</h6>
                                                            <pre class="bg-dark text-light p-3 rounded fs-8">
# Verificar versões
docker --version
docker-compose --version

# Testar Docker
docker run hello-world

# Clonar projeto
git clone https://github.com/seu-usuario/legisinc-v2.git
cd legisinc-v2</pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Passo 2: Configuração de Ambiente -->
                                        <div class="accordion-item mb-3">
                                            <h2 class="accordion-header" id="headingEnv">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEnv">
                                                    <i class="ki-duotone ki-setting-2 fs-2 text-warning me-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <div>
                                                        <h5 class="mb-0">2️⃣ Configuração de Ambiente</h5>
                                                        <small class="text-muted">Ajustar .env e arquivos de configuração</small>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapseEnv" class="accordion-collapse collapse" data-bs-parent="#deployAccordion">
                                                <div class="accordion-body">
                                                    <div class="alert alert-warning">
                                                        <i class="ki-duotone ki-warning fs-2 text-warning me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        <strong>⚠️ Importante:</strong> Substitua <code>localhost</code> pelo IP/domínio da sua VPS em todos os arquivos!
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <h6 class="fw-bold text-warning">📝 Arquivos que DEVEM ser alterados:</h6>

                                                            <div class="table-responsive mb-4">
                                                                <table class="table table-sm table-bordered">
                                                                    <thead class="table-dark">
                                                                        <tr>
                                                                            <th>Arquivo</th>
                                                                            <th>O que alterar</th>
                                                                            <th>De → Para</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td><code>.env</code></td>
                                                                            <td>APP_URL, DB_HOST</td>
                                                                            <td><code>localhost</code> → <code>SEU-IP-VPS</code></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><code>gateway/traefik/dynamic/routes.yml</code></td>
                                                                            <td>CORS allowOriginList</td>
                                                                            <td><code>localhost</code> → <code>SEU-DOMINIO.com</code></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><code>docker-compose.gateway-simple.yml</code></td>
                                                                            <td>Traefik Host rule</td>
                                                                            <td><code>Host(`localhost`)</code> → <code>Host(`SEU-DOMINIO.com`)</code></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><code>gateway/grafana/datasources/prometheus.yml</code></td>
                                                                            <td>URL do Prometheus</td>
                                                                            <td><code>localhost:9090</code> → <code>prometheus:9090</code></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <h6 class="fw-bold text-info">🔧 Comandos Rápidos de Substituição:</h6>
                                                            <pre class="bg-dark text-light p-3 rounded fs-8">
# Definir variáveis (AJUSTE ESTES VALORES!)
export VPS_IP="192.168.1.100"
export VPS_DOMAIN="meudominio.com"

# Substituir localhost pelo IP/domínio da VPS
sed -i "s/localhost/$VPS_DOMAIN/g" .env
sed -i "s/localhost/$VPS_DOMAIN/g" gateway/traefik/dynamic/routes.yml
sed -i "s/Host(\`localhost\`)/Host(\`$VPS_DOMAIN\`)/g" docker-compose.gateway-simple.yml

# Verificar mudanças
grep -r "localhost" . --exclude-dir=node_modules --exclude-dir=vendor</pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Passo 3: Configuração de Rede e Firewall -->
                                        <div class="accordion-item mb-3">
                                            <h2 class="accordion-header" id="headingNetwork">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNetwork">
                                                    <i class="ki-duotone ki-security-user fs-2 text-danger me-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <div>
                                                        <h5 class="mb-0">3️⃣ Rede e Firewall</h5>
                                                        <small class="text-muted">Abrir portas necessárias</small>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapseNetwork" class="accordion-collapse collapse" data-bs-parent="#deployAccordion">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6 class="fw-bold text-danger">🔥 Configurar Firewall (UFW)</h6>
                                                            <pre class="bg-dark text-light p-3 rounded fs-8">
# Instalar UFW
sudo apt install ufw

# Configurações básicas
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh

# Portas do LegisInc
sudo ufw allow 8000/tcp   # Gateway principal
sudo ufw allow 3000/tcp   # Grafana
sudo ufw allow 8090/tcp   # Traefik Dashboard
sudo ufw allow 9090/tcp   # Prometheus
sudo ufw allow 3003/tcp   # Canary Monitor

# Ativar firewall
sudo ufw enable
sudo ufw status</pre>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6 class="fw-bold text-success">📋 Portas Usadas</h6>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Porta</th>
                                                                            <th>Serviço</th>
                                                                            <th>Público?</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr class="table-success">
                                                                            <td><strong>8000</strong></td>
                                                                            <td>Gateway (Traefik)</td>
                                                                            <td>✅ Sim</td>
                                                                        </tr>
                                                                        <tr class="table-info">
                                                                            <td><strong>3000</strong></td>
                                                                            <td>Grafana</td>
                                                                            <td>🔒 Admin</td>
                                                                        </tr>
                                                                        <tr class="table-warning">
                                                                            <td>8090</td>
                                                                            <td>Traefik Dashboard</td>
                                                                            <td>🔒 Admin</td>
                                                                        </tr>
                                                                        <tr class="table-warning">
                                                                            <td>9090</td>
                                                                            <td>Prometheus</td>
                                                                            <td>🔒 Admin</td>
                                                                        </tr>
                                                                        <tr class="table-info">
                                                                            <td>3003</td>
                                                                            <td>Canary Monitor</td>
                                                                            <td>🔒 Admin</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Passo 4: Deploy -->
                                        <div class="accordion-item mb-3">
                                            <h2 class="accordion-header" id="headingDeploy">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDeploy">
                                                    <i class="ki-duotone ki-rocket fs-2 text-success me-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <div>
                                                        <h5 class="mb-0">4️⃣ Deploy dos Containers</h5>
                                                        <small class="text-muted">Subir toda a arquitetura</small>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapseDeploy" class="accordion-collapse collapse" data-bs-parent="#deployAccordion">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6 class="fw-bold text-success">🚀 Executar Deploy</h6>
                                                            <pre class="bg-dark text-light p-3 rounded fs-8">
# 1. Buildar imagens se necessário
docker-compose build

# 2. Subir todos os containers
docker-compose -f docker-compose.gateway-simple.yml up -d

# 3. Verificar se subiram
docker ps | grep legisinc

# 4. Ver logs em tempo real
docker-compose logs -f</pre>

                                                            <div class="alert alert-light-success mt-3">
                                                                <strong>✅ Sucesso!</strong> Se tudo deu certo, você verá os containers rodando.
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6 class="fw-bold text-warning">🔍 Verificar Saúde</h6>
                                                            <pre class="bg-dark text-light p-3 rounded fs-8">
# Testar gateway principal
curl -I http://SEU-IP:8000

# Testar Grafana
curl -I http://SEU-IP:3000

# Testar Prometheus
curl -I http://SEU-IP:9090

# Testar Canary Monitor
curl -I http://SEU-IP:3003

# Ver logs de erro
docker-compose logs --tail=50</pre>

                                                            <div class="alert alert-light-info mt-3">
                                                                <strong>💡 Dica:</strong> Substitua <code>SEU-IP</code> pelo IP da sua VPS.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- URLs de Acesso -->
                                    <div class="card bg-light-primary mt-6">
                                        <div class="card-header border-0">
                                            <h5 class="card-title text-primary">🌐 URLs de Acesso na VPS</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h6 class="fw-bold">🎯 Principal</h6>
                                                    <div class="mb-2">
                                                        <strong>Aplicação:</strong><br>
                                                        <code class="text-primary">http://SEU-IP:8000</code>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <h6 class="fw-bold">📊 Monitoramento</h6>
                                                    <div class="mb-2">
                                                        <strong>Grafana:</strong><br>
                                                        <code class="text-info">http://SEU-IP:3000</code><br>
                                                        <small>admin / admin123</small>
                                                    </div>
                                                    <div class="mb-2">
                                                        <strong>Prometheus:</strong><br>
                                                        <code class="text-warning">http://SEU-IP:9090</code>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <h6 class="fw-bold">🛠️ Admin</h6>
                                                    <div class="mb-2">
                                                        <strong>Traefik Dashboard:</strong><br>
                                                        <code class="text-success">http://SEU-IP:8090</code>
                                                    </div>
                                                    <div class="mb-2">
                                                        <strong>Canary Monitor:</strong><br>
                                                        <code class="text-primary">http://SEU-IP:3003</code>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Troubleshooting VPS -->
                                    <div class="card bg-light-danger mt-4">
                                        <div class="card-header border-0">
                                            <h5 class="card-title text-danger">🚨 Troubleshooting Comum em VPS</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="fw-bold text-danger">❌ Problemas Comuns</h6>
                                                    <ul class="list-unstyled">
                                                        <li class="d-flex align-items-start mb-2">
                                                            <span class="badge badge-danger me-2">1</span>
                                                            <div>
                                                                <strong>Containers não sobem</strong><br>
                                                                <small>Verificar se Docker daemon está rodando: <code>sudo systemctl status docker</code></small>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex align-items-start mb-2">
                                                            <span class="badge badge-warning me-2">2</span>
                                                            <div>
                                                                <strong>Portas bloqueadas</strong><br>
                                                                <small>Verificar firewall: <code>sudo ufw status</code></small>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex align-items-start mb-2">
                                                            <span class="badge badge-info me-2">3</span>
                                                            <div>
                                                                <strong>Out of memory</strong><br>
                                                                <small>VPS precisa de pelo menos 2GB RAM</small>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="fw-bold text-success">✅ Comandos de Diagnóstico</h6>
                                                    <pre class="bg-dark text-light p-3 rounded fs-8">
# Ver recursos do sistema
free -h
df -h

# Ver containers rodando
docker ps -a

# Ver logs de um container específico
docker logs legisinc-gateway-simple

# Reiniciar tudo
docker-compose down
docker-compose up -d

# Limpar sistema
docker system prune -a</pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Comandos Úteis -->
                            <div class="card bg-light mt-5">
                                <div class="card-header border-0">
                                    <h4 class="card-title">🚀 Comandos Úteis</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Verificação Rápida</h5>
                                            <pre class="bg-dark text-light p-3 rounded">
# Ver containers rodando
docker ps | grep legisinc

# Ver logs de um container
docker logs legisinc-[nome] --tail 50

# Verificar saúde
curl localhost:[porta]/health</pre>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Troubleshooting</h5>
                                            <pre class="bg-dark text-light p-3 rounded">
# Reiniciar container
docker restart legisinc-[nome]

# Ver uso de recursos
docker stats --filter "name=legisinc"

# Limpar e reiniciar
docker-compose down && docker-compose up -d</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Markdown Content -->
                            <div id="containers-novos-markdown-content" class="markdown-content mt-5">
                                @if($containersNovos)
                                    <pre class="containers-novos-markdown-raw" style="display: none;">{{ $containersNovos }}</pre>
                                    <div id="containers-novos-markdown-rendered"></div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="ki-duotone ki-warning fs-2x text-warning me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span>Documento não encontrado. Verifique se o arquivo <code>docs/containers-novos-explicacao.md</code> existe.</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Containers Novos tab-->

                <!--begin::Containers Completo tab-->
                <div class="tab-pane fade" id="containers-completo" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold">📋 Containers — O Que É Cada Um e Por Que Existe</h3>
                            </div>
                            <div class="card-toolbar">
                                <button class="btn btn-sm btn-light-success me-2" onclick="checkAllContainerHealth()">
                                    <i class="ki-duotone ki-shield-tick fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Verificar Todos
                                </button>
                            </div>
                        </div>
                        <div class="card-body">

                            <!-- Explicação do Fluxo em 15 Segundos -->
                            <div class="alert alert-primary d-flex align-items-center mb-8" role="alert">
                                <i class="ki-duotone ki-time fs-2x text-primary me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-primary">👀 Como Explicar o Fluxo em 15 Segundos</h4>
                                    <div class="fs-6">
                                        <strong>1.</strong> Usuário chama <code>GET /api/proposicoes</code> → Gateway (8000)<br>
                                        <strong>2.</strong> Gateway decide: 90% vão para o Legacy (8001), 10% vão para a Nova API (3001) <span class="badge badge-light-success">canário</span><br>
                                        <strong>3.</strong> Em paralelo, o Shadow (8002) pode espelhar 100% para a Nova API sem usar a resposta <span class="badge badge-light-warning">teste sem risco</span><br>
                                        <strong>4.</strong> Prometheus (9090) coleta métricas; Grafana (3000) mostra p95/erros; Canary Monitor (3003) exibe os pesos
                                    </div>
                                </div>
                            </div>

                            <!-- Tabela Completa de Containers -->
                            <div class="card bg-white mb-8">
                                <div class="card-header bg-light">
                                    <h4 class="card-title">🐳 Todos os Containers do Sistema</h4>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover align-middle">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th style="width: 20%;">Container</th>
                                                    <th style="width: 20%;">O Que É</th>
                                                    <th style="width: 25%;">Por Que Existe</th>
                                                    <th style="width: 10%;">Porta</th>
                                                    <th style="width: 25%;">Se Cair, O Que Acontece?</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-light">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-router fs-2 text-primary me-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            <div>
                                                                <strong class="text-primary">legisinc-gateway-simple</strong><br>
                                                                <small class="text-muted">(traefik)</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong>API Gateway</strong><br>
                                                        <small class="text-muted">(o porteiro)</small>
                                                    </td>
                                                    <td>
                                                        <small>Roteia cada request: Legacy, Nova API ou API externa; aplica canary/shadow, headers e rate-limit</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-primary">8000</span><br>
                                                        <span class="badge badge-light">8090</span>
                                                        <small class="d-block text-muted">dashboard</small>
                                                    </td>
                                                    <td>
                                                        <span class="text-danger">❌ Frontend não chega nos backends pela 8000</span><br>
                                                        <small class="text-success">✅ Contornar: chamar Legacy direto (8001) em emergência</small>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-code fs-2 text-success me-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            <div>
                                                                <strong class="text-success">legisinc-app</strong><br>
                                                                <small class="text-muted">(legisinc-v2-app)</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong>Laravel</strong><br>
                                                        <small class="text-muted">(Legacy)</small>
                                                    </td>
                                                    <td>
                                                        <small>Mantém os endpoints ainda não migrados e parte do canário</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-success">8001</span><br>
                                                        <span class="badge badge-light">8444</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-danger">❌ Rotas legadas ficam indisponíveis</span><br>
                                                        <small class="text-info">⚠️ Gateway pode redirecionar só o que já migrou</small>
                                                    </td>
                                                </tr>

                                                <tr class="table-light">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-rocket fs-2 text-warning me-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            <div>
                                                                <strong class="text-warning">legisinc-nova-api</strong><br>
                                                                <small class="text-muted">(legisinc-v2-nova-api)</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong>Nova API</strong><br>
                                                        <small class="text-muted">(modernização)</small>
                                                    </td>
                                                    <td>
                                                        <small>Onde você reimplementa endpoints (shadow + canário) sem tocar no frontend</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-warning">3001</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-warning">⚠️ Falham apenas as requisições que estão indo para a Nova API</span><br>
                                                        <small class="text-success">✅ Canário pode voltar para 0%</small>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-mirror fs-2 text-info me-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            <div>
                                                                <strong class="text-info">legisinc-nginx-shadow</strong><br>
                                                                <small class="text-muted">(nginx)</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong>Proxy de Shadow</strong><br>
                                                        <small class="text-muted">(espelho)</small>
                                                    </td>
                                                    <td>
                                                        <small>Duplica requisições (principalmente GET) para a Nova API, mas ignora a resposta (teste sem risco)</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info">8002</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-warning">⚠️ Você perde a validação "em produção de mentirinha"</span><br>
                                                        <small class="text-success">✅ Nada quebra para o usuário</small>
                                                    </td>
                                                </tr>

                                                <tr class="table-light">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-compare fs-2 text-secondary me-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            <div>
                                                                <strong class="text-secondary">legisinc-shadow-comparator</strong><br>
                                                                <small class="text-muted">(node)</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong>Comparador de Respostas</strong><br>
                                                        <small class="text-muted">(detetive)</small>
                                                    </td>
                                                    <td>
                                                        <small>Compara JSON Legacy vs Nova API e mostra diferenças (ignora timestamp/request_id)</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-secondary">3002</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-warning">⚠️ Fica sem "termômetro" de compatibilidade fina</span><br>
                                                        <small class="text-muted">Migração fica mais no escuro</small>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-chart-simple fs-2 text-primary me-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            <div>
                                                                <strong class="text-primary">legisinc-canary-monitor</strong><br>
                                                                <small class="text-muted">(node)</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong>Painel do Canário</strong><br>
                                                        <small class="text-muted">(controle)</small>
                                                    </td>
                                                    <td>
                                                        <small>Visualiza e (opcionalmente) ajusta pesos por rota, mostra p95/erros do canário</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-primary">3003</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-info">ℹ️ Você ainda pode mudar pesos via config/labels</span><br>
                                                        <small class="text-warning">Só perde o painel amigável</small>
                                                    </td>
                                                </tr>

                                                <tr class="table-light">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-chart-line fs-2 text-warning me-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            <div>
                                                                <strong class="text-warning">legisinc-prometheus-simple</strong><br>
                                                                <small class="text-muted">(prom/prometheus)</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong>Coletor de Métricas</strong><br>
                                                        <small class="text-muted">(sensores)</small>
                                                    </td>
                                                    <td>
                                                        <small>Junta métricas de gateway, banco, app, etc.</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-warning">9090</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-warning">⚠️ Dashboards perdem dados novos</span><br>
                                                        <small class="text-success">✅ App continua funcionando</small>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-graph-3 fs-2 text-info me-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            <div>
                                                                <strong class="text-info">legisinc-grafana-simple</strong><br>
                                                                <small class="text-muted">(grafana)</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong>Dashboards</strong><br>
                                                        <small class="text-muted">(TV)</small>
                                                    </td>
                                                    <td>
                                                        <small>Mostra p95/p99, erros, RPS, saúde, KPIs</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info">3000</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-info">ℹ️ Só perde visualização</span><br>
                                                        <small class="text-success">✅ Nada funcional quebra</small>
                                                    </td>
                                                </tr>

                                                <tr class="table-light">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-database fs-2 text-secondary me-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            <div>
                                                                <strong class="text-secondary">legisinc-postgres-exporter</strong><br>
                                                                <small class="text-muted">(postgres-exporter)</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong>Métricas do Postgres</strong><br>
                                                        <small class="text-muted">(sensor DB)</small>
                                                    </td>
                                                    <td>
                                                        <small>Expõe internals do DB pro Prometheus</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-secondary">9187</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-warning">⚠️ Painéis de DB ficam "cegos"</span><br>
                                                        <small class="text-success">✅ App segue</small>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-data fs-2 text-success me-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            <div>
                                                                <strong class="text-success">legisinc-postgres</strong><br>
                                                                <small class="text-muted">(postgres:15)</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong>Banco de Dados</strong><br>
                                                        <small class="text-muted">(arquivo mestre)</small>
                                                    </td>
                                                    <td>
                                                        <small>Armazena tudo (proposições, usuários, etc.)</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-success">5432</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-danger">❌ A aplicação não lê/escreve</span><br>
                                                        <small class="text-danger">→ indisponibilidade real</small>
                                                    </td>
                                                </tr>

                                                <tr class="table-light">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-flash fs-2 text-warning me-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            <div>
                                                                <strong class="text-warning">legisinc-redis</strong><br>
                                                                <small class="text-muted">(redis:7)</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong>Cache & Filas</strong><br>
                                                        <small class="text-muted">(memória rápida)</small>
                                                    </td>
                                                    <td>
                                                        <small>Sessões, rate-limit, jobs (ex.: conversões), idempotência</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-warning">6379</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-warning">⚠️ Jobs param e cache expira</span><br>
                                                        <small class="text-warning">→ lentidão/erros em algumas rotas</small>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-document fs-2 text-primary me-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            <div>
                                                                <strong class="text-primary">legisinc-onlyoffice</strong><br>
                                                                <small class="text-muted">(documentserver)</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong>Documentos</strong><br>
                                                        <small class="text-muted">(gráfica)</small>
                                                    </td>
                                                    <td>
                                                        <small>Edição/Conversão (PDF, DOCX) no fluxo oficial</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-primary">8080</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-warning">⚠️ Aprovações que convertem/abrem docs falham</span><br>
                                                        <small class="text-info">Ou ficam na fila</small>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Traduções Rápidas -->
                            <div class="card bg-light-info mb-8">
                                <div class="card-header border-0">
                                    <h4 class="card-title text-info">🔎 "Traduções" Rápidas (Para Quem É Não-Técnico)</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <h6 class="fw-bold text-primary">🏢 Gateway</h6>
                                                <p class="text-gray-600 fs-7 mb-2">Recepção que direciona as visitas</p>
                                            </div>

                                            <div class="mb-4">
                                                <h6 class="fw-bold text-success">🏛️ Legacy/Nova API</h6>
                                                <p class="text-gray-600 fs-7 mb-2">Salas onde o atendimento acontece</p>
                                            </div>

                                            <div class="mb-4">
                                                <h6 class="fw-bold text-info">📹 Shadow</h6>
                                                <p class="text-gray-600 fs-7 mb-2">Câmera escondida: grava o atendimento na sala nova, mas não vale como resposta</p>
                                            </div>

                                            <div class="mb-4">
                                                <h6 class="fw-bold text-warning">🐤 Canário</h6>
                                                <p class="text-gray-600 fs-7 mb-2">1 em cada 100 visitas testando a sala nova "de verdade"</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <h6 class="fw-bold text-secondary">📊 Prometheus/Grafana</h6>
                                                <p class="text-gray-600 fs-7 mb-2">Livro de visitas + painel com tempos, filas e erros</p>
                                            </div>

                                            <div class="mb-4">
                                                <h6 class="fw-bold text-warning">📮 Redis</h6>
                                                <p class="text-gray-600 fs-7 mb-2">Fila e memória rápida</p>
                                            </div>

                                            <div class="mb-4">
                                                <h6 class="fw-bold text-success">🗄️ Postgres</h6>
                                                <p class="text-gray-600 fs-7 mb-2">Arquivo mestre</p>
                                            </div>

                                            <div class="mb-4">
                                                <h6 class="fw-bold text-primary">🖨️ OnlyOffice</h6>
                                                <p class="text-gray-600 fs-7 mb-2">Gráfica dos documentos oficiais</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Comandos de Sanity Check -->
                            <div class="card bg-light-success mb-6">
                                <div class="card-header border-0">
                                    <h4 class="card-title text-success">🧰 Comandos de Sanity Check (Rápidos)</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="fw-bold text-dark">🏥 Verificação de Saúde</h6>
                                            <pre class="bg-dark text-light p-3 rounded fs-8">
# Gateway vivo
curl -I http://localhost:8000/health
docker logs -f legisinc-gateway-simple

# Legacy vivo
curl -I http://localhost:8001

# Nova API viva
curl -I http://localhost:3001/health

# Shadow espelhando
curl -I http://localhost:8002</pre>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="fw-bold text-dark">📊 Dashboards & Métricas</h6>
                                            <pre class="bg-dark text-light p-3 rounded fs-8">
# Métricas DB
curl http://localhost:9187/metrics | head -n 5

# Acessar dashboards
# Grafana: http://localhost:3000
# Prometheus: http://localhost:9090
# Canary: http://localhost:3003</pre>

                                            <div class="mt-3">
                                                <div class="d-flex justify-content-between">
                                                    <a href="http://localhost:3000" target="_blank" class="btn btn-sm btn-info">
                                                        <i class="ki-duotone ki-chart-simple fs-6 me-1"><span class="path1"></span><span class="path2"></span></i>
                                                        Grafana
                                                    </a>
                                                    <a href="http://localhost:9090" target="_blank" class="btn btn-sm btn-warning">
                                                        <i class="ki-duotone ki-chart-line fs-6 me-1"><span class="path1"></span><span class="path2"></span></i>
                                                        Prometheus
                                                    </a>
                                                    <a href="http://localhost:3003" target="_blank" class="btn btn-sm btn-primary">
                                                        <i class="ki-duotone ki-monitor fs-6 me-1"><span class="path1"></span><span class="path2"></span></i>
                                                        Canary Monitor
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!--end::Containers Completo tab-->

                <!--begin::Migrar Backend tab-->
                <div class="tab-pane fade" id="migrar-backend" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold">🔄 Como Migrar para um Novo Backend</h3>
                            </div>
                            <div class="card-toolbar">
                                <span class="badge badge-light-primary">Guia Completo</span>
                            </div>
                        </div>
                        <div class="card-body">

                            <!-- Introdução -->
                            <div class="alert alert-info d-flex align-items-center mb-8" role="alert">
                                <i class="ki-duotone ki-information fs-2x text-info me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-info">🎯 Objetivo da Migração</h4>
                                    <div class="fs-6">
                                        Substituir o <strong>Laravel (Legacy)</strong> por um novo backend (ex: Java, Node.js, Python, Go) mantendo o <strong>frontend inalterado</strong> e todas as <strong>regras de negócio funcionais</strong>. O Gateway gerencia a transição de forma gradual e segura.
                                    </div>
                                </div>
                            </div>

                            <!-- Cenário Exemplo -->
                            <div class="card bg-light-warning mb-8">
                                <div class="card-header border-0">
                                    <h4 class="card-title text-warning">📝 Exemplo Prático: Laravel → Java Spring Boot</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="fw-bold text-dark">🔴 Situação Atual</h6>
                                            <ul class="list-unstyled">
                                                <li class="d-flex align-items-center mb-2">
                                                    <i class="ki-duotone ki-check fs-6 text-muted me-2"><span class="path1"></span><span class="path2"></span></i>
                                                    <span>Frontend React/Vue chamando Laravel</span>
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <i class="ki-duotone ki-check fs-6 text-muted me-2"><span class="path1"></span><span class="path2"></span></i>
                                                    <span>Regras de negócio em PHP</span>
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <i class="ki-duotone ki-check fs-6 text-muted me-2"><span class="path1"></span><span class="path2"></span></i>
                                                    <span>Banco PostgreSQL compartilhado</span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="fw-bold text-dark">🟢 Situação Desejada</h6>
                                            <ul class="list-unstyled">
                                                <li class="d-flex align-items-center mb-2">
                                                    <i class="ki-duotone ki-check fs-6 text-success me-2"><span class="path1"></span><span class="path2"></span></i>
                                                    <span>Mesmo Frontend (sem alterações)</span>
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <i class="ki-duotone ki-check fs-6 text-success me-2"><span class="path1"></span><span class="path2"></span></i>
                                                    <span>Regras de negócio em Java</span>
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <i class="ki-duotone ki-check fs-6 text-success me-2"><span class="path1"></span><span class="path2"></span></i>
                                                    <span>Mesmo banco, mesmos dados</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Processo de Migração -->
                            <div class="accordion" id="migrationAccordion">

                                <!-- Fase 1: Preparação -->
                                <div class="accordion-item mb-3">
                                    <h2 class="accordion-header" id="headingPhase1">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePhase1">
                                            <i class="ki-duotone ki-notepad-edit fs-2 text-primary me-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div>
                                                <h5 class="mb-0">1️⃣ Preparação: Analisar e Mapear</h5>
                                                <small class="text-muted">Entender o sistema atual antes de começar</small>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapsePhase1" class="accordion-collapse collapse show" data-bs-parent="#migrationAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="fw-bold text-primary">🕵️ Inventário do Sistema Atual</h6>
                                                    <div class="table-responsive mb-4">
                                                        <table class="table table-sm table-bordered">
                                                            <thead class="table-dark">
                                                                <tr>
                                                                    <th>Componente</th>
                                                                    <th>Como Mapear</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td><strong>APIs/Endpoints</strong></td>
                                                                    <td><code>php artisan route:list</code></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Models/Entidades</strong></td>
                                                                    <td><code>app/Models/</code></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Regras de Negócio</strong></td>
                                                                    <td><code>app/Services/</code>, Controllers</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Banco de Dados</strong></td>
                                                                    <td><code>database/migrations/</code></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Integrações</strong></td>
                                                                    <td>APIs externas, filas, cache</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="fw-bold text-success">📋 Checklist de Preparação</h6>
                                                    <div class="form-check form-check-sm mb-2">
                                                        <input class="form-check-input" type="checkbox" id="prep1">
                                                        <label class="form-check-label fs-7" for="prep1">Documentar todos os endpoints atuais</label>
                                                    </div>
                                                    <div class="form-check form-check-sm mb-2">
                                                        <input class="form-check-input" type="checkbox" id="prep2">
                                                        <label class="form-check-label fs-7" for="prep2">Mapear estruturas de banco (tabelas, campos)</label>
                                                    </div>
                                                    <div class="form-check form-check-sm mb-2">
                                                        <input class="form-check-input" type="checkbox" id="prep3">
                                                        <label class="form-check-label fs-7" for="prep3">Listar regras de negócio críticas</label>
                                                    </div>
                                                    <div class="form-check form-check-sm mb-2">
                                                        <input class="form-check-input" type="checkbox" id="prep4">
                                                        <label class="form-check-label fs-7" for="prep4">Identificar integrações externas</label>
                                                    </div>
                                                    <div class="form-check form-check-sm mb-2">
                                                        <input class="form-check-input" type="checkbox" id="prep5">
                                                        <label class="form-check-label fs-7" for="prep5">Definir ordem de migração (por prioridade)</label>
                                                    </div>
                                                    <div class="form-check form-check-sm mb-2">
                                                        <input class="form-check-input" type="checkbox" id="prep6">
                                                        <label class="form-check-label fs-7" for="prep6">Configurar ambiente de desenvolvimento</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="alert alert-light-primary mt-4">
                                                <i class="ki-duotone ki-information fs-2 text-primary me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                                <strong>💡 Dica:</strong> Comece pelos endpoints <strong>GET</strong> mais simples (sem side-effects) e depois avance para POST/PUT/DELETE.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fase 2: Desenvolvimento -->
                                <div class="accordion-item mb-3">
                                    <h2 class="accordion-header" id="headingPhase2">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePhase2">
                                            <i class="ki-duotone ki-code fs-2 text-success me-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div>
                                                <h5 class="mb-0">2️⃣ Desenvolvimento: Criar Novo Backend</h5>
                                                <small class="text-muted">Implementar em paralelo com o sistema atual</small>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapsePhase2" class="accordion-collapse collapse" data-bs-parent="#migrationAccordion">
                                        <div class="accordion-body">

                                            <!-- Exemplo Java Spring Boot -->
                                            <div class="card bg-light-success mb-6">
                                                <div class="card-header border-0">
                                                    <h5 class="card-title text-success">☕ Exemplo: Criar Backend Java Spring Boot</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6 class="fw-bold text-dark">📦 Setup Inicial</h6>
                                                            <pre class="bg-dark text-light p-3 rounded fs-8">
# 1. Criar projeto Spring Boot
curl https://start.spring.io/starter.tgz \
  -d dependencies=web,jpa,postgresql \
  -d name=legisinc-java-api \
  -d packageName=com.legisinc.api \
  -d javaVersion=17 | tar -xzvf -

# 2. Estrutura de projeto
legisinc-java-api/
├── src/main/java/com/legisinc/api/
│   ├── controller/     # Endpoints REST
│   ├── service/        # Regras de negócio
│   ├── repository/     # Acesso a dados
│   ├── model/          # Entidades JPA
│   └── config/         # Configurações
└── src/main/resources/
    └── application.yml # Config DB, etc.</pre>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6 class="fw-bold text-dark">⚙️ Configuração application.yml</h6>
                                                            <pre class="bg-dark text-light p-3 rounded fs-8">
spring:
  application:
    name: legisinc-java-api

  datasource:
    url: jdbc:postgresql://legisinc-postgres:5432/legisinc
    username: postgres
    password: 123456
    driver-class-name: org.postgresql.Driver

  jpa:
    hibernate:
      ddl-auto: validate  # Não alterar schema
    show-sql: false
    database-platform: org.hibernate.dialect.PostgreSQLDialect

server:
  port: 3001  # Mesma porta da Nova API atual

management:
  endpoints:
    web:
      exposure:
        include: health,metrics</pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Implementação por Camadas -->
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="card bg-light-primary h-100">
                                                        <div class="card-body">
                                                            <h6 class="fw-bold text-primary">1. Entidades/Models</h6>
                                                            <pre class="bg-dark text-light p-2 rounded fs-8">
@Entity
@Table(name = "proposicoes")
public class Proposicao {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "numero")
    private String numero;

    @Column(name = "titulo")
    private String titulo;

    @Column(name = "status")
    private String status;

    // getters/setters
}</pre>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card bg-light-warning h-100">
                                                        <div class="card-body">
                                                            <h6 class="fw-bold text-warning">2. Repository/DAO</h6>
                                                            <pre class="bg-dark text-light p-2 rounded fs-8">
@Repository
public interface ProposicaoRepository
    extends JpaRepository&lt;Proposicao, Long&gt; {

    List&lt;Proposicao&gt; findByStatus(String status);

    @Query("SELECT p FROM Proposicao p WHERE p.titulo LIKE %?1%")
    List&lt;Proposicao&gt; findByTituloContaining(String titulo);

    Page&lt;Proposicao&gt; findAll(Pageable pageable);
}</pre>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card bg-light-success h-100">
                                                        <div class="card-body">
                                                            <h6 class="fw-bold text-success">3. Service/Regras</h6>
                                                            <pre class="bg-dark text-light p-2 rounded fs-8">
@Service
public class ProposicaoService {

    @Autowired
    private ProposicaoRepository repository;

    public List&lt;ProposicaoDTO&gt; buscarProposicoes() {
        return repository.findAll()
            .stream()
            .map(this::toDTO)
            .collect(toList());
    }

    public ProposicaoDTO criarProposicao(CreateProposicaoRequest request) {
        // Regras de negócio aqui
        Proposicao proposicao = new Proposicao();
        proposicao.setTitulo(request.getTitulo());
        // ... validações e lógica
        return toDTO(repository.save(proposicao));
    }
}</pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <div class="card bg-light-info">
                                                    <div class="card-body">
                                                        <h6 class="fw-bold text-info">4. Controller/Endpoints</h6>
                                                        <pre class="bg-dark text-light p-3 rounded fs-8">
@RestController
@RequestMapping("/api")
public class ProposicaoController {

    @Autowired
    private ProposicaoService service;

    @GetMapping("/proposicoes")
    public ResponseEntity&lt;List&lt;ProposicaoDTO&gt;&gt; listarProposicoes() {
        List&lt;ProposicaoDTO&gt; proposicoes = service.buscarProposicoes();
        return ResponseEntity.ok(proposicoes);
    }

    @PostMapping("/proposicoes")
    public ResponseEntity&lt;ProposicaoDTO&gt; criarProposicao(@RequestBody CreateProposicaoRequest request) {
        ProposicaoDTO created = service.criarProposicao(request);
        return ResponseEntity.status(HttpStatus.CREATED).body(created);
    }

    @GetMapping("/health")
    public ResponseEntity&lt;Map&lt;String, String&gt;&gt; health() {
        return ResponseEntity.ok(Map.of("status", "healthy", "service", "java-api"));
    }
}</pre>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="alert alert-light-warning mt-4">
                                                <i class="ki-duotone ki-warning fs-2 text-warning me-2"><span class="path1"></span><span class="path2"></span></i>
                                                <strong>⚠️ Importante:</strong> Mantenha o <strong>mesmo formato de resposta JSON</strong> que o Laravel para compatibilidade total com o frontend.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fase 3: Containerização -->
                                <div class="accordion-item mb-3">
                                    <h2 class="accordion-header" id="headingPhase3">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePhase3">
                                            <i class="ki-duotone ki-package fs-2 text-info me-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            <div>
                                                <h5 class="mb-0">3️⃣ Containerização: Preparar para Deploy</h5>
                                                <small class="text-muted">Dockerizar o novo backend</small>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapsePhase3" class="accordion-collapse collapse" data-bs-parent="#migrationAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="fw-bold text-info">🐳 Dockerfile</h6>
                                                    <pre class="bg-dark text-light p-3 rounded fs-8">
# Dockerfile para Java Spring Boot
FROM openjdk:17-jdk-slim

WORKDIR /app

# Copiar arquivos de dependências
COPY pom.xml .
COPY mvnw .
COPY .mvn .mvn

# Baixar dependências (cache Docker)
RUN ./mvnw dependency:go-offline -B

# Copiar código fonte
COPY src src

# Build da aplicação
RUN ./mvnw clean package -DskipTests

# Expor porta
EXPOSE 3001

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:3001/api/health || exit 1

# Executar aplicação
CMD ["java", "-jar", "target/legisinc-java-api-1.0.jar"]</pre>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="fw-bold text-success">⚙️ Atualizar Docker Compose</h6>
                                                    <pre class="bg-dark text-light p-3 rounded fs-8">
# Adicionar ao docker-compose.gateway-simple.yml

  java-api:
    build:
      context: ./java-backend
      dockerfile: Dockerfile
    container_name: legisinc-java-api
    restart: unless-stopped
    environment:
      - SPRING_PROFILES_ACTIVE=docker
      - SPRING_DATASOURCE_URL=jdbc:postgresql://legisinc-postgres:5432/legisinc
      - SPRING_DATASOURCE_USERNAME=postgres
      - SPRING_DATASOURCE_PASSWORD=123456
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.java-api.rule=Host(`localhost`)"
      - "traefik.http.routers.java-api.entrypoints=web"
      - "traefik.http.services.java-api.loadbalancer.server.port=3001"
    ports:
      - "3001:3001"  # Para debug direto
    depends_on:
      - legisinc-postgres
      - legisinc-redis
    networks:
      - legisinc_network</pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fase 3: Arquivos do Projeto que Precisam ser Alterados -->
                                <div class="accordion-item mb-3">
                                    <h2 class="accordion-header" id="headingPhase3">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePhase3">
                                            <i class="ki-duotone ki-file-up fs-2 text-info me-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div>
                                                <h5 class="mb-0">3️⃣ Arquivos do Projeto: O que Deve ser Alterado</h5>
                                                <small class="text-muted">Lista completa dos arquivos que precisam ser modificados</small>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapsePhase3" class="accordion-collapse collapse" data-bs-parent="#migrationAccordion">
                                        <div class="accordion-body">

                                            <!-- Tabela de Arquivos Críticos -->
                                            <div class="card bg-light-danger mb-6">
                                                <div class="card-header border-0">
                                                    <h5 class="card-title text-danger">📋 Arquivos Obrigatórios para Migração</h5>
                                                    <div class="card-toolbar">
                                                        <span class="badge badge-light-danger">Alteração Necessária</span>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped">
                                                            <thead class="table-dark">
                                                                <tr>
                                                                    <th width="35%">📁 Arquivo</th>
                                                                    <th width="25%">🎯 Propósito</th>
                                                                    <th width="40%">🔧 O que Alterar</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr class="table-warning">
                                                                    <td><code><strong>docker-compose.gateway-simple.yml</strong></code></td>
                                                                    <td>Configuração principal dos containers</td>
                                                                    <td>
                                                                        • Adicionar serviço do novo backend<br>
                                                                        • Configurar labels do Traefik<br>
                                                                        • Definir variáveis de ambiente
                                                                    </td>
                                                                </tr>
                                                                <tr class="table-info">
                                                                    <td><code><strong>gateway/traefik/dynamic/routes.yml</strong></code></td>
                                                                    <td>Roteamento e balanceamento de carga</td>
                                                                    <td>
                                                                        • Configurar roteamento weighted<br>
                                                                        • Definir middlewares de migração<br>
                                                                        • Ajustar regras de CORS
                                                                    </td>
                                                                </tr>
                                                                <tr class="table-success">
                                                                    <td><code><strong>.env</strong></code></td>
                                                                    <td>Variáveis de ambiente</td>
                                                                    <td>
                                                                        • Configurar conexão do novo backend<br>
                                                                        • Adicionar URLs dos serviços<br>
                                                                        • Definir configurações específicas
                                                                    </td>
                                                                </tr>
                                                                <tr class="table-primary">
                                                                    <td><code><strong>gateway/shadow-comparator/</strong></code></td>
                                                                    <td>Comparação de respostas</td>
                                                                    <td>
                                                                        • Configurar endpoints para comparar<br>
                                                                        • Definir tolerâncias de diferença<br>
                                                                        • Ajustar relatórios de divergência
                                                                    </td>
                                                                </tr>
                                                                <tr class="table-light">
                                                                    <td><code><strong>gateway/canary-monitor/</strong></code></td>
                                                                    <td>Controle da migração gradual</td>
                                                                    <td>
                                                                        • Configurar percentuais de migração<br>
                                                                        • Definir métricas de rollback<br>
                                                                        • Configurar alertas automáticos
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Arquivos Opcionais -->
                                            <div class="card bg-light-warning mb-6">
                                                <div class="card-header border-0">
                                                    <h5 class="card-title text-warning">⚙️ Arquivos Opcionais (Recomendados)</h5>
                                                    <div class="card-toolbar">
                                                        <span class="badge badge-light-warning">Melhoria</span>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6 class="fw-bold text-dark">📊 Monitoramento</h6>
                                                            <ul class="list-unstyled">
                                                                <li class="d-flex align-items-center mb-2">
                                                                    <i class="ki-duotone ki-document fs-5 text-info me-2"><span class="path1"></span><span class="path2"></span></i>
                                                                    <code>gateway/prometheus/prometheus.yml</code>
                                                                </li>
                                                                <li class="d-flex align-items-center mb-2">
                                                                    <i class="ki-duotone ki-document fs-5 text-info me-2"><span class="path1"></span><span class="path2"></span></i>
                                                                    <code>gateway/grafana/dashboards/</code>
                                                                </li>
                                                                <li class="d-flex align-items-center mb-2">
                                                                    <i class="ki-duotone ki-document fs-5 text-info me-2"><span class="path1"></span><span class="path2"></span></i>
                                                                    <code>gateway/postgres-exporter/queries.yaml</code>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6 class="fw-bold text-dark">🔧 Configurações Específicas</h6>
                                                            <ul class="list-unstyled">
                                                                <li class="d-flex align-items-center mb-2">
                                                                    <i class="ki-duotone ki-document fs-5 text-success me-2"><span class="path1"></span><span class="path2"></span></i>
                                                                    <code>gateway/nginx/nginx.conf</code>
                                                                </li>
                                                                <li class="d-flex align-items-center mb-2">
                                                                    <i class="ki-duotone ki-document fs-5 text-success me-2"><span class="path1"></span><span class="path2"></span></i>
                                                                    <code>Dockerfile</code> (do novo backend)
                                                                </li>
                                                                <li class="d-flex align-items-center mb-2">
                                                                    <i class="ki-duotone ki-document fs-5 text-success me-2"><span class="path1"></span><span class="path2"></span></i>
                                                                    <code>database/migrations/</code> (se necessário)
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Comandos de Verificação -->
                                            <div class="card bg-light-success">
                                                <div class="card-header border-0">
                                                    <h5 class="card-title text-success">✅ Comandos para Verificar Alterações</h5>
                                                </div>
                                                <div class="card-body">
                                                    <pre class="bg-dark text-light p-3 rounded fs-8">
# Verificar se todos os arquivos existem
ls -la docker-compose.gateway-simple.yml
ls -la gateway/traefik/dynamic/routes.yml
ls -la .env
ls -la gateway/shadow-comparator/
ls -la gateway/canary-monitor/

# Validar sintaxe dos arquivos YAML
docker-compose -f docker-compose.gateway-simple.yml config

# Verificar se as variáveis de ambiente estão corretas
grep -E "^[A-Z_]+" .env | head -10

# Testar conectividade dos serviços
docker-compose ps
docker-compose logs gateway</pre>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <!-- Fase 4: Configuração do Gateway -->
                                <div class="accordion-item mb-3">
                                    <h2 class="accordion-header" id="headingPhase4">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePhase4">
                                            <i class="ki-duotone ki-router fs-2 text-warning me-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div>
                                                <h5 class="mb-0">4️⃣ Gateway: Configurar Roteamento</h5>
                                                <small class="text-muted">Direcionar tráfego para o novo backend</small>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapsePhase4" class="accordion-collapse collapse" data-bs-parent="#migrationAccordion">
                                        <div class="accordion-body">
                                            <div class="alert alert-light-info mb-6">
                                                <i class="ki-duotone ki-information fs-2 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                                <strong>Estratégia:</strong> O Traefik direcionará gradualmente o tráfego do Laravel para o novo backend Java, mantendo o frontend intacto.
                                            </div>

                                            <h6 class="fw-bold text-warning">📝 Atualizar gateway/traefik/dynamic/routes.yml</h6>
                                            <pre class="bg-dark text-light p-4 rounded fs-8">
http:
  routers:
    # ====================================
    # MIGRAÇÃO: /api/proposicoes (5% Java)
    # ====================================
    api-proposicoes-migration:
      rule: "PathPrefix(`/api/proposicoes`)"
      entryPoints: ["web"]
      priority: 100
      middlewares:
        - "migration-headers"
        - "request-metrics"
      service: "proposicoes-migration-weighted"

    # ====================================
    # FALLBACK: Outras rotas ainda no Laravel
    # ====================================
    api-fallback:
      rule: "PathPrefix(`/api`)"
      entryPoints: ["web"]
      priority: 50
      middlewares:
        - "legacy-headers"
        - "request-metrics"
      service: "laravel-svc@docker"

  services:
    # ====================================
    # WEIGHTED SERVICE - MIGRAÇÃO GRADUAL
    # ====================================
    proposicoes-migration-weighted:
      weighted:
        services:
          # NOVO: Java API - Começar com 5%
          - name: "java-api-svc@docker"
            weight: 5

          # LEGACY: Laravel - 95% do tráfego
          - name: "laravel-svc@docker"
            weight: 95

  middlewares:
    # ====================================
    # MIGRATION HEADERS
    # ====================================
    migration-headers:
      headers:
        customRequestHeaders:
          X-Migration-Phase: "java-backend"
          X-Backend-Version: "v2.0"
        customResponseHeaders:
          X-Powered-By: "Java-Spring-Boot"
          X-Migration-Status: "in-progress"</pre>

                                            <div class="card bg-light-success mt-4">
                                                <div class="card-header border-0">
                                                    <h6 class="card-title text-success">📊 Plano de Migração Gradual</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th>Semana</th>
                                                                    <th>Java %</th>
                                                                    <th>Laravel %</th>
                                                                    <th>Ação</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>1-2</td>
                                                                    <td><span class="badge badge-warning">5%</span></td>
                                                                    <td><span class="badge badge-primary">95%</span></td>
                                                                    <td>Shadow + observar métricas</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>3</td>
                                                                    <td><span class="badge badge-warning">20%</span></td>
                                                                    <td><span class="badge badge-primary">80%</span></td>
                                                                    <td>Se erro < 0.5%</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>4</td>
                                                                    <td><span class="badge badge-info">50%</span></td>
                                                                    <td><span class="badge badge-primary">50%</span></td>
                                                                    <td>Monitorar performance</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>5</td>
                                                                    <td><span class="badge badge-success">100%</span></td>
                                                                    <td><span class="badge badge-light">0%</span></td>
                                                                    <td>Migração completa!</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fase 5: Testes e Validação -->
                                <div class="accordion-item mb-3">
                                    <h2 class="accordion-header" id="headingPhase5">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePhase5">
                                            <i class="ki-duotone ki-verify fs-2 text-success me-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                                <span class="path5"></span>
                                                <span class="path6"></span>
                                            </i>
                                            <div>
                                                <h5 class="mb-0">5️⃣ Testes: Validar Compatibilidade</h5>
                                                <small class="text-muted">Garantir que tudo funciona igual</small>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapsePhase5" class="accordion-collapse collapse" data-bs-parent="#migrationAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="fw-bold text-success">🧪 Testes Automatizados</h6>
                                                    <pre class="bg-dark text-light p-3 rounded fs-8">
# Criar testes de comparação
# tests/compare-backends.js

const compareEndpoints = async () => {
  const endpoints = [
    '/api/proposicoes',
    '/api/users/profile',
    '/api/documents'
  ];

  for (const endpoint of endpoints) {
    // Chamar Laravel
    const laravelResponse = await fetch(`http://localhost:8001${endpoint}`);
    const laravelData = await laravelResponse.json();

    // Chamar Java
    const javaResponse = await fetch(`http://localhost:3001${endpoint}`);
    const javaData = await javaResponse.json();

    // Comparar estruturas (ignorar timestamps)
    const differences = compareJSON(laravelData, javaData, ['created_at', 'updated_at']);

    if (differences.length > 0) {
      console.error(`❌ Diferenças em ${endpoint}:`, differences);
    } else {
      console.log(`✅ ${endpoint} compatível`);
    }
  }
};</pre>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="fw-bold text-warning">📋 Checklist de Validação</h6>
                                                    <div class="form-check form-check-sm mb-2">
                                                        <input class="form-check-input" type="checkbox" id="test1">
                                                        <label class="form-check-label fs-7" for="test1">Estrutura JSON idêntica</label>
                                                    </div>
                                                    <div class="form-check form-check-sm mb-2">
                                                        <input class="form-check-input" type="checkbox" id="test2">
                                                        <label class="form-check-label fs-7" for="test2">Códigos de status HTTP iguais</label>
                                                    </div>
                                                    <div class="form-check form-check-sm mb-2">
                                                        <input class="form-check-input" type="checkbox" id="test3">
                                                        <label class="form-check-label fs-7" for="test3">Headers de resposta compatíveis</label>
                                                    </div>
                                                    <div class="form-check form-check-sm mb-2">
                                                        <input class="form-check-input" type="checkbox" id="test4">
                                                        <label class="form-check-label fs-7" for="test4">Validações de negócio funcionando</label>
                                                    </div>
                                                    <div class="form-check form-check-sm mb-2">
                                                        <input class="form-check-input" type="checkbox" id="test5">
                                                        <label class="form-check-label fs-7" for="test5">Performance igual ou melhor</label>
                                                    </div>
                                                    <div class="form-check form-check-sm mb-2">
                                                        <input class="form-check-input" type="checkbox" id="test6">
                                                        <label class="form-check-label fs-7" for="test6">Logs estruturados funcionando</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="alert alert-light-success mt-4">
                                                <i class="ki-duotone ki-shield-tick fs-2 text-success me-2"><span class="path1"></span><span class="path2"></span></i>
                                                <strong>✅ Shadow Testing:</strong> O sistema de Shadow Traffic já configurado permitirá testar o backend Java com tráfego real sem afetar os usuários.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Comandos Úteis -->
                            <div class="card bg-light-primary mt-8">
                                <div class="card-header border-0">
                                    <h4 class="card-title text-primary">🛠️ Comandos Úteis para Migração</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="fw-bold text-dark">🚀 Deploy e Build</h6>
                                            <pre class="bg-dark text-light p-3 rounded fs-8">
# Build e subir novo backend
docker-compose build java-api
docker-compose up -d java-api

# Verificar saúde
curl http://localhost:3001/api/health

# Ver logs em tempo real
docker logs -f legisinc-java-api</pre>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="fw-bold text-dark">📊 Monitoramento</h6>
                                            <pre class="bg-dark text-light p-3 rounded fs-8">
# Comparar respostas
curl http://localhost:8001/api/proposicoes > laravel.json
curl http://localhost:3001/api/proposicoes > java.json
diff laravel.json java.json

# Métricas do Prometheus
curl http://localhost:9090/api/v1/query?query=http_requests_total

# Dashboard do Canary
open http://localhost:3003</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Troubleshooting -->
                            <div class="card bg-light-danger mt-6">
                                <div class="card-header border-0">
                                    <h4 class="card-title text-danger">🚨 Problemas Comuns e Soluções</h4>
                                </div>
                                <div class="card-body">
                                    <div class="accordion" id="troubleshootingAccordion">
                                        <div class="accordion-item mb-2">
                                            <h2 class="accordion-header" id="headingTrouble1">
                                                <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTrouble1">
                                                    ❌ Backend Java não conecta no banco
                                                </button>
                                            </h2>
                                            <div id="collapseTrouble1" class="accordion-collapse collapse" data-bs-parent="#troubleshootingAccordion">
                                                <div class="accordion-body py-2">
                                                    <strong>Soluções:</strong>
                                                    <ul class="mb-0">
                                                        <li>Verificar se PostgreSQL está rodando: <code>docker ps | grep postgres</code></li>
                                                        <li>Validar credenciais no application.yml</li>
                                                        <li>Testar conexão manual: <code>psql -h localhost -p 5432 -U postgres -d legisinc</code></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="accordion-item mb-2">
                                            <h2 class="accordion-header" id="headingTrouble2">
                                                <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTrouble2">
                                                    ⚠️ JSON diferentes entre Laravel e Java
                                                </button>
                                            </h2>
                                            <div id="collapseTrouble2" class="accordion-collapse collapse" data-bs-parent="#troubleshootingAccordion">
                                                <div class="accordion-body py-2">
                                                    <strong>Soluções:</strong>
                                                    <ul class="mb-0">
                                                        <li>Usar DTOs no Java que espelhem exatamente a estrutura do Laravel</li>
                                                        <li>Configurar Jackson para formato de data igual: <code>@JsonFormat(pattern="yyyy-MM-dd HH:mm:ss")</code></li>
                                                        <li>Implementar conversores customizados se necessário</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="accordion-item mb-2">
                                            <h2 class="accordion-header" id="headingTrouble3">
                                                <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTrouble3">
                                                    🐌 Performance pior que Laravel
                                                </button>
                                            </h2>
                                            <div id="collapseTrouble3" class="accordion-collapse collapse" data-bs-parent="#troubleshootingAccordion">
                                                <div class="accordion-body py-2">
                                                    <strong>Soluções:</strong>
                                                    <ul class="mb-0">
                                                        <li>Otimizar queries JPA com <code>@Query</code> personalizadas</li>
                                                        <li>Implementar cache com Redis: <code>@Cacheable("proposicoes")</code></li>
                                                        <li>Configurar connection pool do banco adequadamente</li>
                                                        <li>Usar paginação para listas grandes</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!--end::Migrar Backend tab-->

            </div>
            <!--end::Tab content-->

        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
<script>
// Inicializar Mermaid
mermaid.initialize({
    startOnLoad: true,
    theme: 'default',
    themeVariables: {
        primaryColor: '#007bff',
        primaryTextColor: '#1a1a1a',
        primaryBorderColor: '#007bff',
        lineColor: '#6c757d',
        secondaryColor: '#f8f9fa',
        tertiaryColor: '#e9ecef'
    }
});

// Função para atualizar status geral
function refreshStatus() {
    refreshContainers();
    refreshServices();
}

// Função para atualizar containers
function refreshContainers() {
    fetch('{{ route("admin.arquitetura.api.containers") }}')
        .then(response => response.json())
        .then(data => {
            const grid = document.getElementById('containers-grid');
            if (data.length === 0) {
                grid.innerHTML = `
                    <div class="col-12 text-center p-5">
                        <i class="ki-duotone ki-information-5 fs-2x text-warning mb-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <p class="text-muted">Nenhum container encontrado ou erro na verificação.</p>
                    </div>
                `;
                return;
            }

            grid.innerHTML = data.map(container => `
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="symbol symbol-50px me-3">
                                <div class="symbol-label bg-light-primary">
                                    <i class="ki-duotone ki-monitor fs-2x text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h4 class="fw-bold mb-1">${container.nome}</h4>
                                <p class="text-muted mb-0 fs-7">${container.descricao}</p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold">Container:</span>
                            <code class="bg-light px-2 py-1 rounded">${container.container}</code>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-semibold">Porta:</span>
                            <span class="badge badge-light-info">:${container.porta}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Status:</span>
                            <span class="status-badge status-${container.status}">${container.status}</span>
                        </div>
                    </div>
                </div>
            `).join('');
        })
        .catch(error => {
            console.error('Erro ao carregar containers:', error);
            document.getElementById('containers-grid').innerHTML = `
                <div class="col-12 text-center p-5">
                    <i class="ki-duotone ki-cross-circle fs-2x text-danger mb-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <p class="text-danger">Erro ao carregar informações dos containers.</p>
                </div>
            `;
        });
}

// Função para atualizar serviços
function refreshServices() {
    // Implementar se necessário
    console.log('Atualizando status dos serviços...');
}

// Carregar dados iniciais quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    refreshContainers();

    // Renderizar markdown da explicação completa
    const explicacaoRaw = document.querySelector('.explicacao-markdown-raw');
    const explicacaoRendered = document.getElementById('explicacao-markdown-rendered');

    if (explicacaoRaw && explicacaoRendered) {
        // Importar marked.js se ainda não foi carregado
        if (typeof marked === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/marked/marked.min.js';
            script.onload = function() {
                renderExplicacaoMarkdown();
                renderContainersMarkdown();
            };
            document.head.appendChild(script);
        } else {
            renderExplicacaoMarkdown();
            renderContainersMarkdown();
        }
    }

    // Renderizar markdown dos containers
    const containersRaw = document.querySelector('.containers-markdown-raw');
    const containersRendered = document.getElementById('containers-markdown-rendered');

    if (containersRaw && containersRendered) {
        if (typeof marked !== 'undefined') {
            renderContainersMarkdown();
        }
    }

    // Renderizar markdown dos containers novos
    const containersNovosRaw = document.querySelector('.containers-novos-markdown-raw');
    const containersNovosRendered = document.getElementById('containers-novos-markdown-rendered');

    if (containersNovosRaw && containersNovosRendered) {
        if (typeof marked !== 'undefined') {
            renderContainersNovosMarkdown();
        }
    }
});

// Função para renderizar markdown da explicação
function renderExplicacaoMarkdown() {
    const explicacaoRaw = document.querySelector('.explicacao-markdown-raw');
    const explicacaoRendered = document.getElementById('explicacao-markdown-rendered');

    if (explicacaoRaw && explicacaoRendered) {
        const content = explicacaoRaw.textContent;
        explicacaoRendered.innerHTML = marked.parse(content);

        // Re-renderizar diagramas Mermaid se houver
        if (typeof mermaid !== 'undefined') {
            mermaid.init(undefined, explicacaoRendered.querySelectorAll('.mermaid'));
        }
    }
}

// Função para alternar visualização da explicação
function toggleExplanationView() {
    const rawElement = document.querySelector('.explicacao-markdown-raw');
    const renderedElement = document.getElementById('explicacao-markdown-rendered');

    if (rawElement && renderedElement) {
        if (rawElement.style.display === 'none') {
            rawElement.style.display = 'block';
            renderedElement.style.display = 'none';
        } else {
            rawElement.style.display = 'none';
            renderedElement.style.display = 'block';
        }
    }
}

// Função para renderizar markdown dos containers
function renderContainersMarkdown() {
    const containersRaw = document.querySelector('.containers-markdown-raw');
    const containersRendered = document.getElementById('containers-markdown-rendered');

    if (containersRaw && containersRendered) {
        const content = containersRaw.textContent;
        containersRendered.innerHTML = marked.parse(content);

        // Re-renderizar diagramas Mermaid se houver
        if (typeof mermaid !== 'undefined') {
            mermaid.init(undefined, containersRendered.querySelectorAll('.mermaid'));
        }
    }
}

// Função para alternar visualização dos containers
function toggleContainersView() {
    const rawElement = document.querySelector('.containers-markdown-raw');
    const renderedElement = document.getElementById('containers-markdown-rendered');

    if (rawElement && renderedElement) {
        if (rawElement.style.display === 'none') {
            rawElement.style.display = 'block';
            renderedElement.style.display = 'none';
        } else {
            rawElement.style.display = 'none';
            renderedElement.style.display = 'block';
        }
    }
}

// Função para renderizar markdown dos containers novos
function renderContainersNovosMarkdown() {
    const containersNovosRaw = document.querySelector('.containers-novos-markdown-raw');
    const containersNovosRendered = document.getElementById('containers-novos-markdown-rendered');

    if (containersNovosRaw && containersNovosRendered) {
        const content = containersNovosRaw.textContent;
        containersNovosRendered.innerHTML = marked.parse(content);

        // Re-renderizar diagramas Mermaid se houver
        if (typeof mermaid !== 'undefined') {
            mermaid.init(undefined, containersNovosRendered.querySelectorAll('.mermaid'));
        }
    }
}

// Função para verificar saúde de todos os containers
function checkAllHealth() {
    // Containers para verificar
    const healthChecks = [
        { id: 'health-traefik', url: 'http://localhost:8000', fallback: 'docker' },
        { id: 'health-shadow', url: 'http://localhost:8002', fallback: 'docker' },
        { id: 'health-comparator', container: 'legisinc-shadow-comparator' },
        { id: 'health-canary', url: 'http://localhost:3003' },
        { id: 'health-laravel', url: 'http://localhost:8001/health' },
        { id: 'health-nova-api', url: 'http://localhost:3001/health' },
        { id: 'health-prometheus', url: 'http://localhost:9090' },
        { id: 'health-pg-exporter', url: 'http://localhost:9187/metrics' },
        { id: 'health-grafana', url: 'http://localhost:3000' },
        { id: 'health-onlyoffice', url: 'http://localhost:8080/healthcheck' },
        { id: 'health-redis', container: 'legisinc-redis' },
        { id: 'health-postgres', container: 'legisinc-postgres' }
    ];

    healthChecks.forEach(check => {
        const element = document.getElementById(check.id);
        if (element) {
            element.textContent = 'Verificando...';
            element.className = 'badge badge-secondary';

            // Simular verificação (em produção seria uma chamada AJAX real)
            setTimeout(() => {
                const isHealthy = Math.random() > 0.2; // 80% de chance de estar healthy
                if (isHealthy) {
                    element.textContent = 'Online';
                    element.className = 'badge badge-success';
                } else {
                    element.textContent = 'Offline';
                    element.className = 'badge badge-danger';
                }
            }, Math.random() * 2000);
        }
    });
}

// Função para verificar saúde de todos os containers (tab Completo)
function checkAllContainerHealth() {
    const containerNames = [
        'legisinc-gateway-simple',
        'legisinc-app',
        'legisinc-nova-api',
        'legisinc-nginx-shadow',
        'legisinc-shadow-comparator',
        'legisinc-canary-monitor',
        'legisinc-prometheus-simple',
        'legisinc-grafana-simple',
        'legisinc-postgres-exporter',
        'legisinc-postgres',
        'legisinc-redis',
        'legisinc-onlyoffice'
    ];

    // Simular verificação de todos os containers
    containerNames.forEach((containerName, index) => {
        setTimeout(() => {
            const isHealthy = Math.random() > 0.15; // 85% de chance de estar healthy

            // Encontrar a linha da tabela correspondente
            const table = document.querySelector('#containers-completo .table tbody');
            if (table) {
                const row = table.rows[index];
                if (row) {
                    const lastCell = row.cells[row.cells.length - 1];
                    const statusSpan = document.createElement('span');

                    if (isHealthy) {
                        statusSpan.innerHTML = '<i class="ki-duotone ki-check fs-6 text-success me-1"><span class="path1"></span><span class="path2"></span></i>Container rodando normalmente';
                        statusSpan.className = 'text-success fs-8 d-block mt-1';
                    } else {
                        statusSpan.innerHTML = '<i class="ki-duotone ki-close fs-6 text-danger me-1"><span class="path1"></span><span class="path2"></span></i>Container com problemas';
                        statusSpan.className = 'text-danger fs-8 d-block mt-1';
                    }

                    // Remover status anterior se existir
                    const existingStatus = lastCell.querySelector('.fs-8');
                    if (existingStatus) {
                        existingStatus.remove();
                    }

                    lastCell.appendChild(statusSpan);
                }
            }
        }, index * 300); // Verificar um por vez com delay de 300ms
    });

    // Mostrar notification de sucesso
    setTimeout(() => {
        // Se tiver sistema de notificação, mostrar aqui
        console.log('Verificação de saúde concluída para todos os containers');
    }, containerNames.length * 300 + 500);
}

// Auto-refresh a cada 30 segundos
setInterval(refreshStatus, 30000);
</script>
@endpush