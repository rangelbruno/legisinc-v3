@extends('components.layouts.app')

@section('title', 'Gestão de Usuários')

@section('content')
<style>
.usuarios-card-primary {
    background: linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.usuarios-card-info {
    background: linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.usuarios-card-success {
    background: linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.usuarios-card-warning {
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
                    Gestão de Usuários
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Usuários</li>
                </ul>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <div class="row gy-5 gx-xl-8 mb-5">
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 usuarios-card-primary">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-people text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['total'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">usuários</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Total de Usuários</span>
                                <span class="badge badge-light-primary fs-8">{{ $estatisticas['total'] > 0 ? round(($estatisticas['ativos'] / $estatisticas['total']) * 100) : 0 }}%</span>
                            </div>
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $estatisticas['total'] > 0 ? ($estatisticas['ativos'] / $estatisticas['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 usuarios-card-info">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-profile-user text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['parlamentares'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">parlamentares</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Parlamentares</span>
                                <span class="badge badge-light-info fs-8">{{ $estatisticas['total'] > 0 ? round(($estatisticas['ultimo_acesso'] / $estatisticas['total']) * 100) : 0 }}%</span>
                            </div>
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $estatisticas['total'] > 0 ? ($estatisticas['ultimo_acesso'] / $estatisticas['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 usuarios-card-success">
                        <div class="card-header pt-5 pb-3">
                            <div class="d-flex flex-center rounded-circle h-70px w-70px bg-white bg-opacity-20">
                                <i class="ki-duotone ki-user-tick text-white fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-end pt-0">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['ativos'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">ativos</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Usuários Ativos</span>
                                <span class="badge badge-light-success fs-8">{{ $estatisticas['total'] > 0 ? round(($estatisticas['ativos'] / $estatisticas['total']) * 100) : 0 }}%</span>
                            </div>
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $estatisticas['total'] > 0 ? ($estatisticas['ativos'] / $estatisticas['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10 usuarios-card-warning">
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
                                <span class="fs-2hx fw-bold text-white me-2">{{ $estatisticas['ultimo_acesso'] }}</span>
                                <span class="fs-6 fw-semibold text-white opacity-75">último mês</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-6 fw-bold text-white">Acesso Recente</span>
                                <span class="badge badge-light-warning fs-8">{{ $estatisticas['total'] > 0 ? round(($estatisticas['ultimo_acesso'] / $estatisticas['total']) * 100) : 0 }}%</span>
                            </div>
                            <div class="progress h-6px bg-white bg-opacity-50">
                                <div class="progress-bar bg-white" style="width: {{ $estatisticas['total'] > 0 ? ($estatisticas['ultimo_acesso'] / $estatisticas['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                         <!-- Seção de Distribuição por Perfil -->
            <div class="row g-5 g-xl-8 mb-5">
                <div class="col-xl-12">
                    <div class="card card-flush h-100 mb-5 mb-xl-10">
                        <div class="card-header pt-5 pb-3 border-0 bg-light-primary">
                            <div class="card-title align-items-start flex-column w-100">
                                <h3 class="card-label fw-bold text-gray-900 mb-1">Distribuição por Perfil</h3>
                                <div class="text-muted fw-semibold fs-7">Usuários agrupados por tipo de acesso</div>
                            </div>
                        </div>
                        <div class="card-body pt-3 pb-5">
                            <div class="row g-5">
                                @foreach($estatisticas['por_perfil'] as $perfil)
                                    <div class="col-xl-3 col-lg-4 col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-50px me-4">
                                                <div class="symbol-label bg-light-primary">
                                                    <i class="ki-duotone ki-shield-tick text-primary fs-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="text-gray-900 fw-bold fs-6 mb-1">{{ $perfil->perfil }}</div>
                                                <div class="text-gray-600 fs-7 mb-2">{{ $perfil->total }} {{ $perfil->total == 1 ? 'usuário' : 'usuários' }}</div>
                                                <div class="progress h-5px bg-light-primary">
                                                    <div class="progress-bar bg-primary" style="width: {{ $estatisticas['total'] > 0 ? ($perfil->total / $estatisticas['total']) * 100 : 0 }}%"></div>
                                                </div>
                                            </div>
                                            <div class="text-end ms-3">
                                                <span class="badge badge-primary fs-8 fw-bold mb-1">{{ $estatisticas['total'] > 0 ? round(($perfil->total / $estatisticas['total']) * 100) : 0 }}%</span>
                                                <div class="text-gray-500 fs-8">{{ $perfil->total }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                         <!-- Filtros -->
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Buscar usuário..." />
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                            <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-filter fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Filtros
                            </button>
                            <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                                <div class="px-7 py-5">
                                    <div class="fs-5 text-dark fw-bold">Filtros</div>
                                </div>
                                <div class="separator border-gray-200"></div>
                                <form method="GET" action="{{ route('usuarios.index') }}">
                                    <div class="px-7 py-5">
                                        <div class="mb-5">
                                            <label class="form-label fw-semibold">Perfil:</label>
                                            <select class="form-select form-select-solid" name="perfil">
                                                <option value="">Todos</option>
                                                @foreach($perfis as $key => $nome)
                                                    <option value="{{ $key }}" {{ request('perfil') == $key ? 'selected' : '' }}>{{ $nome }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-5">
                                            <label class="form-label fw-semibold">Status:</label>
                                            <select class="form-select form-select-solid" name="ativo">
                                                <option value="">Todos</option>
                                                <option value="1" {{ request('ativo') == '1' ? 'selected' : '' }}>Ativo</option>
                                                <option value="0" {{ request('ativo') == '0' ? 'selected' : '' }}>Inativo</option>
                                            </select>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2">Limpar</button>
                                            <button type="submit" class="btn btn-sm btn-primary">Aplicar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
                                <i class="ki-duotone ki-plus fs-2"></i>
                                Novo Usuário
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="table-responsive">
                        <table class="table table-rounded table-striped border gy-7 gs-7" id="kt_table_users">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom-2 border-gray-200">
                                    <th class="min-w-125px">Usuário</th>
                                    <th class="min-w-125px">Perfil</th>
                                    <th class="min-w-125px">Documento</th>
                                    <th class="min-w-125px">Telefone</th>
                                    <th class="min-w-125px">Status</th>
                                    <th class="min-w-125px">Último Acesso</th>
                                    <th class="text-end min-w-100px">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usuarios as $usuario)
                                <tr>
                                    <td class="d-flex align-items-center">
                                        <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                            <div class="symbol-label fs-3 bg-light-primary text-primary">
                                                {{ strtoupper(substr($usuario->name, 0, 2)) }}
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <a href="{{ route('usuarios.show', $usuario->id) }}" class="text-gray-800 text-hover-primary mb-1 fs-6">{{ $usuario->name }}</a>
                                            <span class="text-muted fw-semibold text-muted d-block fs-7">{{ $usuario->email }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($usuario->roles->count() > 0)
                                            @foreach($usuario->roles as $role)
                                                <div class="badge badge-light-primary fw-bold">{{ $perfis[$role->name] ?? $role->name }}</div>
                                            @endforeach
                                        @else
                                            <div class="badge badge-light-secondary fw-bold">Sem perfil</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $usuario->documento ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $usuario->telefone ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="badge badge-light-{{ $usuario->ativo ? 'success' : 'danger' }} fw-bold">
                                            {{ $usuario->ativo ? 'Ativo' : 'Inativo' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $usuario->ultimo_acesso ? $usuario->ultimo_acesso->diffForHumans() : 'Nunca' }}</div>
                                    </td>
                                    <td class="text-end">
                                        <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            Ações
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                        </a>
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                            <div class="menu-item px-3">
                                                <a href="{{ route('usuarios.show', $usuario->id) }}" class="menu-link px-3">Ver</a>
                                            </div>
                                            <div class="menu-item px-3">
                                                <a href="{{ route('usuarios.edit', $usuario->id) }}" class="menu-link px-3">Editar</a>
                                            </div>
                                            <div class="separator my-2"></div>
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3" data-kt-users-table-action="reset-password" data-user-id="{{ $usuario->id }}">
                                                    Resetar Senha
                                                </a>
                                            </div>
                                            @if($usuario->id !== auth()->id())
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3" data-kt-users-table-action="toggle-status" data-user-id="{{ $usuario->id }}" data-status="{{ $usuario->ativo }}">
                                                    {{ $usuario->ativo ? 'Desativar' : 'Ativar' }}
                                                </a>
                                            </div>
                                            <div class="separator my-2"></div>
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3 text-danger" data-kt-users-table-action="delete" data-user-id="{{ $usuario->id }}">
                                                    Excluir
                                                </a>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                                         <!-- Paginação -->
                    @if($usuarios->hasPages())
                    <div class="d-flex flex-stack flex-wrap pt-10">
                        <div class="fs-6 fw-semibold text-gray-700">
                            Exibindo {{ $usuarios->firstItem() }} a {{ $usuarios->lastItem() }} de {{ $usuarios->total() }} usuários
                        </div>
                        <ul class="pagination">
                            {{ $usuarios->links() }}
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
        // Toggle status do usuário
        document.querySelectorAll('[data-kt-users-table-action="toggle-status"]').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                const userId = this.getAttribute('data-user-id');
                const currentStatus = this.getAttribute('data-status');
                const newStatus = currentStatus === '1' ? 0 : 1;
                const action = newStatus ? 'ativar' : 'desativar';
                
                if (confirm(`Tem certeza que deseja ${action} este usuário?`)) {
                    fetch(`/usuarios/${userId}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ ativo: newStatus })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Erro: ' + data.message);
                        }
                    });
                }
            });
        });

        // Resetar senha
        document.querySelectorAll('[data-kt-users-table-action="reset-password"]').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                const userId = this.getAttribute('data-user-id');
                
                if (confirm('Tem certeza que deseja resetar a senha deste usuário?')) {
                    fetch(`/usuarios/${userId}/resetar-senha`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(`Senha resetada com sucesso! Nova senha: ${data.nova_senha}`);
                        } else {
                            alert('Erro: ' + data.message);
                        }
                    });
                }
            });
        });

        // Excluir usuário
        document.querySelectorAll('[data-kt-users-table-action="delete"]').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                const userId = this.getAttribute('data-user-id');
                
                if (confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/usuarios/${userId}`;
                    
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
        const searchInput = document.querySelector('[data-kt-user-table-filter="search"]');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const searchTerm = this.value;
                    if (searchTerm.length >= 3 || searchTerm.length === 0) {
                        // Fazer busca via AJAX
                        fetch(`/usuarios/search?termo=${encodeURIComponent(searchTerm)}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Atualizar tabela com resultados
                                    console.log('Resultados da busca:', data.usuarios);
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