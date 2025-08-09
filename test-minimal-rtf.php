<?php

// Teste RTF mínimo para OnlyOffice
echo "Criando RTF mínimo para teste...\n";

$rtf = '{\rtf1\ansi\deff0
{\fonttbl{\f0 Times New Roman;}}
\f0\fs24
{\b TESTE RTF SIMPLES}\par
Este é um teste básico de RTF.\par
\par
{\b Art. 1º} Este artigo é um teste.\par
{\b Art. 2º} Este RTF deve abrir no OnlyOffice.\par
}';

file_put_contents(__DIR__ . '/test-minimal.rtf', $rtf);
echo "RTF mínimo criado: " . strlen($rtf) . " bytes\n";
echo "Arquivo: " . __DIR__ . "/test-minimal.rtf\n";