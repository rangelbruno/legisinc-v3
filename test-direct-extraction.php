<?php
$rtfFile = '/home/bruno/legisinc/storage/app/public/proposicoes/proposicao_53_template_4.rtf';
$content = file_get_contents($rtfFile);

// Extrair apenas o último pedaço que contém o conteúdo real
$endPart = substr($content, -5000); // Últimos 5000 caracteres

// Agora aplicar nossa decodificação Unicode
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
}, $endPart);

// Remove RTF commands
$text = preg_replace('/\\\\[a-z]+[-]?\d*\s?/i', '', $text);
$text = str_replace(['{', '}', '\\'], '', $text);

// Clean up
$text = preg_replace('/\s+/', ' ', $text);
$text = trim($text);

echo "Texto extraído da parte final:\n";
echo $text . "\n\n";

// Procurar por padrões específicos
if (preg_match('/0053.*?Projeto.*?Ordinária/i', $text)) {
    echo "Encontrado padrão da proposição!\n";
}

if (preg_match('/Ementa.*?Bruno/i', $text)) {
    echo "Encontrado ementa com Bruno!\n";
}