#!/bin/bash

echo "🧪 TESTE FINAL: PDF de Assinatura com Arquivo Editado pelo Legislativo"
echo "======================================================================"

echo "📋 1. Verificando dados da proposição 1:"
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(1);
echo 'ID: ' . \$proposicao->id . PHP_EOL;
echo 'Status: ' . \$proposicao->status . PHP_EOL;
echo 'Arquivo Path: ' . \$proposicao->arquivo_path . PHP_EOL;
echo 'Arquivo existe: ' . (file_exists(storage_path('app/' . \$proposicao->arquivo_path)) ? 'SIM' : 'NÃO') . PHP_EOL;
"

echo ""
echo "📁 2. Verificando conteúdo do arquivo editado:"
ARQUIVO_PATH="/home/bruno/legisinc/storage/app/private/proposicoes/proposicao_1_1755395857.docx"
if [ -f "$ARQUIVO_PATH" ]; then
    echo "✅ Arquivo encontrado: $ARQUIVO_PATH"
    echo "📏 Tamanho: $(stat --format=%s "$ARQUIVO_PATH") bytes"
    echo "📅 Modificado: $(stat --format=%y "$ARQUIVO_PATH")"
else
    echo "❌ Arquivo não encontrado: $ARQUIVO_PATH"
fi

echo ""
echo "🎯 3. Testando geração de PDF para assinatura:"

# Fazer request para forçar geração do PDF
echo "Fazendo request para /proposicoes/1/assinar..."

# Simular login e acesso
curl -s \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8" \
  -H "User-Agent: Mozilla/5.0 (compatible; TestScript/1.0)" \
  -X GET \
  "http://localhost:8001/proposicoes/1/assinar" \
  -o /tmp/response.html \
  --connect-timeout 30 \
  --max-time 60

# Verificar resposta
if [ $? -eq 0 ]; then
    STATUS=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8001/proposicoes/1/assinar")
    echo "Status HTTP: $STATUS"
    
    if [ "$STATUS" = "200" ]; then
        echo "✅ Página carregou com sucesso"
    elif [ "$STATUS" = "302" ]; then
        echo "⚠️  Redirecionamento (possivelmente para login)"
    else
        echo "❌ Erro no carregamento: Status $STATUS"
    fi
else
    echo "❌ Erro na conexão HTTP"
fi

echo ""
echo "📋 4. Verificando logs do Laravel:"
LOG_FILE="/home/bruno/legisinc/storage/logs/laravel.log"
if [ -f "$LOG_FILE" ]; then
    echo "Últimas entradas relacionadas a PDF:"
    tail -20 "$LOG_FILE" | grep -E "(PDF Assinatura|Arquivo encontrado|ARQUIVO NÃO ENCONTRADO)" || echo "Nenhum log específico nos últimos 20 registros"
else
    echo "❌ Arquivo de log não encontrado"
fi

echo ""
echo "🎯 5. Verificando se PDF foi gerado:"
PDF_PATH="/home/bruno/legisinc/storage/app/proposicoes/pdfs/1/proposicao_1.pdf"
if [ -f "$PDF_PATH" ]; then
    echo "✅ PDF gerado: $PDF_PATH"
    echo "📏 Tamanho: $(stat --format=%s "$PDF_PATH") bytes"
    echo "📅 Criado: $(stat --format=%y "$PDF_PATH")"
    
    # Verificar se PDF contém texto esperado
    if command -v pdftotext >/dev/null 2>&1; then
        echo "🔍 Extraindo texto do PDF para verificação:"
        pdftotext "$PDF_PATH" /tmp/pdf_content.txt 2>/dev/null
        if [ -f /tmp/pdf_content.txt ]; then
            echo "📄 Primeiras linhas do PDF:"
            head -10 /tmp/pdf_content.txt
            rm -f /tmp/pdf_content.txt
        fi
    fi
else
    echo "⚠️  PDF não encontrado em: $PDF_PATH"
    echo "Verificando outros locais:"
    find /home/bruno/legisinc/storage -name "*proposicao_1*.pdf" -type f -newer /tmp -exec ls -la {} \; 2>/dev/null | head -5
fi

echo ""
echo "✅ TESTE CONCLUÍDO!"
echo ""
echo "🔧 DIAGNÓSTICO:"
echo "- Se PDF foi gerado recentemente: Correção funcionou ✅"
echo "- Se arquivo editado existe mas PDF não reflete: Verificar extração de conteúdo"
echo "- Logs devem mostrar 'Arquivo encontrado' para confirmar uso do arquivo editado"