# 🚀 Documentação da API - LegisInc

## 📋 Visão Geral

**Versão**: 1.0.0  
**Última Atualização**: 2025-07-12  
**Base URL**: `/api/v1`

Esta documentação descreve a API RESTful do sistema LegisInc, desenvolvida para gestão legislativa com Laravel 12. A API segue padrões REST e utiliza autenticação baseada em tokens.

## 🔧 Configuração Inicial

### Headers Obrigatórios

```http
Accept: application/json
Content-Type: application/json
Authorization: Bearer {token}
```

### Respostas Padrão

#### Sucesso (200)
```json
{
  "success": true,
  "data": {...},
  "message": "Operação realizada com sucesso",
  "meta": {
    "timestamp": "2025-07-12T10:30:00Z",
    "version": "1.0.0"
  }
}
```

#### Erro (4xx/5xx)
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Dados inválidos",
    "details": {...}
  },
  "meta": {
    "timestamp": "2025-07-12T10:30:00Z",
    "version": "1.0.0"
  }
}
```

## 🔐 Autenticação

### POST /api/v1/auth/login
Autenticação de usuário no sistema.

**Parâmetros:**
```json
{
  "email": "usuario@exemplo.com",
  "password": "senha123"
}
```

**Resposta:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "João Silva",
      "email": "joao@exemplo.com",
      "roles": ["PARLAMENTAR"],
      "permissions": ["projetos.create", "projetos.read"]
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "expires_at": "2025-07-13T10:30:00Z"
  }
}
```

### POST /api/v1/auth/logout
Logout do usuário autenticado.

### POST /api/v1/auth/refresh
Renovação do token de autenticação.

## 👥 Gestão de Usuários

### GET /api/v1/users
Lista todos os usuários do sistema.

**Parâmetros de Query:**
- `page`: Página atual (padrão: 1)
- `per_page`: Itens por página (padrão: 15, máximo: 100)
- `search`: Busca por nome/email
- `role`: Filtrar por perfil
- `status`: Filtrar por status (ativo/inativo)

**Resposta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "João Silva",
      "email": "joao@exemplo.com",
      "documento": "123.456.789-00",
      "telefone": "(11) 99999-9999",
      "cargo_atual": "Vereador",
      "partido": "PSDB",
      "status": "ativo",
      "roles": ["PARLAMENTAR"],
      "ultimo_acesso": "2025-07-12T09:30:00Z",
      "created_at": "2025-01-01T00:00:00Z",
      "updated_at": "2025-07-12T09:30:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 45,
    "last_page": 3
  }
}
```

### POST /api/v1/users
Criar novo usuário.

**Parâmetros:**
```json
{
  "name": "Maria Santos",
  "email": "maria@exemplo.com",
  "password": "senha123",
  "documento": "987.654.321-00",
  "telefone": "(11) 88888-8888",
  "data_nascimento": "1980-05-15",
  "profissao": "Advogada",
  "cargo_atual": "Vereadora",
  "partido": "PT",
  "roles": ["PARLAMENTAR"]
}
```

### GET /api/v1/users/{id}
Buscar usuário específico.

### PUT /api/v1/users/{id}
Atualizar dados do usuário.

### DELETE /api/v1/users/{id}
Excluir usuário (soft delete).

## 🏛️ Gestão de Parlamentares

### GET /api/v1/parlamentares
Lista todos os parlamentares.

**Parâmetros de Query:**
- `page`: Página atual
- `per_page`: Itens por página
- `search`: Busca por nome/partido
- `partido`: Filtrar por partido
- `status`: Filtrar por status
- `cargo`: Filtrar por cargo

**Resposta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nome": "João Silva",
      "partido": "PSDB",
      "cargo": "Vereador",
      "status": "ativo",
      "email": "joao@camara.gov.br",
      "telefone": "(11) 99999-9999",
      "data_nascimento": "1975-03-20",
      "idade": 49,
      "profissao": "Advogado",
      "escolaridade": "Superior Completo",
      "comissoes": [
        {
          "nome": "Comissão de Finanças",
          "funcao": "Presidente",
          "periodo": "2025-2026"
        }
      ],
      "mandatos": [
        {
          "periodo": "2021-2024",
          "status": "concluido"
        },
        {
          "periodo": "2025-2028",
          "status": "atual"
        }
      ],
      "is_mesa_diretora": true,
      "total_comissoes": 2,
      "created_at": "2025-01-01T00:00:00Z",
      "updated_at": "2025-07-12T09:30:00Z"
    }
  ]
}
```

