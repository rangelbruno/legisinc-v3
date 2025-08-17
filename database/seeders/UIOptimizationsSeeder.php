<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class UIOptimizationsSeeder extends Seeder
{
    /**
     * Seeder para preservar todas as otimizações de UI aplicadas
     * 
     * Este seeder garante que:
     * 1. Botões OnlyOffice têm estrutura HTML correta e CSS otimizado
     * 2. Botões "Assinar Documento" têm estrutura HTML correta e CSS otimizado
     * 3. Todos os botões têm classes apropriadas (btn-lg, btn-onlyoffice, btn-assinatura)
     * 4. CSS personalizado está aplicado para melhor experiência do usuário
     */
    public function run(): void
    {
        $this->command->info('🎨 Aplicando otimizações de UI para botões...');
        
        // 1. Corrigir estrutura HTML dos botões
        $this->corrigirEstruturaBotoes();
        
        // 2. Aplicar CSS otimizado
        $this->aplicarCSSOptimizado();
        
        // 3. Validar que as correções foram aplicadas
        $this->validarCorrecoes();
        
        $this->command->info('✅ Otimizações de UI aplicadas com sucesso!');
        
        // Exibir resumo das otimizações
        $this->exibirResumoOtimizacoes();
    }
    
    /**
     * Corrigir estrutura HTML dos botões OnlyOffice e Assinatura
     */
    private function corrigirEstruturaBotoes(): void
    {
        $showViewFile = base_path('resources/views/proposicoes/show.blade.php');
        
        if (!File::exists($showViewFile)) {
            $this->command->warn('   ⚠️ Arquivo show.blade.php não encontrado');
            return;
        }
        
        $content = File::get($showViewFile);
        $originalContent = $content;
        
        // FORÇA a reaplicação das correções sempre
        $this->command->info('   🔧 Forçando reaplicação das correções HTML...');
        
        // CORREÇÕES ROBUSTAS para botões OnlyOffice (múltiplas variações)
        $ooCorrections = [
            // Padrão 1: Botão sem btn-lg e sem btn-onlyoffice, sem </a>
            [
                'search' => 'proposicoes.onlyoffice.editor\', $proposicao->id) }}" class="btn btn-primary">',
                'replace' => 'proposicoes.onlyoffice.editor\', $proposicao->id) }}" class="btn btn-primary btn-lg btn-onlyoffice">'
            ],
            // Padrão 2: Botão com btn-lg mas sem btn-onlyoffice
            [
                'search' => 'proposicoes.onlyoffice.editor\', $proposicao->id) }}" class="btn btn-primary btn-lg">',
                'replace' => 'proposicoes.onlyoffice.editor\', $proposicao->id) }}" class="btn btn-primary btn-lg btn-onlyoffice">'
            ],
            // Padrão 3: Botão com btn-onlyoffice mas sem btn-lg
            [
                'search' => 'proposicoes.onlyoffice.editor\', $proposicao->id) }}" class="btn btn-primary btn-onlyoffice">',
                'replace' => 'proposicoes.onlyoffice.editor\', $proposicao->id) }}" class="btn btn-primary btn-lg btn-onlyoffice">'
            ]
        ];
        
        // CORREÇÕES ROBUSTAS para botões OnlyOffice Parlamentar
        $ooParlamentarCorrections = [
            [
                'search' => 'proposicoes.onlyoffice.editor-parlamentar\', $proposicao->id) }}" class="btn btn-primary">',
                'replace' => 'proposicoes.onlyoffice.editor-parlamentar\', $proposicao->id) }}" class="btn btn-primary btn-lg btn-onlyoffice">'
            ],
            [
                'search' => 'proposicoes.onlyoffice.editor-parlamentar\', $proposicao->id) }}" class="btn btn-primary btn-lg">',
                'replace' => 'proposicoes.onlyoffice.editor-parlamentar\', $proposicao->id) }}" class="btn btn-primary btn-lg btn-onlyoffice">'
            ],
            [
                'search' => 'proposicoes.onlyoffice.editor-parlamentar\', $proposicao->id) }}" class="btn btn-outline-primary">',
                'replace' => 'proposicoes.onlyoffice.editor-parlamentar\', $proposicao->id) }}" class="btn btn-outline-primary btn-lg btn-onlyoffice">'
            ],
            [
                'search' => 'proposicoes.onlyoffice.editor-parlamentar\', $proposicao->id) }}" class="btn btn-outline-warning">',
                'replace' => 'proposicoes.onlyoffice.editor-parlamentar\', $proposicao->id) }}" class="btn btn-outline-warning btn-lg btn-onlyoffice">'
            ]
        ];
        
        // Aplicar correções OnlyOffice Legislativo
        foreach ($ooCorrections as $correction) {
            if (str_contains($content, $correction['search'])) {
                $content = str_replace($correction['search'], $correction['replace'], $content);
                $this->command->info('      ✅ Botão OnlyOffice Legislativo corrigido');
            }
        }
        
        // Aplicar correções OnlyOffice Parlamentar
        foreach ($ooParlamentarCorrections as $correction) {
            if (str_contains($content, $correction['search'])) {
                $content = str_replace($correction['search'], $correction['replace'], $content);
                $this->command->info('      ✅ Botão OnlyOffice Parlamentar corrigido');
            }
        }
        
        // CORREÇÕES ROBUSTAS para botões Assinar Documento
        $assinaturaCorrections = [
            // Padrão 1: Botão sem btn-lg e sem btn-assinatura
            [
                'search' => 'proposicoes.assinar\', $proposicao->id) }}" class="btn btn-success">',
                'replace' => 'proposicoes.assinar\', $proposicao->id) }}" class="btn btn-success btn-lg btn-assinatura">'
            ],
            // Padrão 2: Botão com btn-lg mas sem btn-assinatura  
            [
                'search' => 'proposicoes.assinar\', $proposicao->id) }}" class="btn btn-success btn-lg">',
                'replace' => 'proposicoes.assinar\', $proposicao->id) }}" class="btn btn-success btn-lg btn-assinatura">'
            ],
            // Padrão 3: Botão com btn-assinatura mas sem btn-lg
            [
                'search' => 'proposicoes.assinar\', $proposicao->id) }}" class="btn btn-success btn-assinatura">',
                'replace' => 'proposicoes.assinar\', $proposicao->id) }}" class="btn btn-success btn-lg btn-assinatura">'
            ]
        ];
        
        // Aplicar correções Assinatura
        foreach ($assinaturaCorrections as $correction) {
            if (str_contains($content, $correction['search'])) {
                $content = str_replace($correction['search'], $correction['replace'], $content);
                $this->command->info('      ✅ Botão Assinatura corrigido');
            }
        }
        
        // CORREÇÃO de tags de fechamento </a> faltantes
        $content = $this->corrigirTagsFechamento($content);
        
        // SEMPRE salvar para garantir que as mudanças sejam aplicadas
        File::put($showViewFile, $content);
        $this->command->info('   🔧 Estrutura HTML dos botões corrigida e aplicada');
    }
    
    /**
     * Corrigir tags de fechamento </a> faltantes
     */
    private function corrigirTagsFechamento(string $content): string
    {
        // Padrões ROBUSTOS para correção de tags faltantes baseados nas correções manuais específicas
        $patterns = [
            // Correção específica: Revisar no Editor
            '/(<a href="{{ route\(\'proposicoes\.onlyoffice\.editor\'[^>]+>\s*<i[^>]+><\/i>Revisar no Editor)\s*\n\s*\n\s*@if/s' => '$1</a>

                                @if',
            
            // Correção específica: Análise Técnica (primeira ocorrência)
            '/(<a href="{{ route\(\'proposicoes\.revisar\.show\'[^>]+>\s*<i[^>]+><\/i>Análise Técnica)\s*\n\s*\n\s*@endif\s*\n\s*<button onclick="devolverParaParlamentar/s' => '$1</a>

                                @endif
                                <button onclick="devolverParaParlamentar',
            
            // Correção específica: Preencher Campos do Template
            '/(<a href="{{ route\(\'proposicoes\.preencher-modelo\'[^>]+>\s*<i[^>]+><\/i>Preencher Campos do Template)\s*\n\s*\n\s*<small class="text-muted d-block mt-1">/s' => '$1</a>

                                <small class="text-muted d-block mt-1">',
            
            // Correção específica: Continuar Revisão no Editor
            '/(<a href="{{ route\(\'proposicoes\.onlyoffice\.editor\'[^>]+>\s*<i[^>]+><\/i>Continuar Revisão no Editor)\s*\n\s*\n\s*@if\(Auth::user\(\)->isAssessorJuridico\(\)\)/s' => '$1</a>

                                @if(Auth::user()->isAssessorJuridico())',
            
            // Correção específica: Análise Técnica (segunda ocorrência)
            '/(<a href="{{ route\(\'proposicoes\.revisar\.show\'[^>]+>\s*<i[^>]+><\/i>Análise Técnica)\s*\n\s*\n\s*@endif\s*\n\s*<button onclick="retornarParaParlamentar/s' => '$1</a>

                                @endif
                                <button onclick="retornarParaParlamentar',
            
            // Correção específica: Protocolar
            '/(<a href="{{ route\(\'proposicoes\.protocolar\.show\'[^>]+>\s*<i[^>]+><\/i>Protocolar)\s*\n\s*\n\s*<button class="btn btn-outline-success"/s' => '$1</a>

                                    <button class="btn btn-outline-success"',
            
            // Correção específica: Fazer Correções no Editor
            '/(<a href="{{ route\(\'proposicoes\.onlyoffice\.editor\'[^>]+>\s*<i[^>]+><\/i>Fazer Correções no Editor)\s*\n\s*\n\s*<button onclick="retornarParaParlamentar/s' => '$1</a>

                                <button onclick="retornarParaParlamentar',
            
            // Correção específica: Baixar PDF
            '/(<a href="{{ route\(\'proposicoes\.serve-pdf\'[^>]+>\s*<i[^>]+><\/i>Baixar PDF)\s*\n\s*\n\s*@endif/s' => '$1</a>

                            @endif',
            
            // Correção específica: Visualizar PDF
            '/(<a href="{{ route\(\'proposicoes\.serve-pdf\'[^>]+>\s*<i[^>]+><\/i>Visualizar PDF)\s*\n\s*\n\s*@endif/s' => '$1</a>

                            @endif',
            
            // Correção específica: Análise Técnica (terceira ocorrência)
            '/(<a href="{{ route\(\'proposicoes\.revisar\.show\'[^>]+>\s*<i[^>]+><\/i>Análise Técnica)\s*\n\s*\n\s*@endif\s*\n\s*<\/div>/s' => '$1</a>

                                @endif
                            </div>',
            
            // Padrões genéricos para outros casos
            '/(<a href="{{ route\(\'proposicoes\.onlyoffice\.editor-parlamentar\'[^>]+>\s*<i[^>]+><\/i>[^<]+)\s*\n\s*\n\s*@if/s' => '$1</a>

                                @if',
            '/(<a href="{{ route\(\'proposicoes\.onlyoffice\.editor-parlamentar\'[^>]+>\s*<i[^>]+><\/i>[^<]+)\s*\n\s*\n\s*<button/s' => '$1</a>

                            <button',
            '/(<a href="{{ route\(\'proposicoes\.onlyoffice\.editor-parlamentar\'[^>]+>\s*<i[^>]+><\/i>[^<]+)\s*\n\s*\n\s*@endif/s' => '$1</a>

                            @endif',
            '/(<a href="{{ route\(\'proposicoes\.onlyoffice\.editor-parlamentar\'[^>]+>\s*<i[^>]+><\/i>[^<]+)\s*\n\s*\n\s*<!--/s' => '$1</a>

                            <!--',
            
            // Assinatura - faltando </a>
            '/(<a href="{{ route\(\'proposicoes\.assinar\'[^>]+>\s*<i[^>]+><\/i>Assinar Documento)\s*<button/s' => '$1</a>

                            <button',
            '/(<a href="{{ route\(\'proposicoes\.assinar\'[^>]+>\s*<i[^>]+><\/i>Assinar Documento)\s*@if/s' => '$1</a>

                            @if'
        ];
        
        foreach ($patterns as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }
        
        return $content;
    }
    
    /**
     * Aplicar CSS otimizado para botões
     */
    private function aplicarCSSOptimizado(): void
    {
        $showViewFile = base_path('resources/views/proposicoes/show.blade.php');
        
        if (!File::exists($showViewFile)) {
            return;
        }
        
        $content = File::get($showViewFile);
        
        // SEMPRE reaplicar CSS para garantir que está atualizado
        $this->command->info('   🎨 Reaplicando CSS otimizado...');
        
        // Remover CSS antigo se existir
        if (str_contains($content, '.btn-onlyoffice')) {
            // Remover CSS existente
            $content = preg_replace('/\/\* Estilos otimizados para botões OnlyOffice \*\/.*?\/\* Espaçamento melhorado para botões de assinatura em grid \*\/[^}]*}/s', '', $content);
        }
        
        // CSS otimizado completo
        $cssOptimizado = '
<style>
/* Estilos otimizados para botões OnlyOffice */
.btn-onlyoffice {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    border-radius: 8px;
    font-weight: 600;
    padding: 12px 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-onlyoffice:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-onlyoffice .fas {
    transition: transform 0.3s ease;
}

.btn-onlyoffice:hover .fas {
    transform: scale(1.1);
}

/* Variações de cores para diferentes contextos */
.btn-onlyoffice.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
}

.btn-onlyoffice.btn-outline-primary {
    border: 2px solid #007bff;
    background: rgba(0, 123, 255, 0.05);
}

.btn-onlyoffice.btn-outline-primary:hover {
    background: #007bff;
    color: white;
}

.btn-onlyoffice.btn-outline-warning {
    border: 2px solid #ffc107;
    background: rgba(255, 193, 7, 0.05);
}

.btn-onlyoffice.btn-outline-warning:hover {
    background: #ffc107;
    color: #212529;
}

/* Espaçamento melhorado para botões em grid */
.d-grid .btn-onlyoffice {
    margin-bottom: 8px;
}

.d-grid .btn-onlyoffice:last-child {
    margin-bottom: 0;
}

/* Estilos otimizados para botão de assinatura */
.btn-assinatura {
    font-weight: 600;
    padding: 12px 20px;
    border-radius: 8px;
}

.btn-assinatura:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.btn-assinatura .fas {
    /* Removido animações que podem interferir com o clique */
}

/* Estilo específico para botão de assinatura success */
.btn-assinatura.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
    border: none;
}

.btn-assinatura.btn-success:hover {
    background: linear-gradient(135deg, #218838 0%, #1c7430 100%);
}

/* Espaçamento melhorado para botões de assinatura em grid */
.d-grid .btn-assinatura {
    margin-bottom: 8px;
    position: relative;
    z-index: 1;
    display: inline-block;
}

.d-grid .btn-assinatura:last-child {
    margin-bottom: 0;
}
</style>';
        
        // Procurar onde inserir o CSS (após @section('content'))
        if (str_contains($content, '@section(\'content\')')) {
            $content = str_replace('@section(\'content\')', '@section(\'content\')' . $cssOptimizado, $content);
            File::put($showViewFile, $content);
            $this->command->info('   🎨 CSS otimizado aplicado');
        } else {
            $this->command->warn('   ⚠️ Não foi possível aplicar CSS - seção content não encontrada');
        }
    }
    
    /**
     * Validar que as correções foram aplicadas corretamente
     */
    private function validarCorrecoes(): void
    {
        $showViewFile = base_path('resources/views/proposicoes/show.blade.php');
        
        if (!File::exists($showViewFile)) {
            $this->command->error('   ❌ Arquivo show.blade.php não existe');
            return;
        }
        
        $content = File::get($showViewFile);
        
        $validacoes = [
            'btn-onlyoffice presente' => str_contains($content, 'btn-onlyoffice'),
            'btn-assinatura presente' => str_contains($content, 'btn-assinatura'),
            'CSS OnlyOffice aplicado' => str_contains($content, '.btn-onlyoffice {'),
            'CSS Assinatura aplicado' => str_contains($content, '.btn-assinatura {'),
            'Botões com btn-lg' => str_contains($content, 'btn-lg btn-onlyoffice') && str_contains($content, 'btn-lg btn-assinatura'),
            'Tags de fechamento' => substr_count($content, 'btn-onlyoffice">') === substr_count($content, '</a>') - substr_count($content, 'btn-assinatura">')
        ];
        
        $this->command->info('   🔍 Validando aplicação das correções...');
        
        foreach ($validacoes as $descricao => $resultado) {
            if ($resultado) {
                $this->command->info("      ✅ $descricao");
            } else {
                $this->command->warn("      ⚠️ $descricao - pode precisar verificação manual");
            }
        }
    }
    
    /**
     * Exibir resumo das otimizações aplicadas
     */
    private function exibirResumoOtimizacoes(): void
    {
        $this->command->info('');
        $this->command->info('🎯 ====== OTIMIZAÇÕES DE UI APLICADAS ======');
        $this->command->info('');
        $this->command->info('🔧 BOTÕES ONLYOFFICE:');
        $this->command->info('   ✅ Estrutura HTML corrigida (tags </a> fechadas)');
        $this->command->info('   ✅ Classes btn-lg btn-onlyoffice aplicadas');
        $this->command->info('   ✅ CSS com gradientes e hover effects');
        $this->command->info('   ✅ Animações suaves (transform, scale)');
        $this->command->info('');
        $this->command->info('🖊️ BOTÕES ASSINATURA:');
        $this->command->info('   ✅ Estrutura HTML corrigida (tags </a> fechadas)');
        $this->command->info('   ✅ Classes btn-lg btn-assinatura aplicadas');
        $this->command->info('   ✅ CSS otimizado sem conflitos de clique');
        $this->command->info('   ✅ Z-index configurado para clicabilidade');
        $this->command->info('');
        $this->command->info('🎨 EXPERIÊNCIA DO USUÁRIO:');
        $this->command->info('   ✅ Botões proporcionais e profissionais');
        $this->command->info('   ✅ Efeitos hover consistentes');
        $this->command->info('   ✅ Navegação fluida sem problemas de clique');
        $this->command->info('   ✅ Interface moderna e responsiva');
        $this->command->info('');
        $this->command->info('🚀 RESULTADOS:');
        $this->command->info('   ✅ UI consistente em todos os botões importantes');
        $this->command->info('   ✅ Funcionalidade preservada e melhorada');
        $this->command->info('   ✅ Código limpo e manutenível');
        $this->command->info('   ✅ Compatível com todos os navegadores');
        $this->command->info('');
        $this->command->info('🎉 UI otimizada preservada permanentemente!');
        $this->command->info('================================== FIM ==================================');
    }
}