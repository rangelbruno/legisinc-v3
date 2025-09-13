<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonitoringSyntheticSeeder extends Seeder
{
    /**
     * Generate synthetic monitoring metrics for testing percentile calculations
     */
    public function run(): void
    {
        $this->command->info('🔄 Generating synthetic monitoring metrics...');
        
        // Clear existing metrics to avoid duplicates
        DB::table('monitoring_metrics')->where('metric_name', 'LIKE', 'synthetic_%')->delete();
        
        $startTime = microtime(true);
        $metricsInserted = 0;
        
        // Generate metrics for different time periods
        $timeframes = [
            ['hours' => 1, 'count' => 2000, 'description' => 'last 1 hour'],
            ['hours' => 24, 'count' => 8000, 'description' => 'last 24 hours'],
            ['hours' => 168, 'count' => 15000, 'description' => 'last 7 days'], // For historical analysis
        ];
        
        foreach ($timeframes as $timeframe) {
            $this->command->info("Generating {$timeframe['count']} metrics for {$timeframe['description']}...");
            
            $batchSize = 500;
            $batches = ceil($timeframe['count'] / $batchSize);
            
            for ($batch = 0; $batch < $batches; $batch++) {
                $metrics = [];
                $batchCount = min($batchSize, $timeframe['count'] - ($batch * $batchSize));
                
                for ($i = 0; $i < $batchCount; $i++) {
                    $metrics[] = $this->generateSyntheticMetric($timeframe['hours']);
                }
                
                DB::table('monitoring_metrics')->insert($metrics);
                $metricsInserted += count($metrics);
            }
        }
        
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        $this->command->info("✅ Generated {$metricsInserted} synthetic metrics in {$duration}ms");
        $this->command->info("📊 Data distribution:");
        $this->showMetricsDistribution();
    }
    
    /**
     * Generate a single synthetic metric with realistic data
     */
    protected function generateSyntheticMetric(int $hoursAgo): array
    {
        $routes = $this->getRouteDistribution();
        $route = $this->selectWeightedRoute($routes);
        
        // Generate timestamp within the specified timeframe
        $timestamp = Carbon::now()->subMinutes(rand(0, $hoursAgo * 60));
        
        // Generate latency with route-specific characteristics
        $latency = $this->generateLatencyForRoute($route);
        
        // Generate status code based on route type
        $statusCode = $this->generateStatusCode($route);
        
        return [
            'metric_type' => 'http',
            'metric_name' => 'request_duration_ms',
            'value' => $latency,
            'tags' => json_encode([
                'route' => $route['name'],
                'method' => $route['method'],
                'status' => (string) $statusCode,
                'synthetic' => 'true', // Mark as synthetic data
            ]),
            'created_at' => $timestamp,
        ];
    }
    
    /**
     * Define route distribution with realistic weights and characteristics
     */
    protected function getRouteDistribution(): array
    {
        return [
            // High traffic routes (fast, reliable)
            ['name' => '/api/proposicoes', 'method' => 'GET', 'weight' => 25, 'base_latency' => 150, 'variance' => 50, 'error_rate' => 1],
            ['name' => '/admin/dashboard', 'method' => 'GET', 'weight' => 20, 'base_latency' => 200, 'variance' => 75, 'error_rate' => 2],
            ['name' => '/api/usuarios/profile', 'method' => 'GET', 'weight' => 15, 'base_latency' => 120, 'variance' => 40, 'error_rate' => 1],
            
            // Medium traffic routes
            ['name' => '/proposicoes/create', 'method' => 'POST', 'weight' => 10, 'base_latency' => 400, 'variance' => 150, 'error_rate' => 5],
            ['name' => '/api/monitoring/overview', 'method' => 'GET', 'weight' => 8, 'base_latency' => 300, 'variance' => 100, 'error_rate' => 3],
            ['name' => '/admin/usuarios', 'method' => 'GET', 'weight' => 7, 'base_latency' => 250, 'variance' => 80, 'error_rate' => 2],
            
            // Lower traffic but complex routes (slower, more variable)
            ['name' => '/proposicoes/{id}/pdf', 'method' => 'GET', 'weight' => 5, 'base_latency' => 800, 'variance' => 300, 'error_rate' => 8],
            ['name' => '/api/relatorios/export', 'method' => 'POST', 'weight' => 3, 'base_latency' => 1200, 'variance' => 500, 'error_rate' => 12],
            ['name' => '/onlyoffice/callback', 'method' => 'POST', 'weight' => 2, 'base_latency' => 600, 'variance' => 250, 'error_rate' => 15],
            
            // Occasional heavy operations (high latency, potential outliers)
            ['name' => '/admin/backup/create', 'method' => 'POST', 'weight' => 1, 'base_latency' => 2000, 'variance' => 1000, 'error_rate' => 20],
            ['name' => '/api/migration/sync', 'method' => 'POST', 'weight' => 1, 'base_latency' => 3000, 'variance' => 1500, 'error_rate' => 25],
            
            // Debug and monitoring routes (low traffic, variable performance)
            ['name' => '/debug/logs', 'method' => 'GET', 'weight' => 2, 'base_latency' => 500, 'variance' => 200, 'error_rate' => 10],
            ['name' => '/admin/monitoring/performance/pxx-last-hour', 'method' => 'GET', 'weight' => 1, 'base_latency' => 350, 'variance' => 120, 'error_rate' => 5],
        ];
    }
    
    /**
     * Select a route based on weighted distribution
     */
    protected function selectWeightedRoute(array $routes): array
    {
        $totalWeight = array_sum(array_column($routes, 'weight'));
        $random = rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($routes as $route) {
            $currentWeight += $route['weight'];
            if ($random <= $currentWeight) {
                return $route;
            }
        }
        
        return $routes[0]; // Fallback
    }
    
    /**
     * Generate realistic latency using Gaussian distribution with outliers
     */
    protected function generateLatencyForRoute(array $route): float
    {
        $baseLatency = $route['base_latency'];
        $variance = $route['variance'];
        
        // 95% of requests follow normal distribution
        if (rand(1, 100) <= 95) {
            // Box-Muller transformation for Gaussian distribution
            $u1 = mt_rand() / mt_getrandmax();
            $u2 = mt_rand() / mt_getrandmax();
            $gaussian = sqrt(-2 * log($u1)) * cos(2 * pi() * $u2);
            
            $latency = $baseLatency + ($gaussian * $variance * 0.5);
        } else {
            // 5% outliers - much higher latency (timeouts, slow queries, etc.)
            $outlierMultiplier = rand(3, 10); // 3x to 10x normal latency
            $latency = $baseLatency * $outlierMultiplier;
        }
        
        // Ensure minimum latency of 10ms and maximum of 30 seconds
        return max(10, min(30000, round($latency, 1)));
    }
    
    /**
     * Generate status code based on route characteristics
     */
    protected function generateStatusCode(array $route): int
    {
        $errorRate = $route['error_rate'];
        $random = rand(1, 100);
        
        if ($random <= $errorRate) {
            // Generate error status codes
            if ($random <= $errorRate * 0.6) {
                // 4xx errors (client errors)
                return collect([400, 401, 403, 404, 422])->random();
            } else {
                // 5xx errors (server errors)
                return collect([500, 502, 503, 504])->random();
            }
        }
        
        // Success status codes (weighted distribution)
        $successCodes = [200 => 80, 201 => 10, 202 => 5, 204 => 5];
        $totalWeight = array_sum($successCodes);
        $random = rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($successCodes as $code => $weight) {
            $currentWeight += $weight;
            if ($random <= $currentWeight) {
                return $code;
            }
        }
        
        return 200; // Fallback
    }
    
    /**
     * Show distribution of generated metrics
     */
    protected function showMetricsDistribution(): void
    {
        $stats = DB::select("
            SELECT 
                (tags->>'route') AS route,
                COUNT(*) as requests,
                ROUND(AVG(value)::numeric, 1) as avg_latency,
                ROUND(percentile_disc(0.95) WITHIN GROUP (ORDER BY value)::numeric, 1) as p95,
                COUNT(CASE WHEN (tags->>'status')::text ~ '^[45]' THEN 1 END) as errors
            FROM monitoring_metrics 
            WHERE metric_name = 'request_duration_ms' 
              AND tags->>'synthetic' = 'true'
            GROUP BY 1
            ORDER BY requests DESC
            LIMIT 10
        ");
        
        $this->command->table(
            ['Route', 'Requests', 'Avg Latency (ms)', 'P95 (ms)', 'Errors'],
            collect($stats)->map(function ($stat) {
                return [
                    $stat->route,
                    $stat->requests,
                    $stat->avg_latency,
                    $stat->p95,
                    $stat->errors,
                ];
            })->toArray()
        );
        
        // Overall system stats
        $overall = DB::selectOne("
            SELECT 
                COUNT(*) as total_requests,
                ROUND(AVG(value)::numeric, 1) as avg_latency,
                ROUND(percentile_disc(0.50) WITHIN GROUP (ORDER BY value)::numeric, 1) as p50,
                ROUND(percentile_disc(0.95) WITHIN GROUP (ORDER BY value)::numeric, 1) as p95,
                ROUND(percentile_disc(0.99) WITHIN GROUP (ORDER BY value)::numeric, 1) as p99,
                COUNT(CASE WHEN (tags->>'status')::text ~ '^5' THEN 1 END) as server_errors
            FROM monitoring_metrics 
            WHERE metric_name = 'request_duration_ms' 
              AND tags->>'synthetic' = 'true'
        ");
        
        if ($overall) {
            $errorRate = $overall->total_requests > 0 ? 
                round(($overall->server_errors / $overall->total_requests) * 100, 2) : 0;
                
            $this->command->info("📈 Overall Statistics:");
            $this->command->info("   Total Requests: {$overall->total_requests}");
            $this->command->info("   P50: {$overall->p50}ms | P95: {$overall->p95}ms | P99: {$overall->p99}ms");
            $this->command->info("   Server Error Rate: {$errorRate}%");
        }
    }
}