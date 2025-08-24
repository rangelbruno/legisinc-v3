<?php

require '/var/www/html/vendor/autoload.php';

$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Proposicao;
use App\Http\Controllers\ProposicaoAssinaturaController;

echo "🧪 TESTE: Geração de PDF para Proposição 1\n";
echo "==========================================\n\n";

// Buscar a proposição 1
$proposicao = Proposicao::find(1);

if (!$proposicao) {
    echo "❌ Proposição 1 não encontrada!\n";
    exit;
}

echo "📋 Proposição encontrada:\n";
echo "   ID: {$proposicao->id}\n";
echo "   Tipo: {$proposicao->tipo}\n";
echo "   Status: {$proposicao->status}\n";
echo "   Arquivo: {$proposicao->arquivo_path}\n";
echo "   Ementa: {$proposicao->ementa}\n\n";

// Verificar se status permite geração do PDF
$statusPermitidos = ['aprovado_assinatura', 'retornado_legislativo'];
$statusOk = in_array($proposicao->status, $statusPermitidos);

echo "🔍 Verificação de status:\n";
echo "   Status atual: {$proposicao->status}\n";
echo "   Status permitidos: " . implode(', ', $statusPermitidos) . "\n";
echo "   Status OK: " . ($statusOk ? "✅ SIM" : "❌ NÃO") . "\n\n";

// Verificar se arquivo existe
$locaisParaBuscar = [
    storage_path('app/' . $proposicao->arquivo_path),
    storage_path('app/private/' . $proposicao->arquivo_path),
    storage_path('app/public/' . $proposicao->arquivo_path),
    '/var/www/html/storage/app/' . $proposicao->arquivo_path,
    '/var/www/html/storage/app/private/' . $proposicao->arquivo_path,
    '/var/www/html/storage/app/public/' . $proposicao->arquivo_path
];

$arquivoEncontrado = null;
foreach ($locaisParaBuscar as $caminho) {
    if (file_exists($caminho)) {
        $arquivoEncontrado = $caminho;
        break;
    }
}

echo "📁 Verificando arquivo:\n";
echo "   Path BD: {$proposicao->arquivo_path}\n";
echo "   Arquivo encontrado: " . ($arquivoEncontrado ? "✅ $arquivoEncontrado" : "❌ NÃO") . "\n";

if ($arquivoEncontrado) {
    echo "   Tamanho: " . filesize($arquivoEncontrado) . " bytes\n";
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
    
    // Recarregar proposição para ver se PDF foi salvo
    $proposicao->refresh();
    
    // Verificar se PDF foi criado
    if ($proposicao->arquivo_pdf_path) {
        $pdfPath = storage_path('app/' . $proposicao->arquivo_pdf_path);
        echo "📄 PDF salvo em: {$pdfPath}\n";
        echo "📊 Tamanho do PDF: " . (file_exists($pdfPath) ? filesize($pdfPath) . " bytes" : "Arquivo não encontrado") . "\n";
        
        // Verificar logs recentes
        echo "\n📝 Logs recentes:\n";
        $logs = shell_exec('tail -5 /var/log/php/php_errors.log | grep "PDF Assinatura"');
        if ($logs) {
            echo $logs;
        } else {
            echo "   Nenhum log encontrado\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "📍 Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n✅ Teste concluído!\n";