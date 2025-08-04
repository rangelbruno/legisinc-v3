<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use App\Services\RTFTextExtractor;

// Buscar proposição
$proposicao = App\Models\Proposicao::find(54);
if (!$proposicao) {
    echo "Proposição 54 não encontrada\n";
    exit;
}

echo "=== DIAGNÓSTICO PROPOSIÇÃO 54 ===\n";
echo "Estado atual:\n";
echo "- Ementa: '" . $proposicao->ementa . "'\n";
echo "- Conteúdo: '" . substr($proposicao->conteudo, 0, 100) . "...'\n";
echo "- Arquivo: " . $proposicao->arquivo_path . "\n\n";

// Verificar arquivo RTF
$filePath = storage_path('app/public/' . $proposicao->arquivo_path);
if (!file_exists($filePath)) {
    echo "❌ Arquivo RTF não encontrado: $filePath\n";
    echo "Limpando arquivo_path do banco...\n";
    $proposicao->arquivo_path = null;
    $proposicao->save();
    echo "✅ Proposição limpa. Acesse o editor novamente para criar novo arquivo.\n";
    exit;
}

$fileSize = filesize($filePath);
echo "Arquivo RTF encontrado:\n";
echo "- Tamanho: " . number_format($fileSize) . " bytes\n";

// Se arquivo muito grande (> 100KB), provavelmente está corrompido
if ($fileSize > 100000) {
    echo "❌ Arquivo muito grande - provavelmente corrompido\n";
    echo "Removendo arquivo corrompido...\n";
    unlink($filePath);
    
    echo "Limpando arquivo_path do banco...\n";
    $proposicao->arquivo_path = null;
    $proposicao->save();
    
    echo "✅ Proposição 54 limpa. Acesse /proposicoes/54/editar-onlyoffice/4 para criar novo arquivo.\n";
    exit;
}

// Se arquivo pequeno, tentar processar
echo "Tentando processar arquivo RTF...\n";
$rtfContent = file_get_contents($filePath);
$texto = RTFTextExtractor::extract($rtfContent);
$resultado = RTFTextExtractor::extractEmentaAndConteudo($texto);

echo "Resultado da extração:\n";
echo "- Nova Ementa: '" . $resultado['ementa'] . "'\n";
echo "- Novo Conteúdo: '" . substr($resultado['conteudo'], 0, 200) . "...'\n\n";

// Atualizar no banco
$proposicao->ementa = $resultado['ementa'];
$proposicao->conteudo = $resultado['conteudo'];
$proposicao->save();

echo "✅ Proposição 54 atualizada com sucesso!\n";