import { Extension } from '@tiptap/core'

/**
 * Extensão para templates jurídicos
 * Fornece comandos para inserir templates pré-definidos
 */
export const LegalTemplates = Extension.create({
  name: 'legalTemplates',

  addCommands() {
    return {
      insertContractTemplate: (data = {}) => ({ editor }) => {
        const template = this.generateContractTemplate(data)
        editor.commands.setContent(template)
        return true
      },

      insertPetitionTemplate: (data = {}) => ({ editor }) => {
        const template = this.generatePetitionTemplate(data)
        editor.commands.setContent(template)
        return true
      },

      insertBillTemplate: (data = {}) => ({ editor }) => {
        const template = this.generateBillTemplate(data)
        editor.commands.setContent(template)
        return true
      },

      insertContractClause: (clauseData = {}) => ({ editor }) => {
        const clause = this.generateContractClause(clauseData)
        editor.commands.insertContent(clause)
        return true
      },

      insertLegalParagraph: (level = 'artigo', content = '') => ({ editor }) => {
        const paragraph = this.generateLegalParagraph(level, content)
        editor.commands.insertContent(paragraph)
        return true
      },
    }
  },

  // Gerar template de contrato
  generateContractTemplate(data) {
    const defaultData = {
      type: 'PRESTAÇÃO DE SERVIÇOS',
      contractor: {
        name: '[NOME DO CONTRATANTE]',
        cnpj: '[CNPJ]',
        address: '[ENDEREÇO]'
      },
      contractee: {
        name: '[NOME DO CONTRATADO]',
        cpf: '[CPF]',
        address: '[ENDEREÇO]'
      },
      objeto: '[DESCRIÇÃO DO OBJETO]',
      prazo: '[PRAZO DE VIGÊNCIA]',
      valor: '[VALOR]',
      pagamento: '[FORMA DE PAGAMENTO]',
      cidade: '[CIDADE]',
      date: new Date().toLocaleDateString('pt-BR')
    }

    const templateData = { ...defaultData, ...data }

    return `
      <div class="legal-document">
        <h1 style="text-align: center; font-weight: bold; font-size: 1.25rem; margin-bottom: 1rem;">
          CONTRATO DE ${templateData.type}
        </h1>
        
        <div style="margin-bottom: 1.5rem;">
          <p><strong>CONTRATANTE:</strong> ${templateData.contractor.name}</p>
          <p><strong>CNPJ:</strong> ${templateData.contractor.cnpj}</p>
          <p><strong>Endereço:</strong> ${templateData.contractor.address}</p>
          
          <p style="margin-top: 1rem;"><strong>CONTRATADO:</strong> ${templateData.contractee.name}</p>
          <p><strong>CPF:</strong> ${templateData.contractee.cpf}</p>
          <p><strong>Endereço:</strong> ${templateData.contractee.address}</p>
        </div>
        
        <div class="legal-artigo">
          <p><strong>CLÁUSULA PRIMEIRA - DO OBJETO</strong></p>
          <p>${templateData.objeto}</p>
        </div>
        
        <div class="legal-artigo">
          <p><strong>CLÁUSULA SEGUNDA - DO PRAZO</strong></p>
          <p>${templateData.prazo}</p>
        </div>
        
        <div class="legal-artigo">
          <p><strong>CLÁUSULA TERCEIRA - DO VALOR</strong></p>
          <p>O valor total do contrato é de ${templateData.valor}.</p>
        </div>
        
        <div class="legal-artigo">
          <p><strong>CLÁUSULA QUARTA - DO PAGAMENTO</strong></p>
          <p>${templateData.pagamento}</p>
        </div>
        
        <div class="legal-artigo">
          <p><strong>CLÁUSULA QUINTA - DAS DISPOSIÇÕES GERAIS</strong></p>
          <p>Este contrato entra em vigor na data de sua assinatura e permanece válido até o cumprimento integral de todas as obrigações.</p>
        </div>
        
        <div style="margin-top: 2rem; text-align: center;">
          <p>${templateData.cidade}, ${templateData.date}</p>
          <div style="margin-top: 3rem; display: flex; justify-content: space-between;">
            <div style="text-align: center;">
              <div style="border-top: 1px solid #666; width: 200px; margin-top: 4rem;"></div>
              <p>${templateData.contractor.name}</p>
              <p>Contratante</p>
            </div>
            <div style="text-align: center;">
              <div style="border-top: 1px solid #666; width: 200px; margin-top: 4rem;"></div>
              <p>${templateData.contractee.name}</p>
              <p>Contratado</p>
            </div>
          </div>
        </div>
      </div>
    `
  },

  // Gerar template de petição
  generatePetitionTemplate(data) {
    const defaultData = {
      tipo_peticao: '[TIPO DE PETIÇÃO]',
      autoridade: '[AUTORIDADE]',
      orgao: '[ÓRGÃO]',
      comarca: '[COMARCA]',
      requerente: {
        name: '[NOME DO REQUERENTE]',
        qualificacao: '[QUALIFICAÇÃO]',
        cpf: '[CPF]',
        endereco: '[ENDEREÇO]'
      },
      fatos: '[DESCRIÇÃO DOS FATOS]',
      fundamento_juridico: '[FUNDAMENTAÇÃO JURÍDICA]',
      pedido: '[PEDIDO]',
      advogado: {
        nome: '[NOME DO ADVOGADO]',
        estado: '[ESTADO]',
        numero: '[NÚMERO OAB]'
      },
      cidade: '[CIDADE]',
      date: new Date().toLocaleDateString('pt-BR')
    }

    const templateData = { ...defaultData, ...data }

    return `
      <div class="legal-document">
        <h1 style="text-align: center; font-weight: bold; font-size: 1.25rem; margin-bottom: 1rem;">
          ${templateData.tipo_peticao}
        </h1>
        
        <div style="margin-bottom: 1.5rem;">
          <p><strong>Exmo. Sr. ${templateData.autoridade}</strong></p>
          <p>${templateData.orgao}</p>
          <p>${templateData.comarca}</p>
        </div>
        
        <div style="margin-bottom: 1.5rem;">
          <p><strong>${templateData.requerente.name}</strong>, ${templateData.requerente.qualificacao}, 
          inscrito no CPF sob o nº ${templateData.requerente.cpf}, 
          residente e domiciliado ${templateData.requerente.endereco}, 
          por intermédio de seu advogado que esta subscreve, 
          vem respeitosamente à presença de Vossa Excelência expor e requerer:</p>
        </div>
        
        <div style="margin-bottom: 1.5rem;">
          <h2 style="font-weight: bold; font-size: 1.1rem; margin-bottom: 0.5rem;">DOS FATOS</h2>
          <p>${templateData.fatos}</p>
        </div>
        
        <div style="margin-bottom: 1.5rem;">
          <h2 style="font-weight: bold; font-size: 1.1rem; margin-bottom: 0.5rem;">DO DIREITO</h2>
          <p>${templateData.fundamento_juridico}</p>
        </div>
        
        <div style="margin-bottom: 1.5rem;">
          <h2 style="font-weight: bold; font-size: 1.1rem; margin-bottom: 0.5rem;">DO PEDIDO</h2>
          <p>Diante do exposto, requer:</p>
          <p>${templateData.pedido}</p>
        </div>
        
        <p style="margin-bottom: 1.5rem;">Termos em que pede deferimento.</p>
        
        <div style="margin-top: 2rem;">
          <p>${templateData.cidade}, ${templateData.date}</p>
          <div style="margin-top: 2rem; text-align: center;">
            <div style="border-top: 1px solid #666; width: 200px; margin: 4rem auto 0;"></div>
            <p>${templateData.advogado.nome}</p>
            <p>OAB/${templateData.advogado.estado} ${templateData.advogado.numero}</p>
          </div>
        </div>
      </div>
    `
  },

  // Gerar template de projeto de lei
  generateBillTemplate(data) {
    const defaultData = {
      numero: '[NÚMERO]',
      ano: new Date().getFullYear(),
      ementa: '[EMENTA]',
      objeto: '[OBJETO]',
      definicao_a: '[DEFINIÇÃO A]',
      definicao_b: '[DEFINIÇÃO B]'
    }

    const templateData = { ...defaultData, ...data }

    return `
      <div class="legal-document">
        <h1 style="text-align: center; font-weight: bold; font-size: 1.25rem; margin-bottom: 1rem;">
          PROJETO DE LEI Nº ${templateData.numero}, DE ${templateData.ano}
        </h1>
        <p style="text-align: center; font-style: italic; margin-bottom: 1.5rem;">
          ${templateData.ementa}
        </p>
        
        <p style="margin-bottom: 1.5rem;"><strong>O CONGRESSO NACIONAL decreta:</strong></p>
        
        <div class="legal-artigo">
          <p>Esta lei ${templateData.objeto}.</p>
        </div>
        
        <div class="legal-artigo">
          <p>Para os efeitos desta lei, considera-se:</p>
          <div class="legal-inciso">
            <p>${templateData.definicao_a};</p>
          </div>
          <div class="legal-inciso">
            <p>${templateData.definicao_b};</p>
          </div>
        </div>
        
        <div class="legal-artigo">
          <p>Esta lei entra em vigor na data de sua publicação.</p>
        </div>
      </div>
    `
  },

  // Gerar cláusula de contrato
  generateContractClause(data) {
    const defaultData = {
      numero: '[NÚMERO]',
      titulo: '[TÍTULO]',
      conteudo: '[CONTEÚDO]'
    }

    const templateData = { ...defaultData, ...data }

    return `
      <div class="legal-artigo">
        <p><strong>CLÁUSULA ${templateData.numero} - ${templateData.titulo}</strong></p>
        <p>${templateData.conteudo}</p>
      </div>
    `
  },

  // Gerar parágrafo jurídico
  generateLegalParagraph(level, content) {
    const levelMap = {
      'artigo': 'legal-artigo',
      'paragrafo': 'legal-paragrafo',
      'inciso': 'legal-inciso',
      'alinea': 'legal-alinea',
      'item': 'legal-item'
    }

    const className = levelMap[level] || 'legal-artigo'
    
    return `
      <div class="${className}">
        <p>${content || '[CONTEÚDO]'}</p>
      </div>
    `
  },

  // Adicionar atalhos de teclado
  addKeyboardShortcuts() {
    return {
      'Mod-Shift-c': () => this.editor.commands.insertContractTemplate(),
      'Mod-Shift-p': () => this.editor.commands.insertPetitionTemplate(),
      'Mod-Shift-b': () => this.editor.commands.insertBillTemplate(),
    }
  },
})

export default LegalTemplates