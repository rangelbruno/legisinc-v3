<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 DIAGNÓSTICO DO TEMPLATE UNIVERSAL\n";
echo "=====================================\n\n";

try {
    // 1. Verificar template universal no banco
    $template = App\Models\TemplateUniversal::where('is_default', true)->first();
    
    if (!$template) {
        echo "❌ Nenhum template universal padrão encontrado\n";
        exit(1);
    }
    
    echo "✅ Template encontrado: " . $template->nome . "\n";
    echo "📄 Análise do conteúdo:\n";
    
    $conteudo = $template->conteudo ?? '';
    
    // 2. Verificar estrutura RTF
    $comecaComRTF = str_starts_with($conteudo, '{\rtf1');
    $temUTF8 = str_contains($conteudo, '\ansicpg65001');
    $terminaComChave = str_ends_with(trim($conteudo), '}');
    
    echo "   Começa com {\rtf1: " . ($comecaComRTF ? "SIM ✅" : "NÃO ❌") . "\n";
    echo "   Contém \\ansicpg65001: " . ($temUTF8 ? "SIM ✅" : "NÃO ❌") . "\n";
    echo "   Termina com }: " . ($terminaComChave ? "SIM ✅" : "NÃO ❌") . "\n";
    
    // 3. Analisar primeiros 200 caracteres
    $inicio = substr($conteudo, 0, 200);
    echo "   Início do conteúdo: " . $inicio . "\n\n";
    
    // 4. Verificar se há duplicação de cabeçalho RTF
    $ocorrenciasRTF = substr_count($conteudo, '{\rtf1');
    echo "🔄 Ocorrências de {\rtf1: $ocorrenciasRTF " . ($ocorrenciasRTF > 1 ? "❌ DUPLICADO!" : "✅") . "\n";
    
    // 5. Verificar acentuação
    echo "🔤 Análise de acentuação:\n";
    $hasUnicodeSequences = preg_match('/\\\\u\d+\*/', $conteudo);
    if ($hasUnicodeSequences) {
        preg_match_all('/\\\\u(\d+)\*/', $conteudo, $matches);
        echo "   ✅ Encontradas " . count($matches[0]) . " sequências Unicode\n";
        foreach (array_slice($matches[0], 0, 5) as $i => $sequence) {
            $codepoint = $matches[1][$i];
            $char = mb_chr((int)$codepoint, 'UTF-8');
            echo "   ✅ Encontrado '$sequence' (conversão de '$char')\n";
        }
    } else {
        echo "   ⚠️  Nenhuma sequência Unicode encontrada\n";
    }
    
    // 6. Testar simulação de download
    echo "\n📥 SIMULANDO DOWNLOAD...\n";
    
    $controller = new App\Http\Controllers\Admin\TemplateUniversalController(
        app(App\Services\OnlyOffice\OnlyOfficeService::class),
        app(App\Services\Template\TemplateProcessorService::class)
    );
    
    $response = $controller->download($template);
    $conteudoDownload = $response->getContent();
    $headers = $response->headers->all();
    
    echo "   Content-Type: " . ($headers['content-type'][0] ?? 'UNDEFINED') . "\n";
    echo "   Tamanho: " . strlen($conteudoDownload) . " bytes\n";
    echo "   Primeiros 100 chars: " . substr($conteudoDownload, 0, 100) . "\n";
    
    // 7. Verificar se download corrompe o conteúdo
    $downloadComecaRTF = str_starts_with($conteudoDownload, '{\rtf1');
    $downloadOcorrenciasRTF = substr_count($conteudoDownload, '{\rtf1');
    
    echo "   Download começa com {\rtf1: " . ($downloadComecaRTF ? "SIM ✅" : "NÃO ❌") . "\n";
    echo "   Download tem $downloadOcorrenciasRTF ocorrências de {\rtf1: " . ($downloadOcorrenciasRTF === 1 ? "✅" : "❌ PROBLEMA!") . "\n";
    
    // 8. Diagnóstico final
    echo "\n🎯 DIAGNÓSTICO FINAL:\n";
    
    if ($downloadOcorrenciasRTF > 1) {
        echo "❌ PROBLEMA IDENTIFICADO: Duplicação de cabeçalho RTF\n";
        echo "   O método processarImagensParaEditor ou garantirRTFValido está duplicando a estrutura RTF\n";
        echo "   Solução: Corrigir a lógica de processamento para evitar duplicação\n";
    } elseif (!$downloadComecaRTF) {
        echo "❌ PROBLEMA IDENTIFICADO: Conteúdo não é RTF válido\n";
        echo "   O download não retorna um RTF com estrutura correta\n";
        echo "   Solução: Verificar método garantirRTFValido()\n";
    } elseif ($headers['content-type'][0] !== 'application/rtf; charset=utf-8') {
        echo "❌ PROBLEMA IDENTIFICADO: Content-Type incorreto\n";
        echo "   Deve ser 'application/rtf; charset=utf-8'\n";
        echo "   Atual: " . ($headers['content-type'][0] ?? 'UNDEFINED') . "\n";
    } else {
        echo "✅ ESTRUTURA RTF PARECE CORRETA\n";
        echo "   O problema pode ser no cache do OnlyOffice ou na configuração do document_key\n";
        echo "   Tente limpar o cache ou regenerar o document_key\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERRO DURANTE DIAGNÓSTICO: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n🔚 Diagnóstico concluído!\n";