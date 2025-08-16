<?php

namespace App\Http\Controllers;

use App\Models\Proposicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProposicaoAssinaturaController extends Controller
{
    use AuthorizesRequests;
    /**
     * Lista proposições aguardando assinatura do parlamentar
     */
    public function index()
    {
        $proposicoes = Proposicao::where('autor_id', Auth::id())
            ->whereIn('status', ['aprovado_assinatura', 'devolvido_correcao'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return view('proposicoes.assinatura.index', compact('proposicoes'));
    }

    /**
     * Tela para assinatura da proposição aprovada
     */
    public function assinar(Proposicao $proposicao)
    {
        // $this->authorize('update', $proposicao);
        
        if (!in_array($proposicao->status, ['aprovado_assinatura', 'retornado_legislativo'])) {
            abort(403, 'Proposição não está disponível para assinatura.');
        }

        // SEMPRE regenerar PDF para assinatura para garantir dados corretos
        try {
            $this->gerarPDFParaAssinatura($proposicao);
        } catch (\Exception $e) {
            // Se falhar, tentar usar PDF existente como fallback
            $pdfPath = $proposicao->arquivo_pdf_path ? storage_path('app/' . $proposicao->arquivo_pdf_path) : null;
            if (!$proposicao->arquivo_pdf_path || !file_exists($pdfPath)) {
                // Se nem o PDF existente serve, mostrar erro
                return back()->withErrors(['pdf' => 'Não foi possível gerar o PDF para assinatura: ' . $e->getMessage()]);
            }
            // Log::warning('Usando PDF existente após falha na regeneração', [
                //     'proposicao_id' => $proposicao->id,
                //     'error' => $e->getMessage()
            // ]);
        }

        return view('proposicoes.assinatura.assinar', compact('proposicao'));
    }

    /**
     * Tela para correção da proposição devolvida
     */
    public function corrigir(Proposicao $proposicao)
    {
        // $this->authorize('update', $proposicao);
        
        if ($proposicao->status !== 'devolvido_correcao') {
            abort(403, 'Proposição não está disponível para correção.');
        }

        return view('proposicoes.assinatura.corrigir', compact('proposicao'));
    }

    /**
     * Confirmar leitura da proposição
     */
    public function confirmarLeitura(Proposicao $proposicao)
    {
        // $this->authorize('update', $proposicao);
        
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
    public function processarAssinatura(Request $request, Proposicao $proposicao)
    {
        // $this->authorize('update', $proposicao);
        
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

        // TODO: Implementar sistema de tramitação quando disponível
        // $proposicao->adicionarTramitacao(
        //     'Proposição assinada digitalmente',
        //     'aprovado_assinatura',
        //     'assinado'
        // );

        // Regenerar PDF com assinatura digital
        try {
            $this->regenerarPDFAtualizado($proposicao);
        } catch (\Exception $e) {
            // Log::warning('Falha ao regenerar PDF após assinatura', [
            //     'proposicao_id' => $proposicao->id,
            //     'error' => $e->getMessage()
            // ]);
        }

        // Enviar automaticamente para protocolo após assinatura
        $proposicao->update([
            'status' => 'enviado_protocolo'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proposição assinada e enviada para protocolo com sucesso!'
        ]);
    }

    /**
     * Reenviar proposição para protocolo (caso necessário)
     */
    public function enviarProtocolo(Proposicao $proposicao)
    {
        // $this->authorize('update', $proposicao);
        
        if (!in_array($proposicao->status, ['assinado', 'protocolado'])) {
            return response()->json([
                'success' => false,
                'message' => 'Proposição deve estar assinada para ser enviada ao protocolo.'
            ], 400);
        }

        $proposicao->update([
            'status' => 'enviado_protocolo'
        ]);

        // TODO: Implementar sistema de tramitação quando disponível
        // $proposicao->adicionarTramitacao(
        //     'Reenviado para protocolo',
        //     $proposicao->status,
        //     'enviado_protocolo'
        // );

        return response()->json([
            'success' => true,
            'message' => 'Proposição reenviada para protocolo!'
        ]);
    }

    /**
     * Salvar correções na proposição devolvida
     */
    public function salvarCorrecoes(Request $request, Proposicao $proposicao)
    {
        // $this->authorize('update', $proposicao);
        
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
    public function reenviarLegislativo(Proposicao $proposicao)
    {
        // $this->authorize('update', $proposicao);
        
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

        // TODO: Implementar sistema de tramitação quando disponível
        // $proposicao->adicionarTramitacao(
        //     'Reenviado para análise legislativa após correções',
        //     'devolvido_correcao',
        //     'enviado_legislativo'
        // );

        return response()->json([
            'success' => true,
            'message' => 'Proposição reenviada para análise legislativa!'
        ]);
    }

    /**
     * Devolver proposição para legislativo com observações
     */
    public function devolverLegislativo(Request $request, Proposicao $proposicao)
    {
        // $this->authorize('update', $proposicao);
        
        $request->validate([
            'observacoes' => 'required|string|min:10',
        ], [
            'observacoes.required' => 'É obrigatório informar o motivo da devolução.',
            'observacoes.min' => 'As observações devem ter pelo menos 10 caracteres.'
        ]);

        if (!in_array($proposicao->status, ['aprovado_assinatura', 'retornado_legislativo'])) {
            return response()->json([
                'success' => false,
                'message' => 'Proposição não está disponível para devolução.'
            ], 400);
        }

        // Atualizar status e salvar observações
        $proposicao->update([
            'status' => 'devolvido_correcao',
            'observacoes_retorno' => $request->observacoes,
            'data_retorno_legislativo' => now(),
        ]);

        // Log::info('Proposição devolvida para legislativo', [
            //     'proposicao_id' => $proposicao->id,
            //     'autor_devolucao' => Auth::user()->name,
            //     'observacoes' => $request->observacoes
        // ]);

        // TODO: Implementar sistema de tramitação quando disponível
        // $proposicao->adicionarTramitacao(
        //     'Devolvido pelo parlamentar para correções',
        //     $proposicao->status,
        //     'devolvido_correcao'
        // );

        return response()->json([
            'success' => true,
            'message' => 'Proposição devolvida para o Legislativo com sucesso!',
            'redirect' => route('proposicoes.assinatura')
        ]);
    }

    /**
     * Histórico de assinaturas do parlamentar
     */
    public function historico()
    {
        $proposicoes = Proposicao::where('autor_id', Auth::id())
            ->whereNotNull('data_assinatura')
            ->orderBy('data_assinatura', 'desc')
            ->paginate(15);

        return view('proposicoes.assinatura.historico', compact('proposicoes'));
    }

    /**
     * Gerar PDF para assinatura se não existir
     */
    private function gerarPDFParaAssinatura(Proposicao $proposicao): void
    {
        // Determinar nome do PDF
        $nomePdf = 'proposicao_' . $proposicao->id . '.pdf';
        $diretorioPdf = 'proposicoes/pdfs/' . $proposicao->id;
        $caminhoPdfRelativo = $diretorioPdf . '/' . $nomePdf;
        $caminhoPdfAbsoluto = storage_path('app/' . $caminhoPdfRelativo);

        // Garantir que o diretório existe
        if (!is_dir(dirname($caminhoPdfAbsoluto))) {
            mkdir(dirname($caminhoPdfAbsoluto), 0755, true);
        }

        // SEMPRE usar o método que prioriza arquivo editado pelo Legislativo
        // (remove verificação de arquivo para garantir regeneração)
        $this->criarPDFDoArquivoEditado($caminhoPdfAbsoluto, $proposicao);
        
        // Atualizar proposição com caminho do PDF
        $proposicao->arquivo_pdf_path = $caminhoPdfRelativo;
        $proposicao->save();
        
        return;
    }

    /**
     * Verificar se LibreOffice está disponível
     */
    private function libreOfficeDisponivel(): bool
    {
        exec('which libreoffice', $output, $returnCode);
        return $returnCode === 0;
    }

    /**
     * Criar PDF prioritariamente do arquivo editado pelo Legislativo
     */
    private function criarPDFDoArquivoEditado(string $caminhoPdfAbsoluto, Proposicao $proposicao): void
    {
        try {
            // PRIORIDADE 1: Converter DOCX editado pelo Legislativo diretamente para PDF (mantém formatação)
            // Para proposições aprovadas para assinatura, SEMPRE priorizar arquivo editado
            if ($proposicao->arquivo_path && in_array($proposicao->status, ['aprovado_assinatura', 'retornado_legislativo'])) {
                $arquivoPath = $proposicao->arquivo_path;
                
                // Buscar arquivo em múltiplos locais (resolver problema do disk)
                $locaisParaBuscar = [
                    storage_path('app/' . $arquivoPath),              // local disk
                    storage_path('app/private/' . $arquivoPath),       // private disk
                    storage_path('app/public/' . $arquivoPath),        // public disk
                    '/var/www/html/storage/app/' . $arquivoPath,       // container path
                    '/var/www/html/storage/app/private/' . $arquivoPath, // container private
                    '/var/www/html/storage/app/public/' . $arquivoPath   // container public
                ];
                
                $arquivoEncontrado = null;
                foreach ($locaisParaBuscar as $caminho) {
                    if (file_exists($caminho)) {
                        $arquivoEncontrado = $caminho;
                        break;
                    }
                }
                
                // Log para debug
                if ($arquivoEncontrado) {
                    // Log::info("PDF Assinatura: Arquivo encontrado para proposição {$proposicao->id}: $arquivoEncontrado");
                } else {
                    // Log::warning("PDF Assinatura: ARQUIVO NÃO ENCONTRADO para proposição {$proposicao->id} em {$arquivoPath}");
                }
                
                // Se encontrou arquivo DOCX, tentar converter diretamente com LibreOffice
                if ($arquivoEncontrado && str_contains($arquivoPath, '.docx') && $this->libreOfficeDisponivel()) {
                    try {
                        // Criar arquivo temporário se necessário para garantir localização correta
                        $tempDir = sys_get_temp_dir();
                        $tempFile = $tempDir . '/proposicao_' . $proposicao->id . '_temp.docx';
                        copy($arquivoEncontrado, $tempFile);
                        
                        // Comando LibreOffice para conversão direta DOCX -> PDF (mantém formatação)
                        $comando = sprintf(
                            'libreoffice --headless --invisible --nodefault --nolockcheck --nologo --norestore --convert-to pdf --outdir %s %s 2>/dev/null',
                            escapeshellarg(dirname($caminhoPdfAbsoluto)),
                            escapeshellarg($tempFile)
                        );
                        
                        exec($comando, $output, $returnCode);
                        
                        // Verificar se PDF foi criado
                        $expectedPdfPath = dirname($caminhoPdfAbsoluto) . '/' . pathinfo($tempFile, PATHINFO_FILENAME) . '.pdf';
                        
                        // Log::info("PDF Assinatura: Comando executado: $comando");
                        // Log::info("PDF Assinatura: Return code: $returnCode");
                        
                        if ($returnCode === 0 && file_exists($expectedPdfPath)) {
                            // Mover PDF para localização final
                            rename($expectedPdfPath, $caminhoPdfAbsoluto);
                            
                            // Limpar arquivo temporário
                            if (file_exists($tempFile)) {
                                unlink($tempFile);
                            }
                            
                            // Log::info("PDF Assinatura: PDF criado com SUCESSO do DOCX editado pelo Legislativo! Proposição {$proposicao->id}");
                            
                            return; // Sucesso! PDF criado com formatação preservada
                        } else {
                            // Log::warning("PDF Assinatura: FALHA na conversão LibreOffice. ReturnCode: $returnCode");
                        }
                        
                        // Limpar arquivo temporário em caso de erro
                        if (file_exists($tempFile)) {
                            unlink($tempFile);
                        }
                        
                    } catch (\Exception $e) {
                        // Log::warning('Falha na conversão direta DOCX->PDF, usando método fallback', [
                        //     'proposicao_id' => $proposicao->id,
                        //     'arquivo_path' => $arquivoEncontrado,
                        //     'error' => $e->getMessage()
                        // ]);
                    }
                }
            }
            
            // FALLBACK: Se conversão direta falhou, usar método anterior (extração de texto)
            error_log("PDF Assinatura: Usando método FALLBACK para proposição {$proposicao->id}");
            $this->criarPDFFallback($caminhoPdfAbsoluto, $proposicao);

        } catch (\Exception $e) {
            // Log::error('Erro ao criar PDF', [
                //     'proposicao_id' => $proposicao->id,
                //     'error' => $e->getMessage()
            // ]);
            throw $e;
        }
    }
    
    /**
     * Método fallback para criar PDF quando conversão direta falha
     * CORRIGIDO: Usa mesma lógica do ProposicaoController para manter consistência
     */
    private function criarPDFFallback(string $caminhoPdfAbsoluto, Proposicao $proposicao): void
    {
        try {
            // USAR MESMA LÓGICA DO ProposicaoController::criarPDFComDomPDF
            // Para manter consistência e evitar template genérico
            
            $conteudo = '';
            
            // 1. Verificar se existe arquivo editado pelo Legislativo
            if ($proposicao->arquivo_path) {
                $caminhoArquivo = null;
                
                // Tentar encontrar o arquivo editado (RTF/DOCX do OnlyOffice)
                $possiveisCaminhos = [
                    storage_path('app/' . $proposicao->arquivo_path),
                    storage_path('app/private/' . $proposicao->arquivo_path),
                    storage_path('app/proposicoes/' . basename($proposicao->arquivo_path)),
                    '/var/www/html/storage/app/' . $proposicao->arquivo_path,
                    '/var/www/html/storage/app/private/' . $proposicao->arquivo_path
                ];
                
                foreach ($possiveisCaminhos as $caminho) {
                    if (file_exists($caminho)) {
                        $caminhoArquivo = $caminho;
                        break;
                    }
                }
                
                // Se encontrou arquivo RTF/DOCX editado, extrair conteúdo real
                if ($caminhoArquivo) {
                    if (str_contains($proposicao->arquivo_path, '.rtf')) {
                        // Arquivo RTF do OnlyOffice - extrair texto real
                        $rtfContent = file_get_contents($caminhoArquivo);
                        $conteudo = $this->converterRTFParaTexto($rtfContent);
                        error_log("PDF Assinatura: Conteúdo extraído do RTF editado: " . strlen($conteudo) . " caracteres");
                    } elseif (str_contains($proposicao->arquivo_path, '.docx')) {
                        // Arquivo DOCX - usar DocumentExtractionService
                        $extractionService = app(\App\Services\DocumentExtractionService::class);
                        try {
                            $conteudo = $extractionService->extractTextFromDocxFile($caminhoArquivo);
                            error_log("PDF Assinatura: Conteúdo extraído do DOCX: " . strlen($conteudo) . " caracteres");
                        } catch (\Exception $e) {
                            error_log("PDF Assinatura: Erro ao extrair DOCX: " . $e->getMessage());
                        }
                    }
                }
            }
            
            // 2. Se não conseguiu extrair do arquivo, usar conteúdo do banco
            if (empty($conteudo) || strlen($conteudo) < 50) {
                if (!empty($proposicao->conteudo)) {
                    $conteudo = $proposicao->conteudo;
                    error_log("PDF Assinatura: Usando conteúdo do banco de dados");
                } else {
                    $conteudo = $proposicao->ementa ?: 'Conteúdo não disponível';
                    error_log("PDF Assinatura: Usando ementa como fallback");
                }
            }

            // 3. Criar HTML usando mesmo método do ProposicaoController
            $html = $this->gerarHTMLParaPDF($proposicao, $conteudo);

            // 4. Usar DomPDF para gerar PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            
            // 5. Salvar PDF
            file_put_contents($caminhoPdfAbsoluto, $pdf->output());
            
            error_log("PDF Assinatura: PDF criado com sucesso! Tamanho: " . filesize($caminhoPdfAbsoluto) . " bytes");

        } catch (\Exception $e) {
            error_log("PDF Assinatura: Erro ao criar PDF fallback: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Substituir placeholders no conteúdo extraído com valores reais
     */
    private function substituirPlaceholders(string $conteudo, Proposicao $proposicao): string
    {
        // Substituir número de protocolo
        if ($proposicao->numero_protocolo) {
            $conteudo = str_replace('[AGUARDANDO PROTOCOLO]', $proposicao->numero_protocolo, $conteudo);
        }
        
        // Substituir outras variáveis comuns
        $substituicoes = [
            '${numero_proposicao}' => $proposicao->numero_protocolo ?: '[AGUARDANDO PROTOCOLO]',
            '${ementa}' => $proposicao->ementa,
            '${texto}' => $proposicao->conteudo,
            '${autor_nome}' => $proposicao->autor->name ?? 'N/A',
            '${autor_cargo}' => $proposicao->autor->cargo_atual ?? 'Parlamentar',
            '${municipio}' => 'Caraguatatuba',
            '${dia}' => now()->format('d'),
            '${mes_extenso}' => $this->getMesExtenso(now()->month),
            '${ano_atual}' => now()->format('Y'),
        ];
        
        foreach ($substituicoes as $placeholder => $valor) {
            $conteudo = str_replace($placeholder, $valor, $conteudo);
        }
        
        return $conteudo;
    }
    
    /**
     * Obter nome do mês por extenso
     */
    private function getMesExtenso(int $mes): string
    {
        $meses = [
            1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
            5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
            9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro'
        ];
        
        return $meses[$mes] ?? 'indefinido';
    }

    /**
     * Regenerar PDF com dados atualizados (protocolo, assinatura, etc.)
     * Método público para ser usado após atribuir protocolo ou assinar
     */
    public function regenerarPDFAtualizado(Proposicao $proposicao): void
    {
        try {
            $this->gerarPDFParaAssinatura($proposicao);
            
            // Log::info('PDF regenerado com dados atualizados', [
            //     'proposicao_id' => $proposicao->id,
            //     'numero_protocolo' => $proposicao->numero_protocolo,
            //     'assinada' => !empty($proposicao->assinatura_digital)
            // ]);
            
        } catch (\Exception $e) {
            // Log::error('Erro ao regenerar PDF atualizado', [
            //     'proposicao_id' => $proposicao->id,
            //     'error' => $e->getMessage()
            // ]);
            
            throw $e;
        }
    }
    
    /**
     * Converter RTF para texto limpo removendo códigos RTF
     * ADICIONADO: Método copiado do ProposicaoController para manter consistência
     */
    private function converterRTFParaTexto(string $rtfContent): string
    {
        // Se não é RTF, retornar como está
        if (!str_contains($rtfContent, '{\rtf')) {
            return $rtfContent;
        }
        
        // Para RTF muito complexo como do OnlyOffice, usar abordagem simplificada:
        // Buscar por texto real entre códigos RTF usando padrões específicos
        
        $textosEncontrados = [];
        
        // 1. Buscar texto em português comum (frases)
        preg_match_all('/(?:[A-ZÁÉÍÓÚÂÊÎÔÛÃÕÀÈÌÒÙÇ][a-záéíóúâêîôûãõàèìòùç\s,.-]{15,})/u', $rtfContent, $matches);
        if (!empty($matches[0])) {
            foreach ($matches[0] as $match) {
                // Limpar RTF restante
                $clean = preg_replace('/\\\\\w+\d*\s*/', ' ', $match);
                $clean = preg_replace('/[{}\\\\]/', '', $clean);
                $clean = trim($clean);
                if (strlen($clean) > 10) {
                    $textosEncontrados[] = $clean;
                }
            }
        }
        
        // 2. Se achou textos válidos, juntar e retornar
        if (!empty($textosEncontrados)) {
            $textoFinal = implode("\n\n", $textosEncontrados);
            // Limpar caracteres especiais restantes
            $textoFinal = preg_replace('/\s+/', ' ', $textoFinal);
            return trim($textoFinal);
        }
        
        // 3. Fallback: busca mais agressiva por qualquer texto legível
        preg_match_all('/[A-Za-záéíóúâêîôûãõàèìòùç\s]{10,}/', $rtfContent, $fallbackMatches);
        if (!empty($fallbackMatches[0])) {
            $texto = implode(' ', $fallbackMatches[0]);
            return trim(preg_replace('/\s+/', ' ', $texto));
        }
        
        return '';
    }
    
    /**
     * Gerar HTML para PDF usando mesmo layout do ProposicaoController
     * ADICIONADO: Para manter consistência visual
     */
    private function gerarHTMLParaPDF(Proposicao $proposicao, string $conteudo): string
    {
        return "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <title>Proposição {$proposicao->id}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
                .title { font-size: 18px; font-weight: bold; margin: 10px 0; }
                .info { font-size: 12px; color: #666; margin: 5px 0; }
                .content { margin-top: 30px; text-align: justify; }
                .ementa { background: #f5f5f5; padding: 15px; margin: 20px 0; border-left: 4px solid #007bff; }
                .signature-area { margin-top: 50px; text-align: right; }
                .signature-line { border-top: 1px solid #333; margin-top: 50px; width: 300px; display: inline-block; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>CÂMARA MUNICIPAL DE CARAGUATATUBA</h1>
                <div class='title'>" . strtoupper($proposicao->tipo) . 
                ($proposicao->numero_protocolo ? " Nº {$proposicao->numero_protocolo}" : " #" . $proposicao->id . " (Aguardando Protocolo)") . "</div>
                <div class='info'>Autor: " . ($proposicao->autor->name ?? 'N/A') . "</div>
                <div class='info'>Data: " . $proposicao->created_at->format('d/m/Y') . "</div>
            </div>
            
            <div class='ementa'>
                <strong>EMENTA:</strong><br>
                " . nl2br(htmlspecialchars($proposicao->ementa)) . "
            </div>
            
            <div class='content'>
                " . nl2br(htmlspecialchars($conteudo ?: 'Conteúdo não disponível')) . "
            </div>
            
            <div class='signature-area'>
                <p>Caraguatatuba, " . now()->format('d') . " de " . $this->getMesExtenso(now()->month) . " de " . now()->format('Y') . ".</p>
                <div class='signature-line'></div>
                <p>" . ($proposicao->autor->name ?? 'Autor da Proposição') . "<br>" . ($proposicao->autor->cargo_atual ?? 'Vereador') . "</p>
            </div>
        </body>
        </html>";
    }
}