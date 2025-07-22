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
    cursor: pointer;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    user-select: none;
}

.variable-chip:hover {
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.variable-chip:active {
    cursor: pointer;
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

@keyframes slideOutRight {
    from {
        opacity: 1;
        transform: translateX(0);
    }
    to {
        opacity: 0;
        transform: translateX(30px);
    }
}

/* Estilos para cabeçalho e rodapé */
.document-header {
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    border-radius: 8px;
    transition: all 0.2s ease;
}

.document-header:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.document-footer {
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    border-radius: 8px;
    transition: all 0.2s ease;
}

.document-footer:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Estilos para importação de documentos */
.drop-zone {
    transition: all 0.3s ease !important;
}

.drop-zone:hover {
    border-color: #007bff !important;
    background-color: #f0f8ff !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
}

.drop-zone.dragover {
    border-color: #28a745 !important;
    background-color: #f0fff4 !important;
    transform: scale(1.02);
}

#import_document_modal .modal-lg {
    max-width: 800px;
}

.file-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    margin-right: 12px;
}

.import-progress {
    background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
    height: 4px;
    border-radius: 2px;
    transition: width 0.3s ease;
}

.slide-in-right {
    animation: slideInRight 0.3s ease forwards;
}

/* SweetAlert Custom Styles */
.swal-wide {
    width: 600px !important;
    max-width: 90vw !important;
}

.swal2-html-container .text-start {
    text-align: left !important;
}

.swal2-html-container .alert {
    border-radius: 8px;
    padding: 12px 16px;
}

.swal2-html-container .alert-info {
    background-color: rgba(13, 202, 240, 0.1);
    border: 1px solid rgba(13, 202, 240, 0.2);
    color: #055160;
}

