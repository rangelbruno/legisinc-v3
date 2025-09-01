<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TemplateUniversal;

echo "===== CORREÇÃO FINAL DO TEMPLATE UNIVERSAL =====\n\n";

$template = TemplateUniversal::find(1);
if (! $template) {
    echo "❌ Template não encontrado\n";
    exit(1);
}

// RTF correto com variáveis SEM escape
$rtfCorreto = '{\rtf1\ansi\ansicpg65001\deff0 {\fonttbl {\f0 Arial;}}
\f0\fs24\sl360\slmult1 

\b\fs28 TEMPLATE UNIVERSAL - PROPOSIÇÕES LEGISLATIVAS\b0\fs24\par
\par

\b CABEÇALHO INSTITUCIONAL:\b0\par
${imagem_cabecalho}\par
${cabecalho_nome_camara}\par
${cabecalho_endereco}\par
Tel: ${cabecalho_telefone} - ${cabecalho_website}\par
CNPJ: ${cnpj_camara}\par
\par

\line\par
\par

\qc\b\fs26 ${tipo_proposicao} N° ${numero_proposicao}\b0\fs24\par
\ql\par

\b EMENTA:\b0 ${ementa}\par
\par

\b PREÂMBULO DINÂMICO:\b0\par
[Este campo se adapta automaticamente ao tipo de proposição]\par
\par

\b CONTEÚDO PRINCIPAL:\b0\par
${texto}\par
\par

\b JUSTIFICATIVA:\b0\par
${justificativa}\par
\par

\b ARTICULADO (Para Projetos de Lei):\b0\par
Art. 1° [Disposição principal]\par
\par
Parágrafo único. [Detalhamento se necessário]\par
\par
Art. 2° [Disposições complementares]\par
\par
Art. 3° Esta lei entra em vigor na data de sua publicação.\par
\par

\line\par
\par

\b ÁREA DE ASSINATURA:\b0\par
${municipio}, ${dia} de ${mes_extenso} de ${ano_atual}.\par
\par
${assinatura_padrao}\par
${autor_nome}\par
${autor_cargo}\par
\par

\b RODAPÉ INSTITUCIONAL:\b0\par
${rodape_texto}\par
${endereco_camara}, ${endereco_bairro} - CEP: ${endereco_cep}\par
${municipio}/${municipio_uf} - Tel: ${telefone_camara}\par
${website_camara} - ${email_camara}\par

}';

// Salvar no banco
$template->conteudo = $rtfCorreto;
$template->document_key = 'template_universal_'.time().'_final_ok';
$template->save();

echo "✅ Template corrigido e salvo!\n\n";

// Verificar o que foi salvo
echo "Verificando conteúdo salvo:\n";
echo '- Começa com {\\rtf1: '.(str_starts_with($template->conteudo, '{\rtf1') ? 'SIM ✅' : 'NÃO ❌')."\n";
echo '- Contém ${cabecalho_nome_camara}: '.(str_contains($template->conteudo, '${cabecalho_nome_camara}') ? 'SIM ✅' : 'NÃO ❌')."\n";
echo '- Contém \\par: '.(str_contains($template->conteudo, '\par') ? 'SIM ✅' : 'NÃO ❌')."\n";

// Testar download
echo "\nTestando download via API:\n";
$ch = curl_init('http://localhost/api/templates/universal/1/download');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

if ($response) {
    echo '- Download realizado: '.strlen($response)." bytes\n";
    echo '- RTF válido: '.(str_starts_with($response, '{\rtf1') ? 'SIM ✅' : 'NÃO ❌')."\n";

    // Verificar variáveis
    $variaveis = ['${cabecalho_nome_camara}', '${texto}', '${ementa}'];
    foreach ($variaveis as $var) {
        if (strpos($response, $var) !== false) {
            echo "- $var encontrada SEM escape ✅\n";
        } else {
            echo "- $var NÃO encontrada ou com escape ❌\n";
        }
    }

    // Salvar para análise
    file_put_contents('/tmp/template_universal_final.rtf', $response);
    echo "\n💾 Arquivo salvo em: /tmp/template_universal_final.rtf\n";
}

echo "\n✅ CORREÇÃO FINALIZADA!\n";
echo "Document key: {$template->document_key}\n";
echo "\nAgora acesse: http://localhost:8001/admin/templates/universal/editor/1\n";
echo "O editor deve abrir diretamente sem diálogo de codificação e com as variáveis visíveis.\n";
