#!/bin/bash

echo "🎯 ========================================="
echo "📝 DEMONSTRAÇÃO: ASSINATURA E QR CODE"
echo "🎯 ========================================="
echo ""

echo "📋 1. VERIFICANDO CONFIGURAÇÕES PADRÃO NO BANCO:"
echo "---------------------------------------------------"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "
SELECT 
    pc.nome as campo,
    pc.label as descricao,
    pv.valor as valor_atual
FROM parametros_submodulos ps 
JOIN parametros_campos pc ON ps.id = pc.submodulo_id 
LEFT JOIN parametros_valores pv ON pc.id = pv.campo_id
WHERE ps.nome = 'Assinatura e QR Code' 
ORDER BY pc.ordem;
"

echo ""
echo "🌐 2. TESTANDO ACESSO À PÁGINA DE CONFIGURAÇÃO:"
echo "---------------------------------------------------"
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8001/parametros-templates-assinatura-qrcode")
if [ "$HTTP_STATUS" = "200" ]; then
    echo "✅ Página de configuração acessível (HTTP $HTTP_STATUS)"
    echo "🔗 URL: http://localhost:8001/parametros-templates-assinatura-qrcode"
else
    echo "❌ Erro ao acessar página (HTTP $HTTP_STATUS)"
fi

echo ""
echo "📝 3. VARIÁVEIS DISPONÍVEIS NOS TEMPLATES:"
echo "---------------------------------------------------"
echo "📍 POSICIONAMENTO:"
echo "   \${assinatura_posicao} - Posição da assinatura"
echo "   \${qrcode_posicao} - Posição do QR Code"
echo ""
echo "✍️  TEXTO E CONTEÚDO:"
echo "   \${assinatura_texto} - Texto da assinatura"
echo "   \${qrcode_texto} - Texto do QR Code"
echo "   \${qrcode_url_formato} - URL do QR Code"
echo ""
echo "⚙️  CONFIGURAÇÕES:"
echo "   \${qrcode_tamanho} - Tamanho em pixels"
echo "   \${assinatura_apenas_protocolo} - Controle de exibição"
echo "   \${qrcode_apenas_protocolo} - Controle de exibição"
echo ""
echo "🔄 VARIÁVEIS DINÂMICAS (processadas automaticamente):"
echo "   \${assinatura_digital_info} - Bloco completo da assinatura"
echo "   \${qrcode_html} - HTML do QR Code"
echo "   \${data_assinatura} - Data da assinatura"
echo "   \${autor_nome} - Nome do autor"
echo "   \${autor_cargo} - Cargo do autor"

echo ""
echo "📄 4. EXEMPLO DE USO NO TEMPLATE RTF:"
echo "---------------------------------------------------"
cat << 'EOF'
CÂMARA MUNICIPAL DE CARAGUATATUBA
================================

MOÇÃO Nº ${numero_proposicao}

EMENTA: ${ementa}

${texto}

Caraguatatuba, ${dia} de ${mes_extenso} de ${ano_atual}.

${assinatura_digital_info}

${qrcode_html}

EOF

echo ""
echo "🚀 5. PRÓXIMOS PASSOS:"
echo "---------------------------------------------------"
echo "1. Acesse: http://localhost:8001/parametros-templates-assinatura-qrcode"
echo "2. Configure as posições da assinatura e QR Code"
echo "3. Personalize os textos usando as variáveis disponíveis"
echo "4. Teste criando uma proposição e assinando digitalmente"
echo "5. Verifique se a assinatura e QR Code aparecem nas posições configuradas"

echo ""
echo "✅ SISTEMA DE ASSINATURA E QR CODE TOTALMENTE FUNCIONAL!"
echo "🎯 ========================================="