#!/bin/bash

echo "✨ TESTE DO MODAL SIMPLIFICADO PARA PARLAMENTARES"
echo "================================================"
echo ""

echo "🎯 OBJETIVO: Tornar o modal menos técnico e mais direto"
echo ""

# Verificar se as modificações foram aplicadas
echo "1. Verificando remoção da seção técnica..."

# Verificar se a seção técnica foi removida
if grep -q "🗑️ O que será excluído permanentemente" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php; then
    echo "   ❌ Seção técnica ainda presente no código"
else
    echo "   ✅ Seção técnica removida com sucesso"
fi

# Verificar se a lista detalhada foi removida
if grep -q "📊 Registro completo do banco de dados" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php; then
    echo "   ❌ Lista técnica detalhada ainda presente"
else
    echo "   ✅ Lista técnica detalhada removida"
fi

# Verificar se o espaçamento foi ajustado
if grep -q "mb-5" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php; then
    echo "   ✅ Espaçamento ajustado (mb-5)"
else
    echo "   ❌ Espaçamento não foi ajustado"
fi

echo ""
echo "2. Verificando estrutura simplificada atual:"

# Limpar caches
php artisan view:clear > /dev/null 2>&1

# Criar sessão para teste
COOKIE_JAR="/tmp/test_modal_simples.txt"
rm -f "$COOKIE_JAR"

# Login e verificação
LOGIN_PAGE=$(curl -s -c "$COOKIE_JAR" http://localhost:8001/login)
CSRF_TOKEN=$(echo "$LOGIN_PAGE" | grep -oP 'name="_token" value="\K[^"]+')

if [ -n "$CSRF_TOKEN" ]; then
    # Fazer login
    LOGIN_RESPONSE=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
        -X POST http://localhost:8001/login \
        -H "Content-Type: application/x-www-form-urlencoded" \
        -d "_token=$CSRF_TOKEN&email=bruno@sistema.gov.br&password=123456")
    
    if echo "$LOGIN_RESPONSE" | grep -q "dashboard\|proposicoes" || [ ${#LOGIN_RESPONSE} -lt 100 ]; then
        echo "   ✅ Login realizado com sucesso"
        
        # Acessar página da proposição
        PROPOSICAO_PAGE=$(curl -s -b "$COOKIE_JAR" http://localhost:8001/proposicoes/2)
        
        # Verificar elementos presentes
        if echo "$PROPOSICAO_PAGE" | grep -q "📄 Proposição:"; then
            echo "   ✅ Seção da proposição mantida"
        else
            echo "   ❌ Seção da proposição não encontrada"
        fi
        
        if echo "$PROPOSICAO_PAGE" | grep -q "⚠️ ATENÇÃO CRÍTICA"; then
            echo "   ✅ Aviso crítico mantido"
        else
            echo "   ❌ Aviso crítico não encontrado"
        fi
        
        # Verificar se elementos técnicos foram removidos
        if echo "$PROPOSICAO_PAGE" | grep -q "📊 Registro completo"; then
            echo "   ❌ Conteúdo técnico ainda presente"
        else
            echo "   ✅ Conteúdo técnico removido da página"
        fi
        
        if echo "$PROPOSICAO_PAGE" | grep -q "🗑️ O que será excluído"; then
            echo "   ❌ Lista técnica ainda presente"
        else
            echo "   ✅ Lista técnica removida da página"
        fi
        
    else
        echo "   ❌ Falha no login"
    fi
else
    echo "   ❌ Não foi possível obter CSRF token"
fi

# Limpeza
rm -f "$COOKIE_JAR"

echo ""
echo "=== ESTRUTURA SIMPLIFICADA DO MODAL ==="
echo ""
echo "┌─────────────────────────────────────────┐"
echo "│       🗑️ EXCLUIR PROPOSIÇÃO            │"
echo "│                                         │"
echo "│  Confirmar Exclusão Permanente         │"
echo "│  Esta ação não pode ser desfeita        │"
echo "│                                         │"
echo "│  📄 Proposição: [ementa + detalhes]    │"
echo "│                                         │"
echo "│  ⚠️ ATENÇÃO CRÍTICA                    │"
echo "│   Ação 100% irreversível!              │"
echo "│   Não há possibilidade de recuperação! │"
echo "│                                         │"
echo "│  [Cancelar]  [Excluir Permanentemente] │"
echo "└─────────────────────────────────────────┘"
echo ""

echo "✅ MELHORIAS APLICADAS:"
echo "   🗑️ Removida lista técnica detalhada"
echo "   📋 Removidos termos técnicos (BD, cache, sessão, etc.)"
echo "   💬 Mantida linguagem simples e direta"
echo "   ⚠️ Foco no aviso de irreversibilidade"
echo "   📄 Informações essenciais da proposição mantidas"
echo ""

echo "🎯 BENEFÍCIOS PARA PARLAMENTARES:"
echo "   ✅ Interface mais limpa e menos intimidadora"
echo "   ✅ Foco na ação principal (exclusão)"
echo "   ✅ Linguagem não técnica"
echo "   ✅ Aviso claro sobre irreversibilidade"
echo "   ✅ Modal mais ágil e direto"
echo ""

echo "📱 TAMANHO MANTIDO:"
echo "   📏 Largura: 800px (mantida para boa visualização)"
echo "   📦 Espaçamento: 2rem (mantido para conforto visual)"
echo "   📱 Responsividade: mantida para todos os dispositivos"
echo ""

echo "🔗 PARA TESTAR:"
echo "   1. Acesse: http://localhost:8001/proposicoes/2"
echo "   2. Login: bruno@sistema.gov.br / 123456"
echo "   3. Clique no botão vermelho 'Excluir Proposição'"
echo "   4. Observe o modal mais limpo e direto"
echo ""

echo "================================================"
echo "Modal simplificado para melhor experiência do parlamentar! 🚀"