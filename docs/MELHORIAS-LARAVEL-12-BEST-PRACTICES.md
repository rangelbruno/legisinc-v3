# 🚀 Melhorias Laravel 12 Best Practices - Sistema Legisinc

## 📋 Sumário
- [Visão Geral](#visão-geral)
- [Checklist de Implementação](#checklist-de-implementação)
- [1. API Resources](#1-api-resources)
- [2. Middleware com HasMiddleware](#2-middleware-com-hasmiddleware)
- [3. Form Requests](#3-form-requests)
- [4. Eager Loading e Prevenção N+1](#4-eager-loading-e-prevenção-n1)
- [5. Scoped Relationships](#5-scoped-relationships)
- [6. Testes Automatizados](#6-testes-automatizados)
- [Validação Final](#validação-final)

---

## 🎯 Visão Geral

Este documento detalha as **melhorias necessárias** para alinhar o Sistema Legisinc com as **melhores práticas do Laravel 12**, elevando o projeto de "bom" para "excelente" em termos de arquitetura e performance.

### **Status Atual vs Recomendado**
| Área | Status Atual | Meta | Prioridade |
|------|-------------|------|------------|
| **API Resources** | ❌ 2/10 | ✅ 10/10 | 🔴 Alta |
| **Middleware Strategy** | ⚠️ 6/10 | ✅ 10/10 | 🟡 Média |
| **Form Validation** | ⚠️ 5/10 | ✅ 10/10 | 🔴 Alta |
| **N+1 Prevention** | ❌ 3/10 | ✅ 10/10 | 🔴 Alta |
| **Scoped Relationships** | ⚠️ 4/10 | ✅ 10/10 | 🟡 Média |

---

## ✅ Checklist de Implementação

### **Fase 1: Estrutura Base (2-3 horas)**
- [ ] **1.1** Criar API Resources para Proposição
- [ ] **1.2** Criar API Resources para User  
- [ ] **1.3** Criar API Resource Collections
- [ ] **1.4** Implementar middleware HasMiddleware nos Controllers

### **Fase 2: Validação e Performance (1-2 horas)**
- [ ] **2.1** Criar Form Requests para todas as operações
- [ ] **2.2** Implementar eager loading padrão nos Models
- [ ] **2.3** Adicionar prevenção N+1 queries
- [ ] **2.4** Criar scoped relationships

### **Fase 3: Testes e Validação (1-2 horas)**
- [ ] **3.1** Criar testes para API Resources
- [ ] **3.2** Criar testes para Form Requests
- [ ] **3.3** Validar performance das queries
- [ ] **3.4** Executar suite completa de testes

---

## 1️⃣ API Resources

### **1.1 Criar ProposicaoResource**

**Comando:**
```bash
php artisan make:resource ProposicaoResource
```

**Implementação:** `app/Http/Resources/ProposicaoResource.php`
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProposicaoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipo' => $this->tipo,
            'ementa' => $this->ementa,
            'conteudo' => $this->when($request->user()->canViewContent($this->resource), $this->conteudo),
            'status' => $this->status,
            'numero_protocolo' => $this->numero_protocolo,
            'ano' => $this->ano,
            
            // Relacionamentos condicionais
            'autor' => new UserResource($this->whenLoaded('autor')),
            'revisor' => new UserResource($this->whenLoaded('revisor')),
            'template' => $this->whenLoaded('template'),
            
            // Contadores condicionais
            'total_anexos' => $this->whenCounted('anexos'),
            'tramitacao_count' => $this->whenCounted('tramitacaoLogs'),
            
            // Campos de data
            'data_protocolo' => $this->data_protocolo,
            'data_assinatura' => $this->data_assinatura,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Campos calculados
            'pode_editar' => $this->when($request->user(), function () use ($request) {
                return $request->user()->can('update', $this->resource);
            }),
            'pode_assinar' => $this->when($request->user(), function () use ($request) {
                return $request->user()->can('sign', $this->resource);
            }),
        ];
    }
}
```

### **1.2 Criar ProposicaoCollection**

**Comando:**
```bash
php artisan make:resource ProposicaoCollection
```

**Implementação:** `app/Http/Resources/ProposicaoCollection.php`
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProposicaoCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->collection->count(),
                'por_status' => $this->collection->groupBy('status')->map->count(),
                'por_tipo' => $this->collection->groupBy('tipo')->map->count(),
            ],
            'links' => [
                'self' => $request->url(),
                'criar_nova' => route('proposicoes.create'),
            ],
        ];
    }
}
```

### **1.3 Criar UserResource**

**Comando:**
```bash
php artisan make:resource UserResource
```

**Implementação:** `app/Http/Resources/UserResource.php`
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->when($request->user()->canViewEmail($this->resource), $this->email),
            'role' => $this->role,
            
            // Relacionamentos condicionais
            'proposicoes_count' => $this->whenCounted('proposicoes'),
            'proposicoes_revisadas_count' => $this->whenCounted('proposicoesRevisadas'),
            
            // Timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
```

### **1.4 Atualizar Controllers para usar Resources**

**Exemplo no ProposicaoController:**
```php
<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProposicaoResource;
use App\Http\Resources\ProposicaoCollection;
use App\Models\Proposicao;

class ProposicaoController extends Controller
{
    public function index()
    {
        $proposicoes = Proposicao::with(['autor', 'template'])
            ->withCount(['anexos', 'tramitacaoLogs'])
            ->paginate(15);

        return new ProposicaoCollection($proposicoes);
    }

    public function show(Proposicao $proposicao)
    {
        $proposicao->load(['autor', 'revisor', 'template', 'tramitacaoLogs']);
        
        return new ProposicaoResource($proposicao);
    }
}
```

---

## 2️⃣ Middleware com HasMiddleware

### **2.1 Implementar HasMiddleware nos Controllers**

**ProposicaoController com HasMiddleware:**
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProposicaoController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('role:PARLAMENTAR,ADMIN', only: ['create', 'store']),
            new Middleware('can:update,proposicao', only: ['update', 'edit']),
            new Middleware('can:delete,proposicao', only: ['destroy']),
        ];
    }

    // ... métodos do controller
}
```

### **2.2 ProposicaoLegislativoController**
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProposicaoLegislativoController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('role:LEGISLATIVO,ADMIN'),
            new Middleware('can:review,proposicao', only: ['revisar', 'aprovar', 'devolver']),
        ];
    }
}
```

### **2.3 ProposicaoAssinaturaController**
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProposicaoAssinaturaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('can:sign,proposicao', only: ['assinar', 'processarAssinatura']),
            new Middleware('role:PARLAMENTAR,ADMIN', only: ['assinar', 'processarAssinatura']),
        ];
    }
}
```

### **2.4 Criar Middleware Customizado (Opcional)**

**Comando:**
```bash
php artisan make:middleware CheckProposicaoOwnership
```

**Implementação:**
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProposicaoOwnership
{
    public function handle(Request $request, Closure $next, string $ownership = 'owner'): Response
    {
        $proposicao = $request->route('proposicao');
        
        if ($ownership === 'owner' && $proposicao->autor_id !== $request->user()->id) {
            abort(403, 'Você só pode acessar suas próprias proposições.');
        }
        
        return $next($request);
    }
}
```

---

## 3️⃣ Form Requests

### **3.1 Criar StoreProposicaoRequest**

**Comando:**
```bash
php artisan make:request StoreProposicaoRequest
```

**Implementação:**
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProposicaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('PARLAMENTAR') || $this->user()->hasRole('ADMIN');
    }

    public function rules(): array
    {
        return [
            'tipo' => 'required|string|exists:tipo_proposicoes,codigo',
            'ementa' => 'required|string|max:1000',
            'conteudo' => 'nullable|string|max:50000',
            'template_id' => 'nullable|exists:tipo_proposicao_templates,id',
            'anexos' => 'nullable|array|max:5',
            'anexos.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
        ];
    }

    public function messages(): array
    {
        return [
            'tipo.required' => 'O tipo da proposição é obrigatório.',
            'tipo.exists' => 'Tipo de proposição inválido.',
            'ementa.required' => 'A ementa é obrigatória.',
            'ementa.max' => 'A ementa não pode exceder 1000 caracteres.',
            'conteudo.max' => 'O conteúdo não pode exceder 50.000 caracteres.',
            'anexos.max' => 'Máximo de 5 anexos permitidos.',
            'anexos.*.mimes' => 'Anexo deve ser: PDF, DOC, DOCX, JPG, JPEG ou PNG.',
            'anexos.*.max' => 'Cada anexo deve ter no máximo 10MB.',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'autor_id' => $this->user()->id,
            'ano' => now()->year,
            'status' => 'rascunho',
        ]);
    }
}
```

### **3.2 Criar UpdateProposicaoRequest**

**Comando:**
```bash
php artisan make:request UpdateProposicaoRequest
```

**Implementação:**
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProposicaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $proposicao = $this->route('proposicao');
        
        return $this->user()->can('update', $proposicao);
    }

    public function rules(): array
    {
        return [
            'ementa' => 'sometimes|required|string|max:1000',
            'conteudo' => 'sometimes|nullable|string|max:50000',
            'observacoes_edicao' => 'nullable|string|max:2000',
            'status' => 'sometimes|in:rascunho,em_edicao,enviado_legislativo',
        ];
    }

    public function messages(): array
    {
        return [
            'ementa.required' => 'A ementa é obrigatória.',
            'ementa.max' => 'A ementa não pode exceder 1000 caracteres.',
            'conteudo.max' => 'O conteúdo não pode exceder 50.000 caracteres.',
            'observacoes_edicao.max' => 'Observações não podem exceder 2000 caracteres.',
            'status.in' => 'Status inválido para esta operação.',
        ];
    }
}
```

### **3.3 Atualizar Controller para usar Form Requests**

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProposicaoRequest;
use App\Http\Requests\UpdateProposicaoRequest;

class ProposicaoController extends Controller
{
    public function store(StoreProposicaoRequest $request)
    {
        $proposicao = Proposicao::create($request->validated());
        
        return new ProposicaoResource($proposicao->load('autor'));
    }

    public function update(UpdateProposicaoRequest $request, Proposicao $proposicao)
    {
        $proposicao->update($request->validated());
        
        return new ProposicaoResource($proposicao->fresh(['autor', 'revisor']));
    }
}
```

---

## 4️⃣ Eager Loading e Prevenção N+1

### **4.1 Configurar Eager Loading Padrão nos Models**

**Model Proposicao:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proposicao extends Model
{
    /**
     * Relacionamentos sempre carregados
     */
    protected $with = ['autor', 'tipoProposicao'];

    /**
     * Relacionamentos disponíveis para eager loading
     */
    protected $availableIncludes = [
        'revisor', 'template', 'tramitacaoLogs', 'parecerJuridico'
    ];

    // Relacionamentos existentes...
    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisor_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(TipoProposicaoTemplate::class, 'template_id');
    }
}
```

### **4.2 Configurar Prevenção N+1 Global**

**AppServiceProvider:**
```php
<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Prevenir lazy loading em desenvolvimento
        Model::preventLazyLoading(! $this->app->isProduction());
        
        // Log personalizado para violações de lazy loading
        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) {
            $class = $model::class;
            \Log::warning("Lazy loading violation: [{$relation}] on model [{$class}].");
        });
    }
}
```

### **4.3 Otimizar Queries nos Controllers**

```php
<?php

