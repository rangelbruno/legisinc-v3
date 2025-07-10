<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Editor - {{ $projeto->titulo }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
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
        
        .editor-container {
            position: relative;
            background: white;
        }
        
        .legal-editor {
            min-height: 600px;
            border: none;
            padding: 20px;
            font-family: 'Times New Roman', serif;
            font-size: 14px;
            line-height: 1.8;
            background: white;
        }
        
        .legal-editor:focus {
            outline: none;
        }
        
        .status-bar {
            background: #f8f9fa;
            padding: 10px 20px;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: #666;
        }
        
        .save-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .save-status.success {
            background: #d4edda;
            color: #155724;
        }
        
        .save-status.error {
            background: #f8d7da;
            color: #721c24;
        }
        
        .save-status.saving {
            background: #fff3cd;
            color: #856404;
        }
        
        .back-button {
            background: #6c757d;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .back-button:hover {
            background: #5a6268;
        }
        
        .quick-actions {
            display: flex;
            gap: 10px;
        }
        
        .action-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .action-button:hover {
            background: #0056b3;
        }
        
        .action-button.secondary {
            background: #6c757d;
        }
        
        .action-button.secondary:hover {
            background: #5a6268;
        }
        
        /* Estilos específicos para documentos jurídicos */
        .legal-document {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .legal-document h1 {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        
        .legal-document h2 {
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        .legal-document p {
            margin-bottom: 10px;
            text-align: justify;
            text-indent: 2em;
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .status-bar {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
            
            .quick-actions {
                flex-wrap: wrap;
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
                    <strong>Número:</strong> {{ $projeto->numero ?? 'Em elaboração' }} | 
                    <strong>Tipo:</strong> {{ $projeto->tipo }} | 
                    <strong>Status:</strong> {{ $projeto->status }}
                </div>
            </div>
            <div class="quick-actions">
                <button class="action-button" onclick="insertLegalTemplate()">
                    📝 Template Jurídico
                </button>
                <button class="action-button secondary" onclick="showWordCount()">
                    📊 Estatísticas
                </button>
                <a href="{{ route('projetos.show', $projeto->id) }}" class="back-button">
                    ← Voltar
                </a>
            </div>
        </div>
        
        <div class="editor-container">
            <div id="legal-editor" class="legal-editor"></div>
        </div>
        
        <div class="status-bar">
            <div>
                <span id="editor-status">Carregando editor...</span>
                <span id="save-status" class="save-status"></span>
            </div>
            <div>
                <span id="word-count">0 palavras</span>
            </div>
        </div>
    </div>

    <script>
        let editorInstance = null;
        let hasUnsavedChanges = false;
        
        // Inicializar editor quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('legal-editor');
            
            // Configurar o editor
            editorInstance = window.LegalEditor.init(container, {
                content: {!! json_encode($projeto->conteudo ?? '<p>Inicie a redação do seu projeto aqui...</p>') !!},
                autoSave: true,
                saveCallback: async (content) => {
                    try {
                        const response = await fetch('{{ route("projetos.salvar-conteudo", $projeto->id) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ 
                                conteudo: content,
                                criar_versao: false 
                            })
                        });
                        
                        if (!response.ok) {
                            throw new Error('Erro na resposta do servidor');
                        }
                        
                        const data = await response.json();
                        hasUnsavedChanges = false;
                        return data;
                    } catch (error) {
                        console.error('Erro ao salvar:', error);
                        throw error;
                    }
                },
                onChange: (content) => {
                    hasUnsavedChanges = true;
                    updateStatus('Editando...', 'info');
                    updateWordCount();
                },
                onSave: (data) => {
                    updateStatus('Salvo automaticamente', 'success');
                },
                onError: (error) => {
                    updateStatus('Erro ao salvar', 'error');
                    console.error('Erro do editor:', error);
                }
            });
            
            // Configurar atalhos de teclado
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey || e.metaKey) {
                    switch(e.key) {
                        case 's':
                            e.preventDefault();
                            saveNow();
                            break;
                        case 'b':
                            e.preventDefault();
                            editorInstance.editor.commands.toggleBold();
                            break;
                        case 'i':
                            e.preventDefault();
                            editorInstance.editor.commands.toggleItalic();
                            break;
                        case 'u':
                            e.preventDefault();
                            editorInstance.editor.commands.toggleUnderline();
                            break;
                    }
                }
            });
            
            // Aviso antes de sair da página
            window.addEventListener('beforeunload', function(e) {
                if (hasUnsavedChanges) {
                    e.preventDefault();
                    e.returnValue = 'Você tem alterações não salvas. Tem certeza que deseja sair?';
                    return e.returnValue;
                }
            });
            
            updateStatus('Editor carregado e pronto para uso!', 'success');
            updateWordCount();
        });
        
        // Funções auxiliares
        function updateStatus(message, type = 'info') {
            const statusElement = document.getElementById('editor-status');
            const saveStatusElement = document.getElementById('save-status');
            
            statusElement.textContent = message;
            
            if (type === 'success' || type === 'error' || type === 'saving') {
                saveStatusElement.textContent = message;
                saveStatusElement.className = `save-status ${type}`;
                
                // Limpar status após 3 segundos
                setTimeout(() => {
                    saveStatusElement.textContent = '';
                    saveStatusElement.className = 'save-status';
                }, 3000);
            }
        }
        
        function updateWordCount() {
            const wordCountElement = document.getElementById('word-count');
            if (editorInstance && wordCountElement) {
                const words = editorInstance.getWordCount();
                const chars = editorInstance.getCharacterCount();
                wordCountElement.textContent = `${words} palavras, ${chars} caracteres`;
            }
        }
        
        function saveNow() {
            if (editorInstance) {
                updateStatus('Salvando...', 'saving');
                editorInstance.save();
            }
        }
        
        function insertLegalTemplate() {
            const templateOptions = [
                { value: 'bill', label: 'Projeto de Lei' },
                { value: 'contract', label: 'Contrato' },
                { value: 'petition', label: 'Petição' }
            ];
            
            let options = templateOptions.map(opt => 
                `<option value="${opt.value}">${opt.label}</option>`
            ).join('');
            
            const template = prompt(`Escolha um template:\n${templateOptions.map((opt, i) => `${i+1}. ${opt.label}`).join('\n')}\n\nDigite o número:`, '1');
            
            if (template) {
                const templateIndex = parseInt(template) - 1;
                if (templateIndex >= 0 && templateIndex < templateOptions.length) {
                    const selectedTemplate = templateOptions[templateIndex];
                    
                    // Dados padrão baseados no projeto
                    const templateData = {
                        numero: '{{ $projeto->numero ?? "XXXX" }}',
                        ano: new Date().getFullYear(),
                        tipo: '{{ $projeto->tipo }}',
                        titulo: '{{ $projeto->titulo }}',
                        ementa: '{{ $projeto->ementa ?? "Ementa do projeto" }}',
                        autor: '{{ $projeto->autor->nome ?? "Autor" }}',
                        objeto: 'estabelece normas sobre {{ strtolower($projeto->titulo) }}',
                        definicao_a: 'definição legal A',
                        definicao_b: 'definição legal B'
                    };
                    
                    editorInstance.insertTemplate(selectedTemplate.value, templateData);
                    updateStatus(`Template "${selectedTemplate.label}" inserido!`, 'success');
                }
            }
        }
        
        function showWordCount() {
            if (editorInstance) {
                const words = editorInstance.getWordCount();
                const chars = editorInstance.getCharacterCount();
                alert(`Estatísticas do documento:\n\n• Palavras: ${words}\n• Caracteres: ${chars}`);
            }
        }
        
        // Função para criar versão (pode ser chamada externamente)
        function createVersion() {
            if (editorInstance) {
                const description = prompt('Descrição da versão:');
                if (description) {
                    fetch('{{ route("projetos.salvar-conteudo", $projeto->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ 
                            conteudo: editorInstance.getContent(),
                            criar_versao: true,
                            descricao_versao: description
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        updateStatus('Versão criada com sucesso!', 'success');
                    })
                    .catch(error => {
                        updateStatus('Erro ao criar versão', 'error');
                        console.error('Erro:', error);
                    });
                }
            }
        }
    </script>
</body>
</html>