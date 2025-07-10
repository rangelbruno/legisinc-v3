/**
 * Toolbar para o Editor Jurídico
 * Fornece botões para formatação e funcionalidades específicas
 */
export class LegalEditorToolbar {
  constructor(editor, options = {}) {
    this.editor = editor
    this.options = {
      showTemplates: true,
      showLegalLevels: true,
      showFormatting: true,
      showTable: true,
      showExport: true,
      ...options
    }
    this.toolbarElement = null
    this.init()
  }

  init() {
    this.createToolbar()
    this.setupEventListeners()
  }

  createToolbar() {
    this.toolbarElement = document.createElement('div')
    this.toolbarElement.className = 'legal-editor-toolbar flex flex-wrap gap-2 p-3 bg-gray-50 border-b border-gray-200 rounded-t-lg'
    
    // Grupo de formatação básica
    if (this.options.showFormatting) {
      this.addFormattingGroup()
    }

    // Grupo de níveis jurídicos
    if (this.options.showLegalLevels) {
      this.addLegalLevelsGroup()
    }

    // Grupo de templates
    if (this.options.showTemplates) {
      this.addTemplatesGroup()
    }

    // Grupo de tabelas
    if (this.options.showTable) {
      this.addTableGroup()
    }

    // Grupo de exportação
    if (this.options.showExport) {
      this.addExportGroup()
    }

    // Grupo de utilitários
    this.addUtilitiesGroup()

    // Status de salvamento
    this.addSaveStatus()
  }

  addFormattingGroup() {
    const group = this.createGroup('Formatação')
    
    // Botões de formatação
    const buttons = [
      {
        icon: '𝐁',
        title: 'Negrito (Ctrl+B)',
        action: () => this.editor.editor.commands.toggleBold(),
        isActive: () => this.editor.editor.isActive('bold')
      },
      {
        icon: '𝐼',
        title: 'Itálico (Ctrl+I)',
        action: () => this.editor.editor.commands.toggleItalic(),
        isActive: () => this.editor.editor.isActive('italic')
      },
      {
        icon: '𝐔',
        title: 'Sublinhado (Ctrl+U)',
        action: () => this.editor.editor.commands.toggleUnderline(),
        isActive: () => this.editor.editor.isActive('underline')
      },
      {
        icon: '≡',
        title: 'Lista com marcadores',
        action: () => this.editor.editor.commands.toggleBulletList(),
        isActive: () => this.editor.editor.isActive('bulletList')
      },
      {
        icon: '1.',
        title: 'Lista numerada',
        action: () => this.editor.editor.commands.toggleOrderedList(),
        isActive: () => this.editor.editor.isActive('orderedList')
      }
    ]

    buttons.forEach(button => {
      const btn = this.createButton(button.icon, button.title, button.action)
      if (button.isActive) {
        this.updateButtonState(btn, button.isActive)
      }
      group.appendChild(btn)
    })

    this.toolbarElement.appendChild(group)
  }

  addLegalLevelsGroup() {
    const group = this.createGroup('Níveis Jurídicos')
    
    const levels = [
      { 
        level: 'livro', 
        label: 'LIVRO', 
        title: 'Livro (Ctrl+Shift+Alt+1)',
        color: '#7c2d12'
      },
      { 
        level: 'capitulo', 
        label: 'CAP', 
        title: 'Capítulo (Ctrl+Shift+Alt+2)',
        color: '#dc2626'
      },
      { 
        level: 'secao', 
        label: 'SEÇ', 
        title: 'Seção (Ctrl+Shift+1)',
        color: '#ea580c'
      },
      { 
        level: 'artigo', 
        label: 'Art.', 
        title: 'Artigo (Ctrl+1)',
        color: '#3b82f6'
      },
      { 
        level: 'paragrafo', 
        label: '§', 
        title: 'Parágrafo (Ctrl+2)',
        color: '#6b7280'
      },
      { 
        level: 'inciso', 
        label: 'I', 
        title: 'Inciso (Ctrl+3)',
        color: '#9ca3af'
      },
      { 
        level: 'alinea', 
        label: 'a)', 
        title: 'Alínea (Ctrl+4)',
        color: '#d1d5db'
      },
      { 
        level: 'item', 
        label: '1.', 
        title: 'Item (Ctrl+5)',
        color: '#6b7280'
      }
    ]

    levels.forEach(levelData => {
      const btn = this.createButton(
        levelData.label, 
        levelData.title, 
        () => {
          this.insertLegalLevel(levelData.level)
        }
      )
      btn.style.borderColor = levelData.color
      btn.style.color = levelData.color
      group.appendChild(btn)
    })

    this.toolbarElement.appendChild(group)
  }

