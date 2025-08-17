#!/bin/bash

echo "=== TESTE: Debug Geração PDF Proposição 5 ==="
echo

# 1. Verificar estado atual
echo "1️⃣ ESTADO ATUAL:"
echo "Proposição 5:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, status, arquivo_path, arquivo_pdf_path FROM proposicoes WHERE id = 5;"
echo

echo "PDF atual (com problema):"
ls -la /home/bruno/legisinc/storage/app/proposicoes/pdfs/5/proposicao_5.pdf
file /home/bruno/legisinc/storage/app/proposicoes/pdfs/5/proposicao_5.pdf
echo

echo "Arquivo DOCX fonte:"
ls -la /home/bruno/legisinc/storage/app/private/proposicoes/proposicao_5_1755375870.docx
echo

# 2. Testar conversão manual
echo "2️⃣ TESTE CONVERSÃO MANUAL:"
echo "Executando LibreOffice..."

# Criar diretório temporário
docker exec legisinc-app mkdir -p /tmp/pdf-test

# Copiar arquivo para local conhecido
docker exec legisinc-app cp /var/www/html/storage/app/private/proposicoes/proposicao_5_1755375870.docx /tmp/pdf-test/source.docx

# Conversão manual
docker exec legisinc-app libreoffice --headless --invisible --nodefault --nolockcheck --nologo --norestore --convert-to pdf --outdir /tmp/pdf-test /tmp/pdf-test/source.docx

echo "Resultado da conversão:"
docker exec legisinc-app ls -la /tmp/pdf-test/
docker exec legisinc-app file /tmp/pdf-test/source.pdf 2>/dev/null || echo "PDF não gerado"
echo

# 3. Simular o mesmo comando usado pelo sistema
echo "3️⃣ SIMULANDO COMANDO DO SISTEMA:"

# Usar mesmo comando que o sistema usa
TEMP_FILE="/tmp/pdf-test/proposicao_5_temp.docx"
docker exec legisinc-app cp /var/www/html/storage/app/private/proposicoes/proposicao_5_1755375870.docx $TEMP_FILE

OUTPUT_DIR="/tmp/pdf-test"
COMMAND="libreoffice --headless --invisible --nodefault --nolockcheck --nologo --norestore --convert-to pdf --outdir $OUTPUT_DIR $TEMP_FILE"

echo "Comando: $COMMAND"
docker exec legisinc-app sh -c "$COMMAND"
echo "Return code: $?"

echo "Arquivos gerados:"
docker exec legisinc-app ls -la /tmp/pdf-test/
echo

# 4. Comparar com sistema existente
echo "4️⃣ COMPARAÇÃO:"
echo "PDF gerado agora:"
docker exec legisinc-app file /tmp/pdf-test/*.pdf 2>/dev/null | head -1

echo "PDF do sistema (com problema):"
file /home/bruno/legisinc/storage/app/proposicoes/pdfs/5/proposicao_5.pdf

echo
echo "=== DIAGNÓSTICO ==="
if docker exec legisinc-app ls /tmp/pdf-test/*.pdf >/dev/null 2>&1; then
    echo "✅ LibreOffice funcionando - problema está na implementação do sistema"
    echo "🔍 Investigar: caminhos de arquivo, permissões, ou lógica de busca"
else
    echo "❌ LibreOffice falhando - problema de configuração ou dependências"
fi

# Cleanup
docker exec legisinc-app rm -rf /tmp/pdf-test