### POST /api/v1/parlamentares
Criar novo parlamentar.

### GET /api/v1/parlamentares/{id}
Buscar parlamentar específico.

### PUT /api/v1/parlamentares/{id}
Atualizar dados do parlamentar.

### DELETE /api/v1/parlamentares/{id}
Excluir parlamentar.

## 📄 Gestão de Projetos

### GET /api/v1/projetos
Lista todos os projetos.

**Parâmetros de Query:**
- `page`: Página atual
- `per_page`: Itens por página
- `search`: Busca por título/número
- `tipo`: Filtrar por tipo de projeto
- `status`: Filtrar por status
- `autor_id`: Filtrar por autor
- `relator_id`: Filtrar por relator
- `urgencia`: Filtrar por urgência
- `ano`: Filtrar por ano

**Resposta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "titulo": "Projeto de Lei sobre Meio Ambiente",
      "numero": "001",
      "ano": 2025,
      "numero_completo": "001/2025",
      "tipo": "projeto_lei_ordinaria",
      "tipo_formatado": "Projeto de Lei Ordinária",
      "status": "em_tramitacao",
      "status_formatado": "Em Tramitação",
      "status_cor": "primary",
      "urgencia": "normal",
      "urgencia_formatada": "Normal",
      "urgencia_cor": "success",
      "resumo": "Projeto que visa melhorar a proteção ambiental",
      "ementa": "Dispõe sobre a proteção do meio ambiente...",
      "palavras_chave": "meio ambiente, sustentabilidade, proteção",
      "version_atual": 2,
      "data_protocolo": "2025-07-01T10:00:00Z",
      "data_limite_tramitacao": "2025-12-31",
      "ativo": true,
      "autor": {
        "id": 1,
        "name": "João Silva",
        "email": "joao@exemplo.com"
      },
      "relator": {
        "id": 2,
        "name": "Maria Santos",
        "email": "maria@exemplo.com"
      },
      "tipo_projeto": {
        "id": 1,
        "nome": "Projeto de Lei Ordinária",
        "codigo": "PLO"
      },
      "tramitacao_atual": {
        "id": 5,
        "acao": "Enviado para Comissão de Meio Ambiente",
        "status_anterior": "protocolado",
        "status_atual": "em_tramitacao",
        "data_acao": "2025-07-10T14:30:00Z"
      },
      "created_at": "2025-07-01T10:00:00Z",
      "updated_at": "2025-07-10T14:30:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 150,
    "last_page": 10
  }
}
```

### POST /api/v1/projetos
Criar novo projeto.

**Parâmetros:**
```json
{
  "titulo": "Projeto de Lei sobre Educação",
  "tipo_projeto_id": 1,
  "resumo": "Projeto que visa melhorar a educação pública",
  "ementa": "Dispõe sobre melhorias na educação pública municipal...",
  "conteudo": "<p>Conteúdo HTML do projeto...</p>",
  "urgencia": "normal",
  "palavras_chave": "educação, escola, ensino",
  "observacoes": "Projeto elaborado com base em consulta pública"
}
```

### GET /api/v1/projetos/{id}
Buscar projeto específico com todas as informações.

**Resposta:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "titulo": "Projeto de Lei sobre Meio Ambiente",
    "conteudo": "<p>Conteúdo completo do projeto...</p>",
    "versions": [
      {
        "id": 1,
        "version_number": 1,
        "changelog": "Versão inicial",
        "conteudo": "<p>Primeira versão...</p>",
        "is_current": false,
        "created_at": "2025-07-01T10:00:00Z"
      },
      {
        "id": 2,
        "version_number": 2,
        "changelog": "Correções na redação",
        "conteudo": "<p>Segunda versão...</p>",
        "is_current": true,
        "created_at": "2025-07-05T15:00:00Z"
      }
    ],
    "anexos": [
      {
        "id": 1,
        "nome": "Estudo de Impacto Ambiental",
        "arquivo": "estudos/impacto-ambiental.pdf",
        "tamanho": 2048000,
        "tipo": "application/pdf",
        "ordem": 1,
        "ativo": true,
        "created_at": "2025-07-02T10:00:00Z"
      }
    ],
    "tramitacao": [
      {
        "id": 1,
        "acao": "Projeto criado",
        "status_anterior": null,
        "status_atual": "rascunho",
        "observacoes": "Projeto inicial",
        "created_at": "2025-07-01T10:00:00Z"
      },
      {
        "id": 2,
        "acao": "Enviado para protocolo",
        "status_anterior": "rascunho",
        "status_atual": "protocolado",
        "observacoes": "Enviado para análise",
        "created_at": "2025-07-01T16:00:00Z"
      }
    ]
  }
}
```

