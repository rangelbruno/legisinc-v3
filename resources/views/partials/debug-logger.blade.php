@if(App\Helpers\DebugHelper::isDebugLoggerActive())
<script>
    // Componente Vue para Debug Logger
    const { createApp } = Vue;
    
    const UserActionLogger = {
        template: `
            <div class="debug-logger-container">
                <div v-if="isVisible" class="debug-panel">
                    <div class="debug-header">
                        <h5>🔧 Debug Logger</h5>
                        <div class="debug-controls">
                            <button :class="['btn btn-sm', isRecording ? 'btn-danger' : 'btn-success']" @click="toggleRecording">
                                @{{ isRecording ? '⏹️ Parar' : '▶️ Iniciar' }}
                            </button>
                            <button class="btn btn-sm btn-primary" @click="copyLogs" :disabled="actions.length === 0">
                                📋 Copiar
                            </button>
                            <button class="btn btn-sm btn-secondary" @click="clearLogs" :disabled="actions.length === 0">
                                🗑️ Limpar
                            </button>
                            <button class="btn btn-sm btn-light" @click="minimize">
                                @{{ isMinimized ? '➕' : '➖' }}
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" @click="close">✖️</button>
                        </div>
                    </div>
                    
                    <div v-if="!isMinimized" class="debug-content">
                        <div class="debug-status">
                            <span :class="['badge', isRecording ? 'badge-success' : 'badge-secondary']">
                                @{{ isRecording ? '🔴 Gravando' : '⚫ Parado' }}
                            </span>
                            <span class="badge badge-info">@{{ actions.length }} ações</span>
                        </div>
                        
                        <div class="debug-filters" v-if="actions.length > 0">
                            <select v-model="filterType" class="form-select form-select-sm">
                                <option value="">Todos os tipos</option>
                                <option v-for="type in uniqueTypes" :key="type" :value="type">@{{ type }}</option>
                            </select>
                        </div>
                        
                        <div class="debug-actions">
                            <div v-for="(action, index) in filteredActions" :key="index" 
                                 :class="['debug-action', action.type, { 'error': action.isError }]">
                                <div class="action-header">
                                    <span class="action-time">@{{ action.time }}</span>
                                    <span class="action-type">@{{ action.type }}</span>
                                </div>
                                <div class="action-details">@{{ action.details }}</div>
                                <div v-if="action.url" class="action-url">@{{ action.method }} @{{ action.url }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-if="!isVisible" class="debug-toggle" @click="show">
                    🔧
                </div>
            </div>
        `,
        data() {
            return {
                isVisible: false,
                isMinimized: false,
                isRecording: false,
                actions: [],
                filterType: '',
                sessionId: null
            };
        },
        mounted() {
            console.log('Debug Logger: Componente Vue montado');
            this.setupEventListeners();
            this.checkDebugStatus();
        },
        computed: {
            filteredActions() {
                if (!this.filterType) return this.actions;
                return this.actions.filter(action => action.type === this.filterType);
            },
            uniqueTypes() {
                return [...new Set(this.actions.map(action => action.type))];
            }
        },
        methods: {
            show() {
                this.isVisible = true;
            },
            close() {
                this.isVisible = false;
                if (this.isRecording) {
                    this.stopRecording();
                }
            },
            minimize() {
                this.isMinimized = !this.isMinimized;
            },
            async toggleRecording() {
                if (this.isRecording) {
                    await this.stopRecording();
                } else {
                    await this.startRecording();
                }
            },
            async startRecording() {
                try {
                    const response = await axios.post('/debug/start');
                    this.isRecording = true;
                    this.sessionId = response.data.session_id;
                    this.logAction('system', 'Debug iniciado', { sessionId: this.sessionId });
                } catch (error) {
                    console.error('Erro ao iniciar debug:', error);
                }
            },
            async stopRecording() {
                try {
                    await axios.post('/debug/stop');
                    this.isRecording = false;
                    this.logAction('system', 'Debug parado');
                } catch (error) {
                    console.error('Erro ao parar debug:', error);
                }
            },
            async checkDebugStatus() {
                try {
                    const response = await axios.get('/debug/status');
                    this.isRecording = response.data.active;
                    this.sessionId = response.data.session_id;
                } catch (error) {
                    console.error('Erro ao verificar status:', error);
                }
            },
            setupEventListeners() {
                const self = this;
                
                // Clicks - usar bind para manter contexto
                document.addEventListener('click', this.handleClick.bind(this));
                
                // Form submissions
                document.addEventListener('submit', this.handleFormSubmit.bind(this));
                
                // Navigation
                if (!window.historyIntercepted) {
                    window.historyIntercepted = true;
                    const originalPushState = history.pushState;
                    const originalReplaceState = history.replaceState;
                    
                    history.pushState = function(...args) {
                        originalPushState.apply(history, args);
                        if (self.isRecording) {
                            self.logAction('navigation', 'Navegação', { url: location.href });
                        }
                    };
                    
                    history.replaceState = function(...args) {
                        originalReplaceState.apply(history, args);
                        if (self.isRecording) {
                            self.logAction('navigation', 'Navegação', { url: location.href });
                        }
                    };
                }
                
                // AJAX requests intercept
                this.interceptFetch();
                this.interceptXHR();
            },
            handleClick(event) {
                if (!this.isRecording) return;
                
                // Ignorar cliques no próprio debug logger
                if (event.target.closest('.debug-logger-container')) return;
                
                const target = event.target;
                let details = '';
                
                if (target.tagName === 'BUTTON') {
                    details = `Clique no botão: "${target.textContent?.trim() || target.value || 'sem texto'}"`;
                } else if (target.tagName === 'A') {
                    details = `Clique no link: "${target.textContent?.trim() || target.href}"`;
                } else if (target.tagName === 'INPUT') {
                    details = `Clique no input ${target.type}: "${target.name || target.id}"`;
                } else {
                    details = `Clique em ${target.tagName.toLowerCase()}`;
                }
                
                this.logAction('click', details);
            },
            handleFormSubmit(event) {
                if (!this.isRecording) return;
                
                const form = event.target;
                const action = form.action || location.href;
                const method = form.method || 'GET';
                
                this.logAction('form_submit', `Envio de formulário: ${method} ${action}`);
            },
            interceptFetch() {
                // Só interceptar se ainda não foi interceptado
                if (window.fetchIntercepted) return;
                window.fetchIntercepted = true;
                
                const originalFetch = window.fetch;
                const self = this;
                window.fetch = async function(...args) {
                    // Usar função regular para manter o contexto correto
                    const response = await originalFetch.apply(window, args);
                    
                    if (self.isRecording) {
                        try {
                            const url = typeof args[0] === 'string' ? args[0] : args[0].url;
                            const method = args[1]?.method || 'GET';
                            const isError = response.status >= 400;
                            
                            self.logAction('ajax', `Requisição AJAX: ${method} ${url}`, {
                                url,
                                method,
                                status: response.status,
                                isError
                            });
                        } catch (e) {
                            console.error('Erro ao logar fetch:', e);
                        }
                    }
                    
                    return response;
                };
            },
            interceptXHR() {
                // Só interceptar se ainda não foi interceptado
                if (window.xhrIntercepted) return;
                window.xhrIntercepted = true;
                
                const self = this;
                const originalOpen = XMLHttpRequest.prototype.open;
                const originalSend = XMLHttpRequest.prototype.send;
                
                XMLHttpRequest.prototype.open = function(method, url, ...args) {
                    this._debugMethod = method;
                    this._debugUrl = url;
                    return originalOpen.call(this, method, url, ...args);
                };
                
                XMLHttpRequest.prototype.send = function(...args) {
                    const xhr = this;
                    this.addEventListener('load', function() {
                        if (self.isRecording) {
                            try {
                                const isError = xhr.status >= 400;
                                self.logAction('ajax', `Requisição XHR: ${xhr._debugMethod} ${xhr._debugUrl}`, {
                                    url: xhr._debugUrl,
                                    method: xhr._debugMethod,
                                    status: xhr.status,
                                    isError
                                });
                            } catch (e) {
                                console.error('Erro ao logar XHR:', e);
                            }
                        }
                    });
                    return originalSend.call(this, ...args);
                };
            },
            logAction(type, details, extra = {}) {
                if (!this.isRecording && type !== 'system') return;
                
                const action = {
                    time: new Date().toLocaleTimeString(),
                    type,
                    details,
                    url: extra.url || location.href,
                    method: extra.method || 'GET',
                    isError: extra.isError || false,
                    ...extra
                };
                
                this.actions.push(action);
                
                // Manter apenas últimas 100 ações
                if (this.actions.length > 100) {
                    this.actions = this.actions.slice(-100);
                }
            },
            copyLogs() {
                const logs = this.actions.map((action, index) => {
                    return `${index + 1}. [${action.time}] ${action.type.toUpperCase()}: ${action.details}${action.url ? ' - ' + action.url : ''}`;
                }).join('\n');
                
                navigator.clipboard.writeText(logs).then(() => {
                    alert('Logs copiados para a área de transferência!');
                });
            },
            clearLogs() {
                this.actions = [];
            }
        }
    };
    
    // Variável global para controlar se já foi inicializado
    window.debugLoggerApp = null;

    // Função fallback para inicializar debug
    function initializeDebugLogger() {
        // Se já foi inicializado, apenas mostrar o painel
        if (window.debugLoggerApp && window.debugLoggerApp._instance) {
            window.debugLoggerApp._instance.proxy.show();
            return;
        }
        
        const fallback = document.getElementById('debug-fallback');
        const debugElement = document.getElementById('debug-logger');
        
        if (debugElement && typeof Vue !== 'undefined') {
            try {
                const { createApp } = Vue;
                // Limpar o elemento antes de montar
                debugElement.innerHTML = '';
                window.debugLoggerApp = createApp(UserActionLogger);
                window.debugLoggerApp.mount('#debug-logger');
                console.log('Debug Logger: Componente inicializado');
                
                // Esconder fallback permanentemente
                if (fallback) {
                    fallback.remove();
                }
            } catch (error) {
                console.error('Debug Logger: Erro ao inicializar', error);
                if (fallback) fallback.style.display = 'block';
            }
        } else {
            console.error('Debug Logger: Vue.js não carregado ou elemento não encontrado');
        }
    }

    // Inicializar componente quando página carregar
    document.addEventListener('DOMContentLoaded', function() {
        // Aguardar um momento para garantir que tudo carregou
        setTimeout(function() {
            const debugElement = document.getElementById('debug-logger');
            const fallback = document.getElementById('debug-fallback');
            
            if (!window.debugLoggerApp && debugElement && typeof Vue !== 'undefined') {
                console.log('Debug Logger: Inicializando componente Vue.js');
                try {
                    const { createApp } = Vue;
                    window.debugLoggerApp = createApp(UserActionLogger);
                    window.debugLoggerApp.mount('#debug-logger');
                    console.log('Debug Logger: Componente inicializado com sucesso');
                    
                    // Remover fallback se Vue.js funcionou
                    if (fallback) {
                        fallback.remove();
                    }
                } catch (error) {
                    console.error('Debug Logger: Erro ao inicializar componente', error);
                    if (fallback) fallback.style.display = 'block';
                }
            } else if (!window.debugLoggerApp) {
                console.log('Debug Logger: Vue.js não disponível, mantendo fallback');
                if (fallback) fallback.style.display = 'block';
            }
        }, 100);
    });
