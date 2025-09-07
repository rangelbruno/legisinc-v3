<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <!--begin::Head-->
    <head>
        <title>LegisInc - Sistema de Tramitação Legislativa</title>
        <meta charset="utf-8" />
        <meta name="description" content="Sistema completo de tramitação legislativa para câmaras municipais, assembleias e órgãos legislativos" />
        <meta name="keywords" content="legisinc, tramitação legislativa, projetos, câmara municipal, assembleia, gestão legislativa" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta property="og:locale" content="pt_BR" />
        <meta property="og:type" content="article" />
        <meta property="og:title" content="LegisInc - Sistema de Tramitação Legislativa" />
        <meta property="og:url" content="{{ url('/') }}" />
        <meta property="og:site_name" content="LegisInc" />
        <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
        <!--begin::Fonts(mandatory for all pages)-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
        <!--end::Fonts-->
        <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
        <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
        <!--end::Global Stylesheets Bundle-->
        <style>
        .auth-aside-bg {
            background-image: url('/assets/media/misc/auth-bg.png');
        }
        </style>
        <script>// Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }</script>
    </head>
    <!--end::Head-->
    <!--begin::Body-->
    <body id="kt_body" class="auth-bg">
        <!--begin::Theme mode setup on page load-->
        <script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
        <!--end::Theme mode setup on page load-->
        <!--begin::Main-->
        <!--begin::Root-->
        <div class="d-flex flex-column flex-root" id="kt_app_root">
            <!--begin::Authentication - Sign-in -->
            <div class="d-flex flex-column flex-lg-row flex-column-fluid">
                <!--begin::Body-->
                <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                    <!--begin::Form-->
                    <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                        <!--begin::Wrapper-->
                        <div class="w-lg-500px p-10">
                            <!--begin::Logo mobile-->
                            <div class="text-center mb-11 d-lg-none">
                                <a href="#" class="mb-7">
                                    <img alt="Logo" src="{{ asset('assets/media/logos/legisinc.png') }}" class="h-40px" />
                                </a>
                            </div>
                            <!--end::Logo mobile-->
                            <!--begin::Form-->
                            <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" method="POST" action="{{ route('login') }}">
                                @csrf
                                <!--begin::Heading-->
                                <div class="text-center mb-11">
                                    <!--begin::Title-->
                                    <h1 class="text-gray-900 fw-bolder mb-3">Acesso ao Sistema</h1>
                                    <!--end::Title-->
                                    <!--begin::Subtitle-->
                                    <div class="text-gray-500 fw-semibold fs-6">Sistema de Tramitação Legislativa</div>
                                    <!--end::Subtitle=-->
                                </div>
                                <!--begin::Heading-->
                                    
                                <x-alerts.flash />
                                    
                                <!--begin::Login options-->
                                <div class="row g-2 mb-9">
                                    <!--begin::Col-->
                                    <div class="col-md-6 mb-2">
                                        <!--begin::Demo access link=-->
                                        <a href="#" class="btn btn-flex btn-outline btn-text-gray-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100" onclick="document.querySelector('input[name=email]').value='bruno@sistema.gov.br'; document.querySelector('input[name=password]').value='123456';">
                                            <i class="ki-duotone ki-user fs-4 me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>Administrador
                                        </a>
                                        <!--end::Demo access link=-->
                                    </div>
                                    <!--end::Col-->
                                    <!--begin::Col-->
                                    <div class="col-md-6 mb-2">
                                        <!--begin::Demo access link=-->
                                        <a href="#" class="btn btn-flex btn-outline btn-text-gray-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100" onclick="document.querySelector('input[name=email]').value='jessica@sistema.gov.br'; document.querySelector('input[name=password]').value='123456';">
                                            <i class="ki-duotone ki-security-user fs-4 me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>Parlamentar
                                        </a>
                                        <!--end::Demo access link=-->
                                    </div>
                                    <!--end::Col-->
                                    <!--begin::Col-->
                                    <div class="col-md-6 mb-2">
                                        <!--begin::Demo access link=-->
                                        <a href="#" class="btn btn-flex btn-outline btn-text-gray-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100" onclick="document.querySelector('input[name=email]').value='joao@sistema.gov.br'; document.querySelector('input[name=password]').value='123456';">
                                            <i class="ki-duotone ki-abstract-39 fs-4 me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>Legislativo
                                        </a>
                                        <!--end::Demo access link=-->
                                    </div>
                                    <!--end::Col-->
                                    <!--begin::Col-->
                                    <div class="col-md-6 mb-2">
                                        <!--begin::Demo access link=-->
                                        <a href="#" class="btn btn-flex btn-outline btn-text-gray-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100" onclick="document.querySelector('input[name=email]').value='roberto@sistema.gov.br'; document.querySelector('input[name=password]').value='123456';">
                                            <i class="ki-duotone ki-document fs-4 me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>Protocolo
                                        </a>
                                        <!--end::Demo access link=-->
                                    </div>
                                    <!--end::Col-->
                                    <!--begin::Col-->
                                    <div class="col-md-6 mb-2">
                                        <!--begin::Demo access link=-->
                                        <a href="#" class="btn btn-flex btn-outline btn-text-gray-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100" onclick="document.querySelector('input[name=email]').value='expediente@sistema.gov.br'; document.querySelector('input[name=password]').value='123456';">
                                            <i class="ki-duotone ki-send fs-4 me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>Expediente
                                        </a>
                                        <!--end::Demo access link=-->
                                    </div>
                                    <!--end::Col-->
                                    <!--begin::Col-->
                                    <div class="col-md-6 mb-2">
                                        <!--begin::Demo access link=-->
                                        <a href="#" class="btn btn-flex btn-outline btn-text-gray-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100" onclick="document.querySelector('input[name=email]').value='juridico@sistema.gov.br'; document.querySelector('input[name=password]').value='123456';">
                                            <i class="ki-duotone ki-courthouse fs-4 me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>Assessor Jurídico
                                        </a>
                                        <!--end::Demo access link=-->
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <!--end::Login options-->
                                <!--begin::Separator-->
                                <div class="separator separator-content my-14">
                                    <span class="w-125px text-gray-500 fw-semibold fs-7">Ou com suas credenciais</span>
                                </div>
                                <!--end::Separator-->
                                <!--begin::Input group=-->
                                <div class="fv-row mb-8">
                                    <!--begin::Email-->
                                    <input type="email" placeholder="E-mail" name="email" autocomplete="email" class="form-control bg-transparent" value="{{ old('email') }}" />
                                    <!--end::Email-->
                                </div>
                                <!--end::Input group=-->
                                <div class="fv-row mb-3">
                                    <!--begin::Password-->
                                    <input type="password" placeholder="Senha" name="password" autocomplete="current-password" class="form-control bg-transparent" />
                                    <!--end::Password-->
                                </div>
                                <!--end::Input group=-->
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                                    <div>
                                        <input type="checkbox" class="form-check-input me-2" id="remember_me" name="remember">
                                        <label class="form-check-label text-gray-600" for="remember_me">
                                            Lembrar-me
                                        </label>
                                    </div>
                                    <!--begin::Link-->
                                    <a href="#" class="link-primary">Esqueceu a senha?</a>
                                    <!--end::Link-->
                                </div>
                                <!--end::Wrapper-->
                                <!--begin::Submit button-->
                                <div class="d-grid mb-10">
                                    <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                                        <!--begin::Indicator label-->
                                        <span class="indicator-label">Entrar no Sistema</span>
                                        <!--end::Indicator label-->
                                        <!--begin::Indicator progress-->
                                        <span class="indicator-progress">Aguarde... 
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        <!--end::Indicator progress-->
                                    </button>
                                </div>
                                <!--end::Submit button-->
                                <!--begin::Sign up-->
                                <div class="text-gray-500 text-center fw-semibold fs-6">Ainda não possui acesso? 
                                <a href="{{ route('progress.index') }}" class="link-primary">Solicitar cadastro</a></div>
                                <!--end::Sign up-->
                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Form-->
                </div>
                <!--end::Body-->
                <!--begin::Aside-->
                <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2 auth-aside-bg">
                    <!--begin::Content-->
                    <div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">
                        <!--begin::Logo-->
                        <a href="#" class="mb-0 mb-lg-12">
                            <img alt="Logo" src="{{ asset('assets/media/logos/legisinc.png') }}" class="h-80px h-lg-120px h-xl-150px" />
                        </a>
                        <!--end::Logo-->
                        <!--begin::Alert-->
                        <div class="mb-10 mb-lg-20">
                            <div class="text-white fs-2qx fw-bolder text-center mb-7 d-none d-lg-block">
                                Sistema de Tramitação Legislativa
                            </div>
                            
                            <div class="alert bg-light-primary d-flex flex-column flex-sm-row p-5 mb-10 shadow-lg rounded-3">
                                <!--begin::Icon-->
                                <i class="ki-duotone ki-notification-bing fs-2hx text-primary me-4 mb-5 mb-sm-0">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <!--end::Icon-->

                                <!--begin::Wrapper-->
                                <div class="d-flex flex-column">
                                    <!--begin::Title-->
                                    <h4 class="fw-semibold text-primary mb-2">Sistema LegisInc - Versão Demo</h4>
                                    <!--end::Title-->

                                    <!--begin::Content-->
                                    <span class="text-gray-700 fs-6">
                                        Plataforma completa para gestão e tramitação legislativa. 
                                        <br class="d-none d-lg-block">
                                        Use as credenciais de teste para explorar todas as funcionalidades do sistema.
                                        <br class="d-none d-lg-block">
                                        <strong>500+ projetos processados | 98% de eficiência | Disponível 24/7</strong>
                                    </span>
                                    <!--end::Content-->
                                </div>
                                <!--end::Wrapper-->
                            </div>
                        </div>
                        <!--end::Alert-->
                        <!--begin::Title-->
                        <h1 class="d-none d-lg-block text-white fs-2qx fw-bolder text-center mb-7">
                            Controle Total do Processo Legislativo
                        </h1>
                        <!--end::Title-->
                        <!--begin::Text-->
                        <div class="d-none d-lg-block text-center">
                            <div class="text-white fs-5 fw-semibold mb-6">
                                O <a href="#" class="opacity-75-hover text-warning fw-bold">LegisInc</a> oferece as ferramentas mais avançadas para gestão legislativa:
                            </div>
                            <div class="row g-4 mb-8">
                                <div class="col-6">
                                    <div class="d-flex align-items-center bg-light bg-opacity-15 rounded-3 p-3">
                                        <i class="ki-duotone ki-arrow-right-square fs-2x text-success me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <span class="text-white fs-6 fw-semibold">Tramitação automatizada de projetos</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center bg-light bg-opacity-15 rounded-3 p-3">
                                        <i class="ki-duotone ki-notification-status fs-2x text-warning me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                        <span class="text-white fs-6 fw-semibold">Controle de prazos e notificações</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center bg-light bg-opacity-15 rounded-3 p-3">
                                        <i class="ki-duotone ki-chart-simple fs-2x text-info me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                        <span class="text-white fs-6 fw-semibold">Relatórios gerenciais em tempo real</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center bg-light bg-opacity-15 rounded-3 p-3">
                                        <i class="ki-duotone ki-arrows-loop fs-2x text-primary me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span class="text-white fs-6 fw-semibold">Integração com sistemas governamentais</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Text-->
                        <!--begin::Documentation Link-->
                        <div class="d-none d-lg-block mt-10 text-center">
                            <a href="{{ route('progress.index') }}" class="btn btn-light-primary btn-lg fw-bold shadow-sm px-8 py-4 rounded-pill">
                                <i class="ki-duotone ki-book-open fs-1 text-primary me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                                <span class="text-primary fs-5">Acessar Documentação</span>
                                <i class="ki-duotone ki-arrow-right fs-2 text-primary ms-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                        </div>
                        <!--end::Documentation Link-->
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Aside-->
            </div>
            <!--end::Authentication - Sign-in -->
        </div>
        <!--end::Root-->
        <!--end::Main-->
        <!--begin::Javascript-->
        <script>var hostUrl = "{{ asset('assets/') }}/";</script>
        <!--begin::Global Javascript Bundle(mandatory for all pages)-->
        <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
        <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
        <!--end::Global Javascript Bundle-->
        <!--begin::Custom Javascript(used for this page only)-->
        <script src="{{ asset('assets/js/custom/authentication/sign-in/general.js') }}"></script>
        <!--end::Custom Javascript-->
        <!--end::Javascript-->
        
        <!--begin::Debug Logger Component-->
        @if(App\Helpers\DebugHelper::isDebugLoggerActive())
        <div id="debug-logger"></div>
        <div id="debug-fallback" class="debug-toggle-fallback" onclick="initializeDebugLogger()" style="display: block;">
            🔧
        </div>
        <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
        @endif
        @include('partials.debug-logger')
        <!--end::Debug Logger Component-->
    </body>
    <!--end::Body-->
</html>