@extends('components.layouts.app')

@section('title', 'Novo Parâmetro')

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Novo Parâmetro
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
                    <li class="breadcrumb-item text-muted">Novo</li>
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
                        <a href="{{ route('parametros.create') }}" class="fw-bold text-warning text-hover-primary">Acesse o novo sistema aqui</a>.
                    </span>
                </div>
            </div>
            <!--end::Migration Alert-->

            <!--begin::Alert-->
            @if ($errors->any())
                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h5 class="mb-1">Erro de Validação</h5>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            <!--end::Alert-->

            <!--begin::Form-->
            <form id="kt_parametros_form" class="form d-flex flex-column flex-lg-row" action="{{ route('parametros.store') }}" method="POST">
                @csrf
                
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
                                <label class="required form-label">Nome</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="nome" class="form-control mb-2" placeholder="Nome do parâmetro" value="{{ old('nome') }}" required />
                                <!--end::Input-->
                                <!--begin::Description-->
                                <div class="text-muted fs-7">Nome descritivo do parâmetro</div>
                                <!--end::Description-->
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="required form-label">Código</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="codigo" class="form-control mb-2" placeholder="codigo_parametro" value="{{ old('codigo') }}" required />
                                <!--end::Input-->
                                <!--begin::Description-->
                                <div class="text-muted fs-7">Código único do parâmetro (usado no código)</div>
                                <!--end::Description-->
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="form-label">Descrição</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <textarea name="descricao" class="form-control mb-2" rows="3" placeholder="Descrição do parâmetro">{{ old('descricao') }}</textarea>
                                <!--end::Input-->
                                <!--begin::Description-->
                                <div class="text-muted fs-7">Descrição detalhada do parâmetro</div>
                                <!--end::Description-->
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="form-label">Texto de Ajuda</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="help_text" class="form-control mb-2" placeholder="Texto de ajuda para o usuário" value="{{ old('help_text') }}" />
                                <!--end::Input-->
                                <!--begin::Description-->
                                <div class="text-muted fs-7">Texto de ajuda exibido na interface</div>
                                <!--end::Description-->
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::General options-->
                    
                    <!--begin::Configuration-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Configuração</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="required form-label">Grupo</label>
                                <!--end::Label-->
                                <!--begin::Select-->
                                <select name="grupo_parametro_id" class="form-select mb-2" data-control="select2" data-placeholder="Selecione um grupo" required>
                                    <option></option>
                                    @foreach($grupos ?? [] as $grupo)
                                        <option value="{{ $grupo->id }}" {{ old('grupo_parametro_id') == $grupo->id ? 'selected' : '' }}>
                                            {{ $grupo->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                <!--end::Select-->
                                <!--begin::Description-->
                                <div class="text-muted fs-7">Grupo ao qual o parâmetro pertence</div>
                                <!--end::Description-->
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="required form-label">Tipo</label>
                                <!--end::Label-->
                                <!--begin::Select-->
                                <select name="tipo_parametro_id" class="form-select mb-2" data-control="select2" data-placeholder="Selecione um tipo" required>
                                    <option></option>
                                    @foreach($tipos ?? [] as $tipo)
                                        <option value="{{ $tipo->id }}" {{ old('tipo_parametro_id') == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                <!--end::Select-->
                                <!--begin::Description-->
                                <div class="text-muted fs-7">Tipo de dados do parâmetro</div>
                                <!--end::Description-->
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="form-label">Valor</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="valor" class="form-control mb-2" placeholder="Valor do parâmetro" value="{{ old('valor') }}" />
                                <!--end::Input-->
                                <!--begin::Description-->
                                <div class="text-muted fs-7">Valor atual do parâmetro</div>
                                <!--end::Description-->
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="form-label">Valor Padrão</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="valor_padrao" class="form-control mb-2" placeholder="Valor padrão do parâmetro" value="{{ old('valor_padrao') }}" />
                                <!--end::Input-->
                                <!--begin::Description-->
                                <div class="text-muted fs-7">Valor padrão usado quando o parâmetro não tem valor</div>
                                <!--end::Description-->
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="form-label">Ordem</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="number" name="ordem" class="form-control mb-2" placeholder="0" value="{{ old('ordem', 0) }}" min="0" />
                                <!--end::Input-->
                                <!--begin::Description-->
                                <div class="text-muted fs-7">Ordem de exibição do parâmetro</div>
                                <!--end::Description-->
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Configuration-->
                    
                    <!--begin::Status-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Status</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Status select-->
                            <div class="d-flex flex-wrap gap-5">
                                <!--begin::Input group-->
                                <div class="fv-row w-100 flex-md-root">
                                    <!--begin::Option-->
                                    <label class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" name="obrigatorio" value="1" {{ old('obrigatorio') ? 'checked' : '' }}/>
                                        <span class="form-check-label fw-semibold text-gray-400 ms-3">Obrigatório</span>
                                    </label>
                                    <!--end::Option-->
                                </div>
                                <!--end::Input group-->
                                
                                <!--begin::Input group-->
                                <div class="fv-row w-100 flex-md-root">
                                    <!--begin::Option-->
                                    <label class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" name="editavel" value="1" {{ old('editavel', true) ? 'checked' : '' }}/>
                                        <span class="form-check-label fw-semibold text-gray-400 ms-3">Editável</span>
                                    </label>
                                    <!--end::Option-->
                                </div>
                                <!--end::Input group-->
                                
                                <!--begin::Input group-->
                                <div class="fv-row w-100 flex-md-root">
                                    <!--begin::Option-->
                                    <label class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" name="visivel" value="1" {{ old('visivel', true) ? 'checked' : '' }}/>
                                        <span class="form-check-label fw-semibold text-gray-400 ms-3">Visível</span>
                                    </label>
                                    <!--end::Option-->
                                </div>
                                <!--end::Input group-->
                                
                                <!--begin::Input group-->
                                <div class="fv-row w-100 flex-md-root">
                                    <!--begin::Option-->
                                    <label class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" name="ativo" value="1" {{ old('ativo', true) ? 'checked' : '' }}/>
                                        <span class="form-check-label fw-semibold text-gray-400 ms-3">Ativo</span>
                                    </label>
                                    <!--end::Option-->
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Status select-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Status-->
                    
                    <div class="d-flex justify-content-end">
                        <!--begin::Button-->
                        <a href="{{ route('parametros.index') }}" class="btn btn-light me-5">Cancelar</a>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <button type="submit" id="kt_parametros_submit" class="btn btn-primary">
                            <span class="indicator-label">Salvar</span>
                            <span class="indicator-progress">Por favor aguarde...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                        <!--end::Button-->
                    </div>
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
            // Form validation
            const form = document.getElementById('kt_parametros_form');
            const submitButton = document.getElementById('kt_parametros_submit');
            
            if (form && submitButton) {
                form.addEventListener('submit', function() {
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;
                });
            }
        });
    </script>
@endpush