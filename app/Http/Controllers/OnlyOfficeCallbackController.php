<?php

namespace App\Http\Controllers;

use App\Models\Proposicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class OnlyOfficeCallbackController extends Controller
{
    /**
     * Status do OnlyOffice Document Server
     */
    const STATUS_EDITING = 1;
    const STATUS_SAVE = 2;
    const STATUS_CORRUPTED = 3;
    const STATUS_FORCESAVE = 6;
    const STATUS_CORRUPTED_FORCESAVE = 7;

    /**
     * Handle OnlyOffice callback
     */
    public function handle(Request $request)
    {
        $data = $request->all();

        Log::info('OnlyOffice callback recebido', [
            'status' => $data['status'] ?? null,
            'key' => $data['key'] ?? null,
            'url' => $data['url'] ?? null,
            'users' => $data['users'] ?? [],
            'forcesavetype' => $data['forcesavetype'] ?? null
        ]);

        $status = $data['status'] ?? 0;
        $documentKey = $data['key'] ?? null;

        // Encontrar a proposição pelo document_key
        $proposicao = Proposicao::where('onlyoffice_document_key', $documentKey)->first();

        if (!$proposicao) {
            Log::warning('Proposição não encontrada para document_key', [
                'document_key' => $documentKey
            ]);
            return response()->json(['error' => 0]);
        }

        switch ($status) {
            case self::STATUS_EDITING:
                Log::info('Documento sendo editado', [
                    'proposicao_id' => $proposicao->id,
                    'users' => $data['users'] ?? []
                ]);
                break;

            case self::STATUS_SAVE:
            case self::STATUS_FORCESAVE:
                $this->saveDocument($proposicao, $data);
                break;

            case self::STATUS_CORRUPTED:
            case self::STATUS_CORRUPTED_FORCESAVE:
                Log::error('Documento corrompido', [
                    'proposicao_id' => $proposicao->id,
                    'status' => $status
                ]);
                break;
        }

        return response()->json(['error' => 0]);
    }

    /**
     * Salva o documento no S3
     */
    private function saveDocument(Proposicao $proposicao, array $data): void
    {
        try {
            $startTime = microtime(true);
            $downloadUrl = $data['url'] ?? null;

            if (!$downloadUrl) {
                Log::error('URL de download não fornecida', [
                    'proposicao_id' => $proposicao->id
                ]);

                // Registrar no DocumentWorkflowLog
                \App\Models\DocumentWorkflowLog::logWorkflowEvent(
                    proposicaoId: $proposicao->id,
                    eventType: 'onlyoffice_callback',
                    stage: 'save',
                    action: 'download_document',
                    status: 'error',
                    description: 'URL de download não fornecida pelo OnlyOffice',
                    metadata: ['callback_data' => $data]
                );
                return;
            }

            Log::info('Salvando documento do OnlyOffice', [
                'proposicao_id' => $proposicao->id,
                'download_url' => $downloadUrl,
                'forcesave' => $data['status'] === self::STATUS_FORCESAVE,
                'storage_disk' => config('filesystems.default')
            ]);

            // Baixar o arquivo do OnlyOffice
            $downloadStartTime = microtime(true);
            $response = Http::get($downloadUrl);
            $downloadTime = round((microtime(true) - $downloadStartTime) * 1000);

            if (!$response->successful()) {
                Log::error('Erro ao baixar documento do OnlyOffice', [
                    'proposicao_id' => $proposicao->id,
                    'status_code' => $response->status(),
                    'error' => $response->body()
                ]);

                // Registrar erro no DocumentWorkflowLog
                \App\Models\DocumentWorkflowLog::logWorkflowEvent(
                    proposicaoId: $proposicao->id,
                    eventType: 'onlyoffice_callback',
                    stage: 'save',
                    action: 'download_document',
                    status: 'error',
                    description: 'Erro ao baixar documento do OnlyOffice',
                    metadata: [
                        'status_code' => $response->status(),
                        'download_time_ms' => $downloadTime
                    ],
                    executionTimeMs: $downloadTime,
                    errorMessage: 'HTTP Status: ' . $response->status()
                );
                return;
            }

            $content = $response->body();

            // Determinar o caminho do arquivo
            $extension = pathinfo($downloadUrl, PATHINFO_EXTENSION) ?: 'rtf';
            $filePath = $proposicao->arquivo_path;

            // Se não tiver caminho definido, criar um novo
            if (!$filePath) {
                $filePath = "proposicoes/{$proposicao->id}/documento.{$extension}";
            }

            // Salvar no S3 (ou storage local)
            $uploadStartTime = microtime(true);
            $saved = Storage::put($filePath, $content);
            $uploadTime = round((microtime(true) - $uploadStartTime) * 1000);

            if ($saved) {
                // Atualizar a proposição
                $proposicao->update([
                    'arquivo_path' => $filePath,
                    'arquivo_tamanho' => strlen($content),
                    'arquivo_modificado_em' => now(),
                    // Invalidar PDF para forçar regeneração
                    'arquivo_pdf_path' => null,
                    'pdf_gerado_em' => null
                ]);

                $totalTime = round((microtime(true) - $startTime) * 1000);

                Log::info('Documento salvo com sucesso no S3', [
                    'proposicao_id' => $proposicao->id,
                    'file_path' => $filePath,
                    'size' => strlen($content),
                    'forcesave' => $data['status'] === self::STATUS_FORCESAVE,
                    'storage_disk' => config('filesystems.default'),
                    'download_time_ms' => $downloadTime,
                    'upload_time_ms' => $uploadTime,
                    'total_time_ms' => $totalTime
                ]);

                // Registrar sucesso no DocumentWorkflowLog
                \App\Models\DocumentWorkflowLog::logWorkflowEvent(
                    proposicaoId: $proposicao->id,
                    eventType: 'onlyoffice_callback',
                    stage: 'save',
                    action: 'save_document',
                    status: 'success',
                    description: 'Documento salvo com sucesso no ' . config('filesystems.default'),
                    metadata: [
                        'forcesave' => $data['status'] === self::STATUS_FORCESAVE,
                        'storage_disk' => config('filesystems.default'),
                        'download_time_ms' => $downloadTime,
                        'upload_time_ms' => $uploadTime,
                        's3_bucket' => config('filesystems.disks.s3.bucket'),
                        's3_region' => config('filesystems.disks.s3.region'),
                        'users' => $data['users'] ?? []
                    ],
                    filePath: $filePath,
                    fileType: $extension,
                    fileSizeBytes: strlen($content),
                    fileHash: md5($content),
                    executionTimeMs: $totalTime
                );

                // Se for um forcesave ou save normal durante aprovação, gerar PDF
                if ($proposicao->status === 'aprovado' || $proposicao->status === 'em_revisao') {
                    $this->gerarPDFAutomatico($proposicao);
                }

            } else {
                Log::error('Erro ao salvar documento no storage', [
                    'proposicao_id' => $proposicao->id,
                    'file_path' => $filePath,
                    'storage_disk' => config('filesystems.default')
                ]);

                // Registrar erro no DocumentWorkflowLog
                \App\Models\DocumentWorkflowLog::logWorkflowEvent(
                    proposicaoId: $proposicao->id,
                    eventType: 'onlyoffice_callback',
                    stage: 'save',
                    action: 'save_document',
                    status: 'error',
                    description: 'Erro ao salvar documento no storage',
                    metadata: [
                        'storage_disk' => config('filesystems.default'),
                        'file_path' => $filePath
                    ],
                    errorMessage: 'Storage::put retornou false'
                );

        } catch (\Exception $e) {
            Log::error('Erro ao processar callback do OnlyOffice', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Registrar exceção no DocumentWorkflowLog
            \App\Models\DocumentWorkflowLog::logWorkflowEvent(
                proposicaoId: $proposicao->id,
                eventType: 'onlyoffice_callback',
                stage: 'save',
                action: 'process_callback',
                status: 'error',
                description: 'Exceção ao processar callback do OnlyOffice',
                metadata: [
                    'error_type' => get_class($e),
                    'error_code' => $e->getCode(),
                    'callback_data' => $data ?? []
                ],
                errorMessage: $e->getMessage(),
                stackTrace: $e->getTraceAsString()
            );
        }
    }

    /**
     * Gera PDF automaticamente após salvamento
     */
    private function gerarPDFAutomatico(Proposicao $proposicao): void
    {
        try {
            Log::info('Gerando PDF automático após salvamento OnlyOffice', [
                'proposicao_id' => $proposicao->id,
                'status' => $proposicao->status
            ]);

            // Usar o serviço de conversão existente
            app(ProposicaoLegislativoController::class)
                ->gerarPDFAposAprovacao($proposicao);

        } catch (\Exception $e) {
            Log::error('Erro ao gerar PDF automático', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}