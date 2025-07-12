<?php

namespace App\Services\Comissao;

use App\Models\Parlamentar;
use Illuminate\Support\Collection;

class ComissaoService
{
    /**
     * Buscar todas as comissões baseadas nos dados dos parlamentares
     */
    public function getAll(array $filters = []): Collection
    {
        $comissoes = $this->getComissoesFromParlamentares();
        
        if (!empty($filters['tipo'])) {
            $comissoes = $comissoes->filter(function ($comissao) use ($filters) {
                return $comissao['tipo'] === $filters['tipo'];
            });
        }
        
        if (!empty($filters['status'])) {
            $comissoes = $comissoes->filter(function ($comissao) use ($filters) {
                return $comissao['status'] === $filters['status'];
            });
        }
        
        return $comissoes;
    }
    
    /**
     * Buscar comissão por ID
     */
    public function getById(int $id): array
    {
        $comissoes = $this->getComissoesFromParlamentares();
        $comissao = $comissoes->firstWhere('id', $id);
        
        if (!$comissao) {
            throw new \Exception('Comissão não encontrada');
        }
        
        return $comissao;
    }
    
    /**
     * Criar nova comissão (simulada)
     */
    public function create(array $data): array
    {
        // Por enquanto, apenas validar e retornar os dados
        $errors = $this->validateData($data);
        if (!empty($errors)) {
            throw new \Exception('Dados inválidos: ' . implode(', ', $errors));
        }
        
        $comissao = [
            'id' => rand(1000, 9999),
            'nome' => $data['nome'],
            'tipo' => $data['tipo'] ?? 'permanente',
            'status' => 'ativa',
            'finalidade' => $data['finalidade'] ?? '',
            'presidente' => $data['presidente'] ?? null,
            'vice_presidente' => $data['vice_presidente'] ?? null,
            'relator' => $data['relator'] ?? null,
            'total_membros' => 0,
            'membros' => [],
            'data_criacao' => now()->toISOString(),
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
        ];
        
        return $this->formatForDisplay($comissao);
    }
    
    /**
     * Atualizar comissão (simulada)
     */
    public function update(int $id, array $data): array
    {
        $comissao = $this->getById($id);
        
        $errors = $this->validateData($data);
        if (!empty($errors)) {
            throw new \Exception('Dados inválidos: ' . implode(', ', $errors));
        }
        
        $comissao = array_merge($comissao, $data);
        $comissao['updated_at'] = now()->toISOString();
        
        return $this->formatForDisplay($comissao);
    }
    
    /**
     * Deletar comissão (simulada)
     */
    public function delete(int $id): bool
    {
        $comissao = $this->getById($id);
        return true; // Simulado - sempre retorna sucesso
    }
    
    /**
     * Buscar comissões por tipo
     */
    public function getByTipo(string $tipo): Collection
    {
        return $this->getAll(['tipo' => $tipo]);
    }
    
    /**
     * Buscar comissões por status
     */
    public function getByStatus(string $status): Collection
    {
        return $this->getAll(['status' => $status]);
    }
    
    /**
     * Buscar membros de uma comissão
     */
    public function getMembros(int $id): array
    {
        $comissao = $this->getById($id);
        
        // Buscar parlamentares que fazem parte desta comissão
        $membros = Parlamentar::whereJsonContains('comissoes', ['id' => $id])
            ->orWhereJsonContains('comissoes', ['nome' => $comissao['nome']])
            ->get()
            ->map(function ($parlamentar) {
                return [
                    'id' => $parlamentar->id,
                    'nome' => $parlamentar->nome,
                    'partido' => $parlamentar->partido,
                    'cargo' => $parlamentar->cargo,
                    'status' => $parlamentar->status,
                ];
            })
            ->toArray();
        
        return [
            'comissao_id' => $id,
            'comissao_nome' => $comissao['nome'],
            'total_membros' => count($membros),
            'membros' => $membros
        ];
    }
    
