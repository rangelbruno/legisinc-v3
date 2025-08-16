@extends('components.layouts.app')

@section('title', 'Protocolo de Proposições')

@section('content')
<style>
/* Estilos modernos e limpos */
.protocol-table {
    border: none;
}

.protocol-table thead th {
    background: #f8f9fa;
    border: none;
    font-weight: 600;
    color: #495057;
    padding: 1rem;
    font-size: 0.875rem;
}

.protocol-table tbody td {
    padding: 1.25rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f4;
}

.protocol-table tbody tr:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s ease;
}

.protocol-table tbody tr:last-child td {
    border-bottom: none;
}

/* Badge customizado */
.badge-tipo {
    padding: 0.5rem 1rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.75rem;
}

/* Card de proposição */
.proposicao-card {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.proposicao-titulo {
    font-size: 1rem;
    font-weight: 600;
    color: #212529;
    margin-bottom: 0.25rem;
}

.proposicao-ementa {
    font-size: 0.875rem;
    color: #6c757d;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Autor info */
.autor-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.autor-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #009ef7 0%, #0077d4 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1rem;
}

.autor-detalhes {
    display: flex;
    flex-direction: column;
}

.autor-nome {
    font-weight: 600;
    color: #212529;
    font-size: 0.875rem;
}

.autor-cargo {
    font-size: 0.75rem;
    color: #6c757d;
}

/* Status de tempo */
.tempo-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.tempo-badge.urgente {
    background-color: #fff3cd;
    color: #856404;
}

.tempo-badge.normal {
    background-color: #d1ecf1;
    color: #0c5460;
}

.tempo-badge.atrasado {
    background-color: #f8d7da;
    color: #721c24;
}

/* Botão de protocolar melhorado */
.btn-protocolar {
    background: linear-gradient(135deg, #009ef7 0%, #0077d4 100%);
    border: none;
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 500;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-protocolar:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 158, 247, 0.3);
    color: white;
}

/* Cards de estatísticas */
.stat-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.stat-icon.warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%);
    color: #856404;
}

