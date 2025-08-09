<?php

// Teste do RTF final para proposição
echo "Gerando RTF de teste final...\n";

// Simular dados da proposição
$conteudoIA = '**MUNICÍPIO DE [Nome do Município]**

**PODER LEGISLATIVO**

**CÂMARA MUNICIPAL**

**MOÇÃO Nº [Número da Moção], DE [Data]**

**MOÇÃO DE APLAUSOS**

O(A) Vereador(a) [Nome do Vereador(a) proponente], membro desta Egrégia Câmara Municipal, vem, respeitosamente, à presença dos Nobres Pares, apresentar a seguinte MOÇÃO DE APLAUSOS:

**Considerando** a importância da valorização dos cidadãos que se destacam por suas contribuições à comunidade;

**Considerando** os relevantes serviços prestados por Bruno José Pereira Rangel ao município de [Nome do Município];

**Resolve esta Egrégia Câmara Municipal:**

**Art. 1º** Aplaudir e homenagear o cidadão Bruno José Pereira Rangel pelos relevantes serviços prestados.

**Art. 2º** Determinar que cópia desta Moção seja encaminhada a Bruno José Pereira Rangel.

**Art. 3º** Esta Moção entra em vigor na data de sua aprovação.';

// Função de escape simples
function escaparRTF($texto) {
    $texto = str_replace(['\\', '{', '}'], ['\\\\', '\\{', '\\}'], $texto);
    $texto = str_replace(['"', "'", "'", '"', '"'], ['"', "'", "'", '"', '"'], $texto);
    return $texto;
}

// Processar conteúdo IA
function processarConteudoIA($conteudo) {
    $rtf = '';
    $linhas = explode("\n", strip_tags($conteudo));
    
    foreach ($linhas as $linha) {
        $linha = trim($linha);
        if (empty($linha)) continue;
        
        if (preg_match('/^\*\*Art\.?\s*(\d+).*?\*\*(.*)/', $linha, $matches)) {
            $numero = $matches[1];
            $texto = trim($matches[2]);
            $rtf .= "{\b Art. {$numero}º} " . escaparRTF($texto) . "\par\par\n";
        } elseif (preg_match('/^\*\*(.+?)\*\*(.*)/', $linha, $matches)) {
            $titulo = trim($matches[1]);
            $resto = trim($matches[2]);
            if ($resto) {
                $rtf .= "{\b " . escaparRTF($titulo) . "} " . escaparRTF($resto) . "\par\par\n";
            } else {
                $rtf .= "{\qc\b " . escaparRTF($titulo) . "\par}\par\n";
            }
        } elseif (preg_match('/^\*\*?Considerando/', $linha)) {
            $texto = str_replace(['**', '*'], '', $linha);
            $rtf .= "{\b " . escaparRTF($texto) . "}\par\par\n";
        } elseif (preg_match('/^\*\*?Resolve/', $linha)) {
            $texto = str_replace(['**', '*'], '', $linha);
            $rtf .= "{\b " . escaparRTF($texto) . "}\par\par\n";
        } else {
            $rtf .= escaparRTF($linha) . "\par\par\n";
        }
    }
    
    return $rtf;
}

// Gerar RTF completo
$rtf = "{\rtf1\ansi\deff0
{\fonttbl{\f0 Times New Roman;}}
{\colortbl;\red0\green0\blue0;}
\margl1701\margr1134\margt1701\margb1134
\f0\fs24\cf1

{\qc\b\fs28 CÂMARA MUNICIPAL DE SÃO PAULO\par}
{\qc Viaduto Jacareí, 100 - Bela Vista - São Paulo/SP\par}
{\qc Legislatura: 2021-2024 - Sessão: 2025\par}
\par\par

{\qc\b\fs24\caps MOÇÃO Nº 0010, DE 2025\par}
\par

{\b EMENTA:}\par
" . escaparRTF('Moção de aplausos para Bruno José Pereira Rangel') . "\par
\par

O(A) Vereador(a) que este subscreve, no uso das atribuições que lhe confere o Regimento Interno desta Casa Legislativa, apresenta a presente proposição:\par
\par

" . processarConteudoIA($conteudoIA) . "

{\qr São Paulo, " . date('d') . " de agosto de " . date('Y') . ".\par}
\par\par

{\qc __________________________________\par}
\par
{\qc\b Jessica Santos\par}
{\qc Vereador(a)\par}

}";

// Salvar arquivo
$arquivo = __DIR__ . '/test-rtf-final.rtf';
file_put_contents($arquivo, $rtf);

echo "RTF gerado: " . strlen($rtf) . " bytes\n";
echo "Arquivo salvo: $arquivo\n";

// Verificar estrutura RTF básica
$verificacoes = [
    'Cabeçalho RTF' => str_contains($rtf, '{\rtf1'),
    'Fonte Times New Roman' => str_contains($rtf, 'Times New Roman'),
    'Conteúdo processado' => strlen($rtf) > 2000,
    'Epígrafe presente' => str_contains($rtf, 'MOÇÃO Nº 0010'),
    'Ementa presente' => str_contains($rtf, 'Bruno José Pereira Rangel'),
    'Artigos processados' => str_contains($rtf, 'Art. 1º'),
    'Fechamento correto' => str_ends_with(trim($rtf), '}')
];

echo "\nVerificações:\n";
foreach ($verificacoes as $teste => $resultado) {
    echo "- $teste: " . ($resultado ? '✅ OK' : '❌ FALHOU') . "\n";
}

echo "\nTeste concluído! Abra o arquivo RTF para verificar se o OnlyOffice consegue abrir.\n";