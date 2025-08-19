@extends('components.layouts.app')

@section('title', 'Criar Nova Proposição')

@push('styles')
<style>
    [v-cloak] { display: none; }
    
    /* Vue.js specific styles */
    .opcao-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #e1e5e9;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .opcao-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,123,255,0.15);
        border-color: #007bff;
    }

    .opcao-card.selected {
        border-color: #007bff;
        background: linear-gradient(145deg, rgba(0,123,255,0.05) 0%, rgba(0,123,255,0.02) 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,123,255,0.2);
    }
    
    .tipo-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #e1e5e9;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .tipo-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,123,255,0.15);
        border-color: #007bff;
    }

    .tipo-card.selected {
        border-color: #007bff;
        background: linear-gradient(145deg, rgba(0,123,255,0.05) 0%, rgba(0,123,255,0.02) 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,123,255,0.2);
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
</style>
@endpush

@section('content')
<div id="vue-app" v-cloak class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-5">
        <div>
            <h1 class="h3 text-gray-800 mb-0">Criar Nova Proposição</h1>
            <p class="text-muted">Interface moderna com Vue.js - Etapa @{{ currentStep + 1 }} de @{{ steps.length }}</p>
        </div>
        <div>
            <a href="{{ route('proposicoes.minhas-proposicoes') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Minhas Proposições
            </a>
        </div>
    </div>

    <!-- Stepper -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="stepper stepper-pills stepper-column d-flex flex-stack flex-wrap">
                        <div 
                            v-for="(step, index) in steps" 
                            :key="index"
                            class="stepper-item"
                            :class="{
                                'current': currentStep === index,
                                'completed': currentStep > index
                            }"
                            data-kt-stepper-element="nav"
                        >
                            <div class="stepper-wrapper d-flex align-items-center">
                                <div class="stepper-icon w-40px h-40px">
                                    <transition name="scale">
                                        <i v-if="currentStep > index" class="stepper-check fas fa-check"></i>
                                        <span v-else class="stepper-number">@{{ index + 1 }}</span>
                                    </transition>
                                </div>
                                <div class="stepper-label">
                                    <h3 class="stepper-title">@{{ step.title }}</h3>
                                    <div class="stepper-desc">@{{ step.description }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário -->
    <div class="row">
        <div class="col-lg-8 col-xl-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt text-primary me-2"></i>
                        @{{ currentStepTitle }}
                    </h5>
                </div>
                <div class="card-body">
                    <transition name="slide-fade" mode="out-in">
                        <!-- Step 1: Tipo de Proposição -->
                        <div v-if="currentStep === 0" key="step1">
                            <div class="mb-4">
                                <label class="form-label required">Tipo de Proposição</label>
                                <p class="text-muted mb-3">Selecione o tipo que melhor se adequa à sua proposição:</p>
                                
                                <!-- Lista de tipos -->
                                <div class="list-group">
                                    <div 
                                        v-for="tipo in tiposProposicao" 
                                        :key="tipo.codigo"
                                        class="list-group-item list-group-item-action tipo-list-item"
                                        :class="{ 'active': formData.tipo === tipo.codigo }"
                                        @click="selecionarTipo(tipo)"
                                        style="cursor: pointer; transition: all 0.3s ease;"
                                    >
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i :class="tipo.icon" class="fa-lg"
                                                   :style="formData.tipo === tipo.codigo ? 'color: white' : 'color: #007bff'"></i>
                                            </div>
                                            <div class="flex-fill">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1 fw-bold">@{{ tipo.nome }}</h6>
                                                        <p class="mb-1 small" 
                                                           :style="formData.tipo === tipo.codigo ? 'color: rgba(255,255,255,0.8)' : 'color: #6c757d'">
                                                            @{{ tipo.descricao }}
                                                        </p>
                                                        <small class="text-muted" 
                                                               :style="formData.tipo === tipo.codigo ? 'color: rgba(255,255,255,0.6) !important' : ''">
                                                            Código: @{{ tipo.codigo }} @{{ tipo.sigla ? '(' + tipo.sigla + ')' : '' }}
                                                        </small>
                                                    </div>
                                                    <div class="form-check">
                                                        <input 
                                                            class="form-check-input" 
                                                            type="radio" 
                                                            :checked="formData.tipo === tipo.codigo"
                                                            :value="tipo.codigo"
                                                            readonly
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Informação adicional -->
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Total de @{{ tiposProposicao.length }} tipos de proposição disponíveis
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Ementa e Opções -->
                        <div v-else-if="currentStep === 1" key="step2">
                            <!-- Ementa -->
                            <div class="mb-4">
                                <label for="ementa" class="form-label required">Ementa da Proposição</label>
                                <textarea 
                                    v-model="formData.ementa"
                                    class="form-control" 
                                    :class="{ 'is-invalid': ementaError }"
                                    rows="3" 
                                    placeholder="Descreva resumidamente o objetivo da proposição..."
                                    @input="validateEmenta"
                                    maxlength="1000"
                                ></textarea>
                                <div class="form-text">
                                    @{{ formData.ementa.length }}/1000 caracteres - Descreva de forma clara e objetiva o que a proposição pretende regulamentar.
                                </div>
                                <div v-if="ementaError" class="invalid-feedback d-block">
                                    @{{ ementaError }}
                                </div>
                            </div>

                            <!-- Opções de Preenchimento -->
                            <div class="mb-4">
                                <label class="form-label required">Como deseja criar o texto da proposição?</label>
                                <div class="row g-3 justify-content-center">
                                    <div class="col-md-5">
                                        <div 
                                            class="card h-100 opcao-card"
                                            :class="{ 'selected': formData.opcaoPreenchimento === 'manual' }"
                                            @click="selecionarOpcao('manual')"
                                        >
                                            <div class="card-body text-center">
                                                <i class="fas fa-edit fa-2x text-success mb-3"></i>
                                                <h6 class="card-title">Texto Personalizado</h6>
                                                <p class="card-text small">Escreva o texto principal e use modelo para formatação</p>
                                                <div class="form-check">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="radio" 
                                                        :checked="formData.opcaoPreenchimento === 'manual'"
                                                        value="manual"
                                                        readonly
                                                    >
                                                    <label class="form-check-label">
                                                        Texto Personalizado
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div 
                                            class="card h-100 opcao-card"
                                            :class="{ 'selected': formData.opcaoPreenchimento === 'ia' }"
                                            @click="selecionarOpcao('ia')"
                                        >
                                            <div class="card-body text-center">
                                                <i class="fas fa-robot fa-2x text-info mb-3"></i>
                                                <h6 class="card-title">Texto com IA</h6>
                                                <p class="card-text small">IA gera o texto e aplica no modelo para formatação</p>
                                                <div class="form-check">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="radio" 
                                                        :checked="formData.opcaoPreenchimento === 'ia'"
                                                        value="ia"
                                                        readonly
                                                    >
                                                    <label class="form-check-label">
                                                        Texto com IA
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Conteúdo -->
                        <div v-else-if="currentStep === 2" key="step3">
                            <!-- Texto Manual -->
                            <div v-if="formData.opcaoPreenchimento === 'manual'" class="mb-4">
                                <label for="texto_principal" class="form-label required">Texto Principal da Proposição</label>
                                <textarea 
                                    v-model="formData.textoManual"
                                    class="form-control" 
                                    rows="10" 
                                    placeholder="Digite aqui o texto principal da sua proposição..."
                                ></textarea>
                                <div class="form-text">
                                    Escreva o conteúdo completo da proposição. Use linguagem técnica e formal apropriada.
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <strong>Dica:</strong> Você pode formatar o texto na etapa seguinte usando o editor avançado.
                                    </small>
                                </div>
                            </div>

                            <!-- Geração via IA -->
                            <div v-else-if="formData.opcaoPreenchimento === 'ia'" class="mb-4">
                                <div class="card border-info">
                                    <div class="card-header bg-light-info">
                                        <h6 class="card-title mb-0">
                                            <i class="fas fa-robot text-info me-2"></i>
                                            Geração Automática via IA
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted mb-3">
                                            Use inteligência artificial para gerar automaticamente o texto da proposição baseado na ementa fornecida.
                                        </p>
                                        <div v-if="!formData.textoIA" class="d-flex gap-2 align-items-center">
                                            <button type="button" class="btn btn-info" @click="gerarComIA">
                                                <i class="fas fa-magic me-2"></i>Gerar Texto via IA
                                            </button>
                                        </div>
                                        <div v-else class="alert alert-success">
                                            <h6><i class="fas fa-check me-2"></i>Texto gerado com sucesso!</h6>
                                            <p class="mb-2"><strong>Preview:</strong></p>
                                            <div class="bg-light p-2 rounded small">@{{ textoIAPreview }}</div>
                                            <small class="text-muted mt-2 d-block">
                                                Texto completo será usado na próxima etapa (@{{ formData.textoIA.length }} caracteres)
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>

                    <!-- Botões -->
                    <div class="d-flex justify-content-between">
                        <button 
                            v-if="currentStep > 0"
                            @click="previousStep"
                            type="button" 
                            class="btn btn-secondary"
                        >
                            <i class="fas fa-arrow-left me-2"></i>Voltar
                        </button>
                        
                        <div class="ms-auto">
                            <button 
                                v-if="currentStep < steps.length - 1"
                                @click="nextStep"
                                type="button" 
                                class="btn btn-primary"
                                :disabled="!canProceed"
                            >
                                Próximo
                                <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                            
                            <button 
                                v-else
                                @click="finalizarCriacao"
                                type="button" 
                                class="btn btn-success"
                                :disabled="!canFinish"
                            >
                                <i class="fas fa-check me-2"></i>
                                Finalizar e Editar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar de Ajuda -->
        <div class="col-lg-4 col-xl-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Ajuda - Etapa @{{ currentStep + 1 }}
                    </h6>
                </div>
                <div class="card-body">
                    <div v-if="currentStep === 0">
                        <div class="mb-3">
                            <h6 class="fw-bold">Tipos Disponíveis:</h6>
                            <div class="small" style="max-height: 300px; overflow-y: auto;">
                                <div v-for="tipo in tiposProposicao" :key="tipo.codigo" class="mb-2">
                                    <strong>@{{ tipo.sigla || tipo.codigo.toUpperCase() }}:</strong> @{{ tipo.nome }}
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <h6 class="fw-bold">Como escolher:</h6>
                            <ul class="list-unstyled small">
                                <li>• Leia a descrição de cada tipo</li>
                                <li>• Escolha baseado no objetivo</li>
                                <li>• Em dúvida, consulte a assessoria</li>
                                <li>• O tipo define o trâmite</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div v-else-if="currentStep === 1">
                        <div class="mb-3">
                            <h6 class="fw-bold">Dicas para a Ementa:</h6>
                            <ul class="list-unstyled small">
                                <li>• Seja claro e objetivo</li>
                                <li>• Use linguagem formal</li>
                                <li>• Evite termos técnicos desnecessários</li>
                                <li>• Indique o objetivo principal</li>
                            </ul>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="fw-bold">Opções de Texto:</h6>
                            <ul class="list-unstyled small">
                                <li><strong>Personalizado:</strong> Escreva seu próprio texto</li>
                                <li><strong>IA:</strong> Texto gerado automaticamente</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div v-else>
                        <div class="mb-3">
                            <h6 class="fw-bold">Criando o Conteúdo:</h6>
                            <ul class="list-unstyled small">
                                <li>• Use linguagem técnica adequada</li>
                                <li>• Seja objetivo e direto</li>
                                <li>• Revise o texto cuidadosamente</li>
                                <li>• Pense na aplicabilidade prática</li>
                            </ul>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-lightbulb me-1"></i>
                            <strong>Vue.js:</strong> Interface moderna e responsiva com auto-save inteligente.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>

// Configure axios
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
}

