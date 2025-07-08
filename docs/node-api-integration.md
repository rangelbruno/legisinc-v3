# 🚀 Integração com API Node.js - Guia Completo

## 📖 Visão Geral

Esta documentação explica como usar o `NodeApiClient` para integrar com a API Node.js Express que possui autenticação JWT. O provider foi especialmente desenvolvido para gerenciar automaticamente tokens JWT e fornecer métodos específicos para todos os endpoints da API.

## 🏗️ Arquitetura da API Node.js

### Endpoints Disponíveis

**Públicos (sem autenticação):**
- `GET /` - Health check
- `POST /register` - Registrar usuário
- `POST /login` - Fazer login e obter token JWT

**Protegidos (requer JWT):**
- `GET /users` - Listar usuários
- `GET /users/:id` - Obter usuário específico
- `POST /users` - Criar usuário (admin)
- `PUT /users/:id` - Atualizar usuário
- `DELETE /users/:id` - Deletar usuário

## ⚙️ Configuração

### 1. Variáveis de Ambiente (.env)

```env
# Configuração da API Node.js
API_PROVIDER=node_api
NODE_API_BASE_URL=http://localhost:3000
NODE_API_TOKEN=
NODE_API_TIMEOUT=30
NODE_API_RETRIES=3
NODE_API_CACHE_TTL=300

# Credenciais padrão para login automático
NODE_API_DEFAULT_EMAIL=bruno@test.com
NODE_API_DEFAULT_PASSWORD=senha123
```

### 2. Iniciar a API Node.js

Certifique-se de que a API Node.js esteja rodando:

```bash
# Na pasta da API Node.js
npm start
# ou
node server.js
```

A API deve estar disponível em `http://localhost:3000`

## 🧪 Testando a Integração

### 1. Comando Artisan Específico (Recomendado)

```bash
# Teste completo da API Node.js
php artisan api:test-node --full

# Apenas health check
php artisan api:test-node --auth-health

# Teste de login
php artisan api:test-node --login

# Teste de registro
php artisan api:test-node --register

# Teste de gerenciamento de usuários
php artisan api:test-node --users

# Login com credenciais específicas
php artisan api:test-node --login --email=bruno@test.com --password=senha123

# Limpar token em cache
php artisan api:test-node --clear-token
```

### 2. Comando Artisan Geral

```bash
# Teste básico com provider node_api
php artisan api:test --provider=node_api

# Health check específico
php artisan api:test --provider=node_api --health

# Endpoint específico
php artisan api:test --provider=node_api --endpoint=/users --method=GET
```

### 3. Interface Web

Acesse: `http://localhost/node-api` para interface de gerenciamento de usuários.

## 📋 Uso nos Controllers

### 1. Injeção de Dependência Específica

```php
<?php

namespace App\Http\Controllers;

use App\Services\ApiClient\Providers\NodeApiClient;
use App\Services\ApiClient\Exceptions\ApiException;

class MeuController extends Controller
{
    public function __construct(
        private NodeApiClient $nodeApi
    ) {}

    public function exemploLogin(Request $request)
    {
        try {
            $response = $this->nodeApi->login(
                $request->input('email'),
                $request->input('password')
            );

            if ($response->isSuccess()) {
                // Token é automaticamente armazenado em cache
                return redirect()->route('dashboard');
            }

            return back()->withErrors(['email' => 'Credenciais inválidas']);

        } catch (ApiException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
```

### 2. Uso com Interface Genérica

```php
use App\Services\ApiClient\Interfaces\ApiClientInterface;

class OutroController extends Controller
{
    public function __construct(
        private ApiClientInterface $apiClient
    ) {
        // Certificar que é NodeApiClient quando necessário
        if (!$apiClient instanceof \App\Services\ApiClient\Providers\NodeApiClient) {
            throw new \InvalidArgumentException('Expected NodeApiClient');
        }
    }
}
```

## 🔐 Gerenciamento de Autenticação

### 1. Métodos de Autenticação

```php
/** @var NodeApiClient $client */
$client = app(NodeApiClient::class);

// Verificar se está autenticado
if ($client->isAuthenticated()) {
    // Usuário logado
}

// Status detalhado de autenticação
$status = $client->getAuthStatus();
/*
[
    'authenticated' => true,
    'token_present' => true,
    'token_cached' => true,
    'token_length' => 128
]
*/

// Login manual
$response = $client->login('bruno@test.com', 'senha123');

// Login automático com credenciais padrão
$response = $client->autoLogin();

// Logout (limpa token)
$client->logout();

// Definir token manualmente
$client->setToken('seu-jwt-token-aqui');
```

### 2. Execução com Autenticação Automática

```php
// Garantir autenticação antes de executar operação
$response = $client->withAuth(function($api) {
    return $api->getUsers();
});

// Ou verificar manualmente
if (!$client->ensureAuthenticated()) {
    throw new ApiException('Falha na autenticação');
}
```

## 👥 Gerenciamento de Usuários

### 1. Operações CRUD

```php
// Listar usuários
$response = $client->getUsers();
$users = $response->getData();

// Obter usuário específico
$response = $client->getUser(1);
$user = $response->getData();

// Criar usuário (admin)
$response = $client->createUser('João Silva', 'joao@test.com');

// Atualizar usuário
$response = $client->updateUser(1, 'João Silva Santos', 'joao.santos@test.com');

// Atualizar apenas nome
$response = $client->updateUser(1, 'João Silva Santos');

// Deletar usuário
$response = $client->deleteUser(1);
```

