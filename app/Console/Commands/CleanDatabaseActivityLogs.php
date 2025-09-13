<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanDatabaseActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db-activity:clean {--days=7 : Manter logs dos últimos N dias} {--force : Forçar limpeza sem confirmação}';

    /**
     * The console command description.
     */
    protected $description = 'Limpa logs antigos de atividade do banco de dados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $force = $this->option('force');

        $this->info("🧹 Iniciando limpeza de logs de atividade do banco de dados");
        $this->info("📅 Mantendo logs dos últimos {$days} dias");

        // Contar registros a serem removidos
        $cutoffDate = now()->subDays($days);

        $activitiesToDelete = DB::table('database_activities')
            ->where('created_at', '<', $cutoffDate)
            ->count();

        $changesToDelete = DB::table('database_column_changes')
            ->where('created_at', '<', $cutoffDate)
            ->count();

        $recursionToDelete = DB::table('database_activities')
            ->where(function ($query) {
                $query->where('endpoint', 'LIKE', '%monitoring%')
                    ->orWhere('endpoint', 'LIKE', '%database-activity%')
                    ->orWhere('endpoint', 'LIKE', '%health%');
            })
            ->count();

        $this->table(
            ['Tipo', 'Registros a Remover'],
            [
                ['Atividades antigas', number_format($activitiesToDelete)],
                ['Mudanças de colunas antigas', number_format($changesToDelete)],
                ['Logs de recursão', number_format($recursionToDelete)]
            ]
        );

        $totalToDelete = $activitiesToDelete + $changesToDelete + $recursionToDelete;

        if ($totalToDelete === 0) {
            $this->info("✅ Nenhum registro para limpar!");
            return 0;
        }

        // Confirmação
        if (!$force && !$this->confirm("Confirma a remoção de {$totalToDelete} registros?")) {
            $this->info("❌ Operação cancelada pelo usuário");
            return 1;
        }

        $this->info("🗑️ Iniciando limpeza...");

        // Limpeza de logs de recursão (prioridade)
        if ($recursionToDelete > 0) {
            $this->info("🔄 Removendo logs de recursão...");
            $deletedRecursion = DB::table('database_activities')
                ->where(function ($query) {
                    $query->where('endpoint', 'LIKE', '%monitoring%')
                        ->orWhere('endpoint', 'LIKE', '%database-activity%')
                        ->orWhere('endpoint', 'LIKE', '%health%');
                })
                ->delete();

            $this->info("✅ Removidos {$deletedRecursion} logs de recursão");
        }

        // Limpeza de atividades antigas
        if ($activitiesToDelete > 0) {
            $this->info("📊 Removendo atividades antigas...");
            $deletedActivities = DB::table('database_activities')
                ->where('created_at', '<', $cutoffDate)
                ->delete();

            $this->info("✅ Removidas {$deletedActivities} atividades antigas");
        }

        // Limpeza de mudanças de colunas antigas
        if ($changesToDelete > 0) {
            $this->info("🔧 Removendo mudanças de colunas antigas...");
            $deletedChanges = DB::table('database_column_changes')
                ->where('created_at', '<', $cutoffDate)
                ->delete();

            $this->info("✅ Removidas {$deletedChanges} mudanças de colunas antigas");
        }

        // Estatísticas finais
        $remainingActivities = DB::table('database_activities')->count();
        $remainingChanges = DB::table('database_column_changes')->count();

        $this->info("");
        $this->info("📈 Estatísticas Finais:");
        $this->table(
            ['Tabela', 'Registros Restantes'],
            [
                ['database_activities', number_format($remainingActivities)],
                ['database_column_changes', number_format($remainingChanges)]
            ]
        );

        $this->info("🎉 Limpeza concluída com sucesso!");

        return 0;
    }
}