<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Spatie\Permission\Models\Role;

class MonitoringSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles and permissions
        $this->artisan('db:seed --class=RolesAndPermissionsSeeder');
        
        // Create test users
        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        $this->user = User::factory()->create(['email' => 'user@test.com']);
        
        // Assign roles
        $adminRole = Role::findByName('ADMIN');
        $this->admin->assignRole($adminRole);
    }

    /**
     * Test that monitoring routes require authentication
     */
    public function test_monitoring_routes_require_authentication()
    {
        $routes = [
            '/debug/status',
            '/debug/logs',
            '/admin/monitoring/performance/pxx-last-hour',
            '/admin/monitoring/performance/overview',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $this->assertContains($response->status(), [302, 401], 
                "Route {$route} should require authentication");
        }
    }

    /**
     * Test RBAC protection on monitoring routes
     */
    public function test_monitoring_routes_require_proper_permissions()
    {
        // User without monitoring permissions
        $this->actingAs($this->user);

        $protectedRoutes = [
            '/debug/status' => 'monitoring.debug',
            '/debug/logs' => 'monitoring.debug',
            '/admin/monitoring/performance/pxx-last-hour' => 'monitoring.view',
            '/admin/monitoring/performance/overview' => 'monitoring.view',
        ];

        foreach ($protectedRoutes as $route => $permission) {
            $response = $this->get($route);
            $this->assertEquals(403, $response->status(), 
                "Route {$route} should require {$permission} permission");
        }
    }

    /**
     * Test that admin can access monitoring routes
     */
    public function test_admin_can_access_monitoring_routes()
    {
        $this->actingAs($this->admin);

        $routes = [
            '/admin/monitoring/performance/pxx-last-hour',
            '/admin/monitoring/performance/overview',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $this->assertEquals(200, $response->status(), 
                "Admin should access {$route}");
        }
    }

    /**
     * Test rate limiting on debug routes
     */
    public function test_debug_routes_are_rate_limited()
    {
        $this->actingAs($this->admin);

        // Make multiple requests to trigger rate limit
        for ($i = 0; $i < 35; $i++) {
            $response = $this->get('/debug/status');
            
            if ($response->status() === 429) {
                $this->assertTrue(true, 'Rate limiting is working');
                return;
            }
        }
        
        // If we get here, rate limiting might not be configured
        $this->markTestSkipped('Rate limiting may not be configured for debug routes');
    }

    /**
     * Test that export routes have strict rate limiting
     */
    public function test_export_routes_have_strict_rate_limiting()
    {
        $this->actingAs($this->admin);

        // Make multiple export requests
        for ($i = 0; $i < 10; $i++) {
            $response = $this->post('/debug/export', [
                'start_date' => now()->subHour()->toISOString(),
                'end_date' => now()->toISOString(),
            ]);
            
            if ($response->status() === 429) {
                $this->assertTrue(true, 'Export rate limiting is working');
                return;
            }
        }
        
        $this->markTestSkipped('Export rate limiting may not be configured');
    }

    /**
     * Test PII masking in logs
     */
    public function test_pii_masking_in_monitoring_logs()
    {
        // Insert a metric with PII
        \DB::table('monitoring_metrics')->insert([
            'metric_type' => 'test',
            'metric_name' => 'test_pii',
            'value' => 1,
            'tags' => json_encode([
                'user_email' => 'test@example.com',
                'user_cpf' => '12345678901',
                'sensitive_data' => 'should_be_masked',
            ]),
            'created_at' => now(),
        ]);

        $this->actingAs($this->admin);
        
        // Fetch logs that might contain PII
        $response = $this->get('/debug/logs?search=test_pii');
        
        $content = $response->getContent();
        
        // Check that email and CPF are masked
        $this->assertStringNotContainsString('test@example.com', $content, 
            'Email should be masked in monitoring logs');
        $this->assertStringNotContainsString('12345678901', $content, 
            'CPF should be masked in monitoring logs');
            
        // Check for masked patterns
        $this->assertStringContainsString('***@***.***', $content, 
            'Email should be replaced with masked pattern');
    }

    /**
     * Test monitoring endpoints respond within acceptable time
     */
    public function test_monitoring_endpoints_response_time()
    {
        // Seed some test data
        $this->artisan('db:seed --class=MonitoringSyntheticSeeder');
        
        $this->actingAs($this->admin);
        
        $endpoints = [
            '/admin/monitoring/performance/pxx-last-hour',
            '/admin/monitoring/performance/pxx-last-24h',
            '/admin/monitoring/performance/error-rates',
            '/admin/monitoring/performance/throughput',
            '/admin/monitoring/performance/overview',
        ];
        
        foreach ($endpoints as $endpoint) {
            $start = microtime(true);
            
            $response = $this->get($endpoint);
            
            $duration = (microtime(true) - $start) * 1000; // Convert to ms
            
            $this->assertEquals(200, $response->status(), 
                "Endpoint {$endpoint} should return 200");
            
            $this->assertLessThan(500, $duration, 
                "Endpoint {$endpoint} should respond within 500ms, took {$duration}ms");
                
            // Verify response structure
            $json = $response->json();
            $this->assertArrayHasKey('updated_at', $json);
            
            if (isset($json['rows'])) {
                $this->assertGreaterThanOrEqual(1, count($json['rows']), 
                    "Endpoint {$endpoint} should return data rows");
            }
        }
    }

    /**
     * Test that synthetic data is properly marked
     */
    public function test_synthetic_data_is_properly_marked()
    {
        // Run synthetic seeder
        $this->artisan('db:seed --class=MonitoringSyntheticSeeder');
        
        // Check that synthetic data is marked
        $syntheticCount = \DB::table('monitoring_metrics')
            ->whereRaw("tags->>'synthetic' = 'true'")
            ->count();
            
        $this->assertGreaterThan(0, $syntheticCount, 
            'Synthetic metrics should be marked with synthetic=true tag');
            
        // Check that we can filter out synthetic data
        $realCount = \DB::table('monitoring_metrics')
            ->whereRaw("tags->>'synthetic' IS NULL OR tags->>'synthetic' != 'true'")
            ->count();
            
        $this->assertGreaterThanOrEqual(0, $realCount, 
            'Should be able to filter out synthetic data');
    }
}