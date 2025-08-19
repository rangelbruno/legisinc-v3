@extends('components.layouts.app')

@section('title', 'Visualizar Proposição')

@section('content')
<div id="proposicao-app" class="container-fluid">
    <!-- Vue.js montará aqui -->
</div>

<!-- Vue.js CDN -->
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

<style>
/* Loading spinner */
.loading-spinner {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 200px;
}

.spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #007bff;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Status badges */
.status-badge {
    transition: all 0.3s ease;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Botões otimizados */
.btn-onlyoffice, .btn-assinatura {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    font-weight: 600;
    padding: 12px 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-onlyoffice:hover, .btn-assinatura:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-onlyoffice {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
}

.btn-assinatura {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
    border: none;
}

/* Fade transitions */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s ease;
}

.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

/* Card hover effects */
.card-hover {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Content area */
.content-area {
    min-height: 200px; 
    white-space: pre-wrap;
    line-height: 1.6;
    font-size: 1rem;
}

/* Background gradients */
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

/* Shadow utilities */
.shadow-sm {
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
}

/* Card improvements */
.card {
    border: none;
    border-radius: 0.5rem;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,.125);
    padding: 1.25rem;
}

.card-header.text-center h5,
.card-header.text-center h6 {
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

/* Alert improvements */
.alert-light {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

/* Timeline styles */
.timeline-container {
    position: relative;
}

.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-line {
    position: absolute;
    left: 19px;
    top: 40px;
    bottom: 0;
    width: 2px;
    background-color: #dee2e6;
}

.timeline-icon .badge {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Bootstrap 5 opacity utilities */
.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}

/* Border utilities */
.border-0 {
    border: 0!important;
}

/* Text color for better contrast */
.text-dark {
    color: #212529!important;
}
</style>

<script>
const { createApp } = Vue;

// Dados da proposição passados do Blade
const PROPOSICAO_DATA = {
    id: {{ $proposicao->id }},
    tipo: '{{ $proposicao->tipo ?? '' }}',
    ementa: {!! json_encode($proposicao->ementa ?? '') !!},
    conteudo: {!! json_encode($proposicao->conteudo ?? '') !!},
    status: '{{ $proposicao->status ?? '' }}',
    created_at: '{{ $proposicao->created_at ? $proposicao->created_at->toISOString() : '' }}',
    updated_at: '{{ $proposicao->updated_at ? $proposicao->updated_at->toISOString() : '' }}',
    numero_protocolo: '{{ $proposicao->numero_protocolo ?? '' }}',
    arquivo_path: '{{ $proposicao->arquivo_path ?? '' }}',
    autor: {
        id: {{ $proposicao->autor ? $proposicao->autor->id : 'null' }},
        name: '{{ $proposicao->autor ? $proposicao->autor->name : '' }}',
        email: '{{ $proposicao->autor ? $proposicao->autor->email : '' }}'
    }
};

// Componente principal
const ProposicaoViewer = {
    data() {
        return {
            proposicao: PROPOSICAO_DATA,
            loading: false,
            error: null,
            lastUpdate: new Date(),
            showFullContent: false,
            pollingEnabled: true,
            pollingInterval: null,
            updateCount: 0,
            pollingErrors: 0,
            // Dados do usuário
            USER_ROLE: '{{ auth()->user()->getRoleNames()->first() ?? "guest" }}',
            USER_ID: {{ auth()->user()->id ?? 0 }},
            CSRF_TOKEN: '{{ csrf_token() }}'
        }
    },
    computed: {
        statusClass() {
            const statusClasses = {
                'rascunho': 'warning',
                'em_edicao': 'warning', 
                'enviado_legislativo': 'secondary',
                'em_revisao': 'primary',
                'aguardando_aprovacao_autor': 'primary',
                'devolvido_edicao': 'warning',
                'retornado_legislativo': 'info',
                'aprovado': 'success',
                'reprovado': 'danger'
            };
            return statusClasses[this.proposicao?.status] || 'secondary';
        },
        statusText() {
            const statusTexts = {
                'rascunho': 'Rascunho',
                'em_edicao': 'Em Edição',
                'enviado_legislativo': 'Enviado ao Legislativo',
                'em_revisao': 'Em Revisão',
                'aguardando_aprovacao_autor': 'Aguardando Aprovação do Autor',
                'devolvido_edicao': 'Devolvido para Edição',
                'retornado_legislativo': 'Retornado do Legislativo',
                'aprovado': 'Aprovado',
                'reprovado': 'Reprovado'
            };
            return statusTexts[this.proposicao?.status] || 'Status Desconhecido';
        },
        isOwner() {
            return this.proposicao?.autor?.id === this.USER_ID;
        },
        canEdit() {
            if (!this.proposicao) return false;
            const editableStatuses = ['rascunho', 'em_edicao', 'devolvido_edicao'];
            return editableStatuses.includes(this.proposicao.status) && (this.isOwner || this.USER_ROLE === 'ADMIN');
        },
        canSendToLegislativo() {
            // Pode enviar se está em rascunho/edição e tem ementa e conteúdo
            if (!this.proposicao) return false;
            const validStatuses = ['rascunho', 'em_edicao', 'devolvido_edicao'];
            const hasContent = this.proposicao.ementa && this.proposicao.conteudo;
            return validStatuses.includes(this.proposicao.status) && hasContent && (this.isOwner || this.USER_ROLE === 'PARLAMENTAR');
        },
        canSign() {
            return this.proposicao?.status === 'aprovado' && 
                   (this.USER_ROLE === 'PARLAMENTAR' || this.USER_ROLE === 'ADMIN' || this.isOwner);
        },
        shortContent() {
            if (!this.proposicao?.conteudo) return 'Nenhum conteúdo disponível';
            return this.proposicao.conteudo.length > 500 
                ? this.proposicao.conteudo.substring(0, 500) + '...'
                : this.proposicao.conteudo;
        },
        authorName() {
            return this.proposicao?.autor?.name || 'N/A';
        },
        tipoNome() {
            return this.proposicao?.tipo_proposicao?.nome || this.proposicao?.tipo || 'N/A';
        }
    },
    methods: {
        updateStatus(newStatus) {
            // Para implementar: redirecionamento para rota de atualização de status
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/proposicoes/${this.proposicao.id}/status`;
            
            // CSRF Token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            // Method spoofing
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PATCH';
            form.appendChild(methodInput);
            
            // Status
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = newStatus;
            form.appendChild(statusInput);
            
            document.body.appendChild(form);
            form.submit();
        },
        
        showStatusChangeNotification(oldStatus, newStatus) {
            const message = `Status alterado de "${this.getStatusText(oldStatus)}" para "${this.getStatusText(newStatus)}"`;
            this.showNotification(message, 'info');
        },
        
        getStatusText(status) {
            const statusTexts = {
                'rascunho': 'Rascunho',
                'em_edicao': 'Em Edição',
                'enviado_legislativo': 'Enviado ao Legislativo',
                'em_revisao': 'Em Revisão',
                'aguardando_aprovacao_autor': 'Aguardando Aprovação do Autor',
                'devolvido_edicao': 'Devolvido para Edição',
                'retornado_legislativo': 'Retornado do Legislativo',
                'aprovado': 'Aprovado',
                'reprovado': 'Reprovado'
            };
            return statusTexts[status] || 'Status Desconhecido';
        },
        
        showNotification(message, type = 'info') {
            // Criar container de toasts se não existir
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                toastContainer.style.zIndex = '1050';
                document.body.appendChild(toastContainer);
            }
            
            const toastId = 'toast-' + Date.now();
            const bgClass = type === 'error' ? 'bg-danger' : type === 'success' ? 'bg-success' : 'bg-primary';
            
            const toastHtml = `
                <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="document.getElementById('${toastId}').remove()"></button>
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            
            // Auto-remove após 5 segundos
            setTimeout(() => {
                const toast = document.getElementById(toastId);
                if (toast) toast.remove();
            }, 5000);
        },
        
        formatDate(date) {
            if (!date) return '';
            return new Date(date).toLocaleString('pt-BR');
        },
        
        toggleContent() {
            this.showFullContent = !this.showFullContent;
        },
        
        openOnlyOfficeEditor() {
            // Determinar qual editor usar baseado no perfil
            let url;
            if (this.USER_ROLE === 'LEGISLATIVO') {
                url = `/proposicoes/${this.proposicao.id}/onlyoffice/editor`;
            } else {
                url = `/proposicoes/${this.proposicao.id}/onlyoffice/editor-parlamentar`;
            }
            // Abrir na mesma página
            window.location.href = url;
        },
        
        openSignaturePage() {
            const url = `/proposicoes/${this.proposicao.id}/assinar`;
            window.open(url, '_blank');
        },
        
        enviarParaLegislativo() {
            if (!confirm('Deseja enviar esta proposição para análise do Legislativo?')) {
                return;
            }
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/proposicoes/${this.proposicao.id}/enviar-legislativo`;
            
            // CSRF Token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            // Method spoofing
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);
            
            document.body.appendChild(form);
            form.submit();
        },
        
        viewPDF() {
            const url = `/proposicoes/${this.proposicao.id}/pdf`;
            window.open(url, '_blank');
        },
        
        async refreshData() {
            this.loading = true;
            try {
                const response = await fetch(`/api/proposicoes/${this.proposicao.id}`, {
                    method: 'GET',
                    credentials: 'same-origin', // Incluir cookies de sessão
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    const oldStatus = this.proposicao.status;
                    this.proposicao = data.proposicao;
                    this.lastUpdate = new Date();
                    this.updateCount++;
                    this.pollingErrors = 0; // Reset erro contador
                    
                    // Mostrar notificação se status mudou
                    if (oldStatus !== this.proposicao.status) {
                        this.showStatusChangeNotification(oldStatus, this.proposicao.status);
                    }
                    
                    console.log('Dados atualizados via API:', data.proposicao);
                } else {
                    throw new Error(data.message || 'Erro ao buscar dados');
                }
            } catch (error) {
                this.error = `Erro ao atualizar dados: ${error.message}`;
                console.error('Erro na atualização:', error);
                
                // Log detalhado para debugging
                console.error('Details:', {
                    url: `/api/proposicoes/${this.proposicao.id}`,
                    error: error.message,
                    stack: error.stack
                });
            } finally {
                this.loading = false;
            }
        },

        async checkForUpdates() {
            if (!this.pollingEnabled || !document.hasFocus()) {
                return;
            }

            try {
                const response = await fetch(`/api/proposicoes/${this.proposicao.id}/updates?last_update=${this.proposicao.updated_at}`, {
                    method: 'GET',
                    credentials: 'same-origin', // Incluir cookies de sessão
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.has_updates) {
                        console.log('Atualizações detectadas, recarregando dados...');
                        await this.refreshData();
                    }
                }
            } catch (error) {
                console.log('Erro no polling (ignorado):', error);
                // Se falhar 3 vezes seguidas, pausar o polling
                this.pollingErrors = (this.pollingErrors || 0) + 1;
                if (this.pollingErrors >= 3) {
                    console.warn('Muitos erros de polling, pausando...');
                    this.togglePolling(); // Pausa o polling
                    this.showNotification('Polling pausado devido a erros na conexão', 'error');
                }
            }
        },

        startPolling() {
            this.stopPolling();
            this.pollingInterval = setInterval(() => {
                this.checkForUpdates();
            }, 30000); // 30 segundos
            console.log('Polling iniciado: verificação a cada 30 segundos');
        },

        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
                this.pollingInterval = null;
                console.log('Polling parado');
            }
        },

        togglePolling() {
            this.pollingEnabled = !this.pollingEnabled;
            if (this.pollingEnabled) {
                this.startPolling();
                this.showNotification('Atualizações automáticas ativadas', 'success');
            } else {
                this.stopPolling();
                this.showNotification('Atualizações automáticas desativadas', 'info');
            }
        },
        
        getStatusIcon(status) {
            const icons = {
                'rascunho': 'fas fa-edit',
                'em_edicao': 'fas fa-pencil-alt',
                'enviado_legislativo': 'fas fa-paper-plane',
                'em_revisao': 'fas fa-search',
                'aguardando_aprovacao_autor': 'fas fa-user-clock',
                'devolvido_edicao': 'fas fa-undo',
                'retornado_legislativo': 'fas fa-reply',
                'aprovado': 'fas fa-check-circle',
                'reprovado': 'fas fa-times-circle'
            };
            return icons[status] || 'fas fa-question-circle';
        },
        
        formatDateShort(date) {
            if (!date) return '';
            const d = new Date(date);
            return d.toLocaleDateString('pt-BR');
        },
        
        getHistorico() {
            const eventos = [];
            
            // Evento de criação
            eventos.push({
                title: 'Proposição Criada',
                description: `Por ${this.authorName}`,
                date: this.formatDate(this.proposicao.created_at),
                icon: 'fas fa-plus',
                color: 'primary'
            });
            
            // Eventos baseados no status atual
            const status = this.proposicao.status;
            
            if (status === 'em_edicao' || ['enviado_legislativo', 'em_revisao', 'aprovado', 'reprovado', 'devolvido_edicao'].includes(status)) {
                eventos.push({
                    title: 'Em Edição',
                    description: 'Conteúdo sendo elaborado',
                    date: this.formatDate(this.proposicao.updated_at),
                    icon: 'fas fa-pencil-alt',
                    color: 'warning'
                });
            }
            
            if (['enviado_legislativo', 'em_revisao', 'aprovado', 'reprovado'].includes(status)) {
                eventos.push({
                    title: 'Enviado ao Legislativo',
                    description: 'Para análise técnica',
                    date: this.formatDate(this.proposicao.updated_at),
                    icon: 'fas fa-paper-plane',
                    color: 'info'
                });
            }
            
            if (['em_revisao', 'aprovado', 'reprovado'].includes(status)) {
                eventos.push({
                    title: 'Em Revisão',
                    description: 'Análise do Legislativo',
                    date: this.formatDate(this.proposicao.updated_at),
                    icon: 'fas fa-search',
                    color: 'primary'
                });
            }
            
            if (status === 'devolvido_edicao') {
                eventos.push({
                    title: 'Devolvido para Edição',
                    description: 'Necessita ajustes',
                    date: this.formatDate(this.proposicao.updated_at),
                    icon: 'fas fa-undo',
                    color: 'warning'
                });
            }
            
            if (status === 'aprovado') {
                eventos.push({
                    title: 'Aprovado',
                    description: 'Proposição aprovada',
                    date: this.formatDate(this.proposicao.updated_at),
                    icon: 'fas fa-check-circle',
                    color: 'success'
                });
                
                if (this.proposicao.numero_protocolo) {
                    eventos.push({
                        title: 'Protocolado',
                        description: `Nº ${this.proposicao.numero_protocolo}`,
                        date: this.formatDate(this.proposicao.updated_at),
                        icon: 'fas fa-hashtag',
                        color: 'success'
                    });
                }
            }
            
            if (status === 'reprovado') {
                eventos.push({
                    title: 'Reprovado',
                    description: 'Proposição não aprovada',
                    date: this.formatDate(this.proposicao.updated_at),
                    icon: 'fas fa-times-circle',
                    color: 'danger'
                });
            }
            
            return eventos;
        }
    },
    
    mounted() {
        // Dados já carregados do Blade
        console.log('Interface Vue.js carregada com dados:', this.proposicao);
        console.log('User role:', this.USER_ROLE);
        
        // Verificar se existe parâmetro _refresh na URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('_refresh')) {
            // Remover o parâmetro _refresh e fazer refresh via API
            const cleanUrl = window.location.pathname;
            window.history.replaceState({}, document.title, cleanUrl);
            this.refreshData();
        }

        // Iniciar polling automático
        this.startPolling();

        // Parar polling quando janela perde foco e retomar quando ganha
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                console.log('Janela oculta, pausando polling');
            } else {
                console.log('Janela visível, fazendo refresh automático de dados');
                // Aguardar um pouco para os callbacks do OnlyOffice serem processados
                setTimeout(() => {
                    this.refreshData();
                }, 2000);
            }
        });

        // Detectar quando usuário retorna de OnlyOffice via focus
        window.addEventListener('focus', () => {
            console.log('Janela ganhou foco, verificando atualizações');
            setTimeout(() => {
                this.refreshData();
            }, 1000);
        });
    },

    beforeUnmount() {
        this.stopPolling();
    },
    
    template: `
        <div>
            <!-- Loading State -->
            <div v-if="loading" class="loading-spinner">
                <div class="spinner"></div>
            </div>
            
            <!-- Error State -->
            <div v-else-if="error" class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                @{{ error }}
                <button @click="refreshData()" class="btn btn-outline-danger btn-sm ms-3">
                    <i class="fas fa-redo me-1"></i> Tentar Novamente
                </button>
            </div>
            
            <!-- Main Content -->
            <div v-else-if="proposicao">
                <!-- Header Card -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-gradient-primary text-white py-3">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h2 class="mb-0 fw-bold">
                                            <i class="fas fa-file-alt me-2"></i>
                                            @{{ (proposicao.tipo || 'Proposição').toUpperCase() }} 
                                            <span class="badge bg-white text-primary ms-2">#@{{ proposicao.id }}</span>
                                        </h2>
                                    </div>
                                    <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                        <span :class="'badge fs-6 px-3 py-2 bg-' + statusClass">
                                            <i :class="getStatusIcon(proposicao.status)" class="me-1"></i>
                                            @{{ statusText }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Informações Básicas -->
                            <div class="card-body border-bottom">
                                <div class="row">
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Autor</small>
                                                <strong>@{{ authorName }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                                <i class="fas fa-calendar text-info"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Criado em</small>
                                                <strong>@{{ formatDateShort(proposicao.created_at) }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                                <i class="fas fa-hashtag text-success"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Protocolo</small>
                                                <strong>@{{ proposicao.numero_protocolo || '[Aguardando]' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Ementa -->
                            <div class="card-body">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-quote-left me-2"></i>
                                    Ementa
                                </h5>
                                <p class="mb-0 fs-5 text-dark">
                                    @{{ proposicao.ementa || 'Ementa não definida' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Content and Actions Row -->
                <div class="row">
                    <!-- Content Card -->
                    <div class="col-lg-8">
                        <div class="card card-hover h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-document me-2"></i>Conteúdo da Proposição</h5>
                                <button 
                                    v-if="proposicao.conteudo && proposicao.conteudo.length > 500"
                                    @click="toggleContent()" 
                                    class="btn btn-outline-primary btn-sm">
                                    <i :class="showFullContent ? 'fas fa-compress' : 'fas fa-expand'"></i>
                                    @{{ showFullContent ? 'Mostrar Menos' : 'Mostrar Mais' }}
                                </button>
                            </div>
                            <div class="card-body">
                                <transition name="fade" mode="out-in">
                                    <div 
                                        :key="showFullContent"
                                        class="content-area border p-3 rounded bg-light">
                                        @{{ showFullContent ? (proposicao.conteudo || 'Nenhum conteúdo disponível') : shortContent }}
                                    </div>
                                </transition>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions Card -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white text-center">
                                <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Ações Disponíveis</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    
                                    <!-- Botões para PARLAMENTAR ou dono da proposição -->
                                    <template v-if="isOwner || USER_ROLE === 'PARLAMENTAR'">
                                        
                                        <!-- OnlyOffice Editor (rascunho ou em edição) -->
                                        <button 
                                            v-if="proposicao.status === 'rascunho' || proposicao.status === 'em_edicao'"
                                            @click="openOnlyOfficeEditor()"
                                            class="btn btn-lg btn-primary btn-onlyoffice">
                                            <i class="fas fa-file-word me-2"></i>
                                            @{{ proposicao.conteudo ? 'Continuar Editando' : 'Adicionar Conteúdo' }}
                                        </button>
                                        
                                        <!-- Enviar para Legislativo -->
                                        <button 
                                            v-if="canSendToLegislativo"
                                            @click="enviarParaLegislativo()"
                                            class="btn btn-lg btn-success">
                                            <i class="fas fa-paper-plane me-2"></i>
                                            Enviar para o Legislativo
                                        </button>
                                        
                                        <!-- OnlyOffice Editor (devolvido) -->
                                        <button 
                                            v-if="proposicao.status === 'devolvido_edicao'"
                                            @click="openOnlyOfficeEditor()"
                                            class="btn btn-lg btn-warning btn-onlyoffice">
                                            <i class="fas fa-edit me-2"></i>
                                            Revisar Proposição
                                        </button>
                                        
                                    </template>
                                    
                                    <!-- Botões para LEGISLATIVO -->
                                    <template v-if="USER_ROLE === 'LEGISLATIVO'">
                                        
                                        <!-- OnlyOffice Editor para Legislativo -->
                                        <button 
                                            v-if="proposicao.status === 'enviado_legislativo' || proposicao.status === 'em_revisao'"
                                            @click="openOnlyOfficeEditor()"
                                            class="btn btn-lg btn-info btn-onlyoffice">
                                            <i class="fas fa-file-alt me-2"></i>
                                            Revisar no Editor
                                        </button>
                                        
                                        <!-- Ações de Status -->
                                        <div v-if="proposicao.status === 'em_revisao'" class="mt-2">
                                            <button 
                                                @click="updateStatus('aprovado')"
                                                class="btn btn-success w-100 mb-2">
                                                <i class="fas fa-check me-2"></i>
                                                Aprovar Proposição
                                            </button>
                                            <button 
                                                @click="updateStatus('devolvido_edicao')"
                                                class="btn btn-warning w-100 mb-2">
                                                <i class="fas fa-undo me-2"></i>
                                                Devolver para Edição
                                            </button>
                                            <button 
                                                @click="updateStatus('reprovado')"
                                                class="btn btn-danger w-100">
                                                <i class="fas fa-times me-2"></i>
                                                Reprovar Proposição
                                            </button>
                                        </div>
                                        
                                    </template>
                                    
                                    <!-- Assinatura (disponível quando aprovado) -->
                                    <button 
                                        v-if="proposicao.status === 'aprovado' && (isOwner || USER_ROLE === 'PARLAMENTAR')"
                                        @click="openSignaturePage()"
                                        class="btn btn-lg btn-success btn-assinatura">
                                        <i class="fas fa-signature me-2"></i>
                                        Assinar Documento
                                    </button>
                                    
                                    <!-- Visualizar PDF (quando tem arquivo) -->
                                    <button 
                                        v-if="proposicao.arquivo_pdf_path"
                                        @click="viewPDF()"
                                        class="btn btn-outline-info">
                                        <i class="fas fa-file-pdf me-2"></i>
                                        Visualizar PDF
                                    </button>
                                    
                                    <!-- Refresh Button -->
                                    <hr class="my-3">
                                    <button 
                                        @click="refreshData()" 
                                        :disabled="loading"
                                        class="btn btn-sm btn-outline-primary">
                                        <i :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-sync'" class="me-1"></i>
                                        @{{ loading ? 'Atualizando...' : 'Atualizar Dados' }}
                                    </button>
                                    
                                    <!-- Polling Toggle -->
                                    <button 
                                        @click="togglePolling()" 
                                        class="btn btn-sm btn-outline-info mt-2">
                                        <i :class="pollingEnabled ? 'fas fa-pause' : 'fas fa-play'" class="me-1"></i>
                                        @{{ pollingEnabled ? 'Pausar Auto-Update' : 'Ativar Auto-Update' }}
                                    </button>
                                    
                                    <!-- Update Info -->
                                    <small class="text-muted d-block mt-2" v-if="updateCount > 0">
                                        <i class="fas fa-info-circle me-1"></i>
                                        @{{ updateCount }} atualizações
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Histórico de Tramitação -->
                        <div class="card shadow-sm mt-3">
                            <div class="card-header bg-light text-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-history me-2"></i>
                                    Histórico de Tramitação
                                </h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="timeline-container p-3">
                                    <div class="timeline-item" v-for="(event, index) in getHistorico()" :key="index">
                                        <div class="d-flex">
                                            <div class="timeline-icon">
                                                <div :class="'badge rounded-circle p-2 bg-' + event.color">
                                                    <i :class="event.icon" class="text-white"></i>
                                                </div>
                                            </div>
                                            <div class="timeline-content ms-3 flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <strong class="d-block">@{{ event.title }}</strong>
                                                        <small class="text-muted">@{{ event.description }}</small>
                                                    </div>
                                                </div>
                                                <small class="text-muted d-block mt-1">
                                                    <i class="fas fa-clock me-1"></i>
                                                    @{{ event.date }}
                                                </small>
                                            </div>
                                        </div>
                                        <div v-if="index < getHistorico().length - 1" class="timeline-line"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `
};

// Inicializar aplicação Vue
createApp(ProposicaoViewer).mount('#proposicao-app');
</script>
@endsection