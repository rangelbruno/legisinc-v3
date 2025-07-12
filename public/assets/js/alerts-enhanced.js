/**
 * Enhanced Alert System for LegisPro
 * Provides dismissible alerts with auto-hide functionality
 */

class AlertManager {
    constructor() {
        this.alerts = [];
        this.defaultTimeout = 5000; // 5 seconds
        this.init();
    }

    init() {
        // Create toast container if it doesn't exist
        if (!document.getElementById('toast-container')) {
            this.createToastContainer();
        }

        // Initialize existing Bootstrap alerts
        this.enhanceBootstrapAlerts();
        
        // Setup CSS styles
        this.injectStyles();
    }

    createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }

    injectStyles() {
        if (document.getElementById('alert-enhanced-styles')) return;

        const style = document.createElement('style');
        style.id = 'alert-enhanced-styles';
        style.textContent = `
            /* Enhanced Alert Styles */
            .alert-dismissible {
                position: relative;
                padding-right: 4rem;
            }

            .alert .btn-close {
                position: absolute;
                top: 0;
                right: 0;
                z-index: 2;
                padding: 1.25rem 1rem;
                background: transparent;
                border: 0;
                border-radius: 0.375rem;
                opacity: 0.5;
                cursor: pointer;
                transition: opacity 0.15s ease-in-out;
            }

            .alert .btn-close:hover {
                opacity: 0.75;
            }

            .alert .btn-close:focus {
                opacity: 1;
                outline: 0;
                box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            }

            .alert.fade-out {
                opacity: 0;
                transform: translateX(100%);
                transition: all 0.3s ease-in-out;
            }

            /* Toast Notifications */
            .toast-notification {
                min-width: 350px;
                max-width: 500px;
                margin-bottom: 0.5rem;
                background: white;
                border-radius: 0.5rem;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
                transform: translateX(100%);
                transition: all 0.3s ease-in-out;
                opacity: 0;
            }

            .toast-notification.show {
                transform: translateX(0);
                opacity: 1;
            }

            .toast-notification.hide {
                transform: translateX(100%);
                opacity: 0;
            }

            .toast-notification .toast-header {
                padding: 0.75rem 1rem;
                background-color: rgba(0, 0, 0, 0.03);
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                border-top-left-radius: 0.5rem;
                border-top-right-radius: 0.5rem;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .toast-notification .toast-body {
                padding: 1rem;
            }

            .toast-notification .progress-bar {
                position: absolute;
                bottom: 0;
                left: 0;
                height: 3px;
                background: rgba(0, 0, 0, 0.1);
                border-bottom-left-radius: 0.5rem;
                border-bottom-right-radius: 0.5rem;
                transition: width linear;
            }

            /* Icon colors for different alert types */
            .toast-success .toast-icon { color: #198754; }
            .toast-danger .toast-icon { color: #dc3545; }
            .toast-warning .toast-icon { color: #ffc107; }
            .toast-info .toast-icon { color: #0dcaf0; }
            .toast-primary .toast-icon { color: #0d6efd; }

            .toast-success .progress-bar { background: #198754; }
            .toast-danger .progress-bar { background: #dc3545; }
            .toast-warning .progress-bar { background: #ffc107; }
            .toast-info .progress-bar { background: #0dcaf0; }
            .toast-primary .progress-bar { background: #0d6efd; }

            /* Animation for alert removal */
            @keyframes alertSlideOut {
                0% {
                    opacity: 1;
                    transform: translateX(0);
                    max-height: 200px;
                }
                50% {
                    opacity: 0;
                    transform: translateX(100%);
                    max-height: 200px;
                }
                100% {
                    opacity: 0;
                    transform: translateX(100%);
                    max-height: 0;
                    padding: 0;
                    margin: 0;
                }
            }

            .alert-removing {
                animation: alertSlideOut 0.5s ease-in-out forwards;
                overflow: hidden;
            }
        `;
        document.head.appendChild(style);
    }

    enhanceBootstrapAlerts() {
        // Find all existing Bootstrap alerts
        const alerts = document.querySelectorAll('.alert:not(.alert-enhanced)');
        
        alerts.forEach(alert => {
            this.makeAlertDismissible(alert);
            this.addAutoHide(alert);
            alert.classList.add('alert-enhanced');
        });
    }

    makeAlertDismissible(alert) {
        // Skip if already dismissible
        if (alert.querySelector('.btn-close')) return;

        // Add dismissible class
        alert.classList.add('alert-dismissible');

        // Create close button
        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'btn-close';
        closeBtn.setAttribute('aria-label', 'Close');
        closeBtn.innerHTML = '<i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>';

        // Add click event
        closeBtn.addEventListener('click', () => {
            this.dismissAlert(alert);
        });

        alert.appendChild(closeBtn);
    }

    addAutoHide(alert, timeout = this.defaultTimeout) {
        // Skip if alert is in a form (error messages should stay visible)
        if (alert.closest('form')) return;

        // Skip if alert has data-no-auto-hide attribute
        if (alert.dataset.noAutoHide === 'true') return;

        setTimeout(() => {
            if (alert.parentNode) {
                this.dismissAlert(alert);
            }
        }, timeout);
    }

    dismissAlert(alert) {
        alert.classList.add('alert-removing');
        
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 500);
    }

    // Toast notification methods
    showToast(message, type = 'info', options = {}) {
        const toast = this.createToast(message, type, options);
        const container = document.getElementById('toast-container');
        
        container.appendChild(toast);
        
        // Trigger show animation
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);

        // Auto hide
        const timeout = options.timeout !== undefined ? options.timeout : this.defaultTimeout;
        if (timeout > 0) {
            this.scheduleToastRemoval(toast, timeout);
        }

        return toast;
    }

    createToast(message, type, options) {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        
        const icons = {
            success: '<i class="ki-duotone ki-check-circle fs-2 toast-icon"><span class="path1"></span><span class="path2"></span></i>',
            danger: '<i class="ki-duotone ki-cross-circle fs-2 toast-icon"><span class="path1"></span><span class="path2"></span></i>',
            warning: '<i class="ki-duotone ki-information fs-2 toast-icon"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>',
            info: '<i class="ki-duotone ki-information fs-2 toast-icon"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>',
            primary: '<i class="ki-duotone ki-information fs-2 toast-icon"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>'
        };

        const titles = {
            success: 'Sucesso!',
            danger: 'Erro!',
            warning: 'Atenção!',
            info: 'Informação',
            primary: 'Notificação'
        };

        toast.innerHTML = `
            <div class="toast-header">
                <div class="d-flex align-items-center">
                    ${icons[type] || icons.info}
                    <strong class="me-auto ms-2">${options.title || titles[type]}</strong>
                </div>
                <button type="button" class="btn-close ms-2" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                </button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
            ${options.timeout > 0 ? '<div class="progress-bar" style="width: 100%;"></div>' : ''}
        `;

        // Add close functionality
        const closeBtn = toast.querySelector('.btn-close');
        closeBtn.addEventListener('click', () => {
            this.hideToast(toast);
        });

        return toast;
    }

    scheduleToastRemoval(toast, timeout) {
        // Animate progress bar if present
        const progressBar = toast.querySelector('.progress-bar');
        if (progressBar) {
            progressBar.style.transition = `width ${timeout}ms linear`;
            setTimeout(() => {
                progressBar.style.width = '0%';
            }, 10);
        }

        setTimeout(() => {
            this.hideToast(toast);
        }, timeout);
    }

    hideToast(toast) {
        toast.classList.add('hide');
        toast.classList.remove('show');
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 300);
    }

    // Convenience methods
    success(message, options = {}) {
        return this.showToast(message, 'success', options);
    }

    error(message, options = {}) {
        return this.showToast(message, 'danger', options);
    }

    warning(message, options = {}) {
        return this.showToast(message, 'warning', options);
    }

    info(message, options = {}) {
        return this.showToast(message, 'info', options);
    }

    primary(message, options = {}) {
        return this.showToast(message, 'primary', options);
    }

    // Method to enhance new alerts added dynamically
    enhanceNewAlert(alert, autoHide = true) {
        this.makeAlertDismissible(alert);
        if (autoHide) {
            this.addAutoHide(alert);
        }
        alert.classList.add('alert-enhanced');
    }

    // Clear all toasts
    clearAllToasts() {
        const toasts = document.querySelectorAll('.toast-notification');
        toasts.forEach(toast => this.hideToast(toast));
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Create global instance
    window.AlertManager = new AlertManager();

    // Create convenient global functions
    window.showAlert = {
        success: (message, options) => window.AlertManager.success(message, options),
        error: (message, options) => window.AlertManager.error(message, options),
        warning: (message, options) => window.AlertManager.warning(message, options),
        info: (message, options) => window.AlertManager.info(message, options),
        primary: (message, options) => window.AlertManager.primary(message, options)
    };

    // Enhance any alerts that were added after page load
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) { // Element node
                    // Check if the added node is an alert
                    if (node.classList && node.classList.contains('alert') && !node.classList.contains('alert-enhanced')) {
                        window.AlertManager.enhanceNewAlert(node);
                    }
                    
                    // Check for alerts within the added node
                    const alerts = node.querySelectorAll && node.querySelectorAll('.alert:not(.alert-enhanced)');
                    if (alerts) {
                        alerts.forEach(alert => window.AlertManager.enhanceNewAlert(alert));
                    }
                }
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AlertManager;
}