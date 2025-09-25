<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Proposicao;

class PadesSignatureAppearanceService
{
    /**
     * Create visual signature panel for PAdES signature WITHOUT changing page dimensions
     * Follows strict requirements: no MediaBox/CropBox changes, elements within current page bounds
     *
     * @param string $pdfPath Path to existing PDF
     * @param Proposicao $proposicao Proposition model with data for template variables
     * @param array $signatureData Signature data including signer info
     * @param string $verificationUrl URL for QR code verification
     * @return string|null Path to PDF with signature elements
     */
    public function createSignaturePanel(string $pdfPath, Proposicao $proposicao, array $signatureData, string $verificationUrl): ?string
    {
        try {
            Log::info('🎨 PAdES Appearance: Criando painel de assinatura visual', [
                'pdf_path' => basename($pdfPath),
                'signer_name' => $signatureData['nome_assinante'] ?? 'N/A',
                'verification_url' => $verificationUrl,
                'proposicao_id' => $proposicao->id
            ]);

            if (!file_exists($pdfPath)) {
                throw new \Exception('PDF original não encontrado: ' . $pdfPath);
            }

            // Initialize FPDI
            $pdf = new Fpdi();
            $pdf->setSourceFile($pdfPath);

            // Import first page to get dimensions
            $templateId = $pdf->importPage(1);
            $size = $pdf->getTemplateSize($templateId);

            Log::info('📏 Dimensões do PDF original', [
                'width' => $size['width'],
                'height' => $size['height']
            ]);

            // Import all pages with SAME dimensions (no MediaBox changes)
            $pageCount = $pdf->setSourceFile($pdfPath);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                // Add page with EXACT same dimensions as original
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);

                // Add signature elements ONLY on first page
                if ($pageNo === 1) {
                    $this->addSignatureElements($pdf, $proposicao, $signatureData, $verificationUrl, $size);
                }
            }

            // Generate output path
            $outputPath = preg_replace('/\.pdf$/i', '_pades_signed_' . time() . '.pdf', $pdfPath);

            // Save PDF
            $pdf->Output('F', $outputPath);

            if (!file_exists($outputPath)) {
                throw new \Exception('Falha ao salvar PDF com painel de assinatura');
            }

            Log::info('✅ PAdES Appearance: Painel de assinatura criado com sucesso', [
                'original_size' => filesize($pdfPath),
                'signed_size' => filesize($outputPath),
                'output_path' => basename($outputPath)
            ]);

