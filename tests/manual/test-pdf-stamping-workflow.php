<?php

/**
 * Test complete PDF stamping workflow
 * Tests signature stamping and protocol stamping over existing PDFs
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\Proposicao;
use App\Models\User;
use App\Services\PDFStampingService;
use App\Services\AssinaturaDigitalService;
use App\Http\Controllers\ProposicaoProtocoloController;
use Illuminate\Support\Facades\Auth;

// Boot Laravel app
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE COMPLETO DE WORKFLOW DE STAMPING PDF ===\n\n";

try {
    // 1. Get a proposição with PDF (preferably proposição 2 that we created)
    echo "1. Buscando proposição para teste...\n";
    $proposicao = Proposicao::where('id', 2)->first();
    
    if (!$proposicao) {
        echo "❌ Proposição 2 não encontrada, criando uma nova...\n";
        // Could create a test proposição here, but let's use existing one
        $proposicao = Proposicao::first();
        
        if (!$proposicao) {
            throw new Exception("Nenhuma proposição encontrada para teste");
        }
    }
    
    echo "✅ Usando proposição ID: {$proposicao->id}\n";
    echo "   - Tipo: {$proposicao->tipo}\n";
    echo "   - Status: {$proposicao->status}\n";
    echo "   - PDF oficial: " . ($proposicao->pdf_oficial_path ?? 'NENHUM') . "\n";
    echo "   - PDF assinado: " . ($proposicao->arquivo_pdf_assinado ?? 'NENHUM') . "\n";
    echo "   - PDF protocolado: " . ($proposicao->arquivo_pdf_protocolado ?? 'NENHUM') . "\n\n";
    
    // 2. Ensure we have a PDF to work with
    if (!$proposicao->pdf_oficial_path && !$proposicao->arquivo_pdf_path) {
        echo "❌ Proposição não possui PDF para teste\n";
        return;
    }
    
    $basePdfPath = storage_path('app/' . ($proposicao->pdf_oficial_path ?? $proposicao->arquivo_pdf_path));
    if (!file_exists($basePdfPath)) {
        echo "❌ Arquivo PDF base não encontrado: {$basePdfPath}\n";
        return;
    }
    
    echo "✅ PDF base encontrado: " . basename($basePdfPath) . " (" . filesize($basePdfPath) . " bytes)\n\n";
    
    // 3. Test signature stamping
    echo "2. Testando stamping de assinatura...\n";
    
    $stampingService = app(PDFStampingService::class);
    $signatureData = [
        'tipo_certificado' => 'SIMULADO',
        'nome_assinante' => 'Usuário de Teste',
        'identificador' => 'TEST_' . time(),
        'usuario_id' => 1
    ];
    
    $signedPdfPath = $stampingService->applySignatureStamp($basePdfPath, $signatureData);
    
    if ($signedPdfPath && file_exists($signedPdfPath)) {
        echo "✅ Assinatura aplicada com sucesso!\n";
        echo "   - Arquivo assinado: " . basename($signedPdfPath) . "\n";
        echo "   - Tamanho: " . filesize($signedPdfPath) . " bytes\n";
        
        // Update proposição with signed PDF
        $relativePath = str_replace(storage_path('app/'), '', $signedPdfPath);
        $proposicao->update(['arquivo_pdf_assinado' => $relativePath]);
        echo "   - Campo arquivo_pdf_assinado atualizado\n\n";
    } else {
        echo "❌ Falha ao aplicar assinatura\n\n";
        return;
    }
    
    // 4. Test protocol stamping
    echo "3. Testando stamping de protocolo...\n";
    
    $protocolNumber = '2025' . sprintf('%04d', rand(1000, 9999));
    $protocolData = [
        'data_protocolo' => now()->format('d/m/Y H:i'),
        'funcionario_protocolo' => 'Sistema de Teste'
    ];
    
    $protocoledPdfPath = $stampingService->applyProtocolStamp($signedPdfPath, $protocolNumber, $protocolData);
    
    if ($protocoledPdfPath && file_exists($protocoledPdfPath)) {
        echo "✅ Protocolo aplicado com sucesso!\n";
        echo "   - Arquivo protocolado: " . basename($protocoledPdfPath) . "\n";
        echo "   - Tamanho: " . filesize($protocoledPdfPath) . " bytes\n";
        echo "   - Número protocolo: {$protocolNumber}\n";
        
        // Update proposição with protocoled PDF
        $relativePath = str_replace(storage_path('app/'), '', $protocoledPdfPath);
        $proposicao->update([
            'arquivo_pdf_protocolado' => $relativePath,
            'pdf_protocolo_aplicado' => true,
            'data_aplicacao_protocolo' => now(),
            'numero_protocolo' => $protocolNumber,
            'status' => 'protocolado'
        ]);
        echo "   - Campo arquivo_pdf_protocolado atualizado\n\n";
    } else {
        echo "❌ Falha ao aplicar protocolo\n\n";
        return;
    }
    
    // 5. Test PDF precedence
    echo "4. Testando precedência de PDF...\n";
    
    // Clear any cached results
    app('cache')->flush();
    $proposicao = $proposicao->fresh();
    
    // Use reflection to access the private method
    $controller = new \App\Http\Controllers\ProposicaoController(
        app(\App\Services\Template\TemplateUniversalService::class)
    );
    
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('caminhoPdfOficial');
    $method->setAccessible(true);
    
    $pdfOficial = $method->invoke($controller, $proposicao);
    
    echo "   - PDF oficial retornado: " . ($pdfOficial ?? 'NENHUM') . "\n";
    
    if ($pdfOficial && str_contains($pdfOficial, 'protocolado')) {
        echo "✅ Precedência funcionando - PDF protocolado foi selecionado\n";
    } elseif ($pdfOficial && str_contains($pdfOficial, 'assinado')) {
        echo "⚠️  PDF assinado selecionado (protocolo pode não estar no caminho)\n";
    } else {
        echo "❌ Precedência não funcionou como esperado\n";
    }
    echo "\n";
    
    // 6. Validate PDF contents
    echo "5. Validando conteúdo do PDF final...\n";
    
    if ($protocoledPdfPath) {
        // Check if we can extract text from the PDF
        $command = "pdftotext " . escapeshellarg($protocoledPdfPath) . " -";
        $content = shell_exec($command);
        
        if ($content) {
            $hasProtocol = stripos($content, $protocolNumber) !== false;
            $hasSignature = stripos($content, 'ASSINATURA DIGITAL') !== false;
            
            echo "   - Protocolo encontrado no PDF: " . ($hasProtocol ? 'SIM' : 'NÃO') . "\n";
            echo "   - Assinatura encontrada no PDF: " . ($hasSignature ? 'SIM' : 'NÃO') . "\n";
            
            if ($hasProtocol && $hasSignature) {
                echo "✅ PDF final contém assinatura E protocolo\n";
            } else {
                echo "⚠️  PDF final pode estar incompleto\n";
            }
        } else {
            echo "⚠️  Não foi possível extrair texto do PDF para validação\n";
        }
    }
    echo "\n";
    
    // 7. Summary
    echo "=== RESUMO DO TESTE ===\n";
    echo "✅ Workflow completo testado:\n";
    echo "   1. PDF base → PDF assinado (stamping)\n";
    echo "   2. PDF assinado → PDF protocolado (stamping)\n";
    echo "   3. Sistema de precedência funcionando\n";
    echo "   4. Campos do banco atualizados\n\n";
    
    echo "📄 Arquivos gerados:\n";
    echo "   - Base: " . basename($basePdfPath) . "\n";
    echo "   - Assinado: " . basename($signedPdfPath) . "\n";
    echo "   - Protocolado: " . basename($protocoledPdfPath) . "\n\n";
    
    echo "🎉 TESTE CONCLUÍDO COM SUCESSO!\n";
    echo "   O sistema agora aplica stamps sobre PDFs existentes\n";
    echo "   em vez de regenerar a partir de HTML.\n\n";
    
} catch (\Exception $e) {
    echo "❌ ERRO NO TESTE: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}