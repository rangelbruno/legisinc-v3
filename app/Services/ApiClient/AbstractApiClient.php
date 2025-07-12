<?php

namespace App\Services\ApiClient;

use App\Services\ApiClient\DTOs\ApiResponse;
use App\Services\ApiClient\Exceptions\ApiException;
use App\Services\ApiClient\Interfaces\ApiClientInterface;
use App\Services\ApiClient\Traits\HasCaching;
use App\Services\ApiClient\Traits\HasLogging;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

abstract class AbstractApiClient implements ApiClientInterface
{
    use HasCaching, HasLogging;

    protected string $baseUrl;
    protected ?string $token;
    protected int $timeout;
    protected int $retries;
    protected int $cacheTtl;
    protected string $providerName;

    public function __construct(array $config)
    {
        $this->baseUrl = $config['base_url'];
        $this->token = $config['token'] ?? null;
        $this->timeout = $config['timeout'] ?? 30;
        $this->retries = $config['retries'] ?? 3;
        $this->cacheTtl = $config['cache_ttl'] ?? 300;
        $this->providerName = $config['provider_name'] ?? static::class;
        $this->defaultCacheTtl = $this->cacheTtl;
        $this->cachePrefix = 'api_client_' . strtolower($this->providerName);
    }

    public function get(string $endpoint, array $params = []): ApiResponse
    {
        return $this->makeRequest('GET', $endpoint, $params);
    }

    public function post(string $endpoint, array $payload = []): ApiResponse
    {
        return $this->makeRequest('POST', $endpoint, $payload);
    }

    public function put(string $endpoint, array $payload = []): ApiResponse
    {
        return $this->makeRequest('PUT', $endpoint, $payload);
    }

    public function delete(string $endpoint, array $params = []): ApiResponse
    {
        return $this->makeRequest('DELETE', $endpoint, $params);
    }

    public function healthCheck(): bool
    {
        try {
            $startTime = microtime(true);
            
            $httpClient = Http::baseUrl($this->baseUrl)
                ->timeout(5);
                
            if ($this->token) {
                $httpClient = $httpClient->withToken($this->token);
            }
            
            $response = $httpClient->get($this->getHealthCheckEndpoint());
            
            $responseTime = microtime(true) - $startTime;
            $isHealthy = $response->successful();
            
            $this->logHealthCheck($isHealthy, $responseTime);
            
            return $isHealthy;
        } catch (\Exception $e) {
            $this->logHealthCheck(false, 0);
            return false;
        }
    }

    public function getConfig(): array
    {
        return [
            'provider_name' => $this->providerName,
            'base_url' => $this->baseUrl,
            'timeout' => $this->timeout,
            'retries' => $this->retries,
            'cache_ttl' => $this->cacheTtl,
        ];
    }

    protected function makeRequest(string $method, string $endpoint, array $data = []): ApiResponse
    {
        $cacheKey = null;
        $startTime = microtime(true);

        // Check cache for GET requests
        if ($this->shouldUseCache($method)) {
            $cacheKey = $this->generateCacheKey($method, $endpoint, $data);
            $cached = $this->getFromCache($cacheKey);
            
            if ($cached !== null) {
                return $cached;
            }
        }

        $this->logRequest($method, $endpoint, $data);

        try {
            $httpClient = Http::baseUrl($this->baseUrl)
                ->timeout($this->timeout)
                ->retry($this->retries, 100);
                
            if ($this->token) {
                $httpClient = $httpClient->withToken($this->token);
            }

            // Add custom headers if needed
            $headers = $this->getCustomHeaders();
            if (!empty($headers)) {
                $httpClient = $httpClient->withHeaders($headers);
            }

            $response = match (strtoupper($method)) {
                'GET' => $httpClient->get($endpoint, $data),
                'POST' => $httpClient->post($endpoint, $data),
                'PUT' => $httpClient->put($endpoint, $data),
                'DELETE' => $httpClient->delete($endpoint, $data),
                default => throw new ApiException("Unsupported HTTP method: {$method}")
            };

            $response->throw();
            
            $responseTime = microtime(true) - $startTime;
            $apiResponse = ApiResponse::success(
                data: $response->json() ?? [],
                statusCode: $response->status(),
                headers: $response->headers(),
                responseTime: $responseTime
            );

            $this->logResponse($apiResponse, $responseTime);

            // Cache successful GET responses
            if ($cacheKey && $this->shouldUseCache($method)) {
                $this->putInCache($cacheKey, $apiResponse, $this->cacheTtl);
            }

            return $apiResponse;

        } catch (ConnectionException $e) {
            $this->logError($e, ['method' => $method, 'endpoint' => $endpoint]);
            throw ApiException::connectionError($this->providerName, $e->getMessage());
            
        } catch (RequestException $e) {
            $this->logError($e, ['method' => $method, 'endpoint' => $endpoint]);
            $statusCode = $e->response?->status() ?? 500;
            
            if ($statusCode === 401) {
                throw ApiException::authenticationError($this->providerName);
            }
            
            throw ApiException::invalidResponse(
                $this->providerName,
                $statusCode,
                $e->response?->json()
            );
            
        } catch (\Exception $e) {
            $this->logError($e, ['method' => $method, 'endpoint' => $endpoint]);
            throw new ApiException(
                "API request failed: " . $e->getMessage(),
                500,
                null,
                $this->providerName,
                $e
            );
        }
    }

    /**
     * Get health check endpoint for the provider
     */
    abstract protected function getHealthCheckEndpoint(): string;

    /**
     * Get custom headers for the provider
     */
    protected function getCustomHeaders(): array
    {
        return [];
    }
} 