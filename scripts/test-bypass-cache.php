<?php

require_once '/var/www/html/vendor/autoload.php';

$app = require_once '/var/www/html/bootstrap/app.php';
$app->boot();

echo "🔍 Testando bypass de cache...\\n";

$controller = app(App\Http\Controllers\DadosGeraisCamaraController::class);

echo "=== ANTES DO ACESSO ===\\n";
echo "Verificando dados salvos no banco:\\n";

$dados = \DB::table('parametros_valores as pv')
    ->join('parametros_campos as pc', 'pv.campo_id', '=', 'pc.id')
    ->join('parametros_submodulos as ps', 'pc.submodulo_id', '=', 'ps.id')
    ->join('parametros_modulos as pm', 'ps.modulo_id', '=', 'pm.id')
    ->where('pm.nome', 'Dados Gerais')
    ->where('ps.nome', 'Identificação')
    ->whereNull('pv.valido_ate')
    ->select('pc.nome as campo', 'pv.valor', 'pv.created_at')
    ->orderBy('pv.created_at', 'desc')
    ->get();

foreach ($dados as $dado) {
    echo "Campo: {$dado->campo} = '{$dado->valor}' (criado em: {$dado->created_at})\\n";
}

echo "\\n=== TESTANDO CONTROLLER COM CACHE BYPASS ===\\n";
try {
    $response = $controller->index();
    $configuracoes = $response->getData()['configuracoes'];

    echo "Configurações retornadas pelo controller:\\n";
    echo "Nome: '{$configuracoes['nome_camara']}'\\n";
    echo "Sigla: '{$configuracoes['sigla_camara']}'\\n";
    echo "CNPJ: '{$configuracoes['cnpj']}'\\n";
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\\n";
}

echo "\\n✅ Teste concluído!\\n";