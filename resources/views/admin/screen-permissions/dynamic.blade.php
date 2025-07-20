@extends('components.layouts.app')

@section('title', 'Gerenciamento de Permissões')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Gerenciamento de Permissões
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Administração</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Permissões Dinâmicas</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button id="initialize-btn" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-setting-3 fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                        <span class="path5"></span>
                    </i>
                    Inicializar Sistema
                </button>
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

            <!--begin::Statistics-->
            <div class="row g-5 g-xl-8 mb-xl-8">
                <div class="col-xl-3">
                    <div class="card card-xl-stretch mb-xl-8">
                        <div class="card-body d-flex align-items-center pt-3 pb-0">
                            <div class="d-flex flex-column flex-grow-1 py-2 py-lg-13 me-2">
                                <a href="#" class="fw-bold text-dark fs-4 mb-2 text-hover-primary">
                                    {{ $statistics['total_routes'] }}
                                </a>
                                <div class="fw-semibold text-muted fs-5">Rotas Detectadas</div>
                            </div>
                            <i class="ki-duotone ki-route fs-4x opacity-30">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="card card-xl-stretch mb-xl-8">
                        <div class="card-body d-flex align-items-center pt-3 pb-0">
                            <div class="d-flex flex-column flex-grow-1 py-2 py-lg-13 me-2">
                                <a href="#" class="fw-bold text-dark fs-4 mb-2 text-hover-primary">
                                    {{ $statistics['active_permissions'] }}
                                </a>
                                <div class="fw-semibold text-muted fs-5">Permissões Ativas</div>
                            </div>
                            <i class="ki-duotone ki-check-circle fs-4x opacity-30">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="card card-xl-stretch mb-xl-8">
                        <div class="card-body d-flex align-items-center pt-3 pb-0">
                            <div class="d-flex flex-column flex-grow-1 py-2 py-lg-13 me-2">
                                <a href="#" class="fw-bold text-dark fs-4 mb-2 text-hover-primary">
                                    {{ $roles->count() }}
                                </a>
                                <div class="fw-semibold text-muted fs-5">Tipos de Usuário</div>
                            </div>
                            <i class="ki-duotone ki-profile-user fs-4x opacity-30">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="card card-xl-stretch mb-xl-8">
                        <div class="card-body d-flex align-items-center pt-3 pb-0">
                            <div class="d-flex flex-column flex-grow-1 py-2 py-lg-13 me-2">
                                <a href="#" class="fw-bold text-dark fs-4 mb-2 text-hover-primary">
                                    {{ $statistics['coverage_percentage'] }}%
                                </a>
                                <div class="fw-semibold text-muted fs-5">Cobertura</div>
                            </div>
                            <i class="ki-duotone ki-chart-simple fs-4x opacity-30">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Statistics-->

            <!--begin::Role Selection-->
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-security-user fs-1 position-absolute ms-6">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <h3 class="fw-bold m-0 ps-15">Selecionar Tipo de Usuário</h3>
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end">
                            <span class="text-muted fs-7">Clique em um tipo para configurar suas permissões</span>
                        </div>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="row g-6 g-xl-9">
                        @foreach($roles as $role)
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="card card-flush h-md-100 role-card" data-role="{{ $role['name'] }}" style="cursor: pointer;">
                                <div class="card-header">
                                    <div class="card-title d-flex flex-column">
                                        <div class="d-flex align-items-center">
                                            @php
                                                $iconMap = [
                                                    'ADMIN' => 'crown',
                                                    'PARLAMENTAR' => 'profile-user',
                                                    'LEGISLATIVO' => 'document',
                                                    'PROTOCOLO' => 'folder'
                                                ];
                                                $icon = $iconMap[$role['name']] ?? 'user';
                                                
                                                $colorMap = [
                                                    'ADMIN' => 'danger',
                                                    'PARLAMENTAR' => 'primary',
                                                    'LEGISLATIVO' => 'success',
                                                    'PROTOCOLO' => 'info'
                                                ];
                                                $color = $colorMap[$role['name']] ?? 'dark';
                                            @endphp
                                            <i class="ki-duotone ki-{{ $icon }} fs-2hx text-{{ $color }} me-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                @if($icon === 'profile-user')
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                                @endif
                                            </i>
                                            <div class="d-flex flex-column">
                                                <div class="fw-bold text-gray-800 fs-5">{{ $role['label'] }}</div>
                                                <div class="fs-7 fw-semibold text-muted">{{ $role['name'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="text-gray-600 fw-semibold fs-6 mb-4">{{ $role['description'] }}</div>
                                    
                                    @php
                                        $currentPermissions = $current[$role['name']] ?? [];
                                        $permissionCount = count($currentPermissions);
                                    @endphp
                                    
                                    <div class="d-flex align-items-center flex-wrap d-grid gap-2">
                                        <div class="d-flex align-items-center me-5 mb-2">
                                            <div class="symbol symbol-30px me-4">
                                                <span class="symbol-label bg-light-{{ $color }}">
                                                    <i class="ki-duotone ki-security-check fs-3 text-{{ $color }}">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </span>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <div class="fw-bold fs-6">{{ $permissionCount }}</div>
                                                <div class="fs-7 text-muted">Permissões</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex flex-stack">
                                        <button class="btn btn-sm btn-light btn-active-primary configure-btn" data-role="{{ $role['name'] }}">
                                            <i class="ki-duotone ki-setting-2 fs-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Configurar
                                        </button>
                                        <button class="btn btn-sm btn-light-{{ $color }} apply-defaults-btn" data-role="{{ $role['name'] }}">
                                            <i class="ki-duotone ki-arrows-circle fs-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Padrão
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <!--end::Role Selection-->

            <!--begin::Permission Configuration Panel-->
            <div id="permission-panel" class="card" style="display: none;">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-setting-3 fs-1 position-absolute ms-6">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                            <div class="ps-15">
                                <h3 class="fw-bold m-0">
                                    Configurando: <span id="current-role-name" class="text-primary"></span>
                                </h3>
                                <div class="fs-7 text-muted" id="current-role-description"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end gap-3">
                            <button id="save-permissions-btn" class="btn btn-sm fw-bold btn-primary">
                                <i class="ki-duotone ki-check fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Salvar Alterações
                            </button>
                            <button id="close-panel-btn" class="btn btn-sm btn-light">
                                <i class="ki-duotone ki-cross fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Fechar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div id="modules-container">
                        @foreach($modules as $moduleKey => $module)
                        <div class="card card-flush mb-6 module-card" data-module="{{ $moduleKey }}">
                            <div class="card-header">
                                <div class="card-title">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-element-11 fs-2hx text-primary me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <div class="fw-bold text-gray-800 fs-5">{{ $module['name'] }}</div>
                                            <div class="fs-7 fw-semibold text-muted">
                                                <span class="active-count">0</span>/<span class="total-count">{{ $module['routes']->count() }}</span> ativas
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-toolbar">
                                    <button class="btn btn-sm btn-light btn-active-light-primary toggle-all-btn" data-module="{{ $moduleKey }}">
                                        <i class="ki-duotone ki-check-square fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        Marcar Todas
                                    </button>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="table-responsive">
                                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                        <thead>
                                            <tr class="fw-bold text-muted">
                                                <th class="min-w-150px">Tela</th>
                                                <th class="min-w-140px">Rota</th>
                                                <th class="min-w-100px text-end">Acesso</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($module['routes'] as $routeKey => $route)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="d-flex flex-column">
                                                            <span class="text-dark fw-bold text-hover-primary fs-6">
                                                                {{ $route['name'] }}
                                                            </span>
                                                            <span class="text-muted fw-semibold text-muted d-block fs-7">
                                                                {{ $route['action'] ?? 'Visualizar' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted fw-semibold text-muted d-block fs-7">
                                                        {{ $routeKey }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                                        <input class="form-check-input permission-switch" 
                                                               type="checkbox" 
                                                               id="perm-{{ str_replace('.', '-', $routeKey) }}"
                                                               data-route="{{ $routeKey }}"
                                                               data-module="{{ $moduleKey }}">
                                                        <label class="form-check-label" for="perm-{{ str_replace('.', '-', $routeKey) }}"></label>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <!--end::Permission Configuration Panel-->

            <!--begin::User Test Section-->
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-user-tick fs-1 position-absolute ms-6">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <h3 class="fw-bold m-0 ps-15">Testar Permissões de Usuário</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="test-user-id" class="form-label fw-semibold text-dark">ID do Usuário:</label>
                                <div class="input-group">
                                    <input type="number" class="form-control form-control-solid" 
                                           id="test-user-id" placeholder="Digite o ID do usuário para testar">
                                    <button class="btn btn-primary" id="test-user-btn">
                                        <i class="ki-duotone ki-magnifier fs-2">
                                            <span class="path1"></span>
                                        </i>
                                        Testar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" id="test-results" style="display: none;">
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark">Resultado do Teste:</label>
                                <div id="test-output"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::User Test Section-->

        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<style>
.role-card {
    transition: all 0.3s ease;
}
.role-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}
.role-card.active {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 1px var(--bs-primary);
}
.permission-switch {
    --bs-form-switch-width: 2.5rem;
    --bs-form-switch-height: 1.25rem;
}
.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Toast Styles */
.toast-container {
    z-index: 9999 !important;
}

.toast {
    min-width: 350px;
    backdrop-filter: blur(10px);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.toast.text-bg-success {
    --bs-toast-bg: rgba(25, 135, 84, 0.95);
}

.toast.text-bg-danger {
    --bs-toast-bg: rgba(220, 53, 69, 0.95);
}

.toast.text-bg-warning {
    --bs-toast-bg: rgba(255, 193, 7, 0.95);
    --bs-toast-color: #000;
}

.toast.text-bg-primary {
    --bs-toast-bg: rgba(13, 110, 253, 0.95);
}

.toast-header {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(5px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.toast-body {
    font-weight: 500;
}

/* Animação de entrada para toasts */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast.show {
    animation: slideInRight 0.3s ease-out;
}

/* Melhorar alertas no container também */
.alert {
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.alert.alert-success {
    background-color: rgba(25, 135, 84, 0.1);
    border-color: rgba(25, 135, 84, 0.3);
    color: #0f5132;
}

.alert.alert-danger {
    background-color: rgba(220, 53, 69, 0.1);
    border-color: rgba(220, 53, 69, 0.3);
    color: #842029;
}

.alert.alert-warning {
    background-color: rgba(255, 193, 7, 0.1);
    border-color: rgba(255, 193, 7, 0.3);
    color: #664d03;
}

.alert.alert-primary {
    background-color: rgba(13, 110, 253, 0.1);
    border-color: rgba(13, 110, 253, 0.3);
    color: #084298;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM carregado, inicializando script de permissões');
    
    let currentRole = null;
    let currentPermissions = {};
    let hasChanges = false;

    // Dados do backend
    const roles = @json($roles);
    const modules = @json($modules);
    const defaults = @json($defaults);
    const current = @json($current);
    
    console.log('Dados carregados do backend:');
    console.log('Roles:', roles);
    console.log('Modules:', modules);
    console.log('Current permissions:', current);

    // Elementos
    const permissionPanel = document.getElementById('permission-panel');
    const saveBtn = document.getElementById('save-permissions-btn');
    const closePanelBtn = document.getElementById('close-panel-btn');
    const initializeBtn = document.getElementById('initialize-btn');
    const testUserBtn = document.getElementById('test-user-btn');
    
    console.log('Elementos encontrados:');
    console.log('permissionPanel:', permissionPanel);
    console.log('Botões configure encontrados:', document.querySelectorAll('.configure-btn').length);

    // Event Listeners
    document.querySelectorAll('.configure-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const role = e.target.closest('button').dataset.role;
            console.log('Configurar clicado para role:', role);
            configureRole(role);
        });
    });

    document.querySelectorAll('.apply-defaults-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const role = e.target.closest('button').dataset.role;
            applyDefaults(role);
        });
    });

    document.querySelectorAll('.role-card').forEach(card => {
        card.addEventListener('click', (e) => {
            if (!e.target.closest('button')) {
                const role = card.dataset.role;
                configureRole(role);
            }
        });
    });

    closePanelBtn.addEventListener('click', closePanel);
    saveBtn.addEventListener('click', savePermissions);
    initializeBtn.addEventListener('click', initializeSystem);
    testUserBtn.addEventListener('click', testUser);

    // Configurar role
    function configureRole(role) {
        console.log('configureRole chamado com:', role);
        console.log('roles disponíveis:', roles);
        
        currentRole = role;
        // O objeto roles é um objeto, não array. Vamos acessar diretamente
        const roleData = roles[role];
        
        console.log('roleData encontrado:', roleData);
        
        if (!roleData) {
            console.error('Role não encontrado:', role);
            showAlert('Erro: Tipo de usuário não encontrado', 'danger');
            return;
        }
        
        console.log('Atualizando elementos da UI...');
        
        document.getElementById('current-role-name').textContent = roleData.label;
        document.getElementById('current-role-description').textContent = roleData.description;
        
        console.log('Nomes atualizados, atualizando cards visuais...');
        
        // Atualizar cards visuais
        document.querySelectorAll('.role-card').forEach(card => {
            card.classList.remove('active');
        });
        document.querySelector(`[data-role="${role}"]`).classList.add('active');
        
        console.log('Cards atualizados, carregando permissões...');
        
        // Carregar permissões atuais
        loadRolePermissions(role);
        
        console.log('Mostrando painel...');
        permissionPanel.style.display = 'block';
        permissionPanel.scrollIntoView({ behavior: 'smooth' });
        
        console.log('configureRole concluído');
    }

    // Carregar permissões de um role
    function loadRolePermissions(role) {
        console.log('loadRolePermissions chamado para:', role);
        showLoading(true);
        
        const url = `/admin/screen-permissions/role/${role}`;
        console.log('Fazendo fetch para URL:', url);
        
        fetch(url)
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Dados recebidos:', data);
                if (data.success) {
                    currentPermissions = {};
                    data.permissions.forEach(perm => {
                        currentPermissions[perm] = true;
                    });
                    console.log('Permissões carregadas:', currentPermissions);
                    updatePermissionUI();
                } else {
                    console.error('Erro no backend:', data.message);
                    showAlert('Erro ao carregar permissões: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Erro de fetch:', error);
                showAlert('Erro de comunicação ao carregar permissões', 'danger');
            })
            .finally(() => {
                showLoading(false);
            });
    }

    // Atualizar interface com permissões
    function updatePermissionUI() {
        // Resetar todos os switches
        document.querySelectorAll('.permission-switch').forEach(sw => {
            sw.checked = false;
            sw.disabled = false;
        });

        // Aplicar permissões carregadas
        Object.keys(currentPermissions).forEach(route => {
            const switchElement = document.querySelector(`[data-route="${route}"]`);
            if (switchElement) {
                switchElement.checked = true;
            }
        });

        // Dashboard sempre ativo (exceto se for configuração específica)
        const dashboardSwitch = document.querySelector('[data-route="dashboard"]');
        if (dashboardSwitch && currentRole !== 'ADMIN') {
            dashboardSwitch.checked = true;
            dashboardSwitch.disabled = true;
        }

        // Se for ADMIN, todas as permissões ativas
        if (currentRole === 'ADMIN') {
            document.querySelectorAll('.permission-switch').forEach(sw => {
                sw.checked = true;
                sw.disabled = true;
            });
        }

        updateModuleCounts();
        hasChanges = false;
    }

    // Event listener para switches
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('permission-switch')) {
            const route = e.target.dataset.route;
            const isChecked = e.target.checked;

            // Dashboard não pode ser desmarcado (exceto admin)
            if (route === 'dashboard' && !isChecked && currentRole !== 'ADMIN') {
                e.target.checked = true;
                showAlert('O Dashboard é obrigatório e não pode ser removido. Todos os usuários devem ter acesso ao painel principal.', 'warning');
                return;
            }

            // Admin não pode ter permissões alteradas
            if (currentRole === 'ADMIN') {
                e.target.checked = true;
                return;
            }

            // Atualizar estado
            if (isChecked) {
                currentPermissions[route] = true;
            } else {
                delete currentPermissions[route];
            }

            updateModuleCounts();
            hasChanges = true;
        }
    });

    // Toggle all em um módulo
    document.querySelectorAll('.toggle-all-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const module = e.target.closest('button').dataset.module;
            const switches = document.querySelectorAll(`[data-module="${module}"] .permission-switch`);
            const allChecked = Array.from(switches).every(sw => sw.checked);
            
            switches.forEach(sw => {
                if (currentRole === 'ADMIN') return;
                if (sw.dataset.route === 'dashboard') return;
                
                sw.checked = !allChecked;
                const route = sw.dataset.route;
                
                if (sw.checked) {
                    currentPermissions[route] = true;
                } else {
                    delete currentPermissions[route];
                }
            });
            
            updateModuleCounts();
            hasChanges = true;
        });
    });

    // Atualizar contadores dos módulos
    function updateModuleCounts() {
        document.querySelectorAll('.module-card').forEach(card => {
            const module = card.dataset.module;
            const switches = card.querySelectorAll('.permission-switch');
            const activeCount = Array.from(switches).filter(sw => sw.checked).length;
            const totalCount = switches.length;
            
            card.querySelector('.active-count').textContent = activeCount;
            card.querySelector('.total-count').textContent = totalCount;
            
            const toggleBtn = card.querySelector('.toggle-all-btn');
            const allChecked = Array.from(switches).every(sw => sw.checked);
            toggleBtn.innerHTML = allChecked ? 
                '<i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i> Desmarcar Todas' : 
                '<i class="ki-duotone ki-check-square fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> Marcar Todas';
        });
    }

    // Salvar permissões
    function savePermissions() {
        console.log('savePermissions chamado');
        console.log('hasChanges:', hasChanges);
        console.log('currentRole:', currentRole);
        console.log('currentPermissions:', currentPermissions);
        
        if (!hasChanges) {
            showAlert('Nenhuma alteração foi detectada para salvar.', 'warning');
            return;
        }

        // Contar permissões ativas
        const activePermissions = Object.keys(currentPermissions).length;
        
        console.log('Iniciando processo de salvamento...');
        showLoading(true);
        
        const payload = {
            role: currentRole,
            permissions: currentPermissions
        };
        
        console.log('Payload a ser enviado:', payload);
        
        fetch('/admin/screen-permissions/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            console.log('Response recebida, status:', response.status);
            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Data recebida:', data);
            if (data.success) {
                // Mensagem personalizada baseada no número de permissões
                let message;
                if (activePermissions === 0) {
                    message = `Todas as permissões foram removidas para ${currentRole}. O usuário terá acesso apenas ao Dashboard.`;
                    showAlert(message, 'warning');
                } else if (activePermissions === 1 && currentPermissions['dashboard']) {
                    message = `Configurado acesso apenas ao Dashboard para ${currentRole}.`;
                    showAlert(message, 'info');
                } else {
                    message = `${activePermissions} permissões foram configuradas para ${currentRole}. As telas selecionadas agora aparecerão no menu dos usuários.`;
                    showAlert(message, 'success');
                }
                
                hasChanges = false;
                
                // Atualizar contador na interface
                updateRolePermissionCount(currentRole, activePermissions);
            } else {
                showAlert('Erro ao salvar permissões: ' + (data.message || 'Erro desconhecido'), 'danger');
            }
        })
        .catch(error => {
            console.error('Erro de fetch:', error);
            if (error.message.includes('HTTP: 500')) {
                showAlert('Erro interno do servidor. Verifique os logs do sistema.', 'danger');
            } else if (error.message.includes('HTTP: 422')) {
                showAlert('Dados inválidos enviados. Verifique as permissões selecionadas.', 'danger');
            } else {
                showAlert('Erro de comunicação: ' + error.message, 'danger');
            }
        })
        .finally(() => {
            console.log('Finalizando processo de salvamento...');
            showLoading(false);
        });
    }

    // Atualizar contador de permissões no card do role
    function updateRolePermissionCount(roleName, count) {
        try {
            const roleCard = document.querySelector(`[data-role="${roleName}"]`);
            if (roleCard) {
                const countElement = roleCard.querySelector('.fw-bold.fs-6');
                if (countElement) {
                    countElement.textContent = count;
                }
            }
        } catch (e) {
            console.warn('Não foi possível atualizar contador do role:', e);
        }
    }

    // Aplicar padrões
    function applyDefaults(role) {
        if (!confirm(`Aplicar permissões padrão para ${role}? Isso substituirá as configurações atuais.`)) {
            return;
        }

        showLoading(true);
        
        fetch('/admin/screen-permissions/apply-defaults', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ role: role })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showAlert(`Permissões padrão aplicadas para ${role} com sucesso!`, 'success');
                if (currentRole === role) {
                    loadRolePermissions(role);
                }
            } else {
                showAlert('Erro ao aplicar permissões padrão: ' + (data.message || 'Erro desconhecido'), 'danger');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showAlert('Erro de comunicação ao aplicar permissões padrão.', 'danger');
        })
        .finally(() => {
            showLoading(false);
        });
    }

    // Inicializar sistema
    function initializeSystem() {
        if (!confirm('Inicializar sistema com permissões padrão para todos os perfis?')) {
            return;
        }

        showLoading(true);
        
        fetch('/admin/screen-permissions/initialize', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showAlert('Sistema inicializado com sucesso! A página será recarregada em 2 segundos.', 'success');
                setTimeout(() => window.location.reload(), 2000);
            } else {
                showAlert('Erro ao inicializar sistema: ' + (data.message || 'Erro desconhecido'), 'danger');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showAlert('Erro de comunicação ao inicializar o sistema.', 'danger');
        })
        .finally(() => {
            showLoading(false);
        });
    }

    // Testar usuário
    function testUser() {
        const userId = document.getElementById('test-user-id').value;
        if (!userId) {
            showAlert('Digite um ID de usuário válido', 'warning');
            return;
        }

        showLoading(true);

        fetch('/admin/screen-permissions/test-user', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ user_id: userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayTestResults(data);
            } else {
                showAlert('Erro: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showAlert('Erro de comunicação ao testar usuário', 'danger');
        })
        .finally(() => {
            showLoading(false);
        });
    }

    // Exibir resultados do teste
    function displayTestResults(data) {
        let html = `
            <div class="alert alert-primary d-flex align-items-center p-5">
                <i class="ki-duotone ki-shield-tick fs-2hx text-primary me-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-dark">${data.user.name}</h4>
                    <span>${data.user.email} - Roles: ${data.user.roles.join(', ')}</span>
                </div>
            </div>
            <div class="mt-4">
                <h6 class="fw-bold text-gray-800">Menu disponível:</h6>
        `;

        Object.entries(data.menu).forEach(([module, moduleData]) => {
            html += `
                <div class="card card-flush mb-3">
                    <div class="card-header pt-4">
                        <div class="card-title">
                            <h6 class="fw-bold text-gray-800">${moduleData.name}</h6>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex flex-wrap gap-2">
            `;
            Object.entries(moduleData.routes).forEach(([route, name]) => {
                html += `<span class="badge badge-light-primary">${name}</span>`;
            });
            html += `
                        </div>
                    </div>
                </div>
            `;
        });

        html += `</div>`;
        document.getElementById('test-output').innerHTML = html;
        document.getElementById('test-results').style.display = 'block';
    }

    // Fechar painel
    function closePanel() {
        if (hasChanges && !confirm('Há alterações não salvas. Deseja realmente fechar?')) {
            return;
        }
        permissionPanel.style.display = 'none';
        document.querySelectorAll('.role-card').forEach(card => {
            card.classList.remove('active');
        });
        currentRole = null;
        hasChanges = false;
    }

    // Mostrar loading
    function showLoading(show) {
        document.body.style.cursor = show ? 'wait' : 'default';
        if (show) {
            document.body.classList.add('loading');
        } else {
            document.body.classList.remove('loading');
        }
    }

    // Mostrar alerta usando toasts elegantes
    function showAlert(message, type = 'info') {
        console.log('Mostrando alerta:', message, type);
        
        // Criar toast container se não existir
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        
        // Definir configurações do toast baseado no tipo
        const toastConfig = {
            'success': { 
                class: 'text-bg-success', 
                icon: 'ki-check-circle',
                title: 'Sucesso!'
            },
            'danger': { 
                class: 'text-bg-danger', 
                icon: 'ki-cross-circle',
                title: 'Erro!'
            },
            'warning': { 
                class: 'text-bg-warning', 
                icon: 'ki-information',
                title: 'Atenção!'
            },
            'info': { 
                class: 'text-bg-primary', 
                icon: 'ki-information-5',
                title: 'Informação'
            }
        };
        
        const config = toastConfig[type] || toastConfig['info'];
        const toastId = 'toast-' + Date.now();
        
        // Criar HTML do toast
        const toastHtml = `
            <div id="${toastId}" class="toast ${config.class}" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
                <div class="toast-header">
                    <i class="ki-duotone ${config.icon} fs-2 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <strong class="me-auto">${config.title}</strong>
                    <small class="text-muted">agora</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;
        
        // Adicionar ao container
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        // Inicializar e mostrar o toast
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement);
        
        // Remover do DOM quando o toast for ocultado
        toastElement.addEventListener('hidden.bs.toast', function() {
            this.remove();
        });
        
        // Mostrar o toast
        toast.show();
        
        // Log para debug (sem usar alertContainer que causa loops)
        console.log(`Toast ${type}: ${message}`);
    }
});
</script>
@endpush
@endsection