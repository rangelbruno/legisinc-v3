@extends('components.layouts.app')

@section('title', 'Parâmetros do Sistema')

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Parâmetros do Sistema
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
                    <li class="breadcrumb-item text-muted">Administração</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Parâmetros</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!--begin::Secondary button-->
                <a href="#" class="btn btn-sm btn-flex btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#kt_modal_export">
                    <i class="ki-duotone ki-exit-down fs-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Exportar
                </a>
                <!--end::Secondary button-->
                <!--begin::JSON Extract button-->
                <a href="#" class="btn btn-sm btn-flex btn-info" data-bs-toggle="modal" data-bs-target="#kt_modal_extract_json">
                    <i class="ki-duotone ki-code fs-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                    Extrair JSON
                </a>
                <!--end::JSON Extract button-->
                <!--begin::Primary button-->
                <a href="{{ route('parametros.create') }}" class="btn btn-sm btn-primary">
                    <i class="ki-duotone ki-plus fs-3"></i>
                    Novo Módulo
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
            
            <!--begin::Migration Alert-->
            <div class="alert alert-warning d-flex align-items-center p-5 mb-10">
                <i class="ki-duotone ki-information fs-2hx text-warning me-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <div class="d-flex flex-column">
                    <h5 class="mb-1">Sistema Migrado</h5>
                    <span>Este sistema foi migrado para o novo Sistema de Parâmetros Modulares. 
                        <a href="{{ route('parametros.index') }}" class="fw-bold text-warning text-hover-primary">Acesse o novo sistema aqui</a>.
                    </span>
                </div>
            </div>
            <!--end::Migration Alert-->

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
                    <i class="ki-duotone ki-information fs-2hx text-warning me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h5 class="mb-1">Aviso</h5>
                        <span>{{ session('warning') }}</span>
                    </div>
                </div>
            @endif
            <!--end::Alert-->

            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-6">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <!--begin::Search-->
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" data-kt-parametros-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Buscar parâmetros..." />
                        </div>
                        <!--end::Search-->
                    </div>
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar">
                        <!--begin::Toolbar-->
                        <div class="d-flex justify-content-end" data-kt-parametros-table-toolbar="base">
                            <!--begin::Filter-->
                            <div class="d-flex align-items-center me-3">
                                <!--begin::Filter by group-->
                                <div class="me-3">
                                    <select class="form-select form-select-solid" data-kt-parametros-table-filter="grupo">
                                        <option value="">Todos os Grupos</option>
                                        @foreach($grupos ?? [] as $grupo)
                                            <option value="{{ $grupo->id }}">{{ $grupo->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!--end::Filter by group-->
                                <!--begin::Filter by type-->
                                <div class="me-3">
                                    <select class="form-select form-select-solid" data-kt-parametros-table-filter="tipo">
                                        <option value="">Todos os Tipos</option>
                                        @foreach($tipos ?? [] as $tipo)
                                            <option value="{{ $tipo->id }}">{{ $tipo->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!--end::Filter by type-->
                                <!--begin::View mode-->
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-icon btn-active-color-primary active" data-kt-view-mode="grid" title="Visualização em grade">
                                        <i class="ki-duotone ki-category fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-kt-view-mode="list" title="Visualização em lista">
                                        <i class="ki-duotone ki-menu fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </button>
                                </div>
                                <!--end::View mode-->
                            </div>
                            <!--end::Filter-->
                        </div>
                        <!--end::Toolbar-->
                    </div>
                    <!--end::Card toolbar-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    
                    <!--begin::Grid view-->
                    <div id="kt_parametros_grid_view" style="display: block;">
                        <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                            <!--begin::Empty state-->
                            <div class="col-12">
                                <div class="d-flex flex-center flex-column py-20">
                                    <div class="text-center">
                                        <i class="ki-duotone ki-setting-2 fs-4x text-primary mb-5">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <h3 class="text-gray-800 mb-2">Sistema de Parâmetros</h3>
                                        <p class="text-gray-600 fs-6 mb-6">
                                            O sistema de parâmetros foi implementado com sucesso!<br>
                                            Execute as migrations e seeders para visualizar os dados.
                                        </p>
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ route('parametros.create') }}" class="btn btn-primary">
                                                <i class="ki-duotone ki-plus fs-3"></i>
                                                Criar Primeiro Módulo
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Empty state-->
                        </div>
                    </div>
                    <!--end::Grid view-->
                    
                    <!--begin::List view-->
                    <div id="kt_parametros_list_view" style="display: none;">
                        <!--begin::Table-->
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_parametros_table">
                            <thead>
                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-125px">Nome</th>
                                    <th class="min-w-100px">Código</th>
                                    <th class="min-w-100px">Grupo</th>
                                    <th class="min-w-100px">Tipo</th>
                                    <th class="min-w-150px">Valor</th>
                                    <th class="min-w-70px">Status</th>
                                    <th class="text-end min-w-100px">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                <tr>
                                    <td colspan="7" class="text-center py-10">
                                        <div class="text-gray-600">
                                            <i class="ki-duotone ki-information-5 fs-2x mb-3"></i>
                                            <p>Execute as migrations e seeders para visualizar os parâmetros.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!--end::Table-->
                    </div>
                    <!--end::List view-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->

    <!--begin::Modal - Export-->
    <div class="modal fade" id="kt_modal_export" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Exportar Parâmetros</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-parametros-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <form id="kt_modal_export_form">
                        <div class="fv-row mb-10">
                            <label class="required fs-6 fw-semibold form-label mb-2">Grupo</label>
                            <select class="form-select form-select-solid" data-control="select2" data-placeholder="Selecione um grupo" data-allow-clear="true" name="grupo_id">
                                <option></option>
                                <option value="">Todos os Grupos</option>
                                @foreach($grupos ?? [] as $grupo)
                                    <option value="{{ $grupo->id }}">{{ $grupo->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-center">
                            <button type="reset" class="btn btn-light me-3" data-kt-parametros-modal-action="cancel">Cancelar</button>
                            <button type="submit" class="btn btn-primary" data-kt-parametros-modal-action="submit">
                                <span class="indicator-label">Exportar</span>
                                <span class="indicator-progress">Por favor aguarde...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - Export-->

    <!--begin::Modal - Extract JSON-->
    <div class="modal fade" id="kt_modal_extract_json" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-750px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Extrair Módulos em JSON</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-extract-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <form id="kt_modal_extract_form">
                        <div class="fv-row mb-10">
                            <label class="fs-6 fw-semibold form-label mb-2">Módulo Específico</label>
                            <select class="form-select form-select-solid" data-control="select2" data-placeholder="Selecione um módulo (deixe vazio para todos)" data-allow-clear="true" name="modulo_id" id="extract_modulo_id">
                                <option></option>
                                <option value="">Todos os Módulos</option>
                                <!-- Módulos serão carregados dinamicamente -->
                            </select>
                            <div class="form-text">Deixe vazio para extrair todos os módulos do sistema</div>
                        </div>
                        
                        <div class="fv-row mb-10">
                            <label class="fs-6 fw-semibold form-label mb-2">Tipo de Extração</label>
                            <div class="row mb-5">
                                <div class="col-md-6">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="radio" value="true" name="simples" id="simples_sim" checked>
                                        <label class="form-check-label" for="simples_sim">
                                            <strong>Apenas Campos</strong>
                                            <div class="text-muted fs-7">Lista simplificada dos campos e seu uso</div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="radio" value="false" name="simples" id="simples_nao">
                                        <label class="form-check-label" for="simples_nao">
                                            <strong>Dados Completos</strong>
                                            <div class="text-muted fs-7">Estrutura completa com todas as configurações</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="fv-row mb-10">
                            <label class="fs-6 fw-semibold form-label mb-2">Formato de Saída</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="radio" value="json" name="formato" id="formato_json" checked>
                                        <label class="form-check-label" for="formato_json">
                                            Visualizar JSON
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="radio" value="download" name="formato" id="formato_download">
                                        <label class="form-check-label" for="formato_download">
                                            Download Arquivo
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="separator separator-dashed my-7"></div>

                        <div class="fv-row mb-7">
                            <h6 class="fw-bold text-gray-800 mb-3">O que será extraído:</h6>
                            <ul class="list-unstyled">
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check-square fs-5 text-success me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Estrutura completa dos módulos e submódulos
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check-square fs-5 text-success me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Configurações e validações de cada campo
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check-square fs-5 text-success me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Valores atuais e histórico de uso
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check-square fs-5 text-success me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Estatísticas de utilização por módulo
                                </li>
                                <li class="d-flex align-items-center">
                                    <i class="ki-duotone ki-check-square fs-5 text-success me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Identificação de campos não utilizados
                                </li>
                            </ul>
                        </div>

                        <div class="text-center">
                            <button type="reset" class="btn btn-light me-3" data-kt-extract-modal-action="cancel">Cancelar</button>
                            <button type="submit" class="btn btn-primary" data-kt-extract-modal-action="submit">
                                <span class="indicator-label">Extrair Dados</span>
                                <span class="indicator-progress">Por favor aguarde...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - Extract JSON-->

    <!--begin::Modal - JSON Result-->
    <div class="modal fade" id="kt_modal_json_result" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Dados Extraídos - JSON</h2>
                    <div class="d-flex">
                        <button type="button" class="btn btn-sm btn-light me-3" id="copy_json_btn">
                            <i class="ki-duotone ki-copy fs-3"></i>
                            Copiar
                        </button>
                        <button type="button" class="btn btn-sm btn-primary me-3" id="download_json_btn">
                            <i class="ki-duotone ki-exit-down fs-3"></i>
                            Download
                        </button>
                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-json-modal-action="close">
                            <i class="ki-duotone ki-cross fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <pre id="json_result_content" class="bg-light p-5 rounded" style="max-height: 70vh; overflow-y: auto;"><code></code></pre>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - JSON Result-->
@endsection

@push('scripts')
    <script>
        // Básico para funcionalidade
        document.addEventListener('DOMContentLoaded', function() {
            // View mode toggle
            const viewModeButtons = document.querySelectorAll('[data-kt-view-mode]');
            const gridView = document.getElementById('kt_parametros_grid_view');
            const listView = document.getElementById('kt_parametros_list_view');
            
            viewModeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const mode = this.getAttribute('data-kt-view-mode');
                    
                    // Update active state
                    viewModeButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Toggle views
                    if (mode === 'grid') {
                        gridView.style.display = 'block';
                        listView.style.display = 'none';
                    } else {
                        gridView.style.display = 'none';
                        listView.style.display = 'block';
                    }
                });
            });
            
            // Modal close - Export
            document.querySelectorAll('[data-kt-parametros-modal-action="close"], [data-kt-parametros-modal-action="cancel"]').forEach(button => {
                button.addEventListener('click', function() {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('kt_modal_export'));
                    if (modal) {
                        modal.hide();
                    }
                });
            });

            // Modal close - Extract JSON
            document.querySelectorAll('[data-kt-extract-modal-action="close"], [data-kt-extract-modal-action="cancel"]').forEach(button => {
                button.addEventListener('click', function() {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('kt_modal_extract_json'));
                    if (modal) {
                        modal.hide();
                    }
                });
            });

            // Modal close - JSON Result
            document.querySelectorAll('[data-kt-json-modal-action="close"]').forEach(button => {
                button.addEventListener('click', function() {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('kt_modal_json_result'));
                    if (modal) {
                        modal.hide();
                    }
                });
            });

            // Load modules for extract select
            loadModulesForExtract();

            // Extract form submission
            document.getElementById('kt_modal_extract_form').addEventListener('submit', function(e) {
                e.preventDefault();
                handleExtractSubmission();
            });

            // Copy JSON button
            document.getElementById('copy_json_btn').addEventListener('click', function() {
                const content = document.querySelector('#json_result_content code').textContent;
                navigator.clipboard.writeText(content).then(function() {
                    toastr.success('JSON copiado para a área de transferência!');
                });
            });

            // Download JSON button
            document.getElementById('download_json_btn').addEventListener('click', function() {
                const content = document.querySelector('#json_result_content code').textContent;
                const filename = 'parametros_' + new Date().toISOString().slice(0,10) + '.json';
                downloadJSON(content, filename);
            });
        });

        // Load modules for extract dropdown
        function loadModulesForExtract() {
            fetch('/api/parametros-modular/modulos/')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('extract_modulo_id');
                    // Clear existing options except first two
                    while (select.children.length > 2) {
                        select.removeChild(select.lastChild);
                    }
                    
                    if (data && Array.isArray(data)) {
                        data.forEach(modulo => {
                            const option = document.createElement('option');
                            option.value = modulo.id;
                            option.textContent = modulo.nome;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar módulos:', error);
                });
        }

        // Handle extract form submission
        function handleExtractSubmission() {
            const form = document.getElementById('kt_modal_extract_form');
            const formData = new FormData(form);
            const submitBtn = form.querySelector('[data-kt-extract-modal-action="submit"]');
            
            // Show loading state
            submitBtn.setAttribute('data-kt-indicator', 'on');
            submitBtn.disabled = true;

            // Build query parameters
            const params = new URLSearchParams();
            if (formData.get('modulo_id')) {
                params.append('modulo_id', formData.get('modulo_id'));
            }
            params.append('formato', formData.get('formato'));
            params.append('simples', formData.get('simples'));

            fetch(`/api/parametros-modular/modulos/extrair-json?${params.toString()}`)
                .then(response => {
                    if (formData.get('formato') === 'download') {
                        // Handle file download
                        return response.blob().then(blob => {
                            const filename = response.headers.get('Content-Disposition')?.match(/filename="(.+)"/)?.[1] || 'parametros.json';
                            downloadBlob(blob, filename);
                            
                            // Hide extract modal
                            const extractModal = bootstrap.Modal.getInstance(document.getElementById('kt_modal_extract_json'));
                            extractModal.hide();
                            
                            toastr.success('Arquivo baixado com sucesso!');
                            return null;
                        });
                    } else {
                        // Handle JSON display
                        return response.json();
                    }
                })
                .then(data => {
                    if (data) {
                        // Display JSON in modal
                        const jsonContent = JSON.stringify(data, null, 2);
                        document.querySelector('#json_result_content code').textContent = jsonContent;
                        
                        // Hide extract modal and show result modal
                        const extractModal = bootstrap.Modal.getInstance(document.getElementById('kt_modal_extract_json'));
                        extractModal.hide();
                        
                        const resultModal = new bootstrap.Modal(document.getElementById('kt_modal_json_result'));
                        resultModal.show();
                    }
                })
                .catch(error => {
                    console.error('Erro ao extrair dados:', error);
                    
                    // Tenta extrair mais informações do erro
                    if (error.response) {
                        error.response.json().then(errorData => {
                            console.error('Detalhes do erro:', errorData);
                            toastr.error('Erro: ' + (errorData.erro || 'Erro desconhecido'));
                        }).catch(() => {
                            toastr.error('Erro ao extrair dados. Tente novamente.');
                        });
                    } else {
                        toastr.error('Erro ao extrair dados. Verifique a conexão.');
                    }
                })
                .finally(() => {
                    // Hide loading state
                    submitBtn.removeAttribute('data-kt-indicator');
                    submitBtn.disabled = false;
                });
        }

        // Download JSON as file
        function downloadJSON(content, filename) {
            const blob = new Blob([content], { type: 'application/json' });
            downloadBlob(blob, filename);
        }

        // Download blob as file
        function downloadBlob(blob, filename) {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        }
    </script>
@endpush