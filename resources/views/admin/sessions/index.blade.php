@extends('components.layouts.app')

@section('title', 'Sessões')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Sessões
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Sessões</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('admin.sessions.create') }}" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-plus fs-2"></i>
                    Nova Sessão
                </a>
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

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="ki-duotone ki-check-circle fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="ki-duotone ki-cross-circle fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ session('error') }}
                </div>
            @endif
            
            <!--begin::Row-->
            <div class="row g-5 g-xl-8 mb-xl-8">
                <!--begin::Col-->
                <div class="col-xl-3">
                    <!--begin::Statistics Widget 5-->
                    <div class="card bg-body hoverable card-xl-stretch mb-5 mb-xl-8">
                        <div class="card-body">
                            <i class="ki-duotone ki-calendar fs-2x text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                                {{ count($sessions) }}
                            </div>
                            <div class="fw-semibold text-gray-400">Total de Sessões</div>
                        </div>
                    </div>
                    <!--end::Statistics Widget 5-->
                </div>
                <!--end::Col-->
                
                <!--begin::Col-->
                <div class="col-xl-3">
                    <!--begin::Statistics Widget 5-->
                    <div class="card bg-body hoverable card-xl-stretch mb-5 mb-xl-8">
                        <div class="card-body">
                            <i class="ki-duotone ki-timer fs-2x text-warning">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                                {{ count(array_filter($sessions, function($s) { return $s['status'] === 'preparacao'; })) }}
                            </div>
                            <div class="fw-semibold text-gray-400">Em Preparação</div>
                        </div>
                    </div>
                    <!--end::Statistics Widget 5-->
                </div>
                <!--end::Col-->
                
                <!--begin::Col-->
                <div class="col-xl-3">
                    <!--begin::Statistics Widget 5-->
                    <div class="card bg-body hoverable card-xl-stretch mb-5 mb-xl-8">
                        <div class="card-body">
                            <i class="ki-duotone ki-check-circle fs-2x text-success">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                                {{ count(array_filter($sessions, function($s) { return $s['status'] === 'agendada'; })) }}
                            </div>
                            <div class="fw-semibold text-gray-400">Agendadas</div>
                        </div>
                    </div>
                    <!--end::Statistics Widget 5-->
                </div>
                <!--end::Col-->
                
                <!--begin::Col-->
                <div class="col-xl-3">
                    <!--begin::Statistics Widget 5-->
                    <div class="card bg-body hoverable card-xl-stretch mb-5 mb-xl-8">
                        <div class="card-body">
                            <i class="ki-duotone ki-document fs-2x text-info">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                                {{ count(array_filter($sessions, function($s) { return $s['status'] === 'exportada'; })) }}
                            </div>
                            <div class="fw-semibold text-gray-400">Exportadas</div>
                        </div>
                    </div>
                    <!--end::Statistics Widget 5-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
            
            <!--begin::Card-->
            <div class="card">
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
                            <form action="{{ route('admin.sessions.index') }}" method="GET">
                                <input type="text" name="q" 
                                       class="form-control form-control-solid w-250px ps-13" 
                                       placeholder="Buscar sessões..." 
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
                                    <div class="fs-5 text-dark fw-bold">Filtrar Sessões</div>
                                </div>
                                <!--end::Header-->
                                
                                <!--begin::Separator-->
                                <div class="separator border-gray-200"></div>
                                <!--end::Separator-->
                                
                                <!--begin::Content-->
                                <form action="{{ route('admin.sessions.index') }}" method="GET">
                                    <div class="px-7 py-5">
                                        <!--begin::Input group-->
                                        <div class="mb-10">
                                            <label class="form-label fw-semibold">Tipo:</label>
                                            <div>
                                                <select class="form-select" name="tipo_id">
                                                    <option value="">Todos os tipos</option>
                                                    @foreach($tipos_sessao as $id => $nome)
                                                        <option value="{{ $id }}" 
                                                            {{ ($filtros['tipo_id'] ?? '') == $id ? 'selected' : '' }}>
                                                            {{ $nome }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <!--end::Input group-->
                                        
                                        <!--begin::Input group-->
                                        <div class="mb-10">
                                            <label class="form-label fw-semibold">Ano:</label>
                                            <div>
                                                <select class="form-select" name="ano">
                                                    <option value="">Todos os anos</option>
                                                    @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                                                        <option value="{{ $year }}" 
                                                            {{ ($filtros['ano'] ?? '') == $year ? 'selected' : '' }}>
                                                            {{ $year }}
                                                        </option>
                                                    @endfor
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
                                                    <option value="preparacao" {{ ($filtros['status'] ?? '') == 'preparacao' ? 'selected' : '' }}>Preparação</option>
                                                    <option value="agendada" {{ ($filtros['status'] ?? '') == 'agendada' ? 'selected' : '' }}>Agendada</option>
                                                    <option value="exportada" {{ ($filtros['status'] ?? '') == 'exportada' ? 'selected' : '' }}>Exportada</option>
                                                    <option value="concluida" {{ ($filtros['status'] ?? '') == 'concluida' ? 'selected' : '' }}>Concluída</option>
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
                                    <th class="min-w-150px">Sessão</th>
                                    <th class="min-w-125px">Data/Hora</th>
                                    <th class="min-w-100px">Tipo</th>
                                    <th class="min-w-100px">Status</th>
                                    <th class="min-w-100px">Matérias</th>
                                    <th class="text-end min-w-150px">Ações</th>
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            
                            <!--begin::Table body-->
                            <tbody class="text-gray-600 fw-semibold">
                                @forelse($sessions as $session)
                                    <tr>
                                        <td class="d-flex align-items-center">
                                            <!--begin::Session details-->
                                            <div class="d-flex flex-column">
                                                <a href="{{ route('admin.sessions.show', $session['id']) }}" 
                                                   class="text-gray-800 text-hover-primary mb-1">
                                                    {{ $session['numero'] }}ª Sessão de {{ $session['ano'] }}
                                                </a>
                                                <span class="text-muted">{{ $session['observacoes'] ?? 'Sem observações' }}</span>
                                            </div>
                                            <!--end::Session details-->
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-800 fw-bold mb-1">
                                                    {{ \Carbon\Carbon::parse($session['data'])->format('d/m/Y') }}
                                                </span>
                                                <span class="text-muted">{{ $session['hora'] }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-info fs-7 fw-bold">
                                                {{ $session['tipo_descricao'] }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($session['status'] == 'preparacao')
                                                <span class="badge badge-light-warning">Preparação</span>
                                            @elseif($session['status'] == 'agendada')
                                                <span class="badge badge-light-primary">Agendada</span>
                                            @elseif($session['status'] == 'exportada')
                                                <span class="badge badge-light-success">Exportada</span>
                                            @elseif($session['status'] == 'concluida')
                                                <span class="badge badge-light-dark">Concluída</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-light fs-7">
                                                {{ $session['total_materias'] ?? 0 }} matérias
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end flex-shrink-0">
                                                <!--begin::Export buttons-->
                                                @if(($session['total_materias'] ?? 0) > 0)
                                                    <a href="{{ route('admin.sessions.preview-xml', ['id' => $session['id'], 'document_type' => 'expediente']) }}" 
                                                       class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" 
                                                       title="Exportar Expediente">
                                                        <i class="ki-duotone ki-document-copy fs-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </a>
                                                    <a href="{{ route('admin.sessions.preview-xml', ['id' => $session['id'], 'document_type' => 'ordem_do_dia']) }}" 
                                                       class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" 
                                                       title="Exportar Ordem do Dia">
                                                        <i class="ki-duotone ki-file-down fs-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </a>
                                                @endif
                                                <!--end::Export buttons-->
                                                
                                                <!--begin::Actions dropdown-->
                                                <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    Ações
                                                    <i class="ki-duotone ki-down fs-5 m-0"></i>
                                                </a>
                                                <!--begin::Menu-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                    <div class="menu-item px-3">
                                                        <a href="{{ route('admin.sessions.show', $session['id']) }}" class="menu-link px-3">
                                                            Ver
                                                        </a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="{{ route('admin.sessions.edit', $session['id']) }}" class="menu-link px-3">
                                                            Editar
                                                        </a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3 text-danger" 
                                                           onclick="event.preventDefault(); if(confirm('Tem certeza que deseja deletar esta sessão?')) { document.getElementById('delete-form-{{ $session['id'] }}').submit(); }">
                                                            Deletar
                                                        </a>
                                                        <form id="delete-form-{{ $session['id'] }}" 
                                                              action="{{ route('admin.sessions.destroy', $session['id']) }}" 
                                                              method="POST" style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </div>
                                                </div>
                                                <!--end::Menu-->
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <div class="d-flex flex-column flex-center">
                                                <img src="{{ asset('assets/media/illustrations/sketchy-1/5.png') }}" class="mw-300px">
                                                <div class="fs-1 fw-bolder text-dark mb-4">Nenhuma sessão encontrada.</div>
                                                <div class="fs-6">Comece criando a primeira sessão do sistema.</div>
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