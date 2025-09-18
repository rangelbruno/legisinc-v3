@extends('components.layouts.app')

@section('title', 'Logs do Sistema')

@section('content')

<style>
.dashboard-layer {
    border: 2px solid #e1e5e9;
    border-radius: 0.75rem;
    background: #ffffff;
    margin-bottom: 1.5rem;
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

.filter-section {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
    border: 1px solid #e1e5e9;
}

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

.log-entry {
    border-left: 4px solid #e1e5e9;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    border-radius: 0 0.25rem 0.25rem 0;
    background: #ffffff;
    transition: all 0.2s ease;
}

.log-entry:hover {
    background: #f8f9fa;
    border-left-color: #5e72e4;
}

.log-entry.level-emergency { border-left-color: #dc3545; background: #fff5f5; }
.log-entry.level-alert { border-left-color: #fd7e14; background: #fff8f0; }
.log-entry.level-critical { border-left-color: #dc3545; background: #fff5f5; }
.log-entry.level-error { border-left-color: #dc3545; background: #fff5f5; }
.log-entry.level-warning { border-left-color: #ffc107; background: #fffbf0; }
.log-entry.level-notice { border-left-color: #17a2b8; background: #f0f9ff; }
.log-entry.level-info { border-left-color: #28a745; background: #f0fff4; }
.log-entry.level-debug { border-left-color: #6c757d; background: #f8f9fa; }

.log-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.log-level {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.log-level.level-emergency { background: #dc3545; color: white; }
.log-level.level-alert { background: #fd7e14; color: white; }
.log-level.level-critical { background: #dc3545; color: white; }
.log-level.level-error { background: #dc3545; color: white; }
.log-level.level-warning { background: #ffc107; color: #212529; }
.log-level.level-notice { background: #17a2b8; color: white; }
.log-level.level-info { background: #28a745; color: white; }
.log-level.level-debug { background: #6c757d; color: white; }

.log-timestamp {
    font-size: 0.875rem;
    color: #6c757d;
    font-family: 'Courier New', monospace;
}

.log-message {
    font-size: 0.9rem;
    color: #495057;
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

.log-context {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 0.25rem;
    padding: 0.5rem;
    font-family: 'Courier New', monospace;
    font-size: 0.8rem;
    max-height: 200px;
    overflow-y: auto;
    color: #495057;
}

.log-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 0.5rem;
}

.log-meta span {
    background: #e9ecef;
    padding: 0.2rem 0.4rem;
    border-radius: 0.2rem;
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
}

.no-logs {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
}

.no-logs i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.btn-expand {
    background: none;
    border: none;
    color: #5e72e4;
    font-size: 0.8rem;
    cursor: pointer;
    padding: 0;
}

.btn-expand:hover {
    text-decoration: underline;
}

.highlight {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 0.25rem;
    padding: 0.1rem 0.2rem;
}
</style>

<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    📊 Logs do Sistema
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Admin</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.monitoring.index') }}" class="text-muted text-hover-primary">Monitoramento</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Logs</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2">
                <!-- Botão de Auto-refresh -->
                <button type="button" id="auto-refresh-btn" class="btn btn-success btn-sm" onclick="toggleAutoRefresh()">
                    <i class="fas fa-play"></i> Auto-atualizar
                </button>

                <!-- Botão de Limpar Logs -->
                @if(auth()->user()->hasRole('ADMIN'))
                    <button type="button" class="btn btn-danger btn-sm" onclick="showClearLogsModal()">
                        <i class="fas fa-trash"></i> Limpar Logs
                    </button>
                @endif
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!-- Filtros -->
            <div class="dashboard-layer">
                <div class="layer-header">
                    🔍 Filtros de Logs
                </div>
                <div class="layer-content">
                    <form method="GET" action="{{ route('admin.monitoring.logs') }}" class="filter-section">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Nível do Log</label>
                                <select name="level" class="form-select form-select-sm">
                                    <option value="">Todos os níveis</option>
                                    <option value="emergency" {{ request('level') == 'emergency' ? 'selected' : '' }}>🚨 Emergency</option>
                                    <option value="alert" {{ request('level') == 'alert' ? 'selected' : '' }}>🔔 Alert</option>
                                    <option value="critical" {{ request('level') == 'critical' ? 'selected' : '' }}>💥 Critical</option>
                                    <option value="error" {{ request('level') == 'error' ? 'selected' : '' }}>❌ Error</option>
                                    <option value="warning" {{ request('level') == 'warning' ? 'selected' : '' }}>⚠️ Warning</option>
                                    <option value="notice" {{ request('level') == 'notice' ? 'selected' : '' }}>📢 Notice</option>
                                    <option value="info" {{ request('level') == 'info' ? 'selected' : '' }}>ℹ️ Info</option>
                                    <option value="debug" {{ request('level') == 'debug' ? 'selected' : '' }}>🐛 Debug</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Request ID</label>
                                <input type="text" name="request_id" class="form-control form-control-sm"
                                       value="{{ request('request_id') }}" placeholder="Ex: abc123">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">User ID</label>
                                <input type="number" name="user_id" class="form-control form-control-sm"
                                       value="{{ request('user_id') }}" placeholder="Ex: 123">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Data Início</label>
                                <input type="datetime-local" name="from_date" class="form-control form-control-sm"
                                       value="{{ request('from_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Data Fim</label>
                                <input type="datetime-local" name="to_date" class="form-control form-control-sm"
                                       value="{{ request('to_date') }}">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Tags de filtros ativos -->
                    @if(request()->hasAny(['level', 'request_id', 'user_id', 'from_date', 'to_date']))
                        <div class="mt-3">
                            <strong>Filtros ativos:</strong>
                            @if(request('level'))
                                <span class="filter-tag">
                                    Nível: {{ ucfirst(request('level')) }}
                                    <a href="{{ request()->fullUrlWithQuery(['level' => null]) }}" class="remove-tag">×</a>
                                </span>
                            @endif
                            @if(request('request_id'))
                                <span class="filter-tag">
                                    Request: {{ request('request_id') }}
                                    <a href="{{ request()->fullUrlWithQuery(['request_id' => null]) }}" class="remove-tag">×</a>
                                </span>
                            @endif
                            @if(request('user_id'))
                                <span class="filter-tag">
                                    Usuário: {{ request('user_id') }}
                                    <a href="{{ request()->fullUrlWithQuery(['user_id' => null]) }}" class="remove-tag">×</a>
                                </span>
                            @endif
                            @if(request('from_date'))
                                <span class="filter-tag">
                                    De: {{ \Carbon\Carbon::parse(request('from_date'))->format('d/m/Y H:i') }}
                                    <a href="{{ request()->fullUrlWithQuery(['from_date' => null]) }}" class="remove-tag">×</a>
                                </span>
                            @endif
                            @if(request('to_date'))
                                <span class="filter-tag">
                                    Até: {{ \Carbon\Carbon::parse(request('to_date'))->format('d/m/Y H:i') }}
                                    <a href="{{ request()->fullUrlWithQuery(['to_date' => null]) }}" class="remove-tag">×</a>
                                </span>
                            @endif
                            <a href="{{ route('admin.monitoring.logs') }}" class="btn btn-sm btn-outline-secondary ms-2">
                                <i class="fas fa-times"></i> Limpar todos
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Lista de Logs -->
            <div class="dashboard-layer">
                <div class="layer-header">
                    📋 Logs Recentes ({{ $logs->total() }} registros)
                </div>
                <div class="layer-content">
                    @if($logs->count() > 0)
                        @foreach($logs as $log)
                            <div class="log-entry level-{{ $log->level }}" id="log-{{ $log->id }}">
                                <div class="log-header">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="log-level level-{{ $log->level }}">
                                            {{ strtoupper($log->level) }}
                                        </span>
                                        <span class="log-timestamp">
                                            {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($log->request_id)
                                            <a href="{{ route('admin.monitoring.logs') }}?request_id={{ $log->request_id }}"
                                               class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-link"></i> {{ substr($log->request_id, 0, 8) }}
                                            </a>
                                        @endif
                                        @if($log->user_id)
                                            <a href="{{ route('admin.monitoring.logs') }}?user_id={{ $log->user_id }}"
                                               class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-user"></i> {{ $log->user_id }}
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <div class="log-message">
                                    {{ $log->message }}
                                </div>

                                @if($log->context)
                                    @php
                                        $context = is_string($log->context) ? json_decode($log->context, true) : $log->context;
                                        $hasImportantData = $context && (
                                            isset($context['proposicao_id']) ||
                                            isset($context['user_id']) ||
                                            isset($context['onlyoffice_info']) ||
                                            isset($context['container_info']) ||
                                            isset($context['user_click'])
                                        );
                                    @endphp

                                    @if($hasImportantData)
                                        <div class="log-context">
                                            <strong>Contexto:</strong>
                                            <pre style="margin: 0.5rem 0 0 0; white-space: pre-wrap;">{{ json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    @elseif($context)
                                        <button type="button" class="btn-expand" onclick="toggleContext('context-{{ $log->id }}')">
                                            <i class="fas fa-eye"></i> Ver contexto
                                        </button>
                                        <div id="context-{{ $log->id }}" class="log-context" style="display: none;">
                                            <pre style="margin: 0; white-space: pre-wrap;">{{ json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    @endif
                                @endif

                                @if($log->exception)
                                    <button type="button" class="btn-expand text-danger" onclick="toggleContext('exception-{{ $log->id }}')">
                                        <i class="fas fa-exclamation-triangle"></i> Ver exceção
                                    </button>
                                    <div id="exception-{{ $log->id }}" class="log-context" style="display: none; border-left: 3px solid #dc3545;">
                                        <pre style="margin: 0; white-space: pre-wrap; color: #721c24;">{{ $log->exception }}</pre>
                                    </div>
                                @endif

                                <div class="log-meta">
                                    <span><i class="fas fa-hashtag"></i> ID: {{ $log->id }}</span>
                                    @if($log->request_id)
                                        <span><i class="fas fa-link"></i> Request: {{ $log->request_id }}</span>
                                    @endif
                                    @if($log->user_id)
                                        <span><i class="fas fa-user"></i> User: {{ $log->user_id }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <!-- Paginação -->
                        <div class="pagination-wrapper">
                            {{ $logs->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="no-logs">
                            <i class="fas fa-search"></i>
                            <h5>Nenhum log encontrado</h5>
                            <p>Tente ajustar os filtros ou verifique se há atividade no sistema.</p>
                            <a href="{{ route('admin.monitoring.logs') }}" class="btn btn-primary">
                                <i class="fas fa-refresh"></i> Ver todos os logs
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<script>
function toggleContext(elementId) {
    const element = document.getElementById(elementId);
    if (element.style.display === 'none') {
        element.style.display = 'block';
    } else {
        element.style.display = 'none';
    }
}

// Auto-refresh (opcional)
let autoRefresh = false;
let refreshInterval;

function toggleAutoRefresh() {
    autoRefresh = !autoRefresh;
    const button = document.getElementById('auto-refresh-btn');

    if (autoRefresh) {
        button.innerHTML = '<i class="fas fa-pause"></i> Pausar atualização';
        button.className = 'btn btn-warning btn-sm';
        refreshInterval = setInterval(() => {
            window.location.reload();
        }, 30000); // Refresh a cada 30 segundos
    } else {
        button.innerHTML = '<i class="fas fa-play"></i> Auto-atualizar';
        button.className = 'btn btn-success btn-sm';
        clearInterval(refreshInterval);
    }
}

// Destacar logs recentes (últimos 5 minutos)
document.addEventListener('DOMContentLoaded', function() {
    const fiveMinutesAgo = new Date(Date.now() - 5 * 60 * 1000);

    document.querySelectorAll('.log-entry').forEach(entry => {
        const timestamp = entry.querySelector('.log-timestamp').textContent;
        const logDate = new Date(timestamp.replace(/(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2}):(\d{2})/, '$3-$2-$1T$4:$5:$6'));

        if (logDate > fiveMinutesAgo) {
            entry.style.borderLeft = '4px solid #28a745';
            entry.style.background = '#f0fff4';
        }
    });
});

// Clear Logs Modal and Functions
function showClearLogsModal() {
    const modal = document.getElementById('clearLogsModal');
    if (modal) {
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
    }
}

function clearLogs(level = '', olderThan = '') {
    if (!confirm('⚠️ Tem certeza que deseja limpar os logs? Esta ação não pode ser desfeita.')) {
        return;
    }

    const button = document.getElementById('clearLogsBtn');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Limpando...';
    button.disabled = true;

    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('_method', 'DELETE');

    if (level) formData.append('level', level);
    if (olderThan) formData.append('older_than', olderThan);

    fetch('{{ route("admin.monitoring.logs.clear") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showToast('success', data.message);

            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('clearLogsModal'));
            if (modal) modal.hide();

            // Reload page after 2 seconds
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showToast('error', data.message || 'Erro ao limpar logs');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Erro de conexão ao limpar logs');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function showToast(type, message) {
    // Create toast element
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    const toastId = 'toast-' + Date.now();

    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    toastContainer.insertAdjacentHTML('beforeend', toastHtml);

    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
    toast.show();

    // Remove toast element after it's hidden
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}
</script>

<!-- Clear Logs Modal -->
<div class="modal fade" id="clearLogsModal" tabindex="-1" aria-labelledby="clearLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clearLogsModalLabel">
                    <i class="fas fa-trash text-danger"></i> Limpar Logs do Sistema
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Atenção:</strong> Esta ação irá remover permanentemente os logs selecionados do sistema.
                </div>

                <div class="mb-3">
                    <label for="clearLevel" class="form-label">Filtrar por Nível (opcional)</label>
                    <select id="clearLevel" class="form-select">
                        <option value="">Todos os níveis</option>
                        <option value="debug">🐛 Debug</option>
                        <option value="info">ℹ️ Info</option>
                        <option value="notice">📢 Notice</option>
                        <option value="warning">⚠️ Warning</option>
                        <option value="error">❌ Error</option>
                        <option value="critical">💥 Critical</option>
                        <option value="alert">🔔 Alert</option>
                        <option value="emergency">🚨 Emergency</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="clearOlderThan" class="form-label">Logs mais antigos que (dias)</label>
                    <select id="clearOlderThan" class="form-select">
                        <option value="">Todos os logs</option>
                        <option value="1">1 dia</option>
                        <option value="7">7 dias</option>
                        <option value="30">30 dias</option>
                        <option value="90">90 dias</option>
                    </select>
                </div>

                <div class="text-muted">
                    <small>
                        <i class="fas fa-info-circle"></i>
                        Se nenhum filtro for selecionado, <strong>todos os logs</strong> serão removidos.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" id="clearLogsBtn" class="btn btn-danger"
                        onclick="clearLogs(document.getElementById('clearLevel').value, document.getElementById('clearOlderThan').value)">
                    <i class="fas fa-trash"></i> Confirmar Limpeza
                </button>
            </div>
        </div>
    </div>
</div>

@endsection