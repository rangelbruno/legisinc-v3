<?php

namespace App\Http\Controllers;

use App\Models\Projeto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposicaoLegislativoController extends Controller
{
    /**
     * Lista proposições aguardando revisão
     */
    public function index()
    {
        $proposicoes = Projeto::whereIn('status', ['enviado_legislativo', 'em_revisao'])
            ->with(['autor'])
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('proposicoes.legislativo.index', compact('proposicoes'));
    }

    /**
     * Tela de revisão técnica da proposição
     */
    public function revisar(Projeto $proposicao)
    {
        if (!in_array($proposicao->status, ['enviado_legislativo', 'em_revisao'])) {
            abort(403, 'Proposição não está disponível para revisão.');
        }

        // Marcar como em revisão se ainda não estiver
        if ($proposicao->status === 'enviado_legislativo') {
            $proposicao->update([
                'status' => 'em_revisao',
                'revisor_id' => Auth::id()
            ]);

            $proposicao->adicionarTramitacao(
                'Iniciada revisão técnica',
                'enviado_legislativo',
                'em_revisao'
            );
        }

        return view('proposicoes.legislativo.revisar', compact('proposicao'));
    }

    /**
     * Salvar análise técnica (sem finalizar)
     */
    public function salvarAnalise(Request $request, Projeto $proposicao)
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
    public function aprovar(Request $request, Projeto $proposicao)
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
    public function devolver(Request $request, Projeto $proposicao)
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
        $userId = Auth::id();
        
        $estatisticas = [
            'em_revisao' => Projeto::where('revisor_id', $userId)
                ->where('status', 'em_revisao')
                ->count(),
                
            'aprovadas' => Projeto::where('revisor_id', $userId)
                ->where('status', 'aprovado_assinatura')
                ->whereMonth('data_revisao', now()->month)
                ->count(),
                
            'devolvidas' => Projeto::where('revisor_id', $userId)
                ->where('status', 'devolvido_correcao')
                ->whereMonth('data_revisao', now()->month)
                ->count(),
                
            'total_mes' => Projeto::where('revisor_id', $userId)
                ->whereIn('status', ['aprovado_assinatura', 'devolvido_correcao'])
                ->whereMonth('data_revisao', now()->month)
                ->count(),
        ];

        $revisoes_recentes = Projeto::where('revisor_id', $userId)
            ->whereNotNull('data_revisao')
            ->with(['autor'])
            ->orderBy('data_revisao', 'desc')
            ->limit(10)
            ->get();

        return view('proposicoes.legislativo.relatorio', compact('estatisticas', 'revisoes_recentes'));
    }

    /**
     * Proposições aguardando protocolo
     */
    public function aguardandoProtocolo()
    {
        $proposicoes = Projeto::where('status', 'assinado')
            ->with(['autor'])
            ->orderBy('data_assinatura', 'asc')
            ->paginate(15);

        return view('proposicoes.legislativo.aguardando-protocolo', compact('proposicoes'));
    }
}