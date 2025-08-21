#!/bin/bash

echo "=== TESTE COM LOGIN AUTOMATIZADO ==="
echo ""

# Criar sessão com cookies
COOKIE_JAR="/tmp/test_cookies.txt"
rm -f "$COOKIE_JAR"

echo "1. Obtendo token CSRF e fazendo login..."

# Obter token CSRF da página de login
LOGIN_PAGE=$(curl -s -c "$COOKIE_JAR" http://localhost:8001/login)
CSRF_TOKEN=$(echo "$LOGIN_PAGE" | grep -oP 'name="_token" value="\K[^"]+')

if [ -n "$CSRF_TOKEN" ]; then
    echo "   ✅ CSRF Token obtido: ${CSRF_TOKEN:0:20}..."
    
    # Fazer login
    LOGIN_RESPONSE=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
        -X POST http://localhost:8001/login \
        -H "Content-Type: application/x-www-form-urlencoded" \
        -d "_token=$CSRF_TOKEN&email=bruno@sistema.gov.br&password=123456")
    
    if echo "$LOGIN_RESPONSE" | grep -q "dashboard\|proposicoes" || [ ${#LOGIN_RESPONSE} -lt 100 ]; then
        echo "   ✅ Login realizado com sucesso"
        
        echo ""
        echo "2. Acessando página /proposicoes/2..."
        
        # Acessar a página da proposição
        PROPOSICAO_PAGE=$(curl -s -b "$COOKIE_JAR" http://localhost:8001/proposicoes/2)
        
        # Verificar se contém os elementos
        echo ""
        echo "3. Verificando elementos na página autenticada:"
        
        if echo "$PROPOSICAO_PAGE" | grep -q "podeExcluirDocumento"; then
            echo "   ✅ Método podeExcluirDocumento encontrado"
        else
            echo "   ❌ Método podeExcluirDocumento não encontrado"
        fi
        
        if echo "$PROPOSICAO_PAGE" | grep -q "confirmarExclusaoDocumento"; then
            echo "   ✅ Método confirmarExclusaoDocumento encontrado"
        else
            echo "   ❌ Método confirmarExclusaoDocumento não encontrado"
        fi
        
        if echo "$PROPOSICAO_PAGE" | grep -q "Excluir Documento"; then
            echo "   ✅ Texto 'Excluir Documento' encontrado"
        else
            echo "   ❌ Texto 'Excluir Documento' não encontrado"
        fi
        
        if echo "$PROPOSICAO_PAGE" | grep -q "Apenas arquivos PDF/DOCX"; then
            echo "   ✅ Descrição 'Apenas arquivos PDF/DOCX' encontrada"
        else
            echo "   ❌ Descrição específica não encontrada"
        fi
        
        if echo "$PROPOSICAO_PAGE" | grep -q "btn-light-warning"; then
            echo "   ✅ Classe CSS btn-light-warning encontrada"
        else
            echo "   ❌ Classe CSS btn-light-warning não encontrada"
        fi
        
        echo ""
        echo "4. Verificando estrutura da página:"
        
        # Verificar título
        TITLE=$(echo "$PROPOSICAO_PAGE" | grep -o "<title>.*</title>")
        echo "   Título: $TITLE"
        
        # Verificar se tem Vue.js
        if echo "$PROPOSICAO_PAGE" | grep -q "createApp"; then
            echo "   ✅ Vue.js createApp encontrado"
        else
            echo "   ❌ Vue.js createApp não encontrado"
        fi
        
        # Salvar amostra da página para debug
        echo "$PROPOSICAO_PAGE" | grep -C 3 "Excluir Documento" > /tmp/debug_page_sample.txt
        if [ -s /tmp/debug_page_sample.txt ]; then
            echo ""
            echo "5. Amostra do código encontrado:"
            cat /tmp/debug_page_sample.txt
        fi
        
    else
        echo "   ❌ Falha no login"
        echo "   Resposta: ${LOGIN_RESPONSE:0:200}..."
    fi
else
    echo "   ❌ Não foi possível obter CSRF token"
fi

# Limpeza
rm -f "$COOKIE_JAR" /tmp/debug_page_sample.txt

echo ""
echo "=== RESULTADO ==="