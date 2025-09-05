<?php

/**
 * Teste final: Verificar se PDF protocolado contém assinatura digital
 * 
 * Este teste verifica se o sistema está gerando corretamente PDFs
 * com a assinatura digital no formato padronizado após protocolo.
 */

require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Proposicao;
use App\Models\User;
use App\Http\Controllers\ProposicaoController;
use App\Services\Template\TemplateUniversalService;
use Illuminate\Support\Facades\Storage;

echo "=== TESTE PDF FINAL COM ASSINATURA DIGITAL ===\n\n";

// 1. Buscar proposição específica (ID 1) protocolada e assinada
$proposicao = Proposicao::find(1);

// Verificar se tem os requisitos
if ($proposicao && (
    $proposicao->status !== 'protocolado' || 
    !$proposicao->data_assinatura || 
    !$proposicao->codigo_validacao
)) {
    echo "❌ Proposição 1 não atende aos requisitos (status: {$proposicao->status}, assinatura: " . 
         ($proposicao->data_assinatura ? 'SIM' : 'NÃO') . ", código: " . 
         ($proposicao->codigo_validacao ? 'SIM' : 'NÃO') . ")\n";
    exit(1);
}

if (!$proposicao) {
    echo "❌ Nenhuma proposição protocolada e assinada encontrada\n";
    exit(1);
}

echo "✅ Proposição encontrada: ID {$proposicao->id}\n";
echo "   Status: {$proposicao->status}\n";
echo "   Protocolo: {$proposicao->numero_protocolo}\n";
echo "   Data Assinatura: {$proposicao->data_assinatura->format('d/m/Y H:i:s')}\n";
echo "   Código Validação: {$proposicao->codigo_validacao}\n\n";

// 2. Testar método caminhoPdfOficial
try {
    $templateService = app(TemplateUniversalService::class);
    $controller = new ProposicaoController($templateService);
    
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('caminhoPdfOficial');
    $method->setAccessible(true);
    
    $pdfPath = $method->invoke($controller, $proposicao);
    
    if (!$pdfPath) {
        echo "❌ Método caminhoPdfOficial não retornou caminho\n";
        exit(1);
    }
    
    echo "✅ PDF oficial selecionado: {$pdfPath}\n";
    
    // 3. Verificar se arquivo existe
    $fullPath = Storage::path($pdfPath);
    if (!file_exists($fullPath)) {
        echo "❌ Arquivo PDF não existe: {$fullPath}\n";
        exit(1);
    }
    
    $size = filesize($fullPath);
    echo "✅ Arquivo existe (tamanho: {$size} bytes)\n";
    
    // 4. Extrair e analisar conteúdo do PDF
    $content = shell_exec("pdftotext '{$fullPath}' - 2>/dev/null");
    if (!$content) {
        echo "❌ Não foi possível extrair texto do PDF\n";
        exit(1);
    }
    
    echo "✅ Texto extraído com sucesso\n\n";
    
    // 5. Verificar elementos obrigatórios
    $verificacoes = [
        'Número de Protocolo' => strpos($content, $proposicao->numero_protocolo) !== false,
        'Texto assinatura digital' => stripos($content, 'assinado digitalmente por') !== false,
        'Código de validação' => stripos($content, $proposicao->codigo_validacao) !== false,
        'URL de validação' => stripos($content, 'conferir_assinatura') !== false,
        'Nome do signatário' => stripos($content, $proposicao->autor->nome_politico ?? $proposicao->autor->name) !== false
    ];
    
    echo "=== VERIFICAÇÕES DE CONTEÚDO ===\n";
    $tudoOk = true;
    foreach ($verificacoes as $item => $resultado) {
        $status = $resultado ? "✅" : "❌";
        echo "{$status} {$item}: " . ($resultado ? "PRESENTE" : "AUSENTE") . "\n";
        if (!$resultado) $tudoOk = false;
    }
    
    echo "\n";
    
    // 6. Verificar se não tem placeholders inválidos
    $placeholdersInvalidos = [
        '[AGUARDANDO PROTOCOLO]' => stripos($content, '[AGUARDANDO PROTOCOLO]') !== false
    ];
    
    echo "=== VERIFICAÇÕES DE PLACEHOLDERS ===\n";
    foreach ($placeholdersInvalidos as $placeholder => $presente) {
        $status = $presente ? "❌" : "✅";
        echo "{$status} {$placeholder}: " . ($presente ? "PRESENTE (INVÁLIDO)" : "AUSENTE (CORRETO)") . "\n";
        if ($presente) $tudoOk = false;
    }
    
    echo "\n";
    
    // 7. Resultado final
    if ($tudoOk) {
        echo "🎉 TESTE APROVADO! PDF final contém todos os elementos necessários da assinatura digital.\n\n";
        
        echo "=== RESUMO DO TESTE ===\n";
        echo "✅ Sistema corrigido com sucesso\n";
        echo "✅ Template otimizado implementado\n";
        echo "✅ Assinatura digital padronizada funcionando\n";
        echo "✅ Código de validação gerado e incluído\n";
        echo "✅ PDF protocolado exibe assinatura corretamente\n\n";
        
        echo "Para testar manualmente:\n";
        echo "1. Acesse: http://localhost:8001/proposicoes/{$proposicao->id}/pdf (autenticado)\n";
        echo "2. Verifique a URL de validação: http://localhost:8001/conferir_assinatura?codigo={$proposicao->codigo_validacao}\n\n";
        
        exit(0);
    } else {
        echo "❌ TESTE FALHOU! PDF não contém todos os elementos necessários.\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "❌ Erro durante o teste: {$e->getMessage()}\n";
    exit(1);
}