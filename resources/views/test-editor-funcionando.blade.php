<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>✅ Teste Editor TipTap - Funcionando</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        
        .header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 700;
        }
        
        .header p {
            margin: 10px 0 0;
            font-size: 1.2em;
            opacity: 0.9;
        }
        
        .content {
            padding: 30px;
        }
        
        .status {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
        }
        
        .buttons {
            display: flex;
            gap: 10px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        
        button {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            color: white;
        }
        
        .btn-artigo { background: #007bff; }
        .btn-paragrafo { background: #28a745; }
        .btn-inciso { background: #ffc107; color: #333; }
        .btn-alinea { background: #17a2b8; }
        .btn-item { background: #6c757d; }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .editor-container {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            min-height: 400px;
            padding: 20px;
            background: #fafafa;
            margin: 20px 0;
        }
        
        .success {
            color: #28a745;
            font-weight: bold;
        }
        
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        
        .info {
            color: #007bff;
            font-weight: bold;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            color: #666;
        }
        
        .back-link {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 20px;
        }
        
        .back-link:hover {
            background: #5a6268;
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Editor TipTap</h1>
            <p>Teste de Funcionalidade - Numeração Jurídica</p>
        </div>
        
        <div class="content">
            <div class="status">
                <strong>Status:</strong> <span id="status">Inicializando...</span>
            </div>
            
            <div class="buttons">
                <button class="btn-artigo" onclick="testArtigo()">📝 Inserir Artigo</button>
                <button class="btn-paragrafo" onclick="testParagrafo()">📋 Inserir Parágrafo</button>
                <button class="btn-inciso" onclick="testInciso()">📑 Inserir Inciso</button>
                <button class="btn-alinea" onclick="testAlinea()">📄 Inserir Alínea</button>
                <button class="btn-item" onclick="testItem()">🔸 Inserir Item</button>
            </div>
            
            <div id="legal-editor" class="editor-container">
                <!-- Editor será inicializado aqui -->
            </div>
            
            <div class="footer">
                <strong>Instruções:</strong> O editor deve carregar automaticamente. Use os botões acima para testar a numeração jurídica.
                <br>
                <a href="/admin/modelos/editor" class="back-link">🔙 Voltar ao Editor Principal</a>
            </div>
        </div>
    </div>

    <script>
        let editorInstance = null;

        function updateStatus(message, type = 'info') {
            const statusElement = document.getElementById('status');
            statusElement.textContent = message;
            statusElement.className = type;
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateStatus('Verificando dependências...', 'info');
            
            // Verificar se o LegalEditor está disponível
            if (typeof window.LegalEditor === 'undefined') {
                updateStatus('❌ ERRO: LegalEditor não encontrado', 'error');
                return;
            }
            
            updateStatus('Inicializando editor...', 'info');
            
            try {
                const container = document.getElementById('legal-editor');
                
                editorInstance = window.LegalEditor.init(container, {
                    content: '<p>🎉 <strong>Editor TipTap funcionando perfeitamente!</strong></p><p>Teste os botões acima para inserir elementos jurídicos com numeração automática.</p>',
                    legalNumbering: true,
                    templates: false,
                    autoSave: false,
                    onChange: (content) => {
                        updateStatus('✅ Editor ativo - ' + content.length + ' caracteres', 'success');
                    }
                });
                
                updateStatus('✅ Editor inicializado com sucesso!', 'success');
                
            } catch (error) {
                updateStatus('❌ ERRO: ' + error.message, 'error');
                console.error('Erro ao inicializar editor:', error);
            }
        });

        function testArtigo() {
            if (editorInstance && editorInstance.editor) {
                editorInstance.editor.commands.setLegalLevel('artigo');
                updateStatus('✅ Artigo inserido com sucesso!', 'success');
            } else {
                updateStatus('❌ Editor não inicializado', 'error');
            }
        }

        function testParagrafo() {
            if (editorInstance && editorInstance.editor) {
                editorInstance.editor.commands.setLegalLevel('paragrafo');
                updateStatus('✅ Parágrafo inserido com sucesso!', 'success');
            } else {
                updateStatus('❌ Editor não inicializado', 'error');
            }
        }

        function testInciso() {
            if (editorInstance && editorInstance.editor) {
                editorInstance.editor.commands.setLegalLevel('inciso');
                updateStatus('✅ Inciso inserido com sucesso!', 'success');
            } else {
                updateStatus('❌ Editor não inicializado', 'error');
            }
        }

        function testAlinea() {
            if (editorInstance && editorInstance.editor) {
                editorInstance.editor.commands.setLegalLevel('alinea');
                updateStatus('✅ Alínea inserida com sucesso!', 'success');
            } else {
                updateStatus('❌ Editor não inicializado', 'error');
            }
        }

        function testItem() {
            if (editorInstance && editorInstance.editor) {
                editorInstance.editor.commands.setLegalLevel('item');
                updateStatus('✅ Item inserido com sucesso!', 'success');
            } else {
                updateStatus('❌ Editor não inicializado', 'error');
            }
        }
    </script>
</body>
</html> 