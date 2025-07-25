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
        }
        
        .editor-header {
            background: #343a40;
            color: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 60px;
            box-sizing: border-box;
        }
        
        .editor-title {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }
        
        .editor-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        #onlyoffice-placeholder {
            width: 100%;
            height: calc(100vh - 60px);
            border: none;
            position: absolute;
            top: 60px;
            left: 0;
            right: 0;
            bottom: 0;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .loading-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 60px);
            flex-direction: column;
            gap: 20px;
            position: absolute;
            top: 60px;
            left: 0;
            right: 0;
            bottom: 0;
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
        <h1 class="editor-title">
            Editando Proposição {{ $proposicao->id ?? '' }} - {{ $template->tipoProposicao->nome ?? 'Template' }}
        </h1>
        <div class="editor-actions">
            <a href="{{ route('proposicoes.minhas-proposicoes') }}" class="btn btn-secondary">
                ← Voltar às Proposições
            </a>
            <button id="btn-salvar" class="btn btn-primary" onclick="salvarDocumento()">
                💾 Salvar
            </button>
            <button id="btn-fechar" class="btn btn-secondary" onclick="fecharAba()">
                ✕ Fechar Aba
            </button>
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
            const headerHeight = 60;
            const editorHeight = window.innerHeight - headerHeight;
            
            const config = {
                "width": "100%",
                "height": editorHeight + "px",
                "type": "desktop",
                "documentType": "word",
                "document": {
                    "fileType": "docx",
                    "key": "{{ $documentKey }}",
                    "title": "Proposição {{ $proposicao->id ?? '' }} - {{ $template->tipoProposicao->nome ?? 'Template' }}",
                    "url": "http://172.24.0.2:80/onlyoffice/file/proposicao/{{ $proposicao->id ?? 1 }}/{{ $arquivoProposicao }}",
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
                    "callbackUrl": "http://172.24.0.2:80/api/onlyoffice/callback/proposicao/{{ $proposicao->id ?? 1 }}",
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
                            document.getElementById('btn-salvar').innerHTML = '💾 Salvar*';
                            document.getElementById('btn-salvar').style.background = '#28a745';
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
Document URL: http://172.24.0.2:80/onlyoffice/file/proposicao/{{ $proposicao->id ?? 1 }}/{{ $arquivoProposicao }}
Callback URL: http://172.24.0.2:80/api/onlyoffice/callback/proposicao/{{ $proposicao->id ?? 1 }}
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
                        document.getElementById('btn-salvar').innerHTML = '💾 Salvo!';
                        document.getElementById('btn-salvar').style.background = '#28a745';
                        
                        setTimeout(function() {
                            document.getElementById('btn-salvar').innerHTML = '💾 Salvar';
                            document.getElementById('btn-salvar').style.background = '#007bff';
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
                document.getElementById('btn-salvar').innerHTML = '💾 Salvando...';
                document.getElementById('btn-salvar').disabled = true;
                
                // O OnlyOffice salva automaticamente quando há mudanças
                // Vamos apenas dar feedback visual ao usuário
                // O callback com status 2 será chamado automaticamente pelo OnlyOffice
                
                setTimeout(function() {
                    document.getElementById('btn-salvar').innerHTML = '💾 Salvo!';
                    document.getElementById('btn-salvar').style.background = '#28a745';
                    document.getElementById('btn-salvar').disabled = false;
                    
                    // Voltar ao estado normal após mais 2 segundos
                    setTimeout(function() {
                        document.getElementById('btn-salvar').innerHTML = '💾 Salvar';
                        document.getElementById('btn-salvar').style.background = '#007bff';
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
                const headerHeight = 60;
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