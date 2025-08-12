#!/bin/bash

echo "🌐 Testando salvamento via interface web..."

# 1. Buscar a página e extrair o token CSRF
echo "1️⃣ Obtendo token CSRF..."
CSRF_TOKEN=$(curl -s "http://localhost:8001/parametros-dados-gerais-camara" | grep -o 'csrf-token.*content="[^"]*"' | sed 's/.*content="//;s/".*//')

if [ -z "$CSRF_TOKEN" ]; then
    echo "❌ Não foi possível obter o token CSRF"
    exit 1
fi

echo "🔐 Token CSRF obtido: ${CSRF_TOKEN:0:20}..."

# 2. Fazer a requisição POST com o token
echo "2️⃣ Enviando dados para salvamento..."
RESPONSE=$(curl -X POST "http://localhost:8001/parametros-dados-gerais-camara" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "X-Requested-With: XMLHttpRequest" \
  -d "_token=${CSRF_TOKEN}&nome_camara=Câmara%20TESTE%20WEB&sigla_camara=CTW&cnpj=88.777.666%2F0001-55&save_tab=identificacao" \
  -s)

echo "📡 Resposta do servidor:"
echo "$RESPONSE" | jq . 2>/dev/null || echo "$RESPONSE"

# 3. Verificar se o valor foi salvo no banco
echo "3️⃣ Verificando se foi salvo no banco..."
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT valor, created_at FROM parametros_valores WHERE campo_id = 51 AND valido_ate IS NULL ORDER BY created_at DESC LIMIT 1;"

# 4. Verificar se a página exibe o novo valor
echo "4️⃣ Verificando se a página exibe o novo valor..."
sleep 2  # Esperar um pouco para garantir que o cache foi atualizado
NEW_VALUE=$(curl -s "http://localhost:8001/parametros-dados-gerais-camara?t=$(date +%s)" | grep -o 'value="[^"]*Câmara[^"]*"' | head -1)
echo "🖥️ Valor na página: $NEW_VALUE"

echo "✅ Teste concluído!"