# Sistema de Gerenciamento de APIs - Documentação Técnica

## 📋 Visão Geral

O Sistema de Gerenciamento de APIs do LegisInc foi projetado para facilitar o desenvolvimento e deploy através de um sistema inteligente que alterna entre:

- **Mock API** (desenvolvimento) - API interna Laravel
- **API Externa** (produção) - API Node.js externa

## 🏗️ Arquitetura

### Componentes Principais

```
┌─────────────────────────────────────────────────────────────────┐
│                    Frontend (Laravel Views)                     │
│                     register.blade.php                          │
└─────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────┐
│                   AuthController                                │
│                 (Business Logic)                                │
└─────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────┐
│                   NodeApiClient                                 │
│                (Smart API Client)                               │
└─────────────────────────────────────────────────────────────────┘
                                    │
                    ┌───────────────┴───────────────┐
                    ▼                               ▼
┌─────────────────────────────┐     ┌─────────────────────────────┐
│      MockApiController      │     │     External Node.js API    │
│    (Internal Laravel API)   │     │      (External Service)     │
└─────────────────────────────┘     └─────────────────────────────┘
```

## 🔧 Configuração

### Arquivo `config/api.php`

```php
<?php

return [
    // Modo da API: 'mock' ou 'external'
    'mode' => env('API_MODE', 'mock'),
    
    // Configurações do Mock
    'mock' => [
        'enabled' => true,
        'base_url' => env('APP_URL', 'http://localhost:8000') . '/api/mock-api',
        'description' => 'Mock API interno - Laravel',
    ],
    
    // Configurações da API Externa
    'external' => [
        'base_url' => env('EXTERNAL_API_URL', 'http://localhost:3000'),
        'timeout' => env('EXTERNAL_API_TIMEOUT', 30),
        'retries' => env('EXTERNAL_API_RETRIES', 3),
        'description' => 'API Externa - Node.js',
    ],
    
    // URLs por modo
    'urls' => [
        'mock' => [
            'health' => '/api/mock-api/',
            'register' => '/api/mock-api/register',
            'login' => '/api/mock-api/login',
            'users' => '/api/mock-api/users',
        ],
        'external' => [
            'health' => '/',
            'register' => '/register',
            'login' => '/login',
            'users' => '/users',
        ],
    ],
];
```

### Variáveis de Ambiente

```bash
# Modo da API
API_MODE=mock

# Configurações para API Externa
EXTERNAL_API_URL=http://localhost:3000
EXTERNAL_API_TIMEOUT=30
EXTERNAL_API_RETRIES=3

# Credenciais padrão para testes
API_DEFAULT_EMAIL=bruno@test.com
API_DEFAULT_PASSWORD=senha123
```

## 🛠️ Comando Artisan

### `php artisan api:mode`

Comando para gerenciar o modo da API:

```bash
# Usar modo mock
php artisan api:mode mock

# Usar modo external
php artisan api:mode external

# Ver status atual
php artisan api:mode --status

# Modo interativo
php artisan api:mode
```

#### Implementação do Comando

```php
class ApiModeCommand extends Command
{
    protected $signature = 'api:mode {mode?} {--status : Show current API mode}';
    
    public function handle()
    {
        if ($this->option('status')) {
            $this->showStatus();
            return;
        }

        $mode = $this->argument('mode');
        
        if (!$mode) {
            $this->showStatus();
            $mode = $this->choice('Escolha o modo da API:', ['mock', 'external'], 0);
        }

        $this->setApiMode($mode);
        $this->showModeInfo($mode);
    }
    
    private function setApiMode(string $mode): void
    {
        // Atualiza .env
        // Limpa cache de configuração
        $this->call('config:clear');
    }
}
```

## 🔄 NodeApiClient Inteligente

### Configuração Dinâmica

O `NodeApiClient` se adapta automaticamente ao modo da API:

