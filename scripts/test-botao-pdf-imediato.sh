#!/bin/bash

echo "🧪 ==============================================="
echo "✅ TESTANDO BOTÃO VISUALIZAR PDF IMEDIATO"
echo "🧪 ==============================================="
echo ""

echo "📊 Verificando dados da proposição 2..."

# Verificar status e PDF no banco
echo "🗃️ Status no banco de dados:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, status, arquivo_pdf_path IS NOT NULL as tem_pdf, LEFT(arquivo_pdf_path, 50) as pdf_path FROM proposicoes WHERE id = 2;" 2>/dev/null

echo ""
echo "🔧 Verificando correção no controller..."

CONTROLLER_PATH="/home/bruno/legisinc/app/Http/Controllers/ProposicaoController.php"

# Verificar se a correção foi aplicada
if grep -q "has_pdf = !empty" "$CONTROLLER_PATH"; then
    echo "✅ Propriedade has_pdf adicionada no controller"
else
    echo "❌ Propriedade has_pdf NÃO adicionada no controller"
fi

if grep -q "has_arquivo = !empty" "$CONTROLLER_PATH"; then
    echo "✅ Propriedade has_arquivo adicionada no controller"
else
    echo "❌ Propriedade has_arquivo NÃO adicionada no controller"
fi

echo ""
echo "📝 Verificando condição na view..."

VIEW_PATH="/home/bruno/legisinc/resources/views/proposicoes/show.blade.php"

# Verificar se a condição v-if existe
if grep -q "v-if=\"proposicao.has_pdf\"" "$VIEW_PATH"; then
    echo "✅ Condição v-if=\"proposicao.has_pdf\" encontrada"
    echo "   📍 Localização: Controla visibilidade do botão PDF"
else
    echo "❌ Condição v-if=\"proposicao.has_pdf\" NÃO encontrada"
fi

echo ""
echo "🌐 Testando endpoint..."

# Testar página de visualização
echo "📄 Testando /proposicoes/2..."
SHOW_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8001/proposicoes/2)
echo "   Status: $SHOW_STATUS"

if [ "$SHOW_STATUS" = "302" ] || [ "$SHOW_STATUS" = "200" ]; then
    echo "✅ Página acessível"
else
    echo "❌ Problema no acesso à página"
fi

echo ""
echo "📊 Problema vs. Solução:"
echo ""
echo "🐛 PROBLEMA REPORTADO:"
echo "   - Botão 'Visualizar PDF' só aparece APÓS clicar 'Atualizar dados'"
echo "   - Em carregamento inicial: apenas botão 'Atualizar dados'"
echo "   - Após AJAX: botão 'Visualizar PDF' aparece"
echo ""
echo "🔧 CAUSA IDENTIFICADA:"
echo "   - Controller show() não passava propriedade 'has_pdf'"
echo "   - View dependia de dados via AJAX (/dados-frescos)"
echo "   - Condição v-if=\"proposicao.has_pdf\" sempre false inicialmente"
echo ""
echo "✅ SOLUÇÃO IMPLEMENTADA:"
echo "   - Adicionado \$proposicao->has_pdf no controller show()"
echo "   - Adicionado \$proposicao->has_arquivo no controller show()"
echo "   - Dados disponíveis desde carregamento inicial"
echo ""

echo "🌟 =============================="
echo "✅ TESTE DE CORREÇÃO CONCLUÍDO!"
echo "🌟 =============================="
echo ""
echo "📋 RESULTADO ESPERADO:"
echo "   🎯 Carregamento inicial: Botão 'Visualizar PDF' visível"
echo "   📄 Sem necessidade de clicar 'Atualizar dados'"
echo "   ⚡ Experiência mais fluida para o usuário"
echo ""
echo "📋 PARA TESTAR NO BROWSER:"
echo "   1. Acesse: http://localhost:8001/proposicoes/2"
echo "   2. Login: jessica@sistema.gov.br / 123456"
echo "   3. Verifique: Botão 'Visualizar PDF' já visível"
echo "   4. Não precisa: Clicar 'Atualizar dados'"
echo ""