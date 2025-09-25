<?php

namespace App\Services;

use App\Models\Proposicao;
use Illuminate\Support\Facades\Log;

class TemplateVariableService
{
    /**
     * Replace template variables in text content
     *
     * Supported variables:
     * [NUMERO] - Proposition number
     * [ANO] - Proposition year
     * [PROTOCOLO] - Protocol number
     * [DATA_HORA] - Protocol date/time
     * [NOME_COMPLETO] - Signer full name
     * [CODIGO_VALIDACAO] - Validation code
     * [ASSINATURA_DIGITAL] - Digital signature block (visual representation)
     * [ASSINATURA_PARLAMENTAR] - Parliamentarian signature placeholder
     * [QRCODE_VALIDACAO] - QR Code for validation
     * [CARIMBO_ASSINATURA] - Complete signature stamp with QR code
     *
     * @param string $content Content with template variables
     * @param Proposicao $proposicao Proposition model
     * @param array $signatureData Optional signature data for signer info
     * @return string Content with variables replaced
     */
    public function replaceVariables(string $content, Proposicao $proposicao, array $signatureData = []): string
    {
        Log::info('🔄 TemplateVariable: Substituindo variáveis de template', [
            'proposicao_id' => $proposicao->id,
            'variables_found' => $this->findVariables($content)
        ]);

        // Build replacement map
        $replacements = $this->buildReplacementMap($proposicao, $signatureData);

        // Replace all variables
        $processedContent = $content;
        foreach ($replacements as $variable => $value) {
            $processedContent = str_replace("[{$variable}]", $value, $processedContent);
        }

        // Log replacements made
        $variablesReplaced = array_keys(array_filter($replacements, function($value, $key) use ($content) {
            return strpos($content, "[{$key}]") !== false;
        }, ARRAY_FILTER_USE_BOTH));

        Log::info('✅ TemplateVariable: Variáveis substituídas', [
            'proposicao_id' => $proposicao->id,
            'variables_replaced' => $variablesReplaced,
            'content_length_before' => strlen($content),
            'content_length_after' => strlen($processedContent)
        ]);

        return $processedContent;
    }

    /**
     * Build replacement map for all supported variables
     */
    private function buildReplacementMap(Proposicao $proposicao, array $signatureData = []): array
    {
        $validationCode = $proposicao->codigo_validacao ?? $this->generateValidationCode();

        // Save validation code if not exists
        if (!$proposicao->codigo_validacao) {
            $proposicao->update(['codigo_validacao' => $validationCode]);
        }

        return [
            'NUMERO' => $proposicao->numero ?? 'N/A',
            'ANO' => $proposicao->ano ?? date('Y'),
            'PROTOCOLO' => $proposicao->numero_protocolo ?? 'PENDENTE',
            'DATA_HORA' => $proposicao->data_protocolo
                ? $proposicao->data_protocolo->format('d/m/Y H:i')
                : 'PENDENTE',
            'NOME_COMPLETO' => $signatureData['nome_assinante'] ?? $signatureData['signer_name'] ?? 'N/A',
            'CODIGO_VALIDACAO' => $validationCode,
            'ASSINATURA_DIGITAL' => $this->generateDigitalSignatureBlock($proposicao, $signatureData),
            'ASSINATURA_PARLAMENTAR' => $this->generateParlamentarianSignaturePlaceholder($proposicao, $signatureData),
            'QRCODE_VALIDACAO' => $this->generateQRCodePlaceholder($proposicao, $validationCode),
            'CARIMBO_ASSINATURA' => $this->generateCompleteSignatureStamp($proposicao, $signatureData, $validationCode)
        ];
    }

