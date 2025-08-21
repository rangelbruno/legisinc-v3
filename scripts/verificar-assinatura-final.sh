#!/bin/bash

echo "🔍 VERIFICAÇÃO FINAL: Assinatura Digital no PDF"
echo "==============================================="

echo
echo "📋 1. Verificando status da proposição 2..."
docker exec legisinc-app php -r "
\$pdo = new PDO('pgsql:host=db;dbname=legisinc', 'postgres', '123456');
\$stmt = \$pdo->prepare('SELECT id, status, assinatura_digital IS NOT NULL as tem_assinatura, data_assinatura FROM proposicoes WHERE id = 2');
\$stmt->execute();
\$row = \$stmt->fetch(PDO::FETCH_ASSOC);
if (\$row) {
    echo \"✅ ID: {\$row['id']}, Status: {\$row['status']}, Assinatura: \" . (\$row['tem_assinatura'] ? 'SIM' : 'NÃO') . \", Data: {\$row['data_assinatura']}\";
} else {
    echo \"❌ Proposição não encontrada\";
}
"

echo
echo
echo "🌐 2. Fazendo requisição para a página de assinatura..."
RESPONSE=$(curl -s "http://localhost:8001/proposicoes/2/assinar" 2>/dev/null)

echo
echo "🔍 3. Procurando por texto de assinatura digital..."
if echo "$RESPONSE" | grep -q "Autenticar documento em"; then
    echo "   ✅ SUCESSO! Texto de assinatura encontrado:"
    echo "$RESPONSE" | grep -o "Autenticar documento em[^<]*" | head -1
    echo
else
    echo "   ❌ Texto de assinatura NÃO encontrado"
    echo
    echo "🔍 Verificando variáveis não substituídas..."
    if echo "$RESPONSE" | grep -q "\${assinatura_digital_info}"; then
        echo "   ⚠️  Variável \${assinatura_digital_info} ainda não foi substituída"
    fi
    
    if echo "$RESPONSE" | grep -q "assinatura_digital_info"; then
        echo "   ⚠️  Variável assinatura_digital_info encontrada:"
        echo "$RESPONSE" | grep -o "assinatura_digital_info[^<]*" | head -3
    fi
fi

echo
echo "📄 4. Verificando se PDF contém a Lei 14.063/2020..."
if echo "$RESPONSE" | grep -q "14.063/2020"; then
    echo "   ✅ Referência à Lei 14.063/2020 encontrada!"
else
    echo "   ❌ Referência à Lei 14.063/2020 NÃO encontrada"
fi

echo
echo "📋 RESULTADO:"
if echo "$RESPONSE" | grep -q "Autenticar documento em.*14.063/2020"; then
    echo "✅ SUCESSO! Assinatura digital está aparecendo corretamente no PDF"
    echo "🎯 Texto completo encontrado:"
    echo "$RESPONSE" | grep -o "Autenticar documento em[^<]*14.063/2020" | head -1
else
    echo "❌ PROBLEMA: Assinatura digital ainda não está aparecendo no PDF"
    echo "🔧 Verifique se os métodos foram implementados corretamente"
fi

echo
echo "🌐 Para testar manualmente, acesse: http://localhost:8001/proposicoes/2/assinar"