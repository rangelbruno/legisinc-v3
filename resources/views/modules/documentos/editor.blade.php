@extends('layouts.app')

@section('title', $title ?? 'Editor de Documentos')

@push('styles')
<style>
    .document-editor-container {
        height: calc(100vh - 100px);
        min-height: 600px;
    }
    
    .editor-toolbar {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .editor-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .editor-actions {
        display: flex;
        gap: 10px;
    }
    
    .status-indicator {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.9em;
    }
    
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    
    .status-connected {
        background-color: #28a745;
    }
    
    .status-disconnected {
        background-color: #dc3545;
    }
    
    .status-saving {
        background-color: #ffc107;
        animation: pulse 1s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .collaborators-panel {
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        min-width: 250px;
        z-index: 1000;
    }
    
    .collaborator-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 5px 0;
    }
    
    .collaborator-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #007bff;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 12px;
    }
    
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999;
    }
    
    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 15px;
        border-radius: 4px;
        margin: 20px;
        border: 1px solid #f5c6cb;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <div class="document-editor-container">
        
        {{-- Toolbar do Editor --}}
        <div class="editor-toolbar">
            <div class="editor-info">
                <h5 class="mb-0">{{ $title }}</h5>
                
                @if(isset($instancia))
                    <span class="badge badge-{{ $instancia->status === 'finalizado' ? 'success' : 'primary' }}">
                        {{ ucfirst($instancia->status) }}
                    </span>
                    <span class="text-muted">Versão {{ $instancia->versao }}</span>
                @endif
                
                <div class="status-indicator">
                    <div class="status-dot status-connected" id="connection-status"></div>
                    <span id="connection-text">Conectado</span>
                </div>
            </div>
            
            <div class="editor-actions">
                @if(isset($instancia))
                    <a href="{{ route('onlyoffice.pdf.instancia', $instancia) }}" 
                       class="btn btn-outline-secondary btn-sm" 
                       title="Exportar para PDF">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    
                    <a href="{{ route('onlyoffice.history.instancia', $instancia) }}" 
                       class="btn btn-outline-info btn-sm" 
                       title="Histórico de Versões"
                       onclick="showVersionHistory(); return false;">
                        <i class="fas fa-history"></i> Histórico
                    </a>
                @endif
                
                <button type="button" 
                        class="btn btn-outline-secondary btn-sm" 
                        onclick="window.close()" 
                        title="Fechar Editor">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>
        </div>
        
        {{-- Container do Editor ONLYOFFICE --}}
        <div id="onlyoffice-editor" style="height: calc(100% - 60px); position: relative;">
            <div class="loading-overlay" id="editor-loading">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Carregando...</span>
                    </div>
                    <p class="mt-2">Carregando editor...</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Painel de Colaboradores --}}
@if(isset($instancia))
<div class="collaborators-panel" id="collaborators-panel" style="display: none;">
    <h6 class="mb-3">
        <i class="fas fa-users"></i> Colaboradores Online
    </h6>
    <div id="online-collaborators">
        {{-- Será preenchido via JavaScript --}}
    </div>
</div>
@endif

