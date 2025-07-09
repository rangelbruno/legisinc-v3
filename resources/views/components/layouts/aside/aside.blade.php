<div id="kt_aside" class="aside py-9" data-kt-drawer="true" data-kt-drawer-name="aside"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start"
    data-kt-drawer-toggle="#kt_aside_toggle">
    <!--begin::Brand-->
    <div class="aside-logo flex-column-auto px-9 mb-9" id="kt_aside_logo">
        <!--begin::Logo-->
        <a href="{{ route('dashboard') }}">
            <img alt="Logo" src="assets/media/logos/demo3.svg" class="h-20px logo theme-light-show" />
            <img alt="Logo" src="assets/media/logos/demo3-dark.svg" class="h-20px logo theme-dark-show" />
        </a>
        <!--end::Logo-->
    </div>
    <!--end::Brand-->
    <!--begin::Aside menu-->
    <div class="aside-menu flex-column-fluid ps-5 pe-3 mb-9" id="kt_aside_menu">
        <!--begin::Aside Menu-->
        <div class="w-100 hover-scroll-overlay-y d-flex pe-3" id="kt_aside_menu_wrapper" data-kt-scroll="true"
            data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer"
            data-kt-scroll-wrappers="#kt_aside, #kt_aside_menu, #kt_aside_menu_wrapper" data-kt-scroll-offset="100">
            <!--begin::Menu-->
            <div class="menu menu-column menu-rounded menu-sub-indention menu-active-bg fw-semibold my-auto"
                id="#kt_aside_menu" data-kt-menu="true">
                
                <!--begin:Menu item - Dashboard-->
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-home fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </div>
                <!--end:Menu item-->
                
                <!--begin:Menu item - Parlamentares-->
                @can('parlamentares.view')
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('parlamentares.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-people fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                        </span>
                        <span class="menu-title">Parlamentares</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('parlamentares.index') ? 'active' : '' }}" href="{{ route('parlamentares.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Lista de Parlamentares</span>
                            </a>
                        </div>
                        @can('parlamentares.create')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('parlamentares.create') ? 'active' : '' }}" href="{{ route('parlamentares.create') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Novo Parlamentar</span>
                            </a>
                        </div>
                        @endcan
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('parlamentares.mesa-diretora') ? 'active' : '' }}" href="{{ route('parlamentares.mesa-diretora') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Mesa Diretora</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endcan
                <!--end:Menu item-->
                
                <!--begin:Menu item - Projetos (futuro)-->
                <div class="menu-item">
                    <a class="menu-link" href="#" onclick="showComingSoon('Projetos')">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-document fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                        <span class="menu-title">Projetos</span>
                        <span class="badge badge-light-warning ms-auto">Em breve</span>
                    </a>
                </div>
                <!--end:Menu item-->
                
                <!--begin:Menu item - Projetos de Lei-->
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('projetos.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-document fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                        <span class="menu-title">Projetos de Lei</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('projetos.index') ? 'active' : '' }}" href="{{ route('projetos.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Todos os Projetos</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('projetos.create') ? 'active' : '' }}" href="{{ route('projetos.create') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Novo Projeto</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('projetos.index', ['status' => 'rascunho']) }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Rascunhos</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('projetos.index', ['status' => 'em_tramitacao']) }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Em Tramitação</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('projetos.index', ['urgentes' => '1']) }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Urgentes</span>
                            </a>
                        </div>
                    </div>
                </div>
                <!--end:Menu item-->
                
                <!--begin:Menu item - Sessões (futuro)-->
                <div class="menu-item">
                    <a class="menu-link" href="#" onclick="showComingSoon('Sessões')">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-calendar fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                        <span class="menu-title">Sessões</span>
                        <span class="badge badge-light-warning ms-auto">Em breve</span>
                    </a>
                </div>
                <!--end:Menu item-->
                
                <!--begin:Menu item - Votações (futuro)-->
                <div class="menu-item">
                    <a class="menu-link" href="#" onclick="showComingSoon('Votações')">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-poll fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </span>
                        <span class="menu-title">Votações</span>
                        <span class="badge badge-light-warning ms-auto">Em breve</span>
                    </a>
                </div>
                <!--end:Menu item-->
                
                <!--begin:Menu item - Comissões-->
                @can('comissoes.view')
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('comissoes.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-category fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </span>
                        <span class="menu-title">Comissões</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('comissoes.index') ? 'active' : '' }}" href="{{ route('comissoes.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Lista de Comissões</span>
                            </a>
                        </div>
                        @can('comissoes.create')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('comissoes.create') ? 'active' : '' }}" href="{{ route('comissoes.create') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Nova Comissão</span>
                            </a>
                        </div>
                        @endcan
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('comissoes.por-tipo', 'permanente') ? 'active' : '' }}" href="{{ route('comissoes.por-tipo', 'permanente') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Permanentes</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('comissoes.por-tipo', 'cpi') ? 'active' : '' }}" href="{{ route('comissoes.por-tipo', 'cpi') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">CPIs</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endcan
                <!--end:Menu item-->
                
                <!--begin:Menu item - Relatórios-->
                @can('sistema.relatorios')
                <div class="menu-item">
                    <a class="menu-link" href="#" onclick="showComingSoon('Relatórios')">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-chart fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                        <span class="menu-title">Relatórios</span>
                        <span class="badge badge-light-warning ms-auto">Em breve</span>
                    </a>
                </div>
                @endcan
                <!--end:Menu item-->
                
                <!--begin:Menu item - Configurações-->
                @can('sistema.configuracoes')
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-setting-2 fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                        <span class="menu-title">Configurações</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        @can('usuarios.view')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('usuarios.index') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Usuários</span>
                            </a>
                        </div>
                        @endcan
                        @can('usuarios.manage_permissions')
                        <div class="menu-item">
                            <a class="menu-link" href="#" onclick="showComingSoon('Permissões')">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Permissões</span>
                            </a>
                        </div>
                        @endcan
                        <div class="menu-item">
                            <a class="menu-link" href="#" onclick="showComingSoon('Sistema')">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Sistema</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endcan
                <!--end:Menu item-->
                
                <!--begin:Menu item - Meu Perfil-->
                @auth
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('user.profile') ? 'active' : '' }}" href="{{ route('user.profile') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-profile-circle fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </span>
                        <span class="menu-title">Meu Perfil</span>
                    </a>
                </div>
                @endauth
                <!--end:Menu item-->
                
            </div>
            <!--end::Menu-->
            
            <!--begin::Coming Soon Script-->
            <script>
            function showComingSoon(feature) {
                Swal.fire({
                    title: feature + ' - Em Desenvolvimento',
                    text: 'Esta funcionalidade está sendo desenvolvida e estará disponível em breve.',
                    icon: 'info',
                    confirmButtonText: 'Ok',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
            }
            </script>
            <!--end::Coming Soon Script-->
        </div>
        <!--end::Aside Menu-->
    </div>
    <!--end::Aside menu-->
    <!--begin::Footer-->
    <x-layouts.aside.footer />
    <!--end::Footer-->
</div>