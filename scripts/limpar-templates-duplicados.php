<?php

// Script para limpar módulos Templates duplicados
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Parametro\ParametroModulo;

echo "🔍 Verificando módulos Templates duplicados...\n";

$templatesModulos = ParametroModulo::where('nome', 'Templates')->orderBy('id')->get();

echo "📊 Encontrados " . $templatesModulos->count() . " módulos Templates\n";

if ($templatesModulos->count() > 1) {
    echo "🧹 Removendo duplicados...\n";
    
    // Manter apenas o mais recente (com submódulos)
    $moduloMaisRecente = $templatesModulos->sortByDesc('id')->first();
    $moduloMaisRecente->load('submodulosAtivos');
    
    echo "✅ Mantendo módulo ID {$moduloMaisRecente->id} com {$moduloMaisRecente->submodulosAtivos->count()} submódulos\n";
    
    // Remover os outros
    $templatesModulos->where('id', '!=', $moduloMaisRecente->id)->each(function ($modulo) {
        echo "❌ Removendo módulo duplicado ID {$modulo->id}\n";
        $modulo->delete();
    });
    
    echo "✨ Limpeza concluída!\n";
} else {
    echo "✅ Nenhum duplicado encontrado!\n";
}

echo "🎯 Módulo Templates único confirmado!\n";