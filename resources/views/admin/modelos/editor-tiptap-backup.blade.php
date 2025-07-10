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
            width: 320px;
            background: white;
            border-right: 1px solid #e9ecef;
            padding: 20px;
            margin-top: 60px;
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
        
        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-top: 60px;
            background: white;
        }
        
        .editor-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .legal-editor {
            flex: 1;
            padding: 20px;
            border: none;
            font-family: 'Times New Roman', serif;
            font-size: 14px;
            line-height: 1.8;
            background: white;
            overflow-y: auto;
        }
        
        .legal-editor:focus {
            outline: none;
        }
        
        .page-container {
            min-height: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
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
            
            .main-content {
                width: 100%;
            }
        }
        
        /* Estilos específicos para documentos jurídicos */
        .legal-document {
            margin: 0 auto;
            padding: 20px;
            background: white;
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
            margin-bottom: 12px;
            text-align: justify;
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
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Editor de Modelo - {{ $tipos[$tipoSelecionado] ?? 'Novo Modelo' }}</h1>
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
        
        <!-- Sidebar -->
        <div class="sidebar">
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
                    <code>{{nome}}</code>
                    <span>Nome da parte</span>
                    <button onclick="insertVariableAtCursor('nome')">+</button>
                </div>
                <div class="variable-item">
                    <code>{{cpf}}</code>
                    <span>CPF</span>
                    <button onclick="insertVariableAtCursor('cpf')">+</button>
                </div>
                <div class="variable-item">
                    <code>{{cnpj}}</code>
                    <span>CNPJ</span>
                    <button onclick="insertVariableAtCursor('cnpj')">+</button>
                </div>
                <div class="variable-item">
                    <code>{{endereco}}</code>
                    <span>Endereço</span>
                    <button onclick="insertVariableAtCursor('endereco')">+</button>
                </div>
                <div class="variable-item">
                    <code>{{data}}</code>
                    <span>Data atual</span>
                    <button onclick="insertVariableAtCursor('data')">+</button>
                </div>
                <div class="variable-item">
                    <code>{{valor}}</code>
                    <span>Valor em reais</span>
                    <button onclick="insertVariableAtCursor('valor')">+</button>
                </div>
                <div class="variable-item">
                    <code>{{cidade}}</code>
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
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="editor-container">
                <div id="legal-editor" class="legal-editor">
                    <div class="page-container">
                        <!-- O conteúdo do editor será inserido aqui -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let editorInstance = null;
        let hasUnsavedChanges = false;
        
        // Dados do modelo existente (se editando)
        const modeloExistente = {!! json_encode($modelo) !!};
        
        // Inicializar editor quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('legal-editor');
            
            // Conteúdo inicial
            let initialContent = '<p>Inicie a criação do seu modelo jurídico aqui...</p>';
            if (modeloExistente && modeloExistente.conteudo) {
                initialContent = modeloExistente.conteudo;
            }
            
            // Configurar o editor
            editorInstance = window.LegalEditor.init(container, {
                content: initialContent,
                autoSave: false, // Desabilitar auto-save para modelos
                legalNumbering: true,
                templates: true,
                showToolbar: true,
                toolbarOptions: {
                    showTemplates: true,
                    showLegalLevels: true,
                    showFormatting: true,
                    showTable: true,
                    showExport: false // Não mostrar exportação em modelos
                },
                onChange: (content) => {
                    hasUnsavedChanges = true;
                    updateStatus('Editando...', 'editing');
                    updateStats();
                    highlightVariables();
                }
            });
            
            // Configurar atalhos de teclado
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey || e.metaKey) {
                    switch(e.key) {
                        case 's':
                            e.preventDefault();
                            saveModel();
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
            
            updateStatus('Editor carregado e pronto!', 'ready');
            updateStats();
            highlightVariables();
        });
        
        // Funções auxiliares
        function updateStatus(message, type = 'ready') {
            const statusDot = document.getElementById('status-dot');
            const statusText = document.getElementById('status-text');
            
            statusText.textContent = message;
            statusDot.className = `status-dot ${type}`;
            
            if (type === 'saving') {
                statusDot.classList.add('saving');
            } else if (type === 'error') {
                statusDot.classList.add('error');
            }
        }
        
        function updateStats() {
            if (!editorInstance) return;
            
            const wordCount = editorInstance.getWordCount();
            const charCount = editorInstance.getCharacterCount();
            const content = editorInstance.getContent();
            const varCount = (content.match(/\{\{[^}]+\}\}/g) || []).length;
            
            document.getElementById('word-count').textContent = wordCount;
            document.getElementById('char-count').textContent = charCount;
            document.getElementById('var-count').textContent = varCount;
        }
        
        function highlightVariables() {
            // Destacar variáveis no editor
            const content = editorInstance.getContent();
            const highlightedContent = content.replace(
                /\{\{([^}]+)\}\}/g,
                '<span class="variable-placeholder">{{$1}}</span>'
            );
            
            // Só atualizar se houve mudança para evitar loops
            if (content !== highlightedContent) {
                // Implementar highlight sem reescrever todo o conteúdo
                // Por simplicidade, vamos apenas contar as variáveis
            }
        }
        
        function insertQuickTemplate() {
            const tipo = document.getElementById('modelo-tipo').value;
            
            let templateData = {};
            let templateName = '';
            
            switch(tipo) {
                case 'contrato':
                    templateName = 'contract';
                    templateData = {
                        type: '{{tipo_contrato}}',
                        contractor: {
                            name: '{{contratante_nome}}',
                            cnpj: '{{contratante_cnpj}}',
                            address: '{{contratante_endereco}}'
                        },
                        contractee: {
                            name: '{{contratado_nome}}',
                            cpf: '{{contratado_cpf}}',
                            address: '{{contratado_endereco}}'
                        },
                        objeto: '{{objeto_contrato}}',
                        prazo: '{{prazo_contrato}}',
                        valor: '{{valor_contrato}}',
                        pagamento: '{{forma_pagamento}}',
                        cidade: '{{cidade}}'
                    };
                    break;
                    
                case 'peticao':
                    templateName = 'petition';
                    templateData = {
                        tipo_peticao: '{{tipo_peticao}}',
                        autoridade: '{{autoridade}}',
                        orgao: '{{orgao}}',
                        comarca: '{{comarca}}',
                        requerente: {
                            name: '{{requerente_nome}}',
                            qualificacao: '{{requerente_qualificacao}}',
                            cpf: '{{requerente_cpf}}',
                            endereco: '{{requerente_endereco}}'
                        },
                        fatos: '{{fatos}}',
                        fundamento_juridico: '{{fundamento_juridico}}',
                        pedido: '{{pedido}}',
                        advogado: {
                            nome: '{{advogado_nome}}',
                            estado: '{{advogado_estado}}',
                            numero: '{{advogado_oab}}'
                        },
                        cidade: '{{cidade}}'
                    };
                    break;
                    
                case 'projeto_lei':
                    templateName = 'bill';
                    templateData = {
                        numero: '{{numero_projeto}}',
                        ano: '{{ano_projeto}}',
                        ementa: '{{ementa}}',
                        objeto: '{{objeto_lei}}',
                        definicao_a: '{{definicao_a}}',
                        definicao_b: '{{definicao_b}}'
                    };
                    break;
                    
                default:
                    Swal.fire({
                        title: 'Template Básico',
                        text: 'Inserindo template básico para documento jurídico',
                        icon: 'info',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    editorInstance.setContent(`
                        <div class="legal-document">
                            <h1>{{titulo_documento}}</h1>
                            
                            <div class="legal-artigo">
                                <p>{{conteudo_artigo_1}}</p>
                            </div>
                            
                            <div class="legal-artigo">
                                <p>{{conteudo_artigo_2}}</p>
                            </div>
                            
                            <div class="legal-artigo">
                                <p>Este documento entra em vigor na data de {{data}}.</p>
                            </div>
                            
                            <div style="margin-top: 2rem;">
                                <p>{{cidade}}, {{data}}</p>
                            </div>
                        </div>
                    `);
                    return;
            }
            
            if (templateName) {
                editorInstance.insertTemplate(templateName, templateData);
                
                Swal.fire({
                    title: 'Template Inserido!',
                    text: `Template de ${tipo} foi inserido com sucesso.`,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        }
        
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
            const variableHtml = `<span class="variable-placeholder">{{${variableName}}}</span>`;
            editorInstance.editor.commands.insertContent(variableHtml);
            
            updateStats();
        }
        
        function saveModel() {
            if (!editorInstance) return;
            
            const nome = document.getElementById('modelo-nome').value;
            const tipo = document.getElementById('modelo-tipo').value;
            const descricao = document.getElementById('modelo-descricao').value;
            const conteudo = editorInstance.getContent();
            
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
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('open');
        }
        
        // Fechar sidebar no mobile ao clicar fora
        document.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.sidebar');
            const header = document.querySelector('.header');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !header.contains(e.target) && 
                sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });
    </script>
</body>
</html>