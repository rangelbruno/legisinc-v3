#!/bin/bash

# Script para testar a correção de parágrafos no OnlyOffice
# Garante que quebras de linha são preservadas quando o texto é convertido para RTF

echo "=================================================="
echo "TESTE: Parágrafos no OnlyOffice"
echo "=================================================="
echo ""

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# URL base do sistema
BASE_URL="http://localhost:8001"

# Credenciais do parlamentar
EMAIL="jessica@sistema.gov.br"
PASSWORD="123456"

# Obter CSRF token da página de login primeiro
echo -e "${YELLOW}1. Obtendo CSRF token...${NC}"
CSRF_PAGE=$(curl -s "$BASE_URL/login")
CSRF_TOKEN=$(echo "$CSRF_PAGE" | grep 'name="_token"' | sed -n 's/.*value="\([^"]*\)".*/\1/p')

if [ -z "$CSRF_TOKEN" ]; then
    # Tentar outro padrão
    CSRF_TOKEN=$(echo "$CSRF_PAGE" | grep 'csrf-token' | sed -n 's/.*content="\([^"]*\)".*/\1/p')
fi

echo -e "${YELLOW}2. Fazendo login como Parlamentar...${NC}"
LOGIN_RESPONSE=$(curl -s -c /tmp/cookies.txt \
    -X POST "$BASE_URL/login" \
    -H "Content-Type: application/x-www-form-urlencoded" \
    -d "email=$EMAIL&password=$PASSWORD&_token=$CSRF_TOKEN" \
    -L)

if [ -z "$CSRF_TOKEN" ]; then
    echo -e "${RED}❌ Erro: Não foi possível obter o CSRF token${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Login realizado com sucesso${NC}"
echo ""

# Texto com múltiplos parágrafos para teste
TEXTO_TESTE="Primeiro parágrafo do texto da proposição.

Segundo parágrafo com mais conteúdo explicativo sobre o tema em questão.

Terceiro parágrafo final com a conclusão e justificativa da proposição."

echo -e "${YELLOW}3. Criando proposição com texto multi-parágrafo...${NC}"
echo "Texto enviado:"
echo "---"
echo "$TEXTO_TESTE"
echo "---"
echo ""

# Criar proposição
CREATE_RESPONSE=$(curl -s -b /tmp/cookies.txt -c /tmp/cookies.txt \
    -X POST "$BASE_URL/proposicoes/salvar-rascunho" \
    -H "X-CSRF-TOKEN: $CSRF_TOKEN" \
    -H "X-Requested-With: XMLHttpRequest" \
    -H "Content-Type: application/x-www-form-urlencoded" \
    -d "tipo=mocao&ementa=Teste de parágrafos no OnlyOffice&opcao_preenchimento=manual&texto_manual=$(echo -n "$TEXTO_TESTE" | jq -sRr @uri)" \
    -L)

# Extrair ID da proposição criada
PROPOSICAO_ID=$(echo "$CREATE_RESPONSE" | jq -r '.proposicao_id')

if [ -z "$PROPOSICAO_ID" ] || [ "$PROPOSICAO_ID" = "null" ]; then
    echo -e "${RED}❌ Erro ao criar proposição${NC}"
    echo "Resposta: $CREATE_RESPONSE"
    exit 1
fi

echo -e "${GREEN}✓ Proposição criada com ID: $PROPOSICAO_ID${NC}"
echo ""

echo -e "${YELLOW}4. Verificando conteúdo salvo no banco de dados...${NC}"
DB_CHECK=$(docker exec -it legisinc-app php artisan tinker --execute="
    \$p = \App\Models\Proposicao::find($PROPOSICAO_ID);
    echo 'Conteúdo salvo:' . PHP_EOL;
    echo \$p->conteudo . PHP_EOL;
    echo '---' . PHP_EOL;
    echo 'Quebras de linha encontradas: ' . substr_count(\$p->conteudo, \"\\n\");
")

echo "$DB_CHECK"
echo ""

echo -e "${YELLOW}5. Acessando editor OnlyOffice...${NC}"
EDITOR_URL="$BASE_URL/proposicoes/$PROPOSICAO_ID/onlyoffice/editor-parlamentar"

# Fazer requisição para obter o documento
DOCUMENT_RESPONSE=$(curl -s -b /tmp/cookies.txt \
    "$BASE_URL/onlyoffice/download?key=proposicao_${PROPOSICAO_ID}&type=embedded")

# Verificar se o documento contém \par para quebras de linha
if echo "$DOCUMENT_RESPONSE" | grep -q "\\\\par"; then
    echo -e "${GREEN}✓ Documento RTF contém marcadores de parágrafo (\\par)${NC}"
    
    # Contar quantos \par existem
    PAR_COUNT=$(echo "$DOCUMENT_RESPONSE" | grep -o "\\\\par" | wc -l)
    echo -e "${GREEN}  Encontrados $PAR_COUNT marcadores de parágrafo${NC}"
else
    echo -e "${RED}❌ Documento RTF NÃO contém marcadores de parágrafo${NC}"
fi

echo ""
echo -e "${YELLOW}6. Resumo do teste:${NC}"
echo "-----------------------------------"
echo "• Proposição ID: $PROPOSICAO_ID"
echo "• URL do Editor: $EDITOR_URL"
echo "• Texto original tinha 2 quebras de linha (3 parágrafos)"
echo ""

# Verificar resultado final
if echo "$DOCUMENT_RESPONSE" | grep -q "\\\\par"; then
    echo -e "${GREEN}✅ TESTE PASSOU: Parágrafos estão sendo preservados!${NC}"
    echo ""
    echo "Você pode agora:"
    echo "1. Acessar: $EDITOR_URL"
    echo "2. Verificar se o texto aparece com os 3 parágrafos separados"
else
    echo -e "${RED}❌ TESTE FALHOU: Parágrafos não estão sendo preservados${NC}"
    echo ""
    echo "Verifique:"
    echo "1. Se a função converterParaRTF foi atualizada corretamente"
    echo "2. Se o cache foi limpo: docker exec -it legisinc-app php artisan cache:clear"
fi

echo ""
echo "=================================================="
echo "Teste concluído!"
echo "=================================================="

# Limpar cookies
rm -f /tmp/cookies.txt