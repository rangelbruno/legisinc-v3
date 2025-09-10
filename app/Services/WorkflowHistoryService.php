<?php

namespace App\Services;

use App\Models\DocumentoWorkflowHistorico;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkflowHistoryService
{
    /**
     * Obtém o histórico completo de um documento
     */
    public function obterHistoricoDocumento(
        Model $documento,
        int $limite = 50,
        bool $paginar = false
    ): Collection|LengthAwarePaginator {
        $query = DocumentoWorkflowHistorico::porDocumento(get_class($documento), $documento->id)
            ->with(['workflow', 'transicao', 'etapaOrigem', 'etapaDestino', 'executadoPor'])
            ->recente();

        if ($paginar) {
            return $query->paginate($limite);
        }

        return $query->limit($limite)->get();
    }

    /**
     * Obtém histórico por período
     */
    public function obterHistoricoPorPeriodo(
        Carbon $inicio,
        Carbon $fim,
        array $filtros = []
    ): Collection {
        $query = DocumentoWorkflowHistorico::periodo($inicio, $fim)
            ->with(['workflow', 'transicao', 'etapaOrigem', 'etapaDestino', 'executadoPor'])
            ->recente();

        // Aplicar filtros opcionais
        if (!empty($filtros['workflow_id'])) {
            $query->porWorkflow($filtros['workflow_id']);
        }

        if (!empty($filtros['usuario_id'])) {
            $query->porUsuario($filtros['usuario_id']);
        }

        if (!empty($filtros['acao'])) {
            $query->acao($filtros['acao']);
        }

        if (!empty($filtros['documento_type'])) {
            $query->where('documento_type', $filtros['documento_type']);
        }

        return $query->get();
    }

    /**
     * Gera relatório de performance de workflows
     */
    public function relatorioPerformance(
        array $workflowIds = [],
        Carbon $inicio = null,
        Carbon $fim = null
    ): array {
        $inicio = $inicio ?? now()->subMonth();
        $fim = $fim ?? now();

        $query = DocumentoWorkflowHistorico::periodo($inicio, $fim)
            ->with(['workflow', 'etapaOrigem', 'etapaDestino']);

        if (!empty($workflowIds)) {
            $query->whereIn('workflow_id', $workflowIds);
        }

        $historico = $query->get();

        return [
            'periodo' => [
                'inicio' => $inicio->format('d/m/Y'),
                'fim' => $fim->format('d/m/Y')
            ],
            'estatisticas_gerais' => $this->calcularEstatisticasGerais($historico),
            'por_workflow' => $this->agruparPorWorkflow($historico),
            'por_etapa' => $this->agruparPorEtapa($historico),
            'por_usuario' => $this->agruparPorUsuario($historico),
            'tempo_medio_por_etapa' => $this->calcularTempoMedioPorEtapa($historico)
        ];
    }

    /**
     * Obtém estatísticas de um documento específico
     */
    public function estatisticasDocumento(Model $documento): array
    {
        $historico = $this->obterHistoricoDocumento($documento);

        if ($historico->isEmpty()) {
            return [
                'total_transicoes' => 0,
                'tempo_total_processamento' => null,
                'etapa_atual' => null,
                'ultima_acao' => null
            ];
        }

        $primeiraTransicao = $historico->last();
        $ultimaTransicao = $historico->first();

        return [
            'total_transicoes' => $historico->count(),
            'tempo_total_processamento' => $this->calcularTempoProcessamento($primeiraTransicao, $ultimaTransicao),
            'etapa_atual' => $ultimaTransicao->etapaDestino?->nome,
            'ultima_acao' => [
                'acao' => $ultimaTransicao->acao_formatada,
                'usuario' => $ultimaTransicao->executadoPor?->name,
                'data' => $ultimaTransicao->executado_em?->format('d/m/Y H:i')
            ],
            'etapas_percorridas' => $this->obterEtapasPercorridas($historico),
            'tempo_por_etapa' => $this->calcularTempoPorEtapaDocumento($historico)
        ];
    }

    /**
     * Identifica gargalos no workflow
     */
    public function identificarGargalos(
        int $workflowId,
        Carbon $inicio = null,
        Carbon $fim = null
    ): array {
        $inicio = $inicio ?? now()->subMonth();
        $fim = $fim ?? now();

        $historico = DocumentoWorkflowHistorico::porWorkflow($workflowId)
            ->periodo($inicio, $fim)
            ->with(['etapaOrigem', 'etapaDestino'])
            ->get();

        $temposPorEtapa = [];
        $documentosPorEtapa = [];

        foreach ($historico->groupBy('etapa_origem_id') as $etapaId => $transicoes) {
            if ($etapaId) {
                $etapa = $transicoes->first()->etapaOrigem;
                $tempos = [];

                foreach ($transicoes as $transicao) {
                    $duracao = $transicao->duracaoNaEtapa();
                    if ($duracao) {
                        $tempos[] = $this->converterDuracaoParaHoras($duracao);
                    }
                }

                if (!empty($tempos)) {
                    $temposPorEtapa[$etapa->nome] = [
                        'tempo_medio' => array_sum($tempos) / count($tempos),
                        'tempo_maximo' => max($tempos),
                        'total_documentos' => count($tempos),
                        'etapa_id' => $etapaId
                    ];
                }
            }
        }

        // Ordenar por tempo médio (gargalos primeiro)
        arsort($temposPorEtapa);

        return [
            'workflow_id' => $workflowId,
            'periodo_analise' => [
                'inicio' => $inicio->format('d/m/Y'),
                'fim' => $fim->format('d/m/Y')
            ],
            'gargalos_identificados' => array_slice($temposPorEtapa, 0, 5, true),
            'todas_etapas' => $temposPorEtapa
        ];
    }

    /**
     * Calcula estatísticas gerais do histórico
     */
    protected function calcularEstatisticasGerais(Collection $historico): array
    {
        return [
            'total_transicoes' => $historico->count(),
            'documentos_unicos' => $historico->unique(fn($item) => $item->documento_type . '#' . $item->documento_id)->count(),
            'workflows_ativos' => $historico->unique('workflow_id')->count(),
            'usuarios_ativos' => $historico->unique('executado_por')->count(),
            'acoes_mais_comuns' => $historico->groupBy('acao')
                ->map(fn($group) => $group->count())
                ->sortDesc()
                ->take(5)
                ->toArray()
        ];
    }

    /**
     * Agrupa histórico por workflow
     */
    protected function agruparPorWorkflow(Collection $historico): array
    {
        return $historico->groupBy('workflow_id')
            ->map(function ($grupo) {
                $workflow = $grupo->first()->workflow;
                return [
                    'nome' => $workflow?->nome ?? 'Desconhecido',
                    'total_transicoes' => $grupo->count(),
                    'documentos_processados' => $grupo->unique(fn($item) => $item->documento_type . '#' . $item->documento_id)->count()
                ];
            })
            ->toArray();
    }

    /**
     * Agrupa histórico por etapa
     */
    protected function agruparPorEtapa(Collection $historico): array
    {
        return $historico->groupBy('etapa_origem_id')
            ->map(function ($grupo) {
                $etapa = $grupo->first()->etapaOrigem;
                return [
                    'nome' => $etapa?->nome ?? 'Início',
                    'total_transicoes' => $grupo->count(),
                    'acoes' => $grupo->groupBy('acao')->map(fn($g) => $g->count())->toArray()
                ];
            })
            ->toArray();
    }

    /**
     * Agrupa histórico por usuário
     */
    protected function agruparPorUsuario(Collection $historico): array
    {
        return $historico->groupBy('executado_por')
            ->map(function ($grupo) {
                $usuario = $grupo->first()->executadoPor;
                return [
                    'nome' => $usuario?->name ?? 'Sistema',
                    'email' => $usuario?->email ?? 'sistema@legisinc.com',
                    'total_transicoes' => $grupo->count(),
                    'workflows_utilizados' => $grupo->unique('workflow_id')->count()
                ];
            })
            ->sortByDesc('total_transicoes')
            ->take(10)
            ->toArray();
    }

    /**
     * Calcula tempo médio por etapa
     */
    protected function calcularTempoMedioPorEtapa(Collection $historico): array
    {
        $temposPorEtapa = [];

        foreach ($historico->groupBy('etapa_origem_id') as $etapaId => $transicoes) {
            if ($etapaId) {
                $etapa = $transicoes->first()->etapaOrigem;
                $tempos = $transicoes->map(function ($transicao) {
                    return $this->converterDuracaoParaHoras($transicao->duracaoNaEtapa() ?? '0 minutos');
                })->filter(fn($tempo) => $tempo > 0);

                if ($tempos->isNotEmpty()) {
                    $temposPorEtapa[$etapa->nome] = [
                        'tempo_medio_horas' => $tempos->avg(),
                        'tempo_total_horas' => $tempos->sum(),
                        'documentos_processados' => $tempos->count()
                    ];
                }
            }
        }

        return $temposPorEtapa;
    }

    /**
     * Calcula tempo total de processamento
     */
    protected function calcularTempoProcessamento(
        DocumentoWorkflowHistorico $primeira,
        DocumentoWorkflowHistorico $ultima
    ): ?string {
        if (!$primeira->executado_em || !$ultima->executado_em) {
            return null;
        }

        $duracao = $primeira->executado_em->diff($ultima->executado_em);

        if ($duracao->days > 0) {
            return $duracao->days . ' dias, ' . $duracao->h . ' horas';
        } elseif ($duracao->h > 0) {
            return $duracao->h . ' horas, ' . $duracao->i . ' minutos';
        } else {
            return $duracao->i . ' minutos';
        }
    }

    /**
     * Obtém etapas percorridas por um documento
     */
    protected function obterEtapasPercorridas(Collection $historico): array
    {
        return $historico->reverse()->map(function ($item) {
            return [
                'etapa' => $item->etapaDestino?->nome ?? 'Final',
                'acao' => $item->acao_formatada,
                'data' => $item->executado_em?->format('d/m/Y H:i'),
                'usuario' => $item->executadoPor?->name ?? 'Sistema'
            ];
        })->values()->toArray();
    }

    /**
     * Calcula tempo por etapa para um documento específico
     */
    protected function calcularTempoPorEtapaDocumento(Collection $historico): array
    {
        $temposPorEtapa = [];
        $historico = $historico->reverse(); // Cronológica

        for ($i = 0; $i < $historico->count(); $i++) {
            $atual = $historico[$i];
            $proximo = $historico[$i + 1] ?? null;

            if ($proximo) {
                $duracao = $atual->executado_em->diff($proximo->executado_em);
                $etapaNome = $atual->etapaDestino?->nome ?? 'Etapa ' . $atual->etapa_destino_id;

                $temposPorEtapa[$etapaNome] = [
                    'duracao_formatada' => $this->formatarDuracao($duracao),
                    'duracao_horas' => $this->converterDuracaoParaHoras($this->formatarDuracao($duracao))
                ];
            }
        }

        return $temposPorEtapa;
    }

    /**
     * Converte string de duração para horas
     */
    protected function converterDuracaoParaHoras(string $duracao): float
    {
        if (str_contains($duracao, 'dias')) {
            preg_match('/(\d+) dias/', $duracao, $matches);
            return ($matches[1] ?? 0) * 24;
        } elseif (str_contains($duracao, 'horas')) {
            preg_match('/(\d+) horas/', $duracao, $matches);
            return (float) ($matches[1] ?? 0);
        } elseif (str_contains($duracao, 'minutos')) {
            preg_match('/(\d+) minutos/', $duracao, $matches);
            return ($matches[1] ?? 0) / 60;
        }

        return 0;
    }

    /**
     * Formata duração do Carbon Diff
     */
    protected function formatarDuracao(\DateInterval $duracao): string
    {
        if ($duracao->days > 0) {
            return $duracao->days . ' dias';
        } elseif ($duracao->h > 0) {
            return $duracao->h . ' horas';
        } else {
            return $duracao->i . ' minutos';
        }
    }
}