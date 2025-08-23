#!/bin/bash

echo "🔧 Testando correção completa do botão de assinatura..."
echo "======================================================"
echo ""

# Verificar se estamos no diretório correto
if [ ! -f "docker-compose.yml" ]; then
    echo "❌ Execute este script no diretório raiz do projeto"
    exit 1
fi

echo "1. 🧹 Limpando cache do Laravel..."
docker exec -it legisinc-app php artisan cache:clear
docker exec -it legisinc-app php artisan config:clear
docker exec -it legisinc-app php artisan view:clear

echo ""
echo "2. 🔍 Verificando estrutura atual do botão..."
./scripts/test-botao-assinatura-corrigido.sh

echo ""
echo "3. 🚀 Executando seeders de correção..."

echo "   - ButtonAssinaturaTagFixSeeder..."
docker exec -it legisinc-app php artisan db:seed --class=ButtonAssinaturaTagFixSeeder

echo "   - HTMLStructureValidationSeeder..."
docker exec -it legisinc-app php artisan db:seed --class=HTMLStructureValidationSeeder

echo ""
echo "4. 🔍 Verificando estrutura após correção..."
./scripts/test-botao-assinatura-corrigido.sh

echo ""
echo "5. 🌐 Testando no navegador..."
echo "   - Acesse: http://localhost:8001/proposicoes/2"
echo "   - Verifique se o botão 'Assinar Documento' aparece com:"
echo "     ✅ Ícone de assinatura"
echo "     ✅ Texto 'Assinar Documento'"
echo "     ✅ Descrição 'Assinatura digital com certificado'"
echo "     ✅ Funcionalidade de redirecionamento"

echo ""
echo "6. 🧪 Teste de funcionalidade:"
echo "   - Clique no botão para verificar se redireciona para /assinatura-digital"
echo "   - Verifique se não há erros no console do navegador (F12)"

echo ""
echo "✅ Teste de correção completa concluído!"
echo ""
echo "Se ainda houver problemas, execute:"
echo "docker exec -it legisinc-app php artisan migrate:fresh --seed"
