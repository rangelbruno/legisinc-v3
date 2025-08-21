#!/bin/bash

echo "=== TESTE DO BOTÃO DE EXCLUSÃO EM /proposicoes/2 ==="
echo "Testando funcionalidade de exclusão de documento na página de visualização"
echo ""

# Verificar se o servidor está rodando
echo "1. Verificando se o servidor está acessível..."
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8001 | grep -q "200\|302"; then
    echo "   ✅ Servidor rodando em http://localhost:8001"
else
    echo "   ❌ Servidor não está acessível"
    exit 1
fi

# Verificar estado atual da proposição 2
echo ""
echo "2. Estado atual da proposição 2:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, tipo, ementa, status, arquivo_path IS NOT NULL as tem_arquivo, arquivo_pdf_path IS NOT NULL as tem_pdf FROM proposicoes WHERE id = 2;"
echo ""

# Verificar se o botão aparece na página HTML
echo "3. Verificando se o botão de exclusão aparece na página:"
RESPONSE=$(curl -s http://localhost:8001/proposicoes/2)

if echo "$RESPONSE" | grep -q "confirmarExclusaoDocumento"; then
    echo "   ✅ Método Vue confirmarExclusaoDocumento encontrado"
else
    echo "   ❌ Método Vue não encontrado"
fi

if echo "$RESPONSE" | grep -q "Excluir Documento"; then
    echo "   ✅ Botão 'Excluir Documento' encontrado na página"
else
    echo "   ❌ Botão não encontrado na página"
fi

if echo "$RESPONSE" | grep -q "podeExcluirDocumento"; then
    echo "   ✅ Método de validação podeExcluirDocumento encontrado"
else
    echo "   ❌ Método de validação não encontrado"
fi

if echo "$RESPONSE" | grep -q "Apenas arquivos PDF/DOCX"; then
    echo "   ✅ Descrição específica do botão encontrada"
else
    echo "   ❌ Descrição específica não encontrada"
fi

echo ""

# Verificar diferenciação entre os dois botões
echo "4. Verificando diferenciação entre botões:"
if echo "$RESPONSE" | grep -q "Excluir Proposição"; then
    echo "   ✅ Botão 'Excluir Proposição' (remove tudo) encontrado"
else
    echo "   ❌ Botão de exclusão de proposição não encontrado"
fi

if echo "$RESPONSE" | grep -q "Remove completamente"; then
    echo "   ✅ Descrição de exclusão completa encontrada"
else
    echo "   ❌ Descrição de exclusão completa não encontrada"
fi

echo ""

# Verificar se a rota de exclusão existe
echo "5. Verificando rota de exclusão:"
ROUTE_EXISTS=$(curl -s -X DELETE -o /dev/null -w "%{http_code}" http://localhost:8001/proposicoes/2/excluir-documento -H "X-CSRF-TOKEN: test")
if [ "$ROUTE_EXISTS" = "419" ] || [ "$ROUTE_EXISTS" = "403" ] || [ "$ROUTE_EXISTS" = "302" ]; then
    echo "   ✅ Rota DELETE /proposicoes/2/excluir-documento existe (erro de CSRF esperado)"
else
    echo "   ❌ Rota não encontrada (HTTP $ROUTE_EXISTS)"
fi

echo ""

# Verificar estrutura CSS dos botões
echo "6. Verificando estrutura CSS dos botões:"
if echo "$RESPONSE" | grep -q "btn-light-warning"; then
    echo "   ✅ Classe CSS btn-light-warning (botão amarelo) encontrada"
else
    echo "   ❌ Classe CSS do novo botão não encontrada"
fi

if echo "$RESPONSE" | grep -q "btn-light-danger"; then
    echo "   ✅ Classe CSS btn-light-danger (botão vermelho) encontrada"
else
    echo "   ❌ Classe CSS do botão de exclusão de proposição não encontrada"
fi

echo ""

# Verificar arquivos existentes
echo "7. Verificando arquivos que podem ser excluídos:"
echo "   - Diretório proposicoes/pdfs/2:"
if [ -d "/home/bruno/legisinc/storage/app/proposicoes/pdfs/2" ]; then
    echo "     $(ls -la /home/bruno/legisinc/storage/app/proposicoes/pdfs/2/ | wc -l) arquivos encontrados"
    ls -la /home/bruno/legisinc/storage/app/proposicoes/pdfs/2/ | tail -3
else
    echo "     Diretório não existe"
fi

echo ""

echo "=== RESUMO DA IMPLEMENTAÇÃO ==="
echo ""
echo "✅ Funcionalidades implementadas:"
echo "   - Botão 'Excluir Documento' adicionado em /proposicoes/2"
echo "   - Posicionado abaixo do botão 'Visualizar PDF'"
echo "   - Diferenciado do botão 'Excluir Proposição'"
echo "   - Cor amarela (warning) vs vermelho (danger)"
echo "   - Descrição clara: 'Apenas arquivos PDF/DOCX'"
echo "   - Métodos Vue: podeExcluirDocumento(), confirmarExclusaoDocumento(), excluirDocumento()"
echo "   - Modal SweetAlert2 com lista detalhada de arquivos"
echo "   - Validação de status permitidos"
echo "   - Integração com API existente"
echo ""
echo "🔗 Para testar manualmente:"
echo "   1. Acesse: http://localhost:8001/proposicoes/2"
echo "   2. Login: bruno@sistema.gov.br / 123456"
echo "   3. Procure na seção 'Ações' pelo botão amarelo 'Excluir Documento'"
echo "   4. Confirme que está abaixo do botão 'Visualizar PDF'"
echo "   5. Clique e teste o modal de confirmação"
echo ""
echo "🎯 Diferenças entre os botões:"
echo "   📄 'Excluir Documento' (Amarelo): Remove apenas PDF/DOCX"
echo "   🗑️ 'Excluir Proposição' (Vermelho): Remove a proposição inteira"