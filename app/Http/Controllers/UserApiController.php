<?php

namespace App\Http\Controllers;

use App\Services\ApiClient\Providers\NodeApiClient;
use App\Services\ApiClient\Exceptions\ApiException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;

class UserApiController extends Controller
{
    public function __construct(
        private NodeApiClient $nodeApi
    ) {}

    /**
     * Exibir página de gerenciamento de usuários
     */
    public function index()
    {
        try {
            // Verificar se o usuário está autenticado na sessão
            if (!Session::get('api_authenticated', false)) {
                return redirect()->route('auth.login')
                    ->with('error', 'Você precisa fazer login para acessar esta página.');
            }

            $authStatus = $this->nodeApi->getAuthStatus();
            $config = $this->nodeApi->getConfig();
            
            $users = [];
            if ($authStatus['authenticated']) {
                $response = $this->nodeApi->getUsers();
                if ($response->isSuccess()) {
                    $users = $response->getData();
                }
            }
            
            return view('user-api.index', compact('users', 'authStatus', 'config'));
            
        } catch (ApiException $e) {
            return view('user-api.index', [
                'users' => [],
                'authStatus' => ['authenticated' => false],
                'config' => $this->nodeApi->getConfig(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Fazer login na API
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        try {
            $response = $this->nodeApi->login(
                $request->input('email'),
                $request->input('password')
            );

            if ($response->isSuccess()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login realizado com sucesso',
                    'data' => $response->getData(),
                    'auth_status' => $this->nodeApi->getAuthStatus()
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Credenciais inválidas',
                'data' => $response->getData()
            ], 401);

        } catch (ApiException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'context' => $e->getContext()
            ], $e->statusCode);
        }
    }

    /**
     * Fazer logout da API
     */
    public function logout(): JsonResponse
    {
        $this->nodeApi->logout();
        
        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso',
            'auth_status' => $this->nodeApi->getAuthStatus()
        ]);
    }

    /**
     * Registrar novo usuário
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6'
        ]);

        try {
            $response = $this->nodeApi->register(
                $request->input('name'),
                $request->input('email'),
                $request->input('password')
            );

            if ($response->isSuccess()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Usuário registrado com sucesso',
                    'data' => $response->getData()
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Falha ao registrar usuário',
                'data' => $response->getData()
            ], 400);

        } catch (ApiException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'context' => $e->getContext()
            ], $e->statusCode);
        }
    }

    /**
     * Listar todos os usuários
     */
    public function getUsers(): JsonResponse
    {
        try {
            $response = $this->nodeApi->withAuth(function($api) {
                return $api->getUsers();
            });

            return response()->json([
                'success' => true,
                'data' => $response->getData(),
                'response_time' => $response->responseTime
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
     * Obter usuário específico
     */
    public function getUser(int $id): JsonResponse
    {
        try {
            $response = $this->nodeApi->withAuth(function($api) use ($id) {
                return $api->getUser($id);
            });

            return response()->json([
                'success' => true,
                'data' => $response->getData(),
                'response_time' => $response->responseTime
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
     * Criar novo usuário (admin)
     */
    public function createUser(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255'
        ]);

        try {
            $response = $this->nodeApi->withAuth(function($api) use ($request) {
                return $api->createUser(
                    $request->input('name'),
                    $request->input('email')
                );
            });

            if ($response->isSuccess()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Usuário criado com sucesso',
                    'data' => $response->getData()
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Falha ao criar usuário',
                'data' => $response->getData()
            ], 400);

        } catch (ApiException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'context' => $e->getContext()
            ], $e->statusCode);
        }
    }

    /**
     * Atualizar usuário
     */
    public function updateUser(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255'
        ]);

        try {
            $response = $this->nodeApi->withAuth(function($api) use ($request, $id) {
                return $api->updateUser(
                    $id,
                    $request->input('name'),
                    $request->input('email')
                );
            });

            if ($response->isSuccess()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Usuário atualizado com sucesso',
                    'data' => $response->getData()
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Falha ao atualizar usuário',
                'data' => $response->getData()
            ], 400);

        } catch (ApiException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'context' => $e->getContext()
            ], $e->statusCode);
        }
    }

    /**
     * Deletar usuário
     */
    public function deleteUser(int $id): JsonResponse
    {
        try {
            $response = $this->nodeApi->withAuth(function($api) use ($id) {
                return $api->deleteUser($id);
            });

            if ($response->isSuccess()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Usuário deletado com sucesso',
                    'data' => $response->getData()
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Falha ao deletar usuário',
                'data' => $response->getData()
            ], 400);

        } catch (ApiException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'context' => $e->getContext()
            ], $e->statusCode);
        }
    }

    /**
     * Verificar status de autenticação
     */
    public function authStatus(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'auth_status' => $this->nodeApi->getAuthStatus(),
            'config' => [
                'provider_name' => $this->nodeApi->getConfig()['provider_name'],
                'base_url' => $this->nodeApi->getConfig()['base_url']
            ]
        ]);
    }

    /**
     * Health check completo da API
     */
    public function healthCheck(): JsonResponse
    {
        try {
            $results = $this->nodeApi->authHealthCheck();
            
            return response()->json([
                'success' => true,
                'health_check' => $results,
                'overall_status' => $results['public_endpoint'] && ($results['protected_endpoint'] ?? false)
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
     * Tentar login automático
     */
    public function autoLogin(): JsonResponse
    {
        try {
            $response = $this->nodeApi->autoLogin();

            if ($response->isSuccess()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login automático realizado com sucesso',
                    'auth_status' => $this->nodeApi->getAuthStatus()
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Falha no login automático',
                'data' => $response->getData()
            ], 401);

        } catch (ApiException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'context' => $e->getContext()
            ], $e->statusCode);
        }
    }
} 