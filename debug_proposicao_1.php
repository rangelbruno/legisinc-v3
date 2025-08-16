<?php

require '/var/www/html/vendor/autoload.php';

$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Proposicao;
use App\Services\DocumentExtractionService;

echo "🔍 DEBUG: Proposição 1 - Verificação de Arquivo\n";
echo "==============================================\n\n";

// Buscar a proposição 1
$proposicao = Proposicao::find(1);

if (!$proposicao) {
    echo "❌ Proposição 1 não encontrada!\n";
    exit;
}

echo "📋 Dados da Proposição:\n";
echo "   ID: {$proposicao->id}\n";
echo "   Tipo: {$proposicao->tipo}\n";
echo "   Status: {$proposicao->status}\n";
echo "   Ementa: {$proposicao->ementa}\n";
echo "   Arquivo Path (BD): {$proposicao->arquivo_path}\n";
echo "   Template ID: {$proposicao->template_id}\n";
echo "   Conteúdo Length: " . strlen($proposicao->conteudo) . " chars\n\n";

// Verificar múltiplos locais onde o arquivo pode estar
$locaisParaBuscar = [
    storage_path('app/' . $proposicao->arquivo_path),
    storage_path('app/private/' . $proposicao->arquivo_path),
    storage_path('app/public/' . $proposicao->arquivo_path),
    '/var/www/html/storage/app/' . $proposicao->arquivo_path,
    '/var/www/html/storage/app/private/' . $proposicao->arquivo_path,
    '/var/www/html/storage/app/public/' . $proposicao->arquivo_path
];

echo "📁 Verificando locais de arquivo:\n";
$arquivoEncontrado = null;
foreach ($locaisParaBuscar as $i => $caminho) {
    $existe = file_exists($caminho);
    echo "   " . ($i + 1) . ". {$caminho}\n";
    echo "      Existe: " . ($existe ? "✅ SIM" : "❌ NÃO") . "\n";
    
    if ($existe) {
        echo "      Tamanho: " . filesize($caminho) . " bytes\n";
        echo "      Modificado: " . date('Y-m-d H:i:s', filemtime($caminho)) . "\n";
        if (!$arquivoEncontrado) {
            $arquivoEncontrado = $caminho;
        }
    }
    echo "\n";
}

if ($arquivoEncontrado) {
    echo "🎯 Arquivo encontrado em: {$arquivoEncontrado}\n\n";
    
    // Extrair conteúdo do DOCX
    echo "📄 Extraindo conteúdo do DOCX...\n";
    try {
        $extractionService = app(DocumentExtractionService::class);
        $conteudoExtraido = $extractionService->extractTextFromDocxFile($arquivoEncontrado);
        
        echo "✅ Conteúdo extraído com sucesso!\n";
        echo "📊 Tamanho: " . strlen($conteudoExtraido) . " caracteres\n\n";
        
        echo "📝 PRIMEIROS 500 CARACTERES DO ARQUIVO:\n";
        echo "=====================================\n";
        echo substr($conteudoExtraido, 0, 500) . "\n";
        echo "=====================================\n\n";
        
        echo "📝 CONTEÚDO DO BANCO DE DADOS:\n";
        echo "=============================\n";
        echo substr($proposicao->conteudo, 0, 500) . "\n";
        echo "=============================\n\n";
        
        // Comparar se são diferentes
        if (trim($conteudoExtraido) === trim($proposicao->conteudo)) {
            echo "⚠️  ARQUIVO E BANCO SÃO IDÊNTICOS - Pode estar usando template original\n";
        } else {
            echo "✅ ARQUIVO E BANCO SÃO DIFERENTES - Arquivo foi editado pelo Legislativo\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Erro ao extrair conteúdo: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Nenhum arquivo encontrado!\n";
}

echo "\n✅ Debug concluído!\n";