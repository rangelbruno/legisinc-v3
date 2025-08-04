@extends('components.layouts.app')

@section('title', 'Assinar Proposição')

@section('content')

<style>
/* Prevenir fechamento automático do modal */
.swal2-no-auto-close {
    animation: none !important;
    transition: none !important;
}

.swal2-no-auto-close .swal2-timer-progress-bar {
    display: none !important;
}

/* Garantir que o modal permaneça visível */
.swal2-no-auto-close.swal2-shown {
    display: flex !important;
    opacity: 1 !important;
    visibility: visible !important;
}

/* Desabilitar animações que podem causar fechamento */
.swal2-no-auto-close .swal2-popup {
    animation: none !important;
}

/* Modal customizado simples */
#modalDevolverLegislativo {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1055;
}
.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
}

.certificado-option {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.certificado-option:hover {
    border-color: var(--kt-primary);
    background-color: rgba(var(--kt-primary-rgb), 0.1);
}

.certificado-option.selected {
    border-color: var(--kt-primary);
    background-color: rgba(var(--kt-primary-rgb), 0.1);
}

.certificado-option .form-check-input:checked {
    background-color: var(--kt-primary);
    border-color: var(--kt-primary);
}

.file-upload-area {
    border: 2px dashed #e1e3ea;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-upload-area:hover {
    border-color: var(--kt-primary);
    background-color: rgba(var(--kt-primary-rgb), 0.05);
}

.file-upload-area.dragover {
    border-color: var(--kt-primary);
    background-color: rgba(var(--kt-primary-rgb), 0.1);
}

.progress-container {
    display: none;
}

.certificado-info {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
    display: none;
}

.btn-assinar {
    background: linear-gradient(135deg, #17C653 0%, #13a342 100%);
    border: none;
    color: white;
    font-weight: 600;
}

.btn-assinar:hover {
    background: linear-gradient(135deg, #13a342 0%, #0f8635 100%);
    color: white;
}

.btn-assinar:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>

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
                    <!--begin::Card-->
                    <div class="card card-flush mb-6 mb-xl-9">
                        <!--begin::Card header-->
                        <div class="card-header mt-5">
                            <!--begin::Card title-->
                            <div class="card-title flex-column">
                                <h2 class="mb-1">Informações da Proposição</h2>
                                <div class="fs-6 fw-semibold text-muted">Revise os dados antes de assinar</div>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Details-->
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
                                        <span class="badge badge-light-primary">{{ $proposicao->tipo }}</span>
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
                                    <div class="fw-bold text-end">{{ $proposicao->numero_temporario ?? 'Aguardando' }}</div>
                                </div>
                                
                                <div class="d-flex align-items-center justify-content-between mb-5">
                                    <div class="fw-semibold">
                                        <i class="ki-duotone ki-calendar text-gray-400 fs-6 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>Data Criação:
                                    </div>
                                    <div class="fw-bold text-end">{{ $proposicao->created_at->format('d/m/Y') }}</div>
                                </div>
                                
                                <div class="d-flex align-items-center justify-content-between mb-5">
                                    <div class="fw-semibold">
                                        <i class="ki-duotone ki-flash text-gray-400 fs-6 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>Urgência:
                                    </div>
                                    <div class="fw-bold text-end">
                                        @if($proposicao->urgencia === 'urgentissima')
                                            <span class="badge badge-light-danger">Urgentíssima</span>
                                        @elseif($proposicao->urgencia === 'urgente')
                                            <span class="badge badge-light-warning">Urgente</span>
                                        @else
                                            <span class="badge badge-light-secondary">Normal</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!--end::Details-->
                            
                            <!--begin::Separator-->
                            <div class="separator separator-dashed my-5"></div>
                            <!--end::Separator-->
                            
                            <!--begin::Title-->
                            <div class="mb-5">
                                <label class="fs-6 fw-semibold mb-2 text-gray-600">Título:</label>
                                <div class="fw-bold text-gray-800">{{ $proposicao->titulo ?? 'Sem título' }}</div>
                            </div>
                            <!--end::Title-->
                            
                            <!--begin::Ementa-->
                            <div class="mb-5">
                                <label class="fs-6 fw-semibold mb-2 text-gray-600">Ementa:</label>
                                <div class="fw-semibold text-gray-700 lh-lg">{{ $proposicao->ementa }}</div>
                            </div>
                            <!--end::Ementa-->
                            
                            <!--begin::Revisor-->
                            @if($proposicao->revisor)
                            <div class="mb-5">
                                <label class="fs-6 fw-semibold mb-2 text-gray-600">Aprovado por:</label>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-35px me-3">
                                        <div class="symbol-label bg-light-success text-success fs-8 fw-bold">
                                            {{ substr($proposicao->revisor->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <div class="fw-bold text-gray-800">{{ $proposicao->revisor->name }}</div>
                                        <div class="fs-7 text-muted">{{ $proposicao->data_revisao->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <!--end::Revisor-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Sidebar-->

                <!--begin::Content-->
                <div class="col-xl-8">
                    <!--begin::PDF Viewer Card-->
                    <div class="card card-flush mb-6">
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
                                    @if($proposicao->arquivo_pdf_path)
                                    <a href="{{ route('proposicoes.serve-pdf', $proposicao) }}" target="_blank" class="btn btn-sm btn-outline btn-outline-primary">
                                        <i class="fas fa-external-link-alt me-1"></i>Abrir PDF
                                    </a>
                                    <a href="{{ route('proposicoes.serve-pdf', $proposicao) }}" download class="btn btn-sm btn-outline btn-outline-secondary">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!--end::Card header-->
                        
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            @if(config('app.debug'))
                            <!-- Debug Information -->
                            <div class="alert alert-secondary mb-4">
                                <strong>Debug Info:</strong><br>
                                arquivo_pdf_path: {{ $proposicao->arquivo_pdf_path ?? 'null' }}<br>
                                arquivo_path: {{ $proposicao->arquivo_path ?? 'null' }}<br>
                                PDF exists (Storage): {{ $proposicao->arquivo_pdf_path ? (\Storage::exists($proposicao->arquivo_pdf_path) ? 'true' : 'false') : 'n/a' }}<br>
                                PDF exists (file_exists): {{ $proposicao->arquivo_pdf_path ? (file_exists(storage_path('app/' . $proposicao->arquivo_pdf_path)) ? 'true' : 'false') : 'n/a' }}<br>
                                PDF Route: {{ route('proposicoes.serve-pdf', $proposicao) }}<br>
                                Full PDF Path: {{ $proposicao->arquivo_pdf_path ? storage_path('app/' . $proposicao->arquivo_pdf_path) : 'n/a' }}
                                <br><br>
                                <strong>Test PDF Access:</strong><br>
                                <a href="{{ route('proposicoes.serve-pdf', $proposicao) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-external-link-alt me-1"></i>Test PDF Route
                                </a>
                                <button onclick="forceHideLoading()" class="btn btn-sm btn-warning ms-2">
                                    <i class="fas fa-eye-slash me-1"></i>Force Hide Loading
                                </button>
                            </div>
                            @endif
                            
                            @if($proposicao->arquivo_pdf_path)
                            <!-- PDF Viewer -->
                            <div class="pdf-viewer-container position-relative" style="height: 600px; border: 1px solid #e1e3ea; border-radius: 8px; overflow: hidden; background-color: #f8f9fa;">
                                <!-- Loading indicator -->
                                <div id="pdf-loading" class="position-absolute top-50 start-50 translate-middle d-flex flex-column align-items-center text-center">
                                    <div class="spinner-border text-primary mb-3" role="status">
                                        <span class="visually-hidden">Carregando...</span>
                                    </div>
                                    <p class="text-muted">Carregando documento PDF...</p>
                                    <small class="text-muted mt-2">Se não carregar, clique em "Abrir PDF" acima</small>
                                </div>
                                
                                <!-- PDF Object (mais compatível que iframe) -->
                                <object 
                                    id="pdf-object"
                                    data="{{ route('proposicoes.serve-pdf', $proposicao) }}" 
                                    type="application/pdf" 
                                    width="100%" 
                                    height="100%"
                                    style="display: none;"
                                    onload="hideLoadingAndShowPdf()"
                                    onerror="showPdfError()">
                                    
                                    <!-- Fallback iframe dentro do object -->
                                    <iframe 
                                        id="pdf-frame"
                                        src="{{ route('proposicoes.serve-pdf', $proposicao) }}" 
                                        width="100%" 
                                        height="100%" 
                                        frameborder="0">
                                        
                                        <!-- Fallback final para browsers antigos -->
                                        <div class="d-flex flex-column align-items-center justify-content-center h-100 text-center p-4">
                                            <i class="fas fa-file-pdf text-danger fs-2x mb-3"></i>
                                            <h5 class="mb-3">PDF não pode ser exibido</h5>
                                            <p class="text-muted mb-3">Seu navegador não suporta visualização de PDF incorporado.</p>
                                            <a href="{{ route('proposicoes.serve-pdf', $proposicao) }}" target="_blank" class="btn btn-primary">
                                                <i class="fas fa-external-link-alt me-1"></i>Abrir PDF em nova aba
                                            </a>
                                        </div>
                                    </iframe>
                                </object>
                                
                                <!-- Error fallback -->
                                <div id="pdf-error" class="position-absolute top-50 start-50 translate-middle text-center d-none">
                                    <i class="fas fa-exclamation-triangle text-warning fs-2x mb-3"></i>
                                    <h5 class="mb-3">Problema ao carregar o PDF</h5>
                                    <p class="text-muted mb-3">O PDF existe mas não pode ser exibido neste navegador.</p>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('proposicoes.serve-pdf', $proposicao) }}" target="_blank" class="btn btn-primary">
                                            <i class="fas fa-external-link-alt me-1"></i>Abrir em nova aba
                                        </a>
                                        <button onclick="retryPdfLoad()" class="btn btn-outline-primary">
                                            <i class="fas fa-redo me-1"></i>Tentar novamente
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @elseif($proposicao->arquivo_path)
                            <!-- Fallback: Link para documento original -->
                            <div class="alert alert-info d-flex align-items-center p-5">
                                <i class="fas fa-info-circle fs-2hx text-info me-4"></i>
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-info">Gerando PDF para Assinatura</h4>
                                    <span>O documento PDF está sendo processado automaticamente. 
                                    <a href="{{ asset('storage/' . $proposicao->arquivo_path) }}" target="_blank" class="fw-bold">Visualizar documento original</a> enquanto isso.
                                    <br><small class="text-muted">Recarregue a página em alguns segundos para ver o PDF.</small></span>
                                </div>
                            </div>
                            
                            <!-- Auto-refresh para verificar se PDF foi gerado -->
                            <script>
                            let autoRefreshTimer = setTimeout(function() {
                                // Só recarrega se não há modal ativo de devolução
                                if (!document.getElementById('modalDevolverLegislativo')) {
                                    location.reload();
                                } else {
                                    console.log('Auto-refresh cancelado: modal de devolução está ativo');
                                }
                            }, 10000); // Recarrega após 10 segundos
                            
                            // Disponibilizar globalmente para cancelar se necessário
                            window.autoRefreshTimer = autoRefreshTimer;
                            </script>
                            @else
                            <!-- Erro: Nenhum documento disponível -->
                            <div class="alert alert-danger d-flex align-items-center p-5">
                                <i class="fas fa-exclamation-circle fs-2hx text-danger me-4"></i>
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-danger">Documento não encontrado</h4>
                                    <span>Não foi possível localizar o documento para assinatura.</span>
                                </div>
                            </div>
                            @endif
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::PDF Viewer Card-->
                    
                    <!--begin::Signature Card-->
                    <div class="card card-flush">
                        <!--begin::Card header-->
                        <div class="card-header align-items-start">
                            <div class="card-title">
                                <h2 class="fw-bold text-dark">Assinatura Digital</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            
                            <!-- Formulário de Assinatura -->
                            <form id="assinatura-digital-form" method="POST" action="{{ route('proposicoes.processar-assinatura', $proposicao) }}" enctype="multipart/form-data">
                                @csrf
                                
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
                                    <input class="form-check-input" type="checkbox" id="confirmacao_leitura" name="confirmacao_leitura" value="1" />
                                    <label class="form-check-label fw-semibold text-gray-700" for="confirmacao_leitura">
                                        Confirmo que li e revisei completamente o documento e estou ciente do seu conteúdo
                                    </label>
                                </div>
                            </div>
                            
                            <div id="assinatura-form" style="display: none;">
                                <!-- Seleção do Tipo de Certificado -->
                                <div class="mb-8">
                                    <label class="required fs-6 fw-semibold mb-2">Tipo de Certificado Digital</label>
                                    <div class="row g-4">
                                        <!-- Certificado A1 -->
                                        <div class="col-lg-4">
                                            <div class="certificado-option card h-100 p-4" data-tipo="a1">
                                                <div class="form-check form-check-custom form-check-solid mb-3">
                                                    <input class="form-check-input" type="radio" name="tipo_certificado" value="a1" id="cert_a1"/>
                                                    <label class="form-check-label" for="cert_a1"></label>
                                                </div>
                                                <div class="text-center">
                                                    <i class="ki-duotone ki-safe-home text-primary fs-3x mb-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <h5 class="fw-bold mb-2">Certificado A1</h5>
                                                    <p class="text-muted fs-7 mb-0">Arquivo instalado no computador (.pfx/.p12)</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Certificado A3 -->
                                        <div class="col-lg-4">
                                            <div class="certificado-option card h-100 p-4" data-tipo="a3">
                                                <div class="form-check form-check-custom form-check-solid mb-3">
                                                    <input class="form-check-input" type="radio" name="tipo_certificado" value="a3" id="cert_a3"/>
                                                    <label class="form-check-label" for="cert_a3"></label>
                                                </div>
                                                <div class="text-center">
                                                    <i class="ki-duotone ki-tablet text-warning fs-3x mb-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <h5 class="fw-bold mb-2">Certificado A3</h5>
                                                    <p class="text-muted fs-7 mb-0">Token/Smartcard físico</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Upload PFX -->
                                        <div class="col-lg-4">
                                            <div class="certificado-option card h-100 p-4" data-tipo="pfx">
                                                <div class="form-check form-check-custom form-check-solid mb-3">
                                                    <input class="form-check-input" type="radio" name="tipo_certificado" value="pfx" id="cert_pfx"/>
                                                    <label class="form-check-label" for="cert_pfx"></label>
                                                </div>
                                                <div class="text-center">
                                                    <i class="ki-duotone ki-file-up text-success fs-3x mb-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <h5 class="fw-bold mb-2">Upload .PFX</h5>
                                                    <p class="text-muted fs-7 mb-0">Enviar arquivo de certificado</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Área de Upload para PFX -->
                                <div id="pfx-upload-area" class="mb-8" style="display: none;">
                                    <label class="fs-6 fw-semibold mb-2">Arquivo do Certificado (.pfx/.p12)</label>
                                    <div class="file-upload-area" id="file-drop-zone">
                                        <input type="file" id="pfx-file" name="arquivo_certificado" accept=".pfx,.p12" style="display: none;">
                                        <i class="ki-duotone ki-file-up fs-3x text-primary mb-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <h5 class="fw-bold mb-2">Clique ou arraste o arquivo aqui</h5>
                                        <p class="text-muted mb-3">Formatos aceitos: .pfx, .p12</p>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="document.getElementById('pfx-file').click()">
                                            Selecionar Arquivo
                                        </button>
                                    </div>
                                    
                                    <!-- Progresso do Upload -->
                                    <div class="progress-container">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-semibold text-gray-700" id="file-name"></span>
                                            <span class="fs-7 text-muted" id="file-size"></span>
                                        </div>
                                        <div class="progress h-8px">
                                            <div class="progress-bar bg-primary" id="upload-progress" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Campo de Senha -->
                                <div id="senha-certificado" class="mb-8" style="display: none;">
                                    <label class="required fs-6 fw-semibold mb-2">Senha do Certificado</label>
                                    <input type="password" class="form-control" id="senha_certificado" name="senha_certificado" placeholder="Digite a senha do certificado digital" autocomplete="current-password">
                                    <div class="form-text">Necessária para validar e utilizar o certificado digital</div>
                                </div>
                                
                                <!-- Informações do Certificado -->
                                <div id="certificado-info" class="certificado-info">
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
                                                <div class="fw-bold text-gray-800" id="cert-titular">-</div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="fs-7 fw-semibold text-gray-600">Emissor:</label>
                                                <div class="fw-bold text-gray-800" id="cert-emissor">-</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="fs-7 fw-semibold text-gray-600">Válido até:</label>
                                                <div class="fw-bold text-gray-800" id="cert-validade">-</div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="fs-7 fw-semibold text-gray-600">Status:</label>
                                                <div id="cert-status">
                                                    <span class="badge badge-light-success">Válido</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Botões de Ação -->
                                <div class="d-flex justify-content-end">
                                    <div>
                                        <button type="button" class="btn btn-light me-3" onclick="window.history.back()">
                                            Cancelar
                                        </button>
                                        <button type="button" id="btn-assinar" class="btn btn-assinar" disabled>
                                            <i class="ki-duotone ki-check fs-2 me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>Assinar Digitalmente
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            </form>
                            <!--end::Formulário de Assinatura-->
                            
                            <!-- Separador -->
                            <div class="separator separator-dashed my-6"></div>
                            
                            <!-- Botão Devolver para Legislativo (sempre visível) -->
                            <div class="d-flex justify-content-start">
                                <button type="button" class="btn btn-outline-warning" onclick="devolverParaLegislativo()">
                                    <i class="fas fa-arrow-left me-2"></i>Devolver para o Legislativo
                                </button>
                                <div class="ms-3 d-flex align-items-center">
                                    <small class="text-muted">Se o documento precisa de alterações, devolva-o para o Legislativo com suas observações</small>
                                </div>
                            </div>
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Content-->
            </div>

        </div>
        <!--end::Container-->
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection

@push('scripts')
<script>
"use strict";

// Variáveis globais
let certificadoSelecionado = null;
let arquivoCertificado = null;

// Inicialização
$(document).ready(function() {
    initAssinaturaForm();
});

function initAssinaturaForm() {
    
    // Confirmação de leitura
    $('#confirmacao_leitura').on('change', function() {
        if (this.checked) {
            $('#assinatura-form').slideDown();
            // Confirma leitura via AJAX
            confirmarLeitura();
        } else {
            $('#assinatura-form').slideUp();
            $('#btn-assinar').prop('disabled', true);
        }
    });
    
    // Seleção de tipo de certificado
    $('.certificado-option').on('click', function() {
        const tipo = $(this).data('tipo');
        
        // Remove seleção anterior
        $('.certificado-option').removeClass('selected');
        $('input[name="tipo_certificado"]').prop('checked', false);
        
        // Marca opção selecionada
        $(this).addClass('selected');
        $(this).find('input[name="tipo_certificado"]').prop('checked', true);
        
        certificadoSelecionado = tipo;
        
        // Mostra/esconde campos específicos
        toggleCertificadoFields(tipo);
        
        // Habilita botão se tudo estiver preenchido
        validateForm();
    });
    
    // Upload de arquivo PFX
    $('#pfx-file').on('change', function() {
        const file = this.files[0];
        if (file) {
            handleFileUpload(file);
        }
    });
    
    // Drag and drop
    const dropZone = document.getElementById('file-drop-zone');
    
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });
    
    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
    });
    
    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            if (file.name.endsWith('.pfx') || file.name.endsWith('.p12')) {
                $('#pfx-file')[0].files = files;
                handleFileUpload(file);
            } else {
                Swal.fire({
                    title: 'Arquivo Inválido',
                    text: 'Por favor, selecione um arquivo .pfx ou .p12',
                    icon: 'error'
                });
            }
        }
    });
    
    // Validação da senha
    $('#senha_certificado').on('input', function() {
        validateForm();
    });
    
    // Botão de assinatura
    $('#btn-assinar').on('click', function() {
        processarAssinatura();
    });
}

