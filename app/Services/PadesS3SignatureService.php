<?php

namespace App\Services;

use App\Models\Proposicao;
use App\Models\DocumentWorkflowLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PadesS3SignatureService
{
    protected PadesSignatureAppearanceService $appearanceService;
    protected AssinaturaDigitalService $signingService;

    public function __construct(
        PadesSignatureAppearanceService $appearanceService,
        AssinaturaDigitalService $signingService
    ) {
        $this->appearanceService = $appearanceService;
        $this->signingService = $signingService;
    }

    /**
     * Sign PDF from S3 with PAdES visible signature panel
     *
     * @param Proposicao $proposicao
     * @param array $signatureData
     * @param object $user
     * @return array
     */
    public function signS3PDF(Proposicao $proposicao, array $signatureData, $user): array
    {
        $startTime = microtime(true);

        try {
            Log::info('🚀 PAdES S3: Iniciando assinatura PAdES de PDF no S3', [
                'proposicao_id' => $proposicao->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                's3_path' => $proposicao->pdf_s3_path
            ]);

            // 1. Validate S3 PDF exists
            if (empty($proposicao->pdf_s3_path)) {
                throw new \Exception('PDF não encontrado no S3. Execute a exportação primeiro.');
            }

            if (!Storage::disk('s3')->exists($proposicao->pdf_s3_path)) {
                throw new \Exception('Arquivo PDF não encontrado no S3: ' . $proposicao->pdf_s3_path);
            }

            // 2. Download PDF from S3 to temporary location
            $tempPdfPath = $this->downloadS3PDFToTemp($proposicao->pdf_s3_path);

            // 3. Generate verification URL
            $verificationUrl = $this->generateVerificationUrl($proposicao);

            // 4. Prepare signature data with additional info
            $enhancedSignatureData = array_merge($signatureData, [
                'nome_assinante' => $user->name,
                'email_assinante' => $user->email,
                'cargo' => $this->getUserRole($user),
                'certificado_cn' => $user->certificado_digital_cn ?? $user->name,
                'checksum' => hash('sha256', file_get_contents($tempPdfPath)),
                'signature_timestamp' => now()->format('c')
            ]);

            // 5. Create visual signature panel
            $pdfWithPanel = $this->appearanceService->createSignaturePanel(
                $tempPdfPath,
                $proposicao,
                $enhancedSignatureData,
                $verificationUrl
            );

            if (!$pdfWithPanel || !file_exists($pdfWithPanel)) {
                Log::error('❌ PAdES S3: Falha ao criar painel visual', [
                    'temp_pdf_path' => $tempPdfPath,
                    'pdf_with_panel' => $pdfWithPanel,
                    'file_exists' => $pdfWithPanel ? file_exists($pdfWithPanel) : false
                ]);
                throw new \Exception('Falha ao criar painel de assinatura visual');
            }

            Log::info('✅ PAdES S3: Painel visual criado com sucesso', [
                'original_pdf' => basename($tempPdfPath),
                'panel_pdf' => basename($pdfWithPanel),
                'panel_pdf_full_path' => $pdfWithPanel,
                'file_exists' => file_exists($pdfWithPanel),
                'file_size' => file_exists($pdfWithPanel) ? filesize($pdfWithPanel) : 0
            ]);

            // 6. Apply PAdES digital signature
            Log::info('🔒 PAdES S3: Iniciando assinatura digital do PDF com painel', [
                'pdf_with_panel' => basename($pdfWithPanel),
                'signing_service_class' => get_class($this->signingService)
            ]);

            $signedPdfPath = $this->signingService->assinarPDF($pdfWithPanel, $enhancedSignatureData, $user);

            Log::info('🔒 PAdES S3: Resultado da assinatura digital', [
                'signed_pdf_path' => $signedPdfPath,
                'file_exists' => $signedPdfPath ? file_exists($signedPdfPath) : false
            ]);

            if (!$signedPdfPath || !file_exists($signedPdfPath)) {
                throw new \Exception('Falha ao aplicar assinatura digital PAdES');
            }

            // 7. Upload signed PDF back to S3
            $s3SignedPath = $this->uploadSignedPDFToS3($signedPdfPath, $proposicao);

            // 8. Update proposicao with signed PDF information
            $this->updateProposicaoWithSignedPDF($proposicao, $s3SignedPath, $enhancedSignatureData, $user, $verificationUrl);

            // 9. Generate new presigned URL for the signed PDF
            $signedPdfUrl = Storage::disk('s3')->temporaryUrl($s3SignedPath, now()->addHours(24));

            // 10. Clean up temporary files
            $this->cleanupTempFiles([$tempPdfPath, $pdfWithPanel, $signedPdfPath]);

            $executionTime = round((microtime(true) - $startTime) * 1000);

            Log::info('✅ PAdES S3: Assinatura concluída com sucesso', [
                'proposicao_id' => $proposicao->id,
                'original_s3_path' => $proposicao->pdf_s3_path,
                'signed_s3_path' => $s3SignedPath,
                'execution_time_ms' => $executionTime
            ]);

            return [
                'success' => true,
                'message' => 'PDF assinado com sucesso usando PAdES',
                'original_s3_path' => $proposicao->pdf_s3_path,
                'signed_s3_path' => $s3SignedPath,
                'signed_pdf_url' => $signedPdfUrl,
                'verification_url' => $verificationUrl,
                'signature_metadata' => [
                    'signer_name' => $user->name,
                    'signature_timestamp' => $enhancedSignatureData['signature_timestamp'],
                    'document_hash' => $enhancedSignatureData['checksum'],
                    'pades_level' => 'PAdES-B', // Can be enhanced to B-T/B-LT later
                ],
                'execution_time_ms' => $executionTime
            ];

        } catch (\Exception $e) {
            Log::error('❌ PAdES S3: Falha na assinatura', [
                'proposicao_id' => $proposicao->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Clean up any temporary files
            if (isset($tempPdfPath)) $this->cleanupTempFiles([$tempPdfPath]);
            if (isset($pdfWithPanel)) $this->cleanupTempFiles([$pdfWithPanel]);
            if (isset($signedPdfPath)) $this->cleanupTempFiles([$signedPdfPath]);

            return [
                'success' => false,
                'message' => 'Falha na assinatura PAdES: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Download PDF from S3 to temporary location
     */
    private function downloadS3PDFToTemp(string $s3Path): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'pades_pdf_') . '.pdf';

        $pdfContent = Storage::disk('s3')->get($s3Path);

        if (!$pdfContent) {
            throw new \Exception('Não foi possível baixar PDF do S3');
        }

        if (file_put_contents($tempPath, $pdfContent) === false) {
            throw new \Exception('Falha ao salvar PDF temporário');
        }

        Log::info('📥 PDF baixado do S3 para local temporário', [
            's3_path' => $s3Path,
            'temp_path' => basename($tempPath),
            'size_bytes' => strlen($pdfContent)
        ]);

        return $tempPath;
    }

    /**
     * Upload signed PDF back to S3
     */
    private function uploadSignedPDFToS3(string $localPath, Proposicao $proposicao): string
    {
        if (!file_exists($localPath)) {
            throw new \Exception('PDF assinado não encontrado: ' . $localPath);
        }

        // Use the same S3 path as the original PDF to replace it with the signed version
        // This ensures the intelligent substitution system works correctly
        $s3SignedPath = $proposicao->pdf_s3_path;

        if (empty($s3SignedPath)) {
            throw new \Exception('Caminho S3 original não encontrado na proposição');
        }

        Log::info('🔄 Substituindo PDF original no S3 com versão assinada', [
            'proposicao_id' => $proposicao->id,
            's3_path' => $s3SignedPath
        ]);

        $pdfContent = file_get_contents($localPath);

        $uploaded = Storage::disk('s3')->put($s3SignedPath, $pdfContent, [
            'ContentType' => 'application/pdf',
            'ACL' => 'private',
            'Metadata' => [
                'signed_by' => auth()->user()->name ?? 'Sistema',
                'signature_type' => 'PAdES',
                'proposicao_id' => (string) $proposicao->id,
                'original_path' => $proposicao->pdf_s3_path
            ]
        ]);

        if (!$uploaded) {
            throw new \Exception('Falha ao enviar PDF assinado para S3');
        }

        Log::info('📤 PDF assinado enviado para S3', [
            'local_path' => basename($localPath),
            's3_signed_path' => $s3SignedPath,
            'size_bytes' => strlen($pdfContent)
        ]);

        return $s3SignedPath;
    }

    /**
     * Update proposicao with signed PDF information
     */
    private function updateProposicaoWithSignedPDF(Proposicao $proposicao, string $s3SignedPath, array $signatureData, $user, string $verificationUrl): void
    {
        // Generate signature metadata
        $signatureMetadata = [
            'id' => $this->signingService->gerarIdentificadorAssinatura(),
            'type' => 'PAdES-B',
            'signer_name' => $user->name,
            'signer_email' => $user->email,
            'certificate_cn' => $user->certificado_digital_cn ?? $user->name,
            'signature_timestamp' => $signatureData['signature_timestamp'],
            'document_hash' => $signatureData['checksum'],
            'signed_at' => now()->format('d/m/Y H:i:s')
        ];

        // Get file size
        $fileSize = Storage::disk('s3')->size($s3SignedPath);

        // Enhanced PAdES metadata with verification URL (already generated earlier)
        $padesMetadata = array_merge($signatureMetadata, [
            'verification_url' => $verificationUrl
        ]);

        // Update proposicao in transaction
        DB::transaction(function () use ($proposicao, $s3SignedPath, $signatureMetadata, $padesMetadata, $fileSize, $user) {
            // Since we're replacing the original PDF with the signed version,
            // we only need to update the signed metadata and URL (same path, new content)
            $proposicao->update([
                'status' => 'assinado', // Change status to signed
                'assinatura_digital' => json_encode($signatureMetadata),
                'data_assinatura' => now(),
                'ip_assinatura' => request()->ip(),
                'certificado_digital' => $signatureMetadata['id'],
                'pdf_s3_url' => Storage::disk('s3')->temporaryUrl($s3SignedPath, now()->addHours(24)), // Update the main URL
                'pdf_size_bytes' => $fileSize, // Update the size as file is now signed
                'pades_metadata' => json_encode($padesMetadata) // Store PAdES metadata with verification URL
            ]);
        });

        // Log digital signature in workflow system
        DocumentWorkflowLog::logDigitalSignature(
            proposicaoId: $proposicao->id,
            status: 'success',
            description: "Documento assinado digitalmente com PAdES - Assinante: {$user->name}",
            signatureType: 'PAdES-B',
            certificateInfo: $user->certificado_digital_cn ?? $user->name,
            signedFilePath: $s3SignedPath,
            fileSizeBytes: $fileSize,
            metadata: [
                'signature_method' => 'PAdES',
                'document_hash' => $signatureMetadata['document_hash'],
                'verification_url' => $verificationUrl,
                'certificate_cn' => $user->certificado_digital_cn ?? $user->name
            ]
        );

        Log::info('✅ Proposição atualizada com informações da assinatura', [
            'proposicao_id' => $proposicao->id,
            'new_status' => 'assinado',
            'signed_s3_path' => $s3SignedPath,
            'signature_id' => $signatureMetadata['id']
        ]);
    }

    /**
     * Generate verification URL for signed document
     */
    private function generateVerificationUrl(Proposicao $proposicao): string
    {
        // Generate UUID for document verification
        $verificationUuid = \Illuminate\Support\Str::uuid()->toString();

        // Store just the UUID (not the full URL) in the database
        $proposicao->update(['pades_verification_uuid' => $verificationUuid]);

        // Generate verification URL
        return route('proposicoes.verificar.assinatura', [
            'proposicao' => $proposicao->id,
            'uuid' => $verificationUuid
        ]);
    }

    /**
     * Get user role for signature
     */
    private function getUserRole($user): string
    {
        $roles = $user->roles->pluck('name')->toArray();

        if (in_array('PARLAMENTAR', $roles)) {
            return 'Vereador';
        } elseif (in_array('LEGISLATIVO', $roles)) {
            return 'Assessor Legislativo';
        } elseif (in_array('JURIDICO', $roles)) {
            return 'Assessor Jurídico';
        }

        return 'Usuário do Sistema';
    }

    /**
     * Clean up temporary files
     */
    private function cleanupTempFiles(array $paths): void
    {
        foreach ($paths as $path) {
            if ($path && file_exists($path)) {
                @unlink($path);
                Log::debug('🗑️ Arquivo temporário removido', ['path' => basename($path)]);
            }
        }
    }

    /**
     * Validate if PDF can be signed
     */
    public function canSignPDF(Proposicao $proposicao): array
    {
        $checks = [];

        // Check if proposicao has S3 PDF
        $checks['has_s3_pdf'] = !empty($proposicao->pdf_s3_path);

        // Check if S3 PDF exists
        $checks['s3_pdf_exists'] = $checks['has_s3_pdf'] && Storage::disk('s3')->exists($proposicao->pdf_s3_path);

        // Check if already signed
        $checks['not_already_signed'] = !in_array($proposicao->status, ['assinado', 'enviado_protocolo']);

        // Check if status allows signing
        $checks['status_allows_signing'] = in_array($proposicao->status, ['aprovado', 'aprovado_assinatura', 'retornado_legislativo']);

        $canSign = $checks['has_s3_pdf'] &&
                   $checks['s3_pdf_exists'] &&
                   $checks['not_already_signed'] &&
                   $checks['status_allows_signing'];

        return [
            'can_sign' => $canSign,
            'checks' => $checks,
            'message' => $canSign ? 'PDF pode ser assinado' : 'PDF não pode ser assinado no momento'
        ];
    }
}