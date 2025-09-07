@extends('components.layouts.app')

@section('title', 'PDF - Proposição ' . $proposicao->id)

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    📄 PDF - Proposição {{ $proposicao->id }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('proposicoes.show', $proposicao->id) }}" class="text-muted text-hover-primary">Proposição {{ $proposicao->id }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Visualizar PDF</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('proposicoes.show', $proposicao->id) }}" class="btn btn-sm btn-light">
                    <i class="ki-duotone ki-arrow-left fs-2"></i>
                    Voltar
                </a>
                <button class="btn btn-sm btn-primary" onclick="window.initializeDebugLogger()">
                    <i class="ki-duotone ki-setting-2 fs-2"></i>
                    Debug Logger
                </button>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!--begin::PDF Viewer Card-->
            <div class="card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h3>📄 Visualizando PDF da Proposição {{ $proposicao->id }}</h3>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge badge-light-primary">Status: {{ $proposicao->status }}</span>
                            <a href="{{ route('proposicoes.serve-pdf', $proposicao->id) }}" 
                               target="_blank" 
                               class="btn btn-sm btn-light-danger"
                               onclick="logDirectPDFAccess()">
                                <i class="ki-duotone ki-file-down fs-2"></i>
                                Abrir PDF Direto
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!--begin::PDF Embed-->
                    <div class="position-relative">
                        <iframe 
                            id="pdf-frame"
                            src="{{ route('proposicoes.serve-pdf', $proposicao->id) }}"
                            width="100%" 
                            height="800px"
                            style="border: none;"
                            onload="logPDFLoad()"
                            onerror="logPDFError()">
                        </iframe>
                        
                        <!--begin::Loading Overlay-->
                        <div id="pdf-loading" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-light" style="z-index: 10;">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <div class="mt-3">
                                    <h5>Carregando PDF...</h5>
                                    <p class="text-muted">Aguarde enquanto o documento é processado</p>
                                </div>
                            </div>
                        </div>
                        <!--end::Loading Overlay-->
                    </div>
                    <!--end::PDF Embed-->
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <small>
                                <strong>Proposição:</strong> {{ $proposicao->ementa ?? 'Sem ementa' }} |
                                <strong>Autor:</strong> {{ $proposicao->autor->name ?? 'N/A' }} |
                                <strong>Criado:</strong> {{ $proposicao->created_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('proposicoes.debug-pdf', $proposicao->id) }}" class="btn btn-sm btn-light-info">
                                <i class="ki-duotone ki-information fs-2"></i>
                                Info Debug
                            </a>
                            <button class="btn btn-sm btn-light-warning" onclick="reloadPDF()">
                                <i class="ki-duotone ki-arrows-circle fs-2"></i>
                                Recarregar PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::PDF Viewer Card-->

            <!--begin::Debug Info Card-->
            <div class="card card-flush mt-5">
                <div class="card-header">
                    <div class="card-title">
                        <h3>🔧 Informações de Debug</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="ki-duotone ki-information-5 fs-1 me-2"></i>Debug Logger Ativo</h5>
                        <p class="mb-0">Esta página tem o Debug Logger integrado. Todas as interações com o PDF serão registradas automaticamente.</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="ki-duotone ki-file-pdf fs-1 text-danger me-3"></i>
                                <div>
                                    <div class="fw-bold">URL do PDF:</div>
                                    <div class="text-muted small">{{ route('proposicoes.serve-pdf', $proposicao->id) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="ki-duotone ki-time fs-1 text-primary me-3"></i>
                                <div>
                                    <div class="fw-bold">Carregado em:</div>
                                    <div class="text-muted small" id="load-time">Aguardando...</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="ki-duotone ki-chart-simple fs-1 text-success me-3"></i>
                                <div>
                                    <div class="fw-bold">Status:</div>
                                    <div class="text-muted small" id="pdf-status">Carregando...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Debug Info Card-->

        </div>
    </div>
    <!--end::Content-->
</div>

<script>
let pdfLoadStartTime = Date.now();

// Log quando a página carrega
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔴 PDF VIEWER: Página carregada', {
        proposicao_id: {{ $proposicao->id }},
        status: '{{ $proposicao->status }}',
        pdf_url: '{{ route('proposicoes.serve-pdf', $proposicao->id) }}',
        timestamp: new Date().toISOString(),
        page_type: 'pdf_viewer'
    });

    // Registrar no debug logger se estiver ativo
    if (window.debugLoggerApp && window.debugLoggerApp._instance) {
        const debugInstance = window.debugLoggerApp._instance.proxy;
        if (debugInstance.isRecording) {
            debugInstance.logAction('page_load', 'PDF Viewer carregado - Proposição {{ $proposicao->id }}', {
                proposicao_id: {{ $proposicao->id }},
                status: '{{ $proposicao->status }}',
                pdf_url: '{{ route('proposicoes.serve-pdf', $proposicao->id) }}',
                action_type: 'pdf_viewer_load'
            });
        }
    }
});

