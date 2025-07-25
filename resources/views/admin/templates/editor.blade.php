<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor: {{ $tipo->nome }}</title>
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet">
    
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
        }
        
        .editor-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        
        .editor-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .editor-content {
            flex: 1;
            position: relative;
        }
        
        #onlyoffice-editor {
            width: 100%;
            height: 100%;
        }
        
        .variables-panel {
            position: fixed;
            top: 80px;
            right: 20px;
            width: 280px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            z-index: 999;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .variables-panel.collapsed {
            height: 50px;
            overflow: hidden;
        }
        
        .panel-toggle {
            cursor: pointer;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            background: #f8f9fa;
            border-radius: 10px 10px 0 0;
        }
        
        .panel-content {
            padding: 15px;
        }
    </style>
</head>
<body>
    <div class="editor-container">
        <!-- Header -->
        <div class="editor-header">
            <div class="d-flex align-items-center">
                <i class="ki-duotone ki-document fs-2 me-3">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <div>
                    <h4 class="mb-0">Template: {{ $tipo->nome }}</h4>
                    <small class="opacity-75">Editor de Template de Documento</small>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <span class="badge badge-light-success px-3 py-2">
                    <i class="ki-duotone ki-check fs-7 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Salvamento Automático
                </span>
                
                <a href="{{ route('admin.templates.index') }}" class="btn btn-light btn-sm">
                    <i class="ki-duotone ki-cross fs-6 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Fechar
                </a>
            </div>
        </div>
        
        <!-- Editor -->
        <div class="editor-content">
            <div id="onlyoffice-editor"></div>
        </div>
    </div>

    <!-- Variables Panel -->
    <div class="variables-panel" id="variablesPanel">
        <div class="panel-toggle" onclick="togglePanel()">
            <div class="d-flex justify-content-between align-items-center">
                <strong class="text-primary">
                    <i class="ki-duotone ki-code fs-5 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                    Variáveis
                </strong>
                <i class="ki-duotone ki-up fs-6" id="panelIcon">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
        </div>
        
        <div class="panel-content" id="panelContent">
            <div class="mb-3">
                <strong class="fs-7 text-muted">DADOS DA PROPOSIÇÃO</strong>
                <div class="mt-2">
                    <div class="mb-2"><code class="fs-8">${numero_proposicao}</code> <small class="text-muted">- Número</small></div>
                    <div class="mb-2"><code class="fs-8">${ementa}</code> <small class="text-muted">- Ementa</small></div>
                    <div class="mb-2"><code class="fs-8">${texto}</code> <small class="text-muted">- Texto principal</small></div>
                </div>
            </div>
            
            <div class="mb-3">
                <strong class="fs-7 text-muted">AUTOR & DATA</strong>
                <div class="mt-2">
                    <div class="mb-2"><code class="fs-8">${autor_nome}</code> <small class="text-muted">- Nome do autor</small></div>
                    <div class="mb-2"><code class="fs-8">${data_atual}</code> <small class="text-muted">- Data atual</small></div>
                </div>
            </div>
            
            <div class="mb-3">
                <strong class="fs-7 text-muted">LOCALIZAÇÃO</strong>
                <div class="mt-2">
                    <div class="mb-2"><code class="fs-8">${municipio}</code> <small class="text-muted">- Nome do município</small></div>
                </div>
            </div>
            
            <div class="bg-light p-3 rounded">
                <div class="fs-8 text-muted">
                    <strong>💡 Dica:</strong> Copie e cole as variáveis no documento. Elas serão substituídas automaticamente ao gerar documentos.
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ config('onlyoffice.server_url') }}/web-apps/apps/api/documents/api.js"></script>
    <script>
        // Toggle panel de variáveis
        function togglePanel() {
            const panel = document.getElementById('variablesPanel');
            const icon = document.getElementById('panelIcon');
            
            panel.classList.toggle('collapsed');
            
            if (panel.classList.contains('collapsed')) {
                icon.className = 'ki-duotone ki-down fs-6';
                icon.innerHTML = '<span class="path1"></span><span class="path2"></span>';
            } else {
                icon.className = 'ki-duotone ki-up fs-6';
                icon.innerHTML = '<span class="path1"></span><span class="path2"></span>';
            }
        }

        // OnlyOffice Config
        document.addEventListener('DOMContentLoaded', function() {
            const config = @json($config);
            
            console.log('OnlyOffice Config:', config);
            console.log('Document URL:', config.document.url);
            
            config.events = {
                'onDocumentReady': function() {
                    console.log('Template carregado para edição');
                    showToast('Template carregado com sucesso!', 'success');
                },
                'onError': function(event) {
                    console.error('Erro OnlyOffice:', event);
                    showToast('Erro no editor: ' + JSON.stringify(event.data), 'error');
                }
            };
            
            try {
                console.log('Inicializando OnlyOffice com config:', config);
                new DocsAPI.DocEditor('onlyoffice-editor', config);
            } catch (error) {
                console.error('Erro ao inicializar OnlyOffice:', error);
                showToast('Erro ao carregar editor: ' + error.message, 'error');
            }
        });

        // Toast notifications
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible position-fixed`;
            toast.style.cssText = 'top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                ${message}
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 5000);
        }
    </script>
</body>
</html>