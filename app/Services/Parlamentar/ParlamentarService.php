<?php

namespace App\Services\Parlamentar;

use App\Services\ApiClient\Providers\NodeApiClient;
use App\Services\ApiClient\DTOs\ApiResponse;
use App\Services\ApiClient\Exceptions\ApiException;
use Illuminate\Support\Collection;

class ParlamentarService
{
    protected NodeApiClient $apiClient;
    
    public function __construct(NodeApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }
    
    /**
     * Obter todos os parlamentares com filtros opcionais
     */
    public function getAll(array $filters = []): Collection
    {
        $response = $this->apiClient->getParlamentares($filters);
        
        if ($response->isSuccess()) {
            return collect($response->data['data'] ?? []);
        }
        
        throw new ApiException('Erro ao buscar parlamentares: ' . $response->getMessage());
    }
    
    /**
     * Obter parlamentar por ID
     */
    public function getById(int $id): array
    {
        $response = $this->apiClient->getParlamentar($id);
        
        if ($response->isSuccess()) {
            return $response->data['data'] ?? [];
        }
        
        throw new ApiException("Erro ao buscar parlamentar ID {$id}: " . $response->getMessage());
    }
    
    /**
     * Criar novo parlamentar
     */
    public function create(array $data): array
    {
        $response = $this->apiClient->createParlamentar($data);
        
        if ($response->isSuccess()) {
            return $response->data['data'] ?? $response->data;
        }
        
        throw new ApiException('Erro ao criar parlamentar: ' . $response->getMessage());
    }
    
    /**
     * Atualizar parlamentar
     */
    public function update(int $id, array $data): array
    {
        $response = $this->apiClient->updateParlamentar($id, $data);
        
        if ($response->isSuccess()) {
            return $response->data['data'] ?? $response->data;
        }
        
        throw new ApiException("Erro ao atualizar parlamentar ID {$id}: " . $response->getMessage());
    }
    
    /**
     * Deletar parlamentar
     */
    public function delete(int $id): bool
    {
        $response = $this->apiClient->deleteParlamentar($id);
        
        if ($response->isSuccess()) {
            return true;
        }
        
        throw new ApiException("Erro ao deletar parlamentar ID {$id}: " . $response->getMessage());
    }
    
    /**
     * Obter parlamentares por partido
     */
    public function getByPartido(string $partido): Collection
    {
        $response = $this->apiClient->getParlamentaresByPartido($partido);
        
        if ($response->isSuccess()) {
            return collect($response->data['data'] ?? []);
        }
        
        throw new ApiException("Erro ao buscar parlamentares do partido {$partido}: " . $response->getMessage());
    }
    
    /**
     * Obter parlamentares por status
     */
    public function getByStatus(string $status): Collection
    {
        $response = $this->apiClient->getParlamentaresByStatus($status);
        
        if ($response->isSuccess()) {
            return collect($response->data['data'] ?? []);
        }
        
        throw new ApiException("Erro ao buscar parlamentares com status {$status}: " . $response->getMessage());
    }
    
    /**
     * Obter mesa diretora
     */
    public function getMesaDiretora(): Collection
    {
        $response = $this->apiClient->getMesaDiretora();
        
        if ($response->isSuccess()) {
            return collect($response->data['data'] ?? []);
        }
        
        throw new ApiException('Erro ao buscar mesa diretora: ' . $response->getMessage());
    }
    
    /**
     * Obter comissões de um parlamentar
     */
    public function getComissoes(int $parlamentarId): array
    {
        $response = $this->apiClient->getComissoesParlamentar($parlamentarId);
        
        if ($response->isSuccess()) {
            return [
                'comissoes' => $response->data['data'] ?? [],
                'meta' => $response->data['meta'] ?? []
            ];
        }
        
        throw new ApiException("Erro ao buscar comissões do parlamentar ID {$parlamentarId}: " . $response->getMessage());
    }
    
    /**
     * Obter estatísticas dos parlamentares
     */
    public function getEstatisticas(): array
    {
        try {
            $todos = $this->getAll();
            $ativos = $this->getByStatus('ativo');
            
            $partidosCount = $todos->groupBy('partido')->map->count();
            $statusCount = $todos->groupBy('status')->map->count();
            
            return [
                'total' => $todos->count(),
                'ativos' => $ativos->count(),
                'inativos' => $todos->where('status', '!=', 'ativo')->count(),
                'por_partido' => $partidosCount->toArray(),
                'por_status' => $statusCount->toArray(),
            ];
        } catch (ApiException $e) {
            throw new ApiException('Erro ao obter estatísticas: ' . $e->getMessage());
        }
    }
    
    /**
     * Buscar parlamentares por termo
     */
    public function search(string $termo): Collection
    {
        $todos = $this->getAll();
        
        return $todos->filter(function ($parlamentar) use ($termo) {
            $termo = strtolower($termo);
            return str_contains(strtolower($parlamentar['nome']), $termo) ||
                   str_contains(strtolower($parlamentar['partido']), $termo) ||
                   str_contains(strtolower($parlamentar['cargo']), $termo) ||
                   str_contains(strtolower($parlamentar['profissao'] ?? ''), $termo);
        });
    }
    
    /**
     * Validar dados de parlamentar
     */
    public function validateData(array $data): array
    {
        $errors = [];
        
        if (empty($data['nome'])) {
            $errors['nome'] = 'Nome é obrigatório';
        }
        
        if (empty($data['partido'])) {
            $errors['partido'] = 'Partido é obrigatório';
        }
        
        if (empty($data['cargo'])) {
            $errors['cargo'] = 'Cargo é obrigatório';
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email válido é obrigatório';
        }
        
        if (empty($data['telefone'])) {
            $errors['telefone'] = 'Telefone é obrigatório';
        }
        
        if (empty($data['data_nascimento'])) {
            $errors['data_nascimento'] = 'Data de nascimento é obrigatória';
        }
        
        return $errors;
    }
    
    /**
     * Formatar dados de parlamentar para exibição
     */
    public function formatForDisplay(array $parlamentar): array
    {
        return [
            'id' => $parlamentar['id'],
            'nome' => $parlamentar['nome'],
            'partido' => strtoupper($parlamentar['partido']),
            'cargo' => $parlamentar['cargo'],
            'status' => ucfirst($parlamentar['status']),
            'email' => $parlamentar['email'],
            'telefone' => $parlamentar['telefone'],
            'data_nascimento' => $parlamentar['data_nascimento'] ? 
                \Carbon\Carbon::parse($parlamentar['data_nascimento'])->format('d/m/Y') : '',
            'profissao' => $parlamentar['profissao'] ?? '',
            'escolaridade' => $parlamentar['escolaridade'] ?? '',
            'comissoes' => $parlamentar['comissoes'] ?? [],
            'total_comissoes' => count($parlamentar['comissoes'] ?? []),
            'mandatos' => $parlamentar['mandatos'] ?? [],
            'created_at' => $parlamentar['created_at'] ? 
                \Carbon\Carbon::parse($parlamentar['created_at'])->format('d/m/Y H:i') : '',
            'updated_at' => $parlamentar['updated_at'] ? 
                \Carbon\Carbon::parse($parlamentar['updated_at'])->format('d/m/Y H:i') : '',
        ];
    }
}