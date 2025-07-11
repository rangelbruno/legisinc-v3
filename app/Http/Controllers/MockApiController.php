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

    // ============================================================================
    // ENDPOINTS DE PROJETOS/DOCUMENTOS
    // ============================================================================

    /**
     * Listar documentos/projetos disponíveis para adicionar às sessões
     */
    public function documents(Request $request): JsonResponse
    {
        $tipo = $request->input('tipo');
        $status = $request->input('status');
        $ano = $request->input('ano', date('Y'));
        $search = $request->input('search');

        $documents = $this->getAvailableDocuments();

        // Filtrar por tipo se especificado
        if ($tipo) {
            $documents = array_filter($documents, function($doc) use ($tipo) {
                return $doc['tipo_id'] == $tipo;
            });
        }

        // Filtrar por status se especificado
        if ($status) {
            $documents = array_filter($documents, function($doc) use ($status) {
                return $doc['status'] === $status;
            });
        }

        // Filtrar por ano se especificado
        if ($ano) {
            $documents = array_filter($documents, function($doc) use ($ano) {
                return $doc['ano'] == $ano;
            });
        }

        // Busca por texto se especificado
        if ($search) {
            $documents = array_filter($documents, function($doc) use ($search) {
                return stripos($doc['titulo'], $search) !== false ||
                       stripos($doc['ementa'], $search) !== false ||
                       stripos($doc['numero'], $search) !== false;
            });
        }

        return response()->json([
            'data' => array_values($documents),
            'meta' => [
                'total' => count($documents),
                'filtros' => [
                    'tipo' => $tipo,
                    'status' => $status,
                    'ano' => $ano,
                    'search' => $search
                ]
            ]
        ]);
    }

    /**
     * Obter documento específico por ID
     */
    public function getDocument(Request $request, int $id): JsonResponse
    {
        $documents = $this->getAvailableDocuments();
        
        foreach ($documents as $doc) {
            if ($doc['id'] === $id) {
                return response()->json([
                    'data' => $doc
                ]);
            }
        }

        return response()->json([
            'error' => 'Documento não encontrado',
            'message' => 'Documento com ID ' . $id . ' não foi encontrado'
        ], 404);
    }

    /**
     * Gerar dados mock de documentos/projetos disponíveis
     */
    private function getAvailableDocuments(): array
    {
        return Cache::remember('mock_available_documents', 3600, function() {
            return [
                [
                    'id' => 1,
                    'numero' => '001',
                    'ano' => 2024,
                    'tipo_id' => 135,
                    'tipo_descricao' => 'Projeto de Lei',
                    'titulo' => 'Criação do Programa Municipal de Educação Ambiental',
                    'ementa' => 'Dispõe sobre a criação de programa de educação ambiental nas escolas municipais',
                    'autor_id' => 1,
                    'autor_nome' => 'João Silva Santos',
                    'status' => 'em_tramitacao',
                    'status_descricao' => 'Em Tramitação',
                    'urgencia' => 'normal',
                    'data_protocolo' => '2024-01-15',
                    'created_at' => '2024-01-15T10:00:00Z',
                    'updated_at' => '2024-01-15T10:00:00Z'
                ],
                [
                    'id' => 2,
                    'numero' => '002',
                    'ano' => 2024,
                    'tipo_id' => 135,
                    'tipo_descricao' => 'Projeto de Lei',
                    'titulo' => 'Incentivo ao Transporte Sustentável',
                    'ementa' => 'Cria incentivos para o uso de bicicletas e transporte público na cidade',
                    'autor_id' => 2,
                    'autor_nome' => 'Maria Santos Oliveira',
                    'status' => 'em_tramitacao',
                    'status_descricao' => 'Em Tramitação',
                    'urgencia' => 'normal',
                    'data_protocolo' => '2024-02-20',
                    'created_at' => '2024-02-20T14:30:00Z',
                    'updated_at' => '2024-02-20T14:30:00Z'
                ],
                [
                    'id' => 3,
                    'numero' => '003',
                    'ano' => 2024,
                    'tipo_id' => 138,
                    'tipo_descricao' => 'Projeto de Resolução',
                    'titulo' => 'Alteração do Regimento Interno da Câmara',
                    'ementa' => 'Altera dispositivos do Regimento Interno para modernização dos processos',
                    'autor_id' => 3,
                    'autor_nome' => 'Carlos Eduardo Pereira',
                    'status' => 'protocolado',
                    'status_descricao' => 'Protocolado',
                    'urgencia' => 'urgente',
                    'data_protocolo' => '2024-03-05',
                    'created_at' => '2024-03-05T09:15:00Z',
                    'updated_at' => '2024-03-05T09:15:00Z'
                ],
                [
                    'id' => 4,
                    'numero' => '001',
                    'ano' => 2024,
                    'tipo_id' => 140,
                    'tipo_descricao' => 'Requerimento',
                    'titulo' => 'Informações sobre Obras da Praça Central',
                    'ementa' => 'Solicita informações detalhadas sobre o andamento das obras de revitalização da Praça Central',
                    'autor_id' => 2,
                    'autor_nome' => 'Maria Santos Oliveira',
                    'status' => 'protocolado',
                    'status_descricao' => 'Protocolado',
                    'urgencia' => 'normal',
                    'data_protocolo' => '2024-03-10',
                    'created_at' => '2024-03-10T16:45:00Z',
                    'updated_at' => '2024-03-10T16:45:00Z'
                ],
                [
                    'id' => 5,
                    'numero' => '002',
                    'ano' => 2024,
                    'tipo_id' => 140,
                    'tipo_descricao' => 'Requerimento',
                    'titulo' => 'Criação de Comissão de Estudos Ambientais',
                    'ementa' => 'Requer a criação de comissão especial para estudar impactos ambientais na região',
                    'autor_id' => 4,
                    'autor_nome' => 'Ana Paula Costa',
                    'status' => 'em_tramitacao',
                    'status_descricao' => 'Em Tramitação',
                    'urgencia' => 'normal',
                    'data_protocolo' => '2024-03-15',
                    'created_at' => '2024-03-15T11:20:00Z',
                    'updated_at' => '2024-03-15T11:20:00Z'
                ],
                [
                    'id' => 6,
                    'numero' => '001',
                    'ano' => 2024,
                    'tipo_id' => 141,
                    'tipo_descricao' => 'Indicação',
                    'titulo' => 'Melhorias na Iluminação Pública do Bairro Centro',
                    'ementa' => 'Indica ao Executivo a necessidade de melhorias na iluminação pública',
                    'autor_id' => 5,
                    'autor_nome' => 'Roberto Mendes Lima',
                    'status' => 'protocolado',
                    'status_descricao' => 'Protocolado',
                    'urgencia' => 'normal',
                    'data_protocolo' => '2024-04-01',
                    'created_at' => '2024-04-01T08:30:00Z',
                    'updated_at' => '2024-04-01T08:30:00Z'
                ],
                [
                    'id' => 7,
                    'numero' => '004',
                    'ano' => 2024,
                    'tipo_id' => 135,
                    'tipo_descricao' => 'Projeto de Lei',
                    'titulo' => 'Marco Regulatório do Saneamento Municipal',
                    'ementa' => 'Estabelece diretrizes para o saneamento básico no município',
                    'autor_id' => 1,
                    'autor_nome' => 'João Silva Santos',
                    'status' => 'em_tramitacao',
                    'status_descricao' => 'Em Tramitação',
                    'urgencia' => 'urgentissima',
                    'data_protocolo' => '2024-04-10',
                    'created_at' => '2024-04-10T13:15:00Z',
                    'updated_at' => '2024-04-10T13:15:00Z'
                ],
                [
                    'id' => 8,
                    'numero' => '003',
                    'ano' => 2024,
                    'tipo_id' => 140,
                    'tipo_descricao' => 'Requerimento',
                    'titulo' => 'Informações sobre Licitação de Transporte Escolar',
                    'ementa' => 'Solicita cópia do processo licitatório para contratação de transporte escolar',
                    'autor_id' => 3,
                    'autor_nome' => 'Carlos Eduardo Pereira',
                    'status' => 'protocolado',
                    'status_descricao' => 'Protocolado',
                    'urgencia' => 'urgente',
                    'data_protocolo' => '2024-04-15',
                    'created_at' => '2024-04-15T10:45:00Z',
                    'updated_at' => '2024-04-15T10:45:00Z'
                ]
            ];
        });
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
    
    /**
     * Listar todas as comissões
     */
    public function comissoes(Request $request): JsonResponse
    {
        $comissoes = $this->getDefaultComissoes();
        
        // Aplicar filtros se fornecidos
        if ($request->has('tipo')) {
            $comissoes = array_filter($comissoes, function($comissao) use ($request) {
                return $comissao['tipo'] === $request->input('tipo');
            });
        }
        
        if ($request->has('status')) {
            $comissoes = array_filter($comissoes, function($comissao) use ($request) {
                return $comissao['status'] === $request->input('status');
            });
        }
        
        return response()->json([
            'data' => array_values($comissoes),
            'meta' => [
                'total' => count($comissoes),
                'page' => 1,
                'per_page' => 50
            ]
        ]);
    }
    
    /**
     * Obter comissão específica
     */
    public function getComissao(int $id): JsonResponse
    {
        $comissoes = $this->getDefaultComissoes();
        
        foreach ($comissoes as $comissao) {
            if ($comissao['id'] == $id) {
                return response()->json([
                    'data' => $comissao
                ]);
            }
        }
        
        return response()->json([
            'error' => 'Comissão não encontrada'
        ], 404);
    }
    
    /**
     * Criar nova comissão
     */
    public function createComissao(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:200',
            'tipo' => 'required|string',
            'finalidade' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 400);
        }
        
        $comissoes = Cache::get('mock_api_comissoes', $this->getDefaultComissoes());
        
        $novaComissao = [
            'id' => count($comissoes) + 1,
            'nome' => $request->input('nome'),
            'descricao' => $request->input('descricao', ''),
            'tipo' => $request->input('tipo'),
            'status' => $request->input('status', 'ativa'),
            'presidente' => $request->input('presidente_id') ? $this->getParlamentarById($request->input('presidente_id')) : null,
            'vice_presidente' => $request->input('vice_presidente_id') ? $this->getParlamentarById($request->input('vice_presidente_id')) : null,
            'relator' => $request->input('relator_id') ? $this->getParlamentarById($request->input('relator_id')) : null,
            'membros' => $request->input('membros', []),
            'total_membros' => count($request->input('membros', [])),
            'finalidade' => $request->input('finalidade'),
            'data_criacao' => now()->format('Y-m-d'),
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString()
        ];
        
        $comissoes[] = $novaComissao;
        Cache::put('mock_api_comissoes', $comissoes, now()->addHours(24));
        
        return response()->json([
            'data' => $novaComissao,
            'message' => 'Comissão criada com sucesso'
        ], 201);
    }
    
    /**
     * Atualizar comissão
     */
    public function updateComissao(Request $request, int $id): JsonResponse
    {
        $comissoes = Cache::get('mock_api_comissoes', $this->getDefaultComissoes());
        
        foreach ($comissoes as $index => $comissao) {
            if ($comissao['id'] == $id) {
                $comissoes[$index] = array_merge($comissao, $request->all(), [
                    'updated_at' => now()->toISOString()
                ]);
                
                Cache::put('mock_api_comissoes', $comissoes, now()->addHours(24));
                
                return response()->json([
                    'data' => $comissoes[$index],
                    'message' => 'Comissão atualizada com sucesso'
                ]);
            }
        }
        
        return response()->json([
            'error' => 'Comissão não encontrada'
        ], 404);
    }
    
    /**
     * Deletar comissão
     */
    public function deleteComissao(int $id): JsonResponse
    {
        $comissoes = Cache::get('mock_api_comissoes', $this->getDefaultComissoes());
        
        foreach ($comissoes as $index => $comissao) {
            if ($comissao['id'] == $id) {
                unset($comissoes[$index]);
                $comissoes = array_values($comissoes);
                
                Cache::put('mock_api_comissoes', $comissoes, now()->addHours(24));
                
                return response()->json([
                    'message' => 'Comissão deletada com sucesso'
                ]);
            }
        }
        
        return response()->json([
            'error' => 'Comissão não encontrada'
        ], 404);
    }
    
    /**
     * Listar comissões por tipo
     */
    public function comissoesByTipo(string $tipo): JsonResponse
    {
        $comissoes = $this->getDefaultComissoes();
        
        $comissoesFiltradas = array_filter($comissoes, function($comissao) use ($tipo) {
            return $comissao['tipo'] === $tipo;
        });
        
        return response()->json([
            'data' => array_values($comissoesFiltradas),
            'meta' => [
                'tipo' => $tipo,
                'total' => count($comissoesFiltradas)
            ]
        ]);
    }
    
    /**
     * Listar comissões por status
     */
    public function comissoesByStatus(string $status): JsonResponse
    {
        $comissoes = $this->getDefaultComissoes();
        
        $comissoesFiltradas = array_filter($comissoes, function($comissao) use ($status) {
            return $comissao['status'] === $status;
        });
        
        return response()->json([
            'data' => array_values($comissoesFiltradas),
            'meta' => [
                'status' => $status,
                'total' => count($comissoesFiltradas)
            ]
        ]);
    }
    
    /**
     * Obter membros de uma comissão
     */
    public function membrosComissao(int $id): JsonResponse
    {
        $comissoes = $this->getDefaultComissoes();
        
        foreach ($comissoes as $comissao) {
            if ($comissao['id'] == $id) {
                return response()->json([
                    'membros' => $comissao['membros'] ?? [],
                    'meta' => [
                        'comissao_id' => $id,
                        'total_membros' => count($comissao['membros'] ?? [])
                    ]
                ]);
            }
        }
        
        return response()->json([
            'error' => 'Comissão não encontrada'
        ], 404);
    }
    
    /**
     * Obter reuniões de uma comissão
     */
    public function reunioesComissao(int $id): JsonResponse
    {
        // Dados simulados de reuniões
        $reunioes = [
            [
                'id' => 1,
                'data' => '2024-01-15',
                'hora' => '14:00',
                'local' => 'Sala de Reuniões 1',
                'pauta' => 'Discussão sobre projeto de lei educacional',
                'status' => 'realizada'
            ],
            [
                'id' => 2,
                'data' => '2024-01-22',
                'hora' => '10:00',
                'local' => 'Plenário',
                'pauta' => 'Análise de relatórios técnicos',
                'status' => 'realizada'
            ]
        ];
        
        return response()->json([
            'reunioes' => $reunioes,
            'meta' => [
                'comissao_id' => $id,
                'total_reunioes' => count($reunioes)
            ]
        ]);
    }
    
    /**
     * Buscar comissões
     */
    public function searchComissoes(Request $request): JsonResponse
    {
        $termo = $request->input('q', '');
        $comissoes = $this->getDefaultComissoes();
        
        if (empty($termo)) {
            return response()->json([
                'data' => [],
                'meta' => ['total' => 0, 'termo' => '']
            ]);
        }
        
        $comissoesFiltradas = array_filter($comissoes, function($comissao) use ($termo) {
            return stripos($comissao['nome'], $termo) !== false ||
                   stripos($comissao['descricao'], $termo) !== false ||
                   stripos($comissao['finalidade'], $termo) !== false;
        });
        
        return response()->json([
            'data' => array_values($comissoesFiltradas),
            'meta' => [
                'total' => count($comissoesFiltradas),
                'termo' => $termo
            ]
        ]);
    }
    
    /**
     * Obter estatísticas das comissões
     */
    public function estatisticasComissoes(): JsonResponse
    {
        $comissoes = $this->getDefaultComissoes();
        
        $estatisticas = [
            'total' => count($comissoes),
            'ativas' => count(array_filter($comissoes, fn($c) => $c['status'] === 'ativa')),
            'permanentes' => count(array_filter($comissoes, fn($c) => $c['tipo'] === 'permanente')),
            'temporarias' => count(array_filter($comissoes, fn($c) => $c['tipo'] === 'temporaria')),
            'especiais' => count(array_filter($comissoes, fn($c) => $c['tipo'] === 'especial')),
            'cpi' => count(array_filter($comissoes, fn($c) => $c['tipo'] === 'cpi'))
        ];
        
        return response()->json([
            'data' => $estatisticas
        ]);
    }
    
    /**
     * Obter dados padrão das comissões
     */
    private function getDefaultComissoes(): array
    {
        return Cache::get('mock_api_comissoes', [
            [
                'id' => 1,
                'nome' => 'Comissão de Educação e Cultura',
                'descricao' => 'Responsável por analisar projetos relacionados à educação e cultura',
                'tipo' => 'permanente',
                'status' => 'ativa',
                'presidente' => [
                    'id' => 2,
                    'nome' => 'Maria Santos Oliveira',
                    'partido' => 'PSDB'
                ],
                'vice_presidente' => null,
                'relator' => null,
                'membros' => ['João Silva Santos', 'Maria Santos Oliveira', 'Ana Paula Costa'],
                'total_membros' => 3,
                'finalidade' => 'Analisar e emitir pareceres sobre projetos de lei relacionados à educação, cultura, desporto e turismo',
                'data_criacao' => '2024-01-01',
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ],
            [
                'id' => 2,
                'nome' => 'Comissão de Saúde e Assistência Social',
                'descricao' => 'Responsável por analisar projetos relacionados à saúde e assistência social',
                'tipo' => 'permanente',
                'status' => 'ativa',
                'presidente' => [
                    'id' => 4,
                    'nome' => 'Ana Paula Costa',
                    'partido' => 'PSL'
                ],
                'vice_presidente' => null,
                'relator' => null,
                'membros' => ['Ana Paula Costa', 'Roberto Mendes Lima'],
                'total_membros' => 2,
                'finalidade' => 'Analisar e emitir pareceres sobre projetos de lei relacionados à saúde pública e assistência social',
                'data_criacao' => '2024-01-01',
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ],
            [
                'id' => 3,
                'nome' => 'Comissão de Finanças e Orçamento',
                'descricao' => 'Responsável por analisar projetos relacionados às finanças municipais',
                'tipo' => 'permanente',
                'status' => 'ativa',
                'presidente' => [
                    'id' => 3,
                    'nome' => 'Carlos Eduardo Pereira',
                    'partido' => 'MDB'
                ],
                'vice_presidente' => [
                    'id' => 5,
                    'nome' => 'Roberto Mendes Lima',
                    'partido' => 'PDT'
                ],
                'relator' => null,
                'membros' => ['Carlos Eduardo Pereira', 'Roberto Mendes Lima', 'João Silva Santos'],
                'total_membros' => 3,
                'finalidade' => 'Analisar e emitir pareceres sobre o orçamento municipal e projetos que impactem as finanças públicas',
                'data_criacao' => '2024-01-01',
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ],
            [
                'id' => 4,
                'nome' => 'CPI da Transparência Pública',
                'descricao' => 'Comissão Parlamentar de Inquérito para investigar a transparência nos órgãos públicos',
                'tipo' => 'cpi',
                'status' => 'ativa',
                'presidente' => [
                    'id' => 1,
                    'nome' => 'João Silva Santos',
                    'partido' => 'PT'
                ],
                'vice_presidente' => null,
                'relator' => [
                    'id' => 2,
                    'nome' => 'Maria Santos Oliveira',
                    'partido' => 'PSDB'
                ],
                'membros' => ['João Silva Santos', 'Maria Santos Oliveira', 'Carlos Eduardo Pereira'],
                'total_membros' => 3,
                'finalidade' => 'Investigar irregularidades na aplicação de recursos públicos e garantir a transparência dos atos administrativos',
                'data_criacao' => '2024-02-15',
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ],
            [
                'id' => 5,
                'nome' => 'Comissão Especial de Meio Ambiente',
                'descricao' => 'Comissão temporária para tratar de questões ambientais urgentes',
                'tipo' => 'especial',
                'status' => 'ativa',
                'presidente' => [
                    'id' => 5,
                    'nome' => 'Roberto Mendes Lima',
                    'partido' => 'PDT'
                ],
                'vice_presidente' => null,
                'relator' => null,
                'membros' => ['Roberto Mendes Lima', 'Ana Paula Costa'],
                'total_membros' => 2,
                'finalidade' => 'Analisar projetos relacionados ao meio ambiente e sustentabilidade urbana',
                'data_criacao' => '2024-03-01',
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ]
        ]);
    }
    
    /**
     * Helper para buscar parlamentar por ID
     */
    private function getParlamentarById(int $id): ?array
    {
        $parlamentares = Cache::remember('mock_parlamentares', 3600, function() {
            return [
                [
                    'id' => 1,
                    'nome' => 'João Silva Santos',
                    'partido' => 'PT',
                    'status' => 'ativo',
                    'cargo' => 'Vereador'
                ],
                [
                    'id' => 2,
                    'nome' => 'Maria Santos Oliveira',
                    'partido' => 'PSDB',
                    'status' => 'ativo',
                    'cargo' => 'Vereadora'
                ],
                [
                    'id' => 3,
                    'nome' => 'Carlos Eduardo Pereira',
                    'partido' => 'MDB',
                    'status' => 'ativo',
                    'cargo' => 'Presidente da Câmara'
                ],
                [
                    'id' => 4,
                    'nome' => 'Ana Paula Costa',
                    'partido' => 'PSL',
                    'status' => 'licenciada',
                    'cargo' => 'Vereadora'
                ],
                [
                    'id' => 5,
                    'nome' => 'Roberto Mendes Lima',
                    'partido' => 'PDT',
                    'status' => 'ativo',
                    'cargo' => 'Vice-Presidente'
                ]
            ];
        });
        
        foreach ($parlamentares as $parlamentar) {
            if ($parlamentar['id'] == $id) {
                return [
                    'id' => $parlamentar['id'],
                    'nome' => $parlamentar['nome'],
                    'partido' => $parlamentar['partido']
                ];
            }
        }
        
        return null;
    }

    // ============================================================================
    // ENDPOINTS DE SESSÕES
    // ============================================================================

    /**
     * Listar todas as sessões
     */
    public function sessions(Request $request): JsonResponse
    {
        $sessions = $this->getDefaultSessions();
        
        // Aplicar filtros se fornecidos
        if ($request->has('tipo_id')) {
            $sessions = array_filter($sessions, function($s) use ($request) {
                return $s['tipo_id'] == $request->get('tipo_id');
            });
        }
        
        if ($request->has('ano')) {
            $sessions = array_filter($sessions, function($s) use ($request) {
                return $s['ano'] == $request->get('ano');
            });
        }
        
        if ($request->has('status')) {
            $sessions = array_filter($sessions, function($s) use ($request) {
                return $s['status'] === $request->get('status');
            });
        }
        
        if ($request->has('com_votacao') && $request->get('com_votacao') == '1') {
            $sessions = array_filter($sessions, function($s) {
                // Filter sessions that have matters with voting
                $matters = $this->getSessionMatters($s['id']);
                foreach ($matters as $matter) {
                    if (isset($matter['votacao']) && !empty($matter['votacao'])) {
                        return true;
                    }
                }
                return false;
            });
        }
        
        return response()->json([
            'data' => array_values($sessions),
            'meta' => [
                'total' => count($sessions),
                'page' => 1,
                'per_page' => 50
            ]
        ]);
    }

    /**
     * Obter sessão específica
     */
    public function getSession(Request $request, int $id): JsonResponse
    {
        $sessions = Cache::get('mock_api_sessions', $this->getDefaultSessions());
        
        foreach ($sessions as $session) {
            if ($session['id'] === $id) {
                return response()->json([
                    'data' => $session
                ]);
            }
        }
        
        return response()->json([
            'error' => 'Sessão não encontrada',
            'message' => 'Sessão com ID ' . $id . ' não foi encontrada'
        ], 404);
    }

    /**
     * Criar nova sessão
     */
    public function createSession(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'numero' => 'required|integer',
            'ano' => 'required|integer',
            'data' => 'required|date',
            'hora' => 'required|string',
            'tipo_id' => 'required|integer|in:8,9,10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 400);
        }

        $sessions = Cache::get('mock_api_sessions', $this->getDefaultSessions());
        
        $novaSessao = [
            'id' => count($sessions) + 1,
            'numero' => $request->input('numero'),
            'ano' => $request->input('ano'),
            'data' => $request->input('data'),
            'hora' => $request->input('hora'),
            'tipo_id' => $request->input('tipo_id'),
            'tipo_descricao' => $this->getTipoSessaoDescricao($request->input('tipo_id')),
            'status' => 'preparacao',
            'observacoes' => $request->input('observacoes', ''),
            'hora_inicial' => null,
            'hora_final' => null,
            'total_materias' => 0,
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString()
        ];
        
        $sessions[] = $novaSessao;
        Cache::put('mock_api_sessions', $sessions, now()->addHours(24));
        
        return response()->json([
            'message' => 'Sessão criada com sucesso',
            'data' => $novaSessao
        ], 201);
    }

    /**
     * Atualizar sessão
     */
    public function updateSession(Request $request, int $id): JsonResponse
    {
        $sessions = Cache::get('mock_api_sessions', $this->getDefaultSessions());
        $sessionIndex = null;
        
        foreach ($sessions as $index => $session) {
            if ($session['id'] === $id) {
                $sessionIndex = $index;
                break;
            }
        }
        
        if ($sessionIndex === null) {
            return response()->json([
                'error' => 'Sessão não encontrada',
                'message' => 'Sessão com ID ' . $id . ' não foi encontrada'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'numero' => 'sometimes|integer',
            'ano' => 'sometimes|integer',
            'data' => 'sometimes|date',
            'hora' => 'sometimes|string',
            'tipo_id' => 'sometimes|integer|in:8,9,10',
            'status' => 'sometimes|string|in:preparacao,agendada,exportada,concluida',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 400);
        }
        
        // Atualizar campos fornecidos
        $updateFields = ['numero', 'ano', 'data', 'hora', 'tipo_id', 'status', 'observacoes', 'hora_inicial', 'hora_final'];
        
        foreach ($updateFields as $field) {
            if ($request->has($field)) {
                $sessions[$sessionIndex][$field] = $request->input($field);
            }
        }
        
        if ($request->has('tipo_id')) {
            $sessions[$sessionIndex]['tipo_descricao'] = $this->getTipoSessaoDescricao($request->input('tipo_id'));
        }
        
        $sessions[$sessionIndex]['updated_at'] = now()->toISOString();
        
        Cache::put('mock_api_sessions', $sessions, now()->addHours(24));
        
        return response()->json([
            'message' => 'Sessão atualizada com sucesso',
            'data' => $sessions[$sessionIndex]
        ]);
    }

    /**
     * Deletar sessão
     */
    public function deleteSession(Request $request, int $id): JsonResponse
    {
        $sessions = Cache::get('mock_api_sessions', $this->getDefaultSessions());
        $sessionIndex = null;
        
        foreach ($sessions as $index => $session) {
            if ($session['id'] === $id) {
                $sessionIndex = $index;
                break;
            }
        }
        
        if ($sessionIndex === null) {
            return response()->json([
                'error' => 'Sessão não encontrada',
                'message' => 'Sessão com ID ' . $id . ' não foi encontrada'
            ], 404);
        }
        
        $sessionDeletada = $sessions[$sessionIndex];
        unset($sessions[$sessionIndex]);
        $sessions = array_values($sessions);
        
        Cache::put('mock_api_sessions', $sessions, now()->addHours(24));
        
        return response()->json([
            'message' => 'Sessão deletada com sucesso',
            'data' => $sessionDeletada
        ]);
    }

    /**
     * Obter matérias de uma sessão
     */
    public function sessionMatters(Request $request, int $sessionId): JsonResponse
    {
        $matters = $this->getSessionMatters($sessionId);
        
        return response()->json([
            'data' => $matters,
            'meta' => [
                'session_id' => $sessionId,
                'total_matters' => count($matters)
            ]
        ]);
    }

    /**
     * Adicionar matéria à sessão
     */
    public function addSessionMatter(Request $request, int $sessionId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tipo_id' => 'required|integer|in:109,135,138,140,141',
            'numero' => 'required|string',
            'ano' => 'required|integer',
            'descricao' => 'required|string',
            'assunto' => 'required|string',
            'autor_id' => 'required|integer',
            'fase_id' => 'required|integer|in:13,14,15,16,17',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 400);
        }

        $matters = Cache::get("mock_api_session_matters_{$sessionId}", []);
        
        $novaMateria = [
            'id' => count($matters) + 1,
            'session_id' => $sessionId,
            'tipo_id' => $request->input('tipo_id'),
            'tipo_descricao' => $this->getTipoMateriaDescricao($request->input('tipo_id')),
            'numero' => $request->input('numero'),
            'ano' => $request->input('ano'),
            'data' => $request->input('data', now()->format('Y-m-d')),
            'descricao' => $request->input('descricao'),
            'assunto' => $request->input('assunto'),
            'autor_id' => $request->input('autor_id'),
            'autor_nome' => $this->getAutorNome($request->input('autor_id')),
            'fase_id' => $request->input('fase_id'),
            'fase_descricao' => $this->getFaseDescricao($request->input('fase_id')),
            'regime_id' => $request->input('regime_id'),
            'regime_descricao' => $request->input('regime_id') ? $this->getRegimeDescricao($request->input('regime_id')) : null,
            'quorum_id' => $request->input('quorum_id'),
            'quorum_descricao' => $request->input('quorum_id') ? $this->getQuorumDescricao($request->input('quorum_id')) : null,
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString()
        ];
        
        $matters[] = $novaMateria;
        Cache::put("mock_api_session_matters_{$sessionId}", $matters, now()->addHours(24));
        
        // Atualizar contador na sessão
        $this->updateSessionMatterCount($sessionId);
        
        return response()->json([
            'message' => 'Matéria adicionada à sessão com sucesso',
            'data' => $novaMateria
        ], 201);
    }

    /**
     * Atualizar matéria na sessão
     */
    public function updateSessionMatter(Request $request, int $sessionId, int $matterId): JsonResponse
    {
        $matters = Cache::get("mock_api_session_matters_{$sessionId}", []);
        $matterIndex = null;
        
        foreach ($matters as $index => $matter) {
            if ($matter['id'] === $matterId) {
                $matterIndex = $index;
                break;
            }
        }
        
        if ($matterIndex === null) {
            return response()->json([
                'error' => 'Matéria não encontrada',
                'message' => 'Matéria com ID ' . $matterId . ' não foi encontrada nesta sessão'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'tipo_id' => 'sometimes|integer|in:109,135,138,140,141',
            'numero' => 'sometimes|string',
            'ano' => 'sometimes|integer',
            'descricao' => 'sometimes|string',
            'assunto' => 'sometimes|string',
            'autor_id' => 'sometimes|integer',
            'fase_id' => 'sometimes|integer|in:13,14,15,16,17',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 400);
        }
        
        // Atualizar campos
        $updateFields = ['tipo_id', 'numero', 'ano', 'data', 'descricao', 'assunto', 'autor_id', 'fase_id', 'regime_id', 'quorum_id'];
        
        foreach ($updateFields as $field) {
            if ($request->has($field)) {
                $matters[$matterIndex][$field] = $request->input($field);
            }
        }
        
        // Atualizar descrições relacionadas
        if ($request->has('tipo_id')) {
            $matters[$matterIndex]['tipo_descricao'] = $this->getTipoMateriaDescricao($request->input('tipo_id'));
        }
        if ($request->has('autor_id')) {
            $matters[$matterIndex]['autor_nome'] = $this->getAutorNome($request->input('autor_id'));
        }
        if ($request->has('fase_id')) {
            $matters[$matterIndex]['fase_descricao'] = $this->getFaseDescricao($request->input('fase_id'));
        }
        if ($request->has('regime_id')) {
            $matters[$matterIndex]['regime_descricao'] = $this->getRegimeDescricao($request->input('regime_id'));
        }
        if ($request->has('quorum_id')) {
            $matters[$matterIndex]['quorum_descricao'] = $this->getQuorumDescricao($request->input('quorum_id'));
        }
        
        $matters[$matterIndex]['updated_at'] = now()->toISOString();
        
        Cache::put("mock_api_session_matters_{$sessionId}", $matters, now()->addHours(24));
        
        return response()->json([
            'message' => 'Matéria atualizada com sucesso',
            'data' => $matters[$matterIndex]
        ]);
    }

    /**
     * Remover matéria da sessão
     */
    public function removeSessionMatter(Request $request, int $sessionId, int $matterId): JsonResponse
    {
        $matters = Cache::get("mock_api_session_matters_{$sessionId}", []);
        $matterIndex = null;
        
        foreach ($matters as $index => $matter) {
            if ($matter['id'] === $matterId) {
                $matterIndex = $index;
                break;
            }
        }
        
        if ($matterIndex === null) {
            return response()->json([
                'error' => 'Matéria não encontrada',
                'message' => 'Matéria com ID ' . $matterId . ' não foi encontrada nesta sessão'
            ], 404);
        }
        
        $removedMatter = $matters[$matterIndex];
        unset($matters[$matterIndex]);
        $matters = array_values($matters);
        
        Cache::put("mock_api_session_matters_{$sessionId}", $matters, now()->addHours(24));
        
        // Atualizar contador na sessão
        $this->updateSessionMatterCount($sessionId);
        
        return response()->json([
            'message' => 'Matéria removida da sessão com sucesso',
            'data' => $removedMatter
        ]);
    }

    /**
     * Gerar XML da sessão
     */
    public function generateSessionXml(Request $request, int $sessionId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'document_type' => 'required|string|in:expediente,ordem_do_dia',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 400);
        }

        $session = $this->getSessionById($sessionId);
        if (!$session) {
            return response()->json([
                'error' => 'Sessão não encontrada'
            ], 404);
        }

        $matters = $this->getSessionMatters($sessionId);
        $documentType = $request->input('document_type');
        
        // Filtrar matérias por tipo de documento
        $filteredMatters = $this->filterMattersByDocumentType($matters, $documentType);
        
        if (empty($filteredMatters)) {
            return response()->json([
                'error' => 'Nenhuma matéria encontrada para este tipo de documento'
            ], 400);
        }

        $xml = $this->buildSessionXml($session, $filteredMatters, $documentType);
        
        return response()->json([
            'data' => [
                'session_id' => $sessionId,
                'document_type' => $documentType,
                'xml' => $xml,
                'matter_count' => count($filteredMatters),
                'generated_at' => now()->toISOString()
            ]
        ]);
    }

    /**
     * Exportar XML da sessão
     */
    public function exportSessionXml(Request $request, int $sessionId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'xml' => 'required|string',
            'document_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 400);
        }

        // Simular exportação
        $exportId = time() . '_' . $sessionId;
        $exports = Cache::get("mock_api_session_exports_{$sessionId}", []);
        
        $newExport = [
            'id' => $exportId,
            'session_id' => $sessionId,
            'document_type' => $request->input('document_type'),
            'exported_at' => now()->toISOString(),
            'status' => 'success',
            'file_path' => "/exports/session_{$sessionId}_{$exportId}.xml"
        ];
        
        $exports[] = $newExport;
        Cache::put("mock_api_session_exports_{$sessionId}", $exports, now()->addHours(24));
        
        return response()->json([
            'message' => 'XML exportado com sucesso',
            'data' => $newExport
        ]);
    }

    /**
     * Obter histórico de exportações da sessão
     */
    public function sessionExports(Request $request, int $sessionId): JsonResponse
    {
        $exports = Cache::get("mock_api_session_exports_{$sessionId}", []);
        
        return response()->json([
            'data' => $exports,
            'meta' => [
                'session_id' => $sessionId,
                'total_exports' => count($exports)
            ]
        ]);
    }

    /**
     * Obter dados padrão das sessões
     */
    private function getDefaultSessions(): array
    {
        return [
            [
                'id' => 1,
                'numero' => 37,
                'ano' => 2024,
                'data' => '2024-12-02',
                'hora' => '17:00',
                'tipo_id' => 8,
                'tipo_descricao' => 'Ordinária',
                'status' => 'preparacao',
                'observacoes' => 'Sessão para análise de projetos pendentes',
                'hora_inicial' => null,
                'hora_final' => null,
                'total_materias' => 5,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ],
            [
                'id' => 2,
                'numero' => 38,
                'ano' => 2024,
                'data' => '2024-12-09',
                'hora' => '14:00',
                'tipo_id' => 9,
                'tipo_descricao' => 'Extraordinária',
                'status' => 'agendada',
                'observacoes' => 'Sessão extraordinária para votação urgente',
                'hora_inicial' => null,
                'hora_final' => null,
                'total_materias' => 2,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ]
        ];
    }

    /**
     * Obter matérias de uma sessão
     */
    private function getSessionMatters(int $sessionId): array
    {
        if ($sessionId === 1) {
            return [
                [
                    'id' => 1,
                    'session_id' => 1,
                    'tipo_id' => 135,
                    'tipo_descricao' => 'Projeto de Lei',
                    'numero' => '001',
                    'ano' => 2024,
                    'data' => '2024-11-15',
                    'descricao' => 'Dispõe sobre a criação de programa de educação ambiental',
                    'assunto' => 'Educação Ambiental',
                    'autor_id' => 1,
                    'autor_nome' => 'João Silva Santos',
                    'fase_id' => 14,
                    'fase_descricao' => '1ª Discussão',
                    'regime_id' => 6,
                    'regime_descricao' => 'Ordinário',
                    'quorum_id' => 28,
                    'quorum_descricao' => 'Maioria simples'
                ],
                [
                    'id' => 2,
                    'session_id' => 1,
                    'tipo_id' => 140,
                    'tipo_descricao' => 'Requerimento',
                    'numero' => '005',
                    'ano' => 2024,
                    'data' => '2024-11-20',
                    'descricao' => 'Solicita informações sobre obras da Praça Central',
                    'assunto' => 'Obras Públicas',
                    'autor_id' => 2,
                    'autor_nome' => 'Maria Santos Oliveira',
                    'fase_id' => 13,
                    'fase_descricao' => 'Leitura',
                    'regime_id' => null,
                    'regime_descricao' => null,
                    'quorum_id' => null,
                    'quorum_descricao' => null
                ]
            ];
        }
        
        return Cache::get("mock_api_session_matters_{$sessionId}", []);
    }

    /**
     * Auxiliares para descrições
     */
    private function getTipoSessaoDescricao(int $tipoId): string
    {
        $tipos = [8 => 'Ordinária', 9 => 'Extraordinária', 10 => 'Solene'];
        return $tipos[$tipoId] ?? 'Desconhecido';
    }

    private function getTipoMateriaDescricao(int $tipoId): string
    {
        $tipos = [
            109 => 'Correspondência Recebida',
            135 => 'Projeto de Lei',
            138 => 'Projeto de Resolução',
            140 => 'Requerimento',
            141 => 'Indicação'
        ];
        return $tipos[$tipoId] ?? 'Desconhecido';
    }

    private function getFaseDescricao(int $faseId): string
    {
        $fases = [
            13 => 'Leitura',
            14 => '1ª Discussão',
            15 => '2ª Discussão',
            16 => '3ª Discussão',
            17 => 'Votação Final'
        ];
        return $fases[$faseId] ?? 'Desconhecido';
    }

    private function getRegimeDescricao(int $regimeId): string
    {
        $regimes = [6 => 'Ordinário', 7 => 'Urgência', 8 => 'Urgência Urgentíssima'];
        return $regimes[$regimeId] ?? 'Desconhecido';
    }

    private function getQuorumDescricao(int $quorumId): string
    {
        $quorums = [28 => 'Maioria simples', 29 => 'Maioria absoluta', 30 => 'Dois terços'];
        return $quorums[$quorumId] ?? 'Desconhecido';
    }

    private function getAutorNome(int $autorId): string
    {
        $parlamentar = $this->getParlamentarById($autorId);
        return $parlamentar['nome'] ?? 'Autor Desconhecido';
    }

    private function getSessionById(int $sessionId): ?array
    {
        $sessions = Cache::get('mock_api_sessions', $this->getDefaultSessions());
        
        foreach ($sessions as $session) {
            if ($session['id'] === $sessionId) {
                return $session;
            }
        }
        
        return null;
    }

    private function updateSessionMatterCount(int $sessionId): void
    {
        $sessions = Cache::get('mock_api_sessions', $this->getDefaultSessions());
        $matters = $this->getSessionMatters($sessionId);
        
        foreach ($sessions as $index => $session) {
            if ($session['id'] === $sessionId) {
                $sessions[$index]['total_materias'] = count($matters);
                $sessions[$index]['updated_at'] = now()->toISOString();
                break;
            }
        }
        
        Cache::put('mock_api_sessions', $sessions, now()->addHours(24));
    }

    private function filterMattersByDocumentType(array $matters, string $documentType): array
    {
        if ($documentType === 'expediente') {
            // Expediente: Leitura (13) e 1ª Discussão (14)
            return array_filter($matters, function($matter) {
                return in_array($matter['fase_id'], [13, 14]);
            });
        } elseif ($documentType === 'ordem_do_dia') {
            // Ordem do Dia: 1ª, 2ª, 3ª Discussão e Votação Final (14, 15, 16, 17)
            return array_filter($matters, function($matter) {
                return in_array($matter['fase_id'], [14, 15, 16, 17]);
            });
        }
        
        return [];
    }

    private function buildSessionXml(array $session, array $matters, string $documentType): string
    {
        $documentTypeId = $documentType === 'expediente' ? 144 : 145;
        $documentTypeName = $documentType === 'expediente' ? 'Expediente' : 'Ordem do dia';
        
        // Agrupar matérias por fase
        $mattersByPhase = [];
        foreach ($matters as $matter) {
            $phaseId = $matter['fase_id'];
            if (!isset($mattersByPhase[$phaseId])) {
                $mattersByPhase[$phaseId] = [];
            }
            $mattersByPhase[$phaseId][] = $matter;
        }
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sessao id="' . $session['id'] . '">' . "\n";
        $xml .= '  <tipo id="' . $session['tipo_id'] . '">' . "\n";
        $xml .= '    <descricao>' . htmlspecialchars($session['tipo_descricao']) . '</descricao>' . "\n";
        $xml .= '  </tipo>' . "\n";
        $xml .= '  <numero>' . $session['numero'] . '</numero>' . "\n";
        $xml .= '  <ano>' . $session['ano'] . '</ano>' . "\n";
        $xml .= '  <data>' . $session['data'] . '</data>' . "\n";
        $xml .= '  <hora>' . $session['hora'] . ':00.0000000-03:00</hora>' . "\n";
        
        if ($session['hora_inicial']) {
            $xml .= '  <horaInicial>' . $session['hora_inicial'] . '</horaInicial>' . "\n";
        }
        if ($session['hora_final']) {
            $xml .= '  <horaFinal>' . $session['hora_final'] . '</horaFinal>' . "\n";
        }
        
        $xml .= '  <sessao-documento id="' . $documentTypeId . '">' . "\n";
        $xml .= '    <tipo id="' . $documentTypeId . '">' . "\n";
        $xml .= '      <descricao>' . $documentTypeName . '</descricao>' . "\n";
        $xml .= '    </tipo>' . "\n";
        $xml .= '    <numero>' . $session['numero'] . '</numero>' . "\n";
        $xml .= '    <ano>' . $session['ano'] . '</ano>' . "\n";
        $xml .= '    <data>' . $session['data'] . '</data>' . "\n";
        $xml .= '    <observacoes>' . htmlspecialchars($session['observacoes']) . '</observacoes>' . "\n";
        $xml .= '  </sessao-documento>' . "\n";
        
        $xml .= '  <fases>' . "\n";
        
        foreach ($mattersByPhase as $phaseId => $phaseMatters) {
            $phaseName = $this->getFaseDescricao($phaseId);
            $xml .= '    <fase id="' . $phaseId . '" valor="' . htmlspecialchars($phaseName) . '">' . "\n";
            $xml .= '      <materias>' . "\n";
            
            foreach ($phaseMatters as $matter) {
                $xml .= '        <materia id="' . $matter['id'] . '">' . "\n";
                $xml .= '          <tipo id="' . $matter['tipo_id'] . '">' . "\n";
                $xml .= '            <descricao>' . htmlspecialchars($matter['tipo_descricao']) . '</descricao>' . "\n";
                $xml .= '          </tipo>' . "\n";
                $xml .= '          <numero>' . htmlspecialchars($matter['numero']) . '</numero>' . "\n";
                $xml .= '          <ano>' . $matter['ano'] . '</ano>' . "\n";
                $xml .= '          <data>' . $matter['data'] . '</data>' . "\n";
                $xml .= '          <descricao>' . htmlspecialchars($matter['descricao']) . '</descricao>' . "\n";
                $xml .= '          <assunto>' . htmlspecialchars($matter['assunto']) . '</assunto>' . "\n";
                
                if ($matter['regime_id']) {
                    $xml .= '          <regime id="' . $matter['regime_id'] . '">' . "\n";
                    $xml .= '            <descricao>' . htmlspecialchars($matter['regime_descricao']) . '</descricao>' . "\n";
                    $xml .= '          </regime>' . "\n";
                }
                
                if ($matter['quorum_id']) {
                    $xml .= '          <quorum id="' . $matter['quorum_id'] . '">' . "\n";
                    $xml .= '            <descricao>' . htmlspecialchars($matter['quorum_descricao']) . '</descricao>' . "\n";
                    $xml .= '          </quorum>' . "\n";
                }
                
                $xml .= '          <autoria>' . "\n";
                $xml .= '            <autor id="' . $matter['autor_id'] . '">' . "\n";
                $xml .= '              <nome>' . htmlspecialchars($matter['autor_nome']) . '</nome>' . "\n";
                $xml .= '              <apelido>' . htmlspecialchars($matter['autor_nome']) . '</apelido>' . "\n";
                $xml .= '              <usar-apelido>false</usar-apelido>' . "\n";
                $xml .= '              <iniciativa>Parlamentar</iniciativa>' . "\n";
                $xml .= '            </autor>' . "\n";
                $xml .= '          </autoria>' . "\n";
                $xml .= '        </materia>' . "\n";
            }
            
            $xml .= '      </materias>' . "\n";
            $xml .= '    </fase>' . "\n";
        }
        
        $xml .= '  </fases>' . "\n";
        $xml .= '</sessao>';
        
        return $xml;
    }
} 