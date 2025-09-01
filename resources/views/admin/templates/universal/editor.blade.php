<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Editor - {{ $template->nome }}</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    
    <!-- Global Stylesheets Bundle (includes Bootstrap) -->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            background: #f5f8fa;
        }
        
        .editor-toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: #ffffff;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 1000;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        
        .editor-title {
            display: flex;
            align-items: center;
            font-weight: 600;
            font-size: 16px;
            color: #181c32;
        }
        
        .editor-title i {
            margin-right: 8px;
            color: #667eea;
        }
        
        .editor-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-editor {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .btn-editor-info {
            background: #f1f9ff;
            color: #0ea5e9;
        }
        
        .btn-editor-info:hover {
            background: #e0f2fe;
        }
        
        .btn-editor-primary {
            background: #eff6ff;
            color: #2563eb;
        }
        
        .btn-editor-primary:hover {
            background: #dbeafe;
        }
        
        .btn-editor-success {
            background: #f0fdf4;
            color: #16a34a;
        }
        
        .btn-editor-success:hover {
            background: #dcfce7;
        }
        
        .btn-editor-light {
            background: #f8f9fa;
            color: #6c757d;
        }
        
        .btn-editor-light:hover {
            background: #e9ecef;
        }
        
        #onlyoffice-editor {
            position: absolute;
            top: 60px;
            left: 0;
            right: 0;
            bottom: 0;
            border: none;
        }
        
        .status-indicator {
            position: fixed;
            top: 70px;
            right: 20px;
            padding: 8px 12px;
            background: rgba(22, 163, 74, 0.9);
            color: white;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            z-index: 999;
            display: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .loading-overlay {
            position: fixed;
            top: 60px;
            left: 0;
            right: 0;
            bottom: 0;
            background: #f5f8fa;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999;
            flex-direction: column;
        }
        
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #e9ecef;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 16px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading-text {
            color: #6c757d;
            font-size: 14px;
            font-weight: 500;
        }
        
        /* Estilos para os itens de variáveis */
        .variable-item {
            padding: 12px 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            background: #f9fafb;
        }
        
        .variable-item:hover {
            background: #e0f2fe;
            border-color: #0ea5e9;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.15);
        }
        
        .variable-item:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(14, 165, 233, 0.2);
        }
        
        .variable-item code {
            user-select: all;
            background: transparent !important;
            padding: 0 !important;
            border: none !important;
        }
        
        /* Animação para feedback de clique */
        .variable-item.clicked {
            animation: clickFeedback 0.3s ease;
            background: #dcfce7 !important;
            border-color: #16a34a !important;
        }
        
        @keyframes clickFeedback {
            0% { transform: scale(1); }
            50% { transform: scale(0.98); }
            100% { transform: scale(1); }
        }
        
        /* Toast personalizado */
        #copy-toast .toast {
            min-width: 300px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <!-- Toolbar Minimalista -->
    <div class="editor-toolbar">
        <div class="editor-title">
            <i class="ki-duotone ki-design-1 fs-4">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            {{ $template->nome }}
            @if($template->is_default)
                <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">PADRÃO</span>
            @endif
        </div>
        
        <div class="editor-actions">
            <button type="button" class="btn-editor btn-editor-primary" onclick="mostrarVariaveis()">
                <i class="ki-duotone ki-code fs-6 me-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                </i>
                Variáveis
            </button>
            
            <button type="button" class="btn-editor btn-editor-info" onclick="mostrarAjuda()">
                <i class="ki-duotone ki-information-5 fs-6 me-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                Ajuda
            </button>
            
            <button type="button" class="btn-editor btn-editor-success" onclick="salvarManual()">
                <i class="ki-duotone ki-save fs-6 me-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Salvar
            </button>
            
            <button type="button" class="btn-editor btn-editor-light" onclick="voltarParaTemplates()">
                <i class="ki-duotone ki-arrow-left fs-6 me-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Voltar
            </button>
        </div>
    </div>
    
    <!-- Status Indicator -->
    <div id="status-indicator" class="status-indicator">
        <i class="ki-duotone ki-check-circle fs-6 me-1">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
        Salvo automaticamente
    </div>
    
    <!-- Loading Overlay -->
    <div id="loading-overlay" class="loading-overlay">
        <div class="loading-spinner"></div>
        <div class="loading-text">Carregando Editor OnlyOffice...</div>
    </div>
    
    <!-- Editor OnlyOffice -->
    <div id="onlyoffice-editor"></div>