function toggleCertificadoFields(tipo) {
    // Esconde todos os campos específicos
    $('#pfx-upload-area').hide();
    $('#senha-certificado').hide();
    $('#certificado-info').hide();
    
    if (tipo === 'pfx') {
        $('#pfx-upload-area').show();
        $('#senha-certificado').show();
    } else if (tipo === 'a1' || tipo === 'a3') {
        $('#senha-certificado').show();
        // Para A1/A3, simular detecção do certificado
        detectarCertificado(tipo);
    }
}

function handleFileUpload(file) {
    arquivoCertificado = file;
    
    // Mostra informações do arquivo
    $('.progress-container').show();
    $('#file-name').text(file.name);
    $('#file-size').text(formatFileSize(file.size));
    
    // Simula progresso do upload
    let progress = 0;
    const interval = setInterval(() => {
        progress += 10;
        $('#upload-progress').css('width', progress + '%');
        
        if (progress >= 100) {
            clearInterval(interval);
            setTimeout(() => {
                validarCertificadoPFX(file);
            }, 500);
        }
    }, 100);
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function detectarCertificado(tipo) {
    // Simula detecção do certificado A1/A3
    // Em implementação real, usaria APIs específicas do navegador ou plugins
    
    setTimeout(() => {
        $('#certificado-info').show();
        $('#cert-titular').text('{{ auth()->user()->name }}');
        $('#cert-emissor').text('AC Certisign RFB G5');
        $('#cert-validade').text('31/12/2025');
        
        validateForm();
    }, 1000);
}

function validarCertificadoPFX(file) {
    // Em implementação real, validaria o arquivo PFX
    // Por enquanto, simula validação bem-sucedida
    
    $('#certificado-info').show();
    $('#cert-titular').text('{{ auth()->user()->name }}');
    $('#cert-emissor').text('AC Certisign RFB G5');
    $('#cert-validade').text('31/12/2025');
    
    validateForm();
}

function validateForm() {
    let isValid = true;
    
    // Verifica se tipo foi selecionado
    if (!certificadoSelecionado) {
        isValid = false;
    }
    
    // Verifica senha
    const senha = $('#senha_certificado').val();
    if (!senha || senha.length < 4) {
        isValid = false;
    }
    
    // Verifica arquivo PFX se necessário
    if (certificadoSelecionado === 'pfx' && !arquivoCertificado) {
        isValid = false;
    }
    
    $('#btn-assinar').prop('disabled', !isValid);
}

function confirmarLeitura() {
    $.ajax({
        url: '{{ route("proposicoes.confirmar-leitura", $proposicao) }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
            }
        },
        error: function(xhr) {
            toastr.error('Erro ao confirmar leitura');
            $('#confirmacao_leitura').prop('checked', false);
            $('#assinatura-form').slideUp();
        }
    });
}

