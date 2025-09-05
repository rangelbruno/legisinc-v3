<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PDFStampingService
{
    /**
     * Apply digital signature stamp to existing PDF
     */
    public function applySignatureStamp(string $existingPdfPath, array $signatureData): ?string
    {
        try {
            Log::info('Applying signature stamp to existing PDF', [
                'pdf_path' => $existingPdfPath,
                'signature_type' => $signatureData['tipo_certificado'] ?? 'unknown'
            ]);

            // Validate input PDF exists
            if (!file_exists($existingPdfPath)) {
                throw new \Exception('PDF file not found: ' . $existingPdfPath);
            }

            // Create output path
            $outputPath = $this->generateSignedPdfPath($existingPdfPath);

            // Initialize FPDI
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($existingPdfPath);

            Log::info('PDF loaded for signature stamping', [
                'page_count' => $pageCount,
                'output_path' => $outputPath
            ]);

            // Import all pages from original PDF
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                
                // Add page with same size as original
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                // Add signature stamp to last page
                if ($pageNo === $pageCount) {
                    $this->addSignatureStampToPage($pdf, $signatureData, $size);
                }
            }

            // Save the stamped PDF
            $pdf->Output($outputPath, 'F');

            if (!file_exists($outputPath)) {
                throw new \Exception('Failed to create signed PDF');
            }

            Log::info('Signature stamp applied successfully', [
                'original_size' => filesize($existingPdfPath),
                'signed_size' => filesize($outputPath)
            ]);

            return $outputPath;

        } catch (\Exception $e) {
            Log::error('Error applying signature stamp', [
                'pdf_path' => $existingPdfPath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Apply protocol number stamp to existing PDF
     */
    public function applyProtocolStamp(string $existingPdfPath, string $protocolNumber, array $additionalData = []): ?string
    {
        try {
            Log::info('Applying protocol stamp to existing PDF', [
                'pdf_path' => $existingPdfPath,
                'protocol_number' => $protocolNumber
            ]);

            // Validate input PDF exists
            if (!file_exists($existingPdfPath)) {
                throw new \Exception('PDF file not found: ' . $existingPdfPath);
            }

            // Create output path
            $outputPath = $this->generateProtocoledPdfPath($existingPdfPath, $protocolNumber);

            // Initialize FPDI
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($existingPdfPath);

            Log::info('PDF loaded for protocol stamping', [
                'page_count' => $pageCount,
                'output_path' => $outputPath
            ]);

            // Import all pages and replace protocol placeholder
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                
                // Add page with same size as original
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                // Add protocol stamp to first page (where header/number is located)
                if ($pageNo === 1) {
                    $this->addProtocolStampToPage($pdf, $protocolNumber, $additionalData, $size);
                }
            }

            // Save the stamped PDF
            $pdf->Output($outputPath, 'F');

            if (!file_exists($outputPath)) {
                throw new \Exception('Failed to create protocoled PDF');
            }

            Log::info('Protocol stamp applied successfully', [
                'protocol_number' => $protocolNumber,
                'original_size' => filesize($existingPdfPath),
                'protocoled_size' => filesize($outputPath)
            ]);

            return $outputPath;

        } catch (\Exception $e) {
            Log::error('Error applying protocol stamp', [
                'pdf_path' => $existingPdfPath,
                'protocol_number' => $protocolNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Apply both signature and protocol stamps in sequence
     */
    public function applyBothStamps(string $originalPdfPath, array $signatureData, string $protocolNumber): ?string
    {
        try {
            Log::info('Applying both signature and protocol stamps', [
                'original_pdf' => $originalPdfPath,
                'protocol_number' => $protocolNumber
            ]);

            // Step 1: Apply signature stamp
            $signedPdfPath = $this->applySignatureStamp($originalPdfPath, $signatureData);
            if (!$signedPdfPath) {
                throw new \Exception('Failed to apply signature stamp');
            }

            // Step 2: Apply protocol stamp to signed PDF
            $finalPdfPath = $this->applyProtocolStamp($signedPdfPath, $protocolNumber);
            if (!$finalPdfPath) {
                throw new \Exception('Failed to apply protocol stamp');
            }

            // Clean up intermediate file
            if (file_exists($signedPdfPath) && $signedPdfPath !== $finalPdfPath) {
                unlink($signedPdfPath);
            }

            Log::info('Both stamps applied successfully', [
                'final_pdf' => $finalPdfPath,
                'final_size' => filesize($finalPdfPath)
            ]);

            return $finalPdfPath;

        } catch (\Exception $e) {
            Log::error('Error applying both stamps', [
                'original_pdf' => $originalPdfPath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Add signature visual stamp to a page
     */
    private function addSignatureStampToPage(Fpdi $pdf, array $signatureData, array $pageSize): void
    {
        // Set font for signature
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(0, 0, 0);

        // Position signature at bottom of page
        $x = 20;
        $y = $pageSize['height'] - 40;
        $width = $pageSize['width'] - 40;

        // Draw signature box background
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Rect($x, $y, $width, 25, 'F');

        // Draw border
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->Rect($x, $y, $width, 25, 'D');

        // Add signature text
        $pdf->SetXY($x + 5, $y + 3);
        $pdf->Cell(0, 4, 'ASSINATURA DIGITAL', 0, 1, 'L');
        
        $nomeAssinante = $signatureData['nome_assinante'] ?? 'Usuário';
        $dataAssinatura = now()->format('d/m/Y H:i');
        $tipoAssinatura = $signatureData['tipo_certificado'] ?? 'DIGITAL';
        
        $pdf->SetXY($x + 5, $y + 8);
        $pdf->Cell(0, 4, "Assinado eletronicamente por: {$nomeAssinante}", 0, 1, 'L');
        
        $pdf->SetXY($x + 5, $y + 13);
        $pdf->Cell(0, 4, "Data/Hora: {$dataAssinatura}", 0, 1, 'L');
        
        $pdf->SetXY($x + 5, $y + 18);
        $pdf->Cell(0, 4, "Tipo: {$tipoAssinatura} | ID: {$signatureData['identificador']}", 0, 1, 'L');
    }

    /**
     * Add protocol stamp to page (replaces placeholder if found)
     */
    private function addProtocolStampToPage(Fpdi $pdf, string $protocolNumber, array $additionalData, array $pageSize): void
    {
        // Set font for protocol number
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);

        // Position protocol number at top-right area (common location for protocol numbers)
        $x = $pageSize['width'] - 120;
        $y = 30;

        // Draw white background to cover any placeholder text
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Rect($x - 5, $y - 2, 110, 20, 'F');

        // Add protocol number
        $pdf->SetXY($x, $y);
        $pdf->Cell(100, 8, "Protocolo: {$protocolNumber}", 0, 1, 'C');
        
        // Add protocol date if available
        if (isset($additionalData['data_protocolo'])) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetXY($x, $y + 10);
            $pdf->Cell(100, 6, $additionalData['data_protocolo'], 0, 1, 'C');
        }

        // Alternative: Try to replace [AGUARDANDO PROTOCOLO] placeholder
        // Note: FPDI doesn't support text replacement in existing PDFs directly
        // This is a visual overlay approach
    }

    /**
     * Generate path for signed PDF
     */
    private function generateSignedPdfPath(string $originalPath): string
    {
        $pathInfo = pathinfo($originalPath);
        $baseName = $pathInfo['filename'];
        $extension = $pathInfo['extension'];
        $directory = $pathInfo['dirname'];
        
        return $directory . '/' . $baseName . '_assinado_' . time() . '.' . $extension;
    }

    /**
     * Generate path for protocoled PDF
     */
    private function generateProtocoledPdfPath(string $originalPath, string $protocolNumber): string
    {
        $pathInfo = pathinfo($originalPath);
        $baseName = $pathInfo['filename'];
        $extension = $pathInfo['extension'];
        $directory = $pathInfo['dirname'];
        
        // Clean protocol number for filename
        $cleanProtocol = preg_replace('/[^a-zA-Z0-9_-]/', '_', $protocolNumber);
        
        return $directory . '/' . $baseName . '_protocolado_' . $cleanProtocol . '.' . $extension;
    }

    /**
     * Get relative path for Laravel storage
     */
    public function getRelativePath(string $absolutePath): string
    {
        $storagePath = storage_path('app/');
        return str_replace($storagePath, '', $absolutePath);
    }

    /**
     * Check if PDF has placeholder text that needs replacement
     */
    public function hasProtocolPlaceholder(string $pdfPath): bool
    {
        try {
            // Use pdftotext to extract text content
            $command = "pdftotext " . escapeshellarg($pdfPath) . " -";
            $content = shell_exec($command);
            
            if ($content) {
                return stripos($content, '[AGUARDANDO PROTOCOLO]') !== false;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::warning('Could not check for protocol placeholder', [
                'pdf_path' => $pdfPath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Check if PDF already has signature
     */
    public function hasSignature(string $pdfPath): bool
    {
        try {
            $command = "pdftotext " . escapeshellarg($pdfPath) . " -";
            $content = shell_exec($command);
            
            if ($content) {
                return stripos($content, 'ASSINATURA DIGITAL') !== false ||
                       stripos($content, 'Assinado eletronicamente') !== false;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::warning('Could not check for signature', [
                'pdf_path' => $pdfPath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}