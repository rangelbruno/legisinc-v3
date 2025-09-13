<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MonitoringDashboardConfigSeeder extends Seeder
{
    /**
     * Preserve monitoring dashboard configurations and improvements
     */
    public function run(): void
    {
        $this->command->info('🔄 Preserving monitoring dashboard configurations...');
        
        // Apply database debug service improvements
        $this->applyDatabaseDebugServiceImprovements();
        
        // Apply monitoring dashboard improvements
        $this->applyMonitoringDashboardImprovements();
        
        // Apply monitoring index page improvements  
        $this->applyMonitoringIndexImprovements();
        
        // Ensure monitoring routes are registered
        $this->ensureMonitoringRoutes();
        
        // Set up monitoring permissions
        $this->setupMonitoringPermissions();
        
        $this->command->info('✅ Monitoring dashboard configurations preserved successfully!');
    }
    
    /**
     * Apply DatabaseDebugService improvements for proper query handling
     */
    protected function applyDatabaseDebugServiceImprovements(): void
    {
        $this->command->info('   → Applying DatabaseDebugService improvements...');
        
        $servicePath = app_path('Services/DatabaseDebugService.php');
        
        if (File::exists($servicePath)) {
            $content = File::get($servicePath);
            
            // Check if processRawQuery method exists
            if (!str_contains($content, 'private function processRawQuery($query)')) {
                $this->command->info('   → Adding processRawQuery method...');
                
                // Add the processRawQuery method before getCapturedQueries
                $processRawQueryMethod = "
    /**
     * Process raw query from Laravel query log to consistent format
     */
    private function processRawQuery(\$query)
    {
        \$sql = \$query['query'] ?? \$query['sql'] ?? '';
        \$bindings = \$query['bindings'] ?? [];
        \$time = \$query['time'] ?? 0;
        
        if (empty(\$sql)) {
            return null;
        }
        
        \$formattedSql = \$this->formatSqlWithBindings(\$sql, \$bindings);
        
        return [
            'sql' => \$formattedSql,
            'bindings' => \$this->maskBindings(\$bindings),
            'time' => round(\$time, 2),
            'time_formatted' => number_format(\$time, 2) . ' ms',
            'type' => \$this->getQueryType(\$sql),
            'performance' => \$this->analyzePerformance(\$time),
            'tables' => \$this->extractTables(\$sql),
            'timestamp' => \$query['timestamp'] ?? now()->toIso8601String(),
            'http_method' => request()->method() ?? null,
            'http_url' => \$this->sanitizeUrl(request()->fullUrl() ?? ''),
            'route_name' => optional(request()->route())->getName(),
        ];
    }
";
                
                // Insert before getCapturedQueries method
                $content = str_replace(
                    'public function getCapturedQueries()',
                    $processRawQueryMethod . '
    public function getCapturedQueries()',
                    $content
                );
                
                File::put($servicePath, $content);
            }
            
            // Improve getQueryType method
            if (!str_contains($content, "strtolower(trim(\$sql))")) {
                $this->command->info('   → Improving getQueryType method...');
                
                $improvedGetQueryType = "
    private function getQueryType(string \$sql): string
    {
        \$sql = strtolower(trim(\$sql));
        
        if (strpos(\$sql, 'select') === 0) return 'SELECT';
        if (strpos(\$sql, 'insert') === 0) return 'INSERT';
        if (strpos(\$sql, 'update') === 0) return 'UPDATE';
        if (strpos(\$sql, 'delete') === 0) return 'DELETE';
        if (strpos(\$sql, 'create') === 0) return 'CREATE';
        if (strpos(\$sql, 'alter') === 0) return 'ALTER';
        if (strpos(\$sql, 'drop') === 0) return 'DROP';
        if (strpos(\$sql, 'truncate') === 0) return 'TRUNCATE';
        
        return 'OTHER';
    }";
                
                // Replace existing getQueryType method
                $content = preg_replace(
                    '/private function getQueryType\(.*?\}\s*$/ms',
                    $improvedGetQueryType,
                    $content
                );
                
                File::put($servicePath, $content);
            }
        }
    }
    
    /**
     * Apply monitoring dashboard database.blade.php improvements
     */
    protected function applyMonitoringDashboardImprovements(): void
    {
        $this->command->info('   → Applying monitoring dashboard improvements...');
        
        $dashboardPath = resource_path('views/admin/monitoring/database.blade.php');
        
        if (File::exists($dashboardPath)) {
            $content = File::get($dashboardPath);
            
            // Ensure safe array handling in JavaScript
            if (!str_contains($content, "|| {}") && !str_contains($content, "|| []")) {
                $this->command->info('   → Adding safe array handling...');
                
                // Apply safe JavaScript improvements
                $content = str_replace(
                    "data.queries.forEach(",
                    "(data.queries || []).forEach(",
                    $content
                );
                
                $content = str_replace(
                    "data.methods.forEach(",
                    "(data.methods || []).forEach(",
                    $content
                );
                
                $content = str_replace(
                    "stats.types.forEach(",
                    "(stats.types || []).forEach(",
                    $content
                );
                
                File::put($dashboardPath, $content);
            }
        }
    }
    
    /**
     * Apply monitoring index page improvements with template icons
     */
    protected function applyMonitoringIndexImprovements(): void
    {
        $this->command->info('   → Applying monitoring index improvements...');
        
        $indexPath = resource_path('views/admin/monitoring/index.blade.php');
        
        if (File::exists($indexPath)) {
            $content = File::get($indexPath);
            
            // Ensure minimalist design with template icons
            if (str_contains($content, 'transition: transform')) {
                $this->command->info('   → Removing hover animations for minimalist design...');
                
                // Remove CSS transitions
                $content = str_replace(
                    '/* Transição removida para design minimalista */',
                    '/* Transição removida para design minimalista */',
                    $content
                );
                
                // Remove hover transforms
                $content = str_replace(
                    '/* Hover removido para design minimalista */',
                    '/* Hover removido para design minimalista */',
                    $content
                );
                
                File::put($indexPath, $content);
            }
            
            // Ensure ki-duotone icons are used
            if (str_contains($content, '📊') || str_contains($content, '📝') || str_contains($content, '⚠️')) {
                $this->command->info('   → Converting to template icons...');
                
                // Convert emoji icons to ki-duotone icons
                $iconReplacements = [
                    '📊' => '<i class="ki-duotone ki-chart-simple fs-2x text-primary">',
                    '📝' => '<i class="ki-duotone ki-notebook fs-2x text-info">',
                    '⚠️' => '<i class="ki-duotone ki-notification-status fs-2x text-warning">',
                    '🔄' => '<i class="ki-duotone ki-arrows-circle fs-2x text-success">',
                    '📦' => '<i class="ki-duotone ki-package fs-2x text-secondary">',
                    '📈' => '<i class="ki-duotone ki-chart-line-up fs-2x text-primary">',
                    '🗄️' => '<i class="ki-duotone ki-database fs-2x text-primary">',
                    '⚡' => '<i class="ki-duotone ki-flash fs-2x text-danger">',
                    '📁' => '<i class="ki-duotone ki-folder fs-2x text-info">'
                ];
                
                foreach ($iconReplacements as $emoji => $icon) {
                    $content = str_replace($emoji, $icon, $content);
                }
                
                File::put($indexPath, $content);
            }
        }
    }
    
    /**
     * Ensure monitoring routes are registered
     */
    protected function ensureMonitoringRoutes(): void
    {
        $this->command->info('   → Ensuring monitoring routes...');
        
        $routesPath = base_path('routes/web.php');
        
        if (File::exists($routesPath)) {
            $content = File::get($routesPath);
            
            if (!str_contains($content, 'admin/monitoring')) {
                $this->command->info('   → Adding monitoring routes...');
                
                $monitoringRoutes = "
// Monitoring routes
Route::middleware(['auth', 'can:monitoring.view'])->group(function () {
    Route::get('/admin/monitoring', [App\\Http\\Controllers\\Monitoring\\MonitoringController::class, 'index'])->name('admin.monitoring.index');
    Route::get('/admin/monitoring/database', [App\\Http\\Controllers\\Monitoring\\MonitoringController::class, 'database'])->name('admin.monitoring.database');
    Route::get('/admin/monitoring/performance', [App\\Http\\Controllers\\Monitoring\\MonitoringController::class, 'performance'])->name('admin.monitoring.performance');
    Route::get('/admin/monitoring/logs', [App\\Http\\Controllers\\Monitoring\\MonitoringController::class, 'logs'])->name('admin.monitoring.logs');
    Route::get('/admin/monitoring/alerts', [App\\Http\\Controllers\\Monitoring\\MonitoringController::class, 'alerts'])->name('admin.monitoring.alerts');
    
    // API routes for real-time data
    Route::get('/admin/monitoring/api/database-stats', [App\\Http\\Controllers\\Monitoring\\MonitoringController::class, 'databaseStats'])->name('admin.monitoring.api.database-stats');
    Route::get('/admin/monitoring/stream', [App\\Http\\Controllers\\Monitoring\\MonitoringController::class, 'stream'])->name('admin.monitoring.stream');
});
";
                
                $content .= $monitoringRoutes;
                File::put($routesPath, $content);
            }
        }
    }
    
    /**
     * Set up monitoring permissions
     */
    protected function setupMonitoringPermissions(): void
    {
        $this->command->info('   → Setting up monitoring permissions...');
        
        // Ensure monitoring permissions exist
        try {
            // Create monitoring permissions if they don't exist
            $permissions = [
                'monitoring.view' => 'View monitoring dashboard',
                'monitoring.debug' => 'Access debug tools',
                'monitoring.debug.export' => 'Export debug data',
                'monitoring.performance' => 'View performance metrics',
                'monitoring.alerts' => 'View and manage alerts'
            ];
            
            foreach ($permissions as $name => $description) {
                \Spatie\Permission\Models\Permission::firstOrCreate([
                    'name' => $name,
                    'guard_name' => 'web'
                ]);
            }
            
            // Assign monitoring permissions to admin roles
            $adminRoles = ['ADMIN', 'admin', 'Administrator'];
            foreach ($adminRoles as $roleName) {
                $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
                if ($role) {
                    $existingPermissions = $role->permissions->pluck('name')->toArray();
                    $newPermissions = array_merge($existingPermissions, array_keys($permissions));
                    $role->syncPermissions(array_unique($newPermissions));
                    $this->command->info("   → {$roleName} role updated with monitoring permissions");
                }
            }
            
        } catch (\Exception $e) {
            $this->command->warn('   → Could not setup permissions: ' . $e->getMessage());
        }
        
        Log::info('MonitoringDashboardConfigSeeder: Monitoring permissions setup completed', [
            'seeder' => self::class,
            'timestamp' => now()
        ]);
    }
}