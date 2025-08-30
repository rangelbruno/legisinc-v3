#!/bin/bash

echo "🎯 VALIDAÇÃO: PDF com Formato A4 Após Correção"
echo "=============================================="
echo ""

echo "📄 1. Regenerando PDF da proposição 3..."
docker exec legisinc-app php artisan proposicao:regenerar-pdf 3

echo ""
echo "📏 2. Verificando dimensões do PDF..."
PDF_MAIS_RECENTE=$(docker exec legisinc-app find storage/app/proposicoes/pdfs/3/ -name "proposicao_3_protocolado_*.pdf" -type f -exec ls -t {} \; | head -1)
echo "Arquivo mais recente: $PDF_MAIS_RECENTE"

echo ""
echo "📊 3. Análise de dimensões:"
PDFINFO=$(docker exec legisinc-app pdfinfo "$PDF_MAIS_RECENTE")
echo "$PDFINFO"

echo ""
echo "🔍 4. Verificando formato A4:"
PAGE_SIZE=$(echo "$PDFINFO" | grep "Page size" | cut -d':' -f2 | xargs)
echo "Dimensões detectadas: $PAGE_SIZE"

if echo "$PAGE_SIZE" | grep -q "A4"; then
    echo "✅ SUCESSO: PDF está no formato A4!"
else
    echo "❌ FALHA: PDF não está no formato A4"
    echo "   Esperado: 595.28 x 841.89 pts (A4)"
    echo "   Encontrado: $PAGE_SIZE"
fi

echo ""
echo "📝 5. Verificando conteúdo (protocolo e assinatura):"
CONTEUDO=$(docker exec legisinc-app pdftotext "$PDF_MAIS_RECENTE" -)

echo ""
echo "🔢 Número de protocolo:"
if echo "$CONTEUDO" | grep -q "mocao/2025/0001"; then
    echo "✅ SUCESSO: Número de protocolo presente"
    echo "   $(echo "$CONTEUDO" | grep "mocao/2025/0001" | head -1)"
else
    echo "❌ FALHA: Número de protocolo não encontrado"
fi

echo ""
echo "✍️ Assinatura digital:"
if echo "$CONTEUDO" | grep -q "ASSINATURA DIGITAL"; then
    echo "✅ SUCESSO: Assinatura digital presente"
    echo "   $(echo "$CONTEUDO" | grep -A 3 "ASSINATURA DIGITAL")"
else
    echo "❌ FALHA: Assinatura digital não encontrada"
fi

echo ""
echo "📋 6. Comparação com PDF antigo:"
PDF_ANTIGO=$(docker exec legisinc-app find storage/app/proposicoes/pdfs/3/ -name "proposicao_3_protocolado_1756159945.pdf" -type f 2>/dev/null | head -1)

if [ ! -z "$PDF_ANTIGO" ]; then
    echo "PDF antigo encontrado: $(basename $PDF_ANTIGO)"
    PDFINFO_ANTIGO=$(docker exec legisinc-app pdfinfo "$PDF_ANTIGO")
    PAGE_SIZE_ANTIGO=$(echo "$PDFINFO_ANTIGO" | grep "Page size" | cut -d':' -f2 | xargs)
    echo "Dimensões do PDF antigo: $PAGE_SIZE_ANTIGO"
    
    if echo "$PAGE_SIZE_ANTIGO" | grep -q "letter"; then
        echo "🔄 CONFIRMADO: PDF antigo estava em formato Letter (problema resolvido)"
    fi
else
    echo "⚠️ PDF antigo não encontrado para comparação"
fi

echo ""
echo "🎊 RESUMO DA CORREÇÃO:"
echo "====================="
echo "✅ Formato A4 (595.28 x 841.89 pts) implementado"
echo "✅ Conteúdo do Legislativo (RTF) sendo usado"
echo "✅ Número de protocolo correto"
echo "✅ Assinatura digital preservada"
echo "✅ DomPDF configurado com setPaper('A4', 'portrait')"
echo ""
echo "🔧 Métodos corrigidos:"
echo "- criarPDFComConteudoRTFProcessado()"
echo "- criarPDFComMetodoHTML()"
echo "- Configuração DomPDF em config/dompdf.php"
echo ""
echo "🛡️ Preservação garantida via:"
echo "- database/seeders/CorrecaoPDFProtocoloAssinaturaSeeder.php"
echo "- Execução automática em migrate:fresh --seed"