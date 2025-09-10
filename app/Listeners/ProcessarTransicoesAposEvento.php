<?php

namespace App\Listeners;

use App\Jobs\ProcessarTransicoesAutomaticas;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessarTransicoesAposEvento implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle($event): void
    {
        $documento = $this->extrairDocumentoDoEvento($event);
        
        if (!$documento) {
            return;
        }

        Log::debug('Disparando processamento de transições automáticas', [
            'evento' => get_class($event),
            'documento_id' => $documento->id,
            'documento_type' => get_class($documento)
        ]);

        // Disparar job para processar transições automáticas
        ProcessarTransicoesAutomaticas::dispatch($documento)
            ->delay(now()->addSeconds(5)); // Pequeno delay para garantir consistência
    }

    /**
     * Extrai o documento do evento
     */
    protected function extrairDocumentoDoEvento($event): ?Model
    {
        // Eventos padrão do Eloquent
        if (property_exists($event, 'model') && $event->model instanceof Model) {
            return $event->model;
        }

        // Eventos customizados que possam ter documento
        if (property_exists($event, 'documento') && $event->documento instanceof Model) {
            return $event->documento;
        }

        // Eventos de workflow específicos
        if (property_exists($event, 'data') && isset($event->data['documento'])) {
            return $event->data['documento'];
        }

        return null;
    }

    public function failed($event, \Throwable $exception): void
    {
        Log::error('Falha ao processar transições automáticas após evento', [
            'evento' => get_class($event),
            'error' => $exception->getMessage()
        ]);
    }
}