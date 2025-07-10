/**
 * Engine de Templates para Documentos Jurídicos
 * Processa templates com variáveis, helpers e lógica condicional
 */
export class LegalTemplateEngine {
  constructor() {
    this.templates = new Map()
    this.helpers = new Map()
    this.setupDefaultHelpers()
  }

  setupDefaultHelpers() {
    // Formatação de data
    this.helpers.set('formatDate', (date) => {
      if (!date) return ''
      const d = new Date(date)
      return d.toLocaleDateString('pt-BR')
    })

    // Formatação de moeda
    this.helpers.set('formatCurrency', (value) => {
      if (!value) return ''
      return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
      }).format(value)
    })

    // Formatação de CPF
    this.helpers.set('formatCPF', (cpf) => {
      if (!cpf) return ''
      const cleanCPF = cpf.replace(/\D/g, '')
      return cleanCPF.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4')
    })

    // Formatação de CNPJ
    this.helpers.set('formatCNPJ', (cnpj) => {
      if (!cnpj) return ''
      const cleanCNPJ = cnpj.replace(/\D/g, '')
      return cleanCNPJ.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5')
    })

    // Formatação de CEP
    this.helpers.set('formatCEP', (cep) => {
      if (!cep) return ''
      const cleanCEP = cep.replace(/\D/g, '')
      return cleanCEP.replace(/(\d{5})(\d{3})/, '$1-$2')
    })

    // Capitalizar texto
    this.helpers.set('capitalize', (text) => {
      if (!text) return ''
      return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase()
    })

    // Texto em maiúsculas
    this.helpers.set('uppercase', (text) => {
      if (!text) return ''
      return text.toUpperCase()
    })

    // Texto em minúsculas
    this.helpers.set('lowercase', (text) => {
      if (!text) return ''
      return text.toLowerCase()
    })

    // Formatação de número ordinal
    this.helpers.set('ordinal', (number) => {
      if (!number) return ''
      return number <= 9 ? `${number}º` : `${number}`
    })

    // Formatação de número romano
    this.helpers.set('roman', (number) => {
      if (!number) return ''
      return this.toRoman(number)
    })

    // Formatação de número para letra (a, b, c...)
    this.helpers.set('alpha', (number) => {
      if (!number) return ''
      return String.fromCharCode(96 + number)
    })
  }

  // Registrar template
  registerTemplate(name, template) {
    this.templates.set(name, template)
  }

  // Registrar helper customizado
  registerHelper(name, helperFunction) {
    this.helpers.set(name, helperFunction)
  }

  // Renderizar template
  render(templateName, data = {}) {
    const template = this.templates.get(templateName)
    if (!template) {
      throw new Error(`Template '${templateName}' não encontrado`)
    }

    return this.processTemplate(template, data)
  }

  // Processar template com dados
  processTemplate(template, data) {
    let processed = template

    // Processar variáveis simples: {{variavel}}
    processed = processed.replace(/\{\{([^}]+)\}\}/g, (match, expression) => {
      const trimmed = expression.trim()
      
      // Verificar se é um helper
      const spaceIndex = trimmed.indexOf(' ')
      if (spaceIndex > 0) {
        const helperName = trimmed.substring(0, spaceIndex)
        const helperArg = trimmed.substring(spaceIndex + 1)
        
        const helper = this.helpers.get(helperName)
        if (helper) {
          const value = this.getNestedValue(data, helperArg)
          return helper(value)
        }
      }
      
      // Variável simples
      return this.getNestedValue(data, trimmed) || ''
    })

    // Processar condicionais: {{#if variavel}}...{{/if}}
    processed = processed.replace(/\{\{#if\s+([^}]+)\}\}(.*?)\{\{\/if\}\}/gs, (match, condition, content) => {
      const value = this.getNestedValue(data, condition.trim())
      return value ? content : ''
    })

    // Processar condicionais com else: {{#if variavel}}...{{else}}...{{/if}}
    processed = processed.replace(/\{\{#if\s+([^}]+)\}\}(.*?)\{\{else\}\}(.*?)\{\{\/if\}\}/gs, (match, condition, ifContent, elseContent) => {
      const value = this.getNestedValue(data, condition.trim())
      return value ? ifContent : elseContent
    })

    // Processar loops: {{#each array}}...{{/each}}
    processed = processed.replace(/\{\{#each\s+([^}]+)\}\}(.*?)\{\{\/each\}\}/gs, (match, arrayName, content) => {
      const array = this.getNestedValue(data, arrayName.trim())
      if (!Array.isArray(array)) return ''
      
      return array.map((item, index) => {
        const itemData = {
          ...data,
          '@index': index,
          '@first': index === 0,
          '@last': index === array.length - 1,
          '@length': array.length,
          ...item
        }
        return this.processTemplate(content, itemData)
      }).join('')
    })

    return processed
  }

  // Obter valor aninhado de objeto (ex: user.name)
  getNestedValue(obj, path) {
    return path.split('.').reduce((current, key) => {
      return current && current[key] !== undefined ? current[key] : null
    }, obj)
  }

  // Converter número para romano
  toRoman(num) {
    const values = [1000, 900, 500, 400, 100, 90, 50, 40, 10, 9, 5, 4, 1]
    const symbols = ['M', 'CM', 'D', 'CD', 'C', 'XC', 'L', 'XL', 'X', 'IX', 'V', 'IV', 'I']
    let roman = ''
    
    for (let i = 0; i < values.length; i++) {
      while (num >= values[i]) {
        roman += symbols[i]
        num -= values[i]
      }
    }
    
    return roman
  }

  // Listar templates registrados
  getTemplateNames() {
    return Array.from(this.templates.keys())
  }

  // Obter template
  getTemplate(name) {
    return this.templates.get(name)
  }

  // Remover template
  removeTemplate(name) {
    return this.templates.delete(name)
  }

  // Listar helpers registrados
  getHelperNames() {
    return Array.from(this.helpers.keys())
  }

  // Validar template (verificar sintaxe)
  validateTemplate(template) {
    const errors = []
    
    // Verificar se todas as tags estão fechadas
    const openTags = template.match(/\{\{#\w+/g) || []
    const closeTags = template.match(/\{\{\/\w+/g) || []
    
    if (openTags.length !== closeTags.length) {
      errors.push('Tags não balanceadas')
    }
    
    // Verificar helpers inválidos
    const helperMatches = template.match(/\{\{(\w+)\s+[^}]+\}\}/g) || []
    helperMatches.forEach(match => {
      const helperName = match.match(/\{\{(\w+)/)[1]
      if (!this.helpers.has(helperName)) {
        errors.push(`Helper '${helperName}' não encontrado`)
      }
    })
    
    return {
      valid: errors.length === 0,
      errors
    }
  }

  // Criar template a partir de dados de exemplo
  createTemplateFromExample(exampleData) {
    const template = JSON.stringify(exampleData, null, 2)
    return template.replace(/"([^"]+)"/g, (match, value) => {
      // Converter valores em variáveis de template
      if (typeof value === 'string' && value.length > 0) {
        return `"{{${value.toLowerCase().replace(/\s+/g, '_')}}}"`
      }
      return match
    })
  }
}

export default LegalTemplateEngine