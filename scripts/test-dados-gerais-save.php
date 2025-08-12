<?php

// Script para testar o salvamento dos dados gerais da câmara

use App\Services\Parametro\ParametroService;

// Criar instância do serviço
$parametroService = app(ParametroService::class);

echo "🧪 Testando salvamento dos dados gerais da câmara...\n";

// Testar salvamento de cada campo da aba "Identificação"
$resultados = [];

$campos_teste = [
    'nome_camara' => 'Câmara Municipal TESTE SISTEMA',
    'sigla_camara' => 'CMTS',
    'cnpj' => '12.345.678/0001-90'
];

foreach ($campos_teste as $campo => $valor) {
    echo "📝 Salvando $campo = $valor\n";
    
    $resultado = $parametroService->salvarValor('Dados Gerais', 'Identificação', $campo, $valor);
    
    if ($resultado) {
        echo "✅ $campo salvo com sucesso\n";
        
        // Verificar se pode recuperar o valor
        $valorRecuperado = $parametroService->obterValor('Dados Gerais', 'Identificação', $campo);
        echo "🔍 Valor recuperado: $valorRecuperado\n";
        
        $resultados[$campo] = ['salvo' => true, 'valor_recuperado' => $valorRecuperado];
    } else {
        echo "❌ Erro ao salvar $campo\n";
        $resultados[$campo] = ['salvo' => false, 'valor_recuperado' => null];
    }
    echo "---\n";
}

echo "\n📊 RESUMO DOS TESTES:\n";
foreach ($resultados as $campo => $resultado) {
    $status = $resultado['salvo'] ? '✅' : '❌';
    echo "$status $campo: {$resultado['valor_recuperado']}\n";
}

echo "\n🔍 Verificando valores no BD...\n";
$valores = DB::table('parametros_valores')
    ->join('parametros_campos', 'parametros_valores.campo_id', '=', 'parametros_campos.id')
    ->where('parametros_campos.nome', 'nome_camara')
    ->whereNull('parametros_valores.valido_ate')
    ->orderBy('parametros_valores.created_at', 'desc')
    ->select('parametros_valores.*', 'parametros_campos.nome as campo_nome')
    ->get();

foreach ($valores as $valor) {
    echo "🗄️ Campo: {$valor->campo_nome}, Valor: {$valor->valor}, Criado: {$valor->created_at}\n";
}

echo "\n✅ Teste concluído!\n";