<!--begin::Modal - Ajuda-->
<div class="modal fade" id="kt_modal_help" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-800px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Como Usar o Template Universal</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body py-10 px-lg-17">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-8">
                            <h3 class="text-gray-900 fw-bold fs-4 mb-5">
                                <i class="ki-duotone ki-rocket fs-2 text-primary me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Conceito do Template Universal
                            </h3>
                            <div class="text-gray-700 fs-6 mb-5">
                                O <strong>Template Universal</strong> é um modelo único que se adapta automaticamente a qualquer tipo de proposição. 
                                Em vez de manter 23 templates separados, você agora gerencia apenas um que é inteligente o suficiente 
                                para se ajustar ao contexto.
                            </div>
                        </div>
                        
                        <div class="mb-8">
                            <h4 class="text-gray-900 fw-bold fs-5 mb-4">
                                <i class="ki-duotone ki-code fs-3 text-success me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                                Variáveis Principais
                            </h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <code class="text-primary fs-7 fw-bold">${tipo_proposicao}</code>
                                        </div>
                                        <div class="text-muted fs-8">Se adapta automaticamente (MOÇÃO, PROJETO DE LEI, etc.)</div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <code class="text-primary fs-7 fw-bold">${numero_proposicao}</code>
                                        </div>
                                        <div class="text-muted fs-8">Numeração automática por tipo</div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <code class="text-primary fs-7 fw-bold">${ementa}</code>
                                        </div>
                                        <div class="text-muted fs-8">Ementa da proposição</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <code class="text-primary fs-7 fw-bold">${texto}</code>
                                        </div>
                                        <div class="text-muted fs-8">Conteúdo principal</div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <code class="text-primary fs-7 fw-bold">${autor_nome}</code>
                                        </div>
                                        <div class="text-muted fs-8">Nome do parlamentar</div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <code class="text-primary fs-7 fw-bold">${justificativa}</code>
                                        </div>
                                        <div class="text-muted fs-8">Justificativa (quando aplicável)</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-8">
                            <h4 class="text-gray-900 fw-bold fs-5 mb-4">
                                <i class="ki-duotone ki-setting-2 fs-3 text-info me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Seções Adaptáveis
                            </h4>
                            <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4">
                                <i class="ki-duotone ki-information-5 fs-2 text-info me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <div class="fs-6 text-gray-700">Estrutura Inteligente</div>
                                        <div class="fs-7 text-muted">
                                            Use seções condicionais como "<strong>ARTICULADO (Para Projetos de Lei)</strong>" 
                                            que aparecerão apenas quando relevante para o tipo de proposição.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-8">
                            <h4 class="text-gray-900 fw-bold fs-5 mb-4">
                                <i class="ki-duotone ki-home fs-3 text-warning me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Variáveis Institucionais
                            </h4>
                            <div class="text-gray-700 fs-6 mb-3">
                                Todas as variáveis de cabeçalho, rodapé e dados da câmara estão disponíveis:
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-1"><code class="text-warning">${cabecalho_nome_camara}</code></li>
                                        <li class="mb-1"><code class="text-warning">${endereco_camara}</code></li>
                                        <li class="mb-1"><code class="text-warning">${telefone_camara}</code></li>
                                    </ul>
                                </div>
                                <div class="col-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-1"><code class="text-warning">${municipio}</code></li>
                                        <li class="mb-1"><code class="text-warning">${rodape_texto}</code></li>
                                        <li class="mb-1"><code class="text-warning">${assinatura_padrao}</code></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success">
                            <div class="d-flex align-items-center">
                                <i class="ki-duotone ki-shield-tick fs-2 text-success me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div>
                                    <div class="fw-semibold">Resultado Final</div>
                                    <div class="fs-7 text-muted">
                                        Um template que funciona para todos os tipos, mantendo a consistência 
                                        e simplificando a manutenção.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Ajuda-->

