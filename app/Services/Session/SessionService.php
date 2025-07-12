<?php

namespace App\Services\Session;

use Illuminate\Support\Facades\Log;
use Exception;

class SessionService
{
    /**
     * Listar todas as sessões (simuladas com dados estáticos)
     */
    public function listar(array $filtros = []): array
    {
        try {
            $sessoes = $this->getSessoesSimuladas();
            
            // Aplicar filtros se fornecidos
            if (!empty($filtros['tipo'])) {
                $sessoes = array_filter($sessoes, function ($sessao) use ($filtros) {
                    return $sessao['tipo'] === $filtros['tipo'];
                });
            }
            
            if (!empty($filtros['status'])) {
                $sessoes = array_filter($sessoes, function ($sessao) use ($filtros) {
                    return $sessao['status'] === $filtros['status'];
                });
            }
            
            if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
                $sessoes = array_filter($sessoes, function ($sessao) use ($filtros) {
                    $dataSessao = $sessao['data'];
                    return $dataSessao >= $filtros['data_inicio'] && $dataSessao <= $filtros['data_fim'];
                });
            }
            
            return [
                'success' => true,
                'data' => array_values($sessoes),
                'total' => count($sessoes),
                'message' => 'Sessões listadas com sucesso'
            ];
            
        } catch (Exception $e) {
            Log::error('Erro ao listar sessões', [
                'erro' => $e->getMessage(),
                'filtros' => $filtros
            ]);
            throw new Exception('Erro ao buscar sessões: ' . $e->getMessage());
        }
    }
    
    /**
     * Buscar sessão por ID
     */
    public function buscarPorId(int $id): array
    {
        try {
            $sessoes = $this->getSessoesSimuladas();
            $sessao = collect($sessoes)->firstWhere('id', $id);
            
            if (!$sessao) {
                throw new Exception('Sessão não encontrada');
            }
            
            return [
                'success' => true,
                'data' => $sessao,
                'message' => 'Sessão encontrada com sucesso'
            ];
            
        } catch (Exception $e) {
            Log::error('Erro ao buscar sessão', [
                'erro' => $e->getMessage(),
                'id' => $id
            ]);
            throw $e;
        }
    }
    
    /**
     * Criar nova sessão
     */
    public function criar(array $dados): array
    {
        try {
            $novoId = rand(1000, 9999);
            
            $novaSessao = [
                'id' => $novoId,
                'numero' => $dados['numero'] ?? $novoId,
                'tipo' => $dados['tipo'] ?? 'ordinaria',
                'data' => $dados['data'] ?? now()->toDateString(),
                'hora_inicio' => $dados['hora_inicio'] ?? '14:00',
                'hora_fim' => $dados['hora_fim'] ?? null,
                'status' => $dados['status'] ?? 'agendada',
                'legislatura' => $dados['legislatura'] ?? '2025-2028',
                'sessao_legislativa' => $dados['sessao_legislativa'] ?? '2025',
                'presidente' => $dados['presidente'] ?? 'Presidente da Sessão',
                'secretario' => $dados['secretario'] ?? 'Secretário da Sessão',
                'local' => $dados['local'] ?? 'Plenário Principal',
                'observacoes' => $dados['observacoes'] ?? '',
                'ordem_dia' => $dados['ordem_dia'] ?? [],
                'materias' => $dados['materias'] ?? [],
                'presencas' => $dados['presencas'] ?? [],
                'votacoes' => $dados['votacoes'] ?? [],
                'ata' => null,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ];
            
            return [
                'success' => true,
                'data' => $novaSessao,
                'message' => 'Sessão criada com sucesso'
            ];
            
        } catch (Exception $e) {
            Log::error('Erro ao criar sessão', [
                'erro' => $e->getMessage(),
                'dados' => $dados
            ]);
            throw new Exception('Erro ao criar sessão: ' . $e->getMessage());
        }
    }
    
    /**
     * Atualizar sessão
     */
    public function atualizar(int $id, array $dados): array
    {
        try {
            $sessaoAtual = $this->buscarPorId($id);
            $sessao = $sessaoAtual['data'];
            
            $sessaoAtualizada = array_merge($sessao, $dados);
            $sessaoAtualizada['updated_at'] = now()->toISOString();
            
            return [
                'success' => true,
                'data' => $sessaoAtualizada,
                'message' => 'Sessão atualizada com sucesso'
            ];
            
        } catch (Exception $e) {
            Log::error('Erro ao atualizar sessão', [
                'erro' => $e->getMessage(),
                'id' => $id,
                'dados' => $dados
            ]);
            throw new Exception('Erro ao atualizar sessão: ' . $e->getMessage());
        }
    }
    
    /**
     * Deletar sessão
     */
    public function deletar(int $id): array
    {
        try {
            $sessao = $this->buscarPorId($id);
            
            return [
                'success' => true,
                'message' => 'Sessão deletada com sucesso'
            ];
            
        } catch (Exception $e) {
            Log::error('Erro ao deletar sessão', [
                'erro' => $e->getMessage(),
                'id' => $id
            ]);
            throw new Exception('Erro ao deletar sessão: ' . $e->getMessage());
        }
    }
    
    /**
     * Buscar matérias da sessão
     */
    public function buscarMaterias(int $sessionId): array
    {
        try {
            $sessao = $this->buscarPorId($sessionId);
            $materias = $sessao['data']['materias'] ?? [];
            
            return [
                'success' => true,
                'data' => $materias,
                'total' => count($materias),
                'message' => 'Matérias da sessão listadas com sucesso'
            ];
            
        } catch (Exception $e) {
            Log::error('Erro ao buscar matérias da sessão', [
                'erro' => $e->getMessage(),
                'sessionId' => $sessionId
            ]);
            throw new Exception('Erro ao buscar matérias da sessão: ' . $e->getMessage());
        }
    }
    
    /**
     * Adicionar matéria à sessão
     */
    public function adicionarMateria(int $sessionId, array $materia): array
    {
        try {
            $sessao = $this->buscarPorId($sessionId);
            $materias = $sessao['data']['materias'] ?? [];
            
            $novaMateria = [
                'id' => rand(10000, 99999),
                'titulo' => $materia['titulo'] ?? 'Matéria sem título',
                'tipo' => $materia['tipo'] ?? 'projeto_lei',
                'numero' => $materia['numero'] ?? rand(1, 999),
                'ano' => $materia['ano'] ?? date('Y'),
                'autor' => $materia['autor'] ?? 'Autor não informado',
                'relator' => $materia['relator'] ?? null,
                'situacao' => $materia['situacao'] ?? 'tramitando',
                'ordem' => count($materias) + 1,
                'created_at' => now()->toISOString(),
            ];
            
            $materias[] = $novaMateria;
            
            return [
                'success' => true,
                'data' => $novaMateria,
                'message' => 'Matéria adicionada à sessão com sucesso'
            ];
            
        } catch (Exception $e) {
            Log::error('Erro ao adicionar matéria à sessão', [
                'erro' => $e->getMessage(),
                'sessionId' => $sessionId,
                'materia' => $materia
            ]);
            throw new Exception('Erro ao adicionar matéria à sessão: ' . $e->getMessage());
        }
    }
    
    /**
     * Gerar XML da sessão
     */
    public function gerarXml(int $sessionId): array
    {
        try {
            $sessao = $this->buscarPorId($sessionId);
            $dadosSessao = $sessao['data'];
            
            $xml = $this->construirXmlSessao($dadosSessao);
            
            return [
                'success' => true,
                'data' => [
                    'xml' => $xml,
                    'filename' => "sessao_{$sessionId}.xml",
                    'size' => strlen($xml)
                ],
                'message' => 'XML da sessão gerado com sucesso'
            ];
            
        } catch (Exception $e) {
            Log::error('Erro ao gerar XML da sessão', [
                'erro' => $e->getMessage(),
                'sessionId' => $sessionId
            ]);
            throw new Exception('Erro ao gerar XML da sessão: ' . $e->getMessage());
        }
    }
    
    /**
     * Obter tipos de sessão disponíveis
     */
    public function obterTiposSessao(): array
    {
        return [
            'ordinaria' => 'Ordinária',
            'extraordinaria' => 'Extraordinária',
            'solene' => 'Solene',
            'especial' => 'Especial'
        ];
    }
    
    /**
     * Obter estatísticas das sessões
     */
    public function obterEstatisticas(): array
    {
        try {
            $sessoes = $this->getSessoesSimuladas();
            
            $estatisticas = [
                'total' => count($sessoes),
                'realizadas' => count(array_filter($sessoes, fn($s) => $s['status'] === 'realizada')),
                'agendadas' => count(array_filter($sessoes, fn($s) => $s['status'] === 'agendada')),
                'canceladas' => count(array_filter($sessoes, fn($s) => $s['status'] === 'cancelada')),
                'ordinarias' => count(array_filter($sessoes, fn($s) => $s['tipo'] === 'ordinaria')),
                'extraordinarias' => count(array_filter($sessoes, fn($s) => $s['tipo'] === 'extraordinaria')),
                'solenes' => count(array_filter($sessoes, fn($s) => $s['tipo'] === 'solene')),
            ];
            
            return [
                'success' => true,
                'data' => $estatisticas,
                'message' => 'Estatísticas obtidas com sucesso'
            ];
            
        } catch (Exception $e) {
            Log::error('Erro ao obter estatísticas das sessões', [
                'erro' => $e->getMessage()
            ]);
            throw new Exception('Erro ao obter estatísticas: ' . $e->getMessage());
        }
    }
    
    /**
     * Obter sessões simuladas para demonstração
     */
    private function getSessoesSimuladas(): array
    {
        return [
            [
                'id' => 1,
                'numero' => 1,
                'tipo' => 'ordinaria',
                'data' => now()->subDays(7)->toDateString(),
                'hora_inicio' => '14:00',
                'hora_fim' => '18:30',
                'status' => 'realizada',
                'legislatura' => '2025-2028',
                'sessao_legislativa' => '2025',
                'presidente' => 'Dr. João Silva',
                'secretario' => 'Maria Santos',
                'local' => 'Plenário Principal',
                'observacoes' => 'Sessão realizada com quórum regimental',
                'ordem_dia' => ['Leitura da ata', 'Expediente', 'Ordem do dia'],
                'materias' => [
                    [
                        'id' => 101,
                        'titulo' => 'Projeto de Lei nº 001/2025',
                        'tipo' => 'projeto_lei',
                        'numero' => 1,
                        'ano' => 2025,
                        'autor' => 'Vereador Carlos Lima',
                        'relator' => 'Vereador Ana Costa',
                        'situacao' => 'aprovado',
                        'ordem' => 1
                    ]
                ],
                'presencas' => ['João Silva', 'Maria Santos', 'Carlos Lima', 'Ana Costa'],
                'votacoes' => [
                    [
                        'materia_id' => 101,
                        'resultado' => 'aprovado',
                        'votos_favoraveis' => 15,
                        'votos_contrarios' => 3,
                        'abstencoes' => 2
                    ]
                ],
                'ata' => 'Ata da 1ª Sessão Ordinária disponível',
                'created_at' => now()->subDays(10)->toISOString(),
                'updated_at' => now()->subDays(7)->toISOString(),
            ],
            [
                'id' => 2,
                'numero' => 2,
                'tipo' => 'ordinaria',
                'data' => now()->subDays(3)->toDateString(),
                'hora_inicio' => '14:00',
                'hora_fim' => null,
                'status' => 'agendada',
                'legislatura' => '2025-2028',
                'sessao_legislativa' => '2025',
                'presidente' => 'Dr. João Silva',
                'secretario' => 'Maria Santos',
                'local' => 'Plenário Principal',
                'observacoes' => '',
                'ordem_dia' => ['Leitura da ata', 'Expediente', 'Ordem do dia'],
                'materias' => [
                    [
                        'id' => 102,
                        'titulo' => 'Projeto de Lei nº 002/2025',
                        'tipo' => 'projeto_lei',
                        'numero' => 2,
                        'ano' => 2025,
                        'autor' => 'Vereadora Ana Costa',
                        'relator' => 'Vereador Pedro Oliveira',
                        'situacao' => 'tramitando',
                        'ordem' => 1
                    ]
                ],
                'presencas' => [],
                'votacoes' => [],
                'ata' => null,
                'created_at' => now()->subDays(5)->toISOString(),
                'updated_at' => now()->subDays(3)->toISOString(),
            ],
            [
                'id' => 3,
                'numero' => 1,
                'tipo' => 'extraordinaria',
                'data' => now()->addDays(2)->toDateString(),
                'hora_inicio' => '09:00',
                'hora_fim' => null,
                'status' => 'agendada',
                'legislatura' => '2025-2028',
                'sessao_legislativa' => '2025',
                'presidente' => 'Dr. João Silva',
                'secretario' => 'Maria Santos',
                'local' => 'Plenário Principal',
                'observacoes' => 'Sessão convocada para votação urgente',
                'ordem_dia' => ['Matéria urgente'],
                'materias' => [
                    [
                        'id' => 103,
                        'titulo' => 'Projeto de Lei nº 003/2025 - Urgente',
                        'tipo' => 'projeto_lei',
                        'numero' => 3,
                        'ano' => 2025,
                        'autor' => 'Mesa Diretora',
                        'relator' => 'Vereador Carlos Lima',
                        'situacao' => 'tramitando',
                        'ordem' => 1
                    ]
                ],
                'presencas' => [],
                'votacoes' => [],
                'ata' => null,
                'created_at' => now()->subDays(1)->toISOString(),
                'updated_at' => now()->subDays(1)->toISOString(),
            ]
        ];
    }
    
    /**
     * Construir XML da sessão
     */
    private function construirXmlSessao(array $sessao): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sessao>' . "\n";
        $xml .= '  <identificacao>' . "\n";
        $xml .= '    <id>' . $sessao['id'] . '</id>' . "\n";
        $xml .= '    <numero>' . $sessao['numero'] . '</numero>' . "\n";
        $xml .= '    <tipo>' . $sessao['tipo'] . '</tipo>' . "\n";
        $xml .= '    <data>' . $sessao['data'] . '</data>' . "\n";
        $xml .= '    <hora_inicio>' . $sessao['hora_inicio'] . '</hora_inicio>' . "\n";
        if ($sessao['hora_fim']) {
            $xml .= '    <hora_fim>' . $sessao['hora_fim'] . '</hora_fim>' . "\n";
        }
        $xml .= '    <status>' . $sessao['status'] . '</status>' . "\n";
        $xml .= '    <legislatura>' . $sessao['legislatura'] . '</legislatura>' . "\n";
        $xml .= '    <sessao_legislativa>' . $sessao['sessao_legislativa'] . '</sessao_legislativa>' . "\n";
        $xml .= '    <local>' . htmlspecialchars($sessao['local']) . '</local>' . "\n";
        $xml .= '  </identificacao>' . "\n";
        
        $xml .= '  <mesa_diretora>' . "\n";
        $xml .= '    <presidente>' . htmlspecialchars($sessao['presidente']) . '</presidente>' . "\n";
        $xml .= '    <secretario>' . htmlspecialchars($sessao['secretario']) . '</secretario>' . "\n";
        $xml .= '  </mesa_diretora>' . "\n";
        
        if (!empty($sessao['materias'])) {
            $xml .= '  <materias>' . "\n";
            foreach ($sessao['materias'] as $materia) {
                $xml .= '    <materia>' . "\n";
                $xml .= '      <id>' . $materia['id'] . '</id>' . "\n";
                $xml .= '      <titulo>' . htmlspecialchars($materia['titulo']) . '</titulo>' . "\n";
                $xml .= '      <tipo>' . $materia['tipo'] . '</tipo>' . "\n";
                $xml .= '      <numero>' . $materia['numero'] . '</numero>' . "\n";
                $xml .= '      <ano>' . $materia['ano'] . '</ano>' . "\n";
                $xml .= '      <autor>' . htmlspecialchars($materia['autor']) . '</autor>' . "\n";
                if ($materia['relator']) {
                    $xml .= '      <relator>' . htmlspecialchars($materia['relator']) . '</relator>' . "\n";
                }
                $xml .= '      <situacao>' . $materia['situacao'] . '</situacao>' . "\n";
                $xml .= '      <ordem>' . $materia['ordem'] . '</ordem>' . "\n";
                $xml .= '    </materia>' . "\n";
            }
            $xml .= '  </materias>' . "\n";
        }
        
        if (!empty($sessao['observacoes'])) {
            $xml .= '  <observacoes>' . htmlspecialchars($sessao['observacoes']) . '</observacoes>' . "\n";
        }
        
        $xml .= '</sessao>';
        
        return $xml;
    }
}