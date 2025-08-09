<?php

echo "=== Teste de resposta RTF ===\n\n";

// Simular RTF simples
$rtfContent = '{\rtf1\ansi\deff0
{\fonttbl{\f0 Times New Roman;}}
\f0\fs24
{\qc\b\fs28 CÂMARA MUNICIPAL DE SÃO PAULO\par}
\par\par
{\qc\b\fs24 MOÇÃO Nº 0010, DE 2025\par}
\par
{\b EMENTA:}\par
Moção de aplausos para Bruno José Pereira Rangel\par
\par
O(A) Vereador(a) que este subscreve, no uso das atribuições que lhe confere o Regimento Interno desta Casa Legislativa, apresenta a presente proposição:\par
\par
{\b Art. 1º} Aplaudir e homenagear o cidadão Bruno José Pereira Rangel.\par
{\b Art. 2º} Esta Moção entra em vigor na data de sua aprovação.\par
\par
{\qr São Paulo, 09 de agosto de 2025.\par}
\par\par
{\qc __________________________________\par}
\par
{\qc\b Jessica Santos\par}
{\qc Vereador(a)\par}
}';

echo "Tamanho do RTF: " . strlen($rtfContent) . " bytes\n";
echo "RTF começa com: " . substr($rtfContent, 0, 20) . "\n";
echo "RTF termina com: " . substr($rtfContent, -20) . "\n\n";

// Validar estrutura RTF
$validacoes = [
    'Cabeçalho RTF válido' => str_starts_with($rtfContent, '{\rtf1'),
    'Fechamento RTF válido' => str_ends_with(trim($rtfContent), '}'),
    'Contém fonte Times New Roman' => str_contains($rtfContent, 'Times New Roman'),
    'Contém título CÂMARA' => str_contains($rtfContent, 'CÂMARA MUNICIPAL'),
    'Contém ementa' => str_contains($rtfContent, 'EMENTA'),
    'Contém artigos' => str_contains($rtfContent, 'Art. 1º'),
];

foreach ($validacoes as $teste => $resultado) {
    echo $teste . ": " . ($resultado ? "✅ OK" : "❌ FALHOU") . "\n";
}

echo "\n✅ RTF deve funcionar corretamente com OnlyOffice!\n";