function processarAssinatura() {
    // Desabilita botão
    const btnAssinar = $('#btn-assinar');
    btnAssinar.prop('disabled', true);
    btnAssinar.html('<span class="spinner-border spinner-border-sm me-2"></span>Assinando...');
    
    // Simula processo de assinatura digital
    // Em implementação real, usaria bibliotecas específicas para assinatura
    
    setTimeout(() => {
        // Usar FormData do formulário para incluir arquivos
        const form = document.getElementById('assinatura-digital-form');
        const formData = new FormData(form);
        
        // Adicionar dados específicos da assinatura
        formData.append('tipo_certificado', certificadoSelecionado);
        formData.append('assinatura_digital', gerarAssinaturaDigital());
        formData.append('certificado_digital', obterCertificadoDigital());
        
        $.ajax({
            url: '{{ route("proposicoes.processar-assinatura", $proposicao) }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Sucesso!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = '{{ route("proposicoes.assinatura") }}';
                    });
                } else {
                    Swal.fire({
                        title: 'Erro',
                        text: response.message,
                        icon: 'error'
                    });
                    resetarBotaoAssinatura();
                }
            },
            error: function(xhr) {
                let message = 'Erro ao processar assinatura';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    title: 'Erro',
                    text: message,
                    icon: 'error'
                });
                resetarBotaoAssinatura();
            }
        });
    }, 2000);
}

