#!/bin/bash

echo "=== Aplicando Correção Final para Salvamento OnlyOffice ==="
echo ""

# Limpar cache Laravel
echo "1. Limpando cache..."
docker exec legisinc-app php artisan cache:clear
docker exec legisinc-app php artisan config:clear

echo ""
echo "2. Verificando estrutura de diretórios..."
mkdir -p /home/bruno/legisinc/storage/app/proposicoes
mkdir -p /home/bruno/legisinc/storage/app/private/proposicoes
chmod -R 777 /home/bruno/legisinc/storage/app/proposicoes
chmod -R 777 /home/bruno/legisinc/storage/app/private/proposicoes

echo ""
echo "3. Status atual da proposição:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, tipo, arquivo_path, LENGTH(conteudo) as conteudo_length FROM proposicoes WHERE id = 1;"

echo ""
echo "=== Instruções para Testar ==="
echo ""
echo "1. Acesse: http://localhost:8001"
echo "2. Login: jessica@sistema.gov.br / 123456"
echo "3. Vá em 'Minhas Proposições'"
echo "4. Clique em 'Continuar Edição no OnlyOffice'"
echo "5. Adicione o texto: TESTE SALVAMENTO $(date +%H:%M:%S)"
echo "6. Salve (Ctrl+S ou botão salvar)"
echo "7. Aguarde 5 segundos"
echo "8. Feche e reabra o documento"
echo ""
echo "Executando monitoramento de logs..."
echo "(Pressione Ctrl+C para parar)"
echo ""

tail -f /home/bruno/legisinc/storage/logs/laravel.log | grep -E "callback.*status.*2|arquivo.*salvo|conteudo.*atualizado|Erro ao processar"