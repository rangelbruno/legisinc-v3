<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ApiClient\Providers\NodeApiClient;
use App\Services\ApiClient\Exceptions\ApiException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NodeApiClientTest extends TestCase
{
    private NodeApiClient $client;
    private array $config;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->config = [
            'base_url' => 'http://localhost:3000',
            'token' => '',
            'timeout' => 30,
            'retries' => 3,
            'cache_ttl' => 300,
            'provider_name' => 'node_api',
            'default_email' => 'bruno@test.com',
            'default_password' => 'senha123',
        ];

        $this->client = new NodeApiClient($this->config);
        
        // Limpar cache entre testes
        Cache::flush();
    }

    /** @test */
    public function it_can_initialize_with_config()
    {
        $this->assertInstanceOf(NodeApiClient::class, $this->client);
        $this->assertEquals($this->config, $this->client->getConfig());
    }

    /** @test */
    public function it_can_perform_health_check()
    {
        Http::fake([
            'localhost:3000/*' => Http::response([
                'status' => 'ok',
                'message' => 'API is running'
            ], 200)
        ]);

                 $response = $this->client->get('/');
        
        $this->assertTrue($response->isSuccess());
        $this->assertEquals(200, $response->statusCode);
    }

    /** @test */
    public function it_can_register_user()
    {
        Http::fake([
            'localhost:3000/register' => Http::response([
                'id' => 1,
                'name' => 'Test User',
                'email' => 'test@test.com',
                'message' => 'User registered successfully'
            ], 201)
        ]);

        $response = $this->client->register('Test User', 'test@test.com', 'senha123');
        
        $this->assertTrue($response->isSuccess());
        $this->assertEquals(201, $response->statusCode);
        $this->assertEquals('Test User', $response->data['name']);
    }

    /** @test */
    public function it_can_login_and_store_token()
    {
        $mockToken = 'mock-jwt-token-12345';
        
        Http::fake([
            'localhost:3000/login' => Http::response([
                'token' => $mockToken,
                'user' => [
                    'id' => 1,
                    'name' => 'Bruno Silva',
                    'email' => 'bruno@test.com'
                ]
            ], 200)
        ]);

        $response = $this->client->login('bruno@test.com', 'senha123');
        
        $this->assertTrue($response->isSuccess());
        $this->assertEquals($mockToken, $response->data['token']);
        $this->assertTrue($this->client->isAuthenticated());
        $this->assertEquals($mockToken, $this->client->getToken());
        
        // Verificar se token foi cacheado
        $this->assertTrue(Cache::has('node_api_jwt_token'));
        $this->assertEquals($mockToken, Cache::get('node_api_jwt_token'));
    }

    /** @test */
    public function it_can_logout_and_clear_token()
    {
        // Primeiro fazer login
        $this->client->setToken('mock-token');
        $this->assertTrue($this->client->isAuthenticated());
        
        // Fazer logout
        $this->client->logout();
        
        $this->assertFalse($this->client->isAuthenticated());
        $this->assertNull($this->client->getToken());
        $this->assertFalse(Cache::has('node_api_jwt_token'));
    }

    /** @test */
    public function it_can_auto_login()
    {
        $mockToken = 'auto-login-token-12345';
        
        Http::fake([
            'localhost:3000/login' => Http::response([
                'token' => $mockToken,
                'user' => [
                    'id' => 1,
                    'name' => 'Bruno Silva',
                    'email' => 'bruno@test.com'
                ]
            ], 200)
        ]);

        $response = $this->client->autoLogin();
        
        $this->assertTrue($response->isSuccess());
        $this->assertTrue($this->client->isAuthenticated());
        $this->assertEquals($mockToken, $this->client->getToken());
    }

    /** @test */
    public function it_can_get_users_with_auth()
    {
        $mockUsers = [
            ['id' => 1, 'name' => 'User 1', 'email' => 'user1@test.com'],
            ['id' => 2, 'name' => 'User 2', 'email' => 'user2@test.com']
        ];

        // Simular que já está autenticado
        $this->client->setToken('mock-token');

        Http::fake([
            'localhost:3000/users' => Http::response($mockUsers, 200)
        ]);

        $response = $this->client->getUsers();
        
        $this->assertTrue($response->isSuccess());
        $this->assertCount(2, $response->data);
        $this->assertEquals('User 1', $response->data[0]['name']);
    }

    /** @test */
    public function it_can_get_specific_user()
    {
        $mockUser = ['id' => 1, 'name' => 'Specific User', 'email' => 'specific@test.com'];

        $this->client->setToken('mock-token');

        Http::fake([
            'localhost:3000/users/1' => Http::response($mockUser, 200)
        ]);

        $response = $this->client->getUser(1);
        
        $this->assertTrue($response->isSuccess());
        $this->assertEquals('Specific User', $response->data['name']);
        $this->assertEquals(1, $response->data['id']);
    }

    /** @test */
    public function it_can_create_user()
    {
        $mockUser = ['id' => 3, 'name' => 'New User', 'email' => 'new@test.com'];

        $this->client->setToken('mock-token');

        Http::fake([
            'localhost:3000/users' => Http::response($mockUser, 201)
        ]);

        $response = $this->client->createUser('New User', 'new@test.com');
        
        $this->assertTrue($response->isSuccess());
        $this->assertEquals(201, $response->statusCode);
        $this->assertEquals('New User', $response->data['name']);
    }

    /** @test */
    public function it_can_update_user()
    {
        $mockUser = ['id' => 1, 'name' => 'Updated User', 'email' => 'updated@test.com'];

        $this->client->setToken('mock-token');

        Http::fake([
            'localhost:3000/users/1' => Http::response($mockUser, 200)
        ]);

        $response = $this->client->updateUser(1, 'Updated User', 'updated@test.com');
        
        $this->assertTrue($response->isSuccess());
        $this->assertEquals('Updated User', $response->data['name']);
    }

    /** @test */
    public function it_can_delete_user()
    {
        $this->client->setToken('mock-token');

        Http::fake([
            'localhost:3000/users/1' => Http::response(['message' => 'User deleted'], 200)
        ]);

        $response = $this->client->deleteUser(1);
        
        $this->assertTrue($response->isSuccess());
        $this->assertEquals('User deleted', $response->data['message']);
    }

    /** @test */
    public function it_throws_exception_for_protected_endpoints_without_auth()
    {
        // Garantir que não está autenticado
        $this->client->logout();

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Authentication failed for node_api');

        $this->client->getUsers();
    }

    /** @test */
    public function it_can_use_with_auth_method()
    {
        $mockUsers = [['id' => 1, 'name' => 'User 1', 'email' => 'user1@test.com']];

        Http::fake([
            'localhost:3000/login' => Http::response([
                'token' => 'auto-token',
                'user' => ['id' => 1, 'name' => 'Bruno Silva', 'email' => 'bruno@test.com']
            ], 200),
            'localhost:3000/users' => Http::response($mockUsers, 200)
        ]);

        $response = $this->client->withAuth(function($api) {
            return $api->getUsers();
        });
        
        $this->assertTrue($response->isSuccess());
        $this->assertCount(1, $response->data);
    }

    /** @test */
    public function it_provides_auth_status()
    {
        // Sem autenticação
        $status = $this->client->getAuthStatus();
        $this->assertFalse($status['authenticated']);
        $this->assertFalse($status['token_present']);
        $this->assertFalse($status['token_cached']);
        $this->assertEquals(0, $status['token_length']);

        // Com autenticação
        $this->client->setToken('test-token-12345');
        $status = $this->client->getAuthStatus();
        $this->assertTrue($status['authenticated']);
        $this->assertTrue($status['token_present']);
        $this->assertTrue($status['token_cached']);
        $this->assertEquals(17, $status['token_length']);
    }

    /** @test */
    public function it_can_perform_auth_health_check()
    {
        Http::fake([
            'localhost:3000/' => Http::response(['status' => 'ok'], 200),
            'localhost:3000/login' => Http::response([
                'token' => 'health-token',
                'user' => ['id' => 1, 'name' => 'Bruno', 'email' => 'bruno@test.com']
            ], 200),
            'localhost:3000/users' => Http::response([
                ['id' => 1, 'name' => 'Test User', 'email' => 'test@test.com']
            ], 200)
        ]);

        $results = $this->client->authHealthCheck();
        
        $this->assertTrue($results['public_endpoint']);
        $this->assertArrayHasKey('auth_status', $results);
        $this->assertTrue($results['auto_login']);
        $this->assertTrue($results['protected_endpoint']);
    }

    /** @test */
    public function it_includes_auth_headers_when_authenticated()
    {
        $this->client->setToken('test-token-123');

        Http::fake([
            'localhost:3000/users' => function ($request) {
                $this->assertArrayHasKey('Authorization', $request->headers());
                $this->assertEquals('Bearer test-token-123', $request->header('Authorization')[0]);
                
                return Http::response([['id' => 1, 'name' => 'User 1']], 200);
            }
        ]);

        $response = $this->client->getUsers();
        $this->assertTrue($response->isSuccess());
    }

    /** @test */
    public function it_handles_login_failure()
    {
        Http::fake([
            'localhost:3000/login' => Http::response([
                'error' => 'Invalid credentials'
            ], 401)
        ]);

        $response = $this->client->login('wrong@email.com', 'wrongpassword');
        
        $this->assertFalse($response->isSuccess());
        $this->assertEquals(401, $response->statusCode);
        $this->assertFalse($this->client->isAuthenticated());
    }

    /** @test */
    public function it_recovers_token_from_cache()
    {
        // Simular token em cache
        Cache::put('node_api_jwt_token', 'cached-token-123', now()->addHours(1));

        // Criar nova instância (simula nova requisição)
        $newClient = new NodeApiClient($this->config);
        
        $this->assertTrue($newClient->isAuthenticated());
        $this->assertEquals('cached-token-123', $newClient->getToken());
    }

    /** @test */
    public function it_can_ensure_authentication()
    {
        Http::fake([
            'localhost:3000/login' => Http::response([
                'token' => 'ensure-auth-token',
                'user' => ['id' => 1, 'name' => 'Bruno Silva', 'email' => 'bruno@test.com']
            ], 200)
        ]);

        // Deve fazer login automaticamente se não estiver autenticado
        $this->assertFalse($this->client->isAuthenticated());
        
        $result = $this->client->ensureAuthenticated();
        
        $this->assertTrue($result);
        $this->assertTrue($this->client->isAuthenticated());
    }

    /** @test */
    public function it_handles_ensure_authentication_failure()
    {
        Http::fake([
            'localhost:3000/login' => Http::response([
                'error' => 'Invalid credentials'
            ], 401)
        ]);

        $result = $this->client->ensureAuthenticated();
        
        $this->assertFalse($result);
        $this->assertFalse($this->client->isAuthenticated());
    }
} 