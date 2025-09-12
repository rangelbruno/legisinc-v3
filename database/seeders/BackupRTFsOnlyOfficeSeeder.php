<?php

namespace Database\Seeders;

use App\Models\Proposicao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BackupRTFsOnlyOfficeSeeder extends Seeder
{
    /**
     * Fazer backup dos RTFs do OnlyOffice antes do reset para restaurá-los depois
     */
    public function run(): void
    {
        // RTF backup is disabled to reduce output noise
        $this->info('💾 RTF backup disabled');
    }
    
    private function criarMapeamentoRTFs()
    {
        $mapeamento = [];
        $proposicoes = Proposicao::whereNotNull('arquivo_path')->get();
        
        foreach ($proposicoes as $proposicao) {
            if ($proposicao->arquivo_path && Storage::exists($proposicao->arquivo_path)) {
                $rtfPath = Storage::path($proposicao->arquivo_path);
                $tamanho = filesize($rtfPath);
                
                if ($tamanho > 500000) {
                    $mapeamento[$proposicao->id] = [
                        'arquivo_path' => $proposicao->arquivo_path,
                        'tamanho' => $tamanho,
                        'ementa' => substr($proposicao->ementa, 0, 100),
                        'status' => $proposicao->status
                    ];
                }
            }
        }
        
        if (!empty($mapeamento)) {
            $jsonPath = storage_path('app/backups/rtfs_onlyoffice/mapeamento.json');
            file_put_contents($jsonPath, json_encode($mapeamento, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->info("   📋 Mapeamento salvo: " . count($mapeamento) . " proposições");
        }
    }
    
    // Helper methods
    private function info($message)
    {
        echo "\033[0;32m$message\033[0m\n";
    }
    
    private function comment($message)
    {
        echo "\033[0;36m$message\033[0m\n";
    }
    
    private function newLine()
    {
        echo "\n";
    }
}