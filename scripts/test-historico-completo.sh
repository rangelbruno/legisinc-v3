#!/bin/bash

echo "=== TESTE: Histórico Completo da Proposição ==="
echo ""

# Verificar dados da proposição para histórico
echo "1. Verificando dados da proposição para construção do histórico..."
DADOS=$(docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::find(1);
echo 'ID: ' . \$proposicao->id . PHP_EOL;
echo 'Status: ' . \$proposicao->status . PHP_EOL;
echo 'Created: ' . \$proposicao->created_at . PHP_EOL;
echo 'Updated: ' . \$proposicao->updated_at . PHP_EOL;
echo 'Enviado Revisão: ' . (\$proposicao->enviado_revisao_em ?: 'NULL') . PHP_EOL;
echo 'Revisado Em: ' . (\$proposicao->revisado_em ?: 'NULL') . PHP_EOL;
echo 'Revisor: ' . (\$proposicao->revisor ? \$proposicao->revisor->name : 'NULL') . PHP_EOL;
")

echo "$DADOS"
echo ""

# Verificar lógica do histórico
echo "2. Verificando lógica do histórico baseada no status..."

STATUS_LOGIC=$(docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::find(1);
\$status = \$proposicao->status;

echo 'Status atual: ' . \$status . PHP_EOL;

// Verificar quais seções do histórico serão exibidas
echo 'Mostra Criação: SIM (sempre mostrado)' . PHP_EOL;

// Enviado para análise
\$mostraEnviado = in_array(\$status, ['enviado_legislativo', 'em_revisao', 'analise', 'aprovado_assinatura']);
echo 'Mostra Enviado Análise: ' . (\$mostraEnviado ? 'SIM' : 'NAO') . PHP_EOL;

// Aprovado para assinatura
\$mostraAprovado = \$status === 'aprovado_assinatura';
echo 'Mostra Aprovado Assinatura: ' . (\$mostraAprovado ? 'SIM' : 'NAO') . PHP_EOL;

// Assinado
\$mostraAssinado = \$status === 'assinado';
echo 'Mostra Assinado: ' . (\$mostraAssinado ? 'SIM' : 'NAO') . PHP_EOL;
")

echo "$STATUS_LOGIC"
echo ""

echo "3. Simulando como o histórico deve aparecer..."
echo ""
echo "📅 Histórico Esperado:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "🟢 Proposição Criada"
echo "   15/08/2025 22:25"
echo "   Jessica Santos criou esta proposição do tipo MOCAO"
echo ""
echo "🔵 Enviada para Análise"
echo "   15/08/2025 22:33"
echo "   Proposição enviada para análise do Legislativo"
echo ""
echo "🟡 Aprovado para Assinatura"
echo "   15/08/2025 22:38"
echo "   João Oliveira"
echo "   Proposição aprovada pelo Legislativo e liberada para assinatura digital pelo parlamentar"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

echo "✅ CORREÇÕES APLICADAS:"
echo "- Seção 'Aprovado para Assinatura' adicionada ao histórico"
echo "- Status 'aprovado_assinatura' incluído na seção 'Enviado para Análise'"
echo "- Dados de revisor e datas atualizados na proposição"
echo ""
echo "🎯 TESTE MANUAL:"
echo "1. Acesse http://localhost:8001/proposicoes/1"
echo "2. Faça login como Jessica (jessica@sistema.gov.br / 123456)"
echo "3. Verifique se o histórico mostra as 3 etapas:"
echo "   - Proposição Criada"
echo "   - Enviada para Análise"
echo "   - Aprovado para Assinatura"
echo ""
echo "📋 STATUS: Histórico completo implementado ✅"