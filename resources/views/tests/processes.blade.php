@extends('components.layouts.app')

@section('title', 'Testes de Processos - Sistema Parlamentar')

@push('styles')
<style>
    .workflow-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        padding: 30px;
        position: relative;
        overflow: hidden;
    }
    
    .workflow-step {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        position: relative;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .workflow-step.active {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border: 2px solid #007bff;
    }
    
    .workflow-step.completed {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border: 2px solid #28a745;
    }
    
    .workflow-step.pending {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border: 2px solid #ffc107;
    }
    
    .workflow-arrow {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 60px;
        position: relative;
    }
    
    .workflow-arrow::after {
        content: '';
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, #6c757d 0%, #007bff 50%, #6c757d 100%);
        position: relative;
        border-radius: 2px;
    }
    
    .workflow-arrow::before {
        content: '→';
        font-size: 24px;
        color: #007bff;
        background: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        z-index: 2;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    .document-icon {
        position: absolute;
        top: 50%;
        left: 0;
        transform: translateY(-50%);
        background: #007bff;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: moveDocument 8s linear infinite;
        z-index: 3;
        box-shadow: 0 2px 10px rgba(0,123,255,0.5);
    }
    
    @keyframes moveDocument {
        0% { left: 0; }
        25% { left: calc(25% - 15px); }
        50% { left: calc(50% - 15px); }
        75% { left: calc(75% - 15px); }
        100% { left: calc(100% - 30px); }
    }
    
    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
        margin-bottom: 10px;
    }
    
    .workflow-controls {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .step-indicator {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .step-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #6c757d;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    
    .step-number.active {
        background: #007bff;
        animation: pulse 1.5s infinite;
    }
    
    .step-number.completed {
        background: #28a745;
    }
</style>
@endpush

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Visualização de Processos de Tramitação
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('tests.index') }}" class="text-muted text-hover-primary">Testes</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Processos</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('tests.index') }}" class="btn btn-sm btn-light">
                    <i class="ki-duotone ki-arrows-left fs-4 me-1"></i>
                    Voltar aos Testes
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Workflow Controls-->
            <div class="row g-5 g-xl-8 mb-8">
                <div class="col-xl-12">
                    <div class="workflow-controls">
                        <div class="d-flex justify-content-between align-items-center mb-6">
                            <h3 class="fw-bold text-dark fs-4 mb-0">Controles de Simulação</h3>
                            <div class="d-flex gap-3">
                                <button id="startWorkflow" class="btn btn-success btn-sm">
                                    <i class="ki-duotone ki-play fs-4 me-1"></i>
                                    Iniciar Simulação
                                </button>
                                <button id="pauseWorkflow" class="btn btn-warning btn-sm" disabled>
                                    <i class="ki-duotone ki-pause fs-4 me-1"></i>
                                    Pausar
                                </button>
                                <button id="resetWorkflow" class="btn btn-secondary btn-sm">
                                    <i class="ki-duotone ki-arrows-loop fs-4 me-1"></i>
                                    Reiniciar
                                </button>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tipo de Documento:</label>
                                <select id="documentType" class="form-select form-select-sm">
                                    <option value="projeto_lei">Projeto de Lei</option>
                                    <option value="indicacao">Indicação</option>
                                    <option value="requerimento">Requerimento</option>
                                    <option value="mocao">Moção</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Velocidade da Animação:</label>
                                <select id="animationSpeed" class="form-select form-select-sm">
                                    <option value="4000">Lenta (4s)</option>
                                    <option value="2000" selected>Normal (2s)</option>
                                    <option value="1000">Rápida (1s)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status Atual:</label>
                                <div id="currentStatus" class="badge badge-light-primary fs-7 p-2">
                                    Aguardando início da simulação
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Workflow Controls-->
            
            <!--begin::Workflow Visualization-->
            <div class="row g-5 g-xl-8 mb-8">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title fw-bold">Fluxo de Tramitação de Documentos</h3>
                        </div>
                        <div class="card-body">
                            <div class="workflow-container">
                                
                                <!--begin::Workflow Row 1-->
                                <div class="row g-4 mb-4">
                                    <!--begin::Step 1 - Parlamentar-->
                                    <div class="col-md-3">
                                        <div class="workflow-step" id="step-1">
                                            <div class="step-indicator">
                                                <span class="step-number" id="step-number-1">1</span>
                                                <span class="fw-bold">Criação</span>
                                            </div>
                                            <div class="user-avatar bg-primary">
                                                <i class="ki-duotone ki-user fs-4"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark mb-2">Parlamentar</h5>
                                            <p class="text-muted fs-7 mb-0">Cria a proposição usando templates</p>
                                            <div class="mt-3">
                                                <span class="badge badge-light-primary fs-8">Rascunho</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Step 1-->
                                    
                                    <!--begin::Arrow 1-->
                                    <div class="col-md-1">
                                        <div class="workflow-arrow" id="arrow-1">
                                            <div class="document-icon" id="document-1" style="display: none;">
                                                <i class="ki-duotone ki-document fs-6"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Arrow 1-->
                                    
                                    <!--begin::Step 2 - Assinatura-->
                                    <div class="col-md-3">
                                        <div class="workflow-step" id="step-2">
                                            <div class="step-indicator">
                                                <span class="step-number" id="step-number-2">2</span>
                                                <span class="fw-bold">Assinatura</span>
                                            </div>
                                            <div class="user-avatar bg-info">
                                                <i class="ki-duotone ki-pencil fs-4"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark mb-2">Co-autores</h5>
                                            <p class="text-muted fs-7 mb-0">Parlamentares assinam a proposição</p>
                                            <div class="mt-3">
                                                <span class="badge badge-light-info fs-8">Em assinatura</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Step 2-->
                                    
                                    <!--begin::Arrow 2-->
                                    <div class="col-md-1">
                                        <div class="workflow-arrow" id="arrow-2">
                                            <div class="document-icon" id="document-2" style="display: none;">
                                                <i class="ki-duotone ki-document fs-6"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Arrow 2-->
                                    
                                    <!--begin::Step 3 - Protocolo-->
                                    <div class="col-md-3">
                                        <div class="workflow-step" id="step-3">
                                            <div class="step-indicator">
                                                <span class="step-number" id="step-number-3">3</span>
                                                <span class="fw-bold">Protocolo</span>
                                            </div>
                                            <div class="user-avatar bg-warning">
                                                <i class="ki-duotone ki-file-up fs-4"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark mb-2">Protocolo</h5>
                                            <p class="text-muted fs-7 mb-0">Registra e numera a proposição</p>
                                            <div class="mt-3">
                                                <span class="badge badge-light-warning fs-8">Aguardando protocolo</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Step 3-->
                                </div>
                                <!--end::Workflow Row 1-->
                                
                                <!--begin::Workflow Row 2-->
                                <div class="row g-4 mb-4">
                                    <!--begin::Step 4 - Legislativo-->
                                    <div class="col-md-3">
                                        <div class="workflow-step" id="step-4">
                                            <div class="step-indicator">
                                                <span class="step-number" id="step-number-4">4</span>
                                                <span class="fw-bold">Análise</span>
                                            </div>
                                            <div class="user-avatar bg-success">
                                                <i class="ki-duotone ki-notepad-edit fs-4"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark mb-2">Legislativo</h5>
                                            <p class="text-muted fs-7 mb-0">Análise técnica e correções</p>
                                            <div class="mt-3">
                                                <span class="badge badge-light-success fs-8">Em análise</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Step 4-->
                                    
                                    <!--begin::Arrow 3-->
                                    <div class="col-md-1">
                                        <div class="workflow-arrow" id="arrow-3">
                                            <div class="document-icon" id="document-3" style="display: none;">
                                                <i class="ki-duotone ki-document fs-6"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Arrow 3-->
                                    
                                    <!--begin::Step 5 - Jurídico-->
                                    <div class="col-md-3">
                                        <div class="workflow-step" id="step-5">
                                            <div class="step-indicator">
                                                <span class="step-number" id="step-number-5">5</span>
                                                <span class="fw-bold">Jurídico</span>
                                            </div>
                                            <div class="user-avatar bg-danger">
                                                <i class="ki-duotone ki-law fs-4"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark mb-2">Assessoria Jurídica</h5>
                                            <p class="text-muted fs-7 mb-0">Parecer jurídico e legalidade</p>
                                            <div class="mt-3">
                                                <span class="badge badge-light-danger fs-8">Parecer jurídico</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Step 5-->
                                    
                                    <!--begin::Arrow 4-->
                                    <div class="col-md-1">
                                        <div class="workflow-arrow" id="arrow-4">
                                            <div class="document-icon" id="document-4" style="display: none;">
                                                <i class="ki-duotone ki-document fs-6"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Arrow 4-->
                                    
                                    <!--begin::Step 6 - Expediente-->
                                    <div class="col-md-3">
                                        <div class="workflow-step" id="step-6">
                                            <div class="step-indicator">
                                                <span class="step-number" id="step-number-6">6</span>
                                                <span class="fw-bold">Expediente</span>
                                            </div>
                                            <div class="user-avatar bg-dark">
                                                <i class="ki-duotone ki-calendar fs-4"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark mb-2">Expediente</h5>
                                            <p class="text-muted fs-7 mb-0">Inclusão na pauta de sessão</p>
                                            <div class="mt-3">
                                                <span class="badge badge-light-dark fs-8">Pautado</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Step 6-->
                                </div>
                                <!--end::Workflow Row 2-->
                                
                                <!--begin::Final Step-->
                                <div class="row g-4">
                                    <div class="col-md-4 offset-md-4">
                                        <div class="workflow-step" id="step-7">
                                            <div class="step-indicator">
                                                <span class="step-number" id="step-number-7">7</span>
                                                <span class="fw-bold">Finalização</span>
                                            </div>
                                            <div class="user-avatar bg-primary">
                                                <i class="ki-duotone ki-check-circle fs-4"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark mb-2">Sessão Plenária</h5>
                                            <p class="text-muted fs-7 mb-0">Discussão e votação</p>
                                            <div class="mt-3">
                                                <span class="badge badge-light-primary fs-8">Em sessão</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Final Step-->
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Workflow Visualization-->
            
            <!--begin::Interactive Process Test-->
            <div class="row g-5 g-xl-8 mb-8">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title fw-bold">Teste Interativo de Tramitação</h3>
                            <div class="card-toolbar">
                                <button id="resetProcessTest" class="btn btn-sm btn-light-danger">
                                    <i class="ki-duotone ki-arrows-loop fs-4 me-1"></i>
                                    Reiniciar Teste
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-5">
                                <!-- Formulário de Criação -->
                                <div class="col-lg-4">
                                    <div class="bg-light rounded p-4">
                                        <h4 class="fw-bold mb-4">1. Criar Proposição</h4>
                                        <form id="createProposicaoForm">
                                            <div class="mb-3">
                                                <label class="form-label">Tipo de Proposição:</label>
                                                <select id="tipoProposicaoSelect" class="form-select form-select-sm" required>
                                                    <option value="">Selecione...</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Template:</label>
                                                <select id="templateSelect" class="form-select form-select-sm" required>
                                                    <option value="">Selecione o tipo primeiro</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Ementa:</label>
                                                <textarea id="ementaInput" class="form-control form-control-sm" rows="3" required placeholder="Descreva o objetivo da proposição..."></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Conteúdo Principal:</label>
                                                <textarea id="conteudoInput" class="form-control form-control-sm" rows="5" required placeholder="CONSIDERANDO...
ARTIGO 1º - ..."></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                                <i class="ki-duotone ki-plus fs-4 me-1"></i>
                                                Criar Proposição
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Controles de Tramitação -->
                                <div class="col-lg-4">
                                    <div class="bg-light rounded p-4">
                                        <h4 class="fw-bold mb-4">2. Controles de Tramitação</h4>
                                        <div id="tramitationControls">
                                            <div class="alert alert-info">
                                                <i class="ki-duotone ki-information fs-2 me-2"></i>
                                                Crie uma proposição primeiro para habilitar os controles
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Log de Ações -->
                                <div class="col-lg-4">
                                    <div class="bg-light rounded p-4">
                                        <h4 class="fw-bold mb-4">3. Log de Tramitação</h4>
                                        <div id="tramitationLog" style="max-height: 500px; overflow-y: auto;">
                                            <div class="text-muted">Aguardando ações...</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Status da Proposição -->
                            <div class="row mt-5">
                                <div class="col-12">
                                    <div id="proposicaoStatus" class="alert alert-light-primary d-none">
                                        <h5 class="fw-bold mb-3">Status da Proposição</h5>
                                        <div id="statusDetails"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Interactive Process Test-->
            
            <!--begin::Process Tests-->
            <div class="row g-5 g-xl-8">
                <!--begin::Col-->
                <div class="col-xl-12">
                    <!--begin::Process Tests Card-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Testes Automatizados</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Execução completa do processo de tramitação</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            <div class="d-flex flex-wrap gap-3 mb-5">
                                <button 
                                    id="runProcessTestsBtn" 
                                    class="btn btn-success btn-sm"
                                >
                                    <i class="ki-duotone ki-code fs-4 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                    Executar Teste Completo
                                </button>
                                
                                <button 
                                    id="runPestTestsBtn" 
                                    class="btn btn-primary btn-sm"
                                >
                                    <i class="ki-duotone ki-rocket fs-4 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Executar Testes Unitários
                                </button>
                            </div>

                            <!-- Área de Resultados dos Testes de Processo -->
                            <div id="processResults" class="mb-5"></div>
                            
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Process Tests Card-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row Process Tests-->

        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Process Tests Elements
        const runProcessTestsBtn = document.getElementById('runProcessTestsBtn');
        const runPestTestsBtn = document.getElementById('runPestTestsBtn');
        const processResultsDiv = document.getElementById('processResults');
        
        // Interactive Test Elements
        const tipoProposicaoSelect = document.getElementById('tipoProposicaoSelect');
        const templateSelect = document.getElementById('templateSelect');
        const createProposicaoForm = document.getElementById('createProposicaoForm');
        const tramitationControls = document.getElementById('tramitationControls');
        const tramitationLog = document.getElementById('tramitationLog');
        const proposicaoStatus = document.getElementById('proposicaoStatus');
        const statusDetails = document.getElementById('statusDetails');
        const resetProcessTest = document.getElementById('resetProcessTest');
        
        let currentProposicao = null;
        let tramitationHistory = [];
        
        // Carregar tipos de proposição
        function loadTiposProposicao() {
            fetch('/tests/get-tipos-proposicao', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                tipoProposicaoSelect.innerHTML = '<option value="">Selecione...</option>';
                data.tipos.forEach(tipo => {
                    tipoProposicaoSelect.innerHTML += `<option value="${tipo.id}">${tipo.nome}</option>`;
                });
            })
            .catch(error => {
                console.error('Erro ao carregar tipos:', error);
            });
        }
        
        // Carregar templates quando o tipo for selecionado
        tipoProposicaoSelect.addEventListener('change', function() {
            if (this.value) {
                fetch(`/tests/get-templates/${this.value}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    templateSelect.innerHTML = '<option value="">Selecione...</option>';
                    data.templates.forEach(template => {
                        templateSelect.innerHTML += `<option value="${template.id}">Template ${template.id}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Erro ao carregar templates:', error);
                });
            } else {
                templateSelect.innerHTML = '<option value="">Selecione o tipo primeiro</option>';
            }
        });
        
        // Adicionar ao log
        function addToLog(message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString('pt-BR');
            const badgeClass = type === 'success' ? 'badge-success' : 
                              type === 'error' ? 'badge-danger' : 
                              type === 'warning' ? 'badge-warning' : 'badge-info';
            
            const logEntry = `
                <div class="d-flex align-items-start mb-3">
                    <span class="badge ${badgeClass} me-2">${timestamp}</span>
                    <div class="flex-grow-1">${message}</div>
                </div>
            `;
            
            tramitationLog.innerHTML = logEntry + tramitationLog.innerHTML;
            tramitationHistory.push({ time: timestamp, message, type });
        }
        
        // Atualizar status da proposição
        function updateProposicaoStatus() {
            if (!currentProposicao) return;
            
            proposicaoStatus.classList.remove('d-none');
            statusDetails.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID:</strong> ${currentProposicao.id}</p>
                        <p><strong>Tipo:</strong> ${currentProposicao.tipo}</p>
                        <p><strong>Status:</strong> <span class="badge badge-light-primary">${currentProposicao.status}</span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Autor:</strong> ${currentProposicao.autor || 'Parlamentar de Teste'}</p>
                        <p><strong>Protocolo:</strong> ${currentProposicao.numero_protocolo || 'Aguardando'}</p>
                        <p><strong>Ementa:</strong> ${currentProposicao.ementa}</p>
                    </div>
                </div>
            `;
        }
        
        // Criar proposição
        createProposicaoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                tipo_proposicao_id: tipoProposicaoSelect.value,
                template_id: templateSelect.value,
                ementa: document.getElementById('ementaInput').value,
                conteudo: document.getElementById('conteudoInput').value
            };
            
            addToLog('Criando proposição...', 'info');
            
            fetch('/tests/create-proposicao', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentProposicao = data.proposicao;
                    addToLog(`Proposição criada com ID: ${currentProposicao.id}`, 'success');
                    updateProposicaoStatus();
                    showTramitationControls();
                    createProposicaoForm.reset();
                } else {
                    addToLog('Erro ao criar proposição: ' + data.message, 'error');
                }
            })
            .catch(error => {
                addToLog('Erro ao criar proposição: ' + error.message, 'error');
            });
        });
        
        // Mostrar controles de tramitação
        function showTramitationControls() {
            tramitationControls.innerHTML = `
                <div class="d-grid gap-2">
                    <button class="btn btn-sm btn-info" onclick="enviarLegislativo()">
                        <i class="ki-duotone ki-send fs-4 me-1"></i>
                        Enviar ao Legislativo
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="analisarLegislativo()" disabled id="btnAnalisar">
                        <i class="ki-duotone ki-notepad-edit fs-4 me-1"></i>
                        Análise Legislativa
                    </button>
                    <button class="btn btn-sm btn-primary" onclick="converterPDF()" disabled id="btnPDF">
                        <i class="ki-duotone ki-file-down fs-4 me-1"></i>
                        Converter para PDF
                    </button>
                    <button class="btn btn-sm btn-success" onclick="assinarDocumento()" disabled id="btnAssinar">
                        <i class="ki-duotone ki-pencil fs-4 me-1"></i>
                        Assinar Documento
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="protocolizar()" disabled id="btnProtocolo">
                        <i class="ki-duotone ki-file-up fs-4 me-1"></i>
                        Protocolizar
                    </button>
                    <button class="btn btn-sm btn-secondary" onclick="enviarExpediente()" disabled id="btnExpediente">
                        <i class="ki-duotone ki-calendar fs-4 me-1"></i>
                        Enviar ao Expediente
                    </button>
                    <button class="btn btn-sm btn-dark" onclick="emitirParecer()" disabled id="btnParecer">
                        <i class="ki-duotone ki-law fs-4 me-1"></i>
                        Emitir Parecer Jurídico
                    </button>
                </div>
            `;
        }
        
        // Funções de tramitação
        window.enviarLegislativo = function() {
            if (!currentProposicao) return;
            
            addToLog('Enviando ao setor Legislativo...', 'info');
            
            fetch(`/tests/tramitar/${currentProposicao.id}/enviar-legislativo`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentProposicao = data.proposicao;
                    addToLog('Enviado ao Legislativo com sucesso', 'success');
                    updateProposicaoStatus();
                    document.getElementById('btnAnalisar').disabled = false;
                } else {
                    addToLog('Erro: ' + data.message, 'error');
                }
            });
        };
        
        window.analisarLegislativo = function() {
            if (!currentProposicao) return;
            
            addToLog('Legislativo analisando e fazendo alterações...', 'info');
            
            fetch(`/tests/tramitar/${currentProposicao.id}/analisar-legislativo`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentProposicao = data.proposicao;
                    addToLog('Análise legislativa concluída com alterações', 'success');
                    updateProposicaoStatus();
                    document.getElementById('btnPDF').disabled = false;
                }
            });
        };
        
        window.converterPDF = function() {
            if (!currentProposicao) return;
            
            addToLog('Convertendo documento para PDF...', 'info');
            
            fetch(`/tests/tramitar/${currentProposicao.id}/converter-pdf`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentProposicao = data.proposicao;
                    addToLog('Documento convertido para PDF', 'success');
                    updateProposicaoStatus();
                    document.getElementById('btnAssinar').disabled = false;
                }
            });
        };
        
        window.assinarDocumento = function() {
            if (!currentProposicao) return;
            
            addToLog('Parlamentar assinando documento...', 'info');
            
            fetch(`/tests/tramitar/${currentProposicao.id}/assinar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentProposicao = data.proposicao;
                    addToLog('Documento assinado digitalmente', 'success');
                    updateProposicaoStatus();
                    document.getElementById('btnProtocolo').disabled = false;
                }
            });
        };
        
        window.protocolizar = function() {
            if (!currentProposicao) return;
            
            addToLog('Protocolizando documento...', 'info');
            
            fetch(`/tests/tramitar/${currentProposicao.id}/protocolizar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentProposicao = data.proposicao;
                    addToLog(`Protocolizado com número: ${currentProposicao.numero_protocolo}`, 'success');
                    updateProposicaoStatus();
                    document.getElementById('btnExpediente').disabled = false;
                    document.getElementById('btnParecer').disabled = false;
                }
            });
        };
        
        window.enviarExpediente = function() {
            if (!currentProposicao) return;
            
            addToLog('Enviando ao Expediente...', 'info');
            
            fetch(`/tests/tramitar/${currentProposicao.id}/enviar-expediente`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentProposicao = data.proposicao;
                    addToLog(`Incluído na pauta - Momento: ${data.momento}`, 'success');
                    updateProposicaoStatus();
                }
            });
        };
        
        window.emitirParecer = function() {
            if (!currentProposicao) return;
            
            addToLog('Assessor Jurídico emitindo parecer...', 'info');
            
            fetch(`/tests/tramitar/${currentProposicao.id}/emitir-parecer`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentProposicao = data.proposicao;
                    addToLog('Parecer jurídico favorável emitido', 'success');
                    updateProposicaoStatus();
                    addToLog('🎉 Processo de tramitação completo!', 'success');
                }
            });
        };
        
        // Reset do teste
        resetProcessTest.addEventListener('click', function() {
            if (confirm('Deseja reiniciar o teste? Isso apagará a proposição atual.')) {
                currentProposicao = null;
                tramitationHistory = [];
                tramitationLog.innerHTML = '<div class="text-muted">Aguardando ações...</div>';
                proposicaoStatus.classList.add('d-none');
                tramitationControls.innerHTML = `
                    <div class="alert alert-info">
                        <i class="ki-duotone ki-information fs-2 me-2"></i>
                        Crie uma proposição primeiro para habilitar os controles
                    </div>
                `;
                createProposicaoForm.reset();
                addToLog('Teste reiniciado', 'warning');
            }
        });
        
        // Inicializar
        loadTiposProposicao();
        
        // Workflow Animation Elements
        const startWorkflowBtn = document.getElementById('startWorkflow');
        const pauseWorkflowBtn = document.getElementById('pauseWorkflow');
        const resetWorkflowBtn = document.getElementById('resetWorkflow');
        const documentTypeSelect = document.getElementById('documentType');
        const animationSpeedSelect = document.getElementById('animationSpeed');
        const currentStatusDiv = document.getElementById('currentStatus');
        
        let currentStep = 0;
        let animationInterval;
        let isAnimating = false;
        let isPaused = false;
        
        const workflowSteps = [
            { id: 'step-1', name: 'Criação pelo Parlamentar', status: 'Rascunho criado', badge: 'primary' },
            { id: 'step-2', name: 'Coleta de Assinaturas', status: 'Coletando assinaturas', badge: 'info' },
            { id: 'step-3', name: 'Protocolo', status: 'Aguardando protocolo', badge: 'warning' },
            { id: 'step-4', name: 'Análise Legislativa', status: 'Em análise técnica', badge: 'success' },
            { id: 'step-5', name: 'Parecer Jurídico', status: 'Análise jurídica', badge: 'danger' },
            { id: 'step-6', name: 'Expediente', status: 'Inclusão na pauta', badge: 'secondary' },
            { id: 'step-7', name: 'Sessão Plenária', status: 'Aguardando votação', badge: 'primary' }
        ];
        
        function updateStatus(message, badgeClass = 'primary') {
            currentStatusDiv.className = `badge badge-light-${badgeClass} fs-7 p-2`;
            currentStatusDiv.textContent = message;
        }
        
        function resetWorkflow() {
            currentStep = 0;
            isAnimating = false;
            isPaused = false;
            
            // Reset all steps
            for (let i = 1; i <= 7; i++) {
                const step = document.getElementById(`step-${i}`);
                const stepNumber = document.getElementById(`step-number-${i}`);
                const document = document.getElementById(`document-${i}`);
                
                step.classList.remove('active', 'completed', 'pending');
                stepNumber.classList.remove('active', 'completed');
                if (document) document.style.display = 'none';
            }
            
            clearInterval(animationInterval);
            updateStatus('Aguardando início da simulação');
            
            startWorkflowBtn.disabled = false;
            pauseWorkflowBtn.disabled = true;
            startWorkflowBtn.innerHTML = `
                <i class="ki-duotone ki-play fs-4 me-1"></i>
                Iniciar Simulação
            `;
        }
        
        function activateStep(stepIndex) {
            if (stepIndex >= workflowSteps.length) {
                // Animation completed
                updateStatus('Tramitação concluída! Documento pronto para votação', 'success');
                startWorkflowBtn.disabled = false;
                pauseWorkflowBtn.disabled = true;
                isAnimating = false;
                clearInterval(animationInterval);
                return;
            }
            
            const stepData = workflowSteps[stepIndex];
            const step = document.getElementById(stepData.id);
            const stepNumber = document.getElementById(`step-number-${stepIndex + 1}`);
            const document = document.getElementById(`document-${stepIndex + 1}`);
            
            // Remove previous active states
            document.querySelectorAll('.workflow-step').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.step-number').forEach(s => s.classList.remove('active'));
            
            // Mark previous steps as completed
            for (let i = 0; i < stepIndex; i++) {
                const prevStep = document.getElementById(workflowSteps[i].id);
                const prevStepNumber = document.getElementById(`step-number-${i + 1}`);
                prevStep.classList.add('completed');
                prevStepNumber.classList.add('completed');
            }
            
            // Activate current step
            step.classList.add('active');
            stepNumber.classList.add('active');
            if (document) document.style.display = 'flex';
            
            updateStatus(stepData.status, stepData.badge);
        }
        
        function startAnimation() {
            if (isPaused) {
                // Resume animation
                isPaused = false;
                startWorkflowBtn.innerHTML = `
                    <i class="ki-duotone ki-pause fs-4 me-1"></i>
                    Pausar
                `;
            } else {
                // Start new animation
                currentStep = 0;
                isAnimating = true;
                
                startWorkflowBtn.innerHTML = `
                    <i class="ki-duotone ki-pause fs-4 me-1"></i>
                    Pausar
                `;
            }
            
            pauseWorkflowBtn.disabled = false;
            
            const speed = parseInt(animationSpeedSelect.value);
            const docType = documentTypeSelect.options[documentTypeSelect.selectedIndex].text;
            
            updateStatus(`Iniciando tramitação: ${docType}`, 'info');
            
            animationInterval = setInterval(() => {
                if (!isPaused) {
                    activateStep(currentStep);
                    currentStep++;
                    
                    if (currentStep >= workflowSteps.length) {
                        clearInterval(animationInterval);
                        isAnimating = false;
                        startWorkflowBtn.innerHTML = `
                            <i class="ki-duotone ki-play fs-4 me-1"></i>
                            Iniciar Simulação
                        `;
                        pauseWorkflowBtn.disabled = true;
                    }
                }
            }, speed);
        }
        
        function pauseAnimation() {
            isPaused = true;
            clearInterval(animationInterval);
            updateStatus('Simulação pausada', 'warning');
            startWorkflowBtn.innerHTML = `
                <i class="ki-duotone ki-play fs-4 me-1"></i>
                Continuar
            `;
            startWorkflowBtn.disabled = false;
        }
        
        // Event Listeners
        startWorkflowBtn.addEventListener('click', function() {
            if (isAnimating && !isPaused) {
                pauseAnimation();
            } else {
                startAnimation();
            }
        });
        
        pauseWorkflowBtn.addEventListener('click', pauseAnimation);
        resetWorkflowBtn.addEventListener('click', resetWorkflow);
        
        // Initialize
        resetWorkflow();

        // Função para mostrar resultados dos testes de processo
        function showProcessResults(results) {
            let html = '';
            
            results.forEach(result => {
                let alertClass = 'alert-success';
                let iconClass = 'ki-check-circle text-success';
                let borderClass = 'border-success';
                
                if (result.status === 'error') {
                    alertClass = 'alert-danger';
                    iconClass = 'ki-cross-circle text-danger';
                    borderClass = 'border-danger';
                } else if (result.status === 'warning') {
                    alertClass = 'alert-warning';
                    iconClass = 'ki-information text-warning';
                    borderClass = 'border-warning';
                }
                
                // Formatar mensagem com quebras de linha e destaque para informações importantes
                let formattedMessage = result.message;
                if (result.test === 'Processo Completo de Tramitação') {
                    formattedMessage = result.message.replace(/\n/g, '<br>');
                    formattedMessage = formattedMessage.replace(/(Proposição ID: \d+)/g, '<strong class="text-primary">$1</strong>');
                    formattedMessage = formattedMessage.replace(/(Número de Protocolo: [^\s]+)/g, '<strong class="text-info">$1</strong>');
                    formattedMessage = formattedMessage.replace(/(Status Final: [^\s]+)/g, '<strong class="text-success">$1</strong>');
                    formattedMessage = formattedMessage.replace(/(\d+\. [\w_]+)/g, '<span class="badge badge-light-primary me-1">$1</span>');
                }
                
                html += `
                    <div class="alert ${alertClass} border ${borderClass} border-2 d-flex align-items-start p-5 mb-3">
                        <i class="ki-duotone ${iconClass} fs-2hx me-4 mt-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="d-flex flex-column flex-grow-1">
                            <h4 class="mb-3 text-dark fw-bold">${result.test}</h4>
                            <div class="fs-7 ${result.test === 'Processo Completo de Tramitação' ? 'bg-light p-3 rounded' : ''}">${formattedMessage}</div>
                        </div>
                    </div>
                `;
            });
            
            processResultsDiv.innerHTML = html;
        }

        // Executar testes de processo
        runProcessTestsBtn.addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Executando...';
            
            fetch('/tests/run-process-tests', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showProcessResults(data.results);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                processResultsDiv.innerHTML = `
                    <div class="alert alert-danger d-flex align-items-center p-5">
                        <i class="ki-duotone ki-cross-circle text-danger fs-2hx me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-dark fw-bold">Erro</h4>
                            <span class="fs-7">Erro ao executar testes de processo</span>
                        </div>
                    </div>
                `;
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = `
                    <i class="ki-duotone ki-code fs-4 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                    Executar Testes de Processo
                `;
            });
        });

        // Executar testes Pest
        runPestTestsBtn.addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Executando...';
            
            fetch('/tests/run-pest-tests', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                let alertClass = data.success ? 'alert-success' : 'alert-danger';
                let iconClass = data.success ? 'ki-check-circle text-success' : 'ki-cross-circle text-danger';
                let title = data.success ? 'Testes Executados com Sucesso' : 'Falha na Execução dos Testes';
                
                processResultsDiv.innerHTML = `
                    <div class="alert ${alertClass} d-flex align-items-center p-5">
                        <i class="ki-duotone ${iconClass} fs-2hx me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-dark fw-bold">${title}</h4>
                            <pre class="fs-8 mt-2 text-muted" style="white-space: pre-wrap; max-height: 300px; overflow-y: auto;">${data.output || data.error || 'Nenhuma saída disponível'}</pre>
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                console.error('Erro:', error);
                processResultsDiv.innerHTML = `
                    <div class="alert alert-danger d-flex align-items-center p-5">
                        <i class="ki-duotone ki-cross-circle text-danger fs-2hx me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-dark fw-bold">Erro</h4>
                            <span class="fs-7">Erro ao executar testes Pest</span>
                        </div>
                    </div>
                `;
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = `
                    <i class="ki-duotone ki-rocket fs-4 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Executar Testes Pest
                `;
            });
        });
    });
</script>
@endpush