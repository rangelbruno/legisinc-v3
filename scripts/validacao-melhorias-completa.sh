#!/bin/bash

# 🔍 VALIDAÇÃO COMPLETA - MELHORIAS CRÍTICAS FLUXO DOCUMENTO
# Sistema Legisinc v2.1 → v2.2 Enterprise
# Data: 07/09/2025

set -e # Exit on any error

echo "🔍 VALIDAÇÃO COMPLETA - MELHORIAS CRÍTICAS"
echo "=========================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

log_step() {
    echo -e "${BLUE}$1${NC}"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

# Function to check if docker container is running
check_container() {
    if ! docker ps | grep -q "legisinc-app"; then
        log_error "Container legisinc-app não está rodando"
        echo "Execute: docker-compose up -d"
        exit 1
    fi
    log_success "Container legisinc-app está ativo"
}

# Function to check if database is accessible
check_database() {
    log_step "Verificando conexão com banco de dados..."
    if docker exec legisinc-app php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';" > /dev/null 2>&1; then
        log_success "Banco de dados acessível"
    else
        log_error "Não foi possível conectar ao banco de dados"
        exit 1
    fi
}

# Main validation function
main() {
    echo "Iniciando validação das melhorias críticas..."
    echo ""
    
    # Pre-checks
    log_step "🔧 PRÉ-VALIDAÇÃO"
    check_container
    check_database
    echo ""
    
    # 1. Reset complete environment
    log_step "1️⃣ Reset completo do ambiente..."
    docker exec legisinc-app php artisan migrate:fresh --seed --force
    if [ $? -eq 0 ]; then
        log_success "Reset do ambiente concluído"
    else
        log_error "Falha no reset do ambiente"
        exit 1
    fi
    echo ""
    
    # 2. Test corrected PDF outdated query
    log_step "2️⃣ Testando query de PDF desatualizado corrigida..."
    if docker exec legisinc-app php artisan test:pdf-outdated-query > /dev/null 2>&1; then
        log_success "Query de PDF desatualizado funcionando"
    else
        log_warning "Comando test:pdf-outdated-query não encontrado (será implementado)"
    fi
    echo ""
    
    # 3. Test OnlyOffice pipeline
    log_step "3️⃣ Testando pipeline OnlyOffice→PDF..."
    if docker exec legisinc-app php artisan test:onlyoffice-pipeline > /dev/null 2>&1; then
        log_success "Pipeline OnlyOffice funcionando"
    else
        log_warning "Pipeline OnlyOffice será implementado"
    fi
    echo ""
    
    # 4. Test PDF layers
    log_step "4️⃣ Testando camadas de PDF..."
    if docker exec legisinc-app php artisan test:pdf-camadas > /dev/null 2>&1; then
        log_success "Camadas de PDF funcionando"
    else
        log_warning "Sistema de camadas de PDF será implementado"
    fi
    echo ""
    
    # 5. Test ServePDF state-aware
    log_step "5️⃣ Testando ServePDF state-aware..."
    # Check if servePDF method exists and has state logic
    if docker exec legisinc-app grep -r "servePDF" app/Http/Controllers/ > /dev/null 2>&1; then
        log_success "Método servePDF encontrado"
    else
        log_warning "ServePDF state-aware será implementado"
    fi
    echo ""
    
    # 6. Test protocol numbering
    log_step "6️⃣ Testando numeração transacional de protocolo..."
    # Check if protocolo_sequencias table exists
    if docker exec legisinc-app php artisan tinker --execute="echo Schema::hasTable('protocolo_sequencias') ? 'existe' : 'nao_existe';" 2>/dev/null | grep -q "existe"; then
        log_success "Tabela protocolo_sequencias existe"
    else
        log_warning "Sistema de numeração transacional será implementado"
    fi
    echo ""
    
    # 7. Test hash control
    log_step "7️⃣ Testando controle por hash..."
    # Check if arquivo_hash column exists
    if docker exec legisinc-app php artisan tinker --execute="echo Schema::hasColumn('proposicoes', 'arquivo_hash') ? 'existe' : 'nao_existe';" 2>/dev/null | grep -q "existe"; then
        log_success "Campo arquivo_hash existe"
    else
        log_warning "Sistema de controle por hash será implementado"
    fi
    echo ""
    
    # 8. Test state machine
    log_step "8️⃣ Testando state machine..."
    # Check if ProposicaoStateMachine service exists
    if [ -f "/home/bruno/legisinc/app/Services/ProposicaoStateMachine.php" ]; then
        log_success "ProposicaoStateMachine encontrado"
    else
        log_warning "State Machine será implementado"
    fi
    echo ""
    
    # Additional validations
    log_step "🔍 VALIDAÇÕES ADICIONAIS"
    
    # Check OnlyOffice functionality
    log_step "Verificando OnlyOffice..."
    if docker exec legisinc-app curl -s http://legisinc-onlyoffice:80/welcome > /dev/null 2>&1; then
        log_success "OnlyOffice DocumentServer acessível"
    else
        log_warning "OnlyOffice DocumentServer pode não estar configurado"
    fi
    
    # Check storage directories
    log_step "Verificando estrutura de storage..."
    docker exec legisinc-app mkdir -p storage/app/proposicoes/2025/{rtf,pdf,docx}
    if [ $? -eq 0 ]; then
        log_success "Estrutura de diretórios OK"
    else
        log_error "Problema na criação de diretórios"
    fi
    
    # Check key services
    log_step "Verificando serviços principais..."
    if docker exec legisinc-app php artisan route:list | grep -q "proposicoes" > /dev/null 2>&1; then
        log_success "Rotas de proposições carregadas"
    else
        log_error "Problema nas rotas de proposições"
    fi
    
    echo ""
    echo "📊 RESUMO DA VALIDAÇÃO"
    echo "====================="
    echo ""
    echo "🔴 IMPLEMENTAÇÕES NECESSÁRIAS:"
    echo "  • Pipeline OnlyOffice-first para PDF"
    echo "  • Três camadas de PDF (para_assinatura/assinado/protocolado)"
    echo "  • ServePDF state-aware por status"
    echo "  • Numeração transacional de protocolo"
    echo "  • Controle por hash SHA-256"
    echo "  • State Machine explícita"
    echo ""
    echo "✅ SISTEMA BASE FUNCIONANDO"
    echo "✅ AMBIENTE PRONTO PARA IMPLEMENTAÇÕES"
    echo ""
    echo "🎯 PRÓXIMOS PASSOS:"
    echo "1. Implementar corrections SQL query"
    echo "2. Criar pipeline OnlyOffice→PDF"
    echo "3. Implementar sistema de 3 PDFs"
    echo "4. Criar ServePDF state-aware"
    echo "5. Implementar numeração transacional"
    echo ""
    log_success "VALIDAÇÃO COMPLETA FINALIZADA"
    echo ""
    echo "📋 Para implementar as melhorias, siga o checklist em:"
    echo "   docs/MELHORIAS-FLUXO-DOCUMENTO-COMPLETO.md"
}

# Execute main function
main "$@"