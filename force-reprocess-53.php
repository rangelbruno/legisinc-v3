<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use App\Services\RTFTextExtractor;

// Buscar proposição
$proposicao = App\Models\Proposicao::find(53);
if (!$proposicao) {
    echo "Proposição não encontrada\n";
    exit;
}

echo "Proposição atual:\n";
echo "Ementa: " . $proposicao->ementa . "\n";
echo "Conteúdo: " . substr($proposicao->conteudo, 0, 100) . "...\n\n";

// Ler o arquivo RTF atual
$filePath = storage_path('app/public/' . $proposicao->arquivo_path);
if (!file_exists($filePath)) {
    echo "Arquivo não encontrado: $filePath\n";
    exit;
}

echo "Reprocessando arquivo: " . basename($filePath) . "\n";
$rtfContent = file_get_contents($filePath);
echo "Tamanho do arquivo: " . strlen($rtfContent) . " bytes\n\n";

// Extrair texto com o novo extrator
$texto = RTFTextExtractor::extract($rtfContent);
echo "Texto extraído (primeiros 500 chars):\n";
echo substr($texto, 0, 500) . "\n\n";

// Extrair ementa e conteúdo
$resultado = RTFTextExtractor::extractEmentaAndConteudo($texto);
echo "Nova Ementa: '" . $resultado['ementa'] . "'\n";
echo "Novo Conteúdo: '" . substr($resultado['conteudo'], 0, 200) . "...'\n\n";

// Atualizar no banco
$proposicao->ementa = $resultado['ementa'];
$proposicao->conteudo = $resultado['conteudo'];
$proposicao->save();

echo "✅ Proposição 53 reprocessada e atualizada!\n";