@extends('components.layouts.app')

@section('title', 'Modelos de Projeto')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Modelos de Projeto
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Modelos de Projeto</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('modelos.create') }}" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-plus fs-2"></i>
                    Novo Modelo
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-check-circle fs-2hx text-success me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-dark">Sucesso!</h4>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-dark">Erro!</h4>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

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
                            <input type="text" class="form-control form-control-solid w-250px ps-13" 
                                   placeholder="Buscar modelos..." id="campoBusca" onkeyup="buscarModelos()">
                        </div>
                        <!--end::Search-->
                    </div>
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar">
                        <!--begin::Toolbar-->
                        <div class="d-flex justify-content-end align-items-center gap-2">
                            <!--begin::Filter-->
                            <div class="d-flex align-items-center">
                                <select class="form-select form-select-solid w-150px" id="filtroTipo" onchange="filtrarPorTipo()">
                                    <option value="">Todos os Tipos</option>
                                    @foreach($tipos as $key => $tipo)
                                        <option value="{{ $key }}">{{ $tipo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Filter-->
                            
                            <!--begin::View Toggle-->
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-light-primary active" id="btnGridView" onclick="toggleView('grid')">
                                    <i class="ki-duotone ki-category fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light-primary" id="btnListView" onclick="toggleView('list')">
                                    <i class="ki-duotone ki-row-horizontal fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </button>
                            </div>
                            <!--end::View Toggle-->
                        </div>
                        <!--end::Toolbar-->
                    </div>
                    <!--end::Card toolbar-->
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body py-4">
                    
                    <!-- Grid View -->
                    <div id="gridView" class="grid-view">
                        @if($modelosPorTipo->isEmpty())
                            <div class="text-center py-10">
                                <i class="ki-duotone ki-file-deleted fs-4x text-muted mb-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <h3 class="text-gray-800 fs-2 fw-bold mb-3">Nenhum modelo encontrado</h3>
                                <p class="text-muted fs-6">Comece criando seu primeiro modelo de projeto.</p>
                                <a href="{{ route('modelos.create') }}" class="btn btn-primary">
                                    <i class="ki-duotone ki-plus fs-2"></i>
                                    Criar Primeiro Modelo
                                </a>
                            </div>
                        @else
                            <div class="row g-6 g-xl-9">
                                @foreach($tipos as $tipoKey => $tipoNome)
                                    @php
                                        $modelosDoTipo = $modelosPorTipo->get($tipoKey, collect());
                                        $totalModelos = $modelosDoTipo->count();
                                    @endphp
                                    
                                    <div class="col-md-6 col-lg-4 col-xl-3" data-tipo="{{ $tipoKey }}">
                                        <!--begin::Card-->
                                        <div class="card card-flush h-md-100">
                                            <!--begin::Card header-->
                                            <div class="card-header">
                                                <!--begin::Card title-->
                                                <div class="card-title d-flex flex-column">
                                                    <h2 class="fs-6 fw-bold text-dark">{{ $tipoNome }}</h2>
                                                    <div class="fs-7 fw-semibold text-gray-400 mt-1">
                                                        {{ $totalModelos }} {{ $totalModelos === 1 ? 'modelo' : 'modelos' }}
                                                    </div>
                                                </div>
                                                <!--end::Card title-->
                                                <!--begin::Card toolbar-->
                                                <div class="card-toolbar">
                                                    <span class="badge badge-light-primary fs-7">{{ $totalModelos }}</span>
                                                </div>
                                                <!--end::Card toolbar-->
                                            </div>
                                            <!--end::Card header-->
                                            
                                            <!--begin::Card body-->
                                            <div class="card-body pt-0">
                                                @if($totalModelos > 0)
                                                    <div class="d-flex flex-column gap-3">
                                                        @foreach($modelosDoTipo->take(3) as $modelo)
                                                            <div class="d-flex align-items-center">
                                                                <div class="symbol symbol-40px me-4">
                                                                    <div class="symbol-label bg-light-primary">
                                                                        <i class="ki-duotone ki-document fs-2 text-primary">
                                                                            <span class="path1"></span>
                                                                            <span class="path2"></span>
                                                                        </i>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex flex-column flex-grow-1">
                                                                    <a href="{{ route('modelos.show', $modelo->id) }}" 
                                                                       class="text-dark fw-bold text-hover-primary fs-7">
                                                                        {{ Str::limit($modelo->nome, 25) }}
                                                                    </a>
                                                                    <span class="text-muted fs-8">
                                                                        {{ $modelo->created_at->format('d/m/Y') }}
                                                                    </span>
                                                                </div>
                                                                <div class="dropdown">
                                                                    <button class="btn btn-sm btn-light btn-active-light-primary" 
                                                                            type="button" data-bs-toggle="dropdown">
                                                                        <i class="ki-duotone ki-dots-vertical fs-5">
                                                                            <span class="path1"></span>
                                                                            <span class="path2"></span>
                                                                            <span class="path3"></span>
                                                                        </i>
                                                                    </button>
                                                                    <div class="dropdown-menu dropdown-menu-end">
                                                                        <a class="dropdown-item" href="{{ route('modelos.show', $modelo->id) }}">
                                                                            <i class="ki-duotone ki-eye fs-5 me-2">
                                                                                <span class="path1"></span>
                                                                                <span class="path2"></span>
                                                                                <span class="path3"></span>
                                                                            </i>
                                                                            Ver
                                                                        </a>
                                                                        <a class="dropdown-item" href="{{ route('modelos.edit', $modelo->id) }}">
                                                                            <i class="ki-duotone ki-pencil fs-5 me-2">
                                                                                <span class="path1"></span>
                                                                                <span class="path2"></span>
                                                                            </i>
                                                                            Editar
                                                                        </a>
                                                                        <div class="dropdown-divider"></div>
                                                                        <a class="dropdown-item text-danger btn-excluir" 
                                                                           href="#" data-modelo-id="{{ $modelo->id }}">
                                                                            <i class="ki-duotone ki-trash fs-5 me-2">
                                                                                <span class="path1"></span>
                                                                                <span class="path2"></span>
                                                                                <span class="path3"></span>
                                                                                <span class="path4"></span>
                                                                                <span class="path5"></span>
                                                                            </i>
                                                                            Excluir
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        
                                                        @if($totalModelos > 3)
                                                            <div class="text-center mt-3">
                                                                <button class="btn btn-sm btn-light-primary btn-ver-todos" 
                                                                        data-tipo="{{ $tipoKey }}">
                                                                    <i class="ki-duotone ki-arrow-down fs-5 me-1">
                                                                        <span class="path1"></span>
                                                                        <span class="path2"></span>
                                                                    </i>
                                                                    Ver todos ({{ $totalModelos }})
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="text-center py-6">
                                                        <i class="ki-duotone ki-file-deleted fs-3x text-muted mb-3">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        <p class="text-muted fs-7 mb-3">Nenhum modelo criado para este tipo</p>
                                                        <a href="{{ route('modelos.create', ['tipo' => $tipoKey]) }}" 
                                                           class="btn btn-sm btn-light-primary">
                                                            <i class="ki-duotone ki-plus fs-5 me-1">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            Criar Primeiro Modelo
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                            <!--end::Card body-->
                                        </div>
                                        <!--end::Card-->
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <!-- List View -->
                    <div id="listView" class="list-view" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-striped table-row-bordered gy-5 gs-7">
                                <thead>
                                    <tr class="fw-semibold fs-6 text-gray-800 border-bottom-2 border-gray-200">
                                        <th class="min-w-200px">Nome</th>
                                        <th class="min-w-100px">Tipo</th>
                                        <th class="min-w-100px">Status</th>
                                        <th class="min-w-100px">Criado em</th>
                                        <th class="min-w-100px text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($modelos as $modelo)
                                        <tr data-tipo="{{ $modelo->tipo_projeto }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-40px me-4">
                                                        <div class="symbol-label bg-light-primary">
                                                            <i class="ki-duotone ki-document fs-2 text-primary">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <a href="{{ route('modelos.show', $modelo->id) }}" 
                                                           class="text-dark fw-bold text-hover-primary fs-6">
                                                            {{ $modelo->nome }}
                                                        </a>
                                                        @if($modelo->descricao)
                                                            <span class="text-muted fs-7">
                                                                {{ Str::limit($modelo->descricao, 50) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-info fs-7">
                                                    {{ $tipos[$modelo->tipo_projeto] ?? 'Indefinido' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-{{ $modelo->ativo ? 'success' : 'danger' }} fs-7">
                                                    {{ $modelo->ativo ? 'Ativo' : 'Inativo' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-muted fs-7">
                                                    {{ $modelo->created_at->format('d/m/Y H:i') }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end align-items-center gap-2">
                                                    <a href="{{ route('modelos.show', $modelo->id) }}" 
                                                       class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
                                                        <i class="ki-duotone ki-eye fs-5">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                    </a>
                                                    <a href="{{ route('modelos.edit', $modelo->id) }}" 
                                                       class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
                                                        <i class="ki-duotone ki-pencil fs-5">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </a>
                                                    <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-danger btn-excluir" 
                                                            data-modelo-id="{{ $modelo->id }}">
                                                        <i class="ki-duotone ki-trash fs-5">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                            <span class="path4"></span>
                                                            <span class="path5"></span>
                                                        </i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-10">
                                                <i class="ki-duotone ki-file-deleted fs-4x text-muted mb-4">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <h3 class="text-gray-800 fs-2 fw-bold mb-3">Nenhum modelo encontrado</h3>
                                                <p class="text-muted fs-6">Comece criando seu primeiro modelo de projeto.</p>
                                                <a href="{{ route('modelos.create') }}" class="btn btn-primary">
                                                    <i class="ki-duotone ki-plus fs-2"></i>
                                                    Criar Primeiro Modelo
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<!-- Modal de Confirmação -->
<div class="modal fade" id="modalConfirmacao" tabindex="-1" aria-labelledby="modalConfirmacaoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmacaoLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="ki-duotone ki-exclamation-triangle fs-5x text-warning mb-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <h3 class="mb-3">Tem certeza?</h3>
                    <p class="mb-0">Esta ação não pode ser desfeita. O modelo será permanentemente excluído.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarExclusao">Excluir</button>
            </div>
        </div>
    </div>
</div>

<script>
let modeloParaExcluir = null;

// Event delegation para botões
document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-excluir')) {
        const modeloId = e.target.closest('.btn-excluir').getAttribute('data-modelo-id');
        confirmarExclusao(modeloId);
    }
    
    if (e.target.closest('.btn-ver-todos')) {
        const tipo = e.target.closest('.btn-ver-todos').getAttribute('data-tipo');
        verTodosModelos(tipo);
    }
});

// Confirmar exclusão
function confirmarExclusao(modeloId) {
    modeloParaExcluir = modeloId;
    new bootstrap.Modal(document.getElementById('modalConfirmacao')).show();
}

// Executar exclusão
document.getElementById('btnConfirmarExclusao').addEventListener('click', function() {
    if (modeloParaExcluir) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/modelos/' + modeloParaExcluir;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = '{{ csrf_token() }}';
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
});

// Alternar visualização
function toggleView(view) {
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const btnGrid = document.getElementById('btnGridView');
    const btnList = document.getElementById('btnListView');
    
    if (view === 'grid') {
        gridView.style.display = 'block';
        listView.style.display = 'none';
        btnGrid.classList.add('active');
        btnList.classList.remove('active');
        localStorage.setItem('modelos_view', 'grid');
    } else {
        gridView.style.display = 'none';
        listView.style.display = 'block';
        btnList.classList.add('active');
        btnGrid.classList.remove('active');
        localStorage.setItem('modelos_view', 'list');
    }
}

// Restaurar visualização salva
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('modelos_view') || 'grid';
    toggleView(savedView);
});

// Filtrar por tipo
function filtrarPorTipo() {
    const tipoSelecionado = document.getElementById('filtroTipo').value;
    const cards = document.querySelectorAll('[data-tipo]');
    
    cards.forEach(card => {
        if (tipoSelecionado === '' || card.getAttribute('data-tipo') === tipoSelecionado) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Buscar modelos
function buscarModelos() {
    const termo = document.getElementById('campoBusca').value.toLowerCase();
    const cards = document.querySelectorAll('.card');
    
    cards.forEach(card => {
        const texto = card.textContent.toLowerCase();
        if (texto.includes(termo)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Ver todos os modelos de um tipo
function verTodosModelos(tipo) {
    document.getElementById('filtroTipo').value = tipo;
    filtrarPorTipo();
    toggleView('list');
 }
 </script>
@endsection