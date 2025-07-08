<?php

namespace App\Console\Commands;

use App\Services\ApiClient\Interfaces\ApiClientInterface;
use App\Services\ApiClient\Exceptions\ApiException;
use Illuminate\Console\Command;

class TestApiClient extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'api:test 
                            {--provider= : Provider to test (jsonplaceholder, example_api, node_api)}
                            {--endpoint= : Endpoint to test}
                            {--method=GET : HTTP method (GET, POST, PUT, DELETE)}
                            {--data= : JSON data for POST/PUT requests}
                            {--health : Only run health check}';

    /**
     * The console command description.
     */
    protected $description = 'Test API client architecture with different providers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $provider = $this->option('provider');
        
        // Switch provider if specified
        if ($provider) {
            if (!in_array($provider, ['jsonplaceholder', 'example_api', 'node_api'])) {
                $this->error('Invalid provider. Available: jsonplaceholder, example_api, node_api');
                return 1;
            }
            
            config(['services.api_provider' => $provider]);
            app()->forgetInstance(ApiClientInterface::class);
        }

        $client = app(ApiClientInterface::class);
        $config = $client->getConfig();
        
        $this->info("Testing API Client Architecture");
        $this->info("================================");
        $this->line("Provider: {$config['provider_name']}");
        $this->line("Base URL: {$config['base_url']}");
        $this->line("Timeout: {$config['timeout']}s");
        $this->line("Retries: {$config['retries']}");
        $this->line("Cache TTL: {$config['cache_ttl']}s");
        $this->newLine();

        // Health check
        $this->info("🔍 Testing Health Check...");
        try {
            $startTime = microtime(true);
            $isHealthy = $client->healthCheck();
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            if ($isHealthy) {
                $this->info("✅ Health Check: PASSED ({$responseTime}ms)");
            } else {
                $this->warn("⚠️  Health Check: FAILED ({$responseTime}ms)");
            }
        } catch (ApiException $e) {
            $this->error("❌ Health Check: ERROR - {$e->getMessage()}");
        }

        // If only health check requested
        if ($this->option('health')) {
            return 0;
        }

        $this->newLine();

        // Test specific endpoint if provided
        if ($endpoint = $this->option('endpoint')) {
            $method = strtoupper($this->option('method'));
            $data = $this->option('data') ? json_decode($this->option('data'), true) : [];

            $this->info("🌐 Testing {$method} request to: {$endpoint}");
            
            try {
                $startTime = microtime(true);
                
                $response = match ($method) {
                    'GET' => $client->get($endpoint, $data),
                    'POST' => $client->post($endpoint, $data),
                    'PUT' => $client->put($endpoint, $data),
                    'DELETE' => $client->delete($endpoint, $data),
                    default => throw new \InvalidArgumentException("Unsupported method: {$method}")
                };
                
                $responseTime = round((microtime(true) - $startTime) * 1000, 2);
                
                $this->info("✅ Request successful ({$responseTime}ms)");
                $this->line("Status Code: {$response->statusCode}");
                $this->line("Response Time: {$response->responseTime}s");
                $this->line("Data Size: " . strlen(json_encode($response->data)) . " bytes");
                
                if ($this->option('verbose')) {
                    $this->newLine();
                    $this->line("Response Data:");
                    $this->line(json_encode($response->data, JSON_PRETTY_PRINT));
                }
                
            } catch (ApiException $e) {
                $this->error("❌ Request failed: {$e->getMessage()}");
                if ($this->option('verbose')) {
                    $this->line("Context: " . json_encode($e->getContext(), JSON_PRETTY_PRINT));
                }
            }
        } else {
            // Run default tests based on provider
            $this->runDefaultTests($client);
        }

        return 0;
    }

    /**
     * Run default tests based on provider
     */
    private function runDefaultTests(ApiClientInterface $client): void
    {
        $config = $client->getConfig();
        
        if ($config['provider_name'] === 'jsonplaceholder') {
            $this->testJsonPlaceholder($client);
        } elseif ($config['provider_name'] === 'node_api') {
            $this->info("🚀 For comprehensive Node.js API testing, use: php artisan api:test-node");
            $this->testNodeApiBasic($client);
        } else {
            $this->info("💡 Use --endpoint option to test specific endpoints for this provider");
        }
    }

    /**
     * Test JSONPlaceholder specific endpoints
     */
    private function testJsonPlaceholder(ApiClientInterface $client): void
    {
        $tests = [
            ['method' => 'GET', 'endpoint' => '/posts', 'description' => 'Get all posts'],
            ['method' => 'GET', 'endpoint' => '/posts/1', 'description' => 'Get specific post'],
            ['method' => 'GET', 'endpoint' => '/users', 'description' => 'Get all users'],
        ];

        foreach ($tests as $test) {
            $this->info("🧪 {$test['description']}...");
            
            try {
                $startTime = microtime(true);
                $response = $client->get($test['endpoint']);
                $responseTime = round((microtime(true) - $startTime) * 1000, 2);
                
                $this->info("   ✅ Success ({$responseTime}ms) - Status: {$response->statusCode}");
                
            } catch (ApiException $e) {
                $this->error("   ❌ Failed: {$e->getMessage()}");
            }
        }

        // Test caching
        $this->newLine();
        $this->info("🧪 Testing cache functionality...");
        
        try {
            // First request (should hit API)
            $startTime = microtime(true);
            $client->get('/posts/1');
            $firstTime = round((microtime(true) - $startTime) * 1000, 2);
            
            // Second request (should hit cache)
            $startTime = microtime(true);
            $client->get('/posts/1');
            $secondTime = round((microtime(true) - $startTime) * 1000, 2);
            
            $this->info("   ✅ First request: {$firstTime}ms");
            $this->info("   ✅ Cached request: {$secondTime}ms");
            
            if ($secondTime < $firstTime) {
                $this->info("   🚀 Cache is working! Speedup: " . round($firstTime / $secondTime, 2) . "x");
            }
            
        } catch (ApiException $e) {
            $this->error("   ❌ Cache test failed: {$e->getMessage()}");
        }
    }

    /**
     * Test Node API basic functionality
     */
    private function testNodeApiBasic(ApiClientInterface $client): void
    {
        $this->info("🧪 Testing Node.js API basic functionality...");
        
        // Test health check
        try {
            $this->info("🔍 Health check...");
            $startTime = microtime(true);
            $response = $client->get('/');
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            $this->info("   ✅ Success ({$responseTime}ms) - Status: {$response->statusCode}");
            
        } catch (ApiException $e) {
            $this->error("   ❌ Failed: {$e->getMessage()}");
        }
        
        // Test authentication status if it's NodeApiClient
        if ($client instanceof \App\Services\ApiClient\Providers\NodeApiClient) {
            $authStatus = $client->getAuthStatus();
            $this->info("🔐 Authentication Status:");
            $this->line("   Authenticated: " . ($authStatus['authenticated'] ? '✅' : '❌'));
            $this->line("   Token Present: " . ($authStatus['token_present'] ? '✅' : '❌'));
        }
        
        $this->newLine();
        $this->info("💡 For full Node.js API testing including authentication:");
        $this->line("   php artisan api:test-node --full");
        $this->line("   php artisan api:test-node --login");
        $this->line("   php artisan api:test-node --users");
        $this->line("   php artisan api:test-node --auth-health");
    }
} 