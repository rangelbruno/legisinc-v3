#!/bin/bash

echo "🌐 TESTE REAL VIA NAVEGADOR AUTOMATIZADO"
echo "========================================"

# Criar arquivo HTML para testar JavaScript
cat > /tmp/test-create-parlamentar.html << 'EOF'
<!DOCTYPE html>
<html>
<head>
    <title>Teste Parlamentares Create</title>
    <script>
        async function testPage() {
            try {
                // 1. Fazer login
                console.log('1. Fazendo login...');
                let loginResponse = await fetch('http://localhost:8001/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'email=bruno@sistema.gov.br&password=123456',
                    credentials: 'include'
                });
                
                console.log('Login status:', loginResponse.status);
                
                // 2. Acessar página de criação
                console.log('2. Acessando /parlamentares/create...');
                let pageResponse = await fetch('http://localhost:8001/parlamentares/create', {
                    credentials: 'include'
                });
                
                console.log('Page status:', pageResponse.status);
                let pageContent = await pageResponse.text();
                
                // 3. Verificar se usuários estão presentes
                console.log('3. Verificando usuários no HTML...');
                if (pageContent.includes('Carlos Deputado Silva')) {
                    console.log('✅ Carlos encontrado!');
                } else {
                    console.log('❌ Carlos NÃO encontrado');
                }
                
                if (pageContent.includes('Ana Vereadora Costa')) {
                    console.log('✅ Ana encontrada!');
                } else {
                    console.log('❌ Ana NÃO encontrada');
                }
                
                if (pageContent.includes('select name="user_id"')) {
                    console.log('✅ Select user_id encontrado!');
                } else {
                    console.log('❌ Select user_id NÃO encontrado');
                }
                
                // 4. Extrair parte relevante do HTML
                let selectMatch = pageContent.match(/select name="user_id"[\s\S]*?<\/select>/);
                if (selectMatch) {
                    console.log('Select HTML encontrado:', selectMatch[0].substring(0, 500) + '...');
                }
                
            } catch (error) {
                console.error('Erro:', error);
            }
        }
        
        // Executar teste quando página carregar
        window.onload = testPage;
    </script>
</head>
<body>
    <h1>Testando Parlamentares Create</h1>
    <p>Veja o console do navegador para resultados...</p>
</body>
</html>
EOF

echo "📁 Arquivo de teste criado em: /tmp/test-create-parlamentar.html"
echo ""
echo "🚀 PARA TESTAR MANUALMENTE:"
echo "1. Abra: http://localhost:8001/login"
echo "2. Faça login com: bruno@sistema.gov.br / 123456"
echo "3. Acesse: http://localhost:8001/parlamentares/create"
echo "4. Procure pela seção 'Integração com Usuário'"
echo "5. Marque 'Vincular a usuário já cadastrado'"
echo "6. Verifique se o select mostra os usuários criados"

echo ""
echo "✅ TESTE MANUAL RECOMENDADO!"