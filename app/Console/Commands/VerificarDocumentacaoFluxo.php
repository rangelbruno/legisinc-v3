<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class VerificarDocumentacaoFluxo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docs:verificar-fluxo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica e atualiza a documentação do fluxo de proposições';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando documentação do fluxo de proposições...');
        
        $filePath = base_path('docs/FLUXO-PROPOSICOES-MERMAID.md');
        
        if (!File::exists($filePath)) {
            $this->error('❌ Arquivo não encontrado: ' . $filePath);
            return 1;
        }
        
        // Informações do arquivo
        $lastModified = File::lastModified($filePath);
        $size = File::size($filePath);
        
        $this->info('📄 Arquivo: docs/FLUXO-PROPOSICOES-MERMAID.md');
        $this->info('📅 Última modificação: ' . date('d/m/Y H:i:s', $lastModified));
        $this->info('📊 Tamanho: ' . number_format($size / 1024, 2) . ' KB');
        
        // Verificar cache
        $cacheKey = 'docs.fluxo-proposicoes.' . $lastModified;
        
        if (Cache::has($cacheKey)) {
            $this->info('✅ Cache está atualizado');
        } else {
            $this->warn('⚠️ Cache desatualizado ou inexistente');
            $this->info('🔄 Atualizando cache...');
            
            try {
                $content = File::get($filePath);
                $converter = new GithubFlavoredMarkdownConverter([
                    'html_input' => 'strip',
                    'allow_unsafe_links' => false,
                    'max_nesting_level' => 10
                ]);
                
                $htmlContent = $converter->convert($content)->getContent();
                Cache::put($cacheKey, $htmlContent, 3600);
                
                $this->info('✅ Cache atualizado com sucesso!');
            } catch (\Exception $e) {
                $this->error('❌ Erro ao atualizar cache: ' . $e->getMessage());
                return 1;
            }
        }
        
        // Análise do conteúdo
        $this->newLine();
        $this->info('📋 Análise do conteúdo:');
        
        $content = File::get($filePath);
        
        // Contar elementos
        $mermaidCount = substr_count($content, '```mermaid');
        $h1Count = substr_count($content, '# ');
        $h2Count = substr_count($content, '## ');
        $h3Count = substr_count($content, '### ');
        $checkCount = substr_count($content, '✅');
        
        $this->table(
            ['Elemento', 'Quantidade'],
            [
                ['Diagramas Mermaid', $mermaidCount],
                ['Títulos (H1)', $h1Count],
                ['Subtítulos (H2)', $h2Count],
                ['Seções (H3)', $h3Count],
                ['Itens concluídos (✅)', $checkCount],
            ]
        );
        
        // Verificar versão e status
        if (str_contains($content, 'v2.0')) {
            $this->info('✅ Versão 2.0 detectada (com melhores práticas)');
        }
        
        if (str_contains($content, 'Produção com Melhores Práticas')) {
            $this->info('✅ Status: Produção com Melhores Práticas');
        }
        
        // URL de acesso
        $this->newLine();
        $this->info('🌐 Acesso via navegador:');
        $this->line('   http://localhost:8001/admin/docs/fluxo-proposicoes');
        
        $this->newLine();
        $this->info('✅ Verificação concluída com sucesso!');
        
        return 0;
    }
}
