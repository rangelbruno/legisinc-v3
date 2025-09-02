<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Simular ambiente Laravel
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 DEBUG RTF REAL - TESTE ESPECÍFICO\n";
echo "===================================\n";

$service = app(\App\Services\OnlyOffice\OnlyOfficeService::class);
$reflection = new ReflectionClass($service);

try {
    $limparMethod = $reflection->getMethod('limparConteudoExtraido');
    $limparMethod->setAccessible(true);
    
    $validarMethod = $reflection->getMethod('isConteudoValido');
    $validarMethod->setAccessible(true);
    
    // Simular conteúdo similar ao que aparece nos logs
    $conteudoRTFReal = 'ulc1 * * * * * ; * * * * * * * ; * * * * * * * * * * * * * * * ; * * * * * * * ; * * * * * * * * * ; * * * * * * * * * * * * * * ; * * * * * * * * * ; * * * * * * * * * * * * * * ; * * * * * * * * * ; Esta é uma proposição que visa estabelecer novas diretrizes para melhorar os serviços públicos oferecidos pela administração municipal, garantindo maior eficiência e qualidade no atendimento aos cidadãos. O objetivo principal é modernizar os processos administrativos e implementar tecnologias que facilitem o acesso da população aos serviços essenciais.';
    
    echo "📄 RTF REAL SIMULADO:\n";
    echo "Tamanho: " . strlen($conteudoRTFReal) . " caracteres\n";
    echo "Preview: " . substr($conteudoRTFReal, 0, 120) . "...\n\n";
    
    echo "🧹 INICIANDO LIMPEZA...\n";
    echo "======================\n";
    
    $conteudoLimpo = $limparMethod->invoke($service, $conteudoRTFReal);
    
    echo "📤 APÓS LIMPEZA:\n";
    echo "Tamanho: " . strlen($conteudoLimpo) . " caracteres\n";
    echo "Conteúdo: " . ($conteudoLimpo ?: '(VAZIO)') . "\n\n";
    
    if (!empty($conteudoLimpo)) {
        echo "🔍 VALIDANDO CONTEÚDO...\n";
        echo "=======================\n";
        
        $ehValido = $validarMethod->invoke($service, $conteudoLimpo);
        $status = $ehValido ? "✅ VÁLIDO" : "❌ INVÁLIDO";
        
        echo "🎯 Resultado da validação: {$status}\n";
    } else {
        echo "❌ PROBLEMA: Limpeza removeu TODO o conteúdo!\n";
        echo "🔧 Análise necessária nos padrões de limpeza.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao testar: " . $e->getMessage() . "\n";
}

echo "\n🎯 CONCLUSÃO\n";
echo "===========\n";
echo "Este teste simula o conteúdo real que está vindo do OnlyOffice.\n";
echo "Se o resultado for VAZIO, significa que a limpeza está muito agressiva.\n";