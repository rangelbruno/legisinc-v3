<?php

namespace App\Services\OnlyOffice;

use App\Models\Proposicao;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class OnlyOfficeConversionService
{
    private string $documentServerUrl;
    private string $jwtSecret;
    private int $timeout;

    public function __construct()
    {
        $this->documentServerUrl = rtrim(config('onlyoffice.server_url'), '/');

        // Ajustar URL para comunicação entre containers
        if (config('app.env') === 'local' && str_contains($this->documentServerUrl, 'localhost:8080')) {
            $this->documentServerUrl = 'http://legisinc-onlyoffice';
        }

        $this->jwtSecret = config('onlyoffice.jwt_secret');
        $this->timeout = 60; // 60 segundos timeout para conversão
    }

    /**
     * Força o salvamento do documento no Document Server
     */
    public function forceSave(Proposicao $proposicao): array
    {
        if (empty($proposicao->onlyoffice_key)) {
            throw new \Exception('Proposição não possui chave OnlyOffice válida');
        }

        Log::info('🔄 OnlyOffice Force Save: Iniciando', [
            'proposicao_id' => $proposicao->id,
            'onlyoffice_key' => $proposicao->onlyoffice_key,
            'document_server' => $this->documentServerUrl
        ]);

        $payload = [
            'key' => $proposicao->onlyoffice_key
        ];

        $cmdBody = [
            'c' => 'forcesave',
            'key' => $proposicao->onlyoffice_key,
            'token' => $this->makeJwt($payload)
        ];

        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->documentServerUrl}/command", $cmdBody);

            $result = $response->json();

            Log::info('✅ OnlyOffice Force Save: Resposta recebida', [
                'proposicao_id' => $proposicao->id,
                'response_status' => $response->status(),
                'response_body' => $result,
                'success' => $response->successful()
            ]);

            if (!$response->successful()) {
                throw new \Exception("Erro no Command Service: HTTP {$response->status()}");
            }

            // Verificar se houve erro no comando
            if (isset($result['error']) && $result['error'] != 0) {
                throw new \Exception("Erro no forcesave: {$result['error']}");
            }

            return [
                'success' => true,
                'response' => $result
            ];

        } catch (\Exception $e) {
            Log::error('❌ OnlyOffice Force Save: Erro', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Aguarda o callback de força salvamento (status 6 ou 2)
     */
    public function waitForForceSaveCallback(Proposicao $proposicao, int $maxWaitSeconds = 30): bool
    {
        $cacheKey = "forcesave_callback_{$proposicao->id}";
        $startTime = time();

        Log::info('⏳ OnlyOffice Force Save: Aguardando callback', [
            'proposicao_id' => $proposicao->id,
            'cache_key' => $cacheKey,
            'max_wait_seconds' => $maxWaitSeconds
        ]);

        while (time() - $startTime < $maxWaitSeconds) {
            if (Cache::has($cacheKey)) {
                $callbackData = Cache::get($cacheKey);
                Cache::forget($cacheKey); // Limpar cache

                Log::info('✅ OnlyOffice Force Save: Callback recebido', [
                    'proposicao_id' => $proposicao->id,
                    'callback_data' => $callbackData,
                    'wait_time' => time() - $startTime
                ]);

                return true;
            }

            usleep(500000); // 0.5 segundos
        }

        Log::warning('⚠️ OnlyOffice Force Save: Timeout aguardando callback', [
            'proposicao_id' => $proposicao->id,
            'wait_time' => time() - $startTime
        ]);

        return false;
    }

    /**
     * Converte RTF para PDF usando a Conversion API
     */
    public function convertToPdf(Proposicao $proposicao): array
    {
        if (empty($proposicao->arquivo_path) || !Storage::exists($proposicao->arquivo_path)) {
            throw new \Exception('Arquivo RTF não encontrado para conversão');
        }

        Log::info('🔄 OnlyOffice Conversion: Iniciando conversão RTF → PDF', [
            'proposicao_id' => $proposicao->id,
            'arquivo_path' => $proposicao->arquivo_path,
            'document_server' => $this->documentServerUrl
        ]);

        // Gerar URL assinada para o arquivo RTF
        $rtfUrl = $this->getSignedFileUrl($proposicao);

        $conversionKey = "proposicao_pdf_{$proposicao->id}_" . time();

        $payload = [
            'url' => $rtfUrl,
            'key' => $conversionKey
        ];

        $convBody = [
            'async' => false,
            'filetype' => 'rtf',
            'outputtype' => 'pdf',
            'key' => $conversionKey,
            'title' => "{$proposicao->id}.rtf",
            'url' => $rtfUrl,
            'token' => $this->makeJwt($payload)
        ];

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['Accept' => 'application/json'])
                ->post("{$this->documentServerUrl}/converter", $convBody);

            $result = $response->json();

            Log::info('📄 OnlyOffice Conversion: Resposta da conversão', [
                'proposicao_id' => $proposicao->id,
                'response_status' => $response->status(),
                'response_body' => $result,
                'conversion_completed' => $result['endConvert'] ?? false
            ]);

            if (!$response->successful()) {
                throw new \Exception("Erro na Conversion API: HTTP {$response->status()}");
            }

            if (!($result['endConvert'] ?? false)) {
                throw new \Exception('Conversão não foi completada pelo OnlyOffice');
            }

            if (empty($result['fileUrl'])) {
                throw new \Exception('URL do PDF convertido não foi retornada');
            }

            // Baixar o PDF convertido
            $pdfResponse = Http::timeout(30)->get($result['fileUrl']);

            if (!$pdfResponse->successful()) {
                throw new \Exception("Erro ao baixar PDF convertido: HTTP {$pdfResponse->status()}");
            }

            $pdfBinary = $pdfResponse->body();

            if (empty($pdfBinary)) {
                throw new \Exception('PDF convertido está vazio');
            }

            // Salvar o PDF
            $pdfPath = "proposicoes/{$proposicao->id}/{$proposicao->id}_final.pdf";
            Storage::put($pdfPath, $pdfBinary);

            Log::info('✅ OnlyOffice Conversion: PDF salvo com sucesso', [
                'proposicao_id' => $proposicao->id,
                'pdf_path' => $pdfPath,
                'pdf_size' => strlen($pdfBinary),
                'original_url' => $result['fileUrl']
            ]);

            return [
                'success' => true,
                'pdf_path' => $pdfPath,
                'pdf_size' => strlen($pdfBinary),
                'conversion_response' => $result
            ];

        } catch (\Exception $e) {
            Log::error('❌ OnlyOffice Conversion: Erro na conversão', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Processo completo: Force Save + Conversão para PDF
     */
    public function forceConvertToPdf(Proposicao $proposicao): array
    {
        Log::info('🚀 OnlyOffice: Iniciando processo completo Force Save → PDF', [
            'proposicao_id' => $proposicao->id,
            'status' => $proposicao->status
        ]);

        try {
            // 1. Força o salvamento
            $forceSaveResult = $this->forceSave($proposicao);

            if (!$forceSaveResult['success']) {
                throw new \Exception("Erro no force save: {$forceSaveResult['error']}");
            }

            // 2. Aguarda callback (opcional, mas recomendado)
            $callbackReceived = $this->waitForForceSaveCallback($proposicao, 15);

            if (!$callbackReceived) {
                Log::warning('⚠️ OnlyOffice: Prosseguindo sem callback de force save');
            }

            // 3. Pequena pausa para garantir que arquivo foi persistido
            sleep(1);

            // 4. Converte para PDF
            $conversionResult = $this->convertToPdf($proposicao);

            if (!$conversionResult['success']) {
                throw new \Exception("Erro na conversão: {$conversionResult['error']}");
            }

            // 5. Atualiza a proposição
            $proposicao->update([
                'arquivo_pdf_path' => $conversionResult['pdf_path'],
                'pdf_gerado_em' => now(),
                'pdf_conversor_usado' => 'onlyoffice_conversion_api'
            ]);

            Log::info('✅ OnlyOffice: Processo completo finalizado', [
                'proposicao_id' => $proposicao->id,
                'pdf_path' => $conversionResult['pdf_path'],
                'pdf_size' => $conversionResult['pdf_size']
            ]);

            return [
                'success' => true,
                'pdf_path' => $conversionResult['pdf_path'],
                'pdf_size' => $conversionResult['pdf_size'],
                'force_save' => $forceSaveResult,
                'conversion' => $conversionResult
            ];

        } catch (\Exception $e) {
            Log::error('❌ OnlyOffice: Erro no processo completo', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Marca no cache que um callback de force save foi recebido
     */
    public function markForceSaveCallbackReceived(Proposicao $proposicao, array $callbackData): void
    {
        $cacheKey = "forcesave_callback_{$proposicao->id}";
        Cache::put($cacheKey, $callbackData, 60); // 1 minuto

        Log::info('📥 OnlyOffice: Callback de force save marcado', [
            'proposicao_id' => $proposicao->id,
            'cache_key' => $cacheKey,
            'callback_status' => $callbackData['status'] ?? 'unknown'
        ]);
    }

    /**
     * Gera URL assinada para acesso ao arquivo pelo OnlyOffice
     */
    private function getSignedFileUrl(Proposicao $proposicao): string
    {
        $baseUrl = config('app.url');
        $fileName = basename($proposicao->arquivo_path);

        // URL do arquivo acessível pelo OnlyOffice
        $fileUrl = "{$baseUrl}/onlyoffice/file/proposicao/{$proposicao->id}/{$fileName}";

        // Ajustar URL para comunicação entre containers
        if (config('app.env') === 'local' && str_contains($fileUrl, 'localhost:8001')) {
            $fileUrl = str_replace('localhost:8001', 'legisinc-app', $fileUrl);
        }

        Log::info('🔗 OnlyOffice: URL gerada para conversão', [
            'proposicao_id' => $proposicao->id,
            'file_url' => $fileUrl,
            'arquivo_path' => $proposicao->arquivo_path
        ]);

        return $fileUrl;
    }

    /**
     * Gera JWT para autenticação com OnlyOffice
     */
    private function makeJwt(array $payload): string
    {
        if (empty($this->jwtSecret)) {
            return '';
        }

        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode($payload);

        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $this->jwtSecret, true);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64Header . '.' . $base64Payload . '.' . $base64Signature;
    }
}