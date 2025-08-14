<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TipoProposicaoTemplate;
use App\Services\OnlyOffice\OnlyOfficeService;

class ProcessTemplateImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'templates:process-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processa imagens de cabeçalho nos templates admin (apenas ${imagem_cabecalho})';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🖼️ Processando imagens dos templates admin...');
        
        $onlyOfficeService = app(OnlyOfficeService::class);
        $templates = TipoProposicaoTemplate::whereNotNull('arquivo_path')->get();
        
        $processed = 0;
        
        foreach ($templates as $template) {
            try {
                // Força o processamento chamando criarConfiguracaoTemplate
                $onlyOfficeService->criarConfiguracaoTemplate($template);
                $processed++;
                $this->line("  ✅ Template {$template->id} processado");
            } catch (\Exception $e) {
                $this->error("  ❌ Erro no template {$template->id}: " . $e->getMessage());
            }
        }
        
        $this->info("✅ {$processed} templates processados com sucesso!");
        
        return Command::SUCCESS;
    }
}