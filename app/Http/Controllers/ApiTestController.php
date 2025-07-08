<?php

namespace App\Http\Controllers;

use App\Services\ApiClient\Interfaces\ApiClientInterface;
use App\Services\ApiClient\Exceptions\ApiException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiTestController extends Controller
{
    public function __construct(
        private ApiClientInterface $apiClient
    ) {}

    /**
     * Exibir página de testes
     */
    public function index()
    {
        $config = $this->apiClient->getConfig();
        return view('api-test.index', compact('config'));
    }

    /**
     * Testar health check
     */
    public function healthCheck(): JsonResponse
    {
        try {
            $isHealthy = $this->apiClient->healthCheck();
            
            return response()->json([
                'success' => true,
                'healthy' => $isHealthy,
                'provider' => $this->apiClient->getConfig()['provider_name'],
                'message' => $isHealthy ? 'API is healthy' : 'API is not responding'
            ]);
        } catch (ApiException $e) {
            return response()->json([
                'success' => false,
                'healthy' => false,
                'error' => $e->getMessage(),
                'context' => $e->getContext()
            ], $e->statusCode);
        }
    }

    /**
     * Fazer requisição GET de teste
     */
    public function testGet(Request $request): JsonResponse
    {
        try {
            $endpoint = $request->input('endpoint', '/posts');
            $params = $request->input('params', []);
            
            $response = $this->apiClient->get($endpoint, $params);
            
            return response()->json([
                'success' => true,
                'response' => $response->toArray(),
                'provider' => $this->apiClient->getConfig()['provider_name']
            ]);
        } catch (ApiException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'context' => $e->getContext()
            ], $e->statusCode);
        }
    }

    /**
     * Fazer requisição POST de teste
     */
    public function testPost(Request $request): JsonResponse
    {
        try {
            $endpoint = $request->input('endpoint', '/posts');
            $payload = $request->input('payload', []);
            
            $response = $this->apiClient->post($endpoint, $payload);
            
            return response()->json([
                'success' => true,
                'response' => $response->toArray(),
                'provider' => $this->apiClient->getConfig()['provider_name']
            ]);
        } catch (ApiException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'context' => $e->getContext()
            ], $e->statusCode);
        }
    }

    /**
     * Trocar provider dinamicamente (apenas para teste)
     */
    public function switchProvider(Request $request): JsonResponse
    {
        $provider = $request->input('provider');
        
        if (!in_array($provider, ['jsonplaceholder', 'example_api'])) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid provider. Use: jsonplaceholder or example_api'
            ], 400);
        }

        // Temporariamente alterar a configuração
        config(['services.api_provider' => $provider]);
        
        // Recriar a instância
        app()->forgetInstance(ApiClientInterface::class);
        $newClient = app(ApiClientInterface::class);
        
        return response()->json([
            'success' => true,
            'message' => "Provider switched to: {$provider}",
            'config' => $newClient->getConfig()
        ]);
    }

    /**
     * Obter informações do provider atual
     */
    public function providerInfo(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'config' => $this->apiClient->getConfig(),
            'available_providers' => array_keys(config('services.api_clients'))
        ]);
    }
} 