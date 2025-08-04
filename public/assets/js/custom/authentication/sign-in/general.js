"use strict";

var KTSigninGeneral = function() {
    var form, submitButton, validator;

    return {
        init: function() {
            form = document.querySelector("#kt_sign_in_form");
            submitButton = document.querySelector("#kt_sign_in_submit");
            
            validator = FormValidation.formValidation(form, {
                fields: {
                    email: {
                        validators: {
                            regexp: {
                                regexp: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                                message: "O valor não é um endereço de email válido"
                            },
                            notEmpty: {
                                message: "Endereço de email é obrigatório"
                            }
                        }
                    },
                    password: {
                        validators: {
                            notEmpty: {
                                message: "A senha é obrigatória"
                            }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger,
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                        eleInvalidClass: "",
                        eleValidClass: ""
                    })
                }
            });

            submitButton.addEventListener("click", function(e) {
                e.preventDefault();
                
                validator.validate().then(function(status) {
                    if (status == "Valid") {
                        submitButton.setAttribute("data-kt-indicator", "on");
                        submitButton.disabled = true;

                        // Get CSRF token
                        const csrfToken = document.querySelector('meta[name="csrf-token"]') || 
                                         document.querySelector('input[name="_token"]');
                        
                        const formData = new FormData(form);
                        
                        // Ensure CSRF token is included
                        if (csrfToken) {
                            if (csrfToken.tagName === 'META') {
                                formData.append('_token', csrfToken.getAttribute('content'));
                            }
                        }

                        axios.post(form.getAttribute("action"), formData, {
                            headers: {
                                'X-CSRF-TOKEN': csrfToken ? 
                                    (csrfToken.tagName === 'META' ? csrfToken.getAttribute('content') : csrfToken.value) : 
                                    '',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }).then(function(response) {
                            if (response.status === 200) {
                                // Check if it's a redirect response
                                if (response.data && response.data.redirect) {
                                    window.location.href = response.data.redirect;
                                } else {
                                    // Handle successful login
                                    const redirectUrl = form.getAttribute("data-kt-redirect-url") || '/dashboard';
                                    window.location.href = redirectUrl;
                                }
                            }
                        }).catch(function(error) {
                            let errorMessage = "Desculpe, parece que alguns erros foram detectados, tente novamente.";
                            
                            if (error.response && error.response.status === 422) {
                                // Validation errors
                                const errors = error.response.data.errors;
                                if (errors && errors.email) {
                                    errorMessage = errors.email[0];
                                }
                            } else if (error.response && error.response.status === 419) {
                                errorMessage = "Sessão expirada. Por favor, recarregue a página e tente novamente.";
                                // Reload page to get fresh CSRF token
                                setTimeout(() => window.location.reload(), 2000);
                            } else if (error.response && error.response.data && error.response.data.message) {
                                errorMessage = error.response.data.message;
                            }

                            Swal.fire({
                                text: errorMessage,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, entendi!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }).finally(function() {
                            submitButton.removeAttribute("data-kt-indicator");
                            submitButton.disabled = false;
                        });
                    } else {
                        Swal.fire({
                            text: "Desculpe, parece que alguns erros foram detectados, por favor tente novamente.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, entendi!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                });
            });
        }
    };
}();

KTUtil.onDOMContentLoaded(function() {
    KTSigninGeneral.init();
});