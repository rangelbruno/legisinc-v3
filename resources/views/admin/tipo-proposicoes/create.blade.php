@extends('components.layouts.app')

@section('title', 'Novo Tipo de Proposição')

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <!--begin::Page title-->
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                Novo Tipo de Proposição
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Admin</li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('admin.tipo-proposicoes.index') }}" class="text-muted text-hover-primary">Tipos de Proposição</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Novo</li>
            </ul>
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('admin.tipo-proposicoes.index') }}" class="btn btn-sm fw-bold btn-secondary">
                <i class="ki-duotone ki-arrow-left fs-2"></i>
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
        <!--begin::Form-->
        <form id="kt_tipo_form" class="form d-flex flex-column flex-lg-row" method="POST" action="{{ route('admin.tipo-proposicoes.store') }}">
            @csrf
            <!--begin::Aside column-->
            <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                <!--begin::Thumbnail settings-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <h2>Visualização</h2>
                        </div>
                        <!--end::Card title-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body text-center pt-0">
                        <!--begin::Image input-->
                        <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3" data-kt-image-input="true">
                            <!--begin::Preview existing avatar-->
                            <div class="image-input-wrapper w-150px h-150px" id="icon-preview">
                                <div class="d-flex align-items-center justify-content-center h-100">
                                    <i class="ki-duotone ki-document fs-4x text-primary" id="preview-icon">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>
                            <!--end::Preview existing avatar-->
                        </div>
                        <!--end::Image input-->
                        <!--begin::Description-->
                        <div class="text-muted fs-7">Preview do ícone selecionado</div>
                        <!--end::Description-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Thumbnail settings-->

                <!--begin::Status-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <h2>Status</h2>
                        </div>
                        <!--end::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <div class="rounded-circle bg-success w-15px h-15px" id="kt_tipo_status"></div>
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Select2-->
                        <select class="form-select mb-2" name="ativo" data-control="select2" data-hide-search="true" data-placeholder="Selecione um status">
                            <option value="1" selected>Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                        <!--end::Select2-->
                        <!--begin::Description-->
                        <div class="text-muted fs-7">Define se o tipo estará disponível para uso.</div>
                        <!--end::Description-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Status-->
            </div>
            <!--end::Aside column-->

            <!--begin::Main column-->
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                <!--begin::General options-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Informações Gerais</h2>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Input group - Autocomplete-->
                        <div class="mb-10 fv-row">
                            <!--begin::Label-->
                            <label class="form-label">Buscar Tipo Existente</label>
                            <!--end::Label-->
                            <!--begin::Autocomplete container-->
                            <div class="position-relative">
                                <!--begin::Input-->
                                <input type="text" id="autocomplete-search" class="form-control mb-2" placeholder="Digite para buscar tipos de proposição existentes..." />
                                <!--end::Input-->
                                <!--begin::Dropdown-->
                                <div id="autocomplete-dropdown" class="dropdown-menu w-100" style="display: none; max-height: 300px; overflow-y: auto; position: absolute; top: 100%; left: 0; z-index: 1000;">
                                    <!-- Results will be populated here -->
                                </div>
                                <!--end::Dropdown-->
                            </div>
                            <!--end::Autocomplete container-->
                            <!--begin::Description-->
                            <div class="text-muted fs-7">Digite para buscar e auto-preencher com dados de tipos existentes.</div>
                            <!--end::Description-->
                        </div>
                        <!--end::Input group - Autocomplete-->

                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <!--begin::Label-->
                            <label class="required form-label">Nome do Tipo</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" name="nome" class="form-control mb-2" placeholder="Ex: Projeto de Lei Ordinária" value="{{ old('nome') }}" />
                            <!--end::Input-->
                            <!--begin::Description-->
                            <div class="text-muted fs-7">Nome completo que será exibido para os usuários.</div>
                            <!--end::Description-->
                            @error('nome')
                                <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <!--begin::Label-->
                            <label class="required form-label">Código</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" name="codigo" class="form-control mb-2" placeholder="Ex: projeto_lei_ordinaria" value="{{ old('codigo') }}" />
                            <!--end::Input-->
                            <!--begin::Description-->
                            <div class="text-muted fs-7">Código único (apenas letras minúsculas, números e underscores).</div>
                            <!--end::Description-->
                            @error('codigo')
                                <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Input group-->


                        <!--begin::Input group-->
                        <div class="row mb-10">
                            <!--begin::Col-->
                            <div class="col-md-6 fv-row">
                                <!--begin::Label-->
                                <label class="required form-label">Ícone</label>
                                <!--end::Label-->
                                <!--begin::Select2-->
                                <select class="form-select mb-2" name="icone" data-control="select2" data-placeholder="Selecione um ícone" id="icone-select">
                                    <option></option>
                                    @foreach($iconesDisponiveis as $icone => $nome)
                                        <option value="{{ $icone }}" {{ old('icone') == $icone ? 'selected' : '' }}>{{ $nome }}</option>
                                    @endforeach
                                </select>
                                <!--end::Select2-->
                                @error('icone')
                                    <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-md-6 fv-row">
                                <!--begin::Label-->
                                <label class="required form-label">Cor</label>
                                <!--end::Label-->
                                <!--begin::Select2-->
                                <select class="form-select mb-2" name="cor" data-control="select2" data-placeholder="Selecione uma cor" id="cor-select">
                                    <option></option>
                                    @foreach($coresDisponiveis as $cor => $nome)
                                        <option value="{{ $cor }}" {{ old('cor', 'primary') == $cor ? 'selected' : '' }}>{{ $nome }}</option>
                                    @endforeach
                                </select>
                                <!--end::Select2-->
                                @error('cor')
                                    <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <!--begin::Label-->
                            <label class="required form-label">Ordem</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="number" name="ordem" class="form-control mb-2" placeholder="Ordem de exibição" value="{{ old('ordem', $proximaOrdem) }}" min="0" />
                            <!--end::Input-->
                            <!--begin::Description-->
                            <div class="text-muted fs-7">Ordem de exibição na lista (menor número aparece primeiro).</div>
                            <!--end::Description-->
                            @error('ordem')
                                <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Input group-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::General options-->

                <!--begin::Advanced options-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Configurações Avançadas</h2>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <!--begin::Label-->
                            <label class="form-label">Configurações (JSON)</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <textarea name="configuracoes" class="form-control mb-2" rows="5" placeholder='{"exemplo": "valor"}'></textarea>
                            <!--end::Input-->
                            <!--begin::Description-->
                            <div class="text-muted fs-7">Configurações específicas em formato JSON (opcional).</div>
                            <!--end::Description-->
                            @error('configuracoes')
                                <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Input group-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Advanced options-->

                <div class="d-flex justify-content-end">
                    <!--begin::Button-->
                    <a href="{{ route('admin.tipo-proposicoes.index') }}" id="kt_tipo_cancel" class="btn btn-light me-5">Cancelar</a>
                    <!--end::Button-->
                    <!--begin::Button-->
                    <button type="submit" id="kt_tipo_submit" class="btn btn-primary">
                        <span class="indicator-label">Salvar</span>
                        <span class="indicator-progress">Aguarde...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                    <!--end::Button-->
                </div>
            </div>
            <!--end::Main column-->
        </form>
        <!--end::Form-->
    </div>
