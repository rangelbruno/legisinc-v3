<?php

// Script PHP para testar diretamente a conversão de parágrafos

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use App\Models\Proposicao;
use App\Models\User;
use App\Services\Template\TemplateProcessorService;

// Login como parlamentar
$user = User::where('email', 'jessica@sistema.gov.br')->first();
Auth::login($user);

echo "================================================\n";
echo "TESTE: Preservação de Parágrafos no OnlyOffice\n";
echo "================================================\n\n";

// Texto com múltiplos parágrafos
$textoOriginal = "Primeiro parágrafo do texto da proposição.

Segundo parágrafo com mais conteúdo explicativo sobre o tema em questão.

Terceiro parágrafo final com a conclusão e justificativa da proposição.";

echo "1. Texto Original:\n";
echo "-------------------\n";
echo $textoOriginal . "\n";
echo "-------------------\n";
echo "Quebras de linha no original: " . substr_count($textoOriginal, "\n") . "\n\n";

// Criar proposição de teste
echo "2. Criando proposição de teste...\n";
$proposicao = Proposicao::create([
    'tipo' => 'Moção',
    'ementa' => 'Teste de preservação de parágrafos',
    'conteudo' => $textoOriginal,
    'autor_id' => $user->id,
    'status' => 'rascunho',
    'ano' => date('Y')
]);

echo "✓ Proposição criada com ID: " . $proposicao->id . "\n\n";

// Testar a conversão para RTF
echo "3. Testando conversão para RTF...\n";

$templateProcessor = app(TemplateProcessorService::class);

// Usar reflexão para acessar o método privado converterParaRTF
$reflection = new ReflectionClass($templateProcessor);
$method = $reflection->getMethod('converterParaRTF');
$method->setAccessible(true);

$textoRTF = $method->invoke($templateProcessor, $textoOriginal);

echo "Texto convertido para RTF:\n";
echo "-------------------\n";
echo substr($textoRTF, 0, 500) . "...\n";
echo "-------------------\n";

// Contar ocorrências de \par (marcador de parágrafo RTF)
$parCount = substr_count($textoRTF, '\par');
echo "Marcadores \\par encontrados: " . $parCount . "\n\n";

// Verificar se as quebras foram preservadas
if ($parCount >= 2) {
    echo "✅ SUCESSO: Quebras de linha foram convertidas para \\par!\n";
    echo "   O texto será exibido com parágrafos separados no OnlyOffice.\n";
} else {
    echo "❌ FALHA: Quebras de linha NÃO foram preservadas!\n";
    echo "   O texto aparecerá em uma única linha no OnlyOffice.\n";
}

echo "\n";
echo "4. Detalhes da conversão:\n";
echo "-------------------\n";

// Mostrar onde estão os \par
$lines = explode('\par', $textoRTF);
foreach ($lines as $i => $line) {
    if ($i < count($lines) - 1) {
        echo "Parágrafo " . ($i + 1) . ": " . substr(trim($line), 0, 50) . "...\n";
    }
}

echo "\n";
echo "================================================\n";
echo "Para verificar no navegador:\n";
echo "1. Acesse: http://localhost:8001/proposicoes/" . $proposicao->id . "\n";
echo "2. Clique em 'Continuar Editando' para abrir no OnlyOffice\n";
echo "3. Verifique se o texto aparece com 3 parágrafos separados\n";
echo "================================================\n";