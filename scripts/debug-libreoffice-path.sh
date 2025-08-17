#!/bin/bash

echo "=== DEBUG: Problema LibreOffice Path ==="
echo

# Simular exatamente o que o sistema faz
PROPOSICAO_ID=5
CAMINHO_PDF_ABSOLUTO="/var/www/html/storage/app/proposicoes/pdfs/$PROPOSICAO_ID/proposicao_$PROPOSICAO_ID.pdf"
TEMP_DIR=$(dirname "$CAMINHO_PDF_ABSOLUTO")
TEMP_FILE="$TEMP_DIR/proposicao_${PROPOSICAO_ID}_temp.docx"
EXPECTED_PDF_PATH="$TEMP_DIR/proposicao_${PROPOSICAO_ID}_temp.pdf"

echo "1️⃣ VARIÁVEIS DO SISTEMA:"
echo "CAMINHO_PDF_ABSOLUTO: $CAMINHO_PDF_ABSOLUTO"
echo "TEMP_DIR (outdir): $TEMP_DIR"
echo "TEMP_FILE: $TEMP_FILE"
echo "EXPECTED_PDF_PATH: $EXPECTED_PDF_PATH"
echo

echo "2️⃣ VERIFICANDO DIRETÓRIOS:"
docker exec legisinc-app ls -la "$(dirname "$CAMINHO_PDF_ABSOLUTO")"
echo

echo "3️⃣ SIMULANDO PROCESSO COMPLETO:"

# Criar arquivo temporário (como faz o sistema)
echo "Copiando arquivo para temp..."
docker exec legisinc-app cp /var/www/html/storage/app/private/proposicoes/proposicao_5_1755375870.docx "$TEMP_FILE"

echo "Verificando arquivo temp criado:"
docker exec legisinc-app ls -la "$TEMP_FILE"

# Executar comando (como faz o sistema)
echo "Executando comando LibreOffice:"
COMANDO="libreoffice --headless --invisible --nodefault --nolockcheck --nologo --norestore --convert-to pdf --outdir $TEMP_DIR $TEMP_FILE"
echo "COMANDO: $COMANDO"

docker exec legisinc-app sh -c "$COMANDO"
RETURN_CODE=$?
echo "RETURN CODE: $RETURN_CODE"

echo "4️⃣ VERIFICANDO RESULTADO:"
echo "Arquivos no diretório destino:"
docker exec legisinc-app ls -la "$TEMP_DIR"

echo "Arquivo esperado existe?"
if docker exec legisinc-app test -f "$EXPECTED_PDF_PATH"; then
    echo "✅ SIM: $EXPECTED_PDF_PATH"
    docker exec legisinc-app file "$EXPECTED_PDF_PATH"
else
    echo "❌ NÃO: $EXPECTED_PDF_PATH"
    echo "Procurando PDFs no diretório:"
    docker exec legisinc-app find "$TEMP_DIR" -name "*.pdf" 2>/dev/null || echo "Nenhum PDF encontrado"
fi

echo
echo "5️⃣ DIAGNÓSTICO:"
if [ $RETURN_CODE -eq 0 ]; then
    if docker exec legisinc-app test -f "$EXPECTED_PDF_PATH"; then
        echo "✅ LibreOffice funcionando perfeitamente"
        echo "✅ Arquivo gerado no local esperado"
        echo "🤔 Problema deve estar em outro lugar (permissões, rename, etc.)"
    else
        echo "⚠️ LibreOffice executou sem erro mas arquivo não está onde esperado"
        echo "🔍 Verificar se LibreOffice está gerando em outro local"
    fi
else
    echo "❌ LibreOffice falhou (return code: $RETURN_CODE)"
fi

# Cleanup
docker exec legisinc-app rm -f "$TEMP_FILE" "$EXPECTED_PDF_PATH"