<?php

namespace App\Services;

use App\Models\Proposicao;
// QR Code functionality will be implemented later
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AssinaturaValidacaoService
{
    /**
     * Generate a unique validation code for signature verification
     */
    public function gerarCodigoValidacao(Proposicao $proposicao): string
    {
        // Format: XXXX-XXXX-XXXX-XXXX (16 characters in groups of 4)
        $codigo = strtoupper(Str::random(4)) . '-' . 
                  strtoupper(Str::random(4)) . '-' . 
                  strtoupper(Str::random(4)) . '-' . 
                  strtoupper(Str::random(4));
        
        // Ensure uniqueness
        while (Proposicao::where('codigo_validacao', $codigo)->exists()) {
            $codigo = strtoupper(Str::random(4)) . '-' . 
                      strtoupper(Str::random(4)) . '-' . 
                      strtoupper(Str::random(4)) . '-' . 
                      strtoupper(Str::random(4));
        }
        
        return $codigo;
    }

    /**
     * Generate validation URL for signature verification
     */
    public function gerarUrlValidacao(string $codigoValidacao): string
    {
        $baseUrl = config('app.url', 'https://sistema.camaracaragua.sp.gov.br');
        return $baseUrl . '/conferir_assinatura?codigo=' . $codigoValidacao;
    }

    /**
     * Generate QR code for signature validation
     */
    public function gerarQRCodeValidacao(string $urlValidacao): string
    {
        try {
            // For now, return a simple placeholder base64 QR code
            // This can be replaced with actual QR code generation later
            $qrCodePlaceholder = $this->gerarQRCodePlaceholder($urlValidacao);
            return $qrCodePlaceholder;
            
        } catch (\Exception $e) {
            Log::error('Erro ao gerar QR Code de validação', [
                'url' => $urlValidacao,
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }

    /**
     * Generate a simple placeholder QR code (can be replaced with real QR code library later)
     */
    private function gerarQRCodePlaceholder(string $url): string
    {
        // Create a simple 200x200 PNG placeholder
        $width = 200;
        $height = 200;
        
        $image = imagecreate($width, $height);
        
        // Colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $gray = imagecolorallocate($image, 128, 128, 128);
        
        // Fill background
        imagefill($image, 0, 0, $white);
        
        // Draw border
        imagerectangle($image, 0, 0, $width-1, $height-1, $black);
        
        // Draw simple pattern to simulate QR code
        for ($i = 10; $i < $width-10; $i += 10) {
            for ($j = 10; $j < $height-10; $j += 10) {
                if (($i + $j) % 20 == 0) {
                    imagefilledrectangle($image, $i, $j, $i+8, $j+8, $black);
                }
            }
        }
        
        // Add text
        $text = "QR CODE";
        $font_size = 3;
        $text_width = strlen($text) * imagefontwidth($font_size);
        $text_height = imagefontheight($font_size);
        $x = ($width - $text_width) / 2;
        $y = ($height - $text_height) / 2;
        
        // White background for text
        imagefilledrectangle($image, $x-5, $y-2, $x+$text_width+5, $y+$text_height+2, $white);
        imagestring($image, $font_size, $x, $y, $text, $black);
        
        // Convert to base64
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);
        
        return base64_encode($imageData);
    }

    /**
     * Generate signature data for validation storage
     */
    public function gerarDadosAssinaturaValidacao(Proposicao $proposicao, array $dadosAssinatura = []): array
    {
        return [
            'proposicao_id' => $proposicao->id,
            'tipo_proposicao' => $proposicao->tipo,
            'numero_proposicao' => $proposicao->numero_protocolo ?: '[AGUARDANDO PROTOCOLO]',
            'ementa' => $proposicao->ementa,
            'autor_nome' => $proposicao->autor->nome_politico ?? $proposicao->autor->name,
            'data_assinatura' => $proposicao->data_assinatura ? $proposicao->data_assinatura->format('d/m/Y H:i:s') : now()->format('d/m/Y H:i:s'),
            'ip_assinatura' => $proposicao->ip_assinatura ?? request()->ip(),
            'dados_assinatura_original' => $dadosAssinatura,
            'hash_conteudo' => hash('sha256', $proposicao->conteudo . $proposicao->ementa),
            'timestamp_validacao' => now()->timestamp,
        ];
    }

    /**
     * Generate standardized signature text for PDF
     */
    public function gerarTextoAssinaturaPadrao(Proposicao $proposicao): string
    {
        $tipoCompleto = $this->obterTipoCompleto($proposicao->tipo);
        $numeroProposicao = $this->obterNumeroProposicao($proposicao);
        $numeroProtocolo = $this->obterNumeroProtocolo($proposicao);
        $dataRecebimento = $this->obterDataRecebimento($proposicao);
        $nomeAssinante = $this->obterNomeAssinante($proposicao);
        $codigoValidacao = $proposicao->codigo_validacao;
        
        $texto = "{$tipoCompleto} Nº {$numeroProposicao}";
        
        if ($numeroProtocolo) {
            $texto .= " - Protocolo nº {$numeroProtocolo} recebido em {$dataRecebimento}";
        }
        
        $texto .= " - Esta é uma cópia do original assinado digitalmente por {$nomeAssinante}";
        $texto .= "\nPara validar o documento, leia o código QR ou acesse https://sistema.camaracaragua.sp.gov.br/conferir_assinatura e informe o código {$codigoValidacao}";
        
        return $texto;
    }

    /**
     * Process signature validation for a proposicao
     */
    public function processarValidacaoAssinatura(Proposicao $proposicao, array $dadosAssinatura = []): array
    {
        // Generate validation code
        $codigoValidacao = $this->gerarCodigoValidacao($proposicao);
        
        // Generate validation URL
        $urlValidacao = $this->gerarUrlValidacao($codigoValidacao);
        
        // Generate QR code
        $qrCodeBase64 = $this->gerarQRCodeValidacao($urlValidacao);
        
        // Generate validation data
        $dadosValidacao = $this->gerarDadosAssinaturaValidacao($proposicao, $dadosAssinatura);
        
        // Update proposicao with validation data
        $proposicao->update([
            'codigo_validacao' => $codigoValidacao,
            'url_validacao' => $urlValidacao,
            'qr_code_validacao' => $qrCodeBase64,
            'dados_assinatura_validacao' => $dadosValidacao
        ]);

        return [
            'codigo_validacao' => $codigoValidacao,
            'url_validacao' => $urlValidacao,
            'qr_code_base64' => $qrCodeBase64,
            'texto_assinatura' => $this->gerarTextoAssinaturaPadrao($proposicao->fresh()),
            'dados_validacao' => $dadosValidacao
        ];
    }

    /**
     * Validate signature by code
     */
    public function validarAssinaturaPorCodigo(string $codigoValidacao): ?array
    {
        $proposicao = Proposicao::where('codigo_validacao', $codigoValidacao)
            ->with('autor')
            ->first();

        if (!$proposicao || !$proposicao->assinatura_digital) {
            return null;
        }

        return [
            'valida' => true,
            'proposicao' => [
                'id' => $proposicao->id,
                'tipo' => $this->obterTipoCompleto($proposicao->tipo),
                'numero' => $this->obterNumeroProposicao($proposicao),
                'ementa' => $proposicao->ementa,
                'autor' => $this->obterNomeAssinante($proposicao),
                'data_assinatura' => $proposicao->data_assinatura?->format('d/m/Y H:i:s'),
                'numero_protocolo' => $this->obterNumeroProtocolo($proposicao),
                'data_protocolo' => $proposicao->data_protocolo?->format('d/m/Y H:i:s'),
            ],
            'dados_validacao' => $proposicao->dados_assinatura_validacao,
            'verificado_em' => now()->format('d/m/Y H:i:s')
        ];
    }

    /**
     * Helper methods
     */
    private function obterTipoCompleto(string $tipo): string
    {
        $tipos = [
            'mocao' => 'MOÇÃO',
            'indicacao' => 'INDICAÇÃO', 
            'requerimento' => 'REQUERIMENTO',
            'projeto_lei' => 'PROJETO DE LEI',
            'projeto_resolucao' => 'PROJETO DE RESOLUÇÃO'
        ];
        
        return $tipos[$tipo] ?? strtoupper($tipo);
    }

    private function obterNumeroProposicao(Proposicao $proposicao): string
    {
        return $proposicao->numero_protocolo ?: '[AGUARDANDO PROTOCOLO]';
    }

    private function obterNumeroProtocolo(Proposicao $proposicao): ?string
    {
        return $proposicao->numero_protocolo;
    }

    private function obterDataRecebimento(Proposicao $proposicao): string
    {
        $data = $proposicao->data_protocolo ?: $proposicao->data_assinatura ?: now();
        return $data->format('d/m/Y H:i:s');
    }

    private function obterNomeAssinante(Proposicao $proposicao): string
    {
        return $proposicao->autor->nome_politico ?? $proposicao->autor->name ?? 'Autor não identificado';
    }
}