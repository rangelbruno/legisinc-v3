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
        $this->comment('💾 FAZENDO BACKUP DOS RTFs DO ONLYOFFICE...');
        
        $backupDir = 'backups/rtfs_onlyoffice';
        $backupCompleto = storage_path("app/{$backupDir}");
        
        // Criar diretório de backup
        if (!is_dir($backupCompleto)) {
            mkdir($backupCompleto, 0755, true);
        }
        
        // Procurar RTFs grandes do OnlyOffice (>500KB)
        $diretorioProposicoes = storage_path('app/proposicoes');
        $rtfsEncontrados = glob("{$diretorioProposicoes}/proposicao_*_*.rtf");
        
        $backups = 0;
        foreach ($rtfsEncontrados as $rtfPath) {
            $tamanho = filesize($rtfPath);
            if ($tamanho > 500000) { // RTFs do OnlyOffice
                $nomeArquivo = basename($rtfPath);
                $backupPath = "{$backupCompleto}/{$nomeArquivo}";
                
                if (copy($rtfPath, $backupPath)) {
                    $this->info("   📄 Backup: {$nomeArquivo} (" . number_format($tamanho) . " bytes)");
                    $backups++;
                }
            }
        }
        
        if ($backups > 0) {
            $this->info("✅ {$backups} RTFs do OnlyOffice salvos em backup");
            
            // Criar arquivo de mapeamento proposicao -> RTF
            $this->criarMapeamentoRTFs();
        } else {
            $this->info("📂 Nenhum RTF grande encontrado para backup");
        }
        
        $this->newLine();
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