### 2. Registro de Usuários

```php
// Registrar novo usuário
$response = $client->register(
    'Maria Santos',
    'maria@test.com',
    'senha123'
);

if ($response->isSuccess()) {
    $userData = $response->getData();
    // Usuário registrado com sucesso
}
```

## 📊 Monitoramento e Health Check

### 1. Health Check Completo

```php
$results = $client->authHealthCheck();
/*
[
    'public_endpoint' => true,
    'auth_status' => [
        'authenticated' => true,
        'token_present' => true,
        'token_cached' => true,
        'token_length' => 128
    ],
    'auto_login' => true,
    'protected_endpoint' => true
]
*/
```

### 2. Health Check Simples

```php
$isHealthy = $client->healthCheck(); // GET /
```

## 🔄 Cache e Performance

### Cache Automático de Token

- **Token JWT é automaticamente cacheado** por 23 horas
- **Cache persiste entre requests** 
- **Recuperação automática** do token em cache
- **Limpeza automática** no logout

### Cache de Respostas

- **GET requests são cacheadas** automaticamente
- **TTL configurável** via `NODE_API_CACHE_TTL`
- **Cache baseado em endpoint + parâmetros**

## 🚨 Tratamento de Erros

### Exceptions Específicas

```php
try {
    $response = $client->getUsers();
} catch (ApiException $e) {
    switch ($e->statusCode) {
        case 401:
            // Token expirado ou inválido
            $client->autoLogin(); // Tentar login automático
            break;
        case 403:
            // Acesso negado
            break;
        case 404:
            // Usuário não encontrado
            break;
        default:
            // Outros erros
            Log::error('API Error: ' . $e->getMessage(), $e->getContext());
    }
}
```

### Recuperação Automática

```php
// O cliente tentará automaticamente fazer login se necessário
$response = $client->withAuth(function($api) {
    return $api->getUsers();
});
```

## 🌐 Endpoints da Interface Web

### Rotas Disponíveis

```php
// Interface principal
GET /node-api

// Autenticação
POST /node-api/login
POST /node-api/logout
POST /node-api/register
POST /node-api/auto-login

// Usuários
GET /node-api/users
GET /node-api/users/{id}
POST /node-api/users
PUT /node-api/users/{id}
DELETE /node-api/users/{id}

// Status
GET /node-api/auth-status
GET /node-api/health-check
```

### Exemplos de Uso

```bash
# Health check
curl http://localhost/node-api/health-check

# Status de autenticação
curl http://localhost/node-api/auth-status

# Login
curl -X POST http://localhost/node-api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"bruno@test.com","password":"senha123"}'

# Listar usuários
curl http://localhost/node-api/users
```

## 📝 Exemplos Práticos

### 1. Service de Sincronização

```php
<?php

namespace App\Services;

use App\Services\ApiClient\Providers\NodeApiClient;

class UserSyncService
{
    public function __construct(
        private NodeApiClient $nodeApi
    ) {}

    public function syncUsers(): array
    {
        $results = ['created' => 0, 'updated' => 0, 'errors' => []];

        try {
            // Garantir autenticação
            if (!$this->nodeApi->ensureAuthenticated()) {
                throw new \Exception('Falha na autenticação');
            }

            // Obter usuários da API
            $response = $this->nodeApi->getUsers();
            
            if ($response->isSuccess()) {
                $apiUsers = $response->getData();
                
                foreach ($apiUsers as $apiUser) {
                    // Processar cada usuário...
                    $results['updated']++;
                }
            }

        } catch (\Exception $e) {
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }
}
```

### 2. Middleware de Autenticação

```php
<?php

namespace App\Http\Middleware;

use App\Services\ApiClient\Providers\NodeApiClient;
use Closure;

class EnsureApiAuthenticated
{
    public function __construct(
        private NodeApiClient $nodeApi
    ) {}

    public function handle($request, Closure $next)
    {
        if (!$this->nodeApi->isAuthenticated()) {
            if (!$this->nodeApi->ensureAuthenticated()) {
                return response()->json(['error' => 'API authentication required'], 401);
            }
        }

        return $next($request);
    }
}
```

## 🔧 Troubleshooting

### Problemas Comuns

1. **"Connection refused"**
   - Verificar se a API Node.js está rodando em `http://localhost:3000`
   - Verificar `NODE_API_BASE_URL` no `.env`

2. **"Authentication failed"**
   - Verificar credenciais em `NODE_API_DEFAULT_EMAIL` e `NODE_API_DEFAULT_PASSWORD`
   - Limpar cache: `php artisan api:test-node --clear-token`

3. **"Token expired"**
   - O token expira em 24h, será renovado automaticamente

4. **Cache issues**
   - Limpar cache: `php artisan cache:clear`

### Debug e Logs

```bash
# Verificar logs da API
tail -f storage/logs/api-client.log

# Teste de conectividade
php artisan api:test-node --auth-health

# Teste com verbose
php artisan api:test-node --full -v
```

## 🎯 Próximos Passos

1. **Implementar middleware** de autenticação automática
2. **Configurar queue jobs** para sincronização
3. **Adicionar rate limiting** se necessário  
4. **Implementar circuit breaker** para fallback
5. **Configurar monitoramento** em produção

Esta integração está pronta para produção e fornece uma base sólida para trabalhar com a API Node.js de forma robusta e escalável. 