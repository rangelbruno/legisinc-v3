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
                            <span v-if="dbQueriesCount > 0" class="badge badge-primary" :title="'Queries SQL capturadas'">
                                🗄️ @{{ dbQueriesCount }} DB
                            </span>
                            <span v-if="sessionId" class="badge badge-warning" :title="'Sessão: ' + sessionId">
                                📱 Persistente
                            </span>
                        </div>
                        
                        <!-- Tabs -->
                        <div class="debug-tabs">
                            <button :class="['debug-tab', { active: activeTab === 'actions' }]" @click="activeTab = 'actions'; loadPersistedLogs()">
                                📋 Ações (@{{ actions.length }})
                            </button>
                            <button :class="['debug-tab', { active: activeTab === 'database' }]" @click="activeTab = 'database'; loadDatabaseQueries()">
                                🗄️ Banco
                            </button>
                        </div>
                        
                        <!-- Actions Tab -->
                        <div v-if="activeTab === 'actions'">
                            <div class="debug-filters">
                                <div class="d-flex justify-content-between align-items-center">
                                    <select v-if="actions.length > 0" v-model="filterType" class="form-select form-select-sm" style="width: auto;">
                                        <option value="">Todos os tipos</option>
                                        <option v-for="type in uniqueTypes" :key="type" :value="type">@{{ type }}</option>
                                    </select>
                                    <button class="btn btn-sm btn-outline-light" @click="loadPersistedLogs()" title="Atualizar ações">
                                        🔄
                                    </button>
                                </div>
                            </div>
                            
                            <div class="debug-actions">
                                <div v-if="filteredActions.length === 0" class="no-actions">
                                    <div v-if="!isRecording" class="text-center py-3">
                                        <p class="mb-2">Debug não está ativo</p>
                                        <p class="text-muted">Clique em "▶️ Iniciar" para começar a capturar suas ações</p>
                                    </div>
                                    <div v-else class="text-center py-3">
                                        <p class="mb-2">Nenhuma ação capturada ainda</p>
                                        <p class="text-muted">Execute ações no sistema para vê-las aparecer aqui</p>
                                    </div>
                                </div>
                                
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
                        
                        <!-- Database Tab -->
                        <div v-if="activeTab === 'database'">
                            <div class="debug-filters">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="db-stats-compact" v-if="dbStats">
                                        <div class="d-flex gap-3">
                                            <span class="stat-compact">
                                                <strong>@{{ dbStats.total_queries || 0 }}</strong> queries
                                            </span>
                                            <span class="stat-compact">
                                                <strong>@{{ (dbStats.total_time || 0).toFixed(2) }}ms</strong> total
                                            </span>
                                            <span class="stat-compact text-danger" v-if="(dbStats.slow_queries || 0) + (dbStats.very_slow_queries || 0) > 0">
                                                <strong>@{{ (dbStats.slow_queries || 0) + (dbStats.very_slow_queries || 0) }}</strong> lentas
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-light" @click="copyDatabaseQueries()" :disabled="dbQueries.length === 0" title="Copiar queries">
                                            📋 Copiar
                                        </button>
                                        <button class="btn btn-sm btn-outline-light" @click="loadDatabaseQueries()" title="Atualizar queries">
                                            🔄
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="debug-db-actions">
                                <div v-for="(query, index) in dbQueries.slice(0, 10)" :key="index" 
                                     :class="['debug-action', 'query', query.performance]"
                                     @click="showQueryDetail(query)">
                                    <div class="action-header">
                                        <span class="action-time">@{{ new Date(query.timestamp).toLocaleTimeString() }}</span>
                                        <span :class="['action-type', 'query-type', query.type.toLowerCase()]">@{{ query.type }}</span>
                                        <span :class="['query-performance', query.performance]">@{{ query.time_formatted }}</span>
                                    </div>
                                    <div class="action-details query-sql">@{{ truncateSQL(query.sql, 80) }}</div>
                                    <div v-if="query.tables.length > 0" class="query-tables">
                                        <span v-for="table in query.tables" :key="table" class="table-badge">@{{ table }}</span>
                                    </div>
                                </div>
                                
                                <div v-if="dbQueries.length > 10" class="more-queries">
                                    ... e mais @{{ dbQueries.length - 10 }} queries
                                </div>
                                
                                <div v-if="dbQueries.length === 0" class="no-queries">
                                    Nenhuma query capturada ainda
                                </div>
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
                sessionId: null,
                activeTab: 'actions',
                dbQueries: [],
                dbStats: null,
                dbQueriesCount: 0
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
            async loadPersistedLogs() {
                if (!this.sessionId) return;
                
                // Primeiro tentar carregar do servidor
                try {
                    const response = await axios.get('/debug/logs');
                    if (response.data.logs && response.data.logs.length > 0) {
                        // Converter logs do servidor para o formato do Vue
                        this.actions = response.data.logs.map(log => ({
                            time: new Date(log.timestamp).toLocaleTimeString(),
                            timestamp: log.timestamp,
                            type: log.action_type,
                            details: this.formatLogDetails(log),
                            url: log.url,
                            method: log.method,
                            isError: log.is_error,
                            sessionId: this.sessionId
                        }));
                        
                        console.log(`Debug Logger: ${this.actions.length} logs carregados do servidor`);
                        // Salvar no localStorage como backup
                        this.persistLogs();
                        return;
                    }
                } catch (error) {
                    console.warn('Debug Logger: Erro ao carregar logs do servidor, tentando localStorage', error);
                }
                
                // Fallback para localStorage se servidor falhar
                const persistedLogs = localStorage.getItem(`debugLogger_logs_${this.sessionId}`);
                if (persistedLogs) {
                    try {
                        const logs = JSON.parse(persistedLogs);
                        this.actions = logs;
                        console.log(`Debug Logger: ${logs.length} logs carregados do localStorage (fallback)`);
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
                        // Recarregar logs do servidor periodicamente
                        this.loadPersistedLogs();
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
                    
                    // Limpar ações antigas
                    this.actions = [];
                    
                    // Aguardar um momento para que a sessão seja registrada no servidor
                    setTimeout(async () => {
                        // Carregar logs existentes desta sessão (se houver)
                        await this.loadPersistedLogs();
                        
                        // Adicionar log de início se não houver logs carregados
                        if (this.actions.length === 0) {
                            this.logAction('system', 'Debug iniciado', { sessionId: this.sessionId });
                        }
                    }, 500);
                    
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
                    const dbQueriesCount = response.data.db_queries_count || 0;
                    
                    // Atualizar contador de queries do banco
                    this.dbQueriesCount = dbQueriesCount;
                    
                    // Sincronizar com o servidor apenas se houver discrepância
                    if (this.isRecording !== serverActive || this.sessionId !== serverSessionId) {
                        console.log('Debug Logger: Sincronizando com servidor', {
                            local: { isRecording: this.isRecording, sessionId: this.sessionId },
                            server: { active: serverActive, sessionId: serverSessionId, dbQueries: dbQueriesCount }
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
                
                // Log no console para debug
                console.log('Debug Logger: Nova ação capturada', { type, details, extra });
            },
            copyLogs() {
                if (!this.actions || this.actions.length === 0) {
                    this.showToast('Nenhuma ação disponível para copiar', 'warning');
                    return;
                }
                
                // Gerar relatório formatado de ações
                let content = '📋 USER ACTIONS DEBUG REPORT\n';
                content += '=====================================\n\n';
                content += `📅 Generated: ${new Date().toLocaleString()}\n`;
                content += `🔑 Session: ${this.sessionId}\n`;
                content += `👤 Total Actions: ${this.actions.length}\n\n`;
                content += '='.repeat(50) + '\n';
                content += 'ACTIONS DETAILS\n';
                content += '='.repeat(50) + '\n\n';
                
                const actionContent = this.actions.map((action, index) => {
                    return `${index + 1}. [${action.time}] ${action.type.toUpperCase()}\n   ${action.details}${action.url ? '\n   URL: ' + action.url : ''}`;
                }).join('\n\n' + '-'.repeat(40) + '\n\n');
                
                content += actionContent;
                content += '\n\n=== END OF REPORT ===\n';
                
                navigator.clipboard.writeText(content).then(() => {
                    this.showToast(`${this.actions.length} ações copiadas para área de transferência!`, 'success');
                }).catch(err => {
                    console.error('Erro ao copiar:', err);
                    this.showToast('Erro ao copiar ações', 'error');
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
            },
            
            // Métodos para banco de dados
            async loadDatabaseQueries() {
                if (!this.isRecording) return;
                
                try {
                    const response = await axios.get('/debug/database/queries');
                    if (response.data.success) {
                        this.dbQueries = response.data.queries || [];
                        this.dbStats = response.data.statistics || {};
                        console.log('Debug Logger: Database queries loaded', {
                            queries: this.dbQueries.length,
                            stats: this.dbStats
                        });
                    }
                } catch (error) {
                    console.error('Erro ao carregar queries do banco:', error);
                }
            },
            
            showQueryDetail(query) {
                // Criar modal ou expansão para mostrar detalhes da query
                const details = `
SQL: ${query.formatted_sql || query.sql}

Performance: ${query.performance} (${query.time_formatted})
Type: ${query.type}
Tables: ${query.tables.join(', ')}

Executed at: ${new Date(query.timestamp).toLocaleString()}
                `.trim();
                
                alert(details);
            },
            
            truncateSQL(sql, maxLength) {
                if (!sql || sql.length <= maxLength) return sql || '';
                return sql.substring(0, maxLength) + '...';
            },
            
            formatLogDetails(log) {
                // Formatar detalhes do log baseado no tipo de ação
                const actionType = log.action_type;
                const method = log.method;
                const url = log.url;
                
                switch(actionType) {
                    case 'page_view':
                        return `Visualização de página: ${url}`;
                    case 'form_submit':
                        return `Envio de formulário: ${method} ${url}`;
                    case 'proposicao_create':
                        return 'Nova proposição criada';
                    case 'proposicao_update':
                        return 'Proposição atualizada';
                    case 'proposicao_view':
                        return 'Proposição visualizada';
                    case 'proposicao_pdf_view':
                        return 'PDF da proposição visualizado';
                    case 'proposicao_sign':
                        return 'Proposição assinada digitalmente';
                    case 'proposicao_protocol':
                        return 'Proposição protocolada';
                    case 'onlyoffice_edit':
                        return 'Edição no OnlyOffice';
                    case 'auth_login':
                        return 'Login realizado';
                    case 'auth_logout':
                        return 'Logout realizado';
                    case 'data_update':
                        return `Atualização de dados: ${method} ${url}`;
                    case 'data_delete':
                        return `Exclusão de dados: ${method} ${url}`;
                    default:
                        return `${actionType}: ${method} ${url}`;
                }
            },
            
            // Função para copiar queries do banco de dados
            copyDatabaseQueries() {
                if (!this.dbQueries || this.dbQueries.length === 0) {
                    this.showToast('Nenhuma query disponível para copiar', 'warning');
                    return;
                }
                
                // Gerar cabeçalho do relatório
                let content = '🗄️ DATABASE QUERIES DEBUG REPORT\n';
                content += '=====================================\n\n';
                content += `📅 Generated: ${new Date().toLocaleString()}\n`;
                content += `🔑 Session: ${this.sessionId}\n`;
                content += `📊 Total Queries: ${this.dbQueries.length}\n`;
                
                if (this.dbStats) {
                    content += `⏱️ Total Time: ${(this.dbStats.total_time || 0).toFixed(2)}ms\n`;
                    content += `📈 Average Time: ${(this.dbStats.average_time || 0).toFixed(2)}ms\n`;
                    content += `🐌 Slow Queries: ${(this.dbStats.slow_queries || 0) + (this.dbStats.very_slow_queries || 0)}\n`;
                }
                
                content += '\n' + '='.repeat(50) + '\n';
                content += 'QUERIES DETAILS\n';
                content += '='.repeat(50) + '\n\n';
                
                // Adicionar cada query formatada
                this.dbQueries.forEach((query, index) => {
                    content += `${index + 1}. [${new Date(query.timestamp).toLocaleTimeString()}] ${query.type} Query\n`;
                    content += `   Performance: ${query.performance} (${query.time_formatted})\n`;
                    
                    if (query.tables && query.tables.length > 0) {
                        content += `   Tables: ${query.tables.join(', ')}\n`;
                    }
                    
                    content += `   SQL:\n`;
                    content += `   ${query.formatted_sql || query.sql}\n`;
                    
                    if (query.bindings && query.bindings.length > 0) {
                        content += `   Bindings: ${JSON.stringify(query.bindings)}\n`;
                    }
                    
                    content += '\n' + '-'.repeat(40) + '\n\n';
                });
                
                content += '\n=== END OF REPORT ===\n';
                
                // Copiar para clipboard
                navigator.clipboard.writeText(content).then(() => {
                    this.showToast(`${this.dbQueries.length} queries copiadas para área de transferência!`, 'success');
                }).catch(err => {
                    console.error('Erro ao copiar:', err);
                    this.showToast('Erro ao copiar queries', 'error');
                });
            },
            
            // Função para mostrar toast/notificação
            showToast(message, type = 'success') {
                // Implementação simples de toast
                const toastElement = document.createElement('div');
                toastElement.className = `debug-toast toast-${type}`;
                toastElement.innerHTML = `
                    <div class="toast-content">
                        <span class="toast-icon">${type === 'success' ? '✅' : type === 'warning' ? '⚠️' : '❌'}</span>
                        <span class="toast-message">${message}</span>
                    </div>
                `;
                
                // Adicionar estilos inline
                toastElement.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 10000;
                    background: ${type === 'success' ? '#48bb78' : type === 'warning' ? '#ed8936' : '#f56565'};
                    color: white;
                    padding: 12px 16px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                    font-size: 12px;
                    max-width: 300px;
                    animation: slideInRight 0.3s ease-out;
                `;
                
                document.body.appendChild(toastElement);
                
                // Remover após 3 segundos
                setTimeout(() => {
                    toastElement.style.animation = 'slideOutRight 0.3s ease-in';
                    setTimeout(() => {
                        if (document.body.contains(toastElement)) {
                            document.body.removeChild(toastElement);
                        }
                    }, 300);
                }, 3000);
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
    
    .debug-filters .btn-outline-light {
        border-color: #4a5568;
        color: #a0aec0;
        font-size: 12px;
        padding: 2px 6px;
    }
    
    .debug-filters .btn-outline-light:hover {
        background-color: #4a5568;
        border-color: #718096;
        color: #e2e8f0;
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
    
    /* Debug Tabs */
    .debug-tabs {
        display: flex;
        border-bottom: 1px solid #4a5568;
        background: #2d3748;
    }
    
    .debug-tab {
        flex: 1;
        padding: 6px 8px;
        background: transparent;
        border: none;
        color: #a0aec0;
        font-size: 11px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .debug-tab:hover {
        background: #4a5568;
        color: #e2e8f0;
    }
    
    .debug-tab.active {
        background: #1a202c;
        color: #4299e1;
        border-bottom: 2px solid #4299e1;
    }
    
    /* Database Stats */
    .db-stats {
        display: flex;
        justify-content: space-between;
        padding: 6px 12px;
        background: #1a202c;
        border-bottom: 1px solid #4a5568;
        font-size: 10px;
    }
    
    .db-stat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .stat-label {
        color: #a0aec0;
        font-size: 9px;
        margin-bottom: 2px;
    }
    
    .stat-value {
        color: #e2e8f0;
        font-weight: bold;
    }
    
    /* Query Actions */
    .debug-db-actions {
        padding: 8px;
        max-height: 350px;
        overflow-y: auto;
    }
    
    .debug-action.query {
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .debug-action.query:hover {
        background: #2d3748;
        transform: translateX(2px);
    }
    
    .debug-action.query.excellent { border-left-color: #48bb78; }
    .debug-action.query.good { border-left-color: #4299e1; }
    .debug-action.query.average { border-left-color: #ed8936; }
    .debug-action.query.slow { border-left-color: #f56565; }
    .debug-action.query.very_slow { border-left-color: #9f7aea; }
    
    .query-type {
        font-size: 8px !important;
    }
    
    .query-type.select { background: #4299e1; }
    .query-type.insert { background: #48bb78; }
    .query-type.update { background: #ed8936; }
    .query-type.delete { background: #f56565; }
    .query-type.transaction { background: #9f7aea; }
    
    .query-performance {
        background: #4a5568;
        color: #e2e8f0;
        padding: 1px 3px;
        border-radius: 2px;
        font-size: 8px;
        margin-left: 4px;
    }
    
    .query-performance.excellent { background: #48bb78; }
    .query-performance.good { background: #4299e1; }
    .query-performance.average { background: #ed8936; }
    .query-performance.slow { background: #f56565; }
    .query-performance.very_slow { background: #9f7aea; }
    
    .query-sql {
        font-family: 'Monaco', 'Consolas', monospace;
        font-size: 10px;
        color: #a0aec0;
        margin: 2px 0;
    }
    
    .query-tables {
        margin-top: 3px;
    }
    
    .table-badge {
        display: inline-block;
        background: #4a5568;
        color: #e2e8f0;
        padding: 1px 3px;
        border-radius: 2px;
        font-size: 8px;
        margin-right: 2px;
    }
    
    .more-queries, .no-queries, .no-actions {
        text-align: center;
        padding: 10px;
        color: #a0aec0;
        font-size: 11px;
        font-style: italic;
    }
    
    .no-actions p {
        margin: 5px 0;
        font-size: 12px;
    }
    
    .no-actions .text-muted {
        color: #718096 !important;
        font-size: 11px;
    }
    
    .text-danger {
        color: #f56565 !important;
    }
    
    /* Database Stats Compact */
    .db-stats-compact {
        font-size: 11px;
        color: #a0aec0;
    }
    
    .stat-compact {
        color: #e2e8f0;
    }
    
    .stat-compact strong {
        color: #4299e1;
    }
    
    /* Toast Animations */
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    /* Debug Toast Styles */
    .debug-toast {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    }
    
    .toast-content {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .toast-icon {
        font-size: 14px;
    }
    
    .toast-message {
        flex: 1;
        font-weight: 500;
    }
</style>
@endif