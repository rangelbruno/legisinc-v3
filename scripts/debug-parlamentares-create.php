<?php

// Script para debug do problema de usuários não aparecendo

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Parlamentar;
use Illuminate\Support\Facades\View;

echo "🔍 DEBUG: /parlamentares/create\n";
echo "==============================\n\n";

// 1. Testar busca de usuários
echo "1. Testando busca de usuários parlamentares sem parlamentar:\n";
$usuariosSemParlamentar = User::whereHas('roles', function ($q) {
    $q->whereIn('name', ['PARLAMENTAR', 'RELATOR']);
})
->whereDoesntHave('parlamentar')
->orderBy('name')
->get(['id', 'name', 'email', 'partido']);

echo "   Encontrados: " . $usuariosSemParlamentar->count() . " usuários\n";

foreach ($usuariosSemParlamentar as $usuario) {
    echo "   - ID: {$usuario->id}, Nome: {$usuario->name}, Email: {$usuario->email}\n";
}

// 2. Testar controller method
echo "\n2. Simulando método create() do ParlamentarController:\n";

try {
    $partidos = ['PSDB' => 'PSDB', 'PT' => 'PT', 'PMDB' => 'PMDB'];
    $cargos = ['Vereador' => 'Vereador', 'Relator' => 'Relator'];
    $statusOptions = ['ativo' => 'Ativo', 'inativo' => 'Inativo'];
    $escolaridadeOptions = [
        'Ensino Fundamental' => 'Ensino Fundamental',
        'Ensino Médio' => 'Ensino Médio',
        'Ensino Superior' => 'Ensino Superior'
    ];
    
    echo "   ✅ Dados básicos preparados\n";
    echo "   ✅ usuariosSemParlamentar: " . $usuariosSemParlamentar->count() . " itens\n";
    
    // 3. Simular a renderização da view
    echo "\n3. Verificando se view pode ser renderizada:\n";
    
    $viewData = [
        'title' => 'Novo Parlamentar',
        'partidos' => $partidos,
        'cargos' => $cargos,
        'statusOptions' => $statusOptions,
        'escolaridadeOptions' => $escolaridadeOptions,
        'usuariosSemParlamentar' => $usuariosSemParlamentar
    ];
    
    echo "   ✅ ViewData preparado\n";
    
    // 4. Testar componente form 
    $formData = [
        'action' => '/parlamentares',
        'method' => null,
        'parlamentar' => [],
        'partidos' => $partidos,
        'cargos' => $cargos,
        'escolaridadeOptions' => $escolaridadeOptions,
        'statusOptions' => null,
        'cancelUrl' => '/parlamentares',
        'submitText' => 'Salvar Parlamentar',
        'usuariosSemParlamentar' => $usuariosSemParlamentar
    ];
    
    echo "   ✅ FormData preparado com usuariosSemParlamentar\n";
    echo "   📝 usuariosSemParlamentar->count(): " . $usuariosSemParlamentar->count() . "\n";
    
    // 5. Verificar se existe problema na variável
    if (isset($formData['usuariosSemParlamentar'])) {
        echo "   ✅ Variável usuariosSemParlamentar EXISTE no formData\n";
        if ($formData['usuariosSemParlamentar']->count() > 0) {
            echo "   ✅ Variável usuariosSemParlamentar tem " . $formData['usuariosSemParlamentar']->count() . " itens\n";
            foreach ($formData['usuariosSemParlamentar'] as $usuario) {
                echo "      - {$usuario->name} ({$usuario->email})\n";
            }
        } else {
            echo "   ❌ Variável usuariosSemParlamentar está VAZIA\n";
        }
    } else {
        echo "   ❌ Variável usuariosSemParlamentar NÃO EXISTE no formData\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n✅ DEBUG CONCLUÍDO!\n";