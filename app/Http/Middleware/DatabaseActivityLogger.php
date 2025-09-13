<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DatabaseActivityLogger
{
    private $startTime;
    private $initialQueryCount;
    private $queryLog = [];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ignorar rotas de monitoramento para evitar recursão
        if ($this->shouldIgnoreRequest($request)) {
            return $next($request);
        }

        // Inicializar contadores
        $this->startTime = microtime(true);
        $this->initialQueryCount = count(DB::getQueryLog());

        // Ativar query log se necessário
        if (!DB::logging()) {
            DB::enableQueryLog();
        }

        // Registrar listener para capturar queries
        DB::listen(function ($query) use ($request) {
            $this->queryLog[] = [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
                'connection' => $query->connectionName
            ];
        });

        $response = $next($request);

        // Processar e salvar atividades após a resposta
        $this->logDatabaseActivities($request, $response);

        return $response;
    }

    /**
     * Processa e salva as atividades do banco de dados
     */
    private function logDatabaseActivities(Request $request, Response $response)
    {
        try {
            $executionTime = (microtime(true) - $this->startTime) * 1000; // em ms

            foreach ($this->queryLog as $query) {
                $this->analyzeAndLogQuery($query, $request, $executionTime);
            }
        } catch (\Exception $e) {
            // Log do erro sem interromper a aplicação
            Log::error('Erro no DatabaseActivityLogger: ' . $e->getMessage());
        }
    }

    /**
     * Analisa e registra uma query específica
     */
    private function analyzeAndLogQuery(array $query, Request $request, float $requestTime)
    {
        try {
            $sql = strtoupper(trim($query['sql']));

            // Detectar tipo de operação
            $operationType = $this->detectOperationType($sql);

            // Detectar tabela afetada
            $tableName = $this->extractTableName($sql, $operationType);

            // Calcular linhas afetadas (aproximado)
            $affectedRows = $this->estimateAffectedRows($sql, $query['bindings']);

            // Só registrar operações relevantes (evitar queries de sistema)
            if ($this->shouldLogQuery($tableName, $operationType)) {
                // Extrair valores das mudanças para armazenar no log principal
                $changeDetails = null;
                if (in_array($operationType, ['INSERT', 'UPDATE', 'DELETE'])) {
                    $changeDetails = $this->extractQueryDetails($sql, $query['bindings'], $tableName, $operationType);
                }

                // Log básico de atividade com detalhes das mudanças
                $activityId = $this->insertActivityLog([
                    'table_name' => $tableName,
                    'operation_type' => $operationType,
                    'query_time_ms' => round($query['time'], 2),
                    'affected_rows' => $affectedRows,
                    'request_method' => $request->method(),
                    'endpoint' => $request->path(),
                    'user_id' => auth()->id(),
                    'ip_address' => $request->ip(),
                    'sql_hash' => hash('sha256', $sql),
                    'change_details' => $changeDetails ? json_encode($changeDetails) : null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Log detalhado de mudanças de colunas (INSERT/UPDATE apenas)
                if (in_array($operationType, ['INSERT', 'UPDATE']) && $this->shouldLogColumnChanges($tableName)) {
                    $this->logColumnChanges($query, $tableName, $operationType, $request);
                }
            }
        } catch (\Exception $e) {
            Log::error('Erro ao analisar query: ' . $e->getMessage());
        }
    }

    /**
     * Detecta o tipo de operação SQL
     */
    private function detectOperationType(string $sql): string
    {
        $sql = trim($sql);

        if (str_starts_with($sql, 'SELECT')) {
            return 'SELECT';
        } elseif (str_starts_with($sql, 'INSERT')) {
            return 'INSERT';
        } elseif (str_starts_with($sql, 'UPDATE')) {
            return 'UPDATE';
        } elseif (str_starts_with($sql, 'DELETE')) {
            return 'DELETE';
        } elseif (str_starts_with($sql, 'CREATE')) {
            return 'CREATE';
        } elseif (str_starts_with($sql, 'ALTER')) {
            return 'ALTER';
        } elseif (str_starts_with($sql, 'DROP')) {
            return 'DROP';
        }

        return 'OTHER';
    }

    /**
     * Extrai o nome da tabela principal da query
     */
    private function extractTableName(string $sql, string $operationType): string
    {
        try {
            $sql = trim($sql);

            switch ($operationType) {
                case 'SELECT':
                    // SELECT ... FROM table_name
                    if (preg_match('/FROM\s+["`]?([a-zA-Z0-9_]+)["`]?/i', $sql, $matches)) {
                        return $matches[1];
                    }
                    break;

                case 'INSERT':
                    // INSERT INTO table_name
                    if (preg_match('/INSERT\s+INTO\s+["`]?([a-zA-Z0-9_]+)["`]?/i', $sql, $matches)) {
                        return $matches[1];
                    }
                    break;

                case 'UPDATE':
                    // UPDATE table_name SET
                    if (preg_match('/UPDATE\s+["`]?([a-zA-Z0-9_]+)["`]?\s+SET/i', $sql, $matches)) {
                        return $matches[1];
                    }
                    break;

                case 'DELETE':
                    // DELETE FROM table_name
                    if (preg_match('/DELETE\s+FROM\s+["`]?([a-zA-Z0-9_]+)["`]?/i', $sql, $matches)) {
                        return $matches[1];
                    }
                    break;
            }

            return 'unknown';
        } catch (\Exception $e) {
            return 'unknown';
        }
    }

    /**
     * Estima o número de linhas afetadas (aproximado)
     */
    private function estimateAffectedRows(string $sql, array $bindings): int
    {
        try {
            $sql = strtoupper(trim($sql));

            // Para INSERT: contar VALUES
            if (str_starts_with($sql, 'INSERT')) {
                $valueCount = substr_count($sql, 'VALUES');
                return max(1, $valueCount);
            }

            // Para outras operações, retornar 1 como padrão
            return 1;
        } catch (\Exception $e) {
            return 1;
        }
    }

    /**
     * Determina se a query deve ser registrada
     */
    private function shouldLogQuery(string $tableName, string $operationType): bool
    {
        // Ignorar tabelas de sistema/cache
        $ignoredTables = [
            'sessions',
            'cache',
            'cache_locks',
            'jobs',
            'failed_jobs',
            'migrations',
            'personal_access_tokens',
            'password_resets',
            'database_activities', // Evitar recursão
            'database_column_changes', // Evitar recursão no novo sistema
            'monitoring_metrics',
            'monitoring_logs',
            'monitoring_alerts'
        ];

        if (in_array($tableName, $ignoredTables)) {
            return false;
        }

        // Ignorar queries de sistema/healthcheck
        if ($tableName === 'unknown' && $operationType === 'SELECT') {
            return false;
        }

        return true;
    }

    /**
     * Insere o log de atividade na tabela
     */
    private function insertActivityLog(array $data)
    {
        try {
            // Normalizar nome da tabela para minúsculas para consistência
            if (isset($data['table_name'])) {
                $data['table_name'] = strtolower($data['table_name']);
            }

            // Usar conexão direta para evitar recursão no logging
            DB::table('database_activities')->insertGetId($data);
            return DB::getPdo()->lastInsertId();
        } catch (\Exception $e) {
            // Em caso de erro, apenas logar sem falhar
            Log::error('Erro ao inserir activity log: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Determina se deve registrar mudanças detalhadas de colunas
     */
    private function shouldLogColumnChanges(string $tableName): bool
    {
        // Tabelas importantes para análise detalhada
        $importantTables = [
            'proposicoes',
            'users',
            'templates',
            'modelo_proposicoes',
            'sessoes',
            'comissoes'
        ];

        return in_array($tableName, $importantTables);
    }

    /**
     * Registra mudanças detalhadas de colunas
     */
    private function logColumnChanges(array $query, string $tableName, string $operationType, Request $request)
    {
        try {
            $sql = $query['sql'];
            $bindings = $query['bindings'];

            // Obter informações do usuário
            $user = auth()->user();
            $userRole = $this->getUserRole($user);

            // Extrair colunas e valores da query
            $columnData = $this->extractColumnData($sql, $bindings, $tableName, $operationType);

            if (!empty($columnData)) {
                foreach ($columnData as $change) {
                    $logData = [
                        'table_name' => strtolower($tableName), // Normalizar para minúsculas
                        'column_name' => $change['column'],
                        'record_id' => $change['record_id'] ?? 0,
                        'operation_type' => $operationType,
                        'old_value' => json_encode($change['old_value']),
                        'new_value' => json_encode($change['new_value']),
                        'user_id' => $user?->id,
                        'user_role' => $userRole,
                        'user_name' => $user?->name ?? 'Sistema',
                        'request_method' => $request->method(),
                        'endpoint' => $request->path(),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'query_time_ms' => round($query['time'], 2),
                        'sql_hash' => hash('sha256', $sql),
                        'additional_context' => json_encode([
                            'full_bindings' => $bindings,
                            'session_id' => session()->getId(),
                            'referer' => $request->header('referer')
                        ]),
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    DB::table('database_column_changes')->insert($logData);
                }
            }
        } catch (\Exception $e) {
            Log::error('Erro ao registrar mudanças de colunas: ' . $e->getMessage());
        }
    }

    /**
     * Obter role do usuário para classificação
     */
    private function getUserRole($user): ?string
    {
        if (!$user) {
            return 'Sistema';
        }

        // Verificar roles usando Spatie
        if ($user->hasRole('Administrador')) {
            return 'Administrador';
        } elseif ($user->hasRole('Parlamentar')) {
            return 'Parlamentar';
        } elseif ($user->hasRole('Legislativo')) {
            return 'Legislativo';
        } elseif ($user->hasRole('Protocolo')) {
            return 'Protocolo';
        } elseif ($user->hasRole('Expediente')) {
            return 'Expediente';
        } elseif ($user->hasRole('Assessor Jurídico')) {
            return 'Assessor Jurídico';
        }

        return 'Usuário';
    }

    /**
     * Extrair dados de colunas da query SQL
     */
    private function extractColumnData(string $sql, array $bindings, string $tableName, string $operationType): array
    {
        $changes = [];

        try {
            if ($operationType === 'INSERT') {
                // Para INSERT, extrair colunas e valores
                $changes = $this->extractInsertColumns($sql, $bindings, $tableName);
            } elseif ($operationType === 'UPDATE') {
                // Para UPDATE, extrair colunas sendo atualizadas
                $changes = $this->extractUpdateColumns($sql, $bindings, $tableName);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao extrair dados de colunas: ' . $e->getMessage());
        }

        return $changes;
    }

    /**
     * Extrair colunas de INSERT
     */
    private function extractInsertColumns(string $sql, array $bindings, string $tableName): array
    {
        $changes = [];

        // Padrão: INSERT INTO table (col1, col2, col3) VALUES (?, ?, ?)
        if (preg_match('/INSERT\s+INTO\s+["`]?' . preg_quote($tableName) . '["`]?\s*\(([^)]+)\)\s*VALUES/i', $sql, $matches)) {
            $columnNames = array_map('trim', explode(',', $matches[1]));
            $columnNames = array_map(function($col) {
                return trim($col, '"`');
            }, $columnNames);

            foreach ($columnNames as $index => $column) {
                if (isset($bindings[$index])) {
                    $changes[] = [
                        'column' => $column,
                        'record_id' => null, // Será definido após o INSERT
                        'old_value' => null,
                        'new_value' => $bindings[$index]
                    ];
                }
            }
        }

        return $changes;
    }

    /**
     * Extrair colunas de UPDATE
     */
    private function extractUpdateColumns(string $sql, array $bindings, string $tableName): array
    {
        $changes = [];

        // Padrão: UPDATE table SET col1 = ?, col2 = ? WHERE ...
        if (preg_match('/UPDATE\s+["`]?' . preg_quote($tableName) . '["`]?\s+SET\s+(.+?)\s+WHERE/i', $sql, $matches)) {
            $setClause = $matches[1];

            // Extrair pares coluna = ?
            if (preg_match_all('/(["`]?[a-zA-Z0-9_]+["`]?)\s*=\s*\?/i', $setClause, $columnMatches)) {
                $columns = $columnMatches[1];

                foreach ($columns as $index => $column) {
                    $cleanColumn = trim($column, '"`');
                    if (isset($bindings[$index])) {
                        $changes[] = [
                            'column' => $cleanColumn,
                            'record_id' => $this->extractRecordIdFromWhere($sql, $bindings, count($columns)),
                            'old_value' => null, // Seria necessário query adicional para obter
                            'new_value' => $bindings[$index]
                        ];
                    }
                }
            }
        }

        return $changes;
    }

    /**
     * Extrair ID do registro da cláusula WHERE
     */
    private function extractRecordIdFromWhere(string $sql, array $bindings, int $setBindingsCount): ?int
    {
        // Assumir que o WHERE usa o ID como primeiro parâmetro após os SETs
        if (isset($bindings[$setBindingsCount])) {
            $value = $bindings[$setBindingsCount];
            return is_numeric($value) ? (int)$value : null;
        }

        return null;
    }

    /**
     * Extrai detalhes da query para armazenar
     */
    private function extractQueryDetails(string $sql, array $bindings, string $tableName, string $operationType): ?array
    {
        try {
            $details = [
                'fields' => [],
                'record_id' => null
            ];

            if ($operationType === 'INSERT') {
                // Extrair campos e valores do INSERT
                if (preg_match('/INSERT\s+INTO\s+["\`]?' . preg_quote($tableName) . '["\`]?\s*\(([^)]+)\)\s*VALUES/i', $sql, $matches)) {
                    $columns = array_map(function($col) {
                        return trim(trim($col), '"\`');
                    }, explode(',', $matches[1]));

                    foreach ($columns as $index => $column) {
                        if (isset($bindings[$index])) {
                            $details['fields'][$column] = [
                                'old' => null,
                                'new' => $this->formatValue($bindings[$index])
                            ];
                        }
                    }
                }
            } elseif ($operationType === 'UPDATE') {
                // Extrair campos do UPDATE
                if (preg_match('/UPDATE\s+["\`]?' . preg_quote($tableName) . '["\`]?\s+SET\s+(.+?)\s+WHERE/i', $sql, $matches)) {
                    $setClause = $matches[1];

                    // Extrair pares campo = valor
                    if (preg_match_all('/(["\`]?[a-zA-Z0-9_]+["\`]?)\s*=\s*\?/i', $setClause, $columnMatches)) {
                        $columns = $columnMatches[1];

                        foreach ($columns as $index => $column) {
                            $cleanColumn = trim(trim($column), '"\`');
                            if (isset($bindings[$index])) {
                                $details['fields'][$cleanColumn] = [
                                    'old' => '[valor anterior]', // Precisaria de query adicional
                                    'new' => $this->formatValue($bindings[$index])
                                ];
                            }
                        }
                    }

                    // Tentar extrair ID do WHERE
                    $details['record_id'] = $this->extractRecordIdFromWhere($sql, $bindings, count($columns ?? []));
                }
            } elseif ($operationType === 'DELETE') {
                // Extrair ID do DELETE
                if (preg_match('/WHERE\s+["\`]?id["\`]?\s*=\s*\?/i', $sql)) {
                    $details['record_id'] = $bindings[0] ?? null;
                }
            }

            return !empty($details['fields']) || $details['record_id'] ? $details : null;
        } catch (\Exception $e) {
            Log::error('Erro ao extrair detalhes da query: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Formata valor para armazenamento
     */
    private function formatValue($value)
    {
        if (is_null($value)) {
            return 'NULL';
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }
        if (strlen($value) > 100) {
            return substr($value, 0, 100) . '...';
        }
        return $value;
    }

    /**
     * Determina se deve ignorar a requisição para evitar recursão
     */
    private function shouldIgnoreRequest(Request $request): bool
    {
        $path = $request->path();

        // Ignorar rotas de monitoramento
        $ignoredPaths = [
            'admin/monitoring',
            'health',
            'monitoring-test'
        ];

        foreach ($ignoredPaths as $ignoredPath) {
            if (str_starts_with($path, $ignoredPath)) {
                return true;
            }
        }

        // Ignorar requests AJAX do monitoring
        if (str_contains($path, 'database-activity') ||
            str_contains($path, 'realtime-metrics') ||
            str_contains($path, 'table-stats') ||
            str_contains($path, 'monitoring/api')) {
            return true;
        }

        // Ignorar requests de assets
        if (str_contains($path, '.css') ||
            str_contains($path, '.js') ||
            str_contains($path, '.png') ||
            str_contains($path, '.jpg') ||
            str_contains($path, 'assets/')) {
            return true;
        }

        return false;
    }
}