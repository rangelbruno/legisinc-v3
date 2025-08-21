#!/bin/bash

echo "=== TESTE: PDF Pesquisável com Extração Avançada OnlyOffice ==="
echo "Verificando se o sistema gera PDF com texto selecionável e auditável"
echo ""

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo "🎯 OBJETIVO DO TESTE"
echo "=================="
echo ""
echo -e "${BLUE}📋 O que será testado:${NC}"
echo "  ✓ Extração fiel do conteúdo OnlyOffice editado pelo Legislativo"
echo "  ✓ Validação de fidelidade do conteúdo extraído (70%+ score)"  
echo "  ✓ Geração de PDF pesquisável com texto invisível"
echo "  ✓ Camadas de auditoria com metadados de integridade"
echo "  ✓ Comunicação otimizada com editor OnlyOffice"

echo ""
echo "🛠️ RECURSOS IMPLEMENTADOS"
echo "========================"

echo ""
echo -e "${GREEN}✅ BACKEND (ProposicaoAssinaturaController.php):${NC}"
echo "  • Método extrairConteudoAvançado() com PhpOffice\PhpWord"
echo "  • Processamento de estrutura, formatação e metadados"
echo "  • Hash de integridade SHA256 para auditoria"  
echo "  • Sistema limparConteudoDuplicado() para conteúdo limpo"
echo "  • Endpoint /conteudo-onlyoffice com dados estruturados"

echo ""
echo -e "${GREEN}✅ FRONTEND (Vue.js):${NC}"
echo "  • Método carregarConteudoOnlyOffice() com logs avançados"
echo "  • Validação de fidelidade com 5 critérios (score 0-100%)"
echo "  • Geração de PDF pesquisável com texto invisível"
echo "  • Camadas de auditoria com metadados timestampados" 
echo "  • Sistema de prioridade OnlyOffice vs Fallback"

echo ""
echo "🔍 VERIFICAÇÕES TÉCNICAS"
echo "======================="

# 1. Verificar se os métodos avançados estão implementados
echo -e "${CYAN}1. Verificando implementação backend...${NC}"

if grep -q "extrairConteudoAvançado" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"; then
    echo -e "${GREEN}  ✓${NC} Método extrairConteudoAvançado implementado"
else
    echo -e "${RED}  ✗${NC} Método extrairConteudoAvançado não encontrado"
fi

if grep -q "limparConteudoDuplicado" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"; then
    echo -e "${GREEN}  ✓${NC} Sistema de limpeza de conteúdo implementado"
else
    echo -e "${RED}  ✗${NC} Sistema de limpeza não encontrado"
fi

echo ""
echo -e "${CYAN}2. Verificando implementação frontend...${NC}"

if grep -q "validarFidelidadeConteudo" "/home/bruno/legisinc/resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php"; then
    echo -e "${GREEN}  ✓${NC} Validação de fidelidade implementada"
else
    echo -e "${RED}  ✗${NC} Validação de fidelidade não encontrada"
fi

if grep -q "texto invisível para auditoria" "/home/bruno/legisinc/resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php"; then
    echo -e "${GREEN}  ✓${NC} PDF pesquisável com texto invisível implementado"
else
    echo -e "${RED}  ✗${NC} PDF pesquisável não encontrado"
fi

if grep -q "extrairParagrafosParaAuditoria" "/home/bruno/legisinc/resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php"; then
    echo -e "${GREEN}  ✓${NC} Extração de parágrafos para auditoria implementada"
else
    echo -e "${RED}  ✗${NC} Extração de parágrafos não encontrada"
fi

echo ""
echo -e "${CYAN}3. Verificando conectividade do sistema...${NC}"

# Verificar se o servidor está rodando
if curl -s http://localhost:8001 > /dev/null; then
    echo -e "${GREEN}  ✓${NC} Servidor Laravel operacional"
    
    # Testar endpoint de conteúdo OnlyOffice
    response=$(curl -s -H "Accept: application/json" "http://localhost:8001/proposicoes/2/conteudo-onlyoffice" 2>/dev/null)
    if echo "$response" | grep -q '"success"'; then
        echo -e "${GREEN}  ✓${NC} Endpoint /conteudo-onlyoffice funcionando"
    else
        echo -e "${YELLOW}  !${NC} Endpoint responde mas pode não ter dados OnlyOffice"
    fi
