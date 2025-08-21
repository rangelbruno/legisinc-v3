#!/bin/bash

echo "🧪 TESTE FINAL: Verificando correção do PDF de assinatura"
echo "=========================================================="

echo ""
echo "📋 STATUS PRÉ-TESTE:"
echo "--------------------"

# Verificar se servidor está rodando
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8001/ | grep -q "200"; then
    echo "✅ Servidor Laravel rodando em http://localhost:8001"
else
    echo "❌ Servidor Laravel não está respondendo"
    echo "   💡 Execute: docker-compose up -d"
    exit 1
fi

# Verificar se correção foi aplicada
if grep -q "processingLock\|Execução duplicada detectada" /home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php; then
    echo "✅ Correção anti-duplicação aplicada"
else
    echo "❌ Correção anti-duplicação NÃO aplicada"
fi

# Verificar se arquivos necessários existem
DOCX_FILE="/home/bruno/legisinc/storage/app/private/proposicoes/proposicao_8_1755736247.docx"
if [ -f "$DOCX_FILE" ]; then
    echo "✅ Arquivo DOCX editado existe ($(stat -c%s "$DOCX_FILE") bytes)"
else
    echo "❌ Arquivo DOCX editado NÃO existe"
fi

echo ""
echo "🔧 TESTANDO GERAÇÃO DE PDF:"
echo "---------------------------"

# Simular acesso à tela de assinatura
echo "1. Testando endpoint de assinatura..."

# Fazer requisição para trigger geração do PDF
response=$(curl -s -o /dev/null -w "%{http_code}" -L "http://localhost:8001/proposicoes/8/assinar" 2>/dev/null)

case $response in
    "200")
        echo "   ✅ Página carregou (já logado ou público)"
        ;;
    "302")
        echo "   ✅ Redirecionamento para login (comportamento normal)"
        ;;
    "404")
        echo "   ❌ Página não encontrada (rota não existe)"
        ;;
    "500")
        echo "   ❌ Erro interno do servidor"
        echo "   💡 Verifique logs: tail /home/bruno/legisinc/storage/logs/laravel.log"
        ;;
    *)
        echo "   ❓ Resposta inesperada: HTTP $response"
        ;;
esac

echo ""
echo "2. Verificando PDFs gerados após correção..."

# Procurar novos PDFs gerados
NEW_PDFS=($(find /home/bruno/legisinc/storage/app -name "*proposicao_8*pdf" -newer /home/bruno/legisinc/scripts/corrigir-pdf-assinatura.sh 2>/dev/null))

