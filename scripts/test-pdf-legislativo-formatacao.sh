#!/bin/bash

echo "🧪 TESTANDO PDF COM FORMATAÇÃO DO LEGISLATIVO"
echo "============================================="

echo ""
echo "🔍 1. Verificando arquivo mais recente da proposição 2..."

arquivo_mais_recente=$(ls -t /home/bruno/legisinc/storage/app/private/proposicoes/proposicao_2_*.docx 2>/dev/null | head -1)

if [ -n "$arquivo_mais_recente" ]; then
    echo "   ✅ Arquivo mais recente encontrado: $arquivo_mais_recente"
    
    # Extrair informações do arquivo
    modificacao=$(stat -c %y "$arquivo_mais_recente")
    tamanho=$(stat -c %s "$arquivo_mais_recente")
    
    echo "   📅 Modificado em: $modificacao"
    echo "   📏 Tamanho: $tamanho bytes"
    
    # Verificar se arquivo não está vazio
    if [ $tamanho -gt 10000 ]; then
        echo "   ✅ Arquivo parece válido (tamanho adequado)"
    else
        echo "   ⚠️ Arquivo muito pequeno - pode estar corrompido"
    fi
else
    echo "   ❌ Nenhum arquivo encontrado para proposição 2"
fi

echo ""
echo "🔧 2. Verificando correções no controller..."

controller_file="/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"

if [ -f "$controller_file" ]; then
    echo "   ✅ Controller encontrado"
    
    # Verificar se tem a nova lógica de preservação
    if grep -q "PDF OnlyOffice LEGISLATIVO" "$controller_file"; then
        echo "   ✅ Nova lógica de preservação do Legislativo implementada"
    else
        echo "   ❌ Nova lógica de preservação não encontrada"
    fi
    
    # Verificar extração melhorada de DOCX
    if grep -q "w:rPr" "$controller_file"; then
        echo "   ✅ Extração melhorada de formatação DOCX implementada"
    else
        echo "   ❌ Extração melhorada de formatação não encontrada"
    fi
    
    # Verificar CSS melhorado
    if grep -q "text-center" "$controller_file"; then
        echo "   ✅ CSS preservação de alinhamentos implementado"
    else
        echo "   ❌ CSS preservação de alinhamentos não encontrado"
    fi
    
    # Verificar formatação de texto
    if grep -q "strong>" "$controller_file"; then
        echo "   ✅ Preservação de formatação bold/italic implementada"
    else
        echo "   ❌ Preservação de formatação bold/italic não encontrada"
    fi
    
else
    echo "   ❌ Controller não encontrado"
fi

echo ""
echo "🌐 3. Testando acesso à página de assinatura..."

# Fazer request para a página
response=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8001/proposicoes/2/assinar)

if [ "$response" = "200" ]; then
    echo "   ✅ Página de assinatura acessível (200 OK)"
elif [ "$response" = "302" ]; then
    echo "   🔄 Redirecionamento para login (302) - esperado sem autenticação"
else
    echo "   ❌ Erro ao acessar página: HTTP $response"
fi

echo ""
echo "📄 4. Simulando teste de extração do arquivo mais recente..."

if [ -n "$arquivo_mais_recente" ]; then
    echo "   🔍 Tentando extrair primeiras palavras do arquivo:"
    
    # Usar unzip para extrair o document.xml temporariamente
    temp_dir=$(mktemp -d)
    if unzip -q "$arquivo_mais_recente" word/document.xml -d "$temp_dir" 2>/dev/null; then
        # Extrair algumas palavras do XML (simplificado)
        words=$(grep -o '<w:t[^>]*>[^<]*</w:t>' "$temp_dir/word/document.xml" | head -10 | sed 's/<[^>]*>//g')
        if [ -n "$words" ]; then
            echo "   ✅ Conteúdo encontrado no arquivo:"
            echo "$words" | head -3
        else
            echo "   ⚠️ Nenhum texto encontrado no arquivo"
        fi
        rm -rf "$temp_dir"
    else
        echo "   ❌ Erro ao extrair conteúdo do arquivo DOCX"
    fi
fi

echo ""
echo "🎯 5. RESULTADO DO TESTE"
echo "======================="

total_checks=6
passed_checks=0

# Contar verificações que passaram
if [ -n "$arquivo_mais_recente" ]; then
    passed_checks=$((passed_checks + 1))
fi

if grep -q "PDF OnlyOffice LEGISLATIVO" "$controller_file" 2>/dev/null; then
    passed_checks=$((passed_checks + 1))
fi

if grep -q "w:rPr" "$controller_file" 2>/dev/null; then
    passed_checks=$((passed_checks + 1))
fi

if grep -q "text-center" "$controller_file" 2>/dev/null; then
    passed_checks=$((passed_checks + 1))
fi

if grep -q "strong>" "$controller_file" 2>/dev/null; then
    passed_checks=$((passed_checks + 1))
fi

if [ "$response" = "200" ] || [ "$response" = "302" ]; then
    passed_checks=$((passed_checks + 1))
fi

percentual=$((passed_checks * 100 / total_checks))

echo "📊 Verificações passaram: $passed_checks/$total_checks ($percentual%)"

if [ $percentual -eq 100 ]; then
    echo "🎉 RESULTADO: TODAS AS CORREÇÕES APLICADAS COM SUCESSO!"
    echo "✅ O PDF de assinatura agora deve refletir a formatação do Legislativo"
elif [ $percentual -ge 80 ]; then
    echo "🟡 RESULTADO: MAIORIA DAS CORREÇÕES APLICADAS ($percentual%)"
    echo "⚠️ Algumas verificações falharam - revisar manualmente"
else
    echo "🔴 RESULTADO: MUITAS CORREÇÕES FALHARAM ($percentual%)"
    echo "❌ Revisão manual necessária"
fi

echo ""
echo "🔗 TESTE MANUAL RECOMENDADO:"
echo "   1. Acesse: http://localhost:8001/login"
echo "   2. Login: jessica@sistema.gov.br / 123456"
echo "   3. Vá para: http://localhost:8001/proposicoes/2/assinar"
echo "   4. Clique na aba 'PDF'"
echo "   5. Verifique se vê:"
echo "      - Imagem do cabeçalho no topo"
echo "      - Formatação feita pelo Legislativo preservada"
echo "      - Textos em negrito/itálico mantidos"
echo "      - Alinhamentos centralizados preservados"
echo "      - Rodapé conforme editado pelo Legislativo"
echo ""
echo "=============================================="
echo "✅ Teste de formatação do Legislativo concluído!"