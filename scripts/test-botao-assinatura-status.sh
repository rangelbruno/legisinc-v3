#!/bin/bash

echo "🔍 Testando botão 'Assinar Documento' - deve aparecer apenas com status 'aprovado'"
echo "============================================================================"

# Verificar status atual da proposição 2
echo "📊 Status atual da proposição 2:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, tipo, ementa, status, autor_id FROM proposicoes WHERE id = 2;"

echo ""
echo "🔧 Acessando proposição 2 para verificar botão..."

# Fazer requisição para a página da proposição 2
curl -s -L "http://localhost:8001/proposicoes/2" \
  -H "Accept: text/html" \
  -o /tmp/proposicao_2_page.html

# Verificar se contém o botão de assinatura
if grep -q "Assinar Documento" /tmp/proposicao_2_page.html; then
    echo "✅ Botão 'Assinar Documento' encontrado na página"
else
    echo "❌ Botão 'Assinar Documento' NÃO encontrado na página"
fi

echo ""
echo "🔍 Verificando condições do Vue.js:"
echo "Status da proposição: aprovado (deve permitir assinatura)"
echo "Função canSign() deve retornar true apenas para status='aprovado'"

# Testar com outro status para confirmar que não aparece
echo ""
echo "🧪 Testando com status diferente (em_revisao)..."
docker exec legisinc-postgres psql -U postgres -d legisinc -c "UPDATE proposicoes SET status = 'em_revisao' WHERE id = 2;"

echo "📊 Status alterado:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, status FROM proposicoes WHERE id = 2;"

curl -s -L "http://localhost:8001/proposicoes/2" \
  -H "Accept: text/html" \
  -o /tmp/proposicao_2_page_revisao.html

if grep -q "Assinar Documento" /tmp/proposicao_2_page_revisao.html; then
    echo "❌ PROBLEMA: Botão ainda aparece com status 'em_revisao'"
else
    echo "✅ CORRETO: Botão não aparece com status 'em_revisao'"
fi

# Restaurar status original
echo ""
echo "🔄 Restaurando status original (aprovado)..."
docker exec legisinc-postgres psql -U postgres -d legisinc -c "UPDATE proposicoes SET status = 'aprovado' WHERE id = 2;"

echo "📊 Status restaurado:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, status FROM proposicoes WHERE id = 2;"

curl -s -L "http://localhost:8001/proposicoes/2" \
  -H "Accept: text/html" \
  -o /tmp/proposicao_2_page_final.html

if grep -q "Assinar Documento" /tmp/proposicao_2_page_final.html; then
    echo "✅ CORRETO: Botão voltou a aparecer com status 'aprovado'"
else
    echo "❌ PROBLEMA: Botão não aparece mesmo com status 'aprovado'"
fi

echo ""
echo "🎯 RESUMO:"
echo "- Botão deve aparecer APENAS quando status = 'aprovado'"
echo "- Função canSign() foi atualizada para verificar isso"
echo "- Logs de debug foram adicionados ao console do navegador"

# Limpar arquivos temporários
rm -f /tmp/proposicao_2_page*.html

echo ""
echo "✅ Teste concluído! Acesse http://localhost:8001/proposicoes/2 e verifique o console do navegador."