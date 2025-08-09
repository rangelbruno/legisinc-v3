<?php

// Teste do processamento RTF do conteúdo IA
$conteudoIA = '**MUNICÍPIO DE [Nome do Município]**

**PODER LEGISLATIVO**

**CÂMARA MUNICIPAL**

**MOÇÃO Nº [Número da Moção], DE [Data]**

**MOÇÃO DE APLAUSOS**

O(A) Vereador(a) [Nome do Vereador(a) proponente], membro desta Egrégia Câmara Municipal, vem, respeitosamente, à presença dos Nobres Pares, apresentar a seguinte MOÇÃO DE APLAUSOS:

**Considerando** a importância da valorização dos cidadãos que se destacam por suas contribuições à comunidade;

**Considerando** os relevantes serviços prestados por Bruno José Pereira Rangel ao município de [Nome do Município];

**Considerando** [inserir aqui os feitos de Bruno José Pereira Rangel que justificam a moção, sendo detalhado e específico];

**Resolve esta Egrégia Câmara Municipal:**

**Art. 1º** Aplaudir e homenagear o cidadão Bruno José Pereira Rangel pelos relevantes serviços prestados ao município de [Nome do Município], conforme descrito nos considerandos desta Moção.

**Art. 2º** Determinar que cópia desta Moção seja encaminhada a Bruno José Pereira Rangel.

**Art. 3º** Esta Moção entra em vigor na data de sua aprovação.';

echo "Testando processamento RTF do conteúdo IA...\n";
echo "Tamanho do conteúdo original: " . strlen($conteudoIA) . " bytes\n\n";

// Simular o processamento
$linhas = explode("\n", strip_tags($conteudoIA));
$linhasNaoVazias = array_filter($linhas, fn($l) => !empty(trim($l)));

echo "Total de linhas: " . count($linhas) . "\n";
echo "Linhas não vazias: " . count($linhasNaoVazias) . "\n\n";

echo "Primeiras 10 linhas processadas:\n";
foreach (array_slice($linhasNaoVazias, 0, 10) as $i => $linha) {
    $linha = trim($linha);
    
    echo ($i + 1) . ". ";
    
    if (preg_match('/^\*\*Art\.?\s*(\d+).*?\*\*(.*)/', $linha, $matches)) {
        echo "[ARTIGO] Art. {$matches[1]}º - " . trim($matches[2]) . "\n";
    } elseif (preg_match('/^Art\.?\s*(\d+).*?[\.\-\s](.*)/', $linha, $matches)) {
        echo "[ARTIGO] Art. {$matches[1]}º - " . trim($matches[2]) . "\n";
    } elseif (preg_match('/^\*\*(.+?)\*\*(.*)/', $linha, $matches)) {
        echo "[TÍTULO] " . trim($matches[1]) . " - " . trim($matches[2]) . "\n";
    } elseif (str_starts_with($linha, 'Considerando') || str_starts_with($linha, '**Considerando')) {
        echo "[CONSIDERANDO] " . str_replace(['**', '*'], '', $linha) . "\n";
    } elseif (str_starts_with($linha, 'Resolve') || str_starts_with($linha, '**Resolve')) {
        echo "[RESOLVE] " . str_replace(['**', '*'], '', $linha) . "\n";
    } else {
        echo "[TEXTO] " . substr($linha, 0, 80) . (strlen($linha) > 80 ? '...' : '') . "\n";
    }
}

// Testar geração RTF simples
$rtfTeste = "{\rtf1\ansi\deff0 
{\fonttbl 
{\f0\froman\fcharset0 Times New Roman;}
}
{\colortbl;\red0\green0\blue0;}
\f0\fs24

{\qc\b\fs28 CÂMARA MUNICIPAL DE SÃO PAULO\par}
\par\par

{\qc\b\fs24\caps MOÇÃO Nº 0010, DE 2025\par}
\par

{\b EMENTA:}\par
Moção de aplausos para Bruno José Pereira Rangel\par
\par

O(A) Vereador(a) que este subscreve, no uso das atribuições que lhe confere o Regimento Interno desta Casa Legislativa, apresenta a presente proposição:\par
\par

{\b MUNICÍPIO DE [Nome do Município]}\par\par
{\b PODER LEGISLATIVO}\par\par
{\b CÂMARA MUNICIPAL}\par\par
{\b MOÇÃO Nº [Número da Moção], DE [Data]}\par\par
{\b MOÇÃO DE APLAUSOS}\par\par

O(A) Vereador(a) [Nome do Vereador(a) proponente], membro desta Egrégia Câmara Municipal, vem, respeitosamente, à presença dos Nobres Pares, apresentar a seguinte MOÇÃO DE APLAUSOS:\par\par

{\b Considerando} a importância da valorização dos cidadãos que se destacam por suas contribuições à comunidade;\par\par

{\b Considerando} os relevantes serviços prestados por Bruno José Pereira Rangel ao município de [Nome do Município];\par\par

{\b Resolve esta Egrégia Câmara Municipal:}\par\par

{\b Art. 1º} Aplaudir e homenagear o cidadão Bruno José Pereira Rangel pelos relevantes serviços prestados ao município de [Nome do Município], conforme descrito nos considerandos desta Moção.\par\par

{\b Art. 2º} Determinar que cópia desta Moção seja encaminhada a Bruno José Pereira Rangel.\par\par

{\b Art. 3º} Esta Moção entra em vigor na data de sua aprovação.\par\par

{\qr São Paulo, 08 de agosto de 2025.\par}
\par\par

{\qc __________________________________\par}
\par
{\qc\b Jessica Santos\par}
{\qc Vereador(a)\par}

}";

echo "\n\nTamanho do RTF gerado: " . strlen($rtfTeste) . " bytes\n";

// Salvar RTF teste
$arquivoTeste = __DIR__ . '/test-rtf-mocao.rtf';
file_put_contents($arquivoTeste, $rtfTeste);

echo "RTF teste salvo em: $arquivoTeste\n";
echo "Você pode abrir este arquivo no Word/LibreOffice para verificar o resultado.\n";

echo "\nTeste concluído!\n";