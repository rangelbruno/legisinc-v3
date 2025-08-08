<div id="kt_aside" class="aside py-9" data-kt-drawer="true" data-kt-drawer-name="aside"
                data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
                data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start"
                data-kt-drawer-toggle="#kt_aside_toggle">
                <!--begin::Brand-->
                <div class="aside-logo flex-column-auto px-9 mb-9" id="kt_aside_logo">
                    <!--begin::Logo-->
                    <a href="{{ route('home') }}">
                        <img alt="Logo" src="{{ asset('assets/media/logos/default.svg') }}" class="h-30px logo theme-light-show" />
                        <img alt="Logo" src="{{ asset('assets/media/logos/default-dark.svg') }}" class="h-30px logo theme-dark-show" />
                    </a>
                    <!--end::Logo-->
                    <!--begin::Aside toggler-->
                    <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle me-n2" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="aside-minimize">
                        <i class="ki-duotone ki-black-left-line fs-1 rotate-180">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <!--end::Aside toggler-->
                </div>
                <!--end::Brand-->
                
                <!--begin::Aside menu-->
                <div class="aside-menu flex-column-fluid ps-5 pe-3 mb-9" id="kt_aside_menu">
                    <!--begin::Aside Menu-->
                    <div class="w-100 hover-scroll-overlay-y d-flex pe-3" id="kt_aside_menu_wrapper"
                        data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}"
                        data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer"
                        data-kt-scroll-wrappers="#kt_aside, #kt_aside_menu, #kt_aside_menu_wrapper"
                        data-kt-scroll-offset="100">
                        <!--begin::Menu-->
                        <div class="menu menu-column menu-rounded menu-sub-indention menu-active-bg fw-semibold my-auto"
                            id="#kt_aside_menu" data-kt-menu="true">
                            
                            {{-- Dashboard --}}
                            @if(\App\Models\ScreenPermission::userCanAccessRoute('dashboard'))
                            <div class="menu-item">
                                <a class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
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
                            @endif

                            {{-- Seção: Sistema Parlamentar --}}
                            @if(\App\Models\ScreenPermission::userCanAccessModule('parlamentares') || \App\Models\ScreenPermission::userCanAccessModule('comissoes') || \App\Models\ScreenPermission::userCanAccessModule('proposicoes'))
                            <div class="menu-item">
                                <div class="menu-content pt-8 pb-2">
                                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">Sistema Parlamentar</span>
                                </div>
                            </div>
                            @endif

                            {{-- Parlamentares --}}
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
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('parlamentares.*') ? 'show' : '' }}">
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('parlamentares.index'))
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('parlamentares.index') ? 'active' : '' }}" href="{{ route('parlamentares.index') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Lista de Parlamentares</span>
                                        </a>
                                    </div>
                                    @endif
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('parlamentares.create'))
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('parlamentares.create') ? 'active' : '' }}" href="{{ route('parlamentares.create') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Novo Parlamentar</span>
                                        </a>
                                    </div>
                                    @endif
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('parlamentares.mesa-diretora'))
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('parlamentares.mesa-diretora') ? 'active' : '' }}" href="{{ route('parlamentares.mesa-diretora') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Mesa Diretora</span>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            {{-- Comissões --}}
                            @if(\App\Models\ScreenPermission::userCanAccessModule('comissoes'))
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('comissoes.*') ? 'here show' : '' }}">
                                <span class="menu-link">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-handshake fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Comissões</span>
                                    <span class="menu-arrow"></span>
                                </span>
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('comissoes.*') ? 'show' : '' }}">
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('comissoes.index'))
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('comissoes.index') ? 'active' : '' }}" href="{{ route('comissoes.index') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Lista de Comissões</span>
                                        </a>
                                    </div>
                                    @endif
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('comissoes.create'))
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('comissoes.create') ? 'active' : '' }}" href="{{ route('comissoes.create') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Nova Comissão</span>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            {{-- Proposições --}}
                            @if(\App\Models\ScreenPermission::userCanAccessModule('proposicoes'))
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('proposicoes.*') ? 'here show' : '' }}">
                                <span class="menu-link">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-file-up fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Proposições</span>
                                    <span class="menu-arrow"></span>
                                </span>
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('proposicoes.*') ? 'show' : '' }}">
                                    
                                    {{-- Menu Parlamentar --}}
                                    @if(auth()->user()->hasRole('PARLAMENTAR') || auth()->user()->isAdmin())
                                        @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.criar'))
                                        <div class="menu-item">
                                            <a class="menu-link {{ request()->routeIs('proposicoes.criar') ? 'active' : '' }}" href="{{ route('proposicoes.criar') }}">
                                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                <span class="menu-title">Criar Proposição</span>
                                            </a>
                                        </div>
                                        @endif
                                        @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.minhas-proposicoes'))
                                        <div class="menu-item">
                                            <a class="menu-link {{ request()->routeIs('proposicoes.minhas-proposicoes') ? 'active' : '' }}" href="{{ route('proposicoes.minhas-proposicoes') }}">
                                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                <span class="menu-title">Minhas Proposições</span>
                                            </a>
                                        </div>
                                        @endif
                                        @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.assinatura'))
                                        <div class="menu-item">
                                            <a class="menu-link {{ request()->routeIs('proposicoes.assinatura') ? 'active' : '' }}" href="{{ route('proposicoes.assinatura') }}">
                                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                <span class="menu-title">Assinatura</span>
                                            </a>
                                        </div>
                                        @endif
                                    @endif

                                    {{-- Menu Legislativo --}}
                                    @if(auth()->user()->hasRole('LEGISLATIVO') || auth()->user()->isAdmin())
                                        <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('proposicoes.legislativo.*') ? 'here show' : '' }}">
                                            <span class="menu-link">
                                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                <span class="menu-title">Legislativo</span>
                                                <span class="menu-arrow"></span>
                                            </span>
                                            <div class="menu-sub menu-sub-accordion {{ request()->routeIs('proposicoes.legislativo.*') ? 'show' : '' }}">
                                                @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.legislativo.index'))
                                                <div class="menu-item">
                                                    <a class="menu-link {{ request()->routeIs('proposicoes.legislativo.index') ? 'active' : '' }}" href="{{ route('proposicoes.legislativo.index') }}">
                                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                        <span class="menu-title">Proposições Recebidas</span>
                                                    </a>
                                                </div>
                                                @endif
                                                @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.relatorio-legislativo'))
                                                <div class="menu-item">
                                                    <a class="menu-link {{ request()->routeIs('proposicoes.relatorio-legislativo') ? 'active' : '' }}" href="{{ route('proposicoes.relatorio-legislativo') }}">
                                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                        <span class="menu-title">Relatório</span>
                                                    </a>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Menu Protocolo --}}
                                    @if(auth()->user()->hasRole('PROTOCOLO') || auth()->user()->isAdmin())
                                        <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('proposicoes.protocolar*') || request()->routeIs('proposicoes.protocolos-hoje') || request()->routeIs('proposicoes.estatisticas-protocolo') ? 'here show' : '' }}">
                                            <span class="menu-link">
                                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                <span class="menu-title">Protocolo</span>
                                                <span class="menu-arrow"></span>
                                            </span>
                                            <div class="menu-sub menu-sub-accordion {{ request()->routeIs('proposicoes.protocolar*') || request()->routeIs('proposicoes.protocolos-hoje') || request()->routeIs('proposicoes.estatisticas-protocolo') ? 'show' : '' }}">
                                                @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.aguardando-protocolo'))
                                                <div class="menu-item">
                                                    <a class="menu-link {{ request()->routeIs('proposicoes.aguardando-protocolo') ? 'active' : '' }}" href="{{ route('proposicoes.aguardando-protocolo') }}">
                                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                        <span class="menu-title">Aguardando Protocolo</span>
                                                    </a>
                                                </div>
                                                @endif
                                                @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.protocolar'))
                                                <div class="menu-item">
                                                    <a class="menu-link {{ request()->routeIs('proposicoes.protocolar') ? 'active' : '' }}" href="{{ route('proposicoes.protocolar') }}">
                                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                        <span class="menu-title">Protocolar</span>
                                                    </a>
                                                </div>
                                                @endif
                                                @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.protocolos-hoje'))
                                                <div class="menu-item">
                                                    <a class="menu-link {{ request()->routeIs('proposicoes.protocolos-hoje') ? 'active' : '' }}" href="{{ route('proposicoes.protocolos-hoje') }}">
                                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                        <span class="menu-title">Protocolos Hoje</span>
                                                    </a>
                                                </div>
                                                @endif
                                                @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.estatisticas-protocolo'))
                                                <div class="menu-item">
                                                    <a class="menu-link {{ request()->routeIs('proposicoes.estatisticas-protocolo') ? 'active' : '' }}" href="{{ route('proposicoes.estatisticas-protocolo') }}">
                                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                        <span class="menu-title">Estatísticas</span>
                                                    </a>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Menu Expediente --}}
                                    @if(auth()->user()->hasRole('EXPEDIENTE') || auth()->user()->isAdmin())
                                        <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('expediente.*') ? 'here show' : '' }}">
                                            <span class="menu-link">
                                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                <span class="menu-title">Expediente</span>
                                                <span class="menu-arrow"></span>
                                            </span>
                                            <div class="menu-sub menu-sub-accordion {{ request()->routeIs('expediente.*') ? 'show' : '' }}">
                                                @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.index'))
                                                <div class="menu-item">
                                                    <a class="menu-link {{ request()->routeIs('expediente.index') ? 'active' : '' }}" href="{{ route('expediente.index') }}">
                                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                        <span class="menu-title">Painel do Expediente</span>
                                                    </a>
                                                </div>
                                                @endif
                                                @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.aguardando-pauta'))
                                                <div class="menu-item">
                                                    <a class="menu-link {{ request()->routeIs('expediente.aguardando-pauta') ? 'active' : '' }}" href="{{ route('expediente.aguardando-pauta') }}">
                                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                        <span class="menu-title">Aguardando Pauta</span>
                                                    </a>
                                                </div>
                                                @endif
                                                @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.relatorio'))
                                                <div class="menu-item">
                                                    <a class="menu-link {{ request()->routeIs('expediente.relatorio') ? 'active' : '' }}" href="{{ route('expediente.relatorio') }}">
                                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                        <span class="menu-title">Relatório</span>
                                                    </a>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Menu Assessoria Jurídica --}}
                                    @if(auth()->user()->hasRole('ASSESSOR_JURIDICO') || auth()->user()->isAdmin())
                                        <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('pareceres.*') ? 'here show' : '' }}">
                                            <span class="menu-link">
                                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                <span class="menu-title">Assessoria Jurídica</span>
                                                <span class="menu-arrow"></span>
                                            </span>
                                            <div class="menu-sub menu-sub-accordion {{ request()->routeIs('pareceres.*') ? 'show' : '' }}">
                                                @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.legislativo.index'))
                                                <div class="menu-item">
                                                    <a class="menu-link {{ request()->routeIs('proposicoes.legislativo.index') ? 'active' : '' }}" href="{{ route('proposicoes.legislativo.index') }}">
                                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                        <span class="menu-title">Proposições para Análise</span>
                                                    </a>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                            @endif

                            {{-- Sessões --}}
                            @if(\App\Models\ScreenPermission::userCanAccessModule('sessoes'))
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('sessoes.*') ? 'here show' : '' }}">
                                <span class="menu-link">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-calendar fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Sessões</span>
                                    <span class="menu-arrow"></span>
                                </span>
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('sessoes.*') ? 'show' : '' }}">
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('sessoes.index'))
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('sessoes.index') ? 'active' : '' }}" href="{{ route('sessoes.index') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Lista de Sessões</span>
                                        </a>
                                    </div>
                                    @endif
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('sessoes.create'))
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('sessoes.create') ? 'active' : '' }}" href="{{ route('sessoes.create') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Nova Sessão</span>
                                        </a>
                                    </div>
                                    @endif
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('sessoes.agenda'))
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('sessoes.agenda') ? 'active' : '' }}" href="{{ route('sessoes.agenda') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Agenda</span>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            {{-- Votações --}}
                            @if(\App\Models\ScreenPermission::userCanAccessModule('votacoes'))
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('votacoes.*') ? 'here show' : '' }}">
                                <span class="menu-link">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-election-2 fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Votações</span>
                                    <span class="menu-arrow"></span>
                                </span>
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('votacoes.*') ? 'show' : '' }}">
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('votacoes.index'))
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('votacoes.index') ? 'active' : '' }}" href="{{ route('votacoes.index') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Lista de Votações</span>
                                        </a>
                                    </div>
                                    @endif
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('votacoes.create'))
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('votacoes.create') ? 'active' : '' }}" href="{{ route('votacoes.create') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Nova Votação</span>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            {{-- Seção: Administração --}}
                            @if(\App\Models\ScreenPermission::userCanAccessModule('usuarios') || \App\Models\ScreenPermission::userCanAccessModule('documentos') || auth()->user()->isAdmin())
                            <div class="menu-item">
                                <div class="menu-content pt-8 pb-2">
                                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">Administração</span>
                                </div>
                            </div>
                            @endif

                            {{-- Usuários --}}
                            @if(\App\Models\ScreenPermission::userCanAccessModule('usuarios'))
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('usuarios.*') ? 'here show' : '' }}">
                                <span class="menu-link">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-user fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Usuários</span>
                                    <span class="menu-arrow"></span>
                                </span>
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('usuarios.*') ? 'show' : '' }}">
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('usuarios.index'))
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('usuarios.index') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Gestão de Usuários</span>
                                        </a>
                                    </div>
                                    @endif
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('usuarios.create'))
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('usuarios.create') ? 'active' : '' }}" href="{{ route('usuarios.create') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Novo Usuário</span>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            {{-- Documentos --}}
                            @if(\App\Models\ScreenPermission::userCanAccessModule('documentos'))
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('documentos.*') ? 'here show' : '' }}">
                                <span class="menu-link">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-document fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Documentos</span>
                                    <span class="menu-arrow"></span>
                                </span>
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('documentos.*') ? 'show' : '' }}">
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('documentos.instancias.index'))
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('documentos.instancias.*') ? 'active' : '' }}" href="{{ route('documentos.instancias.index') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Documentos em Tramitação</span>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            {{-- Configurações --}}
                            @if(auth()->user()->isAdmin())
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('configuracoes.*') || request()->routeIs('admin.*') ? 'here show' : '' }}">
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
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('configuracoes.*') || request()->routeIs('admin.*') ? 'show' : '' }}">
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('admin.screen-permissions.*') ? 'active' : '' }}" href="{{ route('admin.screen-permissions.index') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Permissões</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('admin.tipo-proposicoes.*') ? 'active' : '' }}" href="{{ route('admin.tipo-proposicoes.index') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Tipos de Proposição</span>
                                        </a>
                                    </div>
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('tests.index'))
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('tests.*') ? 'active' : '' }}" href="{{ route('tests.index') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Testes do Sistema</span>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                        </div>
                        <!--end::Menu-->
                    </div>
                    <!--end::Aside Menu-->
                </div>
                <!--end::Aside menu-->
                
                <!--begin::Footer-->
                <div class="aside-footer flex-column-auto px-9" id="kt_aside_footer">
                    <a href="{{ route('auth.logout') }}" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="btn btn-custom btn-primary w-100">
                        <span class="btn-label">Sair do Sistema</span>
                        <i class="ki-duotone ki-entrance-right fs-2 ms-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </a>
                    <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
                <!--end::Footer-->
            </div>