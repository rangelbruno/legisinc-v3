<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--begin::Head-->
<head>
    <title>@yield('title', 'LegisInc - Sistema Administrativo')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistema administrativo para gestão de workflows legislativos">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:type" content="article">
    <meta property="og:title" content="@yield('title', 'LegisInc - Sistema Administrativo')">
    <meta property="og:url" content="{{ config('app.url') }}">
    <meta property="og:site_name" content="LegisInc">
    
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}">
    
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700">
    <!--end::Fonts-->
    
    <!--begin::Vendor Stylesheets-->
    <link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css">
    <!--end::Vendor Stylesheets-->
    
    <!--begin::Global Stylesheets Bundle-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css">
    <!--end::Global Stylesheets Bundle-->
    
    @stack('styles')
</head>
<!--end::Head-->

<!--begin::Body-->
<body id="kt_app_body" 
      data-kt-app-layout="dark-sidebar" 
      data-kt-app-header-fixed="true" 
      data-kt-app-sidebar-enabled="true" 
      data-kt-app-sidebar-fixed="true" 
      data-kt-app-sidebar-hoverable="true" 
      data-kt-app-sidebar-push-header="true" 
      data-kt-app-sidebar-push-toolbar="true" 
      data-kt-app-sidebar-push-footer="true" 
      data-kt-app-toolbar-enabled="true" 
      class="app-default"
      style="--kt-app-sidebar-enabled: 1; --kt-app-sidebar-width: 225px;">
    <!--begin::Theme mode setup-->
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>
    <!--end::Theme mode setup-->

    <!--begin::App-->
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <!--begin::Page-->
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            
            <!--begin::Header-->
            <div id="kt_app_header" class="app-header">
                <!--begin::Header container-->
                <div class="app-container container-fluid d-flex align-items-stretch justify-content-between" id="kt_app_header_container">
                    <!--begin::Sidebar mobile toggle-->
                    <div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Show sidebar menu">
                        <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_sidebar_mobile_toggle">
                            <i class="ki-duotone ki-abstract-14 fs-2 fs-md-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                    </div>
                    <!--end::Sidebar mobile toggle-->

                    <!--begin::Mobile logo-->
                    <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
                        <a href="{{ route('dashboard') }}" class="d-lg-none">
                            <img alt="Logo" src="{{ asset('assets/media/logos/legisinc.png') }}" class="h-30px">
                        </a>
                    </div>
                    <!--end::Mobile logo-->

                    <!--begin::Header wrapper-->
                    <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1" id="kt_app_header_wrapper">
                        <!--begin::Menu-->
                        <div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true" data-kt-swapper-mode="{default: 'append', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
                            <!--begin::Menu wrapper-->
                            <div class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0" id="kt_app_header_menu" data-kt-menu="true">
                                <!--begin::Menu item-->
                                <div class="menu-item me-0 me-lg-2">
                                    <a class="menu-link py-3" href="{{ route('dashboard') }}">
                                        <span class="menu-title">Dashboard</span>
                                    </a>
                                </div>
                                <!--end::Menu item-->

                                <!--begin::Menu item-->
                                <div class="menu-item me-0 me-lg-2">
                                    <a class="menu-link py-3" href="{{ route('admin.workflows.index') }}">
                                        <span class="menu-title">Workflows</span>
                                    </a>
                                </div>
                                <!--end::Menu item-->
                            </div>
                            <!--end::Menu wrapper-->
                        </div>
                        <!--end::Menu-->

                        <!--begin::Navbar-->
                        <div class="app-navbar flex-shrink-0">
                            <!--begin::User menu-->
                            <div class="app-navbar-item ms-1 ms-md-3" id="kt_header_user_menu_toggle">
                                <!--begin::Menu wrapper-->
                                <div class="cursor-pointer symbol symbol-35px symbol-md-40px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                                    <div class="symbol-label fs-3 bg-light-warning text-warning">
                                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                                    </div>
                                </div>
                                <!--begin::User account menu-->
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <div class="menu-content d-flex align-items-center px-3">
                                            <div class="symbol symbol-50px me-5">
                                                <div class="symbol-label fs-3 bg-light-warning text-warning">
                                                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <div class="fw-bold d-flex align-items-center fs-5">
                                                    {{ auth()->user()->name ?? 'Usuário' }}
                                                </div>
                                                <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">
                                                    {{ auth()->user()->email ?? '' }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Menu item-->

                                    <!--begin::Menu separator-->
                                    <div class="separator my-2"></div>
                                    <!--end::Menu separator-->

                                    <!--begin::Menu item-->
                                    <div class="menu-item px-5">
                                        <form method="POST" action="{{ route('auth.logout') }}">
                                            @csrf
                                            <button type="submit" class="menu-link px-5 btn btn-link p-0 w-100 text-start">
                                                Sair do Sistema
                                            </button>
                                        </form>
                                    </div>
                                    <!--end::Menu item-->
                                </div>
                                <!--end::User account menu-->
                                <!--end::Menu wrapper-->
                            </div>
                            <!--end::User menu-->
                        </div>
                        <!--end::Navbar-->
                    </div>
                    <!--end::Header wrapper-->
                </div>
                <!--end::Header container-->
            </div>
            <!--end::Header-->

            <!--begin::Wrapper-->
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                <!--begin::Sidebar-->
                <div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
                    <!--begin::Logo-->
                    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
                        <!--begin::Logo image-->
                        <a href="{{ route('dashboard') }}">
                            <img alt="Logo" src="{{ asset('assets/media/logos/legisinc.png') }}" class="h-25px app-sidebar-logo-default">
                            <img alt="Logo" src="{{ asset('assets/media/logos/legisinc.png') }}" class="h-20px app-sidebar-logo-minimize">
                        </a>
                        <!--end::Logo image-->

                        <!--begin::Sidebar toggle-->
                        <div id="kt_app_sidebar_toggle" class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary body-bg h-30px w-30px position-absolute top-50 start-100 translate-middle rotate" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize">
                            <i class="ki-duotone ki-double-left fs-2 rotate-180">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <!--end::Sidebar toggle-->
                    </div>
                    <!--end::Logo-->

                    <!--begin::sidebar menu-->
                    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
                        <!--begin::Menu wrapper-->
                        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5 my-lg-2" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px">
                            <!--begin::Menu-->
                            <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold" id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">
                                <!--begin::Menu item-->
                                <div class="menu-item">
                                    <a class="menu-link" href="{{ route('dashboard') }}">
                                        <span class="menu-icon">
                                            <i class="ki-duotone ki-element-11 fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                            </i>
                                        </span>
                                        <span class="menu-title">Dashboard</span>
                                    </a>
                                </div>
                                <!--end::Menu item-->

                                <!--begin::Menu item-->
                                <div class="menu-item pt-5">
                                    <div class="menu-content">
                                        <span class="menu-heading fw-bold text-uppercase fs-7">Sistema de Workflows</span>
                                    </div>
                                </div>
                                <!--end::Menu item-->

                                <!--begin::Menu item-->
                                <div class="menu-item">
                                    <a class="menu-link" href="{{ route('admin.workflows.index') }}">
                                        <span class="menu-icon">
                                            <i class="ki-duotone ki-setting-2 fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                        <span class="menu-title">Gerenciar Workflows</span>
                                    </a>
                                </div>
                                <!--end::Menu item-->

                                <!--begin::Menu item-->
                                <div class="menu-item">
                                    <a class="menu-link" href="{{ route('admin.workflows.create') }}">
                                        <span class="menu-icon">
                                            <i class="ki-duotone ki-plus fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                        <span class="menu-title">Criar Workflow</span>
                                    </a>
                                </div>
                                <!--end::Menu item-->

                                <!--begin::Menu item-->
                                <div class="menu-item pt-5">
                                    <div class="menu-content">
                                        <span class="menu-heading fw-bold text-uppercase fs-7">Sistema Parlamentar</span>
                                    </div>
                                </div>
                                <!--end::Menu item-->

                                <!--begin::Menu item-->
                                <div class="menu-item">
                                    <a class="menu-link" href="{{ route('proposicoes.minhas-proposicoes') }}">
                                        <span class="menu-icon">
                                            <i class="ki-duotone ki-document fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                        <span class="menu-title">Proposições</span>
                                    </a>
                                </div>
                                <!--end::Menu item-->
                            </div>
                            <!--end::Menu-->
                        </div>
                        <!--end::Menu wrapper-->
                    </div>
                    <!--end::sidebar menu-->
                </div>
                <!--end::Sidebar-->

                <!--begin::Main-->
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    @yield('content')

                    <!--begin::Footer-->
                    <div id="kt_app_footer" class="app-footer">
                        <!--begin::Footer container-->
                        <div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
                            <!--begin::Copyright-->
                            <div class="text-dark order-2 order-md-1">
                                <span class="text-muted fw-semibold me-1">{{ date('Y') }}&copy;</span>
                                <a href="#" class="text-gray-800 text-hover-primary">LegisInc</a>
                            </div>
                            <!--end::Copyright-->

                            <!--begin::Menu-->
                            <ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
                                <li class="menu-item">
                                    <a href="#" class="menu-link px-2">Sobre</a>
                                </li>
                                <li class="menu-item">
                                    <a href="#" class="menu-link px-2">Suporte</a>
                                </li>
                                <li class="menu-item">
                                    <a href="#" class="menu-link px-2">Documentação</a>
                                </li>
                            </ul>
                            <!--end::Menu-->
                        </div>
                        <!--end::Footer container-->
                    </div>
                    <!--end::Footer-->
                </div>
                <!--end::Main-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::App-->

    <!--begin::Scrolltop-->
    <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
        <i class="ki-duotone ki-arrow-up">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
    </div>
    <!--end::Scrolltop-->

    <!--begin::Javascript-->
    <script>var hostUrl = "{{ asset('assets/') }}";</script>
    
    <!--begin::Global Javascript Bundle-->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <!--end::Global Javascript Bundle-->
    
    <!--begin::Vendors Javascript-->
    <script src="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <!--end::Vendors Javascript-->
    
    <!--begin::Custom Javascript-->
    <script src="{{ asset('assets/js/custom/apps/file-manager/list.js') }}"></script>
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/custom/widgets.js') }}"></script>
    <script src="{{ asset('assets/js/custom/apps/chat/chat.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/upgrade-plan.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/create-app.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/users-search.js') }}"></script>
    <!--end::Custom Javascript-->
    
    <!--begin::Layout Init Script-->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Aplicar estilos corretos do Metronic diretamente
        const sidebar = document.getElementById('kt_app_sidebar');
        const header = document.getElementById('kt_app_header');
        const main = document.getElementById('kt_app_main');
        const body = document.body;
        
        if (sidebar) {
            // Aplicar estilos do sidebar Metronic
            sidebar.style.cssText = `
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 225px !important;
                height: 100vh !important;
                background-color: #1e2129 !important;
                z-index: 1000 !important;
                display: flex !important;
                flex-direction: column !important;
                overflow-y: auto !important;
                border-right: 1px solid #2d3139 !important;
            `;
            
            // Estilos para o logo
            const logo = sidebar.querySelector('.app-sidebar-logo');
            if (logo) {
                logo.style.cssText = `
                    padding: 20px 24px !important;
                    background-color: #1e2129 !important;
                    border-bottom: 1px solid #2d3139 !important;
                `;
            }
            
            // Estilos para o menu
            const menu = sidebar.querySelector('.app-sidebar-menu');
            if (menu) {
                menu.style.cssText = `
                    flex: 1 !important;
                    padding: 10px 0 !important;
                `;
            }
            
            // Estilos para os itens do menu
            const menuItems = sidebar.querySelectorAll('.menu-item .menu-link');
            menuItems.forEach(item => {
                item.style.cssText = `
                    color: #a1a5b7 !important;
                    padding: 10px 24px !important;
                    display: flex !important;
                    align-items: center !important;
                    text-decoration: none !important;
                    border-radius: 6px !important;
                    margin: 2px 12px !important;
                    transition: all 0.15s ease !important;
                `;
                
                // Hover effect
                item.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#2d3139 !important';
                    this.style.color = '#ffffff !important';
                });
                item.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = 'transparent !important';
                    this.style.color = '#a1a5b7 !important';
                });
            });
            
            // Estilos para os headings do menu
            const menuHeadings = sidebar.querySelectorAll('.menu-heading');
            menuHeadings.forEach(heading => {
                heading.style.cssText = `
                    color: #5e6278 !important;
                    font-size: 11px !important;
                    font-weight: 600 !important;
                    text-transform: uppercase !important;
                    letter-spacing: 0.5px !important;
                    padding: 16px 24px 8px !important;
                `;
            });
        }
        
        if (header) {
            // Aplicar estilos do header Metronic
            header.style.cssText = `
                position: fixed !important;
                top: 0 !important;
                left: 225px !important;
                right: 0 !important;
                height: 70px !important;
                background-color: #ffffff !important;
                border-bottom: 1px solid #e4e6ef !important;
                z-index: 999 !important;
                display: flex !important;
                align-items: center !important;
                padding: 0 24px !important;
                box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075) !important;
            `;
        }
        
        if (main) {
            // Ajustar conteúdo principal
            main.style.cssText = `
                margin-left: 225px !important;
                margin-top: 70px !important;
                min-height: calc(100vh - 70px) !important;
                background-color: #f9f9f9 !important;
            `;
        }
        
        // Ajustar body
        body.style.cssText = `
            background-color: #f9f9f9 !important;
            font-family: 'Inter', sans-serif !important;
        `;
        
        // Inicializar componentes KTLayout se estiver disponível
        if (typeof KTLayout !== 'undefined' && KTLayout.init) {
            KTLayout.init();
        }
    });
    </script>
    <!--end::Layout Init Script-->
    
    @stack('scripts')
</body>
<!--end::Body-->
</html>