@extends('layouts.onlyoffice-standalone')

@section('title', $title)

@section('content')
<div id="onlyoffice-editor"></div>

<script type="text/javascript" src="{{ config('onlyoffice.server_url') }}/web-apps/apps/api/documents/api.js"></script>
<script>
    var config = @json($config);
    
    // Add error handling
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
        }
    };
    
    // Initialize OnlyOffice
    var docEditor = new DocsAPI.DocEditor("onlyoffice-editor", config);
    
    console.log('OnlyOffice config:', config);
</script>
@endsection