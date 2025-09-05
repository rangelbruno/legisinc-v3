<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class DocumentConversionService
{
    private int $circuitBreakerFailures = 0;
    private ?int $circuitBreakerLastFailure = null;
    private const CIRCUIT_BREAKER_THRESHOLD = 3;
    private const CIRCUIT_BREAKER_TIMEOUT = 60;
    
    // Tipos de documento oficiais que NÃO podem usar DomPDF
    private const OFFICIAL_STATUSES = ['aprovado', 'protocolado', 'assinado', 'aprovado_assinatura'];

    /**
     * Converte documento para PDF com hardening completo
     */
    public function convertToPDF(string $inputPath, string $outputPath, ?string $proposicaoStatus = null): array
    {
        $startTime = microtime(true);
        $result = [
            'success' => false, 
            'converter' => null, 
            'error' => null, 
            'duration' => 0,
            'input_bytes' => 0,
            'output_bytes' => 0
        ];

        try {
            // Verificar se arquivo existe
            if (!Storage::exists($inputPath)) {
                throw new \Exception("Arquivo fonte não encontrado: {$inputPath}");
            }

            $inputContent = Storage::get($inputPath);
            $result['input_bytes'] = strlen($inputContent);

            // Para status oficiais, NUNCA usar DomPDF
            $converters = $this->getConverterPriority($proposicaoStatus);
            
            // Normalizar para DOCX como formato canônico
            $workingPath = $this->ensureDocxFormat($inputPath);
            
            foreach ($converters as $converter) {
                if ($this->tryConverter($converter, $workingPath, $outputPath)) {
                    $result['success'] = true;
                    $result['converter'] = $converter;
                    
                    if (Storage::exists($outputPath)) {
                        $result['output_bytes'] = Storage::size($outputPath);
                    }
                    break;
                }
            }

            if (!$result['success']) {
                if (in_array($proposicaoStatus, self::OFFICIAL_STATUSES)) {
                    throw new \Exception('Conversão falhou para documento oficial - DomPDF não permitido');
                }
                throw new \Exception('Todos os conversores falharam');
            }

        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            
            // Log estruturado para observabilidade
            Log::error('DocumentConversion failed', [
                'input' => $inputPath,
                'output' => $outputPath,
                'status' => $proposicaoStatus,
                'error' => $e->getMessage(),
                'input_bytes' => $result['input_bytes'],
                'duration_ms' => round((microtime(true) - $startTime) * 1000, 2)
            ]);
        } finally {
            $result['duration'] = round((microtime(true) - $startTime) * 1000, 2);
        }

        return $result;
    }

    /**
     * Determinar prioridade de conversores baseado no status
     */
    private function getConverterPriority(?string $status): array
    {
        // Para documentos oficiais, NUNCA usar DomPDF
        if (in_array($status, self::OFFICIAL_STATUSES)) {
            return ['onlyoffice', 'libreoffice']; // Só conversores que preservam formatação
        }
        
        // Para rascunhos, permitir DomPDF como último recurso
        return explode(',', env('PDF_CONVERTER_PRIORITY', 'onlyoffice,libreoffice,dompdf'));
    }

    /**
     * Tenta um conversor específico
     */
    private function tryConverter(string $converter, string $inputPath, string $outputPath): bool
    {
        switch ($converter) {
            case 'onlyoffice':
                return $this->convertWithOnlyOffice($inputPath, $outputPath);
            case 'libreoffice':
                return $this->convertWithLibreOffice($inputPath, $outputPath);
            case 'dompdf':
                return $this->convertWithDomPDF($inputPath, $outputPath);
            default:
                return false;
        }
    }

    /**
     * Conversão via OnlyOffice com JWT hardened
     */
    private function convertWithOnlyOffice(string $inputPath, string $outputPath): bool
    {
        if ($this->isCircuitBreakerOpen()) {
            Log::debug('OnlyOffice circuit breaker is open');
            return false;
        }

        try {
            $content = Storage::get($inputPath);
            $fileSize = strlen($content);
            $maxSize = env('PDF_MAX_FILE_SIZE', 50000000);

            if ($fileSize > $maxSize) {
                Log::warning('File too large for OnlyOffice conversion', [
                    'size' => $fileSize,
                    'max' => $maxSize
                ]);
                return false;
            }

            $client = new Client([
                'timeout' => env('ONLYOFFICE_TIMEOUT', 60),
                'connect_timeout' => 10,
            ]);

            // Preparar payload com JWT melhorado
            $now = time();
            $payload = [
                'async' => false,
                'filetype' => pathinfo($inputPath, PATHINFO_EXTENSION),
                'outputtype' => 'pdf',
                'title' => basename($inputPath),
                'key' => hash('sha256', $content . $now),
            ];

            // Arquivo pequeno: enviar inline
            if ($fileSize <= 10 * 1024 * 1024) {
                $payload['file'] = base64_encode($content);
            } else {
                // Arquivo grande: criar URL temporária segura
                $payload['url'] = $this->createSecureTemporaryUrl($inputPath);
            }

            // JWT obrigatório para produção
            $headers = ['Content-Type' => 'application/json'];
            if ($jwtSecret = env('ONLYOFFICE_JWT_SECRET')) {
                $jwt = $this->generateJWTWithClockSkew($payload, $jwtSecret);
                $headers[env('ONLYOFFICE_JWT_HEADER', 'Authorization')] = 'Bearer ' . $jwt;
                $payload['token'] = $jwt;
            }

            // Fazer requisição
            $response = $client->post(
                env('ONLYOFFICE_DOCUMENT_SERVER_URL', 'http://legisinc-onlyoffice') . '/ConvertService.ashx',
                [
                    'json' => $payload,
                    'headers' => $headers,
                ]
            );

            $result = json_decode($response->getBody(), true);

            if (isset($result['fileUrl']) && $result['fileUrl']) {
                $pdfContent = file_get_contents($result['fileUrl']);
                
                if ($pdfContent !== false && strlen($pdfContent) > 1024) {
                    Storage::put($outputPath, $pdfContent);
                    
                    // Reset circuit breaker em caso de sucesso
                    $this->circuitBreakerFailures = 0;
                    
                    Log::info('OnlyOffice conversion successful', [
                        'input_size' => $fileSize,
                        'output_size' => strlen($pdfContent),
                        'output_path' => $outputPath
                    ]);
                    
                    return true;
                }
            }

            Log::warning('OnlyOffice invalid response', ['result' => $result]);
            $this->recordCircuitBreakerFailure();
            return false;

        } catch (RequestException $e) {
            $this->recordCircuitBreakerFailure();
            Log::error('OnlyOffice request failed', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
            return false;
        } catch (\Exception $e) {
            $this->recordCircuitBreakerFailure();
            Log::error('OnlyOffice conversion error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Conversão via LibreOffice headless
     */
    private function convertWithLibreOffice(string $inputPath, string $outputPath): bool
    {
        try {
            // Verificar disponibilidade
            if (!$this->isLibreOfficeAvailable()) {
                return false;
            }

            $inputAbsolute = Storage::path($inputPath);
            $outputAbsolute = Storage::path($outputPath);
            $outputDir = dirname($outputAbsolute);

            // Garantir diretório + permissões
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            // Comando otimizado
            $command = sprintf(
                'XDG_RUNTIME_DIR=/tmp soffice --headless --invisible --nodefault --nolockcheck --nologo --norestore --convert-to pdf:writer_pdf_Export --outdir %s %s 2>&1',
                escapeshellarg($outputDir),
                escapeshellarg($inputAbsolute)
            );

            // Executar com timeout
            $timeout = env('ONLYOFFICE_TIMEOUT', 60);
            exec("timeout {$timeout} {$command}", $output, $returnCode);

            if ($returnCode === 0) {
                // LibreOffice gera PDF com mesmo nome
                $generatedPdf = $outputDir . '/' . pathinfo($inputAbsolute, PATHINFO_FILENAME) . '.pdf';
                
                if (file_exists($generatedPdf)) {
                    if ($generatedPdf !== $outputAbsolute) {
                        rename($generatedPdf, $outputAbsolute);
                    }
                    
                    Log::info('LibreOffice conversion successful', [
                        'input' => $inputPath,
                        'output' => $outputPath,
                        'output_size' => filesize($outputAbsolute)
                    ]);
                    
                    return true;
                }
            }

            Log::warning('LibreOffice conversion failed', [
                'command' => $command,
                'output' => implode('\n', $output),
                'return_code' => $returnCode
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('LibreOffice error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Fallback DomPDF com marca d'água "TEMPORÁRIO"
     */
    private function convertWithDomPDF(string $inputPath, string $outputPath): bool
    {
        try {
            Log::warning('Using DomPDF fallback - formatting will be lost', [
                'input' => $inputPath
            ]);

            $content = $this->extractTextFromDocument($inputPath);
            $html = $this->generateTemporaryPdfHTML($content);

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 96,
            ]);

            Storage::put($outputPath, $pdf->output());

            Log::info('DomPDF conversion completed (TEMPORARY - basic formatting)', [
                'output' => $outputPath
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('DomPDF conversion failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Garantir formato DOCX
     */
    private function ensureDocxFormat(string $inputPath): string
    {
        $extension = pathinfo($inputPath, PATHINFO_EXTENSION);
        
        if (strtolower($extension) === 'docx') {
            return $inputPath;
        }

        // Se for RTF, tentar converter para DOCX primeiro
        if (strtolower($extension) === 'rtf') {
            $docxPath = str_replace('.rtf', '.docx', $inputPath);
            
            if ($this->convertRTFToDocx($inputPath, $docxPath)) {
                return $docxPath;
            }
        }

        return $inputPath; // Retorna original se não conseguir converter
    }

    /**
     * Converter RTF para DOCX usando LibreOffice
     */
    private function convertRTFToDocx(string $rtfPath, string $docxPath): bool
    {
        try {
            if (!$this->isLibreOfficeAvailable()) {
                return false;
            }

            $inputAbsolute = Storage::path($rtfPath);
            $outputDir = dirname(Storage::path($docxPath));

            $command = sprintf(
                'XDG_RUNTIME_DIR=/tmp soffice --headless --convert-to docx --outdir %s %s 2>/dev/null',
                escapeshellarg($outputDir),
                escapeshellarg($inputAbsolute)
            );

            exec($command, $output, $returnCode);
            
            return $returnCode === 0 && Storage::exists($docxPath);

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Verificar se LibreOffice está disponível
     */
    private function isLibreOfficeAvailable(): bool
    {
        static $available = null;
        
        if ($available === null) {
            exec('which soffice 2>/dev/null', $output, $returnCode);
            $available = ($returnCode === 0);
            
            if (!$available) {
                Log::debug('LibreOffice not available in PATH');
            }
        }
        
        return $available;
    }

    /**
     * Circuit breaker para OnlyOffice
     */
    private function isCircuitBreakerOpen(): bool
    {
        if ($this->circuitBreakerFailures < self::CIRCUIT_BREAKER_THRESHOLD) {
            return false;
        }

        if ($this->circuitBreakerLastFailure === null) {
            return false;
        }

        return (time() - $this->circuitBreakerLastFailure) < self::CIRCUIT_BREAKER_TIMEOUT;
    }

    private function recordCircuitBreakerFailure(): void
    {
        $this->circuitBreakerFailures++;
        $this->circuitBreakerLastFailure = time();
    }

    /**
     * Gerar JWT com tolerância de clock skew
     */
    private function generateJWTWithClockSkew(array $payload, string $secret): string
    {
        $header = ['typ' => 'JWT', 'alg' => 'HS256'];
        
        // Adicionar clock skew tolerance
        $now = time();
        $payload['iat'] = $now - 60;      // 1 min atrás
        $payload['exp'] = $now + 300;     // 5 min no futuro
        
        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));
        
        $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, $secret, true);
        $signatureEncoded = $this->base64UrlEncode($signature);
        
        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }

    /**
     * Base64 URL encode
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Criar URL temporária segura com TTL
     */
    private function createSecureTemporaryUrl(string $path): string
    {
        $token = encrypt([
            'path' => $path,
            'expires' => time() + env('PDF_TEMP_URL_TTL', 300),
            'nonce' => random_bytes(16)
        ]);
        
        return url('/api/temp-document/' . urlencode($token));
    }

    /**
     * Extrair texto básico do documento
     */
    private function extractTextFromDocument(string $path): string
    {
        $content = Storage::get($path);
        
        // Se for RTF, tentar extrair texto
        if (str_contains($content, '{\rtf')) {
            return $this->extractTextFromRTF($content);
        }
        
        return 'Conteúdo do documento não disponível para conversão.';
    }

    /**
     * Extrair texto de RTF
     */
    private function extractTextFromRTF(string $rtfContent): string
    {
        // Implementação básica
        $text = strip_tags($rtfContent);
        $text = preg_replace('/\\\\\w+\d*\s*/', ' ', $text);
        $text = preg_replace('/[{}]/', '', $text);
        
        return trim($text);
    }

    /**
     * Gerar HTML com marca d'água TEMPORÁRIO
     */
    private function generateTemporaryPdfHTML(string $content): string
    {
        return "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <title>DOCUMENTO TEMPORÁRIO</title>
            <style>
                body { 
                    font-family: 'DejaVu Sans', sans-serif; 
                    margin: 20px; 
                    line-height: 1.6;
                    position: relative;
                }
                .watermark {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%) rotate(-45deg);
                    font-size: 72px;
                    color: rgba(255, 0, 0, 0.1);
                    font-weight: bold;
                    z-index: -1;
                    pointer-events: none;
                }
                .warning { 
                    background: #f8d7da; 
                    color: #721c24; 
                    padding: 15px; 
                    border-radius: 5px; 
                    margin-bottom: 20px;
                    border: 2px solid #f5c6cb;
                }
            </style>
        </head>
        <body>
            <div class='watermark'>TEMPORÁRIO</div>
            <div class='warning'>
                <strong>⚠️ DOCUMENTO TEMPORÁRIO</strong><br>
                Este PDF foi gerado com formatação básica. Para obter o documento oficial 
                com formatação completa, aguarde o processamento ou contate o suporte.
            </div>
            <div>" . nl2br(htmlspecialchars($content)) . "</div>
            <footer style='position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #666;'>
                DOCUMENTO TEMPORÁRIO - NÃO OFICIAL - " . date('d/m/Y H:i:s') . "
            </footer>
        </body>
        </html>";
    }
}