/**
 * Sistema de Numeração Jurídica Brasileira
 * Implementa a hierarquia: Artigo > Parágrafo > Inciso > Alínea > Item
 */
export class BrazilianLegalNumbering {
  constructor() {
    this.counters = {
      artigo: 0,
      paragrafo: 0,
      inciso: 0,
      alinea: 0,
      item: 0,
    }
    this.setupCSS()
  }

  setupCSS() {
    // Verificar se o CSS já foi adicionado
    if (document.getElementById('legal-numbering-css')) {
      return
    }

    const style = document.createElement('style')
    style.id = 'legal-numbering-css'
    style.textContent = `
      .legal-document {
        counter-reset: artigo paragrafo inciso alinea item;
      }
      
      .legal-artigo {
        counter-increment: artigo;
        counter-reset: paragrafo;
        font-weight: bold;
        font-size: 1.1rem;
        margin-bottom: 1rem;
        border-left: 4px solid #3b82f6;
        padding-left: 12px;
      }
      
      .legal-artigo::before {
        content: "Art. " counter(artigo) "º ";
        color: #3b82f6;
        font-weight: bold;
      }
      
      .legal-paragrafo {
        counter-increment: paragrafo;
        counter-reset: inciso;
        font-weight: 600;
        margin-left: 1rem;
        margin-bottom: 0.75rem;
        border-left: 3px solid #6b7280;
        padding-left: 12px;
      }
      
      .legal-paragrafo::before {
        content: "§ " counter(paragrafo) "º ";
        color: #6b7280;
        font-weight: bold;
      }
      
      .legal-inciso {
        counter-increment: inciso;
        counter-reset: alinea;
        margin-left: 2rem;
        margin-bottom: 0.5rem;
        border-left: 2px solid #9ca3af;
        padding-left: 12px;
      }
      
      .legal-inciso::before {
        content: counter(inciso, upper-roman) " – ";
        color: #9ca3af;
        font-weight: bold;
      }
      
      .legal-alinea {
        counter-increment: alinea;
        counter-reset: item;
        margin-left: 3rem;
        margin-bottom: 0.375rem;
        border-left: 1px solid #d1d5db;
        padding-left: 12px;
      }
      
      .legal-alinea::before {
        content: counter(alinea, lower-alpha) ") ";
        color: #d1d5db;
        font-weight: bold;
      }
      
      .legal-item {
        counter-increment: item;
        margin-left: 4rem;
        margin-bottom: 0.25rem;
        padding-left: 12px;
      }
      
      .legal-item::before {
        content: counter(item) ". ";
        color: #6b7280;
        font-weight: bold;
      }
      
      /* Estilos para hover e foco */
      .legal-artigo:hover {
        background-color: #eff6ff;
        border-radius: 4px;
      }
      
      .legal-paragrafo:hover {
        background-color: #f9fafb;
        border-radius: 4px;
      }
      
      .legal-inciso:hover {
        background-color: #f3f4f6;
        border-radius: 4px;
      }
      
      .legal-alinea:hover {
        background-color: #f9fafb;
        border-radius: 4px;
      }
      
      .legal-item:hover {
        background-color: #f9fafb;
        border-radius: 4px;
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
    `
    
    document.head.appendChild(style)
  }

