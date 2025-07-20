<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permissões - Modo Simplificado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 text-dark mb-0">Gerenciamento de Permissões</h1>
                <p class="text-muted">Modo simplificado - Para evitar travamentos</p>
            </div>
            <div>
                <a href="/admin/dashboard" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar ao Admin
                </a>
            </div>
        </div>

        <!-- Alerta Informativo -->
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Sistema Temporariamente Simplificado:</strong> Para evitar travamentos, o sistema de permissões está em modo básico. 
            Use os comandos abaixo para gerenciar permissões diretamente.
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

        <!-- Comandos Úteis -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-terminal me-2"></i>
                    Comandos para Gerenciar Permissões
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>1. Executar Seeder de Permissões:</h6>
                        <div class="bg-light p-3 rounded mb-3">
                            <code>php artisan db:seed --class=ProposicaoPermissionsSeeder</code>
                        </div>
                        
                        <h6>2. Criar Role:</h6>
                        <div class="bg-light p-3 rounded mb-3">
                            <code>php artisan tinker<br>
$role = Spatie\Permission\Models\Role::create(['name' => 'NOVO_ROLE']);</code>
                        </div>
                        
                        <h6>3. Atribuir Role a Usuário:</h6>
                        <div class="bg-light p-3 rounded mb-3">
                            <code>php artisan tinker<br>
$user = User::find(1);<br>
$user->assignRole('PARLAMENTAR');</code>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>4. Criar Permissão:</h6>
                        <div class="bg-light p-3 rounded mb-3">
                            <code>php artisan tinker<br>
$permission = Spatie\Permission\Models\Permission::create(['name' => 'nova.permissao']);</code>
                        </div>
                        
                        <h6>5. Atribuir Permissão a Role:</h6>
                        <div class="bg-light p-3 rounded mb-3">
                            <code>php artisan tinker<br>
$role = Spatie\Permission\Models\Role::findByName('PARLAMENTAR');<br>
$role->givePermissionTo('nova.permissao');</code>
                        </div>
                        
                        <h6>6. Listar Usuários e Roles:</h6>
                        <div class="bg-light p-3 rounded mb-3">
                            <code>php artisan tinker<br>
User::with('roles')->get();</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instruções -->
        <div class="alert alert-info">
            <h6><i class="fas fa-info-circle me-2"></i>Instruções:</h6>
            <ol class="mb-0">
                <li>Configure o banco de dados corretamente no .env</li>
                <li>Execute as migrations: <code>php artisan migrate</code></li>
                <li>Execute o seeder de permissões</li>
                <li>Crie usuários de teste com roles</li>
                <li>Teste o sistema de login e acesso</li>
            </ol>
        </div>
    </div>
</body>
</html>