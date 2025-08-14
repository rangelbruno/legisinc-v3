#!/bin/bash

echo "🧪 Testando se templates editados no admin são aplicados nas proposições"
echo "====================================================================="

# Verificar se o template de moção tem conteúdo no banco
echo "1. Verificando template de moção:"
docker exec legisinc-app php artisan tinker --execute="
\$template = \App\Models\TipoProposicaoTemplate::join('tipo_proposicoes', 'tipo_proposicao_templates.tipo_proposicao_id', '=', 'tipo_proposicoes.id')
    ->where('tipo_proposicoes.codigo', 'mocao')
    ->select('tipo_proposicao_templates.*', 'tipo_proposicoes.codigo', 'tipo_proposicoes.nome')
    ->first();
if(\$template) {
    echo '✅ Template encontrado - ID: ' . \$template->id;
    echo '📄 Conteúdo no banco: ' . (\$template->conteudo ? 'SIM (' . strlen(\$template->conteudo) . ' chars)' : 'NÃO');
    echo '📁 Arquivo: ' . (\$template->arquivo_path ?: 'NENHUM');
    echo '🗂️ Document Key: ' . \$template->document_key;
} else {
    echo '❌ Template de moção não encontrado';
}
"

echo ""
echo "2. Criando proposição de teste:"

# Buscar usuário parlamentar (jessica)
USER_ID=$(docker exec legisinc-app php artisan tinker --execute="
\$user = \App\Models\User::where('email', 'jessica@sistema.gov.br')->first();
if(\$user) { echo \$user->id; } else { echo '1'; }
" 2>/dev/null | tail -n1)

echo "   👤 Usuário parlamentar ID: $USER_ID"

# Criar proposição de teste
PROPOSICAO_ID=$(docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::create([
    'tipo' => 'Moção',
    'ementa' => 'Proposição de teste para verificar template editado - ' . date('d/m/Y H:i:s'),
    'conteudo' => 'Conteúdo inicial da proposição de teste',
    'autor_id' => $USER_ID,
    'status' => 'rascunho'
]);
echo \$proposicao->id;
" 2>/dev/null | tail -n1)

echo "   📋 Proposição criada - ID: $PROPOSICAO_ID"

echo ""
echo "3. Testando geração de documento com template:"

# Testar geração do documento usando o OnlyOfficeService
docker exec legisinc-app php artisan tinker --execute="
\$onlyOffice = app(\App\Services\OnlyOffice\OnlyOfficeService::class);
\$proposicao = \App\Models\Proposicao::find($PROPOSICAO_ID);

if (\$proposicao) {
    echo '📝 Proposição carregada: ' . \$proposicao->tipo . ' - ' . \$proposicao->ementa;
    
    try {
        \$documento = \$onlyOffice->gerarDocumentoProposicao(\$proposicao);
        echo '✅ Documento gerado com sucesso';
        echo '📄 Tamanho do arquivo: ' . filesize(\$documento) . ' bytes';
        
        // Verificar se o conteúdo tem características do template editado
        \$conteudo = file_get_contents(\$documento);
        if (strpos(\$conteudo, 'Caraguatatuba') !== false) {
            echo '✅ Template editado aplicado corretamente (encontrou Caraguatatuba)';
        } else {
            echo '❌ Template editado NÃO foi aplicado (não encontrou Caraguatatuba)';
        }
        
        // Mostrar preview do conteúdo RTF
        echo '📖 Preview do conteúdo (primeiros 300 chars):';
        echo '---';
        echo substr(\$conteudo, 0, 300) . '...';
        
    } catch (\Exception \$e) {
        echo '❌ Erro ao gerar documento: ' . \$e->getMessage();
    }
} else {
    echo '❌ Proposição não encontrada';
}
"

echo ""
echo "4. Limpando proposição de teste:"
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::find($PROPOSICAO_ID);
if (\$proposicao) {
    \$proposicao->delete();
    echo '🗑️  Proposição de teste removida';
}
"

echo ""
echo "✅ Teste concluído!"