### PUT /api/v1/projetos/{id}
Atualizar projeto existente.

### DELETE /api/v1/projetos/{id}
Excluir projeto (soft delete).

## 🔄 Tramitação de Projetos

### POST /api/v1/projetos/{id}/tramitacao
Adicionar nova tramitação ao projeto.

**Parâmetros:**
```json
{
  "acao": "Enviado para Comissão de Finanças",
  "status_atual": "na_comissao",
  "observacoes": "Projeto encaminhado para análise orçamentária"
}
```

### GET /api/v1/projetos/{id}/tramitacao
Listar histórico de tramitação do projeto.

### PUT /api/v1/projetos/{id}/status
Atualizar status do projeto.

**Parâmetros:**
```json
{
  "status": "aprovado",
  "observacoes": "Projeto aprovado em primeira votação"
}
```

## 🗂️ Gestão de Anexos

### POST /api/v1/projetos/{id}/anexos
Adicionar anexo ao projeto.

**Parâmetros (multipart/form-data):**
```
arquivo: [file]
nome: "Relatório Técnico"
descricao: "Análise técnica do projeto"
ordem: 1
```

### GET /api/v1/projetos/{id}/anexos
Listar anexos do projeto.

### DELETE /api/v1/projetos/{projeto_id}/anexos/{anexo_id}
Excluir anexo do projeto.

## 📝 Controle de Versões

### POST /api/v1/projetos/{id}/versions
Criar nova versão do projeto.

**Parâmetros:**
```json
{
  "conteudo": "<p>Novo conteúdo da versão...</p>",
  "changelog": "Correções solicitadas pela comissão",
  "tipo_alteracao": "revisao"
}
```

### GET /api/v1/projetos/{id}/versions
Listar versões do projeto.

### GET /api/v1/projetos/{id}/versions/{version_number}
Buscar versão específica do projeto.

## 🏢 Gestão de Tipos de Projeto

### GET /api/v1/tipos-projeto
Lista todos os tipos de projeto disponíveis.

