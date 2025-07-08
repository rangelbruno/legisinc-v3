@extends('components.layouts.app')

@section('title', $title ?? 'Editar Comissão')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Editar Comissão
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('comissoes.index') }}" class="text-muted text-hover-primary">Comissões</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('comissoes.show', $comissao['id']) }}" class="text-muted text-hover-primary">{{ $comissao['nome'] }}</a>
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
                <a href="{{ route('comissoes.show', $comissao['id']) }}" class="btn btn-sm btn-secondary">
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
                                    {{ strtoupper(substr($comissao['nome'], 0, 2)) }}
                                </div>
                            </div>
                            <!--end::Avatar-->
                            
                            <div class="d-flex flex-column">
                                <h3 class="fw-bold m-0">{{ $comissao['nome'] }}</h3>
                                <span class="text-muted">{{ $comissao['tipo_formatado'] ?? 'Tipo não definido' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-toolbar">
                        @php
                            $statusBadge = match($comissao['status'] ?? 'ativa') {
                                'ativa' => 'success',
                                'inativa' => 'secondary',
                                'suspensa' => 'warning',
                                'encerrada' => 'danger',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge badge-light-{{ $statusBadge }}">
                            {{ ucfirst($comissao['status'] ?? 'ativa') }}
                        </span>
                    </div>
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <form method="POST" action="{{ route('comissoes.update', $comissao['id']) }}" id="kt_comissao_edit_form">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-9 mb-8">
                            <!--begin::Col-->
                            <div class="col-md-12 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Nome da Comissão</label>
                                <input type="text" class="form-control form-control-solid" 
                                       name="nome" 
                                       value="{{ old('nome', $comissao['nome']) }}" 
                                       placeholder="Nome da comissão" required>
                            </div>
                            <!--end::Col-->
                        </div>
                        
                        <div class="row g-9 mb-8">
                            <!--begin::Col-->
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Tipo</label>
                                <select name="tipo" class="form-select form-select-solid" required>
                                    <option value="">Selecione o tipo</option>
                                    @foreach($tipos ?? [] as $value => $label)
                                        <option value="{{ $value }}" {{ old('tipo', $comissao['tipo']) === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Col-->
                            
                            <!--begin::Col-->
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Status</label>
                                <select name="status" class="form-select form-select-solid" required>
                                    @foreach($statusOptions ?? ['ativa' => 'Ativa', 'inativa' => 'Inativa', 'suspensa' => 'Suspensa', 'encerrada' => 'Encerrada'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('status', $comissao['status'] ?? 'ativa') === $value ? 'selected' : '' }}>
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
                                <label class="fs-6 fw-semibold mb-2">Descrição</label>
                                <textarea class="form-control form-control-solid" name="descricao" rows="4" 
                                          placeholder="Descrição da comissão">{{ old('descricao', $comissao['descricao'] ?? '') }}</textarea>
                            </div>
                            <!--end::Col-->
                        </div>
                        
                        <div class="row g-9 mb-8">
                            <!--begin::Col-->
                            <div class="col-md-12 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Finalidade</label>
                                <textarea class="form-control form-control-solid" name="finalidade" rows="3" 
                                          placeholder="Finalidade e objetivos da comissão" required>{{ old('finalidade', $comissao['finalidade'] ?? '') }}</textarea>
                            </div>
                            <!--end::Col-->
                        </div>
                        
                        <div class="separator my-8"></div>
                        
                        <h3 class="fw-bold mb-6">Mesa Diretora</h3>
                        
                        <div class="row g-9 mb-8">
                            <!--begin::Col-->
                            <div class="col-md-4 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Presidente</label>
                                <select name="presidente_id" class="form-select form-select-solid">
                                    <option value="">Selecione o presidente</option>
                                    @foreach($parlamentares ?? [] as $id => $nome)
                                        <option value="{{ $id }}" {{ old('presidente_id', $comissao['presidente']['id'] ?? null) == $id ? 'selected' : '' }}>
                                            {{ $nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Col-->
                            
                            <!--begin::Col-->
                            <div class="col-md-4 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Vice-Presidente</label>
                                <select name="vice_presidente_id" class="form-select form-select-solid">
                                    <option value="">Selecione o vice-presidente</option>
                                    @foreach($parlamentares ?? [] as $id => $nome)
                                        <option value="{{ $id }}" {{ old('vice_presidente_id', $comissao['vice_presidente']['id'] ?? null) == $id ? 'selected' : '' }}>
                                            {{ $nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Col-->
                            
                            <!--begin::Col-->
                            <div class="col-md-4 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Relator</label>
                                <select name="relator_id" class="form-select form-select-solid">
                                    <option value="">Selecione o relator</option>
                                    @foreach($parlamentares ?? [] as $id => $nome)
                                        <option value="{{ $id }}" {{ old('relator_id', $comissao['relator']['id'] ?? null) == $id ? 'selected' : '' }}>
                                            {{ $nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Col-->
                        </div>
                        
                        <div class="row g-9 mb-8">
                            <!--begin::Col-->
                            <div class="col-md-12 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Membros</label>
                                <input type="text" class="form-control form-control-solid" 
                                       name="membros" 
                                       value="{{ old('membros', is_array($comissao['membros'] ?? []) ? implode(', ', $comissao['membros']) : ($comissao['membros'] ?? '')) }}" 
                                       placeholder="Digite os nomes dos membros separados por vírgula">
                                <div class="form-text">Separe múltiplos membros com vírgula. Ex: João Silva, Maria Santos, Carlos Pereira</div>
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
    const form = document.getElementById('kt_comissao_edit_form');
    
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
                window.location.href = '{{ route("comissoes.show", $comissao["id"]) }}';
            }
        });
    }
});
</script>
<!--end::Script-->

@endsection