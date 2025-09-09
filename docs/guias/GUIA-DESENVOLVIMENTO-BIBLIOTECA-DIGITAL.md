# 📚 Guia de Desenvolvimento - Módulo Biblioteca Digital

## 📋 Visão Geral

**Módulo**: Biblioteca Digital  
**Objetivo**: Sistema completo de gestão de acervo digital parlamentar  
**Complexidade**: Alta  
**Tipo**: CRUD + Vue.js + API REST  
**Integração**: OnlyOffice, Sistema de Busca, Upload de Arquivos

---

## 🏗️ Estrutura do Projeto

### **1. Estrutura de Pastas**

```
app/
├── Http/Controllers/
│   ├── BibliotecaController.php              # Controller principal
│   ├── Api/BibliotecaApiController.php       # API REST
│   └── Admin/BibliotecaAdminController.php   # Administração
├── Models/
│   ├── BibliotecaItem.php                    # Item do acervo
│   ├── BibliotecaCategoria.php               # Categorias
│   ├── BibliotecaMetadata.php                # Metadados
│   └── BibliotecaAcesso.php                  # Log de acessos
├── Services/
│   ├── BibliotecaDigitalizacaoService.php    # Digitalização
│   ├── BibliotecaSearchService.php           # Busca avançada
│   └── BibliotecaMetadataService.php         # Processamento de metadados
└── Http/Requests/
    ├── BibliotecaStoreRequest.php
    └── BibliotecaUpdateRequest.php

resources/
└── views/
    └── biblioteca/
        ├── index.blade.php                   # Lista do acervo
        ├── show.blade.php                    # Visualizar item
        ├── create.blade.php                  # Adicionar item
        ├── edit.blade.php                    # Editar item
        ├── search.blade.php                  # Busca avançada
        ├── admin/
        │   ├── dashboard.blade.php           # Dashboard admin
        │   ├── categorias.blade.php          # Gestão de categorias
        │   ├── metadados.blade.php           # Configuração de metadados
        │   └── digitalizacao.blade.php       # Painel de digitalização
        └── components/
            ├── item-card.blade.php           # Card do item
            ├── search-filters.blade.php      # Filtros de busca
            └── metadata-form.blade.php       # Formulário de metadados

resources/js/components/
├── BibliotecaSearch.vue                      # Busca interativa
├── BibliotecaViewer.vue                      # Visualizador de documentos
├── BibliotecaUpload.vue                      # Upload com drag & drop
└── BibliotecaMetadata.vue                    # Editor de metadados

database/migrations/
├── 2026_01_01_000001_create_biblioteca_categorias_table.php
├── 2026_01_01_000002_create_biblioteca_items_table.php
├── 2026_01_01_000003_create_biblioteca_metadata_table.php
└── 2026_01_01_000004_create_biblioteca_acessos_table.php
```

---

## 🎯 Regras de Negócio

### **1. Controle de Acesso**

| Perfil | Permissões |
|--------|------------|
| **ADMIN** | Acesso total, configurações, relatórios |
| **BIBLIOTECARIO** | CRUD completo, digitalização, metadados |
| **LEGISLATIVO** | Visualizar, buscar, baixar documentos legislativos |
| **PARLAMENTAR** | Visualizar, buscar, acessar documentos públicos |
| **PROTOCOLO** | Visualizar, buscar documentos protocolados |
| **PÚBLICO** | Apenas documentos com acesso público |

### **2. Categorização**

```
📁 Legislação
  ├── 📄 Leis Municipais
  ├── 📄 Decretos
  ├── 📄 Portarias
  └── 📄 Resoluções

📁 Proposições
  ├── 📄 Projetos de Lei
  ├── 📄 Moções
  ├── 📄 Indicações
  └── 📄 Requerimentos

📁 Atas e Sessões
  ├── 📄 Atas de Sessões
  ├── 📄 Pautas
  └── 📄 Votações

📁 Documentos Administrativos
  ├── 📄 Contratos
  ├── 📄 Relatórios
  └── 📄 Correspondências

📁 Acervo Histórico
  ├── 📄 Documentos Históricos
  ├── 📄 Fotografias
  └── 📄 Jornais da Época
```

### **3. Estados do Item**

