@extends('layouts.app')

@section('title', 'Criar Novo Template')

@section('content')
<div class="container">
    <!--begin::Toolbar-->
    <div class="toolbar mb-5 mb-lg-7" id="kt_toolbar">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                <i class="ki-duotone ki-plus fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Criar Novo Template
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Administração</li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('templates.index') }}" class="text-muted text-hover-primary">Templates</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-gray-900">Criar Novo</li>
            </ul>
        </div>
    </div>
    <!--end::Toolbar-->

    <div class="row">
        <div class="col-lg-8">
            <!--begin::Form-->
            <form id="kt_create_template_form" method="POST" action="{{ route('templates.store') }}" class="form">
                @csrf
                
                <!--begin::Card-->
                <div class="card card-flush">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Informações do Template</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <label class="required form-label">Tipo de Proposição</label>
                            <select name="tipo_proposicao_id" id="tipo_proposicao_select" class="form-select form-select-solid" data-control="select2" data-placeholder="Selecione um tipo de proposição">
                                <option></option>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->id }}" 
                                            data-has-template="{{ $tipo->hasTemplate() ? 'true' : 'false' }}"
                                            {{ old('tipo_proposicao_id') == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nome }}
                                        @if($tipo->hasTemplate())
                                            (já possui template)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_proposicao_id')
                                <div class="fv-plugins-message-container invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="text-muted fs-7 mt-2">
                                Escolha o tipo de proposição para o qual deseja criar um template.
                            </div>
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group - Substituir existente-->
                        <div class="mb-10 fv-row" id="substituir_existente_group" style="display: none;">
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" name="substituir_existente" id="substituir_existente" {{ old('substituir_existente') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-gray-400 ms-3" for="substituir_existente">
                                    Substituir template existente
                                </label>
                            </div>
                            <div class="text-muted fs-7 mt-2">
                                <i class="ki-duotone ki-warning fs-7 text-warning me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                Atenção: O template atual será permanentemente removido e substituído pelo novo.
                            </div>
                        </div>
                        <!--end::Input group-->
                    </div>
                </div>
                <!--end::Card-->

                <!--begin::Actions-->
                <div class="d-flex justify-content-end mt-10">
                    <a href="{{ route('templates.index') }}" class="btn btn-light me-5">Cancelar</a>
                    <button type="submit" id="kt_create_template_submit" class="btn btn-primary">
                        <span class="indicator-label">Criar Template</span>
                        <span class="indicator-progress">Aguarde...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
                <!--end::Actions-->
            </form>
            <!--end::Form-->
        </div>

        <div class="col-lg-4">
            <!--begin::Card - Instruções-->
            <div class="card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="fw-bold text-gray-800">
                            <i class="ki-duotone ki-information-5 fs-2 text-primary me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            Como Funciona
                        </h3>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="mb-8">
                        <div class="d-flex align-items-start mb-5">
                            <div class="symbol symbol-35px symbol-circle me-3">
                                <div class="symbol-label bg-light-primary">
                                    <span class="text-primary fw-bold fs-7">1</span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="text-gray-800 fw-semibold fs-6 mb-1">Selecionar Tipo</h5>
                                <div class="text-gray-600 fs-7">Escolha o tipo de proposição para criar o template</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mb-5">
                            <div class="symbol symbol-35px symbol-circle me-3">
                                <div class="symbol-label bg-light-success">
                                    <span class="text-success fw-bold fs-7">2</span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="text-gray-800 fw-semibold fs-6 mb-1">Criar Template</h5>
                                <div class="text-gray-600 fs-7">Clique em "Criar Template" para prosseguir</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mb-5">
                            <div class="symbol symbol-35px symbol-circle me-3">
                                <div class="symbol-label bg-light-info">
                                    <span class="text-info fw-bold fs-7">3</span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="text-gray-800 fw-semibold fs-6 mb-1">Editar no ONLYOFFICE</h5>
                                <div class="text-gray-600 fs-7">Use o editor integrado para criar seu documento</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-start">
                            <div class="symbol symbol-35px symbol-circle me-3">
                                <div class="symbol-label bg-light-warning">
                                    <span class="text-warning fw-bold fs-7">4</span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="text-gray-800 fw-semibold fs-6 mb-1">Usar Variáveis</h5>
                                <div class="text-gray-600 fs-7">Adicione variáveis como <code>${ementa}</code></div>
                            </div>
                        </div>
                    </div>

                    <div class="separator my-5"></div>

                    <div class="mb-8">
                        <h4 class="text-gray-800 fw-bold fs-6 mb-4">
                            <i class="ki-duotone ki-code fs-5 text-success me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                            Variáveis Principais
                        </h4>
                        <div class="mb-3">
                            <code class="fs-7">${numero_proposicao}</code>
                            <div class="text-gray-600 fs-8">Número da proposição</div>
                        </div>
                        <div class="mb-3">
                            <code class="fs-7">${ementa}</code>
                            <div class="text-gray-600 fs-8">Ementa da proposição</div>
                        </div>
                        <div class="mb-3">
                            <code class="fs-7">${texto}</code>
                            <div class="text-gray-600 fs-8">Texto principal</div>
                        </div>
                        <div class="mb-3">
                            <code class="fs-7">${autor_nome}</code>
                            <div class="text-gray-600 fs-8">Nome do autor</div>
                        </div>
                        <div class="mb-3">
                            <code class="fs-7">${data_atual}</code>
                            <div class="text-gray-600 fs-8">Data atual</div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Card - Instruções-->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoSelect = document.getElementById('tipo_proposicao_select');
    const substituirGroup = document.getElementById('substituir_existente_group');
    const substituirCheckbox = document.getElementById('substituir_existente');
    const form = document.getElementById('kt_create_template_form');
    
    // Mostrar/ocultar opção de substituir baseado na seleção
    tipoSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const hasTemplate = selectedOption.getAttribute('data-has-template') === 'true';
        
        if (hasTemplate) {
            substituirGroup.style.display = 'block';
        } else {
            substituirGroup.style.display = 'none';
            substituirCheckbox.checked = false;
        }
    });
    
    // Trigger inicial se já houver valor selecionado
    if (tipoSelect.value) {
        tipoSelect.dispatchEvent(new Event('change'));
    }
    
    // Validação do formulário
    form.addEventListener('submit', function(e) {
        const submitButton = document.getElementById('kt_create_template_submit');
        const selectedOption = tipoSelect.options[tipoSelect.selectedIndex];
        const hasTemplate = selectedOption.getAttribute('data-has-template') === 'true';
        
        if (!tipoSelect.value) {
            e.preventDefault();
            Swal.fire({
                title: 'Erro',
                text: 'Por favor, selecione um tipo de proposição.',
                icon: 'error',
                confirmButtonText: 'Ok'
            });
            return;
        }
        
        if (hasTemplate && !substituirCheckbox.checked) {
            e.preventDefault();
            Swal.fire({
                title: 'Template já existe',
                text: 'Este tipo já possui um template. Marque a opção "Substituir template existente" para criar um novo.',
                icon: 'warning',
                confirmButtonText: 'Ok'
            });
            return;
        }
        
        // Mostrar loading
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;
    });
});
</script>
@endpush