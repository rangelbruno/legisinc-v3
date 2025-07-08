<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class MockApiController extends Controller
{
    /**
     * Simular health check da API
     */
    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'message' => 'Mock API is running',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0'
        ]);
    }

    /**
     * Simular registro de usuário
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 400);
        }

        $email = $request->input('email');
        
        // Verificar se usuário já existe (simulado)
        $existingUsers = Cache::get('mock_api_users', []);
        
        foreach ($existingUsers as $user) {
            if ($user['email'] === $email) {
                return response()->json([
                    'error' => 'User already exists',
                    'message' => 'A user with this email already exists'
                ], 409);
            }
        }
        
        // Criar novo usuário
        $newUser = [
            'id' => count($existingUsers) + 1,
            'name' => $request->input('name'),
            'email' => $email,
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString()
        ];
        
        $existingUsers[] = $newUser;
        Cache::put('mock_api_users', $existingUsers, now()->addHours(24));
        
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $newUser
        ], 201);
    }

    /**
     * Simular login de usuário
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 400);
        }

        $email = $request->input('email');
        $password = $request->input('password');
        
        // Buscar usuário (simulado)
        $users = Cache::get('mock_api_users', []);
        $user = null;
        
        foreach ($users as $u) {
            if ($u['email'] === $email) {
                $user = $u;
                break;
            }
        }
        
        if (!$user) {
            return response()->json([
                'error' => 'Invalid credentials',
                'message' => 'User not found'
            ], 401);
        }
        
        // Simular verificação de senha (aceitar qualquer senha por simplicidade)
        $token = 'mock_jwt_' . Str::random(40);
        
        // Armazenar token
        Cache::put("mock_api_token_{$token}", $user, now()->addHours(24));
        
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * Simular logout
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        
        if ($token) {
            Cache::forget("mock_api_token_{$token}");
        }
        
        return response()->json([
            'message' => 'Logout successful'
        ]);
    }

    /**
     * Listar usuários (protegido)
     */
    public function users(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        
        if (!$token || !Cache::has("mock_api_token_{$token}")) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid or missing token'
            ], 401);
        }
        
        $users = Cache::get('mock_api_users', []);
        
        return response()->json($users);
    }

    /**
     * Obter usuário específico (protegido)
     */
    public function getUser(Request $request, int $id): JsonResponse
    {
        $token = $request->bearerToken();
        
        if (!$token || !Cache::has("mock_api_token_{$token}")) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid or missing token'
            ], 401);
        }
        
        $users = Cache::get('mock_api_users', []);
        
        foreach ($users as $user) {
            if ($user['id'] === $id) {
                return response()->json($user);
            }
        }
        
        return response()->json([
            'error' => 'Not found',
            'message' => 'User not found'
        ], 404);
    }

    /**
     * Criar usuário (protegido)
     */
    public function createUser(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        
        if (!$token || !Cache::has("mock_api_token_{$token}")) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid or missing token'
            ], 401);
        }
        
        return $this->register($request);
    }

    /**
     * Atualizar usuário (protegido)
     */
    public function updateUser(Request $request, int $id): JsonResponse
    {
        $token = $request->bearerToken();
        
        if (!$token || !Cache::has("mock_api_token_{$token}")) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid or missing token'
            ], 401);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 400);
        }
        
        $users = Cache::get('mock_api_users', []);
        $userIndex = null;
        
        foreach ($users as $index => $user) {
            if ($user['id'] === $id) {
                $userIndex = $index;
                break;
            }
        }
        
        if ($userIndex === null) {
            return response()->json([
                'error' => 'Not found',
                'message' => 'User not found'
            ], 404);
        }
        
        // Atualizar dados
        if ($request->has('name')) {
            $users[$userIndex]['name'] = $request->input('name');
        }
        if ($request->has('email')) {
            $users[$userIndex]['email'] = $request->input('email');
        }
        $users[$userIndex]['updated_at'] = now()->toISOString();
        
        Cache::put('mock_api_users', $users, now()->addHours(24));
        
        return response()->json([
            'message' => 'User updated successfully',
            'user' => $users[$userIndex]
        ]);
    }

    /**
     * Deletar usuário (protegido)
     */
    public function deleteUser(Request $request, int $id): JsonResponse
    {
        $token = $request->bearerToken();
        
        if (!$token || !Cache::has("mock_api_token_{$token}")) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid or missing token'
            ], 401);
        }
        
        $users = Cache::get('mock_api_users', []);
        $userIndex = null;
        
        foreach ($users as $index => $user) {
            if ($user['id'] === $id) {
                $userIndex = $index;
                break;
            }
        }
        
        if ($userIndex === null) {
            return response()->json([
                'error' => 'Not found',
                'message' => 'User not found'
            ], 404);
        }
        
        $deletedUser = $users[$userIndex];
        unset($users[$userIndex]);
        $users = array_values($users); // Reindex array
        
        Cache::put('mock_api_users', $users, now()->addHours(24));
        
        return response()->json([
            'message' => 'User deleted successfully',
            'user' => $deletedUser
        ]);
    }

    /**
     * Reset mock data (para testes)
     */
    public function reset(): JsonResponse
    {
        Cache::forget('mock_api_users');
        
        // Adicionar alguns usuários padrão
        $defaultUsers = [
            [
                'id' => 1,
                'name' => 'Bruno Admin',
                'email' => 'bruno@test.com',
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ],
            [
                'id' => 2,
                'name' => 'João Silva',
                'email' => 'joao@test.com',
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ]
        ];
        
        Cache::put('mock_api_users', $defaultUsers, now()->addHours(24));
        
        return response()->json([
            'message' => 'Mock API data reset successfully',
            'users_count' => count($defaultUsers)
        ]);
    }
} 