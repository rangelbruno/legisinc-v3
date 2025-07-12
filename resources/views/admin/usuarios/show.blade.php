@extends('components.layouts.app')

@section('title', 'Detalhes do Usuário')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Detalhes do Usuário
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.usuarios.index') }}" class="text-muted text-hover-primary">Usuários</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">{{ $usuario->name }}</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-pencil fs-2"></i>
                    Editar
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <div class="row gy-5 g-xl-10">
                <!--begin::Col-->
                <div class="col-xl-4">
                    <!--begin::Card-->
                    <div class="card card-flush h-xl-100">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Informações Pessoais</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Avatar-->
                            <div class="d-flex flex-center flex-column mb-5">
                                <div class="symbol symbol-100px symbol-circle mb-7">
                                    @if($usuario->avatar && !is_string($usuario->avatar))
                                        <img src="{{ $usuario->avatar }}" alt="{{ $usuario->name }}" />
                                    @else
                                        <div class="symbol-label fs-3 bg-light-{{ $usuario->getCorPerfil() }} text-{{ $usuario->getCorPerfil() }}">
                                            {{ $usuario->avatar ?: substr($usuario->name, 0, 2) }}
                                        </div>
                                    @endif
                                </div>
                                <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-1">{{ $usuario->name }}</a>
                                <div class="fs-5 fw-semibold text-muted mb-6">{{ $usuario->email }}</div>
                                <div class="d-flex flex-wrap flex-center">
                                    <div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3">
                                        <div class="fs-4 fw-bold text-gray-700">
                                            <span class="w-75px">Status:</span>
                                            <span class="badge badge-light-{{ $usuario->ativo ? 'success' : 'danger' }}">
                                                {{ $usuario->ativo ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Avatar-->
                            
                            <!--begin::Details-->
                            <div class="d-flex flex-stack fs-4 py-3">
                                <div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#kt_user_view_details" role="button" aria-expanded="false" aria-controls="kt_user_view_details">
                                    Detalhes
                                    <span class="ms-2 rotate-180">
                                        <i class="ki-duotone ki-down fs-3"></i>
                                    </span>
                                </div>
                                <span data-bs-toggle="tooltip" data-bs-trigger="hover" title="Editar detalhes do usuário">
                                    <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-sm btn-light-primary">
                                        Editar
                                    </a>
                                </span>
                            </div>
                            <div class="separator"></div>
                            <div id="kt_user_view_details" class="collapse show">
                                <div class="pb-5 fs-6">
                                    <div class="fw-bold mt-5">Perfil</div>
                                    <div class="text-gray-600">
                                        <span class="badge badge-light-{{ $usuario->getCorPerfil() }}">
                                            {{ $usuario->getPerfilFormatado() }}
                                        </span>
                                    </div>
                                    
                                    @if($usuario->documento)
                                        <div class="fw-bold mt-5">Documento</div>
                                        <div class="text-gray-600">{{ $usuario->documento }}</div>
                                    @endif
                                    
                                    @if($usuario->telefone)
                                        <div class="fw-bold mt-5">Telefone</div>
                                        <div class="text-gray-600">{{ $usuario->telefone }}</div>
                                    @endif
                                    
                                    @if($usuario->data_nascimento)
                                        <div class="fw-bold mt-5">Data de Nascimento</div>
                                        <div class="text-gray-600">{{ $usuario->data_nascimento->format('d/m/Y') }}</div>
                                    @endif
                                    
                                    @if($usuario->profissao)
                                        <div class="fw-bold mt-5">Profissão</div>
                                        <div class="text-gray-600">{{ $usuario->profissao }}</div>
                                    @endif
                                    
                                    @if($usuario->cargo_atual)
                                        <div class="fw-bold mt-5">Cargo Atual</div>
                                        <div class="text-gray-600">{{ $usuario->cargo_atual }}</div>
                                    @endif
                                    
                                    @if($usuario->partido)
                                        <div class="fw-bold mt-5">Partido</div>
                                        <div class="text-gray-600">{{ $usuario->partido }}</div>
                                    @endif
                                </div>
                            </div>
                            <!--end::Details-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Col-->
                
                <!--begin::Col-->
                <div class="col-xl-8">
                    <!--begin::Card-->
                    <div class="card card-flush h-xl-100">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Informações do Sistema</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table-->
                            <div class="table-responsive">
                                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold text-muted">ID do Usuário</td>
                                            <td class="text-gray-800">#{{ $usuario->id }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">E-mail</td>
                                            <td class="text-gray-800">{{ $usuario->email }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Perfil/Função</td>
                                            <td>
                                                <span class="badge badge-light-{{ $usuario->getCorPerfil() }}">
                                                    {{ $usuario->getPerfilFormatado() }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Status da Conta</td>
                                            <td>
                                                <span class="badge badge-light-{{ $usuario->ativo ? 'success' : 'danger' }}">
                                                    {{ $usuario->ativo ? 'Ativa' : 'Inativa' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Data de Cadastro</td>
                                            <td class="text-gray-800">{{ $usuario->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Última Atualização</td>
                                            <td class="text-gray-800">{{ $usuario->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Último Acesso</td>
                                            <td class="text-gray-800">
                                                @if($usuario->ultimo_acesso)
                                                    {{ $usuario->ultimo_acesso->format('d/m/Y H:i') }}
                                                @else
                                                    <span class="text-muted">Nunca acessou</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($usuario->email_verified_at)
                                            <tr>
                                                <td class="fw-bold text-muted">E-mail Verificado</td>
                                                <td class="text-gray-800">{{ $usuario->email_verified_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <!--end::Table-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Col-->
            </div>
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection