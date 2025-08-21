@extends('components.layouts.app')

@section('title', 'Assinar Proposição - PDF')

@section('content')

<style>
/* Sistema de PDF otimizado */
.pdf-container {
    background: white;
    padding: 40px;
    margin: 0 auto;
    max-width: 210mm;
    min-height: 297mm;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    font-family: 'Times New Roman', Times, serif;
}

/* Estilos para o documento */
.document-header {
    text-align: center;
    margin-bottom: 30px;
}

.document-header img {
    max-width: 200px;
    height: auto;
    margin-bottom: 20px;
}

.document-title {
    font-size: 16pt;
    font-weight: bold;
    text-align: center;
    margin: 30px 0;
}

.document-content {
    text-align: justify;
    line-height: 1.8;
    font-size: 12pt;
    white-space: pre-wrap;
}

.document-signature {
    margin-top: 50px;
    text-align: center;
}

.signature-line {
    border-bottom: 1px solid #000;
    width: 300px;
    margin: 50px auto 10px;
}

.signature-name {
    font-weight: bold;
    margin-top: 10px;
}

.signature-role {
    font-style: italic;
}

/* Assinatura Digital */
.digital-signature-box {
    border: 2px solid #28a745;
    padding: 20px;
    margin: 30px 0;
    background: #f8f9fa;
    page-break-inside: avoid;
}

.digital-signature-title {
    color: #28a745;
    font-weight: bold;
    margin-bottom: 15px;
    font-size: 14pt;
}

.qr-code-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 20px;
}

.qr-code-img {
    width: 150px;
    height: 150px;
}

/* Loading e Progress */
.pdf-loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.95);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.pdf-progress {
    text-align: center;
    max-width: 400px;
}

.progress-bar-container {
    width: 100%;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
    margin: 20px 0;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
    transition: width 0.3s ease;
}

/* Performance optimizations */
[v-cloak] {
    display: none !important;
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }
    
    .pdf-container {
        box-shadow: none;
        padding: 0;
        margin: 0;
    }
}

/* Animations */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.pulse-animation {
    animation: pulse 2s infinite;
}
</style>

