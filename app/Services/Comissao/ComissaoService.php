<?php

namespace App\Services\Comissao;

use App\Services\ApiClient\Providers\NodeApiClient;
use App\Services\ApiClient\Exceptions\ApiException;
use Illuminate\Support\Collection;

class ComissaoService
{
    protected NodeApiClient $apiClient;
    
    public function __construct(NodeApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }
    
    /**
     * Buscar todas as comissões
     */
    public function getAll(array $filters = []): Collection
    {
        try {
            $params = [];
            
            if (!empty($filters['tipo'])) {
                $params['tipo'] = $filters['tipo'];
            }
            
            if (!empty($filters['status'])) {
                $params['status'] = $filters['status'];
            }
            
            $queryString = !empty($params) ? '?' . http_build_query($params) : '';
            $response = $this->apiClient->get('/comissoes' . $queryString);
            
            if (!$response->isSuccess()) {
                throw ApiException::fromResponse($response, 'Erro ao buscar comissões');
            }
            
            return collect($response->getData()['data'] ?? []);
            
        } catch (\Exception $e) {
            throw ApiException::connectionError('Erro ao conectar com a API: ' . $e->getMessage());
        }
    }
    
    /**
     * Buscar comissão por ID
     */
    public function getById(int $id): array
    {
        try {
            $response = $this->apiClient->get("/comissoes/{$id}");
            
            if (!$response->isSuccess()) {
                throw ApiException::fromResponse($response, 'Comissão não encontrada');
            }
            
            return $response->getData()['data'] ?? [];
            
        } catch (\Exception $e) {
            throw ApiException::connectionError('Erro ao conectar com a API: ' . $e->getMessage());
        }
    }
    
    /**
     * Criar nova comissão
     */
    public function create(array $data): array
    {
        try {
            $response = $this->apiClient->post('/comissoes', $data);
            
            if (!$response->isSuccess()) {
                throw ApiException::fromResponse($response, 'Erro ao criar comissão');
            }
            
            return $response->getData()['data'] ?? [];
            
        } catch (\Exception $e) {
            throw ApiException::connectionError('Erro ao conectar com a API: ' . $e->getMessage());
        }
    }
    
    /**
     * Atualizar comissão
     */
    public function update(int $id, array $data): array
    {
        try {
            $response = $this->apiClient->put("/comissoes/{$id}", $data);
            
            if (!$response->isSuccess()) {
                throw ApiException::fromResponse($response, 'Erro ao atualizar comissão');
            }
            
            return $response->getData()['data'] ?? [];
            
        } catch (\Exception $e) {
            throw ApiException::connectionError('Erro ao conectar com a API: ' . $e->getMessage());
        }
    }
    
    /**
     * Deletar comissão
     */
    public function delete(int $id): bool
    {
        try {
            $response = $this->apiClient->delete("/comissoes/{$id}");
            
            if (!$response->isSuccess()) {
                throw ApiException::fromResponse($response, 'Erro ao deletar comissão');
            }
            
            return true;
            
        } catch (\Exception $e) {
            throw ApiException::connectionError('Erro ao conectar com a API: ' . $e->getMessage());
        }
    }
    
    /**
     * Buscar comissões por tipo
     */
    public function getByTipo(string $tipo): Collection
    {
        try {
            $response = $this->apiClient->get("/comissoes/tipo/{$tipo}");
            
            if (!$response->isSuccess()) {
                throw ApiException::fromResponse($response, 'Erro ao buscar comissões por tipo');
            }
            
            return collect($response->getData()['data'] ?? []);
            
        } catch (\Exception $e) {
            throw ApiException::connectionError('Erro ao conectar com a API: ' . $e->getMessage());
        }
    }
    
    /**
     * Buscar comissões por status
     */
    public function getByStatus(string $status): Collection
    {
        try {
            $response = $this->apiClient->get("/comissoes/status/{$status}");
            
            if (!$response->isSuccess()) {
                throw ApiException::fromResponse($response, 'Erro ao buscar comissões por status');
            }
            
            return collect($response->getData()['data'] ?? []);
            
        } catch (\Exception $e) {
            throw ApiException::connectionError('Erro ao conectar com a API: ' . $e->getMessage());
        }
    }
    
    /**
     * Buscar membros de uma comissão
     */
    public function getMembros(int $id): array
    {
        try {
            $response = $this->apiClient->get("/comissoes/{$id}/membros");
            
            if (!$response->isSuccess()) {
                throw ApiException::fromResponse($response, 'Erro ao buscar membros da comissão');
            }
            
            return $response->getData() ?? [];
            
        } catch (\Exception $e) {
            throw ApiException::connectionError('Erro ao conectar com a API: ' . $e->getMessage());
        }
    }
    
