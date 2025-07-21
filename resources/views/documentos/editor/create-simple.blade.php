@extends('components.layouts.app')

@section('title', 'Editor de Documentos')

@push('styles')
<!-- Font Awesome for reliable icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
.editor-container {
    min-height: 500px;
    border: 2px solid #e1e5e9;
    border-radius: 0.625rem;
    box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075);
    background: #ffffff;
}

.editor-menu {
    border-bottom: 2px solid #e1e5e9;
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 0.625rem 0.625rem 0 0;
}

.editor-content {
    padding: 0;
    min-height: 400px;
    max-height: 600px;
    overflow-y: auto;
    border-radius: 0 0 0.625rem 0.625rem;
}

#editor {
    outline: none;
    font-family: 'Times New Roman', serif;
    font-size: 12pt;
    line-height: 1.8;
    min-height: 400px;
    padding: 2rem;
    border: none;
    background: #ffffff;
    border-radius: 0 0 0.625rem 0.625rem;
}

#editor:focus {
    outline: none;
    box-shadow: inset 0 0 0 2px rgba(74, 124, 246, 0.2);
}

/* Toolbar Styling */
.btn-toolbar .btn {
    border: 1px solid #e1e5e9;
    background: #ffffff;
    transition: all 0.2s ease;
    font-weight: 500;
}

.btn-toolbar .btn:hover {
    background: #f8f9fa;
    border-color: #009ef7;
    color: #009ef7;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0, 158, 247, 0.2);
}

.btn-toolbar .btn.active,
.btn-toolbar .btn:active {
    background: #009ef7;
    border-color: #009ef7;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(0, 158, 247, 0.3);
}

.btn-group {
    border-radius: 0.475rem;
    overflow: hidden;
}

.btn-group .btn:first-child {
    border-radius: 0.475rem 0 0 0.475rem;
}

.btn-group .btn:last-child {
    border-radius: 0 0.475rem 0.475rem 0;
}

.btn-group .btn:not(:first-child):not(:last-child) {
    border-radius: 0;
}

/* Variable Tags */
.variable-tag {
    background: linear-gradient(135deg, #e8f4fd 0%, #bbdefb 100%);
    color: #1976d2;
    padding: 3px 8px;
    border-radius: 6px;
    font-weight: 600;
    border: 1px solid #bbdefb;
    cursor: pointer;
    display: inline-block;
    margin: 0 2px;
    transition: all 0.2s ease;
    font-size: 11pt;
}

.variable-tag:hover {
    background: linear-gradient(135deg, #bbdefb 0%, #90caf9 100%);
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(25, 118, 210, 0.3);
}

/* Variable Sidebar */
.variable-sidebar {
    max-height: 600px;
    overflow-y: auto;
    padding-right: 0.5rem;
}

.variable-sidebar::-webkit-scrollbar {
    width: 6px;
}

.variable-sidebar::-webkit-scrollbar-track {
    background: #f1f3f4;
    border-radius: 3px;
}

.variable-sidebar::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.variable-sidebar::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.variable-item {
    cursor: pointer;
    transition: all 0.3s ease;
    border-radius: 0.5rem;
    margin-bottom: 0.75rem;
    position: relative;
    overflow: hidden;
}

.variable-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 4px;
    background: #009ef7;
    transform: scaleY(0);
    transition: transform 0.3s ease;
}

.variable-item:hover {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transform: translateX(8px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.variable-item:hover::before {
    transform: scaleY(1);
}

/* Card Enhancements */
.card {
    border: 1px solid #e4e6ea;
    box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-bottom: 1px solid #e4e6ea;
    padding: 1.25rem 1.5rem;
}

/* Custom Icons with better visibility */
.icon-bold {
    font-weight: 900;
    font-size: 14px;
    font-family: Arial, sans-serif;
}
.icon-bold::before { content: "B"; }

.icon-italic {
    font-style: italic;
    font-size: 14px;
    font-family: Arial, sans-serif;
}
.icon-italic::before { content: "I"; }

.icon-underline {
    text-decoration: underline;
    font-size: 14px;
    font-family: Arial, sans-serif;
}
.icon-underline::before { content: "U"; }

.icon-align-left {
    font-size: 16px;
    font-family: monospace;
}
.icon-align-left::before { content: "⟵"; }

.icon-align-center {
    font-size: 16px;
    font-family: monospace;
}
.icon-align-center::before { content: "⟷"; }

.icon-align-right {
    font-size: 16px;
    font-family: monospace;
}
.icon-align-right::before { content: "⟶"; }

.icon-align-justify {
    font-size: 16px;
    font-family: monospace;
}
.icon-align-justify::before { content: "≡"; }

.icon-list {
    font-size: 16px;
    font-family: monospace;
}
.icon-list::before { content: "•"; }

.icon-list-numbered {
    font-size: 14px;
    font-family: Arial, sans-serif;
}
.icon-list-numbered::before { content: "1."; }

/* Unified Canvas Editor */
.document-canvas {
    background: #ffffff;
    border: none;
    border-radius: 0;
    min-height: 500px;
    position: relative;
    padding: 2rem;
    transition: all 0.3s ease;
    font-family: 'Times New Roman', serif;
    font-size: 12pt;
    line-height: 1.8;
    outline: none;
    width: 100%;
    box-sizing: border-box;
}

.document-canvas[contenteditable="true"]:focus {
    outline: none;
    box-shadow: none;
}

.document-canvas.drag-over {
    background: rgba(59, 130, 246, 0.05);
    box-shadow: inset 0 0 0 2px rgba(59, 130, 246, 0.2);
}

.document-canvas h1,
.document-canvas h2,
.document-canvas h3 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.document-canvas p {
    margin-bottom: 0.75rem;
    text-align: justify;
}


.variable-palette {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border: 1px solid #cbd5e1;
    border-radius: 0.5rem;
    padding: 1rem;
    max-height: 400px;
    overflow-y: auto;
}

.variable-chip {
    display: inline-block;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    margin: 0.25rem;
    cursor: grab;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    user-select: none;
}

.variable-chip:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.variable-chip:active {
    cursor: grabbing;
    transform: rotate(5deg) scale(0.95);
}

.variable-chip.dragging {
    position: fixed;
    z-index: 9999;
    pointer-events: none;
    transform: rotate(5deg);
    opacity: 0.8;
}


/* Variable placeholders */
.variable-placeholder {
    background: #fef3c7;
    color: #92400e;
    padding: 2px 6px;
    border-radius: 4px;
    cursor: pointer;
    border: 1px dashed #fbbf24;
    transition: all 0.2s ease;
    display: inline-block;
    margin: 0 2px;
}

.variable-placeholder:hover {
    background: #fcd34d;
    border-color: #f59e0b;
}

/* Auto-complete modal */
.autocomplete-menu {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
    max-height: 400px;
    width: 350px;
    z-index: 9999;
    display: none;
    pointer-events: auto;
    overflow: hidden;
}

.autocomplete-menu::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(2px);
    z-index: -1;
}

.autocomplete-menu.show {
    display: block;
    animation: modalFadeIn 0.2s ease-out;
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
}

/* Content area with scroll */
.autocomplete-content {
    max-height: 300px;
    overflow-y: auto;
}

/* Header for the modal */
.autocomplete-header {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.autocomplete-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin: 0;
}

.autocomplete-close {
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 0.25rem;
    transition: all 0.2s ease;
}

.autocomplete-close:hover {
    background: #f3f4f6;
    color: #374151;
}

.autocomplete-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    cursor: pointer;
    border-bottom: 1px solid #f3f4f6;
    transition: all 0.2s ease;
}

.autocomplete-item:last-child {
    border-bottom: none;
}

.autocomplete-item:hover,
.autocomplete-item.selected {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    color: #1e40af;
}

.autocomplete-item-icon {
    width: 20px;
    height: 20px;
    margin-right: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.25rem;
    font-size: 12px;
}

.autocomplete-item-content {
    flex: 1;
}

.autocomplete-item-title {
    font-weight: 600;
    font-size: 0.875rem;
    color: #374151;
    margin-bottom: 0.125rem;
}

.autocomplete-item-desc {
    font-size: 0.75rem;
    color: #6b7280;
    line-height: 1.2;
}

.autocomplete-category {
    padding: 0.5rem 1rem;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Loading state */
.autocomplete-loading {
    padding: 1rem;
    text-align: center;
    color: #6b7280;
    font-size: 0.875rem;
}

.autocomplete-no-results {
    padding: 1rem;
    text-align: center;
    color: #9ca3af;
    font-size: 0.875rem;
}

/* Highlight @ trigger */
.at-trigger {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    padding: 1px 3px;
    border-radius: 3px;
    font-weight: 500;
}

/* Input Enhancements */
#document_title {
    border: none;
    background: transparent;
    font-size: 1.5rem;
    padding: 0.5rem 0;
}

#document_title:focus {
    outline: none;
    background: rgba(74, 124, 246, 0.05);
    border-radius: 0.375rem;
    padding: 0.5rem 0.75rem;
}