<!--begin::Modal - Variáveis-->
<div class="modal fade" id="kt_modal_variaveis" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-900px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">
                    <i class="ki-duotone ki-code fs-2 text-primary me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                    Variáveis do Template
                </h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body py-5 px-lg-10">
                <div class="mb-5">
                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                        <i class="ki-duotone ki-information-5 fs-2tx text-primary me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Como Usar</h4>
                                <div class="fs-6 text-gray-700">Clique em qualquer variável abaixo para copiá-la automaticamente. Depois cole no seu template usando <kbd>Ctrl+V</kbd></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row g-6">
                    <!-- Cabeçalho Institucional -->
                    <div class="col-md-6">
                        <div class="card border-2 border-light-primary">
                            <div class="card-header border-0 pt-6">
                                <div class="card-title">
                                    <h3 class="fw-bold text-gray-900">
                                        <i class="ki-duotone ki-home-2 fs-2 text-primary me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Cabeçalho Institucional
                                    </h3>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="d-flex flex-column gap-3">
                                    <div class="variable-item" onclick="copiarVariavel('${imagem_cabecalho}', this)">
                                        <code class="text-primary fs-7 fw-bold">${imagem_cabecalho}</code>
                                        <div class="text-muted fs-8">Logotipo da câmara</div>
                                    </div>
                                    <div class="variable-item" onclick="copiarVariavel('${cabecalho_nome_camara}', this)">
                                        <code class="text-primary fs-7 fw-bold">${cabecalho_nome_camara}</code>
                                        <div class="text-muted fs-8">Nome da câmara</div>
                                    </div>
                                    <div class="variable-item" onclick="copiarVariavel('${cabecalho_endereco}', this)">
                                        <code class="text-primary fs-7 fw-bold">${cabecalho_endereco}</code>
                                        <div class="text-muted fs-8">Endereço da câmara</div>
                                    </div>
                                    <div class="variable-item" onclick="copiarVariavel('${cabecalho_telefone}', this)">
                                        <code class="text-primary fs-7 fw-bold">${cabecalho_telefone}</code>
                                        <div class="text-muted fs-8">Telefone da câmara</div>
                                    </div>
                                    <div class="variable-item" onclick="copiarVariavel('${cabecalho_website}', this)">
                                        <code class="text-primary fs-7 fw-bold">${cabecalho_website}</code>
                                        <div class="text-muted fs-8">Website da câmara</div>
                                    </div>
                                    <div class="variable-item" onclick="copiarVariavel('${cnpj_camara}', this)">
                                        <code class="text-primary fs-7 fw-bold">${cnpj_camara}</code>
                                        <div class="text-muted fs-8">CNPJ da câmara</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dados da Proposição -->
                    <div class="col-md-6">
                        <div class="card border-2 border-light-success">
                            <div class="card-header border-0 pt-6">
                                <div class="card-title">
                                    <h3 class="fw-bold text-gray-900">
                                        <i class="ki-duotone ki-document fs-2 text-success me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Dados da Proposição
                                    </h3>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="d-flex flex-column gap-3">
                                    <div class="variable-item" onclick="copiarVariavel('${tipo_proposicao}', this)">
                                        <code class="text-success fs-7 fw-bold">${tipo_proposicao}</code>
                                        <div class="text-muted fs-8">Tipo (Moção, Projeto, etc.)</div>
                                    </div>
                                    <div class="variable-item" onclick="copiarVariavel('${numero_proposicao}', this)">
                                        <code class="text-success fs-7 fw-bold">${numero_proposicao}</code>
                                        <div class="text-muted fs-8">Número da proposição</div>
                                    </div>
                                    <div class="variable-item" onclick="copiarVariavel('${ementa}', this)">
                                        <code class="text-success fs-7 fw-bold">${ementa}</code>
                                        <div class="text-muted fs-8">Ementa da proposição</div>
                                    </div>
                                    <div class="variable-item" onclick="copiarVariavel('${texto}', this)">
                                        <code class="text-success fs-7 fw-bold">${texto}</code>
                                        <div class="text-muted fs-8">Conteúdo principal</div>
                                    </div>
                                    <div class="variable-item" onclick="copiarVariavel('${justificativa}', this)">
                                        <code class="text-success fs-7 fw-bold">${justificativa}</code>
                                        <div class="text-muted fs-8">Justificativa da proposição</div>
                                    </div>
                                    <div class="variable-item" onclick="copiarVariavel('${preambulo_dinamico}', this)">
                                        <code class="text-success fs-7 fw-bold">${preambulo_dinamico}</code>
                                        <div class="text-muted fs-8">Preâmbulo adaptável</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dados do Autor -->
                    <div class="col-md-6">
                        <div class="card border-2 border-light-warning">
                            <div class="card-header border-0 pt-6">
                                <div class="card-title">
                                    <h3 class="fw-bold text-gray-900">
                                        <i class="ki-duotone ki-profile-circle fs-2 text-warning me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        Dados do Autor
                                    </h3>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="d-flex flex-column gap-3">
                                    <div class="variable-item" onclick="copiarVariavel('${autor_nome}', this)">
                                        <code class="text-warning fs-7 fw-bold">${autor_nome}</code>
                                        <div class="text-muted fs-8">Nome do parlamentar</div>
                                    </div>
                                    <div class="variable-item" onclick="copiarVariavel('${autor_cargo}', this)">
                                        <code class="text-warning fs-7 fw-bold">${autor_cargo}</code>
                                        <div class="text-muted fs-8">Cargo (Vereador)</div>
                                    </div>
                                    <div class="variable-item" onclick="copiarVariavel('${assinatura_padrao}', this)">
                                        <code class="text-warning fs-7 fw-bold">${assinatura_padrao}</code>
                                        <div class="text-muted fs-8">Linha de assinatura</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Data e Local -->
                    <div class="col-md-6">
                        <div class="card border-2 border-light-info">
                            <div class="card-header border-0 pt-6">
                                <div class="card-title">
                                    <h3 class="fw-bold text-gray-900">
                                        <i class="ki-duotone ki-calendar fs-2 text-info me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Data e Local
                                    </h3>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="d-flex flex-column gap-3">
                                    <div class="variable-item" onclick="copiarVariavel('${municipio}', this)">
                                        <code class="text-info fs-7 fw-bold">${municipio}</code>
                                        <div class="text-muted fs-8">Nome do município</div>
                                    </div>
                                    <div class="variable-item" onclick="copiarVariavel('${municipio_uf}', this)">
                                        <code class="text-info fs-7 fw-bold">${municipio_uf}</code>
                                        <div class="text-muted fs-8">UF do município</div>
                                    </div>
                                    <div class="variable-item" onclick="copiarVariavel('${dia}', this)">
                                        <code class="text-info fs-7 fw-bold">${dia}</code>
                                        <div class="text-muted fs-8">Dia atual</div>
                                    </div>
                                    <div class="variable-item" onclick="copiarVariavel('${mes_extenso}', this)">
                                        <code class="text-info fs-7 fw-bold">${mes_extenso}</code>
                                        <div class="text-muted fs-8">Mês por extenso</div>
                                    </div>
                                    <div class="variable-item" onclick="copiarVariavel('${ano_atual}', this)">
                                        <code class="text-info fs-7 fw-bold">${ano_atual}</code>
                                        <div class="text-muted fs-8">Ano atual</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rodapé e Contato -->
                    <div class="col-12">
                        <div class="card border-2 border-light-dark">
                            <div class="card-header border-0 pt-6">
                                <div class="card-title">
                                    <h3 class="fw-bold text-gray-900">
                                        <i class="ki-duotone ki-geolocation fs-2 text-dark me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Rodapé e Contato
                                    </h3>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="d-flex flex-column gap-3">
                                            <div class="variable-item" onclick="copiarVariavel('${rodape_texto}', this)">
                                                <code class="text-dark fs-7 fw-bold">${rodape_texto}</code>
                                                <div class="text-muted fs-8">Texto do rodapé</div>
                                            </div>
                                            <div class="variable-item" onclick="copiarVariavel('${endereco_camara}', this)">
                                                <code class="text-dark fs-7 fw-bold">${endereco_camara}</code>
                                                <div class="text-muted fs-8">Endereço completo</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex flex-column gap-3">
                                            <div class="variable-item" onclick="copiarVariavel('${endereco_bairro}', this)">
                                                <code class="text-dark fs-7 fw-bold">${endereco_bairro}</code>
                                                <div class="text-muted fs-8">Bairro</div>
                                            </div>
                                            <div class="variable-item" onclick="copiarVariavel('${endereco_cep}', this)">
                                                <code class="text-dark fs-7 fw-bold">${endereco_cep}</code>
                                                <div class="text-muted fs-8">CEP</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex flex-column gap-3">
                                            <div class="variable-item" onclick="copiarVariavel('${telefone_camara}', this)">
                                                <code class="text-dark fs-7 fw-bold">${telefone_camara}</code>
                                                <div class="text-muted fs-8">Telefone</div>
                                            </div>
                                            <div class="variable-item" onclick="copiarVariavel('${email_camara}', this)">
                                                <code class="text-dark fs-7 fw-bold">${email_camara}</code>
                                                <div class="text-muted fs-8">E-mail institucional</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Toast de Sucesso (oculto por padrão) -->
                <div id="copy-toast" class="position-fixed top-50 start-50 translate-middle" style="z-index: 9999; display: none;">
                    <div class="toast show" role="alert">
                        <div class="toast-header bg-success text-white">
                            <i class="ki-duotone ki-check-circle fs-4 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <strong class="me-auto">Copiado!</strong>
                        </div>
                        <div class="toast-body bg-light-success">
                            <span id="copied-variable"></span> copiada para a área de transferência
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Variáveis-->

    <!-- Bootstrap JS Bundle -->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    
    <!-- OnlyOffice Document Server API -->
    <script src="{{ config('onlyoffice.server_url', 'http://localhost:8080') }}/web-apps/apps/api/documents/api.js"></script>
    
    <script>
