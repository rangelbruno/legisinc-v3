<?php
$rtfFile = '/home/bruno/legisinc/storage/app/public/proposicoes/proposicao_53_template_4.rtf';
$content = file_get_contents($rtfFile);

echo "Tamanho do arquivo: " . strlen($content) . " bytes\n";

// Ver o final do arquivo onde pode estar o conteúdo real
echo "\nÚltimos 1000 caracteres do arquivo:\n";
echo substr($content, -1000) . "\n";

// Procurar por texto específico que sabemos que deve estar lá
echo "\n\nProcurando por 'Bruno' no arquivo:\n";
if (strpos($content, 'Bruno') \!== false) {
    echo "Encontrado 'Bruno' como texto simples\n";
} else {
    echo "Não encontrado 'Bruno' como texto simples\n";
    
    // Procurar por 'Bruno' em Unicode
    // B=66, r=114, u=117, n=110, o=111
    $unicodeBruno = '\u66*\u114*\u117*\u110*\u111*';
    if (strpos($content, $unicodeBruno) \!== false) {
        echo "Encontrado 'Bruno' em formato Unicode\n";
        $pos = strpos($content, $unicodeBruno);
        echo "Contexto: " . substr($content, $pos - 100, 300) . "\n";
    }
}

// Contar quantos \u temos no arquivo
$unicodeCount = substr_count($content, '\u');
echo "\nTotal de sequências Unicode (\u) no arquivo: $unicodeCount\n";

// Ver algumas sequências Unicode para debug
preg_match_all('/\\\u\d+\*/', $content, $matches);
echo "Primeiras 20 sequências Unicode encontradas:\n";
foreach (array_slice($matches[0], 0, 20) as $match) {
    $code = intval(substr($match, 2, -1));
    $char = $code < 128 ? chr($code) : '?';
    echo "$match = $code = '$char'\n";
}
