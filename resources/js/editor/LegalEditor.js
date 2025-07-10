import { Editor } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'
import { Collaboration } from '@tiptap/extension-collaboration'
import { CollaborationCursor } from '@tiptap/extension-collaboration-cursor'
import { Table } from '@tiptap/extension-table'
import { TableRow } from '@tiptap/extension-table-row'
import { TableCell } from '@tiptap/extension-table-cell'
import { TableHeader } from '@tiptap/extension-table-header'
import { CharacterCount } from '@tiptap/extension-character-count'
import { Underline } from '@tiptap/extension-underline'
import { LegalNumbering } from './extensions/LegalNumbering'
import { LegalTemplates } from './extensions/LegalTemplates'
import { AutoSave } from './features/AutoSave'
import { LegalTemplateEngine } from './features/LegalTemplateEngine'
import { BrazilianLegalNumbering } from './features/BrazilianLegalNumbering'

/**
 * Editor Jurídico Avançado com Tiptap
 * Implementação completa para documentos jurídicos brasileiros
 */
export class LegalEditor {
  constructor(container, options = {}) {
    this.container = container
    this.options = {
      collaboration: false,
      templates: true,
      autoSave: true,
      legalNumbering: true,
      characterLimit: 50000,
      ...options
    }
    
    this.editor = null
    this.templateEngine = null
    this.autoSave = null
    this.legalNumbering = null
    
    this.init()
  }

  init() {
    this.setupEditor()
    this.setupFeatures()
    this.setupEventListeners()
  }

  setupEditor() {
    const extensions = [
      StarterKit.configure({
        history: this.options.collaboration ? false : true,
      }),
      Underline,
      Table.configure({
        resizable: true,
        HTMLAttributes: {
          class: 'legal-table border-collapse border border-gray-300',
        },
      }),
      TableRow,
      TableHeader,
      TableCell,
      CharacterCount.configure({
        limit: this.options.characterLimit,
      }),
    ]

    // Adicionar extensões de colaboração se habilitadas
    if (this.options.collaboration && this.options.ydoc) {
      extensions.push(
        Collaboration.configure({
          document: this.options.ydoc,
          field: 'default',
        }),
        CollaborationCursor.configure({
          provider: this.options.wsProvider,
          user: this.options.user,
        })
      )
    }

    // Adicionar extensões jurídicas
    if (this.options.legalNumbering) {
      extensions.push(LegalNumbering)
    }

    if (this.options.templates) {
      extensions.push(LegalTemplates)
    }

    this.editor = new Editor({
      element: this.container,
      extensions,
      content: this.options.content || '<p>Inicie seu documento jurídico aqui...</p>',
      editorProps: {
        attributes: {
          class: 'legal-editor prose prose-lg max-w-none min-h-[500px] p-4 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500',
        },
      },
    })
  }

  setupFeatures() {
    // Sistema de templates
    if (this.options.templates) {
      this.templateEngine = new LegalTemplateEngine()
      this.setupDefaultTemplates()
    }

    // Auto-save
    if (this.options.autoSave && this.options.saveCallback) {
      this.autoSave = new AutoSave(this.editor, this.options.saveCallback)
    }

    // Numeração jurídica brasileira
    if (this.options.legalNumbering) {
      this.legalNumbering = new BrazilianLegalNumbering()
    }
  }

