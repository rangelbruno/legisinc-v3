<!-- Modal para finalizar mandato -->
<div class="modal fade" id="kt_modal_finalizar_mandato" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Finalizar Mandato</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-members-modal-action="close">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_finalizar_mandato_form" class="form" action="#" method="POST">
                    @csrf
                    
                    <div class="fv-row mb-7">
                        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6">
                            <i class="ki-duotone ki-information fs-2tx text-info me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <h4 class="text-gray-900 fw-bold">Finalizar Mandato</h4>
                                    <div class="fs-6 text-gray-700">Tem certeza que deseja finalizar este mandato? O status será alterado para "finalizado" e a data de fim será ajustada para hoje.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-kt-members-modal-action="cancel">Cancelar</button>
                        <button type="submit" class="btn btn-warning" data-kt-indicator="off">
                            <span class="indicator-label">Finalizar Mandato</span>
                            <span class="indicator-progress">Por favor aguarde...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>