class ProposicaoController extends Controller
{
    public function index()
    {
        $proposicoes = Proposicao::select(['id', 'tipo', 'ementa', 'status', 'autor_id', 'created_at'])
            ->with(['autor:id,name,role', 'tipoProposicao:id,nome,codigo'])
            ->withCount(['anexos', 'tramitacaoLogs'])
            ->latest()
            ->paginate(15);

        return new ProposicaoCollection($proposicoes);
    }

    public function show(Proposicao $proposicao)
    {
        $proposicao->loadMissing([
            'autor:id,name,email,role',
            'revisor:id,name,email,role', 
            'template:id,nome,arquivo_path',
            'tramitacaoLogs' => function ($query) {
                $query->latest()->limit(10);
            }
        ]);
        
        return new ProposicaoResource($proposicao);
    }
}
```

---

## 5️⃣ Scoped Relationships

### **5.1 Implementar Scoped Relationships**

**Model Proposicao:**
```php
<?php

namespace App\Models;

class Proposicao extends Model
{
    // Relacionamentos base
    public function tramitacaoLogs(): HasMany
    {
        return $this->hasMany(TramitacaoLog::class)->latest();
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(ProposicaoAnexo::class);
    }

    // Scoped relationships
    public function tramitacaoRecente(): HasMany
    {
        return $this->tramitacaoLogs()->limit(5);
    }

