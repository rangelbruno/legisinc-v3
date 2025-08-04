@if(session('success') || session('error') || session('warning') || session('info') || $errors->any())
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Create toast container directly in body if it doesn't exist
        let toastContainer = document.getElementById('kt_toast_container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'kt_toast_container';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.cssText = `
                position: fixed !important;
                top: 20px !important;
                right: 20px !important;
                z-index: 99999 !important;
                pointer-events: none !important;
                max-width: 420px !important;
            `;
            document.body.appendChild(toastContainer);
        }
        
        // Toast configuration for different types
        const toastConfig = {
            success: {
                icon: '<i class="ki-duotone ki-check-circle fs-2 text-success me-3"><span class="path1"></span><span class="path2"></span></i>',
                title: 'Sucesso!',
                bgClass: 'bg-light-success'
            },
            error: {
                icon: '<i class="ki-duotone ki-cross-circle fs-2 text-danger me-3"><span class="path1"></span><span class="path2"></span></i>',
                title: 'Erro!',
                bgClass: 'bg-light-danger'
            },
            warning: {
                icon: '<i class="ki-duotone ki-information fs-2 text-warning me-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>',
                title: 'Atenção!',
                bgClass: 'bg-light-warning'
            },
            info: {
                icon: '<i class="ki-duotone ki-information fs-2 text-info me-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>',
                title: 'Informação',
                bgClass: 'bg-light-info'
            }
        };
        
        function showToast(message, type = 'info', duration = 2000) {
            const config = toastConfig[type] || toastConfig.info;
            const toastId = 'toast_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            
            // Create toast HTML
            const toastHtml = `
                <div class="toast ${config.bgClass}" role="alert" aria-live="assertive" aria-atomic="true" id="${toastId}" data-bs-autohide="true" data-bs-delay="${duration}">
                    <div class="toast-header">
                        ${config.icon}
                        <strong class="me-auto">${config.title}</strong>
                        <small class="text-muted">agora</small>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body fw-semibold">
                        ${message}
                    </div>
                </div>
            `;
            
            // Add toast to container
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            
            // Get the new toast element
            const toastElement = document.getElementById(toastId);
            
            // Initialize Bootstrap Toast
            const toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: duration
            });
            
            // Show the toast
            toast.show();
            
            // Remove from DOM after it's hidden
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });
            
            return toast;
        }
        
        // Show session messages
        @if(session('success'))
            showToast(`{{ addslashes(session('success')) }}`, 'success');
        @endif
        
        @if(session('error'))
            showToast(`{{ addslashes(session('error')) }}`, 'error');
        @endif
        
        @if(session('warning'))
            showToast(`{{ addslashes(session('warning')) }}`, 'warning');
        @endif
        
        @if(session('info'))
            showToast(`{{ addslashes(session('info')) }}`, 'info');
        @endif
        
        @if($errors->any())
            @php
                $errorMessage = 'Verifique os campos do formulário:\\n';
                foreach($errors->all() as $error) {
                    $errorMessage .= '• ' . $error . '\\n';
                }
            @endphp
            showToast(`{{ addslashes($errorMessage) }}`, 'error', 3000);
        @endif
        
        // Make showToast globally available for dynamic use
        window.showToast = showToast;
    });
    </script>
    
    <style>
    /* Toast container global positioning - always in viewport */
    #kt_toast_container {
        position: fixed !important;
        top: 20px !important;
        right: 20px !important;
        z-index: 99999 !important;
        pointer-events: none !important;
        max-width: 420px !important;
        width: auto !important;
    }
    
    /* Ensure toast container is not affected by parent positioning */
    body #kt_toast_container {
        position: fixed !important;
        top: 20px !important;
        right: 20px !important;
        z-index: 99999 !important;
    }
    
    /* Custom toast styling */
    #kt_toast_container .toast {
        min-width: 350px;
        max-width: 400px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(0, 0, 0, 0.05);
        pointer-events: all !important;
        margin-bottom: 12px;
    }
    
    .toast.bg-light-success {
        background-color: rgba(25, 135, 84, 0.1) !important;
        border-left: 4px solid #198754;
    }
    
    .toast.bg-light-danger {
        background-color: rgba(220, 53, 69, 0.1) !important;
        border-left: 4px solid #dc3545;
    }
    
    .toast.bg-light-warning {
        background-color: rgba(255, 193, 7, 0.1) !important;
        border-left: 4px solid #ffc107;
    }
    
    .toast.bg-light-info {
        background-color: rgba(13, 202, 240, 0.1) !important;
        border-left: 4px solid #0dcaf0;
    }
    
    .toast-header {
        background-color: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .toast-body {
        background-color: rgba(255, 255, 255, 0.95);
        white-space: pre-line;
    }
    
    /* Responsive adjustments */
    @media (max-width: 576px) {
        #kt_toast_container {
            left: 10px !important;
            right: 10px !important;
            top: 10px !important;
            max-width: none !important;
        }
        
        #kt_toast_container .toast {
            min-width: auto !important;
            max-width: none !important;
            width: 100% !important;
        }
    }
    
    @media (max-width: 768px) {
        #kt_toast_container {
            top: 15px !important;
            right: 15px !important;
            max-width: calc(100vw - 30px) !important;
        }
        
        #kt_toast_container .toast {
            min-width: 300px !important;
            max-width: calc(100vw - 30px) !important;
        }
    }
    </style>
@endif