- **Rascunho**: Em preparação, não visível
- **Processamento**: Sendo digitalizado/processado
- **Indexado**: Metadados aplicados, pronto para busca  
- **Público**: Disponível para acesso público
- **Restrito**: Acesso apenas para perfis específicos
- **Arquivado**: Mantido mas não exibido nas buscas padrão

### **4. Metadados Obrigatórios**

```json
{
  "titulo": "string, required",
  "descricao": "text, required",
  "categoria_id": "integer, required",
  "data_criacao": "date, required",
  "autor": "string, nullable",
  "palavras_chave": "array, nullable",
  "numero_protocolo": "string, nullable",
  "classificacao": "enum[público,restrito,confidencial]",
  "formato_original": "string",
  "tamanho_arquivo": "integer",
  "checksum_md5": "string"
}
```

---

## 👨‍💻 Desenvolvimento Passo a Passo

### **Passo 1: Criar as Migrations**

```bash
# Criar migrations na ordem correta
php artisan make:migration create_biblioteca_categorias_table
php artisan make:migration create_biblioteca_items_table  
php artisan make:migration create_biblioteca_metadata_table
php artisan make:migration create_biblioteca_acessos_table
```

**Migration - Categorias:**
```php
Schema::create('biblioteca_categorias', function (Blueprint $table) {
    $table->id();
    $table->string('nome');
    $table->text('descricao')->nullable();
    $table->string('icone')->default('ki-folder');
    $table->string('cor')->default('#009ef7');
    $table->unsignedBigInteger('parent_id')->nullable();
    $table->integer('ordem')->default(0);
    $table->boolean('ativo')->default(true);
    $table->timestamps();
    
    $table->foreign('parent_id')->references('id')->on('biblioteca_categorias');
    $table->index(['ativo', 'ordem']);
});
```

**Migration - Itens:**
```php
Schema::create('biblioteca_items', function (Blueprint $table) {
    $table->id();
    $table->string('titulo');
    $table->text('descricao');
    $table->unsignedBigInteger('categoria_id');
    $table->string('arquivo_path');
    $table->string('arquivo_original');
    $table->string('mime_type');
    $table->bigInteger('tamanho_bytes');
    $table->string('checksum_md5');
    $table->enum('status', ['rascunho', 'processamento', 'indexado', 'publico', 'restrito', 'arquivado']);
    $table->enum('classificacao', ['publico', 'restrito', 'confidencial']);
    $table->date('data_documento');
    $table->string('autor')->nullable();
    $table->json('palavras_chave')->nullable();
    $table->string('numero_protocolo')->nullable();
    $table->integer('downloads')->default(0);
    $table->integer('visualizacoes')->default(0);
    $table->unsignedBigInteger('uploaded_by');
    $table->timestamp('indexado_em')->nullable();
    $table->timestamps();
    
    $table->foreign('categoria_id')->references('id')->on('biblioteca_categorias');
    $table->foreign('uploaded_by')->references('id')->on('users');
    $table->index(['status', 'classificacao']);
    $table->index(['categoria_id', 'status']);
    $table->fullText(['titulo', 'descricao', 'autor']);
});
```

### **Passo 2: Criar os Models**

**BibliotecaItem.php:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable; // Para busca avançada

class BibliotecaItem extends Model
{
    use SoftDeletes, Searchable;

    protected $table = 'biblioteca_items';

    protected $fillable = [
        'titulo', 'descricao', 'categoria_id', 'arquivo_path',
        'arquivo_original', 'mime_type', 'tamanho_bytes',
        'checksum_md5', 'status', 'classificacao', 'data_documento',
        'autor', 'palavras_chave', 'numero_protocolo', 'uploaded_by'
    ];

    protected $casts = [
        'palavras_chave' => 'array',
        'data_documento' => 'date',
        'indexado_em' => 'datetime',
    ];

