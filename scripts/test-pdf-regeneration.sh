#!/bin/bash

echo "=== TESTE: Regeneração PDF com Logs ==="
echo

echo "1️⃣ Estado antes da regeneração:"
ls -la /home/bruno/legisinc/storage/app/proposicoes/pdfs/5/proposicao_5.pdf
file /home/bruno/legisinc/storage/app/proposicoes/pdfs/5/proposicao_5.pdf
echo

echo "2️⃣ Removendo PDF atual para forçar regeneração:"
rm -f /home/bruno/legisinc/storage/app/proposicoes/pdfs/5/proposicao_5.pdf
echo

echo "3️⃣ Verificando se PDF foi removido:"
if [ -f /home/bruno/legisinc/storage/app/proposicoes/pdfs/5/proposicao_5.pdf ]; then
    echo "❌ PDF ainda existe"
else
    echo "✅ PDF removido"
fi

echo
echo "4️⃣ Logs da regeneração:"
docker exec legisinc-app tail -20 /var/www/html/storage/logs/laravel.log | grep -E "PDF Assinatura|LibreOffice" || echo "Nenhum log encontrado"
