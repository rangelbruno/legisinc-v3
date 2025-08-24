<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Proposicao;

// Carregar proposição
$proposicao = Proposicao::find(2);
if (!$proposicao) {
    echo "Proposição não encontrada\n";
    exit(1);
}

echo "=== TESTE DEBUG ASSINATURA ===\n";
echo "ID: {$proposicao->id}\n";
echo "Status: {$proposicao->status}\n";
echo "Assinatura Digital: " . ($proposicao->assinatura_digital ? 'SIM' : 'NÃO') . "\n";
echo "Data Assinatura: {$proposicao->data_assinatura}\n";

// Instanciar o controller para testar o método
$controller = new \App\Http\Controllers\ProposicaoAssinaturaController();

// Usar reflexão para acessar métodos privados
$reflection = new ReflectionClass($controller);

echo "\n=== TESTANDO MÉTODOS ===\n";

// Testar método gerarTextoAssinaturaDigital
$method = $reflection->getMethod('gerarTextoAssinaturaDigital');
$method->setAccessible(true);
$textoAssinatura = $method->invoke($controller, $proposicao);
echo "Texto Assinatura: '{$textoAssinatura}'\n";

// Testar método gerarTextoQRCode
$method2 = $reflection->getMethod('gerarTextoQRCode');
$method2->setAccessible(true);
$textoQR = $method2->invoke($controller, $proposicao);
echo "Texto QR Code: '{$textoQR}'\n";

// Testar método gerarIdentificadorAssinatura
$method3 = $reflection->getMethod('gerarIdentificadorAssinatura');
$method3->setAccessible(true);
$identificador = $method3->invoke($controller, $proposicao);
echo "Identificador: '{$identificador}'\n";

echo "\n=== TESTE CONCLUÍDO ===\n";