    // Relacionamentos
    public function categoria()
    {
        return $this->belongsTo(BibliotecaCategoria::class, 'categoria_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function metadados()
    {
        return $this->hasMany(BibliotecaMetadata::class, 'item_id');
    }

    public function acessos()
    {
        return $this->hasMany(BibliotecaAcesso::class, 'item_id');
    }

    // Scopes
    public function scopePublico($query)
    {
        return $query->where('classificacao', 'publico')
                    ->where('status', 'publico');
    }

    public function scopeAcessivelPara($query, User $user)
    {
        if ($user->isAdmin()) {
            return $query;
        }
        
        if ($user->hasRole(['BIBLIOTECARIO', 'LEGISLATIVO'])) {
            return $query->whereIn('classificacao', ['publico', 'restrito']);
        }
        
        return $query->publico();
    }

    // Accessors
    public function getTamanhoFormatadoAttribute()
    {
        return $this->formatBytes($this->tamanho_bytes);
    }

    private function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }

    // Scout/Search
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'autor' => $this->autor,
            'palavras_chave' => $this->palavras_chave,
            'numero_protocolo' => $this->numero_protocolo,
            'categoria' => $this->categoria->nome ?? '',
        ];
    }
}
```

### **Passo 3: Criar Controllers**

**BibliotecaController.php:**
```php
<?php

namespace App\Http\Controllers;

use App\Models\BibliotecaItem;
use App\Models\BibliotecaCategoria;
use App\Services\BibliotecaSearchService;
use App\Http\Requests\BibliotecaStoreRequest;
use Illuminate\Http\Request;

class BibliotecaController extends Controller
{
    protected $searchService;

    public function __construct(BibliotecaSearchService $searchService)
    {
        $this->middleware('auth');
        $this->searchService = $searchService;
    }

