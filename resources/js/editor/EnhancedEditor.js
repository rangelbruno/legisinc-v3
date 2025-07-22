/**
 * Editor Jurídico Avançado
 * Funcionalidades modernas para criação de documentos legislativos
 */

class EnhancedEditor {
    constructor(container, options = {}) {
        this.container = container;
        this.options = {
            autoSave: true,
            collaboration: false,
            templates: true,
            legalNumbering: true,
            spellCheck: true,
            ...options
        };
        
        this.currentDocument = {
            id: null,
            title: '',
            content: '',
            version: 1,
            lastSaved: null
        };
        
        this.collaborators = new Map();
        this.templates = new Map();
        this.undoStack = [];
        this.redoStack = [];
        
        this.init();
    }
    
    init() {
        this.setupEditor();
        this.loadTemplates();
        this.setupEventListeners();
        this.setupAutoSave();
        this.setupCollaboration();
        this.setupLegalNumbering();
    }
    
    setupEditor() {
        this.editor = document.getElementById('document-canvas');
        this.titleInput = document.getElementById('document_title');
        
        // Configurar editor
        this.editor.contentEditable = true;
        this.editor.spellcheck = this.options.spellCheck;
        
        // Adicionar classes CSS
        this.editor.classList.add('enhanced-editor');
        
        console.log('Editor inicializado com sucesso');
    }
    
    setupEventListeners() {
        // Eventos de teclado
        this.editor.addEventListener('keydown', (e) => this.handleKeyDown(e));
        this.editor.addEventListener('input', (e) => this.handleInput(e));
        this.editor.addEventListener('paste', (e) => this.handlePaste(e));
        
        // Eventos de título
        this.titleInput.addEventListener('input', (e) => this.handleTitleChange(e));
        
        // Eventos de foco
        this.editor.addEventListener('focus', () => this.handleFocus());
        this.editor.addEventListener('blur', () => this.handleBlur());
        
        // Eventos de seleção
        document.addEventListener('selectionchange', () => this.handleSelectionChange());
    }
    
    handleKeyDown(event) {
        // Atalhos de teclado
        if (event.ctrlKey || event.metaKey) {
            switch(event.key.toLowerCase()) {
                case 's':
                    event.preventDefault();
                    this.saveDocument();
                    break;
                case 'z':
                    if (event.shiftKey) {
                        event.preventDefault();
                        this.redo();
                    } else {
                        event.preventDefault();
                        this.undo();
                    }
                    break;
                case 'y':
                    event.preventDefault();
                    this.redo();
                    break;
                case 'b':
                    event.preventDefault();
                    this.toggleBold();
                    break;
                case 'i':
                    event.preventDefault();
                    this.toggleItalic();
                    break;
                case 'u':
                    event.preventDefault();
                    this.toggleUnderline();
                    break;
                case '1':
                case '2':
                case '3':
                    event.preventDefault();
                    this.setHeading(parseInt(event.key));
                    break;
            }
        }
        
        // Enter para quebra de linha
        if (event.key === 'Enter' && event.shiftKey) {
            event.preventDefault();
            this.insertLineBreak();
        }
        
        // Tab para indentação
        if (event.key === 'Tab') {
            event.preventDefault();
            this.insertIndentation();
        }
    }
    
    handleInput(event) {
        this.updateWordCount();
        this.updateCharacterCount();
        this.saveToHistory();
        this.triggerAutoSave();
        
        // Atualizar status
        this.updateStatus('editing');
    }
    
    handlePaste(event) {
        event.preventDefault();
        
        const clipboardData = event.clipboardData || window.clipboardData;
        const pastedData = clipboardData.getData('text/html') || clipboardData.getData('text');
        
        // Limpar formatação indesejada
        const cleanData = this.cleanPastedContent(pastedData);
        
        // Inserir conteúdo limpo
        this.insertHTML(cleanData);
    }
    
