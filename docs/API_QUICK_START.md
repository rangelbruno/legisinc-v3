# 🚀 API Quick Start Guide - LegisInc

## 📋 Introdução

Este guia é um **ponto de partida rápido** para desenvolvedores implementarem a API do LegisInc. Para documentação completa, consulte `apiDocumentation.md` e `api-implementation-checklist.md`.

## 🎯 Objetivo

Implementar uma API RESTful completa para o sistema LegisInc, permitindo que desenvolvedores criem aplicações frontend, mobile ou integrações com terceiros.

## 📁 Documentação Disponível

1. **[apiDocumentation.md](./apiDocumentation.md)** - Documentação técnica completa da API
2. **[api-implementation-checklist.md](./api-implementation-checklist.md)** - Checklist detalhado de implementação
3. **[API_QUICK_START.md](./API_QUICK_START.md)** - Este guia rápido

## ⚡ Implementação Rápida (30 minutos)

### 1. Configuração Inicial (5 minutos)
```bash
# Instalar Laravel Sanctum
composer require laravel/sanctum

# Publicar configuração
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Executar migrations
php artisan migrate
```

### 2. Estrutura de Pastas (2 minutos)
```bash
# Criar estrutura da API
mkdir -p app/Http/Controllers/Api
mkdir -p app/Http/Resources
mkdir -p app/Http/Requests
```

### 3. Primeiro Endpoint - Autenticação (10 minutos)
```php
<?php
// app/Http/Controllers/Api/AuthController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Credenciais inválidas'
        ], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso'
        ]);
    }
}
```

### 4. Primeiro CRUD - Usuários (10 minutos)
```php
<?php
// app/Http/Controllers/Api/UserController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::when($request->search, function($query, $search) {
            $query->where('name', 'like', "%{$search}%");
        })->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'total' => $users->total(),
                'per_page' => $users->perPage()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8'
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Usuário criado com sucesso'
        ], 201);
    }

    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|min:8'
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Usuário atualizado com sucesso'
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuário excluído com sucesso'
        ]);
    }
}
```

### 5. Configurar Rotas (3 minutos)
```php
<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

Route::prefix('v1')->group(function () {
    // Autenticação
    Route::post('auth/login', [AuthController::class, 'login']);
    
    // Rotas protegidas
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::apiResource('users', UserController::class);
    });
});
```

## 🧪 Testando a API

### 1. Teste de Login
```bash
curl -X POST http://localhost/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@sistema.gov.br",
    "password": "senha123"
  }'
```

### 2. Teste de Listagem (com token)
```bash
curl -X GET http://localhost/api/v1/users \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -H "Accept: application/json"
```

### 3. Teste de Criação
```bash
curl -X POST http://localhost/api/v1/users \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Novo Usuário",
    "email": "novo@exemplo.com",
    "password": "senha123456"
  }'
```

## 📊 Próximos Passos Recomendados

### Ordem de Implementação Sugerida:
1. ✅ **Autenticação** (feito acima)
2. ✅ **Usuários** (feito acima)
3. **Parlamentares** - Use o modelo `Parlamentar` existente
4. **Projetos** - Use o modelo `Projeto` existente
5. **Tramitação** - Use o modelo `ProjetoTramitacao` existente
6. **Anexos** - Use o modelo `ProjetoAnexo` existente

### Para Cada Endpoint:
1. **Controller** - Lógica de negócio
2. **Resource** - Formatação de dados
3. **Request** - Validação de entrada
4. **Route** - Definição da rota
5. **Test** - Teste automatizado

## 🔧 Ferramentas Úteis

### Postman Collection
```json
{
  "info": {
    "name": "LegisInc API",
    "description": "API do sistema LegisInc"
  },
  "variable": [
    {
      "key": "base_url",
      "value": "http://localhost/api/v1"
    },
    {
      "key": "token",
      "value": "{{auth_token}}"
    }
  ]
}
```

### Middleware Personalizado
```php
<?php
// app/Http/Middleware/ApiResponseMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

class ApiResponseMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            $data['meta'] = array_merge($data['meta'] ?? [], [
                'timestamp' => now()->toISOString(),
                'version' => '1.0.0'
            ]);
            $response->setData($data);
        }
        
        return $response;
    }
}
```

## 🎯 Dicas Importantes

### 1. Estrutura de Resposta Padrão
```json
{
  "success": true,
  "data": {...},
  "message": "Operação realizada com sucesso",
  "meta": {
    "timestamp": "2025-07-12T10:30:00Z",
    "version": "1.0.0"
  }
}
```

### 2. Tratamento de Erros
```php
public function render($request, Throwable $exception)
{
    if ($request->expectsJson()) {
        return response()->json([
            'success' => false,
            'error' => [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode()
            ]
        ], 500);
    }

    return parent::render($request, $exception);
}
```

### 3. Validação Consistente
```php
// app/Http/Requests/StoreUserRequest.php
public function rules()
{
    return [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed'
    ];
}

public function messages()
{
    return [
        'name.required' => 'O nome é obrigatório',
        'email.required' => 'O email é obrigatório',
        'email.unique' => 'Este email já está em uso'
    ];
}
```

## 📚 Recursos Complementares

- **Documentação Completa:** `apiDocumentation.md`
- **Checklist Detalhado:** `api-implementation-checklist.md`
- **Modelos Existentes:** `app/Models/`
- **Migrations:** `database/migrations/`
- **Seeders:** `database/seeders/`

## 💡 Próximos Recursos

Após implementar os endpoints básicos, considere:
- Rate limiting
- Cache de respostas
- Logging estruturado
- Documentação automática (Swagger)
- Testes automatizados
- Monitoramento de performance

---

**Tempo estimado para API completa:** 2-3 semanas  
**Desenvolvedor:** 1 pessoa sênior ou 2 pessoas júnior  
**Próximo passo:** Seguir o `api-implementation-checklist.md` 