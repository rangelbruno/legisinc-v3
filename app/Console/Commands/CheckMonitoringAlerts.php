<?php

namespace App\Console\Commands;

use App\Services\Monitoring\AlertService;
use App\Services\Monitoring\MetricsFlushService;
use Illuminate\Console\Command;

class CheckMonitoringAlerts extends Command
{
    protected $signature = 'monitoring:check-alerts';
    protected $description = 'Check system metrics and trigger alerts if thresholds are exceeded';

    private AlertService $alertService;
    private MetricsFlushService $flushService;

    public function __construct(AlertService $alertService, MetricsFlushService $flushService)
    {
        parent::__construct();
        $this->alertService = $alertService;
        $this->flushService = $flushService;
    }

    public function handle()
    {
        $this->info('🔍 Checking monitoring alerts...');
        
        // First, flush any pending metrics from Redis buffer
        $flushed = $this->flushService->flush();
        if ($flushed > 0) {
            $this->line("📊 Flushed {$flushed} metrics from buffer");
        }
        
        // Check for alerts
        $alerts = $this->alertService->checkAndNotify();
        
        if (empty($alerts)) {
            $this->info('✅ No alerts triggered - system is healthy');
        } else {
            $this->warn('⚠️ ' . count($alerts) . ' alert(s) triggered:');
            
            foreach ($alerts as $alert) {
                $emoji = match($alert['severity']) {
                    'critical' => '🚨',
                    'high' => '⚠️',
                    'medium' => '📊',
                    default => 'ℹ️'
                };
                
                $this->line("  {$emoji} [{$alert['severity']}] {$alert['alert_type']}: {$alert['message']}");
            }
        }
        
        return Command::SUCCESS;
    }
}