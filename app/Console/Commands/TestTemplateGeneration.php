<?php

namespace App\Console\Commands;

use App\Models\TipoProposicao;
use App\Models\TipoProposicaoTemplate;
use App\Services\OnlyOffice\OnlyOfficeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestTemplateGeneration extends Command
{
    protected $signature = 'test:template-generation {tipo_id}';
    protected $description = 'Test template generation for a specific tipo proposicao';

    public function handle()
    {
        $tipoId = $this->argument('tipo_id');
        
        // Obter o tipo
        $tipo = TipoProposicao::find($tipoId);
        if (!$tipo) {
            $this->error("Tipo de proposição {$tipoId} não encontrado");
            return 1;
        }

        $this->info("Tipo encontrado: " . $tipo->nome);

        // Buscar ou criar template
        $template = TipoProposicaoTemplate::where('tipo_proposicao_id', $tipoId)->first();
        if (!$template) {
            $template = TipoProposicaoTemplate::create([
                'tipo_proposicao_id' => $tipoId,
                'document_key' => 'test_template_' . time(),
                'ativo' => true
            ]);
            $this->info("Template criado com ID: " . $template->id);
        } else {
            $this->info("Template existente com ID: " . $template->id);
        }

        // Limpar template existente
        if ($template->arquivo_path && Storage::exists($template->arquivo_path)) {
            Storage::delete($template->arquivo_path);
            $this->info("Arquivo existente removido");
        }
        
        $template->update(['arquivo_path' => null, 'conteudo' => null]);

        // Testar o OnlyOfficeService
        $onlyOfficeService = app(OnlyOfficeService::class);

        // Forçar regeneração usando ReflectionClass para acessar método privado
        $reflection = new \ReflectionClass($onlyOfficeService);
        $method = $reflection->getMethod('garantirArquivoTemplate');
        $method->setAccessible(true);

        try {
            $method->invoke($onlyOfficeService, $template);
            
            $template->refresh();
            
            $this->info("Template regenerado!");
            $this->info("Arquivo path: " . ($template->arquivo_path ?? 'null'));
            $this->info("Conteúdo length: " . strlen($template->conteudo ?? ''));
            
            if ($template->arquivo_path && Storage::exists($template->arquivo_path)) {
                $conteudo = Storage::get($template->arquivo_path);
                
                $this->line("Conteúdo do arquivo:");
                $this->line("=====================================");
                $this->line($conteudo);
                $this->line("=====================================");
                
                // Verificar se contém variáveis
                if (strpos($conteudo, '${cabecalho_nome_camara}') !== false) {
                    $this->info("✅ Template contém variáveis de cabeçalho!");
                } else {
                    $this->error("❌ Template NÃO contém variáveis de cabeçalho!");
                }
                
                if (strpos($conteudo, '${rodape_texto}') !== false) {
                    $this->info("✅ Template contém variáveis de rodapé!");
                } else {
                    $this->error("❌ Template NÃO contém variáveis de rodapé!");
                }
                
                if (strpos($conteudo, 'CÂMARA MUNICIPAL DE SÃO PAULO') !== false) {
                    $this->error("❌ Template contém dados hardcoded!");
                } else {
                    $this->info("✅ Template NÃO contém dados hardcoded!");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("Erro: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}