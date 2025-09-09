@extends('components.layouts.app')

@section('title', 'Guia: Módulo Biblioteca Digital - Sistema Parlamentar')

@push('styles')
<style>
    .code-block {
        background: #1e1e2e;
        color: #e1e1e1;
        border-radius: 8px;
        padding: 20px;
        margin: 15px 0;
        font-family: 'Consolas', 'Monaco', monospace;
        font-size: 14px;
        position: relative;
        overflow-x: auto;
    }
    
    .code-block .btn {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    
    .code-block pre {
        margin: 0;
        white-space: pre-wrap;
    }
    
    
    .step-indicator {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 20px;
        flex-shrink: 0;
    }
    
    .file-structure {
        background: #f8f9fa;
        border-left: 4px solid #009ef7;
        padding: 20px;
        font-family: monospace;
        line-height: 1.8;
    }
    
    .permission-table {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .feature-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
        margin: 5px;
        display: inline-block;
    }
</style>
@endpush

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    📚 Guia: Módulo Biblioteca Digital
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Admin</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.guia-desenvolvimento.index') }}" class="text-muted text-hover-primary">Guia de Desenvolvimento</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Biblioteca Digital</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('admin.guia-desenvolvimento.index') }}" class="btn btn-sm fw-bold btn-secondary">
                    <i class="ki-duotone ki-arrow-left fs-2"></i>
                    Voltar ao Guia
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Overview-->
            <div class="card mb-10">
                <div class="card-header">
                    <h3 class="card-title">📋 Visão Geral do Módulo</h3>
                    <div class="card-toolbar">
                        <span class="badge badge-light-success fs-7">Caso Prático Completo</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-5">
                        <div class="col-xl-8">
                            <div class="mb-5">
                                <h4>🎯 Objetivo</h4>
                                <p class="text-gray-600">
                                    Sistema completo de gestão de acervo digital parlamentar com funcionalidades de busca avançada, 
                                    digitalização automatizada, controle de metadados e preservação digital.
                                </p>
                            </div>
                            
                            <div class="mb-5">
                                <h4>🏷️ Características</h4>
                                <div class="d-flex flex-wrap">
                                    <span class="feature-badge bg-light-primary text-primary">CRUD Completo</span>
                                    <span class="feature-badge bg-light-success text-success">Vue.js</span>
                                    <span class="feature-badge bg-light-info text-info">API REST</span>
                                    <span class="feature-badge bg-light-warning text-warning">Upload Avançado</span>
                                    <span class="feature-badge bg-light-danger text-danger">OCR</span>
                                    <span class="feature-badge bg-light-dark text-dark">Laravel Scout</span>
                                </div>
                            </div>
                            
                            <div>
                                <h4>📊 Complexidade</h4>
                                <div class="progress progress-sm mb-3">
                                    <div class="progress-bar bg-danger" style="width: 90%"></div>
                                </div>
                                <p class="text-muted fs-7">
                                    <strong>Alta:</strong> 15+ arquivos, múltiplos services, jobs de background, integração com ferramentas externas
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-xl-4">
                            <div class="card bg-light-primary">
                                <div class="card-body text-center">
                                    <i class="ki-duotone ki-book text-primary fs-3x mb-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                    <h3 class="text-primary">Biblioteca Digital</h3>
                                    <p class="text-primary opacity-75">
                                        Módulo de gestão de acervo digital com recursos avançados
                                    </p>
                                    <div class="text-primary fw-bold">
                                        <div>📁 15+ Arquivos</div>
                                        <div>⚡ 6 Fases de Implementação</div>
                                        <div>🔐 6 Níveis de Permissão</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Overview-->

            <!--begin::Permissions-->
            <div class="card mb-10">
                <div class="card-header">
                    <h3 class="card-title">🔐 Sistema de Permissões</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 gy-7">
                            <thead>
                                <tr class="fw-bold fs-6 text-gray-800">
                                    <th>Perfil</th>
                                    <th>Visualizar</th>
                                    <th>Criar</th>
                                    <th>Editar</th>
                                    <th>Excluir</th>
                                    <th>Admin</th>
                                    <th>Descrição</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge badge-light-danger">ADMIN</span></td>
                                    <td><i class="ki-duotone ki-check text-success fs-1"></i></td>
                                    <td><i class="ki-duotone ki-check text-success fs-1"></i></td>
                                    <td><i class="ki-duotone ki-check text-success fs-1"></i></td>
                                    <td><i class="ki-duotone ki-check text-success fs-1"></i></td>
                                    <td><i class="ki-duotone ki-check text-success fs-1"></i></td>
                                    <td>Acesso total, configurações, relatórios</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-light-primary">BIBLIOTECARIO</span></td>
                                    <td><i class="ki-duotone ki-check text-success fs-1"></i></td>
                                    <td><i class="ki-duotone ki-check text-success fs-1"></i></td>
                                    <td><i class="ki-duotone ki-check text-success fs-1"></i></td>
                                    <td><i class="ki-duotone ki-check text-success fs-1"></i></td>
                                    <td><i class="ki-duotone ki-cross text-danger fs-1"></i></td>
                                    <td>CRUD completo, digitalização, metadados</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-light-info">LEGISLATIVO</span></td>
                                    <td><i class="ki-duotone ki-check text-success fs-1"></i></td>
                                    <td><i class="ki-duotone ki-cross text-danger fs-1"></i></td>
                                    <td><i class="ki-duotone ki-cross text-danger fs-1"></i></td>
                                    <td><i class="ki-duotone ki-cross text-danger fs-1"></i></td>
                                    <td><i class="ki-duotone ki-cross text-danger fs-1"></i></td>
                                    <td>Visualizar, buscar, baixar documentos legislativos</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-light-warning">PARLAMENTAR</span></td>
                                    <td><i class="ki-duotone ki-check text-success fs-1"></i></td>
                                    <td><i class="ki-duotone ki-cross text-danger fs-1"></i></td>
                                    <td><i class="ki-duotone ki-cross text-danger fs-1"></i></td>
                                    <td><i class="ki-duotone ki-cross text-danger fs-1"></i></td>
                                    <td><i class="ki-duotone ki-cross text-danger fs-1"></i></td>
                                    <td>Visualizar, buscar, acessar documentos públicos</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-light-success">PROTOCOLO</span></td>
                                    <td><i class="ki-duotone ki-check text-success fs-1"></i></td>
                                    <td><i class="ki-duotone ki-cross text-danger fs-1"></i></td>
                                    <td><i class="ki-duotone ki-cross text-danger fs-1"></i></td>
                                    <td><i class="ki-duotone ki-cross text-danger fs-1"></i></td>
                                    <td><i class="ki-duotone ki-cross text-danger fs-1"></i></td>
                                    <td>Visualizar, buscar documentos protocolados</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-light-secondary">PÚBLICO</span></td>
                                    <td><i class="ki-duotone ki-check text-success fs-1"></i></td>
                                    <td><i class="ki-duotone ki-cross text-danger fs-1"></i></td>
                                    <td><i class="ki-duotone ki-cross text-danger fs-1"></i></td>
                                    <td><i class="ki-duotone ki-cross text-danger fs-1"></i></td>
                                    <td><i class="ki-duotone ki-cross text-danger fs-1"></i></td>
                                    <td>Apenas documentos com acesso público</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--end::Permissions-->

            <!--begin::File Structure-->
            <div class="card mb-10">
                <div class="card-header">
                    <h3 class="card-title">🏗️ Estrutura de Arquivos</h3>
                </div>
                <div class="card-body">
                    <div class="file-structure">
<pre>📁 <strong class="text-primary">app/</strong>
├── Http/Controllers/
│   ├── <span class="text-success">BibliotecaController.php</span>              # Controller principal
│   ├── <span class="text-success">Api/BibliotecaApiController.php</span>       # API REST
│   └── <span class="text-success">Admin/BibliotecaAdminController.php</span>   # Administração
├── Models/
│   ├── <span class="text-warning">BibliotecaItem.php</span>                    # Item do acervo
│   ├── <span class="text-warning">BibliotecaCategoria.php</span>               # Categorias
│   ├── <span class="text-warning">BibliotecaMetadata.php</span>                # Metadados
│   └── <span class="text-warning">BibliotecaAcesso.php</span>                  # Log de acessos
├── Services/
│   ├── <span class="text-info">BibliotecaDigitalizacaoService.php</span>       # Digitalização
│   ├── <span class="text-info">BibliotecaSearchService.php</span>              # Busca avançada
│   └── <span class="text-info">BibliotecaMetadataService.php</span>            # Processamento de metadados
└── Http/Requests/
    ├── BibliotecaStoreRequest.php
    └── BibliotecaUpdateRequest.php

📁 <strong class="text-primary">resources/</strong>
└── views/
    └── biblioteca/
        ├── <span class="text-success">index.blade.php</span>                   # Lista do acervo
        ├── <span class="text-success">show.blade.php</span>                    # Visualizar item
        ├── <span class="text-success">create.blade.php</span>                  # Adicionar item
        ├── <span class="text-success">edit.blade.php</span>                    # Editar item
        ├── <span class="text-success">search.blade.php</span>                  # Busca avançada
        ├── admin/
        │   ├── <span class="text-warning">dashboard.blade.php</span>           # Dashboard admin
        │   ├── <span class="text-warning">categorias.blade.php</span>          # Gestão de categorias
        │   ├── <span class="text-warning">metadados.blade.php</span>           # Configuração de metadados
        │   └── <span class="text-warning">digitalizacao.blade.php</span>       # Painel de digitalização
        └── components/
            ├── item-card.blade.php           # Card do item
            ├── search-filters.blade.php      # Filtros de busca
            └── metadata-form.blade.php       # Formulário de metadados

📁 <strong class="text-primary">resources/js/components/</strong>
├── <span class="text-info">BibliotecaSearch.vue</span>                         # Busca interativa
├── <span class="text-info">BibliotecaViewer.vue</span>                         # Visualizador de documentos
├── <span class="text-info">BibliotecaUpload.vue</span>                         # Upload com drag & drop
└── <span class="text-info">BibliotecaMetadata.vue</span>                       # Editor de metadados

📁 <strong class="text-primary">database/migrations/</strong>
├── 2026_01_01_000001_create_biblioteca_categorias_table.php
├── 2026_01_01_000002_create_biblioteca_items_table.php
├── 2026_01_01_000003_create_biblioteca_metadata_table.php
└── 2026_01_01_000004_create_biblioteca_acessos_table.php</pre>
                    </div>
                </div>
            </div>
            <!--end::File Structure-->

            <!--begin::Implementation Steps-->
            <div class="card mb-10">
                <div class="card-header">
                    <h3 class="card-title">👨‍💻 Passos de Implementação</h3>
                    <div class="card-toolbar">
                        <span class="badge badge-light-info">6 Fases</span>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!--begin::Step 1-->
                    <div class="d-flex align-items-start mb-10">
                        <div class="step-indicator bg-primary text-white">1</div>
                        <div class="flex-grow-1">
                            <h4 class="mb-3">Criar as Migrations</h4>
                            <p class="text-gray-600 mb-4">
                                Defina a estrutura do banco de dados com tabelas otimizadas para performance
                            </p>
                            
                            <div class="code-block">
                                <button class="btn btn-sm btn-info" onclick="copyToClipboard('migration-code')">
                                    <i class="ki-duotone ki-copy fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Copiar Código
                                </button>
                                <pre id="migration-code"># Criar migrations na ordem correta
php artisan make:migration create_biblioteca_categorias_table
php artisan make:migration create_biblioteca_items_table  
php artisan make:migration create_biblioteca_metadata_table
php artisan make:migration create_biblioteca_acessos_table

# Exemplo - Migration de Itens
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
    
    // Relacionamentos e índices
    $table->foreign('categoria_id')->references('id')->on('biblioteca_categorias');
    $table->foreign('uploaded_by')->references('id')->on('users');
    $table->index(['status', 'classificacao']);
    $table->index(['categoria_id', 'status']);
    $table->fullText(['titulo', 'descricao', 'autor']);
});</pre>
                            </div>
                        </div>
                    </div>
                    <!--end::Step 1-->

                    <!--begin::Step 2-->
                    <div class="d-flex align-items-start mb-10">
                        <div class="step-indicator bg-success text-white">2</div>
                        <div class="flex-grow-1">
                            <h4 class="mb-3">Criar os Models</h4>
                            <p class="text-gray-600 mb-4">
                                Implemente models com relacionamentos, scopes e integração com Laravel Scout para busca
                            </p>
                            
                            <div class="code-block">
                                <button class="btn btn-sm btn-info" onclick="copyToClipboard('model-code')">
                                    <i class="ki-duotone ki-copy fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Copiar Código
                                </button>
                                <pre id="model-code"># Criar model principal
