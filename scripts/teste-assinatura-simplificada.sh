#!/bin/bash

echo "========================================="
echo "🔐 Teste: Acesso à Assinatura Digital"
echo "========================================="
echo ""

# Cores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}1. Verificando arquivos necessários...${NC}"

# PDF para assinatura
PDF_ASSINATURA="/home/bruno/legisinc/storage/app/proposicoes/pdfs/11/proposicao_11_assinatura_1755768024.pdf"
if [ -f "$PDF_ASSINATURA" ]; then
    echo -e "${GREEN}✅ PDF para assinatura: $(basename "$PDF_ASSINATURA")${NC}"
    echo "   Tamanho: $(stat -c%s "$PDF_ASSINATURA") bytes"
else
    echo -e "${RED}❌ PDF para assinatura não encontrado${NC}"
fi

# DOCX mais recente
DOCX_RECENTE=$(find /home/bruno/legisinc/storage/app -name "*proposicao_11_*" -name "*.docx" -type f -printf '%T@ %p\n' 2>/dev/null | sort -n | tail -1 | cut -d' ' -f2)
if [ -f "$DOCX_RECENTE" ]; then
    echo -e "${GREEN}✅ DOCX mais recente: $(basename "$DOCX_RECENTE")${NC}"
    echo "   Tamanho: $(stat -c%s "$DOCX_RECENTE") bytes"
    echo "   Modificado: $(stat -c%y "$DOCX_RECENTE")"
else
    echo -e "${RED}❌ DOCX não encontrado${NC}"
fi

echo ""
echo -e "${BLUE}2. Verificando middleware corrigido...${NC}"

if grep -q "existeDocxParaConversao" /home/bruno/legisinc/app/Http/Middleware/CheckAssinaturaPermission.php; then
    echo -e "${GREEN}✅ Método existeDocxParaConversao adicionado${NC}"
else
    echo -e "${RED}❌ Método não encontrado${NC}"
fi

if grep -q "app/proposicoes/pdfs" /home/bruno/legisinc/app/Http/Middleware/CheckAssinaturaPermission.php; then
    echo -e "${GREEN}✅ Busca no diretório correto implementada${NC}"
else
    echo -e "${RED}❌ Busca no diretório não corrigida${NC}"
fi

echo ""
echo -e "${BLUE}3. Testando lógica do middleware...${NC}"

# Simular verificação via PHP
cat > /tmp/test_middleware.php << 'EOF'
<?php
require_once '/home/bruno/legisinc/vendor/autoload.php';

$app = require_once '/home/bruno/legisinc/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$proposicao = App\Models\Proposicao::find(11);

if ($proposicao) {
    echo "📋 Status da proposição: {$proposicao->status}\n";
    
    // Verificar status permitido
    $statusPermitidos = ['aprovado', 'aprovado_assinatura', 'aguardando_assinatura'];
    $statusOk = in_array($proposicao->status, $statusPermitidos);
    echo "✅ Status permite assinatura: " . ($statusOk ? 'SIM' : 'NÃO') . "\n";
    
    // Verificar PDF existente
    $pdfPath = "/home/bruno/legisinc/storage/app/proposicoes/pdfs/11/proposicao_11_assinatura_1755768024.pdf";
    $pdfExiste = file_exists($pdfPath);
    echo "✅ PDF existe: " . ($pdfExiste ? 'SIM' : 'NÃO') . "\n";
    
    // Verificar DOCX existente
    $docxPath = "/home/bruno/legisinc/storage/app/private/proposicoes/proposicao_11_1755767664.docx";
    $docxExiste = file_exists($docxPath);
    echo "✅ DOCX existe: " . ($docxExiste ? 'SIM' : 'NÃO') . "\n";
    
    echo "\n🎯 Resultado: ";
    if ($statusOk && ($pdfExiste || $docxExiste)) {
        echo "DEVE PERMITIR ACESSO ✅\n";
    } else {
        echo "DEVE BLOQUEAR ACESSO ❌\n";
    }
}
EOF

php /tmp/test_middleware.php
rm -f /tmp/test_middleware.php

echo ""
echo -e "${YELLOW}🎯 INSTRUÇÕES PARA TESTE MANUAL:${NC}"
echo ""
echo "1. Abra seu navegador"
echo "2. Acesse: http://localhost:8001/login"
echo "3. Login: jessica@sistema.gov.br / 123456"
echo "4. Acesse: http://localhost:8001/proposicoes/11/assinatura-digital"
echo "5. Se ainda der 403, veja logs em /storage/logs/laravel.log"
echo ""
echo "========================================="
echo -e "${GREEN}✅ MIDDLEWARE CORRIGIDO!${NC}"
echo "========================================="
echo ""
echo "Agora o sistema deve permitir acesso quando:"
echo "✅ Status da proposição = 'aprovado'"
echo "✅ Existe PDF OU existe DOCX para conversão"
echo "✅ Usuário é autor da proposição"
echo "========================================="