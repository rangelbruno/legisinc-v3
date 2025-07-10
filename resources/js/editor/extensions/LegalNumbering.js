import { Extension } from '@tiptap/core'

/**
 * Adicionar CSS para numeração jurídica
 */
function addLegalCSS() {
  // Verificar se o CSS já foi adicionado
  if (document.getElementById('legal-numbering-css')) {
    return
  }

  const style = document.createElement('style')
  style.id = 'legal-numbering-css'
  style.textContent = `
    .legal-document {
      counter-reset: livro capitulo secao artigo paragrafo inciso alinea item;
      line-height: 1.6;
      font-family: 'Times New Roman', serif;
    }
    
    /* Livro */
    .legal-livro {
      counter-increment: livro;
      counter-reset: capitulo;
      text-align: center;
      margin: 3rem 0 2rem 0;
      page-break-before: always;
    }
    
    .legal-livro h1 {
      font-size: 2rem;
      font-weight: bold;
      color: #7c2d12;
      text-transform: uppercase;
      margin: 0;
      padding: 1.5rem;
      border: 3px solid #7c2d12;
      background-color: #fef7ff;
      letter-spacing: 2px;
    }
    
    /* Capítulo */
    .legal-capitulo {
      counter-increment: capitulo;
      counter-reset: secao;
      text-align: center;
      margin: 2.5rem 0 1.5rem 0;
      page-break-before: always;
    }
    
    .legal-capitulo h2 {
      font-size: 1.75rem;
      font-weight: bold;
      color: #dc2626;
      text-transform: uppercase;
      margin: 0;
      padding: 1.25rem;
      border: 2px solid #dc2626;
      background-color: #fef2f2;
      letter-spacing: 1px;
    }
    
    /* Seção */
    .legal-secao {
      counter-increment: secao;
      counter-reset: artigo;
      text-align: center;
      margin: 1.5rem 0 1rem 0;
    }
    
    .legal-secao h3 {
      font-size: 1.25rem;
      font-weight: bold;
      color: #ea580c;
      text-transform: uppercase;
      margin: 0;
      padding: 0.75rem;
      border: 1px solid #ea580c;
      background-color: #fff7ed;
    }
    
    /* Artigo */
    .legal-artigo {
      counter-increment: artigo;
      counter-reset: paragrafo;
      margin: 1rem 0;
      padding-left: 12px;
      border-left: 4px solid #3b82f6;
      background-color: #eff6ff;
      padding: 0.75rem 0.75rem 0.75rem 12px;
      border-radius: 4px;
    }
    
    .legal-artigo::before {
      content: "Art. " counter(artigo) "º ";
      font-weight: bold;
      color: #3b82f6;
      font-size: 1.1rem;
    }
    
    /* Parágrafo */
    .legal-paragrafo {
      counter-increment: paragrafo;
      counter-reset: inciso;
      margin: 0.75rem 0 0.75rem 2rem;
      padding-left: 12px;
      border-left: 3px solid #6b7280;
      background-color: #f9fafb;
      padding: 0.5rem 0.5rem 0.5rem 12px;
      border-radius: 3px;
    }
    
    .legal-paragrafo::before {
      content: "§ " counter(paragrafo) "º ";
      font-weight: bold;
      color: #6b7280;
    }
    
    /* Inciso */
    .legal-inciso {
      counter-increment: inciso;
      counter-reset: alinea;
      margin: 0.5rem 0 0.5rem 3rem;
      padding-left: 12px;
      border-left: 2px solid #9ca3af;
      padding: 0.25rem 0.25rem 0.25rem 12px;
    }
    
    .legal-inciso::before {
      content: counter(inciso, upper-roman) " – ";
      font-weight: bold;
      color: #9ca3af;
    }
    
    /* Alínea */
    .legal-alinea {
      counter-increment: alinea;
      counter-reset: item;
      margin: 0.25rem 0 0.25rem 4rem;
      padding-left: 12px;
      border-left: 1px solid #d1d5db;
      padding: 0.125rem 0.125rem 0.125rem 12px;
    }
    
    .legal-alinea::before {
      content: counter(alinea, lower-alpha) ") ";
      font-weight: bold;
      color: #d1d5db;
    }
    
    /* Item */
    .legal-item {
      counter-increment: item;
      margin: 0.125rem 0 0.125rem 5rem;
      padding-left: 12px;
      padding: 0.0625rem 0.0625rem 0.0625rem 12px;
    }
    
    .legal-item::before {
      content: counter(item) ". ";
      font-weight: bold;
      color: #6b7280;
    }
    
    /* Estilos de hover para melhor UX */
    .legal-livro:hover {
      background-color: #f3e8ff;
    }
    
    .legal-capitulo:hover {
      background-color: #fecaca;
    }
    
    .legal-secao:hover {
      background-color: #fed7aa;
    }
    
    .legal-artigo:hover {
      background-color: #dbeafe;
      border-left-color: #1d4ed8;
    }
    
    .legal-paragrafo:hover {
      background-color: #f3f4f6;
      border-left-color: #374151;
    }
    
    .legal-inciso:hover {
      background-color: #f3f4f6;
      border-left-color: #6b7280;
    }
    
    .legal-alinea:hover {
      background-color: #f9fafb;
      border-left-color: #9ca3af;
    }
    
    .legal-item:hover {
      background-color: #f9fafb;
    }
    
    /* Estilos para seleção */
    .legal-artigo.selected {
      background-color: #dbeafe;
      border-left-color: #1d4ed8;
    }
    
    .legal-paragrafo.selected {
      background-color: #f3f4f6;
      border-left-color: #374151;
    }
    
    .legal-inciso.selected {
      background-color: #f3f4f6;
      border-left-color: #6b7280;
    }
    
    .legal-alinea.selected {
      background-color: #f9fafb;
      border-left-color: #9ca3af;
    }
    
    .legal-item.selected {
      background-color: #f9fafb;
    }
    
    /* Estilos para impressão */
    @media print {
      .legal-document {
        font-size: 12pt;
        line-height: 1.5;
      }
      
      .legal-livro, .legal-capitulo, .legal-secao, .legal-artigo {
        page-break-inside: avoid;
      }
      
      .legal-livro h1, .legal-capitulo h2, .legal-secao h3 {
        background-color: transparent !important;
        border: 1px solid #000 !important;
      }
      
      .legal-artigo, .legal-paragrafo, .legal-inciso, .legal-alinea, .legal-item {
        background-color: transparent !important;
        border-left-color: #000 !important;
      }
    }
  `
  
  document.head.appendChild(style)
}

/**
 * Extensão para numeração hierárquica jurídica brasileira
 * Suporta: Artigos, Parágrafos, Incisos, Alíneas e Itens
 */
export const LegalNumbering = Extension.create({
  name: 'legalNumbering',

  addGlobalAttributes() {
    return [
      {
        types: ['heading', 'paragraph'],
        attributes: {
          legalLevel: {
            default: null,
            parseHTML: element => element.getAttribute('data-legal-level'),
            renderHTML: attributes => {
              if (!attributes.legalLevel) return {}
              return { 
                'data-legal-level': attributes.legalLevel,
                class: this.getLegalClasses(attributes.legalLevel)
              }
            },
          },
          legalNumber: {
            default: null,
            parseHTML: element => element.getAttribute('data-legal-number'),
            renderHTML: attributes => {
              if (!attributes.legalNumber) return {}
              return { 'data-legal-number': attributes.legalNumber }
            },
          },
        },
      },
    ]
  },

  addCommands() {
    return {
      setLegalLevel: (level) => ({ tr, state, dispatch }) => {
        const { from, to } = state.selection
        const node = state.doc.nodeAt(from)
        
        if (!node) return false

        // Determinar o número baseado no nível
        const number = this.calculateLegalNumber(state.doc, from, level)
        
        if (dispatch) {
          tr.setNodeMarkup(from, undefined, {
            ...node.attrs,
            legalLevel: level,
            legalNumber: number,
          })
        }
        
        return true
      },
      
      removeLegalLevel: () => ({ tr, state, dispatch }) => {
        const { from } = state.selection
        const node = state.doc.nodeAt(from)
        
        if (!node) return false

        if (dispatch) {
          tr.setNodeMarkup(from, undefined, {
            ...node.attrs,
            legalLevel: null,
            legalNumber: null,
          })
        }
        
        return true
      },
    }
  },

  addKeyboardShortcuts() {
    return {
      'Mod-1': () => this.editor.commands.setLegalLevel('artigo'),
      'Mod-2': () => this.editor.commands.setLegalLevel('paragrafo'),
      'Mod-3': () => this.editor.commands.setLegalLevel('inciso'),
      'Mod-4': () => this.editor.commands.setLegalLevel('alinea'),
      'Mod-5': () => this.editor.commands.setLegalLevel('item'),
    }
  },

  onCreate() {
    // Adicionar CSS global para numeração quando a extensão for criada
    addLegalCSS()
  },

  // Método para calcular numeração automática
  calculateLegalNumber(doc, pos, level) {
    const counters = {
      artigo: 0,
      paragrafo: 0,
      inciso: 0,
      alinea: 0,
      item: 0,
    }

    let currentLevel = null
    
    // Percorrer o documento até a posição atual
    doc.nodesBetween(0, pos, (node, nodePos) => {
      if (node.attrs.legalLevel) {
        const nodeLevel = node.attrs.legalLevel
        
        // Resetar contadores inferiores quando encontrar nível superior
        this.resetLowerCounters(counters, nodeLevel)
        
        // Incrementar contador do nível atual
        counters[nodeLevel]++
        currentLevel = nodeLevel
      }
    })

    // Resetar contadores inferiores para o novo nível
    this.resetLowerCounters(counters, level)
    
    // Incrementar contador do nível atual
    counters[level]++
    
    return counters[level]
  },

  // Resetar contadores de níveis inferiores
  resetLowerCounters(counters, level) {
    const levels = ['artigo', 'paragrafo', 'inciso', 'alinea', 'item']
    const currentIndex = levels.indexOf(level)
    
    // Resetar todos os níveis inferiores
    for (let i = currentIndex + 1; i < levels.length; i++) {
      counters[levels[i]] = 0
    }
  },

  // Obter classes CSS para cada nível
  getLegalClasses(level) {
    const classMap = {
      artigo: 'legal-artigo font-bold text-lg mb-4',
      paragrafo: 'legal-paragrafo font-semibold ml-4 mb-3',
      inciso: 'legal-inciso ml-8 mb-2',
      alinea: 'legal-alinea ml-12 mb-2',
      item: 'legal-item ml-16 mb-1',
    }
    
    return classMap[level] || ''
  },

})

export default LegalNumbering