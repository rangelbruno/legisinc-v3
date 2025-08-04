<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

// Buscar proposição
$proposicao = App\Models\Proposicao::find(53);
if (!$proposicao) {
    echo "Proposição não encontrada\n";
    exit;
}

// Corrigir manualmente os textos
$ementaLimpa = "0053/2025 - Projeto de Lei Ordinária";
$conteudoLimpo = "Ementa - Bruno

Texto Principal:

Conteúdo São Paulo...";

// Atualizar no banco
$proposicao->ementa = $ementaLimpa;
$proposicao->conteudo = $conteudoLimpo;
$proposicao->save();

echo "✅ Proposição 53 atualizada com textos limpos!\n";
echo "Ementa: " . $ementaLimpa . "\n";
echo "Conteúdo: " . substr($conteudoLimpo, 0, 100) . "...\n";