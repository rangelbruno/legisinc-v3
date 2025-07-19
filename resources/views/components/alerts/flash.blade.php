@if(session('success') || session('error') || session('warning') || session('info'))
    @if(session('success'))
        <div class="alert alert-success temp-flash-alert">
            <i class="ki-duotone ki-check-circle fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger temp-flash-alert">
            <i class="ki-duotone ki-cross-circle fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger temp-flash-alert">
            <i class="ki-duotone ki-cross-circle fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            <strong>Erro:</strong> Verifique os campos abaixo:
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning temp-flash-alert">
            <i class="ki-duotone ki-information fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
            {{ session('warning') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info temp-flash-alert">
            <i class="ki-duotone ki-information fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
            {{ session('info') }}
        </div>
    @endif

    <script>
    (function() {
        // Create alert container immediately if it doesn't exist
        if (!document.getElementById('alert-container')) {
            const container = document.createElement('div');
            container.id = 'alert-container';
            container.className = 'alert-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1081';
            container.style.maxWidth = '100vw';
            container.style.overflow = 'hidden';
            container.style.pointerEvents = 'none';
            document.body.appendChild(container);
        }
        
        // Find and process all flash alerts
        const alerts = document.querySelectorAll('.temp-flash-alert');
        alerts.forEach(alert => {
            const container = document.getElementById('alert-container');
            
            // Clone and enhance the alert
            const clonedAlert = alert.cloneNode(true);
            clonedAlert.classList.remove('temp-flash-alert');
            clonedAlert.classList.add('alert-dismissible', 'alert-enhanced');
            
            // Apply modern styles
            clonedAlert.style.position = 'relative';
            clonedAlert.style.minWidth = '380px';
            clonedAlert.style.maxWidth = '450px';
            clonedAlert.style.paddingRight = '4rem';
            clonedAlert.style.paddingLeft = '1.5rem';
            clonedAlert.style.paddingTop = '1.25rem';
            clonedAlert.style.paddingBottom = '1.25rem';
            clonedAlert.style.marginBottom = '0.75rem';
            clonedAlert.style.transform = 'translateX(100%)';
            clonedAlert.style.transition = 'all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
            clonedAlert.style.opacity = '0';
            clonedAlert.style.boxShadow = '0 15px 35px rgba(0, 0, 0, 0.25), 0 5px 15px rgba(0, 0, 0, 0.15)';
            clonedAlert.style.border = 'none';
            clonedAlert.style.borderRadius = '12px';
            clonedAlert.style.pointerEvents = 'all';
            clonedAlert.style.backgroundColor = '#ffffff';
            clonedAlert.style.backdropFilter = 'none';
            clonedAlert.style.fontSize = '14px';
            clonedAlert.style.fontWeight = '500';
            clonedAlert.style.color = '#212529';
            
            // Responsive styles for mobile
            if (window.innerWidth <= 576) {
                clonedAlert.style.minWidth = '280px';
                clonedAlert.style.maxWidth = 'calc(100vw - 2rem)';
            } else if (window.innerWidth <= 768) {
                clonedAlert.style.minWidth = '320px';
                clonedAlert.style.maxWidth = 'calc(100vw - 2rem)';
            }
            
            // Apply type-specific styles with solid white background
            if (clonedAlert.classList.contains('alert-success')) {
                clonedAlert.style.borderLeft = '4px solid #198754';
                clonedAlert.style.backgroundColor = '#ffffff';
                clonedAlert.style.border = '1px solid rgba(25, 135, 84, 0.2)';
            } else if (clonedAlert.classList.contains('alert-danger')) {
                clonedAlert.style.borderLeft = '4px solid #dc3545';
                clonedAlert.style.backgroundColor = '#ffffff';
                clonedAlert.style.border = '1px solid rgba(220, 53, 69, 0.2)';
            } else if (clonedAlert.classList.contains('alert-warning')) {
                clonedAlert.style.borderLeft = '4px solid #ffc107';
                clonedAlert.style.backgroundColor = '#ffffff';
                clonedAlert.style.border = '1px solid rgba(255, 193, 7, 0.2)';
            } else if (clonedAlert.classList.contains('alert-info')) {
                clonedAlert.style.borderLeft = '4px solid #0dcaf0';
                clonedAlert.style.backgroundColor = '#ffffff';
                clonedAlert.style.border = '1px solid rgba(13, 202, 240, 0.2)';
            }

            // Add close button if not exists
            if (!clonedAlert.querySelector('.btn-close')) {
                const closeBtn = document.createElement('button');
                closeBtn.type = 'button';
                closeBtn.className = 'btn-close';
                closeBtn.setAttribute('aria-label', 'Close');
                closeBtn.style.position = 'absolute';
                closeBtn.style.top = '12px';
                closeBtn.style.right = '12px';
                closeBtn.style.zIndex = '2';
                closeBtn.style.width = '32px';
                closeBtn.style.height = '32px';
                closeBtn.style.padding = '0';
                closeBtn.style.background = 'rgba(0, 0, 0, 0.15)';
                closeBtn.style.border = '0';
                closeBtn.style.borderRadius = '50%';
                closeBtn.style.opacity = '0.7';
                closeBtn.style.cursor = 'pointer';
                closeBtn.style.display = 'flex';
                closeBtn.style.alignItems = 'center';
                closeBtn.style.justifyContent = 'center';
                closeBtn.style.transition = 'all 0.2s ease';
                closeBtn.innerHTML = '<i class="ki-duotone ki-cross fs-4"><span class="path1"></span><span class="path2"></span></i>';
                
                closeBtn.addEventListener('mouseenter', function() {
                    this.style.opacity = '1';
                    this.style.background = 'rgba(0, 0, 0, 0.25)';
                    this.style.transform = 'scale(1.1)';
                });
                
                closeBtn.addEventListener('mouseleave', function() {
                    this.style.opacity = '0.7';
                    this.style.background = 'rgba(0, 0, 0, 0.15)';
                    this.style.transform = 'scale(1)';
                });
                
                closeBtn.addEventListener('click', function() {
                    clonedAlert.style.transform = 'translateX(100%)';
                    clonedAlert.style.opacity = '0';
                    setTimeout(() => {
                        if (clonedAlert.parentNode) {
                            clonedAlert.remove();
                        }
                    }, 400);
                });
                
                clonedAlert.appendChild(closeBtn);
            }
            
            // Remove original and add to container
            alert.remove();
            container.appendChild(clonedAlert);
            
            // Show with animation
            setTimeout(() => {
                clonedAlert.style.transform = 'translateX(0) scale(1)';
                clonedAlert.style.opacity = '1';
            }, 50);
            
            // Add progress bar for auto-hide
            const progressBar = document.createElement('div');
            progressBar.style.position = 'absolute';
            progressBar.style.bottom = '0';
            progressBar.style.left = '0';
            progressBar.style.height = '3px';
            progressBar.style.backgroundColor = clonedAlert.classList.contains('alert-success') ? '#198754' :
                                                clonedAlert.classList.contains('alert-danger') ? '#dc3545' :
                                                clonedAlert.classList.contains('alert-warning') ? '#ffc107' :
                                                clonedAlert.classList.contains('alert-info') ? '#0dcaf0' : '#6c757d';
            progressBar.style.borderRadius = '0 0 12px 12px';
            progressBar.style.width = '100%';
            progressBar.style.transition = 'width 6s linear';
            progressBar.style.opacity = '0.3';
            clonedAlert.appendChild(progressBar);
            
            // Start progress bar animation
            setTimeout(() => {
                progressBar.style.width = '0%';
            }, 100);
            
            // Auto-hide after 6 seconds
            setTimeout(() => {
                if (clonedAlert.parentNode) {
                    clonedAlert.style.transform = 'translateX(100%) scale(0.9)';
                    clonedAlert.style.opacity = '0';
                    setTimeout(() => {
                        if (clonedAlert.parentNode) {
                            clonedAlert.remove();
                        }
                    }, 400);
                }
            }, 6000);
        });
    })();
    </script>
@endif