            return $outputPath;

        } catch (\Exception $e) {
            Log::error('❌ PAdES Appearance: Erro ao criar painel', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    /**
     * Add signature elements within page boundaries (Elemento A + Elemento B)
     */
    private function addSignatureElements(Fpdi $pdf, Proposicao $proposicao, array $signatureData, string $verificationUrl, array $pageSize): void
    {
        $pageWidth = $pageSize['width'];
        $pageHeight = $pageSize['height'];

        Log::info('🎯 Adicionando elementos de assinatura', [
            'page_width' => $pageWidth,
            'page_height' => $pageHeight
        ]);

        // ELEMENTO A: Painel de assinatura (lateral direita)
        $this->addSignaturePanel($pdf, $proposicao, $signatureData, $verificationUrl, $pageWidth, $pageHeight);

        // ELEMENTO B: Faixa vertical com texto dinâmico
        $this->addVerticalBand($pdf, $proposicao, $signatureData, $pageWidth, $pageHeight);
    }

    /**
     * ELEMENTO A - Painel de assinatura na lateral direita
     */
    private function addSignaturePanel(Fpdi $pdf, Proposicao $proposicao, array $signatureData, string $verificationUrl, float $pageWidth, float $pageHeight): void
    {
        // Panel dimensions as per requirements
        $panelWidth = 130; // pt (±46mm)
        $margin = 18; // pt

        // Position on right margin within page bounds
        $panelX = $pageWidth - $panelWidth - $margin;
        $panelY = $margin;
        $panelHeight = $pageHeight - (2 * $margin);

        Log::info('📐 Elemento A - Painel de assinatura', [
            'panel_x' => $panelX,
            'panel_y' => $panelY,
            'panel_width' => $panelWidth,
            'panel_height' => $panelHeight
        ]);

        // Draw white background with light gray border
        $pdf->SetFillColor(255, 255, 255); // White background
        $pdf->SetDrawColor(200, 200, 200); // Light gray border
        $pdf->SetLineWidth(0.5);
        $pdf->Rect($panelX, $panelY, $panelWidth, $panelHeight, 'DF');

        // Content positioning
        $contentX = $panelX + 8; // 8pt inner margin
        $currentY = $panelY + 12; // Start 12pt from top
        $contentWidth = $panelWidth - 16; // Account for both side margins

        // Title: "ASSINADO ELETRONICAMENTE" (bold, centered)
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY($contentX, $currentY);

        $titleWidth = $pdf->GetStringWidth('ASSINADO ELETRONICAMENTE');
        $titleX = $panelX + ($panelWidth - $titleWidth) / 2; // Center in panel
        $pdf->SetXY($titleX, $currentY);
        $pdf->Cell(0, 6, 'ASSINADO ELETRONICAMENTE', 0, 1, 'C');
        $currentY += 12;

        // Signer name and role
        $signerName = $signatureData['nome_assinante'] ?? 'N/A';
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetXY($contentX, $currentY);
        $pdf->MultiCell($contentWidth, 5, $signerName, 0, 'C');
        $currentY += 10;

        // Date/time in Brazil timezone
        $signatureDate = now()->setTimezone('America/Sao_Paulo');
        $dateString = $signatureDate->format('d/m/Y H:i:s') . ' UTC-3';

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY($contentX, $currentY);
        $pdf->MultiCell($contentWidth, 4, $dateString, 0, 'C');
        $currentY += 10;

        // SHA-256 hash (abbreviated)
        $documentHash = $signatureData['checksum'] ?? hash('sha256', file_get_contents($pdfPath ?? ''));
        $hashAbbreviated = 'SHA-256: ' . strtoupper(substr($documentHash, 0, 16)) . '...';

        $pdf->SetFont('Arial', '', 7);
        $pdf->SetXY($contentX, $currentY);
        $pdf->MultiCell($contentWidth, 4, $hashAbbreviated, 0, 'C');
        $currentY += 15;

        // QR Code centered
        $this->addQRCode($pdf, $verificationUrl, $contentX, $currentY, $contentWidth);
        $currentY += 50; // QR code height + margin

        // Verification URL caption (if fits)
        $pdf->SetFont('Arial', '', 6);
        $pdf->SetXY($contentX, $currentY);
        $pdf->MultiCell($contentWidth, 3, 'Verifique em: ' . $verificationUrl, 0, 'C');
    }

    /**
     * ELEMENTO B - Faixa vertical com texto dinâmico
     */
    private function addVerticalBand(Fpdi $pdf, Proposicao $proposicao, array $signatureData, float $pageWidth, float $pageHeight): void
    {
        // Vertical band dimensions
        $bandWidth = 22; // pt (narrow band as required)
        $bandX = $pageWidth - $bandWidth - 2; // 2pt from right edge
        $bandY = 10; // 10pt from top
        $bandHeight = $pageHeight - 20; // 10pt margins top/bottom

        Log::info('📐 Elemento B - Faixa vertical', [
            'band_x' => $bandX,
            'band_y' => $bandY,
            'band_width' => $bandWidth,
            'band_height' => $bandHeight
        ]);

        // Draw white background with light gray border
        $pdf->SetFillColor(255, 255, 255); // White background
        $pdf->SetDrawColor(204, 204, 204); // #CCCCCC border
        $pdf->SetLineWidth(0.3);
        $pdf->Rect($bandX, $bandY, $bandWidth, $bandHeight, 'DF');

        // Prepare dynamic text with template variables
        $dynamicText = $this->buildDynamicText($proposicao, $signatureData);

        // Add rotated text (90° - base to top)
        $this->addVerticalText($pdf, $dynamicText, $bandX, $bandY, $bandWidth, $bandHeight);
    }

    /**
     * Build dynamic text with template variables substitution
     */
    private function buildDynamicText(Proposicao $proposicao, array $signatureData): string
    {
        // Generate validation code (format: A7CA-9537-1505-BD94)
        $validationCode = $this->generateValidationCode();

        // Build dynamic text with actual data
        $texto = sprintf(
            "INDICAÇÃO Nº %s/%s - Protocolo nº %s recebido em %s - " .
            "Esta é uma cópia do original assinado digitalmente por %s " .
            "Para validar o documento, leia o código QR ou acesse https://sistema.camaracaragua.sp.gov.br/conferir_assinatura " .
            "e informe o código %s",
            $proposicao->numero ?? 'N/A',
            $proposicao->ano ?? date('Y'),
            $proposicao->numero_protocolo ?? 'PENDENTE',
            $proposicao->data_protocolo ? $proposicao->data_protocolo->format('d/m/Y H:i') : 'PENDENTE',
            $signatureData['nome_assinante'] ?? 'N/A',
            $validationCode
        );

        // Store validation code for later use
        $proposicao->update(['codigo_validacao' => $validationCode]);

        return $texto;
    }

    /**
     * Add vertical (rotated 90°) text to the band
     */
    private function addVerticalText(Fpdi $pdf, string $text, float $x, float $y, float $width, float $height): void
    {
        // For simplicity in this implementation, we'll add horizontal text in small font
        // In production, you would implement proper text rotation or use a library that supports it

        // Set font (starting with 7pt for vertical band)
        $fontSize = 7;
        $pdf->SetFont('Arial', '', $fontSize);
        $pdf->SetTextColor(50, 50, 50); // Dark gray

        // Calculate text position within band
        $textX = $x + 2; // 2pt from left edge of band
        $textY = $y + 5; // Start 5pt from top
        $textWidth = $width - 4; // Leave 2pt margins on both sides

        // Break text into smaller chunks for better fit in narrow band
        $words = explode(' ', $text);
        $currentLine = '';
        $currentY = $textY;
        $lineHeight = 5; // Reduced line height for narrow band

        foreach ($words as $word) {
            $testLine = empty($currentLine) ? $word : $currentLine . ' ' . $word;

            // Check if line fits in band width
            if ($pdf->GetStringWidth($testLine) <= ($textWidth)) {
                $currentLine = $testLine;
            } else {
                // Print current line and start new one
                if (!empty($currentLine)) {
                    // Use special formatting for key elements
                    if (strpos($currentLine, 'INDICAÇÃO') !== false) {
                        $pdf->SetFont('Arial', 'B', $fontSize);
                    } elseif (preg_match('/[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}/', $currentLine)) {
                        $pdf->SetFont('Arial', 'B', $fontSize);
                    } else {
                        $pdf->SetFont('Arial', '', $fontSize);
                    }

                    $pdf->SetXY($textX, $currentY);
                    $pdf->Cell($textWidth, $lineHeight, $currentLine, 0, 0, 'L');
                    $currentY += $lineHeight;
                }
                $currentLine = $word;
            }

            // Check if we're running out of vertical space
            if ($currentY > ($y + $height - $lineHeight)) {
                break;
            }
        }

        // Print last line
        if (!empty($currentLine) && $currentY <= ($y + $height - $lineHeight)) {
            if (strpos($currentLine, 'INDICAÇÃO') !== false) {
                $pdf->SetFont('Arial', 'B', $fontSize);
            } elseif (preg_match('/[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}/', $currentLine)) {
                $pdf->SetFont('Arial', 'B', $fontSize);
            } else {
                $pdf->SetFont('Arial', '', $fontSize);
            }

            $pdf->SetXY($textX, $currentY);
            $pdf->Cell($textWidth, $lineHeight, $currentLine, 0, 0, 'L');
        }

        Log::info('📝 Texto dinâmico adicionado à faixa vertical', [
            'band_position' => "x:{$x}, y:{$y}",
            'band_size' => "w:{$width}, h:{$height}",
            'text_length' => strlen($text)
        ]);
    }

    /**
     * Add QR Code (simplified version - you may need a QR library)
     */
    private function addQRCode(Fpdi $pdf, string $url, float $x, float $y, float $width): void
    {
        // For now, add a placeholder rectangle
        // In production, use a proper QR code library like endroid/qr-code
        $qrSize = min(40, $width - 10); // Max 40pt, with margins
        $qrX = $x + (($width - $qrSize) / 2); // Center horizontally

        $pdf->SetFillColor(240, 240, 240); // Light gray placeholder
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->Rect($qrX, $y, $qrSize, $qrSize, 'DF');

        // Add "QR" text in center
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetXY($qrX, $y + ($qrSize / 2) - 2);
        $pdf->Cell($qrSize, 4, 'QR', 0, 0, 'C');

        Log::info('📱 QR Code placeholder adicionado', [
            'url' => $url,
            'position' => "x:{$qrX}, y:{$y}",
            'size' => $qrSize
        ]);
    }

    /**
     * Generate validation code in format A7CA-9537-1505-BD94
     */
    private function generateValidationCode(): string
    {
        $segments = [];
        for ($i = 0; $i < 4; $i++) {
            $segment = '';
            for ($j = 0; $j < 4; $j++) {
                if ($j < 2) {
                    // First 2 chars: letters or numbers
                    $segment .= strtoupper(dechex(rand(10, 15))); // A-F
                } else {
                    // Last 2 chars: numbers
                    $segment .= rand(0, 9);
                }
            }
            $segments[] = $segment;
        }

        return implode('-', $segments);
    }
}