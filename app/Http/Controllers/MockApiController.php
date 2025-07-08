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

    // ============================================================================
    // ENDPOINTS DE PARLAMENTARES
    // ============================================================================

    /**
     * Listar todos os parlamentares
     */
    public function parlamentares(Request $request): JsonResponse
    {
        $parlamentares = Cache::remember('mock_parlamentares', 3600, function() {
            return [
                [
                    'id' => 1,
                    'nome' => 'João Silva Santos',
                    'partido' => 'PT',
                    'status' => 'ativo',
                    'cargo' => 'Vereador',
                    'telefone' => '(11) 98765-4321',
                    'email' => 'joao.silva@camara.gov.br',
                    'data_nascimento' => '1975-03-15',
                    'profissao' => 'Advogado',
                    'escolaridade' => 'Superior Completo',
                    'comissoes' => ['Educação', 'Saúde'],
                    'mandatos' => [
                        ['ano_inicio' => 2021, 'ano_fim' => 2024, 'status' => 'atual']
                    ],
                    'created_at' => now()->toISOString(),
                    'updated_at' => now()->toISOString()
                ],
                [
                    'id' => 2,
                    'nome' => 'Maria Santos Oliveira',
                    'partido' => 'PSDB',
                    'status' => 'ativo',
                    'cargo' => 'Vereadora',
                    'telefone' => '(11) 97654-3210',
                    'email' => 'maria.santos@camara.gov.br',
                    'data_nascimento' => '1980-07-22',
                    'profissao' => 'Professora',
                    'escolaridade' => 'Pós-Graduação',
                    'comissoes' => ['Educação', 'Cultura'],
                    'mandatos' => [
                        ['ano_inicio' => 2021, 'ano_fim' => 2024, 'status' => 'atual']
                    ],
                    'created_at' => now()->toISOString(),
                    'updated_at' => now()->toISOString()
                ],
                [
                    'id' => 3,
                    'nome' => 'Carlos Eduardo Pereira',
                    'partido' => 'MDB',
                    'status' => 'ativo',
                    'cargo' => 'Presidente da Câmara',
                    'telefone' => '(11) 96543-2109',
                    'email' => 'carlos.pereira@camara.gov.br',
                    'data_nascimento' => '1965-11-08',
                    'profissao' => 'Empresário',
                    'escolaridade' => 'Superior Completo',
                    'comissoes' => ['Mesa Diretora', 'Finanças'],
                    'mandatos' => [
                        ['ano_inicio' => 2017, 'ano_fim' => 2020, 'status' => 'anterior'],
                        ['ano_inicio' => 2021, 'ano_fim' => 2024, 'status' => 'atual']
                    ],
                    'created_at' => now()->toISOString(),
                    'updated_at' => now()->toISOString()
                ],
                [
                    'id' => 4,
                    'nome' => 'Ana Paula Costa',
                    'partido' => 'PSL',
                    'status' => 'licenciada',
                    'cargo' => 'Vereadora',
                    'telefone' => '(11) 95432-1098',
                    'email' => 'ana.costa@camara.gov.br',
                    'data_nascimento' => '1988-02-14',
                    'profissao' => 'Médica',
                    'escolaridade' => 'Pós-Graduação',
                    'comissoes' => ['Saúde'],
                    'mandatos' => [
                        ['ano_inicio' => 2021, 'ano_fim' => 2024, 'status' => 'atual']
                    ],
                    'created_at' => now()->toISOString(),
                    'updated_at' => now()->toISOString()
                ],
                [
                    'id' => 5,
                    'nome' => 'Roberto Mendes Lima',
                    'partido' => 'PDT',
                    'status' => 'ativo',
                    'cargo' => 'Vice-Presidente',
                    'telefone' => '(11) 94321-0987',
                    'email' => 'roberto.mendes@camara.gov.br',
                    'data_nascimento' => '1972-09-30',
                    'profissao' => 'Engenheiro',
                    'escolaridade' => 'Superior Completo',
                    'comissoes' => ['Mesa Diretora', 'Obras'],
                    'mandatos' => [
                        ['ano_inicio' => 2021, 'ano_fim' => 2024, 'status' => 'atual']
                    ],
                    'created_at' => now()->toISOString(),
                    'updated_at' => now()->toISOString()
                ]
            ];
        });
        
        // Aplicar filtros se fornecidos
        if ($request->has('partido')) {
            $parlamentares = array_filter($parlamentares, function($p) use ($request) {
                return $p['partido'] === $request->get('partido');
            });
        }
        
        if ($request->has('status')) {
            $parlamentares = array_filter($parlamentares, function($p) use ($request) {
                return $p['status'] === $request->get('status');
            });
        }
        
        return response()->json([
            'data' => array_values($parlamentares),
            'meta' => [
                'total' => count($parlamentares),
                'page' => 1,
                'per_page' => 50
            ]
        ]);
    }

    /**
     * Obter parlamentar específico
     */
    public function getParlamentar(Request $request, int $id): JsonResponse
    {
        $parlamentares = Cache::get('mock_parlamentares', []);
        
        foreach ($parlamentares as $parlamentar) {
            if ($parlamentar['id'] === $id) {
                return response()->json([
                    'data' => $parlamentar
                ]);
            }
        }
        
        return response()->json([
            'error' => 'Parlamentar não encontrado',
            'message' => 'Parlamentar com ID ' . $id . ' não foi encontrado'
        ], 404);
    }

    /**
     * Criar novo parlamentar
     */
    public function createParlamentar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'partido' => 'required|string|max:10',
            'cargo' => 'required|string|max:100',
            'telefone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'data_nascimento' => 'required|date',
            'profissao' => 'required|string|max:100',
            'escolaridade' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 400);
        }

        $parlamentares = Cache::get('mock_parlamentares', []);
        
        $novoParlamentar = [
            'id' => count($parlamentares) + 1,
            'nome' => $request->input('nome'),
            'partido' => $request->input('partido'),
            'status' => 'ativo',
            'cargo' => $request->input('cargo'),
            'telefone' => $request->input('telefone'),
            'email' => $request->input('email'),
            'data_nascimento' => $request->input('data_nascimento'),
            'profissao' => $request->input('profissao'),
            'escolaridade' => $request->input('escolaridade'),
            'comissoes' => $request->input('comissoes', []),
            'mandatos' => [
                ['ano_inicio' => 2021, 'ano_fim' => 2024, 'status' => 'atual']
            ],
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString()
        ];
        
        $parlamentares[] = $novoParlamentar;
        Cache::put('mock_parlamentares', $parlamentares, now()->addHours(24));
        
        return response()->json([
            'message' => 'Parlamentar criado com sucesso',
            'data' => $novoParlamentar
        ], 201);
    }

    /**
     * Atualizar parlamentar
     */
    public function updateParlamentar(Request $request, int $id): JsonResponse
    {
        $parlamentares = Cache::get('mock_parlamentares', []);
        $parlamentarIndex = null;
        
        foreach ($parlamentares as $index => $parlamentar) {
            if ($parlamentar['id'] === $id) {
                $parlamentarIndex = $index;
                break;
            }
        }
        
        if ($parlamentarIndex === null) {
            return response()->json([
                'error' => 'Parlamentar não encontrado',
                'message' => 'Parlamentar com ID ' . $id . ' não foi encontrado'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'nome' => 'sometimes|string|max:255',
            'partido' => 'sometimes|string|max:10',
            'cargo' => 'sometimes|string|max:100',
            'telefone' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|max:255',
            'data_nascimento' => 'sometimes|date',
            'profissao' => 'sometimes|string|max:100',
            'escolaridade' => 'sometimes|string|max:100',
            'status' => 'sometimes|string|in:ativo,inativo,licenciado',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 400);
        }
        
        // Atualizar campos fornecidos
        $updateFields = ['nome', 'partido', 'cargo', 'telefone', 'email', 'data_nascimento', 'profissao', 'escolaridade', 'status'];
        
        foreach ($updateFields as $field) {
            if ($request->has($field)) {
                $parlamentares[$parlamentarIndex][$field] = $request->input($field);
            }
        }
        
        if ($request->has('comissoes')) {
            $parlamentares[$parlamentarIndex]['comissoes'] = $request->input('comissoes');
        }
        
        $parlamentares[$parlamentarIndex]['updated_at'] = now()->toISOString();
        
        Cache::put('mock_parlamentares', $parlamentares, now()->addHours(24));
        
        return response()->json([
            'message' => 'Parlamentar atualizado com sucesso',
            'data' => $parlamentares[$parlamentarIndex]
        ]);
    }

    /**
     * Deletar parlamentar
     */
    public function deleteParlamentar(Request $request, int $id): JsonResponse
    {
        $parlamentares = Cache::get('mock_parlamentares', []);
        $parlamentarIndex = null;
        
        foreach ($parlamentares as $index => $parlamentar) {
            if ($parlamentar['id'] === $id) {
                $parlamentarIndex = $index;
                break;
            }
        }
        
        if ($parlamentarIndex === null) {
            return response()->json([
                'error' => 'Parlamentar não encontrado',
                'message' => 'Parlamentar com ID ' . $id . ' não foi encontrado'
            ], 404);
        }
        
        $parlamentarDeletado = $parlamentares[$parlamentarIndex];
        unset($parlamentares[$parlamentarIndex]);
        $parlamentares = array_values($parlamentares);
        
        Cache::put('mock_parlamentares', $parlamentares, now()->addHours(24));
        
        return response()->json([
            'message' => 'Parlamentar deletado com sucesso',
            'data' => $parlamentarDeletado
        ]);
    }

    /**
     * Obter parlamentares por partido
     */
    public function parlamentaresByPartido(Request $request, string $partido): JsonResponse
    {
        $parlamentares = Cache::get('mock_parlamentares', []);
        
        $parlamentaresFiltrados = array_filter($parlamentares, function($p) use ($partido) {
            return strtoupper($p['partido']) === strtoupper($partido);
        });
        
        return response()->json([
            'data' => array_values($parlamentaresFiltrados),
            'meta' => [
                'total' => count($parlamentaresFiltrados),
                'partido' => $partido
            ]
        ]);
    }

    /**
     * Obter parlamentares por status
     */
    public function parlamentaresByStatus(Request $request, string $status): JsonResponse
    {
        $parlamentares = Cache::get('mock_parlamentares', []);
        
        $parlamentaresFiltrados = array_filter($parlamentares, function($p) use ($status) {
            return $p['status'] === $status;
        });
        
        return response()->json([
            'data' => array_values($parlamentaresFiltrados),
            'meta' => [
                'total' => count($parlamentaresFiltrados),
                'status' => $status
            ]
        ]);
    }

    /**
     * Obter mesa diretora
     */
    public function mesaDiretora(Request $request): JsonResponse
    {
        $mesaDiretora = Cache::remember('mock_mesa_diretora', 3600, function() {
            return [
                [
                    'id' => 3,
                    'nome' => 'Carlos Eduardo Pereira',
                    'partido' => 'MDB',
                    'cargo_mesa' => 'Presidente',
                    'cargo_parlamentar' => 'Presidente da Câmara',
                    'mandato_mesa' => [
                        'ano_inicio' => 2023,
                        'ano_fim' => 2024
                    ]
                ],
                [
                    'id' => 5,
                    'nome' => 'Roberto Mendes Lima',
                    'partido' => 'PDT',
                    'cargo_mesa' => 'Vice-Presidente',
                    'cargo_parlamentar' => 'Vice-Presidente',
                    'mandato_mesa' => [
                        'ano_inicio' => 2023,
                        'ano_fim' => 2024
                    ]
                ]
            ];
        });
        
        return response()->json([
            'data' => $mesaDiretora,
            'meta' => [
                'total' => count($mesaDiretora),
                'ano_mandato' => 2023
            ]
        ]);
    }

    /**
     * Obter comissões de um parlamentar
     */
    public function comissoesParlamentar(Request $request, int $parlamentarId): JsonResponse
    {
        $parlamentares = Cache::get('mock_parlamentares', []);
        $parlamentar = null;
        
        foreach ($parlamentares as $p) {
            if ($p['id'] === $parlamentarId) {
                $parlamentar = $p;
                break;
            }
        }
        
        if (!$parlamentar) {
            return response()->json([
                'error' => 'Parlamentar não encontrado',
                'message' => 'Parlamentar com ID ' . $parlamentarId . ' não foi encontrado'
            ], 404);
        }
        
        $comissoes = $parlamentar['comissoes'] ?? [];
        
        return response()->json([
            'data' => $comissoes,
            'meta' => [
                'parlamentar_id' => $parlamentarId,
                'parlamentar_nome' => $parlamentar['nome'],
                'total_comissoes' => count($comissoes)
            ]
        ]);
    }

    /**
     * Reset mock data (para testes)
     */
    public function reset(): JsonResponse
    {
        Cache::forget('mock_api_users');
        Cache::forget('mock_parlamentares');
        Cache::forget('mock_mesa_diretora');
        
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
            'users_count' => count($defaultUsers),
            'parlamentares_reset' => true
        ]);
    }
} 