<?php
require __DIR__.'/vendor/autoload.php';

use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);
$kernel->terminate($request, $response);

use App\Services\PDFAssinaturaIntegradaService;
use Illuminate\Support\Facades\Log;

echo "🔍 Teste de Posicionamento da Assinatura Digital\n";
echo "=" . str_repeat("=", 50) . "\n\n";

try {
    // Criar PDF de teste
    $tcpdf = new TCPDF('P', 'mm', 'A4');
    $tcpdf->SetMargins(20, 20, 20);
    $tcpdf->AddPage();
    $tcpdf->SetFont('helvetica', '', 12);

    // Adicionar conteúdo de teste
    $tcpdf->MultiCell(0, 5, 'DOCUMENTO DE TESTE PARA ASSINATURA DIGITAL', 0, 'C');
    $tcpdf->Ln(10);
    $tcpdf->MultiCell(0, 5, 'Este é um documento de teste para verificar o posicionamento correto da assinatura digital. O carimbo deve aparecer ao lado do conteúdo, não em cima dele.', 0, 'L');
    $tcpdf->Ln(10);

    // Adicionar mais conteúdo
    for ($i = 1; $i <= 20; $i++) {
        $tcpdf->MultiCell(0, 5, "Linha de teste $i: Lorem ipsum dolor sit amet, consectetur adipiscing elit.", 0, 'L');
    }

    $pdfContent = $tcpdf->Output('', 'S');
    file_put_contents('/tmp/test_original.pdf', $pdfContent);
    echo "✅ PDF de teste criado: /tmp/test_original.pdf\n\n";

    // Configurar assinatura
    $config = [
        'tipo_documento' => 'PROJETO_LEI_ORDINARIA',
        'numero' => 'TEST-001',
        'ano' => '2025',
        'protocolo' => 'PROTO-TEST',
        'data_recebimento' => now(),
        'autor' => 'Teste Automático',
        'proposicao_id' => 999,
        'hash_verificacao' => 'test_hash_' . uniqid()
    ];

    // Instanciar serviço
    $service = app(PDFAssinaturaIntegradaService::class);

    echo "📝 Aplicando assinatura digital...\n";
    $result = $service->assinarDocumento(
        $pdfContent,
        'Teste Automático',
        $config
    );

    if ($result['success']) {
        echo "✅ Assinatura aplicada com sucesso!\n";

        // Salvar PDF assinado
        $pdfAssinado = base64_decode($result['signed_pdf']);
        file_put_contents('/tmp/test_assinado.pdf', $pdfAssinado);

        echo "\n📊 Detalhes do resultado:\n";
        echo "- Arquivo salvo em: /tmp/test_assinado.pdf\n";
        echo "- Tamanho original: " . filesize('/tmp/test_original.pdf') . " bytes\n";
        echo "- Tamanho assinado: " . filesize('/tmp/test_assinado.pdf') . " bytes\n";

        if (isset($result['signature_method'])) {
            echo "- Método usado: " . $result['signature_method'] . "\n";
        }

        echo "\n🎯 IMPORTANTE: Verifique visualmente os arquivos:\n";
        echo "- Original: /tmp/test_original.pdf\n";
        echo "- Assinado: /tmp/test_assinado.pdf\n";
        echo "\nO carimbo deve estar ao LADO do conteúdo, não sobrepondo!\n";

    } else {
        echo "❌ Erro ao aplicar assinatura: " . $result['message'] . "\n";
    }

} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Teste finalizado.\n";