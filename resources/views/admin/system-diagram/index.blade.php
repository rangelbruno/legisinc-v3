@extends('components.layouts.app')

@section('title', 'Arquitetura do Sistema')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    {{-- Toolbar --}}
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            {{-- Page title --}}
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Arquitetura do Sistema
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Admin</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Arquitetura do Sistema</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            {{-- Statistics Cards --}}
            <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                {{-- Total Controllers --}}
                <div class="col-md-2">
                    <div class="card card-flush h-md-100 clickable-card" onclick="showDetailList('controllers')" style="cursor: pointer;">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center mb-5">
                                <span class="svg-icon svg-icon-2x svg-icon-primary me-3">
                                    <i class="ki-duotone ki-code fs-2x text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </span>
                                <div>
                                    <div class="fs-2 fw-bold text-gray-900">{{ $stats['total_controllers'] }}</div>
                                    <div class="fs-7 fw-semibold text-gray-500">Controllers</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Views --}}
                <div class="col-md-2">
                    <div class="card card-flush h-md-100 clickable-card" onclick="showDetailList('views')" style="cursor: pointer;">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center mb-5">
                                <span class="svg-icon svg-icon-2x svg-icon-success me-3">
                                    <i class="ki-duotone ki-screen fs-2x text-success">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </span>
                                <div>
                                    <div class="fs-2 fw-bold text-gray-900">{{ $stats['total_views'] }}</div>
                                    <div class="fs-7 fw-semibold text-gray-500">Views</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Routes --}}
                <div class="col-md-2">
                    <div class="card card-flush h-md-100 clickable-card" onclick="showDetailList('routes')" style="cursor: pointer;">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center mb-5">
                                <span class="svg-icon svg-icon-2x svg-icon-warning me-3">
                                    <i class="ki-duotone ki-route fs-2x text-warning">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <div>
                                    <div class="fs-2 fw-bold text-gray-900">{{ $stats['total_routes'] }}</div>
                                    <div class="fs-7 fw-semibold text-gray-500">Rotas</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Services --}}
                <div class="col-md-2">
                    <div class="card card-flush h-md-100 clickable-card" onclick="showDetailList('services')" style="cursor: pointer;">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center mb-5">
                                <span class="svg-icon svg-icon-2x svg-icon-info me-3">
                                    <i class="ki-duotone ki-setting-3 fs-2x text-info">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                    </i>
                                </span>
                                <div>
                                    <div class="fs-2 fw-bold text-gray-900">{{ $stats['total_services'] }}</div>
                                    <div class="fs-7 fw-semibold text-gray-500">Services</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Diagrams --}}
                <div class="col-md-2">
                    <div class="card card-flush h-md-100 clickable-card" onclick="showDetailList('diagrams')" style="cursor: pointer;">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center mb-5">
                                <span class="svg-icon svg-icon-2x svg-icon-danger me-3">
                                    <i class="ki-duotone ki-chart-pie-3 fs-2x text-danger">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                                <div>
                                    <div class="fs-2 fw-bold text-gray-900">{{ $stats['total_diagrams'] }}</div>
                                    <div class="fs-7 fw-semibold text-gray-500">Diagramas</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Categories --}}
                <div class="col-md-2">
                    <div class="card card-flush h-md-100 clickable-card" onclick="showDetailList('categories')" style="cursor: pointer;">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center mb-5">
                                <span class="svg-icon svg-icon-2x svg-icon-dark me-3">
                                    <i class="ki-duotone ki-category fs-2x text-dark">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </span>
                                <div>
                                    <div class="fs-2 fw-bold text-gray-900">{{ $stats['total_categories'] }}</div>
                                    <div class="fs-7 fw-semibold text-gray-500">Categorias</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Diagrams Navigation --}}
            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Diagramas e Fluxos do Sistema</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Arquitetura completa e fluxos operacionais</span>
                    </h3>
                    <div class="card-toolbar">
                        <div class="btn-group me-2" role="group">
                            <button type="button" class="btn btn-sm btn-light-info" onclick="downloadAllDiagrams('png')">
                                <i class="ki-duotone ki-cloud-download fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Baixar Todos PNG
                            </button>
                            <button type="button" class="btn btn-sm btn-light-warning" onclick="downloadAllDiagrams('pdf')">
                                <i class="ki-duotone ki-file-down fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Baixar Todos PDF
                            </button>
                        </div>

                        <button type="button" class="btn btn-sm btn-light-primary" id="expandAllBtn">
                            <i class="ki-duotone ki-arrows-circle fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Expandir Todos
                        </button>
                        <button type="button" class="btn btn-sm btn-light-danger ms-2" id="collapseAllBtn">
                            <i class="ki-duotone ki-arrow-circle-right fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Recolher Todos
                        </button>
                    </div>
                </div>
                <div class="card-body py-3">
                    @if(count($diagramsByCategory) > 0)
                        {{-- Category Navigation --}}
                        <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6">
                            @php $categoryIndex = 0; @endphp
                            @foreach($diagramsByCategory as $categoryName => $categoryDiagrams)
                            <li class="nav-item">
                                <a class="nav-link {{ $categoryIndex === 0 ? 'active' : '' }}"
                                   data-bs-toggle="tab"
                                   href="#category-{{ \Str::slug($categoryName) }}">
                                    {{ $categoryName }}
                                    <span class="badge badge-light-primary ms-2">{{ count($categoryDiagrams) }}</span>
                                </a>
                            </li>
                            @php $categoryIndex++; @endphp
                            @endforeach
                        </ul>

                        {{-- Category Content --}}
                        <div class="tab-content" id="categoriesTabContent">
                            @php $categoryIndex = 0; @endphp
                            @foreach($diagramsByCategory as $categoryName => $categoryDiagrams)
                            <div class="tab-pane fade {{ $categoryIndex === 0 ? 'show active' : '' }}"
                                 id="category-{{ \Str::slug($categoryName) }}"
                                 role="tabpanel">

                                {{-- Sub Navigation for Diagrams within Category --}}
                                @if(count($categoryDiagrams) > 1)
                                <ul class="nav nav-pills nav-pills-custom mb-4">
                                    @foreach($categoryDiagrams as $subIndex => $diagram)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $subIndex === 0 ? 'active' : '' }}"
                                           data-bs-toggle="tab"
                                           href="#{{ $diagram['id'] }}-tab">
                                            {{ str_replace(['📋 ', '📑 ', '🔏 ', '🔄 '], '', $diagram['title']) }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                                @endif

                                {{-- Diagrams Content --}}
                                <div class="tab-content">
                                    @foreach($categoryDiagrams as $subIndex => $diagram)
                                    <div class="tab-pane fade {{ $subIndex === 0 ? 'show active' : '' }}"
                                         id="{{ $diagram['id'] }}-tab"
                                         role="tabpanel">
                                        <div class="card card-flush">
                                            <div class="card-header">
                                                <h3 class="card-title">{{ $diagram['title'] }}</h3>
                                                <div class="card-toolbar">
                                                    <span class="badge badge-light-info me-2">{{ ucfirst($diagram['source']) }}</span>

                                                    {{-- Download Options --}}
                                                    <div class="btn-group me-2" role="group">
                                                        <button type="button"
                                                                class="btn btn-sm btn-light-success"
                                                                onclick="downloadDiagram('{{ $diagram['id'] }}', 'png')"
                                                                title="Baixar como PNG">
                                                            <i class="ki-duotone ki-document-download fs-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            PNG
                                                        </button>
                                                        <button type="button"
                                                                class="btn btn-sm btn-light-warning"
                                                                onclick="downloadDiagram('{{ $diagram['id'] }}', 'pdf')"
                                                                title="Baixar como PDF">
                                                            <i class="ki-duotone ki-file-down fs-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            PDF
                                                        </button>
                                                    </div>

                                                    <button type="button"
                                                            class="btn btn-sm btn-icon btn-light-primary"
                                                            onclick="toggleFullscreen('{{ $diagram['id'] }}')"
                                                            title="Tela cheia">
                                                        <i class="ki-duotone ki-maximize fs-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                            <span class="path4"></span>
                                                        </i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="mermaid-container" style="background: #f5f8fa; padding: 20px; border-radius: 10px; overflow: auto;">
                                                    <div class="mermaid" id="{{ $diagram['id'] }}">{{ $diagram['content'] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @php $categoryIndex++; @endphp
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="ki-duotone ki-information-5 fs-2x me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            Nenhum diagrama encontrado. Verifique se os arquivos de documentação estão presentes.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info Card --}}
            <div class="card">
                <div class="card-body">
                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6">
                        <i class="ki-duotone ki-information-5 fs-2tx text-info me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Sobre os Diagramas e Fluxos</h4>
                                <div class="fs-6 text-gray-700">
                                    Esta página centraliza todos os diagramas e fluxos do sistema LegisInc, incluindo:
                                    <ul class="mt-3 mb-0">
                                        <li><strong>Arquitetura Geral:</strong> <code>docs/project-overview.md</code></li>
                                        <li><strong>Fluxo de Proposições:</strong> <code>docs/FLUXO-PROPOSICOES-MERMAID.md</code></li>
                                        <li><strong>Fluxo de Documentos:</strong> <code>docs/FLUXO-DOCUMENTO-COMPLETO.md</code></li>
                                        <li><strong>Assinatura Digital:</strong> <code>docs/FLUXO-ASSINATURA-DIGITAL-PYHANKO.md</code></li>
                                        <li><strong>Fluxo Completo:</strong> <code>docs/FLUXO-COMPLETO-PROPOSICAO.md</code></li>
                                    </ul>

                                    <div class="mt-4">
                                        <h6 class="text-gray-900 fw-bold mb-2">🎯 Funcionalidades Disponíveis:</h6>
                                        <ul class="mb-3">
                                            <li>📱 <strong>Visualização Responsiva:</strong> Diagramas adaptam-se a qualquer tamanho de tela</li>
                                            <li>🖼️ <strong>Download Individual:</strong> Baixe qualquer diagrama como PNG ou PDF</li>
                                            <li>📦 <strong>Download em Lote:</strong> Baixe todos os diagramas de uma categoria como ZIP</li>
                                            <li>🔍 <strong>Tela Cheia:</strong> Visualize diagramas em modo fullscreen</li>
                                            <li>⚡ <strong>Renderização em Tempo Real:</strong> Sintaxe Mermaid processada automaticamente</li>
                                        </ul>

                                        <div class="alert alert-light-primary d-flex align-items-center mt-3">
                                            <i class="ki-duotone ki-information-5 fs-2 text-primary me-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            <div>
                                                <strong>Dica:</strong> Use os botões <span class="badge badge-light-success">PNG</span> e
                                                <span class="badge badge-light-warning">PDF</span> para baixar diagramas individuais,
                                                ou <span class="badge badge-light-info">Baixar Todos</span> para download em lote.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Mermaid JS --}}
<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>

{{-- Custom Mermaid Renderer with Validation --}}
<script src="{{ asset('js/mermaid-renderer.js') }}"></script>

{{-- Libraries for Image and PDF Export --}}
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // O MermaidRenderer vai lidar com toda a inicialização e renderização
    console.log('🎨 System Diagram page loaded - MermaidRenderer will handle diagram rendering');

    // Aguardar o MermaidRenderer terminar
    window.addEventListener('mermaid-rendering-complete', function(event) {
        const { successCount, errorCount } = event.detail;
        console.log(`📊 Diagrams rendered: ${successCount} success, ${errorCount} errors`);

        // Mostrar notificação se houver erros
        if (errorCount > 0) {
            console.warn(`⚠️ ${errorCount} diagramas falharam na renderização`);
        }
    });

    // Expand all diagrams
    document.getElementById('expandAllBtn')?.addEventListener('click', function() {
        document.querySelectorAll('.mermaid-container').forEach(function(container) {
            container.style.maxHeight = 'none';
            container.style.overflow = 'visible';
        });
    });

    // Collapse all diagrams
    document.getElementById('collapseAllBtn')?.addEventListener('click', function() {
        document.querySelectorAll('.mermaid-container').forEach(function(container) {
            container.style.maxHeight = '600px';
            container.style.overflow = 'auto';
        });
    });

    // Refresh diagrams button (for debugging)
    const refreshBtn = document.createElement('button');
    refreshBtn.className = 'btn btn-sm btn-light-secondary ms-2';
    refreshBtn.innerHTML = '<i class="ki-duotone ki-refresh fs-2"><span class="path1"></span><span class="path2"></span></i> Recarregar';
    refreshBtn.onclick = function() {
        // Reset todos os diagramas
        document.querySelectorAll('.mermaid').forEach(el => {
            el.removeAttribute('data-processed');
            el.removeAttribute('data-error');
            el.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div>';
        });

        // Usar o novo renderer
        window.mermaidRenderer.renderAllDiagrams();
    };
    document.querySelector('.card-toolbar')?.appendChild(refreshBtn);

    // Handle window resize for responsive diagrams
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            // Reapply responsive styles to all SVG elements
            document.querySelectorAll('.mermaid svg').forEach(svg => {
                svg.style.maxWidth = '100%';
                svg.style.height = 'auto';
                svg.style.width = '100%';
            });
        }, 250);
    });
});

