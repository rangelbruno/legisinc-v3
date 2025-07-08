# 🚀 Arquitetura de API Client - Guia Completo

## 📖 Visão Geral

Esta implementação fornece uma arquitetura robusta e escalável para integração com APIs externas em Laravel, seguindo princípios SOLID e boas práticas de desenvolvimento.

## 🏗️ Estrutura da Arquitetura

```
app/Services/ApiClient/
├── Interfaces/
│   └── ApiClientInterface.php          # Contrato base
├── AbstractApiClient.php               # Implementação base
├── Providers/                          # Implementações específicas
│   ├── JsonPlaceholderClient.php      # Provider de exemplo
│   └── ExampleApiClient.php           # Provider alternativo
├── DTOs/
│   └── ApiResponse.php                 # Objeto de resposta padronizado
├── Exceptions/
│   └── ApiException.php                # Exceções personalizadas
└── Traits/
    ├── HasCaching.php                  # Funcionalidades de cache
    └── HasLogging.php                  # Funcionalidades de log
```

## ⚙️ Configuração

### 1. Variáveis de Ambiente (.env)

```env
# Provider ativo
API_PROVIDER=jsonplaceholder

# JSONPlaceholder (para testes)
JSONPLACEHOLDER_BASE_URL=https://jsonplaceholder.typicode.com
JSONPLACEHOLDER_TOKEN=
JSONPLACEHOLDER_TIMEOUT=30
JSONPLACEHOLDER_RETRIES=3
JSONPLACEHOLDER_CACHE_TTL=300

# API Externa Real
EXAMPLE_API_BASE_URL=https://api.suaapi.com
EXAMPLE_API_TOKEN=seu-token-aqui
EXAMPLE_API_TIMEOUT=30
EXAMPLE_API_RETRIES=3
EXAMPLE_API_CACHE_TTL=300
```

### 2. Configuração em config/services.php

A configuração já está implementada e permite múltiplos providers com configurações independentes.

## 📋 Como Usar

### 1. Injeção de Dependência Básica

```php
<?php

namespace App\Http\Controllers;

use App\Services\ApiClient\Interfaces\ApiClientInterface;
use App\Services\ApiClient\Exceptions\ApiException;

class MeuController extends Controller
{
    public function __construct(
        private ApiClientInterface $apiClient
    ) {}

    public function exemploUso()
    {
        try {
            // GET request
            $response = $this->apiClient->get('/posts', ['limit' => 10]);
            
            if ($response->isSuccess()) {
                $dados = $response->getData();
                // Processar dados...
            }
            
        } catch (ApiException $e) {
            // Tratar erro específico da API
            Log::error('API Error: ' . $e->getMessage(), $e->getContext());
        }
    }
}
```

### 2. Uso em Services

```php
<?php

namespace App\Services;

use App\Services\ApiClient\Interfaces\ApiClientInterface;

class ExternalDataService
{
    public function __construct(
        private ApiClientInterface $apiClient
    ) {}

    public function sincronizarDados(): array
    {
        $response = $this->apiClient->get('/dados');
        
        return [
            'success' => $response->isSuccess(),
            'data' => $response->getData(),
            'response_time' => $response->responseTime
        ];
    }

    public function enviarDados(array $dados): bool
    {
        $response = $this->apiClient->post('/dados', $dados);
        return $response->isSuccess();
    }
}
```

### 3. Health Check

```php
public function verificarSaudeApi()
{
    $isHealthy = $this->apiClient->healthCheck();
    
    if (!$isHealthy) {
        // Notificar administradores
        // Ativar modo de fallback
    }
}
```

## 🔄 Troca de Providers

### Via Environment (.env)
```env
# Trocar de JSONPlaceholder para sua API
API_PROVIDER=example_api
```

### Via Código (para testes)
```php
// Temporariamente trocar provider
config(['services.api_provider' => 'example_api']);
app()->forgetInstance(ApiClientInterface::class);
$newClient = app(ApiClientInterface::class);
```

## 🧪 Testes

