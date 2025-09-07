<template>
  <!-- Botão Flutuante Principal -->
  <div class="user-action-logger">
    <!-- Toggle Button -->
    <button 
      @click="toggleLogger" 
      :class="['logger-toggle', { active: isActive, recording: isRecording }]"
      title="Debug Logger - Clique para ativar/desativar"
    >
      <i :class="isRecording ? 'fas fa-stop' : 'fas fa-bug'"></i>
      <span v-if="isRecording" class="pulse"></span>
    </button>

    <!-- Debug Panel -->
    <transition name="slide-up">
      <div v-if="showPanel" class="debug-panel">
        <div class="panel-header">
          <h4>
            <i class="fas fa-bug"></i>
            User Action Debug Logger
            <span v-if="isRecording" class="recording-indicator">● REC</span>
          </h4>
          <div class="panel-controls">
            <button @click="startRecording" :disabled="isRecording" class="btn-start">
              <i class="fas fa-play"></i> Iniciar
            </button>
            <button @click="stopRecording" :disabled="!isRecording" class="btn-stop">
              <i class="fas fa-stop"></i> Parar
            </button>
            <button @click="clearLog" class="btn-clear">
              <i class="fas fa-trash"></i> Limpar
            </button>
            <button @click="copyLog" class="btn-copy">
              <i class="fas fa-copy"></i> Copiar
            </button>
            <button @click="showPanel = false" class="btn-minimize">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>

        <div class="panel-content">
          <!-- Session Info -->
          <div class="session-info">
            <div class="info-item">
              <strong>Usuário:</strong> {{ userInfo.name }} ({{ userInfo.email }})
            </div>
            <div class="info-item">
              <strong>Role:</strong> {{ userInfo.role }}
            </div>
            <div class="info-item">
              <strong>Sessão:</strong> {{ sessionId }}
            </div>
            <div class="info-item">
              <strong>Página:</strong> {{ currentPage }}
            </div>
            <div class="info-item">
              <strong>Ações:</strong> {{ actionLog.length }}
            </div>
          </div>

          <!-- Filter Controls -->
          <div class="filter-controls">
            <label>
              <input type="checkbox" v-model="filters.clicks"> Cliques
            </label>
            <label>
              <input type="checkbox" v-model="filters.navigation"> Navegação
            </label>
            <label>
              <input type="checkbox" v-model="filters.requests"> Requests
            </label>
            <label>
              <input type="checkbox" v-model="filters.errors"> Erros
            </label>
            <label>
              <input type="checkbox" v-model="filters.forms"> Forms
            </label>
          </div>

          <!-- Action Log -->
          <div class="action-log">
            <div 
              v-for="(action, index) in filteredActions" 
              :key="index"
              :class="['log-entry', `type-${action.type}`, { error: action.error }]"
            >
              <div class="log-timestamp">{{ formatTime(action.timestamp) }}</div>
              <div class="log-type">
                <i :class="getTypeIcon(action.type)"></i>
                {{ action.type }}
              </div>
              <div class="log-description">{{ action.description }}</div>
              <div v-if="action.data" class="log-data">
                <button @click="toggleDetails(index)" class="btn-details">
                  <i :class="action.showDetails ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
                </button>
                <pre v-if="action.showDetails" class="log-details">{{ formatData(action.data) }}</pre>
              </div>
            </div>
            
            <div v-if="filteredActions.length === 0" class="no-actions">
              {{ isRecording ? 'Aguardando ações...' : 'Clique em "Iniciar" para começar a gravar' }}
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'UserActionLogger',
  data() {
    return {
      isActive: false,
      isRecording: false,
      showPanel: false,
      sessionId: this.generateSessionId(),
      currentPage: window.location.pathname,
      userInfo: window.userInfo || { name: 'Unknown', email: '', role: '' },
      actionLog: [],
      filters: {
        clicks: true,
        navigation: true,
        requests: true,
        errors: true,
        forms: true
      },
      listeners: []
    }
  },

  computed: {
    filteredActions() {
      return this.actionLog.filter(action => {
        return this.filters[action.category] || false;
      });
    }
  },

  mounted() {
    this.initializeLogger();
    this.loadUserInfo();
  },

  beforeDestroy() {
    this.removeAllListeners();
  },

  methods: {
    toggleLogger() {
      this.isActive = !this.isActive;
      this.showPanel = this.isActive;
      
      if (!this.isActive) {
        this.stopRecording();
      }
    },

    startRecording() {
      this.isRecording = true;
      this.addActionListeners();
      this.logAction('system', 'Gravação iniciada', { 
        page: this.currentPage,
        user: this.userInfo.email,
        timestamp: new Date().toISOString()
      });
    },

    stopRecording() {
      this.isRecording = false;
      this.removeAllListeners();
      this.logAction('system', 'Gravação finalizada', {
        duration: this.getSessionDuration(),
        totalActions: this.actionLog.length
      });
    },

    clearLog() {
      this.actionLog = [];
      this.sessionId = this.generateSessionId();
    },

    copyLog() {
      const logText = this.generateLogReport();
      navigator.clipboard.writeText(logText).then(() => {
        this.$toast.success('Log copiado para área de transferência!');
      });
    },

    addActionListeners() {
      // Cliques em elementos
      this.addListener('click', document, (e) => {
        if (e.target.closest('.user-action-logger')) return; // Ignorar cliques no próprio logger
        
        const element = e.target;
        const description = this.getElementDescription(element);
        
        this.logAction('click', `Clique em: ${description}`, {
          element: element.tagName,
          id: element.id,
          class: element.className,
          text: element.textContent?.slice(0, 50),
          coordinates: { x: e.clientX, y: e.clientY }
        });
      });

      // Submissão de formulários
      this.addListener('submit', document, (e) => {
        const form = e.target;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        this.logAction('form', `Submissão de formulário: ${form.id || form.action}`, {
          action: form.action,
          method: form.method,
          fields: Object.keys(data)
        });
      });

      // Mudanças de página (popstate)
      this.addListener('popstate', window, (e) => {
        this.currentPage = window.location.pathname;
        this.logAction('navigation', `Navegação para: ${this.currentPage}`, {
          url: window.location.href,
          state: e.state
        });
      });

      // Interceptar requisições AJAX/Fetch
      this.interceptAjaxRequests();
      this.interceptFetchRequests();
    },

    addListener(event, target, handler) {
      target.addEventListener(event, handler);
      this.listeners.push({ event, target, handler });
    },

    removeAllListeners() {
      this.listeners.forEach(({ event, target, handler }) => {
        target.removeEventListener(event, handler);
      });
      this.listeners = [];
    },

    interceptAjaxRequests() {
      const originalOpen = XMLHttpRequest.prototype.open;
      const originalSend = XMLHttpRequest.prototype.send;
      
      XMLHttpRequest.prototype.open = function(method, url, ...args) {
        this._debugMethod = method;
        this._debugUrl = url;
        return originalOpen.apply(this, [method, url, ...args]);
      };

      XMLHttpRequest.prototype.send = function(data) {
        const xhr = this;
        const startTime = Date.now();

        xhr.addEventListener('load', () => {
          const duration = Date.now() - startTime;
          const logger = document.querySelector('.user-action-logger').__vue__;
          
          if (logger && logger.isRecording) {
            logger.logAction('request', `${xhr._debugMethod} ${xhr._debugUrl}`, {
              method: xhr._debugMethod,
              url: xhr._debugUrl,
              status: xhr.status,
              statusText: xhr.statusText,
              duration: `${duration}ms`,
              responseSize: xhr.responseText?.length || 0
            });
          }
        });

        xhr.addEventListener('error', () => {
          const logger = document.querySelector('.user-action-logger').__vue__;
          
          if (logger && logger.isRecording) {
            logger.logAction('request', `ERRO: ${xhr._debugMethod} ${xhr._debugUrl}`, {
              method: xhr._debugMethod,
              url: xhr._debugUrl,
              error: true,
              status: xhr.status,
              statusText: xhr.statusText
            }, true);
          }
        });

        return originalSend.apply(this, arguments);
      };
    },

    interceptFetchRequests() {
      const originalFetch = window.fetch;
      
      window.fetch = function(...args) {
        const [url, options = {}] = args;
        const method = options.method || 'GET';
        const startTime = Date.now();

        return originalFetch.apply(this, args)
          .then(response => {
            const duration = Date.now() - startTime;
            const logger = document.querySelector('.user-action-logger').__vue__;
            
            if (logger && logger.isRecording) {
              logger.logAction('request', `${method} ${url}`, {
                method,
                url,
                status: response.status,
                statusText: response.statusText,
                duration: `${duration}ms`,
                ok: response.ok
              }, !response.ok);
            }
            
            return response;
          })
          .catch(error => {
            const logger = document.querySelector('.user-action-logger').__vue__;
            
            if (logger && logger.isRecording) {
              logger.logAction('request', `ERRO: ${method} ${url}`, {
                method,
                url,
                error: error.message,
                duration: `${Date.now() - startTime}ms`
              }, true);
            }
            
            throw error;
          });
      };
    },

    logAction(type, description, data = null, isError = false) {
      if (!this.isRecording) return;

      const action = {
        id: Date.now() + Math.random(),
        timestamp: new Date(),
        type,
        category: this.getCategoryFromType(type),
        description,
        data,
        error: isError,
        showDetails: false
      };

      this.actionLog.push(action);

      // Auto-scroll para a última ação
      this.$nextTick(() => {
        const logContainer = this.$el.querySelector('.action-log');
        if (logContainer) {
          logContainer.scrollTop = logContainer.scrollHeight;
        }
      });
    },

    getCategoryFromType(type) {
      const categoryMap = {
        'click': 'clicks',
        'navigation': 'navigation',
        'request': 'requests',
        'form': 'forms',
        'system': 'system',
        'error': 'errors'
      };
      return categoryMap[type] || 'system';
    },

    getElementDescription(element) {
      if (element.id) return `#${element.id}`;
      if (element.className) return `.${element.className.split(' ')[0]}`;
      if (element.textContent?.trim()) return `"${element.textContent.trim().slice(0, 30)}"`;
      return `${element.tagName.toLowerCase()}`;
    },

    getTypeIcon(type) {
      const icons = {
        click: 'fas fa-mouse-pointer',
        navigation: 'fas fa-compass',
        request: 'fas fa-exchange-alt',
        form: 'fas fa-edit',
        system: 'fas fa-cog',
        error: 'fas fa-exclamation-triangle'
      };
      return icons[type] || 'fas fa-info';
    },

    toggleDetails(index) {
      this.actionLog[index].showDetails = !this.actionLog[index].showDetails;
    },

    formatTime(timestamp) {
      return timestamp.toLocaleTimeString('pt-BR', { 
        hour12: false, 
        millisecond: true 
      });
    },

    formatData(data) {
      return JSON.stringify(data, null, 2);
    },

    generateLogReport() {
      const header = `
🎯 USER ACTION DEBUG LOG
========================
📅 Data: ${new Date().toLocaleString('pt-BR')}
👤 Usuário: ${this.userInfo.name} (${this.userInfo.email})
🏷️  Role: ${this.userInfo.role}
📄 Página: ${this.currentPage}
🔑 Sessão: ${this.sessionId}
📊 Total de Ações: ${this.actionLog.length}

SEQUÊNCIA DE AÇÕES:
==================
`;

      const actions = this.actionLog.map((action, index) => {
        const time = this.formatTime(action.timestamp);
        const errorFlag = action.error ? ' ❌' : '';
        let entry = `${index + 1}. [${time}] ${action.type.toUpperCase()}: ${action.description}${errorFlag}`;
        
        if (action.data) {
          entry += `\n   Dados: ${JSON.stringify(action.data)}`;
        }
        
        return entry;
      }).join('\n\n');

      return header + actions + '\n\n=== FIM DO LOG ===';
    },

    generateSessionId() {
      return Date.now().toString(36) + Math.random().toString(36).substr(2);
    },

    getSessionDuration() {
      if (this.actionLog.length === 0) return '0s';
      
      const firstAction = this.actionLog[0];
      const lastAction = this.actionLog[this.actionLog.length - 1];
      const duration = lastAction.timestamp - firstAction.timestamp;
      
      return `${Math.round(duration / 1000)}s`;
    },

    initializeLogger() {
      // Auto-ativar em desenvolvimento
      if (process.env.NODE_ENV === 'development') {
        this.isActive = false; // Desabilitado por padrão
      }

      // Listener para erros JavaScript
      window.addEventListener('error', (e) => {
        if (this.isRecording) {
          this.logAction('error', `JavaScript Error: ${e.message}`, {
            filename: e.filename,
            line: e.lineno,
            column: e.colno,
            stack: e.error?.stack
          }, true);
        }
      });
    },

    async loadUserInfo() {
      try {
        // Se não tiver info do usuário no window, buscar via API
        if (!window.userInfo) {
          const response = await fetch('/api/user/current');
          const userData = await response.json();
          this.userInfo = userData;
        }
      } catch (e) {
        console.warn('Could not load user info for debug logger');
      }
    }
  }
}
</script>