/* Select Enhancements */
.form-select {
    border: 1px solid #e1e5e9;
    background: #ffffff;
    transition: all 0.2s ease;
}

.form-select:focus {
    border-color: #009ef7;
    box-shadow: 0 0 0 0.2rem rgba(0, 158, 247, 0.25);
}

/* Button Enhancements */
.btn-primary {
    background: linear-gradient(135deg, #009ef7 0%, #0d7ec7 100%);
    border: none;
    box-shadow: 0 3px 6px rgba(0, 158, 247, 0.3);
    transition: all 0.2s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 12px rgba(0, 158, 247, 0.4);
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
    border: none;
    transition: all 0.2s ease;
}

.btn-secondary:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(108, 117, 125, 0.3);
}

/* Badge Enhancements */
.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
}

/* Animation Classes */
@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.slide-in-right {
    animation: slideInRight 0.3s ease forwards;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .editor-menu {
        padding: 0.75rem;
    }
    
    .btn-toolbar {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .btn-group {
        margin-bottom: 0.5rem;
    }
    
    #editor {
        padding: 1rem;
        font-size: 11pt;
    }
}
</style>
@endpush

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Editor de Documentos
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('documentos.editor.index') }}" class="text-muted text-hover-primary">Editor</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Novo Documento</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('documentos.editor.index') }}" class="btn btn-sm fw-bold btn-secondary">
                    <i class="ki-duotone ki-arrow-left fs-2"></i>
                    Voltar
                </a>
                <button type="button" id="btn_export" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-export fs-2"></i>
                    Exportar Documento
                </button>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <div class="row g-5">
                <!--begin::Main Editor-->
                <div class="col-xl-8">
                    <!--begin::Card-->
                    <div class="card">
                        <!--begin::Header-->
                        <div class="card-header">
                            <h3 class="card-title">
                                <input type="text" id="document_title" class="form-control form-control-flush fw-bold fs-4" 
                                       placeholder="Título do documento..." value="{{ $modelo ? $modelo->nome : 'Novo Documento' }}">
                            </h3>
                            <div class="card-toolbar">
                                <div class="d-flex align-items-center gap-3">
                                    @if($modelo)
                                        <span class="badge badge-light-{{ $modelo->tipoProposicao->cor ?? 'primary' }}">
                                            {{ $modelo->tipoProposicao->nome ?? 'Modelo Geral' }}
                                        </span>
                                    @endif
                                    <select id="formato_exportacao" class="form-select form-select-sm w-auto">
                                        <option value="docx">Word (.docx)</option>
                                        <option value="pdf">PDF (.pdf)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!--end::Header-->

                        <!--begin::Body-->
                        <div class="card-body p-0">
                            <!--begin::Editor-->
                            <div class="editor-container">
                                <!--begin::Menu-->
                                <div class="editor-menu">
                                    <div class="btn-toolbar" role="toolbar">
                                        <div class="btn-group me-2" role="group">
                                            <button type="button" class="btn btn-sm btn-light" onclick="formatText('bold')" title="Negrito">
                                                <i class="fas fa-bold"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light" onclick="formatText('italic')" title="Itálico">
                                                <i class="fas fa-italic"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light" onclick="formatText('underline')" title="Sublinhado">
                                                <i class="fas fa-underline"></i>
                                            </button>
                                        </div>
                                        <div class="btn-group me-2" role="group">
                                            <button type="button" class="btn btn-sm btn-light" onclick="formatText('justifyLeft')" title="Alinhar à esquerda">
                                                <i class="fas fa-align-left"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light" onclick="formatText('justifyCenter')" title="Centralizar">
                                                <i class="fas fa-align-center"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light" onclick="formatText('justifyRight')" title="Alinhar à direita">
                                                <i class="fas fa-align-right"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light" onclick="formatText('justifyFull')" title="Justificar">
                                                <i class="fas fa-align-justify"></i>
                                            </button>
                                        </div>
                                        <div class="btn-group me-2" role="group">
                                            <button type="button" class="btn btn-sm btn-light" onclick="formatText('insertUnorderedList')" title="Lista">
                                                <i class="fas fa-list-ul"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light" onclick="formatText('insertOrderedList')" title="Lista numerada">
                                                <i class="fas fa-list-ol"></i>
                                            </button>
                                        </div>
                                        <div class="btn-group me-2" role="group">
                                            <button type="button" class="btn btn-sm btn-light" onclick="formatText('removeFormat')" title="Limpar formatação">
                                                <i class="fas fa-eraser"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light" onclick="formatText('undo')" title="Desfazer">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light" onclick="formatText('redo')" title="Refazer">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        </div>
                                        <div class="btn-group me-2" role="group">
                                            <button type="button" class="btn btn-sm btn-light" onclick="insertVariable()" title="Inserir Variável">
                                                <i class="fas fa-code"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light" onclick="insertBreak()" title="Quebra de Página">
                                                <i class="fas fa-file-alt"></i>
                                            </button>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <select class="form-select form-select-sm" onchange="formatHeading(this.value)" style="width: auto; min-width: 120px;">
                                                <option value="">Parágrafo</option>
                                                <option value="1">Título 1</option>
                                                <option value="2">Título 2</option>
                                                <option value="3">Título 3</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Menu-->

                                <!--begin::Content-->
                                <div class="editor-content">
                                    <div id="document-canvas" class="document-canvas" contenteditable="true" 
                                         style="min-height: 500px; padding: 2rem; font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.8; text-align: justify;">
                                        <h1 style="text-align: center; margin-bottom: 2rem; color: #2d3748; font-size: 18pt;">
                                            TÍTULO DO DOCUMENTO
                                        </h1>

                                        <div style="margin-bottom: 1.5rem;">
                                            <p><strong>Autor:</strong> <span class="variable-placeholder" data-variable="autor_nome" contenteditable="false">[autor_nome]</span> - <span class="variable-placeholder" data-variable="autor_cargo" contenteditable="false">[autor_cargo]</span></p>
                                            <p><strong>Data:</strong> <span class="variable-placeholder" data-variable="data_criacao" contenteditable="false">[data_criacao]</span></p>
                                            <p><strong>Legislatura:</strong> <span class="variable-placeholder" data-variable="legislatura" contenteditable="false">[legislatura]</span></p>
                                        </div>

                                        <div style="padding: 1rem; background: #f7fafc; border-left: 4px solid #4299e1; border-radius: 0.375rem; margin-bottom: 1.5rem;">
                                            <strong>EMENTA:</strong> <span class="variable-placeholder" data-variable="ementa" contenteditable="false">[ementa]</span>
                                        </div>

                                        <p style="text-align: justify; margin-bottom: 1.5rem;">
                                            A Câmara Municipal de <span class="variable-placeholder" data-variable="municipio" contenteditable="false">[municipio]</span>, Estado de <span class="variable-placeholder" data-variable="estado" contenteditable="false">[estado]</span>, no uso de suas atribuições legais, <strong>APROVA</strong>:
                                        </p>

                                        <div style="margin-bottom: 1.5rem;">
                                            <p><strong>Art. 1º</strong> - Esta lei estabelece [descrever o objetivo principal da proposição].</p>
                                            <p><strong>Art. 2º</strong> - [Detalhamento das disposições da lei].</p>
                                            <p><strong>Art. 3º</strong> - Esta Lei entra em vigor na data de sua publicação.</p>
                                        </div>

                                        <div style="text-align: right; margin-top: 3rem;">
                                            <p><span class="variable-placeholder" data-variable="municipio" contenteditable="false">[municipio]</span>, <span class="variable-placeholder" data-variable="data_criacao" contenteditable="false">[data_criacao]</span></p>
                                            <br>
                                            <p><strong><span class="variable-placeholder" data-variable="autor_nome" contenteditable="false">[autor_nome]</span></strong></p>
                                            <p><span class="variable-placeholder" data-variable="autor_cargo" contenteditable="false">[autor_cargo]</span></p>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Content-->
                            </div>
                            <!--end::Editor-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Main Editor-->

                <!--begin::Sidebar-->
                <div class="col-xl-4">
                    <!--begin::Variables Card-->
                    <div class="card mb-5">
                        <div class="card-header">
                            <h3 class="card-title">Variáveis Disponíveis</h3>
                            <div class="card-toolbar">
                                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="collapse" data-bs-target="#variables_help">
                                    <i class="ki-duotone ki-question fs-2"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="collapse" id="variables_help">
                                <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-4">
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <div class="fw-semibold">
                                            <div class="fs-7 text-gray-700">
                                                <strong>💡 Dica:</strong> Digite <code>@</code> no editor para abrir o menu de componentes e variáveis. 
                                                Use as setas ↑↓ para navegar e Enter para inserir.
                                                <br><br>
                                                Você também pode arrastar variáveis ou clicar nos placeholders para substituí-las.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="variable-palette" id="variables_palette">
                                @if($variaveisData && count($variaveisData) > 0)
                                    @foreach($variaveisData as $nome => $valor)
                                        <div class="variable-chip" 
                                             draggable="true" 
                                             data-variable="{{ $nome }}" 
                                             data-value="{{ $valor }}"
                                             title="{{ $valor }}">
                                            ${{ $nome }}
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-5">
                                        <div class="text-muted fs-6">Selecione um modelo para ver as variáveis disponíveis</div>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-4">
                                <h6 class="fw-bold text-gray-700 mb-3">Componentes de Texto</h6>
                                <div class="variable-palette">
                                    <div class="variable-chip" 
                                         draggable="true" 
                                         data-type="text-block"
                                         style="background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
                                        📝 Novo Parágrafo
                                    </div>
                                    <div class="variable-chip" 
                                         draggable="true" 
                                         data-type="title-block"
                                         style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                                        📑 Novo Título
                                    </div>
                                    <div class="variable-chip" 
                                         draggable="true" 
                                         data-type="article-block"
                                         style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                                        ⚖️ Novo Artigo
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Variables Card-->

                    <!--begin::Options Card-->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Configurações</h3>
                        </div>
                        <div class="card-body">
                            <!--begin::Model Selection-->
                            <div class="mb-5">
                                <label class="form-label fw-semibold">Modelo Base</label>
                                <select id="modelo_select" class="form-select" data-control="select2" data-placeholder="Selecione um modelo">
                                    <option value="">Documento em branco</option>
                                    @foreach($modelos as $modeloOption)
                                        <option value="{{ $modeloOption->id }}" 
                                                {{ $modelo && $modelo->id == $modeloOption->id ? 'selected' : '' }}>
                                            {{ $modeloOption->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Model Selection-->

                            <!--begin::Project Selection-->
                            <div class="mb-5">
                                <label class="form-label fw-semibold">Projeto Associado</label>
                                <select id="projeto_select" class="form-select" data-control="select2" data-placeholder="Selecione um projeto">
                                    <option value="">Nenhum projeto</option>
                                    @foreach($projetos as $projetoOption)
                                        <option value="{{ $projetoOption->id }}" 
                                                {{ $projeto && $projeto->id == $projetoOption->id ? 'selected' : '' }}>
                                            {{ $projetoOption->numero_proposicao ?? $projetoOption->titulo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Project Selection-->

                            <!--begin::Actions-->
                            <div class="d-flex flex-column gap-2">
                                <button type="button" id="btn_refresh_variables" class="btn btn-sm btn-light-success w-100">
                                    <i class="ki-duotone ki-arrows-circle fs-2"></i>
                                    Atualizar Variáveis
                                </button>
                            </div>
                            <!--end::Actions-->
                        </div>
                    </div>
                    <!--end::Options Card-->
                </div>
                <!--end::Sidebar-->
            </div>

        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<!--begin::Export Modal-->
<div class="modal fade" id="export_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exportar Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="export_form">
                    <div class="mb-4">
                        <label class="form-label">Título do Documento</label>
                        <input type="text" id="export_title" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Formato de Exportação</label>
                        <select id="export_format" class="form-select" required>
                            <option value="docx">Microsoft Word (.docx)</option>
                            <option value="pdf">Adobe PDF (.pdf)</option>
                        </select>
                    </div>
                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4">
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-7 text-gray-700">
                                    O documento será processado e as variáveis serão substituídas automaticamente pelos valores atuais.
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btn_confirm_export" class="btn btn-primary">
                    <span class="indicator-label">Exportar Documento</span>
                    <span class="indicator-progress">Processando...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
<!--end::Export Modal-->

<!--begin::Autocomplete Modal Container-->
<div id="autocomplete-menu" class="autocomplete-menu" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(2px);" onclick="checkBackdropClick(event)">
    <div class="modal-content" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2); width: 350px; max-height: 400px; overflow: hidden;" onclick="event.stopPropagation()">
        <div class="autocomplete-header" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 1rem 1.25rem; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between;">
            <h5 class="autocomplete-title" style="font-size: 0.875rem; font-weight: 600; color: #374151; margin: 0;">💡 Inserir Componente</h5>
            <button type="button" class="autocomplete-close" onclick="hideAutoComplete()" style="background: none; border: none; color: #6b7280; cursor: pointer; padding: 0.25rem; border-radius: 0.25rem;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="autocomplete-content" id="autocomplete-content" style="max-height: 300px; overflow-y: auto;"></div>
    </div>
</div>
<!--end::Autocomplete Modal Container-->

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Select2
    $('[data-control="select2"]').select2();

    // Unified canvas drag & drop editor
    let draggedElement = null;
    let draggedData = null;

    // Initialize drag & drop for variable chips and canvas editing
    function initializeDragAndDrop() {
        // Variable chips drag start
        document.addEventListener('dragstart', function(e) {
            if (e.target.classList.contains('variable-chip')) {
                draggedElement = e.target;
                draggedData = {
                    type: e.target.dataset.type || 'variable',
                    variable: e.target.dataset.variable,
                    value: e.target.dataset.value
                };
                
                e.target.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'copy';
                e.dataTransfer.setData('text/plain', ''); // Required for Firefox
            }
        });

        document.addEventListener('dragend', function(e) {
            if (e.target.classList.contains('variable-chip')) {
                e.target.classList.remove('dragging');
            }
        });

        // Canvas drop events
        const canvas = document.getElementById('document-canvas');
        
        canvas.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'copy';
            canvas.classList.add('drag-over');
        });

        canvas.addEventListener('dragleave', function(e) {
            if (!canvas.contains(e.relatedTarget)) {
                canvas.classList.remove('drag-over');
            }
        });

        canvas.addEventListener('drop', function(e) {
            e.preventDefault();
            canvas.classList.remove('drag-over');
            
            if (draggedData && draggedData.type === 'variable') {
                // Insert variable at cursor position or at the end
                insertVariableAtCursor(draggedData.variable, draggedData.value);
                showVariableInsertedFeedback(draggedData.variable);
            } else if (draggedData && draggedData.type !== 'variable') {
                // Insert text components at cursor position
                insertTextComponentAtCursor(draggedData.type);
                showDropFeedback(draggedData.type);
            }
            
            draggedData = null;
        });

        // Variable placeholder clicks for editing
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('variable-placeholder')) {
                e.preventDefault();
                showVariableMenu(e.target);
            }
        });

        // Variable chips click to insert
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('variable-chip')) {
                const variable = e.target.dataset.variable;
                const value = e.target.dataset.value;
                const type = e.target.dataset.type;
                
                if (type && type !== 'variable') {
                    insertTextComponentAtCursor(type);
                    showDropFeedback(type);
                } else if (variable) {
                    insertVariableAtCursor(variable, value);
                    showVariableInsertedFeedback(variable);
                }
            }
        });
    }

    // Insert variable at cursor position in the canvas
    function insertVariableAtCursor(variable, value) {
        const canvas = document.getElementById('document-canvas');
        const selection = window.getSelection();
        
        // Create variable placeholder element
        const placeholder = document.createElement('span');
        placeholder.className = 'variable-placeholder';
        placeholder.setAttribute('data-variable', variable);
        placeholder.setAttribute('data-value', value || '');
        placeholder.setAttribute('contenteditable', 'false');
        placeholder.textContent = `[${variable}]`;
        placeholder.title = value || 'Valor padrão';

        // Insert at cursor position
        if (selection.rangeCount > 0 && canvas.contains(selection.anchorNode)) {
            const range = selection.getRangeAt(0);
            range.deleteContents();
            range.insertNode(placeholder);
            
            // Move cursor after the inserted element
            range.setStartAfter(placeholder);
            range.collapse(true);
            selection.removeAllRanges();
            selection.addRange(range);
        } else {
            // Fallback: insert at the end of canvas
            canvas.appendChild(placeholder);
        }
        
        // Focus back to canvas
        canvas.focus();
    }

    // Insert text components at cursor position
    function insertTextComponentAtCursor(type) {
        const canvas = document.getElementById('document-canvas');
        const selection = window.getSelection();
        let element;

        switch(type) {
            case 'text-block':
                element = document.createElement('p');
                element.textContent = 'Digite seu novo parágrafo aqui...';
                element.style.marginBottom = '1rem';
                break;
            case 'title-block':
                element = document.createElement('h2');
                element.textContent = 'Novo Título';
                element.style.textAlign = 'center';
                element.style.marginBottom = '1.5rem';
                element.style.marginTop = '1.5rem';
                break;
            case 'article-block':
                element = document.createElement('p');
                element.innerHTML = '<strong>Art. X°</strong> - Digite o conteúdo do novo artigo aqui...';
                element.style.marginBottom = '1rem';
                break;
            default:
                return;
        }

        // Insert at cursor position
        if (selection.rangeCount > 0 && canvas.contains(selection.anchorNode)) {
            const range = selection.getRangeAt(0);
            
            // Insert line break before if needed
            if (range.startContainer.nodeType === Node.TEXT_NODE && range.startOffset > 0) {
                range.insertNode(document.createElement('br'));
            }
            
            range.insertNode(element);
            
            // Move cursor inside the new element
            const newRange = document.createRange();
            newRange.selectNodeContents(element);
            newRange.collapse(false);
            selection.removeAllRanges();
            selection.addRange(newRange);
        } else {
            // Fallback: insert at the end of canvas
            canvas.appendChild(document.createElement('br'));
            canvas.appendChild(element);
        }
        
        // Focus the new element
        element.focus();
    }

    function showVariableMenu(placeholder) {
        const variable = placeholder.dataset.variable;
        const availableVariables = Array.from(document.querySelectorAll('.variable-chip')).map(chip => ({
            name: chip.dataset.variable,
            value: chip.dataset.value
        }));

        let optionsHtml = availableVariables.map(v => 
            `<option value="${v.name}" ${v.name === variable ? 'selected' : ''}>${v.name} - ${v.value}</option>`
        ).join('');

        const selectHtml = `
            <select class="form-select form-select-sm" onchange="updateVariablePlaceholder(this, '${variable}')">
                ${optionsHtml}
            </select>
        `;

        placeholder.innerHTML = selectHtml;
        placeholder.querySelector('select').focus();
    }

    window.updateVariablePlaceholder = function(select, originalVariable) {
        const newVariable = select.value;
        const placeholder = select.closest('.variable-placeholder');
        
        placeholder.dataset.variable = newVariable;
        placeholder.innerHTML = `[${newVariable}]`;
        
        showVariableUpdatedFeedback(originalVariable, newVariable);
    };

    // Enhanced toolbar integration with canvas
    window.insertVariable = function() {
        const availableVariables = Array.from(document.querySelectorAll('.variable-chip'));
        if (availableVariables.length > 0) {
            const variable = availableVariables[0].dataset.variable;
            const value = availableVariables[0].dataset.value;
            insertVariableAtCursor(variable, value);
            showVariableInsertedFeedback(variable);
        }
    };

    window.insertBreak = function() {
        const canvas = document.getElementById('document-canvas');
        const selection = window.getSelection();
        
        if (selection.rangeCount > 0 && canvas.contains(selection.anchorNode)) {
            const range = selection.getRangeAt(0);
            const br = document.createElement('br');
            const pageBreak = document.createElement('div');
            pageBreak.style.cssText = 'page-break-after: always; border-top: 2px dashed #ccc; margin: 2rem 0; text-align: center; color: #999; font-size: 12px;';
            pageBreak.textContent = '--- Quebra de Página ---';
            
            range.insertNode(pageBreak);
            range.insertNode(br);
        }
    };

    // Enhanced toolbar functions
    window.formatText = function(command, value = null) {
        document.execCommand(command, false, value);
        showFormatFeedback();
    };

    window.formatHeading = function(level) {
        if (level) {
            document.execCommand('formatBlock', false, 'h' + level);
        } else {
            document.execCommand('formatBlock', false, 'p');
        }
        showFormatFeedback();
    };

    // Enhanced feedback functions
    function showFormatFeedback() {
        const feedback = document.createElement('div');
        feedback.innerHTML = '✓ Formatação aplicada';
        feedback.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        
        document.body.appendChild(feedback);
        setTimeout(() => feedback.style.opacity = '1', 10);
        setTimeout(() => {
            feedback.style.opacity = '0';
            setTimeout(() => document.body.removeChild(feedback), 300);
        }, 1500);
    }

    function showVariableInsertedFeedback(variable) {
        const feedback = document.createElement('div');
        feedback.innerHTML = `✓ Variável \${${variable}} inserida`;
        feedback.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #3b82f6;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        
        document.body.appendChild(feedback);
        setTimeout(() => feedback.style.opacity = '1', 10);
        setTimeout(() => {
            feedback.style.opacity = '0';
            setTimeout(() => document.body.removeChild(feedback), 300);
        }, 2000);
    }

    // Auto-save functionality for canvas
    let autoSaveTimeout;
    function setupAutoSave() {
        const canvas = document.getElementById('document-canvas');
        canvas.addEventListener('input', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                const content = canvas.innerHTML;
                localStorage.setItem('documento_draft', content);
                showAutoSaveFeedback();
            }, 2000);
        });

        // Load draft on page load
        const draft = localStorage.getItem('documento_draft');
        if (draft && confirm('Foi encontrado um rascunho salvo. Deseja carregá-lo?')) {
            canvas.innerHTML = draft;
        }
    }

    function showAutoSaveFeedback() {
        const feedback = document.createElement('div');
        feedback.innerHTML = '💾 Rascunho salvo automaticamente';
        feedback.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #6b7280;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 11px;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        
        document.body.appendChild(feedback);
        setTimeout(() => feedback.style.opacity = '1', 10);
        setTimeout(() => {
            feedback.style.opacity = '0';
            setTimeout(() => document.body.removeChild(feedback), 300);
        }, 1000);
    }

    // Auto-complete system with @
    let autocompleteMenu = null;
    let selectedIndex = -1;
    let autocompleteItems = [];
    let currentAtPosition = null;
    let lastCursorPosition = { x: 0, y: 0 };

    function initializeAutoComplete() {
        const canvas = document.getElementById('document-canvas');
        autocompleteMenu = document.getElementById('autocomplete-menu');

        // Listen for multiple events to detect @ symbol
        canvas.addEventListener('input', handleAutoCompleteInput);
        canvas.addEventListener('keyup', handleAutoCompleteKeyup);
        canvas.addEventListener('keydown', handleAutoCompleteKeydown);
        
        // Track cursor position with mouse
        canvas.addEventListener('click', function(e) {
            lastCursorPosition.x = e.clientX;
            lastCursorPosition.y = e.clientY;
        });
        
        // Note: Click outside handling is now done via checkBackdropClick function
        
        // Global ESC key handler
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && autocompleteMenu.classList.contains('show')) {
                hideAutoComplete();
            }
        });
    }
    
    function handleAutoCompleteInput(e) {
        // Check for @ symbol and store exact position
        const canvas = document.getElementById('document-canvas');
        const selection = window.getSelection();
        
        if (selection.rangeCount === 0) return;
        
        // Get the current text content around the cursor
        const range = selection.getRangeAt(0);
        let textNode = range.startContainer;
        
        // If we're not in a text node, try to find the nearest text node
        if (textNode.nodeType !== Node.TEXT_NODE) {
            // Try to get text from the canvas directly
            const canvasText = canvas.textContent || canvas.innerText || '';
            
            // Simple check if @ is at the end
            if (canvasText.endsWith('@')) {
                // Store the position more accurately
                currentAtPosition = {
                    node: textNode,
                    offset: range.startOffset,
                    range: range.cloneRange()
                };
                console.log('@ found at end, stored position:', currentAtPosition);
                showAutoComplete();
                return;
            }
        } else {
            const textContent = textNode.textContent || '';
            const cursorPosition = range.startOffset;
            
            // Check if @ is immediately before cursor
            if (cursorPosition > 0 && textContent.charAt(cursorPosition - 1) === '@') {
                // Store exact position of the @
                currentAtPosition = {
                    node: textNode,
                    offset: cursorPosition,
                    atOffset: cursorPosition - 1,
                    range: range.cloneRange()
                };
                console.log('@ found in text node, stored position:', currentAtPosition);
                showAutoComplete();
                return;
            }
        }
        
        // If menu is showing and we didn't find @, hide it
        if (autocompleteMenu.classList.contains('show')) {
            hideAutoComplete();
        }
    }

    function handleAutoCompleteKeyup(e) {
        if (e.key === '@') {
            setTimeout(() => { // Small delay to ensure text is inserted
                const selection = window.getSelection();
                if (selection.rangeCount > 0) {
                    const range = selection.getRangeAt(0);
                    const canvas = document.getElementById('document-canvas');
                    
                    // Check if canvas contains @
                    const canvasText = canvas.textContent || '';
                    
                    if (canvasText.includes('@')) {
                        currentAtPosition = {
                            node: range.startContainer,
                            offset: range.startOffset
                        };
                        showAutoComplete();
                    }
                }
            }, 50); // Increased delay for more reliable detection
        }
    }

    function handleAutoCompleteKeydown(e) {
        if (!autocompleteMenu.classList.contains('show')) return;

        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                selectedIndex = Math.min(selectedIndex + 1, autocompleteItems.length - 1);
                updateSelection();
                break;
            case 'ArrowUp':
                e.preventDefault();
                selectedIndex = Math.max(selectedIndex - 1, 0);
                updateSelection();
                break;
            case 'Enter':
            case 'Tab':
                e.preventDefault();
                if (selectedIndex >= 0 && autocompleteItems[selectedIndex]) {
                    selectAutoCompleteItem(autocompleteItems[selectedIndex]);
                }
                break;
            case 'Escape':
                e.preventDefault();
                hideAutoComplete();
                break;
        }
    }

    function showAutoComplete() {
        console.log('showAutoComplete called - menu element:', autocompleteMenu); // Debug
        
        // Load suggestions
        loadAutoCompleteSuggestions();
        
        // Show modal using both class and style for guaranteed visibility
        autocompleteMenu.style.display = 'block';
        autocompleteMenu.classList.add('show');
        selectedIndex = 0;
        
        console.log('Modal should be visible now - display:', autocompleteMenu.style.display); // Debug
        
        // Update selection for first item
        updateSelection();
        
        // Focus on modal for keyboard navigation
        setTimeout(() => autocompleteMenu.focus(), 100);
    }
    
    // Function to handle backdrop clicks
    function checkBackdropClick(event) {
        // Only close if clicked directly on the backdrop (not on modal content)
        if (event.target === event.currentTarget) {
            hideAutoComplete();
        }
    }
    
    // Make checkBackdropClick global for onclick handler
    window.checkBackdropClick = checkBackdropClick;

    function hideAutoComplete() {
        autocompleteMenu.style.display = 'none';
        autocompleteMenu.classList.remove('show');
        selectedIndex = -1;
        autocompleteItems = [];
        currentAtPosition = null;
        
        // Return focus to canvas
        const canvas = document.getElementById('document-canvas');
        canvas.focus();
    }
    
    // Make hideAutoComplete global for button onclick
    window.hideAutoComplete = hideAutoComplete;

    function loadAutoCompleteSuggestions() {
        const content = document.getElementById('autocomplete-content');
        
        // Get available variables
        const availableVariables = Array.from(document.querySelectorAll('.variable-chip')).map(chip => ({
            type: 'variable',
            name: chip.dataset.variable,
            value: chip.dataset.value || '',
            title: `@${chip.dataset.variable}`,
            description: chip.dataset.value || 'Variável do modelo',
            icon: '🔤'
        }));

        // Pre-defined components
        const components = [
            {
                type: 'paragraph',
                name: 'paragrafo',
                title: '@paragrafo',
                description: 'Novo parágrafo de texto',
                icon: '📝'
            },
            {
                type: 'title',
                name: 'titulo',
                title: '@titulo',
                description: 'Novo título ou cabeçalho',
                icon: '📑'
            },
            {
                type: 'article',
                name: 'artigo',
                title: '@artigo',
                description: 'Novo artigo da lei',
                icon: '⚖️'
            },
            {
                type: 'signature',
                name: 'assinatura',
                title: '@assinatura',
                description: 'Bloco de assinatura',
                icon: '✍️'
            },
            {
                type: 'date',
                name: 'data',
                title: '@data',
                description: 'Data atual formatada',
                icon: '📅'
            },
            {
                type: 'pagebreak',
                name: 'quebra',
                title: '@quebra',
                description: 'Quebra de página',
                icon: '📄'
            }
        ];

        autocompleteItems = [...components, ...availableVariables];

        let html = '';
        
        if (components.length > 0) {
            html += '<div style="padding: 0.5rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Componentes</div>';
            components.forEach((item, index) => {
                html += `
                    <div class="autocomplete-item ${index === 0 ? 'selected' : ''}" data-index="${index}" data-type="${item.type}" data-name="${item.name}" 
                         style="display: flex; align-items: center; padding: 0.75rem 1rem; cursor: pointer; border-bottom: 1px solid #f3f4f6; transition: all 0.2s ease; ${index === 0 ? 'background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); color: #1e40af;' : ''}">
                        <div style="width: 20px; height: 20px; margin-right: 0.75rem; display: flex; align-items: center; justify-content: center; border-radius: 0.25rem; font-size: 12px; background: #e0f2fe;">${item.icon}</div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; font-size: 0.875rem; color: #374151; margin-bottom: 0.125rem;">${item.title}</div>
                            <div style="font-size: 0.75rem; color: #6b7280; line-height: 1.2;">${item.description}</div>
                        </div>
                    </div>
                `;
            });
        }

        if (availableVariables.length > 0) {
            html += '<div style="padding: 0.5rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Variáveis</div>';
            availableVariables.forEach((item, index) => {
                const globalIndex = components.length + index;
                const isSelected = globalIndex === 0 && components.length === 0;
                html += `
                    <div class="autocomplete-item" data-index="${globalIndex}" data-type="${item.type}" data-name="${item.name}" data-value="${item.value}"
                         style="display: flex; align-items: center; padding: 0.75rem 1rem; cursor: pointer; border-bottom: 1px solid #f3f4f6; transition: all 0.2s ease; ${isSelected ? 'background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); color: #1e40af;' : ''}">
                        <div style="width: 20px; height: 20px; margin-right: 0.75rem; display: flex; align-items: center; justify-content: center; border-radius: 0.25rem; font-size: 12px; background: #fef3c7;">${item.icon}</div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; font-size: 0.875rem; color: #374151; margin-bottom: 0.125rem;">${item.title}</div>
                            <div style="font-size: 0.75rem; color: #6b7280; line-height: 1.2;">${item.description}</div>
                        </div>
                    </div>
                `;
            });
        }

        if (autocompleteItems.length === 0) {
            html = '<div class="autocomplete-no-results">Nenhum componente disponível</div>';
        }

        content.innerHTML = html;

        // Add click listeners
        const items = content.querySelectorAll('.autocomplete-item');
        
        items.forEach(item => {
            item.addEventListener('click', () => {
                const index = parseInt(item.dataset.index);
                selectAutoCompleteItem(autocompleteItems[index]);
            });
        });
    }

    function updateSelection() {
        const items = document.querySelectorAll('.autocomplete-item');
        items.forEach((item, index) => {
            item.classList.toggle('selected', index === selectedIndex);
        });

        // Scroll into view
        if (items[selectedIndex]) {
            items[selectedIndex].scrollIntoView({ block: 'nearest' });
        }
    }

    function selectAutoCompleteItem(item) {
        const canvas = document.getElementById('document-canvas');
        const selection = window.getSelection();
        
        try {
            // Find the exact position of the @ symbol
            if (!currentAtPosition) {
                console.log('No @ position stored, trying to find it');
                hideAutoComplete();
                return;
            }

            // Create element to insert
            let element;
            
            switch(item.type) {
                case 'variable':
                    element = createVariablePlaceholder(item.name, item.value);
                    break;
                case 'paragraph':
                    element = createParagraphComponent();
                    break;
                case 'title':
                    element = createTitleComponent();
                    break;
                case 'article':
                    element = createArticleComponent();
                    break;
                case 'signature':
                    element = createSignatureComponent();
                    break;
                case 'date':
                    element = createDateComponent();
                    break;
                case 'pagebreak':
                    element = createPageBreakComponent();
                    break;
                default:
                    return;
            }

            // Use stored position to replace @ symbol
            let found = false;
            
            if (currentAtPosition.node && currentAtPosition.node.nodeType === Node.TEXT_NODE && currentAtPosition.atOffset !== undefined) {
                // We have exact text node and position
                const textNode = currentAtPosition.node;
                const atIndex = currentAtPosition.atOffset;
                
                if (textNode.textContent.charAt(atIndex) === '@') {
                    const range = document.createRange();
                    range.setStart(textNode, atIndex);
                    range.setEnd(textNode, atIndex + 1);
                    
                    // Delete the @ character
                    range.deleteContents();
                    
                    // Insert the new element
                    range.insertNode(element);
                    
                    // Position cursor after the inserted element
                    const newRange = document.createRange();
                    newRange.setStartAfter(element);
                    newRange.collapse(true);
                    selection.removeAllRanges();
                    selection.addRange(newRange);
                    
                    found = true;
                    console.log('Replaced @ at stored position');
                }
            }
            
            // Fallback: search for @ symbol if stored position didn't work
            if (!found) {
                console.log('Stored position failed, searching for @ symbol');
                const walker = document.createTreeWalker(
                    canvas,
                    NodeFilter.SHOW_TEXT,
                    null,
                    false
                );

                let textNode;
                
                while (textNode = walker.nextNode()) {
                    const atIndex = textNode.textContent.lastIndexOf('@');
                    if (atIndex !== -1) {
                        // Found the @ symbol, replace it
                        const range = document.createRange();
                        range.setStart(textNode, atIndex);
                        range.setEnd(textNode, atIndex + 1);
                        
                        // Delete the @ character
                        range.deleteContents();
                        
                        // Insert the new element
                        range.insertNode(element);
                        
                        // Position cursor after the inserted element
                        const newRange = document.createRange();
                        newRange.setStartAfter(element);
                        newRange.collapse(true);
                        selection.removeAllRanges();
                        selection.addRange(newRange);
                        
                        found = true;
                        console.log('Found and replaced @ via tree walker');
                        break;
                    }
                }
            }
            
            if (!found) {
                console.log('@ symbol not found, inserting at cursor position');
                // Fallback: insert at current cursor position
                if (selection.rangeCount > 0) {
                    const range = selection.getRangeAt(0);
                    range.insertNode(element);
                    
                    const newRange = document.createRange();
                    newRange.setStartAfter(element);
                    newRange.collapse(true);
                    selection.removeAllRanges();
                    selection.addRange(newRange);
                }
            }

            showComponentInsertedFeedback(item.title);

        } catch (error) {
            console.error('Error inserting component:', error);
        }

        hideAutoComplete();
        canvas.focus();
    }

    function createVariablePlaceholder(name, value) {
        const span = document.createElement('span');
        span.className = 'variable-placeholder';
        span.setAttribute('data-variable', name);
        span.setAttribute('data-value', value);
        span.setAttribute('contenteditable', 'false');
        span.textContent = `[${name}]`;
        span.title = value || 'Valor padrão';
        return span;
    }

    function createParagraphComponent() {
        const p = document.createElement('p');
        p.textContent = 'Digite seu novo parágrafo aqui...';
        p.style.marginBottom = '1rem';
        return p;
    }

    function createTitleComponent() {
        const h2 = document.createElement('h2');
        h2.textContent = 'Novo Título';
        h2.style.textAlign = 'center';
        h2.style.marginBottom = '1.5rem';
        h2.style.marginTop = '1.5rem';
        return h2;
    }

    function createArticleComponent() {
        const p = document.createElement('p');
        p.innerHTML = '<strong>Art. X°</strong> - Digite o conteúdo do novo artigo aqui...';
        p.style.marginBottom = '1rem';
        return p;
    }

    function createSignatureComponent() {
        const div = document.createElement('div');
        div.style.textAlign = 'right';
        div.style.marginTop = '3rem';
        div.innerHTML = `
            <p><span class="variable-placeholder" data-variable="municipio" contenteditable="false">[municipio]</span>, <span class="variable-placeholder" data-variable="data_criacao" contenteditable="false">[data_criacao]</span></p>
            <br>
            <p><strong><span class="variable-placeholder" data-variable="autor_nome" contenteditable="false">[autor_nome]</span></strong></p>
            <p><span class="variable-placeholder" data-variable="autor_cargo" contenteditable="false">[autor_cargo]</span></p>
        `;
        return div;
    }

    function createDateComponent() {
        const span = document.createElement('span');
        const today = new Date();
        const dateStr = today.toLocaleDateString('pt-BR', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        span.textContent = dateStr;
        span.style.fontWeight = '500';
        return span;
    }

    function createPageBreakComponent() {
        const div = document.createElement('div');
        div.style.cssText = 'page-break-after: always; border-top: 2px dashed #ccc; margin: 2rem 0; text-align: center; color: #999; font-size: 12px; padding: 0.5rem;';
        div.textContent = '--- Quebra de Página ---';
        return div;
    }

    function showComponentInsertedFeedback(componentName) {
        const feedback = document.createElement('div');
        feedback.innerHTML = `✓ ${componentName} inserido`;
        feedback.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        
        document.body.appendChild(feedback);
        setTimeout(() => feedback.style.opacity = '1', 10);
        setTimeout(() => {
            feedback.style.opacity = '0';
            setTimeout(() => document.body.removeChild(feedback), 300);
        }, 2000);
    }

    // Test function for autocomplete (call from console: testAutoComplete())
    window.testAutoComplete = function() {
        showAutoComplete();
    };

    // Initialize all systems
    initializeDragAndDrop();
    setupAutoSave();
    initializeAutoComplete();

    // Additional feedback functions for canvas
    function showDropFeedback(type) {
        const feedback = document.createElement('div');
        const messages = {
            'variable': '✓ Variável adicionada',
            'text-block': '✓ Parágrafo adicionado',
            'title-block': '✓ Título adicionado',
            'article-block': '✓ Artigo adicionado'
        };
        
        feedback.innerHTML = messages[type] || '✓ Elemento adicionado';
        feedback.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        
        document.body.appendChild(feedback);
        setTimeout(() => feedback.style.opacity = '1', 10);
        setTimeout(() => {
            feedback.style.opacity = '0';
            setTimeout(() => document.body.removeChild(feedback), 300);
        }, 2000);
    }

    function showVariableUpdatedFeedback(oldVar, newVar) {
        const feedback = document.createElement('div');
        feedback.innerHTML = `✓ Variável alterada: ${oldVar} → ${newVar}`;
        feedback.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #3b82f6;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        
        document.body.appendChild(feedback);
        setTimeout(() => feedback.style.opacity = '1', 10);
        setTimeout(() => {
            feedback.style.opacity = '0';
            setTimeout(() => document.body.removeChild(feedback), 300);
        }, 2000);
    }

    function showRemovalFeedback() {
        const feedback = document.createElement('div');
        feedback.innerHTML = '✓ Bloco removido';
        feedback.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #ef4444;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        
        document.body.appendChild(feedback);
        setTimeout(() => feedback.style.opacity = '1', 10);
        setTimeout(() => {
            feedback.style.opacity = '0';
            setTimeout(() => document.body.removeChild(feedback), 300);
        }, 1500);
    }

    // Model selection change
    document.getElementById('modelo_select').addEventListener('change', function() {
        const modeloId = this.value;
        if (modeloId) {
            loadModelVariables(modeloId);
        } else {
            document.getElementById('variables_list').innerHTML = `
                <div class="text-center py-5">
                    <div class="text-muted fs-6">Selecione um modelo para ver as variáveis disponíveis</div>
                </div>
            `;
        }
    });

    // Project selection change
    document.getElementById('projeto_select').addEventListener('change', function() {
        refreshVariables();
    });

    // Refresh variables
    document.getElementById('btn_refresh_variables').addEventListener('click', function() {
        refreshVariables();
    });

    // Export button
    document.getElementById('btn_export').addEventListener('click', function() {
        document.getElementById('export_title').value = document.getElementById('document_title').value;
        document.getElementById('export_format').value = document.getElementById('formato_exportacao').value;
        
        const modal = new bootstrap.Modal(document.getElementById('export_modal'));
        modal.show();
    });

    // Confirm export
    document.getElementById('btn_confirm_export').addEventListener('click', function() {
        const button = this;
        const title = document.getElementById('export_title').value;
        const format = document.getElementById('export_format').value;
        const content = document.getElementById('document-canvas').innerHTML;
        const modeloId = document.getElementById('modelo_select').value;
        const projetoId = document.getElementById('projeto_select').value;

        if (!title.trim()) {
            Swal.fire('Erro', 'Digite um título para o documento.', 'error');
            return;
        }

        button.setAttribute('data-kt-indicator', 'on');
        button.disabled = true;

        // Collect variables data
        const variaveis = {};
        document.querySelectorAll('.variable-item').forEach(item => {
            const variable = item.getAttribute('data-variable');
            const value = item.getAttribute('data-value');
            if (variable && value) {
                variaveis[variable] = value;
            }
        });

        // Prepare data for export
        const exportData = {
            titulo: title,
            conteudo: content,
            variaveis: variaveis,
            formato_exportacao: format
        };
        
        // Only add modelo_id if it exists and is not empty
        if (modeloId && modeloId.trim() !== '') {
            exportData.modelo_id = modeloId;
        } else {
            // Create a default modelo_id or handle as blank document
            exportData.modelo_id = null;
        }
        
        // Only add projeto_id if it exists and is not empty
        if (projetoId && projetoId.trim() !== '') {
            exportData.projeto_id = projetoId;
        }
        
        console.log('Sending export data:', exportData); // Debug

        fetch('{{ route("documentos.editor.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(exportData)
        })
        .then(response => {
            console.log('Response status:', response.status); // Debug
            console.log('Response headers:', response.headers); // Debug
            
            // Check if response is actually JSON
            if (response.headers.get('content-type')?.includes('application/json')) {
                return response.json();
            } else {
                // If not JSON, get text to see what's being returned
                return response.text().then(text => {
                    console.error('Non-JSON response received:', text);
                    throw new Error('Server returned non-JSON response: ' + text.substring(0, 100) + '...');
                });
            }
        })
        .then(data => {
            console.log('Response data:', data); // Debug
            
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('export_modal')).hide();
                
                // Download file
                window.location.href = data.download_url;
                
                Swal.fire('Sucesso', data.message, 'success');
            } else {
                Swal.fire('Erro', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Export error:', error);
            Swal.fire('Erro', 'Erro ao exportar documento: ' + error.message, 'error');
        })
        .finally(() => {
            button.removeAttribute('data-kt-indicator');
            button.disabled = false;
        });
    });

    // Functions for API calls
    function loadModelVariables(modeloId) {
        fetch(`/admin/documentos/editor/variaveis/${modeloId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateVariablesList(data.variaveis);
                }
            })
            .catch(error => {
                console.error('Error loading variables:', error);
            });
    }

    function refreshVariables() {
        const modeloId = document.getElementById('modelo_select').value;
        const projetoId = document.getElementById('projeto_select').value;
        
        if (!modeloId) return;

        const url = projetoId ? 
            '/admin/documentos/editor/preencher-variaveis' : 
            `/admin/documentos/editor/variaveis/${modeloId}`;

        const requestData = projetoId ? {
            modelo_id: modeloId,
            projeto_id: projetoId
        } : {};

        fetch(url, {
            method: projetoId ? 'POST' : 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: projetoId ? JSON.stringify(requestData) : null
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateVariablesList(data.variaveis);
            }
        })
        .catch(error => {
            console.error('Error refreshing variables:', error);
        });
    }

    function updateVariablesList(variaveis) {
        const container = document.getElementById('variables_palette');
        
        if (!variaveis || Object.keys(variaveis).length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <div class="text-muted fs-6">Nenhuma variável disponível</div>
                </div>
            `;
            return;
        }

        let html = '';
        Object.entries(variaveis).forEach(([nome, valor]) => {
            html += `
                <div class="variable-chip" 
                     draggable="true" 
                     data-variable="${nome}" 
                     data-value="${valor || ''}"
                     title="${valor || 'Valor padrão'}">
                    $${nome}
                </div>
            `;
        });
        
        container.innerHTML = html;
        
        // Re-initialize drag events for new elements
        initializeDragAndDrop();
    }
});
</script>
@endpush