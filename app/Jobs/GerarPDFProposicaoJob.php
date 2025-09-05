<?php

namespace App\Jobs;

use App\Models\Proposicao;
use App\Services\DocumentConversionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GerarPDFProposicaoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 300; // 5 minutos entre tentativas

    public function __construct(private Proposicao $proposicao)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(DocumentConversionService $converter): void
    {
        Log::info('Job: Gerando PDF para proposição', [
            'proposicao_id' => $this->proposicao->id,
            'attempt' => $this->attempts()
        ]);

        if (empty($this->proposicao->arquivo_path)) {
            $this->fail(new \Exception('Proposição sem arquivo fonte'));
            return;
        }

        $fileHash = hash('sha256', Storage::get($this->proposicao->arquivo_path));
        $pdfPath = "proposicoes/pdfs/{$this->proposicao->id}/proposicao_{$this->proposicao->id}_{$fileHash}.pdf";

        $result = $converter->convertToPDF(
            $this->proposicao->arquivo_path,
            $pdfPath,
            $this->proposicao->status
        );

        if ($result['success']) {
            $this->proposicao->update([
                'arquivo_pdf_path' => $pdfPath,
                'pdf_gerado_em' => now(),
                'pdf_conversor_usado' => $result['converter'],
                'pdf_tamanho' => $result['output_bytes'],
                'pdf_erro_geracao' => null,
            ]);

            Log::info('Job: PDF gerado com sucesso', [
                'proposicao_id' => $this->proposicao->id,
                'converter' => $result['converter']
            ]);
        } else {
            throw new \Exception($result['error']);
        }
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
