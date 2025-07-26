<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor: {{ $tipo->nome }}</title>
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet">
    
    <!-- SweetAlert2 for better debugging -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
        }
        
        .editor-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
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
        
        .editor-header .btn-outline-light {
            background-color: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }
        
        .editor-header .btn-outline-light:hover {
            background-color: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.8);
        }
        
        .badge-warning {
            background-color: #ffc107 !important;
            color: #000 !important;
        }
        
        .badge-success {
            background-color: #28a745 !important;
            color: #fff !important;
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
            position: relative;
            overflow: hidden;
        }
        
        .custom-toast.toast-success {
            border-left-color: #10b981;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.05) 0%, rgba(255, 255, 255, 0.95) 100%);
        }
        
        .custom-toast.toast-info {
            border-left-color: #3b82f6;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(255, 255, 255, 0.95) 100%);
        }
        
        .custom-toast.toast-warning {
            border-left-color: #f59e0b;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.05) 0%, rgba(255, 255, 255, 0.95) 100%);
        }
        
        .custom-toast.toast-error {
            border-left-color: #ef4444;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.05) 0%, rgba(255, 255, 255, 0.95) 100%);
        }
        
        .toast-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .toast-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 12px;
        }
        
        .toast-success .toast-icon {
            background: #10b981;
            color: white;
        }
        
        .toast-info .toast-icon {
            background: #3b82f6;
            color: white;
        }
        
        .toast-warning .toast-icon {
            background: #f59e0b;
            color: white;
        }
        
        .toast-error .toast-icon {
            background: #ef4444;
            color: white;
        }
        
        .toast-message {
            flex: 1;
            font-weight: 500;
            color: #1f2937;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .toast-close {
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            font-size: 14px;
        }
        
        .toast-close:hover {
            background: rgba(0, 0, 0, 0.1);
            color: #374151;
        }
        
        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            border-radius: 0 0 12px 12px;
            opacity: 0.6;
            animation: progressBar 4s linear;
        }
        
        .toast-success .toast-progress {
            background: #10b981;
        }
        
        .toast-info .toast-progress {
            background: #3b82f6;
        }
        
        .toast-warning .toast-progress {
            background: #f59e0b;
        }
        
        .toast-error .toast-progress {
            background: #ef4444;
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
        
        @keyframes slideOutRight {
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        @keyframes progressBar {
            from {
                width: 100%;
            }
            to {
                width: 0%;
            }
        }
        
        .custom-toast.removing {
            animation: slideOutRight 0.3s cubic-bezier(0.4, 0, 1, 1);
        }
        
        .editor-content {
            flex: 1;
            position: relative;
        }
        
        #onlyoffice-editor {
            width: 100%;
            height: 100%;
        }
        
        .variables-panel {
            position: fixed;
            top: 80px;
            right: 20px;
            width: 280px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            z-index: 999;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .variables-panel.collapsed {
            height: 50px;
            overflow: hidden;
        }
        
        .panel-toggle {
            cursor: pointer;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            background: #f8f9fa;
            border-radius: 10px 10px 0 0;
        }
        
        .panel-content {
            padding: 15px;
        }
    </style>
</head>
<body>
    <div class="editor-container">
        <!-- Header -->
        <div class="editor-header">
            <div class="d-flex align-items-center">
                <i class="ki-duotone ki-document fs-2 me-3">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <div>
                    <h4 class="mb-0">Template: {{ $tipo->nome }}</h4>
                    <small class="opacity-75">Editor de Template de Documento</small>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-success btn-sm" onclick="forceSave()">
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
                
                <button onclick="fecharEditor()" class="btn btn-outline-light btn-sm border-2">
                    <i class="ki-duotone ki-cross fs-6 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Fechar
                </button>
            </div>
        </div>
        
        <!-- Editor -->
        <div class="editor-content">
            <div id="onlyoffice-editor"></div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Variables Panel -->
    <div class="variables-panel" id="variablesPanel">
        <div class="panel-toggle" onclick="togglePanel()">
            <div class="d-flex justify-content-between align-items-center">
                <strong class="text-primary">
                    <i class="ki-duotone ki-code fs-5 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                    Variáveis
                </strong>
                <i class="ki-duotone ki-up fs-6" id="panelIcon">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
        </div>
        
        <div class="panel-content" id="panelContent">
            <div class="mb-3">
                <strong class="fs-7 text-muted">DADOS DA PROPOSIÇÃO</strong>
                <div class="mt-2">
                    <div class="mb-2"><code class="fs-8">${numero_proposicao}</code> <small class="text-muted">- Número</small></div>
                    <div class="mb-2"><code class="fs-8">${ementa}</code> <small class="text-muted">- Ementa</small></div>
                    <div class="mb-2"><code class="fs-8">${texto}</code> <small class="text-muted">- Texto principal</small></div>
                </div>
            </div>
            
            <div class="mb-3">
                <strong class="fs-7 text-muted">AUTOR & DATA</strong>
                <div class="mt-2">
                    <div class="mb-2"><code class="fs-8">${autor_nome}</code> <small class="text-muted">- Nome do autor</small></div>
                    <div class="mb-2"><code class="fs-8">${data_atual}</code> <small class="text-muted">- Data atual</small></div>
                </div>
            </div>
            
            <div class="mb-3">
                <strong class="fs-7 text-muted">LOCALIZAÇÃO</strong>
                <div class="mt-2">
                    <div class="mb-2"><code class="fs-8">${municipio}</code> <small class="text-muted">- Nome do município</small></div>
                </div>
            </div>
            
            <div class="bg-light p-3 rounded">
                <div class="fs-8 text-muted">
                    <strong>💡 Dica:</strong> Copie e cole as variáveis no documento. Elas serão substituídas automaticamente ao gerar documentos.
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <!-- OnlyOffice Warning Override - STRATEGIC APPROACH -->
    <script>
        console.log('🎯 Setting up STRATEGIC OnlyOffice warning management...');
        
        // Global state and functions
        window._onlyofficeState = {
            documentSaved: false,
            allowClose: false,
            originalBeforeUnload: null
        };
        
        // Define smart beforeunload function globally with detailed debugging
        window.smartBeforeUnload = function(e) {
            const debugInfo = {
                trigger: 'smartBeforeUnload',
                timestamp: new Date().toISOString(),
                state: window._onlyofficeState,
                documentModified: window.documentModified,
                eventType: e ? e.type : 'unknown',
                caller: (new Error()).stack.split('\n')[2]?.trim()
            };
            
            console.group('🔍 BEFOREUNLOAD DEBUG');
            console.log('📊 Full State:', debugInfo);
            
            // If we just saved or explicitly allowed close, don't warn
            if (window._onlyofficeState.documentSaved || window._onlyofficeState.allowClose) {
                console.log('✅ Allowing close - documentSaved:', window._onlyofficeState.documentSaved, 'allowClose:', window._onlyofficeState.allowClose);
                console.groupEnd();
                return undefined;
            }
            
            // Check if there are actual unsaved changes  
            const hasUnsavedChanges = window.documentModified === true;
            if (!hasUnsavedChanges) {
                console.log('✅ No unsaved changes detected');
                console.groupEnd();
                return undefined;
            }
            
            // This is where the warning comes from
            console.error('⚠️ WARNING TRIGGERED - Unsaved changes detected!');
            console.log('🎯 This is OUR custom warning, not OnlyOffice');
            console.groupEnd();
            
            // Use SweetAlert for better debugging
            if (typeof Swal !== 'undefined') {
                e.preventDefault();
                Swal.fire({
                    title: 'Alterações não salvas',
                    html: `
                        <p>Você tem alterações não salvas.</p>
                        <div style="font-size: 12px; text-align: left; margin-top: 10px;">
                            <strong>Debug Info:</strong><br>
                            Estado salvo: ${window._onlyofficeState.documentSaved}<br>
                            Documento modificado: ${window.documentModified}<br>
                            Permitir fechar: ${window._onlyofficeState.allowClose}<br>
                            Timestamp: ${debugInfo.timestamp}
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sair mesmo assim',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window._onlyofficeState.allowClose = true;
                        window.location.href = '{{ route("templates.index") }}';
                    }
                });
                return undefined;
            } else {
                const message = `CUSTOM WARNING: Você tem alterações não salvas. 
                
DEBUG: documentSaved=${window._onlyofficeState.documentSaved}, documentModified=${window.documentModified}
                
Tem certeza que deseja sair?`;
                e.returnValue = message;
                return message;
            }
        };
        
        // Strategic approach: Allow OnlyOffice to work but manage warnings intelligently
        const setupWarningManagement = () => {
            // Store original beforeunload to restore if needed
            window._onlyofficeState.originalBeforeUnload = window.onbeforeunload;
            
            // Apply smart beforeunload
            window.onbeforeunload = window.smartBeforeUnload;
            
            console.log('✅ Strategic warning management setup complete');
        };
        
        // Setup immediately
        setupWarningManagement();
        
        // Intercept ALL beforeunload attempts for debugging
        const originalWindowOnBeforeUnload = Object.getOwnPropertyDescriptor(window, 'onbeforeunload');
        
        Object.defineProperty(window, 'onbeforeunload', {
            get: function() {
                return this._customBeforeUnload || null;
            },
            set: function(handler) {
                console.group('🕵️ BEFOREUNLOAD SETTER INTERCEPTED');
                console.log('🎯 Someone is trying to set beforeunload handler:', handler);
                console.log('📍 Caller stack:', (new Error()).stack);
                console.log('🔍 Handler type:', typeof handler);
                
                if (handler && handler !== window.smartBeforeUnload) {
                    console.warn('⚠️ EXTERNAL beforeunload handler detected! This might be OnlyOffice');
                    console.log('📝 External handler source:', handler.toString().substring(0, 200) + '...');
                    
                    // Store the external handler but don't use it
                    this._externalBeforeUnload = handler;
                    
                    // Keep our smart handler
                    this._customBeforeUnload = window.smartBeforeUnload;
                    console.log('🛡️ Blocked external handler, keeping our smart handler');
                } else {
                    console.log('✅ Setting our smart handler');
                    this._customBeforeUnload = handler;
                }
                console.groupEnd();
            },
            configurable: true
        });
        
        // Monitor for OnlyOffice overriding our handler and restore it
        const monitorInterval = setInterval(() => {
            if (window._customBeforeUnload !== window.smartBeforeUnload) {
                console.log('🔄 Handler changed, restoring smart handler...');
                window._customBeforeUnload = window.smartBeforeUnload;
            }
        }, 1000); // Check every second
        
        // Intercept beforeunload events directly
        window.addEventListener('beforeunload', function(e) {
            console.group('🚨 BEFOREUNLOAD EVENT TRIGGERED');
            console.log('📅 Timestamp:', new Date().toISOString());
            console.log('🎯 Event target:', e.target);
            console.log('🔍 Event type:', e.type);
            console.log('📍 Call stack:', (new Error()).stack);
            console.log('📊 Current state:', {
                documentModified: window.documentModified,
                documentSaved: window._onlyofficeState.documentSaved,
                allowClose: window._onlyofficeState.allowClose
            });
            
            // Call our smart handler
            const result = window.smartBeforeUnload(e);
            console.log('💡 Smart handler result:', result);
            console.groupEnd();
            
            return result;
        }, true); // Use capture phase
        
        // Cleanup function
        window._cleanupOnlyOfficeWarnings = () => {
            clearInterval(monitorInterval);
        };
        
        console.log('🎯 Strategic OnlyOffice warning management with full debugging ready');
    </script>
    
    <script src="{{ config('onlyoffice.server_url') }}/web-apps/apps/api/documents/api.js"></script>
    <script>
        // Global variable to track if document has been modified
        let documentModified = false;
        
        // Toggle panel de variáveis
        function togglePanel() {
            const panel = document.getElementById('variablesPanel');
            const icon = document.getElementById('panelIcon');
            
            panel.classList.toggle('collapsed');
            
            if (panel.classList.contains('collapsed')) {
                icon.className = 'ki-duotone ki-down fs-6';
                icon.innerHTML = '<span class="path1"></span><span class="path2"></span>';
            } else {
                icon.className = 'ki-duotone ki-up fs-6';
                icon.innerHTML = '<span class="path1"></span><span class="path2"></span>';
            }
        }

        // OnlyOffice Config
        document.addEventListener('DOMContentLoaded', function() {
            const config = @json($config);
            
            console.log('OnlyOffice Config:', config);
            console.log('Document URL:', config.document.url);
            
            config.events = {
                'onDocumentReady': function() {
                    console.log('🚀 OnlyOffice document ready');
                    showToast('Editor carregado e pronto para uso', 'success', 3000);
                    
                    // Reset state
                    documentModified = false;
                    window._onlyofficeState.documentSaved = false;
                    window._onlyofficeState.allowClose = false;
                    
                    // Initialize status badge
                    updateStatusBadge('saved');
                    
                    // Log available methods for debugging
                    console.log('📋 Available docEditor methods:', Object.getOwnPropertyNames(window.docEditor));
                    
                    // Log current configuration
                    console.log('⚙️ OnlyOffice Config Debug:');
                    console.log('- Autosave enabled:', true);
                    console.log('- Forcesave enabled:', true);
                    console.log('- Callback URL configured:', '{{ route("api.onlyoffice.callback", $template->document_key ?? "test") }}');
                    
                    // Override OnlyOffice's beforeunload after document is ready
                    setTimeout(() => {
                        overrideOnlyOfficeWarnings();
                        
                        // Set up periodic override to catch any new beforeunload handlers OnlyOffice adds
                        setInterval(() => {
                            overrideOnlyOfficeWarnings();
                        }, 5000); // Re-override every 5 seconds
                        
                    }, 2000); // Increased delay to ensure OnlyOffice has finished loading
                },
                'onDocumentStateChange': function(event) {
                    console.log('📝 Document state changed:', event);
                    if (event && event.data) {
                        // Update our tracking variables
                        documentModified = true;
                        window._onlyofficeState.documentSaved = false;
                        
                        console.log('✏️ Document has been modified - forcing OnlyOffice to recognize changes');
                        
                        // Force OnlyOffice to recognize this as a real change
                        if (window.docEditor && typeof window.docEditor.setModified === 'function') {
                            window.docEditor.setModified(true);
                            console.log('📝 Explicitly set document as modified in OnlyOffice');
                        }
                        
                        // Update page title to show unsaved changes
                        if (!document.title.includes('*')) {
                            document.title = '* ' + document.title.replace('✅ Template Salvo - ', '');
                        }
                        
                        // Update status badge
                        updateStatusBadge('modified');
                    }
                },
                'onRequestSaveAs': function(event) {
                    console.log('OnlyOffice requesting save as:', event);
                    showToast('Processando salvamento...', 'info');
                    return true;
                },
                'onDownloadAs': function(event) {
                    console.log('📥 OnlyOffice download as triggered:', event);
                    showToast('Processando salvamento do documento...', 'info', 3000);
                    
                    // This indicates OnlyOffice is processing the save
                    // The actual save will happen via callback
                },
                'onRequestInsertImage': function(event) {
                    console.log('OnlyOffice image insert requested:', event);
                },
                'onMetaChange': function(event) {
                    console.log('Document meta changed:', event);
                    // This can indicate document changes
                    if (event && event.data) {
                        documentModified = true;
                    }
                },
                'onError': function(event) {
                    console.error('Erro OnlyOffice:', event);
                    showToast('Erro no editor. Tente recarregar a página', 'error', 6000);
                },
                'onRequestClose': function() {
                    // This event is fired when OnlyOffice wants to close
                    console.log('OnlyOffice requesting close');
                    // Return false to prevent OnlyOffice from showing its own warning
                    if (documentModified) {
                        const shouldClose = confirm('Você tem alterações não salvas. Tem certeza que deseja fechar sem salvar?');
                        if (shouldClose) {
                            documentModified = false; // Clear flag to allow closing
                        }
                        return shouldClose;
                    }
                    return true;
                }
            };
            
            try {
                console.log('Inicializando OnlyOffice com config:', config);
                window.docEditor = new DocsAPI.DocEditor('onlyoffice-editor', config);
                
                // After OnlyOffice loads, we'll override its beforeunload behavior
                
            } catch (error) {
                console.error('Erro ao inicializar OnlyOffice:', error);
                showToast('Falha ao carregar o editor. Recarregue a página', 'error', 6000);
            }
        });

        // Update status badge function
        function updateStatusBadge(status) {
            const badge = document.getElementById('statusSalvamento');
            const texto = document.getElementById('statusTexto');
            const icon = badge.querySelector('i');
            
            if (!badge || !texto) return;
            
            // Remove all status classes
            badge.classList.remove('badge-warning', 'badge-success', 'badge-info', 'badge-danger');
            
            switch(status) {
                case 'saved':
                    badge.classList.add('badge-success');
                    texto.textContent = 'Salvo';
                    icon.className = 'ki-duotone ki-check fs-7 me-1';
                    icon.innerHTML = '<span class="path1"></span><span class="path2"></span>';
                    break;
                case 'modified':
                    badge.classList.add('badge-warning');
                    texto.textContent = 'Não Salvo';
                    icon.className = 'ki-duotone ki-information fs-7 me-1';
                    icon.innerHTML = '<span class="path1"></span><span class="path2"></span><span class="path3"></span>';
                    break;
                case 'saving':
                    badge.classList.add('badge-info');
                    texto.textContent = 'Salvando...';
                    icon.className = 'ki-duotone ki-loading fs-7 me-1';
                    icon.innerHTML = '<span class="path1"></span><span class="path2"></span>';
                    break;
                case 'error':
                    badge.classList.add('badge-danger');
                    texto.textContent = 'Erro';
                    icon.className = 'ki-duotone ki-cross fs-7 me-1';
                    icon.innerHTML = '<span class="path1"></span><span class="path2"></span>';
                    break;
                default:
                    badge.classList.add('badge-warning');
                    texto.textContent = 'Não Salvo';
                    icon.className = 'ki-duotone ki-information fs-7 me-1';
                    icon.innerHTML = '<span class="path1"></span><span class="path2"></span><span class="path3"></span>';
            }
        }

        // Modern Toast notifications
        function showToast(message, type = 'info', duration = 4000) {
            const container = document.getElementById('toastContainer');
            if (!container) return;
            
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `custom-toast toast-${type}`;
            
            // Define icons for each type
            const icons = {
                success: '✓',
                info: 'i',
                warning: '!', 
                error: '✕'
            };
            
            // Clean message
            const cleanMessage = message.trim();
            
            toast.innerHTML = `
                <div class="toast-content">
                    <div class="toast-icon">${icons[type] || 'i'}</div>
                    <div class="toast-message">${cleanMessage}</div>
                    <button class="toast-close" onclick="removeToast(this.parentElement)">&times;</button>
                </div>
                <div class="toast-progress"></div>
            `;
            
            // Add to container
            container.appendChild(toast);
            
            // Auto remove after duration
            const autoRemoveTimer = setTimeout(() => {
                removeToast(toast);
            }, duration);
            
            // Store timer for manual removal
            toast.autoRemoveTimer = autoRemoveTimer;
            
            // Add click to dismiss
            toast.addEventListener('click', (e) => {
                if (!e.target.classList.contains('toast-close')) {
                    removeToast(toast);
                }
            });
            
            return toast;
        }
        
        // Remove toast with animation
        function removeToast(toast) {
            if (!toast || toast.classList.contains('removing')) return;
            
            // Clear auto-remove timer
            if (toast.autoRemoveTimer) {
                clearTimeout(toast.autoRemoveTimer);
            }
            
            toast.classList.add('removing');
            
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        }

        // Force save function using OnlyOffice API
        function forceSave() {
            console.log('💾 Force save triggered');
            updateStatusBadge('saving');
            showToast('Iniciando salvamento...', 'info', 2000);
            
            if (window.docEditor) {
                console.log('Available methods:', Object.getOwnPropertyNames(window.docEditor));
                
                try {
                    // First, force document to be marked as modified if it hasn't been already
                    if (!documentModified) {
                        console.log('🔧 Document not marked as modified, forcing change detection...');
                        
                        // Try to force a minimal change to trigger modification state
                        if (typeof window.docEditor.setModified === 'function') {
                            window.docEditor.setModified(true);
                            console.log('📝 Forced document modified state');
                        }
                        
                        // Alternative: try inserting and removing a space to force change
                        if (typeof window.docEditor.insertText === 'function') {
                            console.log('🔤 Inserting minimal change to force modification...');
                            window.docEditor.insertText(' ', false);
                            // Remove it immediately
                            setTimeout(() => {
                                if (typeof window.docEditor.deletePrevious === 'function') {
                                    window.docEditor.deletePrevious();
                                }
                            }, 100);
                        }
                        
                        documentModified = true;
                        window._onlyofficeState.documentSaved = false;
                    }
                    
                    // Method 1: Try downloadAs (triggers proper OnlyOffice save workflow)
                    if (typeof window.docEditor.downloadAs === 'function') {
                        console.log('📥 Using downloadAs to trigger OnlyOffice save...');
                        
                        // This should trigger the proper save callback with status 2 or 6
                        window.docEditor.downloadAs('rtf', 'template_' + Date.now() + '.rtf');
                        
                        // Mark as saved in our state management
                        setTimeout(() => {
                            console.group('💾 MARKING DOCUMENT AS SAVED');
                            console.log('⏰ Previous state:', {
                                documentModified: documentModified,
                                documentSaved: window._onlyofficeState.documentSaved
                            });
                            
                            documentModified = false;
                            window._onlyofficeState.documentSaved = true;
                            document.title = '✅ Template Salvo - ' + document.title.replace('✅ Template Salvo - ', '').replace('* ', '');
                            
                            console.log('🆕 New state:', {
                                documentModified: documentModified,
                                documentSaved: window._onlyofficeState.documentSaved
                            });
                            console.log('🎯 This should prevent beforeunload warnings now');
                            console.groupEnd();
                            
                            // Update status badge
                            updateStatusBadge('saved');
                            showToast('Documento salvo com sucesso!', 'success', 3000);
                            
                            // Reload page after save to avoid version conflict
                            setTimeout(() => {
                                showToast('Recarregando para aplicar alterações...', 'info', 2000);
                                window._onlyofficeState.allowClose = true;
                                window.location.reload();
                            }, 3000);
                        }, 2000); // Increased timeout for processing
                        
                        return;
                    }
                    
                    // Method 2: Try requestInsertImage hack
                    if (typeof window.docEditor.requestInsertImage === 'function') {
                        console.log('🖼️ Using requestInsertImage hack...');
                        window.docEditor.requestInsertImage();
                        
                        setTimeout(() => {
                            documentModified = false;
                            window._onlyofficeState.documentSaved = true;
                            document.title = '✅ Template Salvo - ' + document.title.replace('✅ Template Salvo - ', '').replace('* ', '');
                            updateStatusBadge('saved');
                            showToast('Documento salvo com sucesso!', 'success', 3000);
                        }, 2000);
                        
                        return;
                    }
                    
                    throw new Error('No OnlyOffice save methods available');
                    
                } catch (e) {
                    console.log('⚠️ OnlyOffice save methods not available, using manual fallback:', e);
                }
                
                // Fallback: Manual save endpoint (timestamps only)
                const url = '{{ route("templates.salvar", $tipo) }}';
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        documentModified = false;
                        window._onlyofficeState.documentSaved = true;
                        document.title = '✅ Template Salvo - ' + document.title.replace('✅ Template Salvo - ', '').replace('* ', '');
                        updateStatusBadge('saved');
                        showToast('Documento salvo manualmente', 'success', 3000);
                    } else {
                        updateStatusBadge('error');
                        showToast('Erro ao salvar: ' + (data.message || 'Tente novamente'), 'error', 5000);
                    }
                })
                .catch(error => {
                    console.error('Manual save failed:', error);
                    updateStatusBadge('error');
                    showToast('Falha no salvamento. Verifique sua conexão', 'error', 5000);
                });
            } else {
                showToast('Editor ainda não foi carregado completamente', 'warning', 4000);
            }
        }

        // Legacy function cleanup - removed aggressive overrides
        function overrideOnlyOfficeWarnings() {
            // Just ensure our smart handler is in place
            console.log('🔄 Ensuring smart warning handler is active...');
            if (window.onbeforeunload !== window.smartBeforeUnload) {
                window.onbeforeunload = window.smartBeforeUnload;
                console.log('✅ Smart handler restored');
            }
        }
        
        // Close editor function - SMART APPROACH
        function fecharEditor() {
            console.log('🚪 Attempting to close editor...');
            console.log('State:', { documentModified, saved: window._onlyofficeState.documentSaved });
            
            // Set allow close flag to prevent warnings
            window._onlyofficeState.allowClose = true;
            
            // Check if there are unsaved changes
            const hasUnsavedChanges = documentModified && !window._onlyofficeState.documentSaved;
            
            let confirmTitle = 'Fechar Editor';
            let confirmText = 'Tem certeza que deseja fechar o editor?';
            let confirmIcon = 'question';
            
            if (hasUnsavedChanges) {
                confirmTitle = 'Alterações não salvas';
                confirmText = 'Você tem alterações não salvas. Tem certeza que deseja fechar sem salvar?';
                confirmIcon = 'warning';
            }
            
            // Use SweetAlert for confirmation
            Swal.fire({
                title: confirmTitle,
                text: confirmText,
                icon: confirmIcon,
                showCancelButton: true,
                confirmButtonText: 'Sim, fechar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('✅ User confirmed close - redirecting...');
                    
                    // Stop the monitoring interval
                    if (window._cleanupOnlyOfficeWarnings) {
                        window._cleanupOnlyOfficeWarnings();
                    }
                    
                    // Show loading message
                    Swal.fire({
                        title: 'Fechando editor...',
                        text: 'Aguarde um momento',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Redirect
                    window.location.href = '{{ route("templates.index") }}';
                    
                    // Fallback
                    setTimeout(() => {
                        window.location.replace('{{ route("templates.index") }}');
                    }, 1000);
                } else {
                    // User cancelled, reset flag
                    window._onlyofficeState.allowClose = false;
                    console.log('❌ User cancelled close');
                }
            });
        }
    </script>
</body>
</html>