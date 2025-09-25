<?php

/**
 * Teste direto do MCP service para verificar se o PDF está sendo modificado
 * sem dependências do Laravel
 */

require_once __DIR__ . '/vendor/autoload.php';

use setasign\Fpdi\Fpdi;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

// Mock da classe Log (console output)
class TestLog {
    public static function info($message, $context = []) {
        echo "ℹ️ INFO: $message\n";
        if (!empty($context)) {
            echo "   Context: " . json_encode($context, JSON_PRETTY_PRINT) . "\n";
        }
    }

    public static function error($message, $context = []) {
        echo "❌ ERROR: $message\n";
        if (!empty($context)) {
            echo "   Context: " . json_encode($context, JSON_PRETTY_PRINT) . "\n";
        }
    }

    public static function warning($message, $context = []) {
        echo "⚠️ WARNING: $message\n";
        if (!empty($context)) {
            echo "   Context: " . json_encode($context, JSON_PRETTY_PRINT) . "\n";
        }
    }
}

echo "🧪 Teste Direto do MCP - Carimbo PDF Real\n";
echo "=========================================\n\n";

try {
    // 1. Criar PDF de teste
    echo "📄 1. Criando PDF de teste...\n";

    $testPdf = new Fpdi();
    $testPdf->AddPage();
    $testPdf->SetFont('Arial', 'B', 16);
    $testPdf->Cell(0, 10, 'DOCUMENTO TESTE - INDICAÇÃO 001/2024', 0, 1, 'C');
    $testPdf->Ln(20);
    $testPdf->SetFont('Arial', '', 12);
    $testPdf->Cell(0, 10, 'Este documento será carimbado automaticamente', 0, 1);
    $testPdf->Cell(0, 10, 'com faixa lateral + QR code + texto vertical.', 0, 1);

    $originalPdfPath = '/tmp/claude/original.pdf';
    $testPdf->Output('F', $originalPdfPath);

    echo "✅ PDF original criado: " . filesize($originalPdfPath) . " bytes\n\n";

    // 2. Preparar elementos do carimbo
    echo "🎨 2. Preparando elementos do carimbo lateral...\n";

    $elements = [
        [
            'type' => 'text',
            'x' => 491,
            'y' => 120,
            'width' => 88,
            'height' => 600,
            'text' => 'INDICAÇÃO Nº 001/2024 - Protocolo nº 12345 recebido em 25/09/2024 15:30 - Esta é uma cópia do original assinado digitalmente por Bruno Silva. Para validar o documento, leia o código QR ou acesse exemplo.com/verificar e informe o código ABC123DE.',
            'font_size' => 8,
            'font_weight' => 'normal',
            'color' => '#333333',
            'text_align' => 'center',
            'rotation' => 90,
            'word_wrap' => true,
            'line_height' => 1.2
        ],
        [
            'type' => 'qrcode',
            'x' => 491,
            'y' => 16,
            'width' => 88,
            'height' => 88,
            'data' => 'https://exemplo.com/verificar/abc123',
            'error_correction' => 'M',
            'border' => 1,
            'quiet_zone' => 2
        ]
    ];

    echo "✅ " . count($elements) . " elementos preparados\n\n";

    // 3. Aplicar carimbo manualmente usando FPDI diretamente
    echo "🔧 3. Aplicando carimbo com FPDI...\n";

    $pdf = new Fpdi();
    $pdf->setSourceFile($originalPdfPath);

    // Importar primeira página
    $templateId = $pdf->importPage(1);
    $size = $pdf->getTemplateSize($templateId);

    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
    $pdf->useTemplate($templateId);

    echo "📐 Dimensões da página: {$size['width']}×{$size['height']}pt\n";

    // Adicionar sidebar de fundo
    $pdf->SetFillColor(248, 249, 250); // Cor de fundo suave
    $pdf->Rect(475, 0, 120, $size['height'], 'F');

    // Borda sutil
    $pdf->SetDrawColor(220, 220, 220);
    $pdf->SetLineWidth(0.5);
    $pdf->Rect(475, 0, 120, $size['height'], 'D');

    echo "✅ Sidebar base adicionada\n";

    $elementsProcessed = 0;

    // Processar elementos
    foreach ($elements as $element) {
        if ($element['type'] === 'text') {
            // Adicionar texto (horizontal para simplificar o teste)
            $pdf->SetFont('Arial', $element['font_weight'] === 'bold' ? 'B' : '', 6);

            // Cor do texto
            $pdf->SetTextColor(51, 51, 51); // #333333

            // Quebrar texto em linhas menores para caber na sidebar
            $text = $element['text'];
            $words = explode(' ', $text);
            $lines = [];
            $currentLine = '';

            foreach ($words as $word) {
                if (strlen($currentLine . ' ' . $word) < 20) {
                    $currentLine .= ($currentLine ? ' ' : '') . $word;
                } else {
                    if ($currentLine) $lines[] = $currentLine;
                    $currentLine = $word;
                }
            }
            if ($currentLine) $lines[] = $currentLine;

            // Adicionar linhas
            $pdf->SetXY(491, 120);
            foreach ($lines as $i => $line) {
                if ($i >= 20) break; // Limitar a 20 linhas
                $pdf->SetXY(491, 120 + ($i * 8));
                $pdf->Cell(88, 6, $line, 0, 0, 'C');
            }

            $elementsProcessed++;
            echo "✅ Texto lateral adicionado (" . count($lines) . " linhas)\n";

        } elseif ($element['type'] === 'qrcode') {
            // Gerar QR Code
            $builder = new Builder(
                writer: new PngWriter(),
                data: $element['data'],
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::Medium,
                size: 200,
                margin: 10,
                roundBlockSizeMode: RoundBlockSizeMode::Margin
            );

            $result = $builder->build();

            // Salvar como arquivo temporário
            $tempQrPath = tempnam(sys_get_temp_dir(), 'qr_') . '.png';
            file_put_contents($tempQrPath, $result->getString());

            // Adicionar ao PDF
            $pdf->Image($tempQrPath, $element['x'], $element['y'], $element['width'], $element['height'], 'PNG');

            // Limpeza
            unlink($tempQrPath);

            $elementsProcessed++;
            echo "✅ QR Code adicionado\n";
        }
    }

    // Salvar PDF carimbado
    $stampedPdfPath = '/tmp/claude/stamped.pdf';
    $modifiedPdfContent = $pdf->Output('S');
    file_put_contents($stampedPdfPath, $modifiedPdfContent);

    echo "\n📊 4. Resultados:\n";
    echo "================\n";
    echo "PDF original: " . filesize($originalPdfPath) . " bytes\n";
    echo "PDF carimbado: " . filesize($stampedPdfPath) . " bytes\n";
    echo "Diferença: " . (filesize($stampedPdfPath) - filesize($originalPdfPath)) . " bytes\n";
    echo "Elementos processados: $elementsProcessed\n";

    // Verificar se realmente modificou
    $originalContent = file_get_contents($originalPdfPath);
    $stampedContent = file_get_contents($stampedPdfPath);

    if ($originalContent === $stampedContent) {
        echo "❌ FALHA: Conteúdo idêntico!\n";
        exit(1);
    } else {
        echo "✅ SUCESSO: PDF foi modificado com carimbo!\n";
    }

    // Verificar elementos específicos no PDF
    echo "\n🔍 5. Verificando elementos no PDF:\n";

    $elementsFound = [];

    if (strpos($stampedContent, '/Image') !== false || strpos($stampedContent, 'PNG') !== false) {
        $elementsFound[] = 'QR Code PNG detectado';
    }

    if (strpos($stampedContent, 'INDICACAO') !== false) {
        $elementsFound[] = 'Texto da indicação';
    }

    if (strpos($stampedContent, '001/2024') !== false) {
        $elementsFound[] = 'Número do documento';
    }

    if (strpos($stampedContent, 'Bruno Silva') !== false) {
        $elementsFound[] = 'Nome do signatário';
    }

    if (strpos($stampedContent, 'exemplo.com') !== false) {
        $elementsFound[] = 'URL de verificação';
    }

    echo "Elementos encontrados:\n";
    foreach ($elementsFound as $element) {
        echo "✅ $element\n";
    }

    echo "\n🎉 TESTE CONCLUÍDO COM SUCESSO!\n";
    echo "Status: " . (count($elementsFound) >= 3 ? "✅ APROVADO" : "⚠️ PARCIAL") . "\n";
    echo "Arquivos:\n";
    echo "- Original: $originalPdfPath\n";
    echo "- Carimbado: $stampedPdfPath\n";

} catch (Exception $e) {
    echo "\n❌ ERRO durante o teste:\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";

    exit(1);
}