#!/bin/bash

echo "=== Teste de Correção do PDF de Assinatura ==="

# Testar proposição 6 que deve estar com status aprovado_assinatura
echo "Acessando /proposicoes/6/assinar para testar PDF sem seções de assinatura..."

curl -s -c /tmp/cookies.txt \
     -d "email=jessica@sistema.gov.br" \
     -d "password=123456" \
     -H "Content-Type: application/x-www-form-urlencoded" \
     -H "X-Requested-With: XMLHttpRequest" \
     -H "Accept: application/json" \
     "http://localhost:8001/login"

# Acessar a tela de assinatura que deve gerar o PDF
echo "Acessando tela de assinatura..."
curl -s -b /tmp/cookies.txt \
     -H "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36" \
     "http://localhost:8001/proposicoes/6/assinar" > /tmp/assinar_response.html

# Verificar se teve sucesso
if grep -q "proposicoes.pdf.template" /tmp/assinar_response.html; then
    echo "✅ Página de assinatura carregada com sucesso"
else
    echo "❌ Erro na página de assinatura"
    head -20 /tmp/assinar_response.html
fi

# Verificar se o PDF foi regenerado
if [ -f "/home/bruno/legisinc/storage/app/proposicoes/pdfs/6/proposicao_6.pdf" ]; then
    echo "✅ PDF regenerado com sucesso"
    
    # Extrair texto do PDF para verificar se não contém seções de assinatura
    if command -v pdftotext >/dev/null 2>&1; then
        pdftotext "/home/bruno/legisinc/storage/app/proposicoes/pdfs/6/proposicao_6.pdf" /tmp/pdf_content.txt
        
        echo ""
        echo "=== Verificando conteúdo do PDF ==="
        
        if grep -q "ASSINATURA DIGITAL" /tmp/pdf_content.txt; then
            echo "❌ ERRO: PDF ainda contém seções de assinatura"
            echo "Conteúdo encontrado:"
            grep -A5 -B5 "ASSINATURA DIGITAL" /tmp/pdf_content.txt
        else
            echo "✅ SUCESSO: PDF não contém seções de assinatura desnecessárias"
        fi
        
        echo ""
        echo "=== Conteúdo do PDF (primeiras 20 linhas) ==="
        head -20 /tmp/pdf_content.txt
        
    else
        echo "⚠️  pdftotext não disponível, verificação de conteúdo saltada"
    fi
    
    echo ""
    echo "Tamanho do PDF: $(ls -lh /home/bruno/legisinc/storage/app/proposicoes/pdfs/6/proposicao_6.pdf | awk '{print $5}')"
else
    echo "❌ PDF não foi gerado"
fi

# Limpeza
rm -f /tmp/cookies.txt /tmp/assinar_response.html /tmp/pdf_content.txt

echo ""
echo "=== Teste Concluído ==="