<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Editor - {{ $projeto->titulo }}</title>
    <meta name="description" content="Editor de conteúdo avançado para projetos de lei" />
    <meta name="keywords" content="editor, projeto, lei, legislativo" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="/assets/media/logos/favicon.ico" />
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    
    <!-- Global Stylesheets Bundle -->
    <link href="/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    
    <!-- TipTap Editor -->
    <script src="https://unpkg.com/@tiptap/core@2.1.0/dist/index.umd.js"></script>
    <script src="https://unpkg.com/@tiptap/starter-kit@2.1.0/dist/index.umd.js"></script>
    <script src="https://unpkg.com/@tiptap/extension-text-align@2.1.0/dist/index.umd.js"></script>
    <script src="https://unpkg.com/@tiptap/extension-underline@2.1.0/dist/index.umd.js"></script>
    <script src="https://unpkg.com/@tiptap/extension-table@2.1.0/dist/index.umd.js"></script>
    <script src="https://unpkg.com/@tiptap/extension-table-row@2.1.0/dist/index.umd.js"></script>
    <script src="https://unpkg.com/@tiptap/extension-table-cell@2.1.0/dist/index.umd.js"></script>
    <script src="https://unpkg.com/@tiptap/extension-table-header@2.1.0/dist/index.umd.js"></script>
    
    <style>
        body {
            margin: 0;
            font-family: Inter, sans-serif;
            background: #f8f9fa;
        }
        
        .editor-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .editor-header {
            background: white;
            padding: 15px 20px;
            border-bottom: 1px solid #e5e5e5;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .editor-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e1e2d;
            margin: 0;
        }
        
        .editor-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .editor-toolbar {
            background: white;
            padding: 10px 20px;
            border-bottom: 1px solid #e5e5e5;
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
            position: sticky;
            top: 65px;
            z-index: 999;
        }
        
        .toolbar-btn {
            padding: 8px 12px;
            border: 1px solid #e5e5e5;
            background: white;
            cursor: pointer;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .toolbar-btn:hover {
            background: #f5f5f5;
        }
        
        .toolbar-btn.active {
            background: #009ef7;
            color: white;
            border-color: #009ef7;
        }
        
        .toolbar-separator {
            width: 1px;
            height: 30px;
            background: #e5e5e5;
            margin: 0 5px;
        }
        
        .editor-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
        
        .editor-wrapper {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            min-height: calc(100vh - 200px);
        }
        
        .ProseMirror {
            padding: 40px;
            outline: none;
            line-height: 1.6;
            font-size: 16px;
        }
        
        .ProseMirror h1, .ProseMirror h2, .ProseMirror h3 {
            margin-top: 30px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .ProseMirror p {
            margin-bottom: 15px;
        }
        
        .ProseMirror ul, .ProseMirror ol {
            margin: 15px 0;
            padding-left: 30px;
        }
        
        .ProseMirror table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
        }
        
        .ProseMirror table td, .ProseMirror table th {
            border: 1px solid #ddd;
            padding: 8px 12px;
        }
        
        .ProseMirror table th {
            background: #f5f5f5;
            font-weight: 600;
        }
        
        .status-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #28a745;
        }
        
        .save-indicator {
            background: #ffc107;
        }
        
        .error-indicator {
            background: #dc3545;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: #009ef7;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0084d4;
        }
        
        .btn-light {
            background: #f5f5f5;
            color: #333;
        }
        
        .btn-light:hover {
            background: #e5e5e5;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 500px;
            max-width: 90%;
        }
        
        .modal h3 {
            margin-top: 0;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #009ef7;
        }
        
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="editor-container">
        <!-- Header -->
        <div class="editor-header">
            <div>
                <h1 class="editor-title">{{ $projeto->titulo }}</h1>
                <div style="font-size: 14px; color: #666; margin-top: 5px;">
                    {{ $projeto->numero_completo }} - Versão {{ $projeto->version_atual }}
                </div>
            </div>
            
            <div class="editor-actions">
                <div class="status-indicator" id="saveStatus">
                    <div class="status-dot"></div>
                    <span>Salvo</span>
                </div>
                
                <button type="button" class="btn btn-light" onclick="showVersionModal()">
                    Salvar Versão
                </button>
                
                <button type="button" class="btn btn-success" onclick="saveContent()">
                    Salvar
                </button>
                
                <a href="{{ route('projetos.show', $projeto->id) }}" class="btn btn-light" target="_blank">
                    Ver Projeto
                </a>
            </div>
        </div>
        
        <!-- Toolbar -->
        <div class="editor-toolbar" id="toolbar">
            <button class="toolbar-btn" onclick="editor.chain().focus().toggleBold().run()" data-action="bold">
                <strong>B</strong>
            </button>
            <button class="toolbar-btn" onclick="editor.chain().focus().toggleItalic().run()" data-action="italic">
                <em>I</em>
            </button>
            <button class="toolbar-btn" onclick="editor.chain().focus().toggleUnderline().run()" data-action="underline">
                <u>U</u>
            </button>
            
            <div class="toolbar-separator"></div>
            
            <button class="toolbar-btn" onclick="editor.chain().focus().toggleHeading({ level: 1 }).run()" data-action="h1">
                H1
            </button>
            <button class="toolbar-btn" onclick="editor.chain().focus().toggleHeading({ level: 2 }).run()" data-action="h2">
                H2
            </button>
            <button class="toolbar-btn" onclick="editor.chain().focus().toggleHeading({ level: 3 }).run()" data-action="h3">
                H3
            </button>
            
            <div class="toolbar-separator"></div>
            
            <button class="toolbar-btn" onclick="editor.chain().focus().setTextAlign('left').run()" data-action="align-left">
                ←
            </button>
            <button class="toolbar-btn" onclick="editor.chain().focus().setTextAlign('center').run()" data-action="align-center">
                ↔
            </button>
            <button class="toolbar-btn" onclick="editor.chain().focus().setTextAlign('right').run()" data-action="align-right">
                →
            </button>
            
            <div class="toolbar-separator"></div>
            
            <button class="toolbar-btn" onclick="editor.chain().focus().toggleBulletList().run()" data-action="bullet-list">
                •
            </button>
            <button class="toolbar-btn" onclick="editor.chain().focus().toggleOrderedList().run()" data-action="ordered-list">
                1.
            </button>
            
            <div class="toolbar-separator"></div>
            
            <button class="toolbar-btn" onclick="insertTable()">
                Tabela
            </button>
            
            <button class="toolbar-btn" onclick="editor.chain().focus().setHorizontalRule().run()">
                HR
            </button>
        </div>
        
        <!-- Editor Content -->
        <div class="editor-content">
            <div class="editor-wrapper">
                <div id="editor"></div>
            </div>
        </div>
    </div>
    
    <!-- Modal Salvar Versão -->
    <div id="versionModal" class="modal">
        <div class="modal-content">
            <h3>Salvar Nova Versão</h3>
            <form id="versionForm">
                <div class="form-group">
                    <label for="changelog">Descrição das alterações:</label>
                    <textarea id="changelog" class="form-control" rows="3" placeholder="Descreva as principais alterações desta versão..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="tipoAlteracao">Tipo de alteração:</label>
                    <select id="tipoAlteracao" class="form-control">
                        <option value="revisao">Revisão</option>
                        <option value="emenda">Emenda</option>
                        <option value="correcao">Correção</option>
                        <option value="formatacao">Formatação</option>
                    </select>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-light" onclick="closeVersionModal()">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar Versão</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Inicializar TipTap Editor
        let editor;
        let saveTimeout;
        
        const { Editor } = tiptap.core;
        const { StarterKit } = tiptap.starterKit;
        const { TextAlign } = tiptap.textAlign;
        const { Underline } = tiptap.underline;
        const { Table } = tiptap.table;
        const { TableRow } = tiptap.tableRow;
        const { TableCell } = tiptap.tableCell;
        const { TableHeader } = tiptap.tableHeader;

        document.addEventListener('DOMContentLoaded', function() {
            editor = new Editor({
                element: document.querySelector('#editor'),
                extensions: [
                    StarterKit,
                    Underline,
                    TextAlign.configure({
                        types: ['heading', 'paragraph'],
                    }),
                    Table.configure({
                        resizable: true,
                    }),
                    TableRow,
                    TableHeader,
                    TableCell,
                ],
                content: {!! json_encode($projeto->conteudo ?? '') !!},
                onUpdate: function({ editor }) {
                    // Auto-save após 2 segundos de inatividade
                    clearTimeout(saveTimeout);
                    updateSaveStatus('saving');
                    
                    saveTimeout = setTimeout(() => {
                        autoSave();
                    }, 2000);
                },
                onSelectionUpdate: function({ editor }) {
                    updateToolbarState();
                }
            });
            
            updateToolbarState();
            
            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey || e.metaKey) {
                    if (e.key === 's') {
                        e.preventDefault();
                        saveContent();
                    }
                }
            });
        });
        
        function updateToolbarState() {
            const toolbar = document.getElementById('toolbar');
            const buttons = toolbar.querySelectorAll('.toolbar-btn');
            
            buttons.forEach(button => {
                button.classList.remove('active');
                const action = button.getAttribute('data-action');
                
                if (action === 'bold' && editor.isActive('bold')) {
                    button.classList.add('active');
                }
                if (action === 'italic' && editor.isActive('italic')) {
                    button.classList.add('active');
                }
                if (action === 'underline' && editor.isActive('underline')) {
                    button.classList.add('active');
                }
                if (action === 'h1' && editor.isActive('heading', { level: 1 })) {
                    button.classList.add('active');
                }
                if (action === 'h2' && editor.isActive('heading', { level: 2 })) {
                    button.classList.add('active');
                }
                if (action === 'h3' && editor.isActive('heading', { level: 3 })) {
                    button.classList.add('active');
                }
                if (action === 'bullet-list' && editor.isActive('bulletList')) {
                    button.classList.add('active');
                }
                if (action === 'ordered-list' && editor.isActive('orderedList')) {
                    button.classList.add('active');
                }
            });
        }
        
        function updateSaveStatus(status) {
            const statusEl = document.getElementById('saveStatus');
            const dot = statusEl.querySelector('.status-dot');
            const text = statusEl.querySelector('span');
            
            dot.className = 'status-dot';
            
            switch(status) {
                case 'saving':
                    dot.classList.add('save-indicator');
                    text.textContent = 'Salvando...';
                    break;
                case 'saved':
                    text.textContent = 'Salvo';
                    break;
                case 'error':
                    dot.classList.add('error-indicator');
                    text.textContent = 'Erro ao salvar';
                    break;
            }
        }
        
        function autoSave() {
            const content = editor.getHTML();
            
            fetch(`/projetos/{{ $projeto->id }}/salvar-conteudo`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    conteudo: content,
                    changelog: 'Auto-save',
                    tipo_alteracao: 'revisao'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateSaveStatus('saved');
                } else {
                    updateSaveStatus('error');
                    console.error('Erro ao salvar:', data.message);
                }
            })
            .catch(error => {
                updateSaveStatus('error');
                console.error('Erro na requisição:', error);
            });
        }
        
        function saveContent() {
            const content = editor.getHTML();
            updateSaveStatus('saving');
            
            fetch(`/projetos/{{ $projeto->id }}/salvar-conteudo`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    conteudo: content,
                    changelog: 'Salvamento manual',
                    tipo_alteracao: 'revisao'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateSaveStatus('saved');
                    alert('Conteúdo salvo com sucesso!');
                } else {
                    updateSaveStatus('error');
                    alert('Erro ao salvar: ' + data.message);
                }
            })
            .catch(error => {
                updateSaveStatus('error');
                alert('Erro na conexão. Tente novamente.');
                console.error('Erro:', error);
            });
        }
        
        function insertTable() {
            editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run();
        }
        
        function showVersionModal() {
            document.getElementById('versionModal').style.display = 'block';
            document.getElementById('changelog').focus();
        }
        
        function closeVersionModal() {
            document.getElementById('versionModal').style.display = 'none';
            document.getElementById('changelog').value = '';
            document.getElementById('tipoAlteracao').value = 'revisao';
        }
        
        document.getElementById('versionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const content = editor.getHTML();
            const changelog = document.getElementById('changelog').value;
            const tipoAlteracao = document.getElementById('tipoAlteracao').value;
            
            if (!changelog.trim()) {
                alert('Por favor, descreva as alterações desta versão.');
                return;
            }
            
            updateSaveStatus('saving');
            
            fetch(`/projetos/{{ $projeto->id }}/salvar-conteudo`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    conteudo: content,
                    changelog: changelog,
                    tipo_alteracao: tipoAlteracao
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateSaveStatus('saved');
                    closeVersionModal();
                    alert(`Nova versão ${data.version} criada com sucesso!`);
                } else {
                    updateSaveStatus('error');
                    alert('Erro ao criar versão: ' + data.message);
                }
            })
            .catch(error => {
                updateSaveStatus('error');
                alert('Erro na conexão. Tente novamente.');
                console.error('Erro:', error);
            });
        });
        
        // Fechar modal clicando fora
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('versionModal');
            if (e.target === modal) {
                closeVersionModal();
            }
        });
        
        // Aviso antes de sair da página
        window.addEventListener('beforeunload', function(e) {
            // Se houver mudanças não salvas, mostrar aviso
            const statusText = document.querySelector('#saveStatus span').textContent;
            if (statusText === 'Salvando...') {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    </script>
</body>
</html>