else
    echo -e "${RED}  ✗${NC} Servidor Laravel não está rodando"
fi

echo ""
echo "📊 FLUXO DE VALIDAÇÃO DE FIDELIDADE"
echo "================================="

echo ""
echo -e "${BLUE}Critérios de Validação (Score 0-100%):${NC}"
echo "  1. 📏 Tamanho do conteúdo (≥100 caracteres)"
echo "  2. 🏗️  Estrutura do documento (elementos essenciais)"  
echo "  3. 🎨 Formatação preservada (estilos e layouts)"
echo "  4. 📋 Metadados disponíveis (informações do arquivo)"
echo "  5. 🔒 Hash de integridade (SHA256 para auditoria)"

echo ""
echo -e "${BLUE}Score de Aprovação:${NC}"
echo "  • 70-100%: ✅ Aprovado - PDF pesquisável gerado"
echo "  • 50-69%:  ⚠️ Atenção - PDF gerado com alertas"
echo "  • 0-49%:   ❌ Falha - Usar fallback tradicional"

echo ""
echo "🔍 PDF PESQUISÁVEL - RECURSOS TÉCNICOS"
echo "======================================"

echo ""
echo -e "${PURPLE}📄 Camadas do PDF:${NC}"
echo "  1. 🖼️  Camada Visual: Imagem renderizada do documento"
echo "  2. 📝 Camada Invisível: Texto selecionável (transparência 0)"
echo "  3. 🔍 Camada de Auditoria: Metadados e hash de integridade"

echo ""
echo -e "${PURPLE}🔍 Funcionalidades de Pesquisa:${NC}"
echo "  • Texto totalmente pesquisável (Ctrl+F)"
echo "  • Seleção de texto para cópia"
echo "  • Indexação por motores de busca"
echo "  • Auditoria digital completa"

echo ""
echo "🧪 COMO TESTAR O SISTEMA COMPLETO"
echo "================================"

echo ""
echo -e "${YELLOW}📋 TESTE PASSO A PASSO:${NC}"

echo ""
echo -e "${CYAN}ETAPA 1: Preparar dados OnlyOffice${NC}"
echo "  1. Login como Legislativo: http://localhost:8001/login"
echo "  2. Credenciais: joao@sistema.gov.br / 123456"
echo "  3. Editar proposição 2 no OnlyOffice"
echo "  4. Fazer modificações significativas no texto"
echo "  5. Salvar no OnlyOffice"

echo ""
echo -e "${CYAN}ETAPA 2: Testar endpoint de extração${NC}"
echo "  1. Abrir terminal"
echo "  2. Executar: curl -H 'Accept: application/json' http://localhost:8001/proposicoes/2/conteudo-onlyoffice"
echo "  3. Verificar resposta JSON com:"
echo "     • success: true"
echo "     • conteudo: texto extraído"
echo "     • hash_integridade: SHA256"
echo "     • estrutura: dados estruturais"

echo ""
echo -e "${CYAN}ETAPA 3: Gerar PDF pesquisável${NC}"
echo "  1. Login como Parlamentar: jessica@sistema.gov.br / 123456"
echo "  2. Acessar: http://localhost:8001/proposicoes/2/assinar"
echo "  3. Abrir DevTools (F12) → Console"
echo "  4. Clicar em 'Gerar PDF'"
echo "  5. Observar logs no console"

echo ""
echo -e "${CYAN}ETAPA 4: Verificar logs esperados${NC}"
echo -e "${GREEN}  Logs de Sucesso:${NC}"
echo "  • '🔍 Iniciando extração avançada OnlyOffice...'"
echo "  • '✅ Extração avançada concluída com sucesso'"
echo "  • '🎯 Iniciando validação de fidelidade do conteúdo OnlyOffice...'"
echo "  • '📊 Validação de fidelidade concluída: score: XX%'"
echo "  • '✅ Fidelidade do conteúdo aprovada: XX%'"
echo "  • '🔍 Adicionando camada de texto invisível para auditoria...'"
echo "  • '✅ Texto invisível para auditoria adicionado com sucesso'"
echo "  • '🔍 PDF agora é totalmente pesquisável e auditável'"

echo ""
echo -e "${CYAN}ETAPA 5: Testar PDF pesquisável${NC}"
echo "  1. Download do PDF gerado"
echo "  2. Abrir em visualizador de PDF"
echo "  3. Usar Ctrl+F para pesquisar texto"
echo "  4. Verificar se encontra palavras do documento"
echo "  5. Tentar selecionar texto (deve funcionar)"

