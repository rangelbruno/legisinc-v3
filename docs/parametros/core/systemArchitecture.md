# System Architecture - Sistema de Parâmetros SGVP

**Versão:** 2.0  
**Última Atualização:** 2024-01-15  
**Responsável:** Arquiteto de Software SGVP

---

## 🏗️ Visão Arquitetural

O Sistema de Parâmetros SGVP segue uma arquitetura em camadas baseada no padrão MVC do Laravel, com camadas adicionais para separação de responsabilidades e reutilização de código.

## 📊 Diagrama de Arquitetura

```
┌─────────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                       │
├─────────────────────────────────────────────────────────────┤
│  Blade Templates  │  Components  │  JavaScript  │  CSS     │
│  - Index Views    │  - Tables    │  - DataTables│  - SCSS  │
│  - Forms          │  - Forms     │  - Validation│  - Boot. │
│  - Layouts        │  - Modals    │  - AJAX      │  - Custom│
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    APPLICATION LAYER                        │
├─────────────────────────────────────────────────────────────┤
│     Controllers           │         Routes                  │
│  - BaseController         │  - Parameter Routes             │
│  - ParameterControllers   │  - API Routes                   │
│  - ConfigControllers      │  - Resource Routes              │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                     BUSINESS LAYER                          │
├─────────────────────────────────────────────────────────────┤
│      Services             │      Requests                   │
│  - ParameterService       │  - Form Requests                │
│  - ValidationService     │  - Validation Rules             │
│  - CacheService          │  - Custom Rules                 │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    INTEGRATION LAYER                        │
├─────────────────────────────────────────────────────────────┤
│    API Facade             │      Cache                      │
│  - ApiSgvp Facade         │  - Redis Cache                  │
│  - HTTP Client            │  - Memory Cache                 │
│  - Response Handler       │  - Cache Tags                   │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                     EXTERNAL LAYER                          │
├─────────────────────────────────────────────────────────────┤
│     SGVP API              │      Storage                    │
│  - REST Endpoints         │  - File System                  │
│  - Authentication        │  - S3 Compatible                │
│  - Data Persistence       │  - Session Storage              │
└─────────────────────────────────────────────────────────────┘
```

---

## 🎯 Padrões Arquiteturais

### **1. Arquitetura em Camadas (Layered Architecture)**

#### **Presentation Layer (Camada de Apresentação)**
- **Responsabilidade:** Interface do usuário e experiência
- **Tecnologias:** Blade Templates, Bootstrap, DataTables, jQuery
- **Componentes:** Views, Layouts, Components, Assets

#### **Application Layer (Camada de Aplicação)**
- **Responsabilidade:** Controle de fluxo e coordenação
- **Tecnologias:** Laravel Controllers, Routes, Middleware
- **Componentes:** Controllers, Routes, Middleware, Policies

#### **Business Layer (Camada de Negócio)**
- **Responsabilidade:** Lógica de negócio e validações
- **Tecnologias:** Services, Form Requests, Custom Rules
- **Componentes:** Services, Validators, Business Rules

#### **Integration Layer (Camada de Integração)**
- **Responsabilidade:** Comunicação com sistemas externos
- **Tecnologias:** HTTP Client, Facades, Cache
- **Componentes:** API Clients, Facades, Cache Managers

#### **External Layer (Camada Externa)**
- **Responsabilidade:** Sistemas e recursos externos
- **Tecnologias:** REST APIs, Databases, File Systems
- **Componentes:** SGVP API, Storage Systems, Cache Stores

### **2. Service Layer Pattern**

```php
┌─────────────────┐
│   Controller    │
│                 │
│  - Validates    │
│  - Coordinates  │
│  - Returns      │
└─────────────────┘
         │
         ▼
┌─────────────────┐
│    Service      │
│                 │
│  - Business     │
│  - Logic        │
│  - Processing   │
└─────────────────┘
         │
         ▼
┌─────────────────┐
│   Repository    │
│   (API Facade)  │
│                 │
│  - Data Access  │
│  - External API │
└─────────────────┘
```

---

## 🔧 Componentes Arquiteturais

### **1. Controllers Especializados**

#### **BaseParameterController**
```php
abstract class BaseParameterController extends Controller
{
    protected ParameterService $service;
    protected string $endpoint;
    protected string $routePrefix;
    protected string $viewPrefix;
    protected string $cachePrefix;
    
    // Métodos comuns abstratos
    abstract protected function prepareDataForStorage(array $data): array;
    abstract protected function prepareDataForUpdate(array $data): array;
    abstract protected function canDelete($id): bool;
}
```

#### **Tipos de Controllers**

