<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--begin::Head-->

<head>
    <title>LegisInc - Sistema de Tramitação Legislativa</title>
    <meta charset="utf-8" />
    
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no" />
    <meta property="og:locale" content="pt_BR" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="LegisInc - Sistema de Tramitação Legislativa" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!--end::Fonts-->
    <!--begin::Stylesheets-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet">
    <!--end::Stylesheets-->
    <style>
        .auth-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .legislative-pattern {
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0.1) 75%, transparent 75%, transparent);
            background-size: 20px 20px;
        }
        
        .form-control {
            border-radius: 8px;
            border: 2px solid #e1e5e9;
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
            background: transparent;
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .legislative-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
            max-width: 100%;
        }
        
        .logo-container {
            position: relative;
            margin-bottom: 2rem;
        }
        
        .logo-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 1;
        }
        
        .legislative-icon {
            color: #667eea;
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
        
        .feature-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            margin: 2px;
            display: inline-flex;
            align-items: center;
        }
        
        /* Mobile Responsive Styles */
        @media (max-width: 991.98px) {
            .auth-bg {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 1rem;
            }
            
            .legislative-card {
                margin: 0;
                padding: 1.5rem !important;
                border-radius: 12px;
                width: 100%;
                max-width: none;
            }
            
            .logo-container {
                margin-bottom: 1.5rem;
            }
            
            .logo-glow {
                width: 80px;
                height: 80px;
            }
            
            .feature-badge {
                font-size: 10px;
                padding: 3px 6px;
                margin: 1px;
            }
            
            .legislative-icon {
                font-size: 1rem;
            }
            
            .form-control {
                padding: 10px 12px;
                font-size: 16px; /* Prevent zoom on iOS */
            }
            
            .btn-primary {
                padding: 14px 24px;
                font-size: 14px;
            }
            
            .btn-primary:hover {
                transform: none; /* Remove hover effect on mobile */
            }
            
            .btn-outline-primary:hover {
                transform: none; /* Remove hover effect on mobile */
            }
            
            .btn-outline-primary {
                padding: 6px 16px;
                font-size: 13px;
            }
            
            h1 {
                font-size: 1.75rem !important;
            }
            
            .d-flex.flex-column.flex-lg-row-fluid.w-lg-50.w-100 {
                width: 100% !important;
            }
        }
        
        @media (max-width: 576px) {
            .auth-bg {
                padding: 0.5rem;
            }
            
            .legislative-card {
                padding: 1rem !important;
                border-radius: 8px;
            }
            
            .logo-container {
                margin-bottom: 1rem;
            }
            
            .logo-glow {
                width: 60px;
                height: 60px;
            }
            
            .feature-badge {
                font-size: 9px;
                padding: 2px 4px;
            }
            
            .d-flex.justify-content-center.flex-wrap.gap-2 {
                gap: 0.25rem !important;
            }
            
            h1 {
                font-size: 1.5rem !important;
                margin-bottom: 1rem !important;
            }
            
            .text-gray-600 {
                font-size: 0.875rem !important;
            }
        }
        
        /* Landscape orientation adjustments */
        @media (max-width: 991.98px) and (orientation: landscape) {
            .auth-bg {
                padding: 0.5rem;
            }
            
            .legislative-card {
                padding: 1rem !important;
            }
            
            .logo-container {
                margin-bottom: 0.5rem;
            }
            
            .mb-11 {
                margin-bottom: 1.5rem !important;
            }
        }
    </style>
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" class="auth-bg">
    <!--begin::Theme mode setup on page load-->
    <script>var defaultThemeMode = "light"; var themeMode; if (document.documentElement) { if (document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if (localStorage.getItem("data-bs-theme") !== null) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
    <!--end::Theme mode setup on page load-->
    <!--begin::Main-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Authentication - Sign-in -->
        <div class="d-flex flex-column flex-lg-row flex-column-fluid min-vh-100">
            <!--begin::Body-->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 w-100 p-lg-10 p-3 order-2 order-lg-1">
                <!--begin::Form-->
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <!--begin::Wrapper-->
                    <div class="w-lg-500px w-100 p-lg-10 p-4 legislative-card">
                        <!--begin::Logo Mobile-->
                        <div class="text-center mb-6 d-lg-none">
                            <div class="logo-container">
                                <div class="logo-glow"></div>
                                <img alt="LegisInc Logo" src="{{ asset('assets/media/logos/legisinc.png') }}" class="h-60px" style="position: relative; z-index: 2;" />
                            </div>
                        </div>
                        <!--end::Logo Mobile-->
                        
                        <!--begin::Form-->
                        <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form"
                            method="POST" action="{{ route('login') }}">
                            @csrf
                            <!--begin::Heading-->
                            <div class="text-center mb-11">
                                <!--begin::Title-->
                                <h1 class="text-gray-900 fw-bolder mb-3 fs-1">
                                    <i class="fas fa-gavel legislative-icon"></i>
                                    Acesso ao Sistema
                                </h1>
                                <!--end::Title-->
                                <!--begin::Subtitle-->
                                <div class="text-gray-600 fw-semibold fs-6 mb-4">
                                    Sistema de Tramitação Legislativa
                                </div>
                                <div class="d-flex justify-content-center flex-wrap gap-2">
                                    <span class="feature-badge">
                                        <i class="fas fa-file-alt me-1"></i>
                                        Projetos
                                    </span>
                                    <span class="feature-badge">
                                        <i class="fas fa-route me-1"></i>
                                        Tramitação
                                    </span>
                                    <span class="feature-badge">
                                        <i class="fas fa-users me-1"></i>
                                        Parlamentares
                                    </span>
                                    <span class="feature-badge">
                                        <i class="fas fa-gavel me-1"></i>
                                        Comissões
                                    </span>
                                </div>
                                <!--end::Subtitle=-->
                            </div>
                            <!--begin::Heading-->
                            
                            @if (session('success'))
                                <div class="alert alert-success mb-8 rounded-3">
                                    <i class="fas fa-check-circle me-2"></i>
                                    {{ session('success') }}
                                </div>
                            @endif
                            
                            @if ($errors->has('email'))
                                <div class="alert alert-danger mb-8 rounded-3">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    {{ $errors->first('email') }}
                                </div>
                            @endif
                            <!--begin::Input group=-->
                            <div class="fv-row mb-8">
                                <!--begin::Email-->
                                <label class="form-label fs-6 fw-bold text-gray-900 mb-2">
                                    <i class="fas fa-envelope me-2"></i>
                                    E-mail
                                </label>
                                <input type="email" placeholder="Digite seu e-mail" name="email" autocomplete="off"
                                    class="form-control" value="{{ old('email') }}" />
                                <!--end::Email-->
                            </div>
                            <!--end::Input group=-->
                            <div class="fv-row mb-3">
                                <!--begin::Password-->
                                <label class="form-label fs-6 fw-bold text-gray-900 mb-2">
                                    <i class="fas fa-lock me-2"></i>
                                    Senha
                                </label>
                                <input type="password" placeholder="Digite sua senha" name="password" autocomplete="off"
                                    class="form-control" />
                                <!--end::Password-->
                            </div>
                            <!--end::Input group=-->
                            <!--begin::Wrapper-->
                            <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" class="form-check-input me-2" id="remember_me" name="remember">
                                    <label class="form-check-label text-gray-600" for="remember_me">
                                        Lembrar-me
                                    </label>
                                </div>
                                <!--begin::Link-->
                                <a href="#" class="link-primary">
                                    <i class="fas fa-key me-1"></i>
                                    Esqueceu a senha?
                                </a>
                                <!--end::Link-->
                            </div>
                            <!--end::Wrapper-->
                            <!--begin::Submit button-->
                            <div class="d-grid mb-10">
                                <button type="submit" id="kt_sign_in_submit" class="btn btn-primary btn-lg">
                                    <!--begin::Indicator label-->
                                    <span class="indicator-label">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        Entrar no Sistema
                                    </span>
                                    <!--end::Indicator label-->
                                    <!--begin::Indicator progress-->
                                    <span class="indicator-progress">
                                        <i class="fas fa-spinner fa-spin me-2"></i>
                                        Aguarde...
                                    </span>
                                    <!--end::Indicator progress-->
                                </button>
                            </div>
                            <!--end::Submit button-->
                            <!--begin::Sign up-->
                            <div class="text-gray-500 text-center fw-semibold fs-6">
                                Não possui acesso? 
                                <a href="{{ route('auth.register') }}" class="link-primary fw-bold">
                                    <i class="fas fa-user-plus me-1"></i>
                                    Solicitar Cadastro
                                </a>
                            </div>
                            <!--end::Sign up-->
                            
                            <!--begin::Progress Link-->
                            <div class="text-center mt-4">
                                <a href="{{ route('progress.index') }}" class="btn btn-outline-primary btn-sm rounded-pill">
                                    <i class="fas fa-chart-line me-2"></i>
                                    Progresso do Projeto
                                </a>
                            </div>
                            <!--end::Progress Link-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Form-->
                
            </div>
            <!--end::Body-->
            <!--begin::Aside-->
            <div class="d-none d-lg-flex flex-lg-row-fluid w-lg-50 legislative-pattern order-1 order-lg-2 position-relative"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <!--begin::Content-->
                <div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">
                    <!--begin::Logo-->
                    <div class="logo-container mb-lg-12">
                        <div class="logo-glow"></div>
                        <img alt="LegisInc Logo" src="{{ asset('assets/media/logos/legisinc.png') }}" 
                             class="h-120px h-lg-150px" style="position: relative; z-index: 2;" />
                    </div>
                    <!--end::Logo-->
                    <!--begin::Image-->
                    <div class="d-none d-lg-block text-center mb-10 mb-lg-20">
                        <div class="d-flex justify-content-center mb-8">
                            <div class="bg-white bg-opacity-20 rounded-circle p-4 mx-3">
                                <i class="fas fa-file-alt text-white fs-2x"></i>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-circle p-4 mx-3">
                                <i class="fas fa-route text-white fs-2x"></i>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-circle p-4 mx-3">
                                <i class="fas fa-check-circle text-white fs-2x"></i>
                            </div>
                        </div>
                    </div>
                    <!--end::Image-->
                    <!--begin::Title-->
                    <h1 class="d-none d-lg-block text-white fs-2qx fw-bolder text-center mb-7">
                        Rápido, Eficiente e Seguro
                    </h1>
                    <!--end::Title-->
                    <!--begin::Text-->
                    <div class="d-none d-lg-block text-white fs-base text-center mb-8">
                        Sistema completo para <strong>tramitação legislativa</strong> que otimiza o processo 
                        <br />de análise e aprovação de projetos, oferecendo 
                        <br />controle total sobre cada etapa do fluxo parlamentar.
                    </div>
                    <!--end::Text-->
                    
                    <!--begin::Features-->
                    <div class="d-none d-lg-block">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="bg-white bg-opacity-20 rounded-circle p-3 mb-3">
                                        <i class="fas fa-shield-alt text-white fs-3"></i>
                                    </div>
                                    <span class="text-white fw-bold fs-6">Segurança</span>
                                    <span class="text-white-50 fs-7">Dados protegidos</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="bg-white bg-opacity-20 rounded-circle p-3 mb-3">
                                        <i class="fas fa-tachometer-alt text-white fs-3"></i>
                                    </div>
                                    <span class="text-white fw-bold fs-6">Performance</span>
                                    <span class="text-white-50 fs-7">Processo ágil</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="bg-white bg-opacity-20 rounded-circle p-3 mb-3">
                                        <i class="fas fa-users text-white fs-3"></i>
                                    </div>
                                    <span class="text-white fw-bold fs-6">Colaboração</span>
                                    <span class="text-white-50 fs-7">Trabalho em equipe</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Features-->
                </div>
                <!--end::Content-->
                
                <!--begin::API Status Indicator-->
                <div class="position-absolute bottom-0 end-0 me-8 mb-8">
                    <div class="d-flex align-items-center bg-dark bg-opacity-50 rounded-pill px-3 py-2">
                        <div id="api-status-indicator" class="rounded-circle me-2" style="width: 12px; height: 12px; background-color: #6c757d;"></div>
                        <span class="text-white fs-7 fw-semibold" id="api-status-text">Verificando API...</span>
                    </div>
                </div>
                <!--end::API Status Indicator-->
                
               
            </div>
            <!--end::Aside-->
        </div>
        <!--end::Authentication - Sign-in-->
    </div>
    <!--end::Root-->
    <!--end::Main-->
    <!--begin::Javascript-->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    
    <!--begin::API Status Script-->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusIndicator = document.getElementById('api-status-indicator');
        const statusText = document.getElementById('api-status-text');
        
        // Cores para os diferentes status
        const colors = {
            checking: '#6c757d',  // Cinza
            online: '#28a745',    // Verde
            offline: '#dc3545',   // Vermelho
            warning: '#ffc107'    // Amarelo
        };
        
        // Textos para os diferentes status
        const texts = {
            checking: 'Verificando API...',
            online: 'API Online',
            offline: 'API Offline',
            warning: 'API com Problemas'
        };
        
        // Função para atualizar o status visual
        function updateStatus(status) {
            statusIndicator.style.backgroundColor = colors[status];
            statusText.textContent = texts[status];
            
            // Adicionar animação de pulso para status checking
            if (status === 'checking') {
                statusIndicator.style.animation = 'pulse 2s infinite';
            } else {
                statusIndicator.style.animation = 'none';
            }
        }
        
        // Função para verificar o status da API
        async function checkApiStatus() {
            try {
                updateStatus('checking');
                
                // Tentar endpoint de health check da API
                const response = await fetch('/api-test/health', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    timeout: 5000
                });
                
                if (response.ok) {
                    const data = await response.json();
                    
                    if (data.success && data.healthy) {
                        updateStatus('online');
                    } else {
                        updateStatus('warning');
                    }
                } else {
                    updateStatus('offline');
                }
            } catch (error) {
                console.warn('API Status Check Error:', error);
                updateStatus('offline');
            }
        }
        
        // Verificar status imediatamente
        checkApiStatus();
        
        // Verificar status a cada 30 segundos
        setInterval(checkApiStatus, 30000);
        
        // Adicionar tooltip ao indicador
        statusIndicator.parentElement.title = 'Status da API - Clique para verificar novamente';
        
        // Permitir verificação manual clicando no indicador
        statusIndicator.parentElement.addEventListener('click', function() {
            checkApiStatus();
        });
        
        // Adicionar estilos CSS para a animação
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.5; transform: scale(1.1); }
                100% { opacity: 1; transform: scale(1); }
            }
            
            #api-status-indicator {
                transition: all 0.3s ease;
            }
            
            #api-status-indicator:hover {
                transform: scale(1.2);
            }
        `;
        document.head.appendChild(style);
    });
    </script>
    <!--end::API Status Script-->
    <!--end::Javascript-->
</body>
<!--end::Body-->
</html>