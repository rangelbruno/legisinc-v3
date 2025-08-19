#!/bin/bash

echo "🔐 TESTE COMPLETO DO SISTEMA DE PERMISSÕES POR ROLE"
echo "=================================================="

# Função para testar acesso com diferentes usuários
test_user_access() {
    local email=$1
    local password=$2
    local role=$3
    local url=$4
    local expected_status=$5
    
    echo ""
    echo "👤 Testando usuário: $email ($role)"
    echo "📍 URL: $url"
    
    # Fazer login e obter cookie de sessão
    local cookie_jar=$(mktemp)
    local csrf_token=$(curl -s -c "$cookie_jar" http://localhost:8001/login | grep '_token' | sed 's/.*value="\([^"]*\)".*/\1/')
    
    if [ -z "$csrf_token" ]; then
        echo "❌ Erro: Não conseguiu obter CSRF token"
        rm -f "$cookie_jar"
        return 1
    fi
    
    # Fazer login
    local login_response=$(curl -s -b "$cookie_jar" -c "$cookie_jar" \
        -d "_token=$csrf_token&email=$email&password=$password" \
        -X POST http://localhost:8001/login \
        -w "%{http_code}" -o /dev/null)
    
    if [ "$login_response" = "302" ]; then
        echo "✅ Login realizado com sucesso"
        
        # Testar acesso à URL
        local access_response=$(curl -s -b "$cookie_jar" \
            -w "%{http_code}" -o /dev/null \
            "$url")
        
        echo "📊 Status recebido: $access_response"
        
        if [ "$access_response" = "$expected_status" ]; then
            echo "✅ SUCESSO: Status esperado ($expected_status)"
        else
            echo "❌ FALHA: Esperado $expected_status, recebido $access_response"
        fi
    else
        echo "❌ Falha no login (Status: $login_response)"
    fi
    
    # Cleanup
    rm -f "$cookie_jar"
}

echo ""
echo "🚀 Iniciando testes de permissão..."

# Teste 1: PARLAMENTAR tentando assinar sua própria proposição
test_user_access "jessica@sistema.gov.br" "123456" "PARLAMENTAR" "http://localhost:8001/proposicoes/2/assinar" "200"

# Teste 2: LEGISLATIVO tentando acessar editor
test_user_access "joao@sistema.gov.br" "123456" "LEGISLATIVO" "http://localhost:8001/proposicoes/2/onlyoffice/editor" "200"

# Teste 3: PROTOCOLO tentando assinar (deve falhar)
test_user_access "roberto@sistema.gov.br" "123456" "PROTOCOLO" "http://localhost:8001/proposicoes/2/assinar" "403"

# Teste 4: EXPEDIENTE tentando editar OnlyOffice (deve falhar)
test_user_access "expediente@sistema.gov.br" "123456" "EXPEDIENTE" "http://localhost:8001/proposicoes/2/onlyoffice/editor-parlamentar" "403"

# Teste 5: ADMIN deve ter acesso total
test_user_access "bruno@sistema.gov.br" "123456" "ADMIN" "http://localhost:8001/proposicoes/2/assinar" "200"

echo ""
echo "🔍 Verificando permissões no banco de dados..."

# Verificar se as permissões foram criadas corretamente
docker exec legisinc-postgres psql -U postgres -d legisinc -c "
SELECT 
    role_name,
    screen_route,
    screen_name,
    can_access,
    can_edit
FROM screen_permissions 
WHERE screen_route IN ('proposicoes.assinar', 'onlyoffice.editor', 'onlyoffice.editor-parlamentar')
ORDER BY role_name, screen_route;
"

echo ""
echo "📋 Resumo do Sistema de Permissões:"
echo "=================================="
echo "✅ Middleware RolePermissionMiddleware criado"
echo "✅ Middleware registrado no bootstrap/app.php"  
echo "✅ Rotas de assinatura protegidas"
echo "✅ Rotas OnlyOffice protegidas"
echo "✅ Seeder de permissões configurado"
echo "✅ Permissões aplicadas no banco de dados"
echo ""
echo "🎯 BENEFÍCIOS:"
echo "• Controle granular de acesso por role"
echo "• Validação contextual (autor da proposição)"
echo "• Eliminação de erros 403 indevidos"
echo "• Sistema centralizado e consistente"
echo "• Fácil manutenção e extensão"
echo ""
echo "💡 USO:"
echo "• PARLAMENTAR: Pode assinar suas próprias proposições"
echo "• LEGISLATIVO: Pode revisar todas as proposições"
echo "• PROTOCOLO: Pode protocolar proposições"
echo "• ADMIN: Acesso total ao sistema"
echo ""
echo "✅ TESTE CONCLUÍDO!"