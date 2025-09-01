<?php

namespace App\Console\Commands;

use App\Models\Proposicao;
use App\Services\Template\TemplateUniversalService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RefreshProposicaoTemplate extends Command
{
    protected $signature = 'proposicao:refresh-template
                            {id : ID da proposição}
                            {--force : Forçar recriação mesmo se arquivo exists}';

    protected $description = 'Força refresh do template de uma proposição específica';

    protected TemplateUniversalService $templateUniversalService;

    public function __construct(TemplateUniversalService $templateUniversalService)
    {
        parent::__construct();
        $this->templateUniversalService = $templateUniversalService;
    }

    public function handle()
    {
        $id = $this->argument('id');
        $force = $this->option('force');

        $proposicao = Proposicao::find($id);

        if (!$proposicao) {
            $this->error("❌ Proposição {$id} não encontrada!");
            return 1;
        }

        $this->info("🔄 Refreshing template da proposição {$id}...");

        try {
            // Aplicar template universal
            $conteudo = $this->templateUniversalService->aplicarTemplateParaProposicao($proposicao);
            
            // Criar novo arquivo
            $timestamp = time();
            $novoArquivo = "proposicoes/proposicao_{$id}_refresh_{$timestamp}.rtf";
            $caminhoCompleto = storage_path('app/' . $novoArquivo);
            
            $diretorio = dirname($caminhoCompleto);
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0755, true);
            }
            
            file_put_contents($caminhoCompleto, $conteudo);
            
            // Atualizar proposição
            $arquivoAnterior = $proposicao->arquivo_path;
            $proposicao->arquivo_path = $novoArquivo;
            $proposicao->ultima_modificacao = now();
            $proposicao->save();
            
            $this->info("✅ Template refreshed!");
            $this->line("   Arquivo anterior: " . ($arquivoAnterior ?: 'null'));
            $this->line("   Novo arquivo: {$novoArquivo}");
            
            // Verificar variáveis
            if (preg_match_all('/\$[a-zA-Z_][a-zA-Z0-9_]*/', $conteudo, $matches)) {
                $this->warn("⚠️  Variáveis não substituídas encontradas:");
                foreach (array_unique($matches[0]) as $var) {
                    $this->line("   - {$var}");
                }
            } else {
                $this->info("✅ Todas as variáveis foram substituídas!");
            }
            
            // Limpar cache
            Cache::flush();
            $this->info("🧹 Cache limpo!");
            
            $this->line("");
            $this->info("🎯 Instruções para o usuário:");
            $this->line("1. Pressionar Ctrl+F5 no navegador para limpar cache");
            $this->line("2. Recarregar /proposicoes/{$id}/onlyoffice/editor-parlamentar");
            $this->line("3. Verificar se as variáveis estão substituídas");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Erro ao refreshar template: " . $e->getMessage());
            return 1;
        }
    }
}