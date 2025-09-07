<?php

namespace App\Console\Commands;

use App\Models\Proposicao;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;

class BackfillProposicoesV22 extends Command
{
    protected $signature = 'proposicoes:backfill-v22 
                            {--dry-run : Show what would be done without making changes}
                            {--batch-size=200 : Number of propositions to process per batch}
                            {--force : Skip confirmation prompt}';

    protected $description = 'Backfill arquivo_hash and conteudo_updated_at for existing propositions (v2.2 upgrade)';

    public function handle(): int
    {
        if (!$this->option('force') && !$this->confirm('This will update all existing propositions with new hash fields. Continue?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $isDryRun = $this->option('dry-run');
        $batchSize = (int) $this->option('batch-size');

        $this->info('🔄 Starting backfill for Proposições v2.2 fields...');
        $this->info("Mode: " . ($isDryRun ? 'DRY RUN' : 'LIVE'));
        $this->info("Batch size: {$batchSize}");
        $this->newLine();

        // Contadores
        $totalProcessed = 0;
        $hashCalculated = 0;
        $timestampSet = 0;
        $errors = 0;

        // Processar em batches para não sobrecarregar memória
        Proposicao::chunkById($batchSize, function ($proposicoes) use ($isDryRun, &$totalProcessed, &$hashCalculated, &$timestampSet, &$errors) {
            
            foreach ($proposicoes as $proposicao) {
                $totalProcessed++;
                
                try {
                    $updates = [];
                    
                    // 1. Calcular hash do arquivo se existe e ainda não tem
                    if (!$proposicao->arquivo_hash && $proposicao->arquivo_path && Storage::exists($proposicao->arquivo_path)) {
                        $hash = hash('sha256', Storage::get($proposicao->arquivo_path));
                        $updates['arquivo_hash'] = $hash;
                        
                        // Se não tem hash base do PDF, usar o mesmo
                        if (!$proposicao->pdf_base_hash) {
                            $updates['pdf_base_hash'] = $hash;
                        }
                        
                        $hashCalculated++;
                        $this->line("  📄 Hash calculated for proposição {$proposicao->id}: " . substr($hash, 0, 8) . "...");
                    }
                    
                    // 2. Definir timestamp de conteúdo se não existe
                    if (!$proposicao->conteudo_updated_at) {
                        // Usar updated_at existente como base (melhor que nothing)
                        $updates['conteudo_updated_at'] = $proposicao->updated_at ?? $proposicao->created_at;
                        $timestampSet++;
                        $this->line("  🕐 Timestamp set for proposição {$proposicao->id}");
                    }
                    
                    // 3. Mapear PDF legado para novo campo se necessário
                    if (!$proposicao->arquivo_pdf_para_assinatura && $proposicao->arquivo_pdf_path && Storage::exists($proposicao->arquivo_pdf_path)) {
                        $updates['arquivo_pdf_para_assinatura'] = $proposicao->arquivo_pdf_path;
                        $this->line("  📋 PDF mapped for proposição {$proposicao->id}");
                    }
                    
                    // 4. Definir conversor padrão se não existe
                    if (!$proposicao->pdf_conversor_usado && $proposicao->arquivo_pdf_path) {
                        $updates['pdf_conversor_usado'] = 'legacy';
                    }
                    
                    // Aplicar updates se houver
                    if (!empty($updates) && !$isDryRun) {
                        $proposicao->update($updates);
                        $this->line("  ✅ Updated proposição {$proposicao->id} with " . count($updates) . " fields");
                    } elseif (!empty($updates) && $isDryRun) {
                        $this->line("  🔍 Would update proposição {$proposicao->id}: " . implode(', ', array_keys($updates)));
                    }
                    
                } catch (Exception $e) {
                    $errors++;
                    $this->error("  ❌ Error processing proposição {$proposicao->id}: " . $e->getMessage());
                }
            }
            
            // Progress indicator
            $this->info("Processed batch ending with ID {$proposicoes->last()->id} (Total: {$totalProcessed})");
        });

        $this->newLine();
        $this->info('📊 BACKFILL SUMMARY');
        $this->line("Total processed: {$totalProcessed}");
        $this->line("Hashes calculated: {$hashCalculated}");
        $this->line("Timestamps set: {$timestampSet}"); 
        $this->line("Errors: {$errors}");

        if ($isDryRun) {
            $this->warn('🔍 This was a DRY RUN - no changes were made');
            $this->info('Run without --dry-run to apply changes');
        } else {
            $this->info('✅ Backfill completed successfully!');
            
            // Verificar integridade pós-backfill
            $this->newLine();
            $this->info('🔍 Running integrity check...');
            $this->runIntegrityCheck();
        }

        return $errors > 0 ? 1 : 0;
    }

    /**
     * Verificação de integridade pós-backfill
     */
    private function runIntegrityCheck(): void
    {
        // Proposições com arquivo mas sem hash
        $semHash = Proposicao::whereNotNull('arquivo_path')
            ->whereNull('arquivo_hash')
            ->where(function($q) {
                $q->where('arquivo_path', '!=', '')
                  ->orWhereRaw('LENGTH(arquivo_path) > 0');
            })
            ->count();

        // Proposições sem timestamp de conteúdo
        $semTimestamp = Proposicao::whereNull('conteudo_updated_at')->count();

        // PDFs órfãos (path exists no DB mas não no storage)
        $pdfsOrfaos = 0;
        Proposicao::whereNotNull('arquivo_pdf_para_assinatura')
            ->chunk(100, function($proposicoes) use (&$pdfsOrfaos) {
                foreach ($proposicoes as $p) {
                    if ($p->arquivo_pdf_para_assinatura && !Storage::exists($p->arquivo_pdf_para_assinatura)) {
                        $pdfsOrfaos++;
                    }
                }
            });

        $this->table(['Check', 'Count', 'Status'], [
            ['Proposições with file but no hash', $semHash, $semHash > 0 ? '⚠️  NEEDS ATTENTION' : '✅ OK'],
            ['Proposições without content timestamp', $semTimestamp, $semTimestamp > 0 ? '⚠️  NEEDS ATTENTION' : '✅ OK'],
            ['Orphaned PDF files', $pdfsOrfaos, $pdfsOrfaos > 0 ? '⚠️  CLEANUP NEEDED' : '✅ OK']
        ]);

        if ($semHash > 0) {
            $this->warn("Found {$semHash} proposições with files but no hash - they may have missing/corrupted files");
        }

        if ($pdfsOrfaos > 0) {
            $this->warn("Found {$pdfsOrfaos} PDF references pointing to non-existent files");
            $this->info("Consider running: php artisan proposicoes:cleanup-orphaned-pdfs");
        }
    }
}