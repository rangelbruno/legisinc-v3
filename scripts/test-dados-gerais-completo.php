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

echo "=== TESTE COMPLETO - DADOS GERAIS DA CÂMARA ===\n\n";

$parametroService = app(ParametroService::class);
$controller = new DadosGeraisCamaraController($parametroService);

// Teste 1: Aba Endereco
echo "1. Testando aba ENDERECO...\n";
$request = new Request();
$request->merge([
    'save_tab' => 'endereco',
    'endereco' => 'Rua das Flores',
    'numero' => '123',
    'complemento' => 'Sala 201',
    'bairro' => 'Centro',
    'cidade' => 'São Paulo',
    'estado' => 'SP',
    'cep' => '01000-000'
]);

try {
    $response = $controller->store($request);
    $data = $response->getData(true);
    echo ($data['success'] ? "  ✅ " : "  ❌ ") . $data['message'] . "\n";
} catch (Exception $e) {
    echo "  ❌ Exception: " . $e->getMessage() . "\n";
}

// Teste 2: Aba Contatos
echo "\n2. Testando aba CONTATOS...\n";
$request = new Request();
$request->merge([
    'save_tab' => 'contatos',
    'telefone' => '(11) 1234-5678',
    'telefone_secundario' => '(11) 8765-4321',
    'email_institucional' => 'contato@camara.test.br',
    'email_contato' => 'protocolo@camara.test.br',
    'website' => 'www.camara.test.br'
]);

try {
    $response = $controller->store($request);
    $data = $response->getData(true);
    echo ($data['success'] ? "  ✅ " : "  ❌ ") . $data['message'] . "\n";
} catch (Exception $e) {
    echo "  ❌ Exception: " . $e->getMessage() . "\n";
}

// Teste 3: Aba Funcionamento
echo "\n3. Testando aba FUNCIONAMENTO...\n";
$request = new Request();
$request->merge([
    'save_tab' => 'funcionamento',
    'horario_funcionamento' => 'Segunda a Sexta: 8h às 18h',
    'horario_atendimento' => 'Segunda a Sexta: 9h às 17h'
]);

try {
    $response = $controller->store($request);
    $data = $response->getData(true);
    echo ($data['success'] ? "  ✅ " : "  ❌ ") . $data['message'] . "\n";
} catch (Exception $e) {
    echo "  ❌ Exception: " . $e->getMessage() . "\n";
}

// Teste 4: Aba Gestao
echo "\n4. Testando aba GESTAO...\n";
$request = new Request();
$request->merge([
    'save_tab' => 'gestao',
    'presidente_nome' => 'João Silva Santos',
    'presidente_partido' => 'PSDB',
    'legislatura_atual' => '2021-2024',
    'numero_vereadores' => '15'
]);

try {
    $response = $controller->store($request);
    $data = $response->getData(true);
    echo ($data['success'] ? "  ✅ " : "  ❌ ") . $data['message'] . "\n";
} catch (Exception $e) {
    echo "  ❌ Exception: " . $e->getMessage() . "\n";
}

// Verificar valores salvos
echo "\n5. Verificando TODOS os valores salvos...\n";
try {
    $valores = DB::table('parametros_valores as pv')
        ->join('parametros_campos as pc', 'pv.campo_id', '=', 'pc.id')
        ->join('parametros_submodulos as ps', 'pc.submodulo_id', '=', 'ps.id')
        ->join('parametros_modulos as pm', 'ps.modulo_id', '=', 'pm.id')
        ->where('pm.nome', 'Dados Gerais')
        ->whereNull('pv.valido_ate')
        ->select('ps.nome as submodulo', 'pc.nome as campo', 'pv.valor', 'pv.created_at')
        ->orderBy('ps.ordem')
        ->orderBy('pc.ordem')
        ->get();

    $agrupados = $valores->groupBy('submodulo');
    
    foreach ($agrupados as $submodulo => $campos) {
        echo "  📂 {$submodulo}:\n";
        foreach ($campos as $campo) {
            echo "    - {$campo->campo}: '{$campo->valor}'\n";
        }
    }
    
    echo "\n  📊 Total de campos salvos: " . $valores->count() . "\n";
} catch (Exception $e) {
    echo "  ❌ Erro ao consultar banco: " . $e->getMessage() . "\n";
}

// Testar carregamento
echo "\n6. Testando carregamento das configurações...\n";
try {
    $configuracoes = $controller->index()->getData()['configuracoes'];
    echo "  📄 Algumas configurações carregadas:\n";
    echo "    - Nome: '" . $configuracoes['nome_camara'] . "'\n";
    echo "    - Cidade: '" . $configuracoes['cidade'] . "'\n";
    echo "    - Telefone: '" . $configuracoes['telefone'] . "'\n";
    echo "    - Presidente: '" . $configuracoes['presidente_nome'] . "'\n";
} catch (Exception $e) {
    echo "  ❌ Erro ao carregar configurações: " . $e->getMessage() . "\n";
}

echo "\n=== TESTE COMPLETO CONCLUÍDO ===\n";