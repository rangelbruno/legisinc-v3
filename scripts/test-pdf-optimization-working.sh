#!/bin/bash

echo "🧪 TESTANDO RECURSOS DE PDF OTIMIZADO PRESERVADOS"
echo "=================================================="

echo ""
echo "🔍 1. Verificando existência dos arquivos críticos..."

# Verificar controller
if [ -f "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php" ]; then
    echo "   ✅ ProposicaoAssinaturaController.php - Presente"
    
    # Verificar métodos críticos
    if grep -q "gerarHTMLSimulandoOnlyOffice" /home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php; then
        echo "   ✅ Método gerarHTMLSimulandoOnlyOffice - Presente"
    else
        echo "   ❌ Método gerarHTMLSimulandoOnlyOffice - AUSENTE"
    fi
    
    if grep -q "obterImagemCabecalhoBase64" /home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php; then
        echo "   ✅ Método obterImagemCabecalhoBase64 - Presente"
    else
        echo "   ❌ Método obterImagemCabecalhoBase64 - AUSENTE"
    fi
    
    # Verificar processamento de imagem
    if grep -q "imagem_cabecalho" /home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php; then
        echo "   ✅ Processamento de \${imagem_cabecalho} - Presente"
    else
        echo "   ❌ Processamento de \${imagem_cabecalho} - AUSENTE"
    fi
    
    # Verificar CSS otimizado
    if grep -q "line-height: 1.4" /home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php; then
        echo "   ✅ CSS otimizado (line-height: 1.4) - Presente"
    else
        echo "   ❌ CSS otimizado (line-height: 1.4) - AUSENTE"
    fi
    
    if grep -q "br + br" /home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php; then
        echo "   ✅ CSS para remover quebras duplas - Presente"
    else
        echo "   ❌ CSS para remover quebras duplas - AUSENTE"
    fi
    
else
    echo "   ❌ ProposicaoAssinaturaController.php - AUSENTE"
fi

echo ""
echo "🖼️ 2. Verificando imagem do cabeçalho..."

if [ -f "/home/bruno/legisinc/public/template/cabecalho.png" ]; then
    echo "   ✅ Imagem cabecalho.png - Presente"
    
    # Verificar tamanho do arquivo
    size=$(stat -c%s "/home/bruno/legisinc/public/template/cabecalho.png")
    if [ $size -gt 1000 ]; then
        echo "   ✅ Tamanho da imagem: $size bytes (válido)"
    else
        echo "   ⚠️ Tamanho da imagem: $size bytes (muito pequeno)"
    fi
else
    echo "   ❌ Imagem cabecalho.png - AUSENTE"
fi

echo ""
echo "💻 3. Verificando interface Vue.js..."

if [ -f "/home/bruno/legisinc/resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php" ]; then
    echo "   ✅ Interface Vue.js - Presente"
    
    # Verificar botão Fonte
    if grep -q "Fonte" /home/bruno/legisinc/resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php; then
        echo "   ✅ Botão 'Fonte' - Presente"
    else
        echo "   ❌ Botão 'Fonte' - AUSENTE"
    fi
    
    # Verificar toggle view
    if grep -q "toggleView" /home/bruno/legisinc/resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php; then
        echo "   ✅ Função toggleView - Presente"
    else
        echo "   ❌ Função toggleView - AUSENTE"
    fi
    
    # Verificar visualização source
    if grep -q "viewMode === 'source'" /home/bruno/legisinc/resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php; then
        echo "   ✅ Visualização Source - Presente"
    else
        echo "   ❌ Visualização Source - AUSENTE"
    fi
    
else
    echo "   ❌ Interface Vue.js - AUSENTE"
fi

echo ""
echo "🌐 4. Verificando rotas..."

if [ -f "/home/bruno/legisinc/routes/web.php" ]; then
    if grep -q "proposicoes.pdf-original" /home/bruno/legisinc/routes/web.php; then
        echo "   ✅ Rota proposicoes.pdf-original - Presente"
    else
        echo "   ❌ Rota proposicoes.pdf-original - AUSENTE"
    fi
    
    if grep -q "proposicoes.assinar" /home/bruno/legisinc/routes/web.php; then
        echo "   ✅ Rota proposicoes.assinar - Presente"
    else
        echo "   ❌ Rota proposicoes.assinar - AUSENTE"
    fi
else
    echo "   ❌ Arquivo de rotas - AUSENTE"
fi

echo ""
echo "📊 5. RESUMO DOS RECURSOS DE PDF OTIMIZADO"
echo "=========================================="

total_recursos=8
recursos_presentes=0

# Contar recursos presentes
if [ -f "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php" ]; then
    if grep -q "gerarHTMLSimulandoOnlyOffice" /home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php; then
        recursos_presentes=$((recursos_presentes + 1))
    fi
    if grep -q "obterImagemCabecalhoBase64" /home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php; then
        recursos_presentes=$((recursos_presentes + 1))
    fi
    if grep -q "imagem_cabecalho" /home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php; then
        recursos_presentes=$((recursos_presentes + 1))
    fi
    if grep -q "line-height: 1.4" /home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php; then
        recursos_presentes=$((recursos_presentes + 1))
    fi
    if grep -q "br + br" /home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php; then
        recursos_presentes=$((recursos_presentes + 1))
    fi
fi

if [ -f "/home/bruno/legisinc/public/template/cabecalho.png" ]; then
    recursos_presentes=$((recursos_presentes + 1))
fi

if [ -f "/home/bruno/legisinc/resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php" ]; then
    if grep -q "Fonte" /home/bruno/legisinc/resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php; then
        recursos_presentes=$((recursos_presentes + 1))
    fi
    if grep -q "toggleView" /home/bruno/legisinc/resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php; then
        recursos_presentes=$((recursos_presentes + 1))
    fi
fi

percentual=$((recursos_presentes * 100 / total_recursos))

echo ""
echo "📈 Status dos Recursos de PDF Otimizado:"
echo "   Recursos presentes: $recursos_presentes/$total_recursos"
echo "   Percentual de preservação: $percentual%"
echo ""

if [ $percentual -eq 100 ]; then
    echo "🎉 RESULTADO: TODOS OS RECURSOS DE PDF OTIMIZADO ESTÃO PRESERVADOS!"
    echo "✅ A solução documentada em SOLUCAO_PDF_OTIMIZADO.md está ATIVA"
elif [ $percentual -gt 80 ]; then
    echo "🟡 RESULTADO: MAIORIA DOS RECURSOS PRESERVADOS ($percentual%)"
    echo "⚠️ Alguns recursos podem precisar de ajustes menores"
elif [ $percentual -gt 50 ]; then
    echo "🟠 RESULTADO: RECURSOS PARCIALMENTE PRESERVADOS ($percentual%)"
    echo "⚠️ Alguns recursos importantes podem estar ausentes"
else
    echo "🔴 RESULTADO: MUITOS RECURSOS AUSENTES ($percentual%)"
    echo "❌ A solução pode precisar ser restaurada"
fi

echo ""
echo "🔗 PRÓXIMOS PASSOS:"
echo "   1. Teste manual: http://localhost:8001/proposicoes/2/assinar"
echo "   2. Login: jessica@sistema.gov.br / 123456"
echo "   3. Clique na aba 'PDF' para ver o preview"
echo "   4. Clique no botão 'Fonte' para ver o HTML"
echo "   5. Verifique se a imagem do cabeçalho aparece"
echo "   6. Verifique se o espaçamento está otimizado"
echo ""
echo "=============================================="
echo "✅ Teste de verificação de PDF otimizado concluído!"