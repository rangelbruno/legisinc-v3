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
     * Consulta documentação Laravel via MCP Laravel Boost
     */
    public function consultarDocs(Request $request)
    {
        $request->validate([
            'topico' => 'required|string|in:routing,controllers,eloquent,blade,middleware,validation,migrations,artisan'
        ]);

        $topico = $request->topico;

        try {
            // Simular consulta via MCP Laravel Boost
            // Em uma implementação real, aqui seria feita a consulta via MCP
            $docs = $this->getMockLaravelDocs($topico);

            return response()->json([
                'success' => true,
                'topic' => $topico,
                'content' => $docs,
                'source' => 'MCP Laravel Boost'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao consultar documentação',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mock da documentação Laravel para demonstração
     * Na implementação real, seria substituído por consulta MCP
     */
    private function getMockLaravelDocs($topico)
    {
        $docs = [
            'routing' => "# Laravel Routing\n\n## Definindo Rotas\n\nRoutes no Laravel são definidas nos arquivos do diretório routes/. Para aplicações web, as rotas são tipicamente definidas em routes/web.php.\n\n### Rota Básica\n```php\nRoute::get('/user', function () {\n    return 'Hello World';\n});\n```\n\n### Rota com Parâmetros\n```php\nRoute::get('/user/{id}', function (\$id) {\n    return 'User '.\$id;\n});\n```\n\n### Route Groups\n```php\nRoute::prefix('admin')->group(function () {\n    Route::get('/users', function () {\n        // Matches \"/admin/users\"\n    });\n});\n```\n\n### Middleware em Rotas\n```php\nRoute::middleware(['auth'])->group(function () {\n    Route::get('/dashboard', function () {\n        // Só acessível para usuários autenticados\n    });\n});\n```",

            'controllers' => "# Laravel Controllers\n\n## Criando Controllers\n\nControllers agrupam lógicas de requisições relacionadas em uma única classe.\n\n### Criando um Controller\n```bash\nphp artisan make:controller UserController\n```\n\n### Controller Básico\n```php\n<?php\n\nnamespace App\\Http\\Controllers;\n\nuse App\\Http\\Controllers\\Controller;\nuse Illuminate\\Http\\Request;\n\nclass UserController extends Controller\n{\n    public function index()\n    {\n        return view('users.index');\n    }\n\n    public function show(\$id)\n    {\n        return view('users.show', ['user' => User::findOrFail(\$id)]);\n    }\n}\n```\n\n### Resource Controllers\n```bash\nphp artisan make:controller UserController --resource\n```\n\n### Registro de Rotas\n```php\nRoute::resource('users', UserController::class);\n```",

            'eloquent' => "# Eloquent ORM\n\n## O que é Eloquent?\n\nEloquent é o ORM (Object-Relational Mapping) incluído no Laravel que facilita a interação com o banco de dados.\n\n### Definindo um Model\n```php\n<?php\n\nnamespace App\\Models;\n\nuse Illuminate\\Database\\Eloquent\\Model;\n\nclass User extends Model\n{\n    protected \$fillable = [\n        'name', 'email', 'password',\n    ];\n\n    protected \$hidden = [\n        'password', 'remember_token',\n    ];\n\n    protected \$casts = [\n        'email_verified_at' => 'datetime',\n    ];\n}\n```\n\n### Consultas Básicas\n```php\n// Buscar todos os usuários\n\$users = User::all();\n\n// Buscar por ID\n\$user = User::find(1);\n\n// Criar novo usuário\n\$user = User::create([\n    'name' => 'João',\n    'email' => 'joao@email.com'\n]);\n```\n\n### Relacionamentos\n```php\n// One to Many\npublic function posts()\n{\n    return \$this->hasMany(Post::class);\n}\n\n// Many to Many\npublic function roles()\n{\n    return \$this->belongsToMany(Role::class);\n}\n```",

            'blade' => "# Blade Templates\n\n## O que é Blade?\n\nBlade é o motor de templates do Laravel que permite escrever código PHP elegante em suas views.\n\n### Sintaxe Básica\n```blade\n{{-- Comentário --}}\n{{ \$name }} {{-- Escaped output --}}\n{!! \$html !!} {{-- Raw output --}}\n```\n\n### Estruturas de Controle\n```blade\n@if(\$user->isAdmin())\n    <p>O usuário é admin</p>\n@elseif(\$user->isModerator())\n    <p>O usuário é moderador</p>\n@else\n    <p>Usuário regular</p>\n@endif\n\n@foreach(\$users as \$user)\n    <p>{{ \$user->name }}</p>\n@endforeach\n```\n\n### Layout e Sections\n```blade\n{{-- Layout master --}}\n@extends('layouts.app')\n\n@section('title', 'Página Inicial')\n\n@section('content')\n    <h1>Bem-vindo!</h1>\n@endsection\n```\n\n### Components\n```blade\n{{-- Definindo component --}}\n<x-alert type=\"error\" :message=\"\$message\" />\n\n{{-- Usando component --}}\n@component('alert')\n    @slot('title')\n        Erro!\n    @endslot\n    \n    Algo deu errado!\n@endcomponent\n```",

            'middleware' => "# Middleware\n\n## O que é Middleware?\n\nMiddleware fornece um mecanismo conveniente para inspecionar e filtrar requisições HTTP.\n\n### Criando Middleware\n```bash\nphp artisan make:middleware EnsureTokenIsValid\n```\n\n### Implementação\n```php\n<?php\n\nnamespace App\\Http\\Middleware;\n\nuse Closure;\nuse Illuminate\\Http\\Request;\n\nclass EnsureTokenIsValid\n{\n    public function handle(Request \$request, Closure \$next)\n    {\n        if (\$request->input('token') !== 'my-secret-token') {\n            return redirect('home');\n        }\n\n        return \$next(\$request);\n    }\n}\n```\n\n### Registrando Middleware\n```php\n// app/Http/Kernel.php\nprotected \$routeMiddleware = [\n    'auth' => \\App\\Http\\Middleware\\Authenticate::class,\n    'valid.token' => \\App\\Http\\Middleware\\EnsureTokenIsValid::class,\n];\n```\n\n### Usando em Rotas\n```php\n// Middleware único\nRoute::get('/admin', function () {\n    //\n})->middleware('auth');\n\n// Múltiplos middleware\nRoute::get('/admin', function () {\n    //\n})->middleware(['auth', 'valid.token']);\n```",

            'validation' => "# Validation\n\n## Validação de Dados\n\nLaravel fornece várias abordagens para validar dados de entrada da aplicação.\n\n### Validação Básica\n```php\npublic function store(Request \$request)\n{\n    \$validated = \$request->validate([\n        'title' => 'required|unique:posts|max:255',\n        'body' => 'required',\n        'email' => 'required|email',\n        'age' => 'required|integer|min:18'\n    ]);\n\n    // Dados validados...\n}\n```\n\n### Form Request Classes\n```bash\nphp artisan make:request StorePostRequest\n```\n\n```php\n<?php\n\nnamespace App\\Http\\Requests;\n\nuse Illuminate\\Foundation\\Http\\FormRequest;\n\nclass StorePostRequest extends FormRequest\n{\n    public function authorize()\n    {\n        return true;\n    }\n\n    public function rules()\n    {\n        return [\n            'title' => 'required|unique:posts|max:255',\n            'body' => 'required',\n        ];\n    }\n\n    public function messages()\n    {\n        return [\n            'title.required' => 'O título é obrigatório.',\n            'body.required' => 'O conteúdo é obrigatório.',\n        ];\n    }\n}\n```\n\n### Regras Comuns\n```php\n'required'        // Campo obrigatório\n'email'          // Deve ser email válido\n'unique:table'   // Deve ser único na tabela\n'min:3'          // Mínimo 3 caracteres\n'max:255'        // Máximo 255 caracteres\n'integer'        // Deve ser inteiro\n'boolean'        // Deve ser boolean\n'date'           // Deve ser data válida\n```",

            'migrations' => "# Database Migrations\n\n## O que são Migrations?\n\nMigrations são como controle de versão para o seu banco de dados, permitindo modificar e compartilhar o schema da aplicação.\n\n### Criando Migrations\n```bash\n# Nova tabela\nphp artisan make:migration create_users_table\n\n# Modificar tabela existente\nphp artisan make:migration add_email_to_users_table --table=users\n```\n\n### Estrutura de Migration\n```php\n<?php\n\nuse Illuminate\\Database\\Migrations\\Migration;\nuse Illuminate\\Database\\Schema\\Blueprint;\nuse Illuminate\\Support\\Facades\\Schema;\n\nreturn new class extends Migration\n{\n    public function up()\n    {\n        Schema::create('users', function (Blueprint \$table) {\n            \$table->id();\n            \$table->string('name');\n            \$table->string('email')->unique();\n            \$table->timestamp('email_verified_at')->nullable();\n            \$table->string('password');\n            \$table->rememberToken();\n            \$table->timestamps();\n        });\n    }\n\n    public function down()\n    {\n        Schema::dropIfExists('users');\n    }\n};\n```\n\n### Tipos de Coluna Comuns\n```php\n\$table->id();                     // Auto-incrementing ID\n\$table->string('name');           // VARCHAR\n\$table->text('description');      // TEXT\n\$table->integer('votes');         // INTEGER\n\$table->boolean('confirmed');     // BOOLEAN\n\$table->timestamps();             // created_at e updated_at\n\$table->foreignId('user_id');     // Foreign key\n```\n\n### Executando Migrations\n```bash\nphp artisan migrate              # Executar migrations\nphp artisan migrate:rollback     # Reverter último batch\nphp artisan migrate:reset        # Reverter todas\nphp artisan migrate:fresh        # Drop all tables e migrate\n```",

            'artisan' => "# Artisan Commands\n\n## O que é Artisan?\n\nArtisan é a interface de linha de comando incluída no Laravel que fornece comandos úteis durante o desenvolvimento.\n\n### Comandos Comuns\n\n#### Criação de Arquivos\n```bash\nphp artisan make:controller UserController\nphp artisan make:model User\nphp artisan make:migration create_users_table\nphp artisan make:seeder UserSeeder\nphp artisan make:factory UserFactory\nphp artisan make:middleware AuthMiddleware\nphp artisan make:request StoreUserRequest\nphp artisan make:resource UserResource\n```\n\n#### Database\n```bash\nphp artisan migrate              # Executar migrations\nphp artisan migrate:fresh --seed # Fresh migrate com seeders\nphp artisan db:seed              # Executar seeders\nphp artisan tinker               # REPL interativo\n```\n\n#### Cache e Otimização\n```bash\nphp artisan cache:clear          # Limpar cache\nphp artisan config:clear         # Limpar cache de config\nphp artisan route:clear          # Limpar cache de rotas\nphp artisan view:clear           # Limpar cache de views\nphp artisan optimize             # Otimizar para produção\n```\n\n#### Servidor de Desenvolvimento\n```bash\nphp artisan serve                # Iniciar servidor dev\nphp artisan serve --port=8080    # Porta customizada\n```\n\n### Criando Comandos Customizados\n```bash\nphp artisan make:command SendEmails\n```\n\n```php\n<?php\n\nnamespace App\\Console\\Commands;\n\nuse Illuminate\\Console\\Command;\n\nclass SendEmails extends Command\n{\n    protected \$signature = 'email:send {user}';\n    protected \$description = 'Send email to user';\n\n    public function handle()\n    {\n        \$userId = \$this->argument('user');\n        \$this->info('Email sent to user ' . \$userId);\n    }\n}\n```"
        ];

        return $docs[$topico] ?? 'Documentação não encontrada.';
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