</div>
<!--end::Content-->

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Autocomplete functionality
    const autocompleteInput = document.getElementById('autocomplete-search');
    const autocompleteDropdown = document.getElementById('autocomplete-dropdown');
    let searchTimeout;
    
    // Mock API data - Replace with actual API endpoint
    const mockApiData = [
        {
            nome: "Projeto de Lei Ordinária",
            codigo: "projeto_lei_ordinaria",
            icone: "ki-document",
            cor: "primary",
            ordem: 1,
            configuracoes: {
                "numeracao_automatica": true,
                "tramitacao_obrigatoria": true,
                "campos_obrigatorios": ["ementa", "justificativa"],
                "prazos": {
                    "apresentacao": 30,
                    "emendas": 15
                }
            }
        },
        {
            nome: "Moção",
            codigo: "mocao",
            icone: "ki-message-text-2",
            cor: "warning",
            ordem: 5,
            configuracoes: {
                "numeracao_automatica": true,
                "tramitacao_simplificada": true,
                "campos_obrigatorios": ["ementa"],
                "tipos": ["apoio", "repudio", "pesar"]
            }
        },
        {
            nome: "Requerimento",
            codigo: "requerimento",
            icone: "ki-questionnaire-tablet",
            cor: "info",
            ordem: 3,
            configuracoes: {
                "numeracao_automatica": true,
                "resposta_obrigatoria": true,
                "prazo_resposta": 30,
                "campos_obrigatorios": ["ementa", "justificativa"]
            }
        },
        {
            nome: "Projeto de Decreto Legislativo",
            codigo: "projeto_decreto_legislativo",
            icone: "ki-document-edit",
            cor: "success",
            ordem: 2,
            configuracoes: {
                "numeracao_automatica": true,
                "tramitacao_especial": true,
                "quorum_qualificado": true,
                "campos_obrigatorios": ["ementa", "justificativa", "base_legal"]
            }
        }
    ];
    
    function searchApi(query) {
        // Simulate API call - Replace with actual fetch to your API
        return new Promise((resolve) => {
            setTimeout(() => {
                const results = mockApiData.filter(item => 
                    item.nome.toLowerCase().includes(query.toLowerCase()) ||
                    item.codigo.toLowerCase().includes(query.toLowerCase())
                );
                resolve(results);
            }, 300);
        });
    }
    
    function populateForm(data) {
        // Populate form fields with selected data
        document.querySelector('input[name="nome"]').value = data.nome;
        document.querySelector('input[name="codigo"]').value = data.codigo;
        document.querySelector('input[name="ordem"]').value = data.ordem;
        
        // Set select values
        const iconeSelect = document.querySelector('select[name="icone"]');
        const corSelect = document.querySelector('select[name="cor"]');
        
        if (iconeSelect) {
            $(iconeSelect).val(data.icone).trigger('change');
        }
        
        if (corSelect) {
            $(corSelect).val(data.cor).trigger('change');
        }
        
        // Populate JSON configuration
        if (data.configuracoes) {
            document.querySelector('textarea[name="configuracoes"]').value = JSON.stringify(data.configuracoes, null, 2);
        }
        
        // Update preview
        updatePreview();
        
        // Hide autocomplete
        autocompleteDropdown.style.display = 'none';
        autocompleteInput.value = '';
        
        // Show success message
        Swal.fire({
            icon: 'success',
            title: 'Dados Preenchidos!',
            text: `Formulário preenchido com dados de "${data.nome}"`,
            timer: 2000,
            showConfirmButton: false
        });
    }
    
    function renderResults(results) {
        if (results.length === 0) {
            autocompleteDropdown.innerHTML = '<div class="dropdown-item-text text-muted">Nenhum resultado encontrado</div>';
        } else {
            autocompleteDropdown.innerHTML = results.map(item => `
                <div class="dropdown-item cursor-pointer autocomplete-item" data-item='${JSON.stringify(item)}'>
                    <div class="d-flex align-items-center">
                        <i class="ki-duotone ${item.icone} fs-2 text-${item.cor} me-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div>
                            <div class="fw-bold">${item.nome}</div>
                            <div class="text-muted fs-7">${item.codigo}</div>
                        </div>
                    </div>
                </div>
            `).join('');
            
            // Add click handlers
            autocompleteDropdown.querySelectorAll('.autocomplete-item').forEach(item => {
                item.addEventListener('click', function() {
                    const data = JSON.parse(this.dataset.item);
                    populateForm(data);
                });
            });
        }
        
        autocompleteDropdown.style.display = 'block';
    }
    
    autocompleteInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            autocompleteDropdown.style.display = 'none';
            return;
        }
        
        searchTimeout = setTimeout(() => {
            searchApi(query).then(results => {
                renderResults(results);
            });
        }, 300);
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#autocomplete-search') && !e.target.closest('#autocomplete-dropdown')) {
            autocompleteDropdown.style.display = 'none';
        }
    });
    
    // Add CSS for cursor pointer
    const style = document.createElement('style');
    style.textContent = `
        .cursor-pointer { cursor: pointer; }
        .autocomplete-item:hover { background-color: #f8f9fa; }
        #autocomplete-dropdown {
            border: 1px solid #e4e6ef;
            border-radius: 0.475rem;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.075);
        }
    `;
    document.head.appendChild(style);
    // Auto-generate codigo from nome
    const nomeInput = document.querySelector('input[name="nome"]');
    const codigoInput = document.querySelector('input[name="codigo"]');
    
    nomeInput.addEventListener('input', function() {
        if (!codigoInput.value || codigoInput.dataset.autoGenerated) {
            const codigo = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\s]/g, '')
                .replace(/\s+/g, '_')
                .replace(/_+/g, '_')
                .replace(/^_|_$/g, '');
            
            codigoInput.value = codigo;
            codigoInput.dataset.autoGenerated = 'true';
        }
    });
    
    codigoInput.addEventListener('input', function() {
        this.dataset.autoGenerated = 'false';
    });

    // Update icon preview
    const iconeSelect = document.getElementById('icone-select');
    const corSelect = document.getElementById('cor-select');
    const previewIcon = document.getElementById('preview-icon');
    
    function updatePreview() {
        const selectedIcon = iconeSelect.value || 'ki-document';
        const selectedCor = corSelect.value || 'primary';
        
        previewIcon.className = `ki-duotone ${selectedIcon} fs-4x text-${selectedCor}`;
    }
    
    iconeSelect.addEventListener('change', updatePreview);
    corSelect.addEventListener('change', updatePreview);
    
    // Initial preview update
    updatePreview();

    // Form validation
    const form = document.getElementById('kt_tipo_form');
    const submitButton = document.getElementById('kt_tipo_submit');
    
    // Function to remove validation classes
    function removeValidationClasses(element) {
        element.classList.remove('is-invalid');
        element.classList.remove('is-valid');
        const feedback = element.parentElement.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.remove();
        }
    }
    
    // Function to add validation error
    function addValidationError(element, message) {
        element.classList.add('is-invalid');
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = message;
        element.parentElement.appendChild(feedback);
        
        // Add animation to draw attention
        element.classList.add('shake');
        setTimeout(() => element.classList.remove('shake'), 600);
    }
    
    // Function to validate and highlight fields
    function validateForm() {
        let isValid = true;
        const errors = [];
        
        // Validate nome
        const nomeInput = document.querySelector('input[name="nome"]');
        removeValidationClasses(nomeInput);
        if (!nomeInput.value.trim()) {
            addValidationError(nomeInput, 'Nome do tipo é obrigatório');
            errors.push({ field: 'nome', element: nomeInput });
            isValid = false;
        } else {
            nomeInput.classList.add('is-valid');
        }
        
        // Validate codigo
        const codigoInput = document.querySelector('input[name="codigo"]');
        removeValidationClasses(codigoInput);
        if (!codigoInput.value.trim()) {
            addValidationError(codigoInput, 'Código é obrigatório');
            errors.push({ field: 'codigo', element: codigoInput });
            isValid = false;
        } else if (!/^[a-z0-9_]+$/.test(codigoInput.value)) {
            addValidationError(codigoInput, 'Código deve conter apenas letras minúsculas, números e underscores');
            errors.push({ field: 'codigo', element: codigoInput });
            isValid = false;
        } else {
            codigoInput.classList.add('is-valid');
        }
        
        // Validate icone
        const iconeSelect = document.querySelector('select[name="icone"]');
        const iconeContainer = iconeSelect.closest('.form-select');
        removeValidationClasses(iconeContainer);
        if (!iconeSelect.value) {
            addValidationError(iconeContainer, 'Selecione um ícone');
            errors.push({ field: 'icone', element: iconeSelect });
            isValid = false;
        } else {
            iconeContainer.classList.add('is-valid');
        }
        
        // Validate cor
        const corSelect = document.querySelector('select[name="cor"]');
        const corContainer = corSelect.closest('.form-select');
        removeValidationClasses(corContainer);
        if (!corSelect.value) {
            addValidationError(corContainer, 'Selecione uma cor');
            errors.push({ field: 'cor', element: corSelect });
            isValid = false;
        } else {
            corContainer.classList.add('is-valid');
        }
        
        // Validate ordem
        const ordemInput = document.querySelector('input[name="ordem"]');
        removeValidationClasses(ordemInput);
        if (!ordemInput.value || parseInt(ordemInput.value) < 0) {
            addValidationError(ordemInput, 'Ordem deve ser um número maior ou igual a 0');
            errors.push({ field: 'ordem', element: ordemInput });
            isValid = false;
        } else {
            ordemInput.classList.add('is-valid');
        }
        
        // Validate JSON if provided
        const configuracoesTextarea = document.querySelector('textarea[name="configuracoes"]');
        removeValidationClasses(configuracoesTextarea);
        if (configuracoesTextarea.value.trim()) {
            try {
                JSON.parse(configuracoesTextarea.value);
                configuracoesTextarea.classList.add('is-valid');
            } catch (e) {
                addValidationError(configuracoesTextarea, 'JSON inválido: ' + e.message);
                errors.push({ field: 'configuracoes', element: configuracoesTextarea });
                isValid = false;
            }
        }
        
        // Scroll to first error if any
        if (errors.length > 0) {
            errors[0].element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            errors[0].element.focus();
        }
        
        return isValid;
    }
    
    // Add shake animation CSS
    const shakeStyle = document.createElement('style');
    shakeStyle.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        .shake {
            animation: shake 0.6s;
        }
        .form-control.is-invalid, .form-select.is-invalid {
            border-color: #f1416c !important;
            padding-right: calc(1.5em + 1.1rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23f1416c' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 2l12 12M14 2L2 14'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.275rem) center;
            background-size: calc(0.75em + 0.55rem) calc(0.75em + 0.55rem);
        }
        .form-control.is-valid, .form-select.is-valid {
            border-color: #50cd89 !important;
            padding-right: calc(1.5em + 1.1rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2350cd89' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M2 8.5L5.5 12l7-7'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.275rem) center;
            background-size: calc(0.75em + 0.55rem) calc(0.75em + 0.55rem);
        }
        .invalid-feedback {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #f1416c;
        }
    `;
    document.head.appendChild(shakeStyle);
    
    // Add real-time validation on input
    document.querySelectorAll('input[required], select[required]').forEach(element => {
        element.addEventListener('blur', function() {
            if (this.name === 'nome') {
                removeValidationClasses(this);
                if (!this.value.trim()) {
                    addValidationError(this, 'Nome do tipo é obrigatório');
                } else {
                    this.classList.add('is-valid');
                }
            } else if (this.name === 'codigo') {
                removeValidationClasses(this);
                if (!this.value.trim()) {
                    addValidationError(this, 'Código é obrigatório');
                } else if (!/^[a-z0-9_]+$/.test(this.value)) {
                    addValidationError(this, 'Código deve conter apenas letras minúsculas, números e underscores');
                } else {
                    this.classList.add('is-valid');
                }
            }
        });
    });
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;
        
        // Validate form
        if (!validateForm()) {
            // Hide loading state
            submitButton.removeAttribute('data-kt-indicator');
            submitButton.disabled = false;
            
            Swal.fire({
                text: "Por favor, corrija os erros destacados no formulário.",
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: "Ok",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
            return;
        }
        
        // Submit form
        form.submit();
    });
});
</script>
@endpush
@endsection