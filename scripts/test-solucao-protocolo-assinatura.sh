#!/bin/bash

echo "🧪 TESTE COMPLETO: Solução Protocolo + Assinatura"
echo "=================================================="

# 1. Verificar estado atual da proposição 3
echo "📋 1. Estado atual da proposição 3:"
docker exec legisinc-app php artisan tinker --execute="
\$p = App\Models\Proposicao::find(3);
echo '- ID: ' . \$p->id . PHP_EOL;
echo '- Protocolo: ' . (\$p->numero_protocolo ?? 'NULL') . PHP_EOL;
echo '- Status: ' . \$p->status . PHP_EOL;
echo '- Assinatura: ' . (\$p->assinatura_digital ? 'SIM' : 'NÃO') . PHP_EOL;
echo '- PDF Path: ' . (\$p->arquivo_pdf_path ?? 'NULL') . PHP_EOL;
"

echo ""

# 2. Verificar PDF mais recente
echo "📁 2. Arquivos PDF da proposição 3:"
find /home/bruno/legisinc/storage/app -name "*proposicao_3*" -name "*.pdf" -type f -printf "%T@ %p\n" | sort -n | tail -3

echo ""

# 3. Verificar conteúdo do PDF mais recente
echo "📄 3. Conteúdo do PDF mais recente:"
LATEST_PDF=$(find /home/bruno/legisinc/storage/app -name "*proposicao_3*" -name "*.pdf" -type f -printf "%T@ %p\n" | sort -n | tail -1 | cut -d' ' -f2)
echo "Arquivo: $LATEST_PDF"

if [ ! -z "$LATEST_PDF" ]; then
    echo "Primeiras linhas do PDF:"
    docker exec legisinc-app pdftotext "$LATEST_PDF" - | head -5
    
    echo ""
    echo "🔍 Verificações específicas:"
    
    # Verificar se contém número de protocolo
    if docker exec legisinc-app pdftotext "$LATEST_PDF" - | grep -q "mocao/2025/0002"; then
        echo "✅ Número de protocolo correto encontrado: mocao/2025/0002"
    else
        echo "❌ Número de protocolo NÃO encontrado"
    fi
    
    # Verificar se não contém placeholder
    if docker exec legisinc-app pdftotext "$LATEST_PDF" - | grep -q "\[AGUARDANDO PROTOCOLO\]"; then
        echo "❌ Ainda contém [AGUARDANDO PROTOCOLO]"
    else
        echo "✅ Placeholder [AGUARDANDO PROTOCOLO] removido"
    fi
    
    # Verificar assinatura digital
    if docker exec legisinc-app pdftotext "$LATEST_PDF" - | grep -q "Assinatura Digital"; then
        echo "✅ Assinatura digital presente"
    else
        echo "❌ Assinatura digital NÃO encontrada"
    fi
fi

echo ""

# 4. Testar método encontrarPDFMaisRecente
echo "🔧 4. Teste do método encontrarPDFMaisRecente:"
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(3);
\$controller = new App\Http\Controllers\ProposicaoController();
\$reflection = new ReflectionClass(\$controller);
\$method = \$reflection->getMethod('encontrarPDFMaisRecente');
\$method->setAccessible(true);
\$pdfPath = \$method->invoke(\$controller, \$proposicao);
echo 'PDF encontrado: ' . (\$pdfPath ? basename(\$pdfPath) : 'NULL') . PHP_EOL;
"

echo ""

# 5. Resultado final
echo "🎯 5. RESULTADO FINAL:"
echo "========================"

# Verificar se a solução está completa
SUCCESS_COUNT=0

# Check 1: Protocolo atribuído
if docker exec legisinc-app php artisan tinker --execute="echo App\Models\Proposicao::find(3)->numero_protocolo;" | grep -q "mocao/2025/0002"; then
    echo "✅ Protocolo atribuído corretamente"
    ((SUCCESS_COUNT++))
else
    echo "❌ Protocolo não atribuído"
fi

# Check 2: PDF com protocolo correto
if [ ! -z "$LATEST_PDF" ] && docker exec legisinc-app pdftotext "$LATEST_PDF" - | grep -q "mocao/2025/0002"; then
    echo "✅ PDF mostra número de protocolo correto"
    ((SUCCESS_COUNT++))
else
    echo "❌ PDF não mostra protocolo correto"
fi

# Check 3: Assinatura presente
if [ ! -z "$LATEST_PDF" ] && docker exec legisinc-app pdftotext "$LATEST_PDF" - | grep -q "Assinatura Digital"; then
    echo "✅ Assinatura digital presente no PDF"
    ((SUCCESS_COUNT++))
else
    echo "❌ Assinatura digital ausente"
fi

# Check 4: Placeholder removido
if [ ! -z "$LATEST_PDF" ] && ! docker exec legisinc-app pdftotext "$LATEST_PDF" - | grep -q "\[AGUARDANDO PROTOCOLO\]"; then
    echo "✅ Placeholder removido do PDF"
    ((SUCCESS_COUNT++))
else
    echo "❌ Placeholder ainda presente"
fi

echo ""
echo "📊 SCORE: $SUCCESS_COUNT/4 verificações passaram"

if [ $SUCCESS_COUNT -eq 4 ]; then
    echo "🎉 SOLUÇÃO 100% FUNCIONAL!"
    echo "✅ A proposição agora mostra protocolo E assinatura corretamente"
else
    echo "⚠️  Solução parcial - algumas verificações falharam"
fi

echo ""
echo "🔗 Para testar manualmente:"
echo "1. Acesse: http://localhost:8001/login"
echo "2. Login: jessica@sistema.gov.br / 123456"
echo "3. Vá para: http://localhost:8001/proposicoes/3"
echo "4. Clique em 'Ver PDF' e verifique o conteúdo"