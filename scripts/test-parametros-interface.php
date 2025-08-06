#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Parametro\ParametroModulo;

echo "=== TESTE DE INTERFACE DE PARÂMETROS ===\n\n";

try {
    // Tentar carregar da mesma forma que o controller
    $modulo = ParametroModulo::where('nome', 'Templates')
        ->with(['submodulos.campos.valores'])
        ->first();

    if (!$modulo) {
        echo "❌ Módulo 'Templates' não encontrado!\n";
        exit(1);
    }

    echo "✅ Módulo encontrado: {$modulo->nome}\n";
    echo "📁 Submódulos: {$modulo->submodulos->count()}\n\n";

    foreach ($modulo->submodulos as $submodulo) {
        echo "  📂 {$submodulo->nome} ({$submodulo->campos->count()} campos)\n";
        
        foreach ($submodulo->campos as $campo) {
            echo "    🔧 {$campo->nome} ({$campo->tipo_campo})";
            
            // Testar acesso ao valor_atual
            try {
                $valor = $campo->valor_atual;
                echo " = " . (is_string($valor) ? substr($valor, 0, 30) : json_encode($valor));
            } catch (Exception $e) {
                echo " ❌ ERRO: " . $e->getMessage();
            }
            echo "\n";
        }
        echo "\n";
    }

    echo "=== TESTE CONCLUÍDO COM SUCESSO ===\n";

} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}