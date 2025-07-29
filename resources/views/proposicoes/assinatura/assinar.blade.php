@extends('components.layouts.app')

@section('title', 'Assinar Proposição')

@section('content')

<style>
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
                    <!--begin::Card-->
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
                                    <input class="form-check-input" type="checkbox" id="confirmacao_leitura" />
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
                                        <input type="file" id="pfx-file" accept=".pfx,.p12" style="display: none;">
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
                                    <input type="password" class="form-control" id="senha_certificado" placeholder="Digite a senha do certificado digital">
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
        const dadosAssinatura = {
            tipo_certificado: certificadoSelecionado,
            assinatura_digital: gerarAssinaturaDigital(),
            certificado_digital: obterCertificadoDigital(),
            _token: '{{ csrf_token() }}'
        };
        
        $.ajax({
            url: '{{ route("proposicoes.processar-assinatura", $proposicao) }}',
            method: 'POST',
            data: dadosAssinatura,
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
</script>
@endpush