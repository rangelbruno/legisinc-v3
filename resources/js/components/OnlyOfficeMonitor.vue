<template>
  <div class="onlyoffice-monitor">
    <!-- Status do Documento -->
    <div class="document-status" :class="statusClass">
      <div class="status-icon">
        <i :class="statusIcon"></i>
      </div>
      <div class="status-info">
        <h4>{{ statusTitle }}</h4>
        <p>{{ statusMessage }}</p>
        <small v-if="lastActivity">Última atividade: {{ formatTime(lastActivity) }}</small>
      </div>
    </div>

    <!-- Histórico de Callbacks -->
    <div class="callback-history" v-if="showHistory">
      <h5>📋 Histórico de Atividades</h5>
      <div class="callbacks-list">
        <div 
          v-for="callback in callbacks" 
          :key="callback.id"
          class="callback-item"
          :class="getCallbackClass(callback.status)"
        >
          <div class="callback-icon">
            <i :class="getCallbackIcon(callback.status)"></i>
          </div>
          <div class="callback-details">
            <span class="callback-status">{{ getCallbackStatusText(callback.status) }}</span>
            <span class="callback-time">{{ formatTime(callback.timestamp) }}</span>
            <div v-if="callback.validation" class="callback-validation">
              <span :class="callback.validation.valid ? 'text-success' : 'text-danger'">
                {{ callback.validation.message }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Controles -->
    <div class="monitor-controls">
      <button 
        @click="toggleHistory" 
        class="btn btn-sm btn-outline-secondary"
      >
        {{ showHistory ? 'Ocultar' : 'Mostrar' }} Histórico
      </button>
      <button 
        @click="forceRefresh" 
        class="btn btn-sm btn-outline-primary"
        :disabled="isRefreshing"
      >
        <i class="fas fa-sync" :class="{ 'fa-spin': isRefreshing }"></i>
        Atualizar
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'OnlyOfficeMonitor',
  props: {
    proposicaoId: {
      type: Number,
      required: true
    },
    documentKey: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      callbacks: [],
      showHistory: false,
      isRefreshing: false,
      lastActivity: null,
      currentStatus: 0,
      pollInterval: null
    }
  },
  computed: {
    statusClass() {
      switch (this.currentStatus) {
        case 1: return 'status-editing'
        case 2: return 'status-saved'
        case 4: return 'status-closed'
        case 6: return 'status-force-saved'
        default: return 'status-unknown'
      }
    },
    statusIcon() {
      switch (this.currentStatus) {
        case 1: return 'fas fa-edit text-primary'
        case 2: return 'fas fa-save text-success'
        case 4: return 'fas fa-times-circle text-warning'
        case 6: return 'fas fa-check-circle text-success'
        default: return 'fas fa-question-circle text-secondary'
      }
    },
    statusTitle() {
      switch (this.currentStatus) {
        case 1: return 'Editando Documento'
        case 2: return 'Documento Salvo'
        case 4: return 'Documento Fechado'
        case 6: return 'Salvamento Forçado'
        default: return 'Status Desconhecido'
      }
    },
    statusMessage() {
      switch (this.currentStatus) {
        case 1: return 'O documento está sendo editado. Suas alterações serão salvas automaticamente.'
        case 2: return 'Suas alterações foram salvas com sucesso e validadas!'
        case 4: return 'O documento foi fechado. Use "Continuar Editando" para reabrir.'
        case 6: return 'O documento foi salvo automaticamente pelo sistema.'
        default: return 'Aguardando status do documento...'
      }
    }
  },
  mounted() {
    this.startPolling()
    this.setupDocumentEventListeners()
  },
  beforeDestroy() {
    this.stopPolling()
    this.removeDocumentEventListeners()
  },
  methods: {
    startPolling() {
      // Poll de callbacks a cada 2 segundos para resposta mais rápida
      this.pollInterval = setInterval(() => {
        this.fetchCallbacks()
      }, 2000)
      
      // Fetch inicial
      this.fetchCallbacks()
    },
    
    setupDocumentEventListeners() {
      // LARAVEL BOOST: Escutar eventos customizados do OnlyOffice
      window.addEventListener('onlyoffice:ready', this.onDocumentReady)
      window.addEventListener('onlyoffice:modified', this.onDocumentModified)
      window.addEventListener('onlyoffice:saving', this.onDocumentSaving)
      window.addEventListener('onlyoffice:saved', this.onDocumentSaved)
      
      // Escutar teclas de atalho
      document.addEventListener('keydown', this.handleKeyboardShortcuts)
      
      console.log('✅ Vue.js: Event listeners configurados')
    },
    
    removeDocumentEventListeners() {
      window.removeEventListener('onlyoffice:ready', this.onDocumentReady)
      window.removeEventListener('onlyoffice:modified', this.onDocumentModified)
      window.removeEventListener('onlyoffice:saving', this.onDocumentSaving)
      window.removeEventListener('onlyoffice:saved', this.onDocumentSaved)
      document.removeEventListener('keydown', this.handleKeyboardShortcuts)
    },
    
    onDocumentReady(event) {
      console.log('📄 Documento pronto:', event.detail)
      this.currentStatus = 0
      this.lastActivity = new Date()
      this.showNotification('Documento carregado', 'info')
    },
    
    onDocumentSaving(event) {
      console.log('💾 Salvando documento:', event.detail)
      this.currentStatus = 2
      this.lastActivity = new Date()
      this.showNotification('Salvando documento...', 'info')
    },
    
    handleKeyboardShortcuts(e) {
      // Detectar Ctrl+S
      if (e.ctrlKey && e.key === 's') {
        e.preventDefault()
        this.forceSave()
      }
    },
    
    onDocumentModified(event) {
      console.log('📝 Documento modificado detectado:', event.detail)
      // Atualizar status imediatamente
      this.currentStatus = 1
      this.lastActivity = new Date()
      
      // Adicionar callback ao histórico local
      const modifiedCallback = {
        id: Date.now().toString(),
        status: 1,
        timestamp: new Date().toISOString(),
        validation: {
          valid: true,
          message: 'Documento sendo editado'
        }
      }
      
      // Adicionar ao início da lista
      this.callbacks.unshift(modifiedCallback)
      // Limitar a 20 callbacks
      this.callbacks = this.callbacks.slice(0, 20)
    },
    
    onDocumentSaved(event) {
      console.log('💾 Documento salvo detectado:', event.detail)
      // Atualizar status imediatamente
      this.currentStatus = 2
      this.lastActivity = new Date()
      
      // Adicionar callback de salvamento ao histórico
      const savedCallback = {
        id: Date.now().toString(),
        status: 2,
        timestamp: new Date().toISOString(),
        validation: {
          valid: true,
          message: 'Documento salvo com sucesso!'
        }
      }
      
      this.callbacks.unshift(savedCallback)
      this.callbacks = this.callbacks.slice(0, 20)
      
      // Buscar callbacks atualizados do servidor
      this.fetchCallbacks()
      
      // Notificação de sucesso
      this.showNotification('✅ Documento salvo com sucesso!', 'success')
      
      // Emitir evento para atualizar conteúdo
      this.$emit('content-saved', event.detail)
    },
    
    async forceSave() {
      console.log('💾 Forçando salvamento...')
      try {
        const response = await fetch(`/api/onlyoffice/force-save/${this.proposicaoId}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
          }
        })
        
        if (response.ok) {
          this.showNotification('Documento salvo com sucesso!', 'success')
          // Atualizar callbacks imediatamente
          await this.fetchCallbacks()
          // Emitir evento para atualizar conteúdo
          this.$emit('content-saved')
        }
      } catch (error) {
        console.error('Erro ao forçar salvamento:', error)
        this.showNotification('Erro ao salvar documento', 'error')
      }
    },
    
    showNotification(message, type = 'info') {
      // Criar notificação toast
      const toast = document.createElement('div')
      toast.className = `toast-notification toast-${type}`
      toast.textContent = message
      toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        background: ${type === 'success' ? '#4caf50' : type === 'error' ? '#f44336' : '#2196f3'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 10000;
        animation: slideIn 0.3s ease;
      `
      document.body.appendChild(toast)
      
      // Remover após 3 segundos
      setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease'
        setTimeout(() => toast.remove(), 300)
      }, 3000)
    },
    
    stopPolling() {
      if (this.pollInterval) {
        clearInterval(this.pollInterval)
        this.pollInterval = null
      }
    },
    
    async fetchCallbacks() {
      try {
        const response = await fetch(`/api/onlyoffice/callbacks/${this.proposicaoId}`)
        const data = await response.json()
        
        if (data.success) {
          this.callbacks = data.callbacks
          if (data.callbacks.length > 0) {
            this.currentStatus = data.callbacks[0].status
            this.lastActivity = data.callbacks[0].timestamp
          }
        }
      } catch (error) {
        console.error('Erro ao buscar callbacks:', error)
      }
    },
    
    async forceRefresh() {
      this.isRefreshing = true
      try {
        await this.fetchCallbacks()
        // Emitir evento para componente pai atualizar
        this.$emit('refresh-content')
      } finally {
        this.isRefreshing = false
      }
    },
    
    toggleHistory() {
      this.showHistory = !this.showHistory
    },
    
    getCallbackClass(status) {
      switch (status) {
        case 1: return 'callback-editing'
        case 2: return 'callback-saved'
        case 4: return 'callback-closed'
        case 6: return 'callback-force-saved'
        default: return 'callback-unknown'
      }
    },
    
    getCallbackIcon(status) {
      switch (status) {
        case 1: return 'fas fa-edit'
        case 2: return 'fas fa-save'
        case 4: return 'fas fa-times-circle'
        case 6: return 'fas fa-check-circle'
        default: return 'fas fa-question'
      }
    },
    
    getCallbackStatusText(status) {
      switch (status) {
        case 1: return 'Documento Aberto'
        case 2: return 'Documento Salvo'
        case 4: return 'Documento Fechado'
        case 6: return 'Salvamento Forçado'
        default: return `Status ${status}`
      }
    },
    
    formatTime(timestamp) {
      if (!timestamp) return ''
      return new Date(timestamp).toLocaleString('pt-BR', {
        day: '2-digit',
        month: '2-digit', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      })
    }
  }
}
</script>

