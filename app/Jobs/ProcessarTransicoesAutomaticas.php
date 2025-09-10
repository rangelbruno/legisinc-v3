<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Services\WorkflowTransitionService;

class ProcessarTransicoesAutomaticas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Model $documento;
    protected array $dadosAdicionais;
    
    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(Model $documento, array $dadosAdicionais = [])
    {
        $this->documento = $documento;
        $this->dadosAdicionais = $dadosAdicionais;
    }

    public function handle(WorkflowTransitionService $transitionService): void
    {
        try {
            Log::info('Processando transições automáticas', [
                'documento_id' => $this->documento->id,
                'documento_type' => get_class($this->documento)
            ]);

            $executou = $transitionService->processarTransicoesAutomaticas($this->documento);
            
            if ($executou) {
                Log::info('Transições automáticas executadas com sucesso', [
                    'documento_id' => $this->documento->id
                ]);
            } else {
                Log::debug('Nenhuma transição automática disponível', [
                    'documento_id' => $this->documento->id
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao processar transições automáticas', [
                'documento_id' => $this->documento->id,
                'documento_type' => get_class($this->documento),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Job de transições automáticas falhou definitivamente', [
            'documento_id' => $this->documento->id,
            'documento_type' => get_class($this->documento),
            'error' => $exception->getMessage()
        ]);
    }
}