function logPDFLoad() {
    const loadTime = Date.now() - pdfLoadStartTime;
    console.log('✅ PDF VIEWER: PDF carregado com sucesso', {
        proposicao_id: {{ $proposicao->id }},
        load_time_ms: loadTime,
        timestamp: new Date().toISOString()
    });

    // Atualizar interface
    document.getElementById('pdf-loading').style.display = 'none';
    document.getElementById('load-time').textContent = loadTime + 'ms';
    document.getElementById('pdf-status').textContent = 'Carregado com sucesso';
    document.getElementById('pdf-status').className = 'text-success small';

    // Registrar no debug logger
    if (window.debugLoggerApp && window.debugLoggerApp._instance) {
        const debugInstance = window.debugLoggerApp._instance.proxy;
        if (debugInstance.isRecording) {
            debugInstance.logAction('pdf_load', 'PDF carregado com sucesso em ' + loadTime + 'ms', {
                proposicao_id: {{ $proposicao->id }},
                load_time_ms: loadTime,
                action_type: 'pdf_load_success'
            });
        }
    }
}

function logPDFError() {
    console.error('❌ PDF VIEWER: Erro ao carregar PDF', {
        proposicao_id: {{ $proposicao->id }},
        pdf_url: '{{ route('proposicoes.serve-pdf', $proposicao->id) }}',
        timestamp: new Date().toISOString()
    });

    // Atualizar interface
    document.getElementById('pdf-loading').style.display = 'none';
    document.getElementById('pdf-status').textContent = 'Erro ao carregar';
    document.getElementById('pdf-status').className = 'text-danger small';

    // Registrar no debug logger
    if (window.debugLoggerApp && window.debugLoggerApp._instance) {
        const debugInstance = window.debugLoggerApp._instance.proxy;
        if (debugInstance.isRecording) {
            debugInstance.logAction('pdf_error', 'Erro ao carregar PDF - Proposição {{ $proposicao->id }}', {
                proposicao_id: {{ $proposicao->id }},
                pdf_url: '{{ route('proposicoes.serve-pdf', $proposicao->id) }}',
                action_type: 'pdf_load_error',
                isError: true
            });
        }
    }
}

function logDirectPDFAccess() {
    console.log('🔗 PDF VIEWER: Acesso direto ao PDF solicitado', {
        proposicao_id: {{ $proposicao->id }},
        timestamp: new Date().toISOString()
    });

    // Registrar no debug logger
    if (window.debugLoggerApp && window.debugLoggerApp._instance) {
        const debugInstance = window.debugLoggerApp._instance.proxy;
        if (debugInstance.isRecording) {
            debugInstance.logAction('user_interaction', 'Clique em "Abrir PDF Direto"', {
                proposicao_id: {{ $proposicao->id }},
                action_type: 'direct_pdf_access'
            });
        }
    }
}

function reloadPDF() {
    console.log('🔄 PDF VIEWER: Recarregando PDF', {
        proposicao_id: {{ $proposicao->id }},
        timestamp: new Date().toISOString()
    });

    // Mostrar loading novamente
    document.getElementById('pdf-loading').style.display = 'flex';
    document.getElementById('pdf-status').textContent = 'Recarregando...';
    document.getElementById('pdf-status').className = 'text-muted small';
    
    // Resetar timer
    pdfLoadStartTime = Date.now();
    
    // Recarregar iframe
    const iframe = document.getElementById('pdf-frame');
    iframe.src = iframe.src;

    // Registrar no debug logger
    if (window.debugLoggerApp && window.debugLoggerApp._instance) {
        const debugInstance = window.debugLoggerApp._instance.proxy;
        if (debugInstance.isRecording) {
            debugInstance.logAction('user_interaction', 'PDF recarregado manualmente', {
                proposicao_id: {{ $proposicao->id }},
                action_type: 'pdf_reload'
            });
        }
    }
}
</script>
@endsection