<style scoped>
@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@keyframes slideOut {
  from {
    transform: translateX(0);
    opacity: 1;
  }
  to {
    transform: translateX(100%);
    opacity: 0;
  }
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}

.onlyoffice-monitor {
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 20px;
  margin: 20px 0;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
}

.onlyoffice-monitor:hover {
  box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.document-status {
  display: flex;
  align-items: center;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 20px;
  transition: all 0.3s ease;
}

.status-editing { background-color: #e3f2fd; border-left: 4px solid #2196f3; }
.status-saved { background-color: #e8f5e8; border-left: 4px solid #4caf50; }
.status-closed { background-color: #fff3e0; border-left: 4px solid #ff9800; }
.status-unknown { background-color: #f5f5f5; border-left: 4px solid #9e9e9e; }

.status-icon {
  margin-right: 15px;
  font-size: 24px;
}

.status-info h4 {
  margin: 0 0 5px 0;
  font-size: 18px;
  font-weight: 600;
}

.status-info p {
  margin: 0 0 5px 0;
  color: #666;
}

.status-info small {
  color: #999;
  font-size: 12px;
}

.callback-history {
  border-top: 1px solid #eee;
  padding-top: 20px;
}

.callbacks-list {
  max-height: 200px;
  overflow-y: auto;
  border: 1px solid #f0f0f0;
  border-radius: 4px;
}

.callback-item {
  display: flex;
  align-items: center;
  padding: 10px;
  border-bottom: 1px solid #f8f8f8;
  transition: background-color 0.2s ease;
}

.callback-item:hover {
  background-color: #f9f9f9;
}

.callback-item:last-child {
  border-bottom: none;
}

.callback-icon {
  margin-right: 12px;
  width: 20px;
  text-align: center;
}

.callback-editing { background-color: #f3f8ff; }
.callback-saved { background-color: #f0fff4; }
.callback-closed { background-color: #fffaf0; }

.callback-details {
  flex: 1;
}

.callback-status {
  font-weight: 500;
  margin-right: 10px;
}

.callback-time {
  font-size: 12px;
  color: #666;
}

.callback-validation {
  margin-top: 4px;
  font-size: 12px;
}

.monitor-controls {
  display: flex;
  gap: 10px;
  margin-top: 15px;
  padding-top: 15px;
  border-top: 1px solid #eee;
}

.text-success { color: #4caf50; }
.text-danger { color: #f44336; }
</style>