@extends('layouts.app')

@section('title', $title ?? 'Visualizar Documento')

@push('styles')
<style>
    .document-viewer-container {
        height: calc(100vh - 100px);
        min-height: 600px;
    }
    
    .viewer-toolbar {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .viewer-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .viewer-actions {
        display: flex;
        gap: 10px;
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
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <div class="document-viewer-container">
        
        {{-- Toolbar do Visualizador --}}
        <div class="viewer-toolbar">
            <div class="viewer-info">
                <h5 class="mb-0">{{ $title }}</h5>
                
                <span class="badge badge-{{ $instancia->status === 'finalizado' ? 'success' : 'primary' }}">
                    {{ ucfirst($instancia->status) }}
                </span>
                <span class="text-muted">Versão {{ $instancia->versao }}</span>
                <span class="badge badge-light">
                    <i class="fas fa-eye"></i> Visualização
                </span>
            </div>
            
            <div class="viewer-actions">
                <a href="{{ route('onlyoffice.pdf.instancia', $instancia) }}" 
                   class="btn btn-outline-secondary btn-sm" 
                   title="Exportar para PDF">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
                
                <a href="{{ route('onlyoffice.file.instancia', $instancia) }}" 
                   class="btn btn-outline-primary btn-sm" 
                   title="Download do Arquivo"
                   download>
                    <i class="fas fa-download"></i> Download
                </a>
                
                <button type="button" 
                        class="btn btn-outline-secondary btn-sm" 
                        onclick="window.close()" 
                        title="Fechar Visualizador">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>
        </div>
        
        {{-- Container do Visualizador ONLYOFFICE --}}
        <div id="onlyoffice-viewer" style="height: calc(100% - 60px); position: relative;">
            <div class="loading-overlay" id="viewer-loading">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Carregando...</span>
                    </div>
                    <p class="mt-2">Carregando documento...</p>
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
    let docViewer = null;
    
    // Configurar eventos do ONLYOFFICE para visualização
    config.events = {
        'onAppReady': onAppReady,
        'onDocumentReady': onDocumentReady,
        'onError': onError,
        'onRequestClose': onRequestClose
    };
    
    // Inicializar visualizador
    try {
        docViewer = new DocsAPI.DocEditor('onlyoffice-viewer', config);
        console.log('ONLYOFFICE Viewer inicializado com sucesso');
    } catch (error) {
        console.error('Erro ao inicializar ONLYOFFICE Viewer:', error);
        showError('Erro ao carregar o visualizador. Verifique se o ONLYOFFICE está funcionando.');
    }
    
    // Event handlers
    function onAppReady() {
        console.log('ONLYOFFICE Viewer carregado');
    }
    
    function onDocumentReady() {
        console.log('Documento carregado para visualização');
        hideLoading();
    }
    
    function onError(event) {
        console.error('Erro ONLYOFFICE Viewer:', event.data);
        showError('Erro no visualizador: ' + event.data);
    }
    
    function onRequestClose() {
        window.close();
    }
    
    // Utility functions
    function hideLoading() {
        const loading = document.getElementById('viewer-loading');
        if (loading) {
            loading.style.display = 'none';
        }
    }
    
    function showError(message) {
        console.error(message);
        alert('Erro: ' + message);
    }
});
</script>
@endpush