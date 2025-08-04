<?php

namespace App\Http\Controllers;

use App\Models\Proposicao;
use App\Services\MomentoSessaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpedienteController extends Controller
{
    /**
     * Exibir painel do expediente com documentos separados por momento da sessão
     */
    public function index()
    {
        // Obter proposições protocoladas separadas por momento
        $proposicoesExpediente = MomentoSessaoService::obterProposicoesPorMomento('EXPEDIENTE');
        $proposicoesOrdemDia = MomentoSessaoService::obterProposicoesPorMomento('ORDEM_DO_DIA');
        
        // Proposições não classificadas (precisam de atenção)
        $proposicoesNaoClassificadas = Proposicao::where('status', 'protocolado')
            ->whereIn('momento_sessao', ['NAO_CLASSIFICADO', null])
            ->with(['autor', 'tipoProposicao'])
            ->orderBy('data_protocolo', 'asc')
            ->get();

        // Obter estatísticas
        $estatisticas = MomentoSessaoService::obterEstatisticas();

        // Obter regras dos momentos
        $regrasExpediente = MomentoSessaoService::getRegrasMomento('EXPEDIENTE');
        $regrasOrdemDia = MomentoSessaoService::getRegrasMomento('ORDEM_DO_DIA');

        return view('expediente.index', compact(
            'proposicoesExpediente',
            'proposicoesOrdemDia', 
            'proposicoesNaoClassificadas',
            'estatisticas',
            'regrasExpediente',
            'regrasOrdemDia'
        ));
    }

    /**
     * Visualizar detalhes de uma proposição
     */
    public function show(Proposicao $proposicao)
    {
        // Carregar relacionamentos
        $proposicao->load(['autor', 'tipoProposicao', 'funcionarioProtocolo', 'parecer']);

        // Verificar se pode ser enviada para votação
        $validacaoVotacao = MomentoSessaoService::podeEnviarParaVotacao($proposicao);

        // Obter histórico de tramitação
        $tramitacao = $proposicao->logstramitacao()->with('user')->get();

        return view('expediente.show', compact(
            'proposicao',
            'validacaoVotacao',
            'tramitacao'
        ));
    }

    /**
     * Classificar proposição manualmente
     */
    public function classificar(Request $request, Proposicao $proposicao)
    {
        $request->validate([
            'momento_sessao' => 'required|in:EXPEDIENTE,ORDEM_DO_DIA'
        ]);

        $proposicao->update([
            'momento_sessao' => $request->momento_sessao
        ]);

        // Log da ação
        $proposicao->logstramitacao()->create([
            'acao' => 'CLASSIFICACAO_MOMENTO',
            'observacoes' => "Classificado como: " . $request->momento_sessao,
            'user_id' => Auth::id(),
            'status_anterior' => $proposicao->momento_sessao,
            'status_novo' => $request->momento_sessao
        ]);

        return redirect()->back()->with('success', 
            'Proposição classificada como ' . 
            ($request->momento_sessao === 'EXPEDIENTE' ? 'Expediente' : 'Ordem do Dia')
        );
    }

    /**
     * Reclassificar todas as proposições automaticamente
     */
    public function reclassificarTodas()
    {
        $classificadas = MomentoSessaoService::reclassificarProposicoes();

        return redirect()->back()->with('success', 
            "Reclassificadas {$classificadas} proposições automaticamente"
        );
    }

    /**
     * Enviar proposição para votação
     */
    public function enviarParaVotacao(Request $request, Proposicao $proposicao)
    {
        // Validar se pode ser enviada
        $validacao = MomentoSessaoService::podeEnviarParaVotacao($proposicao);
        
        if (!$validacao['pode_enviar']) {
            return redirect()->back()->withErrors([
                'votacao' => 'Não é possível enviar para votação: ' . implode(', ', $validacao['erros'])
            ]);
        }

        $request->validate([
            'observacoes' => 'nullable|string|max:1000'
        ]);

        // Atualizar status
        $proposicao->update([
            'status' => 'EM_VOTACAO'
        ]);

        // Log da ação
        $proposicao->logstramitacao()->create([
            'acao' => 'ENVIO_VOTACAO',
            'observacoes' => $request->observacoes ?? "Enviado para votação na " . 
                ($proposicao->momento_sessao === 'EXPEDIENTE' ? 'fase do Expediente' : 'Ordem do Dia'),
            'user_id' => Auth::id(),
            'status_anterior' => 'protocolado',
            'status_novo' => 'EM_VOTACAO'
        ]);

        return redirect()->back()->with('success', 
            'Proposição enviada para votação com sucesso!'
        );
    }

    /**
     * Exibir proposições do expediente (para menu)
     */
    public function expediente()
    {
        return $this->index();
    }

    /**
     * Exibir proposições aguardando pauta
     */
    public function aguardandoPauta()
    {
        $proposicoes = Proposicao::where('status', 'protocolado')
            ->whereNotNull('momento_sessao')
            ->where('momento_sessao', '!=', 'NAO_CLASSIFICADO')
            ->with(['autor', 'tipoProposicao', 'funcionarioProtocolo'])
            ->orderBy('momento_sessao')
            ->orderBy('data_protocolo', 'asc')
            ->get();

        // Separar por momento
        $porMomento = $proposicoes->groupBy('momento_sessao');

        return view('expediente.aguardando-pauta', compact('proposicoes', 'porMomento'));
    }

    /**
     * Relatório do expediente
     */
    public function relatorio(Request $request)
    {
        $dataInicio = $request->get('data_inicio', now()->subDays(30)->format('Y-m-d'));
        $dataFim = $request->get('data_fim', now()->format('Y-m-d'));

        $query = Proposicao::where('status', 'protocolado')
            ->whereBetween('data_protocolo', [$dataInicio, $dataFim])
            ->with(['autor', 'tipoProposicao', 'funcionarioProtocolo']);

        $proposicoes = $query->get();
        $estatisticas = [
            'total' => $proposicoes->count(),
            'por_momento' => $proposicoes->groupBy('momento_sessao')->map->count(),
            'por_tipo' => $proposicoes->groupBy('tipo')->map->count(),
            'por_autor' => $proposicoes->groupBy('autor.name')->map->count(),
        ];

        return view('expediente.relatorio', compact(
            'proposicoes', 
            'estatisticas', 
            'dataInicio', 
            'dataFim'
        ));
    }
}