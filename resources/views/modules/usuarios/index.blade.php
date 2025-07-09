<x-layouts.app title="Gestão de Usuários">
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
                
                <!-- Estatísticas -->
                <div class="row g-5 g-xl-8 mb-5">
                    <div class="col-xl-3">
                        <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50 mb-5 mb-xl-10">
                            <div class="card-header pt-5">
                                <div class="card-title d-flex flex-column">
                                    <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $estatisticas['total'] }}</span>
                                    <span class="text-gray-400 pt-1 fw-semibold fs-6">Total de Usuários</span>
                                </div>
                            </div>
                            <div class="card-body d-flex align-items-end pt-0">
                                <div class="d-flex align-items-center flex-column mt-3 w-100">
                                    <div class="d-flex justify-content-between fw-bold fs-6 text-gray-400 w-100 mt-auto mb-2">
                                        <span>Ativos</span>
                                        <span>{{ $estatisticas['ativos'] }}</span>
                                    </div>
                                    <div class="h-8px mx-3 w-100 bg-light-success rounded">
                                        <div class="bg-success rounded h-8px" style="width: {{ $estatisticas['total'] > 0 ? ($estatisticas['ativos'] / $estatisticas['total']) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3">
                        <div class="card card-flush h-md-50 mb-5 mb-xl-10">
                            <div class="card-header pt-5">
                                <div class="card-title d-flex flex-column">
                                    <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $estatisticas['parlamentares'] }}</span>
                                    <span class="text-gray-400 pt-1 fw-semibold fs-6">Parlamentares</span>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-end pe-0">
                                <div class="d-flex align-items-center flex-column mt-3 w-100">
                                    <div class="d-flex justify-content-between fw-bold fs-6 text-gray-400 w-100 mt-auto mb-2">
                                        <span>Ativos último mês</span>
                                        <span>{{ $estatisticas['ultimo_acesso'] }}</span>
                                    </div>
                                    <div class="h-8px mx-3 w-100 bg-light-primary rounded">
                                        <div class="bg-primary rounded h-8px" style="width: {{ $estatisticas['total'] > 0 ? ($estatisticas['ultimo_acesso'] / $estatisticas['total']) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-6">
                        <div class="card card-flush h-md-50 mb-xl-10">
                            <div class="card-header pt-5">
                                <h3 class="card-title text-gray-800 fw-bold">Distribuição por Perfil</h3>
                            </div>
                            <div class="card-body pt-2 pb-4 d-flex flex-wrap align-items-center">
                                @foreach($estatisticas['por_perfil'] as $perfil)
                                    <div class="d-flex flex-column me-7 me-lg-16 pt-sm-3 pt-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bullet w-8px h-6px rounded-2 bg-primary me-3"></div>
                                            <div class="fs-6 fw-semibold text-gray-600">{{ $perfil->perfil }}</div>
                                        </div>
                                        <div class="fw-bold fs-6 text-gray-800">{{ $perfil->total }}</div>
                                    </div>
                                @endforeach
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
                                            <div class="badge badge-light-primary fw-bold">Administrador</div>
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
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-users-table-action="status" data-user-id="{{ $usuario->id }}" data-status="{{ $usuario->ativo ? 0 : 1 }}">
                                                        {{ $usuario->ativo ? 'Desativar' : 'Ativar' }}
                                                    </a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-users-table-action="reset-password" data-user-id="{{ $usuario->id }}">
                                                        Resetar Senha
                                                    </a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3 text-danger" data-kt-users-table-action="delete" data-user-id="{{ $usuario->id }}">
                                                        Excluir
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginação -->
                        <div class="d-flex flex-stack flex-wrap pt-10">
                            <div class="fs-6 fw-semibold text-gray-700">
                                Exibindo {{ $usuarios->firstItem() }} a {{ $usuarios->lastItem() }} de {{ $usuarios->total() }} usuários
                            </div>
                            <ul class="pagination">
                                {{ $usuarios->links() }}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Implementar actions da tabela
        document.addEventListener('DOMContentLoaded', function() {
            // Status toggle
            document.querySelectorAll('[data-kt-users-table-action="status"]').forEach(function(element) {
                element.addEventListener('click', function(e) {
                    e.preventDefault();
                    const userId = this.getAttribute('data-user-id');
                    const status = this.getAttribute('data-status');
                    
                    // Fazer request AJAX para alterar status
                    fetch(`/usuarios/${userId}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            ativo: status === '1'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Erro: ' + data.message);
                        }
                    });
                });
            });

            // Reset password
            document.querySelectorAll('[data-kt-users-table-action="reset-password"]').forEach(function(element) {
                element.addEventListener('click', function(e) {
                    e.preventDefault();
                    const userId = this.getAttribute('data-user-id');
                    
                    if (confirm('Tem certeza que deseja resetar a senha deste usuário?')) {
                        fetch(`/usuarios/${userId}/resetar-senha`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Senha resetada com sucesso! Nova senha: ' + data.nova_senha);
                            } else {
                                alert('Erro: ' + data.message);
                            }
                        });
                    }
                });
            });

            // Delete
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
        });
    </script>
    @endpush
</x-layouts.app>