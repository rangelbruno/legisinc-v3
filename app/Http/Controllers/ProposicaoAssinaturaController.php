<?php

namespace App\Http\Controllers;

// use App\Models\Projeto; // REMOVED - migrated to Proposições
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposicaoAssinaturaController extends Controller
{
    /**
     * Lista proposições aguardando assinatura do parlamentar
     */
    public function index()
    {
        $proposicoes = Projeto::where('autor_id', Auth::id())
            ->whereIn('status', ['aprovado_assinatura', 'devolvido_correcao'])
            ->with(['revisor'])
            ->orderBy('data_revisao', 'desc')
            ->paginate(15);

        return view('proposicoes.assinatura.index', compact('proposicoes'));
    }

    /**
     * Tela para assinatura da proposição aprovada
     */
    public function assinar(Projeto $proposicao)
    {
        $this->authorize('update', $proposicao);
        
        if ($proposicao->status !== 'aprovado_assinatura') {
            abort(403, 'Proposição não está disponível para assinatura.');
        }

        return view('proposicoes.assinatura.assinar', compact('proposicao'));
    }

    /**
     * Tela para correção da proposição devolvida
     */
    public function corrigir(Projeto $proposicao)
    {
        $this->authorize('update', $proposicao);
        
        if ($proposicao->status !== 'devolvido_correcao') {
            abort(403, 'Proposição não está disponível para correção.');
        }

        return view('proposicoes.assinatura.corrigir', compact('proposicao'));
    }

    /**
     * Confirmar leitura da proposição
     */
    public function confirmarLeitura(Projeto $proposicao)
    {
        $this->authorize('update', $proposicao);
        
        $proposicao->update([
            'confirmacao_leitura' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Leitura confirmada!'
        ]);
    }

    /**
     * Processar assinatura digital
     */
    public function processarAssinatura(Request $request, Projeto $proposicao)
    {
        $this->authorize('update', $proposicao);
        
        $request->validate([
            'assinatura_digital' => 'required|string',
            'certificado_digital' => 'nullable|string',
        ]);

        if (!$proposicao->confirmacao_leitura) {
            return response()->json([
                'success' => false,
                'message' => 'É necessário confirmar a leitura do documento antes de assinar.'
            ], 400);
        }

        $proposicao->update([
            'status' => 'assinado',
            'assinatura_digital' => $request->assinatura_digital,
            'certificado_digital' => $request->certificado_digital,
            'data_assinatura' => now(),
            'ip_assinatura' => $request->ip(),
        ]);

        $proposicao->adicionarTramitacao(
            'Proposição assinada digitalmente',
            'aprovado_assinatura',
            'assinado'
        );

        return response()->json([
            'success' => true,
            'message' => 'Proposição assinada com sucesso!'
        ]);
    }

    /**
     * Enviar proposição para protocolo
     */
    public function enviarProtocolo(Projeto $proposicao)
    {
        $this->authorize('update', $proposicao);
        
        if ($proposicao->status !== 'assinado') {
            return response()->json([
                'success' => false,
                'message' => 'Proposição deve estar assinada para ser enviada ao protocolo.'
            ], 400);
        }

        $proposicao->update([
            'status' => 'enviado_protocolo'
        ]);

        $proposicao->adicionarTramitacao(
            'Enviado para protocolo',
            'assinado',
            'enviado_protocolo'
        );

        return response()->json([
            'success' => true,
            'message' => 'Proposição enviada para protocolo!'
        ]);
    }

    /**
     * Salvar correções na proposição devolvida
     */
    public function salvarCorrecoes(Request $request, Projeto $proposicao)
    {
        $this->authorize('update', $proposicao);
        
        $request->validate([
            'conteudo' => 'required|string'
        ]);

        if ($proposicao->status !== 'devolvido_correcao') {
            return response()->json([
                'success' => false,
                'message' => 'Proposição não está disponível para correção.'
            ], 400);
        }

        // Criar nova versão com as correções
        $proposicao->criarNovaVersao(
            $request->conteudo,
            'Correções baseadas no parecer técnico',
            'correcao'
        );

        return response()->json([
            'success' => true,
            'message' => 'Correções salvas com sucesso!'
        ]);
    }

    /**
     * Reenviar proposição para legislativo após correções
     */
    public function reenviarLegislativo(Projeto $proposicao)
    {
        $this->authorize('update', $proposicao);
        
        if ($proposicao->status !== 'devolvido_correcao') {
            return response()->json([
                'success' => false,
                'message' => 'Proposição não está disponível para reenvio.'
            ], 400);
        }

        // Limpar dados da revisão anterior
        $proposicao->update([
            'status' => 'enviado_legislativo',
            'revisor_id' => null,
            'analise_constitucionalidade' => null,
            'analise_juridicidade' => null,
            'analise_regimentalidade' => null,
            'analise_tecnica_legislativa' => null,
            'parecer_tecnico' => null,
            'tipo_retorno' => null,
            'observacoes_internas' => null,
            'data_revisao' => null,
        ]);

        $proposicao->adicionarTramitacao(
            'Reenviado para análise legislativa após correções',
            'devolvido_correcao',
            'enviado_legislativo'
        );

        return response()->json([
            'success' => true,
            'message' => 'Proposição reenviada para análise legislativa!'
        ]);
    }

    /**
     * Histórico de assinaturas do parlamentar
     */
    public function historico()
    {
        $proposicoes = Projeto::where('autor_id', Auth::id())
            ->whereNotNull('data_assinatura')
            ->with(['revisor'])
            ->orderBy('data_assinatura', 'desc')
            ->paginate(15);

        return view('proposicoes.assinatura.historico', compact('proposicoes'));
    }
}