php artisan make:model BibliotecaItem

# Exemplo - BibliotecaItem.php
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

    // Scopes para controle de acesso
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

    // Accessor para tamanho formatado
    public function getTamanhoFormatadoAttribute()
    {
        return $this->formatBytes($this->tamanho_bytes);
    }

    // Scout/Search - Índice de busca
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
}</pre>
                            </div>
                        </div>
                    </div>
                    <!--end::Step 2-->

                    <!--begin::Step 3-->
                    <div class="d-flex align-items-start mb-10">
                        <div class="step-indicator bg-info text-white">3</div>
                        <div class="flex-grow-1">
                            <h4 class="mb-3">Criar Controllers</h4>
                            <p class="text-gray-600 mb-4">
                                Desenvolva controllers com validação, controle de acesso e integração com services
                            </p>
                            
                            <div class="code-block">
                                <button class="btn btn-sm btn-info" onclick="copyToClipboard('controller-code')">
                                    <i class="ki-duotone ki-copy fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Copiar Código
                                </button>
                                <pre id="controller-code"># Criar controller principal
php artisan make:controller BibliotecaController --resource

# Exemplo - BibliotecaController.php
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
        
        // Aplicar filtros
        if ($request->categoria_id) {
            $query->where('categoria_id', $request->categoria_id);
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
}</pre>
                            </div>
                        </div>
                    </div>
                    <!--end::Step 3-->

                    <!--begin::Step 4-->
                    <div class="d-flex align-items-start mb-10">
                        <div class="step-indicator bg-warning text-white">4</div>
                        <div class="flex-grow-1">
                            <h4 class="mb-3">Criar Views Blade</h4>
                            <p class="text-gray-600 mb-4">
                                Desenvolva interface responsiva seguindo o padrão Keen Theme do sistema
                            </p>
                            
                            <div class="code-block">
                                <button class="btn btn-sm btn-info" onclick="copyToClipboard('view-code')">
                                    <i class="ki-duotone ki-copy fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Copiar Código
                                </button>
                                <pre id="view-code"># Estrutura de views
