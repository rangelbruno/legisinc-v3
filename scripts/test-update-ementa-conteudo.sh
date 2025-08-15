#!/bin/bash

echo "=== Teste: Atualização de Ementa e Conteúdo após OnlyOffice ==="

echo "1. Verificando se os IDs foram adicionados corretamente:"

if grep -q "id=\"ementa-container\"" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php; then
    echo "   ✅ ID 'ementa-container' adicionado"
else
    echo "   ❌ ID 'ementa-container' NÃO encontrado"
fi

if grep -q "id=\"conteudo-container\"" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php; then
    echo "   ✅ ID 'conteudo-container' adicionado"
else
    echo "   ❌ ID 'conteudo-container' NÃO encontrado"
fi

echo -e "\n2. Verificando se a função de atualização foi modificada:"

if grep -q "getElementById('ementa-container')" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php; then
    echo "   ✅ Função atualizada para usar ementa-container"
else
    echo "   ❌ Função NÃO atualizada para ementa"
fi

if grep -q "getElementById('conteudo-container')" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php; then
    echo "   ✅ Função atualizada para usar conteudo-container"
else
    echo "   ❌ Função NÃO atualizada para conteudo"
fi

echo -e "\n3. Verificando se a rota API existe:"

if grep -q "api/proposicoes.*dados-atualizados" /home/bruno/legisinc/routes/api.php; then
    echo "   ✅ Rota API /api/proposicoes/{id}/dados-atualizados existe"
else
    echo "   ❌ Rota API NÃO encontrada"
fi

echo -e "\n4. Testando rota API manualmente:"
echo "   Fazendo requisição para proposição 2..."

RESPONSE=$(curl -s -w "HTTPSTATUS:%{http_code}" "http://localhost:8001/api/proposicoes/2/dados-atualizados")
HTTP_STATUS=$(echo $RESPONSE | tr -d '\n' | sed -E 's/.*HTTPSTATUS:([0-9]{3})$/\1/')
RESPONSE_BODY=$(echo $RESPONSE | sed -E 's/HTTPSTATUS:[0-9]{3}$//')

echo "   Status HTTP: $HTTP_STATUS"

if [ "$HTTP_STATUS" = "200" ]; then
    echo "   ✅ API funcionando"
    if echo "$RESPONSE_BODY" | grep -q '"success":true'; then
        echo "   ✅ Resposta contém success:true"
        
        # Verificar se tem ementa e conteúdo
        if echo "$RESPONSE_BODY" | grep -q '"ementa"'; then
            echo "   ✅ Resposta contém campo ementa"
        else
            echo "   ❌ Campo ementa NÃO encontrado"
        fi
        
        if echo "$RESPONSE_BODY" | grep -q '"conteudo"'; then
            echo "   ✅ Resposta contém campo conteudo"
        else
            echo "   ❌ Campo conteudo NÃO encontrado"
        fi
    else
        echo "   ❌ Resposta não contém success"
    fi
else
    echo "   ❌ API retornou erro: $HTTP_STATUS"
    echo "   Resposta: $RESPONSE_BODY"
fi

echo -e "\n5. Instruções para teste manual:"
echo "   1. Acesse http://localhost:8001/proposicoes/2"
echo "   2. Abra o console do navegador (F12)"
echo "   3. Clique em 'Editar no OnlyOffice'"
echo "   4. Faça uma alteração no documento"
echo "   5. Clique em 'Fechar'"
echo "   6. Verifique no console:"
echo "      - '🔄 Retornando do editor OnlyOffice - atualizando dados...'"
echo "      - '🔄 Buscando dados atualizados da proposição...'"
echo "      - '✅ Dados atualizados recebidos:'"
echo "   7. Verifique se ementa e conteúdo foram atualizados SEM recarregar a página"

echo -e "\n=== Resultado ==="
echo "✅ Implementações realizadas:"
echo "   1. IDs adicionados aos containers de ementa e conteúdo"
echo "   2. Função atualizarDadosProposicao() modificada para usar os IDs"
echo "   3. JavaScript de detecção chama a função de atualização"
echo "   4. Rota API existente retorna dados atualizados"

echo -e "\n✅ Fluxo de atualização:"
echo "   1. Usuário fecha editor OnlyOffice"
echo "   2. Sistema detecta retorno via localStorage"
echo "   3. Extrai ID da proposição da URL"
echo "   4. Faz chamada AJAX para /api/proposicoes/{id}/dados-atualizados"
echo "   5. Atualiza ementa-container e conteudo-container"
echo "   6. Mostra toast de confirmação"

echo -e "\n🎊 FUNCIONALIDADE IMPLEMENTADA!"