    /**
     * Buscar reuniões de uma comissão (simuladas)
     */
    public function getReunioes(int $id): array
    {
        $comissao = $this->getById($id);
        
        // Gerar algumas reuniões simuladas
        $reunioes = [];
        for ($i = 1; $i <= 3; $i++) {
            $reunioes[] = [
                'id' => $id * 100 + $i,
                'comissao_id' => $id,
                'titulo' => "Reunião {$i} - {$comissao['nome']}",
                'data' => now()->subDays(rand(1, 30))->toDateString(),
                'hora' => '14:00',
                'local' => 'Plenário da Comissão',
                'status' => rand(0, 1) ? 'realizada' : 'agendada',
                'ata' => rand(0, 1) ? 'Ata disponível' : null,
            ];
        }
        
        return [
            'comissao_id' => $id,
            'comissao_nome' => $comissao['nome'],
            'total_reunioes' => count($reunioes),
            'reunioes' => $reunioes
        ];
    }
    
    /**
     * Buscar comissões (pesquisa)
     */
    public function search(string $termo): Collection
    {
        $comissoes = $this->getComissoesFromParlamentares();
        
        return $comissoes->filter(function ($comissao) use ($termo) {
            $termo = strtolower($termo);
            return str_contains(strtolower($comissao['nome']), $termo) ||
                   str_contains(strtolower($comissao['tipo']), $termo) ||
                   str_contains(strtolower($comissao['finalidade']), $termo);
        });
    }
    
    /**
     * Obter estatísticas das comissões
     */
    public function getEstatisticas(): array
    {
        $comissoes = $this->getComissoesFromParlamentares();
        
        return [
            'total' => $comissoes->count(),
            'ativas' => $comissoes->where('status', 'ativa')->count(),
            'permanentes' => $comissoes->where('tipo', 'permanente')->count(),
            'temporarias' => $comissoes->where('tipo', 'temporaria')->count(),
            'especiais' => $comissoes->where('tipo', 'especial')->count(),
            'cpis' => $comissoes->where('tipo', 'cpi')->count(),
        ];
    }
    
    /**
     * Extrair comissões dos dados dos parlamentares
     */
    private function getComissoesFromParlamentares(): Collection
    {
        $parlamentares = Parlamentar::all();
        $comissoesMap = [];
        
        foreach ($parlamentares as $parlamentar) {
            $comissoes = $parlamentar->comissoes ?? [];
            
            foreach ($comissoes as $comissao) {
                $nome = $comissao['nome'] ?? $comissao;
                if (is_string($nome)) {
                    $id = crc32($nome) % 10000; // Gerar ID baseado no nome
                    if (!isset($comissoesMap[$id])) {
                        $comissoesMap[$id] = [
                            'id' => $id,
                            'nome' => $nome,
                            'tipo' => $this->detectarTipoComissao($nome),
                            'status' => 'ativa',
                            'finalidade' => $this->gerarFinalidade($nome),
                            'presidente' => null,
                            'vice_presidente' => null,
                            'relator' => null,
                            'total_membros' => 0,
                            'membros' => [],
                            'data_criacao' => '2024-01-01T00:00:00Z',
                            'created_at' => '2024-01-01T00:00:00Z',
                            'updated_at' => now()->toISOString(),
                        ];
                    }
                    $comissoesMap[$id]['total_membros']++;
                }
            }
        }
        
        return collect(array_values($comissoesMap))->map(function ($comissao) {
            return $this->formatForDisplay($comissao);
        });
    }
    
    /**
     * Detectar tipo da comissão baseado no nome
     */
    private function detectarTipoComissao(string $nome): string
    {
        $nome = strtolower($nome);
        
        if (str_contains($nome, 'cpi') || str_contains($nome, 'investigação')) {
            return 'cpi';
        }
        
        if (str_contains($nome, 'especial') || str_contains($nome, 'temporária')) {
            return 'especial';
        }
        
        if (str_contains($nome, 'mista')) {
            return 'mista';
        }
        
        return 'permanente';
    }
    
    /**
     * Gerar finalidade baseada no nome da comissão
     */
    private function gerarFinalidade(string $nome): string
    {
        $finalidades = [
            'Análise e deliberação sobre matérias em sua área de competência',
            'Acompanhamento e fiscalização de políticas públicas',
            'Estudo e proposição de medidas legislativas',
            'Controle e avaliação de programas governamentais',
        ];
        
        return $finalidades[abs(crc32($nome)) % count($finalidades)];
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