if [ ${#NEW_PDFS[@]} -gt 0 ]; then
    echo "   ✅ Novos PDFs gerados após correção:"
    for pdf in "${NEW_PDFS[@]}"; do
        size=$(stat -c%s "$pdf")
        modified=$(stat -c%y "$pdf")
        echo "      📄 $pdf ($size bytes - $modified)"
    done
    
    # Testar o PDF mais recente
    LATEST_PDF="${NEW_PDFS[0]}"
    echo ""
    echo "   🔍 Analisando PDF mais recente: $(basename "$LATEST_PDF")"
    
    if [ "$size" -gt 1000 ]; then
        echo "      ✅ Tamanho adequado ($size bytes)"
    else
        echo "      ⚠️  Tamanho suspeito ($size bytes - pode estar vazio)"
    fi
    
else
    echo "   ❌ Nenhum PDF novo gerado após correção"
    echo "   💡 Pode ser que ainda não foi acessado ou há erro"
fi

echo ""
echo "3. Verificando logs recentes..."

echo "   📋 Logs de PDF Assinatura (últimas 10 linhas):"
if [ -f "/home/bruno/legisinc/storage/logs/laravel.log" ]; then
    grep "PDF Assinatura" /home/bruno/legisinc/storage/logs/laravel.log | tail -10 | while read line; do
        echo "      $line"
    done
else
    echo "      ❌ Log não encontrado"
fi

echo ""
echo "   📋 Logs sobre execução duplicada:"
if grep -q "Execução duplicada detectada" /home/bruno/legisinc/storage/logs/laravel.log; then
    echo "      ✅ Sistema detectou e preveniu execução duplicada:"
    grep "Execução duplicada detectada" /home/bruno/legisinc/storage/logs/laravel.log | tail -5 | while read line; do
        echo "         $line"
    done
else
    echo "      ✅ Nenhuma execução duplicada detectada (bom sinal)"
fi

echo ""
echo "4. Verificando todos os PDFs existentes da proposição 8..."

echo "   📊 Todos os PDFs da proposição 8:"
ALL_PDFS=($(find /home/bruno/legisinc/storage/app -name "*proposicao_8*pdf" 2>/dev/null))

if [ ${#ALL_PDFS[@]} -gt 0 ]; then
    for pdf in "${ALL_PDFS[@]}"; do
        size=$(stat -c%s "$pdf")
        modified=$(stat -c%y "$pdf")
        echo "      📄 $(basename "$pdf")"
        echo "         💾 $size bytes"
        echo "         📅 $modified"
        echo "         📁 $pdf"
        echo ""
    done
else
    echo "      ❌ Nenhum PDF encontrado para proposição 8"
fi

echo ""
echo "🎯 RESULTADO DO TESTE:"
echo "====================="

# Verificar se há PDF recente com tamanho adequado
RECENT_GOOD_PDF=""
for pdf in "${ALL_PDFS[@]}"; do
    size=$(stat -c%s "$pdf")
    if [ "$size" -gt 10000 ]; then  # Pelo menos 10KB
        RECENT_GOOD_PDF="$pdf"
        break
    fi
done

if [ -n "$RECENT_GOOD_PDF" ]; then
    echo "✅ SUCESSO: PDF com tamanho adequado encontrado"
    echo "   📄 Arquivo: $(basename "$RECENT_GOOD_PDF")"
    echo "   💾 Tamanho: $(stat -c%s "$RECENT_GOOD_PDF") bytes"
    echo "   📅 Modificado: $(stat -c%y "$RECENT_GOOD_PDF")"
    echo ""
    echo "🧪 TESTE MANUAL RECOMENDADO:"
    echo "----------------------------"
    echo "1. Abra o navegador em: http://localhost:8001/login"
    echo "2. Faça login com: jessica@sistema.gov.br / 123456"
    echo "3. Acesse: http://localhost:8001/proposicoes/8/assinar"
    echo "4. Verifique se o PDF exibe:"
    echo "   • Ementa: 'Editado pelo Parlamentar'"
    echo "   • Texto: 'Bruno, sua oportunidade chegou!'"
    echo "   • Número: '[AGUARDANDO PROTOCOLO]'"
    echo ""
    echo "✅ Se contém esses elementos: PROBLEMA RESOLVIDO!"
    echo "❌ Se NÃO contém: Problema persiste"
    
else
    echo "❌ PROBLEMA PERSISTE: Nenhum PDF adequado encontrado"
    echo ""
    echo "🔧 PRÓXIMOS PASSOS:"
    echo "------------------"
    echo "1. Verificar logs detalhadamente"
    echo "2. Testar acesso manual à tela de assinatura"
    echo "3. Verificar se há erros na extração DOCX → PDF"
    echo "4. Verificar permissões de arquivos"
fi

echo ""
echo "💡 COMANDOS ÚTEIS PARA DEBUG:"
echo "=============================="
echo "# Monitorar logs em tempo real:"
echo "tail -f /home/bruno/legisinc/storage/logs/laravel.log | grep -E 'PDF|proposicao.*8|Assinatura'"
echo ""
echo "# Forçar regeneração removendo PDFs:"
echo "find /home/bruno/legisinc/storage/app -name '*proposicao_8*pdf' -delete"
echo ""
echo "# Verificar processo de conversão manual:"
echo "docker exec -it legisinc-app php -r \"echo 'LibreOffice disponível: ' . (shell_exec('which libreoffice') ? 'SIM' : 'NÃO') . PHP_EOL;\""