**Resposta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nome": "Projeto de Lei Ordinária",
      "codigo": "PLO",
      "descricao": "Projeto de lei para matérias ordinárias",
      "template": "template-plo.html",
      "campos_obrigatorios": ["titulo", "ementa", "conteudo"],
      "ativo": true,
      "created_at": "2025-01-01T00:00:00Z"
    }
  ]
}
```

### POST /api/v1/tipos-projeto
Criar novo tipo de projeto.

## 📋 Modelos de Projeto

### GET /api/v1/modelos-projeto
Lista todos os modelos de projeto disponíveis.

**Resposta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nome": "Modelo Padrão - Projeto de Lei",
      "descricao": "Template padrão para projetos de lei",
      "tipo_projeto_id": 1,
      "conteudo_modelo": "<p>Art. 1º Esta lei...</p>",
      "campos_variaveis": ["titulo", "objeto", "vigencia"],
      "ativo": true,
      "created_at": "2025-01-01T00:00:00Z"
    }
  ]
}
```

### POST /api/v1/modelos-projeto
Criar novo modelo de projeto.

### GET /api/v1/modelos-projeto/{id}
Buscar modelo específico.

## 📊 Relatórios e Estatísticas

### GET /api/v1/relatorios/projetos
Relatório de projetos com estatísticas.

**Parâmetros de Query:**
- `data_inicio`: Data de início (YYYY-MM-DD)
- `data_fim`: Data de fim (YYYY-MM-DD)
- `tipo`: Filtrar por tipo
- `autor_id`: Filtrar por autor
- `status`: Filtrar por status

**Resposta:**
```json
{
  "success": true,
  "data": {
    "resumo": {
      "total_projetos": 150,
      "por_status": {
        "rascunho": 10,
        "em_tramitacao": 45,
        "aprovado": 60,
        "rejeitado": 15,
        "arquivado": 20
      },
      "por_tipo": {
        "projeto_lei_ordinaria": 100,
        "projeto_lei_complementar": 25,
        "decreto_legislativo": 15,
        "resolucao": 10
      },
      "por_urgencia": {
        "normal": 120,
        "urgente": 25,
        "urgentissima": 5
      }
    },
    "detalhes": [
      {
        "id": 1,
        "titulo": "Projeto de Lei sobre Meio Ambiente",
        "numero_completo": "001/2025",
        "autor": "João Silva",
        "status": "em_tramitacao",
        "data_protocolo": "2025-07-01T10:00:00Z",
        "dias_tramitacao": 11
      }
    ]
  }
}
```

### GET /api/v1/relatorios/parlamentares
Relatório de atividades dos parlamentares.

### GET /api/v1/relatorios/tramitacao
Relatório de tramitação de projetos.

## 🔍 Busca e Filtros

### GET /api/v1/busca
Busca global no sistema.

**Parâmetros de Query:**
- `q`: Termo de busca
- `tipo`: Tipo de conteúdo (projetos, usuarios, parlamentares)
- `filtros`: Filtros específicos por tipo

**Resposta:**
```json
{
  "success": true,
  "data": {
    "projetos": [
      {
        "id": 1,
        "titulo": "Projeto de Lei sobre Meio Ambiente",
        "numero_completo": "001/2025",
        "relevancia": 0.95
      }
    ],
    "usuarios": [
      {
        "id": 1,
        "name": "João Silva",
        "email": "joao@exemplo.com",
        "relevancia": 0.87
      }
    ],
    "parlamentares": [
      {
        "id": 1,
        "nome": "João Silva",
        "partido": "PSDB",
        "relevancia": 0.92
      }
    ]
  }
}
```

## 📈 Métricas e Monitoramento

### GET /api/v1/metricas
Métricas do sistema.

**Resposta:**
```json
{
  "success": true,
  "data": {
    "usuarios": {
      "total": 150,
      "ativos": 120,
      "online": 15
    },
    "projetos": {
      "total": 500,
      "em_tramitacao": 85,
      "aprovados_mes": 12
    },
    "sistema": {
      "versao": "1.0.0",
      "uptime": "15 dias",
      "uso_storage": "2.5 GB"
    }
  }
}
```

## 🔐 Gestão de Permissões

### GET /api/v1/permissoes
Lista todas as permissões disponíveis.

### GET /api/v1/roles
Lista todos os perfis/funções disponíveis.

### POST /api/v1/users/{id}/roles
Atribuir perfil ao usuário.