function resetarBotaoAssinatura() {
    const btnAssinar = $('#btn-assinar');
    btnAssinar.prop('disabled', false);
    btnAssinar.html('<i class="ki-duotone ki-check fs-2 me-2"><span class="path1"></span><span class="path2"></span></i>Assinar Digitalmente');
}

function gerarAssinaturaDigital() {
    // Em implementação real, geraria hash da assinatura digital
    const timestamp = new Date().getTime();
    const proposicaoId = '{{ $proposicao->id }}';
    const userId = '{{ auth()->id() }}';
    
    return btoa(`${proposicaoId}-${userId}-${timestamp}-${certificadoSelecionado}`);
}

function obterCertificadoDigital() {
    // Em implementação real, extrairia dados do certificado
    return JSON.stringify({
        titular: '{{ auth()->user()->name }}',
        tipo: certificadoSelecionado,
        emissor: 'AC Certisign RFB G5',
        validade: '2025-12-31',
        arquivo: arquivoCertificado ? arquivoCertificado.name : null
    });
}

// Função simplificada para esconder loading
function hideLoadingAndShowPdf() {
    const loading = document.getElementById('pdf-loading');
    const pdfObject = document.getElementById('pdf-object');
    
    console.log('Hiding loading and showing PDF');
    
    if (loading) {
        loading.style.display = 'none';
    }
    if (pdfObject) {
        pdfObject.style.display = 'block';
    }
}

