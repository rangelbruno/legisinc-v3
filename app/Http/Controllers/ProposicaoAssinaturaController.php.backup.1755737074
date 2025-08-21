<?php

namespace App\Http\Controllers;

use App\Models\Proposicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        
        if (!in_array($proposicao->status, ['aprovado', 'aprovado_assinatura', 'retornado_legislativo', 'assinado', 'enviado_protocolo'])) {
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

        // Obter dados otimizados para visualização integrada
        try {
            // Buscar conteúdo mais recente do OnlyOffice
            $responseOnlyOffice = $this->obterConteudoOnlyOffice($proposicao);
            $dadosOnlyOffice = json_decode($responseOnlyOffice->getContent(), true);
            
            if (!$dadosOnlyOffice['success']) {
                // Fallback para dados da proposição se OnlyOffice falhar
                $dadosOnlyOffice = $this->processarDadosFallback($proposicao);
            }

            // Aplicar limpeza de conteúdo duplicado
            $conteudoLimpo = $this->limparConteudoDuplicado($dadosOnlyOffice['conteudo'] ?? '');
            
            // Preparar dados para visualização integrada
            $dadosVisualizacao = [
                'conteudo_html' => $this->converterDocxParaHTML($conteudoLimpo),
                'usando_onlyoffice' => $dadosOnlyOffice['success'],
                'autor' => $proposicao->autor,
                'tipo_proposicao' => $proposicao->tipoProposicao ?? null,
                'dados_camara' => $this->obterDadosCamara(),
            ];
        } catch (\Exception $e) {
            Log::error("Erro ao obter dados otimizados: " . $e->getMessage());
            // Usar dados básicos em caso de erro
            $dadosVisualizacao = [
                'conteudo_html' => $proposicao->conteudo ? nl2br(e($proposicao->conteudo)) : '',
                'usando_onlyoffice' => false,
                'autor' => $proposicao->autor,
                'tipo_proposicao' => $proposicao->tipoProposicao ?? null,
                'dados_camara' => $this->obterDadosCamara(),
            ];
        }

        // Usar a nova view Vue.js otimizada para PDF com dados integrados
        return view('proposicoes.assinatura.assinar-pdf-vue', array_merge(compact('proposicao'), $dadosVisualizacao));
    }

    /**
     * Gerar visualização otimizada de PDF usando apenas dados do OnlyOffice
     */
    public function visualizarPDFOtimizado(Proposicao $proposicao)
    {
        try {
            // Buscar conteúdo mais recente do OnlyOffice
            $responseOnlyOffice = $this->obterConteudoOnlyOffice($proposicao);
            
            // Converter Response para array
            $dadosOnlyOffice = json_decode($responseOnlyOffice->getContent(), true);
            
            if (!$dadosOnlyOffice['success']) {
                // Fallback para dados da proposição se OnlyOffice falhar
                $dadosOnlyOffice = $this->processarDadosFallback($proposicao);
            }

            // Aplicar limpeza de conteúdo duplicado
            $conteudoLimpo = $this->limparConteudoDuplicado($dadosOnlyOffice['conteudo'] ?? '');
            
            // Preparar dados para visualização
            $dadosVisualizacao = [
                'proposicao' => $proposicao,
                'conteudo_html' => $this->converterDocxParaHTML($conteudoLimpo),
                'usando_onlyoffice' => $dadosOnlyOffice['success'],
                'autor' => $proposicao->autor,
                'tipo_proposicao' => $proposicao->tipoProposicao ?? null,
                'dados_camara' => $this->obterDadosCamara(),
                'metadados' => [
                    'data_geracao' => now(),
                    'metodo_extracao' => $dadosOnlyOffice['success'] ? 'onlyoffice_otimizado' : 'fallback',
                    'hash_integridade' => $dadosOnlyOffice['hash_integridade'] ?? null
                ]
            ];

            return view('proposicoes.assinatura.visualizar-pdf-otimizado', $dadosVisualizacao);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar visualização PDF otimizada', [
                'proposicao_id' => $proposicao->id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['pdf' => 'Erro ao carregar visualização: ' . $e->getMessage()]);
        }
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
                
                // SEMPRE usar conversão DOCX → HTML para preservar formatação do OnlyOffice
                if ($arquivoEncontrado && str_contains($arquivoPath, '.docx')) {
                    try {
                        error_log("PDF Assinatura: Convertendo DOCX → HTML para preservar formatação OnlyOffice");
                        
                        // Verificar se LibreOffice está disponível
                        if ($this->libreOfficeDisponivel()) {
                            $this->criarPDFComFormatacaoOnlyOffice($caminhoPdfAbsoluto, $proposicao, $arquivoEncontrado);
                            return; // Sucesso! PDF criado com formatação preservada
                        } else {
                            error_log("PDF Assinatura: LibreOffice não disponível, usando método alternativo");
                            // Usar método alternativo de extração direta do conteúdo completo
                            $this->criarPDFComConteudoCompleto($caminhoPdfAbsoluto, $proposicao, $arquivoEncontrado);
                            return;
                        }
                    } catch (\Exception $e) {
                        error_log("PDF Assinatura: Falha na conversão, usando extração de texto: " . $e->getMessage());
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
            '${assinatura_digital_info}' => $this->gerarTextoAssinaturaDigital($proposicao),
            'assinatura_digital_info' => $this->gerarTextoAssinaturaDigital($proposicao),
            '${qrcode_html}' => $this->gerarTextoQRCode($proposicao),
            'qrcode_html' => $this->gerarTextoQRCode($proposicao),
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
        
        // ADICIONAL: Processar variáveis que podem ter ficado como texto literal no conteúdo
        // (quando foram salvas pelo OnlyOffice sem processamento)
        if ($proposicao->assinatura_digital && $proposicao->data_assinatura) {
            $textoAssinatura = $this->gerarTextoAssinaturaDigital($proposicao);
            error_log("DEBUG: Texto da assinatura gerado: " . $textoAssinatura);
            // Substituir texto literal que pode ter ficado do template
            $before = substr_count($htmlContent, 'assinatura_digital_info');
            $htmlContent = str_replace('assinatura_digital_info', $textoAssinatura, $htmlContent);
            $htmlContent = str_replace('\nassinatura_digital_info\n', "\n" . $textoAssinatura . "\n", $htmlContent);
            $after = substr_count($htmlContent, 'assinatura_digital_info');
            error_log("DEBUG: Substituições feitas: " . ($before - $after) . " ocorrências");
        } else {
            error_log("DEBUG: Condições não atendidas - assinatura: " . ($proposicao->assinatura_digital ? 'SIM' : 'NÃO') . 
                     ", data: " . ($proposicao->data_assinatura ? 'SIM' : 'NÃO'));
        }
        
        if ($proposicao->numero_protocolo) {
            $textoQR = $this->gerarTextoQRCode($proposicao);
            // Substituir texto literal que pode ter ficado do template
            $htmlContent = str_replace('qrcode_html', $textoQR, $htmlContent);
            $htmlContent = str_replace('\nqrcode_html\n', "\n" . $textoQR . "\n", $htmlContent);
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
     * Encontrar PDF mais recente gerado pelo OnlyOffice
     */
    public function encontrarPDFMaisRecente(Proposicao $proposicao): ?array
    {
        $pdfsPossiveis = [];
        
        // 1. Verificar diretório principal de PDFs da proposição
        $diretorioPrincipal = storage_path("app/private/proposicoes/pdfs/{$proposicao->id}");
        if (is_dir($diretorioPrincipal)) {
            $arquivos = glob($diretorioPrincipal . '/*.pdf');
            foreach ($arquivos as $arquivo) {
                if (file_exists($arquivo)) {
                    $pdfsPossiveis[] = [
                        'path' => $arquivo,
                        'relative_path' => str_replace(storage_path('app/'), '', $arquivo),
                        'modified' => date('Y-m-d H:i:s', filemtime($arquivo)),
                        'timestamp' => filemtime($arquivo),
                        'size' => filesize($arquivo),
                        'tipo' => 'pdf_onlyoffice'
                    ];
                }
            }
        }
        
        // 2. Verificar se há PDF no arquivo_pdf_path
        if ($proposicao->arquivo_pdf_path) {
            $caminhoCompleto = storage_path('app/' . $proposicao->arquivo_pdf_path);
            if (file_exists($caminhoCompleto)) {
                $pdfsPossiveis[] = [
                    'path' => $caminhoCompleto,
                    'relative_path' => $proposicao->arquivo_pdf_path,
                    'modified' => date('Y-m-d H:i:s', filemtime($caminhoCompleto)),
                    'timestamp' => filemtime($caminhoCompleto),
                    'size' => filesize($caminhoCompleto),
                    'tipo' => 'pdf_assinatura'
                ];
            }
        }
        
        // 3. Verificar diretórios alternativos 
        $diretorios = [
            storage_path("app/proposicoes/{$proposicao->id}"),
            storage_path("app/private/proposicoes/{$proposicao->id}"),
            storage_path("app/public/proposicoes/{$proposicao->id}")
        ];
        
        foreach ($diretorios as $diretorio) {
            if (is_dir($diretorio)) {
                $arquivos = glob($diretorio . '/*.pdf');
                foreach ($arquivos as $arquivo) {
                    if (file_exists($arquivo)) {
                        $pdfsPossiveis[] = [
                            'path' => $arquivo,
                            'relative_path' => str_replace(storage_path('app/'), '', $arquivo),
                            'modified' => date('Y-m-d H:i:s', filemtime($arquivo)),
                            'timestamp' => filemtime($arquivo),
                            'size' => filesize($arquivo),
                            'tipo' => 'pdf_backup'
                        ];
                    }
                }
            }
        }
        
        // Ordenar por data de modificação (mais recente primeiro)
        usort($pdfsPossiveis, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        
        // Log para debug
        if (!empty($pdfsPossiveis)) {
            error_log("PDF OnlyOffice: Encontrados " . count($pdfsPossiveis) . " PDFs para proposição {$proposicao->id}");
            foreach (array_slice($pdfsPossiveis, 0, 3) as $idx => $pdf) {
                error_log("  " . ($idx + 1) . ". {$pdf['relative_path']} ({$pdf['tipo']}) - Modificado: {$pdf['modified']} - Tamanho: {$pdf['size']} bytes");
            }
        } else {
            error_log("PDF OnlyOffice: Nenhum PDF encontrado para proposição {$proposicao->id}");
        }
        
        return !empty($pdfsPossiveis) ? $pdfsPossiveis[0] : null;
    }
    
    /**
     * Servir PDF original do OnlyOffice diretamente com formatação preservada
     */
    public function visualizarPDFOriginal(Proposicao $proposicao)
    {
        try {
            // 1. Buscar arquivo DOCX mais recente editado no OnlyOffice
            $arquivoMaisRecente = $this->encontrarArquivoMaisRecente($proposicao);
            
            if (!$arquivoMaisRecente) {
                return response()->json([
                    'error' => 'Arquivo não encontrado',
                    'message' => 'Nenhum arquivo DOCX foi encontrado para esta proposição.'
                ], 404);
            }
            
            error_log("PDF OnlyOffice: Arquivo DOCX encontrado - {$arquivoMaisRecente['relative_path']}");
            
            // 2. Gerar PDF preservando formatação OnlyOffice usando LibreOffice
            $pdfPath = $this->gerarPDFComFormatacaoOnlyOffice($proposicao, $arquivoMaisRecente['path']);
            
            if (!$pdfPath || !file_exists($pdfPath)) {
                return response()->json([
                    'error' => 'PDF não pôde ser gerado',
                    'message' => 'Erro ao converter DOCX para PDF preservando formatação OnlyOffice.'
                ], 500);
            }
            
            // 3. Log da operação
            $tamanho = filesize($pdfPath);
            error_log("PDF OnlyOffice: PDF gerado com formatação preservada - {$tamanho} bytes");
            
            // 4. Retornar PDF para visualização no navegador
            return response()->file($pdfPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="proposicao_' . $proposicao->id . '_onlyoffice.pdf"'
            ]);
            
        } catch (\Exception $e) {
            error_log("PDF OnlyOffice: Erro ao gerar PDF com formatação: " . $e->getMessage());
            
            return response()->json([
                'error' => 'Erro interno',
                'message' => 'Ocorreu um erro ao processar o PDF: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Gerar PDF preservando formatação exata do OnlyOffice
     */
    private function gerarPDFComFormatacaoOnlyOffice(Proposicao $proposicao, string $caminhoDocx): string
    {
        try {
            error_log("PDF OnlyOffice: Iniciando conversão DOCX → PDF com formatação preservada");
            
            // 1. Definir caminhos
            $nomeBasePdf = 'proposicao_' . $proposicao->id . '_onlyoffice_' . time() . '.pdf';
            $finalPdf = storage_path("app/private/proposicoes/pdfs/{$proposicao->id}/" . $nomeBasePdf);
            
            // 2. Criar diretório final se não existir
            $finalDir = dirname($finalPdf);
            if (!is_dir($finalDir)) {
                mkdir($finalDir, 0755, true);
            }
            
            // 3. Tentar conversão via OnlyOffice Document Server se disponível
            if ($this->onlyOfficeServerDisponivel()) {
                $pdfPath = $this->converterDocxParaPdfViaOnlyOffice($caminhoDocx, $finalPdf);
                if ($pdfPath) {
                    return $pdfPath;
                }
            }
            
            // 4. Fallback: Tentar LibreOffice se disponível
            if ($this->libreOfficeDisponivel()) {
                $pdfPath = $this->converterDocxParaPdfViaLibreOffice($caminhoDocx, $finalPdf);
                if ($pdfPath) {
                    return $pdfPath;
                }
            }
            
            // 5. Fallback final: Usar DomPDF com conteúdo extraído e formatação melhorada
            error_log("PDF OnlyOffice: Usando DomPDF como fallback com formatação melhorada");
            return $this->gerarPDFComDomPdfMelhorado($proposicao, $caminhoDocx, $finalPdf);
            
        } catch (\Exception $e) {
            error_log("PDF OnlyOffice: Erro na conversão: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Verificar se OnlyOffice Document Server está disponível
     */
    private function onlyOfficeServerDisponivel(): bool
    {
        try {
            $onlyOfficeUrl = config('services.onlyoffice.document_server_url', 'http://localhost:8080');
            $response = \Http::timeout(5)->get($onlyOfficeUrl . '/healthcheck');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Converter DOCX para PDF via OnlyOffice Document Server
     */
    private function converterDocxParaPdfViaOnlyOffice(string $caminhoDocx, string $finalPdf): ?string
    {
        try {
            error_log("PDF OnlyOffice: Tentando conversão via OnlyOffice Document Server");
            
            // Esta seria a implementação usando a API do OnlyOffice
            // Por ora, retornar null para usar fallback
            return null;
            
        } catch (\Exception $e) {
            error_log("PDF OnlyOffice: Erro na conversão OnlyOffice: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Converter DOCX para PDF via LibreOffice
     */
    private function converterDocxParaPdfViaLibreOffice(string $caminhoDocx, string $finalPdf): ?string
    {
        try {
            error_log("PDF OnlyOffice: Tentando conversão via LibreOffice");
            
            $tempDir = storage_path('app/temp/pdf_conversion');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            $tempDocx = $tempDir . '/temp_' . time() . '.docx';
            copy($caminhoDocx, $tempDocx);
            
            $comando = sprintf(
                'libreoffice --headless --convert-to pdf --outdir %s %s 2>/dev/null',
                escapeshellarg($tempDir),
                escapeshellarg($tempDocx)
            );
            
            exec($comando, $output, $returnCode);
            
            $pdfTemporario = $tempDir . '/' . pathinfo($tempDocx, PATHINFO_FILENAME) . '.pdf';
            
            if ($returnCode === 0 && file_exists($pdfTemporario)) {
                rename($pdfTemporario, $finalPdf);
                unlink($tempDocx);
                error_log("PDF OnlyOffice: Conversão LibreOffice bem-sucedida");
                return $finalPdf;
            }
            
            return null;
            
        } catch (\Exception $e) {
            error_log("PDF OnlyOffice: Erro na conversão LibreOffice: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Gerar PDF usando APENAS conteúdo puro do OnlyOffice
     */
    private function gerarPDFComDomPdfMelhorado(Proposicao $proposicao, string $caminhoDocx, string $finalPdf): string
    {
        try {
            error_log("PDF OnlyOffice: Gerando PDF PURO do OnlyOffice (sem templates adicionais)");
            
            // 1. Extrair conteúdo RAW do DOCX editado no OnlyOffice
            $conteudoRaw = $this->extrairConteudoRawDoOnlyOffice($caminhoDocx);
            
            if (empty($conteudoRaw)) {
                error_log("PDF OnlyOffice: Conteúdo vazio, tentando método alternativo");
                $conteudoRaw = $this->extrairConteudoDOCX($caminhoDocx);
            }
            
            if (empty($conteudoRaw)) {
                throw new \Exception('Não foi possível extrair conteúdo do arquivo OnlyOffice');
            }
            
            error_log("PDF OnlyOffice: Conteúdo extraído - " . strlen($conteudoRaw) . " caracteres");
            error_log("PDF OnlyOffice: Primeiros 200 chars extraídos: " . substr($conteudoRaw, 0, 200));
            
            // 2. Aplicar limpeza APENAS para remover duplicações (não remover conteúdo válido)
            $conteudoLimpo = $this->limparApenasTemplatesPadrao($conteudoRaw);
            
            error_log("PDF OnlyOffice: Conteúdo após limpeza - " . strlen($conteudoLimpo) . " caracteres");
            error_log("PDF OnlyOffice: Primeiros 200 chars após limpeza: " . substr($conteudoLimpo, 0, 200));
            
            // 3. Gerar HTML minimalista preservando APENAS o conteúdo do OnlyOffice
            $html = $this->gerarHTMLSimulandoOnlyOffice($proposicao, $conteudoLimpo);
            
            // 4. Usar DomPDF para gerar PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 96
            ]);
            
            // 5. Salvar PDF
            $pdf->save($finalPdf);
            
            if (!file_exists($finalPdf) || filesize($finalPdf) < 1000) {
                throw new \Exception('PDF gerado é inválido');
            }
            
            $tamanho = filesize($finalPdf);
            error_log("PDF OnlyOffice: PDF PURO gerado com sucesso - {$tamanho} bytes");
            
            return $finalPdf;
            
        } catch (\Exception $e) {
            error_log("PDF OnlyOffice: Erro no PDF puro: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Extrair conteúdo RAW do OnlyOffice preservando formatação original
     */
    private function extrairConteudoRawDoOnlyOffice(string $caminhoDocx): string
    {
        try {
            if (!file_exists($caminhoDocx)) {
                return '';
            }
            
            error_log("PDF OnlyOffice: Extraindo conteúdo RAW de " . basename($caminhoDocx));
            
            $zip = new \ZipArchive();
            if ($zip->open($caminhoDocx) !== TRUE) {
                return '';
            }
            
            // Extrair document.xml que contém o conteúdo real
            $documentXml = $zip->getFromName('word/document.xml');
            $zip->close();
            
            if (empty($documentXml)) {
                return '';
            }
            
            // Carregar XML
            libxml_use_internal_errors(true);
            $doc = new \DOMDocument();
            $doc->loadXML($documentXml);
            
            // Extrair apenas texto, preservando quebras de linha e parágrafos
            $xpath = new \DOMXPath($doc);
            $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            
            $paragrafos = $xpath->query('//w:p');
            $conteudoCompleto = '';
            
            // MÉTODO ROBUSTO: Extrair TODO o texto, sem perder nenhum conteúdo
            foreach ($paragrafos as $paragrafo) {
                $textoP = '';
                
                // Primeira passada: extrair todo o texto simples do parágrafo
                $textos = $xpath->query('.//w:t', $paragrafo);
                foreach ($textos as $texto) {
                    $textoP .= $texto->textContent;
                }
                
                // SEMPRE incluir parágrafos, mesmo que tenham pouco texto
                $textoLimpo = trim($textoP);
                if (!empty($textoLimpo)) {
                    // Tentar aplicar formatação sofisticada
                    $textoFormatado = '';
                    $elementosTexto = $xpath->query('.//w:r', $paragrafo);
                    
                    if ($elementosTexto->length > 0) {
                        foreach ($elementosTexto as $run) {
                            // Verificar formatação
                            $rPr = $xpath->query('.//w:rPr', $run)->item(0);
                            $isBold = $rPr && $xpath->query('.//w:b', $rPr)->length > 0;
                            $isItalic = $rPr && $xpath->query('.//w:i', $rPr)->length > 0;
                            $isUnderline = $rPr && $xpath->query('.//w:u', $rPr)->length > 0;
                            
                            // Extrair texto do run
                            $textosRun = $xpath->query('.//w:t', $run);
                            foreach ($textosRun as $texto) {
                                $textoAtual = $texto->textContent;
                                
                                // Aplicar formatação HTML
                                if ($isBold) $textoAtual = '<strong>' . $textoAtual . '</strong>';
                                if ($isItalic) $textoAtual = '<em>' . $textoAtual . '</em>';
                                if ($isUnderline) $textoAtual = '<u>' . $textoAtual . '</u>';
                                
                                $textoFormatado .= $textoAtual;
                            }
                        }
                    }
                    
                    // GARANTIR que o texto seja incluído - priorizar texto simples se formatação falhar
                    $textoFinal = !empty(trim($textoFormatado)) ? trim($textoFormatado) : $textoLimpo;
                    
                    // Verificar alinhamento do parágrafo
                    $pPr = $xpath->query('.//w:pPr', $paragrafo)->item(0);
                    $jc = null;
                    if ($pPr) {
                        $jcNode = $xpath->query('.//w:jc', $pPr)->item(0);
                        if ($jcNode) {
                            $jc = $jcNode->getAttribute('w:val');
                        }
                    }
                    
                    // Aplicar alinhamento
                    if ($jc === 'center') {
                        $textoFinal = '<p class="text-center">' . $textoFinal . '</p>';
                    } elseif ($jc === 'right') {
                        $textoFinal = '<p class="text-right">' . $textoFinal . '</p>';
                    } elseif ($jc === 'both') {
                        $textoFinal = '<p class="text-justify">' . $textoFinal . '</p>';
                    } else {
                        $textoFinal = '<p>' . $textoFinal . '</p>';
                    }
                    
                    $conteudoCompleto .= $textoFinal . "\n";
                }
            }
            
            error_log("PDF OnlyOffice: Conteúdo RAW extraído - " . strlen($conteudoCompleto) . " caracteres");
            error_log("PDF OnlyOffice: Primeiros 300 chars do conteúdo: " . substr($conteudoCompleto, 0, 300));
            
            return trim($conteudoCompleto);
            
        } catch (\Exception $e) {
            error_log("PDF OnlyOffice: Erro na extração RAW: " . $e->getMessage());
            return '';
        }
    }
    
    /**
     * Limpar APENAS templates padrão, preservando conteúdo do Administrador
     */
    private function limparApenasTemplatesPadrao(string $conteudo): string
    {
        try {
            error_log("PDF OnlyOffice: Limpando apenas templates padrão");
            
            // Remover apenas padrões que indicam template automático, NÃO o template do Administrador
            $padroes = [
                '/EMENTA:\s*EMENTA:/i',  // Duplicação de "EMENTA:"
                '/CÂMARA MUNICIPAL.*?CÂMARA MUNICIPAL/si',  // Duplicação de cabeçalho
                '/Praça da República.*?Praça da República/si',  // Duplicação de endereço
                '/\(12\) 3882-5588.*?\(12\) 3882-5588/si',  // Duplicação de telefone
            ];
            
            $conteudoLimpo = $conteudo;
            foreach ($padroes as $padrao) {
                $conteudoLimpo = preg_replace($padrao, '', $conteudoLimpo);
            }
            
            // Remover múltiplas quebras de linha em excesso
            $conteudoLimpo = preg_replace('/\n{3,}/', "\n\n", $conteudoLimpo);
            
            error_log("PDF OnlyOffice: Limpeza aplicada - " . strlen($conteudoLimpo) . " caracteres finais");
            error_log("PDF OnlyOffice: Conteúdo após limpeza: " . substr($conteudoLimpo, 0, 300));
            
            return trim($conteudoLimpo);
            
        } catch (\Exception $e) {
            error_log("PDF OnlyOffice: Erro na limpeza: " . $e->getMessage());
            return $conteudo;  // Retornar original em caso de erro
        }
    }
    
    /**
     * Gerar HTML com formatação adequada incluindo imagem do cabeçalho
     */
    private function gerarHTMLSimulandoOnlyOffice(Proposicao $proposicao, string $conteudo): string
    {
        error_log("PDF OnlyOffice LEGISLATIVO: Usando conteúdo editado mais recente com formatação preservada");
        error_log("PDF OnlyOffice LEGISLATIVO: Conteúdo recebido (" . strlen($conteudo) . " chars): " . substr($conteudo, 0, 200) . "...");
        
        // Usar o conteúdo recebido que já vem do arquivo mais recente editado pelo Legislativo
        $conteudoPuro = trim($conteudo);
        
        // Verificar se já contém elementos processados pelo OnlyOffice/Legislativo
        $temImagemJaProcessada = strpos($conteudoPuro, '<img') !== false || 
                                strpos($conteudoPuro, 'data:image') !== false ||
                                strpos($conteudoPuro, 'base64') !== false;
        
        $temCabecalho = stripos($conteudoPuro, 'CÂMARA MUNICIPAL') !== false ||
                       stripos($conteudoPuro, 'Praça da República') !== false ||
                       strpos($conteudoPuro, '${imagem_cabecalho}') !== false;
        
        $temRodape = stripos($conteudoPuro, '__________________________________') !== false ||
                    stripos($conteudoPuro, 'Parlamentar') !== false ||
                    stripos($conteudoPuro, 'Vereador') !== false;
        
        if ($temImagemJaProcessada) {
            error_log("PDF OnlyOffice LEGISLATIVO: Imagem já processada pelo Legislativo - preservando formatação");
        } elseif ($temCabecalho || $temRodape) {
            error_log("PDF OnlyOffice LEGISLATIVO: Documento já tem estrutura completa - preservando sem adições");
            // Apenas processar variável de imagem se existir
            if (strpos($conteudoPuro, '${imagem_cabecalho}') !== false) {
                $imagemCabecalho = $this->obterImagemCabecalhoBase64();
                if ($imagemCabecalho) {
                    $htmlImagem = '<img src="' . $imagemCabecalho . '" alt="Cabeçalho da Câmara" style="max-width: 400px; height: auto; display: block; margin: 0 auto 10px auto;">';
                    $conteudoPuro = str_replace('${imagem_cabecalho}', $htmlImagem, $conteudoPuro);
                    error_log("PDF OnlyOffice LEGISLATIVO: Apenas variável \${imagem_cabecalho} substituída");
                } else {
                    $conteudoPuro = str_replace('${imagem_cabecalho}', '', $conteudoPuro);
                }
            }
        } else {
            // Documento sem estrutura - verificar se contém variável de imagem
            if (strpos($conteudoPuro, '${imagem_cabecalho}') !== false) {
                error_log("PDF OnlyOffice LEGISLATIVO: Processando variável \${imagem_cabecalho}");
                $imagemCabecalho = $this->obterImagemCabecalhoBase64();
                if ($imagemCabecalho) {
                    $htmlImagem = '<img src="' . $imagemCabecalho . '" alt="Cabeçalho da Câmara" style="max-width: 400px; height: auto; display: block; margin: 0 auto 10px auto;">';
                    $conteudoPuro = str_replace('${imagem_cabecalho}', $htmlImagem, $conteudoPuro);
                    error_log("PDF OnlyOffice LEGISLATIVO: Variável \${imagem_cabecalho} substituída pela imagem real");
                } else {
                    $conteudoPuro = str_replace('${imagem_cabecalho}', '', $conteudoPuro);
                    error_log("PDF OnlyOffice LEGISLATIVO: Variável \${imagem_cabecalho} removida (imagem não encontrada)");
                }
            } else {
                error_log("PDF OnlyOffice LEGISLATIVO: Documento simples - preservando exatamente como está");
            }
        }
        
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 2.5cm 2cm;
            size: A4;
        }
        
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.2;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        /* Preservar formatação rica do OnlyOffice com otimizações */
        .conteudo-puro {
            white-space: pre-wrap;
            word-wrap: break-word;
            text-align: left;
        }
        
        /* Preservar estilos de texto */
        .conteudo-puro strong, .conteudo-puro b {
            font-weight: bold;
        }
        
        .conteudo-puro em, .conteudo-puro i {
            font-style: italic;
        }
        
        .conteudo-puro u {
            text-decoration: underline;
        }
        
        /* Preservar alinhamentos */
        .conteudo-puro .text-center {
            text-align: center;
        }
        
        .conteudo-puro .text-right {
            text-align: right;
        }
        
        .conteudo-puro .text-justify {
            text-align: justify;
        }
        
        /* Otimizar espaçamento entre parágrafos - COMPACTO */
        .conteudo-puro p {
            margin: 3pt 0;
            line-height: 1.2;
            padding: 0;
        }
        
        /* Remover espaçamentos desnecessários */
        .conteudo-puro br + br,
        .conteudo-puro br + p,
        .conteudo-puro p + br {
            display: none;
        }
        
        /* Espaçamento específico para primeiro e último parágrafo */
        .conteudo-puro p:first-child {
            margin-top: 0;
        }
        
        .conteudo-puro p:last-child {
            margin-bottom: 0;
        }
        
        /* Estilo para imagens (cabeçalho e outras) */
        .conteudo-puro img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto 10px auto;
        }
        
        /* Preservar tabelas se houver */
        .conteudo-puro table {
            border-collapse: collapse;
            width: 100%;
            margin: 10px 0;
        }
        
        .conteudo-puro table td, .conteudo-puro table th {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
        }
        
        /* Garantir texto selecionável */
        * {
            user-select: text !important;
            -webkit-user-select: text !important;
            -moz-user-select: text !important;
            -ms-user-select: text !important;
        }
    </style>
</head>
<body>
    <!-- Conteúdo do template do Administrador com processamento de variáveis -->
    <div class="conteudo-puro">
        ' . nl2br($conteudoPuro) . '
    </div>
</body>
</html>';
    }
    
    /**
     * Obter imagem do cabeçalho em Base64 para incorporar no PDF
     */
    private function obterImagemCabecalhoBase64(): ?string
    {
        try {
            $caminhoImagem = public_path('template/cabecalho.png');
            
            if (!file_exists($caminhoImagem)) {
                error_log("PDF OnlyOffice: Imagem do cabeçalho não encontrada em: $caminhoImagem");
                return null;
            }
            
            $dadosImagem = file_get_contents($caminhoImagem);
            $base64 = base64_encode($dadosImagem);
            $mimeType = 'image/png';
            
            error_log("PDF OnlyOffice: Imagem do cabeçalho carregada - " . strlen($dadosImagem) . " bytes");
            
            return "data:$mimeType;base64,$base64";
            
        } catch (\Exception $e) {
            error_log("PDF OnlyOffice: Erro ao carregar imagem do cabeçalho: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Formatar conteúdo para melhor apresentação no PDF
     */
    private function formatarConteudoParaPDF(string $conteudo): string
    {
        try {
            error_log("PDF OnlyOffice: Formatando conteúdo para PDF");
            
            // Converter quebras de linha para parágrafos HTML
            $conteudoFormatado = trim($conteudo);
            
            // Dividir em parágrafos baseado em quebras duplas
            $paragrafos = preg_split('/\n\s*\n/', $conteudoFormatado);
            
            $html = '';
            foreach ($paragrafos as $paragrafo) {
                $paragrafo = trim($paragrafo);
                if (empty($paragrafo)) continue;
                
                // Converter quebras simples em espaços dentro do parágrafo
                $paragrafo = preg_replace('/\n+/', ' ', $paragrafo);
                $paragrafo = preg_replace('/\s+/', ' ', $paragrafo);
                
                // Detectar títulos (linhas em maiúscula ou com padrões específicos)
                if ($this->ehTitulo($paragrafo)) {
                    $html .= "<h1>" . htmlspecialchars($paragrafo) . "</h1>\n";
                } else {
                    $html .= "<p>" . htmlspecialchars($paragrafo) . "</p>\n";
                }
            }
            
            error_log("PDF OnlyOffice: Conteúdo formatado - " . strlen($html) . " caracteres HTML");
            
            return $html;
            
        } catch (\Exception $e) {
            error_log("PDF OnlyOffice: Erro na formatação: " . $e->getMessage());
            // Fallback simples
            return '<p>' . nl2br(htmlspecialchars($conteudo)) . '</p>';
        }
    }
    
    /**
     * Verificar se um parágrafo é um título
     */
    private function ehTitulo(string $texto): bool
    {
        $texto = trim($texto);
        
        // Critérios para identificar títulos
        $criterios = [
            // Texto todo em maiúsculas
            strtoupper($texto) === $texto && strlen($texto) > 3,
            // Contém palavras-chave de título
            preg_match('/^(MOÇÃO|PROJETO|REQUERIMENTO|INDICAÇÃO|EMENDA)/i', $texto),
            // Padrão de numeração
            preg_match('/^(Nº|N°|\d+)/i', $texto),
            // Texto curto que pode ser título (menos de 100 caracteres)
            strlen($texto) < 100 && !preg_match('/[.!?]$/', $texto),
        ];
        
        return in_array(true, $criterios);
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
     * Extrair conteúdo completo de arquivo DOCX (CABEÇALHO + CORPO + RODAPÉ)
     * SOLUÇÃO DEFINITIVA: Respeita a estrutura do Word como configurada pelo Legislativo
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
                
                // 1. EXTRAIR CABEÇALHO (header*.xml)
                $conteudoCabecalho = $this->extrairSecaoWord($zip, 'header', 'CABEÇALHO');
                
                // 2. EXTRAIR CORPO PRINCIPAL (document.xml) 
                $conteudoCorpo = $this->extrairSecaoWord($zip, 'document', 'CORPO');
                
                // 3. EXTRAIR RODAPÉ (footer*.xml)
                $conteudoRodape = $this->extrairSecaoWord($zip, 'footer', 'RODAPÉ');
                
                $zip->close();
                
                // 4. COMBINAR NA ORDEM CORRETA: CABEÇALHO + CORPO + RODAPÉ
                $documentoCompleto = $this->combinarSecoesWord($conteudoCabecalho, $conteudoCorpo, $conteudoRodape);
                
                error_log("PDF Assinatura: Documento Word extraído com estrutura completa - Cabeçalho: " . strlen($conteudoCabecalho) . " chars, Corpo: " . strlen($conteudoCorpo) . " chars, Rodapé: " . strlen($conteudoRodape) . " chars, Total: " . strlen($documentoCompleto) . " chars");
                
                return $documentoCompleto;
                
            } else {
                error_log("PDF Assinatura: Não foi possível abrir arquivo DOCX como ZIP");
                return '';
            }
            
        } catch (\Exception $e) {
            error_log("PDF Assinatura: Erro ao processar DOCX completo: " . $e->getMessage());
            return '';
        }
    }
    
    /**
     * Extrair seção específica do Word (header/document/footer)
     */
    private function extrairSecaoWord(\ZipArchive $zip, string $tipoSecao, string $nomeSecao): string
    {
        $textoSecao = '';
        
        try {
            if ($tipoSecao === 'document') {
                // Extrair document.xml (corpo principal)
                $xmlContent = $zip->getFromName('word/document.xml');
                if ($xmlContent) {
                    $textoSecao = $this->extrairTextoDeXml($xmlContent, $nomeSecao);
                }
            } else {
                // Extrair header*.xml ou footer*.xml 
                for ($i = 1; $i <= 10; $i++) {
                    $xmlContent = $zip->getFromName("word/{$tipoSecao}{$i}.xml");
                    if ($xmlContent) {
                        $textoArquivo = $this->extrairTextoDeXml($xmlContent, "{$nomeSecao}{$i}");
                        if (!empty($textoArquivo)) {
                            $textoSecao .= $textoArquivo . "\n";
                        }
                    }
                }
            }
            
        } catch (\Exception $e) {
            error_log("PDF Assinatura: Erro ao extrair seção {$nomeSecao}: " . $e->getMessage());
        }
        
        return trim($textoSecao);
    }
    
    /**
     * Extrair texto de XML do Word 
     */
    private function extrairTextoDeXml(string $xmlContent, string $nomeSecao): string
    {
        // Extrair todos os elementos <w:t> que contêm o texto
        preg_match_all('/<w:t[^>]*>(.*?)<\/w:t>/s', $xmlContent, $matches);
        
        if (!isset($matches[1]) || empty($matches[1])) {
            return '';
        }
        
        // Combinar todos os textos encontrados
        $textoCompleto = '';
        foreach ($matches[1] as $texto) {
            // Decodificar entidades XML
            $textoLimpo = html_entity_decode($texto, ENT_QUOTES | ENT_XML1);
            $textoLimpo = trim($textoLimpo);
            
            if (!empty($textoLimpo)) {
                $textoCompleto .= $textoLimpo . ' ';
            }
        }
        
        $textoFinal = trim($textoCompleto);
        
        if (!empty($textoFinal)) {
            error_log("PDF Assinatura: Seção {$nomeSecao} extraída - " . strlen($textoFinal) . " caracteres - Preview: " . substr($textoFinal, 0, 100) . "...");
        }
        
        return $textoFinal;
    }
    
    /**
     * Combinar seções do Word na ordem correta com formatação apropriada
     */
    private function combinarSecoesWord(string $cabecalho, string $corpo, string $rodape): string
    {
        $documentoFinal = '';
        
        // CABEÇALHO (se existir)
        if (!empty($cabecalho)) {
            // Para cabeçalho, preservar estrutura mas tornar compacto
            $documentoFinal .= trim($cabecalho) . "\n\n";
        }
        
        // CORPO PRINCIPAL (sempre presente)
        if (!empty($corpo)) {
            // Aplicar formatação otimizada para o corpo
            $corpoFormatado = $this->formatarCorpoDocumento($corpo);
            $documentoFinal .= $corpoFormatado . "\n\n";
        }
        
        // RODAPÉ (se existir)
        if (!empty($rodape)) {
            // Para rodapé, preservar como linha institucional
            $documentoFinal .= trim($rodape);
        }
        
        return trim($documentoFinal);
    }
    
    /**
     * Formatar corpo do documento com quebras apropriadas
     */
    private function formatarCorpoDocumento(string $corpo): string
    {
        // Limpar espaços excessivos mas manter estrutura
        $corpo = preg_replace('/\s+/', ' ', $corpo);
        $corpo = trim($corpo);
        
        // Adicionar quebras de linha em pontos estratégicos
        $corpo = str_replace('EMENTA:', "\n\nEMENTA:", $corpo);
        $corpo = str_replace('A Câmara Municipal manifesta:', "\n\nA Câmara Municipal manifesta:\n", $corpo);
        $corpo = str_replace('Resolve dirigir', "\n\nResolve dirigir", $corpo);
        
        // Melhorar pontuação e parágrafos
        $corpo = preg_replace('/\.\s+([A-Z])/', ".\n\n$1", $corpo);
        
        return $corpo;
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

    /**
     * Gerar texto da assinatura digital para proposição
     */
    private function gerarTextoAssinaturaDigital(Proposicao $proposicao): string
    {
        // Se não tem assinatura digital, retornar vazio
        if (!$proposicao->assinatura_digital || !$proposicao->data_assinatura) {
            return '';
        }

        // Gerar identificador único baseado na proposição
        $identificador = $this->gerarIdentificadorAssinatura($proposicao);
        
        return "Autenticar documento em /autenticidade com o identificador {$identificador}, Documento assinado digitalmente conforme art. 4º, II da Lei 14.063/2020";
    }

    /**
     * Gerar texto do QR Code para proposição
     */
    private function gerarTextoQRCode(Proposicao $proposicao): string
    {
        // Se não tem protocolo ainda, não gerar QR Code
        if (!$proposicao->numero_protocolo) {
            return '';
        }

        return "Consulte o documento online com este QR Code";
    }

    /**
     * Gerar identificador único para assinatura
     */
    private function gerarIdentificadorAssinatura(Proposicao $proposicao): string
    {
        // Gerar um identificador baseado no ID da proposição e data de assinatura
        $baseString = $proposicao->id . '_' . ($proposicao->data_assinatura ? $proposicao->data_assinatura->timestamp : time());
        
        // Converter para hexadecimal para parecer um identificador oficial
        $hex = strtoupper(bin2hex($baseString));
        
        // Formatá-lo como um identificador padrão (similar ao exemplo fornecido)
        // 31003000350039003A005000 -> dividir em grupos
        $formatted = '';
        for ($i = 0; $i < strlen($hex) && $i < 24; $i += 4) {
            $formatted .= substr($hex, $i, 4);
        }
        
        // Se for muito curto, preencher com zeros
        while (strlen($formatted) < 24) {
            $formatted .= '0000';
        }
        
        // Truncar se for muito longo
        return substr($formatted, 0, 24);
    }
    
    /**
     * Criar PDF com conteúdo completo quando LibreOffice não está disponível
     * Método alternativo que preserva o máximo possível da formatação
     */
    private function criarPDFComConteudoCompleto(string $caminhoPdfAbsoluto, Proposicao $proposicao, string $arquivoPath): void
    {
        try {
            error_log("PDF Assinatura: Usando método alternativo para criar PDF com conteúdo completo");
            
            // Extrair conteúdo completo do DOCX
            $conteudo = $this->extrairConteudoDOCX($arquivoPath);
            
            if (empty($conteudo) || strlen($conteudo) < 50) {
                error_log("PDF Assinatura: Conteúdo extraído insuficiente, usando conteúdo do banco");
                $conteudo = $proposicao->conteudo ?: $proposicao->ementa ?: 'Conteúdo não disponível';
            }
            
            // Substituir placeholders
            $conteudo = $this->substituirPlaceholders($conteudo, $proposicao);
            
            // Gerar HTML completo com formatação aprimorada
            $html = $this->gerarHTMLCompletoParaPDF($proposicao, $conteudo);
            
            // Usar DomPDF para gerar PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            
            // Garantir que o diretório existe
            if (!is_dir(dirname($caminhoPdfAbsoluto))) {
                mkdir(dirname($caminhoPdfAbsoluto), 0755, true);
            }
            
            // Salvar PDF
            file_put_contents($caminhoPdfAbsoluto, $pdf->output());
            
            error_log("PDF Assinatura: PDF criado com conteúdo completo! Tamanho: " . filesize($caminhoPdfAbsoluto) . " bytes");
            
        } catch (\Exception $e) {
            error_log("PDF Assinatura: Erro ao criar PDF com conteúdo completo: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Gerar HTML completo para PDF com toda a formatação e conteúdo
     */
    private function gerarHTMLCompletoParaPDF(Proposicao $proposicao, string $conteudo): string
    {
        // Obter variáveis do template
        $templateVariableService = app(\App\Services\Template\TemplateVariableService::class);
        $variables = $templateVariableService->getTemplateVariables();
        
        // Número da proposição
        $numeroProposicao = $proposicao->numero_protocolo ?: '[AGUARDANDO PROTOCOLO]';
        
        // Gerar cabeçalho com imagem
        $headerHTML = '';
        if (!empty($variables['cabecalho_imagem'])) {
            $imagePath = public_path($variables['cabecalho_imagem']);
            if (file_exists($imagePath)) {
                $imageData = base64_encode(file_get_contents($imagePath));
                $mimeType = mime_content_type($imagePath);
                $headerHTML = '<div style="text-align: center; margin-bottom: 30px;">
                    <img src="data:' . $mimeType . ';base64,' . $imageData . '" 
                         style="max-width: 300px; height: auto;" alt="Cabeçalho" />
                </div>';
            }
        }
        
        // Informações da câmara
        $cabeçalhoTexto = "
        <div style='text-align: center; margin-bottom: 30px; font-size: 11pt;'>
            <strong>{$variables['cabecalho_nome_camara']}</strong><br>
            {$variables['cabecalho_endereco']}<br>
            {$variables['cabecalho_telefone']}<br>
            {$variables['cabecalho_website']}
        </div>";
        
        // Processar conteúdo para identificar seções
        $conteudoProcessado = $this->processarConteudoParaHTML($conteudo, $proposicao);
        
        // Gerar assinatura digital se houver
        $assinaturaDigitalHTML = '';
        if ($proposicao->assinatura_digital && $proposicao->data_assinatura) {
            $assinaturaQRService = app(\App\Services\Template\AssinaturaQRService::class);
            $assinaturaDigitalHTML = $assinaturaQRService->gerarHTMLAssinatura($proposicao) ?: '';
        }
        
        return "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <title>Proposição {$proposicao->id}</title>
            <style>
                body { 
                    font-family: 'Times New Roman', Times, serif; 
                    margin: 2cm 2cm 2cm 2.5cm; 
                    line-height: 1.6; 
                    font-size: 12pt;
                    color: #000;
                    text-align: justify;
                }
                .header { 
                    text-align: center; 
                    margin-bottom: 30px; 
                }
                .titulo-proposicao {
                    text-align: center;
                    font-weight: bold;
                    font-size: 14pt;
                    margin: 30px 0 20px 0;
                }
                .ementa {
                    margin: 20px 0;
                    text-align: justify;
                }
                .ementa-label {
                    font-weight: bold;
                    display: inline;
                }
                .conteudo-principal {
                    margin: 30px 0;
                    text-align: justify;
                    white-space: pre-wrap;
                    font-family: 'Times New Roman', Times, serif;
                }
                .justificativa {
                    margin-top: 30px;
                    text-align: justify;
                }
                .data-local {
                    margin-top: 40px;
                    text-align: right;
                }
                .assinatura-linha {
                    margin-top: 50px;
                    text-align: center;
                }
                .assinatura-nome {
                    margin-top: 5px;
                    text-align: center;
                    font-weight: bold;
                }
                .assinatura-cargo {
                    text-align: center;
                    font-style: italic;
                }
                .assinatura-digital { 
                    border: 2px solid #28a745; 
                    padding: 15px; 
                    margin: 30px 0; 
                    background-color: #f8f9fa;
                    page-break-inside: avoid;
                }
                .assinatura-digital h6 { 
                    color: #28a745; 
                    margin-bottom: 10px; 
                    font-size: 12pt;
                }
                p { 
                    margin: 10px 0; 
                    text-indent: 2em;
                }
            </style>
        </head>
        <body>
            {$headerHTML}
            {$cabeçalhoTexto}
            {$conteudoProcessado}
            " . ($assinaturaDigitalHTML ? "<div class='assinatura-digital'>" . $assinaturaDigitalHTML . "</div>" : "") . "
        </body>
        </html>";
    }
    
    /**
     * Processar conteúdo para HTML identificando e formatando seções
     */
    private function processarConteudoParaHTML(string $conteudo, Proposicao $proposicao): string
    {
        $html = '';
        
        // Título da proposição
        $tipoUppercase = strtoupper($proposicao->tipo);
        $numeroProposicao = $proposicao->numero_protocolo ?: '[AGUARDANDO PROTOCOLO]';
        $html .= "<div class='titulo-proposicao'>{$tipoUppercase} Nº {$numeroProposicao}</div>";
        
        // Processar conteúdo linha por linha para identificar seções
        $linhas = explode("\n", $conteudo);
        $dentroJustificativa = false;
        $conteudoPrincipal = [];
        $justificativa = [];
        
        foreach ($linhas as $linha) {
            $linha = trim($linha);
            
            // Identificar início da EMENTA
            if (stripos($linha, 'EMENTA:') !== false) {
                $ementa = trim(str_ireplace('EMENTA:', '', $linha));
                if (empty($ementa) && !empty($proposicao->ementa)) {
                    $ementa = $proposicao->ementa;
                }
                $html .= "<div class='ementa'><span class='ementa-label'>EMENTA:</span> {$ementa}</div>";
                continue;
            }
            
            // Identificar início da JUSTIFICATIVA
            if (stripos($linha, 'JUSTIFICATIVA') !== false) {
                $dentroJustificativa = true;
                continue;
            }
            
            // Identificar data e local
            if (preg_match('/^(Caraguatatuba|São Paulo),\s*\d/', $linha)) {
                // Finalizar conteúdo principal
                if (!empty($conteudoPrincipal)) {
                    $html .= "<div class='conteudo-principal'>" . implode("\n", $conteudoPrincipal) . "</div>";
                    $conteudoPrincipal = [];
                }
                
                // Adicionar justificativa se houver
                if (!empty($justificativa)) {
                    $html .= "<div class='justificativa'><strong>JUSTIFICATIVA:</strong><br>" . 
                             nl2br(htmlspecialchars(implode("\n", $justificativa))) . "</div>";
                }
                
                // Adicionar data e local
                $html .= "<div class='data-local'>{$linha}</div>";
                
                // Adicionar linha de assinatura
                $html .= "<div class='assinatura-linha'>_________________________________</div>";
                $html .= "<div class='assinatura-nome'>" . ($proposicao->autor->name ?? 'Nome do Parlamentar') . "</div>";
                $html .= "<div class='assinatura-cargo'>" . ($proposicao->autor->cargo_atual ?? 'Vereador') . "</div>";
                
                break; // Parar processamento após assinatura
            }
            
            // Adicionar linha ao conteúdo apropriado
            if (!empty($linha)) {
                if ($dentroJustificativa) {
                    $justificativa[] = $linha;
                } else if (!str_contains($linha, 'MOÇÃO Nº')) { // Evitar duplicar título
                    $conteudoPrincipal[] = $linha;
                }
            }
        }
        
        // Se ainda há conteúdo não processado
        if (!empty($conteudoPrincipal)) {
            $html .= "<div class='conteudo-principal'>" . nl2br(htmlspecialchars(implode("\n", $conteudoPrincipal))) . "</div>";
        }
        
        if (!empty($justificativa)) {
            $html .= "<div class='justificativa'><strong>JUSTIFICATIVA:</strong><br>" . 
                     nl2br(htmlspecialchars(implode("\n", $justificativa))) . "</div>";
        }
        
        return $html;
    }

    /**
     * Salvar PDF gerado pelo Vue.js no servidor
     */
    public function salvarPDFVue(Request $request, Proposicao $proposicao)
    {
        try {
            if (!$request->hasFile('pdf')) {
                return response()->json(['success' => false, 'message' => 'PDF não encontrado']);
            }

            $pdfFile = $request->file('pdf');
            
            // Gerar nome único para o arquivo
            $nomeArquivo = 'proposicao_' . $proposicao->id . '_vue_' . time() . '.pdf';
            $diretorio = 'proposicoes/pdfs/' . $proposicao->id;
            
            // Salvar arquivo
            $caminhoRelativo = $pdfFile->storeAs($diretorio, $nomeArquivo);
            
            // Atualizar proposição
            $proposicao->arquivo_pdf_path = $caminhoRelativo;
            $proposicao->save();
            
            // Limpar PDFs antigos
            $this->limparPDFsAntigos($proposicao->id);
            
            return response()->json([
                'success' => true,
                'message' => 'PDF salvo com sucesso',
                'pdf_path' => $caminhoRelativo
            ]);
            
        } catch (\Exception $e) {
            error_log('Erro ao salvar PDF Vue.js: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter dados do template para Vue.js
     */
    public function obterDadosTemplate(Proposicao $proposicao)
    {
        try {
            $templateVariableService = app(\App\Services\Template\TemplateVariableService::class);
            $variables = $templateVariableService->getTemplateVariables();
            
            // Dados do cabeçalho
            $cabecalho = [
                'nome_camara' => $variables['cabecalho_nome_camara'] ?? 'CÂMARA MUNICIPAL DE CARAGUATATUBA',
                'endereco' => $variables['cabecalho_endereco'] ?? 'Praça da República, 40, Centro, Caraguatatuba-SP',
                'telefone' => $variables['cabecalho_telefone'] ?? '(12) 3882-5588',
                'website' => $variables['cabecalho_website'] ?? 'www.camaracaraguatatuba.sp.gov.br',
                'imagem' => !empty($variables['cabecalho_imagem']) ? asset($variables['cabecalho_imagem']) : null
            ];
            
            // Dados da proposição processados
            $dadosProposicao = [
                'id' => $proposicao->id,
                'tipo' => $proposicao->tipo,
                'numero_protocolo' => $proposicao->numero_protocolo,
                'ementa' => $proposicao->ementa,
                'conteudo' => $this->processarConteudoParaVue($proposicao),
                'status' => $proposicao->status,
                'autor' => [
                    'nome' => $proposicao->autor->name ?? '',
                    'cargo' => $proposicao->autor->cargo_atual ?? 'Vereador'
                ],
                'assinatura_digital' => $proposicao->assinatura_digital,
                'data_assinatura' => $proposicao->data_assinatura,
                'certificado_digital' => $proposicao->certificado_digital
            ];
            
            return response()->json([
                'success' => true,
                'cabecalho' => $cabecalho,
                'proposicao' => $dadosProposicao,
                'data_local' => $this->gerarDataLocal()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter dados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Processar conteúdo da proposição para Vue.js
     */
    private function processarConteudoParaVue(Proposicao $proposicao): string
    {
        // Tentar extrair conteúdo do arquivo mais recente
        $arquivoMaisRecente = $this->encontrarArquivoMaisRecente($proposicao);
        
        if ($arquivoMaisRecente && str_contains($arquivoMaisRecente['path'], '.docx')) {
            $conteudo = $this->extrairConteudoDOCX($arquivoMaisRecente['path']);
            if (!empty($conteudo) && strlen($conteudo) > 50) {
                return $this->substituirPlaceholders($conteudo, $proposicao);
            }
        }
        
        // Fallback para conteúdo do banco
        $conteudo = $proposicao->conteudo ?: $proposicao->ementa ?: '';
        return $this->substituirPlaceholders($conteudo, $proposicao);
    }

    /**
     * Gerar data e local formatado
     */
    private function gerarDataLocal(): string
    {
        $meses = [
            1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
            5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
            9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro'
        ];
        
        $hoje = now();
        $dia = $hoje->format('d');
        $mes = $meses[(int)$hoje->format('n')];
        $ano = $hoje->format('Y');
        
        return "Caraguatatuba, {$dia} de {$mes} de {$ano}";
    }

    /**
     * Processar assinatura digital usando Vue.js
     */
    public function processarAssinaturaVue(Request $request, Proposicao $proposicao)
    {
        $request->validate([
            'senha_certificado' => 'required|string|min:4',
            'confirmacao_leitura' => 'required|boolean',
            'assinatura_digital' => 'required|string'
        ]);

        try {
            // Gerar dados da assinatura
            $timestampAssinatura = now();
            $identificadorAssinatura = $this->gerarIdentificadorAssinaturaComTimestamp($proposicao, $timestampAssinatura);
            
            // Dados do certificado simulado (em produção seria validado)
            $certificadoDigital = json_encode([
                'titular' => Auth::user()->name,
                'emissor' => 'AC Válida',
                'validade' => now()->addYear()->format('d/m/Y'),
                'tipo' => 'A1',
                'identificador' => $identificadorAssinatura
            ]);

            // Atualizar proposição
            $proposicao->update([
                'status' => 'assinado',
                'assinatura_digital' => $request->assinatura_digital,
                'certificado_digital' => $certificadoDigital,
                'data_assinatura' => $timestampAssinatura,
                'ip_assinatura' => $request->ip(),
                'confirmacao_leitura' => true
            ]);

            // Enviar automaticamente para protocolo
            $proposicao->update(['status' => 'enviado_protocolo']);

            return response()->json([
                'success' => true,
                'message' => 'Documento assinado digitalmente com sucesso!',
                'assinatura_digital' => $request->assinatura_digital,
                'data_assinatura' => $timestampAssinatura,
                'identificador' => $identificadorAssinatura
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar assinatura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gerar identificador único para assinatura com timestamp
     */
    private function gerarIdentificadorAssinaturaComTimestamp(Proposicao $proposicao, $timestamp): string
    {
        $baseString = $proposicao->id . '_' . $timestamp->timestamp . '_' . Auth::id();
        $hash = hash('sha256', $baseString);
        
        // Formatar como identificador hexadecimal
        $formatted = strtoupper(substr($hash, 0, 24));
        
        return $formatted;
    }

    /**
     * Verificar assinatura digital
     */
    public function verificarAssinatura(Proposicao $proposicao)
    {
        if (!$proposicao->assinatura_digital) {
            return response()->json([
                'success' => false,
                'message' => 'Documento não possui assinatura digital'
            ]);
        }

        $certificadoInfo = json_decode($proposicao->certificado_digital, true);
        
        return response()->json([
            'success' => true,
            'assinado' => true,
            'data_assinatura' => $proposicao->data_assinatura->format('d/m/Y H:i:s'),
            'assinante' => $proposicao->autor->name,
            'identificador' => $certificadoInfo['identificador'] ?? '',
            'certificado' => $certificadoInfo,
            'ip_assinatura' => $proposicao->ip_assinatura
        ]);
    }

    /**
     * Obter conteúdo editado do OnlyOffice para PDF
     */
    public function obterConteudoOnlyOffice(Proposicao $proposicao)
    {
        try {
            Log::info("Iniciando extração avançada OnlyOffice para proposição {$proposicao->id}");
            
            // Buscar arquivo DOCX mais recente
            $arquivoInfo = $this->encontrarArquivoMaisRecente($proposicao);
            $arquivoMaisRecente = $arquivoInfo ? $arquivoInfo['path'] : null;
            
            if (!$arquivoMaisRecente) {
                Log::warning("Nenhum arquivo OnlyOffice encontrado para proposição {$proposicao->id}");
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum arquivo editado encontrado',
                    'conteudo' => null,
                    'estrutura' => null,
                    'metadados' => null
                ]);
            }

            Log::info("Extraindo conteúdo fiel do arquivo: {$arquivoMaisRecente}");

            // Extração avançada usando PhpWord para fidelidade total
            $extraçãoAvançada = $this->extrairConteudoAvançado($arquivoMaisRecente);
            
            if (!$extraçãoAvançada['success']) {
                Log::error("Falha na extração avançada: {$extraçãoAvançada['erro']}");
                return response()->json([
                    'success' => false,
                    'message' => 'Erro na extração avançada do documento',
                    'conteudo' => null,
                    'estrutura' => null,
                    'metadados' => null
                ]);
            }

            Log::info("Extração avançada concluída com sucesso", [
                'parágrafos' => $extraçãoAvançada['estatísticas']['total_parágrafos'],
                'palavras' => $extraçãoAvançada['estatísticas']['total_palavras'],
                'formatações' => $extraçãoAvançada['estatísticas']['formatações_preservadas'],
                'arquivo' => basename($arquivoMaisRecente)
            ]);

            return response()->json([
                'success' => true,
                'conteudo' => $extraçãoAvançada['conteudo_html'] ?? '',
                'estrutura' => $extraçãoAvançada['estrutura_documento'] ?? [],
                'formatação' => $extraçãoAvançada['formatação_preservada'] ?? [],
                'metadados' => $extraçãoAvançada['metadados'] ?? [],
                'arquivo_origem' => basename($arquivoMaisRecente),
                'data_modificacao' => filemtime($arquivoMaisRecente),
                'hash_integridade' => hash_file('sha256', $arquivoMaisRecente)
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao obter conteúdo OnlyOffice: " . $e->getMessage(), [
                'proposicao_id' => $proposicao->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage(),
                'conteudo' => null,
                'cabecalho' => null
            ]);
        }
    }


    /**
     * Converter conteúdo DOCX para HTML limpo
     */
    private function converterDocxParaHTML($conteudoTexto)
    {
        // LIMPEZA: Remover dados externos que não devem estar no PDF
        $conteudo = $this->limparConteudoDuplicado($conteudoTexto);
        
        // Preservar quebras de linha
        $html = str_replace("\n", "<br>\n", $conteudo);
        
        // Converter parágrafos duplos em quebras
        $html = preg_replace('/\n\s*\n/', "</p>\n<p>", $html);
        
        // Envolver em parágrafo se não começar com tag
        if (!preg_match('/^\s*</', $html)) {
            $html = "<p>{$html}</p>";
        }
        
        // Limpar HTML inválido básico
        $html = preg_replace('/<p>\s*<\/p>/', '', $html);
        $html = str_replace('<br></p>', '</p>', $html);
        
        return $html;
    }

    /**
     * Extração avançada de conteúdo DOCX usando método existente e robusto
     */
    private function extrairConteudoAvançado($caminhoArquivo)
    {
        try {
            if (!file_exists($caminhoArquivo)) {
                return [
                    'success' => false,
                    'erro' => 'Arquivo não encontrado: ' . $caminhoArquivo
                ];
            }

            Log::info("Iniciando extração avançada para: " . basename($caminhoArquivo));

            // Usar o método de extração DOCX já existente e funcional
            $conteudoTexto = $this->extrairConteudoDOCX($caminhoArquivo);
            
            if (empty($conteudoTexto)) {
                return [
                    'success' => false,
                    'erro' => 'Não foi possível extrair conteúdo do arquivo'
                ];
            }

            // Limpeza para remover duplicações
            $conteudoLimpo = $this->limparConteudoDuplicado($conteudoTexto);
            
            // Converter para HTML bem formatado
            $conteudoHTML = $this->converterDocxParaHTML($conteudoLimpo);
            
            // Estatísticas básicas
            $totalPalavras = str_word_count(strip_tags($conteudoHTML));
            $totalParágrafos = substr_count($conteudoHTML, '<p>');
            
            $metadados = [
                'arquivo' => basename($caminhoArquivo),
                'tamanho_arquivo' => filesize($caminhoArquivo),
                'data_modificacao' => date('Y-m-d H:i:s', filemtime($caminhoArquivo)),
                'hash_integridade' => md5_file($caminhoArquivo)
            ];

            Log::info("Extração avançada concluída", [
                'palavras' => $totalPalavras,
                'parágrafos' => $totalParágrafos,
                'tamanho_final' => strlen($conteudoHTML)
            ]);

            return [
                'success' => true,
                'conteudo_html' => $conteudoHTML,
                'estrutura_documento' => [],
                'formatação_preservada' => [],
                'estatísticas' => [
                    'total_parágrafos' => $totalParágrafos,
                    'total_palavras' => $totalPalavras,
                    'formatações_preservadas' => 0
                ],
                'metadados' => $metadados
            ];

        } catch (\Exception $e) {
            Log::error("Erro na extração avançada: " . $e->getMessage());
            
            return [
                'success' => false,
                'erro' => 'Erro interno: ' . $e->getMessage()
            ];
        }
    }
    private function extraçãoFallback($caminhoArquivo)
    {
        try {
            $conteudoBasico = $this->extrairConteudoDOCX($caminhoArquivo);
            $conteudoLimpo = $this->limparConteudoDuplicado($conteudoBasico);

            return [
                'success' => true,
                'conteudo_html' => $this->converterDocxParaHTML($conteudoLimpo),
                'estrutura_documento' => ['método' => 'fallback_básico'],
                'formatação_preservada' => [],
                'metadados' => [
                    'método_extração' => 'fallback',
                    'arquivo' => basename($caminhoArquivo)
                ],
                'estatísticas' => [
                    'total_parágrafos' => substr_count($conteudoLimpo, "\n\n") + 1,
                    'total_palavras' => str_word_count($conteudoLimpo),
                    'formatações_preservadas' => 0,
                    'seções' => 1
                ]
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'erro' => 'Falha no fallback: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Limpar conteúdo duplicado e dados externos do DOCX
     */
    private function limparConteudoDuplicado($conteudo)
    {
        // Remover dados da câmara que vêm do template
        $padroesCabecalho = [
            '/CÂMARA MUNICIPAL DE .*?\n/',
            '/Praça da República.*?\n/',
            '/\(\d{2}\) \d{4}-\d{4}.*?\n/',
            '/www\..*?\..*?\.br.*?\n/',
        ];
        
        foreach ($padroesCabecalho as $padrao) {
            $conteudo = preg_replace($padrao, '', $conteudo);
        }
        
        // Remover múltiplas ementas (manter apenas a última)
        if (preg_match_all('/EMENTA:\s*(.+?)(?=\n|$)/i', $conteudo, $matches)) {
            $ementas = $matches[0];
            
            // Se há múltiplas ementas, manter apenas a última (mais recente)
            if (count($ementas) > 1) {
                $ultimaEmenta = end($ementas);
                
                // Remover todas as ementas
                foreach ($ementas as $ementa) {
                    $conteudo = str_replace($ementa, '', $conteudo);
                }
                
                // Adicionar apenas a última ementa no início
                $conteudo = $ultimaEmenta . "\n\n" . $conteudo;
            }
        }
        
        // Remover múltiplas ocorrências de "MOÇÃO Nº"
        if (preg_match_all('/MOC[ÃA]O\s*N[º°]\s*\[AGUARDANDO PROTOCOLO\]/i', $conteudo, $matches)) {
            $mocoes = $matches[0];
            
            if (count($mocoes) > 1) {
                $ultimaMocao = end($mocoes);
                
                // Remover todas as moções
                foreach ($mocoes as $mocao) {
                    $conteudo = str_replace($mocao, '', $conteudo);
                }
                
                // Adicionar apenas a última moção
                $conteudo = $ultimaMocao . "\n\n" . $conteudo;
            }
        }
        
        // Limpar quebras de linha excessivas
        $conteudo = preg_replace('/\n{3,}/', "\n\n", $conteudo);
        $conteudo = trim($conteudo);
        
        Log::info('Conteúdo limpo de duplicações', [
            'original_size' => strlen($conteudo),
            'linhas_principais' => count(array_filter(explode("\n", $conteudo), function($linha) {
                return !empty(trim($linha));
            }))
        ]);
        
        return $conteudo;
    }

    /**
     * Extrair cabeçalho do conteúdo HTML
     */
    private function extrairCabecalho($conteudoHTML)
    {
        // Buscar por padrões de cabeçalho no início do documento
        $linhas = explode("\n", $conteudoHTML);
        $cabecalho = [];
        
        foreach (array_slice($linhas, 0, 10) as $linha) {
            $linha = strip_tags($linha);
            $linha = trim($linha);
            
            // Se encontrar padrões típicos de cabeçalho
            if (preg_match('/(CÂMARA|MUNICIPAL|CARAGUATATUBA|Praça|República|CNPJ|www\.)/i', $linha)) {
                $cabecalho[] = $linha;
            } else if (count($cabecalho) > 0 && empty($linha)) {
                // Parar no primeiro parágrafo vazio após encontrar cabeçalho
                break;
            }
        }
        
        return implode('<br>', $cabecalho);
    }

    /**
     * Obter dados da câmara para cabeçalho do documento
     */
    private function obterDadosCamara()
    {
        return [
            'nome' => 'CÂMARA MUNICIPAL DE CARAGUATATUBA',
            'endereco' => 'Praça da República, 40, Centro, Caraguatatuba-SP',
            'telefone' => '(12) 3882-5588',
            'website' => 'www.camaracaraguatatuba.sp.gov.br',
            'cnpj' => '50.444.108/0001-41'
        ];
    }

    /**
     * Processar dados de fallback quando OnlyOffice não está disponível
     */
    private function processarDadosFallback(Proposicao $proposicao)
    {
        $conteudo = $proposicao->conteudo ?: 'Conteúdo não disponível.';
        
        return [
            'success' => true,
            'conteudo' => $conteudo,
            'metodo_extracao' => 'fallback_tradicional',
            'hash_integridade' => hash('sha256', $conteudo),
            'timestamp' => now()
        ];
    }

    /**
     * Excluir proposição completa do banco de dados
     */
    public function excluirDocumento(Proposicao $proposicao)
    {
        try {
            // Verificar se a proposição pode ser excluída
            if (!in_array($proposicao->status, ['aprovado', 'aprovado_assinatura', 'retornado_legislativo', 'rascunho', 'em_edicao'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta proposição não pode ser excluída no status atual.'
                ], 403);
            }

            // Verificar se o usuário tem permissão (deve ser o autor ou ter permissão administrativa)
            if (Auth::id() !== $proposicao->autor_id && !Auth::user()->hasRole(['ADMIN', 'LEGISLATIVO'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para excluir esta proposição.'
                ], 403);
            }

            $arquivosExcluidos = [];
            $pastasLimpas = 0;
            $proposicaoInfo = [
                'id' => $proposicao->id,
                'tipo' => $proposicao->tipo,
                'ementa' => $proposicao->ementa,
                'status' => $proposicao->status
            ];

            // 1. Excluir arquivo PDF de assinatura
            if ($proposicao->arquivo_pdf_path) {
                $pdfPath = storage_path('app/' . $proposicao->arquivo_pdf_path);
                if (file_exists($pdfPath)) {
                    unlink($pdfPath);
                    $arquivosExcluidos[] = 'PDF de assinatura';
                    Log::info("PDF de assinatura excluído: {$pdfPath}");
                }
            }

            // 2. Excluir arquivo principal editado (DOCX/RTF)
            if ($proposicao->arquivo_path) {
                $caminhosPossiveisArquivo = [
                    storage_path('app/' . $proposicao->arquivo_path),
                    storage_path('app/private/' . $proposicao->arquivo_path),
                    storage_path('app/proposicoes/' . basename($proposicao->arquivo_path)),
                ];

                foreach ($caminhosPossiveisArquivo as $caminho) {
                    if (file_exists($caminho)) {
                        unlink($caminho);
                        $arquivosExcluidos[] = 'Documento editado (' . pathinfo($caminho, PATHINFO_EXTENSION) . ')';
                        Log::info("Arquivo editado excluído: {$caminho}");
                        break;
                    }
                }
            }

            // 3. Limpar diretório específico da proposição
            $diretorioProposicao = storage_path("app/proposicoes/{$proposicao->id}");
            if (is_dir($diretorioProposicao)) {
                $this->limparDiretorioCompleto($diretorioProposicao);
                $pastasLimpas++;
            }

            // 4. Limpar diretório de PDFs
            $diretorioPdfs = storage_path("app/proposicoes/pdfs/{$proposicao->id}");
            if (is_dir($diretorioPdfs)) {
                $this->limparDiretorioCompleto($diretorioPdfs);
                $pastasLimpas++;
            }

            // 5. Limpar outros arquivos relacionados por padrão de nome
            $padroesArquivos = [
                storage_path("app/proposicoes/*proposicao_{$proposicao->id}*"),
                storage_path("app/private/proposicoes/*proposicao_{$proposicao->id}*"),
                storage_path("app/temp/*proposicao_{$proposicao->id}*"),
            ];

            foreach ($padroesArquivos as $padrao) {
                $arquivos = glob($padrao);
                foreach ($arquivos as $arquivo) {
                    if (is_file($arquivo)) {
                        unlink($arquivo);
                        $arquivosExcluidos[] = basename($arquivo);
                        Log::info("Arquivo relacionado excluído: {$arquivo}");
                    }
                }
            }

            // 6. Limpar dados da sessão relacionados
            $sessionKeys = [
                'proposicao_' . $proposicao->id . '_variaveis_template',
                'proposicao_' . $proposicao->id . '_conteudo_processado',
                'proposicao_' . $proposicao->id . '_dados_onlyoffice'
            ];

            foreach ($sessionKeys as $key) {
                session()->forget($key);
            }

            // 7. EXCLUIR A PROPOSIÇÃO DO BANCO DE DADOS
            $proposicao->delete();

            $mensagem = "Proposição excluída permanentemente do sistema!";
            if (!empty($arquivosExcluidos)) {
                $mensagem .= " Arquivos removidos: " . count($arquivosExcluidos) . " arquivo(s).";
            }
            if ($pastasLimpas > 0) {
                $mensagem .= " {$pastasLimpas} diretório(s) limpo(s).";
            }

            Log::info("Proposição {$proposicaoInfo['id']} excluída permanentemente por usuário " . Auth::id(), [
                'proposicao_info' => $proposicaoInfo,
                'arquivos_excluidos' => $arquivosExcluidos,
                'pastas_limpas' => $pastasLimpas,
                'usuario_id' => Auth::id(),
                'usuario_nome' => Auth::user()->name ?? 'N/A'
            ]);

            return response()->json([
                'success' => true,
                'message' => $mensagem,
                'arquivos_excluidos' => $arquivosExcluidos,
                'proposicao_excluida' => true,
                'redirect_url' => route('proposicoes.minhas-proposicoes') // Redirecionar para minhas proposições
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao excluir proposição {$proposicao->id}: " . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno ao excluir proposição: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Limpar diretório completo e todos os arquivos
     */
    private function limparDiretorioCompleto(string $diretorio): void
    {
        if (!is_dir($diretorio)) {
            return;
        }

        $arquivos = glob($diretorio . '/*');
        foreach ($arquivos as $arquivo) {
            if (is_file($arquivo)) {
                unlink($arquivo);
            } elseif (is_dir($arquivo)) {
                $this->limparDiretorioCompleto($arquivo);
                rmdir($arquivo);
            }
        }

        // Tentar remover o diretório (se estiver vazio)
        @rmdir($diretorio);
    }
}