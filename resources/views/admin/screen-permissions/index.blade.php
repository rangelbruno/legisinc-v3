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
                            <h3 class="fw-bold m-0 ps-15">Sistema de Permissões Avançado</h3>
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
                            <!--end::Actions-->
                        </div>
                    </div>
                    <!--end::Card toolbar-->
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body py-4">
                    
                    <!-- Estatísticas do Cache -->
                    @if(isset($cacheStats))
                    <div class="row g-5 mb-6">
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-flush h-xl-100">
                                <div class="card-body text-center">
                                    <div class="text-gray-800 fw-bold fs-2hx">{{ $cacheStats['hit_ratio'] }}%</div>
                                    <div class="text-gray-400 fw-semibold fs-6">Cache Hit Ratio</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-flush h-xl-100">
                                <div class="card-body text-center">
                                    <div class="text-gray-800 fw-bold fs-2hx">{{ $statistics['total_permissions'] ?? 0 }}</div>
                                    <div class="text-gray-400 fw-semibold fs-6">Total Permissões</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-flush h-xl-100">
                                <div class="card-body text-center">
                                    <div class="text-gray-800 fw-bold fs-2hx">{{ $statistics['active_permissions'] ?? 0 }}</div>
                                    <div class="text-gray-400 fw-semibold fs-6">Permissões Ativas</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-flush h-xl-100">
                                <div class="card-body text-center">
                                    <div class="text-gray-800 fw-bold fs-2hx">{{ $statistics['coverage_percentage'] ?? 0 }}%</div>
                                    <div class="text-gray-400 fw-semibold fs-6">Cobertura</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Informações do Perfil Selecionado -->
                    <div id="role-info" class="d-none mb-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-5">
                                        <span class="symbol-label bg-primary text-white fw-bold" id="role-initial">A</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h4 class="fw-bold mb-1" id="role-name">Nome do Perfil</h4>
                                        <p class="text-muted mb-0" id="role-description">Descrição do perfil</p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge badge-light-primary" id="role-level">Nível: 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--begin::Permissions grid-->
                    <div class="row g-6 g-xl-9" id="permissions-grid" style="display: none;">
                        @foreach($modules as $module)
                            <div class="col-md-6 col-xl-4">
                                <!--begin::Card-->
                                <div class="card h-100">
                                    <!--begin::Card header-->
                                    <div class="card-header">
                                        <div class="card-title d-flex align-items-center">
                                            <i class="{{ $module['iconClass'] }} fs-2x text-{{ $module['color'] }} me-3"></i>
                                            <h3 class="fw-bold m-0">{{ $module['label'] }}</h3>
                                        </div>
                                        <div class="card-toolbar">
                                            <span class="badge badge-light-{{ $module['color'] }}" data-module="{{ $module['value'] }}" id="module-percentage-{{ $module['value'] }}">0%</span>
                                        </div>
                                    </div>
                                    <!--end::Card header-->
                                    
                                    <!--begin::Card body-->
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <div class="progress h-6px bg-light-{{ $module['color'] }}">
                                                <div class="progress-bar bg-{{ $module['color'] }}" id="module-progress-{{ $module['value'] }}" style="width: 0%"></div>
                                            </div>
                                        </div>
                                        
                                        <div class="permissions-list" data-module="{{ $module['value'] }}">
                                            @foreach($module['routes'] as $route => $routeName)
                                                <div class="permission-item mb-3" data-route="{{ $route }}">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="fw-semibold text-gray-800">{{ $routeName }}</span>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input permission-switch" 
                                                                   type="checkbox" 
                                                                   data-route="{{ $route }}" 
                                                                   data-action="view"
                                                                   id="perm_{{ $route }}_view">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="permission-actions" style="display: none;">
                                                        <div class="d-flex gap-2">
                                                            <label class="btn btn-sm btn-outline btn-outline-success btn-active-success">
                                                                <input type="checkbox" class="btn-check permission-action" data-route="{{ $route }}" data-action="create">
                                                                <i class="ki-duotone ki-plus fs-7"></i> C
                                                            </label>
                                                            <label class="btn btn-sm btn-outline btn-outline-warning btn-active-warning">
                                                                <input type="checkbox" class="btn-check permission-action" data-route="{{ $route }}" data-action="edit">
                                                                <i class="ki-duotone ki-pencil fs-7"></i> E
                                                            </label>
                                                            <label class="btn btn-sm btn-outline btn-outline-danger btn-active-danger">
                                                                <input type="checkbox" class="btn-check permission-action" data-route="{{ $route }}" data-action="delete">
                                                                <i class="ki-duotone ki-trash fs-7"></i> D
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <!--end::Card body-->
                                </div>
                                <!--end::Card-->
                            </div>
                        @endforeach
                    </div>
                    <!--end::Permissions grid-->

                    <!-- Estado vazio -->
                    <div id="empty-state" class="text-center py-10">
                        <div class="symbol symbol-100px mx-auto mb-7">
                            <span class="symbol-label bg-light-primary text-primary">
                                <i class="ki-duotone ki-security-user fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                        </div>
                        <h3 class="text-gray-800 fw-bold mb-3">Selecione um Perfil</h3>
                        <p class="text-gray-400 fs-6 mb-0">Escolha um perfil de usuário acima para configurar suas permissões de acesso às telas do sistema.</p>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelector = document.getElementById('role-selector');
    const saveBtn = document.getElementById('save-permissions');
    const resetBtn = document.getElementById('reset-permissions');
    const permissionsGrid = document.getElementById('permissions-grid');
    const emptyState = document.getElementById('empty-state');
    const roleInfo = document.getElementById('role-info');
    const notificationArea = document.getElementById('notification-area');

    let currentPermissions = {};
    let hasChanges = false;

    // Dados dos roles passados do backend
    const roles = @json($roles);
    const modules = @json($modules);

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
        
        fetch(`/admin/screen-permissions/role/${role}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentPermissions = data.permissions || {};
                    updatePermissionsUI();
                } else {
                    showNotification('Erro ao carregar permissões', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro de comunicação', 'error');
            })
            .finally(() => {
                hideLoading();
            });
    }

    function updatePermissionsUI() {
        // Resetar todas as permissões
        document.querySelectorAll('.permission-switch, .permission-action').forEach(input => {
            input.checked = false;
        });

        // Aplicar permissões carregadas
        Object.entries(currentPermissions).forEach(([module, permissions]) => {
            if (permissions && Array.isArray(permissions)) {
                permissions.forEach(permission => {
                    const route = permission.screen_route;
                    
                    // Marcar switch principal
                    if (permission.can_access) {
                        const viewSwitch = document.querySelector(`[data-route="${route}"][data-action="view"]`);
                        if (viewSwitch) {
                            viewSwitch.checked = true;
                            togglePermissionActions(route, true);
                        }
                    }

                    // Marcar ações específicas
                    if (permission.can_create) {
                        const createInput = document.querySelector(`[data-route="${route}"][data-action="create"]`);
                        if (createInput) createInput.checked = true;
                    }
                    if (permission.can_edit) {
                        const editInput = document.querySelector(`[data-route="${route}"][data-action="edit"]`);
                        if (editInput) editInput.checked = true;
                    }
                    if (permission.can_delete) {
                        const deleteInput = document.querySelector(`[data-route="${route}"][data-action="delete"]`);
                        if (deleteInput) deleteInput.checked = true;
                    }
                });
            }
        });

        updateModuleProgress();
        hasChanges = false;
    }

    // Event listeners para switches de permissão
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('permission-switch') || e.target.classList.contains('permission-action')) {
            if (e.target.classList.contains('permission-switch')) {
                const route = e.target.dataset.route;
                const isChecked = e.target.checked;
                
                togglePermissionActions(route, isChecked);
                if (!isChecked) {
                    // Desmarcar todas as ações se o acesso principal for removido
                    document.querySelectorAll(`[data-route="${route}"].permission-action`).forEach(input => {
                        input.checked = false;
                    });
                }
            }
            
            updateModuleProgress();
            hasChanges = true;
            
            console.log('Mudança detectada:', e.target.dataset.route, e.target.dataset.action, e.target.checked);
        }
    });

    function togglePermissionActions(route, show) {
        const actionsDiv = document.querySelector(`[data-route="${route}"] .permission-actions`);
        if (actionsDiv) {
            actionsDiv.style.display = show ? 'block' : 'none';
        }
    }

    function updateModuleProgress() {
        modules.forEach(module => {
            const moduleValue = module.value;
            const totalRoutes = module.routeCount;
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
            
            if (percentageEl) percentageEl.textContent = `${percentage}%`;
            if (progressEl) progressEl.style.width = `${percentage}%`;
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
                showNotification(data.message || 'Permissões salvas com sucesso!', 'success');
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

    function collectPermissions() {
        const permissions = [];

        modules.forEach(module => {
            Object.entries(module.routes).forEach(([route, routeName]) => {
                const viewSwitch = document.querySelector(`[data-route="${route}"][data-action="view"]`);
                const createInput = document.querySelector(`[data-route="${route}"][data-action="create"]`);
                const editInput = document.querySelector(`[data-route="${route}"][data-action="edit"]`);
                const deleteInput = document.querySelector(`[data-route="${route}"][data-action="delete"]`);

                permissions.push({
                    screen_route: route,
                    screen_name: routeName,
                    screen_module: module.value,
                    can_access: viewSwitch ? viewSwitch.checked : false,
                    can_create: createInput ? createInput.checked : false,
                    can_edit: editInput ? editInput.checked : false,
                    can_delete: deleteInput ? deleteInput.checked : false
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
        // Implementar indicador de loading
        document.body.style.cursor = 'wait';
    }

    function hideLoading() {
        document.body.style.cursor = 'default';
    }
});
</script>
@endpush
@endsection