@props([
    'action',
    'method' => 'POST',
    'title' => '',
    'subtitle' => '',
    'campos' => [],
    'submitText' => 'Salvar',
    'cancelRoute' => null,
    'showCancel' => true,
    'enctype' => null,
    'class' => 'row'
])

<div class="widget-content widget-content-area">
    @if($title)
        <div class="mb-4">
            <h4 class="card-title">{{ $title }}</h4>
            @if($subtitle)
                <p class="card-subtitle text-muted">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <form 
        action="{{ $action }}" 
        method="{{ $method === 'GET' ? 'GET' : 'POST' }}" 
        @if($enctype) enctype="{{ $enctype }}" @endif
        class="parametros-form"
        novalidate
    >
        @csrf
        @if(!in_array($method, ['GET', 'POST']))
            @method($method)
        @endif

        <div class="{{ $class }}">
            @foreach($campos as $campo)
                <x-parametros.campo
                    :name="$campo['name']"
                    :label="$campo['label']"
                    :type="$campo['type'] ?? 'text'"
                    :value="$campo['value'] ?? null"
                    :placeholder="$campo['placeholder'] ?? ''"
                    :required="$campo['required'] ?? false"
                    :disabled="$campo['disabled'] ?? false"
                    :options="$campo['options'] ?? []"
                    :multiple="$campo['multiple'] ?? false"
                    :help="$campo['help'] ?? ''"
                    :validation="$campo['validation'] ?? ''"
                    :class="$campo['class'] ?? 'form-control'"
                    :containerClass="$campo['containerClass'] ?? 'form-group col-md-6'"
                />
            @endforeach

            {{ $slot }}
        </div>

        <div class="form-actions mt-4">
            <div class="d-flex justify-content-between">
                <div>
                    @if($showCancel && $cancelRoute)
                        <a href="{{ $cancelRoute }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    @endif
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ $submitText }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
    <style>
        .parametros-form .form-group {
            margin-bottom: 1.5rem;
        }
        
        .parametros-form .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .parametros-form .custom-switch {
            padding-top: 0.375rem;
        }
        
        .parametros-form .form-actions {
            padding-top: 1rem;
            border-top: 1px solid #dee2e6;
        }
        
        .parametros-form .invalid-feedback {
            display: block;
        }
        
        .parametros-form [data-json-editor="true"] {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.875rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validação em tempo real
            const form = document.querySelector('.parametros-form');
            const inputs = form.querySelectorAll('input, select, textarea');
            
            inputs.forEach(function(input) {
                input.addEventListener('blur', function() {
                    validateField(this);
                });
                
                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        validateField(this);
                    }
                });
            });
            
            function validateField(field) {
                const validation = field.getAttribute('data-validation');
                if (!validation) return;
                
                let isValid = true;
                let errorMessage = '';
                
                const rules = validation.split('|');
                
                for (const rule of rules) {
                    if (rule === 'required' && !field.value.trim()) {
                        isValid = false;
                        errorMessage = 'Este campo é obrigatório.';
                        break;
                    }
                    
                    if (rule.startsWith('min:')) {
                        const min = parseInt(rule.split(':')[1]);
                        if (field.value.length < min) {
                            isValid = false;
                            errorMessage = `Mínimo de ${min} caracteres.`;
                            break;
                        }
                    }
                    
                    if (rule.startsWith('max:')) {
                        const max = parseInt(rule.split(':')[1]);
                        if (field.value.length > max) {
                            isValid = false;
                            errorMessage = `Máximo de ${max} caracteres.`;
                            break;
                        }
                    }
                    
                    if (rule === 'email') {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (field.value && !emailRegex.test(field.value)) {
                            isValid = false;
                            errorMessage = 'Email inválido.';
                            break;
                        }
                    }
                    
                    if (rule === 'url') {
                        try {
                            if (field.value) new URL(field.value);
                        } catch {
                            isValid = false;
                            errorMessage = 'URL inválida.';
                            break;
                        }
                    }
                }
                
                if (isValid) {
                    field.classList.remove('is-invalid');
                    const feedback = field.parentNode.querySelector('.invalid-feedback');
                    if (feedback) feedback.style.display = 'none';
                } else {
                    field.classList.add('is-invalid');
                    let feedback = field.parentNode.querySelector('.invalid-feedback');
                    if (!feedback) {
                        feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        field.parentNode.appendChild(feedback);
                    }
                    feedback.textContent = errorMessage;
                    feedback.style.display = 'block';
                }
            }
            
            // Validação no submit
            form.addEventListener('submit', function(e) {
                let hasErrors = false;
                
                inputs.forEach(function(input) {
                    validateField(input);
                    if (input.classList.contains('is-invalid')) {
                        hasErrors = true;
                    }
                });
                
                if (hasErrors) {
                    e.preventDefault();
                    
                    // Focar no primeiro campo com erro
                    const firstError = form.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.focus();
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    
                    // Mostrar notificação
                    if (typeof swal !== 'undefined') {
                        swal('Erro!', 'Por favor, corrija os erros no formulário.', 'error');
                    } else {
                        alert('Por favor, corrija os erros no formulário.');
                    }
                }
            });
        });
    </script>
@endpush