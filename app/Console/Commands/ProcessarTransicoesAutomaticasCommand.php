<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DocumentoWorkflowStatus;
use App\Services\WorkflowTransitionService;
use App\Jobs\ProcessarTransicoesAutomaticas;
use Illuminate\Support\Facades\Log;

class ProcessarTransicoesAutomaticasCommand extends Command
{
    protected $signature = 'workflow:process-automatic-transitions 
                          {--documento-type= : Filtrar por tipo de documento específico}
                          {--documento-id= : Processar documento específico}
                          {--dry-run : Simular execução sem processar}
                          {--batch-size=50 : Tamanho do lote para processamento}';

    protected $description = 'Processa transições automáticas pendentes para documentos em workflow';

    public function handle(WorkflowTransitionService $transitionService): int
    {
        $this->info('Iniciando processamento de transições automáticas...');

        $query = DocumentoWorkflowStatus::query()
            ->where('ativo', true)
            ->where('status', '!=', 'concluido')
            ->with(['workflow', 'etapa']);

        // Aplicar filtros opcionais
        if ($this->option('documento-type')) {
            $query->where('documento_type', $this->option('documento-type'));
        }

        if ($this->option('documento-id')) {
            $query->where('documento_id', $this->option('documento-id'));
        }

        $batchSize = (int) $this->option('batch-size');
        $totalProcessados = 0;
        $totalComTransicoes = 0;

        $this->withProgressBar($query->count(), function ($bar) use ($query, $transitionService, $batchSize, &$totalProcessados, &$totalComTransicoes) {
            $query->chunk($batchSize, function ($statusList) use ($transitionService, $bar, &$totalProcessados, &$totalComTransicoes) {
                foreach ($statusList as $status) {
                    $totalProcessados++;
                    
                    try {
                        // Obter o documento
                        $documentoType = $status->documento_type;
                        $documento = $documentoType::find($status->documento_id);
                        
                        if (!$documento) {
                            $this->warn("Documento não encontrado: {$status->documento_type}#{$status->documento_id}");
                            continue;
                        }

                        if ($this->option('dry-run')) {
                            // Simular: apenas verificar se há transições automáticas
                            $transicoes = $transitionService->obterTransicoesDisponiveis($documento);
                            $automaticas = array_filter($transicoes, fn($t) => $t['automatica'] ?? false);
                            
                            if (count($automaticas) > 0) {
                                $totalComTransicoes++;
                                $this->line("  [DRY-RUN] {$status->documento_type}#{$status->documento_id}: " . count($automaticas) . " transições automáticas disponíveis");
                            }
                        } else {
                            // Processar transições automáticas
                            if ($transitionService->processarTransicoesAutomaticas($documento)) {
                                $totalComTransicoes++;
                                $this->line("  ✓ Processado: {$status->documento_type}#{$status->documento_id}");
                            }
                        }

                    } catch (\Exception $e) {
                        $this->error("  ✗ Erro ao processar {$status->documento_type}#{$status->documento_id}: {$e->getMessage()}");
                        
                        Log::error('Erro no comando de transições automáticas', [
                            'documento_type' => $status->documento_type,
                            'documento_id' => $status->documento_id,
                            'error' => $e->getMessage()
                        ]);
                    }
                    
                    $bar->advance();
                }
            });
        });

        $this->newLine();
        
        if ($this->option('dry-run')) {
            $this->info("Simulação concluída:");
            $this->line("  • Documentos analisados: {$totalProcessados}");
            $this->line("  • Com transições automáticas: {$totalComTransicoes}");
        } else {
            $this->info("Processamento concluído:");
            $this->line("  • Documentos processados: {$totalProcessados}");
            $this->line("  • Com transições executadas: {$totalComTransicoes}");
        }

        return Command::SUCCESS;
    }
}