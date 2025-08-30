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
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('mesa-diretora.atual'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('mesa-diretora.atual') ? 'active' : '' }}" href="{{ route('mesa-diretora.atual') }}">
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
                
                <!--begin:Menu item - Partidos-->
                @if(\App\Models\ScreenPermission::userCanAccessModule('partidos'))
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('partidos.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-flag fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                        <span class="menu-title">Partidos</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('partidos.index'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('partidos.index') ? 'active' : '' }}" href="{{ route('partidos.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Lista de Partidos</span>
                            </a>
                        </div>
                        @endif
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('partidos.create'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('partidos.create') ? 'active' : '' }}" href="{{ route('partidos.create') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Novo Partido</span>
                            </a>
                        </div>
                        @endif
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('partidos.estatisticas'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('partidos.estatisticas') ? 'active' : '' }}" href="{{ route('partidos.estatisticas') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Estatísticas</span>
                            </a>
                        </div>
                        @endif
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('partidos.brasileiros'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('partidos.brasileiros') ? 'active' : '' }}" href="{{ route('partidos.brasileiros') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Partidos Brasileiros</span>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                <!--end:Menu item-->
                
                <!--begin:Menu item - Mesa Diretora-->
                @if(\App\Models\ScreenPermission::userCanAccessModule('mesa-diretora'))
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('mesa-diretora.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-bank fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                        <span class="menu-title">Mesa Diretora</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('mesa-diretora.atual'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('mesa-diretora.atual') ? 'active' : '' }}" href="{{ route('mesa-diretora.atual') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Composição Atual</span>
                            </a>
                        </div>
                        @endif
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('mesa-diretora.index'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('mesa-diretora.index') ? 'active' : '' }}" href="{{ route('mesa-diretora.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Gerenciar Mesa</span>
                            </a>
                        </div>
                        @endif
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('mesa-diretora.historico'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('mesa-diretora.historico') ? 'active' : '' }}" href="{{ route('mesa-diretora.historico') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Histórico</span>
                            </a>
                        </div>
                        @endif
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('mesa-diretora.create'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('mesa-diretora.create') ? 'active' : '' }}" href="{{ route('mesa-diretora.create') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Novo Membro</span>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                <!--end:Menu item-->
                
                <!--begin:Menu item - Proposições-->
                @if(\App\Models\ScreenPermission::userCanAccessModule('proposicoes'))
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('proposicoes.*') || request()->routeIs('expediente.*') ? 'here show' : '' }}">
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
                    <div class="menu-sub menu-sub-accordion {{ request()->routeIs('proposicoes.*') || request()->routeIs('expediente.*') ? 'show' : '' }}">
                        {{-- EXPEDIENTE SUBMENU - FINAL FIX {{ time() }} --}}
                        @if(\App\Models\ScreenPermission::userCanAccessModule('expediente') || \App\Models\ScreenPermission::userCanAccessRoute('expediente.index'))
                        <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('expediente.*') || request()->routeIs('proposicoes.legislativo.index') ? 'here show' : '' }}">
                            <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">📋 EXPEDIENTE</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion {{ request()->routeIs('expediente.*') || request()->routeIs('proposicoes.legislativo.index') ? 'show' : '' }}">
                                @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.index'))
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('expediente.index') ? 'active' : '' }}" href="{{ route('expediente.index') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Painel do Expediente</span>
                                    </a>
                                </div>
                                @endif
                                @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.legislativo.index'))
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('proposicoes.legislativo.index') ? 'active' : '' }}" href="{{ route('proposicoes.legislativo.index') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Proposições Protocoladas</span>
                                    </a>
                                </div>
                                @endif
                                @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.aguardando-pauta'))
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('expediente.aguardando-pauta') ? 'active' : '' }}" href="{{ route('expediente.aguardando-pauta') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Aguardando Pauta</span>
                                    </a>
                                </div>
                                @endif
                                @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.relatorio'))
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('expediente.relatorio') ? 'active' : '' }}" href="{{ route('expediente.relatorio') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Relatório</span>
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                        {{-- END EXPEDIENTE SUBMENU --}}
                        
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.criar'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('proposicoes.criar') ? 'active' : '' }}" href="{{ route('proposicoes.criar') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Criar Proposição</span>
                            </a>
                        </div>
                        @endif
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.minhas-proposicoes'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('proposicoes.minhas-proposicoes') ? 'active' : '' }}" href="{{ route('proposicoes.minhas-proposicoes') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Minhas Proposições</span>
                            </a>
                        </div>
                        @endif
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.assinatura'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('proposicoes.assinatura') ? 'active' : '' }}" href="{{ route('proposicoes.assinatura') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Assinatura</span>
                            </a>
                        </div>
                        @endif
                        <!--begin:Menu item - Legislativo (Submenu)-->
                        @if(auth()->user()->isLegislativo() || auth()->user()->hasRole('LEGISLATIVO') || auth()->user()->isAdmin())
                        <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('proposicoes.legislativo.*') || request()->routeIs('proposicoes.revisar*') ? 'here show' : '' }}">
                            <!--begin:Menu link-->
                            <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Legislativo</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <!--end:Menu link-->
                            <!--begin:Menu sub-->
                            <div class="menu-sub menu-sub-accordion {{ request()->routeIs('proposicoes.legislativo.*') || request()->routeIs('proposicoes.revisar*') ? 'show' : '' }}">
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{ request()->routeIs('proposicoes.legislativo.index') ? 'active' : '' }}" href="{{ route('proposicoes.legislativo.index') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Proposições Recebidas</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{ request()->routeIs('proposicoes.relatorio-legislativo') ? 'active' : '' }}" href="{{ route('proposicoes.relatorio-legislativo') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Relatório</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                            </div>
                            <!--end:Menu sub-->
                        </div>
                        @endif
                        <!--end:Menu item-->
                        <!--begin:Menu item - Protocolo (Submenu)-->
                        @if(auth()->user()->isProtocolo() || auth()->user()->hasRole('PROTOCOLO') || auth()->user()->isAdmin())
                        <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('proposicoes.protocolar*') || request()->routeIs('proposicoes.protocolos-hoje') || request()->routeIs('proposicoes.estatisticas-protocolo') ? 'here show' : '' }}">
                            <!--begin:Menu link-->
                            <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Protocolo</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <!--end:Menu link-->
                            <!--begin:Menu sub-->
                            <div class="menu-sub menu-sub-accordion {{ request()->routeIs('proposicoes.protocolar*') || request()->routeIs('proposicoes.protocolos-hoje') || request()->routeIs('proposicoes.estatisticas-protocolo') ? 'show' : '' }}">
                                <!--begin:Menu item-->
                                @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.protocolar'))
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{ request()->routeIs('proposicoes.protocolar') ? 'active' : '' }}" href="{{ route('proposicoes.protocolar') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Protocolar</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                @endif
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.protocolos-hoje'))
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{ request()->routeIs('proposicoes.protocolos-hoje') ? 'active' : '' }}" href="{{ route('proposicoes.protocolos-hoje') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Protocolos Hoje</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                @endif
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.estatisticas-protocolo'))
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{ request()->routeIs('proposicoes.estatisticas-protocolo') ? 'active' : '' }}" href="{{ route('proposicoes.estatisticas-protocolo') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Estatísticas</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                @endif
                                <!--end:Menu item-->
                            </div>
                            <!--end:Menu sub-->
                        </div>
                        @endif
                        <!--end:Menu item-->
                    </div>
                </div>
                @endif
                <!--end:Menu item-->

                <!--begin:Menu item - Parecer Jurídico-->
                @if(\App\Models\ScreenPermission::userCanAccessRoute('parecer-juridico.index'))
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('parecer-juridico.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-questionnaire-tablet fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                        <span class="menu-title">Parecer Jurídico</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion {{ request()->routeIs('parecer-juridico.*') ? 'show' : '' }}">
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('parecer-juridico.index'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('parecer-juridico.index') ? 'active' : '' }}" href="{{ route('parecer-juridico.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Proposições Protocoladas</span>
                            </a>
                        </div>
                        @endif
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('parecer-juridico.meus-pareceres'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('parecer-juridico.meus-pareceres') ? 'active' : '' }}" href="{{ route('parecer-juridico.meus-pareceres') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Meus Pareceres</span>
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
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('admin.*') || request()->routeIs('usuarios.*') || request()->routeIs('parametros.*') || request()->routeIs('templates.*') || request()->routeIs('tests.*') ? 'here show' : '' }}">
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
                    <div class="menu-sub menu-sub-accordion {{ request()->routeIs('admin.*') || request()->routeIs('usuarios.*') || request()->routeIs('parametros.*') || request()->routeIs('templates.*') || request()->routeIs('tests.*') ? 'show' : '' }}">
                        @if(auth()->user()->isAdmin())
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Dashboard Admin</span>
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
                            <a class="menu-link {{ request()->routeIs('tests.*') ? 'active' : '' }}" href="{{ route('tests.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Módulo de Testes</span>
                            </a>
                        </div>
                        @endif
                        @if(auth()->user()->isAdmin())
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
                            <a class="menu-link {{ request()->routeIs('admin.tipo-proposicoes.*') ? 'active' : '' }}" href="{{ route('admin.tipo-proposicoes.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Tipos de Proposição</span>
                            </a>
                        </div>
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('templates.index'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('templates.*') ? 'active' : '' }}" href="{{ route('templates.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Templates de Documentos</span>
                                <span class="badge badge-primary ms-auto">Novo</span>
                            </a>
                        </div>
                        @endif
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('admin.docs.fluxo-proposicoes'))
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.docs.*') ? 'active' : '' }}" href="{{ route('admin.docs.fluxo-proposicoes') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Fluxo de Proposições</span>
                                <span class="badge badge-light-success badge-sm ms-auto">NOVO</span>
                            </a>
                        </div>
                        @endif
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.system-diagnostic.*') ? 'active' : '' }}" href="{{ route('admin.system-diagnostic.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Diagnóstico do Sistema</span>
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