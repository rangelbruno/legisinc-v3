<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ConfiguracaoSistemaPersistenteSeeder extends Seeder
{
    /**
     * Seeder para aplicar configurações persistentes do sistema
     * Este seeder SEMPRE deve ser executado após migrate:fresh
     */
    public function run(): void
    {
        $this->command->info('🔧 Aplicando configurações persistentes do sistema...');

        // 1. Aplicar correções em arquivos PHP
        $this->aplicarCorrecoesArquivos();

        // 2. Garantir diretórios essenciais
        $this->garantirDiretorios();

        // 3. Configurar fontes para PDF
        $this->configurarFontesPDF();

        // 4. Aplicar configurações do DomPDF
        $this->configurarDomPDF();

        $this->command->info('✅ Configurações persistentes aplicadas com sucesso!');
    }

    /**
     * Aplicar correções em arquivos PHP do sistema
     */
    private function aplicarCorrecoesArquivos(): void
    {
        $this->command->info('📝 Aplicando correções em arquivos...');

        // Correção 1: ProposicaoAssinaturaController
        $this->corrigirProposicaoAssinaturaController();

        // Correção 2: TemplateVariableService
        $this->corrigirTemplateVariableService();

        // Correção 3: Model Proposicao
        $this->corrigirModelProposicao();

        // Correção 4: ProposicaoProtocoloController
        $this->corrigirProposicaoProtocoloController();
    }

    private function corrigirProposicaoAssinaturaController(): void
    {
        $arquivo = app_path('Http/Controllers/ProposicaoAssinaturaController.php');

        if (! File::exists($arquivo)) {
            $this->command->warn('  ⚠️ ProposicaoAssinaturaController não encontrado');

            return;
        }

        $conteudo = File::get($arquivo);
        $modificado = false;

        // Correção linha 3849: cargo_atual
        if (strpos($conteudo, '$cargoAtual = $proposicao->autor->cargo_atual') === false) {
            $pattern = '/\$assinaturaDigitalHTML = "\s*<div[^>]*>\{\$proposicao->autor->cargo_atual \?\? \'Parlamentar\'\}<\/div>/';
            $replacement = '$cargoAtual = $proposicao->autor->cargo_atual ?? \'Parlamentar\';
$assinaturaDigitalHTML = "
    <div style=\'margin: 5px 0;\'>{$cargoAtual}</div>';

            $conteudoNovo = preg_replace($pattern, $replacement, $conteudo);

            if ($conteudoNovo !== $conteudo) {
                $conteudo = $conteudoNovo;
                $modificado = true;
            }
        }

        // Correção linha 3929: rodape_texto
        $buscar = "{\$variables['rodape_texto'] ?? 'Câmara Municipal de Caraguatatuba - Documento Oficial'}";
        $substituir = '" . ($variables[\'rodape_texto\'] ?? \'Câmara Municipal de Caraguatatuba - Documento Oficial\') . "';

        if (strpos($conteudo, $buscar) !== false) {
            $conteudo = str_replace($buscar, $substituir, $conteudo);
            $modificado = true;
        }

        if ($modificado) {
            File::put($arquivo, $conteudo);
            $this->command->line('  ✓ ProposicaoAssinaturaController corrigido');
        } else {
            $this->command->line('  ✓ ProposicaoAssinaturaController já está correto');
        }
    }

    private function corrigirTemplateVariableService(): void
    {
        $arquivo = app_path('Services/Template/TemplateVariableService.php');

        if (! File::exists($arquivo)) {
            $this->command->warn('  ⚠️ TemplateVariableService não encontrado');

            return;
        }

        $conteudo = File::get($arquivo);
        $modificado = false;

        // Adicionar nome_parlamentar se não existir
        if (strpos($conteudo, "'nome_parlamentar'") === false) {
            // Adicionar em getTemplateVariables
            $pattern = '/(getTemplateVariables\(\): array\s*\{[^}]*)(return \$variables;)/s';
            $replacement = '$1        \'nome_parlamentar\' => \'{nome_parlamentar}\',
        $2';

            $conteudoNovo = preg_replace($pattern, $replacement, $conteudo);

            if ($conteudoNovo !== $conteudo) {
                $conteudo = $conteudoNovo;
                $modificado = true;
            }

            // Adicionar em listAvailableVariables
            $pattern = '/(listAvailableVariables\(\): array\s*\{[^}]*)(];)/s';
            $replacement = '$1            \'${nome_parlamentar}\' => \'Nome do parlamentar logado\',
        $2';

            $conteudoNovo = preg_replace($pattern, $replacement, $conteudo);

            if ($conteudoNovo !== $conteudo) {
                $conteudo = $conteudoNovo;
                $modificado = true;
            }
        }

        if ($modificado) {
            File::put($arquivo, $conteudo);
            $this->command->line('  ✓ TemplateVariableService corrigido');
        } else {
            $this->command->line('  ✓ TemplateVariableService já está correto');
        }
    }

    private function corrigirModelProposicao(): void
    {
        $arquivo = app_path('Models/Proposicao.php');

        if (! File::exists($arquivo)) {
            $this->command->warn('  ⚠️ Model Proposicao não encontrado');

            return;
        }

        $conteudo = File::get($arquivo);
        $modificado = false;

        // Descomentar 'numero'
        if (strpos($conteudo, "// 'numero'") !== false || strpos($conteudo, "//'numero'") !== false) {
            $conteudo = preg_replace('/\/\/\s*\'numero\'/', "'numero'", $conteudo);
            $modificado = true;
        }

        // Adicionar 'numero_sequencial' se não existir
        if (strpos($conteudo, "'numero_sequencial'") === false && strpos($conteudo, "'numero'") !== false) {
            $pattern = '/(\$fillable = \[[^\]]*\'numero\'[^\]]*)(];)/s';
            $replacement = '$1,
        \'numero_sequencial\'$2';

            $conteudoNovo = preg_replace($pattern, $replacement, $conteudo);

            if ($conteudoNovo !== $conteudo) {
                $conteudo = $conteudoNovo;
                $modificado = true;
            }
        }

        if ($modificado) {
            File::put($arquivo, $conteudo);
            $this->command->line('  ✓ Model Proposicao corrigido');
        } else {
            $this->command->line('  ✓ Model Proposicao já está correto');
        }
    }

    private function corrigirProposicaoProtocoloController(): void
    {
        $arquivo = app_path('Http/Controllers/ProposicaoProtocoloController.php');

        if (! File::exists($arquivo)) {
            $this->command->warn('  ⚠️ ProposicaoProtocoloController não encontrado');

            return;
        }

        $conteudo = File::get($arquivo);
        $modificado = false;

        // Adicionar verificações de segurança se necessário
        // Por enquanto, apenas verifica se o arquivo existe

        $this->command->line('  ✓ ProposicaoProtocoloController verificado');
    }

    /**
     * Garantir que diretórios essenciais existam
     */
    private function garantirDiretorios(): void
    {
        $this->command->info('📁 Garantindo diretórios essenciais...');

        $diretorios = [
            storage_path('app/backup-dados-criticos'),
            storage_path('app/config-backup'),
            storage_path('app/private/templates'),
            storage_path('app/proposicoes'),
            storage_path('app/public/templates'),
            storage_path('backups'),
            storage_path('fonts'),
            storage_path('logs'),
        ];

        foreach ($diretorios as $dir) {
            if (! File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
                $this->command->line('  ✓ Criado: '.str_replace(storage_path(), 'storage', $dir));
            } else {
                $this->command->line('  ✓ Existe: '.str_replace(storage_path(), 'storage', $dir));
            }
        }
    }

    /**
     * Configurar fontes para geração de PDF
     */
    private function configurarFontesPDF(): void
    {
        $this->command->info('🔤 Configurando fontes para PDF...');

        $fontsDir = storage_path('fonts');

        // Criar arquivo .gitkeep se não existir
        $gitkeep = $fontsDir.'/.gitkeep';
        if (! File::exists($gitkeep)) {
            File::put($gitkeep, '');
            $this->command->line('  ✓ Criado .gitkeep em storage/fonts');
        }

        // Verificar se há fontes instaladas
        $fontes = array_merge(
            File::glob($fontsDir.'/*.ttf'),
            File::glob($fontsDir.'/*.otf'),
            File::glob($fontsDir.'/*.TTF'),
            File::glob($fontsDir.'/*.OTF')
        );

        if (empty($fontes)) {
            $this->command->warn('  ⚠️ Nenhuma fonte encontrada em storage/fonts');
            $this->command->warn('  ℹ️ Copie fontes TTF/OTF para storage/fonts para melhor qualidade de PDF');
        } else {
            $this->command->line('  ✓ '.count($fontes).' fontes encontradas');
        }
    }

    /**
     * Configurar DomPDF
     */
    private function configurarDomPDF(): void
    {
        $this->command->info('📄 Configurando DomPDF...');

        $configFile = config_path('dompdf.php');

        if (! File::exists($configFile)) {
            // Criar arquivo de configuração básico
            $config = <<<'PHP'
<?php

return [
    'show_warnings' => false,
    'orientation' => 'portrait',
    'defines' => [
        'font_dir' => storage_path('fonts/'),
        'font_cache' => storage_path('fonts/'),
        'temp_dir' => sys_get_temp_dir(),
        'chroot' => realpath(base_path()),
        'enable_font_subsetting' => false,
        'pdf_backend' => 'CPDF',
        'default_media_type' => 'screen',
        'default_paper_size' => 'a4',
        'default_font' => 'serif',
        'dpi' => 96,
        'enable_php' => false,
        'enable_javascript' => false,
        'enable_remote' => true,
        'font_height_ratio' => 1.1,
        'enable_html5_parser' => true,
    ],
];
PHP;

            File::put($configFile, $config);
            $this->command->line('  ✓ Arquivo config/dompdf.php criado');
        } else {
            $this->command->line('  ✓ Arquivo config/dompdf.php já existe');
        }
    }
}
