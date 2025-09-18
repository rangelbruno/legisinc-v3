<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

class MigrationPreparationController extends Controller
{
    /**
     * Exibe a tela de preparação para migração de backend
     */
    public function index()
    {
        // Verificar se o usuário é admin
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores podem acessar esta página.');
        }

        // Dados iniciais para a view
        $data = [
            'endpoints_count' => $this->countEndpoints(),
            'models_count' => $this->countModels(),
            'tables_count' => $this->countTables(),
            'migrations_count' => $this->countMigrations(),
        ];

        return view('admin.migration-preparation.index', compact('data'));
    }

    /**
     * Gera o JSON com todos os endpoints do sistema
     */
    public function generateEndpointsJson()
    {
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $routes = collect(Route::getRoutes())->map(function ($route) {
                return [
                    'method' => implode('|', $route->methods()),
                    'uri' => $route->uri(),
                    'name' => $route->getName(),
                    'action' => $route->getActionName(),
                    'middleware' => $route->middleware(),
                ];
            })->filter(function ($route) {
                // Filtrar apenas rotas relevantes (não de sistema/debug)
                return !str_contains($route['uri'], '_ignition')
                    && !str_contains($route['uri'], 'livewire')
                    && !str_contains($route['uri'], '_debugbar')
                    && !str_contains($route['action'], 'Closure');
            })->groupBy('method')->toArray();

            return response()->json([
                'success' => true,
                'data' => $routes,
                'total_routes' => collect($routes)->flatten(1)->count(),
                'generated_at' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao gerar JSON de endpoints: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gera o JSON com estruturas de banco (tabelas e campos)
     */
    public function generateDatabaseStructureJson()
    {
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $tables = [];
            $tableNames = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");

            foreach ($tableNames as $table) {
                $tableName = $table->tablename;

                // Pular tabelas de sistema
                if (in_array($tableName, ['migrations', 'password_resets', 'failed_jobs', 'personal_access_tokens'])) {
                    continue;
                }

                $columns = DB::select("
                    SELECT
                        column_name,
                        data_type,
                        is_nullable,
                        column_default,
                        character_maximum_length,
                        numeric_precision,
                        numeric_scale
                    FROM information_schema.columns
                    WHERE table_name = ?
                    ORDER BY ordinal_position
                ", [$tableName]);

                $indexes = DB::select("
                    SELECT
                        indexname,
                        indexdef
                    FROM pg_indexes
                    WHERE tablename = ?
                ", [$tableName]);

                $foreignKeys = DB::select("
                    SELECT
                        tc.constraint_name,
                        kcu.column_name,
                        ccu.table_name AS foreign_table_name,
                        ccu.column_name AS foreign_column_name
                    FROM information_schema.table_constraints AS tc
                    JOIN information_schema.key_column_usage AS kcu
                        ON tc.constraint_name = kcu.constraint_name
                        AND tc.table_schema = kcu.table_schema
                    JOIN information_schema.constraint_column_usage AS ccu
                        ON ccu.constraint_name = tc.constraint_name
                        AND ccu.table_schema = tc.table_schema
                    WHERE tc.constraint_type = 'FOREIGN KEY'
                        AND tc.table_name = ?
                ", [$tableName]);

                $tables[$tableName] = [
                    'columns' => $columns,
                    'indexes' => $indexes,
                    'foreign_keys' => $foreignKeys,
                    'estimated_rows' => $this->getTableRowCount($tableName)
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $tables,
                'total_tables' => count($tables),
                'generated_at' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao gerar JSON de estrutura do banco: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gera o JSON com modelos e regras de negócio
     */
    public function generateModelsJson()
    {
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $models = [];
            $modelsPath = app_path('Models');

            if (File::exists($modelsPath)) {
                $files = File::allFiles($modelsPath);

                foreach ($files as $file) {
                    if ($file->getExtension() === 'php') {
                        $className = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                        $fullClassName = "App\\Models\\{$className}";

                        if (class_exists($fullClassName)) {
                            $reflection = new \ReflectionClass($fullClassName);

                            // Ler o conteúdo do arquivo para extrair informações
                            $content = File::get($file->getPathname());

                            $models[$className] = [
                                'file_path' => $file->getPathname(),
                                'table_name' => $this->extractTableName($content),
                                'fillable' => $this->extractProperty($content, 'fillable'),
                                'hidden' => $this->extractProperty($content, 'hidden'),
                                'casts' => $this->extractProperty($content, 'casts'),
                                'relationships' => $this->extractRelationships($content),
                                'methods' => $this->extractPublicMethods($reflection),
                                'traits' => $this->extractTraits($reflection),
                            ];
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $models,
                'total_models' => count($models),
                'generated_at' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao gerar JSON de modelos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gera o JSON com integrações externas
     */
    public function generateIntegrationsJson()
    {
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $integrations = [
                'external_apis' => $this->findExternalApiCalls(),
                'queues' => $this->findQueueJobs(),
                'scheduled_tasks' => $this->findScheduledTasks(),
                'config_dependencies' => $this->findConfigDependencies(),
                'environment_variables' => $this->findEnvironmentVariables(),
            ];

            return response()->json([
                'success' => true,
                'data' => $integrations,
                'generated_at' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao gerar JSON de integrações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gera um JSON completo com todas as informações para migração
     */
    public function generateCompleteJson()
    {
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $endpointsResponse = $this->generateEndpointsJson();
            $databaseResponse = $this->generateDatabaseStructureJson();
            $modelsResponse = $this->generateModelsJson();
            $integrationsResponse = $this->generateIntegrationsJson();

            $completeData = [
                'project_info' => [
                    'name' => 'LegisInc V2',
                    'framework' => 'Laravel ' . app()->version(),
                    'php_version' => PHP_VERSION,
                    'generated_at' => now()->toISOString(),
                    'generated_by' => auth()->user()->name,
                ],
                'endpoints' => json_decode($endpointsResponse->getContent(), true)['data'],
                'database_structure' => json_decode($databaseResponse->getContent(), true)['data'],
                'models' => json_decode($modelsResponse->getContent(), true)['data'],
                'integrations' => json_decode($integrationsResponse->getContent(), true)['data'],
            ];

            return response()->json([
                'success' => true,
                'data' => $completeData,
                'generated_at' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao gerar JSON completo: ' . $e->getMessage()
            ], 500);
        }
    }

    // Métodos auxiliares privados

    private function countEndpoints()
    {
        return collect(Route::getRoutes())->filter(function ($route) {
            return !str_contains($route->uri(), '_ignition')
                && !str_contains($route->uri(), 'livewire')
                && !str_contains($route->uri(), '_debugbar');
        })->count();
    }

    private function countModels()
    {
        $modelsPath = app_path('Models');
        if (!File::exists($modelsPath)) {
            return 0;
        }

        return count(File::allFiles($modelsPath));
    }

    private function countTables()
    {
        try {
            $result = DB::select("SELECT COUNT(*) as count FROM pg_tables WHERE schemaname = 'public'");
            return $result[0]->count ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function countMigrations()
    {
        $migrationsPath = database_path('migrations');
        if (!File::exists($migrationsPath)) {
            return 0;
        }

        return count(File::allFiles($migrationsPath));
    }

    private function getTableRowCount($tableName)
    {
        try {
            $result = DB::select("SELECT COUNT(*) as count FROM {$tableName}");
            return $result[0]->count ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function extractTableName($content)
    {
        if (preg_match('/protected\s+\$table\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function extractProperty($content, $property)
    {
        $pattern = "/protected\s+\\\${$property}\s*=\s*(\[.*?\]);/s";
        if (preg_match($pattern, $content, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function extractRelationships($content)
    {
        $relationships = [];
        $relationshipTypes = ['hasOne', 'hasMany', 'belongsTo', 'belongsToMany', 'morphTo', 'morphOne', 'morphMany'];

        foreach ($relationshipTypes as $type) {
            if (preg_match_all("/public\s+function\s+(\w+)\s*\([^)]*\)\s*{\s*return\s+\\\$this->{$type}/", $content, $matches)) {
                foreach ($matches[1] as $method) {
                    $relationships[] = [
                        'method' => $method,
                        'type' => $type
                    ];
                }
            }
        }

        return $relationships;
    }

    private function extractPublicMethods(\ReflectionClass $reflection)
    {
        $methods = [];
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (!$method->isConstructor() && !$method->isDestructor() && $method->class === $reflection->getName()) {
                $methods[] = [
                    'name' => $method->getName(),
                    'parameters' => array_map(function ($param) {
                        return $param->getName();
                    }, $method->getParameters())
                ];
            }
        }
        return $methods;
    }

    private function extractTraits(\ReflectionClass $reflection)
    {
        return array_keys($reflection->getTraits());
    }

    private function findExternalApiCalls()
    {
        // Buscar por chamadas HTTP, cURL, Guzzle, etc.
        $patterns = [
            'Http::' => 'Laravel HTTP Client',
            'curl_' => 'cURL functions',
            'file_get_contents' => 'file_get_contents',
            'GuzzleHttp' => 'Guzzle HTTP'
        ];

        return $this->searchInFiles($patterns, [app_path(), config_path()]);
    }

    private function findQueueJobs()
    {
        // Buscar por jobs de fila
        $jobsPath = app_path('Jobs');
        $jobs = [];

        if (File::exists($jobsPath)) {
            $files = File::allFiles($jobsPath);
            foreach ($files as $file) {
                if ($file->getExtension() === 'php') {
                    $jobs[] = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                }
            }
        }

        return $jobs;
    }

    private function findScheduledTasks()
    {
        // Buscar no arquivo de console/Kernel.php por tarefas agendadas
        $kernelPath = app_path('Console/Kernel.php');
        $tasks = [];

        if (File::exists($kernelPath)) {
            $content = File::get($kernelPath);
            if (preg_match_all('/\$schedule->(\w+)\([^)]*\)/', $content, $matches)) {
                $tasks = array_unique($matches[1]);
            }
        }

        return $tasks;
    }

    private function findConfigDependencies()
    {
        // Buscar configurações importantes
        $configs = [];
        $configFiles = File::allFiles(config_path());

        foreach ($configFiles as $file) {
            if ($file->getExtension() === 'php') {
                $configName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $configs[$configName] = $file->getPathname();
            }
        }

        return $configs;
    }

    private function findEnvironmentVariables()
    {
        $envPath = base_path('.env');
        $variables = [];

        if (File::exists($envPath)) {
            $content = File::get($envPath);
            $lines = explode("\n", $content);

            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line) && !str_starts_with($line, '#') && str_contains($line, '=')) {
                    list($key, $value) = explode('=', $line, 2);
                    $variables[trim($key)] = trim($value);
                }
            }
        }

        return $variables;
    }

    private function searchInFiles($patterns, $directories)
    {
        $results = [];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) continue;

            $files = File::allFiles($directory);
            foreach ($files as $file) {
                if ($file->getExtension() === 'php') {
                    $content = File::get($file->getPathname());

                    foreach ($patterns as $pattern => $description) {
                        if (str_contains($content, $pattern)) {
                            $results[] = [
                                'file' => $file->getPathname(),
                                'pattern' => $pattern,
                                'description' => $description
                            ];
                        }
                    }
                }
            }
        }

        return $results;
    }
}