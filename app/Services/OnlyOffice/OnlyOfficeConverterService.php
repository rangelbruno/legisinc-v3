<?php

namespace App\Services\OnlyOffice;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;

class OnlyOfficeConverterService
{
    private string $baseUrl;
    private string $jwtSecret;
    private int $timeout;
    private int $maxRetries;

    public function __construct()
    {
        $this->baseUrl = config('onlyoffice.converter.url', 'http://legisinc-onlyoffice/ConvertService.ashx');
        $this->jwtSecret = config('onlyoffice.jwt.secret', 'your-secret-key');
        $this->timeout = config('onlyoffice.converter.timeout', 120); // 2 minutos
        $this->maxRetries = config('onlyoffice.converter.retries', 3);
    }

    /**
     * Converte arquivo DOCX para PDF usando OnlyOffice DocumentServer
     * 
     * @param string $inputPath Caminho do arquivo DOCX no Storage
     * @param string $outputPath Caminho onde salvar o PDF
     * @return string Caminho do PDF gerado
     * @throws Exception
     */
    public function convertToPDF(string $inputPath, string $outputPath): string
    {
        Log::info('Iniciando conversão OnlyOffice DOCX→PDF', [
            'input' => $inputPath,
            'output' => $outputPath
        ]);

        // Verificar se arquivo fonte existe
        if (!Storage::exists($inputPath)) {
            throw new Exception("Arquivo de origem não encontrado: {$inputPath}");
        }

        // Verificar saúde do OnlyOffice
        if (!$this->healthCheck()) {
            throw new Exception('OnlyOffice DocumentServer não está disponível');
        }

        $attempts = 0;
        $lastException = null;

        while ($attempts < $this->maxRetries) {
            $attempts++;
            
            try {
                Log::debug("Tentativa {$attempts} de conversão OnlyOffice", [
                    'input' => $inputPath,
                    'attempt' => $attempts
                ]);

                $result = $this->performConversion($inputPath, $outputPath);
                
                Log::info('Conversão OnlyOffice concluída com sucesso', [
                    'input' => $inputPath,
                    'output' => $outputPath,
                    'attempts' => $attempts,
                    'size' => Storage::size($outputPath)
                ]);

                return $result;

            } catch (Exception $e) {
                $lastException = $e;
                
                Log::warning("Tentativa {$attempts} falhou", [
                    'input' => $inputPath,
                    'error' => $e->getMessage(),
                    'attempt' => $attempts
                ]);

                // Aguardar antes de tentar novamente (exponential backoff)
                if ($attempts < $this->maxRetries) {
                    $delay = pow(2, $attempts) * 5; // 5s, 10s, 20s
                    sleep($delay);
                }
            }
        }

        // Todas as tentativas falharam
        throw new Exception(
            "Conversão OnlyOffice falhou após {$this->maxRetries} tentativas. Último erro: " . 
            ($lastException?->getMessage() ?? 'Erro desconhecido')
        );
    }

    /**
     * Executa a conversão propriamente dita
     */
    private function performConversion(string $inputPath, string $outputPath): string
    {
        // Gerar URL temporária para o arquivo
        $fileUrl = $this->generateTempUrl($inputPath);
        
        // Preparar payload da conversão
        $conversionData = [
            'async' => false,
            'filetype' => 'docx',
            'key' => $this->generateDocumentKey($inputPath),
            'outputtype' => 'pdf',
            'title' => basename($inputPath),
            'url' => $fileUrl
        ];

        // Adicionar JWT se configurado
        if ($this->jwtSecret) {
            $conversionData['token'] = $this->generateJWT($conversionData);
        }

        // Fazer requisição para o conversor
        $response = Http::timeout($this->timeout)
            ->post($this->baseUrl, $conversionData);

        if (!$response->successful()) {
            throw new Exception("Erro HTTP na conversão: {$response->status()} - {$response->body()}");
        }

        $result = $response->json();

        if (!isset($result['error']) || $result['error'] !== 0) {
            $errorMsg = $result['error'] ?? 'Erro desconhecido';
            throw new Exception("OnlyOffice retornou erro: {$errorMsg}");
        }

        if (!isset($result['fileUrl'])) {
            throw new Exception('OnlyOffice não retornou URL do arquivo convertido');
        }

        // Baixar o PDF convertido
        $pdfContent = Http::timeout(60)->get($result['fileUrl'])->body();
        
        if (empty($pdfContent)) {
            throw new Exception('PDF convertido está vazio');
        }

        // Salvar no storage
        Storage::put($outputPath, $pdfContent);

        // Verificar se foi salvo corretamente
        if (!Storage::exists($outputPath)) {
            throw new Exception("Falha ao salvar PDF convertido: {$outputPath}");
        }

        return $outputPath;
    }

    /**
     * Verifica se OnlyOffice DocumentServer está funcionando
     */
    public function healthCheck(): bool
    {
        try {
            Log::debug('Executando healthcheck OnlyOffice');
            
            $response = Http::timeout(10)
                ->get(rtrim($this->baseUrl, '/ConvertService.ashx') . '/healthcheck');

            $isHealthy = $response->successful() && 
                        ($response->json('status') === 'OK' || $response->status() === 200);
                        
            Log::debug('Resultado healthcheck OnlyOffice', [
                'healthy' => $isHealthy,
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return $isHealthy;

        } catch (Exception $e) {
            Log::warning('Healthcheck OnlyOffice falhou', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Gera URL temporária para acesso ao arquivo pelo OnlyOffice
     */
    private function generateTempUrl(string $path): string
    {
        // No ambiente de desenvolvimento/docker, usar URL interna
        if (app()->environment(['local', 'development'])) {
            $baseUrl = config('app.url', 'http://legisinc-app');
            return "{$baseUrl}/storage/temp/" . base64_encode($path);
        }

        // Em produção, usar URL pública temporária
        return Storage::temporaryUrl($path, now()->addHour());
    }

    /**
     * Gera chave única para o documento
     */
    private function generateDocumentKey(string $path): string
    {
        return hash('sha256', $path . Storage::lastModified($path));
    }

    /**
     * Gera JWT para autenticação se configurado
     */
    private function generateJWT(array $data): string
    {
        if (empty($this->jwtSecret)) {
            return '';
        }

        // Implementação simples de JWT - em produção usar biblioteca dedicada
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode($data);
        
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, $this->jwtSecret, true);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64Header . "." . $base64Payload . "." . $base64Signature;
    }

    /**
     * Obter estatísticas de conversão
     */
    public function getStats(): array
    {
        return [
            'base_url' => $this->baseUrl,
            'timeout' => $this->timeout,
            'max_retries' => $this->maxRetries,
            'healthy' => $this->healthCheck(),
            'jwt_enabled' => !empty($this->jwtSecret)
        ];
    }
}