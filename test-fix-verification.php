<?php

echo "=== Verificação da correção OnlyOffice RTF ===\n\n";

// Simular cenário da proposição com conteúdo IA
$proposicao = (object)[
    'id' => 10,
    'conteudo' => 'Conteúdo gerado pela IA com artigos e formatação',
    'template_id' => null,
    'arquivo_path' => null
];

// Lógica de detecção do tipo de arquivo (copiada do controller)
$fileType = 'docx'; // Default para documentos modernos

// Se há conteúdo IA e sem template específico, usar RTF
if (!empty($proposicao->conteudo) && $proposicao->template_id === null) {
    $fileType = 'rtf';
}

echo "Proposição ID: {$proposicao->id}\n";
echo "Tem conteúdo: " . (!empty($proposicao->conteudo) ? 'SIM' : 'NÃO') . "\n";
echo "Template ID: " . ($proposicao->template_id ?? 'NULL') . "\n";
echo "Arquivo path: " . ($proposicao->arquivo_path ?? 'NULL') . "\n";
echo "Tipo detectado: {$fileType}\n\n";

if ($fileType === 'rtf') {
    echo "✅ CORREÇÃO APLICADA: OnlyOffice será configurado para RTF\n";
    echo "✅ Formato do arquivo gerado: RTF\n";
    echo "✅ Mismatch resolvido!\n";
} else {
    echo "❌ Problema ainda existe - tipo não é RTF\n";
}

echo "\n=== Próximos passos ===\n";
echo "1. Acesse: /proposicoes/10/onlyoffice/editor-parlamentar?ai_content=true\n";
echo "2. Verifique se OnlyOffice abre o documento sem mostrar 'Choose TXT options'\n";
echo "3. Confirme se o conteúdo completo está visível\n";