@extends('components.layouts.app')

@section('title', "Editar Sessão #{$session['numero']}/{$session['ano']}")

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Editar {{ $session['numero'] }}ª Sessão de {{ $session['ano'] }}
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
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.sessions.show', $session['id']) }}" class="text-muted text-hover-primary">Detalhes</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Editar</li>
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
                        <h2>Editar Informações da Sessão</h2>
                    </div>
                </div>
                <!--end::Card header-->
                
                <!--begin::Card body-->
                <div class="card-body">
                    <form action="{{ route('admin.sessions.update', $session['id']) }}" method="POST" id="kt_session_form">
                        @csrf
                        @method('PUT')
                        
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
                                           value="{{ old('numero', $session['numero']) }}" 
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
                                        @for($year = date('Y') - 2; $year <= date('Y') + 1; $year++)
                                            <option value="{{ $year }}" {{ old('ano', $session['ano']) == $year ? 'selected' : '' }}>
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
                                           value="{{ old('data', $session['data']) }}" required />
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
                                           value="{{ old('hora', $session['hora']) }}" required />
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
                        
                        <!--begin::Row-->
                        <div class="row mb-6">
                            <!--begin::Col-->
                            <div class="col-md-6">
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Tipo de Sessão</label>
                                    <select name="tipo_id" class="form-select form-select-solid" required>
                                        <option value="">Selecione o tipo de sessão</option>
                                        @foreach($tipos_sessao as $id => $nome)
                                            <option value="{{ $id }}" {{ old('tipo_id', $session['tipo_id']) == $id ? 'selected' : '' }}>
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
                            </div>
                            <!--end::Col-->
                            
                            <!--begin::Col-->
                            <div class="col-md-6">
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Status</label>
                                    <select name="status" class="form-select form-select-solid" required>
                                        <option value="preparacao" {{ old('status', $session['status']) == 'preparacao' ? 'selected' : '' }}>Preparação</option>
                                        <option value="agendada" {{ old('status', $session['status']) == 'agendada' ? 'selected' : '' }}>Agendada</option>
                                        <option value="exportada" {{ old('status', $session['status']) == 'exportada' ? 'selected' : '' }}>Exportada</option>
                                        <option value="concluida" {{ old('status', $session['status']) == 'concluida' ? 'selected' : '' }}>Concluída</option>
                                    </select>
                                    @error('status')
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
                                    <label class="fw-semibold fs-6 mb-2">Hora Inicial (Real)</label>
                                    <input type="time" name="hora_inicial" 
                                           class="form-control form-control-solid mb-3 mb-lg-0" 
                                           value="{{ old('hora_inicial', $session['hora_inicial']) }}" />
                                    <div class="form-text">Hora real de início da sessão (se já realizada)</div>
                                    @error('hora_inicial')
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
                                    <label class="fw-semibold fs-6 mb-2">Hora Final (Real)</label>
                                    <input type="time" name="hora_final" 
                                           class="form-control form-control-solid mb-3 mb-lg-0" 
                                           value="{{ old('hora_final', $session['hora_final']) }}" />
                                    <div class="form-text">Hora real de encerramento da sessão (se já realizada)</div>
                                    @error('hora_final')
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
                            <label class="fw-semibold fs-6 mb-2">Observações</label>
                            <textarea name="observacoes" 
                                      class="form-control form-control-solid" 
                                      rows="4" 
                                      placeholder="Observações sobre a sessão (opcional)">{{ old('observacoes', $session['observacoes']) }}</textarea>
                            @error('observacoes')
                                <div class="fv-plugins-message-container">
                                    <div class="fv-help-block">{{ $message }}</div>
                                </div>
                            @enderror
                        </div>
                        <!--end::Input group-->
                        
                        <!--begin::Actions-->
                        <div class="text-center pt-15">
                            <a href="{{ route('admin.sessions.show', $session['id']) }}" class="btn btn-light me-3">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <span class="indicator-label">Salvar Alterações</span>
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
    
    // Validation for hora_inicial and hora_final
    const horaInicial = document.querySelector('input[name="hora_inicial"]');
    const horaFinal = document.querySelector('input[name="hora_final"]');
    
    function validateHours() {
        if (horaInicial.value && horaFinal.value) {
            if (horaInicial.value >= horaFinal.value) {
                horaFinal.setCustomValidity('A hora final deve ser posterior à hora inicial');
            } else {
                horaFinal.setCustomValidity('');
            }
        } else {
            horaFinal.setCustomValidity('');
        }
    }
    
    horaInicial.addEventListener('change', validateHours);
    horaFinal.addEventListener('change', validateHours);
});
</script>
@endpush
@endsection