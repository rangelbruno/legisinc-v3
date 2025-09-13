<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use App\Services\Monitoring\AlertService;

class MonitoringServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register monitoring services
        $this->app->singleton(AlertService::class);
    }

    public function boot(): void
    {
        // Register scheduled tasks
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            
            // Flush Redis metrics to Postgres every minute
            if (config('monitoring.redis.flush_enabled', true)) {
                $schedule->command('monitoring:flush-redis', ['--batch=800', '--timeout=1'])
                    ->everyMinute()
                    ->withoutOverlapping()
                    ->name('monitoring-flush-redis')
                    ->onOneServer()
                    ->runInBackground();
            }
            
            // Evaluate monitoring alerts every 5 minutes
            if (config('monitoring.alerts.enabled', false)) {
                $schedule->call(function () {
                    $alertService = app(AlertService::class);
                    $count = $alertService->evaluateAndNotify();
                    if ($count > 0) {
                        logger('Monitoring alerts triggered', ['count' => $count]);
                    }
                })
                ->everyFiveMinutes()
                ->withoutOverlapping()
                ->onOneServer()
                ->name('monitoring-alerts');
            }
            
            // Partition maintenance (monthly)
            $schedule->command('monitoring:maintain-partitions')
                ->monthly()
                ->name('monitoring-partition-maintenance')
                ->onOneServer();
                
            // Cleanup old monitoring data based on retention policies
            $schedule->call(function () {
                $this->cleanupOldData();
            })
            ->daily()
            ->at('02:00')
            ->name('monitoring-cleanup')
            ->onOneServer();
        });
    }
    
    protected function cleanupOldData(): void
    {
        try {
            $retentionDays = config('monitoring.retention.metrics.default_days', 30);
            
            \DB::table('monitoring_metrics')
                ->where('created_at', '<', now()->subDays($retentionDays))
                ->delete();
                
            \DB::table('monitoring_logs')
                ->where('created_at', '<', now()->subDays(7))
                ->delete();
                
            \DB::table('monitoring_traces')  
                ->where('created_at', '<', now()->subHours(72))
                ->delete();
                
            logger('Monitoring data cleanup completed', [
                'retention_days' => $retentionDays
            ]);
        } catch (\Exception $e) {
            logger('Monitoring cleanup failed', ['error' => $e->getMessage()]);
        }
    }
}