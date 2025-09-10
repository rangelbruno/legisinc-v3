#!/bin/bash

echo "🧪 TESTE DE PERFORMANCE - EXCLUSÃO DOCUMENTO"
echo "=============================================="

echo "📊 Executando teste de exclusão via cURL com medição de queries..."

# Fazer login primeiro para obter token CSRF
echo "🔑 Fazendo login para obter token CSRF..."
LOGIN_RESPONSE=$(curl -s -c /tmp/cookies.txt http://localhost:8001/login)

# Extrair token CSRF do HTML retornado
CSRF_TOKEN=$(echo "$LOGIN_RESPONSE" | grep -oP 'name="_token" value="\K[^"]*' | head -n1)

if [ -z "$CSRF_TOKEN" ]; then
    echo "❌ Erro: Não foi possível extrair token CSRF"
    exit 1
fi

echo "✅ Token CSRF obtido: ${CSRF_TOKEN:0:10}..."

# Fazer login efetivamente
LOGIN_RESULT=$(curl -s -b /tmp/cookies.txt -c /tmp/cookies.txt \
    -X POST http://localhost:8001/login \
    -H "Content-Type: application/x-www-form-urlencoded" \
    -d "email=jessica@sistema.gov.br&password=123456&_token=$CSRF_TOKEN")

echo "🔍 Iniciando monitoramento de queries no log do Laravel..."

# Limpar log de debug queries
docker exec -it legisinc-app bash -c "echo '' > /var/www/html/storage/logs/debug-queries.log 2>/dev/null || true"

# Fazer a requisição de exclusão
echo "🗑️ Executando exclusão da proposição 1..."
EXCLUSAO_RESULT=$(curl -s -b /tmp/cookies.txt \
    -X DELETE "http://localhost:8001/proposicoes/1/excluir-documento" \
    -H "X-Requested-With: XMLHttpRequest" \
    -H "Content-Type: application/json")

echo "📝 Resultado da exclusão:"
echo "$EXCLUSAO_RESULT" | jq '.' 2>/dev/null || echo "$EXCLUSAO_RESULT"

sleep 2

# Verificar quantidade de queries executadas
echo ""
echo "📊 ANÁLISE DE PERFORMANCE:"
echo "=========================="

QUERY_COUNT=$(docker exec legisinc-app bash -c "wc -l /var/www/html/storage/logs/debug-queries.log 2>/dev/null || echo '0'" | awk '{print $1}')

echo "🔢 Total de queries executadas: $QUERY_COUNT"

if [ "$QUERY_COUNT" -gt 50 ]; then
    echo "⚠️  ALERTA: Muitas queries detectadas ($QUERY_COUNT)! Possível problema N+1."
elif [ "$QUERY_COUNT" -gt 20 ]; then
    echo "⚡ Moderado: $QUERY_COUNT queries - pode ser otimizado."
else
    echo "✅ Excelente: $QUERY_COUNT queries - performance adequada!"
fi

# Mostrar últimas queries se houver problemas
if [ "$QUERY_COUNT" -gt 20 ]; then
    echo ""
    echo "🔍 Últimas 10 queries executadas:"
    docker exec legisinc-app tail -n 10 /var/www/html/storage/logs/debug-queries.log 2>/dev/null || echo "Log não disponível"
fi

# Limpar cookies temporários
rm -f /tmp/cookies.txt

echo ""
echo "✅ Teste de performance concluído!"