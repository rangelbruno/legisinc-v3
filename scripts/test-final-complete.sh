#!/bin/bash

echo "🎯 TESTE FINAL COMPLETO - Template com Variáveis"
echo "==============================================="

echo ""
echo "1. Confirmando que variáveis existem no template:"
docker exec legisinc-app php artisan tinker --execute="
\$templateProcessor = app(\App\Services\Template\TemplateProcessorService::class);
\$template = \App\Models\TipoProposicaoTemplate::find(6);

if(\$template) {
    \$reflection = new ReflectionClass(\$templateProcessor);
    \$method = \$reflection->getMethod('decodificarUnicodeRTF');
    \$method->setAccessible(true);
    
    \$conteudo = \$template->conteudo;
    \$decoded = \$method->invoke(\$templateProcessor, \$conteudo);
    
    \$vars = ['numero_proposicao', 'ano_atual', 'ementa', 'texto', 'justificativa', 'municipio', 'autor_nome', 'autor_cargo', 'rodape_texto'];
    \$found = [];
    
    foreach(\$vars as \$var) {
        if(strpos(\$decoded, '\${' . \$var . '}') !== false) {
            \$found[] = '\${' . \$var . '}';
        } elseif(strpos(\$decoded, '\$' . \$var) !== false) {
            \$found[] = '\$' . \$var;
        }
    }
    
    echo '✅ Variáveis encontradas no template: ' . implode(', ', \$found);
}
"

echo ""
echo "2. Criando proposição e testando processamento completo:"

USER_ID=$(docker exec legisinc-app php artisan tinker --execute="
\$user = \App\Models\User::where('email', 'jessica@sistema.gov.br')->first();
echo \$user->id ?? 1;
" 2>/dev/null | tail -n1)

PROPOSICAO_ID=$(docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::create([
    'tipo' => 'mocao',
    'ementa' => 'TESTE FINAL - Verificação completa de substituição de variáveis',
    'conteudo' => 'Este é o texto principal que deve aparecer na variável texto do template.',
    'justificativa' => 'Esta justificativa deve ser processada corretamente.',
    'autor_id' => $USER_ID,
    'status' => 'rascunho'
]);
echo \$proposicao->id;
" 2>/dev/null | tail -n1)

echo "   📋 Proposição: ID $PROPOSICAO_ID"

echo ""
echo "3. Processando com TemplateProcessorService (com logs):"
docker exec legisinc-app php artisan tinker --execute="
\$templateProcessor = app(\App\Services\Template\TemplateProcessorService::class);
\$proposicao = \App\Models\Proposicao::find($PROPOSICAO_ID);
\$template = \App\Models\TipoProposicaoTemplate::find(6);

if(\$proposicao && \$template) {
    \$dadosEditaveis = [
        'ementa' => \$proposicao->ementa,
        'texto' => \$proposicao->conteudo,
        'justificativa' => \$proposicao->justificativa,
        'numero_proposicao' => sprintf('%04d', \$proposicao->id)
    ];
    
    echo '🔧 Processando template...';
    \$resultado = \$templateProcessor->processarTemplate(\$template, \$proposicao, \$dadosEditaveis);
    
    echo '📊 Resultado: ' . number_format(strlen(\$resultado)) . ' chars';
    
    // Decodificar resultado para verificar se variáveis foram substituídas
    \$reflection = new ReflectionClass(\$templateProcessor);
    \$method = \$reflection->getMethod('decodificarUnicodeRTF');
    \$method->setAccessible(true);
    \$resultadoLegivel = \$method->invoke(\$templateProcessor, \$resultado);
    
    // Procurar trechos com as informações da proposição
    if(strpos(\$resultadoLegivel, \$proposicao->ementa) !== false) {
        echo '✅ Ementa foi substituída corretamente!';
    } else {
        echo '❌ Ementa NÃO foi substituída';
    }
    
    if(strpos(\$resultadoLegivel, \$proposicao->conteudo) !== false) {
        echo '✅ Texto foi substituído corretamente!';
    } else {
        echo '❌ Texto NÃO foi substituído';
    }
    
    if(strpos(\$resultadoLegivel, sprintf('%04d', \$proposicao->id)) !== false) {
        echo '✅ Número da proposição foi substituído!';
    } else {
        echo '❌ Número da proposição NÃO foi substituído';
    }
    
    if(strpos(\$resultadoLegivel, 'Caraguatatuba') !== false) {
        echo '✅ Município foi substituído!';
    } else {
        echo '❌ Município NÃO foi substituído';
    }
}
"

echo ""
echo "4. Verificando logs para debug:"
echo "   Últimas linhas relevantes do log:"
docker exec legisinc-app tail -n 10 /var/www/html/storage/logs/laravel.log | grep -i "template\|unicode\|variavel\|decodif" || echo "   Nenhum log relevante encontrado"

echo ""
echo "5. Limpeza:"
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::find($PROPOSICAO_ID);
if(\$proposicao) {
    \$proposicao->delete();
    echo '🗑️ Proposição removida';
}
"

echo ""
echo "✅ Teste completo finalizado!"