mkdir -p resources/views/biblioteca/{admin,components}

# Exemplo - biblioteca/index.blade.php
@@extends('components.layouts.app')
@@section('title', 'Biblioteca Digital - Sistema Parlamentar')

@@section('content')
&lt;div class="d-flex flex-column flex-column-fluid"&gt;
    &lt;!--begin::Toolbar--&gt;
    &lt;div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6"&gt;
        &lt;div class="app-container container-xxl d-flex flex-stack"&gt;
            &lt;div class="page-title d-flex flex-column justify-content-center flex-wrap me-3"&gt;
                &lt;h1 class="page-heading d-flex text-dark fw-bold fs-3"&gt;
                    📚 Biblioteca Digital
                &lt;/h1&gt;
                &lt;ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1"&gt;
                    &lt;li class="breadcrumb-item text-muted"&gt;
                        &lt;a href="@{{ route('dashboard') }}"&gt;Dashboard&lt;/a&gt;
                    &lt;/li&gt;
                    &lt;li class="breadcrumb-item text-muted"&gt;Biblioteca&lt;/li&gt;
                &lt;/ul&gt;
            &lt;/div&gt;
            &lt;div class="d-flex align-items-center gap-2"&gt;
                @@can('create', App\Models\BibliotecaItem::class)
                &lt;a href="@{{ route('biblioteca.create') }}" class="btn btn-sm fw-bold btn-primary"&gt;
                    &lt;i class="ki-duotone ki-plus fs-2"&gt;&lt;/i&gt;
                    Adicionar Documento
                &lt;/a&gt;
                @@endcan
            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/div&gt;

    &lt;!--begin::Content--&gt;
    &lt;div id="kt_app_content" class="app-content flex-column-fluid"&gt;
        &lt;div class="app-container container-xxl"&gt;
            
            &lt;!--begin::Search Filters--&gt;
            &lt;div class="card card-flush mb-5"&gt;
                &lt;div class="card-header"&gt;
                    &lt;h3 class="card-title"&gt;🔍 Buscar Documentos&lt;/h3&gt;
                &lt;/div&gt;
                &lt;div class="card-body"&gt;
                    &lt;form method="GET" action="@{{ route('biblioteca.index') }}"&gt;
                        &lt;div class="row g-5"&gt;
                            &lt;div class="col-md-4"&gt;
                                &lt;label class="form-label"&gt;Buscar por palavra-chave&lt;/label&gt;
                                &lt;input type="text" name="search" class="form-control" 
                                       value="@{{ request('search') }}" 
                                       placeholder="Título, descrição, autor..."&gt;
                            &lt;/div&gt;
                            &lt;div class="col-md-3"&gt;
                                &lt;label class="form-label"&gt;Categoria&lt;/label&gt;
                                &lt;select name="categoria_id" class="form-select"&gt;
                                    &lt;option value=""&gt;Todas as categorias&lt;/option&gt;
                                    @@foreach($categorias as $categoria)
                                    &lt;option value="@{{ $categoria->id }}" 
                                            @{{ request('categoria_id') == $categoria->id ? 'selected' : '' }}&gt;
                                        @{{ $categoria->nome }}
                                    &lt;/option&gt;
                                    @@endforeach
                                &lt;/select&gt;
                            &lt;/div&gt;
                            &lt;div class="col-md-3 d-flex align-items-end"&gt;
                                &lt;button type="submit" class="btn btn-primary me-2"&gt;
                                    &lt;i class="ki-duotone ki-magnifier fs-5"&gt;&lt;/i&gt;
                                    Buscar
                                &lt;/button&gt;
                                &lt;a href="@{{ route('biblioteca.index') }}" class="btn btn-light"&gt;
                                    Limpar
                                &lt;/a&gt;
                            &lt;/div&gt;
                        &lt;/div&gt;
                    &lt;/form&gt;
                &lt;/div&gt;
            &lt;/div&gt;

            &lt;!--begin::Items Grid--&gt;
            &lt;div class="row g-5 g-xl-10"&gt;
                @@forelse($items as $item)
                &lt;div class="col-xl-4 col-lg-6 col-md-6"&gt;
                    @@include('biblioteca.components.item-card', ['item' =&gt; $item])
                &lt;/div&gt;
                @@empty
                &lt;div class="col-12"&gt;
                    &lt;div class="text-center py-10"&gt;
                        &lt;h3 class="text-muted"&gt;Nenhum documento encontrado&lt;/h3&gt;
                    &lt;/div&gt;
                &lt;/div&gt;
                @@endforelse
            &lt;/div&gt;

            &lt;!--begin::Pagination--&gt;
            @@if($items-&gt;hasPages())
            &lt;div class="d-flex justify-content-center mt-10"&gt;
                @{{ $items->links() }}
            &lt;/div&gt;
            @@endif

        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;
