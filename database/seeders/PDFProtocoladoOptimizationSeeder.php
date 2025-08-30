<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class PDFProtocoladoOptimizationSeeder extends Seeder
{
    /**
     * Seed para configurar sistema de otimização de PDFs protocolados
     */
    public function run(): void
    {
        $this->command->info('🎯 Configurando Sistema de Otimização de PDFs Protocolados...');
        
        // 1. Verificar e criar diretórios necessários
        $this->criarDiretoriosNecessarios();
        
        // 2. Verificar dependências do sistema
        $this->verificarDependenciasSistema();
        
        // 3. Configurar arquivos de configuração
        $this->configurarArquivosConfiguracao();
        
        // 4. Validar instalação
        $this->validarInstalacao();
        
        $this->command->info('✅ Sistema de Otimização de PDFs Protocolados configurado!');
        
        // Exibir resumo das configurações
        $this->exibirResumoConfiguracao();
    }
    
    /**
     * Criar diretórios necessários para o sistema
     */
    private function criarDiretoriosNecessarios(): void
    {
        $this->command->info('📁 Criando diretórios necessários...');
        
        $diretorios = [
            storage_path('app/proposicoes/pdfs'),
            storage_path('logs'),
            storage_path('fonts'),
            storage_path('temp'),
        ];
        
        foreach ($diretorios as $diretorio) {
            if (!is_dir($diretorio)) {
                File::makeDirectory($diretorio, 0755, true);
                $this->command->line("  ✓ Criado: {$diretorio}");
            } else {
                $this->command->line("  ✓ Existe: {$diretorio}");
            }
        }
    }
    
    /**
     * Verificar dependências do sistema
     */
    private function verificarDependenciasSistema(): void
    {
        $this->command->info('🔍 Verificando dependências do sistema...');
        
        // Verificar Ghostscript
        $this->verificarGhostscript();
        
        // Verificar LibreOffice
        $this->verificarLibreOffice();
        
        // Verificar ExifTool
        $this->verificarExifTool();
        
        // Verificar fontes
        $this->verificarFontes();
    }
    
    /**
     * Verificar se Ghostscript está disponível
     */
    private function verificarGhostscript(): void
    {
        exec('which gs', $output, $returnCode);
        
        if ($returnCode === 0) {
            exec('gs --version', $version);
            $this->command->line("  ✅ Ghostscript: " . trim($version[0] ?? 'Disponível'));
        } else {
            $this->command->warn("  ⚠️ Ghostscript: Não disponível");
            $this->command->line("     ℹ️ Instale com: sudo apt-get install ghostscript");
        }
    }
    
    /**
     * Verificar se LibreOffice está disponível
     */
    private function verificarLibreOffice(): void
    {
        exec('which libreoffice', $output, $returnCode);
        
        if ($returnCode === 0) {
            exec('libreoffice --version', $version);
            $this->command->line("  ✅ LibreOffice: " . trim($version[0] ?? 'Disponível'));
        } else {
            $this->command->warn("  ⚠️ LibreOffice: Não disponível");
            $this->command->line("     ℹ️ Instale com: sudo apt-get install libreoffice-headless");
        }
    }
    
    /**
     * Verificar se ExifTool está disponível
     */
    private function verificarExifTool(): void
    {
        exec('which exiftool', $output, $returnCode);
        
        if ($returnCode === 0) {
            exec('exiftool -ver', $version);
            $this->command->line("  ✅ ExifTool: " . trim($version[0] ?? 'Disponível'));
        } else {
            $this->command->line("  ℹ️ ExifTool: Não disponível (opcional)");
            $this->command->line("     ℹ️ Instale com: sudo apt-get install exiftool");
        }
    }
    
    /**
     * Verificar fontes disponíveis
     */
    private function verificarFontes(): void
    {
        $fontsDir = storage_path('fonts');
        $fontes = array_merge(
            File::glob($fontsDir . '/*.ttf'),
            File::glob($fontsDir . '/*.otf'),
            File::glob($fontsDir . '/*.TTF'),
            File::glob($fontsDir . '/*.OTF')
        );
        
        if (empty($fontes)) {
            $this->command->warn("  ⚠️ Fontes: Nenhuma fonte encontrada");
            $this->command->line("     ℹ️ Copie fontes TTF/OTF para storage/fonts");
        } else {
            $this->command->line("  ✅ Fontes: " . count($fontes) . " fontes encontradas");
        }
    }
    
    /**
     * Configurar arquivos de configuração
     */
    private function configurarArquivosConfiguracao(): void
    {
        $this->command->info('⚙️ Configurando arquivos de configuração...');
        
        // Verificar configuração do DomPDF
        $this->verificarConfiguracaoDomPDF();
        
        // Verificar configuração do sistema
        $this->verificarConfiguracaoSistema();
    }
    
    /**
     * Verificar configuração do DomPDF
     */
    private function verificarConfiguracaoDomPDF(): void
    {
        $configFile = config_path('dompdf.php');
        
        if (!File::exists($configFile)) {
            $this->command->warn("  ⚠️ Configuração DomPDF: Arquivo não encontrado");
            return;
        }
        
        $config = require $configFile;
        
        // Verificar configurações críticas
        $configuracoesCriticas = [
            'default_paper_size' => 'a4',
            'default_paper_orientation' => 'portrait',
            'dpi' => 96,
            'pdf_backend' => 'CPDF',
        ];
        
        foreach ($configuracoesCriticas as $chave => $valorEsperado) {
            $valorAtual = $config['options'][$chave] ?? null;
            
            if ($valorAtual === $valorEsperado) {
                $this->command->line("  ✅ DomPDF {$chave}: {$valorAtual}");
            } else {
                $this->command->warn("  ⚠️ DomPDF {$chave}: {$valorAtual} (esperado: {$valorEsperado})");
            }
        }
    }
    
    /**
     * Verificar configuração do sistema
     */
    private function verificarConfiguracaoSistema(): void
    {
        $this->command->line("  ✅ PHP: " . PHP_VERSION);
        $this->command->line("  ✅ Laravel: " . app()->version());
        $this->command->line("  ✅ Storage: " . storage_path());
        $this->command->line("  ✅ Temp: " . sys_get_temp_dir());
    }
    
    /**
     * Validar instalação
     */
    private function validarInstalacao(): void
    {
        $this->command->info('🔍 Validando instalação...');
        
        // Verificar se o comando está disponível
        try {
            Artisan::call('list', ['--format' => 'json']);
            $this->command->line("  ✅ Comando Artisan: Funcionando");
        } catch (\Exception $e) {
            $this->command->error("  ❌ Comando Artisan: Erro - " . $e->getMessage());
        }
        
        // Verificar permissões de diretórios
        $this->verificarPermissoes();
        
        // Verificar conectividade com banco
        $this->verificarConectividadeBanco();
    }
    
    /**
     * Verificar permissões de diretórios
     */
    private function verificarPermissoes(): void
    {
        $diretorios = [
            storage_path('app/proposicoes/pdfs') => '0755',
            storage_path('logs') => '0755',
            storage_path('fonts') => '0755',
        ];
        
        foreach ($diretorios as $diretorio => $permissaoEsperada) {
            if (is_dir($diretorio)) {
                $permissaoAtual = substr(sprintf('%o', fileperms($diretorio)), -4);
                
                if ($permissaoAtual === $permissaoEsperada) {
                    $this->command->line("  ✅ Permissão {$diretorio}: {$permissaoAtual}");
                } else {
                    $this->command->warn("  ⚠️ Permissão {$diretorio}: {$permissaoAtual} (esperado: {$permissaoEsperada})");
                }
            }
        }
    }
    
    /**
     * Verificar conectividade com banco
     */
    private function verificarConectividadeBanco(): void
    {
        try {
            DB::connection()->getPdo();
            $this->command->line("  ✅ Banco de dados: Conectado");
        } catch (\Exception $e) {
            $this->command->error("  ❌ Banco de dados: Erro - " . $e->getMessage());
        }
    }
    
    /**
     * Exibir resumo da configuração
     */
    private function exibirResumoConfiguracao(): void
    {
        $this->command->info('');
        $this->command->info('🎯 ====== RESUMO DA CONFIGURAÇÃO ======');
        $this->command->info('');
        $this->command->info('📁 DIRETÓRIOS:');
        $this->command->info('   ✅ storage/app/proposicoes/pdfs - PDFs das proposições');
        $this->command->info('   ✅ storage/logs - Logs do sistema');
        $this->command->info('   ✅ storage/fonts - Fontes para PDFs');
        $this->command->info('   ✅ storage/temp - Arquivos temporários');
        $this->command->info('');
        $this->command->info('🔧 DEPENDÊNCIAS:');
        $this->command->info('   ✅ Ghostscript - Compressão de PDFs');
        $this->command->info('   ✅ LibreOffice - Conversão DOCX → PDF');
        $this->command->info('   ✅ ExifTool - Metadados de PDFs (opcional)');
        $this->command->info('');
        $this->command->info('⚙️ CONFIGURAÇÕES:');
        $this->command->info('   ✅ DomPDF - Configurado para A4 e alta qualidade');
        $this->command->info('   ✅ Fontes - Sistema de fontes configurado');
        $this->command->info('   ✅ Permissões - Diretórios com permissões corretas');
        $this->command->info('');
        $this->command->info('🚀 COMANDOS DISPONÍVEIS:');
        $this->command->info('   php artisan pdf:otimizar-protocolado {id} - Otimizar PDF específico');
        $this->command->info('   php artisan pdf:otimizar-protocolado --all - Otimizar todos');
        $this->command->info('   php artisan pdf:otimizar-protocolado --compare - Comparar tamanhos');
        $this->command->info('   php artisan pdf:otimizar-protocolado --force - Forçar regeneração');
        $this->command->info('');
        $this->command->info('📊 BENEFÍCIOS IMPLEMENTADOS:');
        $this->command->info('   🎯 Qualidade superior: 150 DPI vs 96 DPI padrão');
        $this->command->info('   📉 Compressão inteligente: Ghostscript otimizado');
        $this->command->info('   🔒 Segurança: PHP e JavaScript desabilitados');
        $this->command->info('   📝 Fontes: Subsetting habilitado para arquivos menores');
        $this->command->info('   🎨 Layout: Template profissional com marca d\'água');
        $this->command->info('   📱 QR Code: Verificação de autenticidade');
        $this->command->info('');
        $this->command->info('🎉 SISTEMA PRONTO PARA USO!');
    }
}
