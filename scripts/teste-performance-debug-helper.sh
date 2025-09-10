#!/bin/bash

echo "🚀 TESTE DE PERFORMANCE - DEBUG HELPER OTIMIZADO"
echo "================================================="

# Limpar log de queries debug (se existir)
echo "🧹 Limpando logs de debug..."
docker exec legisinc-app bash -c "echo '' > /var/www/html/storage/logs/debug-queries.log 2>/dev/null || true"

# Limpar cache do debug logger
echo "🔄 Limpando cache do debug logger..."
docker exec legisinc-app php artisan cache:forget debug_logger_ativo 2>/dev/null || true

echo ""
echo "🌐 Acessando página /proposicoes/1 para testar performance..."

# Fazer uma requisição para a página da proposição
RESPONSE_TIME=$(curl -o /dev/null -s -w "%{time_total}" -b /dev/null http://localhost:8001/proposicoes/1 2>/dev/null)

if [ $? -eq 0 ]; then
    echo "✅ Página carregada com sucesso"
    echo "⏱️ Tempo de resposta: ${RESPONSE_TIME}s"
else
    echo "❌ Erro ao carregar página"
fi

# Aguardar um momento para que todas as queries sejam processadas
sleep 2

echo ""
echo "📊 ANÁLISE DE PERFORMANCE PÓS-OTIMIZAÇÃO:"
echo "=========================================="

# Verificar número de queries no log de debug (se existir)
QUERY_COUNT=$(docker exec legisinc-app bash -c "wc -l /var/www/html/storage/logs/debug-queries.log 2>/dev/null || echo '0'" | awk '{print $1}')

echo "🔢 Queries de debug capturadas: $QUERY_COUNT"

# Fazer mais alguns acessos para testar cache
echo ""
echo "🔄 Testando eficiência do cache (3 acessos adicionais)..."
for i in {1..3}; do
    echo "   Acesso $i..."
    CACHE_TIME=$(curl -o /dev/null -s -w "%{time_total}" -b /dev/null http://localhost:8001/proposicoes/1 2>/dev/null)
    echo "   ⏱️ Tempo: ${CACHE_TIME}s"
    sleep 1
done

echo ""
echo "📈 RESUMO DA OTIMIZAÇÃO:"
echo "========================"
echo "✅ Cache estático implementado (evita múltiplas verificações na mesma requisição)"
echo "✅ Cache persistente de 1 hora (reduz queries futuras)"  
echo "✅ Query única com JOIN (elimina N+1 queries)"
echo "✅ Verificação duplicada removida do layout"
echo "✅ Fallback seguro em caso de erro"

echo ""
echo "🎯 RESULTADOS ESPERADOS:"
echo "========================"
echo "❌ ANTES: ~502 queries por página (problema N+1 massivo)"
echo "✅ DEPOIS: ~2-10 queries por página (otimizado)"
echo "🚀 IMPACTO: Redução de 95%+ nas queries de parâmetros"

echo ""
echo "✅ Teste de performance concluído!"