</script>

<style>
    .debug-logger-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 12px;
    }
    
    .debug-panel {
        background: #2d3748;
        color: #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        width: 400px;
        max-height: 600px;
        overflow: hidden;
    }
    
    .debug-header {
        background: #4a5568;
        padding: 8px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #718096;
    }
    
    .debug-header h5 {
        margin: 0;
        font-size: 14px;
    }
    
    .debug-controls {
        display: flex;
        gap: 4px;
    }
    
    .debug-controls .btn {
        padding: 2px 6px;
        font-size: 10px;
    }
    
    .debug-content {
        max-height: 500px;
        overflow-y: auto;
    }
    
    .debug-status {
        padding: 8px 12px;
        background: #1a202c;
        border-bottom: 1px solid #4a5568;
    }
    
    .debug-filters {
        padding: 8px 12px;
        background: #2d3748;
        border-bottom: 1px solid #4a5568;
    }
    
    .debug-filters select {
        background: #4a5568;
        color: #e2e8f0;
        border: 1px solid #718096;
        font-size: 11px;
    }
    
    .debug-actions {
        padding: 8px;
        max-height: 400px;
        overflow-y: auto;
    }
    
    .debug-action {
        margin-bottom: 8px;
        padding: 6px 8px;
        background: #1a202c;
        border-radius: 4px;
        border-left: 3px solid #4a5568;
    }
    
    .debug-action.click { border-left-color: #48bb78; }
    .debug-action.form_submit { border-left-color: #ed8936; }
    .debug-action.navigation { border-left-color: #4299e1; }
    .debug-action.ajax { border-left-color: #9f7aea; }
    .debug-action.system { border-left-color: #38b2ac; }
    .debug-action.error { border-left-color: #f56565; }
    
    .action-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2px;
    }
    
    .action-time {
        color: #a0aec0;
        font-size: 10px;
    }
    
    .action-type {
        background: #4a5568;
        color: #e2e8f0;
        padding: 1px 4px;
        border-radius: 2px;
        font-size: 9px;
        text-transform: uppercase;
    }
    
    .action-details {
        font-size: 11px;
        margin-bottom: 2px;
    }
    
    .action-url {
        font-size: 9px;
        color: #a0aec0;
        font-style: italic;
    }
    
    .debug-toggle {
        position: fixed !important;
        bottom: 20px !important;
        right: 20px !important;
        width: 60px !important;
        height: 60px !important;
        background: #4299e1 !important;
        color: white !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        font-size: 24px !important;
        box-shadow: 0 4px 12px rgba(66, 153, 225, 0.4) !important;
        transition: all 0.3s ease !important;
        z-index: 999999 !important;
        border: none !important;
    }
    
    .debug-toggle:hover {
        background: #3182ce !important;
        transform: scale(1.1) !important;
        box-shadow: 0 6px 16px rgba(66, 153, 225, 0.6) !important;
    }
    
    .debug-toggle-fallback {
        position: fixed !important;
        bottom: 20px !important;
        right: 20px !important;
        width: 60px !important;
        height: 60px !important;
        background: #e53e3e !important;
        color: white !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        font-size: 24px !important;
        box-shadow: 0 4px 12px rgba(229, 62, 62, 0.4) !important;
        transition: all 0.3s ease !important;
        z-index: 999999 !important;
        border: none !important;
        user-select: none !important;
    }
    
    .debug-toggle-fallback:hover {
        background: #c53030 !important;
        transform: scale(1.1) !important;
        box-shadow: 0 6px 16px rgba(229, 62, 62, 0.6) !important;
    }
</style>
@endif