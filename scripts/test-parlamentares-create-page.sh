#!/bin/bash

echo "🧪 TESTE DA PÁGINA /parlamentares/create"
echo "======================================="

echo ""
echo "1. Verificando usuários disponíveis no banco..."
docker exec legisinc-postgres psql -U postgres -d legisinc -c "
SELECT u.id, u.name, u.email, r.name as role_name
FROM users u 
JOIN model_has_roles mhr ON u.id = mhr.model_id 
JOIN roles r ON mhr.role_id = r.id
LEFT JOIN parlamentars p ON u.id = p.user_id
WHERE r.name IN ('PARLAMENTAR', 'RELATOR') 
AND p.user_id IS NULL
ORDER BY u.name;"

echo ""
echo "2. Testando query do controller diretamente..."
docker exec legisinc-app php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo \"Executando query do controller...\n\";
\$usuarios = User::whereHas('roles', function (\$q) {
    \$q->whereIn('name', ['PARLAMENTAR', 'RELATOR']);
})->whereDoesntHave('parlamentar')->orderBy('name')->get(['id', 'name', 'email', 'partido']);

echo \"Usuários encontrados: \" . \$usuarios->count() . \"\n\";
foreach (\$usuarios as \$usuario) {
    echo \"- ID: {\$usuario->id}, Nome: {\$usuario->name}, Email: {\$usuario->email}, Partido: {\$usuario->partido}\n\";
}
"

echo ""
echo "3. Fazendo requisição HTTP para /parlamentares/create..."
# Fazer request com session (simular login admin)
curl -s -c /tmp/cookies.txt -b /tmp/cookies.txt \
  -d "email=bruno@sistema.gov.br&password=123456" \
  -X POST "http://localhost:8001/login" > /dev/null

# Pegar a página de criação de parlamentares  
response=$(curl -s -b /tmp/cookies.txt "http://localhost:8001/parlamentares/create")

echo ""
echo "4. Verificando se usuários aparecem no HTML..."
if echo "$response" | grep -q "Carlos Deputado Silva"; then
    echo "✅ Carlos Deputado Silva encontrado no HTML"
else
    echo "❌ Carlos Deputado Silva NÃO encontrado no HTML"
fi

if echo "$response" | grep -q "Ana Vereadora Costa"; then
    echo "✅ Ana Vereadora Costa encontrada no HTML"
else
    echo "❌ Ana Vereadora Costa NÃO encontrada no HTML"
fi

if echo "$response" | grep -q "Roberto Relator Souza"; then
    echo "✅ Roberto Relator Souza encontrado no HTML"
else
    echo "❌ Roberto Relator Souza NÃO encontrado no HTML"
fi

echo ""
echo "5. Analisando estrutura do select..."
if echo "$response" | grep -q 'select name="user_id"'; then
    echo "✅ Select user_id encontrado"
    # Extrair o conteúdo do select
    echo "$response" | grep -A 20 'select name="user_id"' | grep -B 10 '</select>' | head -15
else
    echo "❌ Select user_id NÃO encontrado"
fi

# Limpeza
rm -f /tmp/cookies.txt

echo ""
echo "✅ TESTE CONCLUÍDO!"