<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Request;

class DocumentWorkflowLog extends Model
{
    protected $fillable = [
        'proposicao_id',
        'user_id',
        'event_type',
        'stage',
        'action',
        'status',
        'description',
        'metadata',
        'file_path',
        'file_type',
        'file_size',
        'file_hash',
        'protocol_number',
        'protocol_date',
        'signature_type',
        'certificate_info',
        'signature_date',
        'ip_address',
        'user_agent',
        'execution_time_ms',
        'error_message',
        'stack_trace',
    ];

    protected $casts = [
        'metadata' => 'array',
        'protocol_date' => 'datetime',
        'signature_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento com Proposicao
     */
    public function proposicao(): BelongsTo
    {
        return $this->belongsTo(Proposicao::class);
    }

    /**
     * Relacionamento com User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Método estático para criar logs de workflow de forma simplificada
     */
    public static function logWorkflowEvent(
        int $proposicaoId,
        string $eventType,
        string $stage,
        string $action,
        string $status,
        string $description,
        array $metadata = [],
        ?int $userId = null,
        ?string $filePath = null,
        ?string $fileType = null,
        ?int $fileSizeBytes = null,
        ?string $fileHash = null,
        ?int $executionTimeMs = null,
        ?string $errorMessage = null,
        ?string $stackTrace = null
    ): self {
        return self::create([
            'proposicao_id' => $proposicaoId,
            'user_id' => $userId ?? auth()->id(),
            'event_type' => $eventType,
            'stage' => $stage,
            'action' => $action,
            'status' => $status,
            'description' => $description,
            'metadata' => $metadata,
            'file_path' => $filePath,
            'file_type' => $fileType,
            'file_size' => $fileSizeBytes,
            'file_hash' => $fileHash,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'execution_time_ms' => $executionTimeMs,
            'error_message' => $errorMessage,
            'stack_trace' => $stackTrace,
        ]);
    }

    /**
     * Método para logs de exportação de PDF
     */
    public static function logPdfExport(
        int $proposicaoId,
        string $status,
        string $description,
        string $filePath = null,
        int $fileSizeBytes = null,
        string $fileHash = null,
        int $executionTimeMs = null,
        array $metadata = [],
        string $errorMessage = null
    ): self {
        return self::logWorkflowEvent(
            proposicaoId: $proposicaoId,
            eventType: 'pdf_exported',
            stage: 'export',
            action: 'export_pdf',
            status: $status,
            description: $description,
            metadata: $metadata,
            filePath: $filePath,
            fileType: 'pdf',
            fileSizeBytes: $fileSizeBytes,
            fileHash: $fileHash,
            executionTimeMs: $executionTimeMs,
            errorMessage: $errorMessage
        );
    }

    /**
     * Método para logs de assinatura de documento
     */
    public static function logDocumentSignature(
        int $proposicaoId,
        string $status,
        string $description,
        string $signatureType = null,
        string $certificateInfo = null,
        array $metadata = [],
        string $errorMessage = null
    ): self {
        $log = self::logWorkflowEvent(
            proposicaoId: $proposicaoId,
            eventType: 'document_signed',
            stage: 'signature',
            action: 'sign_document',
            status: $status,
            description: $description,
            metadata: $metadata,
            errorMessage: $errorMessage
        );

        if ($signatureType) {
            $log->signature_type = $signatureType;
        }
        if ($certificateInfo) {
            $log->certificate_info = $certificateInfo;
        }
        $log->signature_date = now();
        $log->save();

        return $log;
    }

    /**
     * Método para logs de atribuição de protocolo
     */
    public static function logProtocolAssignment(
        int $proposicaoId,
        string $status,
        string $description,
        string $protocolNumber = null,
        array $metadata = []
    ): self {
        $log = self::logWorkflowEvent(
            proposicaoId: $proposicaoId,
            eventType: 'protocol_assigned',
            stage: 'protocol',
            action: 'assign_protocol',
            status: $status,
            description: $description,
            metadata: $metadata
        );

        if ($protocolNumber) {
            $log->protocol_number = $protocolNumber;
            $log->protocol_date = now();
            $log->save();
        }

        return $log;
    }

    /**
     * Método para logs de criação de proposição
     */
    public static function logProposicaoCreation(
        int $proposicaoId,
        string $status,
        string $description,
        string $filePath = null,
        array $metadata = []
    ): self {
        return self::logWorkflowEvent(
            proposicaoId: $proposicaoId,
            eventType: 'proposicao_created',
            stage: 'creation',
            action: 'create_proposicao',
            status: $status,
            description: $description,
            metadata: $metadata,
            filePath: $filePath,
            fileType: 'rtf'
        );
    }

    /**
     * Método para logs de edição OnlyOffice
     */
    public static function logOnlyOfficeEdit(
        int $proposicaoId,
        string $status,
        string $description,
        string $filePath = null,
        int $fileSizeBytes = null,
        array $metadata = []
    ): self {
        return self::logWorkflowEvent(
            proposicaoId: $proposicaoId,
            eventType: 'onlyoffice_edited',
            stage: 'editing',
            action: 'edit_document',
            status: $status,
            description: $description,
            metadata: $metadata,
            filePath: $filePath,
            fileType: 'rtf',
            fileSizeBytes: $fileSizeBytes
        );
    }

    /**
     * Método para logs de exportação S3
     */
    public static function logS3Export(
        int $proposicaoId,
        string $status,
        string $description,
        string $s3Path = null,
        int $fileSizeBytes = null,
        string $fileHash = null,
        int $executionTimeMs = null,
        array $metadata = [],
        string $errorMessage = null
    ): self {
        return self::logWorkflowEvent(
            proposicaoId: $proposicaoId,
            eventType: 's3_exported',
            stage: 'export_s3',
            action: 'export_to_s3',
            status: $status,
            description: $description,
            metadata: array_merge($metadata, ['s3_path' => $s3Path]),
            filePath: $s3Path,
            fileType: 'pdf',
            fileSizeBytes: $fileSizeBytes,
            fileHash: $fileHash,
            executionTimeMs: $executionTimeMs,
            errorMessage: $errorMessage
        );
    }

    /**
     * Método para logs de aprovação legislativa
     */
    public static function logLegislativeApproval(
        int $proposicaoId,
        string $status,
        string $description,
        array $metadata = []
    ): self {
        return self::logWorkflowEvent(
            proposicaoId: $proposicaoId,
            eventType: 'legislative_approved',
            stage: 'approval',
            action: 'approve_legislative',
            status: $status,
            description: $description,
            metadata: $metadata
        );
    }

    /**
     * Método para logs de assinatura digital melhorado
     */
    public static function logDigitalSignature(
        int $proposicaoId,
        string $status,
        string $description,
        string $signatureType = null,
        string $certificateInfo = null,
        string $signedFilePath = null,
        int $fileSizeBytes = null,
        array $metadata = [],
        string $errorMessage = null
    ): self {
        $log = self::logWorkflowEvent(
            proposicaoId: $proposicaoId,
            eventType: 'digital_signed',
            stage: 'signature',
            action: 'digital_sign',
            status: $status,
            description: $description,
            metadata: $metadata,
            filePath: $signedFilePath,
            fileType: 'pdf',
            fileSizeBytes: $fileSizeBytes,
            errorMessage: $errorMessage
        );

        if ($signatureType) {
            $log->signature_type = $signatureType;
        }
        if ($certificateInfo) {
            $log->certificate_info = $certificateInfo;
        }
        $log->signature_date = now();
        $log->save();

        return $log;
    }

    /**
     * Scopes para filtrar logs
     */
    public function scopeByProposicao($query, int $proposicaoId)
    {
        return $query->where('proposicao_id', $proposicaoId);
    }

    public function scopeByStage($query, string $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeRecentFirst($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Accessor para formatar tamanho do arquivo
     */
    public function getFormattedFileSizeAttribute(): ?string
    {
        if (!$this->file_size) {
            return null;
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;

        return number_format($bytes / pow(1024, $power), 2, ',', '.') . ' ' . $units[$power];
    }

    /**
     * Accessor para formatar tempo de execução
     */
    public function getFormattedExecutionTimeAttribute(): ?string
    {
        if (!$this->execution_time_ms) {
            return null;
        }

        if ($this->execution_time_ms < 1000) {
            return $this->execution_time_ms . 'ms';
        }

        return number_format($this->execution_time_ms / 1000, 2) . 's';
    }

    /**
     * Accessor para ícone baseado no status
     */
    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            'success' => 'fas fa-check-circle text-success',
            'error' => 'fas fa-times-circle text-danger',
            'warning' => 'fas fa-exclamation-triangle text-warning',
            'pending' => 'fas fa-clock text-info',
            default => 'fas fa-info-circle text-secondary',
        };
    }

    /**
     * Accessor para cor do badge baseado no status
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'success' => 'badge-success',
            'error' => 'badge-danger',
            'warning' => 'badge-warning',
            'pending' => 'badge-info',
            default => 'badge-secondary',
        };
    }
}