```php
public function __construct(array $config)
{
    // Detecta modo atual
    $apiMode = config('api.mode', 'mock');
    
    // Configura dinamicamente
    $dynamicConfig = $this->buildDynamicConfig($apiMode, $config);
    
    parent::__construct($dynamicConfig);
}

private function buildDynamicConfig(string $apiMode, array $originalConfig): array
{
    $config = $originalConfig;
    
    if ($apiMode === 'mock') {
        $config['base_url'] = config('api.mock.base_url');
        $config['provider_name'] = 'Mock API';
        $config['timeout'] = 10;
        $config['retries'] = 1;
    } else {
        $config['base_url'] = config('api.external.base_url');
        $config['provider_name'] = 'External API';
        $config['timeout'] = config('api.external.timeout', 30);
        $config['retries'] = config('api.external.retries', 3);
    }
    
    return $config;
}
```

### Endpoints Dinâmicos

```php
private function buildEndpointUrl(string $endpoint): string
{
    $apiMode = config('api.mode', 'mock');
    $urls = config('api.urls', []);
    
    $endpointMap = [
        '/register' => $urls[$apiMode]['register'] ?? '/register',
        '/login' => $urls[$apiMode]['login'] ?? '/login',
        '/users' => $urls[$apiMode]['users'] ?? '/users',
    ];
    
    return $endpointMap[$endpoint] ?? $endpoint;
}
```

### Autenticação Condicional

```php
public function isAuthenticated(): bool
{
    // Em modo mock, considerar sempre autenticado
    if (config('api.mode') === 'mock') {
        return true;
    }
    
    return !empty($this->jwtToken);
}
```

## 📊 MockApiController

### Funcionalidades

- **Health Check**: Endpoint de verificação de saúde
- **Autenticação**: Registro, login, logout
- **CRUD Usuários**: Operações completas de usuários
- **Validação**: Validação de dados igual à API real
- **Armazenamento**: Cache do Laravel (temporário)

### Endpoints Disponíveis

```php
// Health check
GET /api/mock-api/
Response: {"status":"ok","message":"Mock API is running","timestamp":"...","version":"1.0.0"}

// Registro
POST /api/mock-api/register
Body: {"name":"João","email":"joao@email.com","password":"senha123"}
Response: {"message":"User registered successfully","user":{...}}

// Login
POST /api/mock-api/login
Body: {"email":"joao@email.com","password":"senha123"}
Response: {"message":"Login successful","token":"mock_jwt_...","user":{...}}

// Usuários (protegido)
GET /api/mock-api/users
Header: Authorization: Bearer mock_jwt_...
Response: [{...}]

// Reset (para testes)
POST /api/mock-api/reset
Response: {"message":"Mock API data reset successfully","users_count":2}
```

### Armazenamento de Dados

```php
// Usuários armazenados em cache
$users = Cache::get('mock_api_users', []);

// Tokens de autenticação
Cache::put("mock_api_token_{$token}", $user, now()->addHours(24));
```

## 🔄 Fluxo de Funcionamento

### 1. Inicialização

```
[Sistema inicia] → [Lê API_MODE do .env] → [Configura NodeApiClient] → [Pronto]
```

### 2. Requisição de Registro

```
[Frontend] → [AuthController] → [NodeApiClient] → [Mock ou External API] → [Resposta]
```

### 3. Troca de Modo

```
[php artisan api:mode external] → [Atualiza .env] → [Limpa cache] → [Próxima requisição usa novo modo]
```

## 🎯 Benefícios

### Para Desenvolvimento

1. **Sem Dependências**: Não precisa de API externa rodando
2. **Dados Controlados**: Ambiente de teste reproduzível
3. **Desenvolvimento Offline**: Funciona sem conexão
4. **Debugging Simplificado**: Tudo no mesmo processo

### Para Produção

1. **Troca Instantânea**: Mudança sem downtime
2. **Configuração Simples**: Apenas uma variável de ambiente
3. **Código Idêntico**: Mesma lógica para ambos os modos
4. **Testes Integrados**: Testa fluxo completo

