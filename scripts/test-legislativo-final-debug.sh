#!/bin/bash

echo "=== Teste Debug: Salvamento Legislativo ==="

# 1. Verificar arquivo atual na proposição
echo "1. Verificando dados da proposição 2:"
docker exec legisinc-app php artisan tinker --execute="
\$prop = App\Models\Proposicao::find(2);
echo 'Arquivo atual: ' . (\$prop->arquivo_path ?? 'null') . PHP_EOL;
"

# 2. Verificar se o arquivo existe no local disk
ARQUIVO_PATH=$(docker exec legisinc-app php artisan tinker --execute="echo App\Models\Proposicao::find(2)->arquivo_path;")
echo "2. Arquivo path: $ARQUIVO_PATH"

if [ -n "$ARQUIVO_PATH" ]; then
    echo "   Verificando se existe no storage/app/:"
    if [ -f "/home/bruno/legisinc/storage/app/$ARQUIVO_PATH" ]; then
        echo "   ✅ Existe no local disk"
        ls -la "/home/bruno/legisinc/storage/app/$ARQUIVO_PATH"
    else
        echo "   ❌ NÃO existe no local disk"
    fi
    
    echo "   Verificando se existe no storage/app/private/:"
    if [ -f "/home/bruno/legisinc/storage/app/private/$ARQUIVO_PATH" ]; then
        echo "   ✅ Existe no private disk"
        ls -la "/home/bruno/legisinc/storage/app/private/$ARQUIVO_PATH"
    else
        echo "   ❌ NÃO existe no private disk"
    fi
fi

# 3. Listar todos os arquivos da proposição 2
echo -e "\n3. Todos os arquivos da proposição 2:"
echo "   Em storage/app/proposicoes/:"
ls -la /home/bruno/legisinc/storage/app/proposicoes/proposicao_2_* 2>/dev/null || echo "   Nenhum arquivo"

echo "   Em storage/app/private/proposicoes/:"
ls -la /home/bruno/legisinc/storage/app/private/proposicoes/proposicao_2_* 2>/dev/null || echo "   Nenhum arquivo"

echo "   Em storage/app/public/proposicoes/:"
ls -la /home/bruno/legisinc/storage/app/public/proposicoes/proposicao_2_* 2>/dev/null || echo "   Nenhum arquivo"

# 4. Testar download
echo -e "\n4. Testando download:"
DOWNLOAD_URL="http://localhost:8001/proposicoes/2/onlyoffice/download?token=$(php -r "echo base64_encode('2|' . time());")"
curl -s "$DOWNLOAD_URL" | head -c 4 | xxd

echo -e "\n=== Status ==="
echo "🔍 Investigação completa dos arquivos de proposição 2"