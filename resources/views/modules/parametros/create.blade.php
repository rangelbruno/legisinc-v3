@extends('components.layouts.app')

@section('title', 'Criar Novo Módulo')

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Criar Novo Módulo
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
                    <li class="breadcrumb-item text-muted">Criar Módulo</li>
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
            <!--begin::Form-->
            <form id="kt_create_module_form" class="form d-flex flex-column flex-lg-row" action="{{ route('parametros.store') }}" method="POST">
                @csrf
                <!--begin::Aside column-->
                <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                    <!--begin::Status-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <h2>Status</h2>
                            </div>
                            <!--end::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar">
                                <div class="rounded-circle bg-success w-15px h-15px" id="kt_create_module_status"></div>
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Select2-->
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="ativo" id="kt_create_module_status_toggle" value="1" checked />
                                <label class="form-check-label" for="kt_create_module_status_toggle">
                                    Módulo ativo
                                </label>
                            </div>
                            <!--end::Select2-->
                            <!--begin::Description-->
                            <div class="text-muted fs-7 mt-3">
                                Defina se o módulo deve estar ativo imediatamente após a criação.
                            </div>
                            <!--end::Description-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Status-->
                    <!--begin::Order-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <h2>Ordem</h2>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Input group-->
                            <div class="fv-row">
                                <input type="number" class="form-control form-control-solid" name="ordem" placeholder="0" min="0" />
                                <div class="text-muted fs-7 mt-2">
                                    Ordem de exibição do módulo (padrão: 0)
                                </div>
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Order-->
                    <!--begin::Template settings-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <h2>Ícone</h2>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Input group-->
                            <div class="fv-row">
                                <input type="text" class="form-control form-control-solid" name="icon" placeholder="ki-setting-2" />
                                <div class="text-muted fs-7 mt-2">
                                    Classe do ícone do Metronic (ex: ki-home, ki-setting-2)
                                </div>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Icon preview-->
                            <div class="mt-5">
                                <label class="form-label">Prévia:</label>
                                <div id="icon-preview" class="d-flex align-items-center">
                                    <i class="ki-duotone ki-setting-2 fs-2x text-primary me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <span class="text-muted">Ícone padrão</span>
                                </div>
                            </div>
                            <!--end::Icon preview-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Template settings-->
                </div>
                <!--end::Aside column-->
                <!--begin::Main column-->
                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                    <!--begin::General options-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Informações Gerais</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="required form-label">Nome do Módulo</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="nome" class="form-control mb-2" placeholder="Ex: Dados da Câmara" value="{{ old('nome') }}" required />
                                <!--end::Input-->
                                <!--begin::Description-->
                                <div class="text-muted fs-7">
                                    Nome que será exibido na interface do sistema.
                                </div>
                                <!--end::Description-->
                                @error('nome')
                                    <div class="text-danger fs-7 mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="form-label">Descrição</label>
                                <!--end::Label-->
                                <!--begin::Editor-->
                                <textarea name="descricao" class="form-control mb-2" rows="5" placeholder="Descrição detalhada do módulo...">{{ old('descricao') }}</textarea>
                                <!--end::Editor-->
                                <!--begin::Description-->
                                <div class="text-muted fs-7">
                                    Descrição que será exibida no card do módulo.
                                </div>
                                <!--end::Description-->
                                @error('descricao')
                                    <div class="text-danger fs-7 mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::General options-->
                    <!--begin::Actions-->
                    <div class="d-flex justify-content-end">
                        <!--begin::Button-->
                        <a href="{{ route('parametros.index') }}" class="btn btn-light me-5">
                            Cancelar
                        </a>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Criar Módulo</span>
                            <span class="indicator-progress">Por favor aguarde...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                        <!--end::Button-->
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Main column-->
            </form>
            <!--end::Form-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Status toggle
            const statusToggle = document.getElementById('kt_create_module_status_toggle');
            const statusIndicator = document.getElementById('kt_create_module_status');
            
            statusToggle.addEventListener('change', function() {
                if (this.checked) {
                    statusIndicator.classList.remove('bg-danger');
                    statusIndicator.classList.add('bg-success');
                } else {
                    statusIndicator.classList.remove('bg-success');
                    statusIndicator.classList.add('bg-danger');
                }
            });
            
            // Icon preview
            const iconInput = document.querySelector('input[name="icon"]');
            const iconPreview = document.getElementById('icon-preview');
            
            iconInput.addEventListener('input', function() {
                const iconClass = this.value || 'ki-setting-2';
                iconPreview.innerHTML = `
                    <i class="ki-duotone ${iconClass} fs-2x text-primary me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <span class="text-muted">${this.value || 'Ícone padrão'}</span>
                `;
            });
            
            // Form validation
            const form = document.getElementById('kt_create_module_form');
            const submitButton = form.querySelector('button[type="submit"]');
            
            form.addEventListener('submit', function(e) {
                // Show loading state
                submitButton.querySelector('.indicator-label').style.display = 'none';
                submitButton.querySelector('.indicator-progress').style.display = 'inline-block';
                submitButton.disabled = true;
            });
        });
    </script>
@endpush