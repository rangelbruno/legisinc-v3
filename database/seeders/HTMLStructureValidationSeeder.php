<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HTMLStructureValidationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔍 Validando e corrigindo estrutura HTML do botão de assinatura...');

        $viewPath = resource_path('views/proposicoes/show.blade.php');
        
        if (!file_exists($viewPath)) {
            $this->command->error("❌ View não encontrada: {$viewPath}");
            return;
        }

        $content = file_get_contents($viewPath);
        $originalContent = $content;
        $changes = 0;

        // 1. Verificar e corrigir estrutura do botão de assinatura
        $changes += $this->fixAssinaturaButton($content);

        // 2. Verificar e corrigir estrutura do botão PDF
        $changes += $this->fixPDFButton($content);

        // 3. Verificar e corrigir estrutura do botão de exclusão
        $changes += $this->fixDeleteButton($content);

        // 4. Verificar e corrigir estrutura do botão de atualização
        $changes += $this->fixRefreshButton($content);

        // 5. Verificar e corrigir estrutura do botão de voltar
        $changes += $this->fixBackButton($content);

        // 6. Salvar apenas se houve mudanças
        if ($changes > 0) {
            file_put_contents($viewPath, $content);
            $this->command->info("✅ {$changes} correções estruturais aplicadas");
        } else {
            $this->command->info('✅ Estrutura HTML já está correta');
        }

        // 7. Validação final
        $this->validateFinalStructure($viewPath);
    }

    /**
     * Corrigir estrutura do botão de assinatura
     */
    private function fixAssinaturaButton(string &$content): int
    {
        $changes = 0;
        
        // Padrão para encontrar o botão de assinatura
        $pattern = '/(<a[^>]*href="[^"]*\/assinatura-digital"[^>]*>)(.*?)(<\/div>)/s';
        
        if (preg_match($pattern, $content, $matches)) {
            $openTag = $matches[1];
            $buttonContent = $matches[2];
            $closeDiv = $matches[3];
            
            // Verificar se a tag de fechamento </a> está presente
            if (!str_contains($buttonContent, '</a>')) {
                // Adicionar tag de fechamento antes do </div>
                $correctedButton = $openTag . $buttonContent . '</a>' . $closeDiv;
                $content = str_replace($matches[0], $correctedButton, $content);
                $changes++;
                $this->command->info('  ✅ Tag de fechamento </a> adicionada ao botão de assinatura');
            }
        }
        
        return $changes;
    }

    /**
     * Corrigir estrutura do botão PDF
     */
    private function fixPDFButton(string &$content): int
    {
        $changes = 0;
        
        // Padrão para encontrar o botão PDF
        $pattern = '/(<a[^>]*href="[^"]*\/pdf"[^>]*>)(.*?)(<\/a>)/s';
        
        if (preg_match($pattern, $content, $matches)) {
            $openTag = $matches[1];
            $buttonContent = $matches[2];
            $closeTag = $matches[3];
            
            // Verificar se o botão tem target="_blank"
            if (!str_contains($openTag, 'target="_blank"')) {
                $correctedOpenTag = str_replace('>', ' target="_blank">', $openTag);
                $content = str_replace($openTag, $correctedOpenTag, $content);
                $changes++;
                $this->command->info('  ✅ target="_blank" adicionado ao botão PDF');
            }
        }
        
        return $changes;
    }

    /**
     * Corrigir estrutura do botão de exclusão
     */
    private function fixDeleteButton(string &$content): int
    {
        $changes = 0;
        
        // Padrão para encontrar o botão de exclusão
        $pattern = '/(<button[^>]*@click="confirmarExclusaoDocumento"[^>]*>)(.*?)(<\/button>)/s';
        
        if (preg_match($pattern, $content, $matches)) {
            $openTag = $matches[1];
            $buttonContent = $matches[2];
            $closeTag = $matches[3];
            
            // Verificar se o botão tem type="button"
            if (!str_contains($openTag, 'type="button"')) {
                $correctedOpenTag = str_replace('>', ' type="button">', $openTag);
                $content = str_replace($openTag, $correctedOpenTag, $content);
                $changes++;
                $this->command->info('  ✅ type="button" adicionado ao botão de exclusão');
            }
        }
        
        return $changes;
    }

    /**
     * Corrigir estrutura do botão de atualização
     */
    private function fixRefreshButton(string &$content): int
    {
        $changes = 0;
        
        // Padrão para encontrar o botão de atualização
        $pattern = '/(<button[^>]*@click="forceRefresh"[^>]*>)(.*?)(<\/button>)/s';
        
        if (preg_match($pattern, $content, $matches)) {
            $openTag = $matches[1];
            $buttonContent = $matches[2];
            $closeTag = $matches[3];
            
            // Verificar se o botão tem type="button"
            if (!str_contains($openTag, 'type="button"')) {
                $correctedOpenTag = str_replace('>', ' type="button">', $openTag);
                $content = str_replace($openTag, $correctedOpenTag, $content);
                $changes++;
                $this->command->info('  ✅ type="button" adicionado ao botão de atualização');
            }
        }
        
        return $changes;
    }

    /**
     * Corrigir estrutura do botão de voltar
     */
    private function fixBackButton(string &$content): int
    {
        $changes = 0;
        
        // Padrão para encontrar o botão de voltar
        $pattern = '/(<a[^>]*:href="getBackUrl\(\)"[^>]*>)(.*?)(<\/a>)/s';
        
        if (preg_match($pattern, $content, $matches)) {
            $openTag = $matches[1];
            $buttonContent = $matches[2];
            $closeTag = $matches[3];
            
            // Verificar se o link tem classes CSS apropriadas
            if (!str_contains($openTag, 'class="btn btn-secondary w-100"')) {
                $correctedOpenTag = str_replace('class="', 'class="btn btn-secondary w-100 ', $openTag);
                $content = str_replace($openTag, $correctedOpenTag, $content);
                $changes++;
                $this->command->info('  ✅ Classes CSS adicionadas ao botão de voltar');
            }
        }
        
        return $changes;
    }

    /**
     * Validação final da estrutura HTML
     */
    private function validateFinalStructure(string $viewPath): void
    {
        $content = file_get_contents($viewPath);
        
        $this->command->info('🔍 Validação final da estrutura HTML...');
        
        // Verificar balanceamento de tags
        $openTags = substr_count($content, '<a');
        $closeTags = substr_count($content, '</a>');
        $openButtons = substr_count($content, '<button');
        $closeButtons = substr_count($content, '</button>');
        
        if ($openTags === $closeTags) {
            $this->command->info('  ✅ Tags <a> e </a> estão balanceadas');
        } else {
            $this->command->warn("  ⚠️ Desbalanceamento de tags <a>: {$openTags} vs {$closeTags}");
        }
        
        if ($openButtons === $closeButtons) {
            $this->command->info('  ✅ Tags <button> e </button> estão balanceadas');
        } else {
            $this->command->warn("  ⚠️ Desbalanceamento de tags <button>: {$openButtons} vs {$closeButtons}");
        }

        // Verificar se todos os botões têm estrutura válida
        $buttonValidations = [
            'assinatura-digital' => 'Botão de assinatura',
            'pdf' => 'Botão PDF',
            'confirmarExclusaoDocumento' => 'Botão de exclusão',
            'forceRefresh' => 'Botão de atualização',
            'getBackUrl()' => 'Botão de voltar'
        ];

        foreach ($buttonValidations as $identifier => $description) {
            if (str_contains($content, $identifier)) {
                $this->command->info("  ✅ {$description} encontrado");
            } else {
                $this->command->warn("  ⚠️ {$description} não encontrado");
            }
        }
    }
}
