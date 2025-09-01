<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Admin\TemplateUniversalController;
use App\Models\TemplateUniversal;
use App\Services\OnlyOffice\OnlyOfficeService;
use App\Services\Template\TemplateProcessorService;

echo "===== TESTE DE TEMPLATE UNIVERSAL RTF =====\n\n";

// Buscar template
$template = TemplateUniversal::find(1);
if (! $template) {
    echo "❌ Template universal ID 1 não encontrado\n";
    exit(1);
}

echo "✅ Template encontrado: {$template->nome}\n";
echo "   Formato: {$template->formato}\n";
echo "   Document Key: {$template->document_key}\n\n";

// Simular download
$onlyOfficeService = app(OnlyOfficeService::class);
$templateProcessor = app(TemplateProcessorService::class);
$controller = new TemplateUniversalController($onlyOfficeService, $templateProcessor);
$response = $controller->download($template);

echo "📥 Response do download:\n";
echo "   Status: {$response->getStatusCode()}\n";
echo '   Content-Type: '.$response->headers->get('Content-Type')."\n";
echo '   Content-Disposition: '.$response->headers->get('Content-Disposition')."\n\n";

$conteudo = $response->getContent();
echo "📄 Análise do conteúdo:\n";
echo '   Tamanho: '.strlen($conteudo)." bytes\n";
echo '   Começa com {\\rtf: '.(strpos($conteudo, '{\\rtf') === 0 ? 'SIM ✅' : 'NÃO ❌')."\n";
echo '   Contém \\ansicpg65001: '.(strpos($conteudo, '\\ansicpg65001') !== false ? 'SIM ✅' : 'NÃO ❌')."\n";
echo '   Primeiros 100 caracteres: '.substr($conteudo, 0, 100)."\n\n";

// Verificar se tem caracteres acentuados e suas conversões
$acentuados = [
    'á' => 225, 'é' => 233, 'í' => 237, 'ó' => 243, 'ú' => 250,
    'ã' => 227, 'õ' => 245, 'ç' => 231,
    'â' => 226, 'ê' => 234, 'ô' => 244,
];

echo "🔤 Análise de acentuação:\n";
foreach ($acentuados as $char => $unicode) {
    $rtfSequence = '\\u'.$unicode.'*';
    if (strpos($conteudo, $char) !== false) {
        echo "   ❌ Encontrado '$char' sem conversão\n";
    }
    if (strpos($conteudo, $rtfSequence) !== false) {
        echo "   ✅ Encontrado '$rtfSequence' (conversão de '$char')\n";
    }
}

// Salvar arquivo para inspeção
$testFile = '/tmp/template_universal_test.rtf';
file_put_contents($testFile, $conteudo);
echo "\n💾 Arquivo salvo em: $testFile\n";

// Verificar configuração do editor
$config = $controller->criarConfiguracaoOnlyOffice($template);
echo "\n⚙️ Configuração OnlyOffice:\n";
echo '   fileType: '.$config['document']['fileType']."\n";
echo '   documentType: '.$config['documentType']."\n";
echo '   mode: '.$config['editorConfig']['mode']."\n";
echo '   lang: '.$config['editorConfig']['lang']."\n";

echo "\n✅ TESTE CONCLUÍDO!\n";
