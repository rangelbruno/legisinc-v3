#!/bin/bash

echo "🔍 TESTE: Verificando Extração de Conteúdo DOCX"
echo "==============================================="

# Encontrar o arquivo DOCX mais recente da proposição 1
ARQUIVO_MAIS_RECENTE=$(find /home/bruno/legisinc/storage/app -name "proposicao_1_*.docx" -type f -printf '%T@ %p\n' | sort -n | tail -1 | cut -d' ' -f2-)

if [ -z "$ARQUIVO_MAIS_RECENTE" ]; then
    echo "❌ Nenhum arquivo DOCX encontrado para proposição 1"
    exit 1
fi

echo "📁 Arquivo mais recente: $(basename $ARQUIVO_MAIS_RECENTE)"
echo "📅 Modificado: $(stat --format='%y' $ARQUIVO_MAIS_RECENTE)"
echo "📏 Tamanho: $(stat --format='%s' $ARQUIVO_MAIS_RECENTE) bytes"
echo ""

# Testar extração via PHP
echo "🧪 Testando extração de conteúdo via DocumentExtractionService:"
docker exec legisinc-app php artisan tinker --execute="
\$service = new App\Services\DocumentExtractionService();
\$arquivo = '$ARQUIVO_MAIS_RECENTE';
echo 'Arquivo: ' . \$arquivo . PHP_EOL;
echo 'Existe: ' . (file_exists(\$arquivo) ? 'SIM' : 'NÃO') . PHP_EOL;

if (file_exists(\$arquivo)) {
    \$conteudo = \$service->extractTextFromDocxFile(\$arquivo);
    echo 'Conteúdo extraído (' . strlen(\$conteudo) . ' caracteres):' . PHP_EOL;
    echo '=================================' . PHP_EOL;
    echo \$conteudo . PHP_EOL;
    echo '=================================' . PHP_EOL;
    
    if (empty(\$conteudo)) {
        echo '⚠️  PROBLEMA: Nenhum conteúdo extraído!' . PHP_EOL;
    } else {
        if (stripos(\$conteudo, 'LEGISLATIVO') !== false) {
            echo '✅ SUCESSO: Contém texto do LEGISLATIVO!' . PHP_EOL;
        } elseif (stripos(\$conteudo, 'PARLAMENTAR') !== false) {
            echo '⚠️  ATENÇÃO: Contém apenas texto do PARLAMENTAR' . PHP_EOL;
        } else {
            echo '❓ INFO: Conteúdo não contém marcadores específicos' . PHP_EOL;
        }
    }
} else {
    echo '❌ ERRO: Arquivo não encontrado' . PHP_EOL;
}
"

echo ""
echo "🔍 Verificando estrutura interna do DOCX:"

# Usar unzip para verificar conteúdo do DOCX
if command -v unzip >/dev/null 2>&1; then
    echo "📂 Arquivos dentro do DOCX:"
    unzip -l "$ARQUIVO_MAIS_RECENTE" | head -20
    
    echo ""
    echo "📄 Conteúdo do document.xml (primeiras 500 chars):"
    unzip -p "$ARQUIVO_MAIS_RECENTE" word/document.xml 2>/dev/null | head -c 500
    echo ""
    echo ""
    
    echo "🔍 Buscando tags <w:t> no document.xml:"
    unzip -p "$ARQUIVO_MAIS_RECENTE" word/document.xml 2>/dev/null | grep -o '<w:t[^>]*>[^<]*</w:t>' | head -10
else
    echo "⚠️  unzip não disponível para verificação manual"
fi

echo ""
echo "✅ Teste de extração concluído!"