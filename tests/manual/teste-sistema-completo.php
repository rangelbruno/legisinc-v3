<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Simular ambiente Laravel
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🚀 TESTE FINAL DO SISTEMA COMPLETO\n";
echo "==================================\n";

// Verificar se existe proposição para testar
$proposicao = \App\Models\Proposicao::first();

if (!$proposicao) {
    echo "❌ Nenhuma proposição encontrada. Execute 'migrate:fresh --seed' primeiro.\n";
    exit;
}

echo "✅ Proposição encontrada: ID {$proposicao->id}\n";
echo "   Conteúdo atual: " . substr($proposicao->conteudo ?? 'vazio', 0, 100) . "...\n";
echo "   Status: {$proposicao->status}\n\n";

// Verificar se existe arquivo salvo
if ($proposicao->arquivo_path) {
    // Tentar múltiplos caminhos possíveis
    $caminhosPossiveis = [
        storage_path('app/' . $proposicao->arquivo_path),
        storage_path('app/private/' . $proposicao->arquivo_path),
        storage_path('app/local/' . $proposicao->arquivo_path),
    ];
    
    $caminhoArquivo = null;
    foreach ($caminhosPossiveis as $caminho) {
        if (file_exists($caminho)) {
            $caminhoArquivo = $caminho;
            break;
        }
    }
    
    if ($caminhoArquivo && file_exists($caminhoArquivo)) {
        $tamanhoArquivo = filesize($caminhoArquivo);
        echo "✅ Arquivo salvo encontrado: {$proposicao->arquivo_path}\n";
        echo "   Tamanho: " . number_format($tamanhoArquivo) . " bytes\n\n";
        
        // Testar extração do arquivo real
        $service = app(\App\Services\OnlyOffice\OnlyOfficeService::class);
        $reflection = new ReflectionClass($service);
        
        try {
            $extrairMethod = $reflection->getMethod('extrairTextoRTFOtimizado');
            $extrairMethod->setAccessible(true);
            
            $limparMethod = $reflection->getMethod('limparConteudoExtraido');
            $limparMethod->setAccessible(true);
            
            $validarMethod = $reflection->getMethod('isConteudoValido');
            $validarMethod->setAccessible(true);
            
            echo "🔍 TESTANDO EXTRAÇÃO DO ARQUIVO REAL...\n";
            echo "=======================================\n";
            
            // Extrair conteúdo
            $conteudoExtraido = $extrairMethod->invoke($service, $caminhoArquivo);
            echo "📤 Conteúdo extraído: " . strlen($conteudoExtraido) . " caracteres\n";
            echo "   Preview: " . substr($conteudoExtraido, 0, 100) . "...\n\n";
            
            if (!empty($conteudoExtraido)) {
                // Limpar conteúdo
                $conteudoLimpo = $limparMethod->invoke($service, $conteudoExtraido);
                echo "🧹 Após limpeza: " . strlen($conteudoLimpo) . " caracteres\n";
                echo "   Preview: " . substr($conteudoLimpo, 0, 100) . "...\n\n";
                
                // Validar conteúdo
                $ehValido = $validarMethod->invoke($service, $conteudoLimpo);
                $status = $ehValido ? "✅ VÁLIDO" : "❌ INVÁLIDO";
                
                echo "🎯 Validação: {$status}\n\n";
                
                if ($ehValido && !empty($conteudoLimpo)) {
                    echo "🎊 SUCESSO! O sistema deveria salvar este conteúdo no banco:\n";
                    echo "   Conteúdo final: " . substr($conteudoLimpo, 0, 200) . "...\n";
                } else {
                    echo "⚠️  PROBLEMA: Conteúdo não passou na validação ou está vazio.\n";
                }
            } else {
                echo "❌ PROBLEMA: Nenhum conteúdo foi extraído do arquivo.\n";
            }
            
        } catch (Exception $e) {
            echo "❌ Erro ao testar extração: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "❌ Arquivo não encontrado: {$caminhoArquivo}\n";
    }
} else {
    echo "⚠️  Nenhum arquivo salvo encontrado. Edite a proposição no OnlyOffice primeiro.\n";
}

echo "\n📋 INSTRUÇÕES PARA TESTE REAL:\n";
echo "==============================\n";
echo "1. Acesse: http://localhost:8001/proposicoes/{$proposicao->id}\n";
echo "2. Clique em 'Editar no OnlyOffice'\n";
echo "3. Digite algum texto real (ex: 'Esta proposição estabelece...')\n";
echo "4. Salve (Ctrl+S ou aguarde salvamento automático)\n";
echo "5. Execute este script novamente para ver o resultado\n";
echo "6. Verifique os logs: tail -f storage/logs/laravel.log\n";