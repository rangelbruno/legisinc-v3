#!/bin/bash

echo "🔧 Testando correção da tag de fechamento do botão Assinar Documento..."
echo ""

# Verificar se o arquivo existe
if [ ! -f "resources/views/proposicoes/show.blade.php" ]; then
    echo "❌ Arquivo show.blade.php não encontrado"
    exit 1
fi

echo "📁 Verificando estrutura do botão de assinatura..."

# Verificar se a tag de fechamento </a> está presente
if grep -A 10 "Assinar Documento" "resources/views/proposicoes/show.blade.php" | grep -q "</a>"; then
    echo "✅ Tag de fechamento </a> encontrada após 'Assinar Documento'"
else
    echo "❌ Tag de fechamento </a> NÃO encontrada após 'Assinar Documento'"
fi

# Verificar se o botão está completo
if grep -A 15 "assinatura-digital" "resources/views/proposicoes/show.blade.php" | grep -q "Assinar Documento" && \
   grep -A 15 "assinatura-digital" "resources/views/proposicoes/show.blade.php" | grep -q "Assinatura digital com certificado" && \
   grep -A 15 "assinatura-digital" "resources/views/proposicoes/show.blade.php" | grep -q "</a>"; then
    echo "✅ Botão de assinatura está completo e funcional"
else
    echo "❌ Botão de assinatura está incompleto"
fi

# Verificar se não há tags <a> órfãs
OPEN_TAGS=$(grep -o "<a" "resources/views/proposicoes/show.blade.php" | wc -l)
CLOSE_TAGS=$(grep -o "</a>" "resources/views/proposicoes/show.blade.php" | wc -l)

echo "📊 Tags <a>: $OPEN_TAGS, Tags </a>: $CLOSE_TAGS"

if [ "$OPEN_TAGS" -eq "$CLOSE_TAGS" ]; then
    echo "✅ Número de tags <a> e </a> está balanceado"
else
    echo "❌ Desbalanceamento de tags: $OPEN_TAGS <a> vs $CLOSE_TAGS </a>"
fi

# Verificar se a condição v-if="canSign()" está presente
if grep -q 'v-if="canSign()"' "resources/views/proposicoes/show.blade.php"; then
    echo "✅ Condição v-if=\"canSign()\" está presente"
else
    echo "❌ Condição v-if=\"canSign()\" não encontrada"
fi

echo ""
echo "🔍 Verificação completa!"
echo ""
echo "Para testar manualmente:"
echo "1. Acesse uma proposição em /proposicoes/2"
echo "2. Verifique se o botão 'Assinar Documento' aparece com texto e ícone"
echo "3. Clique no botão para verificar se redireciona corretamente"
echo ""
echo "Se ainda houver problemas, execute:"
echo "docker exec -it legisinc-app php artisan db:seed --class=ButtonAssinaturaTagFixSeeder"