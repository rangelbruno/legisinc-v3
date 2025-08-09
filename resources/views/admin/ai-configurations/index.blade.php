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
                    <i class="ki-duotone ki-technology-4 fs-2 me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    Configurações de IA
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
                    <li class="breadcrumb-item text-muted">Configurações de IA</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!--begin::Primary button-->
                <a href="{{ route('admin.ai-configurations.create') }}" class="btn btn-sm btn-flex btn-primary">
                    <i class="ki-duotone ki-plus fs-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Nova Configuração
                </a>
                <!--end::Primary button-->
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

            <!--begin::Row-->
            <div class="row g-6 g-xl-9">
                <!--begin::Col-->
                <div class="col-lg-4 col-xl-4">
                    
                    <!-- begin::Stats Card -->
                    <div class="card card-flush mb-6">
                        <div class="card-header pt-8">
                            <div class="card-title">
                                <h3 class="fw-bold text-gray-900">
                                    <i class="ki-duotone ki-chart-simple fs-2 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                    Visão Geral
                                </h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- begin::Stats Grid -->
                            <div class="row g-0">
                                <div class="col-6">
                                    <div class="d-flex align-items-center border-0 p-6">
                                        <div class="symbol symbol-50px me-5">
                                            <span class="symbol-label bg-light-primary">
                                                <i class="ki-duotone ki-technology-4 fs-2x text-primary">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <div class="fs-2 fw-bold text-gray-900" id="total-configs">{{ $configurations->count() }}</div>
                                            <div class="fs-7 fw-semibold text-gray-600">Total</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center border-0 p-6">
                                        <div class="symbol symbol-50px me-5">
                                            <span class="symbol-label bg-light-success">
                                                <i class="ki-duotone ki-check-circle fs-2x text-success">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <div class="fs-2 fw-bold text-gray-900" id="active-configs">{{ $configurations->where('is_active', true)->count() }}</div>
                                            <div class="fs-7 fw-semibold text-gray-600">Ativas</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center border-0 p-6">
                                        <div class="symbol symbol-50px me-5">
                                            <span class="symbol-label bg-light-info">
                                                <i class="ki-duotone ki-setting-2 fs-2x text-info">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <div class="fs-2 fw-bold text-gray-900">{{ $configurations->pluck('provider')->unique()->count() }}</div>
                                            <div class="fs-7 fw-semibold text-gray-600">Provedores</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center border-0 p-6">
                                        <div class="symbol symbol-50px me-5">
                                            <span class="symbol-label bg-light-{{ $configurations->where('is_active', true)->where('last_test_success', true)->count() > 0 ? 'success' : 'warning' }}">
                                                <i class="ki-duotone ki-{{ $configurations->where('is_active', true)->where('last_test_success', true)->count() > 0 ? 'shield-tick' : 'warning-2' }} fs-2x text-{{ $configurations->where('is_active', true)->where('last_test_success', true)->count() > 0 ? 'success' : 'warning' }}">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <div class="fs-2 fw-bold text-gray-900">{{ $configurations->where('is_active', true)->where('last_test_success', true)->count() }}</div>
                                            <div class="fs-7 fw-semibold text-gray-600">Saudáveis</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end::Stats Grid -->

                        </div>
                    </div>
                    <!-- end::Stats Card -->

                    <!-- begin::Actions Card -->
                    <div class="card card-flush">
                        <div class="card-header pt-8">
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
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column gap-3">
                                <a href="{{ route('admin.ai-configurations.create') }}" class="btn btn-flex btn-primary">
                                    <i class="ki-duotone ki-plus fs-3 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Nova Configuração
                                </a>
                                
                                <button type="button" class="btn btn-flex btn-light-warning" onclick="testAllConfigurations()">
                                    <i class="ki-duotone ki-rocket fs-3 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Testar Todas
                                </button>
                                
                                <button type="button" class="btn btn-flex btn-light-info" onclick="refreshUsageStats()">
                                    <i class="ki-duotone ki-arrows-circle fs-3 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Atualizar Status
                                </button>
                            </div>
                            
                            @if($configurations->isNotEmpty())
                            <div class="separator my-6"></div>
                            
                            <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-4">
                                <i class="ki-duotone ki-information-5 fs-1 text-primary me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <div class="fs-6 text-gray-700">
                                            Configure múltiplas APIs para garantir disponibilidade. O sistema usa ordem de prioridade para fallback automático.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <!-- end::Actions Card -->
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-lg-8 col-xl-8">
                    <div class="card card-flush">
                        <div class="card-header pt-8">
                            <div class="card-title">
                                <h3 class="fw-bold text-gray-900">
                                    <i class="ki-duotone ki-element-11 fs-2 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                    Configurações Disponíveis
                                </h3>
                            </div>
                            <div class="card-toolbar">
                                <div class="d-flex align-items-center gap-2">
                                    @if($configurations->isNotEmpty())
                                    <span class="badge badge-light-success">{{ $configurations->where('is_active', true)->count() }} Ativas</span>
                                    <span class="badge badge-light-secondary">{{ $configurations->where('is_active', false)->count() }} Inativas</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            @if($configurations->isEmpty())
                            <!-- begin::Empty State -->
                            <div class="text-center">
                                <div class="pt-10 pb-10">
                                    <i class="ki-duotone ki-technology-4 fs-4x opacity-50 mb-10">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    
                                    <h3 class="fs-1 text-gray-800 fw-bold mb-3">Nenhuma Configuração de IA</h3>
                                    <div class="text-gray-600 fw-semibold fs-5 mb-10">
                                        Configure suas primeiras APIs de IA para começar a gerar<br>
                                        textos automaticamente com fallback inteligente
                                    </div>
                                    
                                    <div class="text-center">
                                        <a href="{{ route('admin.ai-configurations.create') }}" class="btn btn-primary btn-lg">
                                            <i class="ki-duotone ki-plus fs-3 me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Criar Primera Configuração
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- end::Empty State -->
                            @else
                            <!-- begin::Table -->
                            <div class="table-responsive">
                                <table class="table table-row-dashed align-middle gs-5 gy-4" id="configurations-table">
                                    <thead>
                                        <tr class="fw-semibold fs-6 text-gray-800 border-bottom-2 border-gray-200">
                                            <th class="min-w-200px ps-9">Configuração</th>
                                            <th class="min-w-100px d-none d-sm-table-cell">Status</th>
                                            <th class="min-w-120px d-none d-md-table-cell">Uso</th>
                                            <th class="min-w-100px text-end pe-9">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($configurations->sortBy('priority') as $config)
                                        <tr data-config-id="{{ $config->id }}">
                                            <td class="ps-9">
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-50px me-4">
                                                        <span class="symbol-label bg-light-{{ $config->is_active ? 'primary' : 'secondary' }}">
                                                            @switch($config->provider)
                                                                @case('openai')
                                                                    <i class="ki-duotone ki-abstract-26 fs-2x text-{{ $config->is_active ? 'primary' : 'secondary' }}"></i>
                                                                    @break
                                                                @case('anthropic')
                                                                    <i class="ki-duotone ki-crown-2 fs-2x text-{{ $config->is_active ? 'primary' : 'secondary' }}"></i>
                                                                    @break
                                                                @case('google')
                                                                    <i class="ki-duotone ki-google fs-2x text-{{ $config->is_active ? 'primary' : 'secondary' }}"></i>
                                                                    @break
                                                                @case('local')
                                                                    <i class="ki-duotone ki-home fs-2x text-{{ $config->is_active ? 'primary' : 'secondary' }}"></i>
                                                                    @break
                                                            @endswitch
                                                        </span>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <div class="d-flex align-items-center mb-1">
                                                            <span class="symbol symbol-20px symbol-circle me-2">
                                                                <span class="symbol-label bg-{{ $config->is_active ? 'success' : 'secondary' }} text-white fw-bold fs-8">
                                                                    {{ $config->priority }}
                                                                </span>
                                                            </span>
                                                            <a href="{{ route('admin.ai-configurations.show', $config) }}" class="text-gray-900 text-hover-primary fw-bold fs-6">
                                                                {{ $config->name }}
                                                            </a>
                                                        </div>
                                                        <div class="d-flex align-items-center flex-wrap">
                                                            <span class="text-muted fw-semibold fs-7 me-2">{{ $providers[$config->provider]['name'] ?? ucfirst($config->provider) }}</span>
                                                            <span class="bullet bullet-dot bg-gray-400 h-6px w-6px me-2"></span>
                                                            <span class="badge badge-light-info badge-sm me-2">{{ $config->model }}</span>
                                                            
                                                            <!-- Mobile Status -->
                                                            <div class="d-sm-none">
                                                                @if($config->is_active)
                                                                    @if($config->isHealthy())
                                                                        <span class="badge badge-success badge-sm">Funcionando</span>
                                                                    @else
                                                                        <span class="badge badge-warning badge-sm">Não testado</span>
                                                                    @endif
                                                                @else
                                                                    <span class="badge badge-secondary badge-sm">Inativo</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="d-none d-sm-table-cell">
                                                @if($config->is_active)
                                                    @if($config->isHealthy())
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
                                            </td>
                                            
                                            <td class="d-none d-md-table-cell">
                                                @if($config->daily_token_limit)
                                                    @php
                                                        $percentage = min(100, ($config->daily_tokens_used / $config->daily_token_limit) * 100);
                                                    @endphp
                                                    <div class="d-flex align-items-center">
                                                        <div class="d-flex flex-column me-3">
                                                            <span class="fs-7 fw-bold text-gray-900">{{ number_format($config->daily_tokens_used) }}</span>
                                                            <span class="fs-8 text-muted">de {{ number_format($config->daily_token_limit) }}</span>
                                                        </div>
                                                        <div class="progress h-4px w-50px">
                                                            <div class="progress-bar {{ $percentage > 80 ? 'bg-danger' : ($percentage > 60 ? 'bg-warning' : 'bg-success') }}" 
                                                                 style="width: {{ $percentage }}%"></div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="badge badge-light-success">
                                                        <i class="ki-duotone ki-infinity fs-7 me-1">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        Ilimitado
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-9">
                                                <div class="d-flex justify-content-end align-items-center">
                                                    <!-- Test Button -->
                                                    <button class="btn btn-icon btn-light-primary btn-active-primary btn-sm me-2" 
                                                            onclick="testConnection({{ $config->id }})" 
                                                            title="Testar Conexão"
                                                            data-bs-toggle="tooltip">
                                                        <i class="ki-duotone ki-rocket fs-6">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </button>
                                                    
                                                    <!-- Actions Dropdown -->
                                                    <div class="dropdown">
                                                        <button class="btn btn-icon btn-light btn-active-light-primary btn-sm" 
                                                                type="button" 
                                                                data-bs-toggle="dropdown"
                                                                data-bs-auto-close="true"
                                                                aria-expanded="false">
                                                            <i class="ki-duotone ki-dots-vertical fs-6">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end min-w-150px py-4">
                                                            <a class="dropdown-item" href="{{ route('admin.ai-configurations.show', $config) }}">
                                                                <i class="ki-duotone ki-eye fs-6 me-2"></i>
                                                                Ver
                                                            </a>
                                                            <a class="dropdown-item" href="{{ route('admin.ai-configurations.edit', $config) }}">
                                                                <i class="ki-duotone ki-pencil fs-6 me-2"></i>
                                                                Editar
                                                            </a>
                                                            <div class="dropdown-divider"></div>
                                                            <button class="dropdown-item" onclick="toggleActive({{ $config->id }})">
                                                                <i class="ki-duotone ki-{{ $config->is_active ? 'cross-circle' : 'check-circle' }} fs-6 me-2"></i>
                                                                {{ $config->is_active ? 'Desativar' : 'Ativar' }}
                                                            </button>
                                                            <div class="dropdown-divider"></div>
                                                            <button class="dropdown-item text-danger" onclick="deleteConfiguration({{ $config->id }}, '{{ $config->name }}')">
                                                                <i class="ki-duotone ki-trash fs-6 me-2"></i>
                                                                Excluir
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- end::Table -->
                            @endif
                        </div>
                    </div>
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
                        <strong id="delete-config-name"></strong>?
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
                    <button type="button" class="btn btn-danger" id="confirm-delete">
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
let deleteConfigId = null;

