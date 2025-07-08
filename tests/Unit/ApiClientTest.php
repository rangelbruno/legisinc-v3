<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use App\Services\ApiClient\Interfaces\ApiClientInterface;
use App\Services\ApiClient\Providers\JsonPlaceholderClient;
use App\Services\ApiClient\DTOs\ApiResponse;
use App\Services\ApiClient\Exceptions\ApiException;

class ApiClientTest extends TestCase
{
    private ApiClientInterface $client;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurar cliente de teste
        $config = [
            'base_url' => 'https://jsonplaceholder.typicode.com',
            'token' => '',
            'timeout' => 30,
            'retries' => 3,
            'cache_ttl' => 300,
            'provider_name' => 'jsonplaceholder_test',
        ];
        
        $this->client = new JsonPlaceholderClient($config);
    }

    public function test_client_implements_interface(): void
    {
        $this->assertInstanceOf(ApiClientInterface::class, $this->client);
    }

    public function test_client_returns_config(): void
    {
        $config = $this->client->getConfig();
        
        $this->assertIsArray($config);
        $this->assertArrayHasKey('provider_name', $config);
        $this->assertArrayHasKey('base_url', $config);
        $this->assertEquals('jsonplaceholder_test', $config['provider_name']);
    }

    public function test_successful_get_request(): void
    {
        Http::fake([
            'jsonplaceholder.typicode.com/posts' => Http::response([
                ['id' => 1, 'title' => 'Test Post', 'body' => 'Test Body']
            ], 200)
        ]);

        $response = $this->client->get('/posts');

        $this->assertInstanceOf(ApiResponse::class, $response);
        $this->assertTrue($response->isSuccess());
        $this->assertEquals(200, $response->statusCode);
        $this->assertIsArray($response->data);
    }

    public function test_successful_post_request(): void
    {
        Http::fake([
            'jsonplaceholder.typicode.com/posts' => Http::response([
                'id' => 101,
                'title' => 'New Post',
                'body' => 'New Body'
            ], 201)
        ]);

        $payload = ['title' => 'New Post', 'body' => 'New Body'];
        $response = $this->client->post('/posts', $payload);

        $this->assertInstanceOf(ApiResponse::class, $response);
        $this->assertTrue($response->isSuccess());
        $this->assertEquals(201, $response->statusCode);
    }

    public function test_api_exception_on_connection_error(): void
    {
        Http::fake([
            'jsonplaceholder.typicode.com/*' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection failed');
            }
        ]);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Connection error');

        $this->client->get('/posts');
    }

    public function test_api_exception_on_401_error(): void
    {
        Http::fake([
            'jsonplaceholder.typicode.com/posts' => Http::response([], 401)
        ]);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Authentication failed');

        $this->client->get('/posts');
    }

    public function test_health_check_success(): void
    {
        Http::fake([
            'jsonplaceholder.typicode.com/posts/1' => Http::response(['id' => 1], 200)
        ]);

        $isHealthy = $this->client->healthCheck();

        $this->assertTrue($isHealthy);
    }

    public function test_health_check_failure(): void
    {
        Http::fake([
            'jsonplaceholder.typicode.com/posts/1' => Http::response([], 500)
        ]);

        $isHealthy = $this->client->healthCheck();

        $this->assertFalse($isHealthy);
    }

    public function test_api_response_dto_success(): void
    {
        $data = ['key' => 'value'];
        $response = ApiResponse::success($data, 200, 'Success message');

        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isError());
        $this->assertEquals($data, $response->getData());
        $this->assertEquals(200, $response->statusCode);
        $this->assertEquals('Success message', $response->message);
    }

    public function test_api_response_dto_error(): void
    {
        $response = ApiResponse::error('Error message', 400);

        $this->assertFalse($response->isSuccess());
        $this->assertTrue($response->isError());
        $this->assertEquals(400, $response->statusCode);
        $this->assertEquals('Error message', $response->message);
    }

    public function test_api_exception_context(): void
    {
        $exception = new ApiException(
            'Test error',
            400,
            ['error' => 'details'],
            'test_provider'
        );

        $context = $exception->getContext();

        $this->assertIsArray($context);
        $this->assertArrayHasKey('message', $context);
        $this->assertArrayHasKey('status_code', $context);
        $this->assertArrayHasKey('provider', $context);
        $this->assertEquals('Test error', $context['message']);
        $this->assertEquals(400, $context['status_code']);
        $this->assertEquals('test_provider', $context['provider']);
    }

    public function test_service_container_binding(): void
    {
        // Configurar provider no config
        config(['services.api_provider' => 'jsonplaceholder']);
        
        // Resolver do container
        $client = app(ApiClientInterface::class);
        
        $this->assertInstanceOf(ApiClientInterface::class, $client);
        $this->assertInstanceOf(JsonPlaceholderClient::class, $client);
    }

    public function test_provider_switching(): void
    {
        // Testar jsonplaceholder
        config(['services.api_provider' => 'jsonplaceholder']);
        app()->forgetInstance(ApiClientInterface::class);
        $client1 = app(ApiClientInterface::class);
        $this->assertInstanceOf(JsonPlaceholderClient::class, $client1);

        // Testar example_api
        config(['services.api_provider' => 'example_api']);
        app()->forgetInstance(ApiClientInterface::class);
        $client2 = app(ApiClientInterface::class);
        $this->assertInstanceOf(\App\Services\ApiClient\Providers\ExampleApiClient::class, $client2);
    }
} 