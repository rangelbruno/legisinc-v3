<?php

namespace App\Services\Parlamentar;

use App\Models\Parlamentar;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ParlamentarService
{
    /**
     * Obter todos os parlamentares com filtros opcionais
     */
    public function getAll(array $filters = []): Collection
    {
        $query = Parlamentar::query();
        
        // Aplicar filtros
        if (!empty($filters['partido'])) {
            $query->where('partido', $filters['partido']);
        }
        
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (!empty($filters['cargo'])) {
            $query->where('cargo', $filters['cargo']);
        }
        
        return $query->orderBy('nome')->get();
    }
    
    /**
     * Obter parlamentar por ID
     */
    public function getById(int $id): array
    {
        $parlamentar = Parlamentar::find($id);
        
        if (!$parlamentar) {
            throw new ModelNotFoundException("Parlamentar com ID {$id} não encontrado");
        }
        
        return $this->formatForDisplay($parlamentar->toArray());
    }
    
    /**
     * Criar novo parlamentar
     */
    public function create(array $data): array
    {
        // Validar dados únicos
        $this->validateUniqueData($data);
        
        $parlamentar = Parlamentar::create($data);
        
        return $this->formatForDisplay($parlamentar->toArray());
    }
    
    /**
     * Atualizar parlamentar
     */
    public function update(int $id, array $data): array
    {
        $parlamentar = Parlamentar::find($id);
        
        if (!$parlamentar) {
            throw new ModelNotFoundException("Parlamentar com ID {$id} não encontrado");
        }
        
        // Validar dados únicos (excluindo o próprio registro)
        $this->validateUniqueData($data, $id);
        
        $parlamentar->update($data);
        
        return $this->formatForDisplay($parlamentar->fresh()->toArray());
    }
    
    /**
     * Deletar parlamentar
     */
    public function delete(int $id): bool
    {
        $parlamentar = Parlamentar::find($id);
        
        if (!$parlamentar) {
            throw new ModelNotFoundException("Parlamentar com ID {$id} não encontrado");
        }
        
        return $parlamentar->delete();
    }
    
    /**
     * Obter parlamentares por partido
     */
    public function getByPartido(string $partido): Collection
    {
        return Parlamentar::where('partido', $partido)
            ->orderBy('nome')
            ->get();
    }
    
    /**
     * Obter parlamentares por status
     */
    public function getByStatus(string $status): Collection
    {
        return Parlamentar::where('status', $status)
            ->orderBy('nome')
            ->get();
    }
    
    /**
     * Obter mesa diretora
     */
    public function getMesaDiretora(): Collection
    {
        $cargosMesa = ['Presidente da Câmara', 'Vice-Presidente', '1º Secretário', '2º Secretário'];
        
        $parlamentares = Parlamentar::whereIn('cargo', $cargosMesa)
            ->where('status', 'ativo')
            ->get();
            
        // Ordenar manualmente para compatibilidade com SQLite
        return $parlamentares->sortBy(function ($parlamentar) use ($cargosMesa) {
            return array_search($parlamentar->cargo, $cargosMesa);
        })->values();
    }
    
    /**
     * Obter comissões de um parlamentar
     */
    public function getComissoes(int $parlamentarId): array
    {
        $parlamentar = Parlamentar::find($parlamentarId);
        
        if (!$parlamentar) {
            throw new ModelNotFoundException("Parlamentar com ID {$parlamentarId} não encontrado");
        }
        
        return [
            'comissoes' => $parlamentar->comissoes ?? [],
            'meta' => [
                'total' => count($parlamentar->comissoes ?? []),
                'parlamentar_id' => $parlamentarId,
                'parlamentar_nome' => $parlamentar->nome
            ]
        ];
    }
    
    /**
     * Obter estatísticas dos parlamentares
     */
    public function getEstatisticas(): array
    {
        $todos = Parlamentar::all();
        $ativos = Parlamentar::where('status', 'ativo')->get();
        
        $partidosCount = $todos->groupBy('partido')->map->count();
        $statusCount = $todos->groupBy('status')->map->count();
        
        return [
            'total' => $todos->count(),
            'ativos' => $ativos->count(),
            'inativos' => $todos->where('status', '!=', 'ativo')->count(),
            'por_partido' => $partidosCount->toArray(),
            'por_status' => $statusCount->toArray(),
        ];
    }
    
    /**
     * Buscar parlamentares por termo
     */
    public function search(string $termo): Collection
    {
        return Parlamentar::buscar($termo)->orderBy('nome')->get();
    }
    
    /**
     * Validar dados únicos
     */
    private function validateUniqueData(array $data, ?int $excludeId = null): void
    {
        $errors = [];
        
        // Verificar email único
        if (!empty($data['email'])) {
            $query = Parlamentar::where('email', $data['email']);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            if ($query->exists()) {
                $errors['email'] = 'Este email já está sendo usado por outro parlamentar';
            }
        }
        
        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
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

    /**
     * Buscar parlamentares com paginação
     */
    public function getAllPaginated(int $page = 1, int $perPage = 15, array $filters = []): array
    {
        $query = Parlamentar::query();
        
        // Aplicar filtros
        if (!empty($filters['partido'])) {
            $query->where('partido', $filters['partido']);
        }
        
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        $paginated = $query->orderBy('nome')->paginate($perPage, ['*'], 'page', $page);
        
        return [
            'data' => $paginated->items(),
            'current_page' => $paginated->currentPage(),
            'per_page' => $paginated->perPage(),
            'total' => $paginated->total(),
            'last_page' => $paginated->lastPage(),
            'from' => $paginated->firstItem(),
            'to' => $paginated->lastItem(),
        ];
    }

    /**
     * Obter parlamentares ordenados por critério específico
     */
    public function getOrderedBy(string $field, string $direction = 'asc'): Collection
    {
        return Parlamentar::orderBy($field, $direction)->get();
    }

    /**
     * Verificar conflitos de agenda entre parlamentares
     */
    public function checkConflitos(int $parlamentarId, string $data, string $horario): array
    {
        $parlamentar = Parlamentar::find($parlamentarId);
        
        if (!$parlamentar) {
            throw new ModelNotFoundException("Parlamentar com ID {$parlamentarId} não encontrado");
        }
        
        // Simular verificação de conflitos
        return [
            'tem_conflito' => false,
            'conflitos' => [],
            'parlamentar' => $parlamentar->nome,
            'comissoes_ativas' => count($parlamentar->comissoes ?? []),
        ];
    }

    /**
     * Gerar relatório de presença
     */
    public function getRelatorioPresenca(array $filters = []): array
    {
        $query = Parlamentar::query();
        
        if (!empty($filters['partido'])) {
            $query->where('partido', $filters['partido']);
        }
        
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        $parlamentares = $query->get();
        
        // Simular dados de presença
        $relatorio = $parlamentares->map(function ($parlamentar) {
            $presencas = rand(8, 15);
            $total = 15;
            
            return [
                'id' => $parlamentar->id,
                'nome' => $parlamentar->nome,
                'partido' => $parlamentar->partido,
                'presencas' => $presencas,
                'total_sessoes' => $total,
                'percentual' => round(($presencas / $total) * 100, 1),
                'justificativas' => rand(0, 3),
            ];
        });
        
        return [
            'parlamentares' => $relatorio->toArray(),
            'estatisticas' => [
                'presenca_media' => round($relatorio->avg('percentual'), 1),
                'maior_presenca' => $relatorio->max('percentual'),
                'menor_presenca' => $relatorio->min('percentual'),
                'total_parlamentares' => $relatorio->count(),
            ],
        ];
    }

    /**
     * Obter aniversariantes do mês
     */
    public function getAniversariantesDoMes(int $mes = null): Collection
    {
        $mes = $mes ?? now()->month;
        
        return Parlamentar::whereMonth('data_nascimento', $mes)
            ->orderByRaw('DAY(data_nascimento)')
            ->get()
            ->map(function ($parlamentar) {
                return [
                    'id' => $parlamentar->id,
                    'nome' => $parlamentar->nome,
                    'partido' => $parlamentar->partido,
                    'dia_aniversario' => $parlamentar->data_nascimento->day,
                    'idade' => $parlamentar->idade,
                    'data_formatada' => $parlamentar->data_nascimento->format('d/m'),
                ];
            });
    }

    /**
     * Validar se email já existe
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $query = Parlamentar::where('email', $email);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * Obter contatos de emergência
     */
    public function getContatosEmergencia(): array
    {
        $parlamentares = Parlamentar::where('status', 'ativo')->get();
        
        $contatos = $parlamentares->map(function ($parlamentar) {
            return [
                'nome' => $parlamentar->nome,
                'cargo' => $parlamentar->cargo,
                'telefone' => $parlamentar->telefone,
                'email' => $parlamentar->email,
                'partido' => $parlamentar->partido,
            ];
        });
        
        return [
            'contatos' => $contatos->toArray(),
            'total' => $contatos->count(),
            'ultima_atualizacao' => now()->format('d/m/Y H:i'),
        ];
    }
}