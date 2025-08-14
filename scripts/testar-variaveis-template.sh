#!/bin/bash

echo "🔍 TESTANDO SUBSTITUIÇÃO DE VARIÁVEIS NO TEMPLATE"
echo "================================================"

echo ""
echo "1. Verificando se as variáveis estão sendo fornecidas pelo sistema:"
docker exec legisinc-app php artisan tinker --execute="
\$templateVariable = app(\App\Services\Template\TemplateVariableService::class);
\$variables = \$templateVariable->getTemplateVariables();

echo '📋 Variáveis disponíveis que aparecem no template:';
\$variaveisTemplate = ['municipio', 'assinatura_padrao', 'rodape_texto', 'ano_atual', 'dia', 'mes_extenso'];
foreach(\$variaveisTemplate as \$var) {
    if(isset(\$variables[\$var])) {
        \$valor = \$variables[\$var];
        if(strlen(\$valor) > 50) \$valor = substr(\$valor, 0, 50) . '...';
        echo \"✅ \$var = '\$valor'\";
    } else {
        echo \"❌ \$var = NÃO ENCONTRADA\";
    }
}
"

echo ""
echo "2. Criando proposição de teste e simulando processamento:"

# Buscar usuário
USER_ID=$(docker exec legisinc-app php artisan tinker --execute="
\$user = \App\Models\User::where('email', 'jessica@sistema.gov.br')->first();
echo \$user->id ?? 1;
" 2>/dev/null | tail -n1)

# Criar proposição de teste
PROPOSICAO_ID=$(docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::create([
    'tipo' => 'mocao',
    'ementa' => 'TESTE - Verificar se variáveis são substituídas',
    'conteudo' => 'Este é um teste para verificar se o template processa variáveis corretamente.',
    'autor_id' => $USER_ID,
    'status' => 'rascunho'
]);
echo \$proposicao->id;
" 2>/dev/null | tail -n1)

echo "   📋 Proposição criada: ID $PROPOSICAO_ID"

# Testar processamento com template simples
echo ""
echo "3. Testando processamento de template com variáveis:"
docker exec legisinc-app php artisan tinker --execute="
\$templateProcessor = app(\App\Services\Template\TemplateProcessorService::class);
\$proposicao = \App\Models\Proposicao::find($PROPOSICAO_ID);
\$template = \App\Models\TipoProposicaoTemplate::find(6);

if(\$proposicao && \$template) {
    // Criar um template de teste com as variáveis problemáticas
    \$templateTeste = 'MOÇÃO Nº \${numero_proposicao}/\${ano_atual}\\n\\n';
    \$templateTeste .= 'EMENTA: \${ementa}\\n\\n';
    \$templateTeste .= 'A Câmara Municipal manifesta:\\n\\n';
    \$templateTeste .= '\${texto}\\n\\n';
    \$templateTeste .= '\${justificativa}\\n\\n';
    \$templateTeste .= 'Resolve dirigir a presente Moção.\\n\\n';
    \$templateTeste .= '\$municipio, \$dia de \$mes_extenso de \$ano_atual.\\n\\n';
    \$templateTeste .= '\$assinatura_padrao\\n';
    \$templateTeste .= '\$autor_nome\\n';
    \$templateTeste .= '\$autor_cargo\\n\\n';
    \$templateTeste .= '\$rodape_texto';
    
    echo '📄 Template de teste criado com as variáveis problemáticas';
    echo '🔧 Processando template...';
    
    // Temporariamente substituir o conteúdo do template para teste
    \$conteudoOriginal = \$template->conteudo;
    \$template->conteudo = \$templateTeste;
    
    try {
        \$resultado = \$templateProcessor->processarTemplate(\$template, \$proposicao, [
            'ementa' => \$proposicao->ementa,
            'texto' => \$proposicao->conteudo,
            'justificativa' => 'Justificativa de teste'
        ]);
        
        echo '✅ Template processado com sucesso!';
        echo '📊 Resultado:';
        echo '---';
        echo \$resultado;
        echo '---';
        
        // Verificar se ainda há variáveis não substituídas
        \$variaveisNaoSubstituidas = [];
        if(preg_match_all('/\\\$([a-zA-Z_][a-zA-Z0-9_]*)/', \$resultado, \$matches)) {
            \$variaveisNaoSubstituidas = array_unique(\$matches[1]);
        }
        if(preg_match_all('/\\\$\\\\{([a-zA-Z_][a-zA-Z0-9_]*)\\\\}/', \$resultado, \$matches)) {
            \$variaveisNaoSubstituidas = array_merge(\$variaveisNaoSubstituidas, array_unique(\$matches[1]));
        }
        
        if(empty(\$variaveisNaoSubstituidas)) {
            echo '✅ Todas as variáveis foram substituídas!';
        } else {
            echo '⚠️  Variáveis não substituídas: ' . implode(', ', \$variaveisNaoSubstituidas);
        }
        
    } catch (\Exception \$e) {
        echo '❌ Erro: ' . \$e->getMessage();
    }
    
    // Restaurar conteúdo original
    \$template->conteudo = \$conteudoOriginal;
}
"

echo ""
echo "4. Limpando proposição de teste:"
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::find($PROPOSICAO_ID);
if(\$proposicao) {
    \$proposicao->delete();
    echo '🗑️  Proposição removida';
}
"

echo ""
echo "✅ Teste concluído!"
echo ""
echo "🔧 CORREÇÕES APLICADAS:"
echo "✅ Adicionado suporte para variáveis sem chaves (\$variavel)"
echo "✅ TemplateProcessorService agora processa 3 formatos:"
echo "   - \$\\{variavel\\} (RTF escapado)"
echo "   - \${variavel} (formato normal)"
echo "   - \$variavel (formato sem chaves)"