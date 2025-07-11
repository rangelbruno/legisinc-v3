<?php

namespace App\Services\Session;

use App\Services\ApiClient\Providers\NodeApiClient;
use App\Services\ApiClient\DTOs\ApiResponse;
use App\Services\ApiClient\Exceptions\ApiException;
use Illuminate\Support\Facades\Log;
use Exception;

class SessionService
{
    private NodeApiClient $apiClient;

    public function __construct(NodeApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Listar todas as sessões com filtros
     */
    public function listar(array $filtros = []): array
    {
        // Check if we should use mock directly (bypass HTTP for mock mode)
        if (config('api.mode') === 'mock') {
            try {
                $mockController = new \App\Http\Controllers\MockApiController();
                $request = new \Illuminate\Http\Request();
                foreach ($filtros as $key => $value) {
                    $request->merge([$key => $value]);
                }
                
                $response = $mockController->sessions($request);
                $data = json_decode($response->getContent(), true);
                
                return $data;
            } catch (Exception $e) {
                Log::error('Erro ao usar mock direto', [
                    'erro' => $e->getMessage(),
                    'filtros' => $filtros
                ]);
                throw new Exception('Erro ao buscar sessões (mock direto): ' . $e->getMessage());
            }
        }
        
        // For non-mock modes, use HTTP API
        try {
            $response = $this->apiClient->getSessions($filtros);
            
            if (!$response->isSuccess()) {
                throw new Exception($response->getErrorMessage());
            }

            return $response->data;

        } catch (ApiException $e) {
            Log::error('Erro ao listar sessões via API', [
                'erro' => $e->getMessage(),
                'filtros' => $filtros
            ]);
            throw new Exception('Erro ao buscar sessões: ' . $e->getMessage());
        }
    }

    /**
     * Obter sessão por ID
     */
    public function obterPorId(int $id): ?array
    {
        // Check if we should use mock directly (bypass HTTP for mock mode)
        if (config('api.mode') === 'mock') {
            try {
                $mockController = new \App\Http\Controllers\MockApiController();
                $request = new \Illuminate\Http\Request();
                $response = $mockController->getSession($request, $id);
                $data = json_decode($response->getContent(), true);
                
                if ($response->status() === 404) {
                    return null;
                }
                
                return $data['data'] ?? null;
            } catch (Exception $e) {
                Log::error('Erro ao buscar sessão via mock direto', [
                    'erro' => $e->getMessage(),
                    'sessao_id' => $id
                ]);
                throw new Exception('Erro ao buscar sessão (mock direto): ' . $e->getMessage());
            }
        }
        
        // For non-mock modes, use HTTP API
        try {
            $response = $this->apiClient->getSession($id);
            
            if (!$response->isSuccess()) {
                if ($response->status === 404) {
                    return null;
                }
                throw new Exception($response->getErrorMessage());
            }

            return $response->data;

        } catch (ApiException $e) {
            Log::error('Erro ao buscar sessão via API', [
                'erro' => $e->getMessage(),
                'sessao_id' => $id
            ]);
            throw new Exception('Erro ao buscar sessão: ' . $e->getMessage());
        }
    }

    /**
     * Criar nova sessão
     */
    public function criar(array $data): array
    {
        // Check if we should use mock directly (bypass HTTP for mock mode)
        if (config('api.mode') === 'mock') {
            try {
                $this->validarDadosSessao($data);
                
                $mockController = new \App\Http\Controllers\MockApiController();
                $request = new \Illuminate\Http\Request();
                $request->merge($data);
                
                $response = $mockController->createSession($request);
                $responseData = json_decode($response->getContent(), true);
                
                Log::info('Sessão criada com sucesso (mock direto)', [
                    'sessao_id' => $responseData['data']['id'] ?? null,
                    'numero' => $data['numero'] ?? null,
                    'ano' => $data['ano'] ?? null
                ]);
                
                return $responseData['data'];
            } catch (Exception $e) {
                Log::error('Erro ao criar sessão via mock direto', [
                    'erro' => $e->getMessage(),
                    'dados' => $data
                ]);
                throw new Exception('Erro ao criar sessão (mock direto): ' . $e->getMessage());
            }
        }
        
        // For non-mock modes, use HTTP API
        try {
            $this->validarDadosSessao($data);

            $response = $this->apiClient->createSession($data);
            
            if (!$response->isSuccess()) {
                throw new Exception($response->getErrorMessage());
            }

            Log::info('Sessão criada com sucesso', [
                'sessao_id' => $response->data['id'] ?? null,
                'numero' => $data['numero'] ?? null,
                'ano' => $data['ano'] ?? null
            ]);

            return $response->data;

        } catch (ApiException $e) {
            Log::error('Erro ao criar sessão via API', [
                'erro' => $e->getMessage(),
                'dados' => $data
            ]);
            throw new Exception('Erro ao criar sessão: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar sessão
     */
    public function atualizar(int $id, array $data): array
    {
        try {
            $this->validarDadosSessao($data, false);

            $response = $this->apiClient->updateSession($id, $data);
            
            if (!$response->isSuccess()) {
                throw new Exception($response->getErrorMessage());
            }

            Log::info('Sessão atualizada com sucesso', [
                'sessao_id' => $id
            ]);

            return $response->data;

        } catch (ApiException $e) {
            Log::error('Erro ao atualizar sessão via API', [
                'erro' => $e->getMessage(),
                'sessao_id' => $id,
                'dados' => $data
            ]);
            throw new Exception('Erro ao atualizar sessão: ' . $e->getMessage());
        }
    }

    /**
     * Excluir sessão
     */
    public function excluir(int $id): bool
    {
        try {
            $response = $this->apiClient->deleteSession($id);
            
            if (!$response->isSuccess()) {
                throw new Exception($response->getErrorMessage());
            }

            Log::info('Sessão excluída com sucesso', [
                'sessao_id' => $id
            ]);

            return true;

        } catch (ApiException $e) {
            Log::error('Erro ao excluir sessão via API', [
                'erro' => $e->getMessage(),
                'sessao_id' => $id
            ]);
            throw new Exception('Erro ao excluir sessão: ' . $e->getMessage());
        }
    }

    /**
     * Obter matérias de uma sessão
     */
    public function obterMaterias(int $sessionId): array
    {
        // Check if we should use mock directly (bypass HTTP for mock mode)
        if (config('api.mode') === 'mock') {
            try {
                $mockController = new \App\Http\Controllers\MockApiController();
                $request = new \Illuminate\Http\Request();
                $response = $mockController->sessionMatters($request, $sessionId);
                $data = json_decode($response->getContent(), true);
                
                return $data;
            } catch (Exception $e) {
                Log::error('Erro ao buscar matérias via mock direto', [
                    'erro' => $e->getMessage(),
                    'sessao_id' => $sessionId
                ]);
                throw new Exception('Erro ao buscar matérias (mock direto): ' . $e->getMessage());
            }
        }
        
        // For non-mock modes, use HTTP API
        try {
            $response = $this->apiClient->getSessionMatters($sessionId);
            
            if (!$response->isSuccess()) {
                throw new Exception($response->getErrorMessage());
            }

            return $response->data;

        } catch (ApiException $e) {
            Log::error('Erro ao buscar matérias da sessão via API', [
                'erro' => $e->getMessage(),
                'sessao_id' => $sessionId
            ]);
            throw new Exception('Erro ao buscar matérias: ' . $e->getMessage());
        }
    }

    /**
     * Adicionar matéria à sessão
     */
    public function adicionarMateria(int $sessionId, array $data): array
    {
        try {
            $this->validarDadosMateria($data);

            $response = $this->apiClient->addMatterToSession($sessionId, $data);
            
            if (!$response->isSuccess()) {
                throw new Exception($response->getErrorMessage());
            }

            Log::info('Matéria adicionada à sessão', [
                'sessao_id' => $sessionId,
                'materia_id' => $response->data['id'] ?? null
            ]);

            return $response->data;

        } catch (ApiException $e) {
            Log::error('Erro ao adicionar matéria à sessão via API', [
                'erro' => $e->getMessage(),
                'sessao_id' => $sessionId,
                'dados' => $data
            ]);
            throw new Exception('Erro ao adicionar matéria: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar matéria na sessão
     */
    public function atualizarMateria(int $sessionId, int $matterId, array $data): array
    {
        try {
            $this->validarDadosMateria($data, false);

            $response = $this->apiClient->updateSessionMatter($sessionId, $matterId, $data);
            
            if (!$response->isSuccess()) {
                throw new Exception($response->getErrorMessage());
            }

            Log::info('Matéria atualizada na sessão', [
                'sessao_id' => $sessionId,
                'materia_id' => $matterId
            ]);

            return $response->data;

        } catch (ApiException $e) {
            Log::error('Erro ao atualizar matéria na sessão via API', [
                'erro' => $e->getMessage(),
                'sessao_id' => $sessionId,
                'materia_id' => $matterId,
                'dados' => $data
            ]);
            throw new Exception('Erro ao atualizar matéria: ' . $e->getMessage());
        }
    }

    /**
     * Remover matéria da sessão
     */
    public function removerMateria(int $sessionId, int $matterId): bool
    {
        try {
            $response = $this->apiClient->removeSessionMatter($sessionId, $matterId);
            
            if (!$response->isSuccess()) {
                throw new Exception($response->getErrorMessage());
            }

            Log::info('Matéria removida da sessão', [
                'sessao_id' => $sessionId,
                'materia_id' => $matterId
            ]);

            return true;

        } catch (ApiException $e) {
            Log::error('Erro ao remover matéria da sessão via API', [
                'erro' => $e->getMessage(),
                'sessao_id' => $sessionId,
                'materia_id' => $matterId
            ]);
            throw new Exception('Erro ao remover matéria: ' . $e->getMessage());
        }
    }

    /**
     * Gerar XML da sessão
     */
    public function gerarXml(int $sessionId, string $documentType): array
    {
        try {
            if (!in_array($documentType, ['expediente', 'ordem_do_dia'])) {
                throw new Exception('Tipo de documento inválido. Use "expediente" ou "ordem_do_dia"');
            }

            $response = $this->apiClient->generateSessionXml($sessionId, $documentType);
            
            if (!$response->isSuccess()) {
                throw new Exception($response->getErrorMessage());
            }

            Log::info('XML gerado com sucesso', [
                'sessao_id' => $sessionId,
                'document_type' => $documentType
            ]);

            return $response->data;

        } catch (ApiException $e) {
            Log::error('Erro ao gerar XML da sessão via API', [
                'erro' => $e->getMessage(),
                'sessao_id' => $sessionId,
                'document_type' => $documentType
            ]);
            throw new Exception('Erro ao gerar XML: ' . $e->getMessage());
        }
    }

    /**
     * Exportar XML da sessão
     */
    public function exportarXml(int $sessionId, array $xmlData): array
    {
        try {
            $response = $this->apiClient->exportSessionXml($sessionId, $xmlData);
            
            if (!$response->isSuccess()) {
                throw new Exception($response->getErrorMessage());
            }

            Log::info('XML exportado com sucesso', [
                'sessao_id' => $sessionId
            ]);

            return $response->data;

        } catch (ApiException $e) {
            Log::error('Erro ao exportar XML da sessão via API', [
                'erro' => $e->getMessage(),
                'sessao_id' => $sessionId
            ]);
            throw new Exception('Erro ao exportar XML: ' . $e->getMessage());
        }
    }

    /**
     * Obter histórico de exportações
     */
    public function obterHistoricoExportacoes(int $sessionId): array
    {
        // Check if we should use mock directly (bypass HTTP for mock mode)
        if (config('api.mode') === 'mock') {
            try {
                $mockController = new \App\Http\Controllers\MockApiController();
                $request = new \Illuminate\Http\Request();
                $response = $mockController->sessionExports($request, $sessionId);
                $data = json_decode($response->getContent(), true);
                
                return $data;
            } catch (Exception $e) {
                Log::error('Erro ao buscar histórico via mock direto', [
                    'erro' => $e->getMessage(),
                    'sessao_id' => $sessionId
                ]);
                throw new Exception('Erro ao buscar histórico (mock direto): ' . $e->getMessage());
            }
        }
        
        // For non-mock modes, use HTTP API
        try {
            $response = $this->apiClient->getSessionExports($sessionId);
            
            if (!$response->isSuccess()) {
                throw new Exception($response->getErrorMessage());
            }

            return $response->data;

        } catch (ApiException $e) {
            Log::error('Erro ao buscar histórico de exportações via API', [
                'erro' => $e->getMessage(),
                'sessao_id' => $sessionId
            ]);
            throw new Exception('Erro ao buscar histórico: ' . $e->getMessage());
        }
    }

    /**
     * Obter tipos de sessão disponíveis
     */
    public function obterTiposSessao(): array
    {
        return [
            8 => 'Ordinária',
            9 => 'Extraordinária',
            10 => 'Solene'
        ];
    }

    /**
     * Obter tipos de documento disponíveis
     */
    public function obterTiposDocumento(): array
    {
        return [
            144 => 'Expediente',
            145 => 'Ordem do dia'
        ];
    }

    /**
     * Obter tipos de matéria disponíveis
     */
    public function obterTiposMateria(): array
    {
        return [
            109 => 'Correspondência Recebida',
            135 => 'Projeto de Lei',
            138 => 'Projeto de Resolução',
            140 => 'Requerimento',
            141 => 'Indicação'
        ];
    }

    /**
     * Obter fases de tramitação disponíveis
     */
    public function obterFasesTramitacao(): array
    {
        return [
            13 => 'Leitura',
            14 => '1ª Discussão',
            15 => '2ª Discussão',
            16 => '3ª Discussão',
            17 => 'Votação Final'
        ];
    }

    /**
     * Obter regimes de tramitação disponíveis
     */
    public function obterRegimesTramitacao(): array
    {
        return [
            6 => 'Ordinário',
            7 => 'Urgência',
            8 => 'Urgência Urgentíssima'
        ];
    }

    /**
     * Obter tipos de quorum disponíveis
     */
    public function obterTiposQuorum(): array
    {
        return [
            28 => 'Maioria simples',
            29 => 'Maioria absoluta',
            30 => 'Dois terços'
        ];
    }

    /**
     * Validar dados da sessão
     */
    private function validarDadosSessao(array $data, bool $criarNova = true): void
    {
        $tiposValidos = array_keys($this->obterTiposSessao());

        if ($criarNova) {
            if (empty($data['numero'])) {
                throw new Exception('Número da sessão é obrigatório');
            }
            if (empty($data['ano'])) {
                throw new Exception('Ano da sessão é obrigatório');
            }
            if (empty($data['data'])) {
                throw new Exception('Data da sessão é obrigatória');
            }
            if (empty($data['hora'])) {
                throw new Exception('Hora da sessão é obrigatória');
            }
            if (empty($data['tipo_id'])) {
                throw new Exception('Tipo da sessão é obrigatório');
            }
        }

        if (!empty($data['tipo_id']) && !in_array($data['tipo_id'], $tiposValidos)) {
            throw new Exception('Tipo de sessão inválido');
        }

        if (!empty($data['data']) && !strtotime($data['data'])) {
            throw new Exception('Data inválida');
        }

        if (!empty($data['hora']) && !preg_match('/^[0-2][0-9]:[0-5][0-9]$/', $data['hora'])) {
            throw new Exception('Hora inválida (formato: HH:MM)');
        }
    }

    /**
     * Validar dados da matéria
     */
    private function validarDadosMateria(array $data, bool $criarNova = true): void
    {
        $tiposValidos = array_keys($this->obterTiposMateria());
        $fasesValidas = array_keys($this->obterFasesTramitacao());

        if ($criarNova) {
            if (empty($data['tipo_id'])) {
                throw new Exception('Tipo da matéria é obrigatório');
            }
            if (empty($data['numero'])) {
                throw new Exception('Número da matéria é obrigatório');
            }
            if (empty($data['ano'])) {
                throw new Exception('Ano da matéria é obrigatório');
            }
            if (empty($data['descricao'])) {
                throw new Exception('Descrição da matéria é obrigatória');
            }
            if (empty($data['assunto'])) {
                throw new Exception('Assunto da matéria é obrigatório');
            }
            if (empty($data['autor_id'])) {
                throw new Exception('Autor da matéria é obrigatório');
            }
            if (empty($data['fase_id'])) {
                throw new Exception('Fase de tramitação é obrigatória');
            }
        }

        if (!empty($data['tipo_id']) && !in_array($data['tipo_id'], $tiposValidos)) {
            throw new Exception('Tipo de matéria inválido');
        }

        if (!empty($data['fase_id']) && !in_array($data['fase_id'], $fasesValidas)) {
            throw new Exception('Fase de tramitação inválida');
        }

        if (!empty($data['regime_id'])) {
            $regimesValidos = array_keys($this->obterRegimesTramitacao());
            if (!in_array($data['regime_id'], $regimesValidos)) {
                throw new Exception('Regime de tramitação inválido');
            }
        }

        if (!empty($data['quorum_id'])) {
            $quorumsValidos = array_keys($this->obterTiposQuorum());
            if (!in_array($data['quorum_id'], $quorumsValidos)) {
                throw new Exception('Tipo de quorum inválido');
            }
        }
    }
}