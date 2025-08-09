<?php

// Script de teste para template ABNT
require_once __DIR__ . '/vendor/autoload.php';

// Simular dados de proposição
$dadosProposicao = [
    'tipo' => 'mocao',
    'ementa' => 'Moção de aplausos para Bruno José Pereira Rangel',
    'conteudo' => '**MUNICÍPIO DE [Nome do Município]** **PODER LEGISLATIVO** **CÂMARA MUNICIPAL** **MOÇÃO Nº [Número da Moção], DE [Data]** **MOÇÃO DE APLAUSOS** O(A) Vereador(a) [Nome do Vereador(a) proponente], membro desta Egrégia Câmara Municipal, vem, respeitosamente, à presença dos Nobres Pares, apresentar a seguinte MOÇÃO DE APLAUSOS: **Considerando** a importância da valorização dos cidadãos que se destacam por suas contribuições à comunidade; **Considerando** os relevantes serviços prestados por Bruno José Pereira Rangel ao município de [Nome do Município]; **Art. 1º** Aplaudir e homenagear o cidadão Bruno José Pereira Rangel pelos relevantes serviços prestados.',
    'numero' => '0010',
    'status' => 'em_edicao',
    'created_at' => new DateTime(),
    'autor_nome' => 'Jessica Santos',
    'nome_parlamentar' => 'Jessica Santos',
    'cargo_parlamentar' => 'Vereador(a)',
    'email_parlamentar' => 'jessica@sistema.gov.br',
    'partido_parlamentar' => ''
];

// Carregarhtml template
$templatePath = __DIR__ . '/storage/templates/template_padrao_abnt.html';

if (!file_exists($templatePath)) {
    echo "Erro: Template ABNT não encontrado em: $templatePath\n";
    exit(1);
}

$templateHTML = file_get_contents($templatePath);
echo "Template ABNT carregado: " . strlen($templateHTML) . " bytes\n";

// Simular substituição de algumas variáveis básicas
$variaveis = [
    'tipo_proposicao' => 'MOÇÃO',
    'numero_proposicao' => '0010',
    'ano_atual' => date('Y'),
    'ementa' => $dadosProposicao['ementa'],
    'texto' => $dadosProposicao['conteudo'],
    'nome_parlamentar' => $dadosProposicao['nome_parlamentar'],
    'cargo_parlamentar' => $dadosProposicao['cargo_parlamentar'],
    'municipio' => 'São Paulo',
    'data_extenso' => '08 de agosto de 2025',
    'nome_camara' => 'CÂMARA MUNICIPAL DE SÃO PAULO',
    'endereco_camara' => 'Viaduto Jacareí, 100 - Bela Vista - São Paulo/SP',
    'legislatura_atual' => '2021-2024',
    'sessao_legislativa' => '2025',
    'imagem_cabecalho' => '',
    'numero_artigo_final' => '2',
    'numero_artigo_revogacao' => '3',
    'justificativa' => 'A presente moção justifica-se pela necessidade de reconhecer publicamente os méritos de Bruno José Pereira Rangel, cujas contribuições para nossa comunidade são dignas de nota e aplausos.'
];

// Processar template
$documentoProcessado = $templateHTML;
foreach ($variaveis as $nome => $valor) {
    $documentoProcessado = str_replace('${' . $nome . '}', $valor, $documentoProcessado);
}

// Salvar resultado
$resultadoPath = __DIR__ . '/test-resultado-abnt.html';
file_put_contents($resultadoPath, $documentoProcessado);

echo "Documento processado salvo em: $resultadoPath\n";

// Validação básica ABNT
$problemas = [];

// Verificar font-family
if (!preg_match('/font-family:\s*["\']?Times New Roman["\']?/i', $documentoProcessado)) {
    $problemas[] = 'Fonte principal não é Times New Roman';
}

// Verificar font-size 12pt
if (!preg_match('/font-size:\s*12pt/i', $documentoProcessado)) {
    $problemas[] = 'Tamanho do corpo principal não é 12pt';
}

// Verificar line-height 1.5
if (!preg_match('/line-height:\s*1\.5/i', $documentoProcessado)) {
    $problemas[] = 'Espaçamento principal não é 1.5';
}

// Verificar margens ABNT
if (!preg_match('/margin:\s*3cm\s+2cm\s+2cm\s+3cm/i', $documentoProcessado)) {
    $problemas[] = 'Margens não seguem padrão ABNT';
}

// Verificar estrutura básica
$elementos = ['epigrafe', 'ementa', 'preambulo', 'assinatura'];
foreach ($elementos as $elemento) {
    if (!preg_match('/class=["\'][^"\']*' . $elemento . '[^"\']*["\']/', $documentoProcessado)) {
        $problemas[] = "Elemento '$elemento' não encontrado";
    }
}

// Exibir resultado da validação
if (empty($problemas)) {
    echo "✅ Validação ABNT: APROVADO - Documento conforme às normas\n";
} else {
    echo "⚠️  Validação ABNT: PROBLEMAS ENCONTRADOS:\n";
    foreach ($problemas as $problema) {
        echo "   - $problema\n";
    }
}

echo "\nTeste concluído! Abra o arquivo $resultadoPath em um navegador para visualizar o resultado.\n";