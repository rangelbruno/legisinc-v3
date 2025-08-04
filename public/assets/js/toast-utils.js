/**
 * Toast Utilities - Sistema de notificações usando Bootstrap Toast
 * Baseado na documentação oficial do tema e Bootstrap 5
 */

class ToastManager {
    constructor() {
        this.container = this.createContainer();
        this.toastConfig = {
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
    }

    createContainer() {
        let container = document.getElementById('kt_toast_container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'kt_toast_container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.cssText = `
                position: fixed !important;
                top: 20px !important;
                right: 20px !important;
                z-index: 99999 !important;
                pointer-events: none !important;
                max-width: 420px !important;
                width: auto !important;
            `;
            // Always append to body, not to current parent
            document.body.appendChild(container);
        }
        return container;
    }

    show(message, type = 'info', options = {}) {
        const config = this.toastConfig[type] || this.toastConfig.info;
        const duration = options.duration || 2000;
        const title = options.title || config.title;
        const toastId = 'toast_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        
        // Create toast HTML
        const toastHtml = `
            <div class="toast ${config.bgClass}" role="alert" aria-live="assertive" aria-atomic="true" id="${toastId}" data-bs-autohide="true" data-bs-delay="${duration}">
                <div class="toast-header">
                    ${config.icon}
                    <strong class="me-auto">${title}</strong>
                    <small class="text-muted">agora</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body fw-semibold">
                    ${message}
                </div>
            </div>
        `;
        
        // Add toast to container
        this.container.insertAdjacentHTML('beforeend', toastHtml);
        
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

    // Convenience methods
    success(message, options = {}) {
        return this.show(message, 'success', options);
    }

    error(message, options = {}) {
        return this.show(message, 'error', options);
    }

    warning(message, options = {}) {
        return this.show(message, 'warning', options);
    }

    info(message, options = {}) {
        return this.show(message, 'info', options);
    }

    // Clear all toasts
    clearAll() {
        const toasts = this.container.querySelectorAll('.toast');
        toasts.forEach(toast => {
            const toastInstance = bootstrap.Toast.getInstance(toast);
            if (toastInstance) {
                toastInstance.hide();
            }
        });
    }
}

// Initialize global toast manager
window.Toast = new ToastManager();

// Backward compatibility functions
window.showToast = function(message, type, options) {
    return window.Toast.show(message, type, options);
};

// Quick access functions
window.showSuccess = function(message, options) {
    return window.Toast.success(message, options);
};

window.showError = function(message, options) {
    return window.Toast.error(message, options);
};

window.showWarning = function(message, options) {
    return window.Toast.warning(message, options);
};

window.showInfo = function(message, options) {
    return window.Toast.info(message, options);
};

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Toast Manager inicializado');
});