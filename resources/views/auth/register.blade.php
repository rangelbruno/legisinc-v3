<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--begin::Head-->

<head>
    <title>LegisInc - Registro no Sistema</title>
    <meta charset="utf-8" />
    
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no" />
    <meta property="og:locale" content="pt_BR" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="LegisInc - Registro no Sistema de Tramitação Legislativa" />
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
            width: 120px;
            height: 120px;
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
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin: 0 4px;
        }
        
        .form-floating {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .form-floating label {
            position: absolute;
            top: 0;
            left: 0;
            color: #667eea;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.5rem 0;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }
        
        .register-steps {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        
        .step {
            display: flex;
            align-items: center;
            margin: 0 1rem;
            color: #667eea;
            font-weight: 600;
        }
        
        .step-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.5rem;
            font-size: 0.875rem;
        }
        
        /* Mobile Responsive Styles */
        @media (max-width: 991.98px) {
            .auth-bg {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 1rem;
                min-height: 100vh;
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
            
            .register-steps {
                display: none !important;
            }
            
            .form-floating {
                margin-bottom: 1rem;
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
            
            h1 {
                font-size: 1.5rem !important;
                margin-bottom: 1rem !important;
            }
            
            .text-gray-600 {
                font-size: 0.875rem !important;
            }
            
            .form-floating {
                margin-bottom: 0.8rem;
            }
            
            .form-floating label {
                font-size: 0.8rem;
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
        <!--begin::Authentication - Sign-up -->
        <div class="d-flex flex-column flex-lg-row flex-column-fluid min-vh-100">
            <!--begin::Body-->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 w-100 p-lg-10 p-3 order-2 order-lg-1">
                <!--begin::Form-->
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <!--begin::Wrapper-->
                    <div class="w-lg-600px w-100 p-lg-10 p-4 legislative-card">
                        <!--begin::Logo Mobile-->
                        <div class="text-center mb-6 d-lg-none">
                            <div class="logo-container">
                                <div class="logo-glow"></div>
                                <img alt="LegisInc Logo" src="{{ asset('assets/media/logos/legisinc.png') }}" class="h-60px" style="position: relative; z-index: 2;" />
                            </div>
                        </div>
                        <!--end::Logo Mobile-->
                        
                        <!--begin::Form-->
                        <form class="form w-100" novalidate="novalidate" id="kt_sign_up_form"
                            method="POST" action="{{ route('register') }}">
                            @csrf
                            <!--begin::Heading-->
                            <div class="text-center mb-11">
                                <!--begin::Title-->
                                <h1 class="text-gray-900 fw-bolder mb-3 fs-1">
                                    <i class="fas fa-user-plus legislative-icon"></i>
                                    Solicitar Acesso
                                </h1>
                                <!--end::Title-->
                                <!--begin::Subtitle-->
                                <div class="text-gray-600 fw-semibold fs-6 mb-4">
                                    Cadastre-se no Sistema de Tramitação Legislativa
                                </div>
                                <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
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
                                <!--end::Subtitle-->
                                
                                <!--begin::Steps-->
                                <div class="register-steps d-none d-lg-flex">
                                    <div class="step">
                                        <div class="step-number">1</div>
                                        <span>Dados Pessoais</span>
                                    </div>
                                    <div class="step">
                                        <div class="step-number">2</div>
                                        <span>Informações Profissionais</span>
                                    </div>
                                    <div class="step">
                                        <div class="step-number">3</div>
                                        <span>Acesso</span>
                                    </div>
                                </div>
                                <!--end::Steps-->
                            </div>
                            <!--begin::Heading-->
                            
                            <x-alerts.flash />
                            
                            <!--begin::Input group=-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <label>
                                            <i class="fas fa-user me-2"></i>
                                            Nome Completo
                                        </label>
                                        <input type="text" name="name" class="form-control" placeholder="Digite seu nome completo" value="{{ old('name') }}" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <label>
                                            <i class="fas fa-envelope me-2"></i>
                                            E-mail
                                        </label>
                                        <input type="email" name="email" class="form-control" placeholder="Digite seu e-mail" value="{{ old('email') }}" required />
                                    </div>
                                </div>
                            </div>
                            <!--end::Input group=-->
                            
                            <!--begin::Input group=-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <label>
                                            <i class="fas fa-id-card me-2"></i>
                                            CPF
                                        </label>
                                        <input type="text" name="documento" class="form-control" placeholder="000.000.000-00" value="{{ old('documento') }}" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <label>
                                            <i class="fas fa-phone me-2"></i>
                                            Telefone
                                        </label>
                                        <input type="text" name="telefone" class="form-control" placeholder="(00) 00000-0000" value="{{ old('telefone') }}" required />
                                    </div>
                                </div>
                            </div>
                            <!--end::Input group=-->
                            
                            <!--begin::Input group=-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <label>
                                            <i class="fas fa-briefcase me-2"></i>
                                            Profissão
                                        </label>
                                        <input type="text" name="profissao" class="form-control" placeholder="Digite sua profissão" value="{{ old('profissao') }}" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <label>
                                            <i class="fas fa-building me-2"></i>
                                            Cargo Atual
                                        </label>
                                        <input type="text" name="cargo_atual" class="form-control" placeholder="Digite seu cargo atual" value="{{ old('cargo_atual') }}" required />
                                    </div>
                                </div>
                            </div>
                            <!--end::Input group=-->
                            
                            <!--begin::Input group=-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <label>
                                            <i class="fas fa-lock me-2"></i>
                                            Senha
                                        </label>
                                        <input type="password" name="password" class="form-control" placeholder="Digite sua senha" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <label>
                                            <i class="fas fa-lock me-2"></i>
                                            Confirmar Senha
                                        </label>
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirme sua senha" required />
                                    </div>
                                </div>
                            </div>
                            <!--end::Input group=-->
                            
                            <!--begin::Accept-->
                            <div class="fv-row mb-8">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="terms" id="kt_check_api_key" required />
                                    <label class="form-check-label text-gray-700" for="kt_check_api_key">
                                        Concordo com os <a href="#" class="link-primary">Termos de Uso</a> e 
                                        <a href="#" class="link-primary">Política de Privacidade</a>
                                    </label>
                                </div>
                            </div>
                            <!--end::Accept-->
                            
                            <!--begin::Submit button-->
                            <div class="d-grid mb-10">
                                <button type="submit" id="kt_sign_up_submit" class="btn btn-primary btn-lg">
                                    <!--begin::Indicator label-->
                                    <span class="indicator-label">
                                        <i class="fas fa-user-plus me-2"></i>
                                        Solicitar Cadastro
                                    </span>
                                    <!--end::Indicator label-->
                                    <!--begin::Indicator progress-->
                                    <span class="indicator-progress">
                                        <i class="fas fa-spinner fa-spin me-2"></i>
                                        Processando...
                                    </span>
                                    <!--end::Indicator progress-->
                                </button>
                            </div>
                            <!--end::Submit button-->
                            
                            <!--begin::Sign in-->
                            <div class="text-gray-500 text-center fw-semibold fs-6">
                                Já possui acesso? 
                                <a href="{{ route('login') }}" class="link-primary fw-bold">
                                    <i class="fas fa-sign-in-alt me-1"></i>
                                    Fazer Login
                                </a>
                            </div>
                            <!--end::Sign in-->
                            
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
                                <i class="fas fa-user-plus text-white fs-2x"></i>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-circle p-4 mx-3">
                                <i class="fas fa-clipboard-check text-white fs-2x"></i>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-circle p-4 mx-3">
                                <i class="fas fa-handshake text-white fs-2x"></i>
                            </div>
                        </div>
                    </div>
                    <!--end::Image-->
                    <!--begin::Title-->
                    <h1 class="d-none d-lg-block text-white fs-2qx fw-bolder text-center mb-7">
                        Junte-se à Nossa Equipe
                    </h1>
                    <!--end::Title-->
                    <!--begin::Text-->
                    <div class="d-none d-lg-block text-white fs-base text-center mb-8">
                        Faça parte da <strong>revolução digital</strong> na tramitação legislativa
                        <br />e contribua para um processo mais transparente e eficiente
                        <br />no desenvolvimento de políticas públicas.
                    </div>
                    <!--end::Text-->
                    
                    <!--begin::Benefits-->
                    <div class="d-none d-lg-block">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="bg-white bg-opacity-20 rounded-circle p-3 mb-3">
                                        <i class="fas fa-graduation-cap text-white fs-3"></i>
                                    </div>
                                    <span class="text-white fw-bold fs-6">Capacitação</span>
                                    <span class="text-white-50 fs-7">Treinamento completo</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="bg-white bg-opacity-20 rounded-circle p-3 mb-3">
                                        <i class="fas fa-hands-helping text-white fs-3"></i>
                                    </div>
                                    <span class="text-white fw-bold fs-6">Suporte</span>
                                    <span class="text-white-50 fs-7">Ajuda especializada</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="bg-white bg-opacity-20 rounded-circle p-3 mb-3">
                                        <i class="fas fa-chart-line text-white fs-3"></i>
                                    </div>
                                    <span class="text-white fw-bold fs-6">Crescimento</span>
                                    <span class="text-white-50 fs-7">Carreira promissora</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Benefits-->
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
    <!--end::Javascript-->
</body>
<!--end::Body-->
</html>