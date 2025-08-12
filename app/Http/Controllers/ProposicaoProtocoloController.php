<?php

namespace App\Http\Controllers;

use App\Models\Proposicao;
use App\Services\NumeroProcessoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposicaoProtocoloController extends Controller
{
    private NumeroProcessoService $numeroProcessoService;

    public function __construct(NumeroProcessoService $numeroProcessoService)
    {
        $this->numeroProcessoService = $numeroProcessoService;
    }
    /**
     * Lista proposições aguardando protocolo
     */
    public function index()
    {
        $proposicoes = Proposicao::where('status', 'enviado_protocolo')
            ->with(['autor'])
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('proposicoes.protocolo.index', compact('proposicoes'));
    }

    /**
     * Tela de protocolação da proposição
     */
    public function protocolar(Proposicao $proposicao)
    {
        if (!in_array($proposicao->status, ['enviado_protocolo', 'assinado'])) {
            abort(403, 'Proposição não está disponível para protocolo.');
        }

        // Configurações de numeração
        $configuracoes = $this->numeroProcessoService->obterConfiguracoes();
        
        // Próximos números disponíveis
        $proximosNumeros = $this->numeroProcessoService->preverProximosNumeros();

        // Comissões disponíveis (simulado - implementar conforme necessário)
        $comissoes = $this->obterComissoes($proposicao->tipo);

        // Verificações automáticas
        $verificacoes = $this->realizarVerificacoes($proposicao);

        return view('proposicoes.protocolo.protocolar', compact(
            'proposicao', 
            'comissoes', 
            'verificacoes',
            'configuracoes',
            'proximosNumeros'
        ));
    }

    /**
     * Efetivar protocolo da proposição
     */
    public function efetivarProtocolo(Request $request, Proposicao $proposicao)
    {
        $request->validate([
            'comissoes_destino' => 'required|array|min:1',
            'comissoes_destino.*' => 'string',
            'observacoes_protocolo' => 'nullable|string',
        ]);

        if (!in_array($proposicao->status, ['enviado_protocolo', 'assinado'])) {
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

        // Atribuir número de processo se não existir
        if (!$proposicao->numero_processo) {
            try {
                $numeroProcesso = $this->numeroProcessoService->atribuirNumeroProcesso($proposicao);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao gerar número de processo: ' . $e->getMessage()
                ], 400);
            }
        } else {
            $numeroProcesso = $proposicao->numero_processo;
        }

        $proposicao->update([
            'status' => 'protocolado',
            'numero_protocolo' => $numeroProcesso, // Mantém compatibilidade
            'numero_processo' => $numeroProcesso,
            'data_protocolo' => now(),
            'funcionario_protocolo_id' => Auth::id(),
            'comissoes_destino' => $request->comissoes_destino,
            'observacoes_protocolo' => $request->observacoes_protocolo,
            'verificacoes_realizadas' => $verificacoes,
        ]);

        // Regenerar PDF com número de protocolo
        try {
            $onlyOfficeService = app(\App\Services\OnlyOffice\OnlyOfficeService::class);
            $onlyOfficeService->regenerarPDFComProtocolo($proposicao);
        } catch (\Exception $e) {
            // Log::warning('Falha ao regenerar PDF com número de protocolo', [
                //     'proposicao_id' => $proposicao->id,
                //     'numero_protocolo' => $numeroProtocolo,
                //     'error' => $e->getMessage()
            // ]);
        }

        // TODO: Implementar sistema de tramitação quando disponível
        // $proposicao->adicionarTramitacao(
        //     'Proposição protocolada - Nº ' . $numeroProtocolo,
        //     'enviado_protocolo',
        //     'protocolado',
        //     'Distribuída para: ' . implode(', ', $request->comissoes_destino)
        // );

        return response()->json([
            'success' => true,
            'numero_protocolo' => $numeroProcesso,
            'numero_processo' => $numeroProcesso,
            'message' => 'Proposição protocolada com sucesso!'
        ]);
    }

    /**
     * Proposições protocoladas hoje
     */
    public function protocolosHoje()
    {
        $proposicoes = Proposicao::where('status', 'protocolado')
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
            'aguardando_protocolo' => Proposicao::where('status', 'enviado_protocolo')->count(),
            'protocoladas_hoje' => Proposicao::where('status', 'protocolado')
                ->whereDate('data_protocolo', today())
                ->count(),
            'protocoladas_mes' => Proposicao::where('status', 'protocolado')
                ->whereMonth('data_protocolo', now()->month)
                ->whereYear('data_protocolo', now()->year)
                ->count(),
            'por_funcionario_mes' => Proposicao::where('funcionario_protocolo_id', Auth::id())
                ->whereMonth('data_protocolo', now()->month)
                ->whereYear('data_protocolo', now()->year)
                ->count(),
        ];

        $ultimos_protocolos = Proposicao::where('funcionario_protocolo_id', Auth::id())
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
        $ultimoNumero = Proposicao::where('numero_protocolo', 'like', $ano . '%')
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
    private function realizarVerificacoes(Proposicao $proposicao): array
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
     * Atribuir número de processo a uma proposição
     */
    public function atribuirNumeroProcesso(Request $request, Proposicao $proposicao)
    {
        $configuracoes = $this->numeroProcessoService->obterConfiguracoes();
        
        $rules = [
            'tipo_numeracao' => 'required|in:automatico,manual',
        ];
        
        // Só validar número manual se configurado para permitir
        if ($configuracoes['permitir_manual']) {
            $rules['numero_processo'] = 'required_if:tipo_numeracao,manual|string|nullable';
        }
        
        $request->validate($rules);

        if (!in_array($proposicao->status, ['enviado_protocolo', 'assinado'])) {
            return response()->json([
                'success' => false,
                'message' => 'Proposição deve estar assinada ou enviada para protocolo para receber número.'
            ], 400);
        }

        if ($proposicao->numero_processo) {
            return response()->json([
                'success' => false,
                'message' => 'Esta proposição já possui um número de processo: ' . $proposicao->numero_processo
            ], 400);
        }

        try {
            if ($request->tipo_numeracao === 'manual' && !$configuracoes['permitir_manual']) {
                return response()->json([
                    'success' => false,
                    'message' => 'A configuração atual não permite números manuais.'
                ], 400);
            }
            
            $numeroManual = $request->tipo_numeracao === 'manual' ? $request->numero_processo : null;
            $numeroProcesso = $this->numeroProcessoService->atribuirNumeroProcesso($proposicao, $numeroManual);

            // Atualizar status para protocolado
            $proposicao->update([
                'status' => 'protocolado'
            ]);

            return response()->json([
                'success' => true,
                'numero_processo' => $numeroProcesso,
                'numero_protocolo' => $numeroProcesso, // Mantém compatibilidade
                'data_protocolo' => $proposicao->fresh()->data_protocolo->format('d/m/Y H:i'),
                'message' => 'Número de processo atribuído com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Alias para compatibilidade
     */
    public function atribuirNumeroProtocolo(Request $request, Proposicao $proposicao)
    {
        return $this->atribuirNumeroProcesso($request, $proposicao);
    }

    /**
     * Iniciar tramitação da proposição protocolada
     */
    public function iniciarTramitacao(Proposicao $proposicao)
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

        // TODO: Implementar sistema de tramitação quando disponível
        // $proposicao->adicionarTramitacao(
        //     'Iniciada tramitação nas comissões',
        //     'protocolado',
        //     'em_tramitacao'
        // );

        return response()->json([
            'success' => true,
            'message' => 'Tramitação iniciada com sucesso!'
        ]);
    }
}