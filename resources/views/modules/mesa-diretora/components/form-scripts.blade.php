<script>
"use strict";

// Class definition
var KTAppMesaDiretoraForm = function () {
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
                                message: 'Data deve estar no formato DD/MM/AAAA'
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
                                message: 'Data deve estar no formato DD/MM/AAAA'
                            }
                        }
                    }
                    @if(isset($isEdit) && $isEdit)
                    ,
                    'status': {
                        validators: {
                            notEmpty: {
                                message: 'Status é obrigatório'
                            }
                        }
                    }
                    @endif
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
    KTAppMesaDiretoraForm.init();
});
</script>