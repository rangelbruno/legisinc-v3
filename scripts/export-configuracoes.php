<?php

require_once '/var/www/html/vendor/autoload.php';

$app = require_once '/var/www/html/bootstrap/app.php';
$app->boot();

echo "🔍 Exportando configurações atuais...\\n";

// Buscar valores salvos no banco
$valores = \DB::table('parametros_valores as pv')
    ->join('parametros_campos as pc', 'pv.campo_id', '=', 'pc.id')
    ->join('parametros_submodulos as ps', 'pc.submodulo_id', '=', 'ps.id')
    ->join('parametros_modulos as pm', 'ps.modulo_id', '=', 'pm.id')
    ->where('pm.nome', 'Dados Gerais')
    ->whereNull('pv.valido_ate')
    ->select('pc.nome as campo', 'ps.nome as submodulo', 'pv.valor')
    ->orderBy('ps.ordem')
    ->orderBy('pc.ordem')
    ->get();

echo "\\n=== CONFIGURAÇÕES SALVAS ===\\n";
foreach ($valores as $valor) {
    echo "'{$valor->campo}' => '{$valor->valor}',\\n";
}

echo "\\n✅ Export concluído!\\n";