<?php

namespace App\Services\Performance;

use App\Models\Proposicao;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PDFOptimizationService
{
    private CacheService $cacheService;
    
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Gerar PDF com cache inteligente
     */
    public function gerarPDFOtimizado(Proposicao $proposicao, bool $forceRegeneration = false): string
    {
        $cacheKey = $this->getPDFCacheKey($proposicao);
        
        // Verificar se PDF precisa ser regenerado
        if (!$forceRegeneration && $this->isPDFCacheValid($proposicao, $cacheKey)) {
            $cachedPath = Cache::get($cacheKey);
            if ($cachedPath && Storage::exists($cachedPath)) {
                return $cachedPath;
            }
        }

        // Gerar PDF otimizado
        $startTime = microtime(true);
        $pdfPath = $this->generateOptimizedPDF($proposicao);
        $executionTime = microtime(true) - $startTime;

        // Cachear resultado
        Cache::put($cacheKey, $pdfPath, 3600); // 1 hora
        
        // Log para monitoramento
        Log::info('PDF gerado', [
            'proposicao_id' => $proposicao->id,
            'execution_time' => round($executionTime * 1000, 2),
            'file_size' => Storage::size($pdfPath),
            'cache_key' => $cacheKey
        ]);

        return $pdfPath;
    }

    /**
     * Verificar se cache do PDF é válido
     */
    private function isPDFCacheValid(Proposicao $proposicao, string $cacheKey): bool
    {
        if (!Cache::has($cacheKey)) {
            return false;
        }

        $cachedData = Cache::get($cacheKey . '_meta');
        if (!$cachedData) {
            return false;
        }

        // Verificar se proposição foi atualizada após cache
        $proposicaoUpdated = $proposicao->updated_at->timestamp;
        $cacheTimestamp = $cachedData['timestamp'] ?? 0;

        return $proposicaoUpdated <= $cacheTimestamp;
    }

    /**
     * Gerar PDF com otimizações de performance
     */
    private function generateOptimizedPDF(Proposicao $proposicao): string
    {
        $nomePdf = 'proposicao_' . $proposicao->id . '_optimized.pdf';
        $diretorioPdf = 'proposicoes/pdfs/' . $proposicao->id;
        $caminhoPdfRelativo = $diretorioPdf . '/' . $nomePdf;
        $caminhoPdfAbsoluto = storage_path('app/' . $caminhoPdfRelativo);

        // Garantir que diretório existe
        if (!is_dir(dirname($caminhoPdfAbsoluto))) {
            mkdir(dirname($caminhoPdfAbsoluto), 0755, true);
        }

        // Tentar conversão direta DOCX -> PDF (mais rápido e mantém formatação)
        if ($this->tryDirectDocxToPdfConversion($proposicao, $caminhoPdfAbsoluto)) {
            return $caminhoPdfRelativo;
        }

        // Fallback: DomPDF otimizado
        $this->generateOptimizedDomPDF($proposicao, $caminhoPdfAbsoluto);

        return $caminhoPdfRelativo;
    }

    /**
     * Conversão direta DOCX -> PDF (método mais rápido)
     */
    private function tryDirectDocxToPdfConversion(Proposicao $proposicao, string $outputPath): bool
    {
        if (!$proposicao->arquivo_path) {
            return false;
        }

        // Buscar arquivo DOCX
        $docxPath = $this->findDocxFile($proposicao->arquivo_path);
        if (!$docxPath) {
            return false;
        }

        // Verificar se LibreOffice está disponível
        if (!$this->isLibreOfficeAvailable()) {
            return false;
        }

        try {
            // Usar arquivo temporário para garantir acesso
            $tempFile = sys_get_temp_dir() . '/proposicao_' . $proposicao->id . '_' . time() . '.docx';
            copy($docxPath, $tempFile);

            // Comando otimizado para conversão
            $command = sprintf(
                'timeout 30s libreoffice --headless --invisible --nodefault --nolockcheck --nologo --norestore --convert-to pdf --outdir %s %s 2>/dev/null',
                escapeshellarg(dirname($outputPath)),
                escapeshellarg($tempFile)
            );

            exec($command, $output, $returnCode);

            // Verificar se conversão foi bem-sucedida
            $expectedPdfPath = dirname($outputPath) . '/' . pathinfo($tempFile, PATHINFO_FILENAME) . '.pdf';
            
            if ($returnCode === 0 && file_exists($expectedPdfPath)) {
                rename($expectedPdfPath, $outputPath);
                unlink($tempFile);
                
                // Otimizar PDF gerado (compressão)
                $this->optimizePDF($outputPath);
                
                return true;
            }

            // Limpeza em caso de erro
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }

        } catch (\Exception $e) {
            Log::warning('Erro na conversão direta DOCX->PDF', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage()
            ]);
        }

        return false;
    }

    /**
     * Gerar PDF otimizado com DomPDF
     */
    private function generateOptimizedDomPDF(Proposicao $proposicao, string $outputPath): void
    {
        // Obter conteúdo otimizado
        $conteudo = $this->getOptimizedContent($proposicao);

        // Template otimizado para PDF
        $html = view('proposicoes.pdf.template-optimized', [
            'proposicao' => $proposicao,
            'conteudo' => $conteudo
        ])->render();

        // Configurações otimizadas do DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        
        // Opções de otimização
        $pdf->setOptions([
            'isRemoteEnabled' => false,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => false,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96, // Reduzir DPI para arquivos menores
        ]);

        // Salvar PDF
        file_put_contents($outputPath, $pdf->output());

        // Otimizar arquivo final
        $this->optimizePDF($outputPath);
    }

    /**
     * Otimizar PDF gerado (compressão)
     */
    private function optimizePDF(string $pdfPath): void
    {
        try {
            // Verificar se Ghostscript está disponível para otimização
            exec('which gs', $gsOutput, $gsReturnCode);
            
            if ($gsReturnCode === 0) {
                $tempOptimized = $pdfPath . '_optimized';
                
                $gsCommand = sprintf(
                    'gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.5 -dPDFSETTINGS=/screen -dNOPAUSE -dQUIET -dBATCH -sOutputFile=%s %s',
                    escapeshellarg($tempOptimized),
                    escapeshellarg($pdfPath)
                );
                
                exec($gsCommand, $gsOutput, $gsReturnCode);
                
                if ($gsReturnCode === 0 && file_exists($tempOptimized)) {
                    $originalSize = filesize($pdfPath);
                    $optimizedSize = filesize($tempOptimized);
                    
                    // Usar versão otimizada se for menor
                    if ($optimizedSize < $originalSize) {
                        rename($tempOptimized, $pdfPath);
                        
                        Log::info('PDF otimizado com Ghostscript', [
                            'original_size' => $originalSize,
                            'optimized_size' => $optimizedSize,
                            'reduction' => round((($originalSize - $optimizedSize) / $originalSize) * 100, 2)
                        ]);
                    } else {
                        unlink($tempOptimized);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Erro na otimização do PDF: ' . $e->getMessage());
        }
    }

    /**
     * Obter conteúdo otimizado da proposição
     */
    private function getOptimizedContent(Proposicao $proposicao): string
    {
        // Priorizar arquivo editado pelo Legislativo
        if ($proposicao->arquivo_path) {
            $docxPath = $this->findDocxFile($proposicao->arquivo_path);
            
            if ($docxPath) {
                try {
                    $extractionService = app(\App\Services\DocumentExtractionService::class);
                    $extractedContent = $extractionService->extractTextFromDocxFile($docxPath);
                    
                    if (!empty($extractedContent) && strlen($extractedContent) > 50) {
                        return $extractedContent;
                    }
                } catch (\Exception $e) {
                    Log::warning('Erro na extração de conteúdo: ' . $e->getMessage());
                }
            }
        }

        // Fallback para conteúdo do banco
        return $proposicao->conteudo ?: $proposicao->ementa;
    }

    /**
     * Encontrar arquivo DOCX em múltiplos locais
     */
    private function findDocxFile(string $arquivoPath): ?string
    {
        $locaisParaBuscar = [
            storage_path('app/' . $arquivoPath),
            storage_path('app/private/' . $arquivoPath),
            storage_path('app/public/' . $arquivoPath),
        ];

        foreach ($locaisParaBuscar as $caminho) {
            if (file_exists($caminho)) {
                return $caminho;
            }
        }

        return null;
    }

    /**
     * Verificar disponibilidade do LibreOffice
     */
    private function isLibreOfficeAvailable(): bool
    {
        static $available = null;
        
        if ($available === null) {
            exec('which libreoffice', $output, $returnCode);
            $available = $returnCode === 0;
        }

        return $available;
    }

    /**
     * Gerar chave de cache para PDF
     */
    private function getPDFCacheKey(Proposicao $proposicao): string
    {
        return sprintf(
            'pdf_optimized_%d_%s',
            $proposicao->id,
            md5($proposicao->updated_at->timestamp . $proposicao->arquivo_path)
        );
    }

    /**
     * Limpeza de PDFs antigos (garbage collection)
     */
    public function cleanupOldPDFs(int $daysOld = 7): int
    {
        $cleaned = 0;
        $cutoffDate = now()->subDays($daysOld);

        try {
            $pdfDirectory = storage_path('app/proposicoes/pdfs');
            
            if (!is_dir($pdfDirectory)) {
                return 0;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($pdfDirectory)
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'pdf') {
                    $modificationTime = \Carbon\Carbon::createFromTimestamp($file->getMTime());
                    
                    if ($modificationTime->isBefore($cutoffDate)) {
                        unlink($file->getPathname());
                        $cleaned++;
                    }
                }
            }

            Log::info('Limpeza de PDFs antigos concluída', [
                'files_cleaned' => $cleaned,
                'days_old' => $daysOld
            ]);

        } catch (\Exception $e) {
            Log::error('Erro na limpeza de PDFs antigos: ' . $e->getMessage());
        }

        return $cleaned;
    }
}