@@endsection</pre>
                            </div>
                        </div>
                    </div>
                    <!--end::Step 4-->

                    <!--begin::Step 5-->
                    <div class="d-flex align-items-start mb-10">
                        <div class="step-indicator bg-danger text-white">5</div>
                        <div class="flex-grow-1">
                            <h4 class="mb-3">Configurar Rotas</h4>
                            <p class="text-gray-600 mb-4">
                                Defina rotas web e API com middleware de autenticação e autorização
                            </p>
                            
                            <div class="code-block">
                                <button class="btn btn-sm btn-info" onclick="copyToClipboard('routes-code')">
                                    <i class="ki-duotone ki-copy fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Copiar Código
                                </button>
                                <pre id="routes-code"># routes/web.php - Biblioteca Digital Routes
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

# routes/api.php - API Routes para busca avançada
Route::prefix('api/biblioteca')->middleware('auth')->group(function () {
    Route::get('/search', [BibliotecaApiController::class, 'search']);
    Route::get('/categorias', [BibliotecaApiController::class, 'categorias']);
    Route::post('/upload', [BibliotecaApiController::class, 'upload']);
});

# Rotas Admin
Route::middleware(['auth', 'role:ADMIN|BIBLIOTECARIO'])->prefix('admin/biblioteca')->name('admin.biblioteca.')->group(function () {
    Route::get('/dashboard', [BibliotecaAdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('categorias', BibliotecaCategoriaController::class);
    Route::get('/digitalizacao', [BibliotecaAdminController::class, 'digitalizacao'])->name('digitalizacao');
    Route::post('/processar-lote', [BibliotecaAdminController::class, 'processarLote'])->name('processar-lote');
});</pre>
                            </div>
                        </div>
                    </div>
                    <!--end::Step 5-->

                    <!--begin::Step 6-->
                    <div class="d-flex align-items-start mb-10">
                        <div class="step-indicator bg-dark text-white">6</div>
                        <div class="flex-grow-1">
                            <h4 class="mb-3">Componentes Vue.js</h4>
                            <p class="text-gray-600 mb-4">
                                Crie componentes interativos para busca avançada, upload de arquivos e visualização
                            </p>
                            
                            <div class="code-block">
                                <button class="btn btn-sm btn-info" onclick="copyToClipboard('vue-code')">
                                    <i class="ki-duotone ki-copy fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Copiar Código
                                </button>
                                <pre id="vue-code"># Criar componentes Vue
# resources/js/components/BibliotecaSearch.vue

&lt;template&gt;
    &lt;div class="biblioteca-search"&gt;
        &lt;!-- Search form com filtros avançados --&gt;
        &lt;div class="card mb-5"&gt;
            &lt;div class="card-body"&gt;
                &lt;div class="row g-3"&gt;
                    &lt;div class="col-md-6"&gt;
                        &lt;input 
                            v-model="searchTerm" 
                            @@input="debouncedSearch"
                            type="text" 
                            class="form-control"
                            placeholder="Buscar por título, descrição, autor..."&gt;
                    &lt;/div&gt;
                    &lt;div class="col-md-3"&gt;
                        &lt;select v-model="filters.categoria" @@change="search" class="form-select"&gt;
                            &lt;option value=""&gt;Todas as categorias&lt;/option&gt;
                            &lt;option v-for="cat in categorias" :key="cat.id" :value="cat.id"&gt;
                                @{{ cat.nome }}
                            &lt;/option&gt;
                        &lt;/select&gt;
                    &lt;/div&gt;
                    &lt;div class="col-md-3"&gt;
                        &lt;div class="d-flex gap-2"&gt;
                            &lt;input 
                                v-model="filters.data_inicio" 
                                @@change="search"
                                type="date" 
                                class="form-control"&gt;
                            &lt;input 
                                v-model="filters.data_fim" 
                                @@change="search"
                                type="date" 
                                class="form-control"&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/div&gt;

        &lt;!-- Results Grid --&gt;
        &lt;div class="row g-5"&gt;
            &lt;div 
                v-for="item in results.data" 
                :key="item.id" 
                class="col-xl-4 col-lg-6"&gt;
                &lt;biblioteca-item-card :item="item" /&gt;
            &lt;/div&gt;
        &lt;/div&gt;

        &lt;!-- Pagination --&gt;
        &lt;div class="d-flex justify-content-center mt-10" v-if="results.total &gt; 0"&gt;
            &lt;nav&gt;
                &lt;ul class="pagination"&gt;
                    &lt;li class="page-item" :class="{ disabled: !results.prev_page_url }"&gt;
                        &lt;a class="page-link" @@click="changePage(results.current_page - 1)"&gt;
                            Anterior
                        &lt;/a&gt;
                    &lt;/li&gt;
                    &lt;li v-for="page in visiblePages" :key="page"
                        class="page-item" 
                        :class="{ active: page === results.current_page }"&gt;
                        &lt;a class="page-link" @@click="changePage(page)"&gt;
                            @{{ page }}
                        &lt;/a&gt;
                    &lt;/li&gt;
                    &lt;li class="page-item" :class="{ disabled: !results.next_page_url }"&gt;
                        &lt;a class="page-link" @@click="changePage(results.current_page + 1)"&gt;
                            Próximo
                        &lt;/a&gt;
                    &lt;/li&gt;
                &lt;/ul&gt;
            &lt;/nav&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/template&gt;

&lt;script&gt;
export default {
    name: 'BibliotecaSearch',
    data() {
        return {
            searchTerm: '',
            filters: {
                categoria: '',
                data_inicio: '',
                data_fim: ''
            },
            results: { data: [], current_page: 1, last_page: 1, total: 0 },
            categorias: [],
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
            const response = await fetch('/api/biblioteca/categorias');
            this.categorias = await response.json();
        },
        
        debouncedSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() =&gt; {
                this.search();
            }, 300);
        },
        
        async search(page = 1) {
            const params = new URLSearchParams({
                q: this.searchTerm,
                page: page,
                ...this.filters
            });
            
            const response = await fetch(`/api/biblioteca/search?${params}`);
            this.results = await response.json();
        },
        
        changePage(page) {
            if (page &gt;= 1 && page &lt;= this.results.last_page) {
                this.search(page);
            }
        }
    }
}
&lt;/script&gt;</pre>
                            </div>
                        </div>
                    </div>
                    <!--end::Step 6-->

                </div>
            </div>
            <!--end::Implementation Steps-->

            <!--begin::Additional Features-->
            <div class="card mb-10">
                <div class="card-header">
                    <h3 class="card-title">⚡ Funcionalidades Avançadas</h3>
                </div>
                <div class="card-body">
                    <div class="row g-5">
                        <div class="col-xl-6">
                            <h4 class="mb-4">🔍 Sistema de Busca</h4>
                            <ul class="list-unstyled">
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check text-success fs-5 me-2"></i>
                                    Laravel Scout para indexação
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check text-success fs-5 me-2"></i>
                                    Busca full-text em título, descrição, autor
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check text-success fs-5 me-2"></i>
                                    Filtros por categoria, data, status
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check text-success fs-5 me-2"></i>
                                    Busca por conteúdo (OCR)
                                </li>
                            </ul>
                        </div>
                        
                        <div class="col-xl-6">
                            <h4 class="mb-4">🤖 Processamento Automático</h4>
                            <ul class="list-unstyled">
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check text-success fs-5 me-2"></i>
                                    Jobs para processamento em background
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check text-success fs-5 me-2"></i>
                                    Extração automática de metadados
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check text-success fs-5 me-2"></i>
                                    OCR para documentos escaneados
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check text-success fs-5 me-2"></i>
                                    Geração de checksums MD5
                                </li>
                            </ul>
                        </div>
                        
                        <div class="col-xl-6">
                            <h4 class="mb-4">📊 Dashboard Administrativo</h4>
                            <ul class="list-unstyled">
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check text-success fs-5 me-2"></i>
                                    Estatísticas de uso e acesso
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check text-success fs-5 me-2"></i>
                                    Gestão de categorias
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check text-success fs-5 me-2"></i>
                                    Monitoramento de digitalizações
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check text-success fs-5 me-2"></i>
                                    Relatórios de acesso
                                </li>
                            </ul>
                        </div>
                        
                        <div class="col-xl-6">
                            <h4 class="mb-4">🔒 Segurança</h4>
                            <ul class="list-unstyled">
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check text-success fs-5 me-2"></i>
                                    Policies para controle de acesso
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check text-success fs-5 me-2"></i>
                                    Log de acessos e downloads
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check text-success fs-5 me-2"></i>
                                    Classificação de documentos
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check text-success fs-5 me-2"></i>
                                    Validação de integridade
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Additional Features-->

            <!--begin::Implementation Checklist-->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">📋 Checklist de Implementação</h3>
                    <div class="card-toolbar">
                        <span class="badge badge-light-primary">26 Tarefas</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-5">
                        <div class="col-xl-6">
                            <h5 class="mb-4">Fase 1 - Estrutura Base ⚙️</h5>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task1">
                                <label class="form-check-label" for="task1">
                                    Criar migrations e executar
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task2">
                                <label class="form-check-label" for="task2">
                                    Implementar models com relacionamentos
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task3">
                                <label class="form-check-label" for="task3">
                                    Configurar policies de acesso
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task4">
                                <label class="form-check-label" for="task4">
                                    Criar controllers básicos
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task5">
                                <label class="form-check-label" for="task5">
                                    Implementar rotas
                                </label>
                            </div>
                            
                            <h5 class="mb-4 mt-6">Fase 2 - Interface Web 🎨</h5>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task6">
                                <label class="form-check-label" for="task6">
                                    Criar views do acervo (index, show, create, edit)
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task7">
                                <label class="form-check-label" for="task7">
                                    Implementar componentes Blade reutilizáveis
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task8">
                                <label class="form-check-label" for="task8">
                                    Adicionar formulários com validação
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task9">
                                <label class="form-check-label" for="task9">
                                    Configurar upload de arquivos
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task10">
                                <label class="form-check-label" for="task10">
                                    Integrar com menu de navegação
                                </label>
                            </div>
                            
                            <h5 class="mb-4 mt-6">Fase 3 - Busca e Filtros 🔍</h5>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task11">
                                <label class="form-check-label" for="task11">
                                    Implementar busca básica por texto
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task12">
                                <label class="form-check-label" for="task12">
                                    Criar filtros por categoria e data
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task13">
                                <label class="form-check-label" for="task13">
                                    Desenvolver busca avançada
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-xl-6">
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task14">
                                <label class="form-check-label" for="task14">
                                    Adicionar componente Vue de busca
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task15">
                                <label class="form-check-label" for="task15">
                                    Configurar indexação (Laravel Scout)
                                </label>
                            </div>
                            
                            <h5 class="mb-4 mt-6">Fase 4 - Administração 👨‍💼</h5>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task16">
                                <label class="form-check-label" for="task16">
                                    Criar dashboard administrativo
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task17">
                                <label class="form-check-label" for="task17">
                                    Implementar gestão de categorias
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task18">
                                <label class="form-check-label" for="task18">
                                    Desenvolver painel de digitalização
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task19">
                                <label class="form-check-label" for="task19">
                                    Adicionar relatórios e estatísticas
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task20">
                                <label class="form-check-label" for="task20">
                                    Configurar jobs de processamento
                                </label>
                            </div>
                            
                            <h5 class="mb-4 mt-6">Fase 5 - Funcionalidades Avançadas 🚀</h5>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task21">
                                <label class="form-check-label" for="task21">
                                    Sistema de metadados automáticos
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task22">
                                <label class="form-check-label" for="task22">
                                    OCR para documentos escaneados
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task23">
                                <label class="form-check-label" for="task23">
                                    Integração com OnlyOffice
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task24">
                                <label class="form-check-label" for="task24">
                                    API REST completa
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" id="task25">
                                <label class="form-check-label" for="task25">
                                    Sistema de preservação digital
                                </label>
                            </div>
                            
                            <h5 class="mb-4 mt-6">Fase 6 - Testes e Otimização 🧪</h5>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" id="task26">
                                <label class="form-check-label" for="task26">
                                    Testes completos e otimização de performance
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Implementation Checklist-->

        </div>
    </div>
    <!--end::Content-->
</div>

<script>
function copyToClipboard(elementId) {
    const code = document.getElementById(elementId).textContent;
    navigator.clipboard.writeText(code).then(() => {
        if (window.toastr) {
            toastr.success('Código copiado para a área de transferência!');
        } else {
            alert('Código copiado!');
        }
    }).catch(err => {
        console.error('Erro ao copiar:', err);
    });
}

// Progress tracking
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"][id^="task"]');
    const savedProgress = JSON.parse(localStorage.getItem('biblioteca_checklist') || '{}');
    
    // Restore progress
    checkboxes.forEach(checkbox => {
        if (savedProgress[checkbox.id]) {
            checkbox.checked = true;
        }
        
        // Save progress on change
        checkbox.addEventListener('change', function() {
            savedProgress[checkbox.id] = checkbox.checked;
            localStorage.setItem('biblioteca_checklist', JSON.stringify(savedProgress));
            
            // Show toast
            if (checkbox.checked && window.toastr) {
                toastr.success('Progresso salvo!');
            }
        });
    });
});
</script>
@endsection