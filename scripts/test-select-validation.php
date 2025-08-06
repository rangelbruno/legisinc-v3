#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Parametro\ParametroCampo;

echo "=== TESTE DE VALIDAÇÃO DE CAMPOS SELECT ===\n\n";

try {
    // Buscar um campo select
    $campoSelect = ParametroCampo::where('id', 29)->first(); // cabecalho_posicao
    
    if ($campoSelect) {
        echo "✅ Campo select encontrado: {$campoSelect->label}\n";
        echo "📋 Opções: " . json_encode($campoSelect->opcoes_formatada) . "\n\n";
        
        // Testar valores válidos
        $valoresValidos = array_keys($campoSelect->opcoes_formatada);
        foreach ($valoresValidos as $valor) {
            $erros = $campoSelect->validarValor($valor);
            echo ($erros ? "❌" : "✅") . " Valor '$valor': " . (empty($erros) ? 'OK' : implode(', ', $erros)) . "\n";
        }
        
        // Testar valor inválido
        echo "\n🧪 Testando valor inválido:\n";
        $erros = $campoSelect->validarValor('valor_inexistente');
        echo ($erros ? "✅" : "❌") . " Valor inválido rejeitado: " . (empty($erros) ? 'NÃO REJEITADO!' : implode(', ', $erros)) . "\n";
        
        // Salvar um valor válido
        echo "\n💾 Salvando valor válido...\n";
        $valor = $campoSelect->setValor($valoresValidos[0]);
        echo "✅ Valor salvo: {$valor->valor}\n";
        
        // Verificar
        $campoSelect->refresh();
        echo "✅ Valor atual: {$campoSelect->valor_atual}\n";
    }

    echo "\n=== TESTE CONCLUÍDO COM SUCESSO ===\n";

} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}