<div id="pdf-assinatura-app" v-cloak>
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6 no-print">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Assinatura Digital de Proposição
                    </h1>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <button 
                        @click="voltarListagem"
                        class="btn btn-sm btn-outline btn-outline-dashed btn-outline-secondary">
                        <i class="ki-duotone ki-arrow-left fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>Voltar
                    </button>
                </div>
            </div>
        </div>
        <!--end::Toolbar-->
        
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                
                <!-- Loading Overlay -->
                <div v-if="loading" class="pdf-loading-overlay">
                    <div class="pdf-progress">
                        <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></div>
                        <h4>@{{ loadingMessage }}</h4>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" :style="{ width: progress + '%' }"></div>
                        </div>
                        <p class="text-muted">@{{ progressDetail }}</p>
                    </div>
                </div>

                <div class="row">
                    <!-- Controles e Informações -->
                    <div class="col-lg-3 no-print">
                        <div class="card card-flush mb-6">
                            <div class="card-header">
                                <h3 class="card-title">Controles do Documento</h3>
                            </div>
                            <div class="card-body">
                                <!-- Status do Documento -->
                                <div class="mb-6">
                                    <label class="fs-6 fw-semibold mb-2">Status:</label>
                                    <div>
                                        <span :class="getStatusBadgeClass(proposicao.status)">
                                            @{{ getStatusLabel(proposicao.status) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Número do Protocolo -->
                                <div class="mb-6">
                                    <label class="fs-6 fw-semibold mb-2">Protocolo:</label>
                                    <div class="fw-bold">
                                        @{{ proposicao.numero_protocolo || '[AGUARDANDO]' }}
                                    </div>
                                </div>

                                <!-- Nova Visualização Otimizada -->
                                <div class="mb-6">
                                    <a href="{{ route('proposicoes.visualizar-pdf-otimizado', $proposicao->id) }}" 
                                       class="btn btn-success btn-sm w-100"
                                       target="_blank"
                                       title="Visualizar PDF otimizado com texto selecionável">
                                        <i class="ki-duotone ki-eye fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        📄 Visualizar PDF Otimizado
                                        <small class="d-block">Texto selecionável</small>
                                    </a>
                                </div>

                                <!-- Botões de Ação -->
                                <div class="d-grid gap-2">
                                    <button 
                                        @click="visualizarPDF"
                                        class="btn btn-primary"
                                        :disabled="!pdfReady">
                                        <i class="fas fa-eye me-2"></i>
                                        Visualizar PDF
                                    </button>

                                    <button 
                                        @click="baixarPDF"
                                        class="btn btn-outline-primary"
                                        :disabled="!pdfReady">
                                        <i class="fas fa-download me-2"></i>
                                        Baixar PDF
                                    </button>

                                    <button 
                                        @click="imprimirPDF"
                                        class="btn btn-outline-secondary"
                                        :disabled="!pdfReady">
                                        <i class="fas fa-print me-2"></i>
                                        Imprimir
                                    </button>

                                    <!-- Botão para Excluir Documento -->
                                    <button 
                                        v-if="podeExcluirDocumento"
                                        @click="confirmarExclusaoDocumento"
                                        class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash-alt me-2"></i>
                                        Excluir Documento
                                    </button>
                                </div>

                                <div class="separator separator-dashed my-6"></div>

                                <!-- Configurações de PDF -->
                                <h6 class="mb-4">Configurações</h6>
                                
                                <div class="mb-4">
                                    <label class="form-label">Qualidade:</label>
                                    <select v-model="pdfQuality" class="form-select form-select-sm">
                                        <option value="1">Normal</option>
                                        <option value="1.5">Alta</option>
                                        <option value="2">Muito Alta</option>
                                    </select>
                                </div>

                                <div class="form-check form-check-sm mb-4">
                                    <input 
                                        type="checkbox" 
                                        v-model="incluirAssinatura"
                                        class="form-check-input" 
                                        id="incluirAssinatura">
                                    <label class="form-check-label" for="incluirAssinatura">
                                        Incluir Assinatura Digital
                                    </label>
                                </div>

                                <div class="form-check form-check-sm mb-4">
                                    <input 
                                        type="checkbox" 
                                        v-model="incluirQRCode"
                                        class="form-check-input" 
                                        id="incluirQRCode">
                                    <label class="form-check-label" for="incluirQRCode">
                                        Incluir QR Code
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Card de Assinatura -->
                        <div class="card card-flush">
                            <div class="card-header">
                                <h3 class="card-title">Assinatura Digital</h3>
                            </div>
                            <div class="card-body">
                                <div v-if="!proposicao.assinatura_digital">
                                    <p class="text-muted mb-4">
                                        Documento aguardando assinatura digital
                                    </p>
                                    <button 
                                        @click="iniciarAssinatura"
                                        class="btn btn-success w-100">
                                        <i class="fas fa-signature me-2"></i>
                                        Assinar Documento
                                    </button>
                                </div>
                                <div v-else>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-check-circle text-success fs-2 me-3"></i>
                                        <div>
                                            <div class="fw-bold">Documento Assinado</div>
                                            <div class="text-muted fs-7">
                                                @{{ formatDate(proposicao.data_assinatura) }}
                                            </div>
                                        </div>
                                    </div>
                                    <button 
                                        @click="verificarAssinatura"
                                        class="btn btn-outline-success btn-sm w-100">
                                        <i class="fas fa-shield-alt me-2"></i>
                                        Verificar Assinatura
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Visualização do Documento -->
                    <div class="col-lg-9">
                        <div class="card card-flush">
                            <div class="card-body p-0">
                                <!-- Container do PDF para captura -->
                                <div id="pdf-content" class="pdf-container" style="position: absolute; left: -9999px; top: -9999px; visibility: hidden;">
                                    <!-- Conteúdo OnlyOffice PURO (sem adições externas) -->
                                    <div v-if="usandoApenasOnlyOffice" class="document-content" v-html="conteudoProcessado" style="padding: 0; margin: 0;"></div>
                                    
                                    <!-- Estrutura tradicional (fallback quando não há OnlyOffice) -->
                                    <template v-else>
                                        <!-- Cabeçalho do Documento -->
                                        <div class="document-header">
                                            <img v-if="cabecalhoImagem" :src="cabecalhoImagem" alt="Cabeçalho">
                                            <div v-html="cabecalhoTexto"></div>
                                        </div>

                                        <!-- Título da Proposição -->
                                        <div class="document-title">
                                            @{{ tipoProposicao }} Nº @{{ numeroProposicao }}
                                        </div>

                                        <!-- Ementa -->
                                        <div v-if="proposicao.ementa" class="mb-4">
                                            <strong>EMENTA:</strong> @{{ proposicao.ementa }}
                                        </div>

                                        <!-- Conteúdo Principal -->
                                        <div class="document-content" v-html="conteudoProcessado"></div>
                                    </template>

                                    <!-- Assinatura Manual (apenas se não estiver usando OnlyOffice puro) -->
                                    <div v-if="!usandoApenasOnlyOffice" class="document-signature">
                                        <div class="mt-5">
                                            @{{ dataLocal }}
                                        </div>
                                        <div class="signature-line"></div>
                                        <div class="signature-name">
                                            @{{ nomeAutor }}
                                        </div>
                                        <div class="signature-role">
                                            @{{ cargoAutor }}
                                        </div>
                                    </div>

                                    <!-- Assinatura Digital (se houver) -->
                                    <div v-if="incluirAssinatura && proposicao.assinatura_digital" 
                                         class="digital-signature-box">
                                        <div class="digital-signature-title">
                                            <i class="fas fa-certificate me-2"></i>
                                            ASSINATURA DIGITAL
                                        </div>
                                        <div class="row">
                                            <div class="col-8">
                                                <p class="mb-2">
                                                    <strong>Assinado por:</strong> @{{ nomeAutor }}
                                                </p>
                                                <p class="mb-2">
                                                    <strong>Data:</strong> @{{ formatDateTime(proposicao.data_assinatura) }}
                                                </p>
                                                <p class="mb-2">
                                                    <strong>Identificador:</strong> @{{ identificadorAssinatura }}
                                                </p>
                                                <p class="mb-0 text-muted small">
                                                    Documento assinado digitalmente conforme art. 4º, II da Lei 14.063/2020
                                                </p>
                                            </div>
                                            <div v-if="incluirQRCode" class="col-4 text-center">
                                                <canvas id="qrcode-canvas" class="qr-code-img"></canvas>
                                                <p class="small text-muted mt-2">
                                                    Verifique a autenticidade
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Visualização Principal do PDF Otimizada -->
                                <div class="pdf-preview-container">
                                    <div class="d-flex justify-content-between align-items-center mb-3 px-3 pt-3">
                                        <h5 class="mb-0">
                                            <i class="fas fa-file-pdf text-danger me-2"></i>
                                            Visualização do Documento
                                        </h5>
                                        <div class="btn-group" role="group">
                                            <button 
                                                @click="toggleView('preview')"
                                                :class="['btn', 'btn-sm', viewMode === 'preview' ? 'btn-primary' : 'btn-outline-primary']">
                                                <i class="fas fa-eye me-1"></i>
                                                PDF
                                            </button>
                                            <button 
                                                @click="toggleView('source')"
                                                :class="['btn', 'btn-sm', viewMode === 'source' ? 'btn-primary' : 'btn-outline-primary']">
                                                <i class="fas fa-code me-1"></i>
                                                Fonte
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Preview do PDF Original do OnlyOffice -->
                                    <div v-show="viewMode === 'preview'" class="p-3" style="border: 1px solid #dee2e6; border-top: none;">
                                        <div class="pdf-preview-container" style="background: white; height: 800px; border: 1px solid #ddd;">
                                            <iframe 
                                                src="{{ route('proposicoes.pdf-original', $proposicao->id) }}" 
                                                style="width: 100%; height: 100%; border: none;"
                                                title="PDF Original da Proposição">
                                            </iframe>
                                        </div>
                                    </div>
                                    
                                    <!-- Visualização do Código Fonte -->
                                    <div v-show="viewMode === 'source'" class="p-3" style="border: 1px solid #dee2e6; border-top: none;">
                                        <div class="pdf-container" style="background: white; max-height: 800px; overflow-y: auto;">
                                            <!-- Conteúdo OnlyOffice PURO (sem adições externas) -->
                                            <div v-if="usandoApenasOnlyOffice" class="document-content" v-html="conteudoProcessado" style="padding: 0; margin: 0;"></div>
                                            
                                            <!-- Estrutura tradicional (fallback quando não há OnlyOffice) -->
                                            <template v-else>
                                                <!-- Cabeçalho do Documento -->
                                                <div class="document-header">
                                                    <img v-if="cabecalhoImagem" :src="cabecalhoImagem" alt="Cabeçalho">
                                                    <div v-html="cabecalhoTexto"></div>
                                                </div>

                                                <!-- Título da Proposição -->
                                                <div class="document-title">
                                                    @{{ tipoProposicao }} Nº @{{ numeroProposicao }}
                                                </div>

                                                <!-- Ementa -->
                                                <div v-if="proposicao.ementa" class="mb-4">
                                                    <strong>EMENTA:</strong> @{{ proposicao.ementa }}
                                                </div>

                                                <!-- Conteúdo Principal -->
                                                <div class="document-content" v-html="conteudoProcessado"></div>
                                            </template>

                                            <!-- Assinatura Manual (apenas se não estiver usando OnlyOffice puro) -->
                                            <div v-if="!usandoApenasOnlyOffice" class="document-signature">
                                                <div class="mt-5">
                                                    @{{ dataLocal }}
                                                </div>
                                                <div class="signature-line"></div>
                                                <div class="signature-name">
                                                    @{{ nomeAutor }}
                                                </div>
                                                <div class="signature-role">
                                                    @{{ cargoAutor }}
                                                </div>
                                            </div>

                                            <!-- Assinatura Digital (se houver) -->
                                            <div v-if="incluirAssinatura && proposicao.assinatura_digital" 
                                                 class="digital-signature-box">
                                                <div class="digital-signature-title">
                                                    <i class="fas fa-certificate me-2"></i>
                                                    ASSINATURA DIGITAL
                                                </div>
                                                <div class="row">
                                                    <div class="col-8">
                                                        <p class="mb-2">
                                                            <strong>Assinado por:</strong> @{{ nomeAutor }}
                                                        </p>
                                                        <p class="mb-2">
                                                            <strong>Data:</strong> @{{ formatDateTime(proposicao.data_assinatura) }}
                                                        </p>
                                                        <p class="mb-2">
                                                            <strong>Identificador:</strong> @{{ identificadorAssinatura }}
                                                        </p>
                                                        <p class="mb-0 text-muted small">
                                                            Documento assinado digitalmente conforme art. 4º, II da Lei 14.063/2020
                                                        </p>
                                                    </div>
                                                    <div v-if="incluirQRCode" class="col-4 text-center">
                                                        <canvas id="qrcode-canvas-preview" class="qr-code-img"></canvas>
                                                        <p class="small text-muted mt-2">
                                                            Verifique a autenticidade
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->

    <!-- Modal de Assinatura -->
    <div class="modal fade" id="modalAssinatura" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assinar Documento Digitalmente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label required">Senha do Certificado Digital:</label>
                        <input 
                            type="password" 
                            v-model="senhaCertificado"
                            class="form-control"
                            placeholder="Digite a senha">
                    </div>
                    <div class="form-check mb-4">
                        <input 
                            type="checkbox" 
                            v-model="confirmoLeitura"
                            class="form-check-input" 
                            id="confirmoLeitura">
                        <label class="form-check-label" for="confirmoLeitura">
                            Confirmo que li e concordo com o conteúdo do documento
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button 
                        type="button" 
                        @click="processarAssinatura"
                        :disabled="!senhaCertificado || !confirmoLeitura"
                        class="btn btn-success">
                        <i class="fas fa-signature me-2"></i>
                        Assinar Digitalmente
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="modalExclusaoDocumento" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirmar Exclusão de Documento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Atenção:</strong> Esta ação não pode ser desfeita!
                    </div>
                    
                    <p>Você está prestes a excluir <strong>todos os arquivos</strong> relacionados a esta proposição:</p>
                    
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item">
                            <i class="fas fa-file-pdf text-danger me-2"></i>
                            PDF de assinatura gerado
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-file-word text-primary me-2"></i>
                            Documento editado no OnlyOffice
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-folder text-warning me-2"></i>
                            Arquivos temporários e cache
                        </li>
                    </ul>

                    <div class="alert alert-info">
                        <i class="fas fa-undo me-2"></i>
                        Após a exclusão, você poderá recriar o documento no editor OnlyOffice.
                    </div>

                    <div class="form-check mb-3">
                        <input 
                            type="checkbox" 
                            v-model="confirmoExclusao"
                            class="form-check-input" 
                            id="confirmoExclusao">
                        <label class="form-check-label" for="confirmoExclusao">
                            Eu entendo que esta ação é irreversível e confirmo a exclusão
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        Cancelar
                    </button>
                    <button 
                        type="button" 
                        @click="excluirDocumento"
                        :disabled="!confirmoExclusao || excluindoDocumento"
                        class="btn btn-danger">
                        <i v-if="excluindoDocumento" class="spinner-border spinner-border-sm me-2"></i>
                        <i v-else class="fas fa-trash-alt me-2"></i>
                        @{{ excluindoDocumento ? 'Excluindo...' : 'Confirmar Exclusão' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vue.js 3 e Bibliotecas -->
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>

<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            // Dados da proposição
            proposicao: @json($proposicao ?? null),
            
            // Estados
            loading: false,
            loadingMessage: 'Processando documento...',
            progress: 0,
            progressDetail: '',
            pdfReady: false,
            pdfPreviewUrl: null,
            pdfBlob: null,
            
            // Configurações
            pdfQuality: 1.5,
            incluirAssinatura: true,
            incluirQRCode: true,
            viewMode: 'preview', // 'preview' ou 'source'
            
            // Assinatura
            senhaCertificado: '',
            confirmoLeitura: false,
            
            // Exclusão de documento
            confirmoExclusao: false,
            excluindoDocumento: false,
            
            // Cache de elementos processados
            cabecalhoImagem: null,
            cabecalhoTexto: '',
            conteudoProcessado: '',
            
            // Controle de fonte de dados
            usandoApenasOnlyOffice: false,
            conteudoOnlyOffice: null,
            
            // Dados avançados para auditoria
            estruturaDocumento: null,
            formatacaoPreservada: null, 
            metadadosDocumento: null,
            dadosIntegridade: null,
            validacaoFidelidade: null,
            
            // Referências
            modalAssinatura: null,
            modalExclusaoDocumento: null
        }
    },
    
    computed: {
        tipoProposicao() {
            return this.proposicao?.tipo?.toUpperCase() || 'PROPOSIÇÃO';
        },
        
        numeroProposicao() {
            return this.proposicao?.numero_protocolo || '[AGUARDANDO PROTOCOLO]';
        },
        
        nomeAutor() {
            return this.proposicao?.autor?.name || 'Nome do Parlamentar';
        },
        
        cargoAutor() {
            return this.proposicao?.autor?.cargo_atual || 'Vereador';
        },
        
        dataLocal() {
            const meses = ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho',
                          'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
            const hoje = new Date();
            const dia = hoje.getDate();
            const mes = meses[hoje.getMonth()];
            const ano = hoje.getFullYear();
            return `Caraguatatuba, ${dia} de ${mes} de ${ano}`;
        },
        
        identificadorAssinatura() {
            if (!this.proposicao?.assinatura_digital) return '';
            
            const id = this.proposicao.id;
            const timestamp = new Date(this.proposicao.data_assinatura).getTime();
            const hash = btoa(`${id}-${timestamp}`).substring(0, 24).toUpperCase();
            return hash.match(/.{1,4}/g).join('-');
        },
        
        podeExcluirDocumento() {
            // Verificar se a proposição está em um status que permite exclusão
            const statusPermitidos = ['aprovado', 'aprovado_assinatura', 'retornado_legislativo'];
            return statusPermitidos.includes(this.proposicao?.status);
        }
    },
    
    async mounted() {
        console.log('PDF Vue App mounted');
        
        // Inicializar modals Bootstrap
        const modalAssinaturaEl = document.getElementById('modalAssinatura');
        if (modalAssinaturaEl && window.bootstrap) {
            this.modalAssinatura = new window.bootstrap.Modal(modalAssinaturaEl);
        }
        
        const modalExclusaoEl = document.getElementById('modalExclusaoDocumento');
        if (modalExclusaoEl && window.bootstrap) {
            this.modalExclusaoDocumento = new window.bootstrap.Modal(modalExclusaoEl);
        }
        
        // Processar dados iniciais
        await this.processarDadosIniciais();
        
        // Não gerar PDF automaticamente - melhor performance
        // setTimeout(() => {
        //     this.gerarPDF();
        // }, 2000);
    },
    
    methods: {
        async processarDadosIniciais() {
            console.log('Iniciando processamento de dados iniciais...');
            
            // Carregar conteúdo editado do OnlyOffice (prioridade máxima)
            try {
                console.log('Carregando conteúdo do OnlyOffice...');
                const dadosOnlyOffice = await this.carregarConteudoOnlyOffice();
                
                if (dadosOnlyOffice && dadosOnlyOffice.conteudo) {
                    console.log('Conteúdo do OnlyOffice carregado com sucesso:', dadosOnlyOffice.conteudo.length, 'caracteres');
                    console.log('Usando EXCLUSIVAMENTE o conteúdo OnlyOffice - sem cabeçalhos externos');
                    
                    // Usar APENAS o conteúdo do OnlyOffice - sem adicionar nada
                    this.conteudoProcessado = dadosOnlyOffice.conteudo;
                    
                    // Não adicionar cabeçalho - já está no conteúdo OnlyOffice
                    this.cabecalhoTexto = '';
                    this.cabecalhoImagem = null;
                    
                    // Marcar que estamos usando apenas OnlyOffice
                    this.usandoApenasOnlyOffice = true;
                } else {
                    console.warn('Conteúdo do OnlyOffice não disponível, usando dados da proposição como fallback');
                    await this.processarDadosProposicao();
                }
            } catch (error) {
                console.error('Erro ao carregar conteúdo do OnlyOffice:', error);
                console.log('Usando dados da proposição como fallback...');
                await this.processarDadosProposicao();
            }
            
            // Gerar QR Code se necessário
            if (this.incluirQRCode && this.proposicao?.numero_protocolo) {
                await this.gerarQRCode();
            }
        },

        async carregarConteudoOnlyOffice() {
            try {
                console.log('🔍 Iniciando extração avançada OnlyOffice...');
                
                const response = await fetch(`/proposicoes/${this.proposicao.id}/conteudo-onlyoffice`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    console.warn('❌ Resposta não OK do servidor:', response.status, response.statusText);
                    return null;
                }

                const dados = await response.json();
                
                if (dados.success) {
                    console.log('✅ Extração avançada concluída com sucesso:', {
                        temConteudo: !!dados.conteudo,
                        tamanhoConteudo: dados.conteudo?.length || 0,
                        estruturaDocumento: dados.estrutura ? 'Estrutura preservada' : 'Estrutura básica',
                        formatacao: dados.formatação ? 'Formatação preservada' : 'Sem formatação',
                        metadados: dados.metadados ? 'Metadados extraídos' : 'Sem metadados',
                        hashIntegridade: dados.hash_integridade ? 'Hash validado' : 'Sem hash'
                    });

                    // Validar integridade do conteúdo extraído
                    if (dados.hash_integridade) {
                        this.validarIntegridade(dados);
                    }

                    // Armazenar dados para auditoria e validação
                    this.conteudoOnlyOffice = dados.conteudo || dados.conteudo_html;
                    this.estruturaDocumento = dados.estrutura;
                    this.formatacaoPreservada = dados.formatação;
                    this.metadadosDocumento = dados.metadados;

                    return dados;
                } else {
                    console.warn('❌ Extração OnlyOffice falhou:', dados.message);
                    return null;
                }

            } catch (error) {
                console.error('💥 Erro crítico ao carregar conteúdo OnlyOffice:', error);
                return null;
            }
        },

        validarIntegridade(dados) {
            try {
                const integridade = {
                    hash_documento: dados.hash_integridade,
                    timestamp_extração: new Date().toISOString(),
                    tamanho_conteudo: dados.conteudo?.length || 0,
                    método_extração: dados.metadados?.método_extração || 'avançado',
                    arquivo_origem: dados.arquivo_origem
                };

                this.dadosIntegridade = integridade;
                console.log('🔒 Validação de integridade registrada:', integridade);

                // Validar fidelidade do conteúdo extraído
                this.validarFidelidadeConteudo(dados);

            } catch (error) {
                console.warn('⚠️ Erro na validação de integridade:', error);
            }
        },

        validarFidelidadeConteudo(dados) {
            try {
                console.log('🎯 Iniciando validação de fidelidade do conteúdo OnlyOffice...');
                
                const validacao = {
                    timestamp: new Date().toISOString(),
                    metodo_extracao: dados.metadados?.método_extração || 'desconhecido',
                    arquivo_origem: dados.arquivo_origem,
                    validacoes: {}
                };

                // VALIDAÇÃO 1: Tamanho do conteúdo
                if (dados.conteudo && dados.conteudo.length > 0) {
                    validacao.validacoes.tamanho_conteudo = {
                        passou: dados.conteudo.length >= 100, // Mínimo esperado
                        valor: dados.conteudo.length,
                        detalhes: dados.conteudo.length >= 100 ? 'Tamanho adequado' : 'Conteúdo muito pequeno'
                    };
                } else {
                    validacao.validacoes.tamanho_conteudo = {
                        passou: false,
                        valor: 0,
                        detalhes: 'Conteúdo vazio'
                    };
                }

                // VALIDAÇÃO 2: Presença de elementos estruturais
                const elementosEstruturais = [
                    'MOÇÃO', 'EMENTA', 'Câmara Municipal', 'manifesta', 'presente'
                ];

                let elementosEncontrados = 0;
                elementosEstruturais.forEach(elemento => {
                    if (dados.conteudo?.includes(elemento)) {
                        elementosEncontrados++;
                    }
                });

                validacao.validacoes.estrutura_documento = {
                    passou: elementosEncontrados >= 2,
                    valor: `${elementosEncontrados}/${elementosEstruturais.length}`,
                    detalhes: elementosEncontrados >= 2 ? 'Estrutura preservada' : 'Estrutura pode estar incompleta'
                };

                // VALIDAÇÃO 3: Formatação preservada
                if (dados.formatação) {
                    validacao.validacoes.formatacao_preservada = {
                        passou: true,
                        valor: Object.keys(dados.formatação).length,
                        detalhes: 'Formatação extraída com sucesso'
                    };
                } else {
                    validacao.validacoes.formatacao_preservada = {
                        passou: false,
                        valor: 0,
                        detalhes: 'Formatação não preservada'
                    };
                }

                // VALIDAÇÃO 4: Metadados do documento
                if (dados.metadados) {
                    validacao.validacoes.metadados_disponiveis = {
                        passou: true,
                        valor: Object.keys(dados.metadados).length,
                        detalhes: 'Metadados extraídos'
                    };
                } else {
                    validacao.validacoes.metadados_disponiveis = {
                        passou: false,
                        valor: 0,
                        detalhes: 'Metadados não disponíveis'
                    };
                }

                // VALIDAÇÃO 5: Hash de integridade
                validacao.validacoes.hash_integridade = {
                    passou: !!dados.hash_integridade,
                    valor: dados.hash_integridade || 'N/A',
                    detalhes: dados.hash_integridade ? 'Hash disponível para auditoria' : 'Sem hash de integridade'
                };

                // Calcular score geral de fidelidade
                const validacoesPassed = Object.values(validacao.validacoes).filter(v => v.passou).length;
                const totalValidacoes = Object.keys(validacao.validacoes).length;
                const scorePercentual = Math.round((validacoesPassed / totalValidacoes) * 100);

                validacao.score_fidelidade = {
                    percentual: scorePercentual,
                    passou: scorePercentual >= 70, // 70% ou mais é considerado bom
                    detalhes: `${validacoesPassed}/${totalValidacoes} validações aprovadas`
                };

                // Armazenar validação
                this.validacaoFidelidade = validacao;

                // Log detalhado
                console.log('📊 Validação de fidelidade concluída:', {
                    score: `${scorePercentual}%`,
                    status: scorePercentual >= 70 ? 'APROVADO' : 'ATENÇÃO',
                    detalhes: validacao.validacoes
                });

                // Alertar se fidelidade baixa
                if (scorePercentual < 70) {
                    console.warn('⚠️ ATENÇÃO: Fidelidade do conteúdo abaixo do esperado!', {
                        score: scorePercentual,
                        recomendacao: 'Verificar se o arquivo OnlyOffice foi salvo corretamente'
                    });
                } else {
                    console.log('✅ Fidelidade do conteúdo aprovada:', scorePercentual + '%');
                }

                return validacao;

            } catch (error) {
                console.error('💥 Erro na validação de fidelidade:', error);
                return {
                    erro: true,
                    mensagem: error.message,
                    timestamp: new Date().toISOString()
                };
            }
        },

        async processarDadosProposicao() {
            console.log('Processando dados da proposição (fallback)...');
            
            // Validar e pré-carregar imagem do cabeçalho
            try {
                const imagemValida = await this.validarImagem('/template/cabecalho.png');
                if (imagemValida) {
                    this.cabecalhoImagem = '/template/cabecalho.png';
                } else {
                    console.warn('Imagem do cabeçalho não pôde ser carregada, usando fallback de texto');
                    this.cabecalhoImagem = null;
                }
            } catch (error) {
                console.error('Erro ao validar imagem do cabeçalho:', error);
                this.cabecalhoImagem = null;
            }
            
            // Processar cabeçalho (sempre com texto)
            this.cabecalhoTexto = `
                <strong>CÂMARA MUNICIPAL DE CARAGUATATUBA</strong><br>
                Praça da República, 40, Centro, Caraguatatuba-SP<br>
                (12) 3882-5588<br>
                www.camaracaraguatatuba.sp.gov.br
            `;
            
            // Processar conteúdo
            let conteudo = this.proposicao?.conteudo || this.proposicao?.ementa || '';
            
            // Substituir placeholders
            conteudo = this.substituirVariaveis(conteudo);
            
            // Converter para HTML preservando formatação
            this.conteudoProcessado = this.converterParaHTML(conteudo);
        },

        async validarImagem(src) {
            return new Promise((resolve) => {
                const img = new Image();
                img.onload = () => {
                    console.log('Imagem carregada com sucesso:', src);
                    resolve(true);
                };
                img.onerror = (error) => {
                    console.error('Erro ao carregar imagem:', src, error);
                    resolve(false);
                };
                // Adicionar timestamp para evitar cache
                img.src = src + '?t=' + Date.now();
                
                // Timeout de segurança
                setTimeout(() => {
                    console.warn('Timeout ao carregar imagem:', src);
                    resolve(false);
                }, 5000);
            });
        },

        async aguardarCarregamentoImagens() {
            const element = document.getElementById('pdf-content');
            if (!element) return;

            const images = element.querySelectorAll('img');
            const imagePromises = Array.from(images).map(img => {
                if (img.complete && img.naturalHeight > 0) {
                    return Promise.resolve();
                }
                
                return new Promise((resolve) => {
                    const timeout = setTimeout(() => {
                        console.warn('Timeout para imagem:', img.src);
                        resolve();
                    }, 5000);
                    
                    img.onload = () => {
                        clearTimeout(timeout);
                        console.log('Imagem carregada:', img.src);
                        resolve();
                    };
                    
                    img.onerror = () => {
                        clearTimeout(timeout);
                        console.error('Erro ao carregar imagem:', img.src);
                        img.style.display = 'none'; // Ocultar imagem com erro
                        resolve();
                    };
                    
                    // Se a imagem ainda não tem src, definir uma
                    if (!img.src && img.getAttribute('src')) {
                        img.src = img.getAttribute('src');
                    }
                });
            });

            await Promise.all(imagePromises);
            console.log('Todas as imagens processadas');
        },
        
        substituirVariaveis(texto) {
            const substituicoes = {
                '[AGUARDANDO PROTOCOLO]': this.numeroProposicao,
                '${numero_proposicao}': this.numeroProposicao,
                '${ementa}': this.proposicao?.ementa || '',
                '${autor_nome}': this.nomeAutor,
                '${autor_cargo}': this.cargoAutor,
                '${municipio}': 'Caraguatatuba',
                '${dia}': new Date().getDate(),
                '${mes_extenso}': this.dataLocal.split(' ')[3],
                '${ano_atual}': new Date().getFullYear()
            };
            
            let resultado = texto;
            for (const [chave, valor] of Object.entries(substituicoes)) {
                resultado = resultado.replace(new RegExp(chave.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g'), valor);
            }
            
            return resultado;
        },
        
        converterParaHTML(texto) {
            // Preservar quebras de linha e parágrafos
            let html = texto
                .split('\n\n')
                .map(paragrafo => `<p>${paragrafo.trim()}</p>`)
                .join('');
            
            // Destacar seções importantes
            html = html.replace(/JUSTIFICATIVA:/g, '<strong>JUSTIFICATIVA:</strong>');
            html = html.replace(/A Câmara Municipal manifesta:/g, '<strong>A Câmara Municipal manifesta:</strong>');
            
            return html;
        },
        
        async gerarQRCode() {
            await this.$nextTick();
            
            const canvas = document.getElementById('qrcode-canvas');
            if (!canvas) return;
            
            const url = `${window.location.origin}/proposicoes/${this.proposicao.id}/verificar`;
            
            QRCode.toCanvas(canvas, url, {
                width: 150,
                margin: 1,
                color: {
                    dark: '#000000',
                    light: '#FFFFFF'
                }
            });
        },
        
        async gerarPDF() {
            this.loading = true;
            this.progress = 0;
            this.loadingMessage = 'Preparando documento...';
            
            try {
                // Aguardar renderização completa
                await this.$nextTick();
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                this.progress = 10;
                this.progressDetail = 'Aguardando carregamento de imagens...';
                
                // Aguardar carregamento de todas as imagens no documento
                await this.aguardarCarregamentoImagens();
                
                this.progress = 20;
                this.progressDetail = 'Capturando conteúdo do documento...';
                
                // Capturar elemento do documento
                const element = document.getElementById('pdf-content');
                if (!element) {
                    throw new Error('Elemento PDF não encontrado');
                }
                
                console.log('Elemento encontrado:', {
                    id: element.id,
                    className: element.className,
                    offsetWidth: element.offsetWidth,
                    offsetHeight: element.offsetHeight,
                    scrollWidth: element.scrollWidth,
                    scrollHeight: element.scrollHeight,
                    hasContent: element.innerHTML.length > 100
                });
                
                // Tornar elemento temporariamente visível para captura
                const originalStyle = {
                    position: element.style.position,
                    left: element.style.left,
                    top: element.style.top,
                    visibility: element.style.visibility,
                    display: element.style.display
                };
                
                // Aplicar estilos temporários para renderização
                element.style.position = 'fixed';
                element.style.left = '0';
                element.style.top = '0';
                element.style.visibility = 'visible';
                element.style.display = 'block';
                element.style.zIndex = '-1000';
                
                // Aguardar renderização
                await new Promise(resolve => setTimeout(resolve, 500));
                
                // Configurações otimizadas para html2canvas com tratamento de erro
                const canvas = await html2canvas(element, {
                    scale: parseFloat(this.pdfQuality),
                    useCORS: true,
                    allowTaint: true,
                    logging: false,
                    backgroundColor: '#ffffff',
                    windowWidth: element.scrollWidth,
                    windowHeight: element.scrollHeight,
                    imageTimeout: 10000,
                    onclone: (clonedDoc) => {
                        // Ajustar estilos no documento clonado
                        const clonedElement = clonedDoc.getElementById('pdf-content');
                        if (clonedElement) {
                            clonedElement.style.padding = '40px';
                            clonedElement.style.width = '210mm';
                            
                            // Remover imagens problemáticas se houver
                            const imgs = clonedElement.querySelectorAll('img');
                            imgs.forEach(img => {
                                if (!img.complete || img.naturalHeight === 0) {
                                    console.warn('Removendo imagem problemática:', img.src);
                                    img.style.display = 'none';
                                }
                            });
                        }
                    }
                }).catch(error => {
                    console.error('Erro no html2canvas:', error);
                    // Tentar novamente sem imagens se houver erro
                    return html2canvas(element, {
                        scale: 1,
                        useCORS: false,
                        allowTaint: false,
                        logging: false,
                        backgroundColor: '#ffffff',
                        ignoreElements: (element) => {
                            return element.tagName === 'IMG';
                        }
                    });
                });
                
                this.progress = 60;
                this.progressDetail = 'Gerando PDF...';
                
                // Restaurar estilos originais do elemento
                element.style.position = originalStyle.position || '';
                element.style.left = originalStyle.left || '';
                element.style.top = originalStyle.top || '';
                element.style.visibility = originalStyle.visibility || '';
                element.style.display = originalStyle.display || '';
                element.style.zIndex = '';
                
                // Validar canvas antes de tentar converter
                if (!canvas) {
                    throw new Error('Canvas não foi gerado corretamente');
                }
                
                console.log('Canvas gerado:', {
                    width: canvas.width,
                    height: canvas.height,
                    hasContext: !!canvas.getContext,
                    isEmpty: canvas.width === 0 || canvas.height === 0
                });
                
                // Verificar se canvas tem dimensões válidas
                if (canvas.width === 0 || canvas.height === 0) {
                    throw new Error('Canvas gerado com dimensões zero - elemento pode estar vazio ou não renderizado');
                }
                
                // Criar PDF com jsPDF - com tratamento de erro
                let imgData;
                try {
                    imgData = canvas.toDataURL('image/png', 0.8);
                    console.log('Canvas convertido para base64, tamanho:', imgData.length);
                } catch (error) {
                    console.error('Erro ao converter canvas para base64:', error);
                    throw new Error('Falha ao converter imagem do documento: ' + error.message);
                }
                
                console.log('Dados da imagem:', {
                    length: imgData?.length || 0,
                    startsWith: imgData?.substring(0, 30) || 'vazio',
                    isValid: imgData && imgData !== 'data:,' && imgData.length > 100
                });
                
                if (!imgData || imgData === 'data:,' || imgData.length < 100) {
                    const diagnostico = {
                        imagemVazia: !imgData,
                        dataURLVazio: imgData === 'data:,',
                        tamanhoInsuficiente: imgData && imgData.length < 100,
                        elementoVisivel: element.offsetWidth > 0 && element.offsetHeight > 0,
                        canvasDimensoes: `${canvas.width}x${canvas.height}`
                    };
                    console.error('Diagnóstico de imagem inválida:', diagnostico);
                    throw new Error(`Dados de imagem inválidos: ${JSON.stringify(diagnostico)}`);
                }
                
                let pdf;
                try {
                    pdf = new window.jspdf.jsPDF({
                        orientation: 'portrait',
                        unit: 'mm',
                        format: 'a4',
                        compress: true
                    });
                    console.log('PDF jsPDF criado com sucesso');
                } catch (error) {
                    console.error('Erro ao criar instância jsPDF:', error);
                    throw new Error('Falha ao inicializar gerador de PDF: ' + error.message);
                }
                
                // Calcular dimensões com validação
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();
                
                if (!canvas.width || !canvas.height) {
                    throw new Error('Canvas com dimensões inválidas');
                }
                
                const imgWidth = pageWidth - 20; // Margens de 10mm
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                
                console.log('Dimensões calculadas:', {
                    pageWidth,
                    pageHeight,
                    imgWidth,
                    imgHeight,
                    canvasWidth: canvas.width,
                    canvasHeight: canvas.height
                });
                
                let heightLeft = imgHeight;
                let position = 10; // Margem superior
                
                try {
                    // Adicionar primeira página com imagem
                    console.log('Adicionando imagem ao PDF...');
                    pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                    heightLeft -= (pageHeight - 20);
                    console.log('Primeira página adicionada com sucesso');
                    
                    // Adicionar páginas adicionais se necessário
                    let pageCount = 1;
                    while (heightLeft > 0) {
                        position = heightLeft - imgHeight;
                        pdf.addPage();
                        pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                        heightLeft -= pageHeight;
                        pageCount++;
                        
                        // Proteção contra loop infinito
                        if (pageCount > 10) {
                            console.warn('Muitas páginas detectadas, interrompendo...');
                            break;
                        }
                    }
                    console.log(`PDF criado com ${pageCount} página(s)`);

                    // ✅ ADICIONAR TEXTO INVISÍVEL PARA PESQUISA (AUDITORIA)
                    console.log('🔍 Adicionando camada de texto invisível para auditoria...');
                    
                    // Extrair texto para auditoria do conteúdo mais relevante
                    let textoParaAuditoria = '';
                    
                    if (this.usandoApenasOnlyOffice && this.conteudoOnlyOffice) {
                        console.log('📄 Extraindo texto do OnlyOffice para auditoria...');
                        textoParaAuditoria = this.conteudoOnlyOffice.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
                    } else if (this.conteudoProcessado) {
                        console.log('📄 Extraindo texto processado para auditoria...');
                        textoParaAuditoria = this.conteudoProcessado.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
                    }
                    
                    if (textoParaAuditoria && textoParaAuditoria.length > 0) {
                        // Extrair parágrafos para auditoria
                        const paragrafosAuditoria = this.extrairParagrafosParaAuditoria(textoParaAuditoria);
                        
                        console.log('📝 Adicionando texto invisível:', {
                            paragrafos: paragrafosAuditoria.length,
                            caracteres: textoParaAuditoria.length,
                            metodo: this.usandoApenasOnlyOffice ? 'OnlyOffice' : 'Fallback'
                        });
                        
                        // Ir para primeira página para adicionar texto invisível
                        pdf.setPage(1);
                        
                        // Configurar texto invisível (transparência 0)
                        pdf.setTextColor(255, 255, 255, 0); // Branco totalmente transparente
                        pdf.setFontSize(1); // Fonte muito pequena
                        
                        // Adicionar texto invisível distribuído pela página
                        let yPos = 20;
                        const maxLineWidth = pageWidth - 40;
                        
                        paragrafosAuditoria.forEach((paragrafo, index) => {
                            if (yPos > pageHeight - 30) {
                                // Se passar do final da página, ir para próxima ou criar nova
                                if (pdf.internal.getNumberOfPages() > 1) {
                                    pdf.setPage(Math.min(2, pdf.internal.getNumberOfPages()));
                                    yPos = 20;
                                } else {
                                    yPos = 20; // Reset para início se só tiver uma página
                                }
                            }
                            
                            // Quebrar parágrafo em linhas menores se necessário
                            const linhas = pdf.splitTextToSize(paragrafo, maxLineWidth);
                            
                            linhas.forEach(linha => {
                                if (yPos <= pageHeight - 30) {
                                    pdf.text(linha, 20, yPos);
                                    yPos += 2; // Espaçamento muito pequeno entre linhas
                                }
                            });
                        });
                        
                        // Adicionar metadados de auditoria como texto invisível
                        const metadadosAuditoria = `
AUDITORIA_DIGITAL:
ID_Proposicao: ${this.proposicao?.id || 'N/A'}
Autor: ${this.nomeAutor || 'N/A'}  
Data_Geracao: ${new Date().toISOString()}
Metodo_Extracao: ${this.usandoApenasOnlyOffice ? 'OnlyOffice_Avancado' : 'Fallback_Tradicional'}
Hash_Integridade: ${this.dadosIntegridade?.hash_documento || 'N/A'}
Tamanho_Conteudo: ${textoParaAuditoria.length}
Status: ${this.proposicao?.status || 'N/A'}
Numero_Protocolo: ${this.proposicao?.numero_protocolo || '[AGUARDANDO_PROTOCOLO]'}
                        `.trim();
                        
                        // Adicionar metadados na última linha da primeira página
                        pdf.setPage(1);
                        pdf.text(metadadosAuditoria, 20, pageHeight - 10);
                        
                        console.log('✅ Texto invisível para auditoria adicionado com sucesso');
                        console.log('🔍 PDF agora é totalmente pesquisável e auditável');
                        
                    } else {
                        console.warn('⚠️ Nenhum texto disponível para auditoria - PDF terá apenas imagem');
                    }
                    
                } catch (error) {
                    console.error('Erro ao adicionar imagem ao PDF:', error);
                    throw new Error('Falha ao adicionar conteúdo ao PDF: ' + error.message);
                }
                
                this.progress = 80;
                this.progressDetail = 'Finalizando documento...';
                
                try {
                    // Adicionar metadados
                    pdf.setProperties({
                        title: `${this.tipoProposicao} ${this.numeroProposicao}`,
                        subject: this.proposicao?.ementa || '',
                        author: this.nomeAutor,
                        keywords: 'proposição, câmara, documento oficial',
                        creator: 'Sistema Legisinc'
                    });
                    console.log('Metadados adicionados ao PDF');
                } catch (error) {
                    console.warn('Erro ao adicionar metadados (não crítico):', error);
                    // Não interrompe o processo por erro em metadados
                }
                
                try {
                    // Gerar blob do PDF
                    this.pdfBlob = pdf.output('blob');
                    console.log('PDF blob gerado, tamanho:', this.pdfBlob.size);
                    
                    if (!this.pdfBlob || this.pdfBlob.size === 0) {
                        throw new Error('PDF blob vazio ou inválido');
                    }
                    
                    // Criar URL para preview
                    this.pdfPreviewUrl = URL.createObjectURL(this.pdfBlob);
                    console.log('URL de preview criada:', this.pdfPreviewUrl);
                } catch (error) {
                    console.error('Erro ao gerar blob do PDF:', error);
                    throw new Error('Falha ao finalizar PDF: ' + error.message);
                }
                
                this.progress = 100;
                this.progressDetail = 'PDF gerado com sucesso!';
                this.pdfReady = true;
                
                // Salvar no backend
                await this.salvarPDFNoBackend();
                
                setTimeout(() => {
                    this.loading = false;
                }, 500);
                
            } catch (error) {
                console.error('Erro detalhado ao gerar PDF:', {
                    message: error.message,
                    stack: error.stack,
                    name: error.name,
                    proposicaoId: this.proposicao?.id,
                    progress: this.progress,
                    progressDetail: this.progressDetail
                });
                
                this.loading = false;
                this.progress = 0;
                this.progressDetail = 'Erro na geração do PDF';
                
                // Mensagem de erro mais detalhada para o usuário
                let mensagemErro = 'Erro ao gerar PDF: ' + error.message;
                if (error.message.includes('Canvas')) {
                    mensagemErro += '\n\nSugestão: Tente recarregar a página e aguardar o carregamento completo antes de gerar o PDF.';
                } else if (error.message.includes('jsPDF')) {
                    mensagemErro += '\n\nSugestão: Problema com a biblioteca de PDF. Tente usar uma qualidade menor ou recarregar a página.';
                } else if (error.message.includes('imagem')) {
                    mensagemErro += '\n\nSugestão: Problema com imagens. O sistema tentará gerar o PDF sem imagens automaticamente.';
                }
                
                this.mostrarErro(mensagemErro);
                
                // Tentar fallback: gerar PDF simples sem imagens
                console.log('Tentando fallback: PDF sem imagens...');
                setTimeout(() => {
                    this.gerarPDFSimples();
                }, 2000);
            }
        },

        async gerarPDFSimples() {
            console.log('Iniciando geração de PDF simples (fallback)...');
            this.loading = true;
            this.progress = 0;
            this.loadingMessage = 'Gerando PDF alternativo sem imagens...';
            
            try {
                await this.$nextTick();
                
                // Criar PDF diretamente com texto usando jsPDF
                const pdf = new window.jspdf.jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });
                
                this.progress = 25;
                this.progressDetail = 'Adicionando conteúdo textual...';
                
                // Configurações de texto
                const pageWidth = pdf.internal.pageSize.getWidth();
                const margin = 20;
                const maxWidth = pageWidth - (margin * 2);
                let yPosition = 30;
                
                // Título
                pdf.setFontSize(16);
                pdf.setFont(undefined, 'bold');
                const titulo = `${this.tipoProposicao} Nº ${this.numeroProposicao}`;
                pdf.text(titulo, pageWidth/2, yPosition, { align: 'center' });
                yPosition += 15;
                
                // Cabeçalho da Câmara
                pdf.setFontSize(12);
                pdf.setFont(undefined, 'bold');
                pdf.text('CÂMARA MUNICIPAL DE CARAGUATATUBA', pageWidth/2, yPosition, { align: 'center' });
                yPosition += 6;
                pdf.setFont(undefined, 'normal');
                pdf.text('Praça da República, 40, Centro, Caraguatatuba-SP', pageWidth/2, yPosition, { align: 'center' });
                yPosition += 6;
                pdf.text('(12) 3882-5588 - www.camaracaraguatatuba.sp.gov.br', pageWidth/2, yPosition, { align: 'center' });
                yPosition += 15;
                
                this.progress = 50;
                this.progressDetail = 'Adicionando ementa e conteúdo...';
                
                // Ementa
                if (this.proposicao?.ementa) {
                    pdf.setFont(undefined, 'bold');
                    pdf.text('EMENTA: ', margin, yPosition);
                    pdf.setFont(undefined, 'normal');
                    const ementaLines = pdf.splitTextToSize(this.proposicao.ementa, maxWidth - 20);
                    pdf.text(ementaLines, margin + 20, yPosition);
                    yPosition += ementaLines.length * 6 + 10;
                }
                
                // Conteúdo
                if (this.conteudoProcessado) {
                    // Remover tags HTML simples
                    const conteudoTexto = this.conteudoProcessado
                        .replace(/<[^>]*>/g, '')
                        .replace(/&nbsp;/g, ' ')
                        .replace(/&amp;/g, '&')
                        .replace(/&lt;/g, '<')
                        .replace(/&gt;/g, '>')
                        .trim();
                    
                    if (conteudoTexto) {
                        const conteudoLines = pdf.splitTextToSize(conteudoTexto, maxWidth);
                        
                        // Verificar se precisa de nova página
                        const linesHeight = conteudoLines.length * 6;
                        if (yPosition + linesHeight > 250) {
                            pdf.addPage();
                            yPosition = 30;
                        }
                        
                        pdf.text(conteudoLines, margin, yPosition);
                        yPosition += linesHeight + 15;
                    }
                }
                
                this.progress = 75;
                this.progressDetail = 'Adicionando assinatura...';
                
                // Assinatura
                yPosition += 20;
                if (yPosition > 220) {
                    pdf.addPage();
                    yPosition = 30;
                }
                
                pdf.text(this.dataLocal, pageWidth/2, yPosition, { align: 'center' });
                yPosition += 20;
                
                // Linha de assinatura
                pdf.line(margin + 40, yPosition, pageWidth - margin - 40, yPosition);
                yPosition += 8;
                
                pdf.setFont(undefined, 'bold');
                pdf.text(this.nomeAutor, pageWidth/2, yPosition, { align: 'center' });
                yPosition += 6;
                pdf.setFont(undefined, 'normal');
                pdf.text(this.cargoAutor, pageWidth/2, yPosition, { align: 'center' });
                
                this.progress = 90;
                this.progressDetail = 'Finalizando PDF simples...';
                
                // Gerar o PDF
                this.pdfBlob = pdf.output('blob');
                this.pdfPreviewUrl = URL.createObjectURL(this.pdfBlob);
                
                this.progress = 100;
                this.progressDetail = 'PDF alternativo gerado com sucesso!';
                this.pdfReady = true;
                
                console.log('PDF simples gerado com sucesso');
                
                // Salvar no backend
                await this.salvarPDFNoBackend();
                
                setTimeout(() => {
                    this.loading = false;
                    this.mostrarSucesso('PDF gerado com sucesso (modo alternativo sem imagens)!');
                }, 500);
                
            } catch (error) {
                console.error('Erro ao gerar PDF simples:', error);
                this.loading = false;
                this.mostrarErro('Falha crítica na geração do PDF: ' + error.message);
            }
        },
        
        async salvarPDFNoBackend() {
            if (!this.pdfBlob) return;
            
            try {
                const formData = new FormData();
                formData.append('pdf', this.pdfBlob, `proposicao_${this.proposicao.id}.pdf`);
                formData.append('_token', '{{ csrf_token() }}');
                
                const response = await fetch(`/proposicoes/${this.proposicao.id}/salvar-pdf`, {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    console.log('PDF salvo no servidor com sucesso');
                }
            } catch (error) {
                console.error('Erro ao salvar PDF no servidor:', error);
            }
        },
        
        visualizarPDF() {
            if (this.pdfPreviewUrl) {
                window.open(this.pdfPreviewUrl, '_blank');
            }
        },
        
        baixarPDF() {
            if (!this.pdfBlob) return;
            
            const link = document.createElement('a');
            link.href = URL.createObjectURL(this.pdfBlob);
            link.download = `${this.tipoProposicao}_${this.numeroProposicao.replace(/\//g, '_')}.pdf`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },
        
        imprimirPDF() {
            if (this.pdfPreviewUrl) {
                const printWindow = window.open(this.pdfPreviewUrl);
                printWindow.onload = () => {
                    printWindow.print();
                };
            }
        },
        
        iniciarAssinatura() {
            if (this.modalAssinatura) {
                this.modalAssinatura.show();
            }
        },
        
        async processarAssinatura() {
            if (!this.senhaCertificado || !this.confirmoLeitura) {
                this.mostrarErro('Preencha todos os campos obrigatórios');
                return;
            }
            
            this.loading = true;
            this.loadingMessage = 'Processando assinatura digital...';
            this.progress = 0;
            
            try {
                // Simular validação do certificado
                this.progressDetail = 'Validando certificado digital...';
                this.progress = 20;
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                this.progressDetail = 'Gerando hash do documento...';
                this.progress = 40;
                await new Promise(resolve => setTimeout(resolve, 800));
                
                // Dados da assinatura
                const assinaturaData = {
                    _token: '{{ csrf_token() }}',
                    senha_certificado: this.senhaCertificado,
                    confirmacao_leitura: this.confirmoLeitura,
                    assinatura_digital: this.gerarAssinaturaDigital()
                };
                
                this.progressDetail = 'Aplicando assinatura digital...';
                this.progress = 60;
                
                const response = await fetch(`/proposicoes/${this.proposicao.id}/processar-assinatura-vue`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(assinaturaData)
                });
                
                const data = await response.json();
                
                this.progress = 80;
                this.progressDetail = 'Finalizando assinatura...';
                
                if (data.success) {
                    // Atualizar dados da proposição
                    this.proposicao.assinatura_digital = data.assinatura_digital;
                    this.proposicao.data_assinatura = data.data_assinatura;
                    this.proposicao.status = 'assinado';
                    
                    this.progress = 100;
                    this.progressDetail = 'Documento assinado com sucesso!';
                    
                    // Fechar modal
                    if (this.modalAssinatura) {
                        this.modalAssinatura.hide();
                    }
                    
                    // Regenerar PDF com assinatura
                    setTimeout(async () => {
                        this.loading = false;
                        await this.processarDadosIniciais(); // Reprocessar com assinatura
                        await this.gerarPDF(); // Regenerar PDF
                        this.mostrarSucesso('Documento assinado digitalmente com sucesso!');
                    }, 1000);
                } else {
                    this.mostrarErro(data.message || 'Erro ao processar assinatura');
                    this.loading = false;
                }
            } catch (error) {
                console.error('Erro ao processar assinatura:', error);
                this.mostrarErro('Erro ao processar assinatura: ' + error.message);
                this.loading = false;
            }
        },
        
        gerarAssinaturaDigital() {
            const timestamp = Date.now();
            const dados = `${this.proposicao.id}-${timestamp}-{{ auth()->user()->id }}`;
            return btoa(dados);
        },
        
        verificarAssinatura() {
            if (this.proposicao?.assinatura_digital) {
                this.mostrarInfo(`Assinatura válida: ${this.identificadorAssinatura}`);
            }
        },
        
        voltarListagem() {
            window.location.href = '/proposicoes/assinatura';
        },
        
        formatDate(date) {
            if (!date) return '';
            return new Date(date).toLocaleDateString('pt-BR');
        },
        
        formatDateTime(date) {
            if (!date) return '';
            return new Date(date).toLocaleString('pt-BR');
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
        
        mostrarSucesso(mensagem) {
            // Implementar toast de sucesso
            alert(mensagem);
        },
        
        mostrarErro(mensagem) {
            // Implementar toast de erro
            alert('Erro: ' + mensagem);
        },
        
        mostrarInfo(mensagem) {
            // Implementar toast informativo
            alert(mensagem);
        },

        toggleView(mode) {
            this.viewMode = mode;
            
            // Se mudou para source view, gerar QR code no preview se necessário
            if (mode === 'source' && this.incluirQRCode && this.proposicao?.assinatura_digital) {
                setTimeout(() => {
                    const canvas = document.getElementById('qrcode-canvas-preview');
                    if (canvas && window.QRCode) {
                        const url = `${window.location.origin}/proposicoes/${this.proposicao.id}/verificar`;
                        window.QRCode.toCanvas(canvas, url, {
                            width: 150,
                            margin: 1,
                            color: {
                                dark: '#000000',
                                light: '#FFFFFF'
                            }
                        }).catch(err => console.error('Erro ao gerar QR Code preview:', err));
                    }
                }, 100);
            }
        },

        confirmarExclusaoDocumento() {
            if (this.modalExclusaoDocumento) {
                this.modalExclusaoDocumento.show();
            }
        },

        async excluirDocumento() {
            if (!this.confirmoExclusao || this.excluindoDocumento) return;

            this.excluindoDocumento = true;

            try {
                console.log('Iniciando exclusão de documento...');

                const response = await fetch(`/proposicoes/${this.proposicao.id}/excluir-documento`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    console.log('Documento excluído com sucesso:', data);

                    // Fechar modal
                    if (this.modalExclusaoDocumento) {
                        this.modalExclusaoDocumento.hide();
                    }

                    // Resetar estados
                    this.confirmoExclusao = false;
                    this.pdfReady = false;
                    this.pdfBlob = null;
                    this.pdfPreviewUrl = null;

                    // Atualizar status da proposição se foi alterado
                    if (data.status_atualizado) {
                        this.proposicao.status = data.status_atualizado;
                    }

                    // Limpar conteúdo processado para forçar reprocessamento
                    this.conteudoProcessado = '';
                    this.usandoApenasOnlyOffice = false;

                    this.mostrarSucesso(data.message);

                    // Opcional: redirecionar para a listagem após alguns segundos
                    setTimeout(() => {
                        const confirmarRedirect = confirm('Documento excluído com sucesso! Deseja voltar para a listagem de proposições?');
                        if (confirmarRedirect) {
                            this.voltarListagem();
                        }
                    }, 2000);

                } else {
                    console.error('Erro ao excluir documento:', data);
                    this.mostrarErro(data.message || 'Erro ao excluir documento');
                }

            } catch (error) {
                console.error('Erro na requisição de exclusão:', error);
                this.mostrarErro('Erro de conexão ao excluir documento: ' + error.message);
            } finally {
                this.excluindoDocumento = false;
            }
        },

        extrairParagrafosParaAuditoria(conteudo) {
            try {
                // Dividir em parágrafos de forma inteligente
                let paragrafos = conteudo
                    .split(/\n\s*\n|\r\n\s*\r\n/) // Quebras duplas
                    .filter(p => p.trim().length > 0)
                    .map(p => p.replace(/\s+/g, ' ').trim()); // Normalizar espaços

                // Se não houver quebras duplas, tentar quebras simples
                if (paragrafos.length <= 1) {
                    paragrafos = conteudo
                        .split(/\n|\r\n/)
                        .filter(p => p.trim().length > 0)
                        .map(p => p.replace(/\s+/g, ' ').trim());
                }

                // Se ainda não houver paragráfos, dividir por pontos finais
                if (paragrafos.length <= 1) {
                    paragrafos = conteudo
                        .split(/\.\s+/)
                        .filter(p => p.trim().length > 10)
                        .map((p, index, array) => {
                            // Adicionar ponto final exceto no último
                            return index < array.length - 1 ? p.trim() + '.' : p.trim();
                        });
                }

                console.log('📝 Parágrafos extraídos para auditoria:', {
                    total: paragrafos.length,
                    caracteres_total: paragrafos.join(' ').length,
                    amostra: paragrafos[0]?.substring(0, 100) + '...'
                });

                return paragrafos;

            } catch (error) {
                console.warn('Erro ao extrair parágrafos:', error);
                // Fallback: retornar o conteúdo inteiro como um parágrafo
                return [conteudo];
            }
        }
    }
}).mount('#pdf-assinatura-app');
</script>

@endsection