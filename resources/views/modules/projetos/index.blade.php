@extends('components.layouts.app')

@section('title', 'Projetos de Lei')

@section('content')
<style>
.projetos-card-primary {
    background: linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.projetos-card-info {
    background: linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.projetos-card-success {
    background: linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.projetos-card-warning {
    background: linear-gradient(135deg, #FFC700 0%, #e6b300 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #FFC700 0%, #e6b300 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
</style>

<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Projetos de Lei
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Projetos</li>
                </ul>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if(isset($error))
                <div class="alert alert-danger d-flex align-items-center mb-10">
                    <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-danger">Erro</h4>
                        <span>{{ $error }}</span>
                    </div>
                </div>
            @endif
            
            {{-- Estatísticas --}}
            <div class="row gy-5 gx-xl-8 mb-5">
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 projetos-card-primary">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-document text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['total'] ?? 0 }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">projetos</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Total de Projetos</span>
                                <span class="badge badge-light-primary fs-8">{{ ($estatisticas['total'] ?? 0) > 0 ? round((($estatisticas['este_ano'] ?? 0) / $estatisticas['total']) * 100) : 0 }}%</span>
                            </div>
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ ($estatisticas['total'] ?? 0) > 0 ? (($estatisticas['este_ano'] ?? 0) / $estatisticas['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 projetos-card-warning">
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
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['em_tramitacao'] ?? 0 }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">em tramitação</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Em Tramitação</span>
                                <span class="badge badge-light-warning fs-8">{{ ($estatisticas['em_tramitacao'] ?? 0) > 0 ? round((($estatisticas['urgentes'] ?? 0) / $estatisticas['em_tramitacao']) * 100) : 0 }}%</span>
                            </div>
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ ($estatisticas['em_tramitacao'] ?? 0) > 0 ? (($estatisticas['urgentes'] ?? 0) / $estatisticas['em_tramitacao']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 projetos-card-success">
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
                                <span class="fs-2hx fw-bold text-white me-2">{{ collect($estatisticas['por_status'] ?? [])->sum() }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">por status</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Distribuição</span>
                                <span class="badge badge-light-success fs-8">{{ count($estatisticas['por_status'] ?? []) }}</span>
                            </div>
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ count($estatisticas['por_status'] ?? []) > 0 ? 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 projetos-card-info">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-category text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ collect($estatisticas['por_tipo'] ?? [])->sum() }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">por tipo</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Categorias</span>
                                <span class="badge badge-light-info fs-8">{{ count($estatisticas['por_tipo'] ?? []) }}</span>
                            </div>
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ count($estatisticas['por_tipo'] ?? []) > 0 ? 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Seção de Detalhes --}}
            <div class="row g-5 g-xl-8 mb-5">
                <div class="col-xl-6">
                    <div class="card card-flush h-100 mb-5 mb-xl-10">
                        <div class="card-header pt-5 pb-3 border-0 bg-light-success">
                            <div class="card-title align-items-start flex-column w-100">
                                <h3 class="card-label fw-bold text-gray-900 mb-1">Distribuição por Status</h3>
                                <div class="text-muted fw-semibold fs-7">Situação atual dos projetos</div>
                            </div>
                        </div>
                        <div class="card-body pt-3 pb-5">
                            <div class="d-flex flex-column gap-4">
                                @forelse($estatisticas['por_status'] ?? [] as $status => $total)
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40px me-4">
                                            <div class="symbol-label bg-light-primary">
                                                <i class="ki-duotone ki-status text-primary fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-gray-900 fw-bold fs-6 mb-1">{{ ucfirst(str_replace('_', ' ', $status)) }}</div>
                                            <div class="text-gray-600 fs-7 mb-2">{{ $total }} {{ $total == 1 ? 'projeto' : 'projetos' }}</div>
                                            <div class="progress h-5px bg-light-primary">
                                                <div class="progress-bar bg-primary" style="width: {{ ($estatisticas['total'] ?? 0) > 0 ? ($total / $estatisticas['total']) * 100 : 0 }}%"></div>
                                            </div>
                                        </div>
                                        <div class="text-end ms-3">
                                            <span class="badge badge-primary fs-8 fw-bold mb-1">{{ ($estatisticas['total'] ?? 0) > 0 ? round(($total / $estatisticas['total']) * 100) : 0 }}%</span>
                                            <div class="text-gray-500 fs-8">{{ $total }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-10">
                                        <div class="text-gray-500 fs-6">Nenhum dado disponível</div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-6">
                    <div class="card card-flush h-100 mb-5 mb-xl-10">
                        <div class="card-header pt-5 pb-3 border-0 bg-light-info">
                            <div class="card-title align-items-start flex-column w-100">
                                <h3 class="card-label fw-bold text-gray-900 mb-1">Distribuição por Tipo</h3>
                                <div class="text-muted fw-semibold fs-7">Categorias dos projetos</div>
                            </div>
                        </div>
                        <div class="card-body pt-3 pb-5">
                            <div class="d-flex flex-column gap-4">
                                @forelse($estatisticas['por_tipo'] ?? [] as $tipo => $total)
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40px me-4">
                                            <div class="symbol-label bg-light-info">
                                                <i class="ki-duotone ki-category text-info fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                    <span class="path4"></span>
                                                </i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-gray-900 fw-bold fs-6 mb-1">{{ ucfirst($tipo) }}</div>
                                            <div class="text-gray-600 fs-7 mb-2">{{ $total }} {{ $total == 1 ? 'projeto' : 'projetos' }}</div>
                                            <div class="progress h-5px bg-light-info">
                                                <div class="progress-bar bg-info" style="width: {{ ($estatisticas['total'] ?? 0) > 0 ? ($total / $estatisticas['total']) * 100 : 0 }}%"></div>
                                            </div>
                                        </div>
                                        <div class="text-end ms-3">
                                            <span class="badge badge-info fs-8 fw-bold mb-1">{{ ($estatisticas['total'] ?? 0) > 0 ? round(($total / $estatisticas['total']) * 100) : 0 }}%</span>
                                            <div class="text-gray-500 fs-8">{{ $total }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-10">
                                        <div class="text-gray-500 fs-6">Nenhum dado disponível</div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filtros --}}
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" data-kt-projeto-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Buscar projeto..." />
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-projeto-table-toolbar="base">
                            <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-filter fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Filtros
                            </button>
                            <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                                <div class="px-7 py-5">
                                    <div class="fs-5 text-dark fw-bold">Filtros Avançados</div>
                                </div>
                                <div class="separator border-gray-200"></div>
                                <form method="GET" action="{{ route('projetos.index') }}">
                                    <div class="px-7 py-5">
                                        <div class="mb-5">
                                            <label class="form-label fw-semibold">Tipo:</label>
                                            <select class="form-select form-select-solid" name="tipo">
                                                <option value="">Todos</option>
                                                @foreach($opcoes['tipos'] ?? [] as $key => $nome)
                                                    <option value="{{ $key }}" {{ (request('tipo') == $key) ? 'selected' : '' }}>{{ $nome }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-5">
                                            <label class="form-label fw-semibold">Status:</label>
                                            <select class="form-select form-select-solid" name="status">
                                                <option value="">Todos</option>
                                                @foreach($opcoes['status'] ?? [] as $key => $nome)
                                                    <option value="{{ $key }}" {{ (request('status') == $key) ? 'selected' : '' }}>{{ $nome }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-5">
                                            <label class="form-label fw-semibold">Urgência:</label>
                                            <select class="form-select form-select-solid" name="urgencia">
                                                <option value="">Todas</option>
                                                @foreach($opcoes['urgencias'] ?? [] as $key => $nome)
                                                    <option value="{{ $key }}" {{ (request('urgencia') == $key) ? 'selected' : '' }}>{{ $nome }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-5">
                                            <label class="form-label fw-semibold">Ano:</label>
                                            <input type="number" class="form-control form-control-solid" name="ano" value="{{ request('ano') }}" min="2020" max="{{ date('Y') + 5 }}" placeholder="Ex: {{ date('Y') }}" />
                                        </div>
                                        <div class="form-check mb-5">
                                            <input class="form-check-input" type="checkbox" name="urgentes" value="1" {{ request('urgentes') ? 'checked' : '' }} id="filtro_urgentes">
                                            <label class="form-check-label fw-semibold" for="filtro_urgentes">
                                                Apenas urgentes
                                            </label>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2">Limpar</button>
                                            <button type="submit" class="btn btn-sm btn-primary">Aplicar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <a href="{{ route('projetos.create') }}" class="btn btn-primary">
                                <i class="ki-duotone ki-plus fs-2"></i>
                                Novo Projeto
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="table-responsive">
                        <table class="table table-rounded table-striped border gy-7 gs-7" id="kt_table_projetos">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom-2 border-gray-200">
                                    <th class="min-w-200px">Projeto</th>
                                    <th class="min-w-100px">Número</th>
                                    <th class="min-w-125px">Tipo</th>
                                    <th class="min-w-125px">Status</th>
                                    <th class="min-w-125px">Autor</th>
                                    <th class="min-w-125px">Comissão</th>
                                    <th class="min-w-100px">Urgência</th>
                                    <th class="min-w-125px">Criado em</th>
                                    <th class="text-end min-w-100px">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($projetos as $projeto)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <a href="{{ route('projetos.show', $projeto->id) }}" class="text-gray-800 text-hover-primary mb-1 fs-6 fw-bold">{{ $projeto->titulo }}</a>
                                            <span class="text-muted fw-semibold d-block fs-7">{{ Str::limit($projeto->ementa, 80) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $projeto->numero_completo ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="badge badge-light-primary fw-bold">{{ $projeto->tipo_formatado ?? $projeto->tipo }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $statusColor = match($projeto->status) {
                                                'rascunho' => 'secondary',
                                                'protocolado' => 'primary',
                                                'em_tramitacao' => 'warning',
                                                'na_comissao' => 'info',
                                                'aprovado' => 'success',
                                                'rejeitado' => 'danger',
                                                'arquivado' => 'dark',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <div class="badge badge-light-{{ $statusColor }} fw-bold">{{ $projeto->status_formatado ?? ucfirst($projeto->status) }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $projeto->autor->name ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $projeto->comissao->nome ?? '-' }}</div>
                                    </td>
                                    <td>
                                        @if($projeto->urgencia === 'urgente')
                                            <div class="badge badge-light-danger fw-bold">Urgente</div>
                                        @elseif($projeto->urgencia === 'prioritario')
                                            <div class="badge badge-light-warning fw-bold">Prioritário</div>
                                        @else
                                            <div class="badge badge-light-secondary fw-bold">Normal</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $projeto->created_at->format('d/m/Y') }}</div>
                                        <div class="text-muted fs-7">{{ $projeto->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="text-end">
                                        <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            Ações
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                        </a>
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                            <div class="menu-item px-3">
                                                <a href="{{ route('projetos.show', $projeto->id) }}" class="menu-link px-3">Ver</a>
                                            </div>
                                            <div class="menu-item px-3">
                                                <a href="{{ route('projetos.edit', $projeto->id) }}" class="menu-link px-3">Editar</a>
                                            </div>
                                            @if($projeto->podeEditarConteudo())
                                            <div class="menu-item px-3">
                                                <a href="{{ route('projetos.editor', $projeto->id) }}" class="menu-link px-3" target="_blank">Editor</a>
                                            </div>
                                            @endif
                                            <div class="menu-item px-3">
                                                <a href="{{ route('projetos.versoes', $projeto->id) }}" class="menu-link px-3">Versões</a>
                                            </div>
                                            <div class="menu-item px-3">
                                                <a href="{{ route('projetos.tramitacao', $projeto->id) }}" class="menu-link px-3">Tramitação</a>
                                            </div>
                                            <div class="menu-item px-3">
                                                <a href="{{ route('projetos.anexos', $projeto->id) }}" class="menu-link px-3">Anexos</a>
                                            </div>
                                            @if($projeto->status === 'rascunho')
                                            <div class="separator my-2"></div>
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3" data-kt-projetos-table-action="protocolar" data-projeto-id="{{ $projeto->id }}">
                                                    Protocolar
                                                </a>
                                            </div>
                                            @endif
                                            @if(in_array($projeto->status, ['protocolado', 'em_tramitacao']))
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3" data-kt-projetos-table-action="encaminhar-comissao" data-projeto-id="{{ $projeto->id }}">
                                                    Encaminhar
                                                </a>
                                            </div>
                                            @endif
                                            @if($projeto->isRascunho())
                                            <div class="separator my-2"></div>
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3 text-danger" data-kt-projetos-table-action="delete" data-projeto-id="{{ $projeto->id }}">
                                                    Excluir
                                                </a>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-10">
                                        <div class="text-gray-500 fs-6">
                                            Nenhum projeto encontrado.
                                            <br>
                                            <a href="{{ route('projetos.create') }}" class="btn btn-sm btn-primary mt-3">
                                                <i class="ki-duotone ki-plus fs-3"></i>
                                                Criar primeiro projeto
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Paginação --}}
                    @if($projetos->hasPages())
                    <div class="d-flex flex-stack flex-wrap pt-10">
                        <div class="fs-6 fw-semibold text-gray-700">
                            Exibindo {{ $projetos->firstItem() }} a {{ $projetos->lastItem() }} de {{ $projetos->total() }} projetos
                        </div>
                        <ul class="pagination">
                            {{ $projetos->links() }}
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Protocolar projeto
        document.querySelectorAll('[data-kt-projetos-table-action="protocolar"]').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                const projetoId = this.getAttribute('data-projeto-id');
                
                if (confirm('Tem certeza que deseja protocolar este projeto? Após protocolado, não poderá ser editado.')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/projetos/${projetoId}/protocolar`;
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    form.appendChild(csrfInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Excluir projeto
        document.querySelectorAll('[data-kt-projetos-table-action="delete"]').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                const projetoId = this.getAttribute('data-projeto-id');
                
                if (confirm('Tem certeza que deseja excluir este projeto? Esta ação não pode ser desfeita.')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/projetos/${projetoId}`;
                    
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    form.appendChild(methodInput);
                    form.appendChild(csrfInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Busca em tempo real
        const searchInput = document.querySelector('[data-kt-projeto-table-filter="search"]');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const searchTerm = this.value;
                    if (searchTerm.length >= 3 || searchTerm.length === 0) {
                        // Fazer busca via AJAX
                        fetch(`/projetos/buscar?termo=${encodeURIComponent(searchTerm)}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Atualizar tabela com resultados
                                    console.log('Resultados da busca:', data.projetos);
                                }
                            });
                    }
                }, 500);
            });
        }
    });
</script>
@endpush
@endsection