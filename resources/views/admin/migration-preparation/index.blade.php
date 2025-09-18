@extends('components.layouts.app')

@section('title', 'Preparação para Migração de Backend')

@push('styles')
<style>
.card-hover {
    transition: all 0.3s ease;
    cursor: pointer;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
}

.task-card {
    border: 1px solid #e1e3ea;
    border-radius: 0.75rem;
    transition: all 0.3s ease;
}

.task-card:hover {
    border-color: #009ef7;
    box-shadow: 0 0 0 1px rgba(0, 158, 247, 0.1);
}

.task-card.completed {
    border-color: #50cd89;
    background-color: #f8fff9;
}

.progress-circle {
    width: 60px;
    height: 60px;
}

.progress-text {
    font-size: 12px;
    font-weight: 600;
}

.generation-result {
    display: none;
    background-color: #f5f8fa;
    border-radius: 0.75rem;
    border: 1px solid #e1e3ea;
}

.generation-result.show {
    display: block;
}

.json-preview {
    max-height: 250px;
    overflow-y: auto;
    font-family: 'SFMono-Regular', Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
    font-size: 0.8rem;
    line-height: 1.4;
}

.stat-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border: 1px solid #e1e3ea;
    border-radius: 0.75rem;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.btn-generate {
    border-radius: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border: none;
    transition: all 0.3s ease;
}

.btn-generate:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

.section-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent 0%, #e1e3ea 50%, transparent 100%);
    margin: 3rem 0;
}

.icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.checklist-item {
    margin-bottom: 1rem;
}

.checklist-item.completed {
    opacity: 0.7;
}

