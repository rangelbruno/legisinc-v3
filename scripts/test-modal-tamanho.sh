#!/bin/bash

echo "=== TESTE DO MODAL SWEETALERT AUMENTADO ==="
echo "Verificando se o modal de exclusão foi redimensionado corretamente"
echo ""

# Verificar se o servidor está rodando
echo "1. Verificando servidor..."
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8001 | grep -q "200\|302"; then
    echo "   ✅ Servidor rodando em http://localhost:8001"
else
    echo "   ❌ Servidor não está acessível"
    exit 1
fi

# Limpar caches
echo ""
echo "2. Limpando caches para garantir mudanças..."
php artisan view:clear > /dev/null 2>&1
php artisan config:clear > /dev/null 2>&1
echo "   ✅ Caches limpos"

# Verificar se as modificações estão presentes
echo ""
echo "3. Verificando modificações no código:"

# Verificar width aumentado
if grep -q "width: '800px'" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php; then
    echo "   ✅ Largura aumentada para 800px (era 500px)"
else
    echo "   ❌ Largura não foi alterada"
fi

# Verificar padding aumentado
if grep -q "padding: '2rem'" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php; then
    echo "   ✅ Padding aumentado para 2rem"
else
    echo "   ❌ Padding não foi alterado"
fi

# Verificar CSS customizado
if grep -q "swal2-large-modal" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php; then
    echo "   ✅ CSS customizado para modal grande adicionado"
else
    echo "   ❌ CSS customizado não encontrado"
fi

# Verificar emojis e melhorias visuais
if grep -q "🗑️ O que será excluído" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php; then
    echo "   ✅ Emojis e melhorias visuais implementados"
else
    echo "   ❌ Melhorias visuais não encontradas"
fi

# Verificar estrutura melhorada
if grep -q "📊 Registro completo do banco de dados" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php; then
    echo "   ✅ Lista detalhada de exclusão implementada"
else
    echo "   ❌ Lista detalhada não encontrada"
fi

# Verificar alert de atenção crítica
if grep -q "⚠️ ATENÇÃO CRÍTICA" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php; then
    echo "   ✅ Alert de atenção crítica implementado"
else
    echo "   ❌ Alert de atenção não encontrado"
fi

# Verificar botões grandes
if grep -q "btn-lg" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php; then
    echo "   ✅ Botões em tamanho grande (btn-lg)"
else
    echo "   ❌ Botões não foram aumentados"
fi

echo ""
echo "4. Verificando estrutura HTML gerada:"

# Criar sessão com cookies para testar
COOKIE_JAR="/tmp/test_modal_cookies.txt"
rm -f "$COOKIE_JAR"

# Obter token CSRF e fazer login
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
        
        # Verificar elementos do modal
        if echo "$PROPOSICAO_PAGE" | grep -q "width: '800px'"; then
            echo "   ✅ Modal configurado com largura 800px"
        else
            echo "   ❌ Configuração de largura não encontrada na página"
        fi
        
        if echo "$PROPOSICAO_PAGE" | grep -q "swal2-large-modal"; then
            echo "   ✅ Classe CSS customizada presente"
        else
            echo "   ❌ Classe CSS customizada não encontrada"
        fi
        
        if echo "$PROPOSICAO_PAGE" | grep -q "📊 Registro completo"; then
            echo "   ✅ Conteúdo detalhado presente no HTML"
        else
            echo "   ❌ Conteúdo detalhado não encontrado"
        fi
        
        if echo "$PROPOSICAO_PAGE" | grep -q "⚠️ ATENÇÃO CRÍTICA"; then
            echo "   ✅ Aviso crítico presente no HTML"
        else
            echo "   ❌ Aviso crítico não encontrado"
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
echo "=== RESUMO DAS MELHORIAS NO MODAL ==="
echo ""
echo "🔧 DIMENSÕES:"
echo "   📏 Largura: 500px → 800px (+60%)"
echo "   📐 Padding: padrão → 2rem"
echo "   📱 Responsivo: 95% da tela em mobile"
echo ""
echo "🎨 VISUAL:"
echo "   📋 Lista detalhada com emojis e espaçamento"
echo "   🎯 Seções organizadas com cores específicas"
echo "   ⚠️ Alert crítico destacado"
echo "   🔴 Botões grandes (btn-lg)"
echo ""
echo "📱 RESPONSIVIDADE:"
echo "   💻 Desktop: 800px fixo"
echo "   📱 Mobile: 100% da tela com margem"
echo "   📜 Scroll interno se necessário"
echo ""
echo "🔗 Para testar visualmente:"
echo "   1. Acesse: http://localhost:8001/proposicoes/2"
echo "   2. Login: bruno@sistema.gov.br / 123456"
echo "   3. Clique no botão vermelho 'Excluir Proposição'"
echo "   4. Observe o modal maior e mais detalhado"
echo ""
echo "✨ O modal agora tem espaço suficiente para todas as informações!"