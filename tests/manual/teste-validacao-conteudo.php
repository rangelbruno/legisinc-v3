<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Simular ambiente Laravel
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 TESTE DE VALIDAÇÃO DE CONTEÚDO ULTRA ROBUSTA\n";
echo "==============================================\n";

$service = app(\App\Services\OnlyOffice\OnlyOfficeService::class);
$reflection = new ReflectionClass($service);

try {
    $limparMethod = $reflection->getMethod('limparConteudoExtraido');
    $limparMethod->setAccessible(true);
    
    $validarMethod = $reflection->getMethod('isConteudoValido');
    $validarMethod->setAccessible(true);
    
    // Testar diferentes tipos de conteúdo
    $testes = [
        [
            'nome' => 'Conteúdo RTF Corrompido',
            'conteudo' => 'ulc1 * * * * * ; * * * * * * * ; * * * * * * * * * * * * * * * ; * * * * * * * ;',
            'esperado_valido' => false
        ],
        [
            'nome' => 'Conteúdo Misto (Corrompido + Texto)',
            'conteudo' => 'ulc1 * * * * * ; Esta é uma proposição que visa melhorar os serviços públicos da nossa cidade.',
            'esperado_valido' => true
        ],
        [
            'nome' => 'Conteúdo Limpo',
            'conteudo' => 'Esta é uma proposição legislativa que tem como objetivo estabelecer novas diretrizes para o desenvolvimento urbano sustentável do município.',
            'esperado_valido' => true
        ],
        [
            'nome' => 'Conteúdo Muito Curto',
            'conteudo' => 'Texto pequeno.',
            'esperado_valido' => false
        ]
    ];
    
    foreach ($testes as $i => $teste) {
        echo "\n📋 TESTE " . ($i+1) . ": {$teste['nome']}\n";
        echo "==========================================\n";
        
        // Primeiro limpar
        $conteudoLimpo = $limparMethod->invoke($service, $teste['conteudo']);
        echo "🧹 Após limpeza: " . substr($conteudoLimpo, 0, 80) . "\n";
        
        // Depois validar
        $ehValido = $validarMethod->invoke($service, $conteudoLimpo);
        
        $status = $ehValido ? "✅ VÁLIDO" : "❌ INVÁLIDO";
        $esperado = $teste['esperado_valido'] ? "VÁLIDO" : "INVÁLIDO";
        
        echo "🎯 Status: {$status} (Esperado: {$esperado})\n";
        
        if (($ehValido && $teste['esperado_valido']) || (!$ehValido && !$teste['esperado_valido'])) {
            echo "✅ TESTE PASSOU!\n";
        } else {
            echo "❌ TESTE FALHOU!\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao testar: " . $e->getMessage() . "\n";
}

echo "\n🎉 TESTES CONCLUÍDOS!\n";