@extends('components.layouts.app')

@section('title', 'Permissões - Modo Simplificado')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-5">
        <div>
            <h1 class="h3 text-gray-800 mb-0">Gerenciamento de Permissões</h1>
            <p class="text-muted">Modo simplificado - Para evitar travamentos</p>
        </div>
        <div>
            <a href="/admin/dashboard" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Voltar ao Admin
            </a>
        </div>
    </div>

    <!-- Alerta Informativo -->
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Modo Simplificado Ativo:</strong> O sistema de permissões está temporariamente simplificado para evitar travamentos. 
        Use o sistema de roles do Laravel/Spatie diretamente via comandos ou seeders.
    </div>

    <!-- Informações do Sistema -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4>{{ count($roles) }}</h4>
                    <p class="mb-0">Roles Disponíveis</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>{{ count($modules) }}</h4>
                    <p class="mb-0">Módulos do Sistema</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4>{{ $statistics['total_permissions'] }}</h4>
                    <p class="mb-0">Total Permissões</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>{{ $statistics['active_permissions'] }}</h4>
                    <p class="mb-0">Permissões Ativas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Disponíveis -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-users me-2"></i>
                Roles do Sistema
            </h5>
        </div>
        <div class="card-body">
            @if(count($roles) > 0)
                <div class="row">
                    @foreach($roles as $key => $name)
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card border">
                            <div class="card-body text-center">
                                <i class="fas fa-user-tag fa-2x text-primary mb-2"></i>
                                <h6 class="card-title">{{ $name }}</h6>
                                <small class="text-muted">{{ $key }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted">Nenhuma role configurada.</p>
            @endif
        </div>
    </div>

    <!-- Módulos do Sistema -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-cube me-2"></i>
                Módulos do Sistema
            </h5>
        </div>
        <div class="card-body">
            @if(count($modules) > 0)
                <div class="row">
                    @foreach($modules as $key => $name)
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card border">
                            <div class="card-body text-center">
                                <i class="fas fa-cog fa-2x text-success mb-2"></i>
                                <h6 class="card-title">{{ $name }}</h6>
                                <small class="text-muted">{{ $key }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted">Nenhum módulo configurado.</p>
            @endif
        </div>
    </div>

    <!-- Comandos Úteis -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-terminal me-2"></i>
                Comandos Úteis para Gerenciar Permissões
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Executar Seeders:</h6>
                    <pre class="bg-light p-3"><code>php artisan db:seed --class=ProposicaoPermissionsSeeder</code></pre>
                    
                    <h6>Criar Role:</h6>
                    <pre class="bg-light p-3"><code>// Via Tinker
$role = Spatie\Permission\Models\Role::create(['name' => 'NOVO_ROLE']);</code></pre>
                </div>
                <div class="col-md-6">
                    <h6>Atribuir Role a Usuário:</h6>
                    <pre class="bg-light p-3"><code>// Via Tinker
$user = User::find(1);
$user->assignRole('PARLAMENTAR');</code></pre>
                    
                    <h6>Criar Permissão:</h6>
                    <pre class="bg-light p-3"><code>// Via Tinker
$permission = Spatie\Permission\Models\Permission::create(['name' => 'nova.permissao']);</code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Ações -->
    <div class="mt-4">
        <div class="row">
            <div class="col-md-4">
                <button class="btn btn-primary w-100" onclick="executarSeeder()">
                    <i class="fas fa-database me-2"></i>Executar Seeder de Permissões
                </button>
            </div>
            <div class="col-md-4">
                <button class="btn btn-success w-100" onclick="verificarPermissoes()">
                    <i class="fas fa-check me-2"></i>Verificar Permissões
                </button>
            </div>
            <div class="col-md-4">
                <button class="btn btn-warning w-100" onclick="limparCache()">
                    <i class="fas fa-trash me-2"></i>Limpar Cache
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function executarSeeder() {
    alert('Execute: php artisan db:seed --class=ProposicaoPermissionsSeeder');
}

function verificarPermissoes() {
    alert('Execute: php artisan tinker e depois: User::with("roles")->get()');
}

function limparCache() {
    alert('Execute: php artisan cache:clear && php artisan config:clear');
}
</script>
@endpush

@push('styles')
<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

pre {
    font-size: 0.875rem;
}

.border {
    border: 1px solid #dee2e6 !important;
}
</style>
@endpush