    public function anexosAtivos(): HasMany
    {
        return $this->anexos()->where('ativo', true);
    }

    public function proposicoesDoMesmoAutor(): HasMany
    {
        return $this->hasMany(Proposicao::class, 'autor_id', 'autor_id')
            ->where('id', '!=', $this->id);
    }

    public function proposicoesSimilares(): HasMany
    {
        return $this->proposicoesDoMesmoAutor()
            ->where('tipo', $this->tipo)
            ->where('ano', $this->ano);
    }
}
```

**Model User:**
```php
<?php

namespace App\Models;

class User extends Model
{
    // Relacionamentos base
    public function proposicoes(): HasMany
    {
        return $this->hasMany(Proposicao::class, 'autor_id')->latest();
    }

    public function proposicoesRevisadas(): HasMany
    {
        return $this->hasMany(Proposicao::class, 'revisor_id')->latest();
    }

    // Scoped relationships
    public function proposicoesAtivas(): HasMany
    {
        return $this->proposicoes()->whereNotIn('status', ['arquivado', 'cancelado']);
    }

    public function proposicoesProtocoladas(): HasMany
    {
        return $this->proposicoes()->where('status', 'protocolado');
    }

    public function proposicoesPendentes(): HasMany
    {
        return $this->proposicoes()->whereIn('status', [
            'rascunho', 'em_edicao', 'enviado_legislativo', 'devolvido_correcao'
        ]);
    }

