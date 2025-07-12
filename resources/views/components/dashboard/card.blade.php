@props([
    'icon' => '',
    'iconClass' => 'text-white',
    'title' => '',
    'value' => '0',
    'valueUnit' => '',
    'subtitle' => '',
    'badge' => '',
    'badgeType' => 'primary',
    'progress' => 0,
    'cardType' => 'primary',
    'href' => null,
    'colSize' => 'col-xl-3 col-lg-6 col-md-6 col-sm-12'
])

<div class="{{ $colSize }}">
    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-{{ $cardType }}">
        <div class="card-header pt-5 pb-3">
            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                <i class="ki-duotone {{ $icon }} {{ $iconClass }} fs-2x">
                    @if(str_contains($icon, 'ki-people'))
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                        <span class="path5"></span>
                    @elseif(str_contains($icon, 'ki-document'))
                        <span class="path1"></span>
                        <span class="path2"></span>
                    @elseif(str_contains($icon, 'ki-calendar'))
                        <span class="path1"></span>
                        <span class="path2"></span>
                    @elseif(str_contains($icon, 'ki-questionnaire-tablet'))
                        <span class="path1"></span>
                        <span class="path2"></span>
                    @elseif(str_contains($icon, 'ki-check-circle'))
                        <span class="path1"></span>
                        <span class="path2"></span>
                    @elseif(str_contains($icon, 'ki-abstract-39'))
                        <span class="path1"></span>
                        <span class="path2"></span>
                    @elseif(str_contains($icon, 'ki-timer'))
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    @endif
                </i>
            </div>
        </div>
        
        <div class="card-body d-flex flex-column justify-content-end pt-0">
            <div class="d-flex align-items-center mb-3">
                <span class="fs-2hx fw-bold text-white me-2">{{ $value }}</span>
                @if($valueUnit)
                    <span class="fs-6 fw-semibold text-white opacity-75">{{ $valueUnit }}</span>
                @endif
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                @if($href)
                    <a href="{{ $href }}" class="fs-6 fw-bold text-white text-hover-primary text-decoration-none">{{ $title }}</a>
                @else
                    <span class="fs-6 fw-bold text-white">{{ $title }}</span>
                @endif
                
                @if($badge)
                    <span class="badge badge-light-{{ $badgeType }} fs-8">{{ $badge }}</span>
                @endif
            </div>
            
            <div class="progress h-6px bg-white bg-opacity-50">
                <div class="progress-bar bg-white" style="width: {{ $progress }}%"></div>
            </div>
        </div>
    </div>
</div>