<style scoped>
.user-action-logger {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 9999;
  font-family: 'Courier New', monospace;
}

.logger-toggle {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: #2d3748;
  color: white;
  border: none;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.logger-toggle:hover {
  background: #4a5568;
  transform: scale(1.1);
}

.logger-toggle.active {
  background: #3182ce;
}

.logger-toggle.recording {
  background: #e53e3e;
  animation: pulse-ring 2s infinite;
}

.pulse {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 8px;
  height: 8px;
  background: white;
  border-radius: 50%;
  transform: translate(-50%, -50%);
  animation: pulse-dot 1s infinite;
}

@keyframes pulse-ring {
  0% { box-shadow: 0 0 0 0 rgba(229, 62, 62, 0.7); }
  70% { box-shadow: 0 0 0 10px rgba(229, 62, 62, 0); }
  100% { box-shadow: 0 0 0 0 rgba(229, 62, 62, 0); }
}

@keyframes pulse-dot {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.debug-panel {
  position: absolute;
  bottom: 80px;
  right: 0;
  width: 500px;
  max-height: 600px;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.2);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.panel-header {
  background: #2d3748;
  color: white;
  padding: 12px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.panel-header h4 {
  margin: 0;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.recording-indicator {
  color: #ff6b6b;
  font-weight: bold;
  animation: blink 1s infinite;
}

@keyframes blink {
  0%, 50% { opacity: 1; }
  51%, 100% { opacity: 0; }
}

.panel-controls {
  display: flex;
  gap: 4px;
}

.panel-controls button {
  padding: 4px 8px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 12px;
  background: rgba(255,255,255,0.2);
  color: white;
  transition: background 0.2s;
}

.panel-controls button:hover:not(:disabled) {
  background: rgba(255,255,255,0.3);
}

.panel-controls button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-start { background: #48bb78 !important; }
.btn-stop { background: #e53e3e !important; }
.btn-clear { background: #ed8936 !important; }
.btn-copy { background: #3182ce !important; }

.panel-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.session-info {
  padding: 12px;
  background: #f7fafc;
  border-bottom: 1px solid #e2e8f0;
  font-size: 12px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}

.info-item strong {
  color: #2d3748;
}

.filter-controls {
  padding: 8px 12px;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  gap: 12px;
  font-size: 12px;
  background: #f9f9f9;
}

.filter-controls label {
  display: flex;
  align-items: center;
  gap: 4px;
  cursor: pointer;
}

.action-log {
  flex: 1;
  overflow-y: auto;
  padding: 8px;
  max-height: 300px;
}

.log-entry {
  padding: 8px;
  margin-bottom: 4px;
  border-radius: 4px;
  border-left: 3px solid #e2e8f0;
  background: #fafafa;
  font-size: 12px;
}

.log-entry.type-click { border-left-color: #3182ce; }
.log-entry.type-navigation { border-left-color: #38a169; }
.log-entry.type-request { border-left-color: #d69e2e; }
.log-entry.type-form { border-left-color: #805ad5; }
.log-entry.type-system { border-left-color: #718096; }
.log-entry.error { border-left-color: #e53e3e; background: #fed7d7; }

.log-timestamp {
  color: #718096;
  font-weight: bold;
}

.log-type {
  display: flex;
  align-items: center;
  gap: 4px;
  font-weight: bold;
  color: #2d3748;
  margin: 4px 0;
}

.log-description {
  color: #4a5568;
  margin: 4px 0;
}

.log-data {
  margin-top: 8px;
}

.btn-details {
  background: none;
  border: 1px solid #e2e8f0;
  padding: 2px 6px;
  border-radius: 3px;
  cursor: pointer;
  font-size: 10px;
}

.log-details {
  background: #2d3748;
  color: #e2e8f0;
  padding: 8px;
  border-radius: 4px;
  margin-top: 4px;
  font-size: 10px;
  overflow-x: auto;
  max-height: 100px;
}

.no-actions {
  text-align: center;
  color: #718096;
  padding: 20px;
  font-style: italic;
}

.slide-up-enter-active, .slide-up-leave-active {
  transition: all 0.3s ease;
}

.slide-up-enter, .slide-up-leave-to {
  transform: translateY(100%);
  opacity: 0;
}
</style>