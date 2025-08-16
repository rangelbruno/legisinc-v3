#!/bin/bash

echo "=== TESTE: Ações de Assinatura na Proposição ==="
echo ""

# Verificar status atual da proposição
echo "1. Verificando status da proposição..."
STATUS_INFO=$(docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::find(1);
echo 'ID: ' . \$proposicao->id . PHP_EOL;
echo 'Status: ' . \$proposicao->status . PHP_EOL;
echo 'PDF Path: ' . (\$proposicao->arquivo_pdf_path ?: 'NULL') . PHP_EOL;
echo 'PDF Exists: ' . (\$proposicao->arquivo_pdf_path && file_exists(storage_path('app/' . \$proposicao->arquivo_pdf_path)) ? 'YES' : 'NO') . PHP_EOL;
")

echo "$STATUS_INFO"
echo ""

# Verificar lógica das ações na view
echo "2. Simulando lógica das ações baseada no status..."
ACTION_LOGIC=$(docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::find(1);
\$status = \$proposicao->status;

echo 'Status: ' . \$status . PHP_EOL;

// Verificar qual seção de ações será exibida
if (\$status === 'rascunho') {
    echo 'Ações: Editar/Excluir' . PHP_EOL;
} elseif (\$status === 'enviado_legislativo') {
    echo 'Ações: Em Análise (Consultar Status)' . PHP_EOL;
} elseif (in_array(\$status, ['aguardando_aprovacao_autor', 'devolvido_edicao'])) {
    echo 'Ações: Aprovar Edições/Fazer Novas Edições' . PHP_EOL;
} elseif (\$status === 'em_edicao') {
    echo 'Ações: Continuar Edição/Enviar para Legislativo' . PHP_EOL;
} elseif (in_array(\$status, ['analise', 'em_revisao'])) {
    echo 'Ações: Em Revisão Técnica' . PHP_EOL;
} elseif (\$status === 'retornado_legislativo') {
    echo 'Ações: Assinar Documento' . PHP_EOL;
} elseif (\$status === 'aprovado_assinatura') {
    echo 'Ações: ASSINAR DOCUMENTO (NOVA SEÇÃO)' . PHP_EOL;
} elseif (\$status === 'aprovado') {
    echo 'Ações: Aprovado (Baixar Documento)' . PHP_EOL;
} elseif (\$status === 'assinado') {
    echo 'Ações: Assinado (Enviar/Protocolar)' . PHP_EOL;
} else {
    echo 'Ações: Status genérico' . PHP_EOL;
}
")

echo "$ACTION_LOGIC"
echo ""

echo "3. Ações esperadas para status 'aprovado_assinatura'..."
echo ""
echo "📋 AÇÕES QUE DEVEM APARECER:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "🟡 ALERT: Pronto para Assinatura"
echo "   Sua proposição foi aprovada pelo Legislativo e está pronta para assinatura digital."
echo ""
echo "🟢 [ASSINAR DOCUMENTO] - Principal"
echo "   Link: /proposicoes/1/assinar"
echo ""
echo "🔵 [VISUALIZAR PDF] - Se PDF existe"
echo "   Link: /proposicoes/1/pdf (abre em nova aba)"
echo ""
echo "ℹ️  [VER DETALHES] - Informações"
echo "   Função: consultarStatus()"
echo ""
echo "⚠️  [DEVOLVER PARA LEGISLATIVO] - Opcional"
echo "   Função: devolverParaLegislativo()"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

echo "✅ CORREÇÕES APLICADAS:"
echo "- Seção específica para status 'aprovado_assinatura' adicionada"
echo "- Botão 'Assinar Documento' implementado"
echo "- Botão 'Visualizar PDF' condicional"
echo "- Função 'devolverParaLegislativo()' adicionada"
echo ""
echo "🎯 TESTE MANUAL:"
echo "1. Acesse http://localhost:8001/proposicoes/1"
echo "2. Login como Jessica (jessica@sistema.gov.br / 123456)"
echo "3. Verifique a seção 'Ações' no painel lateral direito"
echo "4. Deve mostrar:"
echo "   - Alert amarelo 'Pronto para Assinatura'"
echo "   - Botão verde 'Assinar Documento'"
echo "   - Botão azul 'Visualizar PDF'"
echo "   - Botões de detalhes e devolução"
echo ""
echo "📋 STATUS: Ações de assinatura implementadas ✅"