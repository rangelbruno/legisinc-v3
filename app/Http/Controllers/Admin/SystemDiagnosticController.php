<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\File;

class SystemDiagnosticController extends Controller
{
    public function index()
    {
        $diagnostics = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'redis' => $this->checkRedis(),
            'storage' => $this->checkStorage(),
            'permissions' => $this->checkPermissions(),
            'containers' => $this->checkContainers(),
        ];

        return view('admin.system-diagnostic.index', compact('diagnostics'));
    }

    public function database()
    {
        try {
            $driver = config('database.default');
            $connection = config("database.connections.{$driver}");
            
            // Obter informações básicas do banco
            $databaseInfo = [
                'driver' => $driver,
                'host' => $connection['host'] ?? 'N/A',
                'database' => $connection['database'] ?? 'N/A',
                'port' => $connection['port'] ?? 'padrão',
                'charset' => $connection['charset'] ?? 'N/A'
            ];

            // Obter lista de tabelas com informações detalhadas
            $tables = $this->getDatabaseTables();
            
            return view('admin.system-diagnostic.database', compact('databaseInfo', 'tables'));
        } catch (\Exception $e) {
            return redirect()->route('admin.system-diagnostic.index')
                ->with('error', 'Erro ao carregar informações do banco de dados: ' . $e->getMessage());
        }
    }

    public function tableRecords($table)
    {
        try {
            $driver = config('database.default');
            $connection = config("database.connections.{$driver}");
            
            // Validar se a tabela existe
            $tableExists = $this->checkTableExists($table);
            if (!$tableExists) {
                return redirect()->route('admin.system-diagnostic.database')
                    ->with('error', "Tabela '{$table}' não encontrada.");
            }

            // Obter informações da tabela
            $tableInfo = $this->getTableInfo($table);
            
            // Obter estrutura da tabela (colunas)
            $columns = $this->getTableColumns($table);
            
            // Obter registros com paginação
            $page = request()->get('page', 1);
            $perPage = 50; // Registros por página
            $offset = ($page - 1) * $perPage;
            
            $records = $this->getTableRecords($table, $perPage, $offset);
            $totalRecords = $this->getTableRecordCount($table);
            
            // Calcular informações de paginação
            $totalPages = ceil($totalRecords / $perPage);
            $hasNext = $page < $totalPages;
            $hasPrev = $page > 1;
            
            return view('admin.system-diagnostic.table', compact(
                'table', 
                'tableInfo', 
                'columns', 
                'records', 
                'totalRecords',
                'page',
                'totalPages',
                'hasNext',
                'hasPrev',
                'perPage'
            ));
        } catch (\Exception $e) {
            return redirect()->route('admin.system-diagnostic.database')
                ->with('error', 'Erro ao carregar registros da tabela: ' . $e->getMessage());
        }
    }

    private function checkTableExists($table)
    {
        $driver = config('database.default');
        
        try {
            if ($driver === 'pgsql') {
                $result = DB::select("SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_schema = 'public' AND table_name = ?)", [$table]);
                return $result[0]->exists ?? false;
            } elseif ($driver === 'mysql') {
                $result = DB::select("SHOW TABLES LIKE ?", [$table]);
                return count($result) > 0;
            } elseif ($driver === 'sqlite') {
                $result = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name = ?", [$table]);
                return count($result) > 0;
            }
        } catch (\Exception $e) {
            return false;
        }
        
        return false;
    }

    private function getTableInfo($table)
    {
        $driver = config('database.default');
        $info = [
            'name' => $table,
            'driver' => $driver,
            'size' => 'N/A',
            'rows' => 0
        ];

        try {
            // Obter contagem de registros
            $info['rows'] = DB::table($table)->count();
            
            // Obter tamanho (quando possível)
            if ($driver === 'pgsql') {
                $sizeResult = DB::select("SELECT pg_size_pretty(pg_total_relation_size('public.{$table}')) as size");
                $info['size'] = $sizeResult[0]->size ?? 'N/A';
            } elseif ($driver === 'mysql') {
                $sizeResult = DB::select("SELECT ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb FROM information_schema.TABLES WHERE table_schema = DATABASE() AND table_name = ?", [$table]);
                $info['size'] = isset($sizeResult[0]->size_mb) ? $sizeResult[0]->size_mb . ' MB' : 'N/A';
            }
        } catch (\Exception $e) {
            // Manter valores padrão
        }

        return $info;
    }

    private function getTableColumns($table)
    {
        $driver = config('database.default');
        $columns = [];

        try {
            if ($driver === 'pgsql') {
                $result = DB::select("
                    SELECT 
                        column_name,
                        data_type,
                        is_nullable,
                        column_default,
                        character_maximum_length
                    FROM information_schema.columns 
                    WHERE table_schema = 'public' AND table_name = ? 
                    ORDER BY ordinal_position
                ", [$table]);
                
                foreach ($result as $column) {
                    $columns[] = [
                        'name' => $column->column_name,
                        'type' => $column->data_type,
                        'nullable' => $column->is_nullable === 'YES',
                        'default' => $column->column_default,
                        'length' => $column->character_maximum_length
                    ];
                }
            } elseif ($driver === 'mysql') {
                $result = DB::select("DESCRIBE {$table}");
                
                foreach ($result as $column) {
                    $columns[] = [
                        'name' => $column->Field,
                        'type' => $column->Type,
                        'nullable' => $column->Null === 'YES',
                        'default' => $column->Default,
                        'key' => $column->Key
                    ];
                }
            } elseif ($driver === 'sqlite') {
                $result = DB::select("PRAGMA table_info({$table})");
                
                foreach ($result as $column) {
                    $columns[] = [
                        'name' => $column->name,
                        'type' => $column->type,
                        'nullable' => !$column->notnull,
                        'default' => $column->dflt_value,
                        'pk' => $column->pk
                    ];
                }
            }
        } catch (\Exception $e) {
            // Se falhar, tentar obter pelo menos os nomes das colunas de uma consulta simples
            try {
                $sampleRecord = DB::table($table)->first();
                if ($sampleRecord) {
                    foreach ((array)$sampleRecord as $columnName => $value) {
                        $columns[] = [
                            'name' => $columnName,
                            'type' => 'unknown',
                            'nullable' => true,
                            'default' => null
                        ];
                    }
                }
            } catch (\Exception $e2) {
                // Não foi possível obter informações das colunas
            }
        }

        return $columns;
    }

    private function getTableRecords($table, $limit, $offset)
    {
        try {
            return DB::table($table)
                ->offset($offset)
                ->limit($limit)
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getTableRecordCount($table)
    {
        try {
            return DB::table($table)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getDatabaseTables()
    {
        $driver = config('database.default');
        $tables = [];

        try {
            if ($driver === 'mysql') {
                $rawTables = DB::select('SHOW TABLE STATUS');
                foreach ($rawTables as $table) {
                    $tables[] = [
                        'name' => $table->Name,
                        'rows' => $table->Rows ?? 0,
                        'size' => isset($table->Data_length) ? $this->formatBytes($table->Data_length) : 'N/A',
                        'engine' => $table->Engine ?? 'N/A',
                        'created' => $table->Create_time ?? 'N/A'
                    ];
                }
            } elseif ($driver === 'pgsql') {
                // Primeiro, obter lista de tabelas
                $tableNames = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public' ORDER BY tablename");
                
                foreach ($tableNames as $tableInfo) {
                    $tableName = $tableInfo->tablename;
                    
                    $rowCount = 'N/A';
                    $sizeFormatted = 'N/A';
                    $comment = null;
                    
                    try {
                        // Tentar obter contagem de registros
                        $rowCount = DB::table($tableName)->count();
                    } catch (\Exception $e) {
                        // Se falhar na contagem, tentar uma abordagem mais simples
                        try {
                            $result = DB::select("SELECT COUNT(*) as total FROM \"{$tableName}\"");
                            $rowCount = $result[0]->total ?? 'Erro';
                        } catch (\Exception $e2) {
                            $rowCount = 'Sem acesso';
                        }
                    }
                    
                    try {
                        // Tentar obter tamanho da tabela usando nome qualificado
                        $sizeResult = DB::select("SELECT pg_total_relation_size('public.{$tableName}') as size_bytes");
                        $sizeBytes = $sizeResult[0]->size_bytes ?? null;
                        if ($sizeBytes !== null) {
                            $sizeFormatted = $this->formatBytes($sizeBytes);
                        }
                    } catch (\Exception $e) {
                        // Ignorar erro de tamanho, manter N/A
                    }
                    
                    try {
                        // Tentar obter informações básicas da tabela
                        $tableDetails = DB::select("
                            SELECT 
                                c.relname as table_name,
                                pg_size_pretty(pg_total_relation_size(c.oid)) as size_pretty,
                                c.reltuples::bigint as estimated_rows
                            FROM pg_class c 
                            JOIN pg_namespace n ON n.oid = c.relnamespace 
                            WHERE c.relname = ? AND n.nspname = 'public' AND c.relkind = 'r'
                        ", [$tableName]);
                        
                        if (!empty($tableDetails)) {
                            $detail = $tableDetails[0];
                            if ($sizeFormatted === 'N/A' && isset($detail->size_pretty)) {
                                $sizeFormatted = $detail->size_pretty;
                            }
                            if ($rowCount === 'N/A' && isset($detail->estimated_rows)) {
                                $rowCount = (int)$detail->estimated_rows;
                            }
                        }
                    } catch (\Exception $e) {
                        // Ignorar erro de detalhes
                    }
                    
                    $tables[] = [
                        'name' => $tableName,
                        'rows' => $rowCount,
                        'size' => $sizeFormatted,
                        'engine' => 'PostgreSQL',
                        'created' => 'N/A'
                    ];
                }
            } elseif ($driver === 'sqlite') {
                $rawTables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                foreach ($rawTables as $table) {
                    // Para SQLite, informações detalhadas são limitadas
                    $rowCount = DB::table($table->name)->count();
                    $tables[] = [
                        'name' => $table->name,
                        'rows' => $rowCount,
                        'size' => 'N/A',
                        'engine' => 'SQLite',
                        'created' => 'N/A'
                    ];
                }
            }
        } catch (\Exception $e) {
            // Se falhar, usar método alternativo mais simples
            $tables = $this->getTablesWithBasicInfo();
        }

        return $tables;
    }

    private function getTablesWithBasicInfo()
    {
        $driver = config('database.default');
        $tables = [];
        
        try {
            $tableNames = $this->getSimpleTableList();
            
            foreach ($tableNames as $tableName) {
                $rowCount = 'N/A';
                
                // Tentar pelo menos obter contagem de registros de forma mais simples
                try {
                    if ($driver === 'pgsql') {
                        // Para PostgreSQL, tentar contagem direta
                        $result = DB::select("SELECT COUNT(*) as total FROM \"{$tableName}\"");
                        $rowCount = $result[0]->total ?? 'Erro';
                    } else {
                        $rowCount = DB::table($tableName)->count();
                    }
                } catch (\Exception $e) {
                    $rowCount = 'Sem permissão';
                }
                
                $tables[] = [
                    'name' => $tableName,
                    'rows' => $rowCount,
                    'size' => 'Não disponível',
                    'engine' => ucfirst($driver),
                    'created' => 'N/A'
                ];
            }
        } catch (\Exception $e) {
            // Último recurso: apenas nomes
            $tableNames = $this->getSimpleTableList();
            foreach ($tableNames as $tableName) {
                $tables[] = [
                    'name' => $tableName,
                    'rows' => 'Sem acesso',
                    'size' => 'N/A',
                    'engine' => 'N/A',
                    'created' => 'N/A'
                ];
            }
        }
        
        return $tables;
    }

    private function getSimpleTableList()
    {
        $driver = config('database.default');
        $tables = [];

        if ($driver === 'mysql') {
            $rawTables = DB::select('SHOW TABLES');
            foreach ($rawTables as $table) {
                $tables[] = array_values((array)$table)[0];
            }
        } elseif ($driver === 'pgsql') {
            $rawTables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
            foreach ($rawTables as $table) {
                $tables[] = $table->tablename;
            }
        } elseif ($driver === 'sqlite') {
            $rawTables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            foreach ($rawTables as $table) {
                $tables[] = $table->name;
            }
        }

        return $tables;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            
            // Detectar o driver do banco de dados
            $driver = config('database.default');
            $connection = config("database.connections.{$driver}");
            
            // Comando para listar tabelas baseado no driver
            $tables = [];
            if ($driver === 'mysql') {
                $tables = DB::select('SHOW TABLES');
            } elseif ($driver === 'pgsql') {
                $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
            } elseif ($driver === 'sqlite') {
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
            } else {
                // Para outros drivers, tentar uma consulta genérica
                $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
            }
            
            return [
                'status' => 'success',
                'message' => 'Conexão com banco de dados OK',
                'details' => [
                    'driver' => $driver,
                    'host' => $connection['host'] ?? 'N/A',
                    'database' => $connection['database'] ?? 'N/A',
                    'port' => $connection['port'] ?? 'padrão',
                    'tables_count' => count($tables),
                    'charset' => $connection['charset'] ?? 'N/A'
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro na conexão com banco de dados',
                'error' => $e->getMessage()
            ];
        }
    }

    private function checkCache()
    {
        try {
            $cacheDriver = config('cache.default');
            $testKey = 'diagnostic_test_' . time();
            
            Cache::put($testKey, 'test_value', 60);
            $value = Cache::get($testKey);
            Cache::forget($testKey);
            
            $cacheDir = storage_path('framework/cache');
            $permissions = substr(sprintf('%o', fileperms($cacheDir)), -4);
            
            return [
                'status' => $value === 'test_value' ? 'success' : 'warning',
                'message' => $value === 'test_value' ? 'Cache funcionando corretamente' : 'Cache com problemas',
                'details' => [
                    'driver' => $cacheDriver,
                    'cache_dir' => $cacheDir,
                    'permissions' => $permissions,
                    'writable' => is_writable($cacheDir) ? 'Sim' : 'Não'
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao verificar cache',
                'error' => $e->getMessage()
            ];
        }
    }

    private function checkRedis()
    {
        try {
            $cacheDriver = config('cache.default');
            $sessionDriver = config('session.driver');
            $queueDriver = config('queue.default');
            
            $usingRedis = in_array('redis', [$cacheDriver, $sessionDriver, $queueDriver]);
            
            if ($usingRedis) {
                $redis = Redis::connection();
                $redis->ping();
                
                $redisConfig = config('database.redis.default');
                
                return [
                    'status' => 'success',
                    'message' => 'Redis conectado',
                    'details' => [
                        'host' => $redisConfig['host'] ?? 'localhost',
                        'port' => $redisConfig['port'] ?? 6379,
                        'used_by_cache' => $cacheDriver === 'redis' ? 'Sim' : 'Não',
                        'used_by_session' => $sessionDriver === 'redis' ? 'Sim' : 'Não',
                        'used_by_queue' => $queueDriver === 'redis' ? 'Sim' : 'Não'
                    ]
                ];
            }
            
            return [
                'status' => 'info',
                'message' => 'Redis não está sendo usado',
                'details' => [
                    'cache_driver' => $cacheDriver,
                    'session_driver' => $sessionDriver,
                    'queue_driver' => $queueDriver
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro na conexão com Redis',
                'error' => $e->getMessage()
            ];
        }
    }

    private function checkStorage()
    {
        $directories = [
            'storage/app' => storage_path('app'),
            'storage/framework' => storage_path('framework'),
            'storage/framework/cache' => storage_path('framework/cache'),
            'storage/framework/cache/data' => storage_path('framework/cache/data'),
            'storage/logs' => storage_path('logs'),
            'bootstrap/cache' => base_path('bootstrap/cache')
        ];

        $results = [];
        
        foreach ($directories as $name => $path) {
            if (File::exists($path)) {
                $permissions = substr(sprintf('%o', fileperms($path)), -4);
                $owner = posix_getpwuid(fileowner($path))['name'] ?? 'unknown';
                $group = posix_getgrgid(filegroup($path))['name'] ?? 'unknown';
                
                $results[$name] = [
                    'exists' => true,
                    'writable' => is_writable($path),
                    'permissions' => $permissions,
                    'owner' => $owner . ':' . $group
                ];
            } else {
                $results[$name] = [
                    'exists' => false,
                    'writable' => false,
                    'permissions' => 'N/A',
                    'owner' => 'N/A'
                ];
            }
        }

        $hasErrors = collect($results)->contains(function ($dir) {
            return !$dir['exists'] || !$dir['writable'];
        });

        return [
            'status' => $hasErrors ? 'error' : 'success',
            'message' => $hasErrors ? 'Problemas de permissão detectados' : 'Permissões OK',
            'details' => $results
        ];
    }

    private function checkPermissions()
    {
        try {
            // Tenta criar e deletar um arquivo de teste no cache
            $testFile = storage_path('framework/cache/data/permission_test_' . time());
            
            if (@file_put_contents($testFile, 'test')) {
                @unlink($testFile);
                $canWrite = true;
            } else {
                $canWrite = false;
            }

            // Informações do usuário do processo
            $user = posix_getpwuid(posix_geteuid());
            
            return [
                'status' => $canWrite ? 'success' : 'error',
                'message' => $canWrite ? 'Permissões de escrita OK' : 'Sem permissão de escrita no cache',
                'details' => [
                    'process_user' => $user['name'] ?? 'unknown',
                    'process_uid' => posix_geteuid(),
                    'can_write_cache' => $canWrite ? 'Sim' : 'Não'
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao verificar permissões',
                'error' => $e->getMessage()
            ];
        }
    }

    private function checkContainers()
    {
        try {
            // Verifica se estamos rodando em container
            $isDocker = file_exists('/.dockerenv');
            
            $details = [
                'running_in_docker' => $isDocker ? 'Sim' : 'Não',
                'hostname' => gethostname(),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version()
            ];

            // Tenta verificar conexão com outros containers
            if ($isDocker) {
                // Verifica se consegue resolver nomes de outros containers
                $mysqlHost = config('database.connections.mysql.host');
                $mysqlIp = gethostbyname($mysqlHost);
                
                $details['mysql_host'] = $mysqlHost;
                $details['mysql_resolved_ip'] = $mysqlIp !== $mysqlHost ? $mysqlIp : 'Não resolvido';
            }

            return [
                'status' => 'success',
                'message' => 'Informações do ambiente',
                'details' => $details
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao verificar containers',
                'error' => $e->getMessage()
            ];
        }
    }

    public function fixPermissions()
    {
        try {
            $commands = [
                'chown -R www-data:www-data /var/www/html/storage',
                'chmod -R 775 /var/www/html/storage',
                'chmod -R 775 /var/www/html/bootstrap/cache',
                'php artisan cache:clear',
                'php artisan config:clear',
                'php artisan view:clear'
            ];

            $results = [];
            
            foreach ($commands as $command) {
                exec($command . ' 2>&1', $output, $returnCode);
                $results[] = [
                    'command' => $command,
                    'success' => $returnCode === 0,
                    'output' => implode("\n", $output)
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Tentativa de correção executada',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao executar correções',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}