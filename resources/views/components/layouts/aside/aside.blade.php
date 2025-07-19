<div id="kt_aside" class="aside py-9" data-kt-drawer="true" data-kt-drawer-name="aside"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start"
    data-kt-drawer-toggle="#kt_aside_toggle">
    <!--begin::Brand-->
    <div class="aside-logo flex-column-auto px-9 mb-9" id="kt_aside_logo">
        <!--begin::Logo-->
        <a href="{{ route('dashboard') }}">
            <img alt="Logo" src="{{ asset('assets/media/logos/legisinc.svg') }}" class="logo theme-light-show" style="height: clamp(60px, 8vw, 100px);" />
            <img alt="Logo" src="{{ asset('assets/media/logos/legisinc-bg.svg') }}" class="logo theme-dark-show" style="height: clamp(60px, 8vw, 100px);" />
        </a>
        <!--end::Logo-->
    </div>
    <!--end::Brand-->
    <!--begin::Aside menu-->
    <div class="aside-menu flex-column-fluid ps-3 ps-lg-5 pe-3 mb-9" id="kt_aside_menu">
        <!--begin::Aside Menu-->
        <div class="w-100 hover-scroll-overlay-y d-flex pe-3" id="kt_aside_menu_wrapper" data-kt-scroll="true"
            data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer"
            data-kt-scroll-wrappers="#kt_aside, #kt_aside_menu, #kt_aside_menu_wrapper" data-kt-scroll-offset="100">
            <!--begin::Menu-->
            <div class="menu menu-column menu-rounded menu-sub-indention menu-active-bg fw-semibold my-auto"
                id="#kt_aside_menu" data-kt-menu="true">
                
                <!--begin:Menu item - Dashboard-->
                @if(\App\Models\ScreenPermission::userCanAccessRoute('dashboard'))
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
                @endif
                <!--end:Menu item-->
                
                <!--begin:Menu item - Parlamentares-->
                @if(\App\Models\ScreenPermission::userCanAccessModule('parlamentares'))
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
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('parlamentares.index'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('parlamentares.index') ? 'active' : '' }}" href="{{ route('parlamentares.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Lista de Parlamentares</span>
                            </a>
                        </div>
                        @endif
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('parlamentares.create'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('parlamentares.create') ? 'active' : '' }}" href="{{ route('parlamentares.create') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Novo Parlamentar</span>
                            </a>
                        </div>
                        @endif
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('parlamentares.mesa-diretora'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('parlamentares.mesa-diretora') ? 'active' : '' }}" href="{{ route('parlamentares.mesa-diretora') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Mesa Diretora</span>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                <!--end:Menu item-->
                
                <!--begin:Menu item - Projetos (futuro)-->
                @if(auth()->check() && auth()->user()->isAdmin())
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('modelos.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-document fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                        <span class="menu-title">Projetos</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link" href="#" onclick="showComingSoon('Configurações Gerais')">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Configurações Gerais</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="#" onclick="showComingSoon('Tipos de Projeto')">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Tipos de Projeto</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="#" onclick="showComingSoon('Workflows')">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Workflows</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                <!--end:Menu item-->
                
                <!--begin:Menu item - Projetos de Lei-->
                @if(\App\Models\ScreenPermission::userCanAccessModule('projetos'))
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
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('projetos.index'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('projetos.index') ? 'active' : '' }}" href="{{ route('projetos.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Todos os Projetos</span>
                            </a>
                        </div>
                        @endif
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('projetos.create'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('projetos.create') ? 'active' : '' }}" href="{{ route('projetos.create') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Novo Projeto</span>
                            </a>
                        </div>
                        @endif
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('projetos.index'))
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
                        @endif
                    </div>
                </div>
                @endif
                <!--end:Menu item-->
                
                <!--begin:Menu item - Sessões-->
                @if(\App\Models\ScreenPermission::userCanAccessModule('sessoes'))
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('admin.sessions.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-calendar-8 fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                                <span class="path6"></span>
                            </i>
                        </span>
                        <span class="menu-title">Sessões</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('admin.sessions.index'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.sessions.index') ? 'active' : '' }}" href="{{ route('admin.sessions.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Todas as Sessões</span>
                            </a>
                        </div>
                        @endif
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('admin.sessions.create'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.sessions.create') ? 'active' : '' }}" href="{{ route('admin.sessions.create') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Nova Sessão</span>
                            </a>
                        </div>
                        @endif
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('admin.sessions.index'))
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('admin.sessions.index', ['status' => 'preparacao']) }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Em Preparação</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('admin.sessions.index', ['status' => 'agendada']) }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Agendadas</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('admin.sessions.index', ['ano' => date('Y')]) }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Sessões {{ date('Y') }}</span>
                            </a>
                        </div>
                        @endif
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('admin.sessions.export'))
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('admin.sessions.index', ['status' => 'exportada']) }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Exportadas</span>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                <!--end:Menu item-->
                
                <!--begin:Menu item - Votações-->
                @if(\App\Models\ScreenPermission::userCanAccessRoute('admin.sessions.index'))
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('admin.sessions.*') && request()->has('votacao') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-poll fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </span>
                        <span class="menu-title">Votações</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('admin.sessions.index', ['com_votacao' => '1']) }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Sessões com Votação</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('admin.sessions.index', ['status' => 'em_andamento', 'com_votacao' => '1']) }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Votações em Andamento</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('admin.sessions.index', ['ano' => date('Y'), 'com_votacao' => '1']) }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Votações {{ date('Y') }}</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('admin.sessions.index', ['status' => 'finalizada', 'com_votacao' => '1']) }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Resultados de Votação</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                <!--end:Menu item-->
                
                <!--begin:Menu item - Comissões-->
                @if(\App\Models\ScreenPermission::userCanAccessModule('comissoes'))
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
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('comissoes.index'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('comissoes.index') ? 'active' : '' }}" href="{{ route('comissoes.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Lista de Comissões</span>
                            </a>
                        </div>
                        @endif
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('comissoes.create'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('comissoes.create') ? 'active' : '' }}" href="{{ route('comissoes.create') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Nova Comissão</span>
                            </a>
                        </div>
                        @endif
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('comissoes.index'))
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
                        @endif
                    </div>
                </div>
                @endif
                <!--end:Menu item-->
                
                <!--begin:Menu item - Relatórios-->
                @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->perfil === 'servidor_legislativo'))
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
                @endif
                <!--end:Menu item-->
                
                <!--begin:Menu item - Administração-->
                @if(\App\Models\ScreenPermission::userCanAccessModule('usuarios') || (auth()->check() && auth()->user()->isAdmin()))
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('admin.*') || request()->routeIs('modelos.*') || request()->routeIs('usuarios.*') || request()->routeIs('parametros.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-shield-tick fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </span>
                        <span class="menu-title">Administração</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion {{ request()->routeIs('admin.*') || request()->routeIs('modelos.*') || request()->routeIs('usuarios.*') || request()->routeIs('parametros.*') ? 'show' : '' }}">
                        @if(auth()->user()->isAdmin())
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Dashboard Admin</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}" href="{{ route('admin.usuarios.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Usuários Admin</span>
                            </a>
                        </div>
                        @endif
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('usuarios.index'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('usuarios.index') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Usuários do Sistema</span>
                            </a>
                        </div>
                        @endif
                        @if(auth()->user()->isAdmin())
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('modelos.*') ? 'active' : '' }}" href="{{ route('modelos.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Modelos de Projeto</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.screen-permissions.*') ? 'active' : '' }}" href="{{ route('admin.screen-permissions.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Permissões</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('parametros.*') ? 'active' : '' }}" href="{{ route('parametros.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Parâmetros</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="#" onclick="showComingSoon('Configurações do Sistema')">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Configurações</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="#" onclick="showComingSoon('Logs e Auditoria')">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Logs</span>
                            </a>
                        </div>
                        @endif
                        @auth
                        <div class="menu-item">
                            <a class="menu-link" href="#" onclick="showComingSoon('Preferências')">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Preferências</span>
                            </a>
                        </div>
                        @endauth
                    </div>
                </div>
                @endif
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