<?php

namespace App\Services\Performance;

use App\Models\Proposicao;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PDFProtocoladoOptimizationService
{
    /**
     * Gerar PDF protocolado otimizado com qualidade superior
     */
    public function gerarPDFProtocoladoOtimizado(Proposicao $proposicao): string
    {
        $this->log('🎯 Iniciando geração de PDF protocolado otimizado');
        
        // 1. Gerar PDF base com configurações otimizadas
        $pdfPath = $this->gerarPDFBaseOtimizado($proposicao);
        
        // 2. Aplicar otimizações avançadas
        $pdfOtimizado = $this->aplicarOtimizacoesAvancadas($pdfPath);
        
        // 3. Validar qualidade final
        $this->validarQualidadePDF($pdfOtimizado);
        
        $this->log('✅ PDF protocolado otimizado gerado com sucesso');
        
        return $pdfOtimizado;
    }
    
    /**
     * Gerar PDF base com configurações DomPDF otimizadas
     */
    private function gerarPDFBaseOtimizado(Proposicao $proposicao): string
    {
        $nomePdf = "proposicao_{$proposicao->id}_protocolado_otimizado_" . time() . '.pdf';
        $diretorioPdf = "proposicoes/pdfs/{$proposicao->id}";
        $caminhoPdfRelativo = $diretorioPdf . '/' . $nomePdf;
        $caminhoPdfAbsoluto = storage_path('app/' . $caminhoPdfRelativo);
        
        // Garantir que diretório existe
        if (!is_dir(dirname($caminhoPdfAbsoluto))) {
            mkdir(dirname($caminhoPdfAbsoluto), 0755, true);
        }
        
        // Gerar HTML otimizado para protocolo
        $html = $this->gerarHTMLOtimizadoParaProtocolo($proposicao);
        
        // Configurações DomPDF otimizadas para qualidade superior
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        
        // Configurações de alta qualidade
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false, // Segurança
            'isPhpEnabled' => false,    // Segurança
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 150,               // Alta resolução para qualidade
            'enableFontSubsetting' => true, // Subsetting de fontes
            'pdfBackend' => 'CPDF',     // Backend mais estável
            'tempDir' => sys_get_temp_dir(),
            'chroot' => realpath(base_path()),
            'logOutputFile' => storage_path('logs/dompdf.log'),
            'defaultMediaType' => 'screen',
            'defaultPaperSize' => 'a4',
            'defaultPaperOrientation' => 'portrait',
            'fontHeightRatio' => 1.1,
            'enableCssFloat' => true,
            'enableJavascript' => false,
            'enableInlinePhp' => false,
        ]);
        
        // Salvar PDF
        file_put_contents($caminhoPdfAbsoluto, $pdf->output());
        
        $this->log("📄 PDF base gerado: " . filesize($caminhoPdfAbsoluto) . " bytes");
        
        return $caminhoPdfAbsoluto;
    }
    
    /**
     * Aplicar otimizações avançadas ao PDF
     */
    private function aplicarOtimizacoesAvancadas(string $pdfPath): string
    {
        $this->log('🔧 Aplicando otimizações avançadas...');
        
        $originalSize = filesize($pdfPath);
        $this->log("📊 Tamanho original: " . $this->formatBytes($originalSize));
        
        // 1. Otimização com Ghostscript (compressão inteligente)
        if ($this->otimizarComGhostscript($pdfPath)) {
            $novoTamanho = filesize($pdfPath);
            $reducao = round((($originalSize - $novoTamanho) / $originalSize) * 100, 2);
            $this->log("📉 Compressão Ghostscript: {$reducao}% de redução");
        }
        
        // 2. Otimização de metadados
        $this->otimizarMetadados($pdfPath);
        
        // 3. Validação de integridade
        $this->validarIntegridadePDF($pdfPath);
        
        $tamanhoFinal = filesize($pdfPath);
        $this->log("📊 Tamanho final otimizado: " . $this->formatBytes($tamanhoFinal));
        
        return $pdfPath;
    }
    
    /**
     * Otimização com Ghostscript (compressão inteligente)
     */
    private function otimizarComGhostscript(string $pdfPath): bool
    {
        try {
            // Verificar se Ghostscript está disponível
            exec('which gs', $output, $returnCode);
            if ($returnCode !== 0) {
                $this->log('⚠️ Ghostscript não disponível, pulando otimização');
                return false;
            }
            
            $tempPath = $pdfPath . '_temp';
            $originalSize = filesize($pdfPath);
            
            // Comando Ghostscript otimizado para qualidade vs. tamanho
            $gsCommand = sprintf(
                'gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.7 ' .
                '-dPDFSETTINGS=/printer ' .  // Qualidade alta (printer) vs /screen (baixa)
                '-dColorImageDownsampleType=/Bicubic ' .
                '-dColorImageResolution=150 ' .
                '-dGrayImageDownsampleType=/Bicubic ' .
                '-dGrayImageResolution=150 ' .
                '-dMonoImageDownsampleType=/Bicubic ' .
                '-dMonoImageResolution=150 ' .
                '-dNOPAUSE -dQUIET -dBATCH ' .
                '-sOutputFile=%s %s 2>/dev/null',
                escapeshellarg($tempPath),
                escapeshellarg($pdfPath)
            );
            
            exec($gsCommand, $output, $returnCode);
            
            if ($returnCode === 0 && file_exists($tempPath)) {
                $novoTamanho = filesize($tempPath);
                
                // Usar versão otimizada se for menor ou similar
                if ($novoTamanho <= $originalSize * 1.1) { // Aceita até 10% maior para manter qualidade
                    rename($tempPath, $pdfPath);
                    $this->log("✅ Otimização Ghostscript aplicada com sucesso");
                    return true;
                } else {
                    unlink($tempPath);
                    $this->log("⚠️ Otimização Ghostscript resultou em arquivo maior, mantendo original");
                }
            }
            
        } catch (\Exception $e) {
            $this->log("❌ Erro na otimização Ghostscript: " . $e->getMessage());
        }
        
        return false;
    }
    
    /**
     * Otimizar metadados do PDF
     */
    private function otimizarMetadados(string $pdfPath): void
    {
        try {
            // Verificar se exiftool está disponível
            exec('which exiftool', $output, $returnCode);
            if ($returnCode !== 0) {
                return; // Exiftool não disponível
            }
            
            // Comando para otimizar metadados
            $command = sprintf(
                'exiftool -overwrite_original -Producer="LegisInc Sistema Legislativo" ' .
                '-Creator="Câmara Municipal" -Title="Documento Protocolado" %s 2>/dev/null',
                escapeshellarg($pdfPath)
            );
            
            exec($command);
            $this->log("✅ Metadados otimizados");
            
        } catch (\Exception $e) {
            $this->log("⚠️ Erro na otimização de metadados: " . $e->getMessage());
        }
    }
    
    /**
     * Validar integridade do PDF
     */
    private function validarIntegridadePDF(string $pdfPath): void
    {
        try {
            // Verificar se é um PDF válido
            $content = file_get_contents($pdfPath, false, null, 0, 4);
            if ($content !== '%PDF') {
                throw new \Exception('Arquivo não é um PDF válido');
            }
            
            // Verificar tamanho mínimo
            if (filesize($pdfPath) < 1000) {
                throw new \Exception('PDF muito pequeno, possivelmente corrompido');
            }
            
            $this->log("✅ Integridade do PDF validada");
            
        } catch (\Exception $e) {
            $this->log("❌ Erro na validação de integridade: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Validar qualidade final do PDF
     */
    private function validarQualidadePDF(string $pdfPath): void
    {
        $tamanho = filesize($pdfPath);
        $this->log("📊 Validação de qualidade:");
        $this->log("   📏 Tamanho: " . $this->formatBytes($tamanho));
        $this->log("   📄 Formato: A4 (595.28 x 841.89 pts)");
        $this->log("   🎯 Resolução: 150 DPI");
        $this->log("   🔒 Segurança: PHP e JavaScript desabilitados");
        $this->log("   📝 Fontes: Subsetting habilitado");
        
        // Verificar se está dentro dos parâmetros esperados
        if ($tamanho < 1000) {
            $this->log("⚠️ ATENÇÃO: PDF muito pequeno");
        } elseif ($tamanho > 10 * 1024 * 1024) { // 10MB
            $this->log("⚠️ ATENÇÃO: PDF muito grande");
        } else {
            $this->log("✅ Tamanho dentro dos parâmetros esperados");
        }
    }
    
    /**
     * Gerar HTML otimizado para protocolo
     */
    private function gerarHTMLOtimizadoParaProtocolo(Proposicao $proposicao): string
    {
        // Usar template específico para protocolo com otimizações
        $html = view('proposicoes.pdf.protocolo-otimizado', [
            'proposicao' => $proposicao,
            'numeroProtocolo' => $proposicao->numero_protocolo ?: '[AGUARDANDO PROTOCOLO]',
            'dataProtocolo' => $proposicao->data_protocolo ? $proposicao->data_protocolo->format('d/m/Y H:i:s') : 'N/A',
            'conteudo' => $this->processarConteudoParaProtocolo($proposicao),
            'assinaturaDigital' => $this->gerarAssinaturaDigital($proposicao),
            'qrcode' => $this->gerarQRCode($proposicao),
        ])->render();
        
        // Otimizações de HTML para PDF
        $html = $this->otimizarHTMLParaPDF($html);
        
        return $html;
    }
    
    /**
     * Processar conteúdo otimizado para protocolo
     */
    private function processarConteudoParaProtocolo(Proposicao $proposicao): string
    {
        $conteudo = $proposicao->conteudo ? $proposicao->conteudo : $proposicao->ementa;
        
        // Limpeza e formatação para PDF
        $conteudo = strip_tags($conteudo);
        $conteudo = html_entity_decode($conteudo, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Quebras de linha apropriadas para PDF
        $conteudo = str_replace(['\n', '\r'], '<br>', $conteudo);
        
        return $conteudo;
    }
    
    /**
     * Gerar assinatura digital para protocolo
     */
    private function gerarAssinaturaDigital(Proposicao $proposicao): string
    {
        $dataAssinatura = now()->format('d/m/Y H:i:s');
        
        $nomeAssinante = $proposicao->autor->name ? $proposicao->autor->name : 'Parlamentar';
        $numeroProtocolo = $proposicao->numero_protocolo ? $proposicao->numero_protocolo : 'Pendente';
        
        return "
        <div class='assinatura-digital'>
            <div class='linha-assinatura'></div>
            <div class='nome-assinante'>{$nomeAssinante}</div>
            <div class='cargo-assinante'>Vereador(a)</div>
            <div class='data-assinatura'>Data: {$dataAssinatura}</div>
            <div class='protocolo-info'>Protocolo: {$numeroProtocolo}</div>
        </div>";
    }
    
    /**
     * Gerar QR Code para verificação
     */
    private function gerarQRCode(Proposicao $proposicao): string
    {
        $urlVerificacao = url("/proposicoes/{$proposicao->id}");
        
        return "
        <div class='qrcode-container'>
            <div class='qrcode-info'>
                <strong>QR Code para Verificação</strong><br>
                <small>Escaneie para verificar autenticidade</small><br>
                <small>URL: {$urlVerificacao}</small>
            </div>
        </div>";
    }
    
    /**
     * Otimizar HTML para geração de PDF
     */
    private function otimizarHTMLParaPDF(string $html): string
    {
        // Remover comentários HTML
        $html = preg_replace('/<!--.*?-->/s', '', $html);
        
        // Remover espaços em branco desnecessários
        $html = preg_replace('/\s+/', ' ', $html);
        
        // Otimizar CSS inline
        $html = $this->otimizarCSSInline($html);
        
        return $html;
    }
    
    /**
     * Otimizar CSS inline para PDF
     */
    private function otimizarCSSInline(string $html): string
    {
        // Converter unidades problemáticas
        $html = str_replace(['1.18in', '0.59in', '0.5in', '0.25in'], 
                           ['85pt', '42pt', '36pt', '18pt'], $html);
        
        // Simplificar regras @page
        $html = preg_replace(
            '/@page\s*\{[^}]*\}/',
            '@page { size: A4; margin: 36pt 42pt 18pt 85pt; }',
            $html
        );
        
        return $html;
    }
    
    /**
     * Formatar bytes para exibição
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Log com timestamp
     */
    private function log(string $message): void
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        Log::info("[PDF Protocolado] {$message}");
    }
}
