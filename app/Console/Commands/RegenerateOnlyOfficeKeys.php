<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Documento\DocumentoModelo;
use App\Models\Documento\DocumentoInstancia;

class RegenerateOnlyOfficeKeys extends Command
{
    protected $signature = 'onlyoffice:regenerate-keys';
    protected $description = 'Regenerate OnlyOffice document keys to fix version conflicts';

    public function handle()
    {
        $this->info('Regenerating OnlyOffice document keys...');
        
        // Regenerar para modelos
        $modelos = DocumentoModelo::all();
        foreach ($modelos as $modelo) {
            $novoKey = 'modelo_' . time() . '_' . uniqid() . '_' . rand(1000, 9999);
            $modelo->update(['document_key' => $novoKey]);
            $this->line("Modelo {$modelo->id} - {$modelo->nome}: {$novoKey}");
        }
        
        // Regenerar para instâncias
        $instancias = DocumentoInstancia::all();
        foreach ($instancias as $instancia) {
            $novoKey = 'instancia_' . time() . '_' . uniqid() . '_' . rand(1000, 9999);
            $instancia->update(['document_key' => $novoKey]);
            $this->line("Instância {$instancia->id} - {$instancia->titulo}: {$novoKey}");
        }
        
        $this->info("Keys regenerados: {$modelos->count()} modelos, {$instancias->count()} instâncias");
        return 0;
    }
}