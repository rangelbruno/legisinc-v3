<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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
            'docker_services' => $this->checkDockerServices(),
            's3' => $this->checkS3Connection(),
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
            
            // Obter relacionamentos entre tabelas
            $relationships = $this->getDatabaseRelationships();
            
            return view('admin.system-diagnostic.database-simple', compact('databaseInfo', 'tables', 'relationships'));
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

    private function getDatabaseRelationships()
    {
        $driver = config('database.default');
        $relationships = [];

        try {
            if ($driver === 'pgsql') {
                $result = DB::select("
                    SELECT 
                        tc.constraint_name,
                        tc.table_name as from_table,
                        kcu.column_name as from_column,
                        ccu.table_name as to_table,
                        ccu.column_name as to_column
                    FROM information_schema.table_constraints AS tc
                    JOIN information_schema.key_column_usage AS kcu
                        ON tc.constraint_name = kcu.constraint_name
                        AND tc.table_schema = kcu.table_schema
                    JOIN information_schema.constraint_column_usage AS ccu
                        ON ccu.constraint_name = tc.constraint_name
                        AND ccu.table_schema = tc.table_schema
                    WHERE tc.constraint_type = 'FOREIGN KEY'
                        AND tc.table_schema = 'public'
                    ORDER BY tc.table_name, tc.constraint_name
                ");
                
                foreach ($result as $row) {
                    $relationships[] = [
                        'constraint_name' => $row->constraint_name,
                        'from_table' => $row->from_table,
                        'from_column' => $row->from_column,
                        'to_table' => $row->to_table,
                        'to_column' => $row->to_column
                    ];
                }
            } elseif ($driver === 'mysql') {
                $database = config('database.connections.mysql.database');
                $result = DB::select("
                    SELECT 
                        CONSTRAINT_NAME as constraint_name,
                        TABLE_NAME as from_table,
                        COLUMN_NAME as from_column,
                        REFERENCED_TABLE_NAME as to_table,
                        REFERENCED_COLUMN_NAME as to_column
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE REFERENCED_TABLE_SCHEMA = ?
                        AND REFERENCED_TABLE_NAME IS NOT NULL
                    ORDER BY TABLE_NAME, CONSTRAINT_NAME
                ", [$database]);
                
                foreach ($result as $row) {
                    $relationships[] = [
                        'constraint_name' => $row->constraint_name,
                        'from_table' => $row->from_table,
                        'from_column' => $row->from_column,
                        'to_table' => $row->to_table,
                        'to_column' => $row->to_column
                    ];
                }
            } elseif ($driver === 'sqlite') {
                // Para SQLite, precisamos fazer uma abordagem diferente
                $tables = $this->getSimpleTableList();
                foreach ($tables as $table) {
                    try {
                        $foreignKeys = DB::select("PRAGMA foreign_key_list({$table})");
                        foreach ($foreignKeys as $fk) {
                            $relationships[] = [
                                'constraint_name' => "fk_{$table}_{$fk->from}",
                                'from_table' => $table,
                                'from_column' => $fk->from,
                                'to_table' => $fk->table,
                                'to_column' => $fk->to
                            ];
                        }
                    } catch (\Exception $e) {
                        // Ignorar erro para esta tabela
                    }
                }
            }
        } catch (\Exception $e) {
            // Se falhar, retornar array vazio
            $relationships = [];
        }

        return $relationships;
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

    private function checkDockerServices()
    {
        try {
            // Since we're running inside a Docker container, we'll check services via HTTP health endpoints
            // instead of Docker commands which aren't available in the container
            $services = [
                'postgresql' => [
                    'name' => 'PostgreSQL Database',
                    'host' => 'db',
                    'port' => 5432,
                    'healthcheck_url' => null,
                    'check_type' => 'database'
                ],
                'redis' => [
                    'name' => 'Redis Cache',
                    'host' => 'redis',
                    'port' => 6379,
                    'healthcheck_url' => null,
                    'check_type' => 'redis'
                ],
                'laravel-app' => [
                    'name' => 'Laravel Application (Self)',
                    'host' => 'localhost',
                    'port' => 80,
                    'healthcheck_url' => 'http://localhost/health',
                    'check_type' => 'http'
                ],
                'nova-api' => [
                    'name' => 'Nova API',
                    'host' => 'nova-api',
                    'port' => 3001,
                    'healthcheck_url' => 'http://nova-api:3001/health',
                    'check_type' => 'http'
                ],
                'onlyoffice' => [
                    'name' => 'OnlyOffice Document Server',
                    'host' => 'onlyoffice-documentserver',
                    'port' => 80,
                    'healthcheck_url' => 'http://onlyoffice-documentserver:80/healthcheck',
                    'check_type' => 'http'
                ],
                'traefik' => [
                    'name' => 'Traefik Gateway',
                    'host' => 'traefik',
                    'port' => 8080,
                    'healthcheck_url' => 'http://traefik:8080/api/overview',
                    'check_type' => 'http'
                ],
                'nginx-shadow' => [
                    'name' => 'Nginx Shadow',
                    'host' => 'nginx-shadow',
                    'port' => 80,
                    'healthcheck_url' => 'http://nginx-shadow:80',
                    'check_type' => 'http'
                ],
                'shadow-comparator' => [
                    'name' => 'Shadow Comparator',
                    'host' => 'shadow-comparator',
                    'port' => 3002,
                    'healthcheck_url' => 'http://shadow-comparator:3002/health',
                    'check_type' => 'http'
                ],
                'canary-monitor' => [
                    'name' => 'Canary Monitor',
                    'host' => 'canary-monitor',
                    'port' => 3003,
                    'healthcheck_url' => 'http://canary-monitor:3003/health',
                    'check_type' => 'http'
                ],
                'prometheus' => [
                    'name' => 'Prometheus',
                    'host' => 'prometheus',
                    'port' => 9090,
                    'healthcheck_url' => 'http://prometheus:9090/-/healthy',
                    'check_type' => 'http'
                ],
                'grafana' => [
                    'name' => 'Grafana',
                    'host' => 'grafana',
                    'port' => 3000,
                    'healthcheck_url' => 'http://grafana:3000/api/health',
                    'check_type' => 'http'
                ],
                'postgres-exporter' => [
                    'name' => 'PostgreSQL Exporter',
                    'host' => 'postgres-exporter',
                    'port' => 9187,
                    'healthcheck_url' => 'http://postgres-exporter:9187/metrics',
                    'check_type' => 'http'
                ],
                'swagger-ui' => [
                    'name' => 'Swagger UI',
                    'host' => 'swagger-ui',
                    'port' => 8080,
                    'healthcheck_url' => 'http://swagger-ui:8080',
                    'check_type' => 'http'
                ],
                'mermaid-live-editor' => [
                    'name' => 'Mermaid Live Editor',
                    'host' => 'mermaid-live-editor',
                    'port' => 8080,
                    'healthcheck_url' => 'http://mermaid-live-editor:8080',
                    'check_type' => 'http'
                ]
            ];

            $results = [];
            $overallStatus = 'success';
            $healthyCount = 0;
            $totalCount = 0;

            foreach ($services as $serviceId => $config) {
                $totalCount++;
                $serviceResult = [
                    'name' => $config['name'],
                    'container_id' => $serviceId,
                    'running' => false,
                    'healthy' => false,
                    'status' => 'Checking...',
                    'uptime' => 'N/A',
                    'health_check' => 'N/A',
                    'error_message' => null,
                    'port' => $config['port'],
                    'has_healthcheck' => !empty($config['healthcheck_url'])
                ];

                // Check service based on type
                $isHealthy = false;
                $errorMessage = null;

                switch ($config['check_type']) {
                    case 'database':
                        try {
                            DB::connection()->getPdo();
                            $isHealthy = true;
                            $serviceResult['status'] = 'Connected';
                        } catch (\Exception $e) {
                            $errorMessage = 'Database connection failed: ' . $e->getMessage();
                        }
                        break;

                    case 'redis':
                        try {
                            Cache::put('health_check_test', 'test', 1);
                            $testValue = Cache::get('health_check_test');
                            if ($testValue === 'test') {
                                $isHealthy = true;
                                $serviceResult['status'] = 'Connected';
                                Cache::forget('health_check_test');
                            } else {
                                $errorMessage = 'Redis cache test failed';
                            }
                        } catch (\Exception $e) {
                            $errorMessage = 'Redis connection failed: ' . $e->getMessage();
                        }
                        break;

                    case 'http':
                        // First try basic port connectivity with shorter timeout
                        $connection = @fsockopen($config['host'], $config['port'], $errno, $errstr, 1);
                        if ($connection) {
                            fclose($connection);
                            $isHealthy = true;
                            $serviceResult['status'] = 'Port Open';
                            $serviceResult['health_check'] = 'Port Accessible';

                            // If port is accessible and we have a health URL, try that too
                            if (!empty($config['healthcheck_url'])) {
                                $healthResult = $this->checkHealthEndpointWithDetails($config['healthcheck_url'], 2);
                                if ($healthResult['status']) {
                                    $serviceResult['health_check'] = 'Healthy';
                                } else {
                                    $serviceResult['health_check'] = 'Port Open (Health check failed)';
                                }
                            }
                        } else {
                            $errorMessage = "Port {$config['port']} unreachable on {$config['host']} ($errstr)";
                        }
                        break;
                }

                if ($isHealthy) {
                    $serviceResult['running'] = true;
                    $serviceResult['healthy'] = true;
                    $healthyCount++;
                } else {
                    $serviceResult['error_message'] = $errorMessage;
                    $serviceResult['status'] = 'Error';
                    $overallStatus = 'error';
                }

                $results[$serviceId] = $serviceResult;
            }

            if ($healthyCount < $totalCount && $overallStatus === 'success') {
                $overallStatus = 'warning';
            }

            return [
                'status' => $overallStatus,
                'message' => "Container Services: {$healthyCount}/{$totalCount} healthy",
                'details' => [
                    'containers' => $results,
                    'additional_containers' => [],
                    'docker_available' => false, // We're not checking Docker directly
                    'total_containers' => $totalCount,
                    'healthy_containers' => $healthyCount,
                    'note' => 'Service health checked via network connectivity and health endpoints'
                ]
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error checking container services',
                'error' => $e->getMessage()
            ];
        }
    }

    private function checkHealthEndpoint($url, $timeout = 5)
    {
        $result = $this->checkHealthEndpointWithDetails($url, $timeout);
        return $result['status'];
    }

    private function checkHealthEndpointWithDetails($url, $timeout = 5)
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => $timeout,
                    'method' => 'GET',
                    'ignore_errors' => true
                ]
            ]);

            $response = @file_get_contents($url, false, $context);

            if ($response !== false && isset($http_response_header)) {
                $statusLine = $http_response_header[0];
                $isHealthy = strpos($statusLine, '200') !== false || strpos($statusLine, '204') !== false;

                return [
                    'status' => $isHealthy,
                    'error' => $isHealthy ? null : "HTTP error: $statusLine",
                    'response' => $response
                ];
            }

            return [
                'status' => false,
                'error' => 'No response received',
                'response' => null
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'error' => 'Exception: ' . $e->getMessage(),
                'response' => null
            ];
        }
    }

    private function checkS3Connection()
    {
        try {
            $awsAccessKey = env('AWS_ACCESS_KEY_ID');
            $awsSecretKey = env('AWS_SECRET_ACCESS_KEY');
            $awsRegion = env('AWS_DEFAULT_REGION', 'us-east-1');
            $awsBucket = env('AWS_BUCKET');

            $configDetails = [
                'access_key_configured' => !empty($awsAccessKey),
                'secret_key_configured' => !empty($awsSecretKey),
                'region' => $awsRegion,
                'bucket' => $awsBucket ?: 'Not configured',
                's3_disk_configured' => config('filesystems.disks.s3') !== null
            ];

            // Basic configuration check
            if (empty($awsAccessKey) || empty($awsSecretKey)) {
                return [
                    'status' => 'warning',
                    'message' => 'S3 credentials not configured',
                    'details' => $configDetails
                ];
            }

            // Try to use Laravel's Storage facade to test S3 connection
            if (!empty($awsBucket)) {
                try {
                    // Test connection by attempting to list objects (limited)
                    $s3Disk = Storage::disk('s3');

                    // Try to check if the disk is properly configured by testing a simple operation
                    $testKey = 'diagnostic-test-' . time() . '.txt';
                    $testContent = 'Diagnostic test content';

                    // Try to put and then delete a test file
                    $putResult = $s3Disk->put($testKey, $testContent);

                    if ($putResult) {
                        $exists = $s3Disk->exists($testKey);
                        $s3Disk->delete($testKey); // Clean up

                        if ($exists) {
                            $configDetails['connection_test'] = 'Success';
                            $configDetails['last_test'] = now()->format('Y-m-d H:i:s');

                            return [
                                'status' => 'success',
                                'message' => 'S3 connection working correctly',
                                'details' => $configDetails
                            ];
                        }
                    }

                    $configDetails['connection_test'] = 'Failed to write/read test file';

                } catch (\Exception $storageException) {
                    $configDetails['connection_test'] = 'Error: ' . $storageException->getMessage();
                    $configDetails['connection_error'] = substr($storageException->getMessage(), 0, 200);
                }
            }

            return [
                'status' => 'error',
                'message' => 'S3 connection failed',
                'details' => $configDetails
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error checking S3 connection',
                'error' => $e->getMessage(),
                'details' => [
                    'exception_type' => get_class($e),
                    'error_message' => $e->getMessage()
                ]
            ];
        }
    }
}