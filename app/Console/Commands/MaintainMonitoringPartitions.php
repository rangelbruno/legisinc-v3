<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaintainMonitoringPartitions extends Command
{
    protected $signature = 'monitoring:partitions {--months-to-keep=3 : Number of months to keep data}';
    protected $description = 'Create next-month partitions and drop old ones per retention policy';

    private array $tables = [
        'monitoring_metrics',
        'monitoring_logs', 
        'monitoring_traces',
        'monitoring_alerts'
    ];

    public function handle()
    {
        $keepMonths = (int) $this->option('months-to-keep');
        
        $this->info('🔧 Maintaining monitoring partitions...');
        $this->info("📅 Retention policy: {$keepMonths} months");
        
        // Create future partitions
        $this->createFuturePartitions();
        
        // Drop old partitions
        $this->dropOldPartitions($keepMonths);
        
        // Analyze tables for optimization
        $this->analyzeTables();
        
        $this->info('✅ Partitions maintained successfully!');
        
        return Command::SUCCESS;
    }

    private function createFuturePartitions(): void
    {
        $this->info('📊 Creating future partitions...');
        
        // Create partitions for next 3 months
        for ($i = 1; $i <= 3; $i++) {
            $month = Carbon::now()->startOfMonth()->addMonths($i);
            $partitionName = $month->format('Y_m');
            $startDate = $month->copy()->startOfMonth()->format('Y-m-01');
            $endDate = $month->copy()->endOfMonth()->addDay()->format('Y-m-d');
            
            foreach ($this->tables as $table) {
                try {
                    DB::statement("
                        CREATE TABLE IF NOT EXISTS {$table}_{$partitionName}
                        PARTITION OF {$table}
                        FOR VALUES FROM ('{$startDate}') TO ('{$endDate}')
                    ");
                    
                    $this->line("  ✓ Created partition: {$table}_{$partitionName}");
                } catch (\Exception $e) {
                    // Partition might already exist, log but continue
                    Log::info("Partition {$table}_{$partitionName} already exists or error: " . $e->getMessage());
                }
            }
        }
    }

    private function dropOldPartitions(int $keepMonths): void
    {
        $this->info('🗑️ Checking for old partitions to drop...');
        
        $dropBefore = Carbon::now()->startOfMonth()->subMonths($keepMonths);
        $this->line("  Dropping partitions older than: " . $dropBefore->format('Y-m'));
        
        foreach ($this->tables as $table) {
            // Get all partitions for this table
            $partitions = DB::select("
                SELECT 
                    inhrelid::regclass AS partition_name
                FROM pg_inherits
                JOIN pg_class parent ON pg_inherits.inhparent = parent.oid
                JOIN pg_class child ON pg_inherits.inhrelid = child.oid
                WHERE parent.relname = ?
            ", [$table]);
            
            foreach ($partitions as $partition) {
                // Extract date from partition name (format: table_YYYY_MM)
                if (preg_match('/_(\d{4})_(\d{2})$/', $partition->partition_name, $matches)) {
                    $year = (int) $matches[1];
                    $month = (int) $matches[2];
                    $partitionDate = Carbon::createFromDate($year, $month, 1);
                    
                    if ($partitionDate->lt($dropBefore)) {
                        try {
                            // Count records before dropping
                            $count = DB::selectOne("SELECT COUNT(*) as count FROM {$partition->partition_name}")->count;
                            
                            DB::statement("DROP TABLE IF EXISTS {$partition->partition_name} CASCADE");
                            $this->line("  ✓ Dropped old partition: {$partition->partition_name} ({$count} records)");
                            
                            // Log the action
                            Log::info("Dropped monitoring partition", [
                                'partition' => $partition->partition_name,
                                'records' => $count,
                                'date' => $partitionDate->format('Y-m')
                            ]);
                        } catch (\Exception $e) {
                            $this->error("  ✗ Failed to drop partition {$partition->partition_name}: " . $e->getMessage());
                        }
                    }
                }
            }
        }
    }

    private function analyzeTables(): void
    {
        $this->info('📈 Analyzing tables for optimization...');
        
        foreach ($this->tables as $table) {
            try {
                DB::statement("ANALYZE {$table}");
                $this->line("  ✓ Analyzed: {$table}");
            } catch (\Exception $e) {
                $this->warn("  ⚠ Could not analyze {$table}: " . $e->getMessage());
            }
        }
    }
}