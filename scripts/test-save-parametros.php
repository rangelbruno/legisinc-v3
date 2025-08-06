#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Parametro\ParametroCampo;

echo "=== TESTE DE SALVAMENTO DE PARÂMETROS ===\n\n";

try {
    // Buscar um campo de texto para testar
    $campoTexto = ParametroCampo::whereHas('submodulo', function($query) {
        $query->whereHas('modulo', function($query2) {
            $query2->where('nome', 'Templates');
        });
    })->where('tipo_campo', 'text')->first();

    if ($campoTexto) {
        echo "✅ Campo encontrado: {$campoTexto->label} (ID: {$campoTexto->id})\n";
        
        // Testar validação
        $erros = $campoTexto->validarValor("Teste de valor");
        echo "✅ Validação passou: " . (empty($erros) ? 'SIM' : 'NÃO - ' . implode(', ', $erros)) . "\n";
        
        // Testar salvamento
        $valor = $campoTexto->setValor("Novo valor de teste - " . date('H:i:s'));
        echo "✅ Valor salvo: ID {$valor->id}\n";
        
        // Verificar se foi salvo
        $campoTexto->refresh();
        echo "✅ Valor atual: {$campoTexto->valor_atual}\n";
    }

    // Testar checkbox
    $campoCheckbox = ParametroCampo::whereHas('submodulo', function($query) {
        $query->whereHas('modulo', function($query2) {
            $query2->where('nome', 'Templates');
        });
    })->where('tipo_campo', 'checkbox')->first();

    if ($campoCheckbox) {
        echo "\n✅ Checkbox encontrado: {$campoCheckbox->label} (ID: {$campoCheckbox->id})\n";
        
        // Testar valores true/false
        foreach (['1', '0'] as $testValue) {
            $erros = $campoCheckbox->validarValor($testValue);
            echo "✅ Validação checkbox ($testValue): " . (empty($erros) ? 'OK' : 'ERRO - ' . implode(', ', $erros)) . "\n";
        }
        
        // Salvar valor
        $valor = $campoCheckbox->setValor('1');
        echo "✅ Checkbox salvo como marcado\n";
    }

    echo "\n=== TESTE CONCLUÍDO COM SUCESSO ===\n";

} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}