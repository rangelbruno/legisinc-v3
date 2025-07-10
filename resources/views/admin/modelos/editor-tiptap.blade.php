<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Editor de Modelo - {{ $tipos[$tipoSelecionado] ?? 'Novo Modelo' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        
        .word-editor-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            background: #f5f5f5;
        }
        
        /* Header */
        .header {
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
        
        /* Main Layout */
        .main-layout {
            display: flex;
            flex: 1;
            overflow: hidden;
        }
        
        /* Sidebar */
        .sidebar {
            width: 320px;
            background: white;
            border-right: 1px solid #e9ecef;
            padding: 20px;
            height: calc(100vh - 60px);
            overflow-y: auto;
        }
        
        .sidebar h3 {
            margin: 0 0 15px 0;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            padding-bottom: 8px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 12px;
            font-weight: 500;
            color: #555;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            transition: border-color 0.2s;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #007bff;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 60px;
        }
        
        .template-actions {
            display: flex;
            gap: 8px;
            margin-top: 15px;
        }
        
        .template-btn {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 4px;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }
        
        .template-btn:hover {
            background: #f8f9fa;
            border-color: #007bff;
        }
        
        .template-btn.primary {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .template-btn.primary:hover {
            background: #0056b3;
        }
        
        .variable-list {
            margin-top: 15px;
        }
        
        .variable-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 8px;
            background: #f8f9fa;
            border-radius: 4px;
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .variable-item code {
            background: #e9ecef;
            padding: 2px 4px;
            border-radius: 2px;
            font-family: monospace;
        }
        
        .variable-item button {
            margin-left: auto;
            padding: 2px 6px;
            border: none;
            background: #007bff;
            color: white;
            border-radius: 2px;
            cursor: pointer;
            font-size: 10px;
        }
        
        /* Google Docs Style Editor */
        .word-editor-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        /* Toolbar estilo Google Docs */
        .word-toolbar {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            min-height: 48px;
        }
        
        .toolbar-group {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 0 8px;
            border-right: 1px solid #dee2e6;
        }
        
        .toolbar-group:last-child {
            border-right: none;
        }
        
        .toolbar-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: 1px solid transparent;
            background: transparent;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            color: #444;
            transition: all 0.2s;
        }
        
        .toolbar-btn:hover {
            background: #e9ecef;
            border-color: #dee2e6;
        }
        
        .toolbar-btn.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .toolbar-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .font-family-select,
        .font-size-select {
            padding: 4px 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: white;
            font-size: 13px;
            min-width: 120px;
        }
        
        .font-size-select {
            min-width: 60px;
        }
        
        /* Régua Visual */
        .word-ruler {
            height: 24px;
            background: linear-gradient(to bottom, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
            position: relative;
            background-image: repeating-linear-gradient(
                to right,
                transparent,
                transparent 9px,
                #ccc 9px,
                #ccc 10px
            );
        }
        
        .ruler-margin-left,
        .ruler-margin-right {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #007bff;
        }
        
        .ruler-margin-left {
            left: 72px;
        }
        
        .ruler-margin-right {
            right: 72px;
        }
        
        /* Container Principal do Editor */
        .word-like-editor {
            flex: 1;
            background: #f5f5f5;
            padding: 20px;
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        /* Páginas A4 */
        .page-container {
            max-width: 850px;
            margin: 0 auto;
            padding: 0;
        }
        
        .page {
            width: 794px;
            height: 1123px;
            background: white;
            margin: 0 auto 20px;
            padding: 96px 72px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #d0d0d0;
            transition: box-shadow 0.2s;
            position: relative;
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #333;
            overflow: hidden;
        }
        
        .page:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }
        
        .page-content {
            height: 100%;
            overflow: hidden;
            outline: none;
        }
        
        .page-content:focus {
            outline: none;
        }
        
        /* Numeração de páginas */
        .page-number {
            position: absolute;
            bottom: 48px;
            right: 72px;
            font-size: 10pt;
            color: #666;
            font-family: 'Times New Roman', serif;
        }
        
        /* Barra de Status */
        .word-status-bar {
            height: 32px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 0 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: #495057;
        }
        
        .status-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .status-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .zoom-controls {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .zoom-btn {
            width: 24px;
            height: 24px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 2px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        
        .zoom-btn:hover {
            background: #f8f9fa;
        }
        
        .zoom-level {
            font-size: 12px;
            color: #666;
            min-width: 40px;
            text-align: center;
        }
        
        /* Botões de ação */
        .btn {
            padding: 8px 16px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            background: #f8f9fa;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
            border-color: #28a745;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
            border-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        /* Responsividade */
        @media (max-width: 1024px) {
            .page {
                width: 90%;
                max-width: 794px;
                transform: scale(0.9);
                transform-origin: top center;
            }
            
            .page-container {
                max-width: 100%;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: fixed;
                top: 60px;
                left: -100%;
                z-index: 999;
                transition: left 0.3s;
            }
            
            .sidebar.open {
                left: 0;
            }
            
            .word-editor-main {
                width: 100%;
            }
            
            .page {
                width: 95%;
                transform: scale(0.8);
            }
            
            .word-toolbar {
                flex-wrap: wrap;
                min-height: auto;
            }
            
            .toolbar-group {
                border-right: none;
                border-bottom: 1px solid #dee2e6;
                padding: 4px 0;
                width: 100%;
            }
        }
        
        /* Estilos específicos para documentos jurídicos */
        .legal-document {
            margin: 0;
            padding: 0;
        }
        
        .legal-document h1 {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
            font-family: 'Times New Roman', serif;
        }
        
        .legal-document h2 {
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            font-family: 'Times New Roman', serif;
        }
        
        .legal-document p {
            margin-bottom: 12px;
            text-align: justify;
            font-family: 'Times New Roman', serif;
        }
        
        /* Placeholder para variáveis */
        .variable-placeholder {
            background: #fff3cd;
            border: 1px dashed #ffc107;
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: 500;
            color: #856404;
        }
        
        .variable-placeholder:hover {
            background: #ffeaa7;
            cursor: pointer;
        }
        
        /* Zoom levels */
        .zoom-50 .page { transform: scale(0.5); margin-bottom: 10px; }
        .zoom-75 .page { transform: scale(0.75); margin-bottom: 15px; }
        .zoom-100 .page { transform: scale(1); margin-bottom: 20px; }
        .zoom-125 .page { transform: scale(1.25); margin-bottom: 25px; }
        .zoom-150 .page { transform: scale(1.5); margin-bottom: 30px; }
        .zoom-200 .page { transform: scale(2); margin-bottom: 40px; }
        
        /* Performance optimizations */
        .page {
            contain: layout style paint;
            will-change: transform;
            transform: translateZ(0);
        }
        
        /* Menu toggle para mobile */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 8px;
        }
        
        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="word-editor-container">
        <!-- Header -->
        <div class="header">
            <div style="display: flex; align-items: center; gap: 10px;">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Editor de Modelo - {{ $tipos[$tipoSelecionado] ?? 'Novo Modelo' }}</h1>
            </div>
            <div class="header-actions">
                <div class="status">
                    <div class="status-dot" id="status-dot"></div>
                    <span id="status-text">Carregando...</span>
                </div>
                <button class="btn btn-success" onclick="saveModel()">
                    💾 Salvar Modelo
                </button>
                <a href="{{ route('modelos.index') }}" class="btn btn-secondary">
                    ← Voltar
                </a>
            </div>
        </div>
        
        <div class="main-layout">
            <!-- Sidebar -->
            <div class="sidebar" id="sidebar">
                <h3>📝 Configurações do Modelo</h3>
                
                <div class="form-group">
                    <label for="modelo-nome">Nome do Modelo</label>
                    <input type="text" id="modelo-nome" placeholder="Ex: Contrato de Prestação de Serviços" value="{{ old('nome', optional($modelo)->nome) }}">
                </div>
                
                <div class="form-group">
                    <label for="modelo-tipo">Tipo do Modelo</label>
                    <select id="modelo-tipo">
                        @foreach($tipos as $key => $tipo)
                            <option value="{{ $key }}" {{ $tipoSelecionado == $key ? 'selected' : '' }}>
                                {{ $tipo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="modelo-descricao">Descrição</label>
                    <textarea id="modelo-descricao" placeholder="Descrição do modelo..." rows="3">{{ old('descricao', optional($modelo)->descricao) }}</textarea>
                </div>
                
                <div class="template-actions">
                    <button class="template-btn primary" onclick="insertQuickTemplate()">
                        📋 Template Rápido
                    </button>
                    <button class="template-btn" onclick="insertVariable()">
                        🔧 Variável
                    </button>
                </div>
                
                <h3>🔧 Variáveis Disponíveis</h3>
                <div class="variable-list">
                    <div class="variable-item">
                        <code>@{{nome}}</code>
                        <span>Nome da parte</span>
                        <button onclick="insertVariableAtCursor('nome')">+</button>
                    </div>
                    <div class="variable-item">
                        <code>@{{cpf}}</code>
                        <span>CPF</span>
                        <button onclick="insertVariableAtCursor('cpf')">+</button>
                    </div>
                    <div class="variable-item">
                        <code>@{{cnpj}}</code>
                        <span>CNPJ</span>
                        <button onclick="insertVariableAtCursor('cnpj')">+</button>
                    </div>
                    <div class="variable-item">
                        <code>@{{endereco}}</code>
                        <span>Endereço</span>
                        <button onclick="insertVariableAtCursor('endereco')">+</button>
                    </div>
                    <div class="variable-item">
                        <code>@{{data}}</code>
                        <span>Data atual</span>
                        <button onclick="insertVariableAtCursor('data')">+</button>
                    </div>
                    <div class="variable-item">
                        <code>@{{valor}}</code>
                        <span>Valor em reais</span>
                        <button onclick="insertVariableAtCursor('valor')">+</button>
                    </div>
                    <div class="variable-item">
                        <code>@{{cidade}}</code>
                        <span>Cidade</span>
                        <button onclick="insertVariableAtCursor('cidade')">+</button>
                    </div>
                </div>
                
                <h3>📊 Estatísticas</h3>
                <div class="form-group">
                    <small>
                        <strong>Palavras:</strong> <span id="word-count">0</span><br>
                        <strong>Caracteres:</strong> <span id="char-count">0</span><br>
                        <strong>Variáveis:</strong> <span id="var-count">0</span>
                    </small>
                </div>
            </div>
            
            <!-- Editor Principal -->
            <div class="word-editor-main">
                <!-- Toolbar Google Docs Style -->
                <div class="word-toolbar">
                    <div class="toolbar-group">
                        <button class="toolbar-btn" data-action="undo" title="Desfazer (Ctrl+Z)">
                            <i class="fas fa-undo"></i>
                        </button>
                        <button class="toolbar-btn" data-action="redo" title="Refazer (Ctrl+Y)">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                    
                    <div class="toolbar-group">
                        <select class="font-family-select" id="font-family">
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Arial">Arial</option>
                            <option value="Calibri">Calibri</option>
                            <option value="Georgia">Georgia</option>
                        </select>
                        
                        <select class="font-size-select" id="font-size">
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12" selected>12</option>
                            <option value="14">14</option>
                            <option value="16">16</option>
                            <option value="18">18</option>
                        </select>
                    </div>
                    
                    <div class="toolbar-group">
                        <button class="toolbar-btn" data-action="bold" title="Negrito (Ctrl+B)">
                            <i class="fas fa-bold"></i>
                        </button>
                        <button class="toolbar-btn" data-action="italic" title="Itálico (Ctrl+I)">
                            <i class="fas fa-italic"></i>
                        </button>
                        <button class="toolbar-btn" data-action="underline" title="Sublinhado (Ctrl+U)">
                            <i class="fas fa-underline"></i>
                        </button>
                    </div>
                    
                    <div class="toolbar-group">
                        <button class="toolbar-btn" data-action="alignLeft" title="Alinhar à esquerda">
                            <i class="fas fa-align-left"></i>
                        </button>
                        <button class="toolbar-btn" data-action="alignCenter" title="Centralizar">
                            <i class="fas fa-align-center"></i>
                        </button>
                        <button class="toolbar-btn" data-action="alignRight" title="Alinhar à direita">
                            <i class="fas fa-align-right"></i>
                        </button>
                        <button class="toolbar-btn" data-action="alignJustify" title="Justificar">
                            <i class="fas fa-align-justify"></i>
                        </button>
                    </div>
                    
                    <div class="toolbar-group">
                        <button class="toolbar-btn" data-action="bulletList" title="Lista com marcadores">
                            <i class="fas fa-list-ul"></i>
                        </button>
                        <button class="toolbar-btn" data-action="orderedList" title="Lista numerada">
                            <i class="fas fa-list-ol"></i>
                        </button>
                    </div>
                    
                    <div class="toolbar-group">
                        <button class="toolbar-btn" data-action="insertPageBreak" title="Quebra de página">
                            <i class="fas fa-file-medical"></i>
                        </button>
                        <button class="toolbar-btn" data-action="insertTable" title="Inserir tabela">
                            <i class="fas fa-table"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Régua -->
                <div class="word-ruler">
                    <div class="ruler-margin-left"></div>
                    <div class="ruler-margin-right"></div>
                </div>
                
                <!-- Editor Principal -->
                <div class="word-like-editor" id="editor-container">
                    <div class="page-container">
                        <!-- As páginas serão criadas dinamicamente pelo JavaScript -->
                    </div>
                </div>
                
                <!-- Barra de Status -->
                <div class="word-status-bar">
                    <div class="status-left">
                        <span id="page-info">Página 1 de 1</span>
                        <span id="word-count-status">0 palavras</span>
                    </div>
                    <div class="status-right">
                        <div class="zoom-controls">
                            <button class="zoom-btn" onclick="changeZoom(-10)">−</button>
                            <span class="zoom-level" id="zoom-level">100%</span>
                            <button class="zoom-btn" onclick="changeZoom(10)">+</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let editorInstance = null;
        let hasUnsavedChanges = false;
        let currentZoom = 100;
        let currentPageIndex = 0;
        let pages = [];
        
        // Dados do modelo existente (se editando)
        const modeloExistente = {!! json_encode($modelo) !!};
        
        // Inicializar editor quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            initializeEditor();
            setupEventListeners();
            updateStatus('Editor carregado e pronto!', 'ready');
        });
        
        function initializeEditor() {
            // Conteúdo inicial
            let initialContent = '<p>Inicie a criação do seu modelo jurídico aqui...</p>';
            if (modeloExistente && modeloExistente.conteudo) {
                initialContent = modeloExistente.conteudo;
            }
            
            // Criar primeira página
            pages = [createNewPageElement(1)];
            const pageContainer = document.querySelector('.page-container');
            pageContainer.innerHTML = '';
            pageContainer.appendChild(pages[0]);
            
            // Inserir conteúdo na primeira página
            const pageContent = pages[0].querySelector('.page-content');
            pageContent.innerHTML = initialContent;
            
            // Configurar eventos
            setupPageEvents(pageContent);
            
            // Verificar se precisa paginar o conteúdo inicial
            redistributeContent();
            
            updateStats();
            highlightVariables();
            updatePageInfo();
        }
        
        function setupEventListeners() {
            // Toolbar buttons
            document.querySelectorAll('.toolbar-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const action = this.dataset.action;
                    executeCommand(action);
                });
            });
            
            // Font family and size
            document.getElementById('font-family').addEventListener('change', function() {
                document.execCommand('fontName', false, this.value);
            });
            
            document.getElementById('font-size').addEventListener('change', function() {
                document.execCommand('fontSize', false, this.value);
            });
            
            // Atalhos de teclado
            document.addEventListener('keydown', function(e) {
                // Interceptar Delete/Backspace se todo documento está selecionado
                if (!e.ctrlKey && !e.metaKey && (e.key === 'Delete' || e.key === 'Backspace')) {
                    if (isEntireDocumentSelected()) {
                        e.preventDefault();
                        deleteAllContent();
                        return;
                    }
                }
                
                if (e.ctrlKey || e.metaKey) {
                    switch(e.key) {
                        case 's':
                            e.preventDefault();
                            saveModel();
                            break;
                        case 'z':
                            if (!e.shiftKey) {
                                e.preventDefault();
                                document.execCommand('undo');
                            }
                            break;
                        case 'y':
                            e.preventDefault();
                            document.execCommand('redo');
                            break;
                        case 'b':
                            e.preventDefault();
                            document.execCommand('bold');
                            break;
                        case 'i':
                            e.preventDefault();
                            document.execCommand('italic');
                            break;
                        case 'u':
                            e.preventDefault();
                            document.execCommand('underline');
                            break;
                        case '+':
                        case '=':
                            e.preventDefault();
                            changeZoom(10);
                            break;
                        case '-':
                            e.preventDefault();
                            changeZoom(-10);
                            break;
                        case '0':
                            e.preventDefault();
                            setZoom(100);
                            break;
                        case 'a':
                            e.preventDefault();
                            selectAllDocument();
                            break;
                        case 'c':
                            // Interceptar Ctrl+C se todo documento está selecionado
                            if (isEntireDocumentSelected()) {
                                e.preventDefault();
                                copyAllContent();
                            }
                            // Senão, permitir comportamento nativo
                            break;
                        case 'v':
                            // Permitir Ctrl+V nativo
                            // Não prevenir o comportamento padrão
                            break;
                        case 'x':
                            // Interceptar Ctrl+X se todo documento está selecionado
                            if (isEntireDocumentSelected()) {
                                e.preventDefault();
                                cutAllContent();
                            }
                            // Senão, permitir comportamento nativo
                            break;
                    }
                }
            });
            
            // Resetar flag de seleção quando seleção muda
            document.addEventListener('selectionchange', function() {
                // Verificar se ainda há seleção ou se mudou
                const selection = window.getSelection();
                if (selection.rangeCount === 0 || !selection.toString().trim()) {
                    resetDocumentSelectionFlag();
                }
            });
            
            // Resetar flag quando usuário clica em qualquer lugar
            document.addEventListener('click', function() {
                resetDocumentSelectionFlag();
            });
            
            // Resetar flag quando usuário começa a digitar
            document.addEventListener('input', function() {
                resetDocumentSelectionFlag();
            });
            
            // Aviso antes de sair da página
            window.addEventListener('beforeunload', function(e) {
                if (hasUnsavedChanges) {
                    e.preventDefault();
                    e.returnValue = 'Você tem alterações não salvas. Tem certeza que deseja sair?';
                    return e.returnValue;
                }
            });
            
            // Zoom com scroll
            document.getElementById('editor-container').addEventListener('wheel', function(e) {
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    const delta = e.deltaY > 0 ? -10 : 10;
                    changeZoom(delta);
                }
            });
        }
        
        function executeCommand(command) {
            const pageContent = document.getElementById('page-content');
            pageContent.focus();
            
            switch(command) {
                case 'undo':
                    document.execCommand('undo');
                    break;
                case 'redo':
                    document.execCommand('redo');
                    break;
                case 'bold':
                    document.execCommand('bold');
                    break;
                case 'italic':
                    document.execCommand('italic');
                    break;
                case 'underline':
                    document.execCommand('underline');
                    break;
                case 'alignLeft':
                    document.execCommand('justifyLeft');
                    break;
                case 'alignCenter':
                    document.execCommand('justifyCenter');
                    break;
                case 'alignRight':
                    document.execCommand('justifyRight');
                    break;
                case 'alignJustify':
                    document.execCommand('justifyFull');
                    break;
                case 'bulletList':
                    document.execCommand('insertUnorderedList');
                    break;
                case 'orderedList':
                    document.execCommand('insertOrderedList');
                    break;
                case 'insertPageBreak':
                    insertPageBreak();
                    break;
                case 'insertTable':
                    insertTable();
                    break;
            }
            
            updateToolbarState();
        }
        
        function updateToolbarState() {
            // Atualizar estado dos botões da toolbar
            document.querySelectorAll('.toolbar-btn').forEach(btn => {
                const action = btn.dataset.action;
                let isActive = false;
                
                switch(action) {
                    case 'bold':
                        isActive = document.queryCommandState('bold');
                        break;
                    case 'italic':
                        isActive = document.queryCommandState('italic');
                        break;
                    case 'underline':
                        isActive = document.queryCommandState('underline');
                        break;
                }
                
                btn.classList.toggle('active', isActive);
            });
        }
        
        function handleContentChange() {
            hasUnsavedChanges = true;
            updateStatus('Editando...', 'editing');
            updateStats();
            
            // Usar debounce para highlightVariables para evitar execução excessiva
            clearTimeout(window.highlightTimeout);
            window.highlightTimeout = setTimeout(() => {
                highlightVariables();
            }, 500);
            
            // Verificar overflow e limpar páginas vazias com debounce
            clearTimeout(window.paginationTimeout);
            window.paginationTimeout = setTimeout(() => {
                checkPageOverflow();
                removeEmptyPages();
            }, 1000);
        }
        
        function handlePaste(e) {
            e.preventDefault();
            const text = e.clipboardData.getData('text/plain');
            const html = e.clipboardData.getData('text/html');
            
            // Se tem HTML, usar ele, senão usar texto simples
            let contentToInsert = html || text.replace(/\n/g, '</p><p>');
            
            // Se é texto simples, envolver em parágrafos
            if (!html && text) {
                const paragraphs = text.split('\n').filter(p => p.trim());
                contentToInsert = paragraphs.map(p => `<p>${p}</p>`).join('');
            }
            
            document.execCommand('insertHTML', false, contentToInsert);
            
            // Redistribuir conteúdo após inserção de grandes quantidades de texto
            setTimeout(() => {
                redistributeContentManually();
            }, 100);
        }
        
        function handleKeyDown(e) {
            // Detectar Enter para verificar se precisa criar nova página
            if (e.key === 'Enter') {
                console.log('Enter pressionado');
                const currentPage = getCurrentActivePage();
                if (currentPage) {
                    const pageContent = currentPage.querySelector('.page-content');
                    
                    console.log('Página atual:', currentPage);
                    console.log('Altura antes do Enter:', pageContent.scrollHeight);
                    
                    // Verificar se a página atual está próxima do limite após Enter
                    setTimeout(() => {
                        console.log('Altura após Enter:', pageContent.scrollHeight);
                        checkIfNeedNewPageOnEnter(currentPage);
                    }, 50); // Delay maior para garantir que o Enter foi processado
                }
            }
            
            // Navegação entre páginas
            if (e.key === 'PageUp' && currentPageIndex > 0) {
                e.preventDefault();
                goToPage(currentPageIndex - 1);
            } else if (e.key === 'PageDown' && currentPageIndex < pages.length - 1) {
                e.preventDefault();
                goToPage(currentPageIndex + 1);
            }
            
            // Detectar teclas de deleção para limpeza mais rápida de páginas vazias
            if (e.key === 'Backspace' || e.key === 'Delete') {
                // Usar timeout menor para deleção para resposta mais rápida
                clearTimeout(window.deletionTimeout);
                window.deletionTimeout = setTimeout(() => {
                    removeEmptyPages();
                }, 300);
                
                // Consolidação menos frequente para operações mais pesadas
                clearTimeout(window.consolidationTimeout);
                window.consolidationTimeout = setTimeout(() => {
                    consolidatePages();
                }, 2000);
            }
        }
        
        function checkIfNeedNewPageOnEnter(currentPage) {
            const pageContent = currentPage.querySelector('.page-content');
            const maxHeight = 931; // altura máxima da página em pixels
            const safetyMargin = 300; // margem de segurança bem grande para teste: 300px
            
            console.log('Verificando página:', {
                scrollHeight: pageContent.scrollHeight,
                maxHeight: maxHeight,
                safetyMargin: safetyMargin,
                threshold: maxHeight - safetyMargin
            });
            
            // TESTE: Criar nova página sempre que Enter for pressionado 
            // (depois ajustaremos para a condição correta)
            const shouldCreateNewPage = pageContent.scrollHeight > maxHeight - safetyMargin;
            console.log('Deve criar nova página?', shouldCreateNewPage);
            
            if (shouldCreateNewPage) {
                console.log('Página excedeu limite, criando nova página');
                
                const currentPageIndex = Array.from(pages).indexOf(currentPage);
                
                console.log('Debug páginas:', {
                    totalPaginas: pages.length,
                    currentPageIndex: currentPageIndex,
                    eUltimaPagina: currentPageIndex === pages.length - 1,
                    pagesArray: pages
                });
                
                // Verificar se cursor está próximo do final do conteúdo
                const selection = window.getSelection();
                let isNearEnd = false;
                if (selection.rangeCount > 0) {
                    const range = selection.getRangeAt(0);
                    const cursorPosition = range.startOffset;
                    const container = range.startContainer;
                    isNearEnd = isCursorNearEndOfPage(pageContent, container, cursorPosition);
                }
                
                console.log('Cursor próximo do final?', isNearEnd);
                
                if (isNearEnd) {
                    // Se cursor está no final e página cheia, vamos para próxima página
                    if (currentPageIndex === pages.length - 1) {
                        console.log('É última página, criando nova');
                        // Criar nova página
                        const newPage = createNewPageElement(pages.length + 1);
                        pages.push(newPage);
                        document.querySelector('.page-container').appendChild(newPage);
                        setupPageEvents(newPage.querySelector('.page-content'));
                        
                        console.log('Movendo cursor para nova página');
                        moveToNextPageAfterEnter(currentPage, newPage);
                        updatePageInfo();
                        
                    } else {
                        console.log('Não é última página, movendo para próxima existente');
                        // Mover cursor para próxima página existente
                        const nextPage = pages[currentPageIndex + 1];
                        if (nextPage) {
                            moveToNextPageAfterEnter(currentPage, nextPage);
                            updatePageInfo();
                        }
                    }
                } else {
                    console.log('Cursor não está no final, redistribuindo conteúdo');
                    // Se cursor não está no final, apenas redistribuir conteúdo
                    if (currentPageIndex === pages.length - 1) {
                        // Criar nova página para overflow
                        const newPage = createNewPageElement(pages.length + 1);
                        pages.push(newPage);
                        document.querySelector('.page-container').appendChild(newPage);
                        setupPageEvents(newPage.querySelector('.page-content'));
                        updatePageInfo();
                    }
                    // Redistribuir conteúdo entre páginas existentes
                    redistributeContentManually();
                }
            } else {
                console.log('Página ainda tem espaço');
            }
        }
        
        function isCursorNearEndOfPage(pageContent, container, offset) {
            // Verificar se cursor está próximo do final da página - versão corrigida
            try {
                console.log('Verificando posição do cursor:', {
                    container: container,
                    offset: offset,
                    nodeType: container.nodeType,
                    containerClassName: container.className || 'sem classe'
                });
                
                // Primeiro verificar se o container está realmente dentro do pageContent
                if (!pageContent.contains(container)) {
                    console.log('Container não está dentro da página - ignorando');
                    return false;
                }
                
                // Verificar se estamos nos elementos filhos diretos da página
                const directChildren = Array.from(pageContent.children).filter(child => {
                    // Filtrar apenas elementos de texto (p, div, span, etc.)
                    const tagName = child.tagName.toLowerCase();
                    return ['p', 'div', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'br'].includes(tagName);
                });
                
                if (directChildren.length === 0) {
                    console.log('Página sem elementos de texto válidos');
                    return true; // Página vazia, considerar final
                }
                
                const lastElement = directChildren[directChildren.length - 1];
                const secondToLastElement = directChildren[directChildren.length - 2];
                
                console.log('Último elemento válido:', lastElement);
                console.log('Penúltimo elemento válido:', secondToLastElement);
                
                // Verificar se cursor está no último ou penúltimo elemento
                let currentNode = container;
                let isInFinalElements = false;
                
                // Subir na hierarquia para encontrar o elemento pai
                while (currentNode && currentNode !== pageContent) {
                    if (currentNode === lastElement || currentNode === secondToLastElement) {
                        isInFinalElements = true;
                        console.log('Cursor está nos elementos finais válidos');
                        break;
                    }
                    currentNode = currentNode.parentNode;
                }
                
                // Verificação adicional: se estamos no último elemento de texto
                if (!isInFinalElements && directChildren.length > 0) {
                    // Verificar se estamos nos últimos 20% do conteúdo de texto
                    const allText = pageContent.textContent || '';
                    const selection = window.getSelection();
                    if (selection.rangeCount > 0) {
                        const range = selection.getRangeAt(0);
                        const preCaretRange = range.cloneRange();
                        preCaretRange.selectNodeContents(pageContent);
                        preCaretRange.setEnd(range.endContainer, range.endOffset);
                        const textBeforeCaret = preCaretRange.toString();
                        const percentageFromStart = (textBeforeCaret.length / allText.length) * 100;
                        
                        console.log('Posição no texto:', {
                            textoAntesCursor: textBeforeCaret.length,
                            textoTotal: allText.length,
                            porcentagem: percentageFromStart
                        });
                        
                        if (percentageFromStart > 80) {
                            isInFinalElements = true;
                            console.log('Cursor nos últimos 20% do texto');
                        }
                    }
                }
                
                if (isInFinalElements) {
                    console.log('Cursor próximo do final da página');
                    return true;
                }
                
                console.log('Cursor não está no final');
                return false;
                
            } catch (error) {
                console.warn('Erro ao verificar posição do cursor:', error);
                return false; // Em caso de erro, assumir que NÃO está no final para evitar ações incorretas
            }
        }
        
        function moveToNextPageAfterEnter(currentPage, nextPage) {
            // Move cursor para nova página quando Enter foi pressionado no final
            console.log('Movendo cursor para próxima página:', {
                currentPage: currentPage,
                nextPage: nextPage
            });
            
            const currentPageContent = currentPage.querySelector('.page-content');
            const nextPageContent = nextPage.querySelector('.page-content');
            
            // Verificar se há conteúdo na página atual que precisa ser preservado
            const currentContent = currentPageContent.innerHTML.trim();
            console.log('Conteúdo da página atual:', currentContent.substring(0, 100) + '...');
            
            // Garantir que a nova página tenha um parágrafo inicial se estiver vazia
            const nextContent = nextPageContent.innerHTML.trim();
            if (!nextContent || nextContent === '<br>' || nextContent === '<p></p>') {
                console.log('Próxima página vazia, criando parágrafo inicial');
                nextPageContent.innerHTML = '<p><br></p>';
            } else {
                console.log('Próxima página já tem conteúdo:', nextContent.substring(0, 100) + '...');
            }
            
            // Focar na nova página
            nextPageContent.focus();
            
            // Posicionar cursor no início da nova página
            const range = document.createRange();
            const firstElement = nextPageContent.firstElementChild;
            
            if (firstElement) {
                if (firstElement.tagName === 'P') {
                    // Se é um parágrafo, posicionar dentro dele
                    range.setStart(firstElement, 0);
                } else {
                    // Senão, posicionar antes do elemento
                    range.setStartBefore(firstElement);
                }
                range.collapse(true);
            } else {
                range.setStart(nextPageContent, 0);
                range.collapse(true);
            }
            
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
            
            // Scroll para a nova página
            nextPage.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            console.log('Cursor movido com sucesso para próxima página');
        }
        
        function createNewPageElement(pageNumber) {
            const newPage = document.createElement('div');
            newPage.className = 'page';
            newPage.innerHTML = `
                <div class="page-content" contenteditable="true"></div>
                <div class="page-number">${pageNumber}</div>
            `;
            return newPage;
        }
        
        function setupPageEvents(pageContent) {
            pageContent.addEventListener('input', handleContentChange);
            pageContent.addEventListener('paste', handlePaste);
            pageContent.addEventListener('keydown', handleKeyDown);
        }
        
        function redistributeContent() {
            const allContent = getAllContent();
            if (!allContent.trim()) return;
            
            // Salvar posição do cursor antes de redistribuir
            const selection = window.getSelection();
            const range = selection.rangeCount > 0 ? selection.getRangeAt(0) : null;
            let cursorInfo = null;
            
            if (range) {
                cursorInfo = {
                    startOffset: range.startOffset,
                    endOffset: range.endOffset,
                    startContainer: range.startContainer,
                    endContainer: range.endContainer
                };
            }
            
            // Limpar todas as páginas
            pages.forEach(page => {
                const content = page.querySelector('.page-content');
                content.innerHTML = '';
            });
            
            // Criar um elemento temporário para medir o conteúdo
            const tempDiv = document.createElement('div');
            tempDiv.style.position = 'absolute';
            tempDiv.style.top = '-9999px';
            tempDiv.style.width = '650px'; // largura do conteúdo da página (794 - 144 margins)
            tempDiv.style.fontFamily = 'Times New Roman, serif';
            tempDiv.style.fontSize = '12pt';
            tempDiv.style.lineHeight = '1.5';
            tempDiv.innerHTML = allContent;
            document.body.appendChild(tempDiv);
            
            // Dividir conteúdo em páginas
            const maxHeight = 931; // altura disponível na página (1123 - 192 margins)
            let currentPageIndex = 0;
            let currentHeight = 0;
            
            const elements = Array.from(tempDiv.children);
            
            for (let element of elements) {
                const elementHeight = element.offsetHeight + 12; // margem entre parágrafos
                
                // Se o elemento não cabe na página atual, criar nova página
                if (currentHeight + elementHeight > maxHeight && currentHeight > 0) {
                    currentPageIndex++;
                    currentHeight = 0;
                    
                    // Criar nova página se necessário
                    if (currentPageIndex >= pages.length) {
                        const newPage = createNewPageElement(currentPageIndex + 1);
                        pages.push(newPage);
                        document.querySelector('.page-container').appendChild(newPage);
                        setupPageEvents(newPage.querySelector('.page-content'));
                    }
                }
                
                // Adicionar elemento à página atual
                if (currentPageIndex < pages.length) {
                    const pageContent = pages[currentPageIndex].querySelector('.page-content');
                    pageContent.appendChild(element.cloneNode(true));
                    currentHeight += elementHeight;
                }
            }
            
            // Remover páginas vazias extras
            while (pages.length > currentPageIndex + 1) {
                const lastPage = pages.pop();
                lastPage.remove();
            }
            
            // Limpar elemento temporário
            document.body.removeChild(tempDiv);
            
            // Tentar restaurar posição do cursor se havia uma seleção anterior
            if (cursorInfo && pages.length > 0) {
                try {
                    // Focar na primeira página se não conseguir restaurar a posição exata
                    const firstPageContent = pages[0].querySelector('.page-content');
                    firstPageContent.focus();
                    
                    // Colocar cursor no final do conteúdo
                    const newRange = document.createRange();
                    newRange.selectNodeContents(firstPageContent);
                    newRange.collapse(false);
                    selection.removeAllRanges();
                    selection.addRange(newRange);
                } catch (e) {
                    // Se der erro, apenas focar na primeira página
                    const firstPageContent = pages[0].querySelector('.page-content');
                    firstPageContent.focus();
                }
            }
            
            updatePageInfo();
        }
        
        function getAllContent() {
            let allContent = '';
            pages.forEach(page => {
                const content = page.querySelector('.page-content').innerHTML;
                if (content.trim()) {
                    allContent += content;
                }
            });
            return allContent;
        }
        
        function checkPageOverflow() {
            // Verifica se a página atual precisa de uma nova página sem reorganizar conteúdo
            const currentPage = getCurrentActivePage();
            if (!currentPage) return;
            
            const pageContent = currentPage.querySelector('.page-content');
            const maxHeight = 931; // altura máxima da página em pixels
            
            // Verificar se o conteúdo atual excede a altura máxima
            if (pageContent.scrollHeight > maxHeight) {
                // Criar nova página apenas se ainda não existe
                const currentPageIndex = Array.from(pages).indexOf(currentPage);
                if (currentPageIndex === pages.length - 1) {
                    const newPage = createNewPageElement(pages.length + 1);
                    pages.push(newPage);
                    document.querySelector('.page-container').appendChild(newPage);
                    setupPageEvents(newPage.querySelector('.page-content'));
                    
                    // Mover cursor para nova página se necessário
                    moveOverflowToNextPage(currentPage, newPage);
                }
            }
        }
        
        function getCurrentActivePage() {
            // Encontrar a página que contém o cursor atual
            const selection = window.getSelection();
            if (selection.rangeCount === 0) return pages[0];
            
            const range = selection.getRangeAt(0);
            let container = range.startContainer;
            
            // Subir na hierarquia até encontrar a página
            while (container && !container.classList?.contains('page')) {
                container = container.parentNode;
            }
            
            return container || pages[0];
        }
        
        function moveOverflowToNextPage(currentPage, nextPage) {
            const currentPageContent = currentPage.querySelector('.page-content');
            const nextPageContent = nextPage.querySelector('.page-content');
            const maxHeight = 931;
            
            // Se a página atual não está com overflow, não fazer nada
            if (currentPageContent.scrollHeight <= maxHeight) {
                return;
            }
            
            // Salvar posição do cursor
            const selection = window.getSelection();
            const hadSelection = selection.rangeCount > 0;
            
            // Encontrar onde "cortar" o conteúdo
            const elements = Array.from(currentPageContent.children);
            let totalHeight = 0;
            let cutIndex = -1;
            
            // Criar elemento temporário para medir alturas
            const tempDiv = document.createElement('div');
            tempDiv.style.position = 'absolute';
            tempDiv.style.top = '-9999px';
            tempDiv.style.width = '650px';
            tempDiv.style.fontFamily = 'Times New Roman, serif';
            tempDiv.style.fontSize = '12pt';
            tempDiv.style.lineHeight = '1.5';
            document.body.appendChild(tempDiv);
            
            for (let i = 0; i < elements.length; i++) {
                tempDiv.appendChild(elements[i].cloneNode(true));
                const currentHeight = tempDiv.offsetHeight;
                
                if (currentHeight > maxHeight && cutIndex === -1) {
                    cutIndex = Math.max(0, i - 1);
                    break;
                }
            }
            
            document.body.removeChild(tempDiv);
            
            // Se encontrou onde cortar, mover elementos
            if (cutIndex >= 0 && cutIndex < elements.length - 1) {
                // Mover elementos que não cabem para a próxima página
                for (let i = cutIndex + 1; i < elements.length; i++) {
                    const element = elements[i];
                    element.remove();
                    nextPageContent.appendChild(element);
                }
                
                // Se tinha seleção e está na área movida, mover cursor
                if (hadSelection) {
                    nextPageContent.focus();
                    const range = document.createRange();
                    range.setStart(nextPageContent, 0);
                    range.collapse(true);
                    selection.removeAllRanges();
                    selection.addRange(range);
                }
            }
        }
        
        function redistributeContentManually() {
            // Função para redistribuir conteúdo manualmente quando necessário
            redistributeContent();
        }
        
        function removeEmptyPages() {
            // Remove páginas vazias do final para o início (exceto a primeira)
            let removedAny = false;
            let cursorMovedTo = null;
            
            // Começar do final para evitar problemas de índice
            for (let i = pages.length - 1; i > 0; i--) {
                const page = pages[i];
                const pageContent = page.querySelector('.page-content');
                
                if (isPageEmpty(pageContent)) {
                    // Verificar se o cursor está nesta página
                    const selection = window.getSelection();
                    const isActivelyEditing = selection.rangeCount > 0 && 
                        pageContent.contains(selection.getRangeAt(0).startContainer);
                    
                    if (isActivelyEditing) {
                        // Se está editando nesta página vazia, mover cursor para página anterior
                        const previousPage = pages[i - 1];
                        if (previousPage) {
                            const previousPageContent = previousPage.querySelector('.page-content');
                            cursorMovedTo = previousPageContent;
                        }
                    }
                    
                    // Remover a página vazia
                    page.remove();
                    pages.splice(i, 1);
                    removedAny = true;
                }
            }
            
            // Mover cursor se necessário
            if (cursorMovedTo) {
                cursorMovedTo.focus();
                
                // Colocar cursor no final do conteúdo da página anterior
                const range = document.createRange();
                range.selectNodeContents(cursorMovedTo);
                range.collapse(false);
                
                const selection = window.getSelection();
                selection.removeAllRanges();
                selection.addRange(range);
            }
            
            // Atualizar numeração das páginas e informações
            if (removedAny) {
                updatePageNumbers();
                updatePageInfo();
            }
            
            return removedAny;
        }
        
        function isPageEmpty(pageContent) {
            // Verifica se uma página está vazia ou contém apenas conteúdo irrelevante
            const content = pageContent.innerHTML.trim();
            
            // Página completamente vazia
            if (!content) return true;
            
            // Apenas parágrafos vazios
            if (content === '<p></p>' || content === '<p><br></p>' || content === '<br>') return true;
            
            // Apenas espaços em branco
            const textContent = pageContent.textContent || pageContent.innerText || '';
            if (!textContent.trim()) return true;
            
            // Conteúdo com apenas elementos vazios
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = content;
            const hasActualContent = tempDiv.textContent.trim().length > 0;
            
            return !hasActualContent;
        }
        
        function updatePageNumbers() {
            // Atualizar numeração de todas as páginas
            pages.forEach((page, index) => {
                const pageNumber = page.querySelector('.page-number');
                if (pageNumber) {
                    pageNumber.textContent = index + 1;
                }
            });
        }
        
        let isDocumentFullySelected = false; // Flag para rastrear se todo documento está selecionado
        
        function selectAllDocument() {
            // Seleciona todo o conteúdo do documento (todas as páginas)
            if (pages.length === 0) return;
            
            const selection = window.getSelection();
            selection.removeAllRanges();
            
            try {
                // Para múltiplas páginas contenteditable, vamos usar uma abordagem diferente
                // Vamos selecionar o container de páginas inteiro
                const pageContainer = document.querySelector('.page-container');
                
                if (pageContainer) {
                    const range = document.createRange();
                    range.selectNodeContents(pageContainer);
                    selection.addRange(range);
                    
                    // Focar no primeiro elemento editável
                    const firstPageContent = pages[0].querySelector('.page-content');
                    if (firstPageContent) {
                        firstPageContent.focus();
                    }
                } else {
                    // Fallback para página única
                    const pageContent = pages[0].querySelector('.page-content');
                    const range = document.createRange();
                    range.selectNodeContents(pageContent);
                    selection.addRange(range);
                }
                
            } catch (error) {
                console.warn('Erro ao selecionar documento completo:', error);
                
                // Fallback final: selecionar todo conteúdo usando uma abordagem mais simples
                try {
                    // Selecionar cada página individualmente e combinar
                    let combinedHTML = '';
                    pages.forEach(page => {
                        const content = page.querySelector('.page-content');
                        if (content) {
                            combinedHTML += content.innerHTML;
                        }
                    });
                    
                    // Criar um elemento temporário para seleção
                    const tempDiv = document.createElement('div');
                    tempDiv.style.position = 'absolute';
                    tempDiv.style.left = '-9999px';
                    tempDiv.innerHTML = combinedHTML;
                    document.body.appendChild(tempDiv);
                    
                    // Selecionar o conteúdo temporário
                    const range = document.createRange();
                    range.selectNodeContents(tempDiv);
                    selection.addRange(range);
                    
                    // Remover elemento temporário após um momento
                    setTimeout(() => {
                        if (tempDiv.parentNode) {
                            document.body.removeChild(tempDiv);
                        }
                    }, 100);
                    
                } catch (finalError) {
                    console.warn('Fallback de seleção também falhou:', finalError);
                    // Última tentativa: selecionar apenas primeira página
                    const firstPageContent = pages[0].querySelector('.page-content');
                    if (firstPageContent) {
                        const range = document.createRange();
                        range.selectNodeContents(firstPageContent);
                        selection.addRange(range);
                        firstPageContent.focus();
                    }
                }
            }
            
            // Marcar que todo o documento está selecionado
            isDocumentFullySelected = true;
        }
        
        function getFirstSelectableNode(container) {
            // Encontra o primeiro nó selecionável (texto ou elemento)
            const walker = document.createTreeWalker(
                container,
                NodeFilter.SHOW_TEXT | NodeFilter.SHOW_ELEMENT,
                {
                    acceptNode: function(node) {
                        if (node.nodeType === Node.TEXT_NODE && node.textContent.trim()) {
                            return NodeFilter.FILTER_ACCEPT;
                        }
                        if (node.nodeType === Node.ELEMENT_NODE && 
                            (node.tagName === 'P' || node.tagName === 'DIV' || node.tagName === 'SPAN')) {
                            return NodeFilter.FILTER_ACCEPT;
                        }
                        return NodeFilter.FILTER_SKIP;
                    }
                }
            );
            
            return walker.nextNode();
        }
        
        function getLastSelectableNode(container) {
            // Encontra o último nó selecionável (texto ou elemento)
            const walker = document.createTreeWalker(
                container,
                NodeFilter.SHOW_TEXT | NodeFilter.SHOW_ELEMENT,
                {
                    acceptNode: function(node) {
                        if (node.nodeType === Node.TEXT_NODE && node.textContent.trim()) {
                            return NodeFilter.FILTER_ACCEPT;
                        }
                        if (node.nodeType === Node.ELEMENT_NODE && 
                            (node.tagName === 'P' || node.tagName === 'DIV' || node.tagName === 'SPAN')) {
                            return NodeFilter.FILTER_ACCEPT;
                        }
                        return NodeFilter.FILTER_SKIP;
                    }
                }
            );
            
            let lastNode = null;
            let currentNode = walker.nextNode();
            while (currentNode) {
                lastNode = currentNode;
                currentNode = walker.nextNode();
            }
            
            return lastNode;
        }
        
        function resetDocumentSelectionFlag() {
            // Reseta a flag quando seleção muda
            isDocumentFullySelected = false;
        }
        
        function isEntireDocumentSelected() {
            // Verifica se todo o documento está selecionado
            return isDocumentFullySelected;
        }
        
        function deleteAllContent() {
            // Deleta todo o conteúdo do documento
            pages.forEach((page, index) => {
                const pageContent = page.querySelector('.page-content');
                if (index === 0) {
                    // Manter primeira página com conteúdo vazio
                    pageContent.innerHTML = '<p><br></p>';
                } else {
                    // Remover páginas extras
                    page.remove();
                }
            });
            
            // Resetar array de páginas
            pages = pages.slice(0, 1);
            
            // Focar na primeira página
            const firstPageContent = pages[0].querySelector('.page-content');
            firstPageContent.focus();
            
            // Posicionar cursor no início
            const range = document.createRange();
            range.setStart(firstPageContent, 0);
            range.collapse(true);
            
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
            
            // Resetar flag
            isDocumentFullySelected = false;
            
            // Atualizar interface
            updatePageNumbers();
            updatePageInfo();
            updateStats();
        }
        
        function copyAllContent() {
            // Copia todo o conteúdo do documento
            const allContent = getAllContent();
            
            // Copiar para clipboard usando a API moderna
            if (navigator.clipboard && navigator.clipboard.writeText) {
                // Converter HTML para texto simples para clipboard
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = allContent;
                const textContent = tempDiv.textContent || tempDiv.innerText || '';
                
                navigator.clipboard.writeText(textContent).then(() => {
                    console.log('Conteúdo copiado para o clipboard');
                }).catch(() => {
                    // Fallback para método antigo
                    copyToClipboardFallback(allContent);
                });
            } else {
                // Fallback para navegadores antigos
                copyToClipboardFallback(allContent);
            }
        }
        
        function cutAllContent() {
            // Corta todo o conteúdo do documento (copia e depois deleta)
            const allContent = getAllContent();
            
            // Copiar para clipboard usando a API moderna
            if (navigator.clipboard && navigator.clipboard.writeText) {
                // Converter HTML para texto simples para clipboard
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = allContent;
                const textContent = tempDiv.textContent || tempDiv.innerText || '';
                
                navigator.clipboard.writeText(textContent).then(() => {
                    deleteAllContent();
                }).catch(() => {
                    // Fallback para método antigo
                    copyToClipboardFallback(allContent);
                    deleteAllContent();
                });
            } else {
                // Fallback para navegadores antigos
                copyToClipboardFallback(allContent);
                deleteAllContent();
            }
        }
        
        function copyToClipboardFallback(content) {
            // Método fallback para cópia
            const tempTextArea = document.createElement('textarea');
            tempTextArea.style.position = 'absolute';
            tempTextArea.style.left = '-9999px';
            tempTextArea.style.top = '-9999px';
            
            // Converter HTML para texto
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = content;
            tempTextArea.value = tempDiv.textContent || tempDiv.innerText || '';
            
            document.body.appendChild(tempTextArea);
            tempTextArea.select();
            
            try {
                document.execCommand('copy');
            } catch (err) {
                console.warn('Não foi possível copiar para o clipboard');
            }
            
            document.body.removeChild(tempTextArea);
        }
        
        function consolidatePages() {
            // Consolida o conteúdo de páginas que podem ser reunidas
            // (chamada menos frequente para operações mais complexas)
            let hasChanges = false;
            
            for (let i = pages.length - 1; i > 0; i--) {
                const currentPage = pages[i];
                const previousPage = pages[i - 1];
                const currentContent = currentPage.querySelector('.page-content');
                const previousContent = previousPage.querySelector('.page-content');
                
                // Se a página atual tem pouco conteúdo, tentar mover para a anterior
                if (currentContent.scrollHeight < 200 && !isPageEmpty(currentContent)) {
                    // Testar se o conteúdo caberia na página anterior
                    const tempDiv = document.createElement('div');
                    tempDiv.style.position = 'absolute';
                    tempDiv.style.top = '-9999px';
                    tempDiv.style.width = '650px';
                    tempDiv.style.fontFamily = 'Times New Roman, serif';
                    tempDiv.style.fontSize = '12pt';
                    tempDiv.style.lineHeight = '1.5';
                    
                    // Combinar conteúdo das duas páginas
                    tempDiv.innerHTML = previousContent.innerHTML + currentContent.innerHTML;
                    document.body.appendChild(tempDiv);
                    
                    const combinedHeight = tempDiv.offsetHeight;
                    document.body.removeChild(tempDiv);
                    
                    // Se cabe na página anterior (931px), mover conteúdo
                    if (combinedHeight <= 931) {
                        // Mover conteúdo para página anterior
                        const elementsToMove = Array.from(currentContent.children);
                        elementsToMove.forEach(element => {
                            previousContent.appendChild(element);
                        });
                        
                        // Remover página vazia
                        currentPage.remove();
                        pages.splice(i, 1);
                        hasChanges = true;
                    }
                }
            }
            
            if (hasChanges) {
                updatePageNumbers();
                updatePageInfo();
            }
            
            return hasChanges;
        }
        
        function insertPageBreak() {
            document.execCommand('insertHTML', false, '<div style="page-break-before: always;"></div>');
            createNewPage();
        }
        
        function insertTable() {
            const tableHTML = `
                <table border="1" style="border-collapse: collapse; width: 100%; margin: 10px 0;">
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">Célula 1</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">Célula 2</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">Célula 3</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">Célula 4</td>
                    </tr>
                </table>
            `;
            document.execCommand('insertHTML', false, tableHTML);
        }
        
        function changeZoom(delta) {
            const newZoom = Math.max(50, Math.min(200, currentZoom + delta));
            setZoom(newZoom);
        }
        
        function setZoom(zoom) {
            currentZoom = zoom;
            const editorContainer = document.getElementById('editor-container');
            
            // Remover classes de zoom anteriores
            editorContainer.classList.remove('zoom-50', 'zoom-75', 'zoom-100', 'zoom-125', 'zoom-150', 'zoom-200');
            
            // Adicionar classe de zoom atual
            editorContainer.classList.add(`zoom-${zoom}`);
            
            // Atualizar display
            document.getElementById('zoom-level').textContent = `${zoom}%`;
        }
        
        function goToPage(pageIndex) {
            currentPageIndex = pageIndex;
            const pages = document.querySelectorAll('.page');
            if (pages[pageIndex]) {
                pages[pageIndex].scrollIntoView({ behavior: 'smooth' });
                pages[pageIndex].querySelector('.page-content').focus();
            }
            updatePageInfo();
        }
        
        function updatePageInfo() {
            const totalPages = pages.length;
            document.getElementById('page-info').textContent = `Página ${currentPageIndex + 1} de ${totalPages}`;
        }
        
        function updateStatus(message, type = 'ready') {
            const statusDot = document.getElementById('status-dot');
            const statusText = document.getElementById('status-text');
            
            statusText.textContent = message;
            statusDot.className = `status-dot ${type}`;
        }
        
        function updateStats() {
            const allContent = getAllContent();
            const allText = pages.map(page => page.querySelector('.page-content').textContent || '').join(' ');
            
            const wordCount = allText.trim() ? allText.trim().split(/\s+/).length : 0;
            const charCount = allText.length;
            const varCount = (allContent.match(/\{\{[^}]+\}\}/g) || []).length;
            
            document.getElementById('word-count').textContent = wordCount;
            document.getElementById('char-count').textContent = charCount;
            document.getElementById('var-count').textContent = varCount;
            document.getElementById('word-count-status').textContent = `${wordCount} palavras`;
        }
        
        function highlightVariables() {
            // Destacar variáveis em todas as páginas sem perder a posição do cursor
            pages.forEach(page => {
                const pageContent = page.querySelector('.page-content');
                const content = pageContent.innerHTML;
                
                // Verificar se já há variáveis destacadas para evitar loops desnecessários
                if (content.includes('variable-placeholder')) {
                    return;
                }
                
                const highlightedContent = content.replace(
                    /\{\{([^}]+)\}\}/g,
                    function(match, p1) {
                        return '<span class="variable-placeholder">' + match + '</span>';
                    }
                );
                
                // Só atualizar se houve mudança real e não há seleção ativa
                if (content !== highlightedContent && !window.getSelection().rangeCount) {
                    pageContent.innerHTML = highlightedContent;
                }
            });
        }
        
        function getContent() {
            return getAllContent();
        }
        
        function setContent(content) {
            // Limpar todas as páginas existentes
            const pageContainer = document.querySelector('.page-container');
            pageContainer.innerHTML = '';
            pages = [];
            
            // Criar primeira página
            pages = [createNewPageElement(1)];
            pageContainer.appendChild(pages[0]);
            setupPageEvents(pages[0].querySelector('.page-content'));
            
            // Inserir conteúdo na primeira página
            const pageContent = pages[0].querySelector('.page-content');
            pageContent.innerHTML = content;
            
            // Redistribuir conteúdo automaticamente
            redistributeContent();
            
            updateStats();
            highlightVariables();
            updatePageInfo();
        }
        
        @verbatim
        function insertQuickTemplate() {
            const tipo = document.getElementById('modelo-tipo').value;
            let templateContent = '';
            
            switch(tipo) {
                case 'contrato':
                    templateContent = `
                        <div class="legal-document">
                            <h1>{{tipo_contrato}}</h1>
                            
                            <p><strong>CONTRATANTE:</strong> {{contratante_nome}}, {{contratante_qualificacao}}, inscrita no CNPJ sob o nº {{contratante_cnpj}}, com sede na {{contratante_endereco}}.</p>
                            
                            <p><strong>CONTRATADO:</strong> {{contratado_nome}}, {{contratado_qualificacao}}, inscrito no CPF sob o nº {{contratado_cpf}}, residente na {{contratado_endereco}}.</p>
                            
                            <p>As partes acima identificadas têm, entre si, justo e acertado o presente Contrato, que se regerá pelas cláusulas seguintes e pelas condições descritas no presente.</p>
                            
                            <p><strong>CLÁUSULA 1ª - DO OBJETO:</strong> O presente contrato tem por objeto {{objeto_contrato}}.</p>
                            
                            <p><strong>CLÁUSULA 2ª - DO PRAZO:</strong> O prazo de execução dos serviços será de {{prazo_contrato}}.</p>
                            
                            <p><strong>CLÁUSULA 3ª - DO VALOR:</strong> O valor total dos serviços será de {{valor_contrato}}.</p>
                            
                            <p><strong>CLÁUSULA 4ª - DO PAGAMENTO:</strong> O pagamento será efetuado {{forma_pagamento}}.</p>
                            
                            <p>Para dirimir quaisquer controvérsias decorrentes do presente contrato, as partes elegem o foro da Comarca de {{cidade}}.</p>
                            
                            <p>E, por estarem assim justas e contratadas, as partes assinam o presente contrato em duas vias de igual teor, juntamente com as testemunhas abaixo.</p>
                            
                            <p>{{cidade}}, {{data}}.</p>
                        </div>
                    `;
                    break;
                    
                case 'peticao':
                    templateContent = `
                        <div class="legal-document">
                            <h1>{{tipo_peticao}}</h1>
                            
                            <p>Excelentíssimo(a) Senhor(a) {{autoridade}}<br>
                            {{orgao}}<br>
                            {{comarca}}</p>
                            
                            <p>{{requerente_nome}}, {{requerente_qualificacao}}, inscrito no CPF sob o nº {{requerente_cpf}}, residente na {{requerente_endereco}}, por meio de seu advogado que esta subscreve, vem respeitosamente à presença de Vossa Excelência, com fundamento no {{fundamento_juridico}}, expor e requerer o que segue:</p>
                            
                            <p><strong>I - DOS FATOS:</strong></p>
                            <p>{{fatos}}</p>
                            
                            <p><strong>II - DO DIREITO:</strong></p>
                            <p>{{fundamento_juridico}}</p>
                            
                            <p><strong>III - DO PEDIDO:</strong></p>
                            <p>Diante do exposto, requer:</p>
                            <p>{{pedido}}</p>
                            
                            <p>Termos em que pede deferimento.</p>
                            
                            <p>{{cidade}}, {{data}}.</p>
                            
                            <p>{{advogado_nome}}<br>
                            OAB/{{advogado_estado}} {{advogado_oab}}</p>
                        </div>
                    `;
                    break;
                    
                case 'projeto_lei':
                    templateContent = `
                        <div class="legal-document">
                            <h1>PROJETO DE LEI Nº {{numero_projeto}}/{{ano_projeto}}</h1>
                            
                            <p><strong>EMENTA:</strong> {{ementa}}</p>
                            
                            <p><strong>Art. 1º</strong> Esta Lei estabelece {{objeto_lei}}.</p>
                            
                            <p><strong>Art. 2º</strong> Para os efeitos desta Lei, considera-se:</p>
                            
                            <p><strong>I -</strong> {{definicao_a}};</p>
                            
                            <p><strong>II -</strong> {{definicao_b}}.</p>
                            
                            <p><strong>Art. 3º</strong> Esta Lei entra em vigor na data de sua publicação.</p>
                        </div>
                    `;
                    break;
                    
                default:
                    templateContent = `
                        <div class="legal-document">
                            <h1>{{titulo_documento}}</h1>
                            
                            <p>{{conteudo_documento}}</p>
                            
                            <p>{{cidade}}, {{data}}.</p>
                        </div>
                    `;
            }
            
            setContent(templateContent);
            
            Swal.fire({
                title: 'Template Inserido!',
                text: `Template de ${tipo} foi inserido com sucesso.`,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }
        @endverbatim
        
        function insertVariable() {
            Swal.fire({
                title: 'Inserir Variável',
                input: 'text',
                inputPlaceholder: 'Nome da variável (ex: nome_cliente)',
                showCancelButton: true,
                confirmButtonText: 'Inserir',
                cancelButtonText: 'Cancelar',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Por favor, informe o nome da variável';
                    }
                    if (!/^[a-zA-Z_][a-zA-Z0-9_]*$/.test(value)) {
                        return 'Nome da variável deve conter apenas letras, números e underscore';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    insertVariableAtCursor(result.value);
                }
            });
        }
        
        function insertVariableAtCursor(variableName) {
            const variableHtml = '<span class="variable-placeholder">{{' + variableName + '}}</span>';
            document.execCommand('insertHTML', false, variableHtml);
            updateStats();
        }
        
        function saveModel() {
            const nome = document.getElementById('modelo-nome').value;
            const tipo = document.getElementById('modelo-tipo').value;
            const descricao = document.getElementById('modelo-descricao').value;
            const conteudo = getContent();
            
            if (!nome.trim()) {
                Swal.fire({
                    title: 'Erro!',
                    text: 'Por favor, informe o nome do modelo.',
                    icon: 'error'
                });
                return;
            }
            
            if (!conteudo.trim() || conteudo === '<p></p>') {
                Swal.fire({
                    title: 'Erro!',
                    text: 'Por favor, adicione conteúdo ao modelo.',
                    icon: 'error'
                });
                return;
            }
            
            updateStatus('Salvando modelo...', 'saving');
            
            const data = {
                nome: nome,
                tipo: tipo,
                descricao: descricao,
                conteudo: conteudo,
                variaveis: extractVariables(conteudo)
            };
            
            // URL baseada se é criação ou edição
            const url = modeloExistente ? 
                `/admin/modelos/${modeloExistente.id}` : 
                '{{ route("modelos.store") }}';
            
            const method = modeloExistente ? 'PUT' : 'POST';
            
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na resposta do servidor');
                }
                return response.json();
            })
            .then(data => {
                hasUnsavedChanges = false;
                updateStatus('Modelo salvo com sucesso!', 'ready');
                
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'Modelo salvo com sucesso.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Redirecionar após salvar se for criação
                if (!modeloExistente) {
                    setTimeout(() => {
                        window.location.href = '{{ route("modelos.index") }}';
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Erro ao salvar:', error);
                updateStatus('Erro ao salvar modelo', 'error');
                
                Swal.fire({
                    title: 'Erro!',
                    text: 'Não foi possível salvar o modelo. Tente novamente.',
                    icon: 'error'
                });
            });
        }
        
        function extractVariables(content) {
            const matches = content.match(/\{\{([^}]+)\}\}/g) || [];
            return matches.map(match => match.replace(/[{}]/g, ''));
        }
        
        // Função para mobile - toggle sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }
        
        // Fechar sidebar no mobile ao clicar fora
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.querySelector('.menu-toggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !menuToggle.contains(e.target) && 
                sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });
    </script>
</body>
</html>