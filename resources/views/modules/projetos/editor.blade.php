<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Editor - {{ $projeto->titulo }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
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
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }
        
        .header .meta {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
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
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
        }
        
        .toolbar {
            padding: 15px 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .toolbar-group {
            display: flex;
            gap: 4px;
        }
        
        .toolbar-group:not(:last-child) {
            margin-right: 15px;
            padding-right: 15px;
            border-right: 1px solid #dee2e6;
        }
        
        .toolbar-btn {
            padding: 6px 10px;
            border: 1px solid #dee2e6;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
            min-width: 32px;
            text-align: center;
        }
        
        .toolbar-btn:hover {
            background: #e9ecef;
        }
        
        .toolbar-btn.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .editor-area {
            position: relative;
            min-height: 600px;
        }
        
        .editor {
            padding: 30px;
            min-height: 600px;
            outline: none;
            font-size: 16px;
            line-height: 1.6;
            cursor: text;
            border: none;
            resize: none;
            width: 100%;
            box-sizing: border-box;
        }
        
        .editor:focus {
            outline: none;
        }
        
        .editor::placeholder {
            color: #aaa;
        }
        
        .editor h1 {
            font-size: 2em;
            margin: 1em 0 0.5em 0;
            font-weight: 600;
        }
        
        .editor h2 {
            font-size: 1.5em;
            margin: 1em 0 0.5em 0;
            font-weight: 600;
        }
        
        .editor h3 {
            font-size: 1.25em;
            margin: 1em 0 0.5em 0;
            font-weight: 600;
        }
        
        .editor p {
            margin: 1em 0;
        }
        
        .editor ul, .editor ol {
            margin: 1em 0;
            padding-left: 2em;
        }
        
        .editor li {
            margin: 0.5em 0;
        }
        
        .editor blockquote {
            margin: 1em 0;
            padding-left: 1em;
            border-left: 4px solid #007bff;
            color: #666;
            font-style: italic;
        }
        
        .editor hr {
            margin: 2em 0;
            border: none;
            border-top: 2px solid #dee2e6;
        }
        
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 4px;
            color: white;
            font-size: 14px;
            z-index: 1000;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s;
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
        
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .toolbar {
                gap: 5px;
            }
            
            .toolbar-group {
                margin-right: 10px;
                padding-right: 10px;
            }
            
            .editor {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>{{ $projeto->titulo }}</h1>
                <div class="meta">
                    {{ $projeto->numero_completo }} • Versão {{ $projeto->version_atual }}
                </div>
            </div>
            
            <div class="actions">
                <div class="status">
                    <div class="status-dot" id="statusDot"></div>
                    <span id="statusText">Pronto</span>
                </div>
                
                <button class="btn btn-secondary" onclick="saveContent()">
                    💾 Salvar
                </button>
                
                <a href="{{ route('projetos.show', $projeto->id) }}" class="btn btn-primary" target="_blank">
                    👁️ Ver Projeto
                </a>
            </div>
        </div>
        
        <div class="toolbar">
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
                <button class="toolbar-btn" onclick="formatHeading(1)" title="Título 1">
                    H1
                </button>
                <button class="toolbar-btn" onclick="formatHeading(2)" title="Título 2">
                    H2
                </button>
                <button class="toolbar-btn" onclick="formatHeading(3)" title="Título 3">
                    H3
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
                <button class="toolbar-btn" onclick="insertHR()" title="Linha horizontal">
                    ➖ HR
                </button>
            </div>
            
            <div class="toolbar-group">
                <button class="toolbar-btn" onclick="formatText('undo')" title="Desfazer (Ctrl+Z)">
                    ↶
                </button>
                <button class="toolbar-btn" onclick="formatText('redo')" title="Refazer (Ctrl+Y)">
                    ↷
                </button>
            </div>
        </div>
        
        <div class="editor-area">
            <div 
                id="editor" 
                class="editor" 
                contenteditable="true" 
                placeholder="Comece a escrever o conteúdo do projeto aqui..."
                oninput="handleInput()"
                onkeydown="handleKeydown(event)"
            >
                {!! $projeto->conteudo ?? '<p>Comece a escrever o conteúdo do projeto aqui...</p>' !!}
            </div>
        </div>
    </div>
    
    <div id="toast" class="toast"></div>

    <script>
        let saveTimeout;
        let editor = document.getElementById('editor');
        
        // Foco inicial no editor
        window.addEventListener('load', function() {
            editor.focus();
        });
        
        // Formatação de texto
        function formatText(command) {
            document.execCommand(command, false, null);
            editor.focus();
            updateToolbar();
        }
        
        // Formatação de cabeçalhos
        function formatHeading(level) {
            document.execCommand('formatBlock', false, 'h' + level);
            editor.focus();
            updateToolbar();
        }
        
        // Inserir linha horizontal
        function insertHR() {
            document.execCommand('insertHTML', false, '<hr>');
            editor.focus();
        }
        
        // Atualizar toolbar
        function updateToolbar() {
            const buttons = document.querySelectorAll('.toolbar-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            
            if (document.queryCommandState('bold')) {
                document.querySelector('[onclick="formatText(\'bold\')"]').classList.add('active');
            }
            if (document.queryCommandState('italic')) {
                document.querySelector('[onclick="formatText(\'italic\')"]').classList.add('active');
            }
            if (document.queryCommandState('underline')) {
                document.querySelector('[onclick="formatText(\'underline\')"]').classList.add('active');
            }
        }
        
        // Manipular entrada
        function handleInput() {
            clearTimeout(saveTimeout);
            updateStatus('saving');
            
            saveTimeout = setTimeout(() => {
                autoSave();
            }, 2000);
        }
        
        // Manipular teclas
        function handleKeydown(event) {
            if (event.ctrlKey || event.metaKey) {
                switch(event.key) {
                    case 's':
                        event.preventDefault();
                        saveContent();
                        break;
                    case 'b':
                        event.preventDefault();
                        formatText('bold');
                        break;
                    case 'i':
                        event.preventDefault();
                        formatText('italic');
                        break;
                    case 'u':
                        event.preventDefault();
                        formatText('underline');
                        break;
                    case 'z':
                        event.preventDefault();
                        formatText('undo');
                        break;
                    case 'y':
                        event.preventDefault();
                        formatText('redo');
                        break;
                }
            }
        }
        
        // Atualizar status
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
        
        // Mostrar toast
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type}`;
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
        
        // Auto-save
        function autoSave() {
            const content = editor.innerHTML;
            
            fetch(`/projetos/{{ $projeto->id }}/salvar-conteudo`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    conteudo: content,
                    changelog: 'Salvamento automático',
                    tipo_alteracao: 'revisao'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateStatus('saved');
                    setTimeout(() => updateStatus('ready'), 2000);
                } else {
                    updateStatus('error');
                }
            })
            .catch(error => {
                updateStatus('error');
                console.error('Erro:', error);
            });
        }
        
        // Salvar conteúdo
        function saveContent() {
            const content = editor.innerHTML;
            
            if (!content || content.trim() === '<p>Comece a escrever o conteúdo do projeto aqui...</p>') {
                showToast('Adicione algum conteúdo antes de salvar', 'error');
                return;
            }
            
            updateStatus('saving');
            
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
                    updateStatus('saved');
                    showToast('Conteúdo salvo com sucesso!');
                    setTimeout(() => updateStatus('ready'), 2000);
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
        
        // Aviso antes de sair
        window.addEventListener('beforeunload', function(e) {
            const statusText = document.getElementById('statusText').textContent;
            if (statusText === 'Salvando...') {
                e.preventDefault();
                e.returnValue = '';
            }
        });
        
        // Atualizar toolbar ao selecionar texto
        document.addEventListener('selectionchange', updateToolbar);
    </script>
</body>
</html>