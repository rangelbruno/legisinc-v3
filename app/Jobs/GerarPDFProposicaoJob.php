<?php

namespace App\Jobs;

use App\Models\Proposicao;
use App\Services\DocumentConversionService;
use App\Services\OnlyOffice\OnlyOfficeConverterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class GerarPDFProposicaoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 300; // 5 minutos entre tentativas
    public int $timeout = 300; // 5 minutos timeout
    public string $tipo;

    public function __construct(private Proposicao $proposicao, string $tipo = 'para_assinatura')
    {
        $this->tipo = $tipo;
        
        // Configurar fila baseada na prioridade
        $this->queue = match($tipo) {
            'protocolado' => 'high',
            'assinado' => 'default', 
            'para_assinatura' => 'low',
            default => 'default'
        };
    }

    /**
     * Execute the job with distributed lock.
     */
    public function handle(): void
    {
        $lockKey = "pdf-generation-{$this->proposicao->id}-{$this->tipo}";
        
        // Tentar adquirir lock distribuído por 5 minutos
        $lock = Cache::lock($lockKey, 300);
        
        if (!$lock->get()) {
            Log::info('PDF generation already in progress, skipping', [
                'proposicao_id' => $this->proposicao->id,
                'tipo' => $this->tipo,
                'attempt' => $this->attempts()
            ]);
            return;
        }

        try {
            // Recarregar proposição para evitar dados stale
            $this->proposicao = $this->proposicao->fresh();
            
            if (!$this->proposicao) {
                Log::error('Proposição não encontrada', ['id' => $this->proposicao->id]);
                return;
            }

            // Verificar se ainda precisa gerar
            if ($this->pdfJaAtualizado()) {
                Log::info('PDF já atualizado, pulando', [
                    'proposicao_id' => $this->proposicao->id,
                    'tipo' => $this->tipo
                ]);
                return;
            }

            Log::info('Iniciando geração de PDF', [
                'proposicao_id' => $this->proposicao->id,
                'tipo' => $this->tipo,
                'attempt' => $this->attempts()
            ]);

            // Gerar PDF baseado no tipo
            match($this->tipo) {
                'para_assinatura' => $this->gerarPDFParaAssinatura(),
                'protocolado' => $this->gerarPDFProtocolado(),
                default => $this->gerarPDFLegacy()
            };

        } catch (Exception $e) {
            Log::error('Erro na geração de PDF', [
                'proposicao_id' => $this->proposicao->id,
                'tipo' => $this->tipo,
                'error' => $e->getMessage()
            ]);
            throw $e;
            
        } finally {
            optional($lock)->release();
        }
    }

    /**
     * Verifica se PDF já está atualizado
     */
    private function pdfJaAtualizado(): bool
    {
        if ($this->tipo === 'para_assinatura') {
            return $this->proposicao->pdf_base_hash === $this->proposicao->arquivo_hash
                && $this->proposicao->arquivo_pdf_para_assinatura
                && Storage::exists($this->proposicao->arquivo_pdf_para_assinatura);
        }
        
        // Para tipos legados, usar lógica existente
        return !empty($this->proposicao->arquivo_pdf_path) 
            && Storage::exists($this->proposicao->arquivo_pdf_path);
    }

    /**
     * Gera PDF para assinatura usando OnlyOffice
     */
    private function gerarPDFParaAssinatura(): void
    {
        if (empty($this->proposicao->arquivo_path)) {
            throw new Exception('Proposição sem arquivo fonte');
        }

        $arquivoHash = hash_file('sha256', Storage::path($this->proposicao->arquivo_path));
        $pdfPath = "proposicoes/{$this->proposicao->ano}/{$this->proposicao->id}/para_assinatura.pdf";

        try {
            // Tentar OnlyOffice primeiro
            $converterService = app(OnlyOfficeConverterService::class);
            $result = $converterService->convertToPDF($this->proposicao->arquivo_path, $pdfPath);

            $this->proposicao->update([
                'arquivo_pdf_para_assinatura' => $pdfPath,
                'pdf_gerado_em' => now(),
                'pdf_conversor_usado' => 'onlyoffice',
                'pdf_base_hash' => $arquivoHash,
                'arquivo_hash' => $arquivoHash,
                'pdf_erro_geracao' => null
            ]);

        } catch (Exception $e) {
            Log::warning('OnlyOffice falhou, tentando fallback', [
                'proposicao_id' => $this->proposicao->id,
                'error' => $e->getMessage()
            ]);
            
            // Fallback para conversor legado
            $this->gerarPDFLegacy();
        }
    }

    /**
     * Gera PDF protocolado com carimbo
     */
    private function gerarPDFProtocolado(): void
    {
        if (!$this->proposicao->arquivo_pdf_assinado) {
            throw new Exception('PDF assinado necessário para gerar versão protocolada');
        }

        // Por enquanto, copia o PDF assinado
        // TODO: Implementar carimbagem real
        $pdfAssinado = Storage::get($this->proposicao->arquivo_pdf_assinado);
        $pdfPath = "proposicoes/{$this->proposicao->ano}/{$this->proposicao->id}/protocolado.pdf";
        
        Storage::put($pdfPath, $pdfAssinado);

        $this->proposicao->update([
            'arquivo_pdf_protocolado' => $pdfPath
        ]);
    }

    /**
     * Geração legada para compatibilidade
     */
    private function gerarPDFLegacy(): void
    {
        $converter = app(DocumentConversionService::class);
        
        $fileHash = hash('sha256', Storage::get($this->proposicao->arquivo_path));
        $pdfPath = "proposicoes/pdfs/{$this->proposicao->id}/proposicao_{$this->proposicao->id}_{$fileHash}.pdf";

        $result = $converter->convertToPDF(
            $this->proposicao->arquivo_path,
            $pdfPath,
            $this->proposicao->status
        );

        if ($result['success']) {
            $updates = [
                'arquivo_pdf_path' => $pdfPath,
                'pdf_gerado_em' => now(),
                'pdf_conversor_usado' => $result['converter'],
                'pdf_erro_geracao' => null,
            ];

            // Se é para assinatura, preenche novos campos também  
            if ($this->tipo === 'para_assinatura') {
                $updates['arquivo_pdf_para_assinatura'] = $pdfPath;
                $updates['arquivo_hash'] = $fileHash;
                $updates['pdf_base_hash'] = $fileHash;
            }

            $this->proposicao->update($updates);
        } else {
            throw new Exception($result['error']);
        }
    }

    /**
     * Get unique ID for job deduplication
     */
    public function uniqueId(): string
    {
        return "pdf-{$this->proposicao->id}-{$this->tipo}";
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job: Falha definitiva na geração de PDF', [
            'proposicao_id' => $this->proposicao->id,
            'error' => $exception->getMessage()
        ]);

        $this->proposicao->update([
            'pdf_erro_geracao' => $exception->getMessage(),
            'pdf_tentativa_em' => now()
        ]);
    }
}
