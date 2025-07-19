@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'options' => [],
    'multiple' => false,
    'help' => '',
    'validation' => '',
    'class' => 'form-control',
    'containerClass' => 'form-group col-md-6'
])

<div class="{{ $containerClass }}">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>

    @switch($type)
        @case('text')
        @case('email')
        @case('password')
        @case('number')
        @case('url')
            <input 
                type="{{ $type }}" 
                id="{{ $name }}" 
                name="{{ $name }}" 
                class="{{ $class }} @error($name) is-invalid @enderror"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($validation) data-validation="{{ $validation }}" @endif
            />
            @break

        @case('textarea')
            <textarea 
                id="{{ $name }}" 
                name="{{ $name }}" 
                class="{{ $class }} @error($name) is-invalid @enderror"
                placeholder="{{ $placeholder }}"
                rows="4"
                @if($required) required @endif
                @if($disabled) disabled @endif
            >{{ old($name, $value) }}</textarea>
            @break

        @case('select')
            <select 
                id="{{ $name }}" 
                name="{{ $name }}{{ $multiple ? '[]' : '' }}" 
                class="{{ $class }} @error($name) is-invalid @enderror"
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($multiple) multiple @endif
            >
                @if(!$multiple && !$required)
                    <option value="">Selecione...</option>
                @endif
                @foreach($options as $optionValue => $optionLabel)
                    <option 
                        value="{{ $optionValue }}" 
                        @if(old($name, $value) == $optionValue || (is_array(old($name, $value)) && in_array($optionValue, old($name, $value)))) selected @endif
                    >
                        {{ $optionLabel }}
                    </option>
                @endforeach
            </select>
            @break

        @case('checkbox')
            <div class="custom-switch">
                <label class="switch s-icons s-outline s-outline-success mb-4 mr-2">
                    <input 
                        type="checkbox" 
                        id="{{ $name }}" 
                        name="{{ $name }}" 
                        value="1"
                        @if(old($name, $value)) checked @endif
                        @if($disabled) disabled @endif
                    />
                    <span class="slider round"></span>
                </label>
            </div>
            @break

        @case('radio')
            @foreach($options as $optionValue => $optionLabel)
                <div class="form-check">
                    <input 
                        type="radio" 
                        id="{{ $name }}_{{ $optionValue }}" 
                        name="{{ $name }}" 
                        value="{{ $optionValue }}"
                        class="form-check-input @error($name) is-invalid @enderror"
                        @if(old($name, $value) == $optionValue) checked @endif
                        @if($disabled) disabled @endif
                    />
                    <label class="form-check-label" for="{{ $name }}_{{ $optionValue }}">
                        {{ $optionLabel }}
                    </label>
                </div>
            @endforeach
            @break

        @case('file')
            <input 
                type="file" 
                id="{{ $name }}" 
                name="{{ $name }}" 
                class="{{ $class }} @error($name) is-invalid @enderror"
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($multiple) multiple @endif
            />
            @break

        @case('date')
        @case('datetime-local')
        @case('time')
            <input 
                type="{{ $type }}" 
                id="{{ $name }}" 
                name="{{ $name }}" 
                class="{{ $class }} @error($name) is-invalid @enderror"
                value="{{ old($name, $value) }}"
                @if($required) required @endif
                @if($disabled) disabled @endif
            />
            @break

        @case('json')
            <textarea 
                id="{{ $name }}" 
                name="{{ $name }}" 
                class="{{ $class }} @error($name) is-invalid @enderror"
                placeholder="{{ $placeholder ?: 'Formato JSON válido' }}"
                rows="6"
                @if($required) required @endif
                @if($disabled) disabled @endif
                data-json-editor="true"
            >{{ old($name, is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value) }}</textarea>
            @break

        @default
            <input 
                type="text" 
                id="{{ $name }}" 
                name="{{ $name }}" 
                class="{{ $class }} @error($name) is-invalid @enderror"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
                @if($required) required @endif
                @if($disabled) disabled @endif
            />
    @endswitch

    @if($help)
        <small class="form-text text-muted">{{ $help }}</small>
    @endif

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@push('scripts')
    @if($type === 'json')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const jsonEditors = document.querySelectorAll('[data-json-editor="true"]');
                
                jsonEditors.forEach(function(editor) {
                    editor.addEventListener('blur', function() {
                        try {
                            const value = this.value.trim();
                            if (value) {
                                const parsed = JSON.parse(value);
                                this.value = JSON.stringify(parsed, null, 2);
                                this.classList.remove('is-invalid');
                            }
                        } catch (e) {
                            this.classList.add('is-invalid');
                            const feedback = this.parentNode.querySelector('.invalid-feedback') || 
                                           this.parentNode.appendChild(document.createElement('div'));
                            feedback.className = 'invalid-feedback';
                            feedback.textContent = 'JSON inválido: ' + e.message;
                            feedback.style.display = 'block';
                        }
                    });
                });
            });
        </script>
    @endif
@endpush