#!/bin/bash

echo "Testing RTF Unicode conversion fix..."

# Create a test PHP script to validate the conversion
cat > /tmp/test_conversion.php << 'EOF'
<?php

// Test the conversion function
function converterUtf8ParaRtf($texto) {
    $resultado = '';
    $length = mb_strlen($texto, 'UTF-8');
    
    for ($i = 0; $i < $length; $i++) {
        $char = mb_substr($texto, $i, 1, 'UTF-8');
        $codepoint = mb_ord($char, 'UTF-8');
        
        if ($codepoint > 127) {
            $resultado .= '\u' . $codepoint . '*';
        } else {
            $resultado .= $char;
        }
    }
    
    return $resultado;
}

// Test cases
$tests = [
    'São Paulo',
    'Endereço da Câmara', 
    'Brasília',
    'João',
    'acentuação',
    'Município de São Paulo'
];

echo "Testing UTF-8 to RTF Unicode conversion:\n\n";

foreach ($tests as $test) {
    $converted = converterUtf8ParaRtf($test);
    echo "Original: $test\n";
    echo "RTF:      $converted\n\n";
}

// Create a complete RTF test file
$template = '{\rtf1\ansi\ansicpg65001\deff0\deflang1046
{\fonttbl {\f0 Times New Roman;}}
{\colortbl;\red0\green0\blue0;}
\f0\fs24

{\qc\b\fs32 TESTE DE ACENTUAÇÃO\par}
\par
' . converterUtf8ParaRtf('São Paulo') . '\par
' . converterUtf8ParaRtf('Endereço da Câmara') . '\par
' . converterUtf8ParaRtf('Brasília') . '\par
' . converterUtf8ParaRtf('João') . '\par
\par
}';

file_put_contents('/tmp/test_rtf_unicode.rtf', $template);
echo "Created RTF test file: /tmp/test_rtf_unicode.rtf\n";
echo "Contents:\n";
echo $template;

?>
EOF

php /tmp/test_conversion.php

echo -e "\n\nFile created. You can test this RTF file in OnlyOffice to verify the encoding works properly."