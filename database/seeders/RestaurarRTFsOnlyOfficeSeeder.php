<?php

namespace Database\Seeders;

use App\Models\Proposicao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class RestaurarRTFsOnlyOfficeSeeder extends Seeder
{
    /**
     * Restaurar RTFs do OnlyOffice após reset usando backup
     */
    public function run(): void
    {
        $this->comment('♻️ RESTAURANDO RTFs DO ONLYOFFICE...');
        
        $backupDir = storage_path('app/backups/rtfs_onlyoffice');
        $mapeamentoPath = "{$backupDir}/mapeamento.json";
        
        if (!file_exists($mapeamentoPath)) {
            $this->info('📂 Nenhum backup de RTFs encontrado');
            return;
        }
        
        $mapeamento = json_decode(file_get_contents($mapeamentoPath), true);
        if (empty($mapeamento)) {
            $this->info('📂 Mapeamento de RTFs vazio');
            return;
        }
        
        $this->info('📋 Encontrados ' . count($mapeamento) . ' RTFs no backup');
        
        $restaurados = 0;
        $erros = 0;
        
        foreach ($mapeamento as $proposicaoId => $info) {
            $proposicao = Proposicao::find($proposicaoId);
            if (!$proposicao) {
                $this->warn("   ⚠️  Proposição {$proposicaoId} não existe mais");
                continue;
            }
            
            // Verificar se RTF backup existe
            $nomeRTFBackup = basename($info['arquivo_path']);
            $backupRTFPath = "{$backupDir}/{$nomeRTFBackup}";
            
            if (!file_exists($backupRTFPath)) {
                $this->error("   ❌ Backup RTF não encontrado: {$nomeRTFBackup}");
                $erros++;
                continue;
            }
            
            try {
                // Ler RTF do backup
                $conteudoRTF = file_get_contents($backupRTFPath);
                
                // Salvar RTF restaurado
                $nomeRTFNovo = 'proposicao_' . $proposicaoId . '_restaurado_' . time() . '.rtf';
                $caminhoRTFNovo = 'proposicoes/' . $nomeRTFNovo;
                
                $salvou = Storage::put($caminhoRTFNovo, $conteudoRTF);
                
                if ($salvou) {
                    // Atualizar proposição
                    $proposicao->update([
                        'arquivo_path' => $caminhoRTFNovo,
                        'arquivo_pdf_path' => null,
                        'pdf_gerado_em' => null,
                        'pdf_conversor_usado' => null
                    ]);
                    
                    $this->info("   ✅ Proposição {$proposicaoId}: RTF restaurado (" . number_format(strlen($conteudoRTF)) . " bytes)");
                    $this->info("      📄 " . substr($info['ementa'], 0, 60) . "...");
                    $restaurados++;
                } else {
                    $this->error("   ❌ Erro ao salvar RTF restaurado para proposição {$proposicaoId}");
                    $erros++;
                }
                
            } catch (\Exception $e) {
                $this->error("   ❌ Erro na proposição {$proposicaoId}: " . $e->getMessage());
                $erros++;
            }
        }
        
        if ($restaurados > 0) {
            $this->info("✅ {$restaurados} RTFs do OnlyOffice restaurados com sucesso");
        }
        if ($erros > 0) {
            $this->warn("⚠️  {$erros} erros encontrados durante restauração");
        }
        
        $this->info('✅ Restauração de RTFs concluída!');
        $this->newLine();
    }
    
    // Helper methods
    private function info($message)
    {
        echo "\033[0;32m$message\033[0m\n";
    }
    
    private function warn($message)
    {
        echo "\033[0;33m$message\033[0m\n";
    }
    
    private function error($message)
    {
        echo "\033[0;31m$message\033[0m\n";
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