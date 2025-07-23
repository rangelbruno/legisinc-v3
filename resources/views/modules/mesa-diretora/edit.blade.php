@extends('components.layouts.app')

@section('title', $title ?? 'Editar Membro - Mesa Diretora')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Editar Membro
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('mesa-diretora.index') }}" class="text-muted text-hover-primary">Mesa Diretora</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Editar Membro</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('mesa-diretora.show', $membro['id']) }}" class="btn btn-sm fw-bold btn-secondary">
                    <i class="ki-duotone ki-eye fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    Visualizar
                </a>
                <a href="{{ route('mesa-diretora.index') }}" class="btn btn-sm fw-bold btn-secondary">
                    <i class="ki-duotone ki-black-left fs-2"></i>
                    Voltar
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if(session('error'))
                <div class="alert alert-danger">
                    <div class="alert-text">{{ session('error') }}</div>
                </div>
            @endif

            <!--begin::Form-->
            <form id="kt_mesa_diretora_form" class="form d-flex flex-column flex-lg-row" action="{{ route('mesa-diretora.update', $membro['id']) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!--begin::Main column-->
                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                    <!--begin::General options-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Informações do Membro</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="required form-label">Parlamentar</label>
                                <!--end::Label-->
                                <!--begin::Select2-->
                                <select class="form-select mb-2" data-control="select2" data-placeholder="Selecione um parlamentar" data-allow-clear="true" name="parlamentar_id">
                                    <option></option>
                                    @foreach($parlamentares as $parlamentar)
                                        <option value="{{ $parlamentar->id }}" 
                                            {{ (old('parlamentar_id', $membro['parlamentar_id']) == $parlamentar->id) ? 'selected' : '' }}>
                                            {{ $parlamentar->nome }} ({{ $parlamentar->partido }})
                                        </option>
                                    @endforeach
                                </select>
                                <!--end::Select2-->
                                @error('parlamentar_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <!--begin::Description-->
                                <div class="text-muted fs-7">Selecione o parlamentar que ocupará o cargo na mesa diretora.</div>
                                <!--end::Description-->
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="required form-label">Cargo na Mesa</label>
                                <!--end::Label-->
                                <!--begin::Select2-->
                                <select class="form-select mb-2" data-control="select2" data-placeholder="Selecione um cargo" data-allow-clear="true" name="cargo_mesa">
                                    <option></option>
                                    @foreach($cargos as $cargo)
                                        <option value="{{ $cargo }}" 
                                            {{ (old('cargo_mesa', $membro['cargo_mesa']) == $cargo) ? 'selected' : '' }}>
                                            {{ $cargo }}
                                        </option>
                                    @endforeach
                                </select>
                                <!--end::Select2-->
                                @error('cargo_mesa')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <!--begin::Description-->
                                <div class="text-muted fs-7">Selecione o cargo que o parlamentar ocupará na mesa diretora.</div>
                                <!--end::Description-->
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="row mb-10">
                                <div class="col-md-6 fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Início do Mandato</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input class="form-control mb-2" placeholder="Selecione a data" name="mandato_inicio" id="kt_datepicker_1" 
                                           value="{{ old('mandato_inicio', $membro['mandato_inicio']) }}" />
                                    <!--end::Input-->
                                    @error('mandato_inicio')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <!--begin::Description-->
                                    <div class="text-muted fs-7">Data de início do mandato na mesa diretora.</div>
                                    <!--end::Description-->
                                </div>
                                
                                <div class="col-md-6 fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Fim do Mandato</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input class="form-control mb-2" placeholder="Selecione a data" name="mandato_fim" id="kt_datepicker_2" 
                                           value="{{ old('mandato_fim', $membro['mandato_fim']) }}" />
                                    <!--end::Input-->
                                    @error('mandato_fim')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <!--begin::Description-->
                                    <div class="text-muted fs-7">Data de fim do mandato na mesa diretora.</div>
                                    <!--end::Description-->
                                </div>
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="required form-label">Status</label>
                                <!--end::Label-->
                                <!--begin::Select2-->
                                <select class="form-select mb-2" data-control="select2" data-placeholder="Selecione o status" name="status">
                                    <option value="ativo" {{ (old('status', $membro['status']) == 'ativo') ? 'selected' : '' }}>Ativo</option>
                                    <option value="finalizado" {{ (old('status', $membro['status']) == 'finalizado') ? 'selected' : '' }}>Finalizado</option>
                                </select>
                                <!--end::Select2-->
                                @error('status')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <!--begin::Description-->
                                <div class="text-muted fs-7">Status atual do mandato na mesa diretora.</div>
                                <!--end::Description-->
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <!--begin::Label-->
                                <label class="form-label">Observações</label>
                                <!--end::Label-->
                                <!--begin::Textarea-->
                                <textarea class="form-control mb-2" rows="3" name="observacoes" placeholder="Observações sobre o mandato (opcional)">{{ old('observacoes', $membro['observacoes']) }}</textarea>
                                <!--end::Textarea-->
                                @error('observacoes')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <!--begin::Description-->
                                <div class="text-muted fs-7">Informações adicionais sobre o mandato (opcional).</div>
                                <!--end::Description-->
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::General options-->

                    <div class="d-flex justify-content-end">
                        <!--begin::Button-->
                        <a href="{{ route('mesa-diretora.index') }}" id="kt_mesa_diretora_cancel" class="btn btn-light me-5">Cancelar</a>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <button type="submit" id="kt_mesa_diretora_submit" class="btn btn-primary">
                            <span class="indicator-label">Atualizar</span>
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
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection

@push('scripts')
<script>
"use strict";

// Class definition
var KTAppMesaDiretoraEdit = function () {
    // Shared variables
    const element = document.getElementById('kt_mesa_diretora_form');
    const form = document.getElementById('kt_mesa_diretora_form');

    // Init form
    var initForm = function() {

        // Init form validation rules
        var validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'parlamentar_id': {
                        validators: {
                            notEmpty: {
                                message: 'Parlamentar é obrigatório'
                            }
                        }
                    },
                    'cargo_mesa': {
                        validators: {
                            notEmpty: {
                                message: 'Cargo é obrigatório'
                            }
                        }
                    },
                    'mandato_inicio': {
                        validators: {
                            notEmpty: {
                                message: 'Data de início é obrigatória'
                            },
                            date: {
                                format: 'DD/MM/YYYY',
                                message: 'Data deve estar no formato DD/MM/YYYY'
                            }
                        }
                    },
                    'mandato_fim': {
                        validators: {
                            notEmpty: {
                                message: 'Data de fim é obrigatória'
                            },
                            date: {
                                format: 'DD/MM/YYYY',
                                message: 'Data deve estar no formato DD/MM/YYYY'
                            }
                        }
                    },
                    'status': {
                        validators: {
                            notEmpty: {
                                message: 'Status é obrigatório'
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

        // Submit button handler
        const submitButton = element.querySelector('#kt_mesa_diretora_submit');
        submitButton.addEventListener('click', function (e) {
            // Validate form before submit
            if (validator) {
                validator.validate().then(function (status) {
                    console.log('validated!');

                    if (status == 'Valid') {
                        // Show loading indication
                        submitButton.setAttribute('data-kt-indicator', 'on');

                        // Disable button to avoid multiple click 
                        submitButton.disabled = true;

                        // Submit form
                        form.submit();
                    }
                });
            }
        });
    }

    // Init datepickers
    var initDatepickers = function() {
        // Init datepicker --- more info: https://flatpickr.js.org/
        $("#kt_datepicker_1").flatpickr({
            dateFormat: "d/m/Y",
            altFormat: "d/m/Y",
            altInput: true,
            locale: {
                firstDayOfWeek: 0,
                weekdays: {
                    shorthand: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
                    longhand: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado']
                },
                months: {
                    shorthand: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                    longhand: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro']
                }
            }
        });

        $("#kt_datepicker_2").flatpickr({
            dateFormat: "d/m/Y",
            altFormat: "d/m/Y",
            altInput: true,
            locale: {
                firstDayOfWeek: 0,
                weekdays: {
                    shorthand: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
                    longhand: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado']
                },
                months: {
                    shorthand: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                    longhand: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro']
                }
            }
        });
    }

    return {
        // Public functions
        init: function () {
            initForm();
            initDatepickers();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTAppMesaDiretoraEdit.init();
});
</script>
@endpush