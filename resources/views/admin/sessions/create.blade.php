@extends('components.layouts.app')

@section('title', 'Nova Sessão')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Nova Sessão
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.sessions.index') }}" class="text-muted text-hover-primary">Sessões</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Nova Sessão</li>
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
                <div class="alert alert-danger">
                    <i class="ki-duotone ki-cross-circle fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ session('error') }}
                </div>
            @endif

            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Informações da Sessão</h2>
                    </div>
                </div>
                <!--end::Card header-->
                
                <!--begin::Card body-->
                <div class="card-body">
                    <form action="{{ route('admin.sessions.store') }}" method="POST" id="kt_session_form">
                        @csrf
                        
                        <!--begin::Row-->
                        <div class="row mb-6">
                            <!--begin::Col-->
                            <div class="col-md-6">
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Número da Sessão</label>
                                    <input type="number" name="numero" 
                                           class="form-control form-control-solid mb-3 mb-lg-0" 
                                           placeholder="Ex: 37" 
                                           value="{{ old('numero', \Carbon\Carbon::now()->format('W')) }}" 
                                           min="1" required />
                                    @error('numero')
                                        <div class="fv-plugins-message-container">
                                            <div class="fv-help-block">{{ $message }}</div>
                                        </div>
                                    @enderror
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Col-->
                            
                            <!--begin::Col-->
                            <div class="col-md-6">
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Ano</label>
                                    <select name="ano" class="form-select form-select-solid" required>
                                        @for($year = date('Y'); $year <= date('Y') + 1; $year++)
                                            <option value="{{ $year }}" {{ old('ano', date('Y')) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('ano')
                                        <div class="fv-plugins-message-container">
                                            <div class="fv-help-block">{{ $message }}</div>
                                        </div>
                                    @enderror
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->
                        
                        <!--begin::Row-->
                        <div class="row mb-6">
                            <!--begin::Col-->
                            <div class="col-md-6">
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Data</label>
                                    <input type="date" name="data" 
                                           class="form-control form-control-solid mb-3 mb-lg-0" 
                                           value="{{ old('data', \Carbon\Carbon::now()->addWeek()->format('Y-m-d')) }}" 
                                           min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required />
                                    @error('data')
                                        <div class="fv-plugins-message-container">
                                            <div class="fv-help-block">{{ $message }}</div>
                                        </div>
                                    @enderror
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Col-->
                            
                            <!--begin::Col-->
                            <div class="col-md-6">
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Hora</label>
                                    <input type="time" name="hora" 
                                           class="form-control form-control-solid mb-3 mb-lg-0" 
                                           value="{{ old('hora', '17:00') }}" required />
                                    @error('hora')
                                        <div class="fv-plugins-message-container">
                                            <div class="fv-help-block">{{ $message }}</div>
                                        </div>
                                    @enderror
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->
                        
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Tipo de Sessão</label>
                            <select name="tipo_id" class="form-select form-select-solid" required>
                                <option value="">Selecione o tipo de sessão</option>
                                @foreach($tipos_sessao as $id => $nome)
                                    <option value="{{ $id }}" {{ old('tipo_id') == $id ? 'selected' : '' }}>
                                        {{ $nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_id')
                                <div class="fv-plugins-message-container">
                                    <div class="fv-help-block">{{ $message }}</div>
                                </div>
                            @enderror
                        </div>
                        <!--end::Input group-->
                        
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="fw-semibold fs-6 mb-2">Observações</label>
                            <textarea name="observacoes" 
                                      class="form-control form-control-solid" 
                                      rows="4" 
                                      placeholder="Observações sobre a sessão (opcional)">{{ old('observacoes') }}</textarea>
                            @error('observacoes')
                                <div class="fv-plugins-message-container">
                                    <div class="fv-help-block">{{ $message }}</div>
                                </div>
                            @enderror
                        </div>
                        <!--end::Input group-->
                        
                        <!--begin::Actions-->
                        <div class="text-center pt-15">
                            <button type="reset" class="btn btn-light me-3">Cancelar</button>
                            <button type="submit" class="btn btn-primary">
                                <span class="indicator-label">Criar Sessão</span>
                                <span class="indicator-progress">
                                    Aguarde... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('kt_session_form');
    const submitButton = form.querySelector('[type="submit"]');
    
    form.addEventListener('submit', function() {
        submitButton.disabled = true;
        submitButton.querySelector('.indicator-label').style.display = 'none';
        submitButton.querySelector('.indicator-progress').style.display = 'inline-block';
    });
    
    // Auto-suggest numero based on current date
    const numeroInput = document.querySelector('input[name="numero"]');
    const anoSelect = document.querySelector('select[name="ano"]');
    
    function updateNumeroSuggestion() {
        const currentWeek = new Date().getWeek();
        if (!numeroInput.value || numeroInput.value == currentWeek) {
            numeroInput.value = currentWeek;
        }
    }
    
    anoSelect.addEventListener('change', updateNumeroSuggestion);
});

Date.prototype.getWeek = function() {
    const onejan = new Date(this.getFullYear(), 0, 1);
    return Math.ceil((((this - onejan) / 86400000) + onejan.getDay() + 1) / 7);
};
</script>
@endpush
@endsection