    public function index(Request $request)
    {
        $query = BibliotecaItem::with(['categoria', 'uploader'])
                              ->acessivelPara(auth()->user());
        
        // Filtros
        if ($request->categoria_id) {
            $query->where('categoria_id', $request->categoria_id);
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('titulo', 'like', "%{$request->search}%")
                  ->orWhere('descricao', 'like', "%{$request->search}%")
                  ->orWhere('autor', 'like', "%{$request->search}%");
            });
        }

        $items = $query->orderBy('created_at', 'desc')->paginate(12);
        $categorias = BibliotecaCategoria::ativo()->orderBy('ordem')->get();

        return view('biblioteca.index', compact('items', 'categorias'));
    }

    public function show(BibliotecaItem $biblioteca)
    {
        // Verificar permissão de acesso
        if (!$this->podeAcessar($biblioteca)) {
            abort(403, 'Acesso negado a este documento');
        }

        // Registrar visualização
        $biblioteca->increment('visualizacoes');
        $this->registrarAcesso($biblioteca, 'visualizacao');

        return view('biblioteca.show', compact('biblioteca'));
    }

    public function create()
    {
        $this->authorize('create', BibliotecaItem::class);
        
        $categorias = BibliotecaCategoria::ativo()->orderBy('nome')->get();
        return view('biblioteca.create', compact('categorias'));
    }

    public function store(BibliotecaStoreRequest $request)
    {
        $this->authorize('create', BibliotecaItem::class);

        $item = new BibliotecaItem($request->validated());
        $item->uploaded_by = auth()->id();
        
        // Processar upload do arquivo
        if ($request->hasFile('arquivo')) {
            $arquivo = $request->file('arquivo');
            $path = $arquivo->store('biblioteca/documentos', 'local');
            
            $item->arquivo_path = $path;
            $item->arquivo_original = $arquivo->getClientOriginalName();
            $item->mime_type = $arquivo->getMimeType();
            $item->tamanho_bytes = $arquivo->getSize();
            $item->checksum_md5 = md5_file($arquivo->getRealPath());
        }
        
        $item->save();

        // Processar metadados automaticamente
        dispatch(new ProcessarMetadados($item));

        return redirect()
               ->route('biblioteca.show', $item)
               ->with('success', 'Documento adicionado à biblioteca digital');
    }

    public function download(BibliotecaItem $biblioteca)
    {
        if (!$this->podeAcessar($biblioteca)) {
            abort(403, 'Acesso negado a este documento');
        }

        // Registrar download
        $biblioteca->increment('downloads');
        $this->registrarAcesso($biblioteca, 'download');

        return Storage::download(
            $biblioteca->arquivo_path,
            $biblioteca->arquivo_original
        );
    }

    public function search(Request $request)
    {
        $resultados = $this->searchService->buscar(
            $request->query('q'),
            $request->only(['categoria', 'data_inicio', 'data_fim', 'autor'])
        );

        return view('biblioteca.search', compact('resultados'));
    }

    private function podeAcessar(BibliotecaItem $item)
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) return true;
        
        if ($item->classificacao === 'publico') return true;
        
        if ($item->classificacao === 'restrito') {
            return $user->hasAnyRole(['BIBLIOTECARIO', 'LEGISLATIVO', 'PROTOCOLO']);
        }
        
        return false; // confidencial
    }

    private function registrarAcesso(BibliotecaItem $item, $tipo)
    {
        $item->acessos()->create([
            'user_id' => auth()->id(),
            'tipo_acesso' => $tipo,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
```

### **Passo 4: Criar as Views**

**resources/views/biblioteca/index.blade.php:**
```php
@extends('components.layouts.app')

@section('title', 'Biblioteca Digital - Sistema Parlamentar')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    📚 Biblioteca Digital
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Biblioteca</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                @can('create', App\Models\BibliotecaItem::class)
                <a href="{{ route('biblioteca.create') }}" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-plus fs-2"></i>
                    Adicionar Documento
                </a>
                @endcan
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Search & Filters-->
            <div class="card card-flush mb-5">
                <div class="card-header">
                    <h3 class="card-title">🔍 Buscar Documentos</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('biblioteca.index') }}">
                        <div class="row g-5">
                            <div class="col-md-4">
                                <label class="form-label">Buscar por palavra-chave</label>
                                <input type="text" name="search" class="form-control" 
                                       value="{{ request('search') }}" 
                                       placeholder="Título, descrição, autor...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Categoria</label>
                                <select name="categoria_id" class="form-select">
                                    <option value="">Todas as categorias</option>
                                    @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" 
                                            {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nome }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Todos os status</option>
                                    <option value="publico" {{ request('status') == 'publico' ? 'selected' : '' }}>
                                        Público
                                    </option>
                                    <option value="restrito" {{ request('status') == 'restrito' ? 'selected' : '' }}>
                                        Restrito
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="ki-duotone ki-magnifier fs-5"></i>
                                    Buscar
                                </button>
                                <a href="{{ route('biblioteca.index') }}" class="btn btn-light">
                                    Limpar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--end::Search & Filters-->

            <!--begin::Items Grid-->
            <div class="row g-5 g-xl-10">
                @forelse($items as $item)
                <div class="col-xl-4 col-lg-6 col-md-6">
                    @include('biblioteca.components.item-card', ['item' => $item])
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-10">
                        <i class="ki-duotone ki-file-deleted fs-4x text-muted mb-5">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <h3 class="text-muted">Nenhum documento encontrado</h3>
                        <p class="text-muted">Tente ajustar os filtros de busca ou adicionar novos documentos.</p>
                    </div>
                </div>
                @endforelse
            </div>
            <!--end::Items Grid-->

            <!--begin::Pagination-->
            @if($items->hasPages())
            <div class="d-flex justify-content-center mt-10">
                {{ $items->links() }}
            </div>
            @endif
            <!--end::Pagination-->

        </div>
    </div>
    <!--end::Content-->
</div>
@endsection
```

**resources/views/biblioteca/components/item-card.blade.php:**
```php
<!--begin::Item Card-->
<div class="card h-100 cursor-pointer" onclick="window.location='{{ route('biblioteca.show', $item) }}'">
    <div class="card-body d-flex flex-column">
        <!--begin::Header-->
        <div class="d-flex align-items-center mb-3">
            <div class="symbol symbol-50px me-3">
                <div class="symbol-label bg-light-{{ $item->categoria->cor ?? 'primary' }}">
                    <i class="ki-duotone {{ $item->categoria->icone ?? 'ki-file' }} fs-2 text-{{ $item->categoria->cor ?? 'primary' }}">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start">
                    <h5 class="mb-1 text-gray-900 text-hover-primary fw-bold">
                        {{ Str::limit($item->titulo, 60) }}
                    </h5>
                    <span class="badge badge-light-{{ $item->classificacao === 'publico' ? 'success' : 'warning' }}">
                        {{ ucfirst($item->classificacao) }}
                    </span>
                </div>
                <span class="text-muted fs-7">{{ $item->categoria->nome }}</span>
            </div>
        </div>
        <!--end::Header-->

        <!--begin::Description-->
        <p class="text-gray-600 fs-6 mb-3 flex-grow-1">
            {{ Str::limit($item->descricao, 120) }}
        </p>
        <!--end::Description-->

        <!--begin::Meta Info-->
        <div class="d-flex align-items-center justify-content-between text-muted fs-7 mb-3">
            <div class="d-flex align-items-center">
                <i class="ki-duotone ki-calendar fs-6 me-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                {{ $item->data_documento->format('d/m/Y') }}
            </div>
            <div class="d-flex align-items-center">
                <i class="ki-duotone ki-file fs-6 me-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                {{ $item->tamanho_formatado }}
            </div>
        </div>
        <!--end::Meta Info-->

        <!--begin::Author & Stats-->
        <div class="d-flex align-items-center justify-content-between border-top pt-3">
            <div class="d-flex align-items-center">
                @if($item->autor)
                <div class="symbol symbol-30px me-2">
                    <div class="symbol-label bg-light-info">
                        <span class="text-info fw-bold fs-7">
                            {{ substr($item->autor, 0, 2) }}
                        </span>
                    </div>
                </div>
                <span class="text-muted fs-7">{{ $item->autor }}</span>
                @endif
            </div>
            <div class="d-flex align-items-center text-muted fs-8">
                <i class="ki-duotone ki-eye fs-7 me-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                {{ $item->visualizacoes }}
                <i class="ki-duotone ki-arrows-circle fs-7 ms-3 me-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                {{ $item->downloads }}
            </div>
        </div>
        <!--end::Author & Stats-->
    </div>
</div>
<!--end::Item Card-->
```

### **Passo 5: Configurar Rotas**

**routes/web.php:**
```php
// Biblioteca Digital Routes
Route::middleware(['auth'])->prefix('biblioteca')->name('biblioteca.')->group(function () {
    Route::get('/', [BibliotecaController::class, 'index'])->name('index');
    Route::get('/search', [BibliotecaController::class, 'search'])->name('search');
    Route::get('/create', [BibliotecaController::class, 'create'])->name('create');
    Route::post('/', [BibliotecaController::class, 'store'])->name('store');
    Route::get('/{biblioteca}', [BibliotecaController::class, 'show'])->name('show');
    Route::get('/{biblioteca}/edit', [BibliotecaController::class, 'edit'])->name('edit');
    Route::put('/{biblioteca}', [BibliotecaController::class, 'update'])->name('update');
    Route::delete('/{biblioteca}', [BibliotecaController::class, 'destroy'])->name('destroy');
    Route::get('/{biblioteca}/download', [BibliotecaController::class, 'download'])->name('download');
});

// API Routes para busca avançada
Route::prefix('api/biblioteca')->middleware('auth')->group(function () {
    Route::get('/search', [BibliotecaApiController::class, 'search']);
    Route::get('/categorias', [BibliotecaApiController::class, 'categorias']);
    Route::post('/upload', [BibliotecaApiController::class, 'upload']);
});

// Rotas Admin
Route::middleware(['auth', 'role:ADMIN|BIBLIOTECARIO'])->prefix('admin/biblioteca')->name('admin.biblioteca.')->group(function () {
    Route::get('/dashboard', [BibliotecaAdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('categorias', BibliotecaCategoriaController::class);
    Route::get('/digitalizacao', [BibliotecaAdminController::class, 'digitalizacao'])->name('digitalizacao');
    Route::post('/processar-lote', [BibliotecaAdminController::class, 'processarLote'])->name('processar-lote');
});
```

### **Passo 6: Adicionar ao Menu**

**resources/views/components/layouts/aside.blade.php:**
```php
{{-- Biblioteca Digital --}}
@if(\App\Models\ScreenPermission::userCanAccessModule('biblioteca'))
<div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('biblioteca.*') ? 'here show' : '' }}">
    <span class="menu-link">
        <span class="menu-icon">
            <i class="ki-duotone ki-book fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
                <span class="path4"></span>
            </i>
        </span>
        <span class="menu-title">Biblioteca Digital</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion {{ request()->routeIs('biblioteca.*') ? 'show' : '' }}">
        @if(\App\Models\ScreenPermission::userCanAccessRoute('biblioteca.index'))
        <div class="menu-item">
            <a class="menu-link {{ request()->routeIs('biblioteca.index') ? 'active' : '' }}" href="{{ route('biblioteca.index') }}">
                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                <span class="menu-title">Acervo Digital</span>
            </a>
        </div>
        @endif
        
        @if(\App\Models\ScreenPermission::userCanAccessRoute('biblioteca.search'))
        <div class="menu-item">
            <a class="menu-link {{ request()->routeIs('biblioteca.search') ? 'active' : '' }}" href="{{ route('biblioteca.search') }}">
                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                <span class="menu-title">Busca Avançada</span>
            </a>
        </div>
        @endif
        
        @can('create', App\Models\BibliotecaItem::class)
        <div class="menu-item">
            <a class="menu-link {{ request()->routeIs('biblioteca.create') ? 'active' : '' }}" href="{{ route('biblioteca.create') }}">
                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                <span class="menu-title">Adicionar Documento</span>
            </a>
        </div>
        @endcan
        
        @if(auth()->user()->hasRole(['ADMIN', 'BIBLIOTECARIO']))
        <div class="menu-item">
            <a class="menu-link {{ request()->routeIs('admin.biblioteca.*') ? 'active' : '' }}" href="{{ route('admin.biblioteca.dashboard') }}">
                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                <span class="menu-title">Administração</span>
            </a>
        </div>
        @endif
    </div>
</div>
@endif
```

---

## 🔧 Services Especializados

### **BibliotecaSearchService.php:**
```php
<?php

namespace App\Services;

use App\Models\BibliotecaItem;
use Illuminate\Pagination\LengthAwarePaginator;

class BibliotecaSearchService
{
    public function buscar($termo, $filtros = [])
    {
        $query = BibliotecaItem::search($termo)
                              ->query(function ($builder) use ($filtros) {
                                  return $builder->acessivelPara(auth()->user());
                              });

        // Aplicar filtros
        if (!empty($filtros['categoria'])) {
            $query->where('categoria_id', $filtros['categoria']);
        }

        if (!empty($filtros['data_inicio'])) {
            $query->where('data_documento', '>=', $filtros['data_inicio']);
        }

        if (!empty($filtros['data_fim'])) {
            $query->where('data_documento', '<=', $filtros['data_fim']);
        }

        if (!empty($filtros['autor'])) {
            $query->where('autor', 'like', "%{$filtros['autor']}%");
        }

        return $query->paginate(15);
    }

    public function buscarPorConteudo($arquivo_path, $termo)
    {
        // Implementar OCR/extração de texto para busca no conteúdo
        // Integração com Apache Tika ou similar
    }
}
```

---

## 📱 Componentes Vue.js

### **BibliotecaSearch.vue:**
```vue
<template>
    <div class="biblioteca-search">
        <!-- Search form com filtros avançados -->
        <div class="card mb-5">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input 
                            v-model="searchTerm" 
                            @input="debouncedSearch"
                            type="text" 
                            class="form-control"
                            placeholder="Buscar por título, descrição, autor...">
                    </div>
                    <div class="col-md-3">
                        <select v-model="filters.categoria" @change="search" class="form-select">
                            <option value="">Todas as categorias</option>
                            <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
                                {{ cat.nome }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <input 
                                v-model="filters.data_inicio" 
                                @change="search"
                                type="date" 
                                class="form-control">
                            <input 
                                v-model="filters.data_fim" 
                                @change="search"
                                type="date" 
                                class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results -->
        <div class="row g-5">
            <div 
                v-for="item in results.data" 
                :key="item.id" 
                class="col-xl-4 col-lg-6">
                <biblioteca-item-card :item="item" />
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-10" v-if="results.total > 0">
            <nav>
                <ul class="pagination">
                    <li class="page-item" :class="{ disabled: !results.prev_page_url }">
                        <a class="page-link" @click="changePage(results.current_page - 1)">
                            Anterior
                        </a>
                    </li>
                    <li v-for="page in visiblePages" :key="page"
                        class="page-item" 
                        :class="{ active: page === results.current_page }">
                        <a class="page-link" @click="changePage(page)">
                            {{ page }}
                        </a>
                    </li>
                    <li class="page-item" :class="{ disabled: !results.next_page_url }">
                        <a class="page-link" @click="changePage(results.current_page + 1)">
                            Próximo
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>

<script>
export default {
    name: 'BibliotecaSearch',
    data() {
        return {
            searchTerm: '',
            filters: {
                categoria: '',
                data_inicio: '',
                data_fim: '',
                autor: ''
            },
            results: {
                data: [],
                current_page: 1,
                last_page: 1,
                total: 0
            },
            categorias: [],
            loading: false,
            searchTimeout: null
        }
    },
    
    computed: {
        visiblePages() {
            const current = this.results.current_page;
            const last = this.results.last_page;
            const pages = [];
            
            let start = Math.max(1, current - 2);
            let end = Math.min(last, current + 2);
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            
            return pages;
        }
    },
    
    mounted() {
        this.loadCategorias();
        this.search();
    },
    
    methods: {
        async loadCategorias() {
            try {
                const response = await fetch('/api/biblioteca/categorias');
                this.categorias = await response.json();
            } catch (error) {
                console.error('Erro ao carregar categorias:', error);
            }
        },
        
        debouncedSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.search();
            }, 300);
        },
        
        async search(page = 1) {
            this.loading = true;
            
            try {
                const params = new URLSearchParams({
                    q: this.searchTerm,
                    page: page,
                    ...this.filters
                });
                
                const response = await fetch(`/api/biblioteca/search?${params}`);
                this.results = await response.json();
            } catch (error) {
                console.error('Erro na busca:', error);
            } finally {
                this.loading = false;
            }
        },
        
        changePage(page) {
            if (page >= 1 && page <= this.results.last_page) {
                this.search(page);
            }
        }
    }
}
</script>
```

---

## 📊 Dashboard Administrativo

### **resources/views/admin/biblioteca/dashboard.blade.php:**
```php
@extends('components.layouts.app')

