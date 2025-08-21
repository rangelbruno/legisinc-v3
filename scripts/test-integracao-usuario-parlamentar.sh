#!/bin/bash

echo "🚀 TESTE DE INTEGRAÇÃO USUÁRIO-PARLAMENTAR"
echo "=========================================="

# 1. Reset do banco de dados para teste limpo
echo ""
echo "1. Executando migrate:fresh --seed para ambiente limpo..."
docker exec -it legisinc-app php artisan migrate:fresh --seed

echo ""
echo "2. Verificando dados iniciais..."

# Verificar usuários existentes
echo ""
echo "👤 USUÁRIOS CADASTRADOS:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "
SELECT 
    u.id, 
    u.name, 
    u.email, 
    r.name as role_name,
    CASE WHEN p.id IS NOT NULL THEN 'SIM' ELSE 'NÃO' END as tem_parlamentar
FROM users u 
LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id 
LEFT JOIN roles r ON mhr.role_id = r.id
LEFT JOIN parlamentars p ON u.id = p.user_id
ORDER BY u.id;"

echo ""
echo "🏛️ PARLAMENTARES CADASTRADOS:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "
SELECT 
    p.id, 
    p.nome, 
    p.nome_politico, 
    p.partido,
    p.email,
    CASE WHEN p.user_id IS NOT NULL THEN 'SIM' ELSE 'NÃO' END as tem_usuario,
    u.name as usuario_nome
FROM parlamentars p 
LEFT JOIN users u ON p.user_id = u.id
ORDER BY p.id;"

echo ""
echo "3. Testando acesso às páginas de criação..."

echo ""
echo "📝 TESTANDO /admin/usuarios/create..."
curl -s -o /dev/null -w "Status: %{http_code}" "http://localhost:8001/admin/usuarios/create"

echo ""
echo "📝 TESTANDO /parlamentares/create..."  
curl -s -o /dev/null -w "Status: %{http_code}" "http://localhost:8001/parlamentares/create"

echo ""
echo "4. Análise dos relacionamentos configurados..."

echo ""
echo "🔗 VERIFICANDO RELACIONAMENTOS:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "
SELECT 
    'Users com Parlamentar vinculado' as tipo,
    COUNT(*) as quantidade
FROM users u 
INNER JOIN parlamentars p ON u.id = p.user_id

UNION ALL

SELECT 
    'Parlamentares com User vinculado' as tipo,
    COUNT(*) as quantidade
FROM parlamentars p 
INNER JOIN users u ON p.user_id = u.id

UNION ALL

SELECT 
    'Users sem Parlamentar' as tipo,
    COUNT(*) as quantidade
FROM users u 
LEFT JOIN parlamentars p ON u.id = p.user_id
WHERE p.id IS NULL

UNION ALL

SELECT 
    'Parlamentares sem User' as tipo,
    COUNT(*) as quantidade
FROM parlamentars p 
LEFT JOIN users u ON p.user_id = u.id
WHERE u.id IS NULL;"

echo ""
echo "✅ TESTE CONCLUÍDO!"
echo ""
echo "📋 PRÓXIMOS PASSOS:"
echo "1. Acesse http://localhost:8001/admin/usuarios/create"
echo "2. Selecione perfil 'PARLAMENTAR' ou 'RELATOR'"
echo "3. Escolha vincular a parlamentar existente OU criar novo"
echo "4. Teste também /parlamentares/create"
echo ""
echo "🔑 CREDENCIAIS DE TESTE:"
echo "- Admin: bruno@sistema.gov.br / 123456"
echo "- Parlamentar: jessica@sistema.gov.br / 123456"