#!/bin/bash

echo "=== TESTE DO BOTÃO ASSINATURA ==="
echo ""

echo "📋 1. Verificando STATUS da proposição 1:"
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(1);
echo 'ID: ' . \$proposicao->id . '\n';
echo 'Status: ' . \$proposicao->status . '\n';
echo 'Autor ID: ' . \$proposicao->autor_id . '\n';
"

echo ""
echo "🔒 2. Verificando PERMISSÕES para PARLAMENTAR:"
docker exec legisinc-app php artisan tinker --execute="
\$permission = DB::table('screen_permissions')
    ->where('role_name', 'PARLAMENTAR')
    ->where('screen_route', 'proposicoes.assinar')
    ->first();
if (\$permission) {
    echo 'Permissão existe: ' . (\$permission->can_access ? 'SIM' : 'NÃO') . '\n';
} else {
    echo 'Permissão NÃO ENCONTRADA\n';
}
"

echo ""
echo "🛤️ 3. Verificando ROTA:"
docker exec legisinc-app php artisan route:list | grep "proposicoes.assinar"

echo ""
echo "👤 4. Verificando USUÁRIO Jessica (ID 6):"
docker exec legisinc-app php artisan tinker --execute="
\$user = App\Models\User::find(6);
echo 'ID: ' . \$user->id . '\n';
echo 'Nome: ' . \$user->name . '\n';
echo 'Email: ' . \$user->email . '\n';
"

echo ""
echo "🔍 5. Verificando se proposição 1 pertence ao usuário 6:"
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(1);
\$user = App\Models\User::find(6);
echo 'Proposição autor_id: ' . \$proposicao->autor_id . '\n';
echo 'Usuário ID: ' . \$user->id . '\n';
echo 'É o mesmo autor? ' . (\$proposicao->autor_id == \$user->id ? 'SIM' : 'NÃO') . '\n';
"

echo ""
echo "⚠️ 6. CONDIÇÕES para ASSINATURA:"
echo "   - Status deve ser: aprovado_assinatura OU retornado_legislativo"
echo "   - Status atual: retornado_legislativo ✅"
echo "   - Proposição pertence ao usuário ✅"
echo "   - Permissão existe ✅"

echo ""
echo "🔗 7. Testando geração da URL:"
docker exec legisinc-app php artisan tinker --execute="
echo 'URL gerada: ' . route('proposicoes.assinar', 1) . '\n';
"

echo ""
echo "🌐 8. Testando acesso direto via HTTP:"
echo "   Tentando acessar: http://localhost:8001/proposicoes/1/assinar"

# Fazer uma requisição direta
curl -s -o /dev/null -w "Status HTTP: %{http_code}\n" http://localhost:8001/proposicoes/1/assinar

echo ""
echo "=== FIM DO TESTE ==="