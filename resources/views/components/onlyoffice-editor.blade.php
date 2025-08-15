{{-- OnlyOffice Editor Component --}}
{{-- 
    Componente reutilizável para o editor OnlyOffice
    Usado em 3 contextos diferentes:
    1. Administrador: criação/edição de templates
    2. Parlamentar: criação de proposições usando templates
    3. Legislativo: alterações em proposições enviadas pelo Parlamentar
--}}

@props([
    'documentKey',
    'documentUrl',
    'documentTitle' => 'Documento',
    'documentType' => 'word',
    'fileType' => 'docx',
    'callbackUrl',
    'mode' => 'edit', // edit ou view
    'userType' => 'parlamentar', // admin, parlamentar, legislativo
    'userId' => auth()->id(),
    'userName' => auth()->user()->name ?? 'Usuário',
    'saveRoute' => null,
    'backRoute' => null,
    'height' => 'calc(100vh - 70px)',
    'showVariablesPanel' => false,
    'showToolbar' => true,
    'customActions' => [],
    'proposicaoId' => null,
    'templateId' => null,
])

@php
    $editorId = 'onlyoffice-editor-' . uniqid();
    $serverUrl = config('app.env') === 'local' ? 'http://localhost:8080' : config('onlyoffice.server_url');
@endphp

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $documentTitle }}</title>
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        * {
            box-sizing: border-box;
        }
        
        body, html {
            margin: 0 !important;
            padding: 0 !important;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            font-family: Arial, sans-serif;
        }
        
        .editor-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            width: 100vw;
            position: fixed;
            top: 0;
            left: 0;
        }
        
        .editor-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            min-height: 70px;
            flex-shrink: 0;
        }
        
        .editor-header .badge {
            font-weight: 600;
            font-size: 0.85rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .editor-header .btn {
            font-weight: 600;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .editor-header .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .badge-warning {
            background-color: #ffc107 !important;
            color: #000 !important;
        }
        
        .badge-success {
            background-color: #28a745 !important;
            color: #fff !important;
        }
        
        .editor-content {
            flex: 1;
            position: relative;
            width: 100%;
            height: calc(100vh - 70px);
            overflow: hidden;
        }
        
        #{{ $editorId }} {
            width: 100%;
            height: 100%;
            border: none;
            outline: none;
        }
        
        /* Forçar iframe do OnlyOffice a ocupar todo espaço disponível */
        .editor-content iframe {
            width: 100% !important;
            height: 100% !important;
            border: none !important;
            outline: none !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
        }
        
        /* Forçar todos os divs internos do OnlyOffice */
        #{{ $editorId }} div,
        #{{ $editorId }} iframe,
        #{{ $editorId }} > * {
            width: 100% !important;
            height: 100% !important;
        }
        
        /* Variables Panel Styles */
        .variables-panel {
            position: fixed;
            top: 80px;
            right: 20px;
            width: 320px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            z-index: 999;
            max-height: 80vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .variables-panel.collapsed {
            height: 50px;
            overflow: hidden;
            max-height: 50px;
        }
        
        .panel-toggle {
            cursor: pointer;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px 12px 0 0;
            flex-shrink: 0;
        }
        
        .panel-content {
            padding: 15px;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        .variable-btn {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            border: 1px solid #e2e8f0;
            background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%);
            color: #2d3748;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 0.5rem 0.75rem;
            text-align: left;
            width: 100%;
            margin-bottom: 0.5rem;
            border-radius: 8px;
        }
        
        .variable-btn:hover {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
            border-color: #3182ce;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(66, 153, 225, 0.3);
        }
        
        /* Toast Notifications */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            width: 380px;
        }
        
        .custom-toast {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            margin-bottom: 12px;
            padding: 16px 20px;
            border-left: 4px solid;
            animation: slideInRight 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transition: all 0.3s ease;
        }
        
        .custom-toast.toast-success {
            border-left-color: #10b981;
        }
        
        .custom-toast.toast-info {
            border-left-color: #3b82f6;
        }
        
        .custom-toast.toast-warning {
            border-left-color: #f59e0b;
        }
        
        .custom-toast.toast-error {
            border-left-color: #ef4444;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        /* Loading Spinner */
        .loading-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 70px);
            width: 100%;
            flex-direction: column;
            gap: 20px;
            position: absolute;
            top: 0;
            left: 0;
            background-color: #f8f9fa;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="editor-container">
        @if($showToolbar)
        <!-- Header -->
        <div class="editor-header">
            <div class="d-flex align-items-center">
                <i class="ki-duotone ki-document fs-2 me-3">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <div>
                    <h4 class="mb-0">{{ $documentTitle }}</h4>
                    <small class="opacity-75">
                        @if($userType === 'admin')
                            Editor de Template de Documento
                        @elseif($userType === 'parlamentar')
                            Criação de Proposição
                        @else
                            Edição de Proposição - Legislativo
                        @endif
                    </small>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-success btn-sm" onclick="onlyofficeEditor.forceSave()">
                    <i class="ki-duotone ki-check fs-6 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Salvar
                </button>
                
                <span id="statusSalvamento" class="badge badge-warning px-3 py-2">
                    <i class="ki-duotone ki-information fs-7 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <span id="statusTexto">Não Salvo</span>
                </span>
                
                @foreach($customActions as $action)
                <button onclick="{{ $action['onclick'] }}" class="btn {{ $action['class'] ?? 'btn-secondary' }} btn-sm">
                    @if(isset($action['icon']))
                    <i class="{{ $action['icon'] }} me-1"></i>
                    @endif
                    {{ $action['label'] }}
                </button>
                @endforeach
                
                @if($backRoute)
                <button onclick="onlyofficeEditor.fecharEditor('{{ $backRoute }}')" class="btn btn-outline-light btn-sm border-2">
                    <i class="ki-duotone ki-cross fs-6 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Fechar
                </button>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Editor -->
        <div class="editor-content">
            <div id="loading-{{ $editorId }}" class="loading-container">
                <div class="spinner"></div>
                <p>Carregando editor OnlyOffice...</p>
            </div>
            <div id="{{ $editorId }}" style="display: none;"></div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    @if($showVariablesPanel)
    <!-- Variables Panel -->
    <div class="variables-panel" id="variablesPanel">
        <div class="panel-toggle" onclick="onlyofficeEditor.togglePanel()">
            <div class="d-flex justify-content-between align-items-center">
                <strong class="text-primary">
                    <i class="ki-duotone ki-code fs-5 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                    Variáveis Disponíveis
                </strong>
                <i class="ki-duotone ki-up fs-6" id="panelIcon">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
        </div>
        
        <div class="panel-content" id="panelContent">
            @include('components.onlyoffice-variables')
        </div>
    </div>
    @endif

    <!-- OnlyOffice API -->
    <script src="{{ $serverUrl }}/web-apps/apps/api/documents/api.js" onload="console.info('🟢 OnlyOffice: API loaded successfully')" onerror="console.error('🔴 OnlyOffice: Failed to load API from {{ $serverUrl }}')"></script>
    
    <!-- Component Script -->
    <script>
        window.onlyofficeEditor = {
            docEditor: null,
            documentModified: false,
            editorId: '{{ $editorId }}',
            config: null,
            
            init: function() {
                const self = this;
                
                // Debug removido - problema do documentType resolvido
                
                // Configuração do OnlyOffice
                this.config = {
                    "width": "100%",
                    "height": "100%",
                    "type": "desktop",
                    "documentType": "{{ $documentType }}",
                    "document": {
                        "fileType": "{{ $fileType }}",
                        "key": "{{ $documentKey }}",
                        "title": "{{ $documentTitle }}",
                        "url": "{{ $documentUrl }}",
                        "permissions": {
                            "edit": {{ $mode === 'edit' ? 'true' : 'false' }},
                            "download": true,
                            "print": true,
                            "review": true,
                            "comment": true
                        }
                    },
                    "editorConfig": {
                        "mode": "{{ $mode }}",
                        "lang": "pt-BR",
                        "region": "pt-BR",
                        "callbackUrl": "{{ $callbackUrl }}",
                        "user": {
                            "id": "{{ $userId }}",
                            "name": "{{ $userName }}",
                            "group": "{{ $userType }}"
                        },
                        "customization": {
                            "about": false,
                            "feedback": false,
                            "forcesave": true,
                            "autosave": true,
                            "toolbarNoTabs": true,
                            "toolbarHideFileName": true,
                            "zoom": 100,
                            "compactToolbar": true,
                            "leftMenu": false,
                            "rightMenu": false,
                            "toolbar": true,
                            "statusBar": false,
                            "comments": false
                        }
                    },
                    "events": {
                        "onDocumentReady": function() {
                            console.info('🟢 OnlyOffice: Document ready for editing');
                            self.onDocumentReady();
                        },
                        "onDocumentStateChange": function(event) {
                            self.onDocumentStateChange(event);
                        },
                        "onError": function(event) {
                            self.onError(event);
                        },
                        "onRequestSave": function() {
                            self.onRequestSave();
                        }
                    }
                };
                
                // Inicializar editor
                try {
                    console.group('🔵 OnlyOffice: Editor initialization');
                    console.info('Document URL:', this.config.document.url);
                    console.info('Callback URL:', this.config.editorConfig.callbackUrl);
                    console.info('Editor ID:', this.editorId);
                    console.info('File type:', this.config.document.fileType);
                    console.info('Document Type:', this.config.documentType);
                    console.groupEnd();
                    
                    // Debug removido - problema do documentType resolvido
                    
                    // Verificar se o elemento existe
                    const editorElement = document.getElementById(this.editorId);
                    const loadingElement = document.getElementById('loading-' + this.editorId);
                    
                    if (!editorElement) {
                        console.error('🔴 OnlyOffice: Editor element not found:', this.editorId);
                        return;
                    }
                    
                    // Esconder loading e mostrar editor ANTES de inicializar
                    if (loadingElement) {
                        loadingElement.style.display = 'none';
                    }
                    if (editorElement) {
                        editorElement.style.display = 'block';
                    }
                    
                    this.docEditor = new DocsAPI.DocEditor(this.editorId, this.config);
                    
                } catch (error) {
                    console.error('🔴 OnlyOffice: Initialization failed:', error);
                    this.showToast('Falha ao carregar o editor', 'error', 6000);
                }
            },
            
            onDocumentReady: function() {
                if (typeof this.showToast === 'function') {
                    this.showToast('Editor carregado e pronto para uso', 'success', 3000);
                }
                this.documentModified = false;
                if (typeof this.updateStatusBadge === 'function') {
                    this.updateStatusBadge('saved');
                }
                
                // Força o redimensionamento após carregar
                setTimeout(() => {
                    this.forceResize();
                }, 500);
            },
            
            onDocumentStateChange: function(event) {
                console.debug('🔵 OnlyOffice: Document state changed:', event.data);
                if (event && event.data) {
                    this.documentModified = true;
                    this.updateStatusBadge('modified');
                }
            },
            
            onError: function(event) {
                console.error('🔴 OnlyOffice: Editor error:', {
                    error_code: event?.data?.errorCode,
                    error_description: event?.data?.errorDescription,
                    event_type: event?.type,
                    message: event?.message,
                    full_event: event,
                    document_url: this.config?.document?.url,
                    callback_url: this.config?.editorConfig?.callbackUrl,
                    document_key: this.config?.document?.key
                });
                
                let errorMessage = 'Erro no editor';
                if (event?.data?.errorDescription) {
                    errorMessage = event.data.errorDescription;
                } else if (event?.message) {
                    errorMessage = event.message;
                }
                
                this.showToast(errorMessage + '. Tente recarregar a página', 'error', 8000);
                
                // Esconder loading se ainda estiver visível
                const loadingElement = document.getElementById('loading-' + this.editorId);
                if (loadingElement && !loadingElement.classList.contains('hidden')) {
                    loadingElement.classList.add('hidden');
                }
            },
            
            onRequestSave: function() {
                console.info('🔵 OnlyOffice: Save requested by editor');
                this.updateStatusBadge('saving');
            },
            
            forceSave: function() {
                console.info('🔵 OnlyOffice: Force save initiated');
                this.updateStatusBadge('saving');
                this.showToast('Salvando documento...', 'info', 2000);
                
                if (this.docEditor) {
                    try {
                        // Método correto do OnlyOffice para forçar salvamento
                        // Este método deve gerar um callback com status 6 (force save)
                        this.docEditor.serviceCommand("forcesave", null);
                        console.info('🟢 OnlyOffice: serviceCommand forcesave executed successfully');
                        
                        // Aguardar resposta do callback
                        setTimeout(() => {
                            this.showToast('Documento salvo!', 'success', 3000);
                            this.updateStatusBadge('saved');
                            this.documentModified = false;
                        }, 3000);
                        
                    } catch (error) {
                        console.error('🔴 OnlyOffice: serviceCommand forcesave failed:', error);
                        this.updateStatusBadge('error');
                        this.showToast('Erro ao salvar. Tente usar Ctrl+S', 'error', 5000);
                    }
                } else {
                    this.showToast('Editor ainda não foi carregado', 'warning', 4000);
                }
            },
            
            updateStatusBadge: function(status) {
                const badge = document.getElementById('statusSalvamento');
                const texto = document.getElementById('statusTexto');
                
                if (!badge || !texto) return;
                
                badge.classList.remove('badge-warning', 'badge-success', 'badge-info', 'badge-danger');
                
                switch(status) {
                    case 'saved':
                        badge.classList.add('badge-success');
                        texto.textContent = 'Salvo';
                        break;
                    case 'modified':
                        badge.classList.add('badge-warning');
                        texto.textContent = 'Não Salvo';
                        break;
                    case 'saving':
                        badge.classList.add('badge-info');
                        texto.textContent = 'Salvando...';
                        break;
                    case 'error':
                        badge.classList.add('badge-danger');
                        texto.textContent = 'Erro';
                        break;
                }
            },
            
            showToast: function(message, type = 'info', duration = 4000) {
                const container = document.getElementById('toastContainer');
                if (!container) {
                    console.warn('🟡 OnlyOffice: Toast container element not found');
                    return;
                }
                
                const toast = document.createElement('div');
                toast.className = `custom-toast toast-${type}`;
                
                const icons = {
                    success: '✓',
                    info: 'i',
                    warning: '!', 
                    error: '✕'
                };
                
                toast.innerHTML = `
                    <div class="toast-content">
                        <div class="toast-icon">${icons[type] || 'i'}</div>
                        <div class="toast-message">${message}</div>
                    </div>
                `;
                
                container.appendChild(toast);
                
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, duration);
            },
            
            fecharEditor: function(backRoute) {
                if (this.documentModified) {
                    Swal.fire({
                        title: 'Alterações não salvas',
                        text: 'Você tem alterações não salvas. Tem certeza que deseja fechar?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sim, fechar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = backRoute;
                        }
                    });
                } else {
                    window.location.href = backRoute;
                }
            },
            
            togglePanel: function() {
                const panel = document.getElementById('variablesPanel');
                const icon = document.getElementById('panelIcon');
                
                if (panel) {
                    panel.classList.toggle('collapsed');
                    
                    if (panel.classList.contains('collapsed')) {
                        icon.className = 'ki-duotone ki-down fs-6';
                    } else {
                        icon.className = 'ki-duotone ki-up fs-6';
                    }
                }
            },
            
            inserirVariavel: function(variavel) {
                console.debug('🔵 OnlyOffice: Variable insertion:', variavel);
                
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(variavel).then(() => {
                        this.showToast(`${variavel} copiado! Use Ctrl+V para colar`, 'success', 2000);
                    });
                }
            },
            
            forceResize: function() {
                console.debug('🔵 OnlyOffice: Forcing editor resize');
                
                const editorElement = document.getElementById(this.editorId);
                if (editorElement) {
                    // Força o redimensionamento CSS
                    editorElement.style.width = '100%';
                    editorElement.style.height = '100%';
                    
                    // Força o redimensionamento do iframe interno
                    const iframe = editorElement.querySelector('iframe');
                    if (iframe) {
                        iframe.style.width = '100%';
                        iframe.style.height = '100%';
                    }
                    
                    // Tenta usar o método resize do OnlyOffice se disponível
                    if (this.docEditor && typeof this.docEditor.resize === 'function') {
                        try {
                            this.docEditor.resize();
                            console.debug('🟢 OnlyOffice: Resize via API executed successfully');
                        } catch (e) {
                            console.debug('🟡 OnlyOffice: Resize method not available in this version');
                        }
                    }
                }
            }
        };
        
        // Inicializar quando o DOM estiver pronto
        document.addEventListener('DOMContentLoaded', function() {
            // Verificar se o DocsAPI foi carregado
            if (typeof DocsAPI === 'undefined') {
                console.error('🔴 OnlyOffice: API failed to load - DocsAPI is undefined');
                const loadingElement = document.getElementById('loading-' + onlyofficeEditor.editorId);
                if (loadingElement) {
                    loadingElement.innerHTML = '<div class="text-danger">Erro ao carregar OnlyOffice API. Verifique se o servidor está rodando.</div>';
                }
                return;
            }
            
            // Aguardar um pouco para garantir que todos os elementos foram criados
            setTimeout(function() {
                console.info('🚀 OnlyOffice: Starting editor initialization...');
                onlyofficeEditor.init();
            }, 100);
        });
        
        // Prevenir saída sem salvar
        window.addEventListener('beforeunload', function(e) {
            if (onlyofficeEditor.documentModified) {
                e.preventDefault();
                e.returnValue = 'Você tem alterações não salvas. Tem certeza que deseja sair?';
            }
        });
        
        // Adicionar suporte para Ctrl+S
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault(); // Impedir o save padrão do browser
                if (onlyofficeEditor && typeof onlyofficeEditor.forceSave === 'function') {
                    onlyofficeEditor.forceSave();
                    console.debug('🔵 OnlyOffice: Ctrl+S intercepted, calling forceSave()');
                }
            }
        });
        
        // Redimensionar quando a janela mudar de tamanho
        window.addEventListener('resize', function() {
            if (onlyofficeEditor && typeof onlyofficeEditor.forceResize === 'function') {
                onlyofficeEditor.forceResize();
            }
        });
    </script>
    
    {{ $slot ?? '' }}
</body>
</html>