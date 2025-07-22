@extends('layouts.onlyoffice-standalone')

@section('title', $title)

@section('content')
<div style="position: fixed; top: 10px; right: 10px; z-index: 1000;">
    @if(isset($modelo))
    <button id="force-save-btn" onclick="forceSaveModelo()" style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; margin-right: 10px;">
        💾 Salvar Modelo
    </button>
    <button id="close-tab-btn" onclick="closeTab()" style="background: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold;">
        ✖️ Fechar Aba
    </button>
    @endif
</div>
<div id="onlyoffice-editor"></div>

<script type="text/javascript" src="{{ config('onlyoffice.server_url') }}/web-apps/apps/api/documents/api.js"></script>
<script>
    var config = @json($config);
    
    // Add error handling and custom events
    config.events = {
        'onAppReady': function() {
            console.log('OnlyOffice is ready');
            document.getElementById('loading').style.display = 'none';
            
            // Notificar aba pai que o editor carregou
            if (window.opener) {
                try {
                    localStorage.setItem('onlyoffice_loaded', Date.now());
                } catch (e) {
                    console.log('Could not notify parent window');
                }
            }
        },
        'onError': function(event) {
            console.error('OnlyOffice error:', event.data);
            alert('Erro no editor: ' + JSON.stringify(event.data));
        },
        'onDocumentReady': function() {
            console.log('Document is ready');
            // Notificar que o documento está pronto para edição
            try {
                if (window.opener && !window.opener.closed) {
                    localStorage.setItem('onlyoffice_document_ready', Date.now());
                    window.opener.postMessage({
                        type: 'onlyoffice_document_ready',
                        timestamp: Date.now()
                    }, '*');
                }
            } catch (e) {
                console.log('Could not notify parent about document ready:', e);
            }
        },
        'onRequestHistory': function() {
            console.log('History requested');
        },
        'onRequestHistoryData': function() {
            console.log('History data requested');
        },
        'onRequestHistoryClose': function() {
            console.log('History close requested');
        },
        'onInfo': function(event) {
            console.log('OnlyOffice info:', event.data);
            // Handle reload messages more gracefully
            if (event.data && event.data.type === 'reload') {
                console.log('Document reload requested, this is normal after save operations');
                // Check if document key has changed after save
                setTimeout(() => {
                    @if(isset($modelo))
                    const currentDocumentKey = config.document.key;
                    fetch('{{ route("documentos.modelos.last-update", $modelo) }}')
                        .then(response => response.json())
                        .then(data => {
                            // Check if document key has changed after save
                            if (data.document_key && data.document_key !== currentDocumentKey) {
                                console.log('Document key changed from', currentDocumentKey, 'to', data.document_key, '- refreshing editor...');
                                // Reload the current page to get new document key and prevent version conflicts
                                window.location.reload();
                            } else {
                                console.log('Document key unchanged, proceeding with normal notification');
                                // Normal notification to parent
                                try {
                                    if (window.opener && !window.opener.closed) {
                                        localStorage.setItem('onlyoffice_document_saved', Date.now());
                                        window.opener.postMessage({
                                            type: 'onlyoffice_document_saved',
                                            timestamp: Date.now()
                                        }, '*');
                                    }
                                } catch (e) {
                                    console.log('Could not notify parent about document save:', e);
                                }
                            }
                        })
                        .catch(error => {
                            console.log('Error checking for document key changes:', error);
                            // Fallback to normal notification
                            try {
                                if (window.opener && !window.opener.closed) {
                                    localStorage.setItem('onlyoffice_document_saved', Date.now());
                                    window.opener.postMessage({
                                        type: 'onlyoffice_document_saved',
                                        timestamp: Date.now()
                                    }, '*');
                                }
                            } catch (e) {
                                console.log('Could not notify parent about document save:', e);
                            }
                        });
                    @else
                    // For non-model documents, use normal notification
                    try {
                        if (window.opener && !window.opener.closed) {
                            localStorage.setItem('onlyoffice_document_saved', Date.now());
                            window.opener.postMessage({
                                type: 'onlyoffice_document_saved',
                                timestamp: Date.now()
                            }, '*');
                        }
                    } catch (e) {
                        console.log('Could not notify parent about document save:', e);
                    }
                    @endif
                }, 3000); // Aguardar 3 segundos para o callback processar e regenerar key
            }
        },
        'onWarning': function(event) {
            console.warn('OnlyOffice warning:', event.data);
            // Don't show warning popups for version changes
            if (event.data && event.data.type === 'version_changed') {
                console.log('Document version changed, editor will reload automatically');
                // Aguardar mais tempo para a versão ser processada
                setTimeout(() => {
                    try {
                        if (window.opener && !window.opener.closed) {
                            localStorage.setItem('onlyoffice_version_changed', Date.now());
                            window.opener.postMessage({
                                type: 'onlyoffice_version_changed',
                                timestamp: Date.now()
                            }, '*');
                        }
                    } catch (e) {
                        console.log('Could not notify parent about version change:', e);
                    }
                }, 3000); // Aguardar 3 segundos para versão ser processada
                return; // Don't show popup for version changes
            }
        }
    };
    
    // Initialize OnlyOffice with fresh configuration
    var docEditor = new DocsAPI.DocEditor("onlyoffice-editor", config);
    
    // Force refresh the document on page load to avoid cache issues
    window.addEventListener('beforeunload', function() {
        // Notificar página pai que o editor está sendo fechado
        try {
            if (window.opener && !window.opener.closed) {
                localStorage.setItem('onlyoffice_editor_closed', Date.now());
                window.opener.postMessage({
                    type: 'onlyoffice_editor_closed',
                    timestamp: Date.now()
                }, '*');
            }
        } catch (e) {
            console.log('Could not notify parent window:', e);
        }
        
        if (docEditor && docEditor.destroyEditor) {
            docEditor.destroyEditor();
        }
    });
    
    console.log('OnlyOffice config:', config);
    
    // Function to force save the document
    function forceSaveModelo() {
        @if(isset($modelo))
        const button = document.getElementById('force-save-btn');
        button.disabled = true;
        button.innerHTML = '⏳ Salvando...';
        
        // Use OnlyOffice API to trigger actual save
        if (docEditor) {
            try {
                // Use downloadAs to force save current content
                docEditor.downloadAs('rtf');
                console.log('OnlyOffice downloadAs triggered');
                
                button.innerHTML = '✅ Salvo!';
                button.style.background = '#28a745';
                setTimeout(() => {
                    button.innerHTML = '💾 Salvar Modelo';
                    button.disabled = false;
                    button.style.background = '#28a745';
                }, 2000);
                
            } catch(e) {
                console.log('OnlyOffice API save failed:', e);
                
                button.innerHTML = '❌ Erro!';
                button.style.background = '#dc3545';
                setTimeout(() => {
                    button.innerHTML = '💾 Salvar Modelo';
                    button.disabled = false;
                    button.style.background = '#28a745';
                }, 3000);
            }
        } else {
            button.innerHTML = '❌ Editor não disponível!';
            button.style.background = '#dc3545';
            setTimeout(() => {
                button.innerHTML = '💾 Salvar Modelo';
                button.disabled = false;
                button.style.background = '#28a745';
            }, 3000);
        }
        @endif
    }
    
    // Function to close the current tab
    function closeTab() {
        Swal.fire({
            title: 'Fechar Aba?',
            text: 'Tem certeza que deseja fechar esta aba? Certifique-se de que salvou o documento.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '✖️ Sim, fechar',
            cancelButtonText: '❌ Cancelar',
            customClass: {
                popup: 'swal2-popup-custom'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Notificar página pai que o editor está sendo fechado
                try {
                    if (window.opener && !window.opener.closed) {
                        localStorage.setItem('onlyoffice_editor_closed', Date.now());
                        // Tentar comunicação direta também
                        window.opener.postMessage({
                            type: 'onlyoffice_editor_closed',
                            timestamp: Date.now()
                        }, '*');
                    }
                } catch (e) {
                    console.log('Could not notify parent window:', e);
                }
                window.close();
            }
        });
    }
</script>
@endsection