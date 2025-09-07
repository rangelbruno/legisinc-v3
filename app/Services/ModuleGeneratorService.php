<?php

namespace App\Services;

use App\Models\GeneratedModule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleGeneratorService
{
    private $generatedFiles = [];
    private $tableName = '';
    private $modelName = '';
    private $controllerName = '';
    private $module = null;

    public function generateModule(GeneratedModule $module): array
    {
        $this->module = $module;
        $this->tableName = $module->table_name;
        $this->modelName = Str::studly(Str::singular($module->table_name));
        $this->controllerName = $this->modelName . 'Controller';

        try {
            DB::beginTransaction();

            // 1. Criar Migration
            $this->generateMigration();

            // 2. Criar Model
            $this->generateModel();

            // 3. Criar Controller
            if ($module->has_crud) {
                $this->generateController();
            }

            // 4. Criar Views
            if ($module->has_crud) {
                $this->generateViews();
            }

            // 5. Adicionar Rotas
            $this->generateRoutes();

            // 6. Executar Migration
            $this->runMigration();

            // 7. Marcar como gerado
            $module->markAsGenerated($this->generatedFiles);

            DB::commit();

            return [
                'success' => true,
                'files' => $this->generatedFiles,
                'message' => "Módulo '{$module->name}' gerado com sucesso!"
            ];

        } catch (\Exception $e) {
            DB::rollback();
            
            // Limpar arquivos gerados em caso de erro
            $this->cleanupGeneratedFiles();
            
            $module->markAsError($e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => "Erro ao gerar módulo: " . $e->getMessage()
            ];
        }
    }

    private function generateMigration(): void
    {
        $migrationName = 'create_' . $this->tableName . '_table';
        $migrationFile = database_path('migrations/' . now()->format('Y_m_d_His') . '_' . $migrationName . '.php');
        
        $migrationContent = $this->buildMigrationContent();
        
        File::put($migrationFile, $migrationContent);
        $this->generatedFiles[] = $migrationFile;
    }

    private function buildMigrationContent(): string
    {
        $fields = $this->module->fields_config;
        $relationships = $this->module->relationships ?? [];

        $fieldsCode = "";
        
        // Campos básicos
        foreach ($fields as $field) {
            $fieldsCode .= $this->generateFieldMigration($field);
        }

        // Relacionamentos
        foreach ($relationships as $relationship) {
            if ($relationship['type'] === 'belongsTo') {
                $fieldsCode .= "            \$table->foreignId('{$relationship['foreign_key']}')->constrained('{$relationship['table']}');\n";
            }
        }

        $className = 'Create' . Str::studly($this->tableName) . 'Table';

        return "<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$this->tableName}', function (Blueprint \$table) {
            \$table->id();
{$fieldsCode}            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$this->tableName}');
    }
};
";
    }

    private function generateFieldMigration(array $field): string
    {
        $nullable = $field['nullable'] ?? false ? '->nullable()' : '';
        $default = isset($field['default']) ? "->default('{$field['default']}')" : '';

        switch ($field['type']) {
            case 'string':
                $length = $field['length'] ?? 255;
                return "            \$table->string('{$field['name']}', {$length}){$nullable}{$default};\n";
            
            case 'text':
                return "            \$table->text('{$field['name']}'){$nullable}{$default};\n";
            
            case 'integer':
                return "            \$table->integer('{$field['name']}'){$nullable}{$default};\n";
            
            case 'boolean':
                $default = $default ?: '->default(false)';
                return "            \$table->boolean('{$field['name']}'){$nullable}{$default};\n";
            
            case 'date':
                return "            \$table->date('{$field['name']}'){$nullable}{$default};\n";
            
            case 'datetime':
                return "            \$table->timestamp('{$field['name']}'){$nullable}{$default};\n";
            
            case 'json':
                return "            \$table->json('{$field['name']}'){$nullable};\n";
            
            case 'decimal':
                $precision = $field['precision'] ?? 8;
                $scale = $field['scale'] ?? 2;
                return "            \$table->decimal('{$field['name']}', {$precision}, {$scale}){$nullable}{$default};\n";
            
            default:
                return "            \$table->string('{$field['name']}'){$nullable}{$default};\n";
        }
    }

    private function generateModel(): void
    {
        $modelFile = app_path("Models/{$this->modelName}.php");
        $modelContent = $this->buildModelContent();
        
        File::put($modelFile, $modelContent);
        $this->generatedFiles[] = $modelFile;
    }

    private function buildModelContent(): string
    {
        $fields = $this->module->fields_config;
        $relationships = $this->module->relationships ?? [];

        // Fillable
        $fillableFields = collect($fields)->pluck('name')->toArray();
        $fillableCode = "'" . implode("', '", $fillableFields) . "'";

        // Casts
        $castsArray = [];
        foreach ($fields as $field) {
            if ($field['type'] === 'json') {
                $castsArray[] = "'{$field['name']}' => 'array'";
            } elseif ($field['type'] === 'boolean') {
                $castsArray[] = "'{$field['name']}' => 'boolean'";
            } elseif (in_array($field['type'], ['date', 'datetime'])) {
                $castsArray[] = "'{$field['name']}' => 'datetime'";
            }
        }
        $castsCode = empty($castsArray) ? '' : "    protected \$casts = [\n        " . implode(",\n        ", $castsArray) . "\n    ];\n\n";

        // Relacionamentos
        $relationshipsCode = '';
        foreach ($relationships as $relationship) {
            $relationshipsCode .= $this->generateRelationshipMethod($relationship);
        }

        // Business Logic personalizada
        $businessLogic = $this->module->business_logic ? "\n    " . $this->module->business_logic . "\n" : '';

        return "<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;
use Illuminate\\Database\\Eloquent\\Relations\\HasMany;

class {$this->modelName} extends Model
{
    protected \$fillable = [
        {$fillableCode}
    ];

{$castsCode}{$relationshipsCode}{$businessLogic}}
";
    }

    private function generateRelationshipMethod(array $relationship): string
    {
        $methodName = $relationship['method_name'];
        $relatedModel = Str::studly(Str::singular($relationship['table']));
        
        switch ($relationship['type']) {
            case 'belongsTo':
                return "    public function {$methodName}(): BelongsTo
    {
        return \$this->belongsTo({$relatedModel}::class, '{$relationship['foreign_key']}');
    }

";
            
            case 'hasMany':
                return "    public function {$methodName}(): HasMany
    {
        return \$this->hasMany({$relatedModel}::class, '{$relationship['foreign_key']}');
    }

";
        }

        return '';
    }

    private function generateController(): void
    {
        $controllerFile = app_path("Http/Controllers/{$this->controllerName}.php");
        $controllerContent = $this->buildControllerContent();
        
        File::put($controllerFile, $controllerContent);
        $this->generatedFiles[] = $controllerFile;
    }

    private function buildControllerContent(): string
    {
        $slug = $this->module->slug;
        $modelName = $this->modelName;
        $modelVariable = Str::camel($modelName);
        $pluralSlug = Str::plural($slug);

        return "<?php

namespace App\\Http\\Controllers;

use App\\Models\\{$modelName};
use Illuminate\\Http\\Request;

class {$this->controllerName} extends Controller
{
    public function index()
    {
        \${$pluralSlug} = {$modelName}::paginate(15);
        
        return view('{$slug}.index', compact('{$pluralSlug}'));
    }

    public function create()
    {
        return view('{$slug}.create');
    }

    public function store(Request \$request)
    {
        \$validated = \$request->validate([
            // Adicione validações conforme necessário
        ]);

        \${$modelVariable} = {$modelName}::create(\$validated);

        return redirect()->route('{$slug}.index')
            ->with('success', '{$this->module->name} criado com sucesso!');
    }

    public function show({$modelName} \${$modelVariable})
    {
        return view('{$slug}.show', compact('{$modelVariable}'));
    }

    public function edit({$modelName} \${$modelVariable})
    {
        return view('{$slug}.edit', compact('{$modelVariable}'));
    }

    public function update(Request \$request, {$modelName} \${$modelVariable})
    {
        \$validated = \$request->validate([
            // Adicione validações conforme necessário
        ]);

        \${$modelVariable}->update(\$validated);

        return redirect()->route('{$slug}.index')
            ->with('success', '{$this->module->name} atualizado com sucesso!');
    }

    public function destroy({$modelName} \${$modelVariable})
    {
        \${$modelVariable}->delete();

        return redirect()->route('{$slug}.index')
            ->with('success', '{$this->module->name} excluído com sucesso!');
    }
}
";
    }

    private function generateViews(): void
    {
        $viewsPath = resource_path("views/{$this->module->slug}");
        
        if (!File::exists($viewsPath)) {
            File::makeDirectory($viewsPath, 0755, true);
        }

        // Index View
        $indexView = $viewsPath . '/index.blade.php';
        File::put($indexView, $this->buildIndexView());
        $this->generatedFiles[] = $indexView;

        // Create View  
        $createView = $viewsPath . '/create.blade.php';
        File::put($createView, $this->buildCreateView());
        $this->generatedFiles[] = $createView;

        // Edit View
        $editView = $viewsPath . '/edit.blade.php';
        File::put($editView, $this->buildEditView());
        $this->generatedFiles[] = $editView;

        // Show View
        $showView = $viewsPath . '/show.blade.php';
        File::put($showView, $this->buildShowView());
        $this->generatedFiles[] = $showView;
    }

    private function buildIndexView(): string
    {
        $moduleName = $this->module->name;
        $slug = $this->module->slug;
        $pluralSlug = Str::plural($slug);
        $modelVariable = Str::camel($this->modelName);

        return "@extends('components.layouts.app')

@section('title', '{$moduleName} - Sistema Parlamentar')

@section('content')
<div class=\"d-flex flex-column flex-column-fluid\">
    <!--begin::Toolbar-->
    <div id=\"kt_app_toolbar\" class=\"app-toolbar py-3 py-lg-6\">
        <div id=\"kt_app_toolbar_container\" class=\"app-container container-xxl d-flex flex-stack\">
            <div class=\"page-title d-flex flex-column justify-content-center flex-wrap me-3\">
                <h1 class=\"page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0\">
                    {$moduleName}
                </h1>
                <ul class=\"breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1\">
                    <li class=\"breadcrumb-item text-muted\">
                        <a href=\"{{ route('dashboard') }}\" class=\"text-muted text-hover-primary\">Dashboard</a>
                    </li>
                    <li class=\"breadcrumb-item\">
                        <span class=\"bullet bg-gray-400 w-5px h-2px\"></span>
                    </li>
                    <li class=\"breadcrumb-item text-muted\">{$moduleName}</li>
                </ul>
            </div>
            <div class=\"d-flex align-items-center gap-2 gap-lg-3\">
                <a href=\"{{ route('{$slug}.create') }}\" class=\"btn btn-sm fw-bold btn-primary\">
                    <i class=\"ki-duotone ki-plus fs-2\"></i>Novo {$moduleName}
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id=\"kt_app_content\" class=\"app-content flex-column-fluid\">
        <div id=\"kt_app_content_container\" class=\"app-container container-xxl\">
            <!--begin::Card-->
            <div class=\"card\">
                <!--begin::Card body-->
                <div class=\"card-body pt-0\">
                    <!--begin::Table-->
                    <table class=\"table align-middle table-row-dashed fs-6 gy-5\" id=\"kt_table_{$slug}\">
                        <thead>
                            <tr class=\"text-start text-muted fw-bold fs-7 text-uppercase gs-0\">
                                <th>ID</th>
                                <th>Dados</th>
                                <th>Data Criação</th>
                                <th class=\"text-end min-w-100px\">Ações</th>
                            </tr>
                        </thead>
                        <tbody class=\"text-gray-600 fw-semibold\">
                            @foreach(\${$pluralSlug} as \${$modelVariable})
                            <tr>
                                <td>{{ \${$modelVariable}->id }}</td>
                                <td>
                                    <!-- Adicione campos relevantes aqui -->
                                </td>
                                <td>{{ \${$modelVariable}->created_at->format('d/m/Y H:i') }}</td>
                                <td class=\"text-end\">
                                    <a href=\"{{ route('{$slug}.show', \${$modelVariable}) }}\" class=\"btn btn-sm btn-light btn-active-light-primary\">
                                        Visualizar
                                    </a>
                                    <a href=\"{{ route('{$slug}.edit', \${$modelVariable}) }}\" class=\"btn btn-sm btn-light btn-active-light-primary\">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!--end::Table-->
                    
                    {{ \${$pluralSlug}->links() }}
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
    </div>
    <!--end::Content-->
</div>
@endsection
";
    }

    private function buildCreateView(): string
    {
        $moduleName = $this->module->name;
        $slug = $this->module->slug;
        
        $formFields = '';
        foreach ($this->module->fields_config as $field) {
            $formFields .= $this->generateFormField($field);
        }

        return "@extends('components.layouts.app')

@section('title', 'Novo {$moduleName} - Sistema Parlamentar')

@section('content')
<div class=\"d-flex flex-column flex-column-fluid\">
    <!--begin::Toolbar-->
    <div id=\"kt_app_toolbar\" class=\"app-toolbar py-3 py-lg-6\">
        <div id=\"kt_app_toolbar_container\" class=\"app-container container-xxl d-flex flex-stack\">
            <div class=\"page-title d-flex flex-column justify-content-center flex-wrap me-3\">
                <h1 class=\"page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0\">
                    Novo {$moduleName}
                </h1>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id=\"kt_app_content\" class=\"app-content flex-column-fluid\">
        <div id=\"kt_app_content_container\" class=\"app-container container-xxl\">
            <!--begin::Card-->
            <div class=\"card\">
                <form action=\"{{ route('{$slug}.store') }}\" method=\"POST\">
                    @csrf
                    <!--begin::Card body-->
                    <div class=\"card-body\">
{$formFields}
                    </div>
                    <!--end::Card body-->
                    
                    <!--begin::Card footer-->
                    <div class=\"card-footer d-flex justify-content-end\">
                        <a href=\"{{ route('{$slug}.index') }}\" class=\"btn btn-light me-3\">Cancelar</a>
                        <button type=\"submit\" class=\"btn btn-primary\">Salvar</button>
                    </div>
                    <!--end::Card footer-->
                </form>
            </div>
            <!--end::Card-->
        </div>
    </div>
    <!--end::Content-->
</div>
@endsection
";
    }

    private function generateFormField(array $field): string
    {
        $name = $field['name'];
        $label = $field['label'] ?? Str::title(str_replace('_', ' ', $name));
        $required = ($field['nullable'] ?? false) ? '' : 'required';

        switch ($field['type']) {
            case 'text':
                return "                        <div class=\"mb-10\">
                            <label class=\"form-label\">{$label}</label>
                            <textarea name=\"{$name}\" class=\"form-control\" rows=\"4\" {$required}>{{ old('{$name}') }}</textarea>
                        </div>\n";
            
            case 'boolean':
                return "                        <div class=\"mb-10\">
                            <div class=\"form-check form-switch\">
                                <input class=\"form-check-input\" type=\"checkbox\" name=\"{$name}\" id=\"{$name}\" value=\"1\" {{ old('{$name}') ? 'checked' : '' }}>
                                <label class=\"form-check-label\" for=\"{$name}\">{$label}</label>
                            </div>
                        </div>\n";
            
            case 'date':
                return "                        <div class=\"mb-10\">
                            <label class=\"form-label\">{$label}</label>
                            <input type=\"date\" name=\"{$name}\" class=\"form-control\" value=\"{{ old('{$name}') }}\" {$required}>
                        </div>\n";
            
            default:
                return "                        <div class=\"mb-10\">
                            <label class=\"form-label\">{$label}</label>
                            <input type=\"text\" name=\"{$name}\" class=\"form-control\" value=\"{{ old('{$name}') }}\" {$required}>
                        </div>\n";
        }
    }

    private function buildEditView(): string
    {
        $moduleName = $this->module->name;
        $slug = $this->module->slug;
        $modelVariable = Str::camel($this->modelName);
        
        $formFields = '';
        foreach ($this->module->fields_config as $field) {
            $formFields .= $this->generateEditFormField($field, $modelVariable);
        }

        return "@extends('components.layouts.app')

@section('title', 'Editar {$moduleName} - Sistema Parlamentar')

@section('content')
<div class=\"d-flex flex-column flex-column-fluid\">
    <!--begin::Toolbar-->
    <div id=\"kt_app_toolbar\" class=\"app-toolbar py-3 py-lg-6\">
        <div id=\"kt_app_toolbar_container\" class=\"app-container container-xxl d-flex flex-stack\">
            <div class=\"page-title d-flex flex-column justify-content-center flex-wrap me-3\">
                <h1 class=\"page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0\">
                    Editar {$moduleName}
                </h1>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id=\"kt_app_content\" class=\"app-content flex-column-fluid\">
        <div id=\"kt_app_content_container\" class=\"app-container container-xxl\">
            <!--begin::Card-->
            <div class=\"card\">
                <form action=\"{{ route('{$slug}.update', \${$modelVariable}) }}\" method=\"POST\">
                    @csrf
                    @method('PUT')
                    <!--begin::Card body-->
                    <div class=\"card-body\">
{$formFields}
                    </div>
                    <!--end::Card body-->
                    
                    <!--begin::Card footer-->
                    <div class=\"card-footer d-flex justify-content-end\">
                        <a href=\"{{ route('{$slug}.index') }}\" class=\"btn btn-light me-3\">Cancelar</a>
                        <button type=\"submit\" class=\"btn btn-primary\">Atualizar</button>
                    </div>
                    <!--end::Card footer-->
                </form>
            </div>
            <!--end::Card-->
        </div>
    </div>
    <!--end::Content-->
</div>
@endsection
";
    }

    private function generateEditFormField(array $field, string $modelVariable): string
    {
        $name = $field['name'];
        $label = $field['label'] ?? Str::title(str_replace('_', ' ', $name));
        $required = ($field['nullable'] ?? false) ? '' : 'required';

        switch ($field['type']) {
            case 'text':
                return "                        <div class=\"mb-10\">
                            <label class=\"form-label\">{$label}</label>
                            <textarea name=\"{$name}\" class=\"form-control\" rows=\"4\" {$required}>{{ old('{$name}', \${$modelVariable}->{$name}) }}</textarea>
                        </div>\n";
            
            case 'boolean':
                return "                        <div class=\"mb-10\">
                            <div class=\"form-check form-switch\">
                                <input class=\"form-check-input\" type=\"checkbox\" name=\"{$name}\" id=\"{$name}\" value=\"1\" {{ old('{$name}', \${$modelVariable}->{$name}) ? 'checked' : '' }}>
                                <label class=\"form-check-label\" for=\"{$name}\">{$label}</label>
                            </div>
                        </div>\n";
            
            case 'date':
                return "                        <div class=\"mb-10\">
                            <label class=\"form-label\">{$label}</label>
                            <input type=\"date\" name=\"{$name}\" class=\"form-control\" value=\"{{ old('{$name}', \${$modelVariable}->{$name}?->format('Y-m-d')) }}\" {$required}>
                        </div>\n";
            
            default:
                return "                        <div class=\"mb-10\">
                            <label class=\"form-label\">{$label}</label>
                            <input type=\"text\" name=\"{$name}\" class=\"form-control\" value=\"{{ old('{$name}', \${$modelVariable}->{$name}) }}\" {$required}>
                        </div>\n";
        }
    }

    private function buildShowView(): string
    {
        $moduleName = $this->module->name;
        $slug = $this->module->slug;
        $modelVariable = Str::camel($this->modelName);

        return "@extends('components.layouts.app')

@section('title', 'Visualizar {$moduleName} - Sistema Parlamentar')

@section('content')
<div class=\"d-flex flex-column flex-column-fluid\">
    <!--begin::Toolbar-->
    <div id=\"kt_app_toolbar\" class=\"app-toolbar py-3 py-lg-6\">
        <div id=\"kt_app_toolbar_container\" class=\"app-container container-xxl d-flex flex-stack\">
            <div class=\"page-title d-flex flex-column justify-content-center flex-wrap me-3\">
                <h1 class=\"page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0\">
                    Visualizar {$moduleName}
                </h1>
            </div>
            <div class=\"d-flex align-items-center gap-2 gap-lg-3\">
                <a href=\"{{ route('{$slug}.edit', \${$modelVariable}) }}\" class=\"btn btn-sm fw-bold btn-primary\">
                    <i class=\"ki-duotone ki-pencil fs-2\"></i>Editar
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id=\"kt_app_content\" class=\"app-content flex-column-fluid\">
        <div id=\"kt_app_content_container\" class=\"app-container container-xxl\">
            <!--begin::Card-->
            <div class=\"card\">
                <!--begin::Card body-->
                <div class=\"card-body\">
                    <!-- Adicione campos de visualização aqui -->
                    <div class=\"row\">
                        <div class=\"col-md-6\">
                            <strong>ID:</strong> {{ \${$modelVariable}->id }}
                        </div>
                        <div class=\"col-md-6\">
                            <strong>Data Criação:</strong> {{ \${$modelVariable}->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
    </div>
    <!--end::Content-->
</div>
@endsection
";
    }

    private function generateRoutes(): void
    {
        $routesContent = $this->buildRoutesContent();
        $routesFile = base_path('routes/generated_modules.php');
        
        if (!File::exists($routesFile)) {
            File::put($routesFile, "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\n");
        }
        
        File::append($routesFile, $routesContent);
        $this->generatedFiles[] = $routesFile;
    }

    private function buildRoutesContent(): string
    {
        $slug = $this->module->slug;
        $controllerName = $this->controllerName;

        return "
// Routes for {$this->module->name}
Route::middleware(['auth'])->group(function () {
    Route::prefix('{$slug}')->name('{$slug}.')->group(function () {
        Route::get('/', [App\\Http\\Controllers\\{$controllerName}::class, 'index'])->name('index');
        Route::get('/create', [App\\Http\\Controllers\\{$controllerName}::class, 'create'])->name('create');
        Route::post('/', [App\\Http\\Controllers\\{$controllerName}::class, 'store'])->name('store');
        Route::get('/{{$slug}}', [App\\Http\\Controllers\\{$controllerName}::class, 'show'])->name('show');
        Route::get('/{{$slug}}/edit', [App\\Http\\Controllers\\{$controllerName}::class, 'edit'])->name('edit');
        Route::put('/{{$slug}}', [App\\Http\\Controllers\\{$controllerName}::class, 'update'])->name('update');
        Route::delete('/{{$slug}}', [App\\Http\\Controllers\\{$controllerName}::class, 'destroy'])->name('destroy');
    });
});
";
    }

    private function runMigration(): void
    {
        Artisan::call('migrate', ['--force' => true]);
    }

    private function cleanupGeneratedFiles(): void
    {
        foreach ($this->generatedFiles as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }
        
        // Remover diretório de views se vazio
        $viewsPath = resource_path("views/{$this->module->slug}");
        if (File::exists($viewsPath) && count(File::files($viewsPath)) === 0) {
            File::deleteDirectory($viewsPath);
        }
    }
}