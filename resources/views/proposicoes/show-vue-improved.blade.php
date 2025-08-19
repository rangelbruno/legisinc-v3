@extends('components.layouts.app')

@section('title', 'Visualizar Proposição')

@section('content')
<div id="proposicao-app" v-cloak>
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Page Header-->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-gradient-primary text-white py-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h1 class="mb-0 fw-bold text-white">
                                    <i class="ki-duotone ki-document fs-1 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    @{{ proposicao.tipo?.toUpperCase() || 'PROPOSIÇÃO' }}
                                    <span class="badge bg-white text-primary fs-6 ms-3">#@{{ proposicao.id }}</span>
                                </h1>
                                <p class="mb-0 text-white-75 fs-6">
                                    <i class="ki-duotone ki-calendar fs-7 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Criado em @{{ formatDate(proposicao.created_at) }}
                                </p>
                            </div>
                            <div class="text-end">
                                <span :class="getStatusBadgeClass(proposicao.status)" class="badge fs-5 px-4 py-2">
                                    <i :class="getStatusIcon(proposicao.status)" class="me-2"></i>
                                    @{{ getStatusText(proposicao.status) }}
                                </span>
                                <div class="mt-2">
                                    <small class="text-white-75">
                                        <i class="ki-duotone ki-time fs-7 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Atualizado há @{{ getTimeAgo(proposicao.updated_at) }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Page Header-->

        <!--begin::Main Content-->
        <div class="row g-4">
            <!--begin::Left Column - Content-->
            <div class="col-lg-8">
                <!--begin::Informações Básicas Card-->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="card-title m-0">
                                <i class="ki-duotone ki-information-4 fs-2 text-primary me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                Informações da Proposição
                            </h3>
                            <div class="d-flex align-items-center">
                                <span class="text-muted fs-7 me-3">Sincronização automática</span>
                                <div class="spinner-border spinner-border-sm text-primary" v-show="loading" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <i v-show="!loading" class="ki-duotone ki-check-circle fs-2 text-success" title="Dados atualizados">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <div class="symbol-label bg-light-primary">
                                            <i class="ki-duotone ki-profile-user fs-2 text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-muted fs-7">Autor</div>
                                        <div class="fw-bold fs-6">@{{ proposicao.autor?.name || 'N/A' }}</div>
                                        <div class="text-muted fs-8">@{{ proposicao.autor?.email || '' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <div class="symbol-label bg-light-info">
                                            <i class="ki-duotone ki-hashtag fs-2 text-info">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-muted fs-7">Protocolo</div>
                                        <div class="fw-bold fs-6" :class="proposicao.numero_protocolo ? 'text-success' : 'text-warning'">
                                            @{{ proposicao.numero_protocolo || '[Aguardando]' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <div class="symbol-label bg-light-success">
                                            <i class="ki-duotone ki-file fs-2 text-success">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-muted fs-7">Documentos</div>
                                        <div class="fw-bold fs-6">
                                            <span v-if="proposicao.has_arquivo" class="badge badge-light-success me-1">DOCX</span>
                                            <span v-if="proposicao.has_pdf" class="badge badge-light-danger">PDF</span>
                                            <span v-if="!proposicao.has_arquivo && !proposicao.has_pdf" class="text-muted">Nenhum</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Informações Básicas Card-->

                <!--begin::Ementa Card-->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light">
                        <h3 class="card-title m-0">
                            <i class="ki-duotone ki-notepad-edit fs-2 text-warning me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Ementa
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="fs-5 text-gray-800 lh-lg" v-if="proposicao.ementa">
                            @{{ proposicao.ementa }}
                        </div>
                        <div class="text-muted fst-italic" v-else>
                            Ementa não definida
                        </div>
                    </div>
                </div>
                <!--end::Ementa Card-->

                <!--begin::Conteúdo Card-->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="card-title m-0">
                                <i class="ki-duotone ki-document fs-2 text-success me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Conteúdo da Proposição
                            </h3>
                            <div class="d-flex align-items-center" v-if="proposicao.conteudo && proposicao.conteudo.length > 500">
                                <button 
                                    @click="toggleContent" 
                                    class="btn btn-sm btn-light-primary">
                                    <i :class="showFullContent ? 'ki-duotone ki-up' : 'ki-duotone ki-down'" class="fs-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    @{{ showFullContent ? 'Mostrar Menos' : 'Mostrar Mais' }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="content-display" v-if="proposicao.conteudo">
                            <div v-if="proposicao.conteudo.length <= 500" class="fs-6 text-gray-700 lh-lg">
                                @{{ proposicao.conteudo }}
                            </div>
                            <div v-else>
                                <div v-show="!showFullContent" class="fs-6 text-gray-700 lh-lg">
                                    @{{ proposicao.conteudo.substring(0, 500) }}...
                                </div>
                                <div v-show="showFullContent" class="fs-6 text-gray-700 lh-lg">
                                    @{{ proposicao.conteudo }}
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-5">
                            <i class="ki-duotone ki-file-added fs-3x text-muted mb-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="text-muted">Nenhum conteúdo disponível</div>
                            <small class="text-muted">Adicione conteúdo editando a proposição</small>
                        </div>
                    </div>
                </div>
                <!--end::Conteúdo Card-->
            </div>
            <!--end::Left Column-->

            <!--begin::Right Column - Actions-->
            <div class="col-lg-4">
                <!--begin::Ações Card-->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light">
                        <h3 class="card-title m-0">
                            <i class="ki-duotone ki-gear fs-2 text-primary me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Ações Disponíveis
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-3">
                            <!--begin::OnlyOffice Editor-->
                            <div v-if="canEdit()">
                                <a 
                                    :href="getEditorUrl()"
                                    class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center"
                                    style="min-height: 50px;">
                                    <i class="ki-duotone ki-file-edit fs-2 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="text-start">
                                        <div class="fw-bold">@{{ getEditorButtonText() }}</div>
                                        <small class="text-white-75">Editor OnlyOffice</small>
                                    </div>
                                </a>
                            </div>
                            <!--end::OnlyOffice Editor-->

                            <!--begin::Enviar para Legislativo-->
                            <div v-if="canSendToLegislative()">
                                <form 
                                    :action="'/proposicoes/' + proposicao.id + '/enviar-legislativo'" 
                                    method="POST" 
                                    @submit="confirmSendToLegislative">
                                    @csrf
                                    @method('PUT')
                                    <button 
                                        type="submit" 
                                        class="btn btn-success btn-lg w-100 d-flex align-items-center justify-content-center"
                                        style="min-height: 50px;">
                                        <i class="ki-duotone ki-send fs-2 me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="text-start">
                                            <div class="fw-bold">Enviar para Legislativo</div>
                                            <small class="text-white-75">Submeter para análise</small>
                                        </div>
                                    </button>
                                </form>
                            </div>
                            <!--end::Enviar para Legislativo-->

                            <!--begin::Status Actions for Legislative-->
                            <div v-if="canUpdateStatus()">
                                <div class="separator my-3"></div>
                                <h6 class="text-muted mb-3">Ações do Legislativo</h6>
                                
                                <form 
                                    :action="'/proposicoes/' + proposicao.id + '/status'" 
                                    method="POST" 
                                    @submit="updateStatusAction"
                                    class="mb-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="aprovado">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="ki-duotone ki-check fs-3 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Aprovar Proposição
                                    </button>
                                </form>

                                <form 
                                    :action="'/proposicoes/' + proposicao.id + '/status'" 
                                    method="POST" 
                                    @submit="updateStatusAction"
                                    class="mb-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="devolvido_edicao">
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="ki-duotone ki-arrow-left fs-3 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Devolver para Edição
                                    </button>
                                </form>

                                <form 
                                    :action="'/proposicoes/' + proposicao.id + '/status'" 
                                    method="POST" 
                                    @submit="updateStatusAction">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="reprovado">
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="ki-duotone ki-cross fs-3 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Reprovar Proposição
                                    </button>
                                </form>
                            </div>
                            <!--end::Status Actions-->

                            <!--begin::Sign Document-->
                            <div v-if="canSign()">
                                <div class="separator my-3"></div>
                                <a 
                                    :href="'/proposicoes/' + proposicao.id + '/assinar'"
                                    target="_blank"
                                    class="btn btn-light-success btn-lg w-100 d-flex align-items-center justify-content-center"
                                    style="min-height: 50px;">
                                    <i class="ki-duotone ki-signature fs-2 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="text-start">
                                        <div class="fw-bold">Assinar Documento</div>
                                        <small class="text-muted">Assinatura digital</small>
                                    </div>
                                </a>
                            </div>
                            <!--end::Sign Document-->

                            <!--begin::View PDF-->
                            <div v-if="proposicao.has_pdf">
                                <a 
                                    :href="'/proposicoes/' + proposicao.id + '/pdf'"
                                    target="_blank"
                                    class="btn btn-light-danger w-100">
                                    <i class="ki-duotone ki-file-down fs-3 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Visualizar PDF
                                </a>
                            </div>
                            <!--end::View PDF-->

                            <!--begin::Refresh-->
                            <div class="separator my-3"></div>
                            <button 
                                @click="forceRefresh" 
                                class="btn btn-light btn-sm w-100"
                                :disabled="loading">
                                <i class="ki-duotone ki-arrows-circle fs-4 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                @{{ loading ? 'Atualizando...' : 'Atualizar Dados' }}
                            </button>
                        </div>
                    </div>
                </div>
                <!--end::Ações Card-->

                <!--begin::Histórico Card-->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h3 class="card-title m-0">
                            <i class="ki-duotone ki-time fs-2 text-info me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Histórico de Tramitação
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div v-for="(evento, index) in timeline" :key="index" class="timeline-item">
                                <div class="timeline-line" v-if="index < timeline.length - 1"></div>
                                <div class="timeline-icon">
                                    <div :class="'badge badge-circle badge-' + evento.color + ' p-3'">
                                        <i :class="evento.icon + ' fs-4 text-white'"></i>
                                    </div>
                                </div>
                                <div class="timeline-content ms-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-1 fw-bold">@{{ evento.title }}</h6>
                                        <small class="text-muted">@{{ formatDateTime(evento.date) }}</small>
                                    </div>
                                    <p class="text-muted mb-0 fs-7">@{{ evento.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Histórico Card-->
            </div>
            <!--end::Right Column-->
        </div>
        <!--end::Main Content-->
    </div>
    <!--end::Container-->

    <!--begin::Toast Notifications-->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1080">
        <div 
            v-for="(toast, index) in toasts" 
            :key="index"
            :class="'toast align-items-center text-white bg-' + toast.type + ' border-0'"
            role="alert" 
            :class="{ show: toast.show }"
            style="min-width: 300px;">
            <div class="d-flex">
                <div class="toast-body">
                    <i :class="toast.icon + ' me-2'"></i>
                    @{{ toast.message }}
                </div>
                <button 
                    type="button" 
                    class="btn-close btn-close-white me-2 m-auto" 
                    @click="removeToast(index)">
                </button>
            </div>
        </div>
    </div>
    <!--end::Toast Notifications-->
</div>

<!-- Vue 3 and Dependencies -->
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<style>
[v-cloak] { display: none; }

.bg-gradient-primary {
    background: linear-gradient(135deg, #3699FF 0%, #0BB783 100%);
}

.content-display {
    max-height: none;
    overflow: visible;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.timeline {
    position: relative;
}

.timeline-item {
    position: relative;
    padding-bottom: 2rem;
    display: flex;
    align-items: flex-start;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-line {
    position: absolute;
    left: 19px;
    top: 38px;
    bottom: -2rem;
    width: 2px;
    background: #E1E3EA;
}

.timeline-icon {
    position: relative;
    z-index: 1;
    flex-shrink: 0;
}

.timeline-content {
    flex: 1;
}

.badge-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toast {
    margin-bottom: 0.5rem;
}

.btn:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.card {
    transition: box-shadow 0.15s ease;
}

.card:hover {
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
}
</style>

<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            proposicao: @json($proposicao ?? null),
            userRole: '{{ auth()->user()->getRoleNames()->first() ?? "guest" }}',
            userId: {{ auth()->user()->id ?? 0 }},
            csrfToken: '{{ csrf_token() }}',
            loading: false,
            lastUpdate: null,
            pollingInterval: null,
            showFullContent: false,
            toasts: [],
            timeline: []
        }
    },
    
    mounted() {
        this.setupAxios();
        this.loadProposicao();
        this.generateTimeline();
        this.startPolling();
    },
    
    beforeUnmount() {
        this.stopPolling();
    },
    
    methods: {
        setupAxios() {
            // Configure Axios defaults
            axios.defaults.headers.common['X-CSRF-TOKEN'] = this.csrfToken;
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            axios.defaults.withCredentials = true;
        },
        
        async loadProposicao() {
            if (!this.proposicao?.id) return;
            
            this.loading = true;
            try {
                const response = await axios.get(`/api/proposicoes/${this.proposicao.id}`);
                if (response.data.success) {
                    this.proposicao = response.data.proposicao;
                    this.lastUpdate = response.data.timestamp;
                    this.generateTimeline();
                }
            } catch (error) {
                console.error('Erro ao carregar proposição:', error);
                this.showToast('Erro ao carregar dados', 'danger', 'ki-duotone ki-cross-circle');
            } finally {
                this.loading = false;
            }
        },
        
        startPolling() {
            // Polling inteligente a cada 30 segundos
            this.pollingInterval = setInterval(() => {
                if (!document.hidden) { // Só atualiza se a aba estiver visível
                    this.checkForUpdates();
                }
            }, 30000);
        },
        
        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
                this.pollingInterval = null;
            }
        },
        
        async checkForUpdates() {
            if (!this.proposicao?.id || this.loading) return;
            
            try {
                const response = await axios.get(`/api/proposicoes/${this.proposicao.id}/updates`, {
                    params: { last_update: this.lastUpdate }
                });
                
                if (response.data.success && response.data.has_updates) {
                    await this.loadProposicao();
                    this.showToast('Dados atualizados automaticamente', 'success', 'ki-duotone ki-check-circle');
                }
            } catch (error) {
                console.error('Erro ao verificar atualizações:', error);
            }
        },
        
        async forceRefresh() {
            await this.loadProposicao();
            this.showToast('Dados atualizados manualmente', 'info', 'ki-duotone ki-arrows-circle');
        },
        
        generateTimeline() {
            if (!this.proposicao) return;
            
            const events = [];
            
            // Evento de criação
            events.push({
                title: 'Proposição Criada',
                description: `Por ${this.proposicao.autor?.name || 'N/A'}`,
                date: this.proposicao.created_at,
                icon: 'ki-duotone ki-plus',
                color: 'primary'
            });
            
            // Eventos baseados no status
            const status = this.proposicao.status;
            
            if (['em_edicao', 'enviado_legislativo', 'em_revisao', 'aprovado', 'reprovado', 'devolvido_edicao'].includes(status)) {
                events.push({
                    title: 'Em Edição',
                    description: 'Conteúdo sendo elaborado',
                    date: this.proposicao.updated_at,
                    icon: 'ki-duotone ki-pencil',
                    color: 'warning'
                });
            }
            
            if (['enviado_legislativo', 'em_revisao', 'aprovado', 'reprovado'].includes(status)) {
                events.push({
                    title: 'Enviado ao Legislativo',
                    description: 'Para análise técnica',
                    date: this.proposicao.updated_at,
                    icon: 'ki-duotone ki-send',
                    color: 'info'
                });
            }
            
            if (['em_revisao', 'aprovado', 'reprovado'].includes(status)) {
                events.push({
                    title: 'Em Revisão',
                    description: 'Análise do Legislativo',
                    date: this.proposicao.updated_at,
                    icon: 'ki-duotone ki-search-list',
                    color: 'primary'
                });
            }
            
            if (status === 'aprovado') {
                events.push({
                    title: 'Aprovado',
                    description: 'Proposição aprovada',
                    date: this.proposicao.updated_at,
                    icon: 'ki-duotone ki-check-circle',
                    color: 'success'
                });
                
                if (this.proposicao.numero_protocolo) {
                    events.push({
                        title: 'Protocolado',
                        description: `Nº ${this.proposicao.numero_protocolo}`,
                        date: this.proposicao.updated_at,
                        icon: 'ki-duotone ki-hashtag',
                        color: 'success'
                    });
                }
            }
            
            if (status === 'reprovado') {
                events.push({
                    title: 'Reprovado',
                    description: 'Proposição não aprovada',
                    date: this.proposicao.updated_at,
                    icon: 'ki-duotone ki-cross-circle',
                    color: 'danger'
                });
            }
            
            if (status === 'devolvido_edicao') {
                events.push({
                    title: 'Devolvido para Edição',
                    description: 'Necessita ajustes',
                    date: this.proposicao.updated_at,
                    icon: 'ki-duotone ki-arrow-left',
                    color: 'warning'
                });
            }
            
            this.timeline = events.reverse();
        },
        
        // Utility Methods
        toggleContent() {
            this.showFullContent = !this.showFullContent;
        },
        
        canEdit() {
            if (!this.proposicao) return false;
            const isOwner = this.proposicao.autor_id === this.userId;
            return (isOwner || this.userRole === 'PARLAMENTAR') && 
                   ['rascunho', 'em_edicao', 'devolvido_edicao'].includes(this.proposicao.status);
        },
        
        canSendToLegislative() {
            if (!this.proposicao) return false;
            const isOwner = this.proposicao.autor_id === this.userId;
            return (isOwner || this.userRole === 'PARLAMENTAR') && 
                   ['rascunho', 'em_edicao', 'devolvido_edicao'].includes(this.proposicao.status) &&
                   this.proposicao.ementa && this.proposicao.conteudo;
        },
        
        canUpdateStatus() {
            return this.userRole === 'LEGISLATIVO' && this.proposicao.status === 'em_revisao';
        },
        
        canSign() {
            if (!this.proposicao) return false;
            const isOwner = this.proposicao.autor_id === this.userId;
            return this.proposicao.status === 'aprovado' && (isOwner || this.userRole === 'PARLAMENTAR');
        },
        
        getEditorUrl() {
            if (!this.proposicao) return '#';
            const isOwner = this.proposicao.autor_id === this.userId;
            return isOwner || this.userRole === 'PARLAMENTAR' 
                ? `/proposicoes/${this.proposicao.id}/onlyoffice/editor-parlamentar`
                : `/proposicoes/${this.proposicao.id}/onlyoffice/editor`;
        },
        
        getEditorButtonText() {
            if (!this.proposicao) return 'Editar';
            return this.proposicao.conteudo ? 'Continuar Editando' : 'Adicionar Conteúdo';
        },
        
        getStatusBadgeClass(status) {
            const classes = {
                'rascunho': 'badge-warning',
                'em_edicao': 'badge-warning',
                'enviado_legislativo': 'badge-secondary',
                'em_revisao': 'badge-primary',
                'aguardando_aprovacao_autor': 'badge-primary',
                'devolvido_edicao': 'badge-warning',
                'retornado_legislativo': 'badge-info',
                'aprovado': 'badge-success',
                'reprovado': 'badge-danger'
            };
            return classes[status] || 'badge-secondary';
        },
        
        getStatusIcon(status) {
            const icons = {
                'rascunho': 'ki-duotone ki-pencil',
                'em_edicao': 'ki-duotone ki-pencil',
                'enviado_legislativo': 'ki-duotone ki-send',
                'em_revisao': 'ki-duotone ki-search-list',
                'aguardando_aprovacao_autor': 'ki-duotone ki-time',
                'devolvido_edicao': 'ki-duotone ki-arrow-left',
                'retornado_legislativo': 'ki-duotone ki-arrow-right',
                'aprovado': 'ki-duotone ki-check-circle',
                'reprovado': 'ki-duotone ki-cross-circle'
            };
            return icons[status] || 'ki-duotone ki-question';
        },
        
        getStatusText(status) {
            const texts = {
                'rascunho': 'Rascunho',
                'em_edicao': 'Em Edição',
                'enviado_legislativo': 'Enviado ao Legislativo',
                'em_revisao': 'Em Revisão',
                'aguardando_aprovacao_autor': 'Aguardando Aprovação',
                'devolvido_edicao': 'Devolvido para Edição',
                'retornado_legislativo': 'Retornado do Legislativo',
                'aprovado': 'Aprovado',
                'reprovado': 'Reprovado'
            };
            return texts[status] || 'Status Desconhecido';
        },
        
        formatDate(dateString) {
            if (!dateString) return 'N/A';
            return new Date(dateString).toLocaleDateString('pt-BR');
        },
        
        formatDateTime(dateString) {
            if (!dateString) return 'N/A';
            return new Date(dateString).toLocaleString('pt-BR');
        },
        
        getTimeAgo(dateString) {
            if (!dateString) return 'N/A';
            const now = new Date();
            const date = new Date(dateString);
            const diffInMinutes = Math.floor((now - date) / 1000 / 60);
            
            if (diffInMinutes < 1) return 'agora mesmo';
            if (diffInMinutes < 60) return `${diffInMinutes} min atrás`;
            if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h atrás`;
            return `${Math.floor(diffInMinutes / 1440)}d atrás`;
        },
        
        // Event Handlers
        confirmSendToLegislative(event) {
            if (!confirm('Deseja enviar esta proposição para análise do Legislativo? Esta ação não poderá ser desfeita.')) {
                event.preventDefault();
            }
        },
        
        updateStatusAction(event) {
            const form = event.target.closest('form');
            const status = form.querySelector('input[name="status"]').value;
            const statusTexts = {
                'aprovado': 'aprovar',
                'devolvido_edicao': 'devolver para edição',
                'reprovado': 'reprovar'
            };
            
            if (!confirm(`Deseja realmente ${statusTexts[status]} esta proposição?`)) {
                event.preventDefault();
            }
        },
        
        // Toast System
        showToast(message, type = 'success', icon = 'ki-duotone ki-check') {
            const toast = {
                message,
                type,
                icon,
                show: false
            };
            
            this.toasts.push(toast);
            
            // Show toast with small delay
            setTimeout(() => {
                toast.show = true;
            }, 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                this.removeToast(this.toasts.indexOf(toast));
            }, 5000);
        },
        
        removeToast(index) {
            if (this.toasts[index]) {
                this.toasts[index].show = false;
                setTimeout(() => {
                    this.toasts.splice(index, 1);
                }, 300);
            }
        }
    }
}).mount('#proposicao-app');
</script>
@endsection