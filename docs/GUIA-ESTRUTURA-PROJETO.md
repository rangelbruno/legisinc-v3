# Guia de Estrutura do Projeto - Sistema Legisinc

## 📁 Estrutura de Pastas do Projeto

### **Raiz do Projeto**
```
/
├── app/                    # Lógica da aplicação Laravel
├── bootstrap/              # Arquivos de inicialização
├── config/                 # Configurações
├── database/               # Migrações, seeders, factories
├── docs/                   # Documentação do projeto
├── public/                 # Assets públicos, imagens, CSS, JS
├── resources/              # Views, assets não compilados
├── routes/                 # Definições de rotas
├── storage/                # Logs, cache, arquivos gerados
├── tests/                  # Testes automatizados
├── vendor/                 # Dependências Composer
├── docker/                 # Configurações Docker
├── scripts/                # Scripts de automação
└── mcp/                    # MCP server configurations
```

---

## 🎨 Sistema de Layouts e Componentes

### **Estrutura de Views**
```
resources/views/
├── layouts/                # Layouts principais (legado)
│   └── app.blade.php      # Layout base original
├── components/             # Sistema de componentes atual
│   ├── layouts/           # Layouts componetizados
│   │   ├── app.blade.php  # Layout principal ATUAL
│   │   ├── header.blade.php
│   │   ├── aside.blade.php
│   │   └── footer.blade.php
│   ├── onlyoffice-editor.blade.php
│   └── permission-card.blade.php
├── auth/                   # Telas de autenticação
├── admin/                  # Área administrativa
├── proposicoes/            # Módulo de proposições
├── documentos/             # Módulo de documentos
└── dashboard.blade.php     # Dashboard principal
```

### **Padrão de Herança de Layout**

#### **ATUAL (Recomendado):**
```php
@extends('components.layouts.app')

@section('title', 'Título da Página - Sistema Parlamentar')

@section('content')
    <!-- Conteúdo da página -->
@endsection
```

#### **LEGADO (Evitar):**
```php
@extends('layouts.app')  # ❌ Layout antigo
```

---

## 🎨 Sistema de Estilos e Assets

### **Estrutura de Assets Públicos**
```
public/assets/
├── css/                    # CSS customizado
├── js/                     # JavaScript customizado
├── media/                  # Imagens, ícones, logos
│   ├── logos/
│   ├── patterns/          # Padrões visuais
│   └── backgrounds/
└── plugins/               # Plugins terceiros
    ├── global/
    ├── custom/
    └── datatables/
```

### **Framework CSS Utilizado**
- **Keen Theme** (Template Bootstrap customizado)
- **Bootstrap 5** como base
- **Font Inter** como fonte principal
- **Plugins**: DataTables, FullCalendar, OnlyOffice

### **Assets Principais do Layout**
```html
<!-- Fontes -->
<link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />

<!-- CSS Global -->
<link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" />
<link href="{{ asset('assets/css/style.bundle.css') }}" />

<!-- CSS Específicos -->
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" />
<link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" />
```

---

## 🎯 Sistema JavaScript e Vue.js

### **Estrutura JavaScript**
```
resources/js/
├── app.js                  # Entrada principal
├── bootstrap.js            # Configurações básicas
└── components/
    └── OnlyOfficeMonitor.vue  # Monitor OnlyOffice
```

### **Configuração Vue.js**
```javascript
// resources/js/app.js
import './bootstrap';
import { createApp } from 'vue';
import OnlyOfficeMonitor from './components/OnlyOfficeMonitor.vue';

// Registro global
window.Vue = { createApp };
window.OnlyOfficeMonitorComponent = OnlyOfficeMonitor;
```

### **Uso nos Templates**
```html
<!-- Inicialização Vue no layout -->
<script>
// Vue é carregado automaticamente via app.js
</script>
```

---

## 🏗️ Estrutura de Controllers e Rotas