.stat-icon.success {
    background: linear-gradient(135deg, #d4edda 0%, #b1dfbb 100%);
    color: #155724;
}

.stat-icon.info {
    background: linear-gradient(135deg, #d1ecf1 0%, #a8d8ea 100%);
    color: #004085;
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .protocol-table {
        font-size: 0.875rem;
    }
    
    .hide-mobile {
        display: none !important;
    }
    
    .protocol-table tbody td {
        padding: 1rem 0.75rem;
    }
    
    .proposicao-ementa {
        -webkit-line-clamp: 1;
    }
}
</style>

<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Protocolo de Proposições
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Protocolo</li>
                </ul>
            </div>
            <!--end::Page title-->
            
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('proposicoes.protocolos-hoje') }}" class="btn btn-sm btn-light-success">
                    <i class="ki-duotone ki-calendar-tick fs-4 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                        <span class="path5"></span>
                        <span class="path6"></span>
                    </i>
                    Protocoladas Hoje
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <x-alerts.flash />

            <!--begin::Stats Row-->
            <div class="row g-5 mb-5">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon warning">
                                <i class="ki-duotone ki-time">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <div>
                                <div class="fs-2 fw-bold text-gray-900">{{ $proposicoes->count() }}</div>
                                <div class="fs-7 text-muted">Aguardando Protocolo</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon success">
                                <i class="ki-duotone ki-check-circle">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <div>
                                <div class="fs-2 fw-bold text-gray-900">{{ $proposicoes->where('assinatura_digital', '!=', null)->count() }}</div>
                                <div class="fs-7 text-muted">Assinadas Digitalmente</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon info">
                                <i class="ki-duotone ki-timer">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </div>
                            <div>
                                <div class="fs-2 fw-bold text-gray-900">
                                    @php
                                        $mediaHoras = $proposicoes->avg(function($p) {
                                            return $p->data_assinatura ? $p->data_assinatura->diffInHours(now()) : 0;
                                        });
                                    @endphp
                                    {{ number_format($mediaHoras, 0) }}h
                                </div>
                                <div class="fs-7 text-muted">Tempo Médio de Espera</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Stats Row-->

            <!--begin::Table Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <h3 class="fw-bold mb-0">Proposições para Protocolação</h3>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex align-items-center position-relative">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" 
                                   id="search-proposicoes" 
                                   class="form-control form-control-solid ps-12" 
                                   placeholder="Buscar proposição..."
                                   style="min-width: 250px;">
                        </div>
                    </div>
                </div>
                <!--end::Card header-->
                
                <!--begin::Card body-->
                <div class="card-body py-4">
                    @if($proposicoes->count() > 0)
                        <!--begin::Table-->
                        <div class="table-responsive">
                            <table class="table protocol-table">
                                <!--begin::Table head-->
                                <thead>
                                    <tr>
                                        <th style="width: 35%;">Proposição</th>
                                        <th style="width: 20%;" class="hide-mobile">Autor</th>
                                        <th style="width: 15%;" class="hide-mobile">Assinatura</th>
                                        <th style="width: 15%;" class="text-center">Tempo</th>
                                        <th style="width: 15%;" class="text-end">Ação</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                
                                <!--begin::Table body-->
                                <tbody>
                                    @foreach($proposicoes as $proposicao)
                                    <tr>
                                        <td>
                                            <div class="proposicao-card">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="badge badge-light-primary badge-tipo">
                                                        {{ $proposicao->tipo }}
                                                    </span>
                                                    @if($proposicao->urgencia === 'urgentissima')
                                                        <span class="badge badge-light-danger">Urgentíssima</span>
                                                    @elseif($proposicao->urgencia === 'urgente')
                                                        <span class="badge badge-light-warning">Urgente</span>
                                                    @endif
                                                </div>
                                                <div class="proposicao-titulo">
                                                    Proposição #{{ $proposicao->id }}
                                                </div>
                                                <div class="proposicao-ementa">
                                                    {{ $proposicao->ementa }}
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td class="hide-mobile">
                                            <div class="autor-info">
                                                <div class="autor-avatar">
                                                    {{ substr($proposicao->autor->name ?? 'N', 0, 1) }}
                                                </div>
                                                <div class="autor-detalhes">
                                                    <div class="autor-nome">{{ $proposicao->autor->name ?? 'N/A' }}</div>
                                                    <div class="autor-cargo">{{ $proposicao->autor->cargo_atual ?? 'Parlamentar' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td class="hide-mobile">
                                            @if($proposicao->data_assinatura)
                                                <div class="d-flex flex-column">
                                                    <span class="text-success fw-semibold">
                                                        <i class="ki-duotone ki-shield-tick fs-5 me-1">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        Assinada
                                                    </span>
                                                    <span class="text-muted fs-7">
                                                        {{ $proposicao->data_assinatura->format('d/m/Y H:i') }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-muted">Não assinada</span>
                                            @endif
                                        </td>
                                        
                                        <td class="text-center">
                                            @php
                                                $horas = $proposicao->data_assinatura ? 
                                                    $proposicao->data_assinatura->diffInHours(now()) : 
                                                    $proposicao->created_at->diffInHours(now());
                                                $classe = $horas < 24 ? 'normal' : ($horas < 48 ? 'urgente' : 'atrasado');
                                            @endphp
                                            <span class="tempo-badge {{ $classe }}">
                                                <i class="ki-duotone ki-time fs-6">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                @if($horas < 1)
                                                    {{ $proposicao->data_assinatura ? 
                                                        $proposicao->data_assinatura->diffInMinutes(now()) : 
                                                        $proposicao->created_at->diffInMinutes(now()) }}min
                                                @elseif($horas < 24)
                                                    {{ $horas }}h
                                                @else
                                                    {{ floor($horas / 24) }}d
                                                @endif
                                            </span>
                                        </td>
                                        
                                        <td class="text-end">
                                            <a href="{{ route('proposicoes.protocolar.show', $proposicao) }}" 
                                               class="btn-protocolar">
                                                <i class="ki-duotone ki-files-tablet fs-5">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Protocolar
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <!--end::Table body-->
                            </table>
                        </div>
                        <!--end::Table-->
                        
                        <!--begin::Pagination-->
                        @if($proposicoes->hasPages())
                        <div class="d-flex justify-content-between align-items-center pt-6">
                            <div class="text-muted fs-7">
                                Mostrando {{ $proposicoes->firstItem() }} a {{ $proposicoes->lastItem() }} 
                                de {{ $proposicoes->total() }} proposições
                            </div>
                            {{ $proposicoes->links() }}
                        </div>
                        @endif
                        <!--end::Pagination-->
                    @else
                        <!--begin::Empty State-->
                        <div class="text-center py-10">
                            <img src="/assets/media/illustrations/empty.svg" 
                                 class="mw-200px mb-5" 
                                 alt="Sem proposições">
                            <h3 class="text-gray-800 mb-3">Nenhuma proposição aguardando protocolo</h3>
                            <p class="text-muted">
                                Todas as proposições assinadas já foram protocoladas.
                            </p>
                        </div>
                        <!--end::Empty State-->
                    @endif
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Table Card-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Busca em tempo real
    const searchInput = document.getElementById('search-proposicoes');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchValue = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.protocol-table tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });
    }
});
</script>

@endsection