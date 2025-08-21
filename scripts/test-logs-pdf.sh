#!/bin/bash

echo "🔍 TESTE DE LOGS DO PDF"
echo "======================"

echo ""
echo "📋 1. Limpando logs anteriores..."
echo "" > /home/bruno/legisinc/storage/logs/laravel.log

echo ""
echo "🌐 2. Fazendo request para gerar PDF..."

# Simular request para a página de assinatura (isso deve gerar logs)
curl -s -o /dev/null "http://localhost:8001/proposicoes/2/pdf-original" || true

echo ""
echo "📄 3. Verificando logs gerados..."

if [ -s "/home/bruno/legisinc/storage/logs/laravel.log" ]; then
    echo "   ✅ Arquivo de log tem conteúdo"
    
    # Buscar logs específicos do PDF OnlyOffice
    echo ""
    echo "📊 Logs do PDF OnlyOffice:"
    grep "PDF OnlyOffice" /home/bruno/legisinc/storage/logs/laravel.log | tail -10
    
else
    echo "   ❌ Arquivo de log está vazio"
fi

echo ""
echo "🔗 Para teste manual:"
echo "   1. Acesse: http://localhost:8001/login"
echo "   2. Login: jessica@sistema.gov.br / 123456"
echo "   3. Vá para: http://localhost:8001/proposicoes/2/assinar"
echo "   4. Clique na aba 'PDF'"
echo ""
echo "⏰ Aguarde alguns segundos e execute:"
echo "   tail -f /home/bruno/legisinc/storage/logs/laravel.log | grep 'PDF OnlyOffice'"

echo ""
echo "========================"
echo "✅ Teste de logs concluído!"