let editorInstance = null;
let isEditorReady = false;

// Configuração do OnlyOffice
const onlyOfficeConfig = @json($config);

// Configuração OnlyOffice carregada

// Verificar se o OnlyOffice está disponível
function verificarOnlyOffice() {
    console.log('Verificando OnlyOffice...');
    console.log('DocsAPI disponível:', typeof DocsAPI !== 'undefined');
    console.log('Config URL:', '{{ config("onlyoffice.server_url", "http://localhost:8080") }}');
    console.log('Document Type:', onlyOfficeConfig.documentType);
    console.log('File Type:', onlyOfficeConfig.document.fileType);
    console.log('Document URL:', onlyOfficeConfig.document.url);
}

// Inicializar editor OnlyOffice
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Debug info
        verificarOnlyOffice();
        
        // Verificar se a API do OnlyOffice está disponível
        if (typeof DocsAPI === 'undefined') {
            console.error('OnlyOffice API não carregada');
            console.error('Verifique se o serviço OnlyOffice está rodando em:', '{{ config("onlyoffice.server_url", "http://localhost:8080") }}');
            mostrarErroConexao();
            return;
        }

        // Adicionar header customizado para identificar requisições do OnlyOffice
        if (onlyOfficeConfig.document && onlyOfficeConfig.document.url) {
            // Criar URL com parâmetro de identificação do OnlyOffice
            if (onlyOfficeConfig.document.url.includes('?')) {
                onlyOfficeConfig.document.url += '&onlyoffice=1';
            } else {
                onlyOfficeConfig.document.url += '?onlyoffice=1';
            }
        }

        // Configurar callback do editor com timeouts para evitar erros de canal
        onlyOfficeConfig.events = {
            'onAppReady': function () {
                console.log('OnlyOffice App pronta');
                setTimeout(() => {
                    document.getElementById('loading-overlay').style.display = 'none';
                }, 1000);
            },
            'onDocumentReady': function () {
                console.log('Template universal pronto para edição');
                isEditorReady = true;
                mostrarStatus('Editor carregado com sucesso', 'success');
            },
            'onDocumentStateChange': function(event) {
                if (event && event.data && isEditorReady) {
                    console.log('Documento modificado');
                    mostrarStatus('Salvando alterações...', 'info');
                }
            },
            'onRequestSaveAs': function(event) {
                console.log('Save as requisitado:', event);
            },
            'onError': function (event) {
                console.error('Erro no editor OnlyOffice:', event);
                mostrarErroEditor(event);
            },
            'onWarning': function(event) {
                console.warn('OnlyOffice Warning:', event);
            },
            'onInfo': function(event) {
                if (event && event.data) {
                    console.info('OnlyOffice Info:', event.data);
                    if (typeof event.data === 'string' && event.data.includes('save')) {
                        mostrarStatus('Salvo automaticamente', 'success');
                    }
                }
            }
        };

        // Adicionar timeout para inicialização
        setTimeout(() => {
            try {
                // Inicializar editor
                editorInstance = new DocsAPI.DocEditor("onlyoffice-editor", onlyOfficeConfig);
                
                console.log('Editor OnlyOffice inicializado para template universal:', onlyOfficeConfig.document.title);
                
            } catch (initError) {
                console.error('Erro na inicialização do editor:', initError);
                mostrarErroConexao();
            }
        }, 500);
        
    } catch (error) {
        console.error('Erro ao inicializar OnlyOffice:', error);
        mostrarErroConexao();
    }
});

