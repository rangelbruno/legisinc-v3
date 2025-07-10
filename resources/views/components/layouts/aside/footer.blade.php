<div class="aside-footer flex-column-auto px-9" id="kt_aside_footer">
    <!--begin::User panel-->
    <div class="d-flex flex-stack">
        <!--begin::Wrapper-->
        <div class="d-flex align-items-center">
            <!--begin::Avatar-->
            <div class="symbol symbol-circle symbol-40px">
                @auth
                    <div class="symbol-label fs-3 fw-bold text-white bg-{{ auth()->user()->getCorPerfil() }}">
                        {{ auth()->user()->avatar }}
                    </div>
                @else
                    <img src="assets/media/avatars/300-1.jpg" alt="photo" />
                @endauth
            </div>
            <!--end::Avatar-->
            <!--begin::User info-->
            <div class="ms-2">
                <!--begin::Name-->
                @auth
                    <a href="{{ route('user.profile') }}" class="text-gray-800 text-hover-primary fs-6 fw-bold lh-1">{{ auth()->user()->name }}</a>
                @else
                    <a href="#" class="text-gray-800 text-hover-primary fs-6 fw-bold lh-1">Usuário</a>
                @endauth
                <!--end::Name-->
                <!--begin::Major-->
                @auth
                    <span class="text-muted fw-semibold d-block fs-7 lh-1">{{ auth()->user()->getPerfilFormatado() }}</span>
                @else
                    <span class="text-muted fw-semibold d-block fs-7 lh-1">Faça login</span>
                @endauth
                <!--end::Major-->
            </div>
            <!--end::User info-->
        </div>
        <!--end::Wrapper-->
        <!--begin::User menu-->
        <div class="ms-1">
            <div class="btn btn-sm btn-icon btn-active-color-primary position-relative me-n2"
                data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-overflow="true"
                data-kt-menu-placement="top-end">
                <i class="ki-duotone ki-setting-2 fs-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
            <!--begin::User account menu-->
            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
                data-kt-menu="true">
                <!--begin::Menu item-->
                <div class="menu-item px-3">
                    <div class="menu-content d-flex align-items-center px-3">
                        <!--begin::Avatar-->
                        <div class="symbol symbol-50px me-5">
                            @auth
                                <div class="symbol-label fs-3 fw-bold text-white bg-{{ auth()->user()->getCorPerfil() }}">
                                    {{ auth()->user()->avatar }}
                                </div>
                            @else
                                <img alt="Logo" src="assets/media/avatars/300-1.jpg" />
                            @endauth
                        </div>
                        <!--end::Avatar-->
                        <!--begin::Username-->
                        <div class="d-flex flex-column">
                            @auth
                                <div class="fw-bold d-flex align-items-center fs-5">{{ auth()->user()->name }}
                                    <span class="badge badge-light-{{ auth()->user()->getCorPerfil() }} fw-bold fs-8 px-2 py-1 ms-2">{{ auth()->user()->getPerfilFormatado() }}</span>
                                </div>
                                <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">{{ auth()->user()->email }}</a>
                            @else
                                <div class="fw-bold d-flex align-items-center fs-5">Usuário
                                    <span class="badge badge-light-secondary fw-bold fs-8 px-2 py-1 ms-2">Visitante</span>
                                </div>
                                <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">Faça login</a>
                            @endauth
                        </div>
                        <!--end::Username-->
                    </div>
                </div>
                <!--end::Menu item-->
                <!--begin::Menu separator-->
                <div class="separator my-2"></div>
                <!--end::Menu separator-->
                @auth
                <!--begin::Menu item-->
                <div class="menu-item px-5">
                    <a href="{{ route('user.profile') }}" class="menu-link px-5">
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
                    <form method="POST" action="{{ route('auth.logout') }}">
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
                @else
                <!--begin::Menu item-->
                <div class="menu-item px-5">
                    <a href="{{ route('login') }}" class="menu-link px-5">
                        <i class="ki-duotone ki-entrance-right me-2 fs-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Fazer Login
                    </a>
                </div>
                <!--end::Menu item-->
                @endauth
            </div>
            <!--end::User account menu-->
        </div>
        <!--end::User menu-->
    </div>
    <!--end::User panel-->
</div>