// Show detailed list for each card type
async function showDetailList(type) {
    try {
        // Add click animation
        event.currentTarget.classList.add('clicked');
        setTimeout(() => {
            event.currentTarget.classList.remove('clicked');
        }, 300);

        // Show loading
        Swal.fire({
            title: 'Carregando...',
            text: 'Buscando informações detalhadas',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Fetch data from API
        const response = await fetch(`{{ route('admin.system-diagram.index') }}/api/${type}`);

        if (!response.ok) {
            throw new Error(`Erro na requisição: ${response.status}`);
        }

        const data = await response.json();

        // Configure modal based on type
        let title, html, icon, iconColor;

        switch (type) {
            case 'controllers':
                title = `Controllers (${data.length})`;
                icon = 'ki-duotone ki-code';
                iconColor = 'text-primary';
                html = generateControllersHtml(data);
                break;
            case 'views':
                title = `Views (${data.length})`;
                icon = 'ki-duotone ki-screen';
                iconColor = 'text-success';
                html = generateViewsHtml(data);
                break;
            case 'routes':
                title = `Rotas (${data.length})`;
                icon = 'ki-duotone ki-route';
                iconColor = 'text-warning';
                html = generateRoutesHtml(data);
                break;
            case 'services':
                title = `Services (${data.length})`;
                icon = 'ki-duotone ki-setting-3';
                iconColor = 'text-info';
                html = generateServicesHtml(data);
                break;
            case 'diagrams':
                title = `Diagramas (${data.length})`;
                icon = 'ki-duotone ki-chart-pie-3';
                iconColor = 'text-danger';
                html = generateDiagramsHtml(data);
                break;
            case 'categories':
                title = `Categorias (${data.length})`;
                icon = 'ki-duotone ki-category';
                iconColor = 'text-dark';
                html = generateCategoriesHtml(data);
                break;
            default:
                throw new Error('Tipo não suportado');
        }

        // Show result modal
        Swal.fire({
            title: `<i class="${icon} fs-2x ${iconColor} me-2"></i>${title}`,
            html: html,
            icon: null,
            width: '80%',
            padding: '2em',
            showCloseButton: true,
            showConfirmButton: false,
            customClass: {
                container: 'swal-wide-container',
                popup: 'swal-wide-popup',
                htmlContainer: 'swal-html-container'
            }
        });

    } catch (error) {
        console.error('Erro ao buscar dados:', error);
        Swal.fire({
            title: 'Erro!',
            text: 'Não foi possível carregar as informações. Tente novamente.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
}

// Generate HTML for controllers
function generateControllersHtml(controllers) {
    if (controllers.length === 0) {
        return '<div class="text-center py-4"><p class="text-muted fs-5">Nenhum controller encontrado no sistema.</p></div>';
    }

    let html = '<div class="mb-3"><p class="text-muted fs-6">Lista completa de todos os controllers disponíveis no sistema:</p></div>';
    html += '<div class="table-responsive"><table class="table table-row-dashed table-row-gray-300 gy-7">';
    html += '<thead><tr class="fw-bold fs-6 text-gray-800">';
    html += '<th><i class="ki-duotone ki-code fs-4 text-primary me-2"></i>Nome da Classe</th>';
    html += '<th><i class="ki-duotone ki-folder fs-4 text-gray-600 me-2"></i>Localização do Arquivo</th>';
    html += '</tr></thead><tbody>';

    controllers.forEach((controller, index) => {
        html += '<tr>';
        html += `<td>
            <div class="d-flex align-items-center">
                <span class="badge badge-light-primary fs-8 me-2">${index + 1}</span>
                <code class="text-primary fw-bold">${controller.name}</code>
            </div>
        </td>`;
        html += `<td><span class="text-muted fs-7">${controller.file}</span></td>`;
        html += '</tr>';
    });

    html += '</tbody></table></div>';
    html += `<div class="mt-3"><small class="text-muted"><strong>Total:</strong> ${controllers.length} controllers encontrados</small></div>`;
    return html;
}

// Generate HTML for views
function generateViewsHtml(views) {
    if (views.length === 0) {
        return '<div class="text-center py-4"><p class="text-muted fs-5">Nenhuma view encontrada no sistema.</p></div>';
    }

    let html = '<div class="mb-3"><p class="text-muted fs-6">Lista completa de todas as views Blade disponíveis no sistema:</p></div>';
    html += '<div class="table-responsive"><table class="table table-row-dashed table-row-gray-300 gy-7">';
    html += '<thead><tr class="fw-bold fs-6 text-gray-800">';
    html += '<th><i class="ki-duotone ki-screen fs-4 text-success me-2"></i>Nome da View</th>';
    html += '<th><i class="ki-duotone ki-folder fs-4 text-gray-600 me-2"></i>Localização do Arquivo</th>';
    html += '</tr></thead><tbody>';

    views.forEach((view, index) => {
        html += '<tr>';
        html += `<td>
            <div class="d-flex align-items-center">
                <span class="badge badge-light-success fs-8 me-2">${index + 1}</span>
                <code class="text-success fw-bold">${view.name}</code>
            </div>
        </td>`;
        html += `<td><span class="text-muted fs-7">${view.file}</span></td>`;
        html += '</tr>';
    });

    html += '</tbody></table></div>';
    html += `<div class="mt-3"><small class="text-muted"><strong>Total:</strong> ${views.length} views encontradas</small></div>`;
    return html;
}

// Generate HTML for routes
function generateRoutesHtml(routes) {
    if (routes.length === 0) {
        return '<div class="text-center py-4"><p class="text-muted fs-5">Nenhuma rota encontrada no sistema.</p></div>';
    }

    let html = '<div class="mb-3"><p class="text-muted fs-6">Lista completa de todas as rotas registradas no sistema:</p></div>';
    html += '<div class="table-responsive"><table class="table table-row-dashed table-row-gray-300 gy-7">';
    html += '<thead><tr class="fw-bold fs-6 text-gray-800">';
    html += '<th><i class="ki-duotone ki-route fs-4 text-warning me-2"></i>URI da Rota</th>';
    html += '<th><i class="ki-duotone ki-setting-2 fs-4 text-primary me-2"></i>Métodos HTTP</th>';
    html += '<th><i class="ki-duotone ki-tag fs-4 text-info me-2"></i>Nome da Rota</th>';
    html += '<th><i class="ki-duotone ki-abstract-26 fs-4 text-gray-600 me-2"></i>Controller/Ação</th>';
    html += '</tr></thead><tbody>';

    routes.forEach((route, index) => {
        // Determinar cor do método HTTP
        let methodColor = 'primary';
        if (route.methods.includes('GET')) methodColor = 'success';
        if (route.methods.includes('POST')) methodColor = 'warning';
        if (route.methods.includes('PUT') || route.methods.includes('PATCH')) methodColor = 'info';
        if (route.methods.includes('DELETE')) methodColor = 'danger';

        html += '<tr>';
        html += `<td>
            <div class="d-flex align-items-center">
                <span class="badge badge-light-secondary fs-8 me-2">${index + 1}</span>
                <code class="text-warning fw-bold">${route.uri}</code>
            </div>
        </td>`;
        html += `<td><span class="badge badge-light-${methodColor} fw-bold">${route.methods}</span></td>`;
        html += `<td><span class="text-info fw-semibold">${route.name || '<span class="text-muted">sem nome</span>'}</span></td>`;
        html += `<td><span class="text-muted fs-7">${route.action}</span></td>`;
        html += '</tr>';
    });

    html += '</tbody></table></div>';
    html += `<div class="mt-3"><small class="text-muted"><strong>Total:</strong> ${routes.length} rotas registradas</small></div>`;
    return html;
}

// Generate HTML for services
function generateServicesHtml(services) {
    if (services.length === 0) {
        return '<div class="text-center py-4"><p class="text-muted fs-5">Nenhum service encontrado no sistema.</p></div>';
    }

    let html = '<div class="mb-3"><p class="text-muted fs-6">Lista completa de todos os services (classes de negócio) disponíveis no sistema:</p></div>';
    html += '<div class="table-responsive"><table class="table table-row-dashed table-row-gray-300 gy-7">';
    html += '<thead><tr class="fw-bold fs-6 text-gray-800">';
    html += '<th><i class="ki-duotone ki-setting-3 fs-4 text-info me-2"></i>Nome da Classe Service</th>';
    html += '<th><i class="ki-duotone ki-folder fs-4 text-gray-600 me-2"></i>Localização do Arquivo</th>';
    html += '</tr></thead><tbody>';

    services.forEach((service, index) => {
        html += '<tr>';
        html += `<td>
            <div class="d-flex align-items-center">
                <span class="badge badge-light-info fs-8 me-2">${index + 1}</span>
                <code class="text-info fw-bold">${service.name}</code>
            </div>
        </td>`;
        html += `<td><span class="text-muted fs-7">${service.file}</span></td>`;
        html += '</tr>';
    });

    html += '</tbody></table></div>';
    html += `<div class="mt-3"><small class="text-muted"><strong>Total:</strong> ${services.length} services encontrados</small></div>`;
    return html;
}

// Generate HTML for diagrams
function generateDiagramsHtml(diagrams) {
    if (diagrams.length === 0) {
        return '<div class="text-center py-4"><p class="text-muted fs-5">Nenhum diagrama encontrado no sistema.</p></div>';
    }

    let html = '<div class="mb-3"><p class="text-muted fs-6">Lista completa de todos os diagramas Mermaid disponíveis na documentação:</p></div>';
    html += '<div class="table-responsive"><table class="table table-row-dashed table-row-gray-300 gy-7">';
    html += '<thead><tr class="fw-bold fs-6 text-gray-800">';
    html += '<th><i class="ki-duotone ki-chart-pie-3 fs-4 text-danger me-2"></i>Título do Diagrama</th>';
    html += '<th><i class="ki-duotone ki-category fs-4 text-primary me-2"></i>Categoria</th>';
    html += '<th><i class="ki-duotone ki-document fs-4 text-gray-600 me-2"></i>Arquivo de Origem</th>';
    html += '</tr></thead><tbody>';

    diagrams.forEach((diagram, index) => {
        // Determinar cor da categoria
        let categoryColor = 'danger';
        if (diagram.category.includes('Proposições')) categoryColor = 'primary';
        if (diagram.category.includes('Documentos')) categoryColor = 'success';
        if (diagram.category.includes('Assinatura')) categoryColor = 'warning';
        if (diagram.category.includes('Arquitetura')) categoryColor = 'info';

        html += '<tr>';
        html += `<td>
            <div class="d-flex align-items-center">
                <span class="badge badge-light-secondary fs-8 me-2">${index + 1}</span>
                <strong class="text-danger">${diagram.title}</strong>
            </div>
        </td>`;
        html += `<td><span class="badge badge-light-${categoryColor} fw-bold">${diagram.category}</span></td>`;
        html += `<td><span class="text-muted fs-7">${diagram.source}</span></td>`;
        html += '</tr>';
    });

    html += '</tbody></table></div>';
    html += `<div class="mt-3"><small class="text-muted"><strong>Total:</strong> ${diagrams.length} diagramas documentados</small></div>`;
    return html;
}

// Generate HTML for categories
function generateCategoriesHtml(categories) {
    if (categories.length === 0) {
        return '<div class="text-center py-4"><p class="text-muted fs-5">Nenhuma categoria encontrada no sistema.</p></div>';
    }

    let html = '<div class="mb-3"><p class="text-muted fs-6">Organização das categorias de diagramas disponíveis na documentação do sistema:</p></div>';
    html += '<div class="row g-4">';

    categories.forEach((category, index) => {
        // Determinar cor e ícone da categoria
        let categoryColor = 'dark';
        let categoryIcon = 'ki-category';

        if (category.name.includes('Proposições')) {
            categoryColor = 'primary';
            categoryIcon = 'ki-document';
        } else if (category.name.includes('Documentos')) {
            categoryColor = 'success';
            categoryIcon = 'ki-folder-down';
        } else if (category.name.includes('Assinatura')) {
            categoryColor = 'warning';
            categoryIcon = 'ki-security-check';
        } else if (category.name.includes('Arquitetura')) {
            categoryColor = 'info';
            categoryIcon = 'ki-abstract-26';
        }

        html += '<div class="col-md-6">';
        html += '<div class="card card-flush h-100 border border-gray-300">';
        html += '<div class="card-body">';
        html += `<div class="d-flex align-items-center mb-3">
            <span class="badge badge-light-${categoryColor} fs-8 me-3">${index + 1}</span>
            <i class="ki-duotone ${categoryIcon} fs-2x text-${categoryColor} me-3">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
            <h5 class="card-title text-${categoryColor} mb-0 fw-bold">${category.name}</h5>
        </div>`;
        html += `<p class="card-text text-muted fs-6 mb-3">${category.description}</p>`;
        html += `<div class="d-flex align-items-center">
            <i class="ki-duotone ki-document fs-5 text-gray-600 me-2">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            <small class="text-muted"><strong>Arquivo:</strong> ${category.source}</small>
        </div>`;
        html += '</div>';
        html += '</div>';
        html += '</div>';
    });

    html += '</div>';
    html += `<div class="mt-4 text-center"><small class="text-muted"><strong>Total:</strong> ${categories.length} categorias organizadas</small></div>`;
    return html;
}

// Toggle fullscreen for diagram
function toggleFullscreen(diagramId) {
    const element = document.getElementById(diagramId)?.parentElement;

    if (!element) {
        console.error('Diagram element not found:', diagramId);
        return;
    }

    if (!document.fullscreenElement) {
        element.requestFullscreen().catch(err => {
            console.error(`Error attempting to enable fullscreen: ${err.message}`);
        });
    } else {
        document.exitFullscreen();
    }
}

// Download diagram as PNG or PDF
async function downloadDiagram(diagramId, format) {
    try {
        const diagramElement = document.getElementById(diagramId);
        if (!diagramElement) {
            throw new Error('Diagrama não encontrado');
        }

        const svgElement = diagramElement.querySelector('svg');
        if (!svgElement) {
            throw new Error('SVG não encontrado no diagrama');
        }

        // Show loading indicator
        showDownloadLoading(diagramId, format);

        // Get diagram title for filename
        const cardHeader = diagramElement.closest('.card').querySelector('.card-title');
        const diagramTitle = cardHeader ? cardHeader.textContent.trim() : diagramId;
        const fileName = sanitizeFileName(diagramTitle);

        if (format === 'png') {
            await downloadAsPNG(svgElement, fileName);
        } else if (format === 'pdf') {
            await downloadAsPDF(svgElement, fileName, diagramTitle);
        }

        hideDownloadLoading(diagramId, format);
        showDownloadSuccess(format);

    } catch (error) {
        console.error('Erro ao baixar diagrama:', error);
        hideDownloadLoading(diagramId, format);
        showDownloadError(error.message);
    }
}

// Download as PNG using html2canvas
async function downloadAsPNG(svgElement, fileName) {
    // Reduced wait time for better performance
    await new Promise(resolve => setTimeout(resolve, 200));

    // Create a temporary container with proper styling
    const tempContainer = document.createElement('div');
    tempContainer.style.position = 'absolute';
    tempContainer.style.left = '-9999px';
    tempContainer.style.top = '0';
    tempContainer.style.background = 'white';
    tempContainer.style.padding = '40px';
    tempContainer.style.border = '1px solid #e4e6ea';
    tempContainer.style.borderRadius = '8px';
    tempContainer.style.width = 'auto';
    tempContainer.style.height = 'auto';

    // Clone the SVG with proper dimensions
    const svgClone = svgElement.cloneNode(true);

    // Force SVG to be visible and properly sized
    svgClone.style.display = 'block';
    svgClone.style.width = 'auto';
    svgClone.style.height = 'auto';
    svgClone.style.maxWidth = 'none';
    svgClone.style.maxHeight = 'none';
    svgClone.style.visibility = 'visible';

    // Ensure SVG has proper dimensions
    const bbox = svgElement.getBBox();
    if (bbox.width > 0 && bbox.height > 0) {
        svgClone.setAttribute('width', bbox.width);
        svgClone.setAttribute('height', bbox.height);
        svgClone.setAttribute('viewBox', `${bbox.x} ${bbox.y} ${bbox.width} ${bbox.height}`);
    }

    tempContainer.appendChild(svgClone);
    document.body.appendChild(tempContainer);

    // Wait for the DOM to be updated
    await new Promise(resolve => requestAnimationFrame(resolve));

    try {
        const canvas = await html2canvas(tempContainer, {
            backgroundColor: '#ffffff', // Força fundo branco
            scale: 3, // Aumentar qualidade para PNG também
            useCORS: false, // CRITICAL: Disable CORS to avoid tainted canvas
            allowTaint: false, // CRITICAL: Prevent tainted canvas
            logging: false,
            width: tempContainer.offsetWidth,
            height: tempContainer.offsetHeight,
            foreignObjectRendering: true, // REABILITAR para melhor qualidade SVG
            removeContainer: true,
            imageTimeout: 0,
            onclone: function(clonedDoc) {
                // Ensure all SVG elements are visible in the cloned document
                const clonedSvgs = clonedDoc.querySelectorAll('svg');
                clonedSvgs.forEach(svg => {
                    svg.style.display = 'block !important';
                    svg.style.visibility = 'visible !important';
                    svg.style.opacity = '1 !important';

                    // CRITICAL: Remove external references that cause CORS
                    const externalRefs = svg.querySelectorAll('image, use');
                    externalRefs.forEach(ref => ref.remove());

                    // Aplicar estilos para melhor contraste (IGUAL AO PDF)
                    const texts = svg.querySelectorAll('text, tspan');
                    texts.forEach(text => {
                        text.style.fill = '#2d3436';
                        text.style.fontWeight = 'normal';
                    });

                    const rects = svg.querySelectorAll('rect');
                    rects.forEach(rect => {
                        if (!rect.style.fill || rect.style.fill === 'none') {
                            rect.style.fill = '#ffffff';
                        }
                        rect.style.stroke = '#7E8299';
                    });

                    const paths = svg.querySelectorAll('path');
                    paths.forEach(path => {
                        if (!path.style.stroke) {
                            path.style.stroke = '#5CB85C';
                        }
                        path.style.strokeWidth = '2px';
                    });
                });
            }
        });

        // Convert canvas to blob and download
        canvas.toBlob(function(blob) {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${fileName}.png`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }, 'image/png', 1.0);

    } finally {
        document.body.removeChild(tempContainer);
    }
}

// Download as PDF using jsPDF - MÉTODO SVG DIRETO
async function downloadAsPDF(svgElement, fileName, title) {
    try {
        // Método 1: Tentar SVG direto para Canvas
        const canvas = await convertSVGToCanvas(svgElement);

        if (canvas) {
            await createPDFFromCanvas(canvas, fileName, title);
            return;
        }

        // Método 2: Fallback usando serialização SVG
        console.log('🔄 Fallback: Usando serialização SVG...');
        await downloadPDFUsingSerialization(svgElement, fileName, title);

    } catch (error) {
        console.error('❌ Erro no PDF:', error);
        throw error;
    }
}

// Converte SVG diretamente para Canvas - SEM CORS ISSUES
async function convertSVGToCanvas(svgElement) {
    try {
        // Clone e prepare o SVG
        const svgClone = svgElement.cloneNode(true);
        const bbox = svgElement.getBBox();

        // Definir dimensões explícitas
        svgClone.setAttribute('width', bbox.width);
        svgClone.setAttribute('height', bbox.height);
        svgClone.setAttribute('viewBox', `${bbox.x} ${bbox.y} ${bbox.width} ${bbox.height}`);

        // CRITICAL: Remove qualquer referência externa que cause CORS
        const externalElements = svgClone.querySelectorAll('image, use');
        externalElements.forEach(el => el.remove());

        // Aplicar estilos inline completos para preservar aparência visual
        const allElements = svgClone.querySelectorAll('*');
        allElements.forEach(el => {
            const computedStyle = window.getComputedStyle(el);

            // Aplicar todos os estilos relevantes de forma inline
            if (el.tagName) {
                const tagName = el.tagName.toLowerCase();

                // Estilos de preenchimento e contorno
                const fill = computedStyle.fill;
                const stroke = computedStyle.stroke;
                const strokeWidth = computedStyle.strokeWidth;

                if (fill && fill !== 'none' && fill !== 'rgb(0, 0, 0)') {
                    el.setAttribute('fill', fill);
                }
                if (stroke && stroke !== 'none') {
                    el.setAttribute('stroke', stroke);
                }
                if (strokeWidth && strokeWidth !== '0px') {
                    el.setAttribute('stroke-width', strokeWidth);
                }

                // Estilos de texto
                if (tagName === 'text' || tagName === 'tspan') {
                    el.setAttribute('font-family', computedStyle.fontFamily || 'Inter, sans-serif');
                    el.setAttribute('font-size', computedStyle.fontSize || '14px');
                    el.setAttribute('font-weight', computedStyle.fontWeight || 'normal');

                    // Garantir que texto seja visível
                    if (!el.getAttribute('fill') || el.getAttribute('fill') === 'currentColor') {
                        el.setAttribute('fill', '#2d3436'); // Cor escura para texto
                    }
                }

                // Estilos específicos para elementos Mermaid
                if (el.classList) {
                    if (el.classList.contains('node') || tagName === 'rect') {
                        // Nós do diagrama - garantir fundo claro
                        if (!el.getAttribute('fill') || el.getAttribute('fill') === 'none') {
                            el.setAttribute('fill', '#ffffff');
                        }
                        if (!el.getAttribute('stroke')) {
                            el.setAttribute('stroke', '#7E8299');
                        }
                    }

                    if (el.classList.contains('edge') || tagName === 'path') {
                        // Setas e conexões - garantir visibilidade
                        if (!el.getAttribute('stroke')) {
                            el.setAttribute('stroke', '#5CB85C');
                        }
                        if (!el.getAttribute('stroke-width')) {
                            el.setAttribute('stroke-width', '2');
                        }
                        el.setAttribute('fill', 'none');
                    }
                }
            }
        });

        // Converter SVG para data URL inline (SEM BLOB para evitar CORS)
        const svgData = new XMLSerializer().serializeToString(svgClone);
        const svgDataUrl = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));

        // Criar imagem e canvas
        const img = new Image();

        return new Promise((resolve, reject) => {
            img.onload = function() {
                try {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    canvas.width = bbox.width * 2; // Scale para qualidade
                    canvas.height = bbox.height * 2;

                    ctx.scale(2, 2);
                    // CRITICAL: Garantir fundo branco sólido
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, bbox.width, bbox.height);

                    // Melhorar qualidade da renderização
                    ctx.imageSmoothingEnabled = true;
                    ctx.imageSmoothingQuality = 'high';

                    ctx.drawImage(img, 0, 0, bbox.width, bbox.height);

                    console.log('✅ SVG converted to canvas successfully');
                    resolve(canvas);
                } catch (error) {
                    console.error('❌ Error drawing image to canvas:', error);
                    resolve(null);
                }
            };

            img.onerror = function(error) {
                console.error('❌ Image load error:', error);
                resolve(null); // Retorna null para tentar fallback
            };

            // CRITICAL: Set crossOrigin BEFORE setting src
            img.crossOrigin = 'anonymous';
            img.src = svgDataUrl;
        });

    } catch (error) {
        console.error('❌ Erro na conversão SVG:', error);
        return null;
    }
}

// Fallback usando html2canvas com serialização melhorada
async function downloadPDFUsingSerialization(svgElement, fileName, title) {
    // Criar container temporário
    const tempContainer = document.createElement('div');
    tempContainer.style.position = 'absolute';
    tempContainer.style.left = '-9999px';
    tempContainer.style.background = 'white';
    tempContainer.style.padding = '40px';
    tempContainer.style.width = 'auto';
    tempContainer.style.height = 'auto';

    // Clone do SVG com estilos preservados
    const svgClone = svgElement.cloneNode(true);
    const bbox = svgElement.getBBox();

    // Aplicar estilos inline ao SVG clonado com melhoria de contraste
    const originalStyles = window.getComputedStyle(svgElement);
    for (let i = 0; i < originalStyles.length; i++) {
        const property = originalStyles[i];
        svgClone.style[property] = originalStyles.getPropertyValue(property);
    }

    // Melhorar contraste e visibilidade dos elementos
    const allElements = svgClone.querySelectorAll('*');
    allElements.forEach(el => {
        const computedStyle = window.getComputedStyle(el);
        const tagName = el.tagName ? el.tagName.toLowerCase() : '';

        // Forçar estilos para melhor legibilidade
        if (tagName === 'text' || tagName === 'tspan') {
            el.style.fill = '#2d3436'; // Texto escuro
            el.style.fontWeight = 'normal';
            el.style.fontSize = computedStyle.fontSize || '14px';
        }

        if (tagName === 'rect' || el.classList.contains('node')) {
            el.style.fill = '#ffffff'; // Fundo branco para nós
            el.style.stroke = '#7E8299'; // Borda cinza
            el.style.strokeWidth = '1px';
        }

        if (tagName === 'path' || el.classList.contains('edge')) {
            el.style.stroke = '#5CB85C'; // Verde para conexões
            el.style.strokeWidth = '2px';
            el.style.fill = 'none';
        }

        // Remover estilos que podem causar problemas
        el.style.filter = 'none';
        el.style.opacity = '1';
    });

    // Definir dimensões e visibilidade
    svgClone.setAttribute('width', bbox.width);
    svgClone.setAttribute('height', bbox.height);
    svgClone.setAttribute('viewBox', `${bbox.x} ${bbox.y} ${bbox.width} ${bbox.height}`);
    svgClone.style.display = 'block';
    svgClone.style.visibility = 'visible';

    tempContainer.appendChild(svgClone);
    document.body.appendChild(tempContainer);

    try {
        const canvas = await html2canvas(tempContainer, {
            backgroundColor: '#ffffff', // Força fundo branco
            scale: 3, // Aumentar qualidade
            useCORS: false, // CRITICAL: Disable CORS to avoid tainted canvas
            allowTaint: false, // CRITICAL: Prevent tainted canvas
            logging: false,
            width: tempContainer.offsetWidth,
            height: tempContainer.offsetHeight,
            foreignObjectRendering: true, // REABILITAR para melhor qualidade SVG
            removeContainer: true,
            imageTimeout: 0,
            onclone: function(clonedDoc) {
                const clonedSvgs = clonedDoc.querySelectorAll('svg');
                clonedSvgs.forEach(svg => {
                    svg.style.display = 'block !important';
                    svg.style.visibility = 'visible !important';
                    svg.style.opacity = '1 !important';

                    // CRITICAL: Remove external references that cause CORS
                    const externalRefs = svg.querySelectorAll('image, use');
                    externalRefs.forEach(ref => ref.remove());

                    // Aplicar estilos para melhor contraste
                    const texts = svg.querySelectorAll('text, tspan');
                    texts.forEach(text => {
                        text.style.fill = '#2d3436';
                        text.style.fontWeight = 'normal';
                    });

                    const rects = svg.querySelectorAll('rect');
                    rects.forEach(rect => {
                        if (!rect.style.fill || rect.style.fill === 'none') {
                            rect.style.fill = '#ffffff';
                        }
                        rect.style.stroke = '#7E8299';
                    });

                    const paths = svg.querySelectorAll('path');
                    paths.forEach(path => {
                        if (!path.style.stroke) {
                            path.style.stroke = '#5CB85C';
                        }
                        path.style.strokeWidth = '2px';
                    });
                });
            }
        });

        await createPDFFromCanvas(canvas, fileName, title);

    } finally {
        document.body.removeChild(tempContainer);
    }
}

// Função auxiliar para criar PDF a partir do Canvas
async function createPDFFromCanvas(canvas, fileName, title) {
    // Create PDF
    const { jsPDF } = window.jspdf;

    // Calculate dimensions
    const imgWidth = canvas.width;
    const imgHeight = canvas.height;
    const ratio = imgWidth / imgHeight;

    // A4 dimensions in mm
    const pageWidth = 210;
    const pageHeight = 297;
    const margin = 20;
    const maxWidth = pageWidth - (margin * 2);
    const maxHeight = pageHeight - (margin * 3) - 10; // Extra space for title

    let finalWidth, finalHeight;

    if (ratio > maxWidth / maxHeight) {
        finalWidth = maxWidth;
        finalHeight = maxWidth / ratio;
    } else {
        finalHeight = maxHeight;
        finalWidth = maxHeight * ratio;
    }

    // Create PDF with portrait orientation
    const pdf = new jsPDF('p', 'mm', 'a4');

    // Add title
    pdf.setFontSize(16);
    pdf.setFont(undefined, 'bold');
    pdf.text(title, margin, margin);

    // Add current date
    pdf.setFontSize(10);
    pdf.setFont(undefined, 'normal');
    const currentDate = new Date().toLocaleDateString('pt-BR');
    pdf.text(`Gerado em: ${currentDate}`, margin, margin + 8);

    // Add image - CRITICAL: Use high quality PNG
    const imgData = canvas.toDataURL('image/png', 1.0);
    const yPosition = margin + 20;

    console.log(`📊 PDF: Adding image ${finalWidth}x${finalHeight}mm at position ${margin},${yPosition}`);
    pdf.addImage(imgData, 'PNG', margin, yPosition, finalWidth, finalHeight);

    // Add footer
    pdf.setFontSize(8);
    pdf.text('Sistema LegisInc - Arquitetura e Fluxos', margin, pageHeight - 10);

    // Save PDF
    console.log(`✅ PDF: Saving as ${fileName}.pdf`);
    pdf.save(`${fileName}.pdf`);
}

// Helper functions
function sanitizeFileName(name) {
    return name
        .replace(/[^\w\s-]/g, '') // Remove special characters
        .replace(/\s+/g, '_') // Replace spaces with underscores
        .toLowerCase()
        .substring(0, 50); // Limit length
}

function showDownloadLoading(diagramId, format) {
    const buttons = document.querySelectorAll(`[onclick*="${diagramId}"]`);
    buttons.forEach(button => {
        if (button.onclick.toString().includes(format)) {
            button.disabled = true;
            const originalContent = button.innerHTML;
            button.setAttribute('data-original-content', originalContent);

            // Add loading animation with progress
            button.classList.add('btn-loading');
            button.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                <span class="loading-text">Processando ${format.toUpperCase()}...</span>
                <div class="loading-progress-bar"></div>
            `;

            // Animate progress bar using requestAnimationFrame for better performance
            requestAnimationFrame(() => {
                const progressBar = button.querySelector('.loading-progress-bar');
                if (progressBar) {
                    progressBar.style.width = '100%';
                }
            });
        }
    });
}

function hideDownloadLoading(diagramId, format) {
    const buttons = document.querySelectorAll(`[onclick*="${diagramId}"]`);
    buttons.forEach(button => {
        if (button.onclick.toString().includes(format)) {
            // Add success animation before restoring
            button.classList.add('btn-success-animation');
            button.innerHTML = `
                <i class="ki-duotone ki-check-circle fs-2 me-2 text-success">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <span>Download Concluído!</span>
            `;

            // Restore original state after animation
            setTimeout(() => {
                button.disabled = false;
                button.classList.remove('btn-loading', 'btn-success-animation');
                const originalContent = button.getAttribute('data-original-content');
                if (originalContent) {
                    button.innerHTML = originalContent;
                    button.removeAttribute('data-original-content');
                }
            }, 2000);
        }
    });
}

function showDownloadSuccess(format) {
    // Create toast notification
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.setAttribute('role', 'alert');

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="ki-duotone ki-check-circle fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Diagrama baixado como ${format.toUpperCase()} com sucesso!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;

    document.body.appendChild(toast);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

function showDownloadError(message) {
    // Create error toast notification
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-white bg-danger border-0 position-fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.setAttribute('role', 'alert');

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="ki-duotone ki-cross-circle fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Erro ao baixar: ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;

    document.body.appendChild(toast);

    // Auto remove after 8 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 8000);
}

// Download all diagrams in the current active category
async function downloadAllDiagrams(format) {
    try {
        // Get all visible diagrams in the current active category
        const activeCategory = document.querySelector('.tab-pane.active');
        if (!activeCategory) {
            throw new Error('Nenhuma categoria ativa encontrada');
        }

        const diagramElements = activeCategory.querySelectorAll('.mermaid[data-processed="true"]');
        if (diagramElements.length === 0) {
            throw new Error('Nenhum diagrama renderizado encontrado na categoria ativa');
        }

        // Show progress notification
        const progressToast = showBatchDownloadProgress(diagramElements.length, format);

        // Create ZIP file for multiple downloads
        const { default: JSZip } = await import('https://cdn.skypack.dev/jszip');
        const zip = new JSZip();

        let successCount = 0;
        let errorCount = 0;

        for (let i = 0; i < diagramElements.length; i++) {
            try {
                const diagramElement = diagramElements[i];
                const svgElement = diagramElement.querySelector('svg');

                if (!svgElement) {
                    errorCount++;
                    continue;
                }

                // Get diagram title for filename
                const cardHeader = diagramElement.closest('.card').querySelector('.card-title');
                const diagramTitle = cardHeader ? cardHeader.textContent.trim() : `diagram_${i+1}`;
                const fileName = sanitizeFileName(diagramTitle);

                // Update progress
                updateBatchDownloadProgress(progressToast, i + 1, diagramElements.length, diagramTitle);

                if (format === 'png') {
                    const blob = await generatePNGBlob(svgElement);
                    zip.file(`${fileName}.png`, blob);
                } else if (format === 'pdf') {
                    const pdfBlob = await generatePDFBlob(svgElement, diagramTitle);
                    zip.file(`${fileName}.pdf`, pdfBlob);
                }

                successCount++;

                // Small delay to prevent browser freezing
                await new Promise(resolve => setTimeout(resolve, 200));

            } catch (error) {
                console.error('Erro ao processar diagrama:', error);
                errorCount++;
            }
        }

        if (successCount > 0) {
            // Generate and download ZIP file
            const zipContent = await zip.generateAsync({type: 'blob'});

            // Get category name for ZIP filename
            const activeTab = document.querySelector('.nav-link.active');
            const categoryName = activeTab ? activeTab.textContent.trim().replace(/\d+$/, '').trim() : 'diagramas';
            const zipFileName = `${sanitizeFileName(categoryName)}_${format}_${new Date().toISOString().split('T')[0]}.zip`;

            const url = URL.createObjectURL(zipContent);
            const a = document.createElement('a');
            a.href = url;
            a.download = zipFileName;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        // Hide progress and show result
        hideBatchDownloadProgress(progressToast);

        if (successCount > 0) {
            showBatchDownloadResult(successCount, errorCount, format);
        } else {
            throw new Error('Nenhum diagrama foi processado com sucesso');
        }

    } catch (error) {
        console.error('Erro no download em lote:', error);
        showDownloadError(error.message);
    }
}

// Generate PNG blob from SVG
async function generatePNGBlob(svgElement) {
    const tempContainer = document.createElement('div');
    tempContainer.style.position = 'absolute';
    tempContainer.style.left = '-9999px';
    tempContainer.style.background = 'white';
    tempContainer.style.padding = '40px';

    const svgClone = svgElement.cloneNode(true);
    svgClone.style.width = 'auto';
    svgClone.style.height = 'auto';
    svgClone.style.maxWidth = 'none';

    tempContainer.appendChild(svgClone);
    document.body.appendChild(tempContainer);

    try {
        const canvas = await html2canvas(tempContainer, {
            backgroundColor: 'white',
            scale: 2,
            useCORS: true,
            allowTaint: true,
            logging: false
        });

        return new Promise(resolve => {
            canvas.toBlob(resolve, 'image/png', 1.0);
        });
    } finally {
        document.body.removeChild(tempContainer);
    }
}

// Generate PDF blob from SVG
async function generatePDFBlob(svgElement, title) {
    const tempContainer = document.createElement('div');
    tempContainer.style.position = 'absolute';
    tempContainer.style.left = '-9999px';
    tempContainer.style.background = 'white';
    tempContainer.style.padding = '40px';

    const svgClone = svgElement.cloneNode(true);
    svgClone.style.width = 'auto';
    svgClone.style.height = 'auto';
    svgClone.style.maxWidth = 'none';

    tempContainer.appendChild(svgClone);
    document.body.appendChild(tempContainer);

    try {
        const canvas = await html2canvas(tempContainer, {
            backgroundColor: 'white',
            scale: 2,
            useCORS: true,
            allowTaint: true,
            logging: false
        });

        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');

        const imgWidth = canvas.width;
        const imgHeight = canvas.height;
        const ratio = imgWidth / imgHeight;

        const pageWidth = 210;
        const pageHeight = 297;
        const margin = 20;
        const maxWidth = pageWidth - (margin * 2);
        const maxHeight = pageHeight - (margin * 3) - 10;

        let finalWidth, finalHeight;

        if (ratio > maxWidth / maxHeight) {
            finalWidth = maxWidth;
            finalHeight = maxWidth / ratio;
        } else {
            finalHeight = maxHeight;
            finalWidth = maxHeight * ratio;
        }

        pdf.setFontSize(16);
        pdf.setFont(undefined, 'bold');
        pdf.text(title, margin, margin);

        pdf.setFontSize(10);
        pdf.setFont(undefined, 'normal');
        const currentDate = new Date().toLocaleDateString('pt-BR');
        pdf.text(`Gerado em: ${currentDate}`, margin, margin + 8);

        const imgData = canvas.toDataURL('image/png');
        pdf.addImage(imgData, 'PNG', margin, margin + 20, finalWidth, finalHeight);

        pdf.setFontSize(8);
        pdf.text('Sistema LegisInc - Arquitetura e Fluxos', margin, pageHeight - 10);

        return pdf.output('blob');

    } finally {
        document.body.removeChild(tempContainer);
    }
}

// Progress notification functions
function showBatchDownloadProgress(total, format) {
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-white bg-info border-0 position-fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.style.minWidth = '350px';
    toast.setAttribute('role', 'alert');

    toast.innerHTML = `
        <div class="d-flex w-100">
            <div class="toast-body">
                <div class="d-flex align-items-center">
                    <span class="spinner-border spinner-border-sm me-3" role="status"></span>
                    <div class="flex-grow-1">
                        <div class="fw-bold">Processando diagramas...</div>
                        <small id="progress-text">Preparando download de ${total} diagramas em ${format.toUpperCase()}</small>
                        <div class="progress mt-2" style="height: 4px;">
                            <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(toast);
    return toast;
}

function updateBatchDownloadProgress(toast, current, total, currentDiagram) {
    const percentage = (current / total) * 100;
    const progressBar = toast.querySelector('#progress-bar');
    const progressText = toast.querySelector('#progress-text');

    if (progressBar) {
        progressBar.style.width = `${percentage}%`;
    }

    if (progressText) {
        progressText.textContent = `Processando ${current}/${total}: ${currentDiagram}`;
    }
}

function hideBatchDownloadProgress(toast) {
    if (toast && toast.parentElement) {
        toast.remove();
    }
}

function showBatchDownloadResult(successCount, errorCount, format) {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white ${errorCount > 0 ? 'bg-warning' : 'bg-success'} border-0 position-fixed`;
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.setAttribute('role', 'alert');

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="ki-duotone ki-check-circle fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <div>
                    <div class="fw-bold">Download concluído!</div>
                    <small>${successCount} diagramas baixados como ${format.toUpperCase()}${errorCount > 0 ? ` (${errorCount} erros)` : ''}</small>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 8000);
}
</script>

<style>
.mermaid-container {
    max-height: 600px;
    transition: max-height 0.3s ease;
    overflow: auto;
    border: 1px solid #e4e6ea;
    border-radius: 0.475rem;
    background: #fff;
}

.mermaid {
    text-align: center;
    width: 100%;
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px;
}

/* Ensure SVG scales properly */
.mermaid svg {
    max-width: 100% !important;
    height: auto !important;
    width: 100% !important;
}

/* Fullscreen styles */
.mermaid-container:fullscreen {
    background: white !important;
    padding: 40px !important;
    max-height: none !important;
    border: none !important;
}

/* Tab navigation styles */
.nav-line-tabs .nav-link {
    font-weight: 500;
    font-size: 0.95rem;
}

.nav-line-tabs .nav-link.active {
    color: #f1416c;
    border-bottom-color: #f1416c;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    .mermaid-container {
        max-height: 500px;
    }

    .mermaid {
        padding: 8px;
    }
}

@media (max-width: 992px) {
    .mermaid-container {
        max-height: 450px;
    }

    .mermaid {
        padding: 6px;
    }
}

@media (max-width: 768px) {
    .nav-line-tabs {
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .nav-line-tabs .nav-item {
        flex: 0 0 auto;
    }

    .mermaid-container {
        max-height: 400px;
    }

    .mermaid {
        padding: 5px;
        min-height: 150px;
    }
}

@media (max-width: 576px) {
    .mermaid-container {
        max-height: 300px;
    }

    .mermaid {
        padding: 5px;
        min-height: 120px;
    }
}

/* Scrollbar styling for diagram containers */
.mermaid-container::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.mermaid-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.mermaid-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.mermaid-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Download buttons styles */
.btn-group .btn {
    font-size: 0.75rem;
    font-weight: 500;
    min-width: 60px;
}

.btn-group .btn:first-child {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.btn-group .btn:last-child {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

.btn-group .btn + .btn {
    border-left: 1px solid rgba(255, 255, 255, 0.2);
}

/* Toast notification positioning */
.toast {
    min-width: 300px;
}

/* Loading state for download buttons */
.btn[disabled] {
    opacity: 0.7;
    cursor: not-allowed;
}

/* Download button loading animations */
.btn-loading {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.btn-loading .loading-progress-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    width: 0%;
    background: linear-gradient(90deg, #28a745, #20c997);
    transition: width 3s ease-in-out;
    border-radius: 0 0 4px 4px;
}

.btn-loading .loading-text {
    animation: loading-pulse 1.5s infinite;
}

@keyframes loading-pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

/* Success animation */
.btn-success-animation {
    animation: success-bounce 0.6s ease-out;
    background: linear-gradient(135deg, #28a745, #20c997) !important;
    border-color: #28a745 !important;
    color: white !important;
}

@keyframes success-bounce {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Enhanced spinner animation */
.btn-loading .spinner-border {
    animation: enhanced-spin 1s linear infinite;
}

@keyframes enhanced-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Download button hover effects */
.btn-light-success:hover {
    background-color: #0f5132 !important;
    border-color: #0f5132 !important;
    color: #fff !important;
}

.btn-light-warning:hover {
    background-color: #b45309 !important;
    border-color: #b45309 !important;
    color: #fff !important;
}

/* Clickable cards styles */
.clickable-card {
    transition: all 0.3s ease !important;
    border: 1px solid #e4e6ea !important;
}

.clickable-card:hover {
    transform: translateY(-5px) !important;
    box-shadow: 0 8px 25px 0 rgba(20, 20, 43, 0.1) !important;
    border-color: #f1416c !important;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;
}

.clickable-card:active {
    transform: translateY(-2px) !important;
    transition: all 0.1s ease !important;
}

/* SweetAlert2 custom styles */
.swal-wide-container .swal2-popup {
    max-width: 90% !important;
    max-height: 80vh !important;
    overflow-y: auto !important;
}

.swal-html-container {
    max-height: 60vh !important;
    overflow-y: auto !important;
    padding: 0 !important;
}

/* Table styles in SweetAlert */
.swal2-popup .table {
    margin-bottom: 0 !important;
    font-size: 0.875rem !important;
    text-align: left !important;
}

.swal2-popup .table th {
    background-color: #f5f8fa !important;
    border-top: none !important;
    font-weight: 600 !important;
    padding: 12px 16px !important;
    text-align: left !important;
    color: #2d3436 !important;
    font-size: 0.9rem !important;
}

.swal2-popup .table td {
    padding: 12px 16px !important;
    vertical-align: middle !important;
    text-align: left !important;
    border-bottom: 1px solid #e9ecef !important;
}

.swal2-popup .table code {
    font-size: 0.8rem !important;
    padding: 4px 8px !important;
    border-radius: 4px !important;
    background-color: rgba(0, 0, 0, 0.08) !important;
    font-family: 'Consolas', 'Monaco', 'Courier New', monospace !important;
    font-weight: 500 !important;
}

.swal2-popup .table tr:hover {
    background-color: #f8f9fa !important;
}

/* Improve text readability in SweetAlert */
.swal2-popup .text-muted {
    color: #6c757d !important;
    font-size: 0.85rem !important;
}

.swal2-popup .badge {
    font-size: 0.75rem !important;
    padding: 4px 8px !important;
}

/* Card content in categories */
.swal2-popup .card {
    margin-bottom: 1rem !important;
}

.swal2-popup .card-body {
    padding: 1rem !important;
    text-align: left !important;
}

.swal2-popup .card-title {
    font-size: 1.1rem !important;
    font-weight: 600 !important;
    margin-bottom: 0.5rem !important;
}

.swal2-popup .card-text {
    font-size: 0.9rem !important;
    line-height: 1.4 !important;
    margin-bottom: 0.75rem !important;
}

/* Card hover animations for statistics */
.card.clickable-card .svg-icon {
    transition: all 0.3s ease !important;
}

.card.clickable-card:hover .svg-icon {
    transform: scale(1.1) !important;
}

.card.clickable-card:hover .fs-2 {
    color: #f1416c !important;
    transition: color 0.3s ease !important;
}

/* Add pulse animation on click */
@keyframes cardPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

.card.clickable-card.clicked {
    animation: cardPulse 0.3s ease-out !important;
}
</style>
@endpush