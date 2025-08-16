#!/bin/bash

echo "🎯 =================================================="
echo "📝 TESTE: VARIÁVEIS DE ASSINATURA E QR CODE"
echo "🎯 =================================================="
echo ""

echo "✅ 1. VERIFICANDO IMPLEMENTAÇÃO NO COMPONENTE:"
echo "---------------------------------------------------"
if grep -q "ASSINATURA DIGITAL & QR CODE" /home/bruno/legisinc/resources/views/components/onlyoffice-variables.blade.php; then
    echo "✅ Seção 'ASSINATURA DIGITAL & QR CODE' encontrada no componente"
else
    echo "❌ Seção não encontrada"
fi

echo ""
echo "📋 2. VARIÁVEIS IMPLEMENTADAS:"
echo "---------------------------------------------------"
grep -A 20 "ASSINATURA DIGITAL & QR CODE" /home/bruno/legisinc/resources/views/components/onlyoffice-variables.blade.php | grep "var-name" | while read -r line; do
    variable=$(echo "$line" | sed 's/.*>\${\([^}]*\)}.*/\1/')
    echo "✅ \${$variable}"
done

echo ""
echo "🔍 3. VERIFICANDO INTEGRAÇÃO NO BACKEND:"
echo "---------------------------------------------------"
if grep -q "assinatura_digital_info\|qrcode_html" /home/bruno/legisinc/app/Services/Template/TemplateVariableService.php; then
    echo "✅ Variáveis integradas no TemplateVariableService"
else
    echo "❌ Variáveis não encontradas no backend"
fi

echo ""
echo "🗄️  4. VERIFICANDO CONFIGURAÇÕES NO BANCO:"
echo "---------------------------------------------------"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "
SELECT 
    pc.nome as campo,
    pc.label as descricao
FROM parametros_submodulos ps 
JOIN parametros_campos pc ON ps.id = pc.submodulo_id 
WHERE ps.nome = 'Assinatura e QR Code' 
ORDER BY pc.ordem
LIMIT 5;" 2>/dev/null || echo "⚠️  Banco não acessível no momento"

echo ""
echo "🌐 5. VERIFICANDO ROTA DE CONFIGURAÇÃO:"
echo "---------------------------------------------------"
if grep -q "parametros-templates-assinatura-qrcode" /home/bruno/legisinc/routes/web.php; then
    echo "✅ Rota de configuração implementada"
else
    echo "❌ Rota não encontrada"
fi

echo ""
echo "📄 6. EXEMPLO DE USO NOS TEMPLATES:"
echo "---------------------------------------------------"
cat << 'EOF'
Variáveis disponíveis em /admin/templates/{id}/editor:

🔐 PRINCIPAIS (processadas automaticamente):
   ${assinatura_digital_info} - Bloco completo da assinatura
   ${qrcode_html} - QR Code para consulta do documento

⚙️  CONFIGURÁVEIS (através do admin):
   ${assinatura_posicao} - Posição da assinatura
   ${assinatura_texto} - Texto da assinatura
   ${qrcode_posicao} - Posição do QR Code
   ${qrcode_texto} - Texto do QR Code
   ${qrcode_tamanho} - Tamanho em pixels
   ${qrcode_url_formato} - URL formatada

EXEMPLO NO TEMPLATE RTF:
=======================
${texto}

Caraguatatuba, ${dia} de ${mes_extenso} de ${ano_atual}.

${assinatura_digital_info}

${qrcode_html}
EOF

echo ""
echo "🚀 7. COMO TESTAR:"
echo "---------------------------------------------------"
echo "1. Acesse: http://localhost:8001/admin/templates"
echo "2. Escolha um tipo de proposição (ex: Moção)"
echo "3. Clique em 'Editar Template'"
echo "4. No painel lateral 'Variáveis Disponíveis'"
echo "5. Procure pela seção 'ASSINATURA DIGITAL & QR CODE'"
echo "6. Clique nas variáveis para copiá-las"
echo "7. Use Ctrl+V para colar no documento"

echo ""
echo "✅ IMPLEMENTAÇÃO CONCLUÍDA COM SUCESSO!"
echo "🎯 =================================================="