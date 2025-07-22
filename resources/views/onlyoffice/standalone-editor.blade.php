@extends('layouts.onlyoffice-standalone')

@section('title', $title)

@section('content')
<div style="position: fixed; top: 10px; right: 10px; z-index: 1000;">
    @if(isset($modelo))
    <button id="force-save-btn" onclick="forceSaveModelo()" style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold;">
        💾 Salvar Modelo
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
            }
        },
        'onWarning': function(event) {
            console.warn('OnlyOffice warning:', event.data);
            // Don't show warning popups for version changes
            if (event.data && event.data.type === 'version_changed') {
                console.log('Document version changed, editor will reload automatically');
                return; // Don't show popup for version changes
            }
        }
    };
    
    // Initialize OnlyOffice with fresh configuration
    var docEditor = new DocsAPI.DocEditor("onlyoffice-editor", config);
    
    // Force refresh the document on page load to avoid cache issues
    window.addEventListener('beforeunload', function() {
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
</script>
@endsection