<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use App\Services\RTFTextExtractor;

$rtfFile = storage_path('app/public/proposicoes/proposicao_53_template_4.rtf');
$content = file_get_contents($rtfFile);

// Debug: ver o que está sendo extraído na Step 1
$text = $content;
if (strlen($text) > 100000) {
    $text = substr($text, -10240);
}

echo "Primeiros 500 caracteres após Step 1:\n";
echo substr($text, 0, 500) . "\n\n";

echo "Últimos 500 caracteres após Step 1:\n";
echo substr($text, -500) . "\n\n";

// Aplicar Unicode decoding
$text = preg_replace_callback('/\\\\u(-?\d+)\*/', function($matches) {
    $code = intval($matches[1]);
    if ($code < 0) {
        $code = 65536 + $code;
    }
    
    // Special cases for Portuguese characters
    if ($code === 225) return 'á';
    if ($code === 227) return 'ã';
    if ($code === 231) return 'ç';
    if ($code === 233) return 'é';
    if ($code === 237) return 'í';
    if ($code === 243) return 'ó';
    if ($code === 245) return 'õ';
    if ($code === 250) return 'ú';
    
    if ($code < 128) {
        return chr($code);
    }
    
    return '?';
}, $text);

echo "Após Unicode decoding (primeiros 500):\n";
echo substr($text, 0, 500) . "\n\n";

// Remove RTF commands
$text = preg_replace('/\\\\[a-z]+[-]?\d*\s?/i', '', $text);

echo "Após remoção de comandos RTF (primeiros 500):\n";
echo substr($text, 0, 500) . "\n\n";

// Remove chaves
$text = str_replace(['{', '}'], '', $text);

echo "Após remoção de chaves (primeiros 500):\n";
echo substr($text, 0, 500) . "\n\n";

// Procurar pelos padrões
if (preg_match('/0053.*?Projeto.*?Ordinária/i', $text)) {
    echo "ENCONTRADO padrão da proposição!\n";
} else {
    echo "NÃO encontrado padrão da proposição\n";
}

if (preg_match('/Ementa.*?Bruno/i', $text)) {
    echo "ENCONTRADO ementa com Bruno!\n";
} else {
    echo "NÃO encontrado ementa com Bruno\n";
}