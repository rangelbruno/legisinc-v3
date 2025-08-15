#!/bin/bash

echo "🔍 === TESTE CARREGAMENTO ARQUIVO LEGISLATIVO ==="
echo ""

echo "📋 1. Verificando proposição 1..."
docker exec legisinc-app php artisan tinker --execute="
\$p = App\Models\Proposicao::find(1);
echo 'ID: ' . \$p->id . PHP_EOL;
echo 'Status: ' . \$p->status . PHP_EOL;
echo 'Arquivo Path: ' . \$p->arquivo_path . PHP_EOL;
echo 'Template ID: ' . (\$p->template_id ?? 'null') . PHP_EOL;
echo 'Conteúdo length: ' . strlen(\$p->conteudo ?? '') . PHP_EOL;
echo 'Tem conteúdo IA? ' . (!empty(\$p->conteudo) && \$p->conteudo !== 'Conteúdo a ser definido' ? 'SIM' : 'NÃO') . PHP_EOL;
"

echo ""
echo "📁 2. Verificando arquivo físico..."
docker exec legisinc-app ls -la storage/app/public/proposicoes/proposicao_1_*.rtf | head -2

echo ""
echo "🔄 3. Limpando logs..."
docker exec legisinc-app truncate -s 0 storage/logs/laravel.log

echo ""
echo "📥 4. Simulando download pelo Legislativo..."
curl -s "http://localhost:8001/proposicoes/1/onlyoffice/download?token=test_legislativo" -o /tmp/teste_legislativo.rtf

echo ""
echo "📊 5. Verificando logs gerados..."
docker exec legisinc-app grep -E "(Usando arquivo salvo|Tentando usar template|Template encontrado|Processando template)" storage/logs/laravel.log

echo ""
echo "📄 6. Analisando arquivo baixado..."
if [ -f /tmp/teste_legislativo.rtf ]; then
    echo "Tamanho do arquivo: $(wc -c < /tmp/teste_legislativo.rtf) bytes"
    echo "Primeiras linhas do arquivo:"
    head -c 500 /tmp/teste_legislativo.rtf | strings | head -10
    echo ""
    echo "Verificando se tem variáveis de template:"
    grep -o '\${[^}]*}' /tmp/teste_legislativo.rtf | head -5 || echo "Nenhuma variável encontrada"
else
    echo "Arquivo não encontrado"
fi

echo ""
echo "✅ Teste concluído!"