    /**
     * Generate digital signature visual block for document template
     */
    private function generateDigitalSignatureBlock(Proposicao $proposicao, array $signatureData = []): string
    {
        $signerName = $signatureData['nome_assinante'] ?? $signatureData['signer_name'] ?? 'N/A';
        $signatureDate = isset($signatureData['signature_timestamp'])
            ? date('d/m/Y H:i:s', strtotime($signatureData['signature_timestamp']))
            : now()->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i:s');

        $validationCode = $proposicao->codigo_validacao ?? $this->generateValidationCode();

        // Save validation code if not exists
        if (!$proposicao->codigo_validacao) {
            $proposicao->update(['codigo_validacao' => $validationCode]);
        }

        // Generate signature block template
        $signatureBlock = "
═══════════════════════════════════════════════
                ASSINADO DIGITALMENTE
═══════════════════════════════════════════════

Assinante: {$signerName}
Data/Hora: {$signatureDate} (UTC-3)
Documento: INDICAÇÃO Nº {$proposicao->numero}/{$proposicao->ano}

Código de Validação: {$validationCode}

Para verificar a autenticidade deste documento,
acesse: https://sistema.camaracaragua.sp.gov.br/conferir_assinatura
e informe o código de validação acima.

Este documento foi assinado digitalmente de acordo
com a legislação vigente (MP 2.200-2/2001).

═══════════════════════════════════════════════
        ";

        return trim($signatureBlock);
    }

    /**
     * Find all variables in content
     */
    private function findVariables(string $content): array
    {
        preg_match_all('/\[([A-Z_]+)\]/', $content, $matches);
        return array_unique($matches[1]);
    }

    /**
     * Generate validation code in format A7CA-9537-1505-BD94
     */
    private function generateValidationCode(): string
    {
        $segments = [];
        for ($i = 0; $i < 4; $i++) {
            $segment = '';
            for ($j = 0; $j < 4; $j++) {
                if ($j < 2) {
                    // First 2 chars: letters A-F
                    $segment .= strtoupper(dechex(rand(10, 15))); // A-F
                } else {
                    // Last 2 chars: numbers
                    $segment .= rand(0, 9);
                }
            }
            $segments[] = $segment;
        }

        return implode('-', $segments);
    }

    /**
     * Generate placeholder for parliamentarian signature
     * This will be replaced with actual signature when document is signed
     */
    private function generateParlamentarianSignaturePlaceholder(Proposicao $proposicao, array $signatureData = []): string
    {
        // Check if document is already signed
        if ($proposicao->status === 'assinado' && !empty($proposicao->assinatura_digital)) {
            $signatureInfo = json_decode($proposicao->assinatura_digital, true);
            $signerName = $signatureInfo['nome'] ?? $signatureData['nome_assinante'] ?? 'N/A';
            $signatureDate = $signatureInfo['data'] ?? now()->format('d/m/Y H:i');

            return "
╔════════════════════════════════════════════════════════╗
║         ASSINADO DIGITALMENTE POR                     ║
║                                                        ║
║  {$signerName}                                        ║
║  {$signatureDate}                                     ║
║                                                        ║
║  Este documento foi assinado digitalmente usando      ║
║  certificado digital ICP-Brasil                       ║
╚════════════════════════════════════════════════════════╝
            ";
        }

        // Return placeholder for unsigned document
        $autorName = isset($proposicao->autor) ? $proposicao->autor->name : 'N/A';

        return "
_______________________________________
[ASSINATURA DO PARLAMENTAR]

Nome: {$autorName}
Cargo: Vereador(a)
Data: ___/___/_____
        ";
    }

    /**
     * Generate QR Code placeholder for validation
     */
    private function generateQRCodePlaceholder(Proposicao $proposicao, string $validationCode): string
    {
        $verificationUrl = route('proposicoes.verificar-assinatura', $proposicao->pades_verification_uuid ?? $validationCode);

        return "
┌─────────────────────────┐
│                         │
│      [QR CODE]          │
│                         │
│  Escaneie para validar  │
│                         │
└─────────────────────────┘

Código de Validação: {$validationCode}
URL: {$verificationUrl}
        ";
    }

