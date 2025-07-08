<?php

namespace App\Console\Commands;

use App\Services\ApiClient\Providers\NodeApiClient;
use App\Services\ApiClient\Exceptions\ApiException;
use Illuminate\Console\Command;

class TestNodeApi extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'api:test-node 
                            {--login : Test login functionality}
                            {--register : Test user registration}
                            {--users : Test user management}
                            {--auth-health : Run authentication health check}
                            {--full : Run all tests}
                            {--email= : Email for login (default: bruno@test.com)}
                            {--password= : Password for login (default: senha123)}
                            {--clear-token : Clear cached JWT token}';

    /**
     * The console command description.
     */
    protected $description = 'Test Node.js API with JWT authentication';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🚀 Testing Node.js API with JWT Authentication");
        $this->info("===============================================");

        // Configurar provider para node_api
        config(['services.api_provider' => 'node_api']);
        app()->forgetInstance(\App\Services\ApiClient\Interfaces\ApiClientInterface::class);
        
        /** @var NodeApiClient $client */
        $client = app(\App\Services\ApiClient\Interfaces\ApiClientInterface::class);
        
        if (!$client instanceof NodeApiClient) {
            $this->error("❌ Expected NodeApiClient, got " . get_class($client));
            return 1;
        }

        $this->showApiInfo($client);

        // Clear token if requested
        if ($this->option('clear-token')) {
            $client->logout();
            $this->info("🔑 JWT token cleared");
            $this->newLine();
        }

        $hasErrors = false;

        // Executar testes baseados nas opções
        if ($this->option('full') || (!$this->hasAnyTestOption())) {
            $hasErrors = $this->runFullTest($client) || $hasErrors;
        } else {
            if ($this->option('auth-health')) {
                $hasErrors = $this->testAuthHealth($client) || $hasErrors;
            }
            
            if ($this->option('register')) {
                $hasErrors = $this->testRegistration($client) || $hasErrors;
            }
            
            if ($this->option('login')) {
                $hasErrors = $this->testLogin($client) || $hasErrors;
            }
            
            if ($this->option('users')) {
                $hasErrors = $this->testUserManagement($client) || $hasErrors;
            }
        }

        $this->newLine();
        if ($hasErrors) {
            $this->error("❌ Some tests failed. Check the output above for details.");
            return 1;
        } else {
            $this->info("✅ All tests passed successfully!");
            return 0;
        }
    }

    /**
     * Verificar se alguma opção de teste foi especificada
     */
    private function hasAnyTestOption(): bool
    {
        return $this->option('login') || 
               $this->option('register') || 
               $this->option('users') || 
               $this->option('auth-health');
    }

    /**
     * Mostrar informações da API
     */
    private function showApiInfo(NodeApiClient $client): void
    {
        $config = $client->getConfig();
        $authStatus = $client->getAuthStatus();
        
        $this->line("🌐 API URL: {$config['base_url']}");
        $this->line("⏱️  Timeout: {$config['timeout']}s");
        $this->line("🔄 Retries: {$config['retries']}");
        $this->line("📧 Default Email: {$config['default_email']}");
        $this->newLine();
        
        $this->line("🔐 Authentication Status:");
        $this->line("   Authenticated: " . ($authStatus['authenticated'] ? '✅ Yes' : '❌ No'));
        $this->line("   Token Present: " . ($authStatus['token_present'] ? '✅ Yes' : '❌ No'));
        $this->line("   Token Cached: " . ($authStatus['token_cached'] ? '✅ Yes' : '❌ No'));
        if ($authStatus['token_length'] > 0) {
            $this->line("   Token Length: {$authStatus['token_length']} chars");
        }
        $this->newLine();
    }

    /**
     * Executar teste completo
     */
    private function runFullTest(NodeApiClient $client): bool
    {
        $hasErrors = false;
        
        $this->info("🧪 Running Full Test Suite");
        $this->line("========================");
        
        $hasErrors = $this->testAuthHealth($client) || $hasErrors;
        $this->newLine();
        
        $hasErrors = $this->testRegistration($client) || $hasErrors;
        $this->newLine();
        
        $hasErrors = $this->testLogin($client) || $hasErrors;
        $this->newLine();
        
        $hasErrors = $this->testUserManagement($client) || $hasErrors;
        
        return $hasErrors;
    }

    /**
     * Testar health check de autenticação
     */
    private function testAuthHealth(NodeApiClient $client): bool
    {
        $this->info("🔍 Testing Authentication Health Check...");
        
        try {
            $results = $client->authHealthCheck();
            
            $this->line("📊 Health Check Results:");
            foreach ($results as $key => $value) {
                if (is_bool($value)) {
                    $status = $value ? '✅' : '❌';
                    $this->line("   {$key}: {$status}");
                } elseif (is_array($value)) {
                    $this->line("   {$key}:");
                    foreach ($value as $subKey => $subValue) {
                        $subStatus = is_bool($subValue) ? ($subValue ? '✅' : '❌') : $subValue;
                        $this->line("     {$subKey}: {$subStatus}");
                    }
                } else {
                    $this->line("   {$key}: {$value}");
                }
            }
            
            $overallSuccess = $results['public_endpoint'] && 
                            ($results['protected_endpoint'] ?? false);
            
            if ($overallSuccess) {
                $this->info("✅ Authentication health check passed");
                return false;
            } else {
                $this->warn("⚠️  Authentication health check has issues");
                return true;
            }
            
        } catch (ApiException $e) {
            $this->error("❌ Health check failed: {$e->getMessage()}");
            return true;
        }
    }

    /**
     * Testar registro de usuários
     */
    private function testRegistration(NodeApiClient $client): bool
    {
        $this->info("👤 Testing User Registration...");
        
        $testEmail = 'test_' . time() . '@test.com';
        $testName = 'Test User ' . time();
        
        try {
            $response = $client->register($testName, $testEmail, 'senha123');
            
            if ($response->isSuccess()) {
                $this->info("✅ User registration successful");
                $this->line("   Name: {$testName}");
                $this->line("   Email: {$testEmail}");
                return false;
            } else {
                $this->error("❌ User registration failed");
                $this->line("   Response: " . json_encode($response->data));
                return true;
            }
            
        } catch (ApiException $e) {
            $this->error("❌ Registration error: {$e->getMessage()}");
            return true;
        }
    }

    /**
     * Testar login
     */
    private function testLogin(NodeApiClient $client): bool
    {
        $this->info("🔑 Testing Login...");
        
        $email = $this->option('email') ?: 'bruno@test.com';
        $password = $this->option('password') ?: 'senha123';
        
        try {
            // Limpar token para forçar novo login
            $client->logout();
            
            $response = $client->login($email, $password);
            
            if ($response->isSuccess()) {
                $this->info("✅ Login successful");
                $this->line("   Email: {$email}");
                
                if (isset($response->data['token'])) {
                    $tokenLength = strlen($response->data['token']);
                    $this->line("   Token: ✅ Received ({$tokenLength} chars)");
                } else {
                    $this->warn("   Token: ⚠️  Not found in response");
                }
                
                $authStatus = $client->getAuthStatus();
                $this->line("   Cached: " . ($authStatus['token_cached'] ? '✅ Yes' : '❌ No'));
                
                return false;
            } else {
                $this->error("❌ Login failed");
                $this->line("   Response: " . json_encode($response->data));
                return true;
            }
            
        } catch (ApiException $e) {
            $this->error("❌ Login error: {$e->getMessage()}");
            return true;
        }
    }

    /**
     * Testar gerenciamento de usuários
     */
    private function testUserManagement(NodeApiClient $client): bool
    {
        $this->info("👥 Testing User Management...");
        
        $hasErrors = false;
        
        // Garantir autenticação
        if (!$client->ensureAuthenticated()) {
            $this->error("❌ Failed to authenticate for user management tests");
            return true;
        }
        
        try {
            // 1. Listar usuários
            $this->line("📋 Getting all users...");
            $usersResponse = $client->getUsers();
            
            if ($usersResponse->isSuccess()) {
                $userCount = count($usersResponse->data);
                $this->info("   ✅ Retrieved {$userCount} users");
            } else {
                $this->error("   ❌ Failed to get users");
                $hasErrors = true;
            }
            
            // 2. Obter usuário específico
            $this->line("👤 Getting specific user (ID: 1)...");
            $userResponse = $client->getUser(1);
            
            if ($userResponse->isSuccess()) {
                $userData = $userResponse->data;
                $this->info("   ✅ User retrieved: {$userData['name']} ({$userData['email']})");
            } else {
                $this->error("   ❌ Failed to get user with ID 1");
                $hasErrors = true;
            }
            
            // 3. Criar novo usuário
            $testName = 'Admin Test ' . time();
            $testEmail = 'admin_' . time() . '@test.com';
            
            $this->line("➕ Creating new user...");
            $createResponse = $client->createUser($testName, $testEmail);
            
            if ($createResponse->isSuccess()) {
                $newUserId = $createResponse->data['id'] ?? null;
                $this->info("   ✅ User created: {$testName} (ID: {$newUserId})");
                
                // 4. Atualizar usuário criado
                if ($newUserId) {
                    $this->line("✏️  Updating created user...");
                    $updateResponse = $client->updateUser($newUserId, $testName . ' Updated');
                    
                    if ($updateResponse->isSuccess()) {
                        $this->info("   ✅ User updated successfully");
                    } else {
                        $this->error("   ❌ Failed to update user");
                        $hasErrors = true;
                    }
                    
                    // 5. Deletar usuário criado (limpeza)
                    $this->line("🗑️  Deleting test user...");
                    $deleteResponse = $client->deleteUser($newUserId);
                    
                    if ($deleteResponse->isSuccess()) {
                        $this->info("   ✅ User deleted successfully");
                    } else {
                        $this->error("   ❌ Failed to delete user");
                        $hasErrors = true;
                    }
                }
            } else {
                $this->error("   ❌ Failed to create user");
                $this->line("   Response: " . json_encode($createResponse->data));
                $hasErrors = true;
            }
            
        } catch (ApiException $e) {
            $this->error("❌ User management error: {$e->getMessage()}");
            $hasErrors = true;
        }
        
        return $hasErrors;
    }
} 