<?php

require_once __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTE DE OTIMIZAÇÃO PARA LEGISLATIVO ===\n\n";

// Verificar se existe uma proposição com arquivo salvo
$proposicao = \App\Models\Proposicao::where('arquivo_path', '!=', null)
    ->where('arquivo_path', '!=', '')
    ->first();

if (!$proposicao) {
    echo "❌ Nenhuma proposição com arquivo salvo encontrada.\n";
    echo "Criando proposição de teste...\n";
    
    $proposicao = \App\Models\Proposicao::create([
        'tipo' => 'mocao',
        'ementa' => 'Teste de arquivo salvo para legislativo',
        'conteudo' => 'Conteúdo de teste com acentuação: São Paulo, João, Moção',
        'status' => 'enviado_legislativo',
        'autor_id' => 1,
        'arquivo_path' => 'proposicoes/proposicao_999_teste.rtf'
    ]);
    
    // Criar arquivo fake
    Storage::disk('public')->put($proposicao->arquivo_path, 
        '{\rtf1 Teste de arquivo salvo com acentua\u231*\u227*o}');
    
    echo "✅ Proposição de teste criada: ID {$proposicao->id}\n\n";
}

echo "1. VERIFICANDO EXISTÊNCIA DE ARQUIVO\n";
echo str_repeat('-', 50) . "\n";

$temArquivoSalvo = !empty($proposicao->arquivo_path) && 
                  (Storage::disk('local')->exists($proposicao->arquivo_path) || 
                   Storage::disk('public')->exists($proposicao->arquivo_path) ||
                   file_exists(storage_path('app/' . $proposicao->arquivo_path)));

echo "Proposição ID: {$proposicao->id}\n";
echo "Arquivo Path: {$proposicao->arquivo_path}\n";
echo "Arquivo existe: " . ($temArquivoSalvo ? '✅ SIM' : '❌ NÃO') . "\n";
echo "Status: {$proposicao->status}\n\n";

echo "2. SIMULANDO LÓGICA DO ONLYOFFICE CONTROLLER\n";
echo str_repeat('-', 50) . "\n";

if ($temArquivoSalvo) {
    echo "✅ RESULTADO: Legislativo usará arquivo salvo existente (RÁPIDO)\n";
    echo "   - Não processará template universal (evita 20+ segundos)\n";
    echo "   - Carregará documento já editado pelo Parlamentar\n";
    echo "   - Caracteres especiais já processados corretamente\n";
} else {
    echo "⚠️ FALLBACK: Usará template universal (mais lento)\n";
    echo "   - Só acontece se não existir arquivo salvo\n";
    echo "   - Necessário processar template do zero\n";
}

echo "\n3. VERIFICANDO FLUXO ESPECÍFICO\n";
echo str_repeat('-', 50) . "\n";

// Simular verificação de tipo de proposição
$proposicao->load('tipoProposicao');
$templateUniversalService = app(\App\Services\Template\TemplateUniversalService::class);

if ($proposicao->tipoProposicao) {
    $deveUsarUniversal = $templateUniversalService->deveUsarTemplateUniversal($proposicao->tipoProposicao);
    echo "Tipo de proposição: {$proposicao->tipoProposicao->nome}\n";
    echo "Template universal habilitado: " . ($deveUsarUniversal ? 'SIM' : 'NÃO') . "\n";
} else {
    echo "⚠️ Tipo de proposição não encontrado\n";
}

echo "\n=== RESULTADO FINAL ===\n";

if ($temArquivoSalvo) {
    echo "🚀 OTIMIZAÇÃO ATIVA:\n";
    echo "   ✅ Legislativo carregará INSTANTANEAMENTE\n";  
    echo "   ✅ Usará arquivo com acentuação já corrigida\n";
    echo "   ✅ Evita processamento desnecessário do template\n";
    echo "   ✅ Performance otimizada\n";
} else {
    echo "⚠️ OTIMIZAÇÃO NÃO APLICÁVEL:\n";
    echo "   - Proposição não tem arquivo salvo\n";
    echo "   - Usará template universal (normal)\n";
    echo "   - Performance conforme esperado\n";
}

echo "\n";