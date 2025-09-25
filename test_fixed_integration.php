<?php

/**
 * Teste da integração corrigida - verificar se os elementos estão sendo passados corretamente
 * do PDFAssinaturaIntegradaService para o ESignMCPIntegrationService
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\ESignMCPIntegrationService;
use setasign\Fpdi\Fpdi;

// Mock da função config() para perfis
function config($key, $default = null) {
    if ($key === 'legisinc_sign_profiles') {
        return include __DIR__ . '/config/legisinc_sign_profiles.php';
    }
    return $default;
}

// Mock da classe Log
class TestLog {
    public static function info($message, $context = []) {
        echo "ℹ️ INFO: $message\n";
        if (!empty($context)) {
            $formatted = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            echo "   Context: $formatted\n";
        }
    }

    public static function error($message, $context = []) {
        echo "❌ ERROR: $message\n";
        if (!empty($context)) {
            $formatted = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            echo "   Context: $formatted\n";
        }
    }

    public static function warning($message, $context = []) {
        echo "⚠️ WARNING: $message\n";
    }
}

// Alias para funcionar com o namespace
class_alias(TestLog::class, 'Illuminate\Support\Facades\Log');

echo "🧪 Teste da Integração Corrigida\n";
echo "=================================\n\n";

try {
    // 1. Criar PDF de teste
    echo "📄 1. Criando PDF de teste...\n";

    $testPdf = new Fpdi();
    $testPdf->AddPage();
    $testPdf->SetFont('Arial', 'B', 16);
    $testPdf->Cell(0, 10, 'PROJETO DE LEI ORDINÁRIA Nº 001/2025', 0, 1, 'C');
    $testPdf->Ln(20);
    $testPdf->SetFont('Arial', '', 12);
    $testPdf->Cell(0, 10, 'Conteúdo do documento que será carimbado.', 0, 1);

    $originalPdfPath = '/tmp/claude/test_integration.pdf';
    $testPdf->Output('F', $originalPdfPath);

    echo "✅ PDF criado: " . filesize($originalPdfPath) . " bytes\n\n";

    // 2. Simular os elementos que o PDFAssinaturaIntegradaService criaria
    echo "🔧 2. Preparando elementos (simulando buildElements())...\n";

    $elements = [
        [
            'type' => 'text',
            'x' => 111.9,
            'y' => 112,
            'width' => 88,
            'height' => 135.4,
            'text' => 'PROJETO DE LEI ORDINÁRIA Nº 001/2025 - Protocolo nº 12345 recebido em 25/09/2025 15:30 - Esta é uma cópia do original assinado digitalmente por Jessica Santos. Para validar o documento, leia o código QR ou acesse exemplo.com/verificar e informe o código ABC123DE.',
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
            'x' => 111.9,
            'y' => 16,
            'width' => 88,
            'height' => 88,
            'data' => 'https://exemplo.com/verificar/abc123',
            'error_correction' => 'M',
            'border' => 1,
            'quiet_zone' => 2
        ]
    ];

    echo "✅ " . count($elements) . " elementos preparados\n";
    foreach ($elements as $i => $element) {
        echo "   - Elemento " . ($i+1) . ": {$element['type']} - " .
             (isset($element['text']) ? substr($element['text'], 0, 50) . "..." : $element['data']) . "\n";
    }
    echo "\n";

    // 3. Chamar o método carimbarLateral diretamente com os elementos
    echo "🎯 3. Testando carimbarLateral com elementos reais...\n";

    $mcpService = new ESignMCPIntegrationService();

    $stampOptions = [
        'pdf_path' => $originalPdfPath,
        'page' => 1,
        'sidebar' => [
            'x' => 95.9,
            'y' => 0,
            'w' => 120,
            'h' => 279.4
        ],
        'padding' => 16,
        'elements' => $elements, // AQUI ESTÃO OS ELEMENTOS CORRETOS
        'profile_id' => 'legisinc_v2_lateral'
    ];

    $stampedPdfPath = $mcpService->carimbarLateral($stampOptions);

    echo "✅ Carimbo aplicado!\n";
    echo "   PDF original: " . filesize($originalPdfPath) . " bytes\n";
    echo "   PDF carimbado: " . filesize($stampedPdfPath) . " bytes\n";
    echo "   Diferença: " . (filesize($stampedPdfPath) - filesize($originalPdfPath)) . " bytes\n\n";

    // 4. Verificar se realmente modificou
    echo "🔍 4. Verificando modificações...\n";

    $originalContent = file_get_contents($originalPdfPath);
    $stampedContent = file_get_contents($stampedPdfPath);

    if ($originalContent === $stampedContent) {
        echo "❌ FALHA: Conteúdo idêntico - elementos não foram aplicados!\n";
        exit(1);
    } else {
        echo "✅ SUCESSO: PDF foi modificado com elementos reais!\n";
    }

    // 5. Buscar indícios de elementos no PDF
    echo "\n🔍 5. Buscando elementos no PDF carimbado...\n";

    $elementsFound = [];

    // Buscar texto
    if (strpos($stampedContent, 'Jessica') !== false) {
        $elementsFound[] = 'Nome do signatário (Jessica)';
    }

    if (strpos($stampedContent, '001/2025') !== false) {
        $elementsFound[] = 'Número do projeto (001/2025)';
    }

    if (strpos($stampedContent, '/Image') !== false || strpos($stampedContent, 'PNG') !== false) {
        $elementsFound[] = 'QR Code (PNG detectado)';
    }

    if (strpos($stampedContent, 'exemplo.com') !== false) {
        $elementsFound[] = 'URL de verificação';
    }

    // Verificar se a sidebar foi adicionada (buscando por coordenadas ou retângulos)
    if (preg_match('/95\.9|111\.9/', $stampedContent)) {
        $elementsFound[] = 'Coordenadas da sidebar';
    }

    echo "Elementos detectados:\n";
    foreach ($elementsFound as $element) {
        echo "✅ $element\n";
    }

    $score = count($elementsFound);
    $maxScore = 5;

    echo "\n🎯 RESULTADO FINAL:\n";
    echo "==================\n";
    echo "Score: $score/$maxScore elementos detectados\n";
    echo "Status: " . ($score >= 3 ? "✅ APROVADO" : ($score >= 1 ? "⚠️ PARCIAL" : "❌ FALHOU")) . "\n";
    echo "\nArquivos:\n";
    echo "- Original: $originalPdfPath\n";
    echo "- Carimbado: $stampedPdfPath\n";

    if ($score >= 3) {
        echo "\n🎉 INTEGRAÇÃO CORRIGIDA COM SUCESSO!\n";
        echo "Os elementos estão sendo passados e processados corretamente.\n";
    } elseif ($score >= 1) {
        echo "\n⚠️ INTEGRAÇÃO PARCIALMENTE FUNCIONANDO\n";
        echo "Alguns elementos foram detectados, mas pode haver problemas.\n";
    } else {
        echo "\n❌ INTEGRAÇÃO AINDA COM PROBLEMAS\n";
        echo "Nenhum elemento foi detectado no PDF final.\n";
    }

} catch (Exception $e) {
    echo "\n❌ ERRO durante o teste:\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}