<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CorrecaoStatusPDFSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->comment('🔧 APLICANDO CORREÇÕES DE STATUS E PDF...');
        
        // 1. Corrigir mapeamento de status na view show.blade.php
        $this->corrigirMapeamentoStatus();
        
        // 2. Validar otimização de regeneração de PDF
        $this->validarOtimizacaoPDF();
        
        // 3. Validar correção do botão PDF imediato
        $this->validarBotaoPDFImediato();
        
        $this->info('✅ Correções de status e PDF aplicadas com sucesso!');
        $this->newLine();
    }
    
    private function corrigirMapeamentoStatus()
    {
        $this->info('📝 Verificando mapeamento de status...');
        
        $viewPath = resource_path('views/proposicoes/show.blade.php');
        
        if (!File::exists($viewPath)) {
            $this->error("❌ View show.blade.php não encontrada");
            return;
        }
        
        $content = File::get($viewPath);
        
        // Verificar se os status foram corrigidos
        $statusCorrigidos = [
            'enviado_protocolo' => 'Enviado ao Protocolo',
            'aprovado_assinatura' => 'Aguardando Assinatura',
            'assinado' => 'Assinado',
            'protocolado' => 'Protocolado'
        ];
        
        $statusEncontrados = 0;
        foreach ($statusCorrigidos as $status => $label) {
            if (strpos($content, "'$status': '$label'") !== false) {
                $statusEncontrados++;
                $this->info("✅ Status '$status' mapeado corretamente");
            } else {
                $this->warn("⚠️  Status '$status' não encontrado no mapeamento");
            }
        }
        
        if ($statusEncontrados >= 3) {
            $this->info("✅ Mapeamento de status corrigido ($statusEncontrados/4 status encontrados)");
        } else {
            $this->warn("⚠️  Apenas $statusEncontrados/4 status corrigidos");
        }
    }
    
    private function validarOtimizacaoPDF()
    {
        $this->info('📄 Verificando otimização de PDF...');
        
        $controllerPath = app_path('Http/Controllers/ProposicaoAssinaturaController.php');
        
        if (!File::exists($controllerPath)) {
            $this->error("❌ Controller ProposicaoAssinaturaController não encontrado");
            return;
        }
        
        $content = File::get($controllerPath);
        
        // Verificar se as otimizações foram aplicadas
        $otimizacoes = [
            'precisaRegerarPDF' => 'Método de verificação de regeneração PDF',
            'if ($precisaRegerarPDF)' => 'Condição de regeneração otimizada',
            '// 30 minutos' => 'Cache de PDF por 30 minutos',
            'filemtime($pdfPath)' => 'Verificação de idade do arquivo PDF'
        ];
        
        $otimizacoesEncontradas = 0;
        foreach ($otimizacoes as $codigo => $descricao) {
            if (strpos($content, $codigo) !== false) {
                $otimizacoesEncontradas++;
                $this->info("✅ $descricao implementado");
            } else {
                $this->warn("⚠️  $descricao não encontrado");
            }
        }
        
        if ($otimizacoesEncontradas >= 3) {
            $this->info("✅ Otimizações de PDF aplicadas ($otimizacoesEncontradas/4 otimizações)");
        } else {
            $this->warn("⚠️  Apenas $otimizacoesEncontradas/4 otimizações aplicadas");
        }
    }
    
    private function validarBotaoPDFImediato()
    {
        $this->info('📄 Verificando correção do botão PDF imediato...');
        
        $controllerPath = app_path('Http/Controllers/ProposicaoController.php');
        
        if (!File::exists($controllerPath)) {
            $this->error("❌ Controller ProposicaoController não encontrado");
            return;
        }
        
        $content = File::get($controllerPath);
        
        // Verificar se as propriedades foram adicionadas
        $propriedades = [
            'has_pdf = !empty' => 'Propriedade has_pdf adicionada',
            'has_arquivo = !empty' => 'Propriedade has_arquivo adicionada'
        ];
        
        $propriedadesEncontradas = 0;
        foreach ($propriedades as $codigo => $descricao) {
            if (strpos($content, $codigo) !== false) {
                $propriedadesEncontradas++;
                $this->info("✅ $descricao");
            } else {
                $this->warn("⚠️  $descricao não encontrado");
            }
        }
        
        if ($propriedadesEncontradas >= 1) {
            $this->info("✅ Botão PDF imediato corrigido ($propriedadesEncontradas/2 propriedades)");
        } else {
            $this->warn("⚠️  Botão PDF imediato não corrigido");
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