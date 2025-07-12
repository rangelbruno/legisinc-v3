<div class="{{ $colSize ?? 'col-xl-3' }}">
    <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
        <div class="card-body">
            <i class="ki-duotone {{ $icon ?? '' }} fs-2x {{ $iconColor ?? 'text-primary' }}">
                @if(isset($icon) && str_contains($icon, 'ki-people'))
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                    <span class="path5"></span>
                @elseif(isset($icon) && str_contains($icon, 'ki-check-circle'))
                    <span class="path1"></span>
                    <span class="path2"></span>
                @elseif(isset($icon) && str_contains($icon, 'ki-abstract-39'))
                    <span class="path1"></span>
                    <span class="path2"></span>
                @elseif(isset($icon) && str_contains($icon, 'ki-questionnaire-tablet'))
                    <span class="path1"></span>
                    <span class="path2"></span>
                @endif
            </i>
            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                {{ $value ?? '0' }}
            </div>
            <div class="fw-semibold text-gray-400">{{ $label ?? '' }}</div>
        </div>
    </div>
</div>