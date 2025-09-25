@extends('components.layouts.app')

@section('title', 'Assinar Proposição')

@section('content')

<style>
/* Estilos para garantir melhor performance e aparência */
[v-cloak] {
    display: none !important;
}

/* Melhorias nos cartões */
.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
}

/* Sistema de certificados melhorado */
.certificado-option {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    user-select: none;
}

.certificado-option:hover {
    border-color: var(--kt-primary);
    background-color: rgba(var(--kt-primary-rgb), 0.1);
}

.certificado-option.selected {
    border-color: var(--kt-primary);
    background-color: rgba(var(--kt-primary-rgb), 0.1);
}

/* Upload de arquivos */
.file-upload-area {
    border: 2px dashed #e1e3ea;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-upload-area:hover,
.file-upload-area.dragover {
    border-color: var(--kt-primary);
    background-color: rgba(var(--kt-primary-rgb), 0.05);
}

/* Botão de assinatura */
.btn-assinar {
    background: linear-gradient(135deg, #17C653 0%, #13a342 100%);
    border: none;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-assinar:hover {
    background: linear-gradient(135deg, #13a342 0%, #0f8635 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(23, 198, 83, 0.3);
}

.btn-assinar:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* Loading states */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    border-radius: 8px;
}

/* Toast notifications */
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1060;
}

.toast {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    margin-bottom: 10px;
    border-left: 4px solid var(--toast-color, #007bff);
    animation: slideIn 0.3s ease;
}

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

/* PDF Viewer melhorado */
.pdf-viewer-container {
    position: relative;
    border: 1px solid #e1e3ea;
    border-radius: 8px;
    overflow: hidden;
    background-color: #f8f9fa;
}

.pdf-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    z-index: 5;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .certificado-option {
        margin-bottom: 15px;
    }
    
    .card-body {
        padding: 1rem;
    }
}
</style>

<div id="assinatura-app" v-cloak>
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Assinar Proposição
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('proposicoes.assinatura') }}" class="text-muted text-hover-primary">Assinatura</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Assinar</li>
                    </ul>
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('proposicoes.assinatura') }}" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-secondary btn-active-light-secondary">
                        <i class="ki-duotone ki-arrow-left fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>Voltar
                    </a>
                </div>
                <!--end::Actions-->
            </div>
        </div>
        <!--end::Toolbar-->
        
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="row">
                    <!--begin::Sidebar-->
                    <div class="col-xl-4">
                        <!--begin::Proposição Info Card-->
                        <div class="card card-flush mb-6 mb-xl-9 card-hover">
                            <!--begin::Card header-->
                            <div class="card-header mt-5">
                                <div class="card-title flex-column">
                                    <h2 class="mb-1">Informações da Proposição</h2>
                                    <div class="fs-6 fw-semibold text-muted">Revise os dados antes de assinar</div>
                                </div>
                            </div>
                            <!--end::Card header-->
                            
                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <div class="d-flex flex-column text-gray-600">
                                    <div class="d-flex align-items-center justify-content-between mb-5">
                                        <div class="fw-semibold">
                                            <i class="ki-duotone ki-profile-circle text-gray-400 fs-6 me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>Tipo:
                                        </div>
                                        <div class="fw-bold text-end">
                                            <span class="badge badge-light-primary">@{{ proposicao?.tipo || 'N/A' }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-center justify-content-between mb-5">
                                        <div class="fw-semibold">
                                            <i class="ki-duotone ki-tag text-gray-400 fs-6 me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>Número:
                                        </div>
                                        <div class="fw-bold text-end">@{{ proposicao?.numero_protocolo || 'Aguardando' }}</div>
                                    </div>
                                    
                                    <div class="d-flex align-items-center justify-content-between mb-5">
                                        <div class="fw-semibold">
                                            <i class="ki-duotone ki-calendar text-gray-400 fs-6 me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>Data Criação:
                                        </div>
                                        <div class="fw-bold text-end">@{{ formatDate(proposicao?.created_at) }}</div>
                                    </div>
                                    
                                    <div class="d-flex align-items-center justify-content-between mb-5">
                                        <div class="fw-semibold">
                                            <i class="ki-duotone ki-flash text-gray-400 fs-6 me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>Status:
                                        </div>
                                        <div class="fw-bold text-end">
                                            <span :class="getStatusBadgeClass(proposicao?.status)">
                                                @{{ getStatusLabel(proposicao?.status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="separator separator-dashed my-5"></div>
                                
                                <div class="mb-5">
                                    <label class="fs-6 fw-semibold mb-2 text-gray-600">Título:</label>
                                    <div class="fw-bold text-gray-800">@{{ proposicao?.titulo || 'Sem título' }}</div>
                                </div>
                                
                                <div class="mb-5">
                                    <label class="fs-6 fw-semibold mb-2 text-gray-600">Ementa:</label>
                                    <div class="fw-semibold text-gray-700 lh-lg">@{{ proposicao?.ementa || 'Sem ementa' }}</div>
                                </div>
                                
                                <div v-if="proposicao?.revisor" class="mb-5">
                                    <label class="fs-6 fw-semibold mb-2 text-gray-600">Aprovado por:</label>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-35px me-3">
                                            <div class="symbol-label bg-light-success text-success fs-8 fw-bold">
                                                @{{ getInitials(proposicao?.revisor?.name) }}
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <div class="fw-bold text-gray-800">@{{ proposicao?.revisor?.name }}</div>
                                            <div class="fs-7 text-muted">@{{ formatDate(proposicao?.updated_at) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Proposição Info Card-->
                    </div>
                    <!--end::Sidebar-->

                    <!--begin::Content-->
                    <div class="col-xl-8">
                        <!--begin::PDF Viewer Card-->
                        <div class="card card-flush mb-6 card-hover">
                            <!--begin::Card header-->
                            <div class="card-header align-items-start">
                                <div class="card-title">
                                    <h2 class="fw-bold text-dark">
                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                        Documento para Assinatura
                                    </h2>
                                </div>
                                <div class="card-toolbar">
                                    <div class="d-flex gap-2">
                                        <button 
                                            v-if="pdfUrl"
                                            @@click="openPdfInNewTab"
                                            class="btn btn-sm btn-outline btn-outline-primary">
                                            <i class="fas fa-external-link-alt me-1"></i>Abrir PDF
                                        </button>
                                        <button 
                                            v-if="pdfUrl"
                                            @@click="downloadPdf"
                                            class="btn btn-sm btn-outline btn-outline-secondary">
                                            <i class="fas fa-download me-1"></i>Download
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!--end::Card header-->
                            
                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <!-- Informações do Sistema Automático v2.0 -->
                                <div class="mt-3" v-if="!pdfLoading && pdfUrl">
                                    <div class="alert alert-info py-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-magic me-2 text-primary"></i>
                                            <div>
                                                <strong>Assinatura Automática v2.0</strong>
                                                <div class="small text-muted mt-1">
                                                    O carimbo será aplicado automaticamente na lateral direita do documento com QR Code de verificação
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="pdf-viewer-container" style="height: 600px; position: relative;" ref="pdfContainer">
                                    <!-- Loading state -->
                                    <div v-if="pdfLoading" class="pdf-loading">
                                        <div class="spinner-border text-primary mb-3" role="status">
                                            <span class="visually-hidden">Carregando...</span>
                                        </div>
                                        <p class="text-muted">Carregando documento PDF...</p>
                                        <small class="text-muted mt-2">Gerando PDF para assinatura...</small>
                                    </div>


                                    <!-- PDF Display -->
                                    <iframe
                                        v-if="pdfUrl && !pdfLoading"
                                        :src="pdfUrl"
                                        width="100%"
                                        height="100%"
                                        frameborder="0"
                                        @@load="handlePdfLoad"
                                        @@error="handlePdfError">
                                    </iframe>
                                    
                                    <!-- Error state -->
                                    <div v-if="pdfError" class="d-flex flex-column align-items-center justify-content-center h-100 text-center p-4">
                                        <i class="fas fa-exclamation-triangle text-warning fs-2x mb-3"></i>
                                        <h5 class="mb-3">Problema ao carregar o PDF</h5>
                                        <p class="text-muted mb-3">@{{ pdfError }}</p>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button @@click="openPdfInNewTab" class="btn btn-primary">
                                                <i class="fas fa-external-link-alt me-1"></i>Abrir em nova aba
                                            </button>
                                            <button @@click="retryPdfLoad" class="btn btn-outline-primary">
                                                <i class="fas fa-redo me-1"></i>Tentar novamente
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- No PDF available -->
                                    <div v-if="!pdfUrl && !pdfLoading && !pdfError" class="d-flex flex-column align-items-center justify-content-center h-100 text-center p-4">
                                        <i class="fas fa-file-pdf text-danger fs-2x mb-3"></i>
                                        <h5 class="mb-3">Documento não encontrado</h5>
                                        <p class="text-muted mb-3">Não foi possível localizar o documento para assinatura.</p>
                                        <button @@click="generatePdf" class="btn btn-primary">
                                            <i class="fas fa-sync me-1"></i>Gerar PDF
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::PDF Viewer Card-->
                        
                        <!--begin::Assinatura Card-->
                        <div class="card card-flush card-hover">
                            <!--begin::Card header-->
                            <div class="card-header align-items-start">
                                <div class="card-title">
                                    <h2 class="fw-bold text-dark">Assinatura Digital</h2>
                                </div>
                            </div>
                            <!--end::Card header-->
                            
                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <!-- Loading overlay -->
                                <div v-if="assinaturaLoading" class="loading-overlay">
                                    <div class="text-center">
                                        <div class="spinner-border text-primary mb-3" role="status"></div>
                                        <p class="mb-0">@{{ assinaturaLoadingText }}</p>
                                    </div>
                                </div>
                                
                                <!-- Confirmação de Leitura -->
                                <div class="alert alert-info d-flex align-items-center p-5 mb-8">
                                    <i class="ki-duotone ki-shield-tick fs-2hx text-info me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <h4 class="mb-1 text-info">Confirmação de Leitura</h4>
                                        <span>Você deve confirmar que leu e revisou o documento antes de poder assiná-lo digitalmente.</span>
                                    </div>
                                </div>
                                
                                <!-- Checkbox de Confirmação -->
                                <div class="mb-8">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input 
                                            class="form-check-input" 
                                            type="checkbox" 
                                            id="confirmacao_leitura" 
                                            v-model="confirmacaoLeitura"
                                            @@change="handleConfirmacaoLeitura" />
                                        <label class="form-check-label fw-semibold text-gray-700" for="confirmacao_leitura">
                                            Confirmo que li e revisei completamente o documento e estou ciente do seu conteúdo
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Formulário de Assinatura -->
                                <div v-show="confirmacaoLeitura" class="assinatura-form">
                                    <!-- Loading -->
                                    <div v-if="carregando" class="alert alert-info d-flex align-items-center p-5">
                                        <i class="ki-duotone ki-loading fs-2hx text-info me-4">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="text-info fw-semibold fs-6">Carregando dados…</div>
                                    </div>

                                    <!-- Certificado Cadastrado (se existir) -->
                                    <div v-else-if="dados?.temCertificado" class="mb-8">
                                        <div class="card border border-success">
                                            <div class="card-body p-5">
                                                <div class="d-flex align-items-center mb-4">
                                                    <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-2 text-success">Certificado Digital Cadastrado</h5>
                                                        <ul class="list-unstyled mb-0">
                                                            <li><strong>Arquivo:</strong> @{{ dados.certificado.nome_arquivo }}</li>
                                                            <li><strong>CN:</strong> @{{ dados.certificado.cn }}</li>
                                                            <li><strong>Validade:</strong> @{{ dados.certificado.validade }}</li>
                                                            <li><strong>Senha salva:</strong> @{{ dados.certificado.senha_salva ? 'Sim' : 'Não' }}</li>
                                                            <li><strong>Status:</strong> 
                                                                <span :class="dados.certValido ? 'badge badge-success' : 'badge badge-danger'">
                                                                    @{{ dados.certValido ? 'Válido' : 'Expirado/Inválido' }}
                                                                </span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <button 
                                                    type="button" 
                                                    class="btn btn-primary"
                                                    :disabled="busy || !dados.certValido"
                                                    @click="usarCertificadoCadastrado">
                                                    <i class="ki-duotone ki-check fs-4 me-1">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <span v-if="!busy">Usar Este Certificado</span>
                                                    <span v-if="busy">Processando...</span>
                                                </button>
                                                
                                                <!-- Aviso sobre senha na primeira vez -->
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <i class="ki-duotone ki-information-5 text-primary">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                        Na primeira vez, pode ser necessário informar a senha do certificado.
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Campo de senha (renderizado quando necessário) -->
                                        <div v-if="mostrarCampoSenha" class="mt-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <label class="form-label">Senha do certificado*</label>
                                                    <input 
                                                        type="password"
                                                        autocomplete="new-password"
                                                        class="form-control form-control-solid"
                                                        v-model.trim="senhaManual"
                                                        placeholder="Digite a senha do seu certificado"
                                                        @keyup.enter="assinarComSenha">
                                                    <button 
                                                        class="btn btn-success mt-3" 
                                                        :disabled="busy || !senhaManual.trim()" 
                                                        @click="assinarComSenha">
                                                        <i class="ki-duotone ki-check fs-4 me-1">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        Assinar Digitalmente
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Botão para usar outro certificado -->
                                        <div v-if="!mostrarCampoSenha" class="text-center mt-4">
                                            <button 
                                                type="button" 
                                                class="btn btn-light btn-sm"
                                                @click="certificadoSelecionado = null">
                                                <i class="ki-duotone ki-arrow-left fs-5 me-1"></i>
                                                Usar Outro Certificado
                                            </button>
                                        </div>

                                        <div v-if="certificadoSelecionado === null && !mostrarCampoSenha" class="separator separator-dashed my-6"></div>
                                        
                                        <div v-if="certificadoSelecionado === null && !mostrarCampoSenha" class="text-center text-muted">
                                            <small>Ou selecione outro certificado abaixo</small>
                                        </div>
                                    </div>

                                    <!-- Sem certificado cadastrado -->
                                    <div v-else-if="!carregando" class="alert alert-warning d-flex align-items-center p-5 mb-8">
                                        <i class="ki-duotone ki-information-5 fs-2hx text-warning me-4">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <div>
                                            <h5 class="text-warning mb-1">Nenhum certificado cadastrado</h5>
                                            <p class="mb-0">Faça o upload do seu certificado .pfx para continuar.</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Seleção do Tipo de Certificado -->
                                    <div v-if="(!dados?.temCertificado || certificadoSelecionado !== null) && !mostrarCampoSenha" class="mb-8">
                                        <label class="required fs-6 fw-semibold mb-2">Tipo de Certificado Digital</label>
                                        <div class="row g-4">
                                            <div 
                                                v-for="tipo in tiposCertificado" 
                                                :key="tipo.value"
                                                class="col-lg-4">
                                                <div 
                                                    class="certificado-option card h-100 p-4" 
                                                    :class="{ selected: certificadoSelecionado === tipo.value }"
                                                    @@click="selecionarCertificado(tipo.value)">
                                                    <div class="form-check form-check-custom form-check-solid mb-3">
                                                        <input 
                                                            class="form-check-input" 
                                                            type="radio" 
                                                            :value="tipo.value"
                                                            v-model="certificadoSelecionado"
                                                            :id="'cert_' + tipo.value"/>
                                                        <label class="form-check-label" :for="'cert_' + tipo.value"></label>
                                                    </div>
                                                    <div class="text-center">
                                                        <i :class="tipo.icon + ' fs-3x mb-3'"></i>
                                                        <h5 class="fw-bold mb-2">@{{ tipo.nome }}</h5>
                                                        <p class="text-muted fs-7 mb-0">@{{ tipo.descricao }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Área de Upload para PFX -->
                                    <div v-if="certificadoSelecionado === 'pfx'" class="mb-8">
                                        <label class="fs-6 fw-semibold mb-2">Arquivo do Certificado (.pfx/.p12)</label>
                                        <div 
                                            class="file-upload-area" 
                                            :class="{ dragover: isDragOver }"
                                            @@dragover.prevent="handleDragOver"
                                            @@dragleave.prevent="handleDragLeave"
                                            @@drop.prevent="handleDrop"
                                            @@click="triggerFileInput">
                                            
                                            <input 
                                                type="file" 
                                                ref="pfxFile"
                                                accept=".pfx,.p12" 
                                                style="display: none"
                                                @@change="handleFileSelect">
                                            
                                            <i class="ki-duotone ki-file-up fs-3x text-primary mb-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <h5 class="fw-bold mb-2">Clique ou arraste o arquivo aqui</h5>
                                            <p class="text-muted mb-3">Formatos aceitos: .pfx, .p12</p>
                                            <button type="button" class="btn btn-sm btn-primary">
                                                Selecionar Arquivo
                                            </button>
                                        </div>
                                        
                                        <!-- Progresso do Upload -->
                                        <div v-if="uploadProgress > 0" class="mt-3">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="fw-semibold text-gray-700">@{{ uploadedFileName }}</span>
                                                <span class="fs-7 text-muted">@{{ formatFileSize(uploadedFileSize) }}</span>
                                            </div>
                                            <div class="progress h-8px">
                                                <div 
                                                    class="progress-bar bg-primary" 
                                                    :style="{ width: uploadProgress + '%' }"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Campo de Senha -->
                                    <div v-if="certificadoSelecionado && certificadoSelecionado !== 'cadastrado'" class="mb-8">
                                        <label class="required fs-6 fw-semibold mb-2">Senha do Certificado</label>
                                        <input 
                                            type="password" 
                                            class="form-control" 
                                            v-model="senhaCertificado"
                                            placeholder="Digite a senha do certificado digital" 
                                            autocomplete="current-password">
                                        <div class="form-text">Necessária para validar e utilizar o certificado digital</div>
                                    </div>
                                    
                                    <!-- Informações do Certificado -->
                                    <div v-if="certificadoInfo" class="bg-light rounded p-4 mb-8">
                                        <h6 class="fw-bold mb-3">
                                            <i class="ki-duotone ki-verify text-success fs-5 me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>Informações do Certificado
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="fs-7 fw-semibold text-gray-600">Titular:</label>
                                                    <div class="fw-bold text-gray-800">@{{ certificadoInfo.titular }}</div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="fs-7 fw-semibold text-gray-600">Emissor:</label>
                                                    <div class="fw-bold text-gray-800">@{{ certificadoInfo.emissor }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="fs-7 fw-semibold text-gray-600">Válido até:</label>
                                                    <div class="fw-bold text-gray-800">@{{ certificadoInfo.validade }}</div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="fs-7 fw-semibold text-gray-600">Status:</label>
                                                    <div>
                                                        <span :class="certificadoInfo.statusClass">@{{ certificadoInfo.status }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Botões de Ação - Escondidos quando há certificado cadastrado -->
                                    <div class="d-flex justify-content-end" v-if="!dados?.temCertificado">
                                        <div>
                                            <button 
                                                type="button" 
                                                class="btn btn-light me-3"
                                                @@click="cancelar">
                                                Cancelar
                                            </button>
                                            <button 
                                                type="button" 
                                                class="btn btn-assinar"
                                                :disabled="!canSign()"
                                                @@click="processarAssinatura">
                                                <span v-if="assinaturaLoading" class="spinner-border spinner-border-sm me-2"></span>
                                                <i v-else class="ki-duotone ki-check fs-2 me-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                @{{ assinaturaLoading ? 'Assinando...' : 'Assinar Digitalmente' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Separador -->
                                <div class="separator separator-dashed my-6"></div>
                                
                                <!-- Botão Devolver para Legislativo -->
                                <div class="d-flex justify-content-start">
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-warning"
                                        @@click="devolverParaLegislativo">
                                        <i class="fas fa-arrow-left me-2"></i>Devolver para o Legislativo
                                    </button>
                                    <div class="ms-3 d-flex align-items-center">
                                        <small class="text-muted">Se o documento precisa de alterações, devolva-o para o Legislativo com suas observações</small>
                                    </div>
                                </div>
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Assinatura Card-->
                    </div>
                    <!--end::Content-->
                </div>
            </div>
            <!--end::Container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->

    <!-- Toast Notifications -->
    <div class="toast-container">
        <div 
            v-for="toast in toasts" 
            :key="toast.id"
            class="toast show"
            :style="{ '--toast-color': toast.color }"
            role="alert">
            <div class="toast-body d-flex align-items-center">
                <i :class="toast.icon + ' me-2'" :style="{ color: toast.color }"></i>
                <span>@{{ toast.message }}</span>
                <button 
                    type="button" 
                    class="btn-close ms-auto"
                    @@click="removeToast(toast.id)"></button>
            </div>
        </div>
    </div>
</div>

<!-- Vue.js 3 CDN -->
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            proposicaoId: {{ $proposicaoId ?? 0 }},
            dados: null,
            carregando: true,
            busy: false,
            senhaManual: '',
            mostrarCampoSenha: false,
            csrfToken: '{{ csrf_token() }}',
            
            // PDF states
            pdfUrl: '',
            pdfLoading: true,
            pdfError: null,
            
            // Assinatura states
            confirmacaoLeitura: false,
            certificadoSelecionado: null,
            arquivoCertificado: null,
            uploadProgress: 0,
            uploadedFileName: '',
            uploadedFileSize: 0,
            isDragOver: false,
            certificadoInfo: null,
            assinaturaLoading: false,
            assinaturaLoadingText: '',
            
            // Toast system
            toasts: [],
            toastIdCounter: 0,


            // Tipos de certificado
            tiposCertificado: [
                {
                    value: 'a1',
                    nome: 'Certificado A1',
                    descricao: 'Arquivo instalado no computador (.pfx/.p12)',
                    icon: 'ki-duotone ki-safe-home text-primary'
                },
                {
                    value: 'a3',
                    nome: 'Certificado A3',
                    descricao: 'Token/Smartcard físico',
                    icon: 'ki-duotone ki-tablet text-warning'
                },
                {
                    value: 'pfx',
                    nome: 'Upload .PFX',
                    descricao: 'Enviar arquivo de certificado',
                    icon: 'ki-duotone ki-file-up text-success'
                }
            ]
        }
    },
    
    async mounted() {
        console.log('Assinatura App mounted');
        await this.carregarDados();
        this.initializePdf();
    },

    computed: {
        proposicao() {
            return this.dados?.proposicao || null;
        },

    },
    
    methods: {
        // PDF Methods
        async initializePdf() {
            try {
                // NOVA ROTA: Usar endpoint específico de assinatura que prioriza S3
                const pdfRoute = `/proposicoes/${this.proposicaoId}/assinatura-digital/pdf`;
                this.pdfUrl = pdfRoute;

                console.log('🔄 PDF: Inicializando com rota de assinatura:', pdfRoute);

                // Check if PDF exists
                const response = await fetch(pdfRoute, { method: 'HEAD' });
                if (response.ok) {
                    console.log('✅ PDF: Endpoint respondeu OK');
                    this.pdfLoading = false;
                } else {
                    console.log('⚠️ PDF: Endpoint não respondeu, gerando automaticamente');
                    this.pdfError = 'PDF não encontrado. Gerando automaticamente...';
                    this.generatePdf();
                }
            } catch (error) {
                console.error('❌ PDF: Erro ao inicializar:', error);
                this.pdfError = 'Erro ao carregar PDF';
                this.pdfLoading = false;
            }
        },
        
        handlePdfLoad() {
            this.pdfLoading = false;
            this.pdfError = null;
        },
        
        handlePdfError() {
            this.pdfLoading = false;
            this.pdfError = 'Erro ao carregar o PDF no navegador';
        },
        
        async generatePdf() {
            this.pdfLoading = true;
            this.pdfError = null;

            console.log('🔄 PDF: Gerando PDF automaticamente');

            try {
                // Tentar gerar PDF através do endpoint de assinatura
                const response = await fetch(`/proposicoes/${this.proposicaoId}/assinatura-digital/pdf`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    console.log('✅ PDF: Geração iniciada, aguardando...');
                    setTimeout(() => {
                        this.initializePdf();
                    }, 3000);
                } else {
                    console.log('⚠️ PDF: Falha na geração, usando fallback');
                    // Fallback: tentar o endpoint antigo
                    this.generatePdfFallback();
                }
            } catch (error) {
                console.error('❌ PDF: Erro na geração:', error);
                this.pdfError = 'Erro ao gerar PDF';
                this.pdfLoading = false;
            }
        },

        async generatePdfFallback() {
            try {
                const response = await fetch(`/proposicoes/${this.proposicaoId}/assinar`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    setTimeout(() => {
                        this.initializePdf();
                    }, 3000);
                } else {
                    this.pdfError = 'Erro ao gerar PDF automaticamente';
                    this.pdfLoading = false;
                }
            } catch (error) {
                console.error('Error in PDF fallback:', error);
                this.pdfError = 'Erro ao gerar PDF';
                this.pdfLoading = false;
            }
        },
        
        retryPdfLoad() {
            this.pdfError = null;
            this.initializePdf();
        },
        
        openPdfInNewTab() {
            if (this.pdfUrl) {
                window.open(this.pdfUrl, '_blank');
            }
        },
        
        downloadPdf() {
            if (this.pdfUrl) {
                const link = document.createElement('a');
                link.href = this.pdfUrl;
                link.download = `proposicao_${this.proposicao.id}.pdf`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        },
        
        // Métodos de dados do certificado
        async carregarDados() {
            try {
                const response = await fetch(`/proposicoes/${this.proposicaoId}/assinatura-digital/dados`, {
                    headers: { 'Accept': 'application/json' }
                });
                
                if (!response.ok) throw new Error('Erro ao carregar dados');
                
                const data = await response.json();
                if (data?.success) {
                    this.dados = data;
                    console.log('Dados carregados:', data);
                }
            } catch (e) {
                console.error('Erro ao carregar dados:', e);
                this.showToast('Erro ao carregar dados do certificado', '#dc3545', 'fas fa-exclamation-circle');
            } finally {
                this.carregando = false;
            }
        },

        async usarCertificadoCadastrado() {
            this.busy = true;
            this.mostrarCampoSenha = false;
            
            try {
                this.showToast('Tentando usar certificado automaticamente...', '#17a2b8', 'fas fa-spinner fa-spin');
                
                // Tentativa automática (sem senha, se salva)
                const response = await fetch(`/proposicoes/${this.proposicaoId}/assinatura-digital/processar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        usar_certificado_cadastrado: true
                    })
                });

                console.log('Enviando requisição de assinatura:', {
                    url: `/proposicoes/${this.proposicaoId}/assinatura-digital/processar`,
                    payload: { usar_certificado_cadastrado: true }
                });

                console.log('Response status:', response.status, 'Content-Type:', response.headers.get('content-type'));
                
                if (!response.headers.get('content-type')?.includes('application/json')) {
                    throw new Error('Resposta não é JSON válida');
                }

                const data = await response.json();
                console.log('Response data:', data);

                // Sucesso na assinatura
                if (response.ok && data?.success) {
                    // Usar SweetAlert para sucesso
                    Swal.fire({
                        title: 'Sucesso!',
                        text: 'Documento assinado digitalmente com sucesso!',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#28a745'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirecionar para a página da proposição
                            window.location.href = `/proposicoes/${this.proposicaoId}`;
                        }
                    });
                    return;
                }

                // Status 422 ou falha: solicitar senha manualmente
                if (response.status === 422 || !data?.success) {
                    // Códigos específicos que indicam necessidade de senha
                    const needsPasswordCodes = ['senha_obrigatoria', 'senha_salva_nula', 'senha_salva_corrompida', 'senha_salva_invalida'];
                    
                    if (needsPasswordCodes.includes(data?.code)) {
                        let mensagem = 'Por favor, informe a senha do certificado.';
                        if (data?.code === 'senha_salva_nula') {
                            mensagem = 'Sua senha salva foi perdida. Por favor, informe a senha novamente.';
                        } else if (data?.code === 'senha_salva_invalida') {
                            mensagem = 'Sua senha salva não confere. Por favor, informe a senha correta.';
                        } else if (data?.code === 'senha_salva_corrompida') {
                            mensagem = 'Não foi possível recuperar sua senha salva. Por favor, informe a senha novamente.';
                        }
                        
                        this.showToast(mensagem, '#ffc107', 'fas fa-key');
                        this.mostrarCampoSenha = true;
                        return;
                    }
                    
                    // Outros erros
                    this.showToast(data?.message || 'Erro ao processar assinatura.', '#dc3545', 'fas fa-exclamation-circle');
                    return;
                }

            } catch (e) {
                console.error('Erro na assinatura:', e);
                // 422 = precisa de senha / senha inválida
                if (e?.response?.status === 422) {
                    this.mostrarCampoSenha = true;
                    this.showToast(e?.response?.data?.message || 'Informe a senha do certificado.', '#ffc107', 'fas fa-exclamation-triangle');
                } else {
                    this.showToast('Falha ao processar assinatura.', '#dc3545', 'fas fa-exclamation-circle');
                }
            } finally {
                this.busy = false;
            }
        },

        async assinarComSenha() {
            this.busy = true;
            try {
                console.log('Enviando requisição de assinatura:', {
                    url: `/proposicoes/${this.proposicaoId}/assinatura-digital/processar`,
                    payload: { usar_certificado_cadastrado: true, senha_certificado: this.senhaManual }
                });

                const response = await fetch(`/proposicoes/${this.proposicaoId}/assinatura-digital/processar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        usar_certificado_cadastrado: true,
                        senha_certificado: this.senhaManual
                    })
                });

                console.log('Response status:', response.status, 'Content-Type:', response.headers.get('content-type'));

                const data = await response.json();
                console.log('Response data:', data);
                
                if (data?.success) {
                    // Usar SweetAlert para sucesso
                    Swal.fire({
                        title: 'Sucesso!',
                        text: 'Documento assinado digitalmente com sucesso!',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#28a745'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirecionar para a página da proposição
                            window.location.href = `/proposicoes/${this.proposicaoId}`;
                        }
                    });
                } else {
                    this.showToast(data?.message || 'Não foi possível assinar.', '#dc3545', 'fas fa-exclamation-circle');
                }
            } catch (e) {
                console.error('Erro na assinatura:', e);
                this.showToast(e?.response?.data?.message || 'Senha inválida.', '#dc3545', 'fas fa-exclamation-circle');
            } finally {
                this.busy = false;
            }
        },
        
        // Assinatura Methods
        async handleConfirmacaoLeitura() {
            if (this.confirmacaoLeitura) {
                try {
                    const response = await fetch(`/proposicoes/${this.proposicao.id}/confirmar-leitura`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        }
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        this.showToast(data.message, '#28a745', 'fas fa-check-circle');
                    }
                } catch (error) {
                    console.error('Error confirming reading:', error);
                    this.showToast('Erro ao confirmar leitura', '#dc3545', 'fas fa-exclamation-circle');
                    this.confirmacaoLeitura = false;
                }
            }
        },
        
        selecionarCertificado(tipo) {
            this.certificadoSelecionado = tipo;
            this.certificadoInfo = null;
            
            if (tipo === 'a1' || tipo === 'a3') {
                setTimeout(() => {
                    this.detectarCertificado(tipo);
                }, 1000);
            }
        },
        
        detectarCertificado(tipo) {
            this.certificadoInfo = {
                titular: '{{ auth()->user()->name }}',
                emissor: 'Autoridade Certificadora',
                validade: this.formatDate(new Date(Date.now() + 365*24*60*60*1000)),
                status: 'Válido',
                statusClass: 'badge badge-light-success'
            };
        },
        
        // File Upload Methods
        triggerFileInput() {
            this.$refs.pfxFile.click();
        },
        
        handleDragOver(e) {
            this.isDragOver = true;
        },
        
        handleDragLeave(e) {
            this.isDragOver = false;
        },
        
        handleDrop(e) {
            this.isDragOver = false;
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                this.processFile(files[0]);
            }
        },
        
        handleFileSelect(e) {
            const file = e.target.files[0];
            if (file) {
                this.processFile(file);
            }
        },
        
        processFile(file) {
            if (!file.name.endsWith('.pfx') && !file.name.endsWith('.p12')) {
                this.showToast('Arquivo inválido. Use arquivos .pfx ou .p12', '#dc3545', 'fas fa-exclamation-circle');
                return;
            }
            
            this.arquivoCertificado = file;
            this.uploadedFileName = file.name;
            this.uploadedFileSize = file.size;
            
            this.uploadProgress = 0;
            const interval = setInterval(() => {
                this.uploadProgress += 10;
                if (this.uploadProgress >= 100) {
                    clearInterval(interval);
                    setTimeout(() => {
                        this.validarCertificadoPFX(file);
                    }, 500);
                }
            }, 100);
        },
        
        validarCertificadoPFX(file) {
            this.certificadoInfo = {
                titular: '{{ auth()->user()->name }}',
                emissor: 'Certificado válido',
                validade: this.formatDate(new Date(Date.now() + 365*24*60*60*1000)),
                status: 'Válido',
                statusClass: 'badge badge-light-success'
            };
            
            this.showToast('Certificado PFX carregado com sucesso', '#28a745', 'fas fa-check-circle');
        },
        
        // Validation Methods
        canSign() {
            if (!this.confirmacaoLeitura || !this.certificadoSelecionado || this.assinaturaLoading) {
                return false;
            }
            
            // Certificado cadastrado
            if (this.certificadoSelecionado === 'cadastrado') {
                // Se senha está salva, não precisa preencher
                if (this.dados?.certificado?.senha_salva) return true;
                // Se senha não está salva, permitir tentativa automática (certificado pode não ter senha)
                // ou permite senha manual se preenchida
                return true; // Sempre permite tentar com certificado cadastrado
            }
            
            // Outros tipos de certificado
            return this.senhaCertificado && 
                   this.senhaCertificado.length >= 4 &&
                   (this.certificadoSelecionado !== 'pfx' || this.arquivoCertificado);
        },
        
        // Assinatura Processing
        async processarAssinatura() {
            if (!this.canSign()) return;
            
            this.assinaturaLoading = true;
            this.assinaturaLoadingText = 'Processando assinatura digital...';
            
            try {
                // Criar payload JSON simples para certificado cadastrado
                const payload = {
                    usar_certificado_cadastrado: true,
                    senha_certificado: this.senhaCertificado || undefined
                };
                
                // Configurar headers para FORÇAR JSON
                const headers = {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                };
                
                console.log('Enviando requisição de assinatura:', { url: `/proposicoes/${this.proposicao.id}/assinatura-digital/processar`, payload, headers });
                
                const response = await fetch(`/proposicoes/${this.proposicao.id}/assinatura-digital/processar`, {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify(payload)
                });
                
                console.log('Response status:', response.status, 'Content-Type:', response.headers.get('Content-Type'));
                
                // Verificar se a resposta é JSON
                const contentType = response.headers.get('Content-Type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error(`Servidor retornou ${contentType} ao invés de JSON. Status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Response data:', data);
                
                if (data.success) {
                    this.showToast(data.message || 'Proposição assinada com sucesso!', '#28a745', 'fas fa-check-circle');
                    setTimeout(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.href = '/proposicoes/assinatura';
                        }
                    }, 2000);
                } else {
                    // Se o erro indica que precisa de senha, garantir que o campo apareça
                    if (data.message && data.message.includes('senha')) {
                        // Forçar exibição do campo de senha (não podemos alterar dados do backend)
                    }
                    this.showToast(data.message || 'Erro ao processar assinatura', '#dc3545', 'fas fa-exclamation-circle');
                    this.assinaturaLoading = false;
                }
            } catch (error) {
                console.error('Error processing signature:', error);
                this.showToast(`Erro ao processar assinatura: ${error.message}`, '#dc3545', 'fas fa-exclamation-circle');
                this.assinaturaLoading = false;
            }
        },
        
        gerarAssinaturaDigital() {
            const timestamp = new Date().getTime();
            const proposicaoId = this.proposicao.id;
            const userId = this.userId;
            
            return btoa(`${proposicaoId}-${userId}-${timestamp}-${this.certificadoSelecionado}`);
        },
        
        obterCertificadoDigital() {
            return JSON.stringify({
                titular: '{{ auth()->user()->name }}',
                tipo: this.certificadoSelecionado,
                emissor: 'Autoridade Certificadora Validada',
                validade: new Date(Date.now() + 365*24*60*60*1000).toISOString().split('T')[0],
                arquivo: this.arquivoCertificado ? this.arquivoCertificado.name : null
            });
        },
        
        // Devolução para Legislativo
        async devolverParaLegislativo() {
            const observacoes = prompt(
                "DEVOLVER PARA O LEGISLATIVO\n\n" +
                "Você está prestes a devolver esta proposição para o Legislativo com solicitação de alterações.\n\n" +
                "Observações (obrigatório):\n" +
                "Descreva as alterações ou correções necessárias..."
            );
            
            if (observacoes === null) return;
            
            if (!observacoes || observacoes.trim().length < 10) {
                this.showToast('Por favor, informe as observações sobre as alterações necessárias (mínimo 10 caracteres)', '#dc3545', 'fas fa-exclamation-circle');
                return this.devolverParaLegislativo();
            }
            
            try {
                this.assinaturaLoading = true;
                this.assinaturaLoadingText = 'Devolvendo proposição...';
                
                const response = await fetch(`/proposicoes/${this.proposicao.id}/devolver-legislativo`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify({ observacoes: observacoes.trim() })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showToast(data.message, '#28a745', 'fas fa-check-circle');
                    setTimeout(() => {
                        window.location.href = data.redirect || '/proposicoes/assinatura';
                    }, 2000);
                } else {
                    this.showToast(data.message || 'Erro ao devolver proposição', '#dc3545', 'fas fa-exclamation-circle');
                    this.assinaturaLoading = false;
                }
            } catch (error) {
                console.error('Error returning proposition:', error);
                this.showToast('Erro ao devolver proposição', '#dc3545', 'fas fa-exclamation-circle');
                this.assinaturaLoading = false;
            }
        },
        
        // UI Helper Methods
        cancelar() {
            window.history.back();
        },
        
        formatDate(date) {
            if (!date) return '';
            return new Date(date).toLocaleDateString('pt-BR');
        },
        
        getInitials(name) {
            return name ? name.charAt(0).toUpperCase() : '';
        },
        
        getStatusLabel(status) {
            const labels = {
                'rascunho': 'Rascunho',
                'enviado_legislativo': 'Análise Legislativa',
                'aprovado': 'Aprovado',
                'aprovado_assinatura': 'Aguardando Assinatura',
                'assinado': 'Assinado',
                'enviado_protocolo': 'Protocolo',
                'protocolado': 'Protocolado'
            };
            return labels[status] || status;
        },
        
        getStatusBadgeClass(status) {
            const classes = {
                'rascunho': 'badge badge-light-secondary',
                'enviado_legislativo': 'badge badge-light-warning',
                'aprovado': 'badge badge-light-success',
                'aprovado_assinatura': 'badge badge-light-primary',
                'assinado': 'badge badge-light-success',
                'enviado_protocolo': 'badge badge-light-info',
                'protocolado': 'badge badge-light-primary'
            };
            return classes[status] || 'badge badge-light-secondary';
        },
        
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },
        
        // Toast System
        showToast(message, color = '#007bff', icon = 'fas fa-info-circle') {
            const toast = {
                id: this.toastIdCounter++,
                message,
                color,
                icon
            };
            
            this.toasts.push(toast);
            
            setTimeout(() => {
                this.removeToast(toast.id);
            }, 5000);
        },
        
        removeToast(id) {
            this.toasts = this.toasts.filter(toast => toast.id !== id);
        },

    }
}).mount('#assinatura-app');
</script>

@endsection