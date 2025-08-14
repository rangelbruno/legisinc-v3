#!/bin/bash

echo "🔧 Verificando se a correção do template foi aplicada"
echo "=================================================="

echo ""
echo "1. Verificando o código do método gerarDocumentoComTemplate:"
echo ""
grep -A 15 "Priorizar conteúdo do banco" /home/bruno/legisinc/app/Services/OnlyOffice/OnlyOfficeService.php

echo ""
echo "2. Testando uma proposição de moção real:"

# Buscar uma proposição de moção existente
PROPOSICAO_ID=$(docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::where('tipo', 'LIKE', '%Moção%')->orWhere('tipo', 'LIKE', '%mocao%')->first();
if(\$proposicao) { 
    echo \$proposicao->id; 
} else { 
    echo 'NULL'; 
}
" 2>/dev/null | tail -n1)

if [ "$PROPOSICAO_ID" != "NULL" ] && [ ! -z "$PROPOSICAO_ID" ]; then
    echo "✅ Proposição encontrada - ID: $PROPOSICAO_ID"
    
    # Verificar se vai usar conteúdo do banco ou arquivo
    echo ""
    echo "3. Verificando qual conteúdo será usado:"
    docker exec legisinc-app php artisan tinker --execute="
    \$proposicao = \App\Models\Proposicao::find($PROPOSICAO_ID);
    
    if(\$proposicao) {
        // Simular a mesma lógica do gerarDocumentoComTemplate
        \$tipoProposicao = \App\Models\TipoProposicao::where('codigo', 'mocao')
            ->orWhere('nome', 'Moção')
            ->first();
            
        if(\$tipoProposicao && \$tipoProposicao->templates()->exists()) {
            \$template = \$tipoProposicao->templates()->where('ativo', true)->first();
            
            if(\$template) {
                echo '📋 Template encontrado - ID: ' . \$template->id;
                
                if (!empty(\$template->conteudo)) {
                    echo '✅ USARÁ CONTEÚDO DO BANCO (editado no admin)';
                    echo '📊 Tamanho: ' . number_format(strlen(\$template->conteudo)) . ' caracteres';
                    
                    // Verificar se tem características do template editado
                    if (strpos(\$template->conteudo, 'Caraguatatuba') !== false) {
                        echo '✅ Template editado detectado (contém \"Caraguatatuba\")';
                    } else {
                        echo '⚠️  Não encontrou \"Caraguatatuba\" no conteúdo';
                    }
                } else {
                    echo '⚠️  USARÁ ARQUIVO DO SEEDER (conteúdo do banco vazio)';
                    echo '📁 Arquivo: ' . \$template->arquivo_path;
                }
            } else {
                echo '❌ Template não encontrado';
            }
        } else {
            echo '❌ Tipo não tem templates';
        }
    }
    "
else
    echo "❌ Nenhuma proposição de moção encontrada"
    echo ""
    echo "Criando uma proposição de teste:"
    
    USER_ID=$(docker exec legisinc-app php artisan tinker --execute="
    \$user = \App\Models\User::first();
    echo \$user->id;
    " 2>/dev/null | tail -n1)
    
    TEST_PROPOSICAO_ID=$(docker exec legisinc-app php artisan tinker --execute="
    \$proposicao = \App\Models\Proposicao::create([
        'tipo' => 'Moção',
        'ementa' => 'Teste de verificação de template - ' . date('d/m/Y H:i:s'),
        'conteudo' => 'Conteúdo de teste',
        'autor_id' => $USER_ID,
        'status' => 'rascunho'
    ]);
    echo \$proposicao->id;
    " 2>/dev/null | tail -n1)
    
    echo "✅ Proposição de teste criada - ID: $TEST_PROPOSICAO_ID"
    
    # Repetir o teste com a proposição criada
    echo ""
    echo "3. Verificando qual conteúdo será usado:"
    docker exec legisinc-app php artisan tinker --execute="
    \$proposicao = \App\Models\Proposicao::find($TEST_PROPOSICAO_ID);
    
    if(\$proposicao) {
        \$tipoProposicao = \App\Models\TipoProposicao::where('codigo', 'mocao')
            ->orWhere('nome', 'Moção')
            ->first();
            
        if(\$tipoProposicao && \$tipoProposicao->templates()->exists()) {
            \$template = \$tipoProposicao->templates()->where('ativo', true)->first();
            
            if(\$template) {
                echo '📋 Template encontrado - ID: ' . \$template->id;
                
                if (!empty(\$template->conteudo)) {
                    echo '✅ USARÁ CONTEÚDO DO BANCO (editado no admin)';
                    echo '📊 Tamanho: ' . number_format(strlen(\$template->conteudo)) . ' caracteres';
                    
                    if (strpos(\$template->conteudo, 'Caraguatatuba') !== false) {
                        echo '✅ Template editado detectado (contém \"Caraguatatuba\")';
                    } else {
                        echo '⚠️  Não encontrou \"Caraguatatuba\" no conteúdo';
                    }
                } else {
                    echo '⚠️  USARÁ ARQUIVO DO SEEDER (conteúdo do banco vazio)';
                    echo '📁 Arquivo: ' . \$template->arquivo_path;
                }
            }
        }
    }
    "
    
    # Limpar proposição de teste
    docker exec legisinc-app php artisan tinker --execute="
    \$proposicao = \App\Models\Proposicao::find($TEST_PROPOSICAO_ID);
    if(\$proposicao) {
        \$proposicao->delete();
        echo '🗑️  Proposição de teste removida';
    }
    "
fi

echo ""
echo "✅ Verificação concluída!"
echo ""
echo "📝 Resumo:"
echo "- A correção prioriza o conteúdo do banco (template editado no admin)"
echo "- Se não houver conteúdo no banco, usa o arquivo do seeder como fallback"
echo "- Templates editados no OnlyOffice agora serão aplicados nas proposições"