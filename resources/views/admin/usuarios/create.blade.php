@extends('components.layouts.app')

@section('title', 'Novo Usuário')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Novo Usuário
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
                    <li class="breadcrumb-item text-muted">Novo</li>
                </ul>
            </div>
            <!--end::Page title-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Dados do Usuário</h2>
                    </div>
                </div>
                <!--end::Card header-->
                
                <!--begin::Form-->
                <form id="kt_user_form" action="{{ route('admin.usuarios.store') }}" method="POST">
                    @csrf
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">Nome Completo</label>
                            <div class="col-lg-8">
                                <input type="text" name="name" class="form-control form-control-lg form-control-solid @error('name') is-invalid @enderror" 
                                       placeholder="Nome completo do usuário" value="{{ old('name') }}" required />
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">E-mail</label>
                            <div class="col-lg-8">
                                <input type="email" name="email" class="form-control form-control-lg form-control-solid @error('email') is-invalid @enderror" 
                                       placeholder="email@exemplo.com" value="{{ old('email') }}" required />
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">Senha</label>
                            <div class="col-lg-8">
                                <input type="password" name="password" class="form-control form-control-lg form-control-solid @error('password') is-invalid @enderror" 
                                       placeholder="Digite uma senha segura" required />
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">Confirmar Senha</label>
                            <div class="col-lg-8">
                                <input type="password" name="password_confirmation" class="form-control form-control-lg form-control-solid" 
                                       placeholder="Confirme a senha" required />
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">Documento</label>
                            <div class="col-lg-8">
                                <input type="text" name="documento" class="form-control form-control-lg form-control-solid @error('documento') is-invalid @enderror" 
                                       placeholder="CPF, RG ou outro documento" value="{{ old('documento') }}" />
                                @error('documento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">Telefone</label>
                            <div class="col-lg-8">
                                <input type="text" name="telefone" class="form-control form-control-lg form-control-solid @error('telefone') is-invalid @enderror" 
                                       placeholder="(00) 00000-0000" value="{{ old('telefone') }}" />
                                @error('telefone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">Data de Nascimento</label>
                            <div class="col-lg-8">
                                <input type="date" name="data_nascimento" class="form-control form-control-lg form-control-solid @error('data_nascimento') is-invalid @enderror" 
                                       value="{{ old('data_nascimento') }}" />
                                @error('data_nascimento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">Profissão</label>
                            <div class="col-lg-8">
                                <input type="text" name="profissao" class="form-control form-control-lg form-control-solid @error('profissao') is-invalid @enderror" 
                                       placeholder="Profissão do usuário" value="{{ old('profissao') }}" />
                                @error('profissao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">Cargo Atual</label>
                            <div class="col-lg-8">
                                <input type="text" name="cargo_atual" class="form-control form-control-lg form-control-solid @error('cargo_atual') is-invalid @enderror" 
                                       placeholder="Cargo atual do usuário" value="{{ old('cargo_atual') }}" />
                                @error('cargo_atual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">Partido</label>
                            <div class="col-lg-8">
                                <input type="text" name="partido" class="form-control form-control-lg form-control-solid @error('partido') is-invalid @enderror" 
                                       placeholder="Partido político (se aplicável)" value="{{ old('partido') }}" />
                                @error('partido')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">Perfil</label>
                            <div class="col-lg-8">
                                <select name="role" class="form-select form-select-lg form-select-solid @error('role') is-invalid @enderror" required>
                                    <option value="">Selecione um perfil</option>
                                    @foreach($perfis as $key => $nome)
                                        <option value="{{ $key }}" {{ old('role') === $key ? 'selected' : '' }}>
                                            {{ $nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">Status</label>
                            <div class="col-lg-8">
                                <div class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" name="ativo" value="1" 
                                           {{ old('ativo', '1') ? 'checked' : '' }} />
                                    <label class="form-check-label">
                                        Usuário ativo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Card body-->
                    
                    <!--begin::Actions-->
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-light btn-active-light-primary me-2">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Salvar</span>
                            <span class="indicator-progress">Salvando...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                    <!--end::Actions-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Card-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('kt_user_form');
    const submitButton = form.querySelector('[type="submit"]');
    
    form.addEventListener('submit', function(e) {
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;
    });
});
</script>
@endpush
@endsection