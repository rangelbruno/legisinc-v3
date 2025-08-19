<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class HTMLButtonsFixSeeder extends Seeder
{
    /**
     * Corrige automaticamente a estrutura HTML dos botões no show.blade.php
     * Resolve problema de tags <a> não fechadas que causam capturas incorretas
     */
    public function run(): void
    {
        $this->command->info('🔧 Aplicando correções de estrutura HTML nos botões...');

        try {
            $this->corrigirEstruturaBotoes();
            $this->removerCSSduplico();
            $this->validarCorrecoes();
            
            $this->command->info('✅ Correções de HTML aplicadas com sucesso!');
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao aplicar correções HTML: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Corrige estrutura dos botões com tags não fechadas
     */
    private function corrigirEstruturaBotoes(): void
    {
        $filePath = resource_path('views/proposicoes/show.blade.php');
        
        if (!File::exists($filePath)) {
            $this->command->warn('⚠️ Arquivo show.blade.php não encontrado');
            return;
        }

        $content = File::get($filePath);
        $originalContent = $content;

        // Correções específicas para cada botão problemático
        // Padrão mais robusto que detecta comentários "REMOVIDO" e os substitui por </a>
        $corrections = [
            // 1. Qualquer comentário "<!-- REMOVIDO -->" deve ser substituído por "</a>"
            [
                'pattern' => '/<!--\s*REMOVIDO\s*-->/',
                'replacement' => '</a>',
                'description' => 'Substituir todos os comentários REMOVIDO por </a>'
            ],
            
            // 2. Backup: Botão OnlyOffice Principal (getEditorButtonText) 
            [
                'pattern' => '/(<div class="fw-bold">@\{\{ getEditorButtonText\(\) \}\}<\/div>\s*<small class="text-white-75">Editor OnlyOffice<\/small>\s*<\/div>)\s*(<\/div>)/s',
                'replacement' => '$1
                                </a>
                            $2',
                'description' => 'Botão OnlyOffice Principal (backup)'
            ],

            // 3. Backup: Botão "Revisar Documento" (Legislativo)
            [
                'pattern' => '/(Revisar Documento)\s*(\s*<!--end::Edit Document Button-->)/s',
                'replacement' => '$1
                                </a>
                                $2',
                'description' => 'Botão Revisar Documento (backup)'
            ],

            // 4. Backup: Botão "Assinar Documento"
            [
                'pattern' => '/(<small class="text-muted">Assinatura digital<\/small>\s*<\/div>)\s*(<\/div>\s*<!--end::Sign Document-->)/s',
                'replacement' => '$1
                                </a>
                            $2',
                'description' => 'Botão Assinar Documento (backup)'
            ],

            // 5. Backup: Botão "Visualizar PDF"
            [
                'pattern' => '/(Visualizar PDF)\s*(\s*<\/div>\s*<!--end::View PDF-->)/s',
                'replacement' => '$1
                                </a>
                            $2',
                'description' => 'Botão Visualizar PDF (backup)'
            ],

            // 6. Backup: Botão "Voltar para Lista" (Sidebar)
            [
                'pattern' => '/(@\{\{ getBackButtonText\(\) \}\})\s*(\s*<!--end::Back to List Button-->)/s',
                'replacement' => '$1
                            </a>
                            $2',
                'description' => 'Botão Voltar para Lista (backup)'
            ]
        ];

        $correctionsApplied = 0;

        foreach ($corrections as $correction) {
            $newContent = preg_replace(
                $correction['pattern'], 
                $correction['replacement'], 
                $content, 
                1, 
                $count
            );
            
            if ($count > 0) {
                $content = $newContent;
                $correctionsApplied++;
                $this->command->info("  ✅ {$correction['description']} corrigido");
            } else {
                $this->command->info("  ℹ️ {$correction['description']} já estava correto");
            }
        }

        // Salvar apenas se houve mudanças
        if ($content !== $originalContent) {
            File::put($filePath, $content);
            $this->command->info("📝 {$correctionsApplied} correções aplicadas no arquivo");
        } else {
            $this->command->info("✅ Estrutura HTML já estava correta - nenhuma correção necessária");
        }
    }

    /**
     * Remove CSS duplicado que pode aparecer
     */
    private function removerCSSduplico(): void
    {
        $filePath = resource_path('views/proposicoes/show.blade.php');
        $content = File::get($filePath);

        // Remove CSS duplicado específico
        $patterns = [
            // Remove estilo duplicado
            '/(<style>\s*\n\s*\.d-grid \.btn-assinatura:last-child \{\s*margin-bottom: 0;\s*\}\s*<\/style>)\s*<style>\s*\n\s*\.d-grid \.btn-assinatura:last-child \{\s*margin-bottom: 0;\s*\}\s*<\/style>/s',
            // Remove tags style vazias
            '/<style>\s*<\/style>/',
            // Remove múltiplas linhas em branco
            '/\n{3,}/' => "\n\n"
        ];

        foreach ($patterns as $pattern => $replacement) {
            if (is_numeric($pattern)) {
                $pattern = $replacement;
                $replacement = '';
            }
            $content = preg_replace($pattern, $replacement, $content);
        }

        File::put($filePath, $content);
        $this->command->info('🧹 CSS duplicado removido');
    }

    /**
     * Valida se as correções foram aplicadas corretamente
     */
    private function validarCorrecoes(): void
    {
        $filePath = resource_path('views/proposicoes/show.blade.php');
        $content = File::get($filePath);

        // Contar tags
        $openTags = preg_match_all('/<a\s/', $content);
        $closeTags = preg_match_all('/<\/a>/', $content);

        $this->command->info('🔍 Validando estrutura HTML...');
        $this->command->info("   Links <a> abertos: {$openTags}");
        $this->command->info("   Tags </a> fechadas: {$closeTags}");

        if ($openTags === $closeTags) {
            $this->command->info('✅ Estrutura HTML equilibrada');
        } else {
            $diff = $openTags - $closeTags;
            $this->command->warn("⚠️ Problema: {$diff} tags não fechadas");
        }

        // Validar botões específicos
        $botoes = [
            'getEditorButtonText' => 'Botão OnlyOffice Principal',
            'Revisar Documento' => 'Botão Revisar Documento',  
            'Assinar Documento' => 'Botão Assinar Documento',
            'Visualizar PDF' => 'Botão Visualizar PDF',
            'getBackButtonText' => 'Botão Voltar para Lista'
        ];

        foreach ($botoes as $busca => $nome) {
            // Procura o texto do botão e verifica se há </a> próximo
            if (preg_match("/{$busca}.*?<\/a>/s", $content)) {
                $this->command->info("✅ {$nome}: Tag fechada corretamente");
            } else {
                $this->command->warn("⚠️ {$nome}: Possível problema de estrutura");
            }
        }
    }
}