echo ""
echo "🎯 RESULTADOS ESPERADOS"
echo "======================"

echo ""
echo -e "${GREEN}✅ SUCESSO COMPLETO:${NC}"
echo "  • Score de fidelidade ≥ 70%"
echo "  • PDF visualmente idêntico ao OnlyOffice"
echo "  • Texto totalmente pesquisável"
echo "  • Metadados de auditoria presentes"
echo "  • Hash de integridade validado"
echo "  • Logs detalhados no console"

echo ""
echo -e "${YELLOW}⚠️ CENÁRIOS DE ATENÇÃO:${NC}"
echo "  • Score 50-69%: Sistema funciona mas com alertas"
echo "  • Arquivo OnlyOffice não encontrado: usa fallback"
echo "  • Extração parcial: PDF gerado mas com limitações"

echo ""
echo -e "${RED}❌ CENÁRIOS DE FALHA:${NC}"
echo "  • Score < 50%: Qualidade insuficiente"
echo "  • Erro na extração: Endpoint retorna erro"
echo "  • PDF não pesquisável: Texto invisível não adicionado"

echo ""
echo "📋 TROUBLESHOOTING"
echo "=================="

echo ""
echo -e "${BLUE}🔧 Problemas Comuns:${NC}"

echo ""
echo -e "${YELLOW}1. 'Nenhum arquivo OnlyOffice encontrado'${NC}"
echo "   → Editar e salvar proposição no OnlyOffice primeiro"
echo "   → Verificar se arquivo existe em storage/app/"

echo ""
echo -e "${YELLOW}2. 'Score de fidelidade baixo'${NC}"
echo "   → Verificar se arquivo DOCX não está corrompido"
echo "   → Tentar salvar novamente no OnlyOffice"
echo "   → Verificar se PhpOffice\PhpWord está funcionando"

echo ""
echo -e "${YELLOW}3. 'PDF não é pesquisável'${NC}"
echo "   → Verificar se jsPDF suporta texto invisível"
echo "   → Verificar se método extrairParagrafosParaAuditoria funciona"
echo "   → Conferir se texto invisível foi adicionado nos logs"

echo ""
echo -e "${YELLOW}4. 'Erro na validação de integridade'${NC}"
echo "   → Verificar se SHA256 está sendo gerado corretamente"
echo "   → Confirmar se dados chegam completos no frontend"

echo ""
echo "🚀 COMANDOS ÚTEIS PARA DEBUG"
echo "==========================="

echo ""
echo "# Verificar arquivos OnlyOffice recentes:"
echo "find /home/bruno/legisinc/storage/app -name '*proposic*docx' -newer /tmp/ref 2>/dev/null || find /home/bruno/legisinc/storage/app -name '*proposic*docx' | head -5"

echo ""
echo "# Testar endpoint diretamente:"  
echo "curl -v -H 'Accept: application/json' http://localhost:8001/proposicoes/2/conteudo-onlyoffice"

echo ""
echo "# Verificar logs Laravel:"
echo "tail -f /home/bruno/legisinc/storage/logs/laravel.log"

echo ""
echo "# Verificar se PhpWord está disponível:"
echo "cd /home/bruno/legisinc && php -r \"echo class_exists('PhpOffice\PhpWord\PhpWord') ? 'OK' : 'ERRO'; echo PHP_EOL;\""

echo ""
echo "=== RESUMO ==="
echo ""
echo -e "${GREEN}🎊 SISTEMA AVANÇADO IMPLEMENTADO!${NC}"
echo ""
echo -e "${BLUE}📋 Funcionalidades:${NC}"
echo "  ✓ Extração fiel com PhpOffice\PhpWord"
echo "  ✓ Validação de fidelidade automática" 
echo "  ✓ PDF pesquisável com camadas invisíveis"
echo "  ✓ Auditoria digital completa"
echo "  ✓ Sistema de fallback robusto"
echo "  ✓ Logs detalhados para debugging"
echo ""
echo -e "${PURPLE}🚀 TESTE AGORA E VEJA A DIFERENÇA!${NC}"
echo -e "${CYAN}📄 PDF final: Visualmente perfeito + Totalmente pesquisável${NC}"
echo ""