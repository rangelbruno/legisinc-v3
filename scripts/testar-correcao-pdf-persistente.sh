#!/bin/bash

echo "🔐 TESTE COMPLETO - ASSINATURA DIGITAL COM PYHANKO"
echo "=================================================="

# 1. Gerar certificado de teste
PFX_TESTE="/tmp/teste_assinatura.pfx"
SENHA_TESTE="123456"

echo "📝 1. Gerando certificado PFX de teste..."
openssl genpkey -algorithm RSA -out /tmp/test_key.key
openssl req -new -x509 -key /tmp/test_key.key -out /tmp/test_cert.pem -days 365 \
    -subj "/C=BR/ST=SP/L=Caraguatatuba/O=Câmara Municipal/CN=Sistema Legisinc"
openssl pkcs12 -export -in /tmp/test_cert.pem -inkey /tmp/test_key.key \
    -out "$PFX_TESTE" -password "pass:$SENHA_TESTE"

echo "✅ Certificado criado: $PFX_TESTE"

# 2. Copiar arquivos para o container Laravel  
echo ""
echo "📋 2. Preparando ambiente de teste..."
docker cp "$PFX_TESTE" legisinc-app:/tmp/
docker cp legisinc-app:/var/www/html/storage/app/proposicoes/pdfs/1/proposicao_1_unified_1757315263.pdf /tmp/test_pdf.pdf 2>/dev/null || {
    echo "⚠️ Criando PDF de teste alternativo..."
    echo "%PDF-1.4
1 0 obj
<<
/Type /Catalog
/Pages 2 0 R
>>
endobj

2 0 obj
<<
/Type /Pages
/Kids [3 0 R]
/Count 1
>>
endobj

3 0 obj
<<
/Type /Page
/Parent 2 0 R
/MediaBox [0 0 612 792]
/Contents 4 0 R
>>
endobj

4 0 obj
<<
/Length 44
>>
stream
BT
/F1 12 Tf
100 700 Td
(Teste de assinatura digital) Tj
ET
endstream
endobj

xref
0 5
0000000000 65535 f 
0000000010 00000 n 
0000000053 00000 n 
0000000090 00000 n 
0000000171 00000 n 
trailer
<<
/Size 5
/Root 1 0 R
>>
startxref
264
%%EOF" > /tmp/test_pdf.pdf
}

# 3. Criar script PHP de teste completo
echo ""
echo "🧪 3. Testando integração completa..."
cat > /tmp/teste_assinatura_completa.php << 'EOF'
<?php
require_once '/var/www/html/vendor/autoload.php';

use App\Services\AssinaturaDigitalService;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once '/var/www/html/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 Testando AssinaturaDigitalService...\n";

$service = new AssinaturaDigitalService();

// Teste 1: Validação de senha PFX
echo "\n1️⃣ TESTE: Validação de senha PFX\n";
echo "================================\n";

$pfxPath = '/tmp/teste_assinatura.pfx';
$senhaCorreta = '123456';
$senhaIncorreta = 'senha_errada';

$resultado1 = $service->validarSenhaPFX($pfxPath, $senhaCorreta);
echo "✅ Senha correta: " . ($resultado1 ? "VÁLIDA" : "INVÁLIDA") . "\n";

$resultado2 = $service->validarSenhaPFX($pfxPath, $senhaIncorreta);
echo "✅ Senha incorreta: " . ($resultado2 ? "VÁLIDA (ERRO!)" : "INVÁLIDA (CORRETO)") . "\n";

// Teste 2: Assinatura de PDF  
echo "\n2️⃣ TESTE: Assinatura de PDF\n";
echo "============================\n";

$pdfPath = '/tmp/test_pdf.pdf';
if (!file_exists($pdfPath)) {
    echo "❌ Arquivo PDF não encontrado: $pdfPath\n";
    exit(1);
}

$dadosAssinatura = [
    'tipo_certificado' => 'PFX',
    'certificado_path' => $pfxPath,
    'certificado_senha' => $senhaCorreta,
    'nome_assinante' => 'Sistema de Teste',
    'razao' => 'Teste de assinatura digital',
    'localizacao' => 'Caraguatatuba-SP',
    'contato' => 'teste@sistema.gov.br'
];

try {
    echo "📝 Iniciando assinatura do PDF...\n";
    $pdfAssinado = $service->assinarPDF($pdfPath, $dadosAssinatura);
    
    if ($pdfAssinado && file_exists($pdfAssinado)) {
        echo "✅ PDF assinado com sucesso: $pdfAssinado\n";
        echo "📊 Tamanho original: " . filesize($pdfPath) . " bytes\n";
        echo "📊 Tamanho assinado: " . filesize($pdfAssinado) . " bytes\n";
        
        // Verificar se PDF tem assinatura digital
        $conteudo = file_get_contents($pdfAssinado);
        if (strpos($conteudo, '/ByteRange') !== false && strpos($conteudo, '/Contents') !== false) {
            echo "🔒 PDF contém assinatura digital PAdES!\n";
        } else {
            echo "⚠️ PDF pode não ter assinatura digital válida\n";
        }
    } else {
        echo "❌ Falha na assinatura do PDF\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro na assinatura: " . $e->getMessage() . "\n";
}

echo "\n📊 RESUMO DOS TESTES\n";
echo "===================\n";
echo "- Validação de senha: " . ($resultado1 && !$resultado2 ? "✅ OK" : "❌ FALHA") . "\n";
echo "- Assinatura de PDF: " . (isset($pdfAssinado) && $pdfAssinado ? "✅ OK" : "❌ FALHA") . "\n";

if ($resultado1 && !$resultado2 && isset($pdfAssinado) && $pdfAssinado) {
    echo "\n🎉 TODOS OS TESTES PASSARAM!\n";
    echo "🚀 Sistema de assinatura digital está funcional!\n";
} else {
    echo "\n❌ ALGUNS TESTES FALHARAM\n";
    echo "🔧 Verifique os logs para mais detalhes\n";
}
EOF

# 4. Executar teste completo
docker cp /tmp/teste_assinatura_completa.php legisinc-app:/tmp/
docker cp /tmp/test_pdf.pdf legisinc-app:/tmp/
echo "🐳 Executando teste completo no container Laravel..."
docker exec legisinc-app php /tmp/teste_assinatura_completa.php

# 5. Limpeza
echo ""
echo "🧹 Limpando arquivos temporários..."
rm -f "$PFX_TESTE" /tmp/test_key.key /tmp/test_cert.pem /tmp/test_pdf.pdf
rm -f /tmp/teste_assinatura_completa.php

echo ""
echo "✅ TESTE COMPLETO FINALIZADO!"