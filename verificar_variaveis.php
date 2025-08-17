<?php

echo "=== VERIFICAÇÃO DAS VARIÁVEIS DE TEMPLATE ===\n\n";

// Verificar se as variáveis antigas foram removidas
$variaveisAntigas = [
    'cabecalho_nome_camara',
    'cabecalho_endereco', 
    'cabecalho_telefone',
    'cabecalho_website'
];

echo "❌ Variáveis REMOVIDAS (não devem aparecer):\n";
foreach($variaveisAntigas as $var) {
    echo "   - {$var}\n";
}

echo "\n";

// Verificar se as variáveis novas foram adicionadas
$variaveisNovas = [
    'assinatura_digital_info',
    'qrcode_html'
];

echo "✅ Variáveis ADICIONADAS (devem aparecer):\n";
foreach($variaveisNovas as $var) {
    echo "   - {$var}\n";
}

echo "\n";

// Variáveis que devem permanecer
$variaveisMantidas = [
    'cabecalho_imagem'
];

echo "🔄 Variáveis MANTIDAS:\n";
foreach($variaveisMantidas as $var) {
    echo "   - {$var}\n";
}

echo "\n=== ACESSE /admin/templates PARA VERIFICAR ===\n";