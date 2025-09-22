# Como Criar a Tela Document Workflow Logs

Este documento serve como referência para recriar a funcionalidade de logs do fluxo de documentos caso seja necessário. A tela `/document-workflow-logs` foi criada para monitorar e analisar o fluxo completo de documentos no sistema.

## 1. Controller - DocumentWorkflowLogController

**Localização:** `app/Http/Controllers/Admin/DocumentWorkflowLogController.php`

### Estrutura Principal

```php
<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TramitacaoLog;
use App\Models\DocumentoWorkflowHistorico;
use App\Models\Proposicao;
use App\Models\User;
use App\Models\ScreenPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DocumentWorkflowLogController extends Controller
{
    // Middleware de autenticação e autorização
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Acesso negado. Apenas administradores podem acessar esta funcionalidade.');
            }
            return $next($request);
        });
    }

    // ... métodos principais
}
```

### Métodos Principais

#### 1. `index()` - Página principal
- Exibe lista de logs com paginação
- Aplica filtros (proposição, usuário, ação, datas)
- Calcula estatísticas
- Integra logs de PDF pós-aprovação
- **Linha 31-122**

#### 2. `show()` - Detalhes de uma proposição específica
- Timeline completo de eventos
- Análise de permissões utilizadas
- **Linha 193-233**

#### 3. `getEstatisticas()` - Estatísticas do dashboard
- Contadores por período (hoje, ontem, semana, mês)
- Média de tempo de tramitação
- **Linha 127-152**

#### 4. `export()` e `exportJson()` - Exportação de dados
- CSV simples e JSON detalhado
- **Linha 456-774**

#### 5. Funcionalidades de logs de PDF
- `getPdfWorkflowLogs()` - Lê logs de arquivo
- `parsePdfLogEntry()` - Parse de linhas de log
- `cleanPdfLogs()` - Limpeza de logs
- **Linha 777-1045**

## 2. Views - Interface Blade

**Localização:** `resources/views/admin/document-workflow-logs/`

### Estrutura de Arquivos
```
document-workflow-logs/
├── index.blade.php      # Tela principal
├── show.blade.php       # Detalhes de proposição
└── detailed-view.blade.php  # View detalhada com filtros
```

### index.blade.php - Principais Elementos

#### Cards de Estatísticas (Linha 89-284)
```php
<!--begin::Row-->
<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <!-- Card 1: Logs Hoje -->
    <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
        <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100"
             style="background-color: #F1416C;">
            <!-- Estatística: $estatisticas['logs_hoje'] -->
        </div>
    </div>
    <!-- ... outros cards similares -->
</div>
```

#### Sistema de Filtros (Linha 310-401)
```php
<button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click">
    Filtros
</button>
<div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px">
    <form method="GET" action="{{ route('admin.document-workflow-logs.index') }}">
        <!-- Filtros: proposicao_id, user_id, acao, datas, include_pdf_logs -->
    </form>
</div>
```

#### Tabela Principal (Linha 414-494)
```php
<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_logs_table">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th class="min-w-125px">Data/Hora</th>
            <th class="min-w-100px">Proposição</th>
            <th class="min-w-125px">Ação</th>
            <th class="min-w-125px">Usuário</th>
            <th class="min-w-125px">Status</th>
            <th class="min-w-200px">Observações</th>
            <th class="text-end min-w-70px">Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($logs as $log)
            <tr>
                <!-- Dados do log com formatação -->
            </tr>
        @endforeach
    </tbody>
</table>
```

#### Seção Logs de PDF (Linha 503-627)
```php
@if($includePdfLogs && $pdfLogs->isNotEmpty())
<div class="card card-flush mt-5">
    <div class="card-header pt-7">
        <h3 class="card-title">📄 Logs de PDFs Pós-Aprovação</h3>
    </div>
    <div class="card-body">
        <!-- Tabela específica para logs de PDF -->
    </div>
</div>
@endif
```

## 3. Rotas - web.php

**Localização:** `routes/web.php` - Linha 1505-1511

```php
Route::prefix('document-workflow-logs')->name('admin.document-workflow-logs.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DocumentWorkflowLogController::class, 'index'])->name('index');
    Route::get('/export', [App\Http\Controllers\Admin\DocumentWorkflowLogController::class, 'export'])->name('export');
    Route::get('/export-json', [App\Http\Controllers\Admin\DocumentWorkflowLogController::class, 'exportJson'])->name('export-json');
    Route::delete('/delete', [App\Http\Controllers\Admin\DocumentWorkflowLogController::class, 'deleteLogs'])->name('delete');
    Route::get('/{proposicao}', [App\Http\Controllers\Admin\DocumentWorkflowLogController::class, 'show'])->name('show');
});
```

