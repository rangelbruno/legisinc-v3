@extends('components.layouts.app')

@section('title', 'Meu Perfil')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-0">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Meu Perfil
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Meu Perfil</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-6">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <h3 class="fw-bold m-0">Informações do Perfil</h3>
                    </div>
                    <!--end::Card title-->
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-lg-6">
                            <!--begin::Details-->
                            <div class="card card-flush h-lg-100">
                                <!--begin::Card header-->
                                <div class="card-header pt-7">
                                    <!--begin::Avatar-->
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-circle symbol-50px me-5">
                                            <div class="symbol-label fs-1 fw-bold text-white bg-{{ auth()->user()->getCorPerfil() }}">
                                                {{ auth()->user()->avatar }}
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <h3 class="mb-1">{{ auth()->user()->name }}</h3>
                                            <span class="badge badge-light-{{ auth()->user()->getCorPerfil() }} fw-bold">
                                                {{ auth()->user()->getPerfilFormatado() }}
                                            </span>
                                        </div>
                                    </div>
                                    <!--end::Avatar-->
                                </div>
                                <!--end::Card header-->

                                <!--begin::Card body-->
                                <div class="card-body pt-0">
                                    <!--begin::Details-->
                                    <div class="py-5">
                                        <!--begin::Details item-->
                                        <div class="py-5">
                                            <div class="fw-semibold fs-7 text-gray-600 text-uppercase">Email</div>
                                            <div class="fw-bold fs-6 text-gray-800">{{ auth()->user()->email }}</div>
                                        </div>
                                        <!--end::Details item-->

                                        @if(auth()->user()->documento)
                                        <!--begin::Details item-->
                                        <div class="py-5">
                                            <div class="fw-semibold fs-7 text-gray-600 text-uppercase">Documento</div>
                                            <div class="fw-bold fs-6 text-gray-800">{{ auth()->user()->documento }}</div>
                                        </div>
                                        <!--end::Details item-->
                                        @endif

                                        @if(auth()->user()->telefone)
                                        <!--begin::Details item-->
                                        <div class="py-5">
                                            <div class="fw-semibold fs-7 text-gray-600 text-uppercase">Telefone</div>
                                            <div class="fw-bold fs-6 text-gray-800">{{ auth()->user()->telefone }}</div>
                                        </div>
                                        <!--end::Details item-->
                                        @endif

                                        @if(auth()->user()->profissao)
                                        <!--begin::Details item-->
                                        <div class="py-5">
                                            <div class="fw-semibold fs-7 text-gray-600 text-uppercase">Profissão</div>
                                            <div class="fw-bold fs-6 text-gray-800">{{ auth()->user()->profissao }}</div>
                                        </div>
                                        <!--end::Details item-->
                                        @endif

                                        @if(auth()->user()->cargo_atual)
                                        <!--begin::Details item-->
                                        <div class="py-5">
                                            <div class="fw-semibold fs-7 text-gray-600 text-uppercase">Cargo Atual</div>
                                            <div class="fw-bold fs-6 text-gray-800">{{ auth()->user()->cargo_atual }}</div>
                                        </div>
                                        <!--end::Details item-->
                                        @endif

                                        @if(auth()->user()->partido)
                                        <!--begin::Details item-->
                                        <div class="py-5">
                                            <div class="fw-semibold fs-7 text-gray-600 text-uppercase">Partido</div>
                                            <div class="fw-bold fs-6 text-gray-800">{{ auth()->user()->partido }}</div>
                                        </div>
                                        <!--end::Details item-->
                                        @endif

                                        @if(auth()->user()->ultimo_acesso)
                                        <!--begin::Details item-->
                                        <div class="py-5">
                                            <div class="fw-semibold fs-7 text-gray-600 text-uppercase">Último Acesso</div>
                                            <div class="fw-bold fs-6 text-gray-800">{{ auth()->user()->ultimo_acesso->format('d/m/Y H:i') }}</div>
                                        </div>
                                        <!--end::Details item-->
                                        @endif
                                    </div>
                                    <!--end::Details-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Details-->
                        </div>

                        <div class="col-lg-6">
                            <!--begin::Permissions-->
                            <div class="card card-flush h-lg-100">
                                <!--begin::Card header-->
                                <div class="card-header pt-7">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold text-gray-800">Permissões do Perfil</span>
                                        <span class="text-gray-400 mt-1 fw-semibold fs-6">{{ auth()->user()->getPerfilFormatado() }}</span>
                                    </h3>
                                </div>
                                <!--end::Card header-->

                                <!--begin::Card body-->
                                <div class="card-body pt-0">
                                    @php
                                        $userRoles = auth()->user()->getRoleNames();
                                        $roleId = \DB::table('roles')->where('name', $userRoles->first())->value('id');
                                        $permissions = \DB::table('role_has_permissions')
                                            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                                            ->where('role_has_permissions.role_id', $roleId)
                                            ->pluck('permissions.name');
                                    @endphp

                                    <div class="scroll-y me-n5 pe-5" data-kt-scroll="true" data-kt-scroll-height="400px">
                                        @foreach($permissions->groupBy(function($item) { return explode('.', $item)[0]; }) as $module => $modulePermissions)
                                        <!--begin::Permission group-->
                                        <div class="mb-7">
                                            <div class="fw-bold text-gray-800 text-hover-primary fs-6 mb-3">
                                                {{ ucfirst($module) }}
                                            </div>
                                            @foreach($modulePermissions as $permission)
                                            <div class="d-flex align-items-center py-2">
                                                <span class="bullet bg-primary me-3"></span>
                                                <span class="text-gray-700 fs-7">{{ $permission }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                        <!--end::Permission group-->
                                        @endforeach
                                    </div>
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Permissions-->
                        </div>
                    </div>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
</div>
@endsection