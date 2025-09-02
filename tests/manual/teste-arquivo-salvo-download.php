<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Simular ambiente Laravel
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 TESTE DE DOWNLOAD DE ARQUIVO SALVO\n";
echo "===================================\n";

// Buscar proposição
$proposicao = \App\Models\Proposicao::find(1);

if (!$proposicao) {
    echo "❌ Proposição 1 não encontrada\n";
    exit;
}

echo "✅ Proposição encontrada: ID {$proposicao->id}\n";
echo "   Arquivo Path: {$proposicao->arquivo_path}\n\n";

if ($proposicao->arquivo_path) {
    $caminhosPossiveis = [
        \Storage::disk('local')->path($proposicao->arquivo_path),
        storage_path('app/private/' . $proposicao->arquivo_path),
        storage_path('app/' . $proposicao->arquivo_path),
    ];
    
    echo "🔍 TESTANDO CAMINHOS POSSÍVEIS:\n";
    echo "==============================\n";
    
    $arquivoEncontrado = false;
    foreach ($caminhosPossiveis as $i => $caminho) {
        $existe = file_exists($caminho);
        $status = $existe ? "✅ EXISTE" : "❌ NÃO EXISTE";
        echo ($i + 1) . ". {$status}: {$caminho}\n";
        
        if ($existe && !$arquivoEncontrado) {
            $tamanho = filesize($caminho);
            echo "   📊 Tamanho: " . number_format($tamanho) . " bytes\n";
            echo "   📅 Modificado: " . date('Y-m-d H:i:s', filemtime($caminho)) . "\n";
            $arquivoEncontrado = true;
        }
    }
    
    if ($arquivoEncontrado) {
        echo "\n🎉 ARQUIVO ENCONTRADO! O sistema deveria usar este arquivo.\n";
        echo "📋 Próximo passo: Teste no OnlyOffice deve carregar este arquivo.\n";
    } else {
        echo "\n❌ NENHUM ARQUIVO ENCONTRADO. Sistema usará template universal.\n";
    }
} else {
    echo "⚠️  Proposição não tem arquivo_path definido.\n";
}

echo "\n📋 PARA TESTAR:\n";
echo "===============\n";
echo "1. Acesse: http://localhost:8001/proposicoes/1\n";
echo "2. Clique em 'Editar no OnlyOffice'\n";
echo "3. Verifique nos logs se aparece:\n";
echo "   'OnlyOffice Download: Usando arquivo salvo existente'\n";
echo "4. Se aparecer 'template universal', há problema na lógica.\n";