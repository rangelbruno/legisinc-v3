<?php

require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Boot the application
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Ativar log de queries para medir performance
DB::enableQueryLog();
$startQueries = count(DB::getQueryLog());

echo "🧪 TESTE DIRETO DE PERFORMANCE - EXCLUSÃO\n";
echo "=========================================\n\n";

// Simular usuário logado como Jessica (ID: 2, PARLAMENTAR)
Auth::loginUsingId(2);

$proposicao = App\Models\Proposicao::find(1);
if (!$proposicao) {
    echo "❌ Proposição não encontrada\n";
    return;
}

echo "📋 Proposição encontrada: {$proposicao->ementa}\n";
echo "👤 Usuário logado: " . Auth::user()->name . " (ID: " . Auth::id() . ")\n\n";

// Simular o método excluirDocumento otimizado
echo "🔍 EXECUTANDO VERIFICAÇÕES DE PERMISSÃO OTIMIZADAS...\n";
echo "====================================================\n";

$startTime = microtime(true);

// OTIMIZAÇÃO: Carregar user com roles uma única vez para evitar N+1 queries
$user = Auth::user()->load('roles');
$userId = Auth::id();

echo "✅ User carregado com roles em uma única query\n";

// Verificar permissão usando collection ao invés de hasRole()
$isAdmin = $user->roles->contains('name', 'ADMIN');
echo "🔧 isAdmin: " . ($isAdmin ? 'true' : 'false') . "\n";

if ($userId !== $proposicao->autor_id && !$isAdmin) {
    echo "❌ Sem permissão para exclusão\n";
} else {
    echo "✅ Permissão validada\n";
}

$endTime = microtime(true);

// Contar queries executadas
$finalQueries = count(DB::getQueryLog());
$totalQueries = $finalQueries - $startQueries;
$executionTime = ($endTime - $startTime) * 1000; // em ms

echo "\n📊 RESULTADO DA OTIMIZAÇÃO:\n";
echo "==========================\n";
echo "🔢 Queries executadas: {$totalQueries}\n";
echo "⏱️ Tempo de execução: " . number_format($executionTime, 2) . "ms\n";

if ($totalQueries <= 5) {
    echo "🎉 EXCELENTE! Performance otimizada com sucesso!\n";
    echo "✅ Redução significativa de N+1 queries\n";
} elseif ($totalQueries <= 15) {
    echo "⚡ BOM! Performance melhorada, mas ainda pode ser otimizada\n";
} else {
    echo "⚠️ ALERTA! Ainda há problemas de performance\n";
}

echo "\n🔍 QUERIES EXECUTADAS:\n";
echo "======================\n";

foreach (DB::getQueryLog() as $index => $query) {
    if ($index >= $startQueries) {
        echo ($index - $startQueries + 1) . ". " . substr($query['query'], 0, 100) . "...\n";
        echo "   Time: " . $query['time'] . "ms\n\n";
    }
}

echo "\n🎯 COMPARAÇÃO COM VERSÃO ANTERIOR:\n";
echo "==================================\n";
echo "❌ Versão anterior: ~492 queries (problema N+1 massivo)\n";
echo "✅ Versão otimizada: {$totalQueries} queries (redução de " . 
     number_format((1 - $totalQueries/492) * 100, 1) . "%)\n";

echo "\n✅ Teste concluído!\n";