### 1. Comando Artisan
```bash
# Teste completo com provider padrão
php artisan api:test

# Teste com provider específico
php artisan api:test --provider=jsonplaceholder

# Apenas health check
php artisan api:test --health

# Endpoint específico
php artisan api:test --endpoint=/posts/1 --method=GET

# Com dados para POST
php artisan api:test --endpoint=/posts --method=POST --data='{"title":"Test","body":"Content"}'
```

### 2. Testes Unitários
```bash
# Executar testes da arquitetura
php artisan test tests/Unit/ApiClientTest.php

# Executar todos os testes
php artisan test
```

### 3. Interface Web
Acesse: `http://seu-app.com/api-test` para interface de testes interativa.

## 📊 Monitoramento e Logs

### Logs Específicos
Os logs da API são salvos em: `storage/logs/api-client.log`

### Estrutura de Log
```json
{
  "level": "info",
  "message": "API Request Started",
  "context": {
    "provider": "JsonPlaceholderClient",
    "method": "GET",
    "endpoint": "/posts",
    "data": {...},
    "timestamp": "2024-01-15T10:30:00Z"
  }
}
```

## 🔧 Criando Novos Providers

### 1. Criar Nova Classe
```php
<?php

namespace App\Services\ApiClient\Providers;

use App\Services\ApiClient\AbstractApiClient;

class MinhaApiClient extends AbstractApiClient
{
    protected function getHealthCheckEndpoint(): string
    {
        return '/health';
    }

    protected function getCustomHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'X-API-Version' => '2.0',
        ];
    }

    // Métodos específicos da sua API
    public function buscarUsuarios()
    {
        return $this->get('/usuarios');
    }
}
```

### 2. Configurar em services.php
```php
'api_clients' => [
    // ... providers existentes
    'minha_api' => [
        'base_url' => env('MINHA_API_BASE_URL'),
        'token' => env('MINHA_API_TOKEN'),
        'timeout' => env('MINHA_API_TIMEOUT', 30),
        'retries' => env('MINHA_API_RETRIES', 3),
        'cache_ttl' => env('MINHA_API_CACHE_TTL', 300),
        'provider_name' => 'minha_api',
    ],
],
```

### 3. Registrar no AppServiceProvider
```php
return match ($provider) {
    'jsonplaceholder' => new JsonPlaceholderClient($config),
    'example_api' => new ExampleApiClient($config),
    'minha_api' => new MinhaApiClient($config), // Adicionar aqui
    default => throw new \InvalidArgumentException("Unsupported API provider: {$provider}")
};
```

## 🚀 Funcionalidades Avançadas

### Cache Automático
- GET requests são automaticamente cacheadas
- TTL configurável por provider
- Cache inteligente baseado em endpoint + parâmetros

### Retry Automático
- Tentativas automáticas em caso de falha
- Backoff exponencial
- Configurável por provider

### Logging Detalhado
- Request/Response logging
- Performance monitoring
- Error tracking
- Sanitização de dados sensíveis

### Tratamento de Erros
- Exceptions específicas por tipo de erro
- Context rico para debugging
- Fallback automático

## 📈 Métricas e Performance

### Health Check Endpoint
```php
GET /api-test/health
```

### Provider Info
```php
GET /api-test/provider-info
```

### Métricas de Response Time
Todas as requisições incluem tempo de resposta para monitoramento.

## 🔒 Segurança

### Sanitização de Logs
Dados sensíveis (tokens, passwords) são automaticamente ocultados nos logs.

### Timeout Protection
Timeouts configuráveis previnem hanging requests.

### Retry Limits
Limites de retry previnem loops infinitos.

## 🎯 Próximos Passos

1. **Implementar sua API real** substituindo o ExampleApiClient
2. **Configurar monitoramento** com ferramentas como Laravel Telescope
3. **Adicionar métricas** de performance e disponibilidade
4. **Implementar circuit breaker** para fallback automático
5. **Adicionar rate limiting** se necessário

## 📞 Suporte

Para dúvidas ou problemas:
1. Verifique os logs em `storage/logs/api-client.log`
2. Execute `php artisan api:test --health` para diagnóstico
3. Use `php artisan api:test --verbose` para debugging detalhado 