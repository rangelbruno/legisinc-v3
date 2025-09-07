@if(App\Helpers\DebugHelper::isDebugLoggerActive())
<script>
    // Encapsular em try-catch para evitar quebrar outras funcionalidades
    try {
        
    // Verificar se Vue está disponível e evitar redeclaração
    if (typeof Vue === 'undefined') {
        console.error('Debug Logger: Vue.js não está carregado');
    } else if (window.debugLoggerApp) {
        console.log('Debug Logger: Componente já inicializado, pulando redeclaração');
    } else {
        // Componente Vue para Debug Logger (global para evitar conflitos)
    
    window.UserActionLogger = {
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
                            <span v-if="sessionId" class="badge badge-warning" :title="'Sessão: ' + sessionId">
                                📱 Persistente
                            </span>
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
            this.loadPersistedState();
            this.setupEventListeners();
            this.checkDebugStatus();
            this.setupPeriodicCheck();
            this.setupPageChangeDetection();
            
            // Log de inicialização se estiver gravando
            if (this.isRecording) {
                this.logAction('system', 'Debug Logger reinicializado na nova página', { 
                    url: location.href,
                    timestamp: new Date().toISOString()
                });
            }
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
        watch: {
            // Observar mudanças e persistir automaticamente
            isRecording(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.persistState();
                }
            },
            isVisible(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.persistState();
                }
            },
            sessionId(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.persistState();
                }
            }
        },
        methods: {
            loadPersistedState() {
                // Carregar estado persistido do localStorage
                const persistedState = localStorage.getItem('debugLogger_state');
                if (persistedState) {
                    try {
                        const state = JSON.parse(persistedState);
                        this.isVisible = state.isVisible || false;
                        this.isMinimized = state.isMinimized || false;
                        this.isRecording = state.isRecording || false;
                        this.sessionId = state.sessionId || null;
                        console.log('Debug Logger: Estado carregado do localStorage', state);
                    } catch (e) {
                        console.warn('Debug Logger: Erro ao carregar estado persistido', e);
                    }
                }
                
                // Carregar logs persistidos
                this.loadPersistedLogs();
            },
            loadPersistedLogs() {
                if (!this.sessionId) return;
                
                const persistedLogs = localStorage.getItem(`debugLogger_logs_${this.sessionId}`);
                if (persistedLogs) {
                    try {
                        const logs = JSON.parse(persistedLogs);
                        this.actions = logs;
                        console.log(`Debug Logger: ${logs.length} logs carregados do localStorage`);
                    } catch (e) {
                        console.warn('Debug Logger: Erro ao carregar logs persistidos', e);
                        this.actions = [];
                    }
                } else {
                    this.actions = [];
                }
            },
            persistState() {
                // Salvar estado no localStorage
                const state = {
                    isVisible: this.isVisible,
                    isMinimized: this.isMinimized,
                    isRecording: this.isRecording,
                    sessionId: this.sessionId,
                    lastUpdate: new Date().toISOString()
                };
                localStorage.setItem('debugLogger_state', JSON.stringify(state));
            },
            persistLogs() {
                // Salvar logs no localStorage (associados ao sessionId)
                if (this.sessionId && this.actions.length > 0) {
                    try {
                        localStorage.setItem(`debugLogger_logs_${this.sessionId}`, JSON.stringify(this.actions));
                    } catch (e) {
                        console.warn('Debug Logger: Erro ao salvar logs no localStorage', e);
                        // Se localStorage estiver cheio, remover logs mais antigos
                        this.cleanupOldLogs();
                    }
                }
            },
            cleanupOldLogs() {
                // Limpar logs antigos do localStorage se necessário
                const keys = Object.keys(localStorage);
                const logKeys = keys.filter(key => key.startsWith('debugLogger_logs_'));
                
                // Se há muitas sessões de log, remover as mais antigas
                if (logKeys.length > 5) {
                    logKeys.sort().slice(0, logKeys.length - 5).forEach(key => {
                        localStorage.removeItem(key);
                    });
                }
            },
            setupPeriodicCheck() {
                // Verificar status a cada 10 segundos para manter sincronizado (menos frequente)
                setInterval(() => {
                    if (this.isRecording) {
                        this.checkDebugStatus();
                    }
                }, 10000);
                
                // Verificação adicional mais espaçada para detectar mudanças não capturadas
                setInterval(() => {
                    if (this.isRecording) {
                        console.log('Debug Logger: Verificação de saúde - ainda gravando');
                    }
                }, 30000);
            },
            setupPageChangeDetection() {
                // Detectar mudanças de página e reconfigurar interceptadores
                const self = this;
                
                // Detectar popstate (botão voltar/avançar)
                window.addEventListener('popstate', () => {
                    if (self.isRecording) {
                        console.log('Debug Logger: Detectada mudança de página (popstate)');
                        setTimeout(() => {
                            self.reinitializeInterceptors();
                        }, 100);
                    }
                });
                
                // Observar mudanças no DOM (para SPAs e carregamento dinâmico)
                if (typeof MutationObserver !== 'undefined') {
                    const observer = new MutationObserver((mutations) => {
                        if (self.isRecording) {
                            // Verificar se houve mudanças significativas no DOM
                            let significantChange = false;
                            mutations.forEach((mutation) => {
                                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                                    for (let node of mutation.addedNodes) {
                                        if (node.nodeType === 1 && (
                                            node.tagName === 'MAIN' || 
                                            node.className.includes('container') ||
                                            node.className.includes('content')
                                        )) {
                                            significantChange = true;
                                            break;
                                        }
                                    }
                                }
                            });
                            
                            if (significantChange) {
                                console.log('Debug Logger: Detectada mudança significativa no DOM');
                                setTimeout(() => {
                                    self.reinitializeInterceptors();
                                }, 100);
                            }
                        }
                    });
                    
                    observer.observe(document.body, {
                        childList: true,
                        subtree: true
                    });
                }
            },
            reinitializeInterceptors() {
                console.log('Debug Logger: Reinicializando interceptadores após mudança de página');
                
                // Resetar flags de interceptação
                window.fetchIntercepted = false;
                window.xhrIntercepted = false;
                window.historyIntercepted = false;
                
                // Reconfigrar interceptadores
                this.interceptFetch();
                this.interceptXHR();
                this.setupHistoryInterception();
                this.setupDocumentListeners();
            },
            show() {
                this.isVisible = true;
                this.persistState();
            },
            close() {
                this.isVisible = false;
                if (this.isRecording) {
                    this.stopRecording();
                }
                this.persistState();
            },
            minimize() {
                this.isMinimized = !this.isMinimized;
                this.persistState();
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
                    
                    // Carregar logs existentes desta sessão (se houver)
                    this.loadPersistedLogs();
                    
                    this.logAction('system', 'Debug iniciado', { sessionId: this.sessionId });
                    this.persistState();
                    console.log('Debug Logger: Gravação iniciada', response.data);
                } catch (error) {
                    console.error('Erro ao iniciar debug:', error);
                }
            },
            async stopRecording() {
                try {
                    await axios.post('/debug/stop');
                    this.isRecording = false;
                    this.sessionId = null;
                    this.logAction('system', 'Debug parado');
                    this.persistState();
                    console.log('Debug Logger: Gravação parada');
                } catch (error) {
                    console.error('Erro ao parar debug:', error);
                }
            },
            async checkDebugStatus() {
                try {
                    const response = await axios.get('/debug/status');
                    const serverActive = response.data.active;
                    const serverSessionId = response.data.session_id;
                    
                    // Sincronizar com o servidor apenas se houver discrepância
                    if (this.isRecording !== serverActive || this.sessionId !== serverSessionId) {
                        console.log('Debug Logger: Sincronizando com servidor', {
                            local: { isRecording: this.isRecording, sessionId: this.sessionId },
                            server: { active: serverActive, sessionId: serverSessionId }
                        });
                        
                        // Se sessionId mudou, carregar logs da nova sessão
                        if (this.sessionId !== serverSessionId) {
                            this.sessionId = serverSessionId;
                            this.loadPersistedLogs();
                        }
                        
                        this.isRecording = serverActive;
                        this.persistState();
                        
                        if (serverActive && !this.isVisible) {
                            // Se debug está ativo no servidor mas painel está oculto, mostrar
                            this.isVisible = true;
                            this.persistState();
                        }
                    }
                } catch (error) {
                    console.error('Erro ao verificar status:', error);
                }
            },
            setupEventListeners() {
                this.setupDocumentListeners();
                this.setupHistoryInterception();
                this.interceptFetch();
                this.interceptXHR();
            },
            setupDocumentListeners() {
                // Eventos de documento que precisam ser reconfigurados
                document.removeEventListener('click', this.handleClick);
                document.removeEventListener('submit', this.handleFormSubmit);
                
                document.addEventListener('click', this.handleClick.bind(this));
                document.addEventListener('submit', this.handleFormSubmit.bind(this));
            },
            setupHistoryInterception() {
                const self = this;
                
                // Navigation - só interceptar se ainda não foi interceptado
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
                            
                            // Ignorar requisições internas do debug logger
                            if (url && url.includes('/debug/')) {
                                return response;
                            }
                            
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
                                // Ignorar requisições internas do debug logger
                                if (xhr._debugUrl && xhr._debugUrl.includes('/debug/')) {
                                    return;
                                }
                                
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
                    timestamp: new Date().toISOString(),
                    type,
                    details,
                    url: extra.url || location.href,
                    method: extra.method || 'GET',
                    isError: extra.isError || false,
                    sessionId: this.sessionId,
                    ...extra
                };
                
                this.actions.push(action);
                
                // Manter apenas últimas 200 ações (aumentei o limite)
                if (this.actions.length > 200) {
                    this.actions = this.actions.slice(-200);
                }
                
                // Persistir logs automaticamente após cada nova ação
                this.persistLogs();
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
                if (confirm('Deseja limpar todos os logs desta sessão de debug? Esta ação não pode ser desfeita.')) {
                    this.actions = [];
                    // Também remover do localStorage
                    if (this.sessionId) {
                        localStorage.removeItem(`debugLogger_logs_${this.sessionId}`);
                    }
                    console.log('Debug Logger: Logs limpos completamente');
                }
            },
            clearPersistedState() {
                localStorage.removeItem('debugLogger_state');
                // Também limpar logs da sessão atual se existir
                if (this.sessionId) {
                    localStorage.removeItem(`debugLogger_logs_${this.sessionId}`);
                }
                console.log('Debug Logger: Estado e logs persistidos limpos');
            },
            clearAllPersistedData() {
                // Limpar todos os dados de debug do localStorage
                const keys = Object.keys(localStorage);
                const debugKeys = keys.filter(key => key.startsWith('debugLogger_'));
                debugKeys.forEach(key => localStorage.removeItem(key));
                console.log(`Debug Logger: ${debugKeys.length} chaves de dados limpos do localStorage`);
            },
            // Método público para reiniciar manualmente
            forceReinitialize() {
                console.log('Debug Logger: Reinicialização forçada');
                this.reinitializeInterceptors();
                this.checkDebugStatus();
            }
        }
    };
    
    // Variável global para controlar se já foi inicializado
    window.debugLoggerApp = null;
    
    // Função global para reinicializar debug logger manualmente
    window.reinitializeDebugLogger = function() {
        if (window.debugLoggerApp && window.debugLoggerApp._instance) {
            window.debugLoggerApp._instance.proxy.forceReinitialize();
        }
    };

    } // Fim da condição de inicialização

    // Funções sempre disponíveis
    
    // Função para verificar se componente está disponível  
    window.isDebugLoggerAvailable = function() {
        return typeof Vue !== 'undefined' && 
               window.UserActionLogger && 
               document.getElementById('debug-logger');
    };
    
    // Função fallback para inicializar debug (sempre disponível)
    window.initializeDebugLogger = function initializeDebugLogger() {
        // Se já foi inicializado, apenas mostrar o painel
        if (window.debugLoggerApp && window.debugLoggerApp._instance) {
            window.debugLoggerApp._instance.proxy.show();
            return;
        }
        
        const fallback = document.getElementById('debug-fallback');
        const debugElement = document.getElementById('debug-logger');
        
        if (window.isDebugLoggerAvailable && window.isDebugLoggerAvailable()) {
            try {
                // Limpar o elemento antes de montar
                debugElement.innerHTML = '';
                window.debugLoggerApp = Vue.createApp(window.UserActionLogger);
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
            console.error('Debug Logger: Vue.js não carregado, componente não definido ou elemento não encontrado');
            // Tentar novamente após um tempo
            setTimeout(() => {
                if (window.isDebugLoggerAvailable && window.isDebugLoggerAvailable() && !window.debugLoggerApp) {
                    console.log('Debug Logger: Tentando inicializar novamente...');
                    window.initializeDebugLogger();
                }
            }, 1000);
        }
    };

    // Inicializar componente quando página carregar
    document.addEventListener('DOMContentLoaded', function() {
        // Aguardar um momento para garantir que tudo carregou
        setTimeout(function() {
            const debugElement = document.getElementById('debug-logger');
            const fallback = document.getElementById('debug-fallback');
            
            if (!window.debugLoggerApp && window.isDebugLoggerAvailable && window.isDebugLoggerAvailable()) {
                console.log('Debug Logger: Inicializando componente Vue.js');
                try {
                    window.debugLoggerApp = Vue.createApp(window.UserActionLogger);
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
                console.log('Debug Logger: Vue.js não disponível ou componente não definido, mantendo fallback');
                if (fallback) fallback.style.display = 'block';
            }
        }, 100);
    });
    
    } catch (debugLoggerError) {
        console.error('Debug Logger: Erro crítico durante inicialização', debugLoggerError);
        // Garantir que as funções básicas estejam disponíveis mesmo com erro
        window.initializeDebugLogger = window.initializeDebugLogger || function() { 
            console.warn('Debug Logger: Função fallback - componente não inicializado'); 
        };
        window.reinitializeDebugLogger = window.reinitializeDebugLogger || function() { 
            console.warn('Debug Logger: Função fallback - componente não inicializado'); 
        };
    }
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