  addTemplatesGroup() {
    const group = this.createGroup('Templates')
    
    // Dropdown para templates
    const dropdown = document.createElement('div')
    dropdown.className = 'relative'
    
    const button = this.createButton('📄', 'Inserir Template')
    const menu = document.createElement('div')
    menu.className = 'absolute top-full left-0 mt-1 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-10 hidden'
    
    const templates = [
      { 
        name: 'contract', 
        label: 'Contrato', 
        icon: '📝',
        shortcut: 'Ctrl+Shift+C'
      },
      { 
        name: 'petition', 
        label: 'Petição', 
        icon: '📋',
        shortcut: 'Ctrl+Shift+P'
      },
      { 
        name: 'bill', 
        label: 'Projeto de Lei', 
        icon: '📜',
        shortcut: 'Ctrl+Shift+B'
      }
    ]

    templates.forEach(template => {
      const item = document.createElement('div')
      item.className = 'px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer flex items-center gap-2'
      item.innerHTML = `
        <span>${template.icon}</span>
        <span>${template.label}</span>
        <span class="ml-auto text-xs text-gray-500">${template.shortcut}</span>
      `
      item.addEventListener('click', () => {
        this.insertTemplate(template.name)
        menu.classList.add('hidden')
      })
      menu.appendChild(item)
    })

    button.addEventListener('click', () => {
      menu.classList.toggle('hidden')
    })

    dropdown.appendChild(button)
    dropdown.appendChild(menu)
    group.appendChild(dropdown)

    this.toolbarElement.appendChild(group)
  }

  addTableGroup() {
    const group = this.createGroup('Tabela')
    
    const buttons = [
      {
        icon: '⊞',
        title: 'Inserir Tabela',
        action: () => {
          if (this.editor.editor.commands.insertTable) {
            this.editor.editor.commands.insertTable({ rows: 3, cols: 3, withHeaderRow: true })
          }
        }
      },
      {
        icon: '⊟',
        title: 'Remover Tabela',
        action: () => {
          if (this.editor.editor.commands.deleteTable) {
            this.editor.editor.commands.deleteTable()
          }
        }
      },
      {
        icon: '➕',
        title: 'Adicionar Linha',
        action: () => {
          if (this.editor.editor.commands.addRowAfter) {
            this.editor.editor.commands.addRowAfter()
          }
        }
      },
      {
        icon: '➖',
        title: 'Remover Linha',
        action: () => {
          if (this.editor.editor.commands.deleteRow) {
            this.editor.editor.commands.deleteRow()
          }
        }
      }
    ]

    buttons.forEach(button => {
      const btn = this.createButton(button.icon, button.title, button.action)
      group.appendChild(btn)
    })

    this.toolbarElement.appendChild(group)
  }

  addExportGroup() {
    const group = this.createGroup('Exportar')
    
    const buttons = [
      {
        icon: '📄',
        title: 'Exportar PDF',
        action: () => this.exportPDF()
      },
      {
        icon: '📝',
        title: 'Exportar DOCX',
        action: () => this.exportDOCX()
      },
      {
        icon: '💾',
        title: 'Salvar HTML',
        action: () => this.saveHTML()
      }
    ]

    buttons.forEach(button => {
      const btn = this.createButton(button.icon, button.title, button.action)
      group.appendChild(btn)
    })

    this.toolbarElement.appendChild(group)
  }

