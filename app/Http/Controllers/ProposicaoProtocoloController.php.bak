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
     * Tela de protocolação da proposição (versão simplificada)
     */
    public function protocolar(Proposicao $proposicao)
    {
        if (! in_array($proposicao->status, ['enviado_protocolo', 'assinado'])) {
            abort(403, 'Proposição não está disponível para protocolo.');
        }

        return view('proposicoes.protocolo.protocolar-simples', compact('proposicao'));
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

        if (! in_array($proposicao->status, ['enviado_protocolo', 'assinado'])) {
            return response()->json([
                'success' => false,
                'message' => 'Proposição não está disponível para protocolo.',
            ], 400);
        }

        // Verificações automáticas
        $verificacoes = $this->realizarVerificacoes($proposicao);

        if (! $verificacoes['todas_aprovadas']) {
            return response()->json([
                'success' => false,
                'message' => 'Nem todas as verificações foram aprovadas.',
                'verificacoes' => $verificacoes,
            ], 400);
        }

        // Atribuir número de processo se não existir
        if (! $proposicao->numero_protocolo) {
            try {
                $numeroProcesso = $this->numeroProcessoService->atribuirNumeroProcesso($proposicao);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao gerar número de processo: '.$e->getMessage(),
                ], 400);
            }
        } else {
            $numeroProcesso = $proposicao->numero_protocolo;
        }

        $proposicao->update([
            'status' => 'protocolado',
            'numero_protocolo' => $numeroProcesso, // Mantém compatibilidade
            'data_protocolo' => now(),
            'funcionario_protocolo_id' => Auth::id(),
            'comissoes_destino' => $request->comissoes_destino,
            'observacoes_protocolo' => $request->observacoes_protocolo,
            'verificacoes_realizadas' => $verificacoes,
        ]);

        // Regenerar PDF com número de protocolo
        try {
            error_log("Protocolo: Iniciando regeneração de PDF para proposição {$proposicao->id} com protocolo {$numeroProcesso}");
            $assinaturaController = app(\App\Http\Controllers\ProposicaoAssinaturaController::class);

            // Usar método que preserva formatação OnlyOffice
            $assinaturaController->regenerarPDFAtualizado($proposicao->fresh());
            error_log("Protocolo: PDF regenerado com sucesso para proposição {$proposicao->id}");
            
            // Validar se PDF foi gerado corretamente (validação robusta)
            $this->validarPDFGerado($proposicao->fresh(), $numeroProcesso);
            
        } catch (\Exception $e) {
            error_log("Protocolo: ERRO ao regenerar PDF para proposição {$proposicao->id}: ".$e->getMessage());
            // Log::warning('Falha ao regenerar PDF com número de protocolo', [
            //     'proposicao_id' => $proposicao->id,
            //     'numero_protocolo' => $numeroProcesso,
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
            'message' => 'Proposição protocolada com sucesso!',
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
        $ultimoNumero = Proposicao::where('numero_protocolo', 'like', $ano.'%')
            ->orderBy('numero_protocolo', 'desc')
            ->value('numero_protocolo');

        if ($ultimoNumero) {
            $ultimoSequencial = (int) substr($ultimoNumero, -4);
            $novoSequencial = $ultimoSequencial + 1;
        } else {
            $novoSequencial = 1;
        }

        return $ano.sprintf('%04d', $novoSequencial);
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
     * Obter comissões automáticas baseadas no tipo (versão simplificada)
     */
    private function obterComissoesAutomaticas(string $tipo): array
    {
        $comissoes = ['Comissão de Constituição e Justiça']; // Sempre obrigatória

        // Adicionar comissões específicas por tipo
        switch ($tipo) {
            case 'PL':
            case 'PLP':
            case 'mocao':
                $comissoes[] = 'Comissão de Legislação';
                break;
            case 'PEC':
                $comissoes[] = 'Comissão de Reforma Constitucional';
                break;
            case 'PDC':
                $comissoes[] = 'Comissão de Administração Pública';
                break;
        }

        return $comissoes;
    }

    /**
     * Realizar verificações automáticas
     */
    private function realizarVerificacoes(Proposicao $proposicao): array
    {
        $verificacoes = [
            'documento_assinado' => ! empty($proposicao->assinatura_digital),
            'texto_completo' => ! empty($proposicao->conteudo),
            'formato_adequado' => strlen($proposicao->conteudo) > 100, // Mínimo de caracteres
            'metadados_completos' => ! empty($proposicao->ementa) && ! empty($proposicao->tipo),
            'revisao_aprovada' => in_array($proposicao->status, ['enviado_protocolo', 'assinado']),
        ];

        $verificacoes['todas_aprovadas'] = ! in_array(false, $verificacoes, true);

        return $verificacoes;
    }

    /**
     * Atribuir número de processo a uma proposição (versão simplificada e automática)
     */
    public function atribuirNumeroProcesso(Request $request, Proposicao $proposicao)
    {
        if (! in_array($proposicao->status, ['enviado_protocolo', 'assinado'])) {
            return response()->json([
                'success' => false,
                'message' => 'Proposição deve estar assinada para receber número de protocolo.',
            ], 400);
        }

        if ($proposicao->numero_protocolo) {
            return response()->json([
                'success' => false,
                'message' => 'Esta proposição já possui um número de protocolo: '.$proposicao->numero_protocolo,
            ], 400);
        }

        try {
            // SEMPRE automático - sistema decide o número
            $numeroProcesso = $this->numeroProcessoService->atribuirNumeroProcesso($proposicao, null);

            // Atualizar status e dados de protocolo
            $proposicao->update([
                'status' => 'protocolado',
                'data_protocolo' => now(),
                'funcionario_protocolo_id' => Auth::id(),
                // Definir comissões padrão baseadas no tipo
                'comissoes_destino' => $this->obterComissoesAutomaticas($proposicao->tipo),
                'observacoes_protocolo' => 'Protocolado automaticamente pelo sistema',
            ]);

            // Regenerar PDF com número de protocolo atribuído
            try {
                $assinaturaController = app(\App\Http\Controllers\ProposicaoAssinaturaController::class);
                $assinaturaController->regenerarPDFAtualizado($proposicao->fresh());
            } catch (\Exception $e) {
                // Log::warning('Falha ao regenerar PDF após atribuir número de protocolo', [
                //     'proposicao_id' => $proposicao->id,
                //     'numero_protocolo' => $numeroProcesso,
                //     'error' => $e->getMessage()
                // ]);
            }

            return response()->json([
                'success' => true,
                'numero_protocolo' => $numeroProcesso,
                'data_protocolo' => $proposicao->fresh()->data_protocolo->format('d/m/Y H:i'),
                'message' => 'Proposição protocolada com sucesso!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
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
                'message' => 'Proposição deve estar protocolada para iniciar tramitação.',
            ], 400);
        }

        $proposicao->update([
            'status' => 'em_tramitacao',
        ]);

        // TODO: Implementar sistema de tramitação quando disponível
        // $proposicao->adicionarTramitacao(
        //     'Iniciada tramitação nas comissões',
        //     'protocolado',
        //     'em_tramitacao'
        // );

        return response()->json([
            'success' => true,
            'message' => 'Tramitação iniciada com sucesso!',
        ]);
    }

    /**
     * Validar se o PDF foi gerado corretamente com protocolo e assinatura
     */
    private function validarPDFGerado(Proposicao $proposicao, string $numeroProcesso): void
    {
        try {
            // Verificar se arquivo_pdf_path foi atualizado
            if (empty($proposicao->arquivo_pdf_path)) {
                error_log("Protocolo: AVISO - proposição {$proposicao->id} sem arquivo_pdf_path após regeneração");
                return;
            }

            $pdfPath = storage_path('app/' . $proposicao->arquivo_pdf_path);
            
            // Verificar se arquivo existe fisicamente
            if (!file_exists($pdfPath)) {
                error_log("Protocolo: ERRO - PDF não encontrado: {$pdfPath}");
                return;
            }

            // Extrair conteúdo do PDF para validação
            $comando = "pdftotext '{$pdfPath}' -";
            $conteudo = shell_exec($comando);
            
            if (empty($conteudo)) {
                error_log("Protocolo: AVISO - PDF vazio ou não legível: {$pdfPath}");
                return;
            }

            // Validar presença do número de protocolo
            $temProtocolo = stripos($conteudo, $numeroProcesso) !== false;
            $temPlaceholder = stripos($conteudo, '[AGUARDANDO PROTOCOLO]') !== false;
            
            if (!$temProtocolo || $temPlaceholder) {
                error_log("Protocolo: ❌ CRÍTICO - PDF sem número correto para proposição {$proposicao->id}");
                error_log("Protocolo: - Protocolo '{$numeroProcesso}' encontrado: " . ($temProtocolo ? 'SIM' : 'NÃO'));
                error_log("Protocolo: - Placeholder presente: " . ($temPlaceholder ? 'SIM' : 'NÃO'));
            } else {
                error_log("Protocolo: ✅ PDF válido com protocolo correto para proposição {$proposicao->id}");
            }

            // Validar presença de assinatura (se existir)
            if ($proposicao->assinatura_digital) {
                $temAssinatura = stripos($conteudo, 'ASSINATURA DIGITAL') !== false;
                if (!$temAssinatura) {
                    error_log("Protocolo: ❌ CRÍTICO - PDF sem assinatura digital para proposição {$proposicao->id}");
                } else {
                    error_log("Protocolo: ✅ PDF com assinatura digital OK para proposição {$proposicao->id}");
                }
            }

            // Log do tamanho do arquivo para monitoramento
            $tamanho = filesize($pdfPath);
            error_log("Protocolo: ℹ️  PDF gerado: {$tamanho} bytes - {$pdfPath}");

        } catch (\Exception $e) {
            error_log("Protocolo: ERRO na validação do PDF: " . $e->getMessage());
        }
    }
}
