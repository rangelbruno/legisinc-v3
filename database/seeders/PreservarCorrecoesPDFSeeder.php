<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PreservarCorrecoesPDFSeeder extends Seeder
{
    /**
     * Validar e preservar correções críticas de PDF implementadas
     */
    public function run(): void
    {
        $this->comment('🔍 VALIDANDO CORREÇÕES CRÍTICAS DE PDF...');
        
        // 1. Validar correção de invalidação PDF após aprovação legislativa
        $this->validarCorrecaoAprovacaoLegislativa();
        
        // 2. Validar correção de detecção RTF mais novo
        $this->validarDeteccaoRTFMaisNovo();
        
        // 3. Validar correção de assinatura digital
        $this->validarCorrecaoAssinaturaDigital();
        
        $this->info('✅ Validação de correções críticas concluída!');
        $this->newLine();
    }
    
    private function validarCorrecaoAprovacaoLegislativa()
    {
        $this->info('📋 Verificando correção de aprovação legislativa...');
        
        $controllerPath = app_path('Http/Controllers/ProposicaoController.php');
        
        if (!File::exists($controllerPath)) {
            $this->error("❌ ProposicaoController não encontrado");
            return;
        }
        
        $content = File::get($controllerPath);
        
        // Verificar se a correção de invalidação de PDF foi implementada
        $correcoes = [
            "// CRÍTICO: Invalidar PDF antigo para forçar regeneração com últimas alterações do OnlyOffice" => 'Comentário explicativo da correção',
            "'arquivo_pdf_path' => null," => 'Invalidação do arquivo_pdf_path',
            "'pdf_gerado_em' => null," => 'Invalidação do pdf_gerado_em',
            "'pdf_conversor_usado' => null," => 'Invalidação do pdf_conversor_usado',
            'aprovarEdicoesLegislativo' => 'Método de aprovação legislativa'
        ];
        
        $correcoesEncontradas = 0;
        foreach ($correcoes as $codigo => $descricao) {
            if (strpos($content, $codigo) !== false) {
                $correcoesEncontradas++;
                $this->info("✅ $descricao encontrado");
            } else {
                $this->warn("⚠️  $descricao não encontrado");
            }
        }
        
        if ($correcoesEncontradas >= 4) {
            $this->info("✅ Correção de aprovação legislativa preservada ($correcoesEncontradas/5)");
        } else {
            $this->error("❌ Correção de aprovação legislativa PERDIDA ($correcoesEncontradas/5)");
        }
    }
    
    private function validarDeteccaoRTFMaisNovo()
    {
        $this->info('📄 Verificando detecção de RTF mais novo...');
        
        $controllerPath = app_path('Http/Controllers/ProposicaoController.php');
        $content = File::get($controllerPath);
        
        // Verificar se a correção de detecção RTF mais novo foi implementada
        $correcoes = [
            "// CRÍTICO: Verificar se RTF foi modificado após PDF" => 'Comentário da detecção RTF',
            '$pdfEstaDesatualizado = false;' => 'Variável de controle',
            '$rtfModificado = filemtime($rtfPath);' => 'Obtenção timestamp RTF',
            '$pdfGerado = filemtime($pdfPath);' => 'Obtenção timestamp PDF',
            'if ($rtfModificado > $pdfGerado)' => 'Comparação de timestamps',
            'PDF desatualizado detectado - invalidando cache' => 'Log de invalidação'
        ];
        
        $correcoesEncontradas = 0;
        foreach ($correcoes as $codigo => $descricao) {
            if (strpos($content, $codigo) !== false) {
                $correcoesEncontradas++;
                $this->info("✅ $descricao encontrado");
            } else {
                $this->warn("⚠️  $descricao não encontrado");
            }
        }
        
        if ($correcoesEncontradas >= 5) {
            $this->info("✅ Detecção de RTF mais novo preservada ($correcoesEncontradas/6)");
        } else {
            $this->error("❌ Detecção de RTF mais novo PERDIDA ($correcoesEncontradas/6)");
        }
    }
    
    private function validarCorrecaoAssinaturaDigital()
    {
        $this->info('🔐 Verificando correção de assinatura digital...');
        
        $servicePath = app_path('Services/AssinaturaDigitalService.php');
        
        if (!File::exists($servicePath)) {
            $this->error("❌ AssinaturaDigitalService não encontrado");
            return;
        }
        
        $content = File::get($servicePath);
        
        // Verificar se a correção de verificação dupla foi implementada
        $correcoes = [
            "// Check if file exists using both direct path and Storage" => 'Comentário da correção dupla',
            'str_replace(storage_path(\'app/\'), \'\', $pdfAssinado);' => 'Conversão para path relativo',
            'Storage::exists($relativePath);' => 'Verificação via Storage',
            'if ($fileExists) {' => 'Condição de arquivo existente'
        ];
        
        $correcoesEncontradas = 0;
        foreach ($correcoes as $codigo => $descricao) {
            if (strpos($content, $codigo) !== false) {
                $correcoesEncontradas++;
                $this->info("✅ $descricao encontrado");
            } else {
                $this->warn("⚠️  $descricao não encontrado");
            }
        }
        
        if ($correcoesEncontradas >= 3) {
            $this->info("✅ Correção de assinatura digital preservada ($correcoesEncontradas/4)");
        } else {
            $this->error("❌ Correção de assinatura digital PERDIDA ($correcoesEncontradas/4)");
        }
    }
    
    // Helper methods
    private function info($message)
    {
        echo "\033[0;32m$message\033[0m\n";
    }
    
    private function warn($message)
    {
        echo "\033[0;33m$message\033[0m\n";
    }
    
    private function error($message)
    {
        echo "\033[0;31m$message\033[0m\n";
    }
    
    private function comment($message)
    {
        echo "\033[0;36m$message\033[0m\n";
    }
    
    private function newLine()
    {
        echo "\n";
    }
}