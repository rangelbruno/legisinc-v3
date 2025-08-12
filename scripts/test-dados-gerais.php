<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\DadosGeraisCamaraController;
use App\Services\Parametro\ParametroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

Auth::loginUsingId(1);

echo "=== TESTE DE SALVAMENTO - DADOS GERAIS DA CÂMARA ===\n\n";

$parametroService = app(ParametroService::class);
$controller = new DadosGeraisCamaraController($parametroService);

echo "1. Testando salvamento da aba IDENTIFICACAO...\n";

$request = new Request();
$request->merge([
    'save_tab' => 'identificacao',
    'nome_camara' => 'Câmara Municipal de Teste',
    'sigla_camara' => 'CMT',
    'cnpj' => '12.345.678/0001-90'
]);

try {
    $response = $controller->store($request);
    $data = $response->getData(true);
    
    if ($data['success']) {
        echo "  ✅ Sucesso: " . $data['message'] . "\n";
    } else {
        echo "  ❌ Erro: " . $data['message'] . "\n";
    }
} catch (Exception $e) {
    echo "  ❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n2. Verificando valores salvos no banco...\n";

try {
    $valores = DB::table('parametros_valores as pv')
        ->join('parametros_campos as pc', 'pv.campo_id', '=', 'pc.id')
        ->join('parametros_submodulos as ps', 'pc.submodulo_id', '=', 'ps.id')
        ->join('parametros_modulos as pm', 'ps.modulo_id', '=', 'pm.id')
        ->where('pm.nome', 'Dados Gerais')
        ->whereNull('pv.valido_ate')
        ->select('pc.nome as campo', 'pv.valor', 'pv.created_at')
        ->orderBy('pv.created_at', 'desc')
        ->get();

    if ($valores->count() > 0) {
        echo "  📋 Valores encontrados:\n";
        foreach ($valores as $valor) {
            echo "    - {$valor->campo}: '{$valor->valor}' (criado em: {$valor->created_at})\n";
        }
    } else {
        echo "  ⚠️  Nenhum valor encontrado no banco\n";
    }
} catch (Exception $e) {
    echo "  ❌ Erro ao consultar banco: " . $e->getMessage() . "\n";
}

echo "\n=== TESTE CONCLUÍDO ===\n";