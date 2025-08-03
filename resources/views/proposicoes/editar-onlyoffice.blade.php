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
        
        /* Switch Alert System */
        .switch-alert-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
        }
        
        .switch-alert {
            background: white;
            border-radius: 8px;
            padding: 24px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: switchAlertFadeIn 0.3s ease-out;
        }
        
        @keyframes switchAlertFadeIn {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        
        .switch-alert-header {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }
        
        .switch-alert-icon {
            width: 24px;
            height: 24px;
            margin-right: 12px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        
        .switch-alert-icon.success {
            background: #50CD89;
            color: white;
        }
        
        .switch-alert-icon.error {
            background: #F1416C;
            color: white;
        }
        
        .switch-alert-icon.warning {
            background: #FFC700;
            color: white;
        }
        
        .switch-alert-icon.info {
            background: #009EF7;
            color: white;
        }
        
        .switch-alert-title {
            font-size: 16px;
            font-weight: 600;
            color: #181C32;
            margin: 0;
        }
        
        .switch-alert-message {
            color: #5E6278;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        
        .switch-alert-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }
        
        .switch-alert-btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid #E4E6EA;
            background: #F5F8FA;
            color: #5E6278;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        
        .switch-alert-btn:hover {
            background: #E4E6EA;
        }
        
        .switch-alert-btn.primary {
            background: #009EF7;
            border-color: #009EF7;
            color: white;
        }
        
        .switch-alert-btn.primary:hover {
            background: #0095E8;
        }
        
        .switch-alert-btn.danger {
            background: #F1416C;
            border-color: #F1416C;
            color: white;
        }
        
        .switch-alert-btn.danger:hover {
            background: #E6365F;
        }
        
        /* Toast notifications */
        .toast-container {
            position: fixed;
            top: 90px;
            right: 20px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .toast {
            background: white;
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-left: 4px solid #009EF7;
            min-width: 300px;
            animation: toastSlideIn 0.3s ease-out;
        }
        
        @keyframes toastSlideIn {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .toast.success {
            border-left-color: #50CD89;
        }
        
        .toast.error {
            border-left-color: #F1416C;
        }
        
        .toast.warning {
            border-left-color: #FFC700;
        }
        
        .toast-header {
            display: flex;
            align-items: center;
            margin-bottom: 4px;
        }
        
        .toast-icon {
            width: 16px;
            height: 16px;
            margin-right: 8px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }
        
        .toast-icon.success {
            background: #50CD89;
            color: white;
        }
        
        .toast-icon.error {
            background: #F1416C;
            color: white;
        }
        
        .toast-icon.warning {
            background: #FFC700;
            color: white;
        }
        
        .toast-icon.info {
            background: #009EF7;
            color: white;
        }
        
        .toast-title {
            font-size: 14px;
            font-weight: 600;
            color: #181C32;
        }
        
        .toast-message {
            font-size: 13px;
            color: #5E6278;
            line-height: 1.4;
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
    
    <!-- Switch Alert Overlay -->
    <div id="switch-alert-overlay" class="switch-alert-overlay">
        <div class="switch-alert">
            <div class="switch-alert-header">
                <div id="switch-alert-icon" class="switch-alert-icon"></div>
                <h3 id="switch-alert-title" class="switch-alert-title"></h3>
            </div>
            <div id="switch-alert-message" class="switch-alert-message"></div>
            <div id="switch-alert-actions" class="switch-alert-actions"></div>
        </div>
    </div>
    
    <!-- Toast Container -->
    <div id="toast-container" class="toast-container"></div>

    <script type="text/javascript">
        let docEditor;
        
        // Switch Alert System
        class SwitchAlert {
            static show(type, title, message, actions = []) {
                const overlay = document.getElementById('switch-alert-overlay');
                const icon = document.getElementById('switch-alert-icon');
                const titleEl = document.getElementById('switch-alert-title');
                const messageEl = document.getElementById('switch-alert-message');
                const actionsEl = document.getElementById('switch-alert-actions');
                
                // Set icon based on type
                const icons = {
                    success: '✓',
                    error: '✕',
                    warning: '⚠',
                    info: 'i'
                };
                
                icon.className = `switch-alert-icon ${type}`;
                icon.textContent = icons[type] || 'i';
                titleEl.textContent = title;
                messageEl.textContent = message;
                
                // Clear previous actions
                actionsEl.innerHTML = '';
                
                // Add actions
                if (actions.length === 0) {
                    actions = [{ text: 'OK', action: () => SwitchAlert.hide(), primary: true }];
                }
                
                actions.forEach(action => {
                    const btn = document.createElement('button');
                    btn.className = `switch-alert-btn ${action.primary ? 'primary' : ''} ${action.danger ? 'danger' : ''}`;
                    btn.textContent = action.text;
                    btn.onclick = () => {
                        if (action.action) action.action();
                        SwitchAlert.hide();
                    };
                    actionsEl.appendChild(btn);
                });
                
                overlay.style.display = 'flex';
            }
            
            static hide() {
                const overlay = document.getElementById('switch-alert-overlay');
                overlay.style.display = 'none';
            }
            
            static confirm(title, message, onConfirm, onCancel) {
                this.show('warning', title, message, [
                    { text: 'Cancelar', action: onCancel },
                    { text: 'Confirmar', action: onConfirm, primary: true }
                ]);
            }
        }
        
        // Toast Notification System
        class Toast {
            static show(type, title, message, duration = 4000) {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');
                toast.className = `toast ${type}`;
                
                const icons = {
                    success: '✓',
                    error: '✕',
                    warning: '⚠',
                    info: 'i'
                };
                
                toast.innerHTML = `
                    <div class="toast-header">
                        <div class="toast-icon ${type}">${icons[type] || 'i'}</div>
                        <div class="toast-title">${title}</div>
                    </div>
                    <div class="toast-message">${message}</div>
                `;
                
                container.appendChild(toast);
                
                // Auto-remove after duration
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.style.opacity = '0';
                        toast.style.transform = 'translateX(100%)';
                        setTimeout(() => {
                            if (toast.parentNode) {
                                container.removeChild(toast);
                            }
                        }, 300);
                    }
                }, duration);
            }
            
            static success(title, message, duration) {
                this.show('success', title, message, duration);
            }
            
            static error(title, message, duration) {
                this.show('error', title, message, duration);
            }
            
            static warning(title, message, duration) {
                this.show('warning', title, message, duration);
            }
            
            static info(title, message, duration) {
                this.show('info', title, message, duration);
            }
        }
        
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
                    "fileType": "rtf",
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
                    "region": "pt-BR", 
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
                        "spellcheck": {
                            "mode": true,
                            "lang": ["pt-BR"]
                        },
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
                            updateUnsavedState(true);
                            Toast.info('Documento modificado', 'Lembre-se de salvar suas alterações.', 2000);
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
                        console.log("OnlyOffice onSave event:", event);
                        
                        // Limpar timeout de segurança
                        if (saveTimeout) {
                            clearTimeout(saveTimeout);
                            saveTimeout = null;
                        }
                        
                        // Resetar estado de salvamento
                        isSaving = false;
                        
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
                        btnSalvar.disabled = false;
                        
                        // AGORA SIM - documento realmente salvo pelo OnlyOffice
                        updateUnsavedState(false);
                        Toast.success('Documento salvo', 'Suas alterações foram salvas com sucesso!', 3000);
                        
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
        
        // Estado do salvamento
        let isSaving = false;
        let saveTimeout = null;
        
        function salvarDocumento() {
            if (docEditor && !isSaving) {
                console.log("Forçando salvamento do documento...");
                isSaving = true;
                
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
                
                // IMPORTANTE: Forçar o OnlyOffice a salvar o documento imediatamente
                try {
                    docEditor.requestSave();
                    console.log("requestSave() chamado com sucesso");
                    
                    // Timeout de segurança - se OnlyOffice não responder em 10 segundos
                    saveTimeout = setTimeout(function() {
                        console.warn("Timeout ao salvar - OnlyOffice não respondeu");
                        isSaving = false;
                        btnSalvar.innerHTML = `
                            <i class="ki-duotone ki-cross fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Erro ao salvar
                        `;
                        btnSalvar.className = 'btn btn-sm btn-danger';
                        btnSalvar.disabled = false;
                        
                        Toast.error('Erro ao salvar', 'Timeout - tente novamente', 5000);
                        
                        // Voltar ao estado normal após 3 segundos
                        setTimeout(function() {
                            btnSalvar.innerHTML = `
                                <i class="ki-duotone ki-save fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Salvar*
                            `;
                            btnSalvar.className = 'btn btn-sm btn-success';
                        }, 3000);
                    }, 10000);
                    
                } catch (error) {
                    console.error("Erro ao chamar requestSave():", error);
                    isSaving = false;
                    btnSalvar.innerHTML = `
                        <i class="ki-duotone ki-cross fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Erro
                    `;
                    btnSalvar.className = 'btn btn-sm btn-danger';
                    btnSalvar.disabled = false;
                    
                    SwitchAlert.show('error', 'Erro ao salvar', 'Não foi possível salvar o documento. Tente novamente.');
                }
            } else if (isSaving) {
                Toast.warning('Salvamento em andamento', 'Aguarde o salvamento atual terminar.');
            } else {
                SwitchAlert.show('warning', 'Editor não está pronto', 'Aguarde o editor OnlyOffice carregar completamente antes de salvar.');
            }
        }
        
        function fecharAba() {
            console.log('fecharAba called, hasUnsavedChanges:', hasUnsavedChanges, 'isSaving:', isSaving);
            
            // Se está salvando, avisar para aguardar
            if (isSaving) {
                SwitchAlert.show('warning', 'Salvamento em andamento', 'Aguarde o documento ser salvo antes de fechar o editor.');
                return;
            }
            
            // Verificar se há alterações não salvas
            if (hasUnsavedChanges) {
                SwitchAlert.confirm(
                    'Sair do site?',
                    'As alterações que você fez talvez não sejam salvas.',
                    function() {
                        // Navigate back instead of trying to close window
                        preventUnload = true;
                        window.history.back();
                        
                        // Fallback: redirect to propositions list
                        setTimeout(function() {
                            window.location.href = "{{ route('proposicoes.minhas-proposicoes') }}";
                        }, 100);
                    }
                );
            } else {
                // Navigate back instead of trying to close window
                window.history.back();
                
                // Fallback: redirect to propositions list
                setTimeout(function() {
                    window.location.href = "{{ route('proposicoes.minhas-proposicoes') }}";
                }, 100);
            }
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

        // Variável para controlar se deve mostrar aviso ao sair
        let hasUnsavedChanges = false;
        let preventUnload = false;
        
        // Atualizar estado de alterações não salvas
        function updateUnsavedState(hasChanges) {
            console.log('Updating unsaved state:', hasChanges);
            hasUnsavedChanges = hasChanges;
        }
        
        // Interceptar tentativas de navegação
        function interceptNavigation(e) {
            if (hasUnsavedChanges && !preventUnload) {
                e.preventDefault();
                
                SwitchAlert.confirm(
                    'Sair do site?',
                    'As alterações que você fez talvez não sejam salvas.',
                    function() {
                        // Usuário confirmou - permitir navegação
                        preventUnload = true;
                        // Re-trigger o evento original
                        if (e.type === 'beforeunload') {
                            window.location.reload();
                        } else {
                            window.history.back();
                        }
                    },
                    function() {
                        // Usuário cancelou - não fazer nada
                        preventUnload = false;
                    }
                );
                return false;
            }
        }
        
        // Prevenir saída sem salvar - desabilitado para usar switch alert
        // window.addEventListener('beforeunload', interceptNavigation);
        
        // Keyboard shortcuts for switch alert
        document.addEventListener('keydown', function(e) {
            const overlay = document.getElementById('switch-alert-overlay');
            if (e.key === 'Escape' && overlay.style.display === 'flex') {
                SwitchAlert.hide();
            }
        });
        
        // Click outside to close switch alert
        document.getElementById('switch-alert-overlay').addEventListener('click', function(e) {
            if (e.target === this) {
                SwitchAlert.hide();
            }
        });
        
        // Interceptar cliques em links e navegação
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link) {
                console.log('Link clicked, hasUnsavedChanges:', hasUnsavedChanges, 'isSaving:', isSaving, 'preventUnload:', preventUnload);
                
                // Se está salvando, impedir navegação
                if (isSaving && !preventUnload) {
                    e.preventDefault();
                    SwitchAlert.show('warning', 'Salvamento em andamento', 'Aguarde o documento ser salvo antes de navegar.');
                    return;
                }
                
                if (hasUnsavedChanges && !preventUnload) {
                    // Verificar se não é um link para download ou externo
                    const href = link.getAttribute('href');
                    console.log('Link href:', href);
                    if (href && !href.startsWith('#') && !href.startsWith('javascript:') && !href.includes('download')) {
                        e.preventDefault();
                        
                        SwitchAlert.confirm(
                            'Sair do site?',
                            'As alterações que você fez talvez não sejam salvas.',
                            function() {
                                preventUnload = true;
                                window.location.href = href;
                            }
                        );
                    }
                }
            }
        });
    </script>
</body>
</html>