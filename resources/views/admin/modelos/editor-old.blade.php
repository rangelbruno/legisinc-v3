<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Editor de Modelo - {{ $tipos[$tipoSelecionado] ?? 'Novo Modelo' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        
        .container {
            display: flex;
            height: 100vh;
            background: #f5f5f5;
        }
        
        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            height: 60px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 16px;
            color: #333;
            font-weight: 600;
        }
        
        .header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #666;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #28a745;
        }
        
        .status-dot.saving {
            background: #ffc107;
        }
        
        .status-dot.error {
            background: #dc3545;
        }
        
        /* Sidebar */
        .sidebar {
            width: 280px;
            background: white;
            border-right: 1px solid #e9ecef;
            padding: 20px;
            margin-top: 60px;
            height: calc(100vh - 60px);
            overflow-y: auto;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-top: 60px;
            background: #f5f5f5;
        }
        
        /* Toolbar */
        .toolbar {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 8px 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }
        
        .toolbar-group {
            display: flex;
            gap: 2px;
            padding: 0 8px;
            border-right: 1px solid #e9ecef;
        }
        
        .toolbar-group:last-child {
            border-right: none;
        }
        
        .toolbar-btn {
            padding: 6px 8px;
            border: 1px solid transparent;
            background: transparent;
            border-radius: 3px;
            cursor: pointer;
            font-size: 13px;
            color: #333;
            min-width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        
        .toolbar-btn:hover {
            background: #f8f9fa;
            border-color: #dee2e6;
        }
        
        .toolbar-btn.active {
            background: #e7f3ff;
            border-color: #007bff;
            color: #007bff;
        }
        
        .format-select {
            padding: 4px 8px;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            background: white;
            font-size: 13px;
            min-width: 160px;
        }
        
        /* Document Area */
        .document-area {
            flex: 1;
            padding: 40px 60px; /* Increased padding for rulers */
            display: flex;
            justify-content: center;
            overflow-y: auto;
            background: #f5f5f5;
        }
        
        .document-wrapper {
            width: 794px; /* A4 width at 96 DPI */
            margin: 20px auto;
            background: transparent;
            position: relative;
        }
        
        .document-page {
            width: 100%;
            height: 1123px; /* A4 height at 96 DPI */
            margin-bottom: 20px;
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            position: relative;
            page-break-after: always;
        }
        
        .document-page:last-child {
            margin-bottom: 0;
        }
        
        /* Header and Footer */
        .document-header {
            background: #fafafa;
            border-bottom: 1px solid #e9ecef;
            padding: 20px 40px;
            min-height: 80px;
            font-size: 14px;
            outline: none;
            text-align: center;
        }
        
        .document-header:empty::before {
            content: "Clique para adicionar cabeçalho (órgão, brasão, título do documento)...";
            color: #aaa;
            font-style: italic;
        }
        
        .document-footer {
            background: #fafafa;
            border-top: 1px solid #e9ecef;
            padding: 20px 40px;
            min-height: 60px;
            font-size: 12px;
            outline: none;
            margin-top: auto;
            text-align: center;
        }
        
        .document-footer:empty::before {
            content: "Clique para adicionar rodapé (data, assinatura, página)...";
            color: #aaa;
            font-style: italic;
        }
        
        /* Main Editor */
        .document-content {
            flex: 1;
            padding: 40px;
            font-size: 12pt;
            line-height: 1.5;
            outline: none;
            font-family: 'Times New Roman', serif;
            height: 963px; /* 1123 - 80 (header) - 80 (footer) */
            overflow: hidden;
            text-align: justify;
            position: relative;
            /* Force line breaking */
            word-wrap: break-word !important;
            word-break: break-word !important;
            white-space: pre-wrap !important;
            max-width: 714px !important; /* 794 - 80px padding */
            width: 714px !important;
            box-sizing: border-box;
            /* Prevent horizontal scrolling */
            overflow-x: hidden !important;
            overflow-y: hidden !important;
        }
        
        .document-content:empty::before {
            content: "Comece a escrever o conteúdo do modelo aqui...";
            color: #aaa;
            font-style: italic;
        }
        
        /* Estilos Hierárquicos Oficiais */
        .document-content .capitulo {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            margin: 24px 0 16px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .document-content .titulo {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            margin: 20px 0 12px 0;
            text-transform: uppercase;
        }
        
        .document-content .artigo {
            font-size: 12pt;
            margin: 16px 0 8px 0;
            text-indent: 0;
            font-weight: normal;
        }
        
        .document-content .artigo::before {
            content: "Art. " counter(artigo) "º ";
            font-weight: bold;
        }
        
        .document-content .artigo-10plus::before {
            content: "Art. " counter(artigo) ". ";
            font-weight: bold;
        }
        
        .document-content .paragrafo {
            font-size: 12pt;
            margin: 12px 0 8px 20px;
            text-indent: 0;
        }
        
        .document-content .paragrafo::before {
            content: "§ " counter(paragrafo) "º ";
            font-weight: bold;
        }
        
        .document-content .paragrafo-10plus::before {
            content: "§ " counter(paragrafo) ". ";
            font-weight: bold;
        }
        
        .document-content .paragrafo-unico {
            font-size: 12pt;
            margin: 12px 0 8px 20px;
            text-indent: 0;
        }
        
        .document-content .paragrafo-unico::before {
            content: "Parágrafo único. ";
            font-weight: bold;
        }
        
        .document-content .inciso {
            font-size: 12pt;
            margin: 8px 0 4px 40px;
            text-indent: 0;
        }
        
        .document-content .inciso::before {
            content: counter(inciso, upper-roman) " – ";
            font-weight: bold;
        }
        
        .document-content .alinea {
            font-size: 12pt;
            margin: 6px 0 4px 60px;
            text-indent: 0;
        }
        
        .document-content .alinea::before {
            content: counter(alinea, lower-alpha) ") ";
            font-weight: bold;
        }
        
        .document-content .item {
            font-size: 12pt;
            margin: 6px 0 4px 80px;
            text-indent: 0;
        }
        
        .document-content .item::before {
            content: counter(item) ". ";
            font-weight: bold;
        }
        
        /* Contadores automáticos */
        .document-content {
            counter-reset: artigo paragrafo inciso alinea item;
        }
        
        .document-content .artigo {
            counter-increment: artigo;
            counter-reset: paragrafo inciso alinea item;
        }
        
        .document-content .paragrafo {
            counter-increment: paragrafo;
            counter-reset: inciso alinea item;
        }
        
        .document-content .inciso {
            counter-increment: inciso;
            counter-reset: alinea item;
        }
        
        .document-content .alinea {
            counter-increment: alinea;
            counter-reset: item;
        }
        
        .document-content .item {
            counter-increment: item;
        }
        
        /* Estilos para elementos padrão */
        .document-content h1 {
            font-size: 16pt;
            font-weight: bold;
            margin: 24px 0 12px 0;
            text-align: center;
        }
        
        .document-content h2 {
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0 10px 0;
            text-align: center;
        }
        
        .document-content h3 {
            font-size: 12pt;
            font-weight: bold;
            margin: 16px 0 8px 0;
        }
        
        .document-content p {
            margin: 12px 0;
            text-align: justify;
            text-indent: 1.5cm;
            word-wrap: break-word !important;
            word-break: break-word !important;
            white-space: pre-wrap !important;
            max-width: 714px !important;
            width: 100% !important;
            overflow-wrap: break-word !important;
            overflow-x: hidden !important;
            display: block !important;
        }
        
        .document-content ul, .document-content ol {
            margin: 12px 0;
            padding-left: 40px;
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
        }
        
        .document-content li {
            margin: 6px 0;
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
        }
        
        /* Ensure all text content breaks properly */
        .document-content *,
        .document-header *,
        .document-footer * {
            word-wrap: break-word !important;
            word-break: break-word !important;
            white-space: pre-wrap !important;
            overflow-wrap: break-word !important;
            max-width: 714px !important;
            overflow-x: hidden !important;
        }
        
        /* Special handling for divs and spans */
        .document-content div,
        .document-content span {
            word-wrap: break-word !important;
            word-break: break-word !important;
            white-space: pre-wrap !important;
            display: inline-block;
            max-width: 714px !important;
            overflow-x: hidden !important;
        }
        
        /* Force text nodes to break */
        .document-content, 
        .document-content * {
            hyphens: auto !important;
            -webkit-hyphens: auto !important;
            -moz-hyphens: auto !important;
            -ms-hyphens: auto !important;
        }
        
        /* Specific rules for contenteditable elements */
        [contenteditable="true"] {
            word-wrap: break-word !important;
            word-break: break-word !important;
            white-space: pre-wrap !important;
            overflow-wrap: break-word !important;
            max-width: 714px !important;
            overflow-x: hidden !important;
            -webkit-nbsp-mode: space !important;
            -webkit-line-break: after-white-space !important;
        }
        
        /* Force br tags to be visible */
        .document-content br {
            display: block !important;
            margin: 0 !important;
            content: "" !important;
        }
        
        /* Prevent text from extending beyond boundaries */
        .document-content {
            contain: layout style !important;
        }
        
        .document-content blockquote {
            margin: 16px 0;
            padding-left: 20px;
            border-left: 4px solid #ddd;
            color: #666;
            font-style: italic;
        }
        
        .document-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
        }
        
        .document-content table th,
        .document-content table td {
            border: 1px solid #333;
            padding: 8px 12px;
            text-align: left;
        }
        
        .document-content table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        /* Variable Badge */
        .variable-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10pt;
            font-family: monospace;
            display: inline-block;
            margin: 0 1px;
            border: 1px solid #bbdefb;
            user-select: none;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
            font-size: 13px;
        }
        
        .form-control {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }
        
        .btn {
            padding: 6px 12px;
            border: 1px solid transparent;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .btn-primary:hover {
            background: #0056b3;
            border-color: #0056b3;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
            border-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #545b62;
            border-color: #545b62;
        }
        
        .btn-outline {
            background: transparent;
            color: #007bff;
            border-color: #007bff;
        }
        
        .btn-outline:hover {
            background: #007bff;
            color: white;
        }
        
        .btn-sm {
            padding: 4px 8px;
            font-size: 12px;
        }
        
        /* Variables Panel */
        .variables-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        
        .variables-section h5 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }
        
        .variable-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            margin-bottom: 4px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .variable-info {
            flex: 1;
        }
        
        .variable-code {
            color: #007bff;
            font-family: monospace;
            font-weight: 600;
            font-size: 11px;
        }
        
        .variable-desc {
            color: #666;
            font-size: 10px;
            margin-top: 2px;
        }
        
        .variable-btn {
            padding: 4px 8px;
            font-size: 11px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .variable-btn:hover {
            background: #0056b3;
        }
        
        /* Styles Panel */
        .styles-section {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
        }
        
        .style-item {
            display: block;
            width: 100%;
            padding: 8px;
            margin-bottom: 4px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: left;
        }
        
        .style-item:hover {
            background: #e9ecef;
        }
        
        .style-name {
            font-weight: 600;
            color: #333;
        }
        
        .style-preview {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
        }
        
        /* Custom Variables */
        .custom-variables {
            margin-top: 15px;
        }
        
        .variable-input {
            display: flex;
            gap: 4px;
            margin-bottom: 6px;
        }
        
        .variable-input input {
            flex: 1;
            padding: 4px 6px;
            font-size: 11px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        
        .btn-remove {
            background: #dc3545;
            color: white;
            border: none;
            padding: 4px 6px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 11px;
        }
        
        .btn-add-variable {
            background: #28a745;
            color: white;
            border: none;
            padding: 6px;
            border-radius: 4px;
            font-size: 11px;
            cursor: pointer;
            width: 100%;
            margin-top: 8px;
        }
        
        .btn-add-variable:hover {
            background: #1e7e34;
        }
        
        /* Toast */
        .toast {
            position: fixed;
            top: 80px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 4px;
            color: white;
            font-size: 13px;
            z-index: 1001;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s;
            max-width: 300px;
        }
        
        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .toast.success {
            background: #28a745;
        }
        
        .toast.error {
            background: #dc3545;
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .sidebar {
                width: 240px;
            }
            
            .document-wrapper {
                width: 90%;
                max-width: 794px;
            }
        }
        
        /* Image resizing styles */
        .image-wrapper {
            position: relative;
            display: inline-block;
            margin: 5px;
        }
        
        .resizable-image {
            display: block;
            cursor: pointer;
            transition: box-shadow 0.2s ease;
        }
        
        .resizable-image.selected {
            box-shadow: 0 0 0 2px #007bff;
        }
        
        .image-resize-handle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #007bff;
            border: 2px solid white;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
            opacity: 0;
            transition: opacity 0.2s ease;
            z-index: 10;
        }
        
        .image-resize-handle.bottom-right {
            bottom: -5px;
            right: -5px;
            cursor: nw-resize;
        }
        
        .image-resize-handle.bottom-left {
            bottom: -5px;
            left: -5px;
            cursor: ne-resize;
        }
        
        .image-resize-handle.top-right {
            top: -5px;
            right: -5px;
            cursor: ne-resize;
        }
        
        .image-resize-handle.top-left {
            top: -5px;
            left: -5px;
            cursor: nw-resize;
        }
        
        .image-wrapper:hover .image-resize-handle,
        .resizable-image.selected ~ .image-resize-handle {
            opacity: 1;
        }
        
        .image-wrapper.dragging {
            z-index: 1000;
        }
        
        .image-wrapper.dragging .resizable-image {
            opacity: 0.8;
        }
        
        /* Size indicator */
        .image-size-indicator {
            position: absolute;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-family: monospace;
            white-space: nowrap;
            z-index: 1001;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        
        .image-wrapper.dragging .image-size-indicator {
            opacity: 1;
        }
        
        /* Image positioning styles */
        .image-wrapper.behind-text {
            position: absolute;
            z-index: 0;
            opacity: 0.7;
        }
        
        .image-wrapper.behind-text .resizable-image {
            pointer-events: none; /* Allow text selection over image */
        }
        
        .image-wrapper.behind-text.selected {
            opacity: 1;
            z-index: 10; /* Bring to front when selected for editing */
        }
        
        .image-wrapper.behind-text.selected .resizable-image {
            pointer-events: auto; /* Enable editing when selected */
        }
        
        .image-wrapper.in-front-text {
            position: absolute;
            z-index: 10;
        }
        
        .image-wrapper.inline {
            position: relative;
            display: inline-block;
            z-index: 1;
        }
        
        .image-wrapper.float-left {
            float: left;
            margin: 0 15px 10px 0;
            position: relative;
        }
        
        .image-wrapper.float-right {
            float: right;
            margin: 0 0 10px 15px;
            position: relative;
        }
        
        .image-wrapper.center {
            display: block;
            margin: 10px auto;
            text-align: center;
        }
        
        /* Draggable positioning */
        .image-wrapper.draggable {
            cursor: move;
        }
        
        .image-wrapper.positioning {
            outline: 2px dashed #007bff;
            background: rgba(0, 123, 255, 0.1);
        }
        
        /* Image controls toolbar */
        .image-controls {
            position: absolute;
            top: -35px;
            left: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            display: none;
            z-index: 1000;
            white-space: nowrap;
        }
        
        .resizable-image.selected ~ .image-controls {
            display: block;
        }
        
        .image-control-btn {
            background: none;
            border: none;
            padding: 6px 8px;
            cursor: pointer;
            font-size: 12px;
            border-right: 1px solid #eee;
            color: #666;
            transition: all 0.2s;
        }
        
        .image-control-btn:hover {
            background: #f5f5f5;
            color: #333;
        }
        
        .image-control-btn:last-child {
            border-right: none;
        }
        
        .image-control-btn.active {
            background: #007bff;
            color: white;
        }
        
        /* Page break and numbering */
        .page-break {
            height: 2px;
            background: linear-gradient(90deg, #007bff 50%, transparent 50%);
            background-size: 20px 2px;
            margin: 10px 0;
            position: relative;
        }
        
        .page-break::after {
            content: 'Quebra de Página';
            position: absolute;
            right: 0;
            top: -20px;
            background: #007bff;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
        }
        
        .page-number {
            position: absolute;
            bottom: 10px;
            right: 30px;
            font-size: 12px;
            color: #666;
            pointer-events: none;
        }
        
        /* Page overflow indicators */
        .content-overflow {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40px;
            background: linear-gradient(transparent, rgba(255, 193, 7, 0.5));
            pointer-events: none;
            display: none;
            z-index: 1000;
        }
        
        .document-content.overflow .content-overflow {
            display: block;
        }
        
        /* Overflow handling */
        .document-content.overflow {
            overflow: hidden;
            border-bottom: 3px solid #ff9800;
        }
        
        .document-content.overflow::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20px;
            background: linear-gradient(transparent, rgba(255, 152, 0, 0.3));
            pointer-events: none;
        }
        
        .document-page.processing {
            opacity: 0.8;
            border: 2px dashed #007bff;
        }
        
        /* Ruler and page boundaries */
        .rulers-container {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            z-index: 100;
        }
        
        .page-ruler-left {
            position: absolute;
            left: -30px;
            top: 0;
            width: 20px;
            height: 100%;
            background: linear-gradient(to right, #f0f0f0, #e0e0e0);
            border-right: 1px solid #ccc;
            font-size: 9px;
            color: #666;
        }
        
        .page-ruler-top {
            position: absolute;
            top: -30px;
            left: 0;
            width: 100%;
            height: 20px;
            background: linear-gradient(to bottom, #f0f0f0, #e0e0e0);
            border-bottom: 1px solid #ccc;
            font-size: 9px;
            color: #666;
        }
        
        .page-ruler-right {
            position: absolute;
            right: -30px;
            top: 0;
            width: 20px;
            height: 100%;
            background: linear-gradient(to left, #f0f0f0, #e0e0e0);
            border-left: 1px solid #ccc;
            font-size: 9px;
            color: #666;
        }
        
        .ruler-mark {
            position: absolute;
            background: #999;
        }
        
        .ruler-mark.vertical {
            right: 0;
            width: 8px;
            height: 1px;
        }
        
        .ruler-mark.horizontal {
            bottom: 0;
            width: 1px;
            height: 8px;
        }
        
        .ruler-mark.major {
            background: #666;
        }
        
        .ruler-mark.vertical.major {
            width: 12px;
        }
        
        .ruler-mark.horizontal.major {
            height: 12px;
        }
        
        .ruler-number {
            position: absolute;
            font-size: 8px;
            color: #999;
        }
        
        .ruler-number.vertical {
            right: 2px;
            transform: rotate(-90deg);
            transform-origin: right center;
        }
        
        .ruler-number.horizontal {
            bottom: 2px;
            transform-origin: center bottom;
        }
        
        .content-boundary {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 1px dashed rgba(0, 123, 255, 0.3);
            pointer-events: none;
            z-index: 1;
        }
        
        .document-content.show-boundaries .content-boundary {
            display: block;
        }
        
        .overflow-warning {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            height: 20px;
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid rgba(255, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: #d32f2f;
            font-weight: bold;
            z-index: 1000;
        }
        
        .document-content.overflow .overflow-warning {
            display: flex;
        }
        
        .overflow-warning {
            display: none;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: 200px;
                margin-top: 0;
            }
            
            .main-content {
                margin-top: 0;
            }
            
            .document-wrapper {
                width: 100%;
                border-radius: 0;
            }
            
            .document-content {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📄 Editor de Modelo - {{ $tipos[$tipoSelecionado] ?? 'Novo Modelo' }}</h1>
            
            <div class="header-actions">
                <div class="status">
                    <div class="status-dot" id="statusDot"></div>
                    <span id="statusText">Pronto</span>
                </div>
                
                <button class="btn btn-secondary btn-sm" onclick="saveModel()">
                    💾 Salvar
                </button>
                
                <a href="{{ route('modelos.index') }}" class="btn btn-outline btn-sm">
                    📋 Lista
                </a>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="sidebar">
            <form id="modelForm">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Nome do Modelo</label>
                    <input type="text" class="form-control" name="nome" id="nomeModelo" required 
                           placeholder="Ex: Modelo padrão para {{ $tipos[$tipoSelecionado] ?? 'projetos' }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Descrição</label>
                    <textarea class="form-control" name="descricao" rows="2" 
                              placeholder="Descrição opcional"></textarea>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="ativo" value="1" checked>
                        Modelo ativo
                    </label>
                </div>
                
                <input type="hidden" name="tipo_projeto" value="{{ $tipoSelecionado }}">
                <input type="hidden" name="conteudo_modelo" id="conteudoModelo">
                <input type="hidden" name="campos_variaveis" id="camposVariaveis">
                <input type="hidden" name="cabecalho" id="cabecalhoInput">
                <input type="hidden" name="rodape" id="rodapeInput">
            </form>
            
            <div class="styles-section">
                <h5>📋 Estilos Oficiais</h5>
                <button class="style-item" onclick="applyStyle('capitulo')">
                    <div class="style-name">Capítulo</div>
                    <div class="style-preview">CAPÍTULO - CAIXA ALTA</div>
                </button>
                <button class="style-item" onclick="applyStyle('titulo')">
                    <div class="style-name">Título</div>
                    <div class="style-preview">TÍTULO - NEGRITO</div>
                </button>
                <button class="style-item" onclick="applyStyle('artigo')">
                    <div class="style-name">Artigo</div>
                    <div class="style-preview">Art. 1º - Numeração automática</div>
                </button>
                <button class="style-item" onclick="applyStyle('paragrafo')">
                    <div class="style-name">Parágrafo</div>
                    <div class="style-preview">§ 1º - Numeração automática</div>
                </button>
                <button class="style-item" onclick="applyStyle('paragrafo-unico')">
                    <div class="style-name">Parágrafo Único</div>
                    <div class="style-preview">Parágrafo único.</div>
                </button>
                <button class="style-item" onclick="applyStyle('inciso')">
                    <div class="style-name">Inciso</div>
                    <div class="style-preview">I – Romano + travessão</div>
                </button>
                <button class="style-item" onclick="applyStyle('alinea')">
                    <div class="style-name">Alínea</div>
                    <div class="style-preview">a) Letra + parêntese</div>
                </button>
                <button class="style-item" onclick="applyStyle('item')">
                    <div class="style-name">Item</div>
                    <div class="style-preview">1. Número + ponto</div>
                </button>
            </div>
            
            <div class="variables-section">
                <h5>🔧 Variáveis do Sistema</h5>
                <div class="variable-item">
                    <div class="variable-info">
                        <div class="variable-code">@{{DATA_HOJE}}</div>
                        <div class="variable-desc">Data atual</div>
                    </div>
                    <button class="variable-btn" onclick="insertVariable('DATA_HOJE')">+</button>
                </div>
                <div class="variable-item">
                    <div class="variable-info">
                        <div class="variable-code">@{{ANO_ATUAL}}</div>
                        <div class="variable-desc">Ano atual</div>
                    </div>
                    <button class="variable-btn" onclick="insertVariable('ANO_ATUAL')">+</button>
                </div>
                <div class="variable-item">
                    <div class="variable-info">
                        <div class="variable-code">@{{NOME_AUTOR}}</div>
                        <div class="variable-desc">Nome do autor</div>
                    </div>
                    <button class="variable-btn" onclick="insertVariable('NOME_AUTOR')">+</button>
                </div>
                <div class="variable-item">
                    <div class="variable-info">
                        <div class="variable-code">@{{NUMERO_PROJETO}}</div>
                        <div class="variable-desc">Número do projeto</div>
                    </div>
                    <button class="variable-btn" onclick="insertVariable('NUMERO_PROJETO')">+</button>
                </div>
            </div>
            
            <div class="custom-variables">
                <h5>⚙️ Variáveis Personalizadas</h5>
                <div id="customVariables">
                    <div class="variable-input">
                        <input type="text" placeholder="Nome" class="var-name">
                        <input type="text" placeholder="Descrição" class="var-desc">
                        <button type="button" class="btn-remove" onclick="removeVariable(this)">×</button>
                    </div>
                </div>
                <button class="btn-add-variable" onclick="addCustomVariable()">+ Adicionar</button>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Toolbar -->
            <div class="toolbar">
                <div class="toolbar-group">
                    <select class="format-select" onchange="formatBlock(this.value)">
                        <option value="">Estilos Hierárquicos</option>
                        <option value="capitulo">Capítulo</option>
                        <option value="titulo">Título</option>
                        <option value="artigo">Artigo</option>
                        <option value="paragrafo">Parágrafo</option>
                        <option value="paragrafo-unico">Parágrafo Único</option>
                        <option value="inciso">Inciso</option>
                        <option value="alinea">Alínea</option>
                        <option value="item">Item</option>
                        <option value="p">Parágrafo Normal</option>
                    </select>
                </div>
                
                <div class="toolbar-group">
                    <button class="toolbar-btn" onclick="formatText('bold')" title="Negrito (Ctrl+B)">
                        <strong>B</strong>
                    </button>
                    <button class="toolbar-btn" onclick="formatText('italic')" title="Itálico (Ctrl+I)">
                        <em>I</em>
                    </button>
                    <button class="toolbar-btn" onclick="formatText('underline')" title="Sublinhado (Ctrl+U)">
                        <u>U</u>
                    </button>
                </div>
                
                <div class="toolbar-group">
                    <button class="toolbar-btn" onclick="formatText('justifyLeft')" title="Alinhar à esquerda">
                        ⬅
                    </button>
                    <button class="toolbar-btn" onclick="formatText('justifyCenter')" title="Centralizar">
                        ↔
                    </button>
                    <button class="toolbar-btn" onclick="formatText('justifyRight')" title="Alinhar à direita">
                        ➡
                    </button>
                    <button class="toolbar-btn" onclick="formatText('justifyFull')" title="Justificar">
                        ⬌
                    </button>
                </div>
                
                <div class="toolbar-group">
                    <button class="toolbar-btn" onclick="formatText('insertUnorderedList')" title="Lista com marcadores">
                        • Lista
                    </button>
                    <button class="toolbar-btn" onclick="formatText('insertOrderedList')" title="Lista numerada">
                        1. Lista
                    </button>
                </div>
                
                <div class="toolbar-group">
                    <button class="toolbar-btn" onclick="insertTable()" title="Inserir tabela">
                        📊 Tabela
                    </button>
                    <button class="toolbar-btn" onclick="insertImage()" title="Inserir imagem">
                        🖼️ Imagem
                    </button>
                </div>
                
                <div class="toolbar-group">
                    <button class="toolbar-btn" onclick="insertCustomVariable()" title="Inserir Variável">
                        @{{ }} Variável
                    </button>
                </div>
                
                <div class="toolbar-group">
                    <button class="toolbar-btn" onclick="toggleRuler()" title="Mostrar/Ocultar Régua" id="rulerToggle">
                        📏 Régua
                    </button>
                    <button class="toolbar-btn" onclick="toggleBoundaries()" title="Mostrar/Ocultar Limites" id="boundariesToggle">
                        🔲 Limites
                    </button>
                </div>
            </div>
            
            <!-- Document Area -->
            <div class="document-area">
                <div class="document-wrapper" id="documentWrapper">
                    <div class="document-page" data-page="1">
                        <!-- Rulers Container -->
                        <div class="rulers-container" id="rulersContainer">
                            <div class="page-ruler-left" data-ruler="left"></div>
                            <div class="page-ruler-top" data-ruler="top"></div>
                            <div class="page-ruler-right" data-ruler="right"></div>
                        </div>
                        
                        <!-- Header -->
                        <div class="document-header" 
                             contenteditable="true" 
                             id="documentHeader"
                             onblur="updateHiddenInputs()">
                        </div>
                        
                        <!-- Main Content -->
                        <div class="document-content" 
                             contenteditable="true" 
                             id="documentContent"
                             onpaste="handlePaste(event)"
                             onblur="updateHiddenInputs()"
                             oninput="checkContentOverflow()">
                            <div class="content-boundary"></div>
                            <div class="content-overflow"></div>
                            <div class="overflow-warning">⚠️ CONTEÚDO EXCEDE ALTURA DA PÁGINA</div>
                        </div>
                        
                        <!-- Footer -->
                        <div class="document-footer" 
                             contenteditable="true" 
                             id="documentFooter"
                             onblur="updateHiddenInputs()">
                        </div>
                        
                        <!-- Page Number -->
                        <div class="page-number">Página 1</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="toast" class="toast"></div>

    <script>
        let currentEditor = null;
        let customVarCounter = 1;
        
        // Debounce utility function
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        
        // Set current editor focus
        function setCurrentEditor(editor) {
            currentEditor = editor;
        }
        
        // Apply official style
        function applyStyle(styleName) {
            if (!currentEditor) {
                currentEditor = document.getElementById('documentContent');
            }
            
            currentEditor.focus();
            
            // Get current selection
            const selection = window.getSelection();
            if (selection.rangeCount > 0) {
                const range = selection.getRangeAt(0);
                let element;
                
                if (range.collapsed) {
                    // No selection, create new element
                    element = document.createElement('div');
                    element.className = styleName;
                    
                    // Add placeholder text based on style
                    switch(styleName) {
                        case 'capitulo':
                            element.textContent = 'CAPÍTULO I - TÍTULO DO CAPÍTULO';
                            break;
                        case 'titulo':
                            element.textContent = 'TÍTULO DA SEÇÃO';
                            break;
                        case 'artigo':
                            element.textContent = 'Texto do artigo.';
                            break;
                        case 'paragrafo':
                            element.textContent = 'Texto do parágrafo.';
                            break;
                        case 'paragrafo-unico':
                            element.textContent = 'Texto do parágrafo único.';
                            break;
                        case 'inciso':
                            element.textContent = 'texto do inciso;';
                            break;
                        case 'alinea':
                            element.textContent = 'texto da alínea;';
                            break;
                        case 'item':
                            element.textContent = 'texto do item.';
                            break;
                    }
                    
                    range.insertNode(element);
                    
                    // Select the text for editing
                    const newRange = document.createRange();
                    newRange.selectNodeContents(element);
                    selection.removeAllRanges();
                    selection.addRange(newRange);
                    
                } else {
                    // Wrap selection in style
                    const contents = range.extractContents();
                    element = document.createElement('div');
                    element.className = styleName;
                    element.appendChild(contents);
                    range.insertNode(element);
                }
            }
            
            updateHiddenInputs();
        }
        
        // Format text
        function formatText(command) {
            if (currentEditor) {
                currentEditor.focus();
            }
            document.execCommand(command, false, null);
            updateHiddenInputs();
        }
        
        // Format block
        function formatBlock(tag) {
            if (currentEditor) {
                currentEditor.focus();
            }
            if (tag) {
                if (['capitulo', 'titulo', 'artigo', 'paragrafo', 'paragrafo-unico', 'inciso', 'alinea', 'item'].includes(tag)) {
                    applyStyle(tag);
                } else {
                    document.execCommand('formatBlock', false, tag);
                }
            }
            updateHiddenInputs();
        }
        
        // Handle paste to preserve formatting and ensure line breaks
        function handlePaste(event) {
            event.preventDefault();
            
            const clipboardData = event.clipboardData || window.clipboardData;
            const htmlData = clipboardData.getData('text/html');
            const textData = clipboardData.getData('text/plain');
            
            if (htmlData) {
                // Clean HTML to keep only essential formatting
                const cleanHtml = cleanPastedHtml(htmlData);
                document.execCommand('insertHTML', false, cleanHtml);
            } else {
                // Process plain text to ensure proper line wrapping
                const processedText = processTextForLineWrapping(textData);
                document.execCommand('insertHTML', false, processedText);
            }
            
            // Force text wrapping after paste
            setTimeout(() => {
                enforceTextWrapping();
                updateHiddenInputs();
                checkContentOverflow();
            }, 50);
        }
        
        // Process text to ensure proper line wrapping
        function processTextForLineWrapping(text) {
            const maxLineLength = 100; // Approximate characters per line
            const words = text.split(' ');
            let result = '';
            let currentLine = '';
            
            words.forEach(word => {
                const testLine = currentLine + (currentLine ? ' ' : '') + word;
                
                if (testLine.length > maxLineLength && currentLine) {
                    result += currentLine + '<br>';
                    currentLine = word;
                } else {
                    currentLine = testLine;
                }
            });
            
            if (currentLine) {
                result += currentLine;
            }
            
            return result;
        }
        
        // Clean pasted HTML
        function cleanPastedHtml(html) {
            const allowedTags = ['p', 'div', 'br', 'strong', 'b', 'em', 'i', 'u', 'h1', 'h2', 'h3', 'ul', 'ol', 'li', 'table', 'tr', 'td', 'th', 'thead', 'tbody'];
            const allowedAttributes = ['class', 'style'];
            
            // Create a temporary div to parse HTML
            const temp = document.createElement('div');
            temp.innerHTML = html;
            
            // Remove unwanted elements and attributes
            function cleanElement(element) {
                if (element.nodeType === Node.TEXT_NODE) {
                    return element;
                }
                
                if (element.nodeType === Node.ELEMENT_NODE) {
                    const tagName = element.tagName.toLowerCase();
                    
                    if (!allowedTags.includes(tagName)) {
                        // Replace with div or remove
                        const div = document.createElement('div');
                        while (element.firstChild) {
                            div.appendChild(element.firstChild);
                        }
                        return div;
                    }
                    
                    // Clean attributes
                    const attributes = Array.from(element.attributes);
                    attributes.forEach(attr => {
                        if (!allowedAttributes.includes(attr.name)) {
                            element.removeAttribute(attr.name);
                        }
                    });
                    
                    // Clean children
                    const children = Array.from(element.childNodes);
                    children.forEach(child => {
                        const cleaned = cleanElement(child);
                        if (cleaned !== child) {
                            element.replaceChild(cleaned, child);
                        }
                    });
                }
                
                return element;
            }
            
            cleanElement(temp);
            return temp.innerHTML;
        }
        
        // Insert variable
        function insertVariable(varName) {
            if (currentEditor) {
                currentEditor.focus();
            }
            
            const selection = window.getSelection();
            if (selection.rangeCount > 0) {
                const range = selection.getRangeAt(0);
                
                const span = document.createElement('span');
                span.className = 'variable-badge';
                span.textContent = '{{' + varName + '}}';
                span.contentEditable = false;
                
                range.insertNode(span);
                range.setStartAfter(span);
                range.setEndAfter(span);
                selection.removeAllRanges();
                selection.addRange(range);
            }
            
            updateHiddenInputs();
        }
        
        // Insert custom variable with SweetAlert
        function insertCustomVariable() {
            Swal.fire({
                title: 'Inserir Variável Personalizada',
                html: `
                    <div style="text-align: left;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Nome da Variável:</label>
                        <input id="variableName" class="swal2-input" placeholder="Ex: NOME_EMPRESA" style="margin: 0 0 16px 0;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Descrição (opcional):</label>
                        <input id="variableDesc" class="swal2-input" placeholder="Ex: Nome da empresa" style="margin: 0;">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '✅ Inserir',
                cancelButtonText: '❌ Cancelar',
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                focusConfirm: false,
                preConfirm: () => {
                    const name = document.getElementById('variableName').value.trim();
                    const desc = document.getElementById('variableDesc').value.trim();
                    
                    if (!name) {
                        Swal.showValidationMessage('Por favor, digite o nome da variável');
                        return false;
                    }
                    
                    if (!/^[A-Z_][A-Z0-9_]*$/.test(name.toUpperCase())) {
                        Swal.showValidationMessage('Nome deve conter apenas letras, números e underscore');
                        return false;
                    }
                    
                    return { name: name.toUpperCase(), desc: desc };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { name, desc } = result.value;
                    insertVariable(name);
                    
                    // Add to custom variables if description provided
                    if (desc) {
                        addCustomVariableToList(name, desc);
                    }
                    
                    Swal.fire({
                        title: 'Sucesso!',
                        text: `Variável @{{${name}}} inserida com sucesso!`,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }
        
        // Insert table with SweetAlert
        function insertTable() {
            Swal.fire({
                title: 'Inserir Tabela',
                html: `
                    <div style="text-align: left;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Número de Linhas:</label>
                        <input id="tableRows" type="number" class="swal2-input" value="3" min="1" max="20" style="margin: 0 0 16px 0;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Número de Colunas:</label>
                        <input id="tableCols" type="number" class="swal2-input" value="3" min="1" max="10" style="margin: 0 0 16px 0;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Incluir Cabeçalho:</label>
                        <div style="margin: 0;">
                            <label style="font-weight: normal; cursor: pointer;">
                                <input type="checkbox" id="tableHeader" checked style="margin-right: 8px;">
                                Primeira linha como cabeçalho
                            </label>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '✅ Criar Tabela',
                cancelButtonText: '❌ Cancelar',
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                focusConfirm: false,
                preConfirm: () => {
                    const rows = parseInt(document.getElementById('tableRows').value);
                    const cols = parseInt(document.getElementById('tableCols').value);
                    const hasHeader = document.getElementById('tableHeader').checked;
                    
                    if (!rows || rows < 1 || rows > 20) {
                        Swal.showValidationMessage('Número de linhas deve ser entre 1 e 20');
                        return false;
                    }
                    
                    if (!cols || cols < 1 || cols > 10) {
                        Swal.showValidationMessage('Número de colunas deve ser entre 1 e 10');
                        return false;
                    }
                    
                    return { rows, cols, hasHeader };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { rows, cols, hasHeader } = result.value;
                    const tableHtml = createTableHtml(rows, cols, hasHeader);
                    
                    if (currentEditor) {
                        currentEditor.focus();
                    }
                    document.execCommand('insertHTML', false, tableHtml);
                    updateHiddenInputs();
                    
                    Swal.fire({
                        title: 'Sucesso!',
                        text: `Tabela ${rows}x${cols} inserida com sucesso!`,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }
        
        // Insert image with SweetAlert
        function insertImage() {
            Swal.fire({
                title: 'Inserir Imagem',
                html: `
                    <div style="text-align: left;">
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Escolha o tipo de imagem:</label>
                            <div style="display: flex; gap: 10px; margin-bottom: 16px;">
                                <button type="button" id="imageTypeUrl" class="image-type-btn active" onclick="switchImageType('url')" style="flex: 1; padding: 8px 12px; border: 2px solid #007bff; background: #007bff; color: white; border-radius: 4px; cursor: pointer;">
                                    🌐 URL da Web
                                </button>
                                <button type="button" id="imageTypeLocal" class="image-type-btn" onclick="switchImageType('local')" style="flex: 1; padding: 8px 12px; border: 2px solid #dee2e6; background: white; color: #333; border-radius: 4px; cursor: pointer;">
                                    📁 Arquivo Local
                                </button>
                            </div>
                        </div>
                        
                        <div id="urlInputs">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">URL da Imagem:</label>
                            <input id="imageUrl" type="url" class="swal2-input" placeholder="https://exemplo.com/imagem.jpg" style="margin: 0 0 16px 0;">
                        </div>
                        
                        <div id="fileInputs" style="display: none;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Selecionar Arquivo:</label>
                            <input id="imageFile" type="file" accept="image/*" class="swal2-file" style="margin: 0 0 16px 0; width: 100%; padding: 8px; border: 1px solid #d0d7de; border-radius: 4px;">
                            <div style="font-size: 12px; color: #666; margin-bottom: 16px;">
                                Formatos aceitos: JPEG, PNG, JPG, GIF, SVG (máx. 2MB)
                            </div>
                        </div>
                        
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Texto Alternativo:</label>
                        <input id="imageAlt" class="swal2-input" placeholder="Descrição da imagem" style="margin: 0 0 16px 0;">
                        
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Largura inicial (opcional):</label>
                        <input id="imageWidth" type="number" class="swal2-input" placeholder="Ex: 300" min="50" max="800" style="margin: 0 0 16px 0;">
                        
                        <div style="font-size: 12px; color: #666;">
                            💡 <strong>Dica:</strong> Após inserir, você pode redimensionar a imagem diretamente no editor clicando e arrastando as bordas.
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '✅ Inserir Imagem',
                cancelButtonText: '❌ Cancelar',
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                focusConfirm: false,
                preConfirm: () => {
                    const imageType = document.querySelector('.image-type-btn.active').id === 'imageTypeUrl' ? 'url' : 'local';
                    const alt = document.getElementById('imageAlt').value.trim();
                    const width = document.getElementById('imageWidth').value;
                    
                    if (imageType === 'url') {
                        const url = document.getElementById('imageUrl').value.trim();
                        
                        if (!url) {
                            Swal.showValidationMessage('Por favor, digite a URL da imagem');
                            return false;
                        }
                        
                        try {
                            new URL(url);
                        } catch {
                            Swal.showValidationMessage('Por favor, digite uma URL válida');
                            return false;
                        }
                        
                        if (width && (width < 50 || width > 800)) {
                            Swal.showValidationMessage('Largura deve ser entre 50 e 800 pixels');
                            return false;
                        }
                        
                        return { type: 'url', url, alt: alt || 'Imagem', width };
                    } else {
                        const file = document.getElementById('imageFile').files[0];
                        
                        if (!file) {
                            Swal.showValidationMessage('Por favor, selecione um arquivo de imagem');
                            return false;
                        }
                        
                        if (file.size > 2048 * 1024) {
                            Swal.showValidationMessage('Arquivo muito grande. Máximo 2MB.');
                            return false;
                        }
                        
                        if (width && (width < 50 || width > 800)) {
                            Swal.showValidationMessage('Largura deve ser entre 50 e 800 pixels');
                            return false;
                        }
                        
                        return { type: 'local', file, alt: alt || 'Imagem', width };
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value.type === 'url') {
                        insertImageFromUrl(result.value);
                    } else {
                        uploadAndInsertImage(result.value);
                    }
                }
            });
        }
        
        // Switch image type
        function switchImageType(type) {
            const urlBtn = document.getElementById('imageTypeUrl');
            const localBtn = document.getElementById('imageTypeLocal');
            const urlInputs = document.getElementById('urlInputs');
            const fileInputs = document.getElementById('fileInputs');
            
            if (type === 'url') {
                urlBtn.classList.add('active');
                localBtn.classList.remove('active');
                urlBtn.style.background = '#007bff';
                urlBtn.style.color = 'white';
                urlBtn.style.borderColor = '#007bff';
                localBtn.style.background = 'white';
                localBtn.style.color = '#333';
                localBtn.style.borderColor = '#dee2e6';
                urlInputs.style.display = 'block';
                fileInputs.style.display = 'none';
            } else {
                localBtn.classList.add('active');
                urlBtn.classList.remove('active');
                localBtn.style.background = '#007bff';
                localBtn.style.color = 'white';
                localBtn.style.borderColor = '#007bff';
                urlBtn.style.background = 'white';
                urlBtn.style.color = '#333';
                urlBtn.style.borderColor = '#dee2e6';
                fileInputs.style.display = 'block';
                urlInputs.style.display = 'none';
            }
        }
        
        // Insert image from URL
        function insertImageFromUrl(data) {
            const { url, alt, width } = data;
            
            // Create unique ID for the image
            const imageId = 'img_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            
            let imgHtml = `<div class="image-wrapper inline" contenteditable="false" data-position="inline">
                <img id="${imageId}" src="${url}" alt="${alt}" class="resizable-image" style="max-width: 100%; height: auto; cursor: pointer;`;
            
            if (width) {
                imgHtml += ` width: ${width}px;`;
            }
            
            imgHtml += `" />
                <div class="image-resize-handle top-left"></div>
                <div class="image-resize-handle top-right"></div>
                <div class="image-resize-handle bottom-left"></div>
                <div class="image-resize-handle bottom-right"></div>
                <div class="image-size-indicator"></div>
                <div class="image-controls">
                    <button class="image-control-btn" onclick="setImagePosition('${imageId}', 'inline')" title="Na linha">📝</button>
                    <button class="image-control-btn" onclick="setImagePosition('${imageId}', 'behind-text')" title="Atrás do texto">🔙</button>
                    <button class="image-control-btn" onclick="setImagePosition('${imageId}', 'in-front-text')" title="Na frente do texto">🔝</button>
                    <button class="image-control-btn" onclick="setImagePosition('${imageId}', 'float-left')" title="Flutuar à esquerda">⬅️</button>
                    <button class="image-control-btn" onclick="setImagePosition('${imageId}', 'float-right')" title="Flutuar à direita">➡️</button>
                    <button class="image-control-btn" onclick="setImagePosition('${imageId}', 'center')" title="Centralizar">⬆️</button>
                </div>
            </div>`;
            
            if (currentEditor) {
                currentEditor.focus();
            }
            
            // Insert the HTML
            document.execCommand('insertHTML', false, imgHtml);
            updateHiddenInputs();
            
            // Add resize functionality to the new image
            setTimeout(() => {
                const newImage = document.getElementById(imageId);
                if (newImage) {
                    setupImageResize(newImage);
                    selectImage(newImage);
                }
            }, 50);
            
            Swal.fire({
                title: 'Sucesso!',
                text: 'Imagem inserida com sucesso! Clique na imagem para redimensionar.',
                icon: 'success',
                timer: 3000,
                showConfirmButton: false
            });
        }
        
        // Upload and insert local image
        function uploadAndInsertImage(data) {
            const { file, alt, width } = data;
            
            Swal.fire({
                title: 'Enviando imagem...',
                text: 'Por favor, aguarde',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            const formData = new FormData();
            formData.append('image', file);
            
            fetch('{{ route("modelos.upload-image") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    // Create unique ID for the image
                    const imageId = 'img_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                    
                    let imgHtml = `<div class="image-wrapper inline" contenteditable="false" data-position="inline">
                        <img id="${imageId}" src="${data.url}" alt="${alt}" class="resizable-image" style="max-width: 100%; height: auto; cursor: pointer;`;
                    
                    if (width) {
                        imgHtml += ` width: ${width}px;`;
                    }
                    
                    imgHtml += `" />
                        <div class="image-resize-handle top-left"></div>
                        <div class="image-resize-handle top-right"></div>
                        <div class="image-resize-handle bottom-left"></div>
                        <div class="image-resize-handle bottom-right"></div>
                        <div class="image-size-indicator"></div>
                        <div class="image-controls">
                            <button class="image-control-btn" onclick="setImagePosition('${imageId}', 'inline')" title="Na linha">📝</button>
                            <button class="image-control-btn" onclick="setImagePosition('${imageId}', 'behind-text')" title="Atrás do texto">🔙</button>
                            <button class="image-control-btn" onclick="setImagePosition('${imageId}', 'in-front-text')" title="Na frente do texto">🔝</button>
                            <button class="image-control-btn" onclick="setImagePosition('${imageId}', 'float-left')" title="Flutuar à esquerda">⬅️</button>
                            <button class="image-control-btn" onclick="setImagePosition('${imageId}', 'float-right')" title="Flutuar à direita">➡️</button>
                            <button class="image-control-btn" onclick="setImagePosition('${imageId}', 'center')" title="Centralizar">⬆️</button>
                        </div>
                    </div>`;
                    
                    if (currentEditor) {
                        currentEditor.focus();
                    }
                    
                    // Insert the HTML
                    document.execCommand('insertHTML', false, imgHtml);
                    updateHiddenInputs();
                    
                    // Add resize functionality to the new image
                    setTimeout(() => {
                        const newImage = document.getElementById(imageId);
                        if (newImage) {
                            setupImageResize(newImage);
                            selectImage(newImage);
                        }
                    }, 50);
                    
                    Swal.fire({
                        title: 'Sucesso!',
                        text: 'Imagem enviada e inserida com sucesso! Clique na imagem para redimensionar.',
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        title: 'Erro!',
                        text: data.message || 'Erro ao enviar imagem',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                Swal.fire({
                    title: 'Erro!',
                    text: 'Erro de conexão. Tente novamente.',
                    icon: 'error'
                });
                console.error('Erro:', error);
            });
        }
        
        // Helper function to create table HTML
        function createTableHtml(rows, cols, hasHeader) {
            let html = '<table style="border-collapse: collapse; width: 100%; margin: 16px 0; border: 1px solid #ddd;">';
            
            for (let i = 0; i < rows; i++) {
                html += '<tr>';
                const isHeader = hasHeader && i === 0;
                const tag = isHeader ? 'th' : 'td';
                const headerStyle = isHeader ? ' background-color: #f8f9fa; font-weight: 600;' : '';
                
                for (let j = 0; j < cols; j++) {
                    const cellContent = isHeader ? `Cabeçalho ${j + 1}` : `Célula ${i + 1}-${j + 1}`;
                    html += `<${tag} style="border: 1px solid #ddd; padding: 8px 12px;${headerStyle}">${cellContent}</${tag}>`;
                }
                
                html += '</tr>';
            }
            
            html += '</table>';
            return html;
        }
        
        // Helper function to add custom variable to the list
        function addCustomVariableToList(name, desc) {
            const container = document.getElementById('customVariables');
            const inputs = container.querySelectorAll('.variable-input');
            const lastInput = inputs[inputs.length - 1];
            
            // Check if last input is empty, if so use it
            const nameInput = lastInput.querySelector('.var-name');
            const descInput = lastInput.querySelector('.var-desc');
            
            if (!nameInput.value.trim()) {
                nameInput.value = name;
                descInput.value = desc;
            } else {
                // Create new input
                addCustomVariable();
                const newInputs = container.querySelectorAll('.variable-input');
                const newLastInput = newInputs[newInputs.length - 1];
                newLastInput.querySelector('.var-name').value = name;
                newLastInput.querySelector('.var-desc').value = desc;
            }
        }
        
        // Add custom variable
        function addCustomVariable() {
            const container = document.getElementById('customVariables');
            const div = document.createElement('div');
            div.className = 'variable-input';
            div.innerHTML = `
                <input type="text" placeholder="Nome" class="var-name">
                <input type="text" placeholder="Descrição" class="var-desc">
                <button type="button" class="btn-remove" onclick="removeVariable(this)">×</button>
            `;
            container.appendChild(div);
        }
        
        // Remove variable
        function removeVariable(button) {
            const container = document.getElementById('customVariables');
            if (container.children.length > 1) {
                button.parentElement.remove();
            }
        }
        
        // Get custom variables
        function getCustomVariables() {
            const variables = [];
            const inputs = document.querySelectorAll('#customVariables .variable-input');
            
            inputs.forEach(input => {
                const name = input.querySelector('.var-name').value.trim();
                const desc = input.querySelector('.var-desc').value.trim();
                
                if (name && desc) {
                    variables.push({
                        nome: name.toUpperCase(),
                        descricao: desc
                    });
                }
            });
            
            return variables;
        }
        
        // Update hidden inputs
        function updateHiddenInputs() {
            document.getElementById('conteudoModelo').value = document.getElementById('documentContent').innerHTML;
            document.getElementById('cabecalhoInput').value = document.getElementById('documentHeader').innerHTML;
            document.getElementById('rodapeInput').value = document.getElementById('documentFooter').innerHTML;
            document.getElementById('camposVariaveis').value = JSON.stringify(getCustomVariables());
        }
        
        // Update status
        function updateStatus(status) {
            const statusDot = document.getElementById('statusDot');
            const statusText = document.getElementById('statusText');
            
            statusDot.className = 'status-dot';
            
            switch(status) {
                case 'ready':
                    statusText.textContent = 'Pronto';
                    break;
                case 'saving':
                    statusDot.classList.add('saving');
                    statusText.textContent = 'Salvando...';
                    break;
                case 'saved':
                    statusText.textContent = 'Salvo';
                    break;
                case 'error':
                    statusDot.classList.add('error');
                    statusText.textContent = 'Erro';
                    break;
            }
        }
        
        // Show toast
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type} show`;
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
        
        // Save model
        function saveModel() {
            updateHiddenInputs();
            
            const form = document.getElementById('modelForm');
            const formData = new FormData(form);
            
            // Validation
            if (!formData.get('nome')) {
                showToast('Nome do modelo é obrigatório', 'error');
                return;
            }
            
            const content = document.getElementById('documentContent').innerHTML.trim();
            if (!content || content === '<br>' || content === '') {
                showToast('Conteúdo do modelo é obrigatório', 'error');
                return;
            }
            
            updateStatus('saving');
            
            fetch('{{ route("modelos.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateStatus('saved');
                    showToast('Modelo salvo com sucesso!');
                    setTimeout(() => {
                        window.location.href = '{{ route("modelos.index") }}';
                    }, 1500);
                } else {
                    updateStatus('error');
                    showToast('Erro ao salvar: ' + (data.message || 'Erro desconhecido'), 'error');
                }
            })
            .catch(error => {
                updateStatus('error');
                showToast('Erro de conexão. Tente novamente.', 'error');
                console.error('Erro:', error);
            });
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            const documentContent = document.getElementById('documentContent');
            const documentHeader = document.getElementById('documentHeader');
            const documentFooter = document.getElementById('documentFooter');
            
            // Set default editor
            currentEditor = documentContent;
            documentContent.focus();
            
            // Force proper text wrapping with aggressive line breaking
            function enforceTextWrapping() {
                const allContentAreas = document.querySelectorAll('.document-content, .document-header, .document-footer');
                allContentAreas.forEach(area => {
                    // Set container properties
                    area.style.wordWrap = 'break-word';
                    area.style.wordBreak = 'break-word';
                    area.style.whiteSpace = 'pre-wrap';
                    area.style.overflowWrap = 'break-word';
                    area.style.maxWidth = '714px';
                    area.style.width = '714px';
                    area.style.overflowX = 'hidden';
                    area.style.overflowY = 'hidden';
                    
                    // Force line breaks on long text
                    forceLineBreaks(area);
                    
                    // Also apply to all child elements
                    const children = area.querySelectorAll('*');
                    children.forEach(child => {
                        child.style.wordWrap = 'break-word';
                        child.style.wordBreak = 'break-word';
                        child.style.whiteSpace = 'pre-wrap';
                        child.style.overflowWrap = 'break-word';
                        child.style.maxWidth = '714px';
                        child.style.overflowX = 'hidden';
                    });
                });
            }
            
            // Force line breaks by measuring text width
            function forceLineBreaks(element) {
                const maxWidth = 714; // Maximum content width in pixels
                const walker = document.createTreeWalker(
                    element,
                    NodeFilter.SHOW_TEXT,
                    null,
                    false
                );
                
                let textNode;
                const textNodes = [];
                
                // Collect all text nodes
                while (textNode = walker.nextNode()) {
                    textNodes.push(textNode);
                }
                
                // Process each text node
                textNodes.forEach(node => {
                    const parent = node.parentElement;
                    if (!parent) return;
                    
                    const text = node.textContent;
                    if (text.length < 50) return; // Skip short text
                    
                    // Create temporary span to measure text width
                    const tempSpan = document.createElement('span');
                    tempSpan.style.visibility = 'hidden';
                    tempSpan.style.position = 'absolute';
                    tempSpan.style.whiteSpace = 'nowrap';
                    tempSpan.style.fontSize = getComputedStyle(parent).fontSize;
                    tempSpan.style.fontFamily = getComputedStyle(parent).fontFamily;
                    tempSpan.textContent = text;
                    document.body.appendChild(tempSpan);
                    
                    const textWidth = tempSpan.offsetWidth;
                    document.body.removeChild(tempSpan);
                    
                    // If text is wider than container, break it
                    if (textWidth > maxWidth) {
                        const words = text.split(' ');
                        let newText = '';
                        let currentLine = '';
                        
                        words.forEach(word => {
                            const testLine = currentLine + (currentLine ? ' ' : '') + word;
                            
                            // Test if this line would be too wide
                            const testSpan = document.createElement('span');
                            testSpan.style.visibility = 'hidden';
                            testSpan.style.position = 'absolute';
                            testSpan.style.whiteSpace = 'nowrap';
                            testSpan.style.fontSize = getComputedStyle(parent).fontSize;
                            testSpan.style.fontFamily = getComputedStyle(parent).fontFamily;
                            testSpan.textContent = testLine;
                            document.body.appendChild(testSpan);
                            
                            const lineWidth = testSpan.offsetWidth;
                            document.body.removeChild(testSpan);
                            
                            if (lineWidth > maxWidth && currentLine) {
                                newText += currentLine + '\n';
                                currentLine = word;
                            } else {
                                currentLine = testLine;
                            }
                        });
                        
                        if (currentLine) {
                            newText += currentLine;
                        }
                        
                        if (newText !== text) {
                            node.textContent = newText;
                        }
                    }
                });
            }
            
            // Apply text wrapping immediately and on every change
            enforceTextWrapping();
            
            // Set up MutationObserver to catch any DOM changes
            const observer = new MutationObserver(() => {
                enforceTextWrapping();
            });
            
            observer.observe(documentContent, {
                childList: true,
                subtree: true,
                characterData: true
            });
            
            // Track current editor
            documentContent.addEventListener('focus', () => setCurrentEditor(documentContent));
            
            // Add keydown listener for better text control
            documentContent.addEventListener('keydown', (e) => {
                // Handle Enter key to ensure proper line breaks
                if (e.key === 'Enter') {
                    e.preventDefault();
                    
                    // Insert line break and ensure wrapping
                    const selection = window.getSelection();
                    if (selection.rangeCount > 0) {
                        const range = selection.getRangeAt(0);
                        const br = document.createElement('br');
                        range.insertNode(br);
                        
                        // Position cursor after break
                        range.setStartAfter(br);
                        range.setEndAfter(br);
                        selection.removeAllRanges();
                        selection.addRange(range);
                    }
                    
                    setTimeout(() => {
                        enforceTextWrapping();
                        checkContentOverflow();
                    }, 10);
                    return false;
                }
                
                // Handle space key to check for line wrapping
                if (e.key === ' ') {
                    setTimeout(() => {
                        checkAndWrapCurrentLine();
                        checkForAutoLineWrap();
                    }, 10);
                }
                
                // For other printable characters, check if we need to wrap
                if (e.key.length === 1 && !e.ctrlKey && !e.altKey && !e.metaKey) {
                    setTimeout(() => {
                        checkForAutoLineWrap();
                    }, 1);
                }
            });
            
            // Check if we need to automatically wrap the current line
            function checkForAutoLineWrap() {
                const selection = window.getSelection();
                if (selection.rangeCount === 0) return;
                
                const range = selection.getRangeAt(0);
                let currentNode = range.startContainer;
                
                if (currentNode.nodeType === Node.TEXT_NODE) {
                    const parent = currentNode.parentElement;
                    if (parent) {
                        // Create a temporary span to measure the current line width
                        const tempSpan = document.createElement('span');
                        tempSpan.style.visibility = 'hidden';
                        tempSpan.style.position = 'absolute';
                        tempSpan.style.whiteSpace = 'nowrap';
                        tempSpan.style.fontSize = getComputedStyle(parent).fontSize;
                        tempSpan.style.fontFamily = getComputedStyle(parent).fontFamily;
                        
                        // Get text from start of element to current position
                        const textUpToPosition = currentNode.textContent.substring(0, range.startOffset);
                        tempSpan.textContent = textUpToPosition;
                        document.body.appendChild(tempSpan);
                        
                        const textWidth = tempSpan.offsetWidth;
                        document.body.removeChild(tempSpan);
                        
                        // If we're approaching the edge, auto-wrap
                        if (textWidth > 650) { // Leave some margin before 714px
                            // Find the last space before current position
                            const lastSpaceIndex = textUpToPosition.lastIndexOf(' ');
                            if (lastSpaceIndex > 0) {
                                // Split at the last space
                                const beforeSpace = textUpToPosition.substring(0, lastSpaceIndex);
                                const afterSpace = textUpToPosition.substring(lastSpaceIndex + 1) + 
                                                  currentNode.textContent.substring(range.startOffset);
                                
                                // Update the text node
                                currentNode.textContent = beforeSpace;
                                
                                // Create new text node with remaining text
                                const newTextNode = document.createTextNode(afterSpace);
                                const br = document.createElement('br');
                                
                                currentNode.parentNode.insertBefore(br, currentNode.nextSibling);
                                currentNode.parentNode.insertBefore(newTextNode, br.nextSibling);
                                
                                // Update cursor position
                                const newRange = document.createRange();
                                newRange.setStart(newTextNode, afterSpace.length - currentNode.textContent.substring(range.startOffset).length);
                                newRange.setEnd(newTextNode, afterSpace.length - currentNode.textContent.substring(range.startOffset).length);
                                selection.removeAllRanges();
                                selection.addRange(newRange);
                            }
                        }
                    }
                }
            }
            
            // Check and wrap current line if needed
            function checkAndWrapCurrentLine() {
                const selection = window.getSelection();
                if (selection.rangeCount === 0) return;
                
                const range = selection.getRangeAt(0);
                let currentNode = range.startContainer;
                
                // Find the text node we're currently in
                if (currentNode.nodeType !== Node.TEXT_NODE) {
                    currentNode = currentNode.childNodes[range.startOffset] || currentNode.firstChild;
                }
                
                if (currentNode && currentNode.nodeType === Node.TEXT_NODE) {
                    const parent = currentNode.parentElement;
                    if (parent) {
                        // Check if this line exceeds the width
                        const rect = parent.getBoundingClientRect();
                        const maxWidth = 714; // Our content width
                        
                        if (rect.width > maxWidth) {
                            enforceTextWrapping();
                        }
                    }
                }
            }
            
            // Throttle input events to prevent excessive page creation
            let inputTimeout;
            documentContent.addEventListener('input', (e) => {
                // Enforce text wrapping on every input
                enforceTextWrapping();
                
                clearTimeout(inputTimeout);
                
                // Check immediately for paste events (large content)
                if (e.inputType === 'insertFromPaste' || e.inputType === 'insertText') {
                    updateHiddenInputs();
                    checkContentOverflow();
                    makeImagesResizable();
                } else {
                    // For normal typing, use throttling
                    inputTimeout = setTimeout(() => {
                        updateHiddenInputs();
                        checkContentOverflow();
                        makeImagesResizable();
                    }, 150);
                }
            });
            
            documentContent.addEventListener('paste', (e) => {
                console.log('Paste event detected');
                setTimeout(() => {
                    // Enforce text wrapping after paste
                    enforceTextWrapping();
                    makeImagesResizable();
                    checkContentOverflow();
                    
                    // Continue checking for additional pages needed
                    let checkCount = 0;
                    const maxChecks = 10;
                    
                    function continuousCheck() {
                        checkCount++;
                        if (checkCount > maxChecks) {
                            console.log('Stopped continuous checking after', maxChecks, 'attempts');
                            return;
                        }
                        
                        const wrapper = document.getElementById('documentWrapper');
                        const pages = wrapper.querySelectorAll('.document-page');
                        const lastPage = pages[pages.length - 1];
                        const lastContent = lastPage.querySelector('.document-content');
                        
                        if (lastContent && lastContent.scrollHeight > lastContent.clientHeight) {
                            console.log(`Continuous check ${checkCount}: Still overflowing, creating another page`);
                            checkContentOverflow();
                            setTimeout(continuousCheck, 200);
                        } else {
                            console.log(`Continuous check ${checkCount}: No more overflow, stopping`);
                        }
                    }
                    
                    // Start continuous checking after paste
                    setTimeout(continuousCheck, 300);
                }, 100);
            });
            
            documentContent.addEventListener('dblclick', (e) => {
                const behindImages = documentContent.querySelectorAll('.image-wrapper.behind-text .resizable-image');
                behindImages.forEach(img => {
                    const rect = img.getBoundingClientRect();
                    if (e.clientX >= rect.left && e.clientX <= rect.right &&
                        e.clientY >= rect.top && e.clientY <= rect.bottom) {
                        e.preventDefault();
                        selectImage(img);
                    }
                });
            });
            
            // Header and footer sync events
            documentHeader.addEventListener('focus', () => setCurrentEditor(documentHeader));
            documentHeader.addEventListener('input', () => {
                updateHiddenInputs();
                synchronizeHeadersFooters();
            });
            
            documentFooter.addEventListener('focus', () => setCurrentEditor(documentFooter));
            documentFooter.addEventListener('input', () => {
                updateHiddenInputs();
                synchronizeHeadersFooters();
            });
            
            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey || e.metaKey) {
                    switch(e.key) {
                        case 's':
                            e.preventDefault();
                            saveModel();
                            break;
                        case 'b':
                            e.preventDefault();
                            formatText('bold');
                            break;
                        case 'i':
                            e.preventDefault();
                            formatText('italic');
                            break;
                        case 'u':
                            e.preventDefault();
                            formatText('underline');
                            break;
                    }
                }
                
                // Delete selected image with Delete key
                if (e.key === 'Delete' || e.key === 'Backspace') {
                    const selectedImage = document.querySelector('.resizable-image.selected');
                    if (selectedImage) {
                        e.preventDefault();
                        const wrapper = selectedImage.closest('.image-wrapper') || selectedImage.parentElement;
                        wrapper.remove();
                        updateHiddenInputs();
                    }
                }
            });
            
            // Initialize image resizing
            makeImagesResizable();
            
            // Initialize page ruler
            initializePageRuler();
            
            // Show boundaries for debugging
            documentContent.classList.add('show-boundaries');
            
            // Set button states
            document.getElementById('rulerToggle').classList.add('active');
            document.getElementById('boundariesToggle').classList.add('active');
        });
        
        // Setup image resize functionality for a specific image
        function setupImageResize(img) {
            if (img.dataset.resizable) return; // Already setup
            img.dataset.resizable = 'true';
            
            const wrapper = img.closest('.image-wrapper');
            if (!wrapper) return;
            
            // Click to select
            img.addEventListener('click', function(e) {
                e.stopPropagation();
                selectImage(img);
            });
            
            // Add resize functionality to handles
            wrapper.querySelectorAll('.image-resize-handle').forEach(handle => {
                handle.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    startResize(e, img, handle.classList[1]);
                });
            });
        }
        
        // Make all existing images resizable
        function makeImagesResizable() {
            const images = document.querySelectorAll('.resizable-image');
            
            images.forEach(img => {
                setupImageResize(img);
            });
            
            // Global click handler to deselect
            if (!document.body.dataset.imageClickHandler) {
                document.body.dataset.imageClickHandler = 'true';
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.image-wrapper')) {
                        deselectAllImages();
                    }
                });
            }
        }
        
        // Select image
        function selectImage(img) {
            deselectAllImages();
            img.classList.add('selected');
            
            const wrapper = img.closest('.image-wrapper');
            if (wrapper) {
                wrapper.classList.add('selected');
            }
        }
        
        // Deselect all images
        function deselectAllImages() {
            document.querySelectorAll('.resizable-image.selected').forEach(img => {
                img.classList.remove('selected');
            });
            document.querySelectorAll('.image-wrapper.selected').forEach(wrapper => {
                wrapper.classList.remove('selected');
            });
        }
        
        // Start resize
        function startResize(e, img, direction) {
            e.preventDefault();
            e.stopPropagation();
            
            const wrapper = img.closest('.image-wrapper');
            const sizeIndicator = wrapper.querySelector('.image-size-indicator');
            
            wrapper.classList.add('dragging');
            
            const startX = e.clientX;
            const startY = e.clientY;
            const startWidth = parseInt(window.getComputedStyle(img).width, 10) || img.naturalWidth;
            const startHeight = parseInt(window.getComputedStyle(img).height, 10) || img.naturalHeight;
            const aspectRatio = startWidth / startHeight;
            
            // Prevent text selection during resize
            document.body.style.userSelect = 'none';
            document.body.style.webkitUserSelect = 'none';
            
            function doResize(e) {
                e.preventDefault();
                
                const deltaX = e.clientX - startX;
                const deltaY = e.clientY - startY;
                
                let newWidth;
                
                switch(direction) {
                    case 'bottom-right':
                        newWidth = startWidth + deltaX;
                        break;
                    case 'bottom-left':
                        newWidth = startWidth - deltaX;
                        break;
                    case 'top-right':
                        newWidth = startWidth + deltaX;
                        break;
                    case 'top-left':
                        newWidth = startWidth - deltaX;
                        break;
                }
                
                // Set minimum and maximum sizes
                newWidth = Math.max(50, Math.min(800, newWidth));
                
                // Maintain aspect ratio
                const newHeight = newWidth / aspectRatio;
                
                // Apply new dimensions
                img.style.width = newWidth + 'px';
                img.style.height = newHeight + 'px';
                
                // Remove any max-width constraints during resize
                img.style.maxWidth = 'none';
                
                // Update size indicator
                if (sizeIndicator) {
                    sizeIndicator.textContent = `${Math.round(newWidth)} × ${Math.round(newHeight)}px`;
                }
            }
            
            function stopResize(e) {
                e.preventDefault();
                
                // Remove event listeners
                document.removeEventListener('mousemove', doResize);
                document.removeEventListener('mouseup', stopResize);
                
                // Restore text selection
                document.body.style.userSelect = '';
                document.body.style.webkitUserSelect = '';
                
                // Remove dragging class
                wrapper.classList.remove('dragging');
                
                // Restore max-width for responsiveness
                img.style.maxWidth = '100%';
                
                // Update hidden inputs
                updateHiddenInputs();
                
                // Show feedback
                const width = parseInt(img.style.width, 10);
                console.log(`Imagem redimensionada para ${width}px`);
            }
            
            // Add event listeners
            document.addEventListener('mousemove', doResize, { passive: false });
            document.addEventListener('mouseup', stopResize, { passive: false });
        }
        
        // Set image position
        function setImagePosition(imageId, position) {
            const img = document.getElementById(imageId);
            if (!img) return;
            
            const wrapper = img.closest('.image-wrapper');
            if (!wrapper) return;
            
            // Remove all position classes
            wrapper.classList.remove('inline', 'behind-text', 'in-front-text', 'float-left', 'float-right', 'center', 'draggable');
            
            // Add new position class
            wrapper.classList.add(position);
            wrapper.dataset.position = position;
            
            // Update button states
            const controls = wrapper.querySelectorAll('.image-control-btn');
            controls.forEach(btn => btn.classList.remove('active'));
            
            const activeBtn = wrapper.querySelector(`[onclick*="'${position}'"]`);
            if (activeBtn) activeBtn.classList.add('active');
            
            // Enable dragging for positioned elements
            if (position === 'behind-text' || position === 'in-front-text') {
                wrapper.classList.add('draggable');
                enableImageDragging(wrapper);
            } else {
                disableImageDragging(wrapper);
            }
            
            updateHiddenInputs();
            
            // Show feedback
            const positionNames = {
                'inline': 'Na linha',
                'behind-text': 'Atrás do texto',
                'in-front-text': 'Na frente do texto',
                'float-left': 'Flutuando à esquerda',
                'float-right': 'Flutuando à direita',
                'center': 'Centralizada'
            };
            
            console.log(`Imagem posicionada: ${positionNames[position]}`);
        }
        
        // Enable image dragging
        function enableImageDragging(wrapper) {
            const img = wrapper.querySelector('.resizable-image');
            if (!img || wrapper.dataset.draggable) return;
            
            wrapper.dataset.draggable = 'true';
            
            let isDragging = false;
            let startX, startY, startLeft, startTop;
            
            // Mouse down on image to start dragging
            function onMouseDown(e) {
                if (e.target.classList.contains('image-resize-handle') || 
                    e.target.classList.contains('image-control-btn')) {
                    return; // Don't drag when interacting with controls
                }
                
                e.preventDefault();
                e.stopPropagation();
                
                isDragging = true;
                wrapper.classList.add('positioning');
                
                startX = e.clientX;
                startY = e.clientY;
                
                const rect = wrapper.getBoundingClientRect();
                const editorRect = currentEditor.getBoundingClientRect();
                
                startLeft = rect.left - editorRect.left;
                startTop = rect.top - editorRect.top;
                
                // Prevent text selection
                document.body.style.userSelect = 'none';
                
                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            }
            
            function onMouseMove(e) {
                if (!isDragging) return;
                
                e.preventDefault();
                
                const deltaX = e.clientX - startX;
                const deltaY = e.clientY - startY;
                
                const newLeft = startLeft + deltaX;
                const newTop = startTop + deltaY;
                
                // Constrain to editor bounds
                const editorRect = currentEditor.getBoundingClientRect();
                const wrapperRect = wrapper.getBoundingClientRect();
                
                const maxLeft = editorRect.width - wrapperRect.width;
                const maxTop = editorRect.height - wrapperRect.height;
                
                const constrainedLeft = Math.max(0, Math.min(maxLeft, newLeft));
                const constrainedTop = Math.max(0, Math.min(maxTop, newTop));
                
                wrapper.style.left = constrainedLeft + 'px';
                wrapper.style.top = constrainedTop + 'px';
            }
            
            function onMouseUp(e) {
                if (!isDragging) return;
                
                isDragging = false;
                wrapper.classList.remove('positioning');
                
                // Restore text selection
                document.body.style.userSelect = '';
                
                document.removeEventListener('mousemove', onMouseMove);
                document.removeEventListener('mouseup', onMouseUp);
                
                updateHiddenInputs();
            }
            
            img.addEventListener('mousedown', onMouseDown);
        }
        
        // Disable image dragging
        function disableImageDragging(wrapper) {
            wrapper.dataset.draggable = '';
            wrapper.style.left = '';
            wrapper.style.top = '';
            wrapper.classList.remove('positioning');
            
            // Remove event listeners - we'll need to recreate the image element
            // to fully remove all listeners, but for now just disable functionality
        }
        
        // Update setup function to handle positioning
        function setupImageResize(img) {
            if (img.dataset.resizable) return; // Already setup
            img.dataset.resizable = 'true';
            
            const wrapper = img.closest('.image-wrapper');
            if (!wrapper) return;
            
            // Set initial position button state
            const position = wrapper.dataset.position || 'inline';
            const activeBtn = wrapper.querySelector(`[onclick*="'${position}'"]`);
            if (activeBtn) activeBtn.classList.add('active');
            
            // Enable dragging if positioned
            if (position === 'behind-text' || position === 'in-front-text') {
                enableImageDragging(wrapper);
            }
            
            // Click to select
            img.addEventListener('click', function(e) {
                e.stopPropagation();
                selectImage(img);
            });
            
            // Add resize functionality to handles
            wrapper.querySelectorAll('.image-resize-handle').forEach(handle => {
                handle.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    startResize(e, img, handle.classList[1]);
                });
            });
        }
        
        // Enhanced content overflow check with better line breaking
        function checkContentOverflow() {
            const wrapper = document.getElementById('documentWrapper');
            const pages = wrapper.querySelectorAll('.document-page');
            
            pages.forEach((page, pageIndex) => {
                const content = page.querySelector('.document-content');
                if (!content) return;
                
                // Force text wrapping and layout recalculation
                content.style.wordWrap = 'break-word';
                content.style.wordBreak = 'break-word';
                content.style.whiteSpace = 'normal';
                content.style.overflow = 'hidden';
                content.offsetHeight; // Force reflow
                
                // Get accurate measurements
                const contentHeight = content.scrollHeight;
                const containerHeight = content.clientHeight;
                const tolerance = 5; // Small tolerance for rounding
                
                console.log(`Page ${pageIndex + 1}: Content ${contentHeight}px, Container ${containerHeight}px`);
                
                // Check for overflow
                if (contentHeight > containerHeight + tolerance) {
                    console.log(`Page ${pageIndex + 1}: Content overflow detected`);
                    content.classList.add('overflow');
                    
                    // Only create new page if this is the last page and not already processing
                    if (pageIndex === pages.length - 1 && !content.dataset.creatingPage) {
                        content.dataset.creatingPage = 'true';
                        
                        setTimeout(() => {
                            const newPageNumber = pages.length + 1;
                            
                            if (newPageNumber <= 50) { // Reduced limit
                                console.log(`Creating page ${newPageNumber}`);
                                createNewPageIfNeeded();
                            } else {
                                console.warn('Page limit reached');
                                // Force scroll hidden to prevent overflow
                                content.style.overflow = 'hidden';
                            }
                            
                            content.dataset.creatingPage = '';
                        }, 100); // Increased timeout for stability
                    }
                } else {
                    content.classList.remove('overflow');
                }
            });
            
            // Clean up empty pages
            setTimeout(() => {
                cleanupEmptyPages();
            }, 300);
        }
        
        // Create new page when content overflows
        function createNewPageIfNeeded() {
            const wrapper = document.getElementById('documentWrapper');
            const pages = wrapper.querySelectorAll('.document-page');
            const lastPage = pages[pages.length - 1];
            const lastContent = lastPage.querySelector('.document-content');
            
            // Double check if content really overflows
            if (lastContent.scrollHeight <= lastContent.clientHeight) {
                lastContent.classList.remove('overflow');
                lastPage.classList.remove('processing');
                return;
            }
            
            // Prevent creating too many pages
            if (pages.length >= 50) {
                console.warn('Limite de páginas atingido para evitar loop infinito');
                return;
            }
            
            console.log(`Creating page ${pages.length + 1} (content: ${lastContent.scrollHeight}px, container: ${lastContent.clientHeight}px)`);
            
            // Add processing indicator
            lastPage.classList.add('processing');
            
            // Create new page
            const newPageNumber = pages.length + 1;
            const newPage = createNewPage(newPageNumber);
            wrapper.appendChild(newPage);
            
            // Move overflow content to new page
            moveOverflowContentImproved(lastContent, newPage.querySelector('.document-content'));
            
            // Remove processing indicator
            lastPage.classList.remove('processing');
            
            // Update page references
            updatePageReferences();
            
            // Check if the new page also needs to be split
            setTimeout(() => {
                const newContent = newPage.querySelector('.document-content');
                if (newContent && newContent.scrollHeight > newContent.clientHeight) {
                    console.log('New page also overflows, creating another page');
                    checkContentOverflow();
                }
            }, 50);
        }
        
        // Create a new document page
        function createNewPage(pageNumber) {
            const headerContent = document.getElementById('documentHeader').innerHTML;
            const footerContent = document.getElementById('documentFooter').innerHTML;
            
            const pageHtml = `
                <div class="document-page" data-page="${pageNumber}">
                    <!-- Rulers Container -->
                    <div class="rulers-container">
                        <div class="page-ruler-left" data-ruler="left"></div>
                        <div class="page-ruler-top" data-ruler="top"></div>
                        <div class="page-ruler-right" data-ruler="right"></div>
                    </div>
                    
                    <!-- Header -->
                    <div class="document-header" 
                         contenteditable="true" 
                         onblur="updateHiddenInputs()">
                        ${headerContent}
                    </div>
                    
                    <!-- Main Content -->
                    <div class="document-content show-boundaries" 
                         contenteditable="true" 
                         onpaste="handlePaste(event)"
                         onblur="updateHiddenInputs()"
                         oninput="checkContentOverflow()">
                        <div class="content-boundary"></div>
                        <div class="content-overflow"></div>
                        <div class="overflow-warning">⚠️ CONTEÚDO EXCEDE ALTURA DA PÁGINA</div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="document-footer" 
                         contenteditable="true" 
                         onblur="updateHiddenInputs()">
                        ${footerContent}
                    </div>
                    
                    <!-- Page Number -->
                    <div class="page-number">Página ${pageNumber}</div>
                </div>
            `;
            
            const pageElement = document.createElement('div');
            pageElement.innerHTML = pageHtml;
            const newPage = pageElement.firstElementChild;
            
            // Initialize rulers for new page
            setTimeout(() => {
                const rulersContainer = newPage.querySelector('.rulers-container');
                if (rulersContainer) {
                    createAllRulers(rulersContainer);
                    
                    // Apply current ruler visibility setting
                    const rulerToggle = document.getElementById('rulerToggle');
                    if (!rulerToggle.classList.contains('active')) {
                        rulersContainer.style.display = 'none';
                    }
                }
            }, 10);
            
            // Set up event listeners for proper text wrapping
            const newContent = newPage.querySelector('.document-content');
            if (newContent) {
                // Ensure proper text wrapping on new page immediately
                newContent.style.wordWrap = 'break-word';
                newContent.style.wordBreak = 'break-word';
                newContent.style.whiteSpace = 'normal';
                newContent.style.overflowWrap = 'break-word';
                newContent.style.overflow = 'hidden';
                newContent.style.maxWidth = '100%';
                
                // Apply boundary setting
                const boundariesToggle = document.getElementById('boundariesToggle');
                if (!boundariesToggle || !boundariesToggle.classList.contains('active')) {
                    newContent.classList.remove('show-boundaries');
                }
                
                newContent.addEventListener('focus', () => setCurrentEditor(newContent));
                newContent.addEventListener('input', debounce(() => {
                    // Enforce text wrapping on every input
                    newContent.style.wordWrap = 'break-word';
                    newContent.style.wordBreak = 'break-word';
                    newContent.style.whiteSpace = 'normal';
                    newContent.style.overflow = 'hidden';
                    
                    // Apply to all child elements
                    const children = newContent.querySelectorAll('*');
                    children.forEach(child => {
                        child.style.wordWrap = 'break-word';
                        child.style.wordBreak = 'break-word';
                        child.style.whiteSpace = 'normal';
                        child.style.maxWidth = '100%';
                    });
                    
                    updateHiddenInputs();
                    checkContentOverflow();
                    makeImagesResizable();
                }, 150));
            }
            
            return newPage;
        }
        
        // Move overflow content to new page with improved algorithm
        function moveOverflowContentImproved(sourceContent, targetContent) {
            console.log('Moving overflow content to new page');
            
            // Get all child elements
            const allElements = Array.from(sourceContent.children);
            if (allElements.length === 0) {
                console.log('No elements to move');
                return;
            }
            
            // Find the breakpoint where content starts overflowing
            const maxHeight = sourceContent.clientHeight;
            const keepElements = [];
            const moveElements = [];
            
            // Create a test container to measure content incrementally
            const testContainer = sourceContent.cloneNode(false);
            testContainer.style.position = 'absolute';
            testContainer.style.top = '-9999px';
            testContainer.style.left = '-9999px';
            testContainer.style.visibility = 'hidden';
            document.body.appendChild(testContainer);
            
            // Add elements one by one until we find the overflow point
            for (let i = 0; i < allElements.length; i++) {
                const element = allElements[i].cloneNode(true);
                testContainer.appendChild(element);
                
                if (testContainer.scrollHeight <= maxHeight) {
                    keepElements.push(allElements[i]);
                } else {
                    // This element causes overflow, move it and all remaining elements
                    moveElements.push(...allElements.slice(i));
                    break;
                }
            }
            
            // Clean up test container
            document.body.removeChild(testContainer);
            
            console.log(`Keeping ${keepElements.length} elements, moving ${moveElements.length} elements`);
            
            // If we have elements to move
            if (moveElements.length > 0) {
                // Clear target content
                targetContent.innerHTML = '';
                
                // Move elements to new page
                moveElements.forEach(el => {
                    targetContent.appendChild(el);
                });
                
                // Update source content (keep only fitting elements)
                sourceContent.innerHTML = '';
                keepElements.forEach(el => {
                    sourceContent.appendChild(el);
                });
            } else {
                // If no elements to move, try to split the last element if it's text
                const lastElement = allElements[allElements.length - 1];
                if (lastElement && lastElement.textContent.length > 100) {
                    splitTextElement(lastElement, sourceContent, targetContent, maxHeight);
                }
            }
            
            sourceContent.classList.remove('overflow');
        }
        
        // Split text element when it's too large for the page
        function splitTextElement(element, sourceContent, targetContent, maxHeight) {
            const text = element.textContent;
            const words = text.split(' ');
            
            if (words.length <= 1) return;
            
            // Create test container
            const testContainer = sourceContent.cloneNode(false);
            testContainer.style.position = 'absolute';
            testContainer.style.top = '-9999px';
            testContainer.style.left = '-9999px';
            testContainer.style.visibility = 'hidden';
            document.body.appendChild(testContainer);
            
            // Copy all elements except the last one
            const allElements = Array.from(sourceContent.children);
            allElements.slice(0, -1).forEach(el => {
                testContainer.appendChild(el.cloneNode(true));
            });
            
            // Find how many words fit
            const testElement = element.cloneNode(true);
            let fitWords = [];
            let remainingWords = [];
            
            for (let i = 0; i < words.length; i++) {
                fitWords.push(words[i]);
                testElement.textContent = fitWords.join(' ');
                testContainer.appendChild(testElement);
                
                if (testContainer.scrollHeight > maxHeight && fitWords.length > 1) {
                    // Remove the last word that caused overflow
                    fitWords.pop();
                    remainingWords = words.slice(fitWords.length);
                    break;
                }
                
                testContainer.removeChild(testElement);
            }
            
            document.body.removeChild(testContainer);
            
            if (remainingWords.length > 0) {
                // Update original element with fitting text
                element.textContent = fitWords.join(' ');
                
                // Create new element with remaining text
                const newElement = element.cloneNode(true);
                newElement.textContent = remainingWords.join(' ');
                targetContent.appendChild(newElement);
                
                console.log(`Split text: kept ${fitWords.length} words, moved ${remainingWords.length} words`);
            }
        }
        
        // Update page references and setup events
        function updatePageReferences() {
            const pages = document.querySelectorAll('.document-page');
            
            pages.forEach((page, index) => {
                const pageNumber = index + 1;
                page.dataset.page = pageNumber;
                
                const pageNumberEl = page.querySelector('.page-number');
                if (pageNumberEl) {
                    pageNumberEl.textContent = `Página ${pageNumber}`;
                }
                
                // Setup content editors
                const content = page.querySelector('.document-content');
                const header = page.querySelector('.document-header');
                const footer = page.querySelector('.document-footer');
                
                [content, header, footer].forEach(editor => {
                    if (editor && !editor.dataset.setupComplete) {
                        editor.addEventListener('focus', () => setCurrentEditor(editor));
                        
                        // Throttle input events for dynamically created pages
                        let editorInputTimeout;
                        editor.addEventListener('input', () => {
                            clearTimeout(editorInputTimeout);
                            editorInputTimeout = setTimeout(() => {
                                updateHiddenInputs();
                                if (editor.classList.contains('document-content')) {
                                    checkContentOverflow();
                                }
                                makeImagesResizable();
                            }, 300);
                        });
                        
                        editor.addEventListener('paste', (e) => {
                            setTimeout(() => {
                                makeImagesResizable();
                                if (editor.classList.contains('document-content')) {
                                    checkContentOverflow();
                                }
                            }, 100);
                        });
                        
                        editor.dataset.setupComplete = 'true';
                    }
                });
            });
            
            // Update hidden inputs to include all content
            updateHiddenInputs();
            
            // Clean up empty pages
            cleanupEmptyPages();
        }
        
        // Remove empty pages (except the first one)
        function cleanupEmptyPages() {
            const wrapper = document.getElementById('documentWrapper');
            const pages = wrapper.querySelectorAll('.document-page');
            
            // Keep at least one page and don't remove pages that have content
            for (let i = pages.length - 1; i > 0; i--) {
                const page = pages[i];
                const content = page.querySelector('.document-content');
                
                if (content && isContentEmpty(content)) {
                    page.remove();
                }
            }
            
            // Renumber remaining pages
            const remainingPages = wrapper.querySelectorAll('.document-page');
            remainingPages.forEach((page, index) => {
                const pageNumber = index + 1;
                page.dataset.page = pageNumber;
                
                const pageNumberEl = page.querySelector('.page-number');
                if (pageNumberEl) {
                    pageNumberEl.textContent = `Página ${pageNumber}`;
                }
            });
        }
        
        // Check if content is effectively empty
        function isContentEmpty(content) {
            const text = content.textContent.trim();
            const hasImages = content.querySelectorAll('img').length > 0;
            const hasTables = content.querySelectorAll('table').length > 0;
            const hasSignificantElements = content.querySelectorAll('h1, h2, h3, h4, h5, h6, ul, ol, blockquote').length > 0;
            
            return !text && !hasImages && !hasTables && !hasSignificantElements;
        }
        
        // Initialize page rulers
        function initializePageRuler() {
            const pages = document.querySelectorAll('.document-page');
            pages.forEach(page => {
                const rulersContainer = page.querySelector('.rulers-container');
                if (rulersContainer) {
                    createAllRulers(rulersContainer);
                }
            });
        }
        
        // Create all rulers (left, top, right)
        function createAllRulers(container) {
            const leftRuler = container.querySelector('[data-ruler="left"]');
            const topRuler = container.querySelector('[data-ruler="top"]');
            const rightRuler = container.querySelector('[data-ruler="right"]');
            
            if (leftRuler) createVerticalRuler(leftRuler);
            if (topRuler) createHorizontalRuler(topRuler);
            if (rightRuler) createVerticalRuler(rightRuler);
        }
        
        // Create vertical ruler marks (left and right)
        function createVerticalRuler(ruler) {
            ruler.innerHTML = '';
            const pageHeight = 1123; // A4 height in pixels
            const markInterval = 25; // Mark every 25px
            const isLeft = ruler.dataset.ruler === 'left';
            
            for (let i = 0; i <= pageHeight; i += markInterval) {
                const mark = document.createElement('div');
                mark.className = 'ruler-mark vertical';
                mark.style.top = i + 'px';
                
                // Major marks every 100px
                if (i % 100 === 0) {
                    mark.classList.add('major');
                    
                    // Add number only on left ruler to avoid clutter
                    if (isLeft && i > 0) {
                        const number = document.createElement('div');
                        number.className = 'ruler-number vertical';
                        number.textContent = i;
                        number.style.top = (i - 8) + 'px';
                        ruler.appendChild(number);
                    }
                } else if (i % 50 === 0) {
                    // Medium marks every 50px
                    mark.style.width = '10px';
                }
                
                ruler.appendChild(mark);
            }
        }
        
        // Create horizontal ruler marks (top)
        function createHorizontalRuler(ruler) {
            ruler.innerHTML = '';
            const pageWidth = 794; // A4 width in pixels
            const markInterval = 25; // Mark every 25px
            
            for (let i = 0; i <= pageWidth; i += markInterval) {
                const mark = document.createElement('div');
                mark.className = 'ruler-mark horizontal';
                mark.style.left = i + 'px';
                
                // Major marks every 100px
                if (i % 100 === 0) {
                    mark.classList.add('major');
                    
                    // Add number
                    if (i > 0) {
                        const number = document.createElement('div');
                        number.className = 'ruler-number horizontal';
                        number.textContent = i;
                        number.style.left = (i - 8) + 'px';
                        ruler.appendChild(number);
                    }
                } else if (i % 50 === 0) {
                    // Medium marks every 50px
                    mark.style.height = '10px';
                }
                
                ruler.appendChild(mark);
            }
        }
        
        // Toggle ruler visibility
        function toggleRuler() {
            const rulersContainers = document.querySelectorAll('.rulers-container');
            const button = document.getElementById('rulerToggle');
            
            rulersContainers.forEach(container => {
                if (container.style.display === 'none') {
                    container.style.display = 'block';
                    button.classList.add('active');
                } else {
                    container.style.display = 'none';
                    button.classList.remove('active');
                }
            });
        }
        
        // Toggle boundaries visibility
        function toggleBoundaries() {
            const contents = document.querySelectorAll('.document-content');
            const button = document.getElementById('boundariesToggle');
            
            contents.forEach(content => {
                if (content.classList.contains('show-boundaries')) {
                    content.classList.remove('show-boundaries');
                    button.classList.remove('active');
                } else {
                    content.classList.add('show-boundaries');
                    button.classList.add('active');
                }
            });
        }
        
        // Enhanced update hidden inputs to handle multiple pages
        function updateHiddenInputs() {
            const pages = document.querySelectorAll('.document-page');
            let allContent = '';
            let allHeaders = '';
            let allFooters = '';
            
            pages.forEach((page, index) => {
                const content = page.querySelector('.document-content');
                const header = page.querySelector('.document-header');
                const footer = page.querySelector('.document-footer');
                
                if (content) {
                    allContent += content.innerHTML;
                    if (index < pages.length - 1) {
                        allContent += '<div class="page-break"></div>';
                    }
                }
                
                if (header && index === 0) { // Only use first header as template
                    allHeaders = header.innerHTML;
                }
                
                if (footer && index === 0) { // Only use first footer as template
                    allFooters = footer.innerHTML;
                }
            });
            
            const contentInput = document.getElementById('conteudoModelo');
            const headerInput = document.getElementById('cabecalhoInput');
            const footerInput = document.getElementById('rodapeInput');
            const variablesInput = document.getElementById('camposVariaveis');
            
            if (contentInput) contentInput.value = allContent;
            if (headerInput) headerInput.value = allHeaders;
            if (footerInput) footerInput.value = allFooters;
            if (variablesInput) variablesInput.value = JSON.stringify(getCustomVariables());
        }
        
        // Synchronize headers and footers across pages
        function synchronizeHeadersFooters() {
            const firstPage = document.querySelector('.document-page[data-page="1"]');
            if (!firstPage) return;
            
            const masterHeader = firstPage.querySelector('.document-header');
            const masterFooter = firstPage.querySelector('.document-footer');
            
            const pages = document.querySelectorAll('.document-page:not([data-page="1"])');
            
            pages.forEach(page => {
                const header = page.querySelector('.document-header');
                const footer = page.querySelector('.document-footer');
                
                if (header && masterHeader) {
                    header.innerHTML = masterHeader.innerHTML;
                }
                
                if (footer && masterFooter) {
                    footer.innerHTML = masterFooter.innerHTML;
                }
            });
        }
    </script>
</body>
</html>