### **Organização de Controllers**
```
app/Http/Controllers/
├── Admin/                  # Controllers administrativos
├── Api/                    # Controllers API
├── Auth/                   # Autenticação
├── Parlamentar/            # Área parlamentar
├── Protocolo/              # Área de protocolo
├── Legislativo/            # Área legislativa
├── OnlyOffice/             # Integração OnlyOffice
└── [NomeController].php    # Controllers principais
```

### **Padrão de Rotas**
```php
// routes/web.php

// Rotas públicas
Route::get('/', function () {
    return redirect()->route('login');
});

// Rotas de autenticação
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Rotas protegidas
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Agrupamento por módulo
    Route::prefix('proposicoes')->name('proposicoes.')->group(function () {
        Route::get('/', [ProposicaoController::class, 'index'])->name('index');
        Route::get('/create', [ProposicaoController::class, 'create'])->name('create');
    });
});
```

---

## 🎨 Componentes e Padrões UI

### **Layout Principal**
O layout usa a estrutura Keen Theme com:
- **Header**: Navegação superior, notificações, perfil
- **Aside**: Menu lateral navegacional
- **Content**: Área de conteúdo principal
- **Footer**: Rodapé institucional

### **Componentes Reutilizáveis**
```
components/
├── onlyoffice-editor.blade.php    # Editor OnlyOffice
├── permission-card.blade.php      # Card de permissões
└── layouts/                       # Layouts estruturais
```

### **Classes CSS Padrão**
```css
/* Cards Dashboard */
.dashboard-card-primary    /* Gradient vermelho */
.dashboard-card-info       /* Gradient roxo */
.dashboard-card-success    /* Gradient verde */
.dashboard-card-warning    /* Gradient amarelo */

/* Notificações */
#kt_activities .timeline-item      /* Item timeline */
#kt_activities .timeline-content   /* Conteúdo notificação */

/* Botões padrão */
.btn-sm                    /* Botão pequeno */
.btn-light-primary         /* Botão primário light */
```

---

## 📋 Padrões de Desenvolvimento

### **1. Criação de Nova Tela**

#### **Passo 1: Controller**
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NovoModuloController extends Controller
{
    public function index()
    {
        return view('novo-modulo.index');
    }

    public function create()
    {
        return view('novo-modulo.create');
    }
}
```

#### **Passo 2: Rota**
```php
// routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::prefix('novo-modulo')->name('novo-modulo.')->group(function () {
        Route::get('/', [NovoModuloController::class, 'index'])->name('index');
        Route::get('/create', [NovoModuloController::class, 'create'])->name('create');
    });
});
```

#### **Passo 3: View**
```php
{{-- resources/views/novo-modulo/index.blade.php --}}
@extends('components.layouts.app')

@section('title', 'Novo Módulo - Sistema Parlamentar')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Novo Módulo
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Novo Módulo</li>
                </ul>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <!-- Conteúdo da página aqui -->
        </div>
    </div>
    <!--end::Content-->
</div>
@endsection
```

### **2. Estrutura Padrão de Card**
```html
<!--begin::Card-->
<div class="card">
    <!--begin::Card header-->
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3>Título do Card</h3>
        </div>
        <div class="card-toolbar">
            <!-- Botões de ação -->
        </div>
    </div>
    <!--end::Card header-->
    
    <!--begin::Card body-->
    <div class="card-body pt-0">
        <!-- Conteúdo do card -->
    </div>
    <!--end::Card body-->
</div>
<!--end::Card-->
```

### **3. Padrão de Tabela DataTable**
```html
<!--begin::Table-->
<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_exemplo">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th>Coluna 1</th>
            <th>Coluna 2</th>
            <th class="text-end min-w-100px">Ações</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        <!-- Dados da tabela -->
    </tbody>