### DELETE /api/v1/users/{id}/roles/{role}
Remover perfil do usuário.

## 🚨 Códigos de Erro

### Códigos de Status HTTP

| Código | Descrição | Uso |
|--------|-----------|-----|
| 200 | OK | Requisição bem-sucedida |
| 201 | Created | Recurso criado com sucesso |
| 400 | Bad Request | Dados inválidos |
| 401 | Unauthorized | Não autenticado |
| 403 | Forbidden | Não autorizado |
| 404 | Not Found | Recurso não encontrado |
| 422 | Unprocessable Entity | Erro de validação |
| 500 | Internal Server Error | Erro interno do servidor |

### Códigos de Erro Específicos

| Código | Descrição |
|--------|-----------|
| `USER_NOT_FOUND` | Usuário não encontrado |
| `INVALID_CREDENTIALS` | Credenciais inválidas |
| `PERMISSION_DENIED` | Permissão negada |
| `VALIDATION_ERROR` | Erro de validação |
| `PROJECT_NOT_FOUND` | Projeto não encontrado |
| `INVALID_STATUS_TRANSITION` | Transição de status inválida |
| `FILE_UPLOAD_ERROR` | Erro no upload de arquivo |
| `DUPLICATE_ENTRY` | Entrada duplicada |

## 🔧 Configuração do Cliente

### Exemplo de Configuração (JavaScript)

```javascript
// Configuração do cliente API
const apiClient = axios.create({
  baseURL: 'https://api.legisinc.gov.br/api/v1',
  timeout: 10000,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
});

// Interceptor para adicionar token automaticamente
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Interceptor para tratar erros
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Redirecionar para login
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);
```

### Exemplo de Uso (JavaScript)

```javascript
// Buscar projetos
const buscarProjetos = async (filtros = {}) => {
  try {
    const response = await apiClient.get('/projetos', {
      params: filtros
    });
    return response.data;
  } catch (error) {
    console.error('Erro ao buscar projetos:', error);
    throw error;
  }
};

// Criar projeto
const criarProjeto = async (dadosProjeto) => {
  try {
    const response = await apiClient.post('/projetos', dadosProjeto);
    return response.data;
  } catch (error) {
    console.error('Erro ao criar projeto:', error);
    throw error;
  }
};

// Upload de anexo
const uploadAnexo = async (projetoId, arquivo) => {
  try {
    const formData = new FormData();
    formData.append('arquivo', arquivo);
    formData.append('nome', arquivo.name);
    
    const response = await apiClient.post(
      `/projetos/${projetoId}/anexos`,
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }
    );
    return response.data;
  } catch (error) {
    console.error('Erro ao fazer upload:', error);
    throw error;
  }
};
```

## 📝 Guia de Implementação