    cleanPastedContent(content) {
        // Remover estilos indesejados
        let cleanContent = content
            .replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '')
            .replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '')
            .replace(/class="[^"]*"/gi, '')
            .replace(/style="[^"]*"/gi, '')
            .replace(/<span[^>]*>/gi, '')
            .replace(/<\/span>/gi, '')
            .replace(/<div[^>]*>/gi, '<p>')
            .replace(/<\/div>/gi, '</p>');
        
        return cleanContent;
    }
    
    insertHTML(html) {
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            range.deleteContents();
            
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            const fragment = document.createDocumentFragment();
            while (tempDiv.firstChild) {
                fragment.appendChild(tempDiv.firstChild);
            }
            
            range.insertNode(fragment);
            range.collapse(false);
        }
    }
    
    // Funcionalidades de formatação
    toggleBold() {
        document.execCommand('bold', false, null);
        this.updateToolbarState();
    }
    
    toggleItalic() {
        document.execCommand('italic', false, null);
        this.updateToolbarState();
    }
    
    toggleUnderline() {
        document.execCommand('underline', false, null);
        this.updateToolbarState();
    }
    
    setHeading(level) {
        const tag = `h${level}`;
        document.execCommand('formatBlock', false, `<${tag}>`);
        this.updateToolbarState();
    }
    
    alignText(alignment) {
        document.execCommand(`justify${alignment}`, false, null);
        this.updateToolbarState();
    }
    
    insertList(type) {
        const command = type === 'ordered' ? 'insertOrderedList' : 'insertUnorderedList';
        document.execCommand(command, false, null);
        this.updateToolbarState();
    }
    
    // Sistema de numeração jurídica
    setupLegalNumbering() {
        if (!this.options.legalNumbering) return;
        
        this.legalNumbering = {
            articles: 0,
            paragraphs: 0,
            items: 0,
            subItems: 0
        };
        
        // Observar mudanças no conteúdo
        const observer = new MutationObserver(() => {
            this.updateLegalNumbering();
        });
        
        observer.observe(this.editor, {
            childList: true,
            subtree: true
        });
    }
    
    updateLegalNumbering() {
        const paragraphs = this.editor.querySelectorAll('p');
        let articleCount = 0;
        let paragraphCount = 0;
        
        paragraphs.forEach((p, index) => {
            const text = p.textContent.trim();
            
            // Detectar artigos
            if (text.match(/^Art\.?\s*\d+[º°]?/i)) {
                articleCount++;
                p.innerHTML = p.innerHTML.replace(
                    /^Art\.?\s*(\d+)[º°]?/i,
                    `Art. ${articleCount}º`
                );
            }
            
            // Detectar parágrafos
            if (text.match(/^§\s*\d+[º°]?/i)) {
                paragraphCount++;
                p.innerHTML = p.innerHTML.replace(
                    /^§\s*(\d+)[º°]?/i,
                    `§ ${paragraphCount}º`
                );
            }
        });
    }
    
    // Sistema de templates
    loadTemplates() {
        this.templates.set('projeto_lei', {
            name: 'Projeto de Lei',
            content: `
                <h1 style="text-align: center;">PROJETO DE LEI Nº [NUMERO]</h1>
                
                <div style="margin-bottom: 1.5rem;">
                    <p><strong>Autor:</strong> [AUTOR_NOME] - [AUTOR_CARGO]</p>
                    <p><strong>Data:</strong> [DATA_CRIACAO]</p>
                    <p><strong>Legislatura:</strong> [LEGISLATURA]</p>
                </div>
                
                <div style="padding: 1rem; background: #f7fafc; border-left: 4px solid #4299e1; border-radius: 0.375rem; margin-bottom: 1.5rem;">
                    <strong>EMENTA:</strong> [EMENTA]
                </div>
                
                <p style="text-align: justify; margin-bottom: 1.5rem;">
                    A Câmara Municipal de [MUNICIPIO], Estado de [ESTADO], no uso de suas atribuições legais, <strong>APROVA</strong>:
                </p>
                
                <div style="margin-bottom: 1.5rem;">
                    <p><strong>Art. 1º</strong> - [DISPOSIÇÃO PRINCIPAL]</p>
                    <p><strong>Art. 2º</strong> - [DISPOSIÇÕES COMPLEMENTARES]</p>
                    <p><strong>Art. 3º</strong> - Esta Lei entra em vigor na data de sua publicação.</p>
                </div>
                
                <div style="text-align: right; margin-top: 3rem;">
                    <p>[MUNICIPIO], [DATA_CRIACAO]</p>
                    <br>
                    <p><strong>[AUTOR_NOME]</strong></p>
                    <p>[AUTOR_CARGO]</p>
                </div>
            `
        });
        
        this.templates.set('contrato', {
            name: 'Contrato de Prestação de Serviços',
            content: `
                <h1 style="text-align: center;">CONTRATO DE PRESTAÇÃO DE SERVIÇOS</h1>
                
                <div style="margin-bottom: 1.5rem;">
                    <p><strong>CONTRATANTE:</strong> [CONTRATANTE_NOME]</p>
                    <p><strong>CONTRATADO:</strong> [CONTRATADO_NOME]</p>
                    <p><strong>DATA:</strong> [DATA_CONTRATO]</p>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <p><strong>Art. 1º</strong> - OBJETO</p>
                    <p>O presente contrato tem por objeto a prestação de serviços de [DESCRIÇÃO_SERVIÇOS].</p>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <p><strong>Art. 2º</strong> - VALOR E FORMA DE PAGAMENTO</p>
                    <p>O valor total dos serviços será de R$ [VALOR], a ser pago conforme acordado.</p>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <p><strong>Art. 3º</strong> - PRAZO</p>
                    <p>Os serviços serão executados no prazo de [PRAZO] dias.</p>
                </div>
                
                <div style="text-align: right; margin-top: 3rem;">
                    <p>[CIDADE], [DATA_CONTRATO]</p>
                    <br>
                    <p><strong>[CONTRATANTE_NOME]</strong></p>
                    <p>Contratante</p>
                    <br>
                    <p><strong>[CONTRATADO_NOME]</strong></p>
                    <p>Contratado</p>
                </div>
            `
        });
        
        this.templates.set('peticao', {
            name: 'Petição Inicial',
            content: `
                <h1 style="text-align: center;">PETIÇÃO INICIAL</h1>
                
                <div style="margin-bottom: 1.5rem;">
                    <p><strong>REQUERENTE:</strong> [REQUERENTE_NOME]</p>
                    <p><strong>REQUERIDO:</strong> [REQUERIDO_NOME]</p>
                    <p><strong>DATA:</strong> [DATA_PETICAO]</p>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <p><strong>I - DOS FATOS</strong></p>
                    <p>[DESCRIÇÃO DOS FATOS]</p>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <p><strong>II - DO DIREITO</strong></p>
                    <p>[FUNDAMENTAÇÃO JURÍDICA]</p>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <p><strong>III - DOS PEDIDOS</strong></p>
                    <p>Ante o exposto, requer:</p>
                    <p>a) [PEDIDO PRINCIPAL];</p>
                    <p>b) [PEDIDOS COMPLEMENTARES].</p>
                </div>
                
                <div style="text-align: right; margin-top: 3rem;">
                    <p>[CIDADE], [DATA_PETICAO]</p>
                    <br>
                    <p><strong>[REQUERENTE_NOME]</strong></p>
                    <p>Requerente</p>
                </div>
            `
        });
    }
    
    loadTemplate(templateName) {
        const template = this.templates.get(templateName);
        if (template) {
            this.editor.innerHTML = template.content;
            this.updateWordCount();
            this.updateCharacterCount();
            this.saveToHistory();
            
            // Notificar usuário
            this.showNotification(`Template "${template.name}" carregado com sucesso!`, 'success');
        }
    }
    
    // Sistema de auto-save
    setupAutoSave() {
        if (!this.options.autoSave) return;
        
        this.autoSaveInterval = setInterval(() => {
            this.saveDocument();
        }, 30000); // Salvar a cada 30 segundos
    }
    
    triggerAutoSave() {
        if (this.autoSaveTimeout) {
            clearTimeout(this.autoSaveTimeout);
        }
        
        this.autoSaveTimeout = setTimeout(() => {
            this.saveDocument();
        }, 2000); // Salvar 2 segundos após parar de digitar
    }
    
    // Sistema de histórico (undo/redo)
    saveToHistory() {
        const currentContent = this.editor.innerHTML;
        this.undoStack.push(currentContent);
        
        // Limitar tamanho do histórico
        if (this.undoStack.length > 50) {
            this.undoStack.shift();
        }
        
        // Limpar redo stack quando novo conteúdo é adicionado
        this.redoStack = [];
    }
    
    undo() {
        if (this.undoStack.length > 1) {
            const currentContent = this.undoStack.pop();
            this.redoStack.push(currentContent);
            
            const previousContent = this.undoStack[this.undoStack.length - 1];
            this.editor.innerHTML = previousContent;
            
            this.updateWordCount();
            this.updateCharacterCount();
        }
    }
    
    redo() {
        if (this.redoStack.length > 0) {
            const content = this.redoStack.pop();
            this.undoStack.push(this.editor.innerHTML);
            this.editor.innerHTML = content;
            
            this.updateWordCount();
            this.updateCharacterCount();
        }
    }
    
    // Sistema de colaboração
    setupCollaboration() {
        if (!this.options.collaboration) return;
        
        // Implementar WebSocket para colaboração em tempo real
        this.collaborationSocket = new WebSocket('ws://localhost:8080/collaboration');
        
        this.collaborationSocket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            this.handleCollaborationMessage(data);
        };
    }
    
    handleCollaborationMessage(data) {
        switch (data.type) {
            case 'cursor_update':
                this.updateCollaboratorCursor(data);
                break;
            case 'content_update':
                this.updateCollaboratorContent(data);
                break;
            case 'user_joined':
                this.addCollaborator(data.user);
                break;
            case 'user_left':
                this.removeCollaborator(data.userId);
                break;
        }
    }
    
    updateCollaboratorCursor(data) {
        // Mostrar cursor do colaborador
        const cursor = document.createElement('div');
        cursor.className = 'collaborator-cursor';
        cursor.style.left = `${data.x}px`;
        cursor.style.top = `${data.y}px`;
        cursor.style.backgroundColor = data.color;
        cursor.innerHTML = data.userName;
        
        document.body.appendChild(cursor);
        
        setTimeout(() => {
            cursor.remove();
        }, 3000);
    }
    
    // Utilitários
    updateWordCount() {
        const text = this.editor.textContent || this.editor.innerText;
        const words = text.trim().split(/\s+/).filter(word => word.length > 0);
        const wordCount = words.length;
        
        const wordCountElement = document.getElementById('word-count');
        if (wordCountElement) {
            wordCountElement.textContent = `📝 ${wordCount} palavras`;
        }
        
        return wordCount;
    }
    
    updateCharacterCount() {
        const text = this.editor.textContent || this.editor.innerText;
        const charCount = text.length;
        
        // Atualizar elemento de contagem de caracteres se existir
        const charCountElement = document.getElementById('char-count');
        if (charCountElement) {
            charCountElement.textContent = `${charCount} caracteres`;
        }
        
        return charCount;
    }
    
    updateToolbarState() {
        // Atualizar estado dos botões da toolbar
        const buttons = document.querySelectorAll('.toolbar-btn');
        
        buttons.forEach(button => {
            const command = button.getAttribute('data-command');
            if (command) {
                const isActive = document.queryCommandState(command);
                button.classList.toggle('active', isActive);
            }
        });
    }
    
    updateStatus(status) {
        const statusElement = document.getElementById('save-status');
        if (!statusElement) return;
        
        switch (status) {
            case 'editing':
                statusElement.className = 'status-indicator status-saving';
                statusElement.innerHTML = '<i class="fas fa-pencil-alt"></i> Editando...';
                break;
            case 'saving':
                statusElement.className = 'status-indicator status-saving';
                statusElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
                break;
            case 'saved':
                statusElement.className = 'status-indicator status-saved';
                statusElement.innerHTML = '<i class="fas fa-check-circle"></i> Salvo';
                break;
            case 'error':
                statusElement.className = 'status-indicator status-error';
                statusElement.innerHTML = '<i class="fas fa-exclamation-circle"></i> Erro';
                break;
        }
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    // Métodos públicos
    saveDocument() {
        this.currentDocument.content = this.editor.innerHTML;
        this.currentDocument.title = this.titleInput.value;
        this.currentDocument.version++;
        this.currentDocument.lastSaved = new Date();
        
        // Simular salvamento
        this.updateStatus('saving');
        
        setTimeout(() => {
            this.updateStatus('saved');
            this.showNotification('Documento salvo com sucesso!', 'success');
        }, 1000);
        
        return this.currentDocument;
    }
    
    loadDocument(documentData) {
        this.currentDocument = { ...documentData };
        this.editor.innerHTML = documentData.content;
        this.titleInput.value = documentData.title;
        
        this.updateWordCount();
        this.updateCharacterCount();
        
        this.showNotification('Documento carregado com sucesso!', 'success');
    }
    
    exportDocument(format = 'docx') {
        const content = this.editor.innerHTML;
        const title = this.titleInput.value;
        
        // Simular exportação
        this.showNotification(`Exportando documento em formato ${format.toUpperCase()}...`, 'info');
        
        setTimeout(() => {
            this.showNotification(`Documento exportado com sucesso!`, 'success');
        }, 2000);
        
        return { content, title, format };
    }
    
    // Event handlers
    handleTitleChange(event) {
        this.currentDocument.title = event.target.value;
        this.triggerAutoSave();
    }
    
    handleFocus() {
        this.editor.classList.add('focused');
    }
    
    handleBlur() {
        this.editor.classList.remove('focused');
    }
    
    handleSelectionChange() {
        this.updateToolbarState();
    }
    
    insertLineBreak() {
        document.execCommand('insertLineBreak', false, null);
    }
    
    insertIndentation() {
        document.execCommand('insertText', false, '    ');
    }
}

// Estilos CSS para notificações
const notificationStyles = `
<style>
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    transform: translateX(100%);
    transition: transform 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    max-width: 300px;
}

.notification.show {
    transform: translateX(0);
}

.notification-success {
    border-left: 4px solid #059669;
    color: #059669;
}

.notification-error {
    border-left: 4px solid #dc2626;
    color: #dc2626;
}

.notification-info {
    border-left: 4px solid #2563eb;
    color: #2563eb;
}

.collaborator-cursor {
    position: absolute;
    width: 2px;
    height: 20px;
    background: #ff0000;
    z-index: 1000;
    pointer-events: none;
    font-size: 12px;
    color: white;
    padding: 2px 4px;
    border-radius: 2px;
}

.enhanced-editor {
    position: relative;
}

.enhanced-editor.focused {
    box-shadow: inset 0 0 0 2px rgba(37, 99, 235, 0.2);
}
</style>
`;

// Adicionar estilos ao documento
document.head.insertAdjacentHTML('beforeend', notificationStyles);

// Exportar classe
window.EnhancedEditor = EnhancedEditor; 