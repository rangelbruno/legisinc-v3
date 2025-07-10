<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--begin::Head-->

<head>
    <title>Template - Register</title>
    <meta charset="utf-8" />
    
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="Metronic - The World's #1 Selling Bootstrap Admin Template by KeenThemes" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Stylesheets-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet">
    <!--end::Stylesheets-->
    
    <style>
        .api-status-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            padding: 10px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: white;
            text-align: center;
            min-width: 150px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .api-status-indicator.checking {
            background-color: #6c757d;
        }
        
        .api-status-indicator.online {
            background-color: #28a745;
        }
        
        .api-status-indicator.offline {
            background-color: #dc3545;
        }
        
        .api-status-indicator.problems {
            background-color: #ffc107;
            color: #212529;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: currentColor;
        }
        
        .status-dot.pulse {
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .alert-enhanced {
            border-left: 4px solid;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        
        .alert-success-enhanced {
            background-color: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }
        
        .alert-error-enhanced {
            background-color: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }
        
        .alert-warning-enhanced {
            background-color: #fff3cd;
            border-left-color: #ffc107;
            color: #856404;
        }
        
        .form-error {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        
        .form-success {
            border-color: #28a745 !important;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
        }
        
        .field-error {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Toast notifications */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            max-width: 350px;
        }
        
        .toast-message {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            margin-bottom: 10px;
            padding: 15px;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease-out;
        }
        
        .toast-message.success {
            border-left-color: #28a745;
            background-color: #d4edda;
        }
        
        .toast-message.error {
            border-left-color: #dc3545;
            background-color: #f8d7da;
        }
        
        .toast-message.warning {
            border-left-color: #ffc107;
            background-color: #fff3cd;
        }
        
        .toast-message.info {
            border-left-color: #17a2b8;
            background-color: #d1ecf1;
        }
        
        .toast-icon {
            font-size: 20px;
            flex-shrink: 0;
        }
        
        .toast-content {
            flex: 1;
        }
        
        .toast-title {
            font-weight: 600;
            margin-bottom: 2px;
            color: #212529;
        }
        
        .toast-text {
            color: #6c757d;
            font-size: 14px;
        }
        
        .toast-close {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #6c757d;
            padding: 0;
            margin-left: 10px;
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
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    </style>
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" class="auth-bg">
    <!-- API Status Indicator -->
    <div id="apiStatusIndicator" class="api-status-indicator checking" onclick="checkApiStatus()">
        <div class="status-dot pulse"></div>
        <span id="apiStatusText">Verificando API...</span>
    </div>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>
    
    <!-- Toast Container -->
    <div id="toastContainer" class="toast-container"></div>
    
    <!--begin::Theme mode setup on page load-->
    <script>var defaultThemeMode = "light"; var themeMode; if (document.documentElement) { if (document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if (localStorage.getItem("data-bs-theme") !== null) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
    <!--end::Theme mode setup on page load-->
    <!--begin::Main-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Authentication - Sign-up -->
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <!--begin::Body-->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                <!--begin::Form-->
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <!--begin::Wrapper-->
                    <div class="w-lg-500px p-10">
                        <!--begin::Form-->
                        <form class="form w-100" novalidate="novalidate" id="kt_sign_up_form"
                            method="POST" action="{{ route('auth.register') }}">
                            @csrf
                            <!--begin::Heading-->
                            <div class="text-center mb-11">
                                <!--begin::Title-->
                                <h1 class="text-gray-900 fw-bolder mb-3">Criar Conta</h1>
                                <!--end::Title-->
                                <!--begin::Subtitle-->
                                <div class="text-gray-500 fw-semibold fs-6">Registre-se para acessar o sistema</div>
                                <!--end::Subtitle=-->
                            </div>
                            <!--begin::Heading-->
                            
                            <!-- Mensagens de Feedback Melhoradas -->
                            @if (session('success'))
                                <div class="alert-enhanced alert-success-enhanced" id="successAlert">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-check-circle fs-2 me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div>
                                            <strong>Sucesso!</strong><br>
                                            {{ session('success') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            @if (session('error'))
                                <div class="alert-enhanced alert-error-enhanced" id="errorAlert">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-cross-circle fs-2 me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div>
                                            <strong>Erro!</strong><br>
                                            {{ session('error') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            @if ($errors->any())
                                <div class="alert-enhanced alert-error-enhanced" id="validationErrors">
                                    <div class="d-flex align-items-start">
                                        <i class="ki-duotone ki-information fs-2 me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <div>
                                            <strong>Corrija os seguintes erros:</strong>
                                            <ul class="mb-0 mt-2">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Alerta de Status da API -->
                            <div id="apiAlert" class="alert-enhanced alert-warning-enhanced d-none">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-warning fs-2 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div>
                                        <strong>Atenção!</strong><br>
                                        <span id="apiAlertText">API com problemas. Tente novamente em alguns instantes.</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!--begin::Input group=-->
                            <div class="fv-row mb-8">
                                <!--begin::Name-->
                                <input type="text" placeholder="Nome completo" name="name" autocomplete="off"
                                    class="form-control bg-transparent @error('name') form-error @enderror" 
                                    value="{{ old('name') }}" required id="nameInput" />
                                @error('name')
                                    <div class="field-error">{{ $message }}</div>
                                @enderror
                                <!--end::Name-->
                            </div>
                            <!--end::Input group=-->
                            <!--begin::Input group=-->
                            <div class="fv-row mb-8">
                                <!--begin::Email-->
                                <input type="email" placeholder="Email" name="email" autocomplete="off"
                                    class="form-control bg-transparent @error('email') form-error @enderror" 
                                    value="{{ old('email') }}" required id="emailInput" />
                                @error('email')
                                    <div class="field-error">{{ $message }}</div>
                                @enderror
                                <!--end::Email-->
                            </div>
                            <!--end::Input group=-->
                            <!--begin::Input group=-->
                            <div class="fv-row mb-8">
                                <!--begin::Tipo de Usuário-->
                                <select name="tipo_usuario" 
                                    class="form-select form-select-solid @error('tipo_usuario') form-error @enderror" 
                                    data-placeholder="Selecione o tipo de usuário" 
                                    data-allow-clear="false"
                                    required id="tipoUsuarioInput">
                                    <option value="">Selecione o tipo de usuário</option>
                                    <option value="PUBLICO" {{ old('tipo_usuario') == 'PUBLICO' ? 'selected' : '' }}>
                                        👥 Cidadão - Acesso público aos dados
                                    </option>
                                    <option value="CIDADAO_VERIFICADO" {{ old('tipo_usuario') == 'CIDADAO_VERIFICADO' ? 'selected' : '' }}>
                                        ✅ Cidadão Verificado - Acesso ampliado
                                    </option>
                                    <option value="ASSESSOR" {{ old('tipo_usuario') == 'ASSESSOR' ? 'selected' : '' }}>
                                        💼 Assessor - Suporte aos parlamentares
                                    </option>
                                    <option value="LEGISLATIVO" {{ old('tipo_usuario') == 'LEGISLATIVO' ? 'selected' : '' }}>
                                        🏛️ Servidor Legislativo - Funcionário da casa
                                    </option>
                                    <option value="PARLAMENTAR" {{ old('tipo_usuario') == 'PARLAMENTAR' ? 'selected' : '' }}>
                                        👨‍💼 Parlamentar - Representante eleito
                                    </option>
                                    <option value="ADMIN" {{ old('tipo_usuario') == 'ADMIN' ? 'selected' : '' }}>
                                        ⚡ Administrador - Acesso total ao sistema
                                    </option>
                                </select>
                                @error('tipo_usuario')
                                    <div class="field-error">{{ $message }}</div>
                                @enderror
                                <!--end::Tipo de Usuário-->
                            </div>
                            <!--begin::Input group-->
                            <div class="fv-row mb-8" data-kt-password-meter="true">
                                <!--begin::Wrapper-->
                                <div class="mb-1">
                                    <!--begin::Input wrapper-->
                                    <div class="position-relative mb-3">
                                        <input class="form-control bg-transparent @error('password') form-error @enderror" 
                                            type="password" placeholder="Senha" name="password" autocomplete="off" 
                                            required id="passwordInput" />
                                        <span
                                            class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                            data-kt-password-meter-control="visibility">
                                            <i class="ki-duotone ki-eye-slash fs-2"></i>
                                            <i class="ki-duotone ki-eye fs-2 d-none"></i>
                                        </span>
                                    </div>
                                    <!--end::Input wrapper-->
                                    <!--begin::Meter-->
                                    <div class="d-flex align-items-center mb-3"
                                        data-kt-password-meter-control="highlight">
                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                        </div>
                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                        </div>
                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                        </div>
                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                                    </div>
                                    <!--end::Meter-->
                                </div>
                                <!--end::Wrapper-->
                                <!--begin::Hint-->
                                <div class="text-muted">Use pelo menos 8 caracteres com letras, números e símbolos.</div>
                                @error('password')
                                    <div class="field-error">{{ $message }}</div>
                                @enderror
                                <!--end::Hint-->
                            </div>
                            <!--end::Input group=-->
                            <!--end::Input group=-->
                            <div class="fv-row mb-8">
                                <!--begin::Repeat Password-->
                                <input placeholder="Confirmar senha" name="password_confirmation" type="password"
                                    autocomplete="off" class="form-control bg-transparent" required id="confirmPasswordInput" />
                                <div id="passwordMatchError" class="field-error d-none">As senhas não coincidem.</div>
                                <!--end::Repeat Password-->
                            </div>
                            <!--end::Input group=-->

                            <!--begin::Submit button-->
                            <div class="d-grid mb-10">
                                <button type="submit" id="kt_sign_up_submit" class="btn btn-primary">
                                    <!--begin::Indicator label-->
                                    <span class="indicator-label">Criar Conta</span>
                                    <!--end::Indicator label-->
                                    <!--begin::Indicator progress-->
                                    <span class="indicator-progress">Registrando...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    <!--end::Indicator progress-->
                                </button>
                            </div>
                            <!--end::Submit button-->
                            <!--begin::Sign up-->
                            <div class="text-gray-500 text-center fw-semibold fs-6">Já tem uma conta?
                                <a href="{{ route('login') }}"
                                    class="link-primary fw-semibold">Entre aqui</a>
                            </div>
                            <!--end::Sign up-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Form-->
                <!--begin::Footer-->
                <div class="w-lg-500px d-flex flex-stack px-10 mx-auto">
                    <!--begin::Languages-->
                    <div class="me-10">
                        <!--begin::Toggle-->
                        <button class="btn btn-flex btn-link btn-color-gray-700 btn-active-color-primary rotate fs-base"
                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start"
                            data-kt-menu-offset="0px, 0px">
                            <img data-kt-element="current-lang-flag" class="w-20px h-20px rounded me-3"
                                src="{{ asset('assets/media/flags/united-states.svg') }}" alt="" />
                            <span data-kt-element="current-lang-name" class="me-1">English</span>
                            <span class="d-flex flex-center rotate-180">
                                <i class="ki-duotone ki-down fs-5 text-muted m-0"></i>
                            </span>
                        </button>
                        <!--end::Toggle-->
                        <!--begin::Menu-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-4 fs-7"
                            data-kt-menu="true" id="kt_auth_lang_menu">
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link d-flex px-5" data-kt-lang="English">
                                    <span class="symbol symbol-20px me-4">
                                        <img data-kt-element="lang-flag" class="rounded-1"
                                            src="{{ asset('assets/media/flags/united-states.svg') }}" alt="" />
                                    </span>
                                    <span data-kt-element="lang-name">English</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link d-flex px-5" data-kt-lang="Spanish">
                                    <span class="symbol symbol-20px me-4">
                                        <img data-kt-element="lang-flag" class="rounded-1"
                                            src="{{ asset('assets/media/flags/spain.svg') }}" alt="" />
                                    </span>
                                    <span data-kt-element="lang-name">Spanish</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link d-flex px-5" data-kt-lang="German">
                                    <span class="symbol symbol-20px me-4">
                                        <img data-kt-element="lang-flag" class="rounded-1"
                                            src="{{ asset('assets/media/flags/germany.svg') }}" alt="" />
                                    </span>
                                    <span data-kt-element="lang-name">German</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link d-flex px-5" data-kt-lang="Japanese">
                                    <span class="symbol symbol-20px me-4">
                                        <img data-kt-element="lang-flag" class="rounded-1"
                                            src="{{ asset('assets/media/flags/japan.svg') }}" alt="" />
                                    </span>
                                    <span data-kt-element="lang-name">Japanese</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link d-flex px-5" data-kt-lang="French">
                                    <span class="symbol symbol-20px me-4">
                                        <img data-kt-element="lang-flag" class="rounded-1"
                                            src="{{ asset('assets/media/flags/france.svg') }}" alt="" />
                                    </span>
                                    <span data-kt-element="lang-name">French</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                        </div>
                        <!--end::Menu-->
                    </div>
                    <!--end::Languages-->
                    <!--begin::Links-->
                    <div class="d-flex fw-semibold text-primary fs-base gap-5">
                        <a href="pages/team.html" target="_blank">Terms</a>
                        <a href="pages/pricing/column.html" target="_blank">Plans</a>
                        <a href="pages/contact.html" target="_blank">Contact Us</a>
                    </div>
                    <!--end::Links-->
                </div>
                <!--end::Footer-->
            </div>
            <!--end::Body-->
            <!--begin::Aside-->
            <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2"
                style="background-image: url({{ asset('assets/media/misc/auth-bg.png') }})">
                <!--begin::Content-->
                <div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">
                    <!--begin::Logo-->
                    <a href="{{ route('dashboard') }}" class="mb-0 mb-lg-12">
                        <img alt="Logo" src="{{ asset('assets/media/logos/custom-1.png') }}" class="h-60px h-lg-75px" />
                    </a>
                    <!--end::Logo-->
                    <!--begin::Image-->
                    <img class="d-none d-lg-block mx-auto w-275px w-md-50 w-xl-500px mb-10 mb-lg-20"
                        src="{{ asset('assets/media/misc/auth-screens.png') }}" alt="" />
                    <!--end::Image-->
                    <!--begin::Title-->
                    <h1 class="d-none d-lg-block text-white fs-2qx fw-bolder text-center mb-7">Rápido, Eficiente e
                        Produtivo</h1>
                    <!--end::Title-->
                    <!--begin::Text-->
                    <div class="d-none d-lg-block text-white fs-base text-center">Junte-se a nós e
                        <a href="#" class="opacity-75-hover text-warning fw-bold me-1">experimente</a>uma nova forma
                        <br />de gerenciar seus projetos e
                        <a href="#" class="opacity-75-hover text-warning fw-bold me-1">aumentar</a>sua produtividade
                        <br />com nossas ferramentas avançadas.
                    </div>
                    <!--end::Text-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Aside-->
        </div>
        <!--end::Authentication - Sign-up-->
    </div>
    <!--end::Root-->
    <!--end::Main-->
    <!--begin::Javascript-->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    
    <script>
        // Variáveis globais
        let isApiHealthy = null; // null = checking, true = healthy, false = unhealthy
        let formValidated = false;
        
        // Função para limpar todos os toasts
        function clearAllToasts() {
            const container = document.getElementById('toastContainer');
            if (container) {
                container.innerHTML = '';
            }
        }
        
        // Função para criar toasts
        function showToast(message, type = 'info', title = null) {
            const container = document.getElementById('toastContainer');
            if (!container) {
                console.error('Container de toast não encontrado');
                return;
            }
            
            const iconMap = {
                'success': '✅',
                'error': '❌',
                'warning': '⚠️',
                'info': 'ℹ️'
            };
            
            const titleMap = {
                'success': 'Sucesso',
                'error': 'Erro',
                'warning': 'Atenção',
                'info': 'Informação'
            };
            
            const toast = document.createElement('div');
            toast.className = `toast-message ${type}`;
            
            toast.innerHTML = `
                <div class="toast-icon">${iconMap[type] || iconMap['info']}</div>
                <div class="toast-content">
                    <div class="toast-title">${title || titleMap[type]}</div>
                    <div class="toast-text">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
            `;
            
            container.appendChild(toast);
            
            // Auto-remover após 7 segundos (exceto para sucesso que fica mais tempo)
            const autoRemoveTime = type === 'success' ? 10000 : 7000;
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.style.animation = 'slideOut 0.3s ease-out forwards';
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                    }, 300);
                }
            }, autoRemoveTime);
            
            console.log(`Toast ${type}: ${message}`);
        }
        
        // Função para criar notificações de forma robusta (backward compatibility)
        function showNotification(message, type = 'info') {
            // Usar toast como método principal
            showToast(message, type);
            
            // Remover notificações anteriores do sistema antigo
            const existingNotifications = document.querySelectorAll('.dynamic-notification');
            existingNotifications.forEach(n => n.remove());
            
            // Criar nova notificação inline também
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} dynamic-notification mt-3`;
            notification.style.cssText = 'position: relative; z-index: 1050; margin: 10px 0;';
            
            const iconMap = {
                'success': 'check-circle',
                'error': 'cross-circle', 
                'warning': 'warning',
                'info': 'information'
            };
            
            notification.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="ki-duotone ki-${iconMap[type] || 'information'} fs-2 me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div>
                        <strong>${type.charAt(0).toUpperCase() + type.slice(1)}:</strong> ${message}
                    </div>
                </div>
            `;
            
            // Tentar inserir na posição ideal
            let inserted = false;
            
            // Primeiro, tentar inserir no formulário
            const form = document.getElementById('kt_sign_up_form');
            if (form) {
                const heading = form.querySelector('.text-center');
                if (heading && heading.nextElementSibling) {
                    heading.parentNode.insertBefore(notification, heading.nextElementSibling);
                    inserted = true;
                } else {
                    form.insertBefore(notification, form.firstChild);
                    inserted = true;
                }
            }
            
            // Se não conseguir no formulário, tentar no body
            if (!inserted) {
                document.body.appendChild(notification);
            }
            
            // Auto-remover após 7 segundos
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.transition = 'opacity 0.5s';
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 500);
                }
            }, 7000);
            
            console.log(`Notificação ${type}: ${message}`);
        }
        
        // Verificar status da API
        function checkApiStatus() {
            const indicator = document.getElementById('apiStatusIndicator');
            const text = document.getElementById('apiStatusText');
            
            if (!indicator || !text) {
                console.warn('Elementos de status da API não encontrados');
                return;
            }
            
            const dot = indicator.querySelector('.status-dot');
            
            console.log('Verificando status da API...');
            
            // Definir estado de verificação
            indicator.className = 'api-status-indicator checking';
            text.textContent = 'Verificando API...';
            if (dot) {
                dot.classList.add('pulse');
            }
            
            // Primeiro tentar o endpoint do health check
            fetch('/api-test/health')
                .then(response => response.json())
                .then(data => {
                    console.log('Resposta do health check:', data);
                    if (data.success && data.healthy) {
                        console.log('API está funcionando corretamente');
                        indicator.className = 'api-status-indicator online';
                        text.textContent = 'API Online';
                        isApiHealthy = true;
                        hideApiAlert();
                    } else {
                        console.log('API com problemas:', data);
                        indicator.className = 'api-status-indicator problems';
                        text.textContent = 'API com Problemas';
                        isApiHealthy = true; // API responde, mas com problemas - permitir uso
                        showApiAlert('API com problemas. Algumas funcionalidades podem não funcionar corretamente.');
                    }
                })
                .catch(error => {
                    // Se falhar, tentar o endpoint mock diretamente
                    fetch('/api/mock-api/', {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Resposta da API mock:', data);
                            if (data.status === 'ok') {
                                console.log('API mock está funcionando');
                                indicator.className = 'api-status-indicator online';
                                text.textContent = 'API Online';
                                isApiHealthy = true;
                                hideApiAlert();
                            } else {
                                console.log('API mock com problemas:', data);
                                indicator.className = 'api-status-indicator problems';
                                text.textContent = 'API com Problemas';
                                isApiHealthy = false;
                                showApiAlert('API com problemas. Algumas funcionalidades podem não funcionar corretamente.');
                            }
                        })
                        .catch(error => {
                            console.error('API Status Error:', error);
                            indicator.className = 'api-status-indicator offline';
                            text.textContent = 'API Offline';
                            isApiHealthy = false; // Apenas aqui definimos como false (completamente offline)
                            showApiAlert('Não foi possível conectar com a API. Tente novamente mais tarde.');
                        });
                })
                .finally(() => {
                    if (dot) {
                        dot.classList.remove('pulse');
                    }
                });
        }
        
        // Mostrar alerta da API
        function showApiAlert(message, type = 'warning') {
            const alert = document.getElementById('apiAlert');
            const text = document.getElementById('apiAlertText');
            
            if (alert && text) {
                text.textContent = message;
                alert.classList.remove('d-none');
            } else {
                console.warn('Elementos de alerta não encontrados, usando notificação dinâmica');
                // Usar sistema de notificação robusto como fallback
                showNotification(message, type);
            }
        }
        
        // Esconder alerta da API
        function hideApiAlert() {
            const alert = document.getElementById('apiAlert');
            if (alert) {
                alert.classList.add('d-none');
            }
        }
        
        // Validação em tempo real
        function setupRealTimeValidation() {
            const nameInput = document.getElementById('nameInput');
            const emailInput = document.getElementById('emailInput');
            const passwordInput = document.getElementById('passwordInput');
            const confirmPasswordInput = document.getElementById('confirmPasswordInput');
            
            // Verificar se todos os elementos existem
            const tipoUsuarioInput = document.getElementById('tipoUsuarioInput');
            if (!nameInput || !emailInput || !tipoUsuarioInput || !passwordInput || !confirmPasswordInput) {
                console.error('Alguns elementos do formulário não foram encontrados para validação em tempo real');
                return;
            }
            
            // Validar nome
            nameInput.addEventListener('input', function() {
                if (this.value.length >= 2) {
                    this.classList.remove('form-error');
                    this.classList.add('form-success');
                } else {
                    this.classList.remove('form-success');
                    this.classList.add('form-error');
                }
            });
            
            // Validar email
            emailInput.addEventListener('input', function() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailRegex.test(this.value)) {
                    this.classList.remove('form-error');
                    this.classList.add('form-success');
                } else {
                    this.classList.remove('form-success');
                    this.classList.add('form-error');
                }
            });
            
            // Validar tipo de usuário
            tipoUsuarioInput.addEventListener('change', function() {
                if (this.value) {
                    this.classList.remove('form-error');
                    this.classList.add('form-success');
                } else {
                    this.classList.remove('form-success');
                    this.classList.add('form-error');
                }
            });
            
            // Validar senha
            passwordInput.addEventListener('input', function() {
                if (this.value.length >= 8) {
                    this.classList.remove('form-error');
                    this.classList.add('form-success');
                } else {
                    this.classList.remove('form-success');
                    this.classList.add('form-error');
                }
            });
            
            // Validar confirmação de senha
            confirmPasswordInput.addEventListener('input', checkPasswordMatch);
            
            function checkPasswordMatch() {
                const passwordMatchError = document.getElementById('passwordMatchError');
                if (passwordInput.value && confirmPasswordInput.value && passwordMatchError) {
                    if (passwordInput.value === confirmPasswordInput.value) {
                        confirmPasswordInput.classList.remove('form-error');
                        confirmPasswordInput.classList.add('form-success');
                        passwordMatchError.classList.add('d-none');
                    } else {
                        confirmPasswordInput.classList.remove('form-success');
                        confirmPasswordInput.classList.add('form-error');
                        passwordMatchError.classList.remove('d-none');
                    }
                }
            }
        }
        
        // Validar formulário antes do envio
        function validateForm() {
            const nameInput = document.getElementById('nameInput');
            const emailInput = document.getElementById('emailInput');
            const tipoUsuarioInput = document.getElementById('tipoUsuarioInput');
            const passwordInput = document.getElementById('passwordInput');
            const confirmPasswordInput = document.getElementById('confirmPasswordInput');
            
            // Verificar se todos os elementos existem
            if (!nameInput || !emailInput || !tipoUsuarioInput || !passwordInput || !confirmPasswordInput) {
                console.error('Alguns elementos do formulário não foram encontrados');
                showNotification('Erro na validação do formulário. Recarregue a página.', 'error');
                return false;
            }
            
            let isValid = true;
            
            // Validar nome
            if (nameInput.value.length < 2) {
                nameInput.classList.add('form-error');
                isValid = false;
            }
            
            // Validar email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value)) {
                emailInput.classList.add('form-error');
                isValid = false;
            }
            
            // Validar tipo de usuário
            if (!tipoUsuarioInput.value) {
                tipoUsuarioInput.classList.add('form-error');
                isValid = false;
            }
            
            // Validar senha
            if (passwordInput.value.length < 8) {
                passwordInput.classList.add('form-error');
                isValid = false;
            }
            
            // Validar confirmação de senha
            if (passwordInput.value !== confirmPasswordInput.value) {
                confirmPasswordInput.classList.add('form-error');
                const passwordMatchError = document.getElementById('passwordMatchError');
                if (passwordMatchError) {
                    passwordMatchError.classList.remove('d-none');
                }
                isValid = false;
            }
            
            return isValid;
        }
        
        // Configurar envio do formulário
        function setupFormSubmission() {
            const form = document.getElementById('kt_sign_up_form');
            const submitBtn = document.getElementById('kt_sign_up_submit');
            const loadingOverlay = document.getElementById('loadingOverlay');
            
            // Verificar se todos os elementos existem
            if (!form || !submitBtn || !loadingOverlay) {
                console.error('Elementos do formulário não encontrados para configurar envio');
                return;
            }
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validar formulário
                if (!validateForm()) {
                    showApiAlert('Por favor, corrija os campos destacados em vermelho.', 'error');
                    showToast('Verifique os campos obrigatórios e tente novamente.', 'error', 'Dados Inválidos');
                    return;
                }
                
                // Verificar se API está funcionando (permitir se ainda estiver verificando)
                if (isApiHealthy === false) {
                    showApiAlert('A API não está disponível no momento. Tente novamente mais tarde.', 'warning');
                    showToast('Servidor temporariamente indisponível. Tente novamente em alguns minutos.', 'warning', 'Servidor Indisponível');
                    return;
                }
                
                // Se API ainda está sendo verificada, mostrar aviso mas continuar
                if (isApiHealthy === null) {
                    showToast('Enviando dados... (verificação da API em andamento)', 'info', 'Processando');
                }
                
                // Mostrar loading
                submitBtn.setAttribute('data-kt-indicator', 'on');
                loadingOverlay.style.display = 'flex';
                
                // Mostrar toast de processo iniciado
                showToast('Criando sua conta...', 'info', 'Processando');
                
                // Enviar formulário
                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('Resposta do formulário:', response.status, response.statusText);
                    return response.text().then(html => {
                        if (response.ok) {
                            console.log('Formulário enviado com sucesso, recarregando página...');
                            
                                                         // Verificar se há mensagem de sucesso na resposta
                             if (html.includes('Usuário registrado com sucesso')) {
                                 clearAllToasts(); // Limpar toasts de "processando"
                                 showToast('Seus dados foram salvos no sistema! Você pode fazer login agora.', 'success', 'Registro Concluído');
                             }
                            
                            // Aguardar um pouco para mostrar a notificação antes do reload
                            setTimeout(() => {
                                // Recarregar página com resposta
                                document.open();
                                document.write(html);
                                document.close();
                            }, 1500);
                        } else {
                            // Erro HTTP - mostrar resposta da página
                            console.error('Erro HTTP:', response.status);
                            document.open();
                            document.write(html);
                            document.close();
                        }
                    });
                })
                .catch(error => {
                    console.error('Erro no envio do formulário:', error);
                    showToast('Verifique sua conexão com a internet e tente novamente.', 'error', 'Erro de Conexão');
                    showNotification('Erro de conexão. Verifique sua internet e tente novamente.', 'error');
                })
                .finally(() => {
                    // Esconder loading
                    submitBtn.removeAttribute('data-kt-indicator');
                    loadingOverlay.style.display = 'none';
                });
            });
        }
        
        // Inicializar quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            checkApiStatus();
            setupRealTimeValidation();
            setupFormSubmission();
            
            // Verificar API periodicamente
            setInterval(checkApiStatus, 30000); // A cada 30 segundos
            
            // Auto-hide alerts após 5 segundos
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert-enhanced');
                alerts.forEach(alert => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });
    </script>
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>