// Proteção contra saída acidental da página
let isExiting = false;

window.beforeunloadHandler = function(event) {
    if (!isExiting) {
        // A mensagem personalizada não é mais suportada pelos navegadores modernos
        // Eles mostram uma mensagem padrão do navegador
        const message = 'As alterações são salvas automaticamente. Deseja realmente sair?';
        
        // Para navegadores antigos que ainda suportam mensagem personalizada
        event.returnValue = message;
        
        // Para navegadores modernos
        return message;
    }
};

window.addEventListener('beforeunload', window.beforeunloadHandler);

// Função para desabilitar proteção (quando saindo intencionalmente)
function desabilitarProtecaoSaida() {
    isExiting = true;
    window.removeEventListener('beforeunload', window.beforeunloadHandler);
}

// Função para voltar para templates (com lógica inteligente de salvamento)
function voltarParaTemplates() {
    // Se documento foi salvo recentemente, sair direto sem confirmação
    if (documentoFoiSalvo) {
        console.log('Documento foi salvo, saindo sem confirmação...');
        desabilitarProtecaoSaida();
        window.location.href = '{{ route("admin.templates.universal") }}';
        return;
    }
    
    // Mostrar confirmação apenas se documento não foi salvo
    Swal.fire({
        title: 'Deseja sair do editor?',
        text: 'As alterações são salvas automaticamente.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="ki-duotone ki-check fs-6 me-2"><span class="path1"></span><span class="path2"></span></i>Sim, sair',
        cancelButtonText: '<i class="ki-duotone ki-cross fs-6 me-2"><span class="path1"></span><span class="path2"></span></i>Cancelar',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-light'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Desabilitar proteção de saída (saída intencional)
            desabilitarProtecaoSaida();
            
            // Mostrar loading suave
            Swal.fire({
                title: 'Saindo do editor...',
                text: 'Aguarde um momento',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Redirecionar após pequena pausa para melhor UX
            setTimeout(() => {
                window.location.href = '{{ route("admin.templates.universal") }}';
            }, 500);
        }
    });
}

// Função para mostrar status
function mostrarStatus(mensagem, tipo = 'success') {
    const statusEl = document.getElementById('status-indicator');
    const iconHtml = tipo === 'success' 
        ? '<i class="ki-duotone ki-check-circle fs-6 me-1"><span class="path1"></span><span class="path2"></span></i>'
        : '<i class="ki-duotone ki-information-5 fs-6 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>';
    
    statusEl.innerHTML = iconHtml + mensagem;
    statusEl.style.display = 'block';
    statusEl.style.background = tipo === 'success' 
        ? 'rgba(22, 163, 74, 0.9)' 
        : 'rgba(59, 130, 246, 0.9)';
    
    if (tipo === 'success') {
        setTimeout(() => {
            statusEl.style.display = 'none';
        }, 3000);
    }
}

// Variável global para controlar se documento foi salvo
let documentoFoiSalvo = false;

// Função para salvar manualmente (baseado em melhores práticas)
function salvarManual() {
    if (!isEditorReady) {
        Swal.fire({
            icon: 'warning',
            title: 'Editor não está pronto',
            text: 'Aguarde o editor carregar completamente.'
        });
        return;
    }

    // Feedback visual imediato (como nas melhores práticas)
    const btnSalvar = document.querySelector('[onclick="salvarManual()"]');
    if (btnSalvar) {
        btnSalvar.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Salvando...';
        btnSalvar.disabled = true;
    }

    mostrarStatus('Processando alterações...', 'info');
    
    // Force save endpoint (seguindo padrão das melhores práticas)
    fetch(`/api/templates/universal/{{ $template->id }}/force-save`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            document_key: onlyOfficeConfig.document.key
        })
    })
    .then(response => response.json())
    .then(data => {
        // Marca como salvo após 2 segundos (padrão das melhores práticas)
        setTimeout(() => {
            documentoFoiSalvo = true;
            
            if (btnSalvar) {
                btnSalvar.innerHTML = '<i class="ki-duotone ki-check fs-6 me-2"><span class="path1"></span><span class="path2"></span></i>Salvo!';
                btnSalvar.disabled = false;
            }
            
            mostrarStatus('Alterações salvas com sucesso!', 'success');
            
            // Reset do botão após 3 segundos
            setTimeout(() => {
                if (btnSalvar) {
                    btnSalvar.innerHTML = '<i class="ki-duotone ki-file-down fs-6 me-2"><span class="path1"></span><span class="path2"></span></i>Salvar';
                }
            }, 3000);
            
        }, 2000);
    })
    .catch(error => {
        console.error('Erro no salvamento:', error);
        mostrarStatus('Erro ao salvar. Tente novamente.', 'error');
        
        if (btnSalvar) {
            btnSalvar.innerHTML = '<i class="ki-duotone ki-file-down fs-6 me-2"><span class="path1"></span><span class="path2"></span></i>Salvar';
            btnSalvar.disabled = false;
        }
    });
}

