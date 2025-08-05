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
                            
                            <!--begin:Menu item - Dashboard-->
                            @if(\App\Models\ScreenPermission::userCanAccessRoute('dashboard'))
                            <div class="menu-item">
                                <!--begin:Menu link-->
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
                                <!--end:Menu link-->
                            </div>
                            @endif
                            <!--end:Menu item-->

                            <!--begin:Menu separator-->
                            <div class="menu-item">
                                <div class="menu-content pt-8 pb-2">
                                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">Sistema Parlamentar</span>
                                </div>
                            </div>
                            <!--end:Menu separator-->

                            <!--begin:Menu item - Parlamentares-->
                            @if(\App\Models\ScreenPermission::userCanAccessModule('parlamentares'))
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('parlamentares.*') ? 'here show' : '' }}">
                                <!--begin:Menu link-->
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
                                <!--end:Menu link-->
                                <!--begin:Menu sub-->
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('parlamentares.*') ? 'show' : '' }}">
                                    <!--begin:Menu item-->
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('parlamentares.index'))
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('parlamentares.index') ? 'active' : '' }}" href="{{ route('parlamentares.index') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Lista de Parlamentares</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    @endif
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('parlamentares.create'))
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('parlamentares.create') ? 'active' : '' }}" href="{{ route('parlamentares.create') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Novo Parlamentar</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    @endif
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('parlamentares.mesa-diretora'))
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('parlamentares.mesa-diretora') ? 'active' : '' }}" href="{{ route('parlamentares.mesa-diretora') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Mesa Diretora</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    @endif
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('parlamentares.index'))
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link" href="{{ route('parlamentares.por-partido', 'PT') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Por Partido</span>
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

                            <!--begin:Menu item - Comissões-->
                            @if(\App\Models\ScreenPermission::userCanAccessModule('comissoes'))
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('comissoes.*') ? 'here show' : '' }}">
                                <!--begin:Menu link-->
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
                                <!--end:Menu link-->
                                <!--begin:Menu sub-->
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('comissoes.*') ? 'show' : '' }}">
                                    <!--begin:Menu item-->
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('comissoes.index'))
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('comissoes.index') ? 'active' : '' }}" href="{{ route('comissoes.index') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Lista de Comissões</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    @endif
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('comissoes.create'))
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('comissoes.create') ? 'active' : '' }}" href="{{ route('comissoes.create') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Nova Comissão</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    @endif
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('comissoes.index'))
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('comissoes.por-tipo') ? 'active' : '' }}" href="{{ route('comissoes.por-tipo', 'permanente') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Por Tipo</span>
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


                            <!--begin:Menu item - Proposições-->
                            @if(\App\Models\ScreenPermission::userCanAccessModule('proposicoes'))
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('proposicoes.*') ? 'here show' : '' }}">
                                <!--begin:Menu link-->
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
                                <!--end:Menu link-->
                                <!--begin:Menu sub-->
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('proposicoes.*') ? 'show' : '' }}">
                                    <!-- DEBUG: SUBMENU ACTIVE {{ time() }} - USER: {{ Auth::user()->name ?? 'NOT_LOGGED' }} -->
                                    
                                    <!-- TESTE ITEM - FORCE UPDATE -->
                                    <div class="menu-item">
                                        <span class="menu-link" style="color: red; font-weight: bold;">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">🔥 TESTE UPDATE {{ now() }}</span>
                                        </span>
                                    </div>
                                    <!-- END TESTE ITEM -->
                                    <!--begin:Menu item - Criar Proposição-->
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.criar'))
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('proposicoes.criar') ? 'active' : '' }}" href="{{ route('proposicoes.criar') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Criar Proposição</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    @endif
                                    <!--end:Menu item-->
                                    <!--begin:Menu item - Minhas Proposições-->
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.minhas-proposicoes'))
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('proposicoes.minhas-proposicoes') ? 'active' : '' }}" href="{{ route('proposicoes.minhas-proposicoes') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Minhas Proposições</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    @endif
                                    <!--end:Menu item-->
                                    <!--begin:Menu item - Assinatura-->
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.assinatura'))
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('proposicoes.assinatura') ? 'active' : '' }}" href="{{ route('proposicoes.assinatura') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Assinatura</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    @endif
                                    <!--end:Menu item-->
                                    <!--begin:Menu item - Histórico de Assinaturas-->
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.historico-assinaturas'))
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('proposicoes.historico-assinaturas') ? 'active' : '' }}" href="{{ route('proposicoes.historico-assinaturas') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Histórico de Assinaturas</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    @endif
                                    <!--end:Menu item-->
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
                                            <!--begin:Menu item-->
                                            @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.aguardando-protocolo'))
                                            <div class="menu-item">
                                                <!--begin:Menu link-->
                                                <a class="menu-link {{ request()->routeIs('proposicoes.aguardando-protocolo') ? 'active' : '' }}" href="{{ route('proposicoes.aguardando-protocolo') }}">
                                                    <span class="menu-bullet">
                                                        <span class="bullet bullet-dot"></span>
                                                    </span>
                                                    <span class="menu-title">Aguardando Protocolo</span>
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
                                    <!--begin:Menu item - Protocolo (Submenu)-->
                                    @if(auth()->user()->isProtocolo() || auth()->user()->hasRole('PROTOCOLO') || auth()->user()->isAdmin())
                                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('proposicoes.protocolar*') || request()->routeIs('proposicoes.protocolos-hoje') || request()->routeIs('proposicoes.estatisticas-protocolo') || request()->routeIs('proposicoes.aguardando-protocolo') ? 'here show' : '' }}">
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
                                        <div class="menu-sub menu-sub-accordion {{ request()->routeIs('proposicoes.protocolar*') || request()->routeIs('proposicoes.protocolos-hoje') || request()->routeIs('proposicoes.estatisticas-protocolo') || request()->routeIs('proposicoes.aguardando-protocolo') ? 'show' : '' }}">
                                            <!--begin:Menu item-->
                                            @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.aguardando-protocolo'))
                                            <div class="menu-item">
                                                <!--begin:Menu link-->
                                                <a class="menu-link {{ request()->routeIs('proposicoes.aguardando-protocolo') ? 'active' : '' }}" href="{{ route('proposicoes.aguardando-protocolo') }}">
                                                    <span class="menu-bullet">
                                                        <span class="bullet bullet-dot"></span>
                                                    </span>
                                                    <span class="menu-title">Aguardando Protocolo</span>
                                                </a>
                                                <!--end:Menu link-->
                                            </div>
                                            @endif
                                            <!--end:Menu item-->
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
                                    
                                    <!--begin:Menu item - Expediente (Submenu) - CACHE BUSTER {{ time() }}-->
                                    @if(\App\Models\ScreenPermission::userCanAccessModule('expediente') || \App\Models\ScreenPermission::userCanAccessRoute('expediente.index'))
                                    <!-- EXPEDIENTE SUBMENU ACTIVE - Time: {{ now() }} -->
                                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('expediente.*') || request()->routeIs('proposicoes.legislativo.index') || request()->routeIs('pautas.*') ? 'here show' : '' }}">
                                        <!--begin:Menu link-->
                                        <span class="menu-link">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">📋 Expediente</span>
                                            <span class="menu-arrow"></span>
                                        </span>
                                        <!--end:Menu link-->
                                        <!--begin:Menu sub-->
                                        <div class="menu-sub menu-sub-accordion {{ request()->routeIs('expediente.*') || request()->routeIs('proposicoes.legislativo.index') || request()->routeIs('pautas.*') ? 'show' : '' }}">
                                            <!--begin:Menu item-->
                                            @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.index'))
                                            <div class="menu-item">
                                                <!--begin:Menu link-->
                                                <a class="menu-link {{ request()->routeIs('expediente.index') ? 'active' : '' }}" href="{{ route('expediente.index') }}">
                                                    <span class="menu-bullet">
                                                        <span class="bullet bullet-dot"></span>
                                                    </span>
                                                    <span class="menu-title">📋 Painel do Expediente [NOVO]</span>
                                                </a>
                                                <!--end:Menu link-->
                                            </div>
                                            @endif
                                            <!--end:Menu item-->
                                            <!--begin:Menu item-->
                                            @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.legislativo.index'))
                                            <div class="menu-item">
                                                <!--begin:Menu link-->
                                                <a class="menu-link {{ request()->routeIs('proposicoes.legislativo.index') ? 'active' : '' }}" href="{{ route('proposicoes.legislativo.index') }}">
                                                    <span class="menu-bullet">
                                                        <span class="bullet bullet-dot"></span>
                                                    </span>
                                                    <span class="menu-title">Proposições Protocoladas</span>
                                                </a>
                                                <!--end:Menu link-->
                                            </div>
                                            @endif
                                            <!--end:Menu item-->
                                            <!--begin:Menu item-->
                                            @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.aguardando-pauta'))
                                            <div class="menu-item">
                                                <!--begin:Menu link-->
                                                <a class="menu-link {{ request()->routeIs('expediente.aguardando-pauta') ? 'active' : '' }}" href="{{ route('expediente.aguardando-pauta') }}">
                                                    <span class="menu-bullet">
                                                        <span class="bullet bullet-dot"></span>
                                                    </span>
                                                    <span class="menu-title">Aguardando Pauta</span>
                                                </a>
                                                <!--end:Menu link-->
                                            </div>
                                            @endif
                                            <!--end:Menu item-->
                                            <!--begin:Menu item-->
                                            @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.relatorio'))
                                            <div class="menu-item">
                                                <!--begin:Menu link-->
                                                <a class="menu-link {{ request()->routeIs('expediente.relatorio') ? 'active' : '' }}" href="{{ route('expediente.relatorio') }}">
                                                    <span class="menu-bullet">
                                                        <span class="bullet bullet-dot"></span>
                                                    </span>
                                                    <span class="menu-title">Relatório</span>
                                                </a>
                                                <!--end:Menu link-->
                                            </div>
                                            @endif
                                            <!--end:Menu item-->
                                            <!--begin:Menu item-->
                                            @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.aguardando-pauta'))
                                            <div class="menu-item">
                                                <!--begin:Menu link-->
                                                <a class="menu-link {{ request()->routeIs('proposicoes.aguardando-pauta') ? 'active' : '' }}" href="{{ route('proposicoes.aguardando-pauta') }}">
                                                    <span class="menu-bullet">
                                                        <span class="bullet bullet-dot"></span>
                                                    </span>
                                                    <span class="menu-title">Aguardando Pauta (Antigo)</span>
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
                                    
                                    <!--begin:Menu item - Assessor Jurídico (Submenu)-->
                                    @if(auth()->user()->isAssessorJuridico() || auth()->user()->hasRole('ASSESSOR_JURIDICO') || auth()->user()->isAdmin())
                                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('proposicoes.legislativo.index') || request()->routeIs('pareceres.*') ? 'here show' : '' }}">
                                        <!--begin:Menu link-->
                                        <span class="menu-link">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Assessoria Jurídica</span>
                                            <span class="menu-arrow"></span>
                                        </span>
                                        <!--end:Menu link-->
                                        <!--begin:Menu sub-->
                                        <div class="menu-sub menu-sub-accordion {{ request()->routeIs('proposicoes.legislativo.index') || request()->routeIs('pareceres.*') ? 'show' : '' }}">
                                            <!--begin:Menu item-->
                                            @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.legislativo.index'))
                                            <div class="menu-item">
                                                <!--begin:Menu link-->
                                                <a class="menu-link {{ request()->routeIs('proposicoes.legislativo.index') ? 'active' : '' }}" href="{{ route('proposicoes.legislativo.index') }}">
                                                    <span class="menu-bullet">
                                                        <span class="bullet bullet-dot"></span>
                                                    </span>
                                                    <span class="menu-title">Proposições para Análise</span>
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
                                <!--end:Menu sub-->
                            </div>
                            @endif
                            <!--end:Menu item-->

                            <!--begin:Menu item - Sessões-->
                            @if(\App\Models\ScreenPermission::userCanAccessRoute('admin.sessions.index'))
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('sessoes.*') ? 'here show' : '' }}">
                                <!--begin:Menu link-->
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
                                <!--end:Menu link-->
                                <!--begin:Menu sub-->
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('sessoes.*') ? 'show' : '' }}">
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('sessoes.index') ? 'active' : '' }}" href="{{ route('sessoes.index') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Lista de Sessões</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('sessoes.create') ? 'active' : '' }}" href="{{ route('sessoes.create') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Nova Sessão</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('sessoes.agenda') ? 'active' : '' }}" href="{{ route('sessoes.agenda') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Agenda</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('sessoes.atas') ? 'active' : '' }}" href="{{ route('sessoes.atas') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Atas</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('sessoes.por-tipo') ? 'active' : '' }}" href="{{ route('sessoes.por-tipo', 'ordinaria') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Por Tipo</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                </div>
                                <!--end:Menu sub-->
                            </div>
                            @endif
                            <!--end:Menu item-->

                            <!--begin:Menu item - Votações-->
                            @if(\App\Models\ScreenPermission::userCanAccessRoute('admin.sessions.index'))
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('votacoes.*') ? 'here show' : '' }}">
                                <!--begin:Menu link-->
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
                                <!--end:Menu link-->
                                <!--begin:Menu sub-->
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('votacoes.*') ? 'show' : '' }}">
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('votacoes.index') ? 'active' : '' }}" href="{{ route('votacoes.index') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Lista de Votações</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('votacoes.create') ? 'active' : '' }}" href="{{ route('votacoes.create') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Nova Votação</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('votacoes.em-andamento') ? 'active' : '' }}" href="{{ route('votacoes.em-andamento') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Em Andamento</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('votacoes.finalizadas') ? 'active' : '' }}" href="{{ route('votacoes.finalizadas') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Finalizadas</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('votacoes.relatorios') ? 'active' : '' }}" href="{{ route('votacoes.relatorios') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Relatórios</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                </div>
                                <!--end:Menu sub-->
                            </div>
                            @endif
                            <!--end:Menu item-->

                            <!--begin:Menu separator-->
                            <div class="menu-item">
                                <div class="menu-content pt-8 pb-2">
                                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">Administração</span>
                                </div>
                            </div>
                            <!--end:Menu separator-->


                            <!--begin:Menu item - Usuários-->
                            @if(\App\Models\ScreenPermission::userCanAccessModule('usuarios'))
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('usuarios.*') || request()->routeIs('user-api.*') ? 'here show' : '' }}">
                                <!--begin:Menu link-->
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
                                <!--end:Menu link-->
                                <!--begin:Menu sub-->
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('usuarios.*') || request()->routeIs('user-api.*') ? 'show' : '' }}">
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('usuarios.index') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Gestão de Usuários</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('usuarios.create') ? 'active' : '' }}" href="{{ route('usuarios.create') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Novo Usuário</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('user-api.index') ? 'active' : '' }}" href="{{ route('user-api.index') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">API de Usuários</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                </div>
                                <!--end:Menu sub-->
                            </div>
                            @endif
                            <!--end:Menu item-->

                            <!--begin:Menu item - Documentos-->
                            @if(\App\Models\ScreenPermission::userCanAccessModule('documentos'))
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('documentos.*') ? 'here show' : '' }}">
                                <!--begin:Menu link-->
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
                                <!--end:Menu link-->
                                <!--begin:Menu sub-->
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('documentos.*') ? 'show' : '' }}">
                                    <!--begin:Menu item - Instâncias-->
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('documentos.instancias.index'))
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('documentos.instancias.*') ? 'active' : '' }}" href="{{ route('documentos.instancias.index') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Documentos em Tramitação</span>
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

                            <!--begin:Menu separator-->
                            <div class="menu-item">
                                <div class="menu-content pt-8 pb-2">
                                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">Ferramentas</span>
                                </div>
                            </div>
                            <!--end:Menu separator-->

                            <!--begin:Menu item - API Status-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ request()->routeIs('user-api.health') ? 'active' : '' }}" href="{{ route('user-api.health') }}">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-technology-2 fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Status da API</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->

                            <!--begin:Menu item - Testes-->
                            @if(auth()->user()->isAdmin())
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ request()->routeIs('tests.*') ? 'active' : '' }}" href="{{ route('tests.index') }}">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-code fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Testes do Sistema</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            @endif
                            <!--end:Menu item-->

                            <!--begin:Menu item - Configurações-->
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('configuracoes.*') ? 'here show' : '' }}">
                                <!--begin:Menu link-->
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
                                <!--end:Menu link-->
                                <!--begin:Menu sub-->
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('configuracoes.*') ? 'show' : '' }}">
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('usuarios.index') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Usuários</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    @if(auth()->user()->isAdmin())
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('admin.screen-permissions.*') ? 'active' : '' }}" href="{{ route('admin.screen-permissions.index') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Permissões</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    @else
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link" href="#" onclick="showComingSoon('Permissões')">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Permissões</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    @endif
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    @if(auth()->user()->isAdmin())
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('admin.screen-permissions.*') ? 'active' : '' }}" href="{{ route('admin.screen-permissions.index') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Permissões e Roles</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    @else
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link" href="#" onclick="showComingSoon('Permissões e Roles')">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Permissões e Roles</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    @endif
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    @if(auth()->user()->isAdmin() || \App\Models\ScreenPermission::userCanAccessRoute('admin.tipo-proposicoes.index'))
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ request()->routeIs('admin.tipo-proposicoes.*') ? 'active' : '' }}" href="{{ route('admin.tipo-proposicoes.index') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Tipos de Proposição</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    @else
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link" href="#" onclick="showComingSoon('Tipos de Proposição')">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Tipos de Proposição</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    @endif
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link" href="#" onclick="showComingSoon('Sistema')">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Sistema</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                </div>
                                <!--end:Menu sub-->
                            </div>
                            <!--end:Menu item-->

                        </div>
                        <!--end::Menu-->
                    </div>
                    <!--end::Aside Menu-->
                </div>
                <!--end::Aside menu-->
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