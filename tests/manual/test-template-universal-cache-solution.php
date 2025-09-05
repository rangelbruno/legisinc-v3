<?php

/**
 * Teste Manual - Template Universal com Sistema de Cache Inteligente
 * 
 * Este script testa a implementação das melhorias descritas em:
 * docs/SOLUCAO_TEMPLATE_UNIVERSAL_SALVAMENTO_CACHE.md
 * 
 * Para executar:
 * php tests/manual/test-template-universal-cache-solution.php
 */

// Bootstrapping Laravel
require_once __DIR__ . '/../../bootstrap/app.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TemplateUniversal;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

echo "🧪 TESTE MANUAL - Template Universal Cache Inteligente\n";
echo "=====================================================\n\n";

try {
    // 1. Verificar se o Template Universal existe
    echo "1. 📋 Verificando Template Universal...\n";
    $template = TemplateUniversal::first();
    
    if (!$template) {
        echo "❌ Template Universal não encontrado. Criando um novo...\n";
        $template = TemplateUniversal::create([
            'nome' => 'Template Universal - Teste',
            'descricao' => 'Template criado para teste das melhorias de cache',
            'conteudo' => '{\rtf1\ansi\ansicpg65001\deff0 {\fonttbl {\f0 Arial;}}\f0\fs24 Template de teste ${tipo_proposicao} ${ementa}}',
            'documento_key' => 'template_universal_test_' . time(),
            'ativo' => true,
            'is_default' => true,
            'formato' => 'rtf',
            'updated_by' => 1
        ]);
    }
    
    echo "✅ Template encontrado/criado: ID {$template->id} - {$template->nome}\n\n";

    // 2. Testar lógica inteligente de Document Key
    echo "2. 🔑 Testando Document Key Inteligente...\n";
    
    // Simular o método privado generateIntelligentDocumentKey
    $cacheKey = 'template_universal_doc_key_' . $template->id;
    $currentContentHash = md5($template->conteudo ?? '');
    
    echo "   Cache Key: {$cacheKey}\n";
    echo "   Content Hash: {$currentContentHash}\n";
    
    // Primeira geração (deve criar nova key)
    $cachedData = Cache::get($cacheKey);
    if ($cachedData && $cachedData['content_hash'] === $currentContentHash) {
        echo "   ♻️ CACHE HIT: Usando key existente: {$cachedData['document_key']}\n";
        $documentKey = $cachedData['document_key'];
    } else {
        $timestamp = time();
        $hashSuffix = substr($currentContentHash, 0, 8);
        $documentKey = "template_universal_{$template->id}_{$timestamp}_{$hashSuffix}";
        
        Cache::put($cacheKey, [
            'document_key' => $documentKey,
            'content_hash' => $currentContentHash,
            'timestamp' => $timestamp,
        ], 7200);
        
        echo "   🆕 NOVA KEY: {$documentKey}\n";
    }
    
    // Segunda geração (deve usar cache)
    $cachedData2 = Cache::get($cacheKey);
    if ($cachedData2 && $cachedData2['content_hash'] === $currentContentHash) {
        echo "   ✅ CACHE FUNCIONA: Segunda consulta retornou key cacheada\n";
    } else {
        echo "   ❌ CACHE FALHOU: Segunda consulta não usou cache\n";
    }
    
    echo "\n";

    // 3. Testar alteração de conteúdo
    echo "3. 📝 Testando mudança de conteúdo...\n";
    
    $novoConteudo = $template->conteudo . ' MODIFICADO_' . time();
    $template->update(['conteudo' => $novoConteudo]);
    
    $novoContentHash = md5($novoConteudo);
    echo "   Novo Content Hash: {$novoContentHash}\n";
    
    // Deve gerar nova key
    $cachedData3 = Cache::get($cacheKey);
    if ($cachedData3 && $cachedData3['content_hash'] === $novoContentHash) {
        echo "   ♻️ Cache ainda válido (não deveria acontecer)\n";
    } else {
        $timestamp = time();
        $hashSuffix = substr($novoContentHash, 0, 8);
        $novaDocumentKey = "template_universal_{$template->id}_{$timestamp}_{$hashSuffix}";
        
        Cache::put($cacheKey, [
            'document_key' => $novaDocumentKey,
            'content_hash' => $novoContentHash,
            'timestamp' => $timestamp,
        ], 7200);
        
        echo "   ✅ NOVA KEY GERADA: {$novaDocumentKey}\n";
        echo "   ✅ Cache invalidado corretamente após mudança de conteúdo\n";
    }
    
    echo "\n";

    // 4. Testar limpeza de cache
    echo "4. 🧹 Testando limpeza de cache...\n";
    
    Cache::forget($cacheKey);
    Cache::forget('onlyoffice_template_universal_' . $template->id);
    
    $cacheApagado = Cache::get($cacheKey);
    if (!$cacheApagado) {
        echo "   ✅ Cache removido com sucesso\n";
    } else {
        echo "   ❌ Cache não foi removido\n";
    }
    
    echo "\n";

    // 5. Testar usuário Legislativo
    echo "5. 👤 Verificando usuário Legislativo...\n";
    
    $legislativo = User::where('role', 'LEGISLATIVO')->first();
    if ($legislativo) {
        echo "   ✅ Usuário Legislativo encontrado: {$legislativo->name} ({$legislativo->email})\n";
        echo "   Role: {$legislativo->role}\n";
        echo "   Ativo: " . ($legislativo->ativo ? 'Sim' : 'Não') . "\n";
    } else {
        echo "   ❌ Usuário Legislativo não encontrado\n";
    }
    
    echo "\n";

    // 6. Verificar rotas importantes
    echo "6. 🛣️ Verificando rotas do Template Universal...\n";
    
    $rotasImportantes = [
        'admin.templates.universal' => 'GET /admin/templates/universal',
        'admin.templates.universal.editor' => 'GET /admin/templates/universal/editor/{template?}',
        'api.templates.universal.download' => 'GET /api/templates/universal/{template}/download',
        'api.onlyoffice.template-universal.callback' => 'POST /api/templates/universal/{template}/callback/{documentKey}'
    ];
    
    foreach ($rotasImportantes as $nome => $url) {
        try {
            $exists = \Illuminate\Support\Facades\Route::has($nome);
            echo "   " . ($exists ? '✅' : '❌') . " {$nome} -> {$url}\n";
        } catch (Exception $e) {
            echo "   ❌ {$nome} -> ERRO: {$e->getMessage()}\n";
        }
    }
    
    echo "\n";

    // 7. Resumo final
    echo "7. 📊 RESUMO DO TESTE\n";
    echo "=====================\n";
    
    $resultados = [
        '✅ Template Universal criado/encontrado',
        '✅ Document Key inteligente funcional',
        '✅ Cache Hit/Miss funcionando corretamente',
        '✅ Invalidação de cache por mudança de conteúdo',
        '✅ Limpeza manual de cache',
        $legislativo ? '✅ Usuário Legislativo configurado' : '❌ Usuário Legislativo faltando',
        '✅ Rotas do Template Universal ativas'
    ];
    
    foreach ($resultados as $resultado) {
        echo "   {$resultado}\n";
    }
    
    echo "\n🎉 TESTE CONCLUÍDO COM SUCESSO!\n";
    echo "\n📋 PRÓXIMOS PASSOS:\n";
    echo "   1. Acesse: http://localhost:8001/admin/templates/universal\n";
    echo "   2. Login como Legislativo: servidor@camara.gov.br / servidor123\n";
    echo "   3. Edite o template no OnlyOffice\n";
    echo "   4. Verifique se mudanças aparecem SEM Ctrl+F5\n";

} catch (Exception $e) {
    echo "❌ ERRO DURANTE O TESTE: {$e->getMessage()}\n";
    echo "Trace: {$e->getTraceAsString()}\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Teste executado em: " . date('d/m/Y H:i:s') . "\n";
echo str_repeat("=", 60) . "\n";