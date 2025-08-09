@extends('components.layouts.app')

@section('title', 'Configuração de IA: ' . $aiConfiguration->name)

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    @switch($aiConfiguration->provider)
                        @case('openai')
                            <i class="ki-duotone ki-abstract-26 fs-2 me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            @break
                        @case('anthropic')
                            <i class="ki-duotone ki-crown-2 fs-2 me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            @break
                        @case('google')
                            <i class="ki-duotone ki-google fs-2 me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            @break
                        @case('local')
                            <i class="ki-duotone ki-home fs-2 me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            @break
                        @default
                            <i class="ki-duotone ki-technology-4 fs-2 me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                    @endswitch
                    {{ $aiConfiguration->name }}
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
                    <li class="breadcrumb-item text-muted">{{ $aiConfiguration->name }}</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!--begin::Test button-->
                <button type="button" class="btn btn-sm btn-flex btn-warning me-2" onclick="testConnection()">
                    <i class="ki-duotone ki-rocket fs-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Testar Conexão
                </button>
                <!--end::Test button-->
                <!--begin::Edit button-->
                <a href="{{ route('admin.ai-configurations.edit', $aiConfiguration) }}" class="btn btn-sm btn-flex btn-primary me-2">
                    <i class="ki-duotone ki-pencil fs-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Editar
                </a>
                <!--end::Edit button-->
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

            @if (session('warning'))
                <div class="alert alert-warning d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-warning fs-2hx text-warning me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h5 class="mb-1">Aviso</h5>
                        <span>{{ session('warning') }}</span>
                    </div>
                </div>
            @endif
            <!--end::Alert-->

            <!--begin::Row-->
            <div class="row g-6 g-xl-9">
                <!--begin::Col-->
                <div class="col-lg-8">
                    <!--begin::Details Card-->
                    <div class="card card-flush mb-6">
                        <!--begin::Card header-->
                        <div class="card-header pt-8">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <h2 class="fw-bold text-gray-900">
                                    <i class="ki-duotone ki-information-5 fs-2 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    Detalhes da Configuração
                                </h2>
                            </div>
                            <!--end::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar">
                                <div class="d-flex align-items-center">
                                    @if($aiConfiguration->is_active)
                                        @if($aiConfiguration->isHealthy())
                                            <span class="badge badge-light-success me-2">
                                                <i class="ki-duotone ki-check-circle fs-7 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Funcionando
                                            </span>
                                        @else
                                            <span class="badge badge-light-warning me-2">
                                                <i class="ki-duotone ki-warning fs-7 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Não testado
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge badge-light-secondary me-2">
                                            <i class="ki-duotone ki-cross-circle fs-7 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Inativo
                                        </span>
                                    @endif
                                    
                                    <span class="symbol symbol-20px symbol-circle me-2">
                                        <span class="symbol-label bg-{{ $aiConfiguration->is_active ? 'success' : 'secondary' }} text-white fw-bold fs-8">
                                            {{ $aiConfiguration->priority }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body">
                            <!--begin::Details Grid-->
                            <div class="row mb-8">
                                <div class="col-xl-3">
                                    <div class="fs-6 fw-semibold mt-2 mb-3">Provedor:</div>
                                </div>
                                <div class="col-xl-9">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-50px me-4">
                                            <span class="symbol-label bg-light-{{ $aiConfiguration->is_active ? 'primary' : 'secondary' }}">
                                                @switch($aiConfiguration->provider)
                                                    @case('openai')
                                                        <i class="ki-duotone ki-abstract-26 fs-2x text-{{ $aiConfiguration->is_active ? 'primary' : 'secondary' }}"></i>
                                                        @break
                                                    @case('anthropic')
                                                        <i class="ki-duotone ki-crown-2 fs-2x text-{{ $aiConfiguration->is_active ? 'primary' : 'secondary' }}"></i>
                                                        @break
                                                    @case('google')
                                                        <i class="ki-duotone ki-google fs-2x text-{{ $aiConfiguration->is_active ? 'primary' : 'secondary' }}"></i>
                                                        @break
                                                    @case('local')
                                                        <i class="ki-duotone ki-home fs-2x text-{{ $aiConfiguration->is_active ? 'primary' : 'secondary' }}"></i>
                                                        @break
                                                @endswitch
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <div class="fw-bold fs-6">
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
                                            <div class="fw-semibold text-muted fs-7">{{ ucfirst($aiConfiguration->provider) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-8">
                                <div class="col-xl-3">
                                    <div class="fs-6 fw-semibold mt-2 mb-3">Modelo:</div>
                                </div>
                                <div class="col-xl-9">
                                    <span class="badge badge-light-info fs-6 px-3 py-2">{{ $aiConfiguration->model }}</span>
                                </div>
                            </div>

                            <div class="row mb-8">
                                <div class="col-xl-3">
                                    <div class="fs-6 fw-semibold mt-2 mb-3">Configurações:</div>
                                </div>
                                <div class="col-xl-9">
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge badge-light-primary">
                                            <i class="ki-duotone ki-setting-2 fs-7 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Tokens: {{ number_format($aiConfiguration->max_tokens) }}
                                        </span>
                                        <span class="badge badge-light-primary">
                                            <i class="ki-duotone ki-chart-line-up fs-7 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            Temperatura: {{ $aiConfiguration->temperature }}
                                        </span>
                                        @if($aiConfiguration->cost_per_1k_tokens)
                                            <span class="badge badge-light-info">
                                                <i class="ki-duotone ki-dollar fs-7 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                                ${{ number_format($aiConfiguration->cost_per_1k_tokens, 4) }}/1k tokens
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($aiConfiguration->base_url)
                            <div class="row mb-8">
                                <div class="col-xl-3">
                                    <div class="fs-6 fw-semibold mt-2 mb-3">URL Base:</div>
                                </div>
                                <div class="col-xl-9">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-abstract-41 fs-2 text-gray-500 me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <code class="fs-6 text-gray-700">{{ $aiConfiguration->base_url }}</code>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($aiConfiguration->daily_token_limit)
                            <div class="row mb-8">
                                <div class="col-xl-3">
                                    <div class="fs-6 fw-semibold mt-2 mb-3">Uso Diário:</div>
                                </div>
                                <div class="col-xl-9">
                                    @php
                                        $percentage = min(100, ($aiConfiguration->daily_tokens_used / $aiConfiguration->daily_token_limit) * 100);
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex flex-column flex-grow-1 me-4">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="fw-bold text-gray-900">{{ number_format($aiConfiguration->daily_tokens_used) }}</span>
                                                <span class="text-muted">/ {{ number_format($aiConfiguration->daily_token_limit) }} tokens</span>
                                            </div>
                                            <div class="progress h-6px">
                                                <div class="progress-bar {{ $percentage > 80 ? 'bg-danger' : ($percentage > 60 ? 'bg-warning' : 'bg-success') }}" 
                                                     style="width: {{ $percentage }}%"></div>
                                            </div>
                                            @if($aiConfiguration->remaining_tokens !== null)
                                                <div class="text-muted fs-7 mt-1">{{ number_format($aiConfiguration->remaining_tokens) }} tokens restantes</div>
                                            @endif
                                        </div>
                                        <div class="fw-bold text-{{ $percentage > 80 ? 'danger' : ($percentage > 60 ? 'warning' : 'success') }} fs-6">
                                            {{ number_format($percentage, 1) }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($aiConfiguration->custom_prompt)
                            <div class="row mb-8">
                                <div class="col-xl-3">
                                    <div class="fs-6 fw-semibold mt-2 mb-3">Prompt Personalizado:</div>
                                </div>
                                <div class="col-xl-9">
                                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6">
                                        <i class="ki-duotone ki-message-text-2 fs-2tx text-info me-4">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <div class="d-flex flex-stack flex-grow-1">
                                            <div class="fw-semibold">
                                                <pre class="fs-6 text-gray-700 mb-0">{{ $aiConfiguration->custom_prompt }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($aiConfiguration->last_tested_at)
                            <div class="row mb-8">
                                <div class="col-xl-3">
                                    <div class="fs-6 fw-semibold mt-2 mb-3">Último Teste:</div>
                                </div>
                                <div class="col-xl-9">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-30px me-3">
                                            <span class="symbol-label bg-light-{{ $aiConfiguration->last_test_success ? 'success' : 'danger' }}">
                                                <i class="ki-duotone ki-{{ $aiConfiguration->last_test_success ? 'check-circle' : 'cross-circle' }} fs-6 text-{{ $aiConfiguration->last_test_success ? 'success' : 'danger' }}">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-gray-900">{{ $aiConfiguration->last_tested_at->format('d/m/Y H:i') }}</span>
                                            <span class="text-{{ $aiConfiguration->last_test_success ? 'success' : 'danger' }} fs-7">
                                                {{ $aiConfiguration->last_test_success ? '✓ Sucesso' : '✗ Falhou' }}
                                            </span>
                                            @if(!$aiConfiguration->last_test_success && $aiConfiguration->last_test_error)
                                                <span class="text-muted fs-8 mt-1">{{ Str::limit($aiConfiguration->last_test_error, 100) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!--begin::Actions-->
                            <div class="d-flex justify-content-start pt-6">
                                <button type="button" class="btn btn-warning me-3" onclick="testConnection()">
                                    <i class="ki-duotone ki-rocket fs-3 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Testar Conexão
                                </button>
                                @if($aiConfiguration->daily_token_limit && $aiConfiguration->daily_tokens_used > 0)
                                <button type="button" class="btn btn-light-primary me-3" onclick="resetUsage()">
                                    <i class="ki-duotone ki-arrows-circle fs-3 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Reset Contador
                                </button>
                                @endif
                                <button type="button" class="btn btn-light-{{ $aiConfiguration->is_active ? 'danger' : 'success' }}" onclick="toggleStatus()">
                                    <i class="ki-duotone ki-{{ $aiConfiguration->is_active ? 'cross-circle' : 'check-circle' }} fs-3 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    {{ $aiConfiguration->is_active ? 'Desativar' : 'Ativar' }}
                                </button>
                            </div>
                            <!--end::Actions-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Details Card-->
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-lg-4">
                    <!--begin::Statistics Card-->
                    <div class="card card-flush mb-6">
                        <!--begin::Card header-->
                        <div class="card-header pt-8">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <h3 class="fw-bold text-gray-900">
                                    <i class="ki-duotone ki-chart-simple fs-2 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                    Estatísticas
                                </h3>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body">
                            <div class="d-flex align-items-center border-0 mb-7">
                                <div class="symbol symbol-50px me-5">
                                    <span class="symbol-label bg-light-{{ $aiConfiguration->canBeUsed() ? 'success' : 'danger' }}">
                                        <i class="ki-duotone ki-{{ $aiConfiguration->canBeUsed() ? 'check-circle' : 'cross-circle' }} fs-2x text-{{ $aiConfiguration->canBeUsed() ? 'success' : 'danger' }}">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fs-2 fw-bold text-gray-900">{{ $aiConfiguration->canBeUsed() ? 'Disponível' : 'Indisponível' }}</div>
                                    <div class="fs-7 fw-semibold text-gray-600">Status Atual</div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center border-0 mb-7">
                                <div class="symbol symbol-50px me-5">
                                    <span class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-abstract-26 fs-2x text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fs-2 fw-bold text-gray-900">#{{ $aiConfiguration->priority }}</div>
                                    <div class="fs-7 fw-semibold text-gray-600">Prioridade</div>
                                </div>
                            </div>

                            @if($aiConfiguration->daily_usage_percentage !== null)
                            <div class="d-flex align-items-center border-0 mb-7">
                                <div class="symbol symbol-50px me-5">
                                    <span class="symbol-label bg-light-warning">
                                        <i class="ki-duotone ki-chart-pie-4 fs-2x text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fs-2 fw-bold text-gray-900">{{ number_format($aiConfiguration->daily_usage_percentage, 1) }}%</div>
                                    <div class="fs-7 fw-semibold text-gray-600">Uso Diário</div>
                                </div>
                            </div>
                            @endif

                            <div class="separator separator-dashed my-5"></div>

                            <div class="d-flex flex-stack">
                                <div class="text-muted fw-semibold fs-7">
                                    <strong>Criada:</strong><br>
                                    {{ $aiConfiguration->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="text-muted fw-semibold fs-7 text-end">
                                    <strong>Atualizada:</strong><br>
                                    {{ $aiConfiguration->updated_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Statistics Card-->

                    <!--begin::Actions Card-->
                    <div class="card card-flush">
                        <!--begin::Card header-->
                        <div class="card-header pt-8">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <h3 class="fw-bold text-gray-900">
                                    <i class="ki-duotone ki-setting-3 fs-2 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                    </i>
                                    Ações
                                </h3>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body">
                            <div class="d-flex flex-column gap-3">
                                <a href="{{ route('admin.ai-configurations.edit', $aiConfiguration) }}" class="btn btn-flex btn-primary">
                                    <i class="ki-duotone ki-pencil fs-3 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Editar Configuração
                                </a>
                                
                                <a href="{{ route('admin.ai-configurations.index') }}" class="btn btn-flex btn-secondary">
                                    <i class="ki-duotone ki-arrow-left fs-3 me-2"></i>
                                    Voltar à Lista
                                </a>
                                
                                <div class="separator my-3"></div>
                                
                                <button type="button" class="btn btn-flex btn-light-danger" onclick="deleteConfiguration()">
                                    <i class="ki-duotone ki-trash fs-3 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                    </i>
                                    Excluir Configuração
                                </button>
                            </div>

                            <div class="separator my-6"></div>

                            <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-4">
                                <i class="ki-duotone ki-information-5 fs-2tx text-primary me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <div class="fs-6 text-gray-700">
                                            Configure múltiplas APIs para garantir alta disponibilidade. 
                                            O sistema usa ordem de prioridade para fallback automático.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Actions Card-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->

<!--begin::Modal - Delete Confirmation-->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y pt-0 pb-15">
                <div class="text-center mb-13">
                    <h1 class="mb-3">Confirmar Exclusão</h1>
                    <div class="text-muted fw-semibold fs-5">
                        Tem certeza de que deseja excluir a configuração 
                        <strong>"{{ $aiConfiguration->name }}"</strong>?
                    </div>
                    <div class="text-danger fw-bold fs-6 mt-4">
                        <i class="ki-duotone ki-warning fs-2x text-danger me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Esta ação não pode ser desfeita
                    </div>
                </div>
                <div class="d-flex flex-center flex-row-fluid pt-12">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                        <i class="ki-duotone ki-trash fs-3 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                        </i>
                        Sim, Excluir
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Delete Confirmation-->

@endsection

@push('scripts')
<script>
function testConnection() {
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    
    // Mostrar loading
    btn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Testando...';
    btn.disabled = true;
    
    // Criar elemento de status visual
    let statusElement = document.getElementById('test-status-show');
    if (!statusElement) {
        statusElement = document.createElement('div');
        statusElement.id = 'test-status-show';
        statusElement.className = 'alert alert-info mt-3';
        statusElement.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Testando conexão com o provedor de IA...';
        btn.parentNode.insertBefore(statusElement, btn.nextSibling);
    }
    
    fetch(`/admin/ai-configurations/{{ $aiConfiguration->id }}/test`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Test response:', data);
        
        // Remover status de loading
        if (statusElement) {
            statusElement.remove();
        }
        
        if (data.success) {
            // Mostrar sucesso
            const successElement = document.createElement('div');
            successElement.className = 'alert alert-success mt-3';
            successElement.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="ki-duotone ki-check-circle fs-2 text-success me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div>
                        <h5 class="mb-1">✅ Teste Bem-sucedido!</h5>
                        <p class="mb-0">${data.message}</p>
                        ${data.response_preview ? `<small class="text-muted">Preview: ${data.response_preview}</small>` : ''}
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-light mt-2" onclick="this.parentElement.remove()">Fechar</button>
            `;
            btn.parentNode.insertBefore(successElement, btn.nextSibling);
            
            // Usar toastr se disponível, senão usar alert nativo
            if (typeof toastr !== 'undefined') {
                toastr.success('✅ ' + data.message);
            } else {
                alert('✅ Teste bem-sucedido: ' + data.message);
            }
            
            // Recarregar após 3 segundos para atualizar status
            setTimeout(() => {
                location.reload();
            }, 3000);
            
        } else {
            // Mostrar erro
            const errorElement = document.createElement('div');
            errorElement.className = 'alert alert-danger mt-3';
            errorElement.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="ki-duotone ki-cross-circle fs-2 text-danger me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div>
                        <h5 class="mb-1">❌ Teste Falhou</h5>
                        <p class="mb-0">${data.message}</p>
                        ${data.details ? `<details class="mt-2"><summary>Detalhes técnicos</summary><pre>${data.details}</pre></details>` : ''}
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-light mt-2" onclick="this.parentElement.remove()">Fechar</button>
            `;
            btn.parentNode.insertBefore(errorElement, btn.nextSibling);
            
            // Usar toastr se disponível, senão usar alert nativo
            if (typeof toastr !== 'undefined') {
                toastr.error('❌ ' + data.message);
            } else {
                alert('❌ Teste falhou: ' + data.message);
            }
        }
    })
    .catch(error => {
        console.error('Test error:', error);
        
        // Remover status de loading
        if (statusElement) {
            statusElement.remove();
        }
        
        // Mostrar erro de conexão
        const errorElement = document.createElement('div');
        errorElement.className = 'alert alert-danger mt-3';
        errorElement.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="ki-duotone ki-cross-circle fs-2 text-danger me-3">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <div>
                    <h5 class="mb-1">🔌 Erro de Conexão</h5>
                    <p class="mb-0">Não foi possível conectar com o servidor: ${error.message}</p>
                    <small class="text-muted">Verifique sua conexão com a internet e tente novamente.</small>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-light mt-2" onclick="this.parentElement.remove()">Fechar</button>
        `;
        btn.parentNode.insertBefore(errorElement, btn.nextSibling);
        
        const errorMessage = `Erro de conexão: ${error.message}`;
        // Usar toastr se disponível, senão usar alert nativo
        if (typeof toastr !== 'undefined') {
            toastr.error(errorMessage);
        } else {
            alert(errorMessage);
        }
    })
    .finally(() => {
        // Restaurar botão
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function toggleStatus() {
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    
    fetch(`/admin/ai-configurations/{{ $aiConfiguration->id }}/toggle-active`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            location.reload();
        } else {
            toastr.error(data.message);
        }
    })
    .catch(error => {
        toastr.error('Erro ao alterar status: ' + error.message);
    })
    .finally(() => {
        btn.disabled = false;
    });
}

function resetUsage() {
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Resetando...';
    btn.disabled = true;
    
    fetch(`/admin/ai-configurations/{{ $aiConfiguration->id }}/reset-usage`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            location.reload();
        } else {
            toastr.error(data.message);
        }
    })
    .catch(error => {
        toastr.error('Erro ao resetar contador: ' + error.message);
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function deleteConfiguration() {
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function confirmDelete() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/ai-configurations/{{ $aiConfiguration->id }}';
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    
    const tokenField = document.createElement('input');
    tokenField.type = 'hidden';
    tokenField.name = '_token';
    tokenField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    form.appendChild(methodField);
    form.appendChild(tokenField);
    document.body.appendChild(form);
    
    form.submit();
}
</script>
@endpush