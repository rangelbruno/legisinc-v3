<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class OnlyOfficeConversionService
{
    /**
     * Converte um arquivo (docx/rtf/…) para PDF via OnlyOffice e retorna caminho temporário do PDF.
     * @param string $absoluteInputPath Caminho absoluto do arquivo fonte
     * @return string Caminho absoluto do PDF gerado (temporário)
     */
    public function convertToPdf(string $absoluteInputPath): string
    {
        if (!is_file($absoluteInputPath)) {
            throw new RuntimeException("Arquivo fonte não encontrado: {$absoluteInputPath}");
        }

        $startTime = microtime(true);
        $tmpPdf = sys_get_temp_dir() . '/oo_export_' . uniqid() . '.pdf';

        try {
            // Determinar tipo do arquivo fonte
            $extension = strtolower(pathinfo($absoluteInputPath, PATHINFO_EXTENSION));
            $fileType = $this->mapFileExtensionToType($extension);

            Log::info('OnlyOffice Conversion: Iniciando conversão', [
                'input_file' => $absoluteInputPath,
                'file_type' => $fileType,
                'file_size' => filesize($absoluteInputPath),
                'target_format' => 'pdf'
            ]);

            // Preparar dados para a API de conversão do OnlyOffice
            $conversionData = [
                'async' => false,
                'filetype' => $fileType,
                'key' => $this->generateConversionKey($absoluteInputPath),
                'outputtype' => 'pdf',
                'title' => basename($absoluteInputPath, '.' . $extension) . '.pdf',
                'url' => $this->prepareFileForConversion($absoluteInputPath)
            ];

            // Chamar API de conversão do OnlyOffice
            $result = $this->callOnlyOfficeConversionAPI($conversionData);

            if (!$result || !isset($result['fileUrl'])) {
                throw new RuntimeException('OnlyOffice conversion API failed or returned invalid response');
            }

            // Baixar o PDF convertido
            $this->downloadConvertedFile($result['fileUrl'], $tmpPdf);

            $executionTime = round((microtime(true) - $startTime) * 1000);

            Log::info('OnlyOffice Conversion: Conversão concluída com sucesso', [
                'input' => $absoluteInputPath,
                'output' => $tmpPdf,
                'output_size' => filesize($tmpPdf),
                'execution_time_ms' => $executionTime
            ]);

            return $tmpPdf;

        } catch (\Exception $e) {
            $executionTime = round((microtime(true) - $startTime) * 1000);

            Log::error('OnlyOffice Conversion: Falha na conversão', [
                'input' => $absoluteInputPath,
                'error' => $e->getMessage(),
                'execution_time_ms' => $executionTime
            ]);

            // Fallback: tentar conversão com LibreOffice se disponível
            return $this->convertWithLibreOffice($absoluteInputPath, $tmpPdf);
        }
    }

    /**
     * Mapear extensão de arquivo para tipo OnlyOffice
     */
    private function mapFileExtensionToType(string $extension): string
    {
        $mapping = [
            'rtf' => 'rtf',
            'docx' => 'docx',
            'doc' => 'doc',
            'odt' => 'odt',
            'txt' => 'txt'
        ];

        return $mapping[$extension] ?? 'rtf';
    }

    /**
     * Gerar chave única para conversão
     */
    private function generateConversionKey(string $filePath): string
    {
        return md5($filePath . filemtime($filePath) . uniqid());
    }

    /**
     * Preparar arquivo para conversão (simular URL acessível)
     */
    private function prepareFileForConversion(string $absolutePath): string
    {
        // Em um ambiente real, você colocaria o arquivo em um local acessível via HTTP
        // Por ora, retornar um URL simulado
        $filename = basename($absolutePath);
        return "http://legisinc-app/storage/temp/" . $filename;
    }

    /**
     * Chamar API de conversão do OnlyOffice
     */
    private function callOnlyOfficeConversionAPI(array $data): ?array
    {
        $onlyOfficeUrl = config('onlyoffice.server_url', 'http://localhost:8080');
        $conversionUrl = $onlyOfficeUrl . '/ConvertService.ashx';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $conversionUrl,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 10
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new RuntimeException("cURL error: {$error}");
        }

        if ($httpCode !== 200) {
            throw new RuntimeException("HTTP error: {$httpCode}");
        }

        $result = json_decode($response, true);

        if (!$result || ($result['error'] ?? 0) !== 0) {
            $errorMsg = $result['error'] ?? 'Unknown error';
            throw new RuntimeException("OnlyOffice conversion error: {$errorMsg}");
        }

        return $result;
    }

    /**
     * Baixar arquivo convertido
     */
    private function downloadConvertedFile(string $url, string $destination): void
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 120
        ]);

        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$content) {
            throw new RuntimeException("Failed to download converted file from {$url}");
        }

        file_put_contents($destination, $content);
    }

    /**
     * Fallback: conversão com LibreOffice com configurações otimizadas para fontes
     */
    private function convertWithLibreOffice(string $inputPath, string $outputPath): string
    {
        Log::warning('OnlyOffice conversion failed, trying LibreOffice fallback');

        // 1. Pré-processar RTF para corrigir sequências Unicode corrompidas
        $preprocessedPath = $this->preprocessRTFForLibreOffice($inputPath);

        // 2. Configurações otimizadas para preservar fontes e encoding UTF-8
        $command = sprintf(
            'libreoffice --headless --convert-to pdf:writer_pdf_Export:"{\"EmbedFonts\":true,\"UseTaggedPDF\":true,\"ExportFormFields\":false,\"Quality\":90}" --outdir %s %s 2>&1',
            escapeshellarg(dirname($outputPath)),
            escapeshellarg($preprocessedPath)
        );

        Log::info('LibreOffice conversion command', ['command' => $command]);

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            Log::error('LibreOffice conversion failed', [
                'return_code' => $returnCode,
                'output' => $output,
                'input_path' => $preprocessedPath
            ]);

            // Tentar comando mais simples como fallback
            $simpleCommand = sprintf(
                'libreoffice --headless --convert-to pdf --outdir %s %s 2>&1',
                escapeshellarg(dirname($outputPath)),
                escapeshellarg($preprocessedPath)
            );

            exec($simpleCommand, $simpleOutput, $simpleReturnCode);

            if ($simpleReturnCode !== 0) {
                throw new RuntimeException('LibreOffice conversion failed: ' . implode('\n', $simpleOutput));
            }
        }

        // LibreOffice cria o arquivo com nome baseado no input
        $inputBasename = basename($preprocessedPath, '.' . pathinfo($preprocessedPath, PATHINFO_EXTENSION));
        $libreOfficePdf = dirname($outputPath) . '/' . $inputBasename . '.pdf';

        if (!file_exists($libreOfficePdf)) {
            throw new RuntimeException('LibreOffice conversion completed but PDF not found');
        }

        // Verificar se o PDF gerado é válido
        $fileInfo = exec('file ' . escapeshellarg($libreOfficePdf));
        if (strpos($fileInfo, 'PDF document') === false) {
            throw new RuntimeException('LibreOffice generated invalid PDF file');
        }

        // Mover para o caminho esperado
        rename($libreOfficePdf, $outputPath);

        // Limpar arquivo temporário
        if ($preprocessedPath !== $inputPath && file_exists($preprocessedPath)) {
            unlink($preprocessedPath);
        }

        Log::info('LibreOffice fallback conversion successful', [
            'input' => $inputPath,
            'preprocessed' => $preprocessedPath,
            'output' => $outputPath,
            'file_size' => filesize($outputPath),
            'file_info' => $fileInfo
        ]);

        return $outputPath;
    }

    /**
     * Pré-processar RTF para corrigir sequências Unicode problemáticas
     */
    private function preprocessRTFForLibreOffice(string $inputPath): string
    {
        $content = file_get_contents($inputPath);
        if (!$content) {
            return $inputPath; // Retornar arquivo original se não conseguir ler
        }

        Log::info('RTF Preprocessing: Iniciando conversão de sequências Unicode');

        // Corrigir sequências Unicode RTF (incluindo valores negativos)
        $content = preg_replace_callback('/\\\\uc1\\\\u(\d+)\*/m', function($matches) {
            $unicode = intval($matches[1]);
            if ($unicode > 0 && $unicode < 65536) {
                $char = mb_chr($unicode, 'UTF-8');
                Log::debug('RTF Unicode conversion', ['unicode' => $unicode, 'char' => $char]);
                return $char;
            }
            return $matches[0]; // Manter original se não conseguir converter
        }, $content);

        // Substituir nomes de fontes comuns em Unicode por versões legíveis
        $fontReplacements = [
            // Arial
            '/\\\\uc1\\\\u65\*\\\\u114\*\\\\u105\*\\\\u97\*\\\\u108\*/' => 'Arial',
            // Calibri
            '/\\\\uc1\\\\u67\*\\\\u97\*\\\\u108\*\\\\u105\*\\\\u98\*\\\\u114\*\\\\u105\*/' => 'Calibri',
            // Times New Roman
            '/\\\\uc1\\\\u84\*\\\\u105\*\\\\u109\*\\\\u101\*\\\\u115\*\\\\u32\*\\\\u78\*\\\\u101\*\\\\u119\*\\\\u32\*\\\\u82\*\\\\u111\*\\\\u109\*\\\\u97\*\\\\u110\*/' => 'Times New Roman',
            // Cambria
            '/\\\\uc1\\\\u67\*\\\\u97\*\\\\u109\*\\\\u98\*\\\\u114\*\\\\u105\*\\\\u97\*/' => 'Cambria'
        ];

        foreach ($fontReplacements as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }

        // Forçar uso de fontes seguras
        $content = str_replace(
            ['Arial', 'Calibri', 'Times New Roman', 'Cambria'],
            ['DejaVu Sans', 'DejaVu Sans', 'DejaVu Serif', 'DejaVu Serif'],
            $content
        );

        // Criar arquivo temporário pré-processado
        $tempPath = sys_get_temp_dir() . '/rtf_preprocessed_' . uniqid() . '.rtf';
        file_put_contents($tempPath, $content);

        Log::info('RTF Preprocessing: Arquivo pré-processado criado', [
            'original' => $inputPath,
            'preprocessed' => $tempPath,
            'original_size' => filesize($inputPath),
            'preprocessed_size' => filesize($tempPath)
        ]);

        return $tempPath;
    }
}