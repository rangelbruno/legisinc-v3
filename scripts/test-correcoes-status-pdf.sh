#!/bin/bash

echo "🧪 ==============================================="
echo "✅ TESTANDO CORREÇÕES DE STATUS E PDF"
echo "🧪 ==============================================="
echo ""

echo "📊 Verificando status atual da proposição 2..."

# Verificar status no banco de dados
echo "🗃️ Status no banco de dados:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, status, arquivo_pdf_path IS NOT NULL as tem_pdf FROM proposicoes WHERE id = 2;" 2>/dev/null

echo ""
echo "📝 Verificando mapeamento de status na view..."

# Verificar se os status foram corrigidos
VIEW_PATH="/home/bruno/legisinc/resources/views/proposicoes/show.blade.php"

STATUS_ESPERADOS=(
    "enviado_protocolo"
    "aprovado_assinatura"
    "assinado"
    "protocolado"
)

echo "🎯 Status verificados:"
for status in "${STATUS_ESPERADOS[@]}"; do
    if grep -q "'$status':" "$VIEW_PATH"; then
        echo "✅ Status '$status' mapeado"
    else
        echo "❌ Status '$status' NÃO mapeado"
    fi
done

echo ""
echo "🔧 Verificando otimizações de PDF..."

CONTROLLER_PATH="/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"

OTIMIZACOES=(
    "precisaRegerarPDF:Método de verificação"
    "if (\$precisaRegerarPDF):Condição otimizada"
    "30 minutos:Cache de tempo"
    "filemtime:Verificação de idade"
)

for item in "${OTIMIZACOES[@]}"; do
    IFS=':' read -r pattern description <<< "$item"
    if grep -q "$pattern" "$CONTROLLER_PATH"; then
        echo "✅ $description implementado"
    else
        echo "❌ $description NÃO implementado"
    fi
done

echo ""
echo "🌐 Testando endpoints..."

# Testar página de visualização
echo "📄 Testando /proposicoes/2..."
SHOW_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8001/proposicoes/2)
echo "   Status: $SHOW_STATUS"

# Testar página de assinatura
echo "📝 Testando /proposicoes/2/assinar..."
ASSINAR_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8001/proposicoes/2/assinar)
echo "   Status: $ASSINAR_STATUS"

# Testar endpoint PDF
echo "📄 Testando /proposicoes/2/pdf..."
PDF_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8001/proposicoes/2/pdf)
echo "   Status: $PDF_STATUS"

echo ""
echo "🎯 Verificando se seeder foi adicionado..."

if grep -q "CorrecaoStatusPDFSeeder" /home/bruno/legisinc/database/seeders/DatabaseSeeder.php; then
    echo "✅ Seeder integrado ao DatabaseSeeder"
else
    echo "❌ Seeder NÃO integrado"
fi

echo ""
echo "📊 Resumo dos problemas reportados:"
echo ""
echo "🐛 PROBLEMA 1: Status 'Desconhecido'"
echo "   📍 Localização: /proposicoes/2 (view show.blade.php)"
echo "   🔧 Correção: Adicionados mapeamentos para:"
echo "      - enviado_protocolo → 'Enviado ao Protocolo'"
echo "      - aprovado_assinatura → 'Aguardando Assinatura'"
echo "      - assinado → 'Assinado'"
echo "      - protocolado → 'Protocolado'"
echo ""
echo "🐛 PROBLEMA 2: Botão 'Visualizar PDF' intermitente"
echo "   📍 Localização: Regeneração constante de PDF"
echo "   🔧 Correção: Cache inteligente de PDF:"
echo "      - Verifica se PDF existe e é recente (< 30min)"
echo "      - Evita regeneração desnecessária"
echo "      - Reduz race conditions"
echo ""

echo "🌟 =============================="
echo "✅ TESTE DE CORREÇÕES CONCLUÍDO!"
echo "🌟 =============================="
echo ""
echo "📋 PARA APLICAR AS CORREÇÕES:"
echo "   docker exec -it legisinc-app php artisan migrate:fresh --seed"
echo ""
echo "📋 RESULTADOS ESPERADOS:"
echo "   🎯 Status correto: 'Enviado ao Protocolo'"
echo "   📄 Botão PDF: Estável, sem piscar"
echo "   ⚡ Performance: Menos regeneração de PDF"
echo ""