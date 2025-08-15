#!/bin/bash

echo "🧪 === TESTE CORREÇÃO LEGISLATIVO ONLYOFFICE ==="
echo ""

echo "📋 1. Verificando status da proposição 1..."
docker exec legisinc-app php artisan tinker --execute="
\$p = App\Models\Proposicao::find(1);
if(\$p) {
    echo 'ID: ' . \$p->id . PHP_EOL;
    echo 'Tipo: ' . \$p->tipo . PHP_EOL;
    echo 'Status: ' . \$p->status . PHP_EOL;
    echo 'Autor ID: ' . \$p->autor_id . PHP_EOL;
    echo 'Template ID: ' . (\$p->template_id ?? 'null') . PHP_EOL;
    echo 'Arquivo Path: ' . (\$p->arquivo_path ?? 'null') . PHP_EOL;
} else {
    echo 'Proposição não encontrada' . PHP_EOL;
}
"

echo ""
echo "👤 2. Verificando usuário João (Legislativo)..."
docker exec legisinc-app php artisan tinker --execute="
\$user = App\Models\User::where('email', 'joao@sistema.gov.br')->first();
if(\$user) {
    echo 'ID: ' . \$user->id . PHP_EOL;
    echo 'Nome: ' . \$user->name . PHP_EOL;
    echo 'Email: ' . \$user->email . PHP_EOL;
    echo 'É Legislativo? ' . (\$user->isLegislativo() ? 'SIM' : 'NÃO') . PHP_EOL;
    echo 'Roles: ' . \$user->getRoleNames()->implode(', ') . PHP_EOL;
} else {
    echo 'Usuário não encontrado' . PHP_EOL;
}
"

echo ""
echo "🔗 3. Testando URL de acesso ao editor legislativo..."
echo "URL: http://localhost:8001/proposicoes/1/onlyoffice/editor"
echo ""

echo "📝 4. Verificando rotas de callback..."
docker exec legisinc-app php artisan route:list --name="api.onlyoffice" --columns=Method,URI,Name

echo ""
echo "🏥 5. Verificando saúde do OnlyOffice..."
curl -s http://localhost:8080/healthcheck | jq . || echo "OnlyOffice não responde ou JSON inválido"

echo ""
echo "✅ Teste concluído!"
echo ""
echo "Para testar manualmente:"
echo "1. Acesse: http://localhost:8001"
echo "2. Login: joao@sistema.gov.br / 123456"
echo "3. Vá para: Proposições > Legislativo"
echo "4. Edite a proposição 1 no OnlyOffice"
echo "5. Faça alterações e salve (Ctrl+S)"