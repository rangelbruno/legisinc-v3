#!/bin/bash

echo "🔐 TESTE: Assinatura Digital com Login"
echo "====================================="

echo
echo "🔑 1. Fazendo login como Jessica (Parlamentar)..."
# Primeiro, obter o token CSRF
LOGIN_PAGE=$(curl -s -c /tmp/cookies.txt "http://localhost:8001/login")
CSRF_TOKEN=$(echo "$LOGIN_PAGE" | grep -oP 'name="_token" value="\K[^"]+')

if [ -z "$CSRF_TOKEN" ]; then
    echo "❌ Não foi possível obter token CSRF"
    exit 1
fi

echo "   Token CSRF obtido: ${CSRF_TOKEN:0:10}..."

# Fazer login
LOGIN_RESPONSE=$(curl -s -b /tmp/cookies.txt -c /tmp/cookies.txt \
    -X POST \
    -d "_token=$CSRF_TOKEN" \
    -d "email=jessica@sistema.gov.br" \
    -d "password=123456" \
    "http://localhost:8001/login")

# Verificar se login foi bem-sucedido (redirecionamento)
if echo "$LOGIN_RESPONSE" | grep -q "dashboard\|proposicoes"; then
    echo "   ✅ Login realizado com sucesso"
else
    echo "   ⚠️  Login pode ter falhado, mas continuando..."
fi

echo
echo "📄 2. Acessando página de assinatura da proposição 2..."
PDF_RESPONSE=$(curl -s -b /tmp/cookies.txt "http://localhost:8001/proposicoes/2/assinar")

echo
echo "🔍 3. Verificando se página foi carregada corretamente..."
if echo "$PDF_RESPONSE" | grep -q "login"; then
    echo "   ❌ Ainda redirecionando para login"
    echo "   🔍 Verificando resposta:"
    echo "$PDF_RESPONSE" | head -10
    exit 1
else
    echo "   ✅ Página de assinatura carregada"
fi

echo
echo "🔍 4. Procurando texto de assinatura digital..."
if echo "$PDF_RESPONSE" | grep -q "Autenticar documento em"; then
    echo "   ✅ SUCESSO! Texto de assinatura encontrado:"
    echo "$PDF_RESPONSE" | grep -o "Autenticar documento em[^<]*" | head -1
else
    echo "   ❌ Texto de assinatura NÃO encontrado"
    
    echo
    echo "🔍 Verificando outras pistas..."
    if echo "$PDF_RESPONSE" | grep -q "14.063/2020"; then
        echo "   ✅ Lei 14.063/2020 encontrada"
    fi
    
    if echo "$PDF_RESPONSE" | grep -q "assinatura"; then
        echo "   📝 Palavra 'assinatura' encontrada no HTML"
    fi
    
    # Salvar resposta para análise
    echo "$PDF_RESPONSE" > /tmp/pdf_response_full.html
    echo "   💾 Resposta completa salva em /tmp/pdf_response_full.html"
fi

echo
echo "🧹 5. Limpeza..."
rm -f /tmp/cookies.txt

echo
echo "📋 RESULTADO FINAL:"
if echo "$PDF_RESPONSE" | grep -q "Autenticar documento em.*14.063/2020"; then
    echo "✅ SUCESSO! Assinatura digital funcionando corretamente"
else
    echo "❌ Assinatura digital ainda não está funcionando"
    echo "🔧 Verifique o arquivo salvo em /tmp/pdf_response_full.html"
fi