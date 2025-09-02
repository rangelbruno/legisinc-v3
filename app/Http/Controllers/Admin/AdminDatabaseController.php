<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Exception;

class AdminDatabaseController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        try {
            $tableNames = $this->getAllTables();
            $tables = $this->getTablesWithDescriptions($tableNames);
            $error = null;
        } catch (Exception $e) {
            $tables = [];
            $error = "Erro ao conectar com o banco de dados: " . $e->getMessage();
        }
        
        return view('admin.database.index', compact('tables', 'error'));
    }

    public function showTable(string $table): \Illuminate\View\View
    {
        try {
            if (!$this->isValidTable($table)) {
                abort(404, 'Tabela não encontrada');
            }

            $columns = Schema::getColumnListing($table);
            $data = DB::table($table)->paginate(50);
            $error = null;
            
        } catch (Exception $e) {
            $columns = [];
            $data = collect([]);
            $error = "Erro ao acessar a tabela: " . $e->getMessage();
        }
        
        return view('admin.database.table', compact('table', 'columns', 'data', 'error'));
    }

    private function getAllTables(): array
    {
        // Primeiro, tentamos usar o método mais confiável via Laravel Schema
        try {
            $connection = Schema::getConnection();
            $schemaBuilder = $connection->getSchemaBuilder();
            
            // Para versões mais recentes do Laravel que suportam getTables()
            if (method_exists($schemaBuilder, 'getTables')) {
                $tables = $schemaBuilder->getTables();
                return array_map(function($table) {
                    return is_object($table) ? $table->name : $table['name'];
                }, $tables);
            }
        } catch (Exception $e) {
            // Se falhar, tentamos métodos específicos por driver
        }
        
        // Fallback para métodos específicos por driver
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");
        
        try {
            if ($driver === 'pgsql') {
                // PostgreSQL - usando information_schema que é mais universal
                $tables = DB::select("
                    SELECT table_name 
                    FROM information_schema.tables 
                    WHERE table_schema = 'public' 
                    AND table_type = 'BASE TABLE'
                    ORDER BY table_name
                ");
                return array_map(function($table) {
                    return $table->table_name;
                }, $tables);
                
            } elseif ($driver === 'mysql') {
                // MySQL
                $tables = DB::select('SHOW TABLES');
                $databaseName = config("database.connections.{$connection}.database");
                $tableKey = "Tables_in_{$databaseName}";
                
                return array_map(function($table) use ($tableKey) {
                    return $table->$tableKey;
                }, $tables);
                
            } elseif ($driver === 'sqlite') {
                // SQLite
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                return array_map(function($table) {
                    return $table->name;
                }, $tables);
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao listar tabelas ({$driver}): " . $e->getMessage());
        }
        
        throw new Exception("Driver de banco de dados não suportado: {$driver}");
    }

    private function isValidTable(string $table): bool
    {
        $tableNames = $this->getAllTables();
        return in_array($table, $tableNames);
    }

    private function getTablesWithDescriptions(array $tableNames): array
    {
        $descriptions = $this->getTableDescriptions();
        
        return array_map(function($tableName) use ($descriptions) {
            return [
                'name' => $tableName,
                'description' => $descriptions[$tableName] ?? 'Tabela do sistema',
                'category' => $this->getTableCategory($tableName),
                'icon' => $this->getTableIcon($tableName)
            ];
        }, $tableNames);
    }

    private function getTableDescriptions(): array
    {
        return [
            // Usuários e Autenticação
            'users' => 'Usuários do sistema (parlamentares, servidores, administradores)',
            'password_reset_tokens' => 'Tokens para redefinição de senhas dos usuários',
            'sessions' => 'Sessões ativas dos usuários logados no sistema',
            'personal_access_tokens' => 'Tokens de acesso pessoal para API',
            
            // Parlamentares e Política
            'parlamentars' => 'Dados dos parlamentares (vereadores, prefeito, etc.)',
            'partidos' => 'Partidos políticos cadastrados no sistema',
            'mesa_diretora' => 'Composição da Mesa Diretora da Câmara',
            
            // Proposições Legislativas
            'proposicoes' => 'Proposições legislativas (projetos de lei, moções, etc.)',
            'tipo_proposicoes' => 'Tipos de proposições (PL, Moção, Requerimento, etc.)',
            'proposicoes_historico' => 'Histórico de alterações nas proposições',
            'parecer_juridicos' => 'Pareceres jurídicos das proposições',
            'tramitacao_logs' => 'Log de tramitação das proposições',
            
            // Sessões e Pautas
            'sessao_plenarias' => 'Sessões plenárias da Câmara Municipal',
            'item_pautas' => 'Itens de pauta das sessões plenárias',
            
            // Templates e Documentos
            'documento_modelos' => 'Modelos de documentos do sistema',
            'documento_instancias' => 'Instâncias de documentos gerados',
            'documento_versoes' => 'Versões dos documentos',
            'documento_colaboradores' => 'Colaboradores dos documentos',
            'tipo_proposicao_templates' => 'Templates por tipo de proposição',
            'template_universal' => 'Template universal do sistema',
            
            // Parâmetros e Configuração
            'parametros' => 'Parâmetros de configuração do sistema',
            'grupos_parametros' => 'Grupos de parâmetros para organização',
            'tipos_parametros' => 'Tipos de parâmetros (texto, número, booleano, etc.)',
            'historico_parametros' => 'Histórico de alterações dos parâmetros',
            'parametros_modulos' => 'Módulos de parâmetros do sistema',
            'parametros_submodulos' => 'Submódulos de parâmetros',
            'parametros_campos' => 'Campos dos parâmetros',
            'parametros_valores' => 'Valores dos parâmetros',
            'auditoria_parametros' => 'Auditoria de alterações nos parâmetros',
            
            // Permissões e Segurança
            'permissions' => 'Permissões do sistema',
            'roles' => 'Perfis de usuário (roles)',
            'model_has_permissions' => 'Relacionamento entre modelos e permissões',
            'model_has_roles' => 'Relacionamento entre modelos e perfis',
            'role_has_permissions' => 'Relacionamento entre perfis e permissões',
            'screen_permissions' => 'Permissões de tela do sistema',
            'permission_audit_log' => 'Log de auditoria das permissões',
            'permission_access_log' => 'Log de acesso às permissões',
            'user_permission_cache' => 'Cache de permissões dos usuários',
            'permission_performance_log' => 'Log de performance das permissões',
            
            // Inteligência Artificial
            'ai_configurations' => 'Configurações de IA do sistema',
            'ai_providers' => 'Provedores de serviços de IA',
            
            // Variáveis e Cache
            'variaveis_dinamicas' => 'Variáveis dinâmicas do sistema',
            'cache' => 'Cache de dados do sistema',
            'cache_locks' => 'Locks do sistema de cache',
            
            // Sistema e Jobs
            'jobs' => 'Filas de jobs para processamento assíncrono',
            'job_batches' => 'Lotes de jobs agrupados',
            'failed_jobs' => 'Jobs que falharam na execução',
            
            // Projetos (se existir)
            'projetos' => 'Projetos do sistema legislativo',
            
            // OnlyOffice (se existir)
            'doc_changes' => 'Controle de alterações do OnlyOffice',
            'task_result' => 'Resultados de tarefas do OnlyOffice',
        ];
    }

    private function getTableCategory(string $tableName): string
    {
        $categories = [
            'Usuários' => ['users', 'password_reset_tokens', 'sessions', 'personal_access_tokens'],
            'Parlamentares' => ['parlamentars', 'partidos', 'mesa_diretora'],
            'Proposições' => ['proposicoes', 'tipo_proposicoes', 'proposicoes_historico', 'parecer_juridicos', 'tramitacao_logs'],
            'Sessões' => ['sessao_plenarias', 'item_pautas'],
            'Documentos' => ['documento_modelos', 'documento_instancias', 'documento_versoes', 'documento_colaboradores', 'tipo_proposicao_templates', 'template_universal'],
            'Configuração' => ['parametros', 'grupos_parametros', 'tipos_parametros', 'historico_parametros', 'parametros_modulos', 'parametros_submodulos', 'parametros_campos', 'parametros_valores', 'auditoria_parametros'],
            'Segurança' => ['permissions', 'roles', 'model_has_permissions', 'model_has_roles', 'role_has_permissions', 'screen_permissions', 'permission_audit_log', 'permission_access_log', 'user_permission_cache', 'permission_performance_log'],
            'Inteligência Artificial' => ['ai_configurations', 'ai_providers'],
            'Sistema' => ['variaveis_dinamicas', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'doc_changes', 'task_result']
        ];

        foreach ($categories as $category => $tables) {
            if (in_array($tableName, $tables)) {
                return $category;
            }
        }

        return 'Outros';
    }

    private function getTableIcon(string $tableName): string
    {
        $icons = [
            'users' => 'ki-profile-circle',
            'parlamentars' => 'ki-people',
            'partidos' => 'ki-flag',
            'proposicoes' => 'ki-document',
            'sessao_plenarias' => 'ki-calendar-8',
            'permissions' => 'ki-shield-tick',
            'roles' => 'ki-security-user',
            'parametros' => 'ki-setting-3',
            'ai_configurations' => 'ki-abstract-26',
            'cache' => 'ki-storage',
            'jobs' => 'ki-timer',
        ];

        return $icons[$tableName] ?? 'ki-technology-3';
    }
}
