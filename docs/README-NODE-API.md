# 🚀 Integração com API Node.js - Quick Start

## ✅ O que foi Implementado

Criamos um provider específico (`NodeApiClient`) para integrar com sua API Node.js Express que usa autenticação JWT. O provider gerencia automaticamente tokens e fornece métodos para todos os endpoints.

## 🔧 Configuração Rápida

### 1. Configurar Environment

Adicione ao seu `.env`:

```env
# API Node.js Configuration
API_PROVIDER=node_api
NODE_API_BASE_URL=http://localhost:3000
NODE_API_TIMEOUT=30
NODE_API_DEFAULT_EMAIL=bruno@test.com
NODE_API_DEFAULT_PASSWORD=senha123
```

### 2. Iniciar API Node.js

Certifique-se que sua API Node.js está rodando em `http://localhost:3000`

## 🧪 Testes Rápidos

### Comando Específico (Recomendado)

```bash
# Teste completo da API Node.js
php artisan api:test-node --full

# Apenas health check
php artisan api:test-node --auth-health

# Teste de login
php artisan api:test-node --login

# Gerenciamento de usuários
php artisan api:test-node --users
```

### Comando Geral

```bash
# Teste básico
php artisan api:test --provider=node_api

# Health check
php artisan api:test --provider=node_api --health
```

## 💻 Interface Web

Acesse `http://localhost/node-api` para interface de gerenciamento de usuários.

## 📋 Uso no Código

### 1. Controller Básico

```php
use App\Services\ApiClient\Providers\NodeApiClient;

class UserController extends Controller
{
    public function __construct(private NodeApiClient $nodeApi) {}

    public function login(Request $request)
    {
        $response = $this->nodeApi->login(
            $request->email,
            $request->password
        );

        if ($response->isSuccess()) {
            // Token automaticamente cacheado
            return redirect('/dashboard');
        }

        return back()->withErrors(['login' => 'Credenciais inválidas']);
    }
}
```

### 2. Gerenciamento de Usuários

```php
// Listar usuários (com auth automática)
$response = $this->nodeApi->withAuth(fn($api) => $api->getUsers());
$users = $response->getData();

// Criar usuário
$response = $this->nodeApi->createUser('João', 'joao@test.com');

// Atualizar usuário
$response = $this->nodeApi->updateUser(1, 'João Silva');

// Deletar usuário
$response = $this->nodeApi->deleteUser(1);
```

### 3. Autenticação

```php
// Verificar se está autenticado
if ($this->nodeApi->isAuthenticated()) {
    // Usuário logado
}

// Status detalhado
$status = $this->nodeApi->getAuthStatus();

// Login automático
$this->nodeApi->autoLogin();

// Logout
$this->nodeApi->logout();
```

## 🚀 Funcionalidades Principais

### ✅ Implementado

- **Gerenciamento automático de JWT** - Token é automaticamente cacheado e usado
- **Métodos para todos endpoints** - Register, login, CRUD de usuários
- **Autenticação automática** - Faz login automaticamente quando necessário
- **Cache inteligente** - GET requests são cacheadas
- **Health checks completos** - Testa conectividade e autenticação
- **Interface web** - Gerenciamento via browser
- **Comandos artisan dedicados** - Testes específicos da API
- **Tratamento robusto de erros** - Exceptions específicas
- **Logging detalhado** - Logs separados em `api-client.log`

### 🎯 Endpoints Suportados

**Públicos:**
- `POST /register` - `$client->register($name, $email, $password)`
- `POST /login` - `$client->login($email, $password)`
- `GET /` - `$client->get('/')`

**Protegidos (JWT automático):**
- `GET /users` - `$client->getUsers()`
- `GET /users/:id` - `$client->getUser($id)`
- `POST /users` - `$client->createUser($name, $email)`
- `PUT /users/:id` - `$client->updateUser($id, $name, $email)`
- `DELETE /users/:id` - `$client->deleteUser($id)`

## 📊 Endpoints Web Disponíveis

```bash
# Interface principal
GET /node-api

# API endpoints
POST /node-api/login
POST /node-api/register
GET /node-api/users
POST /node-api/users
PUT /node-api/users/{id}
DELETE /node-api/users/{id}
GET /node-api/health-check
```

## 🔍 Debug e Troubleshooting

### Verificar Conectividade

```bash
# Health check completo
php artisan api:test-node --auth-health

# Verificar se API está rodando
curl http://localhost:3000/

# Verificar logs
tail -f storage/logs/api-client.log
```

### Problemas Comuns

1. **API não conecta**: Verificar se Node.js está rodando em `http://localhost:3000`
2. **Credenciais inválidas**: Verificar `NODE_API_DEFAULT_EMAIL` e `NODE_API_DEFAULT_PASSWORD`
3. **Token expirado**: Executar `php artisan api:test-node --clear-token`

## 📚 Documentação Completa

Para documentação detalhada, consulte:
- `docs/node-api-integration.md` - Guia completo
- `docs/api-client-architecture.md` - Arquitetura geral

## 🎉 Próximos Passos

1. **Testar integração**: `php artisan api:test-node --full`
2. **Acessar interface web**: `http://localhost/node-api`
3. **Implementar em controllers**: Use os exemplos acima
4. **Monitorar logs**: `storage/logs/api-client.log`

A integração está **100% funcional** e pronta para uso! 🚀 