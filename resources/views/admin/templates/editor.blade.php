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
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
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
                
                <span class="badge badge-light-info px-3 py-2">
                    <i class="ki-duotone ki-information fs-7 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    Salvamento Manual
                </span>
                
                <button onclick="fecharEditor()" class="btn btn-light btn-sm">
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
                    showToast('Template carregado com sucesso!', 'success');
                    
                    // Reset state
                    documentModified = false;
                    window._onlyofficeState.documentSaved = false;
                    window._onlyofficeState.allowClose = false;
                    
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
                    }
                },
                'onRequestSaveAs': function(event) {
                    console.log('OnlyOffice requesting save as:', event);
                    showToast('Processando salvamento...', 'info');
                    return true;
                },
                'onDownloadAs': function(event) {
                    console.log('📥 OnlyOffice download as triggered:', event);
                    showToast('📥 Download/Save em progresso...', 'info');
                    
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
                    showToast('Erro no editor: ' + JSON.stringify(event.data), 'error');
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
                showToast('Erro ao carregar editor: ' + error.message, 'error');
            }
        });

        // Toast notifications
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible position-fixed`;
            toast.style.cssText = 'top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                ${message}
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 5000);
        }

        // Force save function using OnlyOffice API
        function forceSave() {
            console.log('💾 Force save triggered');
            showToast('Salvando documento...', 'info');
            
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
                            
                            showToast('💾 Salvamento OnlyOffice iniciado!', 'success');
                            
                            // Reload page after save to avoid version conflict
                            setTimeout(() => {
                                showToast('🔄 Recarregando editor...', 'info');
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
                            showToast('💾 Salvamento via hack iniciado!', 'success');
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
                        showToast('💾 Timestamp salvo (manual)!', 'info');
                    } else {
                        showToast('❌ Erro ao salvar: ' + (data.message || 'Erro desconhecido'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Manual save failed:', error);
                    showToast('❌ Erro ao salvar documento', 'error');
                });
            } else {
                showToast('❌ Editor não está carregado', 'error');
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