    /**
     * Generate complete signature stamp with all elements
     */
    private function generateCompleteSignatureStamp(Proposicao $proposicao, array $signatureData = [], string $validationCode = null): string
    {
        $validationCode = $validationCode ?? $proposicao->codigo_validacao ?? $this->generateValidationCode();
        $signerName = $signatureData['nome_assinante'] ?? $signatureData['signer_name'] ?? $proposicao->autor->name ?? 'N/A';
        $signatureDate = now()->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i:s');
        $protocolNumber = $proposicao->numero_protocolo ?? 'PENDENTE';

        // Generate complete stamp with signature area and QR code
        return "
╔═══════════════════════════════════════════════════════════════════════════╗
║                     CARIMBO DE ASSINATURA DIGITAL                        ║
╠═══════════════════════════════════════════════════════════════════════════╣
║                                                                           ║
║  INDICAÇÃO Nº {$proposicao->numero}/{$proposicao->ano}                  ║
║  Protocolo: {$protocolNumber}                                           ║
║                                                                           ║
║  ┌────────────────────────────────────────┬─────────────────────┐      ║
║  │ ASSINANTE:                             │    [QR CODE]        │      ║
║  │                                         │                     │      ║
║  │ {$signerName}                          │    Escaneie para    │      ║
║  │ Vereador(a)                            │    validar          │      ║
║  │                                         │                     │      ║
║  │ DATA/HORA:                              │                     │      ║
║  │ {$signatureDate} UTC-3                  │                     │      ║
║  │                                         └─────────────────────┘      ║
║  │ CÓDIGO DE VALIDAÇÃO:                                                 ║
║  │ {$validationCode}                                                    ║
║  │                                                                       ║
║  │ Este documento foi assinado digitalmente de acordo com a             ║
║  │ Medida Provisória nº 2.200-2/2001 (ICP-Brasil)                      ║
║  └───────────────────────────────────────────────────────────────────┘   ║
║                                                                           ║
║  Para validar este documento, acesse:                                    ║
║  https://sistema.camaracaragua.sp.gov.br/conferir_assinatura            ║
║  e informe o código de validação acima.                                  ║
║                                                                           ║
╚═══════════════════════════════════════════════════════════════════════════╝
        ";
    }

    /**
     * Process RTF content and replace variables
     * Special handling for RTF format
     */
    public function replaceVariablesInRtf(string $rtfContent, Proposicao $proposicao, array $signatureData = []): string
    {
        Log::info('📄 TemplateVariable: Processando variáveis em RTF', [
            'proposicao_id' => $proposicao->id,
            'rtf_size_bytes' => strlen($rtfContent)
        ]);

        // First, extract plain text content from RTF
        $plainContent = $this->extractPlainTextFromRtf($rtfContent);

        // Find variables in plain content
        $variables = $this->findVariables($plainContent);

        if (empty($variables)) {
            return $rtfContent;
        }

        // Build replacements
        $replacements = $this->buildReplacementMap($proposicao, $signatureData);

        // Replace variables in RTF content (handle RTF encoding)
        $processedRtf = $rtfContent;
        foreach ($replacements as $variable => $value) {
            // Handle RTF special characters
            $rtfValue = $this->convertToRtfText($value);
            $processedRtf = str_replace("[{$variable}]", $rtfValue, $processedRtf);
        }

        Log::info('✅ TemplateVariable: RTF processado', [
            'variables_replaced' => array_intersect($variables, array_keys($replacements)),
            'rtf_size_after' => strlen($processedRtf)
        ]);

        return $processedRtf;
    }

    /**
     * Extract plain text from RTF (simplified)
     */
    private function extractPlainTextFromRtf(string $rtfContent): string
    {
        // Remove RTF control words and formatting
        $plainText = preg_replace('/\{[^}]*\}/', '', $rtfContent);
        $plainText = preg_replace('/\\\\[a-zA-Z0-9]+/', '', $plainText);
        return $plainText;
    }

    /**
     * Convert text to RTF-safe format
     */
    private function convertToRtfText(string $text): string
    {
        // Escape RTF special characters
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace('{', '\\{', $text);
        $text = str_replace('}', '\\}', $text);

        // Convert line breaks to RTF format
        $text = str_replace("\n", '\\line ', $text);
        $text = str_replace("\r", '', $text);

        return $text;
    }
}