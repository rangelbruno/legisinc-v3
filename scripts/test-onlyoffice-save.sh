#!/bin/bash

echo "=== Teste de Salvamento do OnlyOffice ==="
echo ""

# Verificar última proposição
echo "1. Verificando última proposição no banco:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, tipo, ementa, arquivo_path, LENGTH(conteudo) as conteudo_length, ultima_modificacao FROM proposicoes WHERE id = 1;"

echo ""
echo "2. Verificando arquivos salvos:"
ls -la /home/bruno/legisinc/storage/app/private/proposicoes/proposicao_1_* 2>/dev/null | tail -5

echo ""
echo "3. Últimos callbacks do OnlyOffice:"
grep -i "callback.*status.*2" /home/bruno/legisinc/storage/logs/laravel.log | tail -3

echo ""
echo "4. Verificando conteúdo extraído:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT LEFT(conteudo, 200) as conteudo_preview FROM proposicoes WHERE id = 1;"

echo ""
echo "=== Instruções para testar ==="
echo ""
echo "1. Acesse: http://localhost:8001"
echo "2. Login como: jessica@sistema.gov.br / 123456"
echo "3. Vá em Minhas Proposições"
echo "4. Clique em 'Continuar Edição no OnlyOffice'"
echo "5. Faça uma alteração no documento (adicione texto 'TESTE SALVAMENTO')"
echo "6. Clique no botão Salvar (ícone de disquete)"
echo "7. Aguarde a mensagem de confirmação"
echo "8. Feche o editor e abra novamente"
echo "9. Verifique se o texto 'TESTE SALVAMENTO' foi preservado"
echo ""
echo "Após o teste, execute novamente este script para verificar se o conteúdo foi atualizado."
