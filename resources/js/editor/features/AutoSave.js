/**
 * Sistema de Auto-Save com debouncing
 * Salva automaticamente o conteúdo do editor após um delay
 */
export class AutoSave {
  constructor(editor, saveCallback, delay = 2000) {
    this.editor = editor
    this.saveCallback = saveCallback
    this.delay = delay
    this.timeoutId = null
    this.isActive = true
    this.lastSavedContent = ''
    this.setupAutoSave()
  }

  setupAutoSave() {
    this.editor.on('update', () => {
      if (this.isActive) {
        this.debouncedSave()
      }
    })
  }

  debouncedSave() {
    if (this.timeoutId) {
      clearTimeout(this.timeoutId)
    }

    this.timeoutId = setTimeout(() => {
      this.save()
    }, this.delay)
  }

  async save() {
    try {
      const content = this.editor.getHTML()
      
      // Evitar salvar se o conteúdo não mudou
      if (content === this.lastSavedContent) {
        return
      }

      this.showSaveStatus('Salvando...', 'saving')
      
      await this.saveCallback(content)
      
      this.lastSavedContent = content
      this.showSaveStatus('Salvo automaticamente', 'success')
      
      // Disparar evento customizado
      this.editor.emit('autoSave:success', { content })
      
    } catch (error) {
      console.error('Erro ao salvar:', error)
      this.showSaveStatus('Erro ao salvar', 'error')
      this.editor.emit('autoSave:error', { error })
    }
  }

  showSaveStatus(message, type = 'success') {
    const statusElement = document.getElementById('save-status')
    if (statusElement) {
      statusElement.textContent = message
      statusElement.className = `save-status ${type}`
      
      // Remover status após 3 segundos
      setTimeout(() => {
        if (statusElement.textContent === message) {
          statusElement.textContent = ''
          statusElement.className = 'save-status'
        }
      }, 3000)
    }

    // Também emitir evento para componentes que queiram escutar
    this.editor.emit('autoSave:status', { message, type })
  }

  // Salvar imediatamente
  async saveNow() {
    if (this.timeoutId) {
      clearTimeout(this.timeoutId)
    }
    await this.save()
  }

  // Pausar auto-save
  pause() {
    this.isActive = false
    if (this.timeoutId) {
      clearTimeout(this.timeoutId)
    }
  }

  // Reativar auto-save
  resume() {
    this.isActive = true
  }

  // Alterar delay
  setDelay(newDelay) {
    this.delay = newDelay
  }

  // Verificar se há mudanças não salvas
  hasUnsavedChanges() {
    return this.editor.getHTML() !== this.lastSavedContent
  }

  // Limpar recursos
  destroy() {
    if (this.timeoutId) {
      clearTimeout(this.timeoutId)
    }
    this.isActive = false
  }
}

export default AutoSave