function testConnection(configId) {
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    
    // Mostrar loading no botão
    btn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Testando...';
    btn.disabled = true;
    
    fetch(`/admin/ai-configurations/${configId}/test`, {
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
        
        if (data.success) {
            // Usar toastr se disponível, senão usar alert nativo
            if (typeof toastr !== 'undefined') {
                toastr.success(`✅ ${data.message}`, 'Teste Bem-sucedido', {
                    timeOut: 5000,
                    extendedTimeOut: 2000
                });
            } else {
                alert('✅ Teste bem-sucedido: ' + data.message);
            }
            
            // Recarregar a página após 2 segundos para atualizar o status
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            // Usar toastr se disponível, senão usar alert nativo
            if (typeof toastr !== 'undefined') {
                toastr.error(`❌ ${data.message}`, 'Teste Falhou', {
                    timeOut: 8000,
                    extendedTimeOut: 3000
                });
            } else {
                alert('❌ Teste falhou: ' + data.message);
            }
        }
    })
    .catch(error => {
        console.error('Test error:', error);
        
        const errorMessage = `Erro de conexão: ${error.message}`;
        // Usar toastr se disponível, senão usar alert nativo
        if (typeof toastr !== 'undefined') {
            toastr.error(errorMessage, 'Erro de Conexão', {
                timeOut: 8000,
                extendedTimeOut: 3000
            });
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

function testAllConfigurations() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Testando...';
    btn.disabled = true;
    
    fetch('/admin/ai-configurations/test-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const successCount = data.results.filter(r => r.success).length;
            const totalCount = data.results.length;
            
            if (successCount === totalCount) {
                toastr.success(`Todas as ${totalCount} configurações testadas com sucesso!`);
            } else {
                toastr.warning(`${successCount} de ${totalCount} configurações funcionando`);
            }
            
            // Atualizar a página para mostrar os novos status
            location.reload();
        } else {
            toastr.error(data.message);
        }
    })
    .catch(error => {
        toastr.error('Erro ao testar configurações: ' + error.message);
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function toggleActive(configId) {
    fetch(`/admin/ai-configurations/${configId}/toggle-active`, {
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
    });
}

function resetDailyUsage(configId) {
    fetch(`/admin/ai-configurations/${configId}/reset-usage`, {
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
    });
}

function deleteConfiguration(configId, configName) {
    deleteConfigId = configId;
    document.getElementById('delete-config-name').textContent = configName;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('confirm-delete').addEventListener('click', function() {
    if (!deleteConfigId) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/ai-configurations/${deleteConfigId}`;
    
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
});

function refreshUsageStats() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Atualizando...';
    btn.disabled = true;
    
    fetch('/admin/ai-configurations/usage-stats', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success('Estatísticas atualizadas!');
            location.reload();
        } else {
            toastr.error(data.message);
        }
    })
    .catch(error => {
        toastr.error('Erro ao atualizar estatísticas: ' + error.message);
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}
</script>
@endpush