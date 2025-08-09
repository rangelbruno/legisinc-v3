<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\TipoProposicaoTemplate;

class MigrarTemplatesParaBanco extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'templates:migrar-para-banco 
                           {--force : Forçar migração mesmo se já tiver conteúdo}
                           {--template= : Migrar apenas um template específico por ID}';

    /**
     * The console command description.
     */
    protected $description = 'Migrar templates de arquivos para o banco de dados PostgreSQL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Iniciando migração de templates para o banco de dados...');
        
        // Obter templates para migrar
        $query = TipoProposicaoTemplate::query();
        
        if ($templateId = $this->option('template')) {
            $query->where('id', $templateId);
        }
        
        if (!$this->option('force')) {
            $query->whereNull('conteudo');
        }
        
        $templates = $query->get();
        
        if ($templates->isEmpty()) {
            $this->warn('⚠️  Nenhum template encontrado para migração.');
            return Command::SUCCESS;
        }
        
        $this->info("📁 Encontrados {$templates->count()} templates para migrar");
        
        $sucesso = 0;
        $falhas = 0;
        
        foreach ($templates as $template) {
            $tipoNome = $template->tipoProposicao ? $template->tipoProposicao->nome : 'Sem tipo';
            $this->line("🔧 Migrando template ID: {$template->id} ({$tipoNome})");
            
            try {
                if ($this->migrarTemplate($template)) {
                    $sucesso++;
                    $this->info("  ✅ Template {$template->id} migrado com sucesso");
                } else {
                    $falhas++;
                    $this->error("  ❌ Falha ao migrar template {$template->id}");
                }
            } catch (\Exception $e) {
                $falhas++;
                $this->error("  ❌ Erro ao migrar template {$template->id}: " . $e->getMessage());
            }
        }
        
        $this->newLine();
        $this->info("📊 Resultado da migração:");
        $this->info("  ✅ Sucessos: {$sucesso}");
        $this->info("  ❌ Falhas: {$falhas}");
        
        return Command::SUCCESS;
    }
    
    /**
     * Migrar um template específico
     */
    private function migrarTemplate(TipoProposicaoTemplate $template): bool
    {
        // Verificar se já tem conteúdo e não é força
        if (!empty($template->conteudo) && !$this->option('force')) {
            $this->line("  ℹ️ Template já tem conteúdo, pulando...");
            return true;
        }
        
        // Verificar se tem arquivo
        if (!$template->arquivo_path) {
            $this->line("  ⚠️ Template sem arquivo_path");
            return false;
        }
        
        // Verificar se arquivo existe
        if (!Storage::exists($template->arquivo_path)) {
            $this->line("  ⚠️ Arquivo não encontrado: {$template->arquivo_path}");
            return false;
        }
        
        // Ler conteúdo do arquivo
        $conteudo = Storage::get($template->arquivo_path);
        
        if (empty($conteudo)) {
            $this->line("  ⚠️ Arquivo vazio: {$template->arquivo_path}");
            return false;
        }
        
        // Detectar formato
        $formato = $this->detectarFormato($template->arquivo_path, $conteudo);
        
        // Atualizar template no banco
        $updated = $template->update([
            'conteudo' => $conteudo,
            'formato' => $formato
        ]);
        
        if ($updated) {
            $this->line("    📄 Conteúdo: " . strlen($conteudo) . " bytes");
            $this->line("    🏷️ Formato: {$formato}");
        }
        
        return $updated;
    }
    
    /**
     * Detectar formato do arquivo
     */
    private function detectarFormato(string $path, string $conteudo): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        // Baseado na extensão
        if ($extension === 'docx') {
            return 'docx';
        }
        
        if ($extension === 'html' || $extension === 'htm') {
            return 'html';
        }
        
        // Baseado no conteúdo
        if (str_starts_with($conteudo, '{\rtf')) {
            return 'rtf';
        }
        
        if (str_starts_with($conteudo, 'PK')) {
            return 'docx'; // ZIP-based format
        }
        
        if (str_contains($conteudo, '<html') || str_contains($conteudo, '<HTML')) {
            return 'html';
        }
        
        // Padrão
        return 'rtf';
    }
}
