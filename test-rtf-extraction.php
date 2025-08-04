<?php

$rtfFile = '/home/bruno/legisinc/storage/app/public/proposicoes/proposicao_53_template_4.rtf';
$rtfContent = file_get_contents($rtfFile);

// Primeiro, vamos ver alguns unicode sequences
preg_match_all('/\\\\u(\d+)\*/', $rtfContent, $matches);
echo "Primeiros 10 Unicode sequences encontrados:\n";
for ($i = 0; $i < min(10, count($matches[0])); $i++) {
    $code = intval($matches[1][$i]);
    $char = chr($code);
    echo "\\u{$matches[1][$i]}* = código $code = char '$char'\n";
}

// Testar nossa regex
$text = $rtfContent;

// Remover grupos de controle do cabeçalho
$text = preg_replace('/\{\\\\fonttbl[^}]*\}+/', '', $text);
$text = preg_replace('/\{\\\\colortbl[^}]*\}+/', '', $text);
$text = preg_replace('/\{\\\\stylesheet[^}]*\}+/', '', $text);

// Decodificar Unicode com asterisco
$text = preg_replace_callback('/\\\\u(-?\d+)\*/', function($matches) {
    $code = intval($matches[1]);
    if ($code < 0) {
        $code = 65536 + $code;
    }
    if ($code < 128) {
        return chr($code);
    } else {
        return mb_convert_encoding(pack('n', $code), 'UTF-8', 'UTF-16BE');
    }
}, $text);

// Converter comandos de formatação
$text = str_replace(['\\par', '\\line'], "\n", $text);

// Remover comandos RTF
$text = preg_replace('/\\\\\w+\d*\s?/', '', $text);

// Remover chaves
$text = str_replace(['{', '}'], '', $text);

// Limpar
$text = trim($text);

echo "\n\nPrimeiros 500 caracteres do texto extraído:\n";
echo substr($text, 0, 500) . "\n";