  setupDefaultTemplates() {
    // Template de contrato
    this.templateEngine.registerTemplate('contract', `
      <div class="legal-document">
        <h1 class="text-center font-bold text-xl mb-4">CONTRATO DE {{type}}</h1>
        
        <div class="parties mb-6">
          <p><strong>CONTRATANTE:</strong> {{contractor.name}}</p>
          <p><strong>CNPJ:</strong> {{formatCNPJ contractor.cnpj}}</p>
          <p><strong>Endereço:</strong> {{contractor.address}}</p>
          
          <p class="mt-4"><strong>CONTRATADO:</strong> {{contractee.name}}</p>
          <p><strong>CPF:</strong> {{formatCPF contractee.cpf}}</p>
          <p><strong>Endereço:</strong> {{contractee.address}}</p>
        </div>
        
        <div class="clauses">
          <div class="artigo mb-4">
            <p><strong>CLÁUSULA PRIMEIRA - DO OBJETO</strong></p>
            <p>{{objeto}}</p>
          </div>
          
          <div class="artigo mb-4">
            <p><strong>CLÁUSULA SEGUNDA - DO PRAZO</strong></p>
            <p>{{prazo}}</p>
          </div>
          
          <div class="artigo mb-4">
            <p><strong>CLÁUSULA TERCEIRA - DO VALOR</strong></p>
            <p>O valor total do contrato é de {{formatCurrency valor}}.</p>
          </div>
          
          <div class="artigo mb-4">
            <p><strong>CLÁUSULA QUARTA - DO PAGAMENTO</strong></p>
            <p>{{pagamento}}</p>
          </div>
          
          <div class="artigo mb-4">
            <p><strong>CLÁUSULA QUINTA - DAS DISPOSIÇÕES GERAIS</strong></p>
            <p>Este contrato entra em vigor na data de sua assinatura.</p>
          </div>
        </div>
        
        <div class="signature-block mt-8">
          <p class="text-center">{{cidade}}, {{formatDate date}}</p>
          <div class="signatures mt-8 flex justify-between">
            <div class="text-center">
              <div class="border-t border-gray-400 w-48 mt-16"></div>
              <p>{{contractor.name}}</p>
              <p>Contratante</p>
            </div>
            <div class="text-center">
              <div class="border-t border-gray-400 w-48 mt-16"></div>
              <p>{{contractee.name}}</p>
              <p>Contratado</p>
            </div>
          </div>
        </div>
      </div>
    `)

    // Template de projeto de lei
    this.templateEngine.registerTemplate('bill', `
      <div class="legal-document">
        <h1 class="text-center font-bold text-xl mb-4">PROJETO DE LEI Nº {{numero}}, DE {{ano}}</h1>
        <p class="text-center italic mb-6">{{ementa}}</p>
        
        <p class="mb-6"><strong>O CONGRESSO NACIONAL decreta:</strong></p>
        
        <div class="artigo mb-4">
          <p>Esta lei {{objeto}}.</p>
        </div>
        
        <div class="artigo mb-4">
          <p>Para os efeitos desta lei, considera-se:</p>
          <div class="inciso ml-4">
            <p>{{definicao_a}};</p>
          </div>
          <div class="inciso ml-4">
            <p>{{definicao_b}};</p>
          </div>
        </div>
        
        <div class="artigo mb-4">
          <p>Esta lei entra em vigor na data de sua publicação.</p>
        </div>
      </div>
    `)

    // Template de petição
    this.templateEngine.registerTemplate('petition', `
      <div class="legal-document">
        <h1 class="text-center font-bold text-xl mb-4">{{tipo_peticao}}</h1>
        
        <div class="header mb-6">
          <p><strong>Exmo. Sr. {{autoridade}}</strong></p>
          <p>{{orgao}}</p>
          <p>{{comarca}}</p>
        </div>
        
        <div class="parties mb-6">
          <p><strong>{{requerente.name}}</strong>, {{requerente.qualificacao}}, 
          inscrito no CPF sob o nº {{formatCPF requerente.cpf}}, 
          residente e domiciliado {{requerente.endereco}}, 
          por intermédio de seu advogado que esta subscreve, 
          vem respeitosamente à presença de Vossa Excelência expor e requerer:</p>
        </div>
        
        <div class="facts mb-6">
          <h2 class="font-bold text-lg mb-2">DOS FATOS</h2>
          <p>{{fatos}}</p>
        </div>
        
        <div class="legal-basis mb-6">
          <h2 class="font-bold text-lg mb-2">DO DIREITO</h2>
          <p>{{fundamento_juridico}}</p>
        </div>
        
        <div class="request mb-6">
          <h2 class="font-bold text-lg mb-2">DO PEDIDO</h2>
          <p>Diante do exposto, requer:</p>
          <p>{{pedido}}</p>
        </div>
        
        <p class="mb-6">Termos em que pede deferimento.</p>
        
        <div class="signature-block mt-8">
          <p>{{cidade}}, {{formatDate date}}</p>
          <div class="signature mt-8">
            <div class="border-t border-gray-400 w-48 mt-16"></div>
            <p>{{advogado.nome}}</p>
            <p>OAB/{{advogado.estado}} {{advogado.numero}}</p>
          </div>
        </div>
      </div>
    `)
  }

  setupEventListeners() {
    // Eventos customizados
    this.editor.on('update', ({ editor }) => {
      if (this.options.onChange) {
        this.options.onChange(editor.getHTML())
      }
    })

    // Atalhos de teclado para numeração jurídica
    if (this.options.legalNumbering) {
      this.setupLegalKeyboardShortcuts()
    }
  }

  setupLegalKeyboardShortcuts() {
    // Adicionar atalhos para numeração jurídica
    document.addEventListener('keydown', (event) => {
      if (event.ctrlKey || event.metaKey) {
        switch (event.key) {
          case '1':
            event.preventDefault()
            this.setLegalLevel('artigo')
            break
          case '2':
            event.preventDefault()
            this.setLegalLevel('paragrafo')
            break
          case '3':
            event.preventDefault()
            this.setLegalLevel('inciso')
            break
          case '4':
            event.preventDefault()
            this.setLegalLevel('alinea')
            break
        }
      }
    })
  }

  // Métodos públicos
  getContent() {
    return this.editor.getHTML()
  }

  setContent(content) {
    this.editor.commands.setContent(content)
  }

  getJSON() {
    return this.editor.getJSON()
  }

  setJSON(json) {
    this.editor.commands.setContent(json)
  }

  insertTemplate(templateName, data) {
    if (this.templateEngine) {
      const content = this.templateEngine.render(templateName, data)
      this.editor.commands.insertContent(content)
    }
  }

  setLegalLevel(level) {
    if (this.editor.commands.setLegalLevel) {
      this.editor.commands.setLegalLevel(level)
    }
  }

  insertTable(rows = 3, cols = 3) {
    this.editor.commands.insertTable({
      rows,
      cols,
      withHeaderRow: true,
    })
  }

  focus() {
    this.editor.commands.focus()
  }

  blur() {
    this.editor.commands.blur()
  }

  getCharacterCount() {
    return this.editor.storage.characterCount.characters()
  }

  getWordCount() {
    return this.editor.storage.characterCount.words()
  }

  canUndo() {
    return this.editor.can().undo()
  }

  canRedo() {
    return this.editor.can().redo()
  }

  undo() {
    this.editor.commands.undo()
  }

  redo() {
    this.editor.commands.redo()
  }

  // Exportação básica (será expandida com bibliotecas específicas)
  exportHTML() {
    return this.getContent()
  }

  exportJSON() {
    return JSON.stringify(this.getJSON())
  }

  // Método de limpeza
  destroy() {
    if (this.editor) {
      this.editor.destroy()
    }
    if (this.autoSave) {
      this.autoSave.destroy()
    }
  }
}

// Exportar como padrão
export default LegalEditor