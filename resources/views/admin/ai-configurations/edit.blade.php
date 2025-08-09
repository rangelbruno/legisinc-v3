@extends('components.layouts.app')

@section('title', 'Editar Configuração de IA: ' . $aiConfiguration->name)

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
                    Editar Configuração de IA
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
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.ai-configurations.show', $aiConfiguration) }}" class="text-muted text-hover-primary">{{ $aiConfiguration->name }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Editar</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!--begin::View button-->
                <a href="{{ route('admin.ai-configurations.show', $aiConfiguration) }}" class="btn btn-sm btn-flex btn-info me-2">
                    <i class="ki-duotone ki-eye fs-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    Ver Detalhes
                </a>
                <!--end::View button-->
                <!--begin::Back button-->
                <a href="{{ route('admin.ai-configurations.index') }}" class="btn btn-sm btn-flex btn-secondary">
                    <i class="ki-duotone ki-arrow-left fs-3"></i>
                    Voltar
                </a>
                <!--end::Back button-->
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
                                    Editar: {{ $aiConfiguration->name }}
                                </h3>
                            </div>
                            <!--end::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar">
                                @if($aiConfiguration->is_active)
                                    @if($aiConfiguration->isHealthy())
                                        <span class="badge badge-light-success">
                                            <i class="ki-duotone ki-check-circle fs-7 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Funcionando
                                        </span>
                                    @else
                                        <span class="badge badge-light-warning">
                                            <i class="ki-duotone ki-warning fs-7 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Não testado
                                        </span>
                                    @endif
                                @else
                                    <span class="badge badge-light-secondary">
                                        <i class="ki-duotone ki-cross-circle fs-7 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Inativo
                                    </span>
                                @endif
                            </div>
                            <!--end::Card toolbar-->
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
                            <form action="{{ route('admin.ai-configurations.update', $aiConfiguration) }}" method="POST" id="ai-config-form" autocomplete="off">
                                @csrf
                                @method('PUT')

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2 required">Nome da Configuração</label>
                                    <input type="text" name="name" class="form-control form-control-solid @error('name') is-invalid @enderror" 
                                           placeholder="Ex: OpenAI Principal, Claude Backup" value="{{ old('name', $aiConfiguration->name) }}" required autocomplete="off">
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
                                            <option value="{{ $key }}" {{ old('provider', $aiConfiguration->provider) === $key ? 'selected' : '' }}>
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
                                <div class="fv-row mb-7" id="api-key-field">
                                    <label class="fs-6 fw-semibold form-label mb-2">API Key</label>
                                    <div class="input-group">
                                        <input type="password" name="api_key" class="form-control form-control-solid @error('api_key') is-invalid @enderror" 
                                               placeholder="Cole sua API key aqui ou deixe vazio para manter a atual" value="{{ old('api_key') }}" autocomplete="new-password">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleApiKeyVisibility()">
                                            <i class="ki-duotone ki-eye" id="api-key-icon"></i>
                                        </button>
                                    </div>
                                    @error('api_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text" id="api-key-help">
                                        @if($aiConfiguration->api_key)
                                            API Key atual está configurada. Deixe vazio para manter a atual ou insira uma nova para alterá-la.
                                        @else
                                            Nenhuma API Key configurada atualmente.
                                        @endif
                                    </div>
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
                                <div class="fv-row mb-7" id="base-url-field">
                                    <label class="fs-6 fw-semibold form-label mb-2">URL Base</label>
                                    <input type="url" name="base_url" class="form-control form-control-solid @error('base_url') is-invalid @enderror" 
                                           placeholder="https://api.example.com" value="{{ old('base_url', $aiConfiguration->base_url) }}" id="base-url-input" autocomplete="off">
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
                                           min="100" max="32000" value="{{ old('max_tokens', $aiConfiguration->max_tokens) }}" required>
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
                                               min="0" max="2" step="0.01" value="{{ old('temperature', $aiConfiguration->temperature) }}" 
                                               oninput="updateTemperatureValue(this.value)" style="flex: 1;">
                                        <span class="fw-bold fs-6 text-gray-800" id="temperature-value">{{ old('temperature', $aiConfiguration->temperature) }}</span>
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
                                                   min="1" max="100" value="{{ old('priority', $aiConfiguration->priority) }}" required>
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
                                                   min="100" placeholder="Deixe vazio para sem limite" value="{{ old('daily_token_limit', $aiConfiguration->daily_token_limit) }}">
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
                                           min="0" step="0.000001" value="{{ old('cost_per_1k_tokens', $aiConfiguration->cost_per_1k_tokens) }}" id="cost-input">
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
                                              rows="4" placeholder="Deixe vazio para usar o prompt padrão">{{ old('custom_prompt', $aiConfiguration->custom_prompt) }}</textarea>
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
                                               id="is_active" {{ old('is_active', $aiConfiguration->is_active) ? 'checked' : '' }}>
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
                                        Salvar Alterações
                                    </button>
                                    <a href="{{ route('admin.ai-configurations.show', $aiConfiguration) }}" class="btn btn-light">
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
                    <!--begin::Current Settings Card-->
                    <div class="card card-flush mb-6">
                        <!--begin::Card header-->
                        <div class="card-header pt-8">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <h3 class="fw-bold text-gray-900">
                                    <i class="ki-duotone ki-setting-4 fs-2 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    Configuração Atual
                                </h3>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body">
                            <div class="d-flex align-items-center border-0 mb-5">
                                <div class="symbol symbol-40px me-4">
                                    <span class="symbol-label bg-light-primary">
                                        @switch($aiConfiguration->provider)
                                            @case('openai')
                                                <i class="ki-duotone ki-abstract-26 fs-2 text-primary"></i>
                                                @break
                                            @case('anthropic')
                                                <i class="ki-duotone ki-crown-2 fs-2 text-primary"></i>
                                                @break
                                            @case('google')
                                                <i class="ki-duotone ki-google fs-2 text-primary"></i>
                                                @break
                                            @case('local')
                                                <i class="ki-duotone ki-home fs-2 text-primary"></i>
                                                @break
                                            @default
                                                <i class="ki-duotone ki-technology-4 fs-2 text-primary"></i>
                                        @endswitch
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fs-6 fw-bold text-gray-900">
                                        @switch($aiConfiguration->provider)
                                            @case('openai')
                                                OpenAI
                                                @break
                                            @case('anthropic')
                                                Anthropic (Claude)
                                                @break
                                            @case('google')
                                                Google (Gemini)
                                                @break
                                            @case('local')
                                                Local (Ollama)
                                                @break
                                            @default
                                                {{ ucfirst($aiConfiguration->provider) }}
                                        @endswitch
                                    </div>
                                    <div class="fs-7 fw-semibold text-gray-600">Provedor</div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center border-0 mb-5">
                                <div class="symbol symbol-40px me-4">
                                    <span class="symbol-label bg-light-info">
                                        <i class="ki-duotone ki-setting-2 fs-2 text-info">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fs-6 fw-bold text-gray-900">{{ $aiConfiguration->model }}</div>
                                    <div class="fs-7 fw-semibold text-gray-600">Modelo</div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center border-0 mb-5">
                                <div class="symbol symbol-40px me-4">
                                    <span class="symbol-label bg-light-success">
                                        <span class="symbol-label bg-{{ $aiConfiguration->is_active ? 'success' : 'secondary' }} text-white fw-bold fs-6">
                                            {{ $aiConfiguration->priority }}
                                        </span>
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fs-6 fw-bold text-gray-900">#{{ $aiConfiguration->priority }}</div>
                                    <div class="fs-7 fw-semibold text-gray-600">Prioridade</div>
                                </div>
                            </div>

                            <div class="separator separator-dashed my-5"></div>

                            <div class="text-muted fs-7">
                                <strong>Última modificação:</strong><br>
                                {{ $aiConfiguration->updated_at->format('d/m/Y H:i') }}
                            </div>

                            @if($aiConfiguration->last_tested_at)
                            <div class="text-muted fs-7 mt-3">
                                <strong>Último teste:</strong><br>
                                {{ $aiConfiguration->last_tested_at->format('d/m/Y H:i') }}
                                @if($aiConfiguration->last_test_success)
                                    <span class="text-success">✓</span>
                                @else
                                    <span class="text-danger">✗</span>
                                @endif
                            </div>
                            @endif
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Current Settings Card-->

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
                                    Dicas de Edição
                                </h3>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body">
                            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mb-6">
                                <i class="ki-duotone ki-warning fs-2tx text-warning me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">API Key</h4>
                                        <div class="fs-6 text-gray-700">
                                            Deixe o campo da API Key vazio para manter a chave atual. 
                                            Preencha apenas se quiser alterá-la.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-6">
                                <i class="ki-duotone ki-information-5 fs-2tx text-info me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">Teste Recomendado</h4>
                                        <div class="fs-6 text-gray-700">
                                            Sempre teste a configuração após fazer alterações para garantir que está funcionando corretamente.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-4">
                                <i class="ki-duotone ki-design-1 fs-2tx text-primary me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">Sistema de Prioridades</h4>
                                        <div class="fs-6 text-gray-700">
                                            Alterar a prioridade afeta a ordem em que o sistema tentará usar as configurações de IA.
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
        return;
    }

    const providerData = providers[provider];
    const currentModel = '{{ old("model", $aiConfiguration->model) }}';

    // Mostrar/ocultar API key
    if (providerData.requires_api_key) {
        apiKeyField.style.display = 'block';
        document.querySelector('input[name="api_key"]').required = false; // Na edição não é obrigatório
    } else {
        apiKeyField.style.display = 'none';
        document.querySelector('input[name="api_key"]').required = false;
    }

    // Mostrar URL base
    baseUrlField.style.display = 'block';
    if (!baseUrlInput.value) {
        baseUrlInput.placeholder = providerData.default_base_url;
    }

    // Definir custo padrão apenas se não houver valor atual
    if (!costInput.value) {
        costInput.value = providerData.cost_per_1k_tokens;
    }

    // Adicionar modelos
    providerData.models.forEach(model => {
        const option = document.createElement('option');
        option.value = model;
        option.textContent = model;
        if (model === currentModel) {
            option.selected = true;
        }
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
        api_key: formData.get('api_key') || null, // null se vazio para manter a atual
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

    // Para edição, se API key estiver vazia, usamos a atual para teste
    if (['openai', 'anthropic', 'google'].includes(testData.provider) && !testData.api_key) {
        // Usar configuração atual para teste
        testData.use_current_config = true;
        testData.config_id = {{ $aiConfiguration->id }};
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

    // Use endpoint específico para teste durante edição
    const testUrl = testData.use_current_config ? 
        `/admin/ai-configurations/{{ $aiConfiguration->id }}/test` :
        '/admin/ai-configurations/test-data';
    
    const method = testData.use_current_config ? 'POST' : 'POST';
    const body = testData.use_current_config ? {} : testData;

    fetch(testUrl, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: testData.use_current_config ? '' : JSON.stringify(body)
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
            toastr.success('✅ Teste bem-sucedido! Salvando alterações...');
            
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

// Inicializar campos baseado no valor selecionado
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