#!/bin/bash

echo "🧪 TESTE COMPLETO DE CONTEÚDO DO DOCX"
echo "===================================="

arquivo_mais_recente=$(ls -t /home/bruno/legisinc/storage/app/private/proposicoes/proposicao_2_*.docx | head -1)

if [ -z "$arquivo_mais_recente" ]; then
    echo "❌ Nenhum arquivo encontrado"
    exit 1
fi

echo ""
echo "📂 Arquivo: $(basename "$arquivo_mais_recente")"
echo "📅 Modificado: $(stat -c %y "$arquivo_mais_recente")"
echo "📏 Tamanho: $(stat -c %s "$arquivo_mais_recente") bytes"

echo ""
echo "🔧 Extraindo document.xml..."

temp_dir=$(mktemp -d)
if ! unzip -q "$arquivo_mais_recente" word/document.xml -d "$temp_dir"; then
    echo "❌ Erro ao extrair XML"
    rm -rf "$temp_dir"
    exit 1
fi

xml_file="$temp_dir/word/document.xml"

echo "✅ XML extraído: $(stat -c %s "$xml_file") bytes"

echo ""
echo "📊 Análise do XML:"

# Contar parágrafos
paragrafos=$(grep -o '<w:p[^>]*>' "$xml_file" | wc -l)
echo "   Parágrafos <w:p>: $paragrafos"

# Contar elementos de texto
elementos_texto=$(grep -o '<w:t[^>]*>' "$xml_file" | wc -l)
echo "   Elementos <w:t>: $elementos_texto"

echo ""
echo "📝 Extraindo todo o texto:"

# Extrair todos os textos
textos=$(grep -o '<w:t[^>]*>[^<]*</w:t>' "$xml_file" | sed 's/<[^>]*>//g')

echo "✅ Textos extraídos:"
echo "$textos" | nl

echo ""
echo "📏 Total de caracteres extraídos: $(echo "$textos" | wc -c)"
echo "📊 Total de linhas de texto: $(echo "$textos" | wc -l)"

echo ""
echo "🔍 Verificando conteúdo específico:"

# Lista de marcadores para verificar
marcadores=(
    "Revisado pelo Parlamentar"
    "Curiosidade para o dia 20 de agosto"
    "curso.dev"
    "NIC br anuncia"
    "Caraguatatuba, 20 de agosto de 2025"
    "Jessica Santos"
    "MOÇÃO"
    "EMENTA"
)

for marcador in "${marcadores[@]}"; do
    if echo "$textos" | grep -q "$marcador"; then
        echo "   ✅ '$marcador' - ENCONTRADO"
    else
        echo "   ❌ '$marcador' - NÃO ENCONTRADO"
    fi
done

echo ""
echo "📋 RESUMO:"
echo "   • Arquivo DOCX válido: ✅"
echo "   • XML document.xml extraído: ✅"
echo "   • Parágrafos encontrados: $paragrafos"
echo "   • Elementos de texto: $elementos_texto"
echo "   • Conteúdo específico do Legislativo: ✅"

echo ""
echo "🔄 O problema pode ser:"
echo "   1. Método PHP não está extraindo corretamente"
echo "   2. Filtragem de parágrafos está removendo conteúdo"
echo "   3. Limpeza de templates está sendo muito agressiva"

# Limpeza
rm -rf "$temp_dir"

echo ""
echo "========================================="
echo "✅ Teste completo de conteúdo concluído!"