const { createApp, ref, computed, onMounted, watch } = Vue;

const app = createApp({
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
        const ementaError = ref('');
        const proposicaoId = ref(null);

        // Dados
        const tiposProposicao = ref(@json($tipos));

        const steps = [
            { title: 'Dados Básicos', description: 'Escolha o tipo de proposição' },
            { title: 'Ementa e Método', description: 'Defina objetivo e como criar' },
            { title: 'Conteúdo', description: 'Crie o texto da proposição' }
        ];

        // Computed properties
        const canProceed = computed(() => {
            switch (currentStep.value) {
                case 0:
                    return !!formData.value.tipo;
                case 1:
                    return formData.value.ementa.length >= 10 && !ementaError.value && !!formData.value.opcaoPreenchimento;
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
        
        const currentStepTitle = computed(() => {
            switch (currentStep.value) {
                case 0: return 'Selecione o Tipo de Proposição';
                case 1: return 'Ementa e Método de Preenchimento';
                case 2: return 'Criação do Conteúdo';
                default: return 'Informações da Proposição';
            }
        });
        
        const textoIAPreview = computed(() => {
            if (!formData.value.textoIA) return '';
            const maxLength = 200;
            return formData.value.textoIA.length > maxLength 
                ? formData.value.textoIA.substring(0, maxLength) + '...' 
                : formData.value.textoIA;
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

        const gerarComIA = async () => {
            try {
                const response = await axios.post('/proposicoes/gerar-texto-ia', {
                    tipo: formData.value.tipo,
                    ementa: formData.value.ementa
                });

                if (response.data.success) {
                    formData.value.textoIA = response.data.texto;
                    saveToLocalStorage();
                    // Mostrar notificação de sucesso aqui se necessário
                }
            } catch (error) {
                console.error('Erro ao gerar texto IA:', error);
                // Mostrar notificação de erro aqui se necessário
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
                
                window.location.href = `/proposicoes/${proposicaoId.value}/editar?${params}`;
            }
        };

        const salvarRascunho = async () => {
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
                }
            } catch (error) {
                console.error('Erro ao salvar rascunho:', error);
            }
        };

        const carregarTemplate = async (tipo) => {
            try {
                const response = await axios.get(`/proposicoes/modelos/${tipo}`);
                if (response.data && response.data.length > 0) {
                    formData.value.templateId = response.data[0].id;
                }
            } catch (error) {
                console.error('Erro ao carregar template:', error);
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
            ementaError,
            proposicaoId,
            
            // Data
            tiposProposicao,
            steps,
            
            // Computed
            canProceed,
            canFinish,
            currentStepTitle,
            textoIAPreview,
            
            // Methods
            selecionarTipo,
            selecionarOpcao,
            validateEmenta,
            nextStep,
            previousStep,
            gerarComIA,
            finalizarCriacao
        };
    }
});

app.mount('#vue-app');
</script>
@endpush