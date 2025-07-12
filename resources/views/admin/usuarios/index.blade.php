@extends('components.layouts.app')

@section('title', 'Usuários')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Usuários
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
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('admin.usuarios.create') }}" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-plus fs-2"></i>
                    Novo Usuário
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

            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-6">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <!--begin::Search-->
                        <form method="GET" action="{{ route('admin.usuarios.index') }}" class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" name="busca" value="{{ request('busca') }}" 
                                   class="form-control form-control-solid w-250px ps-13" 
                                   placeholder="Buscar usuários..." />
                        </form>
                        <!--end::Search-->
                    </div>
                    <!--begin::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar">
                        <!--begin::Toolbar-->
                        <div class="d-flex justify-content-end gap-3" data-kt-user-table-toolbar="base">
                            <!--begin::Filter-->
                            <div class="d-flex align-items-center gap-2">
                                <form method="GET" action="{{ route('admin.usuarios.index') }}" class="d-flex gap-2">
                                    <input type="hidden" name="busca" value="{{ request('busca') }}">
                                    
                                    <select name="role" class="form-select form-select-solid w-150px">
                                        <option value="">Todos os perfis</option>
                                        @foreach($perfis as $key => $nome)
                                            <option value="{{ $key }}" {{ request('role') === $key ? 'selected' : '' }}>
                                                {{ $nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                    <select name="ativo" class="form-select form-select-solid w-100px">
                                        <option value="">Todos</option>
                                        <option value="1" {{ request('ativo') === '1' ? 'selected' : '' }}>Ativo</option>
                                        <option value="0" {{ request('ativo') === '0' ? 'selected' : '' }}>Inativo</option>
                                    </select>
                                    
                                    <button type="submit" class="btn btn-light btn-active-light-primary">
                                        <i class="ki-duotone ki-filter fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Filtrar
                                    </button>
                                </form>
                            </div>
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
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                            <!--begin::Table head-->
                            <thead>
                                <tr class="fw-bold text-muted">
                                    <th class="min-w-150px">Usuário</th>
                                    <th class="min-w-140px">E-mail</th>
                                    <th class="min-w-120px">Perfil</th>
                                    <th class="min-w-100px">Status</th>
                                    <th class="min-w-120px">Último Acesso</th>
                                    <th class="min-w-100px text-end">Ações</th>
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody>
                                @forelse($usuarios as $usuario)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px me-5">
                                                    @if($usuario->avatar && !is_string($usuario->avatar))
                                                        <img src="{{ $usuario->avatar }}" alt="{{ $usuario->name }}" />
                                                    @else
                                                        <div class="symbol-label fs-3 bg-light-{{ $usuario->getCorPerfil() }} text-{{ $usuario->getCorPerfil() }}">
                                                            {{ $usuario->avatar ?: substr($usuario->name, 0, 2) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="{{ route('admin.usuarios.show', $usuario) }}" 
                                                       class="text-dark fw-bold text-hover-primary fs-6">
                                                        {{ $usuario->name }}
                                                    </a>
                                                    @if($usuario->documento)
                                                        <span class="text-muted fw-semibold text-muted d-block fs-7">
                                                            {{ $usuario->documento }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-dark fw-bold d-block fs-6">{{ $usuario->email }}</span>
                                            @if($usuario->telefone)
                                                <span class="text-muted fw-semibold text-muted d-block fs-7">{{ $usuario->telefone }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-light-{{ $usuario->getCorPerfil() }}">
                                                {{ $usuario->getPerfilFormatado() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input status-toggle" 
                                                       type="checkbox" 
                                                       data-user-id="{{ $usuario->id }}"
                                                       {{ $usuario->ativo ? 'checked' : '' }}
                                                       {{ auth()->id() === $usuario->id ? 'disabled' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            @if($usuario->ultimo_acesso)
                                                <span class="text-dark fw-bold d-block fs-6">
                                                    {{ $usuario->ultimo_acesso->format('d/m/Y') }}
                                                </span>
                                                <span class="text-muted fw-semibold d-block fs-7">
                                                    {{ $usuario->ultimo_acesso->format('H:i') }}
                                                </span>
                                            @else
                                                <span class="text-muted">Nunca acessou</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end flex-shrink-0">
                                                <a href="{{ route('admin.usuarios.show', $usuario) }}" 
                                                   class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                    <i class="ki-duotone ki-eye fs-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </a>
                                                <a href="{{ route('admin.usuarios.edit', $usuario) }}" 
                                                   class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                    <i class="ki-duotone ki-pencil fs-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </a>
                                                @if(auth()->id() !== $usuario->id)
                                                    <button class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete-user" 
                                                            data-user-id="{{ $usuario->id }}"
                                                            data-user-name="{{ $usuario->name }}">
                                                        <i class="ki-duotone ki-trash fs-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                            <span class="path4"></span>
                                                            <span class="path5"></span>
                                                        </i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-10">
                                            <div class="text-muted">Nenhum usuário encontrado</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <!--end::Table body-->
                        </table>
                    </div>
                    <!--end::Table-->
                    
                    <!--begin::Pagination-->
                    @if($usuarios->hasPages())
                        <div class="d-flex flex-stack flex-wrap pt-10">
                            <div class="fs-6 fw-semibold text-gray-700">
                                Mostrando {{ $usuarios->firstItem() }} a {{ $usuarios->lastItem() }} 
                                de {{ $usuarios->total() }} resultados
                            </div>
                            {{ $usuarios->links() }}
                        </div>
                    @endif
                    <!--end::Pagination-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle status
    document.querySelectorAll('.status-toggle').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const userId = this.dataset.userId;
            const isActive = this.checked;
            
            fetch(`/admin/usuarios/${userId}/toggle-ativo`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message);
                } else {
                    toastr.error(data.message);
                    this.checked = !isActive; // Revert toggle
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('Erro ao alterar status do usuário');
                this.checked = !isActive; // Revert toggle
            });
        });
    });
    
    // Delete user
    document.querySelectorAll('.delete-user').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            
            Swal.fire({
                title: 'Tem certeza?',
                text: `Você está prestes a excluir o usuário "${userName}". Esta ação não pode ser desfeita!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/usuarios/${userId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Excluído!', data.message, 'success')
                                .then(() => {
                                    location.reload();
                                });
                        } else {
                            Swal.fire('Erro!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Erro!', 'Erro ao excluir usuário', 'error');
                    });
                }
            });
        });
    });
});
</script>
@endpush
@endsection