// Função para forçar esconder o loading (para debug)
function forceHideLoading() {
    console.log('Force hiding loading manually');
    const loading = document.getElementById('pdf-loading');
    const pdfObject = document.getElementById('pdf-object');
    
    if (loading) {
        loading.style.display = 'none !important';
        loading.style.visibility = 'hidden';
        loading.classList.add('d-none');
        loading.remove(); // Remove do DOM
        console.log('Loading forcefully hidden and removed');
    }
    
    if (pdfObject) {
        pdfObject.style.display = 'block';
        pdfObject.style.visibility = 'visible';
        console.log('PDF object forcefully shown');
    }
    
    alert('Loading forçadamente escondido!');
}

function retryPdfLoad() {
    console.log('Retrying PDF load');
    
    // Esconder erro e mostrar loading
    const loading = document.getElementById('pdf-loading');
    const error = document.getElementById('pdf-error');
    const pdfObject = document.getElementById('pdf-object');
    
    if (error) error.classList.add('d-none');
    if (loading) loading.style.display = 'flex';
    if (pdfObject) pdfObject.style.display = 'none';
    
    // Recarregar o PDF object
    if (pdfObject) {
        const originalData = pdfObject.data;
        pdfObject.data = '';
        setTimeout(() => {
            pdfObject.data = originalData;
            initPdfViewer();
        }, 100);
    }
}

