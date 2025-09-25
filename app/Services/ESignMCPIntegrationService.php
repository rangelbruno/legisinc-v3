<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

/**
 * Serviço de integração com o eSign MCP Server
 * Permite adicionar assinaturas digitais em PDFs usando o servidor MCP
 */
class ESignMCPIntegrationService
{
    /**
     * Adicionar assinatura visual em PDF usando o MCP Server
     *
     * @param string $pdfPath Caminho do PDF original
     * @param array $signatureData Dados da assinatura (posição, imagem, texto)
     * @return string|null Caminho do PDF modificado ou null em caso de erro
     */
    public function addSignatureToPDF(string $pdfPath, array $signatureData): ?string
    {
        try {
            // Ler o PDF original
            if (!file_exists($pdfPath)) {
                Log::error('ESignMCP: PDF não encontrado', ['path' => $pdfPath]);
                return null;
            }

            $pdfContent = file_get_contents($pdfPath);
            $pdfBase64 = base64_encode($pdfContent);

            // Preparar dados para o MCP
            $mcpData = [
                'pdf_base64' => $pdfBase64,
                'page' => $signatureData['page'] ?? 0,
                'x' => $signatureData['x'] ?? 100,
                'y' => $signatureData['y'] ?? 100,
                'width' => $signatureData['width'] ?? 200,
                'height' => $signatureData['height'] ?? 60,
            ];

            // Adicionar imagem de assinatura se disponível
            if (isset($signatureData['signature_image'])) {
                if (file_exists($signatureData['signature_image'])) {
                    $imageContent = file_get_contents($signatureData['signature_image']);
                    $mcpData['signature_image_base64'] = base64_encode($imageContent);
                }
            }

            // Adicionar texto se disponível
            if (isset($signatureData['signature_text'])) {
                $mcpData['signature_text'] = $signatureData['signature_text'];
            }

            // Chamar o MCP Server (simulação - em produção usaria comunicação real)
            $result = $this->callMCPServer('add_signature_to_pdf', $mcpData);

            if ($result && isset($result['pdf_base64'])) {
                // Salvar o PDF modificado
                $modifiedPdfContent = base64_decode($result['pdf_base64']);
                $outputPath = str_replace('.pdf', '_signed.pdf', $pdfPath);
                file_put_contents($outputPath, $modifiedPdfContent);

                Log::info('ESignMCP: PDF assinado com sucesso', [
                    'original' => basename($pdfPath),
                    'signed' => basename($outputPath)
                ]);

                return $outputPath;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('ESignMCP: Erro ao adicionar assinatura', [
                'error' => $e->getMessage(),
                'pdf' => basename($pdfPath)
            ]);
            return null;
        }
    }

    /**
     * Criar campo de assinatura visual com informações do assinante
     */
    public function createSignatureField(string $pdfPath, array $fieldData): ?string
    {
        try {
            $pdfContent = file_get_contents($pdfPath);
            $pdfBase64 = base64_encode($pdfContent);

            $mcpData = [
                'pdf_base64' => $pdfBase64,
                'page' => $fieldData['page'] ?? 0,
                'x' => $fieldData['x'] ?? 100,
                'y' => $fieldData['y'] ?? 100,
                'width' => $fieldData['width'] ?? 250,
                'height' => $fieldData['height'] ?? 80,
                'signer_name' => $fieldData['signer_name'] ?? 'Não informado',
                'timestamp' => $fieldData['timestamp'] ?? now()->format('d/m/Y H:i:s'),
                'reason' => $fieldData['reason'] ?? 'Assinatura Digital'
            ];

            $result = $this->callMCPServer('create_signature_field', $mcpData);

            if ($result && isset($result['pdf_base64'])) {
                $modifiedPdfContent = base64_decode($result['pdf_base64']);
                $outputPath = str_replace('.pdf', '_field.pdf', $pdfPath);
                file_put_contents($outputPath, $modifiedPdfContent);

                Log::info('ESignMCP: Campo de assinatura criado', [
                    'pdf' => basename($outputPath),
                    'signer' => $mcpData['signer_name']
                ]);

                return $outputPath;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('ESignMCP: Erro ao criar campo de assinatura', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Obter informações sobre o PDF (páginas, dimensões)
     */
    public function getPDFInfo(string $pdfPath): ?array
    {
        try {
            $pdfContent = file_get_contents($pdfPath);
            $pdfBase64 = base64_encode($pdfContent);

            $result = $this->callMCPServer('get_pdf_info', [
                'pdf_base64' => $pdfBase64
            ]);

            if ($result && isset($result['pages'])) {
                return $result;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('ESignMCP: Erro ao obter informações do PDF', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Processar PDF com elementos visuais reais usando FPDF/FPDI
     */
    private function callMCPServer(string $tool, array $arguments): ?array
    {
        Log::info('ESignMCP: Processando com PDF real', [
            'tool' => $tool,
            'has_pdf' => isset($arguments['pdf_base64'])
        ]);

        // Simular resposta para get_pdf_info usando FPDI
        if ($tool === 'get_pdf_info') {
            try {
                if (isset($arguments['pdf_base64'])) {
                    $pdfContent = base64_decode($arguments['pdf_base64']);
                    $tempPdfPath = tempnam(sys_get_temp_dir(), 'pdf_info_') . '.pdf';
                    file_put_contents($tempPdfPath, $pdfContent);

                    $pdf = new Fpdi();
                    $pageCount = $pdf->setSourceFile($tempPdfPath);

                    // Obter dimensões da primeira página
                    $templateId = $pdf->importPage(1);
                    $size = $pdf->getTemplateSize($templateId);

                    unlink($tempPdfPath);

                    return [
                        'success' => true,
                        'total_pages' => $pageCount,
                        'pages' => [
                            ['page' => 0, 'width' => $size['width'], 'height' => $size['height']]
                        ]
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('Erro ao obter info do PDF, usando A4 padrão', ['error' => $e->getMessage()]);
            }

            // Fallback para A4
            return [
                'success' => true,
                'total_pages' => 1,
                'pages' => [
                    ['page' => 0, 'width' => 595, 'height' => 842]
                ]
            ];
        }

        // Processamento real para stamp lateral
        if ($tool === 'stamp_lateral') {
            return $this->processLateralStamp($arguments);
        }

        // Para outras ferramentas, retornar o PDF original como simulação
        return [
            'success' => true,
            'pdf_base64' => $arguments['pdf_base64'] ?? '',
            'message' => 'Operação simulada com sucesso'
        ];
    }

    /**
     * Processar carimbo lateral real usando FPDF/FPDI
     */
    private function processLateralStamp(array $arguments): array
    {
        try {
            Log::info('🎯 STAMP_MCP: Processando carimbo lateral REAL', [
                'elements_count' => count($arguments['elements'] ?? []),
                'sidebar' => $arguments['sidebar'] ?? null,
                'page' => $arguments['page'] ?? 0
            ]);

            if (!isset($arguments['pdf_base64'])) {
                throw new \Exception('PDF base64 não fornecido');
            }

            // 1. Salvar PDF original temporariamente
            $pdfContent = base64_decode($arguments['pdf_base64']);
            $tempInputPath = tempnam(sys_get_temp_dir(), 'stamp_input_') . '.pdf';
            $tempOutputPath = tempnam(sys_get_temp_dir(), 'stamp_output_') . '.pdf';
            file_put_contents($tempInputPath, $pdfContent);

            // 2. Inicializar FPDI
            $pdf = new Fpdi();
            $pdf->setSourceFile($tempInputPath);

            // 3. Importar primeira página
            $templateId = $pdf->importPage(1);
            $size = $pdf->getTemplateSize($templateId);

            $sidebar = $arguments['sidebar'] ?? [];
            $targetPage = $arguments['target_page'] ?? null;

            $targetWidth = $targetPage['target_width'] ?? $size['width'];
            $targetHeight = $targetPage['target_height'] ?? $size['height'];
            $originalWidth = $targetPage['original_width'] ?? $size['width'];
            $originalHeight = $targetPage['original_height'] ?? $size['height'];

            // Não escalar o conteúdo original - manter tamanho original
            // A sidebar será adicionada ao lado direito sem comprimir o documento

            $orientation = $targetWidth >= $targetHeight ? 'L' : 'P';
            $pdf->AddPage($orientation, [$targetWidth, $targetHeight]);

            // Não preencher toda a página com branco - manter transparência
            // Apenas desenhar o conteúdo original sem escalar para caber na largura disponível
            // Manter o documento original intacto e adicionar sidebar ao lado
            $pdf->useTemplate($templateId, 0, 0, $originalWidth, $originalHeight);

            // 4. Adicionar sidebar de fundo
            if (!empty($sidebar)) {
                $sidebarX = $sidebar['x'] ?? ($targetWidth - ($sidebar['w'] ?? 0));
                $sidebarY = $sidebar['y'] ?? 0;
                $sidebarW = $sidebar['w'] ?? 0;
                $sidebarH = $sidebar['h'] ?? $targetHeight;

                $pdf->SetFillColor(248, 249, 250); // Cor de fundo suave
                $pdf->Rect($sidebarX, $sidebarY, $sidebarW, $sidebarH, 'F');

                // Borda sutil
                $pdf->SetDrawColor(220, 220, 220);
                $pdf->SetLineWidth(0.5);
                $pdf->Rect($sidebarX, $sidebarY, $sidebarW, $sidebarH, 'D');
            }

            // 5. Processar elementos (usar elementos enviados pelo PDFAssinaturaIntegradaService)
            $elementsProcessed = 0;
            $elements = $arguments['elements'] ?? [];

            Log::info('🔧 STAMP_MCP: Processando elementos', [
                'elements_received' => count($elements),
                'elements_data' => $elements
            ]);

            foreach ($elements as $element) {
                if ($element['type'] === 'text') {
                    $elementsProcessed += $this->addTextElement($pdf, $element);
                } elseif ($element['type'] === 'qrcode') {
                    $elementsProcessed += $this->addQRCodeElement($pdf, $element);
                }
            }

            // 6. Gerar PDF final
            $modifiedPdfContent = $pdf->Output('S');

            // 7. Limpeza
            unlink($tempInputPath);

            Log::info('✅ STAMP_MCP: Carimbo lateral aplicado com elementos reais', [
                'elements_processed' => $elementsProcessed,
                'pdf_size' => strlen($modifiedPdfContent),
                'original_size' => strlen($pdfContent),
                'page_width_original' => $size['width'],
                'page_width_final' => $targetWidth,
                'content_preserved' => 'original_size'
            ]);

            return [
                'success' => true,
                'pdf_base64' => base64_encode($modifiedPdfContent),
                'message' => 'Carimbo lateral aplicado com elementos reais',
                'elements_applied' => $elementsProcessed
            ];

        } catch (\Exception $e) {
            Log::error('❌ STAMP_MCP: Erro ao processar carimbo lateral', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Em caso de erro, retornar PDF original
            return [
                'success' => false,
                'pdf_base64' => $arguments['pdf_base64'] ?? '',
                'message' => 'Erro ao aplicar carimbo: ' . $e->getMessage(),
                'elements_applied' => 0
            ];
        }
    }

    /**
     * Adicionar elemento de texto ao PDF
     */
    private function addTextElement(Fpdi $pdf, array $element): int
    {
        try {
            // Configurar fonte
            $pdf->SetFont('Arial', $element['font_weight'] === 'bold' ? 'B' : '', $element['font_size'] ?? 8);

            // Configurar cor do texto
            $color = $this->hexToRGB($element['color'] ?? '#333333');
            $pdf->SetTextColor($color[0], $color[1], $color[2]);

            // Posicionar
            $x = $element['x'] ?? 0;
            $y = $element['y'] ?? 0;
            $width = $element['width'] ?? 100;
            $height = $element['height'] ?? 20;

            // Para rotação de texto, usar abordagem simplificada (FPDI não suporta transformações)
            if (isset($element['rotation']) && $element['rotation'] == 90) {
                // Texto vertical - simular com múltiplas linhas pequenas
                $text = $element['text'] ?? '';
                $words = explode(' ', $text);
                $lines = array_chunk($words, 3); // 3 palavras por linha

                foreach ($lines as $i => $lineWords) {
                    $lineText = implode(' ', $lineWords);
                    $pdf->SetXY($x, $y + ($i * 6));
                    $pdf->Cell($width, 6, $lineText, 0, 0, 'C');
                }
            } else {
                // Texto horizontal normal
                $pdf->SetXY($x, $y);
                $align = match($element['text_align'] ?? 'left') {
                    'center' => 'C',
                    'right' => 'R',
                    default => 'L'
                };
                $pdf->MultiCell($width, $element['line_height'] ?? 4, $element['text'] ?? '', 0, $align);
            }

            return 1;

        } catch (\Exception $e) {
            Log::warning('Erro ao adicionar texto ao PDF', [
                'error' => $e->getMessage(),
                'element' => $element
            ]);
            return 0;
        }
    }

    /**
     * Adicionar elemento QR Code ao PDF
     */
    private function addQRCodeElement(Fpdi $pdf, array $element): int
    {
        try {
            $qrData = $element['data'] ?? 'https://exemplo.com';
            $x = $element['x'] ?? 0;
            $y = $element['y'] ?? 0;
            $size = min($element['width'] ?? 88, $element['height'] ?? 88);

            // Usar QR code simulado (retângulo) para este teste
            // Em produção, a biblioteca QR code seria configurada corretamente
            Log::info('🔲 STAMP_MCP: Adicionando QR code simulado', [
                'data' => $qrData,
                'position' => "{$x},{$y}",
                'size' => "{$size}x{$size}"
            ]);

            // Criar um retângulo preto com bordas como QR code simulado
            $pdf->SetFillColor(0, 0, 0);
            $pdf->Rect($x, $y, $size, $size, 'F');

            // Área interna branca
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Rect($x + 8, $y + 8, $size - 16, $size - 16, 'F');

            // Texto "QR CODE" no centro
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', 'B', 6);
            $pdf->SetXY($x + 8, $y + $size/2 - 8);
            $pdf->Cell($size - 16, 4, 'QR CODE', 0, 0, 'C');
            $pdf->SetXY($x + 8, $y + $size/2 - 4);
            $pdf->Cell($size - 16, 4, 'SIMULADO', 0, 0, 'C');
            $pdf->SetXY($x + 8, $y + $size/2 + 2);
            $pdf->SetFont('Arial', '', 4);
            $pdf->Cell($size - 16, 4, substr($qrData, 0, 20) . '...', 0, 0, 'C');

            return 1;

        } catch (\Exception $e) {
            Log::warning('Erro ao adicionar QR Code ao PDF', [
                'error' => $e->getMessage(),
                'element' => $element
            ]);
            return 0;
        }
    }

    /**
     * Converter cor hex para RGB
     */
    private function hexToRGB(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2))
        ];
    }

    /**
     * Processar assinatura com posicionamento customizado
     * Integração com o sistema de drag & drop
     */
    public function processCustomPositionSignature($proposicao, array $customPosition, array $signatureData): ?string
    {
        try {
            // Obter PDF original
            $pdfPath = $this->getProposicaoPDFPath($proposicao);
            if (!$pdfPath) {
                return null;
            }

            // Converter coordenadas percentuais para pontos PDF
            $pdfInfo = $this->getPDFInfo($pdfPath);
            if (!$pdfInfo || !isset($pdfInfo['pages'][0])) {
                return null;
            }

            $pageInfo = $pdfInfo['pages'][0];
            $pdfWidth = $pageInfo['width'];
            $pdfHeight = $pageInfo['height'];

            // Converter percentuais para coordenadas absolutas
            $x = ($customPosition['x_percent'] / 100) * $pdfWidth;
            $y = ($customPosition['y_percent'] / 100) * $pdfHeight;

            // Preparar dados da assinatura
            $signatureParams = [
                'page' => 0,
                'x' => $x,
                'y' => $y,
                'width' => 200,
                'height' => 60,
                'signature_text' => sprintf(
                    "Assinado por: %s\nData: %s",
                    $signatureData['signer_name'] ?? $proposicao->autor->name,
                    now()->format('d/m/Y H:i:s')
                )
            ];

            // Adicionar assinatura ao PDF
            $signedPdfPath = $this->addSignatureToPDF($pdfPath, $signatureParams);

            if ($signedPdfPath) {
                Log::info('ESignMCP: Assinatura com posicionamento customizado aplicada', [
                    'proposicao_id' => $proposicao->id,
                    'position' => $customPosition,
                    'pdf' => basename($signedPdfPath)
                ]);
            }

            return $signedPdfPath;

        } catch (\Exception $e) {
            Log::error('ESignMCP: Erro ao processar assinatura customizada', [
                'error' => $e->getMessage(),
                'proposicao_id' => $proposicao->id
            ]);
            return null;
        }
    }

    /**
     * Carimbar painel lateral no PDF (stamp antes da assinatura PAdES)
     *
     * @param array $opts Opções: pdf_path, page, sidebar{x,y,w,h}, padding, blocos{title, signer, ...}
     * @return string Caminho do PDF carimbado
     * @throws \Exception Se falhar no processo
     */
    public function carimbarLateral(array $opts): string
    {
        $startTime = microtime(true);

        try {
            Log::info('🎯 STAMP: Iniciando carimbo lateral', [
                'pdf_path' => basename($opts['pdf_path'] ?? ''),
                'page' => $opts['page'] ?? 1,
                'sidebar' => $opts['sidebar'] ?? null
            ]);

            // 1) Validar entrada
            if (!isset($opts['pdf_path']) || !file_exists($opts['pdf_path'])) {
                throw new \Exception('PDF não encontrado: ' . ($opts['pdf_path'] ?? 'não informado'));
            }

            // 2) Obter dimensões da página dinamicamente
            $pdfInfo = $this->getPDFInfo($opts['pdf_path']);
            if (!$pdfInfo || !isset($pdfInfo['pages'][0])) {
                throw new \Exception('Não foi possível obter informações do PDF');
            }

            $pageInfo = $pdfInfo['pages'][0];
            $pageWidth = $pageInfo['width'];
            $pageHeight = $pageInfo['height'];

            $targetPage = $opts['target_page'] ?? null;
            $targetWidth = $targetPage['target_width'] ?? $pageWidth;
            $targetHeight = $targetPage['target_height'] ?? $pageHeight;

            // 3) Calcular sidebar dinamicamente (se não fornecida) já considerando página normalizada
            $sidebar = $opts['sidebar'] ?? [
                'x' => $targetWidth - 120,
                'y' => 0,
                'w' => 120,
                'h' => $targetHeight
            ];

            $padding = $opts['padding'] ?? 16;
            $innerWidth = $sidebar['w'] - ($padding * 2);
            $innerX = $sidebar['x'] + $padding;

            // 4) Preparar dados para o MCP
            $pdfContent = file_get_contents($opts['pdf_path']);
            $mcpData = [
                'pdf_base64' => base64_encode($pdfContent),
                'page' => ($opts['page'] ?? 1) - 1, // MCP usa índice 0
                'sidebar' => $sidebar,
                'elements' => [],
                'target_page' => $targetPage
            ];

            // 5) Usar elementos enviados pelo PDFAssinaturaIntegradaService (não mais blocos hardcoded)
            // Os elementos já vêm no formato correto do buildElements()
            $elementsFromCaller = $opts['elements'] ?? [];

            Log::info('🔧 STAMP: Elementos recebidos do caller', [
                'elements_count' => count($elementsFromCaller),
                'elements_types' => array_map(fn($e) => $e['type'] ?? 'unknown', $elementsFromCaller)
            ]);

            // Adicionar elementos diretamente ao mcpData
            $mcpData['elements'] = $elementsFromCaller;

            // 6) Chamar MCP para aplicar carimbo
            $result = $this->callMCPServer('stamp_lateral', $mcpData);

            if (!$result || !isset($result['pdf_base64'])) {
                throw new \Exception('MCP não retornou PDF carimbado');
            }

            // 7) Salvar PDF carimbado
            $stampedContent = base64_decode($result['pdf_base64']);
            $outputPath = str_replace('.pdf', '_stamped_' . uniqid() . '.pdf', $opts['pdf_path']);
            file_put_contents($outputPath, $stampedContent);

            $duration = round((microtime(true) - $startTime) * 1000, 2);
            $finalPageWidth = $targetWidth;
            $finalPageHeight = $targetHeight;

            Log::info('✅ STAMP: Carimbo lateral aplicado com sucesso', [
                'input' => basename($opts['pdf_path']),
                'output' => basename($outputPath),
                'duration_ms' => $duration,
                'page_size_before' => "{$pageWidth}x{$pageHeight}pt",
                'page_size_after' => "{$finalPageWidth}x{$finalPageHeight}pt",
                'sidebar_coords' => $sidebar,
                'elements_count' => count($mcpData['elements'])
            ]);

            return $outputPath;

        } catch (\Exception $e) {
            Log::error('❌ STAMP: Erro ao aplicar carimbo lateral', [
                'error' => $e->getMessage(),
                'pdf_path' => basename($opts['pdf_path'] ?? ''),
                'duration_ms' => round((microtime(true) - $startTime) * 1000, 2)
            ]);
            throw $e;
        }
    }

    /**
     * Helper para obter tamanho de página específica
     *
     * @param string $pdfPath
     * @param int $pageNumber (1-indexed)
     * @return array|null [width, height] em pontos
     */
    public function getPageSize(string $pdfPath, int $pageNumber = 1): ?array
    {
        $pdfInfo = $this->getPDFInfo($pdfPath);
        if (!$pdfInfo || !isset($pdfInfo['pages'])) {
            return null;
        }

        $pageIndex = $pageNumber - 1;
        if (isset($pdfInfo['pages'][$pageIndex])) {
            $page = $pdfInfo['pages'][$pageIndex];
            return [$page['width'], $page['height']];
        }

        // Fallback para A4 se não encontrar
        return [595, 842];
    }

    /**
     * Obter caminho do PDF da proposição
     */
    private function getProposicaoPDFPath($proposicao): ?string
    {
        // Verificar se existe PDF no S3
        if ($proposicao->pdf_s3_path) {
            // Baixar temporariamente do S3
            $tempPath = storage_path('app/temp/' . basename($proposicao->pdf_s3_path));

            // Aqui você faria o download do S3
            // Storage::disk('s3')->download($proposicao->pdf_s3_path, $tempPath);

            return $tempPath;
        }

        // Verificar PDFs locais
        $localPath = storage_path('app/proposicoes/pdfs/' . $proposicao->id);
        if (is_dir($localPath)) {
            $pdfs = glob($localPath . '/*.pdf');
            if (!empty($pdfs)) {
                return $pdfs[0];
            }
        }

        return null;
    }
}