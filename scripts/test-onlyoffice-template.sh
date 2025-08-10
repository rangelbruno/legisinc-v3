#!/bin/bash

echo "🧪 Testando acesso ao OnlyOffice Template Editor"
echo "=============================================="

# Verificar se OnlyOffice está rodando
if ! docker ps | grep -q "legisinc-onlyoffice"; then
    echo "❌ OnlyOffice container não está rodando"
    exit 1
fi

echo "✅ OnlyOffice container está rodando"

# Verificar se as tabelas foram criadas
echo "🗄️ Verificando tabelas do OnlyOffice..."
TABLES=$(docker exec legisinc-postgres psql -U postgres -d legisinc -t -c "SELECT COUNT(*) FROM pg_tables WHERE tablename IN ('doc_changes', 'task_result');")
if [ "$TABLES" -eq 2 ]; then
    echo "✅ Tabelas OnlyOffice criadas: doc_changes, task_result"
else
    echo "❌ Tabelas OnlyOffice não encontradas"
    exit 1
fi

# Verificar conectividade do OnlyOffice
echo "🌐 Testando conectividade OnlyOffice..."
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8080 | grep -q "200"; then
    echo "✅ OnlyOffice responde corretamente"
else
    echo "❌ OnlyOffice não está respondendo"
    exit 1
fi

# Verificar template 12
echo "📄 Verificando template 12..."
TEMPLATE_EXISTS=$(docker exec legisinc-postgres psql -U postgres -d legisinc -t -c "SELECT COUNT(*) FROM tipo_proposicao_templates WHERE id = 12;")
if [ "$TEMPLATE_EXISTS" -eq 1 ]; then
    echo "✅ Template 12 existe no banco de dados"
    
    # Verificar arquivo do template
    ARQUIVO_PATH=$(docker exec legisinc-postgres psql -U postgres -d legisinc -t -c "SELECT arquivo_path FROM tipo_proposicao_templates WHERE id = 12;" | tr -d ' ')
    if docker exec legisinc-app test -f "/var/www/storage/app/$ARQUIVO_PATH"; then
        echo "✅ Arquivo do template existe: $ARQUIVO_PATH"
    else
        echo "⚠️ Arquivo do template não existe: $ARQUIVO_PATH"
        echo "Executando correção de templates..."
        docker exec legisinc-app php artisan templates:fix-files
    fi
else
    echo "❌ Template 12 não encontrado"
    exit 1
fi

echo ""
echo "✅ Verificação concluída!"
echo ""
echo "🎯 Para testar o editor:"
echo "1. Acesse: http://localhost:8001/admin"
echo "2. Login com usuário administrador"
echo "3. Vá para: Administração > Templates"
echo "4. Clique em 'Editar' no template 12"
echo ""
echo "💡 Se ainda houver erro de permissão, pode ser:"
echo "- Cache do navegador (limpe o cache)"
echo "- Sessão expirada (faça logout/login)"
echo "- Verifique os logs: docker logs legisinc-onlyoffice --tail=20"