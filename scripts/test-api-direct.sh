#!/bin/bash

echo "🧪 === TESTE DIRETO: API DE PROPOSIÇÕES ==="

echo "🔍 1. Testando API via container (sem autenticação):"
echo "Resultado esperado: Erro 401 Unauthenticated"

docker exec legisinc-app curl -s -X GET "http://localhost/api/proposicoes/1" \
     -H "Accept: application/json" \
     -H "Content-Type: application/json"

echo -e "\n\n🔍 2. Testando modelo diretamente:"
echo "Verificando se a proposição 1 existe no banco:"

docker exec legisinc-app php artisan tinker --execute="
try {
    \$proposicao = App\Models\Proposicao::with('autor')->find(1);
    if (\$proposicao) {
        echo 'Proposição encontrada!' . PHP_EOL;
        echo 'ID: ' . \$proposicao->id . PHP_EOL;
        echo 'Ementa: ' . \$proposicao->ementa . PHP_EOL;
        echo 'Autor: ' . \$proposicao->autor->name . PHP_EOL;
    } else {
        echo 'Proposição NÃO encontrada!' . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'ERRO: ' . \$e->getMessage() . PHP_EOL;
}
"

echo -e "\n🔍 3. Testando controller API diretamente:"
echo "Simulando chamada do controller:"

docker exec legisinc-app php artisan tinker --execute="
try {
    \$user = App\Models\User::find(6); // Jessica
    Auth::login(\$user);
    \$controller = new App\Http\Controllers\Api\ProposicaoApiController();
    \$response = \$controller->show(1);
    echo 'Response status: ' . \$response->getStatusCode() . PHP_EOL;
    echo 'Response content: ' . substr(\$response->getContent(), 0, 200) . '...' . PHP_EOL;
} catch (Exception \$e) {
    echo 'ERRO no controller: ' . \$e->getMessage() . PHP_EOL;
    echo 'Stack trace: ' . \$e->getTraceAsString() . PHP_EOL;
}
"

echo -e "\n✅ Se todos os testes acima funcionaram, o problema é na autenticação via AJAX"
echo "❌ Se houver erro no teste 3, o problema é no controller da API"