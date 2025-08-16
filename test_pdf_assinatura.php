<?php

require '/var/www/html/vendor/autoload.php';

$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Proposicao;
use App\Http\Controllers\ProposicaoAssinaturaController;

echo "🧪 TESTE: Geração de PDF para Assinatura\n";
echo "==========================================\n\n";

// Buscar a proposição de teste
$proposicao = Proposicao::find(3);

if (!$proposicao) {
    echo "❌ Proposição 3 não encontrada!\n";
    exit;
}

echo "📋 Proposição encontrada:\n";
echo "   ID: {$proposicao->id}\n";
echo "   Tipo: {$proposicao->tipo}\n";
echo "   Status: {$proposicao->status}\n";
echo "   Arquivo: {$proposicao->arquivo_path}\n";
echo "   Ementa: {$proposicao->ementa}\n\n";

// Verificar se arquivo existe
$arquivoPath = storage_path('app/' . $proposicao->arquivo_path);
echo "📁 Verificando arquivo:\n";
echo "   Path: {$arquivoPath}\n";
echo "   Existe: " . (file_exists($arquivoPath) ? "✅ SIM" : "❌ NÃO") . "\n";

if (file_exists($arquivoPath)) {
    echo "   Tamanho: " . filesize($arquivoPath) . " bytes\n";
}

echo "\n🔧 Testando geração de PDF...\n";

try {
    $controller = new ProposicaoAssinaturaController();
    
    // Chamar o método de geração do PDF
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('gerarPDFParaAssinatura');
    $method->setAccessible(true);
    
    $method->invoke($controller, $proposicao);
    
    echo "✅ PDF gerado com sucesso!\n";
    
    // Verificar se PDF foi criado
    if ($proposicao->arquivo_pdf_path) {
        $pdfPath = storage_path('app/' . $proposicao->arquivo_pdf_path);
        echo "📄 PDF salvo em: {$pdfPath}\n";
        echo "📊 Tamanho do PDF: " . (file_exists($pdfPath) ? filesize($pdfPath) . " bytes" : "Arquivo não encontrado") . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "📍 Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n✅ Teste concluído!\n";