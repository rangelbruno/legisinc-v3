<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class UIButtonsFixSeeder extends Seeder
{
    /**
     * Corrige problemas de tags HTML não fechadas em botões OnlyOffice
     * Previne botões capturando cliques de outros elementos
     */
    public function run(): void
    {
        $this->command->info('🔧 Aplicando correções de UI para botões OnlyOffice...');
        
        $viewPath = resource_path('views/proposicoes/show.blade.php');
        
        if (!File::exists($viewPath)) {
            $this->command->error("Arquivo não encontrado: {$viewPath}");
            return;
        }
        
        $content = File::get($viewPath);
        $originalContent = $content;
        
        // Array de correções para aplicar
        $corrections = [
            // 1. Corrigir "Adicionar Conteúdo" sem tag fechada
            [
                'pattern' => '/<a href="{{ route\(\'proposicoes\.editar-onlyoffice\'[^>]+>\s*<i[^>]+><\/i>Adicionar Conteúdo\s*\n\s*\n\s*\n/',
                'replacement' => '<a href="{{ route(\'proposicoes.editar-onlyoffice\', [\'proposicao\' => $proposicao->id, \'template\' => $proposicao->template_id]) }}" class="btn btn-primary">
                                        <i class="fas fa-file-word me-2"></i>Adicionar Conteúdo
                                    </a>'
            ],
            
            // 2. Corrigir "Adicionar Conteúdo no OnlyOffice"
            [
                'pattern' => '/<a href="{{ route\(\'proposicoes\.onlyoffice\.editor-parlamentar\'[^>]+>\s*<i[^>]+><\/i>Adicionar Conteúdo no OnlyOffice\s*\n\s*\n/',
                'replacement' => '<a href="{{ route(\'proposicoes.onlyoffice.editor-parlamentar\', $proposicao->id) }}" class="btn btn-primary btn-lg btn-onlyoffice">
                                        <i class="fas fa-file-word me-2"></i>Adicionar Conteúdo no OnlyOffice
                                    </a>'
            ],
            
            // 3. Corrigir "Editar Proposição"
            [
                'pattern' => '/<a href="{{ route\(\'proposicoes\.editar-onlyoffice\'[^>]+>\s*<i[^>]+><\/i>Editar Proposição\s*\n\s*\n\s*\n/',
                'replacement' => '<a href="{{ route(\'proposicoes.editar-onlyoffice\', [\'proposicao\' => $proposicao->id, \'template\' => $proposicao->template_id]) }}" class="btn btn-primary">
                                    <i class="fas fa-file-word me-2"></i>Editar Proposição
                                </a>'
            ],
            
            // 4. Corrigir "Continuar Editando no OnlyOffice"
            [
                'pattern' => '/<a href="{{ route\(\'proposicoes\.onlyoffice\.editor-parlamentar\'[^>]+>\s*<i[^>]+><\/i>Continuar Editando no OnlyOffice\s*\n\n\n/',
                'replacement' => '<a href="{{ route(\'proposicoes.onlyoffice.editor-parlamentar\', $proposicao->id) }}" class="btn btn-outline-primary btn-lg btn-onlyoffice">
                                <i class="fas fa-file-word me-2"></i>Continuar Editando no OnlyOffice
                            </a>'
            ],
            
            // 5. Corrigir "Fazer Novas Edições no OnlyOffice"
            [
                'pattern' => '/<a href="{{ route\(\'proposicoes\.onlyoffice\.editor-parlamentar\'[^>]+>\s*<i[^>]+><\/i>Fazer Novas Edições no OnlyOffice<\/a>/',
                'replacement' => '<a href="{{ route(\'proposicoes.onlyoffice.editor-parlamentar\', $proposicao->id) }}" class="btn btn-outline-warning btn-lg btn-onlyoffice">
                                <i class="fas fa-file-word me-2"></i>Fazer Novas Edições no OnlyOffice
                            </a>'
            ],
            
            // 6. Corrigir "Continuar Edição no OnlyOffice"
            [
                'pattern' => '/<a href="{{ route\(\'proposicoes\.onlyoffice\.editor-parlamentar\'[^>]+>\s*<i[^>]+><\/i>Continuar Edição no OnlyOffice\s*\n\n\n/',
                'replacement' => '<a href="{{ route(\'proposicoes.onlyoffice.editor-parlamentar\', $proposicao->id) }}" class="btn btn-primary btn-lg btn-onlyoffice">
                                <i class="fas fa-file-word me-2"></i>Continuar Edição no OnlyOffice
                            </a>'
            ],
            
            // 7. Corrigir "Análise Técnica" sem fechamento
            [
                'pattern' => '/<a href="{{ route\(\'proposicoes\.revisar\.show\'[^>]+>\s*<i[^>]+><\/i>Análise Técnica\s*\n\s*\n/',
                'replacement' => '<a href="{{ route(\'proposicoes.revisar.show\', $proposicao->id) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-clipboard-check me-2"></i>Análise Técnica
                                </a>'
            ],
            
            // 8. Corrigir "Visualizar PDF" mal fechado
            [
                'pattern' => '/<a href="{{ route\(\'proposicoes\.serve-pdf\'[^>]+>\s*<i[^>]+><\/i>Visualizar PDF<\/a>/',
                'replacement' => '<a href="{{ route(\'proposicoes.serve-pdf\', $proposicao) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-file-pdf me-2"></i>Visualizar PDF
                            </a>'
            ],
            
            // 9. Remover tags </a> duplicadas
            [
                'pattern' => '/<\/a>\s*<\/a>/',
                'replacement' => '</a>'
            ],
            
            // 10. Remover tag </a> dentro de button
            [
                'pattern' => '/(<button[^>]*>.*?)<\/a>(<\/button>)/s',
                'replacement' => '$1$2'
            ],
            
            // 11. Remover tag </a> incorreta em JavaScript
            [
                'pattern' => '/\{\{ \$proposicao->id \}\};<\/a>/',
                'replacement' => '{{ $proposicao->id }};'
            ],
            
            // 12. Corrigir botão "Protocolar" mal fechado
            [
                'pattern' => '/<a href="{{ route\(\'proposicoes\.protocolar\.show\'[^>]+>\s*<i[^>]+><\/i>Protocolar<\/a>/',
                'replacement' => '<a href="{{ route(\'proposicoes.protocolar.show\', $proposicao) }}" class="btn btn-primary">
                                        <i class="fas fa-file-signature me-2"></i>Protocolar
                                    </a>'
            ]
        ];
        
        $appliedCorrections = 0;
        
        // Aplicar cada correção
        foreach ($corrections as $index => $correction) {
            $count = 0;
            $content = preg_replace($correction['pattern'], $correction['replacement'], $content, -1, $count);
            
            if ($count > 0) {
                $appliedCorrections += $count;
                $this->command->info("  ✅ Correção " . ($index + 1) . " aplicada: {$count} ocorrência(s)");
            }
        }
        
        // Verificação adicional: garantir que todas as tags <a> estejam fechadas corretamente
        // Desabilitado temporariamente pois estava criando tags extras
        // $content = $this->ensureAllTagsClosed($content);
        
        // Salvar arquivo apenas se houve mudanças
        if ($content !== $originalContent) {
            File::put($viewPath, $content);
            $this->command->info("✅ Arquivo atualizado com {$appliedCorrections} correções aplicadas");
            
            // Validar estrutura final
            $this->validateHTMLStructure($content);
        } else {
            $this->command->info("✅ Estrutura HTML já está correta - nenhuma correção necessária");
        }
        
        // Log das correções aplicadas
        Log::info('UIButtonsFixSeeder executado', [
            'corrections_applied' => $appliedCorrections,
            'file' => $viewPath
        ]);
    }
    
    /**
     * Garante que todas as tags <a> estejam fechadas corretamente
     */
    private function ensureAllTagsClosed(string $content): string
    {
        // Padrões específicos para garantir fechamento correto
        $patterns = [
            // Botões OnlyOffice que terminam sem </a> apropriado
            '/(class="[^"]*btn-onlyoffice[^"]*">[\s\S]*?)(<i[^>]*>[\s\S]*?<\/i>[^<]*)\s*\n\s*\n\s*(?!<\/a>)/' => '$1$2</a>',
            
            // Links de botões que não têm </a> na linha seguinte adequada
            '/(<a[^>]+class="btn[^"]*"[^>]*>[\s\S]*?)(<\/i>[^<\n]*)\s*\n\s*(?!<\/a>)/' => '$1$2</a>',
        ];
        
        foreach ($patterns as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }
        
        return $content;
    }
    
    /**
     * Valida a estrutura HTML após as correções
     */
    private function validateHTMLStructure(string $content): void
    {
        // Contar tags abertas e fechadas
        preg_match_all('/<a\s+href=/', $content, $openTags);
        preg_match_all('/<\/a>/', $content, $closeTags);
        
        $openCount = count($openTags[0]);
        $closeCount = count($closeTags[0]);
        
        if ($openCount === $closeCount) {
            $this->command->info("✅ Validação: Estrutura HTML equilibrada ({$openCount} tags <a> abertas e fechadas)");
        } else {
            $diff = $openCount - $closeCount;
            $this->command->warn("⚠️ Aviso: Estrutura HTML pode ter {$diff} tag(s) não fechada(s)");
            $this->command->warn("   Tags <a> abertas: {$openCount}");
            $this->command->warn("   Tags </a> fechadas: {$closeCount}");
        }
        
        // Verificar botões críticos
        $criticalButtons = [
            'Continuar Edição no OnlyOffice',
            'Adicionar Conteúdo no OnlyOffice',
            'Editar Proposição no OnlyOffice',
            'Continuar Editando no OnlyOffice',
            'Fazer Novas Edições no OnlyOffice',
            'Assinar Documento'
        ];
        
        $this->command->info("\n🔍 Verificando botões críticos:");
        foreach ($criticalButtons as $button) {
            if (strpos($content, $button) !== false) {
                // Verificar se o botão tem tag fechada próxima
                $pattern = '/' . preg_quote($button, '/') . '.*?<\/a>/s';
                if (preg_match($pattern, $content)) {
                    $this->command->info("  ✅ {$button}: Estrutura correta");
                } else {
                    $this->command->warn("  ⚠️ {$button}: Possível problema de estrutura");
                }
            }
        }
    }
}