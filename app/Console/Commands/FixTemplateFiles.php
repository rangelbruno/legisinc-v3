<?php

namespace App\Console\Commands;

use App\Models\TipoProposicaoTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixTemplateFiles extends Command
{
    protected $signature = 'templates:fix-files';
    protected $description = 'Fix template files that are missing or have broken paths';

    public function handle()
    {
        $this->info('🔧 Verificando e corrigindo arquivos de templates...');
        
        $templates = TipoProposicaoTemplate::all();
        $fixed = 0;
        $missing = 0;
        
        foreach ($templates as $template) {
            if (!$template->arquivo_path) {
                $this->warn("Template {$template->id}: Sem arquivo_path definido");
                continue;
            }
            
            if (!Storage::exists($template->arquivo_path)) {
                $this->error("Template {$template->id}: Arquivo não existe: {$template->arquivo_path}");
                $missing++;
                
                // Tentar encontrar um arquivo similar
                $similarFile = $this->findSimilarFile($template->arquivo_path);
                if ($similarFile) {
                    $template->arquivo_path = $similarFile;
                    $template->save();
                    $this->info("  ✅ Corrigido para: {$similarFile}");
                    $fixed++;
                } else {
                    // Criar um arquivo básico se não encontrar
                    $defaultContent = $this->createDefaultContent($template);
                    Storage::put($template->arquivo_path, $defaultContent);
                    $this->info("  ✅ Arquivo básico criado");
                    $fixed++;
                }
            } else {
                $this->line("✅ Template {$template->id}: OK");
            }
        }
        
        $this->info("\n📊 Resumo:");
        $this->info("Total de templates: {$templates->count()}");
        $this->info("Arquivos faltando: {$missing}");
        $this->info("Corrigidos: {$fixed}");
        
        return 0;
    }
    
    private function findSimilarFile($originalPath)
    {
        $basename = basename($originalPath, '.rtf');
        $directory = dirname($originalPath);
        
        // Procurar arquivos com nomes similares
        $files = Storage::files($directory);
        
        foreach ($files as $file) {
            if (str_contains($file, $basename) || str_contains($basename, basename($file, '.rtf'))) {
                return $file;
            }
        }
        
        return null;
    }
    
    private function createDefaultContent($template)
    {
        $tipoNome = $template->tipoProposicao->nome ?? 'Documento';
        
        return "{\\rtf1\\ansi\\deff0 {\\fonttbl {\\f0 Times New Roman;}}
{\\f0\\fs24 
{\\qc\\b " . strtoupper($tipoNome) . "\\par}
\\par
{\\qc N° \$numero_proposicao/\$ano_atual\\par}
\\par
EMENTA: \$ementa
\\par\\par
\$texto
\\par\\par
Sala das Sessões, em _____ de _____________ de _______.
\\par\\par
_________________________________\\par
Vereador(a)
}}";
    }
}