.swal2-html-container .alert-warning {
    background-color: rgba(255, 193, 7, 0.1);
    border: 1px solid rgba(255, 193, 7, 0.2);
    color: #664d03;
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
                <button type="button" id="btn_import_document" class="btn btn-sm fw-bold btn-light-warning">
                    <i class="ki-duotone ki-file-up fs-2"></i>
                    Importar Documento
                </button>
                <button type="button" id="btn_save_template" class="btn btn-sm fw-bold btn-light-success">
                    <i class="ki-duotone ki-file-added fs-2"></i>
                    Salvar como Modelo
                </button>
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
                                        <option value="rtf">Word (.rtf)</option>
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
                                        <div class="btn-group me-2" role="group">
                                            <button type="button" class="btn btn-sm btn-light-primary" onclick="insertHeader()" title="Inserir Cabeçalho Oficial">
                                                <i class="fas fa-heading"></i> Cabeçalho
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light-info" onclick="insertFooter()" title="Inserir Rodapé Oficial">
                                                <i class="fas fa-shoe-prints"></i> Rodapé
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
                                <button type="button" id="btn_clear_draft" class="btn btn-sm btn-light-warning w-100">
                                    <i class="ki-duotone ki-trash fs-2"></i>
                                    Limpar Rascunho
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
                            <option value="rtf">Microsoft Word (.rtf)</option>
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

<!--begin::Save Template Modal-->
<div class="modal fade" id="save_template_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Salvar como Modelo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="save_template_form">
                    <div class="mb-4">
                        <label class="form-label">Nome do Modelo</label>
                        <input type="text" id="template_name" class="form-control" placeholder="Ex: Ofício Padrão, Lei Municipal, etc." required>
                        <div class="text-muted fs-7 mt-1">Escolha um nome descritivo para identificar este modelo</div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Descrição</label>
                        <textarea id="template_description" class="form-control" rows="3" placeholder="Descrição opcional do modelo e quando utilizá-lo"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Tipo de Proposição</label>
                        <select id="template_tipo" class="form-select" data-control="select2" data-placeholder="Selecione um tipo">
                            <option value="">Modelo Geral</option>
                            <!-- Tipos serão carregados dinamicamente -->
                        </select>
                    </div>
                    <div class="mb-4">
                        <div class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" id="template_include_layout" checked>
                            <label class="form-check-label" for="template_include_layout">
                                Incluir configurações de cabeçalho e rodapé
                            </label>
                        </div>
                    </div>
                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4">
                        <i class="ki-duotone ki-information-5 fs-2tx text-info me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-7 text-gray-700">
                                    O modelo será salvo com todo o conteúdo atual, incluindo variáveis e formatação. 
                                    Poderá ser reutilizado para criar novos documentos.
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btn_confirm_save_template" class="btn btn-success">
                    <span class="indicator-label">
                        <i class="ki-duotone ki-file-added fs-2"></i>
                        Salvar Modelo
                    </span>
                    <span class="indicator-progress">Salvando...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
<!--end::Save Template Modal-->

<!--begin::Import Document Modal-->
<div class="modal fade" id="import_document_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ki-duotone ki-file-up fs-2 text-warning me-2"></i>
                    Importar Documento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!--begin::Upload Area-->
                    <div class="col-md-6">
                        <div class="drop-zone" id="document_drop_zone" 
                             style="border: 2px dashed #e1e5e9; border-radius: 8px; padding: 2rem; text-align: center; transition: all 0.2s ease; background: #f8f9fa;">
                            <i class="ki-duotone ki-file-added fs-3x text-muted mb-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <h6 class="text-gray-700 fw-bold mb-2">Arraste o arquivo aqui</h6>
                            <p class="text-muted fs-7 mb-3">ou clique para selecionar</p>
                            <button type="button" class="btn btn-sm btn-light-primary" onclick="handleFileSelect()">
                                <i class="ki-duotone ki-folder-up fs-2"></i>
                                Selecionar Arquivo
                            </button>
                            <input type="file" id="document_file_input" accept=".docx,.doc,.rtf,.txt,.html" style="display: none;">
                        </div>
                        <div class="text-muted fs-7 mt-3">
                            <strong>Formatos suportados:</strong><br>
                            • Rich Text Format (.rtf)<br>
                            • Texto simples (.txt)<br>
                            • HTML (.html)<br>
                            <div class="text-warning fs-8 mt-2">
                                <i class="ki-duotone ki-information fs-5"></i>
                                Word (.docx/.doc) em desenvolvimento
                            </div>
                        </div>
                    </div>
                    <!--end::Upload Area-->

                    <!--begin::File Info-->
                    <div class="col-md-6">
                        <div id="file_info_panel" style="display: none;">
                            <div class="card card-flush">
                                <div class="card-header">
                                    <h6 class="card-title">Arquivo Selecionado</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="symbol symbol-40px me-3">
                                            <div class="symbol-label bg-light-primary">
                                                <i class="ki-duotone ki-document fs-2 text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold text-gray-900" id="file_name">arquivo.docx</div>
                                            <div class="text-muted fs-7" id="file_size">1.2 MB</div>
                                        </div>
                                    </div>
                                    <div class="separator my-3"></div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Opções de Importação</label>
                                        <div class="form-check form-check-custom form-check-solid mb-2">
                                            <input class="form-check-input" type="checkbox" id="preserve_formatting" checked>
                                            <label class="form-check-label" for="preserve_formatting">
                                                Preservar formatação original
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid mb-2">
                                            <input class="form-check-input" type="checkbox" id="extract_variables" checked>
                                            <label class="form-check-label" for="extract_variables">
                                                Detectar variáveis automaticamente
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" id="replace_current">
                                            <label class="form-check-label" for="replace_current">
                                                Substituir conteúdo atual
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!--begin::Tips-->
                        <div id="import_tips">
                            <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4">
                                <i class="ki-duotone ki-information-5 fs-2tx text-info me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h6 class="text-info fw-bold mb-1">Dicas de Importação</h6>
                                        <div class="fs-7 text-gray-700">
                                            • Documentos Word mantêm melhor formatação<br>
                                            • Use [variavel] para marcar campos dinâmicos<br>
                                            • O sistema detectará variáveis automaticamente
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Tips-->
                    </div>
                    <!--end::File Info-->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btn_confirm_import" class="btn btn-warning" disabled>
                    <span class="indicator-label">
                        <i class="ki-duotone ki-file-up fs-2"></i>
                        Importar Documento
                    </span>
                    <span class="indicator-progress">Importando...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
<!--end::Import Document Modal-->

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
// Import Document System - Fixed variable conflicts v1.1

// Função global para seleção de arquivo
window.handleFileSelect = function() {
    console.log('handleFileSelect chamada');
    
    // Tentar encontrar o input, com retry se necessário
    function tryFindInput(attempts = 0) {
        const fileInput = document.getElementById('document_file_input');
        
        if (fileInput) {
            console.log('Input encontrado, abrindo seleção de arquivo...');
            fileInput.click();
            return;
        }
        
        if (attempts < 5) {
            console.log(`Tentativa ${attempts + 1}: Input não encontrado, tentando novamente...`);
            setTimeout(() => tryFindInput(attempts + 1), 100);
        } else {
            console.error('Input de arquivo não encontrado após 5 tentativas!');
            
            // Debug completo
            const modal = document.getElementById('import_document_modal');
            console.log('Debug do modal:', {
                modalExists: !!modal,
                modalDisplay: modal ? modal.style.display : 'N/A',
                modalHTML: modal ? modal.innerHTML.substring(0, 500) : 'Modal não encontrado'
            });
            
            // Tentar encontrar qualquer input file em qualquer lugar
            const allFileInputs = document.querySelectorAll('input[type="file"]');
            console.log('Todos os inputs file na página:', allFileInputs.length);
            
            if (allFileInputs.length > 0) {
                console.log('Usando primeiro input file encontrado na página');
                allFileInputs[0].click();
            } else {
                console.error('Nenhum input file encontrado em toda a página!');
                
                // Último recurso: criar input dinamicamente
                console.log('Criando input file dinamicamente...');
                const dynamicInput = document.createElement('input');
                dynamicInput.type = 'file';
                dynamicInput.accept = '.rtf,.txt,.html';
                dynamicInput.style.display = 'none';
                dynamicInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        console.log('Arquivo selecionado via input dinâmico:', file.name);
                        handleFileSelection(file);
                    }
                });
                document.body.appendChild(dynamicInput);
                dynamicInput.click();
            }
        }
    }
    
    tryFindInput();
};

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Select2
    $('[data-control="select2"]').select2();


    // Initialize click functionality for variable chips and placeholder editing
    function initializeClickHandlers() {
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
                } else if (variable) {
                    insertVariableAtCursor(variable, value);
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

    // Header and Footer insertion functions
    window.insertHeader = function() {
        const canvas = document.getElementById('document-canvas');
        const selection = window.getSelection();
        
        // Template do cabeçalho oficial
        const headerTemplate = `
        <div class="document-header" style="text-align: center; border-bottom: 2px solid #333; padding-bottom: 1rem; margin-bottom: 2rem; page-break-inside: avoid;">
            <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                <div style="width: 60px; height: 60px; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-size: 10px; color: #999;">
                    LOGO
                </div>
                <div style="text-align: left;">
                    <h2 style="margin: 0; font-size: 16pt; color: #2d3748;">CÂMARA MUNICIPAL DE <span class="variable-placeholder" data-variable="municipio" contenteditable="false">[municipio]</span></h2>
                    <p style="margin: 0; font-size: 12pt; color: #4a5568;">Estado de <span class="variable-placeholder" data-variable="estado" contenteditable="false">[estado]</span></p>
                    <p style="margin: 0; font-size: 10pt; color: #718096;">Rua das Flores, 123 - Centro - CEP: 12345-678</p>
                </div>
            </div>
        </div>`;
        
        if (selection.rangeCount > 0 && canvas.contains(selection.anchorNode)) {
            const range = selection.getRangeAt(0);
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = headerTemplate;
            const headerElement = tempDiv.firstElementChild;
            
            range.insertNode(headerElement);
            
            // Mover cursor para depois do cabeçalho
            range.setStartAfter(headerElement);
            range.setEndAfter(headerElement);
            selection.removeAllRanges();
            selection.addRange(range);
            
            showInsertFeedback('Cabeçalho oficial inserido!');
        }
    };

    window.insertFooter = function() {
        const canvas = document.getElementById('document-canvas');
        const selection = window.getSelection();
        
        // Template do rodapé oficial
        const footerTemplate = `
        <div class="document-footer" style="border-top: 1px solid #ccc; padding-top: 1rem; margin-top: 2rem; text-align: center; font-size: 10pt; color: #718096; page-break-inside: avoid;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="text-align: left;">
                    <p style="margin: 0;">Câmara Municipal de <span class="variable-placeholder" data-variable="municipio" contenteditable="false">[municipio]</span></p>
                    <p style="margin: 0;">Telefone: (11) 1234-5678 | E-mail: contato@camara.gov.br</p>
                </div>
                <div style="text-align: right;">
                    <p style="margin: 0;">www.camara.gov.br</p>
                    <p style="margin: 0; font-size: 9pt;">Página {NUMERO_PAGINA}</p>
                </div>
            </div>
        </div>`;
        
        if (selection.rangeCount > 0 && canvas.contains(selection.anchorNode)) {
            const range = selection.getRangeAt(0);
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = footerTemplate;
            const footerElement = tempDiv.firstElementChild;
            
            range.insertNode(footerElement);
            
            // Mover cursor para depois do rodapé
            range.setStartAfter(footerElement);
            range.setEndAfter(footerElement);
            selection.removeAllRanges();
            selection.addRange(range);
            
            showInsertFeedback('Rodapé oficial inserido!');
        }
    };

    // Feedback function for insertions
    function showInsertFeedback(message) {
        const feedback = document.createElement('div');
        feedback.innerHTML = `<i class="fas fa-check"></i> ${message}`;
        feedback.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            animation: slideInRight 0.3s ease;
        `;
        
        document.body.appendChild(feedback);
        
        setTimeout(() => {
            feedback.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                if (feedback.parentNode) {
                    feedback.parentNode.removeChild(feedback);
                }
            }, 300);
        }, 2500);
    }

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


    // Auto-save functionality for canvas
    let autoSaveTimeout;
    let currentAutoSaveListener;
    
    function setupAutoSave() {
        const canvas = document.getElementById('document-canvas');
        
        // Remove previous listener if it exists
        if (currentAutoSaveListener) {
            canvas.removeEventListener('input', currentAutoSaveListener);
        }
        
        // Create unique key for this session
        const modeloId = document.getElementById('modelo_select').value || 'blank';
        const projetoId = document.getElementById('projeto_select').value || 'none';
        const draftKey = `documento_draft_${modeloId}_${projetoId}`;
        
        // Create new listener
        currentAutoSaveListener = function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                const content = canvas.innerHTML;
                // Only save if there's meaningful content
                if (content.trim().length > 50) {
                    const draftData = {
                        content: content,
                        timestamp: Date.now(),
                        title: document.getElementById('document_title').value,
                        modeloId: modeloId,
                        projetoId: projetoId
                    };
                    localStorage.setItem(draftKey, JSON.stringify(draftData));
                    showAutoSaveFeedback();
                }
            }, 3000); // 3 seconds delay
        };
        
        canvas.addEventListener('input', currentAutoSaveListener);

        // Load draft on page load (only once, not on model/project changes)
        if (!window.draftCheckDone) {
            loadDraftIfExists(draftKey, canvas);
            window.draftCheckDone = true;
        }
    }
    
    function getTimeAgo(milliseconds) {
        const seconds = Math.floor(milliseconds / 1000);
        const minutes = Math.floor(seconds / 60);
        const hours = Math.floor(minutes / 60);
        const days = Math.floor(hours / 24);
        
        if (days > 0) {
            return `há ${days} dia${days > 1 ? 's' : ''}`;
        } else if (hours > 0) {
            return `há ${hours} hora${hours > 1 ? 's' : ''}`;
        } else if (minutes > 0) {
            return `há ${minutes} minuto${minutes > 1 ? 's' : ''}`;
        } else {
            return 'há poucos segundos';
        }
    }

    function loadDraftIfExists(draftKey, canvas) {
        const draftString = localStorage.getItem(draftKey);
        if (!draftString) return;
        
        try {
            const draftData = JSON.parse(draftString);
            const draftAge = Date.now() - draftData.timestamp;
            const maxAge = 24 * 60 * 60 * 1000; // 24 hours in milliseconds
            
            // Only show draft if it's less than 24 hours old and has meaningful content
            if (draftAge < maxAge && draftData.content && draftData.content.trim().length > 100) {
                const draftDate = new Date(draftData.timestamp).toLocaleString('pt-BR');
                const timeAgo = getTimeAgo(draftAge);
                
                Swal.fire({
                    title: '📄 Rascunho Encontrado',
                    html: `
                        <div class="text-start">
                            <div class="mb-3">
                                <i class="fas fa-clock text-warning me-2"></i>
                                <span class="fw-bold">Salvo em:</span> ${draftDate}
                                <small class="text-muted d-block ms-4">${timeAgo}</small>
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-file-alt text-primary me-2"></i>
                                <span class="fw-bold">Título:</span> 
                                <span class="text-primary">${draftData.title || 'Sem título'}</span>
                            </div>
                            <div class="alert alert-info d-flex align-items-center mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <small>Seu rascunho será carregado automaticamente, preservando todo o conteúdo editado anteriormente.</small>
                            </div>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-download me-1"></i> Carregar Rascunho',
                    cancelButtonText: '<i class="fas fa-times me-1"></i> Ignorar',
                    confirmButtonColor: '#009ef7',
                    cancelButtonColor: '#6c757d',
                    customClass: {
                        popup: 'swal-wide',
                        title: 'fw-bold',
                        confirmButton: 'btn-lg',
                        cancelButton: 'btn-lg'
                    },
                    showCloseButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        canvas.innerHTML = draftData.content;
                        if (draftData.title) {
                            document.getElementById('document_title').value = draftData.title;
                        }
                        showDraftLoadedFeedback();
                    } else if (result.isDismissed) {
                        // If user declines, ask if they want to clear the draft
                        Swal.fire({
                            title: '🗑️ Remover Rascunho?',
                            html: `
                                <div class="text-center">
                                    <p class="mb-3">Deseja remover permanentemente este rascunho?</p>
                                    <div class="alert alert-warning d-flex align-items-center">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <small><strong>Atenção:</strong> Esta ação não pode ser desfeita.</small>
                                    </div>
                                </div>
                            `,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: '<i class="fas fa-trash me-1"></i> Sim, Remover',
                            cancelButtonText: '<i class="fas fa-keep me-1"></i> Manter',
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            customClass: {
                                confirmButton: 'btn-lg',
                                cancelButton: 'btn-lg'
                            }
                        }).then((deleteResult) => {
                            if (deleteResult.isConfirmed) {
                                localStorage.removeItem(draftKey);
                                showDraftClearedFeedback();
                            }
                        });
                    }
                });
            } else if (draftAge >= maxAge) {
                // Auto-clean old drafts
                localStorage.removeItem(draftKey);
            }
        } catch (error) {
            console.error('Error loading draft:', error);
            localStorage.removeItem(draftKey); // Remove corrupted draft
        }
    }
    
    function showDraftLoadedFeedback() {
        const feedback = document.createElement('div');
        feedback.innerHTML = '✅ Rascunho carregado com sucesso';
        feedback.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        `;
        
        document.body.appendChild(feedback);
        setTimeout(() => feedback.style.opacity = '1', 10);
        setTimeout(() => {
            feedback.style.opacity = '0';
            setTimeout(() => document.body.removeChild(feedback), 300);
        }, 3000);
    }
    
    function showDraftClearedFeedback() {
        const feedback = document.createElement('div');
        feedback.innerHTML = '🗑️ Rascunho removido';
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
        }, 2000);
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
    initializeClickHandlers();
    setupAutoSave();
    initializeAutoComplete();

    // Additional feedback functions for canvas

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
        
        // Reinitialize auto-save with new context
        setupAutoSave();
    });

    // Project selection change
    document.getElementById('projeto_select').addEventListener('change', function() {
        refreshVariables();
        
        // Reinitialize auto-save with new context
        setupAutoSave();
    });

    // Refresh variables
    document.getElementById('btn_refresh_variables').addEventListener('click', function() {
        refreshVariables();
    });

    // Clear draft button
    document.getElementById('btn_clear_draft').addEventListener('click', function() {
        const modeloId = document.getElementById('modelo_select').value || 'blank';
        const projetoId = document.getElementById('projeto_select').value || 'none';
        const draftKey = `documento_draft_${modeloId}_${projetoId}`;
        
        if (localStorage.getItem(draftKey)) {
            Swal.fire({
                title: '🗑️ Limpar Rascunho',
                html: `
                    <div class="text-center">
                        <p class="mb-3">Tem certeza que deseja remover o rascunho salvo?</p>
                        <div class="alert alert-warning d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <small><strong>Atenção:</strong> Esta ação não pode ser desfeita e todo o conteúdo não salvo será perdido permanentemente.</small>
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-trash-alt me-1"></i> Sim, Limpar',
                cancelButtonText: '<i class="fas fa-times me-1"></i> Cancelar',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                customClass: {
                    popup: 'swal-wide',
                    confirmButton: 'btn-lg',
                    cancelButton: 'btn-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    localStorage.removeItem(draftKey);
                    showDraftClearedFeedback();
                    
                    Swal.fire({
                        title: '✅ Rascunho Removido',
                        text: 'O rascunho foi removido com sucesso.',
                        icon: 'success',
                        confirmButtonColor: '#009ef7',
                        timer: 2000,
                        timerProgressBar: true
                    });
                }
            });
        } else {
            Swal.fire({
                title: '📄 Nenhum Rascunho',
                text: 'Não há rascunho salvo para este documento.',
                icon: 'info',
                confirmButtonColor: '#009ef7',
                confirmButtonText: 'OK'
            });
        }
    });

    // Export button
    document.getElementById('btn_export').addEventListener('click', function() {
        document.getElementById('export_title').value = document.getElementById('document_title').value;
        document.getElementById('export_format').value = document.getElementById('formato_exportacao').value;
        
        const modal = new bootstrap.Modal(document.getElementById('export_modal'));
        modal.show();
    });

    // Import Document button
    document.getElementById('btn_import_document').addEventListener('click', function() {
        resetImportModal();
        const modal = new bootstrap.Modal(document.getElementById('import_document_modal'));
        modal.show();
        
        // Verificar se elementos estão presentes após o modal abrir
        setTimeout(() => {
            const fileInput = document.getElementById('document_file_input');
            const modal = document.getElementById('import_document_modal');
            const allInputs = modal ? modal.querySelectorAll('input') : [];
            const fileInputs = modal ? modal.querySelectorAll('input[type="file"]') : [];
            
            console.log('DEBUG: Verificação completa do modal:', {
                modalExists: !!modal,
                modalVisible: modal ? modal.classList.contains('show') : false,
                fileInputExists: !!fileInput,
                totalInputsInModal: allInputs.length,
                fileInputsInModal: fileInputs.length,
                inputIds: Array.from(allInputs).map(inp => inp.id || 'sem-id'),
                modalContent: modal ? modal.innerHTML.length : 0
            });
            
            // Se o input não existe, talvez o modal não foi renderizado completamente
            if (!fileInput) {
                console.warn('Input não encontrado - modal pode não estar completamente renderizado');
            } else {
                // Re-configurar listener se input existe
                setupFileInputListener();
            }
        }, 500);
    });

    // Reset import modal when closed
    document.getElementById('import_document_modal').addEventListener('hidden.bs.modal', function() {
        resetImportModal();
    });

    function resetImportModal() {
        selectedFile = null;
        
        // Reset elementos com verificação de existência
        const fileInfoPanel = document.getElementById('file_info_panel');
        const importTips = document.getElementById('import_tips');
        const confirmBtn = document.getElementById('btn_confirm_import');
        const fileInput = document.getElementById('document_file_input');
        const dropZone = document.getElementById('document_drop_zone');
        
        if (fileInfoPanel) fileInfoPanel.style.display = 'none';
        if (importTips) importTips.style.display = 'block';
        if (confirmBtn) confirmBtn.disabled = true;
        if (fileInput) fileInput.value = '';
        
        // Reset drop zone
        if (dropZone) {
            dropZone.innerHTML = `
                <i class="ki-duotone ki-file-added fs-3x text-muted mb-3">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <h6 class="text-gray-700 fw-bold mb-2">Arraste o arquivo aqui</h6>
                <p class="text-muted fs-7 mb-3">ou clique no botão abaixo</p>
                <button type="button" class="btn btn-sm btn-light-primary" onclick="handleFileSelect()">
                    <i class="ki-duotone ki-folder-up fs-2"></i>
                    Selecionar Arquivo
                </button>
                <input type="file" id="document_file_input" accept=".rtf,.txt,.html" style="display: none;">
            `;
            dropZone.style.borderColor = '#e1e5e9';
            dropZone.style.backgroundColor = '#f8f9fa';
            
            // Re-configurar event listener do input após recriar
            setTimeout(() => {
                setupFileInputListener();
            }, 100);
        }
    }

    // Save Template button
    document.getElementById('btn_save_template').addEventListener('click', function() {
        // Carregar tipos de proposição
        loadTiposProposicao();
        
        // Sugerir nome baseado no título atual
        const currentTitle = document.getElementById('document_title').value;
        if (currentTitle && currentTitle.trim()) {
            document.getElementById('template_name').value = `Modelo: ${currentTitle.trim()}`;
        }
        
        const modal = new bootstrap.Modal(document.getElementById('save_template_modal'));
        modal.show();
    });

    // Confirm save template
    document.getElementById('btn_confirm_save_template').addEventListener('click', function() {
        const button = this;
        const templateName = document.getElementById('template_name').value;
        const templateDescription = document.getElementById('template_description').value;
        const templateTipo = document.getElementById('template_tipo').value;
        const includeLayout = document.getElementById('template_include_layout').checked;
        const content = document.getElementById('document-canvas').innerHTML;

        if (!templateName.trim()) {
            Swal.fire('Erro', 'Digite um nome para o modelo.', 'error');
            return;
        }

        button.setAttribute('data-kt-indicator', 'on');
        button.disabled = true;

        // Extrair variáveis do conteúdo
        const variables = extractVariablesFromContent(content);

        fetch('{{ route("documentos.modelos.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                nome: templateName,
                descricao: templateDescription,
                tipo_proposicao_id: templateTipo || null,
                conteudo: content,
                variaveis: variables,
                include_layout: includeLayout
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('save_template_modal')).hide();
                
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'Modelo salvo com sucesso.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Limpar formulário
                    document.getElementById('save_template_form').reset();
                });
            } else {
                Swal.fire('Erro', data.message || 'Erro ao salvar modelo.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Erro', 'Erro ao salvar modelo.', 'error');
        })
        .finally(() => {
            button.removeAttribute('data-kt-indicator');
            button.disabled = false;
        });
    });

    // Função para carregar tipos de proposição
    function loadTiposProposicao() {
        // Usar dados já disponíveis ou fazer fetch se necessário
        @if(isset($tiposProposicao) && $tiposProposicao->count() > 0)
            const tiposData = @json($tiposProposicao);
            populateTiposSelect(tiposData);
        @else
            // Se não tiver dados, fazer fetch (implementar rota se necessário)
            console.log('Tipos de proposição não disponíveis');
        @endif
    }

    function populateTiposSelect(tipos) {
        const select = document.getElementById('template_tipo');
        // Limpar opções existentes (exceto a primeira)
        while (select.children.length > 1) {
            select.removeChild(select.lastChild);
        }
        
        // Adicionar tipos
        tipos.forEach(tipo => {
            const option = document.createElement('option');
            option.value = tipo.id;
            option.textContent = tipo.nome;
            select.appendChild(option);
        });
    }

    // Função original como fallback
    function loadTiposProposicaoFetch() {
        fetch('/admin/tipos-proposicao/api')
            .then(response => response.json())
            .then(data => {
                populateTiposSelect(data);
            })
            .catch(error => {
                console.error('Erro ao carregar tipos:', error);
            });
    }

    // Função para extrair variáveis do conteúdo
    function extractVariablesFromContent(content) {
        const variables = {};
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = content;
        
        // Buscar por placeholders de variáveis
        const variablePlaceholders = tempDiv.querySelectorAll('.variable-placeholder');
        variablePlaceholders.forEach(placeholder => {
            const variable = placeholder.getAttribute('data-variable');
            if (variable) {
                variables[variable] = placeholder.textContent.replace(/[\[\]]/g, '') || variable;
            }
        });
        
        // Buscar por padrão ${variavel}
        const variablePattern = /\$\{([^}]+)\}/g;
        let match;
        while ((match = variablePattern.exec(content)) !== null) {
            const variable = match[1];
            if (!variables[variable]) {
                variables[variable] = variable;
            }
        }
        
        return variables;
    }

    // Import Document Functions
    let selectedFile = null;

    // File selection simplificado - usando onclick inline nos botões
    // Botões agora usam handleFileSelect() diretamente via onclick

    // Função para configurar event listener do input file
    function setupFileInputListener() {
        const fileInput = document.getElementById('document_file_input');
        if (fileInput) {
            // Remove listener anterior se existir
            fileInput.removeEventListener('change', handleFileInputChange);
            // Adiciona novo listener
            fileInput.addEventListener('change', handleFileInputChange);
            console.log('Event listener do input file configurado');
        } else {
            console.warn('Input file não encontrado para configurar listener');
        }
    }
    
    // Função para handle do change do input
    function handleFileInputChange(e) {
        console.log('Input file change detectado');
        const file = e.target.files[0];
        if (file) {
            console.log('Arquivo selecionado:', file.name, file.size, 'bytes');
            handleFileSelection(file);
        } else {
            console.log('Nenhum arquivo selecionado');
        }
    }
    
    // Setup inicial
    setupFileInputListener();

    // Drag and drop functionality
    const dropZone = document.getElementById('document_drop_zone');
    
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.style.borderColor = '#007bff';
        dropZone.style.backgroundColor = '#f0f8ff';
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dropZone.style.borderColor = '#e1e5e9';
        dropZone.style.backgroundColor = '#f8f9fa';
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.style.borderColor = '#e1e5e9';
        dropZone.style.backgroundColor = '#f8f9fa';
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFileSelection(files[0]);
        }
    });

    // Removido evento de clique do dropzone para evitar conflito
    // O botão "Selecionar Arquivo" já tem o evento correto

    function handleFileSelection(file) {
        // Validate file type - removidos .docx e .doc temporariamente
        const validTypes = [
            'application/rtf', // .rtf
            'text/plain', // .txt
            'text/html' // .html
        ];
        
        const validExtensions = ['.rtf', '.txt', '.html'];
        const fileNameLower = file.name.toLowerCase();
        const hasValidExtension = validExtensions.some(ext => fileNameLower.endsWith(ext));
        
        if (!validTypes.includes(file.type) && !hasValidExtension) {
            Swal.fire('Erro', 'Formato de arquivo não suportado. Use: .rtf, .txt ou .html (Word em desenvolvimento)', 'error');
            return;
        }

        // Validate file size (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            Swal.fire('Erro', 'O arquivo deve ter no máximo 10MB.', 'error');
            return;
        }

        selectedFile = file;
        
        // Update UI com verificação de existência
        const fileNameElement = document.getElementById('file_name');
        const fileSizeElement = document.getElementById('file_size');
        const fileInfoPanel = document.getElementById('file_info_panel');
        const importTips = document.getElementById('import_tips');
        const confirmBtn = document.getElementById('btn_confirm_import');
        
        if (fileNameElement) fileNameElement.textContent = file.name;
        if (fileSizeElement) fileSizeElement.textContent = formatFileSize(file.size);
        if (fileInfoPanel) fileInfoPanel.style.display = 'block';
        if (importTips) importTips.style.display = 'none';
        if (confirmBtn) confirmBtn.disabled = false;
        
        // Update drop zone
        dropZone.innerHTML = `
            <i class="ki-duotone ki-check fs-3x text-success mb-3">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            <h6 class="text-success fw-bold mb-2">Arquivo Selecionado</h6>
            <p class="text-muted fs-7">${file.name}</p>
        `;
        dropZone.style.borderColor = '#198754';
        dropZone.style.backgroundColor = '#f0fff4';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Confirm import
    document.getElementById('btn_confirm_import').addEventListener('click', function() {
        if (!selectedFile) {
            Swal.fire('Erro', 'Nenhum arquivo selecionado.', 'error');
            return;
        }

        const button = this;
        const preserveFormatting = document.getElementById('preserve_formatting').checked;
        const extractVariables = document.getElementById('extract_variables').checked;
        const replaceCurrent = document.getElementById('replace_current').checked;

        button.setAttribute('data-kt-indicator', 'on');
        button.disabled = true;

        // Read file content
        const reader = new FileReader();
        
        reader.onload = function(e) {
            try {
                let content = e.target.result;
                
                // Para RTF, verificar se o encoding está correto
                if (selectedFile.name.endsWith('.rtf')) {
                    // Se encontrar caracteres estranhos, pode ser problema de encoding
                    if (content.includes('�') || /[\x80-\xFF]/.test(content)) {
                        console.warn('Detectados caracteres com encoding problemático, tentando ISO-8859-1...');
                        
                        // Tentar recarregar com ISO-8859-1
                        const reader2 = new FileReader();
                        reader2.onload = function(e2) {
                            processFileContent(e2.target.result);
                        };
                        reader2.readAsText(selectedFile, 'ISO-8859-1');
                        return;
                    }
                }
                
                processFileContent(content);
                
            } catch (error) {
                console.error('Erro ao processar arquivo:', error);
                Swal.fire('Erro', 'Erro ao processar o arquivo.', 'error');
            }
        };
        
        function processFileContent(content) {
            // Process content based on file type
            content = processImportedContent(content, selectedFile.type, {
                preserveFormatting,
                extractVariables,
                replaceCurrent,
                fileName: selectedFile.name
            });
            
            // Load content into editor
            loadContentIntoEditor(content, replaceCurrent);
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('import_document_modal')).hide();
            
            // Show success message
            Swal.fire({
                title: 'Sucesso!',
                text: 'Documento importado com sucesso.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }
        
        reader.onerror = function() {
            Swal.fire('Erro', 'Erro ao ler o arquivo.', 'error');
        };
        
        reader.onloadend = function() {
            button.removeAttribute('data-kt-indicator');
            button.disabled = false;
        };

        // Read as text for most formats
        if (selectedFile.type.includes('text/') || selectedFile.name.endsWith('.rtf') || selectedFile.name.endsWith('.html')) {
            // Para RTF, tentar diferentes encodings
            if (selectedFile.name.endsWith('.rtf')) {
                console.log('Lendo arquivo RTF com encoding UTF-8...');
                reader.readAsText(selectedFile, 'UTF-8');
            } else {
                reader.readAsText(selectedFile);
            }
        } else if (selectedFile.name.endsWith('.docx') || selectedFile.name.endsWith('.doc')) {
            // For Word documents, show message that server processing is needed
            Swal.fire({
                title: 'Funcionalidade em Desenvolvimento',
                text: 'A importação de documentos Word está em desenvolvimento. Por enquanto, use arquivos .rtf, .txt ou .html.',
                icon: 'info',
                confirmButtonText: 'OK'
            });
            button.removeAttribute('data-kt-indicator');
            button.disabled = false;
            return;
        } else {
            reader.readAsText(selectedFile);
        }
    });

    function processImportedContent(content, fileType, options) {
        let processedContent = content;
        
        // Processar RTF
        if (fileType === 'application/rtf' || options.fileName?.endsWith('.rtf')) {
            console.log('Processando arquivo RTF...');
            console.log('Tamanho original:', content.length, 'caracteres');
            
            // Debug de tabelas encontradas
            const fonttbl = content.match(/{\\fonttbl[\s\S]*?}/g);
            const stylesheet = content.match(/{\\stylesheet[\s\S]*?}/g);
            if (fonttbl) console.log('Tabelas de fonte encontradas:', fonttbl.length);
            if (stylesheet) console.log('Folhas de estilo encontradas:', stylesheet.length);
            
            processedContent = convertRtfToHtml(content);
            console.log('Tamanho após conversão:', processedContent.length, 'caracteres');
            console.log('Conteúdo HTML convertido (primeiros 300 chars):', processedContent.substring(0, 300));
            
            // Verificar se ainda tem lixo RTF
            const rtfLeftovers = processedContent.match(/\\[a-zA-Z]+|[{}]/g);
            if (rtfLeftovers && rtfLeftovers.length > 5) {
                console.warn('Ainda há resíduos RTF no resultado:', rtfLeftovers.slice(0, 10));
            }
        }
        // Clean up content
        else if (fileType === 'text/html') {
            // Remove unnecessary HTML tags
            processedContent = processedContent.replace(/<head[^>]*>[\s\S]*?<\/head>/gi, '');
            processedContent = processedContent.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '');
            processedContent = processedContent.replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '');
        }
        
        if (options.extractVariables) {
            // Convert common patterns to variables
            processedContent = processedContent.replace(/\[([^\]]+)\]/g, '<span class="variable-placeholder" data-variable="$1" contenteditable="false">[$1]</span>');
            processedContent = processedContent.replace(/\{([^}]+)\}/g, '<span class="variable-placeholder" data-variable="$1" contenteditable="false">{$1}</span>');
        }
        
        if (!options.preserveFormatting && fileType === 'text/plain') {
            // Convert plain text to HTML with basic formatting
            processedContent = processedContent.replace(/\n\n/g, '</p><p>');
            processedContent = processedContent.replace(/\n/g, '<br>');
            processedContent = '<p>' + processedContent + '</p>';
        }
        
        return processedContent;
    }

    // Função para converter RTF para HTML com melhor suporte
    function convertRtfToHtml(rtfContent) {
        console.log('Convertendo RTF para HTML...');
        
        let content = rtfContent;
        
        // Primeira passada: extrair conteúdo principal e remover metadados
        content = content.replace(/^{\\rtf1[^{]*{/, '{'); // Remove header RTF inicial
        
        // Remover tabelas complexas (fontes, cores, estilos)
        content = removeNestedRtfTables(content);
        
        // Remover comandos de documento
        content = content.replace(/\\paperw\d+\\paperh\d+/g, ''); // Dimensões papel
        content = content.replace(/\\marg[lrtb]\d+/g, ''); // Margens
        content = content.replace(/\\viewkind\d+/g, ''); // Tipo visualização
        content = content.replace(/\\uc\d+/g, ''); // Unicode skip count
        content = content.replace(/\\deff\d+/g, ''); // Fonte padrão
        content = content.replace(/\\deflang\d+/g, ''); // Idioma padrão
        
        // Remover referências de fonte e formatação complexa
        content = content.replace(/\\f\d+/g, ''); // Referências de fonte
        content = content.replace(/\\fs\d+/g, ''); // Tamanho fonte
        content = content.replace(/\\sl\d+\\slmult\d+/g, ''); // Espaçamento linha
        content = content.replace(/\\cf\d+/g, ''); // Cores
        content = content.replace(/\\cb\d+/g, ''); // Cores de fundo
        content = content.replace(/\\highlight\d+/g, ''); // Destaque
        
        // Remover comandos de página e seção
        content = content.replace(/\\sectd/g, ''); // Seção padrão
        content = content.replace(/\\pard/g, ''); // Parágrafo padrão
        content = content.replace(/\\nowidctlpar/g, ''); // Controle de largura
        content = content.replace(/\\widctlpar/g, ''); // Controle de largura
        
        // Remover imagens e objetos embebidos
        content = content.replace(/{\\pict[^}]*}/g, ''); // Imagens
        content = content.replace(/{\\\*\\shppict[^}]*}/g, ''); // Formas/imagens
        content = content.replace(/\\shapeType\s+\d+[^}]*/g, ''); // Tipos de forma
        content = content.replace(/\\pib\s+[a-fA-F0-9]+/g, ''); // Dados binários imagem
        content = content.replace(/[a-fA-F0-9]{50,}/g, ''); // Sequências hexadecimais longas
        
        // Segunda passada: processar formatação
        const formatStack = [];
        let htmlParts = [];
        let currentText = '';
        
        // Dividir em tokens para processar sequencialmente
        const tokens = content.split(/(\\[a-zA-Z]+\d*|\\par|\{|\}|\[|\])/g)
            .filter(token => {
                // Filtrar tokens que são apenas números ou dados binários
                if (/^\d+$/.test(token.trim())) return false;
                if (/^[0-9a-fA-F]{20,}$/.test(token.trim())) return false;
                return token.trim().length > 0;
            });
        
        
        for (let token of tokens) {
            if (token.startsWith('\\par')) {
                // Novo parágrafo
                if (currentText.trim()) {
                    htmlParts.push(currentText.trim());
                    currentText = '';
                }
                htmlParts.push('</p><p>');
            }
            else if (token === '\\b') {
                // Início negrito
                formatStack.push('bold');
                currentText += '<strong>';
            }
            else if (token === '\\b0') {
                // Fim negrito
                const lastFormat = formatStack.pop();
                if (lastFormat === 'bold') {
                    currentText += '</strong>';
                }
            }
            else if (token === '\\i') {
                // Início itálico
                formatStack.push('italic');
                currentText += '<em>';
            }
            else if (token === '\\i0') {
                // Fim itálico
                const lastFormat = formatStack.pop();
                if (lastFormat === 'italic') {
                    currentText += '</em>';
                }
            }
            else if (token === '\\ul') {
                // Início sublinhado
                formatStack.push('underline');
                currentText += '<u>';
            }
            else if (token === '\\ul0') {
                // Fim sublinhado
                const lastFormat = formatStack.pop();
                if (lastFormat === 'underline') {
                    currentText += '</u>';
                }
            }
            else if (token === '\\qc') {
                // Centralizado
                currentText += '<div style="text-align: center;">';
            }
            else if (token === '\\qr') {
                // Alinhado à direita
                currentText += '<div style="text-align: right;">';
            }
            else if (token === '\\qj') {
                // Justificado
                currentText += '<div style="text-align: justify;">';
            }
            else if (token === '\\ql') {
                // Alinhado à esquerda (padrão)
                currentText += '<div style="text-align: left;">';
            }
            else if (token.startsWith('\\sb') || token.startsWith('\\sa')) {
                // Espaçamento antes/depois - ignorar por agora
                continue;
            }
            else if (token.startsWith('\\')) {
                // Outros comandos RTF - ignorar
                continue;
            }
            else if (token === '{' || token === '}') {
                // Ignorar chaves de agrupamento
                continue;
            }
            else if (token.trim()) {
                // Texto real
                let cleanText = token;
                
                // Primeiro: tratar caracteres especiais RTF comuns
                cleanText = cleanText
                    .replace(/\\-/g, '-') // Hífen
                    .replace(/\\~/g, ' ') // Espaço não-quebrável
                    .replace(/\\\\/, '\\') // Barra invertida literal
                    .replace(/\\{/g, '{') // Chave literal
                    .replace(/\\}/g, '}'); // Chave literal
                
                // Segundo: converter códigos hexadecimais RTF
                cleanText = cleanText.replace(/\\'([0-9a-fA-F]{2})/g, (match, hex) => {
                    const charCode = parseInt(hex, 16);
                    return String.fromCharCode(charCode);
                });
                
                // Terceiro: mapeamento direto de caracteres problemáticos
                cleanText = convertRtfSpecialChars(cleanText);
                
                currentText += cleanText;
            }
        }
        
        // Adicionar texto restante
        if (currentText.trim()) {
            htmlParts.push(currentText.trim());
        }
        
        // Montar HTML final
        let htmlContent = htmlParts.join(' ');
        
        // Limpeza final mais rigorosa
        htmlContent = htmlContent
            .replace(/\s+/g, ' ') // Normalizar espaços
            .replace(/< \//g, '</') // Corrigir tags de fechamento
            .replace(/< /g, '<') // Corrigir tags de abertura
            // Remover resíduos de nomes de fontes de forma mais agressiva
            .replace(/(Times New Roman|Cambria Math|Cambria|Aptos|Arial|Helvetica|Tahoma|Verdana|Georgia|Calibri|Century|Book Antiqua|Franklin Gothic|Lucida|Trebuchet|Impact|Comic Sans)( CE| Cyr| Greek| Tur| Hebrew| Arabic| Baltic| Vietnamese|;|,|\s)*/gi, '')
            // Remover sequências de ponto e vírgula com nomes de fontes
            .replace(/([A-Za-z\s]+;){2,}/g, '')
            // Remover listas de estilos e metadados
            .replace(/\b(Normal|header|footer|heading|index|title|subtitle|Caption|List|TOC|Bibliography|Quote|Intense|Emphasis)[\s\d]*[;\s]*/gi, '')
            // Remover URLs de schema e namespaces
            .replace(/http:\/\/[^\s]*/g, '')
            .replace(/xmlns[^=]*="[^"]*"/g, '')
            // Remover números soltos que podem ser IDs ou códigos
            .replace(/\b\d{8,}\b/g, '')
            // Remover sequências de pontos, vírgulas e parênteses
            .replace(/[.()\s;,]{3,}/g, ' ')
            // Remover linhas que contêm apenas caracteres especiais e espaços
            .replace(/^[^\w\u00C0-\u017F]+$/gm, '')
            .replace(/\n\s*\n/g, '\n') // Remover linhas vazias duplas
            .trim();
        
        // Envolver em parágrafos se necessário
        if (!htmlContent.startsWith('<p>')) {
            htmlContent = '<p>' + htmlContent;
        }
        if (!htmlContent.endsWith('</p>')) {
            htmlContent = htmlContent + '</p>';
        }
        
        // Corrigir parágrafos aninhados
        htmlContent = htmlContent.replace(/<\/p><p>/g, '</p>\n<p>');
        htmlContent = htmlContent.replace(/<p>\s*<\/p>/g, ''); // Remover parágrafos vazios
        
        // Limpeza específica para metadados e estilos que aparecem no final
        htmlContent = htmlContent
            // Remover listas longas de estilos (Bullet, Number, Table, etc.)
            .replace(/\b(Bullet|Number|Continue|Body Text|Note|Outline|Table|Medium|Light|Dark|Colorful|Shading|Grid|Accent)\s*[^;]*;[\s\S]*?(?=\b[A-ZÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ][a-záàâãäåæçèéêëìíîïðñòóôõöøùúûüýþß]|\s*$)/gi, '')
            // Remover fragmentos soltos como "efaultcl", "Vietnamese", etc.
            .replace(/\b(efaultcl|Vietnamese|Display|style="[^"]*")\b/gi, '')
            // Remover sequências de pontos e vírgulas com estilos
            .replace(/([A-Z][a-z]+\s*\d*\s*[A-Z]*[a-z]*\s*\d*\s*;[\s]*){3,}/g, '')
            // Remover números soltos e comandos RTF restantes
            .replace(/\b-?\d{2,}\s+(shapeType|fFlipH|fFlipV|pib|pibFlags|fRecolorFillAsPicture|fUseShapeAnchor|fLine|dhgt|fBehindDocument|fLayoutInCell)\b/g, '')
            // Remover texto que parece ser lista de estilos (mais de 5 palavras com ";" )
            .replace(/([A-Z][^;]*;\s*){5,}/g, '');
        
        // Limpar linhas que são principalmente lixo
        const lines = htmlContent.split('\n');
        const cleanedLines = lines.filter(line => {
            const cleanLine = line.replace(/<[^>]*>/g, '').trim();
            // Manter linhas que têm conteúdo útil em português
            if (cleanLine.length < 10) return false;
            if ((cleanLine.match(/;/g) || []).length > 3) return false; // Muitos pontos e vírgula = lista de estilos
            if (/\b(Table|Bullet|Accent|Medium|Light|Grid)\b/i.test(cleanLine)) return false; // Palavras de estilo
            return true;
        });
        
        htmlContent = cleanedLines.join('\n');
        
        // Verificação final: se ainda há muito lixo, tentar extrair apenas o conteúdo útil
        const cleannessCheck = htmlContent.replace(/<[^>]*>/g, ''); // Remove HTML tags
        const meaningfulContent = cleannessCheck.replace(/[^a-zA-ZÀ-ſ\s]/g, '').trim();
        
        // Só usar extração emergencial se o resultado for realmente muito ruim
        if (meaningfulContent.length < cleannessCheck.length * 0.1 || cleannessCheck.length < 20) {
            console.warn('Muito lixo detectado, tentando extração de conteúdo emergencial...');
            
            // Extração emergencial: procurar por padrões de conteúdo útil
            const emergencyContent = extractMeaningfulContent(content);
            if (emergencyContent.length > 50) {
                console.log('Conteúdo emergencial extraído com sucesso');
                return emergencyContent;
            }
        }
        
        console.log('RTF convertido para HTML:', htmlContent.substring(0, 200) + '...');
        return htmlContent;
    }

    // Função para converter caracteres especiais RTF
    function convertRtfSpecialChars(text) {
        // Mapeamento completo de caracteres especiais RTF
        const rtfCharMap = {
            // Vogais com acento agudo
            'á': 'á', 'Á': 'Á',
            'é': 'é', 'É': 'É', 
            'í': 'í', 'Í': 'Í',
            'ó': 'ó', 'Ó': 'Ó',
            'ú': 'ú', 'Ú': 'Ú',
            
            // Vogais com acento grave
            'à': 'à', 'À': 'À',
            
            // Vogais com acento circunflexo
            'â': 'â', 'Â': 'Â',
            'ê': 'ê', 'Ê': 'Ê',
            'ô': 'ô', 'Ô': 'Ô',
            
            // Vogais com til
            'ã': 'ã', 'Ã': 'Ã',
            'õ': 'õ', 'Õ': 'Õ',
            
            // Cedilha
            'ç': 'ç', 'Ç': 'Ç',
            
            // Trema
            'ü': 'ü', 'Ü': 'Ü',
            
            // Ordinals
            'ª': 'ª', 'º': 'º',
            
            // Outros símbolos
            '§': '§', // Parágrafo
            '°': '°', // Grau
            '²': '²', // Ao quadrado
            '³': '³', // Ao cubo
            '¹': '¹', // Primeiro
            '½': '½', // Um meio
            '¼': '¼', // Um quarto
            '¾': '¾', // Três quartos
        };
        
        // Aplicar conversões diretas
        let convertedText = text;
        for (const [encoded, decoded] of Object.entries(rtfCharMap)) {
            convertedText = convertedText.replace(new RegExp(encoded, 'g'), decoded);
        }
        
        // Conversões específicas para problemas comuns
        convertedText = convertedText
            .replace(/TíTULO/gi, 'TÍTULO')
            .replace(/Câmara/gi, 'Câmara')
            .replace(/proposição/gi, 'proposição')
            .replace(/atribuições/gi, 'atribuições')
            .replace(/disposições/gi, 'disposições')
            .replace(/publicação/gi, 'publicação')
            .replace(/legislação/gi, 'legislação')
            .replace(/orientação/gi, 'orientação')
            .replace(/situação/gi, 'situação')
            .replace(/reunião/gi, 'reunião')
            .replace(/comissão/gi, 'comissão')
            .replace(/sessão/gi, 'sessão')
            .replace(/eleição/gi, 'eleição')
            .replace(/decisão/gi, 'decisão')
            .replace(/versão/gi, 'versão')
            .replace(/informação/gi, 'informação')
            .replace(/administração/gi, 'administração')
            .replace(/execução/gi, 'execução')
            .replace(/elaboração/gi, 'elaboração')
            .replace(/organização/gi, 'organização');
        
        return convertedText;
    }

    // Função para remover tabelas aninhadas complexas do RTF
    function removeNestedRtfTables(content) {
        let cleaned = content;
        
        // Versão mais simples e conservadora - remover apenas blocos específicos
        
        // Remover apenas blocos de fontes claramente identificados
        cleaned = cleaned.replace(/{\\fonttbl[^{}]*(?:{[^{}]*}[^{}]*)*}/g, '');
        cleaned = cleaned.replace(/{\\stylesheet[^{}]*(?:{[^{}]*}[^{}]*)*}/g, '');
        cleaned = cleaned.replace(/{\\colortbl[^;}]*}/g, '');
        
        // Remover apenas listas óbvias de fontes (3+ nomes seguidos)
        cleaned = cleaned.replace(/(Times New Roman|Cambria Math|Arial|Calibri)([^;]*;[^;]*;[^;]*;)/gi, '');
        
        return cleaned;
    }

    // Função de extração emergencial para casos extremos
    function extractMeaningfulContent(rtfContent) {
        console.log('Executando extração emergencial de conteúdo...');
        
        let content = rtfContent;
        
        // Procurar por padrões de conteúdo útil entre \\par
        const paragraphMatches = content.split(/\\par/);
        const meaningfulParagraphs = [];
        
        for (let para of paragraphMatches) {
            // Limpeza básica
            let cleanPara = para
                .replace(/\\[a-zA-Z]+\d*/g, ' ') // Remove comandos RTF
                .replace(/[{}]/g, '') // Remove chaves
                .replace(/\s+/g, ' ') // Normaliza espaços
                .trim();
            
            // Filtrar parágrafos que parecem ser conteúdo real
            if (cleanPara.length > 10 && 
                /[a-zA-ZÀ-ſ]/.test(cleanPara) && // Contém letras
                !/^(Times New Roman|Arial|Cambria|Normal|heading)/i.test(cleanPara) && // Não é metadado
                !cleanPara.match(/^[0-9\s;.-]+$/)) { // Não é só números/pontos
                
                // Aplicar conversão de caracteres especiais
                cleanPara = convertRtfSpecialChars(cleanPara);
                meaningfulParagraphs.push(cleanPara);
            }
        }
        
        if (meaningfulParagraphs.length > 0) {
            return '<p>' + meaningfulParagraphs.join('</p>\n<p>') + '</p>';
        }
        
        // Se não encontrou nada útil, retornar mensagem
        return '<p>Conteúdo do documento não pôde ser extraído completamente. Arquivo RTF muito complexo.</p>';
    }

    function loadContentIntoEditor(content, replaceCurrent) {
        const canvas = document.getElementById('document-canvas');
        
        if (replaceCurrent) {
            canvas.innerHTML = content;
        } else {
            // Append to current content
            canvas.innerHTML += '<div class="separator separator-dashed my-5"></div>' + content;
        }
        
        // Re-initialize variable placeholders and click handlers
        if (typeof initializeClickHandlers === 'function') {
            console.log('Re-inicializando click handlers...');
            initializeClickHandlers();
        } else {
            console.warn('Função initializeClickHandlers não encontrada');
        }
        
        // Trigger auto-save (se disponível)
        setTimeout(() => {
            if (window.autoSaveDocument && typeof window.autoSaveDocument === 'function') {
                console.log('Executando auto-save...');
                window.autoSaveDocument();
            } else {
                console.log('Auto-save não disponível - documento carregado sem auto-save');
            }
        }, 1000);
    }

    // Função removida temporariamente - usará processamento client-side
    // uploadAndProcessDocument será implementada quando a rota estiver disponível

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
 
                     data-variable="${nome}" 
                     data-value="${valor || ''}"
                     title="${valor || 'Valor padrão'}">
                    $${nome}
                </div>
            `;
        });
        
        container.innerHTML = html;
        
        // Re-initialize click handlers for new elements
        initializeClickHandlers();
    }
});
</script>
@endpush