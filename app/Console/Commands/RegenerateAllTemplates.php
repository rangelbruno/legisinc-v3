<?php

namespace App\Console\Commands;

use App\Models\TipoProposicaoTemplate;
use App\Services\OnlyOffice\OnlyOfficeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RegenerateAllTemplates extends Command
{
    protected $signature = 'templates:regenerate-all {--force}';
    protected $description = 'Regenerate all templates with parameter variables instead of hardcoded data';

    public function handle()
    {
        $force = $this->option('force');
        
        $templates = TipoProposicaoTemplate::with('tipoProposicao')->get();
        
        if ($templates->isEmpty()) {
            $this->info('No templates found to regenerate');
            return 0;
        }

        $this->info("Found {$templates->count()} templates to regenerate");
        
        if (!$force && !$this->confirm('Are you sure you want to regenerate all templates? This will replace existing template content.')) {
            $this->info('Operation cancelled');
            return 0;
        }

        $onlyOfficeService = app(OnlyOfficeService::class);
        $reflection = new \ReflectionClass($onlyOfficeService);
        $method = $reflection->getMethod('garantirArquivoTemplate');
        $method->setAccessible(true);

        $regenerated = 0;
        $errors = 0;

        foreach ($templates as $template) {
            try {
                $this->info("Processing template {$template->id} for {$template->tipoProposicao->nome}...");
                
                // Clear existing content to force regeneration
                if ($template->arquivo_path && Storage::exists($template->arquivo_path)) {
                    Storage::delete($template->arquivo_path);
                }
                
                $template->update(['arquivo_path' => null, 'conteudo' => null]);
                
                // Force regeneration
                $method->invoke($onlyOfficeService, $template);
                
                $template->refresh();
                
                // Verify regeneration was successful
                if ($template->arquivo_path && Storage::exists($template->arquivo_path)) {
                    $content = Storage::get($template->arquivo_path);
                    
                    // Check if template has variables instead of hardcoded data
                    $hasVariables = strpos($content, '${cabecalho_nome_camara}') !== false;
                    $hasHardcoded = strpos($content, 'CÂMARA MUNICIPAL DE SÃO PAULO') !== false;
                    
                    if ($hasVariables && !$hasHardcoded) {
                        $this->info("  ✅ Template {$template->id} regenerated successfully with variables");
                        $regenerated++;
                    } else {
                        $this->error("  ❌ Template {$template->id} regenerated but still has issues");
                        $errors++;
                    }
                } else {
                    $this->error("  ❌ Failed to regenerate template {$template->id}");
                    $errors++;
                }
                
            } catch (\Exception $e) {
                $this->error("  ❌ Error regenerating template {$template->id}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->info("\nRegeneration complete!");
        $this->info("Successfully regenerated: {$regenerated}");
        $this->error("Errors: {$errors}");

        return $errors > 0 ? 1 : 0;
    }
}