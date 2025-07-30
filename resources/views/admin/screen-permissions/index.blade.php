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
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Gerenciamento de Permissões</h1>
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
                    <li class="breadcrumb-item text-muted">Permissões</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!-- Mensagens de sucesso/erro -->
            <div id="notification-area"></div>

            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-6">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-security-user fs-1 position-absolute ms-6">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <h3 class="fw-bold m-0 ps-15">Controle de Acesso às Telas</h3>
                        </div>
                    </div>
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end gap-3">
                            <!--begin::Role selector-->
                            <div class="w-250px">
                                <select class="form-select form-select-solid" id="role-selector" data-placeholder="Selecionar Perfil">
                                    <option value="">Selecione um perfil...</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role['value'] }}" data-level="{{ $role['level'] }}">
                                            {{ $role['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Role selector-->
                            
                            <!--begin::Actions-->
                            <button type="button" class="btn btn-primary" id="save-permissions" disabled>
                                <i class="ki-duotone ki-check fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Salvar Alterações
                            </button>
                            
                            <button type="button" class="btn btn-secondary" id="reset-permissions" disabled>
                                <i class="ki-duotone ki-arrows-circle fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Restaurar Padrão
                            </button>
                            
                            <button type="button" class="btn btn-info" id="initialize-permissions">
                                <i class="ki-duotone ki-setting-3 fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                                Inicializar Sistema
                            </button>
                            <!--end::Actions-->
                        </div>
                    </div>
                    <!--end::Card toolbar-->
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body py-4">
                    
                    <!-- Estatísticas do Sistema - Dashboard Cards -->
                    <div class="row g-5 mb-6">
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-info cursor-pointer">
                                <div class="card-header pt-5 pb-3">
                                    <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                        <i class="ki-duotone ki-chart-simple text-white fs-2x">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                    </div>
                                </div>
                                
                                <div class="card-body d-flex flex-column justify-content-end pt-0">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="fs-2hx fw-bold text-white me-2">{{ $cacheStats['hit_ratio'] ?? 0 }}%</span>
                                        <span class="fs-6 fw-semibold text-white opacity-75">hits</span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="fs-6 fw-bold text-white">Cache Hit Ratio</span>
                                        <span class="badge badge-light-info fs-8">{{ $cacheStats['hits'] ?? 0 }}/{{ $cacheStats['total'] ?? 0 }}</span>
                                    </div>
                                    
                                    <div class="progress h-6px bg-white bg-opacity-50">
                                        <div class="progress-bar bg-white" style="width: {{ $cacheStats['hit_ratio'] ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-primary cursor-pointer">
                                <div class="card-header pt-5 pb-3">
                                    <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                        <i class="ki-duotone ki-security-user text-white fs-2x">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                
                                <div class="card-body d-flex flex-column justify-content-end pt-0">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="fs-2hx fw-bold text-white me-2">{{ $statistics['total_permissions'] ?? 0 }}</span>
                                        <span class="fs-6 fw-semibold text-white opacity-75">total</span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="fs-6 fw-bold text-white">Total Permissões</span>
                                        <span class="badge badge-light-primary fs-8">100%</span>
                                    </div>
                                    
                                    <div class="progress h-6px bg-white bg-opacity-50">
                                        <div class="progress-bar bg-white" style="width: 100%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-success cursor-pointer">
                                <div class="card-header pt-5 pb-3">
                                    <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                        <i class="ki-duotone ki-check-circle text-white fs-2x">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                
                                <div class="card-body d-flex flex-column justify-content-end pt-0">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="fs-2hx fw-bold text-white me-2">{{ $statistics['active_permissions'] ?? 0 }}</span>
                                        <span class="fs-6 fw-semibold text-white opacity-75">ativas</span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="fs-6 fw-bold text-white">Permissões Ativas</span>
                                        <span class="badge badge-light-success fs-8">{{ round((($statistics['active_permissions'] ?? 0) / max(($statistics['total_permissions'] ?? 1), 1)) * 100) }}%</span>
                                    </div>
                                    
                                    <div class="progress h-6px bg-white bg-opacity-50">
                                        <div class="progress-bar bg-white" style="width: {{ round((($statistics['active_permissions'] ?? 0) / max(($statistics['total_permissions'] ?? 1), 1)) * 100) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-warning cursor-pointer">
                                <div class="card-header pt-5 pb-3">
                                    <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                        <i class="ki-duotone ki-chart-pie-4 text-white fs-2x">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </div>
                                </div>
                                
                                <div class="card-body d-flex flex-column justify-content-end pt-0">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="fs-2hx fw-bold text-white me-2">{{ $statistics['coverage_percentage'] ?? 0 }}%</span>
                                        <span class="fs-6 fw-semibold text-white opacity-75">cobertura</span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="fs-6 fw-bold text-white">Perfis Configurados</span>
                                        <span class="badge badge-light-warning fs-8">{{ $statistics['coverage_percentage'] ?? 0 }}%</span>
                                    </div>
                                    
                                    <div class="progress h-6px bg-white bg-opacity-50">
                                        <div class="progress-bar bg-white" style="width: {{ $statistics['coverage_percentage'] ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informações do Perfil Selecionado -->
                    <div id="role-info" class="d-none mb-6">
                        <div class="card bg-light-info border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-60px me-5">
                                        <span class="symbol-label bg-info text-white fw-bold fs-1" id="role-initial">A</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h4 class="fw-bold mb-1 text-gray-900" id="role-name">Nome do Perfil</h4>
                                        <p class="text-gray-700 mb-0 fs-6" id="role-description">Descrição do perfil</p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge badge-info fs-7" id="role-level">Nível: 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--begin::Permissions grid-->
                    <div id="permissions-grid" style="display: none;">
                        <div class="d-flex flex-wrap gap-6 justify-content-start">
                            @foreach($modules as $module)
                                <x-permission-card 
                                    :module="$module" 
                                    :show-actions="true"
                                    :readonly="false"
                                    size="default"
                                    theme="light"
                                />
                            @endforeach
                        </div>
                    </div>
                    <!--end::Permissions grid-->

                    <!-- Estado vazio -->
                    <div id="empty-state" class="text-center py-15">
                        <div class="symbol symbol-150px mx-auto mb-8">
                            <span class="symbol-label bg-light-primary text-primary">
                                <i class="ki-duotone ki-security-user fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                        </div>
                        <h3 class="text-gray-800 fw-bold mb-4 fs-2">Selecione um Perfil</h3>
                        <p class="text-gray-500 fs-5 mb-0 mw-500px mx-auto">Escolha um perfil de usuário no menu suspenso acima para configurar quais telas ele terá acesso. As telas selecionadas aparecerão no menu lateral do usuário.</p>
                        
                        <!-- Dica de uso -->
                        <div class="alert alert-primary d-flex align-items-center mt-6 mw-600px mx-auto">
                            <i class="ki-duotone ki-information-5 fs-2hx text-primary me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-column">
                                <h5 class="mb-1">Como funciona?</h5>
                                <span>Marque as telas que o tipo de usuário poderá acessar. Apenas as telas selecionadas aparecerão no menu lateral para usuários desse perfil.</span>
                            </div>
                        </div>
                        
                        <!-- Aviso importante -->
                        <div class="alert alert-warning d-flex align-items-center mt-4 mw-600px mx-auto">
                            <i class="ki-duotone ki-warning-2 fs-2hx text-warning me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-column">
                                <h5 class="mb-1">Importante!</h5>
                                <span>O Dashboard sempre estará habilitado para todos os usuários. Configure as demais permissões para cada perfil liberando as telas necessárias.</span>
                            </div>
                        </div>
                        
                        <!-- Aviso quando não há permissões configuradas -->
                        @if(($statistics['total_permissions'] ?? 0) == 0)
                        <div class="alert alert-info d-flex align-items-center mt-4 mw-600px mx-auto">
                            <i class="ki-duotone ki-information-4 fs-2hx text-info me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-column">
                                <h5 class="mb-1">Sistema Não Inicializado</h5>
                                <span>Clique em "Inicializar Sistema" para configurar automaticamente as permissões básicas (Dashboard para todos os perfis).</span>
                            </div>
                        </div>
                        @endif
                    </div>

                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<style>
/* Dashboard Cards */
.dashboard-card-primary {
    background: linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
}

.dashboard-card-info {
    background: linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
}

.dashboard-card-success {
    background: linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
}

.dashboard-card-warning {
    background: linear-gradient(135deg, #FFC700 0%, #e6b300 100%) !important;
}

.permission-card-container {
    flex: 1 1 380px;
    max-width: 420px;
    min-width: 360px;
}

.permission-card {
    transition: all 0.3s ease;
    border: 1px solid #e4e6ef;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

.permission-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
    border-color: #c8d4e6;
}

.permission-card .card-header {
    background: #ffffff;
    border-bottom: 1px solid #f1f3f6;
}

.permission-item {
    transition: all 0.2s ease;
    border: 1px solid #f1f3f6;
    background: #ffffff;
    position: relative;
}

.permission-item:hover {
    background: #f8f9fa;
    border-color: #e4e6ef;
}

.permission-item:last-child {
    margin-bottom: 0;
}

.permission-actions {
    transition: all 0.3s ease;
    background: #f8f9fa;
    border-top: 1px solid #f1f3f6;
}

.action-btn {
    transition: all 0.2s ease;
    border-radius: 6px;
    font-weight: 500;
    min-width: 80px;
    border: 1px solid transparent;
}

.action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.form-switch .form-check-input {
    width: 2.5rem;
    height: 1.25rem;
    border-radius: 1rem;
    background-color: #e4e6ef;
    border: 1px solid #d1d5db;
    transition: all 0.2s ease;
}

.form-switch .form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

.form-switch .form-check-input:focus {
    box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.1);
}

.progress {
    background-color: #f1f3f6;
    border-radius: 6px;
    overflow: hidden;
}

.progress-bar {
    transition: width 0.4s ease;
}

.symbol-label {
    border-radius: 8px;
    transition: all 0.2s ease;
}

.permission-status .badge {
    transition: all 0.2s ease;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 500;
    padding: 4px 8px;
    border: 1px solid transparent;
}

.permission-status .badge-light-success {
    background-color: #d4edda;
    color: #155724;
    border-color: #c3e6cb;
}

.permission-status .badge-light-danger {
    background-color: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}

.permission-status .badge-light-gray {
    background-color: #f8f9fa;
    color: #6c757d;
    border-color: #e9ecef;
}

.separator {
    height: 1px;
    background-color: #e4e6ef;
    border: none;
}

.separator-dashed {
    border-top: 1px dashed #d1d5db;
    background: none;
}

/* Melhorias na responsividade */
@media (max-width: 768px) {
    .permission-card-container {
        flex: 1 1 100%;
        max-width: 100%;
        min-width: 300px;
    }
    
    .permission-card .card-header {
        padding: 1.5rem 1rem;
    }
    
    .permission-item {
        margin-bottom: 0.75rem;
    }
    
    .action-btn {
        min-width: 70px;
        font-size: 0.8rem;
    }
}

@media (min-width: 769px) and (max-width: 1199px) {
    .permission-card-container {
        flex: 1 1 calc(50% - 12px);
    }
}

@media (min-width: 1200px) {
    .permission-card-container {
        flex: 1 1 calc(33.333% - 16px);
    }
}

@media (min-width: 1600px) {
    .permission-card-container {
        flex: 1 1 calc(25% - 18px);
    }
}

/* Estados de foco melhorados */
.permission-switch:focus,
.action-btn:focus,
.form-select:focus {
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
    outline: none;
}

/* Animações suaves */
.permission-card,
.permission-item,
.action-btn,
.form-switch .form-check-input {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Loading states mais sutis */
.loading {
    opacity: 0.7;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 16px;
    height: 16px;
    margin: -8px 0 0 -8px;
    border: 2px solid #e4e6ef;
    border-top: 2px solid #6c757d;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Admin switches - disabled but visually different */
.permission-switch:disabled {
    opacity: 0.8;
    cursor: not-allowed;
}

.permission-switch:disabled:checked {
    background-color: #198754 !important;
    border-color: #198754 !important;
}

/* Dashboard switch - sempre habilitado */
.permission-switch[data-route="dashboard.index"]:disabled {
    opacity: 1;
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelector = document.getElementById('role-selector');
    const saveBtn = document.getElementById('save-permissions');
    const resetBtn = document.getElementById('reset-permissions');
    const initializeBtn = document.getElementById('initialize-permissions');
    const permissionsGrid = document.getElementById('permissions-grid');
    const emptyState = document.getElementById('empty-state');
    const roleInfo = document.getElementById('role-info');
    const notificationArea = document.getElementById('notification-area');

    let currentPermissions = {};
    let hasChanges = false;

    // Dados dos roles passados do backend
    const roles = @json($roles);
    const modules = @json($modules);

    // Inicializar tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    roleSelector.addEventListener('change', function() {
        const selectedRole = this.value;
        if (selectedRole) {
            loadRolePermissions(selectedRole);
            updateRoleInfo(selectedRole);
            permissionsGrid.style.display = 'block';
            emptyState.style.display = 'none';
            roleInfo.classList.remove('d-none');
            saveBtn.disabled = false;
            resetBtn.disabled = false;
        } else {
            permissionsGrid.style.display = 'none';
            emptyState.style.display = 'block';
            roleInfo.classList.add('d-none');
            saveBtn.disabled = true;
            resetBtn.disabled = true;
        }
    });

    function updateRoleInfo(roleValue) {
        const role = roles.find(r => r.value === roleValue);
        if (role) {
            document.getElementById('role-initial').textContent = role.label.charAt(0);
            document.getElementById('role-name').textContent = role.label;
            document.getElementById('role-description').textContent = role.description;
            document.getElementById('role-level').textContent = `Nível: ${role.level}`;
        }
    }

    function loadRolePermissions(role) {
        showLoading();
        
        // Se for administrador, não precisa carregar do servidor - todas as permissões devem estar ativas
        if (role === 'ADMIN') {
            currentPermissions = {};
            updatePermissionsUI();
            hideLoading();
            return;
        }
        
        fetch(`/admin/screen-permissions/role/${role}`)
            .then(response => response.json())
            .then(data => {
                console.log('Resposta da API:', data);
                if (data.success) {
                    currentPermissions = data.permissions || {};
                    console.log('Permissões carregadas:', currentPermissions);
                    
                    // Garantir que Dashboard sempre esteja nas permissões para todos os perfis
                    if (!currentPermissions.dashboard) {
                        currentPermissions.dashboard = [];
                    }
                    
                    // Forçar Dashboard como habilitado se não estiver presente
                    const dashboardExists = currentPermissions.dashboard.some(perm => perm.screen_route === 'dashboard.index');
                    if (!dashboardExists) {
                        currentPermissions.dashboard.push({
                            screen_route: 'dashboard.index',
                            screen_name: 'Painel Principal',
                            screen_module: 'dashboard',
                            can_access: true
                        });
                    }
                    
                    console.log('Antes de updatePermissionsUI');
                    updatePermissionsUI();
                    console.log('Depois de updatePermissionsUI');
                } else {
                    showNotification('Erro ao carregar permissões', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro de comunicação', 'error');
            })
            .finally(() => {
                console.log('Finally - hideLoading');
                hideLoading();
            });
    }

    function updatePermissionsUI() {
        // Resetar todas as permissões
        document.querySelectorAll('.permission-switch, .permission-action').forEach(input => {
            input.checked = false;
        });

        // Verificar se é administrador
        const selectedRole = roleSelector.value;
        const isAdmin = selectedRole === 'ADMIN';

        if (isAdmin) {
            // Para administrador: habilitar todas as telas por padrão e desabilitar edição
            document.querySelectorAll('.permission-switch[data-action="view"]').forEach(viewSwitch => {
                viewSwitch.checked = true;
                viewSwitch.disabled = true; // Não permitir desabilitar
                const route = viewSwitch.dataset.route;
                updatePermissionStatus(route, true);
            });
            
            // Mostrar mensagem informativa para administrador
            showNotification('Perfil Administrador: Todas as telas estão habilitadas por padrão e não podem ser desabilitadas.', 'info');
            
            // Desabilitar botões para administrador
            saveBtn.disabled = true;
            resetBtn.disabled = true;
        } else {
            // Para outros perfis: habilitar edição e aplicar permissões carregadas
            document.querySelectorAll('.permission-switch[data-action="view"]').forEach(viewSwitch => {
                viewSwitch.disabled = false; // Permitir edição
                
                // Dashboard sempre habilitado por padrão para todos os usuários
                if (viewSwitch.dataset.route === 'dashboard.index') {
                    viewSwitch.checked = true;
                    viewSwitch.disabled = true; // Dashboard não pode ser desabilitado
                    updatePermissionStatus('dashboard.index', true);
                }
            });
            
            // Habilitar botões para outros perfis
            saveBtn.disabled = false;
            resetBtn.disabled = false;
            
            // Para outros perfis: aplicar permissões carregadas
            Object.entries(currentPermissions).forEach(([module, permissions]) => {
                if (permissions && Array.isArray(permissions)) {
                    permissions.forEach(permission => {
                        const route = permission.screen_route;
                        
                        // Marcar switch principal
                        if (permission.can_access) {
                            const viewSwitch = document.querySelector(`[data-route="${route}"][data-action="view"]`);
                            if (viewSwitch) {
                                viewSwitch.checked = true;
                                // Dashboard não pode ser desabilitado para nenhum perfil
                                if (route === 'dashboard.index') {
                                    viewSwitch.disabled = true;
                                }
                                updatePermissionStatus(route, true);
                            }
                        }

                        // Ações específicas removidas - agora só controla acesso às telas
                    });
                }
            });
            
            // Garantir que Dashboard sempre esteja marcado mesmo se não estiver nas permissões carregadas
            const dashboardSwitch = document.querySelector('[data-route="dashboard.index"][data-action="view"]');
            if (dashboardSwitch) {
                if (!dashboardSwitch.checked) {
                    dashboardSwitch.checked = true;
                    dashboardSwitch.disabled = true;
                    updatePermissionStatus('dashboard.index', true);
                }
                console.log('Dashboard switch encontrado e configurado:', dashboardSwitch.checked);
            } else {
                console.error('Dashboard switch não encontrado!');
            }
        }

        // Forçar atualização do progresso após um pequeno delay para garantir que os elementos estejam prontos
        setTimeout(() => {
            updateModuleProgress();
        }, 100);
        
        hasChanges = false;
    }

    // Event listeners para switches de permissão
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('permission-switch')) {
            const route = e.target.dataset.route;
            const isChecked = e.target.checked;
            
            // Verificar se é administrador - não permitir alterações
            const selectedRole = roleSelector.value;
            if (selectedRole === 'ADMIN') {
                e.target.checked = true; // Forçar a permanecer marcado
                return;
            }
            
            // Dashboard não pode ser desmarcado para nenhum perfil
            if (route === 'dashboard.index' && !isChecked) {
                e.target.checked = true; // Forçar Dashboard a permanecer marcado
                showNotification('O Dashboard não pode ser removido. Todos os usuários devem ter acesso ao Dashboard.', 'warning');
                return;
            }
            
            updatePermissionStatus(route, isChecked);
            updateModuleProgress();
            hasChanges = true;
            
            console.log('Mudança de permissão detectada:', route, isChecked);
        }
    });

    // Função togglePermissionActions removida - ações não são mais exibidas

    function updatePermissionStatus(route, hasAccess) {
        const statusEl = document.getElementById(`status-${route}`);
        if (statusEl) {
            const badge = statusEl.querySelector('.badge');
            if (hasAccess) {
                badge.className = 'badge badge-light-success fs-8';
                badge.textContent = 'Ativo';
            } else {
                badge.className = 'badge badge-light-gray fs-8';
                badge.textContent = 'Desabilitado';
            }
        }
    }

    function updateModuleProgress() {
        modules.forEach(module => {
            const moduleValue = module.value;
            const totalRoutes = module.routeCount || Object.keys(module.routes).length;
            let activeRoutes = 0;

            Object.entries(module.routes).forEach(([route, routeName]) => {
                const viewSwitch = document.querySelector(`[data-route="${route}"][data-action="view"]`);
                if (viewSwitch && viewSwitch.checked) {
                    activeRoutes++;
                }
            });

            const percentage = totalRoutes > 0 ? Math.round((activeRoutes / totalRoutes) * 100) : 0;
            
            const percentageEl = document.getElementById(`module-percentage-${moduleValue}`);
            const progressEl = document.getElementById(`module-progress-${moduleValue}`);
            const activeCountEl = document.getElementById(`module-active-count-${moduleValue}`);
            const totalCountEl = document.getElementById(`module-total-count-${moduleValue}`);
            const statusEl = document.getElementById(`module-status-${moduleValue}`);
            
            if (percentageEl) percentageEl.textContent = `${percentage}%`;
            if (progressEl) progressEl.style.width = `${percentage}%`;
            if (activeCountEl) activeCountEl.textContent = activeRoutes;
            if (totalCountEl) totalCountEl.textContent = totalRoutes;
            if (statusEl) {
                if (percentage === 100) {
                    statusEl.textContent = 'Completo';
                    statusEl.className = 'text-success fs-8';
                } else if (percentage > 0) {
                    statusEl.textContent = 'Ativo';
                    statusEl.className = 'text-primary fs-8';
                } else {
                    statusEl.textContent = 'Desabilitado';
                    statusEl.className = 'text-muted fs-8';
                }
            }
        });
    }

    // Salvar permissões
    saveBtn.addEventListener('click', function() {
        if (!hasChanges) {
            showNotification('Nenhuma alteração para salvar', 'warning');
            return;
        }

        const selectedRole = roleSelector.value;
        if (!selectedRole) return;

        const permissions = collectPermissions();
        
        showLoading();
        
        fetch('/admin/screen-permissions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                role: selectedRole,
                permissions: permissions
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Permissões salvas com sucesso! As telas selecionadas agora aparecerão no menu lateral dos usuários deste perfil.', 'success');
                hasChanges = false;
            } else {
                showNotification(data.message || 'Erro ao salvar permissões', 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showNotification('Erro de comunicação', 'error');
        })
        .finally(() => {
            hideLoading();
        });
    });

    // Resetar permissões
    resetBtn.addEventListener('click', function() {
        const selectedRole = roleSelector.value;
        if (!selectedRole) return;

        if (!confirm('Tem certeza que deseja restaurar as permissões padrão? Esta ação não pode ser desfeita.')) {
            return;
        }

        showLoading();
        
        fetch('/admin/screen-permissions/reset', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                role: selectedRole
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Permissões resetadas com sucesso!', 'success');
                loadRolePermissions(selectedRole);
            } else {
                showNotification(data.message || 'Erro ao resetar permissões', 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showNotification('Erro de comunicação', 'error');
        })
        .finally(() => {
            hideLoading();
        });
    });

    // Inicializar sistema de permissões
    initializeBtn.addEventListener('click', function() {
        if (!confirm('Tem certeza que deseja inicializar o sistema de permissões? Isso irá configurar as permissões básicas automaticamente.')) {
            return;
        }

        showLoading();
        
        fetch('/admin/screen-permissions/initialize', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Sistema de permissões inicializado com sucesso!', 'success');
                // Recarregar a página para mostrar as estatísticas atualizadas
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showNotification(data.message || 'Erro ao inicializar sistema', 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showNotification('Erro de comunicação', 'error');
        })
        .finally(() => {
            hideLoading();
        });
    });

    function collectPermissions() {
        const permissions = [];

        modules.forEach(module => {
            Object.entries(module.routes).forEach(([route, routeName]) => {
                const viewSwitch = document.querySelector(`[data-route="${route}"][data-action="view"]`);
                
                // Dashboard sempre habilitado para todos os perfis
                const canAccess = route === 'dashboard.index' ? true : (viewSwitch ? viewSwitch.checked : false);

                permissions.push({
                    screen_route: route,
                    screen_name: routeName,
                    screen_module: module.value,
                    can_access: canAccess,
                    can_create: false, // Sempre false - não usado
                    can_edit: false,   // Sempre false - não usado
                    can_delete: false  // Sempre false - não usado
                });
            });
        });

        return permissions;
    }

    function showNotification(message, type = 'info') {
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'error' ? 'alert-danger' : 
                          type === 'warning' ? 'alert-warning' : 'alert-info';
        
        const notification = `
            <div class="alert ${alertClass} d-flex align-items-center p-5 mb-6">
                <div class="d-flex flex-column">
                    <span>${message}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        notificationArea.innerHTML = notification;
        
        // Auto-remove após 5 segundos
        setTimeout(() => {
            const alert = notificationArea.querySelector('.alert');
            if (alert) alert.remove();
        }, 5000);
    }

    function showLoading() {
        document.querySelectorAll('.permission-card').forEach(card => {
            card.classList.add('loading');
        });
        document.body.style.cursor = 'wait';
    }

    function hideLoading() {
        document.querySelectorAll('.permission-card').forEach(card => {
            card.classList.remove('loading');
        });
        document.body.style.cursor = 'default';
    }
});
</script>
@endpush
@endsection