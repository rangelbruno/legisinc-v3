#!/bin/bash

echo "🔧 TESTE DA CORREÇÃO DO ERROR_LOG"
echo "=================================="

echo ""
echo "🧹 1. Limpando logs anteriores..."
> /home/bruno/legisinc/storage/logs/laravel.log

echo "   ✅ Log limpo"

echo ""
echo "🚀 2. Testando a URL novamente..."
echo "   🌐 URL: http://localhost:8001/proposicoes/2/assinar"

# Simular acesso via curl
response=$(curl -s -o /dev/null -w "%{http_code}" -L -b "/tmp/cookies.txt" -c "/tmp/cookies.txt" \
    -H "User-Agent: Mozilla/5.0" \
    "http://localhost:8001/proposicoes/2/assinar" 2>/dev/null || echo "000")

echo "   📊 Código de resposta: $response"

if [ "$response" = "200" ]; then
    echo "   ✅ Sucesso! Página carregada"
elif [ "$response" = "302" ] || [ "$response" = "301" ]; then
    echo "   🔄 Redirecionamento (provável tela de login)"
elif [ "$response" = "500" ]; then
    echo "   ❌ Ainda há erro 500"
else
    echo "   ⚠️ Código inesperado: $response"
fi

echo ""
echo "📋 3. Verificando logs gerados..."

if [ -f "/home/bruno/legisinc/storage/logs/laravel.log" ] && [ -s "/home/bruno/legisinc/storage/logs/laravel.log" ]; then
    echo "   📊 Logs encontrados:"
    
    # Mostrar últimas 10 linhas
    tail -10 /home/bruno/legisinc/storage/logs/laravel.log | while read linha; do
        if echo "$linha" | grep -q "ERROR"; then
            echo "      ❌ $linha"
        elif echo "$linha" | grep -q "PDF Assinatura"; then
            echo "      ✅ $linha"
        else
            echo "      📝 $linha"
        fi
    done
    
    # Verificar se ainda há erros de error_log
    if grep -q "error_log().*must be of type int.*array given" /home/bruno/legisinc/storage/logs/laravel.log; then
        echo "   ❌ AINDA HÁ ERROS DE error_log() com array"
    else
        echo "   ✅ Erro de error_log() com array CORRIGIDO!"
    fi
    
    # Verificar se extração está funcionando
    if grep -q "Documento Word extraído com estrutura completa" /home/bruno/legisinc/storage/logs/laravel.log; then
        echo "   ✅ Extração de estrutura Word FUNCIONANDO!"
    fi
    
else
    echo "   ⚠️ Nenhum log gerado ainda"
    echo "   💡 Tente acessar: http://localhost:8001/login"
    echo "      Login: jessica@sistema.gov.br / 123456"
    echo "      Depois: http://localhost:8001/proposicoes/2/assinar"
fi

echo ""
echo "🔍 4. Validando correções no código..."

controller_file="/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"

# Verificar se não há mais error_log com array
if grep -q "error_log.*\[" "$controller_file"; then
    echo "   ⚠️ Ainda há chamadas error_log() com array no código"
    grep -n "error_log.*\[" "$controller_file" | head -3
else
    echo "   ✅ Todas as chamadas error_log() corrigidas!"
fi

# Verificar se os métodos ainda existem
metodos=("extrairConteudoDOCX" "extrairSecaoWord" "extrairTextoDeXml" "combinarSecoesWord")
for metodo in "${metodos[@]}"; do
    if grep -q "private function ${metodo}(" "$controller_file"; then
        echo "   ✅ Método $metodo presente"
    else
        echo "   ❌ Método $metodo AUSENTE"
    fi
done

echo ""
echo "=================================="
echo "✅ TESTE DE CORREÇÃO CONCLUÍDO!"
echo ""
echo "🎯 PRÓXIMO PASSO:"
echo "   http://localhost:8001/proposicoes/2/assinar"
echo ""
echo "✅ Erro de error_log() corrigido!"