1. **DataParameterController** - Para parâmetros com CRUD completo
   - TipoSessaoController
   - MomentoController  
   - AutorController

2. **ConfigParameterController** - Para parâmetros de configuração única
   - DadosCamaraController
   - ConfiguracaoSessaoController
   - ConfiguracaoPainelController

### **2. Service Layer**

#### **ParameterService**
```php
class ParameterService
{
    // CRUD Operations
    public function getAll(string $endpoint): array
    public function findById(string $endpoint, $id): ?array  
    public function create(string $endpoint, array $data): array
    public function update(string $endpoint, $id, array $data): array
    public function delete(string $endpoint, $id): bool
    
    // Business Logic
    public function validateDependencies($id): bool
    public function clearRelatedCache(string $type): void
    public function logOperation(string $operation, array $context): void
}
```

### **3. Cache Strategy**

#### **Estratégia de Cache Multi-Nível**

```php
// Nível 1: Cache de Aplicação (Redis)
Cache::remember("parameter.{type}.index", 3600, $callback);

// Nível 2: Cache de Tags (Para invalidação)
Cache::tags(['parameters', 'tipo_sessao'])->put($key, $data);

// Nível 3: Cache de Request (Memory)
app()->singleton("cache.{endpoint}", $callback);
```

#### **Políticas de Invalidação**
- **Invalidação por Tipo:** Cada tipo de parâmetro tem sua própria tag
- **Invalidação por Operação:** CREATE/UPDATE/DELETE invalidam caches relacionados
- **Invalidação por Tempo:** TTL de 1 hora para dados frequentes, 24h para dados estáticos

---

## 🗂️ Estrutura de Diretórios

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Parameters/
│   │       ├── BaseParameterController.php
│   │       ├── Data/                          # CRUD completo
│   │       │   ├── TipoSessaoController.php
│   │       │   ├── MomentoController.php
│   │       │   └── AutorController.php
│   │       └── Config/                        # Configurações únicas  
│   │           ├── DadosCamaraController.php
│   │           └── ConfigSessaoController.php
│   └── Requests/
│       └── Parameters/
│           ├── StoreParameterRequest.php
│           ├── UpdateParameterRequest.php
│           └── Specific/
│               ├── StoreTipoSessaoRequest.php
│               └── UpdateTipoSessaoRequest.php
├── Services/
│   ├── ParameterService.php
│   ├── ValidationService.php
│   └── CacheService.php
├── Facades/
│   └── ApiSgvp.php
└── Console/
    └── Commands/
        └── MakeParameterCommand.php

resources/
└── views/
    ├── components/
    │   ├── parameter-layout.blade.php
    │   ├── parameter-table.blade.php
    │   ├── parameter-form.blade.php
    │   └── parameter-modal.blade.php
    └── parametrizacao/
        ├── _partials/
        │   ├── breadcrumbs.blade.php
        │   ├── alerts.blade.php
        │   └── scripts.blade.php
        └── [tipo]/
            ├── index.blade.php
            ├── create.blade.php
            └── edit.blade.php
```

---

## 🔗 Fluxo de Dados

### **1. Fluxo de Listagem (READ)**

```
User Request → Route → Controller → Service → Cache Check → API Call → Cache Store → View Render → Response
```

**Detalhamento:**
1. **Request:** Usuário acessa `/parametros/tipo`
2. **Route:** Laravel resolve para `TipoSessaoController@index`
3. **Controller:** Chama `ParameterService->getAll()`
4. **Service:** Verifica cache primeiro
5. **Cache Miss:** Faz chamada para API externa
6. **Cache Store:** Armazena resultado no Redis
7. **View:** Renderiza com dados + componentes
8. **Response:** Retorna HTML + JSON para DataTables

### **2. Fluxo de Criação (CREATE)**

```
Form Submit → Validation → Controller → Service → API Call → Cache Clear → Redirect + Success
```

**Detalhamento:**
1. **Validation:** Form Request valida dados
2. **Controller:** Prepara dados para armazenamento  
3. **Service:** Executa lógica de negócio
4. **API Call:** Envia dados para API externa
5. **Cache Clear:** Invalida caches relacionados
6. **Log:** Registra operação para auditoria
7. **Redirect:** Retorna para listagem com sucesso

### **3. Fluxo de Atualização (UPDATE)**

```
Form Submit → Validation → Load Current → Controller → Service → API Call → Cache Clear → Redirect
```

### **4. Fluxo de Exclusão (DELETE)**

```
AJAX Request → Dependency Check → Controller → Service → API Call → Cache Clear → JSON Response
```

---

## 🔒 Segurança Arquitetural

### **1. Autenticação e Autorização**

```php
// Middleware Stack
'auth.token' → 'throttle:60,1' → 'parameter.access'

