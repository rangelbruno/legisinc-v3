<?php

namespace App\Services\MesaDiretora;

use App\Models\MesaDiretora;
use App\Models\Parlamentar;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class MesaDiretoraService
{
    /**
     * Obter todos os membros da mesa diretora com filtros opcionais
     */
    public function getAll(array $filters = []): Collection
    {
        $query = MesaDiretora::with('parlamentar');
        
        // Aplicar filtros
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (!empty($filters['cargo'])) {
            $query->where('cargo_mesa', $filters['cargo']);
        }
        
        if (!empty($filters['mandato'])) {
            if ($filters['mandato'] === 'atual') {
                $query->mandatoAtual();
            }
        }
        
        return $query->orderBy('mandato_inicio', 'desc')->get();
    }
    
    /**
     * Obter composição atual da mesa diretora
     */
    public function getComposicaoAtual(): Collection
    {
        return MesaDiretora::with('parlamentar')
            ->ativos()
            ->mandatoAtual()
            ->orderByRaw("
                CASE cargo_mesa 
                    WHEN 'Presidente da Câmara' THEN 1
                    WHEN 'Vice-Presidente' THEN 2
                    WHEN '1º Secretário' THEN 3
                    WHEN '2º Secretário' THEN 4
                    ELSE 5
                END
            ")
            ->get();
    }
    
    /**
     * Obter membro por ID
     */
    public function getById(int $id): array
    {
        $membro = MesaDiretora::with('parlamentar')->find($id);
        
        if (!$membro) {
            throw new ModelNotFoundException("Membro da mesa diretora com ID {$id} não encontrado");
        }
        
        return $this->formatForDisplay($membro->toArray());
    }
    
    /**
     * Criar novo membro da mesa diretora
     */
    public function create(array $data): array
    {
        $this->validateMemberData($data);
        
        $membro = MesaDiretora::create($data);
        
        return $this->formatForDisplay($membro->load('parlamentar')->toArray());
    }
    
    /**
     * Atualizar membro da mesa diretora
     */
    public function update(int $id, array $data): array
    {
        $membro = MesaDiretora::find($id);
        
        if (!$membro) {
            throw new ModelNotFoundException("Membro da mesa diretora com ID {$id} não encontrado");
        }
        
        $this->validateMemberData($data, $id);
        
        $membro->update($data);
        
        return $this->formatForDisplay($membro->fresh('parlamentar')->toArray());
    }
    
    /**
     * Deletar membro da mesa diretora
     */
    public function delete(int $id): bool
    {
        $membro = MesaDiretora::find($id);
        
        if (!$membro) {
            throw new ModelNotFoundException("Membro da mesa diretora com ID {$id} não encontrado");
        }
        
        return $membro->delete();
    }
    
    /**
     * Finalizar mandato de um membro
     */
    public function finalizarMandato(int $id): bool
    {
        $membro = MesaDiretora::find($id);
        
        if (!$membro) {
            throw new ModelNotFoundException("Membro da mesa diretora com ID {$id} não encontrado");
        }
        
        $membro->update([
            'status' => 'finalizado',
            'mandato_fim' => now()->format('Y-m-d')
        ]);
        
        return true;
    }
    
    /**
     * Obter histórico de mandatos
     */
    public function getHistoricoMandatos(): Collection
    {
        return MesaDiretora::with('parlamentar')
            ->orderBy('mandato_inicio', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->mandato_inicio->format('Y');
            });
    }
    
    /**
     * Obter estatísticas da mesa diretora
     */
    public function getEstatisticas(): array
    {
        $todos = MesaDiretora::with('parlamentar')->get();
        $ativos = MesaDiretora::ativos()->mandatoAtual()->get();
        
        $cargoCount = $todos->groupBy('cargo_mesa')->map->count();
        $statusCount = $todos->groupBy('status')->map->count();
        
        return [
            'total_membros' => $todos->count(),
            'membros_ativos' => $ativos->count(),
            'mandatos_finalizados' => $todos->where('status', 'finalizado')->count(),
            'por_cargo' => $cargoCount->toArray(),
            'por_status' => $statusCount->toArray(),
            'mandato_atual_inicio' => $ativos->min('mandato_inicio')?->format('d/m/Y'),
            'mandato_atual_fim' => $ativos->max('mandato_fim')?->format('d/m/Y'),
        ];
    }
    
    /**
     * Buscar membros por termo
     */
    public function search(string $termo): Collection
    {
        return MesaDiretora::with('parlamentar')
            ->buscar($termo)
            ->orderBy('mandato_inicio', 'desc')
            ->get();
    }
    
    /**
     * Validar se já existe um membro no cargo para o período
     */
    public function validarCargoDisponivel(string $cargo, string $inicioMandato, string $fimMandato, ?int $excludeId = null): bool
    {
        $query = MesaDiretora::where('cargo_mesa', $cargo)
            ->where('status', 'ativo')
            ->where(function ($q) use ($inicioMandato, $fimMandato) {
                $q->whereBetween('mandato_inicio', [$inicioMandato, $fimMandato])
                  ->orWhereBetween('mandato_fim', [$inicioMandato, $fimMandato])
                  ->orWhere(function ($subQuery) use ($inicioMandato, $fimMandato) {
                      $subQuery->where('mandato_inicio', '<=', $inicioMandato)
                               ->where('mandato_fim', '>=', $fimMandato);
                  });
            });
            
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return !$query->exists();
    }
    
    /**
     * Obter cargos disponíveis
     */
    public function getCargosDisponiveis(): array
    {
        return [
            'Presidente da Câmara',
            'Vice-Presidente',
            '1º Secretário',
            '2º Secretário',
            '1º Tesoureiro',
            '2º Tesoureiro',
        ];
    }
    
    /**
     * Obter parlamentares elegíveis para mesa diretora
     */
    public function getParlamentaresElegiveis(): Collection
    {
        return Parlamentar::where('status', 'ativo')
            ->orderBy('nome')
            ->get();
    }
    
    /**
     * Validar dados do membro
     */
    private function validateMemberData(array $data, ?int $excludeId = null): void
    {
        $errors = [];
        
        // Verificar se parlamentar existe e está ativo
        if (!empty($data['parlamentar_id'])) {
            $parlamentar = Parlamentar::find($data['parlamentar_id']);
            if (!$parlamentar) {
                $errors['parlamentar_id'] = 'Parlamentar não encontrado';
            } elseif ($parlamentar->status !== 'ativo') {
                $errors['parlamentar_id'] = 'Parlamentar deve estar ativo para fazer parte da mesa diretora';
            }
        }
        
        // Verificar se cargo é válido
        if (!empty($data['cargo_mesa'])) {
            $cargosValidos = $this->getCargosDisponiveis();
            if (!in_array($data['cargo_mesa'], $cargosValidos)) {
                $errors['cargo_mesa'] = 'Cargo inválido para mesa diretora';
            }
        }
        
        // Verificar se as datas são válidas
        if (!empty($data['mandato_inicio']) && !empty($data['mandato_fim'])) {
            $inicio = Carbon::parse($data['mandato_inicio']);
            $fim = Carbon::parse($data['mandato_fim']);
            
            if ($fim <= $inicio) {
                $errors['mandato_fim'] = 'Data de fim do mandato deve ser posterior à data de início';
            }
            
            // Verificar se cargo está disponível para o período
            if (!empty($data['cargo_mesa'])) {
                if (!$this->validarCargoDisponivel(
                    $data['cargo_mesa'], 
                    $data['mandato_inicio'], 
                    $data['mandato_fim'], 
                    $excludeId
                )) {
                    $errors['cargo_mesa'] = 'Já existe um membro ativo neste cargo para o período informado';
                }
            }
        }
        
        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }
    
    /**
     * Formatar data para exibição
     */
    private function formatDate($date): string
    {
        if (!$date) {
            return '';
        }

        try {
            if ($date instanceof Carbon) {
                return $date->format('d/m/Y');
            }
            
            if (is_string($date)) {
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
                    return $date;
                }
                
                if (strpos($date, ' ') !== false) {
                    $date = explode(' ', $date)[0];
                }
                
                return Carbon::parse($date)->format('d/m/Y');
            }
            
            return '';
        } catch (\Exception $e) {
            // Log::error('Erro ao formatar data: ' . $e->getMessage(), ['date' => $date]);
            return '';
        }
    }

    /**
     * Formatar datetime para exibição
     */
    private function formatDatetime($datetime): string
    {
        if (!$datetime) {
            return '';
        }

        try {
            if (is_string($datetime) && preg_match('/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}$/', $datetime)) {
                return $datetime;
            }

            if ($datetime instanceof Carbon) {
                return $datetime->format('d/m/Y H:i');
            }
            
            if (is_string($datetime)) {
                return Carbon::parse($datetime)->format('d/m/Y H:i');
            }
            
            return '';
        } catch (\Exception $e) {
            // Log::error('Erro ao formatar datetime: ' . $e->getMessage(), ['datetime' => $datetime]);
            return '';
        }
    }
    
    /**
     * Formatar dados do membro para exibição
     */
    public function formatForDisplay(array $membro): array
    {
        return [
            'id' => $membro['id'],
            'parlamentar_id' => $membro['parlamentar_id'],
            'parlamentar_nome' => $membro['parlamentar']['nome'] ?? '',
            'parlamentar_partido' => $membro['parlamentar']['partido'] ?? '',
            'cargo_mesa' => $membro['cargo_mesa'],
            'cargo_formatado' => ucwords(strtolower($membro['cargo_mesa'])),
            'mandato_inicio' => $this->formatDate($membro['mandato_inicio']),
            'mandato_fim' => $this->formatDate($membro['mandato_fim']),
            'mandato_formatado' => $this->formatDate($membro['mandato_inicio']) . ' - ' . $this->formatDate($membro['mandato_fim']),
            'status' => $membro['status'],
            'status_formatado' => ucfirst($membro['status']),
            'observacoes' => $membro['observacoes'] ?? '',
            'is_mandato_ativo' => $this->isMandatoAtivo($membro['mandato_inicio'], $membro['mandato_fim'], $membro['status']),
            'created_at' => $this->formatDatetime($membro['created_at']),
            'updated_at' => $this->formatDatetime($membro['updated_at']),
        ];
    }
    
    /**
     * Verificar se mandato está ativo
     */
    private function isMandatoAtivo($inicio, $fim, $status): bool
    {
        if ($status !== 'ativo') {
            return false;
        }
        
        $now = now();
        $inicioCarbon = Carbon::parse($inicio);
        $fimCarbon = Carbon::parse($fim);
        
        return $now->between($inicioCarbon, $fimCarbon);
    }

    /**
     * Obter membros com paginação
     */
    public function getAllPaginated(int $page = 1, int $perPage = 15, array $filters = []): array
    {
        $query = MesaDiretora::with('parlamentar');
        
        // Aplicar filtros
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (!empty($filters['cargo'])) {
            $query->where('cargo_mesa', $filters['cargo']);
        }
        
        $paginated = $query->orderBy('mandato_inicio', 'desc')
                          ->paginate($perPage, ['*'], 'page', $page);
        
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
}