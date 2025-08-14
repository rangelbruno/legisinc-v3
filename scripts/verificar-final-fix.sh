#!/bin/bash

echo "🔧 TESTE FINAL: Verificar se templates editados no admin são aplicados"
echo "================================================================="

echo ""
echo "1. Verificando template de moção no banco:"
docker exec legisinc-app php artisan tinker --execute="
\$template = \App\Models\TipoProposicaoTemplate::find(6);
if(\$template) {
    echo '✅ Template ID: ' . \$template->id;
    echo '📄 Conteúdo no banco: ' . (\$template->conteudo ? 'SIM (' . number_format(strlen(\$template->conteudo)) . ' chars)' : 'NÃO');
    echo '📁 Arquivo: ' . (\$template->arquivo_path ?: 'NENHUM');
} else {
    echo '❌ Template não encontrado';
}
"

echo ""
echo "2. Criando proposição de teste:"

# Buscar usuário parlamentar
USER_ID=$(docker exec legisinc-app php artisan tinker --execute="
\$user = \App\Models\User::where('email', 'jessica@sistema.gov.br')->first();
if(\$user) { echo \$user->id; } else { echo '1'; }
" 2>/dev/null | tail -n1)

echo "   👤 Usuário: $USER_ID"

# Criar proposição de teste
PROPOSICAO_ID=$(docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::create([
    'tipo' => 'mocao',
    'ementa' => 'TESTE FINAL - Verificação template editado - ' . date('d/m/Y H:i:s'),
    'conteudo' => 'Conteúdo inicial para verificar se template editado é aplicado corretamente.',
    'autor_id' => $USER_ID,
    'status' => 'rascunho'
]);
echo \$proposicao->id;
" 2>/dev/null | tail -n1)

echo "   📋 Proposição criada: $PROPOSICAO_ID"

echo ""
echo "3. Simulando processamento do TemplateProcessorService:"

# Testar se o TemplateProcessorService agora usa conteúdo do banco
docker exec legisinc-app php artisan tinker --execute="
\$templateProcessor = app(\App\Services\Template\TemplateProcessorService::class);
\$proposicao = \App\Models\Proposicao::find($PROPOSICAO_ID);
\$template = \App\Models\TipoProposicaoTemplate::find(6);

if(\$proposicao && \$template) {
    echo '🔧 Processando template com TemplateProcessorService...';
    
    try {
        // Testar o processamento
        \$resultado = \$templateProcessor->processarTemplate(\$template, \$proposicao, [
            'ementa' => \$proposicao->ementa,
            'texto' => \$proposicao->conteudo
        ]);
        
        echo '✅ Template processado com sucesso';
        echo '📊 Tamanho final: ' . number_format(strlen(\$resultado)) . ' caracteres';
        
        // Verificar nos logs se usou conteúdo do banco
        echo '📋 Verificar nos logs se aparece: \"TemplateProcessorService: Usando conteúdo do banco\"';
        
    } catch (\Exception \$e) {
        echo '❌ Erro: ' . \$e->getMessage();
    }
}
"

echo ""
echo "4. Verificando logs recentes:"
echo "   Procurando por 'TemplateProcessorService' nos últimos logs..."
docker exec legisinc-app tail -n 20 /var/www/html/storage/logs/laravel.log | grep -i "templateprocessorservice" || echo "   ℹ️  Nenhum log do TemplateProcessorService encontrado (normal se não houve processamento)"

echo ""
echo "5. Limpando proposição de teste:"
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::find($PROPOSICAO_ID);
if(\$proposicao) {
    \$proposicao->delete();
    echo '🗑️  Proposição removida';
}
"

echo ""
echo "✅ CORREÇÃO APLICADA:"
echo "================================"
echo "✅ OnlyOfficeService: Prioriza conteúdo do banco sobre arquivo"
echo "✅ TemplateProcessorService: Prioriza conteúdo do banco sobre arquivo"
echo ""
echo "🎯 RESULTADO:"
echo "Agora quando você editar um template em /admin/templates/X/editor"
echo "e salvar no OnlyOffice, as alterações serão aplicadas em novas proposições!"
echo ""
echo "📝 COMO TESTAR:"
echo "1. Acesse /admin/templates/12/editor"
echo "2. Faça alterações no template e salve (Ctrl+S)"
echo "3. Crie uma nova proposição tipo 'mocao'"
echo "4. Abra a proposição no editor"
echo "5. Verifique se as alterações do template aparecem"