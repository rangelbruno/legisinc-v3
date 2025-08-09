@extends('components.layouts.app')

@section('title', 'Nova Configuração de IA')

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <i class="ki-duotone ki-technology-4 fs-2 me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    Nova Configuração de IA
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
                        <a href="{{ route('admin.ai-configurations.index') }}" class="text-muted text-hover-primary">Configurações de IA</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Nova Configuração</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!--begin::Secondary button-->
                <a href="{{ route('admin.ai-configurations.index') }}" class="btn btn-sm btn-flex btn-secondary">
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
            
            <!--begin::Row-->
            <div class="row g-6 g-xl-9">
                <!--begin::Col-->
                <div class="col-lg-8 col-xl-8">
                    <!--begin::Card-->
                    <div class="card card-flush">
                        <!--begin::Card header-->
                        <div class="card-header pt-8">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <h3 class="fw-bold text-gray-900">
                                    <i class="ki-duotone ki-technology-4 fs-2 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    Nova Configuração de IA
                                </h3>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->

                        <div class="card-body">
                            @if($errors->has('general'))
                                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                                    <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <h5 class="mb-1">Erro</h5>
                                        <span>{!! nl2br(e($errors->first('general'))) !!}</span>
                                    </div>
                                </div>
                            @endif

                            @if($errors->has('test'))
                                <div class="alert alert-warning d-flex align-items-center p-5 mb-10">
                                    <i class="ki-duotone ki-warning fs-2hx text-warning me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <h5 class="mb-1">Aviso de Teste</h5>
                                        <span>{{ $errors->first('test') }}</span>
                                    </div>
                                </div>
                            @endif

                            <!--begin::Form-->
                            <form action="{{ route('admin.ai-configurations.store') }}" method="POST" id="ai-config-form" autocomplete="off">
                                @csrf

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2 required">Nome da Configuração</label>
                                    <input type="text" name="name" class="form-control form-control-solid @error('name') is-invalid @enderror" 
                                           placeholder="Ex: OpenAI Principal, Claude Backup" value="{{ old('name') }}" required autocomplete="off">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Um nome amigável para identificar esta configuração</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2 required">Provedor de IA</label>
                                    <select name="provider" class="form-select form-select-solid @error('provider') is-invalid @enderror" 
                                            id="provider-select" onchange="updateProviderFields()" required>
                                        <option value="">Selecione um provedor</option>
                                        @foreach($providers as $key => $provider)
                                            <option value="{{ $key }}" {{ old('provider') === $key ? 'selected' : '' }}>
                                                {{ $provider['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('provider')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7" id="api-key-field" style="display: none;">
                                    <label class="fs-6 fw-semibold form-label mb-2">API Key</label>
                                    <div class="input-group">
                                        <input type="password" name="api_key" class="form-control form-control-solid @error('api_key') is-invalid @enderror" 
                                               placeholder="Cole sua API key aqui" value="{{ old('api_key') }}" autocomplete="new-password">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleApiKeyVisibility()">
                                            <i class="ki-duotone ki-eye" id="api-key-icon"></i>
                                        </button>
                                    </div>
                                    @error('api_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text" id="api-key-help"></div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2 required">Modelo</label>
                                    <select name="model" class="form-select form-select-solid @error('model') is-invalid @enderror" 
                                            id="model-select" required>
                                        <option value="">Primeiro selecione um provedor</option>
                                    </select>
                                    @error('model')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7" id="base-url-field" style="display: none;">
                                    <label class="fs-6 fw-semibold form-label mb-2">URL Base</label>
                                    <input type="url" name="base_url" class="form-control form-control-solid @error('base_url') is-invalid @enderror" 
                                           placeholder="https://api.example.com" value="{{ old('base_url') }}" id="base-url-input" autocomplete="off">
                                    @error('base_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">URL customizada (deixe vazio para usar padrão)</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2 required">Máximo de Tokens</label>
                                    <input type="number" name="max_tokens" class="form-control form-control-solid @error('max_tokens') is-invalid @enderror" 
                                           min="100" max="32000" value="{{ old('max_tokens', 2000) }}" required>
                                    @error('max_tokens')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Entre 100 e 32.000 tokens por requisição</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2 required">Temperatura</label>
                                    <div class="d-flex align-items-center">
                                        <input type="range" name="temperature" class="form-range me-3 @error('temperature') is-invalid @enderror" 
                                               min="0" max="2" step="0.01" value="{{ old('temperature', 0.7) }}" 
                                               oninput="updateTemperatureValue(this.value)" style="flex: 1;">
                                        <span class="fw-bold fs-6 text-gray-800" id="temperature-value">0.7</span>
                                    </div>
                                    @error('temperature')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">0 = Mais preciso, 2 = Mais criativo</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="fv-row mb-7">
                                            <label class="fs-6 fw-semibold form-label mb-2 required">Prioridade</label>
                                            <input type="number" name="priority" class="form-control form-control-solid @error('priority') is-invalid @enderror" 
                                                   min="1" max="100" value="{{ old('priority', 1) }}" required>
                                            @error('priority')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">1 = Maior prioridade</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="fv-row mb-7">
                                            <label class="fs-6 fw-semibold form-label mb-2">Limite Diário de Tokens</label>
                                            <input type="number" name="daily_token_limit" class="form-control form-control-solid @error('daily_token_limit') is-invalid @enderror" 
                                                   min="100" placeholder="Deixe vazio para sem limite" value="{{ old('daily_token_limit') }}">
                                            @error('daily_token_limit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Para controle de custos</div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Input group-->


                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2">Custo por 1000 Tokens (USD)</label>
                                    <input type="number" name="cost_per_1k_tokens" class="form-control form-control-solid @error('cost_per_1k_tokens') is-invalid @enderror" 
                                           min="0" step="0.000001" value="{{ old('cost_per_1k_tokens') }}" id="cost-input">
                                    @error('cost_per_1k_tokens')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Para estimativa de custos (opcional)</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2">Prompt Personalizado</label>
                                    <textarea name="custom_prompt" class="form-control form-control-solid @error('custom_prompt') is-invalid @enderror" 
                                              rows="4" placeholder="Deixe vazio para usar o prompt padrão">{{ old('custom_prompt') }}</textarea>
                                    @error('custom_prompt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Prompt específico para este provedor (opcional)</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                               id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label fs-6 fw-semibold" for="is_active">
                                            Configuração ativa
                                        </label>
                                    </div>
                                    <div class="form-text">Configurações inativas não serão usadas</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Actions-->
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-warning me-3" onclick="testBeforeSave()">
                                        <i class="ki-duotone ki-rocket fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Testar e Salvar
                                    </button>
                                    <button type="submit" class="btn btn-primary me-3">
                                        <i class="ki-duotone ki-check fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Salvar
                                    </button>
                                    <a href="{{ route('admin.ai-configurations.index') }}" class="btn btn-light">
                                        <i class="ki-duotone ki-cross fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Cancelar
                                    </a>
                                </div>
                                <!--end::Actions-->
                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-lg-4 col-xl-4">
                    <!--begin::Help Card-->
                    <div class="card card-flush">
                        <!--begin::Card header-->
                        <div class="card-header pt-8">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <h3 class="fw-bold text-gray-900">
                                    <i class="ki-duotone ki-information-5 fs-2 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    Como Configurar
                                </h3>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body">
                            <div class="mb-10">
                                <h4 class="fw-bold text-gray-900 mb-7">Como obter API Keys</h4>
                                
                                <div class="mb-7">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="symbol symbol-30px me-3">
                                            <span class="symbol-label bg-light-primary">
                                                <i class="ki-duotone ki-abstract-26 fs-4 text-primary"></i>
                                            </span>
                                        </div>
                                        <h5 class="mb-0">OpenAI</h5>
                                    </div>
                                    <div class="text-gray-600 fs-7 mb-3">
                                        1. Acesse <a href="https://platform.openai.com/api-keys" target="_blank" class="link-primary">platform.openai.com</a><br>
                                        2. Faça login ou crie uma conta<br>
                                        3. Vá em "API keys" e clique "Create new secret key"
                                    </div>
                                </div>
                                
                                <div class="mb-7">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="symbol symbol-30px me-3">
                                            <span class="symbol-label bg-light-success">
                                                <i class="ki-duotone ki-google fs-4 text-success"></i>
                                            </span>
                                        </div>
                                        <h5 class="mb-0">Google (Gemini)</h5>
                                    </div>
                                    <div class="text-gray-600 fs-7 mb-3">
                                        1. Acesse <a href="https://aistudio.google.com/app/apikey" target="_blank" class="link-primary">Google AI Studio</a><br>
                                        2. Faça login com conta Google<br>
                                        3. Clique "Create API key"
                                    </div>
                                </div>
                                
                                <div class="mb-7">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="symbol symbol-30px me-3">
                                            <span class="symbol-label bg-light-warning">
                                                <i class="ki-duotone ki-crown-2 fs-4 text-warning"></i>
                                            </span>
                                        </div>
                                        <h5 class="mb-0">Anthropic (Claude)</h5>
                                    </div>
                                    <div class="text-gray-600 fs-7 mb-3">
                                        1. Acesse <a href="https://console.anthropic.com/" target="_blank" class="link-primary">console.anthropic.com</a><br>
                                        2. Crie uma conta e acesse API keys<br>
                                        3. Gere uma nova chave de API
                                    </div>
                                </div>
                            </div>

                            <div class="mb-10">
                                <h4 class="fw-bold text-gray-900 mb-7">Sistema de Prioridades</h4>
                                <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6">
                                    <i class="ki-duotone ki-information-5 fs-2tx text-info me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <div class="fw-semibold">
                                            <div class="fs-6 text-gray-700">
                                                O sistema tentará as APIs na ordem de prioridade (1 = primeiro). 
                                                Se uma falhar ou atingir o limite, tentará a próxima automaticamente.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                                <i class="ki-duotone ki-design-1 fs-2tx text-primary me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">Dica Importante</h4>
                                        <div class="fs-6 text-gray-700">
                                            Configure múltiplas APIs para garantir disponibilidade. 
                                            Use a mais confiável com prioridade 1 e outras como backup.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Help Card-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
@endsection

@push('scripts')
<script>
const providers = @json($providers);

function updateProviderFields() {
    const provider = document.getElementById('provider-select').value;
    const modelSelect = document.getElementById('model-select');
    const apiKeyField = document.getElementById('api-key-field');
    const baseUrlField = document.getElementById('base-url-field');
    const apiKeyHelp = document.getElementById('api-key-help');
    const baseUrlInput = document.getElementById('base-url-input');
    const costInput = document.getElementById('cost-input');

    // Limpar modelos
    modelSelect.innerHTML = '<option value="">Selecione um modelo</option>';

    if (!provider || !providers[provider]) {
        apiKeyField.style.display = 'none';
        baseUrlField.style.display = 'none';
        return;
    }

    const providerData = providers[provider];

    // Mostrar/ocultar API key
    if (providerData.requires_api_key) {
        apiKeyField.style.display = 'block';
        document.querySelector('input[name="api_key"]').required = true;
        apiKeyHelp.textContent = 'API key obrigatória para este provedor';
    } else {
        apiKeyField.style.display = 'none';
        document.querySelector('input[name="api_key"]').required = false;
        apiKeyHelp.textContent = '';
    }

    // Mostrar URL base
    baseUrlField.style.display = 'block';
    baseUrlInput.placeholder = providerData.default_base_url;

    // Definir custo padrão
    costInput.value = providerData.cost_per_1k_tokens;

    // Adicionar modelos
    providerData.models.forEach(model => {
        const option = document.createElement('option');
        option.value = model;
        option.textContent = model;
        modelSelect.appendChild(option);
    });
}

function toggleApiKeyVisibility() {
    const input = document.querySelector('input[name="api_key"]');
    const icon = document.getElementById('api-key-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'ki-duotone ki-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'ki-duotone ki-eye';
    }
}

function updateTemperatureValue(value) {
    document.getElementById('temperature-value').textContent = parseFloat(value).toFixed(2);
}

function testBeforeSave() {
    const form = document.getElementById('ai-config-form');
    const formData = new FormData(form);
    
    const testData = {
        name: formData.get('name'),
        provider: formData.get('provider'),
        api_key: formData.get('api_key'),
        model: formData.get('model'),
        base_url: formData.get('base_url'),
        max_tokens: parseInt(formData.get('max_tokens')),
        temperature: parseFloat(formData.get('temperature'))
    };

    // Validações básicas
    if (!testData.name || !testData.provider || !testData.model) {
        toastr.error('Preencha todos os campos obrigatórios antes de testar');
        return;
    }

    if (['openai', 'anthropic', 'google'].includes(testData.provider) && !testData.api_key) {
        toastr.error('API Key é obrigatória para o provedor ' + testData.provider);
        return;
    }

    const btn = event.target;
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Testando...';
    btn.disabled = true;

    // Criar elemento de status de teste
    const statusDiv = document.createElement('div');
    statusDiv.id = 'test-status';
    statusDiv.className = 'alert alert-info mt-3';
    statusDiv.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Testando configuração...';
    
    // Inserir após o botão
    btn.parentNode.insertBefore(statusDiv, btn.nextSibling);

    fetch('/admin/ai-configurations/test-data', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(testData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        statusDiv.remove();
        
        if (data.success) {
            toastr.success('✅ Teste bem-sucedido! Salvando configuração...');
            
            // Mostrar sucesso visual
            const successDiv = document.createElement('div');
            successDiv.className = 'alert alert-success mt-3';
            successDiv.innerHTML = '<i class="ki-duotone ki-check-circle fs-2 text-success me-2"></i>Configuração testada com sucesso! Salvando...';
            btn.parentNode.insertBefore(successDiv, btn.nextSibling);
            
            setTimeout(() => {
                // Adicionar campo hidden para indicar teste
                const testField = document.createElement('input');
                testField.type = 'hidden';
                testField.name = 'test_connection';
                testField.value = '1';
                form.appendChild(testField);
                
                // Submeter formulário
                form.submit();
            }, 1500);
        } else {
            toastr.error('❌ Teste falhou: ' + data.message);
            
            // Mostrar erro detalhado
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger mt-3';
            errorDiv.innerHTML = `
                <h5><i class="ki-duotone ki-cross-circle fs-2 text-danger me-2"></i>Teste Falhou</h5>
                <p class="mb-2"><strong>Erro:</strong> ${data.message}</p>
                ${data.details ? `<details class="mt-2"><summary>Detalhes técnicos</summary><pre class="mt-2">${data.details}</pre></details>` : ''}
                <button type="button" class="btn btn-sm btn-light mt-2" onclick="this.parentElement.remove()">Fechar</button>
            `;
            btn.parentNode.insertBefore(errorDiv, btn.nextSibling);
        }
    })
    .catch(error => {
        statusDiv.remove();
        
        const errorMessage = `Erro de conexão: ${error.message}`;
        toastr.error(errorMessage);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger mt-3';
        errorDiv.innerHTML = `
            <h5><i class="ki-duotone ki-cross-circle fs-2 text-danger me-2"></i>Erro de Conexão</h5>
            <p class="mb-2">${errorMessage}</p>
            <p class="mb-2 text-muted">Verifique sua conexão com a internet e tente novamente.</p>
            <button type="button" class="btn btn-sm btn-light mt-2" onclick="this.parentElement.remove()">Fechar</button>
        `;
        btn.parentNode.insertBefore(errorDiv, btn.nextSibling);
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// Inicializar campos baseado no valor selecionado (para caso de validação com erro)
document.addEventListener('DOMContentLoaded', function() {
    const provider = document.getElementById('provider-select').value;
    if (provider) {
        updateProviderFields();
    }
    
    // Atualizar valor da temperatura
    const temperatureInput = document.querySelector('input[name="temperature"]');
    updateTemperatureValue(temperatureInput.value);
});
</script>
@endpush