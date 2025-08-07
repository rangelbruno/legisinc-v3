@extends('components.layouts.onlyoffice')

@section('title', 'Editar Proposição - Legislativo')

@section('editor-title')
    Proposição #{{ $proposicao->id }} - {{ $proposicao->tipo_formatado }} - {{ $proposicao->autor->name }}
@endsection

@section('back-url', route('proposicoes.show', $proposicao))

@section('toolbar-actions')
    <button type="button" class="btn btn-success btn-sm" id="btn-salvar-onlyoffice">
        <i class="fas fa-save me-1"></i>Salvar
    </button>
@endsection

@section('content')
<div id="onlyoffice-container" style="height: 100%; width: 100%;"></div>
@endsection

@push('styles')
<style>
    #onlyoffice-container {
        width: 100%;
        height: 100%;
    }
    
    .btn-pulse {
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(25, 135, 84, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(25, 135, 84, 0);
        }
    }
</style>
@endpush

@push('scripts')
<script type="text/javascript" src="{{ config('app.env') === 'local' ? 'http://localhost:8080' : config('onlyoffice.server_url') }}/web-apps/apps/api/documents/api.js"></script>
<script type="text/javascript">
    let docEditor = null;
    
    window.onload = function() {
        console.log('=== OnlyOffice Editor Initialization ===');
        
        // Verificar se a API do OnlyOffice foi carregada
        if (typeof DocsAPI === 'undefined') {
            console.error('OnlyOffice API não carregada!');
            toastr.error('Erro ao carregar o editor OnlyOffice. Verifique se o servidor está rodando em http://localhost:8080');
            
            const container = document.getElementById('onlyoffice-container');
            if (container) {
                container.innerHTML = 
                    '<div class="d-flex align-items-center justify-content-center h-100">' +
                    '<div class="text-center">' +
                    '<div class="alert alert-danger">' +
                    '<h4>Erro ao carregar o editor OnlyOffice</h4>' +
                    '<p>Verifique se o servidor OnlyOffice está rodando na porta 8080.</p>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
            }
            return;
        }
        
        const config = @json($config);
        
        // Log da configuração para debug
        console.log('OnlyOffice Config:', config);
        console.log('Document URL:', config.document.url);
        console.log('Callback URL:', config.editorConfig.callbackUrl);
        
        try {
            // Inicializar editor
            docEditor = new DocsAPI.DocEditor("onlyoffice-container", config);
            console.log('Editor inicializado');
            
            // Aguardar a criação do editor antes de adicionar eventos
            setTimeout(function() {
                if (docEditor && typeof docEditor.attachEvent === 'function') {
                    // Usar attachEvent em vez de on
                    docEditor.attachEvent('onDocumentReady', function() {
                        console.log('OnlyOffice Editor Ready');
                        toastr.success('Editor carregado com sucesso');
                    });
                    
                    docEditor.attachEvent('onError', function(event) {
                        console.error('OnlyOffice Error:', event);
                        toastr.error('Erro no editor: ' + (event.data ? event.data.errorDescription : 'Erro desconhecido'));
                    });
                    
                    docEditor.attachEvent('onDocumentStateChange', function(event) {
                        console.log('Document State Changed:', event.data);
                        const saveBtn = document.getElementById('btn-salvar-onlyoffice');
                        if (saveBtn) {
                            if (event.data && event.data.modified) {
                                saveBtn.classList.add('btn-pulse');
                            } else {
                                saveBtn.classList.remove('btn-pulse');
                            }
                        }
                    });
                } else if (docEditor && typeof docEditor.on === 'function') {
                    // Fallback para versões que usam on
                    docEditor.on('onReady', function() {
                        console.log('OnlyOffice Editor Ready');
                        toastr.success('Editor carregado com sucesso');
                    });
                    
                    docEditor.on('onError', function(event) {
                        console.error('OnlyOffice Error:', event);
                        toastr.error('Erro no editor: ' + (event.data ? event.data.errorDescription : 'Erro desconhecido'));
                    });
                    
                    docEditor.on('onDocumentStateChange', function(event) {
                        console.log('Document State Changed:', event.data);
                        const saveBtn = document.getElementById('btn-salvar-onlyoffice');
                        if (saveBtn) {
                            if (event.data && event.data.modified) {
                                saveBtn.classList.add('btn-pulse');
                            } else {
                                saveBtn.classList.remove('btn-pulse');
                            }
                        }
                    });
                }
            }, 1000);
            
        } catch (error) {
            console.error('Erro ao inicializar editor:', error);
            toastr.error('Erro ao inicializar o editor: ' + error.message);
            
            const container = document.getElementById('onlyoffice-container');
            if (container) {
                container.innerHTML = 
                    '<div class="d-flex align-items-center justify-content-center h-100">' +
                    '<div class="text-center">' +
                    '<div class="alert alert-danger">' +
                    '<h4>Erro ao inicializar o editor</h4>' +
                    '<p>' + error.message + '</p>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
            }
        }
        
        // Botão salvar manual
        const saveBtn = document.getElementById('btn-salvar-onlyoffice');
        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                if (docEditor && typeof docEditor.downloadAs === 'function') {
                    docEditor.downloadAs();
                    toastr.info('Iniciando download...');
                } else {
                    toastr.warning('Editor não está pronto ou não suporta download');
                }
            });
        }
        
        // Salvar antes de sair
        window.addEventListener('beforeunload', function(e) {
            if (docEditor && typeof docEditor.isDocumentModified === 'function') {
                try {
                    if (docEditor.isDocumentModified()) {
                        e.preventDefault();
                        e.returnValue = 'Existem alterações não salvas. Deseja realmente sair?';
                    }
                } catch (err) {
                    console.log('Não foi possível verificar se o documento foi modificado');
                }
            }
        });
    };
</script>
@endpush