    public function estatisticasProposicoes(): array
    {
        return [
            'total' => $this->proposicoes()->count(),
            'protocoladas' => $this->proposicoesProtocoladas()->count(),
            'pendentes' => $this->proposicoesPendentes()->count(),
            'por_tipo' => $this->proposicoes()->groupBy('tipo')
                ->selectRaw('tipo, count(*) as total')
                ->pluck('total', 'tipo')
        ];
    }
}
```

---

## 6️⃣ Testes Automatizados

### **6.1 Criar Testes para API Resources**

**Comando:**
```bash
php artisan make:test ProposicaoResourceTest --unit
```

**Implementação:**
```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Proposicao;
use App\Http\Resources\ProposicaoResource;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProposicaoResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_proposicao_resource_structure()
    {
        $user = User::factory()->create(['role' => 'PARLAMENTAR']);
        $proposicao = Proposicao::factory()->create(['autor_id' => $user->id]);
        
        $resource = new ProposicaoResource($proposicao);
        $array = $resource->toArray(request());

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('tipo', $array);
        $this->assertArrayHasKey('ementa', $array);
        $this->assertArrayHasKey('status', $array);
        $this->assertArrayHasKey('autor', $array);
    }

    public function test_sensitive_content_hidden_for_unauthorized_users()
    {
        $autor = User::factory()->create(['role' => 'PARLAMENTAR']);
        $otherUser = User::factory()->create(['role' => 'PARLAMENTAR']);
        
        $proposicao = Proposicao::factory()->create([
            'autor_id' => $autor->id,
            'conteudo' => 'Conteúdo confidencial'
        ]);

        $this->actingAs($otherUser);
        $resource = new ProposicaoResource($proposicao);
        $array = $resource->toArray(request());

        $this->assertNull($array['conteudo']);
    }
}
```

### **6.2 Criar Testes para Form Requests**

**Comando:**
```bash
php artisan make:test StoreProposicaoRequestTest --unit
```

**Implementação:**
```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Http\Requests\StoreProposicaoRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreProposicaoRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_proposicao_validation_rules()
    {
        $user = User::factory()->create(['role' => 'PARLAMENTAR']);
        
        $request = new StoreProposicaoRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('tipo', $rules);
        $this->assertArrayHasKey('ementa', $rules);
        $this->assertContains('required', $rules['tipo']);
        $this->assertContains('required', $rules['ementa']);
    }

    public function test_authorization_for_parlamentar()
    {
        $user = User::factory()->create(['role' => 'PARLAMENTAR']);
        
        $request = new StoreProposicaoRequest();
        $request->setUserResolver(fn () => $user);

        $this->assertTrue($request->authorize());
    }

    public function test_authorization_denied_for_other_roles()
    {
        $user = User::factory()->create(['role' => 'LEGISLATIVO']);
        
        $request = new StoreProposicaoRequest();
        $request->setUserResolver(fn () => $user);

        $this->assertFalse($request->authorize());
    }
}
```

### **6.3 Criar Testes de Performance**

**Comando:**
```bash
php artisan make:test ProposicaoPerformanceTest
```

**Implementação:**
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Proposicao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class ProposicaoPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_endpoint_without_n_plus_1()
    {
        $user = User::factory()->create(['role' => 'PARLAMENTAR']);
        $this->actingAs($user);

        // Criar múltiplas proposições
        Proposicao::factory()->count(20)->create(['autor_id' => $user->id]);

        DB::enableQueryLog();

        $response = $this->getJson('/proposicoes');

        $queries = DB::getQueryLog();
        
        // Deve ter no máximo 3-4 queries (proposições, users, tipos, counts)
        $this->assertLessThan(5, count($queries));
        $response->assertOk();
    }

    public function test_show_endpoint_optimized()
    {
        $user = User::factory()->create(['role' => 'PARLAMENTAR']);
        $proposicao = Proposicao::factory()->create(['autor_id' => $user->id]);
        
        $this->actingAs($user);

        DB::enableQueryLog();
        
        $response = $this->getJson("/proposicoes/{$proposicao->id}");
        
        $queries = DB::getQueryLog();
        
        // Deve ter no máximo 2-3 queries com eager loading
        $this->assertLessThan(4, count($queries));
        $response->assertOk();
    }
}
```

