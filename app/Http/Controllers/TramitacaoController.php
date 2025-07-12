<?php

namespace App\Http\Controllers;

use App\Services\Projeto\TramitacaoService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Exception;

class TramitacaoController extends Controller
{
    protected TramitacaoService $tramitacaoService;

    public function __construct(TramitacaoService $tramitacaoService)
    {
        $this->tramitacaoService = $tramitacaoService;
    }

    /**
     * Dashboard de tramitação
     */
    public function dashboard(): View
    {
        try {
            $usuario = auth()->user();
            $estatisticas = $this->tramitacaoService->getEstatisticasPorUsuario($usuario);
            $projetosAguardando = $this->tramitacaoService->getProjetosAguardandoAcao($usuario);
            $tramitadosHoje = $this->tramitacaoService->getProjetosTramitadosHoje($usuario);

            return view('modules.tramitacao.dashboard', compact(
                'estatisticas',
                'projetosAguardando',
                'tramitadosHoje'
            ));

        } catch (Exception $e) {
            return view('modules.tramitacao.dashboard', [
                'estatisticas' => [],
                'projetosAguardando' => collect(),
                'tramitadosHoje' => collect(),
                'error' => 'Erro ao carregar dashboard: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Relatório de tramitação
     */
    public function relatorio(Request $request): View
    {
        try {
            $filtros = $request->only([
                'data_inicio',
                'data_fim',
                'status_atual',
                'acao',
                'usuario_id'
            ]);

            $relatorio = $this->tramitacaoService->getRelatorioTramitacao($filtros);

            return view('modules.tramitacao.relatorio', compact('relatorio', 'filtros'));

        } catch (Exception $e) {
            return view('modules.tramitacao.relatorio', [
                'relatorio' => [],
                'filtros' => [],
                'error' => 'Erro ao gerar relatório: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Métricas de performance
     */
    public function metricas(Request $request): View
    {
        try {
            $filtros = $request->only(['data_inicio', 'data_fim']);
            $metricas = $this->tramitacaoService->getMetricasPerformance($filtros);

            return view('modules.tramitacao.metricas', compact('metricas', 'filtros'));

        } catch (Exception $e) {
            return view('modules.tramitacao.metricas', [
                'metricas' => [],
                'filtros' => [],
                'error' => 'Erro ao carregar métricas: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Estatísticas do usuário
     */
    public function estatisticasApi(): JsonResponse
    {
        try {
            $usuario = auth()->user();
            $estatisticas = $this->tramitacaoService->getEstatisticasPorUsuario($usuario);

            return response()->json([
                'success' => true,
                'estatisticas' => $estatisticas
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter estatísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Projetos aguardando ação
     */
    public function projetosAguardandoApi(): JsonResponse
    {
        try {
            $usuario = auth()->user();
            $projetos = $this->tramitacaoService->getProjetosAguardandoAcao($usuario);

            return response()->json([
                'success' => true,
                'projetos' => $projetos->map(function ($projeto) {
                    return [
                        'id' => $projeto->id,
                        'titulo' => $projeto->titulo,
                        'status' => $projeto->status,
                        'status_formatado' => $projeto->status_formatado,
                        'autor' => $projeto->autor->name ?? 'N/A',
                        'created_at' => $projeto->created_at->format('d/m/Y'),
                        'url' => route('projetos.show', $projeto->id),
                    ];
                })
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter projetos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar relatório em CSV
     */
    public function exportarCSV(Request $request): Response
    {
        try {
            $this->authorize('sistema.relatorios');

            $filtros = $request->only([
                'data_inicio',
                'data_fim',
                'status_atual',
                'acao',
                'usuario_id'
            ]);

            $csv = $this->tramitacaoService->exportarTramitacaoCSV($filtros);

            $filename = 'tramitacao_' . date('Y-m-d_H-i-s') . '.csv';

            return response($csv, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($csv),
            ]);

        } catch (Exception $e) {
            abort(500, 'Erro ao exportar relatório: ' . $e->getMessage());
        }
    }

    /**
     * Dashboard específico por role
     */
    public function dashboardParlamentar(): View
    {
        try {
            $this->authorize('projeto.view_own');

            $usuario = auth()->user();
            $estatisticas = $this->tramitacaoService->getEstatisticasPorUsuario($usuario);
            $meusProjetosAguardando = $this->tramitacaoService->getProjetosAguardandoAcao($usuario);

            return view('modules.tramitacao.dashboard-parlamentar', compact(
                'estatisticas',
                'meusProjetosAguardando'
            ));

        } catch (Exception $e) {
            return view('modules.tramitacao.dashboard-parlamentar', [
                'estatisticas' => [],
                'meusProjetosAguardando' => collect(),
                'error' => 'Erro ao carregar dashboard: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Dashboard específico para legislativo
     */
    public function dashboardLegislativo(): View
    {
        try {
            $this->authorize('projeto.analyze');

            $usuario = auth()->user();
            $estatisticas = $this->tramitacaoService->getEstatisticasPorUsuario($usuario);
            $projetosParaAnalise = $this->tramitacaoService->getProjetosAguardandoAcao($usuario);

            return view('modules.tramitacao.dashboard-legislativo', compact(
                'estatisticas',
                'projetosParaAnalise'
            ));

        } catch (Exception $e) {
            return view('modules.tramitacao.dashboard-legislativo', [
                'estatisticas' => [],
                'projetosParaAnalise' => collect(),
                'error' => 'Erro ao carregar dashboard: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Dashboard específico para protocolo
     */
    public function dashboardProtocolo(): View
    {
        try {
            $this->authorize('projeto.assign_number');

            $usuario = auth()->user();
            $estatisticas = $this->tramitacaoService->getEstatisticasPorUsuario($usuario);
            $projetosParaProtocolo = $this->tramitacaoService->getProjetosAguardandoAcao($usuario);

            return view('modules.tramitacao.dashboard-protocolo', compact(
                'estatisticas',
                'projetosParaProtocolo'
            ));

        } catch (Exception $e) {
            return view('modules.tramitacao.dashboard-protocolo', [
                'estatisticas' => [],
                'projetosParaProtocolo' => collect(),
                'error' => 'Erro ao carregar dashboard: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Buscar tramitações por filtros (AJAX)
     */
    public function buscar(Request $request): JsonResponse
    {
        try {
            $filtros = $request->only([
                'projeto_id',
                'data_inicio',
                'data_fim',
                'status_atual',
                'acao',
                'usuario_id'
            ]);

            $relatorio = $this->tramitacaoService->getRelatorioTramitacao($filtros);

            return response()->json([
                'success' => true,
                'tramitacoes' => $relatorio['projetos_tramitacao'],
                'total' => $relatorio['total_tramitacoes'],
                'projetos_distintos' => $relatorio['projetos_distintos']
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar tramitações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter métricas em tempo real (AJAX)
     */
    public function metricasTempoReal(): JsonResponse
    {
        try {
            $this->authorize('sistema.relatorios');

            $metricas = $this->tramitacaoService->getMetricasPerformance();

            return response()->json([
                'success' => true,
                'metricas' => $metricas
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter métricas: ' . $e->getMessage()
            ], 500);
        }
    }
}
