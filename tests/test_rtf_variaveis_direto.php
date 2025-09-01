<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TemplateUniversal;

echo "===== TESTE DIRETO DE VARIÁVEIS RTF =====\n\n";

// Buscar template
$template = TemplateUniversal::find(1);
if (! $template) {
    echo "❌ Template não encontrado\n";
    exit(1);
}

// Verificar conteúdo antes do processamento
echo "1. CONTEÚDO NO BANCO:\n";
echo '   Tamanho: '.strlen($template->conteudo)." bytes\n";
if ($template->conteudo) {
    $primeiros100 = substr($template->conteudo, 0, 100);
    echo '   Primeiros 100 chars: '.$primeiros100."\n";

    // Procurar por variáveis
    $pos = strpos($template->conteudo, 'cabecalho_nome');
    if ($pos !== false) {
        echo '   Contexto da variável: '.substr($template->conteudo, $pos - 10, 40)."\n";
    }
} else {
    echo "   Conteúdo vazio (será criado)\n";
}

echo "\n2. TESTANDO CRIAÇÃO DO TEMPLATE BASE:\n";

// Criar template base diretamente
$conteudoBase = '{\rtf1\ansi\ansicpg65001\deff0 {\fonttbl {\f0 Arial;}}
\f0\fs24\sl360\slmult1 

\b\fs28 TEMPLATE UNIVERSAL\b0\fs24\par
\par

\b VARIÁVEIS:\b0\par
${cabecalho_nome_camara}\par
${texto}\par
${ementa}\par

}';

echo "   Template base criado\n";
echo '   Contém ${cabecalho_nome_camara}: '.(strpos($conteudoBase, '${cabecalho_nome_camara}') !== false ? 'SIM' : 'NÃO')."\n";

// Salvar no banco
$template->conteudo = $conteudoBase;
$template->save();

echo "\n3. VERIFICANDO APÓS SALVAR:\n";
$template->refresh();
$pos = strpos($template->conteudo, 'cabecalho_nome');
if ($pos !== false) {
    echo '   Contexto da variável: '.substr($template->conteudo, $pos - 10, 40)."\n";
}

// Agora fazer download via HTTP
echo "\n4. TESTANDO DOWNLOAD VIA HTTP:\n";
$response = file_get_contents('http://localhost/api/templates/universal/1/download');
echo '   Tamanho do download: '.strlen($response)." bytes\n";

// Verificar variáveis no download
$variaveis = ['${cabecalho_nome_camara}', '${texto}', '${ementa}'];
foreach ($variaveis as $var) {
    if (strpos($response, $var) !== false) {
        echo "   ✅ $var encontrada SEM escape\n";
    } else {
        // Verificar com escape
        $varEscapada = str_replace(['$', '{', '}'], ['\\$', '\\{', '\\}'], $var);
        if (strpos($response, $varEscapada) !== false) {
            echo "   ⚠️ $var encontrada COM escape (problema!)\n";
        } else {
            echo "   ❌ $var NÃO encontrada\n";
        }
    }
}

// Salvar arquivo para análise
file_put_contents('/tmp/test_rtf_direto.rtf', $response);
echo "\n💾 Arquivo salvo em: /tmp/test_rtf_direto.rtf\n";

echo "\n✅ TESTE CONCLUÍDO!\n";
