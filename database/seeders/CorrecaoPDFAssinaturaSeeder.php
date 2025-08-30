<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CorrecaoPDFAssinaturaSeeder extends Seeder
{
    /**
     * Aplicar correção específica para renderização correta de PDF com assinatura
     */
    public function run(): void
    {
        echo "🔧 Aplicando correção específica para PDF com assinatura...\n";

        $this->corrigirAssinaturaQRService();
        $this->corrigirProposicaoAssinaturaController();

        echo "✅ Correção de PDF com assinatura aplicada com sucesso!\n";
    }

    /**
     * Corrigir AssinaturaQRService para gerar HTML compatível com PDF
     */
    private function corrigirAssinaturaQRService(): void
    {
        $servicePath = app_path('Services/Template/AssinaturaQRService.php');

        if (! file_exists($servicePath)) {
            echo "❌ AssinaturaQRService.php não encontrado!\n";

            return;
        }

        $content = file_get_contents($servicePath);

        // Verificar se já tem a correção
        if (strpos($content, '// CORREÇÃO PDF: Remover position fixed que não funciona em PDF') !== false) {
            echo "✅ AssinaturaQRService já corrigido para PDF.\n";

            return;
        }

        // Substituir método gerarHTMLAssinaturaFormatado para ser compatível com PDF
        $search = 'private function gerarHTMLAssinaturaFormatado(string $texto): string
    {
        return \'<div class="assinatura-digital" style="position: fixed; right: 20px; top: 50%; transform: translateY(-50%); width: 200px; border: 2px solid #28a745; padding: 15px; margin: 10px 0; background-color: #f8f9fa; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); font-family: Arial, sans-serif;">
            <h6 style="color: #28a745; margin-bottom: 10px; text-align: center; font-size: 14px; font-weight: bold;"><i class="fas fa-certificate"></i> Assinatura Digital</h6>
            <div style="font-size: 11px; line-height: 1.4; text-align: center; color: #333;">\' . nl2br($texto) . \'</div>
        </div>\';
    }';

        $replace = 'private function gerarHTMLAssinaturaFormatado(string $texto): string
    {
        // CORREÇÃO PDF: Remover position fixed que não funciona em PDF
        // Usar layout em bloco normal compatível com DomPDF
        return \'<div class="assinatura-digital" style="width: 100%; border: 2px solid #28a745; padding: 15px; margin: 20px 0; background-color: #f0f8f0; border-radius: 8px; font-family: Arial, sans-serif; page-break-inside: avoid; text-align: center;">
            <h6 style="color: #28a745; margin: 0 0 10px 0; font-size: 14px; font-weight: bold;">🏆 ASSINATURA DIGITAL</h6>
            <div style="font-size: 12px; line-height: 1.5; color: #333; font-weight: bold;">\' . nl2br($texto) . \'</div>
        </div>\';
    }';

        if (strpos($content, $search) !== false) {
            $content = str_replace($search, $replace, $content);
            file_put_contents($servicePath, $content);
            echo "✅ AssinaturaQRService corrigido para renderização em PDF.\n";
        } else {
            echo "⚠️ Método gerarHTMLAssinaturaFormatado não encontrado na forma esperada.\n";
        }
    }

    /**
     * Corrigir ProposicaoAssinaturaController para melhor renderização
     */
    private function corrigirProposicaoAssinaturaController(): void
    {
        $controllerPath = app_path('Http/Controllers/ProposicaoAssinaturaController.php');

        if (! file_exists($controllerPath)) {
            echo "❌ ProposicaoAssinaturaController.php não encontrado!\n";

            return;
        }

        $content = file_get_contents($controllerPath);

        // Verificar se já tem a correção
        if (strpos($content, '// CORREÇÃO PDF: CSS otimizado para DomPDF') !== false) {
            echo "✅ ProposicaoAssinaturaController já corrigido para PDF.\n";

            return;
        }

        // Corrigir CSS no método gerarHTMLParaPDF
        $cssSearch = '                .assinatura-digital { 
                    border: 1px solid #28a745; 
                    padding: 10px; 
                    margin: 20px 0; 
                    background-color: #f8f9fa;
                    font-family: Arial, sans-serif;
                    font-size: 10pt;
                }';

        $cssReplace = '                .assinatura-digital { 
                    border: 2px solid #28a745; 
                    padding: 15px; 
                    margin: 20px 0; 
                    background-color: #f0f8f0;
                    font-family: Arial, sans-serif;
                    font-size: 12pt;
                    page-break-inside: avoid;
                    width: 100%;
                    box-sizing: border-box;
                    text-align: center;
                }
                /* CORREÇÃO PDF: CSS otimizado para DomPDF */
                .assinatura-digital h6 { 
                    color: #28a745; 
                    margin: 0 0 10px 0; 
                    font-size: 14pt;
                    font-weight: bold;
                }';

        if (strpos($content, $cssSearch) !== false) {
            $content = str_replace($cssSearch, $cssReplace, $content);
        }

        // Adicionar também CSS para melhor formatação geral do PDF
        $bodyStyleSearch = '                body { 
                    font-family: \'Times New Roman\', serif; 
                    margin: 2.5cm 2cm 2cm 2cm; 
                    line-height: 1.8; 
                    font-size: 12pt;
                    color: #000;
                    text-align: justify;
                }';

        $bodyStyleReplace = '                body { 
                    font-family: \'Times New Roman\', serif; 
                    margin: 2.5cm 2cm 2cm 2cm; 
                    line-height: 1.6; 
                    font-size: 12pt;
                    color: #000;
                    text-align: justify;
                }
                /* CORREÇÃO PDF: Melhor formatação para número de protocolo */
                .documento-numero {
                    font-size: 14pt;
                    font-weight: bold;
                    text-align: center;
                    margin: 20px 0;
                }';

        if (strpos($content, $bodyStyleSearch) !== false) {
            $content = str_replace($bodyStyleSearch, $bodyStyleReplace, $content);
        }

        file_put_contents($controllerPath, $content);
        echo "✅ ProposicaoAssinaturaController corrigido para melhor renderização em PDF.\n";
    }
}
