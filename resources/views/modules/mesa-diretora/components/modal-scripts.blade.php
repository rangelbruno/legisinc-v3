<script>
"use strict";

var KTMesaDiretoraModals = function () {
    
    var handleDeleteRows = function () {
        const deleteButtons = document.querySelectorAll('[data-kt-action="delete_row"]');

        deleteButtons.forEach(d => {
            d.addEventListener('click', function (e) {
                e.preventDefault();

                const memberId = d.getAttribute('data-kt-member-id');
                const modal = document.querySelector('#kt_modal_delete_member');
                const form = modal.querySelector('#kt_modal_delete_member_form');
                
                // Se o form já não tem action definida, definir dinamicamente
                if (!form.getAttribute('action') || form.getAttribute('action') === '#') {
                    @if(isset($routePrefix))
                        form.setAttribute('action', '{{ route($routePrefix . ".destroy", ":id") }}'.replace(':id', memberId));
                    @else
                        form.setAttribute('action', '{{ route("mesa-diretora.destroy", ":id") }}'.replace(':id', memberId));
                    @endif
                }
                
                $(modal).modal('show');
            })
        });
    }

    var handleFinalizarMandato = function () {
        const finalizarButtons = document.querySelectorAll('[data-kt-action="finalizar_mandato"]');

        finalizarButtons.forEach(d => {
            d.addEventListener('click', function (e) {
                e.preventDefault();

                const memberId = d.getAttribute('data-kt-member-id');
                const modal = document.querySelector('#kt_modal_finalizar_mandato');
                const form = modal.querySelector('#kt_modal_finalizar_mandato_form');
                
                // Se o form já não tem action definida, definir dinamicamente
                if (!form.getAttribute('action') || form.getAttribute('action') === '#') {
                    @if(isset($routePrefix))
                        form.setAttribute('action', '{{ route($routePrefix . ".finalizar", ":id") }}'.replace(':id', memberId));
                    @else
                        form.setAttribute('action', '{{ route("mesa-diretora.finalizar", ":id") }}'.replace(':id', memberId));
                    @endif
                }
                
                $(modal).modal('show');
            })
        });
    }

    var handleModalCancel = function () {
        const cancelButtons = document.querySelectorAll('[data-kt-members-modal-action="cancel"]');
        const closeButtons = document.querySelectorAll('[data-kt-members-modal-action="close"]');

        cancelButtons.forEach(c => {
            c.addEventListener('click', function (e) {
                e.preventDefault();
                const modal = c.closest('.modal');
                $(modal).modal('hide');
            })
        });

        closeButtons.forEach(c => {
            c.addEventListener('click', function (e) {
                e.preventDefault();
                const modal = c.closest('.modal');
                $(modal).modal('hide');
            })
        });
    }

    return {
        init: function () {
            handleDeleteRows();
            handleFinalizarMandato();
            handleModalCancel();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTMesaDiretoraModals.init();
});
</script>