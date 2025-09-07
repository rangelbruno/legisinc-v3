@extends('components.layouts.app')

@section('title', 'Configurações do Sistema')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between py-5">
        <div class="d-flex align-items-center">
            <i class="ki-duotone ki-setting-2 fs-1 text-primary me-3">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            <div>
                <h1 class="mb-0">Configurações do Sistema</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Configurações do Sistema
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-light-primary" onclick="testDebugLogger()">
                <i class="ki-duotone ki-magnifier fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Testar Debug
            </button>
            <button type="button" class="btn btn-light-warning" onclick="clearCache()">
                <i class="ki-duotone ki-broom fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                </i>
                Limpar Cache
            </button>
        </div>
    </div>


    <!-- Configuration Form -->
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center">
                    <i class="ki-duotone ki-gear fs-1 text-primary me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <h3 class="fw-bold m-0">Configurações Gerais</h3>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.system-configuration.update') }}" method="POST" id="systemConfigForm">
            @csrf
            <div class="card-body py-4">
                
                <!-- Debug Logger Configuration -->
                <div class="row">
                    <div class="col-12">
                        <div class="card bg-light-primary">
                            <div class="card-body p-9">
                                <div class="d-flex align-items-center mb-5">
                                    <i class="ki-duotone ki-code fs-2x text-primary me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                    <div class="flex-grow-1">
                                        <h4 class="text-gray-900 mb-1">🔧 Debug Logger</h4>
                                        <div class="text-gray-700 fw-semibold fs-6">
                                            @if($debugLogger['exists'])
                                                {{ $debugLogger['description'] }}
                                            @else
                                                Sistema de debug para capturar ações do usuário em tempo real
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        @if($debugLogger['exists'])
                                            <span class="badge badge-{{ $debugLogger['active'] ? 'success' : 'secondary' }} fs-8 fw-bold me-3">
                                                {{ $debugLogger['active'] ? 'ATIVO' : 'INATIVO' }}
                                            </span>
                                        @endif
                                        
                                        <div class="form-check form-switch form-check-custom form-check-success form-check-solid">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="debug_logger_ativo" 
                                                   id="debug_logger_ativo" 
                                                   {{ $debugLogger['active'] ? 'checked' : '' }}
                                                   @if(!$debugLogger['exists']) disabled @endif>
                                            <label class="form-check-label fw-semibold text-gray-400 ms-3" for="debug_logger_ativo">
                                                @if($debugLogger['exists'])
                                                    {{ $debugLogger['active'] ? 'Desativar' : 'Ativar' }} Debug Logger
                                                @else
                                                    Debug Logger não configurado
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                @if($debugLogger['exists'])
                                <div class="separator separator-dashed my-5"></div>
                                <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6">
                                    <i class="ki-duotone ki-information fs-2tx text-info me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <div class="fw-semibold">
                                            <h4 class="text-gray-900 fw-bold">Como usar o Debug Logger:</h4>
                                            <div class="fs-6 text-gray-700">
                                                <ul class="mb-0">
                                                    <li>Quando ativo, um botão flutuante aparece no canto da tela</li>
                                                    <li>Clique no botão para iniciar/parar a captura de ações</li>
                                                    <li>O sistema registra cliques, navegação, formulários e requisições AJAX</li>
                                                    <li>Use para documentar problemas ou processos do sistema</li>
                                                    <li>Os logs podem ser copiados para análise ou suporte</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                                    <i class="ki-duotone ki-warning fs-2tx text-warning me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <div class="fw-semibold">
                                            <h4 class="text-gray-900 fw-bold">Configuração Necessária</h4>
                                            <div class="fs-6 text-gray-700">
                                                O parâmetro Debug Logger precisa ser configurado no sistema modular de parâmetros.
                                                <br>Acesse <a href="{{ route('parametros.index') }}" class="link-primary">Dados Gerais > Sistema</a> para configurar.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Future configurations can be added here -->
                <div class="separator separator-dashed my-10"></div>
                
                <div class="notice d-flex bg-light-secondary rounded border-secondary border border-dashed p-6">
                    <i class="ki-duotone ki-rocket fs-2tx text-secondary me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <h4 class="text-gray-900 fw-bold">Mais configurações em breve</h4>
                            <div class="fs-6 text-gray-700">
                                Esta página será expandida com mais opções de configuração do sistema conforme necessário.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <button type="button" class="btn btn-light me-3" onclick="window.history.back()">
                    <i class="ki-duotone ki-arrow-left fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Voltar
                </button>
                
                @if($debugLogger['exists'])
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="ki-duotone ki-check fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Salvar Configurações
                </button>
                @endif
            </div>
        </form>
    </div>
</div>

<script>
// Test Debug Logger functionality
async function testDebugLogger() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Testando...';
    btn.disabled = true;
    
    try {
        const response = await fetch('{{ route("admin.system-configuration.test-debug-logger") }}');
        const data = await response.json();
        
        showToast(data.message, data.active ? 'success' : 'info');
        
    } catch (error) {
        showToast('Erro ao testar Debug Logger: ' + error.message, 'error');
    }
    
    btn.innerHTML = originalText;
    btn.disabled = false;
}

// Clear system cache
async function clearCache() {
    if (!confirm('Tem certeza que deseja limpar todo o cache do sistema?')) {
        return;
    }
    
    const btn = event.target;
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Limpando...';
    btn.disabled = true;
    
    try {
        const response = await fetch('{{ route("admin.system-configuration.clear-cache") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
        
    } catch (error) {
        showToast('Erro ao limpar cache: ' + error.message, 'error');
    }
    
    btn.innerHTML = originalText;
    btn.disabled = false;
}

// Show toast function
function showToast(message, type = 'success') {
    const toastType = type === 'success' ? 'success' : 'danger';
    const icon = type === 'success' ? 'ki-check' : 'ki-cross';
    const title = type === 'success' ? 'Sucesso!' : 'Erro!';
    
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toastr-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    
    if (type === 'success') {
        toastr.success(message, title);
    } else {
        toastr.error(message, title);
    }
}

// Show session messages as toasts
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        showToast('{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        showToast('{{ session('error') }}', 'error');
    @endif
});

// Form submission with loading state
document.getElementById('systemConfigForm').addEventListener('submit', function() {
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Salvando...';
        submitBtn.disabled = true;
    }
});
</script>
@endsection