## 🧪 Testes

### Teste Manual

```bash
# Configurar mock
php artisan api:mode mock

# Testar health
curl http://localhost:8000/api/mock-api/

# Testar registro
curl -X POST http://localhost:8000/api/mock-api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Teste","email":"teste@email.com","password":"senha123"}'

# Resetar dados
curl -X POST http://localhost:8000/api/mock-api/reset
```

### Teste de Troca de Modo

```bash
# Status atual
php artisan api:mode --status

# Trocar para external
php artisan api:mode external

# Verificar mudança
php artisan api:mode --status

# Voltar para mock
php artisan api:mode mock
```

## 🔍 Monitoramento

### Health Check Frontend

O frontend monitora automaticamente a saúde da API:

```javascript
// Verificação periódica
setInterval(() => {
    fetch('/api/mock-api/')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'ok') {
                updateApiStatus('online');
            } else {
                updateApiStatus('problems');
            }
        })
        .catch(() => {
            updateApiStatus('offline');
        });
}, 30000);
```

### Logs Estruturados

```php
// NodeApiClient
$this->logRequest('TOKEN_STORED', $endpoint, ['token_cached' => true]);
$this->logRequest('AUTO_LOGIN_FAILED', '/login', ['error' => $e->getMessage()]);

// AuthController
Log::info('API Response', ['response' => $response->toArray()]);
Log::error('API Error', ['error' => $e->getMessage()]);
```

## 📈 Métricas

### Indicadores de Qualidade

- **Tempo de Resposta**: Mock < 100ms, External < 1000ms
- **Taxa de Sucesso**: > 99% para Mock, > 95% para External
- **Disponibilidade**: Mock 100%, External dependente
- **Consistência**: Mesma resposta para mesma entrada

### Monitoramento de Performance

```php
// Exemplo de medição
$startTime = microtime(true);
$response = $this->nodeApiClient->register($name, $email, $password);
$duration = microtime(true) - $startTime;

Log::info('API Performance', [
    'endpoint' => 'register',
    'duration' => $duration,
    'mode' => config('api.mode'),
    'success' => $response->isSuccess(),
]);
```

## 🚀 Deployment

### Desenvolvimento

```bash
# Configurar mock
php artisan api:mode mock

# Servir aplicação
php artisan serve
```

### Produção

```bash
# Configurar external
php artisan api:mode external

# Verificar conectividade
php artisan api:mode --status

# Deploy com PM2, Docker, etc.
```

## 🔧 Manutenção

### Limpeza de Cache

```bash
# Limpar cache do mock
php artisan cache:clear

# Limpar configuração
php artisan config:clear

# Resetar dados de teste
curl -X POST http://localhost:8000/api/mock-api/reset
```

### Debugging

```bash
# Ver logs
tail -f storage/logs/laravel.log

# Verificar configuração
php artisan config:show api

# Testar conectividade
php artisan tinker
>>> app('App\Services\ApiClient\Providers\NodeApiClient')->healthCheck()
```

## 📋 Checklist de Implementação

- [x] Configuração centralizada (`config/api.php`)
- [x] Comando Artisan (`api:mode`)
- [x] Cliente inteligente (`NodeApiClient`)
- [x] Mock API completo (`MockApiController`)
- [x] Documentação técnica
- [x] Testes manuais
- [x] Monitoramento frontend
- [x] Logs estruturados

## 🎯 Próximos Passos

1. **Testes Automatizados**: Implementar testes PHPUnit
2. **Métricas Avançadas**: Dashboard de monitoramento
3. **Cache Inteligente**: Cache de respostas da API externa
4. **Retry Logic**: Retry automático para falhas temporárias
5. **Health Checks**: Verificação proativa de saúde

---

**Sistema implementado com sucesso!** 🎉

O LegisInc agora possui um sistema robusto e flexível para gerenciamento de APIs, facilitando tanto o desenvolvimento quanto o deploy em produção. 