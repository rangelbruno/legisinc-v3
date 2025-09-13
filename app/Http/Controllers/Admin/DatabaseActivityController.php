<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DatabaseActivityController extends Controller
{
    /**
     * Exibe a tela de atividade do banco de dados
     */
    public function index()
    {
        return view('admin.monitoring.database-activity');
    }

    /**
     * API para obter atividades recentes do banco de dados
     */
    public function getRecentActivity()
    {
        try {
            // Cache por 5 segundos para evitar sobrecarga
            $activities = Cache::remember('db_activity_recent', 5, function () {
                return DB::table('database_activities')
                    ->select([
                        'id',
                        'table_name',
                        'operation_type',
                        'query_time_ms',
                        'affected_rows',
                        'request_method',
                        'endpoint',
                        'user_id',
                        'ip_address',
                        'change_details',
                        'created_at'
                    ])
                    ->orderBy('created_at', 'desc')
                    ->limit(100)
                    ->get();
            });

            return response()->json([
                'success' => true,
                'activities' => $activities,
                'total' => $activities->count(),
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao carregar atividades: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para estatísticas de atividade por tabela
     */
    public function getTableStats()
    {
        try {
            $stats = Cache::remember('db_activity_table_stats', 30, function () {
                return DB::table('database_activities')
                    ->select([
                        'table_name',
                        DB::raw('COUNT(*) as total_operations'),
                        DB::raw('COUNT(CASE WHEN operation_type = \'INSERT\' THEN 1 END) as inserts'),
                        DB::raw('COUNT(CASE WHEN operation_type = \'UPDATE\' THEN 1 END) as updates'),
                        DB::raw('COUNT(CASE WHEN operation_type = \'DELETE\' THEN 1 END) as deletes'),
                        DB::raw('COUNT(CASE WHEN operation_type = \'SELECT\' THEN 1 END) as selects'),
                        DB::raw('AVG(query_time_ms) as avg_query_time'),
                        DB::raw('SUM(affected_rows) as total_affected_rows'),
                        DB::raw('MAX(created_at) as last_activity')
                    ])
                    ->where('created_at', '>=', now()->subHour())
                    ->groupBy('table_name')
                    ->orderBy('total_operations', 'desc')
                    ->get();
            });

            return response()->json([
                'success' => true,
                'tables' => $stats,
                'period' => 'última hora'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao carregar estatísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para métricas de operações em tempo real
     */
    public function getRealTimeMetrics()
    {
        try {
            $metrics = Cache::remember('db_activity_realtime_metrics', 2, function () {
                $lastMinute = now()->subMinute();
                $last5Minutes = now()->subMinutes(5);
                $lastHour = now()->subHour();

                return [
                    'operations_last_minute' => DB::table('database_activities')
                        ->where('created_at', '>=', $lastMinute)
                        ->count(),

                    'operations_last_5min' => DB::table('database_activities')
                        ->where('created_at', '>=', $last5Minutes)
                        ->count(),

                    'operations_last_hour' => DB::table('database_activities')
                        ->where('created_at', '>=', $lastHour)
                        ->count(),

                    'avg_query_time_last_5min' => DB::table('database_activities')
                        ->where('created_at', '>=', $last5Minutes)
                        ->avg('query_time_ms'),

                    'slowest_operation_last_5min' => DB::table('database_activities')
                        ->where('created_at', '>=', $last5Minutes)
                        ->orderBy('query_time_ms', 'desc')
                        ->select('table_name', 'operation_type', 'query_time_ms')
                        ->first(),

                    'most_active_table_last_hour' => DB::table('database_activities')
                        ->where('created_at', '>=', $lastHour)
                        ->select('table_name', DB::raw('COUNT(*) as operations'))
                        ->groupBy('table_name')
                        ->orderBy('operations', 'desc')
                        ->first(),

                    'operations_by_type_last_hour' => DB::table('database_activities')
                        ->where('created_at', '>=', $lastHour)
                        ->select('operation_type', DB::raw('COUNT(*) as count'))
                        ->groupBy('operation_type')
                        ->pluck('count', 'operation_type')
                ];
            });

            return response()->json([
                'success' => true,
                'metrics' => $metrics,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao carregar métricas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para filtrar atividades
     */
    public function filterActivities(Request $request)
    {
        try {
            $query = DB::table('database_activities');

            // Filtro por múltiplas tabelas (normalizar para minúsculas)
            if ($request->filled('tables')) {
                $tables = is_array($request->tables) ? $request->tables : explode(',', $request->tables);
                $normalizedTables = array_map('strtolower', array_filter($tables));
                $query->whereIn('table_name', $normalizedTables);
            } elseif ($request->filled('table')) {
                // Compatibilidade com filtro único (normalizar para minúsculas)
                $query->where('table_name', strtolower($request->table));
            }

            // Filtro por múltiplas operações
            if ($request->filled('operations')) {
                $operations = is_array($request->operations) ? $request->operations : explode(',', $request->operations);
                $query->whereIn('operation_type', array_filter($operations));
            } elseif ($request->filled('operation')) {
                // Compatibilidade com filtro único
                $query->where('operation_type', $request->operation);
            }

            // Filtro por múltiplos métodos HTTP
            if ($request->filled('methods')) {
                $methods = is_array($request->methods) ? $request->methods : explode(',', $request->methods);
                $query->whereIn('request_method', array_filter($methods));
            } elseif ($request->filled('method')) {
                $query->where('request_method', $request->method);
            }

            // Filtro por período
            if ($request->filled('period')) {
                switch ($request->period) {
                    case '1min':
                        $query->where('created_at', '>=', now()->subMinute());
                        break;
                    case '5min':
                        $query->where('created_at', '>=', now()->subMinutes(5));
                        break;
                    case '1hour':
                        $query->where('created_at', '>=', now()->subHour());
                        break;
                    case '1day':
                        $query->where('created_at', '>=', now()->subDay());
                        break;
                    case '7days':
                        $query->where('created_at', '>=', now()->subDays(7));
                        break;
                }
            }

            // Filtro por usuário
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Filtro por endpoint
            if ($request->filled('endpoint')) {
                $query->where('endpoint', 'LIKE', '%' . $request->endpoint . '%');
            }

            $activities = $query->select([
                    'id',
                    'table_name',
                    'operation_type',
                    'query_time_ms',
                    'affected_rows',
                    'request_method',
                    'endpoint',
                    'user_id',
                    'ip_address',
                    'change_details',
                    'created_at'
                ])
                ->orderBy('created_at', 'desc')
                ->limit(500) // Aumentar limite para filtros múltiplos
                ->get();

            return response()->json([
                'success' => true,
                'activities' => $activities,
                'filters_applied' => $request->only(['tables', 'table', 'operations', 'operation', 'methods', 'method', 'period', 'user_id', 'endpoint']),
                'total' => $activities->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao filtrar atividades: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para obter lista de tabelas com atividade
     */
    public function getActiveTables()
    {
        try {
            $tables = Cache::remember('db_activity_all_tables', 60, function () {
                // Buscar todas as tabelas do schema público do PostgreSQL
                return DB::select("
                    SELECT table_name
                    FROM information_schema.tables
                    WHERE table_schema = 'public'
                    AND table_type = 'BASE TABLE'
                    ORDER BY table_name
                ");
            });

            // Extrair apenas os nomes das tabelas (manter em minúsculas)
            $tableNames = collect($tables)->map(function ($table) {
                return $table->table_name;
            })->values();

            return response()->json([
                'success' => true,
                'tables' => $tableNames
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao carregar tabelas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para obter fluxo detalhado de um registro específico
     */
    public function getRecordFlow(Request $request)
    {
        try {
            $tableName = $request->get('table');
            $recordId = $request->get('record_id');

            if (!$tableName || !$recordId) {
                return response()->json([
                    'success' => false,
                    'error' => 'Tabela e ID do registro são obrigatórios'
                ], 400);
            }

            // Buscar mudanças de colunas para o registro específico (normalizar nome da tabela)
            $columnChanges = DB::table('database_column_changes')
                ->where('table_name', strtolower($tableName))
                ->where('record_id', $recordId)
                ->orderBy('created_at', 'asc')
                ->get();

            // Agrupar por etapas do fluxo
            $flow = [];
            $currentStage = null;
            $stageIndex = 0;

            foreach ($columnChanges as $change) {
                if ($currentStage !== $change->user_role) {
                    $currentStage = $change->user_role;
                    $stageIndex++;

                    $flow[$stageIndex] = [
                        'stage' => $stageIndex,
                        'user_role' => $change->user_role,
                        'user_name' => $change->user_name,
                        'timestamp' => $change->created_at,
                        'changes' => []
                    ];
                }

                $flow[$stageIndex]['changes'][] = [
                    'column' => $change->column_name,
                    'old_value' => json_decode($change->old_value),
                    'new_value' => json_decode($change->new_value),
                    'timestamp' => $change->created_at,
                    'operation' => $change->operation_type
                ];
            }

            return response()->json([
                'success' => true,
                'table' => $tableName,
                'record_id' => $recordId,
                'flow' => array_values($flow),
                'total_stages' => count($flow),
                'total_changes' => $columnChanges->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao carregar fluxo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para obter análise de colunas mais alteradas
     */
    public function getColumnAnalysis(Request $request)
    {
        try {
            $tableName = $request->get('table');
            $period = $request->get('period', '24h');

            $query = DB::table('database_column_changes');

            if ($tableName) {
                $query->where('table_name', strtolower($tableName));
            }

            // Aplicar filtro de período
            switch ($period) {
                case '1h':
                    $query->where('created_at', '>=', now()->subHour());
                    break;
                case '24h':
                    $query->where('created_at', '>=', now()->subDay());
                    break;
                case '7d':
                    $query->where('created_at', '>=', now()->subWeek());
                    break;
                case '30d':
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
            }

            $analysis = $query
                ->select([
                    'table_name',
                    'column_name',
                    DB::raw('COUNT(*) as total_changes'),
                    DB::raw('COUNT(DISTINCT record_id) as unique_records'),
                    DB::raw('COUNT(DISTINCT user_id) as unique_users'),
                    DB::raw('string_agg(DISTINCT user_role, \', \') as roles_involved'), // PostgreSQL syntax
                    DB::raw('MAX(created_at) as last_change'),
                    DB::raw('AVG(query_time_ms) as avg_query_time')
                ])
                ->groupBy(['table_name', 'column_name'])
                ->orderBy('total_changes', 'desc')
                ->limit(50)
                ->get();

            return response()->json([
                'success' => true,
                'period' => $period,
                'table' => $tableName,
                'analysis' => $analysis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao carregar análise: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para obter registros por tabela com atividade recente
     */
    public function getTableRecords(Request $request)
    {
        try {
            $tableName = $request->get('table');

            if (!$tableName) {
                return response()->json([
                    'success' => false,
                    'error' => 'Nome da tabela é obrigatório'
                ], 400);
            }

            // Buscar registros com atividade recente (normalizar nome da tabela)
            // Corrigir query para PostgreSQL
            $records = DB::table('database_column_changes')
                ->where('table_name', strtolower($tableName))
                ->where('created_at', '>=', now()->subDays(7)) // Últimos 7 dias
                ->select([
                    'record_id',
                    DB::raw('COUNT(*) as total_changes'),
                    DB::raw('COUNT(DISTINCT column_name) as columns_changed'),
                    DB::raw('COUNT(DISTINCT user_role) as roles_involved'),
                    DB::raw('string_agg(DISTINCT user_role, \' → \') as user_flow'), // Removido ORDER BY problemático
                    DB::raw('MIN(created_at) as first_change'),
                    DB::raw('MAX(created_at) as last_change')
                ])
                ->groupBy('record_id')
                ->orderBy('last_change', 'desc')
                ->limit(100)
                ->get();

            return response()->json([
                'success' => true,
                'table' => $tableName,
                'records' => $records,
                'total' => $records->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao carregar registros: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar atividades em vários formatos
     */
    public function exportActivities(Request $request)
    {
        try {
            $format = $request->get('format', 'csv'); // csv, excel, detailed
            $includeDetails = in_array($format, ['detailed', 'excel']);

            // Usar os mesmos filtros da API de filter
            $query = DB::table('database_activities');

            // Aplicar filtros (mesmo código da filterActivities)
            $this->applyFiltersToQuery($query, $request);

            // Selecionar colunas apropriadas
            $selectColumns = [
                'id', 'table_name', 'operation_type', 'query_time_ms', 'affected_rows',
                'request_method', 'endpoint', 'user_id', 'ip_address', 'created_at'
            ];

            if ($includeDetails) {
                $selectColumns[] = 'change_details';
            }

            $activities = $query->select($selectColumns)
                ->orderBy('created_at', 'desc')
                ->limit(2000) // Aumentar limite para exportação
                ->get();

            // Gerar arquivo baseado no formato
            switch ($format) {
                case 'excel':
                    return $this->exportToExcel($activities, $request);
                case 'detailed':
                    return $this->exportToDetailedCSV($activities, $request);
                default:
                    return $this->exportToBasicCSV($activities, $request);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro na exportação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aplicar filtros à query (reutilizado de filterActivities)
     */
    private function applyFiltersToQuery($query, Request $request)
    {
        // Filtro por tabela
        if ($request->filled('table')) {
            $query->where('table_name', strtolower($request->table));
        }

        // Filtro por operações
        if ($request->filled('operations')) {
            $operations = is_array($request->operations) ? $request->operations : explode(',', $request->operations);
            $query->whereIn('operation_type', array_filter($operations));
        }

        // Filtro por métodos
        if ($request->filled('methods')) {
            $methods = is_array($request->methods) ? $request->methods : explode(',', $request->methods);
            $query->whereIn('request_method', array_filter($methods));
        }

        // Filtro por período
        if ($request->filled('period')) {
            switch ($request->period) {
                case '1min':
                    $query->where('created_at', '>=', now()->subMinute());
                    break;
                case '5min':
                    $query->where('created_at', '>=', now()->subMinutes(5));
                    break;
                case '1hour':
                    $query->where('created_at', '>=', now()->subHour());
                    break;
                case '1day':
                    $query->where('created_at', '>=', now()->subDay());
                    break;
                case '7days':
                    $query->where('created_at', '>=', now()->subDays(7));
                    break;
                case '30days':
                    $query->where('created_at', '>=', now()->subDays(30));
                    break;
            }
        }

        // Filtro por usuário
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtro por endpoint
        if ($request->filled('endpoint')) {
            $query->where('endpoint', 'LIKE', '%' . $request->endpoint . '%');
        }
    }

    /**
     * Exportar CSV básico
     */
    private function exportToBasicCSV($activities, Request $request)
    {
        $filename = sprintf('atividades_%s_%s.csv',
            $request->get('table', 'todas'),
            date('Y-m-d_H-i-s')
        );

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function() use ($activities, $request) {
            $handle = fopen('php://output', 'w');

            // BOM para UTF-8
            fputs($handle, "\xEF\xBB\xBF");

            // Cabeçalho com informações do relatório
            fputcsv($handle, ['# Relatório de Atividades do Banco de Dados']);
            fputcsv($handle, ['# Tabela: ' . ($request->get('table') ?: 'Todas')]);
            fputcsv($handle, ['# Período: ' . ($request->get('period') ?: 'Todos')]);
            fputcsv($handle, ['# Gerado em: ' . now()->format('d/m/Y H:i:s')]);
            fputcsv($handle, ['# Total de registros: ' . $activities->count()]);
            fputcsv($handle, []); // Linha em branco

            // Cabeçalhos das colunas
            fputcsv($handle, [
                'Data/Hora',
                'Tabela',
                'Operação',
                'Tempo (ms)',
                'Linhas Afetadas',
                'Método HTTP',
                'Endpoint',
                'Usuário ID',
                'Endereço IP'
            ]);

            // Dados
            foreach ($activities as $activity) {
                fputcsv($handle, [
                    $activity->created_at,
                    $activity->table_name,
                    $activity->operation_type,
                    $activity->query_time_ms,
                    $activity->affected_rows,
                    $activity->request_method ?: 'N/A',
                    $activity->endpoint ?: 'N/A',
                    $activity->user_id ?: 'Sistema',
                    $activity->ip_address ?: 'N/A'
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Exportar CSV detalhado com change_details
     */
    private function exportToDetailedCSV($activities, Request $request)
    {
        $filename = sprintf('atividades_detalhadas_%s_%s.csv',
            $request->get('table', 'todas'),
            date('Y-m-d_H-i-s')
        );

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function() use ($activities, $request) {
            $handle = fopen('php://output', 'w');

            // BOM para UTF-8
            fputs($handle, "\xEF\xBB\xBF");

            // Cabeçalho informativo
            fputcsv($handle, ['# Relatório Detalhado de Atividades do Banco de Dados']);
            fputcsv($handle, ['# Inclui detalhes de campos alterados em cada operação']);
            fputcsv($handle, ['# Tabela: ' . ($request->get('table') ?: 'Todas')]);
            fputcsv($handle, ['# Gerado em: ' . now()->format('d/m/Y H:i:s')]);
            fputcsv($handle, []);

            // Cabeçalhos expandidos
            fputcsv($handle, [
                'Data/Hora',
                'Tabela',
                'Operação',
                'Tempo (ms)',
                'Método HTTP',
                'Endpoint',
                'Usuário ID',
                'IP',
                'ID Registro',
                'Tem Detalhes',
                'Campos Alterados',
                'Resumo das Mudanças'
            ]);

            // Dados com detalhes
            foreach ($activities as $activity) {
                $hasDetails = 'Não';
                $fieldsChanged = '';
                $changesSummary = '';
                $recordId = '';

                if ($activity->change_details) {
                    try {
                        $details = json_decode($activity->change_details, true);
                        if ($details && isset($details['fields'])) {
                            $hasDetails = 'Sim';
                            $fieldsChanged = implode(', ', array_keys($details['fields']));
                            $recordId = $details['record_id'] ?? '';

                            $changes = [];
                            foreach ($details['fields'] as $field => $values) {
                                $old = $values['old'] === null ? 'NULL' : $values['old'];
                                $new = $values['new'] === null ? 'NULL' : $values['new'];
                                $changes[] = "$field: $old → $new";
                            }
                            $changesSummary = implode(' | ', $changes);
                        }
                    } catch (\Exception $e) {
                        $hasDetails = 'Erro';
                        $fieldsChanged = 'Erro ao processar';
                    }
                }

                fputcsv($handle, [
                    $activity->created_at,
                    $activity->table_name,
                    $activity->operation_type,
                    $activity->query_time_ms,
                    $activity->request_method ?: 'N/A',
                    $activity->endpoint ?: 'N/A',
                    $activity->user_id ?: 'Sistema',
                    $activity->ip_address ?: 'N/A',
                    $recordId,
                    $hasDetails,
                    $fieldsChanged,
                    $changesSummary
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Exportar para Excel (CSV avançado)
     */
    private function exportToExcel($activities, Request $request)
    {
        // Para uma implementação real do Excel, seria necessário usar PhpSpreadsheet
        // Por enquanto, retornamos um CSV avançado que pode ser aberto no Excel
        return $this->exportToDetailedCSV($activities, $request);
    }

    /**
     * API para obter opções de filtro disponíveis
     */
    public function getFilterOptions()
    {
        try {
            // Versão simplificada sem cache para debug
            $options = [
                'http_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
                'operation_types' => ['SELECT', 'INSERT', 'UPDATE', 'DELETE'],
                'tables_with_activity' => [
                    ['table_name' => 'proposicoes', 'activity_count' => 513],
                    ['table_name' => 'users', 'activity_count' => 21],
                    ['table_name' => 'templates', 'activity_count' => 15],
                    ['table_name' => 'sessoes', 'activity_count' => 8]
                ],
                'active_users' => [
                    ['user_id' => 2, 'activity_count' => 1916],
                    ['user_id' => 3, 'activity_count' => 573],
                    ['user_id' => 5, 'activity_count' => 529]
                ]
            ];

            return response()->json([
                'success' => true,
                'options' => $options
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro em getFilterOptions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erro ao carregar opções: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para limpeza de logs antigos (executado via cron)
     */
    public function cleanOldLogs()
    {
        try {
            // Manter apenas atividades dos últimos 7 dias
            $deletedActivities = DB::table('database_activities')
                ->where('created_at', '<', now()->subDays(7))
                ->delete();

            // Manter mudanças de colunas dos últimos 30 dias
            $deletedChanges = DB::table('database_column_changes')
                ->where('created_at', '<', now()->subDays(30))
                ->delete();

            return response()->json([
                'success' => true,
                'deleted_activities' => $deletedActivities,
                'deleted_changes' => $deletedChanges,
                'message' => "Limpeza concluída: $deletedActivities atividades e $deletedChanges mudanças removidas"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro na limpeza: ' . $e->getMessage()
            ], 500);
        }
    }
}