@extends('components.layouts.app')

@section('title', 'Atribuição de Permissões de Telas')

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
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Atribuição de Permissões de Telas</h1>
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
                    <li class="breadcrumb-item text-muted">Permissões de Telas</li>
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
            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-success">Sucesso!</h4>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

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
                            <h3 class="fw-bold m-0 ps-15">Configurar Permissões por Perfil</h3>
                        </div>
                    </div>
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                            <!--begin::Role selector-->
                            <div class="w-300px">
                                <select class="form-select form-select-solid" id="role-selector">
                                    @foreach($roles as $roleKey => $roleName)
                                        <option value="{{ $roleKey }}" {{ $selectedRole === $roleKey ? 'selected' : '' }}>
                                            {{ $roleName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Role selector-->
                        </div>
                    </div>
                    <!--end::Card toolbar-->
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body py-4">
                    <form id="permissions-form" method="POST" action="{{ route('admin.screen-permissions.update') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="role_name" value="{{ $selectedRole }}">

                        <!--begin::Permissions grid-->
                        <div class="row g-6 g-xl-9">
                            @foreach($screens as $moduleKey => $module)
                                <!--begin::Col-->
                                <div class="col-md-6 col-lg-6 col-xl-4">
                                    <!--begin::Card-->
                                    <div class="card card-flush h-100 mb-5 mb-xl-10">
                                        <!--begin::Header-->
                                        <div class="card-header pt-5 pb-3">
                                            <!--begin::Title-->
                                            <div class="card-title d-flex flex-column w-100">
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Icon-->
                                                    <div class="symbol symbol-35px me-3">
                                                        <span class="symbol-label bg-light-primary">
                                                            @if($moduleKey === 'dashboard')
                                                                <i class="ki-duotone ki-element-11 fs-2x text-primary">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                    <span class="path3"></span>
                                                                    <span class="path4"></span>
                                                                </i>
                                                            @elseif($moduleKey === 'parlamentares')
                                                                <i class="ki-duotone ki-people fs-2x text-primary">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                    <span class="path3"></span>
                                                                    <span class="path4"></span>
                                                                    <span class="path5"></span>
                                                                </i>
                                                            @elseif($moduleKey === 'comissoes')
                                                                <i class="ki-duotone ki-handshake fs-2x text-primary">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                            @elseif($moduleKey === 'projetos')
                                                                <i class="ki-duotone ki-document fs-2x text-primary">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                            @elseif($moduleKey === 'sessoes')
                                                                <i class="ki-duotone ki-calendar fs-2x text-primary">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                            @elseif($moduleKey === 'usuarios')
                                                                <i class="ki-duotone ki-user fs-2x text-primary">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                            @elseif($moduleKey === 'modelos')
                                                                <i class="ki-duotone ki-copy fs-2x text-primary">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                    <span class="path3"></span>
                                                                    <span class="path4"></span>
                                                                    <span class="path5"></span>
                                                                </i>
                                                            @else
                                                                <i class="ki-duotone ki-setting-2 fs-2x text-primary">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <!--end::Icon-->
                                                    <!--begin::Info-->
                                                    <div class="d-flex flex-column flex-grow-1">
                                                        <div class="fs-5 fw-bold text-gray-900">{{ $module['name'] }}</div>
                                                        <div class="fs-7 fw-semibold text-gray-500">
                                                            @php
                                                                $totalScreens = 0;
                                                                $activeScreens = 0;
                                                                
                                                                if(isset($module['route'])) {
                                                                    $totalScreens++;
                                                                    if(isset($currentPermissions[$module['route']]) && $currentPermissions[$module['route']]['can_access']) {
                                                                        $activeScreens++;
                                                                    }
                                                                }
                                                                
                                                                if(isset($module['children'])) {
                                                                    foreach($module['children'] as $screen) {
                                                                        $totalScreens++;
                                                                        if(isset($currentPermissions[$screen['route']]) && $currentPermissions[$screen['route']]['can_access']) {
                                                                            $activeScreens++;
                                                                        }
                                                                    }
                                                                }
                                                            @endphp
                                                            {{ $activeScreens }}/{{ $totalScreens }} permissões ativas
                                                        </div>
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Badge-->
                                                    @if($activeScreens > 0)
                                                        <div class="badge badge-light-success fs-8">
                                                            {{ round(($activeScreens / $totalScreens) * 100) }}%
                                                        </div>
                                                    @else
                                                        <div class="badge badge-light-danger fs-8">
                                                            0%
                                                        </div>
                                                    @endif
                                                    <!--end::Badge-->
                                                </div>
                                            </div>
                                            <!--end::Title-->
                                        </div>
                                        <!--end::Header-->

                                        <!--begin::Body-->
                                        <div class="card-body pt-2 pb-4">
                                            <!--begin::Permissions list-->
                                            <div class="fv-row">
                                                @if(isset($module['route']))
                                                    <!--begin::Main screen permission-->
                                                    <div class="d-flex align-items-center py-2 border-bottom border-gray-300 mb-2">
                                                        <label class="form-check form-switch form-check-custom form-check-solid flex-grow-1">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="permissions[{{ $module['route'] }}]" 
                                                                   value="1"
                                                                   {{ isset($currentPermissions[$module['route']]) && $currentPermissions[$module['route']]['can_access'] ? 'checked' : '' }}>
                                                            <span class="form-check-label text-gray-800 fs-6 fw-semibold">{{ $module['name'] }}</span>
                                                        </label>
                                                    </div>
                                                    <!--end::Main screen permission-->
                                                @endif

                                                @if(isset($module['children']))
                                                    @foreach($module['children'] as $screenKey => $screen)
                                                        <!--begin::Child screen permission-->
                                                        <div class="d-flex align-items-center py-2">
                                                            <label class="form-check form-switch form-check-custom form-check-solid flex-grow-1">
                                                                <input class="form-check-input" type="checkbox" 
                                                                       name="permissions[{{ $screen['route'] }}]" 
                                                                       value="1"
                                                                       {{ isset($currentPermissions[$screen['route']]) && $currentPermissions[$screen['route']]['can_access'] ? 'checked' : '' }}>
                                                                <span class="form-check-label text-gray-700 fs-7">{{ $screen['name'] }}</span>
                                                            </label>
                                                        </div>
                                                        <!--end::Child screen permission-->
                                                    @endforeach
                                                @endif
                                            </div>
                                            <!--end::Permissions list-->
                                        </div>
                                        <!--end::Body-->
                                    </div>
                                    <!--end::Card-->
                                </div>
                                <!--end::Col-->
                            @endforeach
                        </div>
                        <!--end::Permissions grid-->

                        <!--begin::Actions-->
                        <div class="card-footer d-flex justify-content-end py-6 px-9">
                            <button type="button" class="btn btn-light btn-active-light-primary me-2" id="reset-permissions">
                                <i class="ki-duotone ki-arrows-circle fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Resetar para Padrão
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ki-duotone ki-check fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Salvar Permissões
                            </button>
                        </div>
                        <!--end::Actions-->
                    </form>
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

<!--begin::Modal - Reset Permissions-->
<div class="modal fade" id="kt_modal_reset_permissions" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-550px">
        <div class="modal-content">
            <form id="reset-form" action="{{ route('admin.screen-permissions.reset') }}" method="POST">
                @csrf
                <input type="hidden" name="role_name" value="{{ $selectedRole }}">
                
                <div class="modal-header" id="kt_modal_reset_permissions_header">
                    <h2 class="fw-bold">Resetar Permissões</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <div class="fw-semibold fs-6 text-gray-600 mb-7">
                        Tem certeza de que deseja resetar as permissões do perfil <span class="fw-bold text-gray-900">{{ $roles[$selectedRole] ?? $selectedRole }}</span> para as configurações padrão?
                    </div>
                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                        <i class="ki-duotone ki-information fs-2tx text-warning me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-6 text-gray-700">
                                    Esta ação irá remover todas as personalizações feitas para este perfil e aplicar as permissões padrão do sistema.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light me-3" data-kt-users-modal-action="cancel">Cancelar</button>
                    <button type="submit" class="btn btn-warning" data-kt-users-modal-action="submit">
                        <span class="indicator-label">Resetar</span>
                        <span class="indicator-progress">Por favor aguarde...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Role selector change handler
    const roleSelector = document.getElementById('role-selector');
    const permissionsForm = document.getElementById('permissions-form');
    const resetForm = document.getElementById('reset-form');
    
    roleSelector.addEventListener('change', function() {
        const selectedRole = this.value;
        
        // Update form action
        permissionsForm.querySelector('input[name="role_name"]').value = selectedRole;
        resetForm.querySelector('input[name="role_name"]').value = selectedRole;
        
        // Reload page with new role
        window.location.href = '{{ route("admin.screen-permissions.index") }}?role=' + selectedRole;
    });
    
    // Reset permissions modal
    const resetButton = document.getElementById('reset-permissions');
    const resetModal = new bootstrap.Modal(document.getElementById('kt_modal_reset_permissions'));
    
    resetButton.addEventListener('click', function() {
        resetModal.show();
    });
    
    // Modal close handlers
    document.querySelectorAll('[data-kt-users-modal-action="close"]').forEach(function(element) {
        element.addEventListener('click', function() {
            resetModal.hide();
        });
    });
    
    document.querySelectorAll('[data-kt-users-modal-action="cancel"]').forEach(function(element) {
        element.addEventListener('click', function() {
            resetModal.hide();
        });
    });
    
    // Form submission indicator
    document.querySelectorAll('[data-kt-users-modal-action="submit"]').forEach(function(element) {
        element.addEventListener('click', function() {
            const button = this;
            const indicator = button.querySelector('.indicator-label');
            const progress = button.querySelector('.indicator-progress');
            
            button.setAttribute('data-kt-indicator', 'on');
            button.disabled = true;
            
            indicator.style.display = 'none';
            progress.style.display = 'inline-block';
        });
    });
});
</script>
@endpush