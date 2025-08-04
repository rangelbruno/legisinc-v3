<?php

use App\Models\Proposicao;
use App\Services\NumeroProcessoService;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "====================================\n";
echo "TESTE DO SISTEMA DE NÚMERO DE PROCESSO\n";
echo "====================================\n\n";

$service = new NumeroProcessoService();

// 1. Mostrar configurações atuais
echo "1. CONFIGURAÇÕES ATUAIS:\n";
echo "------------------------\n";
$config = $service->obterConfiguracoes();
foreach ($config as $chave => $valor) {
    echo "  " . str_pad($chave, 25) . ": " . (is_bool($valor) ? ($valor ? 'Sim' : 'Não') : $valor) . "\n";
}

// 2. Prever próximos números
echo "\n2. PRÓXIMOS NÚMEROS DISPONÍVEIS:\n";
echo "--------------------------------\n";
$proximos = $service->preverProximosNumeros();
foreach ($proximos as $tipo => $numero) {
    echo "  $tipo: $numero\n";
}

// 3. Simular atribuição de número
echo "\n3. SIMULAÇÃO DE ATRIBUIÇÃO:\n";
echo "----------------------------\n";

// Buscar uma proposição de teste
$proposicao = Proposicao::where('status', 'enviado_protocolo')
    ->whereNull('numero_processo')
    ->first();

if ($proposicao) {
    echo "  Proposição ID {$proposicao->id} (Tipo: {$proposicao->tipo})\n";
    echo "  Status atual: {$proposicao->status}\n";
    echo "  Número processo atual: " . ($proposicao->numero_processo ?? 'Nenhum') . "\n";
    
    try {
        // Atribuir número
        $numeroAtribuido = $service->atribuirNumeroProcesso($proposicao);
        echo "  ✓ Número atribuído com sucesso: $numeroAtribuido\n";
        
        // Verificar se foi inserido no documento
        $proposicao->refresh();
        if (strpos($proposicao->conteudo, $numeroAtribuido) !== false) {
            echo "  ✓ Número inserido no documento\n";
        } else {
            echo "  ⚠ Número não encontrado no documento\n";
        }
        
    } catch (\Exception $e) {
        echo "  ✗ Erro ao atribuir número: " . $e->getMessage() . "\n";
    }
} else {
    echo "  ⚠ Nenhuma proposição disponível para teste\n";
    echo "  (Precisa ter status 'enviado_protocolo' e sem número de processo)\n";
}

// 4. Estatísticas
echo "\n4. ESTATÍSTICAS:\n";
echo "----------------\n";
$totalComNumero = Proposicao::whereNotNull('numero_processo')->count();
$totalSemNumero = Proposicao::whereNull('numero_processo')->count();
$totalProtocoladas = Proposicao::where('status', 'protocolado')->count();

echo "  Proposições com número: $totalComNumero\n";
echo "  Proposições sem número: $totalSemNumero\n";
echo "  Proposições protocoladas: $totalProtocoladas\n";

echo "\n====================================\n";
echo "TESTE CONCLUÍDO\n";
echo "====================================\n";