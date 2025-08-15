#!/bin/bash

echo "=== Testando regeneração de PDF com correções RTF ==="

# Atualizar arquivo_path da proposição 2 para o arquivo mais recente
echo "Atualizando arquivo_path da proposição 2..."
docker exec legisinc-postgres psql -U postgres -d legisinc -c "UPDATE proposicoes SET arquivo_path = 'private/proposicoes/proposicao_2_1755220561.rtf' WHERE id = 2;"

echo ""
echo "Verificando status da proposição 2..."
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, status, arquivo_path, arquivo_pdf_path FROM proposicoes WHERE id = 2;"

echo ""
echo "Tentando acessar o PDF via curl para forçar regeneração..."
curl -s -o /tmp/test_pdf.pdf "http://localhost:8001/proposicoes/2/pdf" \
  -H "Cookie: laravel_session=test; XSRF-TOKEN=test" \
  -w "HTTP Status: %{http_code}\nContent-Type: %{content_type}\nFile Size: %{size_download} bytes\n"

echo ""
echo "Verificando se PDF foi regenerado..."
if [ -f "/home/bruno/legisinc/storage/app/proposicoes/pdfs/2/proposicao_2.pdf" ]; then
    echo "✅ PDF foi regenerado!"
    echo "Tamanho: $(stat -c%s /home/bruno/legisinc/storage/app/proposicoes/pdfs/2/proposicao_2.pdf) bytes"
    
    echo ""
    echo "Extraindo texto do PDF para verificar se códigos RTF foram removidos..."
    if command -v pdftotext &> /dev/null; then
        pdftotext /home/bruno/legisinc/storage/app/proposicoes/pdfs/2/proposicao_2.pdf - | head -20
    else
        echo "pdftotext não está disponível, instalando..."
        sudo apt update && sudo apt install -y poppler-utils
        pdftotext /home/bruno/legisinc/storage/app/proposicoes/pdfs/2/proposicao_2.pdf - | head -20
    fi
else
    echo "❌ PDF não foi regenerado."
    echo "Verificando logs de erro..."
    docker logs legisinc-app | tail -10
fi

echo ""
echo "Verificando banco de dados após regeneração..."
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, arquivo_path, arquivo_pdf_path FROM proposicoes WHERE id = 2;"