// Mostrar ajuda
function mostrarAjuda() {
    const modal = new bootstrap.Modal(document.getElementById('kt_modal_help'));
    modal.show();
}

// Mostrar modal de variáveis
function mostrarVariaveis() {
    const modal = new bootstrap.Modal(document.getElementById('kt_modal_variaveis'));
    modal.show();
}

// Copiar variável para área de transferência
async function copiarVariavel(variavel, elemento) {
    try {
        // Usar a API moderna de clipboard
        await navigator.clipboard.writeText(variavel);
        
        // Adicionar animação ao elemento clicado
        elemento.classList.add('clicked');
        setTimeout(() => {
            elemento.classList.remove('clicked');
        }, 300);
        
        // Mostrar toast de sucesso
        mostrarToastSucesso(variavel);
        
        console.log('Variável copiada:', variavel);
        
    } catch (err) {
        // Fallback para navegadores mais antigos
        try {
            // Criar elemento temporário para seleção
            const tempInput = document.createElement('textarea');
            tempInput.value = variavel;
            tempInput.style.position = 'fixed';
            tempInput.style.left = '-999999px';
            tempInput.style.opacity = '0';
            document.body.appendChild(tempInput);
            
            // Selecionar e copiar
            tempInput.select();
            tempInput.setSelectionRange(0, 99999);
            document.execCommand('copy');
            
            // Remover elemento temporário
            document.body.removeChild(tempInput);
            
            // Feedback visual
            elemento.classList.add('clicked');
            setTimeout(() => {
                elemento.classList.remove('clicked');
            }, 300);
            
            // Mostrar toast
            mostrarToastSucesso(variavel);
            
            console.log('Variável copiada (fallback):', variavel);
            
        } catch (fallbackErr) {
            console.error('Erro ao copiar variável:', fallbackErr);
            
            // Mostrar alerta simples em caso de erro
            alert(`Variável copiada: ${variavel}\n\nCole no seu template usando Ctrl+V`);
        }
    }
}

// Mostrar toast de sucesso
function mostrarToastSucesso(variavel) {
    const toast = document.getElementById('copy-toast');
    const copiedVariable = document.getElementById('copied-variable');
    
    // Atualizar texto do toast
    copiedVariable.textContent = variavel;
    
    // Mostrar toast
    toast.style.display = 'block';
    
    // Ocultar após 2 segundos
    setTimeout(() => {
        toast.style.display = 'none';
    }, 2000);
}

// Mostrar erro de conexão
function mostrarErroConexao() {
    const loadingOverlay = document.getElementById('loading-overlay');
    loadingOverlay.innerHTML = `
        <div style="text-align: center; padding: 40px;">
            <i class="ki-duotone ki-disconnect" style="font-size: 4rem; color: #dc3545; margin-bottom: 20px;">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
                <span class="path4"></span>
                <span class="path5"></span>
            </i>
            <h3 style="color: #181c32; margin-bottom: 16px; font-weight: 600;">Erro de Conexão com OnlyOffice</h3>
            <div style="color: #6c757d; margin-bottom: 24px; line-height: 1.5;">
                Não foi possível conectar ao servidor OnlyOffice.<br>
                Verifique se o serviço está rodando corretamente.
            </div>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button class="btn-editor btn-editor-success" onclick="location.reload()">
                    <i class="ki-duotone ki-arrows-circle fs-6 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Tentar Novamente
                </button>
                <button class="btn-editor btn-editor-light" onclick="voltarParaTemplates()">
                    <i class="ki-duotone ki-arrow-left fs-6 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Voltar
                </button>
            </div>
        </div>
    `;
}

// Mostrar erro do editor
function mostrarErroEditor(event) {
    console.error('Detalhes do erro OnlyOffice:', event);
    
    Swal.fire({
        icon: 'error',
        title: 'Erro no Editor',
        text: 'Ocorreu um erro no editor OnlyOffice. Tente recarregar a página.',
        confirmButtonText: 'Recarregar',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            location.reload();
        }
    });
}

// Cleanup ao sair da página
window.addEventListener('beforeunload', function() {
    if (editorInstance) {
        editorInstance = null;
    }
});

// Atalhos de teclado
document.addEventListener('keydown', function(e) {
    // Ctrl+S para salvar
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        salvarManual();
        return false;
    }
    
    // ESC para voltar
    if (e.key === 'Escape') {
        e.preventDefault();
        voltarParaTemplates();
        return false;
    }
    
    // F1 para ajuda
    if (e.key === 'F1') {
        e.preventDefault();
        mostrarAjuda();
        return false;
    }
});

console.log('Template Universal Editor carregado - Versão Full Screen');
</script>

</body>
</html>