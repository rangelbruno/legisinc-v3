@extends('components.layouts.app')

@section('title', 'Protocolo de Proposições')

@section('content')
<style>
/* Dashboard Cards - Seguindo padrão do sistema */
.dashboard-card-primary {
    background: linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
    background-image: url("/assets/media/patterns/vector-1.png"), linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}

.dashboard-card-info {
    background: linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
    background-image: url("/assets/media/patterns/vector-1.png"), linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}

.dashboard-card-success {
    background: linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
    background-image: url("/assets/media/patterns/vector-1.png"), linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}

.dashboard-card-warning {
    background: linear-gradient(135deg, #FFC700 0%, #e6b300 100%) !important;
    background-image: url("/assets/media/patterns/vector-1.png"), linear-gradient(135deg, #FFC700 0%, #e6b300 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}

/* Estilos da tabela */
.protocol-table {
    border: none;
    border-collapse: separate;
    border-spacing: 0;
}

.protocol-table thead th {
    background: #f8f9fa;
    border: none;
    font-weight: 600;
    color: #495057;
    padding: 1.25rem 1rem;
    font-size: 0.875rem;
    text-align: left;
    vertical-align: middle;
    white-space: nowrap;
}

.protocol-table tbody td {
    padding: 1.5rem;
    vertical-align: middle;
    border-bottom: 1px solid #e9ecef;
    height: 100px;
}

.protocol-table tbody td:first-child {
    padding-left: 2rem;
}

.protocol-table tbody td:last-child {
    padding-right: 2rem;
}

.protocol-table tbody tr {
    transition: all 0.2s ease;
}

.protocol-table tbody tr:hover {
    background-color: #f8fafe;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
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
    gap: 0.75rem;
    justify-content: center;
    height: 100%;
}

.proposicao-titulo {
    font-size: 1.1rem;
    font-weight: 700;
    color: #212529;
    margin-bottom: 0;
    line-height: 1.3;
}

.proposicao-ementa {
    font-size: 0.9rem;
    color: #6c757d;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 0;
}

/* Autor info */
.autor-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    justify-content: flex-start;
    height: 100%;
}

.autor-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, #009ef7 0%, #0077d4 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0, 158, 247, 0.3);
}

.autor-detalhes {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    justify-content: center;
}

.autor-nome {
    font-weight: 600;
    color: #212529;
    font-size: 0.95rem;
    line-height: 1.3;
    margin-bottom: 0;
}

.autor-cargo {
    font-size: 0.8rem;
    color: #6c757d;
    font-weight: 500;
    margin-bottom: 0;
}

/* Status de tempo */
.tempo-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 1.5rem;
    font-size: 0.85rem;
    font-weight: 600;
    border: 2px solid transparent;
    transition: all 0.2s ease;
}

.tempo-badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.tempo-badge.urgente {
    background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%);
    color: #856404;
    border-color: #ffecb3;
}

.tempo-badge.normal {
    background: linear-gradient(135deg, #d1ecf1 0%, #b8dce6 100%);
    color: #0c5460;
    border-color: #bee5eb;
}

.tempo-badge.atrasado {
    background: linear-gradient(135deg, #f8d7da 0%, #f1b5bb 100%);
    color: #721c24;
    border-color: #f5c6cb;
}

/* Botão de protocolar melhorado */
.btn-protocolar {
    background: linear-gradient(135deg, #009ef7 0%, #0077d4 100%);
    border: none;
    color: white;
    padding: 0.75rem 1.75rem;
    border-radius: 0.75rem;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(0, 158, 247, 0.2);
    white-space: nowrap;
}

.btn-protocolar:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 158, 247, 0.4);
    color: white;
    background: linear-gradient(135deg, #0077d4 0%, #005fb8 100%);
}

.btn-protocolar:active {
    transform: translateY(-1px);
}

/* Espaçamento da tabela */
.table-responsive {
    padding: 0 1rem;
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
        padding: 1rem 1rem;
    }
    
    .protocol-table tbody td:first-child {
        padding-left: 1.5rem;
    }
    
    .protocol-table tbody td:last-child {
        padding-right: 1.5rem;
    }
    
    .proposicao-ementa {
        -webkit-line-clamp: 1;
    }
    
    .table-responsive {
        padding: 0 0.5rem;
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
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-warning cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-time text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $proposicoes->count() }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">aguardando</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Aguardando Protocolo</span>
                                <span class="badge badge-light-warning fs-8">{{ $proposicoes->count() > 0 ? 100 : 0 }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $proposicoes->count() > 0 ? 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-success cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-check-circle text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $proposicoes->where('assinatura_digital', '!=', null)->count() }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">assinadas</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Assinadas Digitalmente</span>
                                <span class="badge badge-light-success fs-8">{{ $proposicoes->count() > 0 ? round(($proposicoes->where('assinatura_digital', '!=', null)->count() / $proposicoes->count()) * 100) : 0 }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $proposicoes->count() > 0 ? round(($proposicoes->where('assinatura_digital', '!=', null)->count() / $proposicoes->count()) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 dashboard-card-info cursor-pointer">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-timer text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                @php
                                    $mediaHoras = $proposicoes->avg(function($p) {
                                        return $p->data_assinatura ? $p->data_assinatura->diffInHours(now()) : 0;
                                    });
                                @endphp
                                <span class="fs-2hx fw-bold text-white me-2">{{ number_format($mediaHoras, 0) }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">horas</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Tempo Médio de Espera</span>
                                <span class="badge badge-light-info fs-8">{{ $mediaHoras > 0 ? 100 : 0 }}%</span>
                            </div>
                            
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $mediaHoras > 0 ? 100 : 0 }}%"></div>
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
                                        <th style="width: 35%; padding-left: 2rem;">Proposição</th>
                                        <th style="width: 20%; padding-left: 1.5rem;" class="hide-mobile">Autor</th>
                                        <th style="width: 15%; padding-left: 1.5rem;" class="hide-mobile">Assinatura</th>
                                        <th style="width: 15%; text-align: center;">Tempo</th>
                                        <th style="width: 15%; text-align: center; padding-right: 2rem;">Ação</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                
                                <!--begin::Table body-->
                                <tbody>
                                    @foreach($proposicoes as $proposicao)
                                    <tr>
                                        <td>
                                            <div class="proposicao-card" style="padding-left: 0.5rem;">
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
                                            <div class="autor-info" style="padding-left: 0.5rem;">
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
                                            <div class="d-flex align-items-center justify-content-start h-100" style="padding-left: 0.5rem;">
                                                @if($proposicao->data_assinatura)
                                                    <div class="d-flex flex-column gap-2">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="d-flex align-items-center justify-content-center" 
                                                                 style="width: 28px; height: 28px; background: linear-gradient(135deg, #17C653 0%, #13a342 100%); border-radius: 50%;">
                                                                <i class="ki-duotone ki-shield-tick text-white fs-6">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                            </div>
                                                            <span class="text-success fw-bold fs-7">Assinada</span>
                                                        </div>
                                                        <span class="text-muted fs-8 ps-1">
                                                            {{ $proposicao->data_assinatura->format('d/m/Y H:i') }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="d-flex align-items-center justify-content-center" 
                                                             style="width: 28px; height: 28px; background: linear-gradient(135deg, #6c757d 0%, #495057 100%); border-radius: 50%;">
                                                            <i class="ki-duotone ki-time text-white fs-6">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                        </div>
                                                        <span class="text-muted fw-semibold fs-7">Não assinada</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        
                                        <td class="text-center">
                                            @php
                                                $horas = $proposicao->data_assinatura ? 
                                                    $proposicao->data_assinatura->diffInHours(now()) : 
                                                    $proposicao->created_at->diffInHours(now());
                                                $classe = $horas < 24 ? 'normal' : ($horas < 48 ? 'urgente' : 'atrasado');
                                                
                                                // Formatar tempo de forma mais legível
                                                if ($horas < 1) {
                                                    $minutos = $proposicao->data_assinatura ? 
                                                        $proposicao->data_assinatura->diffInMinutes(now()) : 
                                                        $proposicao->created_at->diffInMinutes(now());
                                                    $tempoFormatado = $minutos . 'min';
                                                } elseif ($horas < 24) {
                                                    $tempoFormatado = number_format($horas, 0) . 'h';
                                                } else {
                                                    $dias = floor($horas / 24);
                                                    $tempoFormatado = $dias . 'd';
                                                }
                                            @endphp
                                            <div class="d-flex justify-content-center">
                                                <span class="tempo-badge {{ $classe }}">
                                                    <i class="ki-duotone ki-time fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    {{ $tempoFormatado }}
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <td class="text-end">
                                            <div class="d-flex justify-content-center align-items-center h-100">
                                                <a href="{{ route('proposicoes.protocolar.show', $proposicao) }}" 
                                                   class="btn-protocolar">
                                                    <i class="ki-duotone ki-files-tablet fs-5">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Protocolar
                                                </a>
                                            </div>
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