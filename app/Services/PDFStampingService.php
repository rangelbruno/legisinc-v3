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

            // Save the stamped PDF using Laravel Storage
            $pdfContent = $pdf->Output('', 'S'); // Get as string
            
            if (empty($pdfContent)) {
                throw new \Exception('Failed to generate PDF content from FPDI');
            }
            
            // Convert absolute path to relative path for Storage
            $storagePath = storage_path('app/');
            if (strpos($outputPath, $storagePath) === 0) {
                // Remove the storage/app/ prefix
                $relativePath = substr($outputPath, strlen($storagePath));
                // Remove 'private/' prefix if it exists (Storage adds it automatically for private disk)
                if (strpos($relativePath, 'private/') === 0) {
                    $relativePath = substr($relativePath, 8); // Remove 'private/'
                }
            } else {
                // If outputPath doesn't start with storage path, make it relative
                $relativePath = str_replace(storage_path('app/private/'), '', $outputPath);
            }
            
            // Ensure directory exists
            $directory = dirname($relativePath);
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }
            
            // Save using Laravel Storage
            Log::info('Attempting to save PDF', [
                'relative_path' => $relativePath,
                'content_size' => strlen($pdfContent),
                'directory' => $directory,
                'directory_exists' => Storage::exists($directory)
            ]);
            
            $result = Storage::put($relativePath, $pdfContent);
            
            Log::info('Storage::put result', [
                'result' => $result,
                'relative_path' => $relativePath,
                'file_exists_after_put' => Storage::exists($relativePath)
            ]);
            
            if (!$result) {
                Log::error('Storage::put failed', [
                    'relative_path' => $relativePath,
                    'last_error' => error_get_last()
                ]);
                throw new \Exception('Failed to save signed PDF');
            }
            
            // CRÍTICO: Definir ownership correto para PDFs assinados (www-data:www-data)
            // Use Storage::path para obter o caminho absoluto correto
            $absoluteSignedPath = Storage::path($relativePath);
            
            // Aguardar um momento para garantir que o arquivo foi escrito
            usleep(100000); // 100ms
            
            if (file_exists($absoluteSignedPath)) {
                // Não podemos mudar ownership no container, apenas permissões
                @chmod($absoluteSignedPath, 0666);
                Log::info('Permissões ajustadas para PDF assinado', [
                    'path' => $absoluteSignedPath,
                    'exists' => file_exists($absoluteSignedPath),
                    'permissions' => decoct(fileperms($absoluteSignedPath) & 0777)
                ]);
            } else {
                Log::warning('Arquivo não encontrado após Storage::put', [
                    'absolute_path' => $absoluteSignedPath,
                    'relative_path' => $relativePath,
                    'storage_exists' => Storage::exists($relativePath)
                ]);
            }

            if (!Storage::exists($relativePath) && !file_exists($absoluteSignedPath)) {
                throw new \Exception('Failed to create signed PDF - file does not exist after save');
            }

            Log::info('Signature stamp applied successfully', [
                'original_size' => filesize($existingPdfPath),
                'signed_size' => Storage::size($relativePath)
            ]);

            return $absoluteSignedPath;

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
     * 
     * NOTE: This method is now primarily a FALLBACK. The preferred approach is to use 
     * ProtocoloRTFService to replace variables in RTF templates before PDF generation.
     * This visual stamping should only be used when RTF variable replacement fails.
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

            // Save the stamped PDF using Laravel Storage
            $pdfContent = $pdf->Output('', 'S'); // Get as string
            
            if (empty($pdfContent)) {
                throw new \Exception('Failed to generate PDF content from FPDI');
            }
            
            // Convert absolute path to relative path for Storage
            $storagePath = storage_path('app/');
            if (strpos($outputPath, $storagePath) === 0) {
                // Remove the storage/app/ prefix
                $relativePath = substr($outputPath, strlen($storagePath));
                // Remove 'private/' prefix if it exists (Storage adds it automatically for private disk)
                if (strpos($relativePath, 'private/') === 0) {
                    $relativePath = substr($relativePath, 8); // Remove 'private/'
                }
            } else {
                // If outputPath doesn't start with storage path, make it relative
                $relativePath = str_replace(storage_path('app/private/'), '', $outputPath);
            }
            
            // Ensure directory exists
            $directory = dirname($relativePath);
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }
            
            // Save using Laravel Storage
            if (!Storage::put($relativePath, $pdfContent)) {
                throw new \Exception('Failed to save protocoled PDF');
            }
            
            // CRÍTICO: Definir ownership correto para PDFs protocolados (www-data:www-data)
            // Use Storage::path para obter o caminho absoluto correto
            $absoluteProtocoledPath = Storage::path($relativePath);
            if (file_exists($absoluteProtocoledPath)) {
                // Não podemos mudar ownership no container, apenas permissões
                @chmod($absoluteProtocoledPath, 0666);
                Log::info('Permissões ajustadas para PDF protocolado', [
                    'path' => $absoluteProtocoledPath,
                    'permissions' => decoct(fileperms($absoluteProtocoledPath) & 0777)
                ]);
            }

            if (!Storage::exists($relativePath)) {
                throw new \Exception('Failed to create protocoled PDF');
            }

            Log::info('Protocol stamp applied successfully', [
                'protocol_number' => $protocolNumber,
                'original_size' => filesize($existingPdfPath),
                'protocoled_size' => Storage::size($relativePath)
            ]);

            return $absoluteProtocoledPath;

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
        // Calcular posição mais precisa baseada no tamanho da página
        $margemBase = 20;
        $alturaAssinatura = 30;
        $larguraAssinatura = min(160, $pageSize['width'] - 2 * $margemBase);
        
        // Posicionar no canto inferior direito, mas com margem adequada
        $x = $pageSize['width'] - $larguraAssinatura - $margemBase;
        $y = $pageSize['height'] - $alturaAssinatura - $margemBase;
        
        // Ajustar se a posição ficar muito baixa (para páginas pequenas)
        if ($y < 50) {
            $y = $pageSize['height'] - 50;
        }

        // Background com cor mais suave e bordas arredondadas simuladas
        $pdf->SetFillColor(248, 249, 250); // Cinza muito claro
        $pdf->Rect($x, $y, $larguraAssinatura, $alturaAssinatura, 'F');

        // Borda mais elegante
        $pdf->SetDrawColor(200, 200, 200); // Cinza claro
        $pdf->SetLineWidth(0.3);
        $pdf->Rect($x, $y, $larguraAssinatura, $alturaAssinatura, 'D');

        // Título da seção com fonte em negrito
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(60, 60, 60); // Cinza escuro
        $pdf->SetXY($x + 5, $y + 3);
        $pdf->Cell($larguraAssinatura - 10, 4, 'DOCUMENTO ASSINADO DIGITALMENTE', 0, 1, 'C');

        // Linha divisória
        $pdf->SetDrawColor(220, 220, 220);
        $pdf->Line($x + 10, $y + 9, $x + $larguraAssinatura - 10, $y + 9);

        // Informações da assinatura com fonte normal
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetTextColor(80, 80, 80);
        
        $nomeAssinante = $signatureData['nome_assinante'] ?? 'Usuário do Sistema';
        $dataAssinatura = now()->format('d/m/Y \à\s H:i');
        $tipoAssinatura = $this->formatarTipoCertificado($signatureData['tipo_certificado'] ?? 'DIGITAL');
        
        $pdf->SetXY($x + 5, $y + 12);
        $pdf->Cell($larguraAssinatura - 10, 3, "Por: {$nomeAssinante}", 0, 1, 'L');
        
        $pdf->SetXY($x + 5, $y + 16);
        $pdf->Cell($larguraAssinatura - 10, 3, "Em: {$dataAssinatura}", 0, 1, 'L');
        
        $pdf->SetXY($x + 5, $y + 20);
        $pdf->Cell($larguraAssinatura - 10, 3, "Certificado: {$tipoAssinatura}", 0, 1, 'L');
        
        // Hash/ID de verificação se disponível
        if (isset($signatureData['identificador']) && !empty($signatureData['identificador'])) {
            $pdf->SetFont('Arial', '', 6);
            $pdf->SetTextColor(120, 120, 120);
            $pdf->SetXY($x + 5, $y + 24);
            $identificador = substr($signatureData['identificador'], 0, 20) . '...';
            $pdf->Cell($larguraAssinatura - 10, 3, "ID: {$identificador}", 0, 1, 'L');
        }
        
        // Restaurar configurações padrão
        $pdf->SetLineWidth(0.2);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetTextColor(0, 0, 0);
    }
    
    /**
     * Formatar tipo de certificado para exibição
     */
    private function formatarTipoCertificado(string $tipo): string
    {
        $tipos = [
            'PFX' => 'ICP-Brasil PFX',
            'A1' => 'ICP-Brasil A1',
            'A3' => 'ICP-Brasil A3',
            'SIMULADO' => 'Desenvolvimento',
            'DIGITAL' => 'Assinatura Digital'
        ];
        
        return $tipos[$tipo] ?? $tipo;
    }

    /**
     * Add protocol stamp to page (replaces placeholder if found)
     */
    private function addProtocolStampToPage(Fpdi $pdf, string $protocolNumber, array $additionalData, array $pageSize): void
    {
        // Set font for protocol number
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTextColor(0, 0, 0);

        // Position protocol stamp at top-right corner (não sobrepor o título)
        $x = $pageSize['width'] - 80;
        $y = 10; // Bem no topo da página

        // Draw a subtle border box for the protocol
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->SetFillColor(250, 250, 250);
        $pdf->Rect($x - 5, $y - 2, 75, 15, 'FD');

        // Add protocol label and number
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY($x, $y);
        $pdf->Cell(65, 4, "PROTOCOLO", 0, 1, 'C');
        
        // Add protocol number
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetXY($x, $y + 4);
        // Formatar o número do protocolo
        $numeroFormatado = $this->formatarNumeroProtocolo($protocolNumber);
        $pdf->Cell(65, 4, $numeroFormatado, 0, 1, 'C');
        
        // Add protocol date if available (smaller, below)
        if (isset($additionalData['data_protocolo'])) {
            $pdf->SetFont('Arial', '', 7);
            $pdf->SetXY($x, $y + 8);
            $pdf->Cell(65, 3, $additionalData['data_protocolo'], 0, 1, 'C');
        }

        // IMPORTANTE: Não cobrir o título do documento
        // O carimbo fica discreto no canto superior direito
    }
    
    /**
     * Formatar número de protocolo para exibição
     */
    private function formatarNumeroProtocolo(string $protocolNumber): string
    {
        // Remove prefixos longos como "projeto_lei_ordinaria/2025/"
        // e mantém apenas o número essencial
        if (strpos($protocolNumber, '/') !== false) {
            $partes = explode('/', $protocolNumber);
            // Pegar ano e número
            if (count($partes) >= 2) {
                $ano = $partes[count($partes) - 2];
                $numero = $partes[count($partes) - 1];
                return "{$numero}/{$ano}";
            }
        }
        
        return $protocolNumber;
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