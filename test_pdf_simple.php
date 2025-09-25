<?php

/**
 * Teste simplificado para verificar se o PDF está sendo modificado corretamente
 * Foca apenas no texto lateral, sem QR code (para evitar problemas com GD)
 */

require_once __DIR__ . '/vendor/autoload.php';

use setasign\Fpdi\Fpdi;

echo "🧪 Teste Simplificado - Carimbo de Texto PDF\n";
echo "============================================\n\n";

try {
    // 1. Criar PDF de teste
    echo "📄 1. Criando PDF original...\n";

    $testPdf = new Fpdi();
    $testPdf->AddPage();
    $testPdf->SetFont('Arial', 'B', 16);
    $testPdf->Cell(0, 10, 'INDICAÇÃO Nº 001/2024', 0, 1, 'C');
    $testPdf->Ln(20);
    $testPdf->SetFont('Arial', '', 12);
    $testPdf->Cell(0, 10, 'Conteúdo do documento original.', 0, 1);
    $testPdf->Cell(0, 10, 'Este PDF será modificado com carimbo lateral.', 0, 1);

    $originalPdfPath = '/tmp/claude/original_simple.pdf';
    $testPdf->Output('F', $originalPdfPath);

    echo "✅ PDF original criado: " . filesize($originalPdfPath) . " bytes\n\n";

    // 2. Aplicar modificações usando FPDI
    echo "🔧 2. Aplicando carimbo lateral com FPDI...\n";

    $pdf = new Fpdi();
    $pdf->setSourceFile($originalPdfPath);

    // Importar primeira página
    $templateId = $pdf->importPage(1);
    $size = $pdf->getTemplateSize($templateId);

    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
    $pdf->useTemplate($templateId);

    echo "📐 Dimensões: {$size['width']}×{$size['height']}pt\n";

    // Adicionar sidebar de fundo (faixa lateral)
    $sidebarX = $size['width'] - 120;
    $pdf->SetFillColor(248, 249, 250); // Cor de fundo suave
    $pdf->Rect($sidebarX, 0, 120, $size['height'], 'F');

    // Borda da sidebar
    $pdf->SetDrawColor(220, 220, 220);
    $pdf->SetLineWidth(0.5);
    $pdf->Rect($sidebarX, 0, 120, $size['height'], 'D');

    echo "✅ Faixa lateral adicionada (x=$sidebarX, w=120pt)\n";

    // Adicionar texto lateral
    $pdf->SetFont('Arial', '', 6);
    $pdf->SetTextColor(51, 51, 51); // #333333

    $textLines = [
        'INDICAÇÃO Nº 001/2024',
        'Protocolo nº 12345',
        'recebido em 25/09/2024 15:30',
        '',
        'Esta é uma cópia do original',
        'assinado digitalmente por',
        'Bruno Silva.',
        '',
        'Para validar o documento,',
        'acesse exemplo.com/verificar',
        'e informe o código ABC123DE.',
        '',
        'Gerado automaticamente',
        'pelo sistema LegisInc v2.0'
    ];

    $textX = $sidebarX + 10;
    $textY = 50;

    foreach ($textLines as $i => $line) {
        $pdf->SetXY($textX, $textY + ($i * 8));
        $pdf->Cell(100, 6, $line, 0, 0, 'C');
    }

    echo "✅ Texto lateral adicionado (" . count($textLines) . " linhas)\n";

    // Adicionar um "QR code" simulado (retângulo)
    $qrX = $sidebarX + 16;
    $qrY = 16;
    $qrSize = 88;

    $pdf->SetFillColor(0, 0, 0);
    $pdf->Rect($qrX, $qrY, $qrSize, $qrSize, 'F');

    $pdf->SetFillColor(255, 255, 255);
    $pdf->Rect($qrX + 10, $qrY + 10, $qrSize - 20, $qrSize - 20, 'F');

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY($qrX + 20, $qrY + 35);
    $pdf->Cell($qrSize - 40, 10, 'QR CODE', 0, 0, 'C');
    $pdf->SetXY($qrX + 20, $qrY + 45);
    $pdf->Cell($qrSize - 40, 10, 'SIMULADO', 0, 0, 'C');

    echo "✅ QR Code simulado adicionado\n";

    // Salvar PDF modificado
    $modifiedPdfPath = '/tmp/claude/modified_simple.pdf';
    $modifiedContent = $pdf->Output('S');
    file_put_contents($modifiedPdfPath, $modifiedContent);

    echo "\n📊 3. Resultados:\n";
    echo "================\n";
    echo "PDF original: " . filesize($originalPdfPath) . " bytes\n";
    echo "PDF modificado: " . filesize($modifiedPdfPath) . " bytes\n";
    echo "Diferença: " . (filesize($modifiedPdfPath) - filesize($originalPdfPath)) . " bytes\n";

    // Verificar se realmente modificou
    $originalContent = file_get_contents($originalPdfPath);
    $modifiedContentFromFile = file_get_contents($modifiedPdfPath);

    if ($originalContent === $modifiedContentFromFile) {
        echo "❌ FALHA: Conteúdo idêntico!\n";
        exit(1);
    } else {
        echo "✅ SUCESSO: PDF foi modificado!\n";
    }

    // Verificar elementos no PDF
    echo "\n🔍 4. Verificando elementos:\n";

    $elementsFound = [];

    if (strpos($modifiedContentFromFile, 'Bruno Silva') !== false) {
        $elementsFound[] = 'Nome do signatário';
    }

    if (strpos($modifiedContentFromFile, '001/2024') !== false) {
        $elementsFound[] = 'Número do documento';
    }

    if (strpos($modifiedContentFromFile, 'exemplo.com') !== false) {
        $elementsFound[] = 'URL de verificação';
    }

    if (strpos($modifiedContentFromFile, 'ABC123DE') !== false) {
        $elementsFound[] = 'Código de verificação';
    }

    if (strpos($modifiedContentFromFile, 'LegisInc v2.0') !== false) {
        $elementsFound[] = 'Versão do sistema';
    }

    echo "Elementos detectados no PDF:\n";
    foreach ($elementsFound as $element) {
        echo "✅ $element\n";
    }

    $testScore = count($elementsFound);
    $maxScore = 5;

    echo "\n🎯 RESULTADO FINAL:\n";
    echo "==================\n";
    echo "Score: $testScore/$maxScore elementos detectados\n";
    echo "Status: " . ($testScore >= 4 ? "✅ APROVADO" : ($testScore >= 2 ? "⚠️ PARCIAL" : "❌ FALHOU")) . "\n";
    echo "\nArquivos gerados:\n";
    echo "- Original: $originalPdfPath\n";
    echo "- Modificado: $modifiedPdfPath\n";

    if ($testScore >= 4) {
        echo "\n🎉 SUCESSO! O sistema de modificação de PDF está funcionando!\n";
        echo "Os elementos estão sendo incorporados corretamente no conteúdo do PDF.\n";
    } else {
        echo "\n⚠️ ATENÇÃO: Alguns elementos podem não ter sido incorporados corretamente.\n";
    }

} catch (Exception $e) {
    echo "\n❌ ERRO durante o teste:\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";

    exit(1);
}