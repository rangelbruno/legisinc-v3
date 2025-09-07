@extends('components.layouts.app')

@section('title', 'Novo Módulo - Gerador')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Criar Novo Módulo
                </h1>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-danger">Erro de Validação!</h4>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.module-generator.store') }}" method="POST" id="module-form">
                @csrf
                
                <!--begin::Card-->
                <div class="card mb-5 mb-xl-8">
                    <!--begin::Card header-->
                    <div class="card-header border-0 cursor-pointer">
                        <div class="card-title m-0">
                            <h3 class="fw-bold m-0">Informações Básicas</h3>
                        </div>
                    </div>
                    <!--end::Card header-->
                    
                    <!--begin::Card body-->
                    <div class="card-body border-top p-9">
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">Nome do Módulo</label>
                            <div class="col-lg-8">
                                <input type="text" name="name" class="form-control form-control-lg form-control-solid" 
                                       placeholder="Ex: Contratos" value="{{ old('name') }}" required>
                                <div class="form-text">Nome que aparecerá no menu e títulos</div>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">Descrição</label>
                            <div class="col-lg-8">
                                <textarea name="description" class="form-control form-control-solid" rows="3" 
                                          placeholder="Descrição detalhada do módulo">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">Ícone</label>
                            <div class="col-lg-4">
                                <select name="icon" class="form-select form-select-solid">
                                    <option value="ki-element-11" {{ old('icon') === 'ki-element-11' ? 'selected' : '' }}>📋 Documento</option>
                                    <option value="ki-folder" {{ old('icon') === 'ki-folder' ? 'selected' : '' }}>📁 Pasta</option>
                                    <option value="ki-user" {{ old('icon') === 'ki-user' ? 'selected' : '' }}>👤 Usuário</option>
                                    <option value="ki-setting" {{ old('icon') === 'ki-setting' ? 'selected' : '' }}>⚙️ Configuração</option>
                                    <option value="ki-chart-pie" {{ old('icon') === 'ki-chart-pie' ? 'selected' : '' }}>📊 Relatório</option>
                                    <option value="ki-bank" {{ old('icon') === 'ki-bank' ? 'selected' : '' }}>🏛️ Institucional</option>
                                    <option value="ki-calendar" {{ old('icon') === 'ki-calendar' ? 'selected' : '' }}>📅 Agenda</option>
                                </select>
                            </div>
                            <label class="col-lg-2 col-form-label fw-semibold fs-6">Cor</label>
                            <div class="col-lg-2">
                                <select name="color" class="form-select form-select-solid">
                                    <option value="primary" {{ old('color') === 'primary' ? 'selected' : '' }}>Azul</option>
                                    <option value="success" {{ old('color') === 'success' ? 'selected' : '' }}>Verde</option>
                                    <option value="warning" {{ old('color') === 'warning' ? 'selected' : '' }}>Amarelo</option>
                                    <option value="danger" {{ old('color') === 'danger' ? 'selected' : '' }}>Vermelho</option>
                                    <option value="info" {{ old('color') === 'info' ? 'selected' : '' }}>Ciano</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">Funcionalidades</label>
                            <div class="col-lg-8">
                                <div class="form-check form-check-solid form-switch form-check-custom fv-row mb-3">
                                    <input class="form-check-input w-45px h-30px" type="checkbox" name="has_crud" id="has_crud" 
                                           value="1" {{ old('has_crud', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_crud">
                                        Gerar CRUD completo (Create, Read, Update, Delete)
                                    </label>
                                </div>
                                
                                <div class="form-check form-check-solid form-switch form-check-custom fv-row">
                                    <input class="form-check-input w-45px h-30px" type="checkbox" name="has_permissions" id="has_permissions" 
                                           value="1" {{ old('has_permissions', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_permissions">
                                        Integrar com sistema de permissões
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->

                <!--begin::Fields Card-->
                <div class="card mb-5 mb-xl-8">
                    <div class="card-header border-0 cursor-pointer">
                        <div class="card-title m-0">
                            <h3 class="fw-bold m-0">Campos da Tabela</h3>
                        </div>
                        <div class="card-toolbar">
                            <button type="button" class="btn btn-sm btn-primary" onclick="addField()">
                                <i class="ki-duotone ki-plus fs-3"></i>Adicionar Campo
                            </button>
                        </div>
                    </div>
                    
                    <div class="card-body border-top p-9">
                        <div id="fields-container">
                            <!-- Campos serão adicionados dinamicamente -->
                        </div>
                        
                        <div class="alert alert-info d-flex align-items-center p-5" id="fields-help">
                            <i class="ki-duotone ki-information fs-2hx text-info me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-info">Adicione pelo menos um campo</h4>
                                <span>Os campos ID, timestamps (created_at, updated_at) são criados automaticamente.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Fields Card-->

                <!--begin::Relationships Card-->
                <div class="card mb-5 mb-xl-8">
                    <div class="card-header border-0 cursor-pointer">
                        <div class="card-title m-0">
                            <h3 class="fw-bold m-0">Relacionamentos</h3>
                        </div>
                        <div class="card-toolbar">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="addRelationship()">
                                <i class="ki-duotone ki-plus fs-3"></i>Adicionar Relacionamento
                            </button>
                        </div>
                    </div>
                    
                    <div class="card-body border-top p-9">
                        <div id="relationships-container">
                            <!-- Relacionamentos serão adicionados dinamicamente -->
                        </div>
                        
                        <div class="alert alert-secondary d-flex align-items-center p-5">
                            <i class="ki-duotone ki-information fs-2hx text-secondary me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-secondary">Relacionamentos (Opcional)</h4>
                                <span>Configure relacionamentos com outras tabelas do sistema.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Relationships Card-->

                <!--begin::Business Logic Card-->
                <div class="card mb-5 mb-xl-8">
                    <div class="card-header border-0 cursor-pointer" data-bs-toggle="collapse" data-bs-target="#business-logic-card">
                        <div class="card-title m-0">
                            <h3 class="fw-bold m-0">Regras de Negócio (Avançado)</h3>
                        </div>
                        <span class="btn btn-sm btn-icon btn-active-light-primary ms-auto">
                            <i class="ki-duotone ki-down fs-3 rotate-180"></i>
                        </span>
                    </div>
                    
                    <div class="collapse" id="business-logic-card">
                        <div class="card-body border-top p-9">
                            <div class="mb-6">
                                <label class="form-label fw-semibold fs-6">Código PHP Personalizado</label>
                                <textarea name="business_logic" id="business-logic-editor" class="form-control form-control-solid" rows="10" 
                                          placeholder="// Adicione métodos personalizados ao Model
public function getStatusTextAttribute()
{
    return $this->status === 'active' ? 'Ativo' : 'Inativo';
}

public function scopeActive($query)
{
    return $query->where('status', 'active');
}">{{ old('business_logic') }}</textarea>
                                <div class="form-text">Métodos que serão adicionados ao Model. Use sintaxe PHP válida.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Business Logic Card-->

                <!--begin::Actions-->
                <div class="card">
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <a href="{{ route('admin.module-generator.index') }}" class="btn btn-light btn-active-light-primary me-2">
                            Cancelar
                        </a>
                        <button type="button" class="btn btn-light btn-active-light-primary me-2" onclick="previewModule()">
                            Pré-visualizar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Criar Módulo
                        </button>
                    </div>
                </div>
                <!--end::Actions-->
            </form>
        </div>
    </div>
</div>

<script>
let fieldIndex = 0;
let relationshipIndex = 0;

const fieldTypes = {
    'string': 'Texto (String)',
    'text': 'Texto Longo (Text)', 
    'integer': 'Número Inteiro',
    'decimal': 'Número Decimal',
    'boolean': 'Verdadeiro/Falso',
    'date': 'Data',
    'datetime': 'Data e Hora',
    'json': 'JSON'
};

const existingTables = @json($existingTables);

document.addEventListener('DOMContentLoaded', function() {
    addField(); // Adicionar primeiro campo automaticamente
});

function addField() {
    const container = document.getElementById('fields-container');
    const index = fieldIndex++;
    
    const fieldHtml = `
        <div class="field-row border border-gray-300 rounded p-5 mb-4" data-index="${index}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Nome do Campo</label>
                    <input type="text" name="fields[${index}][name]" class="form-control form-control-solid" 
                           placeholder="nome_campo" required>
                    <div class="form-text">Use snake_case</div>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Tipo</label>
                    <select name="fields[${index}][type]" class="form-select form-select-solid" 
                            onchange="toggleFieldOptions(${index}, this.value)" required>
                        ${Object.entries(fieldTypes).map(([value, label]) => 
                            `<option value="${value}">${label}</option>`
                        ).join('')}
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Label (Opcional)</label>
                    <input type="text" name="fields[${index}][label]" class="form-control form-control-solid" 
                           placeholder="Nome Exibido">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Opções</label>
                    <div class="d-flex flex-column">
                        <div class="form-check form-check-sm">
                            <input class="form-check-input" type="checkbox" name="fields[${index}][nullable]" 
                                   id="nullable_${index}" value="1">
                            <label class="form-check-label" for="nullable_${index}">Opcional</label>
                        </div>
                        <input type="text" name="fields[${index}][default]" class="form-control form-control-sm mt-2" 
                               placeholder="Valor padrão">
                    </div>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label fw-semibold">&nbsp;</label>
                    <button type="button" class="btn btn-sm btn-light-danger d-block" 
                            onclick="removeField(${index})">
                        <i class="ki-duotone ki-trash fs-5"></i>Remover
                    </button>
                </div>
            </div>
            
            <div id="field-options-${index}" class="row g-3 mt-3" style="display: none;">
                <!-- Options específicas por tipo serão inseridas aqui -->
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', fieldHtml);
    document.getElementById('fields-help').style.display = 'none';
}

function removeField(index) {
    const fieldRow = document.querySelector(`[data-index="${index}"]`);
    fieldRow.remove();
    
    // Se não há mais campos, mostrar help
    const remainingFields = document.querySelectorAll('.field-row');
    if (remainingFields.length === 0) {
        document.getElementById('fields-help').style.display = 'block';
        addField(); // Adicionar pelo menos um campo
    }
}

function toggleFieldOptions(index, type) {
    const optionsDiv = document.getElementById(`field-options-${index}`);
    optionsDiv.innerHTML = '';
    
    if (type === 'string') {
        optionsDiv.innerHTML = `
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tamanho Máximo</label>
                <input type="number" name="fields[${index}][length]" class="form-control form-control-solid" 
                       value="255" min="1" max="65535">
            </div>
        `;
        optionsDiv.style.display = 'flex';
    } else if (type === 'decimal') {
        optionsDiv.innerHTML = `
            <div class="col-md-3">
                <label class="form-label fw-semibold">Precisão Total</label>
                <input type="number" name="fields[${index}][precision]" class="form-control form-control-solid" 
                       value="8" min="1" max="65">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Casas Decimais</label>
                <input type="number" name="fields[${index}][scale]" class="form-control form-control-solid" 
                       value="2" min="0" max="30">
            </div>
        `;
        optionsDiv.style.display = 'flex';
    } else {
        optionsDiv.style.display = 'none';
    }
}

function addRelationship() {
    const container = document.getElementById('relationships-container');
    const index = relationshipIndex++;
    
    const relationshipHtml = `
        <div class="relationship-row border border-gray-300 rounded p-5 mb-4" data-index="${index}">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Tipo</label>
                    <select name="relationships[${index}][type]" class="form-select form-select-solid" required>
                        <option value="belongsTo">Pertence a (N:1)</option>
                        <option value="hasMany">Tem muitos (1:N)</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tabela Relacionada</label>
                    <select name="relationships[${index}][table]" class="form-select form-select-solid" required>
                        <option value="">Selecione...</option>
                        ${existingTables.map(table => 
                            `<option value="${table}">${table}</option>`
                        ).join('')}
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Nome do Método</label>
                    <input type="text" name="relationships[${index}][method_name]" class="form-control form-control-solid" 
                           placeholder="usuario, categoria, etc." required>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Chave Estrangeira</label>
                    <input type="text" name="relationships[${index}][foreign_key]" class="form-control form-control-solid" 
                           placeholder="user_id, categoria_id, etc." required>
                </div>
                
                <div class="col-md-1">
                    <label class="form-label fw-semibold">&nbsp;</label>
                    <button type="button" class="btn btn-sm btn-light-danger d-block" 
                            onclick="removeRelationship(${index})">
                        <i class="ki-duotone ki-trash fs-5"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', relationshipHtml);
}

function removeRelationship(index) {
    const relationshipRow = document.querySelector(`[data-index="${index}"].relationship-row`);
    relationshipRow.remove();
}

function previewModule() {
    // Implementar preview modal
    alert('Funcionalidade de preview será implementada');
}

// Validação do formulário antes do envio
document.getElementById('module-form').addEventListener('submit', function(e) {
    const fields = document.querySelectorAll('.field-row');
    if (fields.length === 0) {
        e.preventDefault();
        alert('Adicione pelo menos um campo à tabela');
        return false;
    }
    
    // Verificar nomes únicos de campos
    const fieldNames = [];
    let hasError = false;
    
    fields.forEach(field => {
        const nameInput = field.querySelector('input[name*="[name]"]');
        const name = nameInput.value.toLowerCase();
        
        if (fieldNames.includes(name)) {
            e.preventDefault();
            nameInput.focus();
            alert(`Campo "${name}" está duplicado. Use nomes únicos.`);
            hasError = true;
            return false;
        }
        
        fieldNames.push(name);
    });
    
    return !hasError;
});
</script>
@endsection