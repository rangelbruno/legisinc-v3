#!/bin/bash

echo "======================================"
echo "TESTE: PDF com Correções Aplicadas"
echo "======================================"
echo ""

# 1. Verificar se a proposição 5 existe e tem conteúdo
echo "1. Verificando proposição 5..."
PROPOSICAO_DATA=$(docker exec legisinc-app php artisan tinker --execute="
\$p = \App\Models\Proposicao::find(5);
if (\$p) {
    echo 'ID: ' . \$p->id . PHP_EOL;
    echo 'Tipo: ' . \$p->tipo . PHP_EOL;
    echo 'Status: ' . \$p->status . PHP_EOL;
    echo 'Número Protocolo: ' . (\$p->numero_protocolo ?: 'NÃO PROTOCOLADO') . PHP_EOL;
    echo 'Arquivo Path: ' . (\$p->arquivo_path ?: 'NENHUM') . PHP_EOL;
    echo 'Assinatura Digital: ' . (\$p->assinatura_digital ? 'SIM' : 'NÃO') . PHP_EOL;
} else {
    echo 'Proposição 5 não encontrada';
}
" 2>/dev/null)

echo "$PROPOSICAO_DATA"
echo ""

# 2. Testar geração do PDF
echo "2. Testando geração do PDF com correções..."
echo ""

# Criar um script PHP para testar
cat > /tmp/test_pdf_generation.php << 'EOF'
<?php
require_once '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use App\Models\Proposicao;
use App\Http\Controllers\ProposicaoAssinaturaController;

try {
    $proposicao = Proposicao::find(5);
    if (!$proposicao) {
        echo "ERRO: Proposição 5 não encontrada\n";
        exit(1);
    }
    
    echo "Proposição encontrada:\n";
    echo "- ID: {$proposicao->id}\n";
    echo "- Tipo: {$proposicao->tipo}\n";
    echo "- Número Protocolo: " . ($proposicao->numero_protocolo ?: '[AGUARDANDO PROTOCOLO]') . "\n";
    echo "\n";
    
    // Testar geração do PDF
    $controller = new ProposicaoAssinaturaController();
    $pdfPath = storage_path('app/proposicoes/pdfs/5/proposicao_5_test.pdf');
    
    // Criar diretório se não existir
    $dir = dirname($pdfPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    echo "Gerando PDF em: $pdfPath\n";
    
    // Chamar método privado via reflection
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('gerarPDFParaAssinatura');
    $method->setAccessible(true);
    $method->invoke($controller, $proposicao);
    
    // Verificar se PDF foi criado
    $pdfRealPath = storage_path('app/proposicoes/pdfs/5/proposicao_5.pdf');
    if (file_exists($pdfRealPath)) {
        $size = filesize($pdfRealPath);
        echo "\n✅ PDF GERADO COM SUCESSO!\n";
        echo "   Tamanho: " . number_format($size) . " bytes\n";
        
        // Verificar conteúdo do PDF
        exec("pdftotext '$pdfRealPath' - 2>/dev/null | head -20", $content);
        echo "\n📄 Primeiras linhas do PDF:\n";
        echo "   " . implode("\n   ", array_slice($content, 0, 10)) . "\n";
        
        // Verificar se tem AGUARDANDO PROTOCOLO se não houver número
        if (!$proposicao->numero_protocolo) {
            exec("pdftotext '$pdfRealPath' - 2>/dev/null | grep -c 'AGUARDANDO PROTOCOLO'", $aguardando);
            if ($aguardando[0] > 0) {
                echo "\n✅ Texto '[AGUARDANDO PROTOCOLO]' encontrado no PDF (correto!)\n";
            } else {
                echo "\n⚠️ AVISO: Proposição sem protocolo mas '[AGUARDANDO PROTOCOLO]' não encontrado\n";
            }
        }
        
        // Verificar se tem assinatura digital
        if ($proposicao->assinatura_digital) {
            exec("pdftotext '$pdfRealPath' - 2>/dev/null | grep -c 'Assinatura Digital'", $assinatura);
            if ($assinatura[0] > 0) {
                echo "✅ Assinatura digital encontrada no PDF\n";
            } else {
                echo "⚠️ AVISO: Proposição assinada mas assinatura não encontrada no PDF\n";
            }
        }
        
    } else {
        echo "\n❌ ERRO: PDF não foi gerado em $pdfRealPath\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ ERRO na geração do PDF:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
EOF

# Executar teste
docker exec legisinc-app php /tmp/test_pdf_generation.php

echo ""
echo "======================================"
echo "3. Verificando imagem do cabeçalho..."
echo "======================================"

# Verificar se a imagem padrão existe
docker exec legisinc-app ls -la /var/www/html/public/template/cabecalho.png 2>/dev/null
if [ $? -eq 0 ]; then
    echo "✅ Imagem do cabeçalho encontrada em public/template/cabecalho.png"
else
    echo "⚠️ Imagem do cabeçalho não encontrada no local esperado"
fi

echo ""
echo "======================================"
echo "TESTE CONCLUÍDO"
echo "======================================"