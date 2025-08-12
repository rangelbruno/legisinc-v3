@extends('components.layouts.app')

@section('title', 'Sistema de Parâmetros Modular')

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Sistema de Parâmetros
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Administração</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Parâmetros</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!--begin::View toggle-->
                <div class="btn-group" role="group" aria-label="View toggle">
                    <button type="button" class="btn btn-sm btn-light" id="view-cards" data-view="cards">
                        <i class="ki-duotone ki-grid fs-3"></i>
                        Cards
                    </button>
                    <button type="button" class="btn btn-sm btn-light-primary" id="view-table" data-view="table">
                        <i class="ki-duotone ki-row-horizontal fs-3"></i>
                        Tabela
                    </button>
                </div>
                <!--end::View toggle-->
                <!--begin::Secondary button-->
                <button type="button" class="btn btn-sm btn-flex btn-secondary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_module">
                    <i class="ki-duotone ki-plus fs-3"></i>
                    Novo Módulo
                </button>
                <!--end::Secondary button-->
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Alert-->
            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-check fs-2hx text-success me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h5 class="mb-1">Sucesso</h5>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h5 class="mb-1">Erro</h5>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif
            <!--end::Alert-->

            <!--begin::Cards View-->
            <div id="cards-view" class="view-container">
                <!--begin::Row-->
                <div class="row g-6 g-xl-9">
                    <!--begin::Special Card - Dados Gerais da Câmara-->
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <!--begin::Card-->
                        <div class="card card-flush h-xl-100 border-primary border-2">
                            <!--begin::Card header-->
                            <div class="card-header pt-5">
                                <!--begin::Card title-->
                                <div class="card-title d-flex flex-column">
                                    <!--begin::Icon-->
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="ki-duotone ki-bank fs-2x text-primary me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <h3 class="fs-5 fw-bold text-gray-900 mb-0">Dados Gerais da Câmara</h3>
                                            <span class="text-gray-500 fs-7">Configuração principal</span>
                                        </div>
                                    </div>
                                    <!--end::Icon-->
                                </div>
                                <!--end::Card title-->
                                <!--begin::Card toolbar-->
                                <div class="card-toolbar">
                                    <span class="badge badge-light-primary">
                                        <i class="ki-duotone ki-star fs-6">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Prioritário
                                    </span>
                                </div>
                                <!--end::Card toolbar-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body d-flex flex-column">
                                <!--begin::Description-->
                                <div class="flex-grow-1 mb-5">
                                    <p class="text-gray-700 fs-6 mb-0">
                                        Configure as informações institucionais da câmara municipal, incluindo dados de identificação, endereço, contatos e gestão atual.
                                    </p>
                                </div>
                                <!--end::Description-->
                                <!--begin::Progress-->
                                <div class="d-flex flex-stack">
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-light-success">
                                            Disponível
                                        </span>
                                    </div>
                                    
                                    <!--begin::Action-->
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('parametros.dados-gerais-camara') }}" class="btn btn-sm btn-primary">
                                            <i class="ki-duotone ki-setting-3 fs-6 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Configurar
                                        </a>
                                    </div>
                                    <!--end::Action-->
                                </div>
                                <!--end::Progress-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Special Card-->

                    <!--begin::Special Card - Configurações da IA-->
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <!--begin::Card-->
                        <div class="card card-flush h-xl-100 border-info border-2">
                            <!--begin::Card header-->
                            <div class="card-header pt-5">
                                <!--begin::Card title-->
                                <div class="card-title d-flex flex-column">
                                    <!--begin::Icon-->
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="ki-duotone ki-brain fs-2x text-info me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <h3 class="fs-5 fw-bold text-gray-900 mb-0">Configurações da IA</h3>
                                            <span class="text-gray-500 fs-7">Inteligência Artificial</span>
                                        </div>
                                    </div>
                                    <!--end::Icon-->
                                </div>
                                <!--end::Card title-->
                                <!--begin::Card toolbar-->
                                <div class="card-toolbar">
                                    <span class="badge badge-light-info">
                                        <i class="ki-duotone ki-technology-2 fs-6">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        IA
                                    </span>
                                </div>
                                <!--end::Card toolbar-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body d-flex flex-column">
                                <!--begin::Description-->
                                <div class="flex-grow-1 mb-5">
                                    <p class="text-gray-700 fs-6 mb-0">
                                        Configure as funcionalidades de Inteligência Artificial do sistema, incluindo API keys, modelos e preferências.
                                    </p>
                                </div>
                                <!--end::Description-->
                                <!--begin::Progress-->
                                <div class="d-flex flex-stack">
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-light-success">
                                            Disponível
                                        </span>
                                    </div>
                                    
                                    <!--begin::Action-->
                                    <div class="d-flex gap-2">
                                        @php
                                            $iaModulo = $modulos->where('nome', 'IA')->first();
                                        @endphp
                                        @if($iaModulo)
                                            <a href="{{ route('parametros.show', $iaModulo->id) }}" class="btn btn-sm btn-info">
                                        @else
                                            <a href="{{ route('parametros.configurar', 'IA') }}" class="btn btn-sm btn-info">
                                        @endif
                                            <i class="ki-duotone ki-setting-3 fs-6 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Configurar
                                        </a>
                                    </div>
                                    <!--end::Action-->
                                </div>
                                <!--end::Progress-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Special Card - IA-->
                    
                    @forelse($modulos as $modulo)
                    @if($modulo->nome !== 'IA' && $modulo->nome !== 'Dados Gerais')
                    <!--begin::Col-->
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <!--begin::Card-->
                        <div class="card card-flush h-xl-100 @if($modulo->nome === 'Templates') border-success border-2 @elseif($modulo->nome === 'Configuração de IA') border-info border-2 @else border-light @endif">
                            <!--begin::Card header-->
                            <div class="card-header pt-5">
                                <!--begin::Card title-->
                                <div class="card-title d-flex flex-column">
                                    <!--begin::Icon-->
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="ki-duotone {{ $modulo->icon ?: 'ki-setting-2' }} fs-2x @if($modulo->nome === 'Templates') text-success @else text-primary @endif me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <h3 class="fs-5 fw-bold text-gray-900 mb-0">{{ $modulo->nome }}</h3>
                                            <span class="text-gray-500 fs-7">{{ $modulo->submodulos_count ?? 0 }} submódulos</span>
                                        </div>
                                    </div>
                                    <!--end::Icon-->
                                </div>
                                <!--end::Card title-->
                                <!--begin::Card toolbar-->
                                <div class="card-toolbar">
                                    @if($modulo->nome === 'Templates')
                                        <span class="badge badge-light-success">
                                            <i class="ki-duotone ki-document fs-6">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Documentos
                                        </span>
                                    @else
                                        <span class="badge badge-light-primary">
                                            <i class="ki-duotone ki-setting-2 fs-6">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Sistema
                                        </span>
                                    @endif
                                </div>
                                <!--end::Card toolbar-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body d-flex flex-column">
                                <!--begin::Description-->
                                <div class="flex-grow-1 mb-5">
                                    <p class="text-gray-700 fs-6 mb-0">
                                        {{ $modulo->descricao ?: 'Sem descrição disponível' }}
                                    </p>
                                </div>
                                <!--end::Description-->
                                <!--begin::Progress-->
                                <div class="d-flex flex-stack">
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-light-{{ $modulo->ativo ? 'success' : 'danger' }}">
                                            {{ $modulo->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </div>
                                    
                                    <!--begin::Action-->
                                    <div class="d-flex gap-2">
                                        @if($modulo->nome === 'Templates')
                                            <a href="{{ route('parametros.show', $modulo->id) }}" class="btn btn-sm btn-success">
                                                <i class="ki-duotone ki-enter-6 fs-6 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Acessar
                                            </a>
                                        @else
                                            <a href="{{ $modulo->nome === 'Configuração de IA' ? route('parametros.configurar-ia') : route('parametros.configurar', $modulo->nome) }}" class="btn btn-sm btn-primary">
                                                <i class="ki-duotone ki-setting-3 fs-6 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Configurar
                                            </a>
                                        @endif
                                    </div>
                                    <!--end::Action-->
                                </div>
                                <!--end::Progress-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Col-->
                    @endif
                @empty
                    <!--begin::Empty state-->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body d-flex flex-center flex-column py-20">
                                <div class="text-center">
                                    <i class="ki-duotone ki-setting-2 fs-4x text-primary mb-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <h3 class="text-gray-800 mb-2">Nenhum módulo encontrado</h3>
                                    <p class="text-gray-600 fs-6 mb-6">
                                        Crie seu primeiro módulo de parâmetros para começar a configurar o sistema.
                                    </p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_module">
                                        <i class="ki-duotone ki-plus fs-3"></i>
                                        Criar Primeiro Módulo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Empty state-->
                    @endforelse
                </div>
                <!--end::Row-->
            </div>
            <!--end::Cards View-->

            <!--begin::Table View-->
            <div id="table-view" class="view-container" style="display: none;">
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
                                <input type="text" data-kt-customer-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Buscar módulos..." />
                            </div>
                            <!--end::Search-->
                        </div>
                        <!--end::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                                <!--begin::Export-->
                                <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#kt_customers_export_modal">
                                    <i class="ki-duotone ki-exit-up fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Exportar
                                </button>
                                <!--end::Export-->
                                <!--begin::Add customer-->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_module">
                                    <i class="ki-duotone ki-plus fs-2"></i>
                                    Novo Módulo
                                </button>
                                <!--end::Add customer-->
                            </div>
                            <!--end::Toolbar-->
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Table-->
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_modulos_table">
                            <thead>
                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="w-10px pe-2">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                            <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_modulos_table .form-check-input" value="1" />
                                        </div>
                                    </th>
                                    <th class="min-w-125px">Módulo</th>
                                    <th class="min-w-125px">Descrição</th>
                                    <th class="min-w-125px">Submódulos</th>
                                    <th class="min-w-125px">Status</th>
                                    <th class="min-w-125px">Criado em</th>
                                    <th class="text-end min-w-70px">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="fw-semibold text-gray-600">
                                <!-- DataTables will populate this -->
                            </tbody>
                        </table>
                        <!--end::Table-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Table View-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->

    <!--begin::Modal - Create Module-->
    <div class="modal fade" id="kt_modal_create_module" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Criar Novo Módulo</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <form id="kt_modal_create_module_form" action="{{ route('parametros.store') }}" method="POST">
                        @csrf
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-semibold form-label mb-2">Nome do Módulo</label>
                            <input type="text" class="form-control form-control-solid" name="nome" placeholder="Ex: Dados da Câmara" required />
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold form-label mb-2">Descrição</label>
                            <textarea class="form-control form-control-solid" name="descricao" rows="3" placeholder="Descrição do módulo"></textarea>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold form-label mb-2">Ícone</label>
                            <input type="text" class="form-control form-control-solid" name="icon" placeholder="Ex: ki-home" />
                            <div class="form-text">Use classes de ícones do Metronic (ex: ki-home, ki-setting-2)</div>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold form-label mb-2">Ordem</label>
                            <input type="number" class="form-control form-control-solid" name="ordem" placeholder="0" min="0" />
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ativo" id="ativo" value="1" checked />
                                <label class="form-check-label" for="ativo">
                                    Módulo ativo
                                </label>
                            </div>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Actions-->
                        <div class="text-center">
                            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">
                                <span class="indicator-label">Criar Módulo</span>
                                <span class="indicator-progress">Por favor aguarde...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                        <!--end::Actions-->
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - Create Module-->
@endsection

@push('scripts')
    <script>
        // Debug de erros JavaScript
        window.addEventListener('error', function(e) {
            console.error('❌ ERRO JAVASCRIPT:', e.error, e.filename, e.lineno);
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Sistema de parâmetros carregado');
            
            // Initialize view management
            const cardsView = document.getElementById('cards-view');
            const tableView = document.getElementById('table-view');
            const viewCardsBtn = document.getElementById('view-cards');
            const viewTableBtn = document.getElementById('view-table');
            
            let dataTable = null;
            
            // Initialize view toggle
            function switchToCardsView() {
                cardsView.style.display = 'block';
                tableView.style.display = 'none';
                viewCardsBtn.classList.remove('btn-light');
                viewCardsBtn.classList.add('btn-light-primary');
                viewTableBtn.classList.remove('btn-light-primary');
                viewTableBtn.classList.add('btn-light');
                
                // Destroy DataTable if exists
                if (dataTable) {
                    dataTable.destroy();
                    dataTable = null;
                }
            }
            
            function switchToTableView() {
                cardsView.style.display = 'none';
                tableView.style.display = 'block';
                viewTableBtn.classList.remove('btn-light');
                viewTableBtn.classList.add('btn-light-primary');
                viewCardsBtn.classList.remove('btn-light-primary');
                viewCardsBtn.classList.add('btn-light');
                
                // Initialize DataTable
                initializeDataTable();
            }
            
            // View toggle event listeners
            viewCardsBtn.addEventListener('click', switchToCardsView);
            viewTableBtn.addEventListener('click', switchToTableView);
            
            // Initialize DataTable
            function initializeDataTable() {
                if (dataTable) {
                    dataTable.destroy();
                }
                
                dataTable = $('#kt_modulos_table').DataTable({
                    ajax: {
                        url: '{{ route("ajax.modulos.parametros") }}',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        xhrFields: {
                            withCredentials: true
                        },
                        error: function(xhr, error, code) {
                            console.error('Erro ao carregar dados:', error, xhr);
                            console.error('Status:', xhr.status, 'Response:', xhr.responseText);
                            
                            // Se for erro de autenticação, mostrar aviso específico
                            if (xhr.status === 401 || xhr.status === 419) {
                                Swal.fire({
                                    title: 'Sessão Expirada',
                                    text: 'Sua sessão expirou. A página será recarregada.',
                                    icon: 'warning',
                                    confirmButtonText: 'Recarregar'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Erro ao carregar dados',
                                    text: 'Não foi possível carregar os módulos. Tente novamente.',
                                    icon: 'error',
                                    confirmButtonText: 'Entendi'
                                });
                            }
                        }
                    },
                    columns: [
                        { 
                            data: null,
                            orderable: false,
                            render: function(data, type, row) {
                                return `<div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="${row.id}" />
                                </div>`;
                            }
                        },
                        { 
                            data: 'nome',
                            render: function(data, type, row) {
                                return `<div class="d-flex align-items-center">
                                    <i class="ki-duotone ${row.icon} fs-2x text-primary me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <a href="/admin/parametros/${row.id}" class="text-gray-800 text-hover-primary mb-1 fw-bold">${data}</a>
                                    </div>
                                </div>`;
                            }
                        },
                        { 
                            data: 'descricao',
                            render: function(data, type, row) {
                                const maxLength = 60;
                                if (data.length > maxLength) {
                                    return data.substring(0, maxLength) + '...';
                                }
                                return data;
                            }
                        },
                        { 
                            data: 'submodulos_count',
                            className: 'text-center',
                            render: function(data, type, row) {
                                return `<span class="badge badge-light-info">${data}</span>`;
                            }
                        },
                        { 
                            data: 'ativo',
                            className: 'text-center',
                            render: function(data, type, row) {
                                return `<span class="badge badge-light-${row.status_badge}">${row.status_text}</span>`;
                            }
                        },
                        { 
                            data: 'created_at',
                            className: 'text-center'
                        },
                        {
                            data: null,
                            orderable: false,
                            className: 'text-end',
                            render: function(data, type, row) {
                                return `
                                    <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        Ações
                                        <i class="ki-duotone ki-down fs-5 m-0"></i>
                                    </a>
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                        <div class="menu-item px-3">
                                            <a href="/admin/parametros/${row.id}" class="menu-link px-3">Visualizar</a>
                                        </div>
                                        <div class="menu-item px-3">
                                            <a href="${row.nome === 'Configuração de IA' ? '/admin/parametros/configurar-ia' : '/admin/parametros/configurar/' + row.nome}" class="menu-link px-3">Configurar</a>
                                        </div>
                                        <div class="menu-item px-3">
                                            <a href="/admin/parametros/${row.id}/edit" class="menu-link px-3">Editar</a>
                                        </div>
                                        <div class="separator my-2"></div>
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3 text-danger" data-kt-action="delete-module" data-module-id="${row.id}">Excluir</a>
                                        </div>
                                    </div>
                                `;
                            }
                        }
                    ],
                    order: [[1, 'asc']],
                    processing: true,
                    serverSide: false,
                    pageLength: 10,
                    lengthMenu: [5, 10, 25, 50],
                    responsive: true,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
                    },
                    drawCallback: function() {
                        // Reinitialize KTMenu for the new action buttons
                        KTMenu.createInstances();
                        
                        // Reattach delete event listeners
                        attachDeleteEventListeners();
                    }
                });
                
                // Add search functionality
                const filterSearch = document.querySelector('[data-kt-customer-table-filter="search"]');
                if (filterSearch) {
                    filterSearch.addEventListener('keyup', function (e) {
                        dataTable.search(e.target.value).draw();
                    });
                }
            }
            
            // Function to attach delete event listeners
            function attachDeleteEventListeners() {
                document.querySelectorAll('[data-kt-action="delete-module"]').forEach(button => {
                    // Remove previous event listeners to avoid duplicates
                    button.replaceWith(button.cloneNode(true));
                });
                
                // Re-attach event listeners to the new elements
                document.querySelectorAll('[data-kt-action="delete-module"]').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const moduleId = this.getAttribute('data-module-id');
                        handleModuleDeletion(moduleId);
                    });
                });
            }

            // 🔐 SISTEMA DE AUTENTICAÇÃO POR TOKEN - SOLUÇÃO DEFINITIVA
            let authToken = null;
            let tokenExpireTime = null;
            
            // Obter token de autenticação
            async function obterTokenAutenticacao() {
                try {
                    console.log('🔐 Obtendo token de autenticação...');
                    
                    const response = await fetch('/auth/token', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    
                    const data = await response.json();
                    
                    if (data.success && data.token) {
                        authToken = data.token;
                        tokenExpireTime = new Date(Date.now() + (data.expires_in * 1000));
                        
                        console.log('✅ Token obtido com sucesso');
                        console.log('⏰ Expira em:', tokenExpireTime.toLocaleTimeString());
                        
                        return { success: true, token: authToken };
                    } else {
                        throw new Error(data.message || 'Erro ao obter token');
                    }
                    
                } catch (error) {
                    console.error('❌ Erro ao obter token:', error);
                    
                    if (error.message.includes('401')) {
                        return { 
                            success: false, 
                            error: 'Sessão expirada - faça login novamente',
                            needsLogin: true 
                        };
                    }
                    
                    return { 
                        success: false, 
                        error: 'Erro de conectividade: ' + error.message 
                    };
                }
            }
            
            // Verificar se token está válido
            function tokenEstaValido() {
                if (!authToken || !tokenExpireTime) {
                    return false;
                }
                
                // Verificar se não expirou (com margem de 1 minuto)
                const now = new Date();
                const expireWithBuffer = new Date(tokenExpireTime.getTime() - 60000); // 1 min de buffer
                
                return now < expireWithBuffer;
            }
            
            // Função MELHORADA de verificação de autenticação usando TOKEN
            async function verificarAutenticacao() {
                console.log('🔍 [TOKEN] Verificando autenticação com sistema de token...');
                
                // Se não tem token ou está expirado, tentar obter novo
                if (!tokenEstaValido()) {
                    console.log('🔐 Token inválido ou expirado - obtendo novo...');
                    
                    const tokenResult = await obterTokenAutenticacao();
                    
                    if (!tokenResult.success) {
                        console.log('❌ [TOKEN] Falha ao obter token:', tokenResult.error);
                        return { 
                            autenticado: false, 
                            motivo: tokenResult.error,
                            needsLogin: tokenResult.needsLogin || false
                        };
                    }
                }
                
                // Verificar token via API
                try {
                    const response = await fetch('/auth/token/verify', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${authToken}`,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        
                        if (data.valid) {
                            console.log('✅ [TOKEN] Token válido - usuário autenticado');
                            return { autenticado: true, motivo: '' };
                        }
                    }
                    
                    // Se chegou aqui, token é inválido
                    console.log('❌ [TOKEN] Token inválido ou expirado');
                    authToken = null;
                    tokenExpireTime = null;
                    
                    return { 
                        autenticado: false, 
                        motivo: 'Token de autenticação inválido - sessão expirada',
                        needsLogin: true 
                    };
                    
                } catch (error) {
                    console.error('❌ [TOKEN] Erro na verificação:', error);
                    return { 
                        autenticado: false, 
                        motivo: 'Erro de conectividade: ' + error.message 
                    };
                }
            }

            // Função SUPER SIMPLES de exclusão
            function handleModuleDeletion(moduleId) {
                console.log('🚀 Exclusão simples do módulo:', moduleId);
                
                Swal.fire({
                    title: 'Confirmar Exclusão',
                    text: 'Tem certeza que deseja excluir este módulo?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeModuleDeletion(moduleId);
                    }
                });
            }
            
            // Verificação simples de autenticação
            function verificarAutenticacaoSimples() {
                return fetch('/ajax/test-auth', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na verificação');
                    }
                    return response.json();
                })
                .catch(error => {
                    console.error('Erro na verificação de auth:', error);
                    return { authenticated: false };
                });
            }
            
            // Função de teste simplificada para debug
            function testarExclusaoSimples(moduleId) {
                console.log('🧪 Testando exclusão simples...');
                
                const formData = new FormData();
                formData.append('_method', 'DELETE');
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                
                fetch(`/ajax/test-delete/${moduleId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => {
                    console.log('🧪 Teste resposta:', response.status, response.url);
                    return response.json();
                })
                .then(data => {
                    console.log('🧪 Teste dados:', data);
                    alert('Teste OK: ' + data.message);
                })
                .catch(error => {
                    console.error('🧪 Teste erro:', error);
                    alert('Teste ERRO: ' + error.message);
                });
            }
            
            // Função para mostrar interface de sessão expirada
            function mostrarSessaoExpiradaComToken() {
                console.log('🔔 [TOKEN-EXPIRED] Mostrando interface de sessão expirada...');
                
                let countdown = 15;
                const timerSwal = Swal.fire({
                    title: 'Sessão Expirada',
                    html: `
                        <div class="text-center">
                            <i class="ki-duotone ki-time fs-3x text-warning mb-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <h4 class="mb-3">Token de Autenticação Expirado</h4>
                            <p>Sua sessão de autenticação expirou.</p>
                            <div class="alert alert-warning p-3 mb-3">
                                <strong>Você será redirecionado para login em:</strong><br>
                                <span id="countdown" class="fs-4 text-warning fw-bold">${countdown}</span> segundos
                            </div>
                            <p class="text-muted small">Você pode fazer login agora clicando no botão abaixo.</p>
                        </div>
                    `,
                    icon: null,
                    confirmButtonText: '🔄 Fazer Login Agora',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    customClass: {
                        confirmButton: 'btn btn-warning btn-lg'
                    }
                }).then(() => {
                    window.location.href = '/login';
                });
                
                // Contagem regressiva
                const timer = setInterval(() => {
                    countdown--;
                    const countdownElement = document.getElementById('countdown');
                    if (countdownElement) {
                        countdownElement.textContent = countdown;
                    }
                    
                    if (countdown <= 0) {
                        clearInterval(timer);
                        window.location.href = '/login';
                    }
                }, 1000);
            }

            // Execução via API sem CSRF - v3.0 NOVO com async/await
            async function executeModuleDeletion(moduleId) {
                console.log('🚀 Executando exclusão via API v3.0...');
                
                try {
                    const response = await fetch(`/api/parametros-modular/modulos/${moduleId}/ajax-delete`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    console.log('📨 Status v3.0:', response.status);
                    const data = await response.json();
                    console.log('📨 Data v3.0:', data);
                    
                    if (response.status === 401 || response.status === 419) {
                        window.location.href = '/login';
                        return;
                    }
                    
                    if (response.status === 200 && data.success) {
                        console.log('✅ Sucesso v3.0');
                        Swal.fire({
                            title: 'Sucesso!',
                            text: data.message || 'Módulo excluído com sucesso!',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else if (response.status === 422) {
                        console.log('⚠️ Mostrando aviso v3.0 - can_force:', data.can_force);
                        
                        if (data.can_force) {
                            Swal.fire({
                                title: 'Módulo possui dependências',
                                text: data.message + '\n\nDeseja excluir mesmo assim? Todos os submódulos e campos relacionados serão removidos permanentemente.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Sim, excluir tudo!',
                                cancelButtonText: 'Cancelar',
                                confirmButtonColor: '#d33',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    console.log('🔥 Usuário confirmou exclusão forçada');
                                    executeForceModuleDeletion(moduleId);
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Não é possível excluir',
                                text: data.message || 'Erro de validação',
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            });
                        }
                    } else {
                        console.log('❌ Erro desconhecido v3.0');
                        Swal.fire({
                            title: 'Erro',
                            text: data.message || `Erro ${response.status}`,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                } catch (error) {
                    console.error('❌ Erro de rede v3.0:', error);
                    Swal.fire({
                        title: 'Erro de conexão',
                        text: 'Erro ao conectar com o servidor',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            }
            
            // Execução forçada de exclusão v3.0
            async function executeForceModuleDeletion(moduleId) {
                console.log('🔥 Executando exclusão FORÇADA v3.0...');
                
                try {
                    const response = await fetch(`/api/parametros-modular/modulos/${moduleId}/ajax-delete`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ force: true })
                    });
                    
                    console.log('🔥 Status força v3.0:', response.status);
                    const data = await response.json();
                    console.log('🔥 Data força v3.0:', data);
                    
                    if (response.status === 200 && data.success) {
                        console.log('✅ Exclusão forçada bem-sucedida v3.0');
                        Swal.fire({
                            title: 'Excluído com sucesso!',
                            text: 'Módulo e todas suas dependências foram removidos.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        console.log('❌ Erro na exclusão forçada v3.0');
                        Swal.fire({
                            title: 'Erro na exclusão',
                            text: data.message || 'Erro ao forçar exclusão',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                } catch (error) {
                    console.error('❌ Erro de rede força v3.0:', error);
                    Swal.fire({
                        title: 'Erro de conexão',
                        text: 'Erro ao conectar com o servidor',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            }
            
            // Initialize cards view delete functionality
            function initializeCardsDeleteListeners() {
                document.querySelectorAll('[data-kt-action="delete-module"]').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const moduleId = this.getAttribute('data-module-id');
                        handleModuleDeletion(moduleId);
                    });
                });
            }

            // Initialize on page load
            initializeCardsDeleteListeners();
            
            // Debug simples
            console.log('🎯 Templates button should be a BUTTON element with onclick');
        });
    </script>
@endpush
