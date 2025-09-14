<?php

namespace App\Services\Template;

use App\Models\Proposicao;
use App\Services\Parametro\ParametroService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssinaturaQRService
{
    protected ParametroService $parametroService;

    public function __construct(ParametroService $parametroService)
    {
        $this->parametroService = $parametroService;
    }

    /**
     * Gera o QR Code para uma proposição
     */
    public function gerarQRCode(Proposicao $proposicao): ?string
    {
        try {
            // Verificar se QR Code deve ser exibido apenas após protocolo
            $apenasProtocolo = $this->parametroService->obterValor('Templates', 'Assinatura e QR Code', 'qrcode_apenas_protocolo');

            if ($apenasProtocolo && ! $proposicao->numero_protocolo) {
                return null;
            }

            // Obter configurações do QR Code
            $urlFormato = $this->parametroService->obterValor('Templates', 'Assinatura e QR Code', 'qrcode_url_formato');
            $tamanho = $this->parametroService->obterValor('Templates', 'Assinatura e QR Code', 'qrcode_tamanho') ?: 100;

            // Construir URL
            $url = $this->construirURL($proposicao, $urlFormato);

            if (! $url) {
                return null;
            }

            // Gerar QR Code
            $qrCodeSvg = QrCode::size($tamanho)
                ->format('svg')
                ->generate($url);

            // Salvar QR Code como arquivo SVG
            $fileName = "qrcode_proposicao_{$proposicao->id}.svg";
            $filePath = "qrcodes/{$fileName}";

            Storage::disk('public')->put($filePath, $qrCodeSvg);

            return $filePath;

        } catch (\Exception $e) {
            Log::error('Erro ao gerar QR Code', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Gera o HTML da assinatura digital para uma proposição
     */
    public function gerarHTMLAssinatura(Proposicao $proposicao): ?string
    {
        try {
            // Verificar se assinatura deve ser exibida apenas após protocolo
            $apenasProtocolo = $this->parametroService->obterValor('Templates', 'Assinatura e QR Code', 'assinatura_apenas_protocolo');

            if ($apenasProtocolo && ! $proposicao->numero_protocolo) {
                return null;
            }

            // Verificar se proposição tem assinatura digital
            if (! $proposicao->assinatura_digital || ! $proposicao->data_assinatura) {
                return null;
            }

            // Obter texto da assinatura
            $textoAssinatura = $this->parametroService->obterValor('Templates', 'Assinatura e QR Code', 'assinatura_texto');

            // Substituir variáveis no texto
            $textoFinal = $this->substituirVariaveisAssinatura($proposicao, $textoAssinatura);

            // Gerar HTML da assinatura
            return $this->gerarHTMLAssinaturaFormatado($textoFinal);

        } catch (\Exception $e) {
            Log::error('Erro ao gerar HTML da assinatura', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Gera o HTML do QR Code para uma proposição
     */
    public function gerarHTMLQRCode(Proposicao $proposicao): ?string
    {
        try {
            $qrCodePath = $this->gerarQRCode($proposicao);

            if (! $qrCodePath) {
                return null;
            }

            // Obter texto do QR Code
            $textoQR = $this->parametroService->obterValor('Templates', 'Assinatura e QR Code', 'qrcode_texto');
            $textoFinal = $this->substituirVariaveisQR($proposicao, $textoQR);

            // Gerar HTML do QR Code
            return $this->gerarHTMLQRCodeFormatado($qrCodePath, $textoFinal);

        } catch (\Exception $e) {
            Log::error('Erro ao gerar HTML do QR Code', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Obter variáveis de posicionamento configuradas
     */
    public function obterConfiguracoesPosicionamento(): array
    {
        return [
            'assinatura_posicao' => $this->parametroService->obterValor('Templates', 'Assinatura e QR Code', 'assinatura_posicao'),
            'qrcode_posicao' => $this->parametroService->obterValor('Templates', 'Assinatura e QR Code', 'qrcode_posicao'),
            'assinatura_apenas_protocolo' => $this->parametroService->obterValor('Templates', 'Assinatura e QR Code', 'assinatura_apenas_protocolo'),
            'qrcode_apenas_protocolo' => $this->parametroService->obterValor('Templates', 'Assinatura e QR Code', 'qrcode_apenas_protocolo'),
        ];
    }

    /**
     * Construir URL para o QR Code
     */
    private function construirURL(Proposicao $proposicao, string $urlFormato): ?string
    {
        if (! $urlFormato) {
            return null;
        }

        $baseUrl = config('app.url');
        $numeroProtocolo = $proposicao->numero_protocolo;
        $numeroProposicao = $proposicao->id;

        $url = str_replace([
            '{base_url}',
            '{numero_protocolo}',
            '{numero_proposicao}',
        ], [
            $baseUrl,
            $numeroProtocolo,
            $numeroProposicao,
        ], $urlFormato);

        return $url;
    }

    /**
     * Substituir variáveis no texto da assinatura
     */
    private function substituirVariaveisAssinatura(Proposicao $proposicao, string $texto): string
    {
        $autor = $proposicao->autor;
        $dataAssinatura = $proposicao->data_assinatura ?
            $proposicao->data_assinatura->format('d/m/Y H:i:s') :
            'Data não disponível';

        return str_replace([
            '{autor_nome}',
            '{autor_cargo}',
            '{data_assinatura}',
        ], [
            $autor ? $autor->name : 'Autor não identificado',
            'Vereador',
            $dataAssinatura,
        ], $texto);
    }

    /**
     * Substituir variáveis no texto do QR Code
     */
    private function substituirVariaveisQR(Proposicao $proposicao, string $texto): string
    {
        return str_replace([
            '{numero_protocolo}',
            '{numero_proposicao}',
        ], [
            $proposicao->numero_protocolo ?: 'Aguardando protocolo',
            $proposicao->id,
        ], $texto);
    }

    /**
     * Gerar HTML formatado da assinatura na lateral direita
     */
    private function gerarHTMLAssinaturaFormatado(string $texto): string
    {
        // CORREÇÃO PDF: Remover position fixed que não funciona em PDF
        // Usar layout em bloco normal compatível com DomPDF
        return '<div class="assinatura-digital" style="width: 100%; border: 2px solid #28a745; padding: 15px; margin: 20px 0; background-color: #f0f8f0; border-radius: 8px; font-family: Arial, sans-serif; page-break-inside: avoid; text-align: center;">
            <h6 style="color: #28a745; margin: 0 0 10px 0; font-size: 14px; font-weight: bold;">🏆 ASSINATURA DIGITAL</h6>
            <div style="font-size: 12px; line-height: 1.5; color: #333; font-weight: bold;">'.nl2br($texto).'</div>
        </div>';
    }

    /**
     * Gerar HTML formatado do QR Code
     */
    private function gerarHTMLQRCodeFormatado(string $qrCodePath, string $texto): string
    {
        $qrCodeUrl = Storage::disk('public')->url($qrCodePath);

        return '<div class="qr-code-section" style="border: 1px solid #17a2b8; padding: 10px; margin: 10px 0; text-align: center; background-color: #f8f9fa;">
            <h6 style="color: #17a2b8; margin-bottom: 10px;"><i class="fas fa-qrcode"></i> Verificação Online</h6>
            <img src="'.$qrCodeUrl.'" alt="QR Code" style="margin-bottom: 5px;" />
            <div style="font-size: 11px; line-height: 1.3;">'.nl2br($texto).'</div>
        </div>';
    }

    /**
     * Gerar texto de assinatura visual para PDF no estilo padrão
     */
    public function gerarTextoAssinaturaVisual(Proposicao $proposicao): ?string
    {
        // Verificar se proposição tem assinatura digital
        if (!$proposicao->assinatura_digital || !$proposicao->data_assinatura) {
            return null;
        }

        $autor = $proposicao->autor;
        $nomeAutor = $autor ? $autor->name : 'Autor não identificado';

        // Obter informações da proposição
        $tipoFormatado = strtoupper($proposicao->tipo);
        $numeroProposicao = $proposicao->numero ?: '[AGUARDANDO PROTOCOLO]';
        $numeroProtocolo = $proposicao->numero_protocolo ?: 'Aguardando protocolo';

        // Formatar data da assinatura
        $dataAssinatura = $proposicao->data_assinatura->format('d/m/Y H:i:s');

        // Gerar código de verificação baseado na assinatura digital (primeiros 4 grupos de 4 dígitos)
        $hashAssinatura = strtoupper(substr(md5($proposicao->assinatura_digital), 0, 16));
        $codigoVerificacao = implode('-', str_split($hashAssinatura, 4));

        // Construir texto no formato solicitado
        $textoAssinatura = "{$tipoFormatado} Nº {$numeroProposicao} - Protocolo nº {$numeroProtocolo} recebido em {$dataAssinatura} - Esta é uma cópia do original assinado digitalmente por {$nomeAutor}\n";
        $textoAssinatura .= "Para validar o documento, leia o código QR ou acesse https://sistema.camaracaragua.sp.gov.br/conferir_assinatura e informe o código {$codigoVerificacao}";

        return $textoAssinatura;
    }

    /**
     * Gerar HTML da assinatura visual para PDF
     */
    public function gerarHTMLAssinaturaVisualPDF(Proposicao $proposicao): ?string
    {
        $textoAssinatura = $this->gerarTextoAssinaturaVisual($proposicao);

        if (!$textoAssinatura) {
            return null;
        }

        return '<div class="assinatura-visual-pdf" style="
            margin-top: 30px;
            padding: 15px;
            border: 1px solid #333;
            background-color: #f9f9f9;
            font-size: 10pt;
            line-height: 1.4;
            font-family: Arial, sans-serif;
            page-break-inside: avoid;
        ">
            <strong style="color: #0066cc;">📄 DOCUMENTO ASSINADO DIGITALMENTE</strong><br><br>
            ' . nl2br(htmlspecialchars($textoAssinatura)) . '
        </div>';
    }
}