@section('title', 'Biblioteca Digital - Administração')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Stats Row-->
            <div class="row g-5 g-xl-10 mb-xl-10">
                <!--begin::Col-->
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="card card-md-stretch mb-xl-8">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-4">
                                    <div class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-book fs-2 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="text-gray-900 fw-bold fs-6 mb-1">Total de Documentos</div>
                                    <div class="fw-semibold text-muted fs-7">{{ $stats['total_documentos'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col-->
                
                <!-- Mais stats cards... -->
            </div>
            <!--end::Stats Row-->
            
            <!--begin::Charts Row-->
            <div class="row g-5 g-xl-10">
                <div class="col-xl-6">
                    <!--begin::Chart-->
                    <div class="card card-xl-stretch mb-xl-8">
                        <div class="card-header">
                            <h3 class="card-title">📈 Documentos por Categoria</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="documentosPorCategoria"></canvas>
                        </div>
                    </div>
                    <!--end::Chart-->
                </div>
                
                <div class="col-xl-6">
                    <!--begin::Recent Activity-->
                    <div class="card card-xl-stretch mb-xl-8">
                        <div class="card-header">
                            <h3 class="card-title">📋 Atividade Recente</h3>
                        </div>
                        <div class="card-body">
                            <!-- Lista de atividades recentes -->
                        </div>
                    </div>
                    <!--end::Recent Activity-->
                </div>
            </div>
            <!--end::Charts Row-->
            
        </div>
    </div>
</div>
@endsection
```

---

## 🔐 Políticas de Acesso (Policies)

### **app/Policies/BibliotecaItemPolicy.php:**
```php
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BibliotecaItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class BibliotecaItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Todos podem ver a lista (filtrada por permissões)
    }

    public function view(User $user, BibliotecaItem $item)
    {
        if ($user->isAdmin()) return true;
        
        if ($item->classificacao === 'publico') return true;
        
        if ($item->classificacao === 'restrito') {
            return $user->hasAnyRole(['BIBLIOTECARIO', 'LEGISLATIVO', 'PROTOCOLO']);
        }
        
        return false; // confidencial
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['ADMIN', 'BIBLIOTECARIO']);
    }

    public function update(User $user, BibliotecaItem $item)
    {
        if ($user->isAdmin()) return true;
        
        return $user->hasRole('BIBLIOTECARIO') || $item->uploaded_by === $user->id;
    }

    public function delete(User $user, BibliotecaItem $item)
    {
        return $user->isAdmin() || 
               ($user->hasRole('BIBLIOTECARIO') && $item->uploaded_by === $user->id);
    }

    public function download(User $user, BibliotecaItem $item)
    {
        return $this->view($user, $item);
    }
}
```

---

## 🚀 Jobs e Queues

### **app/Jobs/ProcessarMetadados.php:**
```php
<?php

