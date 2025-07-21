# Sistema de Parâmetros Modular - LegisInc

## Contexto do Projeto

O **LegisInc** é um sistema de gestão legislativa em Laravel 12 que precisa de uma refatoração completa do sistema de parâmetros. A estrutura atual precisa ser transformada em um sistema modular flexível que centralize configurações de todos os módulos do sistema.

## Arquitetura Atual do LegisInc

### Stack Tecnológico
- **Laravel**: 12.0
- **PHP**: ^8.2
- **Spatie Laravel Permission**: ^6.20
- **PostgreSQL**: Banco de dados principal
- **Vite**: ^6.2.4 (Build system)
- **TailwindCSS**: ^4.0.0
- **Template**: Metronic (interface administrativa)

### Estrutura Modular Existente
```
app/
├── Http/Controllers/
│   ├── Projeto/
│   ├── User/
│   ├── Comissao/
│   ├── Parlamentar/
│   └── MockApiController.php
├── Models/
├── Services/
├── DTOs/
├── Policies/
└── Providers/
```

## Objetivo: Sistema de Parâmetros Centralizados

### Estrutura Hierárquica Proposta
```
Sistema de Parâmetros
├── Módulos (ex: "Dados da Câmara", "Configurações da Sessão")
│   ├── Submódulos (ex: "Formulário dados da câmara", "Checkbox Veto")
│   │   ├── Campos/Formulários específicos
│   │   └── Validações e regras de negócio
```

### Módulos Identificados para Implementação

#### 1. Módulo Dados da Câmara
- **Submódulo**: Formulário institucional
- **Campos**: Nome, Endereço, Tipo de Integração, Qtd Vereadores, Qtd Quorum, Tempo Sessão, Logotipo
- **Validações**: Obrigatórios, formatos específicos

#### 2. Módulo Configurações da Sessão
- **Submódulos**: Checkboxes de controle
- **Campos**: Veto (Acato/Não Acato), Iniciar Expediente, Abster, Chamada Automática, Pop-up Votação
- **Tipo**: Boolean toggles

#### 3. Módulo Tipo de Sessão
- **Submódulo**: Cadastro de tipos
- **Campos**: Nome, Descrição, Status (Ativo/Inativo)
- **Relacionamento**: Com sessões plenárias

#### 4. Módulo Momento da Sessão
- **Submódulo**: Cadastro de momentos
- **Campos**: Nome, Descrição, Ordem, Status (Ativo/Inativo)
- **Relacionamento**: Com tramitação de projetos

#### 5. Módulo Tipo de Votação
- **Submódulo**: Cadastro de tipos de votação
- **Campos**: Nome, Descrição, Regras, Status (Ativo/Inativo)
- **Relacionamento**: Com sistema de votação

## Implementação Técnica

### 1. Estrutura de Banco de Dados

#### Migration: parametros_modulos
```php
Schema::create('parametros_modulos', function (Blueprint $table) {
    $table->id();
    $table->string('nome');
    $table->text('descricao')->nullable();
    $table->string('icon')->nullable(); // Para ícones ki-duotone
    $table->integer('ordem')->default(0);
    $table->boolean('ativo')->default(true);
    $table->timestamps();
    
    $table->index(['ativo', 'ordem']);
});
```

#### Migration: parametros_submodulos
```php
Schema::create('parametros_submodulos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('modulo_id')->constrained('parametros_modulos')->cascadeOnDelete();
    $table->string('nome');
    $table->text('descricao')->nullable();
    $table->enum('tipo', ['form', 'checkbox', 'select', 'toggle', 'custom']);
    $table->json('config')->nullable(); // Configurações específicas
    $table->integer('ordem')->default(0);
    $table->boolean('ativo')->default(true);
    $table->timestamps();
    
    $table->index(['modulo_id', 'ativo', 'ordem']);
});
```