function hidePdfLoading() {
    const loading = document.getElementById('pdf-loading');
    const pdfObject = document.getElementById('pdf-object');
    
    console.log('Hiding PDF loading indicator');
    
    if (loading) loading.style.display = 'none';
    if (pdfObject) pdfObject.style.display = 'block';
}

function showPdfError() {
    const loading = document.getElementById('pdf-loading');
    const error = document.getElementById('pdf-error');
    
    if (loading) loading.classList.add('d-none');
    if (error) error.classList.remove('d-none');
}

// Inicializar o visualizador PDF quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, hiding loading immediately');
    
    // Esconder loading imediatamente
    const loading = document.getElementById('pdf-loading');
    const pdfObject = document.getElementById('pdf-object');
    
    console.log('Loading element:', loading);
    console.log('PDF object:', pdfObject);
    
    if (loading) {
        loading.style.display = 'none';
        loading.style.visibility = 'hidden';
        loading.classList.add('d-none');
        console.log('Loading hidden with multiple methods');
    }
    
    if (pdfObject) {
        pdfObject.style.display = 'block';
        pdfObject.style.visibility = 'visible';
        console.log('PDF object shown');
    }
    
    // Backup forçado após 500ms
    setTimeout(() => {
        console.log('Backup: force hiding loading again');
        const loadingBackup = document.getElementById('pdf-loading');
        if (loadingBackup) {
            loadingBackup.remove(); // Remove completamente do DOM
            console.log('Loading element removed from DOM');
        }
    }, 500);
});

