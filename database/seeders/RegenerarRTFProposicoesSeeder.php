<?php

namespace Database\Seeders;

use App\Models\Proposicao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RegenerarRTFProposicoesSeeder extends Seeder
{
    /**
     * Regenerar RTFs de proposições após reset do banco para garantir sincronização
     */
    public function run(): void
    {
        $this->comment('🔄 REGENERANDO RTFs DE PROPOSIÇÕES APÓS RESET...');
        
        // Buscar proposições que têm arquivo_path mas o arquivo não existe mais
        $proposicoes = Proposicao::whereNotNull('arquivo_path')
            ->where('status', '!=', 'rascunho') // Não processar rascunhos
            ->get();
        
        if ($proposicoes->count() === 0) {
            $this->info('📝 Nenhuma proposição encontrada para regeneração');
            return;
        }
        
        $this->info('📋 Encontradas ' . $proposicoes->count() . ' proposições para verificação');
        
        $templateService = app(\App\Services\Template\TemplateUniversalService::class);
        $regeneradas = 0;
        $erros = 0;
        
        foreach ($proposicoes as $proposicao) {
            // Verificar se RTF existe
            $rtfExists = $proposicao->arquivo_path && Storage::exists($proposicao->arquivo_path);
            
            if (!$rtfExists) {
                $this->warn("⚠️  Proposição {$proposicao->id}: RTF não existe, procurando RTFs do OnlyOffice...");
                
                // PRIORIDADE 1: Procurar RTFs do OnlyOffice existentes (grandes, com edições)
                $rtfDoOnlyOfficeEncontrado = $this->encontrarRTFDoOnlyOffice($proposicao->id);
                
                if ($rtfDoOnlyOfficeEncontrado) {
                    $this->info("   📄 RTF do OnlyOffice encontrado: {$rtfDoOnlyOfficeEncontrado}");
                    
                    // Usar RTF do OnlyOffice em vez de regenerar
                    $proposicao->update([
                        'arquivo_path' => $rtfDoOnlyOfficeEncontrado,
                        'arquivo_pdf_path' => null,
                        'pdf_gerado_em' => null,
                        'pdf_conversor_usado' => null
                    ]);
                    
                    $this->info("   ✅ Proposição {$proposicao->id} atualizada com RTF do OnlyOffice");
                    $regeneradas++;
                    
                } else {
                    // PRIORIDADE 2: Regenerar apenas se não existe RTF do OnlyOffice
                    try {
                        $conteudoRTF = $templateService->aplicarTemplateParaProposicao($proposicao);
                        
                        // Salvar novo RTF
                        $nomeArquivo = 'proposicao_' . $proposicao->id . '_' . time() . '.rtf';
                        $caminhoRelativo = 'proposicoes/' . $nomeArquivo;
                        
                        $salvou = Storage::put($caminhoRelativo, $conteudoRTF);
                        
                        if ($salvou) {
                            $proposicao->update([
                                'arquivo_path' => $caminhoRelativo,
                                'arquivo_pdf_path' => null,
                                'pdf_gerado_em' => null,
                                'pdf_conversor_usado' => null
                            ]);
                            
                            $this->info("   ✅ RTF regenerado: {$caminhoRelativo} (" . number_format(strlen($conteudoRTF)) . " bytes)");
                            $regeneradas++;
                        } else {
                            $this->error("   ❌ Erro ao salvar RTF para proposição {$proposicao->id}");
                            $erros++;
                        }
                        
                    } catch (\Exception $e) {
                        $this->error("   ❌ Erro na proposição {$proposicao->id}: " . $e->getMessage());
                        $erros++;
                    }
                }
            } else {
                // RTF existe, apenas invalidar cache PDF se necessário
                if ($proposicao->arquivo_pdf_path || $proposicao->pdf_gerado_em) {
                    $proposicao->update([
                        'arquivo_pdf_path' => null,
                        'pdf_gerado_em' => null,
                        'pdf_conversor_usado' => null
                    ]);
                    $this->info("   🔄 Cache PDF invalidado para proposição {$proposicao->id}");
                }
            }
        }
        
        // Limpar PDFs antigos órfãos
        $this->limparPDFsOrfaos();
        
        if ($regeneradas > 0) {
            $this->info("✅ {$regeneradas} RTFs regenerados com sucesso");
        }
        if ($erros > 0) {
            $this->warn("⚠️  {$erros} erros encontrados");
        }
        
        $this->info('✅ Regeneração de RTFs concluída!');
        $this->newLine();
    }
    
    private function limparPDFsOrfaos()
    {
        $this->info('🧹 Limpando PDFs antigos órfãos...');
        
        $diretoriosParaLimpar = [
            'private/pdfs_oficiais',
            'proposicoes/pdfs',
            'pdfs'
        ];
        
        $limpas = 0;
        foreach ($diretoriosParaLimpar as $diretorio) {
            if (Storage::exists($diretorio)) {
                $subdiretorios = Storage::directories($diretorio);
                foreach ($subdiretorios as $subdir) {
                    // Extrair ID da proposição do nome do diretório
                    $proposicaoId = basename($subdir);
                    if (is_numeric($proposicaoId)) {
                        // Verificar se proposição ainda existe
                        $proposicaoExists = Proposicao::where('id', $proposicaoId)->exists();
                        if (!$proposicaoExists) {
                            Storage::deleteDirectory($subdir);
                            $this->info("   🗑️  Removido diretório órfão: {$subdir}");
                            $limpas++;
                        }
                    }
                }
            }
        }
        
        if ($limpas > 0) {
            $this->info("✅ {$limpas} diretórios órfãos removidos");
        } else {
            $this->info("📂 Nenhum diretório órfão encontrado");
        }
    }
    
    /**
     * Procurar RTF do OnlyOffice para proposição (arquivo grande com edições)
     */
    private function encontrarRTFDoOnlyOffice(int $proposicaoId): ?string
    {
        $diretorioProposicoes = storage_path('app/proposicoes');
        $pattern = "proposicao_{$proposicaoId}_*.rtf";
        
        $arquivos = glob("{$diretorioProposicoes}/{$pattern}");
        if (empty($arquivos)) {
            return null;
        }
        
        // Filtrar RTFs grandes (indicam edições do OnlyOffice)
        // RTFs do template universal têm ~63KB, RTFs do OnlyOffice têm 900KB+
        $rtfsGrandes = [];
        foreach ($arquivos as $arquivo) {
            $tamanho = filesize($arquivo);
            if ($tamanho > 500000) { // Maior que 500KB = provavelmente do OnlyOffice
                $rtfsGrandes[] = [
                    'arquivo' => $arquivo,
                    'tamanho' => $tamanho,
                    'modificado' => filemtime($arquivo)
                ];
            }
        }
        
        if (empty($rtfsGrandes)) {
            return null;
        }
        
        // Ordenar por modificação mais recente
        usort($rtfsGrandes, function($a, $b) {
            return $b['modificado'] - $a['modificado'];
        });
        
        $melhorRTF = $rtfsGrandes[0];
        $nomeRelativo = 'proposicoes/' . basename($melhorRTF['arquivo']);
        
        return $nomeRelativo;
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