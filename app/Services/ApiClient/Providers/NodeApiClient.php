<?php

namespace App\Services\ApiClient\Providers;

use App\Services\ApiClient\AbstractApiClient;
use App\Services\ApiClient\DTOs\ApiResponse;
use App\Services\ApiClient\Exceptions\ApiException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class NodeApiClient extends AbstractApiClient
{
    private ?string $jwtToken = null;
    private string $cacheKeyToken = 'node_api_jwt_token';
    private array $config;

    public function __construct(array $config)
    {
        // Usar configuração dinâmica baseada no modo da API
        $apiMode = config('api.mode', 'mock');
        $dynamicConfig = $this->buildDynamicConfig($apiMode, $config);
        
        parent::__construct($dynamicConfig);
        
        // Armazenar configuração completa
        $this->config = $dynamicConfig;
        
        // Tentar recuperar token do cache apenas se não estiver em modo mock
        if ($apiMode !== 'mock') {
            $this->jwtToken = Cache::get($this->cacheKeyToken);
        }
    }

    /**
     * Constrói configuração dinâmica baseada no modo da API
     */
    private function buildDynamicConfig(string $apiMode, array $originalConfig): array
    {
        $config = $originalConfig;
        
        if ($apiMode === 'mock') {
            // Configuração para modo mock
            $config['base_url'] = config('api.mock.base_url');
            $config['provider_name'] = 'Mock API';
            $config['description'] = config('api.mock.description');
            $config['timeout'] = 10; // Timeout menor para mock
            $config['retries'] = 1;  // Menos tentativas para mock
        } else {
            // Configuração para API externa
            $config['base_url'] = config('api.external.base_url');
            $config['provider_name'] = 'External API';
            $config['description'] = config('api.external.description');
            $config['timeout'] = config('api.external.timeout', 30);
            $config['retries'] = config('api.external.retries', 3);
        }
        
        return $config;
    }

    /**
     * Get health check endpoint
     */
    protected function getHealthCheckEndpoint(): string
    {
        $apiMode = config('api.mode', 'mock');
        $urls = config('api.urls', []);
        
        return $urls[$apiMode]['health'] ?? '/';
    }

    /**
     * Get custom headers for the provider
     */
    protected function getCustomHeaders(): array
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        // Adicionar token JWT se disponível (e não em modo mock)
        if ($this->jwtToken && config('api.mode') !== 'mock') {
            $headers['Authorization'] = "Bearer {$this->jwtToken}";
        }

        return $headers;
    }

    /**
     * Sobrescrever token de configuração com JWT quando disponível
     */
    protected function makeRequest(string $method, string $endpoint, array $data = []): ApiResponse
    {
        // Para endpoints protegidos, garantir que temos token (exceto em modo mock)
        if ($this->isProtectedEndpoint($endpoint) && !$this->jwtToken && config('api.mode') !== 'mock') {
            throw ApiException::authenticationError($this->providerName);
        }

        return parent::makeRequest($method, $endpoint, $data);
    }

    /**
     * Verificar se endpoint requer autenticação
     */
    private function isProtectedEndpoint(string $endpoint): bool
    {
        $protectedPaths = ['/users'];
        
        foreach ($protectedPaths as $path) {
            if (str_starts_with($endpoint, $path)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Constrói URL do endpoint baseada no modo da API
     */
    private function buildEndpointUrl(string $endpoint): string
    {
        $apiMode = config('api.mode', 'mock');
        
        if ($apiMode === 'mock') {
            // Para modo mock, usar apenas o endpoint relativo
            // pois a base_url já contém o caminho completo
            $endpointMap = [
                '/register' => '/register',
                '/login' => '/login',
                '/users' => '/users',
                '/parlamentares' => '/parlamentares',
                '/parlamentares/partido' => '/parlamentares/partido',
                '/parlamentares/status' => '/parlamentares/status',
                '/mesa-diretora' => '/mesa-diretora',
                '/sessions' => '/sessions',
            ];
            
            return $endpointMap[$endpoint] ?? $endpoint;
        } else {
            // Para modo external, usar configuração das URLs
            $urls = config('api.urls', []);
            $endpointMap = [
                '/register' => $urls[$apiMode]['register'] ?? '/register',
                '/login' => $urls[$apiMode]['login'] ?? '/login',
                '/users' => $urls[$apiMode]['users'] ?? '/users',
                '/parlamentares' => $urls[$apiMode]['parlamentares'] ?? '/parlamentares',
                '/parlamentares/partido' => $urls[$apiMode]['parlamentares/partido'] ?? '/parlamentares/partido',
                '/parlamentares/status' => $urls[$apiMode]['parlamentares/status'] ?? '/parlamentares/status',
                '/mesa-diretora' => $urls[$apiMode]['mesa-diretora'] ?? '/mesa-diretora',
                '/sessions' => $urls[$apiMode]['sessions'] ?? '/sessions',
            ];
            
            return $endpointMap[$endpoint] ?? $endpoint;
        }
    }

    // ============================================================================
    // MÉTODOS DE AUTENTICAÇÃO
    // ============================================================================

    /**
     * Registrar novo usuário
     */
    public function register(string $name, string $email, string $password): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/register');
        
        $response = $this->post($endpoint, [
            'name' => $name,
            'email' => $email,
            'password' => $password
        ]);

        return $response;
    }

    /**
     * Fazer login e obter token JWT
     */
    public function login(string $email, string $password): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/login');
        
        $response = $this->post($endpoint, [
            'email' => $email,
            'password' => $password
        ]);

        // Se login foi bem-sucedido, armazenar token (exceto em modo mock)
        if ($response->isSuccess() && isset($response->data['token']) && config('api.mode') !== 'mock') {
            $this->jwtToken = $response->data['token'];
            
            // Cachear token por 23 horas (expire em 24h)
            Cache::put($this->cacheKeyToken, $this->jwtToken, now()->addHours(23));
            
            $this->logRequest('TOKEN_STORED', $endpoint, ['token_cached' => true]);
        }

        return $response;
    }

    /**
     * Fazer logout (limpar token)
     */
    public function logout(): void
    {
        $this->jwtToken = null;
        Cache::forget($this->cacheKeyToken);
        $this->logRequest('TOKEN_CLEARED', '/logout', ['token_cleared' => true]);
    }

    /**
     * Verificar se está autenticado
     */
    public function isAuthenticated(): bool
    {
        // Em modo mock, considerar sempre autenticado
        if (config('api.mode') === 'mock') {
            return true;
        }
        
        return !empty($this->jwtToken);
    }

    /**
     * Obter token atual
     */
    public function getToken(): ?string
    {
        return $this->jwtToken;
    }

    /**
     * Definir token manualmente
     */
    public function setToken(string $token): void
    {
        $this->jwtToken = $token;
        
        // Não cachear em modo mock
        if (config('api.mode') !== 'mock') {
            Cache::put($this->cacheKeyToken, $this->jwtToken, now()->addHours(23));
        }
    }

    // ============================================================================
    // MÉTODOS DE GERENCIAMENTO DE USUÁRIOS
    // ============================================================================

    /**
     * Obter todos os usuários
     */
    public function getUsers(): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/users');
        return $this->get($endpoint);
    }

    /**
     * Obter usuário específico por ID
     */
    public function getUser(int $id): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/users');
        return $this->get("{$endpoint}/{$id}");
    }

    /**
     * Criar novo usuário (admin)
     */
    public function createUser(string $name, string $email): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/users');
        
        return $this->post($endpoint, [
            'name' => $name,
            'email' => $email
        ]);
    }

    /**
     * Atualizar usuário
     */
    public function updateUser(int $id, ?string $name = null, ?string $email = null): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/users');
        
        $payload = array_filter([
            'name' => $name,
            'email' => $email
        ]);

        return $this->put("{$endpoint}/{$id}", $payload);
    }

    /**
     * Deletar usuário
     */
    public function deleteUser(int $id): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/users');
        return $this->delete("{$endpoint}/{$id}");
    }

    // ============================================================================
    // MÉTODOS DE PARLAMENTARES
    // ============================================================================

    /**
     * Obter todos os parlamentares
     */
    public function getParlamentares(array $filters = []): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/parlamentares');
        return $this->get($endpoint, $filters);
    }

    /**
     * Obter parlamentar específico por ID
     */
    public function getParlamentar(int $id): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/parlamentares');
        return $this->get("{$endpoint}/{$id}");
    }

    /**
     * Criar novo parlamentar
     */
    public function createParlamentar(array $data): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/parlamentares');
        return $this->post($endpoint, $data);
    }

    /**
     * Atualizar parlamentar
     */
    public function updateParlamentar(int $id, array $data): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/parlamentares');
        return $this->put("{$endpoint}/{$id}", $data);
    }

    /**
     * Deletar parlamentar
     */
    public function deleteParlamentar(int $id): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/parlamentares');
        return $this->delete("{$endpoint}/{$id}");
    }

    /**
     * Obter parlamentares por partido
     */
    public function getParlamentaresByPartido(string $partido): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/parlamentares/partido');
        return $this->get("{$endpoint}/{$partido}");
    }

    /**
     * Obter parlamentares por status
     */
    public function getParlamentaresByStatus(string $status): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/parlamentares/status');
        return $this->get("{$endpoint}/{$status}");
    }

    /**
     * Obter mesa diretora
     */
    public function getMesaDiretora(): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/mesa-diretora');
        return $this->get($endpoint);
    }

    /**
     * Obter comissões de um parlamentar
     */
    public function getComissoesParlamentar(int $parlamentarId): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/parlamentares');
        return $this->get("{$endpoint}/{$parlamentarId}/comissoes");
    }

    // ============================================================================
    // MÉTODOS DE CONVENIÊNCIA
    // ============================================================================

    /**
     * Fazer login automático usando credenciais padrão
     */
    public function autoLogin(): ApiResponse
    {
        $defaultCredentials = config('api.default_credentials');
        
        return $this->login(
            $defaultCredentials['email'],
            $defaultCredentials['password']
        );
    }

    /**
     * Garantir que está autenticado, fazer login se necessário
     */
    public function ensureAuthenticated(): bool
    {
        if ($this->isAuthenticated()) {
            return true;
        }

        try {
            $response = $this->autoLogin();
            return $response->isSuccess();
        } catch (ApiException $e) {
            $this->logRequest('AUTO_LOGIN_FAILED', '/login', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Executar operação com autenticação garantida
     */
    public function withAuth(callable $operation): ApiResponse
    {
        if (!$this->ensureAuthenticated()) {
            throw ApiException::authenticationError($this->providerName);
        }

        return $operation();
    }

    /**
     * Obter status de autenticação detalhado
     */
    public function getAuthStatus(): array
    {
        return [
            'authenticated' => $this->isAuthenticated(),
            'token' => $this->jwtToken ? 'present' : 'absent',
            'api_mode' => config('api.mode'),
            'provider' => $this->providerName,
            'base_url' => $this->config['base_url'] ?? 'not_set',
        ];
    }

    /**
     * Verificar saúde da API com informações de autenticação
     */
    public function authHealthCheck(): array
    {
        try {
            $healthResponse = $this->healthCheck();
            $authStatus = $this->getAuthStatus();
            
            return [
                'api_healthy' => $healthResponse->isSuccess(),
                'auth_status' => $authStatus,
                'can_authenticate' => $this->ensureAuthenticated(),
                'last_check' => now()->toISOString(),
                'mode' => config('api.mode'),
                'endpoints' => [
                    'health' => $this->getHealthCheckEndpoint(),
                    'register' => $this->buildEndpointUrl('/register'),
                    'login' => $this->buildEndpointUrl('/login'),
                    'users' => $this->buildEndpointUrl('/users'),
                ],
            ];
        } catch (ApiException $e) {
            return [
                'api_healthy' => false,
                'auth_status' => $this->getAuthStatus(),
                'error' => $e->getMessage(),
                'last_check' => now()->toISOString(),
                'mode' => config('api.mode'),
            ];
        }
    }

    /**
     * Obter configuração atual
     */
    public function getConfig(): array
    {
        return array_merge($this->config, [
            'api_mode' => config('api.mode'),
            'auth_status' => $this->getAuthStatus(),
        ]);
    }

    // ============================================================================
    // MÉTODOS DE SESSÕES
    // ============================================================================

    /**
     * Obter todas as sessões
     */
    public function getSessions(array $filters = []): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/sessions');
        return $this->get($endpoint, $filters);
    }

    /**
     * Obter sessão específica por ID
     */
    public function getSession(int $id): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/sessions');
        return $this->get("{$endpoint}/{$id}");
    }

    /**
     * Criar nova sessão
     */
    public function createSession(array $data): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/sessions');
        return $this->post($endpoint, $data);
    }

    /**
     * Atualizar sessão
     */
    public function updateSession(int $id, array $data): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/sessions');
        return $this->put("{$endpoint}/{$id}", $data);
    }

    /**
     * Deletar sessão
     */
    public function deleteSession(int $id): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/sessions');
        return $this->delete("{$endpoint}/{$id}");
    }

    /**
     * Obter matérias de uma sessão
     */
    public function getSessionMatters(int $sessionId): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/sessions');
        return $this->get("{$endpoint}/{$sessionId}/matters");
    }

    /**
     * Adicionar matéria à sessão
     */
    public function addMatterToSession(int $sessionId, array $data): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/sessions');
        return $this->post("{$endpoint}/{$sessionId}/matters", $data);
    }

    /**
     * Atualizar matéria na sessão
     */
    public function updateSessionMatter(int $sessionId, int $matterId, array $data): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/sessions');
        return $this->put("{$endpoint}/{$sessionId}/matters/{$matterId}", $data);
    }

    /**
     * Remover matéria da sessão
     */
    public function removeSessionMatter(int $sessionId, int $matterId): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/sessions');
        return $this->delete("{$endpoint}/{$sessionId}/matters/{$matterId}");
    }

    /**
     * Gerar XML da sessão
     */
    public function generateSessionXml(int $sessionId, string $documentType): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/sessions');
        return $this->post("{$endpoint}/{$sessionId}/xml", [
            'document_type' => $documentType
        ]);
    }

    /**
     * Exportar XML da sessão
     */
    public function exportSessionXml(int $sessionId, array $xmlData): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/sessions');
        return $this->post("{$endpoint}/{$sessionId}/export", $xmlData);
    }

    /**
     * Obter histórico de exportações da sessão
     */
    public function getSessionExports(int $sessionId): ApiResponse
    {
        $endpoint = $this->buildEndpointUrl('/sessions');
        return $this->get("{$endpoint}/{$sessionId}/exports");
    }

    /**
     * Override healthCheck to work properly with both mock and external API modes
     */
    public function healthCheck(): bool
    {
        try {
            $startTime = microtime(true);
            $apiMode = config('api.mode', 'mock');
            
            // For mock mode, use the mock API health endpoint directly
            if ($apiMode === 'mock') {
                $response = Http::timeout(5)
                    ->get($this->baseUrl . '/');
            } else {
                // For external mode, use the configured endpoint with authentication
                $response = Http::baseUrl($this->baseUrl)
                    ->timeout(5)
                    ->get($this->getHealthCheckEndpoint());
            }
            
            $responseTime = microtime(true) - $startTime;
            $isHealthy = $response->successful();
            
            $this->logHealthCheck($isHealthy, $responseTime);
            
            return $isHealthy;
        } catch (\Exception $e) {
            $this->logHealthCheck(false, 0);
            return false;
        }
    }
} 