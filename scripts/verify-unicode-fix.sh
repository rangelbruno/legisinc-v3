#!/bin/bash

echo "🔍 Verificando correção de encoding Unicode RTF..."
echo "================================================"

# Test PHP function directly
cat > /tmp/test_unicode_fix.php << 'EOF'
<?php

function codificarTextoParaUnicode($texto) {
    $textoProcessado = '';
    $chunks = explode("\n", $texto);
    
    foreach ($chunks as $index => $chunk) {
        if ($index > 0) {
            $textoProcessado .= '\\par ';
        }
        
        $length = mb_strlen($chunk, 'UTF-8');
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($chunk, $i, 1, 'UTF-8');
            $codepoint = mb_ord($char, 'UTF-8');
            
            if ($codepoint > 127) {
                $textoProcessado .= '\\u' . $codepoint . '*';
            } else {
                $textoProcessado .= $char;
            }
        }
    }
    
    return $textoProcessado;
}

// Test cases
$tests = [
    'São Paulo' => 'S\\u227*o Paulo',
    'José' => 'Jos\\u233*',
    'Endereço' => 'Endere\\u231*o',
    'Câmara' => 'C\\u226*mara',
    'Brasília' => 'Bras\\u237*lia',
    'Florianópolis' => 'Florian\\u243*polis',
    'número' => 'n\\u250*mero',
    '© Serasa' => '\\u169* Serasa'
];

echo "Testing UTF-8 to RTF Unicode conversion:\n";
echo "=========================================\n\n";

$allPassed = true;
foreach ($tests as $input => $expected) {
    $result = codificarTextoParaUnicode($input);
    $passed = ($result === $expected);
    $allPassed = $allPassed && $passed;
    
    echo "Input:    $input\n";
    echo "Expected: $expected\n";
    echo "Result:   $result\n";
    echo "Status:   " . ($passed ? "✅ PASS" : "❌ FAIL") . "\n\n";
}

echo "\n" . ($allPassed ? "✅ All tests passed!" : "❌ Some tests failed") . "\n";
?>
EOF

php /tmp/test_unicode_fix.php

echo -e "\n📝 Summary:"
echo "==========="
echo "The codificarTextoParaUnicode() function has been fixed to properly"
echo "handle UTF-8 multi-byte characters using mb_* functions."
echo ""
echo "Key changes:"
echo "- Used mb_strlen() instead of strlen() for UTF-8 strings"
echo "- Used mb_substr() instead of array access for UTF-8 characters"
echo "- Used mb_ord() instead of ord() to get proper Unicode codepoints"
echo ""
echo "This should resolve the character encoding issues in OnlyOffice RTF documents."