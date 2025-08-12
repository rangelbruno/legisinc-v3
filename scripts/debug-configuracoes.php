<?php

require_once '/var/www/html/vendor/autoload.php';

$app = require_once '/var/www/html/bootstrap/app.php';
$app->boot();

use App\Services\Parametro\ParametroService;

echo "🔍 Debugando problema das configurações...\n";

$service = app(ParametroService::class);

echo "=== TESTE DIRETO DO SERVICE ===\n";
$nome = $service->obterValor('Dados Gerais', 'Identificação', 'nome_camara');
$sigla = $service->obterValor('Dados Gerais', 'Identificação', 'sigla_camara');
$cnpj = $service->obterValor('Dados Gerais', 'Identificação', 'cnpj');

echo "Nome: "; var_dump($nome);
echo "Sigla: "; var_dump($sigla);
echo "CNPJ: "; var_dump($cnpj);

echo "\n=== TESTE COM OPERADOR TERNÁRIO ===\n";
$nomeComTernario = $nome ?: 'Câmara Municipal';
$siglaComTernario = $sigla ?: 'CM';
$cnpjComTernario = $cnpj ?: '';

echo "Nome com ternário: "; var_dump($nomeComTernario);
echo "Sigla com ternário: "; var_dump($siglaComTernario);
echo "CNPJ com ternário: "; var_dump($cnpjComTernario);

echo "\n=== TESTE DO CONTROLLER ===\n";
$controller = app(App\Http\Controllers\DadosGeraisCamaraController::class);
$response = $controller->index();
$configuracoes = $response->getData()['configuracoes'];

echo "Configurações do controller:\n";
echo "Nome: "; var_dump($configuracoes['nome_camara']);
echo "Sigla: "; var_dump($configuracoes['sigla_camara']);
echo "CNPJ: "; var_dump($configuracoes['cnpj']);

echo "\n✅ Debug concluído!\n";