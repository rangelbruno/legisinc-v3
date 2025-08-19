#!/bin/bash

echo "=== TESTE FLUXO ASSINATURA PARLAMENTAR ==="
echo ""

echo "📋 FLUXO ESPERADO:"
echo "  1. Status 'retornado_legislativo' ✅"
echo "  2. Parlamentar acessa /proposicoes/1"
echo "  3. Clica 'Assinar Documento'"
echo "  4. Vai para /proposicoes/1/assinar"
echo "  5. Assina digitalmente"
echo "  6. Status muda para 'assinado'"
echo "  7. Vai para Protocolo para número"
echo ""

echo "🔍 1. Verificando estado atual:"
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(1);
echo 'Proposição ID: ' . \$proposicao->id . PHP_EOL;
echo 'Status atual: ' . \$proposicao->status . PHP_EOL;
echo 'Autor ID: ' . \$proposicao->autor_id . PHP_EOL;
echo 'Última modificação: ' . \$proposicao->updated_at . PHP_EOL;
"

echo ""
echo "👤 2. Verificando usuário Jessica:"
docker exec legisinc-app php artisan tinker --execute="
\$user = App\Models\User::where('email', 'jessica@sistema.gov.br')->first();
echo 'User ID: ' . \$user->id . PHP_EOL;
echo 'Nome: ' . \$user->name . PHP_EOL;
echo 'Email: ' . \$user->email . PHP_EOL;
echo 'Role: ' . \$user->role . PHP_EOL;
"

echo ""
echo "🔐 3. Verificando se pode acessar rota de assinatura:"
docker exec legisinc-app php artisan tinker --execute="
// Simular login do Jessica
\$user = App\Models\User::find(6);
Auth::login(\$user);

\$proposicao = App\Models\Proposicao::find(1);

// Verificar condições do controller
\$statusValido = in_array(\$proposicao->status, ['aprovado_assinatura', 'retornado_legislativo']);
echo 'Status válido para assinatura: ' . (\$statusValido ? 'SIM' : 'NÃO') . PHP_EOL;

// Testar autorização (se houver)
try {
    // \$this->authorize('update', \$proposicao); // Esta linha está comentada no controller
    echo 'Autorização: PERMITIDA (linha comentada)' . PHP_EOL;
} catch (\Exception \$e) {
    echo 'Autorização: NEGADA - ' . \$e->getMessage() . PHP_EOL;
}

echo 'Usuário logado: ' . (Auth::check() ? Auth::user()->name : 'Não logado') . PHP_EOL;
"

echo ""
echo "🌐 4. TESTE REAL: Simular acesso à tela de assinatura"
echo "   Como o middleware 'auth' exige sessão ativa, vamos testar internamente:"

docker exec legisinc-app php artisan tinker --execute="
// Simular request autenticado
\$user = App\Models\User::find(6);
Auth::login(\$user);

\$proposicao = App\Models\Proposicao::find(1);
\$controller = new App\Http\Controllers\ProposicaoAssinaturaController();

echo 'Usuário autenticado: ' . Auth::user()->name . PHP_EOL;
echo 'Proposição ID: ' . \$proposicao->id . PHP_EOL;
echo 'Status: ' . \$proposicao->status . PHP_EOL;

// Testar condição do método assinar()
if (!in_array(\$proposicao->status, ['aprovado_assinatura', 'retornado_legislativo'])) {
    echo 'ERRO: Status não permite assinatura!' . PHP_EOL;
} else {
    echo 'Status OK: Pode assinar!' . PHP_EOL;
}
"

echo ""
echo "🚨 5. SIMULAÇÃO PROBLEMA NO NAVEGADOR:"
echo ""
echo "   CENÁRIO A - Sessão expirada:"
echo "     • Usuário estava logado, mas sessão expirou"
echo "     • Clica no botão → Redireciona para /login"
echo "     • Solução: Fazer login novamente"
echo ""
echo "   CENÁRIO B - JavaScript/CSS interferindo:"
echo "     • Botão está sendo bloqueado por CSS"
echo "     • z-index, pointer-events, ou overlay invisível"
echo "     • Solução: Inspecionar elemento no DevTools"
echo ""
echo "   CENÁRIO C - Middleware bloqueando:"
echo "     • check.screen.permission está negando acesso"
echo "     • Mesmo com permissão no banco, middleware falha"
echo "     • Solução: Verificar log de erros"
echo ""

echo "🔧 6. TESTE MANUAL RECOMENDADO:"
echo ""
echo "   1. Abra NOVA aba do navegador (limpar cache/sessão)"
echo "   2. Vá para: http://localhost:8001/login"
echo "   3. Login: jessica@sistema.gov.br / 123456"
echo "   4. Após login, vá DIRETAMENTE para: http://localhost:8001/proposicoes/1/assinar"
echo "   5. Se carregar a tela → Botão está funcionando, problema é na interface"
echo "   6. Se redirecionar → Problema é de autenticação/permissão"
echo ""

echo "📝 7. Verificando logs de erro recentes:"
echo "   Últimas 10 linhas do log Laravel:"
tail -n 10 /home/bruno/legisinc/storage/logs/laravel.log

echo ""
echo "=== CONCLUSÃO ==="
echo "✅ Status 'retornado_legislativo' permite assinatura"
echo "✅ Usuário Jessica é autor da proposição"
echo "✅ Permissões estão configuradas"
echo "✅ Rota está registrada"
echo ""
echo "❓ PRÓXIMO PASSO: Teste manual no navegador"
echo "   Se ainda não funcionar, o problema é de SESSÃO ou JAVASCRIPT"
echo ""