// Controller Level
$this->middleware('auth.token');
$this->authorize('manage-parameters');

// Service Level  
if (!$this->hasValidToken()) {
    throw new UnauthorizedException();
}
```

### **2. Validação em Camadas**

```php
// Layer 1: Form Request (HTTP)
class StoreParameterRequest extends FormRequest
{
    public function rules(): array
    {
        return ['dto.name' => 'required|string|max:255'];
    }
}

// Layer 2: Service (Business)  
class ParameterService
{
    public function create(string $endpoint, array $data): array
    {
        $this->validateBusinessRules($data);
        // ... processing
    }
}

// Layer 3: API (Integration)
class ApiSgvp  
{
    public function post(string $endpoint, array $data)
    {
        $this->validateApiContract($data);
        // ... API call
    }
}
```

---

## 🚀 Performance e Escalabilidade

### **1. Estratégias de Performance**

#### **Cache Inteligente**
- **L1 Cache:** Memory (Request scope)
- **L2 Cache:** Redis (Application scope)  
- **L3 Cache:** HTTP (Browser scope)

#### **Lazy Loading**
```php
// Carregar dados apenas quando necessário
public function index()
{
    return view('parameters.index', [
        'endpoint' => $this->endpoint // Para AJAX DataTables
    ]);
}
```

#### **Database Query Optimization**
- Uso de índices apropriados na API
- Paginação server-side para grandes volumes
- Caching de queries frequentes

### **2. Escalabilidade Horizontal**

#### **Stateless Design**
- Controllers sem estado
- Cache distribuído (Redis Cluster)
- Session storage external

#### **API Rate Limiting**
```php
// Throttle por usuário
Route::middleware('throttle:60,1')->group(function () {
    // Parameter routes
});

// Throttle por IP para APIs públicas
Route::middleware('throttle:100,1')->group(function () {
    // Public API routes  
});
```

---

## 📊 Monitoramento e Observabilidade

### **1. Logging Structure**

```php
// Structured Logging
Log::info('Parameter Operation', [
    'operation' => 'create',
    'type' => 'tipo_sessao',
    'user_id' => auth()->id(),
    'data' => $sanitizedData,
    'duration' => $executionTime,
    'memory' => memory_get_usage()
]);
```

### **2. Métricas de Performance**

- **Response Time:** < 2s para 95% das requests
- **Cache Hit Rate:** > 80% para dados frequentes  
- **API Success Rate:** > 99.5%
- **Memory Usage:** < 512MB por request

### **3. Health Checks**

```php
// Health endpoint
Route::get('/health/parameters', function () {
    return [
        'status' => 'healthy',
        'cache' => Cache::store('redis')->ping(),
        'api' => ApiSgvp::healthCheck(),
        'timestamp' => now()
    ];
});
```

---

## 🔄 Patterns de Integração

### **1. Repository Pattern (via Facade)**

```php
// Abstração da fonte de dados
interface ParameterRepositoryInterface
{
    public function findAll(string $type): Collection;
    public function findById(string $type, $id): ?Model;
    public function store(string $type, array $data): Model;
    public function update(string $type, $id, array $data): Model;
    public function delete(string $type, $id): bool;
}

// Implementação via API
class ApiParameterRepository implements ParameterRepositoryInterface
{
    // Implementa métodos usando ApiSgvp Facade
}
```

### **2. Observer Pattern (para Cache)**

```php
// Observador de mudanças
class ParameterCacheObserver
{
    public function created($data) { $this->clearCache($data['type']); }
    public function updated($data) { $this->clearCache($data['type']); }
    public function deleted($data) { $this->clearCache($data['type']); }
}
```

---

## 📚 Conclusão

Esta arquitetura fornece:

✅ **Separação clara de responsabilidades**  
✅ **Reutilização maximizada de código**  
✅ **Performance otimizada com cache**  
✅ **Segurança em múltiplas camadas**  
✅ **Escalabilidade horizontal**  
✅ **Observabilidade completa**

---

## 📖 Links Relacionados

- [Project Brief](./projectBrief.md)
- [Stack Tecnológico](./techStack.md)  
- [Políticas de Segurança](./security.md)
- [Workflows de Desenvolvimento](../processes/creation-workflow.md)
- [Templates de Código](../templates/controller-template.md)

---

**⚠️ Nota:** Esta arquitetura deve ser validada e refinada durante a implementação, mantendo os princípios fundamentais mas adaptando aos requisitos específicos descobertos. 