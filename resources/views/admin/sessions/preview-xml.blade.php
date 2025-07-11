@extends('components.layouts.app')

@section('title', "Preview XML - {$session['numero']}ª Sessão/{$session['ano']}")

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Preview XML - {{ ucfirst(str_replace('_', ' ', $document_type)) }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.sessions.index') }}" class="text-muted text-hover-primary">Sessões</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.sessions.show', $session['id']) }}" class="text-muted text-hover-primary">{{ $session['numero'] }}ª Sessão</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Preview XML</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-sm fw-bold btn-light-primary" onclick="copyXmlToClipboard()">
                    <i class="ki-duotone ki-copy fs-2"></i>
                    Copiar XML
                </button>
                <button type="button" class="btn btn-sm fw-bold btn-primary" onclick="exportXml()">
                    <i class="ki-duotone ki-file-down fs-2"></i>
                    Exportar XML
                </button>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="ki-duotone ki-check-circle fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="ki-duotone ki-cross-circle fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ session('error') }}
                </div>
            @endif
            
            <!--begin::Row-->
            <div class="row g-5 g-xl-8 mb-xl-8">
                <!--begin::Col-->
                <div class="col-xl-4">
                    <!--begin::Statistics Widget 5-->
                    <div class="card bg-body hoverable card-xl-stretch mb-5 mb-xl-8">
                        <div class="card-body">
                            <i class="ki-duotone ki-calendar fs-2x text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                                {{ $session['numero'] }}ª Sessão
                            </div>
                            <div class="fw-semibold text-gray-400">{{ $session['tipo_descricao'] }} de {{ $session['ano'] }}</div>
                        </div>
                    </div>
                    <!--end::Statistics Widget 5-->
                </div>
                <!--end::Col-->
                
                <!--begin::Col-->
                <div class="col-xl-4">
                    <!--begin::Statistics Widget 5-->
                    <div class="card bg-body hoverable card-xl-stretch mb-5 mb-xl-8">
                        <div class="card-body">
                            <i class="ki-duotone ki-document fs-2x text-info">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                                {{ ucfirst(str_replace('_', ' ', $document_type)) }}
                            </div>
                            <div class="fw-semibold text-gray-400">Tipo de Documento</div>
                        </div>
                    </div>
                    <!--end::Statistics Widget 5-->
                </div>
                <!--end::Col-->
                
                <!--begin::Col-->
                <div class="col-xl-4">
                    <!--begin::Statistics Widget 5-->
                    <div class="card bg-body hoverable card-xl-stretch mb-5 mb-xl-8">
                        <div class="card-body">
                            <i class="ki-duotone ki-notepad fs-2x text-success">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                                {{ $matter_count }}
                            </div>
                            <div class="fw-semibold text-gray-400">Matérias Incluídas</div>
                        </div>
                    </div>
                    <!--end::Statistics Widget 5-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
            
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <h2>Conteúdo do XML</h2>
                    </div>
                    <!--end::Card title-->
                    
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-sm btn-light-secondary me-2" onclick="downloadXml()">
                            <i class="ki-duotone ki-file-down fs-2"></i>
                            Download
                        </button>
                        <button type="button" class="btn btn-sm btn-light-primary" onclick="printXml()">
                            <i class="ki-duotone ki-printer fs-2"></i>
                            Imprimir
                        </button>
                    </div>
                    <!--end::Card toolbar-->
                </div>
                <!--end::Card header-->
                
                <!--begin::Card body-->
                <div class="card-body">
                    <!--begin::Code block-->
                    <div class="position-relative">
                        <pre class="bg-light-dark rounded p-5" style="max-height: 600px; overflow-y: auto; font-family: 'Courier New', monospace; font-size: 12px; line-height: 1.4;"><code id="xml-content">{{ $xml }}</code></pre>
                        
                        <!--begin::Copy button-->
                        <button type="button" class="btn btn-sm btn-icon btn-light position-absolute top-0 end-0 mt-3 me-3" onclick="copyXmlToClipboard()" title="Copiar XML">
                            <i class="ki-duotone ki-copy fs-2"></i>
                        </button>
                        <!--end::Copy button-->
                    </div>
                    <!--end::Code block-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
            
            <!--begin::Actions card-->
            <div class="card mt-5">
                <div class="card-body text-center">
                    <h3 class="mb-5">Ações de Exportação</h3>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-light-primary" onclick="copyXmlToClipboard()">
                            <i class="ki-duotone ki-copy fs-2"></i>
                            Copiar para Área de Transferência
                        </button>
                        <button type="button" class="btn btn-success" onclick="exportXml()" id="export-btn">
                            <span class="indicator-label">
                                <i class="ki-duotone ki-file-up fs-2"></i>
                                Confirmar Exportação
                            </span>
                            <span class="indicator-progress" style="display: none;">
                                Exportando... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                        <a href="{{ route('admin.sessions.show', $session['id']) }}" class="btn btn-light">
                            <i class="ki-duotone ki-arrow-left fs-2"></i>
                            Voltar à Sessão
                        </a>
                    </div>
                </div>
            </div>
            <!--end::Actions card-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

