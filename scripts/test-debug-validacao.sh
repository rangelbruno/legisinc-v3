#!/bin/bash

echo "================================================="
echo "   TESTE DEBUG ESPECÍFICO - VALIDAÇÃO FORMULÁRIO"
echo "================================================="
echo ""

# Cores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${RED}🐛 PROBLEMA IDENTIFICADO:${NC}"
echo "============================================="
echo ""
echo "• Erro Select2: Cannot read properties of null (reading 'trim')"
echo "• Validação retorna false mesmo com dados corretos"
echo "• Campos aparecem mas botão Continuar não habilita"
echo ""

echo -e "${GREEN}✅ CORREÇÕES IMPLEMENTADAS:${NC}"
echo ""
echo "1. 🔧 Select2 Condicional:"
echo "   - Não inicializa Select2 no campo tipo quando pré-selecionado"
echo "   - Evita erro de 'trim' em elementos null"
echo ""

echo "2. 🔍 Debug Detalhado:"
echo "   - Logs de cada etapa da validação"
echo "   - Verificação de condições básicas"
echo "   - Debug específico por tipo de preenchimento"
echo ""

echo -e "${BLUE}🧪 TESTE PASSO A PASSO:${NC}"
echo ""
echo "1. Acesse: http://localhost:8001/proposicoes/criar"
echo "2. Selecione: 'Moção'"
echo "3. Abra DevTools (F12) → Console"
echo "4. Preencha ementa: 'Teste de validação'"
echo "5. Clique em 'Texto Personalizado'"
echo "6. Digite no textarea: 'Este é um texto de teste para validação'"
echo ""

echo -e "${YELLOW}📊 LOGS ESPERADOS NO CONSOLE:${NC}"
echo ""
echo "✅ Tipo pré-selecionado: mocao"
echo "✅ Carregando modelos para tipo: mocao"
echo "✅ Modelos carregados: [1 item]"
echo "✅ Opção de preenchimento selecionada: manual"
echo "✅ Mostrando container de texto manual"
echo "✅ Verificando condições básicas: {todos true}"
echo "✅ Condições básicas OK, verificando por opção..."
echo "✅ Validação manual: {temTexto: true, tamanho: >10, valido: true}"
echo "✅ Validação resultado final: true"
echo ""

echo -e "${RED}🚨 SE AINDA FALHAR:${NC}"
echo ""
echo "Verifique se aparece:"
echo "• temEmenta: false → campo ementa não preenchido"
echo "• temOpcaoPreenchimento: false → opção não selecionada"
echo "• temModelo: false → modelo não carregado/selecionado"
echo "• temTexto: false → textarea não preenchido"
echo ""

echo -e "${GREEN}🎯 FLUXO CORRETO:${NC}"
echo ""
echo "1. Tipo: pré-selecionado ✅"
echo "2. Ementa: usuário digita ✅"
echo "3. Opção: usuário seleciona ✅"
echo "4. Modelo: carregado automaticamente ✅"
echo "5. Texto: usuário digita ✅"
echo "6. Botão: habilitado automaticamente ✅"
echo ""

echo -e "${BLUE}📋 PRÓXIMO TESTE:${NC}"
echo ""
echo "Teste também a opção 'Texto com IA':"
echo "1. Selecione 'Texto com IA'"
echo "2. Clique 'Gerar Texto via IA'"
echo "3. Aguarde geração"
echo "4. Botão Continuar deve habilitar"
echo ""

echo -e "${YELLOW}✅ Debug detalhado ativado - teste agora!${NC}"
echo ""
echo "================================================="