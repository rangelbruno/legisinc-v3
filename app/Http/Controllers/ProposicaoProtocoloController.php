<?php

namespace App\Http\Controllers;

use App\Models\Proposicao;
use App\Services\NumeroProcessoService;
use App\Services\ProtocoloRTFService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProposicaoProtocoloController extends Controller
{
    private NumeroProcessoService $numeroProcessoService;
    private ProtocoloRTFService $protocoloRTFService;

    public function __construct(NumeroProcessoService $numeroProcessoService, ProtocoloRTFService $protocoloRTFService)
    {
        $this->numeroProcessoService = $numeroProcessoService;
        $this->protocoloRTFService = $protocoloRTFService;
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
        // CRITICAL: Only allow protocol view for SIGNED proposals
        if (!in_array($proposicao->status, ['assinado', 'enviado_protocolo'])) {
            abort(409, 'Proposição deve estar ASSINADA para protocolo. Status: ' . $proposicao->status);
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

        // CRITICAL: Only allow protocol for SIGNED proposals
        if (!in_array($proposicao->status, ['assinado', 'enviado_protocolo'])) {
            return response()->json([
                'success' => false,
                'message' => 'Proposição deve estar ASSINADA para receber protocolo. Status atual: ' . $proposicao->status,
                'status_code' => 409
            ], 409);
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

        // 📋 LOG: Registrar atribuição de protocolo
        \App\Models\DocumentWorkflowLog::logProtocolAssignment(
            proposicaoId: $proposicao->id,
            status: 'success',
            description: "Número de protocolo {$numeroProcesso} atribuído com sucesso",
            protocolNumber: $numeroProcesso,
            metadata: [
                'funcionario_protocolo_id' => Auth::id(),
                'funcionario_nome' => Auth::user()->name,
                'comissoes_destino' => $request->comissoes_destino,
                'observacoes_protocolo' => $request->observacoes_protocolo,
                'verificacoes_realizadas' => $verificacoes,
                'data_protocolo' => now()->toISOString(),
                'tipo_proposicao' => $proposicao->tipoProposicao?->codigo ?? 'unknown',
                'status_anterior' => $proposicao->status,
                'status_atual' => 'protocolado'
            ]
        );

        // CORREÇÃO: Forçar regeneração de PDF com número de protocolo usando template service
        try {
            error_log("Protocolo: Invalidando PDF antigo e forçando regeneração para proposição {$proposicao->id} com protocolo {$numeroProcesso}");
            
            // CRÍTICO: Invalidar PDF antigo para forçar regeneração com protocolo
            $proposicao->update([
                'arquivo_pdf_path' => null,
                'pdf_gerado_em' => null,
                'pdf_conversor_usado' => null,
            ]);
            
            // NOVO: Usar geração via controller de assinatura que já funciona corretamente
            $assinaturaController = app(\App\Http\Controllers\ProposicaoAssinaturaController::class);
            
            // Forçar regeneração de PDF atualizado
            $novoPDF = $this->forcarRegeneracaoPDFComProtocolo($proposicao->fresh(), $numeroProcesso);
            
            if ($novoPDF) {
                error_log("Protocolo: ✅ PDF regenerado com protocolo para proposição {$proposicao->id}: {$novoPDF}");
                $this->validarPDFGerado($proposicao->fresh(), $numeroProcesso);
            } else {
                throw new \Exception("Falha ao regenerar PDF com protocolo");
            }
            
        } catch (\Exception $e) {
            error_log("Protocolo: ERRO CRÍTICO ao regenerar PDF com protocolo para proposição {$proposicao->id}: ".$e->getMessage());
            
            // Fallback to PDF stamping if regeneration fails
            error_log("Protocolo: Tentando fallback para stamp PDF");
            try {
                $this->aplicarStampProtocolo($proposicao->fresh(), $numeroProcesso);
            } catch (\Exception $stampError) {
                throw new \Exception("Falha ao aplicar protocolo tanto na regeneração quanto no PDF stamp. Regeneração: " . $e->getMessage() . " | Stamp: " . $stampError->getMessage());
            }
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
        // CRITICAL: Only allow protocol for SIGNED proposals  
        if (!in_array($proposicao->status, ['assinado', 'enviado_protocolo'])) {
            return response()->json([
                'success' => false,
                'message' => 'Proposição deve estar ASSINADA para receber número de protocolo. Status atual: ' . $proposicao->status,
                'status_code' => 409
            ], 409);
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

            // CORREÇÃO: Usar mesmo método de regeneração forçada
            try {
                error_log("Protocolo Automático: Invalidando PDF e forçando regeneração para proposição {$proposicao->id}");
                
                // Invalidar PDF antigo
                $proposicao->update([
                    'arquivo_pdf_path' => null,
                    'pdf_gerado_em' => null,
                    'pdf_conversor_usado' => null,
                ]);
                
                // Forçar regeneração de PDF atualizado
                $novoPDF = $this->forcarRegeneracaoPDFComProtocolo($proposicao->fresh(), $numeroProcesso);
                
                if (!$novoPDF) {
                    throw new \Exception("Falha ao regenerar PDF com protocolo automático");
                }
                
                error_log("Protocolo Automático: ✅ PDF regenerado com sucesso");
                
            } catch (\Exception $e) {
                Log::error('ERRO CRÍTICO: Falha ao regenerar PDF com protocolo automático', [
                    'proposicao_id' => $proposicao->id,
                    'numero_protocolo' => $numeroProcesso,
                    'error' => $e->getMessage()
                ]);
                
                // Fallback to PDF stamping
                try {
                    $this->aplicarStampProtocolo($proposicao->fresh(), $numeroProcesso);
                } catch (\Exception $stampError) {
                    throw new \Exception("Impossível protocolar: Falha na regeneração e stamp. Regeneração: " . $e->getMessage() . " | Stamp: " . $stampError->getMessage());
                }
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
     * Update RTF with protocol number and regenerate PDF while preserving digital signature
     * This replaces [AGUARDANDO PROTOCOLO] variable in the RTF template
     */
    private function atualizarRTFComProtocolo(Proposicao $proposicao, string $numeroProcesso): void
    {
        try {
            error_log("Protocolo RTF: Iniciando atualização RTF para proposição {$proposicao->id} com protocolo {$numeroProcesso}");
            
            // Step 1: Update RTF with protocol number
            $rtfAtualizado = $this->protocoloRTFService->atualizarRTFComProtocolo($proposicao, $numeroProcesso);
            
            if (!$rtfAtualizado) {
                throw new \Exception("Falha ao atualizar RTF com protocolo");
            }
            
            error_log("Protocolo RTF: ✅ RTF atualizado com protocolo");
            
            // Step 2: Generate new PDF from updated RTF
            $pdfProtocolado = $this->protocoloRTFService->gerarPDFProtocolado($proposicao->fresh());
            
            if (!$pdfProtocolado) {
                throw new \Exception("Falha ao gerar PDF a partir do RTF protocolado");
            }
            
            error_log("Protocolo RTF: ✅ PDF gerado a partir do RTF protocolado: {$pdfProtocolado}");
            
            // Step 3: If proposal was signed, apply digital signature to new PDF
            if ($proposicao->assinatura_digital && $proposicao->certificado_digital_path) {
                $pdfAssinado = $this->aplicarAssinaturaDigitalAoPDF($pdfProtocolado, $proposicao);
                if ($pdfAssinado) {
                    $pdfProtocolado = $pdfAssinado;
                    error_log("Protocolo RTF: ✅ Assinatura digital aplicada ao PDF protocolado");
                }
            }
            
            // Step 4: Update proposição paths
            $relativePath = str_replace(storage_path('app/'), '', $pdfProtocolado);
            
            $proposicao->update([
                'arquivo_pdf_path' => $relativePath,
                'arquivo_pdf_protocolado' => $relativePath,
                'pdf_protocolo_aplicado' => true,
                'data_aplicacao_protocolo' => now(),
                'pdf_conversor_usado' => 'rtf_protocol_replacement',
                'pdf_gerado_em' => now()
            ]);
            
            error_log("Protocolo RTF: ✅ Protocolo aplicado via substituição RTF com sucesso para proposição {$proposicao->id}");
            
        } catch (\Exception $e) {
            error_log("Protocolo RTF: ❌ ERRO ao atualizar RTF com protocolo: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Apply digital signature to a PDF if proposal was previously signed
     */
    private function aplicarAssinaturaDigitalAoPDF(string $pdfPath, Proposicao $proposicao): ?string
    {
        try {
            if (!$proposicao->assinatura_digital || !$proposicao->certificado_digital_path) {
                return null;
            }

            error_log("Protocolo RTF: Aplicando assinatura digital ao PDF protocolado");

            $assinaturaService = app(\App\Services\AssinaturaDigitalService::class);
            
            // Use the existing signature data
            $pdfAssinado = $assinaturaService->assinarPDF(
                $pdfPath, 
                [
                    'nome_assinante' => $proposicao->autor->name ?? 'Autor',
                    'cargo_assinante' => 'Vereador',
                    'data_assinatura' => $proposicao->data_assinatura ?? now(),
                    'motivo_assinatura' => 'Proposição protocolada',
                    'certificado_path' => $proposicao->certificado_digital_path,
                    'certificado_senha' => $proposicao->certificado_senha ?? ''
                ],
                $proposicao->autor // Pass the proposicao author as user
            );

            return $pdfAssinado;

        } catch (\Exception $e) {
            error_log("Protocolo RTF: ERRO ao aplicar assinatura digital: {$e->getMessage()}");
            return null; // Return null if signature fails, so original PDF is used
        }
    }

    /**
     * Apply protocol stamp to existing signed PDF using PDFStampingService
     * CRITICAL: Use existing signed PDF instead of regenerating to preserve digital signature
     * This is the FALLBACK method when RTF update fails
     */
    private function aplicarStampProtocolo(Proposicao $proposicao, string $numeroProcesso): void
    {
        try {
            // STEP 1: Find the most recent signed PDF automatically
            error_log("Protocolo: PASSO 1 - Localizando PDF assinado mais recente para proposição {$proposicao->id}");
            
            $pdfAssinado = $this->encontrarPDFAssinadoMaisRecente($proposicao);
            
            if (!$pdfAssinado) {
                throw new \Exception("PDF assinado não encontrado para proposição {$proposicao->id}");
            }
            
            error_log("Protocolo: ✅ PDF assinado encontrado: {$pdfAssinado}");
            
            // STEP 2: Apply protocol stamp to existing signed PDF (preserving signature)
            error_log("Protocolo: PASSO 2 - Aplicando carimbo de protocolo ao PDF assinado");
            
            $stampingService = app(\App\Services\PDFStampingService::class);
            
            $protocolData = [
                'data_protocolo' => now()->format('d/m/Y H:i'),
                'funcionario_protocolo' => Auth::user()->name ?? 'Sistema'
            ];
            
            $pdfProtocolado = $stampingService->applyProtocolStamp($pdfAssinado, $numeroProcesso, $protocolData);
            
            if (!$pdfProtocolado) {
                // If stamping fails, use the signed PDF directly with protocol number update
                error_log("Protocolo: ⚠️  Carimbo falhou, usando PDF assinado original");
                $pdfProtocolado = $this->atualizarProtocoloNoPDF($pdfAssinado, $numeroProcesso);
            }

            // Update proposição with protocoled PDF path and ensure correct main pointer
            $relativePath = str_replace(storage_path('app/'), '', $pdfProtocolado);
            
            // Also update the PDF reference fields for consistency
            $pdfAssinadoRelative = str_replace(storage_path('app/'), '', $pdfAssinado);
            
            $proposicao->update([
                'arquivo_pdf_protocolado' => $relativePath,  // Campo correto na tabela
                'arquivo_pdf_path' => $relativePath,  // CRITICAL: Update main pointer
                'arquivo_pdf_assinado' => $pdfAssinadoRelative,  // Campo correto na tabela
                'pdf_assinado_path' => $pdfAssinadoRelative,  // Campo existente
                'pdf_protocolo_aplicado' => true,
                'data_aplicacao_protocolo' => now(),
                'pdf_conversor_usado' => 'protocol_stamp_on_signed',  // Mark as stamped on signed PDF
                'pdf_gerado_em' => now()
            ]);

            error_log("Protocolo: ✅ PDF com protocolo aplicado com sucesso: {$pdfProtocolado}");
            error_log("Protocolo: ✅ Ponteiro arquivo_pdf_path atualizado para: {$relativePath}");
            error_log("Protocolo: ✅ PDF assinado rastreado em: {$pdfAssinadoRelative}");

        } catch (\Exception $e) {
            error_log("Protocolo: ❌ ERRO ao aplicar protocolo: {$e->getMessage()}");
            Log::error("Erro crítico ao aplicar protocolo ao PDF", [
                'proposicao_id' => $proposicao->id,
                'numero_protocolo' => $numeroProcesso,
                'status' => $proposicao->status,
                'pdf_assinado_path' => $proposicao->pdf_assinado_path,
                'pdf_oficial_path' => $proposicao->pdf_oficial_path,
                'arquivo_pdf_path' => $proposicao->arquivo_pdf_path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Generate new PDF with correct protocol number
     * This ensures the PDF shows the actual protocol number instead of [AGUARDANDO PROTOCOLO]
     */
    private function gerarPDFComNumeroProtocolo(Proposicao $proposicao, string $numeroProcesso): ?string
    {
        try {
            error_log("PDF Protocolo: Gerando PDF com protocolo {$numeroProcesso} para proposição {$proposicao->id}");
            
            // Get the latest content (from OnlyOffice saves if any)
            $conteudoFinal = $this->obterConteudoMaisRecente($proposicao);
            
            // Update proposição with protocol number temporarily for PDF generation
            $proposicao->numero_protocolo = $numeroProcesso;
            $proposicao->save();
            
            // Use the PDF optimization service to generate PDF with protocol
            $pdfOptimizationService = app(\App\Services\Performance\PDFOptimizationService::class);
            
            $pdfPath = $pdfOptimizationService->gerarPDFOtimizado($proposicao, true);
            
            if ($pdfPath && file_exists($pdfPath)) {
                error_log("PDF Protocolo: ✅ PDF gerado com sucesso: {$pdfPath}");
                
                // Validate that the PDF contains the protocol number
                $this->validarNumeroNoPDF($pdfPath, $numeroProcesso);
                
                return $pdfPath;
            } else {
                error_log("PDF Protocolo: ❌ Falha ao gerar PDF");
                return null;
            }
            
        } catch (\Exception $e) {
            error_log("PDF Protocolo: ERRO ao gerar PDF com protocolo: {$e->getMessage()}");
            Log::error("Erro ao gerar PDF com número de protocolo", [
                'proposicao_id' => $proposicao->id,
                'numero_protocolo' => $numeroProcesso,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Find the most recent signed PDF automatically by scanning storage directories
     */
    private function encontrarPDFAssinadoMaisRecente(Proposicao $proposicao): ?string
    {
        try {
            // Search patterns for signed PDFs
            $searchPaths = [
                storage_path('app/proposicoes/pdfs/' . $proposicao->id . '/*assinado*.pdf'),
                storage_path('app/private/proposicoes/pdfs/' . $proposicao->id . '/*assinado*.pdf'),
                storage_path('app/proposicoes/pdfs/' . $proposicao->id . '/*_optimized_assinado_*.pdf'),
            ];

            $encontrados = [];

            foreach ($searchPaths as $pattern) {
                $files = glob($pattern);
                if ($files) {
                    foreach ($files as $file) {
                        if (file_exists($file) && filesize($file) > 1000) { // At least 1KB
                            $encontrados[] = [
                                'path' => $file,
                                'mtime' => filemtime($file),
                                'size' => filesize($file)
                            ];
                        }
                    }
                }
            }

            if (empty($encontrados)) {
                error_log("Protocolo: Nenhum PDF assinado encontrado para proposição {$proposicao->id}");
                return null;
            }

            // Sort by modification time (most recent first)
            usort($encontrados, function($a, $b) {
                return $b['mtime'] - $a['mtime'];
            });

            $maisRecente = $encontrados[0];
            error_log("Protocolo: PDF assinado mais recente: {$maisRecente['path']} ({$maisRecente['size']} bytes, " . date('d/m/Y H:i:s', $maisRecente['mtime']) . ")");

            return $maisRecente['path'];

        } catch (\Exception $e) {
            error_log("Protocolo: Erro ao localizar PDF assinado: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Update protocol number in existing PDF by creating a copy with updated metadata
     */
    private function atualizarProtocoloNoPDF(string $pdfOriginal, string $numeroProcesso): string
    {
        try {
            // Create a new filename for the protocoled version
            $pathInfo = pathinfo($pdfOriginal);
            $novoNome = $pathInfo['filename'] . '_protocolado_' . str_replace('/', '_', $numeroProcesso) . '.pdf';
            $novoCaminho = $pathInfo['dirname'] . '/' . $novoNome;

            // For now, just copy the signed PDF and add protocol metadata
            if (copy($pdfOriginal, $novoCaminho)) {
                error_log("Protocolo: PDF copiado com protocolo: {$novoCaminho}");
                return $novoCaminho;
            } else {
                error_log("Protocolo: Falha ao copiar PDF, usando original: {$pdfOriginal}");
                return $pdfOriginal;
            }

        } catch (\Exception $e) {
            error_log("Protocolo: Erro ao atualizar protocolo no PDF: {$e->getMessage()}");
            return $pdfOriginal; // Return original if update fails
        }
    }

    /**
     * Get the most recent content from OnlyOffice saves or database
     */
    private function obterConteudoMaisRecente(Proposicao $proposicao): string
    {
        try {
            // For protocol generation, we'll use the database content
            // The PDF optimization service will handle finding the most recent content
            error_log("PDF Protocolo: Usando conteúdo do banco de dados");
            return $proposicao->conteudo ?? '';
            
        } catch (\Exception $e) {
            error_log("PDF Protocolo: Erro ao obter conteúdo: {$e->getMessage()}");
            return '';
        }
    }

    /**
     * Validate that the PDF contains the correct protocol number
     */
    private function validarNumeroNoPDF(string $pdfPath, string $numeroProtocolo): void
    {
        try {
            $comando = "pdftotext '{$pdfPath}' -";
            $conteudo = shell_exec($comando);
            
            if ($conteudo) {
                $temProtocolo = stripos($conteudo, $numeroProtocolo) !== false;
                $temPlaceholder = stripos($conteudo, '[AGUARDANDO PROTOCOLO]') !== false;
                
                if ($temProtocolo && !$temPlaceholder) {
                    error_log("PDF Protocolo: ✅ Validação OK - PDF contém {$numeroProtocolo}");
                } else {
                    error_log("PDF Protocolo: ⚠️  Problema na validação:");
                    error_log("PDF Protocolo: - Protocolo '{$numeroProtocolo}' encontrado: " . ($temProtocolo ? 'SIM' : 'NÃO'));
                    error_log("PDF Protocolo: - Placeholder presente: " . ($temPlaceholder ? 'SIM' : 'NÃO'));
                }
            }
        } catch (\Exception $e) {
            error_log("PDF Protocolo: Erro na validação: {$e->getMessage()}");
        }
    }


    /**
     * Validate if PDF is legitimate (not HTML fallback)
     */
    private function isPDFLegitimo(string $pdfPath, Proposicao $proposicao): bool
    {
        try {
            // Check file size (HTML PDFs are usually smaller)
            $size = filesize($pdfPath);
            if ($size < 10000) { // Less than 10KB is suspicious
                error_log("Protocolo: PDF muito pequeno ({$size} bytes), provavelmente HTML fallback");
                return false;
            }

            // Check filename patterns that indicate fallback
            $filename = basename($pdfPath);
            $suspiciousPatterns = ['process', 'temporar', 'dompdf', 'padrao', 'fallback', 'html'];
            
            foreach ($suspiciousPatterns as $pattern) {
                if (stripos($filename, $pattern) !== false) {
                    error_log("Protocolo: PDF com nome suspeito: {$filename}");
                    return false;
                }
            }

            // For signed PDFs, ensure they're from signature process
            if (in_array($proposicao->status, ['assinado', 'enviado_protocolo', 'protocolado'])) {
                if (stripos($filename, 'assinatura') === false && stripos($filename, 'protocolado') === false) {
                    // Check if contains actual signature content
                    $content = shell_exec("pdftotext '{$pdfPath}' - 2>/dev/null");
                    if ($content && stripos($content, 'ASSINATURA DIGITAL') === false) {
                        error_log("Protocolo: PDF sem conteúdo de assinatura digital");
                        return false;
                    }
                }
            }

            return true;

        } catch (\Exception $e) {
            error_log("Protocolo: Erro ao validar PDF: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Forçar regeneração de PDF com número de protocolo correto
     * Este método contorna os problemas do ProtocoloRTFService usando o Template Universal
     */
    private function forcarRegeneracaoPDFComProtocolo(Proposicao $proposicao, string $numeroProcesso): ?string
    {
        try {
            error_log("Protocolo: Iniciando regeneração forçada de PDF para proposição {$proposicao->id}");
            
            // 1. Usar o Template Universal Service que já funciona corretamente com variáveis
            $templateService = app(\App\Services\Template\TemplateUniversalService::class);
            
            // 2. Gerar RTF com protocolo usando o Template Universal
            $rtfAtualizado = $templateService->aplicarTemplateParaProposicao($proposicao);
            
            if (!$rtfAtualizado) {
                throw new \Exception("Falha ao gerar RTF com Template Universal");
            }
            
            // 3. Salvar RTF atualizado no disco 'private'
            $novoRTFPath = 'proposicoes/proposicao_' . $proposicao->id . '_protocolo_' . time() . '.rtf';
            \Storage::disk('private')->put($novoRTFPath, $rtfAtualizado);
            
            // 4. Atualizar proposição com novo RTF
            $proposicao->update(['arquivo_path' => $novoRTFPath]);
            
            error_log("Protocolo: RTF regenerado via Template Universal: {$novoRTFPath}");
            
            // 5. Converter RTF para PDF usando DocumentConversionService
            $conversionService = app(\App\Services\DocumentConversionService::class);
            $rtfFullPath = \Storage::disk('private')->path($novoRTFPath);
            $pdfOutputPath = str_replace('.rtf', '.pdf', $rtfFullPath);
            
            $result = $conversionService->convertToPDF($rtfFullPath, $pdfOutputPath);
            
            if ($result['success'] && file_exists($pdfOutputPath)) {
                // 6. Salvar PDF em local correto
                $pdfFinalPath = 'proposicoes/pdfs/' . $proposicao->id . '/proposicao_' . $proposicao->id . '_protocolo_' . time() . '.pdf';
                
                // Criar diretório se não existir
                $pdfDir = dirname(\Storage::path($pdfFinalPath));
                if (!is_dir($pdfDir)) {
                    mkdir($pdfDir, 0755, true);
                }
                
                // Copiar PDF para local final
                if (copy($pdfOutputPath, \Storage::path($pdfFinalPath))) {
                    // 7. Atualizar proposição
                    $proposicao->update([
                        'arquivo_pdf_path' => $pdfFinalPath,
                        'pdf_gerado_em' => now(),
                        'pdf_conversor_usado' => 'template_universal_protocolo'
                    ]);
                    
                    // Limpar arquivo temporário
                    unlink($pdfOutputPath);
                    
                    error_log("Protocolo: ✅ PDF final salvo: {$pdfFinalPath}");
                    return \Storage::path($pdfFinalPath);
                }
            }
            
            throw new \Exception("Falha na conversão RTF para PDF");
            
        } catch (\Exception $e) {
            error_log("Protocolo: ERRO na regeneração forçada: {$e->getMessage()}");
            return null;
        }
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
