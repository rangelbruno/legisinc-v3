@extends('components.layouts.app')

@section('title', 'Configurar: ' . $modulo->nome)

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <i class="ki-duotone {{ $modulo->icon ?: 'ki-setting-2' }} fs-2 me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Configurar {{ $modulo->nome }}
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('parametros.index') }}" class="text-muted text-hover-primary">Parâmetros</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">{{ $modulo->nome }}</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!--begin::Secondary button-->
                <a href="{{ route('parametros.index') }}" class="btn btn-sm btn-flex btn-secondary">
                    <i class="ki-duotone ki-arrow-left fs-3"></i>
                    Voltar
                </a>
                <!--end::Secondary button-->
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Alert-->
            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-check fs-2hx text-success me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h5 class="mb-1">Sucesso</h5>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h5 class="mb-1">Erro</h5>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif
            <!--end::Alert-->

            @if($modulo->nome === 'Dados Gerais')
                <!--begin::Special layout for Dados Gerais - Tabs-->
                <div class="card card-flush">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <h2 class="fw-bold">Configuração de {{ $modulo->nome }}</h2>
                        </div>
                        <!--end::Card title-->
                    </div>
                    <!--end::Card header-->
                    
                    <!--begin::Card body-->
                    <div class="card-body">
                        <!--begin::Tabs-->
                        <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6">
                            @foreach($submodulos as $index => $submodulo)
                                <li class="nav-item">
                                    <a class="nav-link @if($index === 0) active @endif" data-bs-toggle="tab" href="#kt_tab_{{ $submodulo->id }}">
                                        @php
                                            $icon = match($submodulo->nome) {
                                                'Identificação' => 'ki-bank',
                                                'Endereço' => 'ki-geolocation',
                                                'Contatos' => 'ki-telephone',
                                                'Funcionamento' => 'ki-time',
                                                'Dados Gerais da Câmara' => 'ki-bank',
                                                'Gestão' => 'ki-user-square',
                                                default => 'ki-setting-2'
                                            };
                                        @endphp
                                        <i class="ki-duotone {{ $icon }} fs-3 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            @if($icon === 'ki-user-square')<span class="path3"></span>@endif
                                        </i>
                                        {{ $submodulo->nome }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <!--end::Tabs-->
                        
                        <!--begin::Tab content-->
                        <div class="tab-content" id="myTabContent">
                            @foreach($submodulos as $index => $submodulo)
                                <!--begin::Tab pane-->
                                <div class="tab-pane fade @if($index === 0) show active @endif" id="kt_tab_{{ $submodulo->id }}" role="tabpanel">
                                    <div class="row g-6">
                                        <div class="col-12">
                                            @if($submodulo->descricao)
                                                <div class="mb-6">
                                                    <p class="text-gray-600 fs-6 mb-0">{{ $submodulo->descricao }}</p>
                                                </div>
                                            @endif

                                            <!--begin::Form-->
                                            <form action="{{ route('parametros.salvar-configuracoes', $submodulo->id) }}" method="POST" class="submodule-form">
                                                @csrf
                                                
                                                @if(isset($submodulo->campos) && $submodulo->campos->count() > 0)
                                                    <!--begin::Dynamic fields-->
                                                    <div class="row">
                                                        @foreach($submodulo->campos->sortBy('ordem') as $campo)
                                                            @php
                                                                $valorAtual = $campo->valor_atual ?: (isset($submodulo->valores[$campo->nome]) ? $submodulo->valores[$campo->nome] : $campo->valor_padrao);
                                                            @endphp
                                                            
                                                            @if($campo->tipo_campo === 'text')
                                                                <div class="col-md-6">
                                                                    <div class="fv-row mb-7">
                                                                        <label class="fs-6 fw-semibold form-label mb-2 @if($campo->obrigatorio) required @endif">
                                                                            {{ $campo->label }}
                                                                        </label>
                                                                        <input type="text" 
                                                                            class="form-control form-control-solid" 
                                                                            name="{{ $campo->nome }}" 
                                                                            value="{{ $valorAtual }}"
                                                                            placeholder="{{ $campo->placeholder ?? '' }}"
                                                                            @if($campo->obrigatorio) required @endif />
                                                                        @if($campo->help_text)
                                                                            <div class="form-text">{{ $campo->help_text }}</div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @elseif($campo->tipo_campo === 'select')
                                                                <div class="col-md-6">
                                                                    <div class="fv-row mb-7">
                                                                        <label class="fs-6 fw-semibold form-label mb-2 @if($campo->obrigatorio) required @endif">
                                                                            {{ $campo->label }}
                                                                        </label>
                                                                        <select class="form-select form-select-solid" 
                                                                            name="{{ $campo->nome }}" 
                                                                            @if($campo->obrigatorio) required @endif>
                                                                            <option value="">Selecione...</option>
                                                                            @if($campo->opcoes)
                                                                                @foreach($campo->opcoes as $value => $label)
                                                                                    <option value="{{ $value }}" @if($valorAtual == $value) selected @endif>
                                                                                        {{ $label }}
                                                                                    </option>
                                                                                @endforeach
                                                                            @endif
                                                                        </select>
                                                                        @if($campo->help_text)
                                                                            <div class="form-text">{{ $campo->help_text }}</div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @elseif($campo->tipo_campo === 'number')
                                                                <div class="col-md-6">
                                                                    <div class="fv-row mb-7">
                                                                        <label class="fs-6 fw-semibold form-label mb-2 @if($campo->obrigatorio) required @endif">
                                                                            {{ $campo->label }}
                                                                        </label>
                                                                        <input type="number" 
                                                                            class="form-control form-control-solid" 
                                                                            name="{{ $campo->nome }}" 
                                                                            value="{{ $valorAtual }}"
                                                                            placeholder="{{ $campo->placeholder ?? '' }}"
                                                                            step="any"
                                                                            @if($campo->obrigatorio) required @endif />
                                                                        @if($campo->help_text)
                                                                            <div class="form-text">{{ $campo->help_text }}</div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @elseif($campo->tipo_campo === 'textarea')
                                                                <div class="col-md-12">
                                                                    <div class="fv-row mb-7">
                                                                        <label class="fs-6 fw-semibold form-label mb-2 @if($campo->obrigatorio) required @endif">
                                                                            {{ $campo->label }}
                                                                        </label>
                                                                        <textarea class="form-control form-control-solid" 
                                                                            name="{{ $campo->nome }}" 
                                                                            rows="4" 
                                                                            placeholder="{{ $campo->placeholder ?? '' }}"
                                                                            @if($campo->obrigatorio) required @endif>{{ $valorAtual }}</textarea>
                                                                        @if($campo->help_text)
                                                                            <div class="form-text">{{ $campo->help_text }}</div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @elseif($campo->tipo_campo === 'checkbox')
                                                                <div class="col-md-6">
                                                                    <div class="form-check form-check-custom form-check-solid mb-7">
                                                                        <input class="form-check-input" 
                                                                            type="checkbox" 
                                                                            name="{{ $campo->nome }}" 
                                                                            id="{{ $campo->nome }}_{{ $submodulo->id }}" 
                                                                            value="1"
                                                                            @if($valorAtual == '1' || $valorAtual === 'true') checked @endif />
                                                                        <label class="form-check-label" for="{{ $campo->nome }}_{{ $submodulo->id }}">
                                                                            {{ $campo->label }}
                                                                        </label>
                                                                        @if($campo->help_text)
                                                                            <div class="form-text">{{ $campo->help_text }}</div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    
                                                    <!--begin::Actions-->
                                                    <div class="d-flex justify-content-end pt-6">
                                                        <button type="reset" class="btn btn-light me-3">
                                                            Resetar
                                                        </button>
                                                        <button type="submit" class="btn btn-primary">
                                                            <span class="indicator-label">Salvar {{ $submodulo->nome }}</span>
                                                            <span class="indicator-progress">Por favor aguarde...
                                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                            </span>
                                                        </button>
                                                    </div>
                                                    <!--end::Actions-->
                                                @else
                                                    <!--begin::Empty state-->
                                                    <div class="d-flex flex-center flex-column py-10">
                                                        <i class="ki-duotone ki-information-5 fs-3x text-primary mb-5">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                        <h3 class="text-gray-800 mb-2">Configuração em Desenvolvimento</h3>
                                                        <p class="text-gray-600 fs-6 mb-0">
                                                            Os campos de configuração para este submódulo ainda não foram implementados.
                                                        </p>
                                                    </div>
                                                    <!--end::Empty state-->
                                                @endif
                                            </form>
                                            <!--end::Form-->
                                        </div>
                                    </div>
                                </div>
                                <!--end::Tab pane-->
                            @endforeach
                        </div>
                        <!--end::Tab content-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Special layout for Dados Gerais-->
            @else
                <!--begin::Regular layout for other modules-->
                <div class="row g-6 g-xl-9">
                    @forelse($submodulos as $submodulo)
                    <!--begin::Col-->
                    <div class="col-12">
                        <!--begin::Card-->
                        <div class="card card-flush mb-6">
                            <!--begin::Card header-->
                            <div class="card-header pt-8">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h3 class="fw-bold text-gray-900">{{ $submodulo->nome }}</h3>
                                </div>
                                <!--end::Card title-->
                                <!--begin::Card toolbar-->
                                <div class="card-toolbar">
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-light-primary me-3">
                                            {{ ucfirst($submodulo->tipo) }}
                                        </span>
                                        <span class="badge badge-light-{{ $submodulo->ativo ? 'success' : 'danger' }}">
                                            {{ $submodulo->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </div>
                                </div>
                                <!--end::Card toolbar-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body">
                                @if($submodulo->descricao)
                                    <div class="mb-6">
                                        <p class="text-gray-600 fs-6 mb-0">{{ $submodulo->descricao }}</p>
                                    </div>
                                @endif

                                <!--begin::Form-->
                                <form action="{{ route('parametros.salvar-configuracoes', $submodulo->id) }}" method="POST" class="submodule-form">
                                    @csrf
                                    
                                    @if(isset($submodulo->campos) && $submodulo->campos->count() > 0)
                                        <!--begin::Dynamic fields-->
                                        <div class="row">
                                            @foreach($submodulo->campos->sortBy('ordem') as $campo)
                                                @php
                                                    $valorAtual = $campo->valor_atual ?: (isset($submodulo->valores[$campo->nome]) ? $submodulo->valores[$campo->nome] : $campo->valor_padrao);
                                                @endphp
                                                
                                                @if($campo->tipo_campo === 'text')
                                                    <div class="col-md-6">
                                                        <div class="fv-row mb-7">
                                                            <label class="fs-6 fw-semibold form-label mb-2 @if($campo->obrigatorio) required @endif">
                                                                {{ $campo->label }}
                                                            </label>
                                                            <input type="text" 
                                                                class="form-control form-control-solid" 
                                                                name="{{ $campo->nome }}" 
                                                                value="{{ $valorAtual }}"
                                                                placeholder="{{ $campo->placeholder ?? '' }}"
                                                                @if($campo->obrigatorio) required @endif />
                                                            @if($campo->help_text)
                                                                <div class="form-text">{{ $campo->help_text }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($campo->tipo_campo === 'select')
                                                    <div class="col-md-6">
                                                        <div class="fv-row mb-7">
                                                            <label class="fs-6 fw-semibold form-label mb-2 @if($campo->obrigatorio) required @endif">
                                                                {{ $campo->label }}
                                                            </label>
                                                            <select class="form-select form-select-solid" 
                                                                name="{{ $campo->nome }}" 
                                                                @if($campo->obrigatorio) required @endif>
                                                                <option value="">Selecione...</option>
                                                                @if($campo->opcoes)
                                                                    @foreach($campo->opcoes as $value => $label)
                                                                        <option value="{{ $value }}" @if($valorAtual == $value) selected @endif>
                                                                            {{ $label }}
                                                                        </option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                            @if($campo->help_text)
                                                                <div class="form-text">{{ $campo->help_text }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($campo->tipo_campo === 'number')
                                                    <div class="col-md-6">
                                                        <div class="fv-row mb-7">
                                                            <label class="fs-6 fw-semibold form-label mb-2 @if($campo->obrigatorio) required @endif">
                                                                {{ $campo->label }}
                                                            </label>
                                                            <input type="number" 
                                                                class="form-control form-control-solid" 
                                                                name="{{ $campo->nome }}" 
                                                                value="{{ $valorAtual }}"
                                                                placeholder="{{ $campo->placeholder ?? '' }}"
                                                                step="any"
                                                                @if($campo->obrigatorio) required @endif />
                                                            @if($campo->help_text)
                                                                <div class="form-text">{{ $campo->help_text }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($campo->tipo_campo === 'textarea')
                                                    <div class="col-md-12">
                                                        <div class="fv-row mb-7">
                                                            <label class="fs-6 fw-semibold form-label mb-2 @if($campo->obrigatorio) required @endif">
                                                                {{ $campo->label }}
                                                            </label>
                                                            <textarea class="form-control form-control-solid" 
                                                                name="{{ $campo->nome }}" 
                                                                rows="4" 
                                                                placeholder="{{ $campo->placeholder ?? '' }}"
                                                                @if($campo->obrigatorio) required @endif>{{ $valorAtual }}</textarea>
                                                            @if($campo->help_text)
                                                                <div class="form-text">{{ $campo->help_text }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($campo->tipo_campo === 'checkbox')
                                                    <div class="col-md-6">
                                                        <div class="form-check form-check-custom form-check-solid mb-7">
                                                            <input class="form-check-input" 
                                                                type="checkbox" 
                                                                name="{{ $campo->nome }}" 
                                                                id="{{ $campo->nome }}" 
                                                                value="1"
                                                                @if($valorAtual == '1' || $valorAtual === 'true') checked @endif />
                                                            <label class="form-check-label" for="{{ $campo->nome }}">
                                                                {{ $campo->label }}
                                                            </label>
                                                            @if($campo->help_text)
                                                                <div class="form-text">{{ $campo->help_text }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        {{-- Adicionar funcionalidade de teste para IA --}}
                                        @if($modulo->nome === 'Configuração de IA')
                                            <div class="separator my-10"></div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card bg-light-info">
                                                        <div class="card-body">
                                                            <h5 class="card-title">
                                                                <i class="ki-duotone ki-technology-1 fs-2 text-info me-2">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                                Testar Conexão com IA
                                                            </h5>
                                                            <p class="card-text text-gray-700 mb-4">
                                                                Use o botão abaixo para testar se as configurações estão funcionando corretamente.
                                                            </p>
                                                            <button type="button" class="btn btn-info" id="btn-testar-ia">
                                                                <i class="ki-duotone ki-message-question fs-6 me-2">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                    <span class="path3"></span>
                                                                </i>
                                                                Testar Conexão
                                                            </button>
                                                            <div id="teste-resultado" class="mt-3" style="display: none;"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <!--begin::Actions-->
                                        <div class="d-flex justify-content-end pt-6">
                                            <button type="reset" class="btn btn-light me-3">
                                                Resetar
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <span class="indicator-label">Salvar Configurações</span>
                                                <span class="indicator-progress">Por favor aguarde...
                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
                                            </button>
                                        </div>
                                        <!--end::Actions-->
                                        <!--end::Dynamic fields-->
                                    @else
                                        <!--begin::Empty state-->
                                        <div class="d-flex flex-center flex-column py-10">
                                            <i class="ki-duotone ki-information-5 fs-3x text-primary mb-5">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            <h3 class="text-gray-800 mb-2">Configuração em Desenvolvimento</h3>
                                            <p class="text-gray-600 fs-6 mb-0">
                                                Os campos de configuração para este submódulo ainda não foram implementados.
                                            </p>
                                        </div>
                                        <!--end::Empty state-->
                                    @endif
                                </form>
                                <!--end::Form-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Col-->
                @empty
                        <!--begin::Empty state-->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body d-flex flex-center flex-column py-20">
                                    <div class="text-center">
                                        <i class="ki-duotone ki-element-11 fs-4x text-primary mb-5">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                        <h3 class="text-gray-800 mb-2">Nenhum submódulo encontrado</h3>
                                        <p class="text-gray-600 fs-6 mb-6">
                                            Este módulo ainda não possui submódulos configurados.
                                        </p>
                                        <a href="{{ route('parametros.index') }}" class="btn btn-primary">
                                            <i class="ki-duotone ki-arrow-left fs-3"></i>
                                            Voltar aos Parâmetros
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Empty state-->
                    @endforelse
                </div>
                <!--end::Regular layout-->
            @endif
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form submission handling with SweetAlert2
            const forms = document.querySelectorAll('.submodule-form');
            
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // Prevent default form submission
                    
                    const submitButton = this.querySelector('button[type="submit"]');
                    const formData = new FormData(this);
                    const actionUrl = this.action;
                    
                    // Get the current tab/section name for better feedback
                    const currentTabPane = this.closest('.tab-pane');
                    let sectionName = 'configurações';
                    
                    if (currentTabPane) {
                        const tabId = currentTabPane.id;
                        const correspondingTab = document.querySelector(`a[href="#${tabId}"]`);
                        if (correspondingTab) {
                            sectionName = correspondingTab.textContent.trim();
                        }
                    }
                    
                    // Show loading state
                    submitButton.querySelector('.indicator-label').style.display = 'none';
                    submitButton.querySelector('.indicator-progress').style.display = 'inline-block';
                    submitButton.disabled = true;
                    
                    // Show loading SweetAlert
                    Swal.fire({
                        title: `Salvando ${sectionName}...`,
                        html: `Por favor aguarde enquanto salvamos as configurações de <strong>${sectionName}</strong>.`,
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Submit form via AJAX
                    fetch(actionUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => Promise.reject(err));
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Success response
                        Swal.fire({
                            icon: 'success',
                            title: `${sectionName} salvo com sucesso!`,
                            html: data.message || `As configurações de <strong>${sectionName}</strong> foram salvas com sucesso.`,
                            showConfirmButton: true,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#50cd89',
                            timer: 3500,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'animate__animated animate__fadeInDown'
                            },
                            footer: '<small class="text-muted">As alterações estão ativas imediatamente</small>'
                        }).then(() => {
                            // Add success highlight to form
                            this.classList.add('form-success-highlight');
                            setTimeout(() => {
                                this.classList.remove('form-success-highlight');
                            }, 2000);
                            
                            // Trigger formSaved event on all form inputs
                            const formInputs = this.querySelectorAll('input, select, textarea');
                            formInputs.forEach(input => {
                                input.dispatchEvent(new CustomEvent('formSaved'));
                            });
                        });
                    })
                    .catch(error => {
                        console.error('Erro ao salvar:', error);
                        
                        // Error response
                        let errorMessage = 'Erro ao salvar configurações. Tente novamente.';
                        
                        if (error.message) {
                            errorMessage = error.message;
                        } else if (error.errors) {
                            // Laravel validation errors
                            const errors = Object.values(error.errors).flat();
                            errorMessage = errors.join('<br>');
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao salvar',
                            html: errorMessage,
                            showConfirmButton: true,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#f1416c'
                        });
                    })
                    .finally(() => {
                        // Reset button state
                        submitButton.querySelector('.indicator-label').style.display = 'inline-block';
                        submitButton.querySelector('.indicator-progress').style.display = 'none';
                        submitButton.disabled = false;
                    });
                });
            });
            
            // Track form changes to show unsaved changes indicator
            const allInputs = document.querySelectorAll('.submodule-form input, .submodule-form select, .submodule-form textarea');
            
            allInputs.forEach(input => {
                let originalValue = input.value;
                
                input.addEventListener('input', function() {
                    const form = this.closest('.submodule-form');
                    const submitButton = form.querySelector('button[type="submit"]');
                    
                    if (this.value !== originalValue) {
                        // Show unsaved changes indicator
                        if (!submitButton.classList.contains('btn-warning')) {
                            submitButton.classList.add('btn-warning');
                            submitButton.classList.remove('btn-primary');
                            
                            const originalText = submitButton.querySelector('.indicator-label').textContent;
                            submitButton.querySelector('.indicator-label').innerHTML = 
                                `<i class="ki-duotone ki-information-4 fs-6 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>${originalText} *`;
                        }
                        
                        // Add indicator to tab
                        const tabPane = this.closest('.tab-pane');
                        if (tabPane) {
                            const tabId = tabPane.id;
                            const correspondingTab = document.querySelector(`a[href="#${tabId}"]`);
                            if (correspondingTab && !correspondingTab.classList.contains('has-changes')) {
                                correspondingTab.classList.add('has-changes');
                            }
                        }
                    }
                });
                
                // Reset indicator after successful save
                input.addEventListener('formSaved', function() {
                    const form = this.closest('.submodule-form');
                    const submitButton = form.querySelector('button[type="submit"]');
                    
                    submitButton.classList.remove('btn-warning');
                    submitButton.classList.add('btn-primary');
                    
                    const labelText = submitButton.querySelector('.indicator-label').textContent.replace(' *', '');
                    submitButton.querySelector('.indicator-label').innerHTML = labelText;
                    
                    originalValue = this.value; // Update original value
                });
            });

            // Teste de conexão com IA
            const btnTestarIA = document.getElementById('btn-testar-ia');
            if (btnTestarIA) {
                btnTestarIA.addEventListener('click', function() {
                    testarConexaoIA();
                });
            }
        });

        // Modelos disponíveis por provedor
        const modelosPorProvedor = {
            'openai': {
                'gpt-3.5-turbo': 'GPT-3.5 Turbo (Rápido e econômico)',
                'gpt-4': 'GPT-4 (Mais preciso)',
                'gpt-4-turbo': 'GPT-4 Turbo (Balanceado)',
                'gpt-4o': 'GPT-4o (Mais recente)'
            },
            'anthropic': {
                'claude-3-haiku-20240307': 'Claude 3 Haiku (Rápido)',
                'claude-3-sonnet-20240229': 'Claude 3 Sonnet (Balanceado)',
                'claude-3-opus-20240229': 'Claude 3 Opus (Mais preciso)'
            },
            'google': {
                'gemini-1.5-flash': 'Gemini 1.5 Flash (Rápido)',
                'gemini-1.5-pro': 'Gemini 1.5 Pro (Balanceado)',
                'gemini-pro': 'Gemini Pro'
            },
            'local': {
                'llama2': 'Llama 2',
                'codellama': 'CodeLlama',
                'mistral': 'Mistral'
            }
        };

        // Função para atualizar modelos baseado no provedor
        function atualizarModelos() {
            const providerSelect = document.querySelector('select[name="ai_provider"]');
            const modelSelect = document.querySelector('select[name="ai_model"]');
            
            if (!providerSelect || !modelSelect) return;
            
            const providerSelecionado = providerSelect.value;
            const modelosDisponiveis = modelosPorProvedor[providerSelecionado] || {};
            
            // Limpar opções atuais
            modelSelect.innerHTML = '<option value="">Selecione um modelo...</option>';
            
            // Adicionar novos modelos
            Object.entries(modelosDisponiveis).forEach(([value, label]) => {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = label;
                modelSelect.appendChild(option);
            });
            
            // Se só há uma opção, selecionar automaticamente
            if (Object.keys(modelosDisponiveis).length === 1) {
                modelSelect.value = Object.keys(modelosDisponiveis)[0];
            }
        }

        // Inicializar quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            const providerSelect = document.querySelector('select[name="ai_provider"]');
            
            if (providerSelect) {
                // Atualizar modelos quando o provedor mudar
                providerSelect.addEventListener('change', atualizarModelos);
                
                // Atualizar modelos na inicialização
                atualizarModelos();
            }
        });

        function testarConexaoIA() {
            const btn = document.getElementById('btn-testar-ia');
            const resultado = document.getElementById('teste-resultado');
            
            // Coletar dados do formulário
            const formData = {};
            const form = btn.closest('form');
            const inputs = form.querySelectorAll('input, select, textarea');
            
            inputs.forEach(input => {
                if (input.name && input.name.startsWith('ai_')) {
                    formData[input.name] = input.value;
                }
            });
            
            // Validar campos obrigatórios
            if (!formData.ai_provider || !formData.ai_api_key || !formData.ai_model) {
                resultado.style.display = 'block';
                resultado.innerHTML = `
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Dados incompletos</h6>
                        <p class="mb-0">Preencha pelo menos Provider, API Key e Model antes de testar.</p>
                    </div>
                `;
                return;
            }
            
            // Mostrar loading
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Testando...';
            resultado.style.display = 'none';
            
            // Fazer requisição AJAX
            fetch('/api/ai/testar-conexao', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                // Mostrar resultado
                resultado.style.display = 'block';
                
                if (data.success) {
                    resultado.innerHTML = `
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check me-2"></i>Conexão bem-sucedida!</h6>
                            <p class="mb-1"><strong>Provedor:</strong> ${data.provider || 'N/A'}</p>
                            <p class="mb-0"><strong>Modelo:</strong> ${data.model || 'N/A'}</p>
                        </div>
                    `;
                } else {
                    resultado.innerHTML = `
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-times me-2"></i>Erro na conexão</h6>
                            <p class="mb-0">${data.message || 'Erro desconhecido'}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                resultado.style.display = 'block';
                resultado.innerHTML = `
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-times me-2"></i>Erro de conexão</h6>
                        <p class="mb-0">Não foi possível conectar com o servidor</p>
                    </div>
                `;
            })
            .finally(() => {
                // Restaurar botão
                btn.disabled = false;
                btn.innerHTML = `
                    <i class="ki-duotone ki-message-question fs-6 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    Testar Conexão
                `;
            });
        }
    </script>
@endpush

@push('styles')
<style>
/* Custom SweetAlert2 styles */
.swal2-popup {
    border-radius: 15px !important;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2) !important;
}

.swal2-title {
    font-weight: 600 !important;
    color: #181c32 !important;
}

.swal2-content {
    color: #7e8299 !important;
}

.swal2-success .swal2-success-circular-line-right,
.swal2-success .swal2-success-circular-line-left {
    background-color: #50cd89 !important;
}

.swal2-success .swal2-success-ring {
    border-color: #50cd89 !important;
}

.swal2-success .swal2-success-fix {
    background-color: #50cd89 !important;
}

.swal2-error .swal2-x-mark-line-left,
.swal2-error .swal2-x-mark-line-right {
    background-color: #f1416c !important;
}

.swal2-loader {
    border-color: #009ef7 transparent #009ef7 transparent !important;
}

.swal2-timer-progress-bar {
    background: linear-gradient(90deg, #009ef7 0%, #50cd89 100%) !important;
}

/* Form loading states */
.submodule-form button[type="submit"][disabled] {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Tab content smooth transition */
.tab-pane {
    transition: opacity 0.3s ease-in-out;
}

.tab-pane.fade:not(.show) {
    opacity: 0;
}

.tab-pane.fade.show {
    opacity: 1;
}

/* Form field focus animation */
.form-control:focus {
    border-color: #009ef7;
    box-shadow: 0 0 0 0.2rem rgba(0, 158, 247, 0.25);
    transition: all 0.3s ease;
}

/* Success highlight animation */
@keyframes successHighlight {
    0% { background-color: transparent; }
    50% { background-color: rgba(80, 205, 137, 0.1); }
    100% { background-color: transparent; }
}

.form-success-highlight {
    animation: successHighlight 2s ease-in-out;
}

/* Loading button animation */
@keyframes buttonPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.btn-loading {
    animation: buttonPulse 1.5s infinite;
}

/* Unsaved changes indicator */
.btn-warning {
    position: relative;
    overflow: visible;
}

.btn-warning::after {
    content: '';
    position: absolute;
    top: -2px;
    right: -2px;
    width: 8px;
    height: 8px;
    background: #ffc107;
    border-radius: 50%;
    border: 2px solid white;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
    }
}

/* Tab with unsaved changes indicator */
.nav-link.has-changes {
    position: relative;
}

.nav-link.has-changes::after {
    content: '●';
    color: #ffc107;
    position: absolute;
    top: 5px;
    right: 5px;
    font-size: 12px;
}
</style>
@endpush