@extends('components.layouts.app')

@section('title', $title ?? 'Novo Partido')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Novo Partido
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('partidos.index') }}" class="text-muted text-hover-primary">Partidos</a>
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
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!--begin::Form-->
            <form id="kt_account_profile_details_form" class="form" method="POST" action="{{ route('partidos.store') }}">
                @csrf
                <!--begin::Card-->
                <div class="card mb-5 mb-xl-10">
                    <!--begin::Card header-->
                    <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_profile_details" aria-expanded="true" aria-controls="kt_account_profile_details">
                        <!--begin::Card title-->
                        <div class="card-title m-0">
                            <h3 class="fw-bold m-0">Dados do Partido</h3>
                        </div>
                        <!--end::Card title-->
                    </div>
                    <!--begin::Card header-->
                    <!--begin::Content-->
                    <div id="kt_account_profile_details" class="collapse show">
                        <!--begin::Card body-->
                        <div class="card-body border-top p-9">
                            <!--begin::Input group-->
                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Sigla</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-8">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-6 fv-row">
                                            <input type="text" name="sigla" id="sigla" class="form-control form-control-lg form-control-solid @error('sigla') is-invalid @enderror" 
                                                   placeholder="Ex: PT" value="{{ old('sigla', request('sigla')) }}" maxlength="10" style="text-transform: uppercase;" />
                                            @error('sigla')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div id="sigla-feedback" class="form-text"></div>
                                        </div>
                                        <div class="col-lg-6 d-flex align-items-center">
                                            <button type="button" id="buscar-dados-btn" class="btn btn-light-primary btn-sm">
                                                <i class="ki-duotone ki-magnifier fs-4">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Buscar Dados
                                            </button>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Nome</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-8 fv-row">
                                    <input type="text" name="nome" id="nome" class="form-control form-control-lg form-control-solid @error('nome') is-invalid @enderror" 
                                           placeholder="Nome completo do partido" value="{{ old('nome', request('nome')) }}" />
                                    @error('nome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Número</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-8">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-6 fv-row">
                                            <input type="text" name="numero" id="numero" class="form-control form-control-lg form-control-solid @error('numero') is-invalid @enderror" 
                                                   placeholder="Ex: 13" value="{{ old('numero', request('numero')) }}" maxlength="3" />
                                            @error('numero')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div id="numero-feedback" class="form-text"></div>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">Presidente</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-8 fv-row">
                                    <input type="text" name="presidente" id="presidente" class="form-control form-control-lg form-control-solid @error('presidente') is-invalid @enderror" 
                                           placeholder="Nome do presidente" value="{{ old('presidente', request('presidente')) }}" />
                                    @error('presidente')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">Data de Fundação</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-8 fv-row">
                                    <input type="date" name="fundacao" id="fundacao" class="form-control form-control-lg form-control-solid @error('fundacao') is-invalid @enderror" 
                                           value="{{ old('fundacao', request('fundacao')) }}" />
                                    @error('fundacao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">Site</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-8 fv-row">
                                    <input type="url" name="site" id="site" class="form-control form-control-lg form-control-solid @error('site') is-invalid @enderror" 
                                           placeholder="https://exemplo.com" value="{{ old('site', request('site')) }}" />
                                    @error('site')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Status</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-8 fv-row">
                                    <select name="status" class="form-select form-select-lg form-select-solid @error('status') is-invalid @enderror">
                                        @foreach($statusOptions as $value => $label)
                                            <option value="{{ $value }}" {{ old('status', 'ativo') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Card body-->
                        <!--begin::Actions-->
                        <div class="card-footer d-flex justify-content-end py-6 px-9">
                            <a href="{{ route('partidos.index') }}" class="btn btn-light btn-active-light-primary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary" id="kt_account_profile_details_submit">Salvar</button>
                        </div>
                        <!--end::Actions-->
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Card-->
            </form>
            <!--end::Form-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const siglaInput = document.getElementById('sigla');
    const nomeInput = document.getElementById('nome');
    const numeroInput = document.getElementById('numero');
    const presidenteInput = document.getElementById('presidente');
    const fundacaoInput = document.getElementById('fundacao');
    const siteInput = document.getElementById('site');
    const buscarBtn = document.getElementById('buscar-dados-btn');
    const siglaFeedback = document.getElementById('sigla-feedback');
    const numeroFeedback = document.getElementById('numero-feedback');

    // Buscar dados do partido
    buscarBtn.addEventListener('click', function() {
        const sigla = siglaInput.value.trim().toUpperCase();
        
        if (!sigla) {
            showAlert('Por favor, digite uma sigla antes de buscar os dados.', 'warning');
            return;
        }

        buscarBtn.disabled = true;
        buscarBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Buscando...';

        // Primeiro tenta buscar na lista de partidos brasileiros
        fetch(`/api/partidos/buscar-sigla?sigla=${sigla}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    preencherFormulario(data.data);
                    showAlert(`Dados encontrados! Fonte: ${data.source === 'local' ? 'Banco local' : 'Base de partidos brasileiros'}`, 'success');
                } else {
                    showAlert('Partido não encontrado na base de dados.', 'info');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showAlert('Erro ao buscar dados do partido.', 'error');
            })
            .finally(() => {
                buscarBtn.disabled = false;
                buscarBtn.innerHTML = '<i class="ki-duotone ki-magnifier fs-4"><span class="path1"></span><span class="path2"></span></i> Buscar Dados';
            });
    });

    // Validação de sigla em tempo real
    let siglaTimeout;
    siglaInput.addEventListener('input', function() {
        clearTimeout(siglaTimeout);
        const sigla = this.value.trim().toUpperCase();
        this.value = sigla;

        if (sigla.length >= 2) {
            siglaTimeout = setTimeout(() => {
                validarSigla(sigla);
            }, 500);
        } else {
            siglaFeedback.innerHTML = '';
        }
    });

    // Validação de número em tempo real
    let numeroTimeout;
    numeroInput.addEventListener('input', function() {
        clearTimeout(numeroTimeout);
        const numero = this.value.trim();

        if (numero.length >= 1) {
            numeroTimeout = setTimeout(() => {
                validarNumero(numero);
            }, 500);
        } else {
            numeroFeedback.innerHTML = '';
        }
    });

    function preencherFormulario(dados) {
        if (dados.nome) nomeInput.value = dados.nome;
        if (dados.numero) numeroInput.value = dados.numero;
        if (dados.presidente) presidenteInput.value = dados.presidente;
        if (dados.fundacao) fundacaoInput.value = dados.fundacao;
        if (dados.site) siteInput.value = dados.site;
    }

    function validarSigla(sigla) {
        fetch(`/api/partidos/validar-sigla?sigla=${sigla}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.exists) {
                        siglaFeedback.innerHTML = '<span class="text-danger">⚠️ Esta sigla já está em uso</span>';
                        siglaInput.classList.add('is-invalid');
                    } else {
                        siglaFeedback.innerHTML = '<span class="text-success">✓ Sigla disponível</span>';
                        siglaInput.classList.remove('is-invalid');
                    }
                }
            })
            .catch(error => {
                console.error('Erro na validação:', error);
            });
    }

    function validarNumero(numero) {
        fetch(`/api/partidos/validar-numero?numero=${numero}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.exists) {
                        numeroFeedback.innerHTML = '<span class="text-danger">⚠️ Este número já está em uso</span>';
                        numeroInput.classList.add('is-invalid');
                    } else {
                        numeroFeedback.innerHTML = '<span class="text-success">✓ Número disponível</span>';
                        numeroInput.classList.remove('is-invalid');
                    }
                }
            })
            .catch(error => {
                console.error('Erro na validação:', error);
            });
    }

    function showAlert(message, type) {
        let icon = 'info';
        if (type === 'success') icon = 'success';
        if (type === 'error') icon = 'error';
        if (type === 'warning') icon = 'warning';

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: message,
                icon: icon,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            alert(message);
        }
    }
});
</script>

@endsection