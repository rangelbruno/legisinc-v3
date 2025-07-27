<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editar Proposição - OnlyOffice</title>
    
    <!-- OnlyOffice Document Server API -->
    <script type="text/javascript" src="http://localhost:8080/web-apps/apps/api/documents/api.js"></script>
    
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            overflow: hidden;
            padding-top: 70px; /* Espaço para o header fixo */
        }
        
        .editor-header {
            background: #fff;
            border-bottom: 1px solid #E4E6EA;
            padding: 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 70px;
            box-sizing: border-box;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .editor-toolbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100%;
            padding: 0 2rem;
        }
        
        .editor-title-section {
            display: flex;
            flex-column;
            justify-content: center;
        }
        
        .editor-title {
            margin: 0;
            font-size: 1.275rem;
            font-weight: 600;
            color: #181C32;
            line-height: 1.2;
        }
        
        .editor-breadcrumb {
            display: flex;
            align-items: center;
            margin: 0;
            padding: 0;
            list-style: none;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 2px;
        }
        
        .breadcrumb-item {
            display: flex;
            align-items: center;
        }
        
        .breadcrumb-item:not(:first-child)::before {
            content: '';
            display: inline-block;
            width: 5px;
            height: 2px;
            background: #B5B5C3;
            margin: 0 0.5rem;
        }
        
        .breadcrumb-item a {
            color: #A1A5B7;
            text-decoration: none;
            transition: color 0.15s ease;
        }
        
        .breadcrumb-item a:hover {
            color: #009EF7;
        }
        
        .breadcrumb-item.active {
            color: #A1A5B7;
        }
        
        .editor-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.575rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            line-height: 1.5;
            border-radius: 0.475rem;
            border: 1px solid transparent;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.5rem 0.75rem;
            font-size: 0.8125rem;
        }
        
        .btn-secondary {
            background: #F5F8FA;
            border-color: #E4E6EA;
            color: #7E8299;
        }
        
        .btn-secondary:hover {
            background: #E4E6EA;
            color: #5E6278;
        }
        
        .btn-primary {
            background: #009EF7;
            border-color: #009EF7;
            color: #ffffff;
        }
        
        .btn-primary:hover {
            background: #0095E8;
            border-color: #0095E8;
        }
        
        .btn-success {
            background: #50CD89;
            border-color: #50CD89;
            color: #ffffff;
        }
        
        .btn-success:hover {
            background: #47BE7D;
            border-color: #47BE7D;
        }
        
        .btn:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }
        
        .ki-duotone {
            display: inline-flex;
            font-style: normal;
            font-weight: normal;
            font-variant: normal;
            line-height: 1;
        }
        
        #onlyoffice-placeholder {
            width: 100%;
            height: calc(100vh - 70px);
            border: none;
            position: relative;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .loading-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 70px);
            flex-direction: column;
            gap: 20px;
            position: relative;
            background: #ffffff;
            z-index: 999;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="editor-header">
        <div class="editor-toolbar-container">
            <!--begin::Page title-->
            <div class="editor-title-section">
                <h1 class="editor-title">
                    <i class="ki-duotone ki-document fs-2 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Editando Proposição {{ $proposicao->id ?? '' }}
                </h1>
            </div>
            <!--end::Page title-->
            
            <!--begin::Actions-->
            <div class="editor-actions">
                <button id="btn-salvar" class="btn btn-sm btn-primary" onclick="salvarDocumento()">
                    <i class="ki-duotone ki-save fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Salvar
                </button>
                <button id="btn-fechar" class="btn btn-sm btn-secondary" onclick="fecharAba()">
                    <i class="ki-duotone ki-cross fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Fechar
                </button>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    
    <div id="loading-container" class="loading-container">
        <div class="spinner"></div>
        <p>Carregando editor OnlyOffice...</p>
    </div>
    
    <div id="onlyoffice-placeholder"></div>

    <script type="text/javascript">
        let docEditor;
        
        document.addEventListener('DOMContentLoaded', function() {
            inicializarOnlyOffice();
        });
        
        // Função para gerar JWT (versão simplificada - em produção usar biblioteca adequada)
        function generateJWT() {
            const header = {
                "alg": "HS256",
                "typ": "JWT"
            };
            
            const payload = {
                "iss": "legisinc",
                "aud": "onlyoffice",
                "iat": Math.floor(Date.now() / 1000),
                "exp": Math.floor(Date.now() / 1000) + 3600 // 1 hora
            };
            
            // Para desenvolvimento, retornar null para desabilitar JWT
            return null;
        }
        
        function inicializarOnlyOffice() {
            // Calcular dimensões do editor
            const headerHeight = 70;
            const editorHeight = window.innerHeight - headerHeight;
            
            const config = {
                "width": "100%",
                "height": editorHeight + "px",
                "type": "desktop",
                "documentType": "word",
                "document": {
                    "fileType": "docx",
                    "key": "{{ $documentKey }}",
                    "title": "Proposição {{ $proposicao->id ?? '' }} - {{ $template ? ($template->tipoProposicao->nome ?? $template->nome) : 'Template em Branco' }}",
                    "url": "http://host.docker.internal:8001/onlyoffice/file/proposicao/{{ $proposicao->id ?? 1 }}/{{ $arquivoProposicao }}",
                    "permissions": {
                        "comment": true,
                        "download": true,
                        "edit": true,
                        "fillForms": true,
                        "modifyFilter": true,
                        "modifyContentControl": true,
                        "review": true,
                        "chat": false,
                        "copy": true,
                        "print": true
                    }
                },
                "editorConfig": {
                    "mode": "edit",
                    "lang": "pt-BR",
                    "callbackUrl": "http://host.docker.internal:8001/api/onlyoffice/callback/proposicao/{{ $proposicao->id ?? 1 }}",
                    "user": {
                        "id": "{{ auth()->id() }}",
                        "name": "{{ auth()->user()->name }}",
                        "group": "users"
                    },
                    "customization": {
                        "about": false,
                        "feedback": false,
                        "forcesave": true,
                        "goback": {
                            "blank": false,
                            "text": "Voltar às Proposições",
                            "url": "{{ route('proposicoes.minhas-proposicoes') }}"
                        },
                        "logo": {
                            "image": "",
                            "imageEmbedded": "",
                            "url": "{{ route('dashboard') }}"
                        },
                        "reviewDisplay": "markup",
                        "showReviewChanges": false,
                        "toolbarNoTabs": false,
                        "toolbarHideFileName": true,
                        "zoom": 100,
                        "compactToolbar": false,
                        "leftMenu": true,
                        "rightMenu": true,
                        "toolbar": true,
                        "statusBar": true,
                        "autosave": true
                    },
                    "plugins": {
                        "autostart": [],
                        "pluginsData": []
                    }
                },
                "events": {
                    "onAppReady": function() {
                        console.log("OnlyOffice está pronto");
                        const loadingContainer = document.getElementById('loading-container');
                        if (loadingContainer) {
                            loadingContainer.style.display = 'none';
                        }
                        
                        // Garantir que o editor está visível
                        const placeholder = document.getElementById('onlyoffice-placeholder');
                        if (placeholder) {
                            placeholder.style.visibility = 'visible';
                            placeholder.style.opacity = '1';
                        }
                    },
                    "onDocumentStateChange": function(event) {
                        console.log("Estado do documento alterado:", event);
                        // Documento foi modificado
                        if (event.data) {
                            const btnSalvar = document.getElementById('btn-salvar');
                            btnSalvar.innerHTML = `
                                <i class="ki-duotone ki-save fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Salvar*
                            `;
                            btnSalvar.className = 'btn btn-sm btn-success';
                        }
                    },
                    "onError": function(event) {
                        console.error("Erro detalhado no OnlyOffice:", event);
                        console.error("Tipo do evento:", typeof event);
                        console.error("Propriedades do evento:", Object.keys(event));
                        
                        let mensagemErro = "Erro desconhecido";
                        let errorCode = "N/A";
                        
                        if (event && event.data) {
                            if (typeof event.data === 'object') {
                                mensagemErro = JSON.stringify(event.data);
                                errorCode = event.data.errorCode || "N/A";
                            } else {
                                mensagemErro = event.data.toString();
                            }
                        }
                        
                        // Log detalhado do erro
                        console.error("Error Code:", errorCode);
                        console.error("Full Event Object:", JSON.stringify(event, null, 2));
                        
                        document.getElementById('loading-container').innerHTML = `
                            <div style="text-align: center; color: #dc3545; padding: 20px;">
                                <h3>❌ Erro no OnlyOffice</h3>
                                <p><strong>Código:</strong> ${errorCode}</p>
                                <p><strong>Detalhes:</strong> ${mensagemErro}</p>
                                <div style="margin: 20px 0;">
                                    <button onclick="location.reload()" class="btn btn-secondary">
                                        🔄 Tentar Novamente
                                    </button>
                                    <a href="{{ route('proposicoes.editar-texto', $proposicao->id ?? 1) }}" class="btn btn-primary" style="margin-left: 10px;">
                                        📝 Editor de Texto Simples
                                    </a>
                                </div>
                                <details style="margin-top: 20px; text-align: left;">
                                    <summary>Informações de Debug</summary>
                                    <pre style="background: #f8f9fa; padding: 10px; border-radius: 4px; font-size: 12px;">
Document URL: http://host.docker.internal:8001/onlyoffice/file/proposicao/{{ $proposicao->id ?? 1 }}/{{ $arquivoProposicao }}
Callback URL: http://host.docker.internal:8001/api/onlyoffice/callback/proposicao/{{ $proposicao->id ?? 1 }}
Document Key: {{ $documentKey }}
OnlyOffice Server: http://localhost:8080
                                    </pre>
                                </details>
                            </div>
                        `;
                        document.getElementById('loading-container').style.display = 'flex';
                    },
                    "onInfo": function(event) {
                        console.log("Info do OnlyOffice:", event);
                    },
                    "onWarning": function(event) {
                        console.warn("Warning do OnlyOffice:", event);
                    },
                    "onRequestSaveAs": function(event) {
                        console.log("Save As requisitado:", event);
                    },
                    "onRequestSave": function() {
                        console.log("Salvamento requisitado pelo usuário");
                    },
                    "onSave": function(event) {
                        console.log("Documento salvo com sucesso:", event);
                        // Atualizar botão para indicar que foi salvo
                        const btnSalvar = document.getElementById('btn-salvar');
                        btnSalvar.innerHTML = `
                            <i class="ki-duotone ki-check fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Salvo!
                        `;
                        btnSalvar.className = 'btn btn-sm btn-success';
                        
                        setTimeout(function() {
                            btnSalvar.innerHTML = `
                                <i class="ki-duotone ki-save fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Salvar
                            `;
                            btnSalvar.className = 'btn btn-sm btn-primary';
                        }, 3000);
                    }
                }
            };
            
            console.log("Inicializando OnlyOffice com configuração:", config);
            
            try {
                docEditor = new DocsAPI.DocEditor("onlyoffice-placeholder", config);
                console.log("OnlyOffice inicializado com sucesso");
            } catch (error) {
                console.error("Erro ao inicializar OnlyOffice:", error);
                document.getElementById('loading-container').innerHTML = `
                    <div style="text-align: center; color: #dc3545;">
                        <h3>❌ Erro ao carregar o editor</h3>
                        <p>Não foi possível conectar ao servidor OnlyOffice.</p>
                        <p style="font-size: 12px; color: #6c757d;">Erro: ${error.message}</p>
                        <a href="{{ route('proposicoes.editar-texto', $proposicao->id ?? 1) }}" class="btn btn-primary">
                            Usar Editor de Texto Simples
                        </a>
                    </div>
                `;
            }
        }
        
        function salvarDocumento() {
            if (docEditor) {
                console.log("Salvando documento...");
                
                // Mostrar mensagem de salvamento
                const btnSalvar = document.getElementById('btn-salvar');
                btnSalvar.innerHTML = `
                    <i class="ki-duotone ki-loading fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Salvando...
                `;
                btnSalvar.disabled = true;
                
                // O OnlyOffice salva automaticamente quando há mudanças
                // Vamos apenas dar feedback visual ao usuário
                // O callback com status 2 será chamado automaticamente pelo OnlyOffice
                
                setTimeout(function() {
                    btnSalvar.innerHTML = `
                        <i class="ki-duotone ki-check fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Salvo!
                    `;
                    btnSalvar.className = 'btn btn-sm btn-success';
                    btnSalvar.disabled = false;
                    
                    // Voltar ao estado normal após mais 2 segundos
                    setTimeout(function() {
                        btnSalvar.innerHTML = `
                            <i class="ki-duotone ki-save fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Salvar
                        `;
                        btnSalvar.className = 'btn btn-sm btn-primary';
                    }, 2000);
                }, 1500);
                
                // Fazer uma requisição para forçar o salvamento via callback
                // Isso irá disparar o OnlyOffice para enviar o callback com o arquivo atualizado
                fetch('/proposicoes/{{ $proposicao->id ?? 1 }}/atualizar-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        status: 'salvando'
                    })
                });
            } else {
                alert("Editor não está pronto ainda.");
            }
        }
        
        function fecharAba() {
            // Verificar se há alterações não salvas
            const btnSalvar = document.getElementById('btn-salvar');
            if (btnSalvar && btnSalvar.innerHTML.includes('*')) {
                if (!confirm('Você tem alterações não salvas. Deseja realmente fechar?')) {
                    return;
                }
            }
            
            // Navigate back instead of trying to close window
            window.history.back();
            
            // Fallback: redirect to propositions list
            setTimeout(function() {
                window.location.href = "{{ route('proposicoes.minhas-proposicoes') }}";
            }, 100);
        }
        
        // Verificar se o OnlyOffice está disponível
        window.addEventListener('load', function() {
            setTimeout(function() {
                if (typeof DocsAPI === 'undefined') {
                    console.error("OnlyOffice API não está disponível");
                    document.getElementById('loading-container').innerHTML = `
                        <div style="text-align: center; color: #dc3545;">
                            <h3>❌ OnlyOffice não disponível</h3>
                            <p>O servidor OnlyOffice não está acessível em: http://localhost:8080</p>
                            <p style="font-size: 12px; color: #6c757d;">Verifique se o servidor OnlyOffice está rodando.</p>
                            <div style="margin: 20px 0;">
                                <a href="{{ route('proposicoes.editar-texto', $proposicao->id ?? 1) }}" class="btn btn-primary">
                                    📝 Usar Editor de Texto Simples
                                </a>
                                <button onclick="location.reload()" class="btn btn-secondary" style="margin-left: 10px;">
                                    🔄 Tentar Novamente
                                </button>
                            </div>
                        </div>
                    `;
                }
            }, 2000); // Aguardar 2 segundos para garantir que o script foi carregado
        });
        
        // Redimensionar editor quando a janela mudar de tamanho
        window.addEventListener('resize', function() {
            if (docEditor) {
                const headerHeight = 70;
                const newHeight = window.innerHeight - headerHeight;
                try {
                    docEditor.resize();
                } catch (e) {
                    console.log('Resize not supported by OnlyOffice version');
                }
            }
        });

        // Prevenir saída sem salvar
        window.addEventListener('beforeunload', function(e) {
            // Se o documento foi modificado, avisar sobre possível perda de dados
            const btnSalvar = document.getElementById('btn-salvar');
            if (btnSalvar && btnSalvar.innerHTML.includes('*')) {
                e.preventDefault();
                e.returnValue = 'Você tem alterações não salvas. Deseja realmente sair?';
                return e.returnValue;
            }
        });
    </script>
</body>
</html>