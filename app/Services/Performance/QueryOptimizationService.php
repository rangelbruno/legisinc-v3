<?php

namespace App\Services\Performance;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Proposicao;
use App\Models\User;
use App\Models\TipoProposicao;

class QueryOptimizationService
{
    /**
     * Query otimizada para listagem de proposições
     */
    public function getProposicoesOptimizada(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Proposicao::query()
            ->select([
                'id', 'tipo', 'ementa', 'status', 'created_at', 'updated_at',
                'autor_id', 'revisor_id', 'template_id', 'numero_protocolo',
                'data_protocolo', 'data_assinatura'
            ])
            ->with([
                'autor:id,name,cargo_atual',
                'revisor:id,name',
                'tipoProposicao:id,tipo,nome'
            ]);

        // Aplicar filtros de forma otimizada
        $this->aplicarFiltros($query, $filters);

        // Ordenação otimizada
        $query->latest('updated_at')->latest('id');

        return $query->paginate($perPage);
    }

    /**
     * Query para dashboard com agregações otimizadas
     */
    public function getDashboardData(int $userId): array
    {
        // Query única para estatísticas do usuário
        $statsUsuario = Proposicao::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status IN ("rascunho", "enviado_legislativo") THEN 1 ELSE 0 END) as pendentes,
            SUM(CASE WHEN status = "aprovado_assinatura" THEN 1 ELSE 0 END) as aguardando_assinatura,
            SUM(CASE WHEN status = "assinado" THEN 1 ELSE 0 END) as assinadas,
            SUM(CASE WHEN status = "protocolado" THEN 1 ELSE 0 END) as protocoladas
        ')
        ->where('autor_id', $userId)
        ->first();

        // Proposições recentes com relacionamentos necessários
        $proposicoesRecentes = Proposicao::where('autor_id', $userId)
            ->select(['id', 'tipo', 'ementa', 'status', 'created_at', 'template_id'])
            ->with(['tipoProposicao:id,tipo,nome'])
            ->latest()
            ->limit(5)
            ->get();

