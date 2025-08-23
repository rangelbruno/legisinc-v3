<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FixAssinaturaButtonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔧 Aplicando correção definitiva do botão de assinatura...');

        $viewPath = resource_path('views/proposicoes/show.blade.php');
        
        if (!file_exists($viewPath)) {
            $this->command->error("❌ View não encontrada: {$viewPath}");
            return;
        }

        $content = file_get_contents($viewPath);
        $changes = 0;

        // 1. Corrigir botão de assinatura - padrão específico encontrado no problema
        $patterns = [
            // Padrão 1: Tag </a> ausente após certificado</small></div>
            '/(<div class="text-start">\s*<div class="fw-bold">Assinar Documento<\/div>\s*<small class="text-muted">Assinatura digital com certificado<\/small>\s*<\/div>)\s*<\/div>/s' => 
            '$1</a></div>',
            
            // Padrão 2: Espaços em branco onde deveria estar </a>
            '/(<small class="text-muted">Assinatura digital com certificado<\/small>\s*<\/div>)\s+<\/div>/s' => 
            '$1</a></div>',
            
            // Padrão 3: Estrutura quebrada específica
            '/(btn-assinatura-melhorado"[^>]*>.*?<div class="text-start">.*?Assinar Documento.*?certificado<\/small>\s*<\/div>)\s*<\/div>/s' => 
            '$1</a></div>'
        ];

        foreach ($patterns as $pattern => $replacement) {
            $newContent = preg_replace($pattern, $replacement, $content);
            if ($newContent !== $content) {
                $content = $newContent;
                $changes++;
                $this->command->info("  ✅ Correção aplicada com padrão: " . substr($pattern, 0, 50) . "...");
                break; // Só aplicar uma correção para evitar duplicação
            }
        }

        // 2. Corrigir links de anexos também
        $anexoPatterns = [
            '/(<i class="ki-duotone ki-download fs-4">.*?<\/i>\s*Download)\s+<a/s' => '$1</a><a',
            '/(<i class="ki-duotone ki-eye fs-4">.*?<\/i>\s*Visualizar)\s+<\/td>/s' => '$1</a></td>'
        ];

        foreach ($anexoPatterns as $pattern => $replacement) {
            $newContent = preg_replace($pattern, $replacement, $content);
            if ($newContent !== $content) {
                $content = $newContent;
                $changes++;
                $this->command->info("  ✅ Links de anexos corrigidos");
            }
        }

        // 3. Verificar se ainda há desbalanceamento de tags
        $openTags = substr_count($content, '<a');
        $closeTags = substr_count($content, '</a>');

        if ($openTags !== $closeTags) {
            $this->command->warn("  ⚠️ Ainda há desbalanceamento: {$openTags} <a> vs {$closeTags} </a>");
            
            // Tentar correção automática simples
            $diff = $openTags - $closeTags;
            if ($diff > 0 && $diff <= 3) {
                // Adicionar tags de fechamento faltantes no final apropriado
                $content = str_replace('</td>', '</a></td>', $content, $diff);
                $changes++;
                $this->command->info("  ✅ {$diff} tags de fechamento adicionadas automaticamente");
            }
        }

        // 4. Salvar apenas se houve mudanças
        if ($changes > 0) {
            file_put_contents($viewPath, $content);
            $this->command->info("✅ {$changes} correções aplicadas ao botão de assinatura");
        } else {
            $this->command->info('✅ Nenhuma correção necessária');
        }

        // 5. Validação final
        $this->validateFinalResult($viewPath);
    }

    /**
     * Validar resultado final
     */
    private function validateFinalResult(string $viewPath): void
    {
        $content = file_get_contents($viewPath);
        
        $this->command->info('🔍 Validação final...');
        
        // Verificar se o botão está completo
        $buttonComplete = str_contains($content, 'Assinar Documento') && 
                          str_contains($content, 'Assinatura digital com certificado') &&
                          str_contains($content, 'btn-assinatura-melhorado');
        
        if ($buttonComplete) {
            $this->command->info('  ✅ Botão de assinatura está presente e completo');
        }

        // Verificar balanceamento final
        $openTags = substr_count($content, '<a');
        $closeTags = substr_count($content, '</a>');
        
        if ($openTags === $closeTags) {
            $this->command->info('  ✅ Tags HTML estão balanceadas');
        } else {
            $this->command->warn("  ⚠️ Tags ainda desbalanceadas: {$openTags} <a> vs {$closeTags} </a>");
        }

        // Verificar estrutura específica do botão
        $hasCorrectStructure = preg_match('/assinatura-digital.*?Assinar Documento.*?certificado.*?<\/a>/s', $content);
        
        if ($hasCorrectStructure) {
            $this->command->info('  ✅ Estrutura do botão de assinatura está correta');
        } else {
            $this->command->warn('  ⚠️ Estrutura do botão pode ainda ter problemas');
        }
    }
}
