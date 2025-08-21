#!/bin/bash

echo "🔍 ANÁLISE DA ESTRUTURA DO DOCUMENTO WORD"
echo "========================================"

arquivo_mais_recente=$(ls -t /home/bruno/legisinc/storage/app/private/proposicoes/proposicao_2_*.docx | head -1)

if [ -z "$arquivo_mais_recente" ]; then
    echo "❌ Nenhum arquivo encontrado"
    exit 1
fi

echo ""
echo "📂 Arquivo: $(basename "$arquivo_mais_recente")"
echo "📏 Tamanho: $(stat -c %s "$arquivo_mais_recente") bytes"

echo ""
echo "🔧 Extraindo estrutura completa do documento..."

temp_dir=$(mktemp -d)
if ! unzip -q "$arquivo_mais_recente" -d "$temp_dir"; then
    echo "❌ Erro ao extrair DOCX"
    rm -rf "$temp_dir"
    exit 1
fi

echo "✅ DOCX extraído para análise"

echo ""
echo "📋 ARQUIVOS NO DOCX:"
find "$temp_dir" -type f | sort

echo ""
echo "📄 1. ANALISANDO DOCUMENT.XML (CORPO PRINCIPAL):"
if [ -f "$temp_dir/word/document.xml" ]; then
    echo "   📊 Tamanho: $(stat -c %s "$temp_dir/word/document.xml") bytes"
    
    # Contar elementos estruturais
    paragrafos=$(grep -o '<w:p[^>]*>' "$temp_dir/word/document.xml" | wc -l)
    runs=$(grep -o '<w:r[^>]*>' "$temp_dir/word/document.xml" | wc -l)
    textos=$(grep -o '<w:t[^>]*>' "$temp_dir/word/document.xml" | wc -l)
    
    echo "   📊 Parágrafos <w:p>: $paragrafos"
    echo "   📊 Runs <w:r>: $runs"
    echo "   📊 Textos <w:t>: $textos"
    
    echo ""
    echo "   📝 Conteúdo extraído do corpo principal:"
    grep -o '<w:t[^>]*>[^<]*</w:t>' "$temp_dir/word/document.xml" | sed 's/<[^>]*>//g' | nl
fi

echo ""
echo "🎩 2. ANALISANDO CABEÇALHO (HEADER):"
if ls "$temp_dir/word/header"*.xml 2>/dev/null; then
    for header in "$temp_dir/word/header"*.xml; do
        echo "   📄 Arquivo: $(basename "$header")"
        echo "   📊 Tamanho: $(stat -c %s "$header") bytes"
        
        if [ -s "$header" ]; then
            echo "   📝 Conteúdo do cabeçalho:"
            grep -o '<w:t[^>]*>[^<]*</w:t>' "$header" | sed 's/<[^>]*>//g' | nl | sed 's/^/      /'
            
            # Verificar se tem imagens
            if grep -q '<w:drawing>' "$header"; then
                echo "   🖼️ Contém imagens/desenhos"
            fi
        else
            echo "   ⚠️ Arquivo vazio"
        fi
    done
else
    echo "   ❌ Nenhum arquivo de cabeçalho encontrado"
fi

echo ""
echo "👠 3. ANALISANDO RODAPÉ (FOOTER):"
if ls "$temp_dir/word/footer"*.xml 2>/dev/null; then
    for footer in "$temp_dir/word/footer"*.xml; do
        echo "   📄 Arquivo: $(basename "$footer")"
        echo "   📊 Tamanho: $(stat -c %s "$footer") bytes"
        
        if [ -s "$footer" ]; then
            echo "   📝 Conteúdo do rodapé:"
            grep -o '<w:t[^>]*>[^<]*</w:t>' "$footer" | sed 's/<[^>]*>//g' | nl | sed 's/^/      /'
        else
            echo "   ⚠️ Arquivo vazio"
        fi
    done
else
    echo "   ❌ Nenhum arquivo de rodapé encontrado"
fi

echo ""
echo "🔗 4. ANALISANDO RELACIONAMENTOS (_rels):"
if [ -f "$temp_dir/word/_rels/document.xml.rels" ]; then
    echo "   📄 Relacionamentos do documento encontrados"
    
    # Verificar referências a headers e footers
    if grep -q "header" "$temp_dir/word/_rels/document.xml.rels"; then
        echo "   🎩 Referências a cabeçalhos encontradas:"
        grep "header" "$temp_dir/word/_rels/document.xml.rels" | sed 's/^/      /'
    fi
    
    if grep -q "footer" "$temp_dir/word/_rels/document.xml.rels"; then
        echo "   👠 Referências a rodapés encontradas:"
        grep "footer" "$temp_dir/word/_rels/document.xml.rels" | sed 's/^/      /'
    fi
else
    echo "   ❌ Arquivo de relacionamentos não encontrado"
fi

echo ""
echo "📐 5. ANALISANDO CONFIGURAÇÕES DE SEÇÃO:"
if [ -f "$temp_dir/word/document.xml" ]; then
    # Procurar por configurações de seção que referenciam headers/footers
    if grep -q "sectPr" "$temp_dir/word/document.xml"; then
        echo "   📄 Configurações de seção encontradas"
        
        if grep -q "headerReference" "$temp_dir/word/document.xml"; then
            echo "   🎩 Referências a cabeçalho na seção"
        fi
        
        if grep -q "footerReference" "$temp_dir/word/document.xml"; then
            echo "   👠 Referências a rodapé na seção"
        fi
    fi
fi

echo ""
echo "🧪 6. VERIFICAÇÃO DE ESTRUTURA ATUAL DO PDF:"
echo "   ❌ PROBLEMA IDENTIFICADO:"
echo "      • O sistema atual só lê document.xml (corpo principal)"
echo "      • NÃO lê header*.xml (cabeçalho)"
echo "      • NÃO lê footer*.xml (rodapé)"
echo "      • NÃO respeita configurações de seção do Word"

echo ""
echo "💡 7. SOLUÇÃO NECESSÁRIA:"
echo "   ✅ Extrair header*.xml separadamente"
echo "   ✅ Extrair footer*.xml separadamente"
echo "   ✅ Combinar na ordem: CABEÇALHO + CORPO + RODAPÉ"
echo "   ✅ Aplicar CSS específico para cada seção"
echo "   ✅ Respeitar formatação original de cada parte"

# Limpeza
rm -rf "$temp_dir"

echo ""
echo "========================================="
echo "✅ Análise da estrutura Word concluída!"