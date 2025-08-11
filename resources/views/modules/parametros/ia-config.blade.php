@extends('components.layouts.app')

@section('title', 'Configurações de IA')

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <i class="ki-duotone ki-brain fs-2 me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Configurações de Inteligência Artificial
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
                    <li class="breadcrumb-item text-muted">IA</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!--begin::Primary button-->
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_provider">
                    <i class="ki-duotone ki-plus fs-3"></i>
                    Adicionar Nova IA
                </button>
                <!--end::Primary button-->
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
            <!--end::Alert-->

            <!--begin::Header Card-->
            <div class="card mb-10">
                <div class="card-body p-9">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-60px me-5">
                            <span class="symbol-label bg-light-info">
                                <i class="ki-duotone ki-brain fs-2x text-info">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="text-gray-900 fs-2 fw-bold mb-1">Configurações de Inteligência Artificial</h3>
                            <p class="text-gray-700 fs-6 mb-0">Gerencie provedores de IA, monitore uso de tokens e configure APIs</p>
                        </div>
                        <div class="text-end">
                            <span class="badge badge-light-success fs-7 fw-bold">
                                <i class="ki-duotone ki-check fs-6 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                {{ $activeProvider ? $activeProvider->label : 'Nenhum' }} Ativo
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Header Card-->

            <!--begin::Statistics Row-->
            <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                <!--begin::Col - Stats Cards-->
                <div class="col-xxl-6">
                    <!--begin::Row-->
                    <div class="row g-5 g-xl-10">
                        <!--begin::Col-->
                        <div class="col-md-6 col-xxl-6">
                            <!--begin::Stats Widget-->
                            <div class="card card-flush">
                                <div class="card-header pt-5">
                                    <div class="card-title d-flex flex-column">
                                        <div class="d-flex align-items-center">
                                            <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start">Total</span>
                                        </div>
                                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ number_format($totalTokens ?? 0) }}</span>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-end pe-0">
                                    <span class="fs-6 fw-bolder text-gray-800 d-block mb-2">Tokens Utilizados</span>
                                    <div class="d-flex align-items-center flex-wrap">
                                        <span class="badge badge-light-warning fs-8 fw-bold me-2">Últimos 30 dias</span>
                                        @if(isset($tokenStats['source']))
                                            <span class="badge badge-light-info fs-8 fw-bold">
                                                @if($tokenStats['source'] === 'openai_api')
                                                    <i class="ki-duotone ki-check fs-7 me-1"></i>API Real
                                                @elseif($tokenStats['source'] === 'database')
                                                    <i class="ki-duotone ki-data fs-7 me-1"></i>Banco Local
                                                @else
                                                    <i class="ki-duotone ki-abstract-42 fs-7 me-1"></i>Simulado
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!--end::Stats Widget-->
                        </div>
                        <!--end::Col-->
                        
                        <!--begin::Col-->
                        <div class="col-md-6 col-xxl-6">
                            <!--begin::Stats Widget-->
                            <div class="card card-flush">
                                <div class="card-header pt-5">
                                    <div class="card-title d-flex flex-column">
                                        <div class="d-flex align-items-center">
                                            <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start">$</span>
                                        </div>
                                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ number_format($totalCost ?? 0, 3) }}</span>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-end pe-0">
                                    <span class="fs-6 fw-bolder text-gray-800 d-block mb-2">Custo Estimado (USD)</span>
                                    <div class="d-flex align-items-center flex-wrap">
                                        <span class="badge badge-light-success fs-8 fw-bold me-2">Base API oficial</span>
                                    </div>
                                </div>
                            </div>
                            <!--end::Stats Widget-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Col-->

                <!--begin::Col - Chart-->
                <div class="col-xxl-6">
                    <!--begin::Chart Widget-->
                    <div class="card card-flush h-xl-100">
                        <!--begin::Header-->
                        <div class="card-header pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-900">Uso de Tokens</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-7">Últimos 30 dias</span>
                            </h3>
                            <div class="card-toolbar">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-light-info fs-8 fw-bold">
                                        <i class="ki-duotone ki-check fs-7 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        {{ $activeProvider ? $activeProvider->label : 'Nenhum provedor ativo' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-6">
                            <!--begin::Chart-->
                            <canvas id="kt_charts_widget_2_chart" style="height: 300px"></canvas>
                            <!--end::Chart-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Chart Widget-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

            <!--begin::Row - AI Providers Cards-->
            <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                @forelse($providers as $provider)
                <!--begin::Col-->
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <!--begin::Card-->
                    <div class="card card-flush h-xl-100 @if($provider->is_active && isset($provider->api_status) && $provider->api_status === 'error') border-danger border-2 @elseif($provider->is_active) border-success border-2 @endif">
                        <!--begin::Card header-->
                        <div class="card-header pt-5">
                            <!--begin::Card title-->
                            <div class="card-title d-flex flex-column">
                                <!--begin::Icon-->
                                <div class="d-flex align-items-center mb-3">
                                    @php
                                        $colorClass = match($provider->name) {
                                            'openai' => 'text-openai',
                                            'anthropic' => 'text-anthropic', 
                                            'google' => 'text-google',
                                            'ollama' => 'text-ollama',
                                            default => 'text-primary'
                                        };
                                        if ($provider->is_active) $colorClass = 'text-success';
                                    @endphp
                                    <i class="ki-duotone {{ $provider->icon }} fs-2x {{ $colorClass }} me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <h3 class="fs-5 fw-bold text-gray-900 mb-0">{{ $provider->label }}</h3>
                                        <span class="text-gray-500 fs-7">{{ count($provider->supported_models ?? []) }} modelos disponíveis</span>
                                    </div>
                                </div>
                                <!--end::Icon-->
                            </div>
                            <!--end::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar d-flex gap-2">
                                <!--begin::Action buttons-->
                                <div class="d-flex gap-1">
                                    <!--begin::Configure button-->
                                    <button class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_config_{{ $provider->id }}" title="Configurar">
                                        <i class="ki-duotone ki-setting-3 fs-6">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </button>
                                    <!--end::Configure button-->
                                    
                                    <!--begin::Test button-->
                                    <button class="btn btn-sm btn-light-warning btn-test-provider" data-provider-id="{{ $provider->id }}" title="Testar conexão">
                                        <i class="ki-duotone ki-flask fs-6">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </button>
                                    <!--end::Test button-->
                                </div>
                                <!--end::Action buttons-->
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body d-flex flex-column">
                            <!--begin::Description-->
                            <div class="flex-grow-1 mb-5">
                                <p class="text-gray-700 fs-6 mb-2">{{ $provider->description }}</p>
                                <div class="d-flex align-items-center">
                                    <span class="text-gray-500 fs-7 me-2">Modelo padrão:</span>
                                    <span class="badge badge-light-info fs-8">{{ $provider->default_model }}</span>
                                </div>
                            </div>
                            <!--end::Description-->
                            <!--begin::Status Section-->
                            <div class="mb-4">
                                <div class="d-flex align-items-center justify-content-center">
                                    @if($provider->is_active)
                                        @if(isset($provider->api_status) && $provider->api_status === 'error')
                                            <span class="badge badge-light-danger cursor-pointer btn-show-error" data-error-message="{{ $provider->api_error_message ?? 'Erro de comunicação com a API' }}">
                                                <i class="ki-duotone ki-cross-circle fs-6 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Erro - Clique para detalhes
                                            </span>
                                        @else
                                            <span class="badge badge-light-success">
                                                <i class="ki-duotone ki-check fs-6 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Configurado e Ativo
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge badge-light-secondary">
                                            <i class="ki-duotone ki-information fs-6 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            Disponível
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <!--end::Status Section-->
                            
                            <!--begin::Action Section-->
                            <div class="d-flex justify-content-center">
                                @if($provider->is_active)
                                    <button class="btn btn-sm btn-light-success" disabled>
                                        <i class="ki-duotone ki-check fs-6 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Em uso
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-primary btn-activate-provider" data-provider-id="{{ $provider->id }}">
                                        <i class="ki-duotone ki-rocket fs-6 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Ativar
                                    </button>
                                @endif
                            </div>
                            <!--end::Action Section-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Col-->

                <!--begin::Modal - Configure Provider-->
                <div class="modal fade" id="kt_modal_config_{{ $provider->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered mw-650px">
                        <div class="modal-content">
                            <div class="modal-header" id="kt_modal_config_header">
                                <h2 class="fw-bold">
                                    <i class="ki-duotone {{ $provider->icon }} fs-2 text-primary me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Configurar {{ $provider->label }}
                                </h2>
                                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                                    <i class="ki-duotone ki-cross fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>
                            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                <!--begin::Notice-->
                                <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6 mb-9">
                                    <i class="ki-duotone ki-information-5 fs-2tx text-info me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <div class="fw-semibold">
                                            <h4 class="text-gray-900 fw-bold">{{ $provider->description }}</h4>
                                            <div class="fs-6 text-gray-700">Configure as credenciais e parâmetros para integração com {{ $provider->label }}. Suas informações serão criptografadas.</div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Notice-->
                                
                                <form id="kt_modal_config_form_{{ $provider->id }}" class="provider-config-form" data-provider-id="{{ $provider->id }}">
                                    @csrf
                                    <!--begin::Input group-->
                                    <div class="fv-row mb-7">
                                        <label class="required fs-6 fw-semibold form-label mb-2">Modelo Padrão</label>
                                        <select class="form-select form-select-solid" name="default_model" required>
                                            <option value="">Selecione um modelo...</option>
                                            @if($provider->supported_models)
                                                @foreach($provider->supported_models as $model)
                                                    <option value="{{ $model }}" @if($model === $provider->default_model) selected @endif>{{ $model }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="form-text">Este modelo será usado por padrão nas requisições</div>
                                    </div>
                                    <!--end::Input group-->
                                    
                                    @if($provider->config_template)
                                        @foreach($provider->config_template as $key => $config)
                                            <!--begin::Input group-->
                                            <div class="fv-row mb-7">
                                                <label class="fs-6 fw-semibold form-label mb-2 @if($config['required'] ?? false) required @endif">{{ $config['label'] }}</label>
                                                @if($config['type'] === 'text')
                                                    <input type="{{ $key === 'api_key' ? 'password' : 'text' }}" 
                                                           class="form-control form-control-solid" 
                                                           name="{{ $key }}" 
                                                           placeholder="{{ $config['placeholder'] ?? $config['label'] }}"
                                                           value="{{ $config['default'] ?? '' }}"
                                                           @if($config['required'] ?? false) required @endif />
                                                @elseif($config['type'] === 'password')
                                                    <input type="password" 
                                                           class="form-control form-control-solid" 
                                                           name="{{ $key }}" 
                                                           placeholder="{{ $config['placeholder'] ?? $config['label'] }}"
                                                           @if($config['required'] ?? false) required @endif />
                                                @endif
                                                @if(isset($config['help']))
                                                    <div class="form-text">{{ $config['help'] }}</div>
                                                @elseif($key === 'api_key')
                                                    <div class="form-text">Sua chave API será criptografada e armazenada com segurança</div>
                                                @endif
                                            </div>
                                            <!--end::Input group-->
                                        @endforeach
                                    @endif
                                    
                                    <!--begin::Actions-->
                                    <div class="text-center pt-6">
                                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                                            <i class="ki-duotone ki-cross fs-6 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Cancelar
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="kt_modal_config_submit_{{ $provider->id }}">
                                            <span class="indicator-label">
                                                <i class="ki-duotone ki-check fs-6 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Salvar Configuração
                                            </span>
                                            <span class="indicator-progress">Por favor aguarde...
                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>
                                    </div>
                                    <!--end::Actions-->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Modal - Configure Provider-->
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body d-flex flex-center flex-column py-20">
                            <div class="text-center">
                                <i class="ki-duotone ki-brain fs-4x text-primary mb-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <h3 class="text-gray-800 mb-2">Nenhum provedor de IA disponível</h3>
                                <p class="text-gray-600 fs-6 mb-0">
                                    Configure os provedores de IA para começar a usar as funcionalidades.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
            <!--end::Row-->

        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->

    <!--begin::Modal - Add New AI Provider-->
    <div class="modal fade" id="kt_modal_add_provider" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_provider_header">
                    <h2 class="fw-bold">
                        <i class="ki-duotone ki-plus fs-2 text-primary me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Adicionar Novo Provedor de IA
                    </h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <!--begin::Notice-->
                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-9">
                        <i class="ki-duotone ki-information-5 fs-2tx text-primary me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Novo Provedor de IA</h4>
                                <div class="fs-6 text-gray-700">Configure um novo provedor de IA personalizado para integração com o sistema. Você pode adicionar provedores como OpenAI, Anthropic, Google, Ollama ou outros provedores compatíveis.</div>
                            </div>
                        </div>
                    </div>
                    <!--end::Notice-->
                    
                    <form id="kt_modal_add_provider_form">
                        @csrf
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-semibold form-label mb-2">Nome do Provedor</label>
                            <input type="text" class="form-control form-control-solid" name="name" placeholder="Ex: openai, anthropic, google" required />
                            <div class="form-text">Nome técnico do provedor (usado internamente)</div>
                        </div>
                        <!--end::Input group-->
                        
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-semibold form-label mb-2">Nome de Exibição</label>
                            <input type="text" class="form-control form-control-solid" name="label" placeholder="Ex: OpenAI, Claude (Anthropic), Google AI" required />
                            <div class="form-text">Nome que será exibido na interface</div>
                        </div>
                        <!--end::Input group-->
                        
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold form-label mb-2">Descrição</label>
                            <textarea class="form-control form-control-solid" name="description" rows="3" placeholder="Breve descrição do provedor..."></textarea>
                        </div>
                        <!--end::Input group-->
                        
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-semibold form-label mb-2">Ícone</label>
                            <select class="form-select form-select-solid" name="icon" required>
                                <option value="">Selecione um ícone...</option>
                                <option value="ki-brain">Cérebro</option>
                                <option value="ki-rocket">Foguete</option>
                                <option value="ki-technology-2">Tecnologia</option>
                                <option value="ki-abstract-26">Abstract</option>
                                <option value="ki-cube-3">Cubo</option>
                            </select>
                            <div class="form-text">Ícone que representará o provedor</div>
                        </div>
                        <!--end::Input group-->
                        
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-semibold form-label mb-2">URL Base da API</label>
                            <input type="url" class="form-control form-control-solid" name="base_url" placeholder="https://api.exemplo.com/v1" required />
                            <div class="form-text">URL base para as requisições da API</div>
                        </div>
                        <!--end::Input group-->
                        
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-semibold form-label mb-2">Modelo Padrão</label>
                            <input type="text" class="form-control form-control-solid" name="default_model" placeholder="Ex: gpt-3.5-turbo, claude-3-haiku" required />
                            <div class="form-text">Modelo que será usado por padrão</div>
                        </div>
                        <!--end::Input group-->
                        
                        <!--begin::Actions-->
                        <div class="text-center pt-6">
                            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                                <i class="ki-duotone ki-cross fs-6 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary" id="kt_modal_add_provider_submit">
                                <span class="indicator-label">
                                    <i class="ki-duotone ki-check fs-6 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Adicionar Provedor
                                </span>
                                <span class="indicator-progress">Por favor aguarde...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                        <!--end::Actions-->
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - Add New AI Provider-->
@endsection

@push('scripts')
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize token usage chart
            initializeTokenUsageChart();
            
            // Handle provider configuration forms
            document.querySelectorAll('.provider-config-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleProviderConfigSave(this);
                });
            });
            
            // Handle provider activation
            document.querySelectorAll('.btn-activate-provider').forEach(btn => {
                btn.addEventListener('click', function() {
                    const providerId = this.getAttribute('data-provider-id');
                    activateProvider(providerId);
                });
            });
            
            // Handle provider testing
            document.querySelectorAll('.btn-test-provider').forEach(btn => {
                btn.addEventListener('click', function() {
                    const providerId = this.getAttribute('data-provider-id');
                    testProvider(providerId);
                });
            });
            
            // Handle add new provider form
            const addProviderForm = document.getElementById('kt_modal_add_provider_form');
            if (addProviderForm) {
                addProviderForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleAddNewProvider(this);
                });
            }
            
            // Handle error badge clicks
            document.querySelectorAll('.btn-show-error').forEach(btn => {
                btn.addEventListener('click', function() {
                    const errorMessage = this.getAttribute('data-error-message');
                    showErrorDetails(errorMessage);
                });
            });
            
        });

        function initializeTokenUsageChart() {
            const chartData = @json($chartData ?? []);
            const ctx = document.getElementById('kt_charts_widget_2_chart').getContext('2d');
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.map(item => item.date),
                    datasets: [{
                        label: 'Tokens Utilizados',
                        data: chartData.map(item => item.total_tokens),
                        borderColor: '#009EF7',
                        backgroundColor: 'rgba(0, 158, 247, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#009EF7',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#009EF7',
                            borderWidth: 1,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y.toLocaleString() + ' tokens';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: '#E4E6EF',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#7E8299',
                                font: {
                                    size: 12
                                }
                            }
                        },
                        y: {
                            grid: {
                                color: '#E4E6EF',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#7E8299',
                                font: {
                                    size: 12
                                },
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        }
                    },
                    elements: {
                        point: {
                            hoverBackgroundColor: '#009EF7',
                            hoverBorderColor: '#ffffff'
                        }
                    }
                }
            });
        }

        function handleProviderConfigSave(form) {
            const providerId = form.getAttribute('data-provider-id');
            const formData = new FormData(form);
            const submitButton = form.querySelector('button[type="submit"]');
            
            // Show loading state
            submitButton.querySelector('.indicator-label').style.display = 'none';
            submitButton.querySelector('.indicator-progress').style.display = 'inline-block';
            submitButton.disabled = true;
            
            // Submit via AJAX
            fetch(`/admin/parametros/ia/providers/${providerId}/config`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal
                    const modal = form.closest('.modal');
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    modalInstance.hide();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Configuração salva!',
                        text: 'As configurações do provedor foram salvas com sucesso.',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    
                    // Reload page to update UI
                    setTimeout(() => location.reload(), 2000);
                } else {
                    throw new Error(data.message || 'Erro ao salvar configuração');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: error.message,
                    confirmButtonText: 'OK'
                });
            })
            .finally(() => {
                // Reset button state
                submitButton.querySelector('.indicator-label').style.display = 'inline-block';
                submitButton.querySelector('.indicator-progress').style.display = 'none';
                submitButton.disabled = false;
            });
        }

        function activateProvider(providerId) {
            Swal.fire({
                title: 'Ativar Provedor?',
                text: 'Este provedor se tornará o padrão para geração de texto com IA.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sim, ativar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit activation
                    fetch(`/admin/parametros/ia/providers/${providerId}/activate`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Provedor ativado!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            
                            // Reload page to update UI
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            throw new Error(data.message || 'Erro ao ativar provedor');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: error.message,
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        }
        
        function testProvider(providerId) {
            // Show loading state
            Swal.fire({
                title: 'Testando provedor...',
                text: 'Verificando conexão com o provedor de IA.',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Make actual API call to test the provider
            fetch(`/admin/parametros/ia/providers/${providerId}/test`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success result
                    Swal.fire({
                        icon: 'success',
                        title: 'Teste Bem-sucedido!',
                        html: `
                            <div class="text-start">
                                <div class="alert alert-light-success d-flex align-items-center p-5 mb-4">
                                    <i class="ki-duotone ki-check-circle fs-2hx text-success me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <span class="fs-6 fw-bold">${data.message}</span>
                                    </div>
                                </div>
                                ${data.details ? `
                                <div class="mt-3">
                                    <p class="fs-7 text-muted mb-2"><strong>Detalhes da conexão:</strong></p>
                                    <ul class="fs-7 text-muted mb-0">
                                        <li><strong>Status:</strong> ${data.details.status || 'N/A'}</li>
                                        <li><strong>Tempo de resposta:</strong> ${data.details.response_time || 'N/A'}</li>
                                        <li><strong>Provedor pronto:</strong> ${data.details.provider_ready ? 'Sim' : 'Não'}</li>
                                    </ul>
                                </div>
                                ` : ''}
                            </div>
                        `,
                        showConfirmButton: true,
                        confirmButtonText: 'Ótimo!',
                        confirmButtonColor: '#50CD89',
                        width: 600
                    });
                } else {
                    // Show error result
                    Swal.fire({
                        icon: 'error',
                        title: 'Falha no Teste',
                        html: `
                            <div class="text-start">
                                <div class="alert alert-light-danger d-flex align-items-center p-5 mb-4">
                                    <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <span class="fs-6 fw-bold">${data.message}</span>
                                        <span class="fs-7 text-muted mt-1">${data.error_details || 'Erro desconhecido'}</span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <p class="fs-7 text-muted mb-2"><strong>Possíveis soluções:</strong></p>
                                    <ul class="fs-7 text-muted">
                                        <li>Verifique se a chave de API está correta</li>
                                        <li>Confirme se há créditos/cota disponível</li>
                                        <li>Teste a conectividade com a internet</li>
                                        <li>Reconfigure o provedor se necessário</li>
                                    </ul>
                                </div>
                            </div>
                        `,
                        showConfirmButton: true,
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#F1416C',
                        width: 650
                    });
                }
            })
            .catch(error => {
                // Handle network or other errors
                Swal.fire({
                    icon: 'error',
                    title: 'Erro de Comunicação',
                    html: `
                        <div class="text-start">
                            <div class="alert alert-light-danger d-flex align-items-center p-5">
                                <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <span class="fs-6">Erro ao executar o teste de conexão</span>
                                    <span class="fs-7 text-muted mt-1">Verifique sua conexão de internet e tente novamente.</span>
                                </div>
                            </div>
                        </div>
                    `,
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#009EF7',
                    width: 600
                });
            });
        }
        
        
        function handleAddNewProvider(form) {
            const submitButton = form.querySelector('button[type="submit"]');
            
            // Show loading state
            submitButton.querySelector('.indicator-label').style.display = 'none';
            submitButton.querySelector('.indicator-progress').style.display = 'inline-block';
            submitButton.disabled = true;
            
            // For now, simulate the creation
            setTimeout(() => {
                // Close modal
                const modal = form.closest('.modal');
                const modalInstance = bootstrap.Modal.getInstance(modal);
                modalInstance.hide();
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Provedor adicionado!',
                    text: 'O novo provedor de IA foi adicionado com sucesso. A funcionalidade completa será implementada em breve.',
                    showConfirmButton: true,
                    confirmButtonText: 'OK'
                });
                
                // Reset form
                form.reset();
                
                // Reset button state
                submitButton.querySelector('.indicator-label').style.display = 'inline-block';
                submitButton.querySelector('.indicator-progress').style.display = 'none';
                submitButton.disabled = false;
            }, 2000);
        }
        
        function showErrorDetails(errorMessage) {
            Swal.fire({
                icon: 'error',
                title: 'Erro na Comunicação com API',
                html: `
                    <div class="text-start">
                        <p class="mb-3"><strong>Detalhes do erro:</strong></p>
                        <div class="alert alert-light-danger d-flex align-items-center p-5">
                            <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="d-flex flex-column">
                                <span class="fs-6">${errorMessage}</span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="fs-7 text-muted mb-2"><strong>Possíveis soluções:</strong></p>
                            <ul class="fs-7 text-muted">
                                <li>Verifique se a chave de API está correta</li>
                                <li>Confirme se há créditos/cota disponível</li>
                                <li>Teste a conectividade com a internet</li>
                                <li>Reconfigure o provedor se necessário</li>
                            </ul>
                        </div>
                    </div>
                `,
                showConfirmButton: true,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#009EF7',
                width: 600,
                customClass: {
                    popup: 'swal2-show',
                    header: 'border-0',
                    title: 'fs-1 fw-bold text-gray-900'
                }
            });
        }
    </script>
@endpush

@push('styles')
<style>
/* Enhanced Provider Cards */
.card.border-success {
    border-width: 2px !important;
    box-shadow: 0 0 30px rgba(80, 205, 137, 0.15);
    transition: all 0.3s ease;
}

.card.border-success:hover {
    box-shadow: 0 0 40px rgba(80, 205, 137, 0.25);
    transform: translateY(-2px);
}

/* Error State Cards */
.card.border-danger {
    border-width: 2px !important;
    box-shadow: 0 0 30px rgba(248, 113, 113, 0.15);
    transition: all 0.3s ease;
    background: linear-gradient(135deg, rgba(248, 113, 113, 0.02) 0%, rgba(248, 113, 113, 0.01) 100%);
}

.card.border-danger:hover {
    box-shadow: 0 0 40px rgba(248, 113, 113, 0.25);
    transform: translateY(-2px);
}

/* Error Badge Styles */
.badge.badge-light-danger {
    background-color: rgba(248, 113, 113, 0.1);
    color: #dc3545;
    border: 1px solid rgba(248, 113, 113, 0.2);
    transition: all 0.3s ease;
}

.badge.badge-light-danger:hover {
    background-color: rgba(248, 113, 113, 0.15);
    transform: scale(1.05);
    cursor: pointer;
}

.card-flush:hover {
    transition: all 0.3s ease;
    transform: translateY(-2px);
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}

/* Stats Cards Enhancement */
.card-flush .card-header .card-title {
    flex-grow: 1;
}

/* Modal Enhancements */
.modal-content {
    border-radius: 15px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.modal-header {
    background: linear-gradient(135deg, rgba(0, 158, 247, 0.05) 0%, rgba(0, 158, 247, 0.02) 100%);
    border-bottom: 1px solid rgba(0, 158, 247, 0.1);
}

.notice {
    animation: fadeInUp 0.5s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Form Controls */
.form-control-solid:focus {
    border-color: #009ef7;
    box-shadow: 0 0 0 0.2rem rgba(0, 158, 247, 0.15);
}

.form-select-solid:focus {
    border-color: #009ef7;
    box-shadow: 0 0 0 0.2rem rgba(0, 158, 247, 0.15);
}

/* Button Enhancements */
.btn-activate-provider {
    transition: all 0.3s ease;
}

.btn-activate-provider:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0, 158, 247, 0.3);
}

/* Badge Enhancements */
.badge {
    transition: all 0.3s ease;
}

.badge:hover {
    transform: scale(1.05);
}

/* Chart Container */
#kt_charts_widget_2_chart {
    border-radius: 8px;
}

/* Loading States */
.btn .indicator-progress {
    display: none;
}

.btn.loading .indicator-label {
    display: none;
}

.btn.loading .indicator-progress {
    display: inline-block;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .card-flush:hover {
        transform: none;
    }
    
    .btn-activate-provider:hover {
        transform: none;
    }
}

/* Card Icon Colors */
.text-openai { color: #10a37f !important; }
.text-anthropic { color: #d4722b !important; }
.text-google { color: #4285f4 !important; }
.text-ollama { color: #8b5cf6 !important; }

/* Success States */
.border-success {
    background: linear-gradient(135deg, rgba(80, 205, 137, 0.02) 0%, rgba(80, 205, 137, 0.01) 100%);
}

/* Header Card Enhancement */
.symbol-label.bg-light-info {
    background: linear-gradient(135deg, #009ef7 0%, #50cd89 100%) !important;
}

.symbol-label.bg-light-info i {
    color: white !important;
}
</style>
@endpush