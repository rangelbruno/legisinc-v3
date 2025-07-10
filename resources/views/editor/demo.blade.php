<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Demo - Editor Jurídico</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .legal-editor-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .legal-editor {
            min-height: 500px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            background: white;
            font-family: 'Times New Roman', serif;
            line-height: 1.6;
        }
        
        .legal-editor:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .demo-controls {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .demo-button {
            padding: 8px 16px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .demo-button:hover {
            background: #2563eb;
        }
        
        .demo-button.secondary {
            background: #6b7280;
        }
        
        .demo-button.secondary:hover {
            background: #4b5563;
        }
        
        .status-bar {
            margin-top: 10px;
            padding: 10px;
            background: #f3f4f6;
            border-radius: 4px;
            font-size: 14px;
            color: #6b7280;
        }
        
        .template-selector {
            margin-bottom: 20px;
        }
        
        .template-selector select {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            background: white;
        }
        
        .export-options {
            margin-top: 20px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
        }
        
        .export-options h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            font-weight: 600;
        }
        
        .save-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .save-status.success {
            background: #dcfce7;
            color: #166534;
        }
        
        .save-status.error {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .save-status.saving {
            background: #fef3c7;
            color: #d97706;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="legal-editor-container">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Editor Jurídico - Demo</h1>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <!-- Controles de demonstração -->
            <div class="demo-controls">
                <button class="demo-button" onclick="insertContract()">
                    📝 Inserir Contrato
                </button>
                <button class="demo-button" onclick="insertPetition()">
                    📋 Inserir Petição
                </button>
                <button class="demo-button" onclick="insertBill()">
                    📜 Inserir Projeto de Lei
                </button>
                <button class="demo-button secondary" onclick="clearEditor()">
                    🗑️ Limpar
                </button>
                <button class="demo-button secondary" onclick="showContent()">
                    👁️ Ver HTML
                </button>
            </div>
            
            <!-- Seletor de template -->
            <div class="template-selector">
                <label for="templateSelect" class="block text-sm font-medium text-gray-700 mb-2">
                    Template rápido:
                </label>
                <select id="templateSelect" onchange="loadTemplate(this.value)">
                    <option value="">Selecione um template...</option>
                    <option value="contract">Contrato de Prestação de Serviços</option>
                    <option value="petition">Petição Inicial</option>
                    <option value="bill">Projeto de Lei</option>
                </select>
            </div>
            
            <!-- Container do editor -->
            <div id="legal-editor" class="legal-editor"></div>
            
            <!-- Barra de status -->
            <div class="status-bar">
                <span id="editor-status">Editor carregado. Comece a digitar...</span>
                <span id="save-status" class="save-status"></span>
            </div>
            
            <!-- Opções de exportação -->
            <div class="export-options">
                <h3>Exportar Documento:</h3>
                <div class="flex gap-2">
                    <button class="demo-button" onclick="exportHTML()">
                        📄 HTML
                    </button>
                    <button class="demo-button" onclick="exportJSON()">
                        🔧 JSON
                    </button>
                    <button class="demo-button" onclick="showStats()">
                        📊 Estatísticas
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Variável global para o editor
        let editorInstance = null;
        
        // Inicializar editor quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('legal-editor');
            
            // Configurar o editor
            editorInstance = window.LegalEditor.init(container, {
                content: '<p>Bem-vindo ao Editor Jurídico! Comece digitando ou use os templates acima.</p>',
                autoSave: true,
                saveCallback: async (content) => {
                    // Simular salvamento
                    await new Promise(resolve => setTimeout(resolve, 1000));
                    console.log('Conteúdo salvo:', content);
                    return { success: true };
                },
                onChange: (content) => {
                    updateStatus('Editando...');
                    // Atualizar estatísticas em tempo real
                    updateStats();
                },
                onSave: (data) => {
                    updateStatus('Documento salvo com sucesso!');
                },
                onError: (error) => {
                    updateStatus('Erro ao salvar: ' + error.message, 'error');
                }
            });
            
            // Configurar atalhos de teclado
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey || e.metaKey) {
                    switch(e.key) {
                        case 's':
                            e.preventDefault();
                            editorInstance.save();
                            break;
                        case 'z':
                            if (e.shiftKey) {
                                e.preventDefault();
                                editorInstance.redo();
                            }
                            break;
                    }
                }
            });
            
            updateStatus('Editor carregado e pronto para uso!');
        });
        
        // Funções de demonstração
        function insertContract() {
            const contractData = {
                type: 'PRESTAÇÃO DE SERVIÇOS',
                contractor: {
                    name: 'Empresa ABC Ltda',
                    cnpj: '12.345.678/0001-95',
                    address: 'Rua das Flores, 123, São Paulo/SP'
                },
                contractee: {
                    name: 'João Silva',
                    cpf: '123.456.789-01',
                    address: 'Av. Paulista, 1000, São Paulo/SP'
                },
                objeto: 'Prestação de serviços de consultoria jurídica',
                prazo: 'O prazo de vigência deste contrato é de 12 (doze) meses',
                valor: 'R$ 5.000,00',
                pagamento: 'O pagamento será efetuado mensalmente até o dia 5 de cada mês',
                cidade: 'São Paulo'
            };
            
            editorInstance.insertTemplate('contract', contractData);
            updateStatus('Template de contrato inserido!');
        }
        
        function insertPetition() {
            const petitionData = {
                tipo_peticao: 'PETIÇÃO INICIAL',
                autoridade: 'Juiz de Direito',
                orgao: 'Vara Cível da Comarca de São Paulo',
                comarca: 'São Paulo/SP',
                requerente: {
                    name: 'Maria Santos',
                    qualificacao: 'brasileira, casada, empresária',
                    cpf: '987.654.321-00',
                    endereco: 'na Rua Augusta, 500, São Paulo/SP'
                },
                fatos: 'Expõe a requerente que celebrou contrato de prestação de serviços com a empresa ré, conforme documento anexo.',
                fundamento_juridico: 'O pedido encontra amparo no artigo 927 do Código Civil e na legislação consumerista aplicável.',
                pedido: 'a) A condenação da ré ao pagamento de indenização por danos morais no valor de R$ 10.000,00; b) A condenação da ré ao pagamento das custas processuais e honorários advocatícios.',
                advogado: {
                    nome: 'Dr. Carlos Oliveira',
                    estado: 'SP',
                    numero: '123456'
                },
                cidade: 'São Paulo'
            };
            
            editorInstance.insertTemplate('petition', petitionData);
            updateStatus('Template de petição inserido!');
        }
        
        function insertBill() {
            const billData = {
                numero: '1234',
                ano: '2024',
                ementa: 'Dispõe sobre a proteção de dados pessoais no âmbito da Administração Pública.',
                objeto: 'estabelece normas gerais sobre a proteção de dados pessoais',
                definicao_a: 'dados pessoais: informação relacionada a pessoa natural identificada ou identificável',
                definicao_b: 'tratamento: toda operação realizada com dados pessoais'
            };
            
            editorInstance.insertTemplate('bill', billData);
            updateStatus('Template de projeto de lei inserido!');
        }
        
        function clearEditor() {
            editorInstance.setContent('<p>Editor limpo. Comece a digitar...</p>');
            updateStatus('Editor limpo!');
        }
        
        function showContent() {
            const content = editorInstance.getContent();
            alert('Conteúdo HTML:\n\n' + content);
        }
        
        function loadTemplate(templateName) {
            if (!templateName) return;
            
            switch(templateName) {
                case 'contract':
                    insertContract();
                    break;
                case 'petition':
                    insertPetition();
                    break;
                case 'bill':
                    insertBill();
                    break;
            }
            
            // Resetar select
            document.getElementById('templateSelect').value = '';
        }
        
        function exportHTML() {
            const content = editorInstance.exportHTML();
            const blob = new Blob([content], { type: 'text/html' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'documento_juridico.html';
            a.click();
            URL.revokeObjectURL(url);
            updateStatus('HTML exportado!');
        }
        
        function exportJSON() {
            const content = editorInstance.exportJSON();
            const blob = new Blob([content], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'documento_juridico.json';
            a.click();
            URL.revokeObjectURL(url);
            updateStatus('JSON exportado!');
        }
        
        function showStats() {
            const words = editorInstance.getWordCount();
            const chars = editorInstance.getCharacterCount();
            alert(`Estatísticas do documento:\n\n• Palavras: ${words}\n• Caracteres: ${chars}`);
        }
        
        function updateStatus(message, type = 'info') {
            const statusElement = document.getElementById('editor-status');
            statusElement.textContent = message;
            statusElement.className = `status-${type}`;
        }
        
        function updateStats() {
            // Atualizar contador de palavras na toolbar se disponível
            const wordCount = editorInstance.getWordCount();
            const charCount = editorInstance.getCharacterCount();
            
            const wordCountElement = document.getElementById('word-count');
            if (wordCountElement) {
                wordCountElement.textContent = `${wordCount} palavras, ${charCount} caracteres`;
            }
        }
    </script>
</body>
</html>