<?php

namespace App\Http\Controllers;

use App\Models\Projeto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposicaoProtocoloController extends Controller
{
    /**
     * Lista proposições aguardando protocolo
     */
    public function index()
    {
        $proposicoes = Projeto::where('status', 'enviado_protocolo')
            ->with(['autor'])
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('proposicoes.protocolo.index', compact('proposicoes'));
    }

    /**
     * Tela de protocolação da proposição
     */
    public function protocolar(Projeto $proposicao)
    {
        if ($proposicao->status !== 'enviado_protocolo') {
            abort(403, 'Proposição não está disponível para protocolo.');
        }

        // Gerar número de protocolo se não existir
        if (!$proposicao->numero_protocolo) {
            $numeroProtocolo = $this->gerarNumeroProtocolo();
            $proposicao->update(['numero_protocolo' => $numeroProtocolo]);
        }

        // Comissões disponíveis (simulado - implementar conforme necessário)
        $comissoes = $this->obterComissoes($proposicao->tipo);

        // Verificações automáticas
        $verificacoes = $this->realizarVerificacoes($proposicao);

        return view('proposicoes.protocolo.protocolar', compact('proposicao', 'comissoes', 'verificacoes'));
    }

    /**
     * Efetivar protocolo da proposição
     */
    public function efetivarProtocolo(Request $request, Projeto $proposicao)
    {
        $request->validate([
            'comissoes_destino' => 'required|array|min:1',
            'comissoes_destino.*' => 'string',
            'observacoes_protocolo' => 'nullable|string',
        ]);

        if ($proposicao->status !== 'enviado_protocolo') {
            return response()->json([
                'success' => false,
                'message' => 'Proposição não está disponível para protocolo.'
            ], 400);
        }

        // Verificações automáticas
        $verificacoes = $this->realizarVerificacoes($proposicao);
        
        if (!$verificacoes['todas_aprovadas']) {
            return response()->json([
                'success' => false,
                'message' => 'Nem todas as verificações foram aprovadas.',
                'verificacoes' => $verificacoes
            ], 400);
        }

        // Gerar número de protocolo se não existir
        if (!$proposicao->numero_protocolo) {
            $numeroProtocolo = $this->gerarNumeroProtocolo();
        } else {
            $numeroProtocolo = $proposicao->numero_protocolo;
        }

        $proposicao->update([
            'status' => 'protocolado',
            'numero_protocolo' => $numeroProtocolo,
            'data_protocolo' => now(),
            'funcionario_protocolo_id' => Auth::id(),
            'comissoes_destino' => $request->comissoes_destino,
            'observacoes_protocolo' => $request->observacoes_protocolo,
            'verificacoes_realizadas' => $verificacoes,
        ]);

        $proposicao->adicionarTramitacao(
            'Proposição protocolada - Nº ' . $numeroProtocolo,
            'enviado_protocolo',
            'protocolado',
            'Distribuída para: ' . implode(', ', $request->comissoes_destino)
        );

        return response()->json([
            'success' => true,
            'numero_protocolo' => $numeroProtocolo,
            'message' => 'Proposição protocolada com sucesso!'
        ]);
    }

    /**
     * Proposições protocoladas hoje
     */
    public function protocolosHoje()
    {
        $proposicoes = Projeto::where('status', 'protocolado')
            ->whereDate('data_protocolo', today())
            ->with(['autor', 'funcionarioProtocolo'])
            ->orderBy('data_protocolo', 'desc')
            ->paginate(15);

        return view('proposicoes.protocolo.protocolos-hoje', compact('proposicoes'));
    }

    /**
     * Estatísticas de protocolação
     */
    public function estatisticas()
    {
        $estatisticas = [
            'aguardando_protocolo' => Projeto::where('status', 'enviado_protocolo')->count(),
            'protocoladas_hoje' => Projeto::where('status', 'protocolado')
                ->whereDate('data_protocolo', today())
                ->count(),
            'protocoladas_mes' => Projeto::where('status', 'protocolado')
                ->whereMonth('data_protocolo', now()->month)
                ->whereYear('data_protocolo', now()->year)
                ->count(),
            'por_funcionario_mes' => Projeto::where('funcionario_protocolo_id', Auth::id())
                ->whereMonth('data_protocolo', now()->month)
                ->whereYear('data_protocolo', now()->year)
                ->count(),
        ];

        $ultimos_protocolos = Projeto::where('funcionario_protocolo_id', Auth::id())
            ->where('status', 'protocolado')
            ->with(['autor'])
            ->orderBy('data_protocolo', 'desc')
            ->limit(10)
            ->get();

        return view('proposicoes.protocolo.estatisticas', compact('estatisticas', 'ultimos_protocolos'));
    }

    /**
     * Gerar número de protocolo automático
     */
    private function gerarNumeroProtocolo(): string
    {
        $ano = date('Y');
        $ultimoNumero = Projeto::where('numero_protocolo', 'like', $ano . '%')
            ->orderBy('numero_protocolo', 'desc')
            ->value('numero_protocolo');

        if ($ultimoNumero) {
            $ultimoSequencial = (int) substr($ultimoNumero, -4);
            $novoSequencial = $ultimoSequencial + 1;
        } else {
            $novoSequencial = 1;
        }

        return $ano . sprintf('%04d', $novoSequencial);
    }

    /**
     * Obter comissões baseadas no tipo de proposição
     */
    private function obterComissoes(string $tipo): array
    {
        $comissoes = [
            'Comissão de Constituição e Justiça' => true, // Sempre obrigatória
        ];

        // Adicionar comissões específicas por tipo
        switch ($tipo) {
            case 'PL':
            case 'PLP':
                $comissoes['Comissão de Legislação'] = false;
                break;
            case 'PEC':
                $comissoes['Comissão de Reforma Constitucional'] = false;
                break;
            case 'PDC':
                $comissoes['Comissão de Administração Pública'] = false;
                break;
        }

        // Comissões temáticas opcionais
        $comissoes['Comissão de Finanças e Orçamento'] = false;
        $comissoes['Comissão de Direitos Humanos'] = false;
        $comissoes['Comissão de Meio Ambiente'] = false;
        $comissoes['Comissão de Educação'] = false;

        return $comissoes;
    }

    /**
     * Realizar verificações automáticas
     */
    private function realizarVerificacoes(Projeto $proposicao): array
    {
        $verificacoes = [
            'documento_assinado' => !empty($proposicao->assinatura_digital),
            'texto_completo' => !empty($proposicao->conteudo),
            'formato_adequado' => strlen($proposicao->conteudo) > 100, // Mínimo de caracteres
            'metadados_completos' => !empty($proposicao->ementa) && !empty($proposicao->tipo),
            'revisao_aprovada' => $proposicao->status === 'enviado_protocolo',
        ];

        $verificacoes['todas_aprovadas'] = !in_array(false, $verificacoes, true);

        return $verificacoes;
    }

    /**
     * Iniciar tramitação da proposição protocolada
     */
    public function iniciarTramitacao(Projeto $proposicao)
    {
        if ($proposicao->status !== 'protocolado') {
            return response()->json([
                'success' => false,
                'message' => 'Proposição deve estar protocolada para iniciar tramitação.'
            ], 400);
        }

        $proposicao->update([
            'status' => 'em_tramitacao'
        ]);

        $proposicao->adicionarTramitacao(
            'Iniciada tramitação nas comissões',
            'protocolado',
            'em_tramitacao'
        );

        return response()->json([
            'success' => true,
            'message' => 'Tramitação iniciada com sucesso!'
        ]);
    }
}