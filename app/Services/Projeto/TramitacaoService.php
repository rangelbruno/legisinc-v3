<?php

namespace App\Services\Projeto;

use App\Models\Projeto;
use App\Models\ProjetoTramitacao;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class TramitacaoService
{
    /**
     * Obtém estatísticas de tramitação por usuário
     */
    public function getEstatisticasPorUsuario(User $usuario): array
    {
        $baseQuery = Projeto::query();

        // Filtrar por permissões do usuário
        if ($usuario->can('projeto.view_all')) {
            // Usuário pode ver todos os projetos
        } elseif ($usuario->can('projeto.view_own')) {
            // Usuário só pode ver seus próprios projetos
            $baseQuery->where('autor_id', $usuario->id);
        } else {
            // Usuário não tem permissão para ver projetos
            return $this->getEstatisticasVazias();
        }

        $estatisticas = [
            'total_projetos' => $baseQuery->count(),
            'por_status' => $baseQuery->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray(),
            'aguardando_acao' => $this->getProjetosAguardandoAcao($usuario)->count(),
            'tramitados_hoje' => $this->getProjetosTramitadosHoje($usuario)->count(),
            'tramitados_semana' => $this->getProjetosTramitadosSemana($usuario)->count(),
        ];

        return $estatisticas;
    }

    /**
     * Obtém projetos que aguardam ação do usuário
     */
    public function getProjetosAguardandoAcao(User $usuario): Collection
    {
        $projetos = collect();

        // Projetos enviados aguardando análise (para legislativo)
        if ($usuario->can('projeto.analyze')) {
            $enviados = Projeto::where('status', 'enviado')->get();
            $projetos = $projetos->concat($enviados);
        }

        // Projetos em análise aguardando aprovação/rejeição (para legislativo)
        if ($usuario->can('projeto.approve') || $usuario->can('projeto.reject')) {
            $emAnalise = Projeto::where('status', 'em_analise')->get();
            $projetos = $projetos->concat($emAnalise);
        }

        // Projetos aprovados aguardando assinatura (para parlamentares)
        if ($usuario->can('projeto.sign')) {
            $aprovados = Projeto::where('status', 'aprovado')
                ->where('autor_id', $usuario->id)
                ->get();
            $projetos = $projetos->concat($aprovados);
        }

        // Projetos assinados aguardando protocolo (para protocolo)
        if ($usuario->can('projeto.assign_number')) {
            $assinados = Projeto::where('status', 'assinado')->get();
            $projetos = $projetos->concat($assinados);
        }

        // Projetos protocolados aguardando inclusão em sessão (para protocolo)
        if ($usuario->can('projeto.include_session')) {
            $protocolados = Projeto::where('status', 'protocolado')->get();
            $projetos = $projetos->concat($protocolados);
        }

        return $projetos->unique('id');
    }

    /**
     * Obtém projetos tramitados hoje
     */
    public function getProjetosTramitadosHoje(User $usuario): Collection
    {
        $tramitacaoHoje = ProjetoTramitacao::whereDate('created_at', today())
            ->with('projeto')
            ->get();

        return $this->filtrarProjetosPorPermissao($tramitacaoHoje->pluck('projeto'), $usuario);
    }

    /**
     * Obtém projetos tramitados na semana
     */
    public function getProjetosTramitadosSemana(User $usuario): Collection
    {
        $inicioSemana = now()->startOfWeek();
        $tramitacaoSemana = ProjetoTramitacao::where('created_at', '>=', $inicioSemana)
            ->with('projeto')
            ->get();

        return $this->filtrarProjetosPorPermissao($tramitacaoSemana->pluck('projeto'), $usuario);
    }

    /**
     * Obtém relatório detalhado de tramitação
     */
    public function getRelatorioTramitacao(array $filtros = []): array
    {
        $query = ProjetoTramitacao::query()
            ->with(['projeto', 'usuario']);

        // Aplicar filtros
        if (!empty($filtros['data_inicio'])) {
            $query->whereDate('created_at', '>=', $filtros['data_inicio']);
        }

        if (!empty($filtros['data_fim'])) {
            $query->whereDate('created_at', '<=', $filtros['data_fim']);
        }

        if (!empty($filtros['status_atual'])) {
            $query->where('status_atual', $filtros['status_atual']);
        }

        if (!empty($filtros['acao'])) {
            $query->where('acao', $filtros['acao']);
        }

        if (!empty($filtros['usuario_id'])) {
            $query->where('usuario_id', $filtros['usuario_id']);
        }

        $tramitacoes = $query->orderBy('created_at', 'desc')->get();

        // Agrupar por projeto
        $projetosTramitacao = $tramitacoes->groupBy('projeto_id')->map(function ($tramitacoesProjeto) {
            $projeto = $tramitacoesProjeto->first()->projeto;
            return [
                'projeto' => [
                    'id' => $projeto->id,
                    'titulo' => $projeto->titulo,
                    'numero_protocolo' => $projeto->numero_protocolo,
                    'status_atual' => $projeto->status,
                    'autor' => $projeto->autor->name ?? 'N/A',
                ],
                'tramitacoes' => $tramitacoesProjeto->map(function ($tramitacao) {
                    return [
                        'data' => $tramitacao->created_at->format('d/m/Y H:i'),
                        'usuario' => $tramitacao->usuario->name,
                        'acao' => $tramitacao->acao_formatada,
                        'status_anterior' => $tramitacao->status_anterior_formatado,
                        'status_atual' => $tramitacao->status_atual_formatado,
                        'observacoes' => $tramitacao->observacoes,
                    ];
                })->toArray()
            ];
        })->values()->toArray();

        return [
            'total_tramitacoes' => $tramitacoes->count(),
            'projetos_distintos' => $tramitacoes->pluck('projeto_id')->unique()->count(),
            'tramitacoes_por_status' => $tramitacoes->groupBy('status_atual')->map->count(),
            'tramitacoes_por_acao' => $tramitacoes->groupBy('acao')->map->count(),
            'projetos_tramitacao' => $projetosTramitacao,
        ];
    }

    /**
     * Obtém métricas de performance de tramitação
     */
    public function getMetricasPerformance(array $filtros = []): array
    {
        $dataInicio = $filtros['data_inicio'] ?? now()->startOfMonth();
        $dataFim = $filtros['data_fim'] ?? now();

        // Projetos criados no período
        $projetosCriados = Projeto::whereBetween('created_at', [$dataInicio, $dataFim])
            ->get();

        // Projetos finalizados (votados) no período
        $projetosFinalizados = Projeto::where('status', 'votado')
            ->whereBetween('updated_at', [$dataInicio, $dataFim])
            ->get();

        // Tempo médio de tramitação
        $tempoMedioTramitacao = $this->calcularTempoMedioTramitacao($projetosFinalizados);

        // Gargalos na tramitação
        $gargalos = $this->identificarGargalos($dataInicio, $dataFim);

        return [
            'periodo' => [
                'inicio' => $dataInicio->format('d/m/Y'),
                'fim' => $dataFim->format('d/m/Y'),
            ],
            'projetos_criados' => $projetosCriados->count(),
            'projetos_finalizados' => $projetosFinalizados->count(),
            'taxa_conclusao' => $projetosCriados->count() > 0 
                ? round(($projetosFinalizados->count() / $projetosCriados->count()) * 100, 2) 
                : 0,
            'tempo_medio_tramitacao_dias' => $tempoMedioTramitacao,
            'gargalos' => $gargalos,
            'distribuicao_por_status' => Projeto::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray(),
        ];
    }

    /**
     * Filtra projetos por permissão do usuário
     */
    private function filtrarProjetosPorPermissao(Collection $projetos, User $usuario): Collection
    {
        if ($usuario->can('projeto.view_all')) {
            return $projetos;
        }

        if ($usuario->can('projeto.view_own')) {
            return $projetos->where('autor_id', $usuario->id);
        }

        return collect();
    }

    /**
     * Retorna estatísticas vazias
     */
    private function getEstatisticasVazias(): array
    {
        return [
            'total_projetos' => 0,
            'por_status' => [],
            'aguardando_acao' => 0,
            'tramitados_hoje' => 0,
            'tramitados_semana' => 0,
        ];
    }

    /**
     * Calcula tempo médio de tramitação
     */
    private function calcularTempoMedioTramitacao(Collection $projetos): float
    {
        if ($projetos->isEmpty()) {
            return 0;
        }

        $tempos = $projetos->map(function ($projeto) {
            $primeiraTramitacao = $projeto->tramitacao()->oldest()->first();
            $ultimaTramitacao = $projeto->tramitacao()->latest()->first();

            if ($primeiraTramitacao && $ultimaTramitacao) {
                return $primeiraTramitacao->created_at->diffInDays($ultimaTramitacao->created_at);
            }

            return 0;
        })->filter(function ($tempo) {
            return $tempo > 0;
        });

        return $tempos->isEmpty() ? 0 : round($tempos->average(), 1);
    }

    /**
     * Identifica gargalos na tramitação
     */
    private function identificarGargalos($dataInicio, $dataFim): array
    {
        // Status com mais projetos parados
        $statusComMaisProjetos = Projeto::selectRaw('status, count(*) as total, AVG(DATEDIFF(NOW(), updated_at)) as dias_parado')
            ->whereNotIn('status', ['votado', 'rejeitado'])
            ->whereBetween('updated_at', [$dataInicio, $dataFim])
            ->groupBy('status')
            ->orderByDesc('total')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                return [
                    'status' => Projeto::STATUS[$item->status] ?? $item->status,
                    'total_projetos' => $item->total,
                    'dias_medio_parado' => round($item->dias_parado, 1),
                ];
            })
            ->toArray();

        return $statusComMaisProjetos;
    }

    /**
     * Exporta dados de tramitação para CSV
     */
    public function exportarTramitacaoCSV(array $filtros = []): string
    {
        $relatorio = $this->getRelatorioTramitacao($filtros);
        
        $csvData = [];
        $csvData[] = ['Projeto ID', 'Título', 'Protocolo', 'Status Atual', 'Autor', 'Data Tramitação', 'Usuário', 'Ação', 'Status Anterior', 'Status Atual', 'Observações'];

        foreach ($relatorio['projetos_tramitacao'] as $item) {
            foreach ($item['tramitacoes'] as $tramitacao) {
                $csvData[] = [
                    $item['projeto']['id'],
                    $item['projeto']['titulo'],
                    $item['projeto']['numero_protocolo'],
                    $item['projeto']['status_atual'],
                    $item['projeto']['autor'],
                    $tramitacao['data'],
                    $tramitacao['usuario'],
                    $tramitacao['acao'],
                    $tramitacao['status_anterior'],
                    $tramitacao['status_atual'],
                    $tramitacao['observacoes'],
                ];
            }
        }

        $output = fopen('php://temp', 'r+');
        foreach ($csvData as $row) {
            fputcsv($output, $row, ';');
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}