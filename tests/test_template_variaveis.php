<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Admin\TemplateUniversalController;
use App\Models\TemplateUniversal;
use App\Services\OnlyOffice\OnlyOfficeService;
use App\Services\Template\TemplateProcessorService;

echo "===== TESTE DE VARIÁVEIS NO TEMPLATE UNIVERSAL =====\n\n";

// Buscar template
$template = TemplateUniversal::find(1);
if (! $template) {
    echo "❌ Template universal ID 1 não encontrado\n";
    exit(1);
}

echo "✅ Template encontrado: {$template->nome}\n\n";

// Simular download
$onlyOfficeService = app(OnlyOfficeService::class);
$templateProcessor = app(TemplateProcessorService::class);
$controller = new TemplateUniversalController($onlyOfficeService, $templateProcessor);
$response = $controller->download($template);

$conteudo = $response->getContent();

// Procurar por variáveis
$variaveis = [
    '${cabecalho_nome_camara}',
    '${cabecalho_endereco}',
    '${cabecalho_telefone}',
    '${tipo_proposicao}',
    '${numero_proposicao}',
    '${ementa}',
    '${texto}',
    '${justificativa}',
    '${municipio}',
    '${autor_nome}',
    '${autor_cargo}',
];

echo "🔍 Verificando presença das variáveis:\n\n";
$todasPresentes = true;

foreach ($variaveis as $variavel) {
    // Verificar se a variável está presente
    if (strpos($conteudo, $variavel) !== false) {
        echo "✅ {$variavel} - ENCONTRADA\n";
    } else {
        // Verificar se está com escape RTF
        $variavelEscapada = str_replace(['$', '{', '}'], ['\\$', '\\{', '\\}'], $variavel);
        if (strpos($conteudo, $variavelEscapada) !== false) {
            echo "⚠️ {$variavel} - Encontrada com escape RTF\n";
        } else {
            echo "❌ {$variavel} - NÃO ENCONTRADA\n";
            $todasPresentes = false;
        }
    }
}

echo "\n";
if ($todasPresentes) {
    echo "✅ TODAS AS VARIÁVEIS ESTÃO PRESENTES!\n";
} else {
    echo "⚠️ Algumas variáveis estão faltando ou foram convertidas incorretamente.\n";

    // Mostrar amostra do conteúdo para debug
    echo "\n📄 Amostra do conteúdo (primeiros 500 caracteres após RTF header):\n";
    $inicio = strpos($conteudo, '\par');
    if ($inicio !== false) {
        echo substr($conteudo, $inicio, 500)."...\n";
    }
}

// Salvar arquivo para inspeção manual
$testFile = '/tmp/template_universal_variaveis.rtf';
file_put_contents($testFile, $conteudo);
echo "\n💾 Arquivo salvo para inspeção: $testFile\n";
echo "   Você pode abrir este arquivo em um editor RTF para verificar as variáveis.\n";

echo "\n✅ TESTE CONCLUÍDO!\n";