.checklist-item.completed .task-card {
    border-color: #50cd89 !important;
    background: linear-gradient(135deg, #f8fff9 0%, #ffffff 100%);
}

.cursor-pointer {
    cursor: pointer;
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
                    Preparação para Migração
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Admin</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Preparação para Migração</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-light">
                    <i class="ki-duotone ki-arrow-left fs-4 me-1">
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

            <!-- Introdução -->
            <div class="card mb-6">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-light-primary me-4">
                            <i class="ki-duotone ki-switch fs-2 text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="fw-bold text-gray-900 mb-2">Gerar Dados para Migração de Backend</h3>
                            <p class="text-muted fs-6 mb-0">
                                Extraia todas as informações do sistema Laravel para facilitar a migração para qualquer outra linguagem (Java, Node.js, Python, Go).
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas Rápidas -->
            <div class="row g-5 mb-6">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card p-4 text-center">
                        <div class="icon-circle bg-light-primary mx-auto mb-3">
                            <i class="ki-duotone ki-router fs-2 text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <div class="fw-bold text-gray-900 fs-2 mb-1">{{ $data['endpoints_count'] }}</div>
                        <div class="text-muted fs-7">Endpoints</div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card p-4 text-center">
                        <div class="icon-circle bg-light-success mx-auto mb-3">
                            <i class="ki-duotone ki-code fs-2 text-success">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <div class="fw-bold text-gray-900 fs-2 mb-1">{{ $data['models_count'] }}</div>
                        <div class="text-muted fs-7">Models</div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card p-4 text-center">
                        <div class="icon-circle bg-light-warning mx-auto mb-3">
                            <i class="ki-duotone ki-data fs-2 text-warning">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <div class="fw-bold text-gray-900 fs-2 mb-1">{{ $data['tables_count'] }}</div>
                        <div class="text-muted fs-7">Tabelas</div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card p-4 text-center">
                        <div class="icon-circle bg-light-info mx-auto mb-3">
                            <i class="ki-duotone ki-files fs-2 text-info">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <div class="fw-bold text-gray-900 fs-2 mb-1">{{ $data['migrations_count'] }}</div>
                        <div class="text-muted fs-7">Migrations</div>
                    </div>
                </div>
            </div>

            <!-- Checklist de Preparação -->
            <div class="card mb-6">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-light-info me-3">
                            <i class="ki-duotone ki-check-circle fs-2 text-info">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="fw-bold text-gray-900 mb-1">Checklist de Preparação</h3>
                            <p class="text-muted fs-7 mb-0">Acompanhe o progresso da preparação para migração</p>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold fs-4 text-gray-900" id="progress-text">0%</div>
                            <div class="text-muted fs-7">0 de 4 itens</div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="task-card p-4" data-task="endpoints">
                                <div class="d-flex align-items-start">
                                    <div class="form-check form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" id="task-endpoints" />
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="fw-bold text-gray-900 fs-6 cursor-pointer" for="task-endpoints">
                                            Documentar Endpoints
                                        </label>
                                        <div class="text-muted fs-7 mt-1">Mapear todas as rotas e APIs do sistema</div>
                                    </div>
                                    <div class="icon-circle bg-light-primary">
                                        <i class="ki-duotone ki-router fs-5 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="task-card p-4" data-task="database">
                                <div class="d-flex align-items-start">
                                    <div class="form-check form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" id="task-database" />
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="fw-bold text-gray-900 fs-6 cursor-pointer" for="task-database">
                                            Mapear Estrutura do Banco
                                        </label>
                                        <div class="text-muted fs-7 mt-1">Extrair esquema completo do banco de dados</div>
                                    </div>
                                    <div class="icon-circle bg-light-warning">
                                        <i class="ki-duotone ki-data fs-5 text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="task-card p-4" data-task="models">
                                <div class="d-flex align-items-start">
                                    <div class="form-check form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" id="task-models" />
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="fw-bold text-gray-900 fs-6 cursor-pointer" for="task-models">
                                            Listar Regras de Negócio
                                        </label>
                                        <div class="text-muted fs-7 mt-1">Identificar modelos e suas relações</div>
                                    </div>
                                    <div class="icon-circle bg-light-success">
                                        <i class="ki-duotone ki-code fs-5 text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="task-card p-4" data-task="integrations">
                                <div class="d-flex align-items-start">
                                    <div class="form-check form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" id="task-integrations" />
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="fw-bold text-gray-900 fs-6 cursor-pointer" for="task-integrations">
                                            Identificar Integrações
                                        </label>
                                        <div class="text-muted fs-7 mt-1">APIs externas, filas, cache e serviços</div>
                                    </div>
                                    <div class="icon-circle bg-light-info">
                                        <i class="ki-duotone ki-connect fs-5 text-info">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-divider"></div>

            <!-- Ações de Geração -->
            <div class="row g-6 mb-6">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-light-primary me-3">
                                    <i class="ki-duotone ki-document fs-2 text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                                <div>
                                    <h3 class="fw-bold text-gray-900 mb-1">Geração de JSONs</h3>
                                    <p class="text-muted fs-7 mb-0">Gere arquivos específicos para cada componente do sistema</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <button class="btn btn-generate btn-light-primary w-100" onclick="generateComponent('endpoints')">
                                        <i class="ki-duotone ki-router fs-4 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Endpoints
                                    </button>
                                </div>
                                <div class="col-sm-6">
                                    <button class="btn btn-generate btn-light-warning w-100" onclick="generateComponent('database')">
                                        <i class="ki-duotone ki-data fs-4 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Banco de Dados
                                    </button>
                                </div>
                                <div class="col-sm-6">
                                    <button class="btn btn-generate btn-light-success w-100" onclick="generateComponent('models')">
                                        <i class="ki-duotone ki-code fs-4 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Models & Regras
                                    </button>
                                </div>
                                <div class="col-sm-6">
                                    <button class="btn btn-generate btn-light-info w-100" onclick="generateComponent('integrations')">
                                        <i class="ki-duotone ki-connect fs-4 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Integrações
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card h-100" style="background: linear-gradient(135deg, #f8fff9 0%, #e8f5e8 100%); border: 1px solid #50cd89;">
                        <div class="card-body d-flex flex-column text-center">
                            <div class="icon-circle bg-success mx-auto mb-4">
                                <i class="ki-duotone ki-file-down fs-2 text-white">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <h4 class="fw-bold text-gray-900 mb-3">JSON Completo</h4>
                            <p class="text-gray-600 fs-7 mb-4 flex-grow-1">
                                Gera um arquivo único com <strong>todos os componentes</strong> para migração completa.
                            </p>
                            <button class="btn btn-success btn-generate" onclick="generateCompleteJson()">
                                <i class="ki-duotone ki-download fs-4 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Gerar Completo
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status de Geração -->
            <div class="generation-status" id="generation-status" style="display: none;">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-light-success me-3">
                                    <i class="ki-duotone ki-chart-simple fs-2 text-success">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </div>
                                <div>
                                    <h3 class="fw-bold text-gray-900 mb-1">Resultado da Geração</h3>
                                    <p class="text-muted fs-7 mb-0">Visualize e baixe os arquivos JSON gerados</p>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-light-danger" onclick="clearOutput()">
                                <i class="ki-duotone ki-trash fs-5 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                                Limpar
                            </button>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div id="generation-output" class="json-output">
                            <!-- Output será inserido aqui -->
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar progresso do checklist
    updateProgress();

    // Event listeners para checkboxes
    document.querySelectorAll('.checklist-item input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateChecklistItem(this);
            updateProgress();
        });
    });
});

function updateChecklistItem(checkbox) {
    const item = checkbox.closest('.checklist-item');
    if (checkbox.checked) {
        item.classList.add('completed');
    } else {
        item.classList.remove('completed');
    }
}

