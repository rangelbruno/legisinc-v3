<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Parlamentar\ParlamentarService;
use App\Services\ApiClient\Providers\NodeApiClient;
use App\Services\ApiClient\DTOs\ApiResponse;
use App\Services\ApiClient\Exceptions\ApiException;
use Illuminate\Support\Facades\Http;

class ParlamentarServiceTest extends TestCase
{
    protected ParlamentarService $service;
    protected NodeApiClient $apiClient;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurar NodeApiClient para usar mock
        config(['api.mode' => 'mock']);
        config(['api.mock.base_url' => 'http://localhost:8000/api/mock-api']);
        
        $this->apiClient = app(NodeApiClient::class);
        $this->service = new ParlamentarService($this->apiClient);
    }
    
    public function test_can_get_all_parlamentares()
    {
        // Fake da resposta HTTP
        Http::fake([
            'localhost:8000/api/mock-api/parlamentares' => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'nome' => 'João Silva Santos',
                        'partido' => 'PT',
                        'status' => 'ativo',
                        'cargo' => 'Vereador',
                        'telefone' => '(11) 98765-4321',
                        'email' => 'joao.silva@camara.gov.br'
                    ],
                    [
                        'id' => 2,
                        'nome' => 'Maria Santos Oliveira',
                        'partido' => 'PSDB',
                        'status' => 'ativo',
                        'cargo' => 'Vereadora',
                        'telefone' => '(11) 97654-3210',
                        'email' => 'maria.santos@camara.gov.br'
                    ]
                ],
                'meta' => ['total' => 2]
            ], 200)
        ]);
        
        $parlamentares = $this->service->getAll();
        
        $this->assertCount(2, $parlamentares);
        $this->assertEquals('João Silva Santos', $parlamentares->first()['nome']);
        $this->assertEquals('PT', $parlamentares->first()['partido']);
    }
    
    public function test_can_get_parlamentar_by_id()
    {
        Http::fake([
            'localhost:8000/api/mock-api/parlamentares/1' => Http::response([
                'data' => [
                    'id' => 1,
                    'nome' => 'João Silva Santos',
                    'partido' => 'PT',
                    'status' => 'ativo',
                    'cargo' => 'Vereador',
                    'telefone' => '(11) 98765-4321',
                    'email' => 'joao.silva@camara.gov.br'
                ]
            ], 200)
        ]);
        
        $parlamentar = $this->service->getById(1);
        
        $this->assertEquals(1, $parlamentar['id']);
        $this->assertEquals('João Silva Santos', $parlamentar['nome']);
        $this->assertEquals('PT', $parlamentar['partido']);
    }
    
    public function test_can_create_parlamentar()
    {
        $dadosParlamentar = [
            'nome' => 'Carlos Eduardo Pereira',
            'partido' => 'MDB',
            'cargo' => 'Vereador',
            'telefone' => '(11) 96543-2109',
            'email' => 'carlos.pereira@camara.gov.br',
            'data_nascimento' => '1965-11-08',
            'profissao' => 'Empresário',
            'escolaridade' => 'Superior Completo'
        ];
        
        Http::fake([
            'localhost:8000/api/mock-api/parlamentares' => Http::response([
                'message' => 'Parlamentar criado com sucesso',
                'data' => array_merge($dadosParlamentar, ['id' => 6])
            ], 201)
        ]);
        
        $parlamentar = $this->service->create($dadosParlamentar);
        
        $this->assertEquals(6, $parlamentar['id']);
        $this->assertEquals('Carlos Eduardo Pereira', $parlamentar['nome']);
        $this->assertEquals('MDB', $parlamentar['partido']);
    }
    
    public function test_can_get_parlamentares_by_partido()
    {
        Http::fake([
            'localhost:8000/api/mock-api/parlamentares/partido/PT' => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'nome' => 'João Silva Santos',
                        'partido' => 'PT',
                        'status' => 'ativo'
                    ]
                ],
                'meta' => ['total' => 1, 'partido' => 'PT']
            ], 200)
        ]);
        
        $parlamentares = $this->service->getByPartido('PT');
        
        $this->assertCount(1, $parlamentares);
        $this->assertEquals('PT', $parlamentares->first()['partido']);
    }
    
    public function test_can_search_parlamentares()
    {
        Http::fake([
            'localhost:8000/api/mock-api/parlamentares' => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'nome' => 'João Silva Santos',
                        'partido' => 'PT',
                        'status' => 'ativo',
                        'cargo' => 'Vereador',
                        'profissao' => 'Advogado'
                    ],
                    [
                        'id' => 2,
                        'nome' => 'Maria Santos Oliveira',
                        'partido' => 'PSDB',
                        'status' => 'ativo',
                        'cargo' => 'Vereadora',
                        'profissao' => 'Professora'
                    ]
                ]
            ], 200)
        ]);
        
        $resultados = $this->service->search('João');
        
        $this->assertCount(1, $resultados);
        $this->assertEquals('João Silva Santos', $resultados->first()['nome']);
    }
    
    public function test_validate_data_returns_errors_for_invalid_data()
    {
        $dadosInvalidos = [
            'nome' => '',
            'partido' => '',
            'email' => 'email-invalido'
        ];
        
        $errors = $this->service->validateData($dadosInvalidos);
        
        $this->assertArrayHasKey('nome', $errors);
        $this->assertArrayHasKey('partido', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('cargo', $errors);
    }
    
    public function test_format_for_display_formats_data_correctly()
    {
        $parlamentarData = [
            'id' => 1,
            'nome' => 'João Silva Santos',
            'partido' => 'pt',
            'status' => 'ativo',
            'cargo' => 'Vereador',
            'email' => 'joao@test.com',
            'telefone' => '(11) 98765-4321',
            'data_nascimento' => '1975-03-15',
            'profissao' => 'Advogado',
            'escolaridade' => 'Superior Completo',
            'comissoes' => ['Educação', 'Saúde'],
            'mandatos' => [],
            'created_at' => '2023-01-01T10:00:00Z',
            'updated_at' => '2023-01-01T10:00:00Z'
        ];
        
        $formatted = $this->service->formatForDisplay($parlamentarData);
        
        $this->assertEquals('PT', $formatted['partido']); // Deve estar em maiúsculas
        $this->assertEquals('Ativo', $formatted['status']); // Deve estar capitalizado
        $this->assertEquals('15/03/1975', $formatted['data_nascimento']); // Formato brasileiro
        $this->assertEquals(2, $formatted['total_comissoes']);
    }
    
    public function test_throws_exception_when_api_fails()
    {
        Http::fake([
            'localhost:8000/api/mock-api/parlamentares' => Http::response([
                'error' => 'Server error'
            ], 500)
        ]);
        
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Erro ao buscar parlamentares');
        
        $this->service->getAll();
    }
}