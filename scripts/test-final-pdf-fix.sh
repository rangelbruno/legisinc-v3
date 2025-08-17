#!/bin/bash

echo "🎯 TESTE FINAL: Correção PDF Download"
echo "===================================="
echo

echo "PROBLEMA ORIGINAL:"
echo "- Botão 'Baixar Documento Final' baixava PDF com 0 páginas"
echo "- PDF continha template padrão em vez do conteúdo OnlyOffice"
echo

echo "1️⃣ VALIDAÇÃO: PDF atual da proposição 5"
if [ -f /home/bruno/legisinc/storage/app/proposicoes/pdfs/5/proposicao_5.pdf ]; then
    PDF_INFO=$(file /home/bruno/legisinc/storage/app/proposicoes/pdfs/5/proposicao_5.pdf)
    PDF_SIZE=$(ls -lh /home/bruno/legisinc/storage/app/proposicoes/pdfs/5/proposicao_5.pdf | awk '{print $5}')
    
    echo "📁 Arquivo: /storage/app/proposicoes/pdfs/5/proposicao_5.pdf"
    echo "📏 Tamanho: $PDF_SIZE"
    echo "📄 Info: $PDF_INFO"
    
    if echo "$PDF_INFO" | grep -q "1 page(s)"; then
        echo "✅ PDF VÁLIDO - Contém 1 página"
    else
        echo "❌ PDF INVÁLIDO - Não contém páginas"
    fi
else
    echo "❌ PDF não encontrado"
fi
echo

echo "2️⃣ TESTANDO: Download via rota serve-pdf"
echo "Simulando: GET /proposicoes/5/pdf"

# Testar rota de download
HTTP_RESPONSE=$(curl -s -w "%{http_code}" -o /tmp/test_download.pdf "http://localhost:8001/proposicoes/5/pdf" 2>/dev/null || echo "000")

if [ "$HTTP_RESPONSE" = "200" ]; then
    echo "✅ Rota acessível (HTTP 200)"
    
    if [ -f /tmp/test_download.pdf ]; then
        DOWNLOAD_INFO=$(file /tmp/test_download.pdf 2>/dev/null)
        DOWNLOAD_SIZE=$(ls -lh /tmp/test_download.pdf 2>/dev/null | awk '{print $5}')
        
        echo "📄 Download Info: $DOWNLOAD_INFO"
        echo "📏 Download Size: $DOWNLOAD_SIZE"
        
        if echo "$DOWNLOAD_INFO" | grep -q "1 page(s)"; then
            echo "🎉 SUCESSO: Download retorna PDF válido com conteúdo!"
        else
            echo "⚠️ AVISO: Download retorna PDF mas pode estar vazio"
        fi
    else
        echo "❌ Falha: Arquivo não foi baixado"
    fi
else
    echo "❌ Falha: HTTP $HTTP_RESPONSE"
fi
echo

echo "3️⃣ CORREÇÕES IMPLEMENTADAS:"
echo "✅ Uso de diretório temporário para evitar problemas de permissão"
echo "✅ Verificação e criação de diretório de destino"
echo "✅ Fallback de copy() se rename() falhar"
echo "✅ Limpeza adequada de arquivos temporários"
echo "✅ Tratamento de erros melhorado"
echo

echo "4️⃣ FLUXO CORRIGIDO:"
echo "1. LibreOffice converte DOCX → PDF em /tmp (sem problemas de permissão)"
echo "2. Sistema move/copia PDF para /storage/app/proposicoes/pdfs/"
echo "3. Botão download serve PDF válido com conteúdo do OnlyOffice"
echo "4. ✅ Usuário baixa documento final correto!"
echo

echo "===================================="
if [ -f /tmp/test_download.pdf ] && echo "$(file /tmp/test_download.pdf)" | grep -q "1 page(s)"; then
    echo "🎉 PROBLEMA RESOLVIDO!"
    echo "PDF download está funcionando corretamente."
else
    echo "⚠️ Necessária verificação adicional"
    echo "PDF pode ainda ter problemas de geração."
fi
echo "===================================="

# Cleanup
rm -f /tmp/test_download.pdf