### 1. Estrutura de Controllers

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Projeto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProjetoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $projetos = Projeto::with(['autor', 'relator', 'tipoProjeto'])
            ->when($request->search, function($query, $search) {
                $query->where('titulo', 'like', "%{$search}%");
            })
            ->when($request->tipo, function($query, $tipo) {
                $query->where('tipo', $tipo);
            })
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $projetos->items(),
            'meta' => [
                'current_page' => $projetos->currentPage(),
                'per_page' => $projetos->perPage(),
                'total' => $projetos->total(),
                'last_page' => $projetos->lastPage(),
                'timestamp' => now()->toISOString(),
                'version' => '1.0.0'
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo_projeto_id' => 'required|exists:tipo_projetos,id',
            'ementa' => 'required|string',
            'conteudo' => 'nullable|string',
            'urgencia' => 'in:normal,urgente,urgentissima',
            'palavras_chave' => 'nullable|string',
            'observacoes' => 'nullable|string'
        ]);

        $projeto = Projeto::create(array_merge($validated, [
            'autor_id' => auth()->id(),
            'ano' => now()->year,
            'status' => 'rascunho'
        ]));

        return response()->json([
            'success' => true,
            'data' => $projeto->load(['autor', 'tipoProjeto']),
            'message' => 'Projeto criado com sucesso'
        ], 201);
    }

    public function show(Projeto $projeto): JsonResponse
    {
        $projeto->load([
            'autor',
            'relator',
            'tipoProjeto',
            'versions',
            'anexos',
            'tramitacao'
        ]);

        return response()->json([
            'success' => true,
            'data' => $projeto
        ]);
    }

    public function update(Request $request, Projeto $projeto): JsonResponse
    {
        $validated = $request->validate([
            'titulo' => 'sometimes|string|max:255',
            'ementa' => 'sometimes|string',
            'conteudo' => 'sometimes|string',
            'urgencia' => 'sometimes|in:normal,urgente,urgentissima',
            'palavras_chave' => 'sometimes|string',
            'observacoes' => 'sometimes|string'
        ]);

        $projeto->update($validated);

        return response()->json([
            'success' => true,
            'data' => $projeto->load(['autor', 'tipoProjeto']),
            'message' => 'Projeto atualizado com sucesso'
        ]);
    }

    public function destroy(Projeto $projeto): JsonResponse
    {
        $projeto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Projeto excluído com sucesso'
        ]);
    }
}
```

### 2. Middleware de API

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiVersionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Adicionar versão da API na resposta
        $response = $next($request);
        
        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            $data['meta'] = array_merge($data['meta'] ?? [], [
                'version' => '1.0.0',
                'timestamp' => now()->toISOString()
            ]);
            $response->setData($data);
        }

        return $response;
    }
}
```

