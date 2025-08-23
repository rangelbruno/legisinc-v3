<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ButtonAssinaturaTagFixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔧 Corrigindo tag de fechamento do botão Assinar Documento...');

        $viewPath = resource_path('views/proposicoes/show.blade.php');
        
        if (!file_exists($viewPath)) {
            $this->command->error("❌ View não encontrada: {$viewPath}");
            return;
        }

        $content = file_get_contents($viewPath);
        $originalContent = $content;
        $changes = 0;

        // 1. Verificar se a tag de fechamento </a> está ausente no botão de assinatura
        // Padrão mais flexível para detectar o problema
        $problemPattern = '/(<a[^>]*href="[^"]*\/assinatura-digital"[^>]*>.*?<small class="text-muted">Assinatura digital com certificado<\/small>\s*<\/div>)\s*<\/div>/s';
        
        if (preg_match($problemPattern, $content, $matches)) {
            // Verificar se não tem </a> antes do </div>
            if (!str_contains($matches[1], '</a>')) {
                $this->command->warn('  ⚠️ Tag de fechamento </a> ausente no botão de assinatura');
                
                // Corrigir adicionando a tag de fechamento </a> antes do último </div>
                $correctedButton = $matches[1] . '</a></div>';
                $content = str_replace($matches[0], $correctedButton, $content);
                $changes++;
                $this->command->info('  ✅ Tag de fechamento </a> adicionada ao botão de assinatura');
            } else {
                $this->command->info('  ✅ Tag de fechamento </a> já está correta no botão de assinatura');
            }
        } else {
            $this->command->warn('  ⚠️ Estrutura do botão de assinatura não encontrada');
            
            // Tentar padrão ainda mais genérico
            $veryGenericPattern = '/(<a[^>]*assinatura-digital[^>]*>.*?Assinar Documento.*?<\/div>)(\s*<\/div>)/s';
            
            if (preg_match($veryGenericPattern, $content, $matches)) {
                if (!str_contains($matches[1], '</a>')) {
                    $correctedButton = $matches[1] . '</a>' . $matches[2];
                    $content = str_replace($matches[0], $correctedButton, $content);
                    $changes++;
                    $this->command->info('  ✅ Tag de fechamento </a> adicionada (padrão genérico)');
                }
            }
        }

        // 2. Verificar se o botão tem a estrutura completa correta
        $expectedStructure = [
            'href="/proposicoes/' => 'Link para assinatura digital',
            'class="btn btn-light-success btn-lg w-100 d-flex align-items-center justify-content-center btn-assinatura-melhorado"' => 'Classes CSS corretas',
            '<i class="ki-duotone ki-signature fs-2 me-3">' => 'Ícone de assinatura',
            '<div class="fw-bold">Assinar Documento</div>' => 'Texto principal',
            '<small class="text-muted">Assinatura digital com certificado</small>' => 'Descrição',
            '</a>' => 'Tag de fechamento'
        ];

        $this->command->info('🔍 Validando estrutura completa do botão...');
        
        foreach ($expectedStructure as $search => $description) {
            if (str_contains($content, $search)) {
                $this->command->info("  ✅ {$description}");
            } else {
                $this->command->error("  ❌ {$description} não encontrado");
            }
        }

        // 3. Salvar apenas se houve mudanças
        if ($changes > 0) {
            file_put_contents($viewPath, $content);
            $this->command->info("✅ {$changes} correções aplicadas ao botão de assinatura");
        } else {
            $this->command->info('✅ Botão de assinatura já está correto');
        }

        // 4. Validação final
        $this->validateFinalStructure($viewPath);
    }

    /**
     * Validação final da estrutura do botão
     */
    private function validateFinalStructure(string $viewPath): void
    {
        $content = file_get_contents($viewPath);
        
        $this->command->info('🔍 Validação final da estrutura...');
        
        // Verificar se o botão está completo e funcional
        $buttonComplete = preg_match('/<a[^>]*href="[^"]*\/assinatura-digital"[^>]*>.*?<\/a>/s', $content);
        
        if ($buttonComplete) {
            $this->command->info('  ✅ Botão de assinatura está completo e funcional');
        } else {
            $this->command->error('  ❌ Botão de assinatura ainda tem problemas estruturais');
        }

        // Verificar se não há tags <a> órfãs
        $openTags = substr_count($content, '<a');
        $closeTags = substr_count($content, '</a>');
        
        if ($openTags === $closeTags) {
            $this->command->info('  ✅ Número de tags <a> e </a> está balanceado');
        } else {
            $this->command->warn("  ⚠️ Desbalanceamento de tags: {$openTags} <a> vs {$closeTags} </a>");
        }

        // Verificar se o botão aparece apenas quando necessário (v-if="canSign()")
        $canSignCondition = str_contains($content, 'v-if="canSign()"');
        if ($canSignCondition) {
            $this->command->info('  ✅ Condição v-if="canSign()" está presente');
        } else {
            $this->command->warn('  ⚠️ Condição v-if="canSign()" não encontrada');
        }
    }
}