---

## ✅ Validação Final

### **Comandos de Validação**

```bash
# 1. Executar todos os testes
php artisan test

# 2. Executar testes específicos de performance
php artisan test --filter=Performance

# 3. Verificar formatação com Pint
vendor/bin/pint --dirty

# 4. Verificar se há queries N+1 em desenvolvimento
# Habilitar APP_DEBUG=true e Model::preventLazyLoading(true)

# 5. Testar endpoints da API
php artisan route:list --path=api

# 6. Verificar cache de resources
php artisan optimize
```

### **Checklist Final**

- [ ] **API Resources** retornam JSON estruturado
- [ ] **Middleware HasMiddleware** implementado em todos os controllers
- [ ] **Form Requests** validam todos os inputs
- [ ] **Eager Loading** previne queries N+1
- [ ] **Scoped Relationships** melhoram performance
- [ ] **Todos os testes** passando (>= 95%)
- [ ] **Pint** formatação aplicada
- [ ] **Performance** otimizada (< 5 queries por endpoint)

### **Resultado Esperado**

| Área | Status Inicial | Status Final | Melhoria |
|------|---------------|-------------|----------|
| **API Resources** | ❌ 2/10 | ✅ 10/10 | +800% |
| **Middleware Strategy** | ⚠️ 6/10 | ✅ 10/10 | +67% |
| **Form Validation** | ⚠️ 5/10 | ✅ 10/10 | +100% |
| **N+1 Prevention** | ❌ 3/10 | ✅ 10/10 | +233% |
| **Scoped Relationships** | ⚠️ 4/10 | ✅ 10/10 | +150% |

---

## 🎊 Conclusão

Após implementar essas melhorias, o Sistema Legisinc estará **100% alinhado** com as melhores práticas do **Laravel 12**, oferecendo:

✅ **Performance superior** com prevenção N+1  
✅ **API padronizada** com Resources  
✅ **Validação robusta** com Form Requests  
✅ **Segurança aprimorada** com middleware específico  
✅ **Código testável** e maintível  
✅ **Arquitetura escalável** para futuro  

**Tempo estimado de implementação:** 4-6 horas  
**Prioridade:** Alta (arquitetura fundamental)  
**Impacto:** Transformação de "bom" para "excelente"

---

*Documento criado em: 30/08/2025*  
*Versão: 1.0*  
*Status: Pronto para implementação*