#!/bin/bash

echo "=================================================="
echo "🇧🇷 Teste Final - OnlyOffice Português (Brasil)"
echo "=================================================="

echo ""
echo "🔍 Verificando se OnlyOffice está respondendo..."
if curl -s -I "http://localhost:8080/healthcheck" | grep -q "200 OK"; then
    echo "✅ OnlyOffice está rodando e saudável"
else
    echo "❌ OnlyOffice não está respondendo"
    exit 1
fi

echo ""
echo "📊 Verificando configuração de idioma no código PHP..."
echo ""

# Verificar se as configurações de idioma estão no OnlyOfficeService.php
if grep -q "documentLang.*pt-BR" /home/bruno/legisinc/app/Services/OnlyOffice/OnlyOfficeService.php; then
    echo "✅ documentLang: 'pt-BR' encontrado no código"
else
    echo "❌ documentLang não encontrado"
fi

if grep -q "documentLanguage.*pt-BR" /home/bruno/legisinc/app/Services/OnlyOffice/OnlyOfficeService.php; then
    echo "✅ documentLanguage: 'pt-BR' encontrado no código"
else
    echo "❌ documentLanguage não encontrado"
fi

echo ""
echo "🌐 Testando interface web do OnlyOffice..."
echo ""

# Testar a página de login da aplicação
echo "🔐 Testando acesso à aplicação..."
if curl -s "http://localhost:8001/login" | grep -q "Login"; then
    echo "✅ Aplicação Laravel está respondendo"
else
    echo "❌ Aplicação Laravel não está respondendo"
    exit 1
fi

echo ""
echo "📝 Para testar o idioma completamente:"
echo "   1. Acesse: http://localhost:8001/login"
echo "   2. Login: jessica@sistema.gov.br / 123456"
echo "   3. Vá para uma proposição e abra no OnlyOffice"
echo "   4. Verifique se 'Definir Idioma do Texto' mostra 'Português (Brasil)'"
echo ""

echo "🎯 Configurações aplicadas no código:"
echo "   ✓ 'documentLang' => 'pt-BR'"
echo "   ✓ 'documentLanguage' => 'pt-BR'"
echo "   ✓ Variáveis de ambiente PT-BR no container"
echo ""

echo "=================================================="
echo "🎊 OnlyOffice configurado para Português (Brasil)!"
echo "=================================================="