<?php

namespace App\Http\Controllers;

use App\Models\Proposicao;
use App\Services\AssinaturaDigitalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class AssinaturaDigitalController extends Controller
{
    protected $assinaturaService;

    public function __construct(AssinaturaDigitalService $assinaturaService)
    {
        $this->assinaturaService = $assinaturaService;
    }

    /**
     * Mostrar formulário de assinatura digital
     */
    public function mostrarFormulario(Proposicao $proposicao)
    {
        // Verificação de permissões já é feita pelo middleware check.assinatura.permission
        
        // Verificar se a proposição está disponível para assinatura
        if (!in_array($proposicao->status, ['aprovado', 'aprovado_assinatura'])) {
            abort(403, 'Esta proposição não está disponível para assinatura.');
        }

        // Verificar se já existe PDF para assinatura
        $pdfPath = $this->obterCaminhoPDFParaAssinatura($proposicao);
        
        // Se não existe PDF, gerar automaticamente
        if (!$pdfPath || !file_exists($pdfPath)) {
            try {
                $this->gerarPDFParaAssinatura($proposicao);
                $pdfPath = $this->obterCaminhoPDFParaAssinatura($proposicao);
                
                if (!$pdfPath || !file_exists($pdfPath)) {
                    return back()->withErrors(['pdf' => 'Erro ao gerar PDF para assinatura.']);
                }
            } catch (\Exception $e) {
                Log::error('Erro ao gerar PDF para assinatura: ' . $e->getMessage());
                return back()->withErrors(['pdf' => 'Erro ao gerar PDF para assinatura: ' . $e->getMessage()]);
            }
        }

        $tiposCertificado = $this->assinaturaService->getTiposCertificado();

        return view('assinatura.formulario-simplificado', compact('proposicao', 'tiposCertificado', 'pdfPath'));
    }

    /**
     * Processar assinatura digital
     */
    public function processarAssinatura(Request $request, Proposicao $proposicao)
    {
        try {
            // Verificação de permissões já é feita pelo middleware check.assinatura.permission
            
            // Validar dados da requisição
            $request->validate([
                'tipo_certificado' => 'required|in:A1,A3,PFX,SIMULADO',
                'senha' => 'required_if:tipo_certificado,A1,A3|nullable|string|min:4',
                'senha_pfx' => 'required_if:tipo_certificado,PFX|nullable|string|min:1'
            ]);
            
            // Validação adicional para tipos que requerem senha
            if (in_array($request->tipo_certificado, ['A1', 'A3']) && empty($request->senha)) {
                return back()->withErrors(['senha' => 'Senha é obrigatória para certificados A1/A3.']);
            }
            
            // Validação específica para arquivo PFX
            if ($request->tipo_certificado === 'PFX') {
                if (!$request->hasFile('arquivo_pfx')) {
                    return back()->withErrors(['arquivo_pfx' => 'Arquivo de certificado é obrigatório para tipo PFX.']);
                }
                
                $arquivo = $request->file('arquivo_pfx');
                if ($arquivo->getSize() > 2048 * 1024) { // 2MB
                    return back()->withErrors(['arquivo_pfx' => 'Arquivo muito grande. Máximo 2MB.']);
                }
                
                // Para demonstração, aceitar qualquer arquivo
                Log::info('Arquivo PFX aceito para demonstração', [
                    'nome' => $arquivo->getClientOriginalName(),
                    'tamanho' => $arquivo->getSize(),
                    'tipo' => $arquivo->getMimeType()
                ]);
            }

            // Obter caminho do PDF para assinatura
            $pdfPath = $this->obterCaminhoPDFParaAssinatura($proposicao);
            
            if (!$pdfPath || !file_exists($pdfPath)) {
                return back()->withErrors(['pdf' => 'PDF para assinatura não encontrado.']);
            }

            // Preparar dados da assinatura
            $dadosAssinatura = [
                'tipo_certificado' => $request->tipo_certificado,
                'nome_assinante' => Auth::user()->name, // Nome do usuário logado
                'senha' => $request->senha,
                'usuario_id' => Auth::id(),
                'ip_assinatura' => $request->ip(),
                'user_agent' => $request->userAgent()
            ];

            // Processar arquivo PFX se fornecido
            if ($request->tipo_certificado === 'PFX' && $request->hasFile('arquivo_pfx')) {
                $arquivoPFX = $request->file('arquivo_pfx');
                $caminhoPFX = $this->salvarArquivoPFX($arquivoPFX, $proposicao);
                $dadosAssinatura['arquivo_pfx'] = $caminhoPFX;
                $dadosAssinatura['senha_pfx'] = $request->senha_pfx;
            }
            
            // Gerar identificador e checksum
            $pdfContent = file_get_contents($pdfPath);
            $identificador = $this->assinaturaService->gerarIdentificadorAssinatura();
            $checksum = $this->assinaturaService->gerarChecksum($pdfContent);
            
            $dadosAssinatura['identificador'] = $identificador;
            $dadosAssinatura['checksum'] = $checksum;

            // Processar assinatura
            $pdfAssinado = $this->assinaturaService->assinarPDF($pdfPath, $dadosAssinatura);

            if (!$pdfAssinado) {
                return back()->withErrors(['assinatura' => 'Falha ao processar assinatura digital.']);
            }

            // Gerar dados compactos para o banco
            $dadosCompactos = [
                'id' => $identificador,
                'tipo' => $dadosAssinatura['tipo_certificado'],
                'nome' => $dadosAssinatura['nome_assinante'],
                'data' => now()->format('d/m/Y H:i')
            ];
            
            // Atualizar proposição com dados otimizados - Status vai para PROTOCOLO
            $statusAnterior = $proposicao->status;
            $proposicao->update([
                'status' => 'enviado_protocolo', // Após assinatura vai para protocolo
                'assinatura_digital' => json_encode($dadosCompactos), // Dados compactos
                'data_assinatura' => now(),
                'ip_assinatura' => $request->ip(),
                'certificado_digital' => $identificador, // ID de 32 caracteres
                'arquivo_pdf_assinado' => $this->obterCaminhoRelativo($pdfAssinado)
            ]);

            // Observer registrará automaticamente no histórico

            // Log da operação
            Log::info('Proposição assinada digitalmente', [
                'proposicao_id' => $proposicao->id,
                'usuario_id' => Auth::id(),
                'tipo_certificado' => $request->tipo_certificado,
                'pdf_assinado' => $pdfAssinado
            ]);

            return redirect()->route('proposicoes.show', $proposicao)
                ->with('success', 'Proposição assinada digitalmente com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao processar assinatura digital: ' . $e->getMessage(), [
                'proposicao_id' => $proposicao->id,
                'usuario_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['assinatura' => 'Erro ao processar assinatura: ' . $e->getMessage()]);
        }
    }

    /**
     * Visualizar PDF assinado
     */
    public function visualizarPDFAssinado(Proposicao $proposicao)
    {
        // Verificar permissões
        if (!Auth::user()->hasPermissionTo('proposicoes.view')) {
            abort(403, 'Você não tem permissão para visualizar esta proposição.');
        }

        // Verificar se existe PDF assinado
        if (!$proposicao->arquivo_pdf_assinado) {
            abort(404, 'PDF assinado não encontrado.');
        }

        $caminhoPDF = storage_path('app/' . $proposicao->arquivo_pdf_assinado);
        
        if (!file_exists($caminhoPDF)) {
            abort(404, 'Arquivo PDF assinado não encontrado.');
        }

        // Retornar PDF para visualização
        return response()->file($caminhoPDF, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="proposicao_' . $proposicao->id . '_assinada.pdf"'
        ]);
    }

    /**
     * Download do PDF assinado
     */
    public function downloadPDFAssinado(Proposicao $proposicao)
    {
        // Verificar permissões
        if (!Auth::user()->hasPermissionTo('proposicoes.view')) {
            abort(403, 'Você não tem permissão para baixar esta proposição.');
        }

        // Verificar se existe PDF assinado
        if (!$proposicao->arquivo_pdf_assinado) {
            abort(404, 'PDF assinado não encontrado.');
        }

        $caminhoPDF = storage_path('app/' . $proposicao->arquivo_pdf_assinado);
        
        if (!file_exists($caminhoPDF)) {
            abort(404, 'Arquivo PDF assinado não encontrado.');
        }

        // Retornar PDF para download
        return response()->download($caminhoPDF, 'proposicao_' . $proposicao->id . '_assinada.pdf');
    }

    /**
     * Verificar status da assinatura
     */
    public function verificarStatus(Proposicao $proposicao)
    {
        try {
            $status = [
                'assinada' => false,
                'tipo_certificado' => null,
                'data_assinatura' => null,
                'assinante' => null,
                'pdf_disponivel' => false
            ];

            if ($proposicao->status === 'assinado' && $proposicao->assinatura_digital) {
                $dadosAssinatura = json_decode($proposicao->assinatura_digital, true);
                
                $status['assinada'] = true;
                $status['tipo_certificado'] = $dadosAssinatura['tipo_certificado'] ?? null;
                $status['data_assinatura'] = $proposicao->data_assinatura?->format('d/m/Y H:i:s');
                $status['assinante'] = $dadosAssinatura['nome_assinante'] ?? null;
            }

            if ($proposicao->arquivo_pdf_assinado) {
                $caminhoPDF = storage_path('app/' . $proposicao->arquivo_pdf_assinado);
                $status['pdf_disponivel'] = file_exists($caminhoPDF);
            }

            return response()->json($status);

        } catch (\Exception $e) {
            Log::error('Erro ao verificar status da assinatura: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao verificar status'], 500);
        }
    }

    /**
     * Obter caminho do PDF para assinatura
     */
    private function obterCaminhoPDFParaAssinatura(Proposicao $proposicao): ?string
    {
        // Tentar usar PDF gerado pelo sistema
        if ($proposicao->arquivo_pdf_path) {
            $caminho = storage_path('app/' . $proposicao->arquivo_pdf_path);
            if (file_exists($caminho)) {
                return $caminho;
            }
        }

        // Tentar usar PDF do diretório de assinatura
        $diretorioPDFs = storage_path("app/proposicoes/pdfs/{$proposicao->id}");
        if (is_dir($diretorioPDFs)) {
            $pdfs = glob($diretorioPDFs . '/*.pdf');
            if (!empty($pdfs)) {
                // Retornar o PDF mais recente
                $pdfMaisRecente = array_reduce($pdfs, function($carry, $item) {
                    return (!$carry || filemtime($item) > filemtime($carry)) ? $item : $carry;
                });
                return $pdfMaisRecente;
            }
        }

        // Tentar usar PDF do OnlyOffice (diretório antigo)
        $diretorioPDFsOnlyOffice = storage_path("app/private/proposicoes/pdfs/{$proposicao->id}");
        if (is_dir($diretorioPDFsOnlyOffice)) {
            $pdfs = glob($diretorioPDFsOnlyOffice . '/*.pdf');
            if (!empty($pdfs)) {
                $pdfMaisRecente = array_reduce($pdfs, function($carry, $item) {
                    return (!$carry || filemtime($item) > filemtime($carry)) ? $item : $carry;
                });
                return $pdfMaisRecente;
            }
        }

        return null;
    }

    /**
     * Salvar arquivo PFX
     */
    private function salvarArquivoPFX($arquivo, Proposicao $proposicao): string
    {
        $diretorio = storage_path("app/private/certificados/{$proposicao->id}");
        
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }

        $nomeArquivo = 'certificado_' . time() . '.' . $arquivo->getClientOriginalExtension();
        $caminho = $diretorio . '/' . $nomeArquivo;

        $arquivo->move($diretorio, $nomeArquivo);

        return $caminho;
    }

    /**
     * Obter caminho relativo para armazenamento
     */
    private function obterCaminhoRelativo(string $caminhoAbsoluto): string
    {
        $storagePath = storage_path('app/');
        return str_replace($storagePath, '', $caminhoAbsoluto);
    }

    /**
     * Gerar PDF para assinatura automaticamente
     */
    private function gerarPDFParaAssinatura(Proposicao $proposicao): void
    {
        // Se já existe PDF oficial, não regenerar
        if ($proposicao->pdf_oficial_path && Storage::exists($proposicao->pdf_oficial_path)) {
            Log::info('PDF oficial já existe, não regenerando', [
                'proposicao_id' => $proposicao->id,
                'pdf_path' => $proposicao->pdf_oficial_path
            ]);
            return;
        }

        // Gerar nome único para PDF
        $nomePdf = 'proposicao_' . $proposicao->id . '_assinatura_' . time() . '.pdf';
        $diretorioPdf = 'proposicoes/pdfs/' . $proposicao->id;
        $caminhoPdfRelativo = $diretorioPdf . '/' . $nomePdf;
        $caminhoPdfAbsoluto = storage_path('app/' . $caminhoPdfRelativo);

        // Garantir que o diretório existe
        if (!is_dir(dirname($caminhoPdfAbsoluto))) {
            mkdir(dirname($caminhoPdfAbsoluto), 0755, true);
        }

        Log::info('Gerando PDF para assinatura', [
            'proposicao_id' => $proposicao->id,
            'pdf_path' => $caminhoPdfRelativo,
            'arquivo_origem' => $proposicao->arquivo_path
        ]);

        // Verificar se temos arquivo OnlyOffice (RTF/DOCX)
        if ($proposicao->arquivo_path && Storage::exists($proposicao->arquivo_path)) {
            $caminhoArquivo = Storage::path($proposicao->arquivo_path);
            $extensao = strtolower(pathinfo($caminhoArquivo, PATHINFO_EXTENSION));
            
            Log::info('Arquivo OnlyOffice encontrado', [
                'arquivo' => $proposicao->arquivo_path,
                'extensao' => $extensao,
                'tamanho' => filesize($caminhoArquivo)
            ]);
            
            // Usar DocumentConversionService para converter corretamente
            try {
                $conversionService = app(\App\Services\DocumentConversionService::class);
                $resultado = $conversionService->convertToPDF(
                    $proposicao->arquivo_path,
                    $caminhoPdfRelativo,
                    $proposicao->status
                );
                
                if ($resultado['success']) {
                    Log::info('PDF gerado com sucesso via DocumentConversionService', [
                        'proposicao_id' => $proposicao->id,
                        'converter' => $resultado['converter'] ?? 'unknown'
                    ]);
                    
                    // Atualizar proposição com caminho do PDF
                    $proposicao->update([
                        'arquivo_pdf_path' => $caminhoPdfRelativo,
                        'pdf_oficial_path' => $caminhoPdfRelativo,
                        'pdf_gerado_em' => now(),
                        'pdf_conversor_usado' => $resultado['converter'] ?? 'libreoffice'
                    ]);
                    
                    return;
                } else {
                    Log::error('Falha na conversão para PDF', [
                        'proposicao_id' => $proposicao->id,
                        'erro' => $resultado['error'] ?? 'desconhecido'
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Exceção ao converter para PDF', [
                    'proposicao_id' => $proposicao->id,
                    'erro' => $e->getMessage()
                ]);
            }
        }

        // Fallback: Buscar arquivo DOCX mais recente
        $arquivoDocx = $this->encontrarArquivoDocxMaisRecente($proposicao);
        
        if ($arquivoDocx && file_exists($arquivoDocx)) {
            // Converter DOCX para PDF usando LibreOffice
            $this->converterDocxParaPdf($arquivoDocx, $caminhoPdfAbsoluto);
            
            // Atualizar proposição
            $proposicao->update([
                'arquivo_pdf_path' => $caminhoPdfRelativo,
                'pdf_oficial_path' => $caminhoPdfRelativo,
                'pdf_gerado_em' => now()
            ]);
        } else {
            // Último recurso: Gerar PDF a partir do conteúdo do banco
            $this->gerarPdfDoConteudo($proposicao, $caminhoPdfAbsoluto);
            
            // Atualizar proposição
            $proposicao->update([
                'arquivo_pdf_path' => $caminhoPdfRelativo,
                'pdf_oficial_path' => $caminhoPdfRelativo,
                'pdf_gerado_em' => now()
            ]);
        }
    }

    /**
     * Encontrar arquivo DOCX mais recente da proposição
     */
    private function encontrarArquivoDocxMaisRecente(Proposicao $proposicao): ?string
    {
        // Diretórios para buscar arquivos
        $diretorios = [
            storage_path("app/proposicoes"),
            storage_path("app/private/proposicoes"),
            storage_path("app/public/proposicoes")
        ];

        $arquivos = [];
        
        foreach ($diretorios as $diretorio) {
            if (is_dir($diretorio)) {
                $pattern = $diretorio . "/proposicao_{$proposicao->id}_*.docx";
                $encontrados = glob($pattern);
                foreach ($encontrados as $arquivo) {
                    $arquivos[] = [
                        'path' => $arquivo,
                        'modified' => filemtime($arquivo)
                    ];
                }
            }
        }

        // Verificar arquivo_path do banco
        if ($proposicao->arquivo_path) {
            $caminhoCompleto = storage_path('app/' . $proposicao->arquivo_path);
            if (file_exists($caminhoCompleto) && str_ends_with($caminhoCompleto, '.docx')) {
                $arquivos[] = [
                    'path' => $caminhoCompleto,
                    'modified' => filemtime($caminhoCompleto)
                ];
            }
        }

        if (empty($arquivos)) {
            return null;
        }

        // Ordenar por data de modificação (mais recente primeiro)
        usort($arquivos, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });

        return $arquivos[0]['path'];
    }

    /**
     * Converter DOCX para PDF usando LibreOffice
     */
    private function converterDocxParaPdf(string $caminhoDocx, string $caminhoPdf): void
    {
        $diretorioDestino = dirname($caminhoPdf);
        
        // Comando LibreOffice para conversão
        $comando = "libreoffice --headless --convert-to pdf --outdir " . escapeshellarg($diretorioDestino) . " " . escapeshellarg($caminhoDocx) . " 2>&1";
        
        Log::info('Convertendo DOCX para PDF', [
            'comando' => $comando,
            'docx' => $caminhoDocx,
            'pdf_destino' => $caminhoPdf
        ]);

        exec($comando, $output, $returnCode);
        
        if ($returnCode !== 0) {
            Log::error('Erro na conversão DOCX para PDF', [
                'return_code' => $returnCode,
                'output' => implode("\n", $output)
            ]);
            throw new \Exception('Falha na conversão DOCX para PDF: ' . implode("\n", $output));
        }

        // LibreOffice gera o PDF com o mesmo nome base do DOCX
        $nomeBasePdf = pathinfo($caminhoDocx, PATHINFO_FILENAME) . '.pdf';
        $pdfGerado = $diretorioDestino . '/' . $nomeBasePdf;
        
        // Mover para o nome final desejado
        if (file_exists($pdfGerado) && $pdfGerado !== $caminhoPdf) {
            rename($pdfGerado, $caminhoPdf);
        }
        
        if (!file_exists($caminhoPdf)) {
            throw new \Exception('PDF não foi gerado corretamente');
        }
    }

    /**
     * Gerar PDF a partir do conteúdo do banco
     */
    private function gerarPdfDoConteudo(Proposicao $proposicao, string $caminhoPdf): void
    {
        // Para casos onde não há arquivo DOCX, usar conteúdo do banco
        $conteudo = $proposicao->conteudo ?: 'Conteúdo não disponível';
        
        // Gerar HTML simples
        $html = "<html><body><pre>{$conteudo}</pre></body></html>";
        
        // Salvar como arquivo HTML temporário e converter
        $htmlTemp = tempnam(sys_get_temp_dir(), 'proposicao_') . '.html';
        file_put_contents($htmlTemp, $html);
        
        try {
            $this->converterHtmlParaPdf($htmlTemp, $caminhoPdf);
        } finally {
            unlink($htmlTemp);
        }
    }

    /**
     * Converter HTML para PDF usando LibreOffice
     */
    private function converterHtmlParaPdf(string $caminhoHtml, string $caminhoPdf): void
    {
        $diretorioDestino = dirname($caminhoPdf);
        
        $comando = "libreoffice --headless --convert-to pdf --outdir " . escapeshellarg($diretorioDestino) . " " . escapeshellarg($caminhoHtml) . " 2>&1";
        
        exec($comando, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception('Falha na conversão HTML para PDF: ' . implode("\n", $output));
        }

        // LibreOffice gera PDF com nome baseado no HTML
        $nomeBasePdf = pathinfo($caminhoHtml, PATHINFO_FILENAME) . '.pdf';
        $pdfGerado = $diretorioDestino . '/' . $nomeBasePdf;
        
        if (file_exists($pdfGerado) && $pdfGerado !== $caminhoPdf) {
            rename($pdfGerado, $caminhoPdf);
        }
    }
}
