<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneratedModule;
use App\Services\ModuleGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ModuleGeneratorController extends Controller
{
    private $moduleGenerator;

    public function __construct(ModuleGeneratorService $moduleGenerator)
    {
        $this->moduleGenerator = $moduleGenerator;
    }

    public function index()
    {
        $modules = GeneratedModule::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.module-generator.index', compact('modules'));
    }

    public function create()
    {
        // Buscar tabelas existentes para relacionamentos
        $existingTables = $this->getExistingTables();
        
        return view('admin.module-generator.create', compact('existingTables'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:generated_modules,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'has_crud' => 'boolean',
            'has_permissions' => 'boolean',
            'fields' => 'required|array|min:1',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.type' => 'required|string|in:string,text,integer,boolean,date,datetime,json,decimal',
            'fields.*.label' => 'nullable|string|max:255',
            'fields.*.nullable' => 'boolean',
            'fields.*.default' => 'nullable|string',
            'fields.*.length' => 'nullable|integer|min:1|max:65535',
            'fields.*.precision' => 'nullable|integer|min:1|max:65',
            'fields.*.scale' => 'nullable|integer|min:0|max:30',
            'relationships' => 'nullable|array',
            'relationships.*.type' => 'required_with:relationships|string|in:belongsTo,hasMany',
            'relationships.*.table' => 'required_with:relationships|string',
            'relationships.*.method_name' => 'required_with:relationships|string',
            'relationships.*.foreign_key' => 'required_with:relationships|string',
            'business_logic' => 'nullable|string',
        ]);

        try {
            $module = GeneratedModule::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'icon' => $validated['icon'] ?? 'ki-element-11',
                'color' => $validated['color'] ?? 'primary',
                'has_crud' => $validated['has_crud'] ?? true,
                'has_permissions' => $validated['has_permissions'] ?? true,
                'fields_config' => $validated['fields'],
                'relationships' => $validated['relationships'] ?? null,
                'business_logic' => $validated['business_logic'] ?? null,
                'created_by' => auth()->id(),
                'status' => 'draft',
            ]);

            return redirect()->route('admin.module-generator.show', $module)
                ->with('success', 'Módulo configurado com sucesso! Agora você pode gerar o código.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao criar módulo: ' . $e->getMessage()]);
        }
    }

    public function show(GeneratedModule $generatedModule)
    {
        $generatedModule->load('creator');
        
        return view('admin.module-generator.show', compact('generatedModule'));
    }

    public function edit(GeneratedModule $generatedModule)
    {
        if ($generatedModule->isGenerated()) {
            return back()->withErrors(['error' => 'Não é possível editar um módulo já gerado.']);
        }

        $existingTables = $this->getExistingTables();
        
        return view('admin.module-generator.edit', compact('generatedModule', 'existingTables'));
    }

    public function update(Request $request, GeneratedModule $generatedModule)
    {
        if ($generatedModule->isGenerated()) {
            return back()->withErrors(['error' => 'Não é possível editar um módulo já gerado.']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:generated_modules,name,' . $generatedModule->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'has_crud' => 'boolean',
            'has_permissions' => 'boolean',
            'fields' => 'required|array|min:1',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.type' => 'required|string|in:string,text,integer,boolean,date,datetime,json,decimal',
            'fields.*.label' => 'nullable|string|max:255',
            'fields.*.nullable' => 'boolean',
            'fields.*.default' => 'nullable|string',
            'relationships' => 'nullable|array',
            'business_logic' => 'nullable|string',
        ]);

        $generatedModule->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'ki-element-11',
            'color' => $validated['color'] ?? 'primary',
            'has_crud' => $validated['has_crud'] ?? true,
            'has_permissions' => $validated['has_permissions'] ?? true,
            'fields_config' => $validated['fields'],
            'relationships' => $validated['relationships'] ?? null,
            'business_logic' => $validated['business_logic'] ?? null,
        ]);

        return redirect()->route('admin.module-generator.show', $generatedModule)
            ->with('success', 'Módulo atualizado com sucesso!');
    }

    public function generate(GeneratedModule $generatedModule)
    {
        if ($generatedModule->isGenerated()) {
            return back()->withErrors(['error' => 'Este módulo já foi gerado.']);
        }

        try {
            $result = $this->moduleGenerator->generateModule($generatedModule);

            if ($result['success']) {
                // Incluir o arquivo de rotas geradas no web.php se não estiver incluído
                $this->includeGeneratedRoutes();
                
                return redirect()->route('admin.module-generator.show', $generatedModule)
                    ->with('success', $result['message']);
            } else {
                return back()->withErrors(['error' => $result['message']]);
            }

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro inesperado: ' . $e->getMessage()]);
        }
    }

    public function destroy(GeneratedModule $generatedModule)
    {
        if ($generatedModule->isGenerated()) {
            return back()->withErrors(['error' => 'Não é possível excluir um módulo já gerado. Remova os arquivos manualmente primeiro.']);
        }

        $generatedModule->delete();

        return redirect()->route('admin.module-generator.index')
            ->with('success', 'Módulo excluído com sucesso!');
    }

    public function preview(GeneratedModule $generatedModule)
    {
        return view('admin.module-generator.preview', compact('generatedModule'));
    }

    private function getExistingTables(): array
    {
        $tables = DB::select("
            SELECT table_name 
            FROM information_schema.tables 
            WHERE table_schema = 'public' 
            AND table_type = 'BASE TABLE'
            AND table_name NOT LIKE 'generated_%'
            ORDER BY table_name
        ");

        return collect($tables)->pluck('table_name')->toArray();
    }

    private function includeGeneratedRoutes(): void
    {
        $webRoutesPath = base_path('routes/web.php');
        $webRoutesContent = file_get_contents($webRoutesPath);
        
        $includeStatement = "require_once __DIR__ . '/generated_modules.php';";
        
        if (strpos($webRoutesContent, $includeStatement) === false) {
            file_put_contents($webRoutesPath, $webRoutesContent . "\n\n// Generated modules routes\n" . $includeStatement . "\n");
        }
    }

    public function getTableStructure(Request $request)
    {
        $tableName = $request->input('table');
        
        if (!$tableName) {
            return response()->json(['error' => 'Nome da tabela é obrigatório'], 400);
        }

        try {
            $columns = DB::select("
                SELECT column_name, data_type, is_nullable, column_default
                FROM information_schema.columns 
                WHERE table_name = ? AND table_schema = 'public'
                ORDER BY ordinal_position
            ", [$tableName]);

            $structure = collect($columns)->map(function ($column) {
                return [
                    'name' => $column->column_name,
                    'type' => $this->mapPostgreSQLType($column->data_type),
                    'nullable' => $column->is_nullable === 'YES',
                    'default' => $column->column_default,
                ];
            });

            return response()->json([
                'table' => $tableName,
                'columns' => $structure
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar estrutura da tabela'], 500);
        }
    }

    private function mapPostgreSQLType(string $pgType): string
    {
        $mapping = [
            'character varying' => 'string',
            'varchar' => 'string',
            'text' => 'text',
            'integer' => 'integer',
            'bigint' => 'integer',
            'smallint' => 'integer',
            'boolean' => 'boolean',
            'date' => 'date',
            'timestamp without time zone' => 'datetime',
            'timestamp with time zone' => 'datetime',
            'json' => 'json',
            'jsonb' => 'json',
            'numeric' => 'decimal',
            'decimal' => 'decimal',
        ];

        return $mapping[$pgType] ?? 'string';
    }
}
