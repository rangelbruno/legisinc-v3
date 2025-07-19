@extends('components.layouts.app')

@section('title', 'Módulo: ' . $modulo->nome)

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <i class="ki-duotone {{ $modulo->icon ?: 'ki-setting-2' }} fs-2 me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ $modulo->nome }}
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
                    <li class="breadcrumb-item text-muted">{{ $modulo->nome }}</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!--begin::Secondary button-->
                <a href="{{ route('parametros.edit', $modulo->id) }}" class="btn btn-sm btn-flex btn-secondary me-2">
                    <i class="ki-duotone ki-pencil fs-3"></i>
                    Editar Módulo
                </a>
                <!--end::Secondary button-->
                <!--begin::JSON Export button-->
                <button type="button" class="btn btn-sm btn-flex btn-info" id="btn_export_json" data-modulo-id="{{ $modulo->id }}">
                    <i class="ki-duotone ki-code fs-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                    Exportar JSON
                </button>
                <!--end::JSON Export button-->
                <!--begin::Primary button-->
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_submodule">
                    <i class="ki-duotone ki-plus fs-3"></i>
                    Novo Submódulo
                </button>
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
            <!--end::Alert-->

            <!--begin::Module info-->
            <div class="card mb-5 mb-xl-10">
                <div class="card-body pt-9 pb-0">
                    <!--begin::Details-->
                    <div class="d-flex flex-wrap flex-sm-nowrap">
                        <!--begin::Image-->
                        <div class="me-7 mb-4">
                            <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                                <div class="symbol-label fs-2 fw-semibold text-success">
                                    <i class="ki-duotone {{ $modulo->icon ?: 'ki-setting-2' }} fs-2x">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>
                        </div>
                        <!--end::Image-->
                        <!--begin::Info-->
                        <div class="flex-grow-1">
                            <!--begin::Title-->
                            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">{{ $modulo->nome }}</span>
                                        <span class="badge badge-light-{{ $modulo->ativo ? 'success' : 'danger' }}">
                                            {{ $modulo->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </div>
                                    <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                        <span class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                            <i class="ki-duotone ki-profile-circle fs-4 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            {{ $submodulos->count() }} submódulos
                                        </span>
                                        <span class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                            <i class="ki-duotone ki-geolocation fs-4 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Ordem: {{ $modulo->ordem }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!--end::Title-->
                            <!--begin::Stats-->
                            <div class="d-flex flex-wrap flex-stack">
                                <div class="d-flex flex-column flex-grow-1 pe-8">
                                    <div class="d-flex flex-wrap">
                                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="ki-duotone ki-arrow-up fs-3 text-success me-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <div class="fs-2 fw-bold counted">{{ $submodulos->where('ativo', true)->count() }}</div>
                                            </div>
                                            <div class="fw-semibold fs-6 text-gray-500">Submódulos Ativos</div>
                                        </div>
                                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="ki-duotone ki-arrow-down fs-3 text-danger me-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <div class="fs-2 fw-bold counted">{{ $submodulos->where('ativo', false)->count() }}</div>
                                            </div>
                                            <div class="fw-semibold fs-6 text-gray-500">Submódulos Inativos</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Stats-->
                        </div>
                        <!--end::Info-->
                    </div>
                    <!--end::Details-->
                    <!--begin::Navs-->
                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5 active" data-bs-toggle="tab" href="#kt_submodules_tab">
                                Submódulos
                            </a>
                        </li>
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#kt_settings_tab">
                                Configurações
                            </a>
                        </li>
                    </ul>
                    <!--end::Navs-->
                </div>
            </div>
            <!--end::Module info-->

            <!--begin::Tab content-->
            <div class="tab-content">
                <!--begin::Tab pane-->
                <div class="tab-pane fade show active" id="kt_submodules_tab">
                    <!--begin::Row-->
                    <div class="row g-6 g-xl-9">
                        @forelse($submodulos as $submodulo)
                            <!--begin::Col-->
                            <div class="col-md-6 col-lg-4">
                                <!--begin::Card-->
                                <div class="card card-flush h-xl-100">
                                    <!--begin::Card header-->
                                    <div class="card-header pt-5">
                                        <!--begin::Card title-->
                                        <div class="card-title d-flex flex-column">
                                            <h3 class="fs-5 fw-bold text-gray-900 mb-1">{{ $submodulo->nome }}</h3>
                                            <span class="text-gray-500 fs-7">
                                                Tipo: {{ ucfirst($submodulo->tipo) }}
                                            </span>
                                        </div>
                                        <!--end::Card title-->
                                        <!--begin::Card toolbar-->
                                        <div class="card-toolbar">
                                            <span class="badge badge-light-{{ $submodulo->ativo ? 'success' : 'danger' }}">
                                                {{ $submodulo->ativo ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </div>
                                        <!--end::Card toolbar-->
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body d-flex flex-column">
                                        <!--begin::Description-->
                                        <div class="flex-grow-1 mb-5">
                                            <p class="text-gray-700 fs-6 mb-0">
                                                {{ $submodulo->descricao ?: 'Sem descrição disponível' }}
                                            </p>
                                        </div>
                                        <!--end::Description-->
                                        <!--begin::Actions-->
                                        <div class="d-flex justify-content-end">
                                            <a href="#" class="btn btn-sm btn-light me-2">
                                                Editar
                                            </a>
                                            <a href="#" class="btn btn-sm btn-primary">
                                                Configurar
                                            </a>
                                        </div>
                                        <!--end::Actions-->
                                    </div>
                                    <!--end::Card body-->
                                </div>
                                <!--end::Card-->
                            </div>
                            <!--end::Col-->
                        @empty
                            <!--begin::Empty state-->
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body d-flex flex-center flex-column py-20">
                                        <div class="text-center">
                                            <i class="ki-duotone ki-element-11 fs-4x text-primary mb-5">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                            </i>
                                            <h3 class="text-gray-800 mb-2">Nenhum submódulo encontrado</h3>
                                            <p class="text-gray-600 fs-6 mb-6">
                                                Crie seu primeiro submódulo para começar a estruturar os parâmetros.
                                            </p>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_submodule">
                                                <i class="ki-duotone ki-plus fs-3"></i>
                                                Criar Primeiro Submódulo
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Empty state-->
                        @endforelse
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Tab pane-->
                <!--begin::Tab pane-->
                <div class="tab-pane fade" id="kt_settings_tab">
                    <!--begin::Form-->
                    <form class="form">
                        <!--begin::Card-->
                        <div class="card card-flush">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="fw-bold">Configurações do Módulo</h3>
                                </div>
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body">
                                <!--begin::Form group-->
                                <div class="fv-row mb-8">
                                    <label class="fs-6 fw-semibold form-label mb-2">Nome</label>
                                    <input type="text" class="form-control form-control-solid" value="{{ $modulo->nome }}" readonly />
                                </div>
                                <!--end::Form group-->
                                <!--begin::Form group-->
                                <div class="fv-row mb-8">
                                    <label class="fs-6 fw-semibold form-label mb-2">Descrição</label>
                                    <textarea class="form-control form-control-solid" rows="3" readonly>{{ $modulo->descricao }}</textarea>
                                </div>
                                <!--end::Form group-->
                                <!--begin::Form group-->
                                <div class="row">
                                    <div class="col-md-6 fv-row mb-8">
                                        <label class="fs-6 fw-semibold form-label mb-2">Ícone</label>
                                        <input type="text" class="form-control form-control-solid" value="{{ $modulo->icon }}" readonly />
                                    </div>
                                    <div class="col-md-6 fv-row mb-8">
                                        <label class="fs-6 fw-semibold form-label mb-2">Ordem</label>
                                        <input type="number" class="form-control form-control-solid" value="{{ $modulo->ordem }}" readonly />
                                    </div>
                                </div>
                                <!--end::Form group-->
                                <!--begin::Form group-->
                                <div class="fv-row mb-8">
                                    <label class="fs-6 fw-semibold form-label mb-2">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" {{ $modulo->ativo ? 'checked' : '' }} disabled />
                                        <label class="form-check-label">
                                            Módulo {{ $modulo->ativo ? 'ativo' : 'inativo' }}
                                        </label>
                                    </div>
                                </div>
                                <!--end::Form group-->
                                <!--begin::Form group-->
                                <div class="fv-row">
                                    <label class="fs-6 fw-semibold form-label mb-2">Criado em</label>
                                    <input type="text" class="form-control form-control-solid" value="{{ $modulo->created_at->format('d/m/Y H:i:s') }}" readonly />
                                </div>
                                <!--end::Form group-->
                            </div>
                            <!--end::Card body-->
                            <!--begin::Card footer-->
                            <div class="card-footer d-flex justify-content-end py-6 px-9">
                                <a href="{{ route('parametros.edit', $modulo->id) }}" class="btn btn-primary">
                                    <i class="ki-duotone ki-pencil fs-3"></i>
                                    Editar Módulo
                                </a>
                            </div>
                            <!--end::Card footer-->
                        </div>
                        <!--end::Card-->
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Tab pane-->
            </div>
            <!--end::Tab content-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->

    <!--begin::Modal - Create Submodule-->
    <div class="modal fade" id="kt_modal_create_submodule" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Criar Novo Submódulo</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <form id="kt_modal_create_submodule_form">
                        @csrf
                        <input type="hidden" name="modulo_id" value="{{ $modulo->id }}" />
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-semibold form-label mb-2">Nome do Submódulo</label>
                            <input type="text" class="form-control form-control-solid" name="nome" placeholder="Ex: Formulário Institucional" required />
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold form-label mb-2">Descrição</label>
                            <textarea class="form-control form-control-solid" name="descricao" rows="3" placeholder="Descrição do submódulo"></textarea>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-semibold form-label mb-2">Tipo</label>
                            <select class="form-select form-select-solid" name="tipo" required>
                                <option value="">Selecione o tipo</option>
                                <option value="form">Formulário</option>
                                <option value="checkbox">Checkbox</option>
                                <option value="select">Select</option>
                                <option value="toggle">Toggle</option>
                                <option value="custom">Customizado</option>
                            </select>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold form-label mb-2">Ordem</label>
                            <input type="number" class="form-control form-control-solid" name="ordem" placeholder="0" min="0" />
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ativo" id="ativo_submodulo" value="1" checked />
                                <label class="form-check-label" for="ativo_submodulo">
                                    Submódulo ativo
                                </label>
                            </div>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Actions-->
                        <div class="text-center">
                            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">
                                <span class="indicator-label">Criar Submódulo</span>
                                <span class="indicator-progress">Por favor aguarde...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                        <!--end::Actions-->
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - Create Submodule-->

    <!--begin::Modal - JSON Export-->
    <div class="modal fade" id="kt_modal_json_export" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Exportar Módulo em JSON</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <form id="kt_modal_json_export_form">
                        <div class="fv-row mb-10">
                            <label class="fs-6 fw-semibold form-label mb-2">Módulo a Exportar</label>
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="ki-duotone {{ $modulo->icon ?: 'ki-setting-2' }} fs-2 text-primary me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div>
                                    <h6 class="mb-0">{{ $modulo->nome }}</h6>
                                    <span class="text-muted fs-7">{{ $modulo->descricao ?: 'Sem descrição' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="fv-row mb-10">
                            <label class="fs-6 fw-semibold form-label mb-2">Tipo de Extração</label>
                            <div class="row mb-5">
                                <div class="col-md-6">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="radio" value="true" name="simples_export" id="simples_export_sim" checked>
                                        <label class="form-check-label" for="simples_export_sim">
                                            <strong>Apenas Campos</strong>
                                            <div class="text-muted fs-7">Lista simplificada dos campos e seu uso</div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="radio" value="false" name="simples_export" id="simples_export_nao">
                                        <label class="form-check-label" for="simples_export_nao">
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
                                        <input class="form-check-input" type="radio" value="json" name="formato_export" id="formato_export_json" checked>
                                        <label class="form-check-label" for="formato_export_json">
                                            Visualizar JSON
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="radio" value="download" name="formato_export" id="formato_export_download">
                                        <label class="form-check-label" for="formato_export_download">
                                            Download Arquivo
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="separator separator-dashed my-7"></div>

                        <div class="fv-row mb-7">
                            <h6 class="fw-bold text-gray-800 mb-3">Dados que serão incluídos:</h6>
                            <ul class="list-unstyled">
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check-square fs-5 text-success me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Informações completas do módulo
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check-square fs-5 text-success me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Todos os submódulos e campos
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check-square fs-5 text-success me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Valores atuais configurados
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check-square fs-5 text-success me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Estatísticas de utilização
                                </li>
                                <li class="d-flex align-items-center">
                                    <i class="ki-duotone ki-check-square fs-5 text-success me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Configurações e validações
                                </li>
                            </ul>
                        </div>

                        <div class="text-center">
                            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" data-kt-json-export-action="submit">
                                <span class="indicator-label">Exportar Dados</span>
                                <span class="indicator-progress">Por favor aguarde...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - JSON Export-->

    <!--begin::Modal - JSON Result-->
    <div class="modal fade" id="kt_modal_json_result_show" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">{{ $modulo->nome }} - Dados JSON</h2>
                    <div class="d-flex">
                        <button type="button" class="btn btn-sm btn-light me-3" id="copy_json_show_btn">
                            <i class="ki-duotone ki-copy fs-3"></i>
                            Copiar
                        </button>
                        <button type="button" class="btn btn-sm btn-primary me-3" id="download_json_show_btn">
                            <i class="ki-duotone ki-exit-down fs-3"></i>
                            Download
                        </button>
                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <pre id="json_result_show_content" class="bg-light p-5 rounded" style="max-height: 70vh; overflow-y: auto;"><code></code></pre>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - JSON Result-->
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Export JSON button click
            document.getElementById('btn_export_json').addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById('kt_modal_json_export'));
                modal.show();
            });

            // Export form submission
            document.getElementById('kt_modal_json_export_form').addEventListener('submit', function(e) {
                e.preventDefault();
                handleJsonExport();
            });

            // Copy JSON button (show modal)
            document.getElementById('copy_json_show_btn').addEventListener('click', function() {
                const content = document.querySelector('#json_result_show_content code').textContent;
                navigator.clipboard.writeText(content).then(function() {
                    toastr.success('JSON copiado para a área de transferência!');
                });
            });

            // Download JSON button (show modal)
            document.getElementById('download_json_show_btn').addEventListener('click', function() {
                const content = document.querySelector('#json_result_show_content code').textContent;
                const filename = 'modulo_{{ $modulo->id }}_{{ Str::slug($modulo->nome) }}_' + new Date().toISOString().slice(0,10) + '.json';
                downloadJSON(content, filename);
            });
        });

        // Handle JSON export
        function handleJsonExport() {
            const form = document.getElementById('kt_modal_json_export_form');
            const formData = new FormData(form);
            const submitBtn = form.querySelector('[data-kt-json-export-action="submit"]');
            const moduloId = document.getElementById('btn_export_json').getAttribute('data-modulo-id');
            
            // Show loading state
            submitBtn.setAttribute('data-kt-indicator', 'on');
            submitBtn.disabled = true;

            // Build query parameters
            const params = new URLSearchParams();
            params.append('modulo_id', moduloId);
            params.append('formato', formData.get('formato_export'));
            params.append('simples', formData.get('simples_export'));

            fetch(`/api/parametros-modular/modulos/extrair-json?${params.toString()}`)
                .then(response => {
                    if (formData.get('formato_export') === 'download') {
                        // Handle file download
                        return response.blob().then(blob => {
                            const filename = response.headers.get('Content-Disposition')?.match(/filename="(.+)"/)?.[1] || 'modulo_{{ $modulo->id }}_parametros.json';
                            downloadBlob(blob, filename);
                            
                            // Hide export modal
                            const exportModal = bootstrap.Modal.getInstance(document.getElementById('kt_modal_json_export'));
                            exportModal.hide();
                            
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
                        document.querySelector('#json_result_show_content code').textContent = jsonContent;
                        
                        // Hide export modal and show result modal
                        const exportModal = bootstrap.Modal.getInstance(document.getElementById('kt_modal_json_export'));
                        exportModal.hide();
                        
                        const resultModal = new bootstrap.Modal(document.getElementById('kt_modal_json_result_show'));
                        resultModal.show();
                    }
                })
                .catch(error => {
                    console.error('Erro ao exportar dados:', error);
                    
                    // Tenta extrair mais informações do erro
                    if (error.response) {
                        error.response.json().then(errorData => {
                            console.error('Detalhes do erro:', errorData);
                            toastr.error('Erro: ' + (errorData.erro || 'Erro desconhecido'));
                        }).catch(() => {
                            toastr.error('Erro ao exportar dados. Tente novamente.');
                        });
                    } else {
                        toastr.error('Erro ao exportar dados. Verifique a conexão.');
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