@push('scripts')
<script>
const sessionId = {{ $session['id'] }};
const documentType = '{{ $document_type }}';
const xmlContent = @json($xml);

function copyXmlToClipboard() {
    const xmlElement = document.getElementById('xml-content');
    const textArea = document.createElement('textarea');
    textArea.value = xmlElement.textContent;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand('copy');
    document.body.removeChild(textArea);
    
    // Show success message
    Swal.fire({
        icon: 'success',
        title: 'Copiado!',
        text: 'XML copiado para a área de transferência',
        timer: 2000,
        showConfirmButton: false
    });
}

function downloadXml() {
    const filename = `sessao_${sessionId}_${documentType}_${new Date().toISOString().split('T')[0]}.xml`;
    const element = document.createElement('a');
    element.setAttribute('href', 'data:text/xml;charset=utf-8,' + encodeURIComponent(xmlContent));
    element.setAttribute('download', filename);
    element.style.display = 'none';
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
}

function printXml() {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>XML - Sessão ${sessionId} - ${documentType}</title>
                <style>
                    body { font-family: 'Courier New', monospace; font-size: 12px; line-height: 1.4; }
                    pre { white-space: pre-wrap; word-wrap: break-word; }
                </style>
            </head>
            <body>
                <h2>{{ $session['numero'] }}ª Sessão {{ $session['tipo_descricao'] }} de {{ $session['ano'] }}</h2>
                <h3>Documento: {{ ucfirst(str_replace('_', ' ', $document_type)) }}</h3>
                <hr>
                <pre>${xmlContent}</pre>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function exportXml() {
    const exportBtn = document.getElementById('export-btn');
    const indicator = exportBtn.querySelector('.indicator-label');
    const progress = exportBtn.querySelector('.indicator-progress');
    
    exportBtn.disabled = true;
    indicator.style.display = 'none';
    progress.style.display = 'inline-block';
    
    fetch(`{{ route('admin.sessions.export-xml', $session['id']) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            xml: xmlContent,
            document_type: documentType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: 'XML exportado com sucesso para o sistema externo',
                confirmButtonText: 'Voltar à Sessão'
            }).then(() => {
                window.location.href = `{{ route('admin.sessions.show', $session['id']) }}`;
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: data.message || 'Erro ao exportar XML'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: 'Erro de comunicação com o servidor'
        });
    })
    .finally(() => {
        exportBtn.disabled = false;
        indicator.style.display = 'inline-block';
        progress.style.display = 'none';
    });
}

// Format XML for better display
document.addEventListener('DOMContentLoaded', function() {
    const xmlElement = document.getElementById('xml-content');
    
    // Add syntax highlighting classes if available
    if (typeof Prism !== 'undefined') {
        xmlElement.classList.add('language-xml');
        Prism.highlightElement(xmlElement);
    }
});
</script>

<!-- Include SweetAlert2 for better alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
@endsection