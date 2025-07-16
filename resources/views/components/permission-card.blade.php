@props([
    'module' => [],
    'permissions' => [],
    'showActions' => true,
    'readonly' => false,
    'size' => 'default', // default, small, large
    'theme' => 'light' // light, dark
])

@php
    $cardClasses = [
        'small' => 'permission-card-sm',
        'large' => 'permission-card-lg',
        'default' => ''
    ];
    
    $themeClasses = [
        'light' => 'permission-card-light',
        'dark' => 'permission-card-dark'
    ];
    
    $cardClass = $cardClasses[$size] ?? '';
    $themeClass = $themeClasses[$theme] ?? 'permission-card-light';
@endphp

<div class="permission-card-container">
    <!--begin::Card-->
    <div class="card permission-card h-100 {{ $cardClass }} {{ $themeClass }}" 
         data-module="{{ $module['value'] ?? '' }}"
         data-readonly="{{ $readonly ? 'true' : 'false' }}">
        
        <!--begin::Card header-->
        <div class="card-header border-0 pt-6 pb-4">
            <div class="card-title d-flex align-items-center w-100">
                <div class="d-flex align-items-center flex-grow-1">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-white border border-{{ $module['color'] ?? 'primary' }} text-{{ $module['color'] ?? 'primary' }}">
                            <i class="{{ $module['iconClass'] ?? 'ki-duotone ki-element-11' }} fs-4"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <h3 class="fw-bold mb-1 text-gray-900 fs-5">{{ $module['label'] ?? 'Módulo' }}</h3>
                        <div class="d-flex align-items-center text-gray-500 fs-7">
                            <i class="ki-duotone ki-dots-circle fs-6 me-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <span id="module-active-count-{{ $module['value'] ?? 'default' }}">0</span>
                            <span class="mx-1">/</span>
                            <span id="module-total-count-{{ $module['value'] ?? 'default' }}">{{ count($module['routes'] ?? []) }}</span>
                            <span class="ms-1">permissões</span>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <div class="d-flex flex-column align-items-end">
                        <span class="fw-bold text-{{ $module['color'] ?? 'primary' }} fs-6 mb-1" 
                              data-module="{{ $module['value'] ?? 'default' }}" 
                              id="module-percentage-{{ $module['value'] ?? 'default' }}">0%</span>
                        <div class="text-gray-400 fs-8">ativo</div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Card header-->
        
        <!--begin::Card body-->
        <div class="card-body pt-0">
            <!-- Progress bar -->
            <div class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-gray-700 fs-7 fw-semibold">Progresso</span>
                    <span class="text-gray-500 fs-8" id="module-status-{{ $module['value'] ?? 'default' }}">Configurando...</span>
                </div>
                <div class="progress h-6px bg-gray-200 rounded-pill">
                    <div class="progress-bar bg-{{ $module['color'] ?? 'primary' }} rounded-pill" 
                         id="module-progress-{{ $module['value'] ?? 'default' }}" 
                         style="width: 0%"></div>
                </div>
            </div>
            
            <!-- Custom content slot -->
            @if($slot->isNotEmpty())
                <div class="mb-4">
                    {{ $slot }}
                </div>
            @endif
            
            <!-- Permissions list -->
            <div class="permissions-list" data-module="{{ $module['value'] ?? 'default' }}">
                @if(isset($module['routes']) && is_array($module['routes']))
                    @foreach($module['routes'] as $route => $routeName)
                        <div class="permission-item rounded-2 mb-3" data-route="{{ $route }}">
                            <div class="d-flex justify-content-between align-items-center p-3">
                                <div class="d-flex align-items-center">
                                    <div class="form-check form-switch form-check-custom form-check-solid me-3">
                                        <input class="form-check-input permission-switch" 
                                               type="checkbox" 
                                               data-route="{{ $route }}" 
                                               data-action="view"
                                               id="perm_{{ $route }}_view"
                                               {{ $readonly ? 'disabled' : '' }}>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-gray-800 fs-7">{{ $routeName }}</span>
                                        <span class="text-gray-500 fs-8">{{ $route }}</span>
                                    </div>
                                </div>
                                <div class="permission-status" id="status-{{ $route }}">
                                    <span class="badge badge-light-gray fs-8">Desabilitado</span>
                                </div>
                            </div>
                            
                            {{-- Ações removidas - não precisa mostrar criar, editar, excluir --}}
                            @if(false)
                                <div class="permission-actions px-3 pb-3" style="display: none;">
                                    <div class="separator separator-dashed mb-3"></div>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <label class="btn btn-sm btn-light-success action-btn" title="Criar" data-bs-toggle="tooltip">
                                            <input type="checkbox" class="btn-check permission-action" data-route="{{ $route }}" data-action="create">
                                            <i class="ki-duotone ki-plus fs-6 me-1"></i>
                                            Criar
                                        </label>
                                        <label class="btn btn-sm btn-light-warning action-btn" title="Editar" data-bs-toggle="tooltip">
                                            <input type="checkbox" class="btn-check permission-action" data-route="{{ $route }}" data-action="edit">
                                            <i class="ki-duotone ki-pencil fs-6 me-1"></i>
                                            Editar
                                        </label>
                                        <label class="btn btn-sm btn-light-danger action-btn" title="Excluir" data-bs-toggle="tooltip">
                                            <input type="checkbox" class="btn-check permission-action" data-route="{{ $route }}" data-action="delete">
                                            <i class="ki-duotone ki-trash fs-6 me-1"></i>
                                            Excluir
                                        </label>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-6">
                        <div class="text-gray-500 fs-6">
                            <i class="ki-duotone ki-information fs-2 mb-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <br>
                            Nenhuma permissão encontrada
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
</div>

<style>
/* Card sizes */
.permission-card-sm {
    min-height: 250px;
}

.permission-card-sm .symbol {
    width: 40px;
    height: 40px;
}

.permission-card-lg {
    min-height: 450px;
}

.permission-card-lg .symbol {
    width: 60px;
    height: 60px;
}

/* Theme variations */
.permission-card-dark {
    background: #2a2a2a;
    border-color: #404040;
}

.permission-card-dark .card-header {
    background: #2a2a2a;
    border-bottom-color: #404040;
}

.permission-card-dark .permission-item {
    background: #333333;
    border-color: #404040;
}

.permission-card-dark .permission-actions {
    background: #333333;
    border-top-color: #404040;
}

.permission-card-dark .text-gray-900 {
    color: #ffffff !important;
}

.permission-card-dark .text-gray-800 {
    color: #e0e0e0 !important;
}

.permission-card-dark .text-gray-500 {
    color: #a0a0a0 !important;
}

/* Readonly state */
.permission-card[data-readonly="true"] {
    opacity: 0.7;
}

.permission-card[data-readonly="true"] .permission-switch:disabled {
    cursor: not-allowed;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .permission-card-container {
        flex: 1 1 100%;
        max-width: 100%;
    }
    
    .permission-card-sm .symbol {
        width: 35px;
        height: 35px;
    }
    
    .permission-card-lg .symbol {
        width: 50px;
        height: 50px;
    }
}
</style> 