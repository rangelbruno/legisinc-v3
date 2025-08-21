#!/bin/bash

echo "🔍 Debug completo do botão 'Assinar Documento'"
echo "==============================================="

# Verificar usuários e roles
echo "👥 Verificando usuários do sistema:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "
SELECT u.id, u.name, u.email, r.name as role 
FROM users u 
LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id 
LEFT JOIN roles r ON mhr.role_id = r.id 
WHERE u.id IN (1,2,3) 
ORDER BY u.id;"

echo ""
echo "📊 Verificando proposição 2 completa:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "
SELECT id, tipo, ementa, status, autor_id, created_at 
FROM proposicoes 
WHERE id = 2;"

echo ""
echo "🔧 Testando acesso à página com curl..."

# Simular login e acessar a página
COOKIE_JAR="/tmp/cookies.txt"

# Login como Parlamentar (jessica)
echo "🔐 Fazendo login como Parlamentar (jessica@sistema.gov.br)..."
curl -s -L -c "$COOKIE_JAR" \
  -X POST "http://localhost:8001/login" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "email=jessica@sistema.gov.br&password=123456" \
  > /dev/null

# Acessar página da proposição
echo "📄 Acessando página da proposição 2..."
PAGE_CONTENT=$(curl -s -L -b "$COOKIE_JAR" "http://localhost:8001/proposicoes/2")

# Verificar se contém dados do usuário
if echo "$PAGE_CONTENT" | grep -q "userRole:"; then
    echo "✅ Dados do usuário encontrados no JavaScript"
    USER_ROLE=$(echo "$PAGE_CONTENT" | grep -o "userRole: '[^']*'" | head -1)
    echo "Role encontrada: $USER_ROLE"
else
    echo "❌ Dados do usuário NÃO encontrados no JavaScript"
fi

# Verificar se contém dados da proposição
if echo "$PAGE_CONTENT" | grep -q "proposicao:"; then
    echo "✅ Dados da proposição encontrados no JavaScript"
    # Extrair status da proposição
    STATUS=$(echo "$PAGE_CONTENT" | grep -o '"status":"[^"]*"' | head -1)
    echo "Status encontrado: $STATUS"
else
    echo "❌ Dados da proposição NÃO encontrados no JavaScript"
fi

# Verificar se contém a função canSign
if echo "$PAGE_CONTENT" | grep -q "canSign()"; then
    echo "✅ Função canSign() encontrada"
else
    echo "❌ Função canSign() NÃO encontrada"
fi

# Verificar se contém o botão de assinatura
if echo "$PAGE_CONTENT" | grep -q "Assinar Documento"; then
    echo "✅ Texto 'Assinar Documento' encontrado na página"
else
    echo "❌ Texto 'Assinar Documento' NÃO encontrado na página"
fi

# Verificar se contém v-if="canSign()"
if echo "$PAGE_CONTENT" | grep -q 'v-if="canSign()"'; then
    echo "✅ Diretiva v-if=\"canSign()\" encontrada"
else
    echo "❌ Diretiva v-if=\"canSign()\" NÃO encontrada"
fi

# Salvar página para análise
echo "$PAGE_CONTENT" > /tmp/proposicao_2_debug.html

echo ""
echo "🎯 RESUMO DO PROBLEMA:"
echo "1. Status da proposição: 'aprovado' (correto para mostrar botão)"
echo "2. Usuário: Parlamentar (deveria ter permissão)"
echo "3. Função canSign() deve retornar true"
echo ""
echo "📁 Página salva em: /tmp/proposicao_2_debug.html"
echo "🌐 Acesse: http://localhost:8001/proposicoes/2 e abra o Console do navegador"

# Limpar cookies
rm -f "$COOKIE_JAR"

echo ""
echo "🔍 Para verificar no navegador:"
echo "1. Acesse: http://localhost:8001/login"
echo "2. Login: jessica@sistema.gov.br / 123456"
echo "3. Vá para: http://localhost:8001/proposicoes/2"
echo "4. Abra F12 > Console e procure por 'canSign check:'"