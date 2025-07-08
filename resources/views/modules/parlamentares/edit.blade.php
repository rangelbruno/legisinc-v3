@extends('components.layouts.app')

@section('title', $title ?? 'Editar Parlamentar')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Editar Parlamentar
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('parlamentares.index') }}" class="text-muted text-hover-primary">Parlamentares</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('parlamentares.show', $parlamentar['id']) }}" class="text-muted text-hover-primary">{{ $parlamentar['nome'] }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Editar</li>
                </ul>
            </div>
            <!--end::Page title-->
            
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('parlamentares.show', $parlamentar['id']) }}" class="btn btn-sm btn-secondary">
                    <i class="ki-duotone ki-arrow-left fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Voltar
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="ki-duotone ki-check-circle fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="ki-duotone ki-cross-circle fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ session('error') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger">
                    <h4 class="alert-heading">Erro na validação:</h4>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center">
                            <!--begin::Avatar-->
                            <div class="symbol symbol-50px me-5">
                                <div class="symbol-label fs-1 fw-bold text-inverse-primary bg-light-primary">
                                    {{ strtoupper(substr($parlamentar['nome'], 0, 2)) }}
                                </div>
                            </div>
                            <!--end::Avatar-->
                            
                            <div class="d-flex flex-column">
                                <h3 class="fw-bold m-0">{{ $parlamentar['nome'] }}</h3>
                                <span class="text-muted">{{ $parlamentar['partido'] ?? 'Sem partido' }} - {{ $parlamentar['cargo'] }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-toolbar">
                        <span class="badge badge-light-{{ $parlamentar['status'] === 'ativo' ? 'success' : 'danger' }}">
                            {{ ucfirst($parlamentar['status'] ?? 'ativo') }}
                        </span>
                    </div>
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <form method="POST" action="{{ route('parlamentares.update', $parlamentar['id']) }}" id="kt_parlamentar_edit_form">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-9 mb-8">
                            <!--begin::Col-->
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Nome Completo</label>
                                <input type="text" class="form-control form-control-solid" 
                                       name="nome" 
                                       value="{{ old('nome', $parlamentar['nome']) }}" 
                                       placeholder="Nome completo do parlamentar" required>
                            </div>
                            <!--end::Col-->
                            
                            <!--begin::Col-->
                            <div class="col-md-3 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Partido</label>
                                <select name="partido" class="form-select form-select-solid" required>
                                    <option value="">Selecione o partido</option>
                                    @foreach($partidos ?? [] as $sigla => $nome)
                                        <option value="{{ $sigla }}" {{ old('partido', $parlamentar['partido']) === $sigla ? 'selected' : '' }}>
                                            {{ $sigla }} - {{ $nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Col-->
                            
                            <!--begin::Col-->
                            <div class="col-md-3 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Cargo</label>
                                <select name="cargo" class="form-select form-select-solid" required>
                                    <option value="">Selecione o cargo</option>
                                    @foreach($cargos ?? [] as $cargo)
                                        <option value="{{ $cargo }}" {{ old('cargo', $parlamentar['cargo']) === $cargo ? 'selected' : '' }}>
                                            {{ $cargo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Col-->
                        </div>
                        
                        <div class="row g-9 mb-8">
                            <!--begin::Col-->
                            <div class="col-md-4 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Email</label>
                                <input type="email" class="form-control form-control-solid" 
                                       name="email" 
                                       value="{{ old('email', $parlamentar['email'] ?? '') }}" 
                                       placeholder="email@exemplo.com">
                            </div>
                            <!--end::Col-->
                            
                            <!--begin::Col-->
                            <div class="col-md-4 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Telefone</label>
                                <input type="text" class="form-control form-control-solid" 
                                       name="telefone" 
                                       value="{{ old('telefone', $parlamentar['telefone'] ?? '') }}" 
                                       placeholder="(11) 99999-9999">
                            </div>
                            <!--end::Col-->
                            
                            <!--begin::Col-->
                            <div class="col-md-4 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Data de Nascimento</label>
                                <input type="date" class="form-control form-control-solid" 
                                       name="data_nascimento" 
                                       value="{{ old('data_nascimento', $parlamentar['data_nascimento'] ?? '') }}">
                            </div>
                            <!--end::Col-->
                        </div>
                        
                        <div class="row g-9 mb-8">
                            <!--begin::Col-->
                            <div class="col-md-4 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Profissão</label>
                                <input type="text" class="form-control form-control-solid" 
                                       name="profissao" 
                                       value="{{ old('profissao', $parlamentar['profissao'] ?? '') }}" 
                                       placeholder="Ex: Advogado, Médico, etc.">
                            </div>
                            <!--end::Col-->
                            
                            <!--begin::Col-->
                            <div class="col-md-4 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Escolaridade</label>
                                <select name="escolaridade" class="form-select form-select-solid">
                                    <option value="">Selecione a escolaridade</option>
                                    @foreach($escolaridadeOptions ?? [] as $option)
                                        <option value="{{ $option }}" {{ old('escolaridade', $parlamentar['escolaridade'] ?? '') === $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Col-->
                            
                            <!--begin::Col-->
                            <div class="col-md-4 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Status</label>
                                <select name="status" class="form-select form-select-solid">
                                    @foreach($statusOptions ?? ['ativo' => 'Ativo', 'inativo' => 'Inativo', 'licenciado' => 'Licenciado'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('status', $parlamentar['status'] ?? 'ativo') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Col-->
                        </div>
                        
                        <div class="row g-9 mb-8">
                            <!--begin::Col-->
                            <div class="col-md-12 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Comissões</label>
                                <input type="text" class="form-control form-control-solid" 
                                       name="comissoes" 
                                       value="{{ old('comissoes', is_array($parlamentar['comissoes'] ?? []) ? implode(', ', $parlamentar['comissoes']) : ($parlamentar['comissoes'] ?? '')) }}" 
                                       placeholder="Digite as comissões separadas por vírgula">
                                <div class="form-text">Separe múltiplas comissões com vírgula. Ex: Educação, Saúde, Finanças</div>
                            </div>
                            <!--end::Col-->
                        </div>
                        
                        <!--begin::Actions-->
                        <div class="text-center pt-15">
                            <button type="reset" class="btn btn-light me-3">
                                Cancelar
                            </button>
                            
                            <button type="submit" class="btn btn-primary">
                                <span class="indicator-label">Salvar Alterações</span>
                                <span class="indicator-progress">
                                    Salvando... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                        <!--end::Actions-->
                    </form>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<!--begin::Script-->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('kt_parlamentar_edit_form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitButton = form.querySelector('[type="submit"]');
            const buttonText = submitButton.querySelector('.indicator-label');
            const buttonProgress = submitButton.querySelector('.indicator-progress');
            
            // Show loading state
            submitButton.disabled = true;
            buttonText.classList.add('d-none');
            buttonProgress.classList.remove('d-none');
        });
    }
    
    // Reset button
    const resetButton = form.querySelector('[type="reset"]');
    if (resetButton) {
        resetButton.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Tem certeza que deseja descartar as alterações?')) {
                window.location.href = '{{ route("parlamentares.show", $parlamentar["id"]) }}';
            }
        });
    }
});
</script>
<!--end::Script-->

@endsection