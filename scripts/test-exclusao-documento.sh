#!/bin/bash

echo "=== TESTE DE EXCLUSÃO DE DOCUMENTO ==="
echo "Testando funcionalidade de exclusão de documento antes da assinatura"
echo ""

# Verificar estado atual da proposição 2
echo "1. Estado atual da proposição 2:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, tipo, ementa, status, arquivo_path IS NOT NULL as tem_arquivo, arquivo_pdf_path IS NOT NULL as tem_pdf FROM proposicoes WHERE id = 2;"
echo ""

# Verificar se existem arquivos no sistema de arquivos
echo "2. Verificando arquivos existentes:"
echo "   - Diretório proposicoes/2:"
if [ -d "/home/bruno/legisinc/storage/app/proposicoes/2" ]; then
    ls -la /home/bruno/legisinc/storage/app/proposicoes/2/
else
    echo "     Diretório não existe"
fi

echo "   - Diretório proposicoes/pdfs/2:"
if [ -d "/home/bruno/legisinc/storage/app/proposicoes/pdfs/2" ]; then
    ls -la /home/bruno/legisinc/storage/app/proposicoes/pdfs/2/
else
    echo "     Diretório não existe"
fi

echo "   - Arquivos proposicoes/*.rtf ou *.docx:"
find /home/bruno/legisinc/storage/app/proposicoes/ -name "*" -type f | grep -E "\.(rtf|docx|pdf)$" | head -10
echo ""

# Testar a rota de exclusão via curl (simulando o que o Vue.js faria)
echo "3. Testando rota de exclusão via API:"
echo "   Fazendo requisição DELETE para /proposicoes/2/excluir-documento"

# Primeiro vamos obter o CSRF token
CSRF_TOKEN=$(curl -s -c /tmp/cookies.txt http://localhost:8001/login | grep -oP 'name="_token" value="\K[^"]+')

if [ -z "$CSRF_TOKEN" ]; then
    echo "   ❌ Não foi possível obter CSRF token"
    echo "   Verifique se o servidor está rodando em http://localhost:8001"
else
    echo "   ✅ CSRF Token obtido: ${CSRF_TOKEN:0:20}..."
    
    # Fazer login como usuário com permissão (bruno@sistema.gov.br)
    echo "   Fazendo login como administrador..."
    LOGIN_RESPONSE=$(curl -s -b /tmp/cookies.txt -c /tmp/cookies.txt \
        -X POST http://localhost:8001/login \
        -H "Content-Type: application/x-www-form-urlencoded" \
        -d "_token=$CSRF_TOKEN&email=bruno@sistema.gov.br&password=123456")
    
    if echo "$LOGIN_RESPONSE" | grep -q "dashboard\|proposicoes"; then
        echo "   ✅ Login realizado com sucesso"
        
        # Agora testar a exclusão
        echo "   Executando exclusão do documento..."
        EXCLUSAO_RESPONSE=$(curl -s -b /tmp/cookies.txt \
            -X DELETE http://localhost:8001/proposicoes/2/excluir-documento \
            -H "Content-Type: application/json" \
            -H "X-CSRF-TOKEN: $CSRF_TOKEN" \
            -H "Accept: application/json")
        
        echo "   Resposta da API:"
        echo "$EXCLUSAO_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$EXCLUSAO_RESPONSE"
        
    else
        echo "   ❌ Falha no login"
        echo "   Resposta: ${LOGIN_RESPONSE:0:200}..."
    fi
fi

echo ""

# Verificar estado após tentativa de exclusão
echo "4. Estado da proposição após teste:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, tipo, ementa, status, arquivo_path IS NOT NULL as tem_arquivo, arquivo_pdf_path IS NOT NULL as tem_pdf FROM proposicoes WHERE id = 2;"
echo ""

# Verificar arquivos após exclusão
echo "5. Verificando arquivos após exclusão:"
echo "   - Diretório proposicoes/2:"
if [ -d "/home/bruno/legisinc/storage/app/proposicoes/2" ]; then
    ls -la /home/bruno/legisinc/storage/app/proposicoes/2/ || echo "     Diretório vazio ou removido"
else
    echo "     ✅ Diretório removido com sucesso"
fi

echo ""

# Limpeza
rm -f /tmp/cookies.txt

echo "=== TESTE CONCLUÍDO ==="
echo ""
echo "✅ Funcionalidades implementadas:"
echo "   - Rota DELETE /proposicoes/{id}/excluir-documento"
echo "   - Método excluirDocumento() no ProposicaoAssinaturaController"
echo "   - Botão de exclusão na interface Vue.js"
echo "   - Modal de confirmação com checkbox obrigatório"
echo "   - Validações de permissão e status"
echo "   - Limpeza completa de arquivos e diretórios"
echo ""
echo "🔗 Para testar manualmente:"
echo "   1. Acesse: http://localhost:8001/proposicoes/2/assinar"
echo "   2. Login: bruno@sistema.gov.br / 123456"
echo "   3. Procure pelo botão 'Excluir Documento' na lateral esquerda"
echo "   4. Confirme a exclusão e verifique os resultados"