namespace App\Jobs;

use App\Models\BibliotecaItem;
use App\Services\BibliotecaMetadataService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessarMetadados implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $item;

    public function __construct(BibliotecaItem $item)
    {
        $this->item = $item;
    }

    public function handle(BibliotecaMetadataService $metadataService)
    {
        // Extrair metadados do arquivo
        $metadados = $metadataService->extrairMetadados($this->item->arquivo_path);
        
        // Aplicar OCR se necessário
        if ($metadataService->precisaOCR($this->item->mime_type)) {
            $textoExtraido = $metadataService->aplicarOCR($this->item->arquivo_path);
            $metadados['texto_extraido'] = $textoExtraido;
        }
        
        // Salvar metadados
        foreach ($metadados as $chave => $valor) {
            $this->item->metadados()->updateOrCreate(
                ['chave' => $chave],
                ['valor' => $valor]
            );
        }
        
        // Atualizar status
        $this->item->update([
            'status' => 'indexado',
            'indexado_em' => now()
        ]);
    }
}
```

---

## 📋 Checklist de Implementação

### **Fase 1 - Estrutura Base** ✅
- [ ] Criar migrations e executar
- [ ] Implementar models com relacionamentos
- [ ] Configurar policies de acesso
- [ ] Criar controllers básicos
- [ ] Implementar rotas

### **Fase 2 - Interface Web** 
- [ ] Criar views do acervo (index, show, create, edit)
- [ ] Implementar componentes Blade reutilizáveis
- [ ] Adicionar formulários com validação
- [ ] Configurar upload de arquivos
- [ ] Integrar com menu de navegação

### **Fase 3 - Busca e Filtros**
- [ ] Implementar busca básica por texto
- [ ] Criar filtros por categoria e data
- [ ] Desenvolver busca avançada
- [ ] Adicionar componente Vue de busca
- [ ] Configurar indexação (Laravel Scout)

### **Fase 4 - Administração**
- [ ] Criar dashboard administrativo
- [ ] Implementar gestão de categorias
- [ ] Desenvolver painel de digitalização
- [ ] Adicionar relatórios e estatísticas
- [ ] Configurar jobs de processamento

### **Fase 5 - Funcionalidades Avançadas**
- [ ] Sistema de metadados automáticos
- [ ] OCR para documentos escaneados
- [ ] Integração com OnlyOffice
- [ ] API REST completa
- [ ] Sistema de preservação digital

### **Fase 6 - Testes e Otimização**
- [ ] Testes unitários dos models
- [ ] Testes de integração dos controllers
- [ ] Testes de políticas de acesso
- [ ] Otimização de performance
- [ ] Cache de consultas frequentes

---

## 🎯 Considerações Finais

Este guia fornece uma estrutura completa para implementar o módulo **Biblioteca Digital** seguindo as melhores práticas do Laravel e as convenções do sistema Legisinc. 

### **Próximos Passos**:
1. Implementar a estrutura base (migrations, models, controllers)
2. Desenvolver a interface web básica
3. Adicionar funcionalidades de busca
4. Implementar o painel administrativo
5. Integrar funcionalidades avançadas (OCR, metadados automáticos)
6. Realizar testes e otimizações

### **Integração com Sistema Existente**:
- **Permissões**: Usar o sistema `ScreenPermission` existente
- **Layout**: Seguir o padrão `components.layouts.app`
- **Estilo**: Manter consistência com Keen Theme
- **Vue.js**: Integrar com a estrutura Vue existente
- **OnlyOffice**: Reutilizar visualizador de documentos

Este módulo será um diferencial importante para o sistema parlamentar, centralizando todo o acervo digital e facilitando o acesso à informação legislativa! 📚✨