## 4. Modelos Utilizados

### Principais Modelos
- **TramitacaoLog**: Logs de tramitação de documentos
- **DocumentoWorkflowHistorico**: Histórico de workflow
- **Proposicao**: Documentos do sistema
- **User**: Usuários do sistema
- **ScreenPermission**: Permissões de tela

### Relacionamentos Importantes
```php
// TramitacaoLog
public function proposicao()
public function usuario()

// DocumentoWorkflowHistorico
public function workflow()
public function transicao()
public function etapaOrigem()
public function etapaDestino()
public function executadoPor()
```

## 5. Funcionalidades Implementadas

### 5.1 Monitoramento em Tempo Real
- Auto-refresh a cada 30 segundos (quando sem filtros)
- Estatísticas atualizadas automaticamente

### 5.2 Sistema de Filtros
- **Proposição**: Filtro por documento específico
- **Usuário**: Filtro por usuário responsável
- **Ação**: Busca textual em ações
- **Período**: Data início/fim
- **Logs PDF**: Incluir/excluir logs de PDF

### 5.3 Exportação de Dados
- **CSV Simples**: Dados básicos formatados
- **JSON Detalhado**: Estrutura completa com metadados

### 5.4 Limpeza de Logs
- Modal de confirmação
- Opções: hoje, semana, mês, todos
- Inclui logs de PDF nos arquivos

### 5.5 Logs de PDF Pós-Aprovação
- Integração com arquivos de log do sistema
- Parse de logs estruturados
- Verificação de integridade de arquivos

## 6. JavaScript e Funcionalidades Frontend

```javascript
// Auto-refresh condicional
@if(empty(array_filter($filtros ?? [])))
setTimeout(function() {
    window.location.reload();
}, 30000);
@endif

// Inicialização Select2
$('[data-control="select2"]').select2({
    dropdownParent: $('[data-kt-menu="true"]')
});

// Busca em tempo real
const filterSearch = document.querySelector('[data-kt-logs-table-filter="search"]');
filterSearch.addEventListener('keyup', function (e) {
    // Filtro de busca na tabela
});
```

## 7. Segurança e Permissões

### Middleware de Segurança
```php
public function __construct()
{
    $this->middleware('auth');
    $this->middleware(function ($request, $next) {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores podem acessar esta funcionalidade.');
        }
        return $next($request);
    });
}
```

### Validação de Arquivos PDF
```php
// Validação rigorosa de nomes de arquivo
if (!preg_match('/^proposicao_\d+_\d+\.rtf$/', $arquivo)) {
    return response('Invalid file format', 400);
}
```

## 8. Padrões de Código Utilizados

### 8.1 Nomenclatura
- **Controllers**: PascalCase com sufixo "Controller"
- **Views**: kebab-case com estrutura hierárquica
- **Rotas**: kebab-case com namespaces

### 8.2 Estrutura de Dados
- Uso de Collections do Laravel para manipulação
- JSON estruturado para exportação
- Timestamps padronizados (Carbon)

### 8.3 Frontend
- Bootstrap 5 + KTMenus (Metronic theme)
- Select2 para dropdowns
- Modais para confirmações

## 9. Como Recriar a Funcionalidade

### Passo 1: Controller
1. Criar `DocumentWorkflowLogController.php` em `app/Http/Controllers/Admin/`
2. Implementar middleware de autenticação
3. Criar método `index()` com filtros e estatísticas
4. Implementar métodos de exportação e limpeza

### Passo 2: Views
1. Criar pasta `document-workflow-logs` em `resources/views/admin/`
2. Implementar `index.blade.php` com cards e tabelas
3. Criar sistema de filtros com dropdown
4. Adicionar seção de logs de PDF

### Passo 3: Rotas
1. Adicionar grupo de rotas em `web.php`
2. Configurar middleware de admin
3. Definir rotas para index, show, export, delete

### Passo 4: JavaScript
1. Implementar auto-refresh condicional
2. Configurar Select2 nos dropdowns
3. Adicionar busca em tempo real
4. Configurar modais de confirmação

## 10. Considerações Importantes

### Performance
- Paginação implementada para grandes volumes
- Índices necessários nas tabelas de log
- Lazy loading para relacionamentos

### Manutenibilidade
- Separação clara de responsabilidades
- Métodos privados para lógica específica
- Documentação inline nas funções complexas

### Extensibilidade
- Interface preparada para novos tipos de log
- Sistema de filtros extensível
- Exportação estruturada para integrações

Este documento fornece a base completa para recriar a funcionalidade de logs do fluxo de documentos caso necessário.