function updateProgress() {
    const total = document.querySelectorAll('.checklist-item input[type="checkbox"]').length;
    const completed = document.querySelectorAll('.checklist-item input[type="checkbox"]:checked').length;
    const percentage = Math.round((completed / total) * 100);

    // Atualizar texto
    document.getElementById('progress-text').textContent = `${percentage}%`;
    document.querySelector('.card-header .text-muted').textContent = `${completed} de ${total} itens`;
}

function generateComponent(component) {
    showLoading(`Gerando JSON de ${component}...`);

    const endpoints = {
        'endpoints': '/admin/migration-preparation/api/endpoints',
        'database': '/admin/migration-preparation/api/database',
        'models': '/admin/migration-preparation/api/models',
        'integrations': '/admin/migration-preparation/api/integrations'
    };

    fetch(endpoints[component])
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayResult(component, data);
                markTaskCompleted(component);
            } else {
                showError('Erro ao gerar JSON: ' + data.error);
            }
        })
        .catch(error => {
            showError('Erro na requisição: ' + error.message);
        });
}

function generateCompleteJson() {
    showLoading('Gerando JSON completo para migração...');

    fetch('/admin/migration-preparation/api/complete')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayResult('completo', data);
                markAllTasksCompleted();
                downloadJson(data.data, 'legisinc-migration-complete.json');
            } else {
                showError('Erro ao gerar JSON completo: ' + data.error);
            }
        })
        .catch(error => {
            showError('Erro na requisição: ' + error.message);
        });
}

function markTaskCompleted(component) {
    const checkbox = document.getElementById(`task-${component}`);
    if (checkbox && !checkbox.checked) {
        checkbox.checked = true;
        updateChecklistItem(checkbox);
        updateProgress();
    }
}

function markAllTasksCompleted() {
    document.querySelectorAll('.checklist-item input[type="checkbox"]').forEach(checkbox => {
        if (!checkbox.checked) {
            checkbox.checked = true;
            updateChecklistItem(checkbox);
        }
    });
    updateProgress();
}

function showLoading(message) {
    const statusDiv = document.getElementById('generation-status');
    const outputDiv = document.getElementById('generation-output');

    statusDiv.style.display = 'block';
    outputDiv.innerHTML = `
        <div class="d-flex align-items-center p-4 bg-light rounded">
            <div class="spinner-border spinner-border-sm text-primary me-3" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
            <span class="text-primary fw-semibold">${message}</span>
        </div>
    `;
}

function displayResult(component, data) {
    const outputDiv = document.getElementById('generation-output');
    const componentNames = {
        'endpoints': 'Endpoints',
        'database': 'Estrutura do Banco',
        'models': 'Models e Regras',
        'integrations': 'Integrações',
        'completo': 'JSON Completo'
    };

    outputDiv.innerHTML = `
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold text-success mb-0">✅ ${componentNames[component]} - Gerado com sucesso!</h6>
                <button class="btn btn-sm btn-primary" onclick="downloadJsonData('${component}', ${JSON.stringify(data.data).replace(/"/g, '&quot;')})">
                    <i class="ki-duotone ki-download fs-6 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Download JSON
                </button>
            </div>
            <div class="alert alert-light-success">
                <strong>Total de itens:</strong> ${data.total_routes || data.total_tables || data.total_models || 'N/A'}<br>
                <strong>Gerado em:</strong> ${new Date(data.generated_at || data.data?.generated_at).toLocaleString('pt-BR')}
            </div>
        </div>
        <pre class="bg-light p-3 rounded" style="max-height: 300px; overflow: auto; font-size: 0.8rem;">${JSON.stringify(data.data, null, 2)}</pre>
    `;
}

function showError(message) {
    const outputDiv = document.getElementById('generation-output');
    outputDiv.innerHTML = `
        <div class="alert alert-danger">
            <i class="ki-duotone ki-warning fs-3 text-danger me-2">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            <strong>Erro:</strong> ${message}
        </div>
    `;
}

function clearOutput() {
    const statusDiv = document.getElementById('generation-status');
    statusDiv.style.display = 'none';
}

function downloadJsonData(component, data) {
    const filename = `legisinc-${component}-${new Date().toISOString().split('T')[0]}.json`;
    downloadJson(data, filename);
}

function downloadJson(data, filename) {
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);

    // Mostrar notificação de sucesso
    Swal.fire({
        title: 'Download Iniciado!',
        text: `Arquivo ${filename} foi baixado com sucesso.`,
        icon: 'success',
        timer: 3000,
        showConfirmButton: false
    });
}
</script>
@endpush