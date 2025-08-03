#!/bin/bash

echo "Creating RTF template with proper Unicode escape sequences..."

# Create RTF with Unicode escape sequences for Portuguese characters
cat > /tmp/template_unicode.rtf << 'EOF'
{\rtf1\ansi\ansicpg65001\deff0\deflang1046
{\fonttbl {\f0 Times New Roman;}}
{\colortbl;\red0\green0\blue0;}
\f0\fs24

{\qc\b\fs32 MO\u231*\u195*O N\u186* ${numero_proposicao}\par}
\par
{\qc Data: ${data_atual}\par}
{\qc Autor: ${autor_nome}\par}
{\qc Munic\u237*pio: ${municipio}\par}
\par\par
{\b\fs28 EMENTA\par}
\par
${ementa}\par
\par\par
{\b\fs28 TEXTO\par}
\par
${texto}\par
\par\par
{\qr C\u226*mara Municipal de ${municipio}\par}
{\qr ${data_atual}\par}
}
EOF

echo "Created Unicode RTF template at /tmp/template_unicode.rtf"

# Also create a function to convert UTF-8 to RTF Unicode sequences
cat > /tmp/convert_utf8_to_rtf.php << 'EOF'
<?php
function convertUtf8ToRtfUnicode($text) {
    $result = '';
    $length = mb_strlen($text, 'UTF-8');
    
    for ($i = 0; $i < $length; $i++) {
        $char = mb_substr($text, $i, 1, 'UTF-8');
        $codepoint = mb_ord($char, 'UTF-8');
        
        if ($codepoint > 127) {
            // Convert to RTF Unicode escape
            $result .= '\u' . $codepoint . '*';
        } else {
            $result .= $char;
        }
    }
    
    return $result;
}

// Test with some Portuguese text
$tests = [
    'São Paulo',
    'Endereço da Câmara',
    'Brasília',
    'João',
    'acentuação'
];

foreach ($tests as $test) {
    echo "Original: $test\n";
    echo "RTF Unicode: " . convertUtf8ToRtfUnicode($test) . "\n\n";
}
?>
EOF

echo -e "\n\nTesting UTF-8 to RTF Unicode conversion:"
php /tmp/convert_utf8_to_rtf.php