  addUtilitiesGroup() {
    const group = this.createGroup('Utilitários')
    
    const buttons = [
      {
        icon: '↶',
        title: 'Desfazer (Ctrl+Z)',
        action: () => this.editor.editor.commands.undo(),
        isDisabled: () => !this.editor.editor.can().undo()
      },
      {
        icon: '↷',
        title: 'Refazer (Ctrl+Y)',
        action: () => this.editor.editor.commands.redo(),
        isDisabled: () => !this.editor.editor.can().redo()
      },
      {
        icon: '🔍',
        title: 'Contar Palavras',
        action: () => this.showWordCount()
      },
      {
        icon: '⚙️',
        title: 'Configurações',
        action: () => this.showSettings()
      }
    ]

    buttons.forEach(button => {
      const btn = this.createButton(button.icon, button.title, button.action)
      if (button.isDisabled) {
        this.updateButtonDisabled(btn, button.isDisabled)
      }
      group.appendChild(btn)
    })

    this.toolbarElement.appendChild(group)
  }

  addSaveStatus() {
    const statusContainer = document.createElement('div')
    statusContainer.className = 'ml-auto flex items-center gap-2'
    
    const wordCount = document.createElement('span')
    wordCount.id = 'word-count'
    wordCount.className = 'text-sm text-gray-500'
    wordCount.textContent = '0 palavras'
    
    const saveStatus = document.createElement('span')
    saveStatus.id = 'save-status'
    saveStatus.className = 'text-sm text-gray-500'
    
    statusContainer.appendChild(wordCount)
    statusContainer.appendChild(saveStatus)
    
    this.toolbarElement.appendChild(statusContainer)
  }

  createGroup(title) {
    const group = document.createElement('div')
    group.className = 'flex items-center gap-1 px-2 py-1 border-r border-gray-300 last:border-r-0'
    group.setAttribute('data-group', title)
    return group
  }

  createButton(icon, title, action) {
    const button = document.createElement('button')
    button.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors'
    button.innerHTML = icon
    button.title = title
    button.type = 'button'
    
    if (action) {
      button.addEventListener('click', action)
    }
    
    return button
  }

  updateButtonState(button, isActiveCallback) {
    const updateState = () => {
      if (isActiveCallback()) {
        button.classList.add('bg-blue-100', 'border-blue-300')
      } else {
        button.classList.remove('bg-blue-100', 'border-blue-300')
      }
    }
    
    updateState()
    this.editor.editor.on('update', updateState)
  }

  updateButtonDisabled(button, isDisabledCallback) {
    const updateState = () => {
      if (isDisabledCallback()) {
        button.disabled = true
        button.classList.add('opacity-50', 'cursor-not-allowed')
      } else {
        button.disabled = false
        button.classList.remove('opacity-50', 'cursor-not-allowed')
      }
    }
    
    updateState()
    this.editor.editor.on('update', updateState)
  }

  setupEventListeners() {
    // Atualizar contador de palavras
    this.editor.editor.on('update', () => {
      this.updateWordCount()
    })

    // Fechar dropdowns quando clicar fora
    document.addEventListener('click', (e) => {
      const dropdowns = this.toolbarElement.querySelectorAll('.relative > div:last-child')
      dropdowns.forEach(dropdown => {
        if (!dropdown.contains(e.target) && !dropdown.previousElementSibling.contains(e.target)) {
          dropdown.classList.add('hidden')
        }
      })
    })
  }

  updateWordCount() {
    const wordCountElement = document.getElementById('word-count')
    if (wordCountElement && this.editor.editor.storage.characterCount) {
      const words = this.editor.editor.storage.characterCount.words()
      const chars = this.editor.editor.storage.characterCount.characters()
      wordCountElement.textContent = `${words} palavras, ${chars} caracteres`
    }
  }

