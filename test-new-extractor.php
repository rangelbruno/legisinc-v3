<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use App\Services\RTFTextExtractor;

// Ler arquivo RTF
$filePath = storage_path('app/public/proposicoes/proposicao_53_template_4.rtf');
if (!file_exists($filePath)) {
    echo "Arquivo não encontrado: $filePath\n";
    exit;
}

$rtfContent = file_get_contents($filePath);
echo "Tamanho do arquivo RTF: " . strlen($rtfContent) . " bytes\n\n";

// Extrair texto
$texto = RTFTextExtractor::extract($rtfContent);
echo "Texto extraído:\n";
echo "================\n";
echo substr($texto, 0, 500) . "\n\n";

// Extrair ementa e conteúdo
$resultado = RTFTextExtractor::extractEmentaAndConteudo($texto);
echo "Ementa extraída:\n";
echo $resultado['ementa'] . "\n\n";

echo "Conteúdo extraído (primeiros 200 chars):\n";
echo substr($resultado['conteudo'], 0, 200) . "\n\n";

// Atualizar no banco
$proposicao = App\Models\Proposicao::find(53);
if ($proposicao) {
    $proposicao->ementa = $resultado['ementa'];
    $proposicao->conteudo = $resultado['conteudo'];
    $proposicao->save();
    echo "Proposição 53 atualizada com sucesso!\n";
} else {
    echo "Proposição 53 não encontrada no banco.\n";
}