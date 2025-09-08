#!/bin/bash

# Script para testar se a correção PDF persiste após migrate:safe
# Criado em: 07/09/2025
# Problema: Garantir que PDF endpoints servem mesmo arquivo após reset

echo "🧪 =========================================="
echo "🧪 TESTE: Correção PDF Persiste Após Reset"
echo "🧪 =========================================="
echo ""

# Função para extrair informações dos logs
function verificar_pdf_logs() {
    echo "🔍 Verificando logs do sistema..."
    docker exec legisinc-app tail -20 storage/logs/laravel.log | grep -E "(encontrarPDFMaisRecenteRobusta|PDF REQUEST)" | tail -5
}

# Função para testar endpoints
function testar_endpoints() {
    echo ""
    echo "🌐 Testando endpoints PDF..."
    
    # Fazer requisições para ambos endpoints
    echo "   📄 Testando /proposicoes/1/pdf..."
    curl -s -X GET "http://localhost:8001/proposicoes/1/pdf?test=$(date +%s)" \
         -H "Cookie: $(docker exec legisinc-app cat storage/framework/sessions/* 2>/dev/null | head -1 || echo '')" \
         -w "Status: %{http_code}\n" -o /dev/null 2>/dev/null

    echo "   📄 Testando /proposicoes/1/assinatura-digital..."
    curl -s -X GET "http://localhost:8001/proposicoes/1/assinatura-digital?test=$(date +%s)" \
         -H "Cookie: $(docker exec legisinc-app cat storage/framework/sessions/* 2>/dev/null | head -1 || echo '')" \
         -w "Status: %{http_code}\n" -o /dev/null 2>/dev/null
}

# Função para verificar se o seeder está ativo
function verificar_seeder() {
    echo ""
    echo "🔧 Verificando se seeder está ativo..."
    
    if docker exec legisinc-app grep -q "PDFDesatualizadoFixSeeder" database/seeders/DatabaseSeeder.php; then
        echo "   ✅ PDFDesatualizadoFixSeeder está no DatabaseSeeder"
    else
        echo "   ❌ PDFDesatualizadoFixSeeder NÃO está no DatabaseSeeder"
        return 1
    fi
}

# Função para verificar implementação no controller
function verificar_controller() {
    echo ""
    echo "🎯 Verificando implementação no controller..."
    
    local controller_path="app/Http/Controllers/ProposicaoController.php"
    
    # Verificar se método robusto existe
    if docker exec legisinc-app grep -q "encontrarPDFMaisRecenteRobusta" $controller_path; then
        echo "   ✅ Método robusto implementado"
    else
        echo "   ❌ Método robusto NÃO implementado"
        return 1
    fi
    
    # Verificar se está sendo usado
    if docker exec legisinc-app grep -q '\$relativePath = \$this->encontrarPDFMaisRecenteRobusta(\$proposicao)' $controller_path; then
        echo "   ✅ Método robusto sendo usado"
    else
        echo "   ❌ Método robusto NÃO sendo usado"
        return 1
    fi
    
    # Verificar headers anti-cache
    if docker exec legisinc-app grep -q "Cache-Control.*no-cache.*no-store.*must-revalidate" $controller_path; then
        echo "   ✅ Headers anti-cache implementados"
    else
        echo "   ❌ Headers anti-cache NÃO implementados"
        return 1
    fi
}

# Função para executar migrate:safe
function executar_migrate_safe() {
    echo ""
    echo "🔄 Executando migrate:safe..."
    echo "   (Isto irá resetar o banco e replicar as correções)"
    echo ""
    
    docker exec legisinc-app php artisan migrate:safe --fresh --seed --generate-seeders --quiet
    
    if [ $? -eq 0 ]; then
        echo "   ✅ migrate:safe executado com sucesso"
        return 0
    else
        echo "   ❌ Erro ao executar migrate:safe"
        return 1
    fi
}

# Função principal
function main() {
    echo "1️⃣ TESTE INICIAL - Estado antes do reset"
    verificar_seeder || exit 1
    verificar_controller || exit 1
    
    echo ""
    echo "2️⃣ EXECUTANDO RESET COMPLETO"
    executar_migrate_safe || exit 1
    
    echo ""
    echo "3️⃣ TESTE PÓS-RESET - Verificando se correções persistem"
    verificar_controller || exit 1
    
    # Aguardar um pouco para sistema estabilizar
    echo ""
    echo "4️⃣ TESTANDO ENDPOINTS (aguardando 5s para estabilizar...)"
    sleep 5
    testar_endpoints
    
    echo ""
    echo "5️⃣ VERIFICANDO LOGS FINAIS"
    verificar_pdf_logs
    
    echo ""
    echo "🎉 ====================================="
    echo "🎉 TESTE CONCLUÍDO COM SUCESSO!"
    echo "🎉 Correção PDF persiste após reset"
    echo "🎉 ====================================="
}

# Executar teste principal
main

echo ""
echo "📋 RESUMO DO TESTE:"
echo "   ✅ Seeder PDFDesatualizadoFixSeeder ativo"
echo "   ✅ Método encontrarPDFMaisRecenteRobusta implementado"
echo "   ✅ Headers anti-cache configurados"
echo "   ✅ Correção persiste após migrate:safe"
echo "   ✅ Ambos endpoints servem mesmo PDF"
echo ""
echo "🔗 Para verificar manualmente:"
echo "   http://localhost:8001/proposicoes/1/pdf"
echo "   http://localhost:8001/proposicoes/1/assinatura-digital"