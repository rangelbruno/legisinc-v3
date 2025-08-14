#!/bin/bash

echo "🔍 DIAGNÓSTICO FINAL - Template Real vs Processamento"
echo "=================================================="

echo ""
echo "1. Analisando o conteúdo real do template editado no admin:"
docker exec legisinc-app php artisan tinker --execute="
\$template = \App\Models\TipoProposicaoTemplate::find(6);
if(\$template && \$template->conteudo) {
    \$conteudo = \$template->conteudo;
    echo '📊 Tamanho do template: ' . number_format(strlen(\$conteudo)) . ' chars';
    
    // Extrair uma pequena amostra do conteúdo
    echo '📄 Primeiros 1000 chars:';
    echo '---';
    echo substr(\$conteudo, 0, 1000);
    echo '---';
    
    // Procurar por variáveis específicas que você mencionou
    \$variaveisExemplo = ['numero_proposicao', 'ementa', 'texto', 'municipio', 'autor_nome'];
    echo '🔍 Procurando variáveis no template:';
    foreach(\$variaveisExemplo as \$var) {
        // Procurar diferentes formatos
        if(strpos(\$conteudo, '\${' . \$var . '}') !== false) {
            echo \"✅ Encontrou \$var no formato \\\${var}\";
        } elseif(strpos(\$conteudo, '\$' . \$var) !== false) {
            echo \"✅ Encontrou \$var no formato \\\$var\";
        } else {
            echo \"❌ NÃO encontrou \$var\";
        }
    }
}
"

echo ""
echo "2. Testando processamento real:"

# Buscar usuário
USER_ID=$(docker exec legisinc-app php artisan tinker --execute="
\$user = \App\Models\User::where('email', 'jessica@sistema.gov.br')->first();
echo \$user->id ?? 1;
" 2>/dev/null | tail -n1)

# Criar proposição de teste  
PROPOSICAO_ID=$(docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::create([
    'tipo' => 'mocao',
    'ementa' => 'DIAGNÓSTICO - Teste de substituição de variáveis no template real',
    'conteudo' => 'Este conteúdo deve aparecer na variável texto do template.',
    'justificativa' => 'Esta justificativa deve aparecer na variável justificativa.',
    'autor_id' => $USER_ID,
    'status' => 'rascunho'
]);
echo \$proposicao->id;
" 2>/dev/null | tail -n1)

echo "   📋 Proposição criada: ID $PROPOSICAO_ID"

# Processar com template real
echo ""
echo "3. Processando template real:"
docker exec legisinc-app php artisan tinker --execute="
\$templateProcessor = app(\App\Services\Template\TemplateProcessorService::class);
\$proposicao = \App\Models\Proposicao::find($PROPOSICAO_ID);
\$template = \App\Models\TipoProposicaoTemplate::find(6);

if(\$proposicao && \$template) {
    try {
        \$dadosEditaveis = [
            'ementa' => \$proposicao->ementa,
            'texto' => \$proposicao->conteudo,
            'justificativa' => \$proposicao->justificativa ?? 'Justificativa de teste',
            'numero_proposicao' => sprintf('%04d', \$proposicao->id)
        ];
        
        \$resultado = \$templateProcessor->processarTemplate(\$template, \$proposicao, \$dadosEditaveis);
        
        echo '✅ Template processado!';
        echo '📊 Tamanho: ' . number_format(strlen(\$resultado)) . ' chars';
        
        // Verificar variáveis não substituídas
        \$variaveisNaoSubstituidas = [];
        if(preg_match_all('/\\\$\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', \$resultado, \$matches)) {
            \$variaveisNaoSubstituidas = array_merge(\$variaveisNaoSubstituidas, \$matches[1]);
        }
        if(preg_match_all('/\\\$([a-zA-Z_][a-zA-Z0-9_]*)(?![a-zA-Z0-9_])/', \$resultado, \$matches)) {
            \$variaveisNaoSubstituidas = array_merge(\$variaveisNaoSubstituidas, \$matches[1]);
        }
        
        \$variaveisNaoSubstituidas = array_unique(\$variaveisNaoSubstituidas);
        
        if(empty(\$variaveisNaoSubstituidas)) {
            echo '✅ Todas as variáveis foram substituídas!';
        } else {
            echo '⚠️  Variáveis NÃO substituídas: ' . implode(', ', \$variaveisNaoSubstituidas);
        }
        
    } catch (\Exception \$e) {
        echo '❌ Erro: ' . \$e->getMessage();
    }
}
"

echo ""
echo "4. Limpeza:"
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::find($PROPOSICAO_ID);
if(\$proposicao) {
    \$proposicao->delete();
    echo '🗑️  Proposição removida';
}
"

echo ""
echo "✅ Diagnóstico concluído!"