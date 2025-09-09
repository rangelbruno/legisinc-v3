<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class GuiaDesenvolvimentoController extends Controller
{
    public function index()
    {
        // Coletar informações úteis sobre o projeto
        $estruturaProjeto = $this->getEstruturaProjeto();
        $controllersExemplo = $this->getControllersExemplo();
        $viewsExemplo = $this->getViewsExemplo();
        
        return view('admin.guia-desenvolvimento.index', compact(
            'estruturaProjeto',
            'controllersExemplo',
            'viewsExemplo'
        ));
    }
    
    /**
     * Exibir o guia detalhado da Biblioteca Digital
     */
    public function bibliotecaDigital()
    {
        return view('admin.guia-desenvolvimento.biblioteca-digital');
    }
    
    /**
     * Gera código de exemplo para um novo módulo
     */
    public function gerarExemplo(Request $request)
    {
        $request->validate([
            'nome_modulo' => 'required|string|max:50',
            'tipo' => 'required|in:crud,simples,vue',
            'funcionalidades' => 'array'
        ]);
        
        $nomeModulo = $request->nome_modulo;
        $tipo = $request->tipo;
        $funcionalidades = $request->funcionalidades ?? [];
        
        // Gerar exemplos baseados nas escolhas
        $exemplos = [
            'controller' => $this->gerarControllerExemplo($nomeModulo, $tipo, $funcionalidades),
            'model' => $this->gerarModelExemplo($nomeModulo),
            'migration' => $this->gerarMigrationExemplo($nomeModulo),
            'view' => $this->gerarViewExemplo($nomeModulo, $tipo),
            'routes' => $this->gerarRotasExemplo($nomeModulo, $tipo),
            'vue_component' => $tipo === 'vue' ? $this->gerarVueComponentExemplo($nomeModulo) : null,
        ];
        
        return response()->json($exemplos);
    }
    
    private function getEstruturaProjeto()
    {
        return [
            'app' => [
                'Http' => [
                    'Controllers' => ['Admin', 'Api', 'Auth', 'Parlamentar', 'Protocolo', 'Legislativo'],
                    'Middleware' => [],
                    'Requests' => []
                ],
                'Models' => [],
                'Services' => []
            ],
            'resources' => [
                'views' => ['layouts', 'components', 'admin', 'auth'],
                'js' => ['components'],
                'css' => []
            ],
            'routes' => ['web.php', 'api.php'],
            'database' => ['migrations', 'seeders', 'factories']
        ];
    }
    
    private function getControllersExemplo()
    {
        return [
            'ProposicaoController',
            'DocumentoController',
            'ParlamentarController',
            'ProtocoloController'
        ];
    }
    
    private function getViewsExemplo()
    {
        return [
            'proposicoes.index',
            'proposicoes.create',
            'proposicoes.edit',
            'proposicoes.show'
        ];
    }
    
    private function gerarControllerExemplo($nomeModulo, $tipo, $funcionalidades)
    {
        $nomeClasse = ucfirst($nomeModulo) . 'Controller';
        $nomeModel = ucfirst($nomeModulo);
        
        if ($tipo === 'crud') {
            return <<<PHP
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\\{$nomeModel};
use Illuminate\Http\Request;

class {$nomeClasse} extends Controller
{
    public function index()
    {
        \${$nomeModulo}s = {$nomeModel}::paginate(15);
        return view('{$nomeModulo}.index', compact('{$nomeModulo}s'));
    }

    public function create()
    {
        return view('{$nomeModulo}.create');
    }

    public function store(Request \$request)
    {
        \$validated = \$request->validate([
            'nome' => 'required|string|max:255',
            // Adicione mais validações aqui
        ]);
        
        \${$nomeModulo} = {$nomeModel}::create(\$validated);
        
        return redirect()
            ->route('{$nomeModulo}.index')
            ->with('success', '{$nomeModel} criado com sucesso!');
    }

    public function show({$nomeModel} \${$nomeModulo})
    {
        return view('{$nomeModulo}.show', compact('{$nomeModulo}'));
    }

    public function edit({$nomeModel} \${$nomeModulo})
    {
        return view('{$nomeModulo}.edit', compact('{$nomeModulo}'));
    }

    public function update(Request \$request, {$nomeModel} \${$nomeModulo})
    {
        \$validated = \$request->validate([
            'nome' => 'required|string|max:255',
            // Adicione mais validações aqui
        ]);
        
        \${$nomeModulo}->update(\$validated);
        
        return redirect()
            ->route('{$nomeModulo}.index')
            ->with('success', '{$nomeModel} atualizado com sucesso!');
    }

    public function destroy({$nomeModel} \${$nomeModulo})
    {
        \${$nomeModulo}->delete();
        
        return redirect()
            ->route('{$nomeModulo}.index')
            ->with('success', '{$nomeModel} excluído com sucesso!');
    }
}
PHP;
        } elseif ($tipo === 'vue') {
            return <<<PHP
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\\{$nomeModel};
use Illuminate\Http\Request;

class {$nomeClasse} extends Controller
{
    public function index(Request \$request)
    {
        \$query = {$nomeModel}::query();
        
        if (\$request->has('search')) {
            \$query->where('nome', 'like', '%' . \$request->search . '%');
        }
        
        return response()->json(\$query->paginate(15));
    }

    public function store(Request \$request)
    {
        \$validated = \$request->validate([
            'nome' => 'required|string|max:255',
        ]);
        
        \${$nomeModulo} = {$nomeModel}::create(\$validated);
        
        return response()->json(\${$nomeModulo}, 201);
    }

    public function show({$nomeModel} \${$nomeModulo})
    {
        return response()->json(\${$nomeModulo});
    }

    public function update(Request \$request, {$nomeModel} \${$nomeModulo})
    {
        \$validated = \$request->validate([
            'nome' => 'required|string|max:255',
        ]);
        
        \${$nomeModulo}->update(\$validated);
        
        return response()->json(\${$nomeModulo});
    }

    public function destroy({$nomeModel} \${$nomeModulo})
    {
        \${$nomeModulo}->delete();
        
        return response()->json(null, 204);
    }
}
PHP;
        } else {
            return <<<PHP
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class {$nomeClasse} extends Controller
{
    public function index()
    {
        return view('{$nomeModulo}.index');
    }
}
PHP;
        }
    }
    
    private function gerarModelExemplo($nomeModulo)
    {
        $nomeModel = ucfirst($nomeModulo);
        $tabela = strtolower($nomeModulo) . 's';
        
        return <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {$nomeModel} extends Model
{
    use HasFactory;

    protected \$table = '{$tabela}';

    protected \$fillable = [
        'nome',
        'descricao',
        'ativo',
        // Adicione mais campos aqui
    ];

    protected \$casts = [
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relacionamentos
    public function user()
    {
        return \$this->belongsTo(User::class);
    }

    // Scopes
    public function scopeAtivo(\$query)
    {
        return \$query->where('ativo', true);
    }

    // Acessors & Mutators
    public function getNomeCompletoAttribute()
    {
        return \$this->nome . ' - ' . \$this->descricao;
    }
}
PHP;
    }
    
    private function gerarMigrationExemplo($nomeModulo)
    {
        $tabela = strtolower($nomeModulo) . 's';
        
        return <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$tabela}', function (Blueprint \$table) {
            \$table->id();
            \$table->string('nome');
            \$table->text('descricao')->nullable();
            \$table->boolean('ativo')->default(true);
            \$table->foreignId('user_id')->nullable()->constrained();
            \$table->timestamps();
            
            // Índices
            \$table->index('nome');
            \$table->index('ativo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$tabela}');
    }
};
PHP;
    }
    
    private function gerarViewExemplo($nomeModulo, $tipo)
    {
        if ($tipo === 'vue') {
            return <<<BLADE
@extends('components.layouts.app')

@section('title', '{$nomeModulo} - Sistema Parlamentar')

@section('content')
<div id="{$nomeModulo}-app">
    <{$nomeModulo}-component></{$nomeModulo}-component>
</div>
@endsection

@push('scripts')
<script>
    const { createApp } = Vue;
    const app = createApp({
        components: {
            '{$nomeModulo}-component': window.{$nomeModulo}Component
        }
    });
    app.mount('#{$nomeModulo}-app');
</script>
@endpush
BLADE;
        } else {
            return <<<BLADE
@extends('components.layouts.app')

@section('title', '{$nomeModulo} - Sistema Parlamentar')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    {$nomeModulo}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">{$nomeModulo}</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('{$nomeModulo}.create') }}" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-plus fs-2"></i>
                    Novo
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" class="form-control form-control-solid w-250px ps-13" placeholder="Pesquisar...">
                        </div>
                    </div>
                </div>
                <!--end::Card header-->
                
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th class="text-end min-w-100px">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @forelse(\${$nomeModulo}s ?? [] as \$item)
                            <tr>
                                <td>{{ \$item->id }}</td>
                                <td>{{ \$item->nome }}</td>
                                <td>
                                    <div class="badge badge-light-success">Ativo</div>
                                </td>
                                <td>{{ \$item->created_at->format('d/m/Y') }}</td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-light btn-active-light-primary btn-sm">
                                        Ações
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-10">
                                    <div class="text-muted">Nenhum registro encontrado</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
    </div>
    <!--end::Content-->
</div>
@endsection
BLADE;
        }
    }
    
    private function gerarRotasExemplo($nomeModulo, $tipo)
    {
        if ($tipo === 'vue') {
            return <<<PHP
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('{$nomeModulo}', App\Http\Controllers\Api\\{$nomeModulo}Controller::class);
});

// routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::get('/{$nomeModulo}', function () {
        return view('{$nomeModulo}.index');
    })->name('{$nomeModulo}.index');
});
PHP;
        } else {
            return <<<PHP
// routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::prefix('{$nomeModulo}')->name('{$nomeModulo}.')->group(function () {
        Route::get('/', [{$nomeModulo}Controller::class, 'index'])->name('index');
        Route::get('/create', [{$nomeModulo}Controller::class, 'create'])->name('create');
        Route::post('/', [{$nomeModulo}Controller::class, 'store'])->name('store');
        Route::get('/{{$nomeModulo}}', [{$nomeModulo}Controller::class, 'show'])->name('show');
        Route::get('/{{$nomeModulo}}/edit', [{$nomeModulo}Controller::class, 'edit'])->name('edit');
        Route::put('/{{$nomeModulo}}', [{$nomeModulo}Controller::class, 'update'])->name('update');
        Route::delete('/{{$nomeModulo}}', [{$nomeModulo}Controller::class, 'destroy'])->name('destroy');
    });
});
PHP;
        }
    }
    
    private function gerarVueComponentExemplo($nomeModulo)
    {
        $componentName = ucfirst($nomeModulo);
        
        return <<<VUE
<template>
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <h3>{$componentName} Manager</h3>
            </div>
            <div class="card-toolbar">
                <button @click="showCreateModal = true" class="btn btn-primary">
                    <i class="ki-duotone ki-plus fs-2"></i>
                    Novo {$componentName}
                </button>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Search -->
            <div class="d-flex align-items-center position-relative my-1 mb-5">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <input 
                    v-model="searchTerm"
                    @input="debouncedSearch"
                    type="text" 
                    class="form-control form-control-solid w-250px ps-13" 
                    placeholder="Pesquisar..."
                >
            </div>
            
            <!-- Table -->
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        <tr v-for="item in items" :key="item.id">
                            <td>{{ item.id }}</td>
                            <td>{{ item.nome }}</td>
                            <td>{{ item.descricao }}</td>
                            <td>
                                <span class="badge" :class="item.ativo ? 'badge-light-success' : 'badge-light-danger'">
                                    {{ item.ativo ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td>{{ formatDate(item.created_at) }}</td>
                            <td class="text-end">
                                <button @click="editItem(item)" class="btn btn-sm btn-light btn-active-light-primary me-2">
                                    <i class="ki-duotone ki-pencil fs-5"></i>
                                </button>
                                <button @click="deleteItem(item)" class="btn btn-sm btn-light btn-active-light-danger">
                                    <i class="ki-duotone ki-trash fs-5"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-5" v-if="pagination.total > 0">
                <div class="text-muted">
                    Mostrando {{ pagination.from }} a {{ pagination.to }} de {{ pagination.total }} registros
                </div>
                <nav>
                    <ul class="pagination">
                        <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
                            <a class="page-link" @click="changePage(pagination.current_page - 1)" href="javascript:;">
                                Anterior
                            </a>
                        </li>
                        <li v-for="page in visiblePages" :key="page" 
                            class="page-item" 
                            :class="{ active: page === pagination.current_page }">
                            <a class="page-link" @click="changePage(page)" href="javascript:;">
                                {{ page }}
                            </a>
                        </li>
                        <li class="page-item" :class="{ disabled: !pagination.next_page_url }">
                            <a class="page-link" @click="changePage(pagination.current_page + 1)" href="javascript:;">
                                Próximo
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    
    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ editingItem ? 'Editar' : 'Novo' }} {$componentName}
                    </h5>
                    <button type="button" class="btn-close" @click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveItem">
                        <div class="mb-3">
                            <label class="form-label required">Nome</label>
                            <input v-model="formData.nome" type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea v-model="formData.descricao" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input v-model="formData.ativo" class="form-check-input" type="checkbox" id="ativoCheck">
                                <label class="form-check-label" for="ativoCheck">
                                    Ativo
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" @click="closeModal">Cancelar</button>
                    <button type="button" class="btn btn-primary" @click="saveItem">
                        <span v-if="!saving">Salvar</span>
                        <span v-else>
                            <span class="spinner-border spinner-border-sm me-2"></span>
                            Salvando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div v-if="showModal" class="modal-backdrop fade show"></div>
</template>

<script>
export default {
    name: '{$componentName}Component',
    
    data() {
        return {
            items: [],
            searchTerm: '',
            showModal: false,
            showCreateModal: false,
            editingItem: null,
            formData: {
                nome: '',
                descricao: '',
                ativo: true
            },
            pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 15,
                total: 0,
                from: 0,
                to: 0,
                prev_page_url: null,
                next_page_url: null
            },
            loading: false,
            saving: false,
            searchTimeout: null
        }
    },
    
    computed: {
        visiblePages() {
            const pages = [];
            const current = this.pagination.current_page;
            const last = this.pagination.last_page;
            
            let start = Math.max(1, current - 2);
            let end = Math.min(last, current + 2);
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            
            return pages;
        }
    },
    
    mounted() {
        this.loadItems();
    },
    
    methods: {
        async loadItems(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: page,
                    search: this.searchTerm
                });
                
                const response = await fetch(`/api/{$nomeModulo}?\${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                this.items = data.data;
                this.pagination = {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    per_page: data.per_page,
                    total: data.total,
                    from: data.from,
                    to: data.to,
                    prev_page_url: data.prev_page_url,
                    next_page_url: data.next_page_url
                };
            } catch (error) {
                console.error('Erro ao carregar itens:', error);
                this.showToast('Erro ao carregar dados', 'error');
            } finally {
                this.loading = false;
            }
        },
        
        debouncedSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.loadItems(1);
            }, 500);
        },
        
        changePage(page) {
            if (page < 1 || page > this.pagination.last_page) return;
            this.loadItems(page);
        },
        
        editItem(item) {
            this.editingItem = item;
            this.formData = { ...item };
            this.showModal = true;
        },
        
        async saveItem() {
            this.saving = true;
            try {
                const url = this.editingItem 
                    ? `/api/{$nomeModulo}/\${this.editingItem.id}`
                    : '/api/{$nomeModulo}';
                    
                const method = this.editingItem ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.formData)
                });
                
                if (response.ok) {
                    this.showToast(
                        this.editingItem ? 'Item atualizado com sucesso' : 'Item criado com sucesso',
                        'success'
                    );
                    this.closeModal();
                    this.loadItems(this.pagination.current_page);
                } else {
                    throw new Error('Erro ao salvar');
                }
            } catch (error) {
                console.error('Erro ao salvar:', error);
                this.showToast('Erro ao salvar item', 'error');
            } finally {
                this.saving = false;
            }
        },
        
        async deleteItem(item) {
            if (!confirm(`Tem certeza que deseja excluir "\${item.nome}"?`)) return;
            
            try {
                const response = await fetch(`/api/{$nomeModulo}/\${item.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    this.showToast('Item excluído com sucesso', 'success');
                    this.loadItems(this.pagination.current_page);
                } else {
                    throw new Error('Erro ao excluir');
                }
            } catch (error) {
                console.error('Erro ao excluir:', error);
                this.showToast('Erro ao excluir item', 'error');
            }
        },
        
        closeModal() {
            this.showModal = false;
            this.showCreateModal = false;
            this.editingItem = null;
            this.formData = {
                nome: '',
                descricao: '',
                ativo: true
            };
        },
        
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('pt-BR');
        },
        
        showToast(message, type = 'info') {
            // Integração com sistema de notificação do template
            if (window.toastr) {
                window.toastr[type](message);
            } else {
                alert(message);
            }
        }
    },
    
    watch: {
        showCreateModal(val) {
            if (val) {
                this.showModal = true;
            }
        }
    }
}
</script>

<style scoped>
.modal.show {
    background-color: rgba(0, 0, 0, 0.1);
}
</style>
VUE;
    }
}