  // Formatar número baseado no tipo
  formatNumber(type, number) {
    switch (type) {
      case 'artigo':
        return number <= 9 ? `${number}º` : `${number}`
      case 'paragrafo':
        return number <= 9 ? `§ ${number}º` : `§ ${number}`
      case 'inciso':
        return this.toRoman(number)
      case 'alinea':
        return String.fromCharCode(96 + number) + ')' // a), b), c)...
      case 'item':
        return `${number}.`
      default:
        return number.toString()
    }
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

  // Obter hierarquia de níveis
  getLevelHierarchy() {
    return ['artigo', 'paragrafo', 'inciso', 'alinea', 'item']
  }

  // Verificar se um nível é superior a outro
  isLevelHigher(level1, level2) {
    const hierarchy = this.getLevelHierarchy()
    return hierarchy.indexOf(level1) < hierarchy.indexOf(level2)
  }

  // Obter o nível pai
  getParentLevel(level) {
    const hierarchy = this.getLevelHierarchy()
    const index = hierarchy.indexOf(level)
    return index > 0 ? hierarchy[index - 1] : null
  }

  // Obter o nível filho
  getChildLevel(level) {
    const hierarchy = this.getLevelHierarchy()
    const index = hierarchy.indexOf(level)
    return index < hierarchy.length - 1 ? hierarchy[index + 1] : null
  }

  // Resetar contadores de níveis inferiores
  resetLowerCounters(level) {
    const hierarchy = this.getLevelHierarchy()
    const currentIndex = hierarchy.indexOf(level)
    
    // Resetar todos os níveis inferiores
    for (let i = currentIndex + 1; i < hierarchy.length; i++) {
      this.counters[hierarchy[i]] = 0
    }
  }

  // Incrementar contador de um nível
  incrementCounter(level) {
    this.resetLowerCounters(level)
    this.counters[level]++
    return this.counters[level]
  }

  // Obter contador atual
  getCounter(level) {
    return this.counters[level]
  }

  // Resetar todos os contadores
  resetAllCounters() {
    Object.keys(this.counters).forEach(level => {
      this.counters[level] = 0
    })
  }

  // Obter texto formatado para um nível
  getFormattedText(level, number = null) {
    const actualNumber = number || this.counters[level]
    
    switch (level) {
      case 'artigo':
        return `Art. ${actualNumber}º`
      case 'paragrafo':
        return `§ ${actualNumber}º`
      case 'inciso':
        return `${this.toRoman(actualNumber)} –`
      case 'alinea':
        return `${String.fromCharCode(96 + actualNumber)})`
      case 'item':
        return `${actualNumber}.`
      default:
        return actualNumber.toString()
    }
  }

  // Validar estrutura hierárquica
  validateHierarchy(elements) {
    const hierarchy = this.getLevelHierarchy()
    const errors = []
    
    let currentLevels = {}
    
    elements.forEach((element, index) => {
      const level = element.level
      const levelIndex = hierarchy.indexOf(level)
      
      if (levelIndex === -1) {
        errors.push(`Nível inválido '${level}' no elemento ${index + 1}`)
        return
      }
      
      // Verificar se há níveis intermediários faltando
      for (let i = 0; i < levelIndex; i++) {
        if (!currentLevels[hierarchy[i]]) {
          errors.push(`Nível '${hierarchy[i]}' faltando antes do '${level}' no elemento ${index + 1}`)
        }
      }
      
      currentLevels[level] = true
    })
    
    return {
      valid: errors.length === 0,
      errors
    }
  }

  // Gerar estrutura de numeração automática
  generateNumbering(elements) {
    this.resetAllCounters()
    
    return elements.map(element => {
      const level = element.level
      const number = this.incrementCounter(level)
      
      return {
        ...element,
        number,
        formattedNumber: this.getFormattedText(level, number),
        className: `legal-${level}`,
        style: this.getLevelStyle(level)
      }
    })
  }

  // Obter estilo CSS para um nível
  getLevelStyle(level) {
    const styles = {
      artigo: {
        fontWeight: 'bold',
        fontSize: '1.1rem',
        marginBottom: '1rem',
        borderLeft: '4px solid #3b82f6',
        paddingLeft: '12px'
      },
      paragrafo: {
        fontWeight: '600',
        marginLeft: '1rem',
        marginBottom: '0.75rem',
        borderLeft: '3px solid #6b7280',
        paddingLeft: '12px'
      },
      inciso: {
        marginLeft: '2rem',
        marginBottom: '0.5rem',
        borderLeft: '2px solid #9ca3af',
        paddingLeft: '12px'
      },
      alinea: {
        marginLeft: '3rem',
        marginBottom: '0.375rem',
        borderLeft: '1px solid #d1d5db',
        paddingLeft: '12px'
      },
      item: {
        marginLeft: '4rem',
        marginBottom: '0.25rem',
        paddingLeft: '12px'
      }
    }
    
    return styles[level] || {}
  }

  // Exportar configuração atual
  exportConfiguration() {
    return {
      counters: { ...this.counters },
      hierarchy: this.getLevelHierarchy(),
      timestamp: new Date().toISOString()
    }
  }

  // Importar configuração
  importConfiguration(config) {
    if (config.counters) {
      this.counters = { ...config.counters }
    }
  }
}

export default BrazilianLegalNumbering