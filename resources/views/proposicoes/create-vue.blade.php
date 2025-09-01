<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Criar Nova Proposição - Sistema Legisinc</title>
    
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Vue 3 CSS -->
    <style>
        [v-cloak] { display: none; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        
        /* Global utilities */
        .me-1 { margin-right: 0.25rem; }
        .me-2 { margin-right: 0.5rem; }
        .ms-2 { margin-left: 0.5rem; }
        .ms-auto { margin-left: auto; }
        .mt-3 { margin-top: 1rem; }
        .mt-5 { margin-top: 3rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        .text-muted { color: #718096 !important; }
        .text-danger { color: #f56565 !important; }
        .d-flex { display: flex; }
        .gap-2 > * + * { margin-left: 0.5rem; }
        .position-fixed { position: fixed; }
        .top-0 { top: 0; }
        .end-0 { right: 0; }
        .p-3 { padding: 1rem; }
        .show { display: block; }
        
        /* Bootstrap-like classes */
        .btn-close {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: #6c757d;
        }
        
        .btn-close:hover {
            color: #000;
        }
        
        .form-control {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        
        .form-control:focus {
            color: #212529;
            background-color: #fff;
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
        }
        
        .progress-bar-striped {
            background-image: linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);
            background-size: 1rem 1rem;
        }
        
        .progress-bar-animated {
            animation: progress-bar-stripes 1s linear infinite;
        }
        
        @keyframes progress-bar-stripes {
            0% { background-position-x: 1rem; }
        }
    </style>
</head>
<body>
    <div id="app" v-cloak>
        <proposicao-create></proposicao-create>
    </div>

    <!-- Vue 3 and Dependencies -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <!-- Configure Axios -->
    <script>
        // Configure axios defaults
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        
        // Add CSRF token to all requests
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
        }
    </script>

    <!-- Vue Component -->
    <script>
        const { createApp, ref, computed, onMounted, watch } = Vue;

        const ProposicaoCreate = {
            name: 'ProposicaoCreate',
            template: `
                <div class="proposicao-create-container">
                    <!-- Header com animação -->
                    <transition name="fade-slide">
                        <div class="header-section">
                            <div class="header-content">
                                <h1 class="page-title">
                                    <span class="title-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </span>
                                    Criar Nova Proposição
                                </h1>
                                <p class="page-subtitle">Complete as etapas abaixo para criar sua proposição</p>
                            </div>
                            <div class="header-actions">
                                <button @click="salvarRascunho" class="btn btn-outline-secondary" :disabled="!podeSalvarRascunho">
                                    <i class="fas fa-save me-2"></i>
                                    Salvar Rascunho
                                </button>
                            </div>
                        </div>
                    </transition>

                    <!-- Progress Steps -->
                    <div class="progress-steps">
                        <div 
                            v-for="(step, index) in steps" 
                            :key="index"
                            class="step-item"
                            :class="{
                                'active': currentStep === index,
                                'completed': currentStep > index
                            }"
                        >
                            <div class="step-number">
                                <transition name="scale">
                                    <i v-if="currentStep > index" class="fas fa-check"></i>
                                    <span v-else>{{ index + 1 }}</span>
                                </transition>
                            </div>
                            <div class="step-info">
                                <div class="step-title">{{ step.title }}</div>
                                <div class="step-description">{{ step.description }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="main-content">
                        <transition name="slide-fade" mode="out-in">
                            <!-- Step 1: Tipo de Proposição -->
                            <div v-if="currentStep === 0" key="step1" class="step-content">
                                <div class="section-card">
                                    <h2 class="section-title">
                                        <i class="fas fa-list-ul me-2"></i>
                                        Selecione o Tipo de Proposição
                                    </h2>
                                    
                                    <div class="tipo-grid">
                                        <div 
                                            v-for="tipo in tiposProposicao" 
                                            :key="tipo.codigo"
                                            class="tipo-card"
                                            :class="{ 'selected': formData.tipo === tipo.codigo }"
                                            @click="selecionarTipo(tipo)"
                                        >
                                            <div class="tipo-icon">
                                                <i :class="tipo.icon"></i>
                                            </div>
                                            <div class="tipo-content">
                                                <h3 class="tipo-name">{{ tipo.nome }}</h3>
                                                <p class="tipo-description">{{ tipo.descricao }}</p>
                                            </div>
                                            <div class="tipo-check">
                                                <transition name="scale">
                                                    <i v-if="formData.tipo === tipo.codigo" class="fas fa-check-circle"></i>
                                                </transition>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Ementa e Opções -->
                            <div v-else-if="currentStep === 1" key="step2" class="step-content">
                                <div class="section-card">
                                    <h2 class="section-title">
                                        <i class="fas fa-align-left me-2"></i>
                                        Ementa da Proposição
                                    </h2>
                                    
                                    <!-- Campo de Ementa com contador -->
                                    <div class="ementa-section">
                                        <div class="form-group">
                                            <label class="form-label">
                                                Descreva o objetivo da proposição
                                                <span class="required">*</span>
                                            </label>
                                            <div class="textarea-wrapper">
                                                <textarea
                                                    v-model="formData.ementa"
                                                    class="form-control ementa-input"
                                                    :class="{ 'is-invalid': ementaError }"
                                                    placeholder="Ex: Dispõe sobre a criação do programa municipal de incentivo à leitura..."
                                                    rows="4"
                                                    @input="validateEmenta"
                                                    maxlength="1000"
                                                ></textarea>
                                                <div class="char-counter" :class="{ 'text-danger': formData.ementa.length > 900 }">
                                                    {{ formData.ementa.length }}/1000 caracteres
                                                </div>
                                            </div>
                                            <div v-if="ementaError" class="invalid-feedback">
                                                {{ ementaError }}
                                            </div>
                                            <div class="form-hint">
                                                <i class="fas fa-info-circle me-1"></i>
                                                A ementa deve ser clara, objetiva e descrever precisamente o conteúdo da proposição
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Opções de Preenchimento -->
                                    <div class="opcoes-section mt-5">
                                        <h3 class="subsection-title">Como deseja criar o conteúdo?</h3>
                                        
                                        <div class="opcoes-grid">
                                            <!-- Opção Template Automático -->
                                            <div 
                                                class="opcao-card template-option"
                                                :class="{ 'selected': formData.opcaoPreenchimento === 'template' }"
                                                @click="selecionarOpcao('template')"
                                            >
                                                <div class="opcao-header">
                                                    <div class="opcao-icon">
                                                        <i class="fas fa-file-invoice"></i>
                                                    </div>
                                                    <div class="opcao-badge">Recomendado</div>
                                                </div>
                                                <h4 class="opcao-title">Usar Template Padrão</h4>
                                                <p class="opcao-description">
                                                    Template configurado pelo administrador com formatação profissional e campos pré-definidos
                                                </p>
                                                <div class="opcao-features">
                                                    <span class="feature-tag">
                                                        <i class="fas fa-check me-1"></i>
                                                        Formatação ABNT
                                                    </span>
                                                    <span class="feature-tag">
                                                        <i class="fas fa-check me-1"></i>
                                                        Rápido
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Opção Texto Personalizado -->
                                            <div 
                                                class="opcao-card"
                                                :class="{ 'selected': formData.opcaoPreenchimento === 'manual' }"
                                                @click="selecionarOpcao('manual')"
                                            >
                                                <div class="opcao-header">
                                                    <div class="opcao-icon">
                                                        <i class="fas fa-edit"></i>
                                                    </div>
                                                </div>
                                                <h4 class="opcao-title">Texto Personalizado</h4>
                                                <p class="opcao-description">
                                                    Escreva o conteúdo manualmente com total liberdade criativa
                                                </p>
                                                <div class="opcao-features">
                                                    <span class="feature-tag">
                                                        <i class="fas fa-check me-1"></i>
                                                        Flexível
                                                    </span>
                                                    <span class="feature-tag">
                                                        <i class="fas fa-check me-1"></i>
                                                        Controle total
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Opção IA -->
                                            <div 
                                                class="opcao-card"
                                                :class="{ 'selected': formData.opcaoPreenchimento === 'ia' }"
                                                @click="selecionarOpcao('ia')"
                                            >
                                                <div class="opcao-header">
                                                    <div class="opcao-icon">
                                                        <i class="fas fa-robot"></i>
                                                    </div>
                                                    <div class="opcao-badge ia-badge">IA</div>
                                                </div>
                                                <h4 class="opcao-title">Gerar com IA</h4>
                                                <p class="opcao-description">
                                                    Inteligência artificial cria o conteúdo baseado na ementa
                                                </p>
                                                <div class="opcao-features">
                                                    <span class="feature-tag">
                                                        <i class="fas fa-check me-1"></i>
                                                        Automático
                                                    </span>
                                                    <span class="feature-tag">
                                                        <i class="fas fa-check me-1"></i>
                                                        Inteligente
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3: Conteúdo -->
                            <div v-else-if="currentStep === 2" key="step3" class="step-content">
                                <div class="section-card">
                                    <!-- Template Automático -->
                                    <div v-if="formData.opcaoPreenchimento === 'template'">
                                        <h2 class="section-title">
                                            <i class="fas fa-file-invoice me-2"></i>
                                            Template Selecionado
                                        </h2>
                                        
                                        <div class="template-preview">
                                            <div class="template-header">
                                                <h3>{{ templateSelecionado?.nome || 'Carregando template...' }}</h3>
                                                <span class="template-badge" :class="{
                                                    'badge-universal': templateSelecionado?.is_universal,
                                                    'badge-specific': !templateSelecionado?.is_universal
                                                }">
                                                    <i class="fas" :class="{
                                                        'fa-globe': templateSelecionado?.is_universal,
                                                        'fa-certificate': !templateSelecionado?.is_universal
                                                    }" class="me-1"></i>
                                                    {{ templateSelecionado?.is_universal ? 'Template Universal' : 'Template Específico' }}
                                                </span>
                                            </div>
                                            <div class="template-info">
                                                <p class="text-muted">
                                                    Este template será aplicado automaticamente com todas as variáveis preenchidas
                                                </p>
                                                <div class="template-variables">
                                                    <h4>Variáveis que serão preenchidas:</h4>
                                                    <div class="variables-grid">
                                                        <span class="variable-tag" v-for="var in variaveisTemplate" :key="var">
                                                            {{ var }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Texto Manual -->
                                    <div v-else-if="formData.opcaoPreenchimento === 'manual'">
                                        <h2 class="section-title">
                                            <i class="fas fa-edit me-2"></i>
                                            Digite o Conteúdo da Proposição
                                        </h2>
                                        
                                        <div class="editor-section">
                                            <div class="editor-toolbar">
                                                <button class="toolbar-btn" @click="formatText('bold')" title="Negrito">
                                                    <i class="fas fa-bold"></i>
                                                </button>
                                                <button class="toolbar-btn" @click="formatText('italic')" title="Itálico">
                                                    <i class="fas fa-italic"></i>
                                                </button>
                                                <button class="toolbar-btn" @click="formatText('underline')" title="Sublinhado">
                                                    <i class="fas fa-underline"></i>
                                                </button>
                                                <div class="toolbar-separator"></div>
                                                <button class="toolbar-btn" @click="formatText('list')" title="Lista">
                                                    <i class="fas fa-list-ul"></i>
                                                </button>
                                                <button class="toolbar-btn" @click="formatText('orderedList')" title="Lista Numerada">
                                                    <i class="fas fa-list-ol"></i>
                                                </button>
                                            </div>
                                            
                                            <div class="editor-wrapper">
                                                <div 
                                                    ref="editor"
                                                    class="text-editor"
                                                    contenteditable="true"
                                                    @input="updateManualText"
                                                    @paste="handlePaste"
                                                    placeholder="Digite o conteúdo da proposição aqui..."
                                                ></div>
                                                <div class="word-counter">
                                                    {{ wordCount }} palavras
                                                </div>
                                            </div>
                                            
                                            <div class="editor-hint">
                                                <i class="fas fa-lightbulb me-1"></i>
                                                Dica: Você poderá fazer ajustes finais no editor OnlyOffice após salvar
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Geração com IA -->
                                    <div v-else-if="formData.opcaoPreenchimento === 'ia'">
                                        <h2 class="section-title">
                                            <i class="fas fa-robot me-2"></i>
                                            Geração com Inteligência Artificial
                                        </h2>
                                        
                                        <div class="ia-section">
                                            <!-- Status de Geração -->
                                            <transition name="fade">
                                                <div v-if="iaGenerating" class="ia-generating">
                                                    <div class="generating-animation">
                                                        <div class="pulse-loader">
                                                            <div></div>
                                                            <div></div>
                                                            <div></div>
                                                        </div>
                                                    </div>
                                                    <h3>Gerando conteúdo com IA...</h3>
                                                    <p class="text-muted">Isso pode levar alguns segundos</p>
                                                    <div class="progress mt-3">
                                                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                                             :style="{ width: iaProgress + '%' }">
                                                        </div>
                                                    </div>
                                                </div>
                                            </transition>

                                            <!-- Resultado da IA -->
                                            <div v-if="!iaGenerating && formData.textoIA" class="ia-result">
                                                <div class="ia-result-header">
                                                    <h3>Conteúdo Gerado com Sucesso!</h3>
                                                    <div class="ia-actions">
                                                        <button @click="regenerarIA" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-redo me-1"></i>
                                                            Regenerar
                                                        </button>
                                                        <button @click="editarTextoIA" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit me-1"></i>
                                                            Editar
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <div class="ia-preview">
                                                    <div class="preview-content" v-html="formData.textoIA"></div>
                                                </div>
                                                
                                                <div class="ia-info">
                                                    <span class="info-item">
                                                        <i class="fas fa-file-alt me-1"></i>
                                                        {{ iaWordCount }} palavras
                                                    </span>
                                                    <span class="info-item">
                                                        <i class="fas fa-clock me-1"></i>
                                                        Gerado em {{ iaGenerationTime }}s
                                                    </span>
                                                    <span class="info-item">
                                                        <i class="fas fa-brain me-1"></i>
                                                        Modelo: GPT-4
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Botão para Gerar -->
                                            <div v-if="!iaGenerating && !formData.textoIA" class="ia-start">
                                                <div class="ia-start-icon">
                                                    <i class="fas fa-magic"></i>
                                                </div>
                                                <h3>Pronto para gerar o conteúdo!</h3>
                                                <p class="text-muted mb-4">
                                                    A IA criará um texto completo baseado na ementa que você forneceu
                                                </p>
                                                <button @click="gerarComIA" class="btn btn-lg btn-primary">
                                                    <i class="fas fa-robot me-2"></i>
                                                    Gerar Conteúdo com IA
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </transition>

                        <!-- Navigation Buttons -->
                        <div class="navigation-buttons">
                            <button 
                                v-if="currentStep > 0"
                                @click="previousStep"
                                class="btn btn-outline-secondary"
                            >
                                <i class="fas fa-arrow-left me-2"></i>
                                Voltar
                            </button>
                            
                            <div class="ms-auto d-flex gap-2">
                                <button 
                                    v-if="currentStep < steps.length - 1"
                                    @click="nextStep"
                                    class="btn btn-primary"
                                    :disabled="!canProceed"
                                >
                                    Próximo
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                                
                                <button 
                                    v-else
                                    @click="finalizarCriacao"
                                    class="btn btn-success"
                                    :disabled="!canFinish"
                                >
                                    <i class="fas fa-check me-2"></i>
                                    Finalizar e Editar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Toast Notifications Container -->
                    <div class="toast-container position-fixed top-0 end-0 p-3">
                        <transition-group name="toast">
                            <div 
                                v-for="toast in toasts" 
                                :key="toast.id"
                                class="toast show"
                                :class="'toast-' + toast.type"
                            >
                                <div class="toast-header">
                                    <i :class="toast.icon" class="me-2"></i>
                                    <strong class="me-auto">{{ toast.title }}</strong>
                                    <button type="button" class="btn-close" @click="removeToast(toast.id)">×</button>
                                </div>
                                <div class="toast-body">
                                    {{ toast.message }}
                                </div>
                            </div>
                        </transition-group>
                    </div>
                </div>
            `,
            setup() {
                // Estado reativo
                const currentStep = ref(0);
                const formData = ref({
                    tipo: '',
                    ementa: '',
                    opcaoPreenchimento: 'template',
                    textoManual: '',
                    textoIA: '',
                    templateId: null
                });

                // Estados de UI
                const iaGenerating = ref(false);
                const iaProgress = ref(0);
                const iaGenerationTime = ref(0);
                const ementaError = ref('');
                const toasts = ref([]);
                const proposicaoId = ref(null);

                // Dados
                const tiposProposicao = ref([
                    {
                        codigo: 'mocao',
                        nome: 'Moção',
                        icon: 'fas fa-hand-paper',
                        descricao: 'Manifestação de apoio, protesto ou pesar'
                    },
                    {
                        codigo: 'projeto_lei_ordinaria',
                        nome: 'Projeto de Lei Ordinária',
                        icon: 'fas fa-gavel',
                        descricao: 'Proposta de lei sobre matéria de competência municipal'
                    },
                    {
                        codigo: 'indicacao',
                        nome: 'Indicação',
                        icon: 'fas fa-lightbulb',
                        descricao: 'Sugestão ao Poder Executivo'
                    },
                    {
                        codigo: 'requerimento',
                        nome: 'Requerimento',
                        icon: 'fas fa-file-signature',
                        descricao: 'Solicitação de informações ou providências'
                    },
                    {
                        codigo: 'projeto_decreto_legislativo',
                        nome: 'Projeto de Decreto Legislativo',
                        icon: 'fas fa-stamp',
                        descricao: 'Matéria de competência exclusiva da Câmara'
                    },
                    {
                        codigo: 'projeto_resolucao',
                        nome: 'Projeto de Resolução',
                        icon: 'fas fa-scroll',
                        descricao: 'Matérias internas da Câmara'
                    }
                ]);

                const templateSelecionado = ref(null);
                const variaveisTemplate = ref([
                    '\${numero_proposicao}',
                    '\${ementa}',
                    '\${autor_nome}',
                    '\${municipio}',
                    '\${data}'
                ]);

                const steps = [
                    { title: 'Tipo', description: 'Escolha o tipo de proposição' },
                    { title: 'Ementa', description: 'Defina o objetivo e método' },
                    { title: 'Conteúdo', description: 'Crie o texto da proposição' }
                ];

                // Computed properties
                const canProceed = computed(() => {
                    switch (currentStep.value) {
                        case 0:
                            return !!formData.value.tipo;
                        case 1:
                            return formData.value.ementa.length >= 10 && !ementaError.value;
                        case 2:
                            return true;
                        default:
                            return false;
                    }
                });

                const canFinish = computed(() => {
                    if (formData.value.opcaoPreenchimento === 'manual') {
                        return formData.value.textoManual.length >= 50;
                    } else if (formData.value.opcaoPreenchimento === 'ia') {
                        return !!formData.value.textoIA;
                    }
                    return true; // Template sempre pode finalizar
                });

                const podeSalvarRascunho = computed(() => {
                    return formData.value.tipo && formData.value.ementa.length >= 10;
                });

                const wordCount = computed(() => {
                    if (!formData.value.textoManual) return 0;
                    return formData.value.textoManual.trim().split(/\\s+/).filter(word => word.length > 0).length;
                });

                const iaWordCount = computed(() => {
                    if (!formData.value.textoIA) return 0;
                    const text = formData.value.textoIA.replace(/<[^>]*>/g, '');
                    return text.trim().split(/\\s+/).filter(word => word.length > 0).length;
                });

                // Métodos
                const selecionarTipo = (tipo) => {
                    formData.value.tipo = tipo.codigo;
                    saveToLocalStorage();
                    carregarTemplate(tipo.codigo);
                };

                const selecionarOpcao = (opcao) => {
                    formData.value.opcaoPreenchimento = opcao;
                    saveToLocalStorage();
                };

                const validateEmenta = () => {
                    if (formData.value.ementa.length < 10) {
                        ementaError.value = 'A ementa deve ter pelo menos 10 caracteres';
                    } else if (formData.value.ementa.length > 1000) {
                        ementaError.value = 'A ementa não pode exceder 1000 caracteres';
                    } else {
                        ementaError.value = '';
                    }
                };

                const nextStep = () => {
                    if (canProceed.value) {
                        currentStep.value++;
                        saveToLocalStorage();
                    }
                };

                const previousStep = () => {
                    if (currentStep.value > 0) {
                        currentStep.value--;
                    }
                };

                const formatText = (command) => {
                    document.execCommand(command, false, null);
                };

                const updateManualText = (event) => {
                    formData.value.textoManual = event.target.innerText;
                    saveToLocalStorage();
                };

                const handlePaste = (event) => {
                    event.preventDefault();
                    const text = event.clipboardData.getData('text/plain');
                    document.execCommand('insertText', false, text);
                };

                const gerarComIA = async () => {
                    iaGenerating.value = true;
                    iaProgress.value = 0;
                    const startTime = Date.now();

                    // Simular progresso
                    const progressInterval = setInterval(() => {
                        if (iaProgress.value < 90) {
                            iaProgress.value += Math.random() * 15;
                        }
                    }, 500);

                    try {
                        const response = await axios.post('/proposicoes/gerar-texto-ia', {
                            tipo: formData.value.tipo,
                            ementa: formData.value.ementa
                        });

                        if (response.data.success) {
                            formData.value.textoIA = response.data.texto;
                            iaGenerationTime.value = ((Date.now() - startTime) / 1000).toFixed(1);
                            showToast('success', 'Sucesso!', 'Conteúdo gerado com IA');
                            saveToLocalStorage();
                        }
                    } catch (error) {
                        showToast('error', 'Erro', 'Falha ao gerar conteúdo com IA');
                        console.error(error);
                    } finally {
                        clearInterval(progressInterval);
                        iaProgress.value = 100;
                        setTimeout(() => {
                            iaGenerating.value = false;
                            iaProgress.value = 0;
                        }, 500);
                    }
                };

                const regenerarIA = () => {
                    formData.value.textoIA = '';
                    gerarComIA();
                };

                const editarTextoIA = () => {
                    formData.value.opcaoPreenchimento = 'manual';
                    formData.value.textoManual = formData.value.textoIA.replace(/<[^>]*>/g, '');
                    currentStep.value = 2;
                };

                const salvarRascunho = async () => {
                    if (!podeSalvarRascunho.value) return;

                    try {
                        const response = await axios.post('/proposicoes/salvar-rascunho', {
                            tipo: formData.value.tipo,
                            ementa: formData.value.ementa,
                            opcao_preenchimento: formData.value.opcaoPreenchimento,
                            texto_manual: formData.value.textoManual,
                            texto_ia: formData.value.textoIA
                        });

                        if (response.data.success) {
                            proposicaoId.value = response.data.proposicao_id;
                            showToast('success', 'Salvo!', 'Rascunho salvo com sucesso');
                        }
                    } catch (error) {
                        showToast('error', 'Erro', 'Falha ao salvar rascunho');
                        console.error(error);
                    }
                };

                const finalizarCriacao = async () => {
                    // Salvar primeiro se necessário
                    if (!proposicaoId.value) {
                        await salvarRascunho();
                    }

                    if (proposicaoId.value) {
                        // Limpar localStorage
                        localStorage.removeItem('proposicao_form_vue');
                        
                        // Redirecionar para o editor OnlyOffice
                        const params = new URLSearchParams({
                            tipo: formData.value.opcaoPreenchimento,
                            template_id: formData.value.templateId || ''
                        });
                        
                        window.location.href = \`/proposicoes/\${proposicaoId.value}/editar?\${params}\`;
                    }
                };

                const carregarTemplate = async (tipo) => {
                    try {
                        const response = await axios.get(\`/proposicoes/modelos/\${tipo}\`);
                        if (response.data && response.data.length > 0) {
                            templateSelecionado.value = response.data[0];
                            formData.value.templateId = response.data[0].id;
                        }
                    } catch (error) {
                        console.error('Erro ao carregar template:', error);
                    }
                };

                const showToast = (type, title, message) => {
                    const toast = {
                        id: Date.now(),
                        type,
                        title,
                        message,
                        icon: type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'
                    };
                    
                    toasts.value.push(toast);
                    
                    setTimeout(() => {
                        removeToast(toast.id);
                    }, 5000);
                };

                const removeToast = (id) => {
                    const index = toasts.value.findIndex(t => t.id === id);
                    if (index > -1) {
                        toasts.value.splice(index, 1);
                    }
                };

                const saveToLocalStorage = () => {
                    const data = {
                        currentStep: currentStep.value,
                        formData: formData.value,
                        timestamp: Date.now()
                    };
                    localStorage.setItem('proposicao_form_vue', JSON.stringify(data));
                };

                const loadFromLocalStorage = () => {
                    const saved = localStorage.getItem('proposicao_form_vue');
                    if (saved) {
                        try {
                            const data = JSON.parse(saved);
                            // Check if data is not older than 1 hour
                            if (Date.now() - data.timestamp < 3600000) {
                                currentStep.value = data.currentStep;
                                formData.value = data.formData;
                                
                                if (formData.value.tipo) {
                                    carregarTemplate(formData.value.tipo);
                                }
                            } else {
                                localStorage.removeItem('proposicao_form_vue');
                            }
                        } catch (error) {
                            console.error('Error loading saved data:', error);
                            localStorage.removeItem('proposicao_form_vue');
                        }
                    }
                };

                // Lifecycle
                onMounted(() => {
                    loadFromLocalStorage();
                });

                // Auto-save on changes
                watch(formData, () => {
                    saveToLocalStorage();
                }, { deep: true });

                return {
                    // State
                    currentStep,
                    formData,
                    iaGenerating,
                    iaProgress,
                    iaGenerationTime,
                    ementaError,
                    toasts,
                    proposicaoId,
                    
                    // Data
                    tiposProposicao,
                    templateSelecionado,
                    variaveisTemplate,
                    steps,
                    
                    // Computed
                    canProceed,
                    canFinish,
                    podeSalvarRascunho,
                    wordCount,
                    iaWordCount,
                    
                    // Methods
                    selecionarTipo,
                    selecionarOpcao,
                    validateEmenta,
                    nextStep,
                    previousStep,
                    formatText,
                    updateManualText,
                    handlePaste,
                    gerarComIA,
                    regenerarIA,
                    editarTextoIA,
                    salvarRascunho,
                    finalizarCriacao,
                    showToast,
                    removeToast
                };
            }
        };

        // Create Vue app
        const app = createApp({
            components: {
                ProposicaoCreate
            }
        });

        app.mount('#app');
    </script>

    <!-- Custom CSS -->
    <style>
        /* Container Principal */
        .proposicao-create-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
        }

        /* Header Section */
        .header-section {
            background: white;
            border-radius: 20px;
            padding: 2rem 3rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .title-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            margin-right: 1rem;
            color: white;
        }

        .page-subtitle {
            color: #718096;
            margin: 0.5rem 0 0 0;
        }

        /* Progress Steps */
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .step-item {
            flex: 1;
            display: flex;
            align-items: center;
            position: relative;
            opacity: 0.5;
            transition: all 0.3s ease;
        }

        .step-item.active,
        .step-item.completed {
            opacity: 1;
        }

        .step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 25px;
            left: calc(50% + 30px);
            right: calc(-50% + 30px);
            height: 2px;
            background: #e2e8f0;
            z-index: -1;
        }

        .step-item.completed:not(:last-child)::after {
            background: linear-gradient(90deg, #48bb78 0%, #667eea 100%);
        }

        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #718096;
            margin-right: 1rem;
            transition: all 0.3s ease;
        }

        .step-item.active .step-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: scale(1.1);
        }

        .step-item.completed .step-number {
            background: #48bb78;
            color: white;
        }

        .step-info {
            flex: 1;
        }

        .step-title {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }

        .step-description {
            font-size: 0.875rem;
            color: #718096;
        }

        /* Main Content */
        .main-content {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            min-height: 500px;
        }

        .section-card {
            animation: fadeInUp 0.5s ease;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
        }

        /* Tipo Grid */
        .tipo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .tipo-card {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .tipo-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
        }

        .tipo-card.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        }

        .tipo-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .tipo-name {
            font-size: 1.125rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .tipo-description {
            color: #718096;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .tipo-check {
            position: absolute;
            top: 1rem;
            right: 1rem;
            color: #48bb78;
            font-size: 1.5rem;
        }

        /* Ementa Section */
        .ementa-section {
            margin-top: 2rem;
        }

        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
            display: block;
        }

        .required {
            color: #f56565;
        }

        .textarea-wrapper {
            position: relative;
        }

        .ementa-input {
            width: 100%;
            min-height: 120px;
            padding: 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            resize: vertical;
            transition: all 0.3s ease;
        }

        .ementa-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .ementa-input.is-invalid {
            border-color: #f56565;
        }

        .char-counter {
            position: absolute;
            bottom: 0.5rem;
            right: 1rem;
            font-size: 0.75rem;
            color: #718096;
        }

        .invalid-feedback {
            color: #f56565;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .form-hint {
            font-size: 0.875rem;
            color: #718096;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
        }

        /* Opções Grid */
        .opcoes-section {
            margin-top: 3rem;
        }

        .subsection-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 1.5rem;
        }

        .opcoes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .opcao-card {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .opcao-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
        }

        .opcao-card.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.2);
        }

        .template-option {
            border-color: #48bb78;
        }

        .template-option:hover {
            border-color: #48bb78;
            box-shadow: 0 15px 40px rgba(72, 187, 120, 0.2);
        }

        .template-option.selected {
            border-color: #48bb78;
            background: linear-gradient(135deg, rgba(72, 187, 120, 0.08) 0%, rgba(56, 161, 105, 0.08) 100%);
        }

        .opcao-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .opcao-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
        }

        .template-option .opcao-icon {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        }

        .opcao-badge {
            background: #48bb78;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .ia-badge {
            background: linear-gradient(135deg, #f687b3 0%, #d53f8c 100%);
        }

        .opcao-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.75rem;
        }

        .opcao-description {
            color: #718096;
            font-size: 0.875rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .opcao-features {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .feature-tag {
            background: #edf2f7;
            color: #4a5568;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
        }

        /* Editor Section */
        .editor-section {
            margin-top: 2rem;
        }

        .editor-toolbar {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem;
            background: #f7fafc;
            border: 2px solid #e2e8f0;
            border-bottom: none;
            border-radius: 12px 12px 0 0;
        }

        .toolbar-btn {
            width: 40px;
            height: 40px;
            border: none;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4a5568;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .toolbar-btn:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .toolbar-separator {
            width: 1px;
            height: 30px;
            background: #e2e8f0;
            margin: 0 0.5rem;
        }

        .editor-wrapper {
            position: relative;
        }

        .text-editor {
            min-height: 400px;
            padding: 1.5rem;
            border: 2px solid #e2e8f0;
            border-top: none;
            border-radius: 0 0 12px 12px;
            font-size: 1rem;
            line-height: 1.6;
            background: white;
        }

        .text-editor:focus {
            outline: none;
            border-color: #667eea;
        }

        .text-editor[contenteditable]:empty:before {
            content: attr(placeholder);
            color: #a0aec0;
        }

        .word-counter {
            position: absolute;
            bottom: 1rem;
            right: 1rem;
            background: #edf2f7;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            color: #4a5568;
        }

        .editor-hint {
            margin-top: 1rem;
            padding: 1rem;
            background: #f7fafc;
            border-radius: 8px;
            font-size: 0.875rem;
            color: #4a5568;
            display: flex;
            align-items: center;
        }

        /* IA Section */
        .ia-section {
            margin-top: 2rem;
        }

        .ia-generating {
            text-align: center;
            padding: 3rem;
        }

        .generating-animation {
            margin-bottom: 2rem;
        }

        .pulse-loader {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }

        .pulse-loader div {
            width: 15px;
            height: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            animation: pulse 1.5s ease-in-out infinite;
        }

        .pulse-loader div:nth-child(2) {
            animation-delay: 0.2s;
        }

        .pulse-loader div:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.5);
                opacity: 0.5;
            }
        }

        .progress {
            height: 8px;
            background: #edf2f7;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 0.5s ease;
        }

        .ia-result {
            padding: 2rem;
            background: #f7fafc;
            border-radius: 12px;
        }

        .ia-result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .ia-result-header h3 {
            margin: 0;
            color: #2d3748;
        }

        .ia-actions {
            display: flex;
            gap: 0.5rem;
        }

        .ia-preview {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.5rem;
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 1rem;
        }

        .preview-content {
            line-height: 1.6;
            color: #2d3748;
        }

        .ia-info {
            display: flex;
            gap: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
        }

        .info-item {
            display: flex;
            align-items: center;
            color: #718096;
            font-size: 0.875rem;
        }

        .ia-start {
            text-align: center;
            padding: 3rem;
        }

        .ia-start-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin: 0 auto 2rem;
        }

        /* Template Preview */
        .template-preview {
            background: #f7fafc;
            border-radius: 12px;
            padding: 2rem;
            margin-top: 2rem;
        }

        .template-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .template-header h3 {
            margin: 0;
            color: #2d3748;
        }

        .template-badge {
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
        }
        .template-badge.badge-universal {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }
        .template-badge.badge-specific {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        }

        .template-info {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
        }

        .template-variables h4 {
            font-size: 1rem;
            color: #2d3748;
            margin-bottom: 1rem;
        }

        .variables-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .variable-tag {
            background: #edf2f7;
            color: #4a5568;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
        }

        /* Navigation Buttons */
        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 2px solid #e2e8f0;
        }

        /* Buttons */
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
        }

        .btn-success:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(72, 187, 120, 0.3);
        }

        .btn-outline-secondary {
            background: white;
            color: #4a5568;
            border: 2px solid #e2e8f0;
        }

        .btn-outline-secondary:hover {
            background: #f7fafc;
            transform: translateY(-2px);
        }

        .btn-outline-primary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-outline-primary:hover {
            background: rgba(102, 126, 234, 0.05);
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.125rem;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        /* Toast Notifications */
        .toast-container {
            z-index: 9999;
        }

        .toast {
            min-width: 300px;
            margin-bottom: 1rem;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            background: white;
        }

        .toast-success {
            border-left: 4px solid #48bb78;
        }

        .toast-error {
            border-left: 4px solid #f56565;
        }

        .toast-header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .toast-body {
            background: white;
            padding: 1rem;
            color: #4a5568;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-slide-enter-active,
        .fade-slide-leave-active {
            transition: all 0.5s ease;
        }

        .fade-slide-enter-from {
            opacity: 0;
            transform: translateX(-20px);
        }

        .fade-slide-leave-to {
            opacity: 0;
            transform: translateX(20px);
        }

        .slide-fade-enter-active,
        .slide-fade-leave-active {
            transition: all 0.3s ease;
        }

        .slide-fade-enter-from,
        .slide-fade-leave-to {
            opacity: 0;
            transform: translateY(10px);
        }

        .scale-enter-active,
        .scale-leave-active {
            transition: all 0.3s ease;
        }

        .scale-enter-from,
        .scale-leave-to {
            transform: scale(0);
        }

        .fade-enter-active,
        .fade-leave-active {
            transition: opacity 0.3s ease;
        }

        .fade-enter-from,
        .fade-leave-to {
            opacity: 0;
        }

        .toast-enter-active,
        .toast-leave-active {
            transition: all 0.3s ease;
        }

        .toast-enter-from {
            transform: translateX(100%);
            opacity: 0;
        }

        .toast-leave-to {
            transform: translateX(100%);
            opacity: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .proposicao-create-container {
                padding: 1rem;
            }

            .header-section {
                flex-direction: column;
                align-items: flex-start;
                padding: 1.5rem;
            }

            .header-actions {
                margin-top: 1rem;
            }

            .progress-steps {
                flex-direction: column;
                gap: 1rem;
            }

            .step-item::after {
                display: none;
            }

            .main-content {
                padding: 1.5rem;
            }

            .tipo-grid,
            .opcoes-grid {
                grid-template-columns: 1fr;
            }

            .navigation-buttons {
                flex-direction: column;
                gap: 1rem;
            }

            .navigation-buttons .ms-auto {
                margin-left: 0 !important;
                width: 100%;
            }

            .navigation-buttons .btn {
                width: 100%;
            }
        }
    </style>
</body>
</html>