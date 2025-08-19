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
        
        if (!in_array($proposicao->status, ['aprovado', 'aprovado_assinatura', 'retornado_legislativo'])) {
            abort(403, 'Proposição não está disponível para assinatura.');
        }

        // Verificar se já existe PDF válido recente (evitar regeneração desnecessária)
        $precisaRegerarPDF = $this->precisaRegerarPDF($proposicao);
        
        if ($precisaRegerarPDF) {
            try {
                $this->gerarPDFParaAssinatura($proposicao);
            } catch (\Exception $e) {
                // Se falhar, tentar usar PDF existente como fallback
                $pdfPath = $proposicao->arquivo_pdf_path ? storage_path('app/' . $proposicao->arquivo_pdf_path) : null;
                if (!$proposicao->arquivo_pdf_path || !file_exists($pdfPath)) {
                    // Se nem o PDF existente serve, mostrar erro
                    return back()->withErrors(['pdf' => 'Não foi possível gerar o PDF para assinatura: ' . $e->getMessage()]);
                }
            }
        }

        return view('proposicoes.assinatura.assinar-vue', compact('proposicao'));
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
     * Gerar PDF para assinatura sempre com a versão mais recente
     */
    private function gerarPDFParaAssinatura(Proposicao $proposicao): void
    {
        // IMPORTANTE: Sempre regenerar PDF para garantir versão mais recente
        $nomePdf = 'proposicao_' . $proposicao->id . '_assinatura_' . time() . '.pdf';
        $diretorioPdf = 'proposicoes/pdfs/' . $proposicao->id;
        $caminhoPdfRelativo = $diretorioPdf . '/' . $nomePdf;
        $caminhoPdfAbsoluto = storage_path('app/' . $caminhoPdfRelativo);

        // Garantir que o diretório existe
        if (!is_dir(dirname($caminhoPdfAbsoluto))) {
            mkdir(dirname($caminhoPdfAbsoluto), 0755, true);
        }

        // Log para debug
        error_log("PDF Assinatura: Iniciando geração de PDF para proposição {$proposicao->id}");
        error_log("PDF Assinatura: Status atual: {$proposicao->status}");
        error_log("PDF Assinatura: Arquivo path no banco: " . ($proposicao->arquivo_path ?: 'NULL'));
        
        // SEMPRE buscar a versão mais recente do arquivo
        $this->criarPDFDoArquivoMaisRecente($caminhoPdfAbsoluto, $proposicao);
        
        // Atualizar proposição com caminho do PDF
        $proposicao->arquivo_pdf_path = $caminhoPdfRelativo;
        $proposicao->save();
        
        // Limpar PDFs antigos (manter apenas os 3 mais recentes)
        $this->limparPDFsAntigos($proposicao->id);
        
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
     * Criar PDF sempre com a versão mais recente do documento
     * Busca nos diretórios de storage por ordem de prioridade
     */
    private function criarPDFDoArquivoMaisRecente(string $caminhoPdfAbsoluto, Proposicao $proposicao): void
    {
        try {
            // ESTRATÉGIA MELHORADA: Buscar SEMPRE o arquivo mais recente
            $arquivoMaisRecente = $this->encontrarArquivoMaisRecente($proposicao);
            
            if ($arquivoMaisRecente) {
                error_log("PDF Assinatura: Arquivo mais recente encontrado: {$arquivoMaisRecente['path']}");
                error_log("PDF Assinatura: Modificado em: {$arquivoMaisRecente['modified']}");
                error_log("PDF Assinatura: Tamanho: {$arquivoMaisRecente['size']} bytes");
                
                $arquivoEncontrado = $arquivoMaisRecente['path'];
                $arquivoPath = $arquivoMaisRecente['relative_path'];
                
                // Extrair conteúdo do arquivo mais recente
                if (str_contains($arquivoEncontrado, '.docx')) {
                    try {
                        $conteudo = $this->extrairConteudoDOCX($arquivoEncontrado);
                        error_log("PDF Assinatura: Conteúdo extraído do arquivo mais recente: " . strlen($conteudo) . " caracteres");
                        if (!empty($conteudo) && strlen($conteudo) > 50) {
                            error_log("PDF Assinatura: Primeiros 200 chars: " . substr($conteudo, 0, 200));
                        }
                    } catch (\Exception $e) {
                        error_log("PDF Assinatura: Erro ao extrair do arquivo mais recente: " . $e->getMessage());
                    }
                } elseif (str_contains($arquivoEncontrado, '.rtf')) {
                    try {
                        $rtfContent = file_get_contents($arquivoEncontrado);
                        $conteudo = $this->converterRTFParaTexto($rtfContent);
                        error_log("PDF Assinatura: Conteúdo extraído do RTF mais recente: " . strlen($conteudo) . " caracteres");
                    } catch (\Exception $e) {
                        error_log("PDF Assinatura: Erro ao extrair RTF: " . $e->getMessage());
                    }
                }
                
                // NOVA ABORDAGEM: Converter DOCX → HTML para preservar formatação do OnlyOffice
                if ($arquivoEncontrado && str_contains($arquivoPath, '.docx') && $this->libreOfficeDisponivel()) {
                    try {
                        error_log("PDF Assinatura: Convertendo DOCX → HTML para preservar formatação OnlyOffice");
                        $this->criarPDFComFormatacaoOnlyOffice($caminhoPdfAbsoluto, $proposicao, $arquivoEncontrado);
                        return; // Sucesso! PDF criado com formatação preservada
                    } catch (\Exception $e) {
                        error_log("PDF Assinatura: Falha na conversão HTML, usando extração de texto: " . $e->getMessage());
                        // Continua para método de extração de texto como fallback
                    }
                }
                
                // FALLBACK: Extrair apenas texto se conversão HTML falhou
            } else {
                error_log("PDF Assinatura: Nenhum arquivo encontrado, usando conteúdo do banco de dados");
            }
            
            // Método principal: Extrair conteúdo e gerar PDF com assinatura digital
            error_log("PDF Assinatura: Extraindo conteúdo do DOCX editado para proposição {$proposicao->id}");
            $this->criarPDFComConteudoExtraido($caminhoPdfAbsoluto, $proposicao);

        } catch (\Exception $e) {
            // Log::error('Erro ao criar PDF', [
                //     'proposicao_id' => $proposicao->id,
                //     'error' => $e->getMessage()
            // ]);
            throw $e;
        }
    }
    
    /**
     * Criar PDF extraindo conteúdo do DOCX editado e adicionando assinatura digital
     * Mantém formatação próxima ao OnlyOffice mas permite modificar conteúdo
     */
    private function criarPDFComConteudoExtraido(string $caminhoPdfAbsoluto, Proposicao $proposicao): void
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
                        // Arquivo DOCX - usar método direto mais robusto
                        try {
                            $conteudo = $this->extrairConteudoDOCX($caminhoArquivo);
                            error_log("PDF Assinatura: Conteúdo extraído do DOCX: " . strlen($conteudo) . " caracteres");
                            if (!empty($conteudo)) {
                                error_log("PDF Assinatura: Primeiros 200 chars: " . substr($conteudo, 0, 200));
                            }
                        } catch (\Exception $e) {
                            error_log("PDF Assinatura: Erro ao extrair DOCX: " . $e->getMessage());
                        }
                    }
                }
            }
            
            // 2. Se não conseguiu extrair do arquivo ou conteúdo muito pequeno, usar conteúdo do banco
            if (empty($conteudo) || strlen($conteudo) < 50) {
                error_log("PDF Assinatura: Conteúdo extraído insuficiente (" . strlen($conteudo) . " chars), tentando banco de dados");
                
                if (!empty($proposicao->conteudo)) {
                    $conteudo = $proposicao->conteudo;
                    error_log("PDF Assinatura: Usando conteúdo do banco de dados (" . strlen($conteudo) . " chars)");
                } else {
                    $conteudo = $proposicao->ementa ?: 'Conteúdo não disponível';
                    error_log("PDF Assinatura: Usando ementa como fallback");
                }
            } else {
                error_log("PDF Assinatura: Usando conteúdo extraído do arquivo (" . strlen($conteudo) . " chars)");
            }

            // 3. Substituir placeholders no conteúdo (incluindo assinatura digital)
            $conteudo = $this->substituirPlaceholders($conteudo, $proposicao);

            // 4. Criar HTML usando mesmo método do ProposicaoController
            $html = $this->gerarHTMLParaPDF($proposicao, $conteudo);

            // 5. Usar DomPDF para gerar PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            
            // 6. Salvar PDF
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
        
        // A assinatura digital será processada separadamente no HTML final
        // Não inserir HTML no conteúdo de texto
        
        // Substituir outras variáveis comuns (suporta ambos os formatos: ${var} e var)
        $substituicoes = [
            '${numero_proposicao}' => $proposicao->numero_protocolo ?: '[AGUARDANDO PROTOCOLO]',
            'numero_proposicao' => $proposicao->numero_protocolo ?: '[AGUARDANDO PROTOCOLO]',
            '${ementa}' => $proposicao->ementa,
            'ementa' => $proposicao->ementa,
            '${texto}' => $proposicao->conteudo,
            'texto' => $proposicao->conteudo,
            '${autor_nome}' => $proposicao->autor->name ?? 'N/A',
            'autor_nome' => $proposicao->autor->name ?? 'N/A',
            '${autor_cargo}' => $proposicao->autor->cargo_atual ?? 'Parlamentar',
            'autor_cargo' => $proposicao->autor->cargo_atual ?? 'Parlamentar',
            '${municipio}' => 'Caraguatatuba',
            'municipio' => 'Caraguatatuba',
            '${dia}' => now()->format('d'),
            'dia' => now()->format('d'),
            '${mes_extenso}' => $this->getMesExtenso(now()->month),
            'mes_extenso' => $this->getMesExtenso(now()->month),
            '08_extenso' => $this->getMesExtenso(now()->month), // Formato específico do template
            '${ano_atual}' => now()->format('Y'),
            'ano_atual' => now()->format('Y'),
            '2025_atual' => now()->format('Y'), // Formato específico do template
            '${assinatura_digital_info}' => '', // Remover placeholder, será adicionado como HTML
            'assinatura_digital_info' => '', // Remover placeholder, será adicionado como HTML
            '${qrcode_html}' => '', // QR Code não implementado ainda
            'qrcode_html' => '', // QR Code não implementado ainda
            '${data_assinatura}' => $proposicao->data_assinatura ? $proposicao->data_assinatura->format('d/m/Y H:i:s') : '',
            'data_assinatura' => $proposicao->data_assinatura ? $proposicao->data_assinatura->format('d/m/Y H:i:s') : '',
            '${certificado_digital}' => $proposicao->certificado_digital ?: '',
            'certificado_digital' => $proposicao->certificado_digital ?: '',
            '${ip_assinatura}' => $proposicao->ip_assinatura ?: '',
            'ip_assinatura' => $proposicao->ip_assinatura ?: '',
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
     * Gerar HTML para PDF preservando aparência do documento OnlyOffice
     * Remove elementos de "template padrão" para parecer com documento editado
     */
    private function gerarHTMLParaPDF(Proposicao $proposicao, string $conteudo): string
    {
        // Gerar cabeçalho com dados da câmara e número da proposição
        $templateVariableService = app(\App\Services\Template\TemplateVariableService::class);
        $variables = $templateVariableService->getTemplateVariables();
        
        // Obter número da proposição (com protocolo ou aguardando)
        $numeroProposicao = $proposicao->numero_protocolo ?: '[AGUARDANDO PROTOCOLO]';
        
        // Gerar cabeçalho com imagem se disponível
        $headerHTML = '';
        if (!empty($variables['cabecalho_imagem'])) {
            $imagePath = public_path($variables['cabecalho_imagem']);
            if (file_exists($imagePath)) {
                $imageData = base64_encode(file_get_contents($imagePath));
                $mimeType = mime_content_type($imagePath);
                $headerHTML = '<div style="text-align: center; margin-bottom: 20px;">
                    <img src="data:' . $mimeType . ';base64,' . $imageData . '" 
                         style="max-width: 200px; height: auto;" alt="Cabeçalho" />
                </div>';
            }
        }
        
        // Cabeçalho da câmara
        $cabeçalhoTexto = "
        <div style='text-align: center; margin-bottom: 20px;'>
            <strong>{$variables['cabecalho_nome_camara']}</strong><br>
            {$variables['cabecalho_endereco']}<br>
            {$variables['cabecalho_telefone']}<br>
            {$variables['cabecalho_website']}
        </div>";
        
        // Título do documento com número da proposição
        $tipoUppercase = strtoupper($proposicao->tipo);
        $tituloHTML = "
        <div style='text-align: center; margin: 20px 0;'>
            <strong>{$tipoUppercase} Nº {$numeroProposicao}</strong>
        </div>";
        
        // Ementa se disponível
        $ementaHTML = '';
        if ($proposicao->ementa) {
            $ementaHTML = "
            <div style='margin: 20px 0;'>
                <strong>EMENTA:</strong> {$proposicao->ementa}
            </div>";
        }
        
        // Gerar informações da assinatura digital se disponível
        $assinaturaDigitalHTML = '';
        if ($proposicao->assinatura_digital && $proposicao->data_assinatura) {
            $assinaturaQRService = app(\App\Services\Template\AssinaturaQRService::class);
            $assinaturaDigitalHTML = $assinaturaQRService->gerarHTMLAssinatura($proposicao) ?: '';
        }
        
        // Separar conteúdo de texto puro da assinatura HTML
        $conteudoTexto = $conteudo ?: 'Conteúdo não disponível';
        
        // Limpar restos de placeholders e HTML que possam estar no conteúdo
        $conteudoTexto = $this->limparConteudoParaPDF($conteudoTexto);
        
        return "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <title>Proposição {$proposicao->id}</title>
            <style>
                body { 
                    font-family: 'Times New Roman', serif; 
                    margin: 2.5cm 2cm 2cm 2cm; 
                    line-height: 1.8; 
                    font-size: 12pt;
                    color: #000;
                    text-align: justify;
                }
                .header { 
                    text-align: center; 
                    margin-bottom: 30px; 
                }
                .document-content { 
                    white-space: pre-wrap; 
                    margin: 20px 0; 
                    padding: 0;
                }
                .assinatura-digital { 
                    border: 1px solid #28a745; 
                    padding: 10px; 
                    margin: 20px 0; 
                    background-color: #f8f9fa;
                    font-family: Arial, sans-serif;
                    font-size: 10pt;
                }
                .assinatura-digital h6 { 
                    color: #28a745; 
                    margin-bottom: 5px; 
                    font-size: 11pt;
                }
                .assinatura-digital div { 
                    font-size: 10pt; 
                    line-height: 1.4; 
                }
                .digital-signature-section {
                    margin-top: 30px;
                    page-break-inside: avoid;
                }
            </style>
        </head>
        <body>
            {$headerHTML}
            {$cabeçalhoTexto}
            {$tituloHTML}
            {$ementaHTML}
            <div class='document-content'>" . nl2br(htmlspecialchars($conteudoTexto)) . "</div>
            " . ($assinaturaDigitalHTML ? "<div class='digital-signature-section'>" . $assinaturaDigitalHTML . "</div>" : "") . "
        </body>
        </html>";
    }
    
    /**
     * Limpar conteúdo para PDF removendo placeholders e restos de HTML
     */
    private function limparConteudoParaPDF(string $conteudo): string
    {
        // Remover tags HTML que possam estar como texto
        $conteudo = preg_replace('/<[^>]*>/', '', $conteudo);
        
        // Remover placeholders vazios restantes
        $conteudo = str_replace(['${', '}'], '', $conteudo);
        
        // Remover espaços extras e quebras de linha desnecessárias
        $conteudo = preg_replace('/\s+/', ' ', $conteudo);
        $conteudo = trim($conteudo);
        
        return $conteudo;
    }
    
    /**
     * Criar PDF preservando formatação do OnlyOffice via conversão DOCX → HTML → PDF
     */
    private function criarPDFComFormatacaoOnlyOffice(string $caminhoPdfAbsoluto, Proposicao $proposicao, string $arquivoPath): void
    {
        try {
            // 1. Converter DOCX → HTML usando LibreOffice (preserva formatação)
            $tempDir = sys_get_temp_dir();
            $tempFile = $tempDir . '/proposicao_' . $proposicao->id . '_html.docx';
            $outputDir = $tempDir . '/html_output_' . $proposicao->id;
            
            // Criar diretório de saída
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }
            
            // Copiar arquivo DOCX
            copy($arquivoPath, $tempFile);
            
            // Comando LibreOffice para conversão DOCX → HTML
            $comando = sprintf(
                'libreoffice --headless --invisible --nodefault --nolockcheck --nologo --norestore --convert-to html --outdir %s %s 2>/dev/null',
                escapeshellarg($outputDir),
                escapeshellarg($tempFile)
            );
            
            exec($comando, $output, $returnCode);
            
            $htmlPath = $outputDir . '/' . pathinfo($tempFile, PATHINFO_FILENAME) . '.html';
            
            error_log("PDF Assinatura: LibreOffice HTML conversion - return code: {$returnCode}, HTML exists: " . (file_exists($htmlPath) ? 'YES' : 'NO'));
            
            if ($returnCode !== 0 || !file_exists($htmlPath)) {
                error_log("PDF Assinatura: LibreOffice HTML conversion failed - return code: {$returnCode}, output: " . implode(", ", $output));
                throw new \Exception("LibreOffice HTML conversion failed: return code {$returnCode}");
            }
            
            // 2. Ler HTML gerado e fazer correção inicial de números duplicados
            $htmlContent = file_get_contents($htmlPath);
            
            // Correção prévia de números duplicados que podem vir do template OnlyOffice
            if ($proposicao->numero_protocolo) {
                // Se tem protocolo, substituir [AGUARDANDO PROTOCOLO] pelo número
                $htmlContent = str_replace('[AGUARDANDO PROTOCOLO]', $proposicao->numero_protocolo, $htmlContent);
                // Corrigir qualquer duplicação de ano que possa ter sido criada
                // Padrão específico: mocao/2025/0001/2025 → mocao/2025/0001
                $htmlContent = preg_replace('/(mocao\/\d{4}\/\d{4})\/\d{4}/', '$1', $htmlContent);
                // Padrão genérico: 001/2025/2025 → 001/2025
                $htmlContent = preg_replace('/(\d{3,4}\/\d{4})\/\d{4}/', '$1', $htmlContent);
                // Correção específica para o problema atual
                $htmlContent = str_replace('mocao/2025/0001/2025', 'mocao/2025/0001', $htmlContent);
            } else {
                // Se não tem protocolo, corrigir padrão "[AGUARDANDO PROTOCOLO]/2025" para só "[AGUARDANDO PROTOCOLO]"
                $htmlContent = str_replace('[AGUARDANDO PROTOCOLO]/2025', '[AGUARDANDO PROTOCOLO]', $htmlContent);
                $htmlContent = str_replace('[AGUARDANDO PROTOCOLO]/' . date('Y'), '[AGUARDANDO PROTOCOLO]', $htmlContent);
            }
            
            $htmlContent = $this->otimizarHTMLParaDomPDF($htmlContent);
            $htmlContent = $this->adicionarAssinaturaAoHTML($htmlContent, $proposicao);
            
            // 3. Processar variáveis no HTML
            $htmlContent = $this->substituirVariaveisNoHTML($htmlContent, $proposicao);
            
            // 4. CORREÇÃO FINAL GLOBAL: Limpar qualquer duplicação restante
            $htmlContent = str_replace('mocao/2025/0001/2025', 'mocao/2025/0001', $htmlContent);
            $htmlContent = preg_replace('/(\w+\/\d{4}\/\d{4})\/\d{4}/', '$1', $htmlContent);
            
            // 5. Converter HTML → PDF usando DomPDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($htmlContent);
            $pdf->setPaper('A4', 'portrait');
            
            // 5. Garantir que diretório de destino existe
            if (!is_dir(dirname($caminhoPdfAbsoluto))) {
                mkdir(dirname($caminhoPdfAbsoluto), 0755, true);
            }
            
            // 6. Salvar PDF
            file_put_contents($caminhoPdfAbsoluto, $pdf->output());
            
            error_log("PDF Assinatura: PDF criado com formatação OnlyOffice preservada! Tamanho: " . filesize($caminhoPdfAbsoluto) . " bytes");
            
            // Limpeza
            if (file_exists($tempFile)) unlink($tempFile);
            if (file_exists($htmlPath)) unlink($htmlPath);
            // Tentar remover diretório (pode falhar se não vazio, mas não é crítico)
            if (is_dir($outputDir)) @rmdir($outputDir);
            
        } catch (\Exception $e) {
            // Limpeza em caso de erro (usar @ para suprimir warnings)
            if (isset($tempFile) && file_exists($tempFile)) @unlink($tempFile);
            if (isset($htmlPath) && file_exists($htmlPath)) @unlink($htmlPath);
            if (isset($outputDir) && is_dir($outputDir)) @rmdir($outputDir);
            
            throw $e;
        }
    }
    
    /**
     * Adicionar assinatura digital ao HTML preservando formatação original
     */
    private function adicionarAssinaturaAoHTML(string $htmlContent, Proposicao $proposicao): string
    {
        // Processar imagem do cabeçalho se estiver faltando
        if (!str_contains($htmlContent, '<img') || !str_contains($htmlContent, 'cabecalho')) {
            // Adicionar imagem do cabeçalho no início do documento se não existir
            $templateVariableService = app(\App\Services\Template\TemplateVariableService::class);
            $variables = $templateVariableService->getTemplateVariables();
            
            if (!empty($variables['cabecalho_imagem'])) {
                $imagePath = public_path($variables['cabecalho_imagem']);
                if (file_exists($imagePath)) {
                    $imageData = base64_encode(file_get_contents($imagePath));
                    $mimeType = mime_content_type($imagePath);
                    $headerImage = '<div style="text-align: center; margin-bottom: 20px;">
                        <img src="data:' . $mimeType . ';base64,' . $imageData . '" 
                             style="max-width: 200px; height: auto;" alt="Cabeçalho" />
                    </div>';
                    
                    // Adicionar após <body> ou no início
                    if (strpos($htmlContent, '<body') !== false) {
                        $htmlContent = preg_replace('/(<body[^>]*>)/i', '$1' . $headerImage, $htmlContent);
                    } else {
                        $htmlContent = $headerImage . $htmlContent;
                    }
                }
            }
        }
        
        // Gerar HTML da assinatura digital
        $assinaturaHTML = '';
        if ($proposicao->assinatura_digital && $proposicao->data_assinatura) {
            $assinaturaQRService = app(\App\Services\Template\AssinaturaQRService::class);
            $assinaturaHTML = $assinaturaQRService->gerarHTMLAssinatura($proposicao) ?: '';
        }
        
        if (empty($assinaturaHTML)) {
            return $htmlContent;
        }
        
        // Adicionar assinatura antes do fechamento do body
        $assinaturaSection = "\n<div class='digital-signature-section' style='margin-top: 30px; page-break-inside: avoid;'>{$assinaturaHTML}</div>\n";
        
        if (strpos($htmlContent, '</body>') !== false) {
            $htmlContent = str_replace('</body>', $assinaturaSection . '</body>', $htmlContent);
        } else {
            // Se não tem </body>, adicionar no final
            $htmlContent .= $assinaturaSection;
        }
        
        return $htmlContent;
    }
    
    /**
     * Substituir variáveis específicas no HTML (como numero_protocolo)
     */
    private function substituirVariaveisNoHTML(string $htmlContent, Proposicao $proposicao): string
    {
        // Buscar padrões de números de proposição no formato "MOÇÃO Nº XXXX/2025" ou similar
        // Se não houver protocolo, substituir por [AGUARDANDO PROTOCOLO]
        if (!$proposicao->numero_protocolo) {
            // Substituir diferentes formatos de número de proposição
            $patterns = [
                '/(\w+\s+Nº\s+)(\d+\/\d{4})/i' => '$1[AGUARDANDO PROTOCOLO]',
                '/(\w+\s+N°\s+)(\d+\/\d{4})/i' => '$1[AGUARDANDO PROTOCOLO]',
                '/(\w+\s+nº\s+)(\d+\/\d{4})/i' => '$1[AGUARDANDO PROTOCOLO]',
                '/(\w+\s+n°\s+)(\d+\/\d{4})/i' => '$1[AGUARDANDO PROTOCOLO]',
            ];
            
            foreach ($patterns as $pattern => $replacement) {
                $htmlContent = preg_replace($pattern, $replacement, $htmlContent);
            }
        } else {
            // Se houver protocolo, garantir que não há duplicação do ano
            $currentYear = date('Y');
            
            // Corrigir padrão específico do número atual: mocao/2025/0001/2025
            $htmlContent = str_replace($proposicao->numero_protocolo . '/' . $currentYear, $proposicao->numero_protocolo, $htmlContent);
            
            // Corrigir outros formatos de duplicação  
            $htmlContent = preg_replace('/(mocao\/\d{4}\/\d{4})\/\d{4}/', '$1', $htmlContent);
            $htmlContent = preg_replace('/(\d{3,4}\/\d{4})\/\d{4}/', '$1', $htmlContent);
            
            // Correções específicas
            $htmlContent = str_replace('mocao/2025/0001/2025', 'mocao/2025/0001', $htmlContent);
            $htmlContent = str_replace('001/2025/2025', '001/2025', $htmlContent);
        }
        
        // Processar assinatura digital e QR Code
        $assinaturaHTML = '';
        $qrcodeHTML = '';
        
        if ($proposicao->assinatura_digital && $proposicao->data_assinatura) {
            $assinaturaQRService = app(\App\Services\Template\AssinaturaQRService::class);
            $assinaturaHTML = $assinaturaQRService->gerarHTMLAssinatura($proposicao) ?: '';
            $qrcodeHTML = $assinaturaQRService->gerarHTMLQRCode($proposicao) ?: '';
        }
        
        // Preparar número da proposição para diferentes contextos
        $numeroCompleto = $proposicao->numero_protocolo ?: '[AGUARDANDO PROTOCOLO]';
        $numeroSemAno = $proposicao->numero_protocolo ? 
            (str_contains($proposicao->numero_protocolo, '/') ? 
                explode('/', $proposicao->numero_protocolo)[0] : 
                $proposicao->numero_protocolo) 
            : '[AGUARDANDO PROTOCOLO]';
        
        // Substituições diretas de variáveis
        $substituicoes = [
            '[AGUARDANDO PROTOCOLO]' => $numeroCompleto,
            '${numero_proposicao}' => $numeroCompleto,
            '${numero_sequencial}' => $numeroSemAno, // Apenas o número sem o ano
            '${autor_nome}' => $proposicao->autor->name ?? 'N/A',
            '${autor_cargo}' => $proposicao->autor->cargo_atual ?? 'Vereador',
            '${data_assinatura}' => $proposicao->data_assinatura ? $proposicao->data_assinatura->format('d/m/Y H:i:s') : '',
            'assinatura_digital_info' => $assinaturaHTML,
            '${assinatura_digital_info}' => $assinaturaHTML,
            'qrcode_html' => $qrcodeHTML,
            '${qrcode_html}' => $qrcodeHTML,
        ];
        
        // Substituir variáveis de data
        $meses = ['', 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 
                 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
        $substituicoes['${mes_extenso}'] = $meses[(int)now()->format('n')];
        $substituicoes['${dia}'] = now()->format('d');
        $substituicoes['${ano_atual}'] = now()->format('Y');
        $substituicoes['${municipio}'] = 'Caraguatatuba';
        
        foreach ($substituicoes as $placeholder => $valor) {
            // Se for HTML (assinatura/qrcode), não escapar
            if (in_array($placeholder, ['assinatura_digital_info', '${assinatura_digital_info}', 'qrcode_html', '${qrcode_html}']) && !empty($valor)) {
                // Substituir diretamente sem escapar
                $htmlContent = str_replace($placeholder, $valor, $htmlContent);
            } else {
                // Para texto normal, fazer substituição simples
                $htmlContent = str_replace($placeholder, $valor, $htmlContent);
            }
        }
        
        // CORREÇÃO FINAL: Remover qualquer duplicação restante do ano
        if ($proposicao->numero_protocolo) {
            $htmlContent = str_replace($proposicao->numero_protocolo . '/' . date('Y'), $proposicao->numero_protocolo, $htmlContent);
            $htmlContent = str_replace('mocao/2025/0001/2025', 'mocao/2025/0001', $htmlContent);
        }
        
        return $htmlContent;
    }
    
    /**
     * Encontrar o arquivo mais recente da proposição
     * Busca em todos os diretórios possíveis e retorna o mais recente
     */
    private function encontrarArquivoMaisRecente(Proposicao $proposicao): ?array
    {
        $arquivosEncontrados = [];
        
        // Padrões de busca para arquivos da proposição
        $padroes = [
            "proposicao_{$proposicao->id}_*.docx",
            "proposicao_{$proposicao->id}_*.rtf",
            "proposicao_{$proposicao->id}.docx",
            "proposicao_{$proposicao->id}.rtf"
        ];
        
        // Diretórios onde buscar
        $diretorios = [
            storage_path('app/proposicoes'),
            storage_path('app/private/proposicoes'),
            storage_path('app/public/proposicoes'),
            storage_path('app'), // Raiz do storage
            '/var/www/html/storage/app/proposicoes',
            '/var/www/html/storage/app/private/proposicoes'
        ];
        
        // Buscar arquivos em cada diretório
        foreach ($diretorios as $dir) {
            if (!is_dir($dir)) {
                continue;
            }
            
            foreach ($padroes as $padrao) {
                $arquivos = glob($dir . '/' . $padrao);
                foreach ($arquivos as $arquivo) {
                    if (file_exists($arquivo)) {
                        $mtime = filemtime($arquivo);
                        $arquivosEncontrados[] = [
                            'path' => $arquivo,
                            'relative_path' => str_replace(storage_path('app/'), '', $arquivo),
                            'modified' => date('Y-m-d H:i:s', $mtime),
                            'timestamp' => $mtime,
                            'size' => filesize($arquivo),
                            'type' => pathinfo($arquivo, PATHINFO_EXTENSION)
                        ];
                    }
                }
            }
        }
        
        // Se também tiver arquivo_path definido, incluir na busca
        if ($proposicao->arquivo_path) {
            $caminhosPossiveis = [
                storage_path('app/' . $proposicao->arquivo_path),
                storage_path('app/private/' . $proposicao->arquivo_path),
                storage_path('app/public/' . $proposicao->arquivo_path),
                storage_path('app/private/' . ltrim($proposicao->arquivo_path, 'private/')),
                '/var/www/html/storage/app/' . $proposicao->arquivo_path,
                '/var/www/html/storage/app/private/' . $proposicao->arquivo_path
            ];
            
            foreach ($caminhosPossiveis as $caminho) {
                if (file_exists($caminho) && !$this->arquivoJaIncluido($caminho, $arquivosEncontrados)) {
                    $mtime = filemtime($caminho);
                    $arquivosEncontrados[] = [
                        'path' => $caminho,
                        'relative_path' => $proposicao->arquivo_path,
                        'modified' => date('Y-m-d H:i:s', $mtime),
                        'timestamp' => $mtime,
                        'size' => filesize($caminho),
                        'type' => pathinfo($caminho, PATHINFO_EXTENSION)
                    ];
                }
            }
        }
        
        // Ordenar por data de modificação (mais recente primeiro)
        usort($arquivosEncontrados, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        
        // Log para debug
        if (!empty($arquivosEncontrados)) {
            error_log("PDF Assinatura: Encontrados " . count($arquivosEncontrados) . " arquivos para proposição {$proposicao->id}");
            foreach (array_slice($arquivosEncontrados, 0, 3) as $idx => $arquivo) {
                error_log("  " . ($idx + 1) . ". {$arquivo['relative_path']} - Modificado: {$arquivo['modified']} - Tamanho: {$arquivo['size']} bytes");
            }
        } else {
            error_log("PDF Assinatura: Nenhum arquivo encontrado para proposição {$proposicao->id}");
        }
        
        // Retornar o mais recente
        return !empty($arquivosEncontrados) ? $arquivosEncontrados[0] : null;
    }
    
    /**
     * Verificar se arquivo já está incluído na lista
     */
    private function arquivoJaIncluido(string $caminho, array $arquivos): bool
    {
        foreach ($arquivos as $arquivo) {
            if (realpath($arquivo['path']) === realpath($caminho)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Limpar PDFs antigos mantendo apenas os N mais recentes
     */
    private function limparPDFsAntigos(int $proposicaoId, int $manterQuantos = 3): void
    {
        $diretorioPdf = storage_path("app/proposicoes/pdfs/{$proposicaoId}");
        
        if (!is_dir($diretorioPdf)) {
            return;
        }
        
        $pdfs = glob($diretorioPdf . '/*.pdf');
        if (count($pdfs) <= $manterQuantos) {
            return;
        }
        
        // Ordenar por data de modificação (mais recente primeiro)
        usort($pdfs, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        // Remover PDFs antigos
        $paraRemover = array_slice($pdfs, $manterQuantos);
        foreach ($paraRemover as $pdf) {
            unlink($pdf);
            error_log("PDF Assinatura: Removido PDF antigo: " . basename($pdf));
        }
    }
    
    /**
     * Extrair conteúdo de arquivo DOCX de forma robusta
     * Método otimizado baseado no que funciona no teste
     */
    private function extrairConteudoDOCX(string $caminhoArquivo): string
    {
        if (!file_exists($caminhoArquivo)) {
            error_log("PDF Assinatura: Arquivo DOCX não encontrado: $caminhoArquivo");
            return '';
        }
        
        try {
            $zip = new \ZipArchive();
            if ($zip->open($caminhoArquivo) === TRUE) {
                // Extrair o document.xml que contém o texto principal
                $documentXml = $zip->getFromName('word/document.xml');
                $zip->close();
                
                if ($documentXml) {
                    // Extrair texto das tags <w:t>
                    preg_match_all('/<w:t[^>]*>(.*?)<\/w:t>/is', $documentXml, $matches);
                    
                    if (isset($matches[1]) && !empty($matches[1])) {
                        $texto = implode(' ', $matches[1]);
                        
                        // Decodificar entidades XML
                        $texto = html_entity_decode($texto, ENT_QUOTES | ENT_XML1);
                        
                        // Limpar espaços excessivos mas manter estrutura
                        $texto = preg_replace('/\s+/', ' ', $texto);
                        $texto = trim($texto);
                        
                        // Adicionar quebras de linha em pontos apropriados
                        $texto = str_replace('. ', ".\n", $texto);
                        $texto = str_replace('EMENTA:', "\n\nEMENTA:", $texto);
                        $texto = str_replace('A Câmara Municipal manifesta:', "\n\nA Câmara Municipal manifesta:\n\n", $texto);
                        $texto = str_replace('Resolve dirigir', "\n\nResolve dirigir", $texto);
                        
                        error_log("PDF Assinatura: Texto extraído com sucesso do DOCX");
                        return $texto;
                    }
                }
                
                error_log("PDF Assinatura: Não foi possível extrair document.xml do DOCX");
                return '';
                
            } else {
                error_log("PDF Assinatura: Não foi possível abrir arquivo DOCX como ZIP");
                return '';
            }
            
        } catch (\Exception $e) {
            error_log("PDF Assinatura: Erro ao processar DOCX: " . $e->getMessage());
            return '';
        }
    }
    
    /**
     * Otimizar HTML do LibreOffice para melhor compatibilidade com DomPDF
     */
    private function otimizarHTMLParaDomPDF(string $htmlContent): string
    {
        try {
            // 1. Otimizar CSS para DomPDF
            $htmlContent = preg_replace_callback(
                '/<style type="text\/css">(.*?)<\/style>/s',
                function($matches) {
                    $css = $matches[1];
                    
                    // Converter unidades problemáticas
                    $css = str_replace('1.18in', '85pt', $css);  // margin-left
                    $css = str_replace('0.59in', '42pt', $css);  // margin-right
                    $css = str_replace('0.5in', '36pt', $css);   // margin-top
                    $css = str_replace('0.25in', '18pt', $css);  // margin-bottom
                    $css = str_replace('0.1in', '7pt', $css);    // margin-bottom
                    
                    // Simplificar regras @page para DomPDF
                    $css = preg_replace(
                        '/@page\s*\{[^}]*\}/',
                        '@page { size: A4; margin: 36pt 42pt 18pt 85pt; }',
                        $css
                    );
                    
                    // Remover propriedades problemáticas
                    $css = preg_replace('/widows:\s*\d+;?/', '', $css);
                    $css = preg_replace('/orphans:\s*\d+;?/', '', $css);
                    $css = preg_replace('/direction:\s*ltr;?/', '', $css);
                    
                    // Simplificar fontes
                    $css = str_replace('"Arial", serif', 'Arial, sans-serif', $css);
                    
                    return '<style type="text/css">' . $css . '</style>';
                },
                $htmlContent
            );
            
            // 2. Remover elementos problemáticos
            $htmlContent = preg_replace('/<div title="header">.*?<\/div>/s', '', $htmlContent);
            
            // 3. Simplificar tags desnecessárias mantendo conteúdo
            $htmlContent = preg_replace('/<(p|div)([^>]*?)style="[^"]*margin-bottom:\s*0in[^"]*"([^>]*?)>/i', '<$1$2$3>', $htmlContent);
            
            // 4. Processar imagens - converter caminhos relativos para absolutos
            $htmlContent = preg_replace_callback(
                '/<img([^>]*?)src="([^"]*)"([^>]*?)>/i',
                function($matches) {
                    $src = $matches[2];
                    
                    // Se for caminho relativo, converter para absoluto
                    if (!str_starts_with($src, 'http') && !str_starts_with($src, 'data:')) {
                        // Tentar encontrar a imagem em diferentes localizações
                        $possiveisCaminhos = [
                            public_path($src),
                            public_path('template/' . basename($src)),
                            storage_path('app/public/' . $src),
                            base_path($src)
                        ];
                        
                        foreach ($possiveisCaminhos as $caminho) {
                            if (file_exists($caminho)) {
                                // Converter imagem para base64 para embedar no HTML
                                $imageData = base64_encode(file_get_contents($caminho));
                                $mimeType = mime_content_type($caminho);
                                $src = "data:$mimeType;base64,$imageData";
                                break;
                            }
                        }
                    }
                    
                    return '<img' . $matches[1] . 'src="' . $src . '"' . $matches[3] . '>';
                },
                $htmlContent
            );
            
            // 5. Simplificar classes CSS desnecessárias mantendo essenciais
            $htmlContent = preg_replace('/class="(western|cjk|ctl)"/', '', $htmlContent);
            
            return $htmlContent;
            
        } catch (\Exception $e) {
            error_log("PDF Assinatura: Erro ao otimizar HTML: " . $e->getMessage());
            // Retornar HTML original em caso de erro
            return $htmlContent;
        }
    }
    
    /**
     * Verificar se precisa regenerar PDF (otimização para evitar regeneração desnecessária)
     */
    private function precisaRegerarPDF(Proposicao $proposicao): bool
    {
        // Se não tem PDF, precisa gerar
        if (empty($proposicao->arquivo_pdf_path)) {
            return true;
        }
        
        // Verificar se o arquivo PDF existe fisicamente
        $pdfPath = storage_path('app/' . $proposicao->arquivo_pdf_path);
        if (!file_exists($pdfPath)) {
            return true;
        }
        
        // Verificar se o PDF é muito antigo (mais de 30 minutos)
        $pdfAge = time() - filemtime($pdfPath);
        if ($pdfAge > 1800) { // 30 minutos
            return true;
        }
        
        // Se a proposição foi atualizada recentemente, regenerar
        $proposicaoUpdateTime = $proposicao->updated_at ? $proposicao->updated_at->timestamp : 0;
        $pdfCreationTime = filemtime($pdfPath);
        if ($proposicaoUpdateTime > $pdfCreationTime) {
            return true;
        }
        
        // PDF válido e recente, não precisa regenerar
        return false;
    }
}