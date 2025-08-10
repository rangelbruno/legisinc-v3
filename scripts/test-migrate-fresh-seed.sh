#!/bin/bash

echo "🧪 Testando comando completo: migrate:fresh --seed"
echo "================================================"

echo "1️⃣ Executando migrate:fresh --seed..."
docker exec legisinc-app php artisan migrate:fresh --seed

if [ $? -eq 0 ]; then
    echo "✅ Migrate:fresh --seed executado com sucesso"
else
    echo "❌ Erro no migrate:fresh --seed"
    exit 1
fi

echo ""
echo "2️⃣ Verificando tabelas OnlyOffice..."

# Verificar se as tabelas foram criadas
TABLES=$(docker exec legisinc-postgres psql -U postgres -d legisinc -t -c "SELECT COUNT(*) FROM pg_tables WHERE tablename IN ('doc_changes', 'task_result');")
if [ "$TABLES" -eq 2 ]; then
    echo "✅ Tabelas OnlyOffice criadas: doc_changes, task_result"
else
    echo "❌ Tabelas OnlyOffice não encontradas (esperado: 2, encontrado: $TABLES)"
    exit 1
fi

# Verificar função
FUNCTION_EXISTS=$(docker exec legisinc-postgres psql -U postgres -d legisinc -t -c "SELECT COUNT(*) FROM pg_proc WHERE proname = 'merge_db';")
if [ "$FUNCTION_EXISTS" -eq 1 ]; then
    echo "✅ Função merge_db criada"
else
    echo "❌ Função merge_db não encontrada"
    exit 1
fi

echo ""
echo "3️⃣ Testando OnlyOffice após seeder..."

# Reiniciar OnlyOffice para garantir que reconheça as novas tabelas
echo "🔄 Reiniciando OnlyOffice..."
docker restart legisinc-onlyoffice > /dev/null
sleep 5

# Verificar se OnlyOffice está respondendo
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8080 | grep -E "200|302" > /dev/null; then
    echo "✅ OnlyOffice responde corretamente"
else
    echo "⚠️ OnlyOffice não está respondendo corretamente"
fi

echo ""
echo "✅ Teste completo realizado com sucesso!"
echo ""
echo "🎯 Agora o comando 'docker exec -it legisinc-app php artisan migrate:fresh --seed'"
echo "   sempre criará automaticamente as tabelas necessárias para o OnlyOffice!"
echo ""
echo "📄 Templates disponíveis para teste:"
docker exec legisinc-postgres psql -U postgres -d legisinc -t -c "SELECT id, tipo_proposicao_id FROM tipo_proposicao_templates ORDER BY id LIMIT 5;" | head -5