// Função para interceptar reloads da página
function interceptarReloads() {
    if (!window.originalReload) {
        window.originalReload = location.reload;
    }
    
    location.reload = function() {
        if (document.getElementById('modalDevolverLegislativo')) {
            console.log('RELOAD BLOQUEADO: Modal de devolução está ativo');
            return false;
        }
        return window.originalReload.call(location);
    };
}

// Função simples usando modal Bootstrap nativo
function devolverParaLegislativoBootstrap() {
    console.log('Abrindo modal de devolução');
    
    // Interceptar tentativas de reload
    interceptarReloads();
    
    // Cancelar todos os timers ativos
    for (let i = 1; i < 10000; i++) {
        clearTimeout(i);
    }
    console.log('Todos os timers foram cancelados');
    
    // Criar modal usando HTML simples
    const modalHtml = `
        <div class="modal" id="modalDevolverLegislativo" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-arrow-left text-warning me-2"></i>
                            Devolver para o Legislativo?
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <i class="fas fa-arrow-left text-warning fa-4x mb-3"></i>
                            <p class="mb-3">Você está prestes a devolver esta proposição para o Legislativo com solicitação de alterações.</p>
                        </div>
                        <div class="mb-3">
                            <label for="observacoesBootstrap" class="form-label fw-bold">Observações (obrigatório):</label>
                            <textarea id="observacoesBootstrap" class="form-control" rows="4" placeholder="Descreva as alterações ou correções necessárias..."></textarea>
                            <div id="observacoesError" class="text-danger mt-1 d-none">Por favor, informe as observações sobre as alterações necessárias</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="fecharModalDevolver()">Cancelar</button>
                        <button type="button" class="btn btn-warning" onclick="processarDevolucaoBootstrap()">
                            <i class="fas fa-arrow-left me-1"></i>Devolver
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal anterior se existir
    document.getElementById('modalDevolverLegislativo')?.remove();
    
    // Adicionar ao body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Detectar mudanças na página que podem afetar o modal
    const modal = document.getElementById('modalDevolverLegislativo');
    if (modal) {
        console.log('Modal criado e ativo');
        
        // Verificar se há recarregamentos automáticos da página
        if (window.location.reload || document.querySelector('meta[http-equiv="refresh"]')) {
            console.warn('Página tem recarregamento automático que pode fechar o modal');
        }
    }
    
    // Focar no textarea
    setTimeout(() => {
        document.getElementById('observacoesBootstrap')?.focus();
    }, 200);
}

function fecharModalDevolver() {
    document.getElementById('modalDevolverLegislativo')?.remove();
    
    // Restaurar função original de reload
    if (window.originalReload) {
        location.reload = window.originalReload;
        console.log('Função de reload restaurada');
    }
    
    console.log('Modal fechado');
}

function processarDevolucaoBootstrap() {
    const observacoes = document.getElementById('observacoesBootstrap').value;
    const errorDiv = document.getElementById('observacoesError');
    
    if (!observacoes.trim()) {
        errorDiv.classList.remove('d-none');
        return;
    }
    
    errorDiv.classList.add('d-none');
    
    // Fechar modal
    fecharModalDevolver();
    
    // Fazer a requisição
    enviarDevolucao(observacoes);
}

function enviarDevolucao(observacoes) {
    // Mostrar loading usando SweetAlert2 simples
    Swal.fire({
        title: 'Devolvendo...',
        html: '<div class="spinner-border text-warning" role="status"></div><p class="mt-2">Devolvendo proposição...</p>',
        allowOutsideClick: false,
        showConfirmButton: false
    });

    $.ajax({
        url: '{{ route("proposicoes.devolver-legislativo", $proposicao) }}',
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({ observacoes: observacoes }),
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Devolvida com Sucesso!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        window.location.href = '{{ route("proposicoes.minhas-proposicoes") }}';
                    }
                });
            } else {
                Swal.fire('Erro!', response.message || 'Erro ao devolver proposição', 'error');
            }
        },
        error: function(xhr) {
            let message = 'Erro ao devolver proposição. Tente novamente.';
            if (xhr.responseJSON?.message) {
                message = xhr.responseJSON.message;
            }
            Swal.fire('Erro!', message, 'error');
        }
    });
}

// Função para devolver proposição para o Legislativo - VERSÃO FINAL SIMPLIFICADA
function devolverParaLegislativo() {
    console.log('Abrindo modal de devolução para o Legislativo');
    
    // Abordagem super simples: prompt nativo do browser
    const observacoes = prompt(
        "DEVOLVER PARA O LEGISLATIVO\n\n" +
        "Você está prestes a devolver esta proposição para o Legislativo com solicitação de alterações.\n\n" +
        "Observações (obrigatório):\n" +
        "Descreva as alterações ou correções necessárias..."
    );
    
    if (observacoes === null) {
        // Usuário clicou em cancelar
        return;
    }
    
    if (!observacoes || observacoes.trim().length < 10) {
        alert('Por favor, informe as observações sobre as alterações necessárias (mínimo 10 caracteres).');
        return devolverParaLegislativo(); // Tentar novamente
    }
    
    // Fazer a requisição diretamente
    enviarDevolucao(observacoes.trim());
    return;
    
    // Usar uma instância completamente nova do Swal com configurações mais restritivas
    const SwalModal = Swal.mixin({
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        stopKeydownPropagation: false,
        keydownListenerCapture: false,
        timer: null,
        timerProgressBar: false,
        backdrop: true,
        toast: false,
        position: 'center',
        grow: false,
        showClass: {
            popup: 'animate__animated animate__fadeInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        }
    });
    
    SwalModal.fire({
        title: 'Devolver para o Legislativo?',
        html: `<div class="text-center">
                <i class="fas fa-arrow-left text-warning fa-4x mb-3"></i>
                <p class="mb-3">Você está prestes a devolver esta proposição para o Legislativo com solicitação de alterações.</p>
                <div class="text-start">
                    <label for="observacoes" class="form-label fw-bold">Observações (obrigatório):</label>
                    <textarea id="observacoes" class="form-control" rows="4" placeholder="Descreva as alterações ou correções necessárias..."></textarea>
                </div>
               </div>`,
        width: '500px',
        icon: null,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-arrow-left me-1"></i>Devolver',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#f1c40f',
        cancelButtonColor: '#6c757d',
        customClass: {
            confirmButton: 'btn btn-warning',
            cancelButton: 'btn btn-secondary',
            popup: 'swal2-no-auto-close'
        },
        reverseButtons: true,
        focusConfirm: false,
        focusCancel: false,
        preConfirm: () => {
            const observacoes = document.getElementById('observacoes').value;
            if (!observacoes.trim()) {
                Swal.showValidationMessage('Por favor, informe as observações sobre as alterações necessárias');
                return false;
            }
            console.log('Validação passou, observações:', observacoes);
            return observacoes;
        },
        didOpen: (popup) => {
            console.log('Modal de devolução foi aberto');
            
            // Desabilitar qualquer timer global
            if (popup._timer) {
                clearTimeout(popup._timer);
                popup._timer = null;
            }
            
            // Limpar todos os timers globais do Swal
            if (window.Swal && window.Swal.getTimerLeft) {
                window.Swal.stopTimer();
            }
            
            // Override do método close para prevenir fechamento não autorizado
            const originalClose = popup.close;
            let allowClose = false;
            
            popup.close = function() {
                if (allowClose) {
                    originalClose.call(this);
                } else {
                    console.log('Fechamento do modal bloqueado');
                }
            };
            
            // Permitir fechamento apenas nos botões
            popup.querySelector('.swal2-confirm')?.addEventListener('click', () => {
                allowClose = true;
            });
            
            popup.querySelector('.swal2-cancel')?.addEventListener('click', () => {
                allowClose = true;
            });
            
            // Focar no textarea
            setTimeout(() => {
                const textarea = document.getElementById('observacoes');
                if (textarea) {
                    textarea.focus();
                }
            }, 100);
            
            // Adicionar evento personalizado para prevenir fechamento
            popup.addEventListener('click', (e) => {
                // Permitir apenas cliques nos botões
                if (!e.target.closest('.swal2-confirm') && !e.target.closest('.swal2-cancel')) {
                    e.stopPropagation();
                }
            });
        },
        willClose: () => {
            console.log('Modal está tentando fechar');
        },
        didClose: () => {
            console.log('Modal foi fechado');
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Devolvendo...',
                html: '<div class="text-center"><div class="spinner-border text-warning" role="status"><span class="visually-hidden">Carregando...</span></div><p class="mt-2 mb-0">Devolvendo proposição para o Legislativo...</p></div>',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            // Fazer a requisição
            $.ajax({
                url: '{{ route("proposicoes.devolver-legislativo", $proposicao) }}',
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify({
                    observacoes: result.value
                }),
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Devolvida com Sucesso!',
                            html: `<div class="text-center">
                                    <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                                    <p>${response.message}</p>
                                   </div>`,
                            icon: null,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                window.location.href = '{{ route("proposicoes.minhas-proposicoes") }}';
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Erro!',
                            text: response.message || 'Erro ao devolver proposição',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Erro:', xhr);
                    let message = 'Erro ao devolver proposição. Tente novamente.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.status === 403) {
                        message = 'Você não tem permissão para devolver esta proposição.';
                    } else if (xhr.status === 404) {
                        message = 'Proposição não encontrada.';
                    } else if (xhr.status === 400) {
                        message = xhr.responseJSON?.message || 'Esta proposição não pode ser devolvida no status atual.';
                    }
                    
                    Swal.fire({
                        title: 'Erro!',
                        text: message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}
</script>
@endpush