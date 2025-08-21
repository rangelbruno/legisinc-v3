<?php

require '/home/bruno/legisinc/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once '/home/bruno/legisinc/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 TESTE DIRETO DO PDF\n";
echo "=====================\n\n";

// Buscar proposição 2
$proposicao = \App\Models\Proposicao::find(2);
if (!$proposicao) {
    echo "❌ Proposição 2 não encontrada\n";
    exit;
}

echo "📄 Proposição encontrada: {$proposicao->tipo} - {$proposicao->ementa}\n\n";

// Buscar arquivo mais recente
$arquivos = glob('/home/bruno/legisinc/storage/app/private/proposicoes/proposicao_2_*.docx');
if (empty($arquivos)) {
    echo "❌ Nenhum arquivo DOCX encontrado\n";
    exit;
}

usort($arquivos, function($a, $b) {
    return filemtime($b) - filemtime($a);
});

$arquivoMaisRecente = $arquivos[0];
echo "📂 Arquivo mais recente: " . basename($arquivoMaisRecente) . "\n";
echo "📅 Modificado: " . date('Y-m-d H:i:s', filemtime($arquivoMaisRecente)) . "\n";
echo "📏 Tamanho: " . filesize($arquivoMaisRecente) . " bytes\n\n";

// Instanciar controller
$controller = new \App\Http\Controllers\ProposicaoAssinaturaController();

try {
    echo "🔧 Chamando método de extração...\n";
    
    // Usar reflexão para chamar método privado
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('extrairConteudoDOCX');
    $method->setAccessible(true);
    
    $conteudo = $method->invoke($controller, $arquivoMaisRecente);
    
    echo "✅ Conteúdo extraído: " . strlen($conteudo) . " caracteres\n";
    echo "📝 Primeiros 500 chars:\n";
    echo substr($conteudo, 0, 500) . "\n\n";
    
    // Contar parágrafos HTML
    $paragrafos = substr_count($conteudo, '<p');
    echo "📊 Parágrafos HTML encontrados: $paragrafos\n";
    
    // Verificar conteúdo específico
    $marcadores = [
        'Revisado pelo Parlamentar',
        'Curiosidade para o dia 20 de agosto',
        'curso.dev',
        'NIC br anuncia',
        'Caraguatatuba, 20 de agosto de 2025'
    ];
    
    echo "\n🔍 Verificando conteúdo específico:\n";
    foreach ($marcadores as $marcador) {
        if (strpos($conteudo, $marcador) !== false) {
            echo "   ✅ '$marcador' - ENCONTRADO\n";
        } else {
            echo "   ❌ '$marcador' - NÃO ENCONTRADO\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n========================\n";
echo "✅ Teste direto concluído!\n";