    /**
     * Buscar reuniões de uma comissão
     */
    public function getReunioes(int $id): array
    {
        try {
            $response = $this->apiClient->get("/comissoes/{$id}/reunioes");
            
            if (!$response->isSuccess()) {
                throw ApiException::fromResponse($response, 'Erro ao buscar reuniões da comissão');
            }
            
            return $response->getData() ?? [];
            
        } catch (\Exception $e) {
            throw ApiException::connectionError('Erro ao conectar com a API: ' . $e->getMessage());
        }
    }
    
    /**
     * Buscar comissões (pesquisa)
     */
    public function search(string $termo): Collection
    {
        try {
            $response = $this->apiClient->get("/comissoes/search?q=" . urlencode($termo));
            
            if (!$response->isSuccess()) {
                throw ApiException::fromResponse($response, 'Erro na busca de comissões');
            }
            
            return collect($response->getData()['data'] ?? []);
            
        } catch (\Exception $e) {
            throw ApiException::connectionError('Erro ao conectar com a API: ' . $e->getMessage());
        }
    }
    
    /**
     * Obter estatísticas das comissões
     */
    public function getEstatisticas(): array
    {
        try {
            $response = $this->apiClient->get('/comissoes/estatisticas');
            
            if (!$response->isSuccess()) {
                // Se não conseguir as estatísticas, retornar valores padrão
                return [
                    'total' => 0,
                    'ativas' => 0,
                    'permanentes' => 0,
                    'temporarias' => 0
                ];
            }
            
            return $response->getData()['data'] ?? [];
            
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'ativas' => 0,
                'permanentes' => 0,
                'temporarias' => 0
            ];
        }
    }
    
    /**
     * Validar dados da comissão
     */
    public function validateData(array $data): array
    {
        $errors = [];
        
        if (empty($data['nome'])) {
            $errors['nome'] = 'O nome da comissão é obrigatório.';
        }
        
        if (empty($data['tipo'])) {
            $errors['tipo'] = 'O tipo da comissão é obrigatório.';
        }
        
        if (empty($data['finalidade'])) {
            $errors['finalidade'] = 'A finalidade da comissão é obrigatória.';
        }
        
        if (!empty($data['nome']) && strlen($data['nome']) < 3) {
            $errors['nome'] = 'O nome deve ter pelo menos 3 caracteres.';
        }
        
        if (!empty($data['nome']) && strlen($data['nome']) > 200) {
            $errors['nome'] = 'O nome não pode ter mais de 200 caracteres.';
        }
        
        return $errors;
    }
    
    /**
     * Formatar comissão para exibição
     */
    public function formatForDisplay(array $comissao): array
    {
        return [
            'id' => $comissao['id'] ?? 0,
            'nome' => $comissao['nome'] ?? 'Nome não informado',
            'descricao' => $comissao['descricao'] ?? '',
            'tipo' => $comissao['tipo'] ?? 'indefinido',
            'tipo_formatado' => $this->formatarTipo($comissao['tipo'] ?? ''),
            'status' => $comissao['status'] ?? 'ativa',
            'status_formatado' => $this->formatarStatus($comissao['status'] ?? ''),
            'presidente' => $comissao['presidente'] ?? null,
            'vice_presidente' => $comissao['vice_presidente'] ?? null,
            'relator' => $comissao['relator'] ?? null,
            'total_membros' => $comissao['total_membros'] ?? 0,
            'membros' => $comissao['membros'] ?? [],
            'finalidade' => $comissao['finalidade'] ?? '',
            'data_criacao' => $comissao['data_criacao'] ?? null,
            'created_at' => $comissao['created_at'] ?? null,
            'updated_at' => $comissao['updated_at'] ?? null,
        ];
    }
    
    /**
     * Formatar tipo da comissão
     */
    private function formatarTipo(string $tipo): string
    {
        return match($tipo) {
            'permanente' => 'Permanente',
            'temporaria' => 'Temporária',
            'especial' => 'Especial',
            'cpi' => 'CPI',
            'mista' => 'Mista',
            default => 'Indefinido'
        };
    }
    
    /**
     * Formatar status da comissão
     */
    private function formatarStatus(string $status): string
    {
        return match($status) {
            'ativa' => 'Ativa',
            'inativa' => 'Inativa',
            'suspensa' => 'Suspensa',
            'encerrada' => 'Encerrada',
            default => 'Indefinido'
        };
    }
}