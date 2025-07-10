import LegalEditor from './LegalEditor.js'
import LegalEditorToolbar from './LegalEditorToolbar.js'

/**
 * Inicializar o Editor Jurídico
 * Função principal para criar uma instância completa do editor
 */
export function initLegalEditor(container, options = {}) {
  // Configurações padrão
  const defaultOptions = {
    // Configurações do editor
    content: '',
    autoSave: true,
    saveUrl: null,
    saveCallback: null,
    characterLimit: 50000,
    
    // Recursos
    legalNumbering: true,
    templates: true,
    collaboration: false,
    
    // Toolbar
    showToolbar: true,
    toolbarOptions: {
      showTemplates: true,
      showLegalLevels: true,
      showFormatting: true,
      showTable: true,
      showExport: true,
    },
    
    // Callbacks
    onChange: null,
    onSave: null,
    onError: null,
    
    // Colaboração (opcional)
    wsUrl: null,
    roomId: null,
    user: null,
    
    ...options
  }

  // Verificar se o container existe
  if (!container) {
    throw new Error('Container do editor não encontrado')
  }

  // Criar container wrapper se necessário
  let editorContainer = container
  if (defaultOptions.showToolbar) {
    const wrapper = document.createElement('div')
    wrapper.className = 'legal-editor-wrapper'
    container.parentNode.insertBefore(wrapper, container)
    wrapper.appendChild(container)
    editorContainer = container
  }

  // Configurar callback de salvamento
  let saveCallback = defaultOptions.saveCallback
  if (!saveCallback && defaultOptions.saveUrl) {
    saveCallback = async (content) => {
      const response = await fetch(defaultOptions.saveUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify({ content })
      })
      
      if (!response.ok) {
        throw new Error('Erro ao salvar documento')
      }
      
      return response.json()
    }
  }

  // Configurar colaboração se habilitada
  let ydoc = null
  let wsProvider = null
  
  if (defaultOptions.collaboration && defaultOptions.wsUrl && defaultOptions.roomId) {
    // Importar dinamicamente as dependências de colaboração
    import('yjs').then(Y => {
      import('y-websocket').then(YWebsocket => {
        ydoc = new Y.Doc()
        wsProvider = new YWebsocket.WebsocketProvider(
          defaultOptions.wsUrl,
          defaultOptions.roomId,
          ydoc,
          {
            connect: true,
            awareness: {
              user: defaultOptions.user || {
                name: 'Usuário',
                color: '#' + Math.floor(Math.random() * 16777215).toString(16)
              }
            }
          }
        )
        
        // Reconfigurar editor com colaboração
        editor.setupCollaboration(ydoc, wsProvider)
      })
    })
  }

  // Criar instância do editor
  const editor = new LegalEditor(editorContainer, {
    ...defaultOptions,
    saveCallback,
    ydoc,
    wsProvider,
    onChange: (content) => {
      if (defaultOptions.onChange) {
        defaultOptions.onChange(content)
      }
    }
  })

  // Criar toolbar se habilitada
  let toolbar = null
  if (defaultOptions.showToolbar) {
    toolbar = new LegalEditorToolbar(editor, defaultOptions.toolbarOptions)
    toolbar.insertBefore(editorContainer)
  }

  // Configurar eventos globais
  if (defaultOptions.onSave) {
    editor.on('autoSave:success', defaultOptions.onSave)
  }

  if (defaultOptions.onError) {
    editor.on('autoSave:error', defaultOptions.onError)
  }

  // Retornar instância completa
  return {
    editor,
    toolbar,
    
    // Métodos públicos
    getContent: () => editor.getContent(),
    setContent: (content) => editor.setContent(content),
    getJSON: () => editor.getJSON(),
    setJSON: (json) => editor.setJSON(json),
    
    // Templates
    insertTemplate: (name, data) => editor.insertTemplate(name, data),
    
    // Salvamento
    save: () => editor.autoSave?.saveNow(),
    
    // Controle
    focus: () => editor.focus(),
    blur: () => editor.blur(),
    
    // Estatísticas
    getWordCount: () => editor.getWordCount(),
    getCharacterCount: () => editor.getCharacterCount(),
    
    // Histórico
    undo: () => editor.undo(),
    redo: () => editor.redo(),
    canUndo: () => editor.canUndo(),
    canRedo: () => editor.canRedo(),
    
    // Exportação
    exportHTML: () => editor.exportHTML(),
    exportJSON: () => editor.exportJSON(),
    
    // Limpeza
    destroy: () => {
      if (toolbar) toolbar.destroy()
      editor.destroy()
      if (wsProvider) wsProvider.destroy()
    }
  }
}

/**
 * Inicializar editor simples (sem toolbar)
 */
export function initSimpleLegalEditor(container, options = {}) {
  return initLegalEditor(container, {
    ...options,
    showToolbar: false
  })
}

/**
 * Inicializar editor com colaboração
 */
export function initCollaborativeLegalEditor(container, options = {}) {
  if (!options.wsUrl || !options.roomId) {
    throw new Error('wsUrl e roomId são obrigatórios para colaboração')
  }
  
  return initLegalEditor(container, {
    ...options,
    collaboration: true
  })
}

/**
 * Configurações globais do editor
 */
export const LegalEditorConfig = {
  // Configurações padrão
  defaults: {
    theme: 'default',
    language: 'pt-BR',
    autoSave: true,
    saveDelay: 2000,
    characterLimit: 50000
  },
  
  // Configurar tema
  setTheme(theme) {
    document.documentElement.setAttribute('data-legal-editor-theme', theme)
    this.defaults.theme = theme
  },
  
  // Configurar idioma
  setLanguage(language) {
    this.defaults.language = language
  },
  
  // Registrar template global
  registerTemplate(name, template) {
    // Armazenar template globalmente
    if (!window.legalEditorTemplates) {
      window.legalEditorTemplates = new Map()
    }
    window.legalEditorTemplates.set(name, template)
  },
  
  // Obter template global
  getTemplate(name) {
    return window.legalEditorTemplates?.get(name)
  }
}

// Exportar classes principais
export { LegalEditor, LegalEditorToolbar }

// Exportar como padrão
export default {
  initLegalEditor,
  initSimpleLegalEditor,
  initCollaborativeLegalEditor,
  LegalEditor,
  LegalEditorToolbar,
  LegalEditorConfig
}