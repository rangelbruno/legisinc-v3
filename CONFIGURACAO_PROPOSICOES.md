# Configuração do Sistema de Proposições

## Problema Identificado

O travamento do navegador ocorria devido a:
1. Queries no banco sem dados
2. Middleware de permissões sendo aplicado antes dos roles serem configurados
3. Dashboard tentando acessar tabelas sem dados

## Soluções Aplicadas

### 1. Dashboard Controller Otimizado
- Adicionado try/catch em todos os métodos
- Verificação de dados antes de executar queries
- Fallback para dashboard padrão em caso de erro

### 2. Middleware Temporariamente Desabilitado
- Removido `check.proposicao.permission` das rotas
- Dashboard retornando view padrão temporariamente

## Passos para Configuração Completa

### 1. Configurar Banco de Dados
```bash
# Configure o .env com dados do banco corretos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=legisinc
DB_USERNAME=root
DB_PASSWORD=
```

### 2. Executar Migrations
```bash
php artisan migrate
```

### 3. Executar Seeders de Permissões
```bash
php artisan db:seed --class=ProposicaoPermissionsSeeder
```

### 4. Criar Usuários de Teste
```bash
php artisan tinker

# Criar usuário Parlamentar
$user = User::create([
    'name' => 'João Parlamentar',
    'email' => 'parlamentar@teste.com',
    'password' => bcrypt('123456'),
    'ativo' => true
]);
$user->assignRole('PARLAMENTAR');

# Criar usuário Legislativo
$user = User::create([
    'name' => 'Maria Legislativo',
    'email' => 'legislativo@teste.com', 
    'password' => bcrypt('123456'),
    'ativo' => true
]);
$user->assignRole('LEGISLATIVO');

# Criar usuário Protocolo
$user = User::create([
    'name' => 'José Protocolo',
    'email' => 'protocolo@teste.com',
    'password' => bcrypt('123456'),
    'ativo' => true
]);
$user->assignRole('PROTOCOLO');
```

### 5. Reativar Middleware (após configurar roles)
Em `routes/web.php`, linha 509:
```php
// Alterar de:
Route::prefix('proposicoes')->name('proposicoes.')->middleware(['auth'])->group(function () {

// Para:
Route::prefix('proposicoes')->name('proposicoes.')->middleware(['auth', 'check.proposicao.permission'])->group(function () {
```

### 6. Reativar Dashboards Específicos
Em `app/Http/Controllers/DashboardController.php`, método `index()`:
```php
// Substituir o return view('dashboard') por:
// Redirecionar baseado no perfil
if ($user->hasRole('PARLAMENTAR') || $user->isParlamentar()) {
    return $this->dashboardParlamentar();
}

if ($user->hasRole('LEGISLATIVO')) {
    return $this->dashboardLegislativo();
}

if ($user->hasRole('PROTOCOLO')) {
    return $this->dashboardProtocolo();
}

if ($user->isAdmin()) {
    return $this->dashboardAdmin();
}

// Dashboard padrão para outros usuários
return view('dashboard');
```

## Estrutura Implementada

### Controllers
- ✅ `ProposicaoController` - Criação (Parlamentar)
- ✅ `ProposicaoLegislativoController` - Revisão 
- ✅ `ProposicaoAssinaturaController` - Assinatura
- ✅ `ProposicaoProtocoloController` - Protocolo
- ✅ `DashboardController` - Dashboards por perfil

### Views
- ✅ `proposicoes/create.blade.php`
- ✅ `proposicoes/legislativo/index.blade.php`
- ✅ `proposicoes/legislativo/revisar.blade.php`
- ✅ `proposicoes/assinatura/index.blade.php`
- ✅ `proposicoes/protocolo/index.blade.php`
- ✅ `dashboard/parlamentar.blade.php`
- ✅ `dashboard/admin.blade.php`

### Middleware e Permissões
- ✅ `CheckProposicaoPermission.php`
- ✅ `ProjetoPolicy.php` - Atualizada
- ✅ `ProposicaoPermissionsSeeder.php`

### Migrations
- ✅ Campos de revisão legislativa
- ✅ Campos de assinatura digital
- ✅ Campos de protocolo
- ✅ Enums atualizados (status e tipos)

## Sistema Pronto Para Uso

Após seguir os passos acima, o sistema terá:
- ✅ Fluxo completo conforme documentação
- ✅ Permissões por perfil
- ✅ Interfaces específicas por usuário  
- ✅ Dashboards personalizados
- ✅ Controle de acesso granular

## Para Testar

1. Configure o banco
2. Execute migrations e seeders
3. Crie usuários de teste
4. Reative middleware e dashboards
5. Teste o fluxo: Criar → Revisar → Assinar → Protocolar