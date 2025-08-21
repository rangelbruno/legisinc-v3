#!/bin/bash

echo "🔍 DIAGNÓSTICO COMPLETO: PDF de Assinatura da Proposição 8"
echo "=================================================="

# 1. Verificar se arquivo DOCX existe e está acessível
echo ""
echo "1. ARQUIVO DOCX ORIGINAL (editado pelo OnlyOffice)"
echo "---------------------------------------------------"
DOCX_PATH="/home/bruno/legisinc/storage/app/private/proposicoes/proposicao_8_1755736247.docx"
if [ -f "$DOCX_PATH" ]; then
    echo "✅ Arquivo DOCX encontrado: $DOCX_PATH"
    echo "   📏 Tamanho: $(stat -c%s "$DOCX_PATH") bytes"
    echo "   📅 Modificado: $(stat -c%y "$DOCX_PATH")"
    
    # Extrair texto simples do DOCX para verificar conteúdo
    echo ""
    echo "📄 Conteúdo do DOCX (primeiros 500 caracteres):"
    echo "------------------------------------------------"
    if command -v unzip &> /dev/null && command -v xml2 &> /dev/null; then
        unzip -q -o "$DOCX_PATH" word/document.xml -d /tmp/
        if [ -f "/tmp/word/document.xml" ]; then
            # Extrair texto XML e limpar tags
            cat /tmp/word/document.xml | sed 's/<[^>]*>//g' | head -c 500
            echo ""
            rm -f /tmp/word/document.xml
        else
            echo "❌ Erro: Não foi possível extrair document.xml do DOCX"
        fi
    elif command -v unzip &> /dev/null; then
        # Método alternativo sem xml2
        unzip -q -o "$DOCX_PATH" word/document.xml -d /tmp/
        if [ -f "/tmp/word/document.xml" ]; then
            # Extrair texto básico removendo tags XML
            grep -o '>[^<]*<' /tmp/word/document.xml | sed 's/[><]//g' | head -c 500
            echo ""
            rm -f /tmp/word/document.xml
        else
            echo "❌ Erro: Não foi possível extrair document.xml do DOCX"
        fi
    else
        echo "⚠️  unzip não disponível - não é possível extrair conteúdo do DOCX"
    fi
else
    echo "❌ Arquivo DOCX NÃO encontrado: $DOCX_PATH"
fi

# 2. Verificar PDFs gerados
echo ""
echo "2. PDFs GERADOS"
echo "---------------"

# PDF de assinatura
PDF_ASSINATURA="/home/bruno/legisinc/storage/app/proposicoes/pdfs/8/proposicao_8_assinatura_1755736420.pdf"
if [ -f "$PDF_ASSINATURA" ]; then
    echo "✅ PDF de Assinatura: $PDF_ASSINATURA"
    echo "   📏 Tamanho: $(stat -c%s "$PDF_ASSINATURA") bytes"
    echo "   📅 Criado: $(stat -c%y "$PDF_ASSINATURA")"
else
    echo "❌ PDF de Assinatura NÃO encontrado: $PDF_ASSINATURA"
fi

# PDF do OnlyOffice
PDF_ONLYOFFICE="/home/bruno/legisinc/storage/app/private/proposicoes/pdfs/8/proposicao_8_onlyoffice_1755736422.pdf"
if [ -f "$PDF_ONLYOFFICE" ]; then
    echo "✅ PDF OnlyOffice: $PDF_ONLYOFFICE"
    echo "   📏 Tamanho: $(stat -c%s "$PDF_ONLYOFFICE") bytes"
    echo "   📅 Criado: $(stat -c%y "$PDF_ONLYOFFICE")"
else
    echo "❌ PDF OnlyOffice NÃO encontrado: $PDF_ONLYOFFICE"
fi

# 3. Testar extração de texto dos PDFs
echo ""
echo "3. CONTEÚDO DOS PDFs (usando pdftotext se disponível)"
echo "----------------------------------------------------"

if command -v pdftotext &> /dev/null; then
    if [ -f "$PDF_ASSINATURA" ]; then
        echo ""
        echo "📄 Conteúdo do PDF de Assinatura (primeiros 500 chars):"
        echo "-------------------------------------------------------"
        pdftotext "$PDF_ASSINATURA" - | head -c 500
        echo ""
    fi
    
    if [ -f "$PDF_ONLYOFFICE" ]; then
        echo ""
        echo "📄 Conteúdo do PDF OnlyOffice (primeiros 500 chars):"
        echo "----------------------------------------------------"
        pdftotext "$PDF_ONLYOFFICE" - | head -c 500
        echo ""
    fi
else
    echo "⚠️  pdftotext não disponível - instalar com: sudo apt install poppler-utils"
fi

# 4. Verificar logs recentes relacionados à proposição 8
echo ""
echo "4. LOGS RECENTES (últimas 20 linhas relacionadas à proposição 8)"
echo "----------------------------------------------------------------"
if [ -f "/home/bruno/legisinc/storage/logs/laravel.log" ]; then
    grep "proposicao.*8\|Proposição.*8" /home/bruno/legisinc/storage/logs/laravel.log | tail -20
else
    echo "❌ Arquivo de log não encontrado"
fi

# 5. Verificar se OnlyOffice está rodando
echo ""
echo "5. STATUS DO ONLYOFFICE"
echo "----------------------"
if docker ps | grep -q onlyoffice; then
    echo "✅ Container OnlyOffice está rodando:"
    docker ps | grep onlyoffice
else
    echo "❌ Container OnlyOffice NÃO está rodando"
fi

# 6. Verificar configurações de PDF do Laravel
echo ""
echo "6. CONFIGURAÇÃO DOMPDF (Laravel)"
echo "--------------------------------"
if [ -f "/home/bruno/legisinc/config/dompdf.php" ]; then
    echo "✅ Arquivo de configuração DomPDF encontrado"
    echo "Configurações principais:"
    grep -E "default_font|enable_remote|enable_font_subsetting" /home/bruno/legisinc/config/dompdf.php
else
    echo "⚠️  Arquivo config/dompdf.php não encontrado"
fi

echo ""
echo "=================================================="
echo "🎯 DIAGNÓSTICO CONCLUÍDO"
echo ""

# Resumo das descobertas
echo "📋 RESUMO:"
echo "----------"
if [ -f "$DOCX_PATH" ]; then
    echo "✅ Arquivo DOCX original existe e tem $(stat -c%s "$DOCX_PATH") bytes"
else
    echo "❌ Arquivo DOCX original NÃO EXISTE - PROBLEMA CRÍTICO"
fi

if [ -f "$PDF_ASSINATURA" ]; then
    SIZE_PDF=$(stat -c%s "$PDF_ASSINATURA")
    if [ "$SIZE_PDF" -gt 1000 ]; then
        echo "✅ PDF de assinatura existe e tem tamanho adequado ($SIZE_PDF bytes)"
    else
        echo "⚠️  PDF de assinatura muito pequeno ($SIZE_PDF bytes) - pode estar vazio"
    fi
else
    echo "❌ PDF de assinatura NÃO EXISTE"
fi

echo ""
echo "🔧 PRÓXIMOS PASSOS:"
echo "-------------------"
echo "1. Se DOCX existe mas PDF tem problemas → Verificar ProposicaoAssinaturaController"
echo "2. Se DOCX não existe → Verificar OnlyOffice callback e salvamento"
echo "3. Se ambos existem → Verificar se controller está usando arquivo correto"
echo "4. Verificar se há problema na conversão DOCX → PDF"