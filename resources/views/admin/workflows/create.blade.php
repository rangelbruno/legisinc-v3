@extends('components.layouts.admin')

@section('title', 'Criar Workflow')

@section('content')
<x-admin.page-container>
    <!--begin::Toolbar-->
    <x-admin.page-toolbar>
        <x-admin.page-title>
            <x-slot:title>Criar Workflow</x-slot:title>
            <x-admin.breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Workflows', 'url' => route('admin.workflows.index')],
                ['label' => 'Criar', 'active' => true]
            ]" />
        </x-admin.page-title>

        <x-admin.page-actions>
            <a href="{{ route('admin.workflows.index') }}" class="btn btn-light btn-active-light-primary me-2">
                <i class="ki-duotone ki-black-left fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Voltar
            </a>
        </x-admin.page-actions>
    </x-admin.page-toolbar>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="app-container container-fluid d-flex flex-column">
            <div class="row">
                <div class="col-lg-8">
                    <form id="kt_workflow_form" method="POST" action="{{ route('admin.workflows.store') }}">
                        @csrf

                        <!--begin::Card-->
                        <div class="card mb-5">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Informações Básicas</h2>
                                </div>
                            </div>
                            <!--end::Card header-->

                            <!--begin::Card body-->
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                                        <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <h4 class="mb-1 text-danger">Erros de validação</h4>
                                            <span>
                                                @foreach ($errors->all() as $error)
                                                    {{ $error }}<br>
                                                @endforeach
                                            </span>
                                        </div>
                                    </div>
                                @endif
                                <!--begin::Input group-->
                                <div class="row g-9 mb-7">
                                    <!--begin::Col-->
                                    <div class="col-md-8 fv-row">
                                        <label class="required fs-6 fw-semibold mb-2">Nome do Workflow</label>
                                        <input type="text" name="nome" id="nome" 
                                               class="form-control form-control-solid @error('nome') is-invalid @enderror"
                                               value="{{ old('nome') }}" placeholder="Ex: Fluxo de Proposições Especiais">
                                        @error('nome')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="text-muted fs-7">Nome único e descritivo do workflow</div>
                                    </div>
                                    <!--end::Col-->

                                    <!--begin::Col-->
                                    <div class="col-md-4 fv-row">
                                        <label class="fs-6 fw-semibold mb-2">Ordem de Exibição</label>
                                        <input type="number" name="ordem" id="ordem" 
                                               class="form-control form-control-solid @error('ordem') is-invalid @enderror"
                                               value="{{ old('ordem', 1) }}" min="1" max="999">
                                        @error('ordem')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="text-muted fs-7">Prioridade de apresentação</div>
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="required fs-6 fw-semibold mb-2">Tipo de Documento</label>
                                    <select name="tipo_documento" id="tipo_documento" 
                                            class="form-select form-select-solid @error('tipo_documento') is-invalid @enderror" data-control="select2" data-placeholder="Selecione o tipo de documento">
                                <option value="">Selecione o tipo de documento</option>
                                <option value="proposicao" {{ old('tipo_documento') === 'proposicao' ? 'selected' : '' }}>
                                    Proposição
                                </option>
                                <option value="requerimento" {{ old('tipo_documento') === 'requerimento' ? 'selected' : '' }}>
                                    Requerimento
                                </option>
                                <option value="projeto_lei" {{ old('tipo_documento') === 'projeto_lei' ? 'selected' : '' }}>
                                    Projeto de Lei
                                </option>
                                <option value="emenda" {{ old('tipo_documento') === 'emenda' ? 'selected' : '' }}>
                                    Emenda
                                </option>
                                <option value="mocao" {{ old('tipo_documento') === 'mocao' ? 'selected' : '' }}>
                                    Moção
                                </option>
                                <option value="indicacao" {{ old('tipo_documento') === 'indicacao' ? 'selected' : '' }}>
                                    Indicação
                                </option>
                                <option value="pedido_informacao" {{ old('tipo_documento') === 'pedido_informacao' ? 'selected' : '' }}>
                                    Pedido de Informação
                                </option>
                                <option value="outro" {{ old('tipo_documento') === 'outro' ? 'selected' : '' }}>
                                    Outro
                                </option>
                                    </select>
                                    @error('tipo_documento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="text-muted fs-7">Tipo de documento que utilizará este workflow</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold mb-2">Descrição</label>
                                    <textarea name="descricao" id="descricao" rows="3"
                                              class="form-control form-control-solid @error('descricao') is-invalid @enderror"
                                              placeholder="Descreva o propósito e funcionamento deste workflow...">{{ old('descricao') }}</textarea>
                                    @error('descricao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="text-muted fs-7">Descrição opcional do workflow</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="row g-9 mb-7">
                                    <!--begin::Col-->
                                    <div class="col-md-6 fv-row">
                                        <label class="fs-6 fw-semibold mb-2">Configurações</label>
                                        <div class="form-check form-switch form-check-custom form-check-solid">
                                            <input type="checkbox" name="ativo" id="ativo" value="1"
                                                   class="form-check-input" {{ old('ativo', true) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="ativo">Workflow Ativo</label>
                                        </div>
                                        <div class="text-muted fs-7">Workflow pode ser usado por documentos</div>
                                    </div>
                                    <!--end::Col-->

                                    <!--begin::Col-->
                                    <div class="col-md-6 fv-row">
                                        <label class="fs-6 fw-semibold mb-2">&nbsp;</label>
                                        <div class="form-check form-switch form-check-custom form-check-solid">
                                            <input type="checkbox" name="is_default" id="is_default" value="1"
                                                   class="form-check-input" {{ old('is_default') ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="is_default">Workflow Padrão</label>
                                        </div>
                                        <div class="text-muted fs-7">Usado automaticamente para novos documentos deste tipo</div>
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold mb-2">
                                        Configuração Avançada (JSON)
                                        <i class="ki-duotone ki-information-5 fs-7 text-muted ms-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </label>
                                    <textarea name="configuracao" id="configuracao" rows="4"
                                              class="form-control form-control-solid @error('configuracao') is-invalid @enderror"
                                              placeholder='{"timeout": 86400, "notificacoes": true, "auto_aprovacao": false}'>{{ old('configuracao', '{}') }}</textarea>
                                    @error('configuracao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="text-muted fs-7">
                                        Configurações em JSON. Exemplo: timeout em segundos, habilitação de notificações, etc.
                                    </div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Actions-->
                                <div class="text-center pt-15">
                                    <button type="reset" class="btn btn-light me-3" onclick="window.location.href='{{ route('admin.workflows.index') }}'">
                                        <i class="ki-duotone ki-cross fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Cancelar
                                    </button>
                                    
                                    <button type="submit" name="action" value="save" class="btn btn-primary me-3">
                                        <i class="ki-duotone ki-check fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Salvar Workflow
                                    </button>
                                    
                                    <button type="submit" name="action" value="save_and_designer" class="btn btn-success">
                                        <i class="ki-duotone ki-design-1 fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                        Salvar e Abrir Designer
                                    </button>
                                </div>
                                <!--end::Actions-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->

                        <!--begin::Help Card-->
                        <div class="card mt-5">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ki-duotone ki-information-4 fs-2 text-warning me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <h3 class="fw-bold m-0">Próximos Passos</h3>
                                </div>
                            </div>
                            <!--end::Card header-->

                            <!--begin::Card body-->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3">Após criar o workflow:</h6>
                                        <div class="d-flex flex-column">
                                            <div class="d-flex align-items-center py-2">
                                                <i class="ki-duotone ki-right fs-6 text-primary me-3">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <span class="fw-semibold fs-6">Configure as etapas do fluxo</span>
                                            </div>
                                            <div class="d-flex align-items-center py-2">
                                                <i class="ki-duotone ki-right fs-6 text-primary me-3">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <span class="fw-semibold fs-6">Defina as transições entre etapas</span>
                                            </div>
                                            <div class="d-flex align-items-center py-2">
                                                <i class="ki-duotone ki-right fs-6 text-primary me-3">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <span class="fw-semibold fs-6">Configure permissões por role</span>
                                            </div>
                                            <div class="d-flex align-items-center py-2">
                                                <i class="ki-duotone ki-right fs-6 text-primary me-3">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <span class="fw-semibold fs-6">Teste o fluxo com documento real</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3">Designer Visual:</h6>
                                        <p class="text-muted mb-0">
                                            Após salvar, você pode usar o Designer Visual para configurar graficamente 
                                            as etapas e transições do workflow de forma intuitiva.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Help Card-->
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin.page-container>
@endsection

@push('scripts')
<script>
"use strict";

// Class definition
var KTWorkflowCreate = function () {
    // Elements
    var form;
    var submitButton;
    var validator;

    // Handle form
    var handleForm = function() {
        // Init form validation rules
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'nome': {
                        validators: {
                            notEmpty: {
                                message: 'Nome do workflow é obrigatório'
                            }
                        }
                    },
                    'tipo_documento': {
                        validators: {
                            notEmpty: {
                                message: 'Tipo de documento é obrigatório'
                            }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
                }
            }
        );
    }

    var handleJSONValidation = function() {
        const configTextarea = document.getElementById('configuracao');
        
        if (configTextarea) {
            configTextarea.addEventListener('blur', function() {
                const value = this.value.trim();
                if (value && value !== '{}') {
                    try {
                        JSON.parse(value);
                        this.classList.remove('is-invalid');
                    } catch (e) {
                        this.classList.add('is-invalid');
                    }
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        }
    }

    var handleDefaultWorkflow = function() {
        const tipoDocumento = document.getElementById('tipo_documento');
        const isDefault = document.getElementById('is_default');
        
        if (tipoDocumento && isDefault) {
            isDefault.addEventListener('change', function() {
                if (this.checked && !tipoDocumento.value) {
                    Swal.fire({
                        text: "Selecione um tipo de documento antes de definir como padrão.",
                        icon: "warning",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, entendido!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                    this.checked = false;
                }
            });
        }
    }

    // Public methods
    return {
        init: function () {
            form = document.querySelector('#kt_workflow_form');

            if (!form) {
                return;
            }

            handleForm();
            handleJSONValidation();
            handleDefaultWorkflow();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTWorkflowCreate.init();
});
</script>
@endpush