#### Migration: parametros_campos
```php
Schema::create('parametros_campos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('submodulo_id')->constrained('parametros_submodulos')->cascadeOnDelete();
    $table->string('nome');
    $table->string('label');
    $table->enum('tipo_campo', ['text', 'email', 'number', 'textarea', 'select', 'checkbox', 'radio', 'file', 'date', 'datetime']);
    $table->text('descricao')->nullable();
    $table->boolean('obrigatorio')->default(false);
    $table->text('valor_padrao')->nullable();
    $table->json('opcoes')->nullable(); // Para select, radio, etc.
    $table->json('validacao')->nullable(); // Regras de validação
    $table->string('placeholder')->nullable();
    $table->string('classe_css')->nullable();
    $table->integer('ordem')->default(0);
    $table->boolean('ativo')->default(true);
    $table->timestamps();
    
    $table->index(['submodulo_id', 'ativo', 'ordem']);
});
```

#### Migration: parametros_valores
```php
Schema::create('parametros_valores', function (Blueprint $table) {
    $table->id();
    $table->foreignId('campo_id')->constrained('parametros_campos')->cascadeOnDelete();
    $table->text('valor');
    $table->string('tipo_valor')->default('string'); // string, json, boolean, etc.
    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
    $table->timestamp('valido_ate')->nullable();
    $table->timestamps();
    
    $table->index(['campo_id', 'valido_ate']);
});
```

### 2. Estrutura de Arquivos Laravel

#### Models
```php
// app/Models/Parametro/ParametroModulo.php
// app/Models/Parametro/ParametroSubmodulo.php
// app/Models/Parametro/ParametroCampo.php
// app/Models/Parametro/ParametroValor.php
```

#### Controllers
```php
// app/Http/Controllers/Parametro/ParametroController.php
// app/Http/Controllers/Parametro/ModuloController.php
// app/Http/Controllers/Parametro/SubmoduloController.php
// app/Http/Controllers/Parametro/CampoController.php
```

#### Services
```php
// app/Services/Parametro/ParametroService.php
// app/Services/Parametro/ValidacaoService.php
// app/Services/Parametro/ConfiguracaoService.php
```

#### DTOs
```php
// app/DTOs/Parametro/ParametroDTO.php
// app/DTOs/Parametro/ModuloDTO.php
// app/DTOs/Parametro/CampoDTO.php
```

### 3. Interface Administrativa (Metronic)

#### Views Structure
```php
resources/views/modules/parametros/
├── index.blade.php          # Listagem de parâmetros
├── create.blade.php         # Criação de novo parâmetro
├── edit.blade.php           # Edição de parâmetro
├── show.blade.php           # Visualização de parâmetro
├── components/
│   ├── modulo-card.blade.php     # Card de módulo
│   ├── campo-form.blade.php      # Formulário de campo
│   └── valor-input.blade.php     # Input de valor
└── partials/
    ├── filtros.blade.php         # Filtros dinâmicos
    └── modal-confirm.blade.php   # Modal de confirmação
```

#### Características da Interface
- **Grid/List View**: Visualização dupla como nos modelos existentes
- **Cards Interativos**: Sistema de cards com hover effects
- **Ícones ki-duotone**: Ícones específicos para cada tipo de parâmetro
- **Filtros Dinâmicos**: Busca e filtros em tempo real
- **Modal Confirmations**: Confirmações elegantes para ações críticas
- **Design Responsivo**: Interface otimizada para todos os dispositivos

### 4. Sistema de Validação Centralizado

#### Service de Validação
```php
// app/Services/Parametro/ValidacaoService.php
class ValidacaoService 
{
    public function validar(string $modulo, string $submodulo, mixed $valor): bool
    {
        // Busca configurações do parâmetro
        // Aplica validações
        // Retorna resultado
    }
    
    public function obterConfiguracoes(string $modulo, string $submodulo): array
    {
        // Retorna configurações com cache
    }
}
```

#### Middleware de Validação
```php
// app/Http/Middleware/ValidacaoParametrosMiddleware.php
class ValidacaoParametrosMiddleware
{
    public function handle(Request $request, Closure $next, ...$parametros)
    {
        foreach ($parametros as $parametro) {
            // Valida parâmetros necessários
        }
        
        return $next($request);
    }
}
```

### 5. API Routes

#### Rotas de Parâmetros
```php
// routes/api.php - Seguindo padrão do projeto
Route::prefix('parametros')->group(function () {
    Route::get('/', [ParametroController::class, 'index']);
    Route::post('/', [ParametroController::class, 'store']);
    Route::get('/modulos', [ModuloController::class, 'index']);
    Route::post('/modulos', [ModuloController::class, 'store']);
    Route::get('/validar/{modulo}/{submodulo}', [ParametroController::class, 'validar']);
});
```

#### Rotas Web
```php
// routes/web.php - Seguindo padrão do projeto
Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {
    Route::resource('parametros', ParametroController::class);
    Route::get('parametros/configurar/{modulo}', [ParametroController::class, 'configurar']);
});
```

### 6. Integração com Sistema Existente

#### Cache Integration
```php
// Usar sistema de cache do Laravel
use Illuminate\Support\Facades\Cache;

Cache::remember("parametros.{$modulo}.{$submodulo}", 3600, function () {
    // Buscar configurações
});
```

#### Permission Integration
```php
// Usar Spatie Laravel Permission existente
$this->middleware('permission:parametros.view');
$this->middleware('permission:parametros.edit');
```

## Tarefas Específicas de Implementação

### 1. Database Layer
- [ ] Criar migrations para as 4 tabelas de parâmetros
- [ ] Criar models com relacionamentos Eloquent
- [ ] Implementar seeders para parâmetros iniciais
- [ ] Criar factories para testes

### 2. Business Logic
- [ ] Implementar services para lógica de negócio
- [ ] Criar DTOs para transferência de dados
- [ ] Implementar sistema de validação centralizado
- [ ] Criar middleware de validação

### 3. API Layer
- [ ] Implementar controllers seguindo padrão do projeto
- [ ] Criar resource classes para API responses
- [ ] Implementar políticas de autorização
- [ ] Documentar endpoints na API existente

### 4. Frontend Layer
- [ ] Criar views Blade seguindo padrão Metronic
- [ ] Implementar componentes reutilizáveis
- [ ] Criar sistema de filtros dinâmicos
- [ ] Implementar interface Grid/List View

### 5. Integration Layer
- [ ] Integrar com sistema de cache existente
- [ ] Conectar com sistema de permissões
- [ ] Criar comandos Artisan para migração
- [ ] Implementar logging e auditoria

### 6. Testing Layer
- [ ] Criar testes unitários com PestPHP
- [ ] Implementar testes de integração
- [ ] Criar testes de API
- [ ] Implementar testes de interface

## Exemplo de Uso no Sistema

### Validação em Controllers
```php
// app/Http/Controllers/Projeto/ProjetoController.php
public function store(Request $request)
{
    // Validar se tipo de projeto está ativo
    if (!$this->parametroService->validar('tipo_projeto', 'projeto_lei', $request->tipo)) {
        throw new ValidationException('Tipo de projeto não permitido');
    }
    
    // Continuar com a lógica...
}
```

### Configuração Dinâmica
```php
// Obter configurações da câmara
$configuracoes = $this->parametroService->obterConfiguracoes('dados_camara', 'formulario_institucional');

// Aplicar configurações no sistema
$this->aplicarConfiguracoes($configuracoes);
```

## Critérios de Aceite

- [ ] Sistema permite criar parâmetros dinâmicos via interface
- [ ] Validação centralizada funcionando em todos os módulos
- [ ] Interface administrativa seguindo padrão Metronic
- [ ] Integração completa com sistema de permissões
- [ ] Migração dos parâmetros existentes sem perda de dados
- [ ] Performance otimizada com cache
- [ ] Testes unitários e de integração passando
- [ ] Documentação técnica completa
- [ ] API endpoints documentados
- [ ] Sistema de auditoria implementado

## Comandos Artisan Personalizados

```bash
# Gerenciar parâmetros
php artisan parametros:create {modulo} {submodulo}
php artisan parametros:migrate-existing
php artisan parametros:cache-clear
php artisan parametros:validate-all

# Seed parâmetros iniciais
php artisan parametros:seed --modulo=dados_camara
php artisan parametros:seed --all
```

Por favor, implemente esta solução completa considerando:
- Arquitetura Laravel 12 existente
- Integração com template Metronic
- Sistema de permissões Spatie
- Padrões de código do projeto
- Performance e escalabilidade
- Testes automatizados com PestPHP