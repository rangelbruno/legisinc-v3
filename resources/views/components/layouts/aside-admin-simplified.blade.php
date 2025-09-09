<div id="kt_aside" class="aside py-9" data-kt-drawer="true" data-kt-drawer-name="aside"
                data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
                data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start"
                data-kt-drawer-toggle="#kt_aside_toggle">
                <!--begin::Brand-->
                <div class="aside-logo flex-column-auto px-9 mb-9" id="kt_aside_logo">
                    <!--begin::Logo-->
                    <a href="{{ route('home') }}">
                        <img alt="Logo" src="{{ asset('assets/media/logos/legisinc.svg') }}" class="logo theme-light-show" style="height: clamp(60px, 8vw, 100px);">
                        <img alt="Logo" src="{{ asset('assets/media/logos/legisinc-bg.svg') }}" class="logo theme-dark-show" style="height: clamp(60px, 8vw, 100px);">
                    </a>
                    <!--end::Logo-->
                </div>
                <!--end::Brand-->
                
                <!--begin::Aside menu-->
                <div class="aside-menu flex-column-fluid ps-3 ps-lg-5 pe-3 mb-9" id="kt_aside_menu">
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
                                        <i class="ki-duotone ki-home fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Dashboard</span>
                                </a>
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

                            {{-- Partidos --}}
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
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('partidos.*') ? 'show' : '' }}">
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('partidos.index'))
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('partidos.index') ? 'active' : '' }}" href="{{ route('partidos.index') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Lista de Partidos</span>
                                        </a>
                                    </div>
                                    @endif
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('partidos.create'))
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('partidos.create') ? 'active' : '' }}" href="{{ route('partidos.create') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Novo Partido</span>
                                        </a>
                                    </div>
                                    @endif
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('partidos.estatisticas'))
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('partidos.estatisticas') ? 'active' : '' }}" href="{{ route('partidos.estatisticas') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Estatísticas</span>
                                        </a>
                                    </div>
                                    @endif
                                    @if(\App\Models\ScreenPermission::userCanAccessRoute('partidos.brasileiros'))
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('partidos.brasileiros') ? 'active' : '' }}" href="{{ route('partidos.brasileiros') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Partidos Brasileiros</span>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            {{-- Mesa Diretora --}}
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
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('mesa-diretora.*') ? 'show' : '' }}">
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('mesa-diretora.atual') ? 'active' : '' }}" href="{{ route('mesa-diretora.atual') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Composição Atual</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('mesa-diretora.index') ? 'active' : '' }}" href="{{ route('mesa-diretora.index') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Gerenciar Mesa</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('mesa-diretora.historico') ? 'active' : '' }}" href="{{ route('mesa-diretora.historico') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Histórico</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('mesa-diretora.create') ? 'active' : '' }}" href="{{ route('mesa-diretora.create') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Novo Membro</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Proposições --}}
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
                                    
                                    {{-- Expediente - Seção destacada --}}
                                    @if(auth()->user()->hasRole('EXPEDIENTE') || auth()->user()->isAdmin())
                                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('expediente.*') ? 'here show' : '' }}">
                                        <span class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">📋 EXPEDIENTE</span>
                                            <span class="menu-arrow"></span>
                                        </span>
                                        <div class="menu-sub menu-sub-accordion {{ request()->routeIs('expediente.*') ? 'show' : '' }}">
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('expediente.index') ? 'active' : '' }}" href="{{ route('expediente.index') }}">
                                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                    <span class="menu-title">Painel do Expediente</span>
                                                </a>
                                            </div>
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('proposicoes.legislativo') ? 'active' : '' }}" href="{{ route('proposicoes.legislativo') }}">
                                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                    <span class="menu-title">Proposições Protocoladas</span>
                                                </a>
                                            </div>
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('expediente.aguardando-pauta') ? 'active' : '' }}" href="{{ route('expediente.aguardando-pauta') }}">
                                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                    <span class="menu-title">Aguardando Pauta</span>
                                                </a>
                                            </div>
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('expediente.relatorio') ? 'active' : '' }}" href="{{ route('expediente.relatorio') }}">
                                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                    <span class="menu-title">Relatório</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Ações básicas --}}
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

                                    {{-- Legislativo --}}
                                    @if(auth()->user()->hasRole('LEGISLATIVO') || auth()->user()->isAdmin())
                                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('proposicoes.legislativo.*') ? 'here show' : '' }}">
                                        <span class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Legislativo</span>
                                            <span class="menu-arrow"></span>
                                        </span>
                                        <div class="menu-sub menu-sub-accordion {{ request()->routeIs('proposicoes.legislativo.*') ? 'show' : '' }}">
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('proposicoes.legislativo.index') ? 'active' : '' }}" href="{{ route('proposicoes.legislativo.index') }}">
                                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                    <span class="menu-title">Proposições Recebidas</span>
                                                </a>
                                            </div>
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('proposicoes.relatorio-legislativo') ? 'active' : '' }}" href="{{ route('proposicoes.relatorio-legislativo') }}">
                                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                                    <span class="menu-title">Relatório</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Protocolo --}}
                                    @if(auth()->user()->hasRole('PROTOCOLO') || auth()->user()->isAdmin())
                                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('proposicoes.protocolar*') || request()->routeIs('proposicoes.protocolos-hoje') || request()->routeIs('proposicoes.estatisticas-protocolo') ? 'here show' : '' }}">
                                        <span class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Protocolo</span>
                                            <span class="menu-arrow"></span>
                                        </span>
                                        <div class="menu-sub menu-sub-accordion {{ request()->routeIs('proposicoes.protocolar*') || request()->routeIs('proposicoes.protocolos-hoje') || request()->routeIs('proposicoes.estatisticas-protocolo') ? 'show' : '' }}">
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

                                </div>
                            </div>
                            @endif

                            {{-- Parecer Jurídico --}}
                            @if(auth()->user()->hasRole('ASSESSOR_JURIDICO') || auth()->user()->isAdmin())
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
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('parecer-juridico.index') ? 'active' : '' }}" href="{{ route('parecer-juridico.index') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Proposições Protocoladas</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('parecer-juridico.meus-pareceres') ? 'active' : '' }}" href="{{ route('parecer-juridico.meus-pareceres') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Meus Pareceres</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Sessões --}}
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
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('admin.sessions.*') ? 'show' : '' }}">
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('admin.sessions.index') ? 'active' : '' }}" href="{{ route('admin.sessions.index') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Todas as Sessões</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('admin.sessions.create') ? 'active' : '' }}" href="{{ route('admin.sessions.create') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Nova Sessão</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link" href="{{ route('admin.sessions.index', ['status' => 'preparacao']) }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Em Preparação</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link" href="{{ route('admin.sessions.index', ['status' => 'agendada']) }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Agendadas</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link" href="{{ route('admin.sessions.index', ['ano' => '2025']) }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Sessões 2025</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link" href="{{ route('admin.sessions.index', ['status' => 'exportada']) }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Exportadas</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Votações --}}
                            @if(\App\Models\ScreenPermission::userCanAccessModule('votacoes'))
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('votacoes.*') ? 'here show' : '' }}">
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
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('votacoes.*') ? 'show' : '' }}">
                                    <div class="menu-item">
                                        <a class="menu-link" href="{{ route('admin.sessions.index', ['com_votacao' => '1']) }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Sessões com Votação</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link" href="{{ route('admin.sessions.index', ['status' => 'em_andamento', 'com_votacao' => '1']) }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Votações em Andamento</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link" href="{{ route('admin.sessions.index', ['ano' => '2025', 'com_votacao' => '1']) }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Votações 2025</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link" href="{{ route('admin.sessions.index', ['status' => 'finalizada', 'com_votacao' => '1']) }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Resultados de Votação</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Comissões --}}
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
                                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('comissoes.*') ? 'show' : '' }}">
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('comissoes.index') ? 'active' : '' }}" href="{{ route('comissoes.index') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Lista de Comissões</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('comissoes.create') ? 'active' : '' }}" href="{{ route('comissoes.create') }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Nova Comissão</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link" href="{{ route('comissoes.index', ['tipo' => 'permanente']) }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">Permanentes</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link" href="{{ route('comissoes.index', ['tipo' => 'cpi']) }}">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title">CPIs</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Relatórios --}}
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

                            {{-- Administração (apenas para ADMIN) - LINK ÚNICO --}}
                            @if(auth()->user()->isAdmin())
                            <div class="menu-item">
                                <a class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-shield-tick fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Administração</span>
                                </a>
                            </div>
                            @endif

                            {{-- Meu Perfil --}}
                            <div class="menu-item">
                                <a class="menu-link {{ request()->routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}">
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
                <div class="aside-footer flex-column-auto px-9" id="kt_aside_footer">
                    <!--begin::User panel-->
                    <div class="d-flex flex-stack">
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center">
                            <!--begin::Avatar-->
                            <div class="symbol symbol-circle symbol-40px">
                                @if(auth()->user()->avatar)
                                    <div class="symbol-label fs-3 fw-bold text-white" style="background-image: url('{{ Storage::url(auth()->user()->avatar) }}'); background-size: cover; background-position: center;"></div>
                                @else
                                    <div class="symbol-label fs-3 fw-bold text-white bg-danger">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                    </div>
                                @endif
                            </div>
                            <!--end::Avatar-->
                            <!--begin::User info-->
                            <div class="ms-2">
                                <!--begin::Name-->
                                @if(auth()->user())
                                    <a href="{{ route('profile') }}" class="text-gray-800 text-hover-primary fs-6 fw-bold lh-1">{{ auth()->user()->name }}</a>
                                @endif
                                <!--end::Name-->
                                <!--begin::Major-->
                                @if(auth()->user())
                                    <span class="text-muted fw-semibold d-block fs-7 lh-1">{{ auth()->user()->role }}</span>
                                @endif
                                <!--end::Major-->
                            </div>
                            <!--end::User info-->
                        </div>
                        <!--end::Wrapper-->
                        <!--begin::User menu-->
                        <div class="ms-1">
                            <div class="btn btn-sm btn-icon btn-active-color-primary position-relative me-n2" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-overflow="true" data-kt-menu-placement="top-end">
                                <i class="ki-duotone ki-setting-2 fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <!--begin::User account menu-->
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <div class="menu-content d-flex align-items-center px-3">
                                        <!--begin::Avatar-->
                                        <div class="symbol symbol-40px symbol-md-50px me-3 me-md-5">
                                            @if(auth()->user()->avatar)
                                                <div class="symbol-label fs-4 fs-md-3 fw-bold text-white" style="background-image: url('{{ Storage::url(auth()->user()->avatar) }}'); background-size: cover; background-position: center;"></div>
                                            @else
                                                <div class="symbol-label fs-4 fs-md-3 fw-bold text-white bg-danger">
                                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <!--end::Avatar-->
                                        <!--begin::Username-->
                                        <div class="d-flex flex-column flex-grow-1 pe-0 min-w-0">
                                            @if(auth()->user())
                                                <div class="fw-bold d-flex flex-wrap align-items-center fs-6 fs-md-5 gap-1">
                                                    <span class="text-truncate" style="max-width: 120px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</span>
                                                    <span class="badge badge-light-danger fw-bold fs-8 px-2 py-1">{{ auth()->user()->role }}</span>
                                                </div>
                                                <a href="#" class="fw-semibold text-muted text-hover-primary fs-8 fs-md-7 text-truncate">{{ auth()->user()->email }}</a>
                                            @endif
                                        </div>
                                        <!--end::Username-->
                                    </div>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu separator-->
                                <div class="separator my-2"></div>
                                <!--end::Menu separator-->
                                @if(auth()->user())
                                <!--begin::Menu item-->
                                <div class="menu-item px-5">
                                    <a href="{{ route('profile') }}" class="menu-link px-5">
                                        <i class="ki-duotone ki-profile-circle me-2 fs-4">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        Meu Perfil
                                    </a>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu separator-->
                                <div class="separator my-2"></div>
                                <!--end::Menu separator-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-5">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a href="#" class="menu-link px-5" onclick="event.preventDefault(); this.closest('form').submit();">
                                            <i class="ki-duotone ki-entrance-left me-2 fs-4">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Sair
                                        </a>
                                    </form>
                                </div>
                                <!--end::Menu item-->
                                @endif
                            </div>
                            <!--end::User account menu-->
                        </div>
                        <!--end::User menu-->
                    </div>
                    <!--end::User panel-->
                </div>
                <!--end::Footer-->
            </div>