{{-- Modal de Histórico de Versões --}}
<div class="modal fade" id="versionHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Histórico de Versões</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="version-history-content">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Carregando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ config('onlyoffice.server_url') }}/web-apps/apps/api/documents/api.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const config = @json($config);
    let docEditor = null;
    let isConnected = false;
    let isSaving = false;
    
    // Configurar eventos do ONLYOFFICE
    config.events = {
        'onAppReady': onAppReady,
        'onDocumentReady': onDocumentReady,
        'onDocumentStateChange': onDocumentStateChange,
        'onInfo': onInfo,
        'onWarning': onWarning,
        'onError': onError,
        'onRequestClose': onRequestClose,
        'onRequestUsers': onRequestUsers,
        'onRequestSendNotify': onRequestSendNotify,
        'onMetaChange': onMetaChange,
        'onRequestHistory': onRequestHistory,
        'onRequestRestore': onRequestRestore
    };
    
    // Inicializar editor
    try {
        docEditor = new DocsAPI.DocEditor('onlyoffice-editor', config);
        console.log('ONLYOFFICE Editor inicializado com sucesso');
    } catch (error) {
        console.error('Erro ao inicializar ONLYOFFICE:', error);
        showError('Erro ao carregar o editor. Verifique se o ONLYOFFICE está funcionando.');
    }
    
    // Event handlers
    function onAppReady() {
        console.log('ONLYOFFICE App carregado');
        updateConnectionStatus(true);
    }
    
    function onDocumentReady() {
        console.log('Documento carregado e pronto para edição');
        hideLoading();
        showSuccess('Documento carregado com sucesso!');
        showCollaboratorsPanel();
    }
    
    function onDocumentStateChange(event) {
        console.log('Estado do documento alterado:', event.data);
        if (event.data) {
            updateSavingStatus(true);
            // Documento foi modificado
            setTimeout(() => {
                updateSavingStatus(false);
            }, 2000);
        }
    }
    
    function onInfo(event) {
        console.log('Info ONLYOFFICE:', event.data);
    }
    
    function onWarning(event) {
        console.warn('Warning ONLYOFFICE:', event.data);
        showWarning('Aviso: ' + event.data);
    }
    
    function onError(event) {
        console.error('Erro ONLYOFFICE:', event.data);
        showError('Erro no editor: ' + event.data);
        updateConnectionStatus(false);
    }
    
    function onRequestClose() {
        if (confirm('Deseja fechar o editor? Certifique-se de que as alterações foram salvas.')) {
            window.close();
        }
    }
    
    function onRequestUsers(event) {
        console.log('Solicitação de usuários:', event.data);
        // Retornar lista de usuários disponíveis para colaboração
    }
    
    function onRequestSendNotify(event) {
        console.log('Enviar notificação:', event.data);
        // Implementar sistema de notificações
    }
    
    function onMetaChange(event) {
        console.log('Metadados alterados:', event.data);
    }
    
    function onRequestHistory(event) {
        console.log('Histórico solicitado');
        showVersionHistory();
    }
    
    function onRequestRestore(event) {
        console.log('Restaurar versão:', event.data);
        // Implementar restauração de versão
    }
    
    // Utility functions
    function updateConnectionStatus(connected) {
        isConnected = connected;
        const statusDot = document.getElementById('connection-status');
        const statusText = document.getElementById('connection-text');
        
        if (connected) {
            statusDot.className = 'status-dot status-connected';
            statusText.textContent = 'Conectado';
        } else {
            statusDot.className = 'status-dot status-disconnected';
            statusText.textContent = 'Desconectado';
        }
    }
    
    function updateSavingStatus(saving) {
        isSaving = saving;
        const statusDot = document.getElementById('connection-status');
        const statusText = document.getElementById('connection-text');
        
        if (saving) {
            statusDot.className = 'status-dot status-saving';
            statusText.textContent = 'Salvando...';
        } else if (isConnected) {
            statusDot.className = 'status-dot status-connected';
            statusText.textContent = 'Conectado';
        }
    }
    
    function hideLoading() {
        const loading = document.getElementById('editor-loading');
        if (loading) {
            loading.style.display = 'none';
        }
    }
    
    function showCollaboratorsPanel() {
        @if(isset($instancia))
        const panel = document.getElementById('collaborators-panel');
        if (panel) {
            panel.style.display = 'block';
        }
        @endif
    }
    
    function showError(message) {
        showNotification(message, 'error');
    }
    
    function showWarning(message) {
        showNotification(message, 'warning');
    }
    
    function showSuccess(message) {
        showNotification(message, 'success');
    }
    
    function showNotification(message, type) {
        // Implementar sistema de notificações toast
        console.log(`[${type.toUpperCase()}] ${message}`);
        
        // Implementação simples com alert para desenvolvimento
        if (type === 'error') {
            alert('Erro: ' + message);
        }
    }
    
    // Version history
    window.showVersionHistory = function() {
        @if(isset($instancia))
        $('#versionHistoryModal').modal('show');
        
        fetch('{{ route("onlyoffice.history.instancia", $instancia) }}')
            .then(response => response.json())
            .then(data => {
                displayVersionHistory(data.versoes);
            })
            .catch(error => {
                console.error('Erro ao carregar histórico:', error);
                document.getElementById('version-history-content').innerHTML = 
                    '<div class="alert alert-danger">Erro ao carregar histórico de versões</div>';
            });
        @endif
    };
    
    function displayVersionHistory(versions) {
        const content = document.getElementById('version-history-content');
        
        if (!versions || versions.length === 0) {
            content.innerHTML = '<div class="alert alert-info">Nenhuma versão encontrada</div>';
            return;
        }
        
        let html = '<div class="list-group">';
        versions.forEach(version => {
            html += `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Versão ${version.version}</h6>
                            <p class="mb-1">${version.user.name}</p>
                            <small class="text-muted">${new Date(version.created).toLocaleString()}</small>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-primary" 
                                    onclick="restoreVersion('${version.key}')">
                                Restaurar
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        content.innerHTML = html;
    }
    
    window.restoreVersion = function(versionKey) {
        if (confirm('Deseja restaurar esta versão? As alterações não salvas serão perdidas.')) {
            // Implementar restauração via API ONLYOFFICE
            console.log('Restaurando versão:', versionKey);
        }
    };
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', function(e) {
        if (isSaving) {
            e.preventDefault();
            e.returnValue = 'O documento está sendo salvo. Tem certeza que deseja sair?';
        }
    });
});
</script>
@endpush