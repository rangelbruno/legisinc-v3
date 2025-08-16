<?php

namespace App\Observers;

use App\Models\Proposicao;
use App\Services\Performance\CacheService;
use Illuminate\Support\Facades\Log;

class ProposicaoObserver
{
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Proposicao "created" event.
     */
    public function created(Proposicao $proposicao): void
    {
        $this->invalidateRelatedCache($proposicao);
        
        Log::info('Proposição criada', [
            'id' => $proposicao->id,
            'autor_id' => $proposicao->autor_id,
            'tipo' => $proposicao->tipo
        ]);
    }

    /**
     * Handle the Proposicao "updated" event.
     */
    public function updated(Proposicao $proposicao): void
    {
        $this->invalidateRelatedCache($proposicao);
        
        // Invalidar cache de PDF se arquivo foi alterado
        if ($proposicao->wasChanged(['arquivo_path', 'conteudo', 'status'])) {
            $this->cacheService->invalidarCachePDF($proposicao->id);
        }

        // Log mudanças importantes
        $changes = $proposicao->getChanges();
        if (isset($changes['status'])) {
            Log::info('Status da proposição alterado', [
                'id' => $proposicao->id,
                'status_anterior' => $proposicao->getOriginal('status'),
                'status_novo' => $changes['status']
            ]);
        }
    }

    /**
     * Handle the Proposicao "deleted" event.
     */
    public function deleted(Proposicao $proposicao): void
    {
        $this->invalidateRelatedCache($proposicao);
        
        // Limpar arquivos relacionados
        $this->cleanupFiles($proposicao);
        
        Log::info('Proposição excluída', [
            'id' => $proposicao->id,
            'autor_id' => $proposicao->autor_id
        ]);
    }

    /**
     * Invalidar cache relacionado à proposição
     */
    private function invalidateRelatedCache(Proposicao $proposicao): void
    {
        try {
            $this->cacheService->invalidarCacheProposicao(
                $proposicao->id,
                $proposicao->autor_id
            );
        } catch (\Exception $e) {
            Log::warning('Erro ao invalidar cache: ' . $e->getMessage());
        }
    }

    /**
     * Limpar arquivos relacionados à proposição excluída
     */
    private function cleanupFiles(Proposicao $proposicao): void
    {
        try {
            // Limpar PDF
            if ($proposicao->arquivo_pdf_path) {
                $pdfPath = storage_path('app/' . $proposicao->arquivo_pdf_path);
                if (file_exists($pdfPath)) {
                    unlink($pdfPath);
                }
            }

            // Limpar diretório de PDFs se vazio
            $pdfDir = storage_path('app/proposicoes/pdfs/' . $proposicao->id);
            if (is_dir($pdfDir) && count(scandir($pdfDir)) === 2) { // apenas . e ..
                rmdir($pdfDir);
            }

            // Limpar arquivo DOCX se for temporário
            if ($proposicao->arquivo_path && str_contains($proposicao->arquivo_path, '_temp_')) {
                $docxPath = storage_path('app/' . $proposicao->arquivo_path);
                if (file_exists($docxPath)) {
                    unlink($docxPath);
                }
            }

        } catch (\Exception $e) {
            Log::warning('Erro ao limpar arquivos da proposição: ' . $e->getMessage());
        }
    }
}