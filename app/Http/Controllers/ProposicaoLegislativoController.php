<?php

namespace App\Http\Controllers;

use App\Models\Proposicao;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProposicaoLegislativoController extends Controller
{
    /**
     * Calcular tempo médio de revisão
     */
    private function calcularTempoMedioRevisao()
    {
        $proposicoesRevisadas = Proposicao::whereIn('status', ['aprovado_assinatura', 'devolvido_correcao', 'retornado_legislativo'])
            ->whereNotNull('updated_at')
            ->whereColumn('updated_at', '>', 'created_at')
            ->get();
            
        if ($proposicoesRevisadas->isEmpty()) {
            return 0;
        }
        
        $tempoTotal = 0;
        foreach ($proposicoesRevisadas as $proposicao) {
            $tempoTotal += $proposicao->created_at->diffInHours($proposicao->updated_at);
        }
        
        return round($tempoTotal / $proposicoesRevisadas->count(), 1);
    }
    /**
     * Lista proposições aguardando revisão
     */
    public function index(Request $request)
    {
        $query = Proposicao::with(['autor'])
            ->whereIn('status', ['enviado_legislativo', 'em_revisao']);
            
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('ementa', 'like', "%{$search}%")
                  ->orWhere('numero_temporario', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        
        // Removido filtro de prioridade pois a coluna urgencia não existe
        
        if ($request->filled('autor')) {
            $query->whereHas('autor', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->autor}%");
            });
        }
        
        $proposicoes = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get all proposicoes for statistics (without pagination)
        $allProposicoes = Proposicao::whereIn('status', [
            'enviado_legislativo', 
            'em_revisao', 
            'aprovado_assinatura', 
            'devolvido_correcao',
            'retornado_legislativo'
        ])->get();

        // Estatísticas adicionais
        $estatisticas = [
            'total_mes' => Proposicao::whereIn('status', ['enviado_legislativo', 'em_revisao', 'aprovado_assinatura', 'devolvido_correcao', 'retornado_legislativo'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'tempo_medio_revisao' => $this->calcularTempoMedioRevisao(),
            'por_tipo' => Proposicao::whereIn('status', ['enviado_legislativo', 'em_revisao'])
                ->selectRaw('tipo, count(*) as total')
                ->groupBy('tipo')
                ->pluck('total', 'tipo'),
            'urgentes' => 0 // Removido pois a coluna urgencia não existe
        ];

        return view('proposicoes.legislativo.index', compact('proposicoes', 'allProposicoes', 'estatisticas'));
    }

    /**
     * Tela de revisão técnica da proposição
     */
    public function revisar(Proposicao $proposicao)
    {
        if (!in_array($proposicao->status, ['enviado_legislativo', 'em_revisao'])) {
            abort(403, 'Proposição não está disponível para revisão.');
        }

        // Marcar como em revisão se ainda não estiver
        if ($proposicao->status === 'enviado_legislativo') {
            $proposicao->update([
                'status' => 'em_revisao'
            ]);
        }

        return view('proposicoes.legislativo.revisar', compact('proposicao'));
    }

    /**
     * Tela de edição da proposição pelo Legislativo
     */
    public function editar(Proposicao $proposicao)
    {
        if (!in_array($proposicao->status, ['enviado_legislativo', 'em_revisao', 'editado_legislativo'])) {
            abort(403, 'Proposição não está disponível para edição.');
        }

        // Marcar como editado pelo legislativo se ainda não estiver
        if ($proposicao->status !== 'editado_legislativo') {
            $proposicao->update([
                'status' => 'editado_legislativo'
            ]);
        }

        return view('proposicoes.legislativo.editar', compact('proposicao'));
    }

    /**
     * Salvar edições feitas pelo Legislativo
     */
    public function salvarEdicao(Request $request, Proposicao $proposicao)
    {
        $request->validate([
            'ementa' => 'required|string|max:1000',
            'conteudo' => 'required|string',
            'observacoes_edicao' => 'nullable|string'
        ]);

        $proposicao->update([
            'ementa' => $request->ementa,
            'conteudo' => $request->conteudo,
            'observacoes_edicao' => $request->observacoes_edicao,
            'ultima_modificacao' => now(),
            'status' => 'editado_legislativo'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proposição salva com sucesso!'
        ]);
    }

    /**
     * Enviar proposição editada de volta para o Parlamentar
     */
    public function enviarParaParlamentar(Request $request, Proposicao $proposicao)
    {
        $request->validate([
            'observacoes_retorno' => 'nullable|string',
            'solicitar_aprovacao' => 'boolean'
        ]);

        $novoStatus = $request->solicitar_aprovacao ? 'aguardando_aprovacao_autor' : 'devolvido_edicao';
        
        $proposicao->update([
            'status' => $novoStatus,
            'observacoes_retorno' => $request->observacoes_retorno,
            'data_retorno_legislativo' => now()
        ]);

        $mensagem = $request->solicitar_aprovacao 
            ? 'Proposição enviada para aprovação do autor!' 
            : 'Proposição devolvida ao parlamentar!';

        return response()->json([
            'success' => true,
            'message' => $mensagem
        ]);
    }

    /**
     * Salvar análise técnica (sem finalizar)
     */
    public function salvarAnalise(Request $request, Proposicao $proposicao)
    {
        $request->validate([
            'analise_constitucionalidade' => 'nullable|boolean',
            'analise_juridicidade' => 'nullable|boolean',
            'analise_regimentalidade' => 'nullable|boolean',
            'analise_tecnica_legislativa' => 'nullable|boolean',
            'parecer_tecnico' => 'nullable|string',
            'observacoes_internas' => 'nullable|string',
        ]);

        $proposicao->update([
            'analise_constitucionalidade' => $request->analise_constitucionalidade,
            'analise_juridicidade' => $request->analise_juridicidade,
            'analise_regimentalidade' => $request->analise_regimentalidade,
            'analise_tecnica_legislativa' => $request->analise_tecnica_legislativa,
            'parecer_tecnico' => $request->parecer_tecnico,
            'observacoes_internas' => $request->observacoes_internas,
            'data_revisao' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Análise salva com sucesso!'
        ]);
    }

    /**
     * Aprovar proposição para assinatura
     */
    public function aprovar(Request $request, Proposicao $proposicao)
    {
        $request->validate([
            'parecer_tecnico' => 'required|string',
            'analise_constitucionalidade' => 'required|boolean',
            'analise_juridicidade' => 'required|boolean',
            'analise_regimentalidade' => 'required|boolean',
            'analise_tecnica_legislativa' => 'required|boolean',
        ]);

        // Verificar se todas as análises foram aprovadas
        if (!$request->analise_constitucionalidade || 
            !$request->analise_juridicidade || 
            !$request->analise_regimentalidade || 
            !$request->analise_tecnica_legislativa) {
            
            return response()->json([
                'success' => false,
                'message' => 'Todas as análises técnicas devem ser aprovadas para prosseguir.'
            ], 400);
        }

        $proposicao->update([
            'status' => 'aprovado_assinatura',
            'tipo_retorno' => 'aprovado_assinatura',
            'analise_constitucionalidade' => $request->analise_constitucionalidade,
            'analise_juridicidade' => $request->analise_juridicidade,
            'analise_regimentalidade' => $request->analise_regimentalidade,
            'analise_tecnica_legislativa' => $request->analise_tecnica_legislativa,
            'parecer_tecnico' => $request->parecer_tecnico,
            'observacoes_internas' => $request->observacoes_internas,
            'data_revisao' => now(),
        ]);

        $proposicao->adicionarTramitacao(
            'Proposição aprovada para assinatura',
            'em_revisao',
            'aprovado_assinatura',
            $request->parecer_tecnico
        );

        return response()->json([
            'success' => true,
            'message' => 'Proposição aprovada para assinatura!'
        ]);
    }

    /**
     * Devolver proposição para correção
     */
    public function devolver(Request $request, Proposicao $proposicao)
    {
        $request->validate([
            'parecer_tecnico' => 'required|string',
        ]);

        $proposicao->update([
            'status' => 'devolvido_correcao',
            'tipo_retorno' => 'devolver_correcao',
            'parecer_tecnico' => $request->parecer_tecnico,
            'observacoes_internas' => $request->observacoes_internas,
            'data_revisao' => now(),
        ]);

        $proposicao->adicionarTramitacao(
            'Proposição devolvida para correção',
            'em_revisao',
            'devolvido_correcao',
            $request->parecer_tecnico
        );

        return response()->json([
            'success' => true,
            'message' => 'Proposição devolvida para correção!'
        ]);
    }

    /**
     * Relatório de produtividade do revisor
     */
    public function relatorio()
    {
        // Buscar usuários com role Legislativo para o filtro
        $usuarios = User::whereHas('roles', function ($query) {
                $query->where('name', User::PERFIL_LEGISLATIVO);
            })
            ->orWhere('id', Auth::id()) // Incluir o usuário logado sempre
            ->orderBy('name')
            ->get();

        return view('proposicoes.legislativo.relatorio', compact('usuarios'));
    }

    /**
     * Obter dados do relatório baseado nos filtros
     */
    public function dadosRelatorio(Request $request)
    {
        $dados = $this->obterDadosRelatorio($request);
        
        return view('proposicoes.legislativo.relatorio-dados', compact('dados'));
    }

    /**
     * Gerar relatório em PDF
     */
    public function relatorioPdf(Request $request)
    {
        $dados = $this->obterDadosRelatorio($request);
        
        try {
            // Gerar HTML do relatório
            $html = view('proposicoes.legislativo.relatorio-pdf', compact('dados'))->render();
            
            // Tentar usar DomPDF se disponível
            if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
                $filename = 'relatorio_produtividade_' . date('Y-m-d_H-i-s') . '.pdf';
                return $pdf->download($filename);
            }
            
            // Alternativa: retornar HTML como PDF (pode ser impresso pelo navegador)
            $filename = 'relatorio_produtividade_' . date('Y-m-d_H-i-s') . '.html';
            
            return response($html)
                ->header('Content-Type', 'text/html; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao gerar PDF: ' . $e->getMessage(),
                'dados' => $dados
            ], 500);
        }
    }

    /**
     * Gerar relatório em Excel
     */
    public function relatorioExcel(Request $request)
    {
        $dados = $this->obterDadosRelatorio($request);
        
        try {
            $filename = 'relatorio_produtividade_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];
            
            $callback = function() use ($dados) {
                $file = fopen('php://output', 'w');
                
                // BOM para UTF-8
                fwrite($file, "\xEF\xBB\xBF");
                
                // Cabeçalho do relatório
                fputcsv($file, ['RELATÓRIO DE PRODUTIVIDADE LEGISLATIVA'], ';');
                fputcsv($file, [''], ';');
                fputcsv($file, ['Período:', $dados['data_inicio'] . ' até ' . $dados['data_fim']], ';');
                fputcsv($file, ['Data de Geração:', now()->format('d/m/Y H:i:s')], ';');
                fputcsv($file, ['Total de Proposições:', $dados['total_geral']], ';');
                fputcsv($file, [''], ';');
                
                // Resumo geral
                fputcsv($file, ['RESUMO GERAL'], ';');
                fputcsv($file, ['Tipo', 'Quantidade'], ';');
                fputcsv($file, ['Aprovadas', array_sum(array_column($dados['dados_por_usuario'], 'aprovadas'))], ';');
                fputcsv($file, ['Devolvidas', array_sum(array_column($dados['dados_por_usuario'], 'devolvidas'))], ';');
                fputcsv($file, ['Retornadas', array_sum(array_column($dados['dados_por_usuario'], 'retornadas'))], ';');
                fputcsv($file, ['Total', $dados['total_geral']], ';');
                fputcsv($file, [''], ';');
                
                // Produtividade por usuário
                if (count($dados['dados_por_usuario']) > 0) {
                    fputcsv($file, ['PRODUTIVIDADE POR USUÁRIO'], ';');
                    fputcsv($file, ['Usuário', 'Aprovadas', 'Devolvidas', 'Retornadas', 'Total', 'Taxa de Aprovação (%)'], ';');
                    
                    foreach ($dados['dados_por_usuario'] as $dadosUsuario) {
                        $taxaAprovacao = $dadosUsuario['total'] > 0 ? round(($dadosUsuario['aprovadas'] / $dadosUsuario['total']) * 100, 1) : 0;
                        fputcsv($file, [
                            $dadosUsuario['nome'],
                            $dadosUsuario['aprovadas'],
                            $dadosUsuario['devolvidas'],
                            $dadosUsuario['retornadas'],
                            $dadosUsuario['total'],
                            $taxaAprovacao
                        ], ';');
                    }
                    fputcsv($file, [''], ';');
                }
                
                // Detalhamento das proposições
                if ($dados['proposicoes']->count() > 0) {
                    fputcsv($file, ['DETALHAMENTO DAS PROPOSIÇÕES'], ';');
                    fputcsv($file, ['ID', 'Tipo', 'Título', 'Autor', 'Status', 'Data'], ';');
                    
                    foreach ($dados['proposicoes'] as $proposicao) {
                        $status = match($proposicao->status) {
                            'aprovado_assinatura' => 'Aprovada',
                            'devolvido_correcao' => 'Devolvida',
                            'retornado_legislativo' => 'Retornada',
                            default => ucfirst($proposicao->status)
                        };
                        
                        fputcsv($file, [
                            $proposicao->id,
                            strtoupper($proposicao->tipo),
                            $proposicao->titulo ?? 'Proposição #' . $proposicao->id,
                            $proposicao->autor->name,
                            $status,
                            $proposicao->updated_at->format('d/m/Y H:i')
                        ], ';');
                    }
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao gerar Excel: ' . $e->getMessage(),
                'dados' => $dados
            ], 500);
        }
    }

    /**
     * Método auxiliar para obter dados do relatório
     */
    private function obterDadosRelatorio(Request $request)
    {
        $periodo = $request->input('periodo', 'mes_atual');
        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');
        $usuarioId = $request->input('usuario');

        // Definir intervalo de datas baseado no período
        switch ($periodo) {
            case 'hoje':
                $dataInicio = Carbon::today();
                $dataFim = Carbon::today()->endOfDay();
                break;
            case 'mes_atual':
                $dataInicio = Carbon::now()->startOfMonth();
                $dataFim = Carbon::now()->endOfMonth();
                break;
            case 'ano_atual':
                $dataInicio = Carbon::now()->startOfYear();
                $dataFim = Carbon::now()->endOfYear();
                break;
            case 'personalizado':
                $dataInicio = $dataInicio ? Carbon::parse($dataInicio) : Carbon::now()->startOfMonth();
                $dataFim = $dataFim ? Carbon::parse($dataFim)->endOfDay() : Carbon::now()->endOfMonth();
                break;
        }

        // Query base
        $query = Proposicao::whereIn('status', ['aprovado_assinatura', 'devolvido_correcao', 'retornado_legislativo'])
            ->whereBetween('updated_at', [$dataInicio, $dataFim]);

        $proposicoes = $query->with(['autor'])->get();

        // Como não temos a coluna revisor_id, vamos agrupar os dados de forma geral
        // Para futuras implementações, seria necessário adicionar essa coluna
        $dadosPorUsuario = [];
        
        // Se um usuário específico foi selecionado, mostrar apenas dados dele
        if ($usuarioId) {
            $usuario = User::find($usuarioId);
            if ($usuario) {
                $dadosPorUsuario[$usuarioId] = [
                    'nome' => $usuario->name,
                    'aprovadas' => $proposicoes->where('status', 'aprovado_assinatura')->count(),
                    'devolvidas' => $proposicoes->where('status', 'devolvido_correcao')->count(),
                    'retornadas' => $proposicoes->where('status', 'retornado_legislativo')->count(),
                    'total' => $proposicoes->count()
                ];
            }
        } else {
            // Mostrar dados gerais para todos os usuários legislativos
            $usuarios = User::whereHas('roles', function ($query) {
                $query->where('name', User::PERFIL_LEGISLATIVO);
            })->get();

            foreach ($usuarios as $usuario) {
                $dadosPorUsuario[$usuario->id] = [
                    'nome' => $usuario->name,
                    'aprovadas' => $proposicoes->where('status', 'aprovado_assinatura')->count(),
                    'devolvidas' => $proposicoes->where('status', 'devolvido_correcao')->count(),
                    'retornadas' => $proposicoes->where('status', 'retornado_legislativo')->count(),
                    'total' => $proposicoes->count()
                ];
            }

            // Se não há usuários específicos, mostrar dados gerais
            if (empty($dadosPorUsuario)) {
                $dadosPorUsuario['geral'] = [
                    'nome' => 'Dados Gerais',
                    'aprovadas' => $proposicoes->where('status', 'aprovado_assinatura')->count(),
                    'devolvidas' => $proposicoes->where('status', 'devolvido_correcao')->count(),
                    'retornadas' => $proposicoes->where('status', 'retornado_legislativo')->count(),
                    'total' => $proposicoes->count()
                ];
            }
        }

        return [
            'periodo' => $periodo,
            'data_inicio' => $dataInicio->format('d/m/Y'),
            'data_fim' => $dataFim->format('d/m/Y'),
            'dados_por_usuario' => $dadosPorUsuario,
            'total_geral' => $proposicoes->count(),
            'proposicoes' => $proposicoes
        ];
    }

    /**
     * Proposições aguardando protocolo
     */
    public function aguardandoProtocolo()
    {
        $proposicoes = Proposicao::where('status', 'assinado')
            ->with(['autor'])
            ->orderBy('updated_at', 'asc')
            ->paginate(15);

        return view('proposicoes.legislativo.aguardando-protocolo', compact('proposicoes'));
    }
}