        // Status das proposições agrupados
        $statusCount = Proposicao::selectRaw('status, COUNT(*) as count')
            ->where('autor_id', $userId)
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'stats' => $statsUsuario,
            'proposicoes_recentes' => $proposicoesRecentes,
            'status_count' => $statusCount
        ];
    }

    /**
     * Query otimizada para proposições do Legislativo
     */
    public function getProposicoesLegislativo(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Proposicao::query()
            ->select([
                'id', 'tipo', 'ementa', 'status', 'created_at', 'updated_at',
                'autor_id', 'revisor_id', 'data_revisao', 'parecer_tecnico'
            ])
            ->with([
                'autor:id,name,cargo_atual',
                'revisor:id,name'
            ])
            ->whereIn('status', [
                'enviado_legislativo', 'em_analise', 'aprovado_assinatura',
                'retornado_legislativo'
            ]);

        $this->aplicarFiltros($query, $filters);

        return $query->latest('updated_at')->paginate($perPage);
    }

    /**
     * Query otimizada para protocolo
     */
    public function getProposicoesProtocolo(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Proposicao::query()
            ->select([
                'id', 'tipo', 'ementa', 'status', 'created_at', 'updated_at',
                'autor_id', 'numero_protocolo', 'data_protocolo'
            ])
            ->with(['autor:id,name,cargo_atual'])
            ->whereIn('status', [
                'enviado_protocolo', 'protocolado'
            ]);

        $this->aplicarFiltros($query, $filters);

        return $query->latest('data_protocolo')->latest('updated_at')->paginate($perPage);
    }

    /**
     * Busca otimizada de proposições
     */
    public function buscarProposicoes(string $termo, int $perPage = 15): LengthAwarePaginator
    {
        return Proposicao::query()
            ->select([
                'id', 'tipo', 'ementa', 'status', 'created_at',
                'autor_id', 'numero_protocolo'
            ])
            ->with(['autor:id,name'])
            ->where(function ($query) use ($termo) {
                $query->where('ementa', 'ILIKE', "%{$termo}%")
                      ->orWhere('tipo', 'ILIKE', "%{$termo}%")
                      ->orWhere('numero_protocolo', 'ILIKE', "%{$termo}%")
                      ->orWhere('conteudo', 'ILIKE', "%{$termo}%");
            })
            ->latest('updated_at')
            ->paginate($perPage);
    }

    /**
     * Aplicar filtros de forma otimizada
     */
    private function aplicarFiltros(Builder $query, array $filters): void
    {
        if (!empty($filters['status'])) {
            if (is_array($filters['status'])) {
                $query->whereIn('status', $filters['status']);
            } else {
                $query->where('status', $filters['status']);
            }
        }

        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (!empty($filters['autor_id'])) {
            $query->where('autor_id', $filters['autor_id']);
        }

        if (!empty($filters['data_inicio'])) {
            $query->whereDate('created_at', '>=', $filters['data_inicio']);
        }

        if (!empty($filters['data_fim'])) {
            $query->whereDate('created_at', '<=', $filters['data_fim']);
        }

        if (!empty($filters['busca'])) {
            $termo = $filters['busca'];
            $query->where(function ($q) use ($termo) {
                $q->where('ementa', 'ILIKE', "%{$termo}%")
                  ->orWhere('numero_protocolo', 'ILIKE', "%{$termo}%");
            });
        }
    }

    /**
     * Bulk operations para performance
     */
    public function atualizarStatusEmLote(array $proposicaoIds, string $status): int
    {
        return Proposicao::whereIn('id', $proposicaoIds)
            ->update([
                'status' => $status,
                'updated_at' => now()
            ]);
    }

    /**
     * Pré-carregar dados relacionados para evitar N+1
     */
    public function precarregarRelacionamentos(Collection $proposicoes): Collection
    {
        return $proposicoes->load([
            'autor:id,name,email,cargo_atual',
            'revisor:id,name,email',
            'tipoProposicao:id,tipo,nome,codigo',
            'template:id,nome,arquivo_path'
        ]);
    }

    /**
     * Query para relatórios com agregações otimizadas
     */
    public function getRelatorioPerformance(array $periodo = []): array
    {
        $query = Proposicao::query();

        if (!empty($periodo['inicio'])) {
            $query->whereDate('created_at', '>=', $periodo['inicio']);
        }

        if (!empty($periodo['fim'])) {
            $query->whereDate('created_at', '<=', $periodo['fim']);
        }

        // Agregações em uma única query
        $stats = $query->selectRaw('
            COUNT(*) as total_proposicoes,
            COUNT(CASE WHEN status = "protocolado" THEN 1 END) as protocoladas,
            COUNT(CASE WHEN status = "assinado" THEN 1 END) as assinadas,
            AVG(CASE 
                WHEN data_assinatura IS NOT NULL AND created_at IS NOT NULL 
                THEN EXTRACT(EPOCH FROM (data_assinatura - created_at))/86400 
            END) as tempo_medio_dias,
            COUNT(DISTINCT autor_id) as usuarios_ativos
        ')->first();

        // Proposições por tipo
        $porTipo = Proposicao::selectRaw('tipo, COUNT(*) as count')
            ->when(!empty($periodo['inicio']), fn($q) => $q->whereDate('created_at', '>=', $periodo['inicio']))
            ->when(!empty($periodo['fim']), fn($q) => $q->whereDate('created_at', '<=', $periodo['fim']))
            ->groupBy('tipo')
            ->orderByDesc('count')
            ->get();

        // Proposições por status
        $porStatus = Proposicao::selectRaw('status, COUNT(*) as count')
            ->when(!empty($periodo['inicio']), fn($q) => $q->whereDate('created_at', '>=', $periodo['inicio']))
            ->when(!empty($periodo['fim']), fn($q) => $q->whereDate('created_at', '<=', $periodo['fim']))
            ->groupBy('status')
            ->orderByDesc('count')
            ->get();

        return [
            'estatisticas_gerais' => $stats,
            'por_tipo' => $porTipo,
            'por_status' => $porStatus
        ];
    }
}