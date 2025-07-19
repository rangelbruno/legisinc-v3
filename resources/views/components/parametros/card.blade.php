@props([
    'title',
    'subtitle' => '',
    'icon' => '',
    'color' => 'primary',
    'route' => null,
    'count' => null,
    'status' => null,
    'description' => '',
    'actions' => []
])

<div class="col-12 col-xl-6 col-lg-12 mb-xl-5 mb-5">
    <div class="infobox-3 parametro-card" data-color="{{ $color }}">
        <div class="info-icon" style="background-color: {{ $this->getColorCode($color) }}">
            @if($icon)
                <i class="{{ $icon }}"></i>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" 
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            @endif
        </div>
        
        <div class="info-content">
            <h5 class="info-heading mb-3">
                {{ $title }}
                @if($count !== null)
                    <span class="badge badge-{{ $color }} ml-2">{{ $count }}</span>
                @endif
                @if($status)
                    <span class="status-indicator status-{{ $status }}" title="Status: {{ ucfirst($status) }}"></span>
                @endif
            </h5>
            
            @if($subtitle)
                <p class="info-subtitle text-muted mb-2">{{ $subtitle }}</p>
            @endif
            
            @if($description)
                <p class="info-text">{{ $description }}</p>
            @endif
            
            {{ $slot }}
        </div>
        
        <div class="info-actions">
            @if($route)
                <a href="{{ $route }}" class="btn btn-outline-{{ $color }}">
                    <i class="fas fa-arrow-right"></i> ENTRAR
                </a>
            @endif
            
            @foreach($actions as $action)
                <a href="{{ $action['route'] }}" 
                   class="btn btn-{{ $action['type'] ?? 'secondary' }} {{ $action['class'] ?? '' }}"
                   @if(isset($action['onclick'])) onclick="{{ $action['onclick'] }}" @endif
                   @if(isset($action['target'])) target="{{ $action['target'] }}" @endif>
                    @if(isset($action['icon']))
                        <i class="{{ $action['icon'] }}"></i>
                    @endif
                    {{ $action['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
    <style>
        .parametro-card {
            position: relative;
            background: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            min-height: 200px;
            display: flex;
            flex-direction: column;
        }
        
        .parametro-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .parametro-card .info-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        .parametro-card .info-content {
            flex: 1;
        }
        
        .parametro-card .info-heading {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .parametro-card .info-subtitle {
            font-size: 0.875rem;
            margin-bottom: 0.75rem;
        }
        
        .parametro-card .info-text {
            font-size: 0.875rem;
            line-height: 1.5;
            color: #6c757d;
        }
        
        .parametro-card .info-actions {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .parametro-card .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .parametro-card .status-indicator.status-active {
            background-color: #28a745;
        }
        
        .parametro-card .status-indicator.status-inactive {
            background-color: #dc3545;
        }
        
        .parametro-card .status-indicator.status-pending {
            background-color: #ffc107;
        }
        
        .parametro-card[data-color="primary"] .info-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .parametro-card[data-color="success"] .info-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .parametro-card[data-color="warning"] .info-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .parametro-card[data-color="danger"] .info-icon {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
        }
        
        .parametro-card[data-color="info"] .info-icon {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }
        
        @media (max-width: 768px) {
            .parametro-card {
                margin-bottom: 1rem !important;
            }
            
            .parametro-card .info-actions {
                flex-direction: column;
            }
            
            .parametro-card .info-actions .btn {
                width: 100%;
            }
        }
    </style>
@endpush

@php
function getColorCode($color) {
    $colors = [
        'primary' => '#805dca',
        'success' => '#1abc9c',
        'warning' => '#f39c12',
        'danger' => '#e74c3c',
        'info' => '#3498db',
        'secondary' => '#6c757d'
    ];
    
    return $colors[$color] ?? $colors['primary'];
}
@endphp