### 3. Resource Classes

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjetoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'numero_completo' => $this->numero_completo,
            'tipo' => $this->tipo,
            'tipo_formatado' => $this->tipo_formatado,
            'status' => $this->status,
            'status_formatado' => $this->status_formatado,
            'status_cor' => $this->status_cor,
            'urgencia' => $this->urgencia,
            'urgencia_formatada' => $this->urgencia_formatada,
            'urgencia_cor' => $this->urgencia_cor,
            'resumo' => $this->resumo,
            'ementa' => $this->ementa,
            'palavras_chave' => $this->palavras_chave,
            'data_protocolo' => $this->data_protocolo,
            'data_limite_tramitacao' => $this->data_limite_tramitacao,
            'autor' => new UserResource($this->whenLoaded('autor')),
            'relator' => new UserResource($this->whenLoaded('relator')),
            'tipo_projeto' => new TipoProjetoResource($this->whenLoaded('tipoProjeto')),
            'tramitacao_atual' => new TramitacaoResource($this->whenLoaded('tramitacaoAtual')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
```

### 4. Rotas da API

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    UserController,
    ParlamentarController,
    ProjetoController,
    TipoProjetoController,
    ModeloProjetoController,
    RelatorioController
};

Route::prefix('v1')->group(function () {
    // Autenticação
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('auth/refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');

    // Rotas protegidas
    Route::middleware('auth:sanctum')->group(function () {
        // Usuários
        Route::apiResource('users', UserController::class);
        Route::post('users/{user}/roles', [UserController::class, 'assignRole']);
        Route::delete('users/{user}/roles/{role}', [UserController::class, 'removeRole']);

        // Parlamentares
        Route::apiResource('parlamentares', ParlamentarController::class);

        // Projetos
        Route::apiResource('projetos', ProjetoController::class);
        Route::post('projetos/{projeto}/tramitacao', [ProjetoController::class, 'addTramitacao']);
        Route::get('projetos/{projeto}/tramitacao', [ProjetoController::class, 'getTramitacao']);
        Route::put('projetos/{projeto}/status', [ProjetoController::class, 'updateStatus']);
        
        // Anexos
        Route::post('projetos/{projeto}/anexos', [ProjetoController::class, 'addAnexo']);
        Route::get('projetos/{projeto}/anexos', [ProjetoController::class, 'getAnexos']);
        Route::delete('projetos/{projeto}/anexos/{anexo}', [ProjetoController::class, 'deleteAnexo']);
        
        // Versões
        Route::post('projetos/{projeto}/versions', [ProjetoController::class, 'createVersion']);
        Route::get('projetos/{projeto}/versions', [ProjetoController::class, 'getVersions']);
        Route::get('projetos/{projeto}/versions/{version}', [ProjetoController::class, 'getVersion']);

        // Tipos de Projeto
        Route::apiResource('tipos-projeto', TipoProjetoController::class);

        // Modelos de Projeto
        Route::apiResource('modelos-projeto', ModeloProjetoController::class);

        // Relatórios
        Route::get('relatorios/projetos', [RelatorioController::class, 'projetos']);
        Route::get('relatorios/parlamentares', [RelatorioController::class, 'parlamentares']);
        Route::get('relatorios/tramitacao', [RelatorioController::class, 'tramitacao']);

        // Busca
        Route::get('busca', [BuscaController::class, 'search']);

        // Métricas
        Route::get('metricas', [MetricasController::class, 'index']);

        // Permissões
        Route::get('permissoes', [PermissaoController::class, 'index']);
        Route::get('roles', [RoleController::class, 'index']);
    });
});
```

## 🧪 Testes

### Exemplo de Teste de API

```php
<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Projeto;
use Laravel\Sanctum\Sanctum;

class ProjetoApiTest extends TestCase
{
    public function test_can_list_projetos()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $projetos = Projeto::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/projetos');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'titulo',
                            'numero_completo',
                            'tipo',
                            'status',
                            'autor',
                            'created_at'
                        ]
                    ],
                    'meta' => [
                        'current_page',
                        'per_page',
                        'total',
                        'last_page'
                    ]
                ]);
    }

    public function test_can_create_projeto()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $tipoProjeto = TipoProjeto::factory()->create();

        $dadosProjeto = [
            'titulo' => 'Novo Projeto de Lei',
            'tipo_projeto_id' => $tipoProjeto->id,
            'ementa' => 'Ementa do projeto',
            'conteudo' => 'Conteúdo do projeto',
            'urgencia' => 'normal'
        ];

        $response = $this->postJson('/api/v1/projetos', $dadosProjeto);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'titulo',
                        'status',
                        'autor'
                    ],
                    'message'
                ]);

        $this->assertDatabaseHas('projetos', [
            'titulo' => 'Novo Projeto de Lei',
            'autor_id' => $user->id
        ]);
    }
}
```

## 🔄 Versionamento

### Estratégia de Versionamento

1. **Semantic Versioning**: Seguir padrão semântico (MAJOR.MINOR.PATCH)
2. **URL Versioning**: Versão na URL (`/api/v1/`, `/api/v2/`)
3. **Backward Compatibility**: Manter compatibilidade por pelo menos 2 versões
4. **Deprecation Policy**: Anunciar descontinuação com 6 meses de antecedência

### Changelog

#### v1.0.0 (2025-07-12)
- Versão inicial da API
- Endpoints básicos para usuários, parlamentares e projetos
- Sistema de autenticação com tokens
- Documentação completa

## 🎯 Próximos Passos

### Melhorias Planejadas

1. **Cache**: Implementar cache Redis para endpoints frequentes
2. **Rate Limiting**: Adicionar limite de requisições por usuário
3. **Webhooks**: Sistema de notificações em tempo real
4. **GraphQL**: Endpoint GraphQL para consultas flexíveis
5. **Documentação Interativa**: Swagger/OpenAPI para testes
6. **Monitoramento**: Métricas detalhadas com Prometheus
7. **Auditoria**: Log completo de todas as operações

---

**Última Atualização**: 2025-07-12  
**Versão da Documentação**: 1.0.0  
**Contato**: equipe-dev@legisinc.gov.br 