</table>
<!--end::Table-->
```

---

## 🔧 Models e Migrations

### **Estrutura de Models**
```
app/Models/
├── User.php                       # Usuário base
├── Proposicao.php                 # Proposição legislativa
├── TipoProposicao.php            # Tipos de proposição
├── Documento/                     # Módulo documentos
│   ├── DocumentoModelo.php
│   ├── DocumentoInstancia.php
│   └── DocumentoVersao.php
├── Parametro/                     # Sistema de parâmetros
│   ├── ParametroModulo.php
│   ├── ParametroCampo.php
│   └── ParametroValor.php
└── ScreenPermission.php           # Permissões de tela
```

### **Padrão de Migration**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nome_tabela', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nome_tabela');
    }
};
```

---

## 📱 Sistema de Permissões

### **Middleware de Permissões**
```php
// Uso nas rotas
Route::middleware(['auth', 'permission:modulo.acao'])->group(function () {
    // Rotas protegidas
});
```

### **Verificação nas Views**
```php
@can('modulo.acao')
    <!-- Conteúdo com permissão -->
@endcan

@cannot('modulo.acao')
    <!-- Conteúdo sem permissão -->
@endcannot
```

---

## 🎯 Boas Práticas de Desenvolvimento

### **1. Nomenclatura**
- **Controllers**: `NomeModuloController`
- **Models**: `NomeModelo` (singular)
- **Views**: `nome-modulo/acao.blade.php`
- **Rotas**: `nome-modulo.acao`

### **2. Organização de Code**
- Um controller por módulo principal
- Separar controllers complexos em subpastas
- Usar Resource Controllers para CRUDs padrão
- Implementar Form Requests para validação

### **3. Performance**
- Lazy loading de relacionamentos
- Cache de queries frequentes
- Otimização de assets com Vite
- Compressão de imagens

### **4. Segurança**
- CSRF protection em todos os forms
- Validação de permissões em controllers
- Sanitização de inputs
- Proteção contra XSS

---

## 🗄️ Estrutura do Banco de Dados

### **Informações Gerais**
```
Database Engine: PostgreSQL 15.13
Connection: pgsql
Database Name: legisinc
Host: db (Docker)
Port: 5432
Total Tables: 53
```

### **Tabelas Principais**

#### **🏛️ Módulo Legislativo**

**`proposicoes` (62 campos)**
- **ID**: `id` (bigint, PK, auto-increment)
- **Identificação**: `tipo`, `numero`, `numero_protocolo`, `ano`
- **Conteúdo**: `ementa`, `conteudo`, `conteudo_processado`
- **Arquivos**: `arquivo_path`, `arquivo_pdf_path`, `pdf_assinado_path`
- **Status**: `status` (rascunho, revisao, aprovado_assinatura, etc.)
- **Datas**: `created_at`, `data_protocolo`, `data_assinatura`
- **Relacionamentos**: `autor_id` → `users`, `revisor_id` → `users`
- **Template**: `template_id`, `template_usado`, `variaveis_template`
- **Assinatura**: `assinatura_digital`, `certificado_digital`, `codigo_validacao`

**Indexes Otimizados:**
- `idx_proposicoes_numeracao` (tipo, ano, numero)
- `idx_proposicoes_status_data` (status, created_at)
- `proposicoes_autor_id_status_index` (autor_id, status)

**`tipo_proposicoes` (11 campos)**
- **Identificação**: `codigo` (único), `nome`, `descricao`
- **Aparência**: `icone`, `cor`, `ordem`
- **Configuração**: `configuracoes` (JSON), `ativo`

#### **👥 Sistema de Usuários e Permissões**

**`users` (18 campos)**
- **Básico**: `id`, `name`, `email`, `password`
- **Perfil**: `documento`, `telefone`, `profissao`, `cargo_atual`
- **Sistema**: `ativo`, `ultimo_acesso`, `preferencias` (JSON)
- **Político**: `partido`

**`roles` e Sistema Spatie**
- `roles` - Papéis do sistema
- `permissions` - Permissões específicas
- `model_has_roles` - Usuários x Papéis
- `role_has_permissions` - Papéis x Permissões
- `screen_permissions` - Permissões por tela (237KB)

#### **📄 Sistema de Templates**

