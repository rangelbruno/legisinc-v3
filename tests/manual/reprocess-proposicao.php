<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

// Buscar proposição
$proposicao = App\Models\Proposicao::find(53);
if (!$proposicao) {
    echo "Proposição não encontrada\n";
    exit;
}

// Ler arquivo RTF
$filePath = storage_path('app/public/' . $proposicao->arquivo_path);
if (!file_exists($filePath)) {
    echo "Arquivo não encontrado: $filePath\n";
    exit;
}

$content = file_get_contents($filePath);

// Extrair texto usando a função do controller
$controller = new App\Http\Controllers\ProposicaoController();
$reflection = new ReflectionClass($controller);

// Chamar extrairTextoDoArquivo
$extractMethod = $reflection->getMethod('extrairTextoDoArquivo');
$extractMethod->setAccessible(true);
$texto = $extractMethod->invoke($controller, $content);

// Chamar extrairEmentaEConteudo
$splitMethod = $reflection->getMethod('extrairEmentaEConteudo');
$splitMethod->setAccessible(true);
$result = $splitMethod->invoke($controller, $texto);

echo "Texto extraído:\n";
echo "================\n";
echo substr($texto, 0, 500) . "\n\n";

echo "Ementa: " . substr($result['ementa'], 0, 200) . "\n";
echo "Conteúdo: " . substr($result['conteudo'], 0, 200) . "\n\n";

// Atualizar no banco
$proposicao->ementa = $result['ementa'];
$proposicao->conteudo = $result['conteudo'];
$proposicao->save();

echo "Proposição 53 atualizada com sucesso!\n";