  showWordCount() {
    const stats = {
      words: this.editor.editor.storage.characterCount?.words() || 0,
      characters: this.editor.editor.storage.characterCount?.characters() || 0,
      charactersNoSpaces: this.editor.editor.storage.characterCount?.characters({ mode: 'textSize' }) || 0
    }
    
    alert(`Estatísticas do documento:
• Palavras: ${stats.words}
• Caracteres: ${stats.characters}
• Caracteres (sem espaços): ${stats.charactersNoSpaces}`)
  }

  showSettings() {
    // Implementar modal de configurações
    alert('Configurações do editor (em desenvolvimento)')
  }

  exportPDF() {
    const content = this.editor.editor.getHTML()
    // Implementar exportação PDF
    console.log('Exportando PDF:', content)
    alert('Exportação PDF (em desenvolvimento)')
  }

  exportDOCX() {
    const content = this.editor.editor.getHTML()
    // Implementar exportação DOCX
    console.log('Exportando DOCX:', content)
    alert('Exportação DOCX (em desenvolvimento)')
  }

  saveHTML() {
    const content = this.editor.editor.getHTML()
    const blob = new Blob([content], { type: 'text/html' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = 'documento.html'
    a.click()
    URL.revokeObjectURL(url)
  }

  // Inserir toolbar antes do editor
  insertBefore(element) {
    element.parentNode.insertBefore(this.toolbarElement, element)
  }

  // Obter elemento da toolbar
  getElement() {
    return this.toolbarElement
  }

  // Inserir nível jurídico
  insertLegalLevel(level) {
    const levelTemplates = {
      'livro': '<div class="legal-livro"><h1>LIVRO II<br>DAS DISPOSIÇÕES GERAIS SOBRE EDUCAÇÃO</h1></div>',
      'capitulo': '<div class="legal-capitulo"><h2>CAPÍTULO I<br>DA EDUCAÇÃO BÁSICA</h2></div>',
      'secao': '<div class="legal-secao"><h3>Seção I<br>Dos Princípios da Educação Infantil</h3></div>',
      'artigo': '<div class="legal-artigo"><p>A Educação Básica tem por finalidade desenvolver o educando, assegurar-lhe a formação comum indispensável para o exercício da cidadania e fornecer-lhe meios para progredir no trabalho e em estudos posteriores.</p></div>',
      'paragrafo': '<div class="legal-paragrafo"><p>A Educação Básica, nos termos da lei, será organizada em:</p></div>',
      'inciso': '<div class="legal-inciso"><p>Educação Infantil;</p></div>',
      'alinea': '<div class="legal-alinea"><p>nome completo do aluno;</p></div>',
      'item': '<div class="legal-item"><p>30 (trinta) dias para alunos do ensino fundamental;</p></div>'
    }
    
    const template = levelTemplates[level]
    if (template) {
      this.editor.editor.commands.insertContent(template)
      this.editor.editor.commands.focus()
    }
  }

  // Inserir template
  insertTemplate(templateName) {
    const templates = {
      'contract': this.getContractTemplate(),
      'petition': this.getPetitionTemplate(),
      'bill': this.getBillTemplate()
    }
    
    const template = templates[templateName]
    if (template) {
      this.editor.editor.commands.setContent(template)
      this.editor.editor.commands.focus()
    } else {
      console.warn(`Template ${templateName} não encontrado`)
    }
  }

  // Template de contrato
  getContractTemplate() {
    return `
      <div class="legal-document">
        <h1 style="text-align: center; font-weight: bold; font-size: 1.25rem; margin-bottom: 1rem;">
          CONTRATO DE [TIPO DE CONTRATO]
        </h1>
        
        <div style="margin-bottom: 1.5rem;">
          <p><strong>CONTRATANTE:</strong> [NOME DO CONTRATANTE]</p>
          <p><strong>CNPJ:</strong> [CNPJ]</p>
          <p><strong>Endereço:</strong> [ENDEREÇO DO CONTRATANTE]</p>
          
          <p style="margin-top: 1rem;"><strong>CONTRATADO:</strong> [NOME DO CONTRATADO]</p>
          <p><strong>CPF:</strong> [CPF]</p>
          <p><strong>Endereço:</strong> [ENDEREÇO DO CONTRATADO]</p>
        </div>
        
        <div class="legal-artigo">
          <p><strong>CLÁUSULA PRIMEIRA - DO OBJETO</strong></p>
          <p>[DESCRIÇÃO DO OBJETO DO CONTRATO]</p>
        </div>
        
        <div class="legal-artigo">
          <p><strong>CLÁUSULA SEGUNDA - DO PRAZO</strong></p>
          <p>[PRAZO DE VIGÊNCIA DO CONTRATO]</p>
        </div>
        
        <div class="legal-artigo">
          <p><strong>CLÁUSULA TERCEIRA - DO VALOR</strong></p>
          <p>O valor total do contrato é de R$ [VALOR].</p>
        </div>
        
        <div class="legal-artigo">
          <p><strong>CLÁUSULA QUARTA - DO PAGAMENTO</strong></p>
          <p>[FORMA DE PAGAMENTO]</p>
        </div>
        
        <div class="legal-artigo">
          <p><strong>CLÁUSULA QUINTA - DAS DISPOSIÇÕES GERAIS</strong></p>
          <p>Este contrato entra em vigor na data de sua assinatura.</p>
        </div>
        
        <div style="margin-top: 2rem; text-align: center;">
          <p>[CIDADE], [DATA]</p>
          <br><br>
          <p>_________________________________</p>
          <p>[NOME DO CONTRATANTE]</p>
          <p>Contratante</p>
          <br><br>
          <p>_________________________________</p>
          <p>[NOME DO CONTRATADO]</p>
          <p>Contratado</p>
        </div>
      </div>
    `
  }

  // Template de petição
  getPetitionTemplate() {
    return `
      <div class="legal-document">
        <h1 style="text-align: center; font-weight: bold; font-size: 1.25rem; margin-bottom: 1rem;">
          [TIPO DE PETIÇÃO]
        </h1>
        
        <div style="margin-bottom: 1.5rem;">
          <p><strong>Exmo. Sr. [AUTORIDADE]</strong></p>
          <p>[ÓRGÃO]</p>
          <p>[COMARCA]</p>
        </div>
        
        <div style="margin-bottom: 1.5rem;">
          <p><strong>[NOME DO REQUERENTE]</strong>, [QUALIFICAÇÃO], 
          inscrito no CPF sob o nº [CPF], 
          residente e domiciliado [ENDEREÇO], 
          por intermédio de seu advogado que esta subscreve, 
          vem respeitosamente à presença de Vossa Excelência expor e requerer:</p>
        </div>
        
        <div style="margin-bottom: 1.5rem;">
          <h2 style="font-weight: bold; font-size: 1.1rem; margin-bottom: 0.5rem;">DOS FATOS</h2>
          <p>[DESCRIÇÃO DOS FATOS]</p>
        </div>
        
        <div style="margin-bottom: 1.5rem;">
          <h2 style="font-weight: bold; font-size: 1.1rem; margin-bottom: 0.5rem;">DO DIREITO</h2>
          <p>[FUNDAMENTAÇÃO JURÍDICA]</p>
        </div>
        
        <div style="margin-bottom: 1.5rem;">
          <h2 style="font-weight: bold; font-size: 1.1rem; margin-bottom: 0.5rem;">DO PEDIDO</h2>
          <p>Diante do exposto, requer:</p>
          <p>[PEDIDO]</p>
        </div>
        
        <p style="margin-bottom: 1.5rem;">Termos em que pede deferimento.</p>
        
        <div style="margin-top: 2rem;">
          <p>[CIDADE], [DATA]</p>
          <br><br>
          <p>_________________________________</p>
          <p>[NOME DO ADVOGADO]</p>
          <p>OAB/[ESTADO] [NÚMERO]</p>
        </div>
      </div>
    `
  }

  // Template de projeto de lei
  getBillTemplate() {
    return `
      <div class="legal-document">
        <h1 style="text-align: center; font-weight: bold; font-size: 1.5rem; margin-bottom: 1rem;">
          LEI Nº 9.394, DE 20 DE DEZEMBRO DE 1996
        </h1>
        <p style="text-align: center; font-style: italic; margin-bottom: 1.5rem; font-size: 1.1rem;">
          Estabelece as diretrizes e bases da educação nacional.
        </p>
        
        <p style="margin-bottom: 1.5rem; text-align: center; font-weight: bold;">
          O PRESIDENTE DA REPÚBLICA Faço saber que o Congresso Nacional decreta e eu sanciono a seguinte Lei:
        </p>
        
        <div class="legal-livro">
          <h1>LIVRO II<br>DAS DISPOSIÇÕES GERAIS SOBRE EDUCAÇÃO</h1>
        </div>
        
        <div class="legal-capitulo">
          <h2>CAPÍTULO I<br>DA EDUCAÇÃO BÁSICA</h2>
        </div>
        
        <div class="legal-secao">
          <h3>Seção I<br>Dos Princípios da Educação Infantil</h3>
        </div>
        
        <div class="legal-artigo">
          <p>A Educação Básica tem por finalidade desenvolver o educando, assegurar-lhe a formação comum indispensável para o exercício da cidadania e fornecer-lhe meios para progredir no trabalho e em estudos posteriores.</p>
        </div>
        
        <div class="legal-artigo">
          <p>A Educação Básica, nos termos da lei, será organizada em:</p>
          <div class="legal-paragrafo">
            <p>A Educação Básica, nos termos da lei, será organizada em:</p>
            <div class="legal-inciso">
              <p>Educação Infantil;</p>
            </div>
            <div class="legal-inciso">
              <p>Ensino Fundamental;</p>
            </div>
            <div class="legal-inciso">
              <p>Ensino Médio.</p>
            </div>
          </div>
        </div>
        
        <div class="legal-artigo">
          <p>São deveres dos estabelecimentos de ensino:</p>
          <div class="legal-inciso">
            <p>Manter instalações adequadas e seguras;</p>
          </div>
          <div class="legal-inciso">
            <p>Assegurar o cumprimento dos dias letivos e horas-aula estabelecidas;</p>
          </div>
          <div class="legal-inciso">
            <p>Expedir histórico escolar, declarações de conclusão de série e de frequência, com os dados do aluno, conforme as especificações de cada nível e modalidade de ensino:</p>
            <div class="legal-alinea">
              <p>nome completo do aluno;</p>
            </div>
            <div class="legal-alinea">
              <p>data de nascimento;</p>
            </div>
            <div class="legal-alinea">
              <p>identificação do curso e da instituição de ensino;</p>
            </div>
            <div class="legal-alinea">
              <p>registro de frequência mínima;</p>
            </div>
            <div class="legal-alinea">
              <p>notas ou conceitos de aproveitamento;</p>
            </div>
            <div class="legal-alinea">
              <p>em caso de transferência, indicar o prazo máximo para apresentação de documentos complementares, que não deverá exceder:</p>
              <div class="legal-item">
                <p>30 (trinta) dias para alunos do ensino fundamental;</p>
              </div>
              <div class="legal-item">
                <p>60 (sessenta) dias para alunos do ensino médio;</p>
              </div>
              <div class="legal-item">
                <p>90 (noventa) dias para cursos técnicos.</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="legal-artigo">
          <p>Esta lei entra em vigor na data de sua publicação.</p>
        </div>
        
        <div class="legal-artigo">
          <p>Revogam-se as disposições em contrário.</p>
        </div>
      </div>
    `
  }

  // Destruir toolbar
  destroy() {
    if (this.toolbarElement) {
      this.toolbarElement.remove()
    }
  }
}

export default LegalEditorToolbar