**`template_universal` (163KB)**
- Templates RTF universais do sistema
- Variáveis dinâmicas e processamento

**`tipo_proposicao_templates` (98KB)**
- Templates específicos por tipo de proposição
- Relacionamento com `tipo_proposicoes`

#### **⚙️ Sistema de Parâmetros**

**Hierarquia de Parâmetros:**
```
parametros_modulos
    ├── parametros_submodulos  
        ├── parametros_campos
            └── parametros_valores
```

**`parametros` (163KB) - Tabela Central**
- Configurações do sistema
- Valores dinâmicos por contexto

**`grupos_parametros` (98KB)**
- Agrupamento lógico de parâmetros

#### **📊 Auditoria e Logs**

**`auditoria_parametros` (106KB)**
- Log completo de alterações nos parâmetros
- Rastreabilidade total do sistema

**`permission_access_log` (73KB)**
- Log de acesso às funcionalidades
- Monitoramento de segurança

**`melhorias_tracking` (131KB)**
- Tracking de melhorias automáticas
- Sistema de preservação v2.0

#### **🤖 Sistema AI/IA**

**`ai_configurations`, `ai_providers`, `ai_token_usage`**
- Configurações de IA
- Provedores e controle de tokens
- Integração com GPT/Claude

#### **📋 Documentos e Protocolos**

**`documento_modelos`, `documento_instancias`, `documento_versoes`**
- Sistema de documentos colaborativos
- Versionamento e instâncias

**`tramitacao_logs` (81KB)**
- Log de tramitação legislativa
- Histórico de movimentações

### **Relacionamentos Principais**

```sql
-- Proposições → Usuários
proposicoes.autor_id → users.id
proposicoes.revisor_id → users.id
proposicoes.funcionario_protocolo_id → users.id

-- Proposições → Pareceres
proposicoes.parecer_id → parecer_juridicos.id

-- Sistema de Permissões
users → model_has_roles → roles
roles → role_has_permissions → permissions
```

### **Índices de Performance**

**Proposições (Otimizadas para consultas frequentes):**
- Numeração: `(tipo, ano, numero)`
- Status e Data: `(status, created_at)`
- Autor e Status: `(autor_id, status)`
- PDF e Protocolo: `(pdf_protocolo_aplicado)`

**Usuários (Consultas administrativas):**
- Email único: `(email)`
- Documento: `(documento)`
- Status: `(ativo)`

### **Configuração de Conexão**

```php
// config/database.php - PostgreSQL
'connections' => [
    'pgsql' => [
        'driver' => 'pgsql',
        'host' => env('DB_HOST', 'db'),
        'port' => env('DB_PORT', '5432'),
        'database' => env('DB_DATABASE', 'legisinc'),
        'username' => env('DB_USERNAME', 'postgres'),
        'charset' => 'UTF8',
        'prefix' => '',
        'schema' => 'public',
        'sslmode' => 'disable',
    ],
]
```

### **Comandos MCP Úteis**

```bash
# Listar todas as tabelas
docker exec legisinc-app php artisan db:show --json

# Analisar tabela específica  
docker exec legisinc-app php artisan db:table users --json
docker exec legisinc-app php artisan db:table proposicoes --json

# Via MCP Laravel Boost
# mcp__laravel-boost__database-schema (quando disponível)
```

---

## 📚 Recursos Específicos do Sistema

### **OnlyOffice Integration**
- Monitor Vue.js para sincronização
- Polling automático a cada 15s
- Priorização de arquivos salvos
- Cache inteligente de documentos

### **Sistema de Templates**
- Templates RTF com variáveis
- Processamento automático de parâmetros
- Suporte a imagens e formatação
- Template Universal como fallback

### **Assinatura Digital**
- Validação de certificados A1/A3
- Geração de PDFs assinados
- QR Code de validação
- Certificados de autenticidade

---

**🎊 GUIA COMPLETO v1.0**

**Última atualização**: 07/09/2025  
**Versão do Sistema**: v2.1 Enterprise