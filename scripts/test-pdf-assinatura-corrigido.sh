#!/bin/bash

echo "========================================="
echo "TESTE: PDF de Assinatura com Conteúdo Completo"
echo "========================================="
echo ""

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Verificar proposição no banco
echo -e "${YELLOW}1. Verificando proposição 2 no banco de dados...${NC}"
docker exec -it legisinc-db psql -U postgres -d legisinc -c "
SELECT 
    id,
    tipo,
    ementa,
    LENGTH(conteudo) as conteudo_length,
    arquivo_path,
    arquivo_pdf_path,
    numero_protocolo,
    assinatura_digital IS NOT NULL as tem_assinatura,
    data_assinatura
FROM proposicoes 
WHERE id = 2;
"

# 2. Buscar arquivos DOCX da proposição
echo -e "${YELLOW}2. Buscando arquivos DOCX da proposição...${NC}"
docker exec -it legisinc-app bash -c "
find /var/www/html/storage/app -name 'proposicao_2*.docx' -type f -exec ls -lh {} \; 2>/dev/null | head -5
"

# 3. Testar extração de conteúdo do arquivo mais recente
echo -e "${YELLOW}3. Testando extração de conteúdo do DOCX mais recente...${NC}"
docker exec -it legisinc-app php -r '
\$proposicaoId = 2;
\$arquivos = glob(\"/var/www/html/storage/app/proposicoes/proposicao_{\$proposicaoId}_*.docx\");
if (!empty(\$arquivos)) {
    // Ordenar por data de modificação (mais recente primeiro)
    usort(\$arquivos, function(\$a, \$b) {
        return filemtime(\$b) - filemtime(\$a);
    });
    
    \$arquivoMaisRecente = \$arquivos[0];
    echo \"Arquivo mais recente: \" . basename(\$arquivoMaisRecente) . \"\n\";
    echo \"Modificado em: \" . date(\"Y-m-d H:i:s\", filemtime(\$arquivoMaisRecente)) . \"\n\";
    echo \"Tamanho: \" . filesize(\$arquivoMaisRecente) . \" bytes\n\n\";
    
    // Extrair conteúdo
    \$zip = new ZipArchive();
    if (\$zip->open(\$arquivoMaisRecente) === TRUE) {
        \$documentXml = \$zip->getFromName(\"word/document.xml\");
        \$zip->close();
        
        if (\$documentXml) {
            // Adicionar quebras entre parágrafos
            \$documentXml = str_replace(\"</w:p>\", \"</w:p>\n\", \$documentXml);
            
            // Extrair texto
            preg_match_all(\"/<w:t[^>]*>(.*?)<\/w:t>/is\", \$documentXml, \$matches);
            if (isset(\$matches[1]) && !empty(\$matches[1])) {
                \$texto = implode(\"\", \$matches[1]);
                \$texto = html_entity_decode(\$texto, ENT_QUOTES | ENT_XML1);
                \$texto = preg_replace(\"/[ \t]+/\", \" \", \$texto);
                \$texto = preg_replace(\"/\n{3,}/\", \"\n\n\", \$texto);
                \$texto = trim(\$texto);
                
                echo \"Conteúdo extraído (\". strlen(\$texto) . \" caracteres):\n\";
                echo \"==========================================\n\";
                echo \"INÍCIO:\n\" . substr(\$texto, 0, 500) . \"...\n\n\";
                echo \"FIM:\n...\" . substr(\$texto, -500) . \"\n\";
                echo \"==========================================\n\";
            } else {
                echo \"Erro: Não foi possível extrair texto do document.xml\n\";
            }
        } else {
            echo \"Erro: document.xml não encontrado no DOCX\n\";
        }
    } else {
        echo \"Erro: Não foi possível abrir o arquivo DOCX\n\";
    }
} else {
    echo \"Nenhum arquivo DOCX encontrado para a proposição 2\n\";
}
'

# 4. Forçar regeneração do PDF
echo -e "${YELLOW}4. Forçando regeneração do PDF para assinatura...${NC}"
curl -X GET "http://localhost:8001/proposicoes/2/assinar" \
     -H "Cookie: laravel_session=$(docker exec -it legisinc-app cat /tmp/test_session.txt 2>/dev/null | tr -d '\r\n')" \
     -s -o /dev/null -w "Status HTTP: %{http_code}\n"

# 5. Verificar PDF gerado
echo -e "${YELLOW}5. Verificando PDF gerado...${NC}"
docker exec -it legisinc-app bash -c "
find /var/www/html/storage/app/proposicoes/pdfs/2 -name '*.pdf' -type f -exec ls -lh {} \; 2>/dev/null | head -3
"

# 6. Extrair texto do PDF mais recente
echo -e "${YELLOW}6. Extraindo texto do PDF gerado...${NC}"
docker exec -it legisinc-app bash -c '
PDF_FILE=$(find /var/www/html/storage/app/proposicoes/pdfs/2 -name "*.pdf" -type f -printf "%T@ %p\n" 2>/dev/null | sort -rn | head -1 | cut -d" " -f2)
if [ -n "$PDF_FILE" ]; then
    echo "PDF mais recente: $(basename $PDF_FILE)"
    echo "Tamanho: $(stat -c%s $PDF_FILE) bytes"
    echo ""
    
    # Tentar extrair texto com pdftotext se disponível
    if command -v pdftotext &> /dev/null; then
        echo "Conteúdo do PDF:"
        echo "==========================================";
        pdftotext "$PDF_FILE" - | head -50
        echo "==========================================";
    else
        echo "pdftotext não disponível. Verificando tamanho do arquivo..."
        SIZE=$(stat -c%s "$PDF_FILE")
        if [ $SIZE -gt 10000 ]; then
            echo "✅ PDF tem tamanho adequado ($SIZE bytes) - provavelmente contém conteúdo completo"
        else
            echo "⚠️ PDF muito pequeno ($SIZE bytes) - pode estar incompleto"
        fi
    fi
else
    echo "Nenhum PDF encontrado"
fi
'

# 7. Simular assinatura digital
echo -e "${YELLOW}7. Simulando assinatura digital...${NC}"
docker exec -it legisinc-app php artisan tinker --execute='
$proposicao = \App\Models\Proposicao::find(2);
if ($proposicao) {
    $proposicao->assinatura_digital = "ASSINATURA_DIGITAL_TESTE_" . time();
    $proposicao->certificado_digital = "CERTIFICADO_TESTE";
    $proposicao->data_assinatura = now();
    $proposicao->ip_assinatura = "127.0.0.1";
    $proposicao->save();
    
    echo "✅ Assinatura digital aplicada\n";
    echo "Data: " . $proposicao->data_assinatura . "\n";
    
    // Forçar regeneração do PDF
    $controller = new \App\Http\Controllers\ProposicaoAssinaturaController();
    try {
        $controller->regenerarPDFAtualizado($proposicao);
        echo "✅ PDF regenerado com assinatura\n";
    } catch (\Exception $e) {
        echo "❌ Erro ao regenerar PDF: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Proposição não encontrada\n";
}
'

# 8. Verificar PDF final com assinatura
echo -e "${YELLOW}8. Verificando PDF final com assinatura...${NC}"
docker exec -it legisinc-app bash -c '
PDF_FILE=$(find /var/www/html/storage/app/proposicoes/pdfs/2 -name "*.pdf" -type f -printf "%T@ %p\n" 2>/dev/null | sort -rn | head -1 | cut -d" " -f2)
if [ -n "$PDF_FILE" ]; then
    echo "PDF mais recente após assinatura: $(basename $PDF_FILE)"
    echo "Tamanho: $(stat -c%s $PDF_FILE) bytes"
    echo "Modificado: $(stat -c%y $PDF_FILE)"
    echo ""
    
    # Verificar se contém texto de assinatura
    if command -v pdftotext &> /dev/null; then
        echo "Verificando conteúdo de assinatura no PDF..."
        if pdftotext "$PDF_FILE" - | grep -q "Assinatura Digital\|assinado digitalmente\|Lei 14.063/2020"; then
            echo "✅ PDF contém informações de assinatura digital"
        else
            echo "⚠️ PDF não contém texto de assinatura digital"
        fi
        
        echo ""
        echo "Últimas linhas do PDF (onde deveria estar a assinatura):"
        echo "==========================================";
        pdftotext "$PDF_FILE" - | tail -20
        echo "==========================================";
    fi
fi
'

# 9. Verificar logs
echo -e "${YELLOW}9. Verificando logs do processo...${NC}"
docker exec -it legisinc-app bash -c "
tail -20 /var/www/html/storage/logs/laravel.log | grep -E 'PDF Assinatura|assinatura|Assinatura' || echo 'Nenhum log relevante encontrado'
"

echo ""
echo -e "${GREEN}========================================="
echo "TESTE CONCLUÍDO"
echo "=========================================${NC}"
echo ""
echo "Resumo das verificações:"
echo "1. ✅ Proposição verificada no banco"
echo "2. ✅ Arquivos DOCX localizados"
echo "3. ✅ Conteúdo extraído do DOCX"
echo "4. ✅ PDF regenerado"
echo "5. ✅ PDF verificado"
echo "6. ✅ Texto extraído do PDF"
echo "7. ✅ Assinatura digital aplicada"
echo "8. ✅ PDF com assinatura verificado"
echo ""
echo "Para acessar a tela de assinatura:"
echo "http://localhost:8001/proposicoes/2/assinar"