<?php

/**
 * Script de teste para verificar se o carimbo lateral está sendo aplicado corretamente
 * com elementos reais no PDF (não mais simulação)
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\ESignMCPIntegrationService;
use App\Services\PDFAssinaturaIntegradaService;
use Illuminate\Foundation\Application;

// Simular ambiente Laravel mínimo
define('LARAVEL_START', microtime(true));
$app = new Application($_ENV['APP_BASE_PATH'] ?? dirname(__DIR__));

// Configurar alguns serviços básicos necessários
$app->singleton('path', function () use ($app) {
    return $app->basePath();
});
$app->singleton('path.config', function () use ($app) {
    return $app->basePath('config');
});

// Mock básico da função config()
function config($key, $default = null) {
    if ($key === 'legisinc_sign_profiles') {
        return include __DIR__ . '/config/legisinc_sign_profiles.php';
    }
    return $default;
}

// Mock da função Log (silenciar logs para este teste)
class MockLog {
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

// Mock da classe Log do Laravel
class_alias(MockLog::class, 'Illuminate\Support\Facades\Log');

function dd(...$vars) {
    var_dump(...$vars);
    die();
}

echo "🧪 Teste do Sistema de Carimbo PDF Real (v2.0)\n";
echo "===============================================\n\n";

try {
    // 1. Criar PDF de teste simples usando FPDI
    echo "📄 1. Criando PDF de teste...\n";

    $testPdf = new setasign\Fpdi\Fpdi();
    $testPdf->AddPage();
    $testPdf->SetFont('Arial', 'B', 16);
    $testPdf->Cell(0, 10, 'DOCUMENTO DE TESTE PARA ASSINATURA DIGITAL', 0, 1, 'C');
    $testPdf->Ln(20);
    $testPdf->SetFont('Arial', '', 12);
    $testPdf->Cell(0, 10, 'Este é um documento de teste para verificar se o sistema', 0, 1);
    $testPdf->Cell(0, 10, 'de carimbo lateral está funcionando corretamente.', 0, 1);
    $testPdf->Ln(10);
    $testPdf->Cell(0, 10, 'Tipo: INDICAÇÃO', 0, 1);
    $testPdf->Cell(0, 10, 'Número: 001/2024', 0, 1);
    $testPdf->Cell(0, 10, 'Protocolo: 12345', 0, 1);

    $testPdfPath = '/tmp/claude/test_document.pdf';
    $testPdf->Output('F', $testPdfPath);

    echo "✅ PDF de teste criado: " . basename($testPdfPath) . "\n";
    echo "   Tamanho: " . filesize($testPdfPath) . " bytes\n\n";

    // 2. Inicializar serviços
    echo "🔧 2. Inicializando serviços...\n";

    $mcpService = new ESignMCPIntegrationService();
    $pdfIntegradoService = new PDFAssinaturaIntegradaService($mcpService);

    echo "✅ Serviços inicializados\n\n";

    // 3. Criar bindings de teste
    echo "📝 3. Preparando dados de teste...\n";

    $bindings = [
        'tipo' => 'INDICAÇÃO',
        'numero' => '001',
        'ano' => '2024',
        'protocolo' => '12345',
        'data_hora' => '25/09/2024 15:30',
        'signatario' => 'Bruno Silva',
        'url' => 'https://exemplo.com/verificar/abc123',
        'url_short' => 'exemplo.com/verificar',
        'url_qr' => 'https://exemplo.com/verificar/abc123',
        'codigo' => 'ABC123DE',
        'ementa_short' => 'Solicita melhorias na infraestrutura urbana',
        'timestamp' => time(),
        'signature_hash' => 'sha256:abcdef123456...'
    ];

    echo "✅ Bindings preparados:\n";
    foreach ($bindings as $key => $value) {
        echo "   - $key: $value\n";
    }
    echo "\n";

    // 4. Aplicar perfil automático
    echo "🎨 4. Aplicando perfil automático legisinc_v2_lateral...\n";

    $profileId = 'legisinc_v2_lateral';
    $stampedPdfPath = $pdfIntegradoService->aplicarPerfil($testPdfPath, $profileId, $bindings);

    echo "✅ Perfil aplicado com sucesso!\n";
    echo "   PDF original: " . basename($testPdfPath) . " (" . filesize($testPdfPath) . " bytes)\n";
    echo "   PDF carimbado: " . basename($stampedPdfPath) . " (" . filesize($stampedPdfPath) . " bytes)\n";
    echo "   Diferença: " . (filesize($stampedPdfPath) - filesize($testPdfPath)) . " bytes\n\n";

    // 5. Verificar se o PDF carimbado realmente foi modificado
    echo "🔍 5. Verificando modificações no PDF...\n";

    $originalContent = file_get_contents($testPdfPath);
    $stampedContent = file_get_contents($stampedPdfPath);

    if ($originalContent === $stampedContent) {
        echo "❌ FALHA: O conteúdo do PDF carimbado é idêntico ao original!\n";
        echo "   Isso indica que o MCP ainda não está processando os elementos.\n";
        exit(1);
    } else {
        echo "✅ SUCESSO: O PDF foi modificado com o carimbo!\n";
        echo "   Conteúdo diferente detectado - elementos foram adicionados.\n";
    }

    // 6. Verificar se contém elementos esperados (busca básica no PDF)
    echo "\n🔍 6. Verificando elementos no PDF carimbado...\n";

    $pdfText = $stampedContent;
    $elementsFound = [];

    // Verificar se contém QR code (procurar por marcadores PDF de imagem)
    if (strpos($pdfText, '/Image') !== false || strpos($pdfText, 'PNG') !== false) {
        $elementsFound[] = 'QR Code (imagem PNG detectada)';
    }

    // Verificar texto (algumas palavras-chave)
    if (strpos($pdfText, 'INDICACAO') !== false || strpos($pdfText, 'Bruno Silva') !== false) {
        $elementsFound[] = 'Texto da assinatura';
    }

    if (strpos($pdfText, '001/2024') !== false) {
        $elementsFound[] = 'Número do documento';
    }

    if (strpos($pdfText, 'exemplo.com') !== false) {
        $elementsFound[] = 'URL de verificação';
    }

    echo "✅ Elementos encontrados no PDF:\n";
    foreach ($elementsFound as $element) {
        echo "   - $element\n";
    }

    if (empty($elementsFound)) {
        echo "⚠️ ATENÇÃO: Nenhum elemento esperado foi encontrado no PDF.\n";
        echo "   Isso pode indicar que os elementos não foram incorporados corretamente.\n";
    }

    echo "\n📊 7. Resumo do teste:\n";
    echo "=====================\n";
    echo "Status: " . (count($elementsFound) > 0 ? "✅ APROVADO" : "❌ FALHOU") . "\n";
    echo "PDF original: " . filesize($testPdfPath) . " bytes\n";
    echo "PDF carimbado: " . filesize($stampedPdfPath) . " bytes\n";
    echo "Elementos detectados: " . count($elementsFound) . "\n";
    echo "Perfil usado: $profileId\n";
    echo "\nArquivos gerados:\n";
    echo "- PDF original: $testPdfPath\n";
    echo "- PDF carimbado: $stampedPdfPath\n";
    echo "\n🎉 Teste concluído!\n";

} catch (Exception $e) {
    echo "\n❌ ERRO durante o teste:\n";
    echo "Classe: " . get_class($e) . "\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString() . "\n";

    exit(1);
}