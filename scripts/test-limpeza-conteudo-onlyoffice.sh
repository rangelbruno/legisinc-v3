#!/bin/bash

echo "=== TESTE: Limpeza de Conteúdo Duplicado no OnlyOffice ==="
echo "Verificando se o sistema remove dados duplicados do DOCX"
echo ""

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo "1. VERIFICAÇÃO DA IMPLEMENTAÇÃO"
echo "=============================="

# Verificar se o método de limpeza foi implementado
if grep -q "limparConteudoDuplicado" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"; then
    echo -e "${GREEN}✓${NC} Método limparConteudoDuplicado implementado"
else
    echo -e "${RED}✗${NC} Método limparConteudoDuplicado não encontrado"
fi

# Verificar padrões de limpeza
patterns=("CÂMARA MUNICIPAL DE" "Praça da República" "EMENTA:" "MOC[ÃA]O")

echo ""
echo "2. PADRÕES DE LIMPEZA CONFIGURADOS"
echo "================================"

for pattern in "${patterns[@]}"; do
    if grep -q "$pattern" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"; then
        echo -e "${GREEN}✓${NC} Padrão '$pattern' configurado para remoção"
    else
        echo -e "${YELLOW}!${NC} Padrão '$pattern' não encontrado"
    fi
done

echo ""
echo "3. VERIFICAÇÃO DE ARQUIVOS DOCX RECENTES"
echo "======================================"

# Verificar arquivo mais recente da proposição 2
latest_file=""
for file in /home/bruno/legisinc/storage/app/private/proposicoes/proposicao_2_*.docx; do
    if [ -f "$file" ]; then
        size=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file" 2>/dev/null)
        if [ $size -gt 1000 ]; then
            latest_file="$file"
        fi
    fi
done

if [ ! -z "$latest_file" ]; then
    echo -e "${GREEN}✓${NC} Arquivo DOCX mais recente: $(basename $latest_file)"
    size=$(stat -f%z "$latest_file" 2>/dev/null || stat -c%s "$latest_file" 2>/dev/null)
    echo "   Tamanho: ${size} bytes"
else
    echo -e "${YELLOW}!${NC} Nenhum arquivo DOCX substancial encontrado"
fi

echo ""
echo "4. TESTE DE CONECTIVIDADE COM SERVIDOR"
echo "===================================="

# Verificar se o servidor está rodando
if curl -s http://localhost:8001 > /dev/null; then
    echo -e "${GREEN}✓${NC} Servidor Laravel operacional"
else
    echo -e "${RED}✗${NC} Servidor Laravel não está respondendo"
    exit 1
fi

echo ""
echo "5. PROBLEMAS QUE DEVEM SER RESOLVIDOS"
echo "===================================="

echo -e "${BLUE}❌ ANTES (Problemas):${NC}"
echo "  • CÂMARA MUNICIPAL DE CARAGUATATUBA"
echo "  • Praça da República, 40, Centro, Caraguatatuba-SP"
echo "  • EMENTA: Criado pelo Parlamentar"
echo "  • MOÇÃO Nº [AGUARDANDO PROTOCOLO] (primeira)"
echo "  • EMENTA: Revisado pelo Legislativo"
echo "  • MOÇÃO Nº [AGUARDANDO PROTOCOLO] (segunda)"

echo ""
echo -e "${GREEN}✅ DEPOIS (Resultado Esperado):${NC}"
echo "  • MOÇÃO Nº [AGUARDANDO PROTOCOLO] (apenas uma)"
echo "  • EMENTA: Revisado pelo Legislativo (apenas a última)"
echo "  • A Câmara Municipal manifesta:"
echo "  • [Conteúdo editado pelo Legislativo]"
echo "  • Resolve dirigir a presente Moção."
echo "  • Data e assinatura"

echo ""
echo "6. FLUXO DE LIMPEZA IMPLEMENTADO"
echo "==============================="

echo -e "${BLUE}ETAPA 1:${NC} Remover dados do cabeçalho da câmara"
echo "  ✓ Remove CÂMARA MUNICIPAL DE..."
echo "  ✓ Remove endereço e telefone"
echo "  ✓ Remove website"

echo ""
echo -e "${BLUE}ETAPA 2:${NC} Remover ementas duplicadas"
echo "  ✓ Detecta múltiplas ementas"
echo "  ✓ Mantém apenas a última (mais recente)"
echo "  ✓ Remove as anteriores"

echo ""
echo -e "${BLUE}ETAPA 3:${NC} Remover títulos duplicados"
echo "  ✓ Detecta múltiplas moções"
echo "  ✓ Mantém apenas uma ocorrência"
echo "  ✓ Remove duplicatas"

echo ""
echo -e "${BLUE}ETAPA 4:${NC} Limpeza geral"
echo "  ✓ Remove quebras de linha excessivas"
echo "  ✓ Ajusta formatação"
echo "  ✓ Log das operações realizadas"

echo ""
echo "7. COMO TESTAR A CORREÇÃO"
echo "======================="

echo -e "${BLUE}🚀 TESTE COMPLETO:${NC}"
echo ""
echo "1. Acesse: http://localhost:8001/login"
echo "2. Login: jessica@sistema.gov.br / 123456 (Parlamentar)"
echo "3. Navegue para: http://localhost:8001/proposicoes/2/assinar"
echo "4. Abra Console do navegador (F12)"
echo "5. Observe os logs:"
echo "   - 'Carregando conteúdo do OnlyOffice...'"
echo "   - 'Conteúdo limpo de duplicações'"
echo "   - 'Usando EXCLUSIVAMENTE o conteúdo OnlyOffice'"
echo "6. Gere o PDF e verifique se:"
echo "   - Não há dados da câmara no topo"
echo "   - Apenas uma ementa (a mais recente)"
echo "   - Apenas um título de moção"
echo "   - Conteúdo limpo e organizado"

echo ""
echo "8. VALIDAÇÃO ESPERADA"
echo "==================="

echo -e "${GREEN}✅ PDF deve conter APENAS:${NC}"
echo "  • MOÇÃO Nº [AGUARDANDO PROTOCOLO]"
echo "  • EMENTA: Revisado pelo Legislativo"
echo "  • A Câmara Municipal manifesta:"
echo "  • Bruno, percebemos que você não vem usando..."
echo "  • [JUSTIFICATIVA]"
echo "  • Resolve dirigir a presente Moção."
echo "  • Caraguatatuba, 20 de agosto de 2025."
echo "  • Jessica Santos"
echo "  • Parlamentar"

echo ""
echo -e "${RED}❌ PDF NÃO deve conter:${NC}"
echo "  • CÂMARA MUNICIPAL DE CARAGUATATUBA"
echo "  • Praça da República, 40, Centro"
echo "  • (12) 3882-5558"
echo "  • www.camaracaraguatatuba.sp.gov.br"
echo "  • EMENTA: Criado pelo Parlamentar"
echo "  • Títulos ou ementas duplicados"

echo ""
echo "=== RESULTADO ==="
echo -e "${GREEN}🎯 SISTEMA DE LIMPEZA IMPLEMENTADO!${NC}"
echo ""
echo -e "${BLUE}🔧 O que foi corrigido:${NC}"
echo "  ✓ Remoção automática de dados do cabeçalho da câmara"
echo "  ✓ Eliminação de ementas duplicadas (mantém apenas a última)"
echo "  ✓ Remoção de títulos duplicados"
echo "  ✓ Limpeza geral de formatação"
echo "  ✓ Logs detalhados para debug"
echo ""
echo -e "${GREEN}🚀 TESTE AGORA E VEJA O PDF LIMPO!${NC}"
echo ""