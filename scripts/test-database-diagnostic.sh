#!/bin/bash

echo "🔍 Testando tela de diagnóstico do banco de dados..."

# Verificar se a aplicação está rodando
echo "1. Verificando se a aplicação está ativa..."
if curl -s http://localhost:8001 > /dev/null; then
    echo "✅ Aplicação respondendo em http://localhost:8001"
else
    echo "❌ Aplicação não está respondendo"
    exit 1
fi

# Testar o controller diretamente
echo "2. Testando controller SystemDiagnosticController..."
docker exec legisinc-app php artisan tinker --execute="
use App\Http\Controllers\Admin\SystemDiagnosticController;
\$controller = new SystemDiagnosticController();
try {
    \$result = \$controller->database();
    echo 'Controller funcionando corretamente';
} catch (Exception \$e) {
    echo 'Erro no controller: ' . \$e->getMessage();
    exit(1);
}
"

# Testar a obtenção de tabelas
echo "3. Testando obtenção de tabelas do banco..."
docker exec legisinc-app php artisan tinker --execute="
use Illuminate\Support\Facades\DB;
try {
    \$tables = DB::select(\"SELECT tablename FROM pg_tables WHERE schemaname = 'public'\");
    echo 'Encontradas ' . count(\$tables) . ' tabelas no banco';
} catch (Exception \$e) {
    echo 'Erro ao acessar tabelas: ' . \$e->getMessage();
}
"

# Testar a obtenção de relacionamentos
echo "4. Testando obtenção de relacionamentos..."
docker exec legisinc-app php artisan tinker --execute="
use Illuminate\Support\Facades\DB;
try {
    \$relationships = DB::select(\"
        SELECT 
            tc.constraint_name,
            tc.table_name as from_table,
            kcu.column_name as from_column,
            ccu.table_name as to_table,
            ccu.column_name as to_column
        FROM information_schema.table_constraints AS tc
        JOIN information_schema.key_column_usage AS kcu
            ON tc.constraint_name = kcu.constraint_name
            AND tc.table_schema = kcu.table_schema
        JOIN information_schema.constraint_column_usage AS ccu
            ON ccu.constraint_name = tc.constraint_name
            AND ccu.table_schema = tc.table_schema
        WHERE tc.constraint_type = 'FOREIGN KEY'
            AND tc.table_schema = 'public'
        LIMIT 5
    \");
    echo 'Encontrados ' . count(\$relationships) . ' relacionamentos';
} catch (Exception \$e) {
    echo 'Erro ao obter relacionamentos: ' . \$e->getMessage();
}
"

echo ""
echo "🎯 Resumo dos testes:"
echo "- Rota disponível: /admin/system-diagnostic/database"
echo "- Controller: SystemDiagnosticController@database"
echo "- View: admin.system-diagnostic.database"
echo "- Requer autenticação: Sim"
echo ""
echo "Para testar manualmente:"
echo "1. Acesse: http://localhost:8001/login"
echo "2. Login: admin@sistema.gov.br"
echo "3. Senha: 123456"
echo "4. Navegue para: http://localhost:8001/admin/system-diagnostic/database"
echo ""
echo "✨ Funcionalidades do diagrama:"
echo "- Canvas interativo com D3.js"
echo "- Visualização de tabelas e relacionamentos"
echo "- Drag & drop das tabelas"
echo "- Zoom e pan"
echo "- Tooltips informativos"
echo "- Click para abrir detalhes da tabela"
echo "- Toggle para ver lista tabular"