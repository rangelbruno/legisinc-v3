@extends('components.layouts.app')

@section('title', $title ?? 'Parlamentares')

@section('content')
<style>
.dashboard-card-primary {
    background: linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.dashboard-card-info {
    background: linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.dashboard-card-success {
    background: linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.dashboard-card-warning {
    background: linear-gradient(135deg, #FFC700 0%, #e6b300 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #FFC700 0%, #e6b300 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
</style>
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Parlamentares
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Parlamentares</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!--begin::Export menu-->
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-light-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ki-duotone ki-file-down fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Exportar
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('parlamentares.export.csv') }}">
                            <i class="ki-duotone ki-document fs-2 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Exportar CSV
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('parlamentares.estatisticas') }}">
                            <i class="ki-duotone ki-chart-simple fs-2 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                            Estatísticas Avançadas
                        </a></li>
                    </ul>
                </div>
                <!--end::Export menu-->
                
                @if(\App\Models\ScreenPermission::userCanAccessRoute('parlamentares.mesa-diretora'))
                <a href="{{ route('parlamentares.mesa-diretora') }}" class="btn btn-sm fw-bold btn-light-info">
                    <i class="ki-duotone ki-crown fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Mesa Diretora
                </a>
                @endif
                
                @if(\App\Models\ScreenPermission::userCanAccessRoute('parlamentares.create'))
                <a href="{{ route('parlamentares.create') }}" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-plus fs-2"></i>
                    Novo Parlamentar
                </a>
                @endif
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if(isset($error))
                <div class="alert alert-danger">
                    <i class="ki-duotone ki-cross-circle fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ $error }}
                </div>
            @endif

            <x-alerts.flash />
            
            <!--begin::Row-->
            <div class="row gy-5 gx-xl-8">
                <x-dashboard.card
                    icon="ki-people"
                    title="Parlamentares"
                    value="{{ $estatisticas['total'] ?? 0 }}"
                    progress="100"
                    cardType="primary"
                    colSize="col-xl-3"
                />
                
                <x-dashboard.card
                    icon="ki-check-circle"
                    title="Ativos"
                    value="{{ $estatisticas['ativos'] ?? 0 }}"
                    progress="95"
                    cardType="success"
                    colSize="col-xl-3"
                />
                
                <x-dashboard.card
                    icon="ki-abstract-39"
                    title="Partidos"
                    value="{{ count($estatisticas['por_partido'] ?? []) }}"
                    progress="80"
                    cardType="warning"
                    colSize="col-xl-3"
                />
                
                <x-dashboard.card
                    icon="ki-questionnaire-tablet"
                    title="Inativos"
                    value="{{ $estatisticas['inativos'] ?? 0 }}"
                    progress="10"
                    cardType="info"
                    colSize="col-xl-3"
                />
            </div>
            <!--end::Row-->
            
            <!--begin::Card-->
            <div class="card mt-5 mt-xl-8">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-6">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <!--begin::Search-->
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <form action="{{ route('parlamentares.search') }}" method="GET">
                                <input type="text" name="q" 
                                       class="form-control form-control-solid w-250px ps-13" 
                                       placeholder="Buscar parlamentares..." 
                                       value="{{ request('q') }}" />
                            </form>
                        </div>
                        <!--end::Search-->
                    </div>
                    <!--begin::Card title-->
                    
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar">
                        <!--begin::Toolbar-->
                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                            <!--begin::Filter-->
                            <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-filter fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Filtros
                            </button>
                            <!--begin::Menu 1-->
                            <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                                <!--begin::Header-->
                                <div class="px-7 py-5">
                                    <div class="fs-5 text-dark fw-bold">Filtrar Parlamentares</div>
                                </div>
                                <!--end::Header-->
                                
                                <!--begin::Separator-->
                                <div class="separator border-gray-200"></div>
                                <!--end::Separator-->
                                
                                <!--begin::Content-->
                                <form action="{{ route('parlamentares.index') }}" method="GET">
                                    <div class="px-7 py-5">
                                        <!--begin::Input group-->
                                        <div class="mb-10">
                                            <label class="form-label fw-semibold">Partido:</label>
                                            <div>
                                                <select class="form-select" name="partido">
                                                    <option value="">Todos os partidos</option>
                                                    @if(isset($estatisticas['por_partido']))
                                                        @foreach($estatisticas['por_partido'] as $partido => $count)
                                                            <option value="{{ $partido }}" 
                                                                {{ ($filtros['partido'] ?? '') == $partido ? 'selected' : '' }}>
                                                                {{ $partido }} ({{ $count }})
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <!--end::Input group-->
                                        
                                        <!--begin::Input group-->
                                        <div class="mb-10">
                                            <label class="form-label fw-semibold">Status:</label>
                                            <div>
                                                <select class="form-select" name="status">
                                                    <option value="">Todos os status</option>
                                                    <option value="ativo" {{ ($filtros['status'] ?? '') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                                    <option value="licenciado" {{ ($filtros['status'] ?? '') == 'licenciado' ? 'selected' : '' }}>Licenciado</option>
                                                    <option value="inativo" {{ ($filtros['status'] ?? '') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--end::Input group-->
                                        
                                        <!--begin::Actions-->
                                        <div class="d-flex justify-content-end">
                                            <button type="reset" class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6">Reset</button>
                                            <button type="submit" class="btn btn-primary fw-semibold px-6">Aplicar</button>
                                        </div>
                                        <!--end::Actions-->
                                    </div>
                                </form>
                                <!--end::Content-->
                            </div>
                            <!--end::Menu 1-->
                            <!--end::Filter-->
                        </div>
                        <!--end::Toolbar-->
                    </div>
                    <!--end::Card toolbar-->
                </div>
                <!--end::Card header-->
                
                <!--begin::Card body-->
                <div class="card-body py-4">
                    <!--begin::Table-->
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                            <!--begin::Table head-->
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-125px">Parlamentar</th>
                                    <th class="min-w-100px">Partido</th>
                                    <th class="min-w-125px">Cargo</th>
                                    <th class="min-w-100px">Status</th>
                                    <th class="min-w-100px">Comissões</th>
                                    <th class="min-w-100px">Contato</th>
                                    <th class="text-end min-w-100px">Ações</th>
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            
                            <!--begin::Table body-->
                            <tbody class="text-gray-600 fw-semibold">
                                @forelse($parlamentares as $parlamentar)
                                    <tr>
                                        <td class="d-flex align-items-center">
                                            <!--begin::User details-->
                                            <div class="d-flex flex-column">
                                                <a href="{{ route('parlamentares.show', $parlamentar['id']) }}" 
                                                   class="text-gray-800 text-hover-primary mb-1">
                                                    {{ $parlamentar['nome'] }}
                                                </a>
                                                <span>{{ $parlamentar['profissao'] }}</span>
                                            </div>
                                            <!--begin::User details-->
                                        </td>
                                        <td>
                                            <span class="badge badge-light-primary fs-7 fw-bold">
                                                {{ $parlamentar['partido'] }}
                                            </span>
                                        </td>
                                        <td>{{ $parlamentar['cargo'] }}</td>
                                        <td>
                                            @if($parlamentar['status'] == 'Ativo')
                                                <span class="badge badge-light-success">{{ $parlamentar['status'] }}</span>
                                            @elseif($parlamentar['status'] == 'Licenciado')
                                                <span class="badge badge-light-warning">{{ $parlamentar['status'] }}</span>
                                            @else
                                                <span class="badge badge-light-danger">{{ $parlamentar['status'] }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-light">{{ $parlamentar['total_comissoes'] }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <a href="mailto:{{ $parlamentar['email'] }}" class="text-gray-600 text-hover-primary mb-1">
                                                    {{ $parlamentar['email'] }}
                                                </a>
                                                <span>{{ $parlamentar['telefone'] }}</span>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                Ações
                                                <i class="ki-duotone ki-down fs-5 m-0"></i>
                                            </a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('parlamentares.show', $parlamentar['id']) }}" class="menu-link px-3">
                                                        Ver
                                                    </a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('parlamentares.edit', $parlamentar['id']) }}" class="menu-link px-3">
                                                        Editar
                                                    </a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-users-table-filter="delete_row" 
                                                       onclick="event.preventDefault(); if(confirm('Tem certeza que deseja deletar este parlamentar?')) { document.getElementById('delete-form-{{ $parlamentar['id'] }}').submit(); }">
                                                        Deletar
                                                    </a>
                                                    <form id="delete-form-{{ $parlamentar['id'] }}" 
                                                          action="{{ route('parlamentares.destroy', $parlamentar['id']) }}" 
                                                          method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="d-flex flex-column flex-center">
                                                <img src="{{ asset('assets/media/illustrations/sketchy-1/5.png') }}" class="mw-300px">
                                                <div class="fs-1 fw-bolder text-dark mb-4">Nenhum parlamentar encontrado.</div>
                                                <div